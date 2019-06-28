<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: print_cart_tpl.inc.php,v 1.1.2.2 2017-10-16 09:38:09 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/print_cart_tpl.class.php");

$print_cart_tpl = new print_cart_tpl($id);
$print_cart_tpl->proceed();
