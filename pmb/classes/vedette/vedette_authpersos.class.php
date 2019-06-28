<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_authpersos.class.php,v 1.5 2017-06-20 12:45:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/authperso.class.php");

class vedette_authpersos extends vedette_element{
	public $params = array();
	
	public function __construct($params,$type, $id, $isbd = ""){
		$this->entity = new authority(0, $id, AUT_TABLE_AUTHPERSO);
		$params['id_authority'] = $this->entity->get_object_instance()->id;
		$params['label'] = $this->entity->get_object_instance()->info['authperso']['name'];
		$this->params = $params;
		parent::__construct($type, $id, $isbd);
	}

	public function set_vedette_element_from_database(){
		$this->entity = new authority(0, $this->id, AUT_TABLE_AUTHPERSO);
 		$this->isbd = $this->entity->get_object_instance()->get_isbd($this->id);
	}
	
	public function get_link_see(){
		return str_replace("!!type!!", "authperso",$this->get_generic_link());		
	}
}
