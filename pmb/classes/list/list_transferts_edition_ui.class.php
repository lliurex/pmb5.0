<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_edition_ui.class.php,v 1.2 2017-03-01 16:16:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_transferts_ui.class.php');
require_once($include_path.'/templates/list/list_transferts_edition_ui.tpl.php');

class list_transferts_edition_ui extends list_transferts_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_title() {
		global $msg, $sub;
		return "<h1>".$msg["transferts_edition_titre"]."&nbsp;&gt;&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
	}
	
	protected function get_form_title() {
		return '';
	}
	
	protected function get_search_retour_filtre_etat_selector() {
		global $msg, $charset;
	
		$selector = "<div class='row'><label class='etiquette'>".$msg["transferts_circ_retour_filtre_etat"]."</label></div>";
		$selector .= "<div class='row'>";
		$selector .= parent::get_search_retour_filtre_etat_selector();
		$selector .= "</div>";
		return $selector;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $msg, $charset;
		global $base_path, $sub;
		global $list_transferts_edition_ui_search_content_form_tpl;
	
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', $base_path.'/edit.php?categ=transferts&sub='.$sub, $search_form);
		
		$content_form = $list_transferts_edition_ui_search_content_form_tpl;
		$content_form = str_replace('!!liste_sites_origine!!', $this->get_search_options_locations($this->filters['site_origine']), $content_form);
		$content_form = str_replace('!!liste_sites_destination!!', $this->get_search_options_locations($this->filters['site_destination']), $content_form);
		if ($sub=="retours") {
			//le filtre de l'etat de la date
			$content_form = str_replace('!!retour_filtre_etat!!', $this->get_search_retour_filtre_etat_selector(), $content_form);
		} else {
			$content_form = str_replace('!!retour_filtre_etat!!', '', $content_form);
		}
		$search_form = str_replace('!!list_search_content_form_tpl!!', $content_form, $search_form);
		
		return $search_form;
	}
	
	protected function init_default_columns() {
		global $pmb_expl_data;
		global $transferts_edition_show_all_colls;
		
		$this->add_column('record', 'transferts_edition_tableau_titre');
		$this->add_column('section', 'transferts_edition_tableau_section');
		$this->add_column('cote', 'transferts_edition_tableau_cote');
		$this->add_column('cb', 'transferts_edition_tableau_expl');
		
		// paramtres perso demandé dans $pmb_expl_data
		$colonnesarray=explode(",",$pmb_expl_data);
		$this->displayed_cp = array();
		if (strstr($pmb_expl_data, "#")) {
			$this->cp=new parametres_perso("expl");
			for ($i=0; $i<count($colonnesarray); $i++) {
				if (substr($colonnesarray[$i],0,1)=="#") {
					//champ personnalisé
					if (!$this->cp->no_special_fields) {
						$id=substr($colonnesarray[$i],1);
						$this->add_column($this->cp->t_fields[$id]['NAME'], $this->cp->t_fields[$id]['TITRE']);
						$this->displayed_cp[$id] = $this->cp->t_fields[$id]['NAME'];
					}
				}
			}
		}
		
		$this->add_column('empr', 'transferts_edition_tableau_empr');
		$this->add_column('expl_owner', 'transferts_edition_tableau_expl_owner');
		$this->add_column('transfert_ask_formatted_date', 'transferts_popup_ask_date');
		if($this->filters['site_origine'] == 0 || $transferts_edition_show_all_colls) {
			$this->add_column('source', 'transferts_edition_tableau_source');
		}
		if($this->filters['site_destination'] == 0 || $transferts_edition_show_all_colls) {
			$this->add_column('destination', 'transferts_edition_tableau_destination');
		}
		$this->add_column('motif', 'transferts_edition_tableau_motif');
		$this->add_column('transfert_ask_user_num', 'transferts_edition_ask_user');
		$this->add_column('transfert_send_user_num', 'transferts_edition_send_user');

	}
	
	protected function get_display_spreadsheet_title() {
		global $msg, $sub;
		$this->spreadsheet->write_string(0,0,$msg["transferts_edition_titre"]." : ".$msg["transferts_edition_".$sub]);
	}
		
	protected function get_html_title() {
		global $msg, $sub;
		return "<h1>".$msg["transferts_edition_titre"]."&nbsp;:&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
	}
}