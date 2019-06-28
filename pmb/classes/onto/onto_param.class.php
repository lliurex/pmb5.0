<?php

/**
 * @author abacarisse
 *
 */
class onto_param extends stdClass{

	private $tab_param=array();
	
	public function __construct($tab_param=array()){
		if(!sizeof($tab_param)){
			$this->tab_param=array('categ'=>'','sub'=>'','action'=>'','page'=>'1','nb_per_page'=>'20');
		}else{
			$this->tab_param=$tab_param;
		}
		$this->assign_globals();
	}
	
	private function assign_globals(){
		foreach($this->tab_param as $param_name=>$param_default){
			global ${$param_name};
			$this->{$param_name}=${$param_name};
			//ajout d'un cas particulier permettant de remettre la globale page à 1
			//Cette dernière est mise à 0 dans le fichier select.php, 
			//ce qui entraine de nombreux effets de bords dans les selecteurs concept & onto
			if (!isset($this->{$param_name}) || ($this->{$param_name} === "") || ($param_name == "page" && ${$param_name} == 0)) {
				$this->{$param_name}=$param_default;
			}
		}
	}
}
