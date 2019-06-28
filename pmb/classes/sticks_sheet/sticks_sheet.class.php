<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheet.class.php,v 1.4 2017-01-19 14:25:39 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/sticks_sheet/sticks_sheet.tpl.php");
require_once($class_path."/encoding_normalize.class.php");

/**
 * Planche d'étiquettes
 */
class sticks_sheet {
	
	/**
	 * Identifiant
	 * @var int
	 */
	protected $id;
	
	/**
	 * Libellé
	 * @var string
	 */
	protected $label;
	
	/**
	 * Format (ex : A4)
	 * @var string
	 */
	protected $page_format;
	
	/**
	 * Portrait / Paysage
	 * @var string
	 */
	protected $page_orientation;
	
	/**
	 * Unité
	 * @var float
	 */
	protected $unit;
	
	/**
	 * Nombre d'étiquettes en largeur
	 * @var int
	 */
	protected $nbr_x_sticks;
	
	/**
	 * Nombre d'étiquettes en hauteur
	 * @var int
	 */
	protected $nbr_y_sticks;
	
	/**
	 * Largeur de l'étiquette
	 * @var float
	 */
	protected $stick_width;
	
	/**
	 * Hauteur de l'étiquette
	 * @var float
	 */
	protected $stick_height;
	
	/**
	 * Marge de gauche
	 * @var float
	 */
	protected $left_margin;
	
	/**
	 * Marge du haut
	 * @var float
	 */
	protected $top_margin;

	/**
	 * Espace horizontal entre 2 étiquettes
	 * @var float
	 */
	protected $x_sticks_spacing;
	
	/**
	 * Espacement vertical entre 2 étiquettes
	 * @var float
	 */
	protected $y_sticks_spacing;
	
	/**
	 * Position courante de l'étiquette (unité : étiquette)
	 * @var int
	 */
	protected $x_stick;
	
	/**
	 * Position courante de l'étiquette (unité : étiquette)
	 */
	protected $y_stick;
	
	/**
	 * Numéro d'ordre
	 */
	protected $order;
	
	/**
	 * Tailles du format de la page
	 */
	protected $page_sizes;
	
	public function __construct($id=0) {
		$this->id = $id*1;
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->label = 'Standard - 38.1x21.2mm - Avery J8651';
		$this->page_format = 'A4';
		$this->page_orientation = 'P';
		$this->unit = 'mm';
		$this->nbr_x_sticks = '5';
		$this->nbr_y_sticks = '13';
		$this->stick_width = '38.1';
		$this->stick_height = '21.2';
		$this->left_margin = '5.5';
		$this->top_margin = '11.3';
		$this->x_sticks_spacing = '40.75';
		$this->y_sticks_spacing = '21.2';
		$this->order = 0;
		$this->page_sizes = array('210','297');
		if($this->id) {
			$query = "select * from sticks_sheets where id_sticks_sheet = ".$this->id;
			$result = pmb_mysql_query($query);
			$row = pmb_mysql_fetch_object($result);
			$this->label = $row->sticks_sheet_label;
			$this->set_data(json_decode($row->sticks_sheet_data, true));
			$this->order = $row->sticks_sheet_order;
			$this->set_page_sizes();
		}
	}
	
	protected function set_data($data) {
		if (is_array($data)) {
			foreach ($data as $property=>$value) {
				if(property_exists($this, $property)) {
					$this->{$property} = $value;
				}
			}
		}
	}
	
	protected function set_page_sizes() {
		switch ($this->page_format) {
			case 'A3':
				$this->page_sizes = array('297','420');
				break;
			case 'A4':
				$this->page_sizes = array('210','297');
				break;
			case 'A5':
				$this->page_sizes = array('148','210');
				break;
			case 'Letter':
				$this->page_sizes = array('215.9','279.4');
				break;
			case 'Legal':
				$this->page_sizes = array('355.6','216');
				break;
		}
		if($this->page_orientation == 'L') {
			$this->page_sizes = array_reverse($this->page_sizes);
		}
	}
	
	protected function gen_selector_page_format() {
		global $charset;
		$selector = '';
		$page_size=array("A3","A4","A5","Letter","Legal");
		foreach ($page_size as $size) {
			$selector .="<option value='".$size."' ".($this->page_format == $size ? "selected='selected'" : "").">".htmlentities($size, ENT_QUOTES, $charset)."</option>";
		}
		return $selector;
	}
	
	protected function gen_selector_page_orientation() {
		global $msg, $charset;
		$selector = '';
		$page_orientation=array('P' => $msg['edit_cbgen_mep_portrait'], 'L' => $msg['edit_cbgen_mep_paysage']);
		foreach ($page_orientation as $key=>$orientation) {
			$selector .="<option value='".$key."' ".($this->page_orientation == $key ? "selected='selected'" : "").">".htmlentities($orientation, ENT_QUOTES, $charset)."</option>";
		}
		return $selector;
	}
	
	public function get_form() {
		global $msg;
		global $base_path;
		global $sticks_sheet_form;
		
		$form = $sticks_sheet_form;
		
		$form = str_replace('!!label!!', $this->label, $form);
		$form = str_replace('!!unit!!', $this->unit, $form);
		$form = str_replace('!!page_format!!', $this->gen_selector_page_format(), $form);
		$form = str_replace('!!page_orientation!!', $this->gen_selector_page_orientation(), $form);
		$form = str_replace('!!nbr_x_sticks!!', $this->nbr_x_sticks, $form);
		$form = str_replace('!!nbr_y_sticks!!', $this->nbr_y_sticks, $form);
		$form = str_replace('!!stick_width!!', $this->stick_width, $form);
		$form = str_replace('!!stick_height!!', $this->stick_height, $form);
		$form = str_replace('!!left_margin!!', $this->left_margin, $form);
		$form = str_replace('!!top_margin!!', $this->top_margin, $form);
		$form = str_replace('!!x_sticks_spacing!!', $this->x_sticks_spacing, $form);
		$form = str_replace('!!y_sticks_spacing!!', $this->y_sticks_spacing, $form);
		$form = str_replace('!!id!!', $this->id, $form);
		if($this->id) {
			$form = str_replace('!!button_delete!!', "<input type='button' class='bouton' id='sticks_sheet_button_delete' name='sticks_sheet_button_delete' value='".$msg['supprimer']."' onclick=\"if(sticks_sheet_delete()) {document.location='".$base_path."/edit.php?categ=sticks_sheet&sub=models&action=delete&id=".$this->id."'}\" />", $form);
		} else {
			$form = str_replace('!!button_delete!!', "", $form);
		}		
		return $form;
	}
	
	public function set_properties_from_form() {
		global $sticks_sheet_label;
		global $sticks_sheet_page_format;
		global $sticks_sheet_page_orientation;
		global $sticks_sheet_unit;
		global $sticks_sheet_nbr_x_sticks;
		global $sticks_sheet_nbr_y_sticks;
		global $sticks_sheet_stick_width;
		global $sticks_sheet_stick_height;
		global $sticks_sheet_left_margin;
		global $sticks_sheet_top_margin;
		global $sticks_sheet_x_sticks_spacing;
		global $sticks_sheet_y_sticks_spacing;
		
		$this->label = stripslashes($sticks_sheet_label);
		$this->page_format = $sticks_sheet_page_format;
		$this->page_orientation = $sticks_sheet_page_orientation;
		$this->unit = $sticks_sheet_unit;
		$this->nbr_x_sticks = $sticks_sheet_nbr_x_sticks;
		$this->nbr_y_sticks = $sticks_sheet_nbr_y_sticks;
		$this->stick_width = $sticks_sheet_stick_width;
		$this->stick_height = $sticks_sheet_stick_height;
		$this->left_margin = $sticks_sheet_left_margin;
		$this->top_margin = $sticks_sheet_top_margin;
		$this->x_sticks_spacing = $sticks_sheet_x_sticks_spacing;
		$this->y_sticks_spacing = $sticks_sheet_y_sticks_spacing;
		$this->set_page_sizes();
	}
	
	public function get_data() {
		return array(
			'id' => $this->id,
			'label' => $this->label,
			'page_format' => $this->page_format,
			'page_orientation' => $this->page_orientation,
			'unit' => $this->unit,
			'nbr_x_sticks' => $this->nbr_x_sticks,
			'nbr_y_sticks' => $this->nbr_y_sticks,
			'stick_width' => $this->stick_width,
			'stick_height' => $this->stick_height,
			'left_margin' => $this->left_margin,
			'top_margin' => $this->top_margin,
			'x_sticks_spacing' => $this->x_sticks_spacing,
			'y_sticks_spacing' => $this->y_sticks_spacing,
			'page_sizes' => $this->page_sizes
		);
	}
	
	protected function get_next_order() {
		$query = "select max(sticks_sheet_order)+1 as next_order from sticks_sheets";
		$result = pmb_mysql_query($query);
		$row = pmb_mysql_fetch_object($result);
		return $row->next_order*1;
	}
	
	public function save() {
		if($this->id) {
			$query = "update sticks_sheets set ";
			$clause = "where id_sticks_sheet = ".$this->id;
		} else {
			$query = "insert into sticks_sheets set ";
			$clause = "";
			$this->order = $this->get_next_order();
		}
		$data = $this->get_data();
		unset($data['id']);
		unset($data['label']);
		$query .= "sticks_sheet_label = '".addslashes($this->label)."',
				sticks_sheet_data = '".encoding_normalize::json_encode($data)."',
				sticks_sheet_order = '".$this->order."' ";
		$query .= $clause;
		pmb_mysql_query($query);
	}
	
	public static function delete($id) {
		if($id) {
			$query = "delete from sticks_sheets where id_sticks_sheet =".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;
	}
	
	public function get_json_data() {
		return encoding_normalize::json_encode($this->get_data());
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function get_page_format() {
		return $this->page_format;
	}
	
	public function get_page_orientation() {
		return $this->page_orientation;
	}
	
	public function get_page_orientation_label() {
		global $msg;
		
		$label = '';
		switch ($this->page_orientation) {
			case 'P':
				$label = $msg['edit_cbgen_mep_portrait'];
				break;
			case 'L':
				$label = $msg['edit_cbgen_mep_paysage'];
				break;
		}
		return $label;
	}
	
	public function get_unit() {
		return $this->unit;
	}
	
	public function get_nbr_x_sticks() {
		return $this->nbr_x_sticks;
	}
	
	public function get_nbr_y_sticks() {
		return $this->nbr_y_sticks;
	}
	
	public function get_stick_width() {
		return $this->stick_width;
	}
	
	public function get_stick_height() {
		return $this->stick_height;
	}
	
	public function get_left_margin() {
		return $this->left_margin;
	}
	
	public function get_top_margin() {
		return $this->top_margin;
	}
	
	public function get_x_sticks_spacing() {
		return $this->x_sticks_spacing;
	}
	
	public function get_y_sticks_spacing() {
		return $this->y_sticks_spacing;
	}
	
	/**
	 * Retourne le bouton de sélection des planches d'étiquettes
	 * @param string $dialog_title Titre du dialog à ouvrir
	 * @param string $button_label Libellé du bouton
	 * @param string $source Source
	 * @param int $sticks_sheet_selected Identifiant de la plache d'étiquette à utiliser par défaut
	 * @return mixed[]
	 */
	public function get_display_stick_select_button ($dialog_title, $button_label, $source) {
		global $stick_sheet_stick_select_button, $stick_sheet_stick_select_button_script, $charset;
		
		$display = $stick_sheet_stick_select_button;
		$display = str_replace('!!button_label!!', htmlentities($button_label, ENT_QUOTES, $charset), $display);
		$display = str_replace('!!source!!', htmlentities($source, ENT_QUOTES, $charset), $display);
		$display = str_replace('!!sticksSheetSelected!!', $this->id, $display);
		
		$script = $stick_sheet_stick_select_button_script;
		$script = str_replace('!!dialog_title!!', htmlentities($dialog_title, ENT_QUOTES, $charset), $script);
		
		return array(
				'display' => $display,
				'script' => $script
		);
	}
}