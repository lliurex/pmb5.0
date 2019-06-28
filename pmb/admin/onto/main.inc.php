<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2015-08-10 23:16:26 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

$ontologies = new ontologies();
$admin_layout = str_replace("!!ontologies_menu!!",$ontologies->get_admin_menu(),$admin_layout);

 switch($sub) {
 	case 'general':
 		print str_replace("!!menu_sous_rub!!",$msg['ontologies_general'],$admin_layout);
  		$ontologies->admin_proceed($act,$ontology_id);
 		break;
 	default :	
 		print str_replace("!!menu_sous_rub!!","",$admin_layout);
 		$ontology = new ontology($ontology_id);
 		$ontology->exec_onto_framework();
 		break;

 }
?>