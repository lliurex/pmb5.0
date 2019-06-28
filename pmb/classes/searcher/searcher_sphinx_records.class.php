<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_sphinx_records.class.php,v 1.3.2.2 2017-12-19 17:01:06 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class searcher_sphinx_records extends searcher_sphinx {
	protected $index_name = 'records';
	
	public function __construct($user_query){
		global $include_path;
		$this->champ_base_path = $include_path.'/indexation/notices/champs_base.xml';
		parent::__construct($user_query);
		$this->index_name = 'records';
		$this->id_key = 'notice_id';
 	}	
		
	
	protected function get_full_raw_query(){
		return 'select notice_id as id, 100 as weight from notices';
	}
	
	protected function _filter_results(){
		if($this->objects_ids!='') {
			$fr = new filter_results($this->objects_ids);
			$this->objects_ids = $fr->get_results();
			$query = 'delete from '.$this->get_tempo_tablename();
			if($this->objects_ids != ''){
				$query.=' where notice_id not in ('.$this->objects_ids.')' ;
			}
			pmb_mysql_query($query) or die(mysql_error());
		}
	}
	
	public function get_full_query(){		
		$this->get_result();
		$query =  'select notice_id, pert from '.$this->get_tempo_tablename();
		return $query;
	}
	public function get_nb_results(){
		$this->get_result();
		return count(explode(',',$this->objects_ids));
	}

	public function get_sorted_result($tri = "default",$start=0,$number=20){
		$this->tri = $tri;
		$this->get_result();
		$sort = new sort("notices","session");
		$query = $sort->appliquer_tri_from_tmp_table($this->tri,$this->get_tempo_tablename(),'notice_id',$start,$number);
		$res = pmb_mysql_query($query);
		if(pmb_mysql_num_rows($res)){
			$this->result=array();
			while($row = pmb_mysql_fetch_object($res)){
				$this->result[] = $row->notice_id;
			}
		}
		return $this->result;
	}	
		
	public function explain($display,$mode,$mini=false){
		print '<div style="margin-left:10px;width:49%;overflow:hidden;float:left">';
		print '<h1>Recherche SPHINX</h1>';
		print '<p>QUERY : '.$this->sphinx_query.'</p>';
		$start = microtime(true);
 		print '<p>Nombre de resultats trouves: '.$this->get_nb_results().'</p>';
 		if(!$mini){
	 		$result = $this->get_sorted_result();
	 		if($this->get_nb_results()>0 && $result){
		 		$inter = microtime(true);
			 	print '<p>Temps de calcul (en seconde) : '.($inter - $start).'</p>';
			 	if ($display) {
			 		foreach($result as $notice_id){
			 				$n=@pmb_mysql_fetch_object(@pmb_mysql_query("SELECT * FROM notices WHERE notice_id=".$notice_id));
							switch ($n->niveau_biblio) {
								case 's' :
									$serial = new serial_display($n, 6, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum_serial, 0, 0, 1, 1, true, 1 ,$recherche_ajax_mode);
									print $serial->result;
									break;
								case 'a' :
									$serial = new serial_display($n, 6, $link_serial, $link_analysis, $link_bulletin, "", $link_explnum_analysis, 0, 0, 1, 1, true, 1 ,$recherche_ajax_mode);
									print $serial->result;
									break;
								case 'b' :
									$rqt_bull_info = "SELECT s.notice_id as id_notice_mere, bulletin_id as id_du_bulletin, b.notice_id as id_notice_bulletin FROM notices as s, notices as b, bulletins WHERE b.notice_id=$n->notice_id and s.notice_id=bulletin_notice and num_notice=b.notice_id";
									$bull_ids=@pmb_mysql_fetch_object(pmb_mysql_query($rqt_bull_info));
									//si on a les droits
									if(SESSrights & CATALOGAGE_AUTH){//on teste la validitï¿½ du lien
										if(!$link_notice_bulletin){
											$real_link_notice_bulletin = './catalog.php?categ=serials&sub=bulletinage&action=view&bul_id='.$bull_ids->id_du_bulletin;
										} else {
											$real_link_notice_bulletin = str_replace("!!id!!",$bull_ids->id_du_bulletin,$link_notice_bulletin);
										}
									} elseif($link_notice_bulletin) {
										$real_link_notice_bulletin = str_replace("!!id!!",$bull_ids->id_du_bulletin,$link_notice_bulletin);
									}
									$link_explnum_bulletin = str_replace("!!bul_id!!",$bull_ids->id_du_bulletin,$link_explnum_bulletin);
									$display = new mono_display($n, 6, $real_link_notice_bulletin, 1, $link_expl, '', $link_explnum_bulletin,1, 0, 1, 1, "", 1  , false,true,$recherche_ajax_mode);
									print $display->result;
									break;
								default:
								case 'm' :
									// notice de monographie
									$display = new mono_display($n, 6, $link, 1, $link_expl, '', $link_explnum,1, 0, 1, 1, "", 1   , false,true,$recherche_ajax_mode,1);
									print $display->result;
									break ;
							}
			 		}
			 		print '<p>Temps de gen page (en seconde) : '.(microtime(true) - $inter).'</p>';
			 	}
	 		}
 		}
 		print '<p>Temps Total (en seconde) : '.(microtime(true) - $start).'</p></div>';
	}	
	
	public function get_fields_restrict(){
		if(!count($this->fields_restrict)){
			return '';
		}
		$this->fields_restrict = array_unique($this->fields_restrict);
		return '@('.implode(',',$this->fields_restrict).')';
	}
	
	public function init_fields_restrict($mode){
		global $mutli_crit_indexation_oeuvre_title;
		$this->fields_restrict = array();
		switch($mode){
			case 'title' :
				$this->fields_restrict[] = 'f_001_00';
				$this->fields_restrict[] = 'f_002_00';
				$this->fields_restrict[] = 'f_003_00';
				$this->fields_restrict[] = 'f_004_00';
				$this->fields_restrict[] = 'f_006_00';
				$this->fields_restrict[] = 'f_023_01';
				if($mutli_crit_indexation_oeuvre_title){
					$this->fields_restrict[]= 'f_026_01';
				}
				break;
			case 'authors' :
				$this->fields_restrict[] = 'f_027_01';
				$this->fields_restrict[] = 'f_027_02';
				$this->fields_restrict[] = 'f_027_03';
				$this->fields_restrict[] = 'f_027_04';
				$this->fields_restrict[] = 'f_028_01';
				$this->fields_restrict[] = 'f_028_02';
				$this->fields_restrict[] = 'f_028_03';
				$this->fields_restrict[] = 'f_028_04';
				$this->fields_restrict[] = 'f_029_01';
				$this->fields_restrict[] = 'f_029_02';
				$this->fields_restrict[] = 'f_029_03';
				$this->fields_restrict[] = 'f_029_04';
				$this->fields_restrict[] = 'f_127_01';
				$this->fields_restrict[] = 'f_127_02';
				$this->fields_restrict[] = 'f_127_03';
				$this->fields_restrict[] = 'f_127_04';
				$this->fields_restrict[] = 'f_128_01';
				$this->fields_restrict[] = 'f_128_02';
				$this->fields_restrict[] = 'f_128_03';
				$this->fields_restrict[] = 'f_128_04';
				break;
			case 'categories' :
				$this->fields_restrict[] = 'f_025_01';
				break;	
			case 'concepts' :
				$this->fields_restrict[] = 'f_036_01';
				$this->fields_restrict[] = 'f_126_01';
				break;
			case 'map_equinoxe' :
				$this->fields_restrict[] = 'f_041_00';
				break;
			case 'titres_uniformes' :
				$this->fields_restrict[] = 'f_026_01';
				$this->fields_restrict[] = 'f_026_02';
				$this->fields_restrict[] = 'f_026_03';
				$this->fields_restrict[] = 'f_026_04';
				$this->fields_restrict[] = 'f_026_05';
				$this->fields_restrict[] = 'f_026_06';
				$this->fields_restrict[] = 'f_026_07';
				$this->fields_restrict[] = 'f_026_09';
				$this->fields_restrict[] = 'f_026_10';
				$this->fields_restrict[] = 'f_026_11';
				$this->fields_restrict[] = 'f_026_12';
				$this->fields_restrict[] = 'f_026_13';
				$this->fields_restrict[] = 'f_026_14';
				$this->fields_restrict[] = 'f_026_15';
				$this->fields_restrict[] = 'f_026_16';
				$this->fields_restrict[] = 'f_026_17';
				$this->fields_restrict[] = 'f_026_18';
				$this->fields_restrict[] = 'f_026_19';
				$this->fields_restrict[] = 'f_026_20';
				$this->fields_restrict[] = 'f_026_21';
				$this->fields_restrict[] = 'f_026_22';
				$this->fields_restrict[] = 'f_026_23';
				$this->fields_restrict[] = 'f_123_01';
				$this->fields_restrict[] = 'f_124_01';
				$this->fields_restrict[] = 'f_125_01';
				$this->fields_restrict[] = 'f_126_01';
				$this->fields_restrict[] = 'f_127_01';
				$this->fields_restrict[] = 'f_127_02';
				$this->fields_restrict[] = 'f_127_03';
				$this->fields_restrict[] = 'f_127_04';
				//$this->fields_restrict[] = 'f_127_05';
				$this->fields_restrict[] = 'f_128_01';
				$this->fields_restrict[] = 'f_128_02';
				$this->fields_restrict[] = 'f_128_03';
				$this->fields_restrict[] = 'f_128_04';
				//$this->fields_restrict[] = 'f_128_05';
				break;
			default : 
				//nothing to do
				break;
		}
	}
	
	protected function get_filters(){
		$filters = parent::get_filters();
		global $typdoc_query,$statut_query;
		if($typdoc_query){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée! 
			$filters[] = array(
				'name'=> 'typdoc',
				'values' => $typdoc_query
			);
		}
		if($statut_query){
			//on ne s'assure pas de savoir si c'est une chaine ou un tableau, c'est géré dans la classe racine à la volée! 
			$filters[] = array(
				'name'=> 'statut',
				'values' => $statut_query
			);
		}
		return $filters;
	}
	
	protected function _get_objects_ids() {
		if (isset($this->objects_ids)) {
			return $this->objects_ids;
		}
 		global $docnum_query, $mutli_crit_indexation_docnum_allfields;

 		parent::_get_objects_ids();
		if (($this->sphinx_query == '*') || !($docnum_query || $mutli_crit_indexation_docnum_allfields)) {
			return $this->objects_ids;
		}

		$already_found = explode(',', $this->objects_ids);
		$this->sc->SetGroupBy('num_record', SPH_GROUPBY_ATTR);
		$this->sc->SetSelect("id, num_record");
		$nb = 0;
		$matches = array();
		do {
			$this->sc->SetLimits($nb, $this->bypass);
			$result = $this->sc->Query($this->sphinx_query, "records_explnums");
			for($i = 0 ; $i<count($result['matches']) ; $i++){
				if (in_array($result['matches'][$i]['attrs']['num_record'], $already_found)) {
					continue;
				}
				if($this->objects_ids){
					$this->objects_ids.= ',';
				}
				$this->objects_ids.= $result['matches'][$i]['attrs']['num_record'];
				$matches[] = array(
						'id' => $result['matches'][$i]['attrs']['num_record'],
						'weight' => $result['matches'][$i]['weight']
				);
 				$this->nb_result++;
			}
			$nb+= count($result['matches']);
			$this->insert_in_tmp_table($matches);
		} while ($nb < $result['total_found']);
		return $this->objects_ids;
	}
}