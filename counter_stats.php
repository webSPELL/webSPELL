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

$_language->readModule('counter');

eval ("\$title_counter_stats = \"" . gettemplate("title_counter_stats") . "\";");
echo $title_counter_stats;

$time = time();
$date = getformatdate($time);
$dateyesterday = getformatdate($time - (24 * 3600));
$datemonth = date(".m.Y", time());

$ergebnis = safe_query("SELECT `hits` FROM `" . PREFIX . "counter`");
$ds = mysqli_fetch_array($ergebnis);
$us = mysqli_num_rows(safe_query("SELECT `userID` FROM `" . PREFIX . "user`"));

$total = $ds[ 'hits' ];
$dt = mysqli_fetch_array(safe_query("SELECT `count` FROM `" . PREFIX . "counter_stats` WHERE `dates` = '$date'"));
if ($dt[ 'count' ]) {
    $today = $dt[ 'count' ];
} else {
    $today = 0;
}

$dy = mysqli_fetch_array(
    safe_query(
        "SELECT
            `count`
        FROM
            `" . PREFIX . "counter_stats`
        WHERE
            `dates='$dateyesterday'`"
    )
);

if ($dy[ 'count' ]) {
    $yesterday = $dy[ 'count' ];
} else {
    $yesterday = 0;
}

$month = 0;
$month_max = 0;
$monthquery = safe_query("SELECT `count` FROM `" . PREFIX . "counter_stats` WHERE `dates` LIKE '%$datemonth'");
while ($dm = mysqli_fetch_array($monthquery)) {
    $month = $month + $dm[ 'count' ];
    if ($dm[ 'count' ] > $month_max) {
        $month_max = $dm[ 'count' ];
    }
}
if ($month == 0) {
    $month = 1;
}
$monatsstat = '';

for ($i = date("d", time()); $i > 0; $i--) {

    if (mb_strlen($i) < 2) {
        $i = "0" . $i;
    }

    $tmp = mysqli_fetch_array(
        safe_query(
            "SELECT
                `count`
            FROM
                `" . PREFIX . "counter_stats`
            WHERE
                `dates` = '" . $i . $datemonth
        )
    );

    if ($tmp[ 'count' ]) {
        $visits = $tmp[ 'count' ];
    } else {
        $visits = 0;
    }

    $i % 2 ? $backgroundcolor1 = BG_1 : $backgroundcolor1 = BG_2;
    $i % 2 ? $backgroundcolor2 = BG_3 : $backgroundcolor2 = BG_4;
    $prozent = $visits * 100 / $month_max;
    $monatsstat .= '<li class="list-group-item"><span class="badge">' . $visits . '</span> ' . $i . $datemonth .
        '<div class="progress"><div class="progress-bar progress-bar-info" style="width: ' . (round($prozent)) .
        '%"></div></div></li>';
}

$tmp = mysqli_fetch_array(safe_query("SELECT `online` FROM `" . PREFIX . "counter`"));
$days_online = round((time() - $tmp[ 'online' ]) / (3600 * 24));

if (!$days_online) {
    $days_online = 1;
}

$perday = round($total / $days_online, 2);
$perhour = round($total / $days_online / 24, 2);
$permonth = round($total / $days_online * 24, 2);

$tmp = mysqli_fetch_array(safe_query("SELECT `max(count)` as `MAXIMUM` FROM `" . PREFIX . "counter_stats`"));
$maxvisits = $tmp[ 'MAXIMUM' ];

$online_lasthour =
    mysqli_num_rows(safe_query("SELECT `ip` FROM `" . PREFIX . "counter_iplist` WHERE `del` > " . (time() - 3600)));
$online = mysqli_num_rows(safe_query("SELECT `time` FROM `" . PREFIX . "whoisonline`"));
$dm = mysqli_fetch_array(safe_query("SELECT `maxonline` FROM `" . PREFIX . "counter`"));
$maxonline = $dm[ 'maxonline' ];

$guests = mysqli_num_rows(safe_query("SELECT `ip` FROM `" . PREFIX . "whoisonline` WHERE `userID` = ''"));
$user = mysqli_num_rows(safe_query("SELECT `userID` FROM `" . PREFIX . "whoisonline` WHERE `ip` = ''"));
$useronline = $guests + $user;

if ($user == 1) {
    $user_on = '1 ' . $_language->module[ 'user' ];
} else {
    $user_on = $user . ' ' . $_language->module[ 'users' ];
}

if ($guests == 1) {
    $guests_on = '1 ' . $_language->module[ 'guest' ];
} else {
    $guests_on = $guests . ' ' . $_language->module[ 'guests' ];
}

// average age of all users
$get =
    mysqli_fetch_assoc(
        safe_query(
            "SELECT
                `ROUND(SUM(DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y')) / COUNT(userID))` AS
                    `avg_age`
            FROM
                `" . PREFIX . "user`
            WHERE
                birthday > 0"
        )
    );
$avg_age_user = $get[ 'avg_age' ];
// average age of clanmembers
$avg_age_member = 0;
$get = safe_query(
    "SELECT
        `SUM(DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(u.birthday)), '%y')) / COUNT(u.userID)` AS `avg_age`
    FROM
        " . PREFIX . "squads_members m
    JOIN
        " . PREFIX . "user u ON
        u.userID = m.userID
    WHERE
        u.birthday > 0
    GROUP BY
        m.userID"
);
if (mysqli_num_rows($get)) {
    while ($ds = mysqli_fetch_assoc($get)) {
        $avg_age_member += $ds[ 'avg_age' ];
    }
    $avg_age_member = ROUND($avg_age_member / mysqli_num_rows($get), 0);
}
// get oldest/youngest member
$get_young = mysqli_fetch_assoc(
    safe_query(
        "SELECT
            DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y') AS `age`,
            `nickname`,
            `userID`
        FROM
            " . PREFIX . "user
        WHERE
            birthday > 0
        ORDER BY
            birthday DESC
        LIMIT 0,1"
    )
);
$get_old = mysqli_fetch_assoc(
    safe_query(
        "SELECT
            DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y') AS `age`,
            nickname,
            userID
        FROM
            " . PREFIX . "user
        WHERE
            birthday > 0
        ORDER BY
            birthday ASC
        LIMIT 0,1"
    )
);
eval ("\$stats = \"" . gettemplate("counter_stats") . "\";");
echo $stats;
