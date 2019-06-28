<?php
	require_once ("$base_path/includes/init.inc.php");
	/*$pathInclude="../includes/init.inc.php");
	require_once ($pathInclude); */

?>
<HTML>
<HEAD>
<TITLE>Formulario de impresión de carnets</TITLE>
<META http-equiv="Content-Type" content="text/html; charset=utf-8" />
<SCRIPT LANGUAGE="JavaScript">
function valida_formulario()
{
    var codigos = document.forms['frmCodigos'].elements['codigos'].value;
    var sw = "", url="";

    // No se pueden dejar en blanco ni el nick ni el password y ambos no pueden ser iguales.
    if (isEmpty(codigos))
        //sw="Introduzca el/los codigo/s de barras del usuario/s.\n";
	sw="<?php echo $msg[cataleg_Document_Carnet_ErrorMsg]; ?>";
    // Si la validación es correcta se llamará al método submit del formulario.
    if (sw.toString().length>0)
        setMensajeAviso(sw, "red");
    else {
        // Abrimos una ventana con los carnets
	url = './pdf.php?pdfdoc=listadoCarnets&empr_cb=' + codigos;
	window.open(url, 'print_PDF', 'toolbar=no, dependent=yes, width=600, height=500, resizable=yes');
    }
}

// -------------------------------------------------------------
// Funcion que muetra un mensaje de aviso incrustado en un div con DOM
function setMensajeAviso(sw, pcolor) {
        // Obtener el div de pagina del formulario con DOM con getElementById
        var divPagina = document.getElementById("divRegistro"); // Puntero a div "pagina" del formulario

        // Introducir al final el un div nuevo con el mensaje del alert antiguo
        var etiqueta = document.createElement("div"); // Nos definimos etiqueta div (Element)
        etiqueta.setAttribute("id", "aviso");
        var texto = document.createTextNode(sw); // Nos definimos el texto del div (Text)
        etiqueta.appendChild(texto); // añadimos dentro del Element el Text       

        // Aplicarle un estilo
        //var color = getStyle(divPagina, "color");
        etiqueta.style.color = pcolor;

        // Introducimos este nuevo item en la pagina dentro del div pagina
        // Borrar previamente el div aviso si esta creado
        var divAviso = document.getElementById("aviso");
        if (divAviso!=null)
            divPagina.replaceChild(etiqueta, divAviso); //r removeChild(divAviso);
        else
            divPagina.appendChild(etiqueta);
        return true;
}

// Funcion que comprueba si un String esta o no vacio
function isEmpty(paux) {
    if (paux.toString().length>0)
        return false;
    else
        return true;
}

// -------------------------------------------------------------

</SCRIPT>
</HEAD>
<BODY>

<FORM class='form-admin' name='frmCodigos' ENCTYPE="multipart/form-data" ACTION="JavaScript:valida_formulario();">
<CENTER>
<?php echo "<b>$msg[cataleg_Document_Carnet_msg]&nbsp;</b>";		?>
<INPUT name='codigos' accept='text/plain' type='text'  size='80'>
<DIV id="divRegistro">
<INPUT type="button" name="imprimercarte" class="bouton" value="Generar" onclick="form.submit()">
</CENTER>
</FORM>
</DIV>
</BODY>
</HTML>





