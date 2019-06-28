<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_txt.class.php,v 1.3 2017-06-30 14:08:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

/**
 * Classe qui permet la gestion de l'indexation des fichiers texte (.txt)
 */
class index_txt{
	
	public $fichier='';
	
	/**
	 * Constructeur
	 */
	public function __construct($filename, $mimetype='' , $extension=''){
		$this->fichier = $filename;
	}
	
	/**
	 * Récupération du texte à indexer dans le fichier texte (.txt)
	 */
	public function get_text($filename){
		
		$fp = fopen($filename, "r");
		while(!feof($fp)){
			$line = fgets($fp,4096); 
			$texte .= $line;
		}
		fclose($fp);

		return $texte;
	}
}
?>
