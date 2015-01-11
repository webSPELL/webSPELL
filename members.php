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

$_language->readModule('members');

eval("\$title_members = \"" . gettemplate("title_members") . "\";");
echo $title_members;

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "show") {
    if (isset($_GET[ 'squadID' ])) {
        $getsquad = 'WHERE squadID="' . (int)$_GET[ 'squadID' ] . '"';
    } else {
        $getsquad = '';
    }

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "squads " . $getsquad . " ORDER BY sort");
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $anzmembers = mysqli_num_rows(
            safe_query(
                "SELECT
                    sqmID
                FROM
                    " . PREFIX . "squads_members
                WHERE
                    squadID='" . $ds[ 'squadID' ] . "'"
            )
        );
        $name = '<strong>' . $ds[ 'name' ] . '</strong>';
        if ($ds[ 'icon' ]) {
            $icon = '<img src="images/squadicons/' . $ds[ 'icon' ] . '" alt="' . htmlspecialchars($ds[ 'name' ]) . '">';
        } else {
            $icon = '';
        }
        $info = htmloutput($ds[ 'info' ]);
        $squadID = $ds[ 'squadID' ];
        $backlink = $_language->module[ 'back_overview' ];
        $results = '';
        $awards = '';
        $challenge = '';

        if ($ds[ 'gamesquad' ]) {
            $results = '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $squadID .
                '&amp;sort=date&amp;only=squad" class="btn btn-primary">' . $_language->module[ 'results' ] . '</a>';
            $awards = '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID .
                '&amp;page=1" class="btn btn-primary">' . $_language->module[ 'awards' ] . '</a>';
            $challenge = '<a href="index.php?site=challenge" class="btn btn-primary" class="btn btn-primary">' .
                $_language->module[ 'challenge' ] . '</a>';
        } else {
            $results = '';
            $awards = '';
            $challenge = '';
        }

        $member = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "squads_members s, " . PREFIX . "user u
            WHERE
                s.squadID='" . $ds[ 'squadID' ] . "'
            AND
                s.userID = u.userID
            ORDER BY sort"
        );

        if ($anzmembers == 1) {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'member' ];
        } else {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'members' ];
        }

        eval ("\$members_details_head = \"" . gettemplate("members_details_head") . "\";");
        echo $members_details_head;

        while ($dm = mysqli_fetch_array($member)) {
            $country = '[flag]' . $dm[ 'country' ] . '[/flag]';
            $country = flags($country);
            $nickname = '<a href="index.php?site=profile&amp;id=' . $dm[ 'userID' ] . '"><b>' .
                strip_tags(stripslashes($dm[ 'nickname' ])) . '</b></a>';
            $nicknamee = strip_tags(stripslashes($dm[ 'nickname' ]));
            $profilid = $dm[ 'userID' ];

            if ($dm[ 'userdescription' ]) {
                $userdescription = htmloutput($dm[ 'userdescription' ]);
            } else {
                $userdescription = $_language->module[ 'no_description' ];
            }

            if (file_exists("images/userpics/" . $profilid . ".jpg")) {
                $userpic = $profilid . ".jpg";
                $pic_info = $dm[ 'nickname' ] . " userpicture";
            } elseif (file_exists("images/userpics/" . $profilid . ".gif")) {
                $userpic = $profilid . ".gif";
                $pic_info = $dm[ 'nickname' ] . " userpicture";
            } else {
                $userpic = "nouserpic.gif";
                $pic_info = "no userpic available!";
            }

            $icq = $dm[ 'icq' ];
            if (getemailhide($dm[ 'userID' ])) {
                $email = '';
            } else {
                $email = '<a href="mailto:' . mail_protect($dm[ 'email' ]) .
                    '"><span class="glyphicon glyphicon-email" title="email"></span></a>';
            }
            $emaill = $dm[ 'email' ];

            $pm = '';
            $buddy = '';
            if ($loggedin && $dm[ 'userID' ] != $userID) {
                $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $dm[ 'userID' ] .
                    '"><img src="images/icons/pm.gif" width="12" height="13" alt="messenger"></a>';

                if (isignored($userID, $dm[ 'userID' ])) {
                    $buddy = '<a href="buddies.php?action=readd&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_readd.gif" width="16" height="16" alt="back to buddylist"></a>';
                } elseif (isbuddy($userID, $dm[ 'userID' ])) {
                    $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_ignore.gif" width="16" height="16" alt="ignore user"></a>';
                } elseif ($userID == $dm[ 'userID' ]) {
                    $buddy = "";
                } else {
                    $buddy = '<a href="buddies.php?action=add&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_add.gif" width="16" height="16" alt="add to buddylist"></a>';
                }
            }

            if (isonline($dm[ 'userID' ]) == "offline") {
                $statuspic = '<img src="images/icons/offline.gif" width="7" height="7" alt="offline">';
            } else {
                $statuspic = '<img src="images/icons/online.gif" width="7" height="7" alt="online">';
            }

            $position = $dm[ 'position' ];
            $firstname = strip_tags($dm[ 'firstname' ]);
            $lastname = strip_tags($dm[ 'lastname' ]);
            $town = strip_tags($dm[ 'town' ]);
            if ($dm[ 'activity' ]) {
                $activity = '<span class="ws-win-color">' . $_language->module[ 'active' ] . '</span>';
            } else {
                $activity = '<span class="ws-lose-color">' . $_language->module[ 'inactive' ] . '</span>';
            }

            eval ("\$members_details_content = \"" . gettemplate("members_details_content") . "\";");
            echo $members_details_content;
        }
        eval ("\$members_details_foot = \"" . gettemplate("members_details_foot") . "\";");
        echo $members_details_foot;
    }
} else {
    if (isset($_POST[ 'squadID' ])) {
        $onesquadonly = 'WHERE squadID="' . (int)$_POST[ 'squadID' ] . '"';
        $visible = "block";
    } elseif (isset($_GET[ 'squadID' ])) {
        $onesquadonly = 'WHERE squadID="' . (int)$_GET[ 'squadID' ] . '"';
        $visible = "block";
    } else {
        $visible = "none";
        $onesquadonly = '';
    }

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "squads " . $onesquadonly . " ORDER BY sort");
    if (mysqli_num_rows($ergebnis)) {
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $anzmembers = mysqli_num_rows(
                safe_query(
                    "SELECT
                        sqmID
                    FROM
                        " . PREFIX . "squads_members
                    WHERE squadID='" . $ds[ 'squadID' ] . "'"
                )
            );
            $name = '<a href="index.php?site=members&amp;action=show&amp;squadID=' . $ds[ 'squadID' ] . '"><b>' .
                $ds[ 'name' ] . '</b></a>';

            if ($ds[ 'icon' ]) {
                $icon =
                    '<img src="images/squadicons/' . $ds[ 'icon' ] . '" alt="' . htmlspecialchars($ds[ 'name' ]) . '">';
            } else {
                $icon = '';
            }

            $info = htmloutput($ds[ 'info' ]);
            $squadID = $ds[ 'squadID' ];
            $details = str_replace('%squadID%', $squadID, $_language->module[ 'show_details' ]);

            if ($ds[ 'gamesquad' ]) {
                $results = '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $squadID .
                    '&amp;sort=date&amp;only=squad" class="btn btn-primary">' . $_language->module[ 'results' ] .
                    '</a>';
                $awards = '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID .
                    '&amp;page=1" class="btn btn-primary">' . $_language->module[ 'awards' ] . '</a>';
                $challenge =
                    '<a href="index.php?site=challenge" class="btn btn-primary">' . $_language->module[ 'challenge' ] .
                    '</a>';
            } else {
                $results = '';
                $awards = '';
                $challenge = '';
            }

            if ($anzmembers == 1) {
                $anzmembers = $anzmembers . ' ' . $_language->module[ 'member' ];
            } else {
                $anzmembers = $anzmembers . ' ' . $_language->module[ 'members' ];
            }

            eval ("\$members_head_head = \"" . gettemplate("members_head_head") . "\";");
            echo $members_head_head;

            $member =
                safe_query(
                    "SELECT
                        *
                    FROM
                        " . PREFIX . "squads_members s, " . PREFIX . "user u
                    WHERE
                        s.squadID='" . $ds[ 'squadID' ] . "'
                    AND
                        s.userID = u.userID
                    ORDER BY
                        sort"
                );

            eval ("\$members_head = \"" . gettemplate("members_head") . "\";");
            echo $members_head;

            while ($dm = mysqli_fetch_array($member)) {
                $country = '[flag]' . $dm[ 'country' ] . '[/flag]';
                $country = flags($country);
                $nickname = strip_tags(stripslashes($dm[ 'nickname' ]));
                $profilid = $dm[ 'userID' ];

                $icq = $dm[ 'icq' ];
                if (getemailhide($dm[ 'userID' ])) {
                    $email = '';
                } else {
                    $email = '<a href="mailto:' . mail_protect($dm[ 'email' ]) .
                        '"><span class="glyphicon glyphicon-email" title="email"></span></a>';
                }
                $emaill = $dm[ 'email' ];

                $pm = '';
                $buddy = '';
                if ($loggedin && $dm[ 'userID' ] != $userID) {
                    $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $dm[ 'userID' ] .
                        '"><img src="images/icons/pm.gif" width="12" height="13" alt="messenger"></a>';

                    if (isignored($userID, $dm[ 'userID' ])) {
                        $buddy =
                            '<a href="buddies.php?action=readd&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                            '"><img src="images/icons/buddy_readd.gif" width="16" height="16" alt="back to buddylist">
                            </a>';
                    } elseif (isbuddy($userID, $dm[ 'userID' ])) {
                        $buddy =
                            '<a href="buddies.php?action=ignore&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                            '"><img src="images/icons/buddy_ignore.gif" width="16" height="16" alt="ignore user"></a>';
                    } elseif ($userID == $dm[ 'userID' ]) {
                        $buddy = "";
                    } else {
                        $buddy =
                            '<a href="buddies.php?action=add&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                            '"><img src="images/icons/buddy_add.gif" width="16" height="16" alt="add to buddylist">
                            </a>';
                    }
                }

                if (isonline($dm[ 'userID' ]) == "offline") {
                    $statuspic = '<img src="images/icons/offline.gif" width="7" height="7" alt="offline">';
                } else {
                    $statuspic = '<img src="images/icons/online.gif" width="7" height="7" alt="online">';
                }

                $position = $dm[ 'position' ];
                $firstname = strip_tags($dm[ 'firstname' ]);
                $lastname = strip_tags($dm[ 'lastname' ]);
                $town = strip_tags($dm[ 'town' ]);
                if ($dm[ 'activity' ]) {
                    $activity = '<span class="ws-win-color">' . $_language->module[ 'active' ] . '</span>';
                } else {
                    $activity = '<span class="ws-lose-color">' . $_language->module[ 'inactive' ] . '</span>';
                }

                eval ("\$members_content = \"" . gettemplate("members_content") . "\";");
                echo $members_content;
            }
            eval ("\$members_content_foot = \"" . gettemplate("members_content_foot") . "\";");
            echo $members_content_foot;
        }

        $ergebnis = safe_query("SELECT squadID, name FROM " . PREFIX . "squads ORDER BY sort");
        $squadlist = '';
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $squadlist .= '<option value="' . $ds[ 'squadID' ] . '">' . $ds[ 'name' ] . '</option>';
        }

        eval ("\$members_foot = \"" . gettemplate("members_foot") . "\";");
        echo $members_foot;
    }
}
