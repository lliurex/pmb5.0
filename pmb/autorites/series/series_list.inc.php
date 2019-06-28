<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: series_list.inc.php,v 1.38 2017-04-25 07:04:13 apetithomme Exp $

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

serie::search_form();

$series_searcher = searcher_factory::get_searcher('series', '', $user_input);
$nbr_lignes = $series_searcher->get_nb_results();

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page_gestion;

if($nbr_lignes) {
	$num_auth_present = searcher_authorities_series::has_authorities_sources('serie');
	
	$serie_list = "<tr>
		<th></th>
		<th>".$msg[103]."</th>
		".($num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
		<th>".$msg["count_notices_assoc"]."</th>
		</tr>";
	
	$parity=1;
	$url_base = "./autorites.php?categ=series&sub=reach&user_input=".rawurlencode($user_input) ;
	$sorted_series = $series_searcher->get_sorted_result('default', $debut, $nb_per_page_gestion);
	foreach ($sorted_series as $authority_id) {
		// On va chercher les infos spécifique à l'autorité
		$authority = new authority($authority_id);
		$serie_id = $authority->get_num_object();
		$serie = $authority->get_object_instance();
		
		if ($parity % 2) {
			$pair_impair = "even";
			} else {
				$pair_impair = "odd";
				}
		$parity += 1;
		
		$notice_count_sql = "SELECT count(*) FROM notices WHERE tparent_id = ".$serie_id;
		$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
		
	    $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
        $serie_list.= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
        $serie_list.= "<td style='text-align:center; width:25px;'>
        						<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=serie&id=".$serie_id."'>
        							<i class='fa fa-eye'></i>
        						</a>
        					</td>";
        $serie_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=series&sub=serie_form&id=".$serie_id."&user_input=".rawurlencode($user_input)."&nbr_lignes=$nbr_lignes&page=$page';\">";
        //$serie_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=see&sub=serie&id=".$serie_id."';\">";
		$serie_list.= $authority->get_display_statut_class_html().$serie->name."</td>";
		
		//Numéros d'autorite
		if($num_auth_present){
			$serie_list .= "<td>".searcher_authorities_series::get_display_authorities_sources($serie_id, 'serie')."</td>";
		}
		
		if($notice_count && $notice_count!=0)
			$serie_list.= "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=10&etat=aut_search&aut_type=tit_serie&aut_id=$serie_id'\">".$notice_count."</td>";
		else $serie_list.= "<td>&nbsp;</td>";
		$serie_list.= "</tr>";
	}
	$url_base = $url_base.'&authority_statut='.$authority_statut;
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;
        else $nav_bar = "";	
        
	// affichage du résultat
	print $series_searcher->get_results_list_from_search($msg['334'], $user_input, $serie_list, $nav_bar);
} else {
	// la requête n'a produit aucun résultat
	error_message($msg[152], str_replace('!!titre_cle!!', $user_input, $msg[335]), 0, './autorites.php?categ=series&sub=&id=');
}
