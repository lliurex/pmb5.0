<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_series_datasource_concepts.class.php,v 1.3.2.1 2017-09-15 12:37:32 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_series_datasource_concepts extends frbr_entity_common_datasource_concept {
	
	public function __construct($id=0){
		$this->entity_type = 'concepts';
		parent::__construct($id);
	}
	
	/*
	 * R�cup�ration des donn�es de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct index_concept.num_concept as id, index_concept.num_object as parent FROM index_concept
			WHERE index_concept.type_object = 7 AND index_concept.num_object IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}