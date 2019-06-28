<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_authorities.class.php,v 1.7 2017-07-18 09:41:20 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//Classe de gestion des recherches avancees des autorités

require_once($class_path."/search.class.php");

class search_authorities extends search {
	
	public function filter_searchtable_from_accessrights($table) {
		global $dbh;
		global $gestion_acces_active,$gestion_acces_user_authority;
		global $PMBUserId;
		
		if($gestion_acces_active && $gestion_acces_user_authority){
			//En vue de droits d'accès
		}
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
		
		$query = "select ".$table.".*,authorities.num_object,authorities.type_object from ".$table.",authorities where authorities.id_authority=".$table.".id_authority";
		if(count($search) > 1 && !$has_sort) {
			//Tri à appliquer par défaut
		}
		$query .= " limit ".$start_page.",".$nb_per_page_search;
		
		$result = pmb_mysql_query($query, $dbh);
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
	
	public function show_results($url,$url_to_search_form,$hidden_form=true,$search_target="", $acces=false) {
		global $dbh;
		global $begin_result_liste;
		global $nb_per_page_search;
		global $page;
		global $charset;
		global $search;
		global $msg;
		global $pmb_nb_max_tri;
		global $pmb_allow_external_search;
		global $debug;
	
		//Y-a-t-il des champs ?
		if (count($search)==0) {
			array_pop($_SESSION["session_history"]);
			error_message_history($msg["search_empty_field"], $msg["search_no_fields"], 1);
			exit();
		}
		$recherche_externe=true;//Savoir si l'on peut faire une recherche externe à partir des critères choisis
		//Verification des champs vides
		for ($i=0; $i<count($search); $i++) {
			$op=$this->get_global_value("op_".$i."_".$search[$i]);
				
			$field=$this->get_global_value("field_".$i."_".$search[$i]);
	
			$field1=$this->get_global_value("field_".$i."_".$search[$i]."_1");
	
			$s=explode("_",$search[$i]);
			$bool=false;
			if ($s[0]=="f") {
				$champ=$this->fixedfields[$s[1]]["TITLE"];
				if ((string)$field[0]=="" && (string)$field1[0]=="") {
					$bool=true;
				}
			} elseif(array_key_exists($s[0],$this->pp)) {
				$champ=$this->pp[$s[0]]->t_fields[$s[1]]["TITRE"];
				if ((string)$field[0]=="" && (string)$field1[0]=="") {
					$bool=true;
				}
			} elseif($s[0]=="s") {
				$recherche_externe=false;
				$champ=$this->specialfields[$s[1]]["TITLE"];
				$type=$this->specialfields[$s[1]]["TYPE"];
				for ($is=0; $is<count($this->tableau_speciaux["TYPE"]); $is++) {
					if ($this->tableau_speciaux["TYPE"][$is]["NAME"]==$type) {
						$sf=$this->specialfields[$s[1]];
						global $include_path;
						require_once($include_path."/search_queries/specials/".$this->tableau_speciaux["TYPE"][$is]["PATH"]."/search.class.php");
						$specialclass= new $this->tableau_speciaux["TYPE"][$is]["CLASS"]($s[1],$i,$sf,$this);
						$bool=$specialclass->is_empty($field);
						break;
					}
				}
			}//elseif (substr($s,0,9)=="authperso") {}
			if (($bool)&&(!$this->op_empty[$op])) {
				$query_data = array_pop($_SESSION["session_history"]);
				error_message_history($msg["search_empty_field"], sprintf($msg["search_empty_error_message"],$champ), 1);
				print $this->get_back_button($query_data);
				exit();
			}
		}
		$table=$this->make_search();
	
		if ($acces==true) {
			$this->filter_searchtable_from_accessrights($table);
		}
		 
		$requete="select count(1) from $table";
		if($res=pmb_mysql_query($requete)){
			$nb_results=pmb_mysql_result($res,0,0);
		}else{
			$query_data = array_pop($_SESSION["session_history"]);
			error_message_history("",$msg["search_impossible"], 1);
			print $this->get_back_button($query_data);
			exit();
		}
		 
		//gestion du tri
		$has_sort = false;
		if ($nb_results <= $pmb_nb_max_tri) {
			if ($_SESSION["tri"]) {
				$table = $this->sort_results($table);
				$has_sort = true;
			}
		}
		// fin gestion tri
		//Y-a-t-il une erreur lors de la recherche ?
		if ($this->error_message) {
			$query_data = array_pop($_SESSION["session_history"]);
			error_message_history("", $this->error_message, 1);
			print $this->get_back_button($query_data);
			exit();
		}
		 
		if ($hidden_form) {
			print $this->make_hidden_search_form($url,"search_form","",false);
			print facette_search_compare::form_write_facette_compare();
			print "</form>";
		}
		 
		$human_requete = $this->make_human_query();
		print "<strong>".$msg["search_search_extended"]."</strong> : ".$human_requete ;
		if ($debug) print "<br />".$this->serialize_search();
		if ($nb_results) {
			print $this->get_display_nb_results($nb_results);
			print $begin_result_liste;
			print $this->get_display_icons($nb_results, $recherche_externe);
		} else print "<br />".$msg["1915"]." ";		
		
		self::get_caddie_link();		
	
		print "<input type='button' class='bouton' onClick=\"document.search_form.action='".$url_to_search_form."'; document.search_form.target='".$search_target."'; document.search_form.submit(); return false;\" value=\"".$msg["search_back"]."\"/>";
		print $this->get_display_actions();
	
		print $this->get_current_search_map();
	
		$this->show_objects_results($table, $has_sort);
	
		//Gestion de la pagination
		if ($nb_results) {
			$n_max_page=ceil($nb_results/$nb_per_page_search);
			$etendue=10;
				
			if (!$page) $page_en_cours=0 ;
			else $page_en_cours=$page ;
	
			$nav_bar = '';
			//Première
			if(($page_en_cours+1)-$etendue > 1) {
				$nav_bar .= "<a href='#' onClick=\"document.search_form.page.value=0;";
				if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
				$nav_bar .= "document.search_form.submit(); return false;\"><img src='./images/first.gif' border='0' alt='".$msg['first_page']."' hspace='6' align='middle' title='".$msg['first_page']."' /></a>";
			}
				
			// affichage du lien precedent si necessaire
			if ($page>0) {
				$nav_bar .= "<a href='#' onClick='document.search_form.page.value-=1; ";
				if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
				$nav_bar .= "document.search_form.submit(); return false;'>";
				$nav_bar .= "<img src='./images/left.gif' border='0'  title='".$msg[48]."' alt='[".$msg[48]."]' hspace='3' align='middle'/>";
				$nav_bar .= "</a>";
			}
			 
			$deb = $page_en_cours - 10 ;
			if ($deb<0) $deb=0;
			for($i = $deb; ($i < $n_max_page) && ($i<$page_en_cours+10); $i++) {
				if($i==$page_en_cours) $nav_bar .= "<strong>".($i+1)."</strong>";
				else {
					$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=".($i)."; ";
					if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
					$nav_bar .= "document.search_form.submit(); return false;\">";
					$nav_bar .= ($i+1);
					$nav_bar .= "</a>";
				}
				if($i<$n_max_page) $nav_bar .= " ";
			}
			 
			if(($page+1)<$n_max_page) {
				$nav_bar .= "<a href='#' onClick=\"if ((isNaN(document.search_form.page.value))||(document.search_form.page.value=='')) document.search_form.page.value=1; else document.search_form.page.value=parseInt(document.search_form.page.value)+parseInt(1); ";
				if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
				$nav_bar .= "document.search_form.submit(); return false;\">";
				$nav_bar .= "<img src='./images/right.gif' border='0' title='".$msg[49]."' alt='[".$msg[49]."]' hspace='3' align='middle'>";
				$nav_bar .= "</a>";
			} else 	$nav_bar .= "";
	
			//Dernière
			if((($page_en_cours+1)+$etendue)<$n_max_page){
				$nav_bar .= "<a href='#' onClick=\"document.search_form.page.value=".($n_max_page-1).";";
				if (!$hidden_form) $nav_bar .= "document.search_form.launch_search.value=1; ";
				$nav_bar .= "document.search_form.submit(); return false;\"><img src='./images/last.gif' border='0' alt='".$msg['last_page']."' hspace='6' align='middle' title='".$msg['last_page']."' /></a>";
			}
	
			$nav_bar = "<div align='center'>$nav_bar</div>";
			echo $nav_bar ;
			 
		}
	}
	
	public static function get_caddie_link() {
		global $msg;
		print "&nbsp;<a href='#' onClick=\"openPopUp('./print_cart.php?current_print=".$_SESSION['CURRENT']."&action=print_prepare&object_type=".self::get_type_from_mode()."&authorities_caddie=1','popup',500, 600, -2, -2, 'scrollbars=yes,menubar=0'); return false;\"><img src='./images/basket_small_20x20.gif' border='0' align='center' alt=\"".$msg["histo_add_to_cart"]."\" title=\"".$msg["histo_add_to_cart"]."\"></a>&nbsp;";
	}
	
	public static function get_type_from_mode() {
		global $mode;
				
		$type = "MIXED";
		switch ($mode) {
			case 1 :
				$type = "AUTHORS";
				break;
			case 2 :
				$type = "CATEGORIES";
				break;
			case 3 :
				$type = "PUBLISHERS";
				break;
			case 4 :
				$type = "COLLECTIONS";
				break;
			case 5 :
				$type = "SUBCOLLECTIONS";
				break;
			case 6 :
				$type = "SERIES";
				break;
			case 7 :
				$type = "TITRES_UNIFORMES";
				break;
			case 8 :
				$type = "INDEXINT";
				break;
			case 9 :
				$type = "CONCEPTS";
				break;
		}		
		return $type;
	}
}
?>