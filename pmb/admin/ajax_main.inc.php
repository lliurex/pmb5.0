<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.13 2017-01-23 13:36:34 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//En fonction de $categ, il inclut les fichiers correspondants

switch($categ):
	case 'acces':
		include('./admin/acces/ajax/acces.inc.php');
		break;
	case 'req':
		include('./admin/proc/ajax/req.inc.php');
		break;
	case 'sync':
		include('./admin/connecteurs/in/dosync.php');
		break;
	case 'opac':
		include('./admin/opac/ajax_main.inc.php');
	break;	
	case 'harvest':
		include('./admin/harvest/ajax_main.inc.php');
	break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'nomenclature' :
		include("./admin/nomenclature/ajax_main.inc.php");
		break;
	case 'webdav' :
		include("./admin/connecteurs/out/webdav/ajax_main.inc.php");
		break;
	case 'connector' :
		include("./admin/connecteurs/ajax_main.inc.php");
		break;	
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("admin",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'cms':
		include('./admin/cms/ajax_main.inc.php');
		break;
	case 'contribution_area':
		if ($pmb_contribution_area_activate) {
			include('./admin/contribution_area/ajax_main.inc.php');
		}
		break;
	default:
		break;		
endswitch;
