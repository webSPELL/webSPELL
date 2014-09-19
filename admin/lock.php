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

$_language->read_module('lock');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; <a href="admincenter.php?site=settings" class="white">'.$_language->module['settings'].'</a> &raquo; '.$_language->module['pagelock'].'</h1>';

if(!$closed) {

	if(isset($_POST['submit']) != "" AND ispageadmin($userID)) {
		$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
			if(mysql_num_rows(safe_query("SELECT * FROM `".PREFIX."lock`")))
			safe_query("UPDATE `".PREFIX."lock` SET reason='".$_POST['reason']."', time='".time()."'");
			else safe_query("INSERT INTO `".PREFIX."lock` (`time`, `reason`) values( '".time()."', '".$_POST['reason']."') ");
			safe_query("UPDATE `".PREFIX."settings` SET closed='1'");
			
	    	redirect("admincenter.php?site=lock", $_language->module['page_locked'], 3);
	    } else{
			redirect("admincenter.php?site=lock", $_language->module['transaction_invalid'], 3);
		}
    
	}
	else {
		$ergebnis=safe_query("SELECT * FROM `".PREFIX."lock`");
		$ds=mysql_fetch_array($ergebnis);
		$CAPCLASS = new Captcha;
		$CAPCLASS->create_transaction();
		$hash = $CAPCLASS->get_hash();
    
		echo'<form method="post" action="admincenter.php?site=lock">
    <b>'.$_language->module['pagelock'].'</b><br /><small>'.$_language->module['you_can_use_html'].'</small><br /><br />
    <textarea name="reason" rows="30" cols="" style="width: 100%;">'.getinput($ds['reason']).'</textarea><br /><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" />
    <input type="submit" name="submit" value="'.$_language->module['lock'].'" />
    </form>';
	}
}

else {

	if(isset($_POST['submit']) != "" AND isset($_POST['unlock']) AND ispageadmin($userID)) {
		$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
			safe_query("UPDATE `".PREFIX."settings` SET closed='0'");		
    		redirect("admincenter.php?site=lock",$_language->module['page_unlocked'],3);
    	} else{
			redirect("admincenter.php?site=lock", $_language->module['transaction_invalid'], 3);
		}
	}
	else {
		$ergebnis=safe_query("SELECT * FROM `".PREFIX."lock`");
		$ds=mysql_fetch_array($ergebnis);
		$CAPCLASS = new Captcha;
		$CAPCLASS->create_transaction();
		$hash = $CAPCLASS->get_hash();
    
		echo'<form method="post" action="admincenter.php?site=lock">
    '.$_language->module['locked_since'].'&nbsp;'.date("d.m.Y - H:i",$ds['time']).'.<br /><br />
    <input type="checkbox" name="unlock" /> '.$_language->module['unlock_page'].'<br /><br />
    <input type="hidden" name="captcha_hash" value="'.$hash.'" />
    <input type="submit" name="submit" value="'.$_language->module['unlock'].'" />
    </form>';
	}
}
?>