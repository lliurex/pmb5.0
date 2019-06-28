<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editeur.inc.php,v 1.37 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classe pour la gestion des éditeurs dans le sélecteur
require("./selectors/classes/selector_publisher.class.php");

$selector_publisher = new selector_publisher(stripslashes($user_input));
$selector_publisher->proceed();