<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: surligner.inc.php,v 1.21.2.1 2018-01-10 16:10:53 jpermanne Exp $

if (stristr($_SERVER['REQUEST_URI'], "inc.php")) die("no access");

require_once($class_path."/analyse_query.class.php");
require_once($class_path."/XMLlist.class.php");
require_once("$include_path/marc_tables/$pmb_indexation_lang/empty_words");

$carac_spec = new XMLlist("$include_path/messages/diacritiqueiso-8859-1.xml");
$carac_spec->analyser();
$carac = $carac_spec->table;
		
reset($carac_spec->table);

//Nettoyage de la chaine recherch�e
function nettoyer_chaine($tree="",&$tableau,&$tableau_l,$aq,$not) {
	global $empty_word,$charset;
	
	if ($tree=="") $tree=$aq->tree;	
	
	for ($i=0; $i<count($tree); $i++) {
		$mot = "";
		if ($tree[$i]->not) $mul=-1; else $mul=1; 
		if ($tree[$i]->sub==null) {
			if ($not*$mul==1) 
				if ($tree[$i]->literal){
					$mot = str_replace("*","\w*",$tree[$i]->word); 
					$mot=pmb_strtolower(convert_diacrit($mot));
					if($mot && !in_array($mot,$tableau_l) && !in_array($mot,$tableau))
						$tableau_l[]= $mot;
				} else{
					$mot = str_replace("*","\w*",$tree[$i]->word); 				
					if(strlen($tree[$i]->word)<=1) 
						$mot = "";				
				    if($mot && !in_array($mot,$tableau) && !in_array($mot,$tableau_l)){			    	
						$tableau[]= $mot;
				    }
				}
		} else { 
			$not=$not*$mul;
			nettoyer_chaine($tree[$i]->sub,$tableau,$tableau_l,$aq,$not); 
		}
	}
}	

$tableau=array();
$tableau_l=array();
if ($user_query && (trim($user_query) != "*")) {
	$aq=new analyse_query(stripslashes($user_query),0,0,1,0,$opac_stemming_active);
	if (!$aq->error) {
		nettoyer_chaine("",$tableau,$tableau_l,$aq,1);
	}
}

//On calcule des variables de session qui seront utilis�es dans surligner.js.php
$_SESSION['surligner_tableau'] = implode("','",$tableau);
$_SESSION['surligner_tableau_l'] = implode("','",addslashes_array($tableau_l));

$_SESSION['surligner_codes'] = "";
$j=0;
foreach($carac_spec->table as $key=>$val) {
	$values=explode("|",substr($val,1,strlen($val)-2));

	$i=0;
	$temp="[";
	if(!isset($values[$i])) $values[$i] = '';
	while ($values[$i]!="") {
		$temp .=$values[$i];
		$i++;
		if(!isset($values[$i])) $values[$i] = '';
	}
	$temp .= "]";
	$_SESSION['surligner_codes'] .= "codes['".$key."']='".$temp."';\n";
	$j++;
}

$_SESSION['surligner_key_carac'] = "";
foreach($carac_spec->table as $key=>$val) {
	$_SESSION['surligner_key_carac'] .= "	reg=new RegExp(codes['".$key."'], 'g');\n";
	$_SESSION['surligner_key_carac'] .= "	chaine=chaine.replace(reg, '".$key."');\n";
}		

$inclure_recherche = "<script type='text/javascript' src='./includes/javascript/misc.js'></script>";
$inclure_recherche .= "<script type='text/javascript' src='./includes/javascript/surligner.js.php'></script>";
?>