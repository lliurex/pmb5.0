<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_create.inc.php,v 1.22 2016-05-09 10:13:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


// ---------------- LLIUREX 21/02/2018 ---------------------------------
function f_rellena_ceros($as_dato) {
	if(strlen($as_dato)>0 && strlen($as_dato)<9){
		for($i=strlen($as_dato); $i<9; $i++)
			$as_dato="0".$as_dato;}
	
	return $as_dato; 
}
// --------------- FIN LLIUREX 21/02/2018 -----------------------------


// gestion des exemplaires

print "<h1>".$msg[290]."</h1>";

// on checke si l'exemplaire n'existe pas déjà
$requete = "SELECT count(1) FROM exemplaires WHERE expl_cb='$noex'";
$res = pmb_mysql_query($requete, $dbh);


// --------------- LLIUREX 21/02/2018 ---------------------------------
$requete2 = "SELECT count(1) FROM exemplaires WHERE expl_cb='".f_rellena_ceros($noex)."' ";
$res2 = pmb_mysql_query($requete2, $dbh);
// añadida condicion de sustitución de blancos por ceros 
// && !mysql_result($res2, 0, 0)

if(((!pmb_mysql_result($res, 0, 0)) && !pmb_mysql_result($res2,0,0))||(($option_num_auto)&&($noex=='') && !pmb_mysql_result($res2,0,0))) {
//-----------------FIN LLIUREX 21/02/2018 -----------------------------	
	$notice = new mono_display($id, 1, './catalog.php?categ=modif&id=!!id!!', FALSE);
	print pmb_bidi("<div class='row'><b>".$notice->header."</b><br />");
	print pmb_bidi($notice->isbd.'</div>');

	// visibilité des exemplaires
	// On ne vérifie que si l'utlisateur peut créer sur au moins une localisation : 
	if (!$pmb_droits_explr_localises||$explr_visible_mod) {
// --------------------- LLIUREX 21/02/2018---------------------------
//f_rellena_ceros()	
		$nex = new exemplaire(f_rellena_ceros($noex), 0, $id);
//---------------------- FIN LLIUREX 21/02/2018---------------------		
		print "<div class='row'>";
		print $nex->expl_form('./catalog.php?categ=expl_update&sub=create', "./catalog.php?categ=isbd&id=$id");
		print "</div>";
	} 
} else {
	error_message($msg[301], $msg[302], 1, "./catalog.php?categ=expl&id=$id");
}
?>