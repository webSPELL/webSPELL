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

if ($userID) {
    $name_settings = 'value="' . getinput(getnickname($userID)) . '" readonly="readonly" ';
    $captcha_form = '';
} else {
    $name_settings = 'value="Name" onfocus="this.value=\'\'"';
    $CAPCLASS = new \webspell\Captcha;
    $captcha = $CAPCLASS->createCaptcha();
    $hash = $CAPCLASS->getHash();
    $CAPCLASS->clearOldCaptcha();
    $captcha_form =
        '<div class="form-group">
            <div class="input-group">
                <span class="input-group-addon captcha-img">' . $captcha . '</span>
                <input type="number" name="captcha" placeholder="Enter Captcha"  autocomplete="off"
                    class="form-control">
                <input name="captcha_hash" type="hidden" value="' . $hash . '">
            </div>
        </div>';
}

$_language->readModule('shoutbox');

$refresh = $sbrefresh * 1000;

eval ("\$shoutbox = \"" . gettemplate("shoutbox") . "\";");
echo $shoutbox;
