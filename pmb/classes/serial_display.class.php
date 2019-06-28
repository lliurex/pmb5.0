<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_display.class.php,v 1.190.2.3 2017-10-12 12:58:42 jpermanne Exp $

// --------------------- LLIUREX 21/02/2018-----------------------
// no detecta idioma y por lo tanto no forma correctamente el path
$lang="es_ES";
// --------------------- FIN LLIUREX 21/02/2018-------------------- 

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/marc_table.class.php");
require_once($class_path."/mono_display.class.php");
require_once($class_path."/collstate.class.php");
require_once($include_path."/notice_authors.inc.php");
require_once($include_path."/notice_categories.inc.php");
require_once($include_path."/explnum.inc.php");
require_once("$class_path/authperso_notice.class.php");
require_once("$class_path/map/map_objects_controler.class.php");
require_once("$class_path/map_info.class.php");
require_once($class_path."/tu_notice.class.php");
require_once ($class_path."/map/map_locations_controler.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");

// récupération des codes de fonction
if (!isset($fonction_auteur)) {
	$fonction_auteur = new marc_list('function');
	$fonction_auteur = $fonction_auteur->table;
}

// propriétés pour le selecteur de panier (kinda template)
$selector_prop = "toolbar=no, dependent=yes, width=500, height=400, resizable=yes, scrollbars=yes";
$cart_click = "onClick=\"openPopUp('./cart.php?object_type=NOTI&item=!!item!!&unq=!!unique!!', 'cart', 600, 700, -2, -2, '$selector_prop')\"";

// définition de la classe d'affichage des périodiques
class serial_display {
	public $notice_id = 0;					// id de la notice à afficher
  	public $notice;						// objet notice (tel que fetché dans la table 'notices'
	public $bul_id	= 0 ;					// id de bulletin récupéré pour l'ISBD
  	public $action_serial_org = '';		// lien à activer si la notice est s1 (notice chapeau)
  	public $action_analysis_org = '';		// lien à activer si la notice est a2 (dépouillment)
	public $action_serial = '';			// lien modifié pour le header
	public $action_analysis = '';			// lien modifié pour le header (nécessite !!bul_id!!)
	public $action_bulletin = '';			// action pour la notion de bulletin
	public $header	= '';					// chaine accueillant le chapeau de notice (peut-être cliquable)
	public $header_texte	= '';			// chaine accueillant le chapeau de notice sans html
	public $tit1 = '';						// valeur du titre 1
	public $parent_id = 0;					// id de la notice parent
	public $parent_title = '';				// titre de la notice parent si a2
	public $parent_numero = '';			// mention de numérotation dans le bulletinage associé
	public $parent_date = '';				// mention de date (txt) dans le bulletinage associé
	public $parent_date_date = '';			// mention de date (date) dans le bulletinage associé
	public $parent_aff_date_date = '';		// mention de date (date) dans le bulletinage associé au format correct pour affichage
	public $result = '';					// affichage final
	public $level = 1;						// niveau d'affichage
	public $isbd = '';						// isbd de la notice en fonction du level défini
	public $responsabilites = array("responsabilites" => array(),"auteurs" => array());  // les auteurs
	public $categories = array();			// les categories
	public $lien_explnum = '';				// Lien de gestion des documents numériques associés
	public $bouton_explnum = 0 ;			// bouton ou pas d'ajout de doc numérique
	public $p_perso;
	public $show_explnum = 1;
	public $show_statut = 0;
	public $childs= array(); 				//Filles de la notice
	public $print_mode = 0;				// 0 affichage normal
										// 1 affichage impression sans liens
										// 2 affichage impression avec liens sur documents numeriques
										// 4 affichage email : sans lien sauf url associée
	public $langues = array();
	public $languesorg = array();
	public $aff_statut = '' ; 				// carré de couleur pour signaler le statut de la notice
	public $show_opac_hidden_fields = true;
	public $drag = 0;
	public $anti_loop = "";
	public $no_link = false;
	public $serial_nb_bulletins = 0;
	public $serial_nb_exemplaires = 0;
	public $serial_nb_articles = 0;
	public $serial_nb_etats_collection = 0;
	public $serial_nb_abo_actif = 0;
	public $show_map=1;
	public $context_dsi_id_bannette=0;
	public $notice_relations;	//Objet notice_relations
	public $show_abo_actif = 0;
	
	// constructeur
	public function __construct(	$id,						// $id = id de la notice à afficher
								$level='1', 				// $level :
															// 0 : juste le header (titre  / auteur principal avec le lien si applicable)
															// 	6 : cas général détaillé avec notes, categ, langues, indexation... + boutons
								$action_serial='', 			// $action_serial = URL à atteindre si la notice est une notice chapeau
								$action_analysis='', 		// $action_analysis = URL à atteindre si la notice est un dépouillement
															// note dans ces deux variable, '!!id!!' sera remplacé par l'id de cette notice
															// les deux liens s'excluent mutuellement, bien sur.
								$action_bulletin='',
								$lien_suppr_cart="", 		// $lien_suppr_cart = lien de suppression de la notice d'un caddie
								$lien_explnum="",
								$bouton_explnum=1,
								$print=0,					// $print = 0 affichage normal
															//			1 affichage impression sans liens
															//			2 affichage impression avec liens sur documents numeriques
															// 			4 affichage email : sans lien sauf url associée

								$show_explnum=1,
								$show_statut=0,
								$show_opac_hidden_fields=true,
								$draggable=0,
								$ajax_mode=0 ,
								$anti_loop='',
								$no_link=false,
								$show_map=1,                // $show_map = affichage de la map
								$context_dsi_id_bannette=0,  // $context_dsi_id_bannette = dans le contexte de la dsi
								$show_abo_actif = 0
								) {

		global $pmb_recherche_ajax_mode;

		$this->show_map=$show_map;
		$this->context_dsi_id_bannette=$context_dsi_id_bannette;

	  	if($pmb_recherche_ajax_mode){
			$this->ajax_mode=$ajax_mode;
		  	if($this->ajax_mode) {
				if (is_object($id)){
					$param['id']=$id->notice_id;
				} else {
					$param['id']=$id;
				}
				$param['function_to_call']="serial_display";
			  	//if($level)$param['level']=$level;	//6
				if($action_serial)$param['action_serial']=$action_serial;
				if($action_analysis)$param['action_analysis']=$action_analysis;
				if($action_bulletin)$param['action_bulletin']=$action_bulletin;
//			  	if($lien_suppr_cart)$param['lien_suppr_cart']=$lien_suppr_cart;
 			  	if($lien_explnum)$param['lien_explnum']=$lien_explnum;
				if($bouton_explnum)$param['bouton_explnum']=$bouton_explnum;
			  	if($print)$param['print']=$print;
			  //	if($show_explnum)$param['show_explnum']=$show_explnum;
			  	//if($show_statut)$param['show_statut']=$show_statut;
			  	//if($show_opac_hidden_fields)$param['show_opac_hidden_fields']=$show_opac_hidden_fields;
			  	//if($draggable)$param['draggable']=$draggable;//1

			  	$this->mono_display_cmd=serialize($param);
		  	}
	  	}
		$this->lien_explnum = $lien_explnum ;
		$this->bouton_explnum = $bouton_explnum ;
		$this->print_mode=$print;
		$this->show_explnum=$show_explnum;
		$this->show_statut=$show_statut;
		$this->show_abo_actif=$show_abo_actif;
		$this->anti_loop=$anti_loop;
		$this->no_link=$no_link;
		if(!$id) return; else {
			if (is_object($id)){
				$this->notice_id = $id->notice_id;
				$this->notice = $id;
			} else {
					$this->notice_id = $id;
					$this->serial_display_fetch_data();
			}
		}

		$this->show_opac_hidden_fields=$show_opac_hidden_fields;
		if(!$this->ajax_mode)$this->p_perso=new parametres_perso("notices");

		$this->responsabilites = get_notice_authors($this->notice_id) ;

		// mise à jour des catégories
		if(!$this->ajax_mode)$this->categories = get_notice_categories($this->notice_id) ;

		//récupération des langues
		$this->langues	= get_notice_langues($this->notice_id, 0) ;	// langues de la publication
		$this->languesorg	= get_notice_langues($this->notice_id, 1) ; // langues originales

		$this->level = $level;
		$this->lien_suppr_cart = $lien_suppr_cart;

		// si la notice est a2 (dépouillement), on récupère les données du bulletinage
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {
			$this->get_bul_info();
		}

		// mise à jour des liens
		if (SESSrights & CATALOGAGE_AUTH){
			$this->action_serial_org = $action_serial;
			$this->action_analysis = $action_analysis;
			$this->action_bulletin = $action_bulletin;
			if ($action_serial && $this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == '1')
				$this->action_serial = str_replace('!!id!!', $this->notice_id, $action_serial);
			if ($action_analysis && $this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2') {
				$this->action_analysis = str_replace('!!id!!', $this->notice_id, $this->action_analysis);
				$this->action_analysis = str_replace('!!bul_id!!', $this->bul_id, $this->action_analysis);
				}
			$this->lien_explnum = str_replace('!!serial_id!!', $this->notice_id, $this->lien_explnum);
			$this->lien_explnum = str_replace('!!analysis_id!!', $this->notice_id, $this->lien_explnum);
			$this->lien_explnum = str_replace('!!bul_id!!', $this->bul_id, $this->lien_explnum);
			$this->drag=$draggable;
		}else{
			$this->action_serial_org = "";
			$this->action_analysis = "";
			$this->action_bulletin = "";
			$this->action_serial = "";
			$this->lien_explnum = "";
			$this->drag="";
		}

		$this->do_header();

		if($level)
			$this->init_javascript();
		$this->isbd = 'ISBD';

		$this->notice_relations = notice_relations_collection::get_object_instance($this->notice_id);
		if(!$this->ajax_mode) {
			$this->childs = $this->notice_relations->get_childs();
		}

		switch($level) {
			case 0:
				// là, c'est le niveau 0 : juste le header
				//$this->do_header();
				$this->result = $this->header;
				break;
			default:
				global $pmb_map_activate;
				$this->map=array();
				if($pmb_map_activate){
					$ids[]=$this->notice_id;
					$this->map=new map_objects_controler(TYPE_RECORD,$ids);
					$this->map_info=new map_info($this->notice_id);
				}
				// niveau 1 et plus : header + isbd à générer
				//$this->do_header();
				if(!$this->ajax_mode) $this->do_isbd();
				if(!$this->ajax_mode) $this->finalize();
				break;
			}
		return;

		}

	// récupération des info de bulletinage (si applicable)
	public function get_bul_info() {
		global $dbh;
		global $msg ;

		// récupération des données du bulletin et de la notice apparentée
		$requete = "SELECT b.tit1,b.notice_id,b.code,a.*,c.*, date_format(date_date, '".$msg["format_date"]."') as aff_date_date ";
		$requete .= "from analysis a, notices b, bulletins c";
		$requete .= " WHERE a.analysis_notice=".$this->notice_id;
		$requete .= " AND c.bulletin_id=a.analysis_bulletin";
		$requete .= " AND c.bulletin_notice=b.notice_id";
		$requete .= " LIMIT 1";
		$myQuery = pmb_mysql_query($requete, $dbh);
		if (pmb_mysql_num_rows($myQuery)) {
			$parent = pmb_mysql_fetch_object($myQuery);
			$this->parent_title = $parent->tit1;
			$this->parent_id = $parent->notice_id;
			$this->code=$parent->code;
			$this->bul_id = $parent->bulletin_id;
			$this->parent_numero = $parent->bulletin_numero;
			$this->parent_date = $parent->mention_date;
			$this->parent_date_date = $parent->date_date;
			$this->parent_aff_date_date = $parent->aff_date_date;
			}
		}

	// finalisation du résultat (écriture de l'isbd)
	public function finalize() {
		global $msg, $base_path ;

		// Différence avec les monographies on affiche [périodique] et [article] devant l'ISBD
		if ($this->notice->niveau_biblio =='s') {
			$this->result = str_replace('!!serial_type!!', "<span class='fond-mere'>[".$msg['isbd_type_perio']."]</span>", $this->result);
			} else {
				$this->result = str_replace('!!serial_type!!', "<span class='fond-article'>[".$msg['isbd_type_art']."]</span>", $this->result);
				}
		$this->result = str_replace('!!ISBD!!', $this->isbd, $this->result);
	}

	// génération du template javascript
	public function init_javascript() {
		global $msg, $base_path, $pmb_recherche_ajax_mode, $art_to_show;
		// propriétés pour le selecteur de panier
		$selector_prop = "toolbar=no, dependent=yes, width=500, height=400, resizable=yes, scrollbars=yes";
		$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=NOTI&item=!!notice_id!!', 'cart', 600, 700, -2, -2, '$selector_prop')\"";
		$cart_over_out = "onMouseOver=\"show_div_access_carts(event,!!notice_id!!);\" onMouseOut=\"set_flag_info_div(false);\"";
		$current=$_SESSION["CURRENT"];
		if ($current!==false) {
			$print_action = "&nbsp;<a href='#' onClick=\"openPopUp('".$base_path."/print.php?current_print=$current&notice_id=!!notice_id!!&action_print=print_prepare','print',500,600,-2,-2,'scrollbars=yes,menubar=0'); w.focus(); return false;\"><img src='".$base_path."/images/print.gif' border='0' align='center' alt=\"".$msg["histo_print"]."\" title=\"".$msg["histo_print"]."\"/></a>";
		}

		if(($art_to_show == $this->notice_id) && $art_to_show){
			$open_tag = "startOpen=\"Yes\"";
			$anchor = "<a name='anchor_$art_to_show'></a>";
		} else {
			$open_tag = "";
			$anchor = "";
		}

		if($pmb_recherche_ajax_mode && $this->ajax_mode){
			$javascript_template ="$anchor
		<div id=\"el!!id!!Parent\" class=\"notice-parent\">
    		<img src=\"".$base_path."/images/plus.gif\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" param='".rawurlencode($this->mono_display_cmd)."' title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase_ajax('el!!id!!', true,this.getAttribute('param')); return false;\" hspace=\"3\">
    		<span class=\"notice-heada\">!!heada!!</span>
		</div>
		<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" $open_tag >
 		</div>";

		} else{
			if(SESSrights & CATALOGAGE_AUTH){
				$caddie="<img src='".$base_path."/images/basket_small_20x20.gif' align='middle' alt='basket' title=\"${msg[400]}\" $cart_click $cart_over_out>";
			}else{
				$caddie="";
			}

			$javascript_template ="$anchor
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				<img src=\"".$base_path."/images/plus.gif\" class=\"img_plus\" name=\"imEx\" id=\"el!!id!!Img\" title=\"".$msg['admin_param_detail']."\" border=\"0\" onClick=\"expandBase('el!!id!!', true); return false;\" hspace=\"3\" />
				<span class=\"notice-heada\">!!heada!!</span>
			</div>
			<div id=\"el!!id!!Child\" class=\"notice-child\" style=\"margin-bottom:6px;display:none;\" $open_tag >
				$caddie$print_action !!serial_type!! !!ISBD!!
 		</div>";
		}
		$microtime = md5(microtime());
		$this->result = str_replace('!!id!!', $this->notice_id.($this->anti_loop?"_p".implode("_",$this->anti_loop):"").'_'.$microtime, $javascript_template);
		$this->result = str_replace('!!item!!', $this->notice_id, $this->result);
		$this->result = str_replace('!!unique!!', $microtime, $this->result);
		$this->result = str_replace('!!heada!!', $this->lien_suppr_cart.$this->header, $this->result);
		$this->result = str_replace('!!notice_id!!', $this->notice_id, $this->result);
	}

	// génération de l'isbd
	public function do_isbd() {
		global $msg, $dbh, $base_path;
		global $tdoc;
		global $charset;
		global $thesaurus_mode_pmb, $thesaurus_categories_categ_in_line, $pmb_keyword_sep, $thesaurus_categories_affichage_ordre;
		global $load_tablist_js;
		global $pmb_etat_collections_localise,$pmb_droits_explr_localises,$explr_visible_mod;
		global $categories_memo,$libelle_thesaurus_memo;
		global $categories_top,$use_opac_url_base,$opac_url_base,$thesaurus_categories_show_only_last, $opac_categories_show_only_last;
		global $pmb_show_notice_id,$pmb_opac_url,$pmb_show_permalink;
		global $sort_children;
		global $thesaurus_concepts_active;
		global $pmb_map_activate;
		global $pmb_scan_request_activate;
		global $lang;
		global $pmb_use_uniform_title;
		
		$this->isbd = $this->notice->tit1;

		// constitution de la mention de titre
		$tit3 = $this->notice->tit3;
		$tit4 = $this->notice->tit4;
		if($tit3) $this->isbd .= "&nbsp;= $tit3";
		if($tit4) $this->isbd .= "&nbsp;: $tit4";
		$this->isbd .= ' ['.$tdoc->table[$this->notice->typdoc].']';
	
		// constitution de la mention de responsabilité
		if($libelle_mention_resp = gen_authors_isbd($this->responsabilites, $this->print_mode)) {
			$this->isbd .= "&nbsp;/ ". $libelle_mention_resp ." " ;
		}

		if($pmb_map_activate){
			if($mapisbd=$this->map_info->get_isbd())	$this->isbd .=$mapisbd;
		}
		// zone de l'adresse (ne concerne que s1)
		if ($this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == 1) {
			$editeurs = "";
			if($this->notice->ed1_id) {
				$editeur = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $this->notice->ed1_id);
				if ($this->print_mode) {
					$editeurs .= $editeur->isbd_entry;
				} else {
					$editeurs .= $editeur->isbd_entry_lien_gestion;
				}
			}
			if($this->notice->ed2_id) {
				$editeur = authorities_collection::get_authority(AUT_TABLE_PUBLISHERS, $this->notice->ed2_id);
				if ($this->print_mode) {
					$ed_isbd=$editeur->isbd_entry;
				} else {
					$ed_isbd=$editeur->isbd_entry_lien_gestion;
				}
				if($editeurs) {
					$editeurs .= '&nbsp;; '.$ed_isbd;
				} else {
					$editeurs .= $ed_isbd;
				}
			}

			if($this->notice->year) {
				$editeurs ? $editeurs .= ', '.$this->notice->year : $editeurs = $this->notice->year;
			}

			if($editeurs) {
				$this->isbd .= ".&nbsp;-&nbsp;$editeurs";
			}
		}

		// zone de la collation (ne concerne que a2, mention de pagination)
		// pour les périodiques, on rebascule en zone de note
		// avec la mention du périodique parent
		$mention_parent = "";
		if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {

			$bulletin = $this->parent_title;
			if($this->parent_numero) {
				$bulletin .= ' '.$this->parent_numero;
			}
			// affichage de la mention de date utile : mention_date si existe, sinon date_date
			if ($this->parent_date)
				$date_affichee = " (".$this->parent_date.")";
			else if ($this->parent_date_date)
				$date_affichee = " [".formatdate($this->parent_date_date)."]";
			else
				$date_affichee="" ;
			$bulletin .= $date_affichee;

			if($this->action_bulletin) {
				$this->action_bulletin = str_replace('!!id!!', $this->bul_id, $this->action_bulletin);
				$bulletin = "<a href=\"".$this->action_bulletin."\">".htmlentities($bulletin,ENT_QUOTES, $charset)."</a>";
			}
			$mention_parent = "in <b>$bulletin</b>";
		}

		if($mention_parent) {
			$this->isbd .= "<br />$mention_parent";
			$pagination = htmlentities($this->notice->npages,ENT_QUOTES, $charset);
			if($pagination)
				$this->isbd .= ".&nbsp;-&nbsp;$pagination";
		}

		//In
		//Recherche des notices parentes
		if (!$this->no_link) {
			$this->isbd .= $this->notice_relations->get_display_links('parents', $this->print_mode, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields);
		}

		if($pmb_show_notice_id || $pmb_show_permalink) $this->isbd .= "<br />";
		if($pmb_show_notice_id){
    	   	$prefixe = explode(",",$pmb_show_notice_id);
			$this->isbd .= "<b>".$msg['notice_id_libelle']."&nbsp;</b>".(isset($prefixe[1]) ? $prefixe[1] : '').$this->notice_id."<br />";
		}
		// Permalink OPAC
		if ($pmb_show_permalink) {
				$this->isbd .= "<b>".$msg["notice_permalink_opac"]."&nbsp;</b><a href='".$pmb_opac_url."index.php?lvl=notice_display&id=".$this->notice_id."' target=\"_blank\">".$pmb_opac_url."index.php?lvl=notice_display&id=".$this->notice_id."</a><br />";
		}
		// fin du niveau 1
		if($this->level == 1) {
			if ($this->show_explnum) {
				$this->isbd .= '<div id="expl_area_' . $this->notice_id . '">'; 
				$explnum = show_explnum_per_notice($this->notice_id, 0, $this->lien_explnum);
				if ($explnum) $this->isbd .= "<br /><b>$msg[explnum_docs_associes]</b><br />".$explnum ;
				if ($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2' && (SESSrights & CATALOGAGE_AUTH) && $this->bouton_explnum) $this->isbd .= "<br /><input type='button' class='bouton' value=' $msg[explnum_ajouter_doc] ' onClick=\"document.location='".$base_path."/catalog.php?categ=serials&analysis_id=$this->notice_id&sub=analysis&action=explnum_form&bul_id=$this->bul_id'\">" ;
				if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate){
					$this->isbd .= "<input type='button' class='bouton' value='".$msg["scan_request_record_button"]."' onclick='document.location=\"./circ.php?categ=scan_request&sub=request&action=edit&from_record=".$this->notice_id."\"' />";
				}
				$this->isbd .= '</div>'; 
			}
			return;
		}
		// début du niveau 2

		// map
		if($pmb_map_activate && $this->show_map){
			$this->isbd.=$this->map->get_map();
		}
		// note générale
		if($this->notice->n_gen) {
			$this->isbd .= "<br /><b>$msg[265]</b>:&nbsp;".nl2br(htmlentities($this->notice->n_gen,ENT_QUOTES, $charset));
		}
		// note de contenu : non-applicable aux périodiques ??? Ha bon pourquoi ?
		if($this->notice->n_contenu) {
			$this->isbd .= "<br /><b>$msg[266]</b>:&nbsp;".nl2br($this->notice->n_contenu);
		}
		// résumé
		if($this->notice->n_resume) {
			$this->isbd .= "<br /><b>$msg[267]</b>:&nbsp;".nl2br($this->notice->n_resume);
		}

		// fin du niveau 2
		if($this->level == 2)
			return;

		// début du niveau 3
		// fin du niveau 3
		if($this->level == 3)
			return;

		// début du niveau 4
		// catégories
		$categ_repetables = array() ;
		if ($this->context_dsi_id_bannette) {
			$categories_show_only_last = $opac_categories_show_only_last;
		} else {
			$categories_show_only_last = $thesaurus_categories_show_only_last;
		}
		if(!count($categories_top)) {
			$q = "select id_noeud from noeuds where autorite='TOP' ";
			$r = pmb_mysql_query($q, $dbh);
			while($res = pmb_mysql_fetch_object($r)) {
				$categories_top[]=$res->id_noeud;
			}
		}
		$requete = "select * from (
			select libelle_thesaurus, if (catlg.num_noeud is null, catdef.libelle_categorie, catlg.libelle_categorie ) as categ_libelle, noeuds.id_noeud , noeuds.num_parent, langue_defaut,id_thesaurus, if(catdef.langue = '".$lang."',2, if(catdef.langue= thesaurus.langue_defaut ,1,0)) as p, ordre_vedette, ordre_categorie
			FROM ((noeuds
			join thesaurus ON thesaurus.id_thesaurus = noeuds.num_thesaurus
			left join categories as catdef on noeuds.id_noeud=catdef.num_noeud and catdef.langue = thesaurus.langue_defaut
			left join categories as catlg on catdef.num_noeud = catlg.num_noeud and catlg.langue = '".$lang."'))
			,notices_categories
			where notices_categories.num_noeud=noeuds.id_noeud and
			notices_categories.notcateg_notice=".$this->notice_id."	order by id_thesaurus, noeuds.id_noeud, p desc
			) as list_categ group by id_noeud";
		if ($thesaurus_categories_affichage_ordre==1) $requete .= " order by ordre_vedette, ordre_categorie";

		$result_categ=@pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result_categ)) {
			$anti_recurse=array();
			while($res_categ = pmb_mysql_fetch_object($result_categ)) {
				$libelle_thesaurus=$res_categ->libelle_thesaurus;
				$categ_id=$res_categ->id_noeud 	;
				$libelle_categ=$res_categ->categ_libelle ;
				$num_parent=$res_categ->num_parent ;
				$langue_defaut=$res_categ->langue_defaut ;
				$categ_head=0;
				if(in_array($num_parent,$categories_top)) $categ_head=1;

				if ($categories_show_only_last || $categ_head) {
					if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
					else $url_base_lien_aut=$base_path."/autorites.php?categ=see&sub=category&id=";
					if ( (SESSrights & AUTORITES_AUTH || $use_opac_url_base) && (!$this->print_mode) ) $libelle_aff_complet = "<a href='".$url_base_lien_aut.$categ_id."' class='lien_gestion'>".$libelle_categ."</a>";
					else $libelle_aff_complet =$libelle_categ;
					if ($thesaurus_mode_pmb) {
						$categ_repetables[$libelle_thesaurus][] = $libelle_aff_complet;
					} else $categ_repetables['MONOTHESAURUS'][] = $libelle_aff_complet;

				} else {
					if(!isset($categories_memo[$categ_id])) {
						$anti_recurse[$categ_id]=1;
						$path_table='';
						$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle, num_renvoi_voir as categ_see, note_application as categ_comment, if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
							FROM noeuds, categories where id_noeud ='".$num_parent."'
							AND noeuds.id_noeud = categories.num_noeud
							order by p desc limit 1";

						$result=@pmb_mysql_query($requete);
						if (pmb_mysql_num_rows($result)) {
							$parent = pmb_mysql_fetch_object($result);
							$anti_recurse[$parent->categ_id]=1;
							$path_table[] = array(
										'id' => $parent->categ_id,
										'libelle' => $parent->categ_libelle);

							// on remonte les ascendants
							if(!isset($parent->categ_parent)) $parent->categ_parent = 0;
							if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
							while (($parent->categ_parent)&&(!$anti_recurse[$parent->categ_parent])) {
								$requete = "select id_noeud as categ_id, num_noeud, num_parent as categ_parent, libelle_categorie as categ_libelle,	num_renvoi_voir as categ_see, note_application as categ_comment, if(langue = '".$lang."',2, if(langue= '".$langue_defaut."' ,1,0)) as p
									FROM noeuds, categories where id_noeud ='".$parent->categ_parent."'
									AND noeuds.id_noeud = categories.num_noeud
									order by p desc limit 1";
								$result=@pmb_mysql_query($requete);
								if (pmb_mysql_num_rows($result)) {
									$parent = pmb_mysql_fetch_object($result);
									$anti_recurse[$parent->categ_id]=1;
									$path_table[] = array(
												'id' => $parent->categ_id,
												'libelle' => $parent->categ_libelle);
									if(!isset($parent->categ_parent)) $parent->categ_parent = 0;
									if(!isset($anti_recurse[$parent->categ_parent])) $anti_recurse[$parent->categ_parent] = 0;
								} else {
									break;
								}
							}
						 $anti_recurse=array();
						} else $path_table=array();
						// ceci remet le tableau dans l'ordre général->particulier
						$path_table = array_reverse($path_table);
						if(sizeof($path_table)) {
							$temp_table='';
							while(list($xi, $l) = each($path_table)) {
								$temp_table[] = $l['libelle'];
							}
							$parent_libelle = join(':', $temp_table);
							$catalog_form = $parent_libelle.':'.$libelle_categ;
						} else {
							$catalog_form = $libelle_categ;
						}

						if ($use_opac_url_base) $url_base_lien_aut = $opac_url_base."index.php?&lvl=categ_see&id=" ;
						else $url_base_lien_aut=$base_path."/autorites.php?categ=see&sub=category&id=";
						if ((SESSrights & AUTORITES_AUTH || $use_opac_url_base) && (!$this->print_mode) ) $libelle_aff_complet = "<a href='".$url_base_lien_aut.$categ_id."' class='lien_gestion'>".$catalog_form."</a>";
						else $libelle_aff_complet =$catalog_form;
						if ($thesaurus_mode_pmb) {
							$categ_repetables[$libelle_thesaurus][] = $libelle_aff_complet;
						} else $categ_repetables['MONOTHESAURUS'][] = $libelle_aff_complet;

						$categories_memo[$categ_id]=$libelle_aff_complet;
						$libelle_thesaurus_memo[$categ_id]=$libelle_thesaurus;

					} else {
						if ($thesaurus_mode_pmb) $categ_repetables[$libelle_thesaurus_memo[$categ_id]][] =$categories_memo[$categ_id];
						else $categ_repetables['MONOTHESAURUS'][] =$categories_memo[$categ_id] ;
					}
				}
			}
		}

		$tmpcateg_aff = '';
		while (list($nom_thesaurus, $val_lib)=each($categ_repetables)) {
			//c'est un tri par libellé qui est demandé
			if ($thesaurus_categories_affichage_ordre==0){
				$tmp=array();
				foreach ( $val_lib as $key => $value ) {
					$tmp[$key]=strip_tags($value);
				}
				$tmp=array_map("convert_diacrit",$tmp);//On enlève les accents
				$tmp=array_map("strtoupper",$tmp);//On met en majuscule
				asort($tmp);//Tri sur les valeurs en majuscule sans accent
				foreach ( $tmp as $key => $value ) {
					$tmp[$key]=$val_lib[$key];//On reprend les bons couples clé / libellé
				}
				$val_lib=$tmp;
			}

			if ($thesaurus_mode_pmb) {
				if (!$thesaurus_categories_categ_in_line) $categ_repetables_aff = "[".$nom_thesaurus."] ".implode("<br />[".$nom_thesaurus."] ",$val_lib) ;
				else $categ_repetables_aff = "<b>".$nom_thesaurus."</b><br />".implode(" $pmb_keyword_sep ",$val_lib) ;
			} else if (!$thesaurus_categories_categ_in_line) $categ_repetables_aff = implode("<br />",$val_lib) ;
			else $categ_repetables_aff = implode(" $pmb_keyword_sep ",$val_lib) ;

			if($categ_repetables_aff) $tmpcateg_aff .= "<br />$categ_repetables_aff";
		}
		if ($tmpcateg_aff) $this->isbd .= "<br />$tmpcateg_aff";

		// Concepts
		if ($thesaurus_concepts_active == 1) {
			$index_concept = new index_concept($this->notice_id, TYPE_NOTICE);
			$this->isbd .= $index_concept->get_isbd_display();
		}

		// fin du niveau 4
		if($this->level == 4)
			return;

		// début du niveau 5
		// langues
		$langues = '';
		if(count($this->langues)) {
			$langues .= "<b>${msg[537]}</b>&nbsp;: ".construit_liste_langues($this->langues);
			}
		if(count($this->languesorg)) {
			$langues .= " <b>${msg[711]}</b>&nbsp;: ".construit_liste_langues($this->languesorg);
			}
		if($langues)
			$this->isbd .= "<br />$langues";

		// indexation libre
		if($this->notice->index_l)
			$this->isbd .= "<br /><b>${msg[324]}</b>&nbsp;: ".htmlentities($this->notice->index_l,ENT_QUOTES, $charset);

		// indexation interne
		if($this->notice->indexint) {
			$indexint = authorities_collection::get_authority(AUT_TABLE_INDEXINT, $this->notice->indexint);
			if ($this->print_mode) {
				$indexint_isbd=$indexint->display;
			} else {
				$indexint_isbd=$indexint->isbd_entry_lien_gestion;
			}
			$this->isbd .= "<br /><b>".$msg['indexint_catal_title']."</b>&nbsp;: ".$indexint_isbd;
		}

		//code (ISSN,...)
		if ($this->notice->code) $this->isbd .="<br /><b>${msg[165]}</b>&nbsp;: ".$this->notice->code;

		$tu= new tu_notice($this->notice_id);
		if(($tu_liste=$tu->get_print_type())) {
			$this->isbd .= "<br />".$tu_liste;
		}
		
		$authperso = new authperso_notice($this->notice_id);
		$this->isbd .=$authperso->get_notice_display();
		
		//Champs personalisés
		$perso_aff = "" ;
		if (!$this->p_perso->no_special_fields) {
			$perso_=$this->p_perso->show_fields($this->notice_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				// ajout de && ($p['OPAC_SHOW']||$this->show_opac_hidden_fields) afin de masquer les champs masqué de l'POAC en diff de bannette.
				if ($p["AFF"] && ($p['OPAC_SHOW'] || $this->show_opac_hidden_fields)) $perso_aff .="<br />".$p["TITRE"]." ".($p["TYPE"]=='html'?$p["AFF"]:nl2br($p["AFF"]));
			}
		}
		if ($perso_aff) $this->isbd.=$perso_aff ;

		//Notices liées
		if ((count($this->childs) || count($this->notice_relations->get_nb_pairs()))&&(!$this->no_link)) {
			if(!$load_tablist_js) $this->isbd.="<script type='text/javascript' src='".$base_path."/javascript/tablist.js'></script>\n";
			$load_tablist_js=1;
			$this->isbd.="<br />";
			$anti_loop=$this->anti_loop;
			$anti_loop[]=$this->notice_id;
			//Notices horizontales liées
			if(count($this->notice_relations->get_nb_pairs())) {
				$this->isbd .= $this->notice_relations->get_display_links('pairs', $this->print_mode, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields, $anti_loop);
			}
			if(count($this->childs) && !$this->print_mode) {
				$this->isbd .= $this->notice_relations->get_display_links('childs', $this->print_mode, $this->show_explnum, $this->show_statut, $this->show_opac_hidden_fields, $anti_loop);
			}
		}

		thumbnail::do_image($this->isbd, $this->notice);
		$this->isbd .= "<!-- !!avis_notice!! -->";
		// map
		if($pmb_map_activate && $this->show_map){
			$this->isbd.=map_locations_controler::get_map_location($this->notice_id);
		}

		//Documents numériques
		$this->isbd.= '<div id="expl_area_' . $this->notice_id . '">';			
		$boutons='';
		if ($this->show_explnum) {
			$explnum = show_explnum_per_notice($this->notice_id, 0, $this->lien_explnum,array(),false,$this->context_dsi_id_bannette);
			if ($explnum) {
				$this->isbd.= "<br /><b>$msg[explnum_docs_associes]</b> (".show_explnum_per_notice($this->notice->notice_id, 0, $this->lien_explnum,array(),true).")<br />".$explnum ;
			}			
			if ($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == '2' && (SESSrights & CATALOGAGE_AUTH) && $this->bouton_explnum) {
				$boutons.= "<br /><input type='button' class='bouton' value=' $msg[explnum_ajouter_doc] ' onClick=\"document.location='".$base_path."/catalog.php?categ=serials&analysis_id=$this->notice_id&sub=analysis&action=explnum_form&bul_id=$this->bul_id'\">" ;
			}
			if((SESSrights & CIRCULATION_AUTH) && $pmb_scan_request_activate){
				$boutons .= "<input type='button' class='bouton' value='".$msg["scan_request_record_button"]."' onclick='document.location=\"./circ.php?categ=scan_request&sub=request&action=edit&from_record=".$this->notice_id."\"' />";
			}
		}
		if ($pmb_use_uniform_title) {
			$boutons.= form_mapper::get_action_button('notice', $this->notice_id);
		}
		$this->isbd.= $boutons;
		$this->isbd.= '</div>'; // end of <div id="expl_area_' . $this->notice_id . '">

		$event = new event_record('record', 'display');
		$event->set_record_id($this->notice_id);
		$event_handler = events_handler::get_instance();
		$event_handler->send($event);
		if ($event->get_result()) {
			$this->isbd .= $event->get_result();
		}
		
		// fin du niveau 5
		if($this->level == 5)
			return;

		// début du niveau 6
		if($this->notice->niveau_biblio=="s") {
			// Si notice-mère alors on compte le nombre de numéros (bulletins)
			$this->isbd.=$this->get_etat_periodique();
			$this->isbd.=$this->print_etat_periodique();
			//état des collections
			$collstate = new collstate(0,$this->notice_id);
			//$this->isbd.= $collstate->get_callstate_isbd();
			if($pmb_etat_collections_localise)
				$collstate->get_display_list("",0,0,0,1,0,true);
			else
				$collstate->get_display_list("",0,0,0,0,0,true);
			if($collstate->nbr) {
				$this->isbd .= "<br /><b>".$msg["abts_onglet_collstate"]."</b><br />";
				$this->isbd.=$collstate->liste;
			}
		}
		// fin du niveau 6
		return;


	}

	public function get_etat_periodique() {
		global $dbh;
		$bulletins=0;
		$nb_expl=0;
		$nb_notices=0;
		if($this->notice->niveau_biblio=="s") {
			$requete = "SELECT * FROM bulletins WHERE bulletin_notice=".$this->notice_id;
			$Query = pmb_mysql_query($requete, $dbh);
			$bulletins=pmb_mysql_num_rows($Query);
			while (($row = pmb_mysql_fetch_array($Query))) {
				$requete2 = "SELECT count( * )  AS nb_notices FROM  analysis WHERE analysis_bulletin =".$row['bulletin_id'];
				$Query2 = pmb_mysql_query($requete2, $dbh);
				$analysis_array=pmb_mysql_fetch_array($Query2);
				$nb_notices+=$analysis_array['nb_notices'];
				$requete3 = "SELECT count( expL_id )  AS nb_expl FROM  exemplaires WHERE expl_bulletin =".$row['bulletin_id'];
				$Query3 = pmb_mysql_query($requete3, $dbh);
				$expl_array=pmb_mysql_fetch_array($Query3);
				$nb_expl+=$expl_array['nb_expl'];
			};
			$requete="SELECT COUNT(collstate_id) FROM collections_state WHERE id_serial='".$this->notice_id."'";
			$Query=pmb_mysql_query($requete, $dbh);
			if($Query && pmb_mysql_num_rows($Query)){
				$this->serial_nb_etats_collection=pmb_mysql_result($Query,0,0);
			}
			$requete="SELECT COUNT(abt_id) FROM abts_abts WHERE num_notice='".$this->notice_id."' AND date_fin > CURDATE()";
			$Query=pmb_mysql_query($requete, $dbh);
			if($Query && pmb_mysql_num_rows($Query)){
				$this->serial_nb_abo_actif=pmb_mysql_result($Query,0,0);
			}
			$this->serial_nb_bulletins=$bulletins;
			$this->serial_nb_exemplaires=$nb_expl;
			$this->serial_nb_articles=$nb_notices;
		}
	}

	public function print_etat_periodique() {
		global $msg;
		$affichage = '';
		if($this->notice->niveau_biblio=="s") {
			// Cas général : au moins un bulletin
			if ($this->serial_nb_bulletins > 0)
				{$affichage .="<br />\n
				<b>".$msg["serial_bulletinage_etat"]."</b>
				<table border='0' class='expl-list'>
				<tr><td><strong>".$this->serial_nb_bulletins."</strong> ".$msg["serial_nb_bulletin"]."
				<strong>".$this->serial_nb_exemplaires."</strong> ".$msg["bulletin_nb_ex"]."
				<strong>".$this->serial_nb_articles."</strong> ".$msg["serial_nb_articles"]."
				</td>
				</tr></table>";

			} else { // 0 bulletin
				$affichage .="<br /><br />\n
				<b>".$msg["serial_bulletinage_etat"]."</b>
				<table border='0' class='expl-list'>
				<tr><td><strong>".$this->serial_nb_bulletins."</strong>
				".$msg["serial_nb_bulletin"]." : <strong>";
				$affichage .=$msg["bull_no_expl"];
				$affichage .="</strong></td>
				</tr></table>";
			}
		}
		return $affichage;
	}

	// génération du header
	public function do_header() {
		global $dbh, $base_path;
		global $charset,$msg;
		global $pmb_notice_reduit_format;
		global $tdoc;
		global $use_opac_url_base, $opac_url_base, $use_dsi_diff_mode;
		global $no_aff_doc_num_image;

		$type_reduit = substr($pmb_notice_reduit_format,0,1);

		//Icone type de Document
		$icon_doc = marc_list_collection::get_instance('icondoc');
		$icon = $icon_doc->table[$this->notice->niveau_biblio.$this->notice->typdoc];
		if ($icon) {
			$biblio_doc = marc_list_collection::get_instance('nivbiblio');
			$info_bulle_icon=$biblio_doc->table[$this->notice->niveau_biblio]." : ".$tdoc->table[$this->notice->typdoc];
			if ($use_opac_url_base)	$this->icondoc="<img src=\"".$opac_url_base."images/$icon\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" align='top' />";
			else $this->icondoc="<img src=\"".$base_path."/images/$icon\" alt=\"$info_bulle_icon\" title=\"$info_bulle_icon\" align='top' />";
    	}

		//Icone abonnement actif
		if($this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == 1) {
			$req = "select abt_id from abts_abts  where num_notice=".$this->notice_id." and date_fin >= CURDATE() ";
			$res = pmb_mysql_query($req);
			if (pmb_mysql_num_rows($res)) {
				$icon = "check.png";
				$info_bulle_icon_abo_actif=$msg['abonnements_actif_img_title'];
				if ($use_opac_url_base)	$this->icon_abo_actif="<img src=\"".$opac_url_base."images/$icon\" alt=\"$info_bulle_icon_abo_actif\" title=\"$info_bulle_icon_abo_actif\" align='top' />";
				else $this->icon_abo_actif="<img src=\"".$base_path."/images/$icon\" alt=\"$info_bulle_icon_abo_actif\" title=\"$info_bulle_icon_abo_actif\" align='top' />";
			} else {
				$icon = "spacer.gif";
				$this->icon_abo_actif="<img src=\"".$base_path."/images/$icon\" width=\"10\" height=\"10\" />";
			}
		}
		

    	//Icone nouveauté
    	$icon = "icone_nouveautes.png";
    	if($this->notice->notice_is_new){
    		$info_bulle_icon_new=$msg["notice_is_new_gestion"];
    		if ($use_opac_url_base)	$this->icon_is_new="<img src=\"".$opac_url_base."images/$icon\" alt=\"$info_bulle_icon_new\" title=\"$info_bulle_icon_new\" align='top' />";
    		else $this->icon_is_new="<img src=\"".$base_path."/images/$icon\" alt=\"$info_bulle_icon_new\" title=\"$info_bulle_icon_new\" align='top' />";
    	}

		if ($this->notice->statut) {
			$rqt_st = "SELECT class_html , gestion_libelle FROM notice_statut WHERE id_notice_statut='".$this->notice->statut."' ";
			$res_st = pmb_mysql_query($rqt_st, $dbh);
			$class_html = " class='".pmb_mysql_result($res_st, 0, 0)."' ";
			if ($this->notice->statut>1) $txt = pmb_mysql_result($res_st, 0, 1) ;
			else $txt = "" ;
		} else {
			$class_html = " class='statutnot1' " ;
			$txt = "" ;
		}
		if ($this->notice->commentaire_gestion) {
			if ($txt) $txt .= ":\r\n".$this->notice->commentaire_gestion ;
			else $txt = $this->notice->commentaire_gestion ;
		}
		if ($txt) {
			$statut = "<small><span $class_html style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$this->notice_id."'); z.style.display='none'; \"><img src='".$base_path."/images/spacer.gif' width='10' height='10' /></a></span></small>";
			$statut .= "<div id='zoom_statut".$this->notice_id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($txt,ENT_QUOTES, $charset))."</b></div>" ;
		} else {
			$statut = "<small><span $class_html style='margin-right: 3px;'><img src='".$base_path."/images/spacer.gif' width='10' height='10' /></span></small>";
		}
		$this->aff_statut = $statut ;

		if ($type_reduit=="H"){
			$id_tpl=substr($pmb_notice_reduit_format,2);
			if($id_tpl){
				$tpl = notice_tpl_gen::get_instance($id_tpl);
				$notice_tpl_header=$tpl->build_notice($this->notice_id);
				if($notice_tpl_header){
					$this->header=$notice_tpl_header;
				}
			}
		}
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			// peut-être veut-on des personnalisés ?
			$perso_voulus_temp = substr($pmb_notice_reduit_format,2) ;
			if ($perso_voulus_temp!="") $perso_voulus = explode(",",$perso_voulus_temp);
		}

		if ($type_reduit=="E") {
			// zone de l'éditeur
			if ($this->notice->ed1_id) {
				$editeur = new editeur($this->notice->ed1_id);
				$editeur_reduit = $editeur->display ;
				if ($this->notice->year) $editeur_reduit .= " (".$this->notice->year.")";
			} elseif ($this->notice->year) {
				// année mais pas d'éditeur et si pas un article
				if($this->notice->niveau_biblio != 'a' && $this->notice->niveau_hierar != 2) 	$editeur_reduit = $this->notice->year." ";
			}
		} else $editeur_reduit = "" ;

		$perso_voulu_aff = "";
		if ($type_reduit=="E" || $type_reduit=="P" ) {
			if (!is_object($this->p_perso)) $this->p_perso = new parametres_perso("notices");
			//Champs personalisés à ajouter au réduit
			if (!$this->p_perso->no_special_fields) {
				if (count($perso_voulus)) {
					$this->p_perso->get_values($this->notice_id) ;
					for ($i=0; $i<count($perso_voulus); $i++) {
						$perso_voulu_aff .= $this->p_perso->get_formatted_output($this->p_perso->values[$perso_voulus[$i]],$perso_voulus[$i])." " ;
					}
					$perso_voulu_aff=trim($perso_voulu_aff);
				}
			}
		}

		if ($type_reduit!="H"){
			$this->header = htmlentities($this->notice->tit1,ENT_QUOTES, $charset);
			$this->header_texte = $this->notice->tit1;
			$this->memo_titre=$this->notice->tit1;
			$this->memo_complement_titre=$this->notice->tit4;
			$this->memo_titre_parallele=$this->notice->tit3;
		}

		if ($type_reduit=='4') {
			if ($this->memo_titre_parallele != "") {
				$this->header .= "&nbsp;=&nbsp;".$this->memo_titre_parallele;
				$this->header_texte .= ' = '.$this->memo_titre_parallele;
			}
		}

// 		if ((floor($type_reduit/10) == 1)&&($this->memo_complement_titre)) {
// 			$this->header.="&nbsp;:&nbsp;".htmlentities($this->memo_complement_titre,ENT_QUOTES,$charset);
// 		}

		if ($type_reduit=="T" && $this->memo_complement_titre) {
			$this->header.="&nbsp;:&nbsp;".htmlentities($this->memo_complement_titre,ENT_QUOTES,$charset);
			$this->header_texte.=" : ".$this->memo_complement_titre;
		}

		if (($type_reduit!='3') && ($type_reduit!='H')) {		
			if($auteurs_header = gen_authors_header($this->responsabilites)) {
				$this->header .= ' / '.$auteurs_header;
				$this->header_texte .= ' / '.$auteurs_header;
			}
		}

		if ($editeur_reduit) {
			$this->header .= ' / '. $editeur_reduit ;
			$this->header_texte .= ' / '. $editeur_reduit ;
		}
		if ($perso_voulu_aff) {
			$this->header .= ' / '. $perso_voulu_aff ;
			$this->header_texte .= ' / '. $perso_voulu_aff ;
		}

		switch ($type_reduit) {
			case "1":
				if ($this->notice->year != '') {
					$this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					$this->header_texte.=' ('.$this->notice->year.')';
				}
				break;
			case "2":
				if ($this->notice->year != '') {
					$this->header.=' ('.htmlentities($this->notice->year, ENT_QUOTES, $charset).')';
					$this->header_texte.=' ('.$this->notice->year.')';
				}
				if ($this->notice->code != '') {
					$this->header.=' / '.htmlentities($this->notice->code, ENT_QUOTES, $charset);
					$this->header_texte.=' / '.$this->notice->code;
				}
				break;
			default :
				break;
		}

		if (!$this->print_mode) {
			if($this->notice->niveau_biblio == 's' && $this->notice->niveau_hierar == 1) {
				if($this->action_serial)
					$this->header = "<a href=\"".$this->action_serial."\">".$this->header.'</a>';
			}
			if($this->notice->niveau_biblio == 'a' && $this->notice->niveau_hierar == 2) {
				if($this->action_analysis)
					$this->header= "<a href=\"".$this->action_analysis."\">".$this->header.'</a>';
				if ($this->level!=2)
					$this->header=$this->header." <i>in ".$this->parent_title." (".$this->parent_numero." ".($this->parent_date?$this->parent_date:$this->parent_aff_date_date).")</i> ";
			}
		}
		if (isset($this->icon_is_new)) $this->header = $this->header." ".$this->icon_is_new;
		
		if($this->notice->lien) {
			// ajout du lien pour les ressources électroniques
			if (!$this->print_mode || $this->print_mode=='2' || $use_dsi_diff_mode){
				$this->header .= "<a href=\"".$this->notice->lien."\" target=\"_blank\">";
				if (!$use_opac_url_base) $this->header .= "<img src=\"".$base_path."/images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
				else $this->header .= "<img src=\"".$opac_url_base."images/globe.gif\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->header .= " alt=\"";
				$this->header .= $this->notice->eformat;
				$this->header .= "\" title=\"";
				$this->header .= $this->notice->eformat;
				$this->header .= "\">";
				$this->header .= "</a>";
			}  elseif ($this->print_mode=='4') {
				$this->header .= '<br />';
				$this->header .= "<a href=\"".$this->notice->lien."\" target=\"_blank\">";
				$this->header .= '<font size="-1">'.$this->notice->lien.'</font>';
				$this->header .='</a>';
			}else {
				$this->header .= "<br />";
				$this->header .= '<font size="-1">'.$this->notice->lien.'</font>';
			}
		}
		if (!$this->print_mode || $this->print_mode=='2' && !$no_aff_doc_num_image) {
			$sql_explnum = "SELECT explnum_id, explnum_nom FROM explnum WHERE explnum_notice = ".$this->notice_id;
			$explnums = pmb_mysql_query($sql_explnum);
			$explnumscount = pmb_mysql_num_rows($explnums);
			if ($explnumscount == 1) {
				$explnumrow = pmb_mysql_fetch_object($explnums);
				if (!$use_opac_url_base) $this->header .= "<a href=\"".$base_path."/doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"_blank\">";
				else $this->header .= "<a href=\"".$opac_url_base."doc_num.php?explnum_id=".$explnumrow->explnum_id."\" target=\"_blank\">";
				if (!$use_opac_url_base) $this->header .= "<img src=\"".$base_path."/images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				else $this->header .= "<img src=\"".$opac_url_base."images/globe_orange.png\" border=\"0\" align=\"middle\" hspace=\"3\"";
				$this->header .= " alt=\"";
				$this->header .= htmlentities($explnumrow->explnum_nom,ENT_QUOTES,$charset);
				$this->header .= "\" title=\"";
				$this->header .= htmlentities($explnumrow->explnum_nom,ENT_QUOTES,$charset);
				$this->header .= "\">";
				$this->header .='</a>';
			}
			else if ($explnumscount > 1 ) {
				if (!$use_opac_url_base) $this->header .= "<img src=\"".$base_path."/images/globe_rouge.png\" border=\"0\" align=\"middle\" alt=\"".$msg['info_docs_num_notice']."\" title=\"".$msg['info_docs_num_notice']."\" hspace=\"3\">";
				else $this->header .= "<img src=\"".$opac_url_base."images/globe_rouge.png\" border=\"0\" align=\"middle\" alt=\"".$msg['info_docs_num_notice']."\" title=\"".$msg['info_docs_num_notice']."\" hspace=\"3\">";
			}
			if (($this->drag) && (!$this->print_mode)) $this->header.="<span onMouseOver='if(init_drag) init_drag();' id=\"NOTI_drag_".$this->notice_id."\" dragicon=\"".$base_path."/images/icone_drag_notice.png\" dragtext=\"".htmlentities($this->notice->tit1,ENT_QUOTES, $charset)."\" draggable=\"yes\" dragtype=\"notice\" callback_before=\"show_carts\" callback_after=\"\" style=\"padding-left:7px\"><img src=\"".$base_path."/images/notice_drag.png\"/></span>";
		}
		if (isset($this->icondoc)) $this->header = $this->icondoc." ".$this->header;
		if ($this->show_abo_actif && isset($this->icon_abo_actif)) $this->header = $this->icon_abo_actif." ".$this->header;
		if ($this->show_statut) $this->header = $this->aff_statut." ".$this->header ;
	}

	// récupération des valeurs en table
	public function serial_display_fetch_data() {
		$requete = "SELECT * FROM notices WHERE notice_id='".$this->notice_id."' ";
		$myQuery = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($myQuery)){
			$this->notice = pmb_mysql_fetch_object($myQuery);
		}
		return pmb_mysql_num_rows($myQuery);
	}

} // fin classe serial_display

// -------------------------------------------------------------------
//   classe bulletinage_display : utilisée pour le prêt de documents
// -------------------------------------------------------------------
class bulletinage_display {
	public $bul_id = 0;		// id du bulletinage à afficher
	public $display = '';		// texte à afficher
	public $parent_title = '';		// titre général de la revue à laquelle fait référence ce bulletinage
	public $bulletin_titre = '';	// titre de ce bulletin
	public $numerotation = '';		// mention de numérotation sur la revue
	public $periode	  = '';		// mention de date de la revue (txt)
	public $date_date	  = '';		// mention de date de la revue (date)
	public $header	  = '';		// pour affichage réduit

	// constructeur
	public function __construct($id=0) {
		if(!$id) {
			$this->display = "Error : bul_id is null";
			return $this->bul_id;
		}

		$this->bul_id = $id;

		$this->fetch_bulletinage_data();
		$this->make_display();

		return $this->bul_id;
	}

	// fabrication de la mention à afficher
	public function make_display() {
		if ($this->parent_title) {
			$this->display = $this->parent_title;
			} else {
				$this->display = "error: unknown record";
				return;
				}

		if((!$this->numerotation && !$this->periode && !$this->bulletin_titre && !$this->date_date) || !$this->bul_id) {
			$this->display .= " error : missing information";
			}

		if($this->numerotation)
			$this->display .= '. '.$this->numerotation;

		$this->header = $this->display;

		// affichage de la mention de date utile : mention_date si existe, sinon date_date
		if ($this->periode) {
			$date_affichee = " (".$this->periode.") ";
		} else {
			$date_affichee = " [".$this->aff_date_date."]";
		}
		$this->display .= $date_affichee;

		if ($date_affichee) $this->header .= $date_affichee ;
		}

	// récupération des infos bulletinage en base
	public function fetch_bulletinage_data() {
		global $msg, $dbh;

		$requete = "SELECT bulletins.*, notices.tit1, date_format(date_date, '".$msg["format_date"]."') as aff_date_date FROM bulletins, notices ";
		$requete .= " WHERE bulletins.bulletin_id=".$this->bul_id;
		$requete .= " AND notices.notice_id=bulletins.bulletin_notice";
		$requete .= " AND notices.niveau_biblio='s' AND notices.niveau_hierar='1' LIMIT 1";

		$myQuery = pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($myQuery)) {
			$result = pmb_mysql_fetch_object($myQuery);
			$this->parent_title = $result->tit1;
			$this->bulletin_titre = $result->bulletin_titre;
			$this->numerotation = $result->bulletin_numero;
			$this->periode = $result->mention_date;
			$this->date_date = $result->date_date;
			$this->aff_date_date = $result->aff_date_date;
			$this->bul_id = $result->bulletin_id;
		}

		return;
	}
} // class serial_display

