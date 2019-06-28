<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transferts_ui.class.php,v 1.2 2017-03-02 13:13:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_ui.class.php');
require_once($class_path.'/transfert.class.php');
require_once($include_path.'/templates/list/list_transferts_ui.tpl.php');
require_once ($class_path."/mono_display.class.php");
require_once ($class_path."/serial_display.class.php");

class list_transferts_ui extends list_ui {
	
	protected $cp;
	
	protected $displayed_cp;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function fetch_data() {
		$this->objects = array();
		/* Conservation des anciens éléments du select circ
		 num_notice, num_bulletin, " .
		 "expl_cb as val_ex, lender_libelle, transferts.date_creation as val_date_creation, " .
		 "date_visualisee as val_date_accepte, motif as val_motif, location_libelle as val_dest, empr_cb as val_empr, transfert_ask_user_num, transfert_send_user_num "
		 */
		/* Conservation des anciens éléments du select éditions
			"num_notice as val_id_notice, num_bulletin as val_id_bulletin, ".
			"expl_cb as val_expl, expl_cote as val_cote, ". 
			"section_libelle as val_section , locd.location_libelle as val_dest, " .
			"loco.location_libelle as val_source, lender_libelle as val_expl_owner, motif as val_motif, " .
			"empr_cb as val_empr_cb, concat(empr_nom,' ',empr_prenom) as val_empr_nom_prenom, transfert_ask_user_num, transfert_send_user_num, transfert_ask_date, expl_id " .
		 */
		$query = 'select id_transfert from transferts
			INNER JOIN transferts_demande ON id_transfert=num_transfert
			INNER JOIN exemplaires ON num_expl=expl_id
			INNER JOIN docs_section ON expl_section=idsection
			INNER JOIN docs_location AS locd ON num_location_dest=locd.idlocation
			INNER JOIN docs_location AS loco ON num_location_source=loco.idlocation
			INNER JOIN lenders ON expl_owner=idlender
			LEFT JOIN resa ON resa_trans=id_resa
			LEFT JOIN empr ON resa_idempr=id_empr
			LEFT JOIN pret ON pret_idexpl=num_expl';
		$query .= $this->_get_query_filters();
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->objects[] = new transfert($row->id_transfert);
			}
			$this->pager['nb_results'] = count($this->objects);
		}
		$this->messages = "";
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $deflt_docs_location;
		/**
		 * etat_transfert => (0 = non fini)
		 * etat_demande => (0 = non validée, 1 = validée, 2 = envoyée, 3 = aller fini, 4 = refus)
		 * type_transfert => (1 = aller-retour)
		 */
		$this->filters = array(
				'site_origine' => $deflt_docs_location,
				'site_destination' => 0,
				'f_etat_date' => '',
				'f_etat_dispo' => '',
				'etat_transfert' => '',
				'etat_demande' => '',
				'type_transfert' => '',
				'ids' => ''
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		global $transferts_tableau_nb_lignes;
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => $transferts_tableau_nb_lignes,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $site_origine;
		global $site_destination;
		global $f_etat_date;
		global $f_etat_dispo;
		
		if(isset($site_origine) && $site_origine) {
			$this->filters['site_origine'] = $site_origine*1;
		}
		if(isset($site_destination) && $site_destination) {
			$this->filters['site_destination'] = $site_destination*1;
		}
		if(isset($f_etat_date)) {
			$this->filters['f_etat_date'] = $f_etat_date*1;
		}
		if(isset($f_etat_dispo)) {
			$this->filters['f_etat_dispo'] = $f_etat_dispo*1;
		}
		$numeros = '';
		foreach ($_REQUEST as $k => $v) {
			//si c'est une case a cocher d'une liste
			if ((substr($k,0,4)=="sel_") && ($v=="1")) {
				//le no de transfert
				$numeros .= substr($k,4,strlen($k)) . ",";
			}
		}
		if($numeros) {
			//on enleve la derniere virgule
			$numeros =  substr($numeros, 0, strlen($numeros)-1);
			$this->filters['ids'] = $numeros;
		}
		parent::set_filters_from_form();
	}
	
	protected function get_search_options_locations($loc_select,$tous = true) {
		global $msg;
	
		$options = '';
		$query = "SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle ";
		$result = pmb_mysql_query($query);
		if ($tous) {
			$options .= "<option value='0'>".$msg["all_location"]."</option>";
		}
		while ($row = pmb_mysql_fetch_object($result)) {
			$options .= "<option value='".$row->idlocation."' ".($row->idlocation==$loc_select ? "selected='selected'" : "").">";
			$options .= $row->location_libelle."</option>";
		}
		return $options;
	}
	
	protected function get_search_retour_filtre_etat_selector() {
		global $msg, $charset;
	
		$selector = "<select name='f_etat_date'>";
		$selector .= "<option value='0' ".($this->filters['f_etat_date'] == 0 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_tous"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='1' ".($this->filters['f_etat_date'] == 1 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_proche"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='2' ".($this->filters['f_etat_date'] == 2 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_depasse"], ENT_QUOTES, $charset)."</option>";
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_search_retour_filtre_etat_dispo_selector() {
		global $msg, $charset;
	
		$selector = "<select name='f_etat_dispo'>";
		$selector .= "<option value='1' ".($this->filters['f_etat_dispo'] == 1 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_dispo"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='2' ".($this->filters['f_etat_dispo'] == 2 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_circ"], ENT_QUOTES, $charset)."</option>";
		$selector .= "<option value='0' ".($this->filters['f_etat_dispo'] == 0 ? "selected='selected'" : "").">".htmlentities($msg["transferts_circ_retour_filtre_etat_tous"], ENT_QUOTES, $charset)."</option>";
		$selector .= "</select>";
		return $selector;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $transferts_nb_jours_alerte;
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['site_origine']) {
			$filters [] = 'num_location_source = "'.$this->filters['site_origine'].'"';
		}
		if($this->filters['site_destination']) {
			$filters [] = 'num_location_dest = "'.$this->filters['site_destination'].'"';
		}
		if($this->filters['f_etat_date']) {
			switch ($this->filters['f_etat_date']) {
				case "1":
					$filters [] = "(DATEDIFF(DATE_ADD(date_retour,INTERVAL -" . $transferts_nb_jours_alerte . " DAY),CURDATE())<=0
							AND DATEDIFF(date_retour,CURDATE())>=0)";
					break;
				case "2":
					$filters [] = "DATEDIFF(date_retour,CURDATE())<0";
					break;
			}
		}
		if($this->filters['f_etat_dispo']) {
			switch ($this->filters['f_etat_dispo']) {
				case 1 : // pas en pret et non réservé
					$filters [] = "if(id_resa, resa_confirmee=0, 1) and if(pret_idexpl,0 ,1) ";
					break;
				case 2 : // en pret et réservé seulement
					$filters [] = "( if(id_resa, resa_confirmee=1, 0) OR if(pret_idexpl,1 ,0) ) ";
					break;
			}
		}
		if($this->filters['etat_transfert'] !== '') {
			$filters [] = 'etat_transfert = "'.$this->filters['etat_transfert'].'"';
		}
		if(is_array($this->filters['etat_demande'])) {
			$filters [] = 'etat_demande IN ('.implode(',', $this->filters['etat_demande']).')';
		} elseif($this->filters['etat_demande'] !== '') {
			$filters [] = 'etat_demande = "'.$this->filters['etat_demande'].'"';
		}
		if($this->filters['type_transfert'] !== '') {
			$filters [] = 'type_transfert = "'.$this->filters['type_transfert'].'"';
		}
		if($this->filters['ids']) {
			$filters [] = 'id_transfert IN ('.$this->filters['ids'].')';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'transferts', $display);
		$display = str_replace('!!sub!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		
		$is_cp_property = false;
		if(isset($this->displayed_cp) && is_array($this->displayed_cp)) {
			$is_cp_property = array_search($property, $this->displayed_cp);
		}
		if($is_cp_property) {
			$this->cp->get_values($object->get_exemplaire()->expl_id);
			if(isset($this->cp->values[$is_cp_property])) {
				$values = $this->cp->values[$is_cp_property];
			} else {
				$values = array();
			}
			$aff_column=$this->cp->get_formatted_output($values, $is_cp_property);
			if (!$aff_column) $aff_column="&nbsp;";
			$content .= $aff_column;
		} else {
			switch($property) {
				case 'record':
					if($object->get_num_notice()) {
						$disp = new mono_display($object->get_num_notice());
					} else {
						$disp = new bulletinage_display($object->get_num_bulletin());
					}
					$content .= $disp->header;
					break;
				case 'section':
				case 'cote':
				case 'cb':
					$content .= $object->get_exemplaire()->{$property};
					break;
				case 'statut':
					$docs_statut = new docs_statut($object->get_exemplaire()->statut_id);
					$content .= aff_statut_exemplaire($docs_statut->libelle.'###'.$object->get_exemplaire()->expl_id);
					break;
				case 'empr':
					//TODO
					$id_resa = $object->get_transfert_demande()->get_resa_trans();
					if($id_resa) {
						$query = "select id_empr, empr_cb from empr join resa on id_empr = resa_idempr where id_resa = ".$id_resa;
						$result = pmb_mysql_query($query);
						if(pmb_mysql_num_rows($result) == 1) {
							$row = pmb_mysql_fetch_object($result);
							if (SESSrights & CIRCULATION_AUTH) {
								$content = "<a href='./circ.php?categ=pret&form_cb=".$row->empr_cb."'>";
								$content .= emprunteur::get_name($row->id_empr);
								$content .= "</a>";
							} else {
								$content .= emprunteur::get_name($row->id_empr);
							}
						}
					}
					break;
				case 'expl_owner':
					$lender = new lender($object->get_exemplaire()->owner_id);
					$content .= $lender->lender_libelle;
					break;
				case 'source':
					$docs_location = new docs_location($object->get_transfert_demande()->get_num_location_source());
					$content .= $docs_location->libelle;
					break;
				case 'destination':
					$docs_location = new docs_location($object->get_transfert_demande()->get_num_location_dest());
					$content .= $docs_location->libelle;
					break;
				case 'formatted_date_reception':
					$content .= $object->get_transfert_demande()->get_formatted_date_reception();
					break;
				case 'formatted_date_envoyee':
					$content .= $object->get_transfert_demande()->get_formatted_date_envoyee();
					break;
				case 'formatted_date_refus':
					$content .= $object->get_transfert_demande()->get_formatted_date_visualisee();
					break;
				case 'motif_refus':
					$content .= $object->get_transfert_demande()->get_motif_refus();
					break;
				case 'transfert_ask_user_num':
				case 'transfert_send_user_num':
					$content .= user::get_param(call_user_func_array(array($object, "get_".$property), array()), 'username');
					break;
				default :
					if (is_object($object) && isset($object->{$property})) {
						$content .= $object->{$property};
					} elseif(method_exists($object, 'get_'.$property)) {
						$content .= call_user_func_array(array($object, "get_".$property), array());
					}
					break;
			}	
		}
		return $content;
	}
	
	protected function get_edition_link() {
		global $msg;
		global $sub;
		$edition_link = '';
		//le lien pour l'édition si on a le droit ...
		if (SESSrights & EDIT_AUTH) {
			$sub_url = $sub;
			if($sub == 'departs') {
				switch (get_called_class()) {
					case 'list_transferts_envoi_ui':
						$sub_url = 'envoi';
						break;
					case 'list_transferts_retours_ui':
						$sub_url = 'retours';
						break;
					case 'list_transferts_validation_ui':
						$sub_url = 'validation';
						break;
				}
			}
			$url_edition = "./edit.php?categ=transferts&sub=".$sub_url;
			//on applique la seletion du filtre
			if ($this->filters['site_origine']) {
				$url_edition .= "&site_origine=" .$this->filters['site_origine'];
			}
			if ($this->filters['site_destination']) {
				$url_edition .= "&site_destination=" .$this->filters['site_destination'];
			}
			$edition_link = "<a href='" . $url_edition . "'>".$msg[1100]."</a>";
		}
		return $edition_link;
	}
	
	/**
	 * Affichage des éléments de recherche
	 */
	public function get_search_content() {
		global $list_transferts_ui_parcours_search_content_form_tpl;
	
		$content_form = $list_transferts_ui_parcours_search_content_form_tpl;
		
		$content_form = str_replace('!!nb_res!!', $this->pager['nb_per_page'], $content_form);
		$content_form = str_replace('!!filters!!', $this->get_search_filters(), $content_form);
		$content_form = str_replace('!!json_filters!!', json_encode($this->filters), $content_form);
		$content_form = str_replace('!!page!!', $this->pager['page'], $content_form);
		$content_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $content_form);
		$content_form = str_replace('!!pager!!', json_encode($this->pager), $content_form);
		$content_form = str_replace('!!objects_type!!', $this->objects_type, $content_form);
		$content_form = str_replace('!!edition_link!!', $this->get_edition_link(), $content_form);
		return $content_form;
	}
	
	protected function add_column_sel_button() {
		$this->columns[] = array(
				'property' => '',
				'label' => "<div align='center'><input type='button' class='bouton' name='+' onclick='SelAll(document.form_circ_".$this->objects_type.");' value='+'></div>",
				'html' => "<div align='center'><input type='checkbox' name='sel_!!id!!' value='1'></div>"
		);
	}
	
	public function get_display_list() {
		global $msg, $charset;
		global $base_path, $sub;
		global $current_module;
		global $list_transferts_ui_script_case_a_cocher;
		
		$display = '';
		if($current_module == 'circ') {
			$display .= "
			<br />
			<form name='form_".$current_module."_".$this->objects_type."' class='form-".$current_module."' method='post' action='".$base_path."/circ.php?categ=trans&sub=".$sub."'>
				".$this->get_form_title()."
				<div class='form-contenu' >";
			$display .= $this->get_search_content();
			if(count($this->objects)) {
				//Récupération du script JS de tris
				$display .= $this->get_js_sort_script_sort();
				//Affichage de la liste des objets
				$display .= "<table id='".$this->objects_type."_list'>";
				$display .= $this->get_display_header_list();
				$display .= $this->get_display_content_list();
				$display .= "</table>";
				$display .= $this->get_action_buttons();
				$display .= $this->pager();
			} else {
				$display .= $this->get_display_no_results();
			}
			$display .= "</div>";
			$display .= "<input type='hidden' name='action'>
			</form>
			".$list_transferts_ui_script_case_a_cocher;
		} else {
			$display .= parent::get_display_list();
		}
		return $display;
	}
	
	public function get_display_valid_list() {
		global $msg, $charset;
		global $base_path, $sub, $action;
		global $list_transferts_ui_valid_list_tpl;
		
		$display = $this->get_title();
		$display .= $list_transferts_ui_valid_list_tpl;
		
		$display = str_replace('!!submit_action!!', $base_path."/circ.php?categ=trans&sub=". $sub."&action=".str_replace('aff_', '', $action), $display);
		$display = str_replace('!!valid_form_title!!', $this->get_valid_form_title(), $display);
		$display_valid_list = $this->get_display_header_list();
		if(count($this->objects)) {
			$display_valid_list .= $this->get_display_content_list();
		}
		$display = str_replace('!!valid_list!!', $display_valid_list, $display);
		$display = str_replace('!!valid_action!!', $base_path."/circ.php?categ=trans&sub=". $sub, $display);
		$display = str_replace('!!ids!!', $this->filters['ids'], $display);
		$display = str_replace('!!objects_type!!', $this->objects_type, $display);
		return $display;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if($this->filters['site_origine']) {
			$docs_location = new docs_location($this->filters['site_origine']);
			$humans[] = "<b>".htmlentities($msg['transferts_edition_filtre_origine'], ENT_QUOTES, $charset)."</b> ".$docs_location->libelle;
		}
		if($this->filters['site_destination']) {
			$docs_location = new docs_location($this->filters['site_destination']);
			$humans[] = "<b>".htmlentities($msg['transferts_edition_filtre_destination'], ENT_QUOTES, $charset)."</b> ".$docs_location->libelle;
		}
		if($this->filters['f_etat_date']) {
			$option_label = '';
			switch ($this->filters['f_etat_date']) {
				case '1':
					$option_label = $msg["transferts_circ_retour_filtre_etat_proche"];
					break;
				case '2':
					$option_label = $msg["transferts_circ_retour_filtre_etat_depasse"];
					break;
			}
			$humans[] = "<b>".htmlentities($msg["transferts_circ_retour_filtre_etat"], ENT_QUOTES, $charset)."</b> ".htmlentities($option_label, ENT_QUOTES, $charset);
		}
		if($this->filters['f_etat_dispo']) {
			$option_label = '';
			switch ($this->filters['f_etat_dispo']) {
				case '1':
					$option_label = $msg["transferts_circ_retour_filtre_dispo"];
					break;
				case '2':
					$option_label = $msg["transferts_circ_retour_filtre_circ"];
					break;
			}
			$humans[] = "<b>".htmlentities($msg["transferts_circ_retour_filtre_dispo_title"], ENT_QUOTES, $charset)."</b> ".htmlentities($option_label, ENT_QUOTES, $charset);
		}
		$human_query = "<div align='left'><br />".implode(', ', $humans)." => ".sprintf(htmlentities($msg['searcher_results'], ENT_QUOTES, $charset), $this->pager['nb_results'])."<br /><br /></div>";
		return $human_query;
	}
}