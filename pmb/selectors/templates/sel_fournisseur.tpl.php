<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_fournisseur.tpl.php,v 1.14 2017-01-19 10:25:19 dgoron Exp $


if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur fournisseurs

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_fourn' class='etiquette'>".htmlentities($msg['acquisition_sel_fou'], ENT_QUOTES, $charset)."</label>
	</div>
<div class='row'>
";

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, raison, adresse){
	//var temp = window.opener.document.forms[f_caller].elements['$param1'].value;
	window.opener.document.forms[f_caller].elements['$param1'].value = id_value;
	window.opener.document.forms[f_caller].elements['$param2'].value = reverse_html_entities(raison);
	try {
		window.opener.document.forms[f_caller].elements['$param3'].value = reverse_html_entities(adresse);
		} catch (err){}
	window.close();
}
-->
</script>
";

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
<input type='text' name='f_user_input' value=\"!!deb_rech!!\">
&nbsp;
<input type='submit' class='bouton_small' value='$msg[142]' />&nbsp;
!!bouton_ajouter!!
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
";

// ------------------------------------------
// 	$fournisseur_form : form saisie fournisseur
// ------------------------------------------
$fournisseur_form = "
<script type='text/javascript'>
	function test_form(form) {
		if(form.raison.value.length == 0) {
			alert(\"".$msg['acquisition_raison_soc_vide']."\");
			return false;
		}
		return true;
	}
</script>
<form name='saisie_fournisseur' method='post' action=\"$base_url&action=update\">
	<h3>".$msg['acquisition_ajout_fourn']."</h3>
	<div class='form-contenu'>
		<div class='row'>
			!!sel_bibli!!
		</div>
		<div class='row'>
				<label class='etiquette' for='raison'>".htmlentities($msg['acquisition_raison_soc'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type=text id='raison' name='raison' value=\"!!deb_saisie!!\" class='saisie-50em' />
		</div>
		<div class='row'>
				<label class='etiquette' for='num_cp'>".htmlentities($msg['acquisition_num_cp_client'],ENT_QUOTES,$charset)."</label>
		</div>
		<div class='row'>
			<input type=text id='num_cp' name='num_cp' value=\"\" class='saisie-25em' />
		</div>
	</div>
	<div class='row'>
		<input type='button' class='bouton_small' value='$msg[76]' onClick=\"document.location='$base_url';\">
		<input type='submit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_fournisseur'].elements['raison'].focus();
</script>
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
