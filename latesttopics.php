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
    $_language->readModule('latesttopics');
}
$usergroups = array();
if ($loggedin) {
    $usergroups[ ] = 'user';
    $get = safe_query(
        "SELECT
            *
        FROM
            " . PREFIX . "user_forum_groups
        WHERE
            userID='" . $userID . "'"
    );
    $data = mysqli_fetch_row($get);

    $counter = count($data);
    for ($i = 2; $i < $counter; $i++) {
        if ($data[ $i ] == 1) {
            $info = mysqli_fetch_field_direct($get, $i);
            $usergroups[ ] = $info->name;
        }
    }
}
$userallowedreadgrps = array();
$userallowedreadgrps[ 'boardIDs' ] = array();
$userallowedreadgrps[ 'catIDs' ] = array();
$get = safe_query("SELECT boardID FROM " . PREFIX . "forum_boards WHERE readgrps = ''");
while ($ds = mysqli_fetch_assoc($get)) {
    $userallowedreadgrps[ 'boardIDs' ][ ] = $ds[ 'boardID' ];
}
$get = safe_query("SELECT catID FROM " . PREFIX . "forum_categories WHERE readgrps = ''");
while ($ds = mysqli_fetch_assoc($get)) {
    $userallowedreadgrps[ 'catIDs' ][ ] = $ds[ 'catID' ];
}
if ($loggedin) {
    $get = safe_query("SELECT boardID, readgrps FROM " . PREFIX . "forum_boards WHERE readgrps != ''");
    while ($ds = mysqli_fetch_assoc($get)) {
        $groups = explode(";", $ds[ 'readgrps' ]);
        $allowed = array_intersect($groups, $usergroups);
        if (!count($allowed)) {
            continue;
        }
        $userallowedreadgrps[ 'boardIDs' ][ ] = $ds[ 'boardID' ];
    }
    $get = safe_query("SELECT catID, readgrps FROM " . PREFIX . "forum_categories WHERE readgrps != ''");
    while ($ds = mysqli_fetch_assoc($get)) {
        $groups = explode(";", $ds[ 'readgrps' ]);
        $allowed = array_intersect($groups, $usergroups);
        if (!count($allowed)) {
            continue;
        }
        $userallowedreadgrps[ 'catIDs' ][ ] = $ds[ 'catID' ];
    }
}
if (empty($userallowedreadgrps[ 'catIDs' ])) {
    $userallowedreadgrps[ 'catIDs' ][ ] = 0;
}
if (empty($userallowedreadgrps[ 'boardIDs' ])) {
    $userallowedreadgrps[ 'boardIDs' ][ ] = 0;
}
$ergebnis = safe_query(
    "SELECT
        t.*, u.nickname, b.name
    FROM
        " . PREFIX . "forum_topics t
    LEFT JOIN
        " . PREFIX . "user u
    ON
        u.userID = t.lastposter
    LEFT JOIN
        " . PREFIX . "forum_boards b
    ON
        b.boardID = t.boardID
    WHERE
        b.category
    IN
        (" . implode(",", $userallowedreadgrps[ 'catIDs' ]) . ")
    AND
        t.boardID
    IN
        (" . implode(",", $userallowedreadgrps[ 'boardIDs' ]) . ")
    AND
        t.moveID = '0'
    ORDER BY
        t.lastdate
    DESC
        LIMIT 0," . $maxlatesttopics
);
$anz = mysqli_num_rows($ergebnis);
if ($anz) {
    eval ("\$latesttopics_head = \"" . gettemplate("latesttopics_head") . "\";");
    echo $latesttopics_head;
    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($ds[ 'readgrps' ] != "") {
            $usergrps = explode(";", $ds[ 'readgrps' ]);
            $usergrp = 0;
            foreach ($usergrps as $value) {
                if (isinusergrp($value, $userID)) {
                    $usergrp = 1;
                    break;
                }
            }
            if (!$usergrp && !ismoderator($userID, $ds[ 'boardID' ])) {
                continue;
            }
        }
        if ($n % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        $topictitle_full = clearfromtags($ds[ 'topic' ]);
        $topictitle = unhtmlspecialchars($topictitle_full);
        if (mb_strlen($topictitle) > $maxlatesttopicchars) {
            $topictitle = mb_substr($topictitle, 0, $maxlatesttopicchars);
            $topictitle .= '...';
        }
        $topictitle = htmlspecialchars($topictitle);

        $last_poster = $ds[ 'nickname' ];
        $board = $ds[ 'name' ];
        $date = getformatdatetime($ds[ 'lastdate' ]);
        $small_date = date('d.m H:i', $ds[ 'lastdate' ]);

        $latesticon = '<img src="images/icons/' . $ds[ 'icon' ] . '" width="15" height="15" alt="">';
        $boardlink = '<a href="index.php?site=forum&amp;board=' . $ds[ 'boardID' ] . '">' . $board . '</a>';
        $topiclink = 'index.php?site=forum_topic&amp;topic=' . $ds[ 'topicID' ] . '&amp;type=ASC&amp;page=' .
            ceil(($ds[ 'replys' ] + 1) / $maxposts);
        $replys = $ds[ 'replys' ];

        $replys_text = ($replys == 1) ? $_language->module[ 'reply' ] : $_language->module[ 'replies' ];

        eval ("\$latesttopics_content = \"" . gettemplate("latesttopics_content") . "\";");
        echo $latesttopics_content;
        $n++;
    }
    eval ("\$latesttopics_foot = \"" . gettemplate("latesttopics_foot") . "\";");
    echo $latesttopics_foot;
}

unset($board);
