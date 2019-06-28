<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_publishers.class.php,v 1.5 2017-01-10 08:55:03 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/editor.class.php");

class vedette_publishers extends vedette_element{

	public function set_vedette_element_from_database(){
		$this->entity = new authority(0, $this->id, AUT_TABLE_PUBLISHERS);
		$this->isbd = $this->entity->get_object_instance()->display;
	}
	
	public function get_link_see(){
		return str_replace("!!type!!", "publisher",$this->get_generic_link());
	}
}
