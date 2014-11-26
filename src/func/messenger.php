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

function getnewmessages($userID) {
	$anz=mysql_num_rows(safe_query("SELECT messageID FROM `".PREFIX."messenger` WHERE touser='$userID' AND userID='$userID' AND viewed='0'"));
	return $anz;
}

function sendmessage($touser,$title,$message,$from='0') {

	global $hp_url,$admin_email,$admin_name, $hp_title;
	$_language_tmp = new Language;
	$systemmail = false;
	if(!$from) { $systemmail = true; $from = '1'; }

	if(!$systemmail) {
		safe_query("INSERT INTO ".PREFIX."messenger (userID, date, fromuser, touser, title, message, viewed) values('$from', '".time()."', '$from', '$touser', '$title', '".$message."', '0')"); 
		safe_query("UPDATE ".PREFIX."user SET pmsent=pmsent+1 WHERE userID='$from'"); 
	}
	if(!isignored($touser, $from) OR $systemmail) {
		if($touser != $from || $systemmail) {
			safe_query("INSERT INTO ".PREFIX."messenger (userID, date, fromuser, touser, title, message, viewed) VALUES ('$touser', '".time()."', '$from', '$touser', '$title', '".$message."', '0')"); 
		}
		safe_query("UPDATE ".PREFIX."user SET pmgot=pmgot+1 WHERE userID='$touser'"); 
		if(wantmail($touser) AND isonline($touser)=="offline") {
			$ds=mysql_fetch_array(safe_query("SELECT email, language FROM ".PREFIX."user WHERE userID='$touser'"));
			$_language_tmp->set_language($ds['language']);
			$_language_tmp->read_module('messenger');
			$mail_body = str_replace("%nickname%",getnickname($touser),$_language_tmp->module['mail_body']);
			$mail_body = str_replace("%hp_url%",$hp_url,$mail_body);
			mail($ds['email'], $hp_title.': '.$_language_tmp->module['mail_subject'], $mail_body, "Content-Type: text/html; charset=utf-8\nFrom: ".$admin_email."\n");
		}
	}
}

?>