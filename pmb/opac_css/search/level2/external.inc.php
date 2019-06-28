<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: external.inc.php,v 1.14 2017-02-10 16:03:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// second niveau de recherche OPAC sur titre
// inclusion classe pour affichage notices (level 1)
require_once($base_path.'/includes/templates/notice.tpl.php');
require_once($base_path.'/classes/notice.class.php');
require_once($class_path."/search.class.php");
require_once($class_path."/facettes_external.class.php");

global $external_sources;
if(isset($field_0_s_2) && is_array($field_0_s_2)) {
	$selected_sources = implode(',', $field_0_s_2);
}
if(isset($reinit_facettes_external) && $reinit_facettes_external) {
	facettes_external::destroy_global_env();
}
if((isset($param_delete_facette)) || (isset($check_facette) && is_array($check_facette))) {
	facettes_external::checked_facette_search();
}

if ($_SESSION["ext_type"]=="multi") {
	$es=new search("search_fields_unimarc");
	$es->remove_forbidden_fields();
} else {
	$es=new search("search_simple_fields_unimarc");
}
$es->show_results_unimarc("./index.php?lvl=more_results&mode=external","./index.php?search_type_asked=external_search&external_type=simple", true);

//Enregistrement des stats
if($pmb_logs_activate){
	global $nb_results_tab;
	$nb_results_tab['external'] = $count;
}