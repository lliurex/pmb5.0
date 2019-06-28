<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: categ_delete.inc.php,v 1.21 2017-07-05 15:27:40 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/category.class.php");

$id += 0;

if ($id) {
	$category = new category($id);
	
	$response_deleted_category = $category->delete();
	
	if ($response_deleted_category) {
		print $response_deleted_category;
	}
}
include('./autorites/subjects/default.inc.php');

?>
