<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sub_collections_list.inc.php,v 1.39 2017-04-25 07:04:13 apetithomme Exp $

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

subcollection::search_form();

$subcollections_searcher = searcher_factory::get_searcher('subcollections', '', $user_input);
$nbr_lignes = $subcollections_searcher->get_nb_results();

/* pour ajouter un lien de création :
<a href='./autorites.php?categ=souscollections&sub=collection_form&id='>$msg[176]</a>
*/

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page_gestion;

if($nbr_lignes) {
	$num_auth_present = searcher_authorities_subcollections::has_authorities_sources('subcollection');
	
	$collection_list = "<tr>
		<th></th>
		<th>".$msg[103]."</th>
		<th>".$msg[165]."</th>
		".($num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
		<th>".$msg["count_notices_assoc"]."</th>
		</tr>";
	
	$parity=1;
	$url_base = "./autorites.php?categ=souscollections&sub=reach&user_input=".rawurlencode($user_input) ;
	$sorted_subcollections = $subcollections_searcher->get_sorted_result('default', $debut, $nb_per_page_gestion);
	foreach ($sorted_subcollections as $authority_id) {
		// On va chercher les infos spécifique à l'autorité
		$authority = new authority($authority_id);
		$subcollection_id = $authority->get_num_object();
		$coll = $authority->get_object_instance();
		
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
		
		$notice_count_sql = "SELECT count(*) FROM notices WHERE subcoll_id = ".$subcollection_id;
		$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
		
	    $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
        $collection_list.= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
        $collection_list.= "<td style='text-align:center; width:25px;'>
        						<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=subcollection&id=".$subcollection_id."'>
        							<i class='fa fa-eye'></i>
        						</a>
        					</td>";
        $collection_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=souscollections&sub=collection_form&id=$subcollection_id&user_input=".rawurlencode($user_input)."&nbr_lignes=$nbr_lignes&page=$page';\">";
        //$collection_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=see&sub=subcollection&id=".$subcollection_id."';\">";
        $collection_list.= $authority->get_display_statut_class_html().$coll->name;
		$collection_list .= "&nbsp;($coll->parent_libelle.&nbsp;$coll->editeur_libelle)";
		$collection_list .= "</td><td>".htmlentities($coll->issn,ENT_QUOTES, $charset)."</td>";
		
		//Numéros d'autorite
		if($num_auth_present){
			$collection_list .= "<td>".searcher_authorities_subcollections::get_display_authorities_sources($subcollection_id, 'subcollection')."</td>";
		}
		
		if($notice_count && $notice_count>0) 
			$collection_list .= "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=2&etat=aut_search&aut_type=subcoll&aut_id=".$subcollection_id."'\">".$notice_count."</td>";
		else $collection_list.= "<td>&nbsp;</td>";	
		$collection_list .= "</tr>";
	} // fin while
	$url_base = $url_base.'&authority_statut='.$authority_statut;
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;
	else $nav_bar = "";

	// affichage du résultat
	print $subcollections_searcher->get_results_list_from_search($msg['183'], $user_input, $collection_list, $nav_bar);
} else {
	// la requête n'a produit aucun résultat
	error_message($msg[184], str_replace('!!cle!!', $user_input, $msg[181]), 0, './autorites.php?categ=souscollections&sub=&id=');
}

