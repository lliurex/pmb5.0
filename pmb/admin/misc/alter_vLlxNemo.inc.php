<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter_vLlxNemo.inc.php, migración BD a v5.28 (Lliurex 16+ Xenial) desde v.4.47 (Lliurex 12.06 Nemo)


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

settype ($action,"string");

mysql_query("set names latin1 ", $dbh);

switch ($version_pmb_bdd) {
	case "vLlxNemo":
//	case "v4.47":
//-------------------LLIUREX 06/03/2018-------
		// 72 actualizaciones desde nemo (v4.47) a xenial (v5.28)
	
		$increment=100/72;
//-------------------FIN LLIUREX 06/03/2018------
		$action=$increment;
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
		@set_time_limit(0);
		$rqt = "alter TABLE connectors change connector_id connector_id varchar(20) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE connectors connector_id varchar(20)");

		$rqt = "alter TABLE connectors_sources change id_connector id_connector varchar(20) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources id_connector varchar(20)");

		$rqt = "alter TABLE entrepots change connector_id connector_id varchar(20) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE entrepots connector_id varchar(20)");

		$rqt = "alter TABLE entrepots change ref ref varchar(220) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE entrepots ref varchar(220)");

		$rqt = "alter TABLE entrepots change search_id search_id varchar(32) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE entrepots search_id varchar(32)");

		$rqt = "ALTER TABLE entrepots DROP INDEX recid";
		echo traite_rqt($rqt,"ALTER TABLE entrepots DROP INDEX recid");

		$rqt = "ALTER TABLE entrepots DROP INDEX ufield";
		echo traite_rqt($rqt,"ALTER TABLE entrepots DROP INDEX ufield");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_external_search' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'allow_external_search', '0', 'Autorisation ou non de la recherche par connecteurs externes (masque également le menu Administration-Connecteurs) \n 0 : Non \n 1 : Oui','',0)";
			echo traite_rqt($rqt,"insert pmb_external_term_search='0' into parametres");
			}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.48");
//		break;

//	case "v4.48": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		$rqt = "CREATE TABLE transferts_demandes (
				id_transfert int(10) unsigned NOT NULL auto_increment,
				num_location_source int(10) unsigned NOT NULL DEFAULT 0,
				num_location_dest int(10) unsigned NOT NULL DEFAULT 0,
				num_expl int(10) unsigned NOT NULL DEFAULT 0,
				date_creation datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				etat_transfert smallint(1) unsigned NOT NULL DEFAULT 0,
				date_confirmation datetime NOT NULL default '0000-00-00 00:00:00',
				num_precedent int(10) unsigned NOT NULL DEFAULT 0,
				motif longtext NOT NULL,
				motif_refus longtext NOT NULL,
				accuse_reception tinyint(1) unsigned NOT NULL DEFAULT 0,
				origine varchar(50) NOT NULL DEFAULT '',
				type_objet varchar(15) NOT NULL DEFAULT '',
				expl_ancien_statut smallint(5) unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_transfert),
				UNIQUE (num_location_source, num_location_dest, num_expl, date_creation)
				)";
		echo traite_rqt($rqt,"CREATE TABLE transferts_demandes ");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'transferts' and sstype_param='gestion_transferts' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param) VALUES ('transferts', 'gestion_transferts', '0', 'Activation de la gestion des transferts\n 0: Non \n 1: Oui', 'transferts')";
			echo traite_rqt($rqt,"insert transferts_gestion_transferts='0' into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'transferts' and sstype_param='transfert_statut' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param) VALUES ('transferts', 'transfert_statut', '0', 'Id du statut dans lequel sont placés les documents en cours de transfert', 'transferts')";
			echo traite_rqt($rqt,"insert transferts_transfert_statut='0' into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='bannette_notices_order' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param) VALUES ('dsi', 'bannette_notices_order', 'index_serie, tnvol, index_sew', 'Ordre des notices au sein de la bannette: \n index_serie, tnvol, index_sew : par titre \n create_date desc : par date de saisie décroissante \n rand() : aléatoire')";
			echo traite_rqt($rqt,"insert dsi_bannette_notices_order='index_serie, tnvol, index_sew' into parametres");
		}

		$rqt="alter table rss_flux add rss_flux_content longblob NOT NULL default '', add rss_flux_last timestamp NOT NULL default '0000-00-00 00:00:00'";
		echo traite_rqt($rqt,"alter table rss_flux add rss_flux_content, add rss_flux_last ");
  
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_show' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('opac','websubscribe_show', '0', 'Afficher la possibilité de s\'inscrire en ligne ? \n 0: Non \n 1: Oui', 'f_modules', '0')";
			echo traite_rqt($rqt,"insert opac_websubscribe_show='0' into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_empr_status' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('opac','websubscribe_empr_status', '2,1', 'Id des statuts des inscrits séparés par une virgule: en attente de validation, validés', 'f_modules', '0')";
			echo traite_rqt($rqt,"insert opac_websubscribe_empr_status='2,1' into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_empr_categ' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('opac','websubscribe_empr_categ', '0', 'Id de la catégorie des inscrits par le web non adhérents complets', 'f_modules', '0')";
			echo traite_rqt($rqt,"insert opac_websubscribe_empr_categ='0' into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_empr_stat' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('opac','websubscribe_empr_stat', '0', 'Id du code statistique des inscrits par le web non adhérents complets', 'f_modules', '0')";
			echo traite_rqt($rqt,"insert opac_websubscribe_empr_stat='0' into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_valid_limit' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('opac','websubscribe_valid_limit', '24', 'Durée maximum des inscriptions en attente de validation', 'f_modules', '0')";
			echo traite_rqt($rqt,"insert opac_websubscribe_valid_limit='24' into parametres");
			}
		$rqt = "update parametres set comment_param=concat(comment_param,' \n 3 : consultation et ajout anonymes possibles') where type_param='opac' and sstype_param='avis_allow'";
		echo traite_rqt($rqt,"update comment_param on opac_avis_allow");

		$rqt = "ALTER TABLE bulletins drop index i_num_notice " ;
		echo traite_rqt($rqt,"drop index i_num_notice");
		$rqt = "ALTER TABLE bulletins ADD INDEX i_num_notice (num_notice) ";
		echo traite_rqt($rqt,"ADD INDEX bulltins(num_notice) ") ;
		
		$rqt = "DELETE FROM explnum WHERE explnum_bulletin ='0' AND explnum_notice NOT IN (SELECT notice_id FROM notices) ";
		echo traite_rqt($rqt,"drop explnum without notice");
		$rqt = "DELETE FROM explnum WHERE explnum_notice ='0' AND explnum_bulletin NOT IN (SELECT bulletin_id FROM bulletins) ";
		echo traite_rqt($rqt,"drop explnum without bulletin");

		$rqt = "ALTER TABLE caddie_content CHANGE blob_type blob_type VARCHAR(100) default ''";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content CHANGE blob_type VARCHAR(100)");
		
		$rqt = "ALTER TABLE caddie_content CHANGE content content VARCHAR( 100 ) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content CHANGE content VARCHAR( 100 )");
		
		$rqt = "ALTER TABLE caddie_content DROP PRIMARY KEY ";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content DROP PRIMARY KEY ");
		$rqt = "ALTER TABLE caddie_content ADD PRIMARY KEY pk_caddie_content ( caddie_id , object_id , content )";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content ADD PRIMARY KEY ( caddie_id , object_id , content ) : <font color=red>ATTENTION, Cette requête DOIT fonctionner, en cas d'échec, vider vos paniers et recommencer ! <br />This query MUST work, if it doesn't, empty your baskets and retry !</font>");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.49");
//		break;

//	case "v4.49": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_html_format' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'mail_html_format', '1', 'Format d\'envoi des mails : \n 0: Texte brut\n 1: HTML \nAttention, ne fonctionne qu\'en mode d\'envoi smtp !')";
			echo traite_rqt($rqt,"insert pmb_mail_html_format='1' into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='mail_html_format' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'mail_html_format', '1', 'Format d\'envoi des mails à partir de l\'opac: \n 0: Texte brut\n 1: HTML \nAttention, ne fonctionne qu\'en mode d\'envoi smtp !', 'a_general', 0)";
			echo traite_rqt($rqt,"insert opac_mail_html_format='1' into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_empr_location' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('opac','websubscribe_empr_location', '0', 'Id de la localisation des inscrits par le web non adhérents complets', 'f_modules', '0')";
			echo traite_rqt($rqt,"insert opac_websubscribe_empr_location='0' into parametres");
			}
			
		$rqt = "ALTER TABLE empr ADD cle_validation VARCHAR( 255 ) DEFAULT '' NOT NULL ";
		echo traite_rqt($rqt,"alter table empr add cle_validation");
		$rqt = "ALTER TABLE empr CHANGE empr_creation empr_creation DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ";
		echo traite_rqt($rqt,"alter table empr CHANGE empr_creation DATETIME ");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.50");
//		break;

//	case "v4.50": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		$rqt = "ALTER TABLE bannettes ADD typeexport VARCHAR( 20 ) NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE bannettes ADD typeexport ");
		$rqt = "ALTER TABLE bannettes ADD prefixe_fichier VARCHAR( 50 ) NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE bannettes ADD prefixe_fichier ");
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_bannette_export' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'allow_bannette_export', '0', 'Possibilité pour les lecteurs de recevoir les notices de leurs bannettes privées en pièce jointe au mail ?\n 0: Non \n 1: Oui','l_dsi')";
			echo traite_rqt($rqt,"insert opac_allow_bannette_export=0 into parametres");
			}

		$rqt = "UPDATE parametres SET comment_param = 'Numéro de carte de lecteur automatique ?\n 0: Non (si utilisation de cartes pré-imprimées)\n 1: Oui, entièrement numérique\n 2,a,b,c: Oui avec préfixe: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)' WHERE type_param='pmb' and sstype_param='num_carte_auto' " ;
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param =... WHERE type_param='pmb' and sstype_param='num_carte_auto'");

		$rqt = "ALTER TABLE users ADD value_deflt_antivol VARCHAR( 50 ) NOT NULL default '0' AFTER value_email_bcc" ;		
		echo traite_rqt($rqt,"ALTER TABLE users ADD value_deflt_antivol");

		$rqt = "ALTER TABLE connectors_sources ADD opac_allowed INT( 3 ) UNSIGNED DEFAULT 0 NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD opac_allowed");
		
		$rqt = "ALTER TABLE caddie_content DROP INDEX caddie_id";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content DROP INDEX caddie_id");
		$rqt = "ALTER TABLE caddie_content CHANGE content content VARCHAR( 100 ) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content CHANGE content VARCHAR( 100 )");
		$rqt = "ALTER TABLE caddie_content DROP PRIMARY KEY ";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content DROP PRIMARY KEY ");
		$rqt = "ALTER TABLE caddie_content ADD PRIMARY KEY pk_caddie_content ( caddie_id , object_id , content )";
		echo traite_rqt($rqt,"ALTER TABLE caddie_content ADD PRIMARY KEY ( caddie_id , object_id , content ) : <font color=red>ATTENTION, Cette requête DOIT fonctionner, en cas d'échec, vider vos paniers et recommencer ! <br />This query MUST work, if it doesn't, empty your baskets and retry !</font>");

		$rqt = "CREATE TABLE procs_classements (idproc_classement smallint(5) unsigned NOT NULL auto_increment,libproc_classement varchar(255) NOT NULL default '', PRIMARY KEY (idproc_classement) )";
		echo traite_rqt($rqt,"CREATE TABLE procs_classements ");
		$rqt = "ALTER TABLE procs ADD num_classement INT( 5 ) UNSIGNED DEFAULT 0 NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE procs ADD num_classement");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.51");
//		break;

//	case "v4.51": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE responsability CHANGE responsability_fonction responsability_fonction VARCHAR( 4 ) NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE responsability CHANGE responsability_fonction VARCHAR( 4 )");
		
		$rqt = "ALTER TABLE bulletins CHANGE num_notice num_notice INT( 10 ) UNSIGNED not null DEFAULT 0 ";
		echo traite_rqt($rqt,"ALTER TABLE bulletins CHANGE num_notice ");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.52");
//		break;

//	case "v4.52": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE empr_caddie_content DROP PRIMARY KEY ";
		echo traite_rqt($rqt,"ALTER TABLE empr_caddie_content DROP PRIMARY KEY ");
		$rqt = "ALTER TABLE empr_caddie_content ADD PRIMARY KEY empr_caddie_id (empr_caddie_id,object_id)";
		echo traite_rqt($rqt,"ALTER TABLE empr_caddie_content ADD PRIMARY KEY empr_caddie_id (empr_caddie_id,object_id) : <font color=red>ATTENTION, Cette requête DOIT fonctionner, en cas d'échec, vider vos paniers d'emprunteurs et recommencer ! <br />This query MUST work, if it doesn't, empty your borrower's baskets and retry !</font>");
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.53");
//		break;

//	case "v4.53": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='expl_data' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'expl_data', 'expl_cb,expl_cote,tdoc_libelle,location_libelle,section_libelle', 'Colonne des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,tdoc_libelle,location_libelle,section_libelle','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_expl_data=expl_cb,expl_cote,tdoc_libelle,location_libelle,section_libelle into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='expl_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'expl_order', 'location_libelle,section_libelle,expl_cote,tdoc_libelle', 'Ordre d\'affichage des exemplaires, dans l\'ordre donné, séparé par des virgules : location_libelle,section_libelle,expl_cote,tdoc_libelle','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_expl_order=location_libelle,section_libelle,expl_cote,tdoc_libelle into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='curl_available' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'curl_available', '1', 'La librairie cURL est-elle disponible pour les interrogations RSS notamment ? \n 0: Non \n 1: Oui','a_general')";
			echo traite_rqt($rqt,"insert opac_curl_available=1 into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='curl_available' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'curl_available', '1', 'La librairie cURL est-elle disponible pour les interrogations RSS notamment ? \n 0: Non \n 1: Oui')";
			echo traite_rqt($rqt,"insert pmb_curl_available=1 into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='thesaurus_defaut' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion, section_param) VALUES (0, 'opac', 'thesaurus_defaut', '1', 'Identifiant du thésaurus par défaut.', 0, 'i_categories') ";
			echo traite_rqt($rqt, "insert opac_thesaurus_defaut=1 into parameters");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='recherches_pliables' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion, section_param) VALUES (0, 'opac', 'recherches_pliables', '0', 'Les cases à cocher de la recherche simple sont-elles pliées ? \n 0: Non \n 1: Oui et pliée par défaut \n 2: Oui et dépliée par défaut', 0, 'c_recherche') ";
			echo traite_rqt($rqt, "insert opac_recherches_pliables=0 into parameters");
			}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.54");
//		break;

//	case "v4.54": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='pmb' and sstype_param='rfid_ip_port' "))==0){
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param, comment_param, gestion) VALUES ('pmb', 'rfid_ip_port', '192.168.0.10,SerialPort=10;', 'Association ip du poste de prêt et Numéro du port utilisé par le serveur RFID. Ex: 192.168.0.10,SerialPort=10; IpPosteClient,SerialPort=NumPortPlatine; séparer par des points-virgules pour désigner tous les postes' , '0')";
			echo traite_rqt($rqt, "insert pmb_rfid_ip_port into parameters");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='pmb' and sstype_param='pret_timeout_temp' "))==0){
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param,comment_param ) VALUES ('pmb', 'pret_timeout_temp', '15', 'Temps en minutes, après lequel un prêt temporaire est effacé' )";
			echo traite_rqt($rqt, "insert pmb_pret_timeout_temp into parameters");
			}
		$rqt = "ALTER TABLE pret ADD pret_temp VARCHAR( 50 ) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE pret ADD pret_temp VARCHAR( 50 ) NOT NULL default ''");

		$rqt = "create table external_count (rid bigint unsigned not null auto_increment, recid varchar(255) not null default '', index(recid), primary key(rid))";
		echo traite_rqt($rqt,"create table external_count ");

		$rqt = "insert into external_count (recid) select distinct recid from entrepots";
		echo traite_rqt($rqt,"insert into external_count ... ");

		$rqt = "update external_count, entrepots set entrepots.recid=rid where external_count.recid=entrepots.recid";
		echo traite_rqt($rqt,"update external_count, entrepots set ... ");

		$rqt = "alter table entrepots modify recid bigint unsigned not null default 0";
		echo traite_rqt($rqt,"alter table entrepots modify recid bigint not null default 0 ");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.55");
//		break;

//	case "v4.55": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "alter table entrepots modify recid bigint unsigned not null default 0";
		echo traite_rqt($rqt,"alter table entrepots modify recid bigint not null default 0 ");

		$rqt = "ALTER TABLE entrepots drop index i_recid_source_id " ;
		echo traite_rqt($rqt,"drop index i_recid_source_id");
		$rqt = "ALTER TABLE entrepots ADD INDEX i_recid_source_id (recid,source_id) ";
		echo traite_rqt($rqt,"ADD INDEX i_recid_source_id ") ;
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.56");
//		break;

//	case "v4.56": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='permalink' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'permalink', '0', 'Afficher l\'Id de la notice avec un lien permanent ? \n 0: Non \n 1: Oui','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_permalink=0 into parametres");
			}
			
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='3after_recouvrement' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param, comment_param, section_param, gestion) VALUES ('pdflettreretard', '3after_recouvrement', 'Sans nouvelles de votre part dans les sept jours, nous nous verrons contraints de déléguer au Trésor Public le recouvrement des ouvrages ci-dessus.', 'Texte apparaissant après la liste des ouvrages en recouvrement s\'il n\'y a pas d\'autres ouvrages en niveau 1 et 2', '', '0')";
			echo traite_rqt($rqt,"insert pdflettreretard_3after_recouvrement=... into parametres");
			}
			
		$rqt = "ALTER TABLE notices DROP INDEX i_notice_n_biblio ";
		echo traite_rqt($rqt,"DROP INDEX i_notice_n_biblio ") ;
		$rqt = "ALTER TABLE notices ADD INDEX i_notice_n_biblio (niveau_biblio) ";
		echo traite_rqt($rqt,"ADD INDEX i_notice_n_biblio ") ;
		$rqt = "ALTER TABLE notices DROP INDEX i_notice_n_hierar ";
		echo traite_rqt($rqt,"DROP INDEX i_notice_n_hierar ") ;
		$rqt = "ALTER TABLE notices ADD INDEX i_notice_n_hierar (niveau_hierar) ";
		echo traite_rqt($rqt,"ADD INDEX i_notice_n_hierar ") ;

		$rqt = "ALTER TABLE pret_archive DROP INDEX i_pa_idempr ";
		echo traite_rqt($rqt,"DROP INDEX i_pa_idempr ") ;
		$rqt = "ALTER TABLE pret_archive ADD INDEX i_pa_idempr (arc_id_empr) ";
		echo traite_rqt($rqt,"ADD INDEX i_pa_idempr ") ;

		$rqt = "ALTER TABLE pret_archive DROP INDEX i_pa_expl_notice ";
		echo traite_rqt($rqt,"DROP INDEX i_pa_expl_notice ") ;
		$rqt = "ALTER TABLE pret_archive ADD INDEX i_pa_expl_notice (arc_expl_notice) ";
		echo traite_rqt($rqt,"ADD INDEX i_pa_expl_notice ") ;

		$rqt = "ALTER TABLE pret_archive DROP INDEX i_pa_expl_bulletin ";
		echo traite_rqt($rqt,"DROP INDEX i_pa_expl_bulletin ") ;
		$rqt = "ALTER TABLE pret_archive ADD INDEX i_pa_expl_bulletin (arc_expl_bulletin) ";
		echo traite_rqt($rqt,"ADD INDEX i_pa_expl_bulletin ") ;

		$rqt = "ALTER TABLE pret_archive DROP INDEX i_pa_arc_fin ";
		echo traite_rqt($rqt,"DROP INDEX i_pa_arc_fin ") ;
		$rqt = "ALTER TABLE pret_archive ADD INDEX i_pa_arc_fin (arc_fin) ";
		echo traite_rqt($rqt,"ADD INDEX i_pa_arc_fin ") ;

		$rqt = "ALTER TABLE pret_archive DROP INDEX i_pa_arc_expl_id ";
		echo traite_rqt($rqt,"DROP INDEX i_pa_expl_id ") ;
		$rqt = "ALTER TABLE pret_archive ADD INDEX i_pa_expl_id (arc_expl_id) ";
		echo traite_rqt($rqt,"ADD INDEX i_pa_expl_id ") ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.57");
//		break;

//	case "v4.57": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='impression_tri' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param, comment_param, section_param, gestion) VALUES ('pdflettreretard', 'impression_tri', 'empr_cp,empr_ville,empr_nom,empr_prenom', 'Tri pour l\'impression des lettres de relances ? Les champs sont ceux de la table empr séparés par des virgules. Exemple: empr_nom, empr_prenom', '', '0')";
			echo traite_rqt($rqt,"insert pdflettreretard_impression_tri=cp,ville,nom,prenom into parametres");
			}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_date_retour_adhesion_depassee' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param, comment_param, section_param, gestion) VALUES ('pmb', 'pret_date_retour_adhesion_depassee', '0', 'La date de retour peut-elle dépasser la date de fin d\'adhésion ? \n 0: Non: la date de retour sera calculée pour ne pas dépasser la date de fin d\'adhésion. \n 1: Oui, la date de retour du prêt sera indépendante de la date de fin d\'adhésion.', '', '0')";
			echo traite_rqt($rqt,"insert pmb_pret_date_retour_adhesion_depassee=0 into parametres");
			}
		$rqt = "ALTER TABLE collections ADD collection_web VARCHAR( 255 ) NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE collections ADD collection_web VARCHAR(255) ") ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.58");
//		break;

//	case "v4.58": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE notices_custom ADD search INT(1) unsigned NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD search ") ;
		$rqt = "ALTER TABLE empr_custom ADD search INT(1) unsigned NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD search ") ;
		$rqt = "ALTER TABLE expl_custom ADD search INT(1) unsigned NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD search ") ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.59");
//		break;

//	case "v4.59": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE abts_abts_modeles ADD num_statut_general SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE abts_abts_modeles ADD num_statut_general") ;
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.60");
//		break;

//	case "v4.60": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='extended_search_auto' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'extended_search_auto', '1', 'En recherche multicritères, la sélection d\'un champ ajoute celui-ci automatiquement sans avoir besoin de cliquer sur le bouton Ajouter ? \n 0: Non \n 1: Oui', 'c_recherche', 0) ";
			echo traite_rqt($rqt,"insert opac_extended_search_auto=1 into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='extended_search_auto' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
					VALUES (0, 'pmb', 'extended_search_auto', '1', 'En recherche multicritères, la sélection d\'un champ ajoute celui-ci automatiquement sans avoir besoin de cliquer sur le bouton Ajouter ? \n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"insert pmb_extended_search_auto=1 into parametres") ;
		}
		
		$rqt = "ALTER TABLE tris ADD tri_reference VARCHAR( 40 ) NOT NULL DEFAULT 'notices';";
		echo traite_rqt($rqt,"ALTER TABLE tris ADD tri_reference") ;
		$rqt = "ALTER TABLE tris DROP tri_par_texte;";
		echo traite_rqt($rqt,"ALTER TABLE tris DROP tri_par_texte") ;
		
		$rqt = "ALTER TABLE responsability ADD responsability_ordre smallint(2) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE responsability ADD responsability_ordre") ;

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='categories_affichage_ordre'"))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
					VALUES (0, 'thesaurus', 'categories_affichage_ordre', '0', 'Paramétrage de l\'ordre d\'affichage des catégories d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1','categories') ";
			echo traite_rqt($rqt,"insert thesaurus_categories_affichage_ordre=0 into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='categories_affichage_ordre'"))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
					VALUES (0, 'opac', 'categories_affichage_ordre', '0', 'Paramétrage de l\'ordre d\'affichage des catégories d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1', 'i_categories') ";
			echo traite_rqt($rqt,"insert opac_categories_affichage_ordre=0 into parametres") ;
		}

		$rqt = "ALTER TABLE notices_categories ADD ordre_categorie smallint(2) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE notices_categories ADD ordre_categorie") ;

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='rfid_driver' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('pmb','rfid_driver', '', 'Driver du pilote RFID : le nom du répertoire contenant les javascripts propres au matériel en place.', '', '0')" ;
			echo traite_rqt($rqt,"insert pmb_rfid_driver='' into parametres");
			}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.61");
//		break;

//	case "v4.61": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE notices CHANGE year year VARCHAR( 50 )";
		echo traite_rqt($rqt,"ALTER TABLE notices CHANGE year VARCHAR( 50 )") ;

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_pmbws_client_url'"))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
					VALUES (0, 'pmb', 'scan_pmbws_client_url', '', 'URL de l\'interface de numérisation (client du webservice)','') ";
			echo traite_rqt($rqt,"insert pmb_scan_pmbws_client_url into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_pmbws_url'"))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
					VALUES (0, 'pmb', 'scan_pmbws_url', '', 'URL du webservice de pilotage du scanner','') ";
			echo traite_rqt($rqt,"insert pmb_scan_pmbws_url into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='biblio_main_header'"))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
					VALUES (0, 'opac', 'biblio_main_header', '', 'Texte pouvant apparaitre dans le bloc principal, au dessus de tous les autres, nécessaire pour certaines mises en page particulières.','b_aff_general') ";
			echo traite_rqt($rqt,"insert opac_biblio_main_header into parametres") ;
		}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.62");
//		break;

//	case "v4.62": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='sugg_localises'"))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES ( 'opac', 'sugg_localises', '0', 'Activer la localisation des suggestions des lecteurs ? \n 0: Pas de localisation possible.\n 1: Localisation au choix du lecteur.\n 2: Localisation restreinte à la localisation du lecteur.', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_sugg_localises into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='sugg_localises'"))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES ( 'acquisition', 'sugg_localises', '0', 'Activer la localisation des suggestions ? \n 0: Pas de localisation possible. \n 1: Localisation activée.', '', '0')";
			echo traite_rqt($rqt,"insert acquisition_sugg_localises into parametres") ;
		}
		
		$rqt = "ALTER TABLE suggestions ADD sugg_location SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE suggestions ADD sugg_location") ;

		// suppression des modeles    
		$rqt = "DELETE FROM abts_modeles WHERE num_notice not in (select notice_id from notices where niveau_biblio='s')";
		pmb_mysql_query($rqt, $dbh);    

		// suppression des abonnements    
		$rqt = "DELETE FROM abts_abts WHERE num_notice not in (select notice_id from notices where niveau_biblio='s')";
		pmb_mysql_query($rqt, $dbh);    

	    // vide la grille d'abonnement
    	$rqt = "DELETE FROM abts_grille_abt WHERE num_abt not in (select abt_id from abts_abts)";
    	pmb_mysql_query($rqt, $dbh);        
		
	    // elimine les liens entre modele et abonnements
	    $rqt = "DELETE FROM abts_abts_modeles WHERE modele_id not in (select modele_id from abts_modeles)";
	    pmb_mysql_query($rqt, $dbh);                
    	
	    // vide la grille de modele
	    $rqt = "DELETE FROM abts_grille_modele WHERE num_modele not in (select modele_id from abts_modeles) ";
	    pmb_mysql_query($rqt, $dbh);        
	    
		//pour jointures avec la table acte
		$rqt = "ALTER TABLE lignes_actes DROP INDEX num_acte ";
		echo traite_rqt($rqt,"DROP INDEX num_acte ") ;
		$rqt = "ALTER TABLE lignes_actes ADD INDEX num_acte (num_acte) ";
		echo traite_rqt($rqt,"ADD INDEX num_acte  ") ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.63");
//		break;

//	case "v4.63": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE notices CHANGE nocoll nocoll VARCHAR(255),
			CHANGE npages npages VARCHAR(255), CHANGE ill ill VARCHAR(255),
			CHANGE size size VARCHAR(255), CHANGE accomp accomp VARCHAR(255)";
		echo traite_rqt($rqt,"ALTER TABLE notices CHANGE nocoll and coll size") ;	

		$rqt = "ALTER TABLE empr drop index i_empr_categ " ;
		echo traite_rqt($rqt,"drop index i_empr_categ");
		$rqt = "ALTER TABLE empr ADD INDEX i_empr_categ (empr_categ) " ;
		echo traite_rqt($rqt,"create index i_empr_categ");
		
		$rqt = "ALTER TABLE empr drop index i_empr_codestat " ;
		echo traite_rqt($rqt,"drop index i_empr_codestat");
		$rqt = "ALTER TABLE empr ADD INDEX i_empr_codestat (empr_codestat) " ;
		echo traite_rqt($rqt,"create index i_empr_codestat");
		
		$rqt = "ALTER TABLE empr drop index i_empr_location " ;
		echo traite_rqt($rqt,"drop index i_empr_location");
		$rqt = "ALTER TABLE empr ADD INDEX i_empr_location (empr_location) " ;
		echo traite_rqt($rqt,"create index i_empr_location");
		
		$rqt = "ALTER TABLE empr drop index i_empr_statut " ;
		echo traite_rqt($rqt,"drop index i_empr_statut");
		$rqt = "ALTER TABLE empr ADD INDEX i_empr_statut (empr_statut) " ;
		echo traite_rqt($rqt,"create index i_empr_statut");
		
		$rqt = "ALTER TABLE empr drop index i_empr_typabt " ;
		echo traite_rqt($rqt,"drop index i_empr_typabt");
		$rqt = "ALTER TABLE empr ADD INDEX i_empr_typabt (type_abt) " ;
		echo traite_rqt($rqt,"create index i_empr_typabt");
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'opac' and sstype_param='categories_nav_max_display' "))==0){
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param ,comment_param ,section_param ,gestion)
					VALUES ('opac', 'categories_nav_max_display', '200', 'Limiter l\'affichage des catégories en navigation dans les sous-catégories. 0: Pas de limitation. >0: Nombre max de catégories à afficher', 'i_categories','0') ";
			echo traite_rqt($rqt,"INSERT opac_categories_nav_max_display INTO parametres") ;
		}

 		$rqt = "ALTER TABLE exemplaires drop index i_expl_location " ;
		echo traite_rqt($rqt,"drop index i_expl_location");
		$rqt = "ALTER TABLE exemplaires ADD INDEX i_expl_location (expl_location) " ;
		echo traite_rqt($rqt,"create index i_expl_location");
		
		$rqt = "ALTER TABLE exemplaires drop index i_expl_section " ;
		echo traite_rqt($rqt,"drop index i_expl_section");
		$rqt = "ALTER TABLE exemplaires ADD INDEX i_expl_section (expl_section) " ;
		echo traite_rqt($rqt,"create index i_expl_section");
		
		$rqt = "ALTER TABLE exemplaires drop index i_expl_statut " ;
		echo traite_rqt($rqt,"drop index i_expl_statut");
		$rqt = "ALTER TABLE exemplaires ADD INDEX i_expl_statut (expl_statut) " ;
		echo traite_rqt($rqt,"create index i_expl_statut");
		
		$rqt = "ALTER TABLE exemplaires drop index i_expl_lastempr " ;
		echo traite_rqt($rqt,"drop index i_expl_lastempr");
		$rqt = "ALTER TABLE exemplaires ADD INDEX i_expl_lastempr (expl_lastempr) " ;
		echo traite_rqt($rqt,"create index i_expl_lastempr");

		$rqt = "ALTER TABLE exemplaires drop index i_pret_idempr " ;
		echo traite_rqt($rqt,"drop index i_pret_idempr");
		$rqt = "ALTER TABLE pret ADD INDEX i_pret_idempr (pret_idempr) " ;
		echo traite_rqt($rqt,"create index i_pret_idempr");
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='pret_aff_limitation' "))==0){
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param,comment_param ,section_param ,gestion) 
				VALUES ( 'pmb', 'pret_aff_limitation', '0', 'Activer la limitation de l\'affichage de la liste des prêts dans la fiche lecteur ? \n 0: Inactif. \n 1: Limitation activée', '','0')";
			echo traite_rqt($rqt,"INSERT pmb_pret_aff_limitation INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='pret_aff_nombre' "))==0){
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param,comment_param ,section_param ,gestion)
				VALUES ( 'pmb', 'pret_aff_nombre', '10', 'Nombre de prêts à afficher si le paramètre pret_aff_limitation est actif. \n 0: tout voir, illimité. \n ## Nombre de prêts à afficher sur la première page', '','0')";
			echo traite_rqt($rqt,"INSERT pmb_pret_aff_nombre INTO parametres") ;
		}

		$rqt = "ALTER TABLE notices CHANGE nocoll nocoll VARCHAR(255),
			CHANGE npages npages VARCHAR(255), CHANGE ill ill VARCHAR(255),
			CHANGE size size VARCHAR(255), CHANGE accomp accomp VARCHAR(255)";
		echo traite_rqt($rqt,"ALTER TABLE notices CHANGE nocoll and coll size") ;	

		$rqt = "ALTER TABLE comptes drop index i_cpt_proprio_id " ;
		echo traite_rqt($rqt,"drop index i_cpt_proprio_id");
		$rqt = "ALTER TABLE comptes ADD INDEX i_cpt_proprio_id (proprio_id) " ;
		echo traite_rqt($rqt,"create index i_cpt_proprio_id");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.64");
//		break;

//	case "v4.64": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_ticket_url'"))==0){
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param,comment_param ,section_param ,gestion) VALUES ( 'pmb', 'printer_ticket_url', '', 'Permet d\'utiliser une imprimante de ticket, connectée en local sur le poste de prêt client. Vide : pas d\'imprimante. Url (http://localhost/printer/bixolon_srp350.php ) : imprimante active.', '','0')";
			echo traite_rqt($rqt,"insert pmb_printer_ticket_url into parametres") ;
		}

	    //pour les transferts
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='gestion_transferts' "))==1){
			$rqt = "DELETE FROM parametres WHERE type_param='transferts' AND sstype_param='gestion_transferts'";
			echo traite_rqt($rqt,"DELETE gestion_transferts INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres where type_param= 'transferts' and sstype_param='transfert_statut' "))==1){
			$rqt = "DELETE FROM parametres WHERE type_param='transferts' AND sstype_param='transfert_statut'";
			echo traite_rqt($rqt,"DELETE transfert_statut INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='transferts_actif' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
					VALUES (0, 'pmb', 'transferts_actif', '0', 'Active le systeme de transferts d\'exemplaires entre sites\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT transferts_actif INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='statut_validation' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'statut_validation', '0', '1', 'id du statut dans lequel seront placés les documents dont le transfert est validé') ";
			echo traite_rqt($rqt,"INSERT statut_validation INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='statut_transferts' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'statut_transferts', '0', '1', 'id du statut dans lequel seront placés les documents en cours de transit') ";
			echo traite_rqt($rqt,"INSERT satut_transferts INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='validation_actif' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'validation_actif', '1', '1', 'Active la validation des transferts\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT validation_actif INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='nb_jours_pret_defaut' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'nb_jours_pret_defaut', '30', '1', 'Nombre de jours de pret par defaut') ";
			echo traite_rqt($rqt,"INSERT nb_jours_pret_defaut INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='nb_jours_alerte' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'nb_jours_alerte', '7', '1', 'Nombre de jours avant la fin du pret ou l\'alerte s\'affiche') ";
			echo traite_rqt($rqt,"INSERT nb_jours_alerte INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='transfert_transfere_actif' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'transfert_transfere_actif', '0', '1', 'Autorise le transfert d\'exemplaire deja transferer') ";
			echo traite_rqt($rqt,"INSERT transfert_transfere_actif INTO parametres") ;
		}


		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='tableau_nb_lignes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'tableau_nb_lignes', '10', '1', 'Nombre de transferts affichés dans les tableaux') ";
			echo traite_rqt($rqt,"INSERT tableau_nb_lignes into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='envoi_lot' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'envoi_lot', '0', '1', 'traitement par lot possible en envoi') ";
			echo traite_rqt($rqt,"INSERT envoi_lot into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='reception_lot' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'reception_lot', '0', '1', 'traitement par lot possible en reception') ";
			echo traite_rqt($rqt,"INSERT reception_lot into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_lot' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_lot', '0', '1', 'traitement par lot possible en retour') ";
			echo traite_rqt($rqt,"INSERT retour_lot into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_origine' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_origine', '0', '1', 'Force le retour de l\'exemplaire dans son lieu d\'origine\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT retour_origine INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_origine_force' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_origine_force', '1', '1', 'Permet de forcer le retour de l\'exemplaire\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT retour_origine_force INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_action_defaut' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_action_defaut', '1', '1', 'Action par defaut lors du retour d\'un emprunt\n 0: change localisation \n 1: genere transfert') ";
			echo traite_rqt($rqt,"INSERT retour_action_defaut INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_action_autorise_autre' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_action_autorise_autre', '1', '1', 'Autorise une autre action lors du retour de l\'exemplaire\n 0: Non\n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT retour_action_autorise_autre INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_change_localisation' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_change_localisation', '1', '1', 'Sauvegarde de la localisation lors du changement\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT retour_change_localisation INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_etat_transfert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_etat_transfert', '1', '1', 'Etat du transfert lors de sa generation auto\n 0: creer \n 1: envoyer') ";
			echo traite_rqt($rqt,"INSERT retour_etat_transfert INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_motif_transfert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'retour_motif_transfert', 'Transfert suite au retour de l\'exemplaire sur notre site', '1', 'Motif du transfert lors de sa generation auto') ";
			echo traite_rqt($rqt,"INSERT retour_motif_transfert INTO parametres") ;
		}


		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='choix_lieu_opac' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'choix_lieu_opac', '0', '1', '0 pour pas de choix et obligatoirement dans la localisation ou est enregistré l\'utilisateur, 1 pour n\'importe quelle localisation au choix, 2 pour un lieu fixe précisé, 3 pour le lieu de l\'exemplaire') ";
			echo traite_rqt($rqt,"INSERT choix_lieu_opac INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='site_fixe' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'site_fixe', '1', '1', 'id du site pour le retrait des livres si choix_lieu_opac=2') ";
			echo traite_rqt($rqt,"INSERT retour_origine INTO parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='resa_motif_transfert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'resa_motif_transfert', 'Transfert suite à une réservation', '1', 'Motif du transfert lors de sa generation auto pour une réservation') ";
			echo traite_rqt($rqt,"INSERT resa_motif_transfert INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='resa_etat_transfert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'resa_etat_transfert', '1', '1', 'Etat du transfert lors de sa generation auto\n 0: creer \n 1: envoyer') ";
			echo traite_rqt($rqt,"INSERT resa_etat_transfert INTO parametres") ;
		}

		$rqt = "DROP TABLE IF EXISTS transferts_demandes";
		echo traite_rqt($rqt,"DROP TABLE transferts_demandes") ;

		$rqt = "ALTER TABLE docs_location ADD transfert_ordre smallint(2) UNSIGNED NOT NULL DEFAULT 9999";
		echo traite_rqt($rqt,"ALTER TABLE docs_location ADD transfert_ordre") ;

		$rqt = "ALTER TABLE docs_location ADD transfert_statut_defaut smallint(5) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE docs_location ADD transfert_statut_defaut") ;

		$rqt = "ALTER TABLE exemplaires ADD transfert_location_origine SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD transfert_location_origine") ;

		$rqt = "ALTER TABLE exemplaires ADD transfert_statut_origine SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD transfert_statut_origine") ;

		$rqt = "ALTER TABLE resa ADD resa_loc_retrait SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE resa ADD resa_loc_retrait") ;

		$rqt = "ALTER TABLE docs_statut ADD transfert_flag TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT 1";
		echo traite_rqt($rqt,"ALTER TABLE docs_statut ADD transfert_flag") ;
		
		$rqt = "CREATE TABLE transferts (
			id_transfert INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			num_notice INT UNSIGNED NOT NULL default 0,
			num_bulletin INT UNSIGNED NOT NULL default 0,
			date_creation DATE NOT NULL ,
			type_transfert TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
			etat_transfert TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
			origine TINYINT UNSIGNED NOT NULL default 0,
			origine_comp VARCHAR(255) NOT NULL default '',
			source SMALLINT(5) UNSIGNED NULL,
			destinations VARCHAR(255) NULL,
			date_retour DATE,
			motif VARCHAR(255) NOT NULL default '',
			KEY etat_transfert (etat_transfert)
			)"; 
		echo traite_rqt($rqt,"CREATE TABLE 'transferts'") ;

 		$rqt = "CREATE TABLE transferts_demande (
			id_transfert_demande INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			num_transfert INT UNSIGNED NOT NULL default 0,
			date_creation DATE NOT NULL ,
			sens_transfert TINYINT UNSIGNED NOT NULL DEFAULT 0,
			num_location_source SMALLINT(5) UNSIGNED NOT NULL default 0,
			num_location_dest SMALLINT(5) UNSIGNED NOT NULL default 0,
			num_expl INT UNSIGNED NOT NULL default 0,
			etat_demande TINYINT UNSIGNED NOT NULL DEFAULT 0,
			date_visualisee DATE NULL,
			date_envoyee DATE NULL,
			date_reception DATE NULL,
			motif_refus VARCHAR(255) NOT NULL default '',
			KEY num_transfert (num_transfert),
			KEY num_location_source (num_location_source),
			KEY num_location_dest (num_location_dest),
			KEY num_expl (num_expl)
			)"; 
		echo traite_rqt($rqt,"CREATE TABLE 'transferts_demande'") ;
	       
		$rqt = "ALTER TABLE transferts_demande ADD statut_origine INT(10) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transferts_demande ADD statut_origine") ;
	
		$rqt = "ALTER TABLE transferts_demande ADD section_origine INT(10) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transferts_demande ADD section_origine") ;
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.65");
//		break;

//	case "v4.65": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		@set_time_limit(0);
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='recherche_ajax_mode'"))==0){
			$rqt = "INSERT INTO parametres (id_param ,type_param ,sstype_param ,valeur_param,comment_param ,section_param ,gestion)
					VALUES ( 0 , 'pmb', 'recherche_ajax_mode', '1', 'Affichage accéléré des résultats de recherche: \"réduit\" uniquement, la suite est chargée lors du click sur le \"+\". \n 0: Inactif \n 1: Actif', '', '0')";
			echo traite_rqt($rqt,"insert pmb_recherche_ajax_mode into parametres") ;
		}

		//Parametres utilisateur pour acquisitions
		$rqt = "ALTER TABLE users ADD deflt3bibli int(5) unsigned not null default '0' ";		
		echo traite_rqt($rqt,"ALTER TABLE users ADD default bibli");
		$rqt = "ALTER TABLE users ADD deflt3exercice int(8) unsigned not null default '0' ";		
		echo traite_rqt($rqt,"ALTER TABLE users ADD default exercice");
		$rqt = "ALTER TABLE users ADD deflt3rubrique int(8) unsigned not null default '0' ";		
		echo traite_rqt($rqt,"ALTER TABLE users ADD default rubrique");
		$rqt = "ALTER TABLE users ADD deflt3dev_statut int(3) not null default '-1' ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default dev state");
		$rqt = "ALTER TABLE users ADD deflt3cde_statut int(3) not null default '-1' ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default cde state");
		$rqt = "ALTER TABLE users ADD deflt3liv_statut int(3) not null default '-1' ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default liv state");
		$rqt = "ALTER TABLE users ADD deflt3fac_statut int(3) not null default '-1' ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default fac state");
		$rqt = "ALTER TABLE users ADD deflt3sug_statut int(3) not null default '-1' ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default sug state");

		//Modification de la table external_count
		$sql_alter_external_count = "ALTER TABLE external_count ADD source_id INT NOT NULL ";
		echo traite_rqt($sql_alter_external_count,"Modification de la table external_count 1");
		$sql_alter_external_count = "UPDATE external_count, entrepots SET external_count.source_id = entrepots.source_id WHERE entrepots.recid = external_count.rid ";
		echo traite_rqt($sql_alter_external_count,"Modification de la table external_count 2");

		//Récupération de la liste des sources
		$sql_liste_sources = "SELECT source_id FROM connectors_sources ";
		$res_liste_sources = pmb_mysql_query($sql_liste_sources, $dbh) or die(pmb_mysql_error());

		//Pour chaque source
		while ($row=pmb_mysql_fetch_row($res_liste_sources)) {
			//On créer la table
			$sql_create_table = "CREATE TABLE entrepot_source_".$row[0]." (
							  connector_id varchar(20) NOT NULL default '',
							  source_id int(11) unsigned NOT NULL default 0,
							  ref varchar(220) NOT NULL default '',
							  date_import datetime NOT NULL default '0000-00-00 00:00:00',
							  ufield char(3) NOT NULL default '',
							  usubfield char(1) NOT NULL default '',
							  field_order int(10) unsigned NOT NULL default 0,
							  subfield_order int(10) unsigned NOT NULL default 0,
							  value text NOT NULL,
							  i_value text NOT NULL,
							  recid bigint(20) unsigned NOT NULL default 0,
							  search_id varchar(32) NOT NULL default '',
							  PRIMARY KEY  (connector_id,source_id,ref,ufield,usubfield,field_order,subfield_order,search_id),
							  KEY usubfield (usubfield),
							  KEY ufield_2 (ufield,usubfield),
							  KEY recid_2 (recid,ufield,usubfield),
							  KEY source_id (source_id),
							  KEY i_recid_source_id (recid,source_id)
							) ";
			echo traite_rqt($sql_create_table, "CREATE TABLE entrepot_source_".$row[0]);
			
			//On copie les éléments de la source dans sa nouvelle table
			$sql_transfer = "INSERT INTO entrepot_source_".$row[0]." (SELECT * FROM entrepots WHERE source_id = ".$row[0].")";
			echo traite_rqt($sql_transfer, "INSERT INTO entrepot_source_".$row[0]);
		}
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.66");
//		break;

//	case "v4.66": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_title_display_format'"))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param ,section_param ,gestion)
			VALUES ('pmb', 'expl_title_display_format', 'expl_location,expl_section,expl_cote,expl_cb','Format d\'affichage du titre de l\'exemplaire en recherche multi-critères d\'exemplaires. Les libellés des champs correspondent aux champs de la table exemplaires, ou aux id de champs personnalisés. Séparés par une virgule. Les champs disposant d\'un libellé seront remplacés par le libellé correspondant. Exemple: expl_location,expl_section,expl_cote,expl_cb', '', '0')";
			echo traite_rqt($rqt,"insert pmb_expl_title_display_format into parametres") ;
		}

		$rqt = "DROP TABLE entrepots"; //La requête violente
		echo traite_rqt($rqt, "DROP TABLE entrepots");

		$rqt = "ALTER TABLE exemplaires ADD expl_comment VARCHAR(255) NOT NULL default ''"; 
		echo traite_rqt($rqt, "ALTER TABLE exemplaires ADD expl_comment");

		//Ajout d'une date d'echeance dans les actes
		$rqt = "ALTER TABLE actes ADD date_ech DATE NOT NULL DEFAULT '0000-00-00' ";
		echo traite_rqt($rqt,"ALTER TABLE actes ADD date_ech ");

		//Modification du parametre gestion tva
		$rqt = "UPDATE parametres SET comment_param = 'Gestion de la TVA.\n 0 : Non.\n 1 : Oui, avec saisie des prix HT.\n 2 : Oui, avec saisie des prix TTC.' WHERE type_param='acquisition' and sstype_param='gestion_tva' ";
		echo traite_rqt($rqt,"UPDATE parametres set gestion_tva = 0,1,2");

		//Ajout d'un ordre dans les lignes d'acte
		$rqt = "ALTER TABLE lignes_actes ADD ligne_ordre SMALLINT(2) UNSIGNED NOT NULL DEFAULT '0' ";
		echo traite_rqt($rqt,"ALTER TABLE lignes_actes ADD ligne_ordre");

		$rqt = "ALTER TABLE rss_content ADD rss_content_parse LONGBLOB NOT NULL default '' AFTER rss_content ";
		echo traite_rqt($rqt,"ALTER TABLE rss_content ADD rss_content_parse");

		$rqt = "ALTER TABLE notices drop index notice_eformat" ;
		echo traite_rqt($rqt,"drop index notice_eformat");
		$rqt = "ALTER TABLE notices ADD INDEX notice_eformat (eformat)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX notice_eformat");
		
		$rqt = "ALTER TABLE users ADD environnement MEDIUMBLOB NOT NULL default ''" ;
		echo traite_rqt($rqt,"ALTER TABLE users ADD environnement ");
		
		$rqt = "ALTER TABLE notices ADD thumbnail_url MEDIUMBLOB NOT NULL default '' " ;
		echo traite_rqt($rqt,"ALTER TABLE notices ADD thumbnail_url ");

		$rqt = "CREATE TABLE connectors_categ (
			  connectors_categ_id smallint(5) NOT NULL auto_increment,
			  connectors_categ_name varchar(64) NOT NULL default '',
			  opac_expanded smallint(6) NOT NULL default 0,
			  PRIMARY KEY  (connectors_categ_id)
			  )" ;
		echo traite_rqt($rqt,"CREATE TABLE connectors_categ ");

		$rqt = "CREATE TABLE connectors_categ_sources (
			  num_categ smallint(6) NOT NULL default 0,
			  num_source smallint(6) NOT NULL default 0,
			  PRIMARY KEY  (num_categ,num_source),
			  index i_num_source (num_source)
			  )" ;
		echo traite_rqt($rqt,"CREATE TABLE connectors_categ_sources");

		$rqt = "ALTER TABLE connectors_sources CHANGE parameters parameters MEDIUMTEXT NOT NULL default '' " ;
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources CHANGE parameters MEDIUMTEXT ");
		
		$rqt = "ALTER TABLE empr CHANGE empr_password empr_password VARCHAR( 255 ) NOT NULL  default '' " ;
		echo traite_rqt($rqt,"ALTER TABLE empr CHANGE empr_password VARCHAR( 255 )");
		
		$rqt = "ALTER TABLE users ADD value_deflt_relation VARCHAR( 20 ) NOT NULL DEFAULT 'a' AFTER value_deflt_fonction " ;
		echo traite_rqt($rqt,"ALTER TABLE users ADD value_deflt_relation");

		$rqt = "CREATE TABLE entrepots_localisations (
			   loc_id int(11) NOT NULL auto_increment,
			   loc_code varchar(255) NOT NULL default '',
			   loc_libelle varchar(255) NOT NULL default '',
			   loc_visible tinyint(1) UNSIGNED NOT NULL default 1,
			   PRIMARY KEY  (loc_id),
			   UNIQUE KEY loc_code (loc_code)
				) " ;
		echo traite_rqt($rqt,"CREATE TABLE entrepots_localisations ");

		$rqt = "update parametres set comment_param=concat(comment_param,' \n 2: n\'afficher l\'onglet empr que lorsque l\'utilisateur est authentifié (et dans ce cas le clic sur l\'onglet mène vers empr.php)') where type_param='opac' and sstype_param='show_onglet_empr' and comment_param not like '%ne vers empr.php)%' " ;
		echo traite_rqt($rqt,"update parametres set comment_param=... where type_param='opac' and sstype_param='show_onglet_empr' ") ;

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_code_info'"))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param ,section_param ,gestion)
			VALUES ('opac', 'empr_code_info', '','Code HTML affiché au dessus des boutons dans la fiche emprunteur.', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_empr_code_info into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='term_search_height_bottom'"))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param ,section_param ,gestion)
			VALUES ('opac', 'term_search_height_bottom', '120','Hauteur de la partie supérieure de la frame de recherche par termes (en px)', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_term_search_height_bottom into parametres") ;
		}

		$rqt = "ALTER TABLE docs_location DROP logosmall" ;
		echo traite_rqt($rqt,"ALTER TABLE docs_location DROP logosmall") ;
		
		$rqt = "ALTER TABLE empr_caddie_content DROP INDEX empr_caddie_id" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_caddie_content DROP INDEX empr_caddie_id") ;
		$rqt = "ALTER TABLE empr_caddie_content ADD INDEX object_id (object_id)" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_caddie_content ADD INDEX object_id (object_id)") ;
		
		$rqt = "ALTER TABLE notices change lien lien text not null default '' " ;
		echo traite_rqt($rqt,"ALTER TABLE notices change lien TEXT") ;
		
		$rqt = "CREATE TABLE infopages (
				id_infopage INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
				content_infopage BLOB NOT NULL default '',
				title_infopage VARCHAR( 255 ) NOT NULL default '' ,
				valid_infopage TINYINT( 1 ) NOT NULL DEFAULT '1',
				PRIMARY KEY ( id_infopage )
				)  " ;
		echo traite_rqt($rqt,"CREATE TABLE infopages ") ;
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='rfid_library_code'"))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion ) 
			VALUES ( 'pmb','rfid_library_code', '' , 'Code numérique d\'identification de la bibliothèque propriétaire des exemplaires (10 caractères)', '', '0')";
			echo traite_rqt($rqt,"insert pmb_rfid_library_code into parametres") ;
		}

		// $opac_show_infopages_id
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_infopages_id' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'show_infopages_id', '', 'Id des infopages à afficher sous la recherche simple, séparées par des virgules.', 'f_modules', 0) ";
			echo traite_rqt($rqt,"insert opac_show_infopages_id=0 into parametres") ;
		}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.67");
//		break;

//	case "v4.67": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		$rqt = "ALTER TABLE rss_flux ADD export_court_flux TINYINT(1) UNSIGNED NOT NULL DEFAULT 0" ;
		echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD export_court_flux") ;

		$rqt = "ALTER TABLE noeuds ADD path TEXT NOT NULL" ;
		echo traite_rqt($rqt,"ALTER TABLE noeuds ADD path") ;

		$rqt = "ALTER TABLE noeuds DROP INDEX key_path" ;
		echo traite_rqt($rqt,"ALTER TABLE noeuds DROP INDEX key_path") ;
		$rqt = "ALTER TABLE noeuds ADD INDEX key_path ( path ( 1000 ) )" ;
		echo traite_rqt($rqt,"ALTER TABLE noeuds ADD INDEX key_path") ;

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_montant' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'thesaurus','auto_postage_montant', '0', 'Activer la recherche des notices des catégories mères ? \n  0 non, \n 1 oui', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_auto_postage_montant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_descendant' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'thesaurus','auto_postage_descendant', '0', 'Activer la recherche des notices des catégories filles. \n 0 non, \n 1 oui', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_auto_postage_descendant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_nb_descendant' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'thesaurus','auto_postage_nb_descendant', '0', 'Nombre de niveaux de recherche de notices dans les catégories filles. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_auto_postage_nb_descendant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_nb_montant' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'thesaurus','auto_postage_nb_montant', '0', 'Nombre de niveaux de recherche de notices dans les catégories mères. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_auto_postage_nb_montant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_etendre_recherche' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'thesaurus', 'auto_postage_etendre_recherche', '0', 'Proposer la possibilité d\'étendre la recherche dans les catégories mères ou filles. \n 0: non, \n 1: Exclusivement dans les catégories filles, \n 2: Etendre dans les catégories mères et filles, \n 3: Exclusivement dans les catégories mères. ', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_auto_postage_etendre_recherche") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='auto_postage_montant' "))==0) {
		$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'opac','auto_postage_montant', '0', 'Activer la recherche des notices des catégories mères. \n 0 non, \n 1 oui', 'i_categories', 0)" ;
		echo traite_rqt($rqt,"insert into parameters opac_auto_postage_montant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='auto_postage_descendant' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'opac', 'auto_postage_descendant', '0', 'Activer la recherche des notices des catégories filles. \n 0 non, \n 1 oui', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters opac_auto_postage_descendant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='auto_postage_nb_descendant' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion)
				VALUES ( 'opac', 'auto_postage_nb_descendant', '0', 'Nombre de niveaux de recherche de notices dans les catégories filles. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters opac_auto_postage_nb_descendant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='auto_postage_nb_montant' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES ( 'opac', 'auto_postage_nb_montant', '0', 'Nombre de niveaux de recherche de notices dans les catégories mères. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters opac_auto_postage_nb_montant") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='auto_postage_etendre_recherche' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion) 
				VALUES ( 'opac','auto_postage_etendre_recherche', '0', 'Proposer la possibilité d\'étendre la recherche dans les catégories mères ou filles. \n 0: non, \n 1: Exclusivement dans les catégories filles, \n 2: Etendre dans les catégories mères et filles, \n 3: Exclusivement dans les catégories mères. ', 'i_categories', 0)" ;
			echo traite_rqt($rqt,"insert into parameters opac_auto_postage_etendre_recherche") ;
		}
		$rqt = "ALTER TABLE users ADD param_allloc INT(1) UNSIGNED DEFAULT '0' NOT NULL ";
		echo traite_rqt($rqt, "add param_allloc in table users");
		
		//parametre general d'activation de la gestion droits acces 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'gestion_acces', 'active', '0', 'Module gestion des droits d\'accès activé ?\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert gestion_acces=0 into parameters");
		}

		//parametres activation gestion droits acces utilisateurs - notices 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='user_notice' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'gestion_acces', 'user_notice', '0', 'Gestion des droits d\'accès des utilisateurs aux notices \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert gestion_acces_user_notice=0 into parameters");
		}

		//table profils
		$rqt = "CREATE TABLE acces_profiles (
				prf_id INT(2) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				prf_type INT(1) UNSIGNED NOT NULL DEFAULT '1',
				prf_name VARCHAR(255) NOT NULL,
				prf_rule BLOB NOT NULL,
				prf_hrule TEXT NOT NULL,
				prf_used  INT(2) UNSIGNED NOT NULL DEFAULT '0', 
				dom_num INT(2) UNSIGNED NOT NULL DEFAULT '0',
				INDEX prf_type (prf_type), 
				INDEX prf_name (prf_name),
				INDEX dom_num (dom_num)
				)";
		echo traite_rqt($rqt, "CREATE TABLE acces_profiles");

		//table droits
		$rqt = "CREATE TABLE acces_rights (
 				dom_num int(2) unsigned NOT NULL default '0',
  				usr_prf_num int(2) unsigned NOT NULL default '0',
  				res_prf_num int(2) unsigned NOT NULL default '0',
  				dom_rights varbinary(1) NOT NULL,
  				PRIMARY KEY  (dom_num, usr_prf_num, res_prf_num),
				KEY dom_num (dom_num), 
  				KEY usr_prf_num (usr_prf_num),
  				KEY res_prf_num (res_prf_num)
				)";
		echo traite_rqt($rqt, "CREATE TABLE acces_rights");
	

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.68");
//		break;

//	case "v4.68": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE docs_location ADD num_infopage INT( 6 ) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt, "ALTER TABLE docs_location ADD num_infopage");

		$rqt = "ALTER TABLE users CHANGE explr_invisible explr_invisible varchar( 255 ) default '0' ";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_invisible default '0'  ");
		
		$rqt = "ALTER TABLE users CHANGE explr_visible_mod explr_visible_mod varchar( 255 ) default '0'";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_visible_mod default '0'");
		
		$rqt = "ALTER TABLE users CHANGE explr_visible_unmod explr_visible_unmod varchar( 255 ) default '0'";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_visible_unmod default '0'");
		
		$rqt = "UPDATE users set explr_invisible='0' where explr_invisible='' ";
		echo traite_rqt($rqt,"UPDATE users set explr_invisible='0' where explr_invisible='' ");
		
		$rqt = "UPDATE users set explr_visible_mod='0' where explr_visible_mod=''";
		echo traite_rqt($rqt,"UPDATE users set explr_visible_mod='0' where explr_visible_mod=''");
		
		$rqt = "UPDATE users set explr_visible_unmod='0' where explr_visible_unmod=''";
		echo traite_rqt($rqt,"UPDATE users set explr_visible_unmod='0' where explr_visible_unmod=''");
		
		$rqt = "ALTER TABLE sub_collections ADD subcollection_web TEXT NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE sub_collections ADD subcollection_web TEXT ");
				
		$rqt = "ALTER TABLE collections CHANGE collection_web collection_web TEXT NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE collections CHANGE collection_web TEXT ");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='abt_end_delay' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion ) 
				VALUES ( 'pmb', 'abt_end_delay', '30' , 'Délais d\'alerte d\'avertissement des abonnements arrivant à échéance (en jours)', '', '0')";
			echo traite_rqt($rqt, "insert pmb_abt_end_delay=30 into parameters");
		}
		

		$rqt = "ALTER TABLE pret_archive ADD arc_niveau_relance INT( 1 ) UNSIGNED DEFAULT 0 ";
		echo traite_rqt($rqt,"ALTER TABLE pret_archive ADD arc_niveau_relance");
		
		$rqt = "ALTER TABLE pret_archive ADD arc_date_relance DATE not NULL default '0000-00-00'";
		echo traite_rqt($rqt,"ALTER TABLE pret_archive ADD arc_date_relance DATE");
		
		$rqt = "ALTER TABLE pret_archive ADD arc_printed INT( 1 ) UNSIGNED DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE pret_archive ADD arc_printed INT");
		
		$rqt = "ALTER TABLE pret_archive ADD arc_cpt_prolongation INT( 1 ) UNSIGNED DEFAULT 0 ";
		echo traite_rqt($rqt,"ALTER TABLE pret_archive ADD arc_cpt_prolongation INT");
		
		$rqt = "ALTER TABLE authors ADD author_lieu VARCHAR(255) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE authors ADD author_lieu VARCHAR(255) ");

		$rqt = "ALTER TABLE authors ADD author_ville VARCHAR(255) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE authors ADD author_ville VARCHAR(255) ");

		$rqt = "ALTER TABLE authors ADD author_pays VARCHAR(255) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE authors ADD author_pays VARCHAR(255) ");

		$rqt = "ALTER TABLE authors ADD author_subdivision VARCHAR(255) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE authors ADD author_subdivision VARCHAR(255) ");

		$rqt = "ALTER TABLE authors ADD author_numero VARCHAR(50) NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE authors ADD author_numero VARCHAR(255) ");

		$rqt = "ALTER TABLE authors CHANGE author_type author_type ENUM( '70', '71', '72' ) NOT NULL DEFAULT '70' ";
		echo traite_rqt($rqt,"ALTER TABLE authors CHANGE author_type author_type ENUM( '70', '71', '72' ) ");
		
		//Table de stockage des groupes d'utilisateurs
		$rqt = "CREATE TABLE users_groups (
					grp_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
					grp_name VARCHAR(255) NOT NULL default '',
					PRIMARY KEY (grp_id),
					KEY i_users_groups_grp_name(grp_name))";
		echo traite_rqt($rqt, "CREATE TABLE users_groups");

		//Lien avec la table users
		$rqt = "ALTER TABLE users ADD grp_num INT UNSIGNED DEFAULT 0 ";
		echo traite_rqt($rqt, "ALTER TABLE users ADD grp_num");

		// export des champs persos de notices ?
		$rqt = "ALTER TABLE notices_custom ADD export INT( 1 ) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt, "ALTER TABLE notices_custom ADD export");

		// export des champs persos d'exemplaires' ?
		$rqt = "ALTER TABLE expl_custom ADD export INT( 1 ) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt, "ALTER TABLE expl_custom ADD export");

		// export des champs persos d'exemplaires' ?
		$rqt = "ALTER TABLE empr_custom ADD export INT( 1 ) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt, "ALTER TABLE empr_custom ADD export");

		// Ajout Autorités Titres uniformes
		$rqt = "CREATE TABLE titres_uniformes (
	        tu_id INT( 9 ) unsigned NOT NULL AUTO_INCREMENT,
	        tu_name VARCHAR( 255 ) DEFAULT '' NOT NULL,        
	        tu_tonalite VARCHAR( 255 ) DEFAULT '' NOT NULL ,
	        tu_comment TEXT NOT NULL ,
	        index_tu TEXT NOT NULL ,
	        PRIMARY KEY ( tu_id )
		)";
		echo traite_rqt($rqt, "CREATE TABLE titres_uniformes");	
		
		$rqt = "CREATE TABLE tu_distrib (
	        distrib_num_tu INT( 9 ) unsigned NOT NULL default 0,
	        distrib_name VARCHAR( 255 ) DEFAULT '' NOT NULL,
	        distrib_ordre smallint(5) unsigned NOT NULL default 0,
	        PRIMARY KEY (distrib_num_tu, distrib_ordre)	
		)";
		echo traite_rqt($rqt, "CREATE TABLE tu_distrib");	
		
		$rqt = "CREATE TABLE tu_ref (
	        ref_num_tu INT( 9 ) unsigned NOT NULL default 0,
	        ref_name VARCHAR( 255 ) DEFAULT '' NOT NULL,
	        ref_ordre smallint(5) unsigned NOT NULL default 0,
	        PRIMARY KEY (ref_num_tu, ref_ordre)	
		)";
		echo traite_rqt($rqt, "CREATE TABLE tu_ref");		
		
		$rqt = "CREATE TABLE tu_subdiv (
	        subdiv_num_tu INT( 9 ) unsigned NOT NULL default 0,
	        subdiv_name VARCHAR( 255 ) DEFAULT '' NOT NULL,
	        subdiv_ordre smallint(5) unsigned NOT NULL default 0,
	        PRIMARY KEY (subdiv_num_tu, subdiv_ordre)		
		)";
		echo traite_rqt($rqt, "CREATE TABLE tu_subdiv");		
		
		$rqt = "CREATE TABLE notices_titres_uniformes (
	        ntu_num_notice INT( 9 ) unsigned NOT NULL default 0,
	        ntu_num_tu INT( 9 ) unsigned NOT NULL default 0,
	        ntu_titre VARCHAR( 255 ) DEFAULT '' NOT NULL,
	        ntu_date VARCHAR( 255 ) DEFAULT '' NOT NULL,
	        ntu_sous_vedette VARCHAR( 255 ) DEFAULT '' NOT NULL ,
	        ntu_langue VARCHAR( 255 ) DEFAULT '' NOT NULL ,
	        ntu_version VARCHAR( 255 ) DEFAULT '' NOT NULL ,
	        ntu_mention VARCHAR( 255 ) DEFAULT '' NOT NULL ,
	        ntu_ordre smallint(5) unsigned NOT NULL default 0,
	        PRIMARY KEY (ntu_num_notice, ntu_num_tu)	
		)";
		echo traite_rqt($rqt, "CREATE TABLE notices_titres_uniformes");						
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='set_time_limit' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion ) 
				VALUES ( 'pmb', 'set_time_limit', '1200' , 'max_execution_time de certaines opérations (export d\'actions personnalisées, envoi DSI, export, etc.) \nAttention, peut être sans effet si l\'hébergement ne l\'autorise pas (free.fr par exemple)\n 0 : illimité (déconseillé) \n ###: ### secondes', '', '0')";
			echo traite_rqt($rqt, "insert pmb_set_time_limit=1200 into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_list_display_comments' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion ) 
				VALUES ( 'pmb', 'expl_list_display_comments', '0' , 'Afficher les commentaires des exemplaires en liste d\'exemplaires : \n 0 : non \n 1 : commentaire bloquant \n 2 : commentaire non bloquant \n 3 : les deux commentaires', '', '0')";
			echo traite_rqt($rqt, "insert pmb_expl_list_display_comments=0 into parameters");
		}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.69");
//		break;

//	case "v4.69": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt = "ALTER TABLE lenders CHANGE lender_libelle lender_libelle VARCHAR(255) NOT NULL default ''";
		echo traite_rqt($rqt, "ALTER TABLE lenders CHANGE lender_libelle VARCHAR(255)");		
		
		$rqt = "alter table audit drop index type_obj";
		echo traite_rqt($rqt, "ALTER TABLE audit DROP INDEX");
		$rqt = "alter table audit drop index object_id";
		echo traite_rqt($rqt, "ALTER TABLE audit DROP INDEX");
		$rqt = "alter table audit drop index user_id";
		echo traite_rqt($rqt, "ALTER TABLE audit DROP INDEX");
		$rqt = "alter table audit drop index type_modif";
		echo traite_rqt($rqt, "ALTER TABLE audit DROP INDEX");
		$rqt = "alter table audit add index type_obj (type_obj)";
		echo traite_rqt($rqt, "ALTER TABLE audit ADD INDEX");
		$rqt = "alter table audit add index object_id (object_id)";
		echo traite_rqt($rqt, "ALTER TABLE audit ADD INDEX");
		$rqt = "alter table audit add index user_id (user_id)";
		echo traite_rqt($rqt, "ALTER TABLE audit ADD INDEX");
		$rqt = "alter table audit add index type_modif (type_modif)";
		echo traite_rqt($rqt, "ALTER TABLE audit ADD INDEX");
		
		//Ajout du paramètre insert pmb_confirm_delete_from_caddie (voir mantis://0000588)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='confirm_delete_from_caddie' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES
					('pmb', 'confirm_delete_from_caddie', '0', 'Action à réaliser lors de la suppression d''une notice située dans un panier. \r\n0 : Interdire \r\n1 : Supprimer sans confirmation \r\n2 : Demander une confirmation de suppression ', '', 0)";
			echo traite_rqt($rqt, "insert pmb_confirm_delete_from_caddie=0 into parameters");
		}

		$rqt = "ALTER TABLE notices ADD date_parution DATE NOT NULL DEFAULT '0000-00-00'";
		echo traite_rqt($rqt, "ALTER TABLE notices ADD date_parution");
		$rqt = "alter table notices drop index i_date_parution";
		echo traite_rqt($rqt, "ALTER TABLE notices DROP INDEX i_date_parution");
		$rqt = "ALTER TABLE notices ADD INDEX i_date_parution (date_parution) ;";
		echo traite_rqt($rqt, "ALTER TABLE notices ADD INDEX i_date_parution");
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='flux_rss_notices_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) 
					VALUES (0, 'opac', 'flux_rss_notices_order', ' index_serie, tnvol, index_sew ', 'Ordre d\'affichage des notices dans les flux sortants dans l\'opac \n  index_serie, tnvol, index_sew : tri par titre de série et titre \n rand()  : aléatoire \n notice_id desc par ordre décroissant de création de notice', 'l_dsi')";
			echo traite_rqt($rqt,"insert opac_flux_rss_notices_order=' index_serie, tnvol, index_sew ' into parametres");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='modules_search_titre_uniforme' "))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'opac', 'modules_search_titre_uniforme', '1', 'Recherche dans les titres uniformes : \n 0 : interdite, \n 1 : autorisée, \n 2 : autorisée et validée par défaut', 'c_recherche', 0) ";
			echo traite_rqt($rqt, "insert opac_modules_search_titre_uniforme into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='congres_affichage_mode' "))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param,valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'opac', 'congres_affichage_mode', '0', 'Mode d\'affichage des congrès: \n 0 : Comme pour les auteurs, \n 1 : ajout d\'un navigateur de congrès', 'd_aff_recherche', 0) ";
			echo traite_rqt($rqt, "insert opac_congres_affichage_mode into parameters");
		}
			
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_suggest_notice' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
					VALUES(0,'opac','show_suggest_notice','0','Afficher le lien de proposition de suggestion sur une notice existante.\n 0 : Non.\n 1 : Oui, avec authentification.\n 2 : Oui, sans authentification.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_suggest_notice into parametres") ;
		}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.70");
//		break;

//	case "v4.70": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+
		//ajout statut/flag exemplaire numerique
		$rqt = "ALTER TABLE explnum ADD explnum_statut INT(5) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt, "alter table explnum add explnum_statut");

		//ajout parametre pour gerer statut spécifique sur les exemplaires numeriques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='explnum_statut' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) 
						VALUES ('pmb','explnum_statut', '0', 'Utiliser un statut sur les documents numériques \n 0: non \n 1: oui', '', '0')" ;
			echo traite_rqt($rqt,"insert pmb_explnum_statut=0 into parametres");
		}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.71");
//		break;

//	case "v4.71": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='modules_search_titre_uniforme' "))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'opac', 'modules_search_titre_uniforme', '1', 'Recherche dans les titres uniformes : \n 0 : interdite, \n 1 : autorisée, \n 2 : autorisée et validée par défaut', 'c_recherche', 0) ";
			echo traite_rqt($rqt, "insert opac_modules_search_titre_uniforme into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='congres_affichage_mode' "))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param,valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'opac', 'congres_affichage_mode', '0', 'Mode d\'affichage des congrès: \n 0 : Comme pour les auteurs, \n 1 : ajout d\'un navigateur de congrès', 'd_aff_recherche', 0) ";
			echo traite_rqt($rqt, "insert opac_congres_affichage_mode into parameters");
		}
			
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_empty_items_block' "))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param,valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'opac', 'show_empty_items_block', '1', 'Afficher le bloc exemplaires même si aucun exemplaire sur la notice ? : \n 0 : Non, \n 1 : Oui', 'd_aff_recherche', 0) ";
			echo traite_rqt($rqt, "insert opac_show_empty_items_block=1 into parameters");
		}

		$rqt = "ALTER TABLE avis DROP INDEX avis_num_notice";
		echo traite_rqt($rqt, "ALTER TABLE avis DROP INDEX");
		$rqt = "ALTER TABLE avis ADD INDEX avis_num_notice (num_notice)";
		echo traite_rqt($rqt, "ALTER TABLE avis ADD INDEX avis_num_notice");
		 
		$rqt = "ALTER TABLE avis DROP INDEX avis_num_empr";
		echo traite_rqt($rqt, "ALTER TABLE avis DROP INDEX");
		$rqt = "ALTER TABLE avis ADD INDEX avis_num_empr (num_empr)";
		echo traite_rqt($rqt, "ALTER TABLE avis ADD INDEX avis_num_empr");

		$rqt = "ALTER TABLE avis DROP INDEX avis_note";
		echo traite_rqt($rqt, "ALTER TABLE avis DROP INDEX");
		$rqt = "ALTER TABLE avis ADD INDEX avis_note (note)";
		echo traite_rqt($rqt, "ALTER TABLE avis ADD INDEX avis_note"); 
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.72");
//		break;

//	case "v4.72": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		if (!substr($opac_show_languages,2)) {
			// si la liste des langues possibles n'est pas affichée elle doit quand même contenir la langue par défaut.
			$rqt="update parametres set valeur_param='".substr($opac_show_languages,0,1)." ".$opac_default_lang."' where type_param='opac' and sstype_param='show_languages'" ;
			echo traite_rqt($rqt, "Update opac_show_languages, opac_default_lang must be set even if opac_show_languages is set to 0");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_ticket_script' "))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param,valeur_param, comment_param, section_param, gestion) 
					VALUES (NULL,'pmb', 'printer_ticket_script', '', 'Script permettant de personaliser l\'impression du ticket de prêt. Le répertoire du script est à paramétrer à partir de la racine de PMB.\nSi vide PMB utilise ./circ/ticket-pret.inc.php', '', '0')";
			echo traite_rqt($rqt, "insert pmb_printer_ticket_script='' into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='curl_proxy' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) 
					VALUES (0, 'opac', 'curl_proxy', '', 'Paramétrage de proxy de cURL, vide si aucun proxy, sinon\nhost,port,user,password;2nd_host et ainsi de suite','a_general')";
			echo traite_rqt($rqt,"insert opac_curl_proxy='' into parametres");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='curl_proxy' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) 
					VALUES (0, 'pmb', 'curl_proxy', '', 'Paramétrage de proxy de cURL, vide si aucun proxy, sinon\nhost,port,user,password;2nd_host et ainsi de suite','')";
			echo traite_rqt($rqt,"insert pmb_curl_proxy='' into parametres");
		}
		
		//suppression parametre impression commentaires devis obsolete
		$rqt = "delete from parametres where type_param='acquisition' and sstype_param='pdfdev_comment' " ;
		echo traite_rqt($rqt,"delete acquisition_pdfdev_comment from parametres") ;

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='latest_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) 
					VALUES (0, 'pmb', 'latest_order', 'notice_id desc', 'Tri des dernières notices ? \n notice_id desc : par id de notice décroissant: idéal mais peut être problématique après une migration ou un import \n create_date desc: par la colonne date de création.','')";
			echo traite_rqt($rqt,"insert pmb_latest_order='notice_id desc' into parametres");
		}

		// sert à rendre facultatif les champs perso normalement obligatoires sur la création des notices de bulletin
		$rqt = "ALTER TABLE notices_custom ADD exclusion_obligatoire INT(1) UNSIGNED NOT NULL DEFAULT 0" ;
		echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD exclusion_obligatoire ") ;
		// les tables notices_custom, empr_custom et expl_custom sont gérées par la même classe, donc champs identiques :
		$rqt = "ALTER TABLE expl_custom ADD exclusion_obligatoire INT(1) UNSIGNED NOT NULL DEFAULT 0" ;
		echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD exclusion_obligatoire ") ;
		$rqt = "ALTER TABLE empr_custom ADD exclusion_obligatoire INT(1) UNSIGNED NOT NULL DEFAULT 0" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD exclusion_obligatoire ") ;
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='password_forgotten_show' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) 
					VALUES ('opac','password_forgotten_show', '1', 'Afficher le lien  \"Mot de passe oublié ?\" \n 0: Non \n 1: Oui', 'f_modules', '0')";
			echo traite_rqt($rqt,"insert opac_password_forgotten_show='1' into parametres");
		}

		$rqt = "update parametres set comment_param=concat(comment_param,' \n 3: invisibles') where type_param='opac' and sstype_param='recherches_pliables' and comment_param not like '% 3:%'";
		echo traite_rqt($rqt,"change opac_recherches_pliables's comment ") ;
		
		//Gestion des Etats de collections
		$rqt = "ALTER TABLE collections_state DROP PRIMARY KEY ";
		echo traite_rqt($rqt,"ALTER TABLE collections_state DROP PRIMARY KEY ") ;
		$rqt = "ALTER TABLE collections_state ADD collstate_id INT( 8 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ";    
		echo traite_rqt($rqt,"ALTER TABLE collections_state ADD collstate_id") ;        
		$rqt = "ALTER TABLE collections_state 
		    ADD collstate_emplacement INT( 8 ) UNSIGNED NOT NULL DEFAULT 0,
		    ADD collstate_type INT( 8 ) UNSIGNED NOT NULL DEFAULT 0,
		    ADD collstate_origine VARCHAR( 255 ) NOT NULL default '',
		    ADD collstate_cote VARCHAR( 255 ) NOT NULL default '',
		    ADD collstate_archive INT( 8 ) UNSIGNED NOT NULL DEFAULT 0,
		    ADD collstate_statut INT( 8 ) UNSIGNED NOT NULL DEFAULT 0,
		    ADD collstate_lacune TEXT NOT NULL default '',
		    ADD collstate_note TEXT NOT NULL default '' ";        
		echo traite_rqt($rqt,"ALTER TABLE collections_state") ;        

		$rqt = "CREATE TABLE arch_emplacement (
		    archempla_id INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		    archempla_libelle VARCHAR( 255 ) NOT NULL default '')";        
		echo traite_rqt($rqt,"CREATE TABLE arch_emplacement ") ;        

		$rqt = "CREATE TABLE arch_type (
		    archtype_id INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		    archtype_libelle VARCHAR( 255 ) NOT NULL default '')";        
		echo traite_rqt($rqt,"CREATE TABLE arch_type") ;        

		$rqt = "CREATE TABLE arch_statut (
		    archstatut_id INT( 8 ) NOT NULL auto_increment ,
		    archstatut_gestion_libelle VARCHAR( 255 ) NOT NULL default '',
		    archstatut_opac_libelle VARCHAR( 255 ) NOT NULL ,
		    archstatut_visible_opac TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 1,
		    archstatut_visible_opac_abon TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 1,
		    archstatut_visible_gestion TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 1,
		    archstatut_class_html VARCHAR( 255 ) NOT NULL default '',
		    PRIMARY KEY ( archstatut_id ))";        
		echo traite_rqt($rqt,"CREATE TABLE arch_statut") ;        

		$rqt = "ALTER TABLE notices ADD opac_visible_bulletinage TINYINT UNSIGNED NOT NULL DEFAULT 1";        
		echo traite_rqt($rqt,"ALTER TABLE notices ADD opac_visible_bulletinage") ;        

		$rqt = "CREATE TABLE collstate_custom (
		    idchamp int(10) unsigned NOT NULL auto_increment,
		    name varchar(255) NOT NULL default '',
		    titre varchar(255) not NULL default '',
		    type varchar(10) NOT NULL default 'text',
		    datatype varchar(10) NOT NULL default '',
		    options text,
		    multiple int(11) NOT NULL default 0,
		    obligatoire int(11) NOT NULL default 0,
		    ordre int(11) not NULL default 0,
		    search int(11) NOT NULL default 0,
		    export int(1) unsigned NOT NULL default 0,
		    exclusion_obligatoire int(1) unsigned NOT NULL default 0,
		    PRIMARY KEY  (idchamp))";        

		echo traite_rqt($rqt,"CREATE TABLE collstate_custom") ;        
		$rqt = "CREATE TABLE collstate_custom_lists (
		    collstate_custom_champ int(10) unsigned NOT NULL default 0,
		    collstate_custom_list_value varchar(255) NOT NULL default '',
		    collstate_custom_list_lib varchar(255) NOT NULL default '',
		    ordre int(11)  NOT NULL default 0,
		    KEY collstate_custom_champ (collstate_custom_champ),
		    KEY collstate_champ_list_value (collstate_custom_champ,collstate_custom_list_value))";        
		echo traite_rqt($rqt,"CREATE TABLE collstate_custom_lists ") ;
		
		$rqt = "CREATE TABLE collstate_custom_values (
		    collstate_custom_champ int(10) unsigned NOT NULL default 0,
		    collstate_custom_origine int(10) unsigned NOT NULL default 0,
		    collstate_custom_small_text varchar(255) default NULL,
		    collstate_custom_text text,
		    collstate_custom_integer int(11) default NULL,
		    collstate_custom_date date default NULL,
		    collstate_custom_float float default NULL,
		    KEY collstate_custom_champ (collstate_custom_champ),
		    KEY collstate_custom_origine (collstate_custom_origine) )";        
		echo traite_rqt($rqt,"CREATE TABLE collstate_custom_values") ;
		        
		$rqt = "ALTER TABLE users ADD deflt_arch_statut INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL ";        
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_arch_statut") ;        
		$rqt = "ALTER TABLE users ADD deflt_arch_emplacement INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL ";        
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_arch_emplacement ") ;        
		$rqt = "ALTER TABLE users ADD deflt_arch_type INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL ";        
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_arch_type INT( 6 )") ;        
		
        if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='aff_expl_localises' "))==0) {
            $rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param) 
            	VALUES ( 'opac','aff_expl_localises', '0', 'Activer l\'affichage des exemplaires localisés par onglet.\n 0 : désactivé \n 1: premier onglet affiche les exemplaires de la localisation du lecteur, le deuxieme affiche tous les exemplaires','e_aff_notice')";
            echo traite_rqt($rqt,"insert opac_aff_expl_localises=0");
        }
		
		//parametres activation gestion droits acces emprunteurs - notices 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_notice' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'gestion_acces', 'empr_notice', '0', 'Gestion des droits d\'accès des emprunteurs aux notices \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert gestion_acces_empr_notice=0 into parameters");
		}
		
		@set_time_limit(0);
		//modification structure table de stockage des droits ressources/utilisateurs
		$rqt = "describe acces_rights dom_rights ";
		$res = pmb_mysql_query($rqt, $dbh);
		$typ = pmb_mysql_result($res,0,1);
		if ($typ && substr($typ,0,3)!='int') {
			$rqt= "create temporary table acces_rights_tmp as select * from acces_rights ";
			echo traite_rqt($rqt,"create temporary table acces_rights_tmp") ;
			$rqt= "alter table acces_rights modify dom_rights int(2) unsigned not null default 0 ";
			echo traite_rqt($rqt,"alter table acces_rights modify dom_rights to integer") ;
			$rqt= "update acces_rights set dom_rights = (select conv(reverse(lpad(conv(ord(dom_rights),10,2),8,'0')),2,10) from acces_rights_tmp where 
			acces_rights_tmp.dom_num=acces_rights.dom_num and 
			acces_rights_tmp.usr_prf_num=acces_rights.usr_prf_num and acces_rights_tmp.res_prf_num=acces_rights.res_prf_num) ";
			echo traite_rqt($rqt,"update acces_rights") ;
		}
		
		//modification structure table de stockage des droits ressources/utilisateurs (domaine user_notice)
		$rqt = "describe acces_res_1 res_rights ";
		$res = pmb_mysql_query($rqt, $dbh);
		if(!pmb_mysql_errno()) {
			$typ = pmb_mysql_result($res,0,1);
			if ($typ && substr($typ,0,3)!='int') {
				$rqt= "create temporary table acces_res_1_tmp as select * from acces_res_1 ";
				echo traite_rqt($rqt,"create temporary table acces_res_1_tmp");
				$rqt = "update acces_res_1_tmp set res_mask=res_rights where res_mask='' ";
				echo traite_rqt($rqt, "update res_mask in table acces_res_1_tmp");
				flush();
				
				$rqt= "truncate table acces_res_1 ";
				echo traite_rqt($rqt,"truncate table acces_res_1");
				$rqt= "alter table acces_res_1 change prf_num res_prf_num int(2) unsigned not null default 0 ";
				echo traite_rqt($rqt,"alter table acces_res_1 modify prf_num res_prf_num") ;			
				$rqt= "alter table acces_res_1 add usr_prf_num int(2) unsigned not null default 0 after res_prf_num";
				echo traite_rqt($rqt,"alter table acces_res_1 add usr_prf_num");
				$rqt= "alter table acces_res_1 modify res_rights int(2) unsigned not null default 0, modify res_mask int(2) unsigned not null default 0 ";
				echo traite_rqt($rqt,"alter table acces_res_1 modify res_rights, res_mask to integer") ;
				$rqt= "alter table acces_res_1 drop primary key, drop index res_rights, drop index res_mask ";
				echo traite_rqt($rqt,"alter table acces_res_1 drop keys ");
				$rqt = "alter table acces_res_1 add primary key (res_num, usr_prf_num) ";
				echo traite_rqt($rqt,"alter table acces_res_1 add primary key ") ;
				flush();
				$rqt= "SELECT distinct usr_prf_num FROM acces_rights where dom_num=1 order by 1 ";
				$res= pmb_mysql_query($rqt, $dbh);
				$pos=1;
				while(($row=pmb_mysql_fetch_object($res))) {
					$rqt = "insert into acces_res_1 (select res_num, prf_num, ".$row->usr_prf_num.", conv(reverse(lpad(conv(ord(mid(res_rights,".$pos.",1)),10,2),8,'0')),2,10) , ( (conv(reverse(lpad(conv(ord(mid(res_rights,".$pos.",1)),10,2),8,'0')),2,10)) xor (conv(reverse(lpad(conv(ord(mid(res_mask ,".$pos.",1)),10,2),8,'0')),2,10)) ) from acces_res_1_tmp ) ";
					echo traite_rqt($rqt,"update acces_res_1 values for user profile=".($pos)) ;
					flush();
					$pos++;
				}
			}
		}

		// $opac_show_infopages_id_top
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_infopages_id_top' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'show_infopages_id_top', '', 'Id des infopages à afficher SUR la recherche simple, séparées par des virgules.', 'f_modules', 0) ";
			echo traite_rqt($rqt,"insert opac_show_infopages_id_top=0 into parametres") ;
		}
		
		// $opac_show_search_title
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_search_title' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'show_search_title', '0', 'Afficher le titre du bloc de recherche : \n 0 : Non, \n 1 : Oui', 'f_modules', 0) ";
			echo traite_rqt($rqt,"insert opac_show_search_title=0 into parametres") ;
		}
		
		//Gestion des recherche personalisée
		$rqt = "CREATE TABLE search_perso (
		    search_id INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		   	num_user INT( 8 ) UNSIGNED NOT NULL default 0 ,
		    search_name VARCHAR( 255 ) NOT NULL default '',
		    search_shortname VARCHAR( 50 ) NOT NULL default '',
		    search_query text NOT NULL default '',
		    search_human text NOT NULL default '',
		    search_directlink TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 0
		   )";    
		echo traite_rqt($rqt,"CREATE TABLE search_perso ") ;        
		//Gestion des recherche personalisée
		$rqt = "CREATE TABLE search_persopac (
		    search_id INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		   	num_empr INT( 8 ) UNSIGNED NOT NULL default 0 ,
		    search_name VARCHAR( 255 ) NOT NULL default '',
		    search_shortname VARCHAR( 50 ) NOT NULL default '',
		    search_query text NOT NULL default '',
		    search_human text NOT NULL default '',
		    search_directlink TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 0
		   )";    
		echo traite_rqt($rqt,"CREATE TABLE search_persopac ") ;        
		
		//Gestion de traduction des libellés
		$rqt = "CREATE TABLE translation (
		    trans_table VARCHAR( 100 ) NOT NULL default '',
		    trans_field VARCHAR( 100 ) NOT NULL default '',
		    trans_lang VARCHAR( 5 ) NOT NULL default '',
		   	trans_num INT( 8 ) UNSIGNED NOT NULL default 0 ,
		    trans_text VARCHAR( 255 ) NOT NULL default '',
		    PRIMARY KEY trans (trans_table,trans_field,trans_lang,trans_num),
		    index i_lang(trans_lang)
		   )";    
		echo traite_rqt($rqt,"CREATE TABLE translation ") ;  
		// paramètre d'activation de l'onglet 'Recherches préférées' en Opac	
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_personal_search' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			VALUES (0, 'opac', 'allow_personal_search', '0', 'Activer l\'affichage de l\'onglet des recherches personalisées \n 0 : Non.\n 1 : Oui.', 'c_recherche',0) ";
			echo traite_rqt($rqt, "insert pmb_liste_trad into parameters");
		}			
		
		// Passage de int(8) en varchar du numéro d'archive des états de collections
		$rqt = " ALTER TABLE collections_state CHANGE collstate_archive collstate_archive VARCHAR( 255 ) NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE collections_state CHANGE collstate_archive ");     
		   
		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_arc";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_arc");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_arc (collstate_archive)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_arc");

		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_empl";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_empl");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_empl (collstate_emplacement)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_empl");

		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_type";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_type");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_type (collstate_type)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_type");
		
		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_orig";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_orig");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_orig (collstate_origine)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_orig");
		
		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_cote";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_cote");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_cote (collstate_cote)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_cote");

		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_stat";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_stat");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_stat (collstate_statut)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_stat");

		$rqt = "ALTER TABLE search_persopac ADD search_limitsearch TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt, "ALTER TABLE search_persopac ADD search_limitsearch");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.73");
//		break;

//	case "v4.73": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		// paramètre LDAP en OPAC seulement	
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'ldap' and sstype_param='opac_only' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			VALUES (0,'ldap','opac_only','0','Ne pas utiliser l\'authentification LDAP en gestion: \n 0: Non \n 1 : Oui, en OPAC uniquement','',0) ";
			echo traite_rqt($rqt, "insert ldap_opac_only=0 into parameters");
		}			
		
		//Opérateur de recherche pour les recherches sur plusieurs valeurs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='multi_search_operator' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'multi_search_operator', 'or', 'Type d\'opérateur de recherche pour les listes avec plusieurs valeurs: \n or : pour le OU \n and : pour le ET', '', '0')";
			echo traite_rqt($rqt,"insert multi_search_operator='or' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='multi_search_operator' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'multi_search_operator', 'or', 'Type d\'opérateur de recherche pour les listes avec plusieurs valeurs: \n or : pour le OU \n and : pour le ET', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_multi_search_operator='or' into parametres ");
		}
	
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.74");
//		break;

//	case "v4.74": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		//Ajout index Pour amélioration recherche sur les états de collection
		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_serial";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_serial");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_serial (id_serial)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_serial");
 
		$rqt = "ALTER TABLE collections_state DROP INDEX i_colls_loc";
		echo traite_rqt($rqt, "ALTER TABLE collections_state DROP INDEX i_colls_loc");
		$rqt = "ALTER TABLE collections_state ADD INDEX i_colls_loc (location_id)";
		echo traite_rqt($rqt, "ALTER TABLE collections_state ADD INDEX i_colls_loc");
		
		$rqt = "update authors set author_name='' where author_name is null ";
		echo traite_rqt($rqt, "update authors set author_name='' where author_name is null ");  
		$rqt = "update authors set author_rejete='' where author_rejete is null ";
		echo traite_rqt($rqt, "update authors set author_rejete='' where author_rejete is null ");  
		$rqt = "update indexint set indexint_comment='' where indexint_comment is null ";
		echo traite_rqt($rqt, "update indexint set indexint_comment='' where indexint_comment is null ");  
		
		$rqt = "ALTER TABLE authors CHANGE author_name author_name VARCHAR( 255 ) NOT NULL default ''";
		echo traite_rqt($rqt, "ALTER TABLE authors CHANGE author_name NOT NULL default ''");  
		$rqt = "ALTER TABLE authors CHANGE author_rejete author_rejete VARCHAR( 255 ) NOT NULL default ''";
		echo traite_rqt($rqt, "ALTER TABLE authors CHANGE author_rejete NOT NULL default ''");  
		$rqt = "ALTER TABLE indexint CHANGE indexint_comment indexint_comment TEXT NOT NULL default ''";
		echo traite_rqt($rqt, "ALTER TABLE indexint CHANGE indexint_comment NOT NULL default ''");  
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.75");
//		break;

//	case "v4.75": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		// transfert
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='pret_statut_transfert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'pret_statut_transfert', '0', '1', 'Autoriser le prêt lorsque l\'exemplaire est en transfert') ";
			echo traite_rqt($rqt,"INSERT pret_statut_transfert INTO parametres") ;
		}
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.76");
//		break;

//	case "v4.76": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+
		// Pour import/export des notices liées 
		//    sert à définir les différents paramètres pour l'export des notices liées en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='generer_liens' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'generer_liens', '0', 'Générer les liens entre les notices pour l\'export', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_generer_liens='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_mere' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_mere', '0', 'Exporter les notices liées mères', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_mere='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_fille' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_fille', '0', 'Exporter les notices liées filles', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_fille='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_bull_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_bull_link', '1', 'Exporter les liens vers les bulletins pour les notices d\'article', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_bull_link='1' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_perio_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_perio_link', '1', 'Exporter les liens vers les périodiques pour les notices d\'article', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_perio_link='1' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_art_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_art_link', '1', 'Exporter les liens vers les articles pour les notices de périodique', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_art_link='1' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_bulletinage' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_bulletinage', '0', 'Générer le bulletinage pour les notices de périodiques', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_bulletinage='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_notice_perio_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_notice_perio_link', '0', 'Exporter les notices liées de périodique', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_notice_perio_link='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_notice_art_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_notice_art_link', '0', 'Exporter les notices liées d\'article', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_notice_art_link='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_notice_mere_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_notice_mere_link', '0', 'Exporter les notices mères liées', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_notice_mere_link='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_notice_fille_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'exportparam', 'export_notice_fille_link', '0', 'Exporter les notices filles liées', '', '1')";
			echo traite_rqt($rqt,"insert exportparam_export_notice_fille_link='0' into parametres ");
		}
	
		//    sert à définir les différents paramètres pour l'export des notices liées en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_generer_liens' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_generer_liens', '0', 'Générer les liens entre les notices pour l\'export', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_generer_liens='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_mere' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_mere', '0', 'Exporter les notices liées mères', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_mere='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_fille' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_fille', '0', 'Exporter les notices liées filles', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_fille='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_bull_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_bull_link', '1', 'Exporter les liens vers les bulletins pour les notices d\'article', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_bull_link='1' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_perio_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_perio_link', '1', 'Exporter les liens vers les périodiques pour les notices d\'article', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_perio_link='1' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_art_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_art_link', '1', 'Exporter les liens vers les articles pour les notices de périodique', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_art_link='1' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_bulletinage' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_bulletinage', '0', 'Générer le bulletinage pour les notices de périodiques', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_bulletinage='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_notice_perio_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_notice_perio_link', '0', 'Exporter les notices liées de périodique', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_notice_perio_link='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_notice_art_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_notice_art_link', '0', 'Exporter les notices liées d\'article', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_notice_art_link='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_notice_mere_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_notice_mere_link', '0', 'Exporter les notices mères liées', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_notice_mere_link='0' into parametres ");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_notice_fille_link' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'exp_export_notice_fille_link', '0', 'Exporter les notices filles liées', '', '1')";
			echo traite_rqt($rqt,"insert opac_exp_export_notice_fille_link='0' into parametres ");
		}
		
		//   Ajout d'un champ parametre d'export en DSI
		$rqt="ALTER TABLE bannettes ADD param_export BLOB NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"ALTER TABLE bannettes ADD param_export ");

		//STATISTIQUEs DE L'OPAC

		//Paramètres pour les statistiques de l'OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='perio_vidage_log' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'perio_vidage_log', '1', 'Périodicité de vidage de la table de logs (en jours)', '', '0')";
			echo traite_rqt($rqt,"insert perio_vidage_log='1' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='perio_vidage_stat' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'perio_vidage_stat', '1,30', 'Périodicité de vidage de la table de logs (en jours) : mode,jours \n 1,x : vider tous les x jours \n 2,x : vider tout ce qui a plus de x jours \n 0 : ne rien faire', '', '0')";
			echo traite_rqt($rqt,"insert perio_vidage_stat='1,30' into parametres ");
		}
		
		// suppr param en trop
		pmb_mysql_query("delete from parametres where type_param= 'opac' and sstype_param='logs_activate' ");
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='logs_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'logs_activate', '0', 'Activer les statistiques pour l\'OPAC: \n 0 : non activé \n 1 : activé', '', '0')";
			echo traite_rqt($rqt,"insert logs_activate='0' into parametres ");
		}
		
		//Création des tables pour la gestion des stats de l'OPAC
		$rqt = "CREATE TABLE logopac(
			id_log INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			date_log TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			url_demandee VARCHAR( 255 )  NOT NULL default '',
			url_referente VARCHAR( 255 ) NOT NULL default '',
			get_log BLOB NOT NULL default '',
			post_log BLOB NOT NULL default '',
			num_session VARCHAR( 255 ) NOT NULL default '',
			server_log BLOB NOT NULL default '',
			empr_carac BLOB NOT NULL default '',
			empr_doc BLOB NOT NULL default '',
			empr_expl BLOB NOT NULL default '',
			nb_result BLOB NOT NULL default ''
			)";
		echo traite_rqt($rqt,"CREATE TABLE logopac ") ; 
		
		$rqt = "CREATE TABLE statopac(
			id_log INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			date_log TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			url_demandee VARCHAR( 255 )  NOT NULL default '',
			url_referente VARCHAR( 255 ) NOT NULL default '',
			get_log BLOB NOT NULL default '',
			post_log BLOB NOT NULL default '',
			num_session VARCHAR( 255 ) NOT NULL default 0,
			server_log BLOB NOT NULL default '',
			empr_carac BLOB NOT NULL default '',
			empr_doc BLOB NOT NULL default '',
			empr_expl BLOB NOT NULL default '',
			nb_result BLOB NOT NULL default ''
			)";
		echo traite_rqt($rqt,"CREATE TABLE statopac ") ; 
		
		$rqt = "CREATE TABLE statopac_request(
			idproc INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			name VARCHAR( 255 )  NOT NULL default '',
			requete BLOB NOT NULL default '',
			comment TINYTEXT NOT NULL default '',
			parameters TEXT NOT NULL default '',
			num_vue MEDIUMINT( 8 ) NOT NULL default 0,
			autorisations MEDIUMTEXT NOT NULL default ''
			)";
		echo traite_rqt($rqt,"CREATE TABLE statopac_request ") ;
		
		$rqt = "CREATE TABLE statopac_vues(
			id_vue INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			date_consolidation datetime NOT NULL DEFAULT '0000-000-00 00:00:00',
			nom_vue VARCHAR( 255 )  NOT NULL default '',
			comment TINYTEXT NOT NULL default ''
			)";
		echo traite_rqt($rqt,"CREATE TABLE statopac_vues ") ;
		
		$rqt = "CREATE TABLE statopac_vues_col(
			id_col INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			nom_col VARCHAR( 255 )  NOT NULL default '',
			expression VARCHAR( 255 )  NOT NULL default '',
			num_vue MEDIUMINT( 8 ) NOT NULL default 0,
			ordre MEDIUMINT( 8 ) NOT NULL default 0,
			filtre VARCHAR( 255 )  NOT NULL default '',
			datatype VARCHAR( 10 )  NOT NULL default '',
			maj_flag INT( 1 ) NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE statopac_vues_col ") ;

	    //Listes de lecture partagées
		$rqt = "CREATE TABLE abo_liste_lecture(
			num_empr INT( 8 ) UNSIGNED NOT NULL default 0,
			num_liste INT( 8 ) UNSIGNED NOT NULL  default 0,
			PRIMARY KEY  (num_empr,num_liste)
			)";
		echo traite_rqt($rqt,"CREATE TABLE abo_liste_lecture ") ;
		
		$rqt = "CREATE TABLE opac_liste_lecture(
			id_liste INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			nom_liste VARCHAR( 255 )  NOT NULL default '',
			description VARCHAR( 255 )  NOT NULL default '',
			notices_associees BLOB NOT NULL default '',
			public INT( 1 ) NOT NULL default 0,
			num_empr INT( 8 ) UNSIGNED NOT NULL  default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE opac_liste_lecture ") ;
		
		//param	
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='shared_lists' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'shared_lists', '0', 'Activer les listes de lecture partagées \n 0 : non activées \n 1 : activées', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_shared_lists='0' into parametres ");
		}
		
		// Indexation des docs numériques	
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_docnum' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'indexation_docnum', '0', 'Activer l\'indexation des documents numériques \n 0 : non activée \n 1 : activée', '', '0')";
			echo traite_rqt($rqt,"insert indexation_docnum='0' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_docnum_allfields' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'indexation_docnum_allfields', '0', 'Activer par défaut la recherche dans les documents numériques pour la recherche \"Tous les champs\" \n 0 : non activée \n 1 : activée', '', '0')";
			echo traite_rqt($rqt,"insert indexation_docnum_allfields='0' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='indexation_docnum_allfields' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'indexation_docnum_allfields', '0', 'Activer par défaut la recherche dans les documents numériques pour la recherche \"Tous les champs\" \n 0 : non activée \n 1 : activée', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_indexation_docnum_allfields='0' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='modules_search_docnum' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'modules_search_docnum', '0', 'Recherche simple dans les documents numériques \n 0 : interdite \n 1 : autorisée \n 2 : autorisée et validée par défault', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_modules_search_docnum='0' into parametres ");
		}
		$rqt="ALTER TABLE explnum ADD explnum_index_sew TEXT NOT NULL DEFAULT '', ADD explnum_index_wew TEXT NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"ALTER TABLE explnum ADD explnum_index_sew, explnum_index_wew ");

		// localisation des réservations
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='location_reservation' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'pmb', 'location_reservation', '0', '0', 'Utiliser la gestion de la réservation localisée?\n 0: Non\n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT location_reservation INTO parametres") ;
		}
		$rqt = "CREATE TABLE resa_loc (
		   	resa_loc INT( 8 ) UNSIGNED NOT NULL default 0 ,
		   	resa_emprloc INT( 8 ) UNSIGNED NOT NULL default 0 ,
		   	PRIMARY KEY resa (resa_loc,resa_emprloc)
		   )";    
		echo traite_rqt($rqt,"CREATE TABLE resa_loc ") ; 

		$rqt = "ALTER TABLE resa_loc DROP INDEX i_resa_emprloc";
		echo traite_rqt($rqt, "ALTER TABLE resa_loc DROP INDEX i_resa_emprloc");
		$rqt = "ALTER TABLE resa_loc ADD INDEX i_resa_emprloc (resa_emprloc)";
		echo traite_rqt($rqt, "ALTER TABLE resa_loc ADD INDEX i_resa_emprloc (resa_emprloc)");
		
		// Extensions
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='extension_tab' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'extension_tab', '0', 'Afficher l\'onglet Extension ? \n 0 : Non \n 1 : Oui', '', '0')";
			echo traite_rqt($rqt,"insert pmb_extension_tab='0' into parametres ");
		}


		$rqt = "alter TABLE docs_type change idtyp_doc idtyp_doc int(5) unsigned NOT NULL auto_increment";
		echo traite_rqt($rqt, "alter TABLE docs_type change idtyp_doc idtyp_doc int(5)");
		
		$rqt = "alter TABLE exemplaires change expl_typdoc expl_typdoc int(5) unsigned NOT NULL default 0";
		echo traite_rqt($rqt, "alter TABLE exemplaires change expl_typdoc expl_typdoc int(5) ");
		
		$rqt = "alter TABLE pret_archive change arc_expl_typdoc arc_expl_typdoc int(5) unsigned default 0";
		echo traite_rqt($rqt, "alter TABLE pret_archive change arc_expl_typdoc arc_expl_typdoc int(5)");
		
		$rqt = "alter TABLE transferts change type_transfert type_transfert int(5) unsigned NOT NULL default 0";
		echo traite_rqt($rqt, "alter TABLE transferts change type_transfert type_transfert int(5)");
		
		$rqt = "alter TABLE transferts change origine origine int(5) unsigned NOT NULL default 0";
		echo traite_rqt($rqt, "alter TABLE transferts change origine origine int(5)");

		$rqt = "ALTER TABLE docs_location ADD css_style VARCHAR( 100 ) NOT NULL DEFAULT ''";
		echo traite_rqt($rqt, "ALTER TABLE docs_location ADD css_style ");

		// Upload des documents numériques
		$rqt = "CREATE TABLE upload_repertoire (
			repertoire_id INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			repertoire_nom VARCHAR( 255 )  NOT NULL default '',
			repertoire_url TEXT  NOT NULL default '',
			repertoire_path TEXT  NOT NULL default '',
			repertoire_navigation INT( 1 ) NOT NULL default 0,
			repertoire_hachage INT( 1 ) NOT NULL default 0,
			repertoire_subfolder INT( 8 ) NOT NULL default 0,
			repertoire_utf8 INT( 1 ) NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE upload_repertoire ") ;

		$rqt = "ALTER TABLE explnum ADD explnum_repertoire INT( 8 ) NOT NULL default 0, ADD explnum_path TEXT NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE explnum ") ;
		
		$rqt = "ALTER TABLE users ADD deflt_upload_repertoire INT( 8 ) NOT NULL default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE users ") ;

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_docnum_default' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'pmb', 'indexation_docnum_default', '0', 'Indexer le document numérique par défaut ? \n 0 : Non \n 1 : Oui', '', '0')";
			echo traite_rqt($rqt,"insert pmb_indexation_docnum_default='0' into parametres ");
		}
		
		// Modification sur les listes de lecture
		$rqt = "ALTER TABLE opac_liste_lecture ADD read_only INT( 1 ) NOT NULL default 0, ADD confidential INT( 1 ) NOT NULL default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE opac_liste_lecture ") ;
		
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='shared_lists_readonly' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'shared_lists_readonly', '0', 'Listes de lecture partagées en lecture seule \n 0 : non activées \n 1 : activées', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_shared_lists_readonly='0' into parametres ");
		}
		
		$rqt = "ALTER TABLE abo_liste_lecture ADD etat INT(1) UNSIGNED NOT NULL  default 0, ADD commentaire TEXT NOT NULL default ''";
		echo traite_rqt($rqt,"ALTER TABLE abo_liste_lecture") ;
		
		// Paramètres pour la connexion auto
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='connexion_phrase' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'connexion_phrase', '', 'Phrase permettant l\'encodage de la connexion automatique à partir d\'un mail', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_connexion_phrase='' into parametres ");
		}
		
		// Afficher le numéro du lecteur sous l'adresse dans les différentes lettres
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='afficher_numero_lecteur_lettres' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES ('pmb', 'afficher_numero_lecteur_lettres', '1', 'Afficher le numéro du lecteur sous l''adresse dans les différentes lettres.\r\n0: non\r\n1: oui', '', 0) ";
			echo traite_rqt($rqt, "insert afficher_numero_lecteur_lettres into parameters");
		}

		// Place le bloc d'adresse selon des coordonnées absolues
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='lettres_bloc_adresse_position_absolue' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES ('pmb', 'lettres_bloc_adresse_position_absolue', '0 100 40', 'Place le bloc d''adresse selon des coordonnées absolues.\nactivé x y\nactivé : activer cette fonction (valeurs: 0/1)\nx : Position horizontale\ny : Position verticale', '', 0)";
			echo traite_rqt($rqt, "insert lettres_bloc_adresse_position_absolue into parameters");
		}


		// CONNECTEURS SORTANTS
		// Durée de vie des recherches dans le cache, pour les services externes, en secondes.
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='external_service_search_cache' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES ('pmb', 'external_service_search_cache', '3600', 'Durée de vie des recherches dans le cache, pour les services externes, en secondes.', '', 0)";
			echo traite_rqt($rqt, "insert afficher_numero_lecteur_lettres into parameters");
		}

		// Durée de vie des sessions pour les services externes, en secondes..
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='external_service_session_duration' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES ('pmb', 'external_service_session_duration', '600', 'Durée de vie des sessions pour les services externes, en secondes.', '', 0)";
			echo traite_rqt($rqt, "insert afficher_numero_lecteur_lettres into parameters");
		}

		$rqt = "CREATE TABLE connectors_out (
 					connectors_out_id int(11) NOT NULL auto_increment,
  					connectors_out_config longblob NOT NULL DEFAULT '',
  					PRIMARY KEY  (connectors_out_id)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out") ;

		$rqt = "CREATE TABLE connectors_out_oai_tokens (
 					connectors_out_oai_token_token varchar(32) NOT NULL,
  					connectors_out_oai_token_environnement text NOT NULL DEFAULT '',
  					connectors_out_oai_token_expirationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  					PRIMARY KEY  (connectors_out_oai_token_token)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_oai_tokens ") ;

		$rqt = "CREATE TABLE connectors_out_setcaches (
  					connectors_out_setcache_id int(11) NOT NULL auto_increment,
  					connectors_out_setcache_setnum int(11) NOT NULL DEFAULT 0,
  					connectors_out_setcache_lifeduration int(4) NOT NULL DEFAULT 0,
  					connectors_out_setcache_lifeduration_unit enum('seconds','minutes','hours','days','weeks','months')  NOT NULL DEFAULT 'seconds',
  					connectors_out_setcache_lastupdatedate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  					PRIMARY KEY  (connectors_out_setcache_id),
  					UNIQUE KEY connectors_out_setcache_setnum (connectors_out_setcache_setnum)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_setcaches") ;

		$rqt = "CREATE TABLE connectors_out_setcache_values (
  					connectors_out_setcache_values_cachenum int(11) NOT NULL default 0,
  					connectors_out_setcache_values_value int(11) NOT NULL DEFAULT 0,
  					PRIMARY KEY (connectors_out_setcache_values_cachenum,connectors_out_setcache_values_value)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_setcache_values") ;

		$rqt = "CREATE TABLE connectors_out_setcategs (
  					connectors_out_setcateg_id int(11) NOT NULL auto_increment,
  					connectors_out_setcateg_name varchar(100) NOT NULL DEFAULT '',
  					PRIMARY KEY  (connectors_out_setcateg_id),
  					UNIQUE KEY connectors_out_setcateg_name (connectors_out_setcateg_name)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_setcategs") ;

		$rqt = "CREATE TABLE connectors_out_setcateg_sets (
  					connectors_out_setcategset_setnum int(11) NOT NULL,
  					connectors_out_setcategset_categnum int(11) NOT NULL DEFAULT 0,
  					PRIMARY KEY  (connectors_out_setcategset_setnum,connectors_out_setcategset_categnum)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_setcateg_sets") ;

		$rqt = "CREATE TABLE connectors_out_sets (
  					connector_out_set_id int(11) NOT NULL auto_increment,
  					connector_out_set_caption varchar(100) NOT NULL DEFAULT '',
  					connector_out_set_type int(4) NOT NULL DEFAULT 0,
  					connector_out_set_config longblob NOT NULL DEFAULT '',
  					PRIMARY KEY  (connector_out_set_id),
  					UNIQUE KEY connector_out_set_caption (connector_out_set_caption)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_sets") ;

		$rqt = "CREATE TABLE connectors_out_sources (
  					connectors_out_source_id int(11) NOT NULL auto_increment,
  					connectors_out_sources_connectornum int(11) NOT NULL DEFAULT 0,
  					connectors_out_source_name varchar(100) NOT NULL DEFAULT '',
  					connectors_out_source_comment varchar(200) NOT NULL DEFAULT '',
  					connectors_out_source_config longblob NOT NULL DEFAULT '',
  					PRIMARY KEY  (connectors_out_source_id)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_sources") ;

		$rqt = "CREATE TABLE connectors_out_sources_esgroups (
  					connectors_out_source_esgroup_sourcenum int(11) NOT NULL default 0,
  					connectors_out_source_esgroup_esgroupnum int(11) NOT NULL DEFAULT 0,
  					PRIMARY KEY  (connectors_out_source_esgroup_sourcenum,connectors_out_source_esgroup_esgroupnum)
				)";
		echo traite_rqt($rqt,"CREATE TABLE connectors_out_sources_esgroups") ;

		$rqt = "CREATE TABLE es_cache (
  					escache_groupname varchar(100)  NOT NULL DEFAULT '',
  					escache_unique_id varchar(100)  NOT NULL DEFAULT '',
  					escache_value int(11) NOT NULL DEFAULT 0,
  					PRIMARY KEY  (escache_groupname,escache_unique_id,escache_value)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_cache ") ;

		$rqt = "CREATE TABLE es_converted_cache (
  					es_converted_cache_objecttype int(11) NOT NULL DEFAULT 0,
	  				es_converted_cache_objectref int(11) NOT NULL DEFAULT 0,
  					es_converted_cache_format varchar(50) NOT NULL DEFAULT '',
  					es_converted_cache_value text NOT NULL DEFAULT '',
  					es_converted_cache_bestbefore datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  					PRIMARY KEY  (es_converted_cache_objecttype,es_converted_cache_objectref,es_converted_cache_format)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_converted_cache") ;

		$rqt = "CREATE TABLE es_esgroups (
  					esgroup_id int(11) NOT NULL auto_increment,
  					esgroup_name varchar(100) NOT NULL DEFAULT '',
  					esgroup_fullname varchar(255) NOT NULL DEFAULT '',
  					esgroup_pmbusernum int(5) NOT NULL DEFAULT 0,
  					PRIMARY KEY  (esgroup_id),
  					UNIQUE KEY esgroup_name (esgroup_name)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_esgroups") ;

		$rqt = "CREATE TABLE es_esgroup_esusers (
  					esgroupuser_groupnum int(11) NOT NULL DEFAULT 0,
  					esgroupuser_usertype int(4) NOT NULL DEFAULT 0,
  					esgroupuser_usernum int(11) NOT NULL DEFAULT 0,
  					PRIMARY KEY  (esgroupuser_usernum,esgroupuser_groupnum,esgroupuser_usertype)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_esgroup_esusers") ;

		$rqt = "CREATE TABLE es_esusers (
  					esuser_id int(11) NOT NULL auto_increment,
  					esuser_username varchar(100) NOT NULL DEFAULT '',
  					esuser_password varchar(100) NOT NULL DEFAULT '',
  					esuser_fullname varchar(255) NOT NULL DEFAULT '',
  					esuser_groupnum int(11) NOT NULL default 0,
  					PRIMARY KEY  (esuser_id),
  					UNIQUE KEY esuser_username (esuser_username)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_esusers") ;

		$rqt = "CREATE TABLE es_methods (
  					id_method int(10) unsigned NOT NULL auto_increment,
  					groupe varchar(255) NOT NULL DEFAULT '',
  					method varchar(255) NOT NULL DEFAULT '',
  					available smallint(5) unsigned NOT NULL DEFAULT 1,
  					PRIMARY KEY  (id_method)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_methods") ;

		$rqt = "CREATE TABLE es_methods_users (
  					num_method int(10) unsigned NOT NULL DEFAULT 0,
  					num_user int(10) unsigned NOT NULL DEFAULT 0,
  					anonymous smallint(6) DEFAULT '0',
  					PRIMARY KEY  (num_method,num_user)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_methods_users ") ;

		$rqt = "CREATE TABLE es_searchcache (
  					es_searchcache_searchid varchar(100) NOT NULL default '',
  					es_searchcache_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  					es_searchcache_serializedsearch text NOT NULL DEFAULT '',
  					PRIMARY KEY  (es_searchcache_searchid)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_searchcache") ;

		$rqt = "CREATE TABLE es_searchsessions (
 				es_searchsession_id varchar(100) NOT NULL default '',
  				es_searchsession_searchnum varchar(100) NOT NULL DEFAULT '',
  				es_searchsession_searchrealm varchar(100) NOT NULL DEFAULT '',
  				es_searchsession_pmbuserid int(11) NOT NULL DEFAULT -1,
  				es_searchsession_opacemprid int(11) NOT NULL DEFAULT -1,
  				es_searchsession_lastseendate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  				PRIMARY KEY  (es_searchsession_id)
				)";
		echo traite_rqt($rqt,"CREATE TABLE es_searchsessions") ;

		// Gestion des parties d'un exemplaire en RFID
		$rqt = "ALTER TABLE exemplaires ADD expl_nbparts INT( 8 ) unsigned NOT NULL default 1 ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires ") ;

			
		//Suggestions multiples
	
		$rqt = "ALTER TABLE suggestions ADD sugg_source INT( 8 ) NOT NULL default 0, 
					ADD date_publication varchar(255) NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE suggestions ") ;
	
		$rqt = "CREATE TABLE suggestions_source (
	 				id_source INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	  				libelle_source varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY (id_source))";	
		echo traite_rqt($rqt,"CREATE TABLE suggestions_source") ;
	
		$rqt = "CREATE TABLE explnum_doc (
	 				id_explnum_doc INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	  				num_doc INT(8) NOT NULL DEFAULT 0,
					type_doc varchar(3) NOT NULL DEFAULT 'sug',
					explnum_doc_nomfichier text NOT NULL DEFAULT '',
					explnum_doc_mimetype varchar(255) NOT NULL DEFAULT '',
					explnum_doc_data blob NOT NULL DEFAULT '',
					explnum_doc_extfichier varchar(20) NOT NULL DEFAULT '',
					PRIMARY KEY (id_explnum_doc))";	
		echo traite_rqt($rqt,"CREATE TABLE explnum_doc") ;
	
		$rqt = "ALTER TABLE suggestions_origine ADD INDEX i_origine (origine,type_origine)";
		echo traite_rqt($rqt, "ADD INDEX i_origine to suggestions_origine");
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='allow_multiple_sugg' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'allow_multiple_sugg', '0', 'Autoriser les suggestions multiples.\r\n0: non\r\n1: oui', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_allow_multiple_sugg='0' into parametres ");
		}
		
		//Caches services externes
	
		$rqt = "CREATE TABLE es_cache_blob (
				  es_cache_objectref varchar(100) NOT NULL DEFAULT '',
				  es_cache_objecttype int(11) NOT NULL DEFAULT 0,
				  es_cache_objectformat varchar(100) NOT NULL DEFAULT '',
				  es_cache_owner varchar(100) NOT NULL DEFAULT '',
				  es_cache_creationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  es_cache_expirationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  es_cache_content mediumblob NOT NULL DEFAULT '',
				  PRIMARY KEY  (es_cache_objectref,es_cache_objecttype,es_cache_objectformat,es_cache_owner))";	
		echo traite_rqt($rqt,"CREATE TABLE es_cache_blob") ;
	
		$rqt = "CREATE TABLE es_cache_int (
				  es_cache_objectref varchar(100) NOT NULL DEFAULT '',
				  es_cache_objecttype int(11) NOT NULL DEFAULT 0,
				  es_cache_objectformat varchar(100) NOT NULL DEFAULT '',
				  es_cache_owner varchar(100) NOT NULL DEFAULT '',
				  es_cache_creationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  es_cache_expirationdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  es_cache_content int NOT NULL DEFAULT 0,
				  PRIMARY KEY  (es_cache_objectref,es_cache_objecttype,es_cache_objectformat,es_cache_owner))";
		echo traite_rqt($rqt,"CREATE TABLE es_cache_int");
			
		// Template de notice
		$rqt = "CREATE TABLE notice_tpl (
 				notpl_id int(10) unsigned NOT NULL auto_increment,
  				notpl_name varchar(256) NOT NULL DEFAULT '',
  				notpl_code text NOT NULL DEFAULT '',
				notpl_comment varchar(256) NOT NULL DEFAULT '',
 				notpl_id_test int(10) unsigned  NOT NULL DEFAULT 0,
  				PRIMARY KEY  (notpl_id)
				)";
		echo traite_rqt($rqt,"CREATE TABLE notice_tpl") ;
		
		$rqt = "CREATE TABLE notice_tplcode(
 				num_notpl int(10) unsigned  NOT NULL DEFAULT 0,
 				notplcode_localisation mediumint(8) NOT NULL default 0,
  				notplcode_typdoc char(2) not null default 'a',
				notplcode_niveau_biblio char(1) not null default 'm',
				notplcode_niveau_hierar char(1) not null default '0',				
				nottplcode_code text NOT NULL DEFAULT '',
				PRIMARY KEY  (num_notpl,notplcode_localisation,notplcode_typdoc,notplcode_niveau_biblio)
				)";
		echo traite_rqt($rqt,"CREATE TABLE notice_tplcode") ;
		
		$rqt = "ALTER TABLE bannettes ADD piedpage_mail text NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt, "ALTER TABLE bannettes ADD piedpage_mail ");
		
		$rqt = "ALTER TABLE bannettes ADD notice_tpl int(10) unsigned  NOT NULL DEFAULT 0 ";		
		echo traite_rqt($rqt, "ALTER TABLE bannettes ADD notice_tpl ");
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='bannette_notices_template' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES ('opac', 'bannette_notices_template', '0', 'Id du template de notice utilisé par défaut en diffusion de bannettes. Si vide ou à 0, le template classique est utilisé.', 'l_dsi', 0)";
			echo traite_rqt($rqt, "insert bannette_notices_template into parameters");
		}
		
		$rqt = "ALTER TABLE bannettes ADD group_pperso int(10) unsigned NOT NULL DEFAULT 0 ";		
		echo traite_rqt($rqt, "ALTER TABLE bannettes ADD group_pperso ");
		
		$rqt = "ALTER TABLE abts_abts drop index index_num_notice " ;
		echo traite_rqt($rqt,"drop index index_num_notice");
		$rqt = "ALTER TABLE abts_abts ADD INDEX index_num_notice (num_notice)" ;
		echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD INDEX index_num_notice");
		
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.77");
//		break;

//	case "v4.77": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		$rqt = "ALTER TABLE import_marc DROP INDEX i_nonot_orig " ;
		echo traite_rqt($rqt,"DROP INDEX i_nonot_orig");
		$rqt = "ALTER TABLE import_marc ADD INDEX i_nonot_orig(no_notice,origine)" ;
		echo traite_rqt($rqt,"ALTER TABLE import_marc ADD INDEX i_nonot_orig");
		
		$rqt = "ALTER TABLE resa ADD resa_arc int(10) unsigned  NOT NULL DEFAULT 0 ";		
		echo traite_rqt($rqt, "ALTER TABLE resa ADD resa_arc ");
				
		$rqt = "CREATE TABLE resa_archive (
			resarc_id int(10) unsigned NOT NULL auto_increment,
			resarc_date datetime NOT NULL default '0000-00-00 00:00:00',
			resarc_debut date NOT NULL default'0000-00-00',
			resarc_fin date NOT NULL default'0000-00-00',			
			resarc_idnotice int(10) unsigned NOT NULL default '0',
			resarc_idbulletin int(10) unsigned NOT NULL default '0',			
			resarc_confirmee int(1) unsigned default '0',
			resarc_cb varchar(14) NOT NULL default '',
			resarc_loc_retrait smallint(5) unsigned default '0',		
			resarc_from_opac int(1) unsigned default '0',
			resarc_anulee int(1) unsigned default '0',
			resarc_pretee int(1) unsigned default '0',
			resarc_arcpretid int(10) unsigned NOT NULL default '0',			
			resarc_id_empr int(10) unsigned NOT NULL default '0',
			resarc_empr_cp varchar(10) NOT NULL default '',
			resarc_empr_ville varchar(255) NOT NULL default '',
			resarc_empr_prof varchar(255) NOT NULL default '',
			resarc_empr_year int(4) unsigned default '0',
			resarc_empr_categ smallint(5) unsigned default '0',
			resarc_empr_codestat smallint(5) unsigned default '0',
			resarc_empr_sexe tinyint(3) unsigned default '0',
			resarc_empr_location int(6) unsigned NOT NULL default '1',
			resarc_expl_nb int(5) unsigned default '0',
			resarc_expl_typdoc int(5) unsigned default '0',
			resarc_expl_cote varchar(255) NOT NULL default '',
			resarc_expl_statut smallint(5) unsigned default '0',
			resarc_expl_location smallint(5) unsigned default '0',
			resarc_expl_codestat smallint(5) unsigned default '0',
			resarc_expl_owner mediumint(8) unsigned default '0',
			resarc_expl_section int(5) unsigned NOT NULL default '0',	
			PRIMARY KEY(resarc_id),			
			KEY i_pa_idempr (resarc_id_empr),
			KEY i_pa_notice (resarc_idnotice),
			KEY i_pa_bulletin (resarc_idbulletin),
			KEY i_pa_resarc_date (resarc_date)
		) ";
		echo traite_rqt($rqt,"CREATE TABLE resa_archive");
		
		@set_time_limit(0);
		$rqt = "ALTER TABLE empr_custom_values DROP INDEX i_ecv_st " ;
		echo traite_rqt($rqt,"DROP INDEX i_ecv_st");
		$rqt = "ALTER TABLE empr_custom_values ADD INDEX i_ecv_st(empr_custom_small_text)" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_custom_values ADD INDEX i_ecv_st");
		
		$rqt = "ALTER TABLE empr_custom_values DROP INDEX i_ecv_t " ;
		echo traite_rqt($rqt,"DROP INDEX i_ecv_t");
		$rqt = "ALTER TABLE empr_custom_values ADD INDEX i_ecv_t(empr_custom_text(255))" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_custom_values ADD INDEX i_ecv_t");
		
		$rqt = "ALTER TABLE empr_custom_values DROP INDEX i_ecv_i " ;
		echo traite_rqt($rqt,"DROP INDEX i_ecv_i");
		$rqt = "ALTER TABLE empr_custom_values ADD INDEX i_ecv_i(empr_custom_integer)" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_custom_values ADD INDEX i_ecv_i");

		$rqt = "ALTER TABLE empr_custom_values DROP INDEX i_ecv_d " ;
		echo traite_rqt($rqt,"DROP INDEX i_ecv_d");
		$rqt = "ALTER TABLE empr_custom_values ADD INDEX i_ecv_d(empr_custom_date)" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_custom_values ADD INDEX i_ecv_d");
		
		$rqt = "ALTER TABLE empr_custom_values DROP INDEX i_ecv_f " ;
		echo traite_rqt($rqt,"DROP INDEX i_ecv_f");
		$rqt = "ALTER TABLE empr_custom_values ADD INDEX i_ecv_f(empr_custom_float)" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_custom_values ADD INDEX i_ecv_f");
		
		$rqt = "ALTER TABLE empr_custom_lists DROP INDEX champ_list_value  " ;
		echo traite_rqt($rqt,"DROP INDEX champ_list_value ");
		$rqt = "ALTER TABLE empr_custom_lists DROP INDEX i_ecl_lv  " ;
		echo traite_rqt($rqt,"DROP INDEX i_ecl_lv ");
		$rqt = "ALTER TABLE empr_custom_lists ADD INDEX i_ecl_lv(empr_custom_list_value)" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_custom_lists ADD INDEX i_ecl_lv");
		

		$rqt = "ALTER TABLE collstate_custom_values DROP INDEX i_ccv_st " ;
		echo traite_rqt($rqt,"DROP INDEX i_ccv_st");
		$rqt = "ALTER TABLE collstate_custom_values ADD INDEX i_ccv_st(collstate_custom_small_text)" ;
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom_values ADD INDEX i_ccv_st");
		
		$rqt = "ALTER TABLE collstate_custom_values DROP INDEX i_ccv_t " ;
		echo traite_rqt($rqt,"DROP INDEX i_ccv_t");
		$rqt = "ALTER TABLE collstate_custom_values ADD INDEX i_ccv_t(collstate_custom_text(255))" ;
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom_values ADD INDEX i_ccv_t");
		
		$rqt = "ALTER TABLE collstate_custom_values DROP INDEX i_ccv_i " ;
		echo traite_rqt($rqt,"DROP INDEX i_ccv_i");
		$rqt = "ALTER TABLE collstate_custom_values ADD INDEX i_ccv_i(collstate_custom_integer)" ;
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom_values ADD INDEX i_ccv_i");

		$rqt = "ALTER TABLE collstate_custom_values DROP INDEX i_ccv_d " ;
		echo traite_rqt($rqt,"DROP INDEX i_ccv_d");
		$rqt = "ALTER TABLE collstate_custom_values ADD INDEX i_ccv_d(collstate_custom_date)" ;
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom_values ADD INDEX i_ccv_d");
		
		$rqt = "ALTER TABLE collstate_custom_values DROP INDEX i_ccv_f " ;
		echo traite_rqt($rqt,"DROP INDEX i_vcv_f");
		$rqt = "ALTER TABLE collstate_custom_values ADD INDEX i_ccv_f(collstate_custom_float)" ;
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom_values ADD INDEX i_ccv_f");
		
		$rqt = "ALTER TABLE collstate_custom_lists DROP INDEX collstate_champ_list_value  " ;
		echo traite_rqt($rqt,"DROP INDEX collstate_champ_list_value ");
		$rqt = "ALTER TABLE collstate_custom_lists DROP INDEX i_ccl_lv  " ;
		echo traite_rqt($rqt,"DROP INDEX i_ccl_lv ");
		$rqt = "ALTER TABLE collstate_custom_lists ADD INDEX i_ccl_lv(collstate_custom_list_value)" ;
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom_lists ADD INDEX i_ccl_lv");
		

		$rqt = "ALTER TABLE expl_custom_values DROP INDEX i_excv_st " ;
		echo traite_rqt($rqt,"DROP INDEX i_excv_st");
		$rqt = "ALTER TABLE expl_custom_values ADD INDEX i_excv_st(expl_custom_small_text)" ;
		echo traite_rqt($rqt,"ALTER TABLE expl_custom_values ADD INDEX i_excv_st");
		
		$rqt = "ALTER TABLE expl_custom_values DROP INDEX i_excv_t " ;
		echo traite_rqt($rqt,"DROP INDEX i_excv_t");
		$rqt = "ALTER TABLE expl_custom_values ADD INDEX i_excv_t(expl_custom_text(255))" ;
		echo traite_rqt($rqt,"ALTER TABLE expl_custom_values ADD INDEX i_excv_t");
		
		$rqt = "ALTER TABLE expl_custom_values DROP INDEX i_excv_i " ;
		echo traite_rqt($rqt,"DROP INDEX i_excv_i");
		$rqt = "ALTER TABLE expl_custom_values ADD INDEX i_excv_i(expl_custom_integer)" ;
		echo traite_rqt($rqt,"ALTER TABLE expl_custom_values ADD INDEX i_excv_i");

		$rqt = "ALTER TABLE expl_custom_values DROP INDEX i_excv_d " ;
		echo traite_rqt($rqt,"DROP INDEX i_excv_d");
		$rqt = "ALTER TABLE expl_custom_values ADD INDEX i_excv_d(expl_custom_date)" ;
		echo traite_rqt($rqt,"ALTER TABLE expl_custom_values ADD INDEX i_excv_d");
		
		$rqt = "ALTER TABLE expl_custom_values DROP INDEX i_excv_f " ;
		echo traite_rqt($rqt,"DROP INDEX i_excv_f");
		$rqt = "ALTER TABLE expl_custom_values ADD INDEX i_excv_f(expl_custom_float)" ;
		echo traite_rqt($rqt,"ALTER TABLE expl_custom_values ADD INDEX i_excv_f");
		
		$rqt = "ALTER TABLE expl_custom_lists DROP INDEX expl_champ_list_value  " ;
		echo traite_rqt($rqt,"DROP INDEX expl_champ_list_value ");
		$rqt = "ALTER TABLE expl_custom_lists DROP INDEX i_excl_lv " ;
		echo traite_rqt($rqt,"DROP INDEX i_excl_lv");
		$rqt = "ALTER TABLE expl_custom_lists ADD INDEX i_excl_lv(expl_custom_list_value)" ;
		echo traite_rqt($rqt,"ALTER TABLE expl_custom_lists ADD INDEX i_evcl_lv");
		

		$rqt = "ALTER TABLE notices_custom_values DROP INDEX i_ncv_st " ;
		echo traite_rqt($rqt,"DROP INDEX i_ncv_st");
		$rqt = "ALTER TABLE notices_custom_values ADD INDEX i_ncv_st(notices_custom_small_text)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices_custom_values ADD INDEX i_ncv_st");
		
		$rqt = "ALTER TABLE notices_custom_values DROP INDEX i_ncv_t " ;
		echo traite_rqt($rqt,"DROP INDEX i_ncv_t");
		$rqt = "ALTER TABLE notices_custom_values ADD INDEX i_ncv_t(notices_custom_text(255))" ;
		echo traite_rqt($rqt,"ALTER TABLE notices_custom_values ADD INDEX i_ncv_t");
		
		$rqt = "ALTER TABLE notices_custom_values DROP INDEX i_ncv_i " ;
		echo traite_rqt($rqt,"DROP INDEX i_ncv_i");
		$rqt = "ALTER TABLE notices_custom_values ADD INDEX i_ncv_i(notices_custom_integer)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices_custom_values ADD INDEX i_ncv_i");

		$rqt = "ALTER TABLE notices_custom_values DROP INDEX i_ncv_d " ;
		echo traite_rqt($rqt,"DROP INDEX i_ncv_d");
		$rqt = "ALTER TABLE notices_custom_values ADD INDEX i_ncv_d(notices_custom_date)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices_custom_values ADD INDEX i_ncv_d");
		
		$rqt = "ALTER TABLE notices_custom_values DROP INDEX i_ncv_f " ;
		echo traite_rqt($rqt,"DROP INDEX i_ncv_f");
		$rqt = "ALTER TABLE notices_custom_values ADD INDEX i_ncv_f(notices_custom_float)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices_custom_values ADD INDEX i_ncv_f");
		
		$rqt = "ALTER TABLE notices_custom_lists DROP INDEX noti_champ_list_value  " ;
		echo traite_rqt($rqt,"DROP INDEX noti_champ_list_value ");
		$rqt = "ALTER TABLE notices_custom_lists DROP INDEX i_ncl_lv" ;
		echo traite_rqt($rqt,"DROP INDEX i_ncl_lv");
		$rqt = "ALTER TABLE notices_custom_lists ADD INDEX i_ncl_lv(notices_custom_list_value)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices_custom_lists ADD INDEX i_ncl_lv");
			
	     //Modification de la taille des champs tit1 à tit4
        $rqt="alter table notices change tit1 tit1 text";
        echo traite_rqt($rqt,"alter table notices change tit1 format to text");
        $rqt="alter table notices change tit2 tit2 text"; 
        echo traite_rqt($rqt,"alter table notices change tit2 format to text");
        $rqt="alter table notices change tit3 tit3 text"; 
        echo traite_rqt($rqt,"alter table notices change tit3 format to text");
        $rqt="alter table notices change tit4 tit4 text";  
        echo traite_rqt($rqt,"alter table notices change tit4 format to text");
			
		//Documents numériques des suggestions
		$rqt = " CREATE TABLE explnum_doc_sugg(
			num_explnum_doc int(10) NOT NULL default 0,
			num_suggestion int(10) NOT NULL default 0,
			PRIMARY KEY(num_explnum_doc,num_suggestion)
		)";
		echo traite_rqt($rqt,"CREATE TABLE explnum_doc_sugg") ;
		
		$rqt = "select id_explnum_doc, num_doc from explnum_doc where type_doc='sug'";
		$res = pmb_mysql_query($rqt);
		if($res){
			while($explnum_sug = pmb_mysql_fetch_object($res)){
				$req = "insert into explnum_doc_sugg set num_explnum_doc='".$explnum_sug->id_explnum_doc."', num_suggestion='".$explnum_sug->num_doc."'";
				pmb_mysql_query($rqt);
			}
		}
		echo traite_rqt("select 1","insert into explnum_doc_sugg");
		
		$rqt = "ALTER TABLE explnum_doc DROP num_doc";
		echo traite_rqt($rqt,"ALTER TABLE explnum_doc DROP num_doc") ;
	
		$rqt = "ALTER TABLE explnum_doc DROP type_doc";
		echo traite_rqt($rqt,"ALTER TABLE explnum_doc DROP type_doc") ;
		
		$rqt = "ALTER TABLE users ADD param_rfid_activate INT(1) NOT NULL default '1' AFTER param_sounds" ;		
		echo traite_rqt($rqt,"ALTER TABLE users ADD param_rfid_activate");
		
		
		//Module des demandes 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'demandes', 'active', '0', 'Module demandes activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert demandes_active=0 into parameters");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='demandes_statut_notice' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'demandes', 'statut_notice', '0', 'Id du statut de notice pour la notice de demandes.', '',0) ";
			echo traite_rqt($rqt, "insert demandes_statut_notice=0 into parameters");
		}
	
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'demandes_active', '0', 'Activer les demandes pour l\'OPAC.\n 0 : Non.\n 1 : Oui.', 'a_general',0) ";
			echo traite_rqt($rqt, "insert opac_demandes_active=0 into parameters");
		}
		
		$rqt = "CREATE TABLE demandes(
				id_demande int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				num_demandeur mediumint(8) NOT NULL default 0,
				theme_demande int(3) not null default 0,
				type_demande int(3) not null default 0,
				etat_demande int(3) not null default 0,
				date_demande DATE NOT NULL DEFAULT '0000-000-00',
				date_prevue DATE NOT NULL DEFAULT '0000-000-00',	
				deadline_demande DATE NOT NULL DEFAULT '0000-000-00',
				titre_demande varchar(255) NOT NULL default '',				
				sujet_demande text NOT NULL DEFAULT '',
				progression mediumint(3) NOT NULL default 0,
				num_user_cloture mediumint(3) NOT NULL default 0,
				num_notice int(10) not null default 0,
				PRIMARY KEY  (id_demande),
				KEY i_num_demandeur(num_demandeur),
				KEY i_date_demande(date_demande),
				KEY i_deadline_demande(deadline_demande)
				)";
		echo traite_rqt($rqt,"CREATE TABLE demandes") ;
	
		$rqt = "CREATE TABLE demandes_actions(
				id_action int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				type_action int(3) NOT NULL default 0,
				statut_action int(3) NOT NULL default 0,
				sujet_action varchar(255) not null default '',
				detail_action text NOT NULL DEFAULT '',
				date_action DATE NOT NULL DEFAULT '0000-000-00',
				deadline_action DATE NOT NULL DEFAULT '0000-000-00',				
				temps_passe mediumint(8) NOT NULL DEFAULT 0,
				cout mediumint(3) NOT NULL default 0,
				progression_action mediumint(3) NOT NULL default 0,
				prive_action int(1) not null default 0,
				num_demande	int(10) not null default 0,
				PRIMARY KEY  (id_action),
				KEY i_date_action(date_action),
				KEY i_deadline_action(deadline_action),
				KEY i_num_demande(num_demande)
				)";
		echo traite_rqt($rqt,"CREATE TABLE demandes_actions") ;
	
		$rqt = "CREATE TABLE demandes_notes(
				id_note int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				prive int(1) NOT NULL default 0,
				rapport int(1) NOT NULL default 0,
				contenu text NOT NULL default '',
				date_note DATE NOT NULL DEFAULT '0000-000-00',
				num_action	int(10) not null default 0,
				num_note_parent	int(10) not null default 0,
				PRIMARY KEY  (id_note),
				KEY i_date_note(date_note),
				KEY i_num_action(num_action),
				KEY i_num_note_parent(num_note_parent)
				)";
		echo traite_rqt($rqt,"CREATE TABLE demandes_notes") ;
	
		$rqt = " CREATE TABLE demandes_theme(
			id_theme int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			libelle_theme varchar(255) NOT NULL default '',
	        PRIMARY KEY  (id_theme)
			)";
		echo traite_rqt($rqt,"CREATE TABLE demandes_theme") ;
		
	
		$rqt = " CREATE TABLE demandes_type(
			id_type int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			libelle_type varchar(255) NOT NULL default '',
	        PRIMARY KEY  (id_type)
			)";
		echo traite_rqt($rqt,"CREATE TABLE demandes_type") ;
		
	
		$rqt = " CREATE TABLE demandes_users(
			num_user int(10) not null default 0,
			num_demande int(10) not null default 0,
			date_creation date not null default '0000-00-00',
			users_statut int(1) not null default 0,
			PRIMARY KEY (num_user,num_demande)
			)";
		echo traite_rqt($rqt,"CREATE TABLE demandes_users") ;
		
		$rqt = " CREATE TABLE explnum_doc_actions(
			num_explnum_doc int(10) NOT NULL default 0,
			num_action int(10) NOT NULL default 0,
			prive int(1) NOT NULL default 0,
			rapport int(1) NOT NULL default 0,
			num_explnum int(10) NOT NULL default 0,
			PRIMARY KEY(num_explnum_doc,num_action)
			)";
		echo traite_rqt($rqt,"CREATE TABLE explnum_doc_actions") ;
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.78");
//		break;

//	case "v4.78": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		$rqt = "CREATE TABLE rapport_demandes(
			id_item int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			contenu text not null default '',
			num_note int(10) not null default 0,
			num_demande int(10) not null default 0,
			ordre mediumint(3)  not null default 0,
			type mediumint(2) not null default 0,
			PRIMARY KEY(id_item))";
		echo traite_rqt($rqt,"CREATE TABLE rapport_demandes") ;
		
		$rqt="ALTER TABLE empr_statut ADD allow_dema TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT 1 AFTER allow_sugg" ;
		echo traite_rqt($rqt,"ALTER TABLE empr_statut ADD allow_dema") ;
		
		// paramètre de gestion des titres uniformes
		// on initialise à 1 si $pmb_form_editables est à 1
		$pmb_use_uniform_title=$pmb_form_editables;
		$resnbtu=pmb_mysql_query("SELECT * FROM titres_uniformes");
		if (pmb_mysql_num_rows($resnbtu)) $pmb_use_uniform_title=1;
		$resnbgrilles=pmb_mysql_query("SELECT * FROM grilles");
		if (pmb_mysql_num_rows($resnbgrilles)) $pmb_use_uniform_title=1;
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='use_uniform_title' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'use_uniform_title', '".$pmb_use_uniform_title."', 'Utiliser les titres uniformes ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_use_uniform_title=$pmb_use_uniform_title into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_expl_default' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'print_expl_default', '0', 'En impression de panier, imprimer les exemplaires est coché par défaut \n 0 : Non \n 1 : Oui', 'a_general', 0)";
			echo traite_rqt($rqt,"insert opac_print_expl_default='0' into parametres");
		}
		
		//Paramètres d'inclusion auto des notes dans le rapport
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='include_note' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'demandes', 'include_note', '0', 'Inclure automatiquement les notes dans le rapport documentaire.', '',0) ";
			echo traite_rqt($rqt, "insert demandes_include_note=0 into parameters");
		}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.79");
//		break;

//	case "v4.79": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		//	Ajout d'un champ notice unimarc dans les suggestions	
		$rqt="ALTER TABLE suggestions ADD notice_unimarc BLOB NOT NULL DEFAULT ''";
	 	echo traite_rqt($rqt,"alter suggestions add notice_unimarc") ;
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.80");
//		break;

//	case "v4.80": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='ie_reload_on_resize' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'ie_reload_on_resize', '0', 'Recharger la page si l\'utilisateur redimensionne son navigateur (pb de CSS avec IE) ? \n 0: Non \n 1: Oui','a_general')";
			echo traite_rqt($rqt,"insert opac_ie_reload_on_resize=0 into parametres");
		}
		
		// Permet de mémoriser les exemplaires non traités lors d'un retour de prêt.(transfert et résa)
		$rqt="ALTER TABLE exemplaires ADD expl_retloc smallint(5) UNSIGNED NOT NULL DEFAULT 0 " ;
		echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD expl_retloc") ;
		
		//Modification du type du champ explnum_data de explnum_doc
		$rqt="ALTER TABLE explnum_doc CHANGE explnum_doc_data explnum_doc_data mediumblob NOT NULL DEFAULT '' " ;
		echo traite_rqt($rqt,"ALTER TABLE explnum_doc CHANGE explnum_doc_data mediumblob") ;
	 	
 		//Parametre affichage des dates de creation et modification exemplaires
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_show_dates' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'expl_show_dates', '0', 'Afficher les dates de création et de modification des exemplaires ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert expl_show_dates=0 into parameters");
		}

		//parametres valeurs par defaut en modification de notice pour la gestion des droits d'acces utilisateurs - notices 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='user_notice_def' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'gestion_acces', 'user_notice_def', '0', 'Valeur par défaut en modification de notice pour les droits d\'accès utilisateurs - notices \n 0 : Recalculer.\n 1 : Choisir.', '',0) ";
			echo traite_rqt($rqt, "insert gestion_acces_user_notice_def=0 into parameters");
		}

		//parametres valeur par defaut en modification de notice pour la gestion des droits d'acces emprunteurs - notices 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_notice_def' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
				VALUES (0, 'gestion_acces', 'empr_notice_def', '0', 'Valeur par défaut en modification de notice pour les droits d\'accès emprunteurs - notices \n 0 : Recalculer.\n 1 : Choisir.', '',0) ";
			echo traite_rqt($rqt, "insert gestion_acces_empr_notice_def=0 into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_exemplaires_analysis' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'show_exemplaires_analysis', '0', 'Afficher les exemplaires du bulletin sous l\'article affiché ? \n 0: Non \n 1: Oui','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_show_exemplaires_analysis=0 into parametres");
		}
		
		//paramètres pour afficher l'id de la notice dans le detail de la notice
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_notice_id' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'show_notice_id', '0', 'Afficher l\'identifiant de la notice dans le descriptif ? \n 0 : Non.\n 1 : Oui. \n 1,X : Oui avec préfixe X', '',0) ";
			echo traite_rqt($rqt, "insert pmb_show_notice_id=0 into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='section_notices_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'section_notices_order', ' index_serie, tnvol, index_sew ', 'Ordre d\'affichage des notices dans les sections dans l\'opac \n  index_serie, tnvol, index_sew : tri par titre de série et titre ','k_section')";
			echo traite_rqt($rqt,"insert opac_section_notices_order=' index_serie, tnvol, index_sew ' into parametres");
		}
		
		//Parametre pour l'affichage d'un onglet aide et d'un lien dans la barre de navigation
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_onglet_help' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) 
					VALUES (0, 'opac', 'show_onglet_help', '0', 'Afficher l\'onglet HELP avec les onglets de recherche affichant l\'infopage et un lien vers l\'infopage dans la barre de navigation \n 0 : Non.\n ## : id de l\'infopage. \n','f_modules')";
			echo traite_rqt($rqt,"insert opac_show_onglet_help='0' into parametres");
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='navig_empr' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'navig_empr', '0', 'Afficher l\'onglet \"Votre compte\" dans la barre de navigation de l\'Opac ? \n 0 : Non \n 1 : Oui', '', '0')";
			echo traite_rqt($rqt,"insert opac_navig_empr='0' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='confirm_resa' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
			  		VALUES (NULL, 'opac', 'confirm_resa', '0', 'Demander la confirmation sur la réservation d\'un exemplaire en Opac ? \n 0 : Non \n 1 : Oui', '', '0')";
			echo traite_rqt($rqt,"insert opac_confirm_resa='0' into parametres ");
		}
		
		//Ajout d'une colonne générique dans statopac et logopac
		$rqt="ALTER TABLE logopac ADD gen_stat BLOB NOT NULL DEFAULT '' " ;
		echo traite_rqt($rqt,"ALTER TABLE logopac ADD gen_stat") ;
		
		$rqt="ALTER TABLE statopac ADD gen_stat BLOB NOT NULL DEFAULT '' " ;
		echo traite_rqt($rqt,"ALTER TABLE statopac ADD gen_stat") ;
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.81");
//		break;

//	case "v4.81": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+
		
		//Index connecteurs extérieurs
		$rqt="ALTER TABLE es_cache_int drop index cache_index";
		echo traite_rqt($rqt,"ALTER TABLE es_cache_int drop index cache_index") ;
		$rqt="alter table es_cache_int add index cache_index(es_cache_owner,es_cache_objectformat,es_cache_objecttype); " ;
		echo traite_rqt($rqt,"alter table es_cache_int add index cache_index") ;
		
		//Création de la cote en ajax
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='prefill_cote_ajax' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'prefill_cote_ajax', '', 'Script personnalisé de construction de la cote de l\'exemplaire en ajax')";
			echo traite_rqt($rqt,"insert pmb_prefill_cote_ajax='' into parametres");
		}
		
		//Masquer les infos de localisation dans l'entête
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='hide_biblioinfo_letter' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'hide_biblioinfo_letter', '0', 'Masquer les informations de localisation dans l\'entête des lettres (pour les bibliothèques possédant du papier à entête)')";
			echo traite_rqt($rqt,"insert pmb_hide_biblioinfo_letter=0 into parametres");
		}
		
		//Code lecteur + email en position absolue
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='lettres_code_mail_position_absolue' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'lettres_code_mail_position_absolue', '0 100 6', 'Placer le code lecteur et le mail selon des coordonnées absolues.\n activé x y \n activé : activer cette fonction (valeurs: 0/1) \n x : Position horizontale \n y : Position verticale')";
			echo traite_rqt($rqt,"insert pmb_lettres_code_mail_position_absolue='0 100 6' into parametres");
		}
		
		//Ajout d'un statut d'emprunteur pour les listes de lecture
		$rqt = "ALTER TABLE empr_statut ADD allow_liste_lecture TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"alter table empr_statut add allow_liste_lecture=0 ");
		
		//Modification de la valeur du paramètre pour la taille du bloc d'exemplaire pour les lettres de résa (conséquence de l'ajout d'infos d'exemplaire)
		$rqt = "UPDATE parametres set valeur_param=20 where type_param='pdflettreresa' and sstype_param='taille_bloc_expl' and valeur_param='16' ";
		echo traite_rqt($rqt,"UPDATE parametres set valeur_param=20 where type_param='pdflettreresa' and sstype_param='taille_bloc_expl'");
		
		//Paramètres pour définir un statut permettant de restreindre les droits d'origine d'un emprunteur dont l'abonnement est dépassé
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='adhesion_expired_status' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'opac', 'adhesion_expired_status','0','Id du statut permettant de restreindre les droits des emprunteurs dont l\'abonnement est dépassé. \n\rPMB fera un AND logique avec les droits d\'origine.','a_general')";
			echo traite_rqt($rqt,"insert opac_adhesion_expired_status=0 into parametres");
		}
		
		//On réaffecte les paramètres mal classés de l'OPAC à la bonne section		
		$rqt="UPDATE parametres set section_param='a_general' where type_param='opac' and sstype_param='navig_empr'";
		echo traite_rqt($rqt,"UPDATE parametres set section_param='a_general' where type_param='opac' and sstype_param='navig_empr' ");
		$rqt="UPDATE parametres set section_param='a_general' where type_param='opac' and sstype_param='confirm_resa'";
		echo traite_rqt($rqt,"UPDATE parametres set section_param='a_general' where type_param='opac' and sstype_param='confirm_resa' ");
		$rqt="UPDATE parametres set section_param='a_general' where type_param='opac' and sstype_param='adhesion_expired_status'";
		echo traite_rqt($rqt,"UPDATE parametres set section_param='a_general' where type_param='opac' and sstype_param='adhesion_expired_status' ");
		
				
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.82");
//		break;

//	case "v4.82": 
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		// on initialise à 1 si $pmb_form_editables est à 1
		if ($pmb_form_editables) {
			$rqt = "update parametres set valeur_param='1' where type_param='pmb' and sstype_param='use_uniform_title' ";
			echo traite_rqt($rqt, "update pmb_use_uniform_title=1 if pmb_use_uniform_title=1 ");
		}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.83");
//		break;

//	case "v4.83":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		//Paramètres pour définir l'action par défaut à effectuer lors d'un retour si il y a demande de résa
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_retour_action_defaut' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'pmb', 'resa_retour_action_defaut','0','Définit l\'action par défaut à effectuer lors d\'un retour si le document est réservé.\n0, Valider la réservation.\n1, A traiter plus tard.','')";
			echo traite_rqt($rqt,"insert pmb_resa_retour_action_defaut=0 into parametres");
		}
		
		//Paramètres pour définir le format d'affichage des notices filles
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_fille_format' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'pmb', 'notice_fille_format','0','Affichage des notices filles \n 0: avec leurs détails (notice dépliable avec un plus) \n 1: Juste l\'entête','')";
			echo traite_rqt($rqt,"insert pmb_notice_fille_format=0 into parametres");
		}
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.84");
//		break;

//	case "v4.84":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		// +-------------------------------------------------+

		//Ajout des champs d'origine pour les actions et les notes
		$rqt = "ALTER TABLE demandes_actions ADD actions_num_user TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"alter table demandes_actions add actions_num_user default 0 ");
		$rqt = "ALTER TABLE demandes_actions ADD actions_type_user TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"alter table demandes_actions add actions_type_user default 0 ");

		$rqt = "ALTER TABLE demandes_notes ADD notes_num_user TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"alter table demandes_notes add notes_num_user default 0 ");
		$rqt = "ALTER TABLE demandes_notes ADD notes_type_user TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"alter table demandes_notes add notes_type_user default 0 ");
		
		$rqt="alter table demandes_actions drop index i_actions_user" ;
		echo traite_rqt($rqt,"alter table demandes_actions drop index i_actions_user") ;
		$rqt="alter table demandes_actions add index i_actions_user(actions_num_user,actions_type_user) " ;
		echo traite_rqt($rqt,"alter table demandes_actions add index i_actions_user") ;
		
		$rqt="alter table demandes_notes drop index i_notes_user " ;
		echo traite_rqt($rqt,"alter table demandes_notes drop index i_notes_user") ;
		$rqt="alter table demandes_notes add index i_notes_user(notes_num_user,notes_type_user) " ;
		echo traite_rqt($rqt,"alter table demandes_notes add index i_notes_user") ;
		
		$rqt = "ALTER TABLE demandes_actions MODIFY temps_passe FLOAT ";
		echo traite_rqt($rqt,"alter table demandes_actions MODIFY temps_passe FLOAT ");
		
		$rqt = "ALTER TABLE demandes_actions ADD actions_read int(1) not null default 0 ";
		echo traite_rqt($rqt,"alter table demandes_actions ADD actions_read ");
		
		$rqt = "ALTER TABLE explnum_doc ADD explnum_doc_url TEXT not null default '' ";
		echo traite_rqt($rqt,"ALTER TABLE explnum_doc ADD explnum_doc_url ");
		
		$rqt = "alter table users change speci_coordonnees_etab speci_coordonnees_etab mediumtext not null default '' ";
		echo traite_rqt($rqt,"alter table users change speci_coordonnees_etab default '' ");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.85");
//		break;


//	case "v4.85":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		
		//Ajout du champ resa_trans pour associer un transfert à une résa
		$rqt = "ALTER TABLE transferts_demande ADD resa_trans int(8) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table transferts_demande add resa_trans ");	
		
		$rqt = "ALTER TABLE suggestions_origine DROP PRIMARY KEY, ADD PRIMARY KEY(origine,num_suggestion,type_origine)";
		echo traite_rqt($rqt,"ALTER TABLE suggestions_origine DROP PRIMARY KEY, ADD PRIMARY KEY(origine,num_suggestion,type_origine)");
		
		//Masquer le message d'erreur en retour de prêt d'un document issu d'une autre localisation
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='hide_retdoc_loc_error' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'hide_retdoc_loc_error', '0', 'Masquer le message d\'erreur en retour de prêt d\'un document issu d\'une autre localisation')";
			echo traite_rqt($rqt,"insert pmb_hide_retdoc_loc_error=0 into parametres");
		}

		$rqt = "alter table pret_archive drop index i_pa_arc_empr_categ";
		echo traite_rqt($rqt,"alter table pret_archive drop index i_pa_arc_empr_categ");
		$rqt = "alter table pret_archive add index i_pa_arc_empr_categ(arc_empr_categ)";
		echo traite_rqt($rqt,"alter table pret_archive add index i_pa_arc_empr_categ");

		$rqt = "alter table pret_archive drop index i_pa_arc_expl_location";
		echo traite_rqt($rqt,"alter table pret_archive drop index i_pa_arc_expl_location");
		$rqt = "alter table pret_archive add index i_pa_arc_expl_location(arc_expl_location)";
		echo traite_rqt($rqt,"alter table pret_archive add index i_pa_arc_expl_location");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.86");
//		break;

//	case "v4.86":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+

		//Activation de la gestion de borne de prêt
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='selfservice_allow' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'pmb', 'selfservice_allow', '0', 'Activer de la gestion de la borne de prêt?\n0 : Non. \n1 : Oui.')";
			echo traite_rqt($rqt,"insert pmb_selfservice_allow=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='loc_autre_todo' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'loc_autre_todo', '0', '1', 'Action à effectuer si le document est issu d\'une autre localisation') ";
			echo traite_rqt($rqt,"INSERT selfservice_loc_autre_todo INTO parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='loc_autre_todo_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'loc_autre_todo_msg', '', '1', 'Message si le document est réservé sur une autre localisation') ";
			echo traite_rqt($rqt,"INSERT selfservice_loc_autre_todo_msg INTO parametres") ;
		}		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='resa_ici_todo' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'resa_ici_todo', '0', '1', 'Action à effectuer si le document est réservé sur cette localisation') ";
			echo traite_rqt($rqt,"INSERT selfservice_resa_ici_todo INTO parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='resa_ici_todo_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'resa_ici_todo_msg', '', '1', 'Message si le document est réservé sur cette localisation') ";
			echo traite_rqt($rqt,"INSERT selfservice_resa_ici_todo_msg INTO parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='resa_loc_todo' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'resa_loc_todo', '0', '1', 'Action à effectuer si le document est réservé sur une autre localisation') ";
			echo traite_rqt($rqt,"INSERT selfservice_resa_loc_todo INTO parametres") ;
		}		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='resa_loc_todo_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'resa_loc_todo_msg', '', '1', 'Message si le document est réservé sur une autre localisation') ";
			echo traite_rqt($rqt,"INSERT selfservice_resa_loc_todo_msg INTO parametres") ;
		}		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='retour_retard_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'retour_retard_msg', '', '1', 'Message si le document est rendu en retard') ";
			echo traite_rqt($rqt,"INSERT selfservice_retour_retard_msg INTO parametres") ;
		}		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='retour_blocage_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'retour_blocage_msg', '', '1', 'Message si le document est rendu en retard avec blocage') ";
			echo traite_rqt($rqt,"INSERT selfservice_retour_blocage_msg INTO parametres") ;
		}		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='retour_amende_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'retour_amende_msg', '', '1', 'Message si le document est rendu en retard avec amende') ";
			echo traite_rqt($rqt,"INSERT selfservice_retour_amende_msg INTO parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_carte_invalide_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_carte_invalide_msg', 'Votre carte n\'est pas valide !', '1', 'Message borne de prêt: Votre carte n\'est pas valide !') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_carte_invalide_msg INTO parametres") ;
		}		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_pret_interdit_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_pret_interdit_msg', 'Vous n\'êtes pas autorisé à emprunter !', '1', 'Message borne de prêt: Vous n\'êtes pas autorisé à emprunter !') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_pret_interdit_msg INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_deja_prete_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_deja_prete_msg', 'Document déjà prêté ! allez le signaler !', '1', 'Message borne de prêt: Document déjà prêté ! allez le signaler !') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_deja_prete_msg INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_deja_prete_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_deja_prete_msg', 'Document déjà prêté ! allez le signaler !', '1', 'Message borne de prêt: Document déjà prêté ! allez le signaler !') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_deja_prete_msg INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_deja_reserve_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_deja_reserve_msg', 'Vous ne pouvez pas emprunter ce document', '1', 'Message borne de prêt: Vous ne pouvez pas emprunter ce document') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_deja_reserve_msg INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_quota_bloc_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_quota_bloc_msg', 'Vous ne pouvez pas emprunter ce document', '1', 'Message borne de prêt: Vous ne pouvez pas emprunter ce document') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_quota_bloc_msg INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_non_pretable_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_non_pretable_msg', 'Ce document n\'est pas prêtable', '1', 'Message borne de prêt: Ce document n\'est pas prêtable') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_non_pretable_msg INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_expl_inconnu_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_expl_inconnu_msg', 'Ce document est inconnu', '1', 'Message borne de prêt: Ce document est inconnu') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_expl_inconnu_msg INTO parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'selfservice' and sstype_param='pret_prolonge_non_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'selfservice', 'pret_prolonge_non_msg', 'Le prêt ne peut être prolongé', '1', 'Message borne de prêt: Le prêt ne peut être prolongé') ";
			echo traite_rqt($rqt,"INSERT selfservice_pret_prolonge_non_msg INTO parametres") ;
		}
		
		//Paramètres pour afficher les résultats en mode phototèque
		//on supprime cette mise à jour, on la vire dans la version 4.88, la visionneuse la remplace....
		//		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='photo_result_to_phototheque' "))==0) {
		//			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) 
		//				VALUES (0,'opac', 'photo_result_to_phototheque','0','Afficher le résultat d\'une recherche (liste des documents numériques associés aux notices résultats) en mode photothèque','m_photo')";
		//			echo traite_rqt($rqt,"insert opac_photo_result_to_phototheque=0 into parametres");
		//		}
		//Paramètres pour filtrer le type de documents à afficher en mode phototèque
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='photo_filtre_mimetype' "))==0) {
			$rqt = "INSERT INTO parametres (id_param, type_param,sstype_param, valeur_param, comment_param, section_param) 
				VALUES (0,'opac', 'photo_filtre_mimetype','','Liste des mimetypes utilisés pour l\'affichage des résultats en mode photothèque séparés par une virgule et entre cotes (ex:\'application/pdf\',\'image/png\')','m_photo')";
			echo traite_rqt($rqt,"insert opac_photo_filtre_mimetype='' into parametres");
		}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.89");
//		break;


//	case "v4.90":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
	
		$rqt = "alter table bannette_contenu drop KEY i_num_notice ";
		echo traite_rqt($rqt,"drop index bannette_contenu i_num_notice ") ; 
		$rqt = "alter table bannette_contenu add KEY i_num_notice (num_notice) ";
		echo traite_rqt($rqt,"create index bannette_contenu i_num_notice ") ; 

		$rqt = "alter table es_cache_blob drop index cache_index ";
		echo traite_rqt($rqt,"alter table es_cache_blob drop index cache_index ") ; 
		$rqt = "alter table es_cache_blob add index cache_index (es_cache_owner,es_cache_objectformat,es_cache_objecttype) ";
		echo traite_rqt($rqt,"alter table es_cache_blob add index cache_index ") ; 
		
		// Gestion sms
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sms_activation' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'empr', 'sms_activation', '0', 'Activation de l\'envoi de sms. \n 0: Inactif \n 1: Actif')";
			echo traite_rqt($rqt,"insert empr_sms_activation='0' into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sms_config' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'empr', 'sms_config', '', 'Paramétrage de l\'envoi de sms. \nUsage:\n class_name=nom_de_la_classe;param_connection;\nExemple:\n class_name=smstrend;login=xxxx@sigb.net;password=xxxx;tpoa=xxxx;')";
			echo traite_rqt($rqt,"insert empr_sms_config='' into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sms_msg_resa_dispo' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'empr', 'sms_msg_resa_dispo', 'Bonjour,\nUn document réservé est disponible.\nConsultez votre compte!', 'Texte du sms envoyé lors de la validation d\'une réservation')";
			echo traite_rqt($rqt,"insert empr_sms_msg_resa_dispo into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sms_msg_resa_suppr' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'empr', 'sms_msg_resa_suppr', 'Bonjour,\nUne réservation est supprimée.\nConsultez votre compte!', 'Texte du sms envoyé lors de la suppression d\'une réservation')";
			echo traite_rqt($rqt,"insert empr_sms_msg_resa_suppr into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sms_msg_retard' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'empr', 'sms_msg_retard', 'Bonjour,\nVous avez un ou plusieurs document(s) en retard.\nConsultez votre compte!', 'Texte du sms envoyé lors de la suppression d\'une réservation')";
			echo traite_rqt($rqt,"insert empr_sms_msg_retard into parametres");
		}		
		$rqt = "ALTER TABLE empr ADD empr_sms INT(1) unsigned NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr ADD empr_sms ") ;

		//Création des tables pour la gestion de l'historique des relances
		$rqt = "CREATE TABLE log_retard(
			id_log INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			date_log TIMESTAMP NOT NULL,
			niveau_reel INT(1) NOT NULL default 0,
			niveau_suppose INT(1) NOT NULL default 0,
			amende_totale decimal(16,2) NOT NULL default 0,	
			frais decimal(16,2) NOT NULL default 0,
			idempr INT(11) NOT NULL default 0 )";
		echo traite_rqt($rqt,"CREATE TABLE log_retard ") ; 
		$rqt = "CREATE TABLE log_expl_retard(
			id_log INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			date_log TIMESTAMP NOT NULL ,
			titre VARCHAR(255) NOT NULL default '',
			expl_id INT(11) NOT NULL default 0,
			expl_cb VARCHAR(255) NOT NULL default '',
			date_pret date NOT NULL default '0000-00-00',
			date_retour date NOT NULL default '0000-00-00',
			amende decimal(16,2) NOT NULL default 0,	
			num_log_retard INT(11) NOT NULL default 0 )";
		echo traite_rqt($rqt,"CREATE TABLE log_expl_retard ") ;
	
		//Client du serveur de procédures externes:
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='procedure_server_address' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'pmb', 'procedure_server_address', '', 'Adresse du serveur de procédures distances.')";
			echo traite_rqt($rqt,"insert procedure_server_address='' into parametres");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='procedure_server_credentials' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'pmb', 'procedure_server_credentials', '', 'Autentification sur le serveur de procédures distantes.\n1ère ligne: email\n2ème ligne: mot de passe.')";
			echo traite_rqt($rqt,"insert procedure_server_credentials='' into parametres");
		}

 		$rqt = "ALTER TABLE docsloc_section ADD num_pclass int(10) not null default 0";
		echo traite_rqt($rqt,"alter table docsloc_section ADD num_pclass ");
		$requete="SELECT id_pclass FROM pclassement";
		$res=pmb_mysql_query($requete,$dbh);
		if(pmb_mysql_num_rows($res) == 1) {
			$requete="UPDATE docsloc_section SET num_pclass='".pmb_mysql_result($res,0,0)."' WHERE num_pclass='0'";
			pmb_mysql_query($requete,$dbh);
		} elseif (!$thesaurus_classement_mode_pmb) {
			$requete="UPDATE docsloc_section SET num_pclass='".$thesaurus_classement_defaut."' WHERE num_pclass='0'";
			pmb_mysql_query($requete,$dbh);
		}
		
		$rqt = " CREATE TABLE explnum_location(
			num_explnum int(10) NOT NULL default 0,
			num_location int(10) NOT NULL default 0,
			PRIMARY KEY(num_explnum,num_location)
			)";
		echo traite_rqt($rqt,"CREATE TABLE explnum_location") ;
		
		// Ajout d'un 2eme mode de prêt RFID
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='rfid_pret_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) 
					VALUES (0, 'pmb', 'rfid_pret_mode', '0', 'Mode de fonctionnement du prêt:\n 0: Un document retiré de la platine est retiré du prêt.\n 1: Un document retiré de la platine est conservé pour faciliter le prêt de nombreux documents. ')";
			echo traite_rqt($rqt,"insert pmb_rfid_pret_mode into parametres");
		}	

		// Création des liens entre les autorités
		$rqt = "CREATE TABLE aut_link(
			aut_link_from INT( 2 ) NOT NULL default 0 ,
			aut_link_from_num INT( 11 ) NOT NULL default 0 ,
			aut_link_to INT( 2 ) NOT NULL default 0 ,
			aut_link_to_num INT( 11 ) NOT NULL default 0 ,
			aut_link_type INT(2) NOT NULL default 0,
			aut_link_reciproc INT(1) NOT NULL default 0,
			aut_link_comment VARCHAR(255) NOT NULL default '',
			PRIMARY KEY(aut_link_from, aut_link_from_num, aut_link_to, aut_link_to_num, aut_link_type) )";
		echo traite_rqt($rqt,"CREATE TABLE aut_link "); 
	
		//Module fiches 
		$rqt = "update parametres set type_param='fiches' where type_param='fichier' and sstype_param='active' ";
		echo traite_rqt($rqt, "update fiches_active into parameters (previous error)");
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'fiches' and sstype_param='active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'fiches', 'active', '0', 'Module \'fiches\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert fiches_active=0 into parameters");
		}
		$rqt = "CREATE TABLE fiche(
			id_fiche int(10) unsigned NOT NULL auto_increment, 
			infos_global text NOT NULL,
			index_infos_global text NOT NULL,
			PRIMARY KEY (id_fiche)
		)";
		echo traite_rqt($rqt,"create table fiche ");
		
		//Création des champs persos de l'onglet fichier
		$rqt = "CREATE TABLE gestfic0_custom (
			idchamp int(10) unsigned NOT NULL auto_increment, 
			name varchar(255) NOT NULL default '', 
			titre varchar(255) default NULL, 
			type varchar(10) NOT NULL default 'text', 			
			datatype varchar(10) NOT NULL default '', 			
			options text, multiple int(11) NOT NULL default '0', 
			obligatoire int(11) NOT NULL default '0', 
			ordre int(11) default NULL, 
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table gestfic0_custom ");
		$rqt = "CREATE TABLE gestfic0_custom_lists (
			gestfic0_custom_champ int(10) unsigned NOT NULL default '0',
			gestfic0_custom_list_value varchar(255) default NULL, 
			gestfic0_custom_list_lib varchar(255) default NULL, 
			ordre int(11) default NULL, 
			KEY gestfic0_custom_champ (gestfic0_custom_champ), 
			KEY gestfic0_champ_list_value (gestfic0_custom_champ,gestfic0_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table gestfic0_custom_lists ");
		$rqt = "CREATE TABLE gestfic0_custom_values (
			gestfic0_custom_champ int(10) unsigned NOT NULL default '0', 
			gestfic0_custom_origine int(10) unsigned NOT NULL default '0', 
			gestfic0_custom_small_text varchar(255) default NULL, 
			gestfic0_custom_text text, 
			gestfic0_custom_integer int(11) default NULL, 
			gestfic0_custom_date date default NULL, 
			gestfic0_custom_float float default NULL, 
			KEY gestfic0_custom_champ (gestfic0_custom_champ), 
			KEY gestfic0_custom_origine (gestfic0_custom_origine)) " ;
		echo traite_rqt($rqt,"create table gestfic0_custom_values ");
		
		//Module Visionneuse
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_allow' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'visionneuse_allow', '0', 'Visionneuse activée.\n 0 : Non.\n 1 : Oui.', 'm_photo',0) ";
			echo traite_rqt($rqt, "insert visionneuse_allows=0 into parameters");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='photo_result_to_phototheque' "))){
			$rqt = "DELETE FROM parametres WHERE type_param= 'opac' and sstype_param='photo_result_to_phototheque' ";
			echo traite_rqt($rqt, "delete phototheque from parameters");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_params' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'visionneuse_params', '', 'tableau de correspondance mimetype=>class','m_photo',1) ";
			echo traite_rqt($rqt, "insert visionneuse_params into parameters");
		}

	 	//suppression parametres obsoletes opac_authors_aut_sort_records & opac_authors_rec_per_page
		$rqt = "delete from parametres where type_param='opac' and sstype_param='authors_aut_sort_records' " ;
		echo traite_rqt($rqt,"delete opac_authors_aut_sort_records from parametres") ;
		$rqt = "delete from parametres where type_param='opac' and sstype_param='opac_authors_rec_per_page' " ;
		echo traite_rqt($rqt,"delete opac_authors_rec_per_page from parametres") ;
		//correction libelle parametre pmb_resa_retour_action_defaut
		$rqt = "update parametres set comment_param='Définit l\'action par défaut à effectuer lors d\'un retour si le document est réservé.\n0, A traiter plus tard.\n1, Valider la réservation.' where type_param='pmb' and sstype_param='resa_retour_action_defaut' ";
		echo traite_rqt($rqt,"update parametre pmb_resa_retour_action_defaut");
		//correction libelle parametre opac_avis_nb_max
		$rqt = "update parametres set comment_param='Nombre maximal de commentaires conservés par notice. Les plus vieux sont effacés au profit des plus récents quand ce nombre est atteint.' where type_param='opac' and sstype_param='avis_nb_max' ";
		echo traite_rqt($rqt,"update parametre opac_avis_nb_max");
		//correction libelle parametre opac_modules_search_abstract
		$rqt = "update parametres set comment_param='Recherche simple dans le champ résumé :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_abstract' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_abstract");
		//correction libelle parametre opac_modules_search_all
		$rqt = "update parametres set comment_param='Recherche simple dans l\'ensemble des champs :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_all' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_all");	 
		//correction libelle parametre opac_modules_search_author
		$rqt = "update parametres set comment_param='Recherche simple dans les auteurs :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_author' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_author");
		//correction libelle parametre opac_modules_search_category
		$rqt = "update parametres set comment_param='Recherche simple dans les catégories :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_category' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_category");
		//correction libelle parametre opac_modules_search_collection
		$rqt = "update parametres set comment_param='Recherche simple dans les collections :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_collection' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_collection");
		//correction libelle parametre opac_modules_search_indexint
		$rqt = "update parametres set comment_param='Recherche simple dans les indexations décimales :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_indexint' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_indexint");
		//correction libelle parametre opac_modules_search_keywords
		$rqt = "update parametres set comment_param='Recherche simple dans les indexations libres (mots-clés) :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_keywords' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_keywords");
		//correction libelle parametre opac_modules_search_publisher
		$rqt = "update parametres set comment_param='Recherche simple dans les éditeurs :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_publisher' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_publisher");	  
		//correction libelle parametre opac_modules_search_subcollection
		$rqt = "update parametres set comment_param='Recherche simple dans les sous-collections :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_subcollection' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_subcollection");
		//correction libelle parametre opac_modules_search_title
		$rqt = "update parametres set comment_param='Recherche simple dans les titres :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_title' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_title");
		//correction libelle parametre opac_modules_search_titre_uniforme
		$rqt = "update parametres set comment_param='Recherche simple dans les titres uniformes :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères' where type_param='opac' and sstype_param='modules_search_titre_uniforme' ";
		echo traite_rqt($rqt,"update parametre opac_modules_search_titre_uniforme");
		
		//nouvelle table pour l'enregistrement des paramètres spécifiques à une classe d'affichage
		$rqt = "CREATE TABLE visionneuse_params (
			visionneuse_params_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			visionneuse_params_class VARCHAR( 255 ) NOT NULL DEFAULT '',
			visionneuse_params_parameters TEXT NOT NULL ,
			UNIQUE (
				visionneuse_params_class
			)
		)";
		echo traite_rqt($rqt,"create table visionneuse_params");
			
		$rqt = "ALTER TABLE procs ADD proc_notice_tpl int(2) unsigned  NOT NULL DEFAULT 0 ";		
		echo traite_rqt($rqt, "ALTER TABLE procs ADD proc_notice_tpl ");
		$rqt = "ALTER TABLE procs ADD proc_notice_tpl_field VARCHAR(255) NOT NULL default '' ";		
		echo traite_rqt($rqt, "ALTER TABLE procs ADD proc_notice_tpl_field ");
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_self_checkout' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
					VALUES(0,'opac','allow_self_checkout','0','Proposer de faire du prêt autonome dans l\'OPAC.\n 0 : Non.\n 1 : Autorise le prêt de document.\n 2 : Autorise le retour de document.\n 3 : Autorise le prêt et le retour de document.\n','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_allow_self_checkout into parametres") ;
		}
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='self_checkout_url_connector' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) 
					VALUES(0,'opac','self_checkout_url_connector','','URL du connecteur en gestion permettant d\'effectuer le prêt autonome.','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_self_checkout_url_connector into parametres") ;
		}
		
		$rqt = "ALTER TABLE empr_statut ADD allow_self_checkout TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"alter table empr_statut add allow_self_checkout=0 ");
		$rqt = "ALTER TABLE empr_statut ADD allow_self_checkin TINYINT(4) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"alter table empr_statut add allow_self_checkin=0 ");
		
		//Ajout du recouvr_type (0: amende, 1: prix de l'exemplaire)
		$rqt = "ALTER TABLE recouvrements ADD recouvr_type int(2) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table recouvrements add recouvr_type ");
		
		///Ajout de date_pret 
		$rqt = "ALTER TABLE recouvrements ADD date_pret datetime NOT NULL default '0000-00-00 00:00:00'";
		echo traite_rqt($rqt,"alter table recouvrements add date_pret ");
		//Ajout de date_relance1 
		$rqt = "ALTER TABLE recouvrements ADD date_relance1  datetime NOT NULL default '0000-00-00 00:00:00'";
		echo traite_rqt($rqt,"alter table recouvrements add date_relance1 ");
		//Ajout de date_relance2 
		$rqt = "ALTER TABLE recouvrements ADD date_relance2 datetime NOT NULL default '0000-00-00 00:00:00'";
		echo traite_rqt($rqt,"alter table recouvrements add date_relance2 ");
		//Ajout de date_relance3 
		$rqt = "ALTER TABLE recouvrements ADD date_relance3  datetime NOT NULL default '0000-00-00 00:00:00'";
		echo traite_rqt($rqt,"alter table recouvrements add date_relance3 ");
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'finance' and sstype_param='recouvrement_lecteur_statut' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,gestion) 
					VALUES (0, 'finance', 'recouvrement_lecteur_statut', '0', 'Mémorise le statut que prennent les lecteurs lors du passage en recouvrememnt', 1)";
			echo traite_rqt($rqt,"insert finance_recouvrement_lecteur_statut into parametres");
		}	
		$rqt = "CREATE TABLE cache_amendes (
			id_empr int(10) unsigned NOT NULL default 0,
			cache_date date not null default '0000-00-00', 
			data_amendes blob NOT NULL DEFAULT '',	
			key id_empr(id_empr) )" ;
		echo traite_rqt($rqt,"create table cache_amendes ");
		
		$rqt = "ALTER TABLE log_retard ADD log_printed int(1) unsigned NOT NULL default 0";
		echo traite_rqt($rqt,"alter table log_retard add log_printed ");		
		
		$rqt = "ALTER TABLE log_retard ADD log_mail int(1) unsigned NOT NULL default 0";
		echo traite_rqt($rqt,"alter table log_retard add log_mail ");		
				
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'internal' and sstype_param='emptylogstatopac' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,gestion) 
					VALUES (0, 'internal', 'emptylogstatopac', '0', 'Paramètre interne, ne pas modifier\r\n =1 si vidage des logs en cours', 0)";
			echo traite_rqt($rqt,"insert internal_emptylogstatopac=0 into parametres");
		}	

		//Module fichier 
		$rqt = "update parametres set type_param='fiches' where type_param='fichier' and sstype_param='active' ";
		echo traite_rqt($rqt, "update fiches_active into parameters");
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'fiches' and sstype_param='active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'fiches', 'active', '0', 'Module \'fiches\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert fiches_active=0 into parameters");
		}
				
		$rqt = "ALTER TABLE notice_tpl ADD notpl_show_opac int(1) unsigned NOT NULL default 0";
		echo traite_rqt($rqt,"alter table notice_tpl add notpl_show_opac ");		
				
		// Recherche autopostage
		
		$rqt = "ALTER TABLE categories ADD path_word_categ TEXT NOT NULL ";
		echo traite_rqt($rqt,"alter table categories add path_word_categ ");
		$rqt = "ALTER TABLE categories ADD index_path_word_categ TEXT NOT NULL ";
		echo traite_rqt($rqt,"alter table categories add index_path_word_categ ");		
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_search' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'thesaurus', 'auto_postage_search', '0', 'Activer l\'indexation des catégories mères et filles pour la recherche de notices. \n 0 non, \n 1 oui', 'i_categories', 0)";
			echo traite_rqt($rqt,"insert thesaurus_auto_postage_search=0 into parametres");			
		}	
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_search_nb_descendant' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'thesaurus', 'auto_postage_search_nb_descendant', '0', 'Nombre de niveaux de recherche de notices dans les catégories filles. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)";
			echo traite_rqt($rqt,"insert thesaurus_auto_postage_search_nb_descendant=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_search_nb_montant' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'thesaurus', 'auto_postage_search_nb_montant', '0', 'Nombre de niveaux de recherche de notices dans les catégories mères. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)";
			echo traite_rqt($rqt,"insert thesaurus_auto_postage_search_nb_montant=0 into parametres");
		}								
		
		// Agrandir les champs d'indexation des documents numériques
		$rqt="ALTER TABLE explnum CHANGE explnum_index_sew explnum_index_sew MEDIUMTEXT NOT NULL";  
		echo traite_rqt($rqt,"ALTER TABLE explnum CHANGE explnum_index_sew explnum_index_sew MEDIUMTEXT");
		
		$rqt="ALTER TABLE explnum CHANGE explnum_index_wew explnum_index_wew MEDIUMTEXT NOT NULL";  
		echo traite_rqt($rqt,"ALTER TABLE explnum CHANGE explnum_index_wew explnum_index_wew MEDIUMTEXT");
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.91");
//		break;


//	case "v4.91":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		// Recherche autopostage
		
		$rqt = "ALTER TABLE categories ADD path_word_categ TEXT NOT NULL ";
		echo traite_rqt($rqt,"alter table categories add path_word_categ ");
		$rqt = "ALTER TABLE categories ADD index_path_word_categ TEXT NOT NULL ";
		echo traite_rqt($rqt,"alter table categories add index_path_word_categ ");		
		
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_search' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'thesaurus', 'auto_postage_search', '0', 'Activer l\'indexation des catégories mères et filles pour la recherche de notices. \n 0 non, \n 1 oui', 'i_categories', 0)";
			echo traite_rqt($rqt,"insert thesaurus_auto_postage_search=0 into parametres");			
		}	
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_search_nb_descendant' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'thesaurus', 'auto_postage_search_nb_descendant', '0', 'Nombre de niveaux de recherche de notices dans les catégories filles. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)";
			echo traite_rqt($rqt,"insert thesaurus_auto_postage_search_nb_descendant=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_postage_search_nb_montant' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'thesaurus', 'auto_postage_search_nb_montant', '0', 'Nombre de niveaux de recherche de notices dans les catégories mères. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0)";
			echo traite_rqt($rqt,"insert thesaurus_auto_postage_search_nb_montant=0 into parametres");
		}								
		
		// Agrandir les champs d'indexation des documents numériques
		$rqt="ALTER TABLE explnum CHANGE explnum_index_sew explnum_index_sew MEDIUMTEXT NOT NULL";  
		echo traite_rqt($rqt,"ALTER TABLE explnum CHANGE explnum_index_sew explnum_index_sew MEDIUMTEXT");
		
		$rqt="ALTER TABLE explnum CHANGE explnum_index_wew explnum_index_wew MEDIUMTEXT NOT NULL";  
		echo traite_rqt($rqt,"ALTER TABLE explnum CHANGE explnum_index_wew explnum_index_wew MEDIUMTEXT");
		
		//Ajout de la TVA dans les lignes d'acte
		$rqt = "ALTER TABLE lignes_actes ADD debit_tva SMALLINT(2) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"ALTER TABLE lignes_actes ADD debit_tva");		
 
		//Possibilité de saisir un montant négatif dans une facture
		$rqt = "ALTER TABLE lignes_actes CHANGE prix prix FLOAT( 8, 2 ) NOT NULL DEFAULT 0.00 ";
		echo traite_rqt($rqt,"ALTER TABLE lignes_actes CHANGE prix signed");
		
		$rqt = "ALTER TABLE collections ADD collection_comment TEXT NOT NULL "; 
		echo traite_rqt($rqt, "ALTER TABLE collections ADD collection_comment");		
		$rqt = "ALTER TABLE sub_collections ADD subcollection_comment TEXT NOT NULL "; 
		echo traite_rqt($rqt, "ALTER TABLE sub_collections ADD sub_collection_comment");
				
		//insertion d'un champ "charset" dans la table d'import
		$rqt="ALTER TABLE import_marc ADD encoding VARCHAR(50) NOT NULL default '' ";
		echo traite_rqt($rqt,"ALTER TABLE import_marc ADD encoding VARCHAR(50)");
		
		// navigation bulletins
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_bulletin_nav' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'opac', 'show_bulletin_nav', '0', 'Affichage d\'un navigateur dans les bulletins d\'un périodique. \n 0 non \n 1 oui','f_modules', 0)";
			echo traite_rqt($rqt,"insert opac_show_bulletin_nav=0 into parametres");
		}	
		// Jouer l'alerte sonore si le prêt et le retour se passe sans erreur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='play_pret_sound' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'play_pret_sound', '1', 'Jouer l\'alerte sonore si le prêt et le retour se passe sans erreur ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_play_pret_sound=1 into parameters");
		}
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.92");
//		break;


//	case "v4.92":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		$rqt="ALTER TABLE logopac drop INDEX lopac_date_log" ;
		echo traite_rqt($rqt,"ALTER TABLE logopac drop INDEX lopac_date_log") ;
		$rqt="ALTER TABLE statopac drop INDEX sopac_date_log" ;
		echo traite_rqt($rqt,"ALTER TABLE statopac drop INDEX sopac_date_log") ;
		
		
		$rqt="ALTER TABLE logopac ADD INDEX lopac_date_log(date_log)" ;
		echo traite_rqt($rqt,"ALTER TABLE logopac ADD index lopac_date_log") ;
		$rqt="ALTER TABLE statopac ADD INDEX sopac_date_log(date_log)" ;
		echo traite_rqt($rqt,"ALTER TABLE statopac ADD index sopac_date_log") ;
		
		// modification de l'explication de pmb_hide_retdoc_loc_error
		$rqt = "update parametres set comment_param='Gestion du retour de prêt d\'un document issu d\'une autre localisation:\n 0 : Rendu, sans message d\'erreur\n 1 : Non rendu, avec message d\'erreur\n 2 : Rendu, avec message d\'erreur' where type_param='pmb' and sstype_param='hide_retdoc_loc_error' ";
		echo traite_rqt($rqt,"update parametre pmb_hide_retdoc_loc_error");	 
		
		
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.93");
//		break;


//	case "v4.93":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		// modification de l'explication de pmb_hide_retdoc_loc_error
		$rqt = "update parametres set comment_param='Gestion du retour de prêt d\'un document issu d\'une autre localisation:\n 0 : Rendu, sans message d\'erreur\n 1 : Non rendu, avec message d\'erreur\n 2 : Rendu, avec message d\'erreur' where type_param='pmb' and sstype_param='hide_retdoc_loc_error' ";
		echo traite_rqt($rqt,"update parametre pmb_hide_retdoc_loc_error");	 
		
		//Modification commentaire parametre pmb_numero_exemplaire_auto 
		$rqt = "update parametres set comment_param='Autorise la numérotation automatique d\'exemplaire ? \n 0 : non\n 1 : Oui, pour monographies et bulletins\n 2 : Oui, pour monographies seules\n 3 : Oui, pour bulletins seuls' where type_param='pmb' and sstype_param='numero_exemplaire_auto' ";
		echo traite_rqt($rqt,"update parametre pmb_numero_exemplaire_auto ");	 
		
		//Augmentation de la taille des libelles de codes statistiques de lecteurs
		$rqt = "ALTER TABLE empr_codestat CHANGE libelle libelle VARCHAR(255) NOT NULL DEFAULT 'DEFAULT' ";
		echo traite_rqt($rqt,"alter table empr_codestat resize field libelle");	 
		
		//script de vérification de saisie d'une notice perso
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='catalog_verif_js' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'pmb', 'catalog_verif_js', '', 'Script de vérification de saisie de notice','', 0)";
			echo traite_rqt($rqt,"insert catalog_verif_js into parametres");
		}		
		//restrictions des recherches prédéfinies par catégories de lecteurs		
		$rqt = "create table if not exists search_persopac_empr_categ(
			id_categ_empr int not null default 0,
			id_search_persopac int not null default 0, 	
			index i_id_s_persopac(id_search_persopac),
			index i_id_categ_empr(id_categ_empr)
		)" ;
		echo traite_rqt($rqt,"create table search_persopac_empr_categ");	
				
		//tri sur une étagère...
		$rqt = "ALTER TABLE etagere ADD id_tri INT NOT NULL, ADD INDEX i_id_tri (id_tri )";
		echo traite_rqt($rqt,"alter table etagere add id_tri");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v4.94");
//		break;


//	case "v4.94":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		// CSS add on
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_style_addon' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion) 
					VALUES ( 'opac', 'default_style_addon', '', 'Ajout de styles CSS aux feuilles déjà incluses ?\n Ne mettre que le code CSS, exemple:  body {background-color: #FF0000;}','a_general', 0)";
			echo traite_rqt($rqt,"insert opac_default_style_addon into parametres");
		}	
		
		//assocation d'un répertoire d'upload à une source dans les connecteurs
		$rqt = "ALTER TABLE connectors_sources ADD rep_upload INT NOT NULL default 0";
		echo traite_rqt($rqt,"alter table connectors_sources add rep_upload");
		
		//ajout de l'indicateur dans les entrepots...
		$rqt = "select source_id from connectors_sources";
		$res = pmb_mysql_query($rqt);
		$rqt= array();
		if(pmb_mysql_num_rows($res)){
			while ($r= pmb_mysql_fetch_object($res)){
				pmb_mysql_query("alter table entrepot_source_".$r->source_id." add field_ind char(2) not null default '  ' after ufield");
			}
		}
		echo traite_rqt("select 1 ","alter table entrepot_source add field_ind");
			
		// rfid
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='rfid_gates_server_url' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param,valeur_param,comment_param, section_param, gestion) VALUES ('pmb','rfid_gates_server_url', '', 'URL du serveur des portiques RFID', '', '0')" ;
			echo traite_rqt($rqt,"insert pmb_rfid_gates_server_url='' into parametres");
		}
		// Upload des documents numériques lors de l'intégration de notice
		$rqt = "ALTER TABLE connectors_sources ADD upload_doc_num INT NOT NULL default 1";
		echo traite_rqt($rqt,"alter table connectors_sources add upload_doc_num");
		
		//Separateur de valeurs de champs perso 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='perso_sep' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'perso_sep', '/', 'Séparateur des valeurs de champ perso, espace ou ; ou , ou ...')";
			echo traite_rqt($rqt,"insert pmb_perso_sep='/' into parametres");
		}
	
		//Modification du commentaire du paramètre opac_notice_reduit_format
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4' where type_param='opac' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre opac_notice_reduit_format");
		
		$rqt = "update parametres set comment_param = 'Possibilité pour les lecteurs de créer ou modifier leurs bannettes privées\n 0: Non\n 1: Oui\n 2: Oui et le bouton de création s\'affiche en permanence en recherche multicritères' where type_param='opac' and sstype_param='allow_bannette_priv'";
		echo traite_rqt($rqt,"update parametre opac_allow_bannette_priv");
		
		//Modification du commentaire du paramètre pmb_notice_reduit_format
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN' where type_param='pmb' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre pmb_notice_reduit_format");
		
		//on conserve la référence de la source d'origine
		$rqt = "create table if not exists notices_externes(
			num_notice int not null default 0,
			recid varchar(255) not null default '',
			primary key(num_notice),
			index i_recid(recid),
			index i_notice_recid (num_notice, recid))" ;
		echo traite_rqt($rqt,"create table notices_externes");	
		
		$rqt="ALTER TABLE explnum drop INDEX i_f_explnumwew" ;
		echo traite_rqt($rqt,"ALTER TABLE explnum drop INDEX i_f_explnumwew") ;
		$rqt="ALTER TABLE explnum ADD FULLTEXT i_f_explnumwew (explnum_index_wew)" ;
		echo traite_rqt($rqt,"ALTER TABLE explnum ADD FULLTEXT i_f_explnumwew ") ;
	
		// Type de recherche sur documents numériques 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_full_text' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'search_full_text', '0', 'Utiliser un index MySQL FULLTEXT pour la recherche sur les documents numériques \n 0: Non \n 1: Oui')";
			echo traite_rqt($rqt,"insert pmb_search_full_text='0' into parametres");
		}
		
		// Restriction d'une infopage aux abonnés uniquement 
		$rqt = "alter table infopages add restrict_infopage int not null default 0";
		echo traite_rqt($rqt,"alter table infopages add restrict_infopage");
		
		// Parser HTML OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='parse_html' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'opac', 'parse_html', '0', 'Activer le parse HTML des pages OPAC \n 0: Non \n 1: Oui','a_general')";
			echo traite_rqt($rqt,"insert opac_parse_html='0' into parametres");
		}

		//on précise si une source peut enrichir ou non des notices
		$rqt = "ALTER TABLE connectors_sources ADD enrichment INT NOT NULL default 0";
		echo traite_rqt($rqt,"alter table connectors_sources add enrichment");
		
		//stockage des enrichissements de notices
		$rqt = "create table if not exists sources_enrichment(
			source_enrichment_num int not null default 0,
			source_enrichment_typnotice varchar(2) not null default '',
			source_enrichment_typdoc varchar(2) not null default '',
			source_enrichment_params text not null,
			primary key (source_enrichment_num, source_enrichment_typnotice, source_enrichment_typdoc),
			index i_s_enrichment_typnoti(source_enrichment_typnotice),
			index i_s_enrichment_typdoc(source_enrichment_typdoc))" ;
		echo traite_rqt($rqt,"create table sources_enrichment");	
		
		// Enrichissement OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notice_enrichment' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'opac', 'notice_enrichment', '0', 'Activer l\'enrichissement des notices\n 0: Non \n 1: Oui','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_notice_enrichment='0' into parametres");
		}	
		
		// Social Network
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_social_network' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'opac', 'show_social_network', '0', 'Activer les partages sur les réseaux sociaux \n 0: Non \n 1: Oui','e_aff_notice')";
			echo traite_rqt($rqt,"insert show_social_network='0' into parametres");
		}
		
		// valeur par défaut restrict infopages
		$rqt = "ALTER TABLE infopages CHANGE restrict_infopage restrict_infopage INT( 11 ) NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE infopages CHANGE restrict_infopage DEFAULT 0");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;

// Empieza la version 5
// +------------------------------------------------------------------------+
//	case "v5.00":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='opac_view_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'opac_view_activate', '0', 'Activer les vues OPAC:\n 0 : non activé \n 1 : activé', '', '0')";
			echo traite_rqt($rqt,"insert pmb_opac_view_activate='0' into parametres ");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='opac_view_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'opac_view_activate', '0', 'Activer les vues OPAC:\n 0 : non activé \n 1 : activé', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_opac_view_activate='0' into parametres ");
		}

		//Gestion des vues Opac
		$rqt = "CREATE TABLE if not exists opac_views (
			opac_view_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			opac_view_name VARCHAR( 255 ) NOT NULL default '',
			opac_view_query TEXT NOT NULL,
			opac_view_human_query TEXT NOT NULL,
			opac_view_param TEXT NOT NULL,
			opac_view_visible INT( 1 ) UNSIGNED NOT NULL default 0,
			opac_view_comment TEXT NOT NULL)";
		echo traite_rqt($rqt,"CREATE TABLE opac_views ") ;

		//Gestion des filtres de module ( pour vues Opac )
		$rqt = "CREATE TABLE if not exists opac_filters (
			opac_filter_view_num INT UNSIGNED NOT NULL default 0 ,
			opac_filter_path VARCHAR( 20 ) NOT NULL default '',
			opac_filter_param TEXT NOT NULL,
			PRIMARY KEY(opac_filter_view_num,opac_filter_path))";
		echo traite_rqt($rqt,"CREATE TABLE opac_filters ") ;

		//Gestion générique des subst de parametre ( pour vues Opac )
		$rqt = "CREATE TABLE if not exists param_subst (
			subst_module_param VARCHAR( 20 ) NOT NULL default '',
			subst_module_num INT( 2 ) UNSIGNED NOT NULL default 0,
			subst_type_param VARCHAR( 20 ) NOT NULL default '',
			subst_sstype_param VARCHAR( 255 ) NOT NULL default '',
			subst_valeur_param TEXT NOT NULL,
			subst_comment_param longtext NOT NULL,
			PRIMARY KEY(subst_module_param, subst_module_num, subst_type_param, subst_sstype_param))";
		echo traite_rqt($rqt,"CREATE TABLE param_subst ") ;

		$rqt = "CREATE TABLE if not exists opac_views_empr (
			emprview_view_num INT UNSIGNED NOT NULL default 0 ,
			emprview_empr_num INT UNSIGNED NOT NULL default 0 ,
		    emprview_default INT UNSIGNED NOT NULL default 0 ,
			PRIMARY KEY(emprview_view_num,emprview_empr_num))";
		echo traite_rqt($rqt,"CREATE TABLE opac_views_empr ") ;

		// Gestion des sur-localisations
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='sur_location_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'pmb', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activé \n 1 : activé', '', '0')";
			echo traite_rqt($rqt,"insert pmb_sur_location_activate='0' into parametres ");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='sur_location_activate' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activé \n 1 : activé', 'a_general', '0')";
			echo traite_rqt($rqt,"insert opac_sur_location_activate='0' into parametres ");
		}

		$rqt = "CREATE TABLE if not exists sur_location (
			surloc_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			surloc_libelle VARCHAR( 255 ) NOT NULL default '',
			surloc_pic VARCHAR( 255 ) NOT NULL default '',
			surloc_visible_opac tinyint( 1 ) UNSIGNED NOT NULL default 1,
			surloc_name VARCHAR( 255 ) NOT NULL default '',
			surloc_adr1 VARCHAR( 255 ) NOT NULL default '',
			surloc_adr2 VARCHAR( 255 ) NOT NULL default '',
			surloc_cp VARCHAR( 15 ) NOT NULL default '',
			surloc_town VARCHAR( 100 ) NOT NULL default '',
			surloc_state VARCHAR( 100 ) NOT NULL default '',
			surloc_country VARCHAR( 100 ) NOT NULL default '',
			surloc_phone VARCHAR( 100 ) NOT NULL default '',
			surloc_email VARCHAR( 100 ) NOT NULL default '',
			surloc_website VARCHAR( 100 ) NOT NULL default '',
			surloc_logo VARCHAR( 100 ) NOT NULL default '',
			surloc_comment TEXT NOT NULL,
			surloc_num_infopage INT( 6 ) UNSIGNED NOT NULL default 0,
			surloc_css_style VARCHAR( 100 ) NOT NULL default '')";
		echo traite_rqt($rqt,"CREATE TABLE sur_location ") ;

		$rqt = "ALTER TABLE docs_location ADD surloc_num INT NOT NULL default 0";
		echo traite_rqt($rqt,"alter table docs_location add surloc_num");

		$rqt = "ALTER TABLE docs_location ADD surloc_used tinyint( 1 ) NOT NULL default 0";
		echo traite_rqt($rqt,"alter table docs_location add surloc_used");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='opac_view_class' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'pmb', 'opac_view_class', '', 'Nom de la classe substituant la class opac_view pour la personnalisation de la gestion des vues Opac','')";
			echo traite_rqt($rqt,"insert pmb_opac_view_class='' into parametres");
		}
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v5.01");
//		break;

//	case "v5.01":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		// Favicon, reporté de la 4.94 - ER
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='faviconurl' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param) VALUES (0, 'opac', 'faviconurl', '', 'URL du favicon, si vide favicon=celui de PMB','a_general')";
			echo traite_rqt($rqt,"insert opac_faviconurl='' into parametres");
		}

		//on précise si une source est interrogée directement en ajax dans l'OPAC
		$rqt = "ALTER TABLE connectors_sources ADD opac_affiliate_search INT NOT NULL default 0";
		echo traite_rqt($rqt,"alter table connectors_sources add opac_affiliate_search");

		// Activation des recherches affiliées dans les sources externes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_affiliate_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_affiliate_search', '0', 'Activer les recherches affiliées en OPAC:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_allow_affiliate_search='0' into parametres ");
		}

		$rqt = "ALTER TABLE users CHANGE explr_invisible explr_invisible TEXT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_invisible explr_invisible TEXT NULL");
		$rqt = "ALTER TABLE users CHANGE explr_visible_mod explr_visible_mod TEXT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_visible_mod explr_visible_mod TEXT NULL");
		$rqt = "ALTER TABLE users CHANGE explr_visible_unmod explr_visible_unmod TEXT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE users CHANGE explr_visible_unmod explr_visible_unmod TEXT NULL");

		//ajout table statuts de lignes d'actes
		$rqt = "CREATE TABLE lignes_actes_statuts (
			id_statut INT(3) NOT NULL AUTO_INCREMENT,
			libelle TEXT NOT NULL,
			relance INT(3) NOT NULL DEFAULT 0,
			PRIMARY KEY (id_statut)
			)  ";
		echo traite_rqt($rqt,"create table lignes_actes_statuts");

		$rqt = "CREATE TABLE lignes_actes_relances (
			num_ligne INT UNSIGNED NOT NULL ,
			date_relance DATE NOT NULL default '0000-00-00',
			type_ligne int(3) unsigned NOT NULL DEFAULT 0,
			num_acte int(8) unsigned NOT NULL DEFAULT 0,
			lig_ref int(15) unsigned NOT NULL DEFAULT 0,
			num_acquisition int(12) unsigned NOT NULL DEFAULT 0,
			num_rubrique int(8) unsigned NOT NULL DEFAULT 0,
			num_produit int(8) unsigned NOT NULL DEFAULT 0,
			num_type int(8) unsigned NOT NULL DEFAULT 0,
			libelle text NOT NULL,
			code varchar(255) NOT NULL DEFAULT '',
			prix float(8,2) NOT NULL DEFAULT 0,
			tva float(8,2) unsigned NOT NULL DEFAULT 0,
			nb int(5) unsigned NOT NULL DEFAULT 1,
			date_ech date NOT NULL DEFAULT '0000-00-00',
			date_cre date NOT NULL DEFAULT '0000-00-00',
			statut int(3) unsigned NOT NULL DEFAULT 1,
			remise float(8,2) NOT NULL DEFAULT 0,
			index_ligne text NOT NULL,
			ligne_ordre smallint(2) unsigned NOT NULL DEFAULT 0,
			debit_tva smallint(2) unsigned NOT NULL DEFAULT 0,
			commentaires_gestion text NOT NULL,
			commentaires_opac text NOT NULL,
			PRIMARY KEY (num_ligne, date_relance)
			) ";
		echo traite_rqt($rqt,"create table lignes_actes_relances");

		//ajout d'un statut de lignes d'actes par défaut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from lignes_actes_statuts where id_statut='1' "))==0) {
			$rqt = "INSERT INTO lignes_actes_statuts (id_statut,libelle,relance) VALUES (1 ,'Traitement normal', '1') ";
			echo traite_rqt($rqt,"insert default lignes_actes_statuts");
		}

		//raz des statuts de lignes d'actes
		$rqt = "UPDATE lignes_actes set statut='1' ";
		echo traite_rqt($rqt,"alter lignes_actes raz statut");

		//ajout d'un statut de ligne d'acte par défaut par utilisateur pour les devis
		$rqt = "ALTER TABLE users ADD deflt3lgstatdev int(3) not null default 1 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default lg state dev");

		//ajout d'un statut de ligne d'acte par défaut par utilisateur pour les commandes
		$rqt = "ALTER TABLE users ADD deflt3lgstatcde int(3) not null default 1 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default lg state cde");

		//ajout d'un commentaire de gestion pour les lignes d'actes
		$rqt = "ALTER TABLE lignes_actes ADD commentaires_gestion TEXT NOT NULL";
		echo traite_rqt($rqt,"alter table lignes_actes add commentaires_gestion");

		//ajout d'un commentaire OPAC pour les lignes d'actes
		$rqt = "ALTER TABLE lignes_actes ADD commentaires_opac TEXT NOT NULL";
		echo traite_rqt($rqt,"alter table lignes_actes add commentaires_opac");

		//ajout d'un nom (pour les commandes)
		$rqt = "ALTER TABLE actes ADD nom_acte VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter table actes add nom_acte");

		//Paramètres de mise en page des relances d'acquisitions
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_format_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_format_page','210x297','Largeur x Hauteur de la page en mm','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_format_page into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_orient_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_orient_page','P','Orientation de la page: P=Portrait, L=Paysage','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_orient_page into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_marges_page' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_marges_page','10,20,10,10','Marges de page en mm : Haut,Bas,Droite,Gauche','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_marges_page into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_logo' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_logo','10,10,20,20','Position du logo: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_logo into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_raison' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_raison','35,10,100,10,16','Position Raison sociale: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_raison into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_date' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_date','170,10,0,6,8','Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_date into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_adr_rel' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_adr_rel','10,35,60,5,10','Position Adresse de relance: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_adr_rel into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_adr_fou' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_adr_fou','100,55,100,6,14','Position Adresse fournisseur: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_adr_fou into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_num_cli' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_num_cli','10,80,0,10,16','Position numéro de client: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_num_cli into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_num' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_num','10,0,10,16','Position numéro de commande/devis: Distance par rapport au bord gauche de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_num into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_size' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_size','10','Taille de la police texte','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_size into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_titre' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_titre','10,90,100,10,16','Position titre: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_titre into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_before' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_before','','Texte avant le tableau de relances','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_before into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_after' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_after','','Texte après le tableau de relances','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_after into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_tab_rel' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_tab_rel','5,10','Tableau de relances: Hauteur ligne,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_tab_rel into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_footer' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_footer','15,8','Position bas de page: Distance par rapport au bas de page, Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_footer into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pos_sign' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pos_sign','10,60,5,10','Position signature: Distance par rapport au bord gauche de la page, Largeur, Hauteur ligne,Taille police','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pos_sign into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_sign' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_sign','Le responsable de la bibliothèque.','Texte signature','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_sign into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_by_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_by_mail','1','Effectuer les relances par mail :\n 0 : non \n 1 : oui','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_by_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_text_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint un état des commandes en cours.\r\n\r\nMerci de nous préciser par retour vos délais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.','Texte du mail','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_text_mail into parametres") ;
		}

		//ajout bulletinage avec document numérique
		$rqt = "ALTER TABLE abts_abts ADD abt_numeric int(1) not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD abt_numeric ");

		//ajout dans les bannettes la possibilité de ne pas tenir compte du statut des notices
		$rqt = "ALTER TABLE bannettes ADD statut_not_account INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add statut_not_account");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_perio_browser' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','show_perio_browser','0','Affichage du navigateur de périodiques en page d\'accueil OPAC.\n 0 : Non.\n 1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_perio_browser into parametres") ;
		}

		// Gestion des relances des périodiques
		$rqt = "CREATE TABLE perio_relance (
			rel_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			rel_abt_num int(10) unsigned NOT NULL DEFAULT 0,
			rel_date_parution date NOT NULL default '0000-00-00',
			rel_libelle_numero varchar(255) default NULL,
			rel_comment_gestion TEXT NOT NULL,
			rel_comment_opac TEXT NOT NULL ,
			rel_nb int unsigned NOT NULL DEFAULT 0,
			rel_date date NOT NULL default '0000-00-00',
			PRIMARY KEY  (rel_id) ) ";
		echo traite_rqt($rqt,"create table perio_relance ");

		//relances d'acquisitions en pdf/rtf
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_pdfrtf' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_pdfrtf','0','Envoi des relances en :\n 0 : pdf\n 1 : rtf','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_pdfrtf into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_onglet_perio_a2z' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','show_onglet_perio_a2z','0','Activer l\'onglet du navigateur de périodiques en OPAC.\n 0 : Non.\n 1 : Oui.','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_show_onglet_perio_a2z into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_note_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','avis_note_display_mode','1','Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'étoiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'étoiles.','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_avis_note_display_mode into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','avis_display_mode','0','Mode d\'affichage des avis de notices.\n 0 : Visible en lien à coté de l\'onglet Public/ISBD de la notice.\n 1 : Visible dans la notice.','a_general',0)" ;
			echo traite_rqt($rqt,"insert opac_avis_display_mode into parametres") ;
		}

		$rqt = "ALTER TABLE avis ADD avis_rank INT UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"ALTER TABLE avis ADD avis_rank") ;

		//Module Gestionnaire de tâches
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='planificateur_allow' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'planificateur_allow', '0', 'Planificateur activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_planificateur_allow=0 into parameters");
		}

		$rqt = "CREATE TABLE taches_type (
				id_type_tache int(11) unsigned NOT NULL,
				parameters text NOT NULL,
				timeout int(11) NOT NULL default '5',
				histo_day int(11) NOT NULL default '7',
				histo_number int(11) NOT NULL default '3',
				PRIMARY KEY  (id_type_tache)
				)";
		echo traite_rqt($rqt, "CREATE TABLE taches_type ");

		// Création des tables nécessaires au gestionnaire de tâches
		$rqt="CREATE TABLE taches (
			id_tache int(11) unsigned auto_increment,
			num_planificateur int(11),
			start_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			end_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			status varchar(128),
			msg_statut blob,
			commande int(8) NOT NULL default 0,
			next_state int(8) NOT NULL default 0,
			msg_commande blob,
			indicat_progress int(3),
			rapport text,
			id_process int(8),
			primary key (id_tache));";
		echo traite_rqt($rqt,"CREATE TABLE taches ");

		$rqt="CREATE TABLE planificateur (
			id_planificateur int(11) unsigned auto_increment,
			num_type_tache int(11) NOT NULL,
			libelle_tache VARCHAR(255) NOT NULL,
			desc_tache VARCHAR(255),
			num_user int(11) NOT NULL,
			param text,
			statut tinyint(1) unsigned DEFAULT 0,
			rep_upload int(8),
			path_upload text,
			perio_heure varchar(28),
			perio_minute varchar(28) DEFAULT '01',
			perio_jour varchar(128),
			perio_mois varchar(128),
			calc_next_heure_deb varchar(28),
			calc_next_date_deb date,
			primary key (id_planificateur))";
		echo traite_rqt($rqt,"CREATE TABLE planificateur ");

		$rqt="CREATE TABLE taches_docnum (
			id_tache_docnum int(11) unsigned auto_increment,
			tache_docnum_nomfichier varchar(255) NOT NULL,
			tache_docnum_mimetype VARCHAR(255) NOT NULL,
			tache_docnum_data mediumblob NOT NULL,
			tache_docnum_extfichier varchar(20),
			tache_docnum_repertoire int(8),
			tache_docnum_path text NOT NULL,
			num_tache int(11) NOT NULL,
			primary key (id_tache_docnum))";
		echo traite_rqt($rqt,"CREATE TABLE taches_docnum ");

		//modification de la longueur du champ numero de la table actes
		$rqt = "ALTER TABLE actes MODIFY numero varchar(255) NOT NULL default '' ";
		echo traite_rqt($rqt,"alter table actes modify numero");

		//ajout d'un statut par défaut en réception pour les suggestions
		$rqt = "ALTER TABLE users ADD deflt3receptsugstat int(3) not null default 32 ";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default recept sug state");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfrel_obj_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfrel_obj_mail','Etat des en-cours','Objet du mail','pdfrel',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfrel_obj_mail into parametres") ;
		}

		//ajout de paramètres pour l'envoi de commandes par mail
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfcde_by_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfcde_by_mail','1','Effectuer les envois de commandes par mail :\n 0 : non \n 1 : oui','pdfcde',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcde_by_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfcde_obj_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfcde_obj_mail','Commande','Objet du mail','pdfcde',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcde_obj_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfcde_text_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfcde_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint une commande à traiter.\r\n\r\nMerci de nous confirmer par retour vos délais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.','Texte du mail','pdfcde',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcde_text_mail into parametres") ;
		}

		//ajout de paramètres pour l'envoi de devis par mail
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfdev_by_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfdev_by_mail','1','Effectuer les envois de demandes de devis par mail :\n 0 : non \n 1 : oui','pdfdev',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfdev_by_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfdev_obj_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfdev_obj_mail','Demande de devis','Objet du mail','pdfdev',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfdev_obj_mail into parametres") ;
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='pdfdev_text_mail' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'acquisition','pdfdev_text_mail','Bonjour, \r\n\r\nVous trouverez ci-joint une demande de devis.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.','Texte du mail','pdfdev',0)" ;
			echo traite_rqt($rqt,"insert acquisition_pdfcdev_text_mail into parametres") ;
		}

		// masquer la possibilité d'uploader les docnum en base
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='docnum_in_database_allow' "))==0){
			if (pmb_mysql_num_rows(pmb_mysql_query("select * from upload_repertoire "))==0) $upd_param_docnum_in_database_allow = 1;
			else $upd_param_docnum_in_database_allow=0;
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'docnum_in_database_allow', '$upd_param_docnum_in_database_allow', 'Autoriser le stockage de document numérique en base ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_docnum_in_database_allow=$upd_param_docnum_in_database_allow into parameters <br><b>SET this parameter to 1 to (re)allow file storage in database !</b>");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='recherche_ajax_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'recherche_ajax_mode', '1', 'Affichage accéléré des résultats de recherche: header uniquement, la suite est chargée lors du click sur le \"+\".\n 0: Inactif\n 1: Actif (par lot)\n 2: Actif (par notice)', 'c_recherche', '0')" ;
			echo traite_rqt($rqt,"insert opac_recherche_ajax_mode=1 into parametres") ;
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='avis_note_display_mode' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','avis_note_display_mode','1','Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'étoiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'étoiles.','',0)" ;
			echo traite_rqt($rqt,"insert pmb_avis_note_display_mode into parametres") ;
		}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v5.02");
//		break;

//	case "v5.02":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//Module CMS
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active', '0', 'Module \'Portail\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert cms_active=0 into parameters");
		}

		//langue d'indexation par défaut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_lang' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'indexation_lang', '', 'Choix de la langue d\'indexation par défaut. (ex : fr_FR,en_UK,...,ar), si vide c\'est la langue de l\'interface du catalogueur qui est utilisée.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_indexation_lang into parameters");
		}

		//ajout du champ permettant la pré-selection du connecteur en OPAC
		$rqt = "ALTER TABLE connectors_sources ADD opac_selected int(3) unsigned not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD opac_selected");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_show_location' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'websubscribe_show_location', '0', 'Afficher la possibilité pour le lecteur de choisir sa localisation lors de son inscription en ligne.\n 0: Non\n 1: Oui', 'f_modules', '0')" ;
			echo traite_rqt($rqt,"insert opac_websubscribe_show_location=0 into parametres") ;
		}

		// CMS PMB
		//rubriques
		$rqt="create table if not exists cms_sections(
			id_section int unsigned not null auto_increment primary key,
			section_title varchar(255) not null default '',
			section_resume text not null,
			section_logo mediumblob not null,
			section_publication_state varchar(50) not null,
			section_start_date datetime,
			section_end_date datetime,
			section_num_parent int not null default 0,
			index i_cms_section_title(section_title),
			index i_cms_section_publication_state(section_publication_state),
			index i_cms_section_num_parent(section_num_parent)
			)";
		echo traite_rqt($rqt, "create table cms_sections");

		$rqt = "create table if not exists cms_sections_descriptors(
			num_section int not null default 0,
			num_noeud int not null default 0,
			section_descriptor_order int not null default 0,
			primary key (num_section,num_noeud)
			)";
		echo traite_rqt($rqt, "create table cms_sections_descriptors");

		$rqt="create table if not exists cms_articles(
			id_article int unsigned not null auto_increment primary key,
			article_title varchar(255) not null default '',
			article_resume text not null,
			article_contenu text not null,
			article_logo mediumblob not null,
			article_publication_state varchar(50) not null default '',
			article_start_date datetime,
			article_end_date datetime,
			num_section int not null default 0,
			index i_cms_article_title(article_title),
			index i_cms_article_publication_state(article_publication_state),
			index i_cms_article_num_parent(num_section)
			)";
		echo traite_rqt($rqt, "create table cms_articles");

		$rqt = "create table if not exists cms_articles_descriptors(
			num_article int not null default 0,
			num_noeud int not null default 0,
			article_descriptor_order int not null default 0,
			primary key (num_article,num_noeud)
			)";
		echo traite_rqt($rqt, "create table cms_articles_descriptors");


		$rqt = "create table if not exists cms_editorial_publications_states(
			id_publication_state int unsigned not null auto_increment primary key,
			editorial_publication_state_label varchar(255) not null default '',
			editorial_publication_state_opac_show int(1) not null default 0,
			editorial_publication_state_auth_opac_show int(1) not null default 0
			)";
		echo traite_rqt($rqt, "create table cms_editorial_publications_states");

		$rqt="create table if not exists cms_build (
			id_build int unsigned not null auto_increment primary key,
			build_obj varchar(255) not null default '',
			build_parent varchar(255) not null default '',
			build_child_after varchar(255) not null default '',
			build_css text not null
			)";
		echo traite_rqt($rqt, "create table cms_build");

		//paramétrage de la pondération des champs persos...
		// dans le notices
		$rqt = "alter table notices_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table notices_custom add pond");
		//dans les exemplaires
		$rqt = "alter table expl_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table expl_custom add pond ");
		//dans les états des collections
		$rqt = "alter table collstate_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table collstate_custom add pond");
		//dans les lecteurs, pour rester homogène...
		$rqt = "alter table empr_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table empr_custom add pond");

		//tri sur les états des collections en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='collstate_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'collstate_order', 'archempla_libelle,collstate_cote','Ordre d\'affichage des états des collections, dans l\'ordre donné, séparé par des virgules : archempla_libelle,collstate_cote','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_collstate_order=archempla_libelle,collstate_cote into parametres");
		}

		//la pondération dans les fiches ne sert à rien mais pour rester homogène avec les autres champs persos...
		$rqt = "alter table gestfic0_custom add pond int not null default 100";
		echo traite_rqt($rqt,"alter table gestfic0_custom add pond");

		//AR new search !
		@set_time_limit(0);
		flush();
		$rqt = "truncate table notices_mots_global_index";
		echo traite_rqt($rqt,"truncate table notices_mots_global_index");

		//Changement du type de code_champ dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index change code_champ code_champ int(3) not null default 0";
		echo traite_rqt($rqt,"alter table notices_mots_global_index change code_champ");

		//ajout de code_ss_champ dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add code_ss_champ int(3) not null default 0 after code_champ";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add code_ss_champ");

		//ajout de pond dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add pond int(4) not null default 100";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add pond");

		//ajout de position dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add position int not null default 1";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add position");

		//ajout de lang dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add lang varchar(10) not null default ''";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add lang");

		//changement de clé primaire
		$rqt = "alter table notices_mots_global_index drop primary key, add primary key(id_notice,code_champ,code_ss_champ,mot)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index change primary key(id_notice,code_champ,code_ss_champ,mot");

		//index
		$rqt = "alter table notices_mots_global_index drop index i_mot";
		echo traite_rqt($rqt,"alter table notices_mots_global_index drop index i_mot");
		$rqt = "alter table notices_mots_global_index add index i_mot(mot)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_mot");

		$rqt = "alter table notices_mots_global_index drop index i_id_mot";
		echo traite_rqt($rqt,"alter table notices_mots_global_index drop index i_id_mot");
		$rqt = "alter table notices_mots_global_index add index i_id_mot(id_notice,mot)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_id_mot");

		//une nouvelle table pour les recherches exactes...
		$rqt="create table if not exists notices_fields_global_index (
			id_notice mediumint(8) not null default 0,
			code_champ int(3) not null default 0,
			code_ss_champ int(3) not null default 0,
			ordre int(4) not null default 0,
			value text not null,
			pond int(4) not null default 100,
			lang varchar(10) not null default '',
			primary key(id_notice,code_champ,code_ss_champ,ordre),
			index i_value(value(300)),
			index i_id_value(id_notice,value(300))
			)";
		echo traite_rqt($rqt, "create table notices_fields_global_index");

		$rqt = "create table if not exists search_cache (
			object_id varchar(255) not null default '',
			delete_on_date datetime not null default '0000-00-00 00:00:00',
			value mediumblob not null,
	 		PRIMARY KEY (object_id)
			)";
		echo traite_rqt($rqt, "create table search_cache");

		// ajout d'un paramètre de tri par défaut
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','default_sort','d_num_6,c_text_28','Tri par défaut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))','d_aff_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_default_sort into parametres") ;
		}
		flush();
		//AR /new search !

		//maj valeurs possibles pour empr_filter_rows
		$rqt = "update parametres set comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: catégorie\n s: statut\n g: groupe\n y: année de naissance\n cp: code postal\n cs : code statistique\n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='filter_rows' ";
		echo traite_rqt($rqt,"update empr_filter_rows into parametres");

		//Précision affichage amendes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='fine_precision' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, gestion) VALUES (0, 'pmb', 'fine_precision', '2', 'Nombre de décimales pour l\'affichage des amendes',1)";
			echo traite_rqt($rqt,"insert fine_precision=2 into parametres");
		}

		//Rafraichissement des vues opac
		$rqt = "alter table opac_views add opac_view_last_gen datetime default null";
		echo traite_rqt($rqt,"alter table opac_views add opac_view_last_gen");
		$rqt = "alter table opac_views add opac_view_ttl int not null default 86400";
		echo traite_rqt($rqt,"alter table opac_views add opac_view_ttl");

		// paramétrage du cache en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_cache_duration' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','search_cache_duration','600','Durée de validité (en secondes) du cache des recherches OPAC','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_search_cache_duration into parametres") ;
		}

		// ajout d'un paramètre utilisateur de statut par défaut en import (report de l'alter V4, modif tardive en 3.4)
		$rqt = "alter table users add deflt_integration_notice_statut int(6) not null default 1 after deflt_notice_statut";
		echo traite_rqt($rqt,"alter table users add deflt_integration_notice_statut");

		// Info de réindexation
		$rqt = " select 1 " ;
		echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v5.03");
//		break;

//	case "v5.03":
//	case "v5.04":
//	case "v5.05":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//Type de document par défaut en création de périodique
		$rqt = "ALTER TABLE users ADD xmlta_doctype_serial varchar(2) NOT NULL DEFAULT '' after xmlta_doctype";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_serial after xmlta_doctype");

		//Type de document par défaut en création de bulletin
		$rqt = "ALTER TABLE users ADD xmlta_doctype_bulletin varchar(2) NOT NULL DEFAULT '' after xmlta_doctype_serial";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_bulletin after xmlta_doctype_serial");

		//Type de document par défaut en création d'article
		$rqt = "ALTER TABLE users ADD xmlta_doctype_analysis varchar(2) NOT NULL DEFAULT '' after xmlta_doctype_bulletin";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default xmlta_doctype_analysis after xmlta_doctype_bulletin");

		// Mise à jour des valeurs en fonction du type de document par défaut en création de notice, si la valeur est vide !
		if ($res = pmb_mysql_query("select userid, xmlta_doctype,xmlta_doctype_serial,xmlta_doctype_bulletin,xmlta_doctype_analysis from users")){
			while ( $row = pmb_mysql_fetch_object($res)) {
				if ($row->xmlta_doctype_serial == '') pmb_mysql_query("update users set xmlta_doctype_serial='".$row->xmlta_doctype."' where userid=".$row->userid);
				if ($row->xmlta_doctype_bulletin == '') pmb_mysql_query("update users set xmlta_doctype_bulletin='".$row->xmlta_doctype."' where userid=".$row->userid);
				if ($row->xmlta_doctype_analysis == '') pmb_mysql_query("update users set xmlta_doctype_analysis='".$row->xmlta_doctype."' where userid=".$row->userid);
			}
		}

		// Ajout affichage a2z par localisation
		$rqt = "alter table docs_location add show_a2z int(1) unsigned not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE docs_location ADD show_a2z");

		// demande GM : index sur
		$rqt = "alter table pret drop index i_pret_arc_id";
		echo traite_rqt($rqt,"alter table pret drop index i_pret_arc_id");
		$rqt = "alter table pret add index i_pret_arc_id(pret_arc_id)";
		echo traite_rqt($rqt,"alter table pret add index i_pret_arc_id");

		$rqt = "CREATE TABLE if not exists facettes (
				id_facette int unsigned auto_increment,
				facette_name varchar(255) not null default '',
				facette_critere int(5) not null default 0,
				facette_ss_critere int(5) not null default 0,
				facette_nb_result int(2) not null default 0,
				facette_visible tinyint(1) not null default 0,
				facette_type_sort int(1) not null default 0,
				facette_order_sort int(1) not null default 0,
				primary key (id_facette))";
		echo traite_rqt($rqt,"CREATE TABLE facettes");

		// début circulation périodiques
		//ajout du champ expl_abt_num permettant de lier l'exemplaire a un abonnement de pério
		$rqt = "ALTER TABLE exemplaires ADD expl_abt_num int unsigned not null default 0 ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD expl_abt_num");

		$rqt="create table if not exists serialcirc (
			id_serialcirc int unsigned not null auto_increment primary key,
			num_serialcirc_abt int unsigned not null default 0,
			serialcirc_type int unsigned not null default 0,
			serialcirc_virtual int unsigned not null default 0,
			serialcirc_duration int unsigned not null default 0,
			serialcirc_checked int unsigned not null default 0,
			serialcirc_retard_mode int unsigned not null default 0,
			serialcirc_allow_resa int unsigned not null default 0,
			serialcirc_allow_copy int unsigned not null default 0,
			serialcirc_allow_send_ask int unsigned not null default 0,
			serialcirc_allow_subscription int unsigned not null default 0,
			serialcirc_duration_before_send int unsigned not null default 0,
			serialcirc_expl_statut_circ int unsigned not null default 0,
			serialcirc_expl_statut_circ_after int unsigned not null default 0,
			serialcirc_state int unsigned not null default 0
		)";
		echo traite_rqt($rqt, "create table serialcirc");

		$rqt="create table if not exists serialcirc_diff (
			id_serialcirc_diff int unsigned not null auto_increment primary key,
			num_serialcirc_diff_serialcirc int unsigned not null default 0,
			serialcirc_diff_empr_type int unsigned not null default 0,
			serialcirc_diff_type_diff int unsigned not null default 0,
			num_serialcirc_diff_empr int unsigned not null default 0,
			serialcirc_diff_group_name varchar(255) not null default '',
			serialcirc_diff_duration int unsigned not null default 0,
			serialcirc_diff_order int unsigned not null default 0
		)";
		echo traite_rqt($rqt, "create table serialcirc_diff");

		$rqt="create table if not exists serialcirc_group (
			id_serialcirc_group int unsigned not null auto_increment primary key,
			num_serialcirc_group_diff int unsigned not null default 0,
			num_serialcirc_group_empr int unsigned not null default 0,
			serialcirc_group_responsable int unsigned not null default 0,
			serialcirc_group_order int unsigned not null default 0
		)";
		echo traite_rqt($rqt, "create table serialcirc_group");

		$rqt="create table if not exists serialcirc_expl (
			id_serialcirc_expl int unsigned not null auto_increment primary key,
			num_serialcirc_expl_id int unsigned not null default 0,
			num_serialcirc_expl_serialcirc int unsigned not null default 0,
			serialcirc_expl_bulletine_date date NOT NULL default '0000-00-00',
			serialcirc_expl_state_circ int unsigned not null default 0,
			num_serialcirc_expl_serialcirc_diff int unsigned not null default 0,
			serialcirc_expl_ret_asked int unsigned not null default 0,
			serialcirc_expl_trans_asked int unsigned not null default 0,
			serialcirc_expl_trans_doc_asked int unsigned not null default 0,
			num_serialcirc_expl_current_empr int unsigned not null default 0,
			serialcirc_expl_start_date date NOT NULL default '0000-00-00'
		)";
		echo traite_rqt($rqt, "create table serialcirc_expl");

		$rqt="create table if not exists serialcirc_circ (
			id_serialcirc_circ int unsigned not null auto_increment primary key,
			num_serialcirc_circ_diff int unsigned not null default 0,
			num_serialcirc_circ_expl int unsigned not null default 0,
			num_serialcirc_circ_empr int unsigned not null default 0,
			num_serialcirc_circ_serialcirc int unsigned not null default 0,
            serialcirc_circ_order int unsigned not null default 0,
            serialcirc_circ_subscription int unsigned not null default 0,
            serialcirc_circ_ret_asked int unsigned not null default 0,
            serialcirc_circ_trans_asked int unsigned not null default 0,
            serialcirc_circ_trans_doc_asked int unsigned not null default 0,
			serialcirc_circ_expected_date datetime,
			serialcirc_circ_pointed_date datetime
		)";
		//,			primary key(id_serialcirc_circ, num_serialcirc_circ_diff,num_serialcirc_circ_expl,num_serialcirc_circ_empr,num_serialcirc_circ_serialcirc)
		echo traite_rqt($rqt,"create table serialcirc_circ");

		//path_pmb planificateur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='path_php' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'path_php', '', 'Chemin absolu de l\'interpréteur PHP, local ou distant', '',0) ";
			echo traite_rqt($rqt, "insert pmb_path_php into parameters");
		}

		//modification taille du champ expl_comment de la table exemplaires
		$rqt = "ALTER TABLE exemplaires MODIFY expl_comment TEXT ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_comment");

		//tri sur les documents numériques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='explnum_order' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'explnum_order', 'explnum_mimetype, explnum_nom, explnum_id','Ordre d\'affichage des documents numériques, dans l\'ordre donné, séparé par des virgules : explnum_mimetype, explnum_nom, explnum_id','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_explnum_order=explnum_mimetype, explnum_nom, explnum_id into parametres");
		}

		//modification taille du champ resa_idempr de la table resa
		$rqt = "ALTER TABLE resa MODIFY resa_idempr int(10) unsigned NOT NULL default 0";
		echo traite_rqt($rqt,"ALTER TABLE resa MODIFY resa_idempr");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v5.06");
//		break;

//	case "v5.06":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
	// +-------------------------------------------------+

		@set_time_limit(0);
		//ajout d'un flag pour la résa en circulation
		$rqt = "alter table serialcirc_circ add serialcirc_circ_hold_asked int not null default 0 after serialcirc_circ_subscription";
		echo traite_rqt($rqt,"alter table serialcirc_circ add serialcirc_circ_hold_asked");

		//table de gestion des demandes de reproduction
		$rqt="create table if not exists serialcirc_copy (
			id_serialcirc_copy int not null auto_increment primary key,
			num_serialcirc_copy_empr int not null default 0,
			num_serialcirc_copy_bulletin int not null default 0,
			serialcirc_copy_analysis text,
			serialcirc_copy_date date not null default '0000-00-00',
			serialcirc_copy_state int not null default 0,
			serialcirc_copy_comment text not null
			)";
		echo traite_rqt($rqt,"create table serialcirc_copy");

		$rqt="create table if not exists serialcirc_ask (
			id_serialcirc_ask int unsigned not null auto_increment primary key,
			num_serialcirc_ask_perio int unsigned not null default 0,
			num_serialcirc_ask_serialcirc int unsigned not null default 0,
			num_serialcirc_ask_empr int unsigned not null default 0,
			serialcirc_ask_type int unsigned not null default 0,
			serialcirc_ask_statut int unsigned not null default 0,
			serialcirc_ask_date date NOT NULL default '0000-00-00',
			serialcirc_ask_comment text not null
			)";
		echo traite_rqt($rqt,"create table serialcirc_ask");

		// Création table facettes foireuse en développement
		$rqt = "ALTER TABLE facettes add facette_type_sort int(1) not null default 0 AFTER facette_visible";
		echo traite_rqt($rqt,"ALTER TABLE facettes add facette_type_sort ");
		$rqt = "ALTER TABLE facettes add facette_order_sort int(1) not null default 0 AFTER facette_type_sort";
		echo traite_rqt($rqt,"ALTER TABLE facettes add facette_order_sort ");

		// comptabilisation de l'amende : à partir de la date de retour, à partir du délai de grâce
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='amende_comptabilisation' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'amende_comptabilisation', '0','Date à laquelle le début de l\'amende sera comptabilisée \r\n 0 : à partir de la date de retour \r\n 1 : à partir du délai de grâce','')";
			echo traite_rqt($rqt,"insert pmb_amende_comptabilisation=0 into parametres");
		}

		// prêt en retard : compter le jour de la date de retour ou la date de relance comme un retard ?
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_calcul_retard_date_debut_incluse' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'pret_calcul_retard_date_debut_incluse', '0','Compter le jour de retour ou de relance comme un jour de retard pour le calcul de l\'amende ? \r\n 0 : Non \r\n  1 : Oui','')";
			echo traite_rqt($rqt,"insert pmb_pret_calcul_retard_date_debut_incluse=0 into parametres");
		}

		//modification taille du champ comment_gestion de la table bannettes
		$rqt = "ALTER TABLE bannettes MODIFY comment_gestion text NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE bannettes MODIFY comment_gestion");

		//modification taille du champ comment_public de la table bannettes
		$rqt = "ALTER TABLE bannettes MODIFY comment_public text NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE bannettes MODIFY comment_public");

		//AR
		//Exclusion de champs dans la recherche tous les champs en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exclude_fields' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'exclude_fields', '','Identifiants des champs à exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)','c_recherche')";
			echo traite_rqt($rqt,"insert opac_exclude_fields into parametres");
		}

		//ajout dates log dans table des vues
		$rqt = "ALTER TABLE statopac_vues ADD date_debut_log DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
				ADD date_fin_log DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ";
		echo traite_rqt($rqt,"ALTER TABLE statopac_vues add log dates");

		//Ajout champ serialcirc_tpl pour l'impression de la fiche de circulation
		$rqt = "ALTER TABLE serialcirc ADD serialcirc_tpl TEXT NOT NULL";
		echo traite_rqt($rqt,"ALTER TABLE serialcirc ADD serialcirc_tpl ");

		//AR
		//Onglet Abonnement du compte emprunteur visible ou non...
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='serialcirc_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'serialcirc_active', 0,'Activer la circulation des pédioques dans l\'OPAC \r\n 0: Non \r\n 1: Oui','f_modules')";
			echo traite_rqt($rqt,"insert opac_serialcirc_active into parametres");
		}

		//AR
		//Ajout d'un droit sur le statut pour la circulation des périos
		$rqt = "alter table empr_statut add allow_serialcirc int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table empr_statut add allow_serialcirc");

		// création $pmb_bdd_subversion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='bdd_subversion' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'bdd_subversion', '0', 'Sous-version de la base de données')";
			echo traite_rqt($rqt,"insert pmb_bdd_subversion=0 into parametres");
		}

		//AR - Ajout d'un paramètre pour définir la classe d'import des autorités...
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='import_modele_authorities' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'import_modele_authorities', 'notice_authority_import','Quelle classe d\'import utiliser pour les notices d\'autorités ?','')";
			echo traite_rqt($rqt,"insert pmb_import_modele_authorities into parametres");
		}

		//AR - pris dans le tapis entre 2 versions...
		//création de la table origin_authorities
		$rqt = "create table if not exists origin_authorities (
			id_origin_authorities int(10) unsigned NOT NULL AUTO_INCREMENT,
			origin_authorities_name varchar(255) NOT NULL DEFAULT '',
			origin_authorities_country varchar(10) NOT NULL DEFAULT '',
			origin_authorities_diffusible int(10) unsigned NOT NULL DEFAULT 0,
			primary key (id_origin_authorities)
			)";
		echo traite_rqt($rqt,"create table origin_authorities");
		//AR - ajout de valeurs par défault...
		$rqt = "insert into origin_authorities
				(id_origin_authorities,origin_authorities_name,origin_authorities_country,origin_authorities_diffusible)
			values
				(1,'Catalogue Interne','FR',1),
				(2,'BnF','FR',1)";
		echo traite_rqt($rqt,"insert default values into origin_authorities");

		//AR - création de la table authorities_source
		$rqt = "create table if not exists authorities_sources (
			id_authority_source int(10) unsigned NOT NULL AUTO_INCREMENT,
			num_authority int(10) unsigned NOT NULL DEFAULT 0,
			authority_number varchar(50) NOT NULL DEFAULT '',
			authority_type varchar(20) NOT NULL DEFAULT '',
			num_origin_authority int(10) unsigned NOT NULL DEFAULT 0,
			authority_favorite int(10) unsigned NOT NULL DEFAULT 0,
			import_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			update_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			primary key (id_authority_source) )";
		echo traite_rqt($rqt,"create table authorities_sources");

		//AR - création de la table notices_authorities_sources
		$rqt ="create table if not exists notices_authorities_sources (
			num_authority_source int(10) unsigned NOT NULL DEFAULT 0,
			num_notice int(10) unsigned NOT NULL DEFAULT 0,
			primary key (num_authority_source,num_notice)
			)";
		echo traite_rqt($rqt,"create table notices_authorities_sources");

		//AR - modification du champ aut_link_type
		$rqt = "alter table aut_link change aut_link_type aut_link_type varchar(2) not null default ''";
		echo traite_rqt($rqt,"alter table aut_link change aut_link_type varchar");

		//MB - Modification de l'explication du paramètre d'affichage des dates d'exemplaire
		$rqt="UPDATE parametres SET comment_param='Afficher les dates des exemplaires ? \n 0 : Aucune date.\n 1 : Date de création et modification.\n 2 : Date de dépôt et retour (BDP).\n 3 : Date de création, modification, dépôt et retour.' WHERE type_param='pmb' AND sstype_param='expl_show_dates'";
		$res = pmb_mysql_query($rqt, $dbh) ;

		//DG
		// localisation des prévisions
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='location_resa_planning' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'pmb', 'location_resa_planning', '0', '0', 'Utiliser la gestion de la prévision localisée?\n 0: Non\n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT location_resa_planning INTO parametres") ;
		}

		//Localisation par défaut sur la visualisation des états des collections
		$rqt = "ALTER TABLE users ADD deflt_collstate_location int(6) UNSIGNED DEFAULT 0 after deflt_docs_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_collstate_location after deflt_docs_location");

		//maj valeurs possibles pour empr_filter_rows
		$rqt = "update parametres set comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: catégorie\n s: statut\n g: groupe\n y: année de naissance\n cp: code postal\n cs : code statistique\n ab : type d\'abonnement\n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='filter_rows' ";
		echo traite_rqt($rqt,"update empr_filter_rows into parametres");

		//maj valeurs possibles pour empr_show_rows
		$rqt = "update parametres set comment_param='Colonnes affichées en liste de lecteurs, saisir les colonnes séparées par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+prénom \n a: adresse \n b: code-barre \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #n : id des champs personnalisés \n 1: icône panier' where type_param= 'empr' and sstype_param='show_rows' ";
		echo traite_rqt($rqt,"update empr_show_rows into parametres");

		//maj valeurs possibles pour empr_sort_rows
		$rqt = "update parametres set comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+prénom \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='sort_rows' ";
		echo traite_rqt($rqt,"update empr_sort_rows into parametres");

		//maj commentaire sms_msg_retard
		$rqt = "update parametres set comment_param='Texte du sms envoyé lors d\'un retard' where type_param= 'empr' and sstype_param='sms_msg_retard' ";
		echo traite_rqt($rqt,"update empr_sms_msg_retard into parametres");

		//maj commentaire afficher_numero_lecteur_lettres
		$rqt = "update parametres set comment_param='Afficher le numéro et le mail du lecteur sous l\'adresse dans les différentes lettres' where type_param= 'pmb' and sstype_param='afficher_numero_lecteur_lettres' ";
		echo traite_rqt($rqt,"update pmb_afficher_numero_lecteur_lettres into parametres");

		//DB
		//modification du paramètre empr_sms_activation
		$rqt = "select valeur_param from parametres where type_param= 'empr' and sstype_param='sms_activation' ";
		$res = pmb_mysql_query($rqt);
		if (pmb_mysql_num_rows($res)) {
			$old_value = pmb_mysql_result($res,0,0);
			if ($old_value==1) {
				$new_value='1,1,1,1';
				$rqt = "update parametres set valeur_param='".$new_value."', comment_param='Activation de l\'envoi de sms. : relance 1,relance 2,relance 3,resa\n\n 0: Inactif\n 1: Actif' where type_param= 'empr' and sstype_param='sms_activation' ";
				echo traite_rqt($rqt,"update sms_activation");
			} elseif ($old_value==0) {
				$new_value='0,0,0,0';
				$rqt = "update parametres set valeur_param='".$new_value."', comment_param='Activation de l\'envoi de sms. : relance 1,relance 2,relance 3,resa\n\n 0: Inactif\n 1: Actif' where type_param= 'empr' and sstype_param='sms_activation' ";
				echo traite_rqt($rqt,"update empr_sms_activation");
			}
		}

		//Ajout de la durée de consultation pour la circulation des périos
		$rqt = "alter table abts_periodicites add consultation_duration int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table abts_periodicites add consultation_duration");

		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_fields_global_index";
			echo traite_rqt($rqt,"truncate table notices_fields_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		// suppr index inutile
		$rqt = "alter table notices_fields_global_index drop index i_id_value";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop index i_id_value");

		//Modification du commentaire du paramètre opac_notice_reduit_format pour ajout format titre uniquement
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4' where type_param='opac' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre opac_notice_reduit_format");

		// Ajout du module Havest: Moissonneur de notice
        $rqt="create table if not exists harvest_profil (
            id_harvest_profil int unsigned not null auto_increment primary key,
            harvest_profil_name varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table harvest");

        $rqt="create table if not exists harvest_field (
            id_harvest_field int unsigned not null auto_increment primary key,
            num_harvest_profil int unsigned not null default 0,
            harvest_field_xml_id int unsigned not null default 0,
            harvest_field_first_flag int unsigned not null default 0,
            harvest_field_order int unsigned not null default 0
       		)";
        echo traite_rqt($rqt,"create table harvest_field");

        $rqt="create table if not exists harvest_src (
            id_harvest_src int unsigned not null auto_increment primary key,
            num_harvest_field int unsigned not null default 0,
            num_source int unsigned not null default 0,
            harvest_src_unimacfield varchar(255) not null default '',
            harvest_src_unimacsubfield varchar(255) not null default '',
            harvest_src_pmb_unimacfield varchar(255) not null default '',
            harvest_src_pmb_unimacsubfield varchar(255) not null default '',
            harvest_src_prec_flag int unsigned not null default 0,
            harvest_src_order int unsigned not null default 0
        	)";
        echo traite_rqt($rqt,"create table harvest_src");

        $rqt="create table if not exists harvest_profil_import (
            id_harvest_profil_import int unsigned not null auto_increment primary key,
            harvest_profil_import_name varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table harvest_profil_import");

        $rqt="create table if not exists harvest_profil_import_field (
            num_harvest_profil_import int unsigned not null default 0,
            harvest_profil_import_field_xml_id int unsigned not null default 0,
            harvest_profil_import_field_flag int unsigned not null default 0,
            harvest_profil_import_field_order int unsigned not null default 0,
            PRIMARY KEY (num_harvest_profil_import, harvest_profil_import_field_xml_id)
        	)";
        echo traite_rqt($rqt,"create table harvest_profil_import_field");

       	$rqt = "CREATE TABLE if not exists harvest_search_field (
			num_harvest_profil int unsigned not null default 0,
			num_source int unsigned not null default 0,
			num_field int unsigned not null default 0,
			num_ss_field int unsigned not null default 0 ,
            PRIMARY KEY (num_harvest_profil, num_source)
			)";
		echo traite_rqt($rqt,"CREATE TABLE harvest_search_field");

		//AR - Ajout d'un paramètre de blocage d'import dans les autorités
		$rqt = "alter table noeuds add authority_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table noeuds add authority_import_denied");
		$rqt = "alter table authors add author_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table authors add author_import_denied");
		$rqt = "alter table titres_uniformes add tu_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table titres_uniformes add tu_import_denied");
		$rqt = "alter table sub_collections add authority_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table sub_collections add authority_import_denied");
		$rqt = "alter table collections add authority_import_denied int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table collections add authority_import_denied");

		//AR - Modification d'un paramètre pour définir la classe d'import des autorités...
		$rqt = "update parametres set valeur_param = 'authority_import' where type_param= 'pmb' and sstype_param = 'import_modele_authorities'";
		echo traite_rqt($rqt,"update parametres set pmb_import_modele_authorities = 'authority_import'");

		//Ajout d'un index sur le champ ref dans les tables entrepots
		//Récupération de la liste des sources
		$sql_liste_sources = "SELECT source_id FROM connectors_sources ";
		$res_liste_sources = pmb_mysql_query($sql_liste_sources, $dbh) or die(pmb_mysql_error());

		//Pour chaque source
		while ($row=pmb_mysql_fetch_row($res_liste_sources)) {
			$sql_alter_table = "alter table entrepot_source_".$row[0]." drop index i_ref ";
			echo traite_rqt($sql_alter_table, "alter table entrepot_source_".$row[0]." drop index i_ref");
			$sql_alter_table = "alter table entrepot_source_".$row[0]." add index i_ref (ref) ";
			echo traite_rqt($sql_alter_table, "alter table entrepot_source_".$row[0]." add index i_ref");
		}

		//Ajout d'un parametre permettant de préciser si l'on informe par email de l'évolution des demandes
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='email_demandes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'demandes', 'email_demandes', '1',
					'Information par email de l\'évolution des demandes.\n 0 : Non\n 1 : Oui',
					'',0) ";
			echo traite_rqt($rqt, "insert demandes_email_demandes into parameters");
		}


		//AR - Ajout d'un paramètre utilisateur (choix d'un thésaurus par défaut en import d'autorités
		$rqt = "alter table users add deflt_import_thesaurus int not null default 1 after deflt_thesaurus";
		echo traite_rqt($rqt,"alter table users add deflt_import_thesaurus'");

		//AR - On lui met un bonne valeur par défaut...
		$rqt = "update users set deflt_import_thesaurus = ".$thesaurus_defaut;
		echo traite_rqt($rqt,"update users set deflt_import_thesaurus");

		//AR - Ajout d'une colonne sur la table connectors_sources pour définir les types d'enrichissements autorisés dans une source
		$rqt = "alter table connectors_sources add type_enrichment_allowed text not null";
		echo traite_rqt($rqt,"alter table connectors_sources add type_enrichment_allowed");

		// ER - index notices.statut
		$rqt = "ALTER TABLE notices DROP INDEX i_not_statut " ;
		echo traite_rqt($rqt,"ALTER TABLE notices DROP INDEX i_not_statut ") ;
		$rqt = "ALTER TABLE notices ADD INDEX i_not_statut (statut)" ;
		echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX i_not_statut (statut)") ;


		// Création cms
		$rqt="create table if not exists cms_cadres (
            id_cadre int unsigned not null auto_increment primary key,
            cadre_hash varchar(255) not null default '',
            cadre_name varchar(255) not null default '',
            cadre_styles text not null,
            cadre_dom_parent varchar(255) not null default '',
            cadre_dom_after varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table cms_cadres");

		$rqt="create table if not exists cms_cadre_content (
            id_cadre_content int unsigned not null auto_increment primary key,
            cadre_content_hash varchar(255) not null default '',
            cadre_content_type varchar(255) not null default '',
            cadre_content_num_cadre int(10) unsigned not null default 0,
            cadre_content_data text not null,
            cadre_content_num_cadre_content int unsigned not null default 0
        	)";
        echo traite_rqt($rqt,"create table cms_cadre_content");

		$rqt="create table if not exists cms_pages (
            id_page int unsigned not null auto_increment primary key,
            page_hash varchar(255) not null default '',
            page_name varchar(255) not null default '',
            page_description text not null
       		)";
        echo traite_rqt($rqt,"create table cms_pages");

		$rqt="create table if not exists cms_vars (
            id_var int unsigned not null auto_increment primary key,
            var_num_page int unsigned not null default 0,
            var_name varchar(255) not null default '',
            var_comment varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table cms_vars");

		$rqt="create table if not exists cms_pages_env (
            page_env_num_page int unsigned not null auto_increment primary key,
            page_env_name varchar(255) not null default '',
            page_env_id_selector varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table cms_pages_env");


		$rqt="create table if not exists cms_hash (
            hash varchar(255) not null default '' primary key
        	)";
        echo traite_rqt($rqt,"create table cms_hash ");

		//DB - parametre gestion de pret court
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='short_loan_management' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'short_loan_management', '0', 'Gestion des prêts courts\n 0: Non\n 1: Oui')";
			echo traite_rqt($rqt,"insert pmb_short_loan_management=0 into parametres");
		}
		//DB - ajout colonne duree pret court dans la table docs_type
		$rqt="ALTER TABLE docs_type ADD short_loan_duration INT(6) UNSIGNED NOT NULL DEFAULT 1 ";
		echo traite_rqt($rqt,"alter table docs_type add short_loan_duration");

		//DB - correction origine notices
		$rqt = "update notices set origine_catalogage='1', update_date=update_date where origine_catalogage='0' ";
		echo traite_rqt($rqt,"alter table notices correct origine_catalogage");

		//DB - ajout flag pret court dans table pret
		$rqt = "ALTER TABLE pret ADD short_loan_flag INT(1) NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table pret add short_loan_flag");

		//DB - ajout flag pret court dans table pret_archive
		$rqt = "ALTER TABLE pret_archive ADD arc_short_loan_flag INT(1) NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table pret_archive add arc_short_loan_flag");

		//DB - parametre gestion de monopole de pret
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='loan_trust_management' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'loan_trust_management', '0', 'Gestion de monopole de prêt\n 0: Non\n x: nombre de jours entre 2 prêts d\'un exemplaire d\'une même notice (ou bulletin)')";
			echo traite_rqt($rqt,"insert pmb_loan_trust_management=0 into parametres");
		}
		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v5.07");
//		break;

//	case "v5.07":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
		// ER : pour le gars au pull rouge
		$rqt = "ALTER TABLE exemplaires MODIFY expl_cote varchar(255) ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_cote varchar(255) ");
		$rqt = "ALTER TABLE exemplaires MODIFY expl_cb varchar(255) ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_cb varchar(255) ");

		//AR - Ajout d'un champ dans cms_cadres
		$rqt = "alter table cms_cadres add cadre_object varchar(255) not null default '' after cadre_hash";
		echo traite_rqt($rqt,"alter table cms_cadre add cadre_object");

		//JP - Ajout tri en opac pour champs persos de notice
		$rqt = "ALTER TABLE collstate_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE collstate_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE empr_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE expl_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE gestfic0_custom ADD opac_sort INT NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom ADD opac_sort INT NOT NULL DEFAULT 0");

		$rqt = "ALTER TABLE notices_custom ADD opac_sort INT NOT NULL DEFAULT 1";
		echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD opac_sort INT NOT NULL DEFAULT 1");

		//JP : Ajout d'un paramètre permettant de choisir une navigation abécédaire ou non en navigation dans les périodiques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_abc_search' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'perio_a2z_abc_search', '0',
					'Recherche abécédaire dans le navigateur de périodiques en OPAC.\n0 : Non.\n1 : Oui.',
					'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_abc_search 0 into parameters");
		}

		//JP : Ajout d'un paramètre permettant de choisir le nombre maximum de notices par onglet en navigation dans les périodiques en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_max_per_onglet' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'perio_a2z_max_per_onglet', '10',
					'Recherche dans le navigateur de périodiques en OPAC : nombre maximum de notices par onglet.',
					'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_max_per_onglet 10 into parameters");
		}

		//DG - Mail de rappel au référent
		$rqt = "ALTER TABLE groupe ADD mail_rappel INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE groupe ADD mail_rappel default 0");

		//DG - Modification du commentaire du paramètre opac_notice_reduit_format pour ajout format titre uniquement
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal' where type_param='opac' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre opac_notice_reduit_format");

		//DG - Alerter l'utilisateur par mail des nouvelles demandes en OPAC ?
		$rqt = "ALTER TABLE users ADD user_alert_demandesmail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_resamail";
		echo traite_rqt($rqt,"ALTER TABLE users add user_alert_demandesmail default 0");

		$rqt = "ALTER TABLE cms_cadre_content ADD cadre_content_object  VARCHAR(  255 ) NOT NULL DEFAULT '' AFTER cadre_content_type";
		echo traite_rqt($rqt,"ALTER TABLE cms_cadre_content ADD cadre_content_object");

		$rqt = "ALTER TABLE cms_build ADD build_page int(11) NOT NULL DEFAULT 0 AFTER build_obj";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_page");

		//DG - Ordre des langues pour les notices
		$rqt = "ALTER TABLE notices_langues ADD ordre_langue smallint(2) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE notices_langues ADD ordre_langue") ;

		//DB - grilles emprunteurs
		$rqt = "create table empr_grilles (
				empr_grille_categ int(5) not null default 0,
				empr_grille_location int(5) not null default 0,
				empr_grille_format longtext,
				primary key  (empr_grille_categ,empr_grille_location))";
		echo traite_rqt($rqt,"create table empr_grilles") ;

		//DB - parametres de gestion d'accès aux programmes externes pour l'indexation des documents numeriques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='indexation_docnum_ext' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'indexation_docnum_ext', '',
					'Paramètres de gestion d\'accès aux programmes externes pour l\'indexation des documents numériques :\n\n Chaque paramètre est défini par un  couple : \"nom=valeur\"\n Les paramètres sont séparés par un \"point-virgule\".\n\n\n Exemples d\'utilisation de \"pyodconverter\", \"jodconverter\" et \"pdftotext\" :\n\npyodconverter_cmd=/opt/openoffice.org3/program/python /opt/ooo_converter/DocumentConverter.py %1s %2s;\njodconverter_cmd=/usr/bin/java -jar /opt/ooo_converter/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar %1s %2s;\njodconverter_url=http://localhost:8080/converter/converted/%1s;\npdftotext_cmd=/usr/bin/pdftotext -enc UTF-8 %1s -;',
					'',0) ";
			echo traite_rqt($rqt, "insert indexation_docnum_ext into parameters");
		}

		//Onglet perso en affichage de notice
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_format_onglets' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'notices_format_onglets', '','Liste des id de template de notice pour ajouter des onglets personnalisés en affichage de notice\nExemple: 1,3','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_notices_format_onglets into parametres");
		}

		//DG - Ajout de la localisation de l'emprunteur pour les stats
		$rqt="ALTER TABLE pret_archive ADD arc_empr_location INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL AFTER arc_empr_statut ";
 		echo traite_rqt($rqt,"alter table pret_archive add arc_empr_location default 0");

 		//DG - Ajout du type d'abonnement de l'emprunteur pour les stats
		$rqt="ALTER TABLE pret_archive ADD arc_type_abt INT( 6 ) UNSIGNED DEFAULT 0 NOT NULL AFTER arc_empr_location ";
 		echo traite_rqt($rqt,"alter table pret_archive add arc_type_abt default 0");

		//DG - Libellé OPAC des statuts d'exemplaires
		$rqt = "ALTER TABLE docs_statut ADD statut_libelle_opac VARCHAR(255) DEFAULT '' after statut_libelle";
		echo traite_rqt($rqt,"ALTER TABLE docs_statut add statut_libelle_opac default ''");

		//DG - Visibilité OPAC des statuts d'exemplaires
 		$rqt = "ALTER TABLE docs_statut ADD statut_visible_opac TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 1";
		echo traite_rqt($rqt,"ALTER TABLE docs_statut ADD statut_visible_opac") ;

		//DB - parametres d'alerte avant affichage des documents numériques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_alert' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'visionneuse_alert', '', 'Message d\'alerte à l\'ouverture des documents numériques.', 'm_photo',0) ";
			echo traite_rqt($rqt, "insert opac_visionneuse_alert into parameters");
		}

		$rqt = "ALTER TABLE cms_build ADD build_fixed int(11) NOT NULL DEFAULT 0 AFTER id_build";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_fixed");

		$rqt = "ALTER TABLE cms_build ADD build_child_before varchar(255) not null default '' AFTER build_parent";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_child_before");

		//AR - création d'une boite noire pour les modules du portail
		$rqt="create table if not exists cms_managed_modules (
			managed_module_name varchar(255) not null default '',
			managed_module_box text not null,
			primary key (managed_module_name))";
		echo traite_rqt($rqt, "create table if not exists cms_managed_modules");


		$rqt = "alter table cms_cadres add cadre_fixed int(11) not null default 0 after cadre_name";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_fixed");


		//DG - Fixer l'âge minimum d'accès à la catégorie de lecteurs
		$rqt = "ALTER TABLE empr_categ ADD age_min INT(3) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_categ ADD age_min default 0");

		//DG - Fixer l'âge maximum d'accès à la catégorie de lecteurs
		$rqt = "ALTER TABLE empr_categ ADD age_max INT(3) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE empr_categ ADD age_max default 0");

		// Liste des cms
		$rqt="create table if not exists cms (
            id_cms int unsigned not null auto_increment primary key,
            cms_name varchar(255) not null default '',
            cms_comment text not null
        )";
        echo traite_rqt($rqt,"create table cms");

 		// évolutions des cms
		$rqt="create table if not exists cms_version (
            id_version int unsigned not null auto_increment primary key,
            version_cms_num int unsigned not null default 0 ,
            version_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            version_comment text not null,
            version_public int unsigned not null default 0,
            version_user int unsigned not null default 0
        )";
        echo traite_rqt($rqt,"create table cms_version");

		$rqt = "alter table cms_build add build_version_num int not null default 0 after id_build";
		echo traite_rqt($rqt,"alter table cms_build add build_version_num");

		//id du cms à utiliser en Opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cms' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'cms', 0,'id du CMS utilisé en OPAC','a_general')";
			echo traite_rqt($rqt,"insert opac_cms into parametres");
		}

		//DG - Colonnes exemplaires affichées en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_data' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'pmb', 'expl_data', 'expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle', 'Colonne des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle #n : id des champs personnalisés \r\n expl_cb est obligatoire et sera ajouté si absent','')";
			echo traite_rqt($rqt,"insert pmb_expl_data=expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,codestat_libelle,lender_libelle,tdoc_libelle into parametres");
		}

		//DB - parametre gestion de monopole de pret
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_display_location_without_expl' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'pmb', 'expl_display_location_without_expl', '0', 'Affichage de la liste des localisations sans exemplaire\n 0: Non\n 1: oui')";
			echo traite_rqt($rqt,"insert pmb_expl_display_location_without_expl=0 into parametres");
		}

		// Voir les prets de son groupe de lecteur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_group_checkout' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param)
				VALUES (0, 'opac', 'show_group_checkout', '0', 'Le responsable du groupe de lecteur voit les prêts de son groupe\n 0: Non\n 1: oui','a_general')";
			echo traite_rqt($rqt,"insert opac_show_group_checkout=0 into parametres");
		}

		// Archivage DSI
		$rqt="create table if not exists dsi_archive (
           	num_banette_arc int unsigned not null default 0,
            num_notice_arc int unsigned not null default 0,
            date_diff_arc date not null default '0000-00-00',
            primary key (num_banette_arc,num_notice_arc,date_diff_arc)
        )";
		echo traite_rqt($rqt,"create table dsi_archive");

		//Nombre d'archive à mémoriser en dsi
		$rqt = "ALTER TABLE bannettes ADD archive_number INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add archive_number");

		//AR - Erreur dans le type de colonne
		$rqt = "ALTER TABLE cms_pages MODIFY page_hash varchar(255) ";
		echo traite_rqt($rqt,"ALTER TABLE exemplaires MODIFY expl_cote varchar(255) ");

		//AR - L'authentification Digest impose une valeur en clair...
		$rqt= "alter table users add user_digest varchar(255) not null default '' after pwd";
		echo traite_rqt($rqt,"alter table users add user_digest");

		//Ajout de deux paramètres pour la navigation par facette
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='facette_in_bandeau_2' "))==0){
			$rqt = "insert into parametres values(0,'opac','facette_in_bandeau_2',0,'La navigation par facettes apparait dans le bandeau ou dans le bandeau 2\n0 : dans le bandeau\n1 : Dans le bandeau 2','c_recherche',0)";
			echo traite_rqt($rqt,"insert opac_facette_in_bandeau_2=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='autolevel2' "))==0){
			$rqt = "insert into parametres values(0,'opac','autolevel2',0,'0 : mode normal de recherche\n1 : Affiche directement le résultat de la recherche tous les champs sans passer par la présentation du niveau 1 de recherche','c_recherche',0)";
			echo traite_rqt($rqt,"insert opac_autolevel2=0 into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='first_page_params' "))==0){
			$rqt = "insert into parametres values(0,'opac','first_page_params','','Structure Json récapitulant les paramètres à initialiser pour la page d\\'accueil :\nExemple : \n{\n\"lvl\":\"cmspage\",\n\"pageid\":2\n}','b_aff_general',0)";
			echo traite_rqt($rqt,"insert opac_first_page_params='' into parametres");
		}

		$rqt = "ALTER TABLE cms_build ADD build_type varchar(255) not null default 'cadre' AFTER build_version_num";
		echo traite_rqt($rqt,"ALTER TABLE cms_build ADD build_type");

		//Création d'un div class raw
		$rqt = "ALTER TABLE cms_build ADD build_div INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table cms_build add build_div");

		// Ajout tpl de notice pour générer le header
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice' where type_param='opac' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre opac_notice_reduit_format");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v5.08");
//		break;

//	case "v5.08":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800");

		//AR - paramètre activant les liens vers les documents numériques non visibles
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_links_invisible_docnums' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'show_links_invisible_docnums', '0',
			'Afficher les liens vers les documents numériques non visible en mode non connecté. (Ne fonctionne pas avec les droits d\'accès).\n 0 : Non.\n1 : Oui.',
			'e_aff_notice',0) ";
			echo traite_rqt($rqt, "insert opac_show_links_invisible_docnums into parameters");
		}

		// Générer un document (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_generate INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_generate");

		// Template de notice en génération de document (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_notice_tpl INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_notice_tpl");

		// Générer un document avec les doc num (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_insert_docnum INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_insert_docnum");

		// Grouper les documents (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_group INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_group");

		// Ajouter un sommaire (dsi)
		$rqt = "ALTER TABLE bannettes ADD document_add_summary INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add document_add_summary");

		//DG - Index
		$rqt = "alter table explnum drop index explnum_repertoire";
		echo traite_rqt($rqt,"alter table explnum drop index explnum_repertoire");
		$rqt = "alter table explnum add index explnum_repertoire(explnum_repertoire)";
		echo traite_rqt($rqt,"alter table explnum add index explnum_repertoire");

		// Ajout du module template de mail
        $rqt="create table if not exists mailtpl (
            id_mailtpl int unsigned not null auto_increment primary key,
            mailtpl_name varchar(255) not null default '',
            mailtpl_objet varchar(255) not null default '',
            mailtpl_tpl text not null,
            mailtpl_users varchar(255) not null default ''
        	)";
        echo traite_rqt($rqt,"create table mailtpl");

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_folder' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'img_folder', '',	'Répertoire de stockage des images', '', 0) ";
			echo traite_rqt($rqt, "insert pmb_img_folder into parameters");
		}

		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_url' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'img_url', '',	'URL d\'accès du répertoire des images (pmb_img_folder)', '', 0) ";
			echo traite_rqt($rqt, "insert pmb_img_url into parameters");
		}
		// Ajout de la possibilité de joindre les images dans le mail ( pmb_mail_html_format=2 )
		$rqt = "update parametres set comment_param = 'Format d\'envoi des mails à partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !' where type_param='pmb' and sstype_param='mail_html_format'";
		echo traite_rqt($rqt,"update parametre pmb_mail_html_format");

		// Ajout de la possibilité de joindre les images dans le mail ( opac_mail_html_format=2 )
		$rqt = "update parametres set comment_param = 'Format d\'envoi des mails à partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !' where type_param='opac' and sstype_param='mail_html_format'";
		echo traite_rqt($rqt,"update parametre opac_mail_html_format");

		//AR - Ajout d'une colonne pour marquer un set comme étant en cours de rafraississement
		$rqt = "alter table connectors_out_sets add being_refreshed int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table connectors_out_sets add bien_refreshed");

		//DG - Infobulle lors du survol des vignettes (gestion)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='book_pics_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'pmb', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le paramètre book_pics_url','')";
			echo traite_rqt($rqt,"insert pmb_book_pics_msg='' into parametres");
		}

		//DG - Infobulle lors du survol des vignettes (opac)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='book_pics_msg' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param) VALUES (0, 'opac', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le paramètre book_pics_url','e_aff_notice')";
			echo traite_rqt($rqt,"insert opac_book_pics_msg='' into parametres");
		}

		//AR - Utilisation des quotas pour la définition des vues disponibles pour un emprunteur
		$rqt = "create table if not exists quotas_opac_views (
			quota_type int(10) unsigned not null default 0,
			constraint_type varchar(255) not null default '',
			elements int(10) unsigned not null default 0,
			value text not null,
			primary key(quota_type,constraint_type,elements)
		)";
		echo traite_rqt($rqt,"create table quotas_opac_views");

		//AR - table de mots
		$rqt = "create table if not exists words (
			id_word int unsigned not null auto_increment primary key,
			word varchar(255) not null default '',
			lang varchar(10) not null default '',
			unique i_word_lang (word,lang)
		)";
		echo traite_rqt($rqt,"create table words");

		$rqt = "show fields from notices_mots_global_index";
		$res = pmb_mysql_query($rqt);
		$exists = false;
		if(pmb_mysql_num_rows($res)){
			while($row = pmb_mysql_fetch_object($res)){
				if($row->Field == "num_word"){
					$exists = true;
					break;
				}
			}
		}
		if(!$exists){
			//la méthode du chef reste la meilleure
			set_time_limit(0);

			if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
				$rqt = "truncate table notices_fields_global_index";
				echo traite_rqt($rqt,"truncate table notices_fields_global_index");

				$rqt = "truncate table notices_mots_global_index";
				echo traite_rqt($rqt,"truncate table notices_mots_global_index");

				// Info de réindexation
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
			}

			//on ajoute un index bien pratique...
			$rqt ="alter table notices_mots_global_index add index mot_lang(mot,lang)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add index");

			//remplissage de la table mots
			$rqt ="insert ignore into words (word,lang) select distinct mot,lang from notices_mots_global_index";
			echo traite_rqt($rqt,"insert into words");

			//on utilise une table tampon
			$rqt ="create table transition select id_notice,code_champ,code_ss_champ,mot,id_word from notices_mots_global_index join words on (mot=word and notices_mots_global_index.lang=words.lang);";
			echo traite_rqt($rqt,"create table transition");
			//on y ajoute les index qui vont bien
			$rqt ="alter table transition add primary key (id_notice,code_champ,code_ss_champ,mot)";
			echo traite_rqt($rqt,"alter table transition add primary key");

			//on ajout la clé étrangère num_word dans notices_mots_global_index
			$rqt ="alter table notices_mots_global_index add num_word int(10) unsigned not null default 0 after mot";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add num_word");
			//on l'affecte
			$rqt ="update notices_mots_global_index as a0 join transition as a1 on (a0.id_notice=a1.id_notice and a0.code_champ=a1.code_champ and a0.code_ss_champ=a1.code_ss_champ and a0.mot=a1.mot) set num_word=id_word";
			echo traite_rqt($rqt,"update notices_mots_global_index set num_word=id_word");

			//on peut se passer de certains index et mettre les nouveaux
			$rqt ="drop index i_mot on notices_mots_global_index";
			echo traite_rqt($rqt,"drop index i_mot on notices_mots_global_index");
			$rqt ="drop index i_id_mot on notices_mots_global_index";
			echo traite_rqt($rqt,"drop index i_id_mot on notices_mots_global_index");
			$rqt ="alter table notices_mots_global_index add index i_id_mot(num_word,id_notice)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_id_mot");
			$rqt ="alter table notices_mots_global_index drop primary key";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop primary key");
			$rqt ="alter table notices_mots_global_index add primary key (id_notice,code_champ,code_ss_champ,num_word,position)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add primary key");

			//on supprime l'index pratique
			$rqt ="drop index mot_lang on notices_mots_global_index";
			echo traite_rqt($rqt,"drop index mot_lang on notices_mots_global_index");

			//certains champs n'ont plus d'utilité dans notices_mots_global_index
			$rqt ="alter table notices_mots_global_index drop mot";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop mot");
			$rqt ="alter table notices_mots_global_index drop nbr_mot";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop nbr_mot");
			$rqt ="alter table notices_mots_global_index drop lang";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop lang");

			//on supprime l'index pratique
			//on supprime la table de transition
			$rqt ="drop table transition";
			echo traite_rqt($rqt,"drop table transition");
		}

		//AR - modification du paramètre de gestion des vues
		$rqt = "update parametres set comment_param = 'Activer les vues OPAC :\n 0 : non activé\n 1 : activé avec gestion classique\n 2 : activé avec gestion avancée' where type_param = 'pmb' and sstype_param = 'opac_view_activate'";
		echo traite_rqt($rqt,"update parametres pmb_opac_view_activate");

		//DB - modification du paramètre utiliser_calendrier
		$rqt = "update parametres set comment_param = 'Utiliser le calendrier des jours d\'ouverture ?\n 0 : non\n 1 : oui, pour le calcul des dates de retour et des retards\n 2 : oui, pour le calcul des dates de retour uniquement' where type_param = 'pmb' and sstype_param = 'utiliser_calendrier'";
		echo traite_rqt($rqt,"update parametres pmb_utiliser_calendrier");

		//NG - Ajout dans les statuts d'exemplaire la possibilité de rendre réservable ou non
		$rqt = "ALTER TABLE docs_statut ADD statut_allow_resa INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table docs_statut add statut_allow_resa");
		$rqt = "UPDATE docs_statut set statut_allow_resa=1 where pret_flag=1 ";
		echo traite_rqt($rqt,"UPDATE docs_statut set statut_allow_resa=1 where pret_flag=1");

		// Ajout CMS actif par défaut en Opac
		$rqt = "alter table cms add cms_opac_default int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table cms add cms_opac_default");

		$rqt = "create table if not exists cms_editorial_types (
			id_editorial_type int unsigned not null auto_increment primary key,
			editorial_type_element varchar(20) not null default '',
			editorial_type_label varchar(255) not null default '',
			editorial_type_comment text not null
		)";
		echo traite_rqt($rqt,"create table cms_editorial_types");

		//AR - on ajoute le type de contenu sur les tables cms_articles et cms_sections
		$rqt = "alter table cms_articles add article_num_type int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table cms_articles add article_num_type");
		$rqt = "alter table cms_sections add section_num_type int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table cms_sections add section_num_type");

		//AR - Un type de contenu c'est quoi? c'est une définition de grille de champs perso
		$rqt = "create table if not exists cms_editorial_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table cms_editorial_custom ");

		$rqt = "create table if not exists cms_editorial_custom_lists (
			cms_editorial_custom_champ int(10) unsigned NOT NULL default 0,
			cms_editorial_custom_list_value varchar(255) default NULL,
			cms_editorial_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (cms_editorial_custom_champ),
			KEY editorial_champ_list_value (cms_editorial_custom_champ,cms_editorial_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists cms_editorial_custom_lists ");

		$rqt = "create table if not exists cms_editorial_custom_values (
			cms_editorial_custom_champ int(10) unsigned NOT NULL default 0,
			cms_editorial_custom_origine int(10) unsigned NOT NULL default 0,
			cms_editorial_custom_small_text varchar(255) default NULL,
			cms_editorial_custom_text text,
			cms_editorial_custom_integer int(11) default NULL,
			cms_editorial_custom_date date default NULL,
			cms_editorial_custom_float float default NULL,
			KEY editorial_custom_champ (cms_editorial_custom_champ),
			KEY editorial_custom_origine (cms_editorial_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists cms_editorial_custom_values ");

		//NG - Ajout de l'url permetant de retouver la page Opac contenant le cadre
		$rqt = "alter table cms_cadres add cadre_url text not null ";
		echo traite_rqt($rqt,"alter table cms_cadre add cadre_url");

		//MB - Ajout d'une colonne pour les noeuds utilisables ou non en indexation
		$rqt = "ALTER TABLE noeuds ADD not_use_in_indexation INT( 1 ) UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table noeuds add not_use_in_indexation");

		//MB - Modification du commentaire du paramètre show_categ_browser
		$rqt = "UPDATE parametres SET comment_param = 'Affichage des catégories en page d\'accueil OPAC:\n0: Non\n1: Oui\n1 3,1: Oui, avec thésaurus id 3 puis 1 (préciser les thésaurus à afficher et l\'ordre)' where type_param = 'opac' and sstype_param = 'show_categ_browser'";
		echo traite_rqt($rqt,"update parametres show_categ_browser");

		//MB - Remplacement du code de lien d'autorité 2 par z car c'est le même libellé et z est normé
		$rqt = "UPDATE aut_link SET aut_link_type = 'z' where aut_link_type = '2' ";
		echo traite_rqt($rqt,"update aut_link");

		//AR indexons correctement le contenu éditorial
		$rqt = "create table if not exists cms_editorial_words_global_index(
			num_obj int unsigned not null default 0,
			type varchar(20) not null default '',
			code_champ int not null default 0,
			code_ss_champ int not null default 0,
			num_word int not null default 0,
			pond int not null default 100,
			position int not null default 1,
			primary key (num_obj,type,code_champ,code_ss_champ,num_word,position)

		)";
		echo traite_rqt($rqt,"create table cms_editorial_words_global_index ");

		$rqt = "create table if not exists cms_editorial_fields_global_index(
			num_obj int unsigned not null default 0,
			type varchar(20) not null default '',
			code_champ int(3) not null default 0,
			code_ss_champ int(3) not null default 0,
			ordre int(4) not null default 0,
			value text not null,
			pond int(4) not null default 100,
			lang varchar(10) not null default '',
			primary key(num_obj,type,code_champ,code_ss_champ,ordre),
			index i_value(value(300))
		)";
		echo traite_rqt($rqt,"create table cms_editorial_fields_global_index ");

		//DB - parametre d'alerte avant affichage des documents numériques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='visionneuse_alert_doctype' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'visionneuse_alert_doctype', '', 'Liste des types de documents pour lesquels une alerte est générée (séparés par une virgule).', 'm_photo',0) ";
			echo traite_rqt($rqt, "insert opac_visionneuse_alert_doctype into parameters");
		}

		$rqt = "alter table cms_cadres add cadre_memo_url int not null default 0 after cadre_url";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_memo_url");

		//DB - entrepot d'archivage à la suppression des notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='archive_warehouse' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'archive_warehouse', '0', 'Identifiant de l\'entrepôt d\'archivage à la suppression des notices.', '',0) ";
			echo traite_rqt($rqt, "insert archive_warehouse into parameters");
		}

		$rqt = "alter table cms_cadres add cadre_classement  varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_classement");

		//NG - Imprimante ticket
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_name' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'printer_name', '', 'Nom de l\'imprimante de ticket de prêt, utilisant l\'applet jzebra. Le nom de l\'imprimante doit correspondre à la class développée spécifiquement pour la piloter.\nExemple: Nommer l\'imprimante \'metapace\' pour utiliser le driver classes/printer/metapace.class.php', '',0) ";
			echo traite_rqt($rqt, "insert pmb_printer_name into parameters");
		}

		//DG - Localisation par défaut sur la visualisation des réservations
		$rqt = "ALTER TABLE users ADD deflt_resas_location int(6) UNSIGNED DEFAULT 0 after deflt_collstate_location";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_resas_location after deflt_collstate_location");

		//DG - parametre localisation des groupes de lecteurs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='groupes_localises' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
				VALUES (0, 'empr', 'groupes_localises', '0', 'Groupes de lecteurs localisés par rapport au responsable \n0: Non \n1: oui')";
			echo traite_rqt($rqt,"insert empr_groupes_localises=0 into parametres");
		}

		// Activation des recherches similaires
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_simili_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_simili_search', '0', 'Activer les recherches similaires en OPAC:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_allow_simili_search='0' into parametres ");
		}

		//ajout d'une date de création pour les articles et les rubriques
		$rqt ="alter table cms_articles add article_creation_date date";
		echo traite_rqt($rqt,"alter table cms_articles add article_creation_date date");
		$rqt ="alter table cms_sections add section_creation_date date";
		echo traite_rqt($rqt,"alter table cms_sections add section_creation_date date");

		//index d'on se lève tous pour la bannette de Camille
		$rqt = "alter table bannette_abon drop index i_num_empr";
		echo traite_rqt($rqt,"alter table bannette_abon drop index i_num_empr");
		$rqt = "alter table bannette_abon add index i_num_empr(num_empr)";
		echo traite_rqt($rqt,"alter table bannette_abon add index i_num_empr(num_empr)");

		// MB - Modification du plus Opac devant les notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_depliable_plus' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'notices_depliable_plus', 'plus.gif', 'Image à utiliser devant un titre de notice pliée', 'e_aff_notice', '0')";
			echo traite_rqt($rqt,"insert notices_depliable_plus into parametres ");
		}

		// MB - Modification du plus Opac devant les notices
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_depliable_moins' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'notices_depliable_moins', 'minus.gif', 'Image à utiliser devant un titre de notice dépliée', 'e_aff_notice', '0')";
			echo traite_rqt($rqt,"insert notices_depliable_moins into parametres ");
		}

		//MB - Modification du commentaire du paramètre notices_depliable
		$rqt = "UPDATE parametres SET comment_param = 'Affichage dépliable des notices en résultat de recherche:\n0: Non dépliable\n1: Dépliable en cliquant que sur l\'icone\n2: Déplibable en cliquant sur toute la ligne du titre' where type_param = 'opac' and sstype_param = 'notices_depliable'";
		echo traite_rqt($rqt,"update parametres notices_depliable");

		// Ajout du regroupement d'exemplaires pour le prêt
		$rqt = "create table if not exists groupexpl (
			id_groupexpl int(10) unsigned NOT NULL auto_increment,
			groupexpl_resp_expl_num int(10) unsigned NOT NULL default 0,
			groupexpl_name varchar(255) NOT NULL default '',
			groupexpl_comment varchar(255) NOT NULL default '',
			groupexpl_location int(10) unsigned NOT NULL default 0,
			groupexpl_statut_resp int(10) unsigned NOT NULL default 0,
			groupexpl_statut_others int(10) unsigned NOT NULL default 0,
			PRIMARY KEY (id_groupexpl)) ";
		echo traite_rqt($rqt,"create table groupexpl ");

		// Ajout du regroupement d'exemplaires pour le prêt
		$rqt = "create table if not exists groupexpl_expl (
			groupexpl_num int(10) unsigned NOT NULL  default 0,
			groupexpl_expl_num int(10) unsigned NOT NULL  default 0,
			groupexpl_checked int unsigned NOT NULL  default 0,
			PRIMARY KEY (groupexpl_num, groupexpl_expl_num)) ";
		echo traite_rqt($rqt,"create table groupexpl_expl ");

		// Activation du prêt d'exemplaires groupés
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_groupement' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'pret_groupement', '0', 'Activer le prêt d\'exemplaires regroupés en un seul lot. La gestion des groupes se gére en Circulation / Groupe d\'exemplaires :\n 0 : non \n 1 : oui', '', '0')";
			echo traite_rqt($rqt,"insert pmb_pret_groupement='0' into parametres ");
		}

		//AR - refonte éditions...
		$rqt = "create table if not exists editions_states (
			id_editions_state int unsigned not null auto_increment primary key,
			editions_state_name varchar(255) not null default '',
			editions_state_num_classement int not null default 0,
			editions_state_used_datasource varchar(50) not null default '',
			editions_state_comment text not null,
			editions_state_fieldslist text not null,
			editions_state_fieldsparams text not null
		)";
		echo traite_rqt($rqt,"create table if not exists editions_states");

		// cms: Classement des pages
		$rqt = "alter table cms_pages add page_classement  varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table cms_pages add page_classement");

		// Transfert: regroupement des départs
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='regroupement_depart' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'regroupement_depart', '0', '1', 'Active le regroupement des départs\n 0: Non \n 1: Oui') ";
			echo traite_rqt($rqt,"INSERT transferts_regroupement_depart INTO parametres") ;
		}

		//index Camille (comment ça encore ?)
		$rqt = "alter table coordonnees drop index i_num_entite";
		echo traite_rqt($rqt,"alter table coordonnees drop index i_num_entite");
		$rqt = "alter table coordonnees add index i_num_entite (num_entite)";
		echo traite_rqt($rqt,"alter table coordonnees add index i_num_entite (num_entite)");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
// 		echo form_relance ("v5.09");
//		break;

//	case "v5.09":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800");

		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_fields_global_index";
			echo traite_rqt($rqt,"truncate table notices_fields_global_index");

			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}

		//AR - On revoit une clé primaire
		$rqt ="alter table notices_fields_global_index drop primary key";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop primary key");
		$rqt ="alter table notices_fields_global_index add primary key(id_notice,code_champ,code_ss_champ,lang,ordre)";
		echo traite_rqt($rqt,"alter table notices_fields_global_index add primary key(id_notice,code_champ,code_ss_champ,lang,ordre)");

		//AR - ajout du partitionnement de manière systématique
		$rqt="show table status where name='notices_mots_global_index' or name='notices_fields_global_index'";
		$result = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($row->Create_options != "partitioned"){
					$rqt="alter table ".$row->Name." partition by key(code_champ,code_ss_champ) partitions 50";
					echo traite_rqt($rqt,"alter table ".$row->Name." partition by key");
				}
			}
		}

		// RFID: ajout de la gestion de l'antivol par afi
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='rfid_afi_security_codes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
			VALUES (0, 'pmb', 'rfid_afi_security_codes', '', '0', 'Gestion de l\'antivol par le registre AFI.\nLa première valeur est celle de l\'antivol actif, la deuxième est celle de l\antivol inactif.\nExemple: 07,C2  ') ";
			echo traite_rqt($rqt,"INSERT pmb_rfid_afi_security_codes INTO parametres") ;
		}

		// CMS: ajout de l'url de construction de l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'pmb' and sstype_param='url_base_cms_build' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
			VALUES (0, 'pmb', 'url_base_cms_build', '', '0', 'url de construction du CMS de l\'OPAC') ";
			echo traite_rqt($rqt,"INSERT pmb_url_base_cms_build INTO parametres") ;
		}

		//AR - on stocke le double metaphone de chaque mot !
		$rqt = "alter table words add double_metaphone varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table words add double_metaphone");
		$rqt = "alter table words add stem varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table words add stem");
		//AR - Suggestions de mots dans la saisie en recherche simple
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='simple_search_suggestions' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','simple_search_suggestions','0','Activer la suggestion de mots en recherche simple via la complétion\n0 : Désactiver\n1 : Activer\n\nNB : Cette fonction nécessite l\'installation de l\'extension levenshtein dans MySQL','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_simple_search_suggestions into parametres") ;
		}

		//AR - Suggestions de mots dans la saisie en recherche simple
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='stemming_active' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','stemming_active','0','Activer le stemming dans la recherche\n0 : Désactiver\n1 : Activer\n','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_stemming_active into parametres") ;
		}

		$rqt = "delete from parametres where sstype_param like 'url_base_cms_build%' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
		VALUES (0, 'cms', 'url_base_cms_build', '', '0', 'url de construction du CMS de l\'OPAC') ";
		echo traite_rqt($rqt,"INSERT pmb_url_base_cms_build INTO parametres") ;

		//DG - Modification de la taille du champ content_infopage de la table infopages
		$rqt = "ALTER TABLE infopages MODIFY content_infopage longblob NOT NULL default ''";
		echo traite_rqt($rqt,"alter table infopages modify content_infopage");

		//DG - Modification du commentaire du paramètre pmb_blocage_delai
		$rqt = "UPDATE parametres SET comment_param = 'Délai à partir duquel le retard est pris en compte pour le blocage' where type_param = 'pmb' and sstype_param = 'blocage_delai'";
		echo traite_rqt($rqt,"update parametres pmb_blocage_delai");

		$rqt = "delete from parametres where sstype_param like 'url_base_cms_build%' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
		VALUES (0, 'cms', 'url_base_cms_build', '', '0', 'url de construction du CMS de l\'OPAC') ";
		echo traite_rqt($rqt,"INSERT pmb_url_base_cms_build INTO parametres") ;


		//index Camille (c'est que le début d'accord d'accord ?)
		$rqt = "alter table resa drop index i_idbulletin";
		echo traite_rqt($rqt,"alter table resa drop index i_idbulletin");
		$rqt = "alter table resa add index i_idbulletin (resa_idbulletin)";
		echo traite_rqt($rqt,"alter table resa add index i_idbulletin (resa_idbulletin)");

		$rqt = "alter table resa drop index i_idnotice";
		echo traite_rqt($rqt,"alter table resa drop index i_idnotice");
		$rqt = "alter table resa add index i_idnotice (resa_idnotice)";
		echo traite_rqt($rqt,"alter table resa add index i_idnotice (resa_idnotice)");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;

	// Version de Lliurex Pandora
//		echo form_relance ("v5.10");
//		break;

//	case "v5.10":
		 echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//AR - ajout de type de contenu générique pour les articles et rubriques...
		if(!pmb_mysql_num_rows(pmb_mysql_query("select id_editorial_type from cms_editorial_types where editorial_type_element  ='article_generic'"))){
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'article_generic', editorial_type_label ='CP pour Article'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'article_generic'") ;
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'section_generic', editorial_type_label ='CP pour Rubrique'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'section_generic'") ;
		}

		//DG - Ajout du champ index_libelle dans la table frais
		$rqt = "ALTER TABLE frais ADD index_libelle TEXT";
		echo traite_rqt($rqt,"alter table frais add index_libelle");

		//DG - Paramètres pour les lettres de retard par groupe
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1before_list_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1before_list' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1before_list_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1before_list_group into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1after_list_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1after_list' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1after_list_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1after_list_group into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1fdp_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1fdp' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1fdp_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1fdp_group into parametres");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreretard' and sstype_param='1madame_monsieur_group' "))==0){
			$rqt = "select valeur_param,comment_param from parametres where type_param= 'pdflettreretard' and sstype_param='1madame_monsieur' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$comment_param = pmb_mysql_result($res,0,1);
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param) VALUES ('pdflettreretard', '1madame_monsieur_group', '".addslashes($value_param)."', '".addslashes($comment_param)."') " ;
			echo traite_rqt($rqt,"insert pdflettreretard,1madame_monsieur_group into parametres");
		}

		//DG - Impression du nom du groupe sur la lettre de rappel
		$rqt = "ALTER TABLE groupe ADD lettre_rappel_show_nomgroup INT( 1 ) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt,"ALTER TABLE groupe ADD lettre_rappel_show_nomgroup default 0");
		$rqt = "update groupe set lettre_rappel_show_nomgroup=lettre_rappel ";
		echo traite_rqt($rqt,"update groupe set lettre_rappel_show_nomgroup=lettre_rappel");

		//AR - Ajout des extensions de formulaire pour les types de contenus
		$rqt = "alter table cms_editorial_types add editorial_type_extension text not null";
		echo traite_rqt($rqt,"alter table cms_editorial_types add editorial_type_extension");

		//AR - Ajout de la table de stockages des infos des extension
		$rqt = "create table cms_modules_extensions_datas (
			id_extension_datas int(10) not null auto_increment primary key,
			extension_datas_module varchar(255) not null default '',
			extension_datas_type varchar(255) not null default '',
			extension_datas_type_element varchar(255) not null default '',
			extension_datas_num_element int(10) not null default 0,
			extension_datas_datas blob
		)";
		echo traite_rqt($rqt,"create table cms_modules_extensions_datas");

		//NG - Ordre des facettes
		$rqt = "alter table facettes add facette_order int not null default 1";
		echo traite_rqt($rqt,"alter table facettes add facette_order");
		//NG - limit_plus des facettes
		$rqt = "alter table facettes add facette_limit_plus int not null default 0";
		echo traite_rqt($rqt,"alter table facettes add facette_limit_plus");

		//MB - Modification de l'identifiant 28 en 1 pour le trie car il est présent en double dans sort.xml
		$rqt = "update parametres set valeur_param=REPLACE(valeur_param, '_28', '_1') WHERE type_param='opac' AND sstype_param='default_sort' AND valeur_param REGEXP '_28[^0-9]|_28$'";
		echo traite_rqt($rqt,"update param opac_default_sort");

		//NG pb de placement de main_hors_footer et footer
		$rqt = "update cms_build set build_parent='main' where build_obj='main_header' or build_obj='main_hors_footer' or build_obj='footer' ";
		echo traite_rqt($rqt,"update cms_build set build_parent");

		//NG pb de placement des zones du contener
		$rqt = "update cms_build set build_child_before='', build_child_after='intro' where build_obj='main' ";
		echo traite_rqt($rqt,"update cms_build where build_obj='main'");
		$rqt = "update cms_build set build_child_before='main', build_child_after='bandeau' where build_obj='intro' ";
		echo traite_rqt($rqt,"update cms_build where build_obj='intro'");
		$rqt = "update cms_build set build_child_before='intro', build_child_after='bandeau_2' where build_obj='bandeau' ";
		echo traite_rqt($rqt,"update cms_build  where build_obj='bandeau'");
		$rqt = "update cms_build set build_child_before='bandeau', build_child_after='' where build_obj='bandeau_2' ";
		echo traite_rqt($rqt,"update cms_build where build_obj='bandeau_2' ");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
		
		//echo form_relance ("v5.11");
		//break;
//case "v5.11":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//NG Ajout param opac_show_bandeau_2
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_bandeau_2' "))==0){
			$rqt = "select valeur_param from parametres where type_param= 'opac' and sstype_param='show_bandeaugauche' ";
			$res = pmb_mysql_query($rqt);
			$value_param = pmb_mysql_result($res,0,0);
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'show_bandeau_2', '".addslashes($value_param)."', 'Affichage du bandeau_2 ? \n 0 : Non\n 1 : Oui', 'f_modules', 0) " ;
			echo traite_rqt($rqt,"insert opac_show_bandeau_2=opac_show_bandeaugauche into parametres");
		}

		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		//NG ajout de field_position dans notices_mots_global_index
		$rqt = "alter table notices_mots_global_index add field_position int not null default 1";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add field_position");

		//abacarisse en attente
		if (pmb_mysql_num_rows(pmb_mysql_query("select id_param from parametres where type_param= 'opac' and sstype_param='param_social_network' "))==0){
			//Ajout du paramètre de configuration de l'api addThis
			$rqt = "INSERT INTO parametres (type_param ,sstype_param ,valeur_param ,comment_param ,section_param ,gestion) VALUES ('opac', 'param_social_network',
			'{
			\"token\":\"ra-4d9b1e202c30dea1\",
			\"version\":\"300\",
			\"buttons\":[
			{
			\"attributes\":{
			\"class\":\"addthis_button_facebook_like\",
			\"fb:like:layout\":\"button_count\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_tweet\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_counter addthis_button_compact\"
			}
			}
			],
			\"toolBoxParams\":{
			\"class\":\"addthis_toolbox addthis_default_style\"
			},
			\"addthis_share\":{

			},
			\"addthis_config\":{
			\"data_track_clickback\":\"true\",
			\"ui_click\":\"true\"
			}
			}
			', 'Tableau de paramètrage de l\'API de gestion des interconnexions aux réseaux sociaux.
			Au format JSON.
			Exemple :
			{
			\"token\":\"ra-4d9b1e202c30dea1\",
			\"version\":\"300\",
			\"buttons\":[
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_1\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_2\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_3\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_preferred_4\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_button_compact\"
			}
			},
			{
			\"attributes\":{
			\"class\":\"addthis_counter addthis_bubble_style\"
			}
			}
			],
			\"toolBoxParams\":{
			\"class\":\"addthis_toolbox addthis_default_style addthis_32x32_style\"
			},
			\"addthis_share\":{

			},
			\"addthis_config\":{
			\"data_track_addressbar\":true
			}
			}', 'e_aff_notice', '0'
			)";
			echo traite_rqt($rqt,"insert opac_param_social_network into parametres");
		}

		// DG
		//ajout du champ groupe_lecteurs dans la table bannettes
		$rqt = "ALTER TABLE bannettes ADD groupe_lecteurs INT(8) UNSIGNED NOT NULL default 0";
		echo traite_rqt($rqt,"alter table bannettes add groupe_lecteurs");

		// JP
		$rqt = "update parametres set comment_param='Tri par défaut des recherches OPAC. Deux possibilités :\n- un seul tri par défaut de la forme c_num_6\n- plusieurs tris par défaut de la forme c_num_6|Libelle;d_text_7|Libelle 2;c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri si plusieurs' WHERE type_param='opac' AND sstype_param='default_sort'";
		echo traite_rqt($rqt,"update comment for param opac_default_sort");

		// Transfert: statut non pretable pour les expl en demande de transfert
		if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='pret_demande_statut' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
			VALUES (0, 'transferts', 'pret_demande_statut', '0', '1', 'Appliquer ce statut avant la validation') ";
			echo traite_rqt($rqt,"INSERT transferts_pret_demande_statut INTO parametres") ;
		}

		// descriptors in DSI
		$rqt = "create table if not exists bannettes_descriptors(
			num_bannette int not null default 0,
			num_noeud int not null default 0,
			bannette_descriptor_order int not null default 0,
			primary key (num_bannette,num_noeud)
		)";
		echo traite_rqt($rqt,"create table bannettes_descriptors") ;

		//ajout du champ bannette_mail dans bannette_abon
		$rqt = "ALTER TABLE bannette_abon ADD bannette_mail varchar(255) not null default '' ";
		echo traite_rqt($rqt,"alter table bannette_abon add bannette_mail");

		//AR - on a vu un cas ou ca se passe mal dans la 5.10, par précaution, on répète!
		if(!pmb_mysql_num_rows(pmb_mysql_query("select id_editorial_type from cms_editorial_types where editorial_type_element  ='article_generic'"))){
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'article_generic', editorial_type_label ='CP pour Article'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'article_generic'") ;
		}
		if(!pmb_mysql_num_rows(pmb_mysql_query("select id_editorial_type from cms_editorial_types where editorial_type_element  ='section_generic'"))){
			$rqt = "insert into cms_editorial_types set editorial_type_element = 'section_generic', editorial_type_label ='CP pour Rubrique'";
			echo traite_rqt($rqt,"insert into cms_editorial_types set editorial_type_element = 'section_generic'") ;
		}

		//DG - Augmentation de la taille du champ mention_date de la table bulletins
		$rqt = "ALTER TABLE bulletins MODIFY mention_date varchar(255) not null default ''";
		echo traite_rqt($rqt,"alter table bulletins modify mention_date");

		//DG - parametre pour l'affichage des notices de bulletins dans la navigation a2z
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_show_bulletin_notice' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'perio_a2z_show_bulletin_notice', '0', 'Affichage de la notice de bulletin dans le navigateur de périodiques', 'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_show_bulletin_notice=0 into parametres");
		}

		//DG - ajout d'un commentaire de gestion pour les suggestions
		$rqt = "ALTER TABLE suggestions ADD commentaires_gestion TEXT AFTER commentaires";
		echo traite_rqt($rqt,"alter table suggestions add commentaires_gestion");

		//NG - Champs perso author
		$rqt = "create table if not exists author_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table author_custom ");

		$rqt = "create table if not exists author_custom_lists (
			author_custom_champ int(10) unsigned NOT NULL default 0,
			author_custom_list_value varchar(255) default NULL,
			author_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (author_custom_champ),
			KEY editorial_champ_list_value (author_custom_champ,author_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists author_custom_lists ");

		$rqt = "create table if not exists author_custom_values (
			author_custom_champ int(10) unsigned NOT NULL default 0,
			author_custom_origine int(10) unsigned NOT NULL default 0,
			author_custom_small_text varchar(255) default NULL,
			author_custom_text text,
			author_custom_integer int(11) default NULL,
			author_custom_date date default NULL,
			author_custom_float float default NULL,
			KEY editorial_custom_champ (author_custom_champ),
			KEY editorial_custom_origine (author_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists author_custom_values ");

		//NG - Champs perso categ
		$rqt = "create table if not exists categ_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table categ_custom ");

		$rqt = "create table if not exists categ_custom_lists (
			categ_custom_champ int(10) unsigned NOT NULL default 0,
			categ_custom_list_value varchar(255) default NULL,
			categ_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (categ_custom_champ),
			KEY editorial_champ_list_value (categ_custom_champ,categ_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists categ_custom_lists ");

		$rqt = "create table if not exists categ_custom_values (
			categ_custom_champ int(10) unsigned NOT NULL default 0,
			categ_custom_origine int(10) unsigned NOT NULL default 0,
			categ_custom_small_text varchar(255) default NULL,
			categ_custom_text text,
			categ_custom_integer int(11) default NULL,
			categ_custom_date date default NULL,
			categ_custom_float float default NULL,
			KEY editorial_custom_champ (categ_custom_champ),
			KEY editorial_custom_origine (categ_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists categ_custom_values ");

		//NG - Champs perso publisher
		$rqt = "create table if not exists publisher_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table publisher_custom ");

		$rqt = "create table if not exists publisher_custom_lists (
			publisher_custom_champ int(10) unsigned NOT NULL default 0,
			publisher_custom_list_value varchar(255) default NULL,
			publisher_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (publisher_custom_champ),
			KEY editorial_champ_list_value (publisher_custom_champ,publisher_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists publisher_custom_lists ");

		$rqt = "create table if not exists publisher_custom_values (
			publisher_custom_champ int(10) unsigned NOT NULL default 0,
			publisher_custom_origine int(10) unsigned NOT NULL default 0,
			publisher_custom_small_text varchar(255) default NULL,
			publisher_custom_text text,
			publisher_custom_integer int(11) default NULL,
			publisher_custom_date date default NULL,
			publisher_custom_float float default NULL,
			KEY editorial_custom_champ (publisher_custom_champ),
			KEY editorial_custom_origine (publisher_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists publisher_custom_values ");

		//NG - Champs perso collection
		$rqt = "create table if not exists collection_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table collection_custom ");

		$rqt = "create table if not exists collection_custom_lists (
			collection_custom_champ int(10) unsigned NOT NULL default 0,
			collection_custom_list_value varchar(255) default NULL,
			collection_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (collection_custom_champ),
			KEY editorial_champ_list_value (collection_custom_champ,collection_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists collection_custom_lists ");

		$rqt = "create table if not exists collection_custom_values (
			collection_custom_champ int(10) unsigned NOT NULL default 0,
			collection_custom_origine int(10) unsigned NOT NULL default 0,
			collection_custom_small_text varchar(255) default NULL,
			collection_custom_text text,
			collection_custom_integer int(11) default NULL,
			collection_custom_date date default NULL,
			collection_custom_float float default NULL,
			KEY editorial_custom_champ (collection_custom_champ),
			KEY editorial_custom_origine (collection_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists collection_custom_values ");

		//NG - Champs perso subcollection
		$rqt = "create table if not exists subcollection_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table subcollection_custom ");

		$rqt = "create table if not exists subcollection_custom_lists (
			subcollection_custom_champ int(10) unsigned NOT NULL default 0,
			subcollection_custom_list_value varchar(255) default NULL,
			subcollection_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (subcollection_custom_champ),
			KEY editorial_champ_list_value (subcollection_custom_champ,subcollection_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists subcollection_custom_lists ");

		$rqt = "create table if not exists subcollection_custom_values (
			subcollection_custom_champ int(10) unsigned NOT NULL default 0,
			subcollection_custom_origine int(10) unsigned NOT NULL default 0,
			subcollection_custom_small_text varchar(255) default NULL,
			subcollection_custom_text text,
			subcollection_custom_integer int(11) default NULL,
			subcollection_custom_date date default NULL,
			subcollection_custom_float float default NULL,
			KEY editorial_custom_champ (subcollection_custom_champ),
			KEY editorial_custom_origine (subcollection_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists subcollection_custom_values ");

		//NG - Champs perso serie
		$rqt = "create table if not exists serie_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table serie_custom ");

		$rqt = "create table if not exists serie_custom_lists (
			serie_custom_champ int(10) unsigned NOT NULL default 0,
			serie_custom_list_value varchar(255) default NULL,
			serie_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (serie_custom_champ),
			KEY editorial_champ_list_value (serie_custom_champ,serie_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists serie_custom_lists ");

		$rqt = "create table if not exists serie_custom_values (
			serie_custom_champ int(10) unsigned NOT NULL default 0,
			serie_custom_origine int(10) unsigned NOT NULL default 0,
			serie_custom_small_text varchar(255) default NULL,
			serie_custom_text text,
			serie_custom_integer int(11) default NULL,
			serie_custom_date date default NULL,
			serie_custom_float float default NULL,
			KEY editorial_custom_champ (serie_custom_champ),
			KEY editorial_custom_origine (serie_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists serie_custom_values ");

		//NG - Champs perso tu
		$rqt = "create table if not exists tu_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table tu_custom ");

		$rqt = "create table if not exists tu_custom_lists (
			tu_custom_champ int(10) unsigned NOT NULL default 0,
			tu_custom_list_value varchar(255) default NULL,
			tu_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (tu_custom_champ),
			KEY editorial_champ_list_value (tu_custom_champ,tu_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists tu_custom_lists ");

		$rqt = "create table if not exists tu_custom_values (
			tu_custom_champ int(10) unsigned NOT NULL default 0,
			tu_custom_origine int(10) unsigned NOT NULL default 0,
			tu_custom_small_text varchar(255) default NULL,
			tu_custom_text text,
			tu_custom_integer int(11) default NULL,
			tu_custom_date date default NULL,
			tu_custom_float float default NULL,
			KEY editorial_custom_champ (tu_custom_champ),
			KEY editorial_custom_origine (tu_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists tu_custom_values ");

		//NG - Champs perso indexint
		$rqt = "create table if not exists indexint_custom (
			idchamp int(10) unsigned NOT NULL auto_increment,
			num_type int unsigned not null default 0,
			name varchar(255) NOT NULL default '',
			titre varchar(255) default NULL,
			type varchar(10) NOT NULL default 'text',
			datatype varchar(10) NOT NULL default '',
			options text,
			multiple int(11) NOT NULL default 0,
			obligatoire int(11) NOT NULL default 0,
			ordre int(11) default NULL,
			search INT(1) unsigned NOT NULL DEFAULT 0,
			export INT(1) unsigned NOT NULL DEFAULT 0,
			exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
			pond int not null default 100,
			opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
		echo traite_rqt($rqt,"create table indexint_custom ");

		$rqt = "create table if not exists indexint_custom_lists (
			indexint_custom_champ int(10) unsigned NOT NULL default 0,
			indexint_custom_list_value varchar(255) default NULL,
			indexint_custom_list_lib varchar(255) default NULL,
			ordre int(11) default NULL,
			KEY editorial_custom_champ (indexint_custom_champ),
			KEY editorial_champ_list_value (indexint_custom_champ,indexint_custom_list_value)) " ;
		echo traite_rqt($rqt,"create table if not exists indexint_custom_lists ");

		$rqt = "create table if not exists indexint_custom_values (
			indexint_custom_champ int(10) unsigned NOT NULL default 0,
			indexint_custom_origine int(10) unsigned NOT NULL default 0,
			indexint_custom_small_text varchar(255) default NULL,
			indexint_custom_text text,
			indexint_custom_integer int(11) default NULL,
			indexint_custom_date date default NULL,
			indexint_custom_float float default NULL,
			KEY editorial_custom_champ (indexint_custom_champ),
			KEY editorial_custom_origine (indexint_custom_origine)) " ;
		echo traite_rqt($rqt,"create table if not exists indexint_custom_values ");

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
		//echo form_relance ("v5.12");
		//break;

//case "v5.12":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//DG - parametre pour forcer l'exécution des procédures
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='procs_force_execution' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
		VALUES (0, 'pmb', 'procs_force_execution', '0', 'Permettre le forçage de l\'exécution des procédures', '',0) ";
			echo traite_rqt($rqt, "insert pmb_procs_force_execution=0 into parametres");
			$rqt = "update users set rights=rights+131072 where rights<131072 and userid=1 ";
			echo traite_rqt($rqt, "update users add editions forcing rights where super user ");
		}

		//NG - ajout facette en dsi
		$rqt = "ALTER TABLE bannettes ADD group_type int unsigned NOT NULL default 0 AFTER notice_tpl";
		echo traite_rqt($rqt,"alter table bannettes add group_type");

		$rqt = "CREATE TABLE if not exists bannette_facettes (
			num_ban_facette int unsigned NOT NULL default 0,
			ban_facette_critere int(5) not null default 0,
			ban_facette_ss_critere int(5) not null default 0,
			ban_facette_order int(1) not null default 0,
			KEY bannette_facettes_key (num_ban_facette,ban_facette_critere,ban_facette_ss_critere)) " ;
		echo traite_rqt($rqt,"CREATE TABLE bannette_facettes");

		//DB - L'authentification Digest impose une valeur, ce qui n'est pas le cas avec une authentification externe
		$rqt= "alter table empr add empr_digest varchar(255) not null default '' after empr_password";
		echo traite_rqt($rqt,"alter table empr add empr_digest");

		//AB
		$rqt = "UPDATE users SET value_deflt_relation=CONCAT(value_deflt_relation,'-up') WHERE value_deflt_relation!='' AND value_deflt_relation NOT LIKE '%-%'";
		echo traite_rqt($rqt, 'UPDATE users SET value_deflt_relation=CONCAT(value_deflt_relation,"-up")');

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
		//echo form_relance ("v5.13");
		//break;

//case "v5.13":
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+

		//AB parametre OPAC pour activer ou non le drag and drop si notice_depliable != 2
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='draggable' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'draggable', '1', 'Permet d\'activer le glisser déposer dans le panier pour l\'affichage des notices à l\'OPAC', 'e_aff_notice',0) ";
			echo traite_rqt($rqt, "insert opac_draggable=1 into parametres");
		}

		//DG - Modification de la longueur du champ description de la table opac_liste_lecture
		$rqt = "ALTER TABLE opac_liste_lecture MODIFY description TEXT ";
		echo traite_rqt($rqt,"alter table opac_liste_lecture modify description");

		//DB - Ajout d'un champ timestamp dans la table acces_user_2
		@pmb_mysql_query("describe acces_usr_2",$dbh);
		if (!pmb_mysql_error($dbh)) {
			$rqt = "ALTER IGNORE TABLE acces_usr_2 ADD updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ";
			echo traite_rqt($rqt,"alter table acces_usr_2 add field updated");
		}

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
		//echo form_relance ("v5.14");
		//break;

//	case "v5.14": 
		
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		// +-------------------------------------------------+
		// MB - Indexer la colonne num_renvoi_voir de la table noeuds
		$rqt = "ALTER TABLE noeuds DROP INDEX i_num_renvoi_voir";
		echo traite_rqt($rqt,"ALTER TABLE noeuds DROP INDEX i_num_renvoi_voir");
		$rqt = "ALTER TABLE noeuds ADD INDEX i_num_renvoi_voir (num_renvoi_voir)";
		echo traite_rqt($rqt,"ALTER TABLE noeuds ADD INDEX i_num_renvoi_voir (num_renvoi_voir)");

		$rqt="update parametres set comment_param='Liste des id de template de notice pour ajouter des onglets personnalisés en affichage de notice\nExemple: 1,3,ISBD,PUBLIC\nLe paramètre notices_format doit être à 0 pour placer ISBD et PUBLIC' where type_param='opac' and sstype_param='notices_format_onglets' ";
		echo traite_rqt($rqt,"update opac notices_format_onglets comments in parametres") ;

		$rqt = "update parametres set comment_param='0 : mode normal de recherche\n1 : Affiche directement le résultat de la recherche tous les champs sans passer par la présentation du niveau 1 de recherche \n2 : Affiche directement le résultat de la recherche tous les champs sans passer par la présentation du niveau 1 de recherche sans faire de recherche intermédaire'  where type_param='opac' and sstype_param='autolevel2' ";
		echo traite_rqt($rqt,"update opac_autolevel comments in parametres");


		//Création des tables pour le portfolio
		$rqt = "create table cms_collections (
			id_collection int unsigned not null auto_increment primary key,
			collection_title varchar(255) not null default '',
			collection_description text not null,
			collection_num_parent int not null default 0,
			collection_num_storage int not null default 0,
			index i_cms_collection_title(collection_title)
		)";
		echo traite_rqt($rqt,"create table cms_collections") ;
		$rqt = "create table cms_documents (
			id_document int unsigned not null auto_increment primary key,
			document_title varchar(255) not null default '',
			document_description text not null,
			document_filename varchar(255) not null default '',
			document_mimetype varchar(100) not null default '',
			document_filesize int not null default 0,
			document_vignette mediumblob not null default '',
			document_url text not null,
			document_path varchar(255) not null default '',
			document_create_date date not null default '0000-00-00',
			document_num_storage int not null default 0,
			document_type_object varchar(255) not null default '',
			document_num_object int not null default 0,
			index i_cms_document_title(document_title)
		)";
		echo traite_rqt($rqt,"create table cms_documents") ;
		$rqt = "create table storages (
			id_storage int unsigned not null auto_increment primary key,
			storage_name varchar(255) not null default '',
			storage_class varchar(255) not null default '',
			storage_params text not null,
			index i_storage_class(storage_class)
		)";
		echo traite_rqt($rqt,"create table storages") ;
		$rqt = "create table cms_documents_links (
			document_link_type_object varchar(255) not null default '',
			document_link_num_object int not null default 0,
			document_link_num_document int not null default 0,
			primary key(document_link_type_object,document_link_num_object,document_link_num_document)
		)";
		echo traite_rqt($rqt,"create table cms_documents_links") ;

		// FT - Ajout des paramètres pour forcer les tags meta pour les moteurs de recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='meta_description' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('opac','meta_description','','Contenu du meta tag description pour les moteurs de recherche','b_aff_general',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres opac_meta_description");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='meta_keywords' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('opac','meta_keywords','','Contenu du meta tag keywords pour les moteurs de recherche','b_aff_general',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres opac_meta_keywords");
		}
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='meta_author' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('opac','meta_author','','Contenu du meta tag author pour les moteurs de recherche','b_aff_general',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres opac_meta_author");
		}

		//DG - autoriser le code HTML dans les cotes exemplaires
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='html_allow_expl_cote' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'html_allow_expl_cote', '0', 'Autoriser le code HTML dans les cotes exemplaires ? \n 0 : non \n 1', '',0) ";
			echo traite_rqt($rqt, "insert pmb_html_allow_expl_cote=0 into parametres");
		}

		//maj valeurs possibles pour empr_sort_rows
		$rqt = "update parametres set comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+prénom \n b: code-barres \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #n : id des champs personnalisés' where type_param= 'empr' and sstype_param='sort_rows' ";
		echo traite_rqt($rqt,"update empr_sort_rows into parametres");

		//DB - création table index pour le magasin rdf
		$rqt = "create table rdfstore_index (
					num_triple int(10) unsigned not null default 0,
					subject_uri text not null ,
					predicat_uri text not null ,
					num_object int(10) unsigned not null default 0 primary key,
					object_val text not null ,
					object_index text not null ,
					object_lang char(5) not null default ''
		) default charset=utf8 ";
		echo traite_rqt($rqt,"create table rdfstore_index");

		// MB - Création d'une table de cache pour les cadres du portail pour accélérer l'affichage
		$rqt = "DROP TABLE IF EXISTS cms_cache_cadres";
		echo traite_rqt($rqt,"DROP TABLE IF EXISTS cms_cache_cadres");
		$rqt = "CREATE TABLE  cms_cache_cadres (
			cache_cadre_hash VARCHAR( 32 ) NOT NULL,
			cache_cadre_type_content VARCHAR(30) NOT NULL,
			cache_cadre_create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
			cache_cadre_content MEDIUMTEXT NOT NULL,
			PRIMARY KEY (  cache_cadre_hash, cache_cadre_type_content )
		);";
		echo traite_rqt($rqt,"CREATE TABLE  cms_cache_cadres");

		$rqt = "ALTER TABLE rdfstore_index ADD subject_type TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER  subject_uri";
		echo traite_rqt($rqt,"alter table rdfstore_index add subject_type");

		// Info de réindexation
		$rqt = " select 1 " ;
		echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base > Réindexer le magasin RDF</a></b> ") ;

		// AP - Ajout de l'ordre dans les rubriques et les articles
		$rqt = "ALTER TABLE cms_sections ADD section_order INT UNSIGNED default 0";
		echo traite_rqt($rqt,"alter table cms_sections add section_order");

		$rqt = "ALTER TABLE cms_articles ADD article_order INT UNSIGNED default 0";
		echo traite_rqt($rqt,"alter table cms_articles add article_order");

		//DG - CSS add on en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='default_style_addon' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'default_style_addon', '', 'Ajout de styles CSS aux feuilles déjà incluses ?\n Ne mettre que le code CSS, exemple:  body {background-color: #FF0000;}', '',0) ";
			echo traite_rqt($rqt, "insert pmb_default_style_addon into parametres");
		}

		// NG - circulation sans retour
		$rqt = "ALTER TABLE serialcirc ADD serialcirc_no_ret INT UNSIGNED not null default 0";
		echo traite_rqt($rqt,"alter table serialcirc add serialcirc_no_ret");

		// NG - personnalisation d'impression de la liste de circulation des périodiques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_subst' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('pmb','serialcirc_subst','','Nom du fichier permettant de personnaliser l\'impression de la liste de circulation des périodiques','',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres pmb_serialcirc_subst");
		}

		//MB - Augmenter la taille du libellé de groupe
		$rqt = "ALTER TABLE groupe CHANGE libelle_groupe libelle_groupe VARCHAR(255) NOT NULL";
		echo traite_rqt($rqt,"alter table groupe");

		//AR - Ajout d'un type de cache pour un cadre
		$rqt = "alter table cms_cadres add cadre_modcache varchar(255) not null default 'get_post_view'";
		echo traite_rqt($rqt,"alter table cms_cadres add cadre_modcache");

		//DG - Type de relation par défaut en création de périodique
		$rqt = "ALTER TABLE users ADD value_deflt_relation_serial VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_serial after value_deflt_relation");

		//DG - Type de relation par défaut en création de bulletin
		$rqt = "ALTER TABLE users ADD value_deflt_relation_bulletin VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation_serial";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_bulletin after value_deflt_relation_serial");

		//DG - Type de relation par défaut en création d'article
		$rqt = "ALTER TABLE users ADD value_deflt_relation_analysis VARCHAR( 20 ) NOT NULL DEFAULT '' AFTER value_deflt_relation_bulletin";
		echo traite_rqt($rqt,"ALTER TABLE users ADD default value_deflt_relation_analysis after value_deflt_relation_bulletin");

		//DG - Mise à jour des valeurs en fonction du type de relation par défaut en création de notice, si la valeur est vide !
		if ($res = pmb_mysql_query("select userid, value_deflt_relation,value_deflt_relation_serial,value_deflt_relation_bulletin,value_deflt_relation_analysis from users")){
			while ( $row = pmb_mysql_fetch_object($res)) {
				if ($row->value_deflt_relation_serial == '') pmb_mysql_query("update users set value_deflt_relation_serial='".$row->value_deflt_relation."' where userid=".$row->userid);
				if ($row->value_deflt_relation_bulletin == '') pmb_mysql_query("update users set value_deflt_relation_bulletin='".$row->value_deflt_relation."' where userid=".$row->userid);
				if ($row->value_deflt_relation_analysis == '') pmb_mysql_query("update users set value_deflt_relation_analysis='".$row->value_deflt_relation."' where userid=".$row->userid);
			}
		}

		//DG - Activer le prêt court par défaut
		$rqt = "ALTER TABLE users ADD deflt_short_loan_activate INT(1) UNSIGNED DEFAULT 0 NOT NULL ";
		echo traite_rqt($rqt, "ALTER TABLE users ADD deflt_short_loan_activate");

		//DG - Alerter l'utilisateur par mail des nouvelles inscriptions en OPAC ?
		$rqt = "ALTER TABLE users ADD user_alert_subscribemail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_demandesmail";
		echo traite_rqt($rqt,"ALTER TABLE users add user_alert_subscribemail default 0");

		//DB - Modification commentaire autolevel
		$rqt = "update parametres set comment_param='0 : mode normal de recherche.\n1 : Affiche le résultat de la recherche tous les champs après calcul du niveau 1 de recherche.\n2 : Affiche directement le résultat de la recherche tous les champs sans passer par le calcul du niveau 1 de recherche.' where type_param= 'opac' and sstype_param='autolevel2' ";
		echo traite_rqt($rqt,"update parameter comment for opac_autolevel2");

		//AR - Ajout du paramètres pour la durée de validité du cache des cadres du potail
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='cache_ttl' "))==0){
			$rqt = "insert into parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'cms', 'cache_ttl', '1800', 'durée de vie du cache des cadres du portail (en secondes)', '',0) ";
			echo traite_rqt($rqt, "insert cms_caches_ttl into parametres");
		}

		//DG - Périodicité : Jour du mois
		$rqt = "ALTER TABLE planificateur ADD perio_jour_mois VARCHAR( 128 ) DEFAULT '*' AFTER perio_minute";
		echo traite_rqt($rqt,"ALTER TABLE planificateur ADD perio_jour_mois DEFAULT * after perio_minute");

		//DG - Replanifier la tâche en cas d'échec
		$rqt = "alter table taches_type add restart_on_failure int(1) UNSIGNED DEFAULT 0 NOT NULL";
		echo traite_rqt($rqt,"alter table taches_type add restart_on_failure");

		//DG - Alerte mail en cas d'échec de la tâche
		$rqt = "alter table taches_type add alert_mail_on_failure VARCHAR(255) DEFAULT ''";
		echo traite_rqt($rqt,"alter table taches_type add alert_mail_on_failure");

		//DG - Préremplissage de la vignette des dépouillements
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serial_thumbnail_url_article' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'serial_thumbnail_url_article', '0', 'Préremplissage de l\'url de la vignette des dépouillements avec l\'url de la vignette de la notice mère en catalogage des périodiques ? \n 0 : Non \n 1 : Oui', '',0) ";
			echo traite_rqt($rqt, "insert pmb_serial_thumbnail_url_article=0 into parametres");
		}

		//DG - Délai en millisecondes entre les mails envoyés lors d'un envoi groupé
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_delay' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','mail_delay','0','Temps d\'attente en millisecondes entre chaque mail envoyé lors d\'un envoi groupé. \n 0 : Pas d\'attente', '',0)" ;
			echo traite_rqt($rqt,"insert pmb_mail_delay=0 into parametres") ;
		}

		//DG - Timeout cURL sur la vérifications des liens
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='curl_timeout' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'pmb','curl_timeout','5','Timeout cURL (en secondes) pour la vérification des liens', '',1)" ;
			echo traite_rqt($rqt,"insert pmb_curl_timeout=0 into parametres") ;
		}

		//DG - Autoriser la prolongation groupée pour tous les membres
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='allow_prolong_members_group' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'empr', 'allow_prolong_members_group', '0', 'Autoriser la prolongation groupée des adhésions des membres d\'un groupe ? \n 0 : Non \n 1 : Oui', '',0) ";
			echo traite_rqt($rqt, "insert empr_allow_prolong_members_group=0 into parametres");
		}


		//DB - ajout d'un index stem+lang sur la table words
		$rqt = "alter table words add index i_stem_lang(stem, lang)";
		echo traite_rqt($rqt, "alter table words add index i_stem_lang");

		//NG - Autoindex
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_index_notice_fields' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'thesaurus', 'auto_index_notice_fields', '', 'Liste des champs de notice à utiliser pour l\'indexation automatique, séparés par une virgule.\nLes noms des champs sont les identifiants des champs listés dans le fichier XML pmb/notice/notice.xml\nExemple: tit1,n_resume', 'categories',0) ";
			echo traite_rqt($rqt, "insert thesaurus_auto_index_notice_fields='' into parametres");
		}

		//NG - Autoindex: surchage du parametrage de la recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='auto_index_search_param' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'thesaurus', 'auto_index_search_param', '', 'Surchage des paramètres de recherche de l\'indexation automatique.\n\nSyntaxe: param=valeur;\n\nListe des paramètres:\nautoindex_max_up_distance,\nautoindex_max_down_distance,\nautoindex_stem_ratio,\nautoindex_see_also_ratio,\nautoindex_max_down_ratio,\nautoindex_max_up_ratio,\nautoindex_deep_ratio,\nautoindex_distance_ratio,\nmax_relevant_words,\nmax_relevant_terms', 'categories',0) ";
			echo traite_rqt($rqt, "insert thesaurus_auto_index_search_param='' into parametres");
		}

		//DG - Choix par défaut pour la prolongation des lecteurs
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='abonnement_default_debit' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'empr', 'abonnement_default_debit', '0', 'Choix par défaut pour la prolongation des lecteurs. \n 0 : Ne pas débiter l\'abonnement \n 1 : Débiter l\'abonnement sans la caution \n 2 : Débiter l\'abonnement et la caution') " ;
			echo traite_rqt($rqt,"insert empr_abonnement_default_debit = 0 into parametres");
		}

		//NG - Ajout indexation_lang dans la table notices
		$rqt = "ALTER TABLE notices ADD indexation_lang VARCHAR( 20 ) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"ALTER TABLE notices ADD indexation_lang VARCHAR( 20 ) NOT NULL DEFAULT '' ");

		$rqt = "alter table users add xmlta_indexation_lang varchar(10) NOT NULL DEFAULT '' after deflt_integration_notice_statut";
		echo traite_rqt($rqt,"alter table users add xmlta_indexation_lang");

		//NG - Ajout ico_notice
		$rqt = "ALTER TABLE connectors_sources ADD ico_notice VARCHAR( 255 ) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD ico_notice VARCHAR( 255 ) NOT NULL DEFAULT '' ");

		//NG - liste des sources externes d'enrichissements à intégrer dans le a2z
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_enrichissements' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'perio_a2z_enrichissements', '0', 'Affichage de sources externes d\'enrichissement dans le navigateur de périodiques.\nListe des couples (séparé par une virgule) Id de connecteur, Id de source externe d\'enrichissement, séparé par un point virgule\nExemple:\n6,4;6,5', 'c_recherche',0) ";
			echo traite_rqt($rqt, "insert opac_perio_a2z_enrichissements=0 into parametres");
		}

		//DG - Modification taille du champ empr_msg de la table empr
		$rqt = "ALTER TABLE empr MODIFY empr_msg TEXT null " ;
		echo traite_rqt($rqt,"alter table empr modify empr_msg");

		//DG - Identifiant du template de notice par défaut en impression de panier
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_template_default' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'opac', 'print_template_default', '0', 'En impression de panier, identifiant du template de notice utilisé par défaut. Si vide ou à 0, le template classique est utilisé', 'a_general', 0)";
			echo traite_rqt($rqt,"insert opac_print_template_default='0' into parametres");
		}

		//DG - Paramètre pour afficher le permalink de la notice dans le detail de la notice
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='show_permalink' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'show_permalink', '0', 'Afficher le lien permanent de l\'OPAC en gestion ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_show_permalink=0 into parameters");
		}

		//AB - Ajout du champ pour choix d'un template d'export pour les flux RSS
		$rqt = "ALTER TABLE rss_flux ADD tpl_rss_flux INT(11) UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE rss_flux ADD tpl_rss_flux INT(11) UNSIGNED NOT NULL DEFAULT 0 ");

		//DG - Parametre pour afficher ou non l'emprunteur précédent dans la fiche exemplaire
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='expl_show_lastempr' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'expl_show_lastempr', '1', 'Afficher l\'emprunteur précédent sur la fiche exemplaire ? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_expl_show_lastempr=1 into parameters");
		}

		// NG - Gestion de caisses
		$rqt = "CREATE TABLE cashdesk (
			cashdesk_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			cashdesk_name VARCHAR(255) NOT NULL DEFAULT '',
			cashdesk_autorisations VARCHAR(255) NOT NULL DEFAULT '',
			cashdesk_transactypes VARCHAR(255) NOT NULL DEFAULT '',
			cashdesk_cashbox INT UNSIGNED NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE cashdesk");

		$rqt = "CREATE TABLE cashdesk_locations (
			cashdesk_loc_cashdesk_num  INT UNSIGNED NOT NULL default 0,
			cashdesk_loc_num  INT UNSIGNED NOT NULL default 0,
			PRIMARY KEY(cashdesk_loc_cashdesk_num,cashdesk_loc_num)
			)";
		echo traite_rqt($rqt,"CREATE TABLE cashdesk_locations");

		$rqt = "CREATE TABLE cashdesk_sections (
			cashdesk_section_cashdesk_num  INT UNSIGNED NOT NULL default 0,
			cashdesk_section_num  INT UNSIGNED NOT NULL default 0,
			PRIMARY KEY(cashdesk_section_cashdesk_num,cashdesk_section_num)
			)";
		echo traite_rqt($rqt,"CREATE TABLE cashdesk_sections");

		// NG - Gestion de type de transactions
		$rqt = "CREATE TABLE  transactype (
			transactype_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			transactype_name VARCHAR(255) NOT NULL DEFAULT '',
			transactype_quick_allowed INT UNSIGNED NOT NULL default 0,
			transactype_unit_price FLOAT NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE transactype");

		// NG - Mémorisation du payement des transactions
		$rqt = "CREATE TABLE transacash (
			transacash_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			transacash_empr_num INT UNSIGNED NOT NULL default 0,
			transacash_desk_num INT UNSIGNED NOT NULL default 0,
			transacash_user_num INT UNSIGNED NOT NULL default 0,
			transacash_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			transacash_sold FLOAT NOT NULL default 0,
			transacash_collected FLOAT NOT NULL default 0,
			transacash_rendering FLOAT NOT NULL default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE transacash");

		// NG - Activer la gestion de caisses en gestion financière
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='gestion_financiere_caisses' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'pmb', 'gestion_financiere_caisses', '0', 'Activer la gestion de caisses en gestion financière? \n 0 : Non.\n 1 : Oui.', '',0) ";
			echo traite_rqt($rqt, "insert pmb_gestion_financiere_caisses=0 into parameters");
		}

		$rqt = "ALTER TABLE transactions ADD transactype_num INT UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transactions ADD transactype_num INT UNSIGNED NOT NULL DEFAULT 0 ");

		$rqt = "ALTER TABLE transactions ADD cashdesk_num INT UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transactions ADD cashdesk_num INT UNSIGNED NOT NULL DEFAULT 0 ");

		$rqt = "ALTER TABLE transactions ADD transacash_num INT UNSIGNED NOT NULL DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE transactions ADD transacash_num INT UNSIGNED NOT NULL DEFAULT 0 ");

		$rqt = "alter table users add deflt_cashdesk int NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter table users add deflt_cashdesk");

		$rqt= "alter table sessions add notifications text";
		echo traite_rqt($rqt,"alter table sessions add notifications");

		// AP - Ajout du paramètre de segmentation des documents numériques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='diarization_docnum' "))==0){
			$rqt="insert into parametres(type_param,sstype_param,valeur_param,comment_param,section_param,gestion) values('pmb','diarization_docnum',0,'Activer la segmentation des documents numériques vidéo ou audio 0 : non activée 1 : activée','',0)";
			echo traite_rqt($rqt,"INSERT INTO parametres diarization_docnum");
		}

		// AP - Ajout de la table explnum_speakers
		$rqt = "CREATE TABLE explnum_speakers (
			explnum_speaker_id int unsigned not null auto_increment primary key,
			explnum_speaker_explnum_num int unsigned not null default 0,
			explnum_speaker_speaker_num varchar(10) not null default '',
			explnum_speaker_gender varchar(1) default '',
			explnum_speaker_author int unsigned not null default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE explnum_speakers");
		$rqt = "alter table explnum_speakers drop index i_ensk_explnum_num";
		echo traite_rqt($rqt,"alter table explnum_speakers drop index i_ensk_explnum_num");
		$rqt = "alter table explnum_speakers add index i_ensk_explnum_num(explnum_speaker_explnum_num)";
		echo traite_rqt($rqt,"alter table explnum_speakers add index i_ensk_explnum_num");
		$rqt = "alter table explnum_speakers drop index i_ensk_author";
		echo traite_rqt($rqt,"alter table explnum_speakers drop index i_ensk_author");
		$rqt = "alter table explnum_speakers add index i_ensk_author(explnum_speaker_author)";
		echo traite_rqt($rqt,"alter table explnum_speakers add index i_ensk_author");


		// AP - Ajout de la table explnum_segments
		$rqt = "CREATE TABLE  explnum_segments (
			explnum_segment_id int unsigned not null auto_increment primary key,
			explnum_segment_explnum_num int unsigned not null default 0,
			explnum_segment_speaker_num int unsigned not null default 0,
			explnum_segment_start double not null default 0,
			explnum_segment_duration double not null default 0,
			explnum_segment_end double not null default 0
			)";
		echo traite_rqt($rqt,"CREATE TABLE explnum_segments");
		$rqt = "alter table explnum_segments drop index i_ensg_explnum_num";
		echo traite_rqt($rqt,"alter table explnum_segments drop index i_ensg_explnum_num");
		$rqt = "alter table explnum_segments add index i_ensg_explnum_num(explnum_segment_explnum_num)";
		echo traite_rqt($rqt,"alter table explnum_segments add index i_ensg_explnum_num");
		$rqt = "alter table explnum_segments drop index i_ensg_speaker";
		echo traite_rqt($rqt,"alter table explnum_segments drop index i_ensg_speaker");
		$rqt = "alter table explnum_segments add index i_ensg_speaker(explnum_segment_speaker_num)";
		echo traite_rqt($rqt,"alter table explnum_segments add index i_ensg_speaker");

		//DG - Modification de l'emplacement du paramètre bannette_notices_template dans la zone DSI
		$rqt = "update parametres set type_param='dsi',section_param='' where type_param='opac' and sstype_param='bannette_notices_template' ";
		echo traite_rqt($rqt,"update parametres set bannette_notices_template");

		//DG - Retour à la précédente forme de tri
		$rqt = "update parametres set comment_param='Tri par défaut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))' WHERE type_param='opac' AND sstype_param='default_sort'";
		echo traite_rqt($rqt,"update comment for param opac_default_sort");

		//DG - Mode d'application d'un tri - Liste de tris pré-enregistrés
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort_list' "))==0){
	 		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'default_sort_list', '0 d_num_6,c_text_28;d_text_7', 'Afficher la liste déroulante de sélection d\'un tri ? \n 0 : Non \n 1 : Oui \nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle;d_text_7|Libelle 2;c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)','d_aff_recherche',0) " ;
	 		echo traite_rqt($rqt,"insert opac_default_sort_list = 0 d_num_6,c_text_28;d_text_7 into parametres");
	 	}

	 	//DG - Afficher le libellé du tri appliqué par défaut en résultat de recherche
	 	if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='default_sort_display' "))==0){
	 		$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'default_sort_display', '0', 'Afficher le libellé du tri appliqué par défaut en résultat de recherche ? \n 0 : Non \n 1 : Oui','d_aff_recherche',0) " ;
	 		echo traite_rqt($rqt,"insert opac_default_sort_display = 0 into parametres");
	 	}

		// NG - Affichage des bannettes privées en page d'accueil de l'Opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_bannettes' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'opac','show_bannettes','0','Affichage des bannettes en page d\'accueil OPAC.\n 0 : Non.\n 1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_bannettes into parametres") ;
		}

		// AB - Affichage des facettes en AJAX
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='facettes_ajax' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'opac','facettes_ajax','1','Charger les facettes en ajax\n0 : non\n1 : oui','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_facettes_ajax into parametres") ;
		}

		// DB - Modification index sur table notices_mots_global_index
		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800", $dbh);
		if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
			$rqt = "truncate table notices_fields_global_index";
			echo traite_rqt($rqt,"truncate table notices_fields_global_index");

			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");

			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
		}
		$rqt = 'alter table notices_mots_global_index drop primary key';
		echo traite_rqt($rqt, 'alter table notices_mots_global_index drop primary key');
		$rqt = 'alter table notices_mots_global_index add primary key (id_notice, code_champ, num_word, position, code_ss_champ)';
		echo traite_rqt($rqt, 'alter table notices_mots_global_index add primary key');

		//AB
		$rqt = "ALTER TABLE cms_build drop INDEX cms_build_index";
		echo traite_rqt($rqt,"alter cms_build drop index cms_build_index ");
		$rqt = "ALTER TABLE cms_build ADD INDEX cms_build_index (build_version_num , build_obj)";
		echo traite_rqt($rqt,"alter cms_build add index cms_build_index ON build_version_num , build_obj");

		// AR - Paramètres pour ne pas prendre en compte les mots vides en tous les champs à l'OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_all_keep_empty_words' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'opac','search_all_keep_empty_words','1','Conserver les mots vides pour les autorités dans la recherche tous les champs\n0 : non\n1 : oui','c_recherche',0)" ;
			echo traite_rqt($rqt,"insert opac_search_all_keep_empty_words into parametres") ;
		}

		// NG - Paramètre pour activer le piège en prêt si l'emprunteur a déjà emprunté l'exemplaire
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_already_loaned' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES(0,'pmb','pret_already_loaned','0','Activer le piège en prêt si le document a déjà été emprunté par le lecteur. Nécessite l\'activation de l\'archivage des prêts\n0 : non\n1 : oui','',0)" ;
			echo traite_rqt($rqt,"insert pmb_pret_already_loaned into parametres") ;
		}

		//DB - Ajout d'index
		set_time_limit(0);
		pmb_mysql_query("set wait_timeout=28800", $dbh);

		$rqt = "alter table abts_abts drop index i_date_fin";
		echo traite_rqt($rqt,"alter table abts_abts drop index i_date_fin");
		$rqt = "alter table abts_abts add index i_date_fin (date_fin)";
		echo traite_rqt($rqt,"alter table abts_abts add index i_date_fin");

		$rqt = "alter table cms_editorial_types drop index i_editorial_type_element";
		echo traite_rqt($rqt,"alter table cms_editorial_types drop index i_editorial_type_element");
		$rqt = "alter table cms_editorial_types add index i_editorial_type_element (editorial_type_element)";
		echo traite_rqt($rqt,"alter table cms_editorial_types add index i_editorial_type_element");

		$rqt = "alter table cms_editorial_custom drop index i_num_type";
		echo traite_rqt($rqt,"alter table cms_editorial_custom drop index i_num_type");
		$rqt = "alter table cms_editorial_custom add index i_num_type (num_type)";
		echo traite_rqt($rqt,"alter table cms_editorial_custom add index i_num_type");

		$rqt = "alter table cms_build drop index i_build_parent_build_version_num";
		echo traite_rqt($rqt,"alter table cms_build drop index i_build_parent_build_version_num");
		$rqt = "alter table cms_build add index i_build_parent_build_version_num (build_parent,build_version_num)";
		echo traite_rqt($rqt,"alter table cms_build add index i_build_parent_build_version_num");

		$rqt = "alter table cms_build drop index i_build_type_build_version_num";
		echo traite_rqt($rqt,"alter table cms_build drop index i_build_type_build_version_num");
		$rqt = "alter table cms_build add index i_build_parent_build_version_num (build_type,build_version_num)";
		echo traite_rqt($rqt,"alter table cms_build add index i_build_type_build_version_num");

		$rqt = "alter table cms_build drop index i_build_obj_build_version_num";
		echo traite_rqt($rqt,"alter table cms_build drop index i_build_obj_build_version_num");
		$rqt = "alter table cms_build add index i_build_obj_build_version_num (build_obj,build_version_num)";
		echo traite_rqt($rqt,"alter table cms_build add index i_build_obj_build_version_num");

		$rqt = "alter table notices_fields_global_index drop index i_code_champ_code_ss_champ";
		echo traite_rqt($rqt,"alter table notices_fields_global_index drop index i_code_champ_code_ss_champ");
		$rqt = "alter table notices_fields_global_index add index i_code_champ_code_ss_champ (code_champ,code_ss_champ)";
		echo traite_rqt($rqt,"alter table notices_fields_global_index add index i_code_champ_code_ss_champ");

		$rqt = "alter table notices_mots_global_index drop index i_code_champ_code_ss_champ_num_word";
		echo traite_rqt($rqt,"alter table notices_mots_global_index drop index i_code_champ_code_ss_champ_num_word");
		$rqt = "alter table notices_mots_global_index add index i_code_champ_code_ss_champ_num_word (code_champ,code_ss_champ,num_word)";
		echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_code_champ_code_ss_champ_num_word");

		// Activation des recherches exemplaires voisins
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_voisin_search' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'allow_voisin_search', '0', 'Activer la recherche des exemplaires dont la cote est proche:\n 0 : non \n 1 : oui', 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_allow_voisin_search='0' into parametres ");
		}

		// MHo - Paramètre pour indiquer le nombre de notices similaires à afficher à l'opac
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='nb_notices_similaires' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES (0, 'opac', 'nb_notices_similaires', '6', 'Nombre de notices similaires affichées lors du dépliage d\'une notice.\nValeur max = 6.','e_aff_notice',0)";
			echo traite_rqt($rqt,"insert opac_nb_notices_similaires='6' into parametres");
		}
		// MHo - Paramètre pour rendre indépendant l'affichage réduit des notices similaires par rapport aux notices pliées
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notice_reduit_format_similaire' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES (0, 'opac', 'notice_reduit_format_similaire', '1', 'Format d\'affichage des réduits de notices similaires :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice','e_aff_notice',0)";
			echo traite_rqt($rqt,"insert opac_notice_reduit_format_similaire='0' into parametres");
		}

		//AR - Paramètres d'écretage des résultats de recherche
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_noise_limit_type' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'search_noise_limit_type', '0', 'Ecrêter les résulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du résultat tout ce qui est en dessous de la moyenne - l\'écart-type\n2,ratio : Retirer du résultat tout ce qui est en dessous de la moyenne - un ratio de l\'écart-type (ex: 2,1.96)\n3,ratio : Retirer du résultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 élimine tout ce qui est inférieur à 25% de la plus forte pertinence)' , 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_search_noise_limit_type='0' into parametres ");
		}

		//AR - Prise en compte de la fréquence d'apparition d'un mot dans le fonds pour le calcul de pertinence
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_relevant_with_frequency' "))==0){
			$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'search_relevant_with_frequency', '0', 'Utiliser la fréquence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui' , 'c_recherche', '0')";
			echo traite_rqt($rqt,"insert opac_search_relevant_with_frequency='0' into parametres ");
		}

		//DG - Calcul de la prolongation d'adhésion à partir de la date de fin d'adhésion ou la date du jour
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='prolong_calc_date_adhes_depassee' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'empr', 'prolong_calc_date_adhes_depassee', '0', 'Si la date d\'adhésion est dépassée, le calcul de la prolongation se fait à partir de :\n 0 : la date de fin d\'adhésion\n 1 : la date du jour','',0) " ;
			echo traite_rqt($rqt,"insert empr_prolong_calc_date_adhes_depassee = 0 into parametres");
		}

		//DG - Modification du commentaire du paramètre pmb_notice_reduit_format pour les améliorations
		$rqt = "update parametres set comment_param = 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice' where type_param='pmb' and sstype_param='notice_reduit_format'";
		echo traite_rqt($rqt,"update parametre pmb_notice_reduit_format");

		//DG - Périodicité d'envoi par défaut en création de bannette privée (en jours)
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='bannette_priv_periodicite' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'opac', 'bannette_priv_periodicite', '15', 'Périodicité d\'envoi par défaut en création de bannette privée (en jours)','l_dsi',0) " ;
			echo traite_rqt($rqt,"insert opac_bannette_priv_periodicite = 15 into parametres");
		}

		//DG - Modification du commentaire opac_notices_format
		$rqt = "update parametres set comment_param='Format d\'affichage des notices en résultat de recherche \n 0 : Utiliser le paramètre notices_format_onglets \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 5 : ISBD et Public avec ISBD en premier \n 8 : Réduit (titre+auteurs) seul' where type_param='opac' and sstype_param='notices_format'" ;
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_notices_format") ;


		//DB - Modifications et ajout de commentaires pour les paramètres décrivant l'autoindexation
		$rqt = "UPDATE parametres SET valeur_param=replace(valeur_param,',',';'), comment_param = 'Liste des champs de notice à utiliser pour l\'indexation automatique.\n\n";
		$rqt.= "Syntaxe: nom_champ=poids_indexation;\n\n";
		$rqt.= "Les noms des champs sont ceux précisés dans le fichier XML \"pmb/includes/notice/notice.xml\"\n";
		$rqt.= "Le poids de l\'indexation est une valeur de 0.00 à 1. (Si rien n\'est précisé, le poids est de 1)\n\n";
		$rqt.= "Exemple :\n\n";
		$rqt.= "tit1=1.00;n_resume=0.5;' ";
		$rqt.= "WHERE type_param = 'thesaurus' and sstype_param='auto_index_notice_fields' ";
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for thesaurus_auto_index_notice_fields") ;

		$rqt = "UPDATE parametres SET comment_param = 'Surchage des paramètres de recherche de l\'indexation automatique.\n";
		$rqt.= "Syntaxe: param=valeur;\n\n";
		$rqt.= "Listes des parametres:\n\n";
		$rqt.= "max_relevant_words = 20 (nombre maximum de mots et de lemmes de la notice à prendre en compte pour le calcul)\n\n";
		$rqt.= "autoindex_deep_ratio = 0.05 (ratio sur la profondeur du terme dans le thésaurus)\n";
		$rqt.= "autoindex_stem_ratio = 0.80 (ratio de pondération des lemmes / aux mots)\n\n";
		$rqt.= "autoindex_max_up_distance = 2 (distance maximum de recherche dans les termes génériques du thésaurus)\n";
		$rqt.= "autoindex_max_up_ratio = 0.01 (pondération sur les termes génériques)\n\n";
		$rqt.= "autoindex_max_down_distance = 2 (distance maximum de recherche dans les termes spécifiques du thésaurus)\n";
		$rqt.= "autoindex_max_down_ratio = 0.01 (pondération sur les termes spécifiques)\n\n";
		$rqt.= "autoindex_see_also_ratio = 0.01 (surpondération sur les termes voir aussi du thésaurus)\n\n";
		$rqt.= "autoindex_distance_type = 1 (calcul de distance de 1 à 4)\n";
		$rqt.= "autoindex_distance_ratio = 0.50 (ratio de pondération sur la distance entre les mots trouvés et les termes d\'une expression du thésaurus)\n\n";
		$rqt.= "max_relevant_terms = 10 (nombre maximum de termes retournés)' ";
		$rqt.= "WHERE type_param = 'thesaurus' and sstype_param='auto_index_search_param' ";
		echo traite_rqt($rqt,"UPDATE parametres SET comment_param for thesaurus_auto_index_search_param") ;

		// MHo - Ajout des attributs de l'oeuvre dans la table des titres uniformes
		$rqt = "ALTER TABLE titres_uniformes ADD tu_num_author BIGINT(11) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_num_author");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_forme VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_forme");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_date VARCHAR(50) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_date");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_date_date DATE NOT NULL DEFAULT '0000-00-00' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_date_date");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_sujet VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_sujet");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_lieu VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_lieu");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_histoire TEXT NULL ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_histoire");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_caracteristique TEXT NULL ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_caracteristique");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_public VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_public");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_contexte TEXT NULL ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_contexte");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_coordonnees VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_coordonnees");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_equinoxe VARCHAR(255) NOT NULL DEFAULT '' ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_equinoxe");
		$rqt = "ALTER TABLE titres_uniformes ADD tu_completude INT(2) UNSIGNED NOT NULL DEFAULT 0 ";
		echo traite_rqt($rqt,"alter titres_uniformes add tu_completude");

		// AR - Retrait du paramètres juste commité : Activation des recherches exemplaires voisins
		$rqt="delete from parametres where type_param= 'opac' and sstype_param='allow_voisin_search'";
		echo traite_rqt($rqt,"delete from parametres opac_allow_voisin_search");

		// AR - Modification du paramètre opac_allow_simili
		$rqt="update parametres set comment_param = 'Activer les recherches similaires sur une notice :\n0 : Non\n1 : Activer la recherche \"Dans le même rayon\" et \"Peut-être aimerez-vous\"\n2 : Activer seulement la recherche \"Dans le même rayon\"\n3 : Activer seulement la recherche \"Peut-être aimerez-vous\"', section_param = 'e_aff_notice' where type_param='opac' and sstype_param='allow_simili_search'";
		echo traite_rqt($rqt,"update parametres set opac_allow_simili_search");

		// NG - Affichage des bannettes en page d'accueil de l'Opac	selon la banette
		$rqt = "ALTER TABLE bannettes ADD bannette_opac_accueil INT UNSIGNED NOT NULL default 0 ";
		echo traite_rqt($rqt,"alter table bannettes add bannette_opac_accueil");

		// AR - DSI abonné en page d'accueil
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_subscribed_bannettes' "))==0){
			$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES('opac','show_subscribed_bannettes',0,'Affichage des bannettes auxquelles le lecteur est abonné en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert opac_show_subscribed_bannettes=0 into parametres") ;
		}

		// AR - DSI publique sélectionné en page d'accueil
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='show_public_bannettes' "))==0){
			$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES('opac','show_public_bannettes',0,'Affichage des bannettes sélectionnées en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.','f_modules',0)" ;
			echo traite_rqt($rqt,"insert show_public_bannettes=0 into parametres") ;
		}

		// AR - Retrait du paramètre perio_a2z_enrichissements, on ne l'a jamais utilisé car on a finalement ramené le paramétrage par un connecteur
		$rqt="delete from parametres where type_param= 'opac' and sstype_param='perio_a2z_enrichissements'";
		echo traite_rqt($rqt,"delete from parametres opac_perio_a2z_enrichissements");

		//DG - Paramètre non utilisé
		$rqt = "delete from parametres where sstype_param='confirm_resa' and type_param='opac' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;

		//DG - Paramètre non utilisé
		$rqt = "delete from parametres where sstype_param='authors_aut_rec_per_page' and type_param='opac' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;

		// +-------------------------------------------------+
		echo "</table>";
		$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
		$res = pmb_mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;
		//echo form_relance ("v5.15");

	//break;
	//case "v.5.15":	
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// AB - Paramètre de modification du workflow d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='init_workflow' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES('demandes','init_workflow',0,'Initialisation du workflow de la demande.\n 0 : Validation avant tout\n 1 : Validation avant tout et attribution au validateur\n 2 : Attribution avant tout','',0)";
				echo traite_rqt($rqt,"insert demandes_init_workflow=0 into parametres") ;
			}

			// MHo - Paramètre pour automatiser ou non la création de notice lors de l'enregistrement d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='notice_auto' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES (0, 'demandes', 'notice_auto', '0', 'Création automatique de la notice de demande :\n0 : Non\n1 : Oui','',0)";
				echo traite_rqt($rqt,"insert demandes_notice_auto='0' into parametres");
			}

			// MHo - Paramètre pour la création par défaut d'une action lors de la validation d'une demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='default_action' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES (0, 'demandes', 'default_action', '1', 'Création par défaut d\'une action lors de la validation de la demande :\n0 : Non\n1 : Oui','',0)";
				echo traite_rqt($rqt,"insert demandes_default_action='1' into parametres");
			}

			// MHo - Ajout d'une colonne "origine" de l'utilisateur dans la table audit : 0 = gestion, 1 = opac
			$rqt = "ALTER TABLE audit ADD type_user INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter audit add type_user");

			// AR - Ajout d'une colonne pour stocker les actions autorisées par type de demande
			$rqt = "alter table demandes_type add allowed_actions text not null";
			echo traite_rqt($rqt,"alter table demandes_type add allowed_actions");

			//DG - Optimisation
			$rqt = "show fields from notices_fields_global_index";
			$res = pmb_mysql_query($rqt);
			$exists = false;
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){
					if($row->Field == "authority_num"){
						$exists = true;
						break;
					}
				}
			}
			if(!$exists){
				if (pmb_mysql_result(pmb_mysql_query("select count(*) from notices"),0,0) > 15000){
					$rqt = "truncate table notices_fields_global_index";
					echo traite_rqt($rqt,"truncate table notices_fields_global_index");

					// Info de réindexation
					$rqt = " select 1 " ;
					echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
				}

				// JP - Synchronisation RDF
				$rqt = "ALTER TABLE notices_fields_global_index ADD authority_num VARCHAR(50) NOT NULL DEFAULT '0'";
				echo traite_rqt($rqt,"alter table notices_fields_global_index add authority_num");
			}

			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='synchro_rdf' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'synchro_rdf', '0', 'Activer la synchronisation rdf\n 0 : non \n 1 : oui (l\'activation de ce paramètre nécessite une ré-indexation)','',0) " ;
				echo traite_rqt($rqt,"insert pmb_synchro_rdf = 0 into parametres");
			}

			// AB Modification de la valeur par défaut du parametre init_workflow
			$rqt="UPDATE parametres SET valeur_param='1' WHERE type_param='demandes' AND sstype_param='init_workflow'";
			echo traite_rqt($rqt,"update parametres set demandes_init_workflow=1");
			// AB Changement du type de champ pour date_note
			$rqt = "ALTER TABLE demandes_notes CHANGE date_note date_note DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"alter demandes_notes CHANGE date_note");
			// MHo - Ajout d'une colonne "notes_read_gestion" pour indiquer si une note a été lue en gestion ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_notes ADD notes_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_notes add note_read_gestion");
			// MHo - Ajout d'une colonne "actions_read_gestion" pour indiquer si une action a été lue en gestion ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_actions ADD actions_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_actions add actions_read_gestion");
			// MHo - Ajout d'une colonne "dmde_read_gestion" pour indiquer si une demande contient des éléments nouveaux (actions, notes) ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes ADD dmde_read_gestion INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes add dmde_read_gestion");

			// MHo - Ajout d'une colonne "reponse_finale" contenant la réponse finale qui sera intégrée à la faq
			$rqt = "ALTER TABLE demandes ADD reponse_finale TEXT NULL";
			echo traite_rqt($rqt,"alter demandes add reponse_finale");

			// DG - Le super user doit avoir accès à tous les établissements
			$rqt = "UPDATE entites SET autorisations=CONCAT(' 1', autorisations) WHERE type_entite='1' AND autorisations NOT LIKE '% 1 %'";
			echo traite_rqt($rqt, 'UPDATE entites SET autorisations=CONCAT(" 1",autorisations) for super user');

			// AR - Module FAQ - Paramètre d'activation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'faq' and sstype_param='active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'faq', 'active', '0', 'Module \'FAQ\' activé.\n 0 : Non.\n 1 : Oui.', '',0) ";
				echo traite_rqt($rqt, "insert faq_active=0 into parameters");
			}

			// AR - Création de la table des types pour la FAQ
			$rqt = " CREATE TABLE faq_types(
				id_type int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				libelle_type varchar(255) NOT NULL default '',
	        	PRIMARY KEY  (id_type) )";
			echo traite_rqt($rqt,"CREATE TABLE faq_types") ;

			// AR - Création de la table des thèmes pour la FAQ
			$rqt = " CREATE TABLE faq_themes(
				id_theme int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				libelle_theme varchar(255) NOT NULL default '',
	    	    PRIMARY KEY  (id_theme))";
			echo traite_rqt($rqt,"CREATE TABLE faq_themes") ;

			// AR - Création de la table pour la FAQ
			$rqt = "create table faq_questions(
				id_faq_question int(10) unsigned not null auto_increment primary key,
				faq_question_num_type int(10) unsigned not null default 0,
				faq_question_num_theme int(10) unsigned not null default 0,
				faq_question_num_demande int(10) unsigned not null default 0,
				faq_question_question text not null,
				faq_question_question_userdate varchar(255) not null default '',
				faq_question_question_date datetime not null default '0000-00-00 00:00:00',
				faq_question_answer text not null,
				faq_question_answer_userdate varchar(255) not null default '',
				faq_question_answer_date datetime not null default '0000-00-00 00:00:00')";
			echo traite_rqt($rqt,"create table faq_questions");

			// AR - Création de la table de descripteurs pour la FAQ
			$rqt = "create table faq_questions_categories(
				num_faq_question int(10) unsigned not null default 0,
				num_categ int(10) unsigned not null default 0,
				index i_faq_categ(num_faq_question,num_categ))";
			echo traite_rqt($rqt,"create table faq_categories");

			// AR - Ajout de l'ordre dans la table de descripteurs pour la FAQ
			$rqt = "alter table faq_questions_categories add categ_order int(10) unsigned not null default 0";
			echo traite_rqt($rqt,"alter table faq_questions_categories add categ_order");

			// AR - Ajout d'un statut pour les questions de la FAQ (statut de publication 0/1)
			$rqt = "alter table faq_questions add faq_question_statut int(10) unsigned not null default 0";
			echo traite_rqt($rqt,"alter table faq_questions add faq_question_statut");

			// AR indexons correctement la FAQ - Table de mots
			$rqt = "create table if not exists faq_questions_words_global_index(
				id_faq_question int unsigned not null default 0,
				code_champ int unsigned not null default 0,
				code_ss_champ int unsigned not null default 0,
				num_word int unsigned not null default 0,
				pond int unsigned not null default 100,
				position int unsigned not null default 1,
				field_position int unsigned not null default 1,
				primary key (id_faq_question,code_champ,num_word,position,code_ss_champ),
				index code_champ(code_champ),
				index i_id_mot(num_word,id_faq_question),
				index i_code_champ_code_ss_champ_num_word(code_champ,code_ss_champ,num_word))";
			echo traite_rqt($rqt,"create table faq_questions_words_global_index");

			// AR indexons correctement la FAQ - Table de champs
			$rqt = "create table if not exists faq_questions_fields_global_index(
				id_faq_question int unsigned not null default 0,
				code_champ int(3) unsigned not null default 0,
				code_ss_champ int(3) unsigned not null default 0,
				ordre int(4) unsigned not null default 0,
				value text not null,
				pond int(4) unsigned not null default 100,
				lang varchar(10) not null default '',
				authority_num varchar(50) not null default 0,
				primary key(id_faq_question,code_champ,code_ss_champ,lang,ordre),
				index i_value(value(300)),
				index i_code_champ_code_ss_champ(code_champ,code_ss_champ))";
			echo traite_rqt($rqt,"create table faq_questions_fields_global_index ");

			// MHo - Renommage de la colonne "action_read" en "action_read_opac" : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_actions CHANGE actions_read actions_read_opac INT not null default 0";
			echo traite_rqt($rqt,"alter demandes_actions change actions_read actions_read_opac");

			// MHo - Ajout d'une colonne "dmde_read_opac" pour alerter à l'opac en cas d'éléments nouveaux (actions, notes) ou pas : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes ADD dmde_read_opac INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes add dmde_read_opac");

			// MHo - Ajout d'une colonne "notes_read_opac" pour alerter à l'opac en cas de nouveauté : 0 = lue, 1 = non lue
			$rqt = "ALTER TABLE demandes_notes ADD notes_read_opac INT(1) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter demandes_notes add notes_read_opac");

			// DB -Ajout d'une fonction spécifique pour génération de code-barres lecteurs
			$rqt = "update parametres set comment_param='Numéro de carte de lecteur automatique ?\n 0: Non (si utilisation de cartes pré-imprimées)\n";
			$rqt.= " 1: Oui, entièrement numérique\n 2,a,b,c: Oui, avec préfixe: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)\n";
			$rqt.= " 3,fonction: fonction de génération spécifique dans fichier nommé de la même façon, à placer dans pmb/circ/empr' ";
			$rqt.= " where type_param='pmb' and sstype_param='num_carte_auto'";
			echo traite_rqt($rqt,"update parametre pmb_num_carte_auto ");

			// AB On augmente la taille des champs pour le num demandeur ....
			$rqt = "ALTER TABLE demandes CHANGE num_demandeur num_demandeur INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes change num_demandeur");
			$rqt = "ALTER TABLE demandes_actions CHANGE actions_num_user actions_num_user INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes_actions change actions_num_user");
			$rqt = "ALTER TABLE demandes_notes CHANGE notes_num_user notes_num_user INT( 10 ) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter demandes_notes change notes_num_user");

			//DB - Génération code-barres pour les inscritions Web
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='websubscribe_num_carte_auto' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) ";
				$rqt.= "VALUES (NULL, 'opac', 'websubscribe_num_carte_auto', '', 'Numéro de carte de lecteur automatique ?\n 2,a,b,c: Oui avec préfixe: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)\n 3,fonction: fonction de génération spécifique dans fichier nommé de la même façon, à placer dans pmb/opac_css/circ/empr', 'f_modules', '0')" ;
				echo traite_rqt($rqt,"insert opac_websubscribe_num_carte_auto into parametres") ;
			}

			// AB
			$rqt = "CREATE TABLE IF NOT EXISTS onto_uri (
					uri_id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
					uri VARCHAR(255) NOT NULL UNIQUE DEFAULT '' )";
			echo traite_rqt($rqt,"create table onto_uri") ;

			//DB - Génération de cartes lecteurs sur imprimante ticket
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdfcartelecteur' and sstype_param='printer_card_handler' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pdfcartelecteur', 'printer_card_handler', '', 'Gestionnaire d\'impression :\n\n 1 = script \"print_cb.php\"\n 2 = applet jzebra\n 3 = requête ajax','',0)";
				echo traite_rqt($rqt,"insert pmb_printer_card_handler into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdfcartelecteur' and sstype_param='printer_card_name' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pdfcartelecteur', 'printer_card_name', '', 'Nom de l\'imprimante.','',0)";
				echo traite_rqt($rqt,"insert pmb_printer_card_options into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdfcartelecteur' and sstype_param='printer_card_url' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pdfcartelecteur', 'printer_card_url', '', 'Adresse de l\'imprimante.','',0)";
				echo traite_rqt($rqt,"insert pmb_printer_card_url into parametres");
			}

			// NG - Vignette de la notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_img_folder_id' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) ";
				$rqt.= "VALUES (NULL, 'pmb', 'notice_img_folder_id', '0', 'Identifiant du répertoire d\'upload des vignettes de notices', '', '0')" ;
				echo traite_rqt($rqt,"insert pmb_notice_img_folder_id into parametres") ;
			}

			//AR - On ajoute une colonne pour l'inscription en ligne à l'OPAC (pour conserver ce que l'on faisait)
			$rqt = "alter table empr add empr_subscription_action text";
			echo traite_rqt($rqt,"alter table empr add empr_subscription_action");

			//AR - Modification du paramètre opac_websubscribe_show
			$rqt = "update parametres set comment_param = 'Afficher la possibilité de s\'inscrire en ligne ?\n0: Non\n1: Oui\n2: Oui + proposition s\'incription sur les réservations/abonnements' where type_param='opac' and sstype_param = 'websubscribe_show'";
			echo traite_rqt($rqt,"update parametres opac_websubscribe_show");

			//AB parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_template' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_template',0,'Choix du template d\'affichage des notices en mode comparaison.','',1)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_template into parametres");
			}

			//AB comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_nb' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_nb',5,'Nombre de notices à afficher et à raffraichir en mode comparaison.','',1)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_nb into parametres");
			}

			//AB parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='compare_notice_active' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('opac','compare_notice_active',1,'Activer le comparateur de notices','c_recherche',0)";
				echo traite_rqt($rqt,"insert opac_compare_notice_active into parametres");
			}
			// NG - Transfert: mémorisation de la loc d'origine des exemplaires en transfert
			$rqt = "CREATE TABLE if not exists transferts_source (
				trans_source_numexpl INT UNSIGNED NOT NULL default 0 ,
				trans_source_numloc INT UNSIGNED NOT NULL default 0 ,
				PRIMARY KEY(trans_source_numexpl))";
			echo traite_rqt($rqt,"CREATE TABLE transferts_source ") ;

			// NG - Ajout dans les archives de prêt les localisations du pret et de la loc d'origine de l'exemplaire
			$rqt = "alter table pret_archive add arc_expl_location_retour INT UNSIGNED NOT NULL default 0 AFTER arc_expl_location";
			echo traite_rqt($rqt,"alter table pret_archive add arc_expl_location_retour");
			$rqt = "alter table pret_archive add arc_expl_location_origine INT UNSIGNED NOT NULL default 0 AFTER arc_expl_location";
			echo traite_rqt($rqt,"alter table pret_archive add arc_expl_location_origine");

			//DG - Augmentation de la taille du champ pour les équations
			$rqt = "ALTER TABLE equations MODIFY nom_equation TEXT NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE equations MODIFY nom_equation TEXT");

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
		//	echo form_relance ("v5.16");
		//	break;

//case "v5.16":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			// AR indexons correctement SKOS - Table de mots
			$rqt = "create table if not exists skos_words_global_index(
				id_item int unsigned not null default 0,
				code_champ int unsigned not null default 0,
				code_ss_champ int unsigned not null default 0,
				num_word int unsigned not null default 0,
				pond int unsigned not null default 100,
				position int unsigned not null default 1,
				field_position int unsigned not null default 1,
				primary key (id_item,code_champ,num_word,position,code_ss_champ),
				index code_champ(code_champ),
				index i_id_mot(num_word,id_item),
				index i_code_champ_code_ss_champ_num_word(code_champ,code_ss_champ,num_word))";
			echo traite_rqt($rqt,"create table skos_words_global_index");

			// AR indexons correctement  SKOS - Table de champs
			$rqt = "create table if not exists skos_fields_global_index(
				id_item int unsigned not null default 0,
				code_champ int(3) unsigned not null default 0,
				code_ss_champ int(3) unsigned not null default 0,
				ordre int(4) unsigned not null default 0,
				value text not null,
				pond int(4) unsigned not null default 100,
				lang varchar(10) not null default '',
				authority_num varchar(50) not null default 0,
				primary key(id_item,code_champ,code_ss_champ,lang,ordre),
				index i_value(value(300)),
				index i_code_champ_code_ss_champ(code_champ,code_ss_champ))";
			echo traite_rqt($rqt,"create table skos_fields_global_index ");

			//AB table de construction d'une vedette composée
			$rqt = "CREATE TABLE IF NOT EXISTS vedette_object (
						object_type int(3) unsigned NOT NULL DEFAULT 0,
						object_id int(11) unsigned NOT NULL DEFAULT 0,
						num_vedette int(11) unsigned NOT NULL DEFAULT 0,
						subdivision varchar(50) NOT NULL default '',
						position int(3) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (object_type, object_id, num_vedette, subdivision, position),
						INDEX i_vedette_object_object (object_type,object_id),
						INDEX i_vedette_object_vedette (num_vedette)
					) ";
			echo traite_rqt($rqt,"CREATE TABLE vedette_object") ;

			//AB table des identifiants de vedettes
			$rqt = "CREATE TABLE IF NOT EXISTS vedette (
						id_vedette int(11) unsigned NOT NULL AUTO_INCREMENT,
						label varchar(255) NOT NULL default '',
						PRIMARY KEY (id_vedette)
					) ";
			echo traite_rqt($rqt,"CREATE TABLE vedette") ;

			//AP ajout de la table index_concept
			$rqt = "CREATE TABLE IF NOT EXISTS index_concept (
					num_object INT UNSIGNED NOT NULL ,
					type_object INT UNSIGNED NOT NULL ,
					num_concept INT UNSIGNED NOT NULL ,
					order_concept INT UNSIGNED NOT NULL default 0 ,
					PRIMARY KEY(num_object, type_object, num_concept))";
			echo traite_rqt($rqt,"create table index_concept");

			//AP création de la table de lien entre vedettes et autorités
			$rqt = "CREATE TABLE if not exists vedette_link (
				num_vedette INT UNSIGNED NOT NULL ,
				num_object INT UNSIGNED NOT NULL ,
				type_object INT UNSIGNED NOT NULL ,
				PRIMARY KEY (num_vedette, num_object, type_object))";
			echo traite_rqt($rqt,"create table vedette_link");

			// AP script de vérification de saisie des autorités
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='autorites_verif_js' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
						VALUES ( 'pmb', 'autorites_verif_js', '', 'Script de vérification de saisie des autorités','', 0)";
				echo traite_rqt($rqt,"insert autorites_verif_js into parametres");
			}

			//AB paramètre pour masquer/afficher la reservation par panier
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='resa_cart' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('opac','resa_cart',1,'Paramètre pour masquer/afficher la reservation par panier\n0 : Non \n1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_resa_cart into parametres");
			}

			// AR - Report du paramètre activant le stemming en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_stemming_active' "))==0){
				$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES('pmb','search_stemming_active',0,'Activer le stemming dans la recherche\n0 : Désactiver\n1 : Activer','search',0)" ;
				echo traite_rqt($rqt,"insert pmb_search_stemming_active=0 into parametres") ;
			}

			// AR - Report du paramètre excluant des champ dans la recherche tous les champs en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_exclude_fields' "))==0){
				$rqt = "insert into parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES('pmb','search_exclude_fields','','Identifiants des champs à exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)','search',0)" ;
				echo traite_rqt($rqt,"insert pmb_search_exclude_fields into parametres") ;
			}

			//AR - Report du paramètre d'écretage des résultats de recherche en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_noise_limit_type' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_noise_limit_type', '0', 'Ecrêter les résulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du résultat tout ce qui est en dessous de la moyenne - l\'écart-type\n2,ratio : Retirer du résultat tout ce qui est en dessous de la moyenne - un ratio de l\'écart-type (ex: 2,1.96)\n3,ratio : Retirer du résultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 élimine tout ce qui est inférieur à 25% de la plus forte pertinence)' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_search_noise_limit_type='0' into parametres ");
			}

			//AR - Report de la prise en compte de la fréquence d'apparition d'un mot dans le fonds pour le calcul de pertinence en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_relevant_with_frequency' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_relevant_with_frequency', '0', 'Utiliser la fréquence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_relevant_with_frequency='0' into parametres ");
			}

			//AR - Report du paramètre gérant la troncature À droite automatique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_term_troncat_search' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'allow_term_troncat_search', '0', 'Troncature à droite automatique :\n0 : Non \n1 : Oui' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_allow_term_troncat_search='0' into parametres ");
			}

			//AR - Report du paramètre gérant la durée du cache des recherches
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='search_cache_duration' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'search_cache_duration', '0', 'Durée de validité (en secondes) du cache des recherches' , 'search', '0')";
				echo traite_rqt($rqt,"insert pmb_search_cache_duration='0' into parametres ");
			}

			//DG - En impression de panier, imprimer les exemplaires est coché par défaut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='print_expl_default' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'print_expl_default', '0', 'En impression de panier, imprimer les exemplaires est coché par défaut \n 0 : Non \n 1 : Oui','',0) " ;
				echo traite_rqt($rqt,"insert pmb_print_expl_default = 0 into parametres");
			}


			//AR - Activation des concepts ou non
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_active' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_active', '0', 'Active ou non l\'utilisation des concepts:\n0 : Non\n1 : Oui', 'concepts', '0')";
				echo traite_rqt($rqt,"insert thesaurus_concepts_active='0' into parametres ");
			}

			//AP - Paramétrage de l'ordre d'affichage des concepts d'une notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_affichage_ordre' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_affichage_ordre', '0', 'Paramétrage de l\'ordre d\'affichage des catégories d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1', 'concepts', '0')";
				echo traite_rqt($rqt,"insert concepts_affichage_ordre into parametres ");
			}

			//AP - Paramétrage du mode d'affichage des concepts d'une notice (en ligne ou pas)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_concept_in_line' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'thesaurus', 'concepts_concept_in_line', '0', 'Affichage des catégories en ligne.\n 0 : Non.\n 1 : Oui.', 'concepts', '0')";
				echo traite_rqt($rqt,"insert concepts_concept_in_line into parametres ");
			}

			//AB Checkbox pour réafficher les notices dans chaque groupement ou pas
			$rqt = "ALTER TABLE bannettes ADD display_notice_in_every_group INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER group_pperso";
			echo traite_rqt($rqt,"alter table bannettes add display_notice_in_every_group");

			//NG - Autorités personalisées
			$rqt = "create table if not exists authperso (
				id_authperso int(10) unsigned NOT NULL auto_increment,
				authperso_name varchar(255) NOT NULL default '',
				authperso_notice_onglet_num  int unsigned not null default 0,
				authperso_isbd_script text not null,
				authperso_view_script text not null,
				authperso_opac_search int unsigned not null default 0,
				authperso_opac_multi_search int unsigned not null default 0,
				authperso_gestion_search int unsigned not null default 0,
				authperso_gestion_multi_search int unsigned not null default 0,
				authperso_comment text not null,
				PRIMARY KEY  (id_authperso)) ";
			echo traite_rqt($rqt,"create table authperso ");

			//NG - Champs perso des autorités personalisées
			$rqt = "create table if not exists authperso_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				custom_prefixe varchar(255) NOT NULL default '',
				num_type int unsigned not null default 0,
				name varchar(255) NOT NULL default '',
				titre varchar(255) default NULL,
				type varchar(10) NOT NULL default 'text',
				datatype varchar(10) NOT NULL default '',
				options text,
				multiple int(11) NOT NULL default 0,
				obligatoire int(11) NOT NULL default 0,
				ordre int(11) default NULL,
				search INT(1) unsigned NOT NULL DEFAULT 0,
				export INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
			PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table authperso_custom ");

			$rqt = "create table if not exists authperso_custom_lists (
				authperso_custom_champ int(10) unsigned NOT NULL default 0,
				authperso_custom_list_value varchar(255) default NULL,
				authperso_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY editorial_custom_champ (authperso_custom_champ),
				KEY editorial_champ_list_value (authperso_custom_champ,authperso_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table if not exists authperso_custom_lists ");

			$rqt = "create table if not exists authperso_custom_values (
				authperso_custom_champ int(10) unsigned NOT NULL default 0,
				authperso_custom_origine int(10) unsigned NOT NULL default 0,
				authperso_custom_small_text varchar(255) default NULL,
				authperso_custom_text text,
				authperso_custom_integer int(11) default NULL,
				authperso_custom_date date default NULL,
				authperso_custom_float float default NULL,
				KEY editorial_custom_champ (authperso_custom_champ),
				KEY editorial_custom_origine (authperso_custom_origine)) " ;
			echo traite_rqt($rqt,"create table if not exists authperso_custom_values ");

			$rqt = "create table if not exists authperso_authorities (
				id_authperso_authority int(10) unsigned NOT NULL auto_increment,
				authperso_authority_authperso_num int(10) unsigned NOT NULL default 0 ,
				authperso_infos_global text not null,
				authperso_index_infos_global text not null,
				PRIMARY KEY  (id_authperso_authority))  " ;
			echo traite_rqt($rqt,"create table if not exists authperso_authorities ");

			$rqt = "create table if not exists notices_authperso (
				notice_authperso_notice_num int(10) unsigned NOT NULL default 0,
				notice_authperso_authority_num int(10) unsigned NOT NULL default 0,
				notice_authperso_order int(10) unsigned NOT NULL default 0,
				PRIMARY KEY  (notice_authperso_notice_num,notice_authperso_authority_num))  " ;
			echo traite_rqt($rqt,"create table if not exists notices_authperso ");

			// NG : Onglet personalisé de notice
			$rqt = "create table if not exists notice_onglet (
				id_onglet int(10) unsigned NOT NULL auto_increment,
				onglet_name varchar(255) default NULL,
				PRIMARY KEY  (id_onglet)) ";
			echo traite_rqt($rqt,"create table if not exists notice_onglet ");

			//DG - Personnalisation des colonnes pour l'affichage des états des collections
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='collstate_data' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'collstate_data', '', 'Colonne des états des collections, dans l\'ordre donné, séparé par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note\nLes valeurs possibles sont les propriétés de la classe PHP \"pmb/opac_css/classes/collstate.class.php\".','e_aff_notice',0)";
				echo traite_rqt($rqt,"insert opac_collstate_data = 0 into parametres");
			}

			//AB ajout d'un schema SKOS par défaut
			$rqt = "ALTER TABLE users ADD deflt_concept_scheme INT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER deflt_thesaurus";
			echo traite_rqt($rqt,"alter table users add deflt_concept_scheme");

			//AB paramètre caché pour conservation de la date de dernière modification de l'ontologie
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='ontology_filemtime' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('thesaurus','ontology_filemtime',0,'Paramètre caché pour conservation de la date de dernière modification de l\'ontologie','ontologie',1)";
				echo traite_rqt($rqt,"insert thesaurus_ontology_filemtime into parametres");
			}

			// NG - Ajout du champ resa_arc_trans pour associer un transfert à une archive résa
			$rqt = "ALTER TABLE transferts_demande ADD resa_arc_trans int(8) UNSIGNED NOT NULL DEFAULT 0 ";
			echo traite_rqt($rqt,"alter table transferts_demande add resa_arc_trans ");

			// NG - Ajout champ info dans les audits
			$rqt = "ALTER TABLE audit ADD info text NOT NULL";
			echo traite_rqt($rqt,"alter table audit add info ");

			// AP modification du paramètre de schema SKOS par défaut
			$rqt = "ALTER TABLE users CHANGE deflt_concept_scheme deflt_concept_scheme INT(3) NOT NULL DEFAULT -1";
			echo traite_rqt($rqt,"alter table users change deflt_concept_scheme");

			//DG - Statuts sur les documents numériques
			$rqt = "create table if not exists explnum_statut (
				id_explnum_statut smallint(5) unsigned not null auto_increment,
				gestion_libelle varchar(255) not NULL default '',
				opac_libelle varchar(255)  not NULL default '',
				class_html VARCHAR( 255 )  not NULL default '',
				explnum_visible_opac tinyint(1) NOT NULL default 1,
				explnum_visible_opac_abon tinyint(1) NOT NULL default 0,
				explnum_consult_opac tinyint(1) NOT NULL default 1,
				explnum_consult_opac_abon tinyint(1) NOT NULL default 0,
				explnum_download_opac tinyint(1) NOT NULL default 1,
				explnum_download_opac_abon tinyint(1) NOT NULL default 0,
				primary key(id_explnum_statut))";
			echo traite_rqt($rqt,"create table explnum_statut ");

			//DG - Statut "Sans statut particulier" ajouté par défaut
			$rqt = "insert into explnum_statut SET id_explnum_statut=1, gestion_libelle='Sans statut particulier',opac_libelle='', explnum_visible_opac='1' ";
			echo traite_rqt($rqt,"insert minimum into explnum_statut");

			//DG - Ajout d'un champ statut sur les documents numériques
			$rqt = "ALTER TABLE explnum ADD explnum_docnum_statut smallint(5) UNSIGNED NOT NULL DEFAULT 1 ";
			echo traite_rqt($rqt,"alter table explnum add explnum_docnum_statut ");

			//DG - Statut de document numérique par défaut en création de document numérique
			$rqt = "ALTER TABLE users ADD deflt_explnum_statut INT(6) UNSIGNED DEFAULT 1 NOT NULL " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_explnum_statut ");

			//AR - paramétrages des droits d'accès sur les documents numériques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_docnum' "))==0){
			$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_docnum',0,'Gestion des droits d\'accès des emprunteurs aux documents numériques\n0 : Non.\n1 : Oui.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_docnum into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_docnum_def' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_docnum_def',0,'Valeur par défaut en modification de document numérique pour les droits d\'accès emprunteurs - documents numériques\n0 : Recalculer.\n1 : Choisir.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_docnum_def into parametres");
			}

			// NG - Ajout param transferts_retour_action_resa
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='retour_action_resa' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
				VALUES (0, 'transferts', 'retour_action_resa', '1', '1', 'Génére un transfert pour répondre à une réservation lors du retour de l\'exemplaire\n 0: Non\n 1: Oui') ";
				echo traite_rqt($rqt,"INSERT transferts_retour_action_resa INTO parametres") ;
			}

			//DG - Logs OPAC - Exclusion possible des robots et de certaines adresses IP
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='logs_exclude_robots' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'logs_exclude_robots', '1', 'Exclure les robots dans les logs OPAC ?\n 0: Non\n 1: Oui. \nFaire suivre d\'une virgule pour éventuellement exclure les logs OPAC provenant de certaines adresses IP, elles-mêmes séparées par des virgules (ex : 1,127.0.0.1,192.168.0.1).','',0)";
				echo traite_rqt($rqt,"insert pmb_logs_exclude_robots = 1 into parametres");
			}

			// NG - Auteurs répétables dans les titres uniformes
			$rqt = "CREATE TABLE if not exists responsability_tu (
					responsability_tu_author_num int unsigned NOT NULL default 0,
					responsability_tu_num int unsigned NOT NULL default 0,
					responsability_tu_fonction char(4) NOT NULL default '',
					responsability_tu_type int unsigned NOT NULL default 0,
					responsability_tu_ordre smallint(2) unsigned NOT NULL default 0,
					PRIMARY KEY  (responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction),
					KEY responsability_tu_author (responsability_tu_author_num),
					KEY responsability_tu_num (responsability_tu_num) )";
			echo traite_rqt($rqt,"CREATE TABLE responsability_tu ");
			// NG - migration de l'auteur de titre uniforme dans la table responsability_tu
			if ($res = pmb_mysql_query("select tu_num_author, tu_id from titres_uniformes where tu_num_author>0")){
				while ( $row = pmb_mysql_fetch_object($res)) {
					$rqt = "INSERT INTO responsability_tu set responsability_tu_author_num=".$row->tu_num_author.", responsability_tu_num= ".$row->tu_id."  ";
					pmb_mysql_query($rqt, $dbh);
				}
			}

			//NG - ajout pied de page dans la fiche de circulation
			$rqt = "ALTER TABLE serialcirc ADD serialcirc_piedpage text NOT NULL AFTER serialcirc_tpl";
			echo traite_rqt($rqt,"alter table serialcirc add serialcirc_piedpage");

			//DG - Templates de listes de circulation
			$rqt = "CREATE TABLE serialcirc_tpl (
	 				serialcirctpl_id int(10) unsigned NOT NULL auto_increment,
	  				serialcirctpl_name varchar(255) NOT NULL DEFAULT '',
					serialcirctpl_comment varchar(255) NOT NULL DEFAULT '',
					serialcirctpl_tpl text NOT NULL,
	  				PRIMARY KEY  (serialcirctpl_id))";
			echo traite_rqt($rqt,"CREATE TABLE serialcirc_tpl") ;
			$rqt = "insert into serialcirc_tpl SET serialcirctpl_id=1, serialcirctpl_name='Template PMB', serialcirctpl_comment='', serialcirctpl_tpl='a:3:{i:0;a:3:{s:4:\"type\";s:4:\"name\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:1;a:3:{s:4:\"type\";s:5:\"ville\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:2;a:3:{s:4:\"type\";s:5:\"libre\";s:2:\"id\";s:1:\"0\";s:5:\"label\";s:9:\"SIGNATURE\";}}' ";
			echo traite_rqt($rqt,"insert minimum into serialcirc_tpl");

			//DG - Circulation des périodiques : Tri sur les destinataires
			$rqt = "ALTER TABLE serialcirc ADD serialcirc_sort_diff text NOT NULL";
			echo traite_rqt($rqt,"alter table serialcirc add serialcirc_sort_diff");

			//NG - Templates de bannettes
			$rqt = "CREATE TABLE bannette_tpl (
					bannettetpl_id int(10) unsigned NOT NULL auto_increment,
					bannettetpl_name varchar(255) NOT NULL DEFAULT '',
					bannettetpl_comment varchar(255) NOT NULL DEFAULT '',
					bannettetpl_tpl text NOT NULL,
					PRIMARY KEY  (bannettetpl_id))";
			echo traite_rqt($rqt,"CREATE TABLE bannette_tpl") ;
			$rqt = "insert into bannette_tpl SET bannettetpl_id=1, bannettetpl_name='Template PMB', bannettetpl_comment='', bannettetpl_tpl='a:3:{i:0;a:3:{s:4:\"type\";s:4:\"name\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:1;a:3:{s:4:\"type\";s:5:\"ville\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:2;a:3:{s:4:\"type\";s:5:\"libre\";s:2:\"id\";s:1:\"0\";s:5:\"label\";s:9:\"SIGNATURE\";}}' ";
			echo traite_rqt($rqt,"insert minimum into bannette_tpl");

			//NG - Templates de bannettes
			$rqt = "ALTER TABLE bannettes ADD bannette_tpl_num INT(6) UNSIGNED DEFAULT 0 NOT NULL " ;
			echo traite_rqt($rqt,"ALTER bannettes ADD bannette_tpl_num ");


			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.17");
			//break;

//case "v5.17":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			// NG - Ajout paramètre pour activer la géolocalisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_activate', '0', 'Activation de la géolocalisation:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_map_activate into parametres");
			}

			//MB + CB - Renseigner les champs d'exemplaires transfert_location_origine et transfert_statut_origine pour les statistiques et si ils ne le sont pas déjà (lié aux améliorations pour les transferts)
			$rqt = "UPDATE exemplaires SET transfert_location_origine=expl_location, transfert_statut_origine=expl_statut, update_date=update_date WHERE transfert_location_origine=0 AND transfert_statut_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_location_origine transfert_statut_origine");
			//NG - géolocalisation
			$rqt = "ALTER TABLE notices ADD map_echelle_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_echelle_num ");

			$rqt = "ALTER TABLE notices ADD map_projection_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_projection_num ");

			$rqt = "ALTER TABLE notices ADD map_ref_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_ref_num ");

			$rqt = "ALTER TABLE notices ADD map_equinoxe varchar(255) NOT NULL DEFAULT ''" ;
			echo traite_rqt($rqt,"ALTER notices ADD map_equinoxe ");



			//NG - géolocalisation: Memo des emprises
			$rqt = "CREATE TABLE if not exists map_emprises (
					map_emprise_id int(10) unsigned NOT NULL auto_increment,
					map_emprise_type int(10) unsigned NOT NULL default 0,
					map_emprise_obj_num int(10) unsigned NOT NULL default 0,
					map_emprise_data GEOMETRY NOT NULL,
					map_emprise_order int(10) unsigned NOT NULL default 0,
					PRIMARY KEY  (map_emprise_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_emprises") ;

			//NG - géolocalisation: Echelles
			$rqt = "CREATE TABLE if not exists map_echelles (
					map_echelle_id int(10) unsigned NOT NULL auto_increment,
					map_echelle_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_echelle_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_echelles") ;

			//NG - géolocalisation: Système de projection du document
			$rqt = "CREATE TABLE if not exists map_projections (
					map_projection_id int(10) unsigned NOT NULL auto_increment,
					map_projection_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_projection_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_projections") ;

			//NG - géolocalisation: Systeme de référence de coord de la carte
			$rqt = "CREATE TABLE if not exists map_refs (
					map_ref_id int(10) unsigned NOT NULL auto_increment,
					map_ref_name varchar(255) NOT NULL DEFAULT '',
					PRIMARY KEY  (map_ref_id))";
			echo traite_rqt($rqt,"CREATE TABLE map_refs") ;

			// AR - Ajout paramètre pour limiter le nombre d'emprises sur une carte !
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_max_holds' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_max_holds', '250', 'Nombre d\'emprise maximum souhaité par type d\'emprise','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_max_holds into parametres");
			}

			// AR - Les paramètres de cartes sont rangés ensemble !
			$rqt = "update parametres set section_param= 'map' where type_param like 'pmb' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update pmb_map_max_holds");

			// AR - Définition de la couleur d'une emprise de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_record_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associées à des notices','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_record_color into parametres");
			}

			// AR - Définition de la couleur d'une emprise d'autorité
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_authority_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associées à des autorités','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_authority_color into parametres");
			}


			// AR - Définition du fond de carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_base_layer_type' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_base_layer_type', 'OSM', 'Fonds de carte à utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_base_layer_type into parametres");
			}
			// AR - Définition des paramètres du fond de carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_base_layer_params' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_base_layer_params', '', 'Structure JSON à passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_base_layer_params into parametres");
			}

			// NG - Ajout paramètre de la taille de la carte en saisie de recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_search_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_search_edition into parametres");
			}

			// NG - Ajout paramètre de la taille de la carte en résultat de recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_search_result' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_search_result', '800*480', 'Taille de la carte en résultat de recherche','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_search_result into parametres");
			}
			// NG - Ajout paramètre de la taille de la carte en visualisation de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_notice_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_notice_view into parametres");
			}

			// NG - Ajout paramètre de la taille de la carte en édition de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_notice_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_notice_edition', '800*480', 'Taille de la carte en édition de notice','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_notice_edition into parametres");
			}

			// NG - cms: Ajout des vues opac
			$rqt = "ALTER TABLE cms ADD cms_opac_view_num int(10) unsigned NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER cms ADD cms_opac_view_num ");

			//DG - Modification taille du champ article_resume de la table cms_articles
			$rqt ="alter table cms_articles MODIFY article_resume MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table cms_articles modify article_resume mediumtext");

			//DG - Modification taille du champ article_contenu de la table cms_articles
			$rqt ="alter table cms_articles MODIFY article_contenu MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table cms_articles modify article_contenu mediumtext");

			//DG - Modification taille du champ section_resume de la table cms_sections
			$rqt ="alter table cms_sections MODIFY section_resume MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table cms_sections modify section_resume mediumtext");

			//MB - Définition de la taille maximum des vignettes des notices
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_img_pics_max_size' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'notice_img_pics_max_size', '150', 'Taille maximale des vignettes uploadées dans les notices, en largeur ou en hauteur')";
				echo traite_rqt($rqt,"insert pmb_notice_img_pics_max_size='150' into parametres");
			}

			//DG - Vues OPAC dans les facettes
			$rqt = "show fields from facettes";
			$res = pmb_mysql_query($rqt);
			$exists = false;
			if(pmb_mysql_num_rows($res)){
				while($row = pmb_mysql_fetch_object($res)){
					if($row->Field == "facette_opac_views_num"){
						$exists = true;
						break;
					}
				}
			}
			if(!$exists){
				$rqt = "ALTER TABLE facettes ADD facette_opac_views_num text NOT NULL";
				echo traite_rqt($rqt,"alter table facettes add facette_opac_views_num");

				$req = "select id_facette, facette_opac_views_num from facettes";
				$res = pmb_mysql_query($req,$dbh);
				if ($res) {
					$facettes = array();
					while($row = pmb_mysql_fetch_object($res)) {
						$facettes[] = $row->id_facette;
					}
					if (count($facettes)) {
						$req = "select opac_view_id, opac_view_name from opac_views";
						$myQuery = pmb_mysql_query($req, $dbh);
						if ($myQuery) {
							$views = array();
							while ($row = pmb_mysql_fetch_object($myQuery)) {
								$v = array();
								$v["id"] = $row->opac_view_id;
								$v["name"] = $row->opac_view_name;
								$views[] = $v;
							}
							$param["selected"] = $facettes;
							$param=addslashes(serialize($param));
							foreach ($views as $view) {
								//Dans le cas où une modification a été faite avant le passage de la MAJ..
								$req = "delete from opac_filters where opac_filter_view_num=".$view["id"]." and opac_filter_path='facettes'";
								$res = pmb_mysql_query($req,$dbh);
								//Insertion..
								$rqt="insert into opac_filters set opac_filter_view_num=".$view["id"].",opac_filter_path='facettes', opac_filter_param='$param' ";
								echo traite_rqt($rqt,"insert authorization facettes into opac_filters view ".$view["name"]);
							}
						}
					}
				}
			}

			// NG - Ajout paramètre pour activer la géolocalisation en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_activate', '0', 'Activation de la géolocalisation:\n 0 : non \n 1 : oui','a_general', 0)";
				echo traite_rqt($rqt,"insert opac_map_activate into parametres");
			}

			//DB - commande psexec (planificateur sous windows)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='psexec_cmd' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'psexec_cmd', 'psexec -d', 'Paramètres de lancement de psexec (planificateur sous windows)\r\n\nAjouter l\'option -accepteula sur les versions les plus récentes. ', '',0) ";
				echo traite_rqt($rqt, "insert pmb_psexec_cmd into parameters");
			}

			// AR - Ajout paramètre pour activer l'éditeur Dojo
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='editorial_dojo_editor' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'editorial_dojo_editor', '1', 'Activation de l\'éditeur DoJo dans le contenu éditorial:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_editorial_dojo_editor into parametres");
			}

			// DG - Module "Surcharge de méta-données" : Groupes de méta-données par défaut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from cms_managed_modules where managed_module_name= 'cms_module_metadatas' "))==0){
				$struct = array();
				$struct["metadatas1"] = array(
						'prefix' => "og",
						'name' => "Open Graph Protocol",
						'items' => array(
								'title' => array(
										'label' => "titre",
										'desc' => "Titre",
										'default_template' => "{{title}}"
								),
								'type' => array(
										'label' => "type",
										'desc' => "Type",
										'default_template' => "{{type}}"
								),
								'image' => array(
										'label' => "logo",
										'desc' => "Lien vers le logo",
										'default_template' => "{{logo_url}}"
								),
								'url' => array(
										'label' => "lien",
										'desc' => "Lien",
										'default_template' => "{{link}}"
								),
								'description' => array(
										'label' => "description",
										'desc' => "Résumé",
										'default_template' => "{{resume}}"
								),
								'locale' => array(
										'label' => "locale",
										'desc' => "Langue",
										'default_template' => ""
								),
								'site_name' => array(
										'label' => "site_name",
										'desc' => "Nom du site",
										'default_template' => ""
								),
						),
						'separator' => ":",
						'group_template' => "<meta property='{{key_metadata}}' content='{{value_metadata}}' />"
				);

				$struct["metadatas2"] = array(
						'prefix' => "twitter",
						'name' => "Twitter Cards",
						'items' => array(
								'title' => array(
										'label' => "titre",
										'desc' => "Titre",
										'default_template' => "{{title}}"
								),
								'card' => array(
										'label' => "card",
										'desc' => "Résumé",
										'default_template' => ""
								),
								'description' => array(
										'label' => "description",
										'desc' => "Description",
										'default_template' => "{{resume}}"
								),
								'image' => array(
										'label' => "logo",
										'desc' => "Lien vers le logo",
										'default_template' => "{{logo_url}}"
								),
								'site' => array(
										'label' => "site",
										'desc' => "Site",
										'default_template' => ""
								),
						),
						'separator' => ":",
						'group_template' => "<meta name='{{key_metadata}}' content='{{value_metadata}}' />"
				);
				$managed_datas = array();
				$managed_datas["module"]["metadatas"] = $struct;
				$managed_datas=addslashes(serialize($managed_datas));
				$rqt = "INSERT INTO cms_managed_modules ( managed_module_name, managed_module_box)
				VALUES ('cms_module_metadatas', '$managed_datas')";
				echo traite_rqt($rqt,"insert cms_module_metadatas into cms_managed_modules");
			}

			//DB Ajout vignette etageres (SDN)
			$rqt = "ALTER TABLE etagere ADD thumbnail_url MEDIUMBLOB NOT NULL " ;
			echo traite_rqt($rqt,"ALTER TABLE etagere ADD thumbnail_url ");

			// AR - Ajout paramètre pour limiter le nombre d'emprises sur une carte à l'OPAC!
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_max_holds' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_max_holds', '250', 'Nombre d\'emprise maximum souhaité par type d\'emprise','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_max_holds into parametres");
			}

			// AR - Les paramètres de cartes sont rangés ensemble !
			$rqt = "update parametres set section_param= 'map', comment_param='Activation du géoréférencement' where type_param like 'opac' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update opac_map_activate");

			// AR - Changement de nom !
			$rqt = "update parametres set comment_param='Activation du géoréférencement' where type_param like 'pmb' and sstype_param like 'map_activate'";
			echo traite_rqt($rqt,"update pmb_map_activate");

			// AR - Définition de la couleur d'une emprise de notice à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_record_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associées à des notices','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_record_color into parametres");
			}

			// AR - Définition de la couleur d'une emprise d'autorité à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_authority_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associées à des autorités','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_authority_color into parametres");
			}

			// AR - Ajout paramètre de la taille de la carte en saisie de recherche à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_search_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_search_edition into parametres");
			}

			// AR - Ajout paramètre de la taille de la carte en résultat de recherche à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_search_result' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_search_result', '800*480', 'Taille de la carte en résultat de recherche','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_search_result into parametres");
			}
			// AR - Ajout paramètre de la taille de la carte en visualisation de notice à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_notice_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_notice_view into parametres");
			}

			// AR - Définition du fond de carte à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_base_layer_type' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_base_layer_type', 'OSM', 'Fonds de carte à utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_base_layer_type into parametres");
			}
			// AR - Définition des paramètres du fond de carte à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_base_layer_params' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_base_layer_params', '', 'Structure JSON à passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_base_layer_params into parametres");
			}

			// JP - Suggestions - Utilisateur : pouvoir être alerté en cas de nouvelle suggestion à l'OPAC
			$rqt = "ALTER TABLE users ADD user_alert_suggmail int(1) UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"alter table users add user_alert_suggmail");

			// JP - Acquisitions - Sélection rubrique budgétaire en commande : pouvoir toutes les afficher
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='budget_show_all' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'acquisition', 'budget_show_all', '0', 'Sélection d\'une rubrique budgétaire en commande : toutes les afficher ?\n 0: Non (par pagination)\n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert budget_show_all = 0 into parametres");
			}


			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.18");
			//break;

//case "v5.18":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//MB - Ajout index sur le nom des fichiers numériques pour accélérer la recherche
			$add_index=true;
			$req="SHOW INDEX FROM explnum";
			$res=pmb_mysql_query($req);
			if($res && pmb_mysql_num_rows($res)){
				while ($ligne = pmb_mysql_fetch_object($res)){
					if($ligne->Column_name == "explnum_nomfichier"){
						$add_index=false;
						break;
					}
				}
			}
			if($add_index){
				@set_time_limit(0);
				pmb_mysql_query("set wait_timeout=28800", $dbh);
				$rqt = "alter table explnum add index i_explnum_nomfichier(explnum_nomfichier(30))";
				echo traite_rqt($rqt,"alter table explnum add index i_explnum_nomfichier");
			}

			//JP - Ajout deux index sur les liens entre actes pour accélérer la recherche
			$rqt = "alter table liens_actes drop index i_num_acte";
			echo traite_rqt($rqt,"alter table liens_actes drop index i_num_acte");
			$rqt = "alter table liens_actes add index i_num_acte(num_acte)";
			echo traite_rqt($rqt,"alter table liens_actes add index i_num_acte");

			$rqt = "alter table liens_actes drop index i_num_acte_lie";
			echo traite_rqt($rqt,"alter table liens_actes drop index i_num_acte_lie");
			$rqt = "alter table liens_actes add index i_num_acte_lie(num_acte_lie)";
			echo traite_rqt($rqt,"alter table liens_actes add index i_num_acte_lie");

			//JP - Modification taille du champ mailtpl_tpl de la table mailtpl
			$rqt ="alter table mailtpl MODIFY mailtpl_tpl MEDIUMTEXT NOT NULL";
			echo traite_rqt($rqt,"alter table mailtpl modify mailtpl_tpl mediumtext");

			//JP - Nettoyage des catégories sans libellé
			$rqt ="DELETE FROM categories WHERE libelle_categorie=''";
			echo traite_rqt($rqt,"Delete categories sans libellé");

			// JP - Abonnements - nom du périodique par défaut en création d'abonnement
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='abt_label_perio' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
							VALUES (0, 'pmb', 'abt_label_perio', '0', 'Création d\'un abonnement : reprendre le nom du périodique ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert pmb_abt_label_perio = 0 into parametres");
			}

			// JP - Acquisitions - afficher le nom de l'abonnement dans les lignes de la commande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'acquisition' and sstype_param='show_abt_in_cmde' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'acquisition', 'show_abt_in_cmde', '0', 'Afficher l\'abonnement dans les lignes de la commande ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert acquisition_show_abt_in_cmde = 0 into parametres");
			}

			// NG - Nomenclature: Familles
			$rqt = "CREATE TABLE if not exists nomenclature_families (
					id_family int unsigned NOT NULL auto_increment,
					family_name varchar(255) NOT NULL DEFAULT '',
					family_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_family))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_families");

			// NG - Nomenclature: pupitres
			$rqt = "CREATE TABLE if not exists nomenclature_musicstands (
					id_musicstand int unsigned NOT NULL auto_increment,
					musicstand_name varchar(255) NOT NULL DEFAULT '',
					musicstand_famille_num int unsigned NOT NULL DEFAULT 0,
					musicstand_division int unsigned NOT NULL DEFAULT 0,
					musicstand_order int unsigned NOT NULL DEFAULT 0,
					musicstand_workshop int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_musicstand))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_musicstands");

			// NG - Nomenclature: instruments
			$rqt = "CREATE TABLE if not exists nomenclature_instruments (
					id_instrument int unsigned NOT NULL auto_increment,
					instrument_code varchar(255) NOT NULL DEFAULT '',
					instrument_name varchar(255) NOT NULL DEFAULT '',
					instrument_musicstand_num int unsigned NOT NULL DEFAULT 0,
					instrument_standard int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_instruments");

			//NG - Nomenclature: Formations
			$rqt = "CREATE TABLE if not exists nomenclature_formations (
					id_formation int unsigned NOT NULL auto_increment,
					formation_name varchar(255) NOT NULL DEFAULT '',
					formation_nature int unsigned NOT NULL DEFAULT 0,
					formation_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_formation))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_formations");

			// NG - Nomenclature: Types
			$rqt = "CREATE TABLE if not exists nomenclature_types (
					id_type int unsigned NOT NULL auto_increment,
					type_name varchar(255) NOT NULL DEFAULT '',
					type_formation_num int unsigned NOT NULL DEFAULT 0,
					type_order int unsigned NOT NULL DEFAULT 0,
					PRIMARY KEY (id_type))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_types");

			// NG - Nomenclature: voix
			$rqt = "CREATE TABLE if not exists nomenclature_voices (
				id_voice int unsigned NOT NULL auto_increment,
				voice_code varchar(255) NOT NULL DEFAULT '',
				voice_name varchar(255) NOT NULL DEFAULT '',
				voice_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_voice))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_voices");

			// NG - Nomenclature: Formations dans les notices
			$rqt = "CREATE TABLE if not exists nomenclature_notices_nomenclatures (
				id_notice_nomenclature int unsigned NOT NULL auto_increment,
				notice_nomenclature_num_notice int unsigned NOT NULL DEFAULT 0,
				notice_nomenclature_num_formation int unsigned NOT NULL DEFAULT 0,
				notice_nomenclature_num_type int unsigned NOT NULL DEFAULT 0,
				notice_nomenclature_label varchar(255) NOT NULL DEFAULT '',
				notice_nomenclature_abbreviation TEXT NOT NULL ,
				notice_nomenclature_notes TEXT NOT NULL,
				notice_nomenclature_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_notice_nomenclature))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_notices_nomenclatures");

			// NG - Nomenclature: Ateliers des formations de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_workshops (
				id_workshop int unsigned NOT NULL auto_increment,
				workshop_label varchar(255) NOT NULL DEFAULT '',
				workshop_num_nomenclature int unsigned NOT NULL DEFAULT 0,
				workshop_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_workshop))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_workshops");

			// NG - Nomenclature: Instruments des ateliers de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_workshops_instruments (
				id_workshop_instrument int unsigned NOT NULL auto_increment,
				workshop_instrument_num_workshop int unsigned NOT NULL DEFAULT 0,
				workshop_instrument_num_instrument int unsigned NOT NULL DEFAULT 0,
				workshop_instrument_number int unsigned NOT NULL DEFAULT 0,
				workshop_instrument_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_workshop_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_workshops_instruments");

			// NG - Nomenclature: Instruments non standards de la formation de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_exotic_instruments (
				id_exotic_instrument int unsigned NOT NULL auto_increment,
				exotic_instrument_num_nomenclature int unsigned NOT NULL DEFAULT 0,
				exotic_instrument_num_instrument int unsigned NOT NULL DEFAULT 0,
				exotic_instrument_number int unsigned NOT NULL DEFAULT 0,
				exotic_instrument_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_exotic_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_exotic_instruments");


			// NG - Nomenclature: Instruments non standards autres de la formation de la notice
			$rqt = "CREATE TABLE if not exists nomenclature_exotic_other_instruments (
				id_exotic_other_instrument int unsigned NOT NULL auto_increment,
				exotic_other_instrument_num_exotic_instrument int unsigned NOT NULL DEFAULT 0,
				exotic_other_instrument_num_instrument int unsigned NOT NULL DEFAULT 0,
				exotic_other_instrument_order int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (id_exotic_other_instrument))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_exotic_other_instruments");

			// NG - Nomenclature: notices filles
			$rqt = "CREATE TABLE if not exists nomenclature_children_records (
				child_record_num_record int unsigned NOT NULL DEFAULT 0,
				child_record_num_formation int unsigned NOT NULL DEFAULT 0,
				child_record_num_type int unsigned NOT NULL DEFAULT 0,
				child_record_num_musicstand int unsigned NOT NULL DEFAULT 0,
				child_record_num_instrument int unsigned NOT NULL DEFAULT 0,
				child_record_effective int unsigned NOT NULL DEFAULT 0,
				child_record_order int unsigned NOT NULL DEFAULT 0,
				child_record_other varchar(255) NOT NULL DEFAULT '',
				child_record_num_voice int unsigned NOT NULL DEFAULT 0,
				child_record_num_workshop int unsigned NOT NULL DEFAULT 0,
				PRIMARY KEY (child_record_num_record))";
			echo traite_rqt($rqt,"CREATE TABLE nomenclature_children_records");

			// NG - Ajout paramètre pour identifier le type de relation entre une notice de nomenclature et ses notices filles
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_record_children_link' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'nomenclature_record_children_link', '', 'Type de relation entre une notice de nomenclature et ses notices filles.','', 0)";
				echo traite_rqt($rqt,"insert pmb_nomenclature_record_children_link");
			}

			// NG - Ajout paramètre pour activer les nomenclatures
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'nomenclature_activate', '0', 'Activation des nomenclatures:\n 0 : non \n 1 : oui','', 0)";
				echo traite_rqt($rqt,"insert pmb_nomenclature_activate into parametres");
			}

			//MHo - Augmentation de la taille du champ pour les titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes MODIFY tu_sujet TEXT NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes MODIFY tu_sujet TEXT NOT NULL");

			// MB - Affichage liste des bulletins - Modification explication du paramètre
			$rqt = "UPDATE parametres SET comment_param='Fonction d\'affichage de la liste des bulletins d\'un périodique\nValeurs possibles:\naffichage_liste_bulletins_normale (Si paramètre vide)\naffichage_liste_bulletins_tableau\naffichage_liste_bulletins_depliable' WHERE type_param= 'opac' and sstype_param='fonction_affichage_liste_bull'";
			echo traite_rqt($rqt,"UPDATE parametres opac_fonction_affichage_liste_bull");

			// VT & DG - Création des tables de veilles
			$rqt="create table if not exists docwatch_watches(
				id_watch int unsigned not null auto_increment primary key,
				watch_title varchar(255) not null default '',
				watch_owner int unsigned not null default 0,
				watch_allowed_users varchar(255) not null default '',
				watch_num_category int unsigned not null default 0,
				watch_last_date datetime,
				watch_ttl int unsigned not null default 0,
				index i_docwatch_watch_title(watch_title)
				)";
			echo traite_rqt($rqt, "create table docwatch_watches");

			$rqt="create table if not exists docwatch_datasources(
				id_datasource int unsigned not null auto_increment primary key,
				datasource_type varchar(255) not null default '',
				datasource_title varchar(255) not null default '',
				datasource_ttl int unsigned not null default 0,
				datasource_last_date datetime,
				datasource_parameters mediumtext not null,
				datasource_num_category int unsigned not null default 0,
				datasource_default_interesting int unsigned not null default 0,
				datasource_num_watch int unsigned not null default 0,
				index i_docwatch_datasource_title(datasource_title)
				)";
			echo traite_rqt($rqt, "create table docwatch_datasources");

			$rqt="create table if not exists docwatch_selectors (
				id_selector int unsigned not null auto_increment primary key,
				selector_type varchar(255) not null default '',
				selector_num_datasource int unsigned not null default 0,
				selector_parameters mediumtext not null
				)";
			echo traite_rqt($rqt, "create table docwatch_selectors");

			$rqt="create table if not exists docwatch_items(
				id_item int unsigned not null auto_increment primary key,
				item_type varchar(255) not null default '',
				item_title varchar(255) not null default '',
				item_summary mediumtext not null,
				item_content mediumtext not null,
				item_added_date datetime,
				item_publication_date datetime,
				item_hash varchar(255) not null default '',
				item_url varchar(255) not null default '',
				item_logo_url varchar(255) not null default '',
				item_status int unsigned not null default 0,
				item_interesting int unsigned not null default 0,
				item_num_article int unsigned not null default 0,
				item_num_section int unsigned not null default 0,
				item_num_notice int unsigned not null default 0,
				item_num_datasource int unsigned not null default 0,
				item_num_watch int unsigned not null default 0,
				index i_docwatch_item_type(item_type),
				index i_docwatch_item_title(item_title),
				index i_docwatch_item_num_article(item_num_article),
				index i_docwatch_item_num_section(item_num_section),
				index i_docwatch_item_num_notice(item_num_notice),
				index i_docwatch_item_num_watch(item_num_watch)
				)";
			echo traite_rqt($rqt, "create table docwatch_items");

			$rqt="create table if not exists docwatch_items_descriptors(
				num_item int unsigned not null default 0,
				num_noeud int unsigned not null default 0,
				primary key (num_item, num_noeud)
				)";
			echo traite_rqt($rqt, "create table docwatch_items_descriptors");

			$rqt="create table if not exists docwatch_categories(
				id_category int unsigned not null auto_increment primary key,
				category_title varchar(255) not null default '',
				category_num_parent int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table docwatch_categories");

			$rqt="create table if not exists docwatch_items_tags(
				num_item int unsigned not null default 0,
				num_tag int unsigned not null default 0,
				primary key (num_item, num_tag)
				)";
			echo traite_rqt($rqt, "create table docwatch_items_tags");

			$rqt="create table if not exists docwatch_tags(
				id_tag int unsigned not null auto_increment primary key,
				tag_title varchar(255) not null default ''
				)";
			echo traite_rqt($rqt, "create table docwatch_tags");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_desc text NOT NULL" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_desc ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_logo_url varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_logo_url ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_type char(2) not null default 'a'" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_type ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_record_default_status int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_record_default_status ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_article_default_parent int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_article_default_parent");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_article_default_content_type int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_article_default_content_type ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_article_default_publication_status int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_article_default_content_publication_status ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_section_default_parent int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_section_default_parent");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_section_default_content_type int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_section_default_content_type ");

			$rqt = "ALTER TABLE docwatch_watches ADD watch_section_default_publication_status int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_section_default_content_publication_status ");

			// NG - Demandes: Ajout d'un paramètre permettant de saisir un email générique pour la gestion des demanades
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'demandes' and sstype_param='email_generic' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'demandes', 'email_generic', '',
				'Information par un email générique de l\'évolution des demandes.\n 1,adrmail@mail.fr : Envoi une copie uniquement pour toutes les nouvelles demandes\n 2,adrmail@mail.fr : Envoi une copie uniquement des mails envoyés aux personnes affectées\n 3,adrmail@mail.fr : Envoi une copie dans les 2 cas précédents\n ',
				'',0) ";
				echo traite_rqt($rqt, "insert demandes_email_generic into parameters");
			}

			// NG - Demandes: Ajout d'un paramètre permettant d'afficher le format simplifié en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_affichage_simplifie' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'demandes_affichage_simplifie', '0',
				'Active le format simplifié des demandes en Opac:\n 0 : non \n 1 : oui',
				'a_general',0) ";
				echo traite_rqt($rqt, "insert opac_demandes_affichage_simplifie into parameters");
			}

			// NG - Demandes: Ajout d'un paramètre permettant d'interdire l'ajout d'une action en Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_no_action' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'demandes_no_action', '0',
				'Interdire l\'ajout d\'une action en Opac:\n 0 : non \n 1 : oui',
				'a_general',0) ";
				echo traite_rqt($rqt, "insert opac_demandes_no_action into parameters");
			}

			// NG - Demandes: lien entre la note générant la réponse finale d'une demande
			$rqt = "ALTER TABLE demandes ADD demande_note_num int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes ADD demande_note_num ");

			//JP - Modification de la longueur du champ email de la table coordonnees
			$rqt = "ALTER TABLE coordonnees MODIFY email varchar(255) NOT NULL default '' ";
			echo traite_rqt($rqt,"alter table coordonnees modify email");

			// DG - Veilles : Option pour nettoyer le contenu HTML des nouveaux éléments
			$rqt = "ALTER TABLE docwatch_datasources ADD datasource_clean_html int unsigned not null default 1 after datasource_default_interesting" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_datasources ADD datasource_clean_html ");

			// VT - Ajout paramètre pour definir le ratio minimum d'une emprise pour qu'elle s'affiche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_ratio_min' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_ratio_min into parametres");
			}

			// VT - Ajout paramètre pour definir le ratio maximum d'une emprise pour qu'elle s'affiche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_ratio_max' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_ratio_max into parametres");
			}

			// VT - Ajout paramètre pour definir le rapport de distance entre deux points pour qu'ils soit aggrégés ensembles
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_hold_distance' "))==0){
					$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agréger','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_hold_distance into parametres");
			}

			// VT - Creation table de correspondance contenant les aires des différentes emprises de la base
			$rqt="create table if not exists map_hold_areas as (select map_emprise_id as id_obj, map_emprise_type as type_obj, Area(map_emprise_data) as area, Area(envelope(map_emprise_data)) as bbox_area, AsText(Centroid(envelope(map_emprise_data))) as center from map_emprises)";
			echo traite_rqt($rqt, "create table map_hold_areas");

			//VT - Verification de l'existance de la clé primaire (création si non-existante)
			if (pmb_mysql_num_rows(pmb_mysql_query("show keys from map_hold_areas where column_name = 'id_obj' "))==0){
				$rqt="alter table map_hold_areas add primary key(id_obj)";
				echo traite_rqt($rqt, "alter table map_hold_areas add primary key");
			}

			//NG - ajout pied de page dans template de la fiche de circulation
			$rqt = "ALTER TABLE serialcirc_tpl ADD serialcirctpl_piedpage text NOT NULL ";
			echo traite_rqt($rqt,"alter table serialcirc_tpl add serialcirctpl_piedpage");

			// AP - Ajout de la recherche dans les concepts
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='modules_search_concept' "))==0) {
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'modules_search_concept', '0', 'Recherche dans les concepts : \n 0 : interdite, \n 1 : autorisée, \n 2 : autorisée et validée par défaut', 'c_recherche', 0) ";
				echo traite_rqt($rqt, "insert opac_modules_search_concept into parameters");
			}

			// VT - Ajout paramètre pour definir le ratio minimum d'une emprise pour qu'elle s'affiche (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_ratio_min' "))==0) {
				$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ('opac', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher', 'map', 0) ";
				echo traite_rqt($rqt, "insert opac_map_hold_ratio_min into parametres");
			}

			// VT - Ajout paramètre pour definir le ratio maximum d'une emprise pour qu'elle s'affiche (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_ratio_max' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_hold_ratio_max into parametres");
			}

			// VT - Ajout paramètre pour definir le rapport de distance entre deux points pour qu'ils soit aggrégés ensembles (opac)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_hold_distance' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agréger','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_hold_distance into parametres");
			}

			// VT - Ajout d'un index sur la colonne map_emprise_obj_num de la table map_emprises
			$rqt="alter table map_emprises add index i_map_emprise_obj_num(map_emprise_obj_num)";
			echo traite_rqt($rqt, "alter table map_emprises add index i_map_emprise_obj_num");

			// JP - Ajout champ de classement sur étagères et paniers
			$rqt = "ALTER TABLE caddie ADD caddie_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE caddie ADD caddie_classement ");

			$rqt = "ALTER TABLE empr_caddie ADD empr_caddie_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE empr_caddie ADD empr_caddie_classement ");

			$rqt = "ALTER TABLE etagere ADD etagere_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE etagere ADD etagere_classement ");

			// MB - LDAP gestion de l'encodage lors de l'import
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'ldap' and sstype_param='encoding_utf8' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'ldap', 'encoding_utf8', '0', 'Les informations du LDAP sont en utf-8 ?\n 0: Non \n 1: Oui.','',0)";
				echo traite_rqt($rqt,"insert ldap_encoding_utf8 = 0 into parametres");
			}

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.19");
			//break;

//case "v5.19":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//DG - Code Javascript d'analyse d'audience
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='script_analytics' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'script_analytics', '', 'Code Javascript d\'analyse d\'audience (Par exemple pour Google Analytics, XiTi,..).','a_general',0)";
				echo traite_rqt($rqt,"insert opac_script_analytics into parametres");
			}

			//DG - Accessibilité OPAC : Paramètre d'activation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='accessibility' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'accessibility', '1', 'Accessibilité activée.\n 0 : Non.\n 1 : Oui.','a_general',0)";
				echo traite_rqt($rqt,"insert opac_accessibility = 1 into parametres");
			}

			//JP - Renseigner les champs d'exemplaires transfert_location_origine et transfert_statut_origine pour les statistiques et si ils ne le sont pas déjà (ajout sur la requête en v5.17)
			$rqt = "UPDATE exemplaires SET transfert_location_origine=expl_location, update_date=update_date  WHERE transfert_location_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_location_origine");

			$rqt = "UPDATE exemplaires SET transfert_statut_origine=expl_statut, update_date=update_date  WHERE transfert_statut_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_statut_origine");

			// NG - Ajout paramètre indiquant la durée en jours de conservation des notices en tant que nouveauté
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='newrecord_timeshift' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'newrecord_timeshift', '0', 'Nombre de jours de conservation des notices en tant que nouveauté.','', 0)";
				echo traite_rqt($rqt,"insert pmb_newrecord_timeshift");
			}

			// Création shorturls
			$rqt="create table if not exists shorturls (
				id_shorturl int unsigned not null auto_increment primary key,
				shorturl_hash varchar(255) not null default '',
				shorturl_last_access datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				shorturl_context text not null,
				shorturl_type varchar(255) not null default '',
				shorturl_action varchar(255) not null default ''
			)";
			echo traite_rqt($rqt,"create table shorturls");

			// NG - Nouveautés
			$rqt = "ALTER TABLE notices ADD notice_is_new int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE notices ADD notice_is_new ");

			$rqt = "ALTER TABLE notices ADD notice_date_is_new  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'" ;
			echo traite_rqt($rqt,"ALTER TABLE notices ADD notice_date_is_new ");

			// VT - Modif du paramètre map_max_holds en gestion (ajout d'un parametre en plus, update du commentaire) le tout en gardant la valeur precedente
			if (pmb_mysql_num_rows(pmb_mysql_query("select valeur_param from parametres where type_param= 'pmb' and sstype_param='map_max_holds' and valeur_param not like '%,%'"))!=0){
				$rqt="update parametres set valeur_param=concat(valeur_param,',0'), comment_param='Dans l\'ordre donné séparé par une virgule: Nombre limite d\'emprises affichées, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affichées\n\n1 => Clusterisation de toutes les emprises' where type_param like 'pmb' and sstype_param like 'map_max_holds'";
				echo traite_rqt($rqt, "update parametres map_max_holds gestion");
			}

			// VT - Modif du paramètre map_max_holds en opac (ajout d'un parametre en plus, update du commentaire) le tout en gardant la valeur precedente
			if (pmb_mysql_num_rows(pmb_mysql_query("select valeur_param from parametres where type_param= 'opac' and sstype_param='map_max_holds' and valeur_param not like '%,%'"))!=0){
				$rqt="update parametres set valeur_param=concat(valeur_param,',0'), comment_param='Dans l\'ordre donné séparé par une virgule: Nombre limite d\'emprises affichées, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affichées\n\n1 => Clusterisation de toutes les emprises' where type_param like 'opac' and sstype_param like 'map_max_holds'";
				echo traite_rqt($rqt, "update parametres map_max_holds opac");
			}

			// DB - Modification de la table resa_planning (ajout de previsions sur bulletins)
			$rqt = "alter table resa_planning add resa_idbulletin int(8) unsigned default '0' not null after resa_idnotice";
			echo traite_rqt($rqt,"alter resa_planning add resa_idbulletin ");

			//JP - Section origine pour les transferts
			$rqt = "ALTER TABLE exemplaires ADD transfert_section_origine SMALLINT(5) NOT NULL default '0'" ;
			echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD transfert_section_origine ");

			$rqt = "UPDATE exemplaires SET transfert_section_origine=expl_section, update_date=update_date WHERE transfert_section_origine=0 AND expl_id NOT IN (SELECT num_expl FROM transferts_demande JOIN transferts ON (num_transfert=id_transfert AND etat_transfert=0))";
			echo traite_rqt($rqt,"update exemplaires transfert_section_origine");

			//AP Modification du commentaire d'opac_notices_format : Ajout des templates django
			$rqt = "update parametres set comment_param='Format d\'affichage des notices en résultat de recherche\n 0 : Utiliser le paramètre notices_format_onglets\n 1 : ISBD seul\n 2 : Public seul \n4 : ISBD et Public\n 5 : ISBD et Public avec ISBD en premier \n8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)' where type_param= 'opac' and sstype_param='notices_format' ";
			echo traite_rqt($rqt,"update opac_notices_format into parametres");

			// AP - Ajout paramètre indiquant le nom du répertoire des templates django à utiliser en affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_format_django_directory' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'notices_format_django_directory', '', 'Nom du répertoire de templates django à utiliser en affichage de notice.\nLaisser vide pour utiliser le common.','e_aff_notice', 0)";
				echo traite_rqt($rqt,"insert notices_format_django_directory into parametres");
			}

			//MB: Ajouter une PK aux tables de vue
			$res=pmb_mysql_query("SHOW TABLES LIKE 'opac_view_notices_%'");
			if($res && pmb_mysql_num_rows($res)){
				while ($r=pmb_mysql_fetch_array($res)){
					$rqt = "ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice" ;
					echo traite_rqt($rqt,"ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice ");

					$rqt = "ALTER TABLE ".$r[0]." DROP PRIMARY KEY";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." DROP PRIMARY KEY");

					$rqt = "ALTER TABLE ".$r[0]." ADD PRIMARY KEY (opac_view_num_notice)";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." ADD PRIMARY KEY");
				}
			}

			//DG - Paramètre OPAC : Autoriser le téléchargement des documents numériques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_download_docnums' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'allow_download_docnums', '1', 'Autoriser le téléchargement des documents numériques.\n 0 : Non.\n 1 : Individuellement (un par un).\n 2 : Archive ZIP.','a_general',0)";
				echo traite_rqt($rqt,"insert opac_allow_download_docnums = 1 into parametres");
			}

			//AB - Le nom du fichier de paramétrage du selecteur d'affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='notices_display_modes' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'notices_display_modes', '', 'Nom du fichier xml de paramétrage du choix du mode d\'affichage des notices à l\'OPAC.\nPar défaut : display_modes_exemple.xml dans /opac_css/includes/records/','d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert opac_notices_display_modes='' into parametres");
			}

			//DG - Lien pour en savoir plus sur l'utilisation des cookies et des traceurs
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='url_more_about_cookies' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'url_more_about_cookies', '', 'Lien pour en savoir plus sur l\'utilisation des cookies et des traceurs','a_general',0)";
				echo traite_rqt($rqt,"insert opac_url_more_about_cookies into parametres");
			}

			//DG - MAJ du template de bannettes par défaut (identifiant 1)
			$rqt = "UPDATE bannette_tpl SET bannettetpl_tpl='{{info.header}}\r\n<br /><br />\r\n<div class=\"summary\">\r\n{% for sommaire in sommaires %}\r\n<a href=\"#[{{sommaire.level}}]\">\r\n{{sommaire.level}} - {{sommaire.title}}\r\n</a>\r\n<br />\r\n{% endfor %}\r\n</div>\r\n<hr>\r\n{% for sommaire in sommaires %}\r\n<a name=\"[{{sommaire.level}}]\" />\r\n<h1>{{sommaire.level}} - {{sommaire.title}}</h1>\r\n{% for record in sommaire.records %}\r\n{{record.render}}\r\n<hr>\r\n{% endfor %}\r\n<br />\r\n{% endfor %}\r\n{{info.footer}}'
					WHERE bannettetpl_id=1";
			echo traite_rqt($rqt,"ALTER minimum into bannette_tpl");

			// DB - Modification de la table resa_planning (prévisions localisées)
			$rqt = "alter table resa_planning add resa_loc_retrait int(5) unsigned not null default 0 ";
			echo traite_rqt($rqt,"alter resa_planning add resa_loc_retrait ");

			// JP - Ajout champ demande abonnement sur périodique
			$rqt = "ALTER TABLE notices ADD opac_serialcirc_demande TINYINT UNSIGNED NOT NULL DEFAULT 1";
			echo traite_rqt($rqt,"ALTER TABLE notices ADD opac_serialcirc_demande") ;

			// JP - Ajout champ de classement sur infopages
			$rqt = "ALTER TABLE infopages ADD infopage_classement varchar(255) NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE infopages ADD infopage_classement ");

			// JP - Ajout autorisations sur recherches prédéfinies gestion
			$rqt = "ALTER TABLE search_perso ADD autorisations MEDIUMTEXT NULL DEFAULT NULL ";
			echo traite_rqt($rqt,"ALTER TABLE search_perso ADD autorisations") ;

			$rqt = "UPDATE search_perso SET autorisations=num_user ";
			echo traite_rqt($rqt,"UPDATE autorisations INTO search_perso");

			//VT - Paramètre OPAC : Definition du chemin des templates d'autorités en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='authorities_templates_folder' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'authorities_templates_folder', './includes/templates/authorities/common', 'Repertoire des templates utilisés pour l\'affichage des autorités en OPAC','',1)";
				echo traite_rqt($rqt,"insert opac_authorities_templates_folder = ./includes/templates/authorities/common into parametres");
			}

			// JP - template par défaut pour les bannettes privées
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_notices_template' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			VALUES (0, 'dsi', 'private_bannette_notices_template', '0', 'Id du template de notice utilisé par défaut en diffusion de bannettes privées. Si vide ou à 0, le template classique est utilisé.', '', 0)";
				echo traite_rqt($rqt, "insert private_bannette_notices_template into parameters");
			}

			// JP - ajout index manquants sur tables de champs persos
			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_st");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_st(author_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_st");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_t");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_t(author_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_t");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_i");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_i(author_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_i");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_d");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_d(author_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_d");

			$rqt = "ALTER TABLE author_custom_values DROP INDEX i_acv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_f");
			$rqt = "ALTER TABLE author_custom_values ADD INDEX i_acv_f(author_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD INDEX i_acv_f");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_st");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_st(authperso_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_st");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_t");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_t(authperso_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_t");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_i");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_i(authperso_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_i");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_d");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_d(authperso_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_d");

			$rqt = "ALTER TABLE authperso_custom_values DROP INDEX i_acv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_acv_f");
			$rqt = "ALTER TABLE authperso_custom_values ADD INDEX i_acv_f(authperso_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD INDEX i_acv_f");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_st");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_st(categ_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_st");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_t");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_t(categ_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_t");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_i");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_i(categ_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_i");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_d");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_d(categ_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_d");

			$rqt = "ALTER TABLE categ_custom_values DROP INDEX i_ccv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_f");
			$rqt = "ALTER TABLE categ_custom_values ADD INDEX i_ccv_f(categ_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD INDEX i_ccv_f");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_st");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_st(cms_editorial_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_st");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_t");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_t(cms_editorial_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_t");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_i");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_i(cms_editorial_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_i");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_d");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_d(cms_editorial_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_d");

			$rqt = "ALTER TABLE cms_editorial_custom_values DROP INDEX i_ccv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_f");
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_f(cms_editorial_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD INDEX i_ccv_f");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_st");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_st(collection_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_st");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_t");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_t(collection_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_t");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_i");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_i(collection_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_i");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_d");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_d(collection_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_d");

			$rqt = "ALTER TABLE collection_custom_values DROP INDEX i_ccv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_ccv_f");
			$rqt = "ALTER TABLE collection_custom_values ADD INDEX i_ccv_f(collection_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD INDEX i_ccv_f");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_st");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_st(gestfic0_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_st");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_t");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_t(gestfic0_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_t");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_i");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_i(gestfic0_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_i");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_d");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_d(gestfic0_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_d");

			$rqt = "ALTER TABLE gestfic0_custom_values DROP INDEX i_gcv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_gcv_f");
			$rqt = "ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_f(gestfic0_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD INDEX i_gcv_f");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_st");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_st(indexint_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_st");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_t");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_t(indexint_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_t");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_i");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_i(indexint_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_i");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_d");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_d(indexint_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_d");

			$rqt = "ALTER TABLE indexint_custom_values DROP INDEX i_icv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_icv_f");
			$rqt = "ALTER TABLE indexint_custom_values ADD INDEX i_icv_f(indexint_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD INDEX i_icv_f");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_st");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_st(publisher_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_st");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_t");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_t(publisher_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_t");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_i");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_i(publisher_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_i");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_d");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_d(publisher_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_d");

			$rqt = "ALTER TABLE publisher_custom_values DROP INDEX i_pcv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_pcv_f");
			$rqt = "ALTER TABLE publisher_custom_values ADD INDEX i_pcv_f(publisher_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD INDEX i_pcv_f");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_st");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_st(serie_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_st");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_t");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_t(serie_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_t");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_i");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_i(serie_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_i");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_d");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_d(serie_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_d");

			$rqt = "ALTER TABLE serie_custom_values DROP INDEX i_scv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_f");
			$rqt = "ALTER TABLE serie_custom_values ADD INDEX i_scv_f(serie_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD INDEX i_scv_f");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_st");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_st(subcollection_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_st");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_t");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_t(subcollection_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_t");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_i");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_i(subcollection_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_i");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_d");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_d(subcollection_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_d");

			$rqt = "ALTER TABLE subcollection_custom_values DROP INDEX i_scv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_scv_f");
			$rqt = "ALTER TABLE subcollection_custom_values ADD INDEX i_scv_f(subcollection_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD INDEX i_scv_f");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_st " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_st");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_st(tu_custom_small_text)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_st");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_t " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_t");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_t(tu_custom_text(255))" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_t");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_i " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_i");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_i(tu_custom_integer)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_i");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_d " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_d");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_d(tu_custom_date)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_d");

			$rqt = "ALTER TABLE tu_custom_values DROP INDEX i_tcv_f " ;
			echo traite_rqt($rqt,"DROP INDEX i_tcv_f");
			$rqt = "ALTER TABLE tu_custom_values ADD INDEX i_tcv_f(tu_custom_float)" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD INDEX i_tcv_f");


			//AR - Paramètre Portail : Activer la mise en cache des images
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active_image_cache' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active_image_cache', '0', 'Activer la mise en cache des vignettes du contenu éditorial.\n 0: non \n 1:Oui \nAttention, si l\'OPAC ne se trouve pas sur le même serveur que la gestion, la purge du cache ne peut pas se faire automatiquement','',0)";
				echo traite_rqt($rqt,"insert cms_active_image_cache into parametres");
			}

			// MHo - Correction des messages des parametres sur l'ordre d'affichage et le mode d'affichage des concepts d'une notice (remplacement de "categorie" par "concept")
			$rqt="UPDATE parametres SET comment_param='Paramétrage de l\'ordre d\'affichage des concepts d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1'
				WHERE type_param='thesaurus' AND sstype_param='concepts_affichage_ordre' AND section_param='concepts'";
			echo traite_rqt($rqt,"update comment_param de concepts_affichage_ordre into parametres ");

			$rqt="UPDATE parametres SET comment_param='Affichage des concepts en ligne.\n 0 : Non.\n 1 : Oui.'
				WHERE type_param='thesaurus' AND sstype_param='concepts_concept_in_line' AND section_param='concepts'";
			echo traite_rqt($rqt,"update comment_param de concepts_concept_in_line into parametres ");

			//DG - Flag pour savoir si le mot de passe est déjà encrypté
			$rqt= "alter table empr add empr_password_is_encrypted int(1) not null default 0 after empr_password";
			echo traite_rqt($rqt,"alter table empr add empr_password_is_encrypted");

			//DG - Phrase pour le hashage des mots de passe emprunteurs (paramètre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_password_salt' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'empr_password_salt', '', 'Phrase pour le hashage des mots de passe emprunteurs','a_general',1)";
				echo traite_rqt($rqt,"insert opac_empr_password_salt into parametres");
			}

			//DG - Info d'encodage des mots de passe lecteurs pour la connexion à l'Opac
			$res=pmb_mysql_query("SELECT count(*) FROM empr");
			if($res && pmb_mysql_result($res,0,0)){
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ ENCODER LES MOTS DE PASSE LECTEURS (APRES ETAPES DE MISE A JOUR) / YOU MUST ENCODE PASSWORD READERS (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
			}

			// JP - Parametre affichage des dates de creation et modification notices
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notices_show_dates' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES (0, 'pmb', 'notices_show_dates', '0', 'Afficher les dates des notices ? \n 0 : Aucune date.\n 1 : Date de création et modification.', '',0) ";
				echo traite_rqt($rqt, "insert expl_show_dates=0 into parameters");
			}

			// AR - Paramètre pour activer la compression des CSS
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='compress_css' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'compress_css', '0', 'Activer la compilation et la compression des feuilles de styles.\n0: Non\n1: Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_compress_css into parametres");
			}

			//VT - Ajout d'un champ tonalité marclist dans la table titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_tonalite_marclist VARCHAR(5) NOT NULL DEFAULT '' ";
			echo traite_rqt($rqt,"alter titres_uniformes add tu_tonalite_marclist");

			//VT - Ajout d'un champ forme marclist dans la table titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_forme_marclist VARCHAR(5) NOT NULL DEFAULT '' ";
			echo traite_rqt($rqt,"alter titres_uniformes add tu_forme_marclist");

			// DB - Modification de la table resa_planning (quantité prévisions)
			$rqt = "alter table resa_planning add resa_qty int(5) unsigned not null default 1";
			echo traite_rqt($rqt,"alter resa_planning add resa_qty");
			$rqt = "alter table resa_planning add resa_remaining_qty int(5) unsigned not null default 1";
			echo traite_rqt($rqt,"alter resa_planning add resa_remaining_qty");
			// DB - Modification de la table resa (lien vers prévisions)
			$rqt = "alter table resa add resa_planning_id_resa int(8) unsigned not null default 0";
			echo traite_rqt($rqt,"alter resa add resa_planning_id_resa");

			// DB - Delai d'alerte pour le transfert des previsions en reservations
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_planning_toresa' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'resa_planning_toresa', '10', 'Délai d\'alerte pour le transfert des prévisions en réservations (en jours). ' ,'',0)";
				echo traite_rqt($rqt,"insert resa_planning_toresa into parametres");
			}
			
			//JP - Nettoyage vues en erreur suite ajout index unique
			$res=mysql_query("SHOW TABLES LIKE 'opac_view_notices_%'");
			if($res && mysql_num_rows($res)){
				while ($r=mysql_fetch_array($res)){
					$rqt = "TRUNCATE TABLE ".$r[0] ;
					echo traite_rqt($rqt,"TRUNCATE TABLE ".$r[0]);
			
					$rqt = "ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice" ;
					echo traite_rqt($rqt,"ALTER TABLE ".$r[0]." DROP INDEX opac_view_num_notice ");
			
					$rqt = "ALTER TABLE ".$r[0]." DROP PRIMARY KEY";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." DROP PRIMARY KEY");
			
					$rqt = "ALTER TABLE ".$r[0]." ADD PRIMARY KEY (opac_view_num_notice)";
					echo traite_rqt($rqt, "ALTER TABLE ".$r[0]." ADD PRIMARY KEY");
				}
			
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=opac&sub=opac_view&section=list' target=_blank>VOUS DEVEZ RECALCULER LES VUES OPAC (APRES ETAPES DE MISE A JOUR) / YOU MUST RECALCULATE OPAC VIEWS (STEPS AFTER UPDATE) : Admin > Vues Opac > Générer les recherches</a></b> ") ;
			}
			
			//JP - nettoyage table authorities_sources
			$rqt = "DELETE FROM authorities_sources WHERE num_authority=0";
			echo traite_rqt($rqt,"DELETE FROM authorities_sources num_authority vide");
			
			//JP - accès rapide pour les paniers de notices
			$rqt = "ALTER TABLE caddie ADD acces_rapide INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE caddie ADD acces_rapide");
			
			//JP - modification index notices_mots_global_index
			$rqt = "truncate table notices_mots_global_index";
			echo traite_rqt($rqt,"truncate table notices_mots_global_index");
				
			$rqt ="alter table notices_mots_global_index drop primary key";
			echo traite_rqt($rqt,"alter table notices_mots_global_index drop primary key");
			$rqt ="alter table notices_mots_global_index add primary key (id_notice,code_champ,code_ss_champ,num_word,position,field_position)";
			echo traite_rqt($rqt,"alter table notices_mots_global_index add primary key");
			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
				
			//DG - Proposer la conservation de catégories en remplacement de notice
			$rqt= "alter table users add deflt_notice_replace_keep_categories int(1) not null default 0";
			echo traite_rqt($rqt,"alter table users add deflt_notice_replace_keep_categories");
			
			//DG - Champs perso pret
			$rqt = "create table if not exists pret_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				name varchar(255) NOT NULL default '',
				titre varchar(255) default NULL,
				type varchar(10) NOT NULL default 'text',
				datatype varchar(10) NOT NULL default '',
				options text,
				multiple int(11) NOT NULL default 0,
				obligatoire int(11) NOT NULL default 0,
				ordre int(11) default NULL,
				search INT(1) unsigned NOT NULL DEFAULT 0,
				export INT(1) unsigned NOT NULL DEFAULT 0,
				filters INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
				PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table pret_custom ");
			
			$rqt = "create table if not exists pret_custom_lists (
				pret_custom_champ int(10) unsigned NOT NULL default 0,
				pret_custom_list_value varchar(255) default NULL,
				pret_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY i_pret_custom_champ (pret_custom_champ),
				KEY i_pret_champ_list_value (pret_custom_champ,pret_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table if not exists pret_custom_lists ");
			
			$rqt = "create table if not exists pret_custom_values (
				pret_custom_champ int(10) unsigned NOT NULL default 0,
				pret_custom_origine int(10) unsigned NOT NULL default 0,
				pret_custom_small_text varchar(255) default NULL,
				pret_custom_text text,
				pret_custom_integer int(11) default NULL,
				pret_custom_date date default NULL,
				pret_custom_float float default NULL,
				KEY i_pret_custom_champ (pret_custom_champ),
				KEY i_pret_custom_origine (pret_custom_origine)) " ;
			echo traite_rqt($rqt,"create table if not exists pret_custom_values ");
				
			//DG - maj valeurs possibles pour empr_sort_rows
			if (mysql_num_rows(mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='sort_rows'  and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+prénom \n b: code-barres \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n #p[n] : [n] = id des champs personnalisés prêts' where type_param= 'empr' and sstype_param='sort_rows' ";
				echo traite_rqt($rqt,"update empr_sort_rows into parametres");
			}
				
			//DG - maj valeurs possibles pour empr_filter_rows
			if (mysql_num_rows(mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='filter_rows' and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: catégorie\n s: statut\n g: groupe\n y: année de naissance\n cp: code postal\n cs : code statistique\n ab : type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n #p[n] : [n] = id des champs personnalisés prêts' where type_param= 'empr' and sstype_param='filter_rows' ";
				echo traite_rqt($rqt,"update empr_filter_rows into parametres");
			}
				
			//DG - maj valeurs possibles pour empr_show_rows
			if (mysql_num_rows(mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='show_rows'  and (valeur_param like '%#e%' or valeur_param like '%#p%') "))==0){
				$rqt = "update parametres set valeur_param=replace(valeur_param,'#','#e'), comment_param='Colonnes affichées en liste de lecteurs, saisir les colonnes séparées par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+prénom \n a: adresse \n b: code-barre \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n 1: icône panier' where type_param= 'empr' and sstype_param='show_rows' ";
				echo traite_rqt($rqt,"update empr_show_rows into parametres");
			}
				
			// AP - Création d'une table pour la gestion de la suppression des enregistrements OAI
			$rqt = "CREATE TABLE if not exists connectors_out_oai_deleted_records (
					num_set int(11) unsigned NOT NULL DEFAULT 0,
					num_notice int(11) unsigned NOT NULL DEFAULT 0,
					deletion_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					PRIMARY KEY (num_set, num_notice))";
			echo traite_rqt($rqt,"CREATE TABLE connectors_out_oai_deleted_records") ;
			
			// AP - Ajout du stockage de la grammaire d'une vedette
			$rqt = "ALTER TABLE vedette ADD grammar varchar(255) NOT NULL default 'rameau'" ;
			echo traite_rqt($rqt,"ALTER TABLE vedette ADD grammar");
			
			//JP - recalcul des isbn à cause du nouveau fomatage
			require_once($include_path."/isbn.inc.php");
			$res=pmb_mysql_query("SELECT notice_id, code FROM notices WHERE code<>'' AND niveau_biblio='m' AND code LIKE '97%'");
			if($res && pmb_mysql_num_rows($res)){
				while ($row=pmb_mysql_fetch_object($res)) {
					$code = $row->code;
					$new_code = formatISBN($code);
					if ($code!= $new_code){
						pmb_mysql_query("UPDATE notices SET code='".addslashes($new_code)."', update_date=update_date WHERE notice_id=".$row->notice_id);
					}
				}
			}
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"update notices code / ISBN check and clean") ;
				
			
			//JP - mise à jour des dates de validation des commandes
			$rqt="UPDATE actes SET date_valid=date_acte WHERE statut>1 AND date_valid='0000-00-00'";
			echo traite_rqt($rqt,"update actes date_validation ");

		// +-------------------------------------------------+
	//-----------------LLIUREX 06/03/2018---------------	
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.20");

	//case "v5.20":	

			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//DG - maj Colonnes exemplaires affichées en gestion - ajout en commentaire du groupe d'exemplaires
			$rqt = "update parametres set comment_param='Colonne des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle,groupexpl_name #n : id des champs personnalisés \r\n expl_cb est obligatoire et sera ajouté si absent' where type_param= 'pmb' and sstype_param='expl_data' ";
			echo traite_rqt($rqt,"update pmb_expl_data into parametres");

			// AP - Ajout d'une colonne pour lier une notice à une demande
			$rqt = "ALTER TABLE demandes ADD num_linked_notice mediumint(8) UNSIGNED NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes ADD num_linked_notice");

			// AP - Ajout d'un parametre permettant d'autoriser le lecteur à faire une demande à partir d'une notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='demandes_allow_from_record' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'demandes_allow_from_record', '0',	'Autoriser les lecteurs à créer une demande à partir d\'une notice.\n 0 : Non\n 1 : Oui', 'a_general', 0) ";
				echo traite_rqt($rqt, "insert opac_demandes_allow_from_record into parameters");
			}


			//VT - Parametre d'activation de la génération des exemplaires fantomes dans la popup de transfert en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='ghost_expl_enable' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'ghost_expl_enable', '0', '1', 'Script de generation utilise pour les codes barres d\'exemplaires fantomes') ";
				echo traite_rqt($rqt,"INSERT transferts_ghost_expl_enable INTO parametres") ;
			}

			//VT - Parametre de statut par défaut pour les exemplaires fantomes en transfert
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='ghost_statut_expl_transferts' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'ghost_statut_expl_transferts', '0', '1', 'id du statut dans lequel seront placés les exemplaires fantomes en cours de transit') ";
				echo traite_rqt($rqt,"INSERT transferts_ghost_statut_expl_transferts INTO parametres") ;
			}

			//VT - Parametre de choix du script par défaut pour la génération des codes barres des exemplaires fantomes
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='ghost_expl_gen_script' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'ghost_expl_gen_script', 'gen_code\/gen_code_exemplaire.php', '1', 'Script de generation utilise pour les codes barres d\'exemplaires fantomes') ";
				echo traite_rqt($rqt,"INSERT transferts_ghost_expl_gen_script INTO parametres") ;
			}

			//VT - Ajout d'un champs expl_ref_num correspondant à l'id d'exemplaire dont le fantome est issu
			$rqt = "ALTER TABLE exemplaires ADD expl_ref_num INT(10) NOT NULL default '0'" ;
			echo traite_rqt($rqt,"ALTER TABLE exemplaires ADD expl_ref_num ");

			//DG - Champs perso demandes
			$rqt = "create table if not exists demandes_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				name varchar(255) NOT NULL default '',
				titre varchar(255) default NULL,
				type varchar(10) NOT NULL default 'text',
				datatype varchar(10) NOT NULL default '',
				options text,
				multiple int(11) NOT NULL default 0,
				obligatoire int(11) NOT NULL default 0,
				ordre int(11) default NULL,
				search INT(1) unsigned NOT NULL DEFAULT 0,
				export INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
				PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table if not exists demandes_custom ");

			$rqt = "create table if not exists demandes_custom_lists (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_list_value varchar(255) default NULL,
				demandes_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY i_demandes_custom_champ (demandes_custom_champ),
				KEY i_demandes_champ_list_value (demandes_custom_champ,demandes_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table if not exists demandes_custom_lists ");

			$rqt = "create table if not exists demandes_custom_values (
				demandes_custom_champ int(10) unsigned NOT NULL default 0,
				demandes_custom_origine int(10) unsigned NOT NULL default 0,
				demandes_custom_small_text varchar(255) default NULL,
				demandes_custom_text text,
				demandes_custom_integer int(11) default NULL,
				demandes_custom_date date default NULL,
				demandes_custom_float float default NULL,
				KEY i_demandes_custom_champ (demandes_custom_champ),
				KEY i_demandes_custom_origine (demandes_custom_origine)) " ;
			echo traite_rqt($rqt,"create table if not exists demandes_custom_values ");

			//AR - Gestion d'ontologies...
			$rqt = "create table if not exists ontologies (
				id_ontology int unsigned not null auto_increment,
				ontology_name varchar(255) not null default '',
				ontology_description text not null,
				ontology_creation_date datetime not null default '0000-00-00 00:00:00',
				primary key(id_ontology)
			)";
			echo traite_rqt($rqt,"create table if not exists ontologies");

			// NG - Circulation simplifiée de périodique
			$rqt = "ALTER TABLE serialcirc ADD serialcirc_simple int unsigned not null default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE serialcirc ADD serialcirc_simple ");

			// NG - Script de construction d'étiquette de circulation simplifiée de périodique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='serialcirc_simple_print_script' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'serialcirc_simple_print_script', '', 'Script de construction d\'étiquette de circulation simplifiée de périodique' ,'',0)";
				echo traite_rqt($rqt,"insert pmb_serialcirc_simple_print_script into parametres");
			}

			// DB - Modification de la table resarc (id resa_planning pour resa issue d'une prévision)
			$rqt = "alter table resa_archive add resarc_resa_planning_id_resa int(8) unsigned not null default 0";
			echo traite_rqt($rqt,"alter resa_archive add resarc_resa_planning_id_resa");

			// NG - Ajout de tu_oeuvre_nature et tu_oeuvre_type
			$rqt = "ALTER TABLE titres_uniformes ADD tu_oeuvre_nature VARCHAR(3) NOT NULL default 'a'" ;
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes ADD tu_oeuvre_nature ");
			$rqt = "ALTER TABLE titres_uniformes ADD tu_oeuvre_type VARCHAR(3) NOT NULL default 'a'" ;
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes ADD tu_oeuvre_type ");

			// NG - Ajout de la table tu_oeuvres_links
			$rqt = "create table if not exists tu_oeuvres_links (
				oeuvre_link_from int not null default 0,
				oeuvre_link_to int not null default 0,
				oeuvre_link_type VARCHAR(3) not null default '',
				oeuvre_link_expression int not null default 0,
				oeuvre_link_other_link int not null default 1,
				oeuvre_link_order int not null default 0
			)";
			echo traite_rqt($rqt,"create table if not exists tu_oeuvres_links");

			// AP - Nombre maximum de notices à afficher dans une liste sans pagination
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='max_results_on_a_page' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'max_results_on_a_page', '500', 'Nombre maximum de notices à afficher sur une page, utile notamment quand la navigation est désactivée' ,'d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert max_results_on_a_page into parametres");
			}

			//JP - taille de certains champs blob trop juste
			$rqt = "ALTER TABLE opac_sessions CHANGE session session MEDIUMBLOB NULL DEFAULT NULL";
			echo traite_rqt($rqt,"ALTER TABLE opac_sessions CHANGE session MEDIUMBLOB");
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ FAIRE UN NETTOYAGE DE BASE (APRES ETAPES DE MISE A JOUR) / YOU MUST DO A DATABASE CLEANUP (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;

			// NG - Ajout tu_oeuvre_nature_nature dans titres_uniformes
			$rqt = "ALTER TABLE titres_uniformes ADD tu_oeuvre_nature_nature VARCHAR(255) NOT NULL default 'original'" ;
			echo traite_rqt($rqt,"ALTER TABLE titres_uniformes ADD tu_oeuvre_nature_nature ");

			// NG - Ajout authperso_oeuvre_event dans authperso
			$rqt = "ALTER TABLE authperso ADD authperso_oeuvre_event int unsigned NOT NULL default '0'" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso ADD authperso_oeuvre_event ");

			// NG - Ajout de la table tu_oeuvres_events
			$rqt = "create table if not exists tu_oeuvres_events (
				oeuvre_event_tu_num int not null default 0,
				oeuvre_event_authperso_authority_num int not null default 0,
				oeuvre_event_order int not null default 0
			)";
			echo traite_rqt($rqt,"create table if not exists tu_oeuvres_events");

			// NG - Ajout comment dans les champs personalisés
			$rqt = "ALTER TABLE notices_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE notices_custom ADD comment ");

			$rqt = "ALTER TABLE author_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE author_custom ADD comment ");

			$rqt = "ALTER TABLE authperso_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom ADD comment ");

			$rqt = "ALTER TABLE categ_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE categ_custom ADD comment ");

			$rqt = "ALTER TABLE cms_editorial_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom ADD comment ");

			$rqt = "ALTER TABLE collection_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collection_custom ADD comment ");

			$rqt = "ALTER TABLE collstate_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE collstate_custom ADD comment ");

			$rqt = "ALTER TABLE demandes_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE demandes_custom ADD comment ");

			$rqt = "ALTER TABLE empr_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE empr_custom ADD comment ");

			$rqt = "ALTER TABLE expl_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE expl_custom ADD comment ");

			$rqt = "ALTER TABLE gestfic0_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom ADD comment ");

			$rqt = "ALTER TABLE indexint_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom ADD comment ");

			$rqt = "ALTER TABLE pret_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE pret_custom ADD comment ");

			$rqt = "ALTER TABLE publisher_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom ADD comment ");

			$rqt = "ALTER TABLE serie_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE serie_custom ADD comment ");

			$rqt = "ALTER TABLE subcollection_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom ADD comment ");

			$rqt = "ALTER TABLE tu_custom ADD comment BLOB NOT NULL default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE tu_custom ADD comment ");

			// AP - Activation de l'interface DOJO pour la multicritère en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='extended_search_dnd_interface' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'extended_search_dnd_interface', '1', 'Activer l\'interface drag\'n\'drop pour la recherche multicritère.\n0 : Non\n1 : Oui' ,'', 0)";
				echo traite_rqt($rqt,"insert extended_search_dnd_interface into parametres");
			}

			// AP - Activation de l'interface DOJO pour la multicritère en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='extended_search_dnd_interface' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'extended_search_dnd_interface', '0', 'Activer l\'interface drag\'n\'drop pour la recherche multicritère.\n0 : Non\n1 : Oui' ,'c_recherche', 0)";
				echo traite_rqt($rqt,"insert extended_search_dnd_interface into parametres");
			}

			//JP - bouton vider le cache portail
			$rqt = "ALTER TABLE cms_articles ADD article_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE cms_articles ADD article_update_timestamp");
			$rqt = "UPDATE cms_articles SET article_update_timestamp=article_creation_date";
			echo traite_rqt($rqt,"UPDATE cms_articles SET article_update_timestamp");
			$rqt = "ALTER TABLE cms_sections ADD section_update_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE cms_sections ADD section_update_timestamp");
			$rqt = "UPDATE cms_sections SET section_update_timestamp=section_creation_date";
			echo traite_rqt($rqt,"UPDATE cms_sections SET section_update_timestamp");

			//JP - choix notice nouveauté oui/non par utilisateur en création de notice
			$rqt = "ALTER TABLE users ADD deflt_notice_is_new INT( 1 ) UNSIGNED NOT NULL DEFAULT '0'";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_is_new");

			// JP - paramètre mail_adresse_from pour l'envoi de mails
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='mail_adresse_from' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services' ,'',0)";
				echo traite_rqt($rqt,"insert pmb_mail_adresse_from into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='mail_adresse_from' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services' ,'a_general',0)";
				echo traite_rqt($rqt,"insert opac_mail_adresse_from into parametres");
			}

			// JP - blocage des prolongations autorisées si relance sur le prêt
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='pret_prolongation_blocage' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'pret_prolongation_blocage', '0', 'Bloquer la prolongation s\'il y a un niveau de relance validé sur le prêt ?\n0 : Non 1 : Oui' ,'a_general',0)";
				echo traite_rqt($rqt,"insert opac_pret_prolongation_blocage into parametres");
			}

			// VT & DG - Ajout de la table memorisant les grilles de saisie de formulaire
			// grid_generic_type : Type d'objet
			// grid_generic_filter : Signature (en cas de grilles multiples)
			// grid_generic_data : Format JSON de la grille
			$rqt = "create table if not exists grids_generic (
				grid_generic_type VARCHAR(32) not null default '',
				grid_generic_filter VARCHAR(255) not null default '',
				grid_generic_data mediumblob NOT NULL,
				PRIMARY KEY (grid_generic_type,grid_generic_filter)
				)";
			echo traite_rqt($rqt,"create table if not exists grids_generic");

			//DG - Grilles d'autorités éditables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='form_authorities_editables' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'form_authorities_editables', '1', 'Grilles d\'autorités éditables \n 0 non \n 1 oui','',0)";
				echo traite_rqt($rqt,"insert pmb_form_authorities_editables into parametres");
			}

			//JP - Export tableur des prêts dans le compte emprunteur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='empr_export_loans' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'empr_export_loans', '0', 'Afficher sur le compte emprunteur un bouton permettant d\'exporter les prêts dans un tableur ?\n0 : Non 1 : Oui' ,'a_general',0)";
				echo traite_rqt($rqt,"insert opac_empr_export_loans into parametres");
			}

			//Alexandre - Ajout des modes d'affichage avec sélection par étoiles
			$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.') WHERE type_param= 'pmb' AND sstype_param='avis_note_display_mode'";
			echo traite_rqt($rqt,"UPDATE pmb_avis_note_display_mode into parametres");
			$rqt = "UPDATE parametres SET comment_param=CONCAT(comment_param,'\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.') WHERE type_param= 'opac' AND sstype_param='avis_note_display_mode'";
			echo traite_rqt($rqt,"UPDATE opac_avis_note_display_mode into parametres");

			//JP - paramètre utilisateur : localisation par défaut en bulletinage
			// deflt_bulletinage_location : Identifiant de la localisation par défaut en bulletinage
			$rqt = "ALTER TABLE users ADD deflt_bulletinage_location INT( 6 ) UNSIGNED NOT NULL DEFAULT 0 AFTER deflt_collstate_location";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_bulletinage_location");
			$rqt = "UPDATE users SET deflt_bulletinage_location=deflt_docs_location";
			echo traite_rqt($rqt,"UPDATE users SET deflt_bulletinage_location=deflt_docs_location");

			//JP - audit sur le contenu éditorial
			$res=pmb_mysql_query("SELECT id_section, section_creation_date, section_update_timestamp FROM cms_sections");
			if($res && pmb_mysql_num_rows($res)){
				while ($r=pmb_mysql_fetch_object($res)){
					$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_SECTION."', object_id='".$r->id_section."', user_id='0', user_name='', type_modif=1, quand='".$r->section_creation_date." 00:00:00', info='' ";
					pmb_mysql_query($rqt);
					if ($r->section_update_timestamp != $r->section_creation_date.' 00:00:00') {
						$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_SECTION."', object_id='".$r->id_section."', user_id='0', user_name='', type_modif=2, quand='".$r->section_update_timestamp."', info='' ";
						pmb_mysql_query($rqt);
					}
				}
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"INSERT editorial_sections INTO audit ");
			}
			$res=pmb_mysql_query("SELECT id_article, article_creation_date, article_update_timestamp FROM cms_articles");
			if($res && pmb_mysql_num_rows($res)){
				while ($r=pmb_mysql_fetch_object($res)){
					$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_ARTICLE."', object_id='".$r->id_article."', user_id='0', user_name='', type_modif=1, quand='".$r->article_creation_date." 00:00:00', info='' ";
					pmb_mysql_query($rqt);
					if ($r->article_update_timestamp != $r->article_creation_date.' 00:00:00') {
						$rqt = "INSERT INTO audit SET type_obj='".AUDIT_EDITORIAL_ARTICLE."', object_id='".$r->id_article."', user_id='0', user_name='', type_modif=2, quand='".$r->article_update_timestamp."', info='' ";
						pmb_mysql_query($rqt);
					}
				}
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"INSERT editorial_articles INTO audit ");
			}

			//MB - last_sync_date : Date de la dernière synchronisation du connecteur
			$rqt = "ALTER TABLE connectors_sources ADD last_sync_date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE connectors_sources ADD last_sync_date");

			// DG & AP - Création d'une table d'autorités pour des identifiants uniques quel que soit le type d'autorité
			// id_authority : Identifiant unique de l'autorité
			// num_object : Identifiant de l'autorité dans sa table
			// type_object : Type de l'autorité
			$rqt="create table if not exists authorities (
				id_authority int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
				num_object mediumint(8) UNSIGNED NOT NULL default 0,
				type_object int unsigned not null default 0,
				index i_object(num_object, type_object)
				)";
			echo traite_rqt($rqt, "create table authorities");

			// DG & AP - Création de la table d'indexation d'autorités - Table des champs
			// id_authority : Identifiant de l'autorité faisant référence à l'identifiant de la table authorities
			// type : Type d'autorité
			// code_champ : Code champ
			// code_ss_champ : Code sous champ
			// ordre : Ordre
			// value : Valeur
			// pond : Pondération
			// lang : Langue
			// authority_num : Identifiant de l'autorité liée
			$rqt="create table if not exists authorities_fields_global_index (
				id_authority int unsigned not null default 0,
				type int(5) unsigned not null default 0,
				code_champ int(10) not null default 0,
				code_ss_champ int(3) not null default 0,
				ordre int(4) not null default 0,
				value text not null,
				pond int(4) not null default 100,
				lang varchar(10) not null default '',
				authority_num varchar(50) not null default '',
				primary key(id_authority,code_champ,code_ss_champ,ordre),
				index i_value(value(300)),
				index i_id_value(id_authority,value(300))
				)";
			echo traite_rqt($rqt, "create table authorities_fields_global_index");

			// DG & AP - Création de la table d'indexation d'autorités - Table de mots
			// id_authority : Identifiant de l'autorité faisant référence à l'identifiant de la table authorities
			// type : Type d'autorité
			// code_champ : Code champ
			// code_ss_champ : Code sous champ
			// num_word : Identifiant du mot dans la table words
			// pond : Pondération
			// position : Position du champ
			// field_position : Position du mot dans le champ
			$rqt = "create table if not exists authorities_words_global_index(
				id_authority int unsigned not null default 0,
				type int(5) unsigned not null default 0,
				code_champ int unsigned not null default 0,
				code_ss_champ int unsigned not null default 0,
				num_word int unsigned not null default 0,
				pond int unsigned not null default 100,
				position int unsigned not null default 1,
				field_position int unsigned not null default 1,
				primary key (id_authority,code_champ,num_word,position,code_ss_champ),
				index code_champ(code_champ),
				index i_id_mot(num_word,id_authority),
				index i_code_champ_code_ss_champ_num_word(code_champ,code_ss_champ,num_word))";
			echo traite_rqt($rqt,"create table authorities_words_global_index");

			// DG & AP - Ajout d'index sur la table aut_link
			$rqt = "ALTER TABLE aut_link drop index i_from";
			echo traite_rqt($rqt,"alter table aut_link drop index i_from");
			$rqt = "ALTER TABLE aut_link add index i_from (aut_link_from,aut_link_from_num) ";
			echo traite_rqt($rqt, "add index i_from to aut_link");

			// DG & AP - Ajout d'index sur la table aut_link
			$rqt = "ALTER TABLE aut_link drop index i_to";
			echo traite_rqt($rqt,"alter table aut_link drop index i_to");
			$rqt = "ALTER TABLE aut_link add index i_to (aut_link_to,aut_link_to_num) ";
			echo traite_rqt($rqt, "add index i_to to aut_link");

			// AR - Création d'un statut pour les autorités
			// id_authorities_statut : Identifiant du statut d'autorités
			// authorities_statut_label : Libellé du statut
			// authorities_statut_class_html : Distinction de couleur pour le statut
			// authorities_statut_available_for : Quelles sont les autorités autorisées à utiliser ce statut ?
			$rqt = "create table if not exists authorities_statuts (
				id_authorities_statut int unsigned not null auto_increment primary key,
				authorities_statut_label varchar(255) not null default '',
				authorities_statut_class_html varchar(25) not null default '',
				authorities_statut_available_for text
				)";
			echo traite_rqt($rqt,"create table authorities_statuts");

			// NG - VT - Statut par défaut pour les autorités
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from authorities_statuts where id_authorities_statut='1' "))==0) {
				$rqt = 'INSERT INTO authorities_statuts (id_authorities_statut,authorities_statut_label,authorities_statut_class_html,authorities_statut_available_for) VALUES (1 ,"Statut par défaut", "statutnot1", "'.addslashes('a:9:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";i:5;s:1:"6";i:6;s:1:"8";i:7;s:1:"7";i:8;s:2:"10";}').'") ';
				echo traite_rqt($rqt,"insert default lignes_actes_statuts");
			}

			// DG - création du champ statut pour les autorités
			$rqt = "alter table authorities add num_statut int(2) unsigned not null default 1";
			echo traite_rqt($rqt,"alter table authorities add num_statut");

			//DG - Paramètre pour afficher ou non le bandeau d'acceptation des cookies
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='cookies_consent' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'cookies_consent', '1', 'Afficher le bandeau d\'acceptation des cookies et des traceurs ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_cookies_consent into parametres");
			}

			//DG - Grille d'auteur pour les personnes physiques
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from grids_generic where grid_generic_type= 'auteurs' and grid_generic_filter='70' "))==0){
				$rqt = "INSERT INTO grids_generic (grid_generic_type, grid_generic_filter, grid_generic_data)
								VALUES ('auteurs', '70', '[{\"nodeId\":\"el0\",\"label\":\"Zone par d\\u00e9faut\",\"isExpandable\":false,\"showLabel\":false,\"visible\":true,\"elements\":[{\"nodeId\":\"el0Child_0\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_1_a\",\"visible\":true,\"className\":\"colonne2\"},{\"nodeId\":\"el0Child_1_b\",\"visible\":true,\"className\":\"colonne_suite\"},{\"nodeId\":\"el0Child_2\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_3\",\"visible\":false,\"className\":\"row\"},{\"nodeId\":\"el0Child_4_a\",\"visible\":false,\"className\":\"colonne2\"},{\"nodeId\":\"el0Child_4_b\",\"visible\":false,\"className\":\"colonne_suite\"},{\"nodeId\":\"el0Child_5_a\",\"visible\":false,\"className\":\"colonne2\"},{\"nodeId\":\"el0Child_5_b\",\"visible\":false,\"className\":\"colonne_suite\"},{\"nodeId\":\"el0Child_6\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_7\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_8\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el6Child_3\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el0Child_9\",\"visible\":true,\"className\":\"row\"},{\"nodeId\":\"el7Child_0\",\"visible\":true,\"className\":\"row\"}]}]')";
				echo traite_rqt($rqt,"insert minimum into grids_generic");
			}

			//DG - Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER LES AUTORITES (APRES ETAPES DE MISE A JOUR) / YOU MUST REINDEX THE AUTHORITIES (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;

			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.21");

	
	//	case "v5.21":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+

			//NG -  DSI: Ajout de bannette_aff_notice_number pour afficher ou pas le nombre de notices envoyées dans le mail
			$rqt = "ALTER TABLE bannettes ADD bannette_aff_notice_number int unsigned NOT NULL default 1 " ;
			echo traite_rqt($rqt,"ALTER TABLE bannettes ADD bannette_aff_notice_number ");

			//JP - Personnalisation des colonnes pour l'affichage des états des collections en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='collstate_data' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'collstate_data', '', 'Colonne des états des collections, dans l\'ordre donné, séparé par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note,#n : id des champs personnalisés\nLes valeurs possibles sont les propriétés de la classe PHP \"pmb/classes/collstate.class.php\".','',0)";
				echo traite_rqt($rqt,"insert pmb_collstate_data = '' into parametres");
			}

			//JP - champ historique de session trop petit
			$rqt = "ALTER TABLE admin_session CHANGE session session MEDIUMBLOB " ;
			echo traite_rqt($rqt,"ALTER TABLE admin_session CHANGE session MEDIUMBLOB ");

			// JP - Alertes localisées pour les réservations depuis l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_alert_localized' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'resa_alert_localized', '0', 'Si les lecteurs sont localisés, restreindre les notifications par email des nouvelles réservations aux utilisateurs selon le site de gestion des lecteurs par défaut ? \n0 : Non 1 : Oui' ,'',0)";
				echo traite_rqt($rqt,"insert pmb_resa_alert_localized into parametres");
			}

			// VT & AP - Modification de la table nomenclature_children_records : on passe à un varchar pour la gestion des effectifs indéfinis
			$rqt = "ALTER TABLE nomenclature_children_records CHANGE child_record_effective child_record_effective varchar(10) not null default '0'";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_children_records CHANGE child_record_effective varchar(10)");

			//AP & VT - Ajout d'un paramètre définissant le nombre d'éléments affichés par onglet
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nb_elems_per_tab' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nb_elems_per_tab', '20', 'Nombre d\'éléments affichés par page dans les onglets', '', '0')";
				echo traite_rqt($rqt,"insert nb_elems_per_tab='20' into parametres ");
			}

			//NG - Ajout identifiant unique à la table responsability_tu
			$query = "SHOW KEYS FROM responsability_tu WHERE Key_name = 'PRIMARY'";
			$result = pmb_mysql_query($query);
			$primary_fields = array('id_responsability_tu','responsability_tu_author_num','responsability_tu_num','responsability_tu_fonction');
			$flag = false;
			while($row = pmb_mysql_fetch_object($result)) {
				if(!in_array($row->Column_name, $primary_fields)) {
					$flag = true;
				}
			}
			if(!$flag && pmb_mysql_num_rows($result) != 4) {
				$flag = true;
			}
			if($flag) {			
				$rqt = "alter table responsability_tu drop primary key";
				echo traite_rqt($rqt,"alter table responsability_tu drop primary key");
				$rqt = "ALTER TABLE responsability_tu ADD id_responsability_tu  int unsigned not null auto_increment primary key FIRST";
				echo traite_rqt($rqt,"alter table responsability_tu add id_responsability_tu");
				$rqt = "alter table responsability_tu drop primary key, add primary key (id_responsability_tu,responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction)";
				echo traite_rqt($rqt,"alter table responsability_tu add primary key (id_responsability_tu,responsability_tu_author_num, responsability_tu_num, responsability_tu_fonction)");
			}
			
			//NG - Ajout identifiant unique à la table responsability
			$query = "SHOW KEYS FROM responsability WHERE Key_name = 'PRIMARY'";
			$result = pmb_mysql_query($query);
			$primary_fields = array('id_responsability','responsability_author','responsability_notice','responsability_fonction');
			$flag = false;
			while($row = pmb_mysql_fetch_object($result)) {
				if(!in_array($row->Column_name, $primary_fields)) {
					$flag = true;
				}
			}
			if(!$flag && pmb_mysql_num_rows($result) != 4) {
				$flag = true;
			}
			if($flag) {
				$rqt = "alter table responsability drop primary key";
				echo traite_rqt($rqt,"alter table responsability drop primary key");
				$rqt = "ALTER TABLE responsability ADD id_responsability  int unsigned not null auto_increment primary key FIRST";
				echo traite_rqt($rqt,"alter table responsability add id_responsability");
				$rqt = "alter table responsability drop primary key, add primary key (id_responsability, responsability_author, responsability_notice, responsability_fonction)";
				echo traite_rqt($rqt,"alter table responsability add primary key (id_responsability, responsability_author, responsability_notice, responsability_fonction)");
			}
			
			//NG - Ajout d'un paramètre pour activer la qualification d'un lien d'auteur dans les notices et les titres uniformes
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authors_qualification' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'authors_qualification', '0', 'Activer qualification d\'un lien d\'auteur dans les notices et les titres uniformes\n 0 : Non\n 1 : Oui', '', '0')";
				echo traite_rqt($rqt,"insert pmb_authors_qualification=0 into parametres ");
			}

			//DG - Entrepôt par défaut en suppression de notices d'un panier
			$rqt = "ALTER TABLE users ADD deflt_agnostic_warehouse INT(6) UNSIGNED DEFAULT 0 NOT NULL " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_agnostic_warehouse");


			// NG : ajout dans les préférences utilisateur du statut de publication d'article par défaut en création d'article
			$rqt = "ALTER TABLE users ADD deflt_cms_article_statut INT(6) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_cms_article_statut ");
			// NG : ajout dans les préférences utilisateur du type de contenu par défaut en création d'article
			$rqt = "ALTER TABLE users ADD deflt_cms_article_type INT(6) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_cms_article_type ");
			// NG : ajout dans les préférences utilisateur du type de contenu par défaut en création de rubrique
			$rqt = "ALTER TABLE users ADD deflt_cms_section_type INT(6) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_cms_section_type ");

			//NG - Ajout d'un paramètre définissant le nombre de bulletins à afficher dans le navigateur de bulletins
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='navigateur_bulletin_number' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'opac', 'navigateur_bulletin_number', '3', 'Nombre de bulletins à afficher dans le navigateur de bulletins', 'e_aff_notice', '0')";
				echo traite_rqt($rqt,"insert opac_navigateur_bulletin_number=3 into parametres ");
			}

			//DG - Upload du logo pour les veilles
			$rqt = "ALTER TABLE docwatch_watches ADD watch_logo mediumblob not null after watch_desc";
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_logo ");

			//DG Ajout couleur sur le statut de publication du contenu éditorial
			$rqt = "ALTER TABLE cms_editorial_publications_states ADD editorial_publication_state_class_html VARCHAR( 255 ) NOT NULL default '' " ;
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_publications_states ADD editorial_publication_state_class_html ");

			//VT - Paramètre permettant de définir le dossier des classes de mappage à utiliser pour le mappage entre autorités
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authority_mapping_folder' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'authority_mapping_folder', '', 'Dossier des classes de mappage à utiliser pour les autorités','',0)";
				echo traite_rqt($rqt,"insert pmb_authority_mapping_folder='' into parametres");
			}

			//DG - Nomenclatures : Atelier défini/non défini
			$rqt = "ALTER TABLE nomenclature_workshops ADD workshop_defined int unsigned not null default 0";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_workshops ADD workshop_defined ");

			//DG - Nomenclatures : Notes par familles en édition de notices
			$rqt = "ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_families_notes mediumtext not null after notice_nomenclature_notes";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_families_notes ");

			// AP - Ajout d'un droit de numérisation sur les notices
			// notice_scan_request_opac : Autorisation de demander une numérisation de la notice à l'OPAC
			$rqt = "ALTER TABLE notice_statut ADD notice_scan_request_opac tinyint(1) NOT NULL default 0";
			echo traite_rqt($rqt, "ALTER TABLE notice_statut ADD notice_scan_request_opac");
			// notice_scan_request_opac_abon : Autorisation uniquement pour les abonnés de demander une numérisation de la notice à l'OPAC
			$rqt = "ALTER TABLE notice_statut ADD notice_scan_request_opac_abon tinyint(1) NOT NULL default 0";
			echo traite_rqt($rqt, "ALTER TABLE notice_statut ADD notice_scan_request_opac_abon");

			// AP - Activation de la demande de numérisation en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_request_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'scan_request_activate', '0', 'Activer la demande de numérisation.\n0 : Non\n1 : Oui' ,'', 0)";
				echo traite_rqt($rqt,"insert pmb_scan_request_activate=0 into parametres");
			}

			// AP - Activation de la demande de numérisation en OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'scan_request_activate', '0', 'Activer la demande de numérisation.\n0 : Non\n1 : Oui' ,'f_modules', 0)";
				echo traite_rqt($rqt,"insert opac_scan_request_activate=0 into parametres");
			}

			// NG - Demande de numérisation: Ajout de l'interface de gestion de la liste des statuts.
			$rqt = "create table if not exists scan_request_status(
				id_scan_request_status int unsigned not null auto_increment primary key,
				scan_request_status_label varchar(255) not null default '',
				scan_request_status_opac_show int(1) not null default 0,
				scan_request_status_cancelable int(1) not null default 0,
				scan_request_status_infos_editable int(1) not null default 0,
				scan_request_status_class_html VARCHAR( 255 ) not NULL default ''
				)";
			echo traite_rqt($rqt, "create table scan_request_status");

			// NG - Demande de numérisation: Interface pour définir à partir d'un statut, les statuts suivants possibles (Workflow)
			$rqt = "create table if not exists scan_request_status_workflow(
				scan_request_status_workflow_from_num int unsigned not null default 0,
				scan_request_status_workflow_to_num int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table scan_request_status_workflow");

			// NG - Demande de numérisation: Interface pour définir les priorités des demandes
			$rqt = "create table if not exists scan_request_priorities(
				id_scan_request_priority int unsigned not null auto_increment primary key,
				scan_request_priority_label varchar(255) not null default '',
				scan_request_priority_weight int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table scan_request_priorities");

			// VT : Ajout dans les préférences utilisateur du statut par défaut à la création d'une demande de numérisation
			$rqt = "ALTER TABLE users ADD deflt_scan_request_status INT(1) UNSIGNED NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER users ADD deflt_scan_request_status");

			// VT - NG - Table des demandes de numérisation
			// id_scan_request  : Identifiant de la demande de numérisation
			// scan_request_title : Libellé du titre de la demande
			// scan_request_desc : Description de la demande
			// scan_request_num_status : Clé étrangère (correspondance dans la table scan_request_status)
			// scan_request_num_priority : Clé étrangère (correspondance dans la table scan_request_priorities)
			// scan_request_create_date : Date de création de la demande (machine)
			// scan_request_update_date : Date de mise à jour de la demande (machine)
			// scan_request_date : Date de la demande (humain)
			// scan_request_wish_date : Date de traitement de la demande souhaité (humain)
			// scan_request_deadline_date : Date butoir de la demande (humain)
			// scan_request_comment : Commentaire de la demande
			// scan_request_elapsed_time : Temps passé sur la demande
			// scan_request_num_dest_empr : ID du destinataire de la demande
			// scan_request_num_creator : Identifiant du créateur de la demande  (User Gestion ou usager OPAC)
			// scan_request_type_creator : Type du créateur (User Gestion ou usager OPAC)
			// scan_request_num_last_user : Dernier utilisateur à avoir travaillé sur la demande
			// scan_request_state : Défini l'état d'une demande par rapport aux actions de l'usager destinataire (0 = demande normale, 1=modifiée, 2=annulée)
			$rqt = "create table if not exists scan_requests(
				id_scan_request int unsigned not null auto_increment primary key,
				scan_request_title varchar(255) not null default '',
				scan_request_desc text,
				scan_request_num_status int unsigned not null default 0,
				scan_request_num_priority int unsigned not null default 0,
				scan_request_create_date datetime,
				scan_request_update_date datetime,
				scan_request_date datetime,
				scan_request_wish_date datetime,
				scan_request_deadline_date datetime,
				scan_request_comment text,
				scan_request_elapsed_time int unsigned not null default 0,
				scan_request_num_dest_empr int unsigned not null default 0,
				scan_request_num_creator int unsigned not null default 0,
				scan_request_type_creator int unsigned not null default 0,
				scan_request_num_last_user int unsigned not null default 0,
				scan_request_state int unsigned not null default 0,
				scan_request_as_folder int(1) unsigned not null default 0,
				scan_request_folder_num_notice int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table scan_requests");

			// VT - NG - Table des correspondance notices / demandes de numérisation
			// scan_request_linked_record_num_request  : Identifiant de la demande de numérisation
			// scan_request_linked_record_num_notice : Identifiant de la notice
			// scan_request_linked_record_num_bulletin : Identifiant du bulletin
			// scan_request_linked_record_comment : Commentaire lié à cette notice dans la demande 'num_request'
			// scan_request_linked_record_order : Ordre des éléments
			$rqt = "create table if not exists scan_request_linked_records(
				scan_request_linked_record_num_request int unsigned not null default 0,
				scan_request_linked_record_num_notice int unsigned not null default 0,
				scan_request_linked_record_num_bulletin int unsigned not null default 0,
				scan_request_linked_record_comment text,
				scan_request_linked_record_order int(3) unsigned not null default 0,
				PRIMARY KEY (scan_request_linked_record_num_request,scan_request_linked_record_num_notice,scan_request_linked_record_num_bulletin)
				)";
			echo traite_rqt($rqt, "create table scan_requests_linked_records");

			//AP & DG - Ajout d'un droit sur le statut pour les demandes de numérisation
			$rqt = "alter table empr_statut add allow_scan_request int unsigned not null default 0";
			echo traite_rqt($rqt,"alter table empr_statut add allow_scan_request");


			// AP & DG - Statut par défaut en création de demande de numérisation à l'OPAC (paramètre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_create_status' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'scan_request_create_status', '1', 'Statut de création à l\'OPAC','a_general',1)";
				echo traite_rqt($rqt,"insert opac_scan_request_create_status=1 into parametres");
			}

			// AP & DG - Statut par défaut après annulation à l'OPAC (paramètre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_cancel_status' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'scan_request_cancel_status', '1', 'Statut après annulation à l\'OPAC','a_general',1)";
				echo traite_rqt($rqt,"insert opac_scan_request_cancel_status=1 into parametres");
			}

			//DG - Statut "Sans statut particulier" ajouté par défaut pour les demandes de numérisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from scan_request_status where id_scan_request_status=1"))==0){
				$rqt = "insert into scan_request_status SET id_scan_request_status=1, scan_request_status_label='Sans statut particulier', scan_request_status_opac_show='1' ";
				echo traite_rqt($rqt,"insert minimum into scan_request_status");
			}
			
			// VT - Table des correspondance demandes de numérisation/document numérique
			// scan_request_explnum_num_request  : Identifiant de la demande de numérisation
			// scan_request_explnum_num_notice : Identifiant de la notice
			// scan_request_explnum_num_bulletin : Identifiant du bulletin
			// scan_request_explnum_num_explnum : Identifiant du document numérique
			$rqt = "create table if not exists scan_request_explnum(
				scan_request_explnum_num_request int unsigned not null default 0,
				scan_request_explnum_num_notice int unsigned not null default 0,
				scan_request_explnum_num_bulletin int unsigned not null default 0,
				scan_request_explnum_num_explnum int unsigned not null default 0,
				PRIMARY KEY (scan_request_explnum_num_request, scan_request_explnum_num_explnum)
				)";
			echo traite_rqt($rqt, "create table scan_requests_explnum");

			//NG - Ajout d'un paramètre renseignant le répertoire d'upload des documents numériques liés aux demandes de numérisation (paramètre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_request_explnum_folder' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'scan_request_explnum_folder', '0', 'Répertoire d\'upload des documents numériques liés aux demandes de numérisation', '', '1')";
					echo traite_rqt($rqt,"insert pmb_scan_request_explnum_folder=0 into parametres ");
				}

			// AP : Ajout dans les préférences utilisateur du type de notice par défaut à la création d'une notice de dossier de demande de numérisation
			$rqt = "ALTER TABLE users ADD xmlta_doctype_scan_request_folder_record VARCHAR(2) NOT NULL DEFAULT 'a' " ;
			echo traite_rqt($rqt,"ALTER users ADD xmlta_doctype_scan_request_folder_record='a'");

			//DG - Paramètre pour activer ou non le sélecteur d'accès rapide
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='quick_access' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'quick_access', '1', 'Activer le sélecteur d\'accès rapide ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_quick_access into parametres");
			}

			// AP & VT - Ajout de la colonne concept dans la table scan_request (permet de spécifier un concept pour indexer les documents numériques)
			$rqt = "ALTER TABLE scan_requests ADD scan_request_concept_uri VARCHAR(255) NOT NULL DEFAULT '' " ;
			echo traite_rqt($rqt,"ALTER scan_requests ADD scan_request_concept_uri");

			// AP & VT - Ajout de la colonne nb_scanned_pages dans la table scan_request (permet de renseigner le nombre de pages scannées dans la demande de numérisation)
			$rqt = "ALTER TABLE scan_requests ADD scan_request_nb_scanned_pages INT unsigned NOT NULL DEFAULT 0 " ;
			echo traite_rqt($rqt,"ALTER scan_requests ADD scan_request_nb_scanned_pages");

			// VT & AP - Modification de la table nomenclature_children_records : on ajoute une colonne pour stocker l'id de la nomenclature
			$rqt = "ALTER TABLE nomenclature_children_records ADD child_record_num_nomenclature int unsigned not null default 0";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_children_records ADD child_record_num_nomenclature int unsigned not null");

			//MB - Champ prix trop petit
			$rqt = "ALTER TABLE lignes_actes CHANGE prix prix DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
			echo traite_rqt($rqt,"ALTER lignes_actes CHANGE prix");

			//MB - Champ montant trop petit
			$rqt = "ALTER TABLE frais CHANGE montant montant DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
			echo traite_rqt($rqt,"ALTER frais CHANGE montant");

			//MB - Champ montant_global trop petit
			$rqt = "ALTER TABLE budgets CHANGE montant_global montant_global DOUBLE PRECISION(12,2) unsigned NOT NULL default '0.00'" ;
			echo traite_rqt($rqt,"ALTER budgets CHANGE montant_global");

			//DB - script de vérification de saisie d'une notice perso en integration
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='catalog_verif_js_integration' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'catalog_verif_js_integration', '', 'Script de vérification de saisie de notice en intégration','', 0)";
				echo traite_rqt($rqt,"insert pmb_catalog_verif_js_integration='' into parametres");
			}

			//NG & DG - Ajout de la table rent_pricing_systems
			// id_pricing_system : Identifiant du système de tarification
			// pricing_system_label : Nom
			// pricing_system_desc : Description
			// pricing_system_percents : Liste des pourcentages associés
			// pricing_system_num_exercice : Exercice comptable associé

			$rqt = "create table if not exists rent_pricing_systems(
				id_pricing_system int unsigned not null auto_increment primary key,
				pricing_system_label varchar(255) not null default '',
				pricing_system_desc text,
				pricing_system_percents text,
				pricing_system_num_exercice int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table rent_pricing_systems");

			//NG & DG - Ajout de la table rent_pricing_system_grids
			// id_pricing_system_grid : Identifiant incrémentiel
			// pricing_system_grid_num_system : Système de tarification associé
			// pricing_system_grid_time_start : Minutage de départ
			// pricing_system_grid_time_end : Minutage de fin
			// pricing_system_grid_price : Prix
			// pricing_system_grid_type : Type (1 : intervalle, 2 : Temps suppl, 3 : Non utilisé)
			$rqt = "create table if not exists rent_pricing_system_grids(
				id_pricing_system_grid int unsigned not null auto_increment primary key,
				pricing_system_grid_num_system int unsigned not null default 0,
				pricing_system_grid_time_start int unsigned not null default 0,
				pricing_system_grid_time_end int unsigned not null default 0,
				pricing_system_grid_price float(12,2) unsigned not null default 0,
				pricing_system_grid_type int unsigned not null default 0
				)";
			echo traite_rqt($rqt, "create table rent_pricing_system_grids");

			//NG & DG - Ajout de la table rent_account_sections
			// account_type_num_exercice : Exercice comptable associé
			// account_type_num_section : Rubrique budgétaire associée
			// account_type_marclist : Type de décompte associé
			$rqt = "create table if not exists rent_account_types_sections(
				account_type_num_exercice int unsigned not null default 0,
				account_type_num_section int unsigned not null default 0,
				account_type_marclist varchar(10) not null default '',
				PRIMARY KEY (account_type_num_section, account_type_marclist)
				)";
			echo traite_rqt($rqt, "create table rent_account_sections");

			//NG & DG - Ajout de la table rent_accounts
			// id_account : Identifiant du décompte
			// account_num_user : Identifiant de l'utilisateur qui l'a créé
			// account_num_exercice : Exercice comptable associé
			// account_type : Type de la demande
			// account_desc : Description
			// account_date : Date de création
			// account_receipt_limit_date : Date limite de réception
			// account_receipt_effective_date : Date effective de réception
			// account_return_date : Date de retour
			// account_num_authority : Exécution associée
			// account_title : Titre
			// account_event_date : Date de l'évènement
			// account_event_formation : Formation
			// account_event_orchestra : Chef d'orchestre
			// account_event_place : Lieu de l'évènement
			// account_num_publisher : Editeur associé
			// account_num_author : Compositeur associé
			// account_num_pricing_system : Système de tarification associé
			// account_time : Minutage
			// account_percent : Pourcentage
			// account_price : Prix
			// account_web : Diffusion Web (Oui/Non)
			// account_web_percent : Pourcentage Web
			// account_web_price : Prix Web
			$rqt = "create table if not exists rent_accounts(
				id_account int unsigned not null auto_increment primary key,
				account_num_user int unsigned not null default 0,
				account_num_exercice int unsigned not null default 0,
				account_type varchar(3) not null default '',
				account_desc text,
				account_date datetime,
				account_receipt_limit_date datetime,
				account_receipt_effective_date datetime,
				account_return_date datetime,
				account_num_uniform_title int unsigned not null default 0,
				account_title varchar(255) not null default '',
				account_event_date varchar(255) not null default '',
				account_event_formation varchar(255) not null default '',
				account_event_orchestra varchar(255) not null default '',
				account_event_place varchar(255) not null default '',
				account_num_publisher int unsigned not null default 0,
				account_num_author int unsigned not null default 0,
				account_num_pricing_system int unsigned not null default 0,
				account_time int unsigned not null default 0,
				account_percent float(8,2) unsigned not null default 0,
				account_price float(12,2) unsigned not null default 0,
				account_web int(1) unsigned not null default 0,
				account_web_percent float(8,2) unsigned not null default 0,
				account_web_price float(12,2) unsigned not null default 0,
				account_comment text
				)";
			echo traite_rqt($rqt, "create table rent_accounts");

			//NG & DG - Ajout de la table rent_invoices
			// id_invoice : Identifiant de la facture
			// invoice_num_user : Identifiant de l'utilisateur qui l'a créée
			// invoice_date : Date de génération
			// invoice_status : Enumération (0 = en cours, 1 = validée)
			// invoice_valid_date : Date de validation
			$rqt = "create table if not exists rent_invoices(
				id_invoice int unsigned not null auto_increment primary key,
				invoice_num_user int unsigned not null default 0,
				invoice_date datetime,
				invoice_status int unsigned not null default 1,
				invoice_valid_date datetime,
				invoice_destination varchar(10) not null default ''
				)";
			echo traite_rqt($rqt, "create table rent_invoices");

			//NG & DG - Ajout de la table rent_accounts_invoices
			// account_invoice_num_account : Identifiant du décompte associé
			// account_invoice_num_invoice : Identifiant de la facture associée
			$rqt = "create table if not exists rent_accounts_invoices(
				account_invoice_num_account int unsigned not null default 0,
				account_invoice_num_invoice int unsigned not null default 0,
				PRIMARY KEY (account_invoice_num_account, account_invoice_num_invoice)
				)";
			echo traite_rqt($rqt, "create table rent_accounts_invoices");

			//NG
			$rqt = "ALTER TABLE rent_accounts ADD account_request_type varchar(3) not null default '' after account_num_exercice " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_request_type ");

			//DG - Statut sur les demandes de location (commandé / non commandé)
			$rqt = "ALTER TABLE rent_accounts ADD account_request_status int(1) unsigned not null default 1 " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_request_status ");

			//NG
			$rqt = "ALTER TABLE rent_accounts ADD account_num_acte int unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_num_acte ");

			//NG
			$rqt = "ALTER TABLE rent_invoices ADD invoice_num_acte int unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE rent_invoices ADD invoice_num_acte ");

			//NG - Ajout d'un lien fournisseur dans la table des editeurs
			$rqt = "ALTER TABLE publishers ADD ed_num_entite int unsigned NOT NULL default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE publishers ADD ed_num_entite ");

			// DG - Afficher la possibilité pour le lecteur d'inscrire d'autres membres à ses listes
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='shared_lists_add_empr' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'shared_lists_add_empr', '0', 'Afficher la possibilité pour le lecteur d\'inscrire d\'autres membres à ses listes de lecture partagées \n 0 : Non \n 1 : Oui', 'a_general', '0')";
				echo traite_rqt($rqt,"insert opac_shared_lists_add_empr='0' into parametres ");
			}

			//DG - Gestion des avis - Notion de commentaire privé
			$rqt = "ALTER TABLE avis ADD avis_private int(1) unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE avis ADD avis_private ");

			//DG - Gestion des avis - Association d'une liste lecture
			$rqt = "ALTER TABLE avis ADD avis_num_liste_lecture int(8) unsigned not null default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE avis ADD avis_num_liste_lecture ");

			//DG - Listes de lecture - Saisie libre d'un tag
			$rqt = "ALTER TABLE opac_liste_lecture ADD tag varchar(255) not null default '' " ;
			echo traite_rqt($rqt,"ALTER TABLE opac_liste_lecture ADD tag ");

			// AP & NG - Ajout d'un paramètre URI du concept à associer aux partitions avant exécution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_music_concept_before' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nomenclature_music_concept_before', '0', 'URI du concept à associer aux partitions avant exécution', '', '1')";
				echo traite_rqt($rqt,"insert pmb_nomenclature_music_concept_before into parametres ");
			}

			// AP & NG - Ajout d'un paramètre URI du concept à associer aux partitions après exécution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_music_concept_after' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nomenclature_music_concept_after', '0', 'URI du concept à associer aux partitions après exécution', '', '1')";
				echo traite_rqt($rqt,"insert pmb_nomenclature_music_concept_after into parametres ");
			}

			// AP & NG - Ajout d'un paramètre URI du concept à associer aux partitions originales
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='nomenclature_music_concept_blank' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'nomenclature_music_concept_blank', '0', 'URI du concept à associer aux partitions originales', '', '1')";
				echo traite_rqt($rqt,"insert pmb_nomenclature_music_concept_blank into parametres ");
			}

			// AP & NG - Passage du paramètre nomenclature_record_children_link en caché
			$rqt = "UPDATE parametres SET gestion='1' where type_param= 'pmb' and sstype_param='nomenclature_record_children_link' ";
			echo traite_rqt($rqt,"update pmb_nomenclature_record_children_link");


			//AP & VT - Mise à jour du paramètre de définition de répertoire de template pour les affichages autorités en opac
			if(pmb_mysql_num_rows(pmb_mysql_query('select valeur_param from parametres where type_param= "opac" and sstype_param="authorities_templates_folder" and valeur_param like "%/%"'))){
				$rqt = 'UPDATE parametres set valeur_param = SUBSTRING_INDEX(valeur_param, "/", -1) where type_param= "opac" and sstype_param="authorities_templates_folder" ';
				echo traite_rqt($rqt,"UPDATE parametres opac_authorities_templates_folder");
			}

			//DG - Fournisseur associé au décompte de location
			$rqt = "ALTER TABLE rent_accounts ADD account_num_supplier int unsigned not null default 0 after account_num_publisher" ;
			echo traite_rqt($rqt,"ALTER TABLE rent_accounts ADD account_num_supplier ");

			//DG - Modification du champ account_event_date de la table rent_accounts
			$rqt = "ALTER TABLE rent_accounts MODIFY account_event_date datetime" ;
			echo traite_rqt($rqt,"alter table rent_accounts modify account_event_date");

			//DB Maj commentaires mail_methode
			$rqt = "update parametres set comment_param= 'Méthode d\'envoi des mails : \n php : fonction mail() de php\n smtp,hote:port,auth,user,pass,(ssl|tls) : en smtp, mettre O ou 1 pour l\'authentification... ' where sstype_param='mail_methode' ";
			echo traite_rqt($rqt,"update mail_methode comments");

			// DG & VT - Activation des actions rapides (dans le tableau de bord)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='dashboard_quick_params_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'dashboard_quick_params_activate', '1', 'Activer les actions rapides dans le tableau de bord.\n0 : Non\n1 : Oui' ,'', 0)";
				echo traite_rqt($rqt,"insert pmb_dashboard_quick_params_activate=1 into parametres");
			}

			//JP - Autoriser les montants négatifs dans les acquisitions
			$rqt = "ALTER TABLE frais CHANGE montant montant DOUBLE(12,2) NOT NULL DEFAULT '0.00'";
			echo traite_rqt($rqt,"ALTER TABLE frais CHANGE montant");
			$rqt = "ALTER TABLE lignes_actes CHANGE prix prix DOUBLE(12,2) NOT NULL DEFAULT '0.00'";
			echo traite_rqt($rqt,"ALTER TABLE lignes_actes CHANGE prix");

			//DG - accès rapide pour les paniers de lecteurs
			$rqt = "ALTER TABLE empr_caddie ADD acces_rapide INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE empr_caddie ADD acces_rapide");

			//JP - update bannette_aff_notice_number pour les bannettes privées
			$rqt = "UPDATE bannettes SET bannette_aff_notice_number = 1 WHERE proprio_bannette <> 0" ;
			echo traite_rqt($rqt,"UPDATE bannettes SET bannette_aff_notice_number ");
			echo traite_rqt($rqt,"UPDATE bannettes SET bannette_aff_notice_number ");

			//NG - Mémorisation de l'utilisateur qui fait une demande de transfert
			$rqt = "ALTER TABLE transferts ADD transfert_ask_user_num INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE transferts ADD transfert_ask_user_num");

			//AP - Ajout du nom de groupe dans la table des circulations en cours
			$rqt = "ALTER TABLE serialcirc_circ ADD serialcirc_circ_group_name varchar(255) NOT NULL DEFAULT ''";
			echo traite_rqt($rqt,"ALTER TABLE serialcirc_circ add serialcirc_circ_group_name default ''");

			//DG - Avis privé par défaut
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='avis_default_private' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'avis_default_private', '0', 'Avis privé par défaut ? \n 0 : Non \n 1 : Oui', 'a_general', 0) ";
				echo traite_rqt($rqt,"insert opac_avis_default_private into parametres") ;
			}

			//JP - Alerter l'utilisateur par mail des nouvelles demandes d'inscription aux listes de circulation
			$rqt = "ALTER TABLE users ADD user_alert_serialcircmail INT(1) UNSIGNED NOT NULL DEFAULT 0 after user_alert_subscribemail";
			echo traite_rqt($rqt,"ALTER TABLE users add user_alert_serialcircmail default 0");

			//JP - Enrichir les flux rss générés par les veilles
			$rqt = "ALTER TABLE docwatch_watches ADD watch_rss_link VARCHAR(255) NOT NULL, ADD watch_rss_lang VARCHAR(255) NOT NULL, ADD watch_rss_copyright VARCHAR(255) NOT NULL, ADD watch_rss_editor VARCHAR(255) NOT NULL, ADD watch_rss_webmaster VARCHAR(255) NOT NULL, ADD watch_rss_image_title VARCHAR(255) NOT NULL, ADD watch_rss_image_website VARCHAR(255) NOT NULL";
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches add watch_rss_link, watch_rss_lang, watch_rss_copyright, watch_rss_editor, watch_rss_webmaster, watch_rss_image_title, watch_rss_image_website");

			//JP - Possibilité de nettoyer le contenu HTML dans un OAI entrant
			$rqt = "ALTER TABLE connectors_sources ADD clean_html INT(3) UNSIGNED NOT NULL DEFAULT '0'";
			echo traite_rqt($rqt,"ALTER TABLE connectors_sources add clean_html");

			//Alexandre - Préremplissage de la vignette des dépouillements avec la vignette du bulletin
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='bulletin_thumbnail_url_article' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'bulletin_thumbnail_url_article', '0', 'Préremplissage de l\'url de la vignette des dépouillements avec l\'url de la vignette de la notice bulletin en catalogage des périodiques ? \n 0 : Non \n 1 : Oui', '',0) ";
				echo traite_rqt($rqt, "insert pmb_bulletin_thumbnail_url_article=0 into parametres");
			}

			//DG - Grilles exemplaires éditables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='form_expl_editables' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'form_expl_editables', '1', 'Grilles exemplaires éditables ? \n 0 non \n 1 oui','',0)";
				echo traite_rqt($rqt,"insert pmb_form_expl_editables into parametres");
			}

			//DG - Grilles exemplaires numériques éditables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='form_explnum_editables' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'form_explnum_editables', '1', 'Grilles exemplaires numériques éditables ? \n 0 non \n 1 oui','',0)";
				echo traite_rqt($rqt,"insert pmb_form_explnum_editables into parametres");
			}

			//DG - Mis à jour de la table grilles_auth (pour ceux qui ont une version récente de PMB) pour la rendre générique
			if (pmb_mysql_query("select * from grilles_auth")){
				$rqt = "RENAME TABLE grilles_auth TO grids_generic";
				echo traite_rqt($rqt, "rename grilles_auth to grids_generic");

				// DG - Renommage de la colonne "grille_auth_type" en "grid_generic_type"
				$rqt = "ALTER TABLE grids_generic CHANGE grille_auth_type grid_generic_type VARCHAR(32) not null default ''";
				echo traite_rqt($rqt,"alter grids_generic change grille_auth_type grid_generic_type");

				// DG - Renommage de la colonne "grille_auth_filter" en "grid_generic_filter"
				$rqt = "ALTER TABLE grids_generic CHANGE grille_auth_filter grid_generic_filter VARCHAR(255) null default ''";
				echo traite_rqt($rqt,"alter grids_generic change grille_auth_filter grid_generic_filter");

				// DG - Renommage de la colonne "grille_auth_descr_format" en "grid_generic_descr_format"
				$rqt = "ALTER TABLE grids_generic CHANGE grille_auth_descr_format grid_generic_data mediumblob NOT NULL";
				echo traite_rqt($rqt,"alter grids_generic change grille_auth_descr_format grid_generic_data");
			}

			//NG - Mémorisation de l'utilisateur qui fait l'envoi de transfert
			$rqt = "ALTER TABLE transferts ADD transfert_send_user_num INT NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE transferts ADD transfert_send_user_num");

			// DG - Création de la table de stockage des objets pour le formulaire de contact opac
			// id_object : Identifiant de l'objet
			// object_label : Libellé de l'objet
			$rqt = "create table if not exists contact_form_objects(
				id_object int unsigned not null auto_increment primary key,
				object_label varchar(255) not null default '') ";
			echo traite_rqt($rqt,"create table contact_form_objects");

			//DG - Formulaire de contact - Paramétrage général
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contact_form_parameters' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contact_form_parameters','','Paramétrage général du formulaire de contact','',1)";
				echo traite_rqt($rqt,"insert pmb_contact_form_parameters into parametres");
			}

			//DG - Formulaire de contact - Paramétrage des listes de destinataires
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contact_form_recipients_lists' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contact_form_recipients_lists','','Paramétrage des listes de destinataires du formulaire de contact','',1)";
				echo traite_rqt($rqt,"insert pmb_contact_form_recipients_lists into parametres");
			}

			//DG - Paramètre pour afficher ou non le formulaire de contact
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='contact_form' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'contact_form', '0', 'Afficher le formulaire de contact ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_contact_form into parametres");
			}

			//JP - Paramètre inutilisé caché
			$rqt = "update parametres set gestion=1 where type_param= 'opac' and sstype_param='categories_categ_sort_records' ";
			echo traite_rqt($rqt,"update categories_categ_sort_records hide into parametres");

			//JP - date de création et créateur des paniers
			$rqt = "ALTER TABLE caddie ADD creation_user_name VARCHAR(255) NOT NULL DEFAULT '', ADD creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"ALTER TABLE caddie add creation_user_name, creation_date");
			$rqt = "ALTER TABLE empr_caddie ADD creation_user_name VARCHAR(255) NOT NULL DEFAULT '', ADD creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			echo traite_rqt($rqt,"ALTER TABLE empr_caddie add creation_user_name, creation_date");

			//NG - Mémorisation de la date demandée par l'utilisateur en demande de transfert
			$rqt = "ALTER TABLE transferts ADD transfert_ask_date date NOT NULL default '0000-00-00'";
			echo traite_rqt($rqt,"ALTER TABLE transferts ADD transfert_ask_date");

			//DG - Ajout du type sur les recherches prédéfinies gestion (Pour spécifier Notices / Autorités)
			$rqt = "ALTER TABLE search_perso ADD search_type varchar(255) not null default 'RECORDS' after search_id";
			echo traite_rqt($rqt,"ALTER TABLE search_perso ADD search_type") ;

			//DG - Nomenclatures : Note pour les instruments non standard en édition de notices
			$rqt = "ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_exotic_instruments_note text not null after notice_nomenclature_families_notes";
			echo traite_rqt($rqt,"ALTER TABLE nomenclature_notices_nomenclatures ADD notice_nomenclature_exotic_instruments_note ");

			//TS - Ajout d'une classe CSS sur les cadres du portail
			$rqt = "ALTER TABLE cms_cadres ADD cadre_css_class VARCHAR(255) NOT NULL DEFAULT '' AFTER cadre_modcache";
			echo traite_rqt($rqt,"ALTER TABLE cms_cadres ADD cadre_css_class");
			//DG - Personnalisables de notices : Ordre pour les champs répétables
			$rqt = "ALTER TABLE notices_custom_values ADD notices_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE notices_custom_values ADD notices_custom_order");

			//DG - Personnalisables de auteurs : Ordre pour les champs répétables
			$rqt = "ALTER TABLE author_custom_values ADD author_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE author_custom_values ADD author_custom_order");

			//DG - Personnalisables de catégories : Ordre pour les champs répétables
			$rqt = "ALTER TABLE categ_custom_values ADD categ_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE categ_custom_values ADD categ_custom_order");

			//DG - Personnalisables de éditeurs : Ordre pour les champs répétables
			$rqt = "ALTER TABLE publisher_custom_values ADD publisher_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE publisher_custom_values ADD publisher_custom_order");

			//DG - Personnalisables de collections : Ordre pour les champs répétables
			$rqt = "ALTER TABLE collection_custom_values ADD collection_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE collection_custom_values ADD collection_custom_order");

			//DG - Personnalisables de sous-collections : Ordre pour les champs répétables
			$rqt = "ALTER TABLE subcollection_custom_values ADD subcollection_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE subcollection_custom_values ADD subcollection_custom_order");

			//DG - Personnalisables de titres de séries : Ordre pour les champs répétables
			$rqt = "ALTER TABLE serie_custom_values ADD serie_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE serie_custom_values ADD serie_custom_order");

			//DG - Personnalisables de titres uniformes : Ordre pour les champs répétables
			$rqt = "ALTER TABLE tu_custom_values ADD tu_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE tu_custom_values ADD tu_custom_order");

			//DG - Personnalisables d'indexations décimales : Ordre pour les champs répétables
			$rqt = "ALTER TABLE indexint_custom_values ADD indexint_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE indexint_custom_values ADD indexint_custom_order");

			//DG - Personnalisables d'autorités perso : Ordre pour les champs répétables
			$rqt = "ALTER TABLE authperso_custom_values ADD authperso_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE authperso_custom_values ADD authperso_custom_order");

			//DG - Personnalisables du contenu éditorial : Ordre pour les champs répétables
			$rqt = "ALTER TABLE cms_editorial_custom_values ADD cms_editorial_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_custom_values ADD cms_editorial_custom_order");

			//DG - Personnalisables d'états des collections : Ordre pour les champs répétables
			$rqt = "ALTER TABLE collstate_custom_values ADD collstate_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE collstate_custom_values ADD collstate_custom_order");

			//DG - Personnalisables de demandes : Ordre pour les champs répétables
			$rqt = "ALTER TABLE demandes_custom_values ADD demandes_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE demandes_custom_values ADD demandes_custom_order");

			//DG - Personnalisables de lecteurs : Ordre pour les champs répétables
			$rqt = "ALTER TABLE empr_custom_values ADD empr_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE empr_custom_values ADD empr_custom_order");

			//DG - Personnalisables d'exemplaires : Ordre pour les champs répétables
			$rqt = "ALTER TABLE expl_custom_values ADD expl_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE expl_custom_values ADD expl_custom_order");

			//DG - Personnalisables de fiches : Ordre pour les champs répétables
			$rqt = "ALTER TABLE gestfic0_custom_values ADD gestfic0_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE gestfic0_custom_values ADD gestfic0_custom_order");

			//DG - Personnalisables de prêts : Ordre pour les champs répétables
			$rqt = "ALTER TABLE pret_custom_values ADD pret_custom_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE pret_custom_values ADD pret_custom_order");


			//DG - Paramètre pour afficher ou non le lien de génération d'un flux RSS de la recherche
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='short_url' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'short_url', '1', 'Afficher le lien permettant de générer un flux RSS de la recherche ? \n0 : Non 1 : Oui','d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert opac_short_url=1 into parametres");
			}

			//AR - Les index, c'est pratique...
			$rqt = "alter table tu_oeuvres_events add index i_toe_oeuvre_event_tu_num(oeuvre_event_tu_num)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "alter table tu_oeuvres_events add index i_toe_oeuvre_event_authperso_authority_num(oeuvre_event_authperso_authority_num)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "alter table notices_titres_uniformes add index i_ntu_ntu_num_tu(ntu_num_tu)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE authorities ADD INDEX i_a_num_object_type_object(num_object,type_object)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE authorities ADD INDEX i_a_num_statut(num_statut)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE titres_uniformes ADD INDEX i_tu_tu_oeuvre_type(tu_oeuvre_type)";
			echo traite_rqt($rqt,$rqt);

			$rqt = "ALTER TABLE titres_uniformes ADD INDEX i_tu_tu_oeuvre_nature(tu_oeuvre_nature)";
			echo traite_rqt($rqt,$rqt);

			// AP & DG - Création de la table de planches d'étiquettes
			// id_sticks_sheet : Identifiant
			// sticks_sheet_label : Libellé
			// sticks_sheet_data : Structure JSON des données de génération
			// sticks_sheet_order : Ordre
			$rqt = "create table if not exists sticks_sheets(
				id_sticks_sheet int unsigned not null auto_increment primary key,
				sticks_sheet_label varchar(255) not null default '',
				sticks_sheet_data text not null,
				sticks_sheet_order int(11) NOT NULL default 0) ";
			echo traite_rqt($rqt,"create table sticks_sheets");

			// AP & DG - Création de la table de seuils de commandes
			// id_threshold : Identifiant
			// threshold_label : Libellé
			// threshold_amount : Montant du seuil
			// threshold_amount_tax_included : Montant du seuil HT/TTC
			// threshold_footer : Pied de page Signature
			// threshold_num_entity : Etablissement associé
			$rqt = "create table if not exists thresholds(
				id_threshold int unsigned not null auto_increment primary key,
				threshold_label varchar(255) not null default '',
				threshold_amount float NOT NULL DEFAULT 0,
				threshold_amount_tax_included int(1) NOT NULL DEFAULT 0,
				threshold_footer text not null,
				threshold_num_entity int(11) NOT NULL default 0) ";
			echo traite_rqt($rqt,"create table thresholds");

			// DG & AP - Ajout d'un paramètre pour activer la gestion avancées des états des collections
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='collstate_advanced' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (NULL, 'pmb', 'collstate_advanced', '0', 'Activer la gestion avancée des états des collections :\n 0 : non activée \n 1 : activée', '', '0')";
				echo traite_rqt($rqt,"insert collstate_advanced='0' into parametres ");
			}

			// DG & AP - Création de la table de liaison entre bulletins et états des collections
			// collstate_bulletins_num_collstate : Identifiant de la collection
			// collstate_bulletins_num_bulletin : Identifiant du bulletin
			// collstate_bulletins_order : Ordre
			$rqt = "create table if not exists collstate_bulletins(
				collstate_bulletins_num_collstate int(8) not null default 0,
				collstate_bulletins_num_bulletin int(8) not null default 0,
				collstate_bulletins_order int(8) NOT NULL default 0,
				primary key (collstate_bulletins_num_collstate, collstate_bulletins_num_bulletin)) ";
			echo traite_rqt($rqt,"create table collstate_bulletins");

			//DG - OPAC : Personnalisation de la pagination des listes
			if(pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='items_pagination_custom' "))==0){
				$rqt = "INSERT INTO parametres ( id_param , type_param , sstype_param , valeur_param , comment_param , section_param , gestion )
				VALUES (0 , 'opac', 'items_pagination_custom', '25,50,100,200', 'Personnalisation de valeurs numériques supplémentaires dans les listes paginées, séparées par des virgules : 25,50,100,200.', 'a_general', '0')";
				echo traite_rqt($rqt, "insert opac_items_pagination_custom=25,50,100,200 into parametres");
			}

			//DG - Gestion : Personnalisation de la pagination des listes
			if(pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='items_pagination_custom' "))==0){
				$rqt = "INSERT INTO parametres ( id_param , type_param , sstype_param , valeur_param , comment_param , section_param , gestion )
				VALUES (0 , 'pmb', 'items_pagination_custom', '25,50,100,200', 'Personnalisation de valeurs numériques supplémentaires dans les listes paginées, séparées par des virgules : 25,50,100,200.', '', '0')";
				echo traite_rqt($rqt, "insert pmb_items_pagination_custom=25,50,100,200 into parametres");
			}

			//DG - Attribution des droits de construction de portail au Super User uniquement
			$rqt = "update users set rights=rights+1048576 where rights<1048576 and userid=1";
			echo traite_rqt($rqt, "update users add rights cms_build only for Super User");

			//DG - Ajout de l'ordre sur les recherches prédéfinies OPAC
			$rqt = "ALTER TABLE search_persopac ADD search_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE search_persopac ADD search_order") ;

			//JP - Message personnalisé sur la page de connexion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='login_message' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'pmb', 'login_message', '', 'Message à afficher sur la page de connexion','', 0)";
				echo traite_rqt($rqt,"insert pmb_login_message into parametres");
			}

			//JP - éviter les codes-barre identiques en création d'emprunteur
			$rqt = "create table if not exists empr_temp (cb varchar( 255 ) NOT NULL ,sess varchar( 12 ) NOT NULL ,UNIQUE (cb))";
			echo traite_rqt($rqt,"create table  if not exists empr_temp ") ;

			//NG - Ajout d'un paramètre utilisateur permettant d'activer la webcam
			$rqt = "alter table users add deflt_camera_empr int not null default 0";
			echo traite_rqt($rqt,"alter table users add deflt_camera_empr");

			//JP - changement du séparateur des tris de l'opac pour pouvoir utiliser le parse HTML
			$rqt = "UPDATE parametres SET valeur_param=REPLACE(valeur_param,';','||'), comment_param='Afficher la liste déroulante de sélection d\'un tri ? \n 0 : Non \n 1 : Oui \nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)' WHERE type_param='opac' AND sstype_param='default_sort_list'";
			echo traite_rqt($rqt,"update value into parametres") ;

			//JP - Recherche étendue aux oeuvres sur paramètre
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_search_into_linked_elements' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'pmb', 'allow_search_into_linked_elements', '2', 'Afficher la case à cocher permettant d\'étendre la recherche aux oeuvres ?\n 0 : Non\n 1 : Oui, cochée par défaut\n 2 : Oui, non-cochée par défaut','search', 0)";
				echo traite_rqt($rqt,"insert pmb_allow_search_into_linked_elements into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='allow_search_into_linked_elements' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'allow_search_into_linked_elements', '2', 'Afficher la case à cocher permettant d\'étendre la recherche aux oeuvres ?\n 0 : Non\n 1 : Oui, cochée par défaut\n 2 : Oui, non-cochée par défaut','c_recherche', 0)";
				echo traite_rqt($rqt,"insert opac_allow_search_into_linked_elements into parametres");
			}

			//DG - Typage des avis pour combiner les notices et les articles du contenu éditorial..pour le moment..
			$rqt = "ALTER TABLE avis ADD type_object mediumint(8) NOT NULL AFTER num_notice";
			echo traite_rqt($rqt,"ALTER TABLE avis ADD type_object") ;
			//DG - On affecte les avis existants de type AVIS_RECORDS
			$rqt = "UPDATE avis set type_object = 1, dateajout = dateajout where type_object = 0";
			echo traite_rqt($rqt,"UPDATE type_object = 1 FOR records avis") ;

			//VT - Ajout de deux éléments dans la table cms_editorial_types (id d'une page et variable d'environnement) permettant de générer un permalink
			$rqt = "ALTER TABLE cms_editorial_types ADD editorial_type_permalink_num_page int(11) NOT NULL default 0, ADD editorial_type_permalink_var_name varchar(255) not null default ''";
			echo traite_rqt($rqt,"ALTER TABLE cms_editorial_types ADD editorial_type_permalink_num_page, ADD editorial_type_permalink_var_name") ;

			//VT - Définition du mode de génération du flux rss de recherche (le paramètre opac_short_url doit être activé)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='short_url_mode' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'short_url_mode', '0', 'Elements générés dans le flux rss de la recherche: \n0 : Nouveautés \n1 : Résultats de la recherche \nPour le mode 1, un nombre de résultats limite peut être ajouté après le mode, il doit être précédé d\'une virgule\nExemple: 1,30\nSi aucune limite n\'est spécifiée, c\'est le paramètre opac_search_results_per_page qui sera pris en compte','d_aff_recherche',0)";
				echo traite_rqt($rqt,"insert opac_short_url_mode=0 into parametres");
			}

			//VT - Ajout d'un paramètre autorisant la réservation d'une notice sans exemplaire
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='resa_records_no_expl' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'resa_records_no_expl', '0', 'Réservation sur les notices \n 0 : Non \n 1 : Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_resa_records_no_expl=0 into parametres");
			}

			//VT - Création d'une table permettant de stocker les identifiants des emprunteurs ayant suggéré la commande
			$rqt = "create table if not exists lignes_actes_applicants(
				ligne_acte_num int not null default 0,
				empr_num int not null default 0,
				primary key (ligne_acte_num, empr_num)) ";
			echo traite_rqt($rqt,"create table lignes_actes_applicants");

			//NG - OPAC : Ajout du paramètre activant la déconnexion dans le menu d'accès rapide et désactivant le lien de déconnexion classique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='quick_access_logout' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'quick_access_logout', '0', 'Activer le menu de déconnexion dans le sélecteur d\'accès rapide ? \n0 : Non 1 : Oui','a_general',0)";
				echo traite_rqt($rqt,"insert opac_quick_access_logout into parametres");
			}

			//NG - Ajout du libellé opac des classements de bannettes publiques
			$rqt = "ALTER TABLE classements ADD classement_opac_name varchar(255) not null default ''";
			echo traite_rqt($rqt,"ALTER TABLE classements ADD classement_opac_name") ;

			//NG - Ajout de l'ordre dans les classements de bannettes publiques
			$rqt = "ALTER TABLE classements ADD classement_order int NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE classements ADD classement_order") ;

			//NG - Attribution d'un ordre par défault pour le fonctionnement du tri des classement des classement de bannettes
			$rqt = "update classements set classement_order=id_classement where (type_classement='BAN' or type_classement='')";
			echo traite_rqt($rqt, "update classements set classement_order");

			//NG - Ajout du paramètre du répertoire et motif d'upload des photos des emprunteurs, dans le motif fourni, !!num_carte!! sera remplacé par le numéro de carte du lecteur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='pics_folder' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'empr', 'pics_folder', '', 'Répertoire et motif d\'upload des photos des emprunteurs, dans le motif fourni, !!num_carte!! sera remplacé par le numéro de carte du lecteur. \n exemple : ./photos/lecteurs/!!num_carte!!.jpg \n ATTENTION : cohérence avec empr_pics_url à vérifier','',0)";
				echo traite_rqt($rqt,"insert empr_pics_folder into parametres");
			}

			//NG - Activer la recherche de notices déjà présentes en saisie d'une suggestion d'acquisition
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='suggestion_search_notice_doublon' "))==0){
			$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES(0,'opac','suggestion_search_notice_doublon','0','Activer la recherche de notices déjà présentes en saisie d\'une suggestion d\'acquisition\n0 : Désactiver\n1 : Activer\n2 : Activer avec le levenshtein\n    NB : Cette fonction nécessite l\'installation de l\'extension levenshtein dans MySQL','c_recherche',0)" ;
				echo traite_rqt($rqt,"insert opac_suggestion_search_notice_doublon into parametres") ;
			}

			//TS & NG - Ajout de la notion de propriétaire pour les documents numériques
			$rqt = "create table if not exists explnum_lenders(
				explnum_lender_num_explnum int not null default 0,
				explnum_lender_num_lender int not null default 0,
				primary key (explnum_lender_num_explnum, explnum_lender_num_lender)) ";
			echo traite_rqt($rqt,"create table explnum_lenders");

			// VT - Ajout d'un paramètre permettant de d'activer/désactiver l'affinage des recherches à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='search_allow_refinement' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'search_allow_refinement', '1', 'Afficher le lien \"Affiner la recherche\" en résultat de recherche','c_recherche', 0)";
				echo traite_rqt($rqt,"insert opac_search_allow_refinement into parametres");
			}

			//DG - Modification du commentaire opac_etagere_notices_format
			$rqt = "update parametres set comment_param='Format d\'affichage des notices dans les étagères de l\'écran d\'accueil \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)' where type_param='opac' and sstype_param='etagere_notices_format'" ;
			echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_etagere_notices_format") ;

			//DG - Modification du commentaire opac_bannette_notices_format
			$rqt = "update parametres set comment_param='Format d\'affichage des notices dans les bannettes \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)' where type_param='opac' and sstype_param='bannette_notices_format'" ;
			echo traite_rqt($rqt,"UPDATE parametres SET comment_param for opac_bannette_notices_format") ;

			// NG - Création de la table mémorisant les groupes de lecteurs abonnés à une bannette
			// empr_groupe_num_bannette : Identifiant de bannette
			// empr_groupe_num_groupe : Identifiant de groupe de lecteur
			$rqt = "create table if not exists bannette_empr_groupes(
				empr_groupe_num_bannette int unsigned not null default 0,
				empr_groupe_num_groupe int unsigned not null default 0,
				PRIMARY KEY (empr_groupe_num_bannette, empr_groupe_num_groupe) )";
			echo traite_rqt($rqt,"create table bannette_empr_groupes");

			// NG - Création de la table mémorisant les catégories de lecteurs abonnés à une bannette
			// empr_categ_num_bannette : Identifiant de bannette
			// empr_categ_num_categ : Identifiant de categorie de lecteur
			$rqt = "create table if not exists bannette_empr_categs(
				empr_categ_num_bannette int unsigned not null default 0,
				empr_categ_num_categ int unsigned not null default 0,
				PRIMARY KEY (empr_categ_num_bannette, empr_categ_num_categ) )";
			echo traite_rqt($rqt,"create table bannette_empr_categs");

			$rqt = "insert into bannette_empr_groupes (empr_groupe_num_bannette, empr_groupe_num_groupe)
					select id_bannette, groupe_lecteurs from bannettes where groupe_lecteurs > 0";
			echo traite_rqt($rqt,"insert into bannette_empr_groupes");

			$rqt = "insert into bannette_empr_categs (empr_categ_num_bannette, empr_categ_num_categ)
					select id_bannette, categorie_lecteurs from bannettes where categorie_lecteurs > 0";
			echo traite_rqt($rqt,"insert into bannette_empr_categs");

			//Alexandre - Suppression du code pour les articles de périodiques
			$rqt = "UPDATE notices SET code='',update_date=update_date WHERE niveau_biblio='a'";
			echo traite_rqt($rqt,"UPDATE notices SET code for niveau_biblio=a");

			// TS & NG - Définition de la couleur d'une emprise de localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_holds_location_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_holds_location_color', '#D60F0F', 'Couleur des emprises associées à des localisations','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_holds_location_color into parametres");
			}

			// TS & NG - Ajout paramètre de la taille de la carte en édition de localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_location_edition' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_location_edition', '800*480', 'Taille de la carte en édition de localisation en pixels, L*H, exemple : 800*480','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_location_edition into parametres");
			}

			// TS & NG - Ajout paramètre de la taille de la carte en visualisation des localisations
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_size_location_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_size_location_view', '800*480', 'Taille de la carte en visualisation des localisations en pixels, L*H, exemple : 800*480','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_size_location_view into parametres");
			}

			// TS & NG - Définition de la couleur d'une emprise de localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_location_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_holds_location_color', '#D60F0F', 'Couleur des emprises associées à des localisations en RVB, exemple : #D60F0F','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_location_color into parametres");
			}

			// TS & NG - Ajout paramètre de la taille de la carte en visualisation des localisations
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_location_view' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_size_location_view', '800*480', 'Taille de la carte en visualisation des localisationsen pixels, L*H, exemple : 800*480','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_location_view into parametres");
			}

			//JP - Templates de bannette pour dsi privées
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_tpl' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'dsi', 'private_bannette_tpl', '0', 'Identifiant du template de bannette à appliquer sur les bannettes privées \nSi vide ou à 0, l\'entête et pied de page par défaut seront utilisés.','', 0)";
				echo traite_rqt($rqt,"insert dsi_private_bannette_tpl into parametres");
			}

			//VT - Ajout d'un paramètre qui active l'envoi de mail au premier réservataire d'une notice en cas de prolongation du prêt
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pdflettreresa' and sstype_param='resa_prolong_email' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pdflettreresa', 'resa_prolong_email', '0', 'Envoi d\'un mail au réservataire de rang 1, dont réservation validée, lorsque le prêt en cours est prolongé \n Non : 0 \n Oui : id_template ','',0)";
				echo traite_rqt($rqt,"insert pmb_resa_prolong_email=0 into parametres");
			}

			// VT - Ajout d'un paramètre permettant de masquer les +/- dans les listes de résultats à l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='recherche_show_expand' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'recherche_show_expand', '1', 'Affichage des boutons de dépliage de toutes les notices dans les listes de résultats à l\'OPAC \n0: Boutons non affichés \n1: Boutons affichés','c_recherche', 0)";
				echo traite_rqt($rqt,"insert opac_recherche_show_expand into parametres");
			}

			//DG - Paramètre Portail : Activer l'onglet Toolkits
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'cms' and sstype_param='active_toolkits' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'cms', 'active_toolkits', '0', 'Activer la possibilité de gérer des toolkits.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert cms_active_toolkits into parametres");
			}

			// DG - Création de la table pour les toolkits d'aide à la construction d'un portail
			// cms_toolkit_name : Nom du toolkit
			// cms_toolkit_active : Activé Oui/Non
			// cms_toolkit_data : Données
			// cms_toolkit_order : Ordre
			$rqt = "create table if not exists cms_toolkits(
				cms_toolkit_name varchar(255) not null default '' primary key,
				cms_toolkit_active int(1) NOT NULL DEFAULT 0,
				cms_toolkit_data text not null,
				cms_toolkit_order int(3) unsigned not null default 0)";
			echo traite_rqt($rqt,"create table cms_toolkits");

			//DG - Ajout de l'ordre sur les recherches prédéfinies Gestion
			$rqt = "ALTER TABLE search_perso ADD search_order int(11) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE search_perso ADD search_order") ;

			//JP - Champs persos sur les documents numériques
			$rqt = "create table if not exists explnum_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				num_type int unsigned not null default 0,
				name varchar(255) NOT NULL default '',
				titre varchar(255) default NULL,
				type varchar(10) NOT NULL default 'text',
				datatype varchar(10) NOT NULL default '',
				options text,
				multiple int(11) NOT NULL default 0,
				obligatoire int(11) NOT NULL default 0,
				ordre int(11) default NULL,
				search INT(1) unsigned NOT NULL DEFAULT 0,
				export INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
				comment BLOB NOT NULL default '',
				PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table explnum_custom ");

			$rqt = "create table if not exists explnum_custom_lists (
				explnum_custom_champ int(10) unsigned NOT NULL default 0,
				explnum_custom_list_value varchar(255) default NULL,
				explnum_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY explnum_custom_champ (explnum_custom_champ),
				KEY explnum_champ_list_value (explnum_custom_champ,explnum_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table explnum_custom_lists ");

			$rqt = "create table if not exists explnum_custom_values (
				explnum_custom_champ int(10) unsigned NOT NULL default 0,
				explnum_custom_origine int(10) unsigned NOT NULL default 0,
				explnum_custom_small_text varchar(255) default NULL,
				explnum_custom_text text,
				explnum_custom_integer int(11) default NULL,
				explnum_custom_date date default NULL,
				explnum_custom_float float default NULL,
				explnum_custom_order int(11) NOT NULL default 0,
				KEY explnum_custom_champ (explnum_custom_champ),
				KEY i_encv_st (explnum_custom_small_text),
				KEY i_encv_t (explnum_custom_text(255)),
				KEY i_encv_i (explnum_custom_integer),
				KEY i_encv_d (explnum_custom_date),
				KEY i_encv_f (explnum_custom_float),
				KEY explnum_custom_origine (explnum_custom_origine)) " ;
			echo traite_rqt($rqt,"create table explnum_custom_values ");

			//JP - paramètre utilisateur : paniers du catalogue dépliés ou non par défaut
			$rqt = "ALTER TABLE users ADD deflt_catalog_expanded_caddies INT( 1 ) UNSIGNED NOT NULL DEFAULT 1";
			echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_catalog_expanded_caddies");
			
			//JP - Ajout du champ de notice Droit d'usage
			$rqt = "CREATE TABLE if not exists notice_usage (id_usage INT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT , usage_libelle VARCHAR( 255 ) NOT NULL default '', PRIMARY KEY ( id_usage ) , INDEX ( usage_libelle ) ) " ;
			echo traite_rqt($rqt,"CREATE TABLE notice_usage ");
			$rqt = "ALTER TABLE notices ADD num_notice_usage INT( 8 ) UNSIGNED DEFAULT 0 NOT NULL ";
			echo traite_rqt($rqt,"ALTER TABLE notices ADD num_notice_usage ");

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;		
			$res = pmb_mysql_query($rqt, $dbh) ;

			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.22");


	
	//case "v5.22":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
					
			// DG - Veilles : Option pour filtrer les nouveaux items avec une expression booléènne au niveau de la source
			$rqt = "ALTER TABLE docwatch_datasources ADD datasource_boolean_expression varchar(255) not null default '' after datasource_clean_html" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_datasources ADD datasource_boolean_expression ");
				
			// DG - Veilles : Option pour filtrer les nouveaux items avec une expression booléènne au niveau de la veille
			$rqt = "ALTER TABLE docwatch_watches ADD watch_boolean_expression varchar(255) not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_watches ADD watch_boolean_expression ");
				
			// DG - Items de veilles : Index sans les mots vides
			$rqt = "ALTER TABLE docwatch_items ADD item_index_sew mediumtext not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_items ADD item_index_sew ");
				
			// DG - Items de veilles : Index avec les mots vides
			$rqt = "ALTER TABLE docwatch_items ADD item_index_wew mediumtext not null default ''" ;
			echo traite_rqt($rqt,"ALTER TABLE docwatch_items ADD item_index_wew ");
				
			// DG - Création d'une table pour stocker le source des sites surveillés
			// datasource_monitoring_website_num_datasource : Identifiant de la source
			// datasource_monitoring_website_upload_date : Date du téléchargement
			// datasource_monitoring_website_content : Contenu
			// datasource_monitoring_website_content_hash : Hash du contenu
			$rqt = "create table if not exists docwatch_datasource_monitoring_website(
				datasource_monitoring_website_num_datasource int(10) unsigned not null default 0 primary key,
				datasource_monitoring_website_upload_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				datasource_monitoring_website_content mediumtext not null,
				datasource_monitoring_website_content_hash varchar(255) not null default '')";
			echo traite_rqt($rqt,"create table docwatch_datasource_monitoring_website");
				
			// TS & AP - Création d'une table de liaison entre SESSID et token (pour les SSO)
			// sessions_tokens_SESSID : SESSID
			// sessions_tokens_token : Token
			// sessions_tokens_type : Information sur l'utilisation du token
			$rqt = "create table if not exists sessions_tokens(
				sessions_tokens_SESSID varchar(12) not null default '',
				sessions_tokens_token varchar(255) NOT NULL default '',
				sessions_tokens_type varchar(255) NOT NULL default '',
				primary key (sessions_tokens_SESSID, sessions_tokens_type),
				index i_st_sessions_tokens_type(sessions_tokens_type),
				index i_st_sessions_tokens_token(sessions_tokens_token))";
					echo traite_rqt($rqt,"create table sessions_tokens");
								
			// NG - Création de la table des facettes de notices externes
			$rqt = "CREATE TABLE if not exists facettes_external (
				id_facette int unsigned auto_increment,
				facette_name varchar(255) not null default '',
				facette_critere int(5) not null default 0,
				facette_ss_critere int(5) not null default 0,
				facette_nb_result int(2) not null default 0,
				facette_visible_gestion tinyint(1) not null default 0,
				facette_visible tinyint(1) not null default 0,
				facette_type_sort int(1) not null default 0,
				facette_order_sort int(1) not null default 0,
				facette_order int not null default 1,
				facette_limit_plus int not null default 0,
				facette_opac_views_num text NOT NULL,
				primary key (id_facette))";
			echo traite_rqt($rqt,"CREATE TABLE facettes_external");
		
			// NG - Ajout de la visibilité des facettes en Gestion
			$rqt = "ALTER TABLE facettes add facette_visible_gestion tinyint(1) not null default 0 AFTER facette_nb_result";
			echo traite_rqt($rqt,"ALTER TABLE facettes add facette_visible_gestion ");

			//AP & TS - Ajout de la notion de fermeture des statuts de demandes de numérisation
			$rqt = "ALTER TABLE scan_request_status ADD scan_request_status_is_closed int(1) NOT NULL default 0";
			echo traite_rqt($rqt,"ALTER TABLE scan_request_status ADD scan_request_status_is_closed");

			// NG - Suppression de la table tu_oeuvres_others_links
			$rqt ="drop table if exists tu_oeuvres_others_links";
			echo traite_rqt($rqt,"drop table tu_oeuvres_others_links");

			// NG - Suppression de la table tu_oeuvres_expressions
			$rqt ="drop table if exists tu_oeuvres_expressions";
			echo traite_rqt($rqt,"drop table tu_oeuvres_expressions");

			// NG - Ajoute index scan_request_status_workflow
			$rqt ="alter table scan_request_status_workflow add primary key (scan_request_status_workflow_from_num, scan_request_status_workflow_to_num)";
			echo traite_rqt($rqt,"alter table scan_request_status_workflow add primary key");

			// NG - Ajoute index tu_oeuvres_links
			$rqt ="alter table tu_oeuvres_links add primary key (oeuvre_link_from, oeuvre_link_to, oeuvre_link_type, oeuvre_link_expression, oeuvre_link_other_link)";
			echo traite_rqt($rqt,"alter table tu_oeuvres_links add primary key");

			//DG - Parametre du template d'affichage des notices pour le comparateur.
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='compare_notice_active' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','compare_notice_active',1,'Activer le comparateur de notices','',0)";
				echo traite_rqt($rqt,"insert pmb_compare_notice_active into parametres");
			}

			// NG - Ajout d'un paramètre mémorisant le décompte préféré à un type de demande
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'acquisition' and sstype_param='request_type_pref_account' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'acquisition', 'request_type_pref_account', '', '1', 'Mémorise le décompte préféré à un type de demande') ";
				echo traite_rqt($rqt,"INSERT acquisition_request_type_pref_account INTO parametres") ;
			}
											
			//Alexandre - Préremplissage du prix des exemplaires avec le prix indiqué en notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='prefill_prix' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'pmb', 'prefill_prix', '0', 'Préremplissage du prix des exemplaires avec le prix indiqué en notice ? \n 0 : Non \n 1 : Oui', '',0) ";
				echo traite_rqt($rqt, "insert pmb_prefill_prix=0 into parametres");
			}

			// NG - Modification des commentaires des paramètres de géolocalisation
			$rqt = "UPDATE parametres SET comment_param = 'Activation de la géolocalisation:\n 0 : Non \n 1 : Pour toutes les cartes \n 2 : Seulement pour les cartes de notices \n 3 : Seulement pour les cartes de localisation des exemplaires'
					WHERE type_param= 'opac' and sstype_param='map_activate' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_activate");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en visualisation de notice. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'opac' and sstype_param='map_size_notice_view' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_size_notice_view");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en saisie de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'opac' and sstype_param='map_size_search_edition' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_size_search_edition");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en résultat de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'opac' and sstype_param='map_size_search_result' ";
			echo traite_rqt($rqt,"UPDATE comment_param of opac_map_size_search_result");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en édition de notice. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_notice_edition' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_notice_edition");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en visualisation de notice. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_notice_view' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_notice_view");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en saisie de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_search_edition' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_search_edition");
				
			$rqt = "UPDATE parametres SET comment_param = 'Taille de la carte en résultat de recherche. En pixels ou en pourcentage. Exemple : 100%*480px'
					WHERE type_param= 'pmb' and sstype_param='map_size_search_result' ";
			echo traite_rqt($rqt,"UPDATE comment_param of pmb_map_size_search_result");
				
			// TS & NG - Définition de la couleur d'une emprise de sur-localisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_holds_sur_location_color' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'map_holds_sur_location_color', '#D60F0F', 'Couleur des emprises associées à des sur-localisations','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_holds_sur_location_color into parametres");
			}
				
			// TS & NG - Ajout paramètre de la taille de la carte des localisations dans les facettes
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_location_facette' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'map_size_location_facette', '100%*200px', 'Taille de la carte des localisations dans les facettes. En pixels ou en pourcentage. Exemple : 100%*200px','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_location_facette into parametres");
			}
				
			// TS & NG - Ajout paramètre de la taille de la carte des localisations dans la page d'accueil de l'Opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_size_location_home_page' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'map_size_location_home_page', '100%*200px', 'Taille de la carte des localisations dans la page d\'accueil de l\'Opac. En pixels ou en pourcentage. Exemple : 100%*480px','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_size_location_home_page");
			}

			// NG - Paramètre d'activation de la localisation d'une demande de numérisation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='scan_request_location_activate' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion) VALUES ('pmb','scan_request_location_activate',0,'Activer la localisation d\'une demande de numérisation','',0)";
				echo traite_rqt($rqt,"insert pmb_scan_request_location_activate into parametres");
			}

			// NG - Ajout de la localisation d'une demande de numérisation
			$rqt = "ALTER TABLE scan_requests ADD scan_request_num_location INT UNSIGNED NOT NULL DEFAULT 0";
			echo traite_rqt($rqt,"ALTER TABLE scan_requests ADD scan_request_num_location");
				
			// JP - Pouvoir cacher les documents numériques dans les options d'impression des paniers à l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_explnum' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
					VALUES (0, 'opac', 'print_explnum', '1', 'Activer la possibilité d\'imprimer les documents numériques.\n 0: non \n 1: oui','h_cart',0)";
				echo traite_rqt($rqt,"insert opac_print_explnum into parametres");
			}

			// AR - Permettre de masquer les premières pages affichées par défaut dans les listes d'autorités (Onglet autorités et popup de sélection)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='allow_authorities_first_page' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'allow_authorities_first_page', '1', 'Active ou non l\'affichage par défaut la première page d\'une liste d\'autorité lorsque l\'on arrive en sélection dans un popup.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_allow_authorities_first_page into parametres");
			}
				
			// NG - Autoriser le prêt d'un exemplaire déjà prêté
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='pret_already_borrowed' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'pret_already_borrowed', '0', 'Autoriser le prêt d\'un exemplaire déjà prêté.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_pret_already_borrowed into parametres");
			}
				
			// NG - Ajout préférences utilisateur en créattion d'une fiche lecteur
			$rqt = "ALTER TABLE users ADD deflt_empr_categ INT UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_empr_statut ";
			echo traite_rqt($rqt, "add deflt_empr_categ in table users");
			$rqt = "ALTER TABLE users ADD deflt_empr_codestat INT UNSIGNED DEFAULT 1 NOT NULL AFTER deflt_empr_categ ";
			echo traite_rqt($rqt, "add deflt_empr_codestat in table users");
											

			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;

			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.23");
		
	//case "v5.23":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			
			// DG/JP - ajout des paramètres d'export pour les notices liées horizontales
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_horizontale' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'exportparam', 'export_horizontale', '0', 'Lien vers les notices liées horizontales', '', '1')";
				echo traite_rqt($rqt,"insert exportparam_export_horizontale='0' into parametres ");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'exportparam' and sstype_param='export_notice_horizontale_link' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'exportparam', 'export_notice_horizontale_link', '0', 'Exporter les notices liées horizontales', '', '1')";
				echo traite_rqt($rqt,"insert exportparam_export_notice_horizontale_link='0' into parametres ");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_horizontale' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'exp_export_horizontale', '0', 'Lien vers les notices liées horizontales', '', '1')";
				echo traite_rqt($rqt,"insert opac_exp_export_horizontale='0' into parametres ");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='exp_export_notice_horizontale_link' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
			  		VALUES (NULL, 'opac', 'exp_export_notice_horizontale_link', '0', 'Exporter les notices liées horizontales', '', '1')";
				echo traite_rqt($rqt,"insert opac_exp_export_notice_horizontale_link='0' into parametres ");
			}
			// DG/JP - mise à jour de la table notices_relations
			if (pmb_mysql_num_rows(pmb_mysql_query("show columns from notices_relations like 'id_notices_relations'"))==0) {
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ FAIRE UN NETTOYAGE DE BASE (APRES ETAPES DE MISE A JOUR) / YOU MUST DO A DATABASE CLEANUP (STEPS AFTER UPDATE) : Admin > Outils > Nettoyage de base</a></b> ") ;
			}			
			// AR - Activer les ontologies génériques et l'onglet sémantique
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'semantic' and sstype_param='active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
								VALUES (0, 'semantic', 'active', '0', 'Module \"Sémantique\" activé.\n 0: non \n 1:Oui','',0)";
				echo traite_rqt($rqt,"insert semantic_active into parametres");
			}

			// NG - Activer le focus sur le champ de recherche a l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='focus_user_query' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'opac', 'focus_user_query', '1', 'Activer le focus sur le champ de recherche.\n 0: non \n 1:Oui','c_recherche',0)";
				echo traite_rqt($rqt,"insert opac_focus_user_query into parametres");
			}
			
			//NG - Parametre pour afficher dans tous les cas la source et la destination en édition de transfert
			if (pmb_mysql_num_rows(pmb_mysql_query("SELECT 1 FROM parametres WHERE type_param= 'transferts' and sstype_param='edition_show_all_colls' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, gestion, comment_param)
					VALUES (0, 'transferts', 'edition_show_all_colls', '0', '1', 'Afficher dans tous les cas la source et la destination en édition de transfert') ";
				echo traite_rqt($rqt,"INSERT transferts_edition_show_all_colls INTO parametres") ;
			}
			
			//JP - Ajout d'index sur la table catégories
			$rqt = "alter table categories drop index i_num_thesaurus";
			echo traite_rqt($rqt,"alter table categories drop index i_num_thesaurus");
			$rqt = "alter table categories add index i_num_thesaurus(num_thesaurus)";
			echo traite_rqt($rqt,"alter table categories add index i_num_thesaurus");

			///JP - Nombre de notices diffusées pour dsi privées
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'dsi' and sstype_param='private_bannette_nb_notices' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'dsi', 'private_bannette_nb_notices', '30', 'Nombre maximum par défaut de notices diffusées dans les bannettes privées.','', 0)";
				echo traite_rqt($rqt,"insert dsi_private_bannette_nb_notices into parametres");
			}
				
			// AP-VT - Paramètre d'activation des graphes côté gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_graph_activate' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'entity_graph_activate', '1', 'Active ou non le graphe des entités PMB.\n 0: Non \n 1: Oui','',0)";
				echo traite_rqt($rqt,"insert pmb_entity_graph_activate into parametres");
			}
				
			// AP-VT - Définition du niveau de récursion affiché par défaut dans le graphe
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='entity_graph_recursion_lvl' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'pmb', 'entity_graph_recursion_lvl', '1', 'Valeur numérique définissant le niveau de profondeur du graphe.','',0)";
				echo traite_rqt($rqt,"insert pmb_entity_graph_recursion_lvl into parametres");
			}
				
			// NG - VT - Création du paramètre d'activation des espaces de contribution en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_area_activate' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
            	VALUES (NULL, 'pmb', 'contribution_area_activate', '0', 'Espace de contribution actif: \r\n0: Non\r\n1: Oui', '', '0')";
				echo traite_rqt($rqt,"insert pmb_contribution_area_activate=0 into parametres ");
			}
			
			// AP & TS - Création d'un paramètre permettant d'activer l'espace de contribution à l'OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='contribution_area_activate' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'contribution_area_activate', '0', 'Espace de contribution actif ? \n0 : Non\n1 : Oui\n','f_modules', 0)";
				echo traite_rqt($rqt,"insert opac_contribution_area_activate=0 into parametres");
			}
				
			// NG - VT Création de la table des espaces de contribution
			// id_area: identifiant de l'espace de contribution
			// area_title: nom de l'espace de contribution
			// area_comment : Commentaire associé à l'espace de contribution
			// area_color : Couleur associée à l'espace de contribution
			// area_order : Ordre associé à l'espace de contribution
			$rqt="create table if not exists contribution_area_areas(
				id_area int unsigned not null auto_increment primary key,
				area_title varchar(255) not null default '',
				area_comment text not null,
				area_color varchar(10) not null default '',
				area_order int(5) not null default 0
			)";
			echo traite_rqt($rqt, "create table contribution_area_areas");
		
			// AR - VT Création de la table des formulaires de contribution
			// id_form: Identifiant du formulaire
			// form_title: Nom du formulaire
			// form_type: type d'entité que représente le formulaire (notice, auteur, categ etc...)
			// form_status : statut du formulaire
			// form_parameters: structure JSON contenant le paramétrage spécifique du formulaire (champs affichés, label choisis etc..)
			$rqt="create table if not exists contribution_area_forms(
				id_form int unsigned not null auto_increment primary key,
				form_title varchar(255) not null default '',
				form_type varchar(255) not null default '',
				form_status int(3) unsigned NOT NULL DEFAULT 1,
				form_parameters blob not null
			)";
			echo traite_rqt($rqt, "create table contribution_area_forms");
			
			//AP & TS - paramétrages des droits d'accès sur les espaces de contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution' "))==0){
			$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_contribution',0,'Gestion des droits d\'accès des emprunteurs aux espaces de contribution\n0 : Non.\n1 : Oui.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_contribution=0 into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_def' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('gestion_acces','empr_contribution_def',0,'Valeur par défaut en modification d\'espaces de contribution pour les droits d\'accès emprunteurs - espaces de contribution \n0 : Recalculer.\n1 : Choisir.','',0)";
				echo traite_rqt($rqt,"insert gestion_acces_empr_contribution_def=0 into parametres");
			}
		
			//AP & TS - Création de la table des status des espaces de contribution
			//contribution_area_status_id : identifiant du statut
			//contribution_area_status_gestion_libelle : libellé du statut en gestion
			//contribution_area_status_opac_libelle : libellé du statut en OPAC
			//contribution_area_status_class_html : classe HTML du statut
			//contribution_area_status_available_for : Définit les types d'entités PMB pour lesquelles un statut est disponible
			$rqt="create table if not exists contribution_area_status(
			contribution_area_status_id int unsigned not null auto_increment primary key,
				contribution_area_status_gestion_libelle varchar(255) not null default '',
				contribution_area_status_opac_libelle varchar(255) not null default '',
				contribution_area_status_class_html varchar(255) not null default '',
				contribution_area_status_available_for text default null
			)";
			echo traite_rqt($rqt, "create table contribution_area_status");
				
			// AP & TS - Statut par défaut pour la contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from contribution_area_status where contribution_area_status_id ='1' "))==0) {
				$rqt = 'INSERT INTO contribution_area_status (contribution_area_status_id, contribution_area_status_gestion_libelle, contribution_area_status_opac_libelle, contribution_area_status_class_html, contribution_area_status_available_for) VALUES (1,"Statut par défaut", "Statut par défaut", "statutnot1","'.addslashes('a:10:{i:0;s:6:"record";i:1;s:6:"author";i:2;s:8:"category";i:3;s:9:"publisher";i:4;s:10:"collection";i:5;s:14:"sub_collection";i:6;s:5:"serie";i:7;s:4:"work";i:8;s:8:"indexint";i:9;s:7:"concept";}').'")';
				echo traite_rqt($rqt,"insert default contribution_area_status");
			}
				
			//NG & TS - Création de la table des equations de recherches pour les sélecteurs de ressource dans les formulaires de contribution
			//contribution_area_equation_id : identifiant de l'equation
			//contribution_area_equation_name : libellé de l'equation
			//contribution_area_equation_type : type de l'equation
			//contribution_area_equation_query : requete de recherche de l'equation
			//contribution_area_equation_human_query : requete comprehensible
			$rqt="create table if not exists contribution_area_equations(
			contribution_area_equation_id int unsigned not null auto_increment primary key,
				contribution_area_equation_name varchar(255) not null default '',
				contribution_area_equation_type varchar(255) not null default '',
				contribution_area_equation_query text not null,
				contribution_area_equation_human_query text not null
			)";
			echo traite_rqt($rqt, "create table contribution_area_equations");
		
			// AP - Activer les statistiques de fréquentation
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='empr' and sstype_param='visits_statistics_active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
						VALUES (0, 'empr', 'visits_statistics_active', '0', 'Activer les statistiques de fréquentation.\n 0 : Non \n 1 : Oui','',0)";
				echo traite_rqt($rqt,"insert empr_visits_statistics_active=0 into parametres");
			}
		
			// AP - Création d'une table pour stocker les statistiques de fréquentation
			// visits_statistics_date : Date
			// visits_statistics_location : Localisation
			// visits_statistics_type : Type de service
			// visits_statistics_value : Valeur
			$rqt = "CREATE TABLE if not exists visits_statistics (
			visits_statistics_date DATE NOT NULL default '0000-00-00',
			visits_statistics_location SMALLINT(5) UNSIGNED NOT NULL default 0,
					visits_statistics_type VARCHAR(255) NOT NULL default '',
					visits_statistics_value INT UNSIGNED NOT NULL default 0,
					primary key (visits_statistics_date, visits_statistics_location, visits_statistics_type)
			)";
			echo traite_rqt($rqt,"CREATE TABLE visits_statistics ") ;
			
			// AP - Ajout d'une colonne de signature dans la table de documents numériques
						// Attention, chez certain client, cette opération peut prendre beaucoup de temps et mener à un timeout
			if (pmb_mysql_num_rows(pmb_mysql_query("show columns from explnum like 'explnum_signature'")) == 0){
			$info_message = "<font color=\"#FF0000\">ATTENTION</font> si vous avez beaucoup de documents numériques, la prochaine opération peut prendre du temps et nécessiter une nouvelle mise à jour de base !";
				echo "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($info_message) : $info_message)."</font></td><td></td></tr>";
			flush();
				ob_flush();
			
				$rqt = "ALTER TABLE explnum ADD explnum_signature varchar(255) not null default ''";
				echo traite_rqt($rqt,"alter table explnum add explnum_signature");
			}
			
			// TS - VT // Ajout d'un parametre caché contenant le nom d'utilisateur du webservice associé à la contribution OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_ws_username' "))==0){
						$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_ws_username','','Paramètre caché contenant le nom d\'utilisateur du ws','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_ws_username='' into parametres");
			}
		
			// TS - VT // Ajout d'un parametre caché contenant le mot de passe de l'utilisateur du ws associé à la contribution OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_ws_password' "))==0){
						$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_ws_password','','Paramètre caché contenant le mot de passe de l\'utilisateur du ws','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_ws_password='' into parametres");
			}
		
			// TS - VT // Ajout d'un parametre caché contenant l'url du webservice associé à la contribution OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_ws_url' "))==0){
						$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_ws_url','','Paramètre caché contenant l\'url du ws','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_ws_url='' into parametres");
			}
		
			// AP - Paramètre de contrôle des doublons de documents numériques
						if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='explnum_controle_doublons' "))==0){
						$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'pmb', 'explnum_controle_doublons', '0', 'Contrôle sur les doublons de documents numérique.\n 0 : Aucun dédoublonnage\n 1 : Dédoublonnage sur le contenu des documents numériques','',0)";
				echo traite_rqt($rqt,"insert pmb_explnum_controle_doublons=0 into parametres");
			}
		
			// VT & DG - Paramètre de transfert de panier anonyme
						// 0 : Non
						// 1 : Sur demande
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='integrate_anonymous_cart' "))==0){
						$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'integrate_anonymous_cart', '1', 'Proposer le transfert des éléments du panier lors de l\'authentification.\n 0 : Non\n 1 : Sur demande','h_cart',0)";
				echo traite_rqt($rqt,"insert opac_integrate_anonymous_cart=1 into parametres");
			}
		
			// DG - VT Activer l'autocompletion lecteur sur l'impression de recherche à l'opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param='opac' and sstype_param='print_email_autocomplete' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
	  			VALUES (0, 'opac', 'print_email_autocomplete', '0', 'Autoriser la complétion de l\'adresse mail sur le formulaire d\'impression de recherche\n 0 : Non \n 1 : Seulement pour les lecteurs connectés \n 2 : Pour tous les lecteurs', 'a_general', '0')";
				echo traite_rqt($rqt,"insert opac_print_email_autocomplete='0' into parametres ");
			}
				
			///JP - Affichage simplifié du panier OPAC
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='simplified_cart' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
					VALUES ( 'opac', 'simplified_cart', '0', 'Affichage simplifié du panier.\n 0 : non \n 1 : oui','h_cart', 0)";
				echo traite_rqt($rqt,"insert opac_simplified_cart=0 into parametres");
			}
			
			//JP - Prix de l'exemplaire dans le paramétrage de l'abonnement
			$rqt = "ALTER TABLE abts_abts ADD prix varchar(255) NOT NULL DEFAULT ''";
			echo traite_rqt($rqt,"ALTER TABLE abts_abts ADD prix");
			
			//JP - Envoi d'email dans les demandes de numérisation (paramètre invisible)
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='scan_request_send_mail_status' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'scan_request_send_mail_status', '', 'Envoi d\'email au destinataire sur passage de la demande à ces statuts','a_general',1)";
				echo traite_rqt($rqt,"insert opac_scan_request_send_mail_status='' into parametres");
			}			
			
			//JP - Bloquer les prêts dès qu'un lecteur est en retard
			$rqt = "update parametres set comment_param='Délai à partir duquel le retard est pris en compte pour le blocage\n 0 : dès qu\'un prêt est en retard\n N : au bout de N jours de retard' where type_param= 'pmb' and sstype_param='blocage_delai' ";
			echo traite_rqt($rqt,"update blocage_delai into parametres");
			$rqt = "update parametres set comment_param='Nombre maximum de jours bloqués\n 0 : pas de limite\n N : maxi N\n -1 : blocage levé dès qu\'il n\'y a plus de retard' where type_param= 'pmb' and sstype_param='blocage_max' ";
			echo traite_rqt($rqt,"update blocage_max into parametres");
			
			//JP - Ajout d'index sur la table es_searchcache
			$rqt = "alter table es_searchcache drop index i_es_searchcache_date";
			echo traite_rqt($rqt,"alter table es_searchcache drop index i_es_searchcache_date");
			$rqt = "alter table es_searchcache add index i_es_searchcache_date(es_searchcache_date)";
			echo traite_rqt($rqt,"alter table es_searchcache add index i_es_searchcache_date");
				
			//JP - Lien vers la documentation des fonctions de parse HTML dans les paramètres
			$rqt = "update parametres set comment_param=CONCAT(comment_param,'\n<a href=\'".$base_path."/includes/interpreter/doc?group=inhtml\' target=\'_blank\'>Consulter la liste des fonctions disponibles</a>') where type_param= 'opac' and sstype_param='parse_html' ";
			echo traite_rqt($rqt,"update parse_html into parametres");
			
			//MB - Ajout d'index sur la table es_cache_blob
			$req="SHOW INDEX FROM es_cache_blob WHERE key_name LIKE 'i_es_cache_expirationdate'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "alter table es_cache_blob add index i_es_cache_expirationdate(es_cache_expirationdate)";
				echo traite_rqt($rqt,"alter table es_cache_blob add index i_es_cache_expirationdate");
			}
			
			//JP - filtres de relances personnalisables
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='filter_relance_rows' "))==0){
				$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param,comment_param, section_param, gestion) VALUES ('empr','filter_relance_rows', 'g,cs', 'Critères de filtrage ajoutés aux critères existants pour les relances à faire, saisir les critères séparés par des virgules.\nLes critères disponibles correspondent à l\'attribut value du fichier substituable empr_list.xml', '','0')";
				echo traite_rqt($rqt,"insert empr_filter_relance_rows into parametres");
			}
				
			//TS & DG - Liste des pages FRBR
			// id_page : Identifiant de la page
			// page_name : Libellé de la page
			// page_comment : Description de la page
			// page_entity : Quelle page d'entité ?
			// page_parameters : Paramètres
			// page_opac_views : Vues OPAC
			$rqt="create table if not exists frbr_pages (
	            id_page int unsigned not null auto_increment primary key,
	            page_name varchar(255) not null default '',
				page_comment text not null,
	            page_entity varchar(255) not null default '',
				page_parameters text not null,
			    page_opac_views varchar(255) not null default ''
			)";
			echo traite_rqt($rqt,"create table frbr_pages");
				
			//TS & DG - Liste des données FRBR
			// id_datanode : Identifiant du noeud
			// datanode_name : Libellé
			// datanode_comment : Description
			// datanode_object : Classe PHP
			// datanode_num_page : Page associée
			// datanode_num_parent : Noeud de données parent
			$rqt="create table if not exists frbr_datanodes (
	            id_datanode int unsigned not null auto_increment primary key,
	            datanode_name varchar(255) not null default '',
				datanode_comment text not null,
				datanode_object varchar(255) not null default '',
				datanode_num_page int unsigned not null default 0,
				datanode_num_parent int unsigned not null default 0
        	)";
			echo traite_rqt($rqt,"create table frbr_datanodes");
				
			//TS & DG - Liste des données FRBR
			// id_datanode_content : Identifiant d'un élément du noeud
			// datanode_content_type : Type (datasource, filter, etc..)
			// datanode_content_object : Classe PHP
			// datanode_content_num_datanode : Identifiant du noeud associé
			// datanode_content_data : Données
			$rqt="create table if not exists frbr_datanodes_content (
	            id_datanode_content int unsigned not null auto_increment primary key,
	            datanode_content_type varchar(255) not null default '',
				datanode_content_object varchar(255) not null default '',
				datanode_content_num_datanode int unsigned not null default 0,
				datanode_content_data text not null
        	)";
			echo traite_rqt($rqt,"create table frbr_datanodes_content");
		
			//TS & DG - Liste des cadres FRBR
			// id_cadre : Identifiant du cadre
			// cadre_name : Libellé
			// cadre_comment : Description
			// cadre_object : Classe PHP
			// cadre_css_class : Classe CSS
			// cadre_num_datanode : Noeud de données associé
			$rqt="create table if not exists frbr_cadres (
	            id_cadre int unsigned not null auto_increment primary key,
	            cadre_name varchar(255) not null default '',
				cadre_comment text not null,
				cadre_object varchar(255) not null default '',
				cadre_css_class VARCHAR(255) NOT NULL DEFAULT '',
				cadre_num_datanode int unsigned not null default 0
        	)";
			echo traite_rqt($rqt,"create table frbr_cadres");
							
			//TS & DG - Liste des éléments de cadres FRBR
			// id_cadre_content : Identifiant d'un élément du cadre
			// cadre_content_type : Type (view, filter, etc..)
			// cadre_content_object : Classe PHP
			// cadre_content_num_cadre : Identifiant du cadre associé
			// cadre_content_data : Données
			$rqt="create table if not exists frbr_cadres_content (
			id_cadre_content int unsigned not null auto_increment primary key,
	            cadre_content_type varchar(255) not null default '',
				cadre_content_object varchar(255) not null default '',
				cadre_content_num_cadre int unsigned not null default 0,
				cadre_content_data text not null default ''
        	)";
			echo traite_rqt($rqt,"create table frbr_cadres_content");
			
			//DG - Gestion des entités pour le FRBR
			$rqt="create table if not exists frbr_managed_entities (
			managed_entity_name varchar(255) not null default '',
			managed_entity_box text not null,
			primary key (managed_entity_name))";
			echo traite_rqt($rqt, "create table if not exists frbr_managed_entities");
			
			//DG - Vignettes sur les autorités
			$rqt = "ALTER TABLE authorities ADD thumbnail_url MEDIUMBLOB NOT NULL" ;
			echo traite_rqt($rqt,"ALTER TABLE authorities ADD thumbnail_url ");
				
			//DG - Répertoire d'upload pour les vignettes sur les autorités
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authority_img_folder_id' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion) ";
				$rqt.= "VALUES (NULL, 'pmb', 'authority_img_folder_id', '0', 'Identifiant du répertoire d\'upload des vignettes d\'autorités', '', '0')" ;
				echo traite_rqt($rqt,"insert pmb_authority_img_folder_id into parametres") ;
			}
							
			//DG - Définition de la taille maximum des vignettes sur les autorités
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='authority_img_pics_max_size' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param) VALUES (0, 'pmb', 'authority_img_pics_max_size', '150', 'Taille maximale des vignettes uploadées dans les autorités, en largeur ou en hauteur')";
				echo traite_rqt($rqt,"insert pmb_authority_img_pics_max_size='150' into parametres");
			}
			
			//NG - Update fonction d'auteur 'danseur' du code 274 en 275, suite au changement de function.xml
			$rqt = "UPDATE responsability set responsability_fonction='275' where responsability_fonction='274' ";
			echo traite_rqt($rqt,"UPDATE responsability set responsability_fonction ");
			$rqt = "UPDATE responsability_tu set responsability_tu_fonction='275' where responsability_tu_fonction='274' ";
			echo traite_rqt($rqt,"UPDATE responsability_tu set responsability_tu_fonction ");
							
			//NG - Ajout du champ commentaire de gestion dans les étagères
			$rqt = "ALTER TABLE etagere ADD comment_gestion TEXT NOT NULL " ;
			echo traite_rqt($rqt,"ALTER TABLE etagere ADD comment_gestion ");

			//DG - Ordre sur les pages FRBR
			$rqt = "alter table frbr_pages add page_order int not null default 1";
			echo traite_rqt($rqt,"alter table frbr_pages add page_order");
									
			//TS & DG - Liste des données FRBR
			// id_page_content : Identifiant d'un élément du noeud
			// page_content_type : Type (backbone, etc..)
			// page_content_object : Classe PHP
			// page_content_num_page : Identifiant du noeud associé
			// page_content_data : Données
			$rqt="create table if not exists frbr_pages_content (
				id_page_content int unsigned not null auto_increment primary key,
	            page_content_type varchar(255) not null default '',
				page_content_object varchar(255) not null default '',
				page_content_num_page int unsigned not null default 0,
				page_content_data text not null
        	)";
			echo traite_rqt($rqt,"create table frbr_pages_content");
		
			//TS & DG - Placement des cadres FRBR
			// place_num_page : Identifiant de la page associée
			// place_num_cadre : Identifiant du cadre associé
			// place_cadre_type : Type de cadre (natif, dynamique)
			// place_visibility : Visibilité du cadre
			// place_order : Ordre
			$rqt="create table if not exists frbr_place (
				place_num_page int unsigned not null default 0,
				place_num_cadre int unsigned not null default 0,
				place_cadre_type varchar(255) not null default '',
	            place_visibility int(1) not null default 0,
				place_order int(10) UNSIGNED NOT NULL default 0,
				PRIMARY KEY(place_num_page,place_num_cadre, place_cadre_type)
        	)";
			echo traite_rqt($rqt,"create table frbr_place");
			
			//TS - Numéro de page sur les cadres (nécessaire pour les  cadres liés uniquement à la page)
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_num_page int(10) UNSIGNED NOT NULL default 0 " ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_num_page");
		
			//DG - Paniers d'autorités
			$rqt = "CREATE TABLE IF NOT EXISTS authorities_caddie (
			      idcaddie int(8) unsigned NOT NULL AUTO_INCREMENT,
				  name varchar(255) NOT NULL DEFAULT '',
				  type varchar(20) NOT NULL DEFAULT '',
				  comment varchar(255) NOT NULL DEFAULT '',
				  autorisations mediumtext,
				  caddie_classement varchar(255) NOT NULL DEFAULT '',
				  acces_rapide int(11) NOT NULL DEFAULT '0',
				  creation_user_name varchar(255) NOT NULL DEFAULT '',
				  creation_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (idcaddie),
				  KEY caddie_type (type)) ";
			echo traite_rqt($rqt,"create table authorities_caddie");
		
			//DG - Contenu des paniers d'autorités
			$rqt = "CREATE TABLE IF NOT EXISTS authorities_caddie_content (
				caddie_id int(8) unsigned NOT NULL DEFAULT '0',
				object_id int(10) unsigned NOT NULL DEFAULT '0',
				flag varchar(10) DEFAULT NULL,
				PRIMARY KEY (caddie_id,object_id),
				KEY object_id (object_id)) ";
			echo traite_rqt($rqt,"create table authorities_caddie_content");
		
			//DG - Procédures des paniers d'autorités
			$rqt = "CREATE TABLE IF NOT EXISTS authorities_caddie_procs (
				idproc smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				type varchar(20) NOT NULL DEFAULT 'SELECT',
				name varchar(255) NOT NULL DEFAULT '',
				requete blob NOT NULL,
				comment tinytext NOT NULL,
				autorisations mediumtext,
				parameters text,
				PRIMARY KEY (idproc)) ";
			echo traite_rqt($rqt,"create table authorities_caddie_procs");
		
		
			//NG - Regrouper les fonctions d'auteur en affichage de notice
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='notice_author_functions_grouping' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
							VALUES ('pmb','notice_author_functions_grouping',1,'Regrouper les fonctions d\'auteur en affichage de notice ? \n0 : Non.\n1 : Oui.','',0)";
				echo traite_rqt($rqt,"insert pmb_notice_author_functions_grouping=1 into parametres");
			}
		
			//AP - Champs persos sur les concepts
			$rqt = "create table if not exists skos_custom (
				idchamp int(10) unsigned NOT NULL auto_increment,
				name varchar(255) NOT NULL default '',
				titre varchar(255) default NULL,
				type varchar(10) NOT NULL default 'text',
				datatype varchar(10) NOT NULL default '',
				options text,
				multiple int(11) NOT NULL default 0,
				obligatoire int(11) NOT NULL default 0,
				ordre int(11) default NULL,
				search INT(1) unsigned NOT NULL DEFAULT 0,
				export INT(1) unsigned NOT NULL DEFAULT 0,
				exclusion_obligatoire INT(1) unsigned NOT NULL DEFAULT 0,
				pond int not null default 100,
				opac_sort INT NOT NULL DEFAULT 0,
				comment BLOB NOT NULL default '',
				PRIMARY KEY  (idchamp)) ";
			echo traite_rqt($rqt,"create table skos_custom ");
			
			$rqt = "create table if not exists skos_custom_lists (
				skos_custom_champ int(10) unsigned NOT NULL default 0,
				skos_custom_list_value varchar(255) default NULL,
				skos_custom_list_lib varchar(255) default NULL,
				ordre int(11) default NULL,
				KEY skos_custom_champ (skos_custom_champ),
				KEY skos_champ_list_value (skos_custom_champ,skos_custom_list_value)) " ;
			echo traite_rqt($rqt,"create table skos_custom_lists ");
			
			$rqt = "create table if not exists skos_custom_values (
				skos_custom_champ int(10) unsigned NOT NULL default 0,
				skos_custom_origine int(10) unsigned NOT NULL default 0,
				skos_custom_small_text varchar(255) default NULL,
				skos_custom_text text,
				skos_custom_integer int(11) default NULL,
				skos_custom_date date default NULL,
				skos_custom_float float default NULL,
				skos_custom_order int(11) NOT NULL default 0,
				KEY skos_custom_champ (skos_custom_champ),
				KEY i_encv_st (skos_custom_small_text),
				KEY i_encv_t (skos_custom_text(255)),
				KEY i_encv_i (skos_custom_integer),
				KEY i_encv_d (skos_custom_date),
				KEY i_encv_f (skos_custom_float),
				KEY skos_custom_origine (skos_custom_origine)) " ;
			echo traite_rqt($rqt,"create table skos_custom_values ");
			
			//JP - cacher les amendes et frais de relance dans les lettres et mails de retard
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='hide_fine' "))==0){
				$rqt = "INSERT INTO parametres VALUES (0,'mailretard','hide_fine','0','Masquer les amendes et frais de relance dans les lettres et mails de retard :\n 0 : Non\n 1 : Oui','',0)" ;
				echo traite_rqt($rqt,"insert mailretard_hide_fine into parametres") ;
			}
				
			//JP - paramètre pour forcer l'envoi de relance de niveau 2 par lettre si priorite_email = 1
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'mailretard' and sstype_param='priorite_email_2' "))==0){
				$rqt = "INSERT INTO parametres VALUES (0,'mailretard','priorite_email_2','0','Forcer le deuxième niveau de relance par lettre si priorite_email = 1 :\n 0 : Non\n 1 : Oui','',0)" ;
				echo traite_rqt($rqt,"insert mailretard_priorite_email_2 into parametres") ;
			}
				
			//JP - Calculer le retard, l'amende et le blocage sur le calendrier d'ouverture de la localisation de l'utilisateur ou de l'exemplaire
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='utiliser_calendrier_location' "))==0){
				$rqt = "INSERT INTO parametres VALUES (0,'pmb','utiliser_calendrier_location','0','Si le paramètre utiliser_calendrier est à 1, choix de la localisation pour calculer le retard, l\'amende et le blocage :\n 0 : calcul sur le calendrier d\'ouverture de la localisation de l\'utilisateur\n 1 : calcul sur le calendrier d\'ouverture de la localisation de l\'exemplaire','',0)" ;
				echo traite_rqt($rqt,"insert pmb_utiliser_calendrier_location into parametres") ;
			}

			// NG - Ajout d'un parametre caché indiquant l'affichage ou non des sous-formulaires de contribution
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='contribution_opac_show_sub_form' "))==0){
				$rqt = "INSERT INTO parametres (type_param,sstype_param,valeur_param,comment_param,section_param,gestion)
					VALUES ('pmb','contribution_opac_show_sub_form','','Paramètre caché indiquant l\'affichage ou non des sous-formulaires de contribution','',1)";
				echo traite_rqt($rqt,"insert pmb_contribution_opac_show_sub_form='' into parametres");
			}
			
			// TS - cadre visible ou non dans le graphe
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_visible_in_graph tinyint(1) UNSIGNED NOT NULL default 0" ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_visible_in_graph");
				
			// TS - Chemin des jeux de données parent du cadre
			$rqt = "ALTER TABLE frbr_cadres ADD cadre_datanodes_path varchar(255) default NULL " ;
			echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_datanodes_path");
				
			// VT - Ajout de l'identifiant de la methode de stockage sur les ontologies (partie semantique)
			$rqt = "ALTER TABLE ontologies ADD ontology_storage_id INT NOT NULL default 0";
			echo traite_rqt($rqt,"alter table ontologies add ontology_storage_id");
				
			// AP / VT - Création d'une table permettant de stocker les informations liées aux fichiers uploadés pour les ontologies
			$rqt = "CREATE TABLE IF NOT EXISTS onto_files (
						id_onto_file int(10) unsigned NOT NULL auto_increment primary key,
						onto_file_title varchar(255) NOT NULL DEFAULT '' ,
						onto_file_description text NOT NULL,
						onto_file_filename varchar(255) NOT NULL DEFAULT '',
						onto_file_mimetype varchar(100) NOT NULL DEFAULT '',
						onto_file_filesize int(11) NOT NULL DEFAULT '0',
						onto_file_vignette mediumblob NOT NULL,
						onto_file_url text NOT NULL,
						onto_file_path varchar(255)NOT NULL DEFAULT '',
						onto_file_create_date date NOT NULL DEFAULT '0000-00-00',
						onto_file_num_storage int(11) NOT NULL DEFAULT '0',
						onto_file_type_object varchar(255) NOT NULL DEFAULT '',
						onto_file_num_object int(11) NOT NULL DEFAULT '0',
						INDEX i_of_onto_file_title (onto_file_title)
					)";
			echo traite_rqt($rqt,"create table onto_files");
				
			// TS - ajout d'un index sur le code champ et code sous champ de la table authorities_fields_global_index
			$req="SHOW INDEX FROM authorities_fields_global_index WHERE key_name LIKE 'i_code_champ_code_ss_champ'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE authorities_fields_global_index ADD INDEX i_code_champ_code_ss_champ(code_champ,code_ss_champ)";
				echo traite_rqt($rqt,"ALTER TABLE authorities_fields_global_index ADD INDEX i_code_champ_code_ss_champ");
			}
				
			// TS - ajout d'un index sur le num_concept et type_object de la table index_concept
			$req="SHOW INDEX FROM index_concept WHERE key_name LIKE 'i_num_concept_type_object'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE index_concept ADD INDEX i_num_concept_type_object(num_concept,type_object)";
				echo traite_rqt($rqt,"ALTER TABLE index_concept ADD INDEX i_num_concept_type_object");
			}
				
			// TS - ajout d'un index sur le type_object et num_object de la table index_concept
			$req="SHOW INDEX FROM index_concept WHERE key_name LIKE 'i_type_object_num_object'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE index_concept ADD INDEX i_type_object_num_object(type_object,num_object)";
				echo traite_rqt($rqt,"ALTER TABLE index_concept ADD INDEX i_type_object_num_object");
			}
				
			// MB - Nouvelle table pour les paramètres de PMB à ne pas surtout pas mettre dans le cache_apc ni en global (paramètres modifiés fréquemment pour le fonctionnement de PMB)
			$rqt = "CREATE TABLE IF NOT EXISTS parametres_uncached (
					 id_param int(6) unsigned NOT NULL AUTO_INCREMENT,
					 type_param varchar(20) DEFAULT NULL,
					 sstype_param varchar(255) DEFAULT NULL,
					 valeur_param text,
					 comment_param longtext,
					 section_param varchar(255) NOT NULL DEFAULT '',
					 gestion int(1) NOT NULL DEFAULT '0',
					 PRIMARY KEY (id_param),
					 UNIQUE KEY typ_sstyp (type_param,sstype_param)
					)";
			echo traite_rqt($rqt,"create table parametres_uncached");
				
			// MB - Paramètre de verrou pour la gestion des stats
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres_uncached where type_param= 'internal' and sstype_param='emptylogstatopac' "))==0){
				$rqt = "INSERT INTO parametres_uncached (type_param, sstype_param, valeur_param, comment_param,gestion)
					VALUES ('internal', 'emptylogstatopac', '0', 'Paramètre interne, ne pas modifier\r\n =1 si vidage des logs en cours', 0)";
				echo traite_rqt($rqt,"insert internal_emptylogstatopac=0 into parametres_uncached");
			}
				
			// MB - Suppression de l'ancien paramètre déplacé dans la nouvelle table
			if(isset($internal_emptylogstatopac)){
				$rqt = "delete from parametres where type_param= 'internal' and sstype_param='emptylogstatopac' " ;
				echo traite_rqt($rqt,"delete old parameter 'emptylogstatopac' from parametres");
			}
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;

			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.24");
			
	//case "v5.24":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// MB - Paramètre pour définir le répertoire à utiliser pour le cache des images en gestion
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_cache_folder' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
					VALUES (NULL, 'pmb', 'img_cache_folder', '', 'Répertoire de stockage du cache des images')";
				echo traite_rqt($rqt,"insert pmb_img_cache_folder='' into parametres ");
			}
				
			// MB - Paramètre pour définir l'URL du répertoire pour le cache des images en gestion et avoir des URLs en dur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='img_cache_url' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param)
					VALUES (NULL, 'pmb', 'img_cache_url', '', 'URL d\'accès du répertoire du cache des images (img_cache_folder)')";
				echo traite_rqt($rqt,"insert pmb_img_cache_url='' into parametres ");
			}
				
			// MB - Paramètre pour définir le répertoire à utiliser pour le cache des images en opac
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='img_cache_folder' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'opac', 'img_cache_folder', '', 'Répertoire de stockage du cache des images','a_general')";
				echo traite_rqt($rqt,"insert opac_img_cache_folder='' into parametres ");
			}
			// MB - Paramètre pour définir l'URL du répertoire pour le cache des images en opac et avoir des URLs en dur
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='img_cache_url' "))==0){
				$rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'opac', 'img_cache_url', '', 'URL d\'accès du répertoire du cache des images (img_cache_folder)','a_general')";
				echo traite_rqt($rqt,"insert opac_img_cache_url='' into parametres ");
			}
			
			// DG - Index sur le champ oeuvre_link_from de la table tu_oeuvres_links
			$req="SHOW INDEX FROM tu_oeuvres_links WHERE key_name LIKE 'i_oeuvre_link_from'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_from(oeuvre_link_from)";
				echo traite_rqt($rqt,"ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_from");
			}
				
			// DG - Index sur le champ oeuvre_link_to de la table tu_oeuvres_links
			$req="SHOW INDEX FROM tu_oeuvres_links WHERE key_name LIKE 'i_oeuvre_link_to'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_to(oeuvre_link_to)";
				echo traite_rqt($rqt,"ALTER TABLE tu_oeuvres_links ADD INDEX i_oeuvre_link_to");
			}
			
			// DG - Index sur le champ oeuvre_link_to de la table nomenclature_notices_nomenclatures
			$req="SHOW INDEX FROM nomenclature_notices_nomenclatures WHERE key_name LIKE 'i_notice_nomenclature_num_notice'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE nomenclature_notices_nomenclatures ADD INDEX i_notice_nomenclature_num_notice(notice_nomenclature_num_notice)";
				echo traite_rqt($rqt,"ALTER TABLE nomenclature_notices_nomenclatures ADD INDEX i_notice_nomenclature_num_notice");
			}
				
			// DG - Index sur le champ map_echelle_num de la table notices
			$req="SHOW INDEX FROM notices WHERE key_name LIKE 'i_map_echelle_num'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE notices ADD INDEX i_map_echelle_num(map_echelle_num)";
				echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX i_map_echelle_num");
			}
				
			// DG - Index sur le champ map_projection_num de la table notices
			$req="SHOW INDEX FROM notices WHERE key_name LIKE 'i_map_projection_num'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE notices ADD INDEX i_map_projection_num(map_projection_num)";
				echo traite_rqt($rqt,"ALTER TABLE notices ADD INDEX i_map_projection_num");
			}
				
			// DG - Index sur le champ authperso_authority_authperso_num de la table authperso_authorities
			$req="SHOW INDEX FROM authperso_authorities WHERE key_name LIKE 'i_authperso_authority_authperso_num'";
			$res=pmb_mysql_query($req);
			if($res && (pmb_mysql_num_rows($res) == 0)){
				$rqt = "ALTER TABLE authperso_authorities ADD INDEX i_authperso_authority_authperso_num(authperso_authority_authperso_num)";
				echo traite_rqt($rqt,"ALTER TABLE authperso_authorities ADD INDEX i_authperso_authority_authperso_num");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.25");
	
	//case "v5.25":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// NG - Ajout index sur num_word de la table notices_mots_global_index
			$req="SHOW INDEX FROM notices_mots_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table notices_mots_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_num_word");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.26");
	//case "v5.26":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// NG - Ajout index sur num_word de la table cms_editorial_words_global_index
			$req="SHOW INDEX FROM cms_editorial_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_editorial_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table cms_editorial_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur num_word de la table authorities_words_global_index
			$req="SHOW INDEX FROM authorities_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur num_word de la table skos_words_global_index
			$req="SHOW INDEX FROM skos_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table skos_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table skos_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur num_word de la table faq_questions_words_global_index
			$req="SHOW INDEX FROM faq_questions_words_global_index WHERE key_name LIKE 'i_num_word'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table faq_questions_words_global_index add index i_num_word (num_word)";
				echo traite_rqt($rqt,"alter table faq_questions_words_global_index add index i_num_word");
			}
				
			// NG - Ajout index sur responsability_author de la table responsability
			$req="SHOW INDEX FROM responsability WHERE key_name LIKE 'i_responsability_author'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table responsability add index i_responsability_author (responsability_author)";
				echo traite_rqt($rqt,"alter table responsability add index i_responsability_author");
			}
			
			// NG - Ajout index sur shorturl_hash de la table shorturls
			$req="SHOW INDEX FROM shorturls WHERE key_name LIKE 'i_shorturl_hash'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table shorturls add index i_shorturl_hash (shorturl_hash)";
				echo traite_rqt($rqt,"alter table shorturls add index i_shorturl_hash");
			}
			
			// NG - Ajout index sur empr_login de la table empr
			$req="SHOW INDEX FROM empr WHERE key_name LIKE 'i_empr_login'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table empr add index i_empr_login (empr_login)";
				echo traite_rqt($rqt,"alter table empr add index i_empr_login");
			}
			
			// NG - Ajout index sur ouvert, num_location, date_ouverture de la table ouvertures
			$req="SHOW INDEX FROM ouvertures WHERE key_name LIKE 'i_ouvert_num_location_date_ouverture'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table ouvertures add index i_ouvert_num_location_date_ouverture (ouvert, num_location, date_ouverture)";
				echo traite_rqt($rqt,"alter table ouvertures add index i_ouvert_num_location_date_ouverture");
			}
				
			// NG - Ajout index sur etat_transfert, origine de la table transferts
			$req="SHOW INDEX FROM transferts WHERE key_name LIKE 'i_etat_transfert_origine'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transferts add index i_etat_transfert_origine (etat_transfert, origine)";
				echo traite_rqt($rqt,"alter table transferts add index i_etat_transfert_origine");
			}
				
			// NG - Ajout index sur author_type de la table authors
			$req="SHOW INDEX FROM authors WHERE key_name LIKE 'i_author_type'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authors add index i_author_type (author_type)";
				echo traite_rqt($rqt,"alter table authors add index i_author_type");
			}
			
			// NG - Ajout index sur grammar de la table vedette
			$req="SHOW INDEX FROM vedette WHERE key_name LIKE 'i_grammar'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table vedette add index i_grammar (grammar)";
				echo traite_rqt($rqt,"alter table vedette add index i_grammar");
			}
				
			// NG - Suppression index doublon dans la table authorities
			$req="SHOW INDEX FROM authorities WHERE key_name LIKE 'i_object'";
			$res=pmb_mysql_query($req);
			if($res && pmb_mysql_num_rows($res)){
				$req="ALTER TABLE authorities DROP INDEX i_object ";
				$res=pmb_mysql_query($req);
				echo traite_rqt($rqt,"alter table authorities drop index i_object");
			}
			
			// AP - Paramètre pour activer sphinx
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='active' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'active', '0', 'Sphinx activé.\n 0 : Non\n 1 : Oui','')";
				echo traite_rqt($rqt,"insert sphinx_active = 0 into parametres ");
			}
				
			// AP - Paramètre de définition du chemin vers les index sphinx
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='indexes_path' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'indexes_path', '', 'Chemin vers le répertoire de stockage des index sphinx','')";
				echo traite_rqt($rqt,"insert sphinx_indexes_path = '' into parametres ");
			}
				
			// AP - Paramètre de connexion mysql pour sphinx
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'sphinx' and sstype_param='mysql_connect' "))==0){
				$rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param)
					VALUES (NULL, 'sphinx', 'mysql_connect', '127.0.0.1:9306,0', 'Paramètre de connexion mysql au serveur sphinx :\n hote:port,auth,user,pass : mettre 0 ou 1 pour l\'authentification.','')";
				echo traite_rqt($rqt,"insert sphinx_indexes_path = '127.0.0.1:9306,0' into parametres ");
			}
				
			// JP - Ajout index sur num_authority / authority_type de la table authorities_sources
			$req="SHOW INDEX FROM authorities_sources WHERE key_name LIKE 'i_num_authority_authority_type'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_sources add index i_num_authority_authority_type (num_authority, authority_type)";
				echo traite_rqt($rqt,"alter table authorities_sources add index i_num_authority_authority_type");
			}
				
			// JP - Ajout index sur cadre_memo_url de la table cms_cadres
			$req="SHOW INDEX FROM cms_cadres WHERE key_name LIKE 'i_cadre_memo_url'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadres add index i_cadre_memo_url (cadre_memo_url)";
				echo traite_rqt($rqt,"alter table cms_cadres add index i_cadre_memo_url");
			}
				
			// JP - Ajout index sur cadre_object de la table cms_cadres
			$req="SHOW INDEX FROM cms_cadres WHERE key_name LIKE 'i_cadre_object'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadres add index i_cadre_object (cadre_object)";
				echo traite_rqt($rqt,"alter table cms_cadres add index i_cadre_object");
			}
				
			// JP - Ajout index sur cadre_content_num_cadre de la table cms_cadre_content
			$req="SHOW INDEX FROM cms_cadre_content WHERE key_name LIKE 'i_cadre_content_num_cadre'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadre_content add index i_cadre_content_num_cadre (cadre_content_num_cadre)";
				echo traite_rqt($rqt,"alter table cms_cadre_content add index i_cadre_content_num_cadre");
			}
				
			// JP - Ajout index sur cadre_content_num_cadre_content / cadre_content_type cms_cadre_content de la table cms_cadre_content
			$req="SHOW INDEX FROM cms_cadre_content WHERE key_name LIKE 'i_cadre_content_num_cadre_content_cadre_content_type'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_cadre_content add index i_cadre_content_num_cadre_content_cadre_content_type (cadre_content_num_cadre_content, cadre_content_type)";
				echo traite_rqt($rqt,"alter table cms_cadre_content add index i_cadre_content_num_cadre_content_cadre_content_type");
			}
				
			// JP - Ajout index sur document_num_object / document_type_object de la table cms_documents
			$req="SHOW INDEX FROM cms_documents WHERE key_name LIKE 'i_document_num_object_document_type_object'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_documents add index i_document_num_object_document_type_object (document_num_object, document_type_object)";
				echo traite_rqt($rqt,"alter table cms_documents add index i_document_num_object_document_type_object");
			}
				
			// JP - Ajout index sur document_link_num_document de la table cms_documents_links
			$req="SHOW INDEX FROM cms_documents_links WHERE key_name LIKE 'i_document_link_num_document'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table cms_documents_links add index i_document_link_num_document (document_link_num_document)";
				echo traite_rqt($rqt,"alter table cms_documents_links add index i_document_link_num_document");
			}
				
			// JP - Ajout index sur groupe / method / available de la table es_methods
			$req="SHOW INDEX FROM es_methods WHERE key_name LIKE 'i_groupe_method_available'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table es_methods add index i_groupe_method_available (groupe(50), method(50), available)";
				echo traite_rqt($rqt,"alter table es_methods add index i_groupe_method_available");
			}
				
			// JP - Ajout index sur facette_visible de la table facettes
			$req="SHOW INDEX FROM facettes WHERE key_name LIKE 'i_facette_visible'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table facettes add index i_facette_visible (facette_visible)";
				echo traite_rqt($rqt,"alter table facettes add index i_facette_visible");
			}
				
			// JP - Ajout index sur origine de la table import_marc
			$req="SHOW INDEX FROM import_marc WHERE key_name LIKE 'i_origine'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table import_marc add index i_origine (origine)";
				echo traite_rqt($rqt,"alter table import_marc add index i_origine");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.27");
	//case "v5.27":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// JP - Ajout index sur id_notice de la table notices_mots_global_index
			$req="SHOW INDEX FROM notices_mots_global_index WHERE key_name LIKE 'i_id_notice'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table notices_mots_global_index add index i_id_notice (id_notice)";
				echo traite_rqt($rqt,"alter table notices_mots_global_index add index i_id_notice");
			}
				
			// +-------------------------------------------------+
			echo "</table>";
			$rqt = "update parametres set valeur_param='".$action."' where type_param='pmb' and sstype_param='bdd_version' " ;
			$res = pmb_mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;
			//echo form_relance ("v5.27");

		
	//case "v5.28":
			echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
			// +-------------------------------------------------+
			// JP - Ajout index sur resa_idempr de la table resa
			$req="SHOW INDEX FROM resa WHERE key_name LIKE 'i_resa_idempr'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table resa add index i_resa_idempr (resa_idempr)";
				echo traite_rqt($rqt,"alter table resa add index i_resa_idempr");
			}
				
			// JP - Ajout index sur num_suggestion de la table suggestions_origine
			$req="SHOW INDEX FROM suggestions_origine WHERE key_name LIKE 'i_num_suggestion'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table suggestions_origine add index i_num_suggestion (num_suggestion)";
				echo traite_rqt($rqt,"alter table suggestions_origine add index i_num_suggestion");
			}
				
			// JP - Ajout index sur resa_trans de la table transferts_demande
			$req="SHOW INDEX FROM transferts_demande WHERE key_name LIKE 'i_resa_trans'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transferts_demande add index i_resa_trans (resa_trans)";
				echo traite_rqt($rqt,"alter table transferts_demande add index i_resa_trans");
			}
				
			// JP - Ajout index sur realisee de la table transactions
			$req="SHOW INDEX FROM transactions WHERE key_name LIKE 'i_realisee'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transactions add index i_realisee (realisee)";
				echo traite_rqt($rqt,"alter table transactions add index i_realisee");
			}
				
			// JP - Ajout index sur compte_id de la table transactions
			$req="SHOW INDEX FROM transactions WHERE key_name LIKE 'i_compte_id'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table transactions add index i_compte_id (compte_id)";
				echo traite_rqt($rqt,"alter table transactions add index i_compte_id");
			}
			
			// DG - Ajout index sur id_notice de la table notices_fields_global_index
			$req="SHOW INDEX FROM notices_fields_global_index WHERE key_name LIKE 'i_id_notice'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table notices_fields_global_index add index i_id_notice (id_notice)";
				echo traite_rqt($rqt,"alter table notices_fields_global_index add index i_id_notice");
			}
			
			// DG - Ajout index sur id_authority de la table authorities_words_global_index
			$req="SHOW INDEX FROM authorities_words_global_index WHERE key_name LIKE 'i_id_authority'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_words_global_index add index i_id_authority (id_authority)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add index i_id_authority");
			}
				
			// DG - Ajout index sur id_authority de la table authorities_fields_global_index
			$req="SHOW INDEX FROM authorities_fields_global_index WHERE key_name LIKE 'i_id_authority'";
			$res=pmb_mysql_query($req);
			if($res && !pmb_mysql_num_rows($res)){
				$rqt = "alter table authorities_fields_global_index add index i_id_authority (id_authority)";
				echo traite_rqt($rqt,"alter table authorities_fields_global_index add index i_id_authority");
			}
						
			// VT & AP - Nouvelle table pour les régimes de licence de documents numériques
					// id_explnum_licence : Identifiant
					// explnum_licence_label : Libellé
					// explnum_licence_uri : URI
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence (
						id_explnum_licence int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_label varchar(255) DEFAULT '',
						explnum_licence_uri varchar(255) DEFAULT ''
					)";
			echo traite_rqt($rqt,"create table explnum_licence");
		
			// VT & AP - Nouvelle table pour les profils de régimes de licence de documents numériques
					// id_explnum_licence_profile : Identifiant
					// explnum_licence_profile_explnum_licence_num : Identifiant du régime de licence
					// explnum_licence_profile_label : Libellé
					// explnum_licence_profile_uri : URI
					// explnum_licence_profile_logo_url : URL du logo
					// explnum_licence_profile_explanation : Texte explicatif
					// explnum_licence_profile_quotation_rights : Droit de citation du profil
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profiles (
						id_explnum_licence_profile int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_profile_explnum_licence_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_profile_label varchar(255) DEFAULT '',
						explnum_licence_profile_uri varchar(255) DEFAULT '',
						explnum_licence_profile_logo_url varchar(255) DEFAULT '',
						explnum_licence_profile_explanation text,
						explnum_licence_profile_quotation_rights text,
						INDEX i_elp_explnum_licence_num (explnum_licence_profile_explnum_licence_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profiles");
		
			// VT & AP - Nouvelle table pour les droits de régimes de licence de documents numériques
					// id_explnum_licence_right : Identifiant
					// explnum_licence_profile_explnum_licence_num : Identifiant du régime de licence
					// explnum_licence_right_label : Libellé
					// explnum_licence_right_type : Type de droit (Autorisation / Interdiction)
					// explnum_licence_right_logo_url : URL du logo
					// explnum_licence_right_explanation : Texte explicatif
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_rights (
						id_explnum_licence_right int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
						explnum_licence_right_explnum_licence_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_right_label varchar(255) DEFAULT '',
						explnum_licence_right_type int(2) DEFAULT 0,
						explnum_licence_right_logo_url varchar(255) DEFAULT '',
						explnum_licence_right_explanation text,
						INDEX i_elr_explnum_licence_num (explnum_licence_right_explnum_licence_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_rights");
			
			// VT & AP - Nouvelle table pour les liens droits / profils
					// explnum_licence_right_num : Identifiant du droit
					// explnum_licence_profile_num : Identifiant du lien
					$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profile_rights (
						explnum_licence_profile_num INT not null DEFAULT 0,
						explnum_licence_right_num INT not null DEFAULT 0,
						PRIMARY KEY (explnum_licence_profile_num, explnum_licence_right_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profile_rights");

			// AP & VT - Nouvelle table associant un document numérique à un régime de licence
			// explnum_licence_explnums_licence_num : Identifiant du régime de licence
			// explnum_licence_explnums_explnum_num : Identifiant du document numérique
			$rqt = "CREATE TABLE IF NOT EXISTS explnum_licence_profile_explnums (
						explnum_licence_profile_explnums_explnum_num int(10) unsigned NOT NULL DEFAULT 0,
						explnum_licence_profile_explnums_profile_num int(10) unsigned NOT NULL DEFAULT 0,
						PRIMARY KEY (explnum_licence_profile_explnums_explnum_num, explnum_licence_profile_explnums_profile_num),
						INDEX i_elpe_explnum_profile_num (explnum_licence_profile_explnums_profile_num)
					)";
			echo traite_rqt($rqt,"create table explnum_licence_profile_explnums");
			
			// DG - Modification de la clé primaire de la table authorities_words_global_index
			$query = "SHOW KEYS FROM authorities_words_global_index WHERE Key_name = 'PRIMARY'";
			$result = pmb_mysql_query($query);
			$primary_fields = array('id_authority','code_champ','code_ss_champ','num_word','position','field_position');
			$flag = false;
			while($row = pmb_mysql_fetch_object($result)) {
				if(!in_array($row->Column_name, $primary_fields)) {
					$flag = true;
				}
			}
			if(!$flag && pmb_mysql_num_rows($result) != 6) {
				$flag = true;
			}
			if($flag) {
				$rqt ="alter table authorities_words_global_index drop primary key";
				echo traite_rqt($rqt,"alter table authorities_words_global_index drop primary key");
				$rqt ="alter table authorities_words_global_index add primary key (id_authority,code_champ,code_ss_champ,num_word,position,field_position)";
				echo traite_rqt($rqt,"alter table authorities_words_global_index add primary key");
			}
			
			// NG - Zone d'affichage par défaut de la carte
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='map_bounding_box' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'pmb', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par défaut de la carte. Coordonnées d\'un polygone fermé, en degrés décimaux','map', 0)";
				echo traite_rqt($rqt,"insert pmb_map_bounding_box into parametres");
			}
			if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='map_bounding_box' "))==0){
				$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par défaut de la carte. Coordonnées d\'un polygone fermé, en degrés décimaux','map', 0)";
				echo traite_rqt($rqt,"insert opac_map_bounding_box into parametres");
			}


			$rqt = "update parametres set valeur_param='0' where type_param='pmb' and sstype_param='bdd_subversion' " ;
			echo traite_rqt($rqt,"update pmb_bdd_subversion=0 into parametres");
			$pmb_bdd_subversion=0;
			
			if ($pmb_subversion_database_as_it_shouldbe!=$pmb_bdd_subversion) {
				// Info de déconnexion pour passer le add-on
				$rqt = " select 1 " ;
				echo traite_rqt($rqt,"<b><a href='".$base_path."/logout.php' target=_blank>VOUS DEVEZ VOUS DECONNECTER ET VOUS RECONNECTER POUR TERMINER LA MISE A JOUR  / YOU MUST DISCONNECT AND RECONNECT YOU TO COMPLETE UPDATE</a></b> ") ;
			}
			
			
			// +-------------------------------------------------+
			echo "</table>";
			
	//---------------------------------------------------------Se deshabilita la edicion de los formularios--------------------------------
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_authorities_editables' and valeur_param='1'";
			$res = mysql_query($rqt, $dbh);
				
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_editables' and valeur_param='1'";
			$res = mysql_query($rqt, $dbh);
				
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_expl_editables' and valeur_param='1'";
			$res = mysql_query($rqt, $dbh);
				
			$rqt="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_explnum_editables' and valeur_param='1'";
			$res = mysql_query($rqt, $dbh);
//-------------------------------------------------------------------------------------------------------------------------------------------------------			
			
			$rqt = "update parametres set valeur_param='v5.28' where type_param='pmb' and sstype_param='bdd_version' " ;

		//----------FIN LLIUREX 06/03/2018-------	
			
			$res = mysql_query($rqt, $dbh) ;
			echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
			$action=$action+$increment;

			// // Parcheamos la base de datos
			require("$base_path/includes/db_param.inc.php");
			$comando= "cat ".$base_path."/admin/misc/mods_vLlxNemo.sql | mysql -u ". USER_NAME ." --password=". USER_PASS ." ". DATA_BASE;
			if (system($comando, $salida)==0){
			 	echo "$msg[db_patched]";
			}
			//-----------------LLIUREX 20/03/2018-----------
			$comando= "cat ".$base_path."/admin/misc/mods_codification.sql | mysql -u ". USER_NAME ." --password=". USER_PASS ." ". DATA_BASE;
			if (system($comando, $salida)==0){
			 	echo "$msg[db_patched]";
			}
			//----------------FIN LLIUREX 20/03/2018----------------

			echo "<SCRIPT>alert(\"".$msg[actualizacion_ok]."\");</SCRIPT>";
			//echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
			break;

		default:
			include("$include_path/messages/help/$lang/alter.txt");
			break;
		}