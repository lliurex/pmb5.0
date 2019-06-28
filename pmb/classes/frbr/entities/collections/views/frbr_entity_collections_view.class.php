<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_collections_view.class.php,v 1.2 2017-05-05 07:43:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_collections_view extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
<h3>{{collection.name}}</h3>
<blockquote>{{collection.comment}}</blockquote>
</div>";
	}
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_collections_view_title"];
		$collection = new collection($datas[0]);
		$render_datas['collection'] = $collection->format_datas();
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_collections_view_title']
		);
		$collection = array(
			'var' => "collection",
			'desc' => $this->msg['frbr_entity_collections_view_label'],
			'children' => $this->prefix_var_tree(collection::get_format_data_structure(),"collection")
		);
		$format[] = $collection;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}