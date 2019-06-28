<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: oeuvre_event.inc.php,v 1.7 2017-06-26 15:13:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/authperso.class.php");

// la variable $caller, passée par l'URL, contient le nom du form appelant
$base_url = "./select.php?what=oeuvre_event&authperso_id=$authperso_id&caller=$caller&p1=$p1&p2=$p2&p3=$p3&p4=$p4&p5=$p5&p6=$p6&no_display=$no_display&bt_ajouter=$bt_ajouter&dyn=$dyn&callback=$callback&infield=$infield"
."&max_field=".$max_field."&field_id=".$field_id."&field_name_id=".$field_name_id."&add_field=".$add_field;

include("$base_path/selectors/templates/sel_oeuvre_event.tpl.php");

$authpersos= authpersos::get_oeuvre_event_authpersos();
if (!count($authpersos)){
	print $msg['oeuvre_event_sel_no'];
	exit;
}

if(!$authperso_id)$authperso_id=$authpersos[0]['id'];

if ($deb_rech) $f_user_input = $deb_rech ;
$user_input=$f_user_input;
if($bt_ajouter == "no"){
	$bouton_ajouter="";
} else {
	$bouton_ajouter= "<input type='button' class='bouton_small' onclick=\"document.location='$base_url&action=add&deb_rech='+this.form.f_user_input.value\" value='".$msg["authperso_sel_add"]."'>";
}

$sel_authpersos = '';
if (count($authpersos)>1) {
	$sel_authpersos = "<select class='saisie-20em' id='authperso_id' name='authperso_id' onchange = \"this.form.submit()\">";
	foreach($authpersos as $authperso) {
		$sel_authpersos.= "<option value='".$authperso['id']."' "; ;
		if ($authperso_id == $authperso['id']) $sel_authpersos.= " selected";
		$sel_authpersos.= ">".htmlentities($authperso['name'],ENT_QUOTES,$charset)."</option>";
	}
	$sel_authpersos.= "</select>&nbsp;";
}

// affichage des membres de la page
switch($action){
	case 'add':
		$authperso = new authperso($authperso_id);
		print $authperso->get_form_select(0,$base_url);		
		break;
	case 'update':
		print $sel_header;
		$authperso = new authperso($authperso_id);
		$id=$authperso->update_from_form();	
		if($authperso->get_cp_error_message()){
			print '<span class="erreur">'.$authperso->get_cp_error_message().'</span>';
		}	
		$user_input=$f_user_input=authperso::get_isbd($id);
		$sel_search_form=str_replace("!!sel_authpersos!!",$sel_authpersos,$sel_search_form);
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($dbh, $f_user_input, $nbr_lignes, $page, $id);
		break;
	default:
		print $sel_header;
		$sel_search_form=str_replace("!!sel_authpersos!!",$sel_authpersos,$sel_search_form);
		$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		$sel_search_form = str_replace("!!deb_rech!!", htmlentities(stripslashes($f_user_input),ENT_QUOTES,$charset), $sel_search_form);
		print $sel_search_form;
		print $jscript;
		show_results($dbh, $f_user_input, $nbr_lignes, $page);
		break;
}

function show_results($dbh, $user_input, $nbr_lignes=0, $page=0, $id=0) {
	global $nb_per_page;
	global $base_url;
	global $caller;
	global $callback;
	global $msg;
	global $charset;
	global $no_display ;
	global $authperso_id;

	$base_url = $base_url."&f_user_input=".rawurlencode(stripslashes($user_input));
	$authperso=new authperso($authperso_id);
	print $authperso->get_list_selector($id);
}

print $sel_footer;