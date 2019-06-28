<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: templates.class.php,v 1.1 2017-02-24 09:18:55 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class templates {
	
	/**
	 * Fonction de génération de champs autocomplété
	 * @param string $name
	 * @param integer $id
	 * @param integer $index
	 * @param integer $value
	 * @param string $label
	 * @param string $completion
	 */
	public static function get_input_completion($name, $id, $index, $value, $label, $completion){
		global $msg;
		$template = "
			<input type='text' completion='".$completion."' autfield='".$id.$index."' id='".$name."' class='saisie-30emr' name='".$name."' data-form-name='".$name."' value=\"".$label."\" />
			<input type='button' class='bouton' value='".$msg['raz']."' onclick=\"document.getElementById('".$name."').value=''; document.getElementById('".$id.$index."').value='0'; \" />
			<input type='hidden' name='".$id."' data-form-name='".$id.$index."' id='".$id.$index."' value=\"".$value."\" />
			<script type='text/javascript'>
				ajax_pack_element(document.getElementById('".$name."'));
			</script>
		";
		return $template;
	}
	
	public static function get_button_add($onclick_event=''){
		$template = "
			<input type='button' class='bouton' value='+' onclick=\"".$onclick_event."\" />
		";
		return $template;
	}
	
	public static function get_input_hidden($name, $value) {
		$template = "<input type='hidden' id='".$name."' name='".$name."' value=\"".$value."\" />";
		return $template;
	}
	
}