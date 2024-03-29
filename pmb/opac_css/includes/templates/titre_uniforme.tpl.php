<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: titre_uniforme.tpl.php,v 1.3.6.1 2018-01-25 10:15:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// ce fichier contient des templates indiquant comment doit s'afficher un titre uniforme

if ( ! defined( 'TITRE_UNIFORME_TMPL' ) ) {
  define( 'TITRE_UNIFORME_TMPL', 1 );

global $titre_uniforme_level2_display;

// level 2 : affichage g�n�ral
$titre_uniforme_level2_display = "
<div class=publisherlevel2>
<h3>".sprintf($msg["titre_uniforme_detail"],"!!name!!")."</h3>		
!!auteur!!
!!forme!!
!!forme_list!!		
!!date!!
!!lieu!!
!!sujet!!
!!completude!!
!!public!!
!!histoire!!
!!contexte!!
!!distribution!!
!!reference!!
!!tonalite!!
!!tonalite_list!!
!!coordonnees!!
!!equinoxe!!
!!subdivision!!
!!caracteristique!!
<div class=aut_comment>!!aut_comment!!</div>
</div>
";

} # fin de d�finition
