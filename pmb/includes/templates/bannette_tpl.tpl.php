<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_tpl.tpl.php,v 1.3.2.1 2017-11-22 16:25:42 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $bannette_tpl_liste;
global $bannette_tpl_liste_ligne;
global $bannette_tpl_form;

// Affichage de la liste des templates de listes de circulation

$bannette_tpl_liste = "
<table width='100%'>
	<tbody>
		<tr>
			<th width='3%'>".$msg["bannette_tpl_id"]."</th>
			<th>".$msg["bannette_tpl_name"]."</th>
			<th>".$msg["bannette_tpl_description"]."</th>
		</tr>
		!!bannette_tpl_liste!!
	</tbody>
</table>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' value='".$msg["bannette_tpl_ajouter"]."' onclick=\"document.location='!!link_ajouter!!'\" type='button'>
	</div>
</div>
";

$bannette_tpl_liste_ligne = "
<tr <tr class='!!pair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair!!'\" style=\"cursor: pointer;\" >
	<td onmousedown=\"document.location='!!link_edit!!';\" align='right'><b>!!id!!</b></td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!name!!</td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!comment!!</td>
</tr>
";

$bannette_tpl_form = "
<script type='text/javascript'>

	function test_form(form) {
		if(form.name.value.length == 0)	{
			alert('".$msg["bannette_tpl_nom_erreur"]."');
			return false;
		}	
		return true;
	}
	
	function confirm_delete() {
	    result = confirm(\"".$msg['confirm_suppr']."\");
	    if(result) {
	        document.location='!!action_delete!!';
		} else
			document.forms['bannette_tpl_form'].elements['name'].focus();
	}
	
	function insert_vars(theselector,dest){	
		var selvars='';
		for (var i=0 ; i< theselector.options.length ; i++){
			if (theselector.options[i].selected){
				selvars=theselector.options[i].value ;
				break;
			}
		}
		if(!selvars) return ;
		
		if(pmbDojo.aceManager.getEditor(dest.id)){
		    pmbDojo.aceManager.getEditor('bannettetpl_tpl').insert(selvars);
		} else {
			if(typeof(tinyMCE)== 'undefined'){
				var start = dest.selectionStart;
			    var start_text = dest.value.substring(0, start);
			    var end_text = dest.value.substring(start);
			    dest.value = start_text+selvars+end_text;
			}else{
				tinyMCE_execCommand('mceInsertContent',false,selvars);
			}
		}
	}	
	
</script>
<script type='text/javascript' src='./javascript/tabform.js'></script>
<form class='form-$current_module' id='bannette_tpl_form' name='bannette_tpl_form' method='post' action='!!action!!' onSubmit=\"return false\" >
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<!--	nom	-->
		<div class='row'>
			<label class='etiquette' for='name'>".$msg["bannette_tpl_name"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
		</div>		
		<!-- 	Commentaire -->
		<div class='row'>
			<label class='etiquette' for='comment'>".$msg["bannette_tpl_description"]."</label>
		</div>
		<div class='row'>
			<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
		</div>					
		<div class='row'>	
			<label class='etiquette' for='bannettetpl_tpl'>".$msg['bannette_tpl_tpl']."</label>!!fields_options!!
			<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options'), document.getElementById('bannettetpl_tpl')); return false; \" value=' ".$msg['bannette_tpl_insert']." ' >			
		</div>
		<div class='row'>
			<textarea type='text' name='bannettetpl_tpl' id='bannettetpl_tpl' class='saisie-50em' rows='20' cols='50' >!!bannettetpl_tpl!!</textarea>
		</div>	
	</div>
	<!--	boutons	-->
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' onClick=\"history.go(-1);\" />
			<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
			!!duplicate!!
			</div>
		<div class='right'>
			!!delete!!
			</div>
		</div>
	<div class='row'></div>
	<input type='hidden' id='id_tpl' name='id_tpl' value='!!id!!' />
</form>
<script src='./javascript/ace/ace.js' type='text/javascript' charset='utf-8'></script>
<script type='text/javascript'>
	document.forms['bannette_tpl_form'].elements['name'].focus();	
 	pmbDojo.aceManager.initEditor('bannettetpl_tpl');
</script>
";

