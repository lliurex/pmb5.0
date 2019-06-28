<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: status.inc.php,v 1.2 2017-01-23 13:36:34 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!$pmb_contribution_area_activate) {
	die();
}

//dépendances
require_once($class_path.'/contribution_area/contribution_area_status.class.php');

switch($action) {
	case 'update':
		$statut = contribution_area_status::get_from_from();
		if(!contribution_area_status::save($statut)){
			error_message("",$msg['save_error'], 0);
		}
		contribution_area_status::show_list();
		break;
	case 'add':
		contribution_area_status::show_form(0);
		break;
	case 'edit':
		contribution_area_status::show_form($id);
		break;
	case 'del':
		if(!contribution_area_status::delete($id)){
			$used=contribution_area_status::check_used($id);			
			foreach($used as $auth){
				$list.=$auth['link'].'<br/>';
			}
			error_message("", $msg['contribution_area_status_used'].'<br/>'.$list);
		}
		contribution_area_status::show_list();
		break;
	default:
		contribution_area_status::show_list();
		break;
}