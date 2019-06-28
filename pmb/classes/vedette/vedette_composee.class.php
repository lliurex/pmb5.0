<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_composee.class.php,v 1.29 2017-07-21 13:43:36 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/authperso.class.php");
require_once($class_path."/vedette/vedette_link.class.php");

class vedette_composee {
	
	/*** Attributes: ***/	
	
	/**
	 * Le nom du fichier de configuration xml
	 * @var string
	 */
	private $config_filename;
	
	/**
	 * Tableau des �l�ments de la vedette : le premier niveau est la liste des
	 * subdivisions, l'ordre du tableau dans chaque subdivision d�termine l'ordre des
	 * �l�ments
	 * @var vedette_element
	 * @access private
	 */
	protected $vedette_elements = array();
	
	/**
	 * Identifiant de la vedette compos�e
	 * @access private
	 */
	protected $id;
	
	/**
	 * Label de la vedette compos�e
	 * @var string
	 */
	protected $label;
	
	/**
	 * Singleton des configs lues pour ne pas reparser � chaque fois les XML
	 * @var array ($config_filename => array('available_fields', 'subdivisions', 'separator'))
	 */
	static private $configs = array();
	
	static private $grammars; 
	
	public function __construct($id = 0, $config_filename = "rameau"){
		$this->id=$id+0;
		if($this->id){
			$this->read();
		} else {
			$this->config_filename = $config_filename;
			$this->set_config();
		}
	}
	
	/**
	 * Renvoie la liste des subdivisions
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_subdivisions(){
		return self::$configs[$this->config_filename]['subdivisions'];
	}
	
	/**
	 * Renvoie la liste des champs disponibles � l'ajout dans une vedette compos�e
	 * 
	 * @return Array()
	 * @access public
	 */
	public function get_available_fields(){
		return self::$configs[$this->config_filename]['available_fields'];
	}
	
	/**
	 * Renvoie les informations sur le champ vis�
	 * en fonction du num
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_at_available_field_num($num){
		foreach(self::$configs[$this->config_filename]['available_fields'] as $key=>$field){
			if($field['num']==$num){
				return $field;
			}
		}
	}
	
	/**
	 * Renvoie les informations sur le champ vis�
	 * en fonction du label
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_at_available_field_class_name($class_name, $id_authperso_authority=0){
		$id_authperso=0;
		if($class_name=='vedette_authpersos' && $id_authperso_authority){
			$req="select authperso_authority_authperso_num from authperso_authorities,authperso where id_authperso=authperso_authority_authperso_num and id_authperso_authority=". $id_authperso_authority;
			$res = pmb_mysql_query($req,$dbh);
			if(($r=pmb_mysql_fetch_object($res))) {
				$id_authperso=$r->authperso_authority_authperso_num;
			}				
		}
		foreach(self::$configs[$this->config_filename]['available_fields'] as $key=>$field){
			if($field['class_name']==$class_name){
				if($id_authperso){
					if($field['num']==($id_authperso+1000)){
						return $field;
					}
				}else {
					return $field;
				}
			}
		}
	}
	
	/**
	 * Renvoie les informations sur le champ vis�
	 * en fonction du type
	 *
	 * @return Array()
	 * @access public
	 */
	public function get_at_available_field_type($type){
		foreach(self::$configs[$this->config_filename]['available_fields'] as $key=>$field){
			if($field['type']==$type){
				return $field;
			}
		}
	}
	
	/**
	 * Ajoute un �l�ment dans le tableau de gestion des �l�ments
	 *
	 * @param vedette_element vedette_element �l�ment � ajouter
	 * @param string subdivision Subdivision � laquelle ajouter l'ordre
	 * @param int position Position dans la subdivision
	 * 
	 * @return void
	 * @access public
	 */
	public function add_element($vedette_element,$subdivision,$position){
		if(!$vedette_element || !$subdivision){
			return false;
		}
		
		$this->vedette_elements[$subdivision][$position]=$vedette_element;
		return true;
	}
	
	/**
	 * Retourne les �l�ments pr�sents dans une subdivision
	 *
	 * @param string $subdivision
	 * @return array [position]vedette_element
	 */
	public function get_at_elements_subdivision($subdivision){
		$at_elements_subdivision = '';
		if(isset($this->vedette_elements[$subdivision])) {
			$at_elements_subdivision = $this->vedette_elements[$subdivision];
		}
		return $at_elements_subdivision;
	}
	
	/**
	 * Renvoie les informations d'une subdivision en fonction de son code
	 * @param string $code Code de la subdivision
	 * @return array Informations de la subdivision
	 */
	public function get_at_subdivision_code($code) {
		foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
			if ($subdivision["code"] == $code) {
				return $subdivision;
			}
		}
		return array();
	}
	
	/**
	 * Retourne les �l�ments
	 * 
	 * @return array [subdivision][position]vedette_element
	 */
	public function get_elements(){
		return $this->vedette_elements;
	}
	
	/**
	 * Renvoie le nombre d'�l�ments dans la subdivision
	 *
	 * @return int
	 * @access public
	 */
	public function get_nb_elements_subdivision($subdivision){
		return sizeof($this->vedette_elements[$subdivision]);
	}
	
	/**
	 * remonte les donn�es de la base de donn�es
	 *
	 * @return void
	 * @access public
	 */
	public function read(){
		global $dbh;
		global $base_path;
		global $class_path;
		global $include_path;
		global $javascript_path;
		global $styles_path;
		if(!$this->id){
			return false;
		}
		
		$query = "select label, grammar from vedette where id_vedette = ".$this->id;
		$result = pmb_mysql_query($query, $dbh);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($vedette = pmb_mysql_fetch_object($result)) {
				$this->label = $vedette->label;
				$this->config_filename = $vedette->grammar;
			}
		}
		
		$this->set_config();
		
		$query='select object_type, object_id, subdivision, position from vedette_object where num_vedette = '.$this->id.' order by position';
		$result=pmb_mysql_query($query,$dbh);
		if(!pmb_mysql_error($dbh) && pmb_mysql_num_rows($result)){
			while($element_from_database=pmb_mysql_fetch_object($result)){
				$field=$this->get_at_available_field_num($element_from_database->object_type);
				
				$vedette_element_class_name=$field['class_name'];
				require_once($class_path."/vedette/".$vedette_element_class_name.".class.php");
				if(isset($field['params']) && $field['params']){
					$element=new $vedette_element_class_name($field['params'],$field["num"], $element_from_database->object_id);
				}else{
					$element=new $vedette_element_class_name($field["num"], $element_from_database->object_id);
				}
				$this->add_element($element, $element_from_database->subdivision, $element_from_database->position);
			}
		}
	}
	
	/**
	 * Enregistre une vedette compos�e
	 * 
	 * @return void
	 * @access public
	 */
	public function save(){
		global $dbh;
		
		if ($this->check_value()) {
			if($this->id){
				$query='DELETE FROM vedette_object WHERE num_vedette='.$this->id;
				pmb_mysql_query($query,$dbh);
				
				$query='UPDATE vedette set label = "'.addslashes($this->label).'" where id_vedette = '.$this->id;
				pmb_mysql_query($query, $dbh);
			}else{
				$query='INSERT INTO vedette (label, grammar) values ("'.addslashes($this->label).'", "'.$this->config_filename.'")';
				pmb_mysql_query($query,$dbh);
				$this->id=pmb_mysql_insert_id($dbh);
			}
			foreach ($this->vedette_elements as $subdivision=>$elements){
				foreach ($elements as $position=>$element){
					$query = 'INSERT INTO vedette_object (object_type, object_id, num_vedette, subdivision, position) values ('.$element->get_type().', '.$element->get_db_id().', '.$this->id.', "'.$subdivision.'", '.$position.')';
					pmb_mysql_query($query,$dbh);
				}
			}
			return $this->id;
		}
		return 0;
	}
	
	static function replace($object_type, $id, $by){
		global $dbh;
		
		$responsabilities=array();
		$query='select distinct link.num_object as reponsability_num, type_object as reponsability_type from vedette_object as obj, vedette_link as link WHERE obj.num_vedette=link.num_vedette and obj.object_type="'.$object_type.'" and obj.object_id='.$id;
		$result=pmb_mysql_query($query,$dbh);		
		if(pmb_mysql_num_rows($result)){
			while($r=pmb_mysql_fetch_object($result)){
				$responsabilities[]=array(
						'type' => $r->reponsability_type,
						'id' => $r->reponsability_num						
				);
			}
		}
		
		$query='UPDATE vedette_object SET object_id="'.$by.'" WHERE object_type="'.$object_type.'" and object_id='.$id;
		pmb_mysql_query($query,$dbh);
		
		$query='select num_vedette from vedette_object WHERE object_type="'.$object_type.'" and object_id='.$by;
		pmb_mysql_query($query,$dbh);
		$result=pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			while($r=pmb_mysql_fetch_object($result)){
				$vedette_id=$r->num_vedette;
				$vedette = new vedette_composee($vedette_id);
				$vedette->update_label();

				$query = "update vedette set label = '".$vedette->get_label()."' where id_vedette = ".$vedette->get_id();
				pmb_mysql_query($query, $dbh);
					
				vedette_link::update_objects_linked_with_vedette($vedette);
			}
		}
		
		foreach ($responsabilities as $responsability ){
			switch($responsability['type']){
				case TYPE_NOTICE_RESPONSABILITY_PRINCIPAL:
				case TYPE_NOTICE_RESPONSABILITY_AUTRE:
				case TYPE_NOTICE_RESPONSABILITY_SECONDAIRE:
					$query='select responsability_notice from responsability WHERE id_responsability='.$responsability['id'];
					$result=pmb_mysql_query($query,$dbh);
					if(pmb_mysql_num_rows($result)){
						$r=pmb_mysql_fetch_object($result);
						notice::majNoticesTotal($r->responsability_notice);
					}
					break;
				case TYPE_TU_RESPONSABILITY:
				case TYPE_TU_RESPONSABILITY_INTERPRETER:
					$query='select responsability_tu_num from responsability_tu WHERE id_responsability_tu='.$responsability['id'];
					$result=pmb_mysql_query($query,$dbh);
					if(pmb_mysql_num_rows($result)){
						$r=pmb_mysql_fetch_object($result);
						titre_uniforme::update_index($r->responsability_tu_num);
					}
					break;
				case TYPE_CONCEPT_PREFLABEL:
					//r�indexation des notices index�s avec le concepts
					$query = "select num_object from index_concept where type_object = 1 and num_concept = ".$responsability['id'];
					$result = pmb_mysql_query($query,$dbh);
					if($result && pmb_mysql_num_rows($result)){
						while($row = pmb_mysql_fetch_object($result)){
							notice::majNoticesMotsGlobalIndex($row->num_object,"concept");
						}
					}
					break;
			}
		}
	} 
	
	/**
	 * Supprime les donn�es de la base de donn�es
	 *
	 * @return void
	 * @access public
	 */
	public function delete(){
		global $dbh;
		if(!$this->id){
			return false;
		}
		
		$query='DELETE FROM vedette_object WHERE num_vedette='.$this->id;
		pmb_mysql_query($query,$dbh);
		
		$query='DELETE FROM vedette WHERE id_vedette='.$this->id;
		pmb_mysql_query($query,$dbh);
		
		$query='DELETE FROM vedette_link WHERE num_vedette='.$this->id;
		pmb_mysql_query($query,$dbh);
		
		return true;
	}
	
	/**
	 * Lecture de la configuration xml
	 */
	private function set_config(){
	 	global $include_path,$base_path,$charset;
		
		if (!isset(self::$configs[$this->config_filename])) {
		 	self::$configs[$this->config_filename] = array();
			
		 	if(file_exists($include_path.'/vedette/'.$this->config_filename.'_subst.xml')){
		 		$xmlFile = $include_path.'/vedette/'.$this->config_filename.'_subst.xml';
		 	}elseif(file_exists($include_path.'/vedette/'.$this->config_filename.'.xml')){
		 		$xmlFile =$include_path.'/vedette/'.$this->config_filename.'.xml';
		 	}else{
		 		//pas de fichier � analyser
		 		return false;
		 	}
		 	
			$fileInfo = pathinfo($xmlFile);
			$tempFile = $base_path."/temp/XML".preg_replace("/[^a-z0-9]/i","",$fileInfo['dirname'].$fileInfo['filename'].$charset).".tmp";
			
			if (!file_exists($tempFile) || filemtime($xmlFile) > filemtime($tempFile)) {
				//Le fichier XML original a-t-il �t� modifi� ult�rieurement ?
					//on va re-g�n�rer le pseudo-cache
					if(file_exists($tempFile)){
						unlink($tempFile);
					}
					//Parse le fichier dans un tableau
					$fp=fopen($xmlFile,"r") or die("Can't find XML file $xmlFile");
					$xml=fread($fp,filesize($xmlFile));
					fclose($fp);
					$xml_2_analyze=_parser_text_no_function_($xml, 'COMPOSED_HEADINGS');
					
					self::$configs[$this->config_filename]['available_fields'] = array_map("self::clean_read_xml", $xml_2_analyze['AVAILABLE_FIELDS'][0]['FIELD']);
					self::$configs[$this->config_filename]['subdivisions'] = array_map("self::clean_read_xml", $xml_2_analyze['HEADING'][0]['SUBDIVISION']);
					self::$configs[$this->config_filename]['separator'] = $xml_2_analyze['SEPARATOR'][0]['value'];
					
					$tmp = fopen($tempFile, "wb");
					fwrite($tmp,serialize(self::$configs[$this->config_filename]));
					fclose($tmp);
			} else if (file_exists($tempFile)){
				$tmp = fopen($tempFile, "r");
				self::$configs[$this->config_filename] = unserialize(fread($tmp,filesize($tempFile)));
				fclose($tmp);
			}
			$this->build_authpersos();
			$this->build_ontologies();
		}
		return true;
	}
	
	private static function clean_read_xml($array){
		$return=array();
		foreach ($array as $key=>$val){
			if($val){
				$return[strtolower($key)]=$val;
			}
		}
		return $return;
	}
	
	/**
	 * Retourne le label de la vedette compos�e
	 */
	public function get_label() {
		return $this->label;
	}
	
	/**
	 * Retourne l'identifiant de la vedette compos�e
	 */
	public function get_id() {
		return $this->id;
	}
	
	/**
	 * Retourne le separateur du label de la vedette compos�e
	 */
	public function get_separator() {
		return self::$configs[$this->config_filename]['separator'];
	}
	
	/**
	 * V�rifie la validit� de la vedette compos�e
	 */
	protected function check_value() {
		foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
			$nb_elements = 0;
			if (count($this->vedette_elements[$subdivision["code"]])) {
				foreach ($this->vedette_elements[$subdivision["code"]] as $element) {
					if ($element->get_id() && $element->get_isbd()) {
						$nb_elements++;
					}
				}
			}
			if (isset($subdivision["min"]) && $subdivision["min"] && ($subdivision["min"] > $nb_elements)) {
				return false;
			}
			if (isset($subdivision["max"]) && $subdivision["max"] && ($subdivision["max"] < $nb_elements)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Setter du label
	 * @param string $label
	 */
	public function set_label($label) {
		$this->label = $label;
	}
	
	/**
	 * Vide le tableau des �l�ments
	 */
	public function reset_elements() {
		$this->vedette_elements = array();
	}
	
	/**
	 * Met � jour les vedettes contenant l'�l�ment
	 * @param int $element_id Identifiant en base de l'�l�ment
	 * @param string $element_type Type de l'�l�ment
	 */
	public static function update_vedettes_built_with_element($element_id, $element_type) {
		global $dbh;
		
		$vedettes_id = self::get_vedettes_built_with_element($element_id, $element_type);
		foreach ($vedettes_id as $vedette_id) {
			$vedette = new vedette_composee($vedette_id);
			$vedette->update_label();
			
			$query = "update vedette set label = '".$vedette->get_label()."' where id_vedette = ".$vedette->get_id();
			pmb_mysql_query($query, $dbh);
			
			vedette_link::update_objects_linked_with_vedette($vedette);
		}
	}
	
	public static function get_vedettes_display($vedettes_id) {
		global $dbh;
		
		$diplay='';
		foreach ($vedettes_id as $vedette_id) {
			$responsabilities=array();
			$query='select distinct link.num_object as reponsability_num, type_object as reponsability_type from vedette_link as link 
					WHERE link.num_vedette='.$vedette_id;
							
			$result=pmb_mysql_query($query,$dbh);
			if(pmb_mysql_num_rows($result)){
				while($r=pmb_mysql_fetch_object($result)){
					$responsabilities[]=array(
							'type' => $r->reponsability_type,
							'id' => $r->reponsability_num
					);
				}
			}
			$vedette = new vedette_composee($vedette_id);
			$diplay.=$vedette->get_label() .'<br>';
			foreach ($responsabilities as $responsability ){
				switch($responsability['type']){
					case TYPE_NOTICE_RESPONSABILITY_PRINCIPAL:
					case TYPE_NOTICE_RESPONSABILITY_AUTRE:
					case TYPE_NOTICE_RESPONSABILITY_SECONDAIRE:
						$query='select responsability_notice from responsability WHERE id_responsability='.$responsability['id'];
						$result=pmb_mysql_query($query,$dbh);
						if(pmb_mysql_num_rows($result)){
							$r=pmb_mysql_fetch_object($result);
							$diplay.=notice::get_notice_view_link($r->responsability_notice)."<br>";					
						}
						break;
					case TYPE_TU_RESPONSABILITY:
					case TYPE_TU_RESPONSABILITY_INTERPRETER:
						$query='select responsability_tu_num from responsability_tu WHERE id_responsability_tu='.$responsability['id'];
						$result=pmb_mysql_query($query,$dbh);
						if(pmb_mysql_num_rows($result)){
							$r=pmb_mysql_fetch_object($result);
							$tu=new titre_uniforme($r->responsability_tu_num);							
							$diplay.="<a href='./autorites.php?categ=see&sub=titre_uniforme&id=".$r->responsability_tu_num."' class='lien_gestion'>".$tu->get_isbd()."</a><br>";
						}
						break;
					case TYPE_CONCEPT_PREFLABEL:
						//notices index�s avec le concepts
						$query = "select num_object from index_concept where type_object = 1 and num_concept = ".$responsability['id'];
						$result = pmb_mysql_query($query,$dbh);
						if($result && pmb_mysql_num_rows($result)){
							while($row = pmb_mysql_fetch_object($result)){
								$diplay.=notice::get_notice_view_link($r->num_object)."<br>";
							}
						}
						break;
				}
			}
			
		}
		return $diplay;
	}
	
	/**
	 * Supprime un �lement dans les vedettes et met � jour le label des vedettes concern�es
	 * @param int $element_id Identifiant en base de l'�l�ment
	 * @param string $element_type Type de l'�l�ment
	 */
	public static function delete_element_and_update_vedettes_built_with_element($element_id, $element_type) {
		global $dbh;
	
		$vedettes_id = self::get_vedettes_built_with_element($element_id, $element_type);			
		foreach ($vedettes_id as $vedette_id) {
			if(!$deleted){
				$vedette = new vedette_composee($vedette_id);			
				$element_type_num=self::get_element_type_num_from_type_str($vedette,$element_type);
				$query='DELETE FROM vedette_object WHERE object_type='.$element_type_num.' and object_id='. $element_id;
				pmb_mysql_query($query,$dbh);
				$deleted=1;
			}
			$vedette = new vedette_composee($vedette_id);	
			$vedette->update_label();
				
			$query = "update vedette set label = '".$vedette->get_label()."' where id_vedette = ".$vedette->get_id();
			pmb_mysql_query($query, $dbh);
				
			vedette_link::update_objects_linked_with_vedette($vedette);
		}
	}

	public static function get_element_type_num_from_type_str($vedette,$element_type) {
		// On r�cup�re l'identifiant li� au type d'�l�ment
		$element_type_num=0;
		if($element_type>1000){	// authperso
			$element_type_num = $element_type;
		}else{
			foreach($vedette->get_available_fields() as $key=>$field){
				if($field["type"] == $element_type){
					$element_type_num = $field["num"];
					break;
				}
			}
		}
		return $element_type_num;
	}
		
	/**
	 * Retourne un tableau des identifiants des vedettes contenant l'�l�ment
	 * @param int $element_id Identifiant en base de l'�l�ment
	 * @param string $element_type Type de l'�l�ment
	 * 
	 * @return array Tableau des identifiants des vedettes
	 */
	public static function get_vedettes_built_with_element($element_id, $element_type) {
		global $dbh;
	
		$vedettes_id = array();
		
		$grammars = static::get_grammars();
		foreach ($grammars as $grammar) {
			$vedette = new vedette_composee(0, $grammar);
			// On r�cup�re l'identifiant li� au type d'�l�ment
			$element_type_num=self::get_element_type_num_from_type_str($vedette,$element_type);
			// On va chercher en base les vedettes contenant cet �l�ment
			$query = "select distinct num_vedette from vedette_object inner join vedette on num_vedette = id_vedette where object_id = ".$element_id." and object_type = ".$element_type_num." and grammar = '".$grammar."'";
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$vedettes_id[] = $row->num_vedette;
				}
			}
		}
		return $vedettes_id;
	}
	
	/**
	 * Retourne un tableau des identifiants des vedettes contenant les �l�ments pass�s en param�tres
	 * @param int $elements Tableau des identifiants en base des �l�ments
	 * @param string $grammar Grammaire de l'�l�ment � r�cup�rer
	 * @return array Tableau des identifiants des vedettes retrouv�es
	 */
	public static function get_vedettes_built_with_elements($elements, $grammar="") {
		global $dbh;
	
		$vedettes_id = array();
		$grammars = array();
		if($grammar){
			$grammars[] = $grammar;
		}else{
			$grammars = static::get_grammars();
		}
		foreach($grammars as $grammar){
			$vedette = new vedette_composee(0, $grammar);
			$subquery = '';
			foreach($elements as $element){
				// On r�cup�re l'identifiant li� au type d'�l�ment
				$element_type_num=self::get_element_type_num_from_type_str($vedette, $element['type']);				
				// On va chercher en base les vedettes contenant cet �l�ment
				$query = "select distinct num_vedette from vedette_object inner join vedette on num_vedette = id_vedette 
						where object_id = ".$element['id']." and object_type = ".$element_type_num." and grammar = '".$grammar."'";
				if ($subquery) {
					$query.= " and num_vedette in (".$subquery.")";
				}
				$subquery = $query;
			}
			
			$result = pmb_mysql_query($query, $dbh);
			if (pmb_mysql_num_rows($result)) {
				while ($row = pmb_mysql_fetch_object($result)) {
					$vedettes_id[] = $row->num_vedette;
				}
			}	
		}
		return $vedettes_id;
	}
	
	 /**
	  * Recalcule le label de la vedette
	  */
	public function update_label() {
		// On trie le tableau des �l�ments
		$this->sort_vedette_elements();
		
		$label = "";
		
		foreach ($this->vedette_elements as $subdivision=>$elements){
			/* @var $element vedette_element */
			foreach ($elements as $position=>$element){
				if ($label) $label .= self::$configs[$this->config_filename]['separator'];
				$label .= $element->get_isbd();
			}
		}
		$this->label = $label;
	}
	
	/**
	 * Trie le tableau des �l�ments selon l'ordre des subdivisions
	 */
	private function sort_vedette_elements() {
		$sort_array = array();
		
		// On cr�e un tableau contenant les ordres
		foreach ($this->vedette_elements as $subdivision_code => $elements) {
			$subdivision = $this->get_at_subdivision_code($subdivision_code);
			$sort_array[] = $subdivision["order"];
		}
		
		// On trie le tableau des vedettes par rapport au tableau des ordres
		array_multisort($sort_array, $this->vedette_elements, SORT_NUMERIC);
	}
	
	protected function build_authpersos(){
		$authorities = array();
		for($i=0 ; $i<count(self::$configs[$this->config_filename]['available_fields']) ; $i++){
			if(self::$configs[$this->config_filename]['available_fields'][$i]['type'] == 'authperso'){
				$infos = self::$configs[$this->config_filename]['available_fields'][$i];
				unset(self::$configs[$this->config_filename]['available_fields'][$i]);
				$authpersos=authpersos::get_instance();
				foreach($authpersos->info as $authority){
					$authorities[] = array(
						'num' => (string)($infos['num']+$authority['id']),
						'name' => $authority['name'],
						'class_name' => "vedette_authpersos",
						'type' => "authperso".$authority['id'],
						'params' => array(
							'id_authority'=> $authority['id'],
							'label' => $authority['name']
						)
					);
				}
				break;
			}
		}
 		self::$configs[$this->config_filename]['available_fields']=array_merge(self::$configs[$this->config_filename]['available_fields'],$authorities);
	}
	
	protected function build_ontologies(){
		$ontologies = array();
		for($i=0 ; $i<count(self::$configs[$this->config_filename]['available_fields']) ; $i++){
			if(self::$configs[$this->config_filename]['available_fields'][$i]['type'] == 'ontologie'){
				$infos = self::$configs[$this->config_filename]['available_fields'][$i];
				unset(self::$configs[$this->config_filename]['available_fields'][$i]);
				$tmp = new ontologies();
				$ontos = $tmp->looking_for_use_in_concepts();
				foreach($ontos as $onto){
 					$ontologies[] = array(
 						'num' => (string)($infos['num']+$onto['id']),
 						'name' => $onto['name'],
 						'class_name' => "vedette_ontologies",
						'type' => "ontology".$onto['id'],
						'params' => array(
							'num' => (string)($infos['num']+$onto['id']),
							'id_ontology'=> $onto['ontology_id'],
							'label' => $onto['name'],
							'pmbname' => $onto['pmbname']
						)
 					);
				}
				break;
			}
		}
		self::$configs[$this->config_filename]['available_fields']=array_merge(self::$configs[$this->config_filename]['available_fields'],$ontologies);
	}
	
	/**
	 * Retourne l'identifiant de la vedette li�e � un objet
	 * @param int $object_id Identifiant de l'objet
	 * @param int $object_type Type de l'objet
	 * @return int Identifiant de la vedette li�e
	 */
	public static function get_vedette_id_from_object($object_id, $object_type) {
		global $dbh;
		
		$object_id += 0;
		if ($object_id) {
			$query = "select num_vedette from vedette_link where num_object = ".$object_id." and type_object = ".$object_type;
			$result = pmb_mysql_query($query, $dbh);
			if ($result && pmb_mysql_num_rows($result)) {
				if ($row = pmb_mysql_fetch_object($result)) {
					return $row->num_vedette;
				}
			}
		}
		return 0;
	}
	
	/**
	 * Retourne l'identifiant de l'objet li� � la vedette
	 * @param int $object_id Identifiant de la vedette
	 * @param int $object_type Type de l'objet
	 * @return int Identifiant de l'objet li�
	 */
	public static function get_object_id_from_vedette_id($vedette_id, $object_type) {
		global $dbh;
	
		$query = "select num_object from vedette_link where num_vedette = ".$vedette_id." and type_object = ".$object_type;
		$result = pmb_mysql_query($query, $dbh);
		if ($result && pmb_mysql_num_rows($result)) {
			if ($row = pmb_mysql_fetch_object($result)) {
				return $row->num_object;
			}
		}
		return 0;
	}
	
	public function get_subdivision_name_by_code($code) {
		global $msg;
	
		foreach (self::$configs[$this->config_filename]['subdivisions'] as $subdivision) {
			if ($subdivision["code"] == $code) {
				if (substr($subdivision['name'], 0, 4) == "msg:") {
					if ($msg[substr($subdivision['name'], 4)]) {
						return $msg[substr($subdivision['name'], 4)];
					} else {
						return substr($subdivision['name'], 4);
					}
				} else if ($subdivision['name']) {
					return $subdivision['name'];
				} else {
					return $subdivision['code'];
				}
			}
		}
		return "";
	}
	
	public function get_config_filename() {
		return $this->config_filename;
	}
	
	public static function get_grammars() {
		global $include_path;
		if(!isset(static::$grammars)) {
			static::$grammars = array();
			$dh = opendir($include_path.'/vedette');
			while(($file = readdir($dh)) !== false){
				if($file == '.' || $file=='..' || $file =='CVS'){
					continue;
				}
				$grammar = basename(basename($file,".xml"),"_subst");
				if(!in_array($grammar,static::$grammars)){
					static::$grammars[] = $grammar;
				}
			}
			closedir($dh);
		}
		return static::$grammars;
	}
}