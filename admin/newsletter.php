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

$_language->read_module('newsletter');

if(!isuseradmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['newsletter'].'</h1>';

if(isset($_POST['send']) || isset($_POST['testen'])) {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$title = $_POST['title'];
		$testmail = $_POST['testmail'];
		$date=date("d.m.Y", time());
		$message=str_replace('\r\n', "\n", $_POST['message']);
		$message_html=nl2br($message);
		$receptionists = $_language->module['receptionists'];
		$error_send = $_language->module['error_send'];
		
		//use page's default language for newsletter
		$_language->set_language($default_language);
		$_language->read_module('newsletter');
    	$no_htmlmail = $_language->module['no_htmlmail'];
    	$remove = $_language->module['remove'];
    	$profile = $_language->module['profile'];

    	$emailbody = '<!--
'.$no_htmlmail.'
'.stripslashes($message).'
 --> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>webSPELL Newsletter</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style type="text/css">
<!--
body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; background-color: #FFFFFF; border: 0px; margin: 5px; }
h3 {font-size: 16px; color: #515151; margin: 10px; padding: 0px; margin-top: 25px; text-align: center; }
img { border: none; }
.center { margin-left: auto; margin-right: auto; }
#newsletter { width: 650px; }
#footer { color: #8C8C8C; }
hr { height: 1px; background-color: #cdcdcd; color: #cdcdcd; border: none; margin: 6px 0px; }
a { color: #0066FF; text-decoration: none; }
a:hover { text-decoration: underline; }
-->
</style>
<!--[if lte IE 7]>
<style type="text/css">
hr { margin: 0px; }
</style>
<![endif]-->
	</head>
	<body>
		<div id="newsletter" class="center">
		<a href="http://'.$hp_url.'" target="_blank" ><img src="http://'.$hp_url.'/images/banner.gif" alt="" class="center" style="display: block;" /></a>
			<h3>'.stripslashes($title).'</h3>
			<span>'.stripslashes($message_html).'</span>
			<hr />
			<span id="footer">'.$remove.' <a href="http://'.$hp_url.'/index.php?site=myprofile">'.$profile.'</a>.</span>
		</div>
	</body>
</html>'; 
			
		if(isset($_POST['testen'])){
			$bcc[] = $testmail;
			$_SESSION['emailbody'] = $message;
			$_SESSION['title'] = $title;
		}
		else {
	
			//clanmember
	
			if(isset($_POST['sendto_clanmembers'])) {
	
				$ergebnis=safe_query("SELECT userID FROM ".PREFIX."squads_members GROUP BY userID");
				$anz=mysql_num_rows($ergebnis);
				if($anz) {
					while($ds=mysql_fetch_array($ergebnis)) {
						$emails[] = getemail($ds['userID']);
					}
				}
			}
	
			if(isset($_POST['sendto_registered'])) {
	
				$ergebnis=safe_query("SELECT * FROM ".PREFIX."user WHERE newsletter='1'");
				$anz=mysql_num_rows($ergebnis);
				if($anz) {
					while($ds=mysql_fetch_array($ergebnis)) {
						$emails[] = $ds['email'];
					}
				}
	
			}
	
			if(isset($_POST['sendto_newsletter'])) {
	
				$ergebnis=safe_query("SELECT * FROM ".PREFIX."newsletter");
				$anz=mysql_num_rows($ergebnis);
				if($anz) {
					while($ds=mysql_fetch_array($ergebnis)) {
						$emails[] = $ds['email'];
					}
				}
			}
	
			$bcc=$emails;
		}
	
		$header = "From:".addslashes($admin_name)." <$admin_email>\n";
		$header .= "Reply-To:".addslashes($admin_email)."\n";
		$header .= "Content-Type: text/html; charset=utf-8\n";
	
		$success = true;
		$bcc=array_unique($bcc);
		foreach($bcc as $mailto) {
			if(!mail($mailto, $hp_title." Newsletter",$emailbody,$header)) $succces = false;
		}
		if($success) echo '<b>'.$receptionists.'</b><br /><br />'.implode(", ",$bcc);
		else echo'<b>'.$error_send.'</b>';
		redirect("admincenter.php?site=newsletter", "", 5);
	} else echo $_language->module['transaction_invalid'];
}
else {
 	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
	if(isset($_SESSION['emailbody'])) $message = htmlspecialchars(stripslashes($_SESSION['emailbody']));
	else $message = null;
	if(isset($_SESSION['title'])) $title = htmlspecialchars(stripslashes($_SESSION['title']));
	else $title = null;
?>

<form action="admincenter.php?site=newsletter" method="post">
<table width="100%" border="0" cellspacing="1" cellpadding="3">
  <tr>
    <td width="15%"><b><?php echo $_language->module['title']; ?></b></td>
    <td width="85%"><input type="text" name="title" value="<?php echo $title;?>" size="97" /></td>
  </tr>
  <tr>
    <td valign="top"><b><?php echo $_language->module['html_mail']; ?></b></td>
    <td><textarea rows="30" cols="" style="width: 100%;" name="message"><?php echo $message;?></textarea></td>
  </tr>
  <tr>
    <td><b><?php echo $_language->module['test_newsletter']; ?></b></td>
    <td><input type="text" name="testmail" value="user@inter.net" size="30" />
    <input type="submit" value="<?php echo $_language->module['test']; ?>" name="testen" /></td>
  </tr>
  <tr>
    <td><b><?php echo $_language->module['send_to']; ?></b></td>
    <td><input type="checkbox" name="sendto_clanmembers" value="1" checked="checked" /> <?php echo $_language->module['user_clanmembers']; ?> [<?php echo mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."squads_members GROUP BY userID")).'&nbsp;'.$_language->module['users']; ?>]
    <br /><input type="checkbox" name="sendto_registered" value="1" checked="checked" /> <?php echo $_language->module['user_registered']; ?> [<?php echo mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."user WHERE newsletter='1'")).'&nbsp;'.$_language->module['users']; ?>]
    <br /><input type="checkbox" name="sendto_newsletter" value="1" checked="checked" /> <?php echo $_language->module['user_newsletter']; ?> [<?php echo mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."newsletter")).'&nbsp;'.$_language->module['users']; ?>]</td>
  </tr>
  <tr>
    <td><input type="hidden" name="captcha_hash" value="<?php echo $hash; ?>" /></td>
    <td><br /><input type="submit" name="send" value="<?php echo $_language->module['send']; ?>" /></td>
  </tr>
</table>
</form> 
<?php 
}
?>