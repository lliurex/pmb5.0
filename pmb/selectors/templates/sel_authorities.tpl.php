<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_authorities.tpl.php,v 1.2.2.1 2017-09-12 13:23:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates des sélecteurs d'autorités

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $add_field, $field_id, $field_name_id;
global $max_field;

$jscript_common_authorities_unique ="
	<script type='text/javascript'>
		<!--
		function set_parent(f_caller, id_value, libelle_value, callback){
			var w = window;
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
				
				if(callback){
					if(typeof w.opener[callback] == 'function'){
	            		w.opener[callback](id_value);
			    	}
			    }
			}	
		}
		-->
	</script>";

// Pour les liens entre autorités
$jscript_common_authorities_link = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value, callback){	
		var w = window;
		n_aut_link=w.opener.document.forms[f_caller].elements['max_aut_link'].value;
		flag = 1;	
		//Vérification que l'autorité n'est pas déjà sélectionnée
		for (i=0; i<n_aut_link; i++) {
			if (w.opener.document.getElementById('f_aut_link_id'+i).value==id_value && w.opener.document.getElementById('f_aut_link_table'+i).value==!!param1!!) {
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
						
			if(callback){
				if(typeof w.opener[callback] == 'function'){
            		w.opener[callback](id_value);
		    	}
		    }
		}	
	}
	-->
	</script>
	";
