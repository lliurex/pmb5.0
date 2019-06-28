<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lang.inc.php,v 1.15 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de sélection langue

// classe pour la gestion des langues dans le sélecteur
require("./selectors/classes/selector_lang.class.php");

$selector_lang = new selector_lang(stripslashes($user_input));
$selector_lang->proceed();