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

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

/* define calendar functions */

function print_calendar($mon, $year)
{
    global $dates, $first_day, $start_day, $_language;

    $first_day = mktime(0, 0, 0, $mon, 1, $year);
    $start_day = date("w", $first_day);
    if ($start_day == 0) {
        $start_day = 7;
    }
    $res = getdate($first_day);
    $month_name = $res["month"];
    $no_days_in_month = date("t", $first_day);

    //If month's first day does not start with first Sunday, fill table cell with a space
    for ($i = 1; $i <= $start_day; $i++) {
        $dates[1][$i] = " ";
    }

    $row = 1;
    $col = $start_day;
    $num = 1;
    while ($num <= 31) {
        if ($num > $no_days_in_month) {
            break;
        } else {
            $dates[$row][$col] = $num;
            if (($col + 1) > 7) {
                $row++;
                $col = 1;
            } else {
                $col++;
            }

            $num++;
        }
    }

    $mon_num = date("n", $first_day);
    $temp_yr = $next_yr = $prev_yr = $year;

    $prev = $mon_num - 1;
    if ($prev < 10) {
        $prev = "0" . $prev;
    }
    $next = $mon_num + 1;
    if ($next < 10) {
        $next = "0" . $next;
    }

    //If January is currently displayed, month previous is December of previous year
    if ($mon_num == 1) {
        $prev_yr = $year - 1;
        $prev = 12;
    }

    //If December is currently displayed, month next is January of next year
    if ($mon_num == 12) {
        $next_yr = $year + 1;
        $next = 1;
    }

    echo '<table class="table">
    <tr>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=01">' .
        mb_substr($_language->module['jan'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=02">' .
        mb_substr($_language->module['feb'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=03">' .
        mb_substr($_language->module['mar'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=04">' .
        mb_substr($_language->module['apr'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=05">' .
        mb_substr($_language->module['may'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=06">' .
        mb_substr($_language->module['jun'], 0, 3) . '</a></td>
    </tr>
    <tr>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=07">' .
        mb_substr($_language->module['jul'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=08">' .
        mb_substr($_language->module['aug'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=09">' .
        mb_substr($_language->module['sep'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=10">' .
        mb_substr($_language->module['oct'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=11">' .
        mb_substr($_language->module['nov'], 0, 3) . '</a></td>
      <td align="center"><a class="category" href="index.php?site=calendar&amp;month=12">' .
        mb_substr($_language->module['dec'], 0, 3) . '</a></td>
    </tr>
    </table>';

    echo '<a name="event"></a><table class="table table-bordered">
    <tr>
      <th class="text-center"><a class="titlelink" href="index.php?site=calendar&amp;month=' . $prev .
        '&amp;year=' . $prev_yr . '">&laquo; ' .
        mb_substr($_language->module[strtolower(date('M', mktime(0, 0, 0, $prev, 1, $prev_yr)))], 0, 3) . '</a></th>
      <th class="text-center" colspan="5">' . $_language->module[strtolower(date("M", $first_day))] . ' ' .
        $temp_yr . '</th>
      <th class="text-center"><a class="titlelink" href="index.php?site=calendar&amp;month=' . $next .
        '&amp;year=' . $next_yr . '">' . mb_substr(
            $_language->module[strtolower(date('M', mktime(0, 0, 0, $next, 1, $next_yr)))],
            0,
            3
        )
        . ' &raquo;</a></th>
    </tr>
    <tr>
      <td width="14%" align="center">' . $_language->module['mon'] . '</td>
      <td width="14%" align="center">' . $_language->module['tue'] . '</td>
      <td width="14%" align="center">' . $_language->module['wed'] . '</td>
      <td width="14%" align="center">' . $_language->module['thu'] . '</td>
      <td width="14%" align="center">' . $_language->module['fri'] . '</td>
      <td width="14%" align="center">' . $_language->module['sat'] . '</td>
      <td width="16%" align="center">' . $_language->module['sun'] . '</td>
    </tr>
    <tr>';

    $days = date("t", mktime(0, 0, 0, $mon, 1, $year)); //days of selected month
    switch ($days) {
        case 28:
            $end = ($start_day > 1) ? 5 : 4;
            break;
        case 29:
            $end = 5;
            break;
        case 30:
            $end = ($start_day == 7) ? 6 : 5;
            break;
        case 31:
            $end = ($start_day > 5) ? 6 : 5;
            break;
        default:
            $end = 6;
    }
    $count = 0;
    for ($row = 1; $row <= $end; $row++) {
        for ($col = 1; $col <= 7; $col++) {
            if (!isset($dates[$row][$col])) {
                $dates[$row][$col] = " ";
            }
            if (!strcmp($dates[$row][$col], " ")) {
                $count++;
            }

            $t = $dates[$row][$col];
            if ($t < 10) {
                $tag = "0$t";
            } else {
                $tag = $t;
            }

            // DATENBANK ABRUF
            $start_date = mktime(0, 0, 0, $mon, (int)$t, $year);
            $end_date = mktime(23, 59, 59, $mon, (int)$t, $year);

            unset($termin);

            $ergebnis = safe_query("SELECT * FROM " . PREFIX . "upcoming");
            $anz = mysqli_num_rows($ergebnis);
            if ($anz) {
                $termin = '';
                while ($ds = mysqli_fetch_array($ergebnis)) {
                    if ($ds['type'] == "d") {
                        if (($start_date <= $ds['date'] && $end_date >= $ds['date']) ||
                            ($start_date >= $ds['date'] && $end_date <= $ds['enddate']) ||
                            ($start_date <= $ds['enddate'] && $end_date >= $ds['enddate'])
                        ) {
                            $termin .=
                                '<a href="index.php?site=calendar&amp;tag=' . $t . '&amp;month=' . $mon . '&amp;year=' .
                                $year . '#event">' . clearfromtags($ds['short']) . '</a><br>';
                        }
                    } else {
                        if ($ds['date'] >= $start_date && $ds['date'] <= $end_date) {
                            $begin = getformattime($ds['date']);
                            $termin .=
                                '<a href="index.php?site=calendar&amp;tag=' . $t . '&amp;month=' . $mon . '&amp;year=' .
                                $year . '">' . $begin . ' ' . clearfromtags($ds['opptag']) . '</a><br>';
                        }
                    }
                }
            } else {
                $termin = "<br><br>";
            }
            // DB ABRUF ENDE

            //If date is today, highlight it
            if (($t == date("j")) && ($mon == date("n")) && ($year == date("Y"))) {
                echo '<td height="40" valign="top" bgcolor="' . BG_4 . '"><b>' . $t . '</b><br>' . $termin . '</td>';
            } else { //  If the date is absent ie after 31, print space
                if ($t === ' ') {
                    echo '<td height="40" valign="top" style="background-color:' . BG_1 . ';">&nbsp;</td>';
                } else {
                    echo
                        '<td height="40" valign="top" style="background-color:' . BG_2 . ';">' . $t . '<br>' . $termin .
                        '</td>';
                }
            }
        }
        if (($row + 1) != ($end + 1)) {
            echo '</tr><tr>';
        } else {
            echo '</tr>';
        }
    }
    echo '<tr>
      <td colspan="7" align="center"><a class="category" href="index.php?site=calendar#event"><b>' .
        $_language->module['today_events'] . '</b></a></td>
    </tr>
  </table>
  <br><br>';
}

function print_termine($tag, $month, $year)
{
    global $wincolor;
    global $loosecolor;
    global $drawcolor;
    global $userID;
    global $_language;

    $_language->readModule('calendar');

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    $start_date = mktime(0, 0, 0, $month, $tag, $year);
    $end_date = mktime(23, 59, 59, $month, $tag, $year);
    unset($termin);

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "upcoming");
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        while ($ds = mysqli_fetch_array($ergebnis)) {
            if ($ds['type'] == "c") {
                if ($ds['date'] >= $start_date && $ds['date'] <= $end_date) {
                    $date = getformatdate($ds['date']);
                    $time = getformattime($ds['date']);
                    $squad = getsquadname($ds['squad']);
                    $oppcountry = "[flag]" . $ds['oppcountry'] . "[/flag]";
                    $oppcountry = flags($oppcountry);
                    $opponent = $oppcountry . ' <a href="' . $ds['opphp'] . '" target="_blank">' .
                        clearfromtags($ds['opptag']) . ' / ' . clearfromtags($ds['opponent']) . '</a>';
                    $maps = clearfromtags($ds['maps']);
                    $server = clearfromtags($ds['server']);
                    $league = '<a href="' . $ds['leaguehp'] . '" target="_blank">' . clearfromtags($ds['league']) .
                        '</a>';
                    if (isclanmember($userID)) {
                        $warinfo = cleartext($ds['warinfo']);
                    } else {
                        $warinfo = $_language->module['you_have_to_be_clanmember'];
                    }
                    $players = "";
                    $announce = "";
                    $adminaction = '';
                    if (isclanmember($userID) or isanyadmin($userID)) {
                        $anmeldung =
                            safe_query(
                                "SELECT * FROM " . PREFIX . "upcoming_announce WHERE upID='" . $ds['upID'] . "'"
                            );
                        if (mysqli_num_rows($anmeldung)) {
                            $i = 1;
                            while ($da = mysqli_fetch_array($anmeldung)) {
                                if ($da['status'] == "y") {
                                    $fontcolor = $wincolor;
                                } elseif ($da['status'] == "n") {
                                    $fontcolor = $loosecolor;
                                } else {
                                    $fontcolor = $drawcolor;
                                }

                                if ($i > 1) {
                                    $players .= ', <a href="index.php?site=profile&amp;id=' . $da['userID'] .
                                        '"><font color="' . $fontcolor . '">' . getnickname($da['userID']) .
                                        '</font></a>';
                                } else {
                                    $players .= '<a href="index.php?site=profile&amp;id=' . $da['userID'] .
                                        '"><font color="' . $fontcolor . '">' . getnickname($da['userID']) .
                                        '</font></a>';
                                }
                                $i++;
                            }
                        } else {
                            $players = $_language->module['no_announced'];
                        }

                        if (issquadmember($userID, $ds['squad']) && $ds['date'] > time()) {
                            $announce
                                = '&#8226; <a href="index.php?site=calendar&amp;action=announce&amp;upID=' .
                                $ds['upID'] . '">' .
                                $_language->module['announce_here'] . '
                                </a>';
                        } else {
                            $announce = "";
                        }

                        if (isclanwaradmin($userID)) {
                            $adminaction = '<div class="text-right">
                                <input
                                    type="button"
                                    onclick="window.open(
                                        \'clanwars.php?action=new&amp;upID=' . $ds['upID'] . '\',
                                        \'Clanwars\',
                                        \'toolbar=no,status=no,scrollbars=yes,width=800,height=490\'
                                    )"
                                    value="' . $_language->module['add_clanwars'] . '"
                                    class="btn btn-danger">

                                <a href="index.php?site=calendar&amp;action=editwar&amp;upID=' . $ds['upID'] . '"
                                    class="btn btn-danger">
                                    ' . $_language->module['edit'] . '
                                </a>

                                <input type="button" onclick="MM_confirm(
                                        \'' . $_language->module['really_delete'] .
                                '\',
                                        \'calendar.php?action=delete&amp;upID=' . $ds['upID'] . '\'
                                    )"
                                    value="' . $_language->module['delete'] . '" class="btn btn-danger">
                            </div>';
                        } else {
                            $adminaction = '';
                        }
                    } else {
                        $players = $_language->module['access_member'];
                    }

                    eval ("\$upcoming_war_details = \"" . gettemplate("upcoming_war_details") . "\";");
                    echo $upcoming_war_details;
                }
            } else {
                if (($start_date <= $ds['date'] && $end_date >= $ds['date']) ||
                    ($start_date >= $ds['date'] && $end_date <= $ds['enddate']) ||
                    ($start_date <= $ds['enddate'] && $end_date >= $ds['enddate'])
                ) {
                    $date = getformatdate($ds['date']);
                    $time = getformattime($ds['date']);
                    $enddate = getformatdate($ds['enddate']);
                    $endtime = getformattime($ds['enddate']);
                    $title = clearfromtags($ds['title']);
                    $location =
                        '<a href="' . $ds['locationhp'] . '" target="_blank">' . clearfromtags($ds['location']) .
                        '</a>';
                    $dateinfo = cleartext($ds['dateinfo']);
                    $dateinfo = toggle($dateinfo, $ds['upID']);
                    $country = "[flag]" . $ds['country'] . "[/flag]";
                    $country = flags($country);
                    $players = "";

                    if (isclanmember($userID)) {
                        $anmeldung =
                            safe_query(
                                "SELECT * FROM " . PREFIX . "upcoming_announce WHERE upID='" . (int)$ds['upID'] . "'"
                            );
                        if (mysqli_num_rows($anmeldung)) {
                            $i = 1;
                            while ($da = mysqli_fetch_array($anmeldung)) {
                                if ($da['status'] == "y") {
                                    $fontcolor = $wincolor;
                                } elseif ($da['status'] == "n") {
                                    $fontcolor = $loosecolor;
                                } else {
                                    $fontcolor = $drawcolor;
                                }

                                if ($i > 1) {
                                    $players .= ', <a href="index.php?site=profile&amp;id=' . $da['userID'] . '">
                                        <span style="color:' . $fontcolor . '">
                                            ' . getnickname($da['userID']) . '
                                        </span>
                                    </a>';
                                } else {
                                    $players .= '<a href="index.php?site=profile&amp;id=' . $da['userID'] . '">
                                        <span style="color:' . $fontcolor . '">
                                            ' . getnickname($da['userID']) .
                                        '</span>
                                    </a>';
                                }
                                $i++;
                            }
                        } else {
                            $players = $_language->module['no_announced'];
                        }

                        if (isclanmember($userID) && $ds['date'] > time()) {
                            $announce = '&#8226; <a href="index.php?site=calendar&amp;action=announce&amp;upID=' .
                                $ds['upID'] . '">' . $_language->module['announce_here'] . '</a>';
                        } else {
                            $announce = '';
                        }

                        if (isclanwaradmin($userID)) {
                            $adminaction = '<div align="right">
                                <a class="btn btn-danger" href="index.php?site=calendar&amp;action=editdate&amp;upID=' .
                                $ds['upID'] . '">' .
                                $_language->module['edit'] . '
                                </a>
                                <input type="button" class="btn btn-danger" onclick="MM_confirm(
                                        \'' . $_language->module['really_delete'] . '\',
                                        \'calendar.php?action=delete&amp;upID=' . $ds['upID'] . '\'
                                    )" value="' . $_language->module['delete'] . '
                                ">
                            </div>';
                        } else {
                            $adminaction = '';
                        }
                    } else {
                        $players = $_language->module['access_member'];
                        $announce = '';
                        $adminaction = '';
                    }

                    eval ("\$upcoming_date_details = \"" . gettemplate("upcoming_date_details") . "\";");
                    echo $upcoming_date_details;
                }
            }
        }
    } else {
        echo $_language->module['no_entries'];
    }
}

/* beginn processing file */

if ($action === "savewar") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('calendar');
    if (!isclanwaradmin($userID)) {
        die($_language->module['no_access']);
    }

    $date = strtotime($_POST['date']);

    $squad = $_POST['squad'];
    $opponent = $_POST['opponent'];
    $opptag = $_POST['opptag'];
    $opphp = $_POST['opphp'];
    $oppcountry = $_POST['oppcountry'];
    $maps = $_POST['maps'];
    $server = $_POST['server'];
    $league = $_POST['league'];
    $leaguehp = $_POST['leaguehp'];
    $warinfo = $_POST['message'];
    $chID = $_POST['chID'];
    if (isset($_POST['messages'])) {
        $messages = true;
    } else {
        $messages = false;
    }

    safe_query(
        "INSERT INTO
            " . PREFIX . "upcoming (
                `date`,
                `type`,
                `squad`,
                `opponent`,
                `opptag`,
                `opphp`,
                `oppcountry`,
                `maps`,
                `server`,
                `league`,
                `leaguehp`,
                `warinfo`
            )
            values (
                '" . $date . "',
                'c',
                '" . $squad . "',
                '" . $opponent . "',
                '" . $opptag . "',
                '" . $opphp . "',
                '" . $oppcountry . "',
                '" . $maps . "',
                '" . $server . "',
                '" . $league . "',
                '" . $leaguehp . "',
                '" . $warinfo . "'
            )"
    );

    if (isset($chID) and $chID > 0) {
        safe_query("DELETE FROM " . PREFIX . "challenge WHERE chID='" . $chID . "'");
    }

    if ($messages) {
        $replace = [
            '%date%' => getformatdate($date),
            '%opponent_flag%' => $oppcountry,
            '%opp_hp%' => $opphp,
            '%opponent%' => $opponent,
            '%league_hp%' => $leaguehp,
            '%league%' => $league,
            '%warinfo%' => $warinfo
        ];
        $ergebnis = safe_query("SELECT userID FROM " . PREFIX . "squads_members WHERE squadID='$squad'");
        $tmp_lang = new \webspell\Language();
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $id = $ds['userID'];
            $tmp_lang->setLanguage(getuserlanguage($id));
            $tmp_lang->readModule('calendar');
            $title = $tmp_lang->module['clanwar_message_title'];
            $message = $tmp_lang->module['clanwar_message'];
            $message = str_replace(array_keys($replace), array_values($replace), $message);
            sendmessage($id, $title, $message);
        }
    }
    header(
        "Location: index.php?site=calendar&tag=" .
        date("j", $date) . "&month=" .
        date("n", $date) . "&year=" .
        date("Y", $date)
    );
} elseif ($action === "delete") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('calendar');
    if (!isclanwaradmin($userID)) {
        die($_language->module['no_access']);
    }
    $upID = $_GET['upID'];

    safe_query("DELETE FROM " . PREFIX . "upcoming WHERE upID='$upID'");
    safe_query("DELETE FROM " . PREFIX . "upcoming_announce WHERE upID='$upID'");
    header("Location: index.php?site=calendar");
} elseif ($action === "saveannounce") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('calendar');
    if (!isclanmember($userID)) {
        die($_language->module['no_access']);
    }

    $ds = mysqli_fetch_assoc(
        safe_query(
            "SELECT date FROM " . PREFIX . "upcoming WHERE upID=" . (int)$_POST['upID'] . " AND date>" . time()
        )
    );
    if (isset($ds['date'])) {
        $tag = date('d', $ds['date']);
        $month = date('m', $ds['date']);
        $year = date('y', $ds['date']);

        $ergebnis = safe_query(
            "SELECT
                annID
            FROM
                " . PREFIX . "upcoming_announce
            WHERE
                upID='" . (int)$_POST['upID'] . "'
            AND
                userID='" . (int)$userID."'"
        );

        if (mysqli_num_rows($ergebnis)) {
            $ds = mysqli_fetch_array($ergebnis);
            safe_query(
                "UPDATE
                    " . PREFIX . "upcoming_announce
                SET
                    status='" . $_POST['status']{0} . "'
                WHERE
                    annID='" . $ds['annID'] . "'"
            );
        } else {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "upcoming_announce (
                        `upID`,
                        `userID`,
                        `status`
                    )
                values (
                    '" . (int)$_POST['upID'] . "',
                    '$userID',
                    '" . $_POST['status']{0} . "'
                ) "
            );
        }
        header("Location: index.php?site=calendar&tag=$tag&month=$month&year=$year");
    } else {
        header("Location: index.php?site=calendar");
    }
} elseif ($action === "saveeditdate") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('calendar');
    if (!isclanwaradmin($userID)) {
        die($_language->module['no_access']);
    }

    $date_start = strtotime($_POST['date_start']);
    $date_end = strtotime($_POST['date_end']);

    safe_query(
        "UPDATE
            " . PREFIX . "upcoming
        SET
            date='$date_start',
            enddate='$date_end',
            short='" . $_POST['short'] . "',
            title='" . $_POST['title'] . "',
            country='" . $_POST['country'] . "',
            location='" . $_POST['location'] . "',
            locationhp='" . $_POST['locationhp'] . "',
            dateinfo='" . $_POST['message'] . "'
        WHERE
            upID='" . (int)$_POST['upID']."'"
    );

    header(
        "Location: index.php?site=calendar&tag=" .
        date("j", $date_start) . "&month=" .
        date("n", $date_start) . "&year=" .
        date("Y", $date_start)
    );
} elseif ($action === "savedate") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('calendar');
    if (!isclanwaradmin($userID)) {
        die($_language->module['no_access']);
    }

    $date_start = strtotime($_POST['date_start']);
    $date_end = strtotime($_POST['date_end']);

    safe_query(
        "INSERT INTO
            " . PREFIX . "upcoming (
                `date`,
                `type`,
                `enddate`,
                `short`,
                `title`,
                `country`,
                `location`,
                `locationhp`,
                `dateinfo`
            )
            values (
                '$date_start',
                'd',
                '" . $date_end . "',
                '" . $_POST['short'] . "',
                '" . $_POST['title'] . "',
                '" . $_POST['country'] . "',
                '" . $_POST['location'] . "',
                '" . $_POST['locationhp'] . "',
                '" . $_POST['message'] . "'
            )"
    );
    redirect(
        "index.php?site=calendar&amp;tag=" .
        date("j", $date_start) . "&amp;month=" .
        date("n", $date_start) . "&amp;year=" .
        date("Y", $date_start),
        "",
        0
    );
} elseif ($action === "saveeditwar") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('calendar');
    if (!isclanwaradmin($userID)) {
        die($_language->module['no_access']);
    }

    $upID = $_POST['upID'];

    $date = strtotime($_POST['date']);
    $squad = $_POST['squad'];
    $opponent = $_POST['opponent'];
    $opptag = $_POST['opptag'];
    $opphp = $_POST['opphp'];
    $oppcountry = $_POST['oppcountry'];
    $maps = $_POST['maps'];
    $server = $_POST['server'];
    $league = $_POST['league'];
    $leaguehp = $_POST['leaguehp'];
    $warinfo = $_POST['message'];

    safe_query(
        "UPDATE
            " . PREFIX . "upcoming
        SET
            `date` = '$date',
            `type` = 'c',
            `squad` = '$squad',
            `opponent` = '$opponent',
            `opptag` = '$opptag',
            `opphp` = '$opphp',
            `oppcountry` = '$oppcountry',
            `maps` = '$maps',
            `server` = '$server',
            `league` = '$league',
            `leaguehp` = '$leaguehp',
            `warinfo` = '$warinfo'
        WHERE
            `upID` = '$upID' "
    );

    header(
        "Location: index.php?site=calendar&tag=" .
        date("j", $date) . "&month=" .
        date("n", $date) . "&year=" .
        date("Y", $date)
    );
} elseif ($action === "addwar") {
    $_language->readModule('calendar');
    if (isclanwaradmin($userID)) {

        eval ("\$title_calendar = \"" . gettemplate("title_calendar") . "\";");
        echo $title_calendar;

        echo '<a href="index.php?site=calendar&amp;action=addwar" class="btn btn-danger">' .
            $_language->module['add_clanwar'] . '
            </a>
            <a href="index.php?site=calendar&amp;action=adddate" class="btn btn-danger">' .
            $_language->module['add_event'] . '
            </a><br><br>';

        $squads = getgamesquads();

        $opphp = "http://";

        $chID = 0;

        $date = date("d.m.Y H:i");

        $countries = getcountries();

        if (isset($_GET['chID'])) {

            $chID = (int)$_GET['chID'];
            $ergebnis = safe_query("SELECT * FROM " . PREFIX . "challenge WHERE chID='" . $chID . "'");
            $ds = mysqli_fetch_array($ergebnis);
            $date = date("d.m.Y H:i", $ds['cwdate']);

            $squads = str_replace(" selected=\"selected\"", "", $squads);
            $squads = str_replace(
                '<option value="' . $ds['squadID'] . '">',
                '<option value="' . $ds['squadID'] . '" selected="selected">',
                $squads
            );

            $map = $ds['map'];
            $server = $ds['server'];
            $opponent = $ds['opponent'];
            $league = $ds['league'];
            $info = $ds['info'];

            $countries = str_replace(" selected=\"selected\"", "", $countries);
            $countries = str_replace(
                '<option value="' . $ds['oppcountry'] . '">',
                '<option value="' . $ds['oppcountry'] . '" selected="selected">',
                $countries
            );

            $opphp = $ds['opphp'];
        } else {
            $map = '';
            $server = '';
            $opponent = '';
            $league = '';
            $info = '';
        }

        $bg1 = BG_1;
        eval ("\$upcoming_war_new = \"" . gettemplate("upcoming_war_new") . "\";");
        echo $upcoming_war_new;
    } else {
        redirect('index.php?site=calendar', $_language->module['no_access']);
    }
} elseif ($action === "editwar") {
    $_language->readModule('calendar');
    if (isclanwaradmin($userID)) {

        eval ("\$title_calendar = \"" . gettemplate("title_calendar") . "\";");
        echo $title_calendar;

        echo '<a href="index.php?site=calendar&amp;action=addwar" class="btn btn-danger">' .
            $_language->module['add_clanwar'] . '
            </a>
            <a href="index.php?site=calendar&amp;action=adddate" class="btn btn-danger">' .
            $_language->module['add_event'] . '
            </a><br><br>';


        $upID = $_GET['upID'];
        $ds = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "upcoming WHERE upID='$upID'"));

        $date = date("d.m.Y H:i", $ds['date']);

        $squads = getgamesquads();
        $squads = str_replace(
            'value="' . $ds['squad'] . '"',
            'value="' . $ds['squad'] . '" selected="selected"',
            $squads
        );
        $league = htmlspecialchars($ds['league']);
        $leaguehp = htmlspecialchars($ds['leaguehp']);
        $opponent = htmlspecialchars($ds['opponent']);
        $opptag = htmlspecialchars($ds['opptag']);
        $opphp = htmlspecialchars($ds['opphp']);
        $maps = htmlspecialchars($ds['maps']);
        $server = htmlspecialchars($ds['server']);
        $warinfo = htmlspecialchars($ds['warinfo']);
        $countries = getcountries();
        $countries = str_replace(
            'value="' . $ds['oppcountry'] . '"',
            'value="' . $ds['oppcountry'] . '" selected="selected"',
            $countries
        );

        eval ("\$upcoming_war_edit = \"" . gettemplate("upcoming_war_edit") . "\";");
        echo $upcoming_war_edit;
    } else {
        redirect('index.php?site=calendar', $_language->module['no_access']);
    }
} elseif ($action === "adddate") {
    $_language->readModule('calendar');
    if (isclanwaradmin($userID)) {

        eval ("\$title_calendar = \"" . gettemplate("title_calendar") . "\";");
        echo $title_calendar;

        echo
            '<a href="index.php?site=calendar&amp;action=addwar" class="btn btn-danger">' .
            $_language->module['add_clanwar'] . '
            </a>
            <a href="index.php?site=calendar&amp;action=adddate" class="btn btn-danger"> ' .
            $_language->module['add_event'] . '
            </a><br><br>';

        $date = date("d.m.Y H:i");

        $squads = getgamesquads();
        $countries = getcountries();

        $bg1 = BG_1;
        eval ("\$upcoming_date_new = \"" . gettemplate("upcoming_date_new") . "\";");
        echo $upcoming_date_new;
    } else {
        redirect('index.php?site=calendar', $_language->module['no_access']);
    }
} elseif ($action === "editdate") {
    $_language->readModule('calendar');
    if (isclanwaradmin($userID)) {

        eval ("\$title_calendar = \"" . gettemplate("title_calendar") . "\";");
        echo $title_calendar;

        echo
            '<a href="index.php?site=calendar&amp;action=addwar" class="btn btn-danger">' .
            $_language->module['add_clanwar'] . '
            </a>
            <a href="index.php?site=calendar&amp;action=adddate" class="btn btn-danger">' .
            $_language->module['add_event'] . '
            </a><br><br>';

        $upID = $_GET['upID'];
        $ds = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "upcoming WHERE upID='$upID'"));

        $date_start = date("d.m.Y H:i", $ds['date']);
        $date_end = date("d.m.Y H:i", $ds['enddate']);

        $countries = getcountries();
        $countries = str_replace(' selected="selected"', '', $countries);
        $countries =
            str_replace(
                'value="' . $ds['country'] . '"',
                'value="' . $ds['country'] . '" selected="selected"',
                $countries
            );

        $short = htmlspecialchars($ds['short']);
        $title = htmlspecialchars($ds['title']);
        $location = htmlspecialchars($ds['location']);
        $locationhp = htmlspecialchars($ds['locationhp']);
        $dateinfo = htmlspecialchars($ds['dateinfo']);

        $bg1 = BG_1;
        eval ("\$upcoming_date_edit = \"" . gettemplate("upcoming_date_edit") . "\";");
        echo $upcoming_date_edit;
    } else {
        redirect('index.php?site=calendar', $_language->module['no_access']);
    }
} elseif ($action === "announce" && isclanmember($userID)) {

    $_language->readModule('calendar');

    eval ("\$title_calendar = \"" . gettemplate("title_calendar") . "\";");
    echo $title_calendar;

    if (isset($_GET['upID'])) {

        $upID = (int)$_GET['upID'];

        eval ("\$upcomingannounce = \"" . gettemplate("upcomingannounce") . "\";");
        echo $upcomingannounce;
    }
} else {

    $_language->readModule('calendar');

    eval ("\$title_calendar = \"" . gettemplate("title_calendar") . "\";");
    echo $title_calendar;

    if (isclanwaradmin($userID)) {
        echo
            '<a href="index.php?site=calendar&amp;action=addwar" class="btn btn-danger">' .
            $_language->module['add_clanwar'] . '
            </a>
            <a href="index.php?site=calendar&amp;action=adddate" class="btn btn-danger">' .
            $_language->module['add_event'] . '
            </a><br><br>';
    }

    if (isset($_GET['month'])) {
        $month = (int)$_GET['month'];
    } else {
        $month = date("m");
    }

    if (isset($_GET['year'])) {
        $year = (int)$_GET['year'];
    } else {
        $year = date("Y");
    }

    if (isset($_GET['tag'])) {
        $tag = (int)$_GET['tag'];
    } else {
        $tag = date("d");
    }

    print_calendar($month, $year);
    print_termine($tag, $month, $year);
}
