<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_serie.class.php,v 1.1 2017-04-21 15:08:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_serie
 * Controler d'une page d'une autorité série
 */
class authority_page_serie extends authority_page {
	/**
	 * Constructeur
	 * @param int $id Identifiant de la série
	 */
	public function __construct($id) {
		$this->id = $id*1;
		$query = "select serie_id from series where serie_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			$this->authority = new authority(0, $this->id, AUT_TABLE_SERIES);
		}
	}

	protected function get_title_recordslist() {
		global $msg, $charset;
		return htmlentities($msg['doc_serie_title'], ENT_QUOTES, $charset);
	}
	
	protected function get_clause_authority_id_recordslist() {
		return "tparent_id=".$this->id;
	}
	
	protected function get_mode_recordslist() {
		return "serie_see";
	}
	
}