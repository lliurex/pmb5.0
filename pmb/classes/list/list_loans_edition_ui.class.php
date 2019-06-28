<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_loans_edition_ui.class.php,v 1.1 2017-02-28 11:42:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($include_path."/templates/list/list_loans_edition_ui.tpl.php");

class list_loans_edition_ui extends list_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function fetch_data() {
		
		$this->objects = array();
		/* Conservation des anciens éléments du select
		 	date_format(pret_date, '".$msg['format_date']."') as aff_pret_date, ";
			$sql .= "date_format(pret_retour, '".$msg['format_date']."') as aff_pret_retour, ";
			$sql .= "IF(pret_retour>=CURDATE(),0,1) as retard, ";
			$sql .= "id_empr, empr_nom, empr_prenom, empr_mail, empr_cb, expl_cote, expl_cb, expl_notice, expl_bulletin, notices_m.notice_id as idnot, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, tdoc_libelle, ";
			$sql .= "short_loan_flag
		 */
		$query = 'select pret_idempr, pret_idexpl 
			FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id )
				LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id)
				LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id)
				JOIN pret ON pret_idexpl = expl_id
				JOIN empr ON empr.id_empr = pret.pret_idempr
				JOIN docs_type ON expl_typdoc = idtyp_doc 	
				';
		$query .= $this->_get_query_filters();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {				
				$this->objects[] = new pret($row->pret_idempr, $row->pret_idexpl);
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
				'docs_location_id' => ''
		);
		parent::init_filters($filters);
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $empr_location_id;
		global $docs_location_id;
		
		if(isset($empr_location_id)) {
			$this->filters['empr_location_id'] = $empr_location_id*1;
		}
		if(isset($docs_location_id)) {
			$this->filters['docs_location_id'] = $docs_location_id*1;
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
		global $list_loans_ui_search_content_form_tpl;
		global $pmb_lecteurs_localises;
		
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', $base_path.'/edit.php?categ=expl&sub='.$sub, $search_form);
		
		$content_form = $list_loans_ui_search_content_form_tpl;
		if ($pmb_lecteurs_localises) {
			$content_form = str_replace('!!empr_locations!!', docs_location::gen_combo_box_empr($this->filters['empr_location_id']), $content_form);
		} else {
			$content_form = str_replace('!!empr_locations!!', '', $content_form);
		}
		$content_form = str_replace('!!docs_locations!!', docs_location::gen_combo_box_empr($this->filters['docs_location_id']), $content_form);
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
		if($this->filters['docs_location_id']) {
			$filters [] = 'expl_location = "'.$this->filters['docs_location_id'].'"';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);		
		}
		return $filter_query;
	}
	
	protected function init_default_columns() {
		global $sub;
	
		$this->add_column('cb_expl', '4014');
		$this->add_column('', '4016');
		$this->add_column('', '294');
		$this->add_column('', '233');
		$this->add_column('', '234');
		$this->add_column('', 'empr_nom_prenom');
		$this->add_column('date_pret_display', 'circ_date_emprunt');
		$this->add_column('date_retour_display', 'circ_date_retour');
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = array();
		if($this->filters['empr_location_id']) {
			$docs_location = new docs_location($this->filters['empr_location_id']);
			$humans[] = "<b>".htmlentities($msg['editions_filter_empr_location'], ENT_QUOTES, $charset)."</b> ".$docs_location->libelle;
		}
		if($this->filters['docs_location_id']) {
			$docs_location = new docs_location($this->filters['docs_location_id']);
			$humans[] = "<b>".htmlentities($msg['editions_filter_docs_location'], ENT_QUOTES, $charset)."</b> ".$docs_location->libelle;
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