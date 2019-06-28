<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: publishers_list.inc.php,v 1.38 2017-04-24 15:29:18 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// traitement de la saisie utilisateur

include("$include_path/marc_tables/$pmb_indexation_lang/empty_words");
require_once($class_path."/analyse_query.class.php");
require_once($class_path.'/searcher/searcher_authorities_publishers.class.php');

if($user_input) {
	$user_input = stripslashes($user_input);
} else {
	$user_input = '*';
}

editeur::search_form();

$publishers_searcher = searcher_factory::get_searcher('publishers', '',stripslashes($user_input));
$nbr_lignes = $publishers_searcher->get_nb_results();

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page_gestion;

if($nbr_lignes) {
	$ed_list = "<tr>
		<th></th>
		<th>".$msg[103]."</th>
		<th>".$msg[72]."</th>
		<th>".$msg[147]."</th>
		<th>".$msg["count_notices_assoc"]."</th>
		</tr>";
	
	$parity=1;
	$url_base = "./autorites.php?categ=editeurs&sub=reach&user_input=".rawurlencode($user_input) ;
	$sorted_publishers = $publishers_searcher->get_sorted_result('default', $debut, $nb_per_page_gestion);
	foreach ($sorted_publishers as $authority_id) {
		// On va chercher les infos spécifique à l'autorité
		$authority = new authority($authority_id);
		$publisher_id = $authority->get_num_object();
		$ed = $authority->get_object_instance();
		
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
				$pair_impair = "odd";
		}
		$parity += 1;
		
		$notice_count_sql = "SELECT count(*) FROM notices WHERE ed1_id=".$publisher_id;
		$notice_count1 = pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
		$notice_count_sql = "SELECT count(*) FROM notices WHERE ed2_id=".$publisher_id;
		$notice_count = $notice_count1+pmb_mysql_result(pmb_mysql_query($notice_count_sql), 0, 0);
		
	    $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
	    
	    
        $ed_list.= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
        $ed_list.= "<td style='text-align:center; width:25px;'>
        				<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=publisher&id=".$publisher_id."'>
        					<i class='fa fa-eye'></i>
        				</a>
        			</td>";
        $ed_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=editeurs&sub=editeur_form&id=".$publisher_id."&user_input=".rawurlencode($user_input)."&nbr_lignes=$nbr_lignes&page=$page';\">";
        //$ed_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=see&sub=publisher&id=".$publisher_id."';\">";
		$ed_list.= $authority->get_display_statut_class_html().htmlentities($ed->name,ENT_QUOTES,$charset);
		$ed_list.= "</td>";
		//$ed_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=editeurs&sub=editeur_form&id=".$publisher_id."&user_input=".rawurlencode($user_input)."&nbr_lignes=$nbr_lignes&page=$page';\">";
		$ed_list.= "<td valign='top' onmousedown=\"document.location='./autorites.php?categ=see&sub=publisher&id=".$publisher_id."';\">";
		$affcall='';
		if ($ed->ville || $ed->pays) {
			if ($ed->ville) {
				$affcall.=$ed->ville;
				if($ed->pays) $affcall.=' - ';
			}
			$affcall.=$ed->pays;
		}
		$ed_list.= htmlentities($affcall,ENT_QUOTES,$charset);
		
		$ed_list.= "</td>
					<td align='right'>";
					
		if($ed->web) {
			$ed_list .= "<a href='$ed->web' target='_new'>".htmlentities($ed->web,ENT_QUOTES,$charset)."</a>";
		}else {
			$ed_list .= '&nbsp;';
		}
		$ed_list .= "</td>"; 
		
		if($notice_count && $notice_count!=0)
			$ed_list .= "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=2&etat=aut_search&aut_type=publisher&aut_id=".$publisher_id."'\">".$notice_count."</td>";
		else 
			$ed_list .= '<td>&nbsp;</td>';		
		$ed_list .= "</tr>";
			
	} // fin while
	$url_base = $url_base.'&authority_statut='.$authority_statut;
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;
	else $nav_bar = "";
		  	
	// affichage du résultat
	print $publishers_searcher->get_results_list_from_search($msg['154'], $user_input, $ed_list, $nav_bar);
} else {
	// la requête n'a produit aucun résultat
	error_message($msg[152], str_replace('!!ed_cle!!', $user_input, $msg[153]), 0, './autorites.php?categ=editeurs&sub=&id=');
}
