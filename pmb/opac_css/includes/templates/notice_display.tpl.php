<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_display.tpl.php,v 1.6.26.1 2018-01-25 10:15:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $notice_display_header;
global $notice_display_footer;

// template for PMB OPAC

$notice_display_header = "
<div id='notice'><span>
";

$notice_display_footer ="
</span></div>
";
