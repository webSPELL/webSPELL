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

$_language->read_module('out');

//get values
if(isset($_GET['bannerID'])) {
	safe_query("UPDATE ".PREFIX."bannerrotation SET hits=hits+1 WHERE bannerID='".$_GET['bannerID']."'");
	$ds = mysql_fetch_array(safe_query("SELECT bannerurl FROM ".PREFIX."bannerrotation WHERE bannerID='".$_GET['bannerID']."'"));
	$target='http://'.str_replace('http://', '', $ds['bannerurl']);
	$type = "direct";
}

if(isset($_GET['partnerID'])) {
	safe_query("UPDATE ".PREFIX."partners SET hits=hits+1 WHERE partnerID='".$_GET['partnerID']."'");
	$ds = mysql_fetch_array(safe_query("SELECT url FROM ".PREFIX."partners WHERE partnerID='".$_GET['partnerID']."'"));
	$target='http://'.str_replace('http://', '', $ds['url']);
	$type = "direct";
}

if(isset($_GET['sponsorID'])) {
	safe_query("UPDATE ".PREFIX."sponsors SET hits=hits+1 WHERE sponsorID='".$_GET['sponsorID']."'");
	$ds = mysql_fetch_array(safe_query("SELECT url FROM ".PREFIX."sponsors WHERE sponsorID='".$_GET['sponsorID']."'"));
	$target='http://'.str_replace('http://', '', $ds['url']);
	$type = "direct";
}

//output
if($type == "frame") {
	$pagetitle = PAGETITLE;

	eval("\$out_frame = \"".gettemplate("out_frame")."\";");
	echo $out_frame;

}
elseif($type == "direct")
header("Location: ".$target);
?>