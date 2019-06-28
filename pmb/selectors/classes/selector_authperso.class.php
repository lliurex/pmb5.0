<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_authperso.class.php,v 1.4 2017-07-20 12:54:52 ngantier Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_authperso.tpl.php");

class selector_authperso extends selector_authorities {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	protected function get_form() {
		global $authperso_id;
		
		$authperso = new authperso($authperso_id);
		return $authperso->get_form_select(0,static::get_base_url());
	}
	
	protected function get_add_label() {
		global $msg;
	
		return $msg['authperso_sel_add'];
	}
	
	protected function save() {
		global $authperso_id;
		
		$authperso = new authperso($authperso_id);
		$id=$authperso->update_from_form();
		if($authperso->get_cp_error_message()){
			print '<span class="erreur">'.$authperso->get_cp_error_message().'</span>';
		}
		return $id;
	}
	
	protected function get_display_list() {
		global $authperso_id;
		global $id;
		global $base_url;
		global $type_autorite;
		global $nb_per_page, $rech_regexp;
		
		$authperso_id += 0;
		$id += 0;
		$type_autorite += 0;
		$base_url = static::get_base_url()."&rech_regexp=$rech_regexp&user_input=".rawurlencode($this->user_input)."&type_autorite=".$type_autorite;
		
		$authperso=new authperso($authperso_id);
		$display_list = $authperso->get_list_selector($id,$this->get_link_pagination(),$nb_per_page);
		return $display_list;
	}
	
	protected function get_link_pagination() {
		global $rech_regexp;
		global $type_autorite;
		
		$type_autorite += 0;
		$link = static::get_base_url()."&rech_regexp=$rech_regexp&user_input=".rawurlencode($this->user_input)."&type_autorite=".$type_autorite;
		return $link;
	}
	
	public function get_title() {
		global $msg;
		return $msg["authperso_sel_title"];
	}
	
	public static function get_params_url() {
		global $p3, $p4, $p5, $p6, $authperso_id, $perso_id;
	
		$params_url = parent::get_params_url();
		$params_url .= ($p3 ? "&p3=".$p3 : "").($p4 ? "&p4=".$p4 : "").($p5 ? "&p5=".$p5 : "").($p6 ? "&p6=".$p6 : "").($authperso_id ? "&authperso_id=".$authperso_id : "").($perso_id ? "&perso_id=".$perso_id : "");
		return $params_url;
	}
}
?>