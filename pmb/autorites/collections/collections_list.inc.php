<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collections_list.inc.php,v 1.42 2017-04-25 07:04:13 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//collections_list.inc : liste les éditeurs correspondants à la regex user_input
// affichage de la liste collections pour sélection

// initialisation variables
$nav_bar = '';
$collection_list = '';

// traitement de la saisie utilisateur

include("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once($class_path."/analyse_query.class.php");
require_once($class_path.'/searcher/searcher_factory.class.php');

if($user_input) {
	$user_input = stripslashes($user_input);
} else {
	$user_input = '*';
}

collection::search_form();

$collections_searcher = new searcher_authorities_collections($user_input);
$collections_searcher = searcher_factory::get_searcher('collections', '', $user_input);
$nbr_lignes = $collections_searcher->get_nb_results();

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page_gestion;

if($nbr_lignes) {
	$num_auth_present = searcher_authorities_collections::has_authorities_sources('collection');
	
	$collection_list .= "<tr>
		<th></th>
		<th>".$msg[103]."</th>
		<th>".$msg[165]."</th>
		".($num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
		<th>".$msg["count_notices_assoc"]."</th>
		</tr>";
	
	$parity=1;
	$url_base = "./autorites.php?categ=collections&sub=reach&user_input=".rawurlencode($user_input) ;
	$sorted_collections = $collections_searcher->get_sorted_result('default', $debut, $nb_per_page_gestion);
	
	foreach ($sorted_collections as $authority_id) {
		// On va chercher les infos spécifique à l'autorité
		$authority = new authority($authority_id);
		$collection_id = $authority->get_num_object();
		$coll = $authority->get_object_instance();
		
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;

		$notice_count_sql = "SELECT count(*) FROM notices WHERE coll_id = ".$collection_id;
		$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
		
        $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
                $collection_list.= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
                $collection_list.= "<td style='text-align:center; width:25px;'>
        						<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=collection&id=".$collection_id."'>
        							<i class='fa fa-eye'></i>
        						</a>
        					</td>";
                $collection_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=collections&sub=collection_form&id=$collection_id&user_input=".rawurlencode($user_input)."&nbr_lignes=$nbr_lignes&page=$page';\">";
                //$collection_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=see&sub=collection&id=$collection_id';\">";                
                $collection_list.= $authority->get_display_statut_class_html().htmlentities($coll->display,ENT_QUOTES, $charset);
		$collection_list .= "</td>
							<td>".htmlentities($coll->issn,ENT_QUOTES, $charset)."</td>";
		
		//Numéros d'autorite
		if($num_auth_present){
			$collection_list .= "<td>".searcher_authorities_collections::get_display_authorities_sources($collection_id, 'collection')."</td>";
		}
		
		if($notice_count && $notice_count!=0)
			$collection_list .=  "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=2&etat=aut_search&aut_type=collection&aut_id=$collection_id'\">".$notice_count."</td>";
		else $collection_list .= "<td>&nbsp;</td>";
		$collection_list .=  "</tr>";
	} // fin while
	$url_base = $url_base.'&authority_statut='.$authority_statut;
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;
	else $nav_bar="";   

	// affichage du résultat
	print $collections_searcher->get_results_list_from_search($msg[173], $user_input, $collection_list, $nav_bar);
} else {
	// la requête n'a produit aucun résultat
	error_message($msg[175], str_replace('!!cle!!', $user_input, $msg[174]), 0, './autorites.php?categ=collections&sub=&id=');
}