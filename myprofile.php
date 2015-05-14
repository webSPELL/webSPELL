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

$_language->readModule('myprofile');

if (!$userID) {
    $title_myprofile = $GLOBALS[ "_template" ]->replaceTemplate("title_myprofile", array());
    echo $title_myprofile;
    echo generateAlert($_language->module[ 'not_logged_in' ], 'alert-danger');
} else {
    $showerror = '';
    $title_myprofile = $GLOBALS[ "_template" ]->replaceTemplate("title_myprofile", array());
    echo $title_myprofile;

    if (isset($_POST[ 'submit' ])) {
        $nickname = htmlspecialchars(mb_substr(trim($_POST[ 'nickname' ]), 0, 30));
        if (isset($_POST[ 'mail' ])) {
            $mail = $_POST[ 'mail' ];
        } else {
            $mail = "";
        }
        if (isset($_POST[ 'mail_hide' ])) {
            $mail_hide = true;
        } else {
            $mail_hide = false;
        }
        $usernamenew = mb_substr(trim($_POST[ 'usernamenew' ]), 0, 30);
        $usertext = $_POST[ 'usertext' ];
        $firstname = $_POST[ 'firstname' ];
        $lastname = $_POST[ 'lastname' ];
        $birthday = date("Y-m-d", strtotime($_POST[ 'birthday' ]));
        $sex = $_POST[ 'sex' ];
        $flag = preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST[ 'flag' ]);
        $town = $_POST[ 'town' ];
        $icq = $_POST[ 'icq' ];
        $icq = str_replace('-', '', $icq); // Replace -
        $about = $_POST[ 'messageabout' ];
        $clantag = $_POST[ 'clantag' ];
        $clanname = $_POST[ 'clanname' ];
        $clanhp = $_POST[ 'clanhp' ];
        $clanirc = $_POST[ 'clanirc' ];
        $clanhistory = $_POST[ 'clanhistory' ];
        $cpu = $_POST[ 'cpu' ];
        $mainboard = $_POST[ 'mainboard' ];
        $monitor = $_POST[ 'monitor' ];
        $ram = $_POST[ 'ram' ];
        $hdd = $_POST[ 'hdd' ];
        $graphiccard = $_POST[ 'graphiccard' ];
        $soundcard = $_POST[ 'soundcard' ];
        $connection = $_POST[ 'connection' ];
        $keyboard = $_POST[ 'keyboard' ];
        $mouse = $_POST[ 'mouse' ];
        $mousepad = $_POST[ 'mousepad' ];
        $headset = $_POST[ 'headset' ];
        $newsletter = $_POST[ 'newsletter' ];
        $homepage = str_replace('http://', '', $_POST[ 'homepage' ]);
        $pm_mail = $_POST[ 'pm_mail' ];
        $language = $_POST[ 'language' ];
        $date_format = $_POST[ 'date_format' ];
        $time_format = $_POST[ 'time_format' ];
        if ($user_guestbook) {
            $user_gbook = $_POST[ 'user_guestbook' ];
        } else {
            $user_gbook = 1;
        }
        $id = $userID;

        $error_array = array();

        if (isset($_POST[ 'userID' ]) || isset($_GET[ 'userID' ]) || $userID == "") {
            die (generateAlert($_language->module[ 'not_logged_in' ], 'alert-danger'));
        }

        if (isset($_POST[ 'delavatar' ])) {
            $filepath = "./images/avatars/";
            if (file_exists($filepath . $id . '.gif')) {
                @unlink($filepath . $id . '.gif');
            }
            if (file_exists($filepath . $id . '.jpg')) {
                @unlink($filepath . $id . '.jpg');
            }
            if (file_exists($filepath . $id . '.png')) {
                @unlink($filepath . $id . '.png');
            }
            safe_query("UPDATE " . PREFIX . "user SET avatar='' WHERE userID='" . $id . "'");
        }
        if (isset($_POST[ 'deluserpic' ])) {
            $filepath = "./images/userpics/";
            if (file_exists($filepath . $id . '.gif')) {
                @unlink($filepath . $id . '.gif');
            }
            if (file_exists($filepath . $id . '.jpg')) {
                @unlink($filepath . $id . '.jpg');
            }
            if (file_exists($filepath . $id . '.png')) {
                @unlink($filepath . $id . '.png');
            }
            safe_query("UPDATE " . PREFIX . "user SET userpic='' WHERE userID='" . $id . "'");
        }

        //avatar
        $filepath = "./images/avatars/";

        $_language->readModule('formvalidation', true);

        $upload = new \webspell\HttpUpload('avatar');
        if (!$upload->hasFile()) {
            $upload = new \webspell\UrlUpload($_POST[ 'avatar_url' ]);
        }

        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());
                    if (is_array($imageInformation)) {
                        if ($imageInformation[0] < 91 && $imageInformation[1] < 91) {
                            switch ($imageInformation[ 2 ]) {
                                case 1:
                                    $endung = '.gif';
                                    break;
                                case 3:
                                    $endung = '.png';
                                    break;
                                default:
                                    $endung = '.jpg';
                                    break;
                            }
                            $file = $id.$endung;
                            if ($upload->saveAs($filepath.$file, true)) {
                                @chmod($filepath.$file, $new_chmod);
                                safe_query(
                                    "UPDATE "
                                    . PREFIX . "user
                                    SET
                                        avatar='" . $file .
                                    "' WHERE
                                        userID='" . $id . "'"
                                );
                            }
                        } else {
                            $error_array[] = sprintf($_language->module[ 'image_too_big' ], 90, 90);
                        }
                    } else {
                        $error_array[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $error_array[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $error_array[] = $upload->translateError();
            }
        }

        //userpic
        $filepath = "./images/userpics/";

        $upload = new \webspell\HttpUpload('userpic');
        if (!$upload->hasFile()) {
            $upload = new \webspell\UrlUpload($_POST[ 'userpic_url' ]);
        }

        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());
                    if (is_array($imageInformation)) {
                        if ($imageInformation[0] < 231 && $imageInformation[1] < 211) {
                            switch ($imageInformation[ 2 ]) {
                                case 1:
                                    $endung = '.gif';
                                    break;
                                case 3:
                                    $endung = '.png';
                                    break;
                                default:
                                    $endung = '.jpg';
                                    break;
                            }
                            $file = $id.$endung;
                            if ($upload->saveAs($filepath.$file, true)) {
                                @chmod($filepath.$file, $new_chmod);
                                safe_query(
                                    "UPDATE "
                                    . PREFIX . "user
                                    SET
                                        userpic='" . $file .
                                    "' WHERE userID='" . $id . "'"
                                );
                            }
                        } else {
                            $error_array[] = sprintf($_language->module[ 'image_too_big' ], 230, 210);
                        }
                    } else {
                        $error_array[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $error_array[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $error_array[] = $upload->translateError();
            }
        }

        if (empty($usernamenew)) {
            $error_array[ ] = $_language->module[ 'you_have_to_username' ];
        }
        if (empty($nickname)) {
            $error_array[ ] = $_language->module[ 'you_have_to_nickname' ];
        }

        $qry =
            "SELECT userID FROM " . PREFIX . "user WHERE username = '" . $usernamenew . "' AND userID != " . $userID .
            " LIMIT 0,1";
        if (mysqli_num_rows(safe_query($qry))) {
            $error_array[ ] = $_language->module[ 'username_aleady_in_use' ];
        }

        $qry = "SELECT userID FROM " . PREFIX . "user WHERE nickname = '" . $nickname . "' AND userID!=" . $userID .
            " LIMIT 0,1";
        if (mysqli_num_rows(safe_query($qry))) {
            $error_array[ ] = $_language->module[ 'nickname_already_in_use' ];
        }

        if (count($error_array)) {
            $showerror = generateErrorBoxFromArray($_language->module[ 'errors_there' ], $error_array);
        } else {
            safe_query("UPDATE `" . PREFIX . "user`
                    SET
                        nickname='" . $nickname . "',
                        username='" . $usernamenew . "',
                        email_hide='" . $mail_hide . "',
                        firstname='" . $firstname . "',
                        lastname='" . $lastname . "',
                        sex='" . $sex . "',
                        country='" . $flag . "',
                        town='" . $town . "',
                        birthday='" . $birthday . "',
                        icq='" . $icq . "',
                        usertext='" . $usertext . "',
                        clantag='" . $clantag . "',
                        clanname='" . $clanname . "',
                        clanhp='" . $clanhp . "',
                        clanirc='" . $clanirc . "',
                        clanhistory='" . $clanhistory . "',
                        cpu='" . $cpu . "',
                        mainboard='" . $mainboard . "',
                        ram='" . $ram . "',
                        hdd='" . $hdd . "',
                        monitor='" . $monitor . "',
                        graphiccard='" . $graphiccard . "',
                        soundcard='" . $soundcard . "',
                        verbindung='" . $connection . "',
                        keyboard='" . $keyboard . "',
                        mouse='" . $mouse . "',
                        mousepad='" . $mousepad . "',
                        headset='" . $headset . "',
                        mailonpm='" . $pm_mail . "',
                        newsletter='" . $newsletter . "',
                        homepage='" . $homepage . "',
                        about='" . $about . "',
                        date_format='" . $date_format . "',
                        time_format='" . $time_format . "',
                        language='" . $language . "',
                        user_guestbook='" . $user_gbook . "'
                    WHERE
                        userID='" . $id . "'");

            redirect("index.php?site=profile&amp;id=$id", $_language->module[ 'profile_updated' ], 3);
        }
    }

    if (isset($_GET[ 'action' ]) && $_GET[ 'action' ] == "editpwd") {
        $data_array = array();
        $data_array[ '$userID' ] = $userID;
        $myprofile_editpwd = $GLOBALS[ "_template" ]->replaceTemplate("myprofile_editpwd", $data_array);
        echo $myprofile_editpwd;
    } elseif (isset($_POST[ 'savepwd' ])) {
        $oldpwd = $_POST[ 'oldpwd' ];
        $pwd1 = $_POST[ 'pwd1' ];
        $pwd2 = $_POST[ 'pwd2' ];
        $id = $userID;

        $error = "";

        $ergebnis = safe_query("SELECT password FROM " . PREFIX . "user WHERE userID='" . $id . "'");
        $ds = mysqli_fetch_array($ergebnis);

        if (!(mb_strlen(trim($oldpwd)))) {
            $error = $_language->module[ 'forgot_old_pw' ];
        }
        $oldmd5pwd = generatePasswordHash(stripslashes($oldpwd));
        if ($oldmd5pwd != $ds[ 'password' ]) {
            $error = $_language->module[ 'old_pw_not_valid' ];
        }
        if ($pwd1 == $pwd2) {
            if (!(mb_strlen(trim($pwd1)))) {
                $error = $_language->module[ 'forgot_new_pw' ];
            }
        } else {
            $error = $_language->module[ 'repeated_pw_not_valid' ];
        }

        if (!empty($error)) {
            $newmd5pwd = generatePasswordHash(stripslashes($pwd1));
            safe_query("UPDATE " . PREFIX . "user SET password='" . $newmd5pwd . "' WHERE userID='" . $userID . "'");
            //logout
            unset($_SESSION['ws_user']);
            unset($_SESSION['ws_lastlogin']);
            session_destroy();

            redirect('index.php?site=login', $_language->module['pw_changed'], 3);
        } else {
            echo generateAlert(
                '<strong>ERROR: ' . $error . '</strong><br>
                 <br>
                 <a href="#" onclick="history.back()" class="alert-link">' . $_language->module[ 'back' ] .
                '</a>',
                'alert-danger'
            );
        }
    } elseif (isset($_GET[ 'action' ]) && $_GET[ 'action' ] == "editmail") {
        $data_array = array();
        $data_array[ '$userID' ] = $userID;
        $myprofile_editmail = $GLOBALS[ "_template" ]->replaceTemplate("myprofile_editmail", $data_array);
        echo $myprofile_editmail;
    } elseif (isset($_POST[ 'savemail' ])) {
        $activationkey = md5(RandPass(20));
        $activationlink = 'http://' . $hp_url . '/index.php?site=register&mailkey=' . $activationkey;
        $pwd = $_POST[ 'oldpwd' ];
        $mail1 = $_POST[ 'mail1' ];
        $mail2 = $_POST[ 'mail2' ];

        $ergebnis = safe_query("SELECT password, username FROM " . PREFIX . "user WHERE userID='" . $userID . "'");
        $ds = mysqli_fetch_array($ergebnis);
        $error = "";
        $username = $ds[ 'username' ];
        if (!(mb_strlen(trim($pwd)))) {
            $error = $_language->module[ 'forgot_old_pw' ];
        }
        $md5pwd = generatePasswordHash(stripslashes($pwd));
        if ($md5pwd != $ds[ 'password' ]) {
            $error = $_language->module[ 'wrong_password' ];
        }
        if ($mail1 == $mail2) {
            if (!(mb_strlen(trim($mail1)))) {
                $error = $_language->module[ 'mail_not_valid' ];
            }
        } else {
            $error = $_language->module[ 'repeated_mail_not_valid' ];
        }

        // check e-mail

        if (!validate_email($mail1)) {
            $error = $_language->module[ 'invalid_mail' ];
        }

        if (empty($error)) {
            safe_query("UPDATE
                    " . PREFIX . "user
                SET
                    email_change = '" . $mail1 . "', email_activate = '" . $activationkey . "'
                WHERE
                    userID='" . $userID . "'");

            $ToEmail = $mail1;
            $header = str_replace(array('%homepage_url%'), array($hp_url), $_language->module[ 'mail_subject' ]);
            $Message = str_replace(array(
                '%username%',
                '%activationlink%',
                '%pagetitle%',
                '%homepage_url%'
            ), array(
                $username,
                $activationlink,
                $hp_title,
                $hp_url
            ), $_language->module[ 'mail_text' ]);

            $sendmail = \webspell\Email::sendEmail($admin_email, 'Profile', $ToEmail, $header, $Message);

            if ($sendmail[ 'result' ] == 'fail') {
                if (isset($sendmail[ 'debug' ])) {
                    $fehler = array();
                    $fehler[ ] = $sendmail[ 'error' ];
                    $fehler[ ] = $sendmail[ 'debug' ];
                    echo generateErrorBoxFromArray($_language->module[ 'mail_failed' ], $fehler);
                } else {
                    $fehler = array();
                    $fehler[ ] = $sendmail[ 'error' ];
                    echo generateErrorBoxFromArray($_language->module[ 'mail_failed' ], $fehler);
                }
            } else {
                if (isset($sendmail[ 'debug' ])) {
                    $fehler = array();
                    $fehler[ ] = $sendmail[ 'debug' ];
                    echo generateBoxFromArray($_language->module[ 'mail_changed' ], 'alert-success', $fehler);
                } else {
                    echo generateAlert($_language->module[ 'mail_changed' ], 'alert-success');
                }
            }
        } else {
            echo generateAlert(
                '<strong>ERROR: ' . $error . '</strong><br>
                <br>
                <a href="#" onclick="history.back()" class="alert-link">' . $_language->module[ 'back' ] .
                '</a>',
                'alert-danger'
            );
        }
    } else {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "user WHERE userID='" . $userID . "'");
        $anz = mysqli_num_rows($ergebnis);
        if ($anz) {
            $ds = mysqli_fetch_array($ergebnis);
            $flag = '[flag]' . $ds[ 'country' ] . '[/flag]';
            $country = flags($flag);
            $country = str_replace("<img", "<img id='county'", $country);
            $sex = '<option value="m">' . $_language->module[ 'male' ] . '</option><option value="f">' .
                $_language->module[ 'female' ] . '</option><option value="u">' . $_language->module[ 'unknown' ] .
                '</option>';
            $sex = str_replace('value="' . $ds[ 'sex' ] . '"', 'value="' . $ds[ 'sex' ] . '" selected', $sex);
            if ($ds[ 'newsletter' ] == "1") {
                $newsletter =
                    '<option value="1" selected>' . $_language->module[ 'yes' ] . '</option><option value="0">' .
                    $_language->module[ 'no' ] . '</option>';
            } else {
                $newsletter =
                    '<option value="1">' . $_language->module[ 'yes' ] . '</option><option value="0" selected>' .
                    $_language->module[ 'no' ] . '</option>';
            }
            if ($ds[ 'mailonpm' ] == "1") {
                $pm_mail = '<option value="1" selected>' . $_language->module[ 'yes' ] . '</option><option value="0">' .
                    $_language->module[ 'no' ] . '</option>';
            } else {
                $pm_mail = '<option value="1">' . $_language->module[ 'yes' ] . '</option><option value="0" selected>' .
                    $_language->module[ 'no' ] . '</option>';
            }
            if ($ds[ 'email_hide' ]) {
                $email_hide = ' checked';
            } else {
                $email_hide = '';
            }
            $format_date = "<option value='d.m.y'>DD.MM.YY</option>
                <option value='d.m.Y'>DD.MM.YYYY</option>
                <option value='j.n.y'>D.M.YY</option>
                <option value='j.n.Y'>D.M.YYYY</option>
                <option value='y-m-d'>YY-MM-DD</option>
                <option value='Y-m-d'>YYYY-MM-DD</option>
                <option value='y/m/d'>YY/MM/DD</option>
                <option value='Y/m/d'>YYYY/MM/DD</option>";
            $format_date =
                str_replace(
                    "value='" . $ds[ 'date_format' ] . "'",
                    "value='" . $ds[ 'date_format' ] . "' selected",
                    $format_date
                );

            $format_time = "<option value='G:i'>H:MM</option>
                <option value='H:i'>HH:MM</option>
                <option value='G:i a'>H:MM am/pm</option>
                <option value='H:i a'>HH:MM am/pm</option>
                <option value='G:i A'>H:MM AM/PM</option>
                <option value='H:i A'>HH:MM AM/PM</option>
                <option value='G:i:s'>H:MM:SS</option>
                <option value='H:i:s'>HH:MM:SS</option>
                <option value='G:i:s a'>H:MM:SS am/pm</option>
                <option value='H:i:s a'>HH:MM:SS am/pm</option>
                <option value='G:i:s A'>H:MM:SS AM/PM</option>
                <option value='H:i:s A'>HH:MM:SS AM/PM</option>";
            $format_time =
                str_replace(
                    "value='" . $ds[ 'time_format' ] . "'",
                    "value='" . $ds[ 'time_format' ] . "' selected",
                    $format_time
                );
            $user_gbook = "<option value='0'>" . $_language->module[ 'deactivated' ] . "</option><option value='1'>" .
                $_language->module[ 'activated' ] . "</option>";
            $user_gbook = str_replace(
                "value='" . $ds[ 'user_guestbook' ] . "'",
                "value='" . $ds[ 'user_guestbook' ] . "' selected",
                $user_gbook
            );
            $user_gbook_select = '';
            if ($user_guestbook) {
                $user_gbook_select =
                    '<div class="form-group"><label for="select-user_guestbook" class="col-xs-12 col-md-3 control-label">' .
                    $_language->module[ 'guestbook' ] . ':</label>

                    <div class="col-xs-12 col-md-9">
                        <select name="user_guestbook" class="form-control" id="select-user_guestbook">' . $user_gbook . '</select>
                    </div>
                </div>';
            }
            $birthday = date(
                "Y-m-d", strtotime($ds[ 'birthday' ])
            );
            $countries = getcountries();
            $countries = str_replace(
                'value="' . $ds[ 'country' ] . '"',
                'value="' . $ds[ 'country' ] . '" selected',
                $countries
            );
            if ($ds[ 'avatar' ]) {
                $viewavatar = '<a href="javascript:void(0);" onclick="window.open(\'images/avatars/' . $ds[ 'avatar' ] .
                    '\',\'avatar\',\'width=120,height=120\')">' . $_language->module[ 'avatar' ] . '</a>';
            } else {
                $viewavatar = $_language->module[ 'avatar' ];
            }
            if ($ds[ 'userpic' ]) {
                $viewpic =
                    '&#8226; <a href="javascript:void(0);" onclick="window.open(\'images/userpics/' . $ds[ 'userpic' ] .
                    '\',\'userpic\',\'width=250,height=230\')">' . $_language->module[ 'userpic' ] . '</a>';
            } else {
                $viewpic = $_language->module[ 'userpic' ];
            }

            $usertext = getinput($ds[ 'usertext' ]);
            $clanhistory = clearfromtags($ds[ 'clanhistory' ]);
            $clanname = clearfromtags($ds[ 'clanname' ]);
            $clantag = clearfromtags($ds[ 'clantag' ]);
            $clanirc = clearfromtags($ds[ 'clanirc' ]);
            $firstname = clearfromtags($ds[ 'firstname' ]);
            $lastname = clearfromtags($ds[ 'lastname' ]);
            $town = clearfromtags($ds[ 'town' ]);
            $cpu = clearfromtags($ds[ 'cpu' ]);
            $mainboard = clearfromtags($ds[ 'mainboard' ]);
            $ram = clearfromtags($ds[ 'ram' ]);
            $hdd = clearfromtags($ds[ 'hdd' ]);
            $monitor = clearfromtags($ds[ 'monitor' ]);
            $graphiccard = clearfromtags($ds[ 'graphiccard' ]);
            $soundcard = clearfromtags($ds[ 'soundcard' ]);
            $connection = clearfromtags($ds[ 'verbindung' ]);
            $keyboard = clearfromtags($ds[ 'keyboard' ]);
            $mouse = clearfromtags($ds[ 'mouse' ]);
            $mousepad = clearfromtags($ds[ 'mousepad' ]);
            $headset = clearfromtags($ds[ 'headset' ]);
            $clanhp = getinput($ds[ 'clanhp' ]);
            $about = getinput($ds[ 'about' ]);
            $nickname = $ds[ 'nickname' ];
            $username = getinput($ds[ 'username' ]);
            $email = getinput($ds[ 'email' ]);
            $icq = getinput($ds[ 'icq' ]);
            $homepage = getinput($ds[ 'homepage' ]);

            // Select all possible languages
            $langdirs = '';
            $filepath = "./languages/";

            $mysql_langs = array();
            $query = safe_query("SELECT lang, language FROM " . PREFIX . "news_languages");
            while ($sql_lang = mysqli_fetch_assoc($query)) {
                $mysql_langs[ $sql_lang[ 'lang' ] ] = $sql_lang[ 'language' ];
            }
            $langs = array();
            if ($dh = opendir($filepath)) {
                while ($file = mb_substr(readdir($dh), 0, 2)) {
                    if ($file != "." && $file != ".." && is_dir($filepath . $file)) {
                        if (isset($mysql_langs[ $file ])) {
                            $name = $mysql_langs[ $file ];
                            $name = ucfirst($name);
                            $langs[ $name ] = $file;
                        } else {
                            $langs[ $file ] = $file;
                        }
                    }
                }
                closedir($dh);
            }
            ksort($langs, SORT_NATURAL);
            foreach ($langs as $lang => $flag) {
                $langdirs .= '<option value="' . $flag . '">' . $lang . '</option>';
            }

            if ($ds[ 'language' ]) {
                $langdirs =
                    str_replace('"' . $ds[ 'language' ] . '"', '"' . $ds[ 'language' ] . '" selected', $langdirs);
            } else {
                $langdirs =
                    str_replace('"' . $_language->language . '"', '"' . $_language->language . '" selected', $langdirs);
            }

            $lang_flag = '[flag]' . $ds[ 'language' ] . '[/flag]';
            $lang_country = flags($lang_flag);
            $lang_country = str_replace("<img", "<img id='lang_county'", $lang_country);

            $data_array = array();
            $data_array[ '$showerror' ] = $showerror;
            $data_array[ '$nickname' ] = $nickname;
            $data_array[ '$username' ] = $username;
            $data_array[ '$email' ] = $email;
            $data_array[ '$viewavatar' ] = $viewavatar;
            $data_array[ '$viewpic' ] = $viewpic;
            $data_array[ '$usertext' ] = $usertext;
            $data_array[ '$firstname' ] = $firstname;
            $data_array[ '$lastname' ] = $lastname;
            $data_array[ '$country' ] = $country;
            $data_array[ '$countries' ] = $countries;
            $data_array[ '$town' ] = $town;
            $data_array[ '$birthday' ] = $birthday;
            $data_array[ '$sex' ] = $sex;
            $data_array[ '$icq' ] = $icq;
            $data_array[ '$homepage' ] = $homepage;
            $data_array[ '$about' ] = $about;
            $data_array[ '$newsletter' ] = $newsletter;
            $data_array[ '$langdirs' ] = $langdirs;
            $data_array[ '$pm_mail' ] = $pm_mail;
            $data_array[ '$email_hide' ] = $email_hide;
            $data_array[ '$format_date' ] = $format_date;
            $data_array[ '$format_time' ] = $format_time;
            $data_array[ '$user_gbook_select' ] = $user_gbook_select;
            $data_array[ '$clantag' ] = $clantag;
            $data_array[ '$clanname' ] = $clanname;
            $data_array[ '$clanhp' ] = $clanhp;
            $data_array[ '$clanirc' ] = $clanirc;
            $data_array[ '$clanhistory' ] = $clanhistory;
            $data_array[ '$cpu' ] = $cpu;
            $data_array[ '$mainboard' ] = $mainboard;
            $data_array[ '$ram' ] = $ram;
            $data_array[ '$hdd' ] = $hdd;
            $data_array[ '$monitor' ] = $monitor;
            $data_array[ '$graphiccard' ] = $graphiccard;
            $data_array[ '$soundcard' ] = $soundcard;
            $data_array[ '$connection' ] = $connection;
            $data_array[ '$keyboard' ] = $keyboard;
            $data_array[ '$mouse' ] = $mouse;
            $data_array[ '$mousepad' ] = $mousepad;
            $data_array[ '$headset' ] = $headset;
            $myprofile = $GLOBALS[ "_template" ]->replaceTemplate("myprofile", $data_array);
            echo $myprofile;
        } else {
            echo generateAlert($_language->module[ 'not_logged_in' ], 'alert-danger');
        }
    }
}
