<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datasource_works.class.php,v 1.6.2.2 2017-10-02 14:53:51 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_datasource_works extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'works';
		parent::__construct($id);
	}
	
	public function get_sub_datasources(){
		return array(
				"frbr_entity_works_datasource_works_expressions_of",
				"frbr_entity_works_datasource_works_have_expressions",
				"frbr_entity_works_datasource_works_other_links"
		);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		if($this->get_parameters()->sub_datasource_choice) {
			$class_name = $this->get_parameters()->sub_datasource_choice;
			$sub_datasource = new $class_name();
			if (isset($this->parameters->work_link_type)) {
				$sub_datasource->set_work_link_type($this->parameters->work_link_type); 
			}
			if(isset($this->external_filter) && $this->external_filter) {
				$sub_datasource->set_filter($this->external_filter);
			}
			if(isset($this->external_sort) && $this->external_sort) {
				$sub_datasource->set_sort($this->external_sort);
			}
			return $sub_datasource->get_datas($datas);
		} else {
			$query = "select distinct oeuvre_link_from as id, oeuvre_link_to as parent FROM tu_oeuvres_links WHERE oeuvre_link_to IN (".implode(',', $datas).")
			UNION select distinct oeuvre_link_to as id, oeuvre_link_from as parent FROM tu_oeuvres_links WHERE oeuvre_link_from IN (".implode(',', $datas).")";
			$datas = $this->get_datas_from_query($query);
			$datas = parent::get_datas($datas);
			return $datas;
		}		
	}
	
	public function save_form() {
		global $datanode_work_link_type;
		if(isset($datanode_work_link_type)){
			$this->parameters->work_link_type = $datanode_work_link_type;
		}
		return parent::save_form();
	}
	
}