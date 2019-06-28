<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_edition_ui.tpl.php,v 1.1 2017-02-28 11:42:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$list_transferts_edition_ui_search_content_form_tpl = "
<div class='row'>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["transferts_edition_filtre_origine"]."</label>
		</div>
		<div class='row'>
			<select name='site_origine'>!!liste_sites_origine!!</select>
		</div>
	</div>
	<div class='colonne3'>
		<div class='row'>
			<label class='etiquette'>".$msg["transferts_edition_filtre_destination"]."</label>
		</div>
		<div class='row'>
			<select name='site_destination'>!!liste_sites_destination!!</select>
		</div>
	</div>
	<div class='colonne3'>
		!!retour_filtre_etat!!
	</div>
</div>
";
