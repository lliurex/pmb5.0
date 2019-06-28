<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: explnum_licence_right.tpl.php,v 1.1 2017-07-17 13:32:23 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

//statuts de contribution
$admin_explnum_licence_right_form = "<form class='form-$current_module' name='explnumlicencerightform' method=post action=\"./admin.php?categ=docnum&sub=licence&action=settings&id=!!explnum_licence_id!!&what=rights&rightaction=save&rightid=!!id!!\">
<h3><span onclick='menuHide(this,event)'>!!form_title!!</span></h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_right_type'>".$msg["explnum_licence_right_type"]."</label>
		</div>
		<div class='row'>
			<input type='radio' group='right_type' value='1' !!explnum_licence_right_type_1!! name='explnum_licence_right_type' id='explnum_licence_right_type_1'>
			<label class='etiquette' for='explnum_licence_right_type_1'>".$msg["explnum_licence_right_quotation_right_authorisation"]."</label>
			<input type='radio' group='right_type' value='0' !!explnum_licence_right_type_0!! name='explnum_licence_right_type' id='explnum_licence_right_type_0'>
			<label class='etiquette' for='explnum_licence_right_type_0'>".$msg["explnum_licence_right_quotation_right_prohibition"]."</label>
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_right_label'>".$msg["docnum_statut_libelle"]."</label>
		</div>
		<div class='row'>
			<input type=text name='explnum_licence_right_label' value='!!explnum_licence_right_label!!' class='saisie-50em' />
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_right_logo_url'>".$msg["explnum_licence_logo_url"]."</label>
		</div>
		<div class='row'>
			<input type=text name='explnum_licence_right_logo_url' value='!!explnum_licence_right_logo_url!!' class='saisie-50em' />
		</div>
	</div>
	<div class='row'>
		<div class='row'>
			<label class='etiquette' for='explnum_licence_right_explanation'>".$msg["explnum_licence_explanation"]."</label>
		</div>
		<div class='row'>
			<textarea name='explnum_licence_right_explanation'>!!explnum_licence_right_explanation!!</textarea>
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
<script type='text/javascript'>document.forms['explnumlicencerightform'].elements['explnum_licence_right_label'].focus();</script>";