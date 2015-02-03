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

if (isset($site)) {
    $_language->readModule('contact');
}

$title_contact = $GLOBALS["_template"]->replaceTemplate("title_contact", array());
echo $title_contact;

if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
    $action = '';
}

if ($action == "send") {
    $getemail = $_POST['getemail'];
    $subject = $_POST['subject'];
    $text = $_POST['text'];
    $text = str_replace('\r\n', "\n", $text);
    $name = $_POST['name'];
    $from = $_POST['from'];
    $run = 0;

    $fehler = array();
    if (!(mb_strlen(trim($name)))) {
        $fehler[] = $_language->module['enter_name'];
    }

    if (!validate_email($from)) {
        $fehler[] = $_language->module['enter_mail'];
    }
    if (!(mb_strlen(trim($subject)))) {
        $fehler[] = $_language->module['enter_subject'];
    }
    if (!(mb_strlen(trim($text)))) {
        $fehler[] = $_language->module['enter_message'];
    }

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "contact WHERE email='" . $getemail . "'");
    if (mysqli_num_rows($ergebnis) == 0) {
        $fehler[] = $_language->module['unknown_receiver'];
    }

    if ($userID) {
        $run = 1;
    } else {
        $CAPCLASS = new \webspell\Captcha;
        if (!$CAPCLASS->checkCaptcha($_POST['captcha'], $_POST['captcha_hash'])) {
            $fehler[] = $_language->module['wrong_securitycode'];
        } else {
            $run = 1;
        }
    }

    if (!count($fehler) && $run) {
        $message = stripslashes(
            'This mail was send over your webSPELL - Website (IP ' . $GLOBALS['ip'] . '): ' . $hp_url .
            '<br><br><strong>' . getinput($name) . ' writes:</strong><br>' . clearfromtags($text)
        );
        $sendmail = \webspell\Email::sendEmail($from, 'Contact', $getemail, stripslashes($subject), $message);

        if ($sendmail['result'] == 'fail') {
            if (isset($sendmail['debug'])) {
                $fehler[] = $sendmail['error'];
                $fehler[] = $sendmail['debug'];
                $showerror = generateErrorBoxFromArray($_language->module['errors_there'], $fehler);
            } else {
                $fehler[] = $sendmail['error'];
                $showerror = generateErrorBoxFromArray($_language->module['errors_there'], $fehler);
            }
        } else {
            if (isset($sendmail['debug'])) {
                $fehler[] = $sendmail[ 'debug' ];
                redirect(
                    'index.php?site=contact',
                    generateBoxFromArray($_language->module['send_successfull'], 'alert-success', $fehler),
                    3
                );
                unset($_POST['name']);
                unset($_POST['from']);
                unset($_POST['text']);
                unset($_POST['subject']);
            } else {
                redirect('index.php?site=contact', $_language->module['send_successfull'], 3);
                unset($_POST['name']);
                unset($_POST['from']);
                unset($_POST['text']);
                unset($_POST['subject']);
            }
        }
    } else {
        $showerror = generateErrorBoxFromArray($_language->module['errors_there'], $fehler);
    }
}

$getemail = '';
$ergebnis = safe_query("SELECT * FROM `" . PREFIX . "contact` ORDER BY `sort`");
while ($ds = mysqli_fetch_array($ergebnis)) {
    if ($getemail === $ds['email']) {
        $getemail .= '<option value="' . $ds['email'] . '" selected="selected">' . $ds['name'] . '</option>';
    } else {
        $getemail .= '<option value="' . $ds['email'] . '">' . $ds['name'] . '</option>';
    }
}

if ($loggedin) {
    if (!isset($showerror)) {
        $showerror = '';
    }
    $name = getinput(stripslashes(getnickname($userID)));
    $from = getinput(getemail($userID));
    if (isset($_POST['subject'])) {
        $subject = getforminput($_POST['subject']);
    } else {
        $subject = '';
    }
    if (isset($_POST['text'])) {
        $text = getforminput($_POST['text']);
    } else {
        $text = '';
    }

    $data_array = array();
    $data_array['$showerror'] = $showerror;
    $data_array['$getemail'] = $getemail;
    $data_array['$name'] = $name;
    $data_array['$from'] = $from;
    $data_array['$subject'] = $subject;
    $data_array['$text'] = $text;
    $contact_loggedin = $GLOBALS["_template"]->replaceTemplate("contact_loggedin", $data_array);
    echo $contact_loggedin;
} else {
    $CAPCLASS = new \webspell\Captcha;
    $captcha = $CAPCLASS->createCaptcha();
    $hash = $CAPCLASS->getHash();
    $CAPCLASS->clearOldCaptcha();
    if (!isset($showerror)) {
        $showerror = '';
    }
    if (isset($_POST['name'])) {
        $name = getforminput($_POST['name']);
    } else {
        $name = '';
    }
    if (isset($_POST['from'])) {
        $from = getforminput($_POST['from']);
    } else {
        $from = '';
    }
    if (isset($_POST['subject'])) {
        $subject = getforminput($_POST['subject']);
    } else {
        $subject = '';
    }
    if (isset($_POST['text'])) {
        $text = getforminput($_POST['text']);
    } else {
        $text = '';
    }

    $data_array = array();
    $data_array['$showerror'] = $showerror;
    $data_array['$getemail'] = $getemail;
    $data_array['$name'] = $name;
    $data_array['$from'] = $from;
    $data_array['$subject'] = $subject;
    $data_array['$text'] = $text;
    $data_array['$captcha'] = $captcha;
    $data_array['$hash'] = $hash;
    $contact_notloggedin = $GLOBALS["_template"]->replaceTemplate("contact_notloggedin", $data_array);
    echo $contact_notloggedin;
}
