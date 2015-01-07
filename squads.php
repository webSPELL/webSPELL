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

$_language->readModule('squads');

eval ("\$title_squads = \"" . gettemplate("title_squads") . "\";");
echo $title_squads;
if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = "";
}
if ($action == "show") {
    if ($_GET[ 'squadID' ]) {
        $getsquad = 'WHERE squadID="' . (int)$_GET[ 'squadID' ] . '"';
    } else {
        $getsquad = '';
    }

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "squads " . $getsquad . " ORDER BY sort");
    while ($ds = mysqli_fetch_array($ergebnis)) {

        $anzmembers = mysqli_num_rows(
            safe_query(
                "SELECT sqmID FROM " . PREFIX . "squads_members WHERE squadID='" . (int)$ds[ 'squadID' ] . "'"
            )
        );
        if ($anzmembers == 1) {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'member' ];
        } else {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'members' ];
        }
        $name = $ds[ 'name' ];
        $squadID = $ds[ 'squadID' ];
        $backlink = '<a href="index.php?site=squads"><strong>' . $_language->module[ 'back_squad_overview' ] .
            '</strong></a>';
        $results = '';
        $awards = '';
        $challenge = '';
        $games = '';

        $border = BORDER;

        if ($ds[ 'gamesquad' ]) {
            $results = '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $squadID .
                '&amp;sort=date&amp;only=squad" class="btn btn-primary">' . $_language->module[ 'results' ] . '</a>';
            $awards = '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID .
                '&amp;page=1" class="btn btn-primary">' . $_language->module[ 'awards' ] . '</a>';
            $challenge =
                '<a href="index.php?site=challenge" class="btn btn-primary">' . $_language->module[ 'challenge' ] .
                '</a>';
            $games = $ds[ 'games' ];
            if ($games) {
                $games = str_replace(";", ", ", $games);
                $games = $_language->module[ 'squad_plays' ] . ": " . $games;
            }
        }

        $member = safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "squads_members s,
                " . PREFIX . "user u
            WHERE
                s.squadID='" . $ds[ 'squadID' ] . "' AND
                s.userID = u.userID
            ORDER BY
                sort"
        );
        eval("\$squads_head = \"" . gettemplate("squads_head") . "\";");
        echo $squads_head;

        $i = 1;
        while ($dm = mysqli_fetch_array($member)) {

            if ($i % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }

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

            if ($dm[ 'userpic' ] != "" && file_exists("images/userpics/" . $dm[ 'userpic' ])) {
                $userpic = $dm[ 'userpic' ];
                $pic_info = $dm[ 'nickname' ] . ' ' . $_language->module[ 'userpicture' ];
            } else {
                $userpic = "nouserpic.gif";
                $pic_info = $_language->module[ 'no_userpic' ];
            }

            $icq = $dm[ 'icq' ];
            if (getemailhide($dm[ 'userID' ])) {
                $email = '';
            } else {
                $email =
                    '<a href="mailto:' . mail_protect($dm[ 'email' ]) . '"><img src="images/icons/email.gif" alt="' .
                    $_language->module[ 'email' ] . '"></a>';
            }

            $pm = '';
            $buddy = '';
            if ($loggedin && $dm[ 'userID' ] != $userID) {
                $pm = '<a href="index.php?site=messenger&amp;action=touser&amp;touser=' . $dm[ 'userID' ] .
                    '"><img src="images/icons/pm.gif" alt="' . $_language->module[ 'messenger' ] . '"></a>';

                if (isignored($userID, $dm[ 'userID' ])) {
                    $buddy = '<a href="buddies.php?action=readd&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_readd.gif" alt="' . $_language->module[ 'back_buddy' ] .
                        '"></a>';
                } elseif (isbuddy($userID, $dm[ 'userID' ])) {
                    $buddy = '<a href="buddies.php?action=ignore&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_ignore.gif" alt="' . $_language->module[ 'ignore' ] . '"></a>';
                } else {
                    $buddy = '<a href="buddies.php?action=add&amp;id=' . $dm[ 'userID' ] . '&amp;userID=' . $userID .
                        '"><img src="images/icons/buddy_add.gif" alt="' . $_language->module[ 'add_buddy' ] . '"></a>';
                }
            }

            if (isonline($dm[ 'userID' ]) == "offline") {
                $statuspic = '<img src="images/icons/offline.gif" alt="offline">';
            } else {
                $statuspic = '<img src="images/icons/online.gif" alt="online">';
            }

            $position = $dm[ 'position' ];
            $firstname = strip_tags($dm[ 'firstname' ]);
            $lastname = strip_tags($dm[ 'lastname' ]);
            $town = strip_tags($dm[ 'town' ]);
            if ($dm[ 'activity' ]) {
                $activity = '<font color="' . $wincolor . '">' . $_language->module[ 'active' ] . '</font>';
            } else {
                $activity = '<font color="' . $loosecolor . '">' . $_language->module[ 'inactive' ] . '</font>';
            }

            eval ("\$squads_content = \"" . gettemplate("squads_content") . "\";");
            echo $squads_content;
            $i++;
        }
        eval ("\$squads_foot = \"" . gettemplate("squads_foot") . "\";");
        echo $squads_foot;
    }
} else {
    $getsquad = "";
    if (isset($_GET[ 'squadID' ])) {
        $getsquad = 'WHERE squadID="' . (int)$_GET[ 'squadID' ] . '"';
    }

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "squads " . $getsquad . " ORDER BY sort");

    $i = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {

        if ($i % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        $anzmembers = mysqli_num_rows(
            safe_query(
                "SELECT
                    sqmID
                FROM
                    " . PREFIX . "squads_members
                WHERE
                    squadID='" . (int)$ds[ 'squadID' ] . "'"
            )
        );
        if ($anzmembers == 1) {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'member' ];
        } else {
            $anzmembers = $anzmembers . ' ' . $_language->module[ 'members' ];
        }
        $name =
            '<a href="index.php?site=squads&amp;action=show&amp;squadID=' . $ds[ 'squadID' ] . '"><b>' . $ds[ 'name' ] .
            '</b></a>';
        if ($ds[ 'icon' ]) {
            $icon = '<a href="index.php?site=squads&amp;action=show&amp;squadID=' . $ds[ 'squadID' ] .
                '"><img src="images/squadicons/' . $ds[ 'icon' ] . '" alt="' . htmlspecialchars($ds[ 'name' ]) .
                '"></a>';
        } else {
            $icon = '';
        }
        $info = htmloutput($ds[ 'info' ]);
        $details = '<a href="index.php?site=squads&amp;action=show&amp;squadID=' . $ds[ 'squadID' ] . '"><b>' .
            $_language->module[ 'show_details' ] . '</b></a>';
        $squadID = $ds[ 'squadID' ];
        $results = '';
        $awards = '';
        $challenge = '';

        if ($ds[ 'gamesquad' ]) {
            $results = '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $squadID .
                '&amp;sort=date&amp;only=squad" class="btn btn-primary">' . $_language->module[ 'results' ] . '</a>';
            $awards = '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID .
                '&amp;page=1" class="btn btn-primary">' . $_language->module[ 'awards' ] . '</a>';
            $challenge =
                '<a href="index.php?site=challenge" class="btn btn-primary">' . $_language->module[ 'challenge' ] .
                '</a>';
        }

        $bgcat = BGCAT;
        eval ("\$squads = \"" . gettemplate("squads") . "\";");
        echo $squads;

        $i++;
    }
}
