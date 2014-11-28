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

function getanzcomments($id, $type) {
	return mysqli_num_rows(safe_query("SELECT commentID FROM `".PREFIX."comments` WHERE parentID='$id' AND type='$type'"));
}

function getlastcommentposter($id, $type) {
	$ds=mysqli_fetch_array(safe_query("SELECT userID, nickname FROM `".PREFIX."comments` WHERE parentID='$id' AND type='$type' ORDER BY date DESC LIMIT 0,1"));
	if($ds['userID'])
		return getnickname($ds['userID']);

	return htmlspecialchars($ds['nickname']);
}

function getlastcommentdate($id, $type) {
	$ds=mysqli_fetch_array(safe_query("SELECT date FROM `".PREFIX."comments` WHERE parentID='$id' AND type='$type' ORDER BY date DESC LIMIT 0,1"));
	return $ds['date'];
}

function getusernewsposts($userID) {
	return mysqli_num_rows(safe_query("SELECT newsID FROM `".PREFIX."news` WHERE poster='$userID' "));
}

function getusernewscomments($userID) {
	return mysqli_num_rows(safe_query("SELECT commentID FROM `".PREFIX."comments` WHERE userID='$userID' AND type='ne'"));
}

function getrubricname($rubricID) {
	$ds=mysqli_fetch_array(safe_query("SELECT rubric FROM `".PREFIX."news_rubrics` WHERE rubricID='$rubricID'"));
	return $ds['rubric'];
}

function getrubricpic($rubricID) {
	$ds=mysqli_fetch_array(safe_query("SELECT pic FROM `".PREFIX."news_rubrics` WHERE rubricID='$rubricID'"));
	return $ds['pic'];
}

function getlanguage($lang) {
	$ds=mysqli_fetch_array(safe_query("SELECT language FROM `".PREFIX."news_languages` WHERE lang='$lang'"));
	return $ds['language'];
}

function select_language($message_array) {
	if(isset($_SESSION['language'])){
		$i=0;
		foreach($message_array as $val) {
			if($val['lang'] == $_SESSION['language']) return $i;
			$i++;
		}
	}
	return 0;
}

function getlanguageid($lang, $message_array) {
	$i=0;
	foreach($message_array as $val) {
		if($val['lang'] == $lang) {
			$return = $i;
			break;
		}
		$i++;
	}
	if(isset($return)) return $return;
}

?>
