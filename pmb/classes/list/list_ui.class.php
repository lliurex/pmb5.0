<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_ui.class.php,v 1.5.2.2 2017-12-22 14:34:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/list/list_ui.tpl.php");

class list_ui {
	
	/**
	 * Type d'objet
	 * @var string
	 */
	protected $objects_type;
	
	/**
	 * Liste des objets
	 */
	protected $objects;
	
	/**
	 * Tri appliqué
	 */
	protected $applied_sort;
	
	/**
	 * Filtres
	 * @var array
	 */
	protected $filters;
	
	/**
	 * Pagination
	 * @var array
	 */
	protected $pager;
	
	/**
	 * Colonnes
	 */
	protected $columns;
	
	protected $spreadsheet;
	
	/**
	 * Message d'information pour l'utilisateur
	 * @var string
	 */
	protected $messages;

	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		$this->objects_type = str_replace('list_', '', get_class($this));
		$this->init_filters($filters);
		$this->init_pager($pager);
		$this->init_applied_sort($applied_sort);
		$this->fetch_data();
		$this->_sort();
		$this->_limit();
		$this->init_columns();
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$this->messages = "";
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		foreach ($this->filters as $key => $val){
			if(isset($_SESSION['list_'.$this->objects_type.'_filter'][$key])) {
				$this->filters[$key] = $_SESSION['list_'.$this->objects_type.'_filter'][$key];
			}
		}
		if(count($filters)){
			foreach ($filters as $key => $val){
				$this->filters[$key]=$val;
			}
		}
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => 15,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	/**
	 * Initialisation de la pagination
	 */
	public function init_pager($pager=array()) {
		$this->init_default_pager();
		if(isset($_SESSION['list_'.$this->objects_type.'_pager']['nb_per_page'])) {
			$this->pager['nb_per_page'] = $_SESSION['list_'.$this->objects_type.'_pager']['nb_per_page'];
		}
		if(count($pager)){
			foreach ($pager as $key => $val){
				$this->pager[$key]=$val;
			}
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'id',
				'asc_desc' => 'desc'
		);
	}
	
	/**
	 * Initialisation du tri appliqué
	 */
	public function init_applied_sort($applied_sort=array()) {
		$this->init_default_applied_sort();
		if(isset($_SESSION['list_'.$this->objects_type.'_applied_sort']['by'])) {
			$this->applied_sort['by'] = $_SESSION['list_'.$this->objects_type.'_applied_sort']['by'];
			if(isset($_SESSION['list_'.$this->objects_type.'_applied_sort']['asc_desc'])) {
				$this->applied_sort['asc_desc'] = $_SESSION['list_'.$this->objects_type.'_applied_sort']['asc_desc'];
			} else {
				$this->applied_sort['asc_desc'] = 'asc';
			}
		}
		if(count($applied_sort)){
			foreach ($applied_sort as $key => $val){
				$this->applied_sort[$key]=$val;
			}
		}
		//Sauvegarde du tri appliqué en session
		$this->set_applied_sort_in_session();
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		//Sauvegarde des filtres en session
		$this->set_filter_in_session();
	}
	
	/**
	 * Pagination provenant du formulaire
	 */
	public function set_pager_from_form() {
		$page = $this->objects_type.'_page';
		global ${$page};
		$nb_per_page = $this->objects_type.'_nb_per_page';
		global ${$nb_per_page};
		
		if(${$page}*1) {
			$this->pager['page'] = ${$page}*1;
		}
		if(${$nb_per_page}*1) {
			$this->pager['nb_per_page'] = ${$nb_per_page}*1;
		}
		//Sauvegarde de la pagination en session
		$this->set_pager_in_session();
	}
	
	protected function get_title() {
		return '';
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg[$this->objects_type.'_form_title'], ENT_QUOTES, $charset);
	}
	
	protected function get_form_name() {
		return $this->objects_type."_search_form";
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $list_ui_search_form_tpl;
		
		$search_form = $list_ui_search_form_tpl;
		$search_form = str_replace('!!form_title!!', $this->get_form_title(), $search_form);
		$search_form = str_replace('!!form_name!!', $this->get_form_name(), $search_form);
		$search_form = str_replace('!!json_filters!!', json_encode($this->filters), $search_form);
		$search_form = str_replace('!!page!!', $this->pager['page'], $search_form);
		$search_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $search_form);
		$search_form = str_replace('!!pager!!', json_encode($this->pager), $search_form);
		$search_form = str_replace('!!messages!!', $this->get_messages(), $search_form);
		$search_form = str_replace('!!objects_type!!', $this->objects_type, $search_form);
		$search_form = str_replace('!!export_icons!!', $this->get_export_icons(), $search_form);
		
		return $search_form;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		return '';
	}
	
	/**
	 * Limit SQL
	 */
	protected function _get_query_pager() {
		
		$limit_query = '';
		
		$this->set_pager_from_form();
		
		$limit_query .= ' limit '.(($this->pager['page']-1)*$this->pager['nb_per_page']).', '.$this->pager['nb_per_page'];
		
		return $limit_query;
	}
	
	protected function intcmp($a,$b) {
	    if((int)$a == (int)$b)return 0;
	    else if((int)$a  > (int)$b)return 1;
	    else if((int)$a  < (int)$b)return -1;
	}
	
	/**
	 * Fonction de callback
	 * @param $a
	 * @param $b
	 */
	protected function _compare_objects($a, $b) {

	}
	
	/**
	 * Tri des objets
	 */
	protected function _sort() {
		if($this->applied_sort['asc_desc'] == 'desc') {
			usort($this->objects, array($this, "_compare_objects"));
			$this->objects= array_reverse($this->objects);
		} else {
			usort($this->objects, array($this, "_compare_objects"));
		}
	}
	
	/**
	 * Limite des demandes
	 */
	protected function _limit() {
	
		$this->set_pager_from_form();
		
		$this->objects = array_slice(
				$this->objects, 
				($this->pager['page']-1)*$this->pager['nb_per_page'], 
				$this->pager['nb_per_page']);
	}
	
	protected function add_column($property, $label = '', $html = '') {
		$this->columns[] = array(
			'property' => $property,
			'label' => $label,
			'html' => $html
		);
		
	}
	
	/**
	 * Initialisation des colonnes par défaut
	 */
	protected function init_default_columns() {
		$this->columns = array();
	}
	
	protected function init_columns($columns=array()) {
// 		if(isset($_SESSION['list_'.$this->objects_type.'_columns'])) {
// 			$this->columns = $_SESSION['list_'.$this->objects_type.'_columns'];
// 		} else {
			$this->init_default_columns();
// 		}
		if(count($columns)){
			foreach ($columns as $key => $val){
				$this->columns[$key]=$val;
			}
		}
		$this->set_columns_in_session();
	}
	
	/**
	 * Colonnes provenant du formulaire
	 */
	public function set_columns_from_form() {
		//Sauvegarde des colonnes en session
		$this->set_columns_in_session();
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $list_ui_js_sort_script_sort;
	
		$display = $list_ui_js_sort_script_sort;
		$display = str_replace('!!objects_type!!', $this->objects_type, $display);
		return $display;
	}
	
	protected function _get_label_cell_header($name) {
		global $msg, $charset;
		global $current_module;
		
		if(isset($msg[$current_module.'_'.$this->objects_type.'_'.$name])) {
			return htmlentities($msg[$current_module.'_'.$this->objects_type.'_'.$name],ENT_QUOTES,$charset);
		} elseif(isset($msg[$name])) {
			return $msg[$name];
		} else {
			return $name;
		}
	}
	
	/**
	 * Construction dynamique des cellules du header 
	 * @param string $name
	 */
	protected function _get_cell_header($name, $label = '') {
		global $msg, $charset;
		$data_sorted = ($this->applied_sort['asc_desc'] ? $this->applied_sort['asc_desc'] : 'asc');
		$icon_sorted = ($data_sorted == 'asc' ? '<i class="fa fa-sort-desc"></i>' : '<i class="fa fa-sort-asc"></i>');
		if($name) {
			return "
			<th onclick=\"".$this->objects_type."_sort_by('".$name."', this.getAttribute('data-sorted'));\" data-sorted='".($this->applied_sort['by'] == $name ? $data_sorted : '')."' style='cursor:pointer;' title='".htmlentities($msg['sort_by'], ENT_QUOTES, $charset).' '.$this->_get_label_cell_header($label)."'>
					".$this->_get_label_cell_header($label)."
					".($this->applied_sort['by'] == $name ? $icon_sorted : '<i class="fa fa-sort"></i>')."
			</th>";
		} else {
			return "<th>".$this->_get_label_cell_header($label)."</th>";
		}
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		$display = '<tr>';
		foreach ($this->columns as $column) {
			$display .= $this->_get_cell_header($column['property'], $column['label']);
		}
		$display .= '</tr>';
	
		return $display;
	}
	
	/**
	 * Contenu d'une colonne
	 * @param unknown $object
	 * @param string $property
	 */
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
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
	
	/**
	 * Affichage d'une colonne avec du HTML non calculé
	 * @param string $value
	 */
	protected function get_display_cell_html_value($object, $value) {
		$value = str_replace('!!id!!', $object->get_id(), $value);
		$display = "<td align='center'>".$value."</td>";
		return $display;
	}
	
	/**
	 * Affichage d'une colonne
	 * @param unknown $object
	 * @param string $property
	 */
	protected function get_display_cell($object, $property) {
		$display = "<td align='center'>".$this->get_cell_content($object, $property)."</td>";
		return $display;
	}
	
	/**
	 * Liste des objets
	 */
	public function get_display_content_list() {
		$display = '';
		foreach ($this->objects as $i=>$object) {
			$display .= "
				<tr class='".($i % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($i % 2 ? 'odd' : 'even')."'\">";
			foreach ($this->columns as $column) {
				if($column['html']) {
					$display .= $this->get_display_cell_html_value($object, $column['html']);
				} else {
					$display .= $this->get_display_cell($object, $column['property']);
				}
			}
			$display .= "</tr>";
		}
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $msg, $charset;
		global $base_path;
	
		$display = $this->get_title();
		
		// Affichage du formulaire de recherche
		$display .= $this->get_search_form();
	
		// Affichage de la human_query
		$display .= $this->_get_query_human();
	
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
	
	protected function pager() {
		global $msg;
		
		if (!$this->pager['nb_results']) return;
		
		$this->pager['nb_page']=ceil($this->pager['nb_results']/$this->pager['nb_per_page']);
		$suivante = $this->pager['page']+1;
		$precedente = $this->pager['page']-1;
		
		$nav_bar = '';
		// affichage du lien précédent si nécéssaire
		if($precedente > 0) {
			$nav_bar .= "<a href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=".$precedente."; document.".$this->get_form_name().".submit(); return false;\"><img src='./images/left.gif' border='0'  title='$msg[48]' alt='[$msg[48]]' hspace='3' align='middle'></a>";
		}
		$deb = $this->pager['page'] - 10 ;
		if ($deb<1) $deb=1;
		for($i = $deb; ($i <= $this->pager['nb_page']) && ($i <= $this->pager['page']+10); $i++) {
			if($i==$this->pager['page']) $nav_bar .= "<strong>".$i."</strong>";
			else $nav_bar .= "<a href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=".$i."; document.".$this->get_form_name().".submit(); return false;\">".$i."</a>";
			if($i<$this->pager['nb_page']) $nav_bar .= " ";
		}
		if($suivante <= $this->pager['nb_page']) {
			$nav_bar .= "<a href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=".$suivante."; document.".$this->get_form_name().".submit(); return false;\"><img src='./images/right.gif' border='0' title='$msg[49]' alt='[$msg[49]]' hspace='3' align='middle'></a>";
		}
		if($this->pager['nb_page'] && ($this->pager['nb_results'] > $this->pager['nb_per_page'])) {
			$nav_bar .= " | ".$msg['per_page']." ";
			$nav_bar .= "<a href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=25; document.".$this->get_form_name().".submit(); return false;\"> 25 </a>";
			$nav_bar .= "<a href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=50; document.".$this->get_form_name().".submit(); return false;\"> 50 </a>";
			$nav_bar .= "<a href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=100; document.".$this->get_form_name().".submit(); return false;\"> 100 </a>";
		}
		// affichage de la barre de navigation
		return "<div align='center'><br />".$nav_bar."<br /></div>";
	}
	
	protected function _get_query_human() {
		return '';
	}
	
	public function get_export_icons() {
		global $base_path;
		global $msg;
		return "
			<script type='text/javascript'>
				function survol(obj){
					obj.style.cursor = 'pointer';
				}
				function start_export(type){
					document.forms['".$this->get_form_name()."'].dest.value = type;
					document.forms['".$this->get_form_name()."'].submit();
					document.forms['".$this->get_form_name()."'].dest.value = '';
				}	
			</script>
			<img  src='".$base_path."/images/tableur.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='".$msg['export_tableur']."' title='".$msg['export_tableur']."'/>&nbsp;&nbsp;
			<img  src='".$base_path."/images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."'/>
			<input type='hidden' name='dest' value='' />
		";
	}
	
	protected function get_display_spreadsheet_title() {
		
	}
	
	/**
	 * Header de la liste du tableur
	 */
	protected function get_display_spreadsheet_header_list() {
		$j=0;
		foreach ($this->columns as $column) {
			$this->spreadsheet->write_string(2,$j++,$this->_get_label_cell_header($column['label']));
		}
	}
	
	protected function get_display_spreadsheet_cell($object, $property, $row, $col) {
		$this->spreadsheet->write_string($row,$col, strip_tags($this->get_cell_content($object, $property)));
	}
	
	/**
	 * Liste des objets du tableur
	 */
	public function get_display_spreadsheet_content_list() {
		$ligne=3;
		foreach ($this->objects as $object) {
			$j=0;
			foreach ($this->columns as $column) {
				$this->get_display_spreadsheet_cell($object, $column['property'], $ligne, $j++);
			}
			$ligne++;
		}
	}
	
	public function get_display_spreadsheet_list() {
		$this->spreadsheet = new spreadsheet();
		$this->get_display_spreadsheet_title();
		$this->get_display_spreadsheet_header_list();
		if(count($this->objects)) {
			$this->get_display_spreadsheet_content_list();
		}
// 		$worksheet->set_column(0, $j, 18);
		$this->spreadsheet->download('edition.xls');
	}
	
	protected function get_html_title() {
		return '';
	}
	
	/**
	 * Header de la liste du tableau
	 */
	protected function get_display_html_header_list() {
		$display = '<tr>';
		foreach ($this->columns as $column) {
			$display .= "<th>".$this->_get_label_cell_header($column['label'])."</th>";
		}
		$display .= '</tr>';
	
		return $display;
	}
	
	protected function get_display_html_cell($object, $property) {
		$display = "<td align='center'>".strip_tags($this->get_cell_content($object, $property))."</td>";
		return $display;
	}
	
	/**
	 * Liste des objets du tableau HTML
	 */
	public function get_display_html_content_list() {
		$display = '';
		foreach ($this->objects as $i=>$object) {
			$display .= "
				<tr class='".($i % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($i % 2 ? 'odd' : 'even')."'\">";
			foreach ($this->columns as $column) {
				if($column['html']) {
					$display .= "<td></td>";
				} else {
					$display .= $this->get_display_html_cell($object, $column['property']);
				}
			}
			$display .= "</tr>";
		}
		return $display;
	}
	
	public function get_display_html_list() {
		global $msg, $charset;
		global $base_path;
	
		$display = $this->get_html_title();
		
		// Affichage de la human_query
		$display .= $this->_get_query_human();
	
		//Affichage de la liste des objets
		$display .= "<table id='".$this->objects_type."_list' border='1' style='border-collapse: collapse'>";
		$display .= $this->get_display_html_header_list();
		if(count($this->objects)) {
			$display .= $this->get_display_html_content_list();
		}
		$display .= "</table>";
		return $display;
	}
	
	/**
	 * Sauvegarde des filtres en session
	 */
	public function set_filter_in_session() {
		foreach ($this->filters as $name=>$filter) {
			$_SESSION['list_'.$this->objects_type.'_filter'][$name] = $filter;
		}
	}
	
	/**
	 * Sauvegarde de la pagination en session
	 */
	public function set_pager_in_session() {
		$_SESSION['list_'.$this->objects_type.'_pager']['nb_per_page'] = $this->pager['nb_per_page'];
	}
	
	/**
	 * Sauvegarde du tri appliqué en session
	 */
	public function set_applied_sort_in_session() {
		foreach ($this->applied_sort as $name=>$applied_sort) {
			$_SESSION['list_'.$this->objects_type.'_applied_sort'][$name] = $applied_sort;
		}
	}
	
	/**
	 * Sauvegarde des colonnes en session
	 */
	public function set_columns_in_session() {
		foreach ($this->columns as $name=>$column) {
			$_SESSION['list_'.$this->objects_type.'_columns'][$name] = $column;
		}
	}
	
	public function get_objects_type() {
		return $this->objects_type;
	}
	
	public function get_objects() {
		return $this->objects;
	}
	
	public function get_applied_sort() {
		return $this->applied_sort;
	}
	
	public function get_filters() {
		return $this->filters;
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function set_objects_type($objects_type) {
		$this->objects_type = $objects_type;
	}
	
	public function set_objects($objects) {
		$this->objects = $objects;
	}
	
	public function set_applied_sort($applied_sort) {
		$this->applied_sort = $applied_sort;
	}

	public function set_filters($filters) {
		$this->filters = $filters;
	}
	
	public function set_messages($messages) {
		$this->messages = $messages;
	}
	
	public static function unset_filters(){
		if(isset($_SESSION['list_'.str_replace('list_', '', get_called_class()).'_filter'])) {
			unset($_SESSION['list_'.str_replace('list_', '', get_called_class()).'_filter']);
		}
	}
}