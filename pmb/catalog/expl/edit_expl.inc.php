<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: edit_expl.inc.php,v 1.40 2016-09-05 14:07:28 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");


// gestion des exemplaires
print "<h1>".$msg["4008"]."</h1>";

$notice = new mono_display($id, 1, './catalog.php?categ=modif&id=!!id!!', FALSE, '', '', '', 0, 0, 0, '', 0, false, true, 0, 0, 0, 0);
print pmb_bidi("<div class='row'><b>".$notice->header."</b><br />");
print pmb_bidi($notice->isbd."</div>");
$nex = new exemplaire($cb, $expl_id,$id);

// visibilité des exemplaires
// $nex->explr_acces_autorise contient INVIS, MODIF ou UNMOD

if ($nex->explr_acces_autorise!="INVIS") {
	
	print "<div class='row'>";
	print $nex->expl_form("./catalog.php?categ=expl_update&sub=update&org_cb=".urlencode($cb)."&expl_id=".$expl_id, "./catalog.php?categ=isbd&id=$id");
	print "</div>";
} else {
	print "<div class='row'><div class='colonne10'><img src='./images/error.png' /></div>";
	print "<div class='colonne-suite'><span class='erreur'>".$msg["err_mod_expl"]."</span>&nbsp;&nbsp;&nbsp;";
	print "<input type='button' class='bouton' value=\"${msg['bt_retour']}\" name='retour' onClick='history.back(-1);'></div></div>";	
}
	