<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_authperso_view_authpersolist.class.php,v 1.1 2017-05-31 11:30:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/authperso_authority.class.php");

class frbr_entity_authperso_view_authpersolist extends frbr_entity_common_view_django{
	
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->default_template = "<div>
{% for auth in authperso %}
	{% if auth.info.view %}
		{{ auth.info.view }}
	{% else %}
		{{ auth.name }} : {{ auth.info.isbd }}
	{% endif%}
{% endfor %}
</div>";
	}
		
	public function render($datas){	
		//on rajoute nos éléments...
		//le titre
		$render_datas = array();
		$render_datas['title'] = $this->msg["frbr_entity_authperso_view_authpersolist_title"];
		$render_datas['authperso'] = array();
		if(is_array($datas)){
			foreach($datas as $authperso_authority_id){
				$authperso_authority = new authperso_authority($authperso_authority_id);
				$infos= $authperso_authority->format_datas();
				$render_datas['authperso'][]=$infos;
			}
		}
		//on rappelle le tout...
		return parent::render($render_datas);
	}
	
	public function get_format_data_structure(){		
		$format = array();
		$format[] = array(
			'var' => "title",
			'desc' => $this->msg['frbr_entity_authperso_view_title']
		);
		$authperso = array(
			'var' => "authperso",
			'desc' => $this->msg['frbr_entity_authperso_view_authperso_desc'],
			'children' => $this->prefix_var_tree(authperso_authority::get_format_data_structure(),"authperso[i]")
		);
		$format[] = $authperso;
		$format = array_merge($format,parent::get_format_data_structure());
		return $format;
	}
}