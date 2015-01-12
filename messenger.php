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

if (isset($_POST['delete'])) {
    include"_mysql.php";
    include"_settings.php";
    include"_functions.php";
    $_language->readModule('messenger');

    if (!isset($userID)) {
        die($_language->module['not_logged']);
    }
    if (isset($_POST['messageID'])) {
        $messageID = $_POST['messageID'];
    } else {
        $messageID = array();
    }

    foreach ($messageID as $id) {
        safe_query("DELETE FROM " . PREFIX . "messenger WHERE messageID='" . $id . "' AND userID='" . $userID . "'");
    }
    header("Location: index.php?site=messenger&action=outgoing");
} elseif (isset($_POST['quickaction'])) {
    include"_mysql.php";
    include"_settings.php";
    include"_functions.php";
    if (!isset($userID)) {
        die();
    }

    $quickactiontype = $_POST['quickactiontype'];
    if (isset($_POST['messageID'])) {
        $messageID = $_POST['messageID'];
        if ($quickactiontype == "viewed") {
            foreach ($messageID as $id) {
                safe_query("UPDATE " . PREFIX . "messenger SET viewed='1' WHERE messageID='$id' AND userID='$userID'");
            }
        } elseif ($quickactiontype == "notviewed") {
            foreach ($messageID as $id) {
                safe_query("UPDATE " . PREFIX . "messenger SET viewed='0' WHERE messageID='$id' AND userID='$userID'");
            }
        } elseif ($quickactiontype == "delete") {
            foreach ($messageID as $id) {
                safe_query("DELETE FROM " . PREFIX . "messenger WHERE messageID='$id' AND touser='$userID'");
            }
        }
        header("Location: index.php?site=messenger&action=incoming");
    } else {
        header("Location: index.php?site=messenger&action=incoming");
    }
} elseif (isset($_POST['send'])) {
    include"_mysql.php";
    include"_settings.php";
    include"_functions.php";
    $_language->readModule('messenger');

    $touser = $_POST['touser'];
    if ($touser[0] == "" && $_POST['touser_field'] != "*") {
        $tmp = explode(",", $_POST['touser_field']);
        for ($i = 0; $i < 5; $i++) {
            if (isset($tmp[$i])) {
                $tmp[$i] = trim($tmp[$i]);
                if (!empty($tmp[$i])) {
                    $touser[$i] = getuserid($tmp[$i]);
                } else {
                    break;
                }
            } else {
                break;
            }
        }
    }
    if (isset($_POST['eachmember'])) {
        unset($touser);
        $ergebnis = safe_query("SELECT userID FROM " . PREFIX . "user");
        while ($ds = mysqli_fetch_array($ergebnis)) {
            if (isclanmember($ds['userID'])) {
                $touser[] = $ds['userID'];
            }
        }
    }

    if (isset($_POST['eachuser'])) {
        unset($touser);
        $ergebnis = safe_query("SELECT userID FROM " . PREFIX . "user");
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $touser[] = $ds['userID'];
        }
    }

    $date = time();
    if ($_POST['title'] == "") {
        $title = $_language->module['no_subject'];
    } else {
        $title = $_POST['title'];
    }
    $message = $_POST['message'];
    if ($touser[0] != "" && isset($userID)) {
        foreach ($touser as $id) {
            sendmessage($id, $title, $message, $userID);
        }
        if (isset($_SESSION['message_subject'])) {
            unset($_SESSION['message_subject'], $_SESSION['message_body'], $_SESSION['message_error']);
        }
        header("Location: index.php?site=messenger&action=outgoing");
        exit();
    } else {
        $_SESSION['message_subject'] = $title;
        $_SESSION['message_body'] = $message;
        $_SESSION['message_error'] = true;
        header("Location: index.php?site=messenger&action=newmessage");
        exit();
    }
} elseif (isset($_POST['reply'])) {
    include"_mysql.php";
    include"_settings.php";
    include"_functions.php";

    if (isset($userID)) {
        sendmessage($_POST['id'], $_POST['title'], $_POST['message'], $userID);
    }

    header("Location: index.php?site=messenger&action=outgoing");
    exit();
} elseif ($userID) {
    $_language->readModule('messenger');

    if (isset($_REQUEST['action'])) {
        $action = $_REQUEST['action'];
    } else {
        $action = "incoming";
    }

    $title_messenger = $GLOBALS["_template"]->replaceTemplate("title_messenger", array());
    echo $title_messenger;

    if ($action == "incoming") {
        if (isset($_REQUEST['entries'])) {
            $entries = $_REQUEST['entries'];
        }

        $alle = safe_query("SELECT messageID FROM " . PREFIX . "messenger WHERE touser='$userID' AND userID='$userID'");
        $gesamt = mysqli_num_rows($alle);
        $pages = 1;
        if (isset($_GET['page'])) {
            $page = (int)$_GET['page'];
        } else {
            $page = 1;
        }
        $sort = "date";
        if (isset($_GET['sort'])) {
            if (($_GET['sort'] == 'date') || ($_GET['sort'] == 'fromuser') || ($_GET['sort'] == 'title')) {
                $sort = $_GET['sort'];
            }
        }
        $type = 'DESC';
        if (isset($_GET['type'])) {
            if ($_GET['type'] == 'ASC') {
                $type = 'ASC';
            }
        }

        if (isset($entries) && $entries > 0) {
            $max = (int)$entries;
        } else {
            $max = $maxmessages;
        }
        $pages = ceil($gesamt / $max);

        if ($pages > 1) {
            $page_link =
                makepagelink(
                    "index.php?site=messenger&amp;action=incoming&amp;sort=$sort&amp;type=$type&amp;entries=$max",
                    $page,
                    $pages
                );
        } else {
            $page_link = '';
        }

        if ($page == "1") {
            $ergebnis = safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "messenger
                WHERE
                    touser='$userID'
                AND
                    userID='$userID'
                ORDER BY
                    $sort $type LIMIT 0,$max"
            );
            if ($type == "DESC") {
                $n = $gesamt;
            } else {
                $n = 1;
            }
        } else {
            $start = $page * $max - $max;
            $ergebnis = safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "messenger
                WHERE
                    touser='$userID'
                AND
                    userID='$userID'
                ORDER BY
                    $sort $type LIMIT $start,$max"
            );
            if ($type == "DESC") {
                $n = ($gesamt) - $page * $max + $max;
            } else {
                $n = ($gesamt + 1) - $page * $max + $max;
            }
        }

        if ($type == "ASC") {
            $sorter = '<a href="index.php?site=messenger&amp;action=incoming&amp;page=' . $page . '&amp;sort=' . $sort .
                '&amp;type=DESC&amp;entries=' . $max . '">' . $_language->module['sort'] .
                '</a> <span class="glyphicon glyphicon-chevron-down"></span>&nbsp;&nbsp;&nbsp;';
        } else {
            $sorter = '<a href="index.php?site=messenger&amp;action=incoming&amp;page=' . $page . '&amp;sort=' . $sort .
                '&amp;type=ASC&amp;entries=' . $max . '">' . $_language->module['sort'] .
                '</a> <span class="glyphicon glyphicon-chevron-up"></span>&nbsp;&nbsp;&nbsp;';
        }

        $data_array = array();
        $data_array['$sorter'] = $sorter;
        $data_array['$page_link'] = $page_link;
        $data_array['$max'] = $max;
        $pm_incoming_head = $GLOBALS["_template"]->replaceTemplate("pm_incoming_head", $data_array);
        echo $pm_incoming_head;

        $anz = mysqli_num_rows($ergebnis);
        if ($anz) {
            $n = 1;
            while ($ds = mysqli_fetch_array($ergebnis)) {
                if ($n % 2) {
                    $bg1 = BG_1;
                    $bg2 = BG_2;
                } else {
                    $bg1 = BG_3;
                    $bg2 = BG_4;
                }
                $date = getformatdatetime($ds['date']);

                if ($userID == $ds['fromuser']) {
                    $buddy = '';
                } elseif (isignored($userID, $ds['fromuser'])) {
                    $buddy =
                        '<a href="buddies.php?action=readd&amp;id=' . $ds['fromuser'] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_readd.gif" alt="%readd_ignored%"></a>';
                } elseif (isbuddy($userID, $ds['fromuser'])) {
                    $buddy =
                        '<a href="buddies.php?action=ignore&amp;id=' . $ds['fromuser'] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_ignore.gif" alt="%ignore%"></a>';
                } else {
                    $buddy = '<a href="buddies.php?action=add&amp;id=' . $ds['fromuser'] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_add.gif" alt="%add_buddylist%"></a>';
                }

                if (isonline($ds['fromuser']) == "offline") {
                    $statuspic = '<img src="images/icons/offline.gif" width="7" height="7" alt="offline">';
                } else {
                    $statuspic = '<img src="images/icons/online.gif" width="7" height="7" alt="online">';
                }

                $sender = '<a href="index.php?site=profile&amp;id=' . $ds['fromuser'] . '"><b>' .
                    getnickname($ds['fromuser']) . '</b></a>';
                if (isclanmember($ds['fromuser'])) {
                    $member = '<img src="images/icons/member.gif" width="7" height="16" alt="Clanmember">';
                } else {
                    $member = '';
                }

                if (trim($ds['title']) != "") {
                    $title = clearfromtags($ds['title']);
                } else {
                    $title = $_language->module['no_subject'];
                }

                $new = '';
                $icon = '';
                if (!$ds['viewed']) {
                    $icon = '<span class="glyphicon glyphicon-envelope"></span>';
                    $title = '<strong>' . $title . '</strong>';
                    $new = 'class="warning"';
                }

                $title =
                    '<a href="index.php?site=messenger&amp;action=show&amp;id=' . $ds['messageID'] . '">' . $title .
                    '</a>';

                $data_array = array();
                $data_array['$messageID'] = $ds['messageID'];
                $data_array['$icon'] = $icon;
                $data_array['$title'] = $title;
                $data_array['$sender'] = $sender;
                $data_array['$member'] = $member;
                $data_array['$statuspic'] = $statuspic;
                $data_array['$date'] = $date;
                $pm_incoming_content = $GLOBALS["_template"]->replaceTemplate("pm_incoming_content", $data_array);
                echo $pm_incoming_content;
                $n++;
            }
        } else {
            echo '<tr>' . $_language->module['no_incoming'] . '</td></tr>';
        }

        $pm_incoming_foot = $GLOBALS["_template"]->replaceTemplate("pm_incoming_foot", array());
        echo $pm_incoming_foot;
    } elseif ($action == "outgoing") {
        if (isset($_REQUEST['entries'])) {
            $entries = $_REQUEST['entries'];
        }

        $alle =
            safe_query("SELECT messageID FROM " . PREFIX . "messenger WHERE fromuser='$userID' AND userID='$userID'");
        $gesamt = mysqli_num_rows($alle);
        $pages = 1;

        if (!isset($_GET['page'])) {
            $page = 1;
        } else {
            $page = (int)$_GET['page'];
        }
        $sort = 'date';
        $type = 'DESC';
        if (isset($_GET['type'])) {
            if ($_GET['type'] == 'ASC') {
                $type = 'ASC';
            }
        }
        if (isset($entries) && $entries > 0) {
            $max = (int)$entries;
        } else {
            $max = $maxmessages;
        }
        $pages = ceil($gesamt / $max);

        if ($pages > 1) {
            $page_link = makepagelink("index.php?site=messenger&amp;action=outgoing&amp;entries=$max", $page, $pages);
        } else {
            $page_link = '';
        }

        if ($page == "1") {
            $ergebnis = safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "messenger
                WHERE
                    fromuser='$userID'
                AND
                    userID='$userID'
                ORDER BY
                    $sort $type LIMIT 0,$max"
            );
            if ($type == "DESC") {
                $n = $gesamt;
            } else {
                $n = 1;
            }
        } else {
            $start = $page * $max - $max;
            $ergebnis = safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "messenger
                WHERE
                    fromuser='$userID'
                AND
                    userID='$userID'
                ORDER BY
                    $sort $type LIMIT $start,$max"
            );
            if ($type == "DESC") {
                $n = ($gesamt) - $page * $max + $max;
            } else {
                $n = ($gesamt + 1) - $page * $max + $max;
            }
        }

        if ($type == "ASC") {
            $sorter = '<a href="index.php?site=messenger&amp;action=outgoing&amp;page=' . $page . '&amp;sort=' . $sort .
                '&amp;type=DESC&amp;entries=' . $max . '">' . $_language->module['sort'] .
                '</a> <span class="glyphicon glyphicon-chevron-down"></span>&nbsp;&nbsp;&nbsp;';
        } else {
            $sorter = '<a href="index.php?site=messenger&amp;action=outgoing&amp;page=' . $page . '&amp;sort=' . $sort .
                '&amp;type=ASC&amp;entries=' . $max . '">' . $_language->module['sort'] .
                '</a> <span class="glyphicon glyphicon-chevron-up"></span>&nbsp;&nbsp;&nbsp;';
        }

        $data_array = array();
        $data_array['$sorter'] = $sorter;
        $data_array['$page_link'] = $page_link;
        $data_array['$max'] = $max;
        $pm_outgoing_head = $GLOBALS["_template"]->replaceTemplate("pm_outgoing_head", $data_array);
        echo $pm_outgoing_head;

        $anz = mysqli_num_rows($ergebnis);
        if ($anz) {
            $n = 1;
            while ($ds = mysqli_fetch_array($ergebnis)) {
                if ($n % 2) {
                    $bg1 = BG_1;
                    $bg2 = BG_2;
                } else {
                    $bg1 = BG_3;
                    $bg2 = BG_4;
                }
                $date = getformatdatetime($ds['date']);

                if ($userID == $ds['fromuser']) {
                    $buddy = '';
                } elseif (isignored($userID, $ds['touser'])) {
                    $buddy = '<a href="buddies.php?action=readd&amp;id=' . $ds['touser'] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_readd.gif" alt="%readd_ignored%"></a>';
                } elseif (isbuddy($userID, $ds['touser'])) {
                    $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $ds['touser'] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_ignore.gif" alt="%ignore%"></a>';
                } else {
                    $buddy = '<a href="buddies.php?action=add&amp;id=' . $ds['touser'] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_add.gif" alt="%add_buddylist%"></a>';
                }

                $receptionist = '<a href="index.php?site=profile&amp;id=' . $ds['touser'] . '"><b>' .
                    getnickname($ds['touser']) . '</b></a>';

                if (isclanmember($ds['touser'])) {
                    $member = ' <img src="images/icons/member.gif" width="7" height="16" alt="Clanmember">';
                } else {
                    $member = '';
                }

                if (isonline($ds['touser']) == "offline") {
                    $statuspic = '<img src="images/icons/offline.gif" alt="offline">';
                } else {
                    $statuspic = '<img src="images/icons/online.gif" alt="online">';
                }

                if (trim($ds['title']) != "") {
                    $title = clearfromtags($ds['title']);
                } else {
                    $title = $_language->module['no_subject'];
                }
                $title =
                    ' <a href="index.php?site=messenger&amp;action=show&amp;id=' . $ds['messageID'] . '">' . $title .
                    '</a>';

                $icon = '<img src="images/icons/pm_old.gif" width="14" height="12" alt="">';
                $data_array = array();
                $data_array['$messageID'] = $ds['messageID'];
                $data_array['$title'] = $title;
                $data_array['$receptionist'] = $receptionist;
                $data_array['$member'] = $member;
                $data_array['$statuspic'] = $statuspic;
                $data_array['$date'] = $date;
                $pm_outgoing_content = $GLOBALS["_template"]->replaceTemplate("pm_outgoing_content", $data_array);
                echo $pm_outgoing_content;
                $n++;
            }
        } else {
            echo '<tr>' . $_language->module['no_outgoing'] . '</td></tr>';
        }

        $pm_outgoing_foot = $GLOBALS["_template"]->replaceTemplate("pm_outgoing_foot", array());
        echo $pm_outgoing_foot;
    } elseif ($action == "show") {
        $id = (int)$_GET['id'];
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "messenger
                WHERE
                    messageID='" . $id . "'
                AND
                    userID='" . $userID . "'"
            )
        );

        if ($ds['touser'] == $userID || $ds['fromuser'] == $userID) {
            safe_query("UPDATE " . PREFIX . "messenger SET viewed='1' WHERE messageID='$id'");
            $date = getformatdatetime($ds['date']);
            $sender = '<a href="index.php?site=profile&amp;id=' . $ds['fromuser'] . '"><b>' .
                getnickname($ds['fromuser']) . '</b></a>';
            $message = cleartext($ds['message']);
            $message = toggle($message, $ds['messageID']);
            $title = clearfromtags($ds['title']);

            $bg1 = BG_1;
            $data_array = array();
            $data_array['$title'] = $title;
            $data_array['$date'] = $date;
            $data_array['$sender'] = $sender;
            $data_array['$message'] = $message;
            $data_array['$id'] = $id;
            $pm_show = $GLOBALS["_template"]->replaceTemplate("pm_show", $data_array);
            echo $pm_show;
        } else {
            redirect('index.php?site=messenger', '', 0);
        }
    } elseif ($action == "touser") {
        $touser = $_GET['touser'];
        $_language->readModule('bbcode', true);

        $tousernick = getnickname($touser);
        $touser = getforminput($touser);

        $bg1 = BG_1;
        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $data_array = array();
        $data_array['$tousernick'] = $tousernick;
        $data_array['$addbbcode'] = $addbbcode;
        $data_array['$touser'] = $touser;
        $pm_new_touser = $GLOBALS["_template"]->replaceTemplate("pm_new_touser", $data_array);
        echo $pm_new_touser;
    } elseif ($action == "reply") {
        $id = $_GET['id'];
        $_language->readModule('bbcode', true);
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "messenger WHERE messageID='$id'");
        $ds = mysqli_fetch_array($ergebnis);
        if ($ds['touser'] == $userID || $ds['fromuser'] == $userID) {
            $replytouser = $ds['fromuser'];
            $tousernick = getnickname($replytouser);
            $date = getformatdatetime($ds['date']);

            $title = $ds['title'];
            if (!preg_match("#Re\[(.*?)\]:#si", $title)) {
                $title = "Re[1]: " . $title;
            } else {
                preg_match_all("#Re\[(.*?)\]:#si", $title, $re);
                $rep = $re[1][0] + 1;
                $title = preg_replace("#\[(.*?)\]#si", "[$rep]", $title);
            }

            $message = '[QUOTE=' . $tousernick . ']' . getinput($ds['message']) . '[/QUOTE]';

            $bg1 = BG_1;
            $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
            $data_array = array();
            $data_array['$title'] = $title;
            $data_array['$addbbcode'] = $addbbcode;
            $data_array['$message'] = $message;
            $data_array['$replytouser'] = $replytouser;
            $pm_reply = $GLOBALS["_template"]->replaceTemplate("pm_reply", $data_array);
            echo $pm_reply;
        } else {
            redirect('index.php?site=messenger', '', 0);
        }
    } elseif ($action == "newmessage") {
        $_language->readModule('bbcode', true);
        $ergebnis = safe_query("SELECT buddy FROM " . PREFIX . "buddys WHERE userID='$userID'");
        $user = '';
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $user .= '<option value="' . $ds['buddy'] . '">' . getnickname($ds['buddy']) . '</option>';
        }
        if ($user == "") {
            $user = '<option value="">' . $_language->module['no_buddies'] . '</option>';
        } else {
            $user = '<option value="" selected="selected">---</option>' . $user;
        }

        if (isset($_SESSION['message_error'])) {
            $subject = getforminput($_SESSION['message_subject']);
            $message = getforminput($_SESSION['message_body']);
            $error = generateErrorBoxFromArray($_language->module['error'], array($_language->module['unknown_user']));
            unset($_SESSION['message_subject'], $_SESSION['message_body'], $_SESSION['message_error']);
        } else {
            $error = $message = $subject = "";
        }

        $bg1 = BG_1;

        if (isanyadmin($userID)) {
            $admin = '<strong>' . $_language->module['adminoptions'] . '</strong><br>' .
                $_language->module['sendeachuser'] . '<input class="input" type="checkbox" name="eachuser" value="true">
                <br>' . $_language->module['sendeachmember'] .
                '<input class="input" type="checkbox" name="eachmember" value="true">';
        } else {
            $admin = '';
        }

        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $data_array = array();
        $data_array['$error'] = $error;
        $data_array['$subject'] = $subject;
        $data_array['$user'] = $user;
        $data_array['$addbbcode'] = $addbbcode;
        $data_array['$message'] = $message;
        $data_array['$admin'] = $admin;
        $pm_new = $GLOBALS["_template"]->replaceTemplate("pm_new", $data_array);
        echo $pm_new;
    }
} else {
    $_language->readModule('messenger');
    echo $_language->module['not_logged'];
}
