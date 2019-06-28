<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collection.inc.php,v 1.33 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classe pour la gestion des collections dans le sélecteur
require("./selectors/classes/selector_collection.class.php");

$rech_regexp = 0 ;

$selector_collection = new selector_collection(stripslashes($user_input));
$selector_collection->proceed();
