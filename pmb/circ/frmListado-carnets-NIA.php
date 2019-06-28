<?php


// Modulo para migrar los alumnos al NIA //

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");



$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
require_once ("$base_path/includes/init.inc.php");  

include("$include_path/messages/help/$lang/carnets_Migrados.txt");

global $dbh;

$sql_migracion="SELECT * from empr where empr_Migrado='S'";
$resultado_migracion=@mysql_query($sql_migracion, $dbh);

if (@mysql_num_rows($resultado_migracion)>0){

        
 	$sql_alumnos_migrados="SELECT empr_cb from empr where empr_Migrado='S'";
	$alumnos_migrados=@mysql_query($sql_alumnos_migrados, $dbh);
	$total_migrados=@mysql_num_rows($alumnos_migrados);
        $codigos=array();

         
        for ($i=0;$i<$total_migrados;$i++){
             $row =mysql_fetch_array($alumnos_migrados);
	     $codigos[$i]=$row['empr_cb'];
                
	}
	
        $codigosJS=json_encode($codigos); 

        print "<center><INPUT type='button' name='imprimercarte' class='bouton' value='$msg[genera_carnets_NIA]' onclick='valida_formulario(".$codigosJS.")'/></center>";


} else{
        print "<h3><center>$msg[carnetsNIA_nodispo]</center></h3>";
       
	
}

?>

<SCRIPT type="text/javaScript">
function valida_formulario(codigos)
{
     var url="";
 
    // Abrimos una ventana con los carnets
    url = './pdf.php?pdfdoc=listadoCarnets&empr_cb=' + codigos;
    window.open(url, 'print_PDF', 'toolbar=no, dependent=yes, width=600, height=500, resizable=yes');
    
    
}

</SCRIPT>





