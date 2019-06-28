<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param.inc.php,v 1.2 2017-02-09 09:03:10 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

//dépendances
require_once($class_path.'/contribution_area/contribution_area_param.class.php');

$contribution_area= new contribution_area_param();

switch ($action){
	case "save":
		$contribution_area->save_from_form();
		print "<script type='text/javascript'>window.location.href='./admin.php?categ=contribution_area'</script>";
		break;
	default:		
		print $contribution_area->get_form();
		break;
}

