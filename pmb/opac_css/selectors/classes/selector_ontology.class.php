<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_ontology.class.php,v 1.1.2.4 2018-02-14 17:23:21 apetithomme Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");
require($base_path.'/selectors/templates/sel_ontology.tpl.php');

class selector_ontology extends selector {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function proceed() {
		global $class_path;
		global $action;
		global $objs, $caller, $element, $order, $callback;
		global $range;
		global $param1, $param2;
		global $item_uri;
		global $deflt_concept_scheme;
		global $nb_per_page;
		
		$onto_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'ontology',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		$data_store_config = array(
				/* db */
				'db_name' => DATA_BASE,
				'db_user' => USER_NAME,
				'db_pwd' => USER_PASS,
				'db_host' => SQL_SERVER,
				/* store */
				'store_name' => 'rdfstore',
				/* stop after 100 errors */
				'max_errors' => 100,
				'store_strip_mb_comp_str' => 0
		);
		
		
		$tab_namespaces=array(
				"skos"	=> "http://www.w3.org/2004/02/skos/core#",
				"dc"	=> "http://purl.org/dc/elements/1.1",
				"dct"	=> "http://purl.org/dc/terms/",
				"owl"	=> "http://www.w3.org/2002/07/owl#",
				"rdf"	=> "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
				"rdfs"	=> "http://www.w3.org/2000/01/rdf-schema#",
				"xsd"	=> "http://www.w3.org/2001/XMLSchema#",
				"pmb"	=> "http://www.pmbservices.fr/ontology#"
		);
		
		$params=new onto_param(
				array(
						'categ'=>'',
						'sub'=>'',
						'objs'=>$objs,
						'action'=>'list_selector',
						'page'=>'1',
						'nb_per_page'=>($nb_per_page ? $nb_per_page : '20'),
						'caller'=>$caller,
						'element'=>$element,
						'order'=>$order,
						'callback'=>$callback,
						'base_url'=>static::get_base_url(),
						'deb_rech'=>$this->user_input,
						'range'=>$range,
						'parent_id'=>'',
						'param1' => $param1,
						'param2' => $param2,
						'item_uri' => $item_uri,
						'concept_scheme'=>$deflt_concept_scheme,
						'only_top_concepts' => '0',
						'return_concept_id' => false,
						'unique_scheme' => false
				));
		$onto_ui = new onto_ui($class_path."/rdf/skos_pmb.rdf", "arc2", $onto_store_config, "arc2", $data_store_config,$tab_namespaces,'http://www.w3.org/2004/02/skos/core#prefLabel',$params);
		$onto_ui->proceed();
		if($action=='selector_save'){
			print '<script>document.forms["search_form"].submit();</script>';
		
		}
	}
	
	public static function get_params_url() {
		global $objs, $element, $unique_scheme, $return_concept_id, $concept_scheme;
		global $order, $grammar, $perso_id, $custom_prefixe, $perso_name;
		global $deb_rech;
	
		$params_url = parent::get_params_url();
		$params_url .= ($objs ? "&objs=".$objs : "");
		$params_url .= ($element ? "&element=".$element : "");
		$params_url .= ($unique_scheme ? "&unique_scheme=".$unique_scheme : "");
		$params_url .= ($return_concept_id ? "&return_concept_id=".$return_concept_id : "");
		$params_url .= ($concept_scheme ? "&concept_scheme=".$concept_scheme : "");
		$params_url .= ($order ? "&order=".$order : "");
		$params_url .= ($grammar ? "&grammar=".$grammar : "");
		$params_url .= ($perso_id ? "&perso_id=".$perso_id : "");
		$params_url .= ($custom_prefixe ? "&custom_prefixe=".$custom_prefixe : "");
		$params_url .= ($perso_name ? "&perso_name=".$perso_name : "");
		$params_url .= ($deb_rech ? "&deb_rech=".$deb_rech : "");
		return $params_url;
	}
}
?>