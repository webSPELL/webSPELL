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

$_language->read_module('about');

eval ("\$title_about = \"".gettemplate("title_about")."\";");
echo $title_about;

$ergebnis=safe_query("SELECT * FROM ".PREFIX."about");
if(mysql_num_rows($ergebnis)) {
	$ds=mysql_fetch_array($ergebnis);

	$about=htmloutput($ds['about']);
	$about=toggle($about, 1);

	$bg1=BG_1;
	eval ("\$about = \"".gettemplate("about")."\";");
	echo $about;
}
else echo $_language->module['no_about'];


?>
