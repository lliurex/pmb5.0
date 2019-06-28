<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie_root.class.php,v 1.17.2.1 2017-08-25 13:20:38 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/users.class.php");
require_once ($include_path."/cart.inc.php");

// définition de la classe de gestion des paniers

define( 'CADDIE_ITEM_NULL', 0 );
define( 'CADDIE_ITEM_OK', 1 );
define( 'CADDIE_ITEM_DEJA', 1 ); // identique car on peut ajouter des liés avec l'item et non pas l'item saisi lui-même ...
define( 'CADDIE_ITEM_IMPOSSIBLE_BULLETIN', 2 );
define( 'CADDIE_ITEM_EXPL_PRET' , 3 );
define( 'CADDIE_ITEM_BULL_USED', 4) ;
define( 'CADDIE_ITEM_NOTI_USED', 5) ;
define( 'CADDIE_ITEM_SUPPR_BASE_OK', 6) ;
define( 'CADDIE_ITEM_INEXISTANT', 7 );
define( 'CADDIE_ITEM_RESA', 8 );
define( 'CADDIE_ITEM_AUT_USED', 9) ;



class caddie_root {
	// propriétés
	public $name = ''			;	// nom de référence
	public $comment = ""		;	// description du contenu du panier
	public $nb_item = 0		;	// nombre d'enregistrements dans le panier
	public $nb_item_pointe = 0		;	// nombre d'enregistrements pointés dans le panier
	public $autorisations = ""		;	// autorisations accordées sur ce panier
	public $classementGen = ""		;	// classement
	public $liaisons = array(); // Liaisons associées à un panier
	public $acces_rapide = 0;		//accès rapide au panier en résultat de recherche notcies
	public $creation_user_name = '';		//Créateur du panier
	public $creation_date = '';		//Date de création du panier
	public static $table_name = '';
	public static $field_name = '';
	public static $table_content_name = '';
	public static $field_content_name = '';
	
	protected function getData() {
		//initialisation
		$this->name	= '';
		$this->comment	= '';
		$this->nb_item	= 0;
		$this->nb_item_pointe = 0;
		$this->autorisations	= "";
		$this->classementGen	= "";
		$this->acces_rapide	= 0;
		$this->creation_user_name = '';
		$this->creation_date = '0000-00-00 00:00:00';
	}
	
	protected function get_template_form() {
		return "";
	}
	
	protected function get_warning_delete() {
		
	}
	
	// formulaire
	public function get_form($form_action="", $form_cancel="") {
		global $msg, $charset;
		global $PMBuserid;
		
		global $current_print;
		global $clause, $filtered_query;
		
		$form = $this->get_template_form();
		$form = str_replace ( '!!formulaire_action!!', $form_action, $form );
		$form = str_replace('!!formulaire_annuler!!', $form_cancel, $form);
		if ($this->get_idcaddie()) {
			$form = str_replace ( '!!title!!', $msg['edit_cart'], $form);
			$form = str_replace('!!autorisations_users!!', users::get_form_autorisations($this->autorisations,0), $form);
			$form = str_replace('!!infos_creation!!', "<br />".$this->get_info_creation(), $form);
		} else {
			$form = str_replace ( '!!title!!', $msg['new_cart'], $form);
			$form = str_replace('!!autorisations_users!!', users::get_form_autorisations("",1), $form);
			$form = str_replace('!!infos_creation!!', "", $form);
		}
		$form = str_replace('!!name!!', htmlentities($this->name,ENT_QUOTES, $charset), $form);
		$form = str_replace('!!comment!!', htmlentities($this->comment,ENT_QUOTES, $charset), $form);
		$classementGen = new classementGen(static::get_table_name(), $this->get_idcaddie());
		$form = str_replace("!!object_type!!",$classementGen->object_type,$form);
		$form = str_replace("!!classements_liste!!",$classementGen->getClassementsSelectorContent($PMBuserid,$classementGen->libelle),$form);
		$form = str_replace("!!acces_rapide!!",($this->acces_rapide?"checked='checked'":""),$form);
		$memo_contexte = "";
		if($clause) {
			$memo_contexte .= "<input type='hidden' name='clause' value=\"".htmlentities(stripslashes($clause), ENT_QUOTES, $charset)."\">";
		}
		if($filtered_query) {
			$memo_contexte.="<input type='hidden' name='filtered_query' value=\"".htmlentities(stripslashes($filtered_query), ENT_QUOTES, $charset)."\">";
		}
		$form=str_replace('<!--memo_contexte-->', $memo_contexte, $form);
		return $form;
	}
	
	protected function get_links_form() {
		return "";
	}
	
	public function set_properties_from_form() {
		global $autorisations;
		global $cart_name;
		global $cart_comment;
		global $acces_rapide;
	
		if (is_array($autorisations)) {
			$this->autorisations=implode(" ",$autorisations);
		} else {
			$this->autorisations="1";
		}
		$this->name = stripslashes($cart_name);
		$this->comment = stripslashes($cart_comment);
		$this->acces_rapide = (isset($acces_rapide)?1:0);
	}
	
	protected static function get_order_cart_list() {
		return " order by name, comment ";
	}
	
	public static function get_cart_data($temp) {
		return array();	
	}
	
	// liste des paniers disponibles
	public static function get_cart_list($restriction_panier="",$acces_rapide = 0) {
		global $PMBuserid;
		
		$cart_list=array();
		if ($restriction_panier=="") {
			$requete = "SELECT * FROM ".static::get_table_name()." where 1 ";
		} else {
			$requete = "SELECT * FROM ".static::get_table_name()." where type='$restriction_panier' ";
		}
		if ($PMBuserid!=1) {
			$requete.=" and (autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid') ";
		}
		if ($acces_rapide) {
			$requete .= " and acces_rapide=1";
		}
		$requete .= static::get_order_cart_list();
		$result = pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($result)) {
			while ($temp = pmb_mysql_fetch_object($result)) {
				$cart_list[] = static::get_cart_data($temp);
			}
		}
		return $cart_list;
	}
	
	protected function get_info_user() {
		global $PMBuserid;
		$query = "SELECT CONCAT(prenom, ' ', nom) as name FROM users WHERE userid=".$PMBuserid;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			return pmb_mysql_fetch_object($result);
		}
		return false;
	}
	
	// suppression d'un item
	public function del_item($item=0) {
		$query = "delete FROM ".static::get_table_content_name()." where ".static::get_field_content_name()."='".$this->get_idcaddie()."' and object_id='".$item."' ";
		$result = @pmb_mysql_query($query);
		$this->compte_items();
	}
	
	// Dépointage de tous les items
	public function depointe_items() {
		$query = "update ".static::get_table_content_name()." set flag=null where ".static::get_field_content_name()."='".$this->get_idcaddie()."' ";
		$result = @pmb_mysql_query($query);
		$this->compte_items();
	}
	
	// Dépointage d'un item
	public function depointe_item($item=0) {
		if ($item) {
			$query = "update ".static::get_table_content_name()." set flag=null where ".static::get_field_content_name()."='".$this->get_idcaddie()."' and object_id='".$item."' ";
			$result = @pmb_mysql_query($query);
			if ($result) {
				$this->compte_items();
				return 1;
			} else {
				return 0;
			}
		}
	}
	
	public function pointe_items_from_query($query) {
		global $msg;
		
		if (pmb_strtolower(pmb_substr($query,0,6))!="select") {
			error_message_history($msg['caddie_action_invalid_query'],$msg['requete_echouee'],1);
			exit();
		}
		$result_selection = pmb_mysql_query($query);
		if (!$result_selection) {
			error_message_history($msg['caddie_action_invalid_query'],$msg['requete_echouee'].pmb_mysql_error(),1);
			exit();
		}
		if(pmb_mysql_num_rows($result_selection)) {
			while ($obj_selection = pmb_mysql_fetch_object($result_selection)) {
				if(get_called_class() == 'empr_caddie') {
					$this->pointe_item($obj_selection->object_id);
				} else {
					$this->pointe_item($obj_selection->object_id,$obj_selection->object_type);
				}
			}
		}
	}
	
	// suppression d'un panier
	public function delete() {
		$query = "delete FROM ".static::get_table_content_name()." where ".static::get_field_content_name()."='".$this->get_idcaddie()."' ";
		$result = pmb_mysql_query($query);
		$query = "delete FROM ".static::get_table_name()." where ".static::get_field_name()."='".$this->get_idcaddie()."' ";
		$result = pmb_mysql_query($query);
	}
	
	// compte_items
	public function compte_items() {
		$this->nb_item = 0 ;
		$this->nb_item_pointe = 0 ;
		$rqt_nb_item="select count(1) from ".static::get_table_content_name()." where ".static::get_field_content_name()."='".$this->get_idcaddie()."' ";
		$this->nb_item = pmb_mysql_result(pmb_mysql_query($rqt_nb_item), 0, 0);
		$rqt_nb_item_pointe = "select count(1) from ".static::get_table_content_name()." where ".static::get_field_content_name()."='".$this->get_idcaddie()."' and (flag is not null and flag!='') ";
		$this->nb_item_pointe = pmb_mysql_result(pmb_mysql_query($rqt_nb_item_pointe), 0, 0);
	}
	
	public function add_items_by_collecte_selection($final_query) {
		global $msg;
		
		$nb_element_a_ajouter = 0;
		$line = pmb_split("\n", $final_query);
		$nb_element_avant = $this->nb_item;
		while(list($cle, $valeur)= each($line)) {
			if ($valeur != '') {
				if ( (pmb_strtolower(pmb_substr($valeur,0,6))=="select") || (pmb_strtolower(pmb_substr($valeur,0,6))=="create")) {
				} else {
					echo pmb_substr($valeur,0,6);
					error_message_history($msg['caddie_action_invalid_query'],$msg['requete_selection'],1);
					exit();
				}
				if (!explain_requete($valeur)) die("<br /><br />".$valeur."<br /><br />".$msg["proc_param_explain_failed"]."<br /><br />".$erreur_explain_rqt);
				$result_selection = pmb_mysql_query($valeur);
				if (!$result_selection) {
					error_message_history($msg['caddie_action_invalid_query'],$msg['requete_echouee'].pmb_mysql_error(),1);
					exit();
				}
				if (pmb_strtolower(pmb_substr($valeur,0,6))=="select") {
					$nb_element_a_ajouter += pmb_mysql_num_rows($result_selection);
					if(pmb_mysql_num_rows($result_selection)) {
						while ($obj_selection = pmb_mysql_fetch_object($result_selection))
							if(get_called_class() == 'empr_caddie') {
								$this->add_item($obj_selection->object_id);
							} else {
								$this->add_item($obj_selection->object_id,$obj_selection->object_type);
							}
					} // fin if mysql_num_rows
					$this->compte_items();
				} // fin if rqt sélection
			} //fin valeur nonvide
		} // fin while list $cle
		$nb_element_apres = $this->nb_item;
		$msg["caddie_affiche_nb_ajouts"] = str_replace('!!nb_a_ajouter!!', $nb_element_a_ajouter, $msg["caddie_affiche_nb_ajouts"]);
		$msg["caddie_affiche_nb_ajouts"] = str_replace('!!nb_ajoutes!!', ($nb_element_apres-$nb_element_avant), $msg["caddie_affiche_nb_ajouts"]);
		$res_exec = "<hr />$msg[caddie_affiche_nb_ajouts]<hr />";
		return $res_exec;
	}
	
	protected function replace_in_action_query($query, $by) {
		$final_query = $query;
		return $final_query;
	}
	
	public function update_items_by_action_selection($final_query) {
		global $msg;
		global $elt_flag;
		global $elt_no_flag;
		
		$error_message_flag = '';
		$error_message_no_flag = '';
		
		//Sélection des éléments du panier
		$nb_elements_flag=0;
		$nb_elements_no_flag=0;
		
		if ($elt_flag) {
			$liste_flag=$this->get_cart("FLAG");
			if (count($liste_flag)) {
				if (pmb_strtolower(pmb_substr($final_query,0,6))=='insert') {
					// procédure insert
					for ($icount=0; $icount<count($liste_flag);$icount++) {
						$query = $this->replace_in_action_query($final_query, $liste_flag[$icount]);
						$result_selection_flag= pmb_mysql_query($query);
						$nb_elts_traites = pmb_mysql_affected_rows() ;
						if ($nb_elts_traites>0) $nb_elements_flag+=$nb_elts_traites;
					} // fin for
				} else {
					// autre procédure
					$query=preg_replace("/CADDIE\(.*[^\)]\)/i",implode(",",$liste_flag),$final_query);
					$result_selection_flag= pmb_mysql_query($query);
					if ($result_selection_flag) {
						$nb_elements_flag=pmb_mysql_affected_rows();
					} else $error_message_flag=pmb_mysql_error();
				} // fin if autre procédure
			}
		}
		if ($elt_no_flag) {
			$liste_no_flag=$this->get_cart("NOFLAG");
			if (count($liste_no_flag)) {
				if (pmb_strtolower(pmb_substr($final_query,0,6))=='insert') {
					// procédure insert
					for ($icount=0; $icount<count($liste_no_flag);$icount++) {
						$query = $this->replace_in_action_query($final_query, $liste_no_flag[$icount]);
						$result_selection_no_flag= pmb_mysql_query($query);
						$nb_elts_traites = pmb_mysql_affected_rows() ;
						if ($nb_elts_traites>0) $nb_elements_no_flag+=$nb_elts_traites;
					} // fin for
				} else {
					// autre procédure
					$query=preg_replace("/CADDIE\(.*[^\)]\)/i",implode(",",$liste_no_flag),$final_query);
					$result_selection_no_flag= pmb_mysql_query($query);
					if ($result_selection_no_flag) {
						$nb_elements_no_flag=pmb_mysql_affected_rows();
					} else $error_message_no_flag=pmb_mysql_error();
				} // fin if autre procédure
			}
		}
		$error_message="";
		print sprintf($msg["caddie_action_flag_processed"],$nb_elements_flag)."<br />";
		print sprintf($msg["caddie_action_no_flag_processed"],$nb_elements_no_flag)."<br />";
		print "<b>".sprintf($msg["caddie_action_total_processed"],($nb_elements_no_flag+$nb_elements_flag))."</b><br /><br />";
		if ($error_message_flag) {
			$error_message.=sprintf($msg["caddie_action_error"],$error_message_flag)."<br />";
		}
		if ($error_message_no_flag) {
			$error_message.=sprintf($msg["caddie_action_error"],$error_message_no_flag);
		}
		if ($error_message) {
			error_message_history($msg["caddie_action_invalid_query"],$error_message,1);
			exit();
		}
	}
	
	protected function get_edition_template_form() {
		return "";
	}
	
	public function get_edition_form($action="", $action_cancel="") {
		global $msg;
		
		$form = $this->get_edition_template_form();
		$form = str_replace('!!action!!', $action, $form);
		$form = str_replace('!!action_cancel!!', $action_cancel, $form);
		$form = str_replace('!!titre_form!!', $msg["caddie_choix_edition"], $form);
		$suppl = "<input type='hidden' name='dest' value=''>&nbsp;
			<input type='button' class='bouton' value='$msg[caddie_choix_edition_HTML]' onclick=\"this.form.dest.value='HTML'; this.form.submit();\" />&nbsp;
			<input type='button' class='bouton' value='$msg[caddie_choix_edition_TABLEAUHTML]' onclick=\"this.form.dest.value='TABLEAUHTML'; this.form.submit();\" />&nbsp;
			<input type='button' class='bouton' value='$msg[caddie_choix_edition_TABLEAU]' onclick=\"this.form.dest.value='TABLEAU'; this.form.submit();\" />" ;
		$form = str_replace('<!-- !!boutons_supp!! -->', $suppl.'<!-- !!boutons_supp!! -->', $form);
		return $form;
	}
	
	protected function get_js_script_cart_objects($module='ajax') {
		global $msg;
		return "
			<script>
				var ajax_pointage=new http_request();
				var num_caddie=0;
				var num_item=0;
				var action='';
				function add_pointage_item(idcaddie,id_item) {
					num_caddie=idcaddie;
					num_item=id_item;
					action='add_item';
					var url = './ajax.php?module=".$module."&categ=caddie&sub=pointage&moyen=manu&action=add_item&idcaddie='+idcaddie+'&id_item='+id_item;
			 		ajax_pointage.request(url,0,'',1,pointage_callback,0,0);
				}
			
				function del_pointage_item(idcaddie,id_item) {
					num_caddie=idcaddie;
					num_item=id_item;
					action='del_item';
					var url = './ajax.php?module=".$module."&categ=caddie&sub=pointage&moyen=manu&action=del_item&idcaddie='+idcaddie+'&id_item='+id_item;
					ajax_pointage.request(url,0,'',1,pointage_callback,0,0);
				}
				function pointage_callback(response) {
					data = eval('('+response+')');
					switch (action) {
						case 'add_item':
							if (data.res_pointage == 1) {
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/depointer.png';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_depointer']."';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','del_pointage_item('+num_caddie+','+num_item+')');
							} else {
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/pointer.png';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_pointer']."';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','add_pointage_item('+num_caddie+','+num_item+')');
							}
							break;
						case 'del_item':
							if (data.res_pointage == 1) {
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/pointer.png';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_pointer']."';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','add_pointage_item('+num_caddie+','+num_item+')');
							} else {
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).src='./images/depointer.png';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).title='".$msg['caddie_item_depointer']."';
								document.getElementById('caddie_'+num_caddie+'_item_'+num_item).setAttribute('onclick','del_pointage_item('+num_caddie+','+num_item+')');
							}
							break;
					}
					var div = document.createElement('div');
					div.setAttribute('id','cart_'+data.idcaddie+'_nb_items');
					div.innerHTML = data.aff_cart_nb_items;
					document.getElementById('cart_'+data.idcaddie+'_nb_items').parentNode.replaceChild(div,document.getElementById('cart_'+data.idcaddie+'_nb_items'));
				}
			</script>";
	}
	
	// affichage du contenu complet d'un caddie
	public function aff_cart_objects ($url_base="", $no_del=false, $rec_history=0, $no_point=false) {
		
	}
	
	public function aff_cart_nb_items() {
		global $msg;
		
		return "
		<div id='cart_".$this->get_idcaddie()."_nb_items' name='cart_".$this->get_idcaddie()."_nb_items'>
			<div class='row'>
				<div class='colonne3'>".$msg['caddie_contient']."</div>
				<div class='colonne3' align='center'>".$msg['caddie_contient_total']."</div>
				<div class='colonne_suite' align='center'>".$msg['caddie_contient_nb_pointe']."</div>
			</div>
			<div class='row'>
				<div class='colonne3' align='right'>".$msg['caddie_contient_total']."</div>
				<div class='colonne3' align='center'><b>".$this->nb_item."</b></div>
				<div class='colonne_suite' align='center'><b>".$this->nb_item_pointe."</b></div>
			</div>
		</div>
		<br />";
	}
	
	public function aff_nb_items_reduit() {
		global $msg;
		return "<td class='classement20'><b>".$this->nb_item_pointe."</b>". $msg['caddie_contient_pointes']." / <b>".$this->nb_item."</b> </td>";
	}
	
	protected function get_choix_quoi_template_form() {
		return "";
	}
	
	public function get_choix_quoi_form($action="", $action_cancel="", $titre_form="", $bouton_valider="",$onclick="", $aff_choix_dep = false) {
		global $msg;
		global $elt_flag,$elt_no_flag;
		
		$form = $this->get_choix_quoi_template_form();
		$form = str_replace('!!action!!', $action, $form);
		$form = str_replace('!!action_cancel!!', $action_cancel, $form);
		$form = str_replace('!!titre_form!!', $titre_form, $form);
		$form = str_replace('!!bouton_valider!!', $bouton_valider, $form);
		if ($onclick!="") $form = str_replace('!!onclick_valider!!','onClick="'.$onclick.'"',$form);
		else $form = str_replace('!!onclick_valider!!','',$form);
		if ($elt_flag) {
			$form = str_replace('!!elt_flag_checked!!', 'checked=\'checked\'', $form);
		} else {
			$form = str_replace('!!elt_flag_checked!!', '', $form);
		}
		if ($elt_no_flag) {
			$form = str_replace('!!elt_no_flag_checked!!', 'checked=\'checked\'', $form);
		} else {
			$form = str_replace('!!elt_no_flag_checked!!', '', $form);
		}
		return $form;
	}
	
	public function get_info_creation() {
		global $msg;
	
		if ($this->creation_date != '0000-00-00 00:00:00') {
			$create_date = new DateTime($this->creation_date);
			return sprintf($msg["empr_caddie_creation_info"], $create_date->format('d/m/Y'),$this->creation_user_name);
		} else {
			return $msg['empr_caddie_creation_no_info'];
		}
	}
	
	public static function check_rights($id) {
		global $msg;
		global $PMBuserid;
	
		if ($id) {
			$query = "SELECT autorisations FROM ".static::get_table_name()." WHERE ".static::get_field_name()."='$id' ";
			$result = @pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$temp = pmb_mysql_fetch_object($result);
				$rqt_autorisation=explode(" ",$temp->autorisations);
				if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid == 1) return $id ;
			}
		}
		return 0 ;
	}
	
	public function reindex_from_list($liste=array()) {
		
	}
	
	public function del_items_base_from_list($liste=array()) {
		return '';
	}
	
	public function get_tab_list() {
		global $elt_flag, $elt_no_flag;
		
		$list = array();
		
		if (($elt_flag=="") && ($elt_no_flag=="")) {
			$elt_no_flag = 1;
			$elt_flag = 1;
		}
		$query = "SELECT ".static::$table_content_name.".* FROM ".static::$table_content_name." where ".static::$field_content_name."='".$this->get_idcaddie()."' ";
		if ($elt_flag && $elt_no_flag ) $complement_clause = "";
		if (!$elt_flag && $elt_no_flag ) $complement_clause = " and flag is null ";
		if ($elt_flag && !$elt_no_flag ) $complement_clause = " and flag is not null ";
		$query .= $complement_clause." order by object_id";
		$result = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($result)) {
			while ($row = pmb_mysql_fetch_object($result)) {
				$list[] = array('object_id' => $row->object_id, 'flag' => $row->flag ) ;
			}
		}
		return $list;
	}
	
	protected function write_header_tableau($worksheet) {
		
	}
	
	protected function write_content_tableau($worksheet) {
	
	}
	
	protected function get_display_header_tableauhtml() {
	
	}
	
	protected function get_display_content_tableauhtml() {
	
	}
	
	public function write_tableau($worksheet) {
		$this->write_header_tableau($worksheet);
		$this->write_content_tableau($worksheet);
	}
	
	public function get_display_tableauhtml() {
		$display = "<table>";
		$display .= $this->get_display_header_tableauhtml();
		$display .= $this->get_display_content_tableauhtml();
		$display .= "</table>";
		return $display;
	}
	
	public function get_export_iframe($param_exp='') {
		global $elt_flag, $elt_no_flag, $keep_expl, $keep_explnum, $export_type;
		
		return "
			<div>
				<iframe name=\"ieexport\" frameborder=\"0\" scrolling=\"yes\" width=\"600\" height=\"500\" src=\"./admin/convert/start_export_caddie.php?elt_flag=".$elt_flag."&elt_no_flag=".$elt_no_flag."&keep_expl=".$keep_expl."&keep_explnum=".$keep_explnum."&idcaddie=".$this->get_idcaddie()."&export_type=".$export_type.(is_object($param_exp) ? "&".$param_exp->get_parametres_to_string() : '')."\">
			</div>
			<noframes>
			</noframes>";
	}
	
	public function get_idcaddie() {
		return 0;
	}
	
	public static function get_table_name() {
		return static::$table_name;
	}
	
	public static function get_field_name() {
		return static::$field_name;
	}
	
	public static function get_table_content_name() {
		return static::$table_content_name;
	}
	
	public static function get_field_content_name() {
		return static::$field_content_name;
	}
	
	public static function get_instance_from_object_type($object_type='NOTI', $idcaddie=0) {
		switch ($object_type) {
			case 'EMPR':
			case 'GROUP':
				$instance = new empr_caddie($idcaddie);
				break;
			case 'MIXED':
			case 'AUTHORS':
			case 'CATEGORIES':
			case 'PUBLISHERS':
			case 'COLLECTIONS':
			case 'SUBCOLLECTIONS':
			case 'SERIES':
			case 'TITRES_UNIFORMES':
			case 'INDEXINT':
			case 'CONCEPTS':
				$instance = new authorities_caddie($idcaddie);
				break;
			case 'NOTI':
			case 'BULL':
			case 'EXPL':
			default:
				$instance = new caddie($idcaddie);
				break;
		}
		return $instance;
	}
} // fin de déclaration de la classe caddie_root
