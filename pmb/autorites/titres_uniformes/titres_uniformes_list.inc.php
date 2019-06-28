<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titres_uniformes_list.inc.php,v 1.21 2017-04-24 09:21:00 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// traitement de la saisie utilisateur
include("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once($class_path."/analyse_query.class.php");
require_once($class_path.'/searcher/searcher_authorities_titres_uniformes.class.php');

if($user_input) {
	$user_input = stripslashes($user_input);
} else {
	$user_input = '*';
}

titre_uniforme::search_form();

$tu_searcher = searcher_factory::get_searcher('titres_uniformes', '',stripslashes($user_input));
$nbr_lignes = $tu_searcher->get_nb_results();

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page_gestion;

if($nbr_lignes) {
	$url_base = "./autorites.php?categ=titres_uniformes&sub=reach&user_input=".rawurlencode($user_input) ;
	
	$num_auth_present = searcher_authorities_titres_uniformes::has_authorities_sources('uniform_title');
	
	$titre_uniforme_list = "<tr>
			<th></th>
			<th>".$msg[103]."</th>
			".($num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
			<th>".$msg["count_notices_assoc"]."</th>
		</tr>";
	
	$parity=1;
	$sorted_tu = $tu_searcher->get_sorted_result('default', $debut, $nb_per_page_gestion);
	foreach ($sorted_tu as $authority_id) {
		// On va chercher les infos spécifique à l'autorité
		$authority = new authority($authority_id);
		$tu_id = $authority->get_num_object();
		$tu = $authority->get_object_instance();

		$tu->do_isbd();
		$titre_uniforme_entry = $tu->get_isbd();
		//$titre_uniforme_entry = $tu->display;
		$link_titre_uniforme = "./autorites.php?categ=titres_uniformes&sub=titre_uniforme_form&id=".$tu_id."&user_input=".rawurlencode($user_input)."&nbr_lignes=$nbr_lignes&page=$page";
		//$link_titre_uniforme = './autorites.php?categ=see&sub=titre_uniforme&id='.$tu_id;
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
		
		$notice_count_sql = "SELECT count(*) FROM notices_titres_uniformes WHERE ntu_num_tu = ".$tu_id;
		$notice_count = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
		
	    $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\"  ";
        $titre_uniforme_list .= "
        <tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
			<td style='text-align:center; width:25px;'>
        		<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=titre_uniforme&id=".$tu_id."'>
        			<i class='fa fa-eye'></i>
        		</a>
        	</td>
         	<td valign='top' onmousedown=\"document.location='$link_titre_uniforme';\">
				".$authority->get_display_statut_class_html()."$titre_uniforme_entry
			</td>";
		
		//Numéros d'autorite
        if($num_auth_present){
        	$titre_uniforme_list .= "<td>".searcher_authorities_titres_uniformes::get_display_authorities_sources($tu_id, 'uniform_title')."</td>";
        }
		
		if($notice_count && $notice_count!=0)
			$titre_uniforme_list .=  "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=9&etat=aut_search&aut_type=titre_uniforme&aut_id=".$tu_id."'\">".$notice_count."</td>";
		else $titre_uniforme_list .= "<td>&nbsp;</td>";	
		$titre_uniforme_list .=  "</tr>";
			
	} // fin while

	$url_base = $url_base.'&authority_statut='.$authority_statut.'&oeuvre_type_selector='.$oeuvre_type_selector.'&oeuvre_nature_selector='.$oeuvre_nature_selector;
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;
	else $nav_bar = "";
		
	// affichage du résultat
	print $tu_searcher->get_results_list_from_search($msg['aut_titre_uniforme_result'], $user_input, $titre_uniforme_list, $nav_bar);
} else {
	// la requête n'a produit aucun résultat
	error_message($msg[211], str_replace('!!author_cle!!', $user_input, $msg["aut_titre_uniforme_no_result"]), 0, './autorites.php?categ=titres_uniformes&sub=&id=');
}
