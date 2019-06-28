<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.inc.php,v 1.95.2.3 2017-10-20 15:05:19 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once("$class_path/caddie.class.php");
require_once($class_path."/sort.class.php");
require_once($class_path."/notice.class.php");
require_once($class_path."/connecteurs.class.php");

function aff_paniers($item=0, $object_type="NOTI", $lien_origine="./cart.php?", $action_click = "add_item", $titre="Cliquez sur le nom d'un panier pour y déposer la notice", $restriction_panier="", $lien_edition=0, $lien_suppr=0, $lien_creation=1, $nocheck=false, $lien_pointage=0) {
	global $msg;
	global $PMBuserid;
	global $charset;
	global $myCart;
	global $action;
	global $baselink;
	global $deflt_catalog_expanded_caddies;
	global $base_path;
	
	if ($lien_edition) $lien_edition_panier_cst = "<input type=button class=bouton value='$msg[caddie_editer]' onclick=\"document.location='$lien_origine&action=edit_cart&idcaddie=!!idcaddie!!';\" />";
	else $lien_edition_panier_cst = "";

	$liste = caddie::get_cart_list($restriction_panier);
	print "<script type='text/javascript' src='./javascript/tablist.js'></script>";
	if(($item)&&($nocheck)) {
		print "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&item=$item' method='post'>";
		print "<input type='hidden' id='idcaddie' name='idcaddie' >";
		if ($lien_pointage) {
			print "<input type='hidden' id='idcaddie_selected' name='idcaddie_selected' >";
		}
	}	
	if(($item)&&(!$nocheck)) {
		print "<form name='print_options' action='$lien_origine&action=$action_click&object_type=".$object_type."&item=$item' method='post'>";
		if($action!="save_cart")print "<input type='checkbox' name='include_child' >&nbsp;".$msg["cart_include_child"];
	}
	print "<hr />";
	$boutons_select='';
	if ($lien_creation) {
		print "<div class='row'>
		$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
		</div><br>";
	}
	if(sizeof($liste)) {
		print "<div class='row'><a href='javascript:expandAll()'><img src='./images/expand_all.gif' id='expandall' border='0'></a>
		<a href='javascript:collapseAll()'><img src='./images/collapse_all.gif' id='collapseall' border='0'></a>$titre</div>";
		print confirmation_delete("$lien_origine&action=del_cart&object_type=".$object_type."&item=$item&idcaddie=");
		$parity=array();
		while(list($cle, $valeur) = each($liste)) {
			$rqt_autorisation=explode(" ",$valeur['autorisations']);
			if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
				$aff_lien=str_replace('!!idcaddie!!', $valeur['idcaddie'], $lien_edition_panier_cst);
		        if(!$myCart)$myCart = new caddie(0);
		        $myCart->nb_item=$valeur['nb_item'];
		        $myCart->nb_item_pointe=$valeur['nb_item_pointe'];
		        $myCart->type=$valeur['type'];
		        $print_cart[$myCart->type]["titre"]="<b>".$msg["caddie_de_".$myCart->type]."</b><br />";
		        if(!trim($valeur["caddie_classement"])){
		        	$valeur["caddie_classement"]=classementGen::getDefaultLibelle();
		        }
		        if(!isset($parity[$myCart->type][$valeur["caddie_classement"]])) $parity[$myCart->type][$valeur["caddie_classement"]] = 0;
		        $parity[$myCart->type][$valeur["caddie_classement"]]=1-$parity[$myCart->type][$valeur["caddie_classement"]];
				if ($parity[$myCart->type][$valeur["caddie_classement"]]) $pair_impair = "even"; 
				else $pair_impair = "odd";	        
		        $tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
				
				if($item && $action!="save_cart" && $action!="del_cart") {
					$rowPrint = "<tr class='$pair_impair' $tr_javascript ><td class='classement60'>".(!$nocheck?"<input type='checkbox' id='id_".$valeur['idcaddie']."' name='caddie[".$valeur['idcaddie']."]' value='".$valeur['idcaddie']."'>":"")."&nbsp;"; 
					$link = "$lien_origine&action=$action_click&object_type=".$object_type."&idcaddie=".$valeur['idcaddie']."&item=$item";	
            		if(!$nocheck){
            			$rowPrint.= "<a href='#' onclick='javascript:document.getElementById(\"id_".$valeur['idcaddie']."\").checked=true;document.forms[\"print_options\"].submit();' /><strong>".$valeur['name']."</strong>";
            		} else {
            			if ($lien_pointage) {
            				$rowPrint.= "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$item.";document.getElementById(\"idcaddie_selected\").value=".$valeur['idcaddie'].";document.forms[\"print_options\"].submit();' /><strong>".$valeur['name']."</strong>";
            			} else {
            				$rowPrint.= "<a href='#' onclick='javascript:document.getElementById(\"idcaddie\").value=".$valeur['idcaddie'].";document.forms[\"print_options\"].submit();' /><strong>".$valeur['name']."</strong>";
            			}
            		}			
					if ($valeur['comment']){
						$rowPrint.= "<br /><small>(".$valeur['comment'].")</small>";
					}
	            	$rowPrint.=  "</td>
	            		".$myCart->aff_nb_items_reduit()."
	            		<td class='classement20'>$aff_lien</td>
						</tr>";
				} else {
					$link = "$lien_origine&action=$action_click&object_type=".$object_type."&idcaddie=".$valeur['idcaddie']."&item=$item";
	            	$rowPrint= "<tr class='$pair_impair' $tr_javascript >";
	                $rowPrint.= "<td class='classement60'><a href='$link' /><strong>".$valeur['name']."</strong>";	
	                if ($valeur['comment']){
	                	$rowPrint.= "<br /><small>(".$valeur['comment'].")</small>";
	                }
	                $rowPrint.= "</a></td>";
	            	$rowPrint.= $myCart->aff_nb_items_reduit();
	            	if ($lien_creation) {
	            		$classementGen = new classementGen('caddie', $valeur['idcaddie']);
	            		$rowPrint.= "<td class='classement15'>".$aff_lien."&nbsp;".caddie::show_actions($valeur['idcaddie'],$valeur['type']).($valeur['acces_rapide']?" <img src='".$base_path."/images/chrono.png' title='".$msg['caddie_fast_access']."'>":"")."</td>";
	            		$rowPrint.= "<td class='classement5'>".$classementGen->show_selector($lien_origine,$PMBuserid)."</td>";
	            	} else {
	            		$rowPrint.= "<td class='classement20'>$aff_lien</td>";
	            	}
					$rowPrint.= "</tr>";
				}
				$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["titre"] = stripslashes($valeur["caddie_classement"]);
				if(!isset($print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"])) {
					$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] = '';
				}
				$print_cart[$myCart->type]["classement_list"][$valeur["caddie_classement"]]["cart_list"] .= $rowPrint;
			}
		}
		if ($lien_creation) {
			print "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
		}
		//Tri des classements
		foreach($print_cart as $key => $cart_type) {
			ksort($print_cart[$key]["classement_list"]);
		}
		// affichage des paniers par type
		foreach($print_cart as $key => $cart_type) {
			//on remplace les clés à cause des accents
			$cart_type["classement_list"]=array_values($cart_type["classement_list"]);
			$contenu="";
			foreach($cart_type["classement_list"] as $keyBis => $cart_typeBis) {
				$contenu.=gen_plus($key.$keyBis,$cart_typeBis["titre"],"<table border='0' cellspacing='0' width='100%' class='classementGen_tableau'>".$cart_typeBis["cart_list"]."</table>",$deflt_catalog_expanded_caddies);
			}
			print gen_plus($key,$cart_type["titre"],$contenu,$deflt_catalog_expanded_caddies);
		}		
	} else {
		print $msg[398];
	}

	if (!$nocheck) {
		if($item && $action!="save_cart") {
			$boutons_select="<input type='submit' value='".$msg["print_cart_add"]."' class='bouton'/>&nbsp;<input type='button' value='".$msg["print_cancel"]."' class='bouton' onClick='self.close();'/>&nbsp;";
		}	
		if ($lien_creation) {
			print "<div class='row'><hr />
				$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
				</div>"; 
		} else {
			print "<div class='row'><hr />
				$boutons_select
				</div>"; 		
		}
	} else 	if ($lien_creation) {
		print "<div class='row'><hr />
			$boutons_select<input class='bouton' type='button' value=' $msg[new_cart] ' onClick=\"document.location='$lien_origine&action=new_cart&object_type=".$object_type."&item=$item'\" />
			</div>"; 
	}				
	//if(($item)&&(!$nocheck)) print"</form>";
	if(($item)) print"</form>";		
}

// affichage d'un unique objet de caddie
function aff_cart_unique_object ($item, $caddie_type, $url_base="./catalog.php?categ=caddie&sub=gestion&quoi=panier&idcaddie=0" ) {
	global $msg;
	global $dbh;
	global $begin_result_liste;
	global $end_result_list;
	global $page, $nbr_lignes, $nb_per_page, $nb_per_page_search;
	
	// nombre de références par pages
	if ($nb_per_page_search != "") $nb_per_page = $nb_per_page_search ;
	else $nb_per_page = 10;
	
	$cb_display = "
			<div id=\"el!!id!!Parent\" class=\"notice-parent\">
	    		<span class=\"notice-heada\">!!heada!!</span>
	    		<br />
			</div>
			";
	
	$liste[] = array('object_id' => $item, 'content' => "", 'blob_type' => "") ;  
	
	$aff_retour = "" ;
	
	//Calcul des variables pour la suppression d'items
	$modulo = $nbr_lignes%$nb_per_page;
	if($modulo == 1){
		$page_suppr = (!$page ? 1 : $page-1);
	} else {
		$page_suppr = $page;
	}	
	$nb_after_suppr = ($nbr_lignes ? $nbr_lignes-1 : 0);	
	
	if(!sizeof($liste) || !is_array($liste)) {
		return $msg[399];
	} else {
		// en fonction du type de caddie on affiche ce qu'il faut
		if ($caddie_type=="NOTI") {
			// boucle de parcours des notices trouvées
			while(list($cle, $object) = each($liste)) {
				if ($object[content]=="") {
					// affichage de la liste des notices sous la forme 'expandable'
					$requete = "SELECT * FROM notices WHERE notice_id=".$object['object_id']." LIMIT 1";
					$fetch = pmb_mysql_query($requete);
					if(pmb_mysql_num_rows($fetch)) {
						$notice = pmb_mysql_fetch_object($fetch);
						if ($notice->niveau_biblio == 'b') {
							// notice de bulletin
							$rqtbull="select bulletin_id from bulletins where num_notice=".$notice->notice_id;
							$fetchbull = pmb_mysql_query($rqtbull);
							$bull = pmb_mysql_fetch_object($fetchbull);
							$link = "./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".$bull->bulletin_id;
							// pas affichés pour l'instant:
							$link_expl = ''; 
							$link_explnum = '';
							$display = new mono_display($notice, 6, $link, 1, $link_expl, $lien_suppr_cart, $link_explnum );
							$aff_retour .= $display->result;
						} elseif($notice->niveau_biblio != 's' && $notice->niveau_biblio != 'a') {
							// notice de monographie
							$link = './catalog.php?categ=isbd&id=!!id!!';
							$link_expl = './catalog.php?categ=edit_expl&id=!!notice_id!!&cb=!!expl_cb!!&expl_id=!!expl_id!!'; 
							$link_explnum = './catalog.php?categ=edit_explnum&id=!!notice_id!!&explnum_id=!!explnum_id!!';   
							$lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=NOTI&item=$notice->notice_id&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='./images/basket_empty_20x20.gif' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a>";
							$display = new mono_display($notice, 6, $link, 1, $link_expl, $lien_suppr_cart, $link_explnum );
							$aff_retour .= $display->result;
						} else {
							// on a affaire à un périodique
							// préparation des liens pour lui
							$link_serial = './catalog.php?categ=serials&sub=view&serial_id=!!id!!';
							$link_analysis = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!bul_id!!&art_to_show=!!id!!';
							$link_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=!!id!!';
							$lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=NOTI&item=$notice->notice_id&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='./images/basket_empty_20x20.gif' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a>";
							$link_explnum = "./catalog.php?categ=serials&sub=analysis&action=explnum_form&bul_id=!!bul_id!!&analysis_id=!!analysis_id!!&explnum_id=!!explnum_id!!";
							$serial = new serial_display($notice, 6, $link_serial, $link_analysis, $link_bulletin, $lien_suppr_cart, $link_explnum, 0);
							$aff_retour .= $serial->result;
						}
					}
				} else {
					$cb_display = "
						<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				    		<span class=\"notice-heada\"><strong>Code-barre : $object[content]&nbsp;: ${msg[395]}</strong></span>
				    		<br />
						</div>
						";
					$aff_retour .= $cb_display;
				}
			} // fin de liste
			print $end_result_list;
		} // fin si NOTI
		// si EXPL
		if ($caddie_type=="EXPL") {
			// boucle de parcours des exemplaires trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			while(list($cle, $expl) = each($liste)) {
				if (!$expl[content])
					if($stuff = get_expl_info($expl['object_id'])) {
						$stuff->lien_suppr_cart = "<a href='$url_base&action=del_item&object_type=EXPL&item=$expl&page=$page_suppr&nbr_lignes=$nb_after_suppr&nb_per_page=$nb_per_page'><img src='./images/basket_empty_20x20.gif' alt='basket' title=\"".$msg['caddie_icone_suppr_elt']."\" /></a>";
						$stuff = check_pret($stuff);
						$aff_retour .= print_info($stuff,0,1);
					} else {
						$aff_retour .= "<strong>ID : ".$expl['object_id']."&nbsp;: ${msg[395]}</strong>";
					}
				else {
					$cb_display = "
						<div id=\"el!!id!!Parent\" class=\"notice-parent\">
				    		<span class=\"notice-heada\"><strong>Code-barre : $expl[content]&nbsp;: ${msg[395]}</strong></span>
				    		<br />
						</div>
						";
					$aff_retour .= $cb_display;
				}
			} // fin de liste
			print $end_result_list;
		} // fin si EXPL
		if ($caddie_type=="BULL") {
			// boucle de parcours des bulletins trouvés
			// inclusion du javascript de gestion des listes dépliables
			// début de liste
			while(list($cle, $expl) = each($liste)) {
				global $url_base_suppr_cart; 
				$url_base_suppr_cart = $url_base ;
				if ($bull_aff = show_bulletinage_info($expl["object_id"],0,1)) {
					$aff_retour .= $bull_aff;
				} else {
					$aff_retour .= "<strong>$form_cb_expl&nbsp;: ${msg[395]}</strong><br />";
				}
			} // fin de liste
			print $end_result_list;
		} // fin si BULL
	}
	return $aff_retour ;
}
