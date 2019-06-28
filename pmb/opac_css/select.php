<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: select.php,v 1.11.2.3 2017-09-15 08:46:38 dgoron Exp $

// définition du minimum nécéssaire 
$base_path=".";

require_once($base_path."/includes/init.inc.php");
require_once($base_path."/includes/error_report.inc.php") ;
require_once($base_path."/includes/global_vars.inc.php");
require_once($base_path.'/includes/opac_config.inc.php');
	
// récupération paramètres MySQL et connection à la base
require_once($base_path.'/includes/opac_db_param.inc.php');
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();

require_once($base_path."/includes/misc.inc.php");

//Sessions !! Attention, ce doit être impérativement le premier include (à cause des cookies)
require_once($base_path."/includes/session.inc.php");
require_once($base_path.'/includes/start.inc.php');

require_once($base_path."/includes/check_session_time.inc.php");

// récupération localisation
require_once($base_path.'/includes/localisation.inc.php');

// version actuelle de l'opac
require_once($base_path.'/includes/opac_version.inc.php');

// fonctions de gestion de formulaire
require_once($base_path.'/includes/javascript/form.inc.php');

require_once($base_path.'/includes/templates/common.tpl.php');
require_once($base_path.'/includes/divers.inc.php');
// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

require_once($base_path.'/includes/marc_tables/'.$pmb_indexation_lang.'/empty_words');

require_once($base_path."/includes/rec_history.inc.php");

if($what != 'categorie') {
	print $popup_header;
}

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

//initialisation des variables communes
if(!isset($field_id)) $field_id = '';
if(!isset($field_name_id)) $field_name_id = '';
if(!isset($dyn)) $dyn = '';
if(!isset($max_field)) $max_field = '';
if(!isset($add_field)) $add_field = '';
if(!isset($user_input)) $user_input = '';
if(!isset($infield)) $infield = '';

require_once($base_path."/selectors/templates/sel_common.tpl.php");

// classes pour la gestion des sélecteurs
if(!isset($autoloader) || !is_object($autoloader)){
	require_once($class_path."/autoloader.class.php");
	$autoloader = new autoloader();
}
$autoloader->add_register("selectors_class",true);

//L'usager a demandé à voir plus de résultats dans sa liste paginée
if(!isset($nb_per_page) || !$nb_per_page) {
	if(isset($nb_per_page_custom) && $nb_per_page_custom*1) {
 		$nb_per_page = $nb_per_page_custom;
	} else {
		$nb_per_page = 10;
	}
}

print "<script type='text/javascript'>
	self.focus();
</script>";

switch($what) {
	case 'editeur':
		$bt_ajouter ="no";
		$selector_instance = new selector_publisher(stripslashes($user_input));
		break;
	case 'collection':
		$bt_ajouter ="no";
		$selector_instance = new selector_collection(stripslashes($user_input));
		break;
	case 'subcollection':
		$bt_ajouter ="no";
		$selector_instance = new selector_subcollection(stripslashes($user_input));
		break;
	case 'auteur':
		$bt_ajouter ="no";
		$selector_instance = new selector_author(stripslashes($user_input));
		break;
	case 'country':
		$selector_instance = new selector_country(stripslashes($user_input));
		break;
	case 'lang':
		$selector_instance = new selector_lang(stripslashes($user_input));
		break;
	case 'function':
		$selector_instance = new selector_func(stripslashes($user_input));
		break;
	case 'categorie':
		$bt_ajouter ="no";
		if(!isset($perso_id)) $perso_id = '';
		if(!isset($no_display)) $no_display = '';
		if(!isset($bt_ajouter)) $bt_ajouter = '';
		if(!isset($dyn)) $dyn = '';
		if(!isset($keep_tilde)) $keep_tilde = '';
		if(!isset($parent)) $parent = '';
		if(!isset($id2)) $id2 = '';
		if(!isset($callback)) $callback = '';
		if(!isset($htmlfieldstype)) $htmlfieldstype = '';
		if(!isset($id_thes_unique)) $id_thes_unique = '';
		if(!isset($autoindex_class)) $autoindex_class = '';
		
		$base_query = "caller=$caller&p1=$p1&p2=$p2&perso_id=$perso_id&no_display=$no_display&bt_ajouter=$bt_ajouter&dyn=$dyn&keep_tilde=$keep_tilde&parent=$parent&id2=$id2&deb_rech=".rawurlencode(stripslashes($deb_rech))."&callback=".$callback."&infield=".$infield
		."&max_field=".$max_field."&field_id=".$field_id."&field_name_id=".$field_name_id."&add_field=".$add_field."&id_thes_unique=".$id_thes_unique."&autoindex_class=$autoindex_class&htmlfieldstype=$htmlfieldstype";
		
		print "
			<script>self.focus();</script>
			<frameset rows='135,*' border=0>
				<frame name='category_search' src='./selectors/category.php?".$base_query."' />
				<frame name='category_browse' src='' />
			</frameset>";
		break;
	case 'serie':
		$bt_ajouter ="no";
		$selector_instance = new selector_serie(stripslashes($user_input));
		break;
	case 'indexint':
		$bt_ajouter ="no";
		$selector_instance = new selector_indexint(stripslashes($user_input));
		break;
	case 'calendrier':
		require_once('./selectors/calendrier.inc.php');
		break;
	case 'titre_uniforme':
		$bt_ajouter ="no";
		$selector_instance = new selector_titre_uniforme(stripslashes($user_input));
		break;
	case 'music_key' :
		$selector_instance = new selector_music_key(stripslashes($user_input));
		break;
	case 'music_form' :
		$selector_instance = new selector_music_form(stripslashes($user_input));
		break;
	case 'query_list':
		$selector_instance = new selector_query_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'list':
		$selector_instance = new selector_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'marc_list':
		$selector_instance = new selector_marc_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'authperso' :
		require("./selectors/classes/selector_authperso.class.php");		
		$selector_instance = new selector_authperso(stripslashes($user_input));
		break;
	case 'ontology' :
		if (!isset($range)) $range = 0;
		if (!isset($page)) $page = 1;
		
		if(isset($parent_id) && $parent_id){
			$deb_rech= "";
		}
		
		$base_url = selector_ontology::get_base_url();
		
		require_once($class_path."/autoloader.class.php");
		$autoloader = new autoloader();
		$autoloader->add_register("onto_class",true);
		$selector_instance = new selector_ontology(stripslashes($deb_rech));
		break;
	default:
		print "<script type='text/javascript'>
			window.close();
			</script>";
		break;
}
if(isset($selector_instance) && is_object($selector_instance)) {
	$selector_instance->proceed();
}

if($what != 'categorie') {
	print $popup_footer;
}

pmb_mysql_close($dbh);
