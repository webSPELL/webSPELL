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

function getuserid($nickname) {
	$ds=mysql_fetch_array(safe_query("SELECT userID FROM ".PREFIX."user WHERE nickname='".$nickname."'"));
	return $ds['userID'];
}

function getnickname($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT nickname FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return $ds['nickname'];
}

function getuserdescription($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT userdescription FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['userdescription']);
}

function getfirstname($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT firstname FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['firstname']);
}

function getlastname($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT lastname FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['lastname']);
}

function getbirthday($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT birthday FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return date("d.m.Y", $ds['birthday']);
}

function gettown($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT town FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['town']);
}

function getemail($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT email FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['email']);
}

function getemailhide($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT email_hide FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['email_hide']);
}

function gethomepage($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT homepage FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return str_replace('http://', '', getinput($ds['homepage']));
}

function geticq($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT icq FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['icq']);
}

function getcountry($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT country FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['country']);
}

function getuserlanguage($userID){
	$ds=mysql_fetch_array(safe_query("SELECT language FROM ".PREFIX."user WHERE userID='".$userID."'"));
	return getinput($ds['language']);
}

function getuserpic($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT userpic FROM ".PREFIX."user WHERE userID='".$userID."'"));
	if(!$ds['userpic']) $userpic="nouserpic.gif";
	else $userpic=$ds['userpic'];
	return $userpic;
}

function getavatar($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT avatar FROM ".PREFIX."user WHERE userID='".$userID."'"));
	if(!$ds['avatar']) $avatar="noavatar.gif";
	else $avatar=$ds['avatar'];
	return $avatar;
}

function getsignatur($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT usertext FROM ".PREFIX."user WHERE userID='".$userID."'"));
	$clearsignatur=strip_tags($ds['usertext']);
	return $clearsignatur;
}

function getregistered($userID) {
	$ds=mysql_fetch_array(safe_query("SELECT registerdate FROM ".PREFIX."user WHERE userID='".$userID."'"));
	$date=date("d.m.Y", $ds['registerdate']);
	return $date;
}

function usergroupexists($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE userID='".$userID."'"));
	return $anz;
}

function wantmail($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user WHERE userID='".$userID."' AND mailonpm='1'"));
	return $anz;
}

function isbuddy($userID, $buddy) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."buddys WHERE buddy='".$buddy."' AND userID='".$userID."'"));
	if($anz) {
		$ergebnis=safe_query("SELECT * FROM ".PREFIX."buddys WHERE buddy='".$buddy."' AND userID='".$userID."'");
		$ds=mysql_fetch_array($ergebnis);
		if($ds['banned']==0) return 1;
	}
	else return 0;
}

function RandPass($length, $type=0) {

	/* Randpass: Generates an random password
	Parameter:
	length - length of the password string
	type - there are 4 types: 0 - all chars, 1 - numeric only, 2 - upper chars only, 3 - lower chars only
	Example:
	echo RandPass(7, 1); => 0917432
	*/

	for ($i = 0; $i < $length; $i++) {

		if($type==0) $rand = rand(1,3);
		else $rand = $type;
    
    if(!isset($pass)) { $pass = ''; }

		switch($rand) {
			case 1: $pass .= chr(rand(48,57)); break;
			case 2: $pass .= chr(rand(65,90)); break;
			case 3: $pass .= chr(rand(97,122)); break;
		}
	}
	return $pass;
}

function isonline($userID) {
	$ergebnis=safe_query("SELECT site FROM ".PREFIX."whoisonline WHERE userID='$userID'");
	$anz=mysql_num_rows($ergebnis);
	if($anz) {
		$ds=mysql_fetch_array($ergebnis);
		return '<b>online</b> @ <a href="index.php?site='.$ds['site'].'">'.$ds['site'].'</a>';
	}
	else return 'offline';
}
?>