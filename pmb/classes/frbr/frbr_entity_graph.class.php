<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_graph.class.php,v 1.1 2017-06-02 09:52:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// require_once($class_path.'/authorities/tabs/authority_tabs.class.php');
// require_once($class_path.'/authority.class.php');
require_once($class_path.'/entity_graph.class.php');
require_once($class_path.'/index_concept.class.php');
require_once($class_path.'/notice.class.php');
require_once($class_path.'/marc_table.class.php');

class frbr_entity_graph extends entity_graph {
	
	/**
	 * données provenant des cadres
	 * @var array
	 */
	protected static $cadres_data;
	
	protected static $entity_graph = array();

	/**
	 * 
	 * @param unknown $instance
	 * @param unknown $type
	 * @return entity_graph
	 */
	public static function get_entity_graph($instance, $type){
		if (!isset(self::$entity_graph[$type][$instance->get_id()])) {
			self::$entity_graph[$type][$instance->get_id()] = new frbr_entity_graph($instance, $type);
		}
		return self::$entity_graph[$type][$instance->get_id()];
	}
	
	public function get_entities_graphed ($is_root = true) {		
		if (isset($this->entities_graphed)) {
			return $this->entities_graphed;
		}		
		$this->entities_graphed = array('nodes'=>array(), 'links'=>array());
		$nb_result = 0;
		
		switch($this->type){
			case 'authority':				
				if (!isset($this->entities_graphed['nodes']['authorities_'.$this->instance->get_id()])) {
					$type = $this->instance->get_string_type_object();					
					if($type == "authperso" && $this->instance->get_object_instance()->is_event()){
						$type = "event";
					}
					$node = array(
							'id' => 'authorities_'.$this->instance->get_id(),
							'type' => 'root',
							'radius' => '20',
							'color' => self::get_color_from_type($type),
							'name' => $this->instance->header,
							'url' => $this->instance->get_permalink().'&quoi=common_entity_graph',
							'img' => $this->instance->get_type_icon()							
					);
					if ($is_root) {
						$this->entities_graphed['nodes']['authorities_'.$this->instance->get_id()] = $node;
					}
				}
				$this->root_node_id = 'authorities_'.$this->instance->get_id();
				break;
			case 'record':
				if (!isset($this->entities_graphed['nodes']['records_'.$this->instance->get_id()])) {
					$node = array(
							'id' => 'records_'.$this->instance->get_id(),
							'type' => 'root',
							'radius' => '20',
							'color' => self::get_color_from_type('record'),
							'name' => notice::get_notice_title($this->instance->get_id()),
							'url' => notice::get_permalink($this->instance->get_id()),
							'img' => notice::get_icon($this->instance->get_id())
					);
					if ($is_root) {
						$this->entities_graphed['nodes']['records_'.$this->instance->get_id()] = $node;
					}
				}
				$this->root_node_id = 'records_'.$this->instance->get_id();
				break;
		}
		if (count(self::$cadres_data)) {
			foreach(self::$cadres_data as $key => $cadre_data) {
				$entity_type = substr($key,0,strpos($key, '_'));	
				if (isset($cadre_data['parent_node']) && $cadre_data['parent_node']) {
					$this->root_node_id = $cadre_data['parent_node']['id'];
					$this->compute_entities(array($entity_type => $cadre_data['node']), $entity_type, $cadre_data['parent_node']);
				} else {
					$this->root_node_id = $cadre_data['entity_type'].'_'.$this->instance->get_id();
					$this->compute_entities(array($entity_type => $cadre_data), $entity_type, $node);
				}				
			}
		}		
		return $this->entities_graphed;
	}
	
	public static function add_nodes($data, $id, $name, $type, $parent_id = '', $parent_type = '') {
		switch($type) {
			case 'records' :
				self::add_records_nodes($data, $id, $name, $parent_id, $parent_type);
				break;
			default :
				self::add_authorities_nodes($data, $id, $name, $type, $parent_id, $parent_type);
				break;
		}
	}
	
	protected static function add_records_nodes($data, $id, $name, $parent_id = '', $parent_type = '') {
		$node = array(
				'id' => 'records_'.$id,
				'type' => 'subroot',
				'radius' => '15',
				'color' => entity_graph::get_color_from_type('records'),
				'label' => $name,
				'url' => ''
		);
		if ($parent_id) {
			self::$cadres_data['records_'.$id]['parent_node'] = array(
					'id' => $parent_id,
					'color' => entity_graph::get_color_from_type($parent_type)
			);
			self::$cadres_data['records_'.$id]['node']['records'] = $node;
			self::$cadres_data['records_'.$id]['node']['records']['elements'] = $data;
		} else {
			self::$cadres_data['records_'.$id]['records'] = $node;
			self::$cadres_data['records_'.$id]['records']['elements'] = $data;
		}
	}
	
	protected static function add_authorities_nodes($data, $id, $name, $type, $parent_id = '', $parent_type = '') {
		$node = array(
				'id' => 'authorities_'.$id,
				'type' => 'subroot',
				'radius' => '15',
				'color' => entity_graph::get_color_from_type($type),
				'label' => $name,
				'url' => ''
		);
		$cadre_id = explode('_', $id);
		$cadre_id = $cadre_id[0];
		if ($parent_id) {
			self::$cadres_data['authorities_'.$id]['parent_node'] = array(
					'id' => $parent_id,
					'color' => entity_graph::get_color_from_type($parent_type)
			);
			self::$cadres_data['authorities_'.$id]['node'][$type]['authorities_'.$cadre_id] = $node;
			self::$cadres_data['authorities_'.$id]['node'][$type]['authorities_'.$cadre_id]['elements'] = $data;
		} else {
			self::$cadres_data['authorities_'.$id][$type]['authorities_'.$cadre_id] = $node;
			self::$cadres_data['authorities_'.$id][$type]['authorities_'.$cadre_id]['elements'] = $data;
		}
	}
}