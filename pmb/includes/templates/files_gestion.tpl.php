<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: files_gestion.tpl.php,v 1.2 2015-12-21 14:44:13 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$files_gestion_list_tpl="
<script>
var flag_mouseover_info_div = false;
		
function show_div_img(event,img) {
	if (flag_mouseover_info_div == true) {
		return true;
	}	
	
	flag_mouseover_info_div = true;
	var pos=getCoordinate(event);
	posxdown=pos[0];
	posydown=pos[1];
	var pannel=document.createElement('div');
	pannel.setAttribute('id','div_img');
	pannel.style.top=(posydown-50)+'px';
	pannel.style.left=posxdown+'px';
	pannel.style.border='#000000 solid 1px';
	pannel.style.position='absolute';
	pannel.style.background='#FFFFFF';
	
	pannel.style.zIndex=1500;
	document.body.appendChild(pannel);
	pannel.innerHTML='<img src=\"' + img + '\" alt=\"\" />';
	
	return true;
}
		
function hide_div_img() {
	var pannel=document.getElementById('div_img');
	if (pannel) {
		pannel.parentNode.removeChild(pannel);
		flag_mouseover_info_div = false;
	}
}
</script>	
<h1>".htmlentities($msg["admin_files_gestion_title"], ENT_QUOTES, $charset)."</h1>			
<form class='form-".$current_module."' name='files_gestion_form'  method='post' action=\"!!post_url!!\"  enctype='multipart/form-data'>	
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='filename' id='filename' />
	<table>
		<tr>			
			<th>	".htmlentities($msg["admin_files_gestion_name"], ENT_QUOTES, $charset)."			
			</th> 				
			<th>				
			</th> 				
			<th>				
			</th> 			 			
		</tr>						
		!!list!!			
	</table> 				
	<input class='saisie-80em' type='file' name='select_file'>
	<input type='button' class='bouton' name='add_file' value='".htmlentities($msg["admin_files_gestion_add"], ENT_QUOTES, $charset)."' 
		onclick=\" document.getElementById('action').value='upload'; this.form.submit();\" />	
</form>
";

$files_gestion_list_line_tpl="
<tr  class='!!odd_even!!' style=\"cursor: pointer\" 
onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td valign='top'>				
		!!name!!
	</td> 		
	<td valign='top'>				
		!!vignette!!
	</td> 	
	<td valign='top'>	
		<input type='button' class='bouton' name='del_file_button' value='X' 
			onclick=\" document.getElementById('action').value='delete'; document.getElementById('filename').value='!!name!!';this.form.submit(); \" />			
	</td> 	
	
</tr> 	
";

