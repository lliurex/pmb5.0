<?php


// Modulo para migrar los alumnos al NIA //

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");



$base_path=".";                            
$base_auth = "ADMINISTRATION_AUTH";  
$base_title = "\$msg[7]";    
require_once ("$base_path/includes/init.inc.php");  

include("$include_path/messages/help/$lang/migracion_NIA.txt");

function migracion_nia($dbh){
  global $msg;

 // 3. Creamos si es necesario crear (o ya esta creado) el campo empr_cb_old en la tabla empr_custom
	$name_cb='empr_cb_old';	
	$sql_cb="SELECT idchamp from empr_custom WHERE name='" .$name_cb. "'";
	$existe_cb_old=@mysql_query($sql_cb, $dbh);
	
	if (@mysql_num_rows($existe_cb_old)==0){	
		$etiqueta='Codi anterior al NIA';
		$type='text';
		$datatype='small_text';
		$options='<OPTIONS FOR="text"><SIZE>15</SIZE><MAXSIZE>15</MAXSIZE> <REPEATABLE>0</REPEATABLE> <ISHTML>0</ISHTML></OPTIONS>';
		$sql_cb_old="INSERT INTO empr_custom(name,titre,type,datatype,options) values('" .$name_cb. "','" .$etiqueta. "','" .$type. "','" .$datatype. "','" .$options. "')"; 
		$nuevo_campo=@mysql_query($sql_cb_old, $dbh);
	}

  //4. Creamos si es necesario crear el campo empr_migrado en la tabla empr	

	$sql="SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_Migrado'";
	$existeMigrado=@mysql_query($sql, $dbh);
	//Si el campo empr_Tipo no existe se crea
	if (@mysql_num_rows($existeMigrado)==0){
		$sql="ALTER TABLE empr ADD empr_Migrado varchar(1)";
		$insert=@mysql_query($sql, $dbh);
			
	}	
	
// 5. Copiamos el campo empr_cb en la tabla empr_custom_values y cambiamos el campo empr_cb por empr_NIA
	$sql_alumnos_migrar="SELECT id_empr,empr_cb, empr_NIA from empr where NOT ISNULL(empr_NIA) and empr_cb!=empr_NIA";
	$alumnos_migrar=@mysql_query($sql_alumnos_migrar, $dbh);
	$total_migrar=@mysql_num_rows($alumnos_migrar);
        $codigos=array();
       
		
	$sql_idcb_old="SELECT idchamp from empr_custom where name='" .$name_cb. "'";
	$id_cb_old=@mysql_query($sql_idcb_old, $dbh);
	$row2 =mysql_fetch_array($id_cb_old);
        $j=0;
	for ($i=0;$i<$total_migrar;$i++){
		$row1 = mysql_fetch_array($alumnos_migrar);
		$idempr=$row1['id_empr'];
		$old_cb=$row1['empr_cb'];
                $sql_comprobacion="SELECT * from empr_custom_values where empr_custom_champ='" .$row2['idchamp']. "'and empr_custom_origine='" .$idempr. "'";
                $existe_registro=@mysql_num_rows(@mysql_query($sql_comprobacion,$dbh));
                if ($existe_registro==0){
                       	$sql_repetidos="SELECT * from empr where empr_NIA='" .$row1['empr_NIA']. "'  and empr_Migrado='S'";
                        $registro_duplicado=@mysql_num_rows(@mysql_query($sql_repetidos,$dbh));
						if ($registro_duplicado==0){
							$sql_insert_cb="INSERT INTO empr_custom_values(empr_custom_champ,empr_custom_origine,empr_custom_small_text) values('" .$row2['idchamp']. "','" .$idempr. "', '" .$old_cb. "')";
							$insertcb=@mysql_query($sql_insert_cb, $dbh);
							$idempr=$row1['id_empr'];
							$cb_nia=$row1['empr_NIA'];
							$sql_update="UPDATE empr SET empr_cb='" .$cb_nia ."', empr_Migrado='S' WHERE id_empr='" .$idempr. "'";
							$resulmig = @mysql_query($sql_update, $dbh);
                        	$codigos[$j]=$cb_nia;
                        	$j++;
						}else{
							$idempr=$row1['id_empr'];
							$sql_update="UPDATE empr SET empr_Migrado='N' WHERE id_empr='" .$idempr. "'";
							$resulmig = @mysql_query($sql_update, $dbh);


						}
                        
                            
		}
	}
	$codigosJS=json_encode($codigos);  
        print "<h3><center>$msg[migr_ejecutada]</center></h3>";
        //print "<INPUT type='button' name='imprimercarte' class='bouton' value='$msg[genera_carnets_NIA]' onclick='valida_formulario(".$codigosJS.")'/>";
	print '<form id="form1" name="form1" method="post" action="circ.php?categ=carnetsUsuariosMigrados"/>';
	print "<div>
      		<input class='bouton' type='submit' value=' $msg[genera_carnets_NIA]'/>
		</div>
      		</form>";

} 

function show_data($dbh){
    global $msg;

  //1.Comprobamos si ya se ha realizado un migración anteriormente
    $name_cb='empr_cb_old';
    $sql_idcb_old="SELECT idchamp from empr_custom where name='" .$name_cb. "'";
    $id_cb_old=@mysql_query($sql_idcb_old, $dbh);
    $valor=mysql_fetch_array($id_cb_old);
    
    $sql_comprobacion="SELECT * from empr_custom_values where empr_custom_champ='" .$valor['idchamp']. "'";
    $existen_registros=@mysql_num_rows(@mysql_query($sql_comprobacion,$dbh));
   
  
    if ($existen_registros==0){	

    	$sql_NIA="SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_NIA'";
    	$existeNIA=@mysql_query($sql_NIA, $dbh);
    
    	if (@mysql_num_rows($existeNIA)>0){  	
	  
         // Vemos cuantos usuarios disponen de NIA y cuantos no       
		$sql_nia="SELECT count(distinct empr_NIA) from empr";
		$total_nia=mysql_fetch_row(@mysql_query($sql_nia, $dbh));
	
		$sql_sinnia="SELECT count(*) from empr where (ISNULL(empr_NIA) or empr_NIA='') and ISNULL(empr_Tipo) and (YEAR(UTC_DATE())-YEAR(empr_date_expiration))<2";
		$total_sinnia=mysql_fetch_row(@mysql_query($sql_sinnia,$dbh));	 
	
         
	//2. Comprobamos que la mayoria de los alumnos dispone del nia
		
		if ($total_nia[0] > $total_sinnia[0]){
						
		//Total de registros de la tabla usuario
			$sql_total="SELECT count(*) from empr";
			$total_users=mysql_fetch_row(@mysql_query($sql_total, $dbh));
		//Numero de registros de alumnos ya importados con NIA (por no tener número de expediente)
			$sql_connia="SELECT count(distinct empr_NIA) from empr where empr_cb=empr_NIA";
            $total_connia=mysql_fetch_row(@mysql_query($sql_connia, $dbh));

			//$sql_duplicados="SELECT count(*) from empr group by empr_NIA having count(*)>1";
	
			//$sql_sinmigrar="SELECT count(*) from empr where (ISNULL(empr_NIA) or empr_NIA='')";
			//$total_sinmigrar=mysql_fetch_row(@mysql_query($sql_sinmigrar,$dbh));	

 		//Numero de registros a migrar (utilizan el número de expediente pero tenemos su NIA)
            $sql_migrar="SELECT count(distinct empr_NIA) from empr where empr_cb!=empr_NIA";
            $total_migrar=mysql_fetch_row(@mysql_query($sql_migrar, $dbh));
  
        //Numero de registros que no se migraran         
            $total_sinmigrar="$total_users[0]"-"$total_nia[0]";
              
	        	print "
				<style>
					table, th, td {
   			 			border: 0px solid black;
					}
				</style>

				<table border=0 style=width:50%>
					<tr>
						<td border=0 width='15%'><left><b>" .$msg['migr_total_registros']. "</b></left></td>
						<td border=0 width='10%'><left><input name='Total registros' value='" . $total_users[0] . "' type='text' size='6' disabled></left></td>			
					</tr>

					<tr>
						<td border=0 width='15%'><left><b>" .$msg['migr_total_connia']. "</b></left></td>
						<td border=0 width='10%'><left><input name='Total registros con NIA' value='" . $total_connia[0] . "' type='text' size='6' disabled></left></td>
						<td border=0 width='30%'><left><b>$msg[desc_registros_connia]</b></left></td>				
					</tr>

					<tr>
						<td border=0 width='15%'><left><b>" .$msg['migr_total_nia']. "</b></left></td>
						<td border=0 width='10%'><left><input name='Total registros a migrar' value='" . $total_migrar[0] . "' type='text' size='6' disabled></left></td>	
						<td border=0 width='30%'><left><b>$msg[desc_registros_migrar]</b></left></td>		
					</tr>
					<tr>
						<td border=0 width='15%'><left><b>" .$msg['migr_total_simigrar']. "<b></left></td>
						<td border=0 width='10%'><left><input name='Total registros sin NIA' value='" . $total_sinmigrar . "' type='text' size='6' disabled></left></td>
						<td border=0 width='30%'><left><b><a href=./edit.php?categ=empr&sub=no_migrados>$msg[consulta_informe_no_migrados]</b></left></td>			
					</tr>
				</table>";
			print "
			<input class='bouton' type='button' value=' $msg[migr_exe] ' onClick=\"document.location='./admin.php?categ=empr&sub=migration&action=migrar'\" />";
		}else{
	
			print "<h3><center>$msg[migr_noactivada]</center></h3>";
		}
	
  	}else{	
				
		print "<h3><center>$msg[migr_noactivada]</center></h3>";
	}

 }else{
	print "<h3><center>$msg[migr_noactivada]</center></h3>";
}

}


switch($action){

	case 'migrar':
	 	migracion_nia($dbh);		
	break;
	default:
	      	show_data($dbh);
	break;	

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


