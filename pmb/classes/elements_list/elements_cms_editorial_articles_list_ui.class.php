<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_cms_editorial_articles_list_ui.class.php,v 1.1 2016-07-27 08:13:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste d'article du contenu éditorial
 * @author ngantier
 *
 */
class elements_cms_editorial_articles_list_ui extends elements_list_ui {
	
	protected function generate_elements_list($contents){
		global $dbh;
		
		$elements_list = '';
		foreach($contents as $id){			
			$elements_list.= $this->generate_article($id);
		}
		return $elements_list;
	}
	
	private function generate_article($id){
		global $dbh;
		
		$display='';		
		$query = 'select * from cms_articles where id_article ='. $id;
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			$r = pmb_mysql_fetch_object($result);
			$display='<div class="notice-parent"><span class="notice-heada"><a href="./cms.php?categ=article&sub=edit&id='.$id.'">'.$r->article_title.'</a></span></br></div>';
		}
		return $display;
	}
}