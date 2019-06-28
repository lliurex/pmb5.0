<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql.class.php,v 1.4 2017-07-10 15:50:02 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");

class mysql extends scheduler_task {
	
	public function execution() {
		global $charset, $msg;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
		
			$this->add_section_report($this->msg["mysql_operation"]);
			if (method_exists($this->proxy, "pmbesMySQL_mysqlTable")) {
				if ($parameters["mySQL"]) {
					$percent = 0;
					$p_value = (int) 100/count($parameters["mySQL"]);
					foreach($parameters["mySQL"] as $action) {
						$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
			
						if($this->statut == WAITING) {
							$this->send_command(RUNNING);
						}

						if($this->statut == RUNNING) {
							$this->add_section_report($action);
							$result = $this->proxy->pmbesMySQL_mysqlTable($action);
							$maintenance_mysql = array();
							foreach ($result as $i=>$table) {
								switch ($table[2]) {	//Msg_Type : status error info warning
									case "status" :
										$maintenance_mysql["status"][$table[3]][] = $table[0];  
										break;
									case "error" :
										$maintenance_mysql["error"][$table[3]][] = $table[0];
										break;
									case "info" :
										$maintenance_mysql["info"][$table[3]][] = $table[0];
										break;
									case "warning" :
										$maintenance_mysql["warning"][$table[3]][] = $table[0];
										break;
								}
							}
							$txt_msg_type = "";
							$txt_msg_text = "";
							foreach ($maintenance_mysql as $msg_type=>$values) {
								if ($msg_type != $txt_msg_type) {
									$txt_msg_type = $msg_type;
									$this->add_content_report("<b>Op : </b>".$msg_type);
								}
								foreach ($values as $msg_text=>$tables) {
									if ($msg_text != $txt_msg_text) {
										$txt_msg_text = $msg_text;
										$this->add_content_report("<b>Msg_text : </b>".$msg_text);
										$this->add_content_report("<b>Tables : </b>".implode(" - ", $tables));
									}
								}
							}
							$percent += $p_value;
							$this->update_progression($percent);	
						}
					}
				} else {
					$this->add_content_report($this->msg["mysql_action_unknown"]);
				}
			} else {
				$this->add_function_rights_report("mysqlTable","pmbesMySQL");
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}