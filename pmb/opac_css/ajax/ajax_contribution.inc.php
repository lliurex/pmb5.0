<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_contribution.inc.php,v 1.4.2.1 2017-09-14 14:45:28 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if ($opac_contribution_area_activate && $allow_contribution) {
	if ($iframe) {
		print '<textarea>';
	}
	switch ($sub) {
		case 'ajax_check_values' :
			require_once($base_path.'/includes/contribution_area_check_values.inc.php');
			break;
		default :
			require_once($base_path.'/includes/contribution_area.inc.php');
			break;
	}
	if ($iframe) {
		print '</textarea>';
	}
}