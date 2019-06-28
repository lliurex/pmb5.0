<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.9 2017-05-12 15:15:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/caddie/authorities_caddie_controller.class.php");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ):
	case 'commande':
		
	break;
	case 'type_empty_word':
		include('./autorites/semantique/ajax/type_empty_word.inc.php');
	break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'grid' :
		require_once($class_path."/grid.class.php");
		grid::proceed($datas);
		break;
	case 'fill_form':
		include('./autorites/fill_form/ajax_main.inc.php');
		break;
	case 'get_tu_form_vedette':
		include('./autorites/titres_uniformes/tu_form_vedette.inc.php');
		break;
	case 'caddie':
		if(isset($caddie)) {
			$idcaddie = substr($caddie, strrpos($caddie, '_')+1);
		}
		if(isset($object)) {
			$id_item = substr($object, strrpos($object, '_')+1);
		}
		authorities_caddie_controller::proceed_ajax($idcaddie, $id_item);
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("autorites",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	default:
	//tbd
	break;		
endswitch;	
