<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_handler.class.php,v 1.1.2.3 2017-12-06 14:53:53 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * class onto_contribution_handler
 * 
 */
class onto_contribtuion_handler extends onto_handler {
	
	/**
	 * Supprime et recrée les déclarations de l'instance passée en paramètre
	 *
	 * @param onto_common_item $item Instance à sauvegarder
	 * 
	 * @return bool
	 * 
	 * @access public
	 */
	public function save( $item ) {
		global $opac_url_base, $area_id, $action;		
		
		if ($item->check_values()) {	
			if(onto_common_uri::is_temp_uri($item->get_uri())){
				$item->replace_temp_uri();
			}
			$assertions = $item->get_assertions();
			$nb_assertions = count($assertions);
			$i = 0;
			
			if ($errs = $this->data_store->get_errors()) {
				print "<br>Erreurs: <br>";
				print "<pre>";
				var_dump($errs);
				print "</pre><br>";
			}
			
			$subjects_deleted = array();
		
			// On peut y aller
			$query = "insert into <pmb> {
				";
			foreach ($assertions as $assertion) {				
				if (!in_array($assertion->get_subject(), $subjects_deleted)) {
					$pmb_id = 0;
					
					//on stocke l'id de l'entité en base SQL s'il existe  
					$query_pmb_id = '	select ?pmb_id where {
						<'.$assertion->get_subject().'> pmb:identifier ?pmb_id
					}';
					$this->data_store->query($query_pmb_id);
					if ($this->data_store->num_rows()) {
						$pmb_id = $this->data_store->get_result()[0]->pmb_id;
					}
					
					// On supprime tous les triplets correspondant à cette uri pour les mettre à jour par la suite
					$query_delete = "delete {
						<".$assertion->get_subject()."> ?prop ?obj
						}";
					$this->data_store->query($query_delete);

					$subjects_deleted[] = $assertion->get_subject();
					
					//puis on commence par ré-insèrer l'id de l'entité en base SQL dans le store
					if ($pmb_id) {
						if (!$this->data_store->num_rows()) {
							$query_insert = 'insert into <pmb> {
									<'.$assertion->get_subject().'> pmb:identifier "'.$pmb_id.'" .
								}';
							$this->data_store->query($query_insert);
						}
					}					
				}
				
				if ($assertion->offset_get_object_property("type") == "literal"){
					$object = "'".addslashes($assertion->get_object())."'";
					$object_properties = $assertion->get_object_properties();
					if($object_properties['lang']){
						$object.="@".$object_properties['lang'];
					}
				}else{
					
					$object = "<".addslashes($assertion->get_object()).">";
					
					if ($assertion->offset_get_object_property("type") == "uri"){
						
						if ($assertion->get_object_type()) {
							
							if (is_numeric($assertion->get_object())) {
								
								$uri = "<".addslashes($opac_url_base.$this->get_class_pmb_name($assertion->get_object_type()).'#'.$assertion->get_object()).">";
								$object = $uri;
								
								//on teste si le triplet n'existe pas déjà
								$query_bis = "	select ?object_type where {
													".$uri." <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <".addslashes($assertion->get_object_type())."> .
													".$uri." <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ?object_type
												}";
								$this->data_store->query($query_bis);
														
								if (!$this->data_store->num_rows()) {
									
									$object .= " .\n";
									//sujet
									$object .= $uri;
									//prédicat
									$object .= ' pmb:identifier ';
									//objet
									$object .= '"'.addslashes($assertion->get_object()).'"';
									
									$object .= " .\n";
									//sujet
									$object .= $uri;
									//prédicat
									$object .= ' <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ';
									//objet
									$object .= '<'.addslashes($assertion->get_object_type()).'>';
																	
									if ($assertion->get_object_properties()['display_label']) {
										
										$object .= " .\n";
										//sujet
										$object .= $uri;
										//prédicat
										$object .= ' pmb:displayLabel ';
										//objet
										$object .= '"'.$assertion->get_object_properties()['display_label'].'"';
									}
								}
							}
						}						
					}
				}					
				$query.= "<".addslashes($assertion->get_subject())."> <".addslashes($assertion->get_predicate())."> ".$object;
								
				if ($area_id && !$i) {
					$query .= " .\n <".addslashes($assertion->get_subject())."> pmb:area ".$area_id;
					
				}
				
				//on ne rentre qu'une seule, afin de ne pas écraser le display label
				if($assertion->get_object_properties()['type'] == "uri" && !$i) {					
					$display_label = $item->get_label($this->get_display_label($assertion->get_object()));
					$query .= " .\n <".addslashes($assertion->get_subject())."> pmb:displayLabel '".addslashes($display_label)."'";
				}
				
				$i++;
				if ($i < $nb_assertions) {
					$query.=" .";
				}
				
				$query.="\n";
			}
			
			$query.="}";
			
			$this->data_store->query($query);
			if ($errs = $this->data_store->get_errors()) {
				print "<br/>Erreurs: <br/>";
				print "<pre>";print_r($errs);print "</pre><br/>";
			}else{
				$index = onto_index::get_instance();
				$index->set_handler($this);
				$index->maj(0,$item->get_uri());				
			}
		} else {
			return $item->get_checking_errors();
		}
		return true;
	} // end of member function save
} // end of onto_handler