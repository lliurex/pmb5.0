<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serial_update.inc.php,v 1.82 2017-07-12 15:15:02 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($forcage)) $forcage = 0;

require_once($class_path."/vedette/vedette_composee.class.php");
require_once($class_path."/vedette/vedette_link.class.php");
require_once($class_path."/notice_relations.class.php");
require_once($class_path."/notice_relations_collection.class.php");
require_once($class_path."/thumbnail.class.php");

echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[346], $serial_header);

//droits d'acces
if ($gestion_acces_active==1) {
	require_once("$class_path/acces.class.php");
	$ac= new acces();
}

//verification des droits de modification notice
$acces_m=1;
if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
	$dom_1= $ac->setDomain(1);
	if($serial_id!=0) $acces_m = $dom_1->getRights($PMBuserid,$serial_id,8);
}
if ($acces_m==0) {

	error_message('', htmlentities($dom_1->getComment('mod_noti_error'), ENT_QUOTES, $charset), 1, '');

} else {
	
	// nettoyage des valeurs du form
	$f_tit1 = clean_string($f_tit1);
	$f_tit3 = clean_string($f_tit3);
	$f_tit4 = clean_string($f_tit4);
	//$f_indexation = clean_string($f_indexation);
	$f_lien = clean_string($f_lien);
	$f_eformat = clean_string($f_eformat);
	
	require_once($class_path."/notice_doublon.class.php");
	require_once($class_path."/authperso_notice.class.php");
	
	//Si control de dédoublonnage activé	
	if( $pmb_notice_controle_doublons) {
		$sign = new notice_doublon();
		$signature = $sign->gen_signature();
	}	
	
	if ($forcage == 1) {
		$tab= unserialize( urldecode($ret_url) );
		foreach($tab->GET as $key => $val){
			if(!is_array($val)) { $val=addslashes($val);}
			$GLOBALS[$key] = $val;	    
		}	
		foreach($tab->POST as $key => $val){
			if(!is_array($val)) { $val=addslashes($val);}
			$GLOBALS[$key] = $val;
		}
		$signature = $sign->gen_signature();
	} else if( ($pmb_notice_controle_doublons) != 0 && !$serial_id ) {
		
		//Si controle de dedoublonnage active	
		$signature = $sign->gen_signature();	
		$requete="select signature, niveau_biblio ,notice_id from notices where signature='$signature'";
	
		$result=pmb_mysql_query($requete, $dbh);	
		if ($dbls=pmb_mysql_num_rows($result)) {
			//affichage de l'erreur, en passant tous les param postes (serialise) pour l'eventuel forcage 	
			$tab=new stdClass();
			$tab->POST = $_POST;
			$tab->GET = $_GET;
			$ret_url= urlencode(serialize($tab));
	
			require_once("$class_path/serial_display.class.php");
			print "
				<br /><div class='erreur'>$msg[540]</div>
				<script type='text/javascript' src='./javascript/tablist.js'></script>
				<div class='row'>
					<div class='colonne10'>
						<img src='./images/error.gif' align='left'>
					</div>
					<div class='colonne80'>
						<strong>".$msg["gen_signature_erreur_similaire"]."</strong>
					</div>
				</div>
				<div class='row'>
					<form class='form-$current_module' name='dummy'  method='post' action='./catalog.php?categ=serials&sub=update&id=0'>
						<input type='hidden' name='forcage' value='1'>
						<input type='hidden' name='signature' value='$signature'>
						<input type='hidden' name='ret_url' value='$ret_url'>
						<input type='button' name='ok' class='bouton' value=' $msg[76] ' onClick='history.go(-1);'>
						<input type='submit' class='bouton' name='bt_forcage' value=' ".htmlentities($msg["gen_signature_forcage"], ENT_QUOTES,$charset)." '>
					</form>
					
				</div>
				";		
			if($dbls<$nb_per_page_search){
				$maxAffiche=$dbls;
				echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$dbls,$dbls)."</strong></div>";
			}else{
				$maxAffiche=$nb_per_page_search;
				echo "<div class='row'><strong>".sprintf($msg["gen_signature_erreur_similaire_nb"],$maxAffiche,$dbls)."</strong></div>";
			}
			$enCours=1;
			while($enCours<=$maxAffiche){
				$r=pmb_mysql_fetch_object($result);
				// on a affaire a un periodique
				$nt = new serial_display($r->notice_id,1,'catalog.php?categ=serials&sub=view&serial_id='.$r->notice_id);		
				echo "
					<div class='row'>
					$nt->result
			 	    </div>
					<script>document.getElementById('el".$r->notice_id."Child').setAttribute('startOpen','Yes');</script>
					<script type='text/javascript'>document.forms['dummy'].elements['ok'].focus();</script>";	
				$enCours++;
			}
			exit();
		}
	}
	
	// les valeurs passees sont mises en tableau pour etre passees
	// a la methode de mise a jour
	$table = array();
	$table['typdoc']        = $typdoc;
	$table['statut']        = $form_notice_statut;
	$table['commentaire_gestion'] =  $f_commentaire_gestion ;
	$table['num_notice_usage'] = $form_num_notice_usage;
	$table['thumbnail_url'] =  $f_thumbnail_url ;
	$table['code']          = $f_cb;
	$table['tit1']          = $f_tit1;
	$table['tit3']          = $f_tit3;
	$table['tit4']          = $f_tit4;
	
	// auteur principal
	$f_aut[] = array (
			'id' => $f_aut0_id,
			'fonction' => $f_f0_code,
			'type' => '0',
			'ordre' => 0 );
	// autres auteurs
	for ($i=0; $i<$max_aut1; $i++) {
		$var_autid = "f_aut1_id$i" ;
		$var_autfonc = "f_f1_code$i" ;
		$f_aut[] = array (
				'id' => ${$var_autid},
				'fonction' => ${$var_autfonc},
				'type' => '1',
				'ordre' => $i );
	}
	// auteurs secondaires
	for ($i=0; $i<$max_aut2 ; $i++) {
	
		$var_autid = "f_aut2_id$i" ;
		$var_autfonc = "f_f2_code$i" ;
		$f_aut[] = array (
				'id' => ${$var_autid},
				'fonction' => ${$var_autfonc},
				'type' => '2',
				'ordre' => $i );
	}
	
	$table['ed1_id']        = $f_ed1_id;
	$table['ed2_id']        = $f_ed2_id;
	$table['year']        	= $f_year;
	$table['n_gen']         = $f_n_gen;
	$table['n_contenu']		= $f_n_contenu;
	$table['n_resume']      = $f_n_resume;
	
	$date_parution = serial::get_date_parution($f_year);
	$table['date_parution']      = $date_parution;
	
// categories		
	if($tab_categ_order){
		$categ_order=explode(",",$tab_categ_order);
		$order=0;
		foreach($categ_order as $old_order){
			$var_categid = "f_categ_id$old_order" ;
			if($var_categid){
				$f_categ[] = array (
						'id' => ${$var_categid},
						'ordre' => $order );
				$order++;
			}	
		}
	}else{
		for ($i=0; $i< $max_categ ; $i++) {
			$var_categid = "f_categ_id$i" ;
			$f_categ[] = array (
					'id' => ${$var_categid},
					'ordre' => $i );
		}
	}
	
	$table['indexint']      = $f_indexint_id;
	$table['index_l']       = clean_tags($f_indexation);
	$table['lien']          = $f_lien;
	$table['eformat']       = $f_eformat;
	$table['niveau_biblio'] = $b_level;
	$table['niveau_hierar'] = $h_level;
	$table['signature'] 	= $signature;
	$table['indexation_lang'] = $indexation_lang;
	
	if($a2z_opac_show) $val=0; else $val=0x10;
	$table['opac_visible_bulletinage']= $opac_visible_bulletinage | $val;
	
	if($opac_serialcirc_active){
		$table['opac_serialcirc_demande'] = $opac_serialcirc_demande;
	}
		
	if($serial_id) {
		$req_new="select notice_is_new, notice_date_is_new from notices where notice_id=$serial_id ";
		$res_new=pmb_mysql_query($req_new, $dbh);
		if (pmb_mysql_num_rows($res_new)) {
			if($r=pmb_mysql_fetch_object($res_new)){
				if($r->notice_is_new==$f_notice_is_new){ // pas de changement du flag
					$req_notice_date_is_new= "";
				}elseif($f_notice_is_new){ // Changement du flag et affecté comme new
					$req_notice_date_is_new= ", notice_date_is_new =now() ";
				}else{// raz date
					$req_notice_date_is_new= ", notice_date_is_new ='' ";
				}
			}
		}
	}else{
		if($f_notice_is_new){ // flag affecté comme new en création
			$req_notice_date_is_new= ", notice_date_is_new =now() ";
		}
	}
	$table['notice_is_new'] = $f_notice_is_new+0;
	
	$p_perso=new parametres_perso("notices");
	$nberrors=$p_perso->check_submited_fields();
	
	if (!$nberrors) {
		//Pour la synchro rdf
		if($pmb_synchro_rdf){
			require_once($class_path."/synchro_rdf.class.php");
			$synchro_rdf=new synchro_rdf();
			if($serial_id){
				$synchro_rdf->delRdf($serial_id,0);
			}
		}
		$serial = new serial($serial_id);
		$update_result = $serial->update($table,$req_notice_date_is_new);
	} else {
		error_message_history($msg["notice_champs_perso"],$p_perso->error_message,1);
		exit();
	}
	
	// autorité personnalisées
	$authperso = new authperso_notice($serial_id);
	$authperso->save_form();		
	
	// vignette de la notice uploadé dans un répertoire
	$uploaded_thumbnail_url = thumbnail::create($update_result);
	if($uploaded_thumbnail_url) {
  		$query = "update notices set  thumbnail_url='".$uploaded_thumbnail_url."' where notice_id ='".$update_result."'";
		pmb_mysql_query($query);
	}
	
	if ($update_result) {
		//traitement des droits d'acces user_notice
		if ($gestion_acces_active==1 && $gestion_acces_user_notice==1) {
			if ($serial_id) {		
				$dom_1->storeUserRights(1, $update_result, $res_prf, $chk_rights, $prf_rad, $r_rad);
			} else {
				$dom_1->storeUserRights(0, $update_result, $res_prf, $chk_rights, $prf_rad, $r_rad);
			}
			//on applique les memes droits  d'acces user_notice aux bulletins et depouillements lies
			$q = "select num_notice from bulletins where bulletin_notice=$serial_id AND num_notice!=0 ";
			$q.= "union ";
			$q.= "select analysis_notice from analysis join bulletins on analysis_bulletin=bulletin_id where bulletin_notice=$serial_id ";
			$r = pmb_mysql_query($q,$dbh);
			if (pmb_mysql_num_rows($r)) {
				while(($row=pmb_mysql_fetch_object($r))) {
					$q = "replace into acces_res_1 select $row->num_notice, res_prf_num,usr_prf_num,res_rights,res_mask from acces_res_1 where res_num=$serial_id ";
					pmb_mysql_query($q,$dbh);
				}
			}
		}
		
		//traitement des droits acces empr_notice
		if ($gestion_acces_active==1 && $gestion_acces_empr_notice==1) {
			$dom_2= $ac->setDomain(2);
			if ($serial_id) {	
				$dom_2->storeUserRights(1, $update_result, $res_prf, $chk_rights, $prf_rad, $r_rad);
			} else {
				$dom_2->storeUserRights(0, $update_result, $res_prf, $chk_rights, $prf_rad, $r_rad);
			}
		}
		
		//Traitement des liens
		$notice_relations = notice_relations_collection::get_object_instance($update_result);
		$notice_relations->set_properties_from_form();
		$notice_relations->save();
		
		// Clean des vedettes
		$id_vedettes_links_deleted=serial::delete_vedette_links($update_result);

		// traitement des auteurs
		$rqt_del = "DELETE FROM responsability WHERE responsability_notice='$update_result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "INSERT INTO responsability (responsability_author, responsability_notice, responsability_fonction, responsability_type, responsability_ordre) VALUES ";
		$i=0;
		$var_name='notice_role_composed';
		$role_composed=${$var_name};
		$var_name='notice_role_autre_composed';
		$role_composed_autre=${$var_name};
		$var_name='notice_role_secondaire_composed';
		$role_composed_secondaire=${$var_name};	
		$id_vedettes_used=array();	
		while ($i<=count ($f_aut)-1) {
			$id_aut=$f_aut[$i]['id'];
			if ($id_aut) {
				$fonc_aut=$f_aut[$i]['fonction'];
				$type_aut = $f_aut[$i]['type'];
				$ordre_aut = $f_aut[$i]['ordre'];
				$rqt = $rqt_ins . " ('$id_aut','$update_result','$fonc_aut','$type_aut', $ordre_aut)";
				$res_ins = @pmb_mysql_query($rqt, $dbh);				
				$id_responsability=pmb_mysql_insert_id();
				if($pmb_authors_qualification){
					$id_vedette=0;
					switch($type_aut){
						case 0: 
							$id_vedette=update_vedette(stripslashes_array($role_composed[$ordre_aut]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_PRINCIPAL);
						break;
						case 1:  
							$id_vedette=update_vedette(stripslashes_array($role_composed_autre[$ordre_aut]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_AUTRE);
						break;					
						case 2:
							$id_vedette=update_vedette(stripslashes_array($role_composed_secondaire[$ordre_aut]),$id_responsability,TYPE_NOTICE_RESPONSABILITY_SECONDAIRE);
						break;
					}
					if($id_vedette)$id_vedettes_used[]=$id_vedette;
				}
			}
			$i++;
		}
		foreach ($id_vedettes_links_deleted as $id_vedette){
			if(!in_array($id_vedette,$id_vedettes_used)){
				$vedette_composee = new vedette_composee($id_vedette);
				$vedette_composee->delete();
			}
		}	
	
		// traitement des categories
		$rqt_del = "DELETE FROM notices_categories WHERE notcateg_notice='$update_result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "INSERT INTO notices_categories (notcateg_notice, num_noeud, ordre_categorie) VALUES ";
		while (list ($key, $val) = each ($f_categ)) {
			$id_categ=$val['id'];
			if ($id_categ) {
				$ordre_categ = $val['ordre'];
				$rqt = $rqt_ins . " ('$update_result','$id_categ', $ordre_categ ) " ; 
				$res_ins = @pmb_mysql_query($rqt, $dbh);
			}
		}
		
		// Indexation concepts
		global $thesaurus_concepts_active;
		require_once($class_path."/index_concept.class.php");
		
		if($thesaurus_concepts_active == 1){
			$index_concept = new index_concept($update_result, TYPE_NOTICE);
			$index_concept->save();
		}
	
		// traitement des langues
		// langues
		$f_lang_form = array();
		$f_langorg_form = array() ;
		for ($i=0; $i< $max_lang ; $i++) {
			$var_langcode = "f_lang_code$i" ;
			if (${$var_langcode}) $f_lang_form[] =  array ('code' => ${$var_langcode},'ordre' => $i);
		}
	
		// langues originales
		for ($i=0; $i< $max_langorg ; $i++) {
			$var_langorgcode = "f_langorg_code$i" ;
			if (${$var_langorgcode}) $f_langorg_form[] =  array ('code' => ${$var_langorgcode},'ordre' => $i);
		}
	
		$rqt_del = "delete from notices_langues where num_notice='$update_result' ";
		$res_del = pmb_mysql_query($rqt_del, $dbh);
		$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
		while (list ($key, $val) = each ($f_lang_form)) {
			$tmpcode_langue=$val['code'];
			if ($tmpcode_langue) {
				$tmpordre_langue = $val['ordre'];
				$rqt = $rqt_ins . " ('$update_result',0, '$tmpcode_langue',$tmpordre_langue) " ; 
				$res_ins = pmb_mysql_query($rqt, $dbh);
			}
		}
		
		// traitement des langues originales
		$rqt_ins = "insert into notices_langues (num_notice, type_langue, code_langue, ordre_langue) VALUES ";
		while (list ($key, $val) = each ($f_langorg_form)) {
			$tmpcode_langue=$val['code'];
			if ($tmpcode_langue) {
				$tmpordre_langue = $val['ordre'];
				$rqt = $rqt_ins . " ('$update_result',1, '$tmpcode_langue', $tmpordre_langue) " ; 
				$res_ins = @pmb_mysql_query($rqt, $dbh);
			}
		}
		
		//Traitement des champs perso
		$p_perso->rec_fields_perso($update_result);
		
		// Mise à jour de tous les index de la notice
		notice::majNoticesTotal($serial->serial_id);
		
		//Pour la synchro rdf
		if($pmb_synchro_rdf){
			$synchro_rdf->addRdf($serial->serial_id,0);
		}
		print "<div class='row'><div class='msg-perio'>".$msg['maj_encours']."</div></div>";
		$retour = "./catalog.php?categ=serials&sub=view&serial_id=".$serial->serial_id;
		print "
			<form class='form-$current_module' name=\"dummy\" method=\"post\" action=\"$retour\" style=\"display:none\">
				<input type=\"hidden\" name=\"id_form\" value=\"$id_form\">
			</form>
			<script type=\"text/javascript\">document.dummy.submit();</script>
			";
	} else {
		error_message($msg[4004] , $msg['catalog_serie_impossible'], 1, './catalog.php?categ=serials');
	}
	
}	

function update_vedette($data,$id,$type){
	if ($data["elements"]) {
		$vedette_composee = new vedette_composee($data["id"],'serial_authors');
		if ($data["value"]) {
			$vedette_composee->set_label($data["value"]);
		}	
		// On commence par réinitialiser le tableau des éléments de la vedette composée
		$vedette_composee->reset_elements();	
		// On remplit le tableau des éléments de la vedette composée
		$vedette_composee_id=0;
		$tosave=false;
		foreach ($data["elements"] as $subdivision => $elements) {
			if ($elements["elements_order"] !== "") {
				$elements_order = explode(",", $elements["elements_order"]);
				foreach ($elements_order as $position => $num_element) {
					if ($elements[$num_element]["id"] && $elements[$num_element]["label"]) {
						$tosave=true;
						$velement = $elements[$num_element]["type"];
						if(strpos($velement,"vedette_ontologies") === 0){
							$velement = "vedette_ontologies";
						}
						$available_field_class_name = $vedette_composee->get_at_available_field_class_name($velement);
						if($available_field_class_name['params']){
							$vedette_element = new $velement($available_field_class_name['params'],$available_field_class_name["num"],$elements[$num_element]["id"], $elements[$num_element]["label"]);
						}else{
							$vedette_element = new $velement($available_field_class_name["num"],$elements[$num_element]["id"], $elements[$num_element]["label"]);
						}
						$vedette_composee->add_element($vedette_element, $subdivision, $position);
					}
				}
			}
		}
		if($tosave)$vedette_composee_id = $vedette_composee->save();
	}
	if ($vedette_composee_id) {
		vedette_link::save_vedette_link($vedette_composee, $id, $type);
	}	
	return $vedette_composee_id;		
}