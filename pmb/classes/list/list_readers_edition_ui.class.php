<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_readers_edition_ui.class.php,v 1.1 2017-02-28 11:42:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($include_path."/templates/list/list_readers_edition_ui.tpl.php");

class list_readers_edition_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function fetch_data() {
		
		$this->objects = array();
		$query = 'SELECT id_empr FROM empr
				JOIN empr_statut ON empr.empr_statut=empr_statut.idstatut
				JOIN empr_categ ON empr.empr_categ=empr_categ.id_categ_empr';
		$query .= $this->_get_query_filters();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new emprunteur($row->id_empr);
			}
			$this->pager['nb_results'] = count($this->objects);
		}
		$this->messages = "";
	}
		
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt2docs_location;
		
		$this->filters = array(
				'empr_location_id' => $deflt2docs_location,
				'empr_statut_edit' => ''
		);
		parent::init_filters($filters);
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $empr_location_id;
		global $empr_statut_edit;
		
		if(isset($empr_location_id)) {
			$this->filters['empr_location_id'] = $empr_location_id*1;
		}
		if(isset($empr_statut_edit)) {
			$this->filters['empr_statut_edit'] = $empr_statut_edit*1;
		}
		parent::set_filters_from_form();
	}
	
	protected function get_title() {
		global $titre_page;
		return "<h1>".$titre_page."</h1>";
	}
	
	protected function get_form_title() {
		return '';
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $msg, $charset;
		global $base_path, $sub;
		global $list_readers_ui_search_content_form_tpl;
		global $pmb_lecteurs_localises;
	
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', $base_path.'/edit.php?categ=empr&sub='.$sub, $search_form);
		
		$content_form = $list_readers_ui_search_content_form_tpl;
		if ($pmb_lecteurs_localises) {
			$content_form = str_replace('!!locations!!', docs_location::gen_combo_box_empr($this->filters['empr_location_id']), $content_form);
		} else {
			$content_form = str_replace('!!locations!!', '', $content_form);
		}
		$content_form = str_replace('!!status!!', gen_liste("select idstatut, statut_libelle from empr_statut","idstatut","statut_libelle","empr_statut_edit","",$this->filters['empr_statut_edit'],-1,"",0,$msg["all_statuts_empr"]), $content_form);
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
		if($this->filters['empr_location_id']) {
			$filters [] = 'empr_location = "'.$this->filters['empr_location_id'].'"';
		}
		if($this->filters['empr_statut_edit']) {
			$filters [] = 'empr_statut = "'.$this->filters['empr_statut_edit'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function init_default_columns() {
		global $sub;
		
		$this->add_column('cb', 'code_barre_empr');
		$this->add_column('empr_name', 'nom_prenom_empr');
		$this->add_column('adr1', 'adresse_empr');
		$this->add_column('ville', 'ville_empr');
		$this->add_column('birth', 'year_empr');
		$this->add_column('aff_date_expiration', 'readerlist_dateexpiration');
		$this->add_column('empr_statut_libelle', 'statut_empr');
		switch ($sub) {
			case "encours" :
				break;
			case "categ_change" :
				$this->add_column('', 'categ_empr');
				$this->add_column('', 'empr_categ_change_prochain');
				break;
			default :
				break;
		}
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'empr_name':
				$content .= $object->nom." ".$object->prenom;
				break;
			default :
				if (is_object($object) && isset($object->{$property})) {
					$content .= $object->{$property};
				} elseif(method_exists($object, 'get_'.$property)) {
					$content .= call_user_func_array(array($object, "get_".$property), array());
				}
				break;
		}
		return $content;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = array();
		if($this->filters['empr_location_id']) {
			$docs_location = new docs_location($this->filters['empr_location_id']);
			$humans[] = "<b>".htmlentities($msg['editions_filter_empr_location'], ENT_QUOTES, $charset)."</b> ".$docs_location->libelle;
		}
		if($this->filters['empr_statut_edit']) {
			$query = "select statut_libelle from empr_statut where idstatut = ".$this->filters['empr_statut_edit'];
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$humans[] = "<b>".htmlentities($msg['statut_empr'], ENT_QUOTES, $charset)."</b> ".$row->statut_libelle;
		}
		$human_query = "<div align='left'><br />".implode(', ', $humans)." => ".sprintf(htmlentities($msg['searcher_results'], ENT_QUOTES, $charset), $this->pager['nb_results'])."<br /><br /></div>";		
		return $human_query;
	}
	
	protected function get_display_spreadsheet_title() {
		global $titre_page;
		$this->spreadsheet->write_string(0,0,$titre_page);
	}
	
	protected function get_html_title() {
		global $titre_page;
		return "<h1>".$titre_page."</h1>";
	}
}