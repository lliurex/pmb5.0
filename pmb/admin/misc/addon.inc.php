<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: addon.inc.php,v 1.5.8.29 2018-02-13 15:04:03 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

function traite_rqt($requete="", $message="") {

	global $dbh,$charset;
	$retour="";
	if($charset == "utf-8"){
		$requete=utf8_encode($requete);
	}
	$res = pmb_mysql_query($requete, $dbh) ; 
	$erreur_no = pmb_mysql_errno();
	if (!$erreur_no) {
		$retour = "Successful";
	} else {
		switch ($erreur_no) {
			case "1060":
				$retour = "Field already exists, no problem.";
				break;
			case "1061":
				$retour = "Key already exists, no problem.";
				break;
			case "1091":
				$retour = "Object already deleted, no problem.";
				break;
			default:
				$retour = "<font color=\"#FF0000\">Error may be fatal : <i>".pmb_mysql_error()."<i></font>";
				break;
			}
	}		
	return "<tr><td><font size='1'>".($charset == "utf-8" ? utf8_encode($message) : $message)."</font></td><td><font size='1'>".$retour."</font></td></tr>";
}
echo "<table>";

/******************** AJOUTER ICI LES MODIFICATIONS *******************************/

switch ($pmb_bdd_subversion) {
	case '0' :
		//JP - Impression tickets de prêt via raspberry pi
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_name' "))){
			$rqt = "update parametres set comment_param=CONCAT(comment_param,'\n\nSi l\'imprimante est connectée à un Raspberry Pi, indiquer l\'ip et le port\nExemple : raspberry@192.168.0.82:3000') where type_param='pmb' and sstype_param='printer_name' " ;
			echo traite_rqt($rqt,"update parameters pmb_printer_name");
		}
	case '1' :
		// JP - Rectification index sur index_author / author_type de la table authors
		$rqt = "alter table authors drop index i_index_author_author_type";
		echo traite_rqt($rqt,"alter table authors drop index i_index_author_author_type");
		$rqt = "alter table authors add index i_index_author_author_type (index_author (350), author_type)";
		echo traite_rqt($rqt,"alter table authors add index i_index_author_author_type");
	case '2' :
		//JP - Ajout d'une colonne commentaire dans la table des recherches prédéfinies
		if (!pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM search_perso LIKE 'search_comment'"))){
			$rqt = "alter table search_perso add search_comment text not null";
			echo traite_rqt($rqt,"alter table search_perso add search_comment");
		}
	case '3' :
		//JP - Export des informations de documents numériques dans les notices en unimarc pmb xml
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='export_allow_expl' "))){
			$rqt = "update parametres set comment_param='Exporter les exemplaires et les documents numériques avec les notices :\n 0 : Aucun\n 1 : Uniquement les exemplaires\n 2 : Uniquement les documents numériques\n 3 : Les exemplaires et les documents numériques' where type_param='opac' and sstype_param='export_allow_expl' " ;
			echo traite_rqt($rqt,"update parameters opac_export_allow_expl");
		}
	case '4' :
		//JP - Paramètre gérant l'entête de la fiche lecteur
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'empr' and sstype_param='header_format' "))){
			$rqt = "update parametres set comment_param='Champs qui seront affichés dans l\'entête de la fiche emprunteur. Séparer les valeurs par des virgules. \nPour les champs personnalisés, saisir les identifiants. Les autres valeurs possibles sont les propriétés de la classe PHP \"pmb/opac_css/classes/emprunteur.class.php\".' where type_param='empr' and sstype_param='header_format' " ;
			echo traite_rqt($rqt,"update parameters empr_header_format");
		}
	case '5' :
		//JP & MB - Ajout d'index sur la table cms_cache_cadres
		$req="SHOW INDEX FROM cms_cache_cadres WHERE key_name LIKE 'i_cache_cadre_create_date'";
		$res=pmb_mysql_query($req);
		if($res && (pmb_mysql_num_rows($res) == 0)){
			$rqt = "alter table cms_cache_cadres add index i_cache_cadre_create_date(cache_cadre_create_date)";
			echo traite_rqt($rqt,"alter table cache_cadre_create_date add index i_cache_cadre_create_date");
		}
	case '6':
		//VT - Paramètre de définition du style dojo en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='dojo_gestion_style' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
			VALUES ( 'pmb', 'dojo_gestion_style', 'claro', 'Styles disponibles: tundra, claro, flat, nihilo, soria','', 0)";
			echo traite_rqt($rqt,"insert pmb_dojo_gestion_style into parametres");
		}
	case '7':
		// VT & AP - Ajout d'un droit sur le statut de lecteur pour les contributions
		$rqt = "alter table empr_statut add allow_contribution int unsigned not null default 0";
		echo traite_rqt($rqt,"alter table empr_statut add allow_contribution");
			
		// AP & VT - Modification du nom du parametre empr_contribution en empr_contribution_area
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution' "))){
			$rqt = "update parametres set sstype_param='empr_contribution_area' where type_param='gestion_acces' and sstype_param='empr_contribution' " ;
			echo traite_rqt($rqt,"update parameters set sstype_param='empr_contribution_area' where sstype_param='empr_contribution'");
		}
			
		// AP & VT - Modification du nom du parametre empr_contribution_def en empr_contribution_area_def
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_def' "))){
			$rqt = "update parametres set sstype_param='empr_contribution_area_def' where type_param='gestion_acces' and sstype_param='empr_contribution_def' " ;
			echo traite_rqt($rqt,"update parameters set sstype_param='empr_contribution_area_def' where sstype_param='empr_contribution_def'");
		}
			
		// AP & VT - Ajout du parametre empr_contribution_scenario dans les droits d'acces
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_scenario' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'empr_contribution_scenario', '0', 'Gestion des droits d\'accès des emprunteurs aux scénarios de contribution\n0 : Non.\n1 : Oui.', '', 0)";
			echo traite_rqt($rqt,"insert empr_contribution_scenario into parametres");
		}
			
		// AP & VT - Ajout du parametre empr_contribution_scenario_def dans les droits d'acces
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='empr_contribution_scenario_def' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'empr_contribution_scenario_def', '0', 'Valeur par défaut en modification de scenario de contribution pour les droits d\'accès emprunteurs - scénarios\n0 : Recalculer.\n1 : Choisir.', '', 0)";
			echo traite_rqt($rqt,"insert empr_contribution_scenario into parametres");
		}
			
		// AP & VT - Ajout du parametre contribution_moderator_empr dans les droits d'acces
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='contribution_moderator_empr' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'contribution_moderator_empr', '0', 'Gestion des droits d\'accès des modérateurs sur les contributeurs\n0 : Non.\n1 : Oui.', '', 0)";
			echo traite_rqt($rqt,"insert contribution_moderator_empr into parametres");
		}
			
		// AP & VT - Ajout du parametre contribution_moderator_empr_def dans les droits d'acces
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'gestion_acces' and sstype_param='contribution_moderator_empr_def' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'gestion_acces', 'contribution_moderator_empr_def', '0', 'Valeur par défaut en modification d\'emprunteur pour les droits d\'accès modérateur - emprunteur\n0 : Recalculer.\n1 : Choisir.', '', 0)";
			echo traite_rqt($rqt,"insert contribution_moderator_empr_def into parametres");
		}
			
		// AP & VT - Suppression du statut sur les formulaires de contribution
		if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_forms LIKE 'form_status'"))){
			$rqt = "ALTER TABLE contribution_area_forms drop column form_status";
			echo traite_rqt($rqt,"ALTER TABLE contribution_area_forms drop column form_status");
		}
			
		// AP & VT - Ajout d'un statut sur les espaces de contributions
		if (pmb_mysql_num_rows(pmb_mysql_query("SHOW COLUMNS FROM contribution_area_areas LIKE 'area_status'"))==0){
			$rqt = "ALTER TABLE contribution_area_areas add column area_status int(10) unsigned not null default 1";
			echo traite_rqt($rqt,"ALTER TABLE contribution_area_areas add column area_status");
		}
	case '8':
		//JP - Index incorrect sur faq_questions_words_global_index
		$rqt ="alter table faq_questions_words_global_index drop primary key";
		echo traite_rqt($rqt,"alter table faq_questions_words_global_index drop primary key");
		$rqt ="alter table faq_questions_words_global_index add primary key (id_faq_question,code_champ,code_ss_champ,num_word,position,field_position)";
		echo traite_rqt($rqt,"alter table faq_questions_words_global_index add primary key");
		if ($faq_active) {
			// Info de réindexation
			$rqt = " select 1 " ;
			echo traite_rqt($rqt,"<b><a href='".$base_path."/admin.php?categ=netbase' target=_blank>VOUS DEVEZ REINDEXER LA FAQ / YOU MUST REINDEX THE FAQ : Admin > Outils > Nettoyage de base > Réindexer la faq</a></b> ") ;
		}
	case '9':
		// TS - VT - Afficher template même sans données
		$rqt = "ALTER TABLE frbr_cadres ADD cadre_display_empty_template tinyint(1) UNSIGNED NOT NULL default 1" ;
		echo traite_rqt($rqt,"ALTER TABLE frbr_cadres ADD cadre_display_empty_template");
	case '10':		
		// NG - Si concept actif, attribution des droits de modification des concepts CONCEPTS_AUTH, 
		// à tous les utilisateurs ayant acces à THESAURUS_AUTH,
		// seulement si aucun utilisateur n'a ce droit sur les concepts
		if($thesaurus_concepts_active) {
			if (!pmb_mysql_num_rows(pmb_mysql_query("select 1 from users where rights>=4194304"))) {				
				$rqt = "update users set rights=rights+4194304 where rights<4194304 and rights&2048";
				echo traite_rqt($rqt, "update users add rights CONCEPTS_AUTH");
			}
		}
	case '11':
		// NG - Ajout template pour les impressions de panier en OPAC
		$rqt="CREATE TABLE IF NOT EXISTS print_cart_tpl (
	            id_print_cart_tpl int unsigned not null auto_increment primary key,
	            print_cart_tpl_name varchar(255) not null default '',
	            print_cart_tpl_header text not null,
	            print_cart_tpl_footer text not null
       	        )";
		echo traite_rqt($rqt,"create table print_cart_tpl");
			
		// Ajout du paramètre indiquant le template à utiliser pour les impressions de panier en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='print_cart_header_footer' "))==0){
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param,section_param,gestion)
				VALUES ( 'opac', 'print_cart_header_footer', '', 'Identifiant du template à utiliser pour insérer un en-tête et un pied de page en impression de panier. Les templates sont créés en Administration > Template de Mail > Template impression de panier.','h_cart', 0)";
			echo traite_rqt($rqt,"insert opac_print_cart_header_footer into parametres");
		}
	case '12':
		//JP - choix des liens à conserver en remplacement de notice et en import
		$rqt = "ALTER TABLE users ADD deflt_notice_replace_links int(1) UNSIGNED DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_notice_replace_links");
	case '13' :
		// TS - Paramètre pour l'activiation de l'autopostage dans les concepts 
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion)
				VALUES ( 'thesaurus','concepts_autopostage', '0', 'Activer la recherche de notices dans les concepts génériques. \n 0 : Non, \n 1 : Oui', 'concepts', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage") ;
		}
		
		// TS - Paramètre pour le nombre de niveaux de recherche de l'autopostage dans les concepts génériques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage_generic_levels_nb' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ( 'thesaurus', 'concepts_autopostage_generic_levels_nb', '*', 'Nombre de niveaux de recherche de notices dans les concepts génériques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage_generic_levels_nb") ;
		}
		
		// TS - Paramètre pour le nombre de niveaux de recherche de l'autopostage dans les concepts spécifiques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'thesaurus' and sstype_param='concepts_autopostage_specific_levels_nb' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ( 'thesaurus', 'concepts_autopostage_specific_levels_nb', '*', 'Nombre de niveaux de recherche de notices dans les concepts spécifiques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0)" ;
			echo traite_rqt($rqt,"insert into parameters thesaurus_concepts_autopostage_specific_levels_nb") ;
		}
		
		// TS - Paramètre pour l'activiation de l'autopostage dans les concepts à l'OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='concepts_autopostage' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param,comment_param, section_param, gestion)
				VALUES ( 'opac','concepts_autopostage', '0', 'Activer la recherche de notices dans les concepts génériques. \n 0 : Non, \n 1 : Oui', 'c_recherche', 0)" ;
			echo traite_rqt($rqt,"insert into parameters opac_concepts_autopostage") ;
		}
	case '14' :
		// JP & DG - Nettoyage des autorités
		$rqt = "DELETE FROM authorities WHERE num_object = 0";
		echo traite_rqt($rqt,"DELETE FROM authorities WITH num_object = 0");
		
		// JP & DG - Nettoyage des doublons autorités
		require_once($class_path."/indexation_authority.class.php");
		require_once($class_path."/authority.class.php");
		$rqt = "SELECT COUNT(*) AS nbr_doublon, num_object, type_object FROM authorities GROUP BY num_object, type_object HAVING COUNT(*) > 1";
		$result = pmb_mysql_query($rqt);
		if($result && pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$query_authority = "select id_authority from authorities where num_object = ".$row->num_object." and type_object = ".$row->type_object." order by id_authority";
				$result_authority = pmb_mysql_query($query_authority);
				$id_authority = 0;
				while($row_authority = pmb_mysql_fetch_object($result_authority)) {
					if(!$id_authority) {
						$id_authority = $row_authority->id_authority;
					} else {
						$query_caddie_content = "select caddie_id from authorities_caddie_content where object_id = ".$row_authority->id_authority;
						$result_caddie_content = pmb_mysql_query($query_caddie_content);
						while($row_caddie_content = pmb_mysql_fetch_object($result_caddie_content)) {
							if(pmb_mysql_result(pmb_mysql_query("select count(*) from authorities_caddie_content where object_id = ".$id_authority." and caddie_id = ".$row_caddie_content->caddie_id), 0, 0)) {
								$requete = "delete from authorities_caddie_content where object_id = ".$row_authority->id_authority." and caddie_id = ".$row_caddie_content->caddie_id;
								pmb_mysql_query($requete);
							} else {
								$requete = "update authorities_caddie_content set object_id = ".$id_authority." where object_id = ".$row_authority->id_authority." and caddie_id = ".$row_caddie_content->caddie_id;
								pmb_mysql_query($requete);
							}
						}
		
						// nettoyage indexation
						indexation_authority::delete_all_index($row_authority->id_authority, "authorities", "id_authority", $row->type_object);
		
						pmb_mysql_query("delete from authorities where id_authority=".$row_authority->id_authority);
					}
				}
			}
		}
		
		// JP & DG - Passage de l'index en unique
		$rqt = "ALTER TABLE authorities DROP INDEX i_a_num_object_type_object,
			ADD UNIQUE KEY i_a_num_object_type_object(num_object,type_object)";
		echo traite_rqt($rqt,$rqt);
	case '15' :
		// AP - Statistiques de fréquentation en mode horaire
		$errors = '';
		if (pmb_mysql_num_rows(pmb_mysql_query('SHOW COLUMNS FROM visits_statistics LIKE "visits_statistics_value"'))) {
			$errors.= pmb_mysql_error();
			// Renommage de la table existante pour réinjection des données
			$rqt = 'RENAME TABLE visits_statistics TO visits_statistics_old';
			echo traite_rqt($rqt, $rqt);
			$errors.= pmb_mysql_error();
			
			// Création de la nouvelle table
			// visits_statistics_id : ID
			// visits_statistics_date : Date
			// visits_statistics_location : Localisation
			// visits_statistics_type : Type de service
			$rqt = 'CREATE TABLE if not exists visits_statistics (
					visits_statistics_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					visits_statistics_date DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00",
					visits_statistics_location SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
					visits_statistics_type VARCHAR(255) NOT NULL DEFAULT "",
					INDEX i_vs_visits_statistics_date (visits_statistics_date),
					INDEX i_vs_visits_statistics_location_visits_statistics_type (visits_statistics_location, visits_statistics_type)
			)';
			echo traite_rqt($rqt, 'CREATE TABLE visits_statistics');
			$errors.= pmb_mysql_error();
			
			// On va chercher les anciennes données pour ensuite les insérer dans la nouvelle table
			$rqt = 'SELECT visits_statistics_date, visits_statistics_location, visits_statistics_type, visits_statistics_value FROM visits_statistics_old ORDER BY visits_statistics_date';
			$result = pmb_mysql_query($rqt);
			$errors.= pmb_mysql_error();
			if (pmb_mysql_num_rows($result)) {
				$insert = array();
				while ($row = pmb_mysql_fetch_assoc($result)) {
					for ($i = 0; $i < $row['visits_statistics_value']; $i++) {
						$insert[] = '(0, "'.$row['visits_statistics_date'].' 00:00:00", "'.$row['visits_statistics_location'].'", "'.$row['visits_statistics_type'].'")';
					}
				}
				if (count($insert)) {
					$rqt = 'INSERT INTO visits_statistics(visits_statistics_id, visits_statistics_date, visits_statistics_location, visits_statistics_type) VALUES '.implode(',', $insert);
					echo traite_rqt($rqt, 'INSERT INTO visits_statistics') ;
					$errors.= pmb_mysql_error();
				}
			}
			
			// Si tout va bien, on supprime l'ancienne table
			if (!$errors) {
				$rqt = 'DROP TABLE visits_statistics_old';
				echo traite_rqt($rqt, $rqt.($empr_visits_statistics_active ? "<br/><b>Stat de fréquentation modifiées, vous devez mettre à jour vos états personnalisables.</b>": ""));
			} else if ($empr_visits_statistics_active) {
				$rqt="select 1";
				traite_rqt($rqt, "<b>Problème avec le traitement des modifications des tables de statistiques de fréquentation, la nouvelle table est créée mais les archives n'y ont pas été insérées, elles sont dans la table visits_statistics_old.</b>");
			}
		}
	case '16':
		// DG - Options pour le debogage - Afficher les erreurs PHP en gestion
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='display_errors' "))==0){
		    $rqt="INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (NULL, 'pmb', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui' , 'debug', '0')";
		    echo traite_rqt($rqt,"insert pmb_display_errors='0' into parametres ");
		}
		
		// DG - Options pour le debogage - Afficher les erreurs PHP en OPAC
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='display_errors' "))==0){
		    $rqt = "INSERT INTO parametres (id_param, type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES (0, 'opac', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui', 'debug', 0)" ;
		    echo traite_rqt($rqt,"insert opac_display_errors=0 into parametres");
		}
	case '17':
		// JP - Paramètre pour définir l'état par défaut de la case à cocher "Abonnement actif" dans le navigateur de périodiques
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'opac' and sstype_param='perio_a2z_default_active_subscription_filter' "))==0) {
			$rqt = "INSERT INTO parametres ( type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
				VALUES ( 'opac', 'perio_a2z_default_active_subscription_filter', '0', 'Filtre sur les abonnements actifs coché par défaut dans le navigateur de périodiques ?\n 0 : Non\n 1 : Oui', 'c_recherche', 0)" ;
			echo traite_rqt($rqt,"insert into parameters opac_perio_a2z_default_active_subscription_filter") ;
		}
	case '18':
		//AP & DG - Mise à jour des liens perdus entre les notices de bulletin et les périodiques
		require_once($class_path."/notice_relations.class.php");
		$query = "SELECT bulletins.num_notice, bulletins.bulletin_notice from bulletins left join notices_relations ON notices_relations.num_notice = bulletins.num_notice AND notices_relations.linked_notice = bulletins.bulletin_notice where bulletins.num_notice<>0 AND id_notices_relations IS NULL";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_fetch_object($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				notice_relations::insert($row->num_notice, $row->bulletin_notice, 'b', 1, 'up', false);
			}
			echo traite_rqt("SELECT 1","ALTER TABLE notices_relations UPDATE relations ");
		}
	case '19' :
		//JP - Liste des imprimantes ticket de prêt
		if (pmb_mysql_num_rows(pmb_mysql_query("select 1 from parametres where type_param= 'pmb' and sstype_param='printer_list' "))==0){
			$rqt = "INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion)
		VALUES ('pmb', 'printer_list', '', 'Liste des imprimantes de ticket de prêt gérées par raspberry, séparées par un point-virgule. Indiquer un identifiant, un libellé et une IP de raspberry (facultative) alternative à celle du paramètre général printer_name.\nExemple : 1_Imprimante prêt;2_Autre imprimante(192.168.0.83:3000).','', 0)";
			echo traite_rqt($rqt,"insert pmb_printer_list into parametres");
		}
		$rqt = "ALTER TABLE users ADD deflt_printer int(3) UNSIGNED DEFAULT 0";
		echo traite_rqt($rqt,"ALTER TABLE users ADD deflt_printer");
}











/******************** JUSQU'ICI **************************************************/
/* PENSER à faire +1 au paramètre $pmb_subversion_database_as_it_shouldbe dans includes/config.inc.php */
/* COMMITER les deux fichiers addon.inc.php ET config.inc.php en même temps */

echo traite_rqt("update parametres set valeur_param='".$pmb_subversion_database_as_it_shouldbe."' where type_param='pmb' and sstype_param='bdd_subversion'","Update to $pmb_subversion_database_as_it_shouldbe database subversion.");
echo "<table>";