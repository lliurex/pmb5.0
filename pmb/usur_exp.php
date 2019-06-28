<?php
//-------------------------------------> L L I U R E X <--------------------------------------//
// Modulo de importación/exportacion de usuarios de pmb, a partir de un fichero de texto plano


function compruebanull($row, $text, $sep)
{
	if($row[$text]==NULL){
	return (" " . $sep);}
	else return ($row[$text] . $sep);
}

$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
require_once ("$base_path/includes/init.inc.php");  


$categor = $_GET['categor'];


switch($categor){ // Selección de opciones.


case 'export':{
//Exportar a un fichero de texto

$sep= $_POST['separador'];
$temp="$base_path/temp/";//lo metemos en el temporal del pmb
$ale="export".rand().".txt";//auto nombre
$exp=fopen($temp.$ale, "w");
require("$base_path/includes/db_param.inc.php");
$link2 = @mysql_connect(SQL_SERVER, USER_NAME, USER_PASS) OR die("Error MySQL");
$sql= "SELECT empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut 
FROM empr WHERE id_empr is not null";
$result= @mysql_query($sql, $link2);
if (@mysql_num_rows($result) == 0) {
	echo "$msg[usur_imp_m]";
	break;
	}
else{ //variable que luego se escribirÃ¡ en el fichero de exportaciÃ³n
$cadena = "";
 while ($row = mysql_fetch_array($result)) {
$cadena.= compruebanull($row,'empr_cb',$sep);
$cadena.= compruebanull($row,'empr_nom',$sep);
$cadena.= compruebanull($row,'empr_prenom',$sep);
$cadena.= compruebanull($row,'empr_adr1',$sep);
$cadena.= compruebanull($row,'empr_adr2',$sep);
$cadena.= compruebanull($row,'empr_cp',$sep);
$cadena.= compruebanull($row,'empr_ville',$sep);
$cadena.= compruebanull($row,'empr_pays',$sep);
$cadena.= compruebanull($row,'empr_mail',$sep);
$cadena.= compruebanull($row,'empr_tel1',$sep);
$cadena.= compruebanull($row,'empr_tel2',$sep);
$cadena.= compruebanull($row,'empr_prof',$sep);
$cadena.= compruebanull($row,'empr_year',$sep);
$cadena.= compruebanull($row,'empr_sexe',$sep);
$cadena.= compruebanull($row,'empr_login',$sep);
$cadena.= compruebanull($row,'empr_password',$sep);
$cadena.= compruebanull($row,'empr_msg',$sep);
$cadena.= compruebanull($row,'empr_lang',$sep);
$cadena.= compruebanull($row,'type_abt',$sep);
$cadena.= compruebanull($row,'last_loan_date',$sep);
$cadena.= compruebanull($row,'empr_location',$sep);
$cadena.= compruebanull($row,'date_fin_blocage',$sep);
$cadena.= compruebanull($row,'total_loans',$sep);
$cadena.= compruebanull($row,'empr_statut',$sep);
$cadena.="\n\n";
}
fputs($exp, $cadena);
fclose($exp);
echo "$msg[exporta_b] &nbsp;<b><a href=descargas.php?f=$temp$ale target=\"_self\">". $ale . " </a></b><br>";
}}


default:

// Formulario de exportar
echo "$msg[export_intro]";
echo "<form class='form-admin' name='form2' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=empr&sub=export&action=?&categor=export\">";
echo "<h3>$msg[usur_exp_a]</h3>";
echo "<div class='form-contenu'><div class='row'><div class='colonne60'><label class='etiquette' for='form_import_lec'>$msg[usur_imp_o] </label><input type='textfield'  value='@__LlIuReX__@' name='separador' size='13'></div>";
echo "<b>$msg[usur_imp_p]&nbsp;</b><input align='center' type='button' name='fichero' value='Continuar' onclick='form.submit()'></div></div>";
echo "</form>";
break;

}
//-------------------------------------> L L I U R E X <--------------------------------------//
?>

