<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_export_tableau.inc.php,v 1.5 2017-07-04 08:18:01 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if (count($chk)) {
	require_once($class_path.'/suggestions_categ.class.php');
	require_once($class_path.'/suggestion_source.class.php');
	require_once($class_path.'/suggestions_map.class.php');
	
	require_once ($class_path."/spreadsheet.class.php");
	$fichier_temp_nom=str_replace(" ","",microtime());
	$fichier_temp_nom=str_replace("0.","",$fichier_temp_nom);
	$fname = tempnam("./temp", $fichier_temp_nom.".xlsx");
	$worksheet = new spreadsheet();
	
	$worksheet->write_string(0,0,$msg['acquisition_sug']);
	
	$worksheet->write_string(2,0,$msg['acquisition_sug_dat_cre']);
	$worksheet->write_string(2,1,$msg['acquisition_sug_tit']);
	$worksheet->write_string(2,2,$msg['acquisition_sug_edi']);
	$worksheet->write_string(2,3,$msg['acquisition_sug_aut']);
	$worksheet->write_string(2,4,$msg['acquisition_sug_etat']);
	$worksheet->write_string(2,5,$msg['acquisition_sug_iscat']);
	$col = 6;
	if ($acquisition_sugg_categ == '1') {
		$worksheet->write_string(2,$col,$msg['acquisition_categ']);
		$col++;
	}
	$worksheet->write_string(2,$col,$msg['acquisition_sugg_src']);
	$col++;
	$worksheet->write_string(2,$col,$msg['acquisition_sugg_date_publication']);
	$col++;
	$worksheet->write_string(2,$col,$msg['acquisition_sugg_piece_jointe']);
	$col++;
	$worksheet->write_string(2,$col,$msg['acquisition_sugg_prix']);
	$col++;
	$worksheet->write_string(2,$col,$msg['acquisition_sugg_comment']);
	$col++;
	$worksheet->write_string(2,$col,$msg['acquisition_sugg_code']);
	
	$sug_map = new suggestions_map();
	
	$row = 3;
	foreach ($chk as $sugg_id) {
		$sugg=new suggestions($sugg_id);

		$worksheet->write_string($row,0,formatdate($sugg->date_creation));
		$worksheet->write_string($row,1,$sugg->titre);
		$worksheet->write_string($row,2,$sugg->editeur);
		$worksheet->write_string($row,3,$sugg->auteur);
		$worksheet->write_string($row,4,html_entity_decode($sug_map->getHtmlComment($sugg->statut),ENT_HTML401,$charset));
		$worksheet->write_string($row,5,($sugg->num_notice?'X':''));
		$col = 6;
		if ($acquisition_sugg_categ == '1') {
			$categ = new suggestions_categ($sugg->num_categ);
			$worksheet->write_string($row,$col,$categ->libelle_categ);
			$col++;
		}
		$source = new suggestion_source($sugg->sugg_src);
		$worksheet->write_string($row,$col,$source->libelle_source);
		$col++;
		$worksheet->write_string($row,$col,$sugg->date_publi);
		$col++;
		$worksheet->write_string($row,$col,($sugg->get_explnum('id')?'X':''));
		$col++;
		$worksheet->write_string($row,$col,($sugg->prix!=0?$sugg->prix:''));
		$col++;
		$worksheet->write_string($row,$col,preg_replace('/\r\n?/', ' ', $sugg->commentaires));
		$col++;
		$worksheet->write_string($row,$col,$sugg->code);
		
		$row++;
	}
	
	$worksheet->save_file($fname);
	
	print "<script>window.open('acquisition/suggestions/suggestions_export_tableau_download.php?fname=".$fname."'); history.go(-1);</script>";
} else {
	print "<script>alert(\"".$msg["acquisition_sug_msg_nocheck_export"]."\"); history.go(-1);</script>";
}