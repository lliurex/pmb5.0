<?php
//-------------------------------------> L L I U R E X <--------------------------------------//
//Modulo para importar toda la base de datos de un fichero sql.//

$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
require_once ("$base_path/includes/init.inc.php");  

$categor = $_GET['categor'];

switch($categor){ // Selección de opciones.
	case 'import': {
		//--(16/12/2014)--Se comprueba que se ha podido subir el fichero--INI
		if (!is_uploaded_file($_FILES['fich']['tmp_name'])){
			$php_sin_fichero="El fitxer no ha pogut set carregat. Informe a l'administrador del sistema per a que revise la configuració de php.";
			echo "<SCRIPT>alert(\"$php_sin_fichero\");</SCRIPT>"; 
			echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");  
			break;
               	} //--(16/12/2014)--Se comprueba que se ha podido subir el fichero--FIN

		// Formulario de tablas de importacion
		$nomfich = "./temp/".$_FILES['fich']['name']; //nombre fichero en el cliente
 		// -- (17/12/2014)--Nombre del fichero--INI
                $nfich =$_FILES['fich']['name'];
                // -- (17/12/2014)--Nombre del fichero--FIN
                                
		$cont= (strlen($_FILES['fich']['name']))-3; //saca la extension (ultimos 3 digitos)
		
                // --(17/12/2014)--Se obtiene la extensión del fichero--INI
                //$fExt=substr($nomfich, $cont);
                $fExt=substr($nfich,$cont);
                // --(17/12/2014)--Se obtiene la extensión del fichero--FIN
                $finfo=finfo_open(FILEINFO_MIME_TYPE);
                $ftype=finfo_file($finfo,$_FILES['fich']['tmp_name']);
		finfo_close($finfo);
              			        	
		// -- (17/12/2014)--Se corrige la validación para detectar extensiones correctas--INI
		//if (!strpos($fExt, "sql") && $_FILES['fich']['type'] == "text/x-sql"){
		  if (!strpos($fExt, "sql") && $ftype == "text/x-c"){
		// -- (17/12/2014)--Se corrige la validación para detectar extensiones correctas--FIN
			echo "$msg[importa_a]";
			break;
		}
		$post_max_size_php_MB=ini_get('upload_max_filesize');
		$post_max_size_php = substr(ini_get('upload_max_filesize'),0,-1)*1024*1024;
		$nom_fich_size = filesize($nomfich);
					
		if ($nom_fich_size > $post_max_size_php){
			$php_ini_conf = "El fitxer té una mida de: " . number_format($nom_fich_size/1024/1024, 2, '.', ' ') . "MB,\\nsuperior al permés: " . $post_max_size_php_MB ."B\\n\\nInforme a l'administrador del sistema per actualitzar la configuració de php.";
			echo "<SCRIPT>alert(\"$php_ini_conf\");</SCRIPT>";
			echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
			
			break;
		}
		if (move_uploaded_file($_FILES['fich']['tmp_name'], $nomfich)){ //el POsT devuelve el nombre de archivo en el servidor y el segundo campo es a donde se va a mover. 
			require("$base_path/includes/db_param.inc.php");
			$comando= "cat ". $nomfich ." | mysql -u ". USER_NAME ." --password=". USER_PASS ." ". DATA_BASE;
			if (system($comando, $salida)==0){
				echo "$msg[importa_b]";
			}
			// -------------------------------- LLIUREX 11/02/2013
			// Trataremos de forma distinta la importación de versiones anteriores de Nemo
			$query = "select valeur_param from parametres where type_param='pmb' and sstype_param='bdd_version' ";
			$req = mysql_query($query, $dbh);
			$data = mysql_fetch_array($req) ;
			$version_pmb_bdd = $data['valeur_param'];
			echo " versió: ".$version_pmb_bdd;
		//-----------------------------LLIUREX 26/09/2018------------------	
			$query="select valeur_param from parametres where type_param='pmb' and sstype_param ='indexation_must_be_initialized'";
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_num_rows($result)) {
				$query="update parametres set valeur_param='-1' where type_param='pmb' and sstype_param ='indexation_must_be_initialized'";
				$res = mysql_query($query, $dbh);
			}else{
				$query="INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES ('pmb','indexation_must_be_initialized','-1','Indexation required','',0)";
				$res = mysql_query($query, $dbh);
			}

		//------------------------------- FIN LLIUREX 26/09/2018-----------------
						
		//----------------------------- SE ACTUALIZAN PARAMETROS PARA DESHABILITAR EDICION FORMULARIOS----------------

			$query="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_authorities_editables' and valeur_param='1'";
			$result=pmb_mysql_query($query,$dbh);
			
			$query="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_editables' and valeur_param='1'";
			$result=pmb_mysql_query($query,$dbh);
			
			$query="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_expl_editables' and valeur_param='1'";
			$result=pmb_mysql_query($query,$dbh);
			
			$query="Update parametres set valeur_param='0' WHERE type_param='pmb' and sstype_param='form_explnum_editables' and valeur_param='1'";
			$result=pmb_mysql_query($query,$dbh);

			
		//-------------------------------------- LLIUREX CONVOCATORIA------------------------------------	
			//--------------------------Campo personalizado para asociar ejemplares a una convocatoria----------------------------------------
			$query="Insert into expl_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
					select 'Convocatoria','Incluir ejemplar en la convocatoria para el curso:','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"0\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE></DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,3 
					from dual where NOT exists (Select name from expl_custom where name like 'Convocatoria')";
			$result = pmb_mysql_query($query, $dbh);	
			
			$query="Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
					Select (select idchamp from expl_custom where name='Convocatoria'),'2019','2019',1 from dual where not exists
					(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2019' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
					Select (select idchamp from expl_custom where name='Convocatoria'),'2020','2020',2 from dual where not exists
					(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2020' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
					Select (select idchamp from expl_custom where name='Convocatoria'),'2021','2021',3 from dual where not exists
					(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2021' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
					Select (select idchamp from expl_custom where name='Convocatoria'),'2022','2022',4 from dual where not exists
					(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2022' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
					Select (select idchamp from expl_custom where name='Convocatoria'),'2023','2023',5 from dual where not exists
					(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2023' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
					Select (select idchamp from expl_custom where name='Convocatoria'),'2024','2024',6 from dual where not exists
					(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2024' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into expl_custom_lists (expl_custom_champ,expl_custom_list_value,expl_custom_list_lib,ordre)
					Select (select idchamp from expl_custom where name='Convocatoria'),'2025','2025',7 from dual where not exists
					(select expl_custom_list_value from expl_custom_lists where expl_custom_list_value='2025' and expl_custom_champ=(select idchamp from expl_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			//------------------------- Campo personalizado para asociar ejemplares digitales a una convocatoria
			$query="Insert into explnum_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
					select 'Convocatoria','Incluir ejemplar en la convocatoria para el curso:','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"0\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE></DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,3 
					from dual where NOT exists (Select name from explnum_custom where name like 'Convocatoria')";
			$result = pmb_mysql_query($query, $dbh);	
			
			$query="Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
					Select (select idchamp from explnum_custom where name='Convocatoria'),'2019','2019',1 from dual where not exists
					(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2019' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
					Select (select idchamp from explnum_custom where name='Convocatoria'),'2020','2020',2 from dual where not exists
					(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2020' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
					Select (select idchamp from explnum_custom where name='Convocatoria'),'2021','2021',3 from dual where not exists
					(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2021' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
					Select (select idchamp from explnum_custom where name='Convocatoria'),'2022','2022',4 from dual where not exists
					(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2022' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
					Select (select idchamp from explnum_custom where name='Convocatoria'),'2023','2023',5 from dual where not exists
					(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2023' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
					Select (select idchamp from explnum_custom where name='Convocatoria'),'2024','2024',6 from dual where not exists
					(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2024' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into explnum_custom_lists (explnum_custom_champ,explnum_custom_list_value,explnum_custom_list_lib,ordre)
					Select (select idchamp from explnum_custom where name='Convocatoria'),'2025','2025',7 from dual where not exists
					(select explnum_custom_list_value from explnum_custom_lists where explnum_custom_list_value='2025' and explnum_custom_champ=(select idchamp from explnum_custom where name='Convocatoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			
						//-------------------------Campo personalizado para indicar el tipo de indentificación del registro-------------------------------------
			$query="Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
					Select 'Identificacion','Tipo de indentificación','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,1
					from dual where not exists (select name from notices_custom where name='Identificacion')";
			$result = pmb_mysql_query($query, $dbh);		
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN134','ISBN 13',1 
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN134' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'))";
			$result = pmb_mysql_query($query, $dbh);		

			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN81','ISSN',3 
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN81' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'))";
			$result = pmb_mysql_query($query, $dbh);		
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN103','ISBN 10',2 
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN103' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'))";
			$result = pmb_mysql_query($query, $dbh);

			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Identificacion'),'ISBN247','ISAN',4 
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ISBN247' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'))";
			$result = pmb_mysql_query($query, $dbh);	
	
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Identificacion'),'OTROS','Altres / Otros',5
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='OTROS' and notices_custom_champ=(Select idchamp from notices_custom where name='Identificacion'))";
;			$result = pmb_mysql_query($query, $dbh);
		
			//-----------------------Campo personalizado para indicar el idioma del registro----------------------------
			
			$query="Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
					Select 'Idioma','Idioma','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,2
					from dual where not exists (select name from notices_custom where name='Idioma')";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'valenciano','Valencià / Valenciano',1
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='valenciano' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'castellano','Castellà / Castellano',2
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='castellano' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'ingles','Anglés / Inglés',3
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ingles' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'frances','Francés / Francés',4
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='frances' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'aleman','Alemany / Alemán',5
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='aleman' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'portugues','Portugués / Portugués',6
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='portugues' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'italiano','Italià / Italiano',7
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='italiano' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'arabe','Àrab / Árabe',8
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='arabe' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'ruso','Rus / Ruso',9
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='ruso' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Idioma'),'otros','Altres / Otros',10
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='otros' and notices_custom_champ=(Select idchamp from notices_custom where name='Idioma'))";
			$result = pmb_mysql_query($query, $dbh);
			
			//-------------------------Campo personalizado para indicar si el registro tiene autoria femenina----------------------
			$query="Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
					Select 'Autoria','Tiene autoria o coautoria femenina','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,3
					from dual where not exists (select name from notices_custom where name='Autoria')";
			$result = pmb_mysql_query($query, $dbh);	
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Autoria'),'SI','Si',1
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='S' and notices_custom_champ=(Select idchamp from notices_custom where name='Autoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Autoria'),'NO','No',2
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='N' and notices_custom_champ=(Select idchamp from notices_custom where name='Autoria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			//-------------------------Campo personalizado para indicar si el registro es una obra literaria------------------------
			$query="Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
				      Select 'Literaria','¿Es una obra literarária?','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,4
				      from dual where not exists (select name from notices_custom where name='Literaria')";
			$result = pmb_mysql_query($query, $dbh);
		
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Literaria'),'SI','Si',1
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='S' and notices_custom_champ=(Select idchamp from notices_custom where name='Literaria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Literaria'),'NO','No',2
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='N' and notices_custom_champ=(Select idchamp from notices_custom where name='Literaria'))";
			$result = pmb_mysql_query($query, $dbh);
			
			//------------------------Campo personalizado para indicar el precio del registro ----------------------------------------
			$query="Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
					Select 'Precio','Precio pagado por ejemplar con IVA','text','float','<OPTIONS FOR=\"text\">\r\n <SIZE>6</SIZE>\r\n <MAXSIZE>6</MAXSIZE>\r\n <REPEATABLE>0</REPEATABLE>\r\n <ISHTML>0</ISHTML>\r\n</OPTIONS> ',0,0,5
					from dual where not exists (select name from notices_custom where name='Precio')";
			$result = pmb_mysql_query($query, $dbh);
		
			
			//------------------------Campo personalizao para indicar la ubicación del registro-----------------------------------
			$query="Insert into notices_custom (name,titre,type,datatype,options,multiple,obligatoire,ordre)
				      Select 'Ubicacion','Ubicación','list','small_text','<OPTIONS FOR=\"list\">\r\n <MULTIPLE>no</MULTIPLE>\r\n <AUTORITE>no</AUTORITE>\r\n <CHECKBOX>no</CHECKBOX>\r\n <NUM_AUTO>no</NUM_AUTO>\r\n <UNSELECT_ITEM VALUE=\"N/A\"><![CDATA[]]></UNSELECT_ITEM>\r\n <DEFAULT_VALUE>N/A</DEFAULT_VALUE>\r\n <CHECKBOX_NB_ON_LINE></CHECKBOX_NB_ON_LINE>\r\n</OPTIONS>',0,0,6
				      from dual where not exists (select name from notices_custom where name='Ubicacion')";
			$result = pmb_mysql_query($query, $dbh);
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Ubicacion'),'biblioEscolar','Biblioteca Escolar',1
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='biblioEscolar' and notices_custom_champ=(Select idchamp from notices_custom where name='Ubicacion'))";
			$result = pmb_mysql_query($query, $dbh);		
			
			$query="Insert into notices_custom_lists (notices_custom_champ,notices_custom_list_value,notices_custom_list_lib,ordre)
					Select (Select idchamp from notices_custom where name='Ubicacion'),'biblioAula','Biblioteca de l\'aula / Biblioteca del Aula',2
					from dual where not exists (select notices_custom_list_value from notices_custom_lists where notices_custom_list_value='biblioAula' and notices_custom_champ=(Select idchamp from notices_custom where name='Ubicacion'))";
			$result = pmb_mysql_query($query, $dbh);		
			
			
			//--------------------------------- LLIUREX 06/04/2016-----------------
			switch ($version_pmb_bdd){
			case 'v4.47':{
				//cambiamos la versión para que el proceso de actualización sea más rápido
				$rqt = "update parametres set valeur_param='vLlxNemo' where type_param='pmb' and sstype_param='bdd_version' ";
				$res = mysql_query($rqt, $dbh);
				//cambiamos el tema por defecto de pmb4
				$rqt = "update users set deflt_styles = 'light' ";
				$res = mysql_query($rqt, $dbh);
				//activamos las cestas
				$rqt = "update parametres set valeur_param='1' where type_param='empr' and sstype_param='show_caddie' ";
				$res = mysql_query($rqt, $dbh);

				echo "<SCRIPT>alert(\"".$msg[close_session]." ".$msg[database_update]."\");</SCRIPT>";
				echo("<SCRIPT LANGUAGE='JavaScript'> window.alert($msg[close_session])</SCRIPT>");
				echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
				break;
			}
			case 'v5.10':{
				//cambiamos la versión para que el proceso de actualización sea más rápido
				$rqt = "update parametres set valeur_param='vLlxPandora' where type_param='pmb' and sstype_param='bdd_version' ";
				$res = mysql_query($rqt, $dbh);
				echo "<SCRIPT>alert(\"".$msg[close_session]." ".$msg[database_update]."\");</SCRIPT>";
				echo("<SCRIPT LANGUAGE='JavaScript'> window.alert($msg[close_session])</SCRIPT>");
				echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
				break;	

			}
			case 'v5.14':{
				//cambiamos la versión para que el proceso de actualización sea más rápido
				$rqt = "update parametres set valeur_param='vLlxTrusty' where type_param='pmb' and sstype_param='bdd_version' ";
				$res = mysql_query($rqt, $dbh);
				//---------------LLIUREX 08/06/2017--Se añade campo a la tabla notices_mots_global index-----------------
			
				echo "<SCRIPT>alert(\"".$msg[close_session]." ".$msg[database_update]."\");</SCRIPT>";
				echo("<SCRIPT LANGUAGE='JavaScript'> window.alert($msg[close_session])</SCRIPT>");
				echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
				break;	
				
			}	
			case 'v5.19':{
				//Se añade campo a la tabla notices_mots_global index-----------------
				$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_name = 'field_position'";
				$res=mysql_query($rqt, $dbh);
				$data = mysql_num_rows($res) ;
				if ($data == 0) {
					$rqt= "alter table notices_mots_global_index add column field_position int not null default 1";
					$res=mysql_query($rqt, $dbh);
				}	
				$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_name='field_position' and column_key = 'PRI'";
				$res=mysql_query($rqt, $dbh);
				$data = mysql_num_rows($res) ;
				if ($data ==0){
					$rqt = "select * from information_schema.columns where table_name = 'notices_mots_global_index' and table_schema ='pmb' and column_key = 'PRI'";
					$res=mysql_query($rqt, $dbh);
					$data = mysql_num_rows($res) ;
					if ($data >0){
						$rqt= "alter table notices_mots_global_index drop PRIMARY KEY";
						$res=mysql_query($rqt, $dbh);
					}else{

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

					}

					$rqt= "alter table notices_mots_global_index add PRIMARY KEY (id_notice, code_champ, code_ss_champ, num_word, position, field_position)";
					$res=mysql_query($rqt, $dbh);
					
				}	
				//--------------FIN LLIUREX 08/06/2017 -----------------

				//--------------LLIUREX 07/03/2018----------------------
				//cambiamos la versión para que el proceso de actualización sea más rápido

				$rqt = "update parametres set valeur_param='vLlxXenial' where type_param='pmb' and sstype_param='bdd_version' ";
				$res = mysql_query($rqt, $dbh);
				//------------FIN LLIUREX 07/03/2018---------------------

				
				echo "<SCRIPT>alert(\"".$msg[close_session]." ".$msg[database_update]."\");</SCRIPT>";
				echo("<SCRIPT LANGUAGE='JavaScript'> window.alert($msg[close_session])</SCRIPT>");
				echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
				break;	

			}
			default:{
				echo("<SCRIPT LANGUAGE='JavaScript'> window.alert($msg[close_session])</SCRIPT>");
				echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$base_path/\"</SCRIPT>");
			// -------------------------------- LLIUREX 
				break;
			}	
		}
		//----------------------------------- FIN LLIUREX 06/04/2016-----------------------------------
	}
		break;
	}
	default:{
		echo "<form class='form-admin' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=sauvegarde&sub=lliureximp&categor=import\"><h3>$msg[importa_c]</h3><div class='form-contenu'><div class='row'><div class='colonne60'><label class='etiquette' for='form_import_lec'>$msg[importa_d]</label><input name='fich' accept='.sql' type='file'  size='40'></div><br><div class='colonne60'><input type='button' name='fichero' value='Continuar' onclick='form.submit()'></div><br><br><br></form>";
		break;
	}
}
//-------------------------------------> L L I U R E X <--------------------------------------//
?>
