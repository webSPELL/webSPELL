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

if(isset($_GET['staticID'])) $staticID = $_GET['staticID'];
else $staticID = '';

$ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."static WHERE staticID='".$staticID."'"));
$_language->read_module("static");
$allowed = false; 
switch($ds['accesslevel']) {
	case 0: 
		$allowed = true; 
		break;
	case 1: 
		if($userID) $allowed = true; 
		break;
	case 2: 
		if(isclanmember($userID)) $allowed = true; 
		break;
}

if($allowed) {
 	$content = $ds['content'];
	echo toggle(htmloutput($content),1);
}
else{
	redirect("index.php",$_language->module['no_access'],3);
}
?>
