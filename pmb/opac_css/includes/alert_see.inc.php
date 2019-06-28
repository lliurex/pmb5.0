<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alert_see.inc.php,v 1.5 2017-03-03 16:22:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// fonctions de conversion simple2mc
require_once($include_path."/search_queries/specials/combine/search.class.php");

// Gestion des alertes à partir de la recherche simple
$mc=combine_search::simple2mc($_SESSION['last_query']);
$field_0_s_4[]=serialize(array(
		'serialized_search' => $mc['serialized_search'],
		'search_type' => $mc['search_type']
));

unset($search);

$op_0_s_4="EQ";
$inter_0_s_4="";
$search[0]="s_4";

if (isset($_SESSION['opac_view']) && $_SESSION['opac_view']) {
	$query = "select opac_view_query from opac_views where opac_view_id = ".$_SESSION['opac_view'];
	$result = pmb_mysql_query($query, $dbh);

	if ($result && pmb_mysql_num_rows($result)) {
		$row = pmb_mysql_fetch_object($result);
		$serialized = $row->opac_view_query;
	}

	if ($serialized) {
		$field_1_s_4[]=serialize(array(
				'serialized_search' => $serialized,
				'search_type' => "search_fields"
		));
		$op_1_s_4="EQ";
		$inter_1_s_4="and";
		$search[1]="s_4";
	}
}
$es = new search();
$alert_see_mc_values = $es->make_hidden_search_form("./index.php?lvl=more_results","mc_values","",true);