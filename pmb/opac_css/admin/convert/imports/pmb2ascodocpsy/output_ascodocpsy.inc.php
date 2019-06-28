<?php
function _get_header_($output_params) {
	$tab_r = array("TYPE","AUT","TIT","EDIT","LIEU","PAGE","DATE","MOTCLE","NOMP","NOTES","PRODFICH","LOC","COL","THEME","RESU","SUPPORT","SUPPORTPERIO","LIEN","VOL","CANDES","CONGRTIT","CONGRLIE","CONGRDAT","CONGRNUM","ISBNISSN","REED","DIPSPE","REV","VIEPERIO","ETATCOL","NUM","PDPF","NATTEXT","DATETEXT","DATEPUB","NUMTEXOF","DATEVALI","ANNEXE","LIENANNE","DATESAIS");
	$r = implode("\t", $tab_r);
	$r.= "\n";
	return $r;
}

function _get_footer_($output_params) {
	return "";
}