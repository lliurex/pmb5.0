<?php
//-------------------------------------> L L I U R E X <--------------------------------------//
// Modulo de importación/exportacion de usuarios de pmb, a partir de un fichero de texto plano
		
function string_to_array($string) {
   $largo = strlen($string); //Largo de cadena
   $final_array = array();
   for($i = 0; $i < $largo; $i++)  {
       $caracter = $string[$i];
       array_push($final_array,$caracter);
   }
   return $final_array;
}

//-----------Funcion para validar el fichero xml de alumnos y profesores ------------------------//
function validaFichero($archivo) {
	
	$doc = new DOMDocument();
	$doc->load( $archivo );
	
	$listaAlumnos = $doc->getElementsByTagName( "alumne" );
	if ($listaAlumnos->length==0){
		return 1;
		
	}else{
		$listaTagsAlu=$listaAlumnos->item(0);
		$nia=$listaTagsAlu->getElementsByTagName( "nia" );
		if ($nia->length==0){
			return 1;
		}
		$nom=$listaTagsAlu->getElementsByTagName( "nom" );
		if ($nom->length==0){
			return 1;
		}
		$cognoms=$listaTagsAlu->getElementsByTagName( "cognoms" );
		if ($cognoms->length==0){
			return 1;
		}
		$nif=$listaTagsAlu->getElementsByTagName( "nif" );
		if ($nif->length==0){
			return 1;
		}
		$numeroExpediente=$listaTagsAlu->getElementsByTagName( "numeroExpedient" );
		if ($numeroExpediente->length==0){
			return 1;
		}
	
	}
	$listaProfesores=$doc->getElementsByTagName("professor");
	
	if ($listaProfesores->length==0){
		return 1;
	
	}else{
		$listaTagsProfe=$listaProfesores->item(0);
		$nom=$listaTagsProfe->getElementsByTagName( "nom" );
		if ($nom->length==0){
			return 1;
		}
		$cognoms=$listaTagsProfe->getElementsByTagName( "cognoms" );
		if ($cognoms->length==0){
			return 1;
		}
		$nif=$listaTagsProfe->getElementsByTagName( "document" );
		if ($nif->length==0){	
			$nif=$listaTagsProfe->getElementsByTagName( "nif	" );
			if ($nif->length==0){
				return 1;
			}
		}
	}
	return 0;
	
}

function sacaCamposItacaAlu($archivo,$ide) {
	
	$vectorA = array();
	$indice =0;
	
	/*Instancio la clase DOM que nos permitira operar con el XML*/
	$doc = new DOMDocument();
 
	/* Cargo el XML, En este caso es un archivo llamado llxgesc.xml, podriamos usar loadXML si desamos leer de un string*/
	$doc->load( $archivo );
   
	// Obtengo el nodo alumne (listaAlumnos) del XML a traves del metodo getElementsByTagName, retorna una lista de todos los nodos encontrados 
	$listaAlumnos = $doc->getElementsByTagName( "alumne" );

    /*Al ser $listaAlumnos una lista de nodos   lo puedo recorrer y obtener todo  su contenido*/
	foreach( $listaAlumnos as $alumno )
  	{
		/* Obtengo el valor del primer elemento 'item(0)' de la lista $autors.  Si existiera un atributo en el nodo para obtenerlo usaria 
		   $authors->getAttribute('atributo');    */
		$nombres = $alumno->getElementsByTagName( "nom" ); // $authors = $book->getElementsByTagName( "author" );
   		$nombre = $nombres->item(0)->nodeValue;
   
		$apellidos = $alumno->getElementsByTagName( "cognoms" ); // $publishers = $book->getElementsByTagName( "publisher" );
		$apellido = $apellidos->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
   
		
		/* INI LLIUREX 24/09/2015 */
		//Si la tabla no esta vacia		
		if ($ide=="Exp"){
			$expedientes = $alumno->getElementsByTagName( "numeroExpedient" ); // $publishers = $book->getElementsByTagName( "publisher" );
			$expediente = $expedientes->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
			// Eliminamos la barra del numero de expediente
			if ($expediente !=""){
				$expediente = trim($expediente);
				// Para separar el numero de expediente y extraer sólo los números tenemos que eliminar separadores que pueden ser barra, punto, espacio o sin separador
				$cadena="";		
				$expediente = string_to_array($expediente);		
				if (count($expediente)>0) {
					for ($i=0; $i<count($expediente);$i++) {
						$digito = $expediente[$i];
						if ($digito >= "0" && $digito <="9")
							$cadena .= $digito;
					}		
				}
				$cadena=trim($cadena);	
				if (strlen($cadena)<4) 
 					$cadena = str_pad($cadena, 4, "0", STR_PAD_LEFT);
			}else{
				$expedientes = $alumno->getElementsByTagName( "nia" ); // $publishers = $book->getElementsByTagName( "publisher" );
				$expediente = $expedientes->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
				$cadena=$expediente;
			}
		}
		$nias = $alumno->getElementsByTagName( "nia" ); // $publishers = $book->getElementsByTagName( "publisher" );
		$nia = $nias->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
		
		
/*		$posicionBarra = strpos ($expediente, "/");	
		if (!$posicionBarra) $posicionBarra = strpos ($expediente, "-");	 
		
		$trozo1 = substr ( $expediente , 0, $posicionBarra );
		$trozo2 = substr ( $expediente , $posicionBarra+1, strlen($expediente)-$posicionBarra);
		$cadena = $trozo1 . $trozo2; */

				
		// Tenemos que añadir ceros delante hasta completar un minimo de 4 digitos en el numero de expediente 
		// con menos digitos el lector de CB no funciona

		if ($ide=="Exp"){
			// Inicializamos el vector
			for ($i=$indice;$i<$indice+25;$i++) 
				$vectorA[$i]= "";
			$vectorA[$indice] = $cadena;
			$vectorA[$indice+24]=$nia;
                        
		}else{
			
			// Inicializamos el vector
			for ($i=$indice;$i<$indice+24;$i++) 
				$vectorA[$i]= "";
		    $vectorA[$indice] = $nia;
                       						
		}
	
 
		//$vector[$indice] = $cadena;
		$vectorA[$indice+1] = utf8_decode(trim($apellido));	
		$vectorA[$indice+2] = utf8_decode(trim($nombre));		
		//$vector[$indice+17] = "va_ES";
		$indice=$i;	
				
	}
	$vectorA[$indice]="Campo vacio";
	return $vectorA;
}

function sacaCamposItacaProf($archivo) {

		
	$vectorP = array();
	$indice =0;
	
	/*Instancio la clase DOM que nos permitira operar con el XML*/
	$doc = new DOMDocument();
 
	/* Cargo el XML, En este caso es un archivo llamado llxgesc.xml, podriamos usar loadXML si desamos leer de un string*/
	$doc->load( $archivo );

	// Pasamos profesores
	$listaProfesores = $doc->getElementsByTagName( "professor" );

	foreach( $listaProfesores as $profesor )
  	{
		/* Obtengo el valor del primer elemento 'item(0)' de la lista $autors.  Si existiera un atributo en el nodo para obtenerlo usaria 
		$authors->getAttribute('atributo');    */
		$nombres = $profesor->getElementsByTagName( "nom" ); // $authors = $book->getElementsByTagName( "author" );
   		$nombre = $nombres->item(0)->nodeValue;
   
		$apellidos = $profesor->getElementsByTagName( "cognoms" ); // $publishers = $book->getElementsByTagName( "publisher" );
		$apellido = $apellidos->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
   
		$nifs = $profesor->getElementsByTagName( "document" ); // $publishers = $book->getElementsByTagName( "publisher" );

		// Si lo que se importa es un fichero GESCEN el campo nif a parsear es distinto
		if ($nifs->length == 0) $nifs = $profesor->getElementsByTagName( "nif" );


		$nif = $nifs->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
    		
		for ($i=$indice;$i<$indice+24;$i++) 
			$vectorP[$i]= "";
	   	


		$tam = strlen($nif);		
		$nif = substr(trim($nif), 1, $tam-2);
		if (strlen($nif)<4) $nif=str_pad($nif, 4, "0", STR_PAD_LEFT);  // Comprobamos tam min 4 digitos		
		$vectorP[$indice] = $nif;

					
		$vectorP[$indice+1] = utf8_decode(trim($apellido));	
		$vectorP[$indice+2] = utf8_decode(trim($nombre));		
		//$vector[$indice+17] = "va_ES";
		$indice=$i;	
	}
	
	$vectorP[$indice]="Campo vacio"; // Lo añadimos para hacerlo compatible con la importacion del fichero plano del GESCEN
	return $vectorP;
}

//Funcion para insertar los datos en la base de datos
function inserta_datos($vacia,$referencia,$tot,$campos,$link2,$idused,$lang,$tipo_user){
    
    $resul_comp=array();
	$correctorNIA=0;
	$correctorVacia=0;
	$num=0;
	$cont=0;
	$contAct=0;
	$categ=0;
        
   	if ($tipo_user=='A'){
		$categ=5; 
    }else{
        $categ=7;
    }	

    if (!$vacia==0){
		
	 	if ($idused=="Exp" && $tipo_user=="A"){
			$correctorVacia++;		 	
			$correctorNIA=24;
		}

		if ($idused=="NIA"&& $tipo_user=="A"){
			$correctorVacia++;
		}
		if ($tipo_user=="P"){
			$correctorNIA=23;
		}
		
			
	}else{
	  $correctorNIA=23;
	}	
    	
       
	while($num<$campos){
		if ($tot[$num] == " ") {
			$tot[$num] = NULL;
		}
                                      
		if(((($num+1)%$referencia)== 0) && ($num != 0)){//cada 24 debido a que hay 24 campos
		// Comprobamos si existe ya un alumno el mismo nombre y apellidos en la BD del PMB, si está actualizamos 
		// su datos personales si no los incorporamos al PMB
			$sql_comp= "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$num-$correctorNIA] . "' AND `empr`. `empr_nom` like '" . $tot[$num-(22+$correctorVacia)] . "' AND `empr`. `empr_prenom` like '" . $tot[$num-(21+$correctorVacia)] . "' )";
			$resul1= @mysql_query($sql_comp, $link2);
			$fecha= date('Y-m-d');
			$fecha_cad= date('Y-m-d', strtotime('+455 day'));
			
		//Se actualiza el usuario y password del usuario si vien en el fichero. Si no se utiliza el Número Expediente o NIA como usuario y password
			if (trim($tot[$num-9]) != "") {
				$user_a=addslashes($tot[$num-9]);
				if (trim($tot[$num-8]) != "") $pass_a=addslashes($tot[$num-8]);
				else $pass_a=$tot[$num-$correctorNIA];
			} else {
				$user_a=$tot[$num-$correctorNIA];
				$pass_a=$tot[$num-$correctorNIA];
				//echo "a".$user_a." ".$pass_a."a";
				//exit(0);
			}
			if (trim($tot[$num-3]) != "") $loca=intval(($tot[$num-3]));
			else $loca=1;

			//Si el alumno esta repetido se actualizan sus datos a excepción del empr_cb
				
			if (@mysql_num_rows($resul1) != 0) {
				$sql_user_cad="SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$num-$correctorNIA] . "' AND `empr`. `empr_nom` like '" . $tot[$num-(22+$correctorVacia)] . "' AND `empr`. `empr_prenom` like '" . $tot[$num-(21+$correctorVacia)] . "' AND  `empr`. `empr_date_expiration`< '" . $fecha . "')";
				$resul_cad= @mysql_query($sql_user_cad, $link2);
				
				//echo "$msg[usur_imp_b] <b>" . $tot[$num-23] . "</b><br>";
				$row1 = mysql_fetch_array($resul1);
				$requete = "UPDATE empr SET ";
				$requete .= "empr_nom='".fields_slashes($tot[$num-(22+$correctorVacia)])."',";
				$requete .= "empr_prenom='".fields_slashes($tot[$num-(21+$correctorVacia)])."',";
				$requete .= "empr_adr1='".fields_slashes($tot[$num-(20+$correctorVacia)])."',";
				$requete .= "empr_adr2='".fields_slashes($tot[$num-(19+$correctorVacia)])."',";
				$requete .= "empr_cp='".fields_slashes($tot[$num-(18+$correctorVacia)])."',";
				$requete .= "empr_ville='".fields_slashes($tot[$num-(17+$correctorVacia)])."',";
				$requete .= "empr_pays='".fields_slashes($tot[$num-(16+$correctorVacia)])."',";
				$requete .= "empr_mail='".fields_slashes($tot[$num-(15+$correctorVacia)])."',";
				$requete .= "empr_tel1='".fields_slashes($tot[$num-(14+$correctorVacia)])."',";
				$requete .= "empr_tel2='".fields_slashes($tot[$num-(13+$correctorVacia)])."',";
				$requete .= "empr_prof='".fields_slashes($tot[$num-(12+$correctorVacia)])."',";
				$requete .= "empr_year=".intval(($tot[$num-(11+$correctorVacia)])).",";
				if ($idused=="Exp" && $tipo_user=="A"){
					$requete .= "empr_NIA='".fields_slashes($tot[$num])."',";
					$requete .= "empr_Tipo='".$tipo_user."',";
				}
				
				if ($idused=="Exp" && $tipo_user=="P"){
					$requete .= "empr_Tipo='".$tipo_user."',";
				}
		
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
				$requete .= "empr_modif='".$fecha."',";
				if (@mysql_num_rows($resul_cad) != 0) {
					$requete .= "empr_date_expiration='".$fecha_cad."',";
				}
				$requete .= "empr_sexe=".intval(($tot[$num-(10+$correctorVacia)]))."";
				$requete .= " WHERE id_empr=".intval($row1['id_empr'])." ";
				$resul2 = @mysql_query($requete, $link2);
				$contAct++;

			}else{ 
				
				if ($tot[$num-$correctorVacia] == "") $tot[$num-$correctorVacia] = 1;
				if ($idused=="Exp"){
				        	
					if ($tipo_user=="A"){
						$sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat,empr_NIA,empr_Tipo) values ( '" . fields_slashes($tot[$num-$correctorNIA]) . "', '" . fields_slashes($tot[$num-(22+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(21+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(20+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(19+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(18+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(17+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(16+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(15+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(14+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(13+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(12+$correctorVacia)]) . "', " . intval(($tot[$num-(11+$correctorVacia)])) . ", " . intval(($tot[$num-(10+$correctorVacia)])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num-(7+$correctorVacia)]) . "', '" . $lang . "', '" . fields_slashes($tot[$num-(5+$correctorVacia)]) . "', '" . $tot[$num-(4+$correctorVacia)] . "', $loca, '" . $tot[$num-(2+$correctorVacia)] . "', '" . $tot[$num-(1+$correctorVacia)] . "', '" . $tot[$num-($correctorVacia)] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "','" .$categ . "', 2, '" . fields_slashes($tot[$num]) . "', '" .$tipo_user . "')";

					}

					if ($tipo_user=="P"){
						$sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat,empr_Tipo) values ( '" . fields_slashes($tot[$num-$correctorNIA]) . "', '" . fields_slashes($tot[$num-(22+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(21+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(20+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(19+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(18+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(17+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(16+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(15+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(14+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(13+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(12+$correctorVacia)]) . "', " . intval(($tot[$num-(11+$correctorVacia)])) . ", " . intval(($tot[$num-(10+$correctorVacia)])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num-(7+$correctorVacia)]) . "', '" . $lang . "', '" . fields_slashes($tot[$num-(5+$correctorVacia)]) . "', '" . $tot[$num-(4+$correctorVacia)] . "', $loca, '" . $tot[$num-(2+$correctorVacia)] . "', '" . $tot[$num-(1+$correctorVacia)] . "', '" . $tot[$num-($correctorVacia)] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "', '" .$categ . "', 2,'" .$tipo_user . "')";

					}	
				}else{
			
					$sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat) values ( '" . fields_slashes($tot[$num-$correctorNIA]) . "', '" . fields_slashes($tot[$num-(22+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(21+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(20+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(19+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(18+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(17+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(16+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(15+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(14+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(13+$correctorVacia)]) . "', '" . fields_slashes($tot[$num-(12+$correctorVacia)]) . "', " . intval(($tot[$num-(11+$correctorVacia)])) . ", " . intval(($tot[$num-(10+$correctorVacia)])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num-(7+$correctorVacia)]) . "', '" . $lang . "', '" . fields_slashes($tot[$num-(5+$correctorVacia)]) . "', '" . $tot[$num-(4+$correctorVacia)] . "', $loca, '" . $tot[$num-(2+$correctorVacia)] . "', '" . $tot[$num-(1+$correctorVacia)] . "', '" . $tot[$num-($correctorVacia)] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "', '" .$categ . "', 2 )";
				}
			
				$resul2 = @mysql_query($sql, $link2);
				$cont++;
			}		
		}
		
		$num++;
	}	// Fin del While

	$resul_comp[0]=$num;
	$resul_comp[1]=$cont;
	$resul_comp[2]=$contAct;
    $resul_comp[3]=$fecha;
	$resul_comp[4]=$fecha_cad;	
	return $resul_comp;	

}


//Funcion para comprobar que tipo de identificador (Número de Expediente o NIA) se esta usando
function identificador_usado($tot,$campos,$link2){
	$idUsado=0;
	$usanExp=0;
	$j=0;
	$k=0;
	
	
//Comprobamos si en la base de datos estan usando el número de Expediente
	while ($j<$campos){
    	if(((($j+1)%25)== 0) && ($j != 0)){	
			$sql_comp_exp= "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$j-24] . "')";
			$resul_exp= @mysql_query($sql_comp_exp, $link2);
			if(@mysql_num_rows($resul_exp)!=0){
				$usanExp++;
			} 
		}
		$j++;
	}
        
	$usanNia=0;

//Comprobamos si en la base de datos estan usando el NIA	
	while ($k<$campos){
    	if(((($k+1)%25)== 0) && ($k != 0)){	
			$sql_comp_nia= "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$k] . "')";
			$resul_nia= @mysql_query($sql_comp_nia, $link2);
			if(@mysql_num_rows($resul_nia)!=0){
				$usanNia++;
				
			} 
		}
   		$k++;
	}
   	
    
	if ($usanExp>$usanNia){
		$idUsado="Exp";
	}else{
		$idUsado="NIA";
	}
	
	return $idUsado;

}

//Funcion para comprobar si es posible lanzar la migración

function comprueba_migracion($link2){
	global $msg;


	//1.Comprobamos si ya se ha realizado un migración anteriormente
    	$name_cb='empr_cb_old';
    	$sql_idcb_old="SELECT idchamp from empr_custom where name='" .$name_cb. "'";
   		$id_cb_old=@mysql_query($sql_idcb_old, $link2);
    	$valor=mysql_fetch_array($id_cb_old);
    
    	$sql_comprobacion="SELECT * from empr_custom_values where empr_custom_champ='" .$valor['idchamp']. "'";
    	$existen_registros=@mysql_num_rows(@mysql_query($sql_comprobacion,$link2));
	
	
	if ($existen_registros==0){

		// 2. Comprobamos si exisge el campo empr_NIA en la tabla empr

		$sql_NIA="SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_NIA'";
		$existeNIA=@mysql_query($sql_NIA, $link2);
					
		if (@mysql_num_rows($existeNIA)>0){
			$sql_alu_nia="SELECT count(empr_cb) FROM empr WHERE empr_Tipo='A' AND NOT ISNULL(empr_NIA)";
			$alu_con_nia=mysql_fetch_row(@mysql_query($sql_alu_nia, $link2));
	
			//$sql_alu_sinia="SELECT empr_cb FROM empr WHERE empr_Tipo<>'P' AND ISNULL(empr_NIA) AND (YEAR(UTC_DATE())-YEAR(empr_modif))<2";
			$sql_alu_sinnia="SELECT count(*) from empr where (ISNULL(empr_NIA) or empr_NIA='') and ISNULL(empr_Tipo) and (YEAR(UTC_DATE())-YEAR(empr_date_expiration))<2";		
		    $alu_sin_nia=mysql_fetch_row(@mysql_query($sql_alu_sinnia, $link2));
	
		//2. Comprobamos que la mayoria de los alumnos dispone del nia
		
			if ($alu_con_nia[0]>$alu_sin_nia[0]){
		  //echo "<h3>$msg[usur_migr_a]</h3><br>$msg[usur_migr_b]</br><UI><ul><br>";
		  		//echo "<h3><center>PROCESO DE MIGRACIÓN DEL ID DE LOS ALUMNOS AL NIA DISPONIBLE</center></h3><div><b>Mediante este proceso podrá sustituir el Número de Expediente usado como id de los alumnos por el NIA</b><b><UL><li>Total alumnos con NIA: $alu_con_nia</li><li>Total de alumnos sin nia: $alu_sin_nia</li><UL></b></div><div><b><center>Para iniciar el proceso de migración haga clic aqui</b></center></div>";
				  echo "<h3><center><a href=./admin.php?categ=empr&sub=migration>$msg[usur_migr_a]</a></center></h3>";		
		
			}
	
		}
	}
}

/* FIN LLIUREX 24/09/2015 */

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

                
		// Se admiten ficheros xml y .dat
		/* INICIO LLIUREX 24/09/2015 */
		if (!strcmp($tipo, "text/xml") || !strcmp($tipo, "application/octet-stream")){
			require("$base_path/includes/db_param.inc.php");
			$link2 = @mysql_connect(SQL_SERVER, USER_NAME, USER_PASS) OR die("Error MySQL");
			// Comprobamos si la tabla de usuarios (empr) esta vacia
			$sql_vacia="SELECT * FROM empr";
			$vacia=@mysql_num_rows(@mysql_query($sql_vacia, $link2));

			if (move_uploaded_file($_FILES['fich']['tmp_name'], $nomfich)){ //el POsT devuelve el nombre de archivo en el servidor y el segundo campo es a donde se va a mover. 
				$valida=validaFichero($nomfich);
				if  ($valida==1){
					echo "<b><center> ". $nomfich ." ". $msg["xml_incorrect"]."</center></b>";
					break;
				}else{
					
					if ($vacia==0){
						$tipo="NIA";
						$referencia=24; //Si la tabla esta vacia el número  máximo de campos será 24
					}else{
						$tipo="Exp";
						$referencia=25; //Si la tabla esta vacia el número  máximo de campos será 25
				
					}				
			
					$totAlu = sacaCamposItacaAlu($nomfich,$tipo);
					$totProf= sacaCamposItacaProf($nomfich);
				}	
					
			}
			/* FIN LLIUREX 24/09/2015 */
		    
			$camposAlu=(count($totAlu))-1; //total de campos, se le resta 1 debido a que coge un campo mas (vacio)
				
			if (($camposAlu%$referencia) != 0){			
				exit("Campos $camposAlu");	//Se muestra mensaje advirtiendo que el fichero no tiene la estructura correcta
				exit("<b><center>$msg[usur_imp_a]</center></b>");
			}
	       
			/* INICIO LLIUREX 24/09/2015 */
			/*	while($num<$campos){
					if ($tot[$num] == " ") {
						$tot[$num] = NULL;
					}
					      
					if(((($num+1)%$referencia)== 0) && ($num != 0)){//cada 24 debido a que hay 24 campos
				// Comprobamos si existe ya un alumno el mismo nombre y apellidos en la BD del PMB, si está actualizamos 
				// su datos personales si no los incorporamos al PMB
						
						$sql_comp= "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$num-23] . "' AND `empr`. `empr_nom` like '" . $tot[$num-22] . "' AND `empr`. `empr_prenom` like '" . $tot[$num-21] . "' )";

						$resul1= @mysql_query($sql_comp, $link2);
						$fecha= date('Y-m-d');
						$fecha_cad= date('Y-m-d', strtotime('+1 year'));
				
							//Se actualiza el usuario y password del usuario si vien en el fichero. Si no se utiliza el Número Expediente o NIA como usuario y password
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

						//Si el alumno esta repetido se actualizan sus datos a excepción del empr_cb
						if (@mysql_num_rows($resul1) != 0) {
					//echo "$msg[usur_imp_b] <b>" . $tot[$num-23] . "</b><br>";
							echo ("alumno repetido");
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
								echo ("Alumno nuevo en tabla vacia");
								
								if ($tot[$num] == "") $tot[$num] = 1;
				$sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat) values ( '" . fields_slashes($tot[$num-23]) . "', '" . fields_slashes($tot[$num-22]) . "', '" . fields_slashes($tot[$num-21]) . "', '" . fields_slashes($tot[$num-20]) . "', '" . fields_slashes($tot[$num-19]) . "', '" . fields_slashes($tot[$num-18]) . "', '" . fields_slashes($tot[$num-17]) . "', '" . fields_slashes($tot[$num-16]) . "', '" . fields_slashes($tot[$num-15]) . "', '" . fields_slashes($tot[$num-14]) . "', '" . fields_slashes($tot[$num-13]) . "', '" . fields_slashes($tot[$num-12]) . "', " . intval(($tot[$num-11])) . ", " . intval(($tot[$num-10])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num-7]) . "', '" . $lang . "', '" . fields_slashes($tot[$num-5]) . "', '" . $tot[$num-4] . "', $loca, '" . $tot[$num-2] . "', '" . $tot[$num-1] . "', '" . $tot[$num] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "', 7, 2 )";
				$resul2 = @mysql_query($sql, $link2);
				$cont++;
							}		
						}
						$num++;
					}	// Fin del While */

				   
	    
		    //Si la tabla esta vacia se utilizará el NIA como identificador 
		    $tipo_user=0; 
		    $existeNIA=0;	 
		    if ($vacia!=0){
			     $idused=identificador_usado($totAlu,$camposAlu,$link2);
				
			    if ($idused=="Exp"){ 	
					$sql="SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_NIA'";
					$existeNIA=@mysql_query($sql, $link2);
					//Si el campo empr_NIA no existe se crea
					if (@mysql_num_rows($existeNIA)==0){
						$sql="ALTER TABLE empr ADD empr_NIA varchar(15)";
						$insert=@mysql_query($sql, $link2);
					}
				//Comprobamos si existe el campo empr_Tipo para distinguir entre alumnos y profesores
					$sql="SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_Tipo'";
					$existeTipo=@mysql_query($sql, $link2);
					//Si el campo empr_Tipo no existe se crea
					if (@mysql_num_rows($existeTipo)==0){
						$sql="ALTER TABLE empr ADD empr_Tipo varchar(1)";
						$insert=@mysql_query($sql, $link2);
					}
					       
			    }
				$tipo_user="A";
		    }			
			//Importamos datos alumnos
			$resul_comp=inserta_datos($vacia,$referencia,$totAlu,$camposAlu,$link2,$idused,$lang,$tipo_user);
				
			//Importamos datos profesores;

			$camposProf=(count($totProf))-1;
			$tipo_user="P";
			$resul_prof=inserta_datos($vacia,24,$totProf,$camposProf,$link2,$idused,$lang,$tipo_user);			 		
			$contR=$resul_comp[1]+$resul_prof[1];
			$contAct=$resul_comp[2]+$resul_prof[2];
							   
		    //Se muestra mensaje indicado el número de registros importados
			echo "<b>$msg[usur_imp_c] </b>";
			if ($contR>0){
				echo "<b>$msg[usur_imp_d]  " .$contR . "</b>";
			}
			if ($contAct>0){
				echo " <b>$msg[usur_imp_q]  " .$contAct . "</b>";
			}
			$migracion=comprueba_migracion($link2);

			if ($resul_comp[0]>0){ //comparativa campos con los valores a insertar. Se muestra a modo de ejemplo el primer registro importado
				$fecha=$resul_comp[3];
				$fecha_cad=$resul_comp[4];
				echo "<h3>$msg[usur_imp_e]&nbsp;&nbsp;&nbsp;</h3><div class='form-contenu'><table width='98%' border='0' cellspacing='10'><td class='jauge'><b>$msg[usur_imp_f] <br>$msg[usur_imp_g]</b></td><td class='jauge' width='27%'><center><b>$msg[usur_imp_h] <br>$msg[usur_imp_i]</b></center></td><td class='jauge' width='60%'><b>$msg[usur_imp_j]<br>$msg[usur_imp_k]</b></td><tr><td class='nobrd'><font color='#FF0000'>id_empr</font></td><td class='nobrd'><center><input name='id_empr' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem0' value='' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_cb</td><td class='nobrd'><center><input name='empr_cb' value='1' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem1' value='" . $totAlu[0] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_nom</td><td class='nobrd'><center><input name='empr_nom' value='2' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem2' value='" . $totAlu[1] . "'  type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_prenom</td><td class='nobrd'><center><input name='empr_prenom' value='3' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem3' value='" . $totAlu[2] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_adr1</td><td class='nobrd'><center><input name='empr_adr1' value='4' type='text' size='1' disabled><td class='nobrd'><input name='exem5' value='" . $totAlu[3] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_adr2</td><td class='nobrd'><center><input name='empr_adr2' value='5' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem6' value='" . $totAlu[4] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_cp</td><td class='nobrd'><center><input name='empr_cp' value='6' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem7' value='" . $totAlu[5] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_ville</td><td class='nobrd'><center><input name='empr_ville' value='7' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem8' value='" . $totAlu[6] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_pays</td><td class='nobrd'><center><input name='empr_pays' value='8' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem9' value='" . $totAlu[7] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_mail</td><td class='nobrd'><center><input name='empr_mail' value='9' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem10' value='" . $totAlu[8] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_tel1</td><td class='nobrd'><center><input name='empr_tel1' value='10' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem11' value='" . $totAlu[9] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_tel2</td><td class='nobrd'><center><input name='empr_tel2' value='11' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem12' value='" . $totAlu[10] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_prof</td><td class='nobrd'><center><input name='empr_prof' value='12' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem13' value='" . $totalu[11] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_year</td><td class='nobrd'><center><input name='empr_year' value='13' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem14' value='" . $totAlu[12] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_categ</font></td><td class='nobrd'><center><input name='empr_categ' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem15' value='7' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_codestat</font></td><td class='nobrd'><center><input name='empr_codestat' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem16' value='2' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_creation</font></td><td class='nobrd'><center><input name='empr_creation' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem17' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_modif</font></td><td class='nobrd'><center><input name='empr_modif' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem18' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_sexe</td><td class='nobrd'><center><input name='empr_sexe' value='14' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem19' value='" . $totAlu[13] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_login</td><td class='nobrd'><center><input name='empr_login' value='15' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem20' value='" . $totAlu[14] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_password</td><td class='nobrd'><center><input name='empr_password' value='16' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem21' value='" . $totAlu[15] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_date_adhesion</font></td><td class='nobrd'><center><input name='empr_date_adhesion' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem22' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_date_expiration</font></td><td class='nobrd'><center><input name='empr_date_expiration' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem23' value='" . $fecha_cad . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_msg</td><td class='nobrd'><center><input name='empr_msg' value='17' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem24' value='" . $totAlu[16] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_lang</td><td class='nobrd'><center><input name='empr_lang' value='18' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem25' value='" . $totAlu[17] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_ldap</font></td><td class='nobrd'><center><input name='empr_ldap' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem26' value='' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>type_abt</td><td class='nobrd'><center><input name='type_abt' value='19' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem27' value='" . $totAlu[18] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>last_loan_date</td><td class='nobrd'><center><input name='last_loan_date' value='20' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem28' value='" . $totAlu[19] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_location</td><td class='nobrd'><center><input name='empr_location' value='21' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem29' value='" . $totAlu[20] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>date_fin_blocage</td><td class='nobrd'><center><input name='date_fin_blocage' value='22' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem30' value='" . $totAlu[21] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>total_loans</td><td class='nobrd'><center><input name='total_loans' value='23' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem31' value='" . $totAlu[22] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_statut</td><td class='nobrd'><center><input name='empr_statut' value='24' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem32' value='" . $totAlu[23] . "' type='text' disabled size='40'></td></tr></table>";
		/* FIN LLIUREX 24/09/2015 */
				fclose($archivo);
				unlink($nomfich);
				break;
			}
				
	}else { //Si el fichero no es xml o .dat se muestra mensaje de advertencia
		echo "<b><center> ". $nomfich ." ". $msg["usur_imp_l"]."</center></b>";
		
	}
	break;
}

default:

// Formulario para elegir fichero a importar de itaca
echo "<form class='form-admin' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=empr&sub=itaca&action=?&categor=import\"><h3>$msg[import_usu_from_itaca_a]</h3><div class='form-contenu'><div class='row'><div class='colonne60'><label class='etiquette' for='form_import_lec'>$msg[importa_d]&nbsp;</label><input name='fich' accept='text/plain, .xml, .dat' type='file'  size='40'></div><input type='button' name='fichero' value='Continuar' onclick='form.submit()'></div></form>";

break;

}
//-------------------------------------> L L I U R E X <--------------------------------------//

?>

