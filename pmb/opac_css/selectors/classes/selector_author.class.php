<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_author.class.php,v 1.2.2.4 2017-12-22 14:20:17 jpermanne Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/sel_author.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path.'/author.class.php');
require_once($class_path."/authority.class.php");

class selector_author extends selector_authorities {
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	protected function get_form() {
		global $msg, $charset;
		global $author_form;
		global $type_autorite;
		
		$sel_pp = "";
		$sel_coll = "";
		$sel_con = "";
		switch($type_autorite){
			case 70 :
				$sel_pp = "selected";
				$author_form = str_replace("!!titre_ajout!!",$msg['selector_author_add'],$author_form);
				$author_form = str_replace("!!display!!","display:none",$author_form);
				$completion=' ';
				break;
			case 71 :
				$sel_coll = "selected";
				$author_form = str_replace("!!titre_ajout!!",$msg["aut_ajout_collectivite"],$author_form);
				$author_form = str_replace("!!display!!","display:inline",$author_form);
				$completion='collectivite_name';
				break;
			case 72 :
				$sel_con="selected";
				$author_form = str_replace("!!titre_ajout!!",$msg["aut_ajout_congres"],$author_form);
				$author_form = str_replace("!!display!!","display:inline",$author_form);
				$completion='congres_name';
				break;
			default :
				$author_form = str_replace("!!titre_ajout!!",$msg['selector_author_add'],$author_form);
				$author_form = str_replace("!!display!!","display:none",$author_form);
				$completion=' ';
				break;
		}
		
		$author_form = str_replace("!!sel_pp!!",$sel_pp,$author_form);
		$author_form = str_replace("!!sel_coll!!",$sel_coll,$author_form);
		$author_form = str_replace("!!sel_con!!",$sel_con,$author_form);
		$author_form = str_replace("!!completion_name!!",$completion,$author_form);
		$author_form = str_replace("!!deb_saisie!!", htmlentities($this->user_input,ENT_QUOTES,$charset), $author_form);
		$author_form = str_replace("!!base_url!!",static::get_base_url(),$author_form);
		return $author_form;
	}
	
	protected function get_add_link() {
		global $type_autorite;
	
		$link = parent::get_add_link();
		$link .= "&type_autorite=".$type_autorite;
		return $link;
	}
	
	protected function get_add_label() {
		global $msg;
		global $type_autorite;
		
		switch($type_autorite){
			case 70 : 
				$libelleBtn = $msg['selector_author_add'];
			break;
			case 71 : 
				$libelleBtn = $msg["aut_ajout_collectivite"];
			break;
			case 72 : 
				$libelleBtn = $msg["aut_ajout_congres"];
			break;
			default : 
				$libelleBtn = $msg['selector_author_add'];
			break; 		
		}
		return $libelleBtn;
	}
	
	protected function get_search_form() {
		global $msg, $charset;
		global $type_autorite;
	
		$sel_search_form = parent::get_search_form();
		$sel_pp = "";
		$sel_coll = "";
		$sel_con = "";
		$sel_all = "";
		switch($type_autorite){
			case 70 :
				$sel_pp = "selected";
				break;
			case 71 :
				$sel_coll = "selected";
				break;
			case 72 :
				$sel_con="selected";
				break;
			default :
				$sel_all = "selected";
				break;
		}
		$sel_search_form = str_replace("!!sel_pp!!",$sel_pp,$sel_search_form);
		$sel_search_form = str_replace("!!sel_coll!!",$sel_coll,$sel_search_form);
		$sel_search_form = str_replace("!!sel_con!!",$sel_con,$sel_search_form);
		$sel_search_form = str_replace("!!sel_all!!",$sel_all,$sel_search_form);
		return $sel_search_form;
	}
	
	protected function save() {
		global $author_type;
		global $author_name, $author_rejete;
		global $date, $lieu, $ville, $pays;
		global $subdivision, $numero;
		
		$value['type']		=	$author_type;
		$value['name']		=	$author_name;
		$value['rejete']	=	$author_rejete;
		$value['date']		=	$date;
		$value['voir_id']	=	0;
		$value['lieu']		=	$lieu;
		$value['ville']		=	$ville;
		$value['pays']		=	$pays;
		$value['subdivision']=	$subdivision;
		$value['numero']	=	$numero;

		$auteur = new auteur();
		$auteur->update($value);
		return $auteur->id;
	}
	
	protected function get_display_object($authority_id=0, $object_id=0) {
		global $msg, $charset;
		global $caller;
		global $callback;
		
		$display = '';
		$authority = new authority($authority_id, $object_id, AUT_TABLE_AUTHORS);
		$author = $authority->get_object_instance(array('recursif' => 1));
		$author_voir="" ;
		// gestion des voir :
		if($author->see) {
			$auteur_see = new auteur($author->see);
			$author_voir = $auteur_see->authority->get_display_statut_class_html()."<a href='#' onclick=\"set_parent('$caller', '$author->see', '".htmlentities(addslashes($auteur_see->get_isbd()),ENT_QUOTES, $charset)."','$callback')\">".htmlentities($auteur_see->get_isbd(),ENT_QUOTES, $charset)."</a>";
			$author_voir = ".&nbsp;-&nbsp;<i>".$msg['see']."</i>&nbsp;:&nbsp;".$author_voir;
		}
		$display .= "<div class='row'>";
		$display .= pmb_bidi($authority->get_display_statut_class_html()."<a href='#' onclick=\"set_parent('$caller', '".$authority->get_num_object()."', '".htmlentities(addslashes($author->get_isbd()),ENT_QUOTES, $charset)."','$callback')\">".$author->get_isbd()."</a>");
		$display .= pmb_bidi($author_voir );
		$display .= "</div>";
		return $display;
	}
	
	protected function get_searcher_instance() {
		return searcher_factory::get_searcher('authors', '', $this->user_input);
	}
	
	protected function get_link_pagination() {
		global $rech_regexp;
		global $type_autorite;
		
		$type_autorite += 0;
		$link = static::get_base_url()."&rech_regexp=$rech_regexp&user_input=".rawurlencode($this->user_input)."&type_autorite=".$type_autorite."&page=!!page!!";
		return $link;
	}
	
	public function get_sel_search_form_template() {
		global $msg, $charset;
	
		$sel_search_form ="
			<form name='search_form' method='post' action='".static::get_base_url()."'>
				<select id='type_autorite' name='type_autorite'>
					<option value='7' !!sel_all!!>".$msg['selector_author_type_all']."</option>
					<option value='70' !!sel_pp!!>".$msg['selector_author_type_pp']."</option>
					<option value='71' !!sel_coll!!>".$msg['selector_author_type_coll']."</option>
					<option value='72' !!sel_con!! >".$msg['selector_author_type_congres']."</option>
				</select>
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
	
	public function get_title() {
		global $msg;
		global $type_autorite;
		
		switch($type_autorite){
			case 70 :
				return $msg['selector_author'];
			break;
			case 71 : 
				return $msg["aut_select_coll"];
			break;
			case 72 : 
				return $msg["aut_select_congres"];
			break;
			default : 
				return $msg['selector_author'];
			break; 		
		}
	}
}
?>