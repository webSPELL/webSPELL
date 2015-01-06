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
    $_language->readModule('clanwars');
}

eval ("\$title_clanwars_details = \"" . gettemplate("title_clanwars_details") . "\";");
echo $title_clanwars_details;

echo '<p><a href="index.php?site=clanwars" class="btn btn-primary">' . $_language->module[ 'show_clanwars' ] . '</a>
<a href="index.php?site=clanwars&amp;action=stats" class="btn btn-primary">' . $_language->module[ 'stat' ] .
    '</a></p>';

$cwID = (int)$_GET[ 'cwID' ];
$ds = mysqli_fetch_array(safe_query("SELECT * FROM `" . PREFIX . "clanwars` WHERE `cwID` = '" . (int)$cwID."'"));
$date = getformatdate($ds[ 'date' ]);
$opponent = '<a href="' . getinput($ds[ 'opphp' ]) . '" target="_blank"><b>' . getinput($ds[ 'opptag' ]) . ' / ' .
            ($ds[ 'opponent' ]) . '</b></a>';
$league = '<a href="' . getinput($ds[ 'leaguehp' ]) . '" target="_blank">' . getinput($ds[ 'league' ]) . '</a>';
if (file_exists('images/games/' . $ds[ 'game' ] . '.gif')) {
    $game_ico = 'images/games/' . $ds[ 'game' ] . '.gif';
    $game = '<img src="' . $game_ico . '" width="13" height="13" alt="">';
}
else {
    $game = $ds[ 'game' ];
}
$maps = "";
$hometeam = "";
$screens = "";
$score = "";
$extendedresults = "";
$screenshots = "";
$nbr = "";

$homescr = array_sum(unserialize($ds[ 'homescore' ]));
$oppscr = array_sum(unserialize($ds[ 'oppscore' ]));
$theMaps = unserialize($ds[ 'maps' ]);

if (is_array($theMaps)) {
    $n = 1;
    foreach ($theMaps as $map) {
        if ($n == 1) {
            $maps .= $map;
        } else {
            if ($map == '') {
                $maps = $_language->module[ 'no_maps' ];
            } else {
                $maps .= ', ' . $map;
            }
        }
        $n++;
    }
}

if ($homescr > $oppscr) {
    $results_1 = '<font color="' . $wincolor . '">' . $homescr . '</font>';
    $results_2 = '<font color="' . $wincolor . '">' . $oppscr . '</font>';
} elseif ($homescr < $oppscr) {
    $results_1 = '<font color="' . $loosecolor . '">' . $homescr . '</font>';
    $results_2 = '<font color="' . $loosecolor . '">' . $oppscr . '</font>';
} else {
    $results_1 = '<font color="' . $drawcolor . '">' . $homescr . '</font>';
    $results_2 = '<font color="' . $drawcolor . '">' . $oppscr . '</font>';
}

if (isclanwaradmin($userID)) {
    $adminaction = '<input type="button" onclick="window.open(
            \'upload.php?cwID=' . $cwID . '\',
            \'Clanwars\',
            \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
            )" value="' . $_language->module[ 'upload_screenshot' ] . '" class="btn btn-danger">
<input type="button" onclick="window.open(
    \'clanwars.php?action=edit&amp;cwID=' . $ds[ 'cwID' ] . '\',
    \'Clanwars\',
    \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
    )" value="' . $_language->module[ 'edit' ] . '" class="btn btn-danger">
<input type="button" onclick="MM_confirm(
    \'' . $_language->module[ 'really_delete_clanwar' ] . '?\',
    \'clanwars.php?action=delete&amp;cwID=' . $ds[ 'cwID' ] . '\'
    )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger">';
} else {
    $adminaction = '';
}

$report = cleartext($ds[ 'report' ]);
$report = toggle($report, $ds[ 'cwID' ]);
if ($report == "") {
    $report = "n/a";
}

$squad = '<a href="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=' . $ds[ 'squad' ] . '"><b>' .
getsquadname($ds[ 'squad' ]) . '</b></a>';

$opptag = getinput($ds[ 'opptag' ]);
$oppteam = getinput($ds[ 'oppteam' ]);
$server = getinput($ds[ 'server' ]);
$hltv = getinput($ds[ 'hltv' ]);

if (!empty($ds[ 'hometeam' ])) {
    $array = unserialize($ds[ 'hometeam' ]);
    $n = 1;
    foreach ($array as $id) {
        if (!empty($id)) {
            if ($n > 1) {
                $hometeam .= ', <a href="index.php?site=profile&amp;id=' . $id . '">' . getnickname($id) . '</a>';
            } else {
                $hometeam .= '<a href="index.php?site=profile&amp;id=' . $id . '">' . getnickname($id) . '</a>';
            }
            $n++;
        }
    }
}
$screenshots = '';
if (!empty($ds[ 'screens' ])) {
    $screens = explode("|", $ds[ 'screens' ]);
}
if (is_array($screens)) {
    $n = 1;
    foreach ($screens as $screen) {
        if (!empty($screen)) {
            $screenshots .= '<a href="images/clanwar-screens/' . $screen .
            '" target="_blank"><img src="images/clanwar-screens/' . $screen .
            '" width="150" height="100" style="padding-top:3px; padding-right:3px;" alt=""></a>';
            if ($nbr == 2) {
                $nbr = 1;
                $screenshots .= '<br>';
            } else {
                $nbr = 2;
            }
            $n++;
        }
    }
}

if (!(mb_strlen(trim($screenshots)))) {
    $screenshots = $_language->module[ 'no_screenshots' ];
}

$bg1 = BG_1;
$bg2 = BG_2;
$bg3 = BG_3;
$bg4 = BG_4;

$linkpage = cleartext($ds[ 'linkpage' ]);
$linkpage = str_replace('http://', '', $ds[ 'linkpage' ]);
if ($linkpage == "") {
    $linkpage = "#";
}

    // -- v1.0, extended results -- //

$scoreHome = unserialize($ds[ 'homescore' ]);
$scoreOpp = unserialize($ds[ 'oppscore' ]);
$homescr = array_sum($scoreHome);
$oppscr = array_sum($scoreOpp);

if ($homescr > $oppscr) {
    $result_map = '[color=' . $wincolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
    $result_map2 = 'won';
} elseif ($homescr < $oppscr) {
    $result_map = '[color=' . $loosecolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
    $result_map2 = 'lost';
} else {
    $result_map = '[color=' . $drawcolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
    $result_map2 = 'draw';
}

if (is_array($theMaps)) {
    $d = 0;
    foreach ($theMaps as $map) {
        $score = '';
        if (($d + 1) % 2) {
            $bgone = BG_1;
            $bgtwo = BG_2;
        } else {
            $bgone = BG_3;
            $bgtwo = BG_4;
        }
        if ($scoreHome[ $d ] > $scoreOpp[ $d ]) {
            $score_1 = '<font color="' . $wincolor . '"><strong>' . $scoreHome[ $d ] . '</strong></font>';
            $score_2 = '<font color="' . $wincolor . '"><strong>' . $scoreOpp[ $d ] . '</strong></font>';
        } elseif ($scoreHome[ $d ] < $scoreOpp[ $d ]) {
            $score_1 = '<font color="' . $loosecolor . '"><strong>' . $scoreHome[ $d ] . '</strong></font>';
            $score_2 = '<font color="' . $loosecolor . '"><strong>' . $scoreOpp[ $d ] . '</strong></font>';
        } else {
            $score_1 = '<font color="' . $drawcolor . '"><strong>' . $scoreHome[ $d ] . '</strong></font>';
            $score_2 = '<font color="' . $drawcolor . '"><strong>' . $scoreOpp[ $d ] . '</strong></font>';
        }

        eval ("\$clanwars_details_results = \"" . gettemplate("clanwars_details_results") . "\";");
        $extendedresults .= $clanwars_details_results;
        unset($score);
        $d++;
    }
} else {
    $extendedresults = '';
}

    // -- clanwar output -- //

eval ("\$clanwars_details = \"" . gettemplate("clanwars_details") . "\";");
echo $clanwars_details;

$comments_allowed = $ds[ 'comments' ];
$parentID = $cwID;
$type = "cw";
$referer = "index.php?site=clanwars_details&amp;cwID=$cwID";

include("comments.php");
