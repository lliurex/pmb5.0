<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: func.inc.php,v 1.20 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de sélection fonction responsable

// classe pour la gestion des fonctions dans le sélecteur
require("./selectors/classes/selector_func.class.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

$jscript = $jscript_common_selector_simple;

$selector_func = new selector_func(stripslashes($user_input));
$selector_func->proceed();