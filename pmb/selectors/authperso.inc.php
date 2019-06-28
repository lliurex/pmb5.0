<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.inc.php,v 1.11 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classe pour la gestion des autorités perso dans le sélecteur
require("./selectors/classes/selector_authperso.class.php");

$selector_authperso = new selector_authperso(stripslashes($user_input));
$selector_authperso->proceed();