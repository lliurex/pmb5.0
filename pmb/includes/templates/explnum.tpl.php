<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum.tpl.php,v 1.42 2017-07-25 12:43:54 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// on teste si des r�pertoires de stockages sont param�tr�s
if (pmb_mysql_num_rows(pmb_mysql_query("select * from upload_repertoire "))==0) $pmb_docnum_in_directory_allow = 0;
else $pmb_docnum_in_directory_allow=1;
// les deux param�tres pour savoir si on peut stocker de la GED sont donc : 
// $pmb_docnum_in_directory_allow
// $pmb_docnum_in_database_allow

// $expl_form :form de saisie/modif exemplaire num�rique
$explnum_form ="
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit('catalog', 'explnum');
	     });
	});
</script>
<script type='text/javascript'>
<!--
	function test_form(form) {
		if((form.f_nom.value.length == 0) && (form.f_fichier.value.length == 0) && (form.f_url.value.length == 0)) {
			alert(\"".$msg['explnum_error_creation']."\");
			return false;
		}
		if((form.f_fichier.value.length != 0) && (form.f_url.value.length == 0) && (document.getElementById('upload').checked==true) && (document.getElementById('id_rep').value==0)) {
			alert(\"".$msg['explnum_error_rep_upload']."\");
			return false;
		}
		
		return check_form();
	}
	
-->

//Test si le fichier est d�j� upload� au meme endroit
function ecraser_fichier(filename){

	var res = confirm(\"".$msg['docnum_ecrase_file']." \"+filename+\".\\n".$msg['agree_question']."\");
	if(res) {
		document.getElementById('f_new_name').value = filename;
		return true;
	}
	document.getElementById('f_new_name').value = '';
	return false;	
	
}

function chklnk_f_url(element){
	if(element.value != ''){
		var wait = document.createElement('img');
		wait.setAttribute('src','images/patience.gif');
		wait.setAttribute('align','top');
		while(document.getElementById('f_url_check').firstChild){
			document.getElementById('f_url_check').removeChild(document.getElementById('f_url_check').firstChild);
		}
		document.getElementById('f_url_check').appendChild(wait);
		var testlink = encodeURIComponent(element.value);
 		var check = new http_request();
		if(check.request('./ajax.php?module=ajax&categ=chklnk',true,'&timeout=0&link='+testlink)){
			alert(check.get_text());
		}else{
			var result = check.get_text();
			var img = document.createElement('img');
			var src='';
			if(result == '200') {
				if((element.value.substr(0,7) != 'http://') && (element.value.substr(0,8) != 'https://')) element.value = 'http://'+element.value;
				//impec, on print un petit message de confirmation
				src = 'images/tick.gif';
			}else{
				//probl�me...
				src = 'images/error.png';
				img.setAttribute('style','height:1.5em;');
			}
			img.setAttribute('src',src);
			img.setAttribute('align','top');
			while(document.getElementById('f_url_check').firstChild){
				document.getElementById('f_url_check').removeChild(document.getElementById('f_url_check').firstChild);
			}
			document.getElementById('f_url_check').appendChild(img);
		}
	}
}
</script>

<script src=\"./javascript/http_request.js\" type='text/javascript'></script>
<script src=\"./javascript/ajax.js\" type='text/javascript'></script>
<script src=\"./javascript/select.js\" type='text/javascript'></script>
<script src=\"./javascript/upload.js\" type='text/javascript'></script>

<form class='form-$current_module' ENCTYPE='multipart/form-data' name='explnum' method='post' action='!!action!!' onsubmit='!!submit_action!!'>
<div class='left'>
	<h3>".$msg['explnum_data_doc']."</h3>
</div>
<div class='right'>";
if ($PMBuserid==1 && $pmb_form_explnum_editables==1){
	$explnum_form.="<input type='button' class='bouton_small' value='".$msg["catal_edit_format"]."' id=\"bt_inedit\"/>";
}
if ($pmb_form_explnum_editables==1) {
	$explnum_form.="<input type='button' class='bouton_small' value=\"".$msg["catal_origin_format"]."\" id=\"bt_origin_format\"/>";
}
$explnum_form.="</div>
<div class='form-contenu' >
	<div id='zone-container'>
		<div id='el0Child_0' class='row' movable='yes' title=\"".htmlentities($msg['explnum_nom'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_nom'>".$msg['explnum_nom']."</label>
			</div>
			<div class='row'>
				<input type='text' id='f_nom' name='f_nom' class='saisie-80em'  value='!!nom!!' />
				<!-- explnum_statut -->
			</div>
		</div>
		<div id='el0Child_1' class='row' movable='yes' title=\"".htmlentities($msg['explnum_vignette'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_vignette'>".$msg['explnum_vignette']."</label>
			</div>
			<div class='row'>
				<input type='file' id='f_vignette' name='f_vignette' class='saisie-80em' size='65' />
			</div>
			<div class='row'>
				<label class='etiquette' for='f_url_vignette'>".$msg['explnum_url_vignette']."</label>
			</div>
			<div class='row'>
				<input type='text' id='f_url_vignette' name='f_url_vignette' class='saisie-80em' />
				<div>!!mimetype_list!!</div>
			</div>
			<div class='row'>
				!!vignette_existante!!
			</div>
		</div>
		<div id='el0Child_2' class='row' movable='yes' title=\"".htmlentities($msg['empr_location'], ENT_QUOTES, $charset)."\">
			!!location_explnum!!
		</div>
		<div id='el0Child_5' class='row' movable='yes' title=\"".htmlentities($msg[651], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_vignette'>".$msg[651]."</label>
			</div>
			<div class='row'>
				!!lenders!!
			</div>
			<div class='row'>&nbsp;</div>
		</div>
";

if ($pmb_docnum_in_directory_allow || $pmb_docnum_in_database_allow) {
	$explnum_form .="
		<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['explnum_fichier'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<label class='etiquette' for='f_fichier'>".$msg['explnum_fichier']."</label>
			</div>
			<div class='row'>
				<input type='file' id='f_fichier' name='f_fichier' class='saisie-80em' size='65' />
				<div><input type='checkbox' id='multi_ck' name='multi_ck' value='1'/><label for='multi_ck'>".$msg['upload_repertoire_multifile']."</label></div>
			</div>
			<div class='row'>
				<!-- !!scan_button!! -->
			</div>
			!!div_upload!!
			<div class='row'>&nbsp;</div>
			<div class='row'>
				<label class='etiquette' >".$msg['explnum_ou']." :</label>
			</div>";

} else {

	$explnum_form .="
		<div id='el0Child_3' class='row' movable='yes' title=\"".htmlentities($msg['explnum_url'], ENT_QUOTES, $charset)."\">
			<div class='row'>
				<i>".$msg['explnum_no_storage_allowed']."</i>
			</div>
			<div class='row'>&nbsp;</div>";
}
$explnum_form.="
			<div class='row'>
				<label class='etiquette' for='f_url'>".$msg['explnum_url']."</label>
			</div>
			<div class='row'>
				<div id='f_url_check' style='display:inline'></div>
				<input type='text' id='f_url' name='f_url' class='saisie-80em' onchange='chklnk_f_url(this);' value='!!url!!' />
			</div>
			<div class='row'>&nbsp;</div>
		</div>
		<div id='el0Child_4' class='row' movable='yes' title=\"".htmlentities($msg['docnum_statut_gestion'], ENT_QUOTES, $charset)."\">
			<div class='row'>
			    <label class='etiquette'>".$msg['docnum_statut_gestion']."</label>
			</div>
			<div class='row'>
				!!statut_list!!
			</div>
			<div class='row'>&nbsp;</div>
		</div>
		<!-- el0Child_5 utilis� par les licences -->
		!!explnum_licence_selectors!!			    		
		
		!!index_concept_form!!
		!!champs_perso!!
		!!ck_indexation!!
		!!ck_diarization!!
		!!fct_conf_diarize_again!!		
		!!rights_form!!
	</div>
</div>
<div class='row'>
	<input type='button' class='bouton' value='".$msg['76']."' onClick=\"history.go(-1);\" />
	<input type='submit' class='bouton' value='".$msg['77']."' onClick=\"return test_form(this.form);\" />
	!!associate_speakers!!
	<div class='right' >
		!!supprimer!!
	</div>
	<input type='hidden' name='f_explnum_id' value='!!explnum_id!!' />
	<input type='hidden' name='f_bulletin' value='!!bulletin!!' />
	<input type='hidden' name='f_notice' value='!!notice!!' />
	<input type='hidden' name='f_new_name' id='f_new_name' value='' />
</div>
</form>
<script type=\"text/javascript\">
	document.forms['explnum'].elements['f_nom'].focus();
	ajax_parse_dom();
</script>";