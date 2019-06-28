<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_docnums_list_ui.class.php,v 1.2 2016-03-30 14:07:42 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');
require_once($include_path.'/h2o/pmb_h2o.inc.php');
require_once($class_path.'/explnum.class.php');
require_once($class_path.'/notice.class.php');
// require_once($class_path.'/serials.class.php'); // Entraine une fatal sur index.php

/**
 * Classe d'affichage d'un onglet qui affiche une liste de documents numériques
 * @author vtouchard
 *
 */
class elements_docnums_list_ui extends elements_list_ui {
	
	protected function generate_elements_list($contents){
		$elements_list = '';
		$recherche_ajax_mode = 0;
		$nb = 0;
		foreach($contents as $docnum_id){
			if(!$recherche_ajax_mode && ($nb++>5)){
				$recherche_ajax_mode = 1;
			}
			$docnum = new explnum($docnum_id);
			
			$elements_list.= $this->generate_docnum($docnum, $recherche_ajax_mode);
		}
		return $elements_list;
	}
	
	/**
	 * Permet de générer l'affichage d'un élément de liste de type document numérique
	 * @param explnum $docnum
	 * @param bool $recherche_ajax_mode
	 * @return string 
	 */
	private function generate_docnum($docnum, $recherche_ajax_mode){
		global $include_path;
		
		if ($docnum->explnum_notice) {
			$record = new notice($docnum->explnum_notice);
		} else {
			$record = new bulletinage($docnum->explnum_bulletin);
		}
		
		$template_path = $include_path.'/templates/explnum_in_list.tpl.html';
		if(file_exists($include_path.'/templates/explnum_in_list_subst.tpl.html')){
			$template_path = $include_path.'/templates/explnum_in_list_subst.tpl.html';
		}
		if(file_exists($template_path)){
			$h2o = new H2o($template_path);
			$context = array('list_element' => $docnum, 'list_element_record' => $record);
			return $h2o->render($context);
		}
		return '';
	}
	
	public function is_expandable() {
		return false;
	}

}