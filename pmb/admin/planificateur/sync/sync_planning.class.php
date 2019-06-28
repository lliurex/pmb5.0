<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sync_planning.class.php,v 1.2 2017-07-12 15:15:02 tsamson Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/scheduler/scheduler_planning.class.php");
require_once($class_path."/connecteurs.class.php");

class sync_planning extends scheduler_planning {

	//formulaire spécifique au type de tâche
	public function show_form ($param=array()) {
		global $msg;
		global $base_path, $type_task_id, $planificateur_id;
		global $subaction;
		global $charset;

		$auto_import = 0;
		$auto_delete = 0;
		$not_in_notices_externes = 0;
		
		if ($subaction == 'change') {
			global $source_entrepot, $connecteurId;
		} else {
			$source_entrepot = 0;
			$connecteurId = 0;
			if (is_array($param)) {
				foreach ($param as $aparam=>$aparamv) {
					if (is_array($aparamv)) {
						foreach ($aparamv as $sparam=>$sparamv) {
							global ${$sparam};
							${$sparam} = $sparamv;		
						}
					} else {
						global ${$aparam};
						${$aparam} = $aparamv;	
					}		
				}				
			}
		}

		$f_select = "
		<script>
			function reload(obj) {
				var index=document.forms[0].source_entrepot.options.selectedIndex;
				
					document.getElementById('connecteurId').value=obj.form.source_entrepot.options[obj.form.source_entrepot.options.selectedIndex].label;
					document.getElementById('subaction').value='change';
					obj.form.submit();
			}
		</script>";
		$f_select .= "<select id='source_entrepot' class='saisie-50em' name='source_entrepot' onchange='reload(this);'>";
		$f_select .="<option id='' label='' value='' >".$this->msg["planificateur_sync_choice"]."</option>";
		$contrs=new connecteurs();
		foreach ($contrs->catalog as $id=>$prop) {
			//Recherche du nombre de sources
			$n_sources=0;
			if (is_file($base_path."/admin/connecteurs/in/".$prop["PATH"]."/".$prop["NAME"].".class.php")) {
				require_once($base_path."/admin/connecteurs/in/".$prop["PATH"]."/".$prop["NAME"].".class.php");
				eval("\$conn=new ".$prop["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$prop["PATH"]."\");");
				$conn->get_sources();
				$n_sources=count($conn->sources);
			}
			if ($n_sources) {
				foreach($conn->sources as $id_source=>$s) {
					//entrepot synchronisable
					if ($s["REPOSITORY"]==1) {
						$f_select .="<option id='".$id_source."' label='".$id."' value='".$id_source."' ".($source_entrepot == $id_source ? "selected" : "").">".htmlentities($s["NAME"],ENT_QUOTES,$charset)."</option>";
					}
				}
			}
		}
		$f_select .= "</select>";
		$f_select .= "<input type='hidden' id='connecteurId' name='connecteurId' value='".$connecteurId."' />";
		//liste des entrepots synchronisable
		$form_task = "
		<div class='row'> 
			<div class='colonne3'>
				<label for='entrepot'>".$this->msg["planificateur_sync_liste"]."</label>
			</div>
			<div class='colonne_suite'>".
				$f_select
			."</div>
		</div>";

		$form_task .= "<div class='row'>
				<div class='colonne3'>
					<label for='source'>&nbsp;</label>
				</div>
				<div class='colonne_suite' id='synchro_source' >";
		if ($source_entrepot) {		
			if ($connecteurId) {
				require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."/".$contrs->catalog[$connecteurId]["NAME"].".class.php");
				eval("\$conn=new ".$contrs->catalog[$connecteurId]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."\");");

				//Si on doit afficher un formulaire de synchronisation
				$syncr_form = $conn->form_pour_maj_entrepot($source_entrepot,"planificateur_form");			
				if ($syncr_form) {
					$form_task .= $syncr_form;
				}
			}
		}
		$form_task .= "</div>
			</div>
		<div class='row'>&nbsp;</div>	
		<div class='row'>
			<div class='colonne3'>
				<label for='auto_import'>".$this->msg["planificateur_sync_import"]."</label>
			</div>
			<div class='colonne_suite'>
				".$msg['40']."&nbsp;<input type='radio' name='auto_import' value='1' ".($auto_import ? "checked='checked'" : "")."/>&nbsp;".$msg['39']."&nbsp;<input type='radio' name='auto_import' value='0' ".($auto_import ? "" : "checked='checked'")."/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='auto_delete'>".$this->msg["planificateur_sync_delete"]."</label>
			</div>
			<div class='colonne_suite'>
				".$msg['40']."&nbsp;<input type='radio' name='auto_delete' value='1' ".($auto_delete ? "checked='checked'" : "")."/>&nbsp;".$msg['39']."&nbsp;<input type='radio' name='auto_delete' value='0' ".($auto_delete ? "" : "checked='checked'")."/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='auto_delete'>".$this->msg["planificateur_sync_import_not_in_notices_externes"]."</label>
			</div>
			<div class='colonne_suite'>
				<input type='checkbox' name='not_in_notices_externes' id='not_in_notices_externes' value='1' ".($not_in_notices_externes ? "checked='checked'" : "")."/>
			</div>
		</div>
		<div class='row'>&nbsp;</div>";
			
		return $form_task;
	}
	
	public function make_serialized_task_params() {
    	global $base_path, $source_entrepot, $connecteurId;
    	global $auto_import, $auto_delete, $not_in_notices_externes;

    	$t = parent::make_serialized_task_params();

		if ($source_entrepot) {
			$t["source_entrepot"]=$source_entrepot;
			$t["connecteurId"]=$connecteurId;
			if ($connecteurId) {
				$contrs=new connecteurs();
				require_once($base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."/".$contrs->catalog[$connecteurId]["NAME"].".class.php");
				eval("\$conn=new ".$contrs->catalog[$connecteurId]["NAME"]."(\"".$base_path."/admin/connecteurs/in/".$contrs->catalog[$connecteurId]["PATH"]."\");");

				//Propre au connecteur
				$t["envt"]=$conn->get_maj_environnement($source_entrepot);
			}
		}
		if($auto_import){
			$t['auto_import'] = ($auto_import ? true : false);
		}
		if($auto_delete){
			$t['auto_delete'] = ($auto_delete ? true : false);
		}
		if($not_in_notices_externes){
			$t['not_in_notices_externes'] = ($not_in_notices_externes ? true : false);
		}

    	return serialize($t);
	}
}