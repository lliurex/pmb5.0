<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_authorities.class.php,v 1.4.2.1 2018-01-11 14:39:13 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion des recherches avancees des autorités

require_once($class_path."/search.class.php");
require_once($class_path."/searcher/searcher_authorities_authors.class.php");
require_once($class_path."/searcher/searcher_authorities_authpersos.class.php");
require_once($class_path."/searcher/searcher_authorities_categories.class.php");
require_once($class_path."/searcher/searcher_authorities_collections.class.php");
require_once($class_path."/searcher/searcher_authorities_concepts.class.php");
require_once($class_path."/searcher/searcher_authorities_indexint.class.php");
require_once($class_path."/searcher/searcher_authorities_publishers.class.php");
require_once($class_path."/searcher/searcher_authorities_series.class.php");
require_once($class_path."/searcher/searcher_authorities_subcollections.class.php");
require_once($class_path."/searcher/searcher_authorities_tab.class.php");
require_once($class_path."/searcher/searcher_authorities_titres_uniformes.class.php");
require_once($class_path."/searcher/searcher_autorities_skos_concepts.class.php");

class search_authorities extends search {
	
	public function filter_searchtable_from_accessrights($table) {
		global $dbh;
		
	}
	
	protected function sort_results($table) {
		global $nb_per_page_search;
		global $page;
		 
		$start_page=$nb_per_page_search*$page;
		 
		return $table;
	}
	
	protected function get_display_nb_results($nb_results) {
		global $msg;
		 
		return " => ".$nb_results." ".$msg["search_extended_authorities_found"]."<br />\n";
	}
	
	protected function show_objects_results($table, $has_sort) {
		global $dbh;
		global $search;
		global $nb_per_page_search;
		global $page;
		
		$start_page=$nb_per_page_search*$page;
		$nb = 0;
		
		$query = "select $table.*,authorities.num_object,authorities.type_object from ".$table.",authorities where authorities.id_authority=$table.id_authority";
		if(count($search) > 1 && !$has_sort) {
			//Tri à appliquer par défaut
		}		
		$query .= " limit ".$start_page.",".$nb_per_page_search;
		
		$result=pmb_mysql_query($query, $dbh);
		$objects_ids = array();
		while ($row=pmb_mysql_fetch_object($result)) {
			$objects_ids[] = $row->id_authority;
		}
		if(count($objects_ids)) {
			$elements_authorities_list_ui = new elements_authorities_list_ui($objects_ids, count($objects_ids), 1);
			$elements = $elements_authorities_list_ui->get_elements_list();
			print $elements;
		}
	}
	
	protected function get_display_actions() {
		return "";
	}
	
	protected function get_display_icons($nb_results, $recherche_externe = false) {
		return "";
	}

	public static function get_join_and_clause_from_equation($type = AUT_TABLE_AUTHORS, $equation) {
		
		$authority_join = '';
		$authority_clause = '';
		$authority_ids = array();
		if($equation) {
			$my_search = new search_authorities('search_fields_authorities_gestion');
			$my_search->unserialize_search(stripslashes($equation));
			$res = $my_search->make_search();
			$req="select * from ".$res ;
			$resultat=pmb_mysql_query($req);
			while($r=pmb_mysql_fetch_object($resultat)) {
				$authority_ids[]=$r->id_authority;
			}
			switch($type) {
				case AUT_TABLE_AUTHORS :
					$aut_id_name = 'author_id'; 
					break;
				case AUT_TABLE_CATEG :
					$aut_id_name = 'id_noeud'; 
					break;
				case AUT_TABLE_PUBLISHERS :
					$aut_id_name = 'ed_id'; 
					break;
				case AUT_TABLE_COLLECTIONS :
					$aut_id_name = 'collection_id'; 
					break;
				case AUT_TABLE_SUB_COLLECTIONS :
					$aut_id_name = 'sub_coll_id'; 
					break;
				case AUT_TABLE_SERIES :
					$aut_id_name = 'serie_id'; 
					break;
				case AUT_TABLE_TITRES_UNIFORMES :
					$aut_id_name = 'tu_id'; 
					break;
				case AUT_TABLE_INDEXINT :
					$aut_id_name = 'indexint_id'; 
					break;
				case AUT_TABLE_CONCEPT :
					$aut_id_name = 'id_item'; 
					break;
				case AUT_TABLE_AUTHPERSO :
					// TODO
					break;
				default:
					$aut_id_name = 'author_id';
					break;	
			}
			$authority_join =' JOIN authorities on num_object = '.$aut_id_name.' and type_object = '.$type.' ';
			if (count($authority_ids)) {
				$authority_clause = ' and authorities.id_authority IN ('.implode(',',$authority_ids).') ';
			}else {
				$authority_clause = ' and authorities.id_authority IN (0) ';
			}
		}
		return array(
			'join' => $authority_join,
			'clause' => $authority_clause
		);
	}
}
?>