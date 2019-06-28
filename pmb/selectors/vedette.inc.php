<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette.inc.php,v 1.2.4.1 2017-09-12 13:23:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classe pour la gestion des vedettes dans le sélecteur
require("./selectors/classes/selector_vedette.class.php");

$selector_vedette = new selector_vedette(stripslashes($user_input));
$selector_vedette->proceed();
