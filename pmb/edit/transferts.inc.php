<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: transferts.inc.php,v 1.20.2.1 2017-08-22 08:14:06 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($site_origine)) $site_origine = '';
if(!isset($site_destination)) $site_destination = '';
if(!isset($select_order)) $select_order = '';
if(!isset($f_etat_date)) $f_etat_date = '';

require_once ($include_path."/templates/transferts.tpl.php");
require_once ($class_path."/mono_display.class.php");
require_once ($class_path."/serial_display.class.php");

switch($dest) {
	case "TABLEAU":
		$worksheet = new spreadsheet();
		$worksheet->write_string(0,0,$msg["transferts_edition_titre"]." : ".$msg["transferts_edition_".$sub]);
		break;
	case "TABLEAUHTML":
		//le titre de la page
		print "<h1>".$msg["transferts_edition_titre"]."&nbsp;:&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
		break;
	default:
		//le titre de la page
		print "<h1>".$msg["transferts_edition_titre"]."&nbsp;&gt;&nbsp;".$msg["transferts_edition_".$sub]."</h1>";
		break;
}

//Création des variables de la requête
$type_transfert = '';

$join_locations = "INNER JOIN docs_location AS locd ON num_location_dest=locd.idlocation
	INNER JOIN docs_location AS loco ON num_location_source=loco.idlocation ";

$etat_date='';

//Filtres sur les sites
$filtres = '';
if ($site_origine=='') {
	$site_origine = $deflt_docs_location;
	if ($sub=='reception' || $sub=='retours') {
		$site_origine = 0;
	}
}
if ($site_destination=='') {
	$site_destination = 0;
	if ($sub=='reception' || $sub=='retours') {
		$site_destination = $deflt_docs_location;
	}
}
if ($site_origine!=0) {
	$filtres .= " AND ".($sub!='reception'?"num_location_source":"num_location_dest")."="  .$site_origine;
}
if ($site_destination!=0) {
	$filtres .= " AND ".($sub!='reception'?"num_location_dest":"num_location_source")."=" . $site_destination;
}

//En fonction du transfert
switch($sub) {
	case "validation":
		$etat_demande = 0;
		break;
	case "envoi":
		$etat_demande = 1;
		break;
	case "reception":
		$etat_demande = 2;
		break;
	case "retours":
		$etat_demande = 3;
		$type_transfert = ' AND type_transfert=1';
		$join_locations = "INNER JOIN docs_location AS locd ON num_location_source=locd.idlocation
	INNER JOIN docs_location AS loco ON num_location_dest=loco.idlocation ";
		//Filtre état retour
		switch ($f_etat_date) {
			case "1":
				$etat_date .= " AND (DATEDIFF(DATE_ADD(date_retour,INTERVAL -" . $transferts_nb_jours_alerte . " DAY),CURDATE())<=0";
				$etat_date .= " AND DATEDIFF(date_retour,CURDATE())>=0)";
				break;
			case "2":
				$etat_date .= " AND DATEDIFF(date_retour,CURDATE())<0";
				break;
		}
		break;
}

//Construction de la requête
$rqt = "SELECT  
	num_notice as val_id_notice, num_bulletin as val_id_bulletin, expl_cb as val_expl, expl_cote as val_cote, section_libelle as val_section, 
	locd.location_libelle as val_dest, loco.location_libelle as val_source, lender_libelle as val_expl_owner, motif as val_motif, 
	empr_cb as val_empr_cb, concat(empr_nom,' ',empr_prenom) as val_empr_nom_prenom, transfert_ask_user_num, transfert_send_user_num, transfert_ask_date, expl_id 
	FROM transferts 
	INNER JOIN transferts_demande ON id_transfert=num_transfert 
	INNER JOIN exemplaires ON num_expl=expl_id 
	INNER JOIN docs_section ON expl_section=idsection 
	".$join_locations."
	INNER JOIN lenders ON expl_owner=idlender 
	LEFT JOIN resa ON resa_trans=id_resa 
	LEFT JOIN empr ON resa_idempr=id_empr 
	WHERE etat_transfert=0 AND etat_demande=".$etat_demande.$type_transfert.$filtres.$etat_date;

if($select_order) {
	$rqt .= " ORDER BY ".$select_order;
} else {
	$rqt .= " ORDER BY val_cote";
}

$cols_supp = "";
$cols_supp_ligne = "";
// si la destination n'est pas précisé
if ($site_origine==0 || $transferts_edition_show_all_colls) {
	$cols_supp .= $transferts_edition_titre_source;
	$cols_supp_ligne .= $transferts_edition_ligne_source;
}

if ($site_destination==0 || $transferts_edition_show_all_colls) {
	$cols_supp .= $transferts_edition_titre_destination;
	$cols_supp_ligne .= $transferts_edition_ligne_destination;
}

// paramètres perso demandé dans $pmb_expl_data
$colonnesarray=explode(",",$pmb_expl_data);
$th_pp_perso='';
$titles_pp_perso=array();
if (strstr($pmb_expl_data, "#")) {
	$cp=new parametres_perso("expl");
	for ($i=0; $i<count($colonnesarray); $i++) {
		if (substr($colonnesarray[$i],0,1)=="#") {
			//champ personnalisé
			if (!$cp->no_special_fields) {
				$id=substr($colonnesarray[$i],1);
				$th_pp_perso.="<th>".htmlentities($cp->t_fields[$id][TITRE],ENT_QUOTES,$charset)."</th>";
				$titles_pp_perso[]=$cp->t_fields[$id][TITRE];
			}
		}
	}
}

$tabLigne = str_replace("!!colonnes_variables!!", $cols_supp_ligne, $transferts_edition_ligne);

//execution de la requete
$req = pmb_mysql_query($rqt);

switch($dest) {
	case "TABLEAU":
		$nbr_champs = @pmb_mysql_num_fields($req);
		$nbr_lignes = @pmb_mysql_num_rows($req);
		$j=0;
		$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_titre"]);
		$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_section"]);
		$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_cote"]);
		$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_expl"]);
		foreach ($titles_pp_perso as $title){
			$worksheet->write_string(2,$j++,$title);
		}
		$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_empr"]);
		$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_expl_owner"]);
		if (($site_origine==0 && $site_destination==0) || $transferts_edition_show_all_colls) {
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_source"]);
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_destination"]);
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_motif"]);
		}elseif ($site_origine==0) {
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_source"]);
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_motif"]);
		}elseif ($site_destination==0) {
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_destination"]);
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_motif"]);
		}else{
			$worksheet->write_string(2,$j++,$msg["transferts_edition_tableau_motif"]);
		}
		$worksheet->write_string(2,$j++,$msg["transferts_edition_ask_user"]);
		$worksheet->write_string(2,$j++,$msg["transferts_edition_send_user"]);
		$worksheet->write_string(2,$j++,$msg["transferts_popup_ask_date"]);

		$ligne=3;
		while ($value = pmb_mysql_fetch_array($req)) {
			$j=0;
			$worksheet->write_string($ligne,$j++,strip_tags(aff_titre($value[0], $value[1])));
			$worksheet->write_string($ligne,$j++,$value[4]);
			$worksheet->write_string($ligne,$j++,$value[3]);
			$worksheet->write_string($ligne,$j++,$value[2]);
			if($cp){
			// des champs perso sont à afficher			
				for ($i=0; $i<count($colonnesarray); $i++) {
					if (substr($colonnesarray[$i],0,1)=="#") {
						//id champ personnalisé
						$id=substr($colonnesarray[$i],1);
						$cp->get_values($value[14]); // expl_id
						if (!$cp->no_special_fields) {
							$aff_column=$cp->get_formatted_output($cp->values[$id], $id);
							$worksheet->write_string($ligne,$j++,$aff_column);
						}
					}
				}
			}
			$worksheet->write_string($ligne,$j++,$value[10]);
			$worksheet->write_string($ligne,$j++,$value[7]);
			if (($site_origine==0 && $site_destination==0) || $transferts_edition_show_all_colls) {
				$worksheet->write_string($ligne,$j++,$value[6]);
				$worksheet->write_string($ligne,$j++,$value[5]);
				$worksheet->write_string($ligne,$j++,$value[8]);
			}elseif ($site_origine==0) {
				$worksheet->write_string(2,$j,$msg["transferts_edition_tableau_source"]);
				$worksheet->write_string($ligne,$j++,$value[6]);
				$worksheet->write_string($ligne,$j++,$value[8]);
			}elseif ($site_destination==0) {
				$worksheet->write_string(2,$j,$msg["transferts_edition_tableau_destination"]);
				$worksheet->write_string($ligne,$j++,$value[5]);
				$worksheet->write_string($ligne,$j++,$value[8]);
			}else{
				$worksheet->write_string($ligne,$j++,$value[8]);
			}
			$worksheet->write_string($ligne,$j++,aff_user($value[11]));
			$worksheet->write_string($ligne,$j++,aff_user($value[12]));
			$worksheet->write_string($ligne,$j++,formatdate($value[13]));			
			
			$ligne++;
		
		}
		$worksheet->set_column(0, $j, 18);
		$worksheet->download('edition.xls');
		break;
	case "TABLEAUHTML":
		//le nombre de colonnes dans la requete pour remplacer les champs dans le template
		$nbCols = pmb_mysql_num_fields($req);
		
		$transferts_list = "<table  border='1' style='border-collapse: collapse'>" ;
		$transferts_list .= "<tr>
				<th>".$msg["transferts_edition_tableau_titre"]."</th>
				<th>".$msg["transferts_edition_tableau_section"]."</th>
				<th>".$msg["transferts_edition_tableau_cote"]."</th>
				<th>".$msg["transferts_edition_tableau_expl"]."</th>
				!!pp_perso!!		
				<th>".$msg["transferts_edition_tableau_empr"]."</th>
				<th>".$msg["transferts_edition_tableau_expl_owner"]."</th>
				<th>".$msg["transferts_popup_ask_date"]."</th>
				!!colonnes_variables!!
				<th>".$msg["transferts_edition_tableau_motif"]."</th>
				<th>".$msg["transferts_edition_ask_user"]."</th>
				<th>".$msg["transferts_edition_send_user"]."</th>
			</tr>
			!!lignes_tableau!!
		</table>";

		$tmpAff = "";
		$nb = 0;
		//on boucle sur la liste
		while ($value = pmb_mysql_fetch_array($req)) {
		
			//pour la coloration
			if ($nb % 2)
				$tmpLigne = str_replace("!!class_ligne!!", "odd", $tabLigne);
			else			
				$tmpLigne = str_replace("!!class_ligne!!", "even", $tabLigne);
			
			//on parcours toutes les colonnes de la requete
			for($i=0;$i<$nbCols;$i++) {
				$tmpLigne = str_replace("!!".pmb_mysql_field_name($req,$i)."!!",$value[$i],$tmpLigne);
			}
		
			//affichage du titre
			$tmpLigne = str_replace("!!val_titre!!", aff_titre($value[0], $value[1]), $tmpLigne);
			//affichage du demandeur
			$tmpLigne = str_replace("!!transfert_ask_user!!",aff_user($value[11]), $tmpLigne);			
			//affichage de l'utilisateur qui fait l'envoi
			$tmpLigne = str_replace("!!transfert_send_user!!",aff_user($value[12]), $tmpLigne);						
			//affichage de la date de la demande d'envoi
			$tmpLigne = str_replace("!!transfert_ask_date_format!!",formatdate($value[13]), $tmpLigne);

			//si on a des pp perso en plus
			$tmpLigne = str_replace("!!pp_perso!!",get_aff_pp_perso($cp,$colonnesarray,$value[14]), $tmpLigne);
			
			//on ajoute la ligne a la liste
			$tmpAff .= $tmpLigne;
			$nb++;
		
		} //fin while
		
		//on met les lignes du tableau dans le tableau
		$transferts_list = str_replace("!!lignes_tableau!!",$tmpAff,$transferts_list);
		
		//si on a des colonnes en plus
		$transferts_list = str_replace("!!colonnes_variables!!", $cols_supp, $transferts_list);

		//si on a des pp perso en plus
		$transferts_list = str_replace("!!pp_perso!!", $th_pp_perso, $transferts_list);
		
		//on affiche la page !
		echo $transferts_list;
		break;
	default:
		//le nombre de colonnes dans la requete pour remplacer les champs dans le template
		$nbCols = pmb_mysql_num_fields($req);
		
		$tmpAff = "";
		$nb = 0;
		//on boucle sur la liste
		while ($value = pmb_mysql_fetch_array($req)) {
		
			//pour la coloration
			if ($nb % 2)
				$tmpLigne = str_replace("!!class_ligne!!", "odd", $tabLigne);
			else			
				$tmpLigne = str_replace("!!class_ligne!!", "even", $tabLigne);
			
			//on parcours toutes les colonnes de la requete
			for($i=0;$i<$nbCols;$i++) {
				$tmpLigne = str_replace("!!".pmb_mysql_field_name($req,$i)."!!",$value[$i],$tmpLigne);
			}
		
			//affichage du titre
			$tmpLigne = str_replace("!!val_titre!!", aff_titre($value[0], $value[1]), $tmpLigne);
			//affichage du demandeur
			$tmpLigne = str_replace("!!transfert_ask_user!!",aff_user($value[11]), $tmpLigne);			
			//affichage de l'utilisateur qui fait l'envoi
			$tmpLigne = str_replace("!!transfert_send_user!!",aff_user($value[12]), $tmpLigne);				
			//affichage de la date de la demande d'envoi
			$tmpLigne = str_replace("!!transfert_ask_date_format!!",formatdate($value[13]), $tmpLigne);
			
			//si on a des pp perso en plus
			$tmpLigne = str_replace("!!pp_perso!!",get_aff_pp_perso($cp,$colonnesarray,$value[14]), $tmpLigne);
			//on ajoute la ligne a la liste
			$tmpAff .= $tmpLigne;
			$nb++;
		
		} //fin while
		
		//on met les lignes du tableau dans le tableau
		$tmpAff = str_replace("!!lignes_tableau!!",$tmpAff,$transferts_edition_tableau);

		//si on a des colonnes en plus
		$tmpAff = str_replace("!!colonnes_variables!!", $cols_supp, $tmpAff);

		//si on a des pp perso en plus
		$tmpAff = str_replace("!!pp_perso!!", $th_pp_perso, $tmpAff);
		
		//la sub pour retomber sur ses pattes
		$tmpAff = str_replace("!!sub!!",$sub,$tmpAff);
		
		//les filtres
		//pour la liste des origines
		if ($sub != 'retours') {
			$filtres = str_replace("!!liste_sites_origine!!",creer_liste_localisations($site_origine),$transferts_edition_filtre_source);
		} else {
			$filtres = str_replace("!!liste_sites_origine!!",creer_liste_localisations($site_origine),$transferts_edition_filtre_source_retours);
		}
		//pour la liste de destination
		if ($sub != 'retours') {
			$filtres .= str_replace("!!liste_sites_destination!!",creer_liste_localisations($site_destination),$transferts_edition_filtre_destination);
		} else {
			$filtres .= str_replace("!!liste_sites_destination!!",creer_liste_localisations($site_destination),$transferts_edition_filtre_destination_retours);
			//le filtre de l'etat de la date
			$filtres .= str_replace("!!sel_" . $f_etat_date . "!!", "selected", $transferts_retour_filtre_etat);
		}

		$filtres .= str_replace("!!liste_order!!",creer_liste_order($select_order),$transferts_edition_order);
		
		//la sub pour retomber sur ses pattes
		$tmpAff = str_replace("!!filtres_edition!!",$filtres,$tmpAff);
		
		//on affiche la page !
		echo $tmpAff;
		break;
}

//***********************************************************************************************************

function get_aff_pp_perso($cp,$colonnesarray,$expl_id){
	$fields_persos='';
	if($cp){
		// des champs perso sont à afficher
		for ($i=0; $i<count($colonnesarray); $i++) {
			if (substr($colonnesarray[$i],0,1)=="#") {
				//id champ personnalisé
				$id=substr($colonnesarray[$i],1);
				$cp->get_values($expl_id); // expl_id
				if (!$cp->no_special_fields) {
					$aff_column=$cp->get_formatted_output($cp->values[$id], $id);
					if (!$aff_column) $aff_column="&nbsp;";
					$fields_persos .= "<td>".$aff_column."</td>";
				}
			}
		}
	}
	return $fields_persos;
}

//renvoi le titre de l'exemplaire pour le tableau
function aff_titre($id_notice,$id_bulletin) {
	if ($id_notice!=0) {
		//c'est une notice
		$disp = new mono_display($id_notice);

	} else {
		//c'est un bulletin
		$disp = new bulletinage_display($id_bulletin);
	}
	
	return $disp->header;
}

function aff_user($id) {
	if(!$id) return '';
	$result = pmb_mysql_query('SELECT username FROM users where userid="'.$id.'" ');
	if ($r = pmb_mysql_fetch_object($result)) {
		return $r->username;
	}	
	return '';
}

//***********************************************************************************************************

//crée la liste des localisations en précisant une de sélectionner et si on rajoute une ligne tous
function creer_liste_localisations($loc_select,$tous = true) {
	global $msg;

	//la requete
	$rqt="SELECT idlocation, location_libelle FROM docs_location ORDER BY location_libelle ";
	$req = pmb_mysql_query($rqt);
	
	
	//initialisation de la liste
	if ($tous) 
		$tmpListe = "<option value=0>".$msg["all_location"]."</option>";
	else
		$tmpListe = "";
	
	//on parcours
	while ($value = pmb_mysql_fetch_array($req)) {
		
		$tmpListe .= "<option value=".$value[0]; 
		
		if ($value[0]==$loc_select)
			$tmpListe .= " selected";
		
		$tmpListe .= ">".$value[1]."</option>";
		
	}
	
	return $tmpListe;

}


function creer_liste_order($order_selected) {
	global $msg;

	$order_list=array(
			array(
				'msg' => $msg['transferts_edition_order_cote'],	
				'value' => 'val_cote',						
			),
			array(
				'msg' => $msg['transferts_edition_order_user'],	
				'value' => 'transfert_ask_user_num, transfert_ask_user_num, val_cote',						
			),
			array(
				'msg' => $msg['transferts_edition_order_send_user'],	
				'value' => 'transfert_send_user_num, transfert_ask_user_num, val_cote',						
			),
			array(
				'msg' => $msg['transferts_edition_order_empr'],	
				'value' => 'val_empr_cb, val_cote, transfert_ask_user_num',						
			),
			array(
				'msg' => $msg['transferts_edition_order_ask_date'],	
				'value' => 'transfert_ask_date, val_empr_cb ',						
			),
	);
	if(!$order_selected)$order_selected=$order_list[0]['value'];
	$tmpListe='';
	foreach ($order_list as $elt_order){
		if($elt_order['value']==$order_selected) $selected=' selected '; else $selected='';
		$tmpListe.= "<option value='".$elt_order['value']."' ".$selected.">".$elt_order['msg']."</option>";
	}
	return $tmpListe;
}
