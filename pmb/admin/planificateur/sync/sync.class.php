<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sync.class.php,v 1.10 2017-07-12 15:15:02 tsamson Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/connecteurs.class.php");

class sync extends scheduler_task {
	public $id_connector;
	public $id_source;

	public function execution() {
		global $base_path, $msg;				
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			if (file_exists($base_path."/admin/connecteurs/in/catalog_subst.xml")) 
				$catalog=$base_path."/admin/connecteurs/in/catalog_subst.xml";
			else
				$catalog=$base_path."/admin/connecteurs/in/catalog.xml";
				
			$xml=file_get_contents($catalog);
			$param=_parser_text_no_function_($xml,"CATALOG");
			
			$tparameters = $this->unserialize_task_params();
		
			if (isset($tparameters)) {
				if (is_array($tparameters)) {
					foreach ($tparameters as $aparameters=>$aparametersv) {
						if (is_array($aparametersv)) {
							foreach ($aparametersv as $sparameters=>$sparametersv) {
								global ${$sparameters};
								${$sparameters} = $sparametersv;
							}
						} else {
							global ${$aparameters};
							${$aparameters} = $aparametersv;						
						}
					}
				}
			}

			$this->id_source = $source_entrepot;
			if ($this->id_source) {
				$rqt = "select id_connector, name from connectors_sources where source_id=".$this->id_source;
				$res = pmb_mysql_query($rqt);
				$path = pmb_mysql_result($res,0,"id_connector");
				$name = pmb_mysql_result($res,0,"name");
				for ($i=0; $i<count($param["ITEM"]); $i++) {
					$item=$param["ITEM"][$i];
					if ($item["PATH"] == $path) {
						if ($item["ID"]) {
							$this->id_connector = $item["ID"];
							$result = array();
							$this->add_section_report($this->msg["report_sync"]." : ".$name);
							if (method_exists($this->proxy, "pmbesSync_doSync")) {
								$result[] = $this->proxy->pmbesSync_doSync($this->id_connector, $this->id_source, $auto_import, $this->id_tache, array(&$this, "listen_commande"), array(&$this, "traite_commande"), $auto_delete, $not_in_notices_externes);
								if ($result) {
									foreach ($result as $lignes) {
										foreach ($lignes as $ligne) {
											if ($ligne != '') {
												$this->add_content_report($ligne);
											}
										}
									}
								}
							} else {
								$this->add_function_rights_report("doSync","pmbesSync");
							}	
						}
					}
				}
			} else {
				$this->add_section_report($this->msg["report_sync"]." : ".$this->msg["report_sync_false"]);
				$this->add_content_report($this->msg["error_parameters"]);
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
		
	public function traite_commande($cmd,$message = '') {
		global $msg;

		switch ($cmd) {
			case STOP:
				$this->add_content_report($this->msg["planificateur_stop_sync"]);
				break;
			case ABORT:
				$requete="delete from source_sync where source_id=".$this->id_source;
				pmb_mysql_query($requete);
				$this->add_content_report($this->msg["planificateur_abort_sync"]);
				break;
			case FAIL :
				$requete="delete from source_sync where source_id=".$this->id_source;
				pmb_mysql_query($requete);
				$this->add_content_report($this->msg["planificateur_timeout_overtake"]);
				break;
		}
		parent::traite_commande($cmd, $message);
	}
}