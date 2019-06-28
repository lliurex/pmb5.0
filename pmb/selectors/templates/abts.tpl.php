<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts.tpl.php,v 1.4 2017-01-19 10:25:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

if ($dyn==1) {
$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value,callback, flag_circlist_info)	{		
		if(callback)
			window.opener[callback](id_value,libelle_value,flag_circlist_info);
		window.close();
	}
	-->
	</script>
";
}
