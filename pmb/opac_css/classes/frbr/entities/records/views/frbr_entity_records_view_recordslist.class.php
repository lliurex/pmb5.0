<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_view_recordslist.class.php,v 1.4 2017-07-11 14:35:50 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_records_view_recordslist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "
		{% for record in records %}
			{{record.content}}
		{% endfor %}";
	}
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_records_view_recordslist_title"];
		$render_datas['records'] = array();
		if(is_array($datas)){
			foreach($datas as $record){
				$render_datas['records'][]= array(
						'content' => record_display::get_display_in_result($record, (isset($this->parameters->django_directory) ? $this->parameters->django_directory : ""))
				);
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_records_view_title']
		);
		$format[] =	array(
			'var' => "records",
			'desc' => $this->msg['frbr_entity_records_view_records_desc'],
			'children' => array(
				array(
					'var' => "records[i].content",
					'desc'=> $this->msg['frbr_entity_records_view_record_content_desc']
				)
			)
		);
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}