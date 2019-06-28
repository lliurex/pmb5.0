<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2016-08-23 09:40:22 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// page de switch recherche notice

// inclusions principales
require_once("$class_path/search_perso.class.php");

$search_p= new search_perso($id, 'RECORDS');

switch($sub) {
	case "form":
		// affichage du formulaire de recherche perso, en création ou en modification => $id)
		print $search_p->do_form();	
	break;
	case "save":
		// sauvegarde issu du formulaire
		$search_p->set_properties_form_form();
		$search_p->save();
		print $search_p->do_list();
	break;	
	case "delete":
		// effacement d'une recherche personalisée, issu du formulaire
		$search_p->delete();
		print $search_p->do_list();
	break;
	case "launch":
		// accès direct à une recherche personalisée
		print $search_p->launch();
		break;
	default :
		// affiche liste des recherches personalisée
		print $search_p->do_list();
	break;
}


