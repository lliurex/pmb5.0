<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3 2017-01-25 16:43:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'periodicite':
		$admin_layout = str_replace('!!menu_sous_rub!!', $msg['admin_menu_abonnements_periodicite'], $admin_layout);
		echo window_title($database_window_title.$msg['admin_menu_abonnements_periodicite'].$msg['1003'].$msg['1001']);
		print $admin_layout;
		include("./admin/abonnements/periodicite.inc.php");
		break;
	default:
		$admin_layout = str_replace('!!menu_sous_rub!!', "", $admin_layout);
		print $admin_layout;
		echo window_title($database_window_title.$msg['admin_menu_abonnements'].$msg['1003'].$msg['1001']);
		include("$include_path/messages/help/$lang/admin_abonnements.txt");
		break;
}
