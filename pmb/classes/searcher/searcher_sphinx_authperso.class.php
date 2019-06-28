<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authperso.class.php,v 1.3 2017-06-06 07:33:26 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_authperso extends searcher_sphinx_authorities {
	protected $index_name = 'authperso';

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/authperso/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'authperso';
		$this->authority_type = AUT_TABLE_AUTHPERSO;
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		return $filters;
	}
	
	protected function get_search_indexes(){
		global $lang, $id_authperso;
		if ($id_authperso) {
			return $this->index_name.'_'.$id_authperso.'_'.$lang.','.$this->index_name.'_'.$id_authperso;
		}
		// On cherche dans toutes les autorités persos
		$indexes = '';
		$result = pmb_mysql_query('select id_authperso from authperso');
		if (pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				if ($indexes) {
					$indexes.= ',';
				}
				$indexes.= $this->index_name.'_'.$row->id_authperso.'_'.$lang.','.$this->index_name.'_'.$row->id_authperso;
			}
		}
		return $indexes;
	}
	
	protected function get_full_raw_query(){
		global $id_authperso;
		if ($id_authperso) {
			return 'select id_authority as id, 100 as weight from authorities join authperso_authorities on num_object = id_authperso_authority where type_object = '.$this->authority_type.' and authperso_authority_authperso_num = '.$id_authperso;
		}
		return 'select id_authority as id, 100 as weight from authorities where type_object = '.$this->authority_type;
	}
}