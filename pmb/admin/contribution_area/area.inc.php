<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: area.inc.php,v 1.2 2017-01-23 13:36:34 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

require_once($class_path."/contribution_area/contribution_area.class.php");

switch($action) {
	case 'edit':
		$contribution_area= new contribution_area($id);
		print $contribution_area->get_form();
		break;
	case 'save':
		$contribution_area= new contribution_area($id);
		$contribution_area->save_from_form();
		$contribution_area->save();
		print contribution_area::get_list();
		break;
	case 'delete':
		$contribution_area= new contribution_area($id);
		$contribution_area->delete();
		print contribution_area::get_list();
		break;
	case "define" :
		$contribution_area= new contribution_area($id);
		print $contribution_area->get_definition_form();
		break;
	default:
		print contribution_area::get_list();
		break;
}
