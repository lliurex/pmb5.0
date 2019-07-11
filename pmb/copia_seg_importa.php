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
				$query="update parametres set valeur_param='0' where type_param='pmb' and sstype_param ='indexation_must_be_initialized'";
				$res = mysql_query($query, $dbh);
			}else{
				$query="INSERT INTO parametres (type_param, sstype_param, valeur_param, comment_param, section_param, gestion) VALUES ('pmb','indexation_must_be_initialized','0','Indexation required','',0)";
				$res = mysql_query($query, $dbh);
			}	
			
		//------------------------------- FIN LLIUREX 26/09/2018-----------------
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
