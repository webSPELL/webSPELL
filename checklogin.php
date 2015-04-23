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

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $ajax = true;
} else {
    $ajax = false;
}

$return = new stdClass();
$return->state = "failed";
$return->message = "";
$reenter = false;

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
                    $_SESSION[ 'referer' ] = $_SERVER[ 'HTTP_REFERER' ];
                    //remove sessiontest variable
                    if (isset($_SESSION[ 'ws_sessiontest' ])) {
                        unset($_SESSION[ 'ws_sessiontest' ]);
                    }
                    //cookie
                    \webspell\LoginCookie::set('ws_auth', $ds[ 'userID' ], $sessionduration * 60 * 60);

                    //Delete visitor with same IP from whoisonline
                    safe_query("DELETE FROM " . PREFIX . "whoisonline WHERE ip='" . $GLOBALS[ 'ip' ] . "'");
                    //Delete IP from failed logins
                    safe_query("DELETE FROM " . PREFIX . "failed_login_attempts WHERE ip = '" . $GLOBALS[ 'ip' ] . "'");
                    $return->state = "success";
                    $return->message = $_language->module[ 'login_successful' ];
                } elseif (!($ws_pwd == $ds[ 'password' ])) {
                    if ($sleep) {
                        sleep(3);
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
                    $reenter = true;
                    $return->message = $_language->module[ 'invalid_password' ];
                }
            } else {
                $return->message = $_language->module[ 'not_activated' ];
            }
        } else {
            $return->message = str_replace('%username%', htmlspecialchars($ws_user), $_language->module[ 'no_user' ]);
            $reenter = true;
        }
    }
} else {
    $data = mysqli_fetch_assoc($get);
    $return->message = str_replace('%reason%', $data[ 'reason' ], $_language->module[ 'ip_banned' ]);
}

if ($ajax === true) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    echo json_encode($return);
} else {
    if ($return->state == "success") {
        header("Location: index.php?site=loginoverview");
    } else {
        $message = $return->message;
        if ($reenter === true) {
            $message .= '<br><br>'.$_language->module[ 'return_reenter' ];
        } else {
            $message .= '<br><br>'.$_language->module[ 'return' ];
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="description" content="Clanpage using webSPELL 4 CMS">
            <meta name="author" content="webspell.org">
            <meta name="copyright" content="Copyright 2005-2015 by webspell.org">
            <meta name="generator" content="webSPELL">
            <title><?php echo PAGETITLE; ?></title>
            <link href="_stylesheet.css" rel="stylesheet" type="text/css">
        </head>
        <body>
        <table class="table">
            <tr>
                <td height="500" class="text-center">
                    <table width="350" border="0" cellpadding="10" cellspacing="0" style="border:1px solid <?php
                        echo BORDER; ?>" bgcolor="<?php
                            echo BG_1; ?>">
                        <tr>
                            <td class="text-center"><?php echo $message; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </body>
        </html>
        <?php
    }
}
