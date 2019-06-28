<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_history.inc.php,v 1.22 2016-11-14 16:23:45 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($include_path."/rec_history.inc.php");
if ($_SESSION["nb_queries"]) {
	print "<script>
		var history_all_checked = false;
		
		function check_uncheck_all_history() {
			if (history_all_checked) {
				setCheckboxes('cases_a_cocher', 'cases_suppr', false);
				history_all_checked = false;
				document.getElementById('show_history_checked_all').value = '".$msg["show_history_check_all"]."';
				document.getElementById('show_history_checked_all').title = '".$msg["show_history_check_all"]."';
			} else {
				setCheckboxes('cases_a_cocher', 'cases_suppr', true);
				history_all_checked = true;
				document.getElementById('show_history_checked_all').value = '".$msg["show_history_uncheck_all"]."';
				document.getElementById('show_history_checked_all').title = '".$msg["show_history_uncheck_all"]."';
			}
			return false;
		}
		
		function setCheckboxes(the_form, the_objet, do_check) {
			 var elts = document.forms[the_form].elements[the_objet+'[]'] ;
			 var elts_cnt = (typeof(elts.length) != 'undefined') ? elts.length : 0;
			 if (elts_cnt) {
				for (var i = 0; i < elts_cnt; i++) {
			 		elts[i].checked = do_check;
			 	} // end for
			 } else {
			 	elts.checked = do_check;
			 } 
			 return true;
		}
						
		function verifCheckboxes(the_form, the_objet) {
			var bool=false;
			var elts = document.forms[the_form].elements[the_objet+'[]'] ;
			var elts_cnt  = (typeof(elts.length) != 'undefined')
	                  ? elts.length
	                  : 0;
	
			if (elts_cnt) {
					
				for (var i = 0; i < elts_cnt; i++) { 		
					if (elts[i].checked)
					{
						bool = true;
					}
				}
			} else {
					if (elts.checked)
					{
						bool = true;
					}
			}
			return bool;
		} 
	</script>";

	print "<div id='history_action'>";
	print "<input type='button' class='bouton' id='show_history_checked_all' value=\"".$msg["show_history_check_all"]."\" onClick=\"check_uncheck_all_history();\" />&nbsp;";
	print "<input type='button' class='bouton' value=\"".$msg["suppr_elts_coch"]."\" onClick=\"if (verifCheckboxes('cases_a_cocher','cases_suppr')){ document.cases_a_cocher.submit(); return false;}\" />&nbsp;";
	print "</div>";
}


print "<h3 class='title_history'><span>".$msg["history_title"]."</span></h3>";

print "<form name='cases_a_cocher' method='post' action='./index.php?lvl=search_history&raz_history=1'>";

if ($_SESSION["nb_queries"]!=0) {
	for ($i=$_SESSION["nb_queries"]; $i>=1; $i--) {
		if ($_SESSION["search_type".$i]!="module") {
			print "<input type=checkbox name='cases_suppr[]' value='$i'><b>$i)</b> ";
			if ($opac_autolevel2==2) {
				print "<a href=\"javascript:document.forms['search_".$i."'].submit();\">".get_human_query($i)."</a><br /><br />";
			} else {
				print "<a href=\"./index.php?lvl=search_result&get_query=$i\">".get_human_query($i)."</a><br /><br />";
			}			
		}
	}
} else {
	print "<b>".$msg["histo_empty"]."</b>";	
}

print "</form>";

//Si autolevel2=2, on re-soumet immédiatement sans passer par le lvl1
if (($opac_autolevel2==2) && ($_SESSION["nb_queries"]!=0)) {
	for ($i=$_SESSION["nb_queries"]; $i>=1; $i--) {
		if ($_SESSION["search_type".$i]!="module") {
			get_history($i);
			if ($_SESSION["search_type".$i]=="simple_search") {
				print "<form method='post' style='display:none' name='search_".$i."' action='".$base_path."/index.php?lvl=more_results&autolevel1=1'>";
				if (function_exists("search_other_function_post_values")){
					print search_other_function_post_values();
				}
				if(count($map_emprise_query)){
					foreach($map_emprises_query as $map_emprise_query){
						print " <input type='hidden' name='map_emprises_query[]' value='".$map_emprise_query."'>";
					}
				}
				print "
		  		<input type='hidden' name='mode' value='tous'>
		  		<input type='hidden' name='typdoc' value='".$typdoc."'>
		  		<input type='hidden' name='user_query' value='".htmlentities(stripslashes($user_query),ENT_QUOTES,$charset)."'>";				
				if ($look_TITLE) {
					print "<input type='hidden' name='look_TITLE' value='1' />";
				}
				if ($look_AUTHOR) {
					print "<input type='hidden' name='look_AUTHOR' value='1' />";
				}
				if ($look_PUBLISHER) {
					print "<input type='hidden' name='look_PUBLISHER' value='1' />";
				}
				if ($look_TITRE_UNIFORME) {
					print "<input type='hidden' name='look_TITRE_UNIFORME' value='1' />";
				}
				if ($look_COLLECTION) {
					print "<input type='hidden' name='look_COLLECTION' value='1' />";
				}
				if ($look_SUBCOLLECTION) {
					print "<input type='hidden' name='look_SUBCOLLECTION' value='1' />";
				}
				if ($look_CATEGORY) {
					print "<input type='hidden' name='look_CATEGORY' value='1' />";
				}
				if ($look_INDEXINT) {
					print "<input type='hidden' name='look_INDEXINT' value='1' />";
				}
				if ($look_KEYWORDS) {
					print "<input type='hidden' name='look_KEYWORDS' value='1' />";
				}
				if ($look_ABSTRACT) {
					print "<input type='hidden' name='look_ABSTRACT' value='1' />";
				}
				if ($look_ALL) {
					print "<input type='hidden' name='look_ALL' value='1' />";
				}
				if ($look_DOCNUM) {
					print "<input type='hidden' name='look_DOCNUM' value='1' />";
				}
				if ($look_CONCEPT) {
					print "<input type='hidden' name='look_CONCEPT' value='1' />";
				}
				print "</form>";
			} else {
				$action=$base_path."/index.php?lvl=index&search_type_asked=extended_search";
				$sc=new search();
				print $sc->make_hidden_search_form("./index.php?lvl=more_results&mode=extended","search_".$i,"",true);
			}					
		}
	}
}
?>