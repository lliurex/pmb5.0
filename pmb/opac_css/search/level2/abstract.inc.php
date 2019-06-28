<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abstract.inc.php,v 1.67.2.1 2018-01-18 08:30:14 wlair Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur notes
require_once($class_path."/searcher.class.php");
require_once($class_path."/shorturl/shorturl_type_search.class.php");
require_once($class_path."/suggest.class.php");
require_once($class_path."/sort.class.php");

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['abstract'] = $count;
}

if($opac_allow_affiliate_search){
	print $search_result_affiliate_lvl2_head;
}else {
	print "	<div id=\"resultatrech\"><h3 class='searchResult-title'>$msg[resultat_recherche]</h3>\n
		<div id=\"resultatrech_container\">
		<div id=\"resultatrech_see\">";
}

//le contenu du catalogue est calculé dans 2 cas  :
// 1- la recherche affiliée n'est pas activée, c'est donc le seul résultat affichable
// 2- la recherche affiliée est active et on demande l'onglet catalog...
if(!$opac_allow_affiliate_search || ($opac_allow_affiliate_search && $tab == "catalog")){
	print pmb_bidi("<h3 class='searchResult-search'><span class='searchResult-equation'><b>$count</b> $msg[abstract_found] <b>'".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'");
	if ($opac_search_other_function) {
		require_once($include_path."/".$opac_search_other_function);
		print pmb_bidi(" ".search_other_function_human_query($_SESSION["last_query"]));
	}
	print "</b></font>";
	print activation_surlignage();
	print "</h3></span>\n";
	
	//gestion du tri
	if (isset($_GET["sort"])) {
		$_SESSION["last_sortnotices"]=$_GET["sort"];
	}
	if ($count>$opac_nb_max_tri) {
		$_SESSION["last_sortnotices"]="";
	}

	$searcher = new searcher_abstract(stripslashes($user_query));
	if($opac_visionneuse_allow){
		$nbexplnum_to_photo = $searcher->get_nb_explnums();	
	}
	if($count){
		if(isset($_SESSION["last_sortnotices"]) && $_SESSION["last_sortnotices"]!==""){
			$notices = $searcher->get_sorted_result($_SESSION["last_sortnotices"],$debut,$opac_search_results_per_page);	
		}else{
			$notices = $searcher->get_sorted_result("default",$debut,$opac_search_results_per_page);	
		}
		if (count($notices)) {
			$_SESSION['tab_result_current_page'] = implode(",", $notices);
		} else {
			$_SESSION['tab_result_current_page'] = "";
		}
	}	
	
	if(!$opac_allow_affiliate_search) print "
			</div>";
	print "
			<div id=\"resultatrech_liste\">";
			
	if ($opac_notices_depliable) {
		if($filtre_compare=='compare'){
			 print facette_search_compare::get_begin_result_list();
		}else{
			print $begin_result_liste;
		}
	}

	//impression
	print "<span class='print_search_result'>".$link_to_print_search_result."</span>";
	
	//gestion du tri
	print sort::show_tris_in_result_list($count);
			
	print $add_cart_link;
	
	if($opac_visionneuse_allow && $nbexplnum_to_photo){
		print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;&nbsp;</span>".$link_to_visionneuse;
		print $sendToVisionneuseByPost;
	}
	//affinage
	//enregistrement de l'endroit actuel dans la session
	if ($_SESSION["last_query"]) {	$n=$_SESSION["last_query"]; } else { $n=$_SESSION["nb_queries"]; }
	
	$_SESSION["notice_view".$n]["search_mod"]="abstract";
	$_SESSION["notice_view".$n]["search_page"]=$page;
	
	//affichage
	if($opac_search_allow_refinement){
		print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"affiner_recherche\"><a href='$base_path/index.php?search_type_asked=extended_search&mode_aff=aff_simple_search' title='".$msg["affiner_recherche"]."'>".$msg["affiner_recherche"]."</a></span>";
	}	
	//fin affinage
	// url courte
	if($opac_short_url) {
		$shorturl_search = new shorturl_type_search();
		print $shorturl_search->get_display_shorturl_in_result();
	}
	
	//Etendre
	if ($opac_allow_external_search) print "<span class=\"espaceResultSearch\">&nbsp;&nbsp;</span><span class=\"search_bt_external\"><a href='$base_path/index.php?search_type_asked=external_search&mode_aff=aff_simple_search&external_type=simple' title='".$msg["connecteurs_external_search_sources"]."'>".$msg["connecteurs_external_search_sources"]."</a></span>";
	//fin etendre
	
	
	print suggest::get_add_link();
	
	$search_terms = unserialize(stripslashes($search_terms));
	
	//on suis le flag filtre/compare
	facettes::session_filtre_compare();
	
	print "<blockquote>";
	if($filtre_compare=='compare'){
		//on valide la variable session qui comprend les critères de comparaisons
		facette_search_compare::session_facette_compare();
		//affichage comparateur
		$facette_compare= new facette_search_compare();
		$compare=$facette_compare->compare($searcher);
		if($compare===true){
			print $facette_compare->display_compare();
		}else{
			print $msg[$compare];
		}
	}else{
		//si demande de réinitialisation
		if(isset($reinit_compare) && $reinit_compare==1){
			facette_search_compare::session_facette_compare(null,$reinit_compare);
		}
		print aff_notice(-1);
		$nb=0;
		$recherche_ajax_mode=0;
	
		for ($i =0 ; $i<count($notices);$i++){
			if($i>4)$recherche_ajax_mode=1;
			print pmb_bidi(aff_notice($notices[$i], 0, 1, 0, "", "", 0, 0, $recherche_ajax_mode));
		}
		
		print aff_notice(-2);
	}
	print "</blockquote>";
	print "
	</div></div>";
	if($filtre_compare=='compare'){
		print "<div id='navbar'><hr></div>";
		$catal_navbar="";
	}
	if($opac_allow_affiliate_search){
		print $catal_navbar;
	}else{
		print "</div>";
	}
}else{
	if($tab == "affiliate"){
		//l'onglet source affiliées est actif, il faut son contenu...
		$as=new affiliate_search_abstract($user_query);
		$as->getResults();
		print $as->results;
	}
	print "
	</div>
	<div class='row'><span class=\"espaceResultSearch\">&nbsp;</span></div>";
	//Enregistrement des stats
	if($pmb_logs_activate){
		global $nb_results_tab;
		$nb_results_tab['abstract_affiliate'] = $as->getTotalNbResults();
	}
}
