<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector.class.php,v 1.3.2.2 2017-12-11 15:26:56 jpermanne Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class selector {
	protected $user_input;
	
	protected $nbr_lignes;
	
	public function __construct($user_input=''){
		$this->user_input = static::format_user_input($user_input);
	}

	public function proceed() {
		global $page;

		print $this->get_sel_header_template();
		print $this->get_search_form();
		print $this->get_js_script();
		if(!$this->user_input) {
			$this->user_input = '*';
		}
		show_results($this->user_input, $this->nbr_lignes, $page, 0);
		print $this->get_sel_footer_template();
	}
	
	protected function get_form() {
		$form = '';
		return $form;
	}
	
	protected function save() {
		
	}
	
	protected function get_search_form() {
		$sel_search_form = $this->get_sel_search_form_template();
		return $sel_search_form;
	}
	
	protected function get_js_script() {
		global $jscript;
		global $jscript_common_selector;
		global $param1, $param2, $p1, $p2;
		global $infield;
		
		if(!isset($jscript)) $jscript = $jscript_common_selector;
		$jscript = str_replace('!!param1!!', ($param1 ? $param1 : $p1), $jscript);
		$jscript = str_replace('!!param2!!', ($param2 ? $param2 : $p2), $jscript);
		$jscript = str_replace('!!infield!!', $infield, $jscript);
		return $jscript;
	}
	
	protected function get_display_object($id=0, $object_id=0) {
	
	}
	
	protected function get_display_list() {
		
	}
	
	protected function get_message_not_found() {
	}
	
	protected function get_link_pagination() {
		$link = static::get_base_url()."&user_input=".rawurlencode($this->user_input);
		return $link;
	}
	
	public function get_pagination() {
		global $nb_per_page;
		global $page;
		
		// constitution des liens
		$nbepages = ceil($this->nbr_lignes/$nb_per_page);
		if(!$page) {
			$page = 1;
		}
		$suivante = $page+1;
		$precedente = $page-1;
		
		// affichage du lien précédent si nécéssaire
		$pagination = "<div class='row'>&nbsp;<hr /></div><div align='center'>";
		$pagination .= aff_pagination ($this->get_link_pagination(), $this->nbr_lignes, $nb_per_page, $page, 10, false, true) ;
		$pagination .= "</div>";
		return $pagination;
	}
	
	public function get_title() {
		global $msg;
		return $msg[get_called_class()];
	}
	
	public function get_sel_header_template() {
		global $charset;
		global $base_path;
		
		$sel_header = "
			<div id='att' style='z-Index:1000'></div>		
			<script src='".$base_path."/javascript/ajax.js'></script>
			<div class='row'>
				<label for='selector_title' class='etiquette'>".htmlentities($this->get_title(),ENT_QUOTES,$charset)."</label>
				</div>
			<div class='row'>
			";
		return $sel_header;
	}
	
	public function get_sel_search_form_template() {
		global $msg, $charset;
		
		$sel_search_form ="
			<form name='search_form' method='post' action='".static::get_base_url()."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
			</form>
			<script type='text/javascript'>
				<!--
				document.forms['search_form'].elements['f_user_input'].focus();
				-->
			</script>
			<hr />
		";
		return $sel_search_form;
	}
	
	public function get_sel_footer_template() {
		$sel_footer = "</div>";
		return $sel_footer;
	}
	
	// traitement en entrée des requêtes utilisateur
	public static function format_user_input($user_input='') {
		global $deb_rech;
		global $f_user_input;
	
		if ($deb_rech) {
			$user_input = stripslashes($deb_rech);
		} else {
			if(!$user_input) {
				if($f_user_input) {
					$user_input = stripslashes($f_user_input);
				}
			}
		}
		return $user_input;
	}
	
	public static function get_params_url() {
		global $param1, $param2, $p1, $p2;
		
		$params_url = ($param1 ? "&param1=".urlencode($param1) : "").($param2 ? "&param2=".urlencode($param2) : "").($p1 ? "&p1=".urlencode($p1) : "").($p2 ? "&p2=".urlencode($p2) : "");
		return $params_url;
	}
	
	public static function get_base_url() {
		global $base_path;
		global $what, $caller;
		global $no_display, $bt_ajouter, $dyn, $callback, $infield;
		global $max_field, $field_id, $field_name_id, $add_field;
	
		// gestion d'un élément à ne pas afficher
		if (!$no_display) $no_display=0;
		
		$base_url = $base_path."/select.php?what=".$what."&caller=".$caller;
		$base_url .= static::get_params_url();
		if($no_display) 	$base_url .= "&no_display=".$no_display;
		if($bt_ajouter) 	$base_url .= "&bt_ajouter=".$bt_ajouter;
		if($dyn) 			$base_url .= "&dyn=".$dyn;
		if($callback) 		$base_url .= "&callback=".$callback;
		if($infield) 		$base_url .= "&infield=".$infield;
		if($max_field) 		$base_url .= "&max_field=".$max_field;
		if($field_id) 		$base_url .= "&field_id=".$field_id;
		if($field_name_id) 	$base_url .= "&field_name_id=".$field_name_id;
		if($add_field) 		$base_url .= "&add_field=".$add_field;
		return $base_url;
	}
	
}
?>