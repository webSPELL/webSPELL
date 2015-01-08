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

$_language->readModule('registered_users');

eval("\$title_registered_users = \"" . gettemplate("title_registered_users") . "\";");
echo $title_registered_users;

function clear($text)
{
    return str_replace(
        "javascript:",
        "",
        strip_tags($text)
    );
}

$alle = safe_query("SELECT userID FROM " . PREFIX . "user");
$gesamt = mysqli_num_rows($alle);
$pages = ceil($gesamt / $maxusers);

if (isset($_GET[ 'page' ])) {
    $page = (int)$_GET[ 'page' ];
} else {
    $page = 1;
}
$sort = "nickname";
if (isset($_GET[ 'sort' ])) {
    if (
        $_GET[ 'sort' ] === 'country' ||
        $_GET[ 'sort' ] === 'nickname' ||
        $_GET[ 'sort' ] === 'lastlogin' ||
        $_GET[ 'sort' ] === 'registerdate'
    ) {
        $sort = $_GET[ 'sort' ];
    }
}

$type = "ASC";
if (isset($_GET[ 'type' ])) {
    if (($_GET[ 'type' ] == 'ASC') || ($_GET[ 'type' ] == 'DESC')) {
        $type = $_GET[ 'type' ];
    }
}

if ($pages > 1) {
    $page_link = makepagelink("index.php?site=registered_users&amp;sort=$sort&amp;type=$type", $page, $pages);
} else {
    $page_link = '';
}

if ($page == "1") {
    $ergebnis =
        safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "user
            ORDER BY
                " . $sort . " " . $type . "
            LIMIT 0," . (int)$maxusers
        );
    if ($type == "DESC") {
        $n = $gesamt;
    } else {
        $n = 1;
    }
} else {
    $start = $page * $maxusers - $maxusers;
    $ergebnis =
        safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "user
            ORDER BY
                " . $sort . " " . $type . "
            LIMIT " . $start . "," . (int)$maxusers
        );
    if ($type == "DESC") {
        $n = ($gesamt) - $page * $maxusers + $maxusers;
    } else {
        $n = ($gesamt + 1) - $page * $maxusers + $maxusers;
    }
}

$anz = mysqli_num_rows($ergebnis);
if ($anz) {

    if ($type == "ASC") {
        $sorter =
            '<a href="index.php?site=registered_users&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
            $_language->module[ 'sort' ] . ' <span class="glyphicon glyphicon-chevron-down"></span></a>';
    } else {
        $sorter =
            '<a href="index.php?site=registered_users&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
            $_language->module[ 'sort' ] . ' <span class="glyphicon glyphicon-chevron-down"></span></a>';
    }
    eval ("\$registered_users_head = \"" . gettemplate("registered_users_head") . "\";");
    echo $registered_users_head;
    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($n % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }
        $id = $ds[ 'userID' ];
        $country = '[flag]' . htmlspecialchars($ds[ 'country' ]) . '[/flag]';
        $country = flags($country);
        $nickname =
            '<a href="index.php?site=profile&amp;id=' . $ds[ 'userID' ] . '"><b>' . strip_tags($ds[ 'nickname' ]) .
            '</b></a>';
        if (isclanmember($ds[ 'userID' ])) {
            $member = ' <img src="images/icons/member.gif" width="6" height="11" alt="Clanmember">';
        } else {
            $member = '';
        }
        if ($ds[ 'email_hide' ]) {
            $email = '';
        } else {
            $email = '<a href="mailto:' . mail_protect($ds[ 'email' ]) .
                '"><img src="images/icons/email.gif" width="15" height="11" alt="e-mail"></a>';
        }

        if (!validate_url($ds[ 'homepage' ])) {
            $homepage = '';
        } else {
            $homepage = '<a href="' . $ds[ 'homepage' ] .
                '" target="_blank"><img src="images/icons/hp.gif" width="14" height="14" alt="homepage"></a>';
        }

        $pm = '';
        $buddy = '';
        if ($loggedin && $ds[ 'userID' ] != $userID) {
            $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $ds[ 'userID' ] .
                '"><img src="images/icons/pm.gif" width="12" height="13" alt="messenger"></a>';
            if (isignored($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=readd&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID .
                    '"><img src="images/icons/buddy_readd.gif" width="16" height="16" alt="back to buddy-list"></a>';
            } elseif (isbuddy($userID, $ds[ 'userID' ])) {
                $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID .
                    '"><img src="images/icons/buddy_ignore.gif" width="16" height="16" alt="ignore user"></a>';
            } elseif ($userID == $ds[ 'userID' ]) {
                $buddy = '';
            } else {
                $buddy = '<a href="buddies.php?action=add&amp;id=' . $ds[ 'userID' ] . '&amp;userID=' . $userID .
                    '"><img src="images/icons/buddy_add.gif" width="16" height="16" alt="add to buddylist"></a>';
            }
        }
        $lastlogindate = getformatdate($ds[ 'lastlogin' ]);
        $lastlogintime = getformattime($ds[ 'lastlogin' ]);
        $registereddate = getformatdate($ds[ 'registerdate' ]);
        $status = isonline($ds[ 'userID' ]);

        if ($status == "offline") {
            $login = $lastlogindate . ' - ' . $lastlogintime;
        } else {
            $login = '<img src="images/icons/online.gif" width="7" height="7" alt="online"> ' .
                $_language->module[ 'now_on' ];
        }

        eval ("\$registered_users_content = \"" . gettemplate("registered_users_content") . "\";");
        echo $registered_users_content;
        $n++;
    }
    eval ("\$registered_users_foot = \"" . gettemplate("registered_users_foot") . "\";");
    echo $registered_users_foot;
} else {
    echo $_language->module[ 'no_users' ];
}
