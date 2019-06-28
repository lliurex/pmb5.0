<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: comparateur.inc.php,v 1.4 2016-10-26 09:11:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/facette_search_compare.class.php");
$facette_compare = new facette_search_compare();

switch($action) {	
	case "save":
		$facette_compare->save_form();
		print $facette_compare->get_display_parameters();
	break;
	case "modify":
		print $facette_compare->get_form();
		break;
	case "display":
	default:
		print $facette_compare->get_display_parameters();
	break;
}

