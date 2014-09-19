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

$_language->read_module('report');
if(isset($run)) $run=1; else $run=0;
if($userID) $run=1;
else {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha($_POST['captcha'], $_POST['captcha_hash'])) $run=1;
}

if($_POST['mode'] and $run) {
	$mode = $_POST['mode'];
	$type = $_POST['type'];
	$info = $_POST['description'];
	$id = $_POST['id'];

	if($info) {
		$info = clearfromtags($info);
	}
	else $info = $_language->module['no_informations'];

	$date = time();
	$message = sprintf($_language->module['report_message'], $mode, $type, $id, $info, $id);

	//send message to file-admins

	$ergebnis=safe_query("SELECT userID FROM ".PREFIX."user_groups WHERE files='1'");
	while($ds=mysql_fetch_array($ergebnis)) sendmessage($ds['userID'], $type.': '.$mode, $message);

	redirect("index.php?site=".$type, $_language->module['report_recognized'], "3");
}
else echo $_language->module['wrong_securitycode'];

?>