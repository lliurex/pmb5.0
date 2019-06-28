<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_edition_ui.tpl.php,v 1.1 2017-02-28 11:42:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$list_loans_ui_search_content_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['editions_filter_empr_location']."</label>
		</div>
		<div class='row'>
			!!empr_locations!!
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>		
			<label class='etiquette'>".$msg['editions_filter_docs_location']."</label>
		</div>
		<div class='row'>
			!!docs_locations!!
		</div>
	</div>
</div>
";
