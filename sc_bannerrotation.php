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

$_language->read_module('sc_bannerrotation');

//get banner
$allbanner = safe_query("SELECT * FROM ".PREFIX."bannerrotation WHERE displayed='1' ORDER BY RAND() LIMIT 0,1");
$total = mysql_num_rows($allbanner);
if($total) {
	$banner = mysql_fetch_array($allbanner);
	echo '<a href="out.php?bannerID='.$banner['bannerID'].'" target="_blank"><img src="./images/bannerrotation/'.$banner['banner'].'" border="0" alt="'.htmlspecialchars($banner['bannername']).'" /></a>';
}
else echo $_language->module['no_banners'];
unset($banner);
?>
