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
#   Copyright 2005-2015 by webspell.org                                  #
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

$_language->readModule('login');

if ($loggedin) {
    $username =
        '<a href="index.php?site=profile&amp;id=' . $userID . '"><strong>' .
        strip_tags(getnickname($userID)) . '</strong></a>';
    if (isanyadmin($userID)) {
        $admin = '<li class="divider"></li><li><a href="admin/admincenter.php" target="_blank" class="alert-danger">' .
            $_language->module[ 'admin' ] . '</a></li>';
    } else {
        $admin = '';
    }
    if (isclanmember($userID) || iscashadmin($userID)) {
        $cashbox = '<li><a href="index.php?site=cashbox" class="alert-danger">' . $_language->module[ 'cash-box' ] .
            '</a></li><li class="divider"></li>';
    } else {
        $cashbox = '';
    }
    $anz = getnewmessages($userID);
    if ($anz) {
        $newmessages = $anz;
    } else {
        $newmessages = '';
    }
    if ($getavatar = getavatar($userID)) {
        $l_avatar = '<img src="images/avatars/' . $getavatar . '" alt="Avatar">';
    } else {
        $l_avatar = $_language->module[ 'n_a' ];
    }

    $data_array = array();
    $data_array['$username'] = $username;
    $data_array['$l_avatar'] = $l_avatar;
    $data_array['$newmessages'] = $newmessages;
    $data_array['$admin'] = $admin;
    $data_array['$cashbox'] = $cashbox;
    $logged = $GLOBALS["_template"]->replaceTemplate("logged", $data_array);
    echo $logged;
} else {
    //set sessiontest variable (checks if session works correctly)
    $_SESSION[ 'ws_sessiontest' ] = true;
    $loginform = $GLOBALS["_template"]->replaceTemplate("login", array());
    echo $loginform;
}
