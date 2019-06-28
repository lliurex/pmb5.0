-- Modificar codificacion campos tablas

ALTER TABLE abts_abts MODIFY abt_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE abts_abts MODIFY base_modele_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE abts_abts MODIFY destinataire varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE abts_abts MODIFY cote varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE abts_abts MODIFY prix varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE abts_grille_modele MODIFY numero varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE abts_modeles MODIFY modele_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE abts_modeles MODIFY days varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1111111';
ALTER TABLE abts_modeles MODIFY day_month varchar(31) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1111111111111111111111111111111';
ALTER TABLE abts_modeles MODIFY week_month varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '111111';
ALTER TABLE abts_modeles MODIFY week_year varchar(54) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '111111111111111111111111111111111111111111111111111111';
ALTER TABLE abts_modeles MODIFY month_year varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '111111111111';
ALTER TABLE abts_modeles MODIFY format_aff varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE abts_modeles MODIFY format_periode varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE abts_periodicites MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE actes MODIFY numero varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE actes MODIFY num_paiement varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE actes MODIFY commentaires text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE actes MODIFY reference varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE actes MODIFY index_acte text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE actes MODIFY devise varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE actes MODIFY commentaires_i text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE actes MODIFY nom_acte varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE audit MODIFY user_name varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE audit MODIFY info text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE author_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE author_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE author_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE author_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE author_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE author_custom_lists MODIFY author_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE author_custom_lists MODIFY author_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE author_custom_values MODIFY author_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE author_custom_values MODIFY author_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;


ALTER TABLE authors MODIFY author_type enum('70','71','72') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '70';
ALTER TABLE authors MODIFY author_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY author_rejete varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY author_date varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY author_web varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY index_author text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE authors MODIFY author_comment text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE authors MODIFY author_lieu varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY author_ville varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY author_pays varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY author_subdivision varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE authors MODIFY author_numero varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE avis MODIFY sujet text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE avis MODIFY commentaire text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE bannette_abon MODIFY bannette_mail varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE bannette_exports MODIFY export_nomfichier varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '';

ALTER TABLE bannettes MODIFY nom_bannette varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE bannettes MODIFY comment_gestion text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE bannettes MODIFY comment_public text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE bannettes MODIFY entete_mail text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE bannettes MODIFY limite_type char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE bannettes MODIFY update_type char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'C';
ALTER TABLE bannettes MODIFY typeexport varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE bannettes MODIFY prefixe_fichier varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE bannettes MODIFY piedpage_mail text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE budgets MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE budgets MODIFY commentaires text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE bulletins MODIFY bulletin_numero varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE bulletins MODIFY mention_date varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE bulletins MODIFY bulletin_titre text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE bulletins MODIFY index_titre text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE bulletins MODIFY bulletin_cb varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE caddie MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE caddie MODIFY type varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NOTI';
ALTER TABLE caddie MODIFY comment varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE caddie MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE caddie MODIFY caddie_classement varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE caddie MODIFY creation_user_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE caddie_content MODIFY content varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE caddie_content MODIFY blob_type varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '';
ALTER TABLE caddie_content MODIFY flag varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE caddie_procs MODIFY type varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SELECT';
ALTER TABLE caddie_procs MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE caddie_procs MODIFY comment  tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE caddie_procs MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE caddie_procs MODIFY parameters text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE cashdesk MODIFY cashdesk_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cashdesk MODIFY cashdesk_autorisations varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cashdesk MODIFY cashdesk_transactypes varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE categ_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE categ_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE categ_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE categ_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE categ_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;


ALTER TABLE categ_custom_lists MODIFY categ_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE categ_custom_lists MODIFY categ_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE categ_custom_values MODIFY categ_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE categ_custom_values MODIFY categ_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;


ALTER TABLE categories MODIFY langue varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR';
ALTER TABLE categories MODIFY libelle_categorie text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE categories MODIFY note_application text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE categories MODIFY comment_public text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE categories MODIFY comment_voir text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE categories MODIFY index_categorie text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE categories MODIFY path_word_categ text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE categories MODIFY index_path_word_categ text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE classements MODIFY type_classement char(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BAN';
ALTER TABLE classements MODIFY nom_classement varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE classements MODIFY classement_opac_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE cms_cache_cadres MODIFY cache_cadre_hash varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE cms_cache_cadres MODIFY cache_cadre_type_content varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE cms_cache_cadres MODIFY cache_cadre_content mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE cms_collections MODIFY collection_title varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cms_collections MODIFY collection_description text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE cms_documents MODIFY document_title varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cms_documents MODIFY document_description text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE cms_documents MODIFY document_filename varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cms_documents MODIFY document_mimetype varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cms_documents MODIFY document_url text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE cms_documents MODIFY document_path varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cms_documents MODIFY document_type_object varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE cms_documents_links MODIFY document_link_type_object varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE cms_modules_extensions_datas MODIFY extension_datas_module varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cms_modules_extensions_datas MODIFY extension_datas_type varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE cms_modules_extensions_datas MODIFY extension_datas_type_element varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE collection_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE collection_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE collection_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE collection_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE collection_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE collection_custom_lists MODIFY collection_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE collection_custom_lists MODIFY collection_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE collection_custom_values MODIFY collection_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE collection_custom_values MODIFY collection_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE collections MODIFY collection_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE collections MODIFY collection_issn varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE collections MODIFY index_coll text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE collections MODIFY collection_web text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE collections MODIFY collection_comment text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE collections_state MODIFY state_collections text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE collections_state MODIFY collstate_origine varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE collections_state MODIFY collstate_cote varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE collections_state MODIFY collstate_archive varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE collections_state MODIFY collstate_lacune text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE collections_state MODIFY collstate_note text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE comptes MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE comptes MODIFY droits text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE connectors MODIFY connector_id varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE connectors MODIFY parameters text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE connectors_sources MODIFY id_connector varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE connectors_sources MODIFY parameters mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE connectors_sources MODIFY comment varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE connectors_sources MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE connectors_sources MODIFY type_enrichment_allowed text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE connectors_sources MODIFY ico_notice varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE coordonnees MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY contact varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY adr1 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY adr2 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY cp varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY ville varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY etat varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY pays varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY tel1 varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY tel2 varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY fax varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY email varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE coordonnees MODIFY commentaires text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE docs_codestat MODIFY codestat_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE docs_codestat MODIFY statisdoc_codage_import char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE docs_location MODIFY location_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE docs_location MODIFY locdoc_codage_import varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY location_pic varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY adr1 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY adr2 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY cp varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY town varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY state varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY country varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY phone varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY email varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY website varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY logo varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_location MODIFY commentaire text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE docs_location MODIFY css_style varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE docs_section MODIFY section_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE docs_section MODIFY sdoc_codage_import varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE docs_section MODIFY section_pic varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE docs_statut MODIFY statut_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE docs_statut MODIFY statut_libelle_opac varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '';
ALTER TABLE docs_statut MODIFY statusdoc_codage_import char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE docs_type MODIFY tdoc_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE docs_type MODIFY tdoc_codage_import varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE empr MODIFY empr_cb varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE empr MODIFY empr_nom varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_prenom varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_adr1 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_adr2 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_cp varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_ville varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_pays varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_mail varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_tel1 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_tel2 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_prof varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_login varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_password varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_digest varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_msg text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE empr MODIFY empr_lang varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR';
ALTER TABLE empr MODIFY cle_validation varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr MODIFY empr_subscription_action text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE empr_caddie MODIFY name varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE empr_caddie MODIFY comment varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE empr_caddie MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE empr_caddie MODIFY empr_caddie_classement varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr_caddie MODIFY creation_user_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE empr_caddie_content MODIFY flag varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE empr_caddie_procs MODIFY type varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'SELECT';
ALTER TABLE empr_caddie_procs MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr_caddie_procs MODIFY comment tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE empr_caddie_procs MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE empr_caddie_procs MODIFY parameters text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE empr_categ MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE empr_codestat MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DEFAULT';

ALTER TABLE empr_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE empr_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE empr_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE empr_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE empr_custom_lists MODIFY empr_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE empr_custom_lists MODIFY empr_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE empr_custom_values MODIFY empr_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE empr_custom_values MODIFY empr_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE empr_statut MODIFY statut_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE empty_words_calculs MODIFY php_empty_words text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE entites MODIFY raison_sociale varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY commentaires text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE entites MODIFY siret varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY naf varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY rcs varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY tva varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY num_cp_client varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY num_cp_compta varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY site_web varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY logo varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE entites MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE entites MODIFY index_entite text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE equations MODIFY nom_equation text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE equations MODIFY comment_equation varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE error_log MODIFY error_origin varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE error_log MODIFY error_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE etagere MODIFY name varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE etagere MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE etagere MODIFY etagere_classement varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE etagere MODIFY comment_gestion text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE exemplaires MODIFY expl_cb varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE exemplaires MODIFY expl_cote varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE exemplaires MODIFY expl_note tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE exemplaires MODIFY expl_prix varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE exemplaires MODIFY expl_comment text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE exemplaires_temp MODIFY cb varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE exemplaires_temp MODIFY sess varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE exercices MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE expl_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE expl_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE expl_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE expl_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE expl_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE expl_custom_lists MODIFY expl_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE expl_custom_lists MODIFY expl_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE expl_custom_values MODIFY expl_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE expl_custom_values MODIFY expl_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE explnum MODIFY explnum_nom varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE explnum MODIFY explnum_mimetype varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE explnum MODIFY explnum_url text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE explnum MODIFY explnum_extfichier varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT ''; 
ALTER TABLE explnum MODIFY explnum_nomfichier text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE explnum MODIFY explnum_index_sew mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE explnum MODIFY explnum_index_wew mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE explnum MODIFY explnum_path text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE explnum MODIFY explnum_signature varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE explnum_speakers MODIFY explnum_speaker_speaker_num varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE explnum_speakers MODIFY explnum_speaker_gender varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '';

ALTER TABLE frais MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE frais MODIFY condition_frais text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE frais MODIFY num_cp_compta varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE frais MODIFY num_tva_achat varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';
ALTER TABLE frais MODIFY index_libelle text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE grilles MODIFY grille_typdoc char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a';
ALTER TABLE grilles MODIFY grille_niveau_biblio char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm';
ALTER TABLE grilles MODIFY descr_format longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE groupe MODIFY libelle_groupe varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE import_marc MODIFY origine varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '';
ALTER TABLE import_marc MODIFY encoding varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE indexint MODIFY indexint_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE indexint MODIFY indexint_comment text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE indexint MODIFY index_indexint text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE indexint_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE indexint_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE indexint_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE indexint_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE indexint_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE indexint_custom_lists MODIFY indexint_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE indexint_custom_lists MODIFY indexint_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE indexint_custom_values MODIFY indexint_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE indexint_custom_values MODIFY indexint_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE lenders MODIFY lender_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE lignes_actes MODIFY libelle text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE lignes_actes MODIFY code varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE lignes_actes MODIFY index_ligne text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE lignes_actes MODIFY commentaires_gestion text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE lignes_actes MODIFY commentaires_opac text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE mots MODIFY mot varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE noeuds MODIFY autorite varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE noeuds MODIFY visible char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1';
ALTER TABLE noeuds MODIFY path text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE notice_statut MODIFY gestion_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notice_statut MODIFY opac_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notice_statut MODIFY class_html varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE notices MODIFY typdoc Char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a';
ALTER TABLE notices MODIFY tit1 text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY tit2 text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY tit3 text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY tit4 text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY tnvol varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices MODIFY year varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices MODIFY nocoll varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices MODIFY mention_edition varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices MODIFY code varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices MODIFY npages varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices MODIFY ill varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices MODIFY size varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices MODIFY accomp varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices MODIFY n_gen text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices MODIFY n_contenu text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices MODIFY n_resume text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices MODIFY lien text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices MODIFY eformat varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices MODIFY index_l text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices MODIFY index_serie tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY index_matieres text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices MODIFY niveau_biblio char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm';
ALTER TABLE notices MODIFY niveau_hierar char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';
ALTER TABLE notices MODIFY prix varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices MODIFY index_n_gen text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY index_n_contenu text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY index_n_resume text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY index_sew text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY index_wew text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE notices MODIFY commentaire_gestion text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices MODIFY signature varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices MODIFY indexation_lang varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices MODIFY map_equinoxe varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE notices_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE notices_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE notices_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE notices_custom_lists MODIFY notices_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices_custom_lists MODIFY notices_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE notices_custom_values MODIFY notices_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE notices_custom_values MODIFY notices_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE notices_global_index MODIFY infos_global text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE notices_global_index MODIFY index_infos_global text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE notices_langues MODIFY code_langue char(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE notices_relations MODIFY relation_type char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE offres_remises MODIFY condition_remise text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE origine_notice MODIFY orinot_nom varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE origine_notice MODIFY orinot_pays varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'FR';

ALTER TABLE ouvertures MODIFY commentaire varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE paiements MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE paiements MODIFY commentaire text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE parametres MODIFY type_param varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE parametres MODIFY sstype_param varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE parametres MODIFY valeur_param text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE parametres MODIFY comment_param longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE parametres MODIFY section_param varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE pclassement MODIFY name_pclass varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE pclassement MODIFY typedoc varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE pret MODIFY pret_temp varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE pret_archive MODIFY arc_empr_cp varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE pret_archive MODIFY arc_empr_ville varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE pret_archive MODIFY arc_empr_prof varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE pret_archive MODIFY arc_expl_cote varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE pret_archive MODIFY arc_groupe varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE procs MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE procs MODIFY comment tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE procs MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE procs MODIFY parameters text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE procs MODIFY proc_notice_tpl_field varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE publisher_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publisher_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE publisher_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE publisher_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publisher_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE publisher_custom_lists MODIFY publisher_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE publisher_custom_lists MODIFY publisher_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE publisher_custom_values MODIFY publisher_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE publisher_custom_values MODIFY publisher_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE publishers MODIFY ed_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publishers MODIFY ed_adr1 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publishers MODIFY ed_adr2 varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publishers MODIFY ed_cp varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publishers MODIFY ed_ville varchar(96) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publishers MODIFY ed_pays varchar(96) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publishers MODIFY ed_web varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE publishers MODIFY index_publisher text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE publishers MODIFY ed_comment text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE quotas MODIFY constraint_type varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE quotas_finance MODIFY constraint_type varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE rdfstore_index MODIFY subject_uri text CHARACTER SET utf8 NOT NULL;
ALTER TABLE rdfstore_index MODIFY subject_type text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE rdfstore_index MODIFY predicat_uri text CHARACTER SET utf8 NOT NULL;
ALTER TABLE rdfstore_index MODIFY object_val text CHARACTER SET utf8 NOT NULL;
ALTER TABLE rdfstore_index MODIFY object_index text CHARACTER SET utf8 NOT NULL;
ALTER TABLE rdfstore_index MODIFY object_lang char(5) CHARACTER SET utf8 NOT NULL DEFAULT '';

ALTER TABLE recouvrements MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE resa MODIFY resa_cb varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE resa_ranger MODIFY resa_cb varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE responsability MODIFY responsability_fonction varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE rss_flux MODIFY nom_rss_flux varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE rss_flux MODIFY lang_rss_flux varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr';
ALTER TABLE rss_flux MODIFY editor_rss_flux varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE rss_flux MODIFY webmaster_rss_flux varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE rss_flux_content MODIFY type_contenant char(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'BAN';

ALTER TABLE rubriques MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE rubriques MODIFY commentaires text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE rubriques MODIFY num_cp_compta varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE rubriques MODIFY autorisations mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE sauv_lieux MODIFY sauv_lieu_nom varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_lieux MODIFY sauv_lieu_url varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_lieux MODIFY sauv_lieu_protocol varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'file';
ALTER TABLE sauv_lieux MODIFY sauv_lieu_host varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_lieux MODIFY sauv_lieu_login varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_lieux MODIFY sauv_lieu_password varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE sauv_log MODIFY sauv_log_file varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_log MODIFY sauv_log_messages mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_nom varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_file_prefix varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_tables mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_tables mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_lieux mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_users mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_compress_command mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_key1 varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_sauvegardes MODIFY sauv_sauvegarde_key2 varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE sauv_tables MODIFY sauv_table_nom varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE sauv_tables MODIFY sauv_table_tables text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE serie_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE serie_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE serie_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE serie_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE serie_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE serie_custom_lists MODIFY serie_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE serie_custom_lists MODIFY serie_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE serie_custom_values MODIFY serie_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE serie_custom_values MODIFY serie_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE series MODIFY serie_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE series MODIFY serie_index text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE sessions MODIFY SESSID varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sessions MODIFY login varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sessions MODIFY IP varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sessions MODIFY SESSstart varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sessions MODIFY LastOn varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sessions MODIFY SESSNAME varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sessions MODIFY notifications text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE source_sync MODIFY nrecu varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE source_sync MODIFY ntotal varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE source_sync MODIFY message varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE source_sync MODIFY env text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE storages MODIFY storage_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE storages MODIFY storage_class varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE storages MODIFY storage_params text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE sub_collections MODIFY sub_coll_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sub_collections MODIFY sub_coll_issn varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sub_collections MODIFY index_sub_coll text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE sub_collections MODIFY subcollection_web text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE sub_collections MODIFY subcollection_comment text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE subcollection_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE subcollection_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE subcollection_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE subcollection_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE subcollection_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE subcollection_custom_lists MODIFY subcollection_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE subcollection_custom_lists MODIFY subcollection_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE subcollection_custom_values MODIFY subcollection_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE subcollection_custom_values MODIFY subcollection_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE suggestions MODIFY titre tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE suggestions MODIFY editeur varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE suggestions MODIFY auteur varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE suggestions MODIFY code varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE suggestions MODIFY commentaires text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE suggestions MODIFY commentaires_gestion text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE suggestions MODIFY index_suggestion text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE suggestions MODIFY url_suggestion varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE suggestions MODIFY date_publication varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE suggestions_categ MODIFY libelle_categ varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE suggestions_origine MODIFY origine varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE tags MODIFY libelle varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE tags MODIFY user_code varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE thesaurus MODIFY libelle_thesaurus varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE thesaurus MODIFY langue_defaut varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR';
ALTER TABLE thesaurus MODIFY active char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1';
ALTER TABLE thesaurus MODIFY opac_active char(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1';

ALTER TABLE transactions MODIFY user_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE transactions MODIFY machine varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE transactions MODIFY commentaire text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE transactype MODIFY transactype_name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE tris MODIFY tri_par varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE tris MODIFY nom_tri varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE tris MODIFY tri_reference varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'notices';

ALTER TABLE tu_custom MODIFY name varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE tu_custom MODIFY titre varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE tu_custom MODIFY type varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text';
ALTER TABLE tu_custom MODIFY datatype varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE tu_custom MODIFY options text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE tu_custom_lists MODIFY tu_custom_list_value varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE tu_custom_lists MODIFY tu_custom_list_lib varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE tu_custom_values MODIFY tu_custom_small_text varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE tu_custom_values MODIFY tu_custom_text text CHARACTER SET utf8 COLLATE utf8_unicode_ci;

ALTER TABLE tva_achats MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE tva_achats MODIFY num_cp_compta varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';

ALTER TABLE type_abts MODIFY type_abt_libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE type_abts MODIFY commentaire text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE type_abts MODIFY localisations varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE type_comptes MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE type_comptes MODIFY acces_id text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE types_produits MODIFY libelle varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE types_produits MODIFY num_cp_compta varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';
ALTER TABLE types_produits MODIFY num_tva_achat varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';

ALTER TABLE users MODIFY username varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY pwd varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY user_digest varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY nom varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY prenom varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE users MODIFY user_lang varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fr_FR';
ALTER TABLE users MODIFY xmlta_indexation_lang varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY deflt_styles varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default';
ALTER TABLE users MODIFY value_deflt_lang varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'fre';
ALTER TABLE users MODIFY value_deflt_fonction varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '070';
ALTER TABLE users MODIFY value_deflt_relation varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a';
ALTER TABLE users MODIFY value_deflt_relation_serial varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL DEFAULT '';
ALTER TABLE users MODIFY value_deflt_relation_bulletin varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY value_deflt_relation_analysis varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY value_deflt_module varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'circu';
ALTER TABLE users MODIFY user_email varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '';
ALTER TABLE users MODIFY xmlta_doctype char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a';
ALTER TABLE users MODIFY xmlta_doctype_serial varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY xmlta_doctype_bulletin varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY xmlta_doctype_analysis varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  DEFAULT '';
ALTER TABLE users MODIFY speci_coordonnees_etab mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE users MODIFY value_email_bcc varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE users MODIFY value_deflt_antivol varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0';
ALTER TABLE users MODIFY explr_invisible text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE users MODIFY explr_visible_mod text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE users MODIFY explr_visible_unmod text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE users MODIFY xmlta_doctype_scan_request_folder_record varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'a';

ALTER TABLE voir_aussi MODIFY langue varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE voir_aussi MODIFY comment_voir_aussi text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE z_attr MODIFY attr_libelle varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE z_attr MODIFY attr_attr varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE z_bib MODIFY bib_nom varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_bib MODIFY search_type varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_bib MODIFY url varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_bib MODIFY port varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_bib MODIFY base varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_bib MODIFY format varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_bib MODIFY auth_user varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE z_bib MODIFY auth_pass varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE z_bib MODIFY sutrs_lang varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE z_bib MODIFY fichier_func varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE z_notices MODIFY isbd text CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE z_notices MODIFY isbn varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_notices MODIFY titre varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE z_notices MODIFY auteur varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

ALTER TABLE z_query MODIFY search_attr varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;


























