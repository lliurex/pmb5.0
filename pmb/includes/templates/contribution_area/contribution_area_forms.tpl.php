<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_forms.tpl.php,v 1.3 2017-03-21 13:13:29 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$contribution_area_entity_line = '
		<div id="!!entity_id!!" class="notice-parent contribution_forms">
			<div class="row item-expandable">
				<img src="./images/plus.gif" class="img_plus" name="imEx" id="!!entity_id!!Img" title="détail" border="0" onclick="  expandBase(\'!!entity_id!!\', true);  return false;" hspace="3">
				<span class="notice-heada">
					!!entity_name!! !!forms_number!!
				</span>
			</div>
			<div id="!!entity_id!!Child" class="notice-child contribution_forms" style="margin-bottom: 6px; display: block; width: 94%;" startopen="Yes">
				!!forms_table!!
				<div class="row">
					<div class="left">
						<input type="button" class="bouton" name="add_form_!!entity_type!!" id="add_form_!!entity_type!!" value="'.$msg['ajouter'].'" onclick=\'document.location="./admin.php?categ=contribution_area&sub=form&type=!!entity_type!!&action=edit&form_id=0";\'/>
						<div class="row"></div>
					</div>
				</div>
				&nbsp;
				&nbsp;
			</div>			
		</div>                   
		<div class="row"></div>
		<div class="row"></div>
		';

$contribution_area_form_line = '
		<tr class="!!odd_even!!" style="cursor: pointer" onmouseover=\'this.className="surbrillance"\' onmouseout=\'this.className="!!odd_even!!"\' >
			<td onmouseup=\'document.location="./admin.php?categ=contribution_area&sub=form&type=!!form_type!!&action=edit&form_id=!!form_id!!";\' >
				!!form_name!!
			</td>
			<td>
				<input type="button" class="bouton" value="X" onclick=\'document.location="./admin.php?categ=contribution_area&sub=form&type=!!form_type!!&action=delete&form_id=!!form_id!!";\'/>
			</td>
		</tr>	
';

$contribution_area_form_table = '
		<table>
			<tr>
				<th>'.$msg['652'].'</th>
				<th></th>
			</tr>
			!!forms_tab!!
		</table>
';