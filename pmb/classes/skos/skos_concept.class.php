<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: skos_concept.class.php,v 1.16.2.8 2018-01-24 16:05:21 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/common/onto_common_uri.class.php");
require_once($class_path."/onto/onto_store_arc2.class.php");
require_once($class_path."/skos/skos_datastore.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/author.class.php");
require_once($class_path."/category.class.php");
require_once($class_path."/editor.class.php");
require_once($class_path."/collection.class.php");
require_once($class_path."/subcollection.class.php");
require_once($class_path."/serie.class.php");
require_once($class_path."/titre_uniforme.class.php");
require_once($class_path."/indexint.class.php");
require_once($class_path."/explnum.class.php");
require_once($class_path."/authperso_authority.class.php");
require_once($class_path."/skos/skos_view_concepts.class.php");
require_once($class_path."/skos/skos_view_concept.class.php");

if(!defined('TYPE_NOTICE')){
	define('TYPE_NOTICE',1);
}
if(!defined('TYPE_AUTHOR')){
	define('TYPE_AUTHOR',2);
}
if(!defined('TYPE_CATEGORY')){
	define('TYPE_CATEGORY',3);
}
if(!defined('TYPE_PUBLISHER')){
	define('TYPE_PUBLISHER',4);
}
if(!defined('TYPE_COLLECTION')){
	define('TYPE_COLLECTION',5);
}
if(!defined('TYPE_SUBCOLLECTION')){
	define('TYPE_SUBCOLLECTION',6);
}
if(!defined('TYPE_SERIE')){
	define('TYPE_SERIE',7);
}
if(!defined('TYPE_TITRE_UNIFORME')){
	define('TYPE_TITRE_UNIFORME',8);
}
if(!defined('TYPE_INDEXINT')){
	define('TYPE_INDEXINT',9);
}
if(!defined('TYPE_EXPL')){
	define('TYPE_EXPL',10);
}
if(!defined('TYPE_EXPLNUM')){
	define('TYPE_EXPLNUM',11);
}
if(!defined('TYPE_AUTHPERSO')){
	define('TYPE_AUTHPERSO',12);
}
if(!defined('TYPE_CMS_SECTION')){
	define('TYPE_CMS_SECTION',13);
}
if(!defined('TYPE_CMS_ARTICLE')){
	define('TYPE_CMS_ARTICLE',14);
}

/**
 * class skos_concept
 * Le modèle d'un concept
*/
class skos_concept {
	
	/**
	 * Identifiant du concept
	 * @var int
	 */
	private $id;
	
	/**
	 * URI du concept
	 * @var string
	 */
	private $uri;
	
	/**
	 * Label du concept
	 * @var string
	 */
	private $display_label;

	/**
	 * Note du concept
	 * @var string
	 */
	private $note;
	
	/**
	 * Tableau des schemas du concept
	 * @var string
	 */
	private $schemes;
	
	/**
	 * Vedette composée associée si concept composé
	 * @var vedette_composee
	 */
	private $vedette = null;
	
	/**
	 * Enfants du concept
	 * @var skos_concepts_list
	 */
	private $narrowers;
	
	/**
	 * Parents du concept
	 * @var skos_concepts_list
	 */
	private $broaders;
	
	/**
	 * Concepts composés qui utilisent ce concept
	 * @var skos_concepts_list
	 */
	private $composed_concepts;
	
	/**
	 * Tableau des identifiants de notices indexées par le concept
	 * @var array
	 */
	private $indexed_notices;
	
	/**
	 * Tableau associatif de tableaux d'autorités indexées par le concept
	 * @var array
	 */
	private $indexed_authorities;
	
	/**
	 * Constructeur d'un concept
	 * @param int $id Identifiant en base du concept. Si nul, fournir les paramètres suivants.
	 * @param string $uri [optional] URI du concept
	 */
	public function __construct($id = 0, $uri = "") {
		if ($id) {
			$this->id = $id;
			$this->get_uri();
			$this->get_display_label();
		} else {
			$this->uri = $uri;
			$this->get_id();
			$this->get_display_label();
		}
	}
	
	/**
	 * Retourne l'URI du concept
	 */
	public function get_uri() {
		if (!$this->uri) {
			$this->uri = onto_common_uri::get_uri($this->id);
		}
		return $this->uri;
	}
	
	/**
	 * Retourne l'identifiant du concept
	 * @return int
	 */
	public function get_id() {
		if (!$this->id) {
			$this->id = onto_common_uri::get_id($this->uri);
		}
		return $this->id;
	}
	
	/**
	 * Retourne le libellé à afficher
	 * @return string
	 */
	public function get_display_label() {
		if (!$this->display_label) {
			global $lang;
				
			$this->check_display_label_in_index();
			if(!$this->display_label){
	
				$query = "select * where {
					<".$this->uri."> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label
				}";
				
				skos_datastore::query($query);
				if(skos_datastore::num_rows()){
					$results = skos_datastore::get_result();
					foreach($results as $key=>$result){
						if(isset($result->label_lang) && $result->label_lang==substr($lang,0,2)){
							$this->display_label = $result->label;
							break;
						}
					}
					//pas de langue de l'interface trouvée
					if (!$this->display_label){
						$this->display_label = $result->label;
					}
				}
			}
		}
		return $this->display_label;
	}
	
	private function check_display_label_in_index(){
		$query = 'select value from skos_fields_global_index where id_item = '.$this->id.' and code_champ = code_ss_champ and code_champ = 1';
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)){
			$this->display_label = pmb_mysql_result($result, 0, 0);
		}
	}
	
	/**
	 * Retourne les schémas du concept
	 * @return string
	 */
	public function get_schemes() {
		global $dbh, $lang;
		
		if (!isset($this->schemes)) {
			$this->schemes = array();
			$query = "select value, lang, authority_num from skos_fields_global_index where id_item = ".$this->id." and code_champ = 4 and code_ss_champ = 1";
			$last_values = array();
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					if ($row->lang == substr($lang,0,2)) {
						$this->schemes[$row->authority_num] = $row->value;
						break;
					}
					$last_values[$row->authority_num] = $row->value;
				}
				//pas de langue de l'interface trouvée
				foreach ($last_values as $scheme_id => $last_value) {
					if (!isset($this->schemes[$scheme_id])) {
						$this->schemes[$scheme_id] = $last_value;
					}
				}
			}
		}
		return $this->schemes;
	}
	
	/**
	 * Retourne le rendu HTML des schémas
	 */
	public function get_schemes_list() {
		return skos_view_concepts::get_schemes_list($this->get_schemes());
	}
	
	/**
	 * Retourne la vedette composée associée au concept
	 * @return vedette_composee
	 */
	public function get_vedette() {
		if (!$this->vedette) {
			if ($vedette_id = vedette_link::get_vedette_id_from_object($this->id, TYPE_CONCEPT_PREFLABEL)) {
				$this->vedette = new vedette_composee($vedette_id);
			}
		}
		return $this->vedette;
	}
	
	/**
	 * Retourne les enfants du concept
	 * @return skos_concepts_list Liste des enfants du concept
	 */
	public function get_narrowers() {
		if (!$this->narrowers) {
			$this->narrowers = new skos_concepts_list();
	
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?narrower
			}";
			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $result){
					$this->narrowers->add_concept(new skos_concept(0, $result->narrower));
				}
			}
		}
		return $this->narrowers;
	}
	
	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_narrowers_list() {
		return skos_view_concepts::get_narrowers_list($this->get_narrowers());
	}
	
	/**
	 * Retourne les parents du concept
	 * @return skos_concepts_list Liste des parents du concept
	 */
	public function get_broaders() {
		if (!$this->broaders) {
			$this->broaders = new skos_concepts_list();
	
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#broader> ?broader
			}";
			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $result){
					$this->broaders->add_concept(new skos_concept(0, $result->broader));
				}
			}
		}
		return $this->broaders;
	}
	
	/**
	 * Retourne le rendu HTML des enfants du concept
	 */
	public function get_broaders_list() {
		return skos_view_concepts::get_broaders_list($this->get_broaders());
	}
	
	/**
	 * Retourne les identifiants des notices indexées par le concept
	 * @return array Tableau des notices indexées par le concept
	 */
	public function get_indexed_notices() {
		global $dbh;
		
		if (!$this->indexed_notices) {
			$this->indexed_notices = array();
			
			$query = "select num_object from index_concept where num_concept = ".$this->id." and type_object = ".TYPE_NOTICE;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$this->indexed_notices[] = $row->num_object;
				}
			}
		}
		return $this->indexed_notices;
	}
	
	/**
	 * Retourne les autorités indexées par le concept
	 * @return array Tableau associatif de tableaux d'autorités indexées par le concept
	 */
	public function get_indexed_authorities() {
		global $dbh;
		
		if (!$this->indexed_authorities) {
			$this->indexed_authorities = array();
			
			$query = "select num_object, type_object from index_concept where num_concept = ".$this->id." and type_object != ".TYPE_NOTICE;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					switch ($row->type_object) {
						case TYPE_AUTHOR :
							$this->indexed_authorities['author'][] = new auteur($row->num_object);
							break;
						case TYPE_CATEGORY :
							$this->indexed_authorities['category'][] = new category($row->num_object);
							break;
						case TYPE_PUBLISHER :
							$this->indexed_authorities['publisher'][] = new editeur($row->num_object);
							break;
						case TYPE_COLLECTION :
							$this->indexed_authorities['collection'][] = new collection($row->num_object);
							break;
						case TYPE_SUBCOLLECTION :
							$this->indexed_authorities['subcollection'][] = new subcollection($row->num_object);
							break;
						case TYPE_SERIE :
							$this->indexed_authorities['serie'][] = new serie($row->num_object);
							break;
						case TYPE_TITRE_UNIFORME :
							$this->indexed_authorities['titre_uniforme'][] = authorities_collection::get_authority(AUT_TABLE_TITRES_UNIFORMES, $row->num_object);
							break;
						case TYPE_INDEXINT :
							$this->indexed_authorities['indexint'][] = new indexint($row->num_object);
							break;
						case TYPE_EXPL :
							//TODO Quelle classe utiliser ?
// 							$this->indexed_authorities['expl'][] = new auteur($row->num_object);
							break;
						case TYPE_EXPLNUM :
							$this->indexed_authorities['explnum'][] = new explnum($row->num_object);
							break;
						case TYPE_AUTHPERSO :
							$this->indexed_authorities['authperso'][] = new authperso_authority($row->num_object);
							break;
						default:
							break;
					}
				}
			}
		}
		return $this->indexed_authorities;
	}
	
	/**
	 * Retourne les concepts composés qui utilisent le concept
	 * @return skos_concepts_list Liste des concepts composés qui utilisent le concept
	 */
	public function get_composed_concepts() {
		if (!$this->composed_concepts) {
			$this->composed_concepts = new skos_concepts_list();
			
			$this->composed_concepts->set_composed_concepts_built_with_element($this->id, "concept");
		}
		return $this->composed_concepts;
	}

	/**
	 * Retourne le détail d'un concept
	 * @return array Tableau des différentes propriétés du concept
	 */
	public function get_details() {
		global $lang;
		$details = array();

		$resource = skos_datastore::get_data_resource();
		$resource->setURI($this->uri);
		$props = $resource->getProps();

		foreach($props as $prop => $obj){
			//ces property la, on les gère dans d'autres méthodes
			if(in_array($prop,array(
				'http://www.w3.org/2004/02/skos/core#prefLabel',
				'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
				'http://www.w3.org/2004/02/skos/core#inScheme',
				'http://www.pmbservices.fr/ontology#showInTop',
				'http://www.w3.org/2004/02/skos/core#narrower',
				'http://www.w3.org/2004/02/skos/core#borrower',
				'http://www.w3.org/2004/02/skos/core#hasTopConcept',
				'http://www.w3.org/2004/02/skos/core#topConceptOf',
				'http://www.pmbservices.fr/ontology#broadPath',
				'http://www.pmbservices.fr/ontology#narrowPath',
			))){
				continue;
			}
			if(!isset($details[$prop])){
				$details[$prop] = array();
			}
			for($i=0 ; $i<count($obj) ; $i++){
				if($obj[$i]['type'] == 'literal'){
					$obj[$i]['value'] = encoding_normalize::charset_normalize($obj[$i]['value'],'utf-8');
					if(isset($obj[$i]['lang']) && $obj[$i]['lang'] == substr($lang,0,2)){
						if(!in_array($obj[$i]['value'],$details[$prop])){
							$details[$prop][] =$obj[$i]['value'];
						}
						continue;
					}else{
						if(!in_array($obj[$i]['value'],$details[$prop])){
							$details[$prop][] = $obj[$i]['value'];
						}
					}
				}else{
					$resource->setURI($obj[$i]['value']);
					$subobj = $resource->getProps('skos:prefLabel');
					if($subobj != ''){
						//on cherche si l'URI est connu dans notre système
						$id = onto_common_uri::get_id($obj[$i]['value']);
						$detail = array(
								'uri' => $obj[$i]['value']
						);
 						for($j=0 ; $j<count($subobj) ; $j++){
 							$subobj[$j]['value'] = encoding_normalize::charset_normalize($subobj[$j]['value'],'utf-8');
							if(isset($subobj[$j]['lang']) && $subobj[$j]['lang'] == substr($lang,0,2)){
								$detail['label'] = $subobj[$j]['value'];
							}else if($detail['label'] == "") {
								$detail['label'] = $subobj[$j]['value'];
							}
 						}
						if($id){
							$detail['id'] = $id;
						}
						if(!in_array($detail,$details[$prop])){
							$details[$prop][] = $detail;
						}
					}
				}
			}
			if(count($details[$prop]) === 0 ){
				unset ($details[$prop]);
			}
		}
		return $details;
	}
	
	/**
	 * Retourne la note
	 * @return string
	 */
	public function get_note() {
		global $lang;
			
		if (!$this->note) {						
			$query = "select * where {
				<".$this->uri."> <http://www.w3.org/2004/02/skos/core#note> ?note
			}";			
			skos_datastore::query($query);
			if(skos_datastore::num_rows()){
				$results = skos_datastore::get_result();
				foreach($results as $key=>$result){
					if($result->note_lang==substr($lang,0,2)){
						$this->note = $result->note;
						break;
					}
				}
				//pas de langue de l'interface trouvée
				if (!$this->note){
					$this->note = $result->note;
				}
			}
		}
		return $this->note;
	}	
	
	public function get_details_list() {
		return skos_view_concept::get_detail_concept($this);
	}

	public function get_right() {		
		return SESSrights & CONCEPTS_AUTH;
	}
		
	public function get_db_id() {
		return $this->get_id();
	}
	
	public function get_gestion_link(){
		return './autorites.php?categ=see&sub=concept&id='.$this->id;
	}
	
	public function get_isbd() {
		global $msg;
		$this->get_schemes();
		if(count($this->schemes)){
			$display_label = '['.implode(' / ', $this->schemes).'] ';
		}else{
			$display_label = '['.$msg['skos_view_concept_no_scheme'].'] ';
		}
		return $display_label.$this->get_display_label();
		
	}
	
	public function get_comment() {
		return '';
	}
	
	public function get_authoritieslist() {
		return skos_view_concept::get_authorities_indexed_with_concept($this);
	}
	
	public function get_header() {
		return $this->get_isbd();
	}
	
	public static function get_format_data_structure($antiloop = false) {
		global $msg;
			
		$main_fields = array();
		$main_fields[] = array(
				'var' => "id",
				'desc' => $msg['1601']
		);
		$main_fields[] = array(
				'var' => "uri",
				'desc' => $msg['ontology_object_uri']
		);
		$main_fields[] = array(
				'var' => "permalink",
				'desc' => $msg['notice_permalink_opac']
		);
		$main_fields[] = array(
				'var' => "label",
				'desc' => $msg['cms_concept_format_data_display_label']
		);
		$main_fields[] = array(
				'var' => "note",
				'desc' => $msg['ontology_skos_note']
		);
		$main_fields[] = array(
				'var' => "schemes",
				'desc' => $msg['ontology_skos_conceptscheme']
		);
		$main_fields[] = array(
				'var' => "broaders_list",
				'desc' => $msg['onto_common_broader']
		);
		$main_fields[] = array(
				'var' => "narrowers_list",
				'desc' => $msg['onto_common_narrower']
		);
// 		$authority = new authority(0, 0, AUT_TABLE_CONCEPT);
// 		$main_fields = array_merge($authority->get_format_data_structure(), $main_fields);
		return $main_fields;
	}
	
	public function format_datas($antiloop = false){
		$formatted_data = array(
				'id' => $this->get_id(),
				'uri' => $this->get_uri(),
				'permalink' => $this->get_id(),
				'label' => $this->get_isbd(),
				'note' => $this->get_note(),
				'schemes' => $this->get_schemes(),
				'broaders_list' => $this->get_broaders_list(),
				'narrowers_list' => $this->get_narrowers_list()
		);
// 		$authority = new authority(0, $this->id, AUT_TABLE_CONCEPT);
// 		$formatted_data = array_merge($authority->format_datas(), $formatted_data);
		return $formatted_data;
	}
	
	/**
	 * Retourne le chemin des concepts génériques
	 * @param string $uri
	 * @param array $paths
	 * @param string $path_beginning
	 * @return array
	 */
	public static function get_paths($uri, $paths = array(), $path_beginning = '', $type = 'broader') {
		if ($uri) {
			if ($type == 'broader') {
				$query = "select ?entity where {
					<".$uri."> <http://www.w3.org/2004/02/skos/core#broader> ?entity
				}";
			} else {
				$query = "select ?entity where {
					<".$uri."> <http://www.w3.org/2004/02/skos/core#narrower> ?entity
				}";
			}
				
			skos_datastore::query($query);
			$results = skos_datastore::get_result();
				
			if(is_array($results) && count($results)){
				foreach ($results as $result) {
					$entity_id = onto_common_uri::get_id($result->entity);
					if (strpos($path_beginning, $entity_id.'/') === false) {
						$key = array_search($path_beginning, $paths);
						if ($key !== false) {
							$paths[$key] = $path_beginning.$entity_id.'/';
						} else {
							$paths[] = $path_beginning.$entity_id.'/';
						}
						$paths = self::get_paths($result->entity, $paths, $path_beginning.$entity_id.'/', $type );
					}
				}
			}
		}
		return $paths;
	}
	
	public static function get_broad_paths($uri){
// 		$paths = self::get_paths($uri);
		$paths = array();
		$query = "select ?broad_path where {
					<".$uri."> pmb:broadPath ?broad_path
				}";
		skos_datastore::query($query);
		$results = skos_datastore::get_result();
		if (is_array($results) && count($results)) {
			foreach ($results as $result) {
				$paths[] = $result->broad_path;
			}
		}
		return $paths;
	}
	
	public static function get_narrow_paths($uri){
		//$paths = self::get_paths($uri, array(), '', 'narrow');
		$paths = array();
		$query = "select ?narrow_path where {
					<".$uri."> pmb:narrowPath ?narrow_path
				}";
		skos_datastore::query($query);
		$results = skos_datastore::get_result();
		if (is_array($results) && count($results)) {
			foreach ($results as $result) {
				$paths[] = $result->narrow_path;
			}
		}
		return $paths;
	}
}