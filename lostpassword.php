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

$_language->read_module('lostpassword');

eval ("\$title_lostpassword = \"".gettemplate("title_lostpassword")."\";");
echo $title_lostpassword;

if(isset($_POST['submit'])) {
	$email = trim($_POST['email']);
	if($email!=''){
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."user WHERE email = '".$email."'");
		$anz = mysql_num_rows($ergebnis);
	
		if($anz) {
	
			$newpwd=RandPass(6);
			$newmd5pwd=md5($newpwd);
	
			$ds = mysql_fetch_array($ergebnis);
			safe_query("UPDATE ".PREFIX."user SET password='".$newmd5pwd."' WHERE userID='".$ds['userID']."'");
	
			$ToEmail = $ds['email'];
			$ToName = $ds['username'];
			$vars = Array('%pagetitle%', '%username%', '%new_password%', '%homepage_url%');
			$repl = Array($hp_title, $ds['username'], $newpwd, $hp_url);
			$header = str_replace($vars, $repl, $_language->module['email_subject']);
			$Message = str_replace($vars, $repl, $_language->module['email_text']);
	
			if(mail($ToEmail,$header, $Message, "From:".$admin_email."\nContent-type: text/plain; charset=utf-8\n"))
			echo str_replace($vars, $repl, $_language->module['successful']);
			else echo $_language->module['email_failed'];
	
	
		}
		else {
			echo $_language->module['no_user_found'];
		}
	}
	else{
		echo $_language->module['no_mail_given'];
	}
}
else {
	echo'<form method="post" action="index.php?site=lostpassword">
		 '.$_language->module['your_email'].': <input type="text" name="email" size="25" /> <input type="submit" name="submit" value="'.$_language->module['get_password'].'" />
		 </form>';
}

?>