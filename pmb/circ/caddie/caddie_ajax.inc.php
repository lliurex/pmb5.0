<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_ajax.inc.php,v 1.4 2016-11-15 13:35:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/empr_caddie.class.php");
require_once($include_path."/empr_cart.inc.php");

switch($sub) {
	case "pointage" :
		$idcaddie = empr_caddie::check_rights($idcaddie) ;
		if($idcaddie) {
			$myCart = new empr_caddie($idcaddie);
			switch ($action) {
				case 'add_item':
					if($id_item) {
						$res_pointage = $myCart->pointe_item($id_item);
					}
					break;
				case 'del_item':
					$res_pointage = $myCart->depointe_item($id_item);
					break;
				default:
					break;
			}
			$aff_cart_nb_items = $myCart->aff_cart_nb_items();
		}
		
		if(!$id_item) $id_item = 0;
		if(!$idcaddie) $idcaddie = 0;
		if(!$res_pointage) $res_pointage = 0;
		$result = array(
				'id'=>$id_item,
				'idcaddie'=>$idcaddie,
				'res_pointage'=>$res_pointage,
				'aff_cart_nb_items'=>($charset != "utf-8" ? utf8_encode($aff_cart_nb_items) : $aff_cart_nb_items)
		);
		ajax_http_send_response($result);
		break;
	default:
		$idcaddie=substr($caddie,5);
		$object_type=substr($object,0,4);
		$object_id=substr($object,10);
		$idcaddie = empr_caddie::check_rights($idcaddie) ;
		if ($idcaddie) {
			$myCart = new empr_caddie($idcaddie);
			$myCart->add_item($object_id);
			$myCart->compte_items();
		} else die("Failed: "."obj=".$object." caddie=".$caddie);
		print $myCart->nb_item;
		break;
}


?>