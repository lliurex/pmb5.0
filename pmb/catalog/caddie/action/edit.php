<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.19 2017-07-04 09:38:32 dgoron Exp $

// définition du minimum nécessaire 
$base_path="../../..";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ($base_path."/includes/init.inc.php");  
require_once ("./edition_func.inc.php");  
require_once ($class_path."/caddie.class.php");
require_once ($class_path."/caddie/caddie_controller.class.php");

$use_opac_url_base=1;
$prefix_url_image=$opac_url_base;
$no_aff_doc_num_image=1;

$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

$myCart = new caddie($idcaddie);
if (!$myCart->idcaddie) die();
// création de la page
switch($dest) {
	case "TABLEAU":
		caddie_controller::proceed_edition_tableau($idcaddie);
		break;
	case "TABLEAUHTML":
		caddie_controller::proceed_edition_tableauhtml($idcaddie);
		break;
	case "EXPORT_NOTI":
		$fname = "bibliographie.doc";
		header('Content-Disposition: attachment; filename='.$fname);
		header('Content-type: application/msword');
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		$contents=afftab_cart_objects ($idcaddie, $elt_flag , $elt_no_flag, $notice_tpl );
		echo $contents;
		break;		
	default:
		caddie_controller::proceed_edition_html($idcaddie);
		break;
}
pmb_mysql_close($dbh);
