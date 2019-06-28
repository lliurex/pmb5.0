<?php 
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area.tpl.php,v 1.7.2.1 2017-09-14 14:45:28 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$contribution_area_list_tpl="
<table>
	<tr>
		<th>".htmlentities($msg['67'], ENT_QUOTES, $charset)."</th>
		<th>".htmlentities($msg['contribution_area_status'], ENT_QUOTES, $charset)."</th>
		<th></th>
	</tr>
	!!list!!
</table>";

$contribution_area_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">
	<td style='cursor: pointer' onmousedown=\"document.location='./admin.php?categ=contribution_area&sub=area&action=define&id=!!id!!';\" >
		<div class='area_color' style='width:10px; height:10px; background-color:!!area_color!!; display:inline-block;'></div>
		!!area_title!!
	</td>
	<td style='cursor: pointer' onmousedown=\"document.location='./admin.php?categ=contribution_area&sub=area&action=define&id=!!id!!';\" >
		!!area_status!!
	</td>
	<td>
		<input type='button' class='bouton' value='".$msg['contribution_area_params']."' onclick=\"document.location='./admin.php?categ=contribution_area&sub=area&action=edit&id=!!id!!';\"/>
	</td>
</tr>
";

$contribution_area_add_button = "
	<div class='row'>
		<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_nomenclature_voice_add"], ENT_QUOTES, $charset)."'
		onclick=\"document.location='./admin.php?categ=contribution_area&sub=area&action=edit'\" />
	</div>
";

$contribution_area_delete_button = "<input type='button' class='bouton' name='delete_button' value='".htmlentities($msg["admin_nomenclature_voice_form_del"], ENT_QUOTES, $charset)."' onclick=\"document.location='./admin.php?categ=contribution_area&sub=area&action=delete&id=!!id!!'\" />";

$contribution_area_form = 
"<form class='form-".$current_module."' id='contribution_area' name='contribution_area'  method='post' action=\"admin.php?categ=contribution_area&sub=area&action=save\" >
	<h3>!!msg_title!!</h3>	
	<input type='hidden' name='action' id='action' />
	<input type='hidden' name='id' id='id' value='!!id!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='area_title'>".$msg['contribution_area_title']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='area_title' id='area_title' value='!!area_title!!' />
		</div>
		<div class='row'>
			<label class='etiquette' for='area_comment'>".$msg['contribution_area_comment']."</label>
		</div>
		<div class='row'>
			<textarea name='area_comment' id='area_comment'>!!area_comment!!</textarea>
		</div>
		<div class='row'>
			<label class='etiquette' for='area_color'>".$msg['contribution_area_color']."</label>
		</div>
		<div class='row'>
			<input type='color' class='saisie-20em' name='area_color' id='area_color' value='!!area_color!!' />
		</div>
		<div class='row'>						
			<label for='area_status'>". $msg['contribution_area_status'] ."</label>
			<br/>
			<select name='area_status' id='area_status'>
				!!area_status!!
			</select>
		</div>
		!!area_rights!!
		<div class='row'></div>
	</div>
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_nomenclature_voice_form_exit']."'  onclick=\"document.location='./admin.php?categ=contribution_area&sub=area'\"  />
			<input type='submit' class='bouton' value='".$msg['admin_nomenclature_voice_form_save']."' onclick=\"document.getElementById('action').value='save';if (!test_form(this.form)) return false;\" />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>";

$contribution_area_form_definition = "
<div class='row'>
	<h3>
		!!area_title!!
	</h3>
</div>
<div data-dojo-id='availableEntities' data-dojo-type='dojo/store/Memory' data-dojo-props='data:!!available_entities_data!!'></div>
<div data-dojo-id='graphStore' data-dojo-type='apps/contribution_area/GraphStore' data-dojo-props='area_id:!!id!!,data:!!graph_data_store!!,current_scenario:0'></div>
		<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true' style='height:800px;width:100%;'>
	<div data-dojo-type='dijit/layout/BorderContainer' data-dojo-props='splitter:true,region:\"left\"' style='height:100%;width:200px;'>	
		<div data-dojo-props='region:\"center\", splitter:true' style='width:auto;height:50%;' data-dojo-type='apps/contribution_area/FormsList'></div>
		<div  data-dojo-props='region:\"top\",splitter:true' style='width:auto;height:50%' data-dojo-type='apps/contribution_area/ScenariosList'></div>
	</div>	
	<div data-dojo-type='apps/contribution_area/Graph' data-dojo-props='splitter:true,region:\"center\"' style='height:100%;width:auto;overflow:scroll;'></div>
</div>";