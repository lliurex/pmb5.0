<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/facettes_controller.class.php");

// affichage de la liste des recherches en opac
$admin_layout = str_replace('!!menu_sous_rub!!', $msg["admin_menu_opac_facette"], $admin_layout);
print $admin_layout;
print $admin_menu_facettes;

switch($section){
	case "facettes":
		include("./admin/opac/facette_search/facette.inc.php");
		break;
	case "facettes_external":
		$facettes_controller = new facettes_controller($id, 'notices_externes',1);
		$facettes_controller->proceed();		
		break;
	case "comparateur":
		include("./admin/opac/facette_search/comparateur.inc.php");
		break;
}