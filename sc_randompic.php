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

$_language->read_module('sc_randompic');

//get files
$pic_array = Array();
$picpath = './images/userpics/';
$picdir = opendir($picpath);
while (false !== ($file = readdir($picdir))) {
	if ($file != "." && $file != ".." && $file != "nouserpic.gif" && is_file($picpath.$file) && $file!="Thumbs.db") {
		$pic_array[] = $file;
	}
}
closedir($picdir);

//sort array
natcasesort ($pic_array);
reset ($pic_array);

//get randompic
$anz = count($pic_array);
if($anz) {
	$the_pic = $pic_array[rand(0,($anz-1))];
	$picID = str_replace(strrchr($the_pic,'.'),'',$the_pic);
	$nickname = getnickname($picID);
	$nickname_fixed = getinput($nickname);
	$registerdate = getregistered($picID);
	$picurl = $picpath.$the_pic;

  	eval ("\$sc_randompic = \"".gettemplate("sc_randompic")."\";");
	echo $sc_randompic;
  
} else echo $_language->module['no_user'];

?>