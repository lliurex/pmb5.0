<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit_explnum.inc.php,v 1.17 2017-07-18 14:24:20 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des doc numeriques


//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
	$dom_1= $ac->setDomain(1);
	$acces_m = $dom_1->getRights($PMBuserid,$id,8);
}

if ($acces_m==0) {
	
	error_message('', htmlentities($dom_1->getComment('mod_enum_error'), ENT_QUOTES, $charset), 1, '');
	
} else {
	require_once("$include_path/templates/explnum.tpl.php");
	
	print "<h1>".$msg['explnum_doc_associe']."</h1>";
	$notice = new mono_display($id, 1, './catalog.php?categ=modif&id=!!id!!', FALSE);
	print pmb_bidi("<div class=\"row\"><b>".$notice->header.'</b><br />');
	print pmb_bidi($notice->isbd.'</div>');
	print "<div class=\"row\">";
	
	
	$suppr = "./catalog.php?categ=del_explnum&id=$id&explnum_id=$explnum_id";
	if(!isset($bulletin_id)){
		$bulletin_id = 0;
	}
	$nex = new explnum($explnum_id, $id, $bulletin_id);
	$explnum_form = $nex->explnum_form("./catalog.php?categ=explnum_update&sub=update&id=$explnum_id", "./catalog.php?categ=isbd&id=$id",$suppr);
	
	print $explnum_form;
	print '</div>';
	
}
?>
	