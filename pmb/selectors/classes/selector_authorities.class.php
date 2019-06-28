<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_authorities.class.php,v 1.2 2017-01-19 10:25:16 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector.class.php");
// require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

class selector_authorities extends selector {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function proceed() {
		global $action;
		global $pmb_allow_authorities_first_page;
		
		print $this->get_sel_header_template();
		switch($action){
			case 'add':
				print $this->get_form();
				break;
			case 'update':
				$saved_id = $this->save();
				print $this->get_search_form();
				print $this->get_js_script();
				if($saved_id) {
					print $this->get_display_object(0, $saved_id);
				}
				break;
			default:
				print $this->get_search_form();
				print $this->get_js_script();
				if($pmb_allow_authorities_first_page || $this->user_input!= ""){
					if(!$this->user_input) {
						$this->user_input = '*';
					}
					print $this->get_display_list();
				}
				break;
		}
		print $this->get_sel_footer_template();
	}
	
	protected function get_add_link() {
		global $no_display;
		
		$link = static::get_base_url()."&action=add&deb_rech='+this.form.f_user_input.value+'&no_display=".$no_display;
		return $link;
	}
	
	protected function get_add_label() {
		global $msg;
		return $msg[get_called_class().'_add'];
	}
	
	protected function get_search_form() {
		global $charset;
		global $bt_ajouter;
		
		$sel_search_form = $this->get_sel_search_form_template();
		if($bt_ajouter == "no"){
			$sel_search_form = str_replace("!!bouton_ajouter!!", '', $sel_search_form);
		} else {
			$bouton_ajouter = "<input type='button' class='bouton_small' onclick=\"document.location='".$this->get_add_link()."'\" value='".htmlentities($this->get_add_label(), ENT_QUOTES, $charset)."' />";
			$sel_search_form = str_replace("!!bouton_ajouter!!", $bouton_ajouter, $sel_search_form);
		}
		return $sel_search_form;
	}
	
	protected function get_display_list() {
		global $nb_per_page;
		global $page;
		global $no_display;
		
		$display_list = '';
		if(!$page) {
			$debut = 0;
		} else {
			$debut = ($page-1)*$nb_per_page;
		}
		$searcher_instance = $this->get_searcher_instance();
		$this->nbr_lignes = $searcher_instance->get_nb_results();
		if($this->nbr_lignes) {
			$sorted_objects = $searcher_instance->get_sorted_result('default', $debut, $nb_per_page);
			foreach ($sorted_objects as $object_id) {
				$display_list .= $this->get_display_object($object_id);
			}
			$display_list .= $this->get_pagination();
		} else {
			$display_list .= $this->get_message_not_found();
		}
		return $display_list;
	}
	
	public function get_sel_search_form_template() {
		global $msg, $charset;
		
		$sel_search_form ="
			<form name='search_form' method='post' action='".static::get_base_url()."'>
				<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\">
				&nbsp;
				<input type='submit' class='bouton_small' value='".$msg[142]."' />
				!!bouton_ajouter!!
			</form>
			<script type='text/javascript'>
				<!--
				document.forms['search_form'].elements['f_user_input'].focus();
				-->
			</script>
		";
		return $sel_search_form;
	}
	
	protected function get_message_not_found() {
		global $msg;
		return $msg['no_'.str_replace('selector_', '', get_called_class()).'_found'];
	}
}
?>