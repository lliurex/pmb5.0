<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: 

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($suffixe_id)) $suffixe_id = 0;
if(!isset($no_label)) $no_label = 0;

require_once("$class_path/facette_search_opac.class.php");

switch($section){
	case "lst_facettes":
		$facette = new facette_search();
		print $facette->create_list_subfields($list_crit,$sub_field,$suffixe_id,$no_label);
		
	break;
	case "lst_facettes_external":
		$facette_external = new facette_search('notices_externes',1);
		print $facette_external->create_list_subfields($list_crit,$sub_field,$suffixe_id,$no_label);
		
	break;
}
	