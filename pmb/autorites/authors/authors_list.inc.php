<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authors_list.inc.php,v 1.51 2017-06-15 13:31:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// traitement de la saisie utilisateur
include("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once($class_path."/analyse_query.class.php");
require_once($class_path.'/searcher/searcher_factory.class.php');

if($user_input) {
	$user_input = stripslashes($user_input);
} else {
	$user_input = '*';
}
if(!isset($type_autorite)) $type_autorite = '';
auteur::search_form($type_autorite);

$authors_searcher = searcher_factory::get_searcher('authors', '', $user_input);
$nbr_lignes = $authors_searcher->get_nb_results();

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page_gestion;

if($nbr_lignes) {
	//On teste à quelle type d'autorités on a affaire pour les traitements suivants
	switch($type_autorite){
		case 70 :
			//personne physique
			$libelleResult = $msg[209];
			break;
		case 71 :
			//collectivité
			$libelleResult = $msg["aut_resul_collectivite"];
			break;
		case 72 :
			//congrès
			$libelleResult = $msg["aut_resul_congres"];
			break;
		default:
			$libelleResult = $msg[209];
			break;
	}
	$url_base = "./autorites.php?categ=auteurs&sub=reach&user_input=".rawurlencode($user_input);
	
	$num_auth_present = searcher_authorities_authors::has_authorities_sources('author');
	
	$author_list = "<tr>
			<th></th>
			<th>".$msg['103']."</th>
			".($num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
			<th>".$msg["count_notices_assoc"]."</th>
		</tr>";
	
	$sorted_authors = $authors_searcher->get_sorted_result('default', $debut, $nb_per_page_gestion);
	$parity=1;
	foreach ($sorted_authors as $authority_id) {
		// On va chercher les infos spécifique à l'autorité
		$authority = new authority($authority_id);
		$author_id = $authority->get_num_object();
		$aut = $authority->get_object_instance(array('recursif' => 1));
		
		$author_entry=$aut->isbd_entry;
		$link_auteur = "./autorites.php?categ=auteurs&sub=author_form&id=".$author_id."&user_input=".rawurlencode($user_input)."&nbr_lignes=".$nbr_lignes."&page=".$page;
		//$link_auteur = "./autorites.php?categ=see&sub=author&id=$author_id";
		if($aut->see) {
			// auteur avec renvoi
			// récupération des données de l'auteur cible
			$see = authorities_collection::get_authority(AUT_TABLE_AUTHORS, $aut->see, array('recursif' => 1));
			$author_voir=$see->isbd_entry;

			//$author_voir = "<a href='./autorites.php?categ=auteurs&sub=author_form&id=$aut->see&user_input=".rawurlencode($user_input)."&nbr_lignes=$nbr_lignes&page=$page'>$author_voir</a>";
			$author_voir = "<a href='./autorites.php?categ=see&sub=author&id=$aut->see'>$author_voir</a>";
			$author_entry .= ".&nbsp;-&nbsp;<u>$msg[210]</u>&nbsp;:&nbsp;".$author_voir;
		}
		
		$notice_count_sql = "SELECT count(distinct responsability_notice) FROM responsability WHERE responsability_author = ".$author_id;
		$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
			
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
	    $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
        $author_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer' > 
        				<td style='text-align:center; width:25px;'>
	        				<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=author&id=".$author_id."'>
	        					<i class='fa fa-eye'></i>
	        				</a>
        				</td>
              			<td valign='top' onmousedown=\"document.location='$link_auteur';\" title='".$aut->info_bulle."'>".
						$authority->get_display_statut_class_html(). $author_entry
						."</td>";
		
		//Numéros d'autorite
		if($num_auth_present){
			$author_list .= "<td>".searcher_authorities_authors::get_display_authorities_sources($author_id, 'author')."</td>";
		}	
						
		if($notice_count && $notice_count!=0){
			$author_list .= "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=0&etat=aut_search&aut_id=$author_id';\">".($notice_count)."</td>";
		}else{
			$author_list .= "<td>&nbsp;</td>";
		}					
		$author_list .= "</tr>";
			
	}
	$url_base = $url_base."&type_autorite=".$type_autorite.'&authority_statut='.$authority_statut;
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;
	else $nav_bar = "";
		
	// affichage du résultat
	print $authors_searcher->get_results_list_from_search($libelleResult, $user_input, $author_list, $nav_bar);
} else {
	// la requête n'a produit aucun résultat
	error_message($msg[211], str_replace('!!author_cle!!', $user_input, $msg[212]), 0, './autorites.php?categ=auteurs&sub=&id=');
}
