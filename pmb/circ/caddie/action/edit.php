<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit.php,v 1.7 2017-07-04 09:38:32 dgoron Exp $

// définition du minimum nécéssaire 
$base_path="../../..";                            
$base_auth = "CATALOGAGE_AUTH";  
$base_title = "";
$base_noheader=1;
require_once ($base_path."/includes/init.inc.php");  
require_once ("./edition_func.inc.php");  
require_once ($class_path."/empr_caddie.class.php");
require_once ($class_path."/caddie/empr_caddie_controller.class.php");

$fichier_temp_nom=str_replace(" ","",microtime());
$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);

$myCart = new empr_caddie($idemprcaddie);
if (!$myCart->idemprcaddie) die();
// création de la page
switch($dest) {
	case "TABLEAU":
		empr_caddie_controller::proceed_edition_tableau($idemprcaddie);
		break;
	case "TABLEAUHTML":
		empr_caddie_controller::proceed_edition_tableauhtml($idemprcaddie);
		break;
	default:
		empr_caddie_controller::proceed_edition_html($idemprcaddie);
		break;
}
	
pmb_mysql_close($dbh);
