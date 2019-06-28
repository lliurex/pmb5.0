<?php
//-------------------------------------> L L I U R E X <--------------------------------------//
// Modulo de importación/exportacion de usuarios de pmb, a partir de un fichero de texto plano

//funcion para sacar los campos del texto plano
function sacacampo($archivo, $sep)
{//Meter el texto del fichero en una variable y extraer los campos mediante el separador dado.
	$linea ="";
	while (!feof($archivo)){ //metemos el fichero en una variable
		$linea.=fgets($archivo);
				}
	$linea= str_replace("\n", "", $linea); //quitamos los salto de linea
	return(split($sep, $linea));
}




function fields_slashes($field) {
	
	$que = array("&", "<", ">", "\\", "/");
	$por = array("&amp;", "&lt;", "&gt;", "_", "_");

	return addslashes(str_replace($que, $por, $field));
}

$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
require_once ("$base_path/includes/init.inc.php");  


$categor = $_GET['categor'];

switch($categor){ // Selección de opciones.

case 'import': {
// Formulario de tablas de importacion


$nomfich = "./temp/".$_FILES['fich']['name']; //nombre fichero en el cliente

$tipo = $_FILES['fich']['type']; //tipo fichero
$sep= $_POST['separador'];

if (!strcmp($tipo, "text/plain") || !strcmp($tipo, "text/xml")){
 require("$base_path/includes/db_param.inc.php");
 $link2 = @mysql_connect(SQL_SERVER, USER_NAME, USER_PASS) OR die("Error MySQL");
	if (move_uploaded_file($_FILES['fich']['tmp_name'], $nomfich)){ //el POsT devuelve el nombre de archivo en el servidor y el segundo campo es a donde se va a mover. 
		if (!strcmp($tipo, "text/plain")) {		
			$archivo= fopen($nomfich , "r");		
			$tot= sacacampo($archivo, $sep);
		} 
	}	
	$num=0;
	$cont=0;
	$campos=(count($tot))-1; //total de campos, se le resta 1 debido a que coge un campo mas (vacio)
	if (($campos%24) != 0){			
		exit("Campos $campos");	
		exit("<b><center>$msg[usur_imp_a]</center></b>");
	}
	while($num<$campos){
		if ($tot[$num] == " ") {
		$tot[$num] = NULL;}

		if(((($num+1)%24)== 0) && ($num != 0)){//cada 24 debido a que hay 24 campos
		
		$sql_comp= "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$num-23] . "' AND `empr`. `empr_nom` like '" . $tot[$num-22] . "' AND `empr`. `empr_prenom` like '" . $tot[$num-21] . "' )";
		$resul1= @mysql_query($sql_comp, $link2);
		$fecha= date('Y-m-d');
		$fecha_cad= date('Y-m-d', strtotime('+1 year'));
		
		if (trim($tot[$num-9]) != "") {
			$user_a=addslashes($tot[$num-9]);
			if (trim($tot[$num-8]) != "") $pass_a=addslashes($tot[$num-8]);
			else $pass_a=$tot[$num-23];
		} else {
			
			$user_a=$tot[$num-23];
			$pass_a=$tot[$num-23];
			//echo "a".$user_a." ".$pass_a."a";
			//exit(0);
		}
		if (trim($tot[$num-3]) != "") $loca=intval(($tot[$num-3]));
		else $loca=1;
		  if (@mysql_num_rows($resul1) != 0) {
			//echo "$msg[usur_imp_b] <b>" . $tot[$num-23] . "</b><br>";
			$row1 = mysql_fetch_array($resul1);
			$requete = "UPDATE empr SET ";
			$requete .= "empr_nom='".fields_slashes($tot[$num-22])."',";
			$requete .= "empr_prenom='".fields_slashes($tot[$num-21])."',";
			$requete .= "empr_adr1='".fields_slashes($tot[$num-20])."',";
			$requete .= "empr_adr2='".fields_slashes($tot[$num-19])."',";
			$requete .= "empr_cp='".fields_slashes($tot[$num-18])."',";
			$requete .= "empr_ville='".fields_slashes($tot[$num-17])."',";
			$requete .= "empr_pays='".fields_slashes($tot[$num-16])."',";
			$requete .= "empr_mail='".fields_slashes($tot[$num-15])."',";
			$requete .= "empr_tel1='".fields_slashes($tot[$num-14])."',";
			$requete .= "empr_tel2='".fields_slashes($tot[$num-13])."',";
			$requete .= "empr_prof='".fields_slashes($tot[$num-12])."',";
			$requete .= "empr_year=".intval(($tot[$num-11])).",";
			if ($row1['empr_login'] == "") {
				$requete .= "empr_login='".$user_a."', ";
				$requete .= "empr_password='".$pass_a."', ";
			}
			//$requete .= "empr_msg='".$tot[$num-7]."' ";
			//$requete .= "empr_lang='".$lang."', ";
			//$requete .= "type_abt='".$tot[$num-5]."', ";
			//$requete .= "last_loan_date='".$tot[$num-4]."', ";
			if ($row1['empr_location'] == "" || intval($row1['empr_location']) == 0) $requete .= "empr_location='".$loca."', ";
			//$requete .= "date_fin_blocage=$tot[$num-22],";
			//$requete .= "total_loans=$tot[$num-22],";
			//$requete .= "empr_statut='"$tot[$num-22]."',";
			$requete .= "empr_sexe=".intval(($tot[$num-10]))."";
			$requete .= " WHERE id_empr=".intval($row1['id_empr'])." ";
			$resul2 = @mysql_query($requete, $link2);
			$cont++;

			}
		else{ 
			// arreglar saltos de lÃ­nea
			if ($tot[$num] == "") $tot[$num] = 1;
			$sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat) values ( '" . fields_slashes($tot[$num-23]) . "', '" . fields_slashes($tot[$num-22]) . "', '" . fields_slashes($tot[$num-21]) . "', '" . fields_slashes($tot[$num-20]) . "', '" . fields_slashes($tot[$num-19]) . "', '" . fields_slashes($tot[$num-18]) . "', '" . fields_slashes($tot[$num-17]) . "', '" . fields_slashes($tot[$num-16]) . "', '" . fields_slashes($tot[$num-15]) . "', '" . fields_slashes($tot[$num-14]) . "', '" . fields_slashes($tot[$num-13]) . "', '" . fields_slashes($tot[$num-12]) . "', " . intval(($tot[$num-11])) . ", " . intval(($tot[$num-10])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num-7]) . "', '" . $lang . "', '" . fields_slashes($tot[$num-5]) . "', '" . $tot[$num-4] . "', $loca, '" . $tot[$num-2] . "', '" . $tot[$num-1] . "', '" . $tot[$num] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "', 7, 2 )";
			$resul2 = @mysql_query($sql, $link2);
			$cont++;

		}
	}
		
	$num++;
		}
echo "<b>$msg[usur_imp_c]  " . $cont . " $msg[usur_imp_d]   </b>";


if ($num>0){ //comparativa campos con los valores a insertar
echo "<h3>$msg[usur_imp_e]&nbsp;&nbsp;&nbsp;</h3><div class='form-contenu'><table width='98%' border='0' cellspacing='10'><td class='jauge'><b>$msg[usur_imp_f] <br>$msg[usur_imp_g]</b></td><td class='jauge' width='27%'><center><b>$msg[usur_imp_h] <br>$msg[usur_imp_i]</b></center></td><td class='jauge' width='60%'><b>$msg[usur_imp_j]<br>$msg[usur_imp_k]</b></td><tr><td class='nobrd'><font color='#FF0000'>id_empr</font></td><td class='nobrd'><center><input name='id_empr' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem0' value='' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_cb</td><td class='nobrd'><center><input name='empr_cb' value='1' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem1' value='" . $tot[0] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_nom</td><td class='nobrd'><center><input name='empr_nom' value='2' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem2' value='" . $tot[1] . "'  type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_prenom</td><td class='nobrd'><center><input name='empr_prenom' value='3' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem3' value='" . $tot[2] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_adr1</td><td class='nobrd'><center><input name='empr_adr1' value='4' type='text' size='1' disabled><td class='nobrd'><input name='exem5' value='" . $tot[3] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_adr2</td><td class='nobrd'><center><input name='empr_adr2' value='5' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem6' value='" . $tot[4] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_cp</td><td class='nobrd'><center><input name='empr_cp' value='6' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem7' value='" . $tot[5] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_ville</td><td class='nobrd'><center><input name='empr_ville' value='7' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem8' value='" . $tot[6] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_pays</td><td class='nobrd'><center><input name='empr_pays' value='8' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem9' value='" . $tot[7] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_mail</td><td class='nobrd'><center><input name='empr_mail' value='9' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem10' value='" . $tot[8] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_tel1</td><td class='nobrd'><center><input name='empr_tel1' value='10' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem11' value='" . $tot[9] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_tel2</td><td class='nobrd'><center><input name='empr_tel2' value='11' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem12' value='" . $tot[10] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_prof</td><td class='nobrd'><center><input name='empr_prof' value='12' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem13' value='" . $tot[11] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_year</td><td class='nobrd'><center><input name='empr_year' value='13' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem14' value='" . $tot[12] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_categ</font></td><td class='nobrd'><center><input name='empr_categ' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem15' value='7' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_codestat</font></td><td class='nobrd'><center><input name='empr_codestat' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem16' value='2' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_creation</font></td><td class='nobrd'><center><input name='empr_creation' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem17' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_modif</font></td><td class='nobrd'><center><input name='empr_modif' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem18' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_sexe</td><td class='nobrd'><center><input name='empr_sexe' value='14' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem19' value='" . $tot[13] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_login</td><td class='nobrd'><center><input name='empr_login' value='15' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem20' value='" . $tot[14] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_password</td><td class='nobrd'><center><input name='empr_password' value='16' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem21' value='" . $tot[15] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_date_adhesion</font></td><td class='nobrd'><center><input name='empr_date_adhesion' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem22' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_date_expiration</font></td><td class='nobrd'><center><input name='empr_date_expiration' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem23' value='" . $fecha_cad . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_msg</td><td class='nobrd'><center><input name='empr_msg' value='17' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem24' value='" . $tot[16] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_lang</td><td class='nobrd'><center><input name='empr_lang' value='18' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem25' value='" . $tot[17] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_ldap</font></td><td class='nobrd'><center><input name='empr_ldap' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem26' value='' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>type_abt</td><td class='nobrd'><center><input name='type_abt' value='19' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem27' value='" . $tot[18] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>last_loan_date</td><td class='nobrd'><center><input name='last_loan_date' value='20' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem28' value='" . $tot[19] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_location</td><td class='nobrd'><center><input name='empr_location' value='21' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem29' value='" . $tot[20] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>date_fin_blocage</td><td class='nobrd'><center><input name='date_fin_blocage' value='22' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem30' value='" . $tot[21] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>total_loans</td><td class='nobrd'><center><input name='total_loans' value='23' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem31' value='" . $tot[22] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_statut</td><td class='nobrd'><center><input name='empr_statut' value='24' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem32' value='" . $tot[23] . "' type='text' disabled size='40'></td></tr></table>";

fclose($archivo);
unlink($nomfich);
break;
}}
else 
	{
		echo "<b><center> ". $nomfich ." ". $msg["usur_imp_l"]."</center></b>";
		
	}
break;
}

default:

// Formulario para elegir fichero a importar/exportar.
echo "<form class='form-admin' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=empr&sub=implec&action=?&categor=import\"><h3>$msg[usur_imp_n]</h3><div class='form-contenu'><div class='row'><div class='colonne60'><label class='etiquette' for='form_import_lec'>$msg[importa_d]&nbsp;</label><input name='fich' accept='text/plain' type='file'  size='40'></div><div class='colonne_suite'><label class='etiquette' for='form_import_lec'>$msg[usur_imp_o] </label><input type='textfield'  value='@__LlIuReX__@' name='separador' size='13'></div><br><div class='colonne60'><input type='button' name='fichero' value='Continuar' onclick='form.submit()'></div><br><br><br></form>";
break;

}
//-------------------------------------> L L I U R E X <--------------------------------------//
?>

