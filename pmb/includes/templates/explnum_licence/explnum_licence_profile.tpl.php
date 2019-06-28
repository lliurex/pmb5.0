<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_profile.tpl.php,v 1.2 2017-07-19 15:43:18 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//statuts de contribution
$admin_explnum_licence_profile_form = "<form class='form-$current_module' name='explnumlicenceprofileform' method=post action=\"./admin.php?categ=docnum&sub=licence&action=settings&id=!!explnum_licence_id!!&what=profiles&profileaction=save&profileid=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette' for='explnum_licence_profile_label'>".$msg["docnum_statut_libelle"]."</label>
	</div>
	<div class='row'>
		<input type=text name='explnum_licence_profile_label' value='!!explnum_licence_profile_label!!' class='saisie-50em' />
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_profile_uri'>".$msg["explnum_licence_uri"]."</label>
		</div>
		<div class='row'>
			<input type=text name='explnum_licence_profile_uri' value='!!explnum_licence_profile_uri!!' class='saisie-50em' />
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_profile_logo_url'>".$msg["explnum_licence_logo_url"]."</label>
		</div>
		<div class='row'>
			<input type=text name='explnum_licence_profile_logo_url' value='!!explnum_licence_profile_logo_url!!' class='saisie-50em' />
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_profile_explanation'>".$msg["explnum_licence_explanation"]."</label>
		</div>
		<div class='row'>
			<textarea name='explnum_licence_profile_explanation'>!!explnum_licence_profile_explanation!!</textarea>
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_profile_quotation_rights'>".$msg["explnum_licence_profile_quotation_rights"]."</label>
		</div>
		<div class='row'>
			<textarea name='explnum_licence_profile_quotation_rights'>!!explnum_licence_profile_quotation_rights!!</textarea>
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_profile_quotation_rights'>".$msg["explnum_licence_profile_linked_rights"]."</label>
		</div>
		<div class='row'>
			!!explnum_licence_profile_linked_rights!!
		</div>
	</div>
	<!-- Boutons -->
	<div class='row'>
		<div class='left'>
			<input class='bouton' type='button' value='". $msg['76'] ."' onClick=\"history.go(-1);\">&nbsp;
			<input class='bouton' type='submit' value='". $msg['77'] ."' onClick=\"return test_form(this.form)\">
		</div>
		<div class='right'>
			!!bouton_supprimer!!
		</div>
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>document.forms['explnumlicenceprofileform'].elements['explnum_licence_profile_label'].focus();</script>";


$admin_explnum_checkbox_template = "
		<input type='checkbox' id='explnum_licence_profile_rights_!!admin_explnum_right_id!!' value='!!admin_explnum_right_id!!' !!admin_explnum_right_checked!! name='explnum_licence_profile_rights[]'/>
		<label for='explnum_licence_profile_rights_!!admin_explnum_right_id!!'>!!admin_explnum_right_label!!</label>
		";
