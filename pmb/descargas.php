<?php
//-------------------------------------> L L I U R E X <--------------------------------------
//Modulo para descargar ficheros comprobando su extension y la ruta del fichero que se va a descargar
$base_noheader= "YES";
$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
require_once ("$base_path/includes/init.inc.php");  
$extensiones = array("jpg", "png", "sql", "txt");
    $f = $_GET["f"];
    if(strpos($f,"/temp")==false){
        die("$msg[descarg_err_a]");
    }
$f2=substr($f, 7); //saco el nombre de fichero unicamente "./temp/"
$cont= (strlen($f))-3; //saca la extension (ultimos 3 digitos)
$fExt=substr($f, $cont); 
    if(!in_array($fExt, $extensiones)){
        die("$msg[descarg_err_b] $f2");
    }
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$f2\"\n");
    $fp=fopen("$f", "r");
    fpassthru($fp);
//-------------------------------------> L L I U R E X <--------------------------------------
?>
