<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_authors.class.php,v 1.4 2017-06-06 07:33:26 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/searcher/searcher_sphinx_authorities.class.php');

class searcher_sphinx_authors extends searcher_sphinx_authorities {
	protected $index_name = 'authors';

	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/authorities/authors/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'authors';
		$this->authority_type = AUT_TABLE_AUTHORS;
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		global $type_autorite;
		if($type_autorite){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est g�r� dans la classe racine � la vol�e!
			$filters[] = array(
					'name'=> 'author_type',
					'values' => $type_autorite
			);
		}
		return $filters;
	}
}