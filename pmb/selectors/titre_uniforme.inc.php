<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titre_uniforme.inc.php,v 1.21 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "inc.php")) die("no access");

// classe pour la gestion des titres uniformes dans le sélecteur
require("./selectors/classes/selector_titre_uniforme.class.php");

$selector_titre_uniforme = new selector_titre_uniforme(stripslashes($user_input));
$selector_titre_uniforme->proceed();
