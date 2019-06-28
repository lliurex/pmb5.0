<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_bannettes_abon_ui.class.php,v 1.1.2.2 2017-11-14 14:31:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_bannettes_ui.class.php");

class list_bannettes_abon_ui extends list_bannettes_ui {
	
	protected $id_empr;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
		
	protected function get_title() {
		global $msg;
		
		$title = "<h3><span>";
		if($this->filters['proprio_bannette']) {
			$title .= $msg['dsi_bannette_gerer_priv'];
		} else {
			$title .= $msg['dsi_bannette_gerer_pub'];
		}
		$title .= "</span></h3>\n";
		return $title;
	}
	
	protected function get_form_title() {
		return '';
	}
	
	protected function init_default_columns() {
		global $sub;
	
		$this->add_column('subscribed', 'dsi_bannette_gerer_abonn');
		$this->add_column('name', 'dsi_bannette_gerer_nom_liste');
		$this->add_column('aff_date_last_envoi', 'dsi_bannette_gerer_date');
		$this->add_column('nb_notices', 'dsi_bannette_gerer_nb_notices');
		$this->add_column('periodicite', 'dsi_bannette_gerer_periodicite');
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $msg, $charset;
		global $base_path;
	
		$display = $this->get_title();
	
		//Récupération du script JS de tris
		$display .= $this->get_js_sort_script_sort();
	
		//Affichage de la liste des objets
		$display .= "<table id='".$this->objects_type."_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->objects)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		$display .= $this->pager();
		$display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
			</div>
			<div class='right'>
			</div>
		</div>";
		return $display;
	}
	
	protected function get_link_to_bannette($id_bannette, $proprio_bannette) {
		if($proprio_bannette) {
			return "./dsi.php?categ=bannettes&sub=abo&id_bannette=".$id_bannette."&suite=modif";
		} else {
			return "./dsi.php?categ=bannettes&sub=pro&id_bannette=".$id_bannette."&suite=acces";
		}
	}
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'subscribed':
				$content .= "<input class='checkbox_bannette_abon' id_bannette='".$object->id_bannette."' type='checkbox' name='bannette_abon[".$object->id_bannette."]' value='1' " .(!$this->filters['proprio_bannette'] && $object->is_subscribed($this->id_empr) ? "checked='checked'" : ""). " />";
				break;
			case 'name':
				// Construction de l'affichage de l'info bulle de la requette
				$requete = "select * from bannette_equation, equations where num_equation = id_equation and num_bannette = ".$object->id_bannette;
				$resultat = pmb_mysql_query($requete);
				if (($r = pmb_mysql_fetch_object($resultat))) {
					$recherche = $r->requete;
					$equ = new equation ($r->num_equation);
					if (!isset($search) || !is_object($search)) $search = new search();
					$search->unserialize_search($equ->requete);
					$recherche = $search->make_human_query();
					$zoom_comment = "<div id='zoom_comment" . $object->id_bannette . "' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'>";
					$zoom_comment.= $recherche;
					$zoom_comment.= "</div>";
					$java_comment = " onmouseover=\"z=document.getElementById('zoom_comment" . $object->id_bannette . "'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_comment" . $object->id_bannette . "'); z.style.display='none'; \"";
				}
				$content .= "<a href = \"" . str_replace("!!id_bannette!!", $object->id_bannette, $this->get_link_to_bannette($object->id_bannette, $object->proprio_bannette)) . "\" $java_comment >";
				$content .= htmlentities($object->comment_public, ENT_QUOTES, $charset);
				$content .= "</a>";
				$content .= $zoom_comment;
// 				if ($tableau_bannettes[$i]['my_categ']) {
// 					$content .= " / ".$tableau_bannettes[$i]['my_categ'];
// 				}
// 				if ($tableau_bannettes[$i]['my_group']) {
// 					$content .= " / ".$tableau_bannettes[$i]['my_group'];
// 				}
// 				$content .= "<b>".aff_exemplaire($object->{$property})."</b>";
				break;
			default :
				if (is_object($object) && isset($object->{$property})) {
					$content .= $object->{$property};
				} elseif(method_exists($object, 'get_'.$property)) {
					$content .= call_user_func_array(array($object, "get_".$property), array());
				}
				break;
		}
		return $content;
	}
	
	public function set_id_empr($id_empr) {
		$this->id_empr = $id_empr+0;
	}
}