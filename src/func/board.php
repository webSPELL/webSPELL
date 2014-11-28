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
	return (mysqli_num_rows(safe_query("SELECT topicID FROM ".PREFIX."forum_topics WHERE userID='$userID' ")) > 0);
}

function getuserforumposts($userID) {
	return (mysqli_num_rows(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE poster='$userID'")) > 0);
}

function getboardname($boardID) {
	$ds=mysqli_fetch_array(safe_query("SELECT name FROM ".PREFIX."forum_boards WHERE boardID='$boardID'"));
	return $ds['name'];
}
function getcategoryname($catID){
	$ds=mysqli_fetch_array(safe_query("SELECT name FROM ".PREFIX."forum_categories WHERE catID='$catID'"));
	return $ds['name'];
}

function gettopicname($topicID) {
	$ds=mysqli_fetch_array(safe_query("SELECT topic FROM ".PREFIX."forum_topics WHERE topicID='$topicID'"));
	return $ds['topic'];
}

function getmoderators($boardID) {
	$moderatoren=safe_query("SELECT * FROM ".PREFIX."forum_moderators WHERE boardID='$boardID'");
	$moderators = '';
	$j=1;
	while($dm=mysqli_fetch_array($moderatoren)) {
		$username=getnickname($dm['userID']);
		if($j>1) $moderators .= ', <a href="index.php?site=profile&amp;id='.$dm['userID'].'">'.$username.'</a>';
		else $moderators = '<a href="index.php?site=profile&amp;id='.$dm['userID'].'">'.$username.'</a>';
		$j++;
	}
	return $moderators;
}

function getlastpost($topicID) {
	$ds=mysqli_fetch_array(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE topicID='$topicID' ORDER BY postID DESC LIMIT 0,1"));
	return $ds['postID'];
}

function getboardid($topicID) {
	$ds=mysqli_fetch_array(safe_query("SELECT boardID FROM ".PREFIX."forum_topics WHERE topicID='".$topicID."' LIMIT 0,1"));
	return $ds['boardID'];
}

function usergrpexists($fgrID) {
	return (mysqli_num_rows(safe_query("SELECT fgrID FROM ".PREFIX."forum_groups WHERE fgrID='$fgrID'")) > 0);
}
function boardexists($boardID){
	return (mysqli_num_rows(safe_query("SELECT name FROM ".PREFIX."forum_boards WHERE boardID='$boardID'")) > 0);
}
?>
