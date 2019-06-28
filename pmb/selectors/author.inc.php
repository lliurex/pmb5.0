<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: author.inc.php,v 1.44 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "inc.php")) die("no access");

// classe pour la gestion des auteurs dans le sélecteur
require("./selectors/classes/selector_author.class.php");

$selector_author = new selector_author(stripslashes($user_input));
$selector_author->proceed();

