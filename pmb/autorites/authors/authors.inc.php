<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authors.inc.php,v 1.22 2017-07-18 09:40:12 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions communes aux pages de gestion des autorités
require('./autorites/auth_common.inc.php');

// classe de gestion des auteurs
include($class_path.'/author.class.php');
include($include_path.'/templates/authors.tpl.php');

// gestion des auteurs
print '<h1>'.$msg[140].'&nbsp;: '. $msg[133].'</h1>';

if(!isset($type_autorite))  $type_autorite = '';

switch($sub) {
	case 'reach':
		include('./autorites/authors/authors_list.inc.php');
		break;
	case 'delete':
		$auteur = new auteur($id);
		$sup_result = $auteur->delete();
		if(!$sup_result) {
			include('./autorites/authors/authors_list.inc.php');
		}else {
			error_message($msg[132], $sup_result, 1, './autorites.php?categ=auteurs&sub=author_form&id='.$id);
		}
		break;
	case 'replace':
		if(!$by) {
			$auteur = new auteur($id);
			$auteur->replace_form();
		}else {
			// routine de remplacement
			$auteur = new auteur($id);
			$rep_result = $auteur->replace($by,$aut_link_save);
			if(!$rep_result) {
				include('./autorites/authors/authors_list.inc.php');
			}else {
				error_message($msg[132], $rep_result, 1, './autorites.php?categ=auteurs&sub=author_form&id='.$id);
			}
		}
		break;
	case 'duplicate' :
		$auteur = new auteur($id);
		$id = 0;
		$auteur->show_form($auteur->type, true);
		break;
	case 'update':
		// mise à jour d'un auteur
		$author = array(
				'type' 			=> $author_type,
				'name' 			=> $author_nom,
				'rejete' 		=> $author_rejete,
				'date' 			=> $date,
				'author_web'	=> $author_web,
				'author_comment'=> $author_comment,
				'voir_id' 		=> $voir_id,
				'lieu'			=> $lieu,
				'ville'			=> $ville,
				'pays'			=> $pays,
				'subdivision'	=> $subdivision,
				'numero'		=> $numero,
				'import_denied'	=> (isset($author_import_denied) ? $author_import_denied : 0),
		        'statut'		=> $authority_statut,
		        'thumbnail_url' => $authority_thumbnail_url
		);
		$auteur = new auteur($id);
		$auteur->update($author);
		if($auteur->get_cp_error_message()){
			require_once($include_path.'/user_error.inc.php');
			error_message($msg['200'], $auteur->get_cp_error_message(), 1, './autorites.php?categ=auteurs&sub=author_form&id='.$auteur->id);
		}else{
			$id = $auteur->id;
			$type_autorite=$author_type;
			$sub = 'author';
			include('./autorites/see/main.inc.php');
		}		
		break;
	case 'author_form':
		// création / modification d'un responsable
		$auteur = new auteur($id);
		$auteur->show_form((isset($type_autorite) ? $type_autorite : ''));
		break;
	case 'author_last':
		$last_param=1;
		$tri_param = 'order by author_id desc ';
		$limit_param = 'limit 0, '.$pmb_nb_lastautorities;
		$clef = '';
		$nbr_lignes = 0 ;
		include('./autorites/authors/authors_list.inc.php');
		break;
	case 'duplicate':
		$auteur = new auteur($id);
		$auteur->id = 0 ;
		$auteur->duplicate_from_id = $id ; 
		$auteur->type = $type_autorite;
		$auteur->show_form($type_autorite);
		break;
	default:
		if(!$pmb_allow_authorities_first_page && $user_input == ''){
			auteur::search_form($type_autorite);
		}else {
			// affichage du début de la liste
			include('./autorites/authors/authors_list.inc.php');
		}	
		break;
}
