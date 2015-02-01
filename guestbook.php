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

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if (isset($_POST[ 'save' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('guestbook');

    $date = time();
    $run = 0;

    if ($userID) {
        $name = $_database->escape_string(getnickname($userID));
        if (getemailhide($userID)) {
            $email = '';
        } else {
            $email = getemail($userID);
        }
        $url = gethomepage($userID);
        $icq = geticq($userID);
        $run = 1;
    } else {
        $name = $_POST[ 'gbname' ];
        $email = $_POST[ 'gbemail' ];
        $url = $_POST[ 'gburl' ];
        $icq = $_POST[ 'icq' ];
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha($_POST[ 'captcha' ], $_POST[ 'captcha_hash' ])) {
            $run = 1;
        }
    }

    if ($run) {
        if (mb_strlen($_POST[ 'message' ])) {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "guestbook (date, name, email, hp, icq, ip, comment)
                VALUES
                    ('" . $date . "',
                    '" . $name . "',
                    '" . $email . "',
                    '" . $url . "',
                    '" . $icq . "',
                    '" . $GLOBALS[ 'ip' ] . "',
                    '" . $_POST[ 'message' ] . "');"
            );

            if ($gb_info) {
                $ergebnis = safe_query("SELECT userID FROM " . PREFIX . "user_groups WHERE feedback='1'");
                while ($ds = mysqli_fetch_array($ergebnis)) {
                    $touser[ ] = $ds[ 'userID' ];
                }

                $message = str_replace(
                    '%insertid%',
                    'id_' . mysqli_insert_id($_database),
                    $_database->escape_string($_language->module[ 'pmtext_newentry' ])
                );
                foreach ($touser as $id) {
                    sendmessage($id, $_database->escape_string($_language->module[ 'pmsubject_newentry' ]), $message);
                }
            }
            header("Location: index.php?site=guestbook");
        } else {
            header("Location: index.php?site=guestbook&action=add&error=message");
        }
    } else {
        header("Location: index.php?site=guestbook&action=add&error=captcha");
    }
} elseif (isset($_GET[ 'delete' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('guestbook');
    if (!isfeedbackadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    if (isset($_POST[ 'gbID' ])) {
        foreach ($_POST[ 'gbID' ] as $id) {
            safe_query("DELETE FROM " . PREFIX . "guestbook WHERE gbID='$id'");
        }
    }
    header("Location: index.php?site=guestbook");
} elseif (isset($_POST[ 'savecomment' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");

    $_language->readModule('guestbook');
    if (!isfeedbackadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    safe_query(
        "UPDATE
            " . PREFIX . "guestbook
        SET
            admincomment='" . $_POST[ 'message' ] . "'
        WHERE
            gbID='" . $_POST[ 'guestbookID' ] . "' "
    );

    header("Location: index.php?site=guestbook");
} elseif ($action == 'comment' && is_numeric($_GET[ 'guestbookID' ])) {
    $_language->readModule('guestbook');
    $_language->readModule('bbcode', true);
    if (!isfeedbackadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    $ergebnis =
        safe_query("SELECT admincomment FROM " . PREFIX . "guestbook WHERE gbID='" . $_GET[ 'guestbookID' ] . "'");
    $bg1 = BG_1;
    $ds = mysqli_fetch_array($ergebnis);
    $admincomment = getinput($ds[ 'admincomment' ]);
    $title_guestbook = $GLOBALS["_template"]->replaceTemplate("title_guestbook", array());
    echo $title_guestbook;
    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $data_array = array();
    $data_array['$addbbcode'] = $addbbcode;
    $data_array['$admincomment'] = $admincomment;
    $data_array['$guestbookID'] = (int)$_GET[ 'guestbookID' ];
    $guestbook_comment = $GLOBALS["_template"]->replaceTemplate("guestbook_comment", $data_array);
    echo $guestbook_comment;
} elseif ($action == 'add') {
    $_language->readModule('guestbook');
    $_language->readModule('bbcode', true);

    $message = '';
    if (isset($_GET[ 'messageID' ])) {
        if (is_numeric($_GET[ 'messageID' ])) {
            $ds = mysqli_fetch_array(
                safe_query(
                    "SELECT
                        comment, name
                    FROM
                        `" . PREFIX . "guestbook`
                    WHERE gbID='" . $_GET[ 'messageID' ] . "'"
                )
            );
            $message = '[quote=' . $ds[ 'name' ] . ']' . getinput($ds[ 'comment' ]) . '[/quote]';
        }
    }

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $bg1 = BG_1;
    if (isset($_GET[ 'error' ])) {
        if ($_GET[ 'error' ] == "captcha") {
            $error = $_language->module[ 'error_captcha' ];
        } else {
            $error = $_language->module[ 'enter_a_message' ];
        }
    } else {
        $error = null;
    }
    if ($loggedin) {
        $data_array = array();
        $data_array['$addbbcode'] = $addbbcode;
        $data_array['$message'] = $message;
        $guestbook_loggedin = $GLOBALS["_template"]->replaceTemplate("guestbook_loggedin", $data_array);
        echo $guestbook_loggedin;
    } else {
        $CAPCLASS = new \webspell\Captcha;
        $captcha = $CAPCLASS->createCaptcha();
        $hash = $CAPCLASS->getHash();
        $CAPCLASS->clearOldCaptcha();

        $data_array = array();
        $data_array['$error'] = $error;
        $data_array['$addbbcode'] = $addbbcode;
        $data_array['$message'] = $message;
        $data_array['$captcha'] = $captcha;
        $data_array['$hash'] = $hash;
        $guestbook_notloggedin = $GLOBALS["_template"]->replaceTemplate("guestbook_notloggedin", $data_array);
        echo $guestbook_notloggedin;
    }
} else {
    $_language->readModule('guestbook');
    $title_guestbook = $GLOBALS["_template"]->replaceTemplate("title_guestbook", array());
    echo $title_guestbook;

    $gesamt = mysqli_num_rows(safe_query("SELECT gbID FROM " . PREFIX . "guestbook"));

    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }
    $type = "DESC";
    if (isset($_GET[ 'type' ])) {
        if (($_GET[ 'type' ] == 'ASC') || ($_GET[ 'type' ] == 'DESC')) {
            $type = $_GET[ 'type' ];
        }
    }
    $pages = ceil($gesamt / $maxguestbook);

    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=guestbook&amp;type=$type", $page, $pages);
    } else {
        $page_link = '';
    }

    if ($page == "1") {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "guestbook ORDER BY date $type LIMIT 0,$maxguestbook");
        if ($type == "DESC") {
            $n = $gesamt;
        } else {
            $n = 1;
        }
    } else {
        $start = $page * $maxguestbook - $maxguestbook;
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "guestbook ORDER BY date $type LIMIT $start,$maxguestbook");
        if ($type == "DESC") {
            $n = $gesamt - ($page - 1) * $maxguestbook;
        } else {
            $n = ($page - 1) * $maxguestbook + 1;
        }
    }

    if ($type == "ASC") {
        $sorter =
            '<a href="index.php?site=guestbook&amp;page=' . $page . '&amp;type=DESC">' . $_language->module[ 'sort' ] .
            ' <span class="glyphicon glyphicon-chevron-down"></span></a>';
    } else {
        $sorter =
            '<a href="index.php?site=guestbook&amp;page=' . $page . '&amp;type=ASC">' . $_language->module[ 'sort' ] .
            ' <span class="glyphicon glyphicon-chevron-up"></span></a>';
    }

    $data_array = array();
    $data_array['$sorter'] = $sorter;
    $data_array['$page_link'] = $page_link;
    $guestbook_head = $GLOBALS["_template"]->replaceTemplate("guestbook_head", $data_array);
    echo $guestbook_head;

    while ($ds = mysqli_fetch_array($ergebnis)) {
        $n % 2 ? $bg1 = BG_1 : $bg1 = BG_2;
        $date = getformatdatetime($ds[ 'date' ]);

        if (validate_email($ds[ 'email' ])) {
            $email = '<a href="mailto:' . mail_protect($ds[ 'email' ]) .
                '"><span class="glyphicon glyphicon-envelope" title="email"></span></a>';
        } else {
            $email = '';
        }

        if (validate_url($ds[ 'hp' ])) {
            $hp = '<a href="' . $ds[ 'hp' ] .
                '" target="_blank"><img src="images/icons/hp.gif" width="14" height="14" alt="homepage"></a>';
        } else {
            $hp = '';
        }

        $sem = '/[0-9]{6,11}/si';
        $icq_number = str_replace('-', '', $ds[ 'icq' ]);
        if (preg_match($sem, $ds[ 'icq' ])) {
            $icq = '<a href="http://www.icq.com/people/about_me.php?uin=' . $icq_number .
                '" target="_blank"><img src="http://online.mirabilis.com/scripts/online.dll?icq=' . $ds[ 'icq' ] .
                '&amp;img=5" alt="icq"></a>';
        } else {
            $icq = "";
        }
        $guestbookID = 'id_' . $ds[ 'gbID' ];
        $name = strip_tags($ds[ 'name' ]);
        $message = cleartext($ds[ 'comment' ]);
        $message = toggle($message, $ds[ 'gbID' ]);
        unset($admincomment);
        if ($ds[ 'admincomment' ] != "") {
            $admincomment = '<hr><small><strong>' . $_language->module[ 'admin_comment' ] . ':</strong><br>' .
                cleartext($ds[ 'admincomment' ]) . '</small>';
        } else {
            $admincomment = '';
        }

        $actions = '';
        $ip = 'logged';
        $quote = '<a href="index.php?site=guestbook&amp;action=add&amp;messageID=' . $ds[ 'gbID' ] .
            '"><img src="images/icons/quote.gif" alt="quote"></a>';
        if (isfeedbackadmin($userID)) {
            $actions = '<input class="input" type="checkbox" name="gbID[]" value="' . $ds[ 'gbID' ] .
                '"> <a href="index.php?site=guestbook&amp;action=comment&amp;guestbookID=' . $ds[ 'gbID' ] .
                '" class="btn btn-danger">Add Admincomment</a>';
            $ip = $ds[ 'ip' ];
        }

        $data_array = array();
        $data_array['$actions'] = $actions;
        $data_array['$name'] = $name;
        $data_array['$date'] = $date;
        $data_array['$email'] = $email;
        $data_array['$hp'] = $hp;
        $data_array['$icq'] = $icq;
        $data_array['$ip'] = $ip;
        $data_array['$quote'] = $quote;
        $data_array['$message'] = $message;
        $data_array['$admincomment'] = $admincomment;
        $guestbook = $GLOBALS["_template"]->replaceTemplate("guestbook", $data_array);
        echo $guestbook;

        if ($type == "DESC") {
            $n--;
        } else {
            $n++;
        }
    }

    if (isfeedbackadmin($userID)) {
        $submit = '<input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);"> ' .
            $_language->module[ 'select_all' ] . '
            <input type="submit" value="' . $_language->module[ 'delete_selected' ] . '" class="btn btn-danger">';
    } else {
        $submit = '';
    }

    $data_array = array();
    $data_array['$page_link'] = $page_link;
    $data_array['$submit'] = $submit;
    $guestbook_foot = $GLOBALS["_template"]->replaceTemplate("guestbook_foot", $data_array);
    echo $guestbook_foot;
}
