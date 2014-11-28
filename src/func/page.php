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

function redirect($url, $info, $time=1) {
	if($url=="back" AND $info!='' AND isset($_SERVER['HTTP_REFERER'])) {
		$url = $_SERVER['HTTP_REFERER'];
		$info = '';
	} elseif($url=="back" AND $info!='') {
		$url = $info;
		$info = '';
	}
	echo '<meta http-equiv="refresh" content="'.$time.';URL='.$url.'"><br /><p style="color:#000000">'.$info.'</p><br /><br />';
}

function isStaticPage($staticID = null){
	if($GLOBALS['site'] != "static"){
		return false;
	}

	if($staticID != null){
		if($_GET['staticID'] != $staticID){
			return false;
		}
	}

	return true;
}

?>
