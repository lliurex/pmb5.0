<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pro.inc.php,v 1.58.2.1 2017-11-14 14:31:43 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($suite)) $suite = '';
if(!isset($form_cb)) $form_cb = '';
if(!isset($id_bannette)) $id_bannette = 0;
if(!isset($faire)) $faire = '';

print "<h1>".$msg['dsi_ban_pro']."</h1>" ;
switch($suite) {
    case 'acces':
    	$bannette = new bannette($id_bannette) ;
    	print $bannette->show_form(); 
    	
		if ($pmb_javascript_office_editor) {
			print $pmb_javascript_office_editor ;
			print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
		}
		break;
    case 'add':
    	$bannette = new bannette(0) ;
    	print $bannette->show_form();
		if ($pmb_javascript_office_editor) {
			print $pmb_javascript_office_editor ;
			print "<script type='text/javascript' src='".$base_path."/javascript/tinyMCE_interface.js'></script>";
		}
        break;
    case 'delete':
    	$bannette = new bannette($id_bannette) ;
    	print $bannette->delete();  
        print get_bannette_pro ($msg['dsi_ban_search'], $msg['dsi_ban_search_nom'], './dsi.php?categ=bannettes&sub=pro', stripslashes($form_cb));
		print pmb_bidi(dsi_list_bannettes_info($form_cb, 0, $id_classement)) ;
		break;
    case 'update':
    	if(!isset($majautocateg)) $majautocateg = '';
    	if(!isset($majautogroupe)) $majautogroupe = '';
    	$bannette = new bannette($id_bannette) ;
    	$anc_categorie_lecteurs=    $bannette->categorie_lecteurs ;
    	$anc_groupe_lecteurs=    	$bannette->groupe_lecteurs ;
		if ($form_actif) {
			$bannette->set_properties_from_form();
			$bannette->save(); 
			if (!$id_bannette){
				$id_bannette = $bannette->id_bannette;
			}
	    	if (($majautocateg || $majautogroupe) && $id_bannette) {
				if(!count($categorie_lecteurs)) $categorie_lecteurs=array();
				if(!count($groupe_lecteurs)) $groupe_lecteurs=array();
	    		$new_categorie_lecteurs = $bannette->categorie_lecteurs;
				$new_groupe_lecteurs = $groupe_lecteurs;
				if ($majautocateg && count($anc_categorie_lecteurs)){
					$req_lec = "select id_empr from empr where empr_categ in (" . implode(',', $anc_categorie_lecteurs) . ")";
					$res_lec=pmb_mysql_query($req_lec, $dbh) ;
					while ($lec=pmb_mysql_fetch_object($res_lec)) {
						pmb_mysql_query("delete from bannette_abon where num_empr='$lec->id_empr' and num_bannette='$id_bannette'", $dbh) ;
					}
				}
				if ($majautogroupe && count($anc_groupe_lecteurs)) {
					$req_lec = "select id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)
							where groupe_id in (".implode(',',$anc_groupe_lecteurs).")";					
					$res_lec=pmb_mysql_query($req_lec, $dbh) ;
					while ($lec=pmb_mysql_fetch_object($res_lec)) {
						pmb_mysql_query("delete from bannette_abon where num_empr='$lec->id_empr' and num_bannette='$id_bannette'", $dbh) ;
					}
				}				
				if ($majautocateg && count($new_categorie_lecteurs)) {
					$req_lec = "select distinct id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id) 
							where empr_categ in (".implode(',',$new_categorie_lecteurs).")";						
					$res_lec=pmb_mysql_query($req_lec, $dbh) ;
		    		while ($lec=pmb_mysql_fetch_object($res_lec)) {
		    			pmb_mysql_query("insert into bannette_abon (num_bannette, num_empr) values('$id_bannette', '$lec->id_empr')", $dbh) ;
	    			}					
				}				
				if ($majautogroupe && count($new_groupe_lecteurs)) {
	    			$req_lec = "select id_empr from empr left join empr_groupe on (empr.id_empr=empr_groupe.empr_id)
	    					where groupe_id in (" . implode(',', $new_groupe_lecteurs) . ")";		    			
		    		$res_lec=pmb_mysql_query($req_lec, $dbh) ;
		    		while ($lec=pmb_mysql_fetch_object($res_lec)) {
		    			pmb_mysql_query("insert into bannette_abon (num_bannette, num_empr) values('$id_bannette', '$lec->id_empr')", $dbh) ;
	    			}		    		
				}
    		}
		}
    	print get_bannette_pro ($msg['dsi_ban_search'], $msg['dsi_ban_search_nom'], './dsi.php?categ=bannettes&sub=pro', stripslashes($nom_bannette));
			
		print pmb_bidi(dsi_list_bannettes_info(stripslashes($nom_bannette), $id_bannette, $id_classement)) ;
        break;
	case 'duplicate':
     	print "<h1>$msg[catal_duplicate_bannette]</h1>"; 
		// routine de copie
		$bannette = new bannette($id_bannette) ;
		$bannette->id_bannette=0 ;
		$bannette->date_last_remplissage="";
		$bannette->aff_date_last_remplissage="";
		$bannette->date_last_envoi="";
		$bannette->aff_date_last_envoi="";
		$bannette->id_bannette_origine = $id_bannette;
		print pmb_bidi($bannette->show_form()) ;
        break;
    case 'search':
		print get_bannette_pro ($msg['dsi_ban_search'], $msg['dsi_ban_search_nom'], './dsi.php?categ=bannettes&sub=pro', stripslashes($form_cb));
		print pmb_bidi(dsi_list_bannettes_info($form_cb, $id_bannette, $id_classement)) ;
		break;
    case 'affect_equation':
    	if ($faire=="enregistrer") {
    		//Enregistrer les affectations
    		// selectionner les equations affichées
    		if ($id_classement>0) $equ = "select id_equation from equations where num_classement='$id_classement' and proprio_equation=0";
    		if ($id_classement==0) $equ = "select id_equation from equations where proprio_equation=0 ";
    		if ($id_classement==-1) $equ = "select id_equation from equations, bannette_equation where proprio_equation=0 and num_bannette='$id_bannette' and num_equation=id_equation";
    		$res = pmb_mysql_query($equ, $dbh) or die (pmb_mysql_error()." $equ ") ;
    		if (!$bannette_equation) $bannette_equation = array();
			while ($equa=pmb_mysql_fetch_object($res)) {
				pmb_mysql_query("delete from bannette_equation where num_equation='$equa->id_equation' and num_bannette='$id_bannette' ", $dbh) ; 
				$as = array_search($equa->id_equation,$bannette_equation) ;
				if (($as!==false) && ($as!==null) ) pmb_mysql_query("insert into bannette_equation set num_equation='$equa->id_equation', num_bannette='$id_bannette'", $dbh) ; 
				}
    		}
    	$bannette = new bannette($id_bannette) ;
    	print bannette_equation ($bannette->nom_bannette, $id_bannette) ;
		break;
    case 'affect_lecteurs':
    	if ($faire=="enregistrer") {
    		//Enregistrer les affectations
    		
    		// selectionner la localisation affichée
    		if ($pmb_lecteurs_localises && (string)$empr_location_id!="0") {
				if ((string)$empr_location_id=="") $empr_location_id=$deflt2docs_location;
				$restrict_loc = " and empr_location=$empr_location_id ";
			} else $restrict_loc = "";
    		
			if ($mail_abon) {
				$restrict_mail = " and empr_mail <>'' ";
			} else {
				$restrict_mail = "";
			}
    		
    		// selectionner les catégories affichées
    		if ($lect_restrict) $lect_query = " and empr_nom like '".str_replace("*","%",$lect_restrict."*")."'  order by nom_prenom, empr_cb " ;
    			else $lect_query = " order by nom_prenom, empr_cb limit 20 ";
   		
    		if ($quoi == "groups") {
    			if ($id_groupe>0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr,empr_groupe where id_empr=empr_id and groupe_id='$id_groupe' $restrict_loc $restrict_mail ".$lect_query;
    			if ($id_groupe==0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr where 1 $restrict_loc $restrict_mail ".$lect_query;
    			if ($id_groupe==-1) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr, bannette_abon where num_bannette='$id_bannette' and num_empr=id_empr $restrict_loc $restrict_mail ".$lect_query;
    		} else {
    			if ($id_categorie>0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr where empr_categ='$id_categorie' $restrict_loc $restrict_mail ".$lect_query;
    			if ($id_categorie==0) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr where 1 $restrict_loc $restrict_mail ".$lect_query;
    			if ($id_categorie==-1) $equ = "select id_empr, concat(empr_nom, ' ', empr_prenom) as nom_prenom, empr_cb from empr, bannette_abon where num_bannette='$id_bannette' and num_empr=id_empr $restrict_loc $restrict_mail ".$lect_query;
    		}
    		
    		$res = pmb_mysql_query($equ, $dbh) or die (pmb_mysql_error()." $equ ") ;
    		if (!$bannette_abon) $bannette_abon = array();
			while ($empr=pmb_mysql_fetch_object($res)) {
				pmb_mysql_query("delete from bannette_abon where num_empr='$empr->id_empr' and num_bannette='$id_bannette'", $dbh) ; 
				$as = array_search($empr->id_empr,$bannette_abon) ;
				$sel_mail="sel_mail_".$empr->id_empr;
				if (($as!==false) && ($as!==null) ) pmb_mysql_query("insert into bannette_abon set num_empr='$empr->id_empr', num_bannette='$id_bannette', 
						bannette_mail='".${$sel_mail}."'", $dbh) ; 
			}
    	}
    	$bannette = new bannette($id_bannette) ;
    	print bannette_lecteur ($bannette->nom_bannette, $id_bannette) ;
		break;
    default:
		echo window_title($database_window_title.$msg['dsi_menu_title']);
		print get_bannette_pro ($msg['dsi_ban_search'], $msg['dsi_ban_search_nom'], './dsi.php?categ=bannettes&sub=pro', stripslashes($form_cb));
		print pmb_bidi(dsi_list_bannettes_info($form_cb, $id_bannette, $id_classement)) ;
        break;
    }

