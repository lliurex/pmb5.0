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

//-----------------------------------LLIUREX 26/09/2018------------------------

function show_loader(){
	header("Content-type: text/css");
	echo "<style>
		.lds-ellipsis {
		  display: inline-block;
		  position: relative;
		  width: 64px;
		  height: 64px;
		}
		.lds-ellipsis div {
		  position: absolute;
		  top: 27px;
		  width: 11px;
		  height: 11px;
		  border-radius: 50%;
		  background: #3d474c;
		  animation-timing-function: cubic-bezier(0, 1, 1, 0);
		}
		.lds-ellipsis div:nth-child(1) {
		  left: 6px;
		  animation: lds-ellipsis1 0.6s infinite;
		}
		.lds-ellipsis div:nth-child(2) {
		  left: 6px;
		  animation: lds-ellipsis2 0.6s infinite;
		}
		.lds-ellipsis div:nth-child(3) {
		  left: 26px;
		  animation: lds-ellipsis2 0.6s infinite;
		}
		.lds-ellipsis div:nth-child(4) {
		  left: 45px;
		  animation: lds-ellipsis3 0.6s infinite;
		}
		@keyframes lds-ellipsis1 {
		  0% {
		    transform: scale(0);
		  }
		  100% {
		    transform: scale(1);
		  }
		}
		@keyframes lds-ellipsis3 {
		  0% {
		    transform: scale(1);
		  }
		  100% {
		    transform: scale(0);
		  }
		}
		@keyframes lds-ellipsis2 {
		  0% {
		    transform: translate(0, 0);
		  }
		  100% {
		    transform: translate(19px, 0);
		  }
		}
	</style>";	
	echo "<div id='loader' align='center'><div class='lds-ellipsis''><div></div><div></div><div></div><div></div></div></div>";

}

function indexation_required($msg,$bdd_info){

	$query="SELECT * FROM pmb.parametres where type_param='pmb' and sstype_param ='indexation_must_be_initialized' and valeur_param='-1'";

	$result = pmb_mysql_query($query, $dbh);

	if (pmb_mysql_num_rows($result)) {
		$required_indexation=-1;
		if (($bdd_info[0]==$bdd_info[1]) and ($bdd_info[2]==$bdd_info[3])){
			echo "<SCRIPT>alert(\"".$msg["indexation_alert"]."\");</SCRIPT>";
			echo "<div id='llxwaiting'><br><h1>".$msg["indexation_init"]."</h1></br></div>";
			show_loader();
		}	
	}else{
		$required_indexation=0;
	}
	return $required_indexation;
}


//-------------------------------- FIN LLIUREX 26/09/2018------------------------


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

require_once("$include_path/templates/main.tpl.php");

print $std_header;
print "<body class='$current_module claro' id='body_current_module' page_name='$current_module'>";
print $menu_bar;

print $extra;
print $main_layout;
//-----------------------------------LLIUREX 26/09/2018------------------------

$bdd_info=Array($pmb_bdd_version,$pmb_version_database_as_it_should_be,$pmb_subversion_database_as_it_shouldbe,$pmb_bdd_subversion);
$required_indexation=indexation_required($msg,$bdd_info);

//------------------------------- FIN LLIUREX 26/09/2018------------------------


if($use_shortcuts) {
	include("$include_path/shortcuts/circ.sht");
}
#print $main_layout;




if ((!$param_licence)||($pmb_bdd_version!=$pmb_version_database_as_it_should_be)||($pmb_subversion_database_as_it_shouldbe!=$pmb_bdd_subversion)) {
	
	//-----------------------------------LLIUREX 26/09/2018------------------------

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

	//------------------------------- FIN LLIUREX 26/09/2018------------------------

	
	if ($pmb_bdd_version!=$pmb_version_database_as_it_should_be) {
	//-----------------------------------LLIUREX 26/09/2018------------------------
		$required_indexation=0;
	//-------------------------------- FIN LLIUREX 26/09/2018------------------------
	
		echo "<h1>".$msg["pmb_v_db_pas_a_jour"]."</h1>";
		echo "<h1>".$msg[1803]."<font color=red>".$pmb_bdd_version."</font></h1>";
		echo "<h1>".$msg['pmb_v_db_as_it_should_be']."<font color=red>".$pmb_version_database_as_it_should_be."</font></h1>";
		echo "<a href='./admin.php?categ=alter&sub='>".$msg["pmb_v_db_mettre_a_jour"]."</a>";
		echo "<SCRIPT>alert(\"".$msg["pmb_v_db_pas_a_jour"]."\\n".$pmb_version_database_as_it_should_be." <> ".$pmb_bdd_version."\");</SCRIPT>";

	} elseif ($pmb_subversion_database_as_it_shouldbe!=$pmb_bdd_subversion) {

	//-----------------------------------LLIUREX 26/09/2018------------------------
	
		echo "<h1>".$msg["minor_changes_bbdd"]."</h1>";
		show_loader(); 
		include("./admin/misc/addon.inc.php");
		echo "<h1>".$msg["minor_changes_end"]."</h1>";
		echo "<h1>".$msg["indexation_alert"]."</h1>";
		$required_indexation=-1;
	}
	
	//On est probablement sur une première connexion à PMB
	#$pmb_indexation_must_be_initialized -= 1;
	
	if($required_indexation) {

		echo "<h1>".$msg["indexation_progress"]."</h1>";
		flush();
		ob_flush();
		include("./admin/misc/setup_initialization.inc.php");
		echo "<h1>".$msg["indexation_end"]."</h1>";	
		echo "<script>document.querySelector('#loader').remove();</script>";
		
	}

	//---------------------------FIN LLIUREX 26/09/2018------------------------

		
	if (!$param_licence) {
		include("$base_path/resume_licence.inc.php");
		print $PMB_texte_licence ;
	}

	print $main_layout_end;
	print $footer;
	pmb_mysql_close($dbh);
	exit ;

//-----------------------------------LLIUREX 26/09/2018------------------------

}elseif($required_indexation){
	echo "<script>document.querySelector('#llxwaiting').remove();</script>";
	echo "<script>document.querySelector('#loader').remove();</script>";
	echo "<h1>".$msg["indexation_progress"]."</h1>";
	show_loader(); 
	flush();
	ob_flush();
	include("./admin/misc/setup_initialization.inc.php");
	echo "<h1>".$msg["indexation_end"]."</h1>";	
	echo "<script>document.querySelector('#loader').remove();</script>";
	print $main_layout_end;
	print $footer;
	pmb_mysql_close($dbh);
	exit ;
}

//---------------------------- FIN LLIUREX 26/09/2018------------------------


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

go_first_tab();

pmb_mysql_close($dbh);
