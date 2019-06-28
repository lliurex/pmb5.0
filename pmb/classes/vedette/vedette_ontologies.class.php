<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_ontologies.class.php,v 1.2 2015-08-11 10:45:17 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/ontology.class.php");

class vedette_ontologies extends vedette_element{
	public $params = array();
	
	public function __construct($params,$type, $id, $isbd = ""){
		$this->params = $params;
		if(!is_int($id)){
			$id = onto_common_uri::get_id($id);
		}
		parent::__construct($type, $id, $isbd);
	}

	public function set_vedette_element_from_database(){
		$ontology = new ontology($this->params['id_ontology']);
 		$this->isbd = $ontology->get_instance_label(onto_common_uri::get_uri($this->id));
	}
}
