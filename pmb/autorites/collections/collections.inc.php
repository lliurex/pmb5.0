<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections.inc.php,v 1.16 2017-04-04 12:42:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions communes aux pages de gestion des autorités
require('./autorites/auth_common.inc.php');

// templates pour les fonctions de gestion des collections
include($include_path.'/templates/collections.tpl.php');

// classe gestion des collections et des éditeurs
include($class_path.'/collection.class.php');
include($class_path.'/editor.class.php');

// gestion des collections
print '<h1>'.$msg[140].'&nbsp;: '. $msg[136].'</h1>';
$tri_param = ' order by index_coll ';

switch($sub) {
	case 'reach':
	// afficher résultat recherche collection
		include('./autorites/collections/collections_list.inc.php');
		break;
	case 'replace':
		if(!$by) {
			$collection = new collection($id);
			$collection->replace_form();
		} else {
			// routine de remplacement
			$collection = new collection($id);
			$rep_result = $collection->replace($by, $aut_link_save);
			if(!$rep_result)
				include('./autorites/collections/collections_list.inc.php');
			else {
				error_message($msg[132], $rep_result, 1, './autorites.php?categ=collections&sub=collection_form&id='.$id);
			}
		}
		break;
	case 'duplicate':
		$collection = new collection($id);
		$id = 0;
		$collection->show_form(true);
		break;
	case 'delete':
		$coll = new collection($id);
		$sup_result = $coll->delete();
		if(!$sup_result)
			include('./autorites/collections/collections_list.inc.php');
		else {
			error_message($msg[132], $sup_result, 1, './autorites.php?categ=collections&sub=collection_form&id='.$id);
		}
		break;
	case 'update':
		// mise à jour d'une collection
		$collection_nom = clean_string($collection_nom);
		if ((!$collection_nom) || (!$ed_id)) {
			error_message($msg[132], $mg['erreur_creation_collection'], 1, '');
		}else {
			$coll = array(
				'name' => $collection_nom,
				'parent' => $ed_id,
				'collection_web' => $collection_web,
				'issn' => $issn,
				'comment' => $comment,
			    'statut'=> $authority_statut,
				'thumbnail_url' => $authority_thumbnail_url
			);
			
			$collection = new collection($id);
			$collection->update($coll);
			if($collection->get_cp_error_message()){//Traitement des messages d'erreurs champs persos
				require_once($include_path.'/user_error.inc.php');
				error_message($msg['167'], $collection->get_cp_error_message(), 1, './autorites.php?categ=collections&sub=collection_form&id='.$collection->id);
			}else{
				$id = $collection->id;
				$sub = 'collection';
				include('./autorites/see/main.inc.php');
			}
		}
		break;
	case 'collection_form':
			// affichage du form pour création / modification
			$collection = new collection($id);
			$collection->show_form();
		break;
	case 'collection_last':
		$last_param = 1;
		$tri_param = 'order by collection_id desc ';
		$limit_param = 'limit 0, '.$pmb_nb_lastautorities;
		$clef = '';
		$nbr_lignes = 0 ;
		include('./autorites/collections/collections_list.inc.php');
		break;
	default:
		if(!$pmb_allow_authorities_first_page && $user_input == ''){
			collection::search_form();
		}else {
			// affichage du form de recherche (par défaut)
			include('./autorites/collections/collections_list.inc.php');
		}
		break;
}