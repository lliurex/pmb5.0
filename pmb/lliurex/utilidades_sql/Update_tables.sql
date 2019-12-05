-- Se actualiza la tabla z_bib con los nuevos datos de conexión a Rebeca

UPDATE z_bib SET bib_nom = 'REBECA', url = 'catalogos.mecd.es', port = '220', base = 'ABNET_REBECA', format ='ISO 8859-1' WHERE url = 'rebeca.mcu.es' || url='rebeca_z3950.mcu.es';

-- Se actualiza la tabla z_bib con los nuevos datos de conexión a Biblioteca Valenciana
UPDATE z_bib SET bib_nom = 'Biblioteca Valenciana', url = 'bvnpz3950.gva.es', port = '2102', base = 'ABNET_DB', format ='ISO 8859-1' WHERE url = 'bv.gva.es';

-- Se cambia la versión de base de datos de v5.19 a vLlxXenial para actualizar a PMB 5.0.4
UPDATE parametres SET valeur_param='vLlxXenial' WHERE type_param='pmb' and sstype_param='bdd_version' and valeur_param='v5.19';

-- Se cambia el idioma por defecto del tesauro a es_ES para que la creación de nuevas categorias funcione correctamente

UPDATE thesaurus SET libelle_thesaurus= 'Tesauro nº 1', langue_defaut='es_ES' WHERE libelle_thesaurus='Agneaux' and langue_defaut='fr_FR' and id_thesaurus='1';

-- Se añade una acción personalizada para renovar usuarios

Insert into procs (name,requete,comment,autorisations, parameters,num_classement,proc_notice_tpl,proc_notice_tpl_field) select 'LLIUREX_RENOV:Canvi de data de finalització de l\'abonament ','Update empr set empr_date_expiration=\'!!date!!\' where empr_date_expiration<curdate()','Acció per a renovar als usuaris que tenen caducat l\'abonament','1','<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<FIELDS>\n <FIELD NAME=\"date\" MANDATORY=\"yes\">\n  <ALIAS><![CDATA[Seleccione la nova data de caducitat:]]></ALIAS>\n  <TYPE>date_box</TYPE>\n<OPTIONS FOR=\"date_box\"></OPTIONS>\n </FIELD>\n</FIELDS>',20,0,'' from dual where NOT EXISTS(Select * from procs where name like 'LLIUREX_RENOV%');


DELIMITER $$

DROP PROCEDURE IF EXISTS alter_table_addfield $$
CREATE PROCEDURE alter_table_addfield()
BEGIN

IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='pmb' AND COLUMN_NAME='field_position' AND TABLE_NAME='notices_mots_global_index') ) THEN
    ALTER TABLE notices_mots_global_index ADD field_position int not null default 1;

END IF;    

IF NOT EXISTS ((SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='pmb' AND TABLE_NAME='notices_mots_global_index' AND COLUMN_NAME='field_position' AND COLUMN_KEY='PRI')) THEN
	IF EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='pmb' AND TABLE_NAME='notices_mots_global_index' AND COLUMN_KEY='PRI')) THEN
	   ALTER TABLE notices_mots_global_index DROP PRIMARY KEY;
    END IF;	
   	ALTER TABLE notices_mots_global_index ADD PRIMARY KEY (id_notice, code_champ, code_ss_champ, num_word, position, field_position);

END IF;	

END $$

CALL alter_table_addfield() $$

DROP PROCEDURE IF EXISTS alter_table_addfield $$
DELIMITER ;
