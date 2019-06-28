<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.inc.php,v 1.6.2.2 2017-12-28 14:40:34 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$opac_contribution_area_activate || !$allow_contribution) {
	print $msg['empr_contribution_area_unauthorized'];
	return false;
}

require_once($class_path.'/contribution_area/contribution_area.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');

switch ($lvl) {
	case 'contribution_area_new' :		
		$h2o = new H2o($include_path .'/templates/contribution_area/contribution_areas.tpl.html');
		$h2o->set(array('areas' => contribution_area::get_list()));		
		print $h2o->render();
		break;
	case 'contribution_area_list' :
		if ($id_empr) {
			$h2o = new H2o($include_path .'/templates/contribution_area/contribution_areas_list.tpl.html');
			$h2o->set(array('forms' => contribution_area_forms_controller::get_empr_forms($id_empr)));
			print $h2o->render();
		}
		break;
	case 'contribution_area_done' :
		if ($id_empr) {
			$h2o = new H2o($include_path .'/templates/contribution_area/contribution_areas_list.tpl.html');
			$h2o->set(array('forms' => contribution_area_forms_controller::get_empr_forms($id_empr, true, $last_id)));
			print $h2o->render();
		}
		break;
	case 'contribution_area_moderation' :
		if ($id_empr) {
			$h2o = new H2o($include_path .'/templates/contribution_area/contribution_areas_list.tpl.html');
			$h2o->set(array('forms' => contribution_area_forms_controller::get_moderation_forms($id_empr)));
			print $h2o->render();
		}
		break;
}
?>