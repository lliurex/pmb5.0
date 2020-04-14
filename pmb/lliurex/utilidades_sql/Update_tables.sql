-- Se actualiza la tabla z_bib con los nuevos datos de conexión a Rebeca

UPDATE z_bib SET bib_nom = 'REBECA', url = 'catalogos.mecd.es', port = '220', base = 'ABNET_REBECA', format ='ISO 8859-1' WHERE url = 'rebeca.mcu.es' || url='rebeca_z3950.mcu.es';

-- Se actualiza la tabla z_bib con los nuevos datos de conexión a Biblioteca Valenciana
UPDATE z_bib SET bib_nom = 'Biblioteca Valenciana', url = 'bvnpz3950.gva.es', port = '2102', base = 'ABNET_DB', format ='ISO 8859-1' WHERE url = 'bv.gva.es';

-- Se actualiza la tabla z_bib con los nuevos datos de conexion a CSIC
UPDATE z_bib SET bib_nom = 'Red de Bibliotecas del CSIC', url = 'eu00.alma.exlibrisgroup.com', port = '210', base = '34CSIC_INST', format ='MARC21' WHERE url = 'aleph.csic.es';

-- Se cambia la versión de base de datos de v5.19 a vLlxXenial para actualizar a PMB 5.0.4
UPDATE parametres SET valeur_param='vLlxXenial' WHERE type_param='pmb' and sstype_param='bdd_version' and valeur_param='v5.19';

-- Se cambia el idioma por defecto del tesauro a es_ES para que la creación de nuevas categorias funcione correctamente

UPDATE thesaurus SET libelle_thesaurus= 'Tesauro nº 1', langue_defaut='es_ES' WHERE libelle_thesaurus='Agneaux' and langue_defaut='fr_FR' and id_thesaurus='1';

-- Se añade una acción personalizada para renovar usuarios

Insert into procs (name,requete,comment,autorisations, parameters,num_classement,proc_notice_tpl,proc_notice_tpl_field) select 'LLIUREX_RENOV:Canvi de data de finalització de l\'abonament ','Update empr set empr_date_expiration=\'!!date!!\' where empr_date_expiration<curdate()','Acció per a renovar als usuaris que tenen caducat l\'abonament','1','<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n<FIELDS>\n <FIELD NAME=\"date\" MANDATORY=\"yes\">\n  <ALIAS><![CDATA[Seleccione la nova data de caducitat:]]></ALIAS>\n  <TYPE>date_box</TYPE>\n<OPTIONS FOR=\"date_box\"></OPTIONS>\n </FIELD>\n</FIELDS>',20,0,'' from dual where NOT EXISTS(Select * from procs where name like 'LLIUREX_RENOV%');
			
-- Se actualizan parametros para deshabilitar la edicion de los formularios

UPDATE parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_authorities_editables' and valeur_param='1';
UPDATE parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_editables' and valeur_param='1';
UPDATE parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_expl_editables' and valeur_param='1';
UPDATE parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_explnum_editables' and valeur_param='1';

-- Campo personalizado convocatoria para los ejemplares

Insert into expl_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
select 'Convocatoria','Incluir ejemplar en la convocatoria para el curso:','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"0\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE></DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,3 
from dual where NOT exists (Select name from expl_custom where name like 'Convocatoria');

-- Campo personalizado para asociar ejemplares a convocatorias

Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
Select (select idchamp from expl_custom where name='Convocatoria'),'2021','2021',3 from dual where not exists
(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2021' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'));

Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
Select (select idchamp from expl_custom where name='Convocatoria'),'2020','2020',2 from dual where not exists
(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2020' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'));

Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
Select (select idchamp from expl_custom where name='Convocatoria'),'2019','2019',1 from dual where not exists
(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2019' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'));

Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
Select (select idchamp from expl_custom where name='Convocatoria'),'2022','2022',4 from dual where not exists
(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2022' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'));

Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
Select (select idchamp from expl_custom where name='Convocatoria'),'2023','2023',5 from dual where not exists
(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2023' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'));

Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
Select (select idchamp from expl_custom where name='Convocatoria'),'2024','2024',6 from dual where not exists
(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2024' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'));

Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
Select (select idchamp from expl_custom where name='Convocatoria'),'2025','2025',7 from dual where not exists
(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2025' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'));


-- Campo personalizado convocatoria para los ejemplares digitales

Insert into explnum_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
select 'Convocatoria','Incluir ejemplar en la convocatoria para el curso:','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"0\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE></DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,3 
from dual where NOT exists (Select name from explnum_custom where name like 'Convocatoria');

-- Campo personalizado para asociar ejemplares digitales a convocatorias

Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
Select (select idchamp from explnum_custom where name='Convocatoria'),'2021','2021',3 from dual where not exists
(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2021' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'));

Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
Select (select idchamp from explnum_custom where name='Convocatoria'),'2020','2020',2 from dual where not exists
(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2020' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'));

Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
Select (select idchamp from explnum_custom where name='Convocatoria'),'2019','2019',1 from dual where not exists
(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2019' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'));

Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
Select (select idchamp from explnum_custom where name='Convocatoria'),'2022','2022',4 from dual where not exists
(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2022' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'));

Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
Select (select idchamp from explnum_custom where name='Convocatoria'),'2023','2023',5 from dual where not exists
(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2023' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'));

Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
Select (select idchamp from explnum_custom where name='Convocatoria'),'2024','2024',6 from dual where not exists
(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2024' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'));

Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
Select (select idchamp from explnum_custom where name='Convocatoria'),'2025','2025',7 from dual where not exists
(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2025' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'));


-- Campo personalizado para indicar el tipo de indentificaci�n del registro-------------------------------------
Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
Select 'Identificacion','Tipo de indentificación','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,1
from dual where not exists (select name from notices_custom where name='Identificacion');
			
Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN134','ISBN 13',1 
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN134' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'));
		
Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN81','ISSN',3 
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN81' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'));
		
Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN103','ISBN 10',2 
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN103' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN247','ISAN',4 
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN247' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Identificacion'),'OTROS','Altres / Otros',5
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='OTROS' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'));

-- Campo personalizado para indicar el idioma del registro
Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
Select 'Idioma','Idioma','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,2
from dual where not exists (select name from notices_custom where name='Idioma');

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'valenciano','Valencià / Valenciano',1
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='valenciano' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'castellano','Castellà / Castellano',2
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='castellano' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'ingles','Anglés / Inglés',3
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ingles' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'frances','Francés / Francés',4
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='frances' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'aleman','Alemany / Alemán',5
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='aleman' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'portugues','Portugués / Portugués',6
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='portugues' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'italiano','Italià / Italiano',7
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='italiano' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'arabe','Àrab / Árabe',8
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='arabe' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'ruso','Rus / Ruso',9
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ruso' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Idioma'),'otros','Altres / Otros',10
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='otros' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'));

-- Campo personalizado para indicar si el registro tiene autoria femenina----------------------
Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
Select 'Autoria','Tiene autoria o coautoria femenina','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,3
from dual where not exists (select name from notices_custom where name='Autoria');
	
Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Autoria'),'SI','Si',1
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='SI' and notices_custom_champ=(Select idchamp from notices_custom where name='Autoria'));

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Autoria'),'NO','No',2
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='NO' and notices_custom_champ=(Select idchamp from notices_custom where name='Autoria'));

-- Campo personalizado para indicar si el registro es una obra literaria------------------------
Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
Select 'Literaria','¿Es una obra literarária?','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,4
from dual where not exists (select name from notices_custom where name='Literaria');

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Literaria'),'SI','Si',1
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='SI' and notices_custom_champ=(Select idchamp from notices_custom where name='Literaria'));
			
Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Literaria'),'NO','No',2
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='NO' and notices_custom_champ=(Select idchamp from notices_custom where name='Literaria'));

-- Campo pesonalizado para indicar el precio del registro
Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
Select'Precio','Precio pagado por ejemplar con IVA','text','float','<OPTIONS FOR=\"text\">\r\n <SIZE>6</SIZE>\r\n <MAXSIZE>6</MAXSIZE>\r\n <REPEATABLE>0</REPEATABLE>\r\n <ISHTML>0</ISHTML>\r\n</OPTIONS> ',0,0,5
from dual where not exists (select name from notices_custom where name='Precio');


-- Campo personalizao para indicar la ubicaci�n del registro-----------------------------------
Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
Select 'Ubicacion','Ubicación','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,6
from dual where not exists (select name from notices_custom where name='Ubicacion');

Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Ubicacion'),'biblioEscolar','Biblioteca Escolar',1
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='biblioEscolar' and notices_custom_champ=(Select idchamp from notices_custom where name='Ubicacion'));
		
Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
Select (Select idchamp from notices_custom where name='Ubicacion'),'biblioAula','Biblioteca de l\'aula / Biblioteca del Aula',2
from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='biblioAula' and notices_custom_champ=(Select idchamp from notices_custom where name='Ubicacion'));
		

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
