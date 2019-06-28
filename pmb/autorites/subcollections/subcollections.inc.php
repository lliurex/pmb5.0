<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subcollections.inc.php,v 1.14 2017-04-04 12:42:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions communes aux pages de gestion des autorités
require('./autorites/auth_common.inc.php');

// templates pour les fonctions de gestion des collections
include($include_path.'/templates/collections.tpl.php');

// classe gestion des collections et des éditeurs
require_once($class_path.'/editor.class.php');
require_once($class_path.'/collection.class.php');
require_once($class_path.'/subcollection.class.php');

// gestion des sous-collections
print '<h1>'.$msg[140].'&nbsp;: '. $msg[137].'</h1>';

switch($sub) {
	case 'reach':
		// afficher résultat recherche sous collection
		include('./autorites/subcollections/sub_collections_list.inc.php');
		break;
	case 'replace':
		$collection = new subcollection($id);
		if(!$by) {
			$collection->replace_form();
		} else {
			// routine de remplacement
			$rep_result = $collection->replace($by, $aut_link_save);
			if(!$rep_result) {
				include('./autorites/subcollections/sub_collections_list.inc.php');
			}else {
				error_message($msg[132], $rep_result, 1, './autorites.php?categ=souscollections&sub=collection_form&id='.$id);
			}
		}
		break;
	case 'delete':
		$collection = new subcollection($id);
		$sup_result = $collection->delete();
		if(!$sup_result) {
			include('./autorites/subcollections/sub_collections_list.inc.php');
		}else {
			error_message($msg[132], $sup_result, 1, './autorites.php?categ=souscollections&sub=collection_form&id='.$id);
		}
		break;
	case 'duplicate':
		$subcollection = new subcollection($id);
		$id = 0;
		$subcollection->show_form(true);
		break;
	case 'update':
		// mise à jour d'une sous collection
		$collection = new subcollection($id);
		$coll = array(
				'name' => $collection_nom,
				'parent' => $coll_id,
				'issn' => $issn,
				'subcollection_web' => $subcollection_web,
				'comment' => $comment,
				'statut'=> $authority_statut,
				'thumbnail_url' => $authority_thumbnail_url
		);
		$collection->update($coll);
		if($collection->get_cp_error_message()) {
			require_once($include_path.'/user_error.inc.php');
			error_message($msg['177'], $collection->get_cp_error_message(), 1, './autorites.php?categ=souscollections&sub=collection_form&id='.$collection->id);
		}else{
			$id = $collection->id;
			$sub = 'subcollection';
			include('./autorites/see/main.inc.php');
		}
		break;
	case 'collection_form':
		// création / modification d'une sous collection
		$collection = new subcollection($id);
		$collection->show_form();
		break;
	case 'collection_last':
		$last_param = 1;
		$tri_param = 'order by sub_coll_id desc ';
		$limit_param = 'limit 0, '.$pmb_nb_lastautorities;
		$clef = '';
		$nbr_lignes = 0 ;
		include('./autorites/subcollections/sub_collections_list.inc.php');
		break;
	default:
		if(!$pmb_allow_authorities_first_page && $user_input == ''){
			subcollection::search_form();
		}else {
			// affichage du début de la liste (par défaut)
			include('./autorites/subcollections/sub_collections_list.inc.php');
		}
		break;
}
