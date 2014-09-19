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

function getuserforumtopics($userID) {
	$anz=mysql_num_rows(safe_query("SELECT topicID FROM ".PREFIX."forum_topics WHERE userID='$userID' "));
	return $anz;
}

function getuserforumposts($userID) {
	$anz=mysql_num_rows(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE poster='$userID'"));
	return $anz;
}

function getboardname($boardID) {
	$ds=mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."forum_boards WHERE boardID='$boardID'"));
	return $ds['name'];
}
function getcategoryname($catID){
	$ds=mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."forum_categories WHERE catID='$catID'"));
	return $ds['name'];
}

function gettopicname($topicID) {
	$ds=mysql_fetch_array(safe_query("SELECT topic FROM ".PREFIX."forum_topics WHERE topicID='$topicID'"));
	return $ds['topic'];
}

function redirect($url, $info, $time=1) {
	if($url=="back" AND $info!='' AND isset($_SERVER['HTTP_REFERER'])) {
		$url = $_SERVER['HTTP_REFERER'];
		$info = '';
	} elseif($url=="back" AND $info!='') {
		$url = $info;
		$info = '';
	}
	echo'<meta http-equiv="refresh" content="'.$time.';URL='.$url.'" />
  <br /><p style="color:#000000">'.$info.'</p><br /><br />';
}

function getmoderators($boardID) {
	$moderatoren=safe_query("SELECT * FROM ".PREFIX."forum_moderators WHERE boardID='$boardID'");
	$moderators = '';
	$j=1;
	while($dm=mysql_fetch_array($moderatoren)) {
		$username=getnickname($dm['userID']);
		if($j>1) $moderators .= ', <a href="index.php?site=profile&amp;id='.$dm['userID'].'">'.$username.'</a>';
		else $moderators = '<a href="index.php?site=profile&amp;id='.$dm['userID'].'">'.$username.'</a>';
		$j++;
	}
	return $moderators;
}

function getlastpost($topicID) {
	$ds=mysql_fetch_array(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE topicID='$topicID' ORDER BY postID DESC LIMIT 0,1"));
	return $ds['postID'];
}

function getboardid($topicID) {
	$ds=mysql_fetch_array(safe_query("SELECT boardID FROM ".PREFIX."forum_topics WHERE topicID='".$topicID."' LIMIT 0,1"));
	return $ds['boardID'];
}

function usergrpexists($fgrID) {
	$anz=mysql_num_rows(safe_query("SELECT fgrID FROM ".PREFIX."forum_groups WHERE fgrID='$fgrID'"));
	return $anz;
}
function boardexists($boardID){
	$anz = mysql_num_rows(safe_query("SELECT name FROM ".PREFIX."forum_boards WHERE boardID='$boardID'"));
	return $anz;
}
?>