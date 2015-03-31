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

$_language->readModule('counter');

$date = getformatdate(time());
$dateyesterday = getformatdate(time() - (24 * 3600));
$datemonth = date(".m.Y", time());

$ergebnis = safe_query("SELECT hits FROM " . PREFIX . "counter");
$ds = mysqli_fetch_array($ergebnis);
$us = mysqli_fetch_array(safe_query("SELECT count(*) FROM " . PREFIX . "user"));
$us = $us[ 0 ];

$total = $ds[ 'hits' ];
$dt = mysqli_fetch_array(safe_query("SELECT count FROM " . PREFIX . "counter_stats WHERE dates='$date'"));
if ($dt[ 'count' ]) {
    $today = $dt[ 'count' ];
} else {
    $today = 0;
}

$dy = mysqli_fetch_array(safe_query("SELECT count FROM " . PREFIX . "counter_stats WHERE dates='$dateyesterday'"));
if ($dy[ 'count' ]) {
    $yesterday = $dy[ 'count' ];
} else {
    $yesterday = 0;
}

$month = 0;
$monthquery = safe_query("SELECT count FROM " . PREFIX . "counter_stats WHERE dates LIKE '%$datemonth'");
while ($dm = mysqli_fetch_array($monthquery)) {
    $month = $month + $dm[ 'count' ];
}

$guests = mysqli_fetch_array(safe_query("SELECT COUNT(*) FROM " . PREFIX . "whoisonline WHERE userID=''"));
$user = mysqli_fetch_array(safe_query("SELECT COUNT(*) FROM " . PREFIX . "whoisonline WHERE ip=''"));
$useronline = $guests[ 0 ] + $user[ 0 ];

if ($user[ 0 ] == 1) {
    $user_on = 1;
    $user_on_text = $_language->module[ 'user' ];
} else {
    $user_on = $user[ 0 ];
    $user_on_text = $_language->module[ 'users' ];
}
if ($guests[ 0 ] == 1) {
    $guests_on = 1;
    $guests_on_text = $_language->module[ 'guest' ];
} else {
    $guests_on = $guests[ 0 ];
    $guests_on_text = $_language->module[ 'guests' ];
}

$data_array = array();
$data_array['$today'] = $today;
$data_array['$yesterday'] = $yesterday;
$data_array['$month'] = $month;
$data_array['$total'] = $total;
$data_array['$us'] = $us;
$data_array['$user_on'] = $user_on;
$data_array['$user_on_text'] = $user_on_text;
$data_array['$guests_on'] = $guests_on;
$data_array['$guests_on_text'] = $guests_on_text;
$stats = $GLOBALS["_template"]->replaceTemplate("stats", $data_array);
echo $stats;
