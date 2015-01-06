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

if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
}
if (isset($_GET['delete'])) {
    $delete = (bool)$_GET['delete'];
} else {
    $delete = '';
}
if (isset($_GET['edit'])) {
    $edit = (bool)$_GET['edit'];
} else {
    $edit = '';
}
if (isset($_REQUEST['topic'])) {
    $topic = (int)$_REQUEST['topic'];
} else {
    $topic = '';
}
if (isset($_REQUEST['addreply'])) {
    $addreply = (bool)$_REQUEST['addreply'];
} else {
    $addreply = '';
}
if (isset($_GET['type'])) {
    $type = (($_GET['type'] == 'ASC') || ($_GET['type'] == 'DESC')) ? $_GET['type'] : '';
} else {
    $type = '';
}
if (isset($_GET['quoteID'])) {
    $quoteID = (int)$_GET['quoteID'];
} else {
    $quoteID = '';
}
$do_sticky = (isset($_POST['sticky'])) ? true : false;

if (isset($_POST['newreply']) && !isset($_POST['preview'])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('forum');

    if (!$userID) {
        die($_language->module['not_logged']);
    }

    $message = $_POST['message'];
    $topic = (int)$_POST['topic'];
    $page = (int)$_POST['page'];

    if (!(mb_strlen(trim($message)))) {
        die($_language->module['forgot_message']);
    }
    $ds = mysqli_fetch_array(safe_query(
        "SELECT closed, writegrps, boardID FROM " . PREFIX .
        "forum_topics WHERE topicID='" . $topic . "'"
    ));
    if ($ds['closed']) {
        die($_language->module['topic_closed']);
    }

    $writer = 0;
    if ($ds['writegrps'] != "") {
        $writegrps = explode(";", $ds['writegrps']);
        foreach ($writegrps as $value) {
            if (isinusergrp($value, $userID)) {
                $writer = 1;
                break;
            }
        }
        if (ismoderator($userID, $ds['boardID'])) {
            $writer = 1;
        }
    } else {
        $writer = 1;
    }
    if (!$writer) {
        die($_language->module['no_access_write']);
    }
    $do_sticky = '';
    if (isforumadmin($userID) || isanymoderator($userID, $ds['boardID'])) {
        $do_sticky = (isset($_POST['sticky'])) ? ', sticky=1' : ', sticky=0';
    }

    $spamApi = SpamApi::getInstance();
    $validation = $spamApi->validate($message);

    $date = time();
    if ($validation == SpamApi::NoSpam) {
        safe_query(
            "INSERT INTO " . PREFIX . "forum_posts ( boardID, topicID, date, poster, message ) VALUES( '" .
            $_REQUEST['board'] . "', '$topic', '$date', '$userID', '" . $message . "' ) "
        );
        $lastpostID = mysqli_insert_id($_database);
        safe_query("UPDATE " . PREFIX . "forum_boards SET posts=posts+1 WHERE boardID='" . $_REQUEST['board'] . "' ");
        safe_query(
            "UPDATE " . PREFIX . "forum_topics SET lastdate='" . $date . "', lastposter='" . $userID .
            "', lastpostID='" . $lastpostID . "', replys=replys+1 $do_sticky WHERE topicID='$topic' "
        );

        // check if there are more than 1000 unread topics => delete oldest one
        $dv = safe_query("SELECT topics FROM " . PREFIX . "user WHERE userID='" . $userID . "'");
        $array = explode('|', $dv['topics']);
        if (count($array) >= 1000) {
            safe_query(
                "UPDATE " . PREFIX . "user SET topics='|" . implode('|', array_slice($array, 2)) .
                "' WHERE userID='" . $userID . "'"
            );
        }
        unset($array);

        // add this topic to unread
        safe_query(
            "UPDATE " . PREFIX . "user SET topics=CONCAT(topics, '" . $topic . "|') WHERE topics NOT LIKE '%|" .
            $topic . "|%'"
        ); // update unread topics, format: |oldstring| => |oldstring|topicID|

        $emails = [];
        $ergebnis = safe_query(
            "SELECT f.userID, u.email, u.language FROM " . PREFIX . "forum_notify f JOIN " . PREFIX .
            "user u ON u.userID=f.userID WHERE f.topicID=$topic"
        );
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $emails[] = ['mail' => $ds['email'], 'lang' => $ds['language']];
        }
        safe_query("DELETE FROM " . PREFIX . "forum_notify WHERE topicID='$topic'");

        if (count($emails)) {

            $de = mysqli_fetch_array(safe_query("SELECT nickname FROM " . PREFIX . "user WHERE userID='$userID'"));
            $poster = $de['nickname'];
            $de = mysqli_fetch_array(safe_query("SELECT topic FROM " . PREFIX . "forum_topics WHERE topicID='$topic'"));
            $topicname = getinput($de['topic']);

            $link = "http://" . $hp_url . "/index.php?site=forum_topic&topic=" . $topic;
            $maillanguage = new \webspell\Language();
            $maillanguage->setLanguage($default_language);

            foreach ($emails as $email) {
                $maillanguage->setLanguage($email['lang']);
                $maillanguage->readModule('forum');
                $forum_topic_notify = str_replace(
                    ['%poster%', '%topic_link%', '%pagetitle%', '%hpurl%'],
                    [html_entity_decode($poster), $link, $hp_title, 'http://' . $hp_url],
                    $maillanguage->module['notify_mail']
                );
                $header = "From:" . $admin_email . "\nContent-type: text/plain; charset=utf-8\n";
                @mail(
                    $email['mail'],
                    $maillanguage->module['new_reply'] . ' (' . $hp_title . ')',
                    $forum_topic_notify,
                    $header
                );
            }
        }

        if (isset($_POST['notify']) && (bool)$_POST['notify']) {
            safe_query(
                "INSERT INTO " . PREFIX . "forum_notify (topicID, userID) VALUES('" . $topic . "', '" . $userID .
                "') "
            );
        }
    } else {
        safe_query(
            "INSERT INTO " . PREFIX .
            "forum_posts_spam ( boardID, topicID, date, poster, message, rating ) VALUES( '" . $_REQUEST['board'] .
            "', '$topic', '$date', '$userID', '" . $message . "', '" . $rating . "' ) "
        );
    }
    header("Location: index.php?site=forum_topic&topic=" . $topic . "&page=" . $page);
    exit();
} elseif (isset($_POST['editreply']) && (bool)$_POST['editreply']) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('forum');

    if (!isforumposter($userID, $_POST['id']) && !isforumadmin($userID) && !ismoderator($userID, $_GET['board'])
    ) {
        die($_language->module['no_accses']);
    }

    $message = $_POST['message'];
    $id = (int)$_POST['id'];
    $check = mysqli_num_rows(safe_query(
        "SELECT postID FROM " . PREFIX . "forum_posts WHERE postID='" . $id .
        "' AND poster='" . $userID . "'"
    ));
    if (($check || isforumadmin($userID) || ismoderator($userID, (int)$_GET['board'])) && mb_strlen(trim($message))
    ) {

        if (isforumadmin($userID) || isanymoderator($userID, $ds['boardID'])) {
            $do_sticky = (isset($_POST['sticky'])) ? 'sticky=1' : 'sticky=0';
            safe_query(
                "UPDATE " . PREFIX . "forum_topics SET $do_sticky WHERE topicID='" . (int)$_GET['topic'] .
                "'"
            );
        }

        $date = getformatdatetime(time());
        safe_query("UPDATE " . PREFIX . "forum_posts SET message = '" . $message . "' WHERE postID='$id' ");
        safe_query(
            "DELETE FROM " . PREFIX . "forum_notify WHERE userID='$userID' AND topicID='" .
            (int)$_GET['topic'] . "'"
        );
        if (isset($_POST['notify'])) {
            if ((bool)$_POST['notify']) {
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "forum_notify (`notifyID`, `topicID`, `userID`) VALUES ('', '$userID', '" . (int)$_GET['topic'] .
                    "')"
                );
            }
        }
    }
    header("Location: index.php?site=forum_topic&topic=" . (int)$_GET['topic'] . "&page=" . (int)$_GET['page']);
} elseif (isset($_POST['saveedittopic']) && (bool)$_POST['saveedittopic']) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('forum');

    if (!isforumadmin($userID) &&
        !isforumposter($userID, $_POST['post']) && !ismoderator($userID, $_GET['board'])
    ) {
        die($_language->module['no_accses']);
    }

    $board = (int)$_GET['board'];
    $topic = (int)$_GET['topic'];
    $post = $_POST['post'];
    if (isset($_POST['notify'])) {
        $notify = (bool)$_POST['notify'];
    } else {
        $notify = false;
    }
    $topicname = $_POST['topicname'];
    if (!$topicname) {
        $topicname = $_language->module['default_topic_title'];
    }
    $message = $_POST['message'];
    if (mb_strlen($message)) {
        if (isset($_POST['icon'])) {
            $icon = $_POST['icon'];
        } else {
            $icon = '';
        }
        $do_sticky = (isset($_POST['sticky'])) ? true : false;
        if ($do_sticky && (isforumadmin($userID) || isanymoderator($userID, $board))) {
            $do_sticky = true;
        } else {
            $do_sticky = false;
        }

        safe_query("UPDATE " . PREFIX . "forum_posts SET message='" . $message . "' WHERE postID='" . $post . "'");
        safe_query(
            "UPDATE " . PREFIX . "forum_topics SET topic='" . $topicname . "', icon='" . $icon . "', sticky='" .
            $do_sticky . "' WHERE topicID='" . $topic . "'"
        );

        if ($notify == 1) {
            $notified =
                safe_query(
                    "SELECT * FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" .
                    $userID . "'"
                );
            if (mysqli_num_rows($notified) != 1) {
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "forum_notify (notifyID, topicID, userID) VALUES ('', '$topic', '$userID')"
                );
            }
        } else {
            safe_query(
                "DELETE FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" . $userID .
                "'"
            );
        }
    }
    header("Location: index.php?site=forum_topic&topic=" . $topic);
}

function showtopic($topic, $edit, $addreply, $quoteID, $type)
{
    global $userID;
    global $loggedin;
    global $page;
    global $maxposts;
    global $preview;
    global $message;
    global $picsize_l;
    global $_language;
    global $spamapikey;

    $_language->readModule('forum');
    $_language->readModule('bbcode', true);

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    $thread = safe_query("SELECT * FROM " . PREFIX . "forum_topics WHERE topicID='$topic' ");
    $dt = mysqli_fetch_array($thread);

    $usergrp = 0;
    $writer = 0;
    $ismod = ismoderator($userID, $dt['boardID']);
    if ($dt['writegrps'] != "" && !$ismod) {
        $writegrps = explode(";", $dt['writegrps']);
        foreach ($writegrps as $value) {
            if (isinusergrp($value, $userID)) {
                $usergrp = 1;
                $writer = 1;
                break;
            }
        }
    } else {
        $writer = 1;
    }
    if ($dt['readgrps'] != "" && !$usergrp && !$ismod) {
        $readgrps = explode(";", $dt['readgrps']);
        foreach ($readgrps as $value) {
            if (isinusergrp($value, $userID)) {
                $usergrp = 1;
                break;
            }
        }
        if (!$usergrp) {
            echo $_language->module['no_permission'];
            redirect('index.php?site=forum', $_language->module['no_permission'], 2);
            return;
        }
    }
    $gesamt = mysqli_num_rows(safe_query("SELECT topicID FROM " . PREFIX . "forum_posts WHERE topicID='$topic'"));
    if ($gesamt == 0) {
        die($_language->module['topic_not_found'] . " <a href=\"javascript:history.back()\">back</a>");
    }
    $pages = 1;
    if (!isset($page) || $site = '') {
        $page = 1;
    }
    if (isset($type)) {
        if (!(($type == 'ASC') || ($type == 'DESC'))) {
            $type = "ASC";
        }
    } else {
        $type = "ASC";
    }
    $max = $maxposts;
    $pages = ceil($gesamt / $maxposts);

    $page_link = '';
    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=forum_topic&amp;topic=$topic&amp;type=$type", $page, $pages);
    }
    if ($type == "ASC") {
        $sorter =
            '<a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;page=' . $page . '&amp;type=DESC">' .
            $_language->module['sort'] . ' <span class="icon-sort-down"></span></a>';
    } else {
        $sorter = '<a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;page=' . $page . '&amp;type=ASC">' .
            $_language->module['sort'] . ' <span class="icon-sort-up"></span></a>';
    }

    $start = 0;
    if ($page > 1) {
        $start = $page * $max - $max;
    }

    safe_query("UPDATE " . PREFIX . "forum_topics SET views=views+1 WHERE topicID='$topic' ");

    // viewed topics

    if (mysqli_num_rows(safe_query("SELECT userID FROM " . PREFIX . "user WHERE topics LIKE '%|" . $topic . "|%'"))) {

        $gv = mysqli_fetch_array(safe_query("SELECT topics FROM " . PREFIX . "user WHERE userID='$userID'"));
        $array = explode("|", $gv['topics']);
        $new = '|';

        foreach ($array as $split) {
            if ($split != "" && $split != $topic) {
                $new = $new . $split . '|';
            }
        }

        safe_query("UPDATE " . PREFIX . "user SET topics='" . $new . "' WHERE userID='$userID'");
    }

    // end viewed topics

    $topicname = getinput($dt['topic']);

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_boards WHERE boardID='" . $dt['boardID'] . "' ");
    $db = mysqli_fetch_array($ergebnis);
    $boardname = $db['name'];

    $moderators = getmoderators($dt['boardID']);

    $topicactions = '<a href="printview.php?board=' . $dt['boardID'] . '&amp;topic=' . $topic .
        '" target="_blank" class="btn btn-default"><span class="icon-print"></span></a> ';
    if ($loggedin && $writer) {
        $topicactions .=
            '<a href="index.php?site=forum&amp;addtopic=true&amp;action=newtopic&amp;board=' . $dt['boardID'] .
            '" class="btn btn-primary hidden">' . $_language->module['new_topic'] .
            '</a> <a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;addreply=true&amp;page=' . $pages .
            '&amp;type=' . $type . '" class="btn btn-primary"><span class="icon-mail-reply"></span> ' .
            $_language->module['new_reply'] . '</a>';
    }
    if ($dt['closed']) {
        $closed = $_language->module['closed_image'];
    } else {
        $closed = '';
    }
    $posttype = 'topic';

    $kathname = getcategoryname($db['category']);
    eval ("\$forum_topics_title = \"" . gettemplate("forum_topics_title") . "\";");
    echo $forum_topics_title;

    eval ("\$forum_topics_actions = \"" . gettemplate("forum_topics_actions") . "\";");
    echo $forum_topics_actions;

    if ($dt['closed']) {
        echo generateAlert($_language->module['closed_image'], 'alert-danger');
    }

    if ($edit && !$dt['closed']) {

        $id = $_GET['id'];
        $dr = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "forum_posts WHERE postID='" . $id . "'"));
        $topic = $_GET['topic'];
        $bg1 = BG_1;
        $_sticky = ($dt['sticky'] == '1') ? 'checked="checked"' : '';

        $anz = mysqli_num_rows(safe_query(
            "SELECT * FROM " . PREFIX . "forum_posts WHERE topicID='" . $dt['topicID'] .
            "' AND postID='" . $id . "' AND poster='" . $userID . "' ORDER BY DATE ASC LIMIT 0,1"
        ));
        if ($anz || isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {
            if (istopicpost($dt['topicID'], $id)) {
                $bg1 = BG_1;

                // topicmessage
                $message = getinput($dr['message']);
                $post = $id;
                $board = $dt['boardID'];

                // notification check
                $notifyqry =
                    safe_query(
                        "SELECT * FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" .
                        $userID . "'"
                    );
                if (mysqli_num_rows($notifyqry)) {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1" checked="checked"> ' .
                        $_language->module['notify_reply'] . '<br>';
                } else {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1"> ' .
                        $_language->module['notify_reply'] . '<br>';
                }
                //STICKY
                if (isforumadmin($userID) || ismoderator($userID, $board)) {
                    $chk_sticky =
                        '<br>' . "\n" . ' <input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky .
                        '> ' . $_language->module['make_sticky'];
                } else {
                    $chk_sticky = '';
                }

                // topic icon list
                $iconlist = '<ul class="nav nav-pills nav-justified">
        <li><input type="radio" class="input" name="icon" value="ausrufezeichen.gif">
        <img src="images/icons/topicicons/ausrufezeichen.gif"></li>
        <li><input type="radio" class="input" name="icon" value="biggrin.gif">
        <img src="images/icons/topicicons/biggrin.gif"></li>
        <li><input type="radio" class="input" name="icon" value="boese.gif">
        <img src="images/icons/topicicons/boese.gif"></li>
        <li><input type="radio" class="input" name="icon" value="bored.gif">
        <img src="images/icons/topicicons/bored.gif"></li>
        <li><input type="radio" class="input" name="icon" value="cool.gif">
        <img src="images/icons/topicicons/cool.gif"></li>
        <li><input type="radio" class="input" name="icon" value="eek.gif">
        <img src="images/icons/topicicons/eek.gif"></li>
        <li><input type="radio" class="input" name="icon" value="frage.gif">
        <img src="images/icons/topicicons/frage.gif"></li>
        <li><input type="radio" class="input" name="icon" value="frown.gif">
        <img src="images/icons/topicicons/frown.gif"></li>
        <li><input type="radio" class="input" name="icon" value="lampe.gif">
        <img src="images/icons/topicicons/lampe.gif"></li>
        <li><input type="radio" class="input" name="icon" value="mad.gif">
        <img src="images/icons/topicicons/mad.gif"></li>
        <li><input type="radio" class="input" name="icon" value="pfeil.gif">
        <img src="images/icons/topicicons/pfeil.gif"></li>
        <li><input type="radio" class="input" name="icon" value="smile.gif">
        <img src="images/icons/topicicons/smile.gif"></li>
        <li><input type="radio" class="input" name="icon" value="text.gif">
        <img src="images/icons/topicicons/text.gif"></li>
        <li><input type="radio" class="input" name="icon" value="thumb_down.gif">
        <img src="images/icons/topicicons/thumb_down.gif"></li>
        <li><input type="radio" class="input" name="icon" value="thumb_up.gif">
        <img src="images/icons/topicicons/thumb_up.gif"></li>
        <li><input type="radio" class="input" name="icon" value="wink.gif">
        <img src="images/icons/topicicons/wink.gif"></li>
        <li><input type="radio" class="input" name="icon" value="0">
        ' . $_language->module['no_icon'] . '</li>
        </ul>';

                if ($dt['icon']) {
                    $iconlist =
                        str_replace(
                            'value="' . $dt['icon'] . '"',
                            'value="' . $dt['icon'] . '" checked="checked"',
                            $iconlist
                        );
                } else {
                    $iconlist = str_replace('value="0"', 'value="0" checked="checked"', $iconlist);
                }
                eval ("\$addbbcode = \"" . gettemplate("addbbcode") . "\";");
                eval ("\$forum_edittopic = \"" . gettemplate("forum_edittopic") . "\";");
                echo $forum_edittopic;
            } else {
                // notification check
                $notifyqry =
                    safe_query(
                        "SELECT * FROM " . PREFIX . "forum_notify WHERE topicID='" . $topic . "' AND userID='" .
                        $userID . "'"
                    );
                if (mysqli_num_rows($notifyqry)) {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1" checked="checked"> ' .
                        $_language->module['notify_reply'];
                } else {
                    $notify = '<input class="input" type="checkbox" name="notify" value="1"> ' .
                        $_language->module['notify_reply'];
                }

                //STICKY
                if (isforumadmin($userID) || ismoderator($userID, $board)) {
                    $chk_sticky = '<input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky . '> ' .
                        $_language->module['make_sticky'];
                } else {
                    $chk_sticky = '';
                }
                $dr['message'] = getinput($dr['message']);
                eval ("\$addbbcode = \"" . gettemplate("addbbcode") . "\";");
                eval ("\$forum_editpost = \"" . gettemplate("forum_editpost") . "\";");
                echo $forum_editpost;
            }
        } else {
            echo generateAlert($_language->module['permission_denied'], 'alert-danger');
        }

        $replys = safe_query(
            "SELECT * FROM " . PREFIX .
            "forum_posts WHERE topicID='$topic' ORDER BY date DESC LIMIT $start, $max"
        );
    } elseif ($addreply && !$dt['closed']) {
        if ($loggedin && $writer) {
            if (isset($_POST['preview'])) {
                $bg1 = BG_1;
                $bg2 = BG_2;

                $time = getformattime(time());
                $date = $_language->module['today'];

                $message_preview = getforminput($_POST['message']);
                $postID = 0;

                $message = cleartext(getforminput($_POST['message']));

                $message = toggle($message, 'xx');
                $username =
                    '<a href="index.php?site=profile&amp;id=' . $userID . '"><strong>' . getnickname($userID) .
                    '</strong></a>';

                if (isclanmember($userID)) {
                    $member = ' <img src="images/icons/member.gif" alt="' . $_language->module['clanmember'] . '">';
                } else {
                    $member = '';
                }
                if ($getavatar = getavatar($userID)) {
                    $avatar = '<img src="images/avatars/' . $getavatar . '" alt="">';
                } else {
                    $avatar = '';
                }
                if ($getsignatur = getsignatur($userID)) {
                    $signatur = cleartext($getsignatur);
                } else {
                    $signatur = '';
                }
                if ($getemail = getemail($userID) && !getemailhide($userID)) {
                    $email = '<a href="mailto:' . mail_protect($getemail) .
                        '"><img src="images/icons/email.gif" alt="email"></a>';
                } else {
                    $email = '';
                }
                if (isset($_POST['notify'])) {
                    $notify = 'checked="checked"';
                } else {
                    $notify = '';
                }
                $pm = '';
                $buddy = '';
                $statuspic = '<img src="images/icons/online.gif" alt="online">';
                if (!validate_url(gethomepage($userID))) {
                    $hp = '';
                } else {
                    $hp =
                        '<a href="' . gethomepage($userID) . '" target="_blank"><img src="images/icons/hp.gif" alt="' .
                        $_language->module['homepage'] . '"></a>';
                }
                $registered = getregistered($userID);
                $posts = getuserforumposts($userID);
                if (isset($_POST['sticky'])) {
                    $post_sticky = $_POST['sticky'];
                } else {
                    $post_sticky = null;
                }
                $_sticky = ($dt['sticky'] == '1' || $post_sticky == '1') ? 'checked="checked"' : '';

                if (isforumadmin($userID)) {
                    $usertype = $_language->module['admin'];
                    $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
                } elseif (isanymoderator($userID)) {
                    $usertype = $_language->module['moderator'];
                    $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
                } else {
                    $ergebnis = safe_query(
                        "SELECT * FROM " . PREFIX .
                        "forum_ranks WHERE $posts >= postmin AND $posts <= postmax AND postmax >0"
                    );
                    $ds = mysqli_fetch_array($ergebnis);
                    $usertype = $ds['rank'];
                    $rang = '<img src="images/icons/ranks/' . $ds['pic'] . '" alt="">';
                }

                if (isforumadmin($userID)) {
                    $chk_sticky = '<input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky . '> ' .
                        $_language->module['make_sticky'];
                } elseif (isanymoderator($userID)) {
                    $chk_sticky = '<input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky . '> ' .
                        $_language->module['make_sticky'];
                } else {
                    $chk_sticky = '';
                }
                $quote = "";
                $actions = "";
                echo '<table class="table">
                <tr>
                <td colspan="2" class="title" class="text-center">' . $_language->module['preview'] . '</td>
                </tr>';

                eval ("\$forum_topic_content = \"" . gettemplate("forum_topic_content") . "\";");
                echo $forum_topic_content;

                echo '</table>';

                $message = $message_preview;
            } else {
                if ($quoteID) {
                    $ergebnis =
                        safe_query("SELECT poster,message FROM " . PREFIX . "forum_posts WHERE postID='$quoteID'");
                    $ds = mysqli_fetch_array($ergebnis);
                    $message = '[quote=' . getnickname($ds['poster']) . ']' . getinput($ds['message']) . '[/quote]';
                }
            }
            if (isset($_POST['sticky'])) {
                $post_sticky = $_POST['sticky'];
            } else {
                $post_sticky = null;
            }
            $_sticky = ($dt['sticky'] == '1' || $post_sticky == '1') ? 'checked="checked"' : '';
            if (isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {
                $chk_sticky = '<input class="input" type="checkbox" name="sticky" value="1" ' . $_sticky . '> ' .
                    $_language->module['make_sticky'];
            } else {
                $chk_sticky = '';
            }

            if (isset($_POST['notify'])) {
                $post_notify = $_POST['notify'];
            } else {
                $post_notify = null;
            }
            $mysql_notify =
                mysqli_num_rows(safe_query(
                    "SELECT notifyID FROM " . PREFIX . "forum_notify WHERE userID='" . $userID .
                    "' AND topicID='" . $topic . "'"
                ));
            $notify = ($mysql_notify || $post_notify == '1') ? 'checked="checked"' : '';

            $bg1 = BG_1;
            $board = $dt['boardID'];

            eval ("\$addbbcode = \"" . gettemplate("addbbcode") . "\";");
            eval ("\$forum_newreply = \"" . gettemplate("forum_newreply") . "\";");
            echo $forum_newreply;
        } elseif ($loggedin) {
            echo generateAlert($_language->module['no_access_write'], 'alert-danger');
        } else {
            echo generateAlert($_language->module['not_logged_msg'], 'alert-danger');
        }
        $replys =
            safe_query(
                "SELECT * FROM " . PREFIX . "forum_posts WHERE topicID='$topic' ORDER BY date DESC LIMIT 0, " .
                $max . ""
            );
    } else {
        $replys =
            safe_query(
                "SELECT * FROM " . PREFIX . "forum_posts WHERE topicID='$topic' ORDER BY date $type LIMIT " .
                $start . ", " . $max . ""
            );
    }

    eval ("\$forum_topic_head = \"" . gettemplate("forum_topic_head") . "\";");
    echo $forum_topic_head;
    $i = 1;
    while ($dr = mysqli_fetch_array($replys)) {
        if ($i % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        $date = getformatdate($dr['date']);
        $time = getformattime($dr['date']);

        $today = getformatdate(time());
        $yesterday = getformatdate(time() - 3600 * 24);

        if ($date == $today) {
            $date = $_language->module['today'];
        } elseif ($date == $yesterday && $date < $today) {
            $date = $_language->module['yesterday'];
        } else {
            $date = $date;
        }

        $message = cleartext($dr['message']);
        $message = toggle($message, $dr['postID']);
        $postID = $dr['postID'];

        $username = '<a href="index.php?site=profile&amp;id=' . $dr['poster'] . '"><b>' .
            stripslashes(getnickname($dr['poster'])) . '</b></a>';

        if (isclanmember($dr['poster'])) {
            $member = ' <img src="images/icons/member.gif" alt="' . $_language->module['clanmember'] . '">';
        } else {
            $member = '';
        }

        if ($getavatar = getavatar($dr['poster'])) {
            $avatar = '<img src="images/avatars/' . $getavatar . '" alt="">';
        } else {
            $avatar = '';
        }

        if ($getsignatur = getsignatur($dr['poster'])) {
            $signatur = cleartext($getsignatur);
        } else {
            $signatur = '';
        }

        if ($getemail = getemail($dr['poster']) && !getemailhide($dr['poster'])) {
            $email =
                '<a href="mailto:' . mail_protect($getemail) . '"><img src="images/icons/email.gif" alt="email"></a>';
        } else {
            $email = '';
        }

        $pm = '';
        $buddy = '';
        if ($loggedin && $dr['poster'] != $userID) {
            $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $dr['poster'] .
                '"><img src="images/icons/pm.gif" width="12" height="13" alt="' . $_language->module['messenger'] .
                '"></a>';
            if (isignored($userID, $dr['poster'])) {
                $buddy = '<a href="buddies.php?action=readd&amp;id=' . $dr['poster'] . '&amp;userID=' . $userID .
                    '"><img src="images/icons/buddy_readd.gif" alt="' . $_language->module['back_buddy'] . '"></a>';
            } elseif (isbuddy($userID, $dr['poster'])) {
                $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $dr['poster'] . '&amp;userID=' . $userID .
                    '"><img src="images/icons/buddy_ignore.gif" alt="' . $_language->module['ignore'] . '"></a>';
            } else {
                $buddy = '<a href="buddies.php?action=add&amp;id=' . $dr['poster'] . '&amp;userID=' . $userID .
                    '"><img src="images/icons/buddy_add.gif" alt="' . $_language->module['add_buddy'] . '"></a>';
            }
        }

        if (isonline($dr['poster']) == "offline") {
            $statuspic = '<img src="images/icons/offline.gif" alt="offline">';
        } else {
            $statuspic = '<img src="images/icons/online.gif" alt="online">';
        }

        if (!validate_url(gethomepage($dr['poster']))) {
            $hp = '';
        } else {
            $hp =
                '<a href="' . gethomepage($dr['poster']) . '" target="_blank"><img src="images/icons/hp.gif" alt="' .
                $_language->module['homepage'] . '"></a>';
        }

        if (!$dt['closed']) {
            $quote =
                '<a href="index.php?site=forum_topic&amp;addreply=true&amp;board=' . $dt['boardID'] . '&amp;topic=' .
                $topic . '&amp;quoteID=' . $dr['postID'] . '&amp;page=' . $page . '&amp;type=' . $type .
                '"><span class="icon-quote-left"></span></a>';
        } else {
            $quote = "";
        }

        $registered = getregistered($dr['poster']);

        $posts = getuserforumposts($dr['poster']);

        if (isforumadmin($dr['poster'])) {
            $usertype = $_language->module['admin'];
            $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
        } elseif (isanymoderator($dr['poster'])) {
            $usertype = $_language->module['moderator'];
            $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
        } else {
            $ergebnis = safe_query(
                "SELECT * FROM " . PREFIX .
                "forum_ranks WHERE $posts >= postmin AND $posts <= postmax AND postmax >0"
            );
            $ds = mysqli_fetch_array($ergebnis);
            $usertype = $ds['rank'];
            $rang = '<img src="images/icons/ranks/' . $ds['pic'] . '" alt="">';
        }

        $spam_buttons = "";
        if (!empty($spamapikey)) {
            if (ispageadmin($userID) || ismoderator($userID, $dt['boardID'])) {
                $spam_buttons =
                    '<input type="button" value="Spam" onclick="eventfetch(\'ajax_spamfilter.php?postID=' . $postID .
                    '&type=spam\',\'\',\'return\')">
                    <input type="button" value="Ham" onclick="eventfetch(\'ajax_spamfilter.php?postID=' . $postID .
                    '&type=ham\',\'\',\'return\')">';
            }
        }

        $actions = '';
        if (($userID == $dr['poster'] || isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) &&
            !$dt['closed']
        ) {
            $actions = ' <a href="index.php?site=forum_topic&amp;topic=' . $topic . '&amp;edit=true&amp;id=' .
                $dr['postID'] . '&amp;page=' . $page . '"><span class="icon-edit"></span></a> ';
        }
        if (isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {
            $actions .= '<input class="input" type="checkbox" name="postID[]" value="' . $dr['postID'] . '">';
        }

        eval ("\$forum_topic_content = \"" . gettemplate("forum_topic_content") . "\";");
        echo $forum_topic_content;
        unset($actions);
        $i++;
    }

    $adminactions = "";
    if (isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {

        if ($dt['closed']) {
            $close = '<option value="opentopic">- ' . $_language->module['reopen_topic'] . '</option>';
        } else {
            $close = '<option value="closetopic">- ' . $_language->module['close_topic'] . '</option>';
        }

        $adminactions = '<div class="row">
        <div class="col-xs-6 text-left"><input type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);">
            ' . $_language->module['select_all'] . '</div>
        <div class="input-group col-xs-6">
        <select name="admaction" class="form-control">
            <option value="0">' . $_language->module['admin_actions'] . ':</option>
            <option value="delposts">- ' . $_language->module['delete_posts'] . '</option>
            <option value="stickytopic">- ' . $_language->module['make_topic_sticky'] . '</option>
            <option value="unstickytopic">- ' . $_language->module['make_topic_unsticky'] . '</option>
            <option value="movetopic">- ' . $_language->module['move_topic'] . '</option>
            ' . $close . '
            <option value="deletetopic">- ' . $_language->module['delete_topic'] . '</option>
        </select>
        <span class="input-group-btn">
        <input type="submit" name="submit" value="' . $_language->module['go'] . '" class="btn btn-danger">
        </span></div>
        <input type="hidden" name="topicID" value="' . $topic . '">
        <input type="hidden" name="board" value="' . $dt['boardID'] . '"></div>';
    }

    eval ("\$forum_topic_foot = \"" . gettemplate("forum_topic_foot") . "\";");
    echo $forum_topic_foot;

    eval ("\$forum_topics_actions = \"" . gettemplate("forum_topics_actions") . "\";");
    echo $forum_topics_actions;

    echo '<div class="text-right">' . $adminactions . '</div></form>';

    if ($dt['closed']) {
        echo $_language->module['closed_image'];
    } else {
        if (!$loggedin && !$edit) {
            echo $_language->module['not_logged_msg'];
        }
    }
}

showtopic($topic, $edit, $addreply, $quoteID, $type);
