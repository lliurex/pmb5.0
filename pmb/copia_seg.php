<?php
//-------------------------------------> L L I U R E X <--------------------------------------
//Modulo para exportar toda la base de datos en un fichero sql.

$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
require_once ("$base_path/includes/init.inc.php");  

$temp="$base_path/temp/";//lo metemos en el temporal del pmb
$fich="copia_seg_pmb".rand().".sql";//auto nombre

$comando= "mysqldump -u ". USER_NAME ." --password=". USER_PASS ." --opt ". DATA_BASE . " >" .$temp.$fich;

if (system($comando, $salida)==0){
echo "$msg[exporta_a]";
echo "<br>";
echo "$msg[exporta_b]<b><a href=descargas.php?f=$temp$fich target=\"_self\">". $fich . " </a></b><br>";
}
else echo "Por alguna raz&oacute;n no se ha podido realizar la consulta, compruebe el usuario y el password de la base de datos del PMB";
//-------------------------------------> L L I U R E X <--------------------------------------
?>
