<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_section_categories.class.php,v 1.5.4.1 2018-01-08 14:36:32 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_section_categories extends cms_module_common_datasource_records_list{
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_section",
			"cms_module_common_selector_env_var"
		);
	}
	
	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		$return  = parent::get_sort_criterias();
		$return[] = "pert";
		return $return;
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		$selector = $this->get_selected_selector();
		if ($selector) {
			$query = "select distinct notice_id 
				from notices join notices_categories on notice_id=notcateg_notice 
				join cms_sections_descriptors on cms_sections_descriptors.num_noeud=notices_categories.num_noeud 
				and num_section='".($selector->get_value()*1)."'";
			$result = pmb_mysql_query($query);
			$return = array();
			if($result && (pmb_mysql_num_rows($result) > 0)){
				$return["title"] = "Liste de notices";
				while($row = pmb_mysql_fetch_object($result)){
					$return["records"][] = $row->notice_id;
				}
			}
			$return['records'] = $this->filter_datas("notices",$return['records']);
			
			if(!count($return['records'])) return false;
			if ($this->parameters["sort_by"] == 'pert') {
				// on tri par pertinence
				$query = "SELECT notice_id
						FROM notices
						JOIN notices_categories ON notice_id = notcateg_notice
						JOIN cms_sections_descriptors ON cms_sections_descriptors.num_noeud = notices_categories.num_noeud
						AND num_section = '".($selector->get_value()*1)."' where notice_id in ('".implode("','", $return['records'])."') group by notice_id order by count(*) ".$this->parameters["sort_order"].", create_date desc";
				if ($this->parameters['nb_max_elements']*1) {
					$query.= " limit ".$this->parameters['nb_max_elements']*1;
				}
				$result = pmb_mysql_query($query);
				$return = array();
				if(pmb_mysql_num_rows($result) > 0){
					$return["title"] = "Liste de notices";
					while($row = pmb_mysql_fetch_object($result)){
						$return["records"][] = $row->notice_id*1;
					}
				}
			} else {
				$return = $this->sort_records($return['records']);
			}
			return $return;
		}
		return false;
	}
}