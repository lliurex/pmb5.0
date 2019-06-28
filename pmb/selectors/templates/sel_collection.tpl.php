<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_collection.tpl.php,v 1.21.2.1 2017-09-12 13:23:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;
global $mode, $p1, $p2, $p3, $p4, $p5, $p6;

if($mode=="un") {
	$jscript = $jscript_common_selector;
} else {
	if ($dyn==3) {
		$jscript = $jscript_common_authorities_unique;
	}elseif ($dyn==2) { // Pour les liens entre autorités
		$jscript = $jscript_common_authorities_link;
	}else {
		$jscript = "
		<script type='text/javascript'>
		<!--
		function set_parent(f_caller, id_coll, libelle_coll, callback, id_ed, libelle_ed){
			window.opener.document.forms[f_caller].elements['$p1'].value = id_ed;
			window.opener.document.forms[f_caller].elements['$p2'].value = reverse_html_entities(libelle_ed);
			window.opener.document.forms[f_caller].elements['$p3'].value = id_coll;
			window.opener.document.forms[f_caller].elements['$p4'].value = reverse_html_entities(libelle_coll);
			window.opener.document.forms[f_caller].elements['$p5'].value = '';
			window.opener.document.forms[f_caller].elements['$p6'].value = '';
		
			window.close();
		}
		-->
		</script>
		";
	}
}

// ------------------------------------------
// 	$collection_form : form saisie collection
// ------------------------------------------
$collection_form = "
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.collection_nom.value.length == 0){
			alert(\"$msg[166]\");
			return false;
		}
		if (form.ed_id.value == 0){
			alert(\"$msg[172]\");
			return false;
		}
		return true;
	}
-->
</script>
<form name='saisie_collection' method='post' action=\"!!base_url!!&action=update\">
<h3>$msg[163]</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette'>$msg[67]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='collection_nom' value=\"!!deb_saisie!!\" >
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[164]</label>
		</div>
	<div class='row'>
		<input type='text' class='saisie-30emr' name='ed_libelle' value='' readonly >
		<input class='bouton_small' type='button' onclick=\"openPopUp('./select.php?what=editeur&caller=saisie_collection&p1=ed_id&p2=ed_libelle&p3=dcoll_id&p4=dcoll_lib&p5=dsubcoll_id&p6=dsubcoll_lib', 'select_ed', $selector_x_size, $selector_y_size, -2, -2, 'toolbar=no, resizable=yes')\" title='$msg[157]' value='$msg[parcourir]' />
		<input type='button' class='bouton_small' value='$msg[raz]' onclick=\"this.form.ed_libelle.value=''; this.form.ed_id.value='0'; \" />
		<input type='hidden' name='ed_id' value='0'>
		<input type='hidden' name='dcoll_id'>
		<input type='hidden' name='dcoll_lib'>
		<input type='hidden' name='dsubcoll_id'>
		<input type='hidden' name='dsubcoll_lib'>
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[165]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='issn' value='' maxlength='12'>
		</div>
	</div>
<div class='row'>
	<input type='button' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!';\">
	<input type='submit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_collection'].elements['collection_nom'].focus();
</script>
";
