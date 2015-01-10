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

include("_mysql.php");
include("_settings.php");

// copy pagelock information for session test + deactivated pagelock for checklogin
$closed_tmp = $closed;
$closed = 0;

include("_functions.php");

//settings

$sleep = 1; //idle status for script if password is wrong?

//settings end
$_language->readModule('checklogin');

$get = safe_query("SELECT * FROM " . PREFIX . "banned_ips WHERE ip='" . $GLOBALS[ 'ip' ] . "'");
if (mysqli_num_rows($get) == 0) {
    $ws_pwd = generatePasswordHash(stripslashes($_POST[ 'pwd' ]));
    $ws_user = $_POST[ 'ws_user' ];

    $check = safe_query("SELECT * FROM " . PREFIX . "user WHERE username='" . $ws_user . "'");
    $anz = mysqli_num_rows($check);
    $login = 0;

    if (!$closed_tmp && !isset($_SESSION[ 'ws_sessiontest' ])) {
        $error = $_language->module[ 'session_error' ];
    } else {
        if ($anz) {
            $check = safe_query("SELECT * FROM " . PREFIX . "user WHERE username='" . $ws_user . "' AND activated='1'");
            if (mysqli_num_rows($check)) {
                $ds = mysqli_fetch_array($check);

                // check password
                $login = 0;
                if ($ws_pwd == $ds[ 'password' ]) {
                    //session
                    $_SESSION[ 'ws_auth' ] = $ds[ 'userID' ] . ":" . $ws_pwd;
                    $_SESSION[ 'ws_lastlogin' ] = $ds[ 'lastlogin' ];
                    $_SESSION[ 'referer' ] = $_SERVER[ 'HTTP_REFERER' ];
                    //remove sessiontest variable
                    if (isset($_SESSION[ 'ws_sessiontest' ])) {
                        unset($_SESSION[ 'ws_sessiontest' ]);
                    }
                    //cookie
                    $cookieName = "ws_auth";
                    $cookieValue = $ds[ 'userID' ] . ":" . $ws_pwd;
                    $cookieExpire = time() + ($sessionduration * 60 * 60);
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
                    //Delete visitor with same IP from whoisonline
                    safe_query("DELETE FROM " . PREFIX . "whoisonline WHERE ip='" . $GLOBALS[ 'ip' ] . "'");
                    //Delete IP from failed logins
                    safe_query("DELETE FROM " . PREFIX . "failed_login_attempts WHERE ip = '" . $GLOBALS[ 'ip' ] . "'");
                    $login = 1;
                    $error = $_language->module[ 'login_successful' ];
                } elseif (!($ws_pwd == $ds[ 'password' ])) {
                    if ($sleep) {
                        sleep(5);
                    }
                    $get = safe_query(
                        "SELECT
                            `wrong`
                        FROM
                            `" . PREFIX . "failed_login_attempts`
                        WHERE
                            `ip` = '" . $GLOBALS[ 'ip' ]."'"
                    );
                    if (mysqli_num_rows($get)) {
                        safe_query(
                            "UPDATE
                                `" . PREFIX . "failed_login_attempts`
                            SET
                                `wrong` = wrong+1 WHERE ip = '" . $GLOBALS[ 'ip' ]."'"
                        );
                    } else {
                        safe_query(
                            "INSERT INTO
                                `" . PREFIX . "failed_login_attempts` (
                                    `ip`,
                                    `wrong`
                                )
                                VALUES (
                                    '" . $GLOBALS[ 'ip' ] . "',
                                    1
                                )"
                        );
                    }
                    $get = safe_query(
                        "SELECT
                            `wrong`
                        FROM
                            `" . PREFIX . "failed_login_attempts`
                        WHERE
                            `ip` = '" . $GLOBALS[ 'ip' ]."'"
                    );
                    if (mysqli_num_rows($get)) {
                        $ban = mysqli_fetch_assoc($get);
                        if ($ban[ 'wrong' ] == $max_wrong_pw) {
                            $bantime = time() + (60 * 60 * 3); // 3 hours
                            safe_query(
                                "INSERT INTO
                                    `" . PREFIX . "banned_ips` (
                                        `ip`,
                                        `deltime`,
                                        `reason`
                                    )
                                    VALUES (
                                        '" . $GLOBALS[ 'ip' ] . "',
                                        " . $bantime . ",
                                        'Possible brute force attack'
                                    )"
                            );
                            safe_query(
                                "DELETE FROM
                                    `" . PREFIX . "failed_login_attempts`
                                WHERE
                                    `ip` = '" . $GLOBALS[ 'ip' ]."'"
                            );
                        }
                    }
                    $error = $_language->module[ 'invalid_password' ];
                }
            } else {
                $error = $_language->module[ 'not_activated' ];
            }
        } else {
            $error = str_replace('%username%', htmlspecialchars($ws_user), $_language->module[ 'no_user' ]);
        }
    }
} else {
    $login = 0;
    $data = mysqli_fetch_assoc($get);
    $error = str_replace('%reason%', $data[ 'reason' ], $_language->module[ 'ip_banned' ]);
}

if ($login) {
    header("Location: index.php?site=loginoverview");
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="description" content="Clanpage using webSPELL 4 CMS">
    <meta name="author" content="webspell.org">
    <meta name="keywords" content="webspell, webspell4, clan, cms">
    <meta name="generator" content="webSPELL">

    <!-- Head & Title include -->
    <title><?php echo PAGETITLE; ?></title>
    <base href="<?php echo $rewriteBase; ?>">
    <?php foreach ($components['css'] as $component) {
        echo '<link href="' . $component . '" rel="stylesheet">';
}
    ?>
    <link href="_stylesheet.css" rel="stylesheet">
	<style>
		html, body{
			height:100%; margin:0;padding:0
			}

		.container-fluid{
			height:100%;
			display:table;
			width: 100%;
			padding: 0;
			}

		.row-fluid {
			height: 100%; 
			display:table-cell; 
			vertical-align: middle;
			}

		.centering {
			float:none;
			margin:0 auto;
			}
	</style>
    </head>
    <body>
	<div class="container-fluid">
    <div class="row-fluid">
       <div class="alert alert-danger centering text-center" role="alert"><?php echo $error; ?><div>
    </div>
	</div>
    </body>
    </html>
    <?php
}
