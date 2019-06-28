<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_index.class.php,v 1.1.2.5 2017-12-14 16:45:23 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/onto/onto_index.class.php");
require_once($class_path."/onto/common/onto_common_index.class.php");

/**
 * class onto_skos_index
*/
class onto_skos_index extends onto_common_index {
	private $paths_infos = array(
			'broad' => array(
				'code_champ' => 5,
				'code_ss_champ' => 1,
				'pond' => 80
			),
			'narrow' => array(
				'code_champ' => 6,
				'code_ss_champ' => 1,
				'pond' => 80
			) 
	);
	
	public function update_paths_index($id_item, $paths_preflabels, $narrow = false) {
		global $sphinx_active;
		
		if ($narrow) {
			$type = 'narrow';
		} else {
			$type = 'broad';
		}
		
		$this->{'delete_'.$type.'_paths_index'}($id_item);
		$field_order = 1;
		$preflabels_already_indexed = array();
		
		$tab_words_insert = $tab_fields_insert = array();
		
		if (is_array($paths_preflabels) && count($paths_preflabels)) {
			$field_order = 1;
			foreach ($paths_preflabels as $preflabels) {
				foreach($preflabels as $preflabel) {			
					if (!empty($preflabel) && !in_array($preflabel['preflabel'], $preflabels_already_indexed)) {
						$lang = $preflabel['lang'];
						if (!empty($this->lang_codes[$preflabel['lang']])) {
							$lang = $this->lang_codes[$preflabel['lang']];
						}
						//fields (contenu brut)
						$tab_fields_insert[] = "('".$id_item."','".$this->paths_infos[$type]['code_champ']."','".$this->paths_infos[$type]['code_ss_champ']."','".$field_order."','".addslashes($preflabel['preflabel'])."','".$lang."','".$this->paths_infos[$type]['pond']."','".$preflabel['id']."')";
					
						//words (contenu éclaté)
						$tab_tmp=explode(' ',strip_empty_words($preflabel['preflabel']));
						$word_position = 1;
						foreach($tab_tmp as $word){
							$num_word = indexation::add_word($word, $lang);
							$tab_words_insert[]="(".$id_item.",".$this->paths_infos[$type]['code_champ'].",".$this->paths_infos[$type]['code_ss_champ'].",".$num_word.",".$this->paths_infos[$type]['pond'].",$field_order,$word_position)";
							$word_position++;
						}
						$field_order++;
						$preflabels_already_indexed[] = $preflabel['preflabel'];
					}
				}
			}
		}
		$this->save_elements($tab_words_insert,$tab_fields_insert);		
		
		//SPHINX
		if($sphinx_active){
			$si = new sphinx_concepts_indexer();
			if(is_object($si)) {
				$si->fillIndex($id_item);
			}
		}
	}
	
	private function delete_broad_paths_index($id_item) {
		$req_del="DELETE FROM skos_words_global_index WHERE id_item ='".$id_item."' AND code_champ='".$this->paths_infos['broad']['code_champ']."'";
		pmb_mysql_query($req_del);
		$req_del="DELETE FROM skos_fields_global_index WHERE id_item ='".$id_item."' AND code_champ='".$this->paths_infos['broad']['code_champ']."'";
		pmb_mysql_query($req_del);
	}
	
	private function delete_narrow_paths_index($id_item) {
		$req_del="DELETE FROM skos_words_global_index WHERE id_item ='".$id_item."' AND code_champ='".$this->paths_infos['narrow']['code_champ']."'";
		pmb_mysql_query($req_del);
		$req_del="DELETE FROM skos_fields_global_index WHERE id_item ='".$id_item."' AND code_champ='".$this->paths_infos['narrow']['code_champ']."'";
		pmb_mysql_query($req_del);
	}
}