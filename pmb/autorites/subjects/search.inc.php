<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search.inc.php,v 1.56 2017-05-17 07:30:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$url_base = "./autorites.php?categ=categories&sub=&id=0&parent=";

// inclusions diverses
include("$include_path/templates/category.tpl.php");
require_once("$class_path/category.class.php");
require_once("$class_path/analyse_query.class.php");
require_once("$class_path/thesaurus.class.php");
require_once($class_path.'/searcher/searcher_factory.class.php');

// search.inc : recherche des catégories en gestion d'autorités

//Récuperation de la liste des langues définies pour l'interface
$langages = new XMLlist("$include_path/messages/languages.xml", 1);
$langages->analyser();
$lg = $langages->table;

if($user_input) {
	$user_input = stripslashes($user_input);
} else {
	$user_input = '*';
}

//affichage du selectionneur de thesaurus et du lien vers les thésaurus
$liste_thesaurus = thesaurus::getThesaurusList();
$sel_thesaurus = '';
$lien_thesaurus = '';

if ($thesaurus_mode_pmb != 0) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
	$sel_thesaurus = "<select class='saisie-30em' id='id_thes' name='id_thes' ";
	$sel_thesaurus.= "onchange = \"document.location = '".$url_base."&id_thes='+document.getElementById('id_thes').value; \">" ;
	foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
		$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
		if ($id_thesaurus == $id_thes) $sel_thesaurus.= " selected";
		$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES, $charset)."</option>";
	}
	$sel_thesaurus.= "<option value=-1 ";
	if ($id_thes == -1) $sel_thesaurus.= "selected ";
	$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
	$sel_thesaurus.= "</select>&nbsp;";

	$lien_thesaurus = "<a href='./autorites.php?categ=categories&sub=thes'>".$msg['thes_lien']."</a>";

}	
$user_query=str_replace("<!-- sel_thesaurus -->",$sel_thesaurus,$user_query);
$user_query=str_replace("<!-- lien_thesaurus -->",$lien_thesaurus,$user_query);
$user_query = str_replace("<!-- sel_authority_statuts -->", authorities_statuts::get_form_for(AUT_TABLE_CATEG, $authority_statut, true), $user_query);

//affichage du choix de langue pour la recherche
$sel_langue = '';
$sel_langue = "<div class='row'>";
$sel_langue.= "<input type='checkbox' name='lg_search' id='lg_search' value='1' ";
if(isset($lg_search) && $lg_search == 1){
	$sel_langue .= " checked='checked' ";
}
$sel_langue.= "/>&nbsp;".htmlentities($msg['thes_sel_langue'],ENT_QUOTES, $charset);
$sel_langue.= "</div><br />";
$user_query=str_replace("<!-- sel_langue -->",$sel_langue,$user_query);
$user_query=str_replace("!!user_input!!",htmlentities($user_input,ENT_QUOTES, $charset),$user_query);

categ_browser::search_form((isset($parent) ? $parent : ''));

//recuperation du thesaurus session 
if(!$id_thes) {
	$id_thes = thesaurus::getSessionThesaurusId();
} else {
	thesaurus::setSessionThesaurusId($id_thes);
}

// traitement de la saisie utilisateur
include("$include_path/marc_tables/$pmb_indexation_lang/empty_words");

$categ_searcher = searcher_factory::get_searcher('categories', '', $user_input);
$nbr_lignes = $categ_searcher->get_nb_results();

if(!$page) $page=1;
$debut =($page-1)*$nb_per_page_gestion;

if($nbr_lignes) {
	$parity=1;
	
	$num_auth_present = searcher_authorities_categories::has_authorities_sources('category');
	
	$categ_list = "<tr>
		<th></th>
		<th>".$msg[103]."</th>
		<th>".$msg["categ_commentaire"]."</th>
		".($num_auth_present ? '<th>'.$msg['authorities_number'].'</th>' : '')."
		<th>".$msg["count_notices_assoc"]."</th>
		</tr>";

	$sorted_categ = $categ_searcher->get_sorted_result('default', $debut, $nb_per_page_gestion);
	foreach ($sorted_categ as $authority_id) {
		// On va chercher les infos spécifique à l'autorité
		$authority = new authority($authority_id);
		$categ_id = $authority->get_num_object();
		$categ = $authority->get_object_instance();
		
		if ($id_thes == -1) {
			$display = '['.htmlentities($categ->thes->libelle_thesaurus,ENT_QUOTES, $charset).']';
		} else {
			$display = '';
		}
		if (isset($lg_search) && $lg_search) $display.= '['.$lg[$categ->langue].'] ';
		if($categ->voir_id) {
			$temp = authorities_collection::get_authority(AUT_TABLE_CATEG, $categ->voir_id);
			$display .= $categ->libelle." -&gt; <i>";
			$display .= $temp->catalog_form;
			$display.= "@</i>";
		} else {
			$display .= $categ->catalog_form;
		}	

		$notice_count = $categ->notice_count(false);
		
		$categ_entry = $authority->get_display_statut_class_html().$display ;
		$categ_comment = $categ->commentaire;
 		$link_categ = "./autorites.php?categ=categories&sub=categ_form&parent=0&id=".$categ_id."&id_thes=".$categ->thes->id_thesaurus."&user_input=".rawurlencode($user_input)."&nbr_lignes=".$nbr_lignes."&page=".$page."&nb_per_page=".$nb_per_page_gestion;
//		$link_categ = "./autorites.php?categ=see&sub=category&id=".$categ_id;
		
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		
		$parity += 1;
		$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\"  ";
        $categ_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>
        				<td style='text-align:center; width:25px;'>
        					<a title='".$msg['authority_list_see_label']."' href='./autorites.php?categ=see&sub=category&id=".$categ_id."'>
        						<i class='fa fa-eye'></i>
        					</a>
        		    	</td>
        				<td valign='top' onmousedown=\"document.location='$link_categ';\">
						$categ_entry
						</td>
						<td valign='top' onmousedown=\"document.location='$link_categ';\">
						$categ_comment
						</td>";
		
		//Numéros d'autorite
        if($num_auth_present){
        	$categ_list .= "<td>".searcher_authorities_categories::get_display_authorities_sources($categ_id, 'category')."</td>";
        }
		
		if($notice_count && $notice_count!=0)	
			$categ_list .= "<td onmousedown=\"document.location='./catalog.php?categ=search&mode=1&etat=aut_search&aut_type=categ&aut_id=".$categ_id."'\">".$notice_count."</a></td>";
		else $categ_list .= "<td>&nbsp;</td>";
		$categ_list .= "</tr>";
			
	} // fin while

	//Création barre de navigation
	$url_base='./autorites.php?categ=categories&sub=search&id_thes='.$id_thes.'&user_input='.rawurlencode($user_input).'&lg_search='.(isset($lg_search) ? $lg_search : '');
	if (!$last_param) $nav_bar = aff_pagination ($url_base, $nbr_lignes, $nb_per_page_gestion, $page, 10, false, true) ;
        else $nav_bar = "";
	
	// affichage du résultat
	print $categ_searcher->get_results_list_from_search($msg['1320'], $user_input, $categ_list, $nav_bar);

} else {
	// la requête n'a produit aucun résultat
	error_message($msg[211], str_replace('!!categ_cle!!', $user_input, $msg["categ_no_categ_found_with"]), 0, './autorites.php?categ=categories&sub=search');
}