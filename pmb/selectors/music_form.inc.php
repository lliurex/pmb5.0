<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: music_form.inc.php,v 1.2 2017-01-19 10:25:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de sélection music_form

// classe pour la gestion de la forme de l'oeuvre dans le sélecteur
require("./selectors/classes/selector_music_form.class.php");

$selector_music_form = new selector_music_form(stripslashes($user_input));
$selector_music_form->proceed();