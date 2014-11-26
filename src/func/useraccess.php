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

function isanyadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE userID='".$userID."' AND (page='1' OR forum='1' OR user='1' OR news='1' OR clanwars='1' OR feedback='1' OR super='1' OR gallery='1' OR cash='1' OR files='1') "));
	return $anz;
}

function issuperadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE super='1' AND userID='".$userID."'"));
	return $anz;
}

function isforumadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (forum='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function isfileadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (files='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function ispageadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (page='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function isfeedbackadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (feedback='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function isnewsadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (news='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function isnewswriter($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (news='1' OR super='1' OR news_writer='1') AND userID='".$userID."'"));
	return $anz;
}

function ispollsadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (polls='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function isclanwaradmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE (clanwars='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function ismoderator($userID, $boardID) {
	if(!$userID OR !$boardID) return false;
	else {
		if(!isanymoderator($userID)) return false;
		$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."forum_moderators WHERE userID='".$userID."' AND boardID='".$boardID."'"));
		return $anz;
	}
}

function isanymoderator($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE userID='".$userID."' AND moderator='1'"));
	return $anz;
}

function isuseradmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM `".PREFIX."user_groups` WHERE (user='1' OR super='1') AND userID='".$userID."'"));
	if(!$anz) $anz=issuperadmin($userID);
	return $anz;
}

function iscashadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM `".PREFIX."user_groups` WHERE (cash='1' OR super='1') AND userID='".$userID."'"));
	if(!$anz) $anz=issuperadmin($userID);
	return $anz;
}

function isgalleryadmin($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM `".PREFIX."user_groups` WHERE (gallery='1' OR super='1') AND userID='".$userID."'"));
	return $anz;
}

function isclanmember($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM `".PREFIX."squads_members` WHERE userID='".$userID."'"));
	if(!$anz) $anz=issuperadmin($userID);
	return $anz;
}

function isjoinusmember($userID) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM `".PREFIX."squads_members` WHERE userID='".$userID."'"));
	if(!$anz) $anz=issuperadmin($userID);
	return $anz;
}

function isbanned($userID) {
  $anz=mysql_num_rows(safe_query("SELECT userID FROM `".PREFIX."user` WHERE userID='$userID' AND (banned='perm' OR banned IS NOT NULL)"));
	return $anz;
}

function getusercomments($userID, $type) {
	$anz=mysql_num_rows(safe_query("SELECT commentID FROM `".PREFIX."comments` WHERE userID='".$userID."' AND type='".$type."'"));
	return $anz;
}

function iscommentposter($userID,$commID) {
	if(!$userID OR !$commID) return false;
	else {
		$anz = mysql_num_rows(safe_query("SELECT commentID FROM ".PREFIX."comments WHERE commentID='".$commID."' AND userID='".$userID."'"));
		return $anz;
	}
}

function isforumposter($userID, $postID) {
	$anz = mysql_num_rows(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE postID='".$postID."' AND poster='".$userID."'"));
	return $anz;
}

function istopicpost($topicID, $postID) {
	$ds=mysql_fetch_array(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE topicID='".$topicID."' ORDER BY date ASC LIMIT 0,1"));
	if($ds['postID']==$postID) return true;
	else return false;
}

function isinusergrp($usergrp, $userID, $sp=1) {
	if($usergrp == 'user' and $userID != 0) return 1;
	if(!usergrpexists($usergrp)) return 0;
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_forum_groups WHERE (`".$usergrp."`=1) AND userID='".$userID."'"));
	if($sp) if(!$anz) $anz=isforumadmin($userID);
	return $anz;
}

?>