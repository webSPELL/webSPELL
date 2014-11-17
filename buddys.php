<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2011 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('buddys');
	if(!$userID) redirect('index.php?site=buddys', $_language->module['not_logged'], 3);
	elseif(!isset($_GET['id']) OR !is_numeric($_GET['id'])) redirect('index.php?site=buddys', $_language->module['add_nouserid'], 3);
	else {
		if($_GET['id'] == $userID){ 
			redirect('index.php?site=buddys', $_language->module['add_yourself'], 3);
			die();
		}
		if(mysqli_num_rows(safe_query("SELECT userID FROM ".PREFIX."user WHERE userID='".$_GET['id']."'"))) {
			safe_query("INSERT INTO ".PREFIX."buddys (userID, buddy, banned) values ('$userID', '".$_GET['id']."', '0') ");
			header("Location: index.php?site=buddys");
		}
		else redirect('index.php?site=buddys', $_language->module['add_notexists'], 3);
	}
}
elseif($action=="ignore") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('buddys');
	if(!$userID) redirect('index.php?site=buddys', $_language->module['not_logged'], 3);
	elseif(!isset($_GET['id']) OR !is_numeric($_GET['id'])) redirect('index.php?site=buddys', $_language->module['add_nouserid'], 3);
	else {
		if($_GET['id'] == $userID){
			redirect('index.php?site=buddys', $_language->module['add_yourself'], 3);
			die();
		}	
		if(mysqli_num_rows(safe_query("SELECT userID FROM ".PREFIX."user WHERE userID='".$_GET['id']."'"))) {
			safe_query("UPDATE ".PREFIX."buddys SET banned='1' WHERE userID='$userID' AND buddy='".$_GET['id']."'");
			header("Location: index.php?site=buddys");
		}
		else redirect('index.php?site=buddys', $_language->module['add_notexists'], 3);
	}
}
elseif($action=="readd") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('buddys');
	if(!$userID) redirect('index.php?site=buddys', $_language->module['not_logged'], 3);
	elseif(!isset($_GET['id']) OR !is_numeric($_GET['id'])) redirect('index.php?site=buddys', $_language->module['add_nouserid'], 3);
	else {
		safe_query("UPDATE ".PREFIX."buddys SET banned='0' WHERE userID='$userID' AND buddy='".$_GET['id']."'");
		header("Location: index.php?site=buddys");
	}
}
elseif($action=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('buddys');
	if(!$userID) redirect('index.php?site=buddys', $_language->module['not_logged'], 3);
	elseif(!isset($_GET['id']) OR !is_numeric($_GET['id'])) redirect('index.php?site=buddys', $_language->module['add_nouserid'], 3);
	else {
		safe_query("DELETE FROM ".PREFIX."buddys WHERE userID='$userID' AND buddy='".$_GET['id']."'");
		header("Location: index.php?site=buddys");
	}
}
elseif($userID) {

	$_language->read_module('buddys');

	eval ("\$title_buddys = \"".gettemplate("title_buddys")."\";");
	echo $title_buddys;

	eval ("\$buddys_head = \"".gettemplate("buddys_head")."\";");
	echo $buddys_head;
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."buddys WHERE userID='$userID' AND banned='0'");
	$anz=mysqli_num_rows($ergebnis);
	if($anz) {
		$n=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$flag = '[flag]'.getcountry($ds['buddy']).'[/flag]';
			$country=flags($flag);
			$nickname=getnickname($ds['buddy']);
			if(isclanmember($ds['buddy'])) $member='<img src="images/icons/member.gif" width="6" height="11" alt="Clanmember" />';
			else $member='';
			if(isonline($ds['buddy'])=="offline") $statuspic='<img src="images/icons/offline.gif" width="7" height="7" alt="offline" />';
			else $statuspic='<img src="images/icons/online.gif" width="7" height="7" alt="online" />';

			eval ("\$buddys_content = \"".gettemplate("buddys_content")."\";");
			echo $buddys_content;
			$n++;
		}
	}
	else echo'<tr><td colspan="4" bgcolor="'.BG_1.'">'.$_language->module['buddy_nousers'].'</td></tr>';

	eval ("\$buddys_foot = \"".gettemplate("buddys_foot")."\";");
	echo $buddys_foot;

	eval ("\$ignore_head = \"".gettemplate("ignore_head")."\";");
	echo $ignore_head;
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."buddys WHERE userID='$userID' AND banned='1'");
	$anz=mysqli_num_rows($ergebnis);
	if($anz) {
		$n=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$flag = '[flag]'.getcountry($ds['buddy']).'[/flag]';
			$country=flags($flag);
			$nickname=getnickname($ds['buddy']);
			if(isclanmember($ds['buddy'])) $member=' <img src="images/icons/member.gif" width="6" height="11" alt="Clanmember" />';
			else $member='';
			if(isonline($ds['buddy'])=="offline") $statuspic='<img src="images/icons/offline.gif" width="7" height="7" alt="offline" />';
			else $statuspic='<img src="images/icons/online.gif" width="7" height="7" alt="online" />';
			eval ("\$ignore_content = \"".gettemplate("ignore_content")."\";");
			echo $ignore_content;
			$n++;
		}
	}
	else echo $_language->module['ignore_nousers'];

	eval ("\$ignore_foot = \"".gettemplate("ignore_foot")."\";");
	echo $ignore_foot;

}
else {

	$_language->read_module('buddys');
	if(!$userID) redirect('index.php?site=buddys', $_language->module['not_logged'], 3);

}

?>