<?PHP
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_category.class.php,v 1.2.2.1 2017-09-15 08:46:37 dgoron Exp $
  
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($base_path."/selectors/classes/selector_authorities.class.php");
require($base_path."/selectors/templates/category.tpl.php");
require_once($class_path.'/searcher/searcher_factory.class.php');
require_once($class_path."/authority.class.php");
require_once($class_path."/thesaurus.class.php");

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
		global $opac_thesaurus, $id_thes_unique;
		global $search_type;

		$id_thes = $this->get_thesaurus_id();
		
		$liste_thesaurus = thesaurus::getThesaurusList();
		
		$sel_thesaurus = '';
		if ($opac_thesaurus != 0 && !$id_thes_unique) {	 //la liste des thesaurus n'est pas affichée en mode monothesaurus
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
		return "";
	}
	
	protected function display_autoindex_list(){
		return "";
	}
	
	protected function get_search_form() {
		$sel_search_form = parent::get_search_form();
		$sel_search_form=str_replace("!!sel_thesaurus!!", $this->get_thesaurus_selector(),$sel_search_form);
		return $sel_search_form;
	}
	
	protected function get_display_object($authority_id=0, $object_id=0) {
		global $msg, $charset, $base_path;
		global $caller;
		global $callback;
		global $keep_tilde;
		global $opac_thesaurus;
		$display = '';
		$authority = new authority($authority_id, $object_id, AUT_TABLE_CATEG);
		$tcateg = $authority->get_object_instance();
	
		if (!$tcateg->is_under_tilde ||($tcateg->voir_id)||($keep_tilde)) {
			$not_use_in_indexation=$tcateg->not_use_in_indexation;
			$display .= "<div class='row'>";
	
			$authority = new authority(0,$tcateg->id, AUT_TABLE_CATEG);
			$display.= $authority->get_display_statut_class_html();
				
			if($this->get_thesaurus_id() == -1 && $opac_thesaurus){
				$label_display = '['.htmlentities($tcateg->thes->libelle_thesaurus,ENT_QUOTES, $charset).']';
			} else {
				$label_display = '';
			}
			if($tcateg->voir_id) {
				$tcateg_voir = new category($tcateg->voir_id);
				$label_display .= "$tcateg->libelle -&gt;<i>".$tcateg_voir->catalog_form."@</i>";
				$id_=$tcateg->voir_id;
				$not_use_in_indexation=$tcateg_voir->not_use_in_indexation;
				$libelle_=$tcateg_voir->libelle;
			} else {
				$id_=$tcateg->id;
				$libelle_=$tcateg->libelle;
				$label_display .= $tcateg->libelle;
			}
			if ($tcateg->commentaire) {
				$zoom_comment = "<div id='zoom_comment".$tcateg->id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>".htmlentities($tcateg->commentaire,ENT_QUOTES, $charset)."</div>" ;
				$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment".$tcateg->id."'); z.style.display='none'; \"" ;
			} else {
				$zoom_comment = "" ;
				$java_comment = "" ;
			}
			if ($opac_thesaurus) $nom_tesaurus='['.$tcateg->thes->getLibelle().'] ' ;
			else $nom_tesaurus='' ;
			if($not_use_in_indexation){
				$display .= "<img src='$base_path/images/interdit.gif' hspace='3' border='0'/>&nbsp;";
				$display .= $label_display;
				$display .=$zoom_comment."\n";
				$display .= "</td></tr>";
			}else{
				$display .= "<a href='#' $java_comment onclick=\"set_parent('$caller', '$id_', '".htmlentities(addslashes($nom_tesaurus.$libelle_),ENT_QUOTES, $charset)."','$callback','".$tcateg->thes->id_thesaurus."')\">";
				$display .= $label_display;
				$display .= "</a>$zoom_comment\n";
				$display .= "</div>";
			}
		}
		return $display;
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