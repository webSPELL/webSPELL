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

global $userID, $loggedin, $default_language, $_language;

$userID = 0;
$loggedin = false;

if(isset($_SESSION['ws_auth'])) {
	if(stristr($_SESSION['ws_auth'], "userid") === false){
		$authent = explode(":", $_SESSION['ws_auth']);

		$ws_user = $authent[0];
		$ws_pwd = $authent[1];

		if(isset($ws_user) and isset($ws_pwd)) {
			$check = safe_query("SELECT userID, language FROM ".PREFIX."user WHERE userID='".$ws_user."' AND password='".$ws_pwd."'");

			while($ds = mysql_fetch_array($check)) {
				$loggedin = true;
				$userID = $ds['userID'];
				if(!empty($ds['language']) AND isset($_language)) {
					if($_language->set_language($ds['language'])) {
						$_SESSION['language'] = $ds['language'];
					}
				}
			}
		}
	}
	else die();
}

if(!isset($_SESSION['language'])) {
	$_SESSION['language'] = $default_language;
}

?>