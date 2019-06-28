<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.11 2017-01-25 16:43:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (!isset($exact)) $exact = 0;

require_once('./autorites/auth_common.inc.php');
require_once($class_path.'/authperso.class.php');

// gestion des authperso
$authperso = new authperso($id_authperso, $id);
print '<h1>'.$msg[140].'&nbsp;: '.$authperso->info['name'].'</h1>';

$url_base = './autorites.php?categ=authperso&sub='.$sub.'&id_authperso='.$id_authperso.'&user_input='.rawurlencode(stripslashes($user_input)).'&exact='.$exact;

switch($sub) {
	case 'reach':
		print $authperso->get_list();
		break;
	case 'delete':
		$id_authperso = $authperso->id;
		$sup_result = $authperso->delete($id);
		if(!$sup_result) {
			print $authperso->get_list();
		}else {
			error_message($msg[132], $sup_result, 1, './autorites.php?categ=authperso&sub=authperso_form&id_authperso='.$id_authperso.'&id='.$id);
		}
		break;
	case 'replace':
		$id_authperso = $authperso->id;
		if(!$by) {
			print $authperso->replace_form($id);
		} else {
			// routine de remplacement
			$rep_result = $authperso->replace($id, $by, $aut_link_save);
			if(!$rep_result) {
				print $authperso->get_list();
			}else {
				error_message($msg[132], $rep_result, 1, './autorites.php?categ=authperso&sub=authperso_form&id_authperso='.$id_authperso.'&id='.$id);
			}
		}
		break;
	case 'duplicate':
		print $authperso->get_form($id, true);
		break;
	case 'update':				
		$id = $authperso->update_from_form($id);
		if($authperso->get_cp_error_message()) {
			require_once($include_path.'/user_error.inc.php');
			error_message($msg['search_by_authperso_title'], $authperso->get_cp_error_message(), 1, './autorites.php?categ=authperso&sub=authperso_form&id_authperso='.$id_authperso.'&id='.$id);
		}else{
			$sub='authperso';
			include('./autorites/see/main.inc.php');
		}
		break;
	case 'authperso_form':
		print $authperso->get_form($id);
		break;
	case 'authperso_last':			
		print $authperso->get_list();
		break;
	default:
		if(!$pmb_allow_authorities_first_page && $user_input == ''){
			print $authperso->get_list(true);
		}else {
			// affichage du début de la liste
			print $authperso->get_list();
		}
		break;
}
