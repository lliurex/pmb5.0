<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serie.inc.php,v 1.32 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classe pour la gestion des titres de série dans le sélecteur
require("./selectors/classes/selector_serie.class.php");

$selector_serie = new selector_serie(stripslashes($user_input));
$selector_serie->proceed();