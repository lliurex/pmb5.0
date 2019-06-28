<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_controller.class.php,v 1.1 2016-10-14 07:49:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// Controleur de facettes
require_once($class_path."/facette_search_opac.class.php");
require_once($class_path."/facette.class.php");

class facettes_controller {
	
	protected $id;
	
	protected $type;
	
	protected $is_external;
	
	
	public function __construct($id=0, $type='notices', $is_external=false){
		$this->id = $id*1;
		$this->type = $type;
		$this->is_external = $is_external;
	}
	
	public function proceed() {
		global $action;
		
		$facette_search = new facette_search($this->type, $this->is_external);
		
		switch($action) {
			case "edit":
				$facette = new facette($this->id, $this->is_external);
				print $facette->get_form();
				break;
			case "save":
				$facette = new facette($this->id, $this->is_external);
				$facette->set_properties_from_form();
				$facette->save();
				print $facette_search->get_display_list();
				break;
			case "delete":
				$facette = new facette($this->id, $this->is_external);
				$facette->delete();
				print $facette_search->get_display_list();
				break;
			case "up":
				facette_search::facette_up($this->id);
				print $facette_search->get_display_list();
				break;
			case "down":
				facette_search::facette_down($this->id);
				print $facette_search->get_display_list();
				break;
			case "order":
				facette_search::facette_order_by_name();
				print $facette_search->get_display_list();
				break;
			default:
				print $facette_search->get_display_list();
				break;
		}
	}
}

