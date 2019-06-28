<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users.class.php,v 1.1 2017-03-16 15:07:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class users {
	
	public static function get_form_autorisations($param_autorisations="1", $on_create=1) {
		global $msg;
		global $PMBuserid;
	
		$query = "SELECT userid, username FROM users order by username ";
		$result = pmb_mysql_query($query);
		$all_users=array();
		while (list($all_userid,$all_username)=pmb_mysql_fetch_row($result)) {
			$all_users[]=array($all_userid,$all_username);
		}
		if ($on_create) $param_autorisations.=" ".$PMBuserid ;
	
		$autorisations_donnees=explode(" ",$param_autorisations);
	
		for ($i=0 ; $i<count($all_users) ; $i++) {
			if (array_search ($all_users[$i][0], $autorisations_donnees)!==FALSE) $autorisation[$i][0]=1;
			else $autorisation[$i][0]=0;
			$autorisation[$i][1]= $all_users[$i][0];
			$autorisation[$i][2]= $all_users[$i][1];
		}
		$autorisations_users="";
		$id_check_list='';
		while (list($row_number, $row_data) = each($autorisation)) {
			$id_check="auto_".$row_data[1];
			if($id_check_list)$id_check_list.='|';
			$id_check_list.=$id_check;
			if ($row_data[1]==1) $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='autorisations[]' id='$id_check' value='".$row_data[1]."' checked class='checkbox' readonly /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
			elseif ($row_data[0]) $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='autorisations[]' id='$id_check' value='".$row_data[1]."' checked class='checkbox' /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
			else $autorisations_users.="<span class='usercheckbox'><input type='checkbox' name='autorisations[]' id='$id_check' value='".$row_data[1]."' class='checkbox' /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
		}
		$autorisations_users.="<input type='hidden' id='auto_id_list' name='auto_id_list' value='$id_check_list' >";
		return $autorisations_users;
	}
	
} // fin de déclaration de la classe users