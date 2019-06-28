<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reporting.inc.php,v 1.4 2017-07-10 15:50:02 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/scheduler/scheduler_dashboard.class.php");

$scheduler_dashboard = new scheduler_dashboard();
print $scheduler_dashboard->get_display_list();




