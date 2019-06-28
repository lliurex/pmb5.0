<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_ui.class.php,v 1.1.2.2 2017-11-14 14:31:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($include_path.'/templates/list/list_bannettes_ui.tpl.php');
require_once($base_path."/dsi/func_common.inc.php");

class list_bannettes_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'select id_bannette, proprio_bannette FROM bannettes 	
				';
		$query .= $this->_get_query_filters();
		$query .= ' ORDER BY nom_bannette, id_bannette';
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new bannette($row->id_bannette);
			}
			$this->pager['nb_results'] = count($this->objects);
		}
		$this->messages = "";
	}
	
	protected function init_default_columns() {
		$this->columns = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $sub;
		
		$this->filters = array(
				'sub' => $sub,
				'auto' => 1,
				'id_classement' => '',
				'name' => '',
				'proprio_bannette' => ''
		);
		parent::init_filters($filters);
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $id_classement;
		
		$name = $this->objects_type.'_name';
		global ${$name};
		if(isset(${$name}) && ${$name} != '') {
			$this->filters['name'] = ${$name};
		}
		if(isset($id_classement)) {
			$this->filters['id_classement'] = $id_classement;
		}
		parent::set_filters_from_form();
	}
		
	public function get_export_icons() {
		return "";
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $base_path, $sub;
		global $list_bannettes_ui_search_content_form_tpl;
		
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', $base_path.'/dsi.php?categ=diffuser&sub='.$sub, $search_form);
		
		$content_form = $list_bannettes_ui_search_content_form_tpl;
		$content_form = str_replace("!!name!!", $this->filters['name'], $content_form);
		$content_form = str_replace("!!classement!!", gen_liste_classement("BAN", $this->filters['id_classement'], "this.form.submit();")  , $content_form);
		$content_form = str_replace('!!objects_type!!', $this->objects_type, $content_form);
		$search_form = str_replace('!!list_search_content_form_tpl!!', $content_form, $search_form);
		
		return $search_form;
	}
		
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['sub'] == 'lancer') {
			$filters [] = '(DATE_ADD(date_last_envoi, INTERVAL periodicite DAY) <= sysdate())';
		}
// 		if($this->filters['auto']) {
			$filters [] = 'bannette_auto = "'.$this->filters['auto'].'"';
// 		}
		if($this->filters['id_classement']) {
			$filters [] = 'num_classement = "'.$this->filters['id_classement'].'"';
		} elseif($this->filters['id_classement'] === 0) {
			$filters [] = 'num_classement = "0"';
		}
		if($this->filters['name']) {
			$filters [] = 'nom_bannette like "%'.str_replace("*", "%", $this->filters['name']).'%"';
		}
		if($this->filters['proprio_bannette'] !== '') {
			$filters [] = 'proprio_bannette = "'.$this->filters['proprio_bannette'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);		
		}
		return $filter_query;
	}
}