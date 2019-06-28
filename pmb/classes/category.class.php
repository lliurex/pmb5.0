<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.class.php,v 1.52.2.2 2018-01-17 09:32:03 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// définition de la classe de gestion des 'auteurs'
if ( ! defined( 'CATEGORY_CLASS' ) ) {
  define( 'CATEGORY_CLASS', 1 );
require_once($class_path."/thesaurus.class.php");
require_once($base_path."/javascript/misc.inc.php");

require_once($class_path."/categories.class.php");
require_once($class_path."/noeuds.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/synchro_rdf.class.php");
require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/index_concept.class.php");
require_once($class_path."/aut_pperso.class.php");

//Renvoi récursivement la liste des notices référançant un noeuds et ses enfants
function get_category_notice_count($node_id, &$listcontent) {
	//On ajoute les notices du noeuds
	$asql = "SELECT notcateg_notice FROM notices_categories WHERE num_noeud = ".$node_id;
	$ares = pmb_mysql_query($asql);
	while ($arow=pmb_mysql_fetch_row($ares)) {
		$listcontent[] = $arow[0];
	}

	//Et on recurse		
	$asql = "SELECT id_noeud FROM noeuds WHERE num_parent = ".$node_id;
	$ares = pmb_mysql_query($asql);
	while ($arow=pmb_mysql_fetch_row($ares)) {
		get_category_notice_count($arow[0], $listcontent);
	}
}

class category {
	
	// ---------------------------------------------------------------
	//		propriétés de la classe
	// ---------------------------------------------------------------
	public $id=0;
	public $libelle='';
	public $commentaire='';
	public $catalog_form=''; // forme pour affichage complet
	public $isbd_entry_lien_gestion=''; // pour affichage avec lien vers la gestion
	public $parent_id=0;
	public $parent_libelle = '';
	public $voir_id=0;
	public $has_child=FALSE;
	public $has_parent=FALSE;
	public $path_table;	// tableau contenant le path éclaté (ids et libellés)
	public $associated_terms; // tableau des termes associés
	public $is_under_tilde=0; // Savoir si c'est sous une catégorie qui commence par un ~
	public $thes;		//le thesaurus d'appartenance
	public $import_denied = 0;
	public $not_use_in_indexation=0; //Savoir si l'on peut utiliser le terme en indexation
	public $list_see;
	protected $listchilds;
	
	// ---------------------------------------------------------------
	//		category($id) : constructeur
	// ---------------------------------------------------------------
	public function __construct($id=0) {
		$this->id = $id+0;
		$this->is_under_tilde=0;
		if($this->id) {
			$this->thes = thesaurus::getByEltId($this->id);
		}
		$this->getData();
	}

	// ---------------------------------------------------------------
	//		getData() : récupération des propriétés
	// ---------------------------------------------------------------
	public function getData() {
		global $dbh;
		global $lang;
		global $opac_url_base, $use_opac_url_base;
		global $thesaurus_categories_show_only_last ; // le paramètre pour afficher le chemin complet ou pas
		$anti_recurse=array();
		
		if(!$this->id) return;
	
		$requete = "SELECT noeuds.id_noeud as categ_id, ";
		$requete.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle, ";
		$requete.= "noeuds.num_parent as categ_parent, ";
		$requete.= "noeuds.num_renvoi_voir as categ_see, ";
		$requete.= "noeuds.not_use_in_indexation as not_use_in_indexation, ";
		$requete.= "noeuds.authority_import_denied as authority_import_denied, ";	
		$requete.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application) as categ_comment ";
		$requete.= "FROM noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
		$requete.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
		$requete.= "where noeuds.id_noeud = '".$this->id."' limit 1 ";
	
		$result = pmb_mysql_query($requete, $dbh);
		if(!pmb_mysql_num_rows($result)) return;
		
		$data = pmb_mysql_fetch_object($result);
		$this->id = $data->categ_id;
		$id_top = $this->thes->num_noeud_racine;
		$this->libelle = $data->categ_libelle;
		if(preg_match("#^~#",$this->libelle)){
			$this->is_under_tilde=1;
		}
		$this->commentaire = $data->categ_comment;
		$this->parent_id = $data->categ_parent;
		$this->voir_id = $data->categ_see;
		$this->import_denied = $data->authority_import_denied;
		$this->not_use_in_indexation = $data->not_use_in_indexation;
		//$anti_recurse[$this->voir_id]=1;
		if($this->parent_id != $id_top) $this->has_parent = TRUE;
	
		$requete = "SELECT 1 FROM noeuds WHERE num_parent='".$this->id."' limit 1";
		$result = @pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($result)) $this->has_child = TRUE;
	
		// constitution du chemin
		$anti_recurse[$this->id]=1;
		$this->path_table=array();
		if ($this->has_parent) {
			$id_parent=$this->parent_id;
			do {
				$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment,if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
				FROM noeuds, categories where id_noeud ='".$id_parent."' 
				AND noeuds.id_noeud = categories.num_noeud 
				order by p desc limit 1";
				$result=@pmb_mysql_query($requete);
				if (pmb_mysql_num_rows($result)) {
					$parent = pmb_mysql_fetch_object($result);
					if(preg_match("#^~#",$parent->categ_libelle)){
						$this->is_under_tilde=1;
					}
					$anti_recurse[$parent->categ_id]=1;
					$this->path_table[] = array(
								'id' => $parent->categ_id,
								'libelle' => $parent->categ_libelle,
								'commentaire' => $parent->categ_comment);
					$id_parent=$parent->categ_parent;
				} else {
					break;
				}
				if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
			} while (($parent->categ_parent != $id_top) &&(!$anti_recurse[$parent->categ_parent]));
		}
		
		// ceci remet le tableau dans l'ordre général->particulier
		$this->path_table = array_reverse($this->path_table);
	
		if ($thesaurus_categories_show_only_last) {
			$this->catalog_form = $this->libelle;
			
			// si notre catégorie a un parent, on initie la boucle en le récupérant
			/*
			$requete_temp = "SELECT noeuds.id_noeud as categ_id, ";
			$requete_temp.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie) as categ_libelle ";
			$requete_temp.= "FROM noeuds left join categories as catdef on noeuds.id_noeud = catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
			$requete_temp.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
			$requete_temp.= "where noeuds.id_noeud = '".$this->parent_id."' limit 1 ";
	
			ER 12/08/2008 NOUVELLE VERSION OPTIMISEE DESSOUS : */
			$requete_temp = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment,if(langue = '".$lang."',2, if(langue= '".$this->thes->langue_defaut."' ,1,0)) as p
				FROM noeuds, categories where id_noeud ='".$this->parent_id."' 
				AND noeuds.id_noeud = categories.num_noeud 
				order by p desc limit 1";
			
			$result_temp=@pmb_mysql_query($requete_temp);
			if (pmb_mysql_num_rows($result_temp)) {
				$parent = pmb_mysql_fetch_object($result_temp);
				$this->parent_libelle = $parent->categ_libelle ;
			} else $this->parent_libelle ; 
	
		} elseif(sizeof($this->path_table)) {
			while(list($i, $l) = each($this->path_table)) {
				$temp_table[] = $l['libelle'];
			}
			$this->parent_libelle = join(':', $temp_table);
			$this->catalog_form = $this->parent_libelle.':'.$this->libelle;
		} else {
			$this->catalog_form = $this->libelle;
		}
	
		// Ajoute un lien sur la fiche catégorie si l'utilisateur à accès aux autorités, ou bien en envoi en OPAC.
		if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
		else $url_base_lien_aut="./autorites.php?categ=categories&sub=categ_form&id=";
		if (SESSrights & AUTORITES_AUTH || $use_opac_url_base) $this->isbd_entry_lien_gestion = "<a href='".$url_base_lien_aut.$this->id."' class='lien_gestion'>".$this->catalog_form."</a>";
		else $this->isbd_entry_lien_gestion = $this->catalog_form;
		
		//Recherche des termes associés
		$requete = "select count(1) from categories where num_noeud = '".$this->id."' and langue = '".$lang."' ";
		$result = pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_result($result, 0,0) == 0) $lg = $this->thes->langue_defaut ; 
		else $lg = $lang;  
	
		$requete = "SELECT distinct voir_aussi.num_noeud_dest as categ_assoc_categassoc, ";
		$requete.= "categories.libelle_categorie as categ_libelle, categories.note_application as categ_comment ";
		$requete.= "FROM voir_aussi, categories ";
		$requete.= "WHERE voir_aussi.num_noeud_orig='".$this->id."' ";
		$requete.= "AND categories.num_noeud=voir_aussi.num_noeud_dest "; 
		$requete.= "AND categories.langue = '".$lg."' ";
	
		$result=@pmb_mysql_query($requete,$dbh);
		while ($ta=pmb_mysql_fetch_object($result)) {
	
			//Recherche des renvois réciproques
			$requete1 = "select count(1) from voir_aussi where num_noeud_orig = '".$ta->categ_assoc_categassoc."' and num_noeud_dest = '".$this->id."' ";
			if (pmb_mysql_result(pmb_mysql_query($requete1, $dbh), 0, 0)) $rec=1;
			else $rec=0;
			
			$this->associated_terms[] = array(
				'id' => $ta->categ_assoc_categassoc,
				'libelle' => $ta->categ_libelle,
				'commentaire' => $ta->categ_comment,
				'rec' => $rec);
		}	 
	}

	public function has_notices() {
		global $dbh;
		global $thesaurus_auto_postage_montant,$thesaurus_auto_postage_descendant,$thesaurus_auto_postage_nb_montant,$thesaurus_auto_postage_nb_descendant;
		global $thesaurus_auto_postage_etendre_recherche,$nb_level_enfants,$nb_level_parents;
		$thesaurus_auto_postage_descendant = $thesaurus_auto_postage_montant=0;
		// Autopostage actif
		if ($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_montant ) {
			if(!isset($nb_level_enfants)) {
				// non defini, prise des valeurs par défaut
				if(isset($_SESSION["nb_level_enfants"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_descendant=$_SESSION["nb_level_enfants"];
				else $nb_level_descendant=$thesaurus_auto_postage_nb_descendant;
			} else {
				$nb_level_descendant=$nb_level_enfants;
			}				
			
			// lien Etendre auto_postage
			if(!isset($nb_level_parents)) {
				// non defini, prise des valeurs par défaut
				if(isset($_SESSION["nb_level_parents"]) && $thesaurus_auto_postage_etendre_recherche) $nb_level_montant=$_SESSION["nb_level_parents"];
				else $nb_level_montant=$thesaurus_auto_postage_nb_montant;
			} else {
				$nb_level_montant=$nb_level_parents;
			}	
			$_SESSION["nb_level_enfants"]=	$nb_level_descendant;
			$_SESSION["nb_level_parents"]=	$nb_level_montant;
			
			$q = "select path from noeuds where id_noeud = '".$this->id."' ";
			$r = pmb_mysql_query($q);
			$path=pmb_mysql_result($r, 0, 0);
			$nb_pere=substr_count($path,'/');
			// Si un path est renseigné et le paramètrage activé			
			if ($path && ($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && ($nb_level_montant || $nb_level_descendant)){
				
				//Recherche des fils 
				if(($thesaurus_auto_postage_descendant || $thesaurus_auto_postage_etendre_recherche)&& $nb_level_descendant) {
					if($nb_level_descendant != '*' && is_numeric($nb_level_descendant))
						$liste_fils=" path regexp '^$path(\\/[0-9]*){0,$nb_level_descendant}$' ";
					else 
						$liste_fils=" path regexp '^$path(\\/[0-9]*)*' ";
				} else {
					$liste_fils=" id_noeud='".$this->id."' ";
				}
						
				// recherche des pères
				if(($thesaurus_auto_postage_montant || $thesaurus_auto_postage_etendre_recherche) && $nb_level_montant) {
					
					$id_list_pere=explode('/',$path);			
					$stop_pere=0;
					if($nb_level_montant != '*' && is_numeric($nb_level_montant)) $stop_pere=$nb_pere-$nb_level_montant;
					for($i=$nb_pere;$i>=$stop_pere; $i--) {
						$liste_pere.= " or id_noeud='".$id_list_pere[$i]."' ";
					}
				}			
				// requete permettant de remonter les notices associées à la liste des catégories trouvées;
				$suite_req = " FROM noeuds inner join notices_categories on id_noeud=num_noeud inner join notices on notcateg_notice=notice_id 
					WHERE ($liste_fils $liste_pere) and notices_categories.notcateg_notice = notices.notice_id ";					
			} else {	
				// cas normal d'avant		
				$suite_req=" FROM notices_categories, notices WHERE notices_categories.num_noeud = '".$this->id."' and notices_categories.notcateg_notice = notices.notice_id ";
			}	
		
			$query ="SELECT COUNT(1) ".$suite_req;
		} else {
			// Autopostage désactivé	
			$query ="SELECT COUNT(1) FROM notices_categories WHERE notices_categories.num_noeud='".$this->id."' ";
			
		}	 
		$result = pmb_mysql_query($query, $dbh);
		return (pmb_mysql_result($result, 0, 0));
	}

	public function notice_count($include_subcategories=true) {
		/*
		 * $include_subcategories : Inclue également les notices dans les catégories filles
		 */
		$listcontent = array();
		if (!$include_subcategories) {
			$asql = "SELECT notcateg_notice FROM notices_categories WHERE num_noeud = ".$this->id;
			$ares = pmb_mysql_query($asql);
			while ($arow=pmb_mysql_fetch_row($ares)) {
				$listcontent[] = $arow[0];
			}
			$notice_count = count($listcontent);
			return $notice_count;
		}
		else {
			get_category_notice_count($this->id, $listcontent);
			$listcontent = array_unique($listcontent); //S'agirait pas d'avoir deux fois la même notice comptée.
			$notice_count = count($listcontent);
			return $notice_count;
		}
	}
	
	public static function get_informations_from_unimarc($fields,$link = false,$code_field="250"){
		$data = array();
		if(!$link){
			$data['label'] = $fields[$code_field][0]['a'][0];
			if($fields[$code_field][0]['j']){
				for($i=0 ; $i<count($fields[$code_field][0]['j']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['j'][$i];
				}
			}
			if($fields[$code_field][0]['x']){
				for($i=0 ; $i<count($fields[$code_field][0]['x']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['x'][$i];
				}
			}
			if($fields[$code_field][0]['y']){
				for($i=0 ; $i<count($fields[$code_field][0]['y']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['y'][$i];
				}
			}
			if($fields[$code_field][0]['z']){
				for($i=0 ; $i<count($fields[$code_field][0]['z']) ; $i++){
					$data['label'] .=  " -- ".$fields[$code_field][0]['z'][$i];
				}
			}		
			
			for ($i=0 ; $i<count($fields['300']) ; $i++){
				for($j=0 ; $j<count($fields['300'][$i]['a']) ; $j++){
					if($data['comment'] != "") $data['comment'].="\n";
					$data['comment'] .= $fields['300'][$i]['a'][$j];
				}
			}
			for ($i=0 ; $i<count($fields['330']) ; $i++){
				for($j=0 ; $j<count($fields['330'][$i]['a']) ; $j++){
					if($data['note'] != "") $data['note'].="\n";
					$data['note'] .= $fields['330'][$i]['a'][$j];
				}
			}
		}else{
			$data['label'] = $fields['a'][0];
			if($fields['j']){
				for($i=0 ; $i<count($fields['j']) ; $i++){
					$data['label'] .=  " -- ".$fields['j'][$i];
				}
			}
			if($fields['x']){
				for($i=0 ; $i<count($fields['x']) ; $i++){
					$data['label'] .=  " -- ".$fields['x'][$i];
				}
			}
			if($fields['y']){
				for($i=0 ; $i<count($fields['y']) ; $i++){
					$data['label'] .=  " -- ".$fields['y'][$i];
				}
			}
			if($fields['z']){
				for($i=0 ; $i<count($fields['z']) ; $i++){
					$data['label'] .=  " -- ".$fields['z'][$i];
				}
			}		
			$data['authority_number'] = $fields['3'][0];
		}
		$data['type_authority'] = "category";
		return $data; 
	}
	
	public static function import($data, $id_thesaurus, $num_parent = 0, $lang=""){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}
		
		if($data['label'] == ""){
			return 0;
		}
		if($num_parent){//Le noeud parent doit être dans le même thésaurus
			$req="SELECT id_noeud FROM noeuds WHERE id_noeud='".$num_parent."' AND num_thesaurus='".$id_thesaurus."'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				return 0;
			}
		}
		
		$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$id = categories::searchLibelle(addslashes($data['label']), $id_thesaurus, $lang, $num_parent);
			if(!$id){
				//création
				$n=new noeuds();
				$n->num_parent=($num_parent != 0 ? $num_parent : $row->num_noeud_racine);
				$n->num_thesaurus=$id_thesaurus;
				$n->num_statut = ($data['statut'] ? $data['statut']+= 0 : $data['statut'] = 1);
				$n->save();
				$id = $n->id_noeud;
				$c=new categories($id, $lang);
				$c->libelle_categorie=$data['label'];
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
			}else{
				$c=new categories($id, $lang);
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
			}
		}else{
			//pas de thésausus, on peut rien faire...
			return 0;
		}
		return $id;
	}
	
	public function check_if_exists($data, $id_thesaurus, $num_parent = 0, $lang=""){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}
		
		if($data['label'] == ""){
			return 0;
		}
		
		$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
		$result = pmb_mysql_query($query);
		$id=0;
		if(pmb_mysql_num_rows($result)){
			$row = pmb_mysql_fetch_object($result);
			$id = categories::searchLibelle(addslashes($data['label']), $id_thesaurus, $lang, $num_parent);
		}
		return $id;
	}
	
	/*
	 * Pour import autorité
	 */
	public function update($data,$id_thesaurus,$num_parent,$lang){
		$lang = strtolower($lang);
		switch($lang){
			case "fr" :
			case "fre" :
			case "français" :
			case "francais" :
			case "french" :
				$lang = "fr_FR";
				break;
			default :
				$lang = "fr_FR";
				break;
		}
		
		if($data['label'] == ""){
			return 0;
		}
		if($num_parent){//Le noeud parent doit être dans le même thésaurus
			$req="SELECT id_noeud FROM noeuds WHERE id_noeud='".$num_parent."' AND num_thesaurus='".$id_thesaurus."'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				return 0;
			}
		}
		if($this->id == 0){
			$query = "select * from thesaurus where id_thesaurus = ".$id_thesaurus;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				//création
				$n=new noeuds();
				$n->num_parent=($num_parent != 0 ? $num_parent : $row->num_noeud_racine);
				$n->num_thesaurus=$id_thesaurus;
				$n->save();
				$id = $n->id_noeud;
				$c=new categories($id, $lang);
				$c->libelle_categorie= $data['label'];
				$c->note_application = $data['note'];
				$c->comment_public = $data['comment'];
				$c->save();
				$this->id = $c->num_noeud;
				return 1;
			}
		}else{
			$c=new categories($this->id, $lang);
			$c->libelle_categorie= $data['label'];
			$c->note_application = $data['note'];
			$c->comment_public = $data['comment'];
			$c->save();
			return 1;
		}
	}
	
	public function listChilds() {
		global $dbh;
		global $lang;
		if(!isset($this->listchilds)){

			if ($this->id == $this->thes->num_noeud_racine){
				$keep_tilde = 0;
			}else{
				$keep_tilde = 1;
			}
			
			$q = "select ";
			$q.= "catdef.num_noeud, noeuds.autorite, noeuds.num_parent, noeuds.num_renvoi_voir, noeuds.visible, noeuds.num_thesaurus, ";
			$q.= "if (catlg.num_noeud is null, catdef.langue, catlg.langue ) as langue, ";
			$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as libelle_categorie, ";
			$q.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application ) as note_application, ";
			$q.= "if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public ) as comment_public, ";
			$q.= "if (catlg.num_noeud is null, catdef.comment_voir, catlg.comment_voir ) as comment_voir, ";
			$q.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie ) as index_categorie ";
			$q.= "from noeuds left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$this->thes->langue_defaut."' ";
			$q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
			$q.= "where ";
			$q.= "noeuds.num_parent = '".$this->id."' ";
			if (!$keep_tilde) $q.= "and catdef.libelle_categorie not like '~%' ";
			$q.= "order by libelle_categorie ";
			// Possibilité d'ajouter une limitation ici (voir nouveau paramètre gestion)
			$q.="";
			
			$r = pmb_mysql_query($q, $dbh);
			while($child=pmb_mysql_fetch_object($r)) {
				
				$this->listchilds[]= array(
					'id' => $child->num_noeud,
					'name' => $child->comment_public,
					'libelle' => $child->libelle_categorie
				);
			}
			
		}
		return $this->listchilds;
	}
	
	
	/**
	 * Permet de récupérer les catégories dont le num_renvoi correspond à l'id du noeud courant
	 */
	public function listSynonyms(){
		if (isset($this->list_see)) {
			return $this->list_see;
		}
		global $dbh,$lang;
		
		$this->list_see = array();
		$thes = thesaurus::getByEltId($this->id);
		$q = "select id_noeud from noeuds where num_thesaurus = '".$thes->id_thesaurus."' and autorite = 'ORPHELINS' ";
		
		$r = pmb_mysql_query($q, $dbh);
		if($r && pmb_mysql_num_rows($r)){
			$num_noeud_orphelins = pmb_mysql_result($r, 0, 0);
		}else{
			$num_noeud_orphelins=0;
		}		
		$q = "select ";
		$q.= "catdef.num_noeud, noeuds.autorite, noeuds.num_parent, noeuds.num_renvoi_voir, noeuds.visible, noeuds.num_thesaurus, ";
		$q.= "if (catlg.num_noeud is null, catdef.langue, catlg.langue ) as langue, ";
		$q.= "if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as libelle_categorie, ";
		$q.= "if (catlg.num_noeud is null, catdef.note_application, catlg.note_application ) as note_application, ";
		$q.= "if (catlg.num_noeud is null, catdef.comment_public, catlg.comment_public ) as comment_public, ";
		$q.= "if (catlg.num_noeud is null, catdef.comment_voir, catlg.comment_voir ) as comment_voir, ";
		$q.= "if (catlg.num_noeud is null, catdef.index_categorie, catlg.index_categorie ) as index_categorie ";
		$q.= "from noeuds left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = '".$thes->langue_defaut."' ";
		$q.= "left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."' ";
		$q.= "where ";
		$q.= "noeuds.num_parent = '$num_noeud_orphelins' and noeuds.num_renvoi_voir='".$this->id."' ";
		//if (!$keep_tilde) $q.= "and catdef.libelle_categorie not like '~%' ";
		//if ($ordered !== 0) $q.= "order by ".$ordered." ";
		$q.=""; // A voir pour ajouter un parametre gestion maxddisplay
		$r = pmb_mysql_query($q, $dbh);
		
		while($cat_see=pmb_mysql_fetch_object($r)) {
			$this->list_see[]= array(
					'id' => $cat_see->num_noeud,
					'name' => $cat_see->comment_public,
					'parend_id' => $cat_see ->num_parent,
					'libelle' => $cat_see->libelle_categorie
			);
		}
		return $this->list_see;
	}
	
	public function get_header() {
		return $this->catalog_form;
	}
	
	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=categ&id='.$this->id;
	}
	
	public function get_isbd() {
		return $this->libelle;
	}
	
	public static function get_format_data_structure($antiloop = false) {
		global $msg;
			
		$main_fields = array();
		$main_fields[] = array(
				'var' => "name",
				'desc' => $msg['103']
		);
		$main_fields[] = array(
				'var' => "comment",
				'desc' => $msg['categ_commentaire']
		);
		if(!$antiloop) {
			$main_fields[] = array(
					'var' => "parent",
					'desc' => $msg['categ_parent'],
					'children' => authority::prefix_var_tree(category::get_format_data_structure(true),"parent")
			);
			$main_fields[] = array(
					'var' => "renvoi",
					'desc' => $msg['categ_renvoi'],
					'children' => authority::prefix_var_tree(category::get_format_data_structure(true),"renvoi")
			);
			/*$main_fields[] = array(
					'var' => "renvoi_voir_aussi",
					'desc' => $msg['renvoi_voir_aussi'],
					'children' => authority::prefix_var_tree(category::get_format_data_structure(true),"renvoi_voir_aussi[i]")
			);*/
		}

		$authority = new authority(0, 0, AUT_TABLE_CATEG);
		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		$parent_datas = array();
		$renvoi_datas = array();
		if(!$antiloop) {
			if($this->parent_id) {
				$parent = new category($this->parent_id);
				$parent_datas = $parent->format_datas(true);
			}
			if($this->voir_id) {
				$renvoi = new category($this->voir_id);
				$renvoi_datas = $renvoi->format_datas(true);
			}
		}
		$formatted_data = array(
				'name' => $this->libelle,
				'comment' => $this->commentaire,
				'parent' => $parent_datas,
				'renvoi' => $renvoi_datas,
// 				'renvoi_voir_aussi' =>
		);
		$authority = new authority(0, $this->id, AUT_TABLE_CATEG);
		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	/**
	 * Suppression d'une catégorie
	 */
	public function delete() {
		global $msg, $charset, $parent, $force_delete_target, $forcage, $ret_url;
		global $pmb_synchro_rdf, $current_module;
		
		if (noeuds::hasChild($this->id)) {
			//cette autorité a des sous catégories
			return return_error_message($msg[321], $msg[322], 1, "./autorites.php?categ=categories&id=".$this->id."&sub=categ_form&parent=".$parent);
		} elseif (count(noeuds::listTargetsExceptOrphans($this->id))){
			//d'autres catégories renvoient vers elle	
			return return_error_message($msg[321], $msg["thes_suppr_impossible_renvoi_voir"], 1, "./autorites.php?categ=categories&id=".$this->id."&sub=categ_form&parent=".$parent);
		} elseif (noeuds::isProtected($this->id)) {
			//catégorie protégée
			return return_error_message($msg[321], $msg["thes_suppr_impossible_protege"], 1, "./autorites.php?categ=categories&id=".$this->id."&sub=categ_form&parent=".$parent);
		} elseif (count(vedette_composee::get_vedettes_built_with_element($this->id, "category"))) {
			// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
			return return_error_message($msg[321], $msg["vedette_dont_del_autority"], 1).'<br/>'.vedette_composee::get_vedettes_display($attached_vedettes);
		}elseif(($usage=aut_pperso::delete_pperso(AUT_TABLE_CATEG, $this->id,0) )){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			return return_error_message($msg[321], $msg["autority_delete_error"].'<br /><br />'.$usage['display'], 1);
		} elseif (noeuds::isUsedInNotices($this->id)) {
			if ($forcage == 1) {
				$tab= unserialize( urldecode($ret_url) );
				foreach($tab->GET as $key => $val){
					$GLOBALS[$key] = $val;
				}
				foreach($tab->POST as $key => $val){
					$GLOBALS[$key] = $val;
				}
				
				$this->maj_graph_rdf($this->id);
				$this->delete_node_and_index($this->id);
				
				$requete="DELETE FROM notices_categories WHERE num_noeud=".$this->id;
				pmb_mysql_query($requete, $dbh);
				
			} else {
				$tab = new stdClass();
				$requete="SELECT notcateg_notice FROM notices_categories WHERE num_noeud=".$this->id." ORDER BY ordre_categorie";
				$result_cat=pmb_mysql_query($requete, $dbh);
				if (pmb_mysql_num_rows($result_cat)) {
					//affichage de l'erreur, en passant tous les param postés (serialise) pour l'éventuel forcage
					$tab->POST = $_POST;
					$tab->GET = $_GET;
					$ret_url= urlencode(serialize($tab));
					 
					$html = "
					<br /><div class='erreur'>".$msg[540]."</div>
					<script type='text/javascript' src='./javascript/tablist.js'></script>
					<script>
						function confirm_delete() {
							phrase = \"".$msg["autorite_confirm_suppr_categ"]."\";
							result = confirm(phrase);
							if(result) form.submit();
						}
					</script>
					<div class='row'>
						<div class='colonne10'>
							<img src='./images/error.gif' align='left'>
						</div>
						<div class='colonne80'>
							<strong>".$msg["autorite_suppr_categ_titre"]."</strong>
						</div>
					</div>
					<div class='row'>
						<form class='form-".$current_module."' name='dummy'  method='post' action='./autorites.php?categ=categories&sub=delete&parent=".$parent."&id=".$this->id."'>
							<input type='hidden' name='forcage' value='1'>
							<input type='hidden' name='ret_url' value='".$ret_url."'>
							<input type='button' name='ok' class='bouton' value='".$msg[89]."' onClick='history.go(-1);'>
							<input type='submit' class='bouton' name='bt_forcage' value='".htmlentities($msg["autorite_suppr_categ_forcage_button"], ENT_QUOTES,$charset)."'  onClick=\"confirm_delete();return false;\">
						</form>
					</div>";
					while (($r_cat=pmb_mysql_fetch_object($result_cat))) {
						$requete="select signature, niveau_biblio ,notice_id from notices where notice_id=".$r_cat->notcateg_notice." limit 20";
						$result=pmb_mysql_query($requete, $dbh);
						if (($r=pmb_mysql_fetch_object($result))) {		
							if($r->niveau_biblio != 's' && $r->niveau_biblio != 'a') {
								// notice de monographie
								$nt = new mono_display($r->notice_id);
							} else {
								// on a affaire à un périodique
								$nt = new serial_display($r->notice_id,1);
							}
							$html .= "
								<div class='row'>
									".$nt->result."
								</div>";
						}
						$html .= "<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";
					}
					return $html;
				}
			}
		} elseif (count(noeuds::listTargetsOrphansOnly($this->id)) && !isset($force_delete_target)) {
			return return_box_confirm_message($msg[321], $msg["confirm_suppr_categ_rejete"], "./autorites.php?categ=categories&sub=delete&parent=".$parent."&id=".$this->id."&force_delete_target=1", "./autorites.php?categ=categories&id=".$this->id."&sub=categ_form&parent=".$parent, $msg[40], $msg[39]);
		} else {
			$array_to_delete = array();
			$id_list_orphans = noeuds::listTargetsOrphansOnly($this->id);
						
			if (count($id_list_orphans)) {
				foreach ($id_list_orphans as $id_orphan) {
					// on n'efface pas les termes orphelins avec terme spécifique
					// on n'efface pas les termes orphelins utilisées en indexation
					if (!noeuds::hasChild($id_orphan) && !noeuds::isUsedInNotices($id_orphan)) {
						$array_to_delete[] = $id_orphan;
					}
				}
			}
			$array_to_delete[] = $this->id;
		
			foreach($array_to_delete as $id_to_delete){
				$this->maj_graph_rdf($id_to_delete);				
				$this->delete_node_and_index($id_to_delete);
			}
		}
		return false;
	}
	
	protected function maj_graph_rdf($id) {
		global $pmb_synchro_rdf;
		//On met à jour le graphe rdf avant de supprimer
		if ($pmb_synchro_rdf) {
			$arrayIdImpactes=array();
			$synchro_rdf=new synchro_rdf();
			$noeud=new noeuds($id);
			$thes=new thesaurus($noeud->num_thesaurus);
			//parent
			if($noeud->num_parent!=$thes->num_noeud_racine){
				$arrayIdImpactes[]=$noeud->num_parent;
			}
			//renvoi_voir
			if($noeud->num_renvoi_voir){
				$arrayIdImpactes[]=$noeud->num_renvoi_voir;
			}
			//on supprime le rdf
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->delConcept($idNoeud);
				}
			}
			$synchro_rdf->delConcept($id);
			
			//On remet à jour les noeuds impactes
			if(count($arrayIdImpactes)){
				foreach($arrayIdImpactes as $idNoeud){
					$synchro_rdf->storeConcept($idNoeud);
				}
			}
			//On met à jour le thésaurus pour les topConcepts
			$synchro_rdf->updateAuthority($noeud->num_thesaurus,'thesaurus');
		}
	}
	
	protected function delete_node_and_index($id) {
		// nettoyage indexation concepts
		$index_concept = new index_concept($id, TYPE_CATEGORY);
		$index_concept->delete();
			
		noeuds::delete($id);
	}
	
	public function get_right() {		
		return SESSrights & THESAURUS_AUTH;
	}
} # fin de définition de la classe category

} # fin de déclaration
