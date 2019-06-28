<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_subcollection.class.php,v 1.3.2.1 2017-09-12 13:23:19 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_sub_collection.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path.'/subcollection.class.php');
require_once($class_path."/authority.class.php");

class selector_subcollection extends selector_authorities {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}
	
	protected function get_form() {
		global $charset;
		global $sub_collection_form;
		$sub_collection_form = str_replace("!!deb_saisie!!", htmlentities($this->user_input,ENT_QUOTES,$charset), $sub_collection_form);
		$sub_collection_form = str_replace("!!base_url!!",static::get_base_url(),$sub_collection_form);
		return $sub_collection_form;
	}
	
	protected function save() {
		global $collection_nom;
		global $coll_id;
		global $issn;
		
		$value['name']		=	$collection_nom;
		$value['parent']	=	$coll_id;
		$value['issn'] = $issn;
		$collection = new subcollection();
		$collection->update($value);
		return $collection->id;
	}
	
	protected function get_display_object($authority_id=0, $object_id=0) {
		global $msg, $charset;
		global $caller;
		global $callback;
		
		$display = '';
		$authority = new authority($authority_id, $object_id, AUT_TABLE_SUB_COLLECTIONS);
		$subcollection = $authority->get_object_instance();
		
		$libellesubcoll = htmlentities(addslashes($subcollection->name),ENT_QUOTES,$charset);
		$idparentcoll = $subcollection->parent;
		$idparentlibelle = htmlentities(addslashes($subcollection->parent_libelle),ENT_QUOTES,$charset);
		$idediteur = $subcollection->editeur;
		$libelleediteur = htmlentities(addslashes($subcollection->editeur_libelle),ENT_QUOTES,$charset);
		
		$display .= pmb_bidi($authority->get_display_statut_class_html()."
		<a href='#' onclick=\"set_parent('$caller', '".$authority->get_num_object()."', '".$libellesubcoll."','$callback', $idparentcoll, '".$idparentlibelle."', $idediteur, '".$libelleediteur."')\">
			".$subcollection->name."</a>");
		$display .= pmb_bidi("&nbsp;(".$subcollection->name.".&nbsp;".$subcollection->editeur_libelle.")<br />");
		return $display;
	}
	
	protected function get_searcher_instance() {
		return searcher_factory::get_searcher('subcollections', '', $this->user_input);
	}
	
	public static function get_params_url() {
		global $p3, $p4, $p5, $p6, $mode;
	
		$params_url = parent::get_params_url();
		$params_url .= ($p3 ? "&p3=".$p3 : "").($p4 ? "&p4=".$p4 : "").($p5 ? "&p5=".$p5 : "").($p6 ? "&p6=".$p6 : "").($mode ? "&mode=".$mode : "");
		return $params_url;
	}
}
?>