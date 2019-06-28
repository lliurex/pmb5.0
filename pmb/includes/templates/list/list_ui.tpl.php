<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_ui.tpl.php,v 1.3 2017-03-02 13:13:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//	------------------------------------------------------------------------------
//	$list_ui_search_form_tpl : template de recherche pour les listes
//	------------------------------------------------------------------------------

$list_ui_search_form_tpl = "
<script src='javascript/ajax.js'></script>
<form class='form-".$current_module."' id='!!objects_type!!_search_form' name='!!objects_type!!_search_form' method='post' action=\"!!action!!\" >
	<h3>!!form_title!!</h3>
	<!--    Contenu du form    -->
	<div class='form-contenu'>
		!!list_search_content_form_tpl!!
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				<input type='hidden' id='!!objects_type!!_json_filters' name='!!objects_type!!_json_filters' value='!!json_filters!!' />
				<input type='hidden' id='!!objects_type!!_page' name='!!objects_type!!_page' value='!!page!!' />
				<input type='hidden' id='!!objects_type!!_nb_per_page' name='!!objects_type!!_nb_per_page' value='!!nb_per_page!!' />
				<input type='hidden' id='!!objects_type!!_pager' name='!!objects_type!!_pager' value='!!pager!!' />
				<input type='submit' class='bouton' value='".$msg['search']."' />&nbsp;
			</div>
			<div class='right'>
				!!export_icons!!
			</div>
		</div>
		<div class='row'></div>
	</div>
</form>
<div class='row'>
	<span id='!!objects_type!!_messages' class='erreur'>!!messages!!</span>
</div>
<script type='text/javascript'>
	ajax_parse_dom();
</script>
";

$list_ui_js_sort_script_sort = "
	<script type='text/javascript'>
		function !!objects_type!!_sort_by(criteria, asc_desc, indice) {
			var url = './ajax.php?module=".$current_module."&categ=!!categ!!&sub=!!sub!!&sort_by='+criteria;
			if(asc_desc == 'desc') {
				//on repasse en tri croissant
				url += '&sort_asc_desc=asc';
			} else if(asc_desc == 'asc') {
				//on repasse en tri décroissant
				url += '&sort_asc_desc=desc';
			}
			var req = new http_request();
			if(document.getElementById('!!objects_type!!_json_filters_'+indice)) {
				var filters = document.getElementById('!!objects_type!!_json_filters_'+indice).value;
			} else {
				var filters = document.getElementById('!!objects_type!!_json_filters').value;
			}
			if(document.getElementById('!!objects_type!!_pager_'+indice)) {
				var pager = document.getElementById('!!objects_type!!_pager_'+indice).value;
			} else {
				var pager = document.getElementById('!!objects_type!!_pager').value;
			}
			req.request(url,1, 'filters='+filters+'&pager='+pager);
			if(document.getElementById('!!objects_type!!_list_'+indice)) {
				var table = document.getElementById('!!objects_type!!_list_'+indice);
			} else {
				var table = document.getElementById('!!objects_type!!_list_0');
			}
			table.innerHTML = req.get_text();
		}
	</script>
";