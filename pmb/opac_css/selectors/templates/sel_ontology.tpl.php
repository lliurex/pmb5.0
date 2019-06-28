<?php
// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_ontology.tpl.php,v 1.1.2.5 2018-02-15 16:08:08 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

global $dyn,$jscript,$infield,$msg,$sel_search_form,$list_form,$element_form,$list_range_links_form,$range_link_form, $p1, $p2;

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
if($dyn==1){
	$jscript = "
	<script type='text/javascript'>
	
	function set_parent(f_caller, element, id_value, libelle_value, type_value, callback)
	{
		var w = parent;
		n_concept=w.opener.document.forms[f_caller].elements[element+'_new_order'].value*1+1;
		flag = 1;
		var order_concept = new Array();
	
		//Vérification que la catégorie n'est pas déjà sélectionnée
		for (i=0; i<n_concept; i++) {
			order_concept[i] = i;
			if (w.opener.document.getElementById(element+'_'+i+'_value') && w.opener.document.getElementById(element+'_'+i+'_value').value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}
	
		if (flag) {
			var order = 0;
			
			if (w.opener.document.getElementById('tab_concept_order')) {
				order_concept = w.opener.document.getElementById('tab_concept_order').value.split(',');
			}
			for (var i in order_concept) {
				if (w.opener.document.getElementById(element+'_'+order_concept[i]+'_value') && !w.opener.document.getElementById(element+'_'+order_concept[i]+'_value').value) {
					order = order_concept[i];
					flag = 0;
					break;
				}
			}
			if (flag) {
				if(typeof w.opener.onto_add_selector == 'function'){
					w.opener.onto_add_selector(element, 0);
				}else{
					w.opener.onto_add(element, 0);		
				}
				
				order = w.opener.document.getElementById(element+'_new_order').value;
			}
			
			w.opener.document.getElementById(element+'_'+order+'_value').value = id_value;
			w.opener.document.getElementById(element+'_'+order+'_display_label').value = reverse_html_entities(libelle_value);
			w.opener.document.getElementById(element+'_'+order+'_type').value = type_value;
		}
	}
	
	</script>
	";
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, element, id_value, libelle_value, type_value, callback)
	{	
		w=window;
		n_aut_link=w.opener.document.forms[f_caller].elements['max_aut_link'].value;
		flag = 1;	
		//Vérification que l'autorité n'est pas déjà sélectionnée
		for (i=0; i<n_aut_link; i++) {
			if (w.opener.document.getElementById('f_aut_link_id'+i).value==id_value && w.opener.document.getElementById('f_aut_link_table'+i).value==$p1) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}	
		if (flag) {
			for (i=0; i<n_aut_link; i++) {
				if ((w.opener.document.getElementById('f_aut_link_id'+i).value==0)||(w.opener.document.getElementById('f_aut_link_id'+i).value=='')) break;
			}	
			if (i==n_aut_link) w.opener.add_aut_link();
			
			var selObj = w.opener.document.getElementById('f_aut_link_table_list');
			var selIndex=selObj.selectedIndex;
			w.opener.document.getElementById('f_aut_link_table'+i).value= selObj.options[selIndex].value;
			
			w.opener.document.getElementById('f_aut_link_id'+i).value = id_value;
			w.opener.document.getElementById('f_aut_link_libelle'+i).value = reverse_html_entities('['+selObj.options[selIndex].text+']'+libelle_value);		
		}	
	}
	-->
	</script>
	";
}elseif ($dyn==3) { // aut_pperso
	$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, element, id_value, libelle_value, type_value, callback)
	{	
 		w=window;
		//début Copier/Coller depuis le template sel_editeur.tpl.php
		var i=0;
		if(!(typeof w.opener.$add_field == 'function')) {
			w.opener.document.getElementById('$field_id').value = id_value;
			w.opener.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
			parent.parent.close();
			return;
		}
		var n_element=w.opener.document.forms[f_caller].elements['$max_field'].value;
		var flag = 1;
		
		//Vérification que l'élément n'est pas déjà sélectionnée
		for (var i=0; i<n_element; i++) {
			if (w.opener.document.getElementById('$field_id'+i).value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}
		if (flag) {
			for (var i=0; i<n_element; i++) {
				if ((w.opener.document.getElementById('$field_id'+i).value==0)||(w.opener.document.getElementById('$field_id'+i).value=='')) break;
			}
			if (i==n_element) w.opener.$add_field();
			w.opener.document.getElementById('$field_id'+i).value = id_value;
			w.opener.document.getElementById('$field_name_id'+i).value = reverse_html_entities(libelle_value);
		}	
		//fin Copier/Coller

// Ce bloc là, il était commité, mais on ne peut pas changé de terme une fois sélectionné!
// 		var n_aut = eval('w.opener.document.'+f_caller+'.n_".$p1.".value');							
// 		flag = 1;	
// 		//Vérification que l'autorité n'est pas déjà sélectionnée		
// 		for (var i=0; i<n_aut; i++) {
// 			if (w.opener.document.getElementById('".$p1."_'+i) && w.opener.document.getElementById('".$p1."_'+i).value==id_value) {
// 				alert('".$msg["term_already_in_use"]."');
// 				flag = 0;
// 				break;
// 			}
// 		}	
// 		if (flag) {
// 			for (var i=0; i<n_aut; i++) {
// 				if ((w.opener.document.getElementById('".$p1."_'+i).value==0)||(w.opener.document.getElementById('".$p1."_'+i).value=='')) break;
// 			}	
// 			if (i==n_aut) w.opener.add_".$p1."();			
// 			window.opener.document.forms[f_caller].elements['".$p1."_'+i].value = id_value;
// 			window.opener.document.forms[f_caller].elements['".$p2."_'+i].value = reverse_html_entities(libelle_value);
// 		}	
	}
	-->
	</script>
	";
}elseif ($dyn==4) {
	//Recherche multi-critères
	$jscript = "
		<script type='text/javascript'>
			function set_parent(f_caller, element, id_value, libelle_value, type_value, callback){
				var p1 = '$p1';
				var p2 = '$p2';
				//on enlève le dernier _X
				var tmp_p1 = p1.split('_');
				var tmp_p1_length = tmp_p1.length;
				tmp_p1.pop();
				var p1bis = tmp_p1.join('_');
				
				var tmp_p2 = p2.split('_');
				var tmp_p2_length = tmp_p2.length;
				tmp_p2.pop();
				var p2bis = tmp_p2.join('_');
			
				var max_aut = window.opener.document.getElementById(p1bis.replace('id','max_aut'));
				if(max_aut){
					var trouve=false;
					var trouve_id=false;
					for(i_aut=0;i_aut<=max_aut.value;i_aut++){
						if(window.opener.document.getElementById(p1bis+'_'+i_aut).value==0){
							window.opener.document.getElementById(p1bis+'_'+i_aut).value=id_value;
							window.opener.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
							trouve=true;
							break;
						}else if(window.opener.document.getElementById(p1bis+'_'+i_aut).value==id_value){
							trouve_id=true;
						}
					}
					if(!trouve && !trouve_id){
						window.opener.add_line(p1bis.replace('_id',''));
						window.opener.document.getElementById(p1bis+'_'+i_aut).value=id_value;
						window.opener.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
					}
					if(callback)
						window.opener[callback](p1bis.replace('_id','')+'_'+i_aut);
				}else{
					window.opener.document.forms[f_caller].elements['".$p1."'].value = id_value;
					window.opener.document.forms[f_caller].elements['".$p2."'].value = reverse_html_entities(libelle_value);
					if(callback)
						window.opener[callback]('$infield');
					window.close();
				}
			}
		</script>	
	";
}else{
	// Pour les vedettes composées
	$jscript = "
	<script type='text/javascript'>
		function set_parent(f_caller, element, id_value, libelle_value, type_value, callback){
			window.opener.document.forms[f_caller].elements['".$p1."'].value = id_value;
			window.opener.document.forms[f_caller].elements['".$p2."'].value = reverse_html_entities(libelle_value);
			if(callback)
				window.opener[callback]('$infield');
			window.close();
		}
	</script>
	";
}


//-------------------------------------------
//	$sel_search_form : module de recherche
//-------------------------------------------
$sel_search_form ="
<form name='search_form' method='post' action='!!base_url!!'>
	<input type='text' name='deb_rech' value=\"!!deb_rech!!\">
	&nbsp;
	<input type='submit' class='bouton_small' value='".$msg['142']."' />
</form>
<script type='text/javascript'>
	if(document.forms['search_form'].elements['deb_rech']){
		document.forms['search_form'].elements['deb_rech'].focus();
	}
</script>
<hr />
";

$sel_no_available_search_form = "<hr />
<div class='row'>".$msg['onto_selector_no_search_available']."&nbsp;<input type='button' class='bouton' value='!!add_button_label!!' onclick='!!add_button_onclick!!'/></div>	
<hr />";

$list_form = "
		<div class='row'>
			<script type='javascript' src='./javascript/sorttable.js'></script>
			<table class='sorttable'>
				<tr>
					<th>".htmlentities($msg['103'],ENT_QUOTES,$charset)."</th>
				</tr>
				!!elements_form!!
			</table>
		

			!!aff_pagination!!
		</div>";

$element_form = "
				<tr>
					<td><a href='#' onclick=\"set_parent('!!caller!!', '!!element!!', '!!id!!', '!!item!!', '!!range!!', '!!callback!!')\">!!item_libelle!!</a></td>
				</tr>";

$list_range_links_form = "
		<div class='hmenu'>
			!!range_links_form!!
		</div>";

$range_link_form = "
		<span !!class!! >
			<a href='!!href!!'>!!libelle!!</a>
		</span>";