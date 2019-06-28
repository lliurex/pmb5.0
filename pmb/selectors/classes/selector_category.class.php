<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_category.class.php,v 1.3 2017-06-30 09:43:08 apetithomme Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/category.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path."/authority.class.php");
require_once($class_path."/thesaurus.class.php");

if($autoindex_class) {
	require_once($class_path."/autoindex/".$autoindex_class.".class.php");
}

class selector_category extends selector_authorities {
	
	protected $thesaurus_id;
	
	public function __construct($user_input=''){
		parent::__construct($user_input);
	}

	public function proceed() {
		global $action;
		global $pmb_allow_authorities_first_page;
		global $search_type, $jscript_term;
		global $user_input;
		
		print $this->get_sel_header_template();
		switch($action){
			default:
				print $this->get_search_form();
				print $this->get_js_script();
				if($search_type == "autoindex"){
					print $jscript_term;
					print $this->display_autoindex_list();
				} else {
					if($pmb_allow_authorities_first_page || $this->user_input!= ""){
						if(!$this->user_input) {
							$this->user_input = '*';
						}
					}
					switch ($search_type) {
						case "term":
							$src='term_browse.php';
							break;
						default:
							$src='category_browse.php';
							break;
					}
					print "
					<script type='text/javascript' >
						parent.document.getElementsByTagName( 'frameset' )[ 0 ].rows = '135,*' ;
						parent.category_browse.location='".str_replace('../select.php', $src, static::get_base_url()).($user_input ? "&user_input=".rawurlencode(stripslashes($user_input)) : "")."';
					</script>\n";
				}
				break;
		}
		print $this->get_sel_footer_template();
	}
	
	protected function get_thesaurus_id() {
		global $caller, $dyn;
		global $id_thes_unique;
		global $perso_id, $id_thes;
	
		if(!isset($this->thesaurus_id)) {
			if($id_thes_unique>0) {
				$this->thesaurus_id=$id_thes_unique;
			} else{
				//recuperation du thesaurus session en fonction du caller
				switch ($caller) {
					case 'notice' :
						if($id_thes) $this->thesaurus_id = $id_thes;
						else $this->thesaurus_id = thesaurus::getNoticeSessionThesaurusId();
						if (!$perso_id) thesaurus::setNoticeSessionThesaurusId($this->thesaurus_id);
						break;
					case 'categ_form' :
						if($id_thes) $this->thesaurus_id = $id_thes;
						else $this->thesaurus_id = thesaurus::getSessionThesaurusId();
						if( $dyn!=2) thesaurus::setSessionThesaurusId($this->thesaurus_id);
						break;
					default :
						if($id_thes) $this->thesaurus_id = $id_thes;
						else $this->thesaurus_id = thesaurus::getSessionThesaurusId();
						thesaurus::setSessionThesaurusId($this->thesaurus_id);
						break;
				}
			}
		}
		return $this->thesaurus_id;
	}
	
	protected function get_thesaurus_selector() {
		global $msg, $charset;
		global $caller, $dyn;
		global $thesaurus_mode_pmb, $id_thes_unique;
		global $search_type;

		$id_thes = $this->get_thesaurus_id();
		
		$liste_thesaurus = thesaurus::getThesaurusList();
		
		$sel_thesaurus = '';
		if ($thesaurus_mode_pmb != 0 && !$id_thes_unique) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
			$sel_thesaurus = "<select class='saisie-20em' id='id_thes' name='id_thes' ";
		
			//si on vient du form de categories, le choix du thesaurus n'est pas possible
			if($caller == 'categ_form' && $dyn!=2) {
				$sel_thesaurus.= "disabled ";
			}
			if($search_type!='autoindex') {
				$sel_thesaurus.= "onchange = \"this.form.submit()\">" ;
			} else {
				$sel_thesaurus.= '>' ;
			}
			foreach($liste_thesaurus as $id_thesaurus=>$libelle_thesaurus) {
				$sel_thesaurus.= "<option value='".$id_thesaurus."' "; ;
				if ($id_thesaurus == $id_thes) $sel_thesaurus.= " selected";
				$sel_thesaurus.= ">".htmlentities($libelle_thesaurus,ENT_QUOTES,$charset)."</option>";
			}
			$sel_thesaurus.= "<option value=-1 ";
			if ($id_thes == -1) $sel_thesaurus.= "selected ";
			$sel_thesaurus.= ">".htmlentities($msg['thes_all'],ENT_QUOTES, $charset)."</option>";
			$sel_thesaurus.= "</select>&nbsp;";
		}
		return $sel_thesaurus;
	}
	
	protected function get_autoindex_form(){
		global $autoindex_class;
		if(!$autoindex_class) return;
		$autoindex=new $autoindex_class();
		return $autoindex->get_form();
	}
	
	protected function display_autoindex_list(){
		global $autoindex_class;
	
		if(!$autoindex_class) return;
		$autoindex=new $autoindex_class();
		return $autoindex->index_list();
	}
	
	protected function get_search_form() {
		global $msg, $charset;
		global $search_type;
		
		$sel_search_form = parent::get_search_form();
		$sel_search_form=str_replace("!!sel_thesaurus!!", $this->get_thesaurus_selector(),$sel_search_form);
		// indexation auto
		$sel_search_form=str_replace("!!sel_index_auto!!", $this->get_autoindex_form(),$sel_search_form);

		switch ($search_type) {
			case "term":
				$sel_search_form=str_replace("!!t_checked!!","checked",$sel_search_form);
				$sel_search_form=str_replace("!!h_checked!!","",$sel_search_form);
				$sel_search_form=str_replace("!!autoindex_checked!!","",$sel_search_form);
				$sel_search_form=str_replace("!!display_search_part!!","block",$sel_search_form);
				break;	
			case "autoindex":
				$sel_search_form=str_replace("!!t_checked!!","",$sel_search_form);
				$sel_search_form=str_replace("!!h_checked!!","",$sel_search_form);
				$sel_search_form=str_replace("!!autoindex_checked!!","checked",$sel_search_form);
				$sel_search_form=str_replace("!!display_search_part!!","none",$sel_search_form);
				break;	
			default:
				$sel_search_form=str_replace("!!h_checked!!","checked",$sel_search_form);
				$sel_search_form=str_replace("!!t_checked!!","",$sel_search_form);
				$sel_search_form=str_replace("!!autoindex_checked!!","",$sel_search_form);
				$sel_search_form=str_replace("!!display_search_part!!","block",$sel_search_form);
				break;
		}
		return $sel_search_form;
	}
	
	protected function get_searcher_instance() {
		return searcher_factory::get_searcher('categories', '', $this->user_input);
	}
		
	public function get_sel_search_form_template() {
		global $msg, $charset;
	
		$sel_search_form ="
			<script type='text/javascript'>
			<!--
			function test_form(form){
				if(form.f_user_input.value.length == 0){
					return true;
				}
				return true;
			}
			-->
			</script>
			<form name='search_form' method='post' action='".str_replace('../select.php', 'category.php', static::get_base_url())."' >
				!!sel_thesaurus!!
				<div id='search_part' style='display:!!display_search_part!!'>
					<input type='text' name='f_user_input' value=\"".htmlentities($this->user_input,ENT_QUOTES,$charset)."\" />
					&nbsp;
					<input type='submit' class='bouton_small' value='$msg[142]' onclick='return test_form(this.form)' />
				</div>	
				<br />	
				<input type='radio' value='hierarchy' name='search_type' !!h_checked!! onClick=\"this.form.submit()\" />
				&nbsp;".$msg["term_search_type_h"]."&nbsp;
				<input type='radio' value='term' name='search_type' !!t_checked!! onClick=\"this.form.submit()\" />
				&nbsp;".$msg["term_search_type_t"]."
				!!sel_index_auto!!
			</form>
			<script type='text/javascript'>
			<!--
				document.forms['search_form'].elements['f_user_input'].focus();
			-->
			</script>";
		return $sel_search_form;
	}
	
	public static function get_params_url() {
		global $perso_id, $keep_tilde, $parent, $id_thes_unique;
		global $id2, $id_thes, $user_input, $f_user_input;
		
		if(!$parent) $parent=0;
		if(!$user_input) $user_input = $f_user_input;
		
		$params_url = parent::get_params_url();
		$params_url .= ($perso_id ? "&perso_id=".$perso_id : "").($keep_tilde ? "&keep_tilde=".$keep_tilde : "").($parent ? "&parent=".$parent : "").($id_thes_unique ? "&id_thes_unique=".$id_thes_unique : "")."&autoindex_class=autoindex_record";
		$params_url .= ($id2 ? "&id2=".$id2 : "").($id_thes ? "&id_thes=".$id_thes : "");
		return $params_url;
	}
}
?>