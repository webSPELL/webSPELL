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

function getanzcwcomments($cwID) {
	$anz=mysql_num_rows(safe_query("SELECT commentID FROM `".PREFIX."comments` WHERE parentID='$cwID' AND type='cw'"));
	return $anz;
}

function getsquads() {
	$squads="";
  $ergebnis=safe_query("SELECT * FROM ".PREFIX."squads");
	while($ds=mysql_fetch_array($ergebnis)) {
		$squads.='<option value="'.$ds['squadID'].'">'.$ds['name'].'</option>';
	}
	return $squads;
}

function getgamesquads() {
	$squads = '';
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad='1'");
	while($ds=mysql_fetch_array($ergebnis)) {
		$squads.='<option value="'.$ds['squadID'].'">'.$ds['name'].'</option>';
	}
	return $squads;
}

function getsquadname($squadID) {
	$ds=mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."squads WHERE squadID='$squadID'"));
	return $ds['name'];
}

function issquadmember($userID, $squadID) {
	$anz=mysql_num_rows(safe_query("SELECT sqmID FROM ".PREFIX."squads_members WHERE userID='$userID' AND squadID='$squadID'"));
	return $anz;
}

function isgamesquad($squadID) {
	$anz=mysql_num_rows(safe_query("SELECT squadID FROM ".PREFIX."squads WHERE squadID='".$squadID."' AND gamesquad='1'"));
	return $anz;
}

function getgamename($tag) {
	$ds=mysql_fetch_array(safe_query("SELECT name FROM `".PREFIX."games` WHERE tag='$tag'"));
	return $ds['name'];
}
function is_gametag($tag){
	$anz = mysql_num_rows(safe_query("SELECT name FROM `".PREFIX."games` WHERE tag='$tag'"));
	return $anz;
}
?>