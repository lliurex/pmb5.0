<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: translation.class.php,v 1.6 2017-07-05 10:10:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php"))	die("no access");

/**
 * Classe permettant de gérer les traductions de libellé
 * Utilise la table translation, croisée avec le nom de la table et du champ à traduire
 * Mémorise et récupère le texte dans la lange voulue
 * 
 "CREATE TABLE translation (
    trans_table VARCHAR( 255 ) NOT NULL default '',
    trans_field VARCHAR( 255 ) NOT NULL default '',
    trans_lang VARCHAR( 255 ) NOT NULL default '',
   	trans_num INT( 8 ) UNSIGNED NOT NULL default 0 ,
    trans_text VARCHAR( 255 ) NOT NULL default '',
    PRIMARY KEY trans (trans_table,trans_field,trans_lang,trans_num),
    index i_lang(trans_lang)
   )";  
 */
class translation {

	protected static $memo_translation;
	
	public function __construct() {
	}
	
	/**
	 * Retourne la traduction dans la langue voulue, ou le libellé par défaut
	 */
	public static function get_text($id, $trans_table, $trans_field, $text="", $mylang="") {
		global $lang, $dbh;
		
		if(!$mylang) {
			$mylang = $lang;
		}

		if (isset(translation::$memo_translation[$id][$trans_table][$trans_field][$mylang])) {
			return translation::$memo_translation[$id][$trans_table][$trans_field][$mylang];
		}
		$req = "SELECT trans_text FROM translation WHERE trans_table='".$trans_table."' and trans_field='$trans_field' and trans_num='".$id."' and trans_lang='".$mylang."' limit 1";
		$myQuery = pmb_mysql_query($req);
		if(pmb_mysql_num_rows($myQuery)) {
			$myreq = pmb_mysql_fetch_object($myQuery);
			if($myreq->trans_text) {
				$text = $myreq->trans_text;
			}
		}
		translation::$memo_translation[$id][$trans_table][$trans_field][$mylang] = $text;
		return $text;
	}
}
