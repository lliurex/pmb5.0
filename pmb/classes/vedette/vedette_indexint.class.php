<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_indexint.class.php,v 1.3 2017-01-10 08:55:03 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/vedette/vedette_element.class.php");
require_once($class_path."/indexint.class.php");

class vedette_indexint extends vedette_element{
	
	public function set_vedette_element_from_database(){
		$this->entity = new authority(0, $this->id, AUT_TABLE_INDEXINT);
		
		$this->isbd = "";
		if ($this->entity->get_object_instance()->name_pclass) {
			$this->isbd .= "[".$this->entity->get_object_instance()->name_pclass."] ";
		}
		
		$this->isbd .= $this->entity->get_object_instance()->name;
		
		if ($this->entity->get_object_instance()->comment) {
			$this->isbd .= " - ".$this->entity->get_object_instance()->comment;
		}
	}
	public function get_link_see(){
		return str_replace("!!type!!", "indexint",$this->get_generic_link());
	}
}
