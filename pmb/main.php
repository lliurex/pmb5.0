<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.php,v 1.38 2017-04-11 08:58:14 ngantier Exp $

// ----------------- LLIUREX 23/02/2018----------------------------
// forzamos codificación, navegador detecta UTF-8
header('Content-Type: text/html; charset=iso-8859-1');
// ---------------- FIN LLIUREX 23/02/2018------------------------

// définition du minimum nécéssaire
$base_path=".";
$base_auth = "";
$base_title = "\$msg[308]";
$base_noheader=1;
$base_nocheck=1;
require_once ("$base_path/includes/init.inc.php");

//--------------------------LLIUREX 15/07/2019-------------------------
function indexation_required($msg,$bdd_info){

	$query="SELECT * FROM pmb.parametres where type_param='pmb' and sstype_param ='indexation_must_be_initialized' and valeur_param='-1'";

	$result = pmb_mysql_query($query, $dbh);

	if (pmb_mysql_num_rows($result)) {
		$required_indexation=-1;
		
	}else{
		$required_indexation=0;
	}
	return $required_indexation;
}

//---------------------FIN LLIUREX 15/07/2019-----------------

//Est-on déjà authentifié ?
if (!checkUser('PhpMyBibli')) {
	//Vérification que l'utilisateur existe dans PMB
	$query = "SELECT userid,username FROM users WHERE username='$user'";
	$result = pmb_mysql_query($query, $dbh);
	if (pmb_mysql_num_rows($result)) {
		//Récupération du mot de passe
		$dbuser=pmb_mysql_fetch_object($result);

		//Autentification externe si nécéssaire
		if ((file_exists("$include_path/external_admin_auth.inc.php"))&&($dbuser->userid!=1)) {
			include("$include_path/external_admin_auth.inc.php");
		} else {
			// on checke si l'utilisateur existe et si le mot de passe est OK
			$query = "SELECT count(1) FROM users WHERE username='$user' AND pwd=password('$password') ";
			$result = pmb_mysql_query($query, $dbh);
			$valid_user = pmb_mysql_result($result, 0, 0);
		}
	}
} else
	$valid_user=2;

if(!$valid_user) {
	header("Location: index.php?login_error=1");
} else {
	if ($valid_user==1)
		startSession('PhpMyBibli', $user, $database);
}

if(SESSlang) {
	$lang=SESSlang;
	$helpdir = $lang;
}

// localisation (fichier XML)
$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;
require("$include_path/templates/common.tpl.php");
header ("Content-Type: text/html; charset=$charset");

//--------------------------LLIUREX 15/07/2019-------------

require_once("$include_path/templates/main.tpl.php");

print $std_header;
print "<body class='$current_module claro' id='body_current_module' page_name='$current_module'>";
print $menu_bar;

print $extra;
print $main_layout;

$bdd_info=Array($pmb_bdd_version,$pmb_version_database_as_it_should_be,$pmb_subversion_database_as_it_shouldbe,$pmb_bdd_subversion);
$required_indexation=indexation_required($msg,$bdd_info);

if($use_shortcuts) {
		include("$include_path/shortcuts/circ.sht");
	}
if ((!$param_licence)||($pmb_bdd_version!=$pmb_version_database_as_it_should_be)||($pmb_subversion_database_as_it_shouldbe!=$pmb_bdd_subversion)) {

	/*
	require_once("$include_path/templates/main.tpl.php");
	print $std_header;
	print "<body class='$current_module claro' id='body_current_module' page_name='$current_module'>";
	print $menu_bar;

	print $extra;
	if($use_shortcuts) {
		include("$include_path/shortcuts/circ.sht");
	}
	print $main_layout;
	*/

	if ($pmb_bdd_version!=$pmb_version_database_as_it_should_be) {

		$required_indexation=0;

		echo "<h1>".$msg["pmb_v_db_pas_a_jour"]."</h1>";
		echo "<h1>".$msg[1803]."<font color=red>".$pmb_bdd_version."</font></h1>";
		echo "<h1>".$msg['pmb_v_db_as_it_should_be']."<font color=red>".$pmb_version_database_as_it_should_be."</font></h1>";
		echo "<a href='./admin.php?categ=alter&sub='>".$msg["pmb_v_db_mettre_a_jour"]."</a>";
		echo "<SCRIPT>alert(\"".$msg["pmb_v_db_pas_a_jour"]."\\n".$pmb_version_database_as_it_should_be." <> ".$pmb_bdd_version."\");</SCRIPT>";
	} elseif ($pmb_subversion_database_as_it_shouldbe!=$pmb_bdd_subversion) {
		echo "<h1>Minor changes in database in progress...</h1>";
		include("./admin/misc/addon.inc.php");
		echo "<h1>Changes applied in database.</h1>";
		$required_indexation=-1;		
	}
	
	//On est probablement sur une première connexion à PMB
	//$pmb_indexation_must_be_initialized += 0;
	if ($required_indexation) {
		sleep(20);
		print "<SCRIPT>document.location='admin.php?categ=netbase';</SCRIPT>";
		/*
		echo "<h1>Indexation in progress...</h1>";
		flush();
		ob_flush();
		include("./admin/misc/setup_initialization.inc.php");
		echo "<h1>Indexation applied in database.</h1>";	
		*/	
	//---------------------FIN LLIUREX 15/07/2019-----------------
	}
	
	if (!$param_licence) {
		include("$base_path/resume_licence.inc.php");
		print $PMB_texte_licence ;
	}

	print $main_layout_end;
	print $footer;

	pmb_mysql_close($dbh);
	exit ;
}
if ($ret_url) {	
	if(strpos($ret_url, 'ajax.php') !== false) {
		print "<SCRIPT>document.location=\"".$_SERVER['HTTP_REFERER']."\";</SCRIPT>";
		exit;
	}
	print "<SCRIPT>document.location=\"$ret_url\";</SCRIPT>";
	exit ;
}

//chargement de la première page
require_once($include_path."/misc.inc.php");

if ($required_indexation) {
	print "<SCRIPT>document.location='admin.php?categ=netbase';</SCRIPT>";

}else{	
	go_first_tab();
}

pmb_mysql_close($dbh);
