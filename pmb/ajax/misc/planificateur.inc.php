<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: planificateur.inc.php,v 1.4.2.1 2017-12-15 10:44:41 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/scheduler/scheduler_tasks.class.php");
require_once($class_path."/scheduler/scheduler_dashboard.class.php");
require_once($class_path."/connecteurs.class.php");

switch($sub) {
	case 'get_report' :
		print encoding_normalize::utf8_normalize(scheduler_dashboard::get_report());
		break;
	case 'reporting':
		$scheduler_dashboard = new scheduler_dashboard();
		print $scheduler_dashboard->get_display_header_list();
		print $scheduler_dashboard->get_display_content_list();
		break;
	case 'command':
		$scheduler_dashboard = new scheduler_dashboard();
		print $scheduler_dashboard->command_waiting($task_id,$cmd);
		break;
//	case 'source_synchro':
//		if ($id) {
//			if ($planificateur_id) {
//				$sql = "select param from planificateur where id_planificateur=".$planificateur_id;
//				$res = pmb_mysql_query($sql);
//				
//				$params = pmb_mysql_result($res,0,"param");
//			} else {
//				$params ="";
//			}
//			$contrs=new connecteurs();
//			require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$id]["PATH"]."/".$contrs->catalog[$id]["NAME"].".class.php");
//			eval("\$conn=new ".$contrs->catalog[$id]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$id]["PATH"]."\");");
//			$conn->unserialized_environnement($source_id,$params);
//
//			//Si on doit afficher un formulaire de synchronisation
//			$syncr_form = $conn->form_pour_maj_entrepot($source_id,"planificateur_form");			
//			if ($syncr_form) {
//				print utf8_normalize($syncr_form);
//			}
//		}
//		break;		
}
?>