<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_view_workslist.class.php,v 1.5 2017-05-16 09:00:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_view_workslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for work in works %}
<h3>{{work.name}}</h3>
<blockquote>{{work.comment}}</blockquote>
{% endfor %}
</div>";
	}
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_works_view_workslist_title"];
		$render_datas['works'] = array();
		if(is_array($datas)){
			foreach($datas as $work_id){
				$titre_uniforme = new titre_uniforme($work_id);
				$infos= $titre_uniforme->format_datas();
				$render_datas['works'][]=$infos;
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_works_view_title']
		);
		$works = array(
			'var' => "works",
			'desc' => $this->msg['frbr_entity_works_view_works_desc'],
			'children' => $this->prefix_var_tree(titre_uniforme::get_format_data_structure(),"works[i]")
		);
		$format[] = $works;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}