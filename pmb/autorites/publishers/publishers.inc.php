<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publishers.inc.php,v 1.17 2017-04-04 12:42:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// on a besoin des templates éditeurs
include($include_path.'/templates/editeurs.tpl.php');

// la classe de gestion des éditeurs
require_once($class_path.'/editor.class.php');

// gestion des éditeurs
print '<h1>'.$msg[140].'&nbsp;: '. $msg[135].'</h1>';

switch($sub) {
	case 'reach':
		include('./autorites/publishers/publishers_list.inc.php');
		break;
	case 'delete':
		$ed = new editeur($id);
		$sup_result = $ed->delete();
		if(!$sup_result) {
			include('./autorites/publishers/publishers_list.inc.php');
		} else {
			error_message($msg[132], $sup_result, 1, './autorites.php?categ=editeurs&sub=editeur_form&id='.$id);
		}
		break;
	case 'replace':
		$editeur = new editeur($id);
		if(!$ed_id) {
			$editeur->replace_form();
		} else {
			// routine de remplacement
			$rep_result = $editeur->replace($ed_id,$aut_link_save);
			if(!$rep_result) {
				include('./autorites/publishers/publishers_list.inc.php');
			} else { 
				error_message($msg[132], $rep_result, 1, './autorites.php?categ=editeurs&sub=editeur_form&id='.$id);
			}
		}
		break;
	case 'duplicate':
		$ed = new editeur($id);
		$id = 0;
		$ed->show_form(true);
		break;
	case 'update':
		// mise à jour d'un éditeur
		$ed = array(
				'name' => $ed_nom,
				'adr1' => $ed_adr1,
				'adr2' => $ed_adr2,
				'cp' => $ed_cp,
				'ville' => $ed_ville,
				'pays' => $ed_pays,
				'ed_comment'	=> $ed_comment,
				'statut'	=> $authority_statut,
				'web' => $ed_web,
				'id_fou' => $id_fou,
				'thumbnail_url' => $authority_thumbnail_url
		);
		$editeur = new editeur($id);
		$editeur->update($ed);
		if($editeur->get_cp_error_message()){
			require_once($include_path.'/user_error.inc.php');
			error_message($msg['145'], $editeur->get_cp_error_message(), 1, './autorites.php?categ=editeurs&sub=editeur_form&id='.$editeur->id);
		}else{
			$id = $editeur->id;
			$sub = 'publisher';
			include('./autorites/see/main.inc.php');
		}
		break;		
	case 'editeur_form':
			// affichage du form pour création / modification
			$editeur = new editeur($id);
			$editeur->show_form();
		break;		
	case 'editeur_last':
		$last_param = 1;
		$tri_param = 'order by ed_id desc ';
		$limit_param = 'limit 0, '.$pmb_nb_lastautorities;
		$clef = "";
		$nbr_lignes = 0 ;
		include('./autorites/publishers/publishers_list.inc.php');
		break;		
	default:
		if(!$pmb_allow_authorities_first_page && $user_input == ''){
			editeur::search_form();
		}else {
			// affichage du début de la liste (par défaut)
			include('./autorites/publishers/publishers_list.inc.php');
		}
		break;
}
