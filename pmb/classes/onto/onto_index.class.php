<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_index.class.php,v 1.15.2.3 2017-12-14 14:32:07 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/skos/onto_skos_index.class.php');
require_once($class_path.'/onto/common/onto_common_index.class.php');

/**
 * class onto_index
*/
class onto_index {
	/**
	 * tableau des instances
	 * @var unknown
	 */
	protected static $instances = array();
	
	/**
	 * 
	 */
	public function __construct(){		
	}	
	
	/**
	 * Methode qui retourne l'instance de la classe d'indexation correspondant à l'ontologie
	 * @param string $onto_name
	 * @return onto_common_index
	 */
	public static function get_instance($onto_name = ""){
		$prefix="onto_";
		$suffixe = "_index";
		$instance_name = $prefix."common".$suffixe;
		if($onto_name && class_exists($prefix.$onto_name.$suffixe)){			
			$instance_name = $prefix.$onto_name.$suffixe;
		}
		if (empty(self::$instances[$instance_name])) {
			self::$instances[$instance_name] = new $instance_name(); 
		}
		return self::$instances[$instance_name];
	}
}