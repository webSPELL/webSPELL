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

session_name('ws_session');
session_start();

// unset session variables
$_SESSION = array();

// remove session cookie
if (isset($_COOKIE[ session_name() ])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

session_destroy();

// remove login cookie
if (isset($_COOKIE[ 'ws_auth' ])) {
    $cookieName = "ws_auth";
    $cookieValue = '';
    $cookieExpire = time() - (24 * 60 * 60);
    if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
        $cookieInfo = session_get_cookie_params();
        setcookie(
            $cookieName,
            $cookieValue,
            $cookieExpire,
            $cookieInfo[ 'path' ],
            $cookieInfo[ 'domain' ],
            $cookieInfo[ 'secure' ],
            true
        );
    } else {
        setcookie($cookieName, $cookieValue, $cookieExpire);
    }
    unset($cookieName);
    unset($cookieValue);
    unset($cookieExpire);
    unset($cookieInfo);
}

header("Location: index.php");
