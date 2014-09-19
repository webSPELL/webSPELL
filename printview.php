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

include("_mysql.php");
include("_settings.php");
include("_functions.php");
$_language->read_module('forum');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo PAGETITLE; ?></title>
	<style type="text/css">
	<!--
	body {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		color: #000000;
	}
	table {
		font-family: Verdana, Arial, Helvetica, sans-serif;
		font-size: 12px;
		color: #000000;
	}
	-->
	</style>
</head>

<body>
<?php
$topic = $_GET['topic'];
$thread = safe_query("SELECT * FROM ".PREFIX."forum_topics WHERE topicID='$topic' ");

if(mysql_num_rows($thread)) {

	$dt = mysql_fetch_array($thread);

	if($dt['readgrps'] != "") {
		$usergrps = explode(";", $dt['readgrps']);
		$usergrp = 0;
		foreach($usergrps as $value) {
			if(isinusergrp($value, $userID)) {
				$usergrp = 1;
				break;
			}
		}
		if(!$usergrp and !ismoderator($userID, $dt['boardID'])) die($_language->module['no_access']);
	}

	$ergebnis = safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE boardID='$dt[boardID]' ");
	$db = mysql_fetch_array($ergebnis);
	$boardname = $db['name'];

	echo'<div style="width:640px;">
  <table width="640" cellpadding="2" cellspacing="0" border="0" bgcolor="#CCCCCC">
    <tr bgcolor="FFFFFF">
      <td><b>'.$boardname.'</b> &#8226; <b>'.getinput($dt['topic']).'</b></td>
    </tr>
  </table><hr size="1" /><br />';

	echo'<table width="100%" cellpadding="4" cellspacing="1" border="0">';

	$replys = safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE topicID='$topic' ORDER BY date");
	while($dr=mysql_fetch_array($replys)) {
		$date=date("d.m.Y", $dr['date']);
		$time=date("H:i", $dr['date']);

		$message=cleartext($dr['message']);	$username=getnickname($dr['poster']);

		if(getsignatur($dr['poster'])) $signatur='<br /><br />'.getsignatur($dr['poster']);
		else $signatur='';
		$posts = getuserforumposts($dr['poster']);
		if(isforumadmin($dr['poster']) || ismoderator($dr['poster'], $dt['boardID'])) {
			if(ismoderator($dr['poster'], $dt['boardID'])) {
				$usertype="Moderator";
				$rang='<img src="images/icons/ranks/moderator.gif" alt="" />';
			}
			if(isforumadmin($dr['poster'])) {
				$usertype="Administrator";
				$rang='<img src="images/icons/ranks/admin.gif" alt="" />';
			}
		}
		else {
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_ranks WHERE $posts > postmin AND $posts < postmax");
			$ds=mysql_fetch_array($ergebnis);
			$usertype=$ds['rank'];
			$rang='<img src="images/icons/ranks/'.$ds['pic'].'" alt="" />';
		}

		echo'<tr bgcolor="FFFFFF">
        <td valign="top"><i>'.$date.', '.$time.' </i> - <b>'.$username.'</b> <font size="1">- '.$usertype.' - '.$posts.' '.$_language->module['posts'].'</font>
        <br />'.$message.' <font size="1"><i>'.$signatur.'</i></font><br />&nbsp;</td>
      </tr>';
	}
	echo'</table><br /></div>';
}
?>
</body>
</html>