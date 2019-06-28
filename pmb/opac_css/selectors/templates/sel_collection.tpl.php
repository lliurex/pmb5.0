<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_collection.tpl.php,v 1.1.2.1 2017-09-12 13:22:56 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;
global $mode, $p1, $p2, $p3, $p4, $p5, $p6;

if($mode=="un") {
	$jscript = $jscript_common_selector;
} else {
	if ($dyn==3) {
		$jscript = $jscript_common_authorities_unique;
	}elseif ($dyn==2) { // Pour les liens entre autorités
		$jscript = $jscript_common_authorities_link;
	}else {
		$jscript = "
		<script type='text/javascript'>
		<!--
		function set_parent(f_caller, id_coll, libelle_coll, callback, id_ed, libelle_ed){
			window.opener.document.forms[f_caller].elements['$p1'].value = id_ed;
			window.opener.document.forms[f_caller].elements['$p2'].value = reverse_html_entities(libelle_ed);
			window.opener.document.forms[f_caller].elements['$p3'].value = id_coll;
			window.opener.document.forms[f_caller].elements['$p4'].value = reverse_html_entities(libelle_coll);
			window.opener.document.forms[f_caller].elements['$p5'].value = '';
			window.opener.document.forms[f_caller].elements['$p6'].value = '';
		
			window.close();
		}
		-->
		</script>
		";
	}
}