<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authority_page_indexint.class.php,v 1.1 2017-04-21 15:08:11 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


require_once($class_path."/authorities/page/authority_page.class.php");

/**
 * class authority_page_indexint
 * Controler d'une page d'une autorité indexation décimale
 */
class authority_page_indexint extends authority_page {
	/**
	 * Constructeur
	 * @param int $id Identifiant de l'indexation décimale
	 */
	public function __construct($id) {
		$this->id = $id*1;
		$query = "select indexint_id from indexint where indexint_id = ".$this->id;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)){
			$this->authority = new authority(0, $this->id, AUT_TABLE_INDEXINT);
		}
	}
	
	protected function get_title_recordslist() {
		global $msg, $charset;
		return htmlentities($msg['indexint_see_title']." ".$this->authority->get_object_instance()->name, ENT_QUOTES, $charset);
	}
	
	protected function get_clause_authority_id_recordslist() {
		return "indexint=".$this->id;
	}
	
	protected function get_mode_recordslist() {
		return "indexint_see";
	}
	
}