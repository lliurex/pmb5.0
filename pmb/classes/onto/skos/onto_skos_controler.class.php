<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_controler.class.php,v 1.51.2.10 2018-01-24 15:26:28 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/authority.class.php");
require_once($class_path."/aut_pperso.class.php");
require_once($class_path."/aut_link.class.php");
require_once($class_path."/audit.class.php");

class onto_skos_controler extends onto_common_controler {
	
	/** L'uri des schema **/
	protected static $concept_scheme_uri='http://www.w3.org/2004/02/skos/core#ConceptScheme';
	protected static $concept_uri='http://www.w3.org/2004/02/skos/core#Concept';
	
	protected static $onto_index;
	

	/**
	 * Gère la variable session breadcrumb qui garde les id présents dans la navigation
	 * permet la construction du fil de navigation dans le thésaurus
	 * renvoie un tableau des id de parents parcouru
	 *
	 * @param onto_handler $handler
	 * @param onto_param $params
	 * @param bool $reset
	 *
	 * @return array breadcrumb
	 */
	public function handle_breadcrumb($reset=false){
		if(!isset($_SESSION['breadcrumb']) || !$_SESSION['breadcrumb'] || $reset){
			$_SESSION['breadcrumb']='';
		}
	
		//on enregistre le fil d'ariane
		if($this->params->parent_id && !preg_match('/\-'.$this->params->parent_id.'\-/',$_SESSION['breadcrumb'])){
			$_SESSION['breadcrumb'].='-'.$this->params->parent_id.'-';
		}elseif($this->params->parent_id && !preg_match('/\-'.$this->params->parent_id.'\-$/',$_SESSION['breadcrumb'])){
			$_SESSION['breadcrumb']=substr($_SESSION['breadcrumb'],0, strpos($_SESSION['breadcrumb'], '-'.$this->params->parent_id.'-')+strlen('-'.$this->params->parent_id.'-'));
		}elseif(!$this->params->parent_id){
			$_SESSION['breadcrumb']='';
			return $_SESSION['breadcrumb'];
		}
		$breadcrumb=explode('--',$_SESSION['breadcrumb']);
		foreach($breadcrumb as $key=>$parent_id){
			$breadcrumb[$key]=str_replace('-', '', $parent_id);
		}
	
		return $breadcrumb;
	}
	
	/**
	 * renvoie la liste des schema
	 * 
	 * @return array
	 */
	public function get_scheme_list(){
		$params=new onto_param();
		$params->page = 1;
		$params->nb_per_page = 0;
		$params->action = "list";
		return $this->get_list(self::$concept_scheme_uri,$params);	
	}
	
	public function get_list($class_uri,$params){
		global $lang;
		switch($class_uri){
			case self::$concept_uri :
				return $this->get_hierarchized_list($class_uri,$params);
				break;
			default :
				return parent::get_list($class_uri,$params);
				break;
		}
	}
	
	/**
	 * renvoie le nombre d'enfants d'un noeud.
	 * 
	 * @param string $class_uri
	 * @param onto_param $params
	 * 
	 * @return int
	 */
	public function has_narrower($class_uri,$params){
		$in_scheme = "";
		
		if (($params->concept_scheme != -1) && ($params->concept_scheme != 0)) {
			// On est dans un schéma en particulier
			$in_scheme = " .
			?child <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">";
		}
		$query = "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?child".$in_scheme."
		} limit 1 offset 0";
		$this->handler->data_query($query);
		return $this->handler->data_num_rows();
	}
	
	/**
	 * renvoie le nombre de parents d'un noeud. 
	 *
	 * @param string $class_uri
	 * @param onto_param $params
	 * 
	 * * @return int
	 */
	public function has_broader($class_uri,$params){
		$query = "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
			?parent <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">
		} limit 1 offset 0";
		$this->handler->data_query($query);
		return $this->handler->data_num_rows();
	}
	
	/**
	 * renvoie les parents d'un noeud
	 * 
	 * @param string $class_uri
	 * @param onto_param $params
	 * @return array
	 */
	public function get_broaders($class_uri,$params){
		$query .= "select * where {
			<".$class_uri."> <http://www.w3.org/2004/02/skos/core#broader> ?parent .
			?parent <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">
		}";
		$this->handler->data_query($query);
		$results=$this->handler->data_result();
		
		if(sizeof($results)){
			$return=array();
			foreach ($results as $key=>$result){
				$return[$key]["id"]=onto_common_uri::get_id($result->parent);
				$return[$key]["label"] = $this->get_data_label($result->parent);
			}
			return $return;
		}
		return array();
 	}
	
	/**
	 * Retourne une liste hierarchisée
	 * 
	 * @param string $class_uri
	 * @param onto_param $params
	 * @return array
	 */
	public function get_hierarchized_list($class_uri,$params,$user_query_var='deb_rech'){
		global $lang;
	    global $authority_statut;
	    global $pmb_allow_authorities_first_page;
	    
		$page=$params->page-1;
		$displayLabel=$this->handler->get_display_label(self::$concept_uri);
		
		$filter = "";
		$query = "select ?elem ?label where {
			?elem rdf:type <".self::$concept_uri."> .
			?elem <".$displayLabel."> ?label ";
		$counted = false;
		
		if($pmb_allow_authorities_first_page == 0 && !$params->parent_id && ($params->action == 'list_selector' && !$params->{$user_query_var})){
			$list = array(
					'nb_total_elements' => 	0,
					'nb_onto_element_per_page' => $params->nb_per_page,
					'page' => 0
			);
			$list['elements']=array();
		}else{
			if(!$params->parent_id){
				$more = "";
				//retourne les top concepts
				if($params->only_top_concepts){
					if($params->concept_scheme == 0) {
						$more.= " . ?elem pmb:showInTop owl:Nothing";
						$count_query = "select count(?elem) as ?nb where{ ?elem pmb:showInTop owl:Nothing }";
						$this->handler->data_query($count_query);
						if($this->handler->data_num_rows()){
							$counted = true;
							$result = $this->handler->data_result();
							$nb_elements = $result[0]->nb;
						}
					}else if ($params->concept_scheme != -1) {
						$more.= " .	?elem skos:topConceptOf <".onto_common_uri::get_uri($params->concept_scheme).">";
						$count_query = "select count(?elem) as ?nb where{ ?elem skos:topConceptOf <".onto_common_uri::get_uri($params->concept_scheme)."> }";
						$this->handler->data_query($count_query);
						if($this->handler->data_num_rows()){
							$counted = true;
							$result = $this->handler->data_result();
							$nb_elements = $result[0]->nb;
						}
					}else {
						$more.= " .	?elem skos:topConceptOf ?top";
					}
				}else{
					if ($params->concept_scheme == 0) {
						/*
						 * 
						 * TODO : HACK à reprendre un jour
						 * 
						 */
						$more.= " . ?elem pmb:showInTop owl:Nothing";
					} else {
						// Sinon on affiche les top concepts de tous les schémas y compris sans schema
						/*
						 * 
						 * TODO : HACK à reprendre un jour
						 * 
						 */if ($params->concept_scheme != -1) {
							$more.= " . optional { ?elem pmb:showInTop ?scheme";
							// On n'affiche qu'un schéma
							$filter .= " (?scheme = <".onto_common_uri::get_uri($params->concept_scheme).">)";
							$more.= " }";
						}
						if($filter){
							$more.= " filter (".$filter.")
							";
						}
					}
				}
				$query.=$more;
				$nb_elements=$this->handler->get_nb_elements(self::$concept_uri,$more);
			}else{
					//retourne les enfants du parent
					$more = "
						. ?elem <http://www.w3.org/2004/02/skos/core#broader> <".onto_common_uri::get_uri($params->parent_id).">";
		
					if ($params->concept_scheme == 0) {
						// On affiche les concepts qui n'ont pas de schéma
						$more.= " .
							optional {
								?elem <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
							}
							filter (!bound(?scheme))
							";
					} else if ($params->concept_scheme != -1) {
						// On n'affiche qu'un schéma
						$more.= " .
							?elem <http://www.w3.org/2004/02/skos/core#inScheme> <".onto_common_uri::get_uri($params->concept_scheme).">  
							";
					
					}
					$query.=$more;
					$nb_elements=$this->handler->get_nb_elements(self::$concept_uri,$more);
			}
			
			$query.= " } group by ?elem order by ?label limit ".$params->nb_per_page;
			$query.= " offset ".($page*$params->nb_per_page);
			$this->handler->data_query($query);
			$results=$this->handler->data_result();
	
			$list = array(
					'nb_total_elements' => $nb_elements,
					'nb_onto_element_per_page' => $params->nb_per_page,
					'page' => $page
			);
			$list['elements']=array();
			if($this->handler->data_num_rows()){
				foreach($results as $result){
					if(isset($result->elem) && $result->elem) {
						$skos_concept = new skos_concept(0, $result->elem);
						if(!isset($list['elements'][$result->elem]['default']) || !$list['elements'][$result->elem]['default']){
							$list['elements'][$result->elem]['default'] = $skos_concept->get_isbd();
						}
						if(isset($result->label_lang) && substr($lang,0,2) == $result->label_lang){
							$list['elements'][$result->elem][$lang] = $skos_concept->get_isbd();
						}
					}
				}
			}
		}
		return $list;
	}
	
	/**
	 * Dérivation de l'aiguilleur principal pour les ajouts d'éléments dans les sélecteurs
	 */
	public function proceed(){
		
		$this->init_item();
		switch($this->params->action){
			case "selector_add" :
				$this->proceed_selector_add();
				break;
			case "selector_save" :
				$this->proceed_selector_save();
				break;
			case "save":	
				if( get_class($this->item) == "onto_skos_concept_item"){
					$this->proceed_save(false);
					$authority_page = new skos_page_concept(onto_common_uri::get_id($this->item->get_uri()));
					$authority_page->proceed();
				}else{
					$this->proceed_save();
				}
				break;
			case "search" :
				print $this->get_menu();
				// On met à jour le dernier schéma sélectionné
				if (isset($this->params->concept_scheme) && ($this->params->concept_scheme !== "")) {
					$_SESSION['onto_skos_concept_last_concept_scheme'] = $this->params->concept_scheme;
				}	
				$_SESSION['onto_skos_concept_selector_last_parent_id'] = "";
				
				//si on peut on s'évite le processus de recherche... il est moins fluide !
				if($this->params->user_input == "*" && $this->params->concept_scheme == -1 && $this->params->authority_statut == 0){
					$this->proceed_list();
				}else{
					$this->proceed_search();
				}
				break;
			case "last" :
				print $this->get_menu();
				$_SESSION['onto_skos_concept_selector_last_parent_id'] = "";
				$this->proceed_last();
				break;
			case "replace" :
				$this->proceed_replace();
				break;
			case "duplicate":
				$this->item->set_uri(onto_common_uri::get_temp_uri($this->item->get_onto_class()->uri));
				$this->proceed_edit();
				break;
			default :
				$_SESSION['onto_skos_concept_selector_last_parent_id'] = "";
				return parent::proceed();
				break;
		}
	}
	
	protected function init_item(){
		if($this->params->action == "selector_add"){
			//dans le sélecteur, c'est forcément un nouveau...
			$this->item = $this->handler->get_item($this->get_item_type_to_list($this->params),"");
		}else if($this->params->action == "selector_save"){
			//lors d'une sauvegarde d'un item, on a posté l'uri
			$this->item = $this->handler->get_item($this->get_item_type_to_list($this->params), $this->params->item_uri);
		}else{
			//on réinvente pas la roue
			parent::init_item();
		}
	}

	protected function proceed_edit(){
		print $this->item->get_form("./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id."&parent_id=".$this->params->parent_id."&concept_scheme=".$this->params->concept_scheme);
	}
	
	protected function proceed_selector_save(){
			$this->item->get_values_from_form();
			$saved = $this->handler->save($this->item);
			$query = "select ?scheme ?broader ?broaderScheme where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .
				<".$this->item->get_uri()."> skos:inScheme ?scheme .
				optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				}
			} order by ?scheme ?broader";
			$this->handler->data_query($query);
			if($this->handler->data_num_rows()){
				$results = $this->handler->data_result();
				$lastScheme=$results[0]->scheme;
				$flag = true;
				foreach($results as $result){
					if($result->scheme == $result->broaderScheme){
						$flag = false;
					}
					if($lastScheme != $result->scheme){
						if($flag){
							$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
							$this->handler->data_query($query);
						}
						$flag = true;
						$lastScheme = $result->scheme;
					}
				}
				if($flag){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
					$this->handler->data_query($query);
				}
			}else{
				$query = "select * where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .
				optional{
				 <".$this->item->get_uri()."> skos:inScheme ?scheme .
				} . filter(!bound(?scheme)) .
				 optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				} filter (!bound(?broaderScheme))
			} ";
				$this->handler->data_query($query);
				if(!$this->handler->data_num_rows()){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop owl:Nothing}";
					$this->handler->data_query($query);
				}
			}
			
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			//ils sont nouveaux dont pas encore utilisé...pas besoin du hook pour les notices...
			if($saved !== true){
				$ui_class_name::display_errors($this,$saved);
			}else{
				//sauvegarde des autorités liées pour les concepts...
				//Ajout de la sauvegarde du statut si c'est un concept également
				if( get_class($this->item) == "onto_skos_concept_item"){
					global $authority_statut;
					$authority_statut+= 0;
				
					$concept_id = onto_common_uri::get_id($this->item->get_uri());
					$aut_link= new aut_link(AUT_TABLE_CONCEPT, $concept_id);
					$aut_link->save_form();
					
					$aut_pperso = new aut_pperso("skos", $concept_id);
					$aut_pperso->save_form();
				
					//Ajout de la référence dans la table authorities
					$authority = new authority(0, $concept_id, AUT_TABLE_CONCEPT);
					$authority->set_num_statut($authority_statut);
					$authority->update();
				}
				
	// 			$this->proceed_list();
				$this->params->action = "list_selector";
				$this->params->deb_rech = "\"".$this->item->get_label("http://www.w3.org/2004/02/skos/core#prefLabel")."\"";
	//  		$this->params->parent_id = $_SESSION['onto_skos_concept_selector_last_parent_id'];
				return parent::proceed();
			}
		}
	
	protected function proceed_selector_add(){
		//on en aura besoin à la sauvegarde...
		$_SESSION['onto_skos_concept_selector_last_parent_id'] = $this->params->parent_id;
		//réglons rapidement ce problème... cf. dette technique
 		print "<div id='att'></div>";
 		$type = $this->get_item_type_to_list($this->params,true);
		print $this->item->get_form($this->params->base_url."&range=".$this->params->range, $type."_selector_form", "selector_save");
	}
	
	/*
	 * On hook la sauvegarde pour déclencher la réindexation des éléments impactés
	 */
	protected function proceed_save($list=true){
		global $dbh, $thesaurus_concepts_autopostage;
		$this->item->get_values_from_form();
		
		//sauvegarde des autorités liées pour les concepts...
		//Ajout de la sauvegarde du statut si c'est un concept également
		if( get_class($this->item) == "onto_skos_concept_item"){
			global $authority_statut;
			$authority_statut+= 0;
			 
			$concept_id = $this->item->get_id();
			$aut_link= new aut_link(AUT_TABLE_CONCEPT, $concept_id);
			$aut_link->save_form();
		
			$aut_pperso = new aut_pperso("skos", $concept_id);
			$aut_pperso->save_form();
			 
			//Ajout de la référence dans la table authorities
			$authority = new authority(0, $concept_id, AUT_TABLE_CONCEPT);
			$authority->set_num_statut($authority_statut);
			$authority->update();
		}
			
		// Mise à jour des vedettes composées contenant cette autorité
		vedette_composee::update_vedettes_built_with_element($this->item->get_id(), "concept");
			
		//réindexation des notices indexés avec le concepts
		index_concept::update_linked_elements($this->item->get_id());
		
		if (onto_common_uri::is_temp_uri($this->item->get_uri())) {
			audit::insert_creation(AUDIT_CONCEPT, $this->item->get_id());
		} else {
			audit::insert_modif(AUDIT_CONCEPT, $this->item->get_id());
		}
		$result = $this->handler->save($this->item);
		if($result !== true){
			$ui_class_name=self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
			$ui_class_name::display_errors($this,$result);
		}else{
			//TODO: reprendre ce hack un peu crade
			//pour faciliter les requetes SPARQL en gestion, on ajoute une propriété qui sort de nulle part... pmb:showInTop si pas de parent dans le schéma
			
			$query = "select ?scheme ?broader ?broaderScheme where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .	
				<".$this->item->get_uri()."> skos:inScheme ?scheme .
				optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				}
			} order by ?scheme ?broader";
			$this->handler->data_query($query);
			if($this->handler->data_num_rows()){
				$results = $this->handler->data_result();
				$lastScheme=$results[0]->scheme;
				$flag = true;
				foreach($results as $result){
					if($result->scheme == $result->broaderScheme){
						$flag = false;
					}
					if($lastScheme != $result->scheme){
						if($flag){
							$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
							 $this->handler->data_query($query);
						}
						$flag = true;
						$lastScheme = $result->scheme;
					}
				}
				if($flag){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop <".$lastScheme.">}";
					$this->handler->data_query($query);
				}
			}else{
				$query = "select * where{
				<".$this->item->get_uri()."> rdf:type skos:Concept .	
				optional{		
				 <".$this->item->get_uri()."> skos:inScheme ?scheme .
				} . filter(!bound(?scheme)) .
				 optional {
					<".$this->item->get_uri()."> skos:broader ?broader .
					?broader skos:inScheme ?broaderScheme
				} filter (!bound(?broaderScheme))
			} ";
				$this->handler->data_query($query);
				if(!$this->handler->data_num_rows()){
					$query = "insert into <pmb> {<".$this->item->get_uri()."> pmb:showInTop owl:Nothing}";
					$this->handler->data_query($query);
				}
			}
		}
		
		if ($thesaurus_concepts_autopostage) {
			$this->save_paths($this->item->get_uri());
		}
		
		if($list){
			$this->proceed_list();
		}else{
			return $this->item->get_id();
		}
	}
	
	/*
	 * On hook la suppression pour vérifier l'utilisation au préalable
	 */
	protected function proceed_delete($force_delete = false){
		global $dbh,$msg;
		
		// On déclare un flag pour savoir si on peut continuer la suppression
		$deletion_allowed = true;

		$message  = $this->item->get_label($this->handler->get_display_label($this->handler->get_class_uri($this->params->categ)));
		
		// On regarde si le concdept est utilisé pour indexer d'autres éléments (tbl index_concept)
		$query = "select num_object from index_concept where num_concept = ".onto_common_uri::get_id($this->item->get_uri());
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$deletion_allowed = false;
			$message.= "<br/>".$msg['concept_use_cant_delete'];
		}
		
		// On regarde si l'autorité est utilisée dans des vedettes composées
		$attached_vedettes = vedette_composee::get_vedettes_built_with_element(onto_common_uri::get_id($this->item->get_uri()), "concept");
		if (count($attached_vedettes)) {
			// Cette autorité est utilisée dans des vedettes composées, impossible de la supprimer
			$deletion_allowed = false;
			$message.= "<br/>".$msg['vedette_dont_del_autority'];
		}
		
		
		if(($usage = aut_pperso::delete_pperso(AUT_TABLE_CONCEPT, $this->item->get_uri(), $force_delete))){
			// Cette autorité est utilisée dans des champs perso, impossible de supprimer
			$deletion_allowed = false;
			$message.= '<br />'.$msg['autority_delete_error'].'<br /><br />'.$usage['display'];
		}

		if ($force_delete || $deletion_allowed) {
			audit::delete_audit(AUDIT_CONCEPT, $this->item->get_id());
			// On peut continuer la suppression
			$id_vedette = vedette_link::get_vedette_id_from_object(onto_common_uri::get_id($this->item->get_uri()), TYPE_CONCEPT_PREFLABEL);
			$vedette = new vedette_composee($id_vedette);
			$vedette->delete();
			
			//suppression des autorités liées... & des statuts des concepts
			// liens entre autorités
			if( get_class($this->item) == "onto_skos_concept_item"){
			    $concept_id = onto_common_uri::get_id($this->item->get_uri());
				$aut_link= new aut_link(AUT_TABLE_CONCEPT, $concept_id);
				$aut_link->delete();
				
				$aut_pperso = new aut_pperso("skos", $concept_id);
				$aut_pperso->delete();
				
				$authority = new authority(0, $concept_id, AUT_TABLE_CONCEPT);
				$authority->delete();
			}
			parent::proceed_delete($force_delete);
		} else {
			error_message($msg[132], $message, 1, "./".$this->get_base_resource()."categ=concepts&sub=concept&action=edit&id=".onto_common_uri::get_id($this->item->get_uri()));
		}
	}
	
	/**
	 * Place un concept en tête de hiérarchie si il est dans un schéma et qu'il n'a pas de broader
	 */
	protected function define_top_concept_of() {
		$query = "select ?scheme where {
				<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme .
				optional {
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#topConceptOf> ?topscheme .
					filter (?topscheme = ?scheme)
				}
				filter (!bound(?topscheme))
				optional {
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#broader> ?broader .
					?broader <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme
				}
				filter (!bound(?broader))
			}";
		// Détails : on va chercher les schémas de l'item; pour chaque schema, on regarde si il est topconcept ou si il a un parent
		
		$this->handler->data_query($query);
		if($this->handler->data_num_rows()){
			// Le concept est dans des schémas dans lesquels il n'est pas topconcept et il n'a pas de parent
			// On le définit donc top concept de ces schémas 
			$query = "insert into <pmb> {";
			
			$results = $this->handler->data_result();
			foreach($results as $result){
				$query .= "
					<".$this->item->get_uri()."> <http://www.w3.org/2004/02/skos/core#topConceptOf> <".$result->scheme."> .
					<".$result->scheme."> <http://www.w3.org/2004/02/skos/core#hasTopConcept> <".$this->item->get_uri()."> .";
			}
			$query .= "}";
		}
		$this->handler->data_query($query);
	}

	/**
	 * renvoie les informations d'un noeud
	 *
	 * @param string $uri
	 * @return array
	 */
	public function get_informations_concept($uri){
		$query = "select ?scopeNote where {
					<".$uri."> rdf:type <".self::$concept_uri."> .
					optional {
						<".$uri."> skos:scopeNote ?scopeNote
					}
				}";
	
		$this->handler->data_query($query);
		$results=$this->handler->data_result();
		if(is_array($results) && sizeof($results)){
			$return=array();
			foreach ($results as $key=>$result){
				$return[$key]["scopeNote"]=$result->scopeNote;
			}
			return $return;
		}
		return array();
	}
	
	protected function proceed_ajax_selector(){
		//on regarde le range (multiple  ou pas..)
		$ranges = explode("|||",$this->params->att_id_filter);
		$list = array();
		foreach ($ranges as $range){
			$elements = $this->get_ajax_searched_elements($range);
			foreach($elements['elements'] as $key => $value){
				$newKey = $key;
				if($this->params->return_concept_id){
					$newKey = onto_common_uri::get_id($key);
				}
				$list['elements'][$newKey] = $value;
				if(count($ranges)>1){
					$list['prefix'][$newKey]['libelle'] = $elements['label'];
					$list['prefix'][$newKey]['id'] = $range;
				}
			}
		}
		return $list;
	}
	
	public function get_base_resource($with_params=true){
		return $this->params->base_resource.($with_params? "?" : "");
	}

	protected function proceed_last(){
		$ui_class_name = self::resolve_ui_class_name($this->params->sub,$this->handler->get_onto_name());
		print $ui_class_name::get_search_form($this,$this->params);
		print $ui_class_name::get_list($this,$this->params);
	}
	
	/**
	 *
	 * Retourne les derniers éléments créés
	 */
	public function get_last_elements(){
		global $lang;
		
		$page=$this->params->page-1;
		$query = "select SQL_CALC_FOUND_ROWS uri, id_item, value, lang from skos_fields_global_index join onto_uri on id_item = uri_id where code_champ='1' order by id_item desc ";
		if ($page > 0) {
			$query.= " limit ".($page*$this->params->nb_per_page).", ".$this->params->nb_per_page;
		} elseif ($this->params->nb_per_page > 0) {
			$query.= " limit ".$this->params->nb_per_page;
		}
	
		$res=pmb_mysql_query($query);

		$list = array(
				'nb_onto_element_per_page' => $this->params->nb_per_page,
				'page' => $page,
				'elements' => array()
		);
		if (pmb_mysql_num_rows($res)) {
			while($result=pmb_mysql_fetch_object($res)) {
				if(empty($list['elements'][$result->uri]) || !$list['elements'][$result->uri]['default']){
					$list['elements'][$result->uri]['default'] = $result->value;
				}
				if($lang == $result->lang){
					$list['elements'][$result->uri][$result->lang] = $result->value;
				}
			}
		}
		$query = 'select FOUND_ROWS()';
		$result = pmb_mysql_query($query);
		$list['nb_total_elements'] = pmb_mysql_result($result, 0, 0);
		return $list;
	}
	
	protected function proceed_replace() {
		$by = $this->params->by;
		if (!$by) {
			print $this->item->get_replace_form("./".$this->get_base_resource()."categ=".$this->params->categ."&sub=".$this->params->sub."&id=".$this->params->id."&concept_scheme=".$this->params->concept_scheme);
			return;
		}
		global $msg;
		global $dbh;
		if (!is_numeric($by)) {
			$by = onto_common_uri::get_id($by);
		}
		if (($this->item->get_id() == $by) ||(!$this->item->get_id())) {
			return $msg['223'];
		}
		
		$aut_link = new aut_link(AUT_TABLE_CONCEPT, $this->item->get_id());
		// "Conserver les liens entre autorités" est demandé
		if ($this->params->link_save) {
			// liens entre autorités
			$aut_link->add_link_to(AUT_TABLE_CONCEPT, $by);
		}
		$aut_link->delete();
			
		vedette_composee::replace(AUT_TABLE_CONCEPT, $this->item->get_id(), $by);
			
		// nettoyage d'autorities_sources
		$query = "select id_authority_source, authority_favorite from authorities_sources where num_authority = " .$this->item->get_id() ." and authority_type = 'concept'";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while ( $row = pmb_mysql_fetch_object($result) ) {
				if ($row->authority_favorite ==1) {
					// on suprime les références si l'autorité a été importée...
					$query = "delete from notices_authorities_sources where num_authority_source = " .$row->id_authority_source;
					pmb_mysql_result($query);
					$query = "delete from authorities_sources where id_authority_source = " .$row->id_authority_source;
					pmb_mysql_result($query);
				} else {
					// on fait suivre le reste
					$query = "update authorities_sources set num_authority = " .$by ." where num_authority_source = " .$row->id_authority_source;
					pmb_mysql_query($query);
				}
			}
		}
			
		//Remplacement dans les champs persos sélecteur d'autorité
		aut_pperso::replace_pperso(AUT_TABLE_CONCEPT, $this->item->get_id(), $by);
		
		// effacement de l'identifiant unique d'autorité
		$authority = new authority(0, $this->item->get_id(), AUT_TABLE_CONCEPT);
		$authority->delete();
		
		$this->proceed_delete(true);
		
				
		// Remplacement des triplets rdf
		$query = "select ?s ?p where {
				?s ?p <".$this->item->get_uri()."> .
			}";
		
		$this->handler->data_query($query);
		
		if($this->handler->data_num_rows()){
			$assertions = array();
			$results = $this->handler->data_result();			
			foreach($results as $result){
				$assertions[] = $result;
			}
			
			$query_insert = 'insert into <pmb> { ';
			foreach($assertions as $assert){
				$query_insert.= '<'.$assert['s'].'> <'.$assert['p'].'> <'.onto_common_uri::get_uri($by).'> .';
			}
			$query_insert.= '}';
			$this->handler->data_query($query_insert);
		}
		
		$onto_index = onto_index::get_instance($this->get_onto_name());
		$onto_index->set_handler($this->handler);
		$onto_index->maj($by);
		
		//Remplacement de l'identifiant du concept source dans la table index concept
		$query = "update index_concept set num_concept=".$by." where num_concept=".$this->item->get_id();
		pmb_mysql_query($query);
		
		/**
		 * Réindex des éléments à la suite du remplacement des id du concept source
		 */
		index_concept::update_linked_elements($by);
		
		// mise à jour de l'oeuvre rdf
		if ($pmb_synchro_rdf) {
			$synchro_rdf = new synchro_rdf();
			$synchro_rdf->replaceAuthority($this->item->get_id(), $by, 'auteur');
		}
	}
	
	/**
	 * Retourne le label d'un data en fonction de son uri.
	 *
	 * @param unknown_type $uri
	 */
	public function get_data_label($uri){
		if(!empty($this->params->att_id_filter) && ($this->params->att_id_filter == self::$concept_uri)){
			$skos_concept = new skos_concept(0, $uri);
			return $skos_concept->get_isbd();
		}
		return parent::get_data_label($uri);
	}
	
	/**
	 * Retourne le chemin des concepts génériques
	 * @param string $uri
	 * @param array $paths
	 * @param string $path_beginning
	 * @return array
	 */
	public function get_broad_paths($uri, $paths = array(), $path_beginning = '', $nb_levels = -1) {
		if ($uri) {
			if ($nb_levels != -1 && substr_count($path_beginning, '/') == $nb_levels) {
				return $paths;
			}
			$query = "select ?broader where {
				<".$uri."> <http://www.w3.org/2004/02/skos/core#broader> ?broader			
			}";
			
			$this->handler->data_query($query);
			$results = $this->handler->data_result();
			
			if(is_array($results) && count($results)){
				foreach ($results as $result) {
					$broader_id = onto_common_uri::get_id($result->broader);
					if (strpos($path_beginning, $broader_id.'/') === false) {
						$index = array_search($path_beginning, $paths);
						if ($index !== false) {
							$paths[$index] = $path_beginning.$broader_id.'/';
						} else {
							$paths[] = $path_beginning.$broader_id.'/';
						}
						$paths = $this->get_broad_paths($result->broader, $paths, $path_beginning.$broader_id.'/', $nb_levels);
					}					
				}
			} 
		}
		return $paths;
	}
	
	/**
	 * Retourne le chemin des concepts spécifiques
	 * @param string $uri
	 * @param array $paths
	 * @param string $path_beginning
	 * @return array
	 */
	public function get_narrow_paths($uri, $paths = array(), $path_beginning = '', $nb_levels = -1) {
		if ($uri) {
			if ($nb_levels != -1 && substr_count($path_beginning, '/') == $nb_levels) {
				return $paths;
			}
			$query = "select ?narrower where {
				<".$uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower			
			}";
			
			$this->handler->data_query($query);
			$results = $this->handler->data_result();
			
			if(is_array($results) && count($results)){
				foreach ($results as $result) {
					$narrower_id = onto_common_uri::get_id($result->narrower);
					if (strpos($path_beginning, $narrower_id.'/') === false) {
						$index = array_search($path_beginning, $paths);
						if ($index !== false) {
							$paths[$index] = $path_beginning.$narrower_id.'/';
						} else {
							$paths[] = $path_beginning.$narrower_id.'/';
						}
						$paths = $this->get_narrow_paths($result->narrower, $paths, $path_beginning.$narrower_id.'/', $nb_levels);
					}					
				}
			} 
		}
		return $paths;
	}
	
	/**
	 * Enregistrement des chemins des concepts spécifiques et génériques pour l'autopostage
	 * @param string $uri
	 */
	public function save_paths($uri) {
		$broad_paths = $this->save_broad_paths($uri);
		$narrow_paths = $this->save_narrow_paths($uri);

		$broaders_preflabels = $this->get_paths_preflabels($broad_paths);
		$narrowers_preflabels = $this->get_paths_preflabels($narrow_paths);
		
		$id_item = onto_common_uri::get_id($uri);
		$this->get_onto_index()->update_paths_index($id_item, $broaders_preflabels);
		$this->get_onto_index()->update_paths_index($id_item, $narrowers_preflabels, true);
		
		$this->update_narrowers_broad_paths($id_item, $broad_paths);
		$this->update_broaders_narrow_paths($id_item, $narrow_paths);
	}
	
	/**
	 * Enregistrement des chemins des concepts génériques
	 * @param string $uri
	 * @return array
	 */
	protected function save_broad_paths($uri) {
		global $thesaurus_concepts_autopostage_generic_levels_nb;
		
		$nb_levels = $thesaurus_concepts_autopostage_generic_levels_nb;
		if (!is_numeric($nb_levels)) {
			$nb_levels = -1;
		}
		
		$broad_paths = array();
		$broad_paths = $this->get_broad_paths($uri, array(), "", $nb_levels);
		if (count($broad_paths)) {
			foreach ($broad_paths as $broad_path) {
				$formated_path = $this->format_path($broad_path, $nb_levels);
				$query = "INSERT INTO <pmb> {<".$uri."> pmb:broadPath '".$formated_path."'}";
				$this->handler->data_query($query);
			}
		}
		return $broad_paths;
	}
	
	/**
	 * Enregistrement des chemins des concepts specifiques
	 * @param string $uri
	 * @return array
	 */
	protected function save_narrow_paths($uri) {
		global $thesaurus_concepts_autopostage_specific_levels_nb;
		
		$nb_levels = $thesaurus_concepts_autopostage_specific_levels_nb;
		if (!is_numeric($nb_levels)) {
			$nb_levels = -1;
		}
		$narrow_paths = array();
		$narrow_paths = $this->get_narrow_paths($uri, array(), "", $nb_levels);
		if (count($narrow_paths)) {
			foreach ($narrow_paths as $narrow_path) {
				$formated_path = $this->format_path($narrow_path, $nb_levels);
				$query = "INSERT INTO <pmb> {<".$uri."> pmb:narrowPath '".$formated_path."'}";
				$this->handler->data_query($query);
			}
		}
		return $narrow_paths;
	}
	
	protected function update_narrowers_broad_paths($id, $broader_paths = array()) {
		$broad_paths_updated = array();
		$query = "
				SELECT ?narrower ?broadpath
				WHERE {
					?narrower pmb:broadPath ?broadpath .
					FILTER regex(?broadpath, '^(".$id."\/)|(\/".$id."\/)')
				}";
		$this->handler->data_query($query);
		$results = $this->handler->data_result();
		
		$broad_paths_index = array();
		
		if(is_array($results) && count($results)){
			//dans un premier temps on supprime tout
			$query_delete = "DELETE { ";
			foreach ($results as $result) {				
				$query_delete .= "<".$result->narrower."> pmb:broadPath '".$result->broadpath."' .";
			}
			$query_delete .= " }";
			$this->handler->data_query($query_delete);
			foreach ($results as $result) {
				$path = preg_replace('#'.$id.'\/[0-9\/]*\/#', '', $result->broadpath);
				$id_item = onto_common_uri::get_id($result->narrower);
				if (!isset($broad_paths_index[$id_item])) {
					$broad_paths_index[$id_item] = array();
				}
				if (!in_array($path, $broad_paths_updated)) {
					if (count($broader_paths)) {
						$query_insert = " INSERT INTO <pmb> {";
						foreach ($broader_paths as $broader_path) {
							$broad_paths_index[$id_item][] = $path.$id.'/'.$broader_path;
							$query_insert .= "<".$result->narrower."> pmb:broadPath '".$path.$id.'/'.$broader_path."' .";
						}
						$query_insert .= "}";
						$this->handler->data_query($query_insert);
						
						$broad_paths_updated[] = $path;
					}
				}
			}
		}
		//indexation
		foreach ($broad_paths_index as $id => $broad_path_index) {			
			$broadpaths_preflabels = $this->get_paths_preflabels($broad_path_index);
			$this->get_onto_index()->update_paths_index($id, $broadpaths_preflabels);
		}
	}
	
	protected function update_broaders_narrow_paths($id, $narrower_paths = array()) {
		$narrow_paths_updated = array();
		$query = "
				SELECT ?broader ?narrowpath
				WHERE {
					?broader pmb:narrowPath ?narrowpath .
					FILTER regex(?narrowpath, '^(".$id."\/)|(\/".$id."\/)')
				}";
		$this->handler->data_query($query);
		$results = $this->handler->data_result();
		
		$narrow_paths_index = array();
		
		if(is_array($results) && count($results)){
			//dans un premier temps on supprime tout
			$query_delete = "DELETE { ";
			foreach ($results as $result) {				
				$query_delete .= "<".$result->broader."> pmb:narrowPath '".$result->narrowpath."' .";
			}
			$query_delete .= " }";
			$this->handler->data_query($query_delete);
			foreach ($results as $result) {
				$path = preg_replace('#'.$id.'\/[0-9\/]*\/#', '', $result->narrowpath);
				$id_item = onto_common_uri::get_id($result->broader);
				if (!isset($narrow_paths_index[$id_item])) {
					$narrow_paths_index[$id_item] = array();
				}
				if (!in_array($path, $narrow_paths_updated)) {
					if (count($narrower_paths)) {
						$query_insert = " INSERT INTO <pmb> {";
						foreach ($narrower_paths as $narrower_path) {
							$narrow_paths_index[$id_item][] = $path.$id.'/'.$narrower_path;
							$query_insert .= "<".$result->broader."> pmb:narrowPath '".$path.$id.'/'.$narrower_path."' .";
						}
						$query_insert .= "}";
						$this->handler->data_query($query_insert);
						
						$narrow_paths_updated[] = $path;
					}
				}
			}
		}
		//indexation
		foreach ($narrow_paths_index as $id => $narrow_path_index) {
			$narrowpaths_preflabels = $this->get_paths_preflabels($narrow_path_index);
			$this->get_onto_index()->update_paths_index($id_item, $narrowpaths_preflabels, true);
		}
	}
	
	protected function get_paths_preflabels($paths) {
		if (is_array($paths) && count($paths)) {
			for ($i = 0 ; $i < count($paths) ; $i++) {
				$ids = explode('/', $paths[$i]);
				if (count($ids)) {
					for ( $j = 0 ; $j < count($ids) ; $j++) {
						$ids[$j] = $this->get_preflabel_from_id($ids[$j]);
					}
				}
				$paths[$i] = $ids;
			}
		}
		return $paths;
	}
	
	protected function get_preflabel_from_id($id) {
		if ($id) {
			$uri = onto_common_uri::get_uri($id);
			$query = "
				SELECT ?preflabel
				WHERE {
					<".$uri."> skos:prefLabel ?preflabel
				}";
			if ($this->handler->data_query($query)) {
				$results = $this->handler->data_result();
				$lang = '';
				if (!empty($results[0]->preflabel_lang)) {
					$lang = $results[0]->preflabel_lang;
				}
				return array(
						'id' => $id,
						'preflabel' => $results[0]->preflabel,
						'lang' => $lang
				);
			}
		}
		return array();
	}
	
	protected function get_onto_index() {
		if (empty(static::$onto_index)) {
			static::$onto_index = onto_index::get_instance($this->get_onto_name());
			static::$onto_index->set_handler($this->handler);
			static::$onto_index->init();
		}
		return static::$onto_index;
	}
	
	protected function format_path($path, $nb_levels = -1) {
		if ($nb_levels > -1) {
			$formated_path = '';
			$list_path = explode('/', $path);
			for ($i = 0; $i < $nb_levels; $i++) {
				if (!empty($list_path[$i])) {
					$formated_path .= $list_path[$i].'/';
				}
			}
			$path = $formated_path;
		}
		return $path;
	}
}