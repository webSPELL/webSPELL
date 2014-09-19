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

// copy pagelock information for session test + deactivated pagelock for checklogin
$closed_tmp = $closed;
$closed = 0;

include("_functions.php");

//settings

$sleep = 1; //idle status for script if password is wrong?

//settings end
$_language->read_module('checklogin');

$get = safe_query("SELECT * FROM ".PREFIX."banned_ips WHERE ip='".$GLOBALS['ip']."'");
if(mysql_num_rows($get) == 0){
	$ws_pwd = md5(stripslashes($_POST['pwd']));
	$ws_user = $_POST['ws_user'];
	
	$check = safe_query("SELECT * FROM ".PREFIX."user WHERE username='".$ws_user."'");
	$anz = mysql_num_rows($check);
	$login = 0;
	
	if(!$closed_tmp AND !isset($_SESSION['ws_sessiontest'])) {
		$error = $_language->module['session_error'];
	}
	else {
		if($anz) {
		
			$check = safe_query("SELECT * FROM ".PREFIX."user WHERE username='".$ws_user."' AND activated='1'");
			if(mysql_num_rows($check)) {
		
				$ds=mysql_fetch_array($check);
		
				// check password
				$login = 0;
				if($ws_pwd == $ds['password']) {
		
					//session
					$_SESSION['ws_auth'] = $ds['userID'].":".$ws_pwd;
					$_SESSION['ws_lastlogin'] = $ds['lastlogin'];
					$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
					//remove sessiontest variable
					if(isset($_SESSION['ws_sessiontest'])) unset($_SESSION['ws_sessiontest']);
					//cookie
					setcookie("ws_auth", $ds['userID'].":".$ws_pwd, time()+($sessionduration*60*60));					
					//Delete visitor with same IP from whoisonline
					safe_query("DELETE FROM ".PREFIX."whoisonline WHERE ip='".$GLOBALS['ip']."'");
					//Delete IP from failed logins
					safe_query("DELETE FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
					$login = 1;
					$error = $_language->module['login_successful'];
				}
				elseif(!($ws_pwd == $ds['password'])) {
					if($sleep) sleep(5);
					$get = safe_query("SELECT wrong FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
					if(mysql_num_rows($get)){
						safe_query("UPDATE ".PREFIX."failed_login_attempts SET wrong = wrong+1 WHERE ip = '".$GLOBALS['ip']."'");
					}
					else{
						safe_query("INSERT INTO ".PREFIX."failed_login_attempts (ip,wrong) VALUES ('".$GLOBALS['ip']."',1)");
					}
					$get = safe_query("SELECT wrong FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
					if(mysql_num_rows($get)){
						$ban = mysql_fetch_assoc($get);
						if($ban['wrong'] == $max_wrong_pw){
							$bantime = time() + (60*60*3); // 3 hours
							safe_query("INSERT INTO ".PREFIX."banned_ips (ip,deltime,reason) VALUES ('".$GLOBALS['ip']."',".$bantime.",'Possible brute force attack')");
							safe_query("DELETE FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
						}
					}
					$error= $_language->module['invalid_password'];
				}
			}
			else $error= $_language->module['not_activated'];
		
		}
		else $error=str_replace('%username%', htmlspecialchars($ws_user), $_language->module['no_user']);
	}
}
else{
	$login = 0;
	$data = mysql_fetch_assoc($get);
	$error = str_replace('%reason%', $data['reason'], $_language->module['ip_banned']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Clanpage using webSPELL 4 CMS" />
<meta name="author" content="webspell.org" />
<meta name="keywords" content="webspell, webspell4, clan, cms" />
<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
<meta name="generator" content="webSPELL" />
<title><?php echo PAGETITLE; ?></title>
<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
<?php if($login) { echo '<meta http-equiv="refresh" content="1;URL=index.php?site=loginoverview" />'; } ?>
</head>
<body bgcolor="<?php echo PAGEBG; ?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td height="500" align="center">
		<table width="350" border="0" cellpadding="10" cellspacing="0" style="border:1px solid <?php echo BORDER; ?>" bgcolor="<?php echo BG_1; ?>">
			<tr>
				<td align="center"><?php echo $error; ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>