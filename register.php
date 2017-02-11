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

// #read database entries (?)
$_admin_minpasslen = "6";
$_admin_maxpasslen = "18"; #empty = no max
$_admin_musthavelow = true;
$_admin_musthaveupp = true;
$_admin_musthavenum = true;
$_admin_musthavespec = true;

// #chk pass function
function pass_complex($pwd,$_admin_minpasslen,$_admin_maxpasslen,$_admin_musthavelow,$_admin_musthaveupp,$_admin_musthavenum,$_admin_musthavespec) {
    if ($_admin_musthavelow==true) { $_pwd_low = "(?=\S*[a-z])"; } else { $_pwd_low=""; }
    if ($_admin_musthaveupp==true) { $_pwd_upp = "(?=\S*[A-Z])"; } else { $_pwd_upp=""; }
    if ($_admin_musthavenum==true) { $_pwd_num = "(?=\S*[\d])"; } else { $_pwd_num=""; }
    if ($_admin_musthavespec==true) { $_pwd_spec = "(?=\S*[\W])"; } else { $_pwd_spec=""; }
    if (!preg_match_all('$\S*(?=\S{'.$_admin_minpasslen.','.$_admin_maxpasslen.'})'.$_pwd_low.$_pwd_upp.$_pwd_num.$_pwd_spec.'\S*$', $pwd)) { return false; }
return true;
}

$_language->readModule('register');

$title_register = $GLOBALS["_template"]->replaceTemplate("title_register", array());
echo $title_register;
$show = true;
if (isset($_POST['save'])) {
    if (!$loggedin) {
        $username = mb_substr(trim($_POST['username']), 0, 30);
        $nickname = htmlspecialchars(mb_substr(trim($_POST['nickname']), 0, 30));
        $password = $_POST['password'];
        $md5pwd = generatePasswordHash(stripslashes($password));

        $mail = $_POST['mail'];
        $CAPCLASS = new \webspell\Captcha;

        $error = array();

        // check nickname
        if (!(mb_strlen(trim($nickname)))) {
            $error[] = $_language->module['enter_nickname'];
        }

        // check nickname inuse
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "user WHERE nickname = '$nickname' ");
        $num = mysqli_num_rows($ergebnis);
        if ($num) {
            $error[] = $_language->module['nickname_inuse'];
        }

        // check username
        if (!(mb_strlen(trim($username)))) {
            $error[] = $_language->module['enter_username'];
        } elseif (mb_strlen(trim($username)) > 30) {
            $error[] = $_language->module['username_toolong'];
        }

        // check username inuse
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "user WHERE username = '$username' ");
        $num = mysqli_num_rows($ergebnis);
        if ($num) {
            $error[] = $_language->module['username_inuse'];
        }

        // check passwort
        if (pass_complex($password,$_admin_minpasslen,$_admin_maxpasslen,$_admin_musthavelow,$_admin_musthaveupp,$_admin_musthavenum,$_admin_musthavespec)==false) {
            $error[] = $_language->module['enter_password2'];
        }

        // check e-mail
        if (!validate_email($mail)) {
            $error[] = $_language->module['invalid_mail'];
        }

        // check e-mail inuse
        $ergebnis = safe_query("SELECT userID FROM " . PREFIX . "user WHERE email = '$mail' ");
        $num = mysqli_num_rows($ergebnis);
        if ($num) {
            $error[] = $_language->module['mail_inuse'];
        }

        // check captcha
        if (!$CAPCLASS->checkCaptcha($_POST['captcha'], $_POST['captcha_hash'])) {
            $error[] = $_language->module['wrong_securitycode'];
        }

        // check exisitings accounts from ip with same password
        $get_users =
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user
                WHERE
                    password='$md5pwd' AND
                    ip='" . $GLOBALS['ip'] . "'"
            );
        if (mysqli_num_rows($get_users)) {
            $error[] = 'Only one Account per IP';
        }

        if (count($error)) {
            $_language->readModule('formvalidation', true);
            $showerror = generateErrorBoxFromArray($_language->module['errors_there'], $error);
        } else {
            // insert in db
            $registerdate = time();
            $activationkey = md5(RandPass(20));
            $activationlink = 'http://' . $hp_url . '/index.php?site=register&key=' . $activationkey;

            safe_query(
                "INSERT INTO
                    `" . PREFIX . "user` (
                        `registerdate`,
                        `lastlogin`,
                        `username`,
                        `password`,
                        `nickname`,
                        `email`,
                        `newsletter`,
                        `activated`,
                        `ip`,
                        `date_format`,
                        `time_format`
                    )
                    VALUES (
                        '$registerdate',
                        '$registerdate',
                        '$username',
                        '$md5pwd',
                        '$nickname',
                        '$mail',
                        '0',
                        '" . $activationkey . "',
                        '" . $GLOBALS['ip'] . "',
                        '" . $default_format_date . "',
                        '" . $default_format_time . "'
                    )"
            );

            $insertid = mysqli_insert_id($_database);

            // insert in user_groups
            safe_query("INSERT INTO " . PREFIX . "user_groups ( userID ) values('$insertid' )");

            // mail to user
            $ToEmail = $mail;
            $header = str_replace(
                array('%username%', '%activationlink%', '%pagetitle%', '%homepage_url%'),
                array(stripslashes($username), stripslashes($activationlink), $hp_title, $hp_url),
                $_language->module['mail_subject']
            );
            $Message = str_replace(
                array('%username%', '%activationlink%', '%pagetitle%', '%homepage_url%'),
                array(stripslashes($username), stripslashes($activationlink), $hp_title, $hp_url),
                $_language->module['mail_text']
            );
            $sendmail = \webspell\Email::sendEmail($admin_email, 'Register', $ToEmail, $header, $Message);

            if ($sendmail['result'] == 'fail') {
                if (isset($sendmail['debug'])) {
                    $fehler = array();
                    $fehler[] = $sendmail[ 'error' ];
                    $fehler[] = $sendmail[ 'debug' ];
                    redirect(
                        "index.php",
                        generateErrorBoxFromArray($_language->module['mail_failed'], $fehler),
                        10
                    );
                    $show = false;
                } else {
                    $fehler = array();
                    $fehler[] = $sendmail['error'];
                    redirect(
                        "index.php",
                        generateErrorBoxFromArray($_language->module['mail_failed'], $fehler),
                        10
                    );
                    $show = false;
                }
            } else {
                if (isset($sendmail['debug'])) {
                    $fehler = array();
                    $fehler[] = $sendmail[ 'debug' ];
                    redirect(
                        "index.php",
                        generateBoxFromArray($_language->module['register_successful'], 'alert-success', $fehler),
                        10
                    );
                    $show = false;
                } else {
                    redirect("index.php", $_language->module['register_successful'], 3);
                    $show = false;
                }
            }
        }
    } else {
        redirect(
            "index.php?site=register",
            str_replace('%pagename%', $GLOBALS['hp_title'], $_language->module['no_register_when_loggedin']),
            3
        );
    }
}
if (isset($_GET['key'])) {
    safe_query("UPDATE `" . PREFIX . "user` SET activated='1' WHERE activated='" . $_GET['key'] . "'");
    if (mysqli_affected_rows($_database)) {
        redirect('index.php?site=login', $_language->module['activation_successful'], 3);
    } else {
        redirect('index.php?site=login', $_language->module['wrong_activationkey'], 3);
    }
} elseif (isset($_GET['mailkey'])) {
    if (mb_strlen(trim($_GET['mailkey'])) == 32) {
        safe_query(
            "UPDATE
                `" . PREFIX . "user`
            SET
                email_activate='1',
                email=email_change,
                email_change=''
            WHERE
                email_activate='" . $_GET['mailkey'] . "'"
        );
        if (mysqli_affected_rows($_database)) {
            redirect('index.php?site=login', $_language->module['mail_activation_successful'], 3);
        } else {
            redirect('index.php?site=login', $_language->module['wrong_activationkey'], 3);
        }
    }
} else {
    if ($show === true) {
        if (!$loggedin) {
            $CAPCLASS = new \webspell\Captcha;
            $captcha = $CAPCLASS->createCaptcha();
            $hash = $CAPCLASS->getHash();
            $CAPCLASS->clearOldCaptcha();

            if (!isset($showerror)) {
                $showerror = '';
            }
            if (isset($_POST['nickname'])) {
                $nickname = getforminput($_POST['nickname']);
            } else {
                $nickname = '';
            }
            if (isset($_POST['username'])) {
                $username = getforminput($_POST['username']);
            } else {
                $username = '';
            }
            if (isset($_POST['password'])) {
                $password = getforminput($_POST['password']);
            } else {
                $password = '';
            }
            if (isset($_POST['mail'])) {
                $mail = getforminput($_POST['mail']);
            } else {
                $mail = '';
            }

            $data_array = array();
            $data_array['$showerror'] = $showerror;
            $data_array['$nickname'] = $nickname;
            $data_array['$username'] = $username;
            $data_array['$password'] = $password;
            $data_array['$mail'] = $mail;
            $data_array['$captcha'] = $captcha;
            $data_array['$hash'] = $hash;
            $register = $GLOBALS["_template"]->replaceTemplate("register", $data_array);
            echo $register;
        } else {
            redirect(
                "index.php",
                str_replace(
                    '%pagename%',
                    $GLOBALS['hp_title'],
                    $_language->module['no_register_when_loggedin']
                ),
                3
            );
        }
    }
}
