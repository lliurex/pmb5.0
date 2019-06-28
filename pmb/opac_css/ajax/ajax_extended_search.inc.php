<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_extended_search.inc.php,v 1.3 2017-05-16 09:57:20 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search.class.php");

$sc = new search($search_xml_file);

switch ($sub) {
	case 'get_already_selected_fields' :
		if ($add_field && $delete_field==="") {
			$search[] = $add_field;
		}
		print $sc->get_already_selected_fields();
		break;
	default :
		break;
}