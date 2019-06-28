<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: country.inc.php,v 1.9 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de sélection d'un pays

// classe pour la gestion des pays dans le sélecteur
require("./selectors/classes/selector_country.class.php");

$selector_country = new selector_country(stripslashes($user_input));
$selector_country->proceed();
