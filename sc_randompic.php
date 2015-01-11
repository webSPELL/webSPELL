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
#   Copyright 2005-2014 by webspell.org                                  #
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

$_language->readModule('sc_randompic');

//get files
$pic_array = array();
$picpath = './images/userpics/';
$picdir = opendir($picpath);
while (false !== ($file = readdir($picdir))) {
    if ($file != "." && $file != ".." && $file != "nouserpic.gif" && is_file($picpath . $file) && $file != "Thumbs.db"
    ) {
        $pic_array[ ] = $file;
    }
}
closedir($picdir);

//sort array
natcasesort($pic_array);
reset($pic_array);

//get randomPic
$anz = count($pic_array);
if ($anz) {
    $the_pic = $pic_array[ rand(0, ($anz - 1)) ];
    $picID = str_replace(strrchr($the_pic, '.'), '', $the_pic);
    $nickname = getnickname($picID);
    $nickname_fixed = getinput($nickname);
    $registerdate = getregistered($picID);
    $picurl = $picpath . $the_pic;

    $data_array = array();
    $data_array['$picID'] = $picID;
    $data_array['$picurl'] = $picurl;
    $data_array['$nickname_fixed'] = $nickname_fixed;
    $data_array['$nickname'] = $nickname;
    $data_array['$registerdate'] = $registerdate;
    $sc_randompic = $GLOBALS["_template"]->replaceTemplate("sc_randompic", $data_array);
    echo $sc_randompic;
} else {
    echo $_language->module[ 'no_user' ];
}
