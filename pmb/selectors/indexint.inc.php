<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.inc.php,v 1.43.2.1 2017-09-12 13:23:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// classe pour la gestion des indexations décimales dans le sélecteur
require("./selectors/classes/selector_indexint.class.php");


if(!isset($id_pclass)) $id_pclass = '';
if(!isset($num_pclass)) $num_pclass = '';

if (!$id_pclass && !$num_pclass && $thesaurus_classement_defaut){
	$id_pclass=$thesaurus_classement_defaut;
}elseif (!$id_pclass && $num_pclass){
	$id_pclass=$num_pclass;
}
if ($thesaurus_classement_mode_pmb) { //classement indexation décimale autorisé en parametrage
	if (strpos($deb_rech,"]")) $deb_rech=substr($deb_rech,strpos($deb_rech,"]")+2);	
}

$selector_indexint = new selector_indexint(stripslashes($user_input));
$selector_indexint->proceed();