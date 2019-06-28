<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: series.inc.php,v 1.15 2017-01-10 09:48:50 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// on a besoin des templates séries
include($include_path.'/templates/series.tpl.php');

// la classe de gestion des séries
require_once($class_path.'/serie.class.php');

print '<h1>'.$msg[140].'&nbsp;: '. $msg[333].'</h1>';

switch($sub) {
	case 'reach':
		include('./autorites/series/series_list.inc.php');
		break;
	case 'delete':
		$serie = new serie($id);
		$sup_result = $serie->delete();
		if(!$sup_result) {
			include('./autorites/series/series_list.inc.php');
		}else {
			error_message($msg[132], $sup_result, 1, './autorites.php?categ=series&sub=serie_form&id='.$id);
		}
		break;
	case 'replace':
		$serie = new serie($id);
		if(!$n_serie_id) {
			$serie->replace_form();
		} else {
			// routine de remplacement
			$rep_result = $serie->replace($n_serie_id,$aut_link_save);
			if (!$rep_result)
				include('./autorites/series/series_list.inc.php');
			else {
				error_message($msg[132], $rep_result, 1, './autorites.php?categ=series&sub=serie_form&id='.$id);
			}
		} 
		break;
	case 'duplicate':
		$serie = new serie($id);
		$id = 0;
		$serie->show_form(true);
		break;
	case 'update':
		// mettre à jour titre de série id
		$serie = new serie($id);
		$serie->update($serie_nom);
		if($serie->get_cp_error_message()){
			require_once($include_path.'/user_error.inc.php');
			error_message($msg['336'], $serie->get_cp_error_message(), 1, './autorites.php?categ=series&sub=serie_form&id='.$serie->s_id);
		}else{
			$id = $serie->s_id;
			$sub = 'serie';
			include('./autorites/see/main.inc.php');
		}
		break;
	case 'serie_form':
		// création / modification d'un titre de série
		$serie = new serie($id);
		$serie->show_form();
		break;
	case 'serie_last':
		$last_param = 1;
		$tri_param = 'order by serie_id desc ';
		$limit_param = 'limit 0, '.$pmb_nb_lastautorities;
		$clef = '';
		$nbr_lignes = 0 ;
		include('./autorites/series/series_list.inc.php');
		break;
	default:
		if(!$pmb_allow_authorities_first_page && $user_input == ''){
			serie::search_form();
		}else {
			// affichage du début de la liste (par défaut)
			include('./autorites/series/series_list.inc.php');
		}
		break;
}
?>