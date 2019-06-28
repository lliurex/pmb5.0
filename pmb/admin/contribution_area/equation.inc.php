<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: equation.inc.php,v 1.2 2017-01-23 13:36:34 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

//dépendances
require_once($class_path.'/contribution_area/contribution_area_equation.class.php');
$contribution_area_equation = new contribution_area_equation($id);

switch($action) {
	case 'save':
		$equation = $contribution_area_equation->get_from_from();
		if(!$contribution_area_equation->save($equation)){
			error_message("",$msg['save_error'], 0);
		}
		contribution_area_equation::show_list();
		break;
	case 'add':
		$contribution_area_equation->add();
		break;
	case 'edit':
		print $contribution_area_equation->do_form();
		break;
	case 'delete':
		if(!contribution_area_equation::delete($id)){
			$used=contribution_area_equation::check_used($id);			
			foreach($used as $auth){
				$list.=$auth['link'].'<br/>';
			}
			error_message("", $msg['contribution_area_equation_used'].'<br/>'.$list);
		}
		contribution_area_equation::show_list();
		break;
	case 'build':
		$contribution_area_equation->add();
		break;
	case 'form':
		print $contribution_area_equation->do_form();
		break;
	default:
		contribution_area_equation::show_list();
		break;
}