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

function getuserformatdate($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT date_format FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return $ds['date_format'];
}

function getuserformattime($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT time_format FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return $ds['time_format'];
}

function getformatdate($date)
{
    global $userID, $default_format_date;

    if ($userID && !isset($_GET['userID']) && !isset($_POST['userID'])) {
        $DateFormat = date(getuserformatdate($userID), $date);
    } else {
        $DateFormat = date($default_format_date, $date);
    }
    return $DateFormat;
}

function getformattime($time)
{
    global $userID, $default_format_time;

    if ($userID && !isset($_GET['userID']) && !isset($_POST['userID'])) {
        $timeFormat = date(getuserformattime($userID), $time);
    } else {
        $timeFormat = date($default_format_time, $time);
    }
    return $timeFormat;
}

function getformatdatetime($date_time)
{
    global $userID, $default_format_date, $default_format_time;

    if ($userID && !isset($_GET['userID']) && !isset($_POST['userID'])) {
        $datetimeFormat = date((getuserformatdate($userID) . " - " . getuserformattime($userID)), $date_time);
    } else {
        $datetimeFormat = date(($default_format_date . " - " . $default_format_time), $date_time);
    }
    return $datetimeFormat;
}
