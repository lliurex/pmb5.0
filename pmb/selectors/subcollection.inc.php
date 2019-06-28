<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: subcollection.inc.php,v 1.35 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classe pour la gestion des sous-collections dans le sélecteur
require("./selectors/classes/selector_subcollection.class.php");

$selector_subcollection = new selector_subcollection(stripslashes($user_input));
$selector_subcollection->proceed();
