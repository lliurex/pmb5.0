<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: form.inc.php,v 1.4.2.1 2017-09-14 14:45:29 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

require_once($class_path.'/contribution_area/contribution_area_forms_controller.class.php');
require_once($include_path.'/templates/contribution_area/contribution_area_forms.tpl.php');
require_once($class_path."/contribution_area/contribution_area.class.php");
require_once($class_path."/contribution_area/contribution_area_form.class.php");

require_once($class_path."/autoloader.class.php");
$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

switch($action) {
	case 'grid':
            $form_id+=0;
            $form =  new contribution_area_form('', $form_id);
            print $form->render();
            break;
    case 'save' :
    		print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
       		$form_id+=0;
       		$form = new contribution_area_form($type, $form_id);
       		$form->set_from_form();
       		$result = $form->save();
       		print $form->get_redirection($area*1);
       		break;
    case 'delete':
    		print '<div class="row"><div class="msg-perio">'.$msg['catalog_notices_suppression'].'</div></div>';
       		$form_id+=0;
       		$form = new contribution_area_form($type, $form_id);
       		$form->delete();
       		print $form->get_redirection();
       		break;
    case 'edit':
    		if(!isset($area)){
    			$area = 0;
    		}
       		$form_id+=0;
       		$form = new contribution_area_form($type, $form_id);
       		print $form->get_form($area*1);
       		break;
	default:
			print contribution_area_forms_controller::display_forms_list();
            break;
}
