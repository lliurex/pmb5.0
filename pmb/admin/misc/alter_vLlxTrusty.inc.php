<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: alter_vLlxTrusty.inc.php, migración BD a v5.28 (Lliurex 16+ Xenial desde v.5.14 (Lliurex 15.05 Trusty)


if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

settype ($action,"string");

mysql_query("set names latin1 ", $dbh);
switch ($version_pmb_bdd) {
	case "vLlxTrusty":
	
//	case "v5.14": 
//---------------------LLIUREX 07/03/2018-------------------------	
		// 15 actualizaciones desde trusty (v5.14) a xenial+ (v5.28)
		$increment=100/15;
//---------------------FIN LLIUREX 07/03/2018----------------------		
		$action=$increment;
		echo "<table ><tr><th>".$msg['admin_misc_action']."</th><th>".$msg['admin_misc_resultat']."</th></tr>";
		
		//-----------------------LLIUREX 09/06/2017----Add champ field_position to notices_mots_global_index------------
		$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_name = 'field_position'";
		$res=pmb_mysql_query($rqt,$dbh);
		$data=pmb_mysql_num_rows($res);
		if ($data == 0) {
			$rqt= "alter table notices_mots_global_index add column field_position int not null default 1";
			$res=pmb_mysql_query($rqt, $dbh);

		}

		$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_key = 'PRI'";
		$res=mysql_query($rqt, $dbh);
		$data = mysql_num_rows($res) ;
		if ($data == 0) {
			$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_name = 'num_word'";
			$res=mysql_query($rqt, $dbh);
			$data = mysql_num_rows($res) ;
			if ($data == 0) {
				$rqt= "alter table notices_mots_global_index add num_word int(10) unsigned not null default 0 after mot";
				$res=mysql_query($rqt, $dbh);
			}
			$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_name = 'mot'";
			$res=mysql_query($rqt, $dbh);
			$data = mysql_num_rows($res) ;
			if ($data > 0) {
				$rqt= "alter table notices_mots_global_index drop mot";
				$res=mysql_query($rqt, $dbh);
			}	
			$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_name = 'nbr_mot'";
			$res=mysql_query($rqt, $dbh);
			$data = mysql_num_rows($res) ;
			if ($data > 0) {
				$rqt= "alter table notices_mots_global_index drop nbr_mot";
				$res=mysql_query($rqt, $dbh);
			}	
			$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_name = 'lang'";
			$res=mysql_query($rqt, $dbh);
			$data = mysql_num_rows($res) ;
			if ($data > 0) {
				$rqt= "alter table notices_mots_global_index drop lang";
				$res=mysql_query($rqt, $dbh);
			}	
			$rqt = "alter table notices_mots_global_index add  PRIMARY KEY (id_notice, code_champ,code_ss_champ, num_word, position)";
			$res=mysql_query($rqt, $dbh);
										
		}




		//-----------------------FIN LLIUREX 09/06/2017

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
//case "v5.15":		
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
			

	//-----------------LLIUREX 07/03/2018---------------	
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
//--------------------------- FIN LLIUREX 07/03/2018------------------		
		$res = mysql_query($rqt, $dbh) ;
		echo "<strong><font color='#FF0000'>".$msg[1807]." ".number_format($action, 2, ',', '.')."%</font></strong><br />";
		$action=$action+$increment;

		// // Parcheamos la base de datos
		require("$base_path/includes/db_param.inc.php");
//---------------------------------------------------LLIUREX 14/03/2018---------------------------------
//Cambiamos mods_vLlxNemo.sql por mods_vLlxTrusty.sql
		$comando= "cat ".$base_path."/admin/misc/mods_vLlxTrusty.sql | mysql -u ". USER_NAME ." --password=". USER_PASS ." ". DATA_BASE;
//--------------------------------------------------FIN LLIUREX 14/03/2018---------------------------		
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
		
