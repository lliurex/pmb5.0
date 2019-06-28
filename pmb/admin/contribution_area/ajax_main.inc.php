<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2.2.1 2017-09-14 14:45:29 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

// page de switch formulaire de contact

require_once ($class_path . '/contribution_area/contribution_area_form.class.php');
require_once ($class_path . '/contribution_area/contribution_area_scenario.class.php');
require_once ($class_path . '/contribution_area/contribution_area.class.php');
require_once ($class_path . '/onto/common/onto_common_uri.class.php');
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

switch($sub) {
	case 'area':
		switch($action){
			case "save_graph":
				$area = new contribution_area($area_id);
				$area->save_graph($data, $current_scenario);
				break;
		}
		break;
	case 'form':
		switch($action){
			case 'save' :
				$form_id+=0;
				$form = new contribution_area_form($type, $form_id);
				$form->set_from_form();
				$result = $form->save(true);
				print encoding_normalize::json_encode($result);
				break;
			case 'delete':
				$form_id+=0;
				$form = new contribution_area_form($type, $form_id);
				print encoding_normalize::json_encode($form->delete(true)); 
				break;
			default :
				if($type){
					$form_id+=0;
					$form = new contribution_area_form($type, $form_id);
					print $form->get_form();
				}else{
					print 'todo helper';
				}
				break;
		}
		break;
	case 'scenario' :
		switch ($action) {
			case 'get_rights_form' :
				$scenario_uri_id = 0;
				if (!empty($current_scenario)) {
					$uri = 'http://www.pmbservices.fr/ca/Scenario#'.$current_scenario;
					$scenario_uri_id = onto_common_uri::set_new_uri($uri);
				}
				print contribution_area_scenario::get_rights_form($scenario_uri_id);
				break;
			case 'delete' :
				$scenario_uri_id = 0;
				if (!empty($current_scenario)) {
					$uri = 'http://www.pmbservices.fr/ca/Scenario#'.$current_scenario;
					$scenario_uri_id = onto_common_uri::set_new_uri($uri);
					contribution_area_scenario::delete($scenario_uri_id);
				}
				break;
		}
		break;
}