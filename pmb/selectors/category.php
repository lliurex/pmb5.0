<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: category.php,v 1.21 2017-02-24 16:12:26 dgoron Exp $
//
// Affichage de la zone de recherche et choix du mode de navigation dans les catégories

$base_path="..";                            
$base_auth = "";  
$base_title = "Selection";

require_once ("$base_path/includes/init.inc.php");  

if(!isset($user_input)) $user_input = '';

// modules propres à select.php ou à ses sous-modules
include_once ("$javascript_path/misc.inc.php");
print reverse_html_entities();

// classe pour la gestion des catégories dans le sélecteur
require($base_path."/selectors/classes/selector_category.class.php");

$selector_category = new selector_category(stripslashes($user_input));
$selector_category->proceed();
