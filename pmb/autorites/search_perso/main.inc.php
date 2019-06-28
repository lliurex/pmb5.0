<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1 2016-06-10 15:13:09 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/search_perso.class.php");

$search_p= new search_perso($id, 'AUTHORITIES');

switch($sub) {
	case "form":
		print $search_p->do_form();	
	break;
	case "save":
		// sauvegarde issu du formulaire
		$search_p->set_properties_form_form();
		$search_p->save();
		print $search_p->do_list();
	break;	
	case "delete":
		$search_p->delete();
		print $search_p->do_list();
	break;
	default :
		// affiche liste des recherches prédéfinies
		print $search_p->do_list();
	break;
}


