<?php
// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_param.class.php,v 1.2 2017-05-18 14:33:48 ngantier Exp $
if (stristr($_SERVER ['REQUEST_URI'], ".class.php"))
	die("no access");

require_once ($include_path . '/templates/contribution_area/contribution_area_param.tpl.php');

/**
 * class contribution_area
 * Représente un espace de contribution
 */
class contribution_area_param {
	
	public function __construct() {
	} // end of member function __construct
	


	public function get_form() {
		global $contribution_area_param_form;
		global $pmb_contribution_ws_url, $pmb_contribution_ws_username, $pmb_contribution_ws_password, $pmb_contribution_opac_show_sub_form;
		
		$contribution_area_param_form = str_replace('!!user_name!!', ($pmb_contribution_ws_username ? $pmb_contribution_ws_username : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!user_password!!', ($pmb_contribution_ws_password ? $pmb_contribution_ws_password : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!source_url!!', ($pmb_contribution_ws_url ? $pmb_contribution_ws_url : ""), $contribution_area_param_form);
		$contribution_area_param_form = str_replace('!!show_sub_form!!', ($pmb_contribution_opac_show_sub_form ? "checked='checked'" : ""), $contribution_area_param_form);
		return $contribution_area_param_form;
	}

	public function save_from_form(){
		global $source_url, $user_name, $user_password, $show_sub_form;
		global $pmb_contribution_ws_url, $pmb_contribution_ws_username, $pmb_contribution_ws_password, $pmb_contribution_opac_show_sub_form;
		
		$query = "UPDATE parametres SET valeur_param = '".addslashes($user_name)."' WHERE sstype_param = 'contribution_ws_username'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($user_password)."' WHERE sstype_param = 'contribution_ws_password'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($source_url)."' WHERE sstype_param = 'contribution_ws_url'";
		pmb_mysql_query($query);
		$query = "UPDATE parametres SET valeur_param = '".addslashes($show_sub_form)."' WHERE sstype_param = 'contribution_opac_show_sub_form'";
		pmb_mysql_query($query);
		
		$pmb_contribution_ws_url = $source_url; 
		$pmb_contribution_ws_username = $user_name; 
		$pmb_contribution_ws_password = $user_password;
		$pmb_contribution_opac_show_sub_form = $show_sub_form;
	}
} // end of contribution_area
