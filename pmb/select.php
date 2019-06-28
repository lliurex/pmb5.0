<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: select.php,v 1.40.2.2 2017-10-04 13:00:11 ngantier Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "";
$base_use_dojo=1;
//Cas spécial pour les catégories
if ($_GET["what"]=="categorie") {
	$base_nobody=1;
} else {
	$base_title = "Selection";
}


require_once ("$base_path/includes/init.inc.php");  
require_once("$class_path/marc_table.class.php");
require_once("$class_path/analyse_query.class.php");

// modules propres à select.php ou à ses sous-modules
include_once ("$javascript_path/misc.inc.php");
require_once ("$base_path/includes/shortcuts/shortcuts.php");

//initialisation des variables communes
if(!isset($field_id)) $field_id = '';
if(!isset($field_name_id)) $field_name_id = '';
if(!isset($dyn)) $dyn = '';
if(!isset($max_field)) $max_field = '';
if(!isset($add_field)) $add_field = '';
if(!isset($user_input)) $user_input = '';
if(!isset($infield)) $infield = '';
if(!isset($nbr_lignes)) $nbr_lignes = 0;
if(!isset($page)) $page = 0;
if(!isset($no_display)) $no_display = 0;
if(!isset($bt_ajouter)) $bt_ajouter = '';
if(!isset($deb_rech)) $deb_rech = '';
if(!isset($p1)) $p1 = '';
if(!isset($p2)) $p2 = '';
if(!isset($p3)) $p3 = '';
if(!isset($p4)) $p4 = '';
if(!isset($p5)) $p5 = '';
if(!isset($p6)) $p6 = '';
if(!isset($param1)) $param1 = '';
if(!isset($param2)) $param2 = '';
if(!isset($param3)) $param3 = '';
if(!isset($f_user_input)) $f_user_input = '';

require_once($base_path."/selectors/templates/sel_common.tpl.php");

require_once($class_path."/user.class.php");
if(!$nb_per_page) {
	$nb_per_page = user::get_param($PMBuserid, 'nb_per_page_select');
}

// classes pour la gestion des sélecteurs
if(!isset($autoloader) || !is_object($autoloader)){
	require_once($class_path."/autoloader.class.php");
	$autoloader = new autoloader();
}
$autoloader->add_register("selectors_class",true);

print "<script type='text/javascript'>
	self.focus();
	</script>";
print reverse_html_entities();

switch($what) {
	case 'editeur':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		include('./selectors/editeur.inc.php');
		break;
	case 'collection':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		include('./selectors/collection.inc.php');
		break;
	case 'subcollection':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		include('./selectors/subcollection.inc.php');
		break;
	case 'auteur':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		include('./selectors/author.inc.php');
		break;
	case 'country':
		include('./selectors/country.inc.php');
		break;
	case 'lang':
		include('./selectors/lang.inc.php');
		break;
	case 'function':
		include('./selectors/func.inc.php');
		break;
	case 'categorie':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form" || !(SESSrights & THESAURUS_AUTH)){
			$bt_ajouter ="no";
		}
		include('./selectors/category_frame.inc.php');
		break;
	case 'serie':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		include('./selectors/serie.inc.php');
		break;
	case 'indexint':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		include('./selectors/indexint.inc.php');
		break;
	case 'calendrier':
		include ('./selectors/calendrier.inc.php');
		break;
	case 'emprunteur':
		include ('./selectors/empr.inc.php');
		break;
	case 'notice':
		include ('./selectors/notice.inc.php');
		break;
	case 'perio':
		include ('./selectors/perio.inc.php');
		break;
	case 'bulletin':
		include ('./selectors/bulletin.inc.php');
		break;		
	case 'codepostal':
		include ('./selectors/codepostal.inc.php');
		break;
	case 'perso':
		include('./selectors/perso.inc.php');
		break;
	case 'fournisseur':
		include('./selectors/fournisseur.inc.php');
		break;
	case 'coord' :
		include('./selectors/coordonnees.inc.php');
		break;
	case 'acquisition_notice':
		include('./selectors/acquisition_notice.inc.php');
		break;
	case 'types_produits':
		include('./selectors/types_produits.inc.php');
		break;
	case 'rubriques':
		include('./selectors/rubriques.inc.php');
		break;
	case 'origine':
		include('./selectors/origine.inc.php');
		break;		
	case 'synonyms':
		include('./selectors/sel_word.inc.php');
		break;	
	case 'titre_uniforme':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		include('./selectors/titre_uniforme.inc.php');
		break;
	case 'notes':
		include('./selectors/notes.inc.php');
		break;
	case 'ontology' :
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form" || !(SESSrights & CONCEPTS_AUTH)){
			$bt_ajouter = "no";
		}
		include('./selectors/ontology.inc.php');
		break;
	case 'ontologies' :
		include('./selectors/ontologies.inc.php');
		break;
	case 'authperso' :
		include('./selectors/authperso.inc.php');
		break;
	case 'abts' :
		include('./selectors/abts.inc.php');
		break;
	case 'groupexpl' :
		include('./selectors/groupexpl.inc.php');
		break;
	case 'oeuvre_event' :
		include('./selectors/oeuvre_event.inc.php');
		break;
	case 'music_key' :
		include('./selectors/music_key.inc.php');
		break;
	case 'music_form' :
		include('./selectors/music_form.inc.php');
		break;
	case 'bulletins':
		include ('./selectors/bulletins.inc.php');
		break;		
	case 'vedette':
		include ('./selectors/vedette.inc.php');
		break;	
	case 'commande':
		include ('./selectors/commande.inc.php');
		break;
	case 'query_list':
		$selector_query_list = new selector_query_list(stripslashes($user_input));
		$selector_query_list->set_search_xml_file($search_xml_file);
		$selector_query_list->set_search_field_id($search_field_id);
		$selector_query_list->proceed();
		break;
	case 'list':
		$selector_list = new selector_list(stripslashes($user_input));
		$selector_list->set_search_xml_file($search_xml_file);
		$selector_list->set_search_field_id($search_field_id);
		$selector_list->proceed();
		break;
	case 'marc_list':
		$selector_marc_list = new selector_marc_list(stripslashes($user_input));
		$selector_marc_list->set_search_xml_file($search_xml_file);
		$selector_marc_list->set_search_field_id($search_field_id);
		$selector_marc_list->proceed();
		break;
	default:
		print "<script type='text/javascript'>
			window.close();
		</script>";
		break;
}

pmb_mysql_close($dbh);
