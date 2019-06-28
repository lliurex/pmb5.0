<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_view_shelveslist.class.php,v 1.11.4.1 2018-02-12 15:57:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_view_shelveslist extends cms_module_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
	{% for shelve in shelves %}
		<h3>{{shelve.name}}</h3>
		{% if shelve.link_rss %}
			<a href='{{shelve.link_rss}}'>Flux RSS</a>
		{% endif %}
		<div>
			<blockquote>{{shelve.comment}}</blockquote>
			{{shelve.records}}
		</div>
	{% endfor %}
</div>";
	}
	
	public function get_form(){
		if(!isset($this->parameters['nb_notices'])) $this->parameters['nb_notices'] = '';
		if(!isset($this->parameters['django_directory'])) $this->parameters['django_directory'] = '';
		$form="
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_shelveslist_view_link'>".$this->format_text($this->msg['cms_module_common_view_shelveslist_build_shelve_link'])."</label>
			</div>
			<div class='colonne_suite'>";
		$form.= $this->get_constructor_link_form("shelve");
		$form.="
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_shelveslist_view_nb_notices'>".$this->format_text($this->msg['cms_module_common_view_shelveslist_build_shelve_nb_notices'])."</label>
			</div>
			<div class='colonne_suite'>
				<input type='number' name='cms_module_common_view_shelveslist_nb_notices' value='".$this->parameters["nb_notices"]."'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_common_view_shelveslist_django_directory'>".$this->format_text($this->msg['cms_module_common_view_shelveslist_django_directory'])."</label>
			</div>
			<div class='colonne-suite'>
				<select name='cms_module_common_view_shelveslist_django_directory'>";
		$form.= $this->get_directories_options($this->parameters['django_directory']);
		$form.= "
				</select>
			</div>
		</div>";
		$form.= parent::get_form();
		return $form;
	}
	
	public function save_form(){
		global $cms_module_common_view_shelveslist_nb_notices;
		global $cms_module_common_view_shelveslist_django_directory;
		$this->save_constructor_link_form("shelve");
		$this->parameters['nb_notices'] = $cms_module_common_view_shelveslist_nb_notices+0;
		$this->parameters['django_directory'] = $cms_module_common_shelveslist_view_django_directory;
		return parent::save_form();
	}
	
	public function render($datas){
		global $opac_etagere_notices_format;
		global $opac_etagere_order;
		
		// on g�re l'ordre des �tag�res, id asc par d�faut
		$critere = "id";
		$dir = "asc";
		if ($opac_etagere_order) {
			$etagere_order = explode(" ", trim($opac_etagere_order));
			if ((trim($etagere_order[0]) == 'name') || (trim($etagere_order[0]) == 'comment')) {
				$critere  =  trim($etagere_order[0]);
			}
			if (isset($etagere_order[1])) {
				$dir = strtolower(trim($etagere_order[1]));
			}
		}
		
		$order = array();
		foreach ($datas['shelves'] as $shelf) {
			$order[] = $shelf[$critere];
		}
		if ($dir == 'desc') array_multisort($order, SORT_DESC, $datas['shelves']);
		else array_multisort($order, SORT_ASC, $datas['shelves']);
		
		//on g�re l'affichage des notices
		foreach($datas["shelves"] as $i => $shelve) {
			$datas['shelves'][$i]['records'] = contenu_etagere($shelve['id'],$this->parameters["nb_notices"],$opac_etagere_notices_format,"",1,'./index.php?lvl=etagere_see&id=!!id!!',$this->parameters['django_directory']);
			$datas['shelves'][$i]['cart_link'] = $this->get_constructed_link('shelve_to_cart', $shelve['id']);
		}
		//on rappelle le tout...
		return parent::render($datas);
	}
	
	public function get_format_data_structure(){	
		$format_datas= array(
			array(
				'var' => "shelves",
				'desc' => $this->msg['cms_modulecommon_view_shelveslist_desc'],
				'children' => array(
					array(
						'var' => "shelves[i].id",
						'desc'=> $this->msg['cms_modulecommon_view_shelveslist_id_desc']
					),
					array(
						'var' => 'shelves[i].cart_link',
						'desc' => $this->msg['cms_modulecommon_view_shelveslist_desc'],
					),
					array(
						'var' => "shelves[i].name",
						'desc'=> $this->msg['cms_modulecommon_view_shelveslist_name_desc']
					),
					array(
							'var' => "shelves[i].link",
							'desc'=> $this->msg['cms_modulecommon_view_shelveslist_link_desc']
					),
					array(
						'var' => "shelves[i].link_rss",
						'desc'=> $this->msg['cms_modulecommon_view_shelveslist_link_rss_desc']
					),
					array(
						'var' => "shelves[i].comment",
						'desc'=> $this->msg['cms_modulecommon_view_shelveslist_comment_desc']
					),
					array(
						'var' => "shelves[i].records",
						'desc'=> $this->msg['cms_modulecommon_view_shelveslist_records_desc']
					)	
				)
			)
		);
		$format_datas = array_merge($format_datas,parent::get_format_data_structure());
		return $format_datas;
	}
	
	public function get_directories_options($selected = '') {
		global $opac_notices_format_django_directory;
	
		if (!$selected) {
			$selected = $opac_notices_format_django_directory;
		}
		if (!$selected) {
			$selected = 'common';
		}
		$dirs = array_filter(glob('./opac_css/includes/templates/record/*'), 'is_dir');
		$tpl = "";
		foreach($dirs as $dir){
			if(basename($dir) != "CVS"){
				$tpl.= "<option ".(basename($dir) == basename($selected) ? "selected='selected'" : "")." value='".basename($dir)."'>
				".basename($dir)."</option>";
			}
		}
		return $tpl;
	}
}