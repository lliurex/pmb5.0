<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax.inc.php,v 1.6 2017-04-12 12:13:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search.class.php");

if(!isset($search_xml_file)) $search_xml_file = '';
if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';

$sc=new search(true, $search_xml_file, $search_xml_file_full_path);

$sc->link = './catalog.php?categ=isbd&id=!!id!!';
$sc->link_expl = './catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!'; 
$sc->link_explnum = './catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!';
$sc->link_serial = './catalog.php?categ=serials&sub=view&serial_id=!!id!!';
$sc->link_analysis = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
$sc->link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
$sc->link_explnum_serial = "./catalog.php?categ=serials&sub=explnum_form&serial_id=!!serial_id!!&explnum_id=!!explnum_id!!";
$sc->link_explnum_analysis = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
$sc->link_explnum_bulletin = "./catalog.php?categ=serials&sub=bulletinage&action=explnum_form&bul_id=!!bul_id!!&explnum_id=!!explnum_id!!";

switch ($sub) {
	case 'get_already_selected_fields' :
	default:
		if (($add_field)&&(($delete_field==="")&&(!$launch_search))) {
			$search[]=$add_field;
		}		
		print $sc->get_already_selected_fields();
		print '<script type="text/javascript">';
		print $sc->get_script_window_onload();
		print '</script>';
		break;
}
