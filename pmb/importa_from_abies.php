<?php
//-------------------------------------> L L I U R E X <--------------------------------------//
//Modulo para importar toda la base de datos de un fichero sql.//


function update_docsloc_section(){

	$result='0';
	$link2 = @mysql_connect(SQL_SERVER, USER_NAME, USER_PASS) OR die("Error MySQL");
	$sql_location="Select idlocation from docs_location";
	$select_location=@mysql_query($sql_location, $link2);
	$sql_section="Select idsection from docs_section";
	$select_section=@mysql_query($sql_section, $link2);
	$section_array=array();
	$location_array=array();
	while ($section=mysql_fetch_array($select_section)){
		$section_array[]=$section['idsection'];
								
	}
	while ($location=mysql_fetch_array($select_location)){
		$location_array[]=$location['idlocation'];

	}	
				
	foreach ($section_array as $value){
		if ( $value=='28' ){
				foreach ($location_array as $value2){
					$sql_locs_section="Insert into docsloc_section (num_section,num_location) select '". $value . "','". $value2 ."' from dual where NOT EXISTS (Select * from docsloc_section where num_section='". $value ."' and num_location='" .$value2. "')";
					$insert=@mysql_query($sql_locs_section,$link2);
							
				}	
				unset($value2);
		}	
	}
	unset($value1);

	$result='1';
	return $result;

}


$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
$form_ref="$base_path/admin.php?categ=import&sub=import_abies&action=";
$form_index="$base_path/admin.php?categ=netbase";
require_once ("$base_path/includes/init.inc.php");  

include("$include_path/messages/help/$lang/importa_abies.txt");

$categor = $_GET['categor'];


switch($categor){ // Selección de opciones.
	case 'import': {
		if (!is_uploaded_file($_FILES['fich']['tmp_name'])){
			$php_sin_fichero="El fitxer no ha pogut set carregat. Informe a l'administrador del sistema per a que revise la configuració de php.";
			echo "<SCRIPT>alert(\"$php_sin_fichero\");</SCRIPT>"; 
			echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$form_ref/\"</SCRIPT>");  
			break;
	
               	} 

		// Formulario de tablas de importacion
		$nomfich = "./temp/".$_FILES['fich']['name']; //nombre fichero en el cliente
                $nfich =$_FILES['fich']['name'];
                                
		$cont= (strlen($_FILES['fich']['name']))-3; //saca la extension (ultimos 3 digitos)
		
               //$fExt=substr($nfich,$cont);
               //$finfo=finfo_open(FILEINFO_MIME_TYPE);
               //$ftype=finfo_file($finfo,$_FILES['fich']['tmp_name']);
	       //finfo_close($finfo);
		$path_info=pathinfo($nomfich);
		$extension=$path_info['extension'];
							
		if ($extension!="sql"){
			echo "<SCRIPT>alert(\"$msg[importa_a]\");</SCRIPT>";
			echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$form_ref/\"</SCRIPT>");
			break;
		}

		$post_max_size_php_MB=ini_get('upload_max_filesize');
		$post_max_size_php = substr(ini_get('upload_max_filesize'),0,-1)*1024*1024;
		$nom_fich_size = filesize($nomfich);
					
		if ($nom_fich_size > $post_max_size_php){
			$php_ini_conf = "El fitxer té una mida de: " . number_format($nom_fich_size/1024/1024, 2, '.', ' ') . "MB,\\nsuperior al permés: " . $post_max_size_php_MB ."B\\n\\nInforme a l'administrador del sistema per actualitzar la configuració de php.";
			echo "<SCRIPT>alert(\"$php_ini_conf\");</SCRIPT>";
			echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$form_ref/\"</SCRIPT>");
			
			break;
		}
		if (move_uploaded_file($_FILES['fich']['tmp_name'], $nomfich)){ //el POsT devuelve el nombre de archivo en el servidor y el segundo campo es a donde se va a mover. 
			require("$base_path/includes/db_param.inc.php");
			$result='0';
			$comando= "cat ". $nomfich ." | mysql -u ". USER_NAME ." --password=". USER_PASS ." ". DATA_BASE;
			if (system($comando, $salida)==0){
				$result='1';
				$cdu_fich="./lliurex/cdu_Abies.sql";
				$comando= "cat ". $cdu_fich ." | mysql -u ". USER_NAME ." --password=". USER_PASS ." ". DATA_BASE;
				if (system($comando, $salida)==0){
					$cdu_fich="./lliurex/utilidades_sql/abies_update.sql";
					$comando= "cat ". $cdu_fich ." | mysql -u ". USER_NAME ." --password=". USER_PASS ." ". DATA_BASE;
					if (system($comando, $salida)==0){
						$result_insert=update_docsloc_section();
						if ($result_insert=='1'){
							$result='2';
						}
					}	
				}	
			}

			if ($result=='2'){
				
				echo "<SCRIPT>alert(\"$msg[import_abies_a]\");</SCRIPT>";
				echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$form_index\"</SCRIPT>");


			}
			else{
				echo "<SCRIPT>alert(\"$msg[import_abies_d]\");</SCRIPT>";
				echo("<SCRIPT LANGUAGE='JavaScript'> window.location = \"$form_ref/\"</SCRIPT>");
			
			}
			
			
	}
		break;
	}
	default:{
		echo "<form class='form-admin' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=import&sub=import_abies&categor=import\"><h3>$msg[import_abies_b]</h3><div class='form-contenu'><div class='row'><div class='colonne60'><label class='etiquette' for='form_import_lec'>$msg[import_abies_c]</label><input name='fich' accept='.sql' type='file'  size='40'></div><br><div class='colonne60'><input type='button' name='fichero' value='Continuar' onclick='form.submit()'></div><br><br><br></form>";
		break;
	}
}


//-------------------------------------> L L I U R E X <--------------------------------------//
?>