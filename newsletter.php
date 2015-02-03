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

$_language->readModule('newsletter');

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = '';
}

if ($action == "save") {
    $email = $_POST['email'];

    if (!validate_email($email)) {
        redirect(
            'index.php?site=newsletter',
            generateAlert($_language->module['email_not_valid'], 'alert-danger'),
            3
        );
    } else {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "newsletter WHERE email='" . $email . "'");
        if (!mysqli_num_rows($ergebnis)) {
            $pass = RandPass(7);

            safe_query(
                "INSERT INTO
                    " . PREFIX . "newsletter (
                        `email`,
                        `pass`
                    )
                    values (
                        '" . $email . "',
                        '" . $pass . "'
                    )"
            );

            $vars = array('%delete_key%', '%homepage_url%', '%mail%');
            $repl = array($pass, $hp_url, $email);
            $subject = $hp_title . ": " . $_language->module['newsletter_registration'];
            $message = str_replace(
                $vars,
                $repl,
                $_language->module['success_mail']
            );
            $sendmail = \webspell\Email::sendEmail($admin_email, 'Newsletter', $email, $subject, $message);

            if ($sendmail['result'] == 'fail') {
                redirect(
                    'index.php?site=newsletter',
                    generateErrorBox($sendmail["error"]),
                    3
                );
            } else {
                redirect(
                    'index.php?site=newsletter',
                    generateAlert($_language->module['thank_you_for_registration'], 'alert-success'),
                    3
                );
            }
        } else {
            redirect(
                'index.php?site=newsletter',
                generateAlert($_language->module['you_are_already_registered'], 'alert-warning'),
                3
            );
        }
    }
} elseif ($action == "delete") {
    $ergebnis = safe_query("SELECT pass FROM " . PREFIX . "newsletter WHERE email='" . $_POST['email'] . "'");
    $any = mysqli_num_rows($ergebnis);
    if ($any) {
        $dn = mysqli_fetch_array($ergebnis);

        if ($_POST['password'] == $dn['pass']) {
            safe_query("DELETE FROM " . PREFIX . "newsletter WHERE email='" . $_POST['email'] . "'");
            redirect(
                'index.php?site=newsletter',
                generateAlert($_language->module['your_mail_adress_deleted'], 'alert-success'),
                3
            );
        } else {
            redirect(
                'index.php?site=newsletter',
                generateAlert($_language->module['mail_pw_didnt_match'], 'alert-danger'),
                3
            );
        }
    } else {
        redirect(
            'index.php?site=newsletter',
            generateAlert($_language->module['mail_not_in_db'], 'alert-danger'),
            3
        );
    }
} elseif ($action == "forgot") {
    $ergebnis = safe_query("SELECT pass FROM " . PREFIX . "newsletter WHERE email='" . $_POST['email'] . "'");
    $dn = mysqli_fetch_array($ergebnis);

    if ($dn['pass'] != "") {
        $email = $_POST['email'];
        $pass = $dn['pass'];

        $vars = array('%delete_key%', '%homepage_url%', '%mail%');
        $repl = array($pass, $hp_url, $email);
        $subject = $hp_title . ": " . $_language->module['deletion_key'];
        $message = str_replace(
            $vars,
            $repl,
            $_language->module['request_mail']
        );
        $sendmail = \webspell\Email::sendEmail($admin_email, 'Newsletter', $email, $subject, $message);

        if ($sendmail['result'] == 'fail') {
            if (isset($sendmail['debug'])) {
                $fehler = array();
                $fehler[] = $sendmail[ 'error' ];
                $fehler[] = $sendmail[ 'debug' ];
                redirect(
                    'index.php?site=newsletter',
                    generateErrorBoxFromArray($_language->module['errors_there'], $fehler),
                    3
                );
            } else {
                $fehler = array();
                $fehler[] = $sendmail['error'];
                redirect(
                    'index.php?site=newsletter',
                    generateErrorBoxFromArray($_language->module['errors_there'], $fehler),
                    3
                );
            }
        } else {
            redirect(
                'index.php?site=newsletter',
                generateAlert($_language->module['password_had_been_send'], 'alert-success'),
                3
            );
        }
    } else {
        redirect(
            'index.php?site=newsletter',
            generateAlert($_language->module['no_such_mail_adress'], 'alert-danger'),
            3
        );
    }
} else {
    $usermail = getemail($userID);
    if (isset($_GET['mail'])) {
        $get_mail = getforminput($_GET['mail']);
    } else {
        $get_mail = '';
    }
    if ($get_mail == "") {
        $get_mail = $_language->module['mail_adress'];
    }
    if (isset($_GET['pass'])) {
        $get_pw = getforminput($_GET['pass']);
    } else {
        $get_pw = '';
    }
    if ($get_pw == "") {
        $get_pw = $_language->module['del_key'];
    }

    $newsletter_title = $GLOBALS["_template"]->replaceTemplate("title_newsletter", array());
    echo $newsletter_title;

    $data_array = array();
    $data_array['$usermail'] = $usermail;
    $data_array['$get_mail'] = $get_mail;
    $data_array['$get_pw'] = $get_pw;
    $newsletter = $GLOBALS["_template"]->replaceTemplate("newsletter", $data_array);
    echo $newsletter;
}
