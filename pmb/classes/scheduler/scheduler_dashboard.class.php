<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_dashboard.class.php,v 1.2.2.1 2018-02-09 09:45:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");
	
require_once($class_path."/scheduler/scheduler_tasks.class.php");
require_once($class_path."/scheduler/scheduler_task_docnum.class.php");
require_once($class_path."/scheduler/scheduler_progress_bar.class.php");
require_once($class_path."/pdf_html.class.php");
require_once($base_path."/admin/planificateur/templates/tache_rapport.tpl.php");

class scheduler_dashboard {
	
	public function __construct() {
		
	}
	
	public function get_display_list() {
		global $base_path,$msg, $charset;
	
		$display = "<script>
			function show_docsnum(id) {
				if (document.getElementById(id).style.display=='none') {
					document.getElementById(id).style.display='';
			
				} else {
					document.getElementById(id).style.display='none';
				}
			}
		</script>
		<script type=\"text/javascript\" src='".$base_path."/javascript/select.js'></script>
		<script>
			var ajax_get_report=new http_request();
		
			function get_report_content(task_id,type_task_id) {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=get_report&task_id='+task_id+'&type_task_id='+type_task_id;
				  ajax_get_report.request(url,0,'',1,show_report_content,0,0);
			}
		
			function show_report_content(response) {
				document.getElementById('frame_notice_preview').innerHTML=ajax_get_report.get_text();
			}
		
			function refresh() {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=reporting';
				ajax_get_report.request(url,0,'',1,refresh_div,0,0);
	
			}
			function refresh_div() {
				document.getElementById('table_reporting', true).innerHTML=ajax_get_report.get_text();
				var timer=setTimeout('refresh()',20000);
			}
		
			var ajax_command=new http_request();
			var tache_id='';
			function commande(id_tache, cmd) {
				tache_id=id_tache;
				var url_cmd = './ajax.php?module=ajax&categ=planificateur&sub=command&task_id='+tache_id+'&cmd='+cmd;
				ajax_command.request(url_cmd,0,'',1,commande_td,0,0);
			}
			function commande_td() {
				document.getElementById('commande_tache_'+tache_id, true).innerHTML=ajax_command.get_text();
			}
		</script>
		<script type='text/javascript'>var timer=setTimeout('refresh()',20000);</script>";
	
		$display .= "<table id='table_reporting'>";
		$display .= $this->get_display_header_list();
		$display .= $this->get_display_content_list();
		$display .= "</table>";
		return $display;
	}
	
	public function get_display_header_list() {
		global $msg, $charset;
		
		return "
				<tr>
					<th>&nbsp;</th>
					<th width='20%'>".htmlentities($msg["planificateur_task"], ENT_QUOTES, $charset)."</th>
					<th width='15%'>".htmlentities($msg["planificateur_start_exec"], ENT_QUOTES, $charset)."</th>
					<th width='15%'>".htmlentities($msg["planificateur_end_exec"], ENT_QUOTES, $charset)."</th>
					<th width='18%'>".htmlentities($msg["planificateur_next_exec"],ENT_QUOTES,$charset)."</th>
					<th width='12%'>".htmlentities($msg["planificateur_progress_task"], ENT_QUOTES, $charset)."</th>
					<th width='10%'>".htmlentities($msg["planificateur_etat_exec"], ENT_QUOTES, $charset)."</th>
					<th width='10%'>".htmlentities($msg["planificateur_commande_exec"], ENT_QUOTES, $charset)."</th>
				</tr>";
	}
	
	public function get_display_content_list() {
		$display = '';
		
		$sql = "SELECT t.id_tache, p.num_type_tache, p.libelle_tache, t.start_at, t.end_at, t.status, t.msg_statut, p.calc_next_date_deb, p.calc_next_heure_deb, t.commande, t.indicat_progress
				FROM taches t,planificateur p
				Where t.num_planificateur = p.id_planificateur
				";
		$sql_first = $sql." and start_at = '0000-00-00 00:00:00'";
		$sql_second = $sql." and start_at <> '0000-00-00 00:00:00' order by t.start_at DESC";
		
		$res = pmb_mysql_query($sql_first);
		$res2 = pmb_mysql_query($sql_second);
		
		$pair_impair=0;
		$parity=0;
		$parity_source = 0;
		//taches en attente...
		if ($res) {
			while ($row=pmb_mysql_fetch_object($res)) {
				global $pair_impair_source;
				$pair_impair_source = $parity_source++ % 2 ? "even" : "odd";
				$display .= $this->get_display_row($row);
			}
		}
		//taches en cours et terminé
		if ($res2) {
			while ($row2=pmb_mysql_fetch_object($res2)) {
				$pair_impair_source = $parity_source++ % 2 ? "even" : "odd";
				global $pair_impair_source;
				$display .= $this->get_display_row($row2);
			}
		}
		return $display;
	}
	
	//retourne le nombre de tâches associé à un type de tâche
	public function get_nb_docnum($id_tache) {
		$id_tache += 0;
		$result = pmb_mysql_query("select * from taches t, taches_docnum tdn where t.id_tache=tdn.num_tache and id_tache=".$id_tache);
		return pmb_mysql_num_rows($result);
	}
	
	public function get_display_row($row) {
		global $msg, $charset, $pair_impair_source;
	
		//recherche du nombre de documents numériques par tâche
		$n_docsnum = $this->get_nb_docnum($row->id_tache);
			
		//comment task
		$comment = "";
		$scheduler_tasks = new scheduler_tasks();
		foreach ($scheduler_tasks->tasks as $name=>$tasks_type) {
			if ($tasks_type->get_id() == $row->num_type_tache) {
				//présence de commandes .. selecteurs ??
				$show_commands = "";
				$states = $tasks_type->get_states();
				foreach ($states as $aelement) {
					if ($row->status == $aelement["id"]) {
						foreach ($aelement["nextState"] as $state) {
							if ($state["command"] != "") {
								//récupère le label de la commande
								$commands = $tasks_type->get_commands();
								foreach($commands as $command) {
									if (($state["command"] == $command["name"]) && ($state["dontsend"] != "yes")) {
										$show_commands .= "<option id='".$row->id_tache."' value='".$command["id"]."'>".htmlentities($command["label"], ENT_QUOTES, $charset)."</option>";
									}
								}
							}
						}
					}
				}
			}
		}
	
		//lien du rapport
		$line=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair_source'\" onmousedown=\"if (event) e=event; else e=window.event; \" onClick='show_layer(); get_report_content(".$row->id_tache.",".$row->num_type_tache.");' style='cursor: pointer'";
			
		$display = "<tr class='$pair_impair_source' $line title='".htmlentities($comment,ENT_QUOTES,$charset)." : ".htmlentities(stripslashes($row->libelle_tache),ENT_QUOTES,$charset)."'>
				<td>".($n_docsnum?"<img src='images/plus.gif' class='img_plus' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation(); show_docsnum(\"tache_".$row->id_tache."\"); '/>":"&nbsp;")."</td>
				<td>".htmlentities(stripslashes($row->libelle_tache),ENT_QUOTES,$charset)."</td>
				<td>".($row->start_at == '0000-00-00 00:00:00' ? "" : htmlentities(formatdate($row->start_at,ENT_QUOTES,$charset)))."</td>
				<td>".($row->end_at == '0000-00-00 00:00:00' ? "" : htmlentities(formatdate($row->end_at,ENT_QUOTES,$charset)))."</td>
				".$this->command_waiting($row->id_tache)."
				<td >";
		$scheduler_progress_bar = new scheduler_progress_bar($row->indicat_progress);
		$display .= $scheduler_progress_bar->get_display();
		$display .= "</td>
				<td >".htmlentities($msg['planificateur_state_'.$row->status.''],ENT_QUOTES,$charset)."</td>
				<td>";
		if ($show_commands != "") {
			$display .= "<select id='form_commandes' name='form_commandes' class='saisie-15em' onchange='commande(this.options[this.selectedIndex].id, this.options[this.selectedIndex].value)' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation();'>
					<option value='0' selected>".$msg['planificateur_commande_default']."</option>";
			$display .= $show_commands;
			$display .= "</select>";
		}
		$display .= "</td></tr>";
		$display .= "<tr class='$pair_impair_source' style='display:none' id='tache_".$row->id_tache."'><td>&nbsp;</td>
				<td colspan='8'><table style='border:1px solid; background: #ffffff' class='docnum'>";
		$display .= $this->get_display_docsnum($row->id_tache);
		$display .= "</table>
			</td>
		</tr>";
		return $display;
	}
	
	// Envoi d'une commande pour l'interprétation...
	public function command_waiting($id_tache,$cmd=''){
		global $dbh,$msg, $charset;
	
		$requete_sql = "select status, commande from taches where id_tache='".$id_tache."' and end_at='0000-00-00 00:00:00'";
		$result = pmb_mysql_query($requete_sql);
		if(pmb_mysql_num_rows($result) == "1") {
			$status = pmb_mysql_result($result, 0,"status");
			$commande = pmb_mysql_result($result, 0,"commande");
		} else {
			$status = '';
			$commande = 0;
		}
	
		// une commande a déjà été envoyée auparavant...
		if ($commande != '0') {
			$cmd = $commande;
		}
	
		if ($cmd != '') {
			//check command - la commande envoyée est vérifié par rapport au status
			$scheduler_tasks = new scheduler_tasks();
			foreach($scheduler_tasks->tasks as $tasks_type) {
				$states = $tasks_type->get_states();
				foreach ($states as $state) {
					if ($state["id"] == $status) {
						foreach($state["nextState"] as $nextState) {
							$commands = $tasks_type->get_commands();
							foreach($commands as $command) {
								if ($nextState["command"] == $command["name"]) {
									if ($command["id"] == $cmd)
										pmb_mysql_query("update taches set commande=".$cmd.", next_state='".constant($nextState["value"])."' where id_tache=".$id_tache, $dbh);
								}
							}
						}
					}
				}
			}
		}
	
		$rs = pmb_mysql_query("select t.start_at, t.commande, p.calc_next_date_deb, p.calc_next_heure_deb
			from taches t , planificateur p
			where t.num_planificateur = p.id_planificateur
			and id_tache=".$id_tache);
		$tpl = "<td id='commande_tache_".$id_tache."'>";
		if ($rs) {
			$row = pmb_mysql_fetch_object($rs);
			if($row->start_at == '0000-00-00 00:00:00') {
				$tpl .= htmlentities(formatdate($row->calc_next_date_deb),ENT_QUOTES,$charset)." ".htmlentities($row->calc_next_heure_deb,ENT_QUOTES,$charset);
			} else if (($row->start_at != '0000-00-00 00:00:00') && ($row->commande != NULL) && $row->commande) {
				$tpl .= utf8_normalize($msg["planificateur_command_$row->commande"]);
			}
		}
		$tpl .= "</td>";
	
		return $tpl;
	}
	
	public static function get_report_datas($id) {
		$id += 0;
		$query = "SELECT t.id_tache, p.num_type_tache, p.libelle_tache, t.start_at, t.end_at, t.status, t.indicat_progress, t.rapport, t.num_planificateur FROM taches t,planificateur p
				Where t.num_planificateur = p.id_planificateur
				And t.id_tache=".$id."
				order by p.calc_next_date_deb DESC";
		$res=pmb_mysql_query($query);
	
		if (pmb_mysql_num_rows($res)) {
			$r = pmb_mysql_fetch_object($res);
			$task["id_tache"]=$r->id_tache;
			$task["num_planificateur"]=$r->num_planificateur;
			$task["libelle_tache"]=$r->libelle_tache;
			$task["start_at"]= explode (" ",$r->start_at);
			$task["end_at"]= explode (" ",$r->end_at);
			$task["status"] = $r->status;
			$task["indicat_progress"] = $r->indicat_progress;
			$task["rapport"] = unserialize(htmlspecialchars_decode($r->rapport, ENT_QUOTES));
		} else {
			$task["id_tache"]="";
			$task["num_planificateur"]="";
			$task["libelle_tache"]="";
			$task["start_at"]="";
			$task["end_at"]="";
			$task["status"] = "";
			$task["indicat_progress"] = "";
			$task["rapport"] = "";
		}
		return $task;
	}
	
	//appelée si show_report non existant classe spécifique fille
	public static function get_display_details($details) {
		global $charset;
	
		$display = '';
		if (is_array($details)) {
			$display = "<table>";
			foreach ($details as $ligne) {
				if (is_array($ligne)) {
					foreach ($ligne as $une_ligne) {
						$display .= html_entity_decode($une_ligne, ENT_QUOTES, $charset)."<br />";
					}
				} else {
					$display .= html_entity_decode($ligne, ENT_QUOTES, $charset);
				}
			}
			$display .= "</table>";
		}
	
		return $display;
	}
	
	public static function get_report() {
		global $msg, $dbh, $base_path, $report_task, $report_error, $task_id, $type_task_id;
	
		$task_id += 0;
		$query_chk = "select id_tache from taches where id_tache=".$task_id;
		$res_chk = pmb_mysql_query($query_chk, $dbh);
	
		if (pmb_mysql_num_rows($res_chk) == '1') {
			//date de génération du rapport
			$rs = pmb_mysql_query("select curdate()");
			$date_MySQL = pmb_mysql_result($rs, 0);
	
			$task_datas = scheduler_dashboard::get_report_datas($task_id);
	
			//affiche le rapport avec passage du template
			$report_task = str_replace("!!print_report!!", "<a onclick=\"openPopUp('./pdf.php?pdfdoc=rapport_tache&type_task_id=$type_task_id&task_id=".$task_id."', 'Fiche', 500, 400, -2, -2, 'toolbar=no, dependent=yes, resizable=yes, scrollbars=yes')\" href=\"#\"><img src='".$base_path."/images/print.gif' alt='Imprimer...' /></a>", $report_task);
			$report_task = str_replace("!!type_tache_name!!", scheduler_tasks::get_catalog_element($type_task_id, 'COMMENT'), $report_task);
			$report_task = str_replace("!!planificateur_task_name!!", $msg["planificateur_task_name"], $report_task);
			$report_task=str_replace("!!date_mysql!!",formatdate($date_MySQL),$report_task);
			$report_task=str_replace("!!libelle_date_generation!!",$msg["tache_date_generation"],$report_task);
			$report_task=str_replace("!!libelle_date_derniere_exec!!",$msg["tache_date_dern_exec"],$report_task);
			$report_task=str_replace("!!libelle_heure_derniere_exec!!",$msg["tache_heure_dern_exec"],$report_task);
			$report_task=str_replace("!!libelle_date_fin_exec!!",$msg["tache_date_fin_exec"],$report_task);
			$report_task=str_replace("!!libelle_heure_fin_exec!!",$msg["tache_heure_fin_exec"],$report_task);
			$report_task=str_replace("!!libelle_statut_exec!!",$msg["tache_statut"],$report_task);
			$report_task=str_replace("!!report_execution!!",$msg["tache_report_execution"],$report_task);
	
			$report_task=str_replace("!!id!!",$task_datas["id_tache"],$report_task);
			$report_task=str_replace("!!libelle_task!!",stripslashes($task_datas["libelle_tache"]),$report_task);
			$report_task=str_replace("!!date_dern_exec!!",formatdate($task_datas['start_at'][0]),$report_task);
			$report_task=str_replace("!!heure_dern_exec!!",$task_datas['start_at'][1],$report_task);
			$report_task=str_replace("!!date_fin_exec!!",($task_datas['end_at'][0] != '0000-00-00' ? formatdate($task_datas['end_at'][0]) : ''),$report_task);
			$report_task=str_replace("!!heure_fin_exec!!",($task_datas['end_at'][1] != '00:00:00' ? $task_datas['end_at'][1] : ''),$report_task);
			$report_task=str_replace("!!status!!",$msg["planificateur_state_".$task_datas["status"]],$report_task);
			$report_task=str_replace("!!percent!!",$task_datas["indicat_progress"],$report_task);
	
			$report_task=str_replace("!!rapport!!", scheduler_dashboard::get_display_details($task_datas["rapport"]), $report_task);
			
			$log_errors = '';
			$log_filename = 'scheduler_'.scheduler_tasks::get_catalog_element($type_task_id, 'NAME').'_task_'.$task_id.'.log';
			$log_errors_content = scheduler_log::get_content($log_filename); 
			if($log_errors_content) {
				$log_errors .= '
					<table>
						<tr><th>'.$log_filename.'</th></tr>
						<tr><td>	
							<div class="error">'.$log_errors_content.'</div>
						</td></tr>
					</table>';
			}
			$report_task=str_replace("!!log_errors!!", $log_errors, $report_task);
			return $report_task;
		} else {
			return $report_error;
		}
	}
	
	public static function show_pdf_report() {
		global $msg, $dbh, $base_path, $task_id, $type_task_id;
		global $pmb_pdf_font;
		
		$task_id += 0;
		$query_chk = "select id_tache from taches where id_tache=".$task_id;
		$res_chk = pmb_mysql_query($query_chk, $dbh);
	
		if (pmb_mysql_num_rows($res_chk) == '1') {
			//date de génération du rapport
			$rs = pmb_mysql_query("select curdate()");
			$date_MySQL = pmb_mysql_result($rs, 0);
	
			$task_datas = scheduler_dashboard::get_report_datas($task_id);
	
			$ourPDF = new PDF_HTML();
			$ourPDF->Open();
			$ourPDF->addPage();
			$ourPDF->SetXY (15,8);
			$ourPDF->setFont($pmb_pdf_font, 'B', 9);
			$title = "Type : ";
			$ourPDF->setFont($pmb_pdf_font, '', 9);
			$title .= scheduler_tasks::get_catalog_element($type_task_id, 'COMMENT');
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $title, 0, 'L', 0);
			
			$ourPDF->SetXY (15,20);
			$header = $msg["planificateur_task_name"]." : ".stripslashes($task_datas["libelle_tache"])."\n".
					$msg["tache_date_generation"]." : ".formatdate($date_MySQL)."\n".
					$msg["tache_date_dern_exec"]." : ".formatdate($task_datas['start_at'][0])."\n".
					$msg["tache_heure_dern_exec"]." : ".$task_datas['start_at'][1]."\n".
					$msg["tache_date_fin_exec"]." : ".formatdate($task_datas['end_at'][0])."\n".
					$msg["tache_heure_fin_exec"]." : ".$task_datas['end_at'][1]."\n".
					$msg["tache_statut"]. " : ".$msg["planificateur_state_".$task_datas["status"]]." (".$task_datas["indicat_progress"]."%)\n";

			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $msg["planificateur_task_name"]." : ".stripslashes($task_datas["libelle_tache"]), 0, 'L', 0);
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $msg["tache_date_generation"]." : ".formatdate($date_MySQL)."\n", 0, 'L', 0);
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $msg["tache_date_dern_exec"]." : ".formatdate($task_datas['start_at'][0])."\n", 0, 'L', 0);
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $msg["tache_heure_dern_exec"]." : ".$task_datas['start_at'][1]."\n", 0, 'L', 0);
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $msg["tache_date_fin_exec"]." : ".formatdate($task_datas['end_at'][0])."\n", 0, 'L', 0);
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $msg["tache_heure_fin_exec"]." : ".$task_datas['end_at'][1]."\n", 0, 'L', 0);
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 6, $msg["tache_statut"]. " : ".$msg["planificateur_state_".$task_datas["status"]]." (".$task_datas["indicat_progress"]."%)\n", 0, 'L', 0);
			
			$ourPDF->SetXY (15,70);
			$ourPDF->multiCell((210 - 10 - $ourPDF->GetX()), 8, $msg["tache_report_execution"], 0, 'L', 0);
			
			$ourPDF->SetFont('Arial');
			$ourPDF->WriteHTML(scheduler_dashboard::get_display_details($task_datas["rapport"]));
			$ourPDF->OutPut();
		}
	}
	
	//documents numériques par tâches en cours ou exécutées
	public function get_display_docsnum($task_id) {
		$task_id += 0;
		$query = "SELECT id_tache_docnum, tache_docnum_nomfichier, tache_docnum_mimetype,tache_docnum_extfichier, tache_docnum_repertoire FROM taches_docnum WHERE num_tache = '".$task_id."'";
		$res = pmb_mysql_query($query);
		$tab_docnum = array();
		if ($res) {
			while ($row=pmb_mysql_fetch_object($res)) {
				$t=array();
				$t["id_tache_docnum"] = $row->id_tache_docnum;
				$t["tache_docnum_nomfichier"] = $row->tache_docnum_nomfichier;
				$t["tache_docnum_mimetype"] = $row->tache_docnum_mimetype;
				$t["tache_docnum_extfichier"] = $row->tache_docnum_extfichier;
				$t["tache_docnum_repertoire"] = $row->tache_docnum_repertoire;
				$tab_docnum[] = $t;
			}
		}
		$display = "<tr style='cursor: pointer' >";
		$scheduler_task_docnum = new scheduler_task_docnum();
		$display .= $scheduler_task_docnum->show_docnum_table($tab_docnum);
		$display .= "</tr>";
		return $display;
	}
}