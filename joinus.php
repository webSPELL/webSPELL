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

//options

$showonlygamingsquads = true;  //only show gaming squads (=true) or show all squads (=false)?

//php below this line ;)

if (isset($site)) {
    $_language->readModule('joinus');
}

$title_joinus = $GLOBALS["_template"]->replaceTemplate("title_joinus", array());
echo $title_joinus;

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = "";
}
$show = true;
if ($action == "save" && isset($_POST['post'])) {
    if (isset($_POST['squad'])) {
        $squad = $_POST['squad'];
    } else {
        $squad = 0;
    }
    $nick = $_POST['nick'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $messenger = $_POST['messenger'];
    $age = $_POST['age'];
    $city = $_POST['city'];
    $clanhistory = $_POST['clanhistory'];
    $info = $_POST['info'];
    $run = 0;

    $error = array();
    if (!(mb_strlen(trim($nick)))) {
        $error[] = $_language->module['forgot_nickname'];
    }
    if (!(mb_strlen(trim($name)))) {
        $error[] = $_language->module['forgot_realname'];
    }
    if (!validate_email($email)) {
        $error[] = $_language->module['email_not_valid'];
    }
    if (!(mb_strlen(trim($messenger)))) {
        $error[] = $_language->module['forgot_messenger'];
    }
    if (!(mb_strlen(trim($age)))) {
        $error[] = $_language->module['forgot_age'];
    }
    if (!(mb_strlen(trim($city)))) {
        $error[] = $_language->module['forgot_city'];
    }
    if (!(mb_strlen(trim($clanhistory)))) {
        $error[] = $_language->module['forgot_history'];
    }

    if ($userID) {
        $run = 1;
    } else {
        $CAPCLASS = new \webspell\Captcha;
        if (!$CAPCLASS->checkCaptcha($_POST['captcha'], $_POST['captcha_hash'])) {
            $error[] = $_language->module['wrong_security_code'];
        } else {
            $run = 1;
        }
    }

    if (!count($error) && $run) {
        $touser = array();
        $ergebnis =
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "squads_members
                WHERE
                    joinmember='1'
                AND
                    squadID='" . $squad . "'"
            );
        while ($ds = mysqli_fetch_assoc($ergebnis)) {
            $touser[] = $ds['userID'];
        }
        if (!count($touser)) {
            $touser[] = 1;
        }
        $tmp_lang = new \webspell\Language();
        foreach ($touser as $id) {
            $tmp_lang->setLanguage(getuserlanguage($id));
            $tmp_lang->readModule('joinus');
            $message = '[b]' . $tmp_lang->module['someone_want_to_join_your_squad'] . ' ' .
                $_database->escape_string(getsquadname($squad)) . '![/b]
                ' . $tmp_lang->module['nick'] . ' ' . $nick . '
                ' . $tmp_lang->module['name'] . ': ' . $name . '
                ' . $tmp_lang->module['age'] . ': ' . $age . '
                ' . $tmp_lang->module['mail'] . ': [email]' . $email . '[/email]
                ' . $tmp_lang->module['messenger'] . ': ' . $messenger . '
                ' . $tmp_lang->module['city'] . ': ' . $city . '
                ' . $tmp_lang->module['clan_history'] . ': ' . $clanhistory . '

                ' . $tmp_lang->module['info'] . ':
                ' . $info .'';
            sendmessage($id, $tmp_lang->module['message_title'], $message);
        }
        echo generateAlert($_language->module['thanks_you_will_get_mail'], 'alert-success');
        unset($_POST['nick'],
            $_POST['name'],
            $_POST['email'],
            $_POST['messenger'],
            $_POST['age'],
            $_POST['city'],
            $_POST['clanhistory'],
            $_POST['info']);
        $show = false;
    } else {
        $show = true;
        $showerror = generateErrorBoxFromArray($_language->module['problems'], $error);
    }
}
if ($show === true) {
    if ($showonlygamingsquads) {
        $squads = getgamesquads();
    } else {
        $squads = getsquads();
    }

    $bg1 = BG_1;

    if ($loggedin) {
        if (!isset($showerror)) {
            $showerror = '';
        }
        $res = safe_query(
            "SELECT
                *, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y') 'age'
            FROM
                " . PREFIX . "user
            WHERE
                userID = '$userID'"
        );
        $ds = mysqli_fetch_assoc($res);
        $nickname = getinput($ds['nickname']);
        $name = getinput($ds['firstname'] . " " . $ds['lastname']);
        $email = getinput($ds['email']);
        $messenger = getinput($ds['icq']);
        $age = $ds['age'];
        $city = getinput($ds['town']);

        if (isset($_POST['clanhistory'])) {
            $clanhistory = getforminput($_POST['clanhistory']);
        } else {
            $clanhistory = '';
        }
        if (isset($_POST['info'])) {
            $info = getforminput($_POST['info']);
        } else {
            $info = '';
        }

        $data_array = array();
        $data_array['$showerror'] = $showerror;
        $data_array['$squads'] = $squads;
        $data_array['$nickname'] = $nickname;
        $data_array['$name'] = $name;
        $data_array['$email'] = $email;
        $data_array['$messenger'] = $messenger;
        $data_array['$age'] = $age;
        $data_array['$city'] = $city;
        $data_array['$clanhistory'] = $clanhistory;
        $data_array['$info'] = $info;
        $joinus_loggedin = $GLOBALS["_template"]->replaceTemplate("joinus_loggedin", $data_array);
        echo $joinus_loggedin;
    } else {
        $CAPCLASS = new \webspell\Captcha;
        $captcha = $CAPCLASS->createCaptcha();
        $hash = $CAPCLASS->getHash();
        $CAPCLASS->clearOldCaptcha();

        if (!isset($showerror)) {
            $showerror = '';
        }
        if (isset($_POST['nick'])) {
            $nick = getforminput($_POST['nick']);
        } else {
            $nick = '';
        }
        if (isset($_POST['name'])) {
            $name = getforminput($_POST['name']);
        } else {
            $name = '';
        }
        if (isset($_POST['email'])) {
            $email = getforminput($_POST['email']);
        } else {
            $email = '';
        }
        if (isset($_POST['messenger'])) {
            $messenger = getforminput($_POST['messenger']);
        } else {
            $messenger = '';
        }
        if (isset($_POST['age'])) {
            $age = getforminput($_POST['age']);
        } else {
            $age = '';
        }
        if (isset($_POST['city'])) {
            $city = getforminput($_POST['city']);
        } else {
            $city = '';
        }
        if (isset($_POST['clanhistory'])) {
            $clanhistory = getforminput($_POST['clanhistory']);
        } else {
            $clanhistory = '';
        }
        if (isset($_POST['info'])) {
            $info = getforminput($_POST['info']);
        } else {
            $info = '';
        }

        $data_array = array();
        $data_array['$showerror'] = $showerror;
        $data_array['$squads'] = $squads;
        $data_array['$info'] = $info;
        $data_array['$captcha'] = $captcha;
        $data_array['$hash'] = $hash;
        $joinus_notloggedin = $GLOBALS["_template"]->replaceTemplate("joinus_notloggedin", $data_array);
        echo $joinus_notloggedin;
    }
}
