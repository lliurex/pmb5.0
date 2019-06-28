<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2017-02-08 17:50:26 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

require_once ($class_path . '/contribution_area/contribution_area_form.class.php');
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

// page de switch formulaire de contact

switch($sub) {
	case 'area':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_contribution_area"], $admin_layout);
		print $admin_layout;
		include("./admin/contribution_area/area.inc.php");
		break;
	case 'form':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_contribution_area_form"], $admin_layout);
		print $admin_layout;
		include("./admin/contribution_area/form.inc.php");
		break;	
	case 'scenario':
		
		break;
	case 'status':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_contribution_area_status"], $admin_layout);
		print $admin_layout;
		include("./admin/contribution_area/status.inc.php");
		break;
	case 'equation':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_contribution_area_equation"], $admin_layout);
		print $admin_layout;
		include("./admin/contribution_area/equation.inc.php");
		break;
	case 'param':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_contribution_area_param"], $admin_layout);
		print $admin_layout;
		include("./admin/contribution_area/param.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_contribution_area'], $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title. $msg['admin_contribution_area'].$msg[1003].$msg[1001]);
		include("$include_path/messages/help/$lang/admin_contribution_area.txt");
		break;
}