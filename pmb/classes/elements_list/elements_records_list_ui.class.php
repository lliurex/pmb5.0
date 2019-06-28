<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: elements_records_list_ui.class.php,v 1.4 2017-02-24 09:17:46 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/elements_list/elements_list_ui.class.php');
require_once($class_path.'/mono_display.class.php');
require_once($class_path.'/serial_display.class.php');

/**
 * Classe d'affichage d'un onglet qui affiche une liste de notices
 * @author vtouchard
 *
 */
class elements_records_list_ui extends elements_list_ui {
	
	protected function generate_elements_list($contents){
		global $dbh;
		$elements_list = '';
		$recherche_ajax_mode = 0;
		$nb = 0;
		foreach($contents as $notice_id){
			$record = @pmb_mysql_fetch_object(@pmb_mysql_query("SELECT * FROM notices WHERE notice_id=".$notice_id, $dbh));
			if(!$recherche_ajax_mode && ($nb++>5)) $recherche_ajax_mode=1;
			$elements_list.= $this->generate_record($record, $recherche_ajax_mode);
		}
		return $elements_list;
	}
	
	private function generate_record($record, $recherche_ajax_mode){
		global $dbh;
		global $link,$link_expl,$link_explnum,$link_serial,$link_analysis;
		global $link_bulletin,$link_explnum_serial,$link_explnum_analysis;
		global $link_explnum_bulletin,$link_notice_bulletin;
		
		switch($record->niveau_biblio) {
			case 'm' :
				// notice de monographie
				$display = new mono_display($record, 6, $link, 1, $link_expl, '', $link_explnum,1, 0, 1, 1, "", 1  , false,true,$recherche_ajax_mode,1);
				return $display->result;
			case 's' :
				// on a affaire à un périodique
				$serial = new serial_display($record, 6, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum_serial, 0, 0, 1, 1, true, 1,$recherche_ajax_mode );
				return $serial->result;
			case 'a' :
				// on a affaire à un article
				// function serial_display ($id, $level='1', $action_serial='', $action_analysis='', $action_bulletin='', $lien_suppr_cart="", $lien_explnum="", $bouton_explnum=1,$print=0,$show_explnum=1, $show_statut=0, $show_opac_hidden_fields=true, $draggable=0 ) {
				$serial = new serial_display($record, 6, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum_analysis, 0, 0, 1, 1, true, 1,$recherche_ajax_mode );
				return $serial->result;
			case 'b' :
				// on a affaire à un bulletin
				$rqt_bull_info = "SELECT s.notice_id as id_notice_mere, bulletin_id as id_du_bulletin, b.notice_id as id_notice_bulletin FROM notices as s, notices as b, bulletins WHERE b.notice_id=$record->notice_id and s.notice_id=bulletin_notice and num_notice=b.notice_id";
				$bull_ids=@pmb_mysql_fetch_object(pmb_mysql_query($rqt_bull_info, $dbh));
				if(!$link_notice_bulletin){
					$link_notice_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id='.$bull_ids->id_du_bulletin;
				} else {
					$link_notice_bulletin = str_replace("!!id!!",$bull_ids->id_du_bulletin,$link_notice_bulletin);
				}
				$link_explnum_bulletin = str_replace("!!bul_id!!",$bull_ids->id_du_bulletin,$link_explnum_bulletin);
				$display = new mono_display($record, 6, $link_notice_bulletin, 1, $link_expl, '', $link_explnum_bulletin,1, 0, 1, 1, "", 1  , false,true,$recherche_ajax_mode);
				$link_notice_bulletin = '';
				return $display->result;
		}
	}
}