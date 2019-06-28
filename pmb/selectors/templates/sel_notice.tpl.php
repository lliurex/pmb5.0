<?php
// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_notice.tpl.php,v 1.15 2017-01-19 10:25:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur notices

//-------------------------------------------
//	$sel_header : header
//-------------------------------------------
$sel_header = "
<div class='row'>
	<label for='titre_select_notice' class='etiquette'>$msg[selector_notice]</label>
	</div>
<div class='row'>
";


if ($dyn==1) {
	//-------------------------------------------
	//	$jscript : script de m.a.j. du parent
	//-------------------------------------------
	$jscript = $jscript_common_selector_simple;
	$jscript = str_replace('!!param1!!', $param1, $jscript);
	$jscript = str_replace('!!param2!!', $param2, $jscript);
	$jscript = str_replace('!!infield!!', $param1, $jscript);
	$jscript .= "
		<script type='text/javascript'>
			function copier_modele(location){
				window.opener.location.href = location;
				window.close();
			}
		</script>";
}else if ($dyn == 3 ){	
	$jscript ="
		<script type='text/javascript'>
			function set_parent(f_caller, id_value, libelle_value,callback){
				var i=0;
				if(!(typeof window.opener.$add_field == 'function')) {
					window.opener.document.getElementById('$field_id').value = id_value;
					window.opener.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
					parent.parent.close();
					return;
				}
				var n_element=window.opener.document.forms[f_caller].elements['$max_field'].value;
				var flag = 1;
				var multiple=1;
			
				//Vérification que l'élément n'est pas déjà sélectionnée
				for (var i=0; i<n_element; i++) {
					if (window.opener.document.getElementById('$field_id'+i).value==id_value) {
						alert('".$msg["term_already_in_use"]."');
						flag = 0;
						break;
					}
				}
				if (flag) {
					for (var i=0; i<n_element; i++) {							
						if ((window.opener.document.getElementById('$field_id'+i).value==0)||(window.opener.document.getElementById('$field_id'+i).value=='')) {
							break;
						}
					}
					if (i==n_element && (typeof window.opener.$add_field == 'function')) {
						window.opener.$add_field();
					}
					window.opener.document.getElementById('$field_id'+i).value = id_value;
					window.opener.document.getElementById('$field_name_id'+i).value = reverse_html_entities(libelle_value);
				}
			}
		</script>";
} else {
	$jscript = $jscript_common_selector_simple;
	$jscript = str_replace('!!param1!!', $param1, $jscript);
	$jscript = str_replace('!!param2!!', $param2, $jscript);
	$jscript = str_replace('!!infield!!', $infield, $jscript);
	$jscript .= "
		<script type='text/javascript'>
			function copier_modele(location){
				window.opener.location.href = location;
				window.close();
			}
		</script>";
}

//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='$base_url'>
<input type='text' name='f_user_input' value=\"!!deb_rech!!\">
	<select id='typdoc-query' name='typdoc_query'>
		!!typdocfield!!
	</select>";
if ($pmb_show_notice_id) {
	$sel_search_form .="<br>".$msg['notice_id_libelle']." <input type='text' name='id_restrict' value=\"!!id_restrict!!\" class='saisie-5em'>";
} else {
	$sel_search_form .="<input type='hidden' name='id_restrict' value=''>";
}
$sel_search_form .="&nbsp;
<input type='submit' class='bouton_small' value='".$msg['142']."' />
</form>
<script type='text/javascript'>
<!--
	document.forms['search_form'].elements['f_user_input'].focus();
-->
</script>
<hr />
";

//-------------------------------------------
//	$sel_footer : footer
//-------------------------------------------
$sel_footer = "
</div>
";
