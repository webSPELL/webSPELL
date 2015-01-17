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

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = "";
}
if ($action == "new") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('clanwars');

    if (!isanyadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    if (isset($_GET[ 'upID' ])) {
        $upID = $_GET[ 'upID' ];
    }

    if (isclanwaradmin($userID)) {
        $squads = getgamesquads();
        $jumpsquads = str_replace(
            'value="',
            'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=',
            $squads
        );

        $games = "";
        $hometeam = "";

        $gamesa = safe_query("SELECT * FROM " . PREFIX . "games ORDER BY name");
        while ($ds = mysqli_fetch_array($gamesa)) {
            $games .= '<option value="' . $ds[ 'tag' ] . '">' . $ds[ 'name' ] . '</option>';
        }

        $gamesquads = safe_query("SELECT * FROM " . PREFIX . "squads WHERE gamesquad='1' ORDER BY sort");
        while ($ds = mysqli_fetch_array($gamesquads)) {
            $hometeam .= '<option value="0">' . $ds[ 'name' ] . '</option>';
            $squadmembers =
                safe_query("SELECT * FROM " . PREFIX . "squads_members WHERE squadID='$ds[squadID]' ORDER BY sort");
            while ($dm = mysqli_fetch_array($squadmembers)) {
                $hometeam .= '<option value="' . $dm[ 'userID' ] . '">&nbsp; - ' . getnickname($dm[ 'userID' ]) .
                    '</option>';
            }
            $hometeam .= '<option value="0" disabled="disabled">-----</option>';
        }

        $date = "";

        $countries = getcountries();

        $leaguehp = "http://";
        $opphp = "http://";
        $linkpage = "http://";
        $server = "";
        $league = "";
        $opponent = "";
        $opptag = "";
        if (isset($upID)) {
            $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "upcoming` WHERE `upID` = '" . (int)$upID."'");
            $ds = mysqli_fetch_array($ergebnis);
            $league = $ds[ 'league' ];
            if ($ds[ 'leaguehp' ] != $leaguehp) {
                $leaguehp = $ds[ 'leaguehp' ];
            }
            $opponent = $ds[ 'opponent' ];
            $opptag = $ds[ 'opptag' ];
            if ($ds[ 'opphp' ] != $opphp) {
                $opphp = $ds[ 'opphp' ];
            }
            $countries = getcountries();
            $countries = str_replace(" selected=\"selected\"", "", $countries);
            $countries = str_replace(
                'value="' . $ds[ 'oppcountry' ] . '"',
                'value="' . $ds[ 'oppcountry' ] . '" selected="selected"',
                $countries
            );

            $squads = str_replace(" selected=\"selected\"", "", $squads);
            $squads = str_replace(
                'value="' . $ds[ 'squad' ] . '"',
                'value="' . $ds[ 'squad' ] . '" selected="selected"',
                $squads
            );
            $server = $ds[ 'server' ];

            $date = date("Y-m-d", $ds[ 'date' ]);
        }

        $componentsCss = generateComponents($components['css'], 'css');
        $componentsJs = generateComponents($components['js'], 'js');

        $data_array = array();
        $data_array['$rewriteBase'] = $rewriteBase;
        $data_array['$componentsCss'] = $componentsCss;
        $data_array['$date'] = $date;
        $data_array['$squads'] = $squads;
        $data_array['$games'] = $games;
        $data_array['$league'] = $league;
        $data_array['$leaguehp'] = $leaguehp;
        $data_array['$linkpage'] = $linkpage;
        $data_array['$opponent'] = $opponent;
        $data_array['$opptag'] = $opptag;
        $data_array['$opphp'] = $opphp;
        $data_array['$countries'] = $countries;
        $data_array['$server'] = $server;
        $data_array['$hometeam'] = $hometeam;
        $data_array['$componentsJs'] = $componentsJs;
        $clanwar_new = $GLOBALS["_template"]->replaceTemplate("clanwar_new", $data_array);
        echo $clanwar_new;
    } else {
        redirect('index.php?site=clanwars', 'no access!');
    }
} elseif ($action == "save") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('clanwars');

    if (!isanyadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    $date = strtotime($_POST['date']);
    if (isset($_POST[ 'hometeam' ])) {
        $hometeam = $_POST[ 'hometeam' ];
    } else {
        $hometeam = array();
    }
    if (isset($_POST[ 'squad' ])) {
        $squad = $_POST[ 'squad' ];
    } else {
        $squad = '';
    }
    $game = $_POST[ 'game' ];
    $league = $_POST[ 'league' ];
    $leaguehp = $_POST[ 'leaguehp' ];
    $opponent = $_POST[ 'opponent' ];
    $opptag = $_POST[ 'opptag' ];
    $oppcountry = $_POST[ 'oppcountry' ];
    $opphp = $_POST[ 'opphp' ];
    $oppteam = $_POST[ 'oppteam' ];
    $server = $_POST[ 'server' ];
    $hltv = $_POST[ 'hltv' ];
    $report = $_POST[ 'message' ];
    $comments = $_POST[ 'comments' ];
    $linkpage = $_POST[ 'linkpage' ];
    if (isset($_POST[ 'news' ])) {
        $news = $_POST[ 'news' ];
    }

    // v1.0 -- EXTENDED CLANWAR RESULTS
    if (isset($_POST[ 'map_name' ])) {
        $maplist = $_POST[ 'map_name' ];
    }
    if (isset($_POST[ 'map_result_home' ])) {
        $homescr = $_POST[ 'map_result_home' ];
    }
    if (isset($_POST[ 'map_result_opp' ])) {
        $oppscr = $_POST[ 'map_result_opp' ];
    }

    $maps = array();
    if (!empty($maplist)) {
        if (is_array($maplist)) {
            foreach ($maplist as $map) {
                $maps[ ] = stripslashes($map);
            }
        }
    }
    $backup_theMaps = serialize($maps);
    $theMaps = $_database->escape_string($backup_theMaps);

    $scores = array();
    if (!empty($homescr)) {
        if (is_array($homescr)) {
            foreach ($homescr as $result) {
                $scores[ ] = $result;
            }
        }
    }
    $theHomeScore = serialize($scores);

    $results = array();
    if (!empty($oppscr)) {
        if (is_array($oppscr)) {
            foreach ($oppscr as $result) {
                $results[ ] = $result;
            }
        }
    }
    $theOppScore = serialize($results);

    $team = array();
    if (is_array($hometeam)) {
        foreach ($hometeam as $player) {
            if (!in_array($player, $team)) {
                $team[ ] = $player;
            }
        }
    }
    $home_string = serialize($team);

    safe_query(
        "INSERT INTO
            `" . PREFIX . "clanwars` (
                date,
                squad,
                game,
                league,
                leaguehp,
                opponent,
                opptag,
                oppcountry,
                opphp,
                maps,
                hometeam,
                oppteam,
                server,
                hltv,
                homescore,
                oppscore,
                report,
                comments,
                linkpage
            )
            VALUES(
                '". $date . "',
                '". $squad . "',
                '". $game . "',
                '" . $league . "',
                '". $leaguehp . "',
                '" . $opponent . "',
                '" . $opptag . "',
                '". $oppcountry . "',
                '". $opphp . "',
                '" . $theMaps . "',
                '". $home_string . "',
                '". $oppteam . "',
                '". $server . "',
                '". $hltv . "',
                '". $theHomeScore . "',
                '". $theOppScore . "',
                '" . $report . "',
                '". $comments . "',
                '". $linkpage . "'
            )"
    );

    $cwID = mysqli_insert_id($_database);
    $date = getformatdate($date);

    // INSERT CW-NEWS
    if (isset($news)) {
        $_language->readModule('news', true);
        $_language->readModule('bbcode', true);

        safe_query(
            "INSERT INTO
                `" . PREFIX . "news` (
                    `date`,
                    `poster`,
                    `saved`,
                    `cwID`
                )
                VALUES (
                    '" . time() . "',
                    '" . $userID . "',
                    '0',
                    '" . $cwID . "'
                )"
        );
        $newsID = mysqli_insert_id($_database);

        $rubrics = '';
        $newsrubrics = safe_query("SELECT rubricID, rubric FROM " . PREFIX . "news_rubrics ORDER BY rubric");
        while ($dr = mysqli_fetch_array($newsrubrics)) {
            $rubrics .= '<option value="' . $dr[ 'rubricID' ] . '">' . $dr[ 'rubric' ] . '</option>';
        }

        $count_langs = 0;
        $lang = safe_query("SELECT lang, language FROM " . PREFIX . "news_languages ORDER BY language");
        $langs = '';
        while ($dl = mysqli_fetch_array($lang)) {
            $langs .=
                "news_languages[" . $count_langs . "] = new Array();\nnews_languages[" . $count_langs . "][0] = '" .
                $dl[ 'lang' ] . "';\nnews_languages[" . $count_langs . "][1] = '" . $dl[ 'language' ] . "';\n";
            $count_langs++;
        }

        $squad = getsquadname($squad);
        $link1 = $opptag;
        $url1 = $opphp;
        $link2 = $league;
        $url2 = $leaguehp;
        $url3 = "http://";
        $url4 = "http://";
        $link3 = "";
        $link4 = "";
        $window1_new = 'checked="checked"';
        $window1_self = '';
        $window2_new = 'checked="checked"';
        $window2_self = '';
        $window3_new = 'checked="checked"';
        $window3_self = '';
        $window4_new = 'checked="checked"';
        $window4_self = '';

        // v1.0 -- PREPARE CW-NEWS OUTPUT
        $maps = unserialize($backup_theMaps);
        $scoreHome = unserialize($theHomeScore);
        $scoreOpp = unserialize($theOppScore);
        $homescr = array_sum($scoreHome);
        $oppscr = array_sum($scoreOpp);

        if ($homescr > $oppscr) {
            $results = '[color=' . $wincolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
            $result2 = 'won';
        } elseif ($homescr < $oppscr) {
            $results = '[color=' . $loosecolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
            $result2 = 'lost';
        } else {
            $results = '[color=' . $drawcolor . '][b]' . $homescr . ':' . $oppscr . '[/b][/color]';
            $result2 = 'draw';
        }

        $headline1 = 'War ' . stripslashes($squad) . ' vs. ' . stripslashes($opponent) . ' ' . $result2;
        if ($url1 != 'http://' && !(empty($url1))) {
            $opponent = '[url=' . $opphp . '][b]' . $opptag . ' / ' . $opponent . '[/b][/url]';
        } else {
            $opponent = '[b]' . $opptag . ' / ' . $opponent . '[/b]';
        }
        if ($url2 != 'http://' && !(empty($url2))) {
            $league = '[url=' . $leaguehp . ']' . $league . '[/url]';
        }
        // v1.0 -- CREATE CW-NEWS EXTENDED RESULTS
        if (is_array($maps)) {
            $d = 0;
            $results_ext = '[TOGGLE=Results (extended)]';
            foreach ($maps as $maptmp) {
                $map = stripslashes($maptmp);
                $score = "";
                if ($scoreHome[ $d ] > $scoreOpp[ $d ]) {
                    $score .=
                        '<td>[color=' . $wincolor . '][b]' . $scoreHome[ $d ] . '[/b][/color] : [color=' . $loosecolor .
                        '][b]' . $scoreOpp[ $d ] . '[/b][/color]</td>';
                } elseif ($scoreHome[ $d ] < $scoreOpp[ $d ]) {
                    $score .=
                        '<td>[color=' . $loosecolor . '][b]' . $scoreHome[ $d ] . '[/b][/color] : [color=' . $wincolor .
                        '][b]' . $scoreOpp[ $d ] . '[/b][/color]</td>';
                } else {
                    $score .=
                        '<td>[color=' . $drawcolor . '][b]' . $scoreHome[ $d ] . '[/b][/color] : [color=' . $drawcolor .
                        '][b]' . $scoreOpp[ $d ] . '[/b][/color]</td>';
                }
                $d++;
                $data_array = array();
                $data_array['$map'] = $map;
                $data_array['$score'] = $score;
                $news_cw_results = $GLOBALS["_template"]->replaceTemplate("news_cw_results", $data_array);
                $results_ext .= $news_cw_results;
                unset($score);
            }
            $results_ext .= '[/TOGGLE]';
        } else {
            $results_ext = "";
        }
        if (!empty($report)) {
            $more1 = '[TOGGLE=Report]' . getforminput($report) . '[/TOGGLE]';
        } else {
            $more1 = "";
        }
        $home = "";
        if (is_array($team)) {
            $n = 1;
            foreach ($team as $id) {
                if (!empty($id)) {
                    if ($n > 1) {
                        $home .= ', <a href="index.php?site=profile&amp;id=' . $id . '">' . getnickname($id) . '</a>';
                    } else {
                        $home = '<a href="index.php?site=profile&amp;id=' . $id . '">' . getnickname($id) . '</a>';
                    }
                    $n++;
                }
            }
        }

        $_languagepagedefault = new \webspell\Language();
        $_languagepagedefault->setLanguage($rss_default_language);
        $_languagepagedefault->readModule('clanwars');
        $message =
            $_language->module[ 'clanwar_against' ] . ' [flag]' . $oppcountry . '[/flag] ' . stripslashes($opponent) .
            ' ' . $_language->module[ 'on' ] . ' ' . $date . '
' . $_language->module[ 'league' ] . ': ' . stripslashes($league) . '
' . $_language->module[ 'result' ] . ': ' . $results . '
' . $results_ext . '
' . stripslashes($myclantag) . ' ' . $_language->module[ 'team' ] . ': ' . stripslashes($home) . '
' . stripslashes($opptag) . ' ' . $_language->module[ 'team' ] . ': ' . stripslashes($oppteam) . '

' . $more1 . '
<a href="index.php?site=clanwars_details&amp;cwID=' . $cwID . '">' .
            $_languagepagedefault->module[ 'clanwar_details' ] . '</a>';
        $i = 0;
        $message_vars = "message[" . $i . "] = '" . js_replace($message) . "';\n";
        $headline_vars = "headline[" . $i . "] = '" . js_replace(htmlspecialchars($headline1)) . "';\n";
        $langs_vars = "langs[" . $i . "] = '$default_language';\n";
        $langcount = 1;
        $selects = "";
        for ($i = 1; $i <= $count_langs; $i++) {
            if ($i == $langcount) {
                $selects .= '<option value="' . $i . '" selected="selected">' . $i . '</option>';
            } else {
                $selects .= '<option value="' . $i . '">' . $i . '</option>';
            }
        }
        $intern =
            '<option value="0" selected="selected">' . $_language->module[ 'no' ] . '</option><option value="1">' .
            $_language->module[ 'yes' ] . '</option>';
        $topnews =
            '<option value="0" selected="selected">' . $_language->module[ 'no' ] . '</option><option value="1">' .
            $_language->module[ 'yes' ] . '</option>';

        $rubrics = '';
        $newsrubrics = safe_query("SELECT rubricID, rubric FROM " . PREFIX . "news_rubrics ORDER BY rubric");
        while ($dr = mysqli_fetch_array($newsrubrics)) {
            $rubrics .= '<option value="' . $dr[ 'rubricID' ] . '">' . $dr[ 'rubric' ] . '</option>';
        }
        $bg1 = BG_1;

        $comments = '<option value="0">' . $_language->module[ 'no_comments' ] . '</option><option value="1">' .
            $_language->module[ 'user_comments' ] . '</option><option value="2" selected="selected">' .
            $_language->module[ 'visitor_comments' ] . '</option>';

        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $addflags = $GLOBALS["_template"]->replaceTemplate("flags", array());
        $_language->readModule('news');
        $data_array = array();
        $data_array['$rewriteBase'] = $rewriteBase;
        $data_array['$componentsCss'] = $componentsCss;
        $data_array['$addbbcode'] = $addbbcode;
        $data_array['$addflags'] = $addflags;
        $data_array['$rubrics'] = $rubrics;
        $data_array['$newsID'] = $newsID;
        $data_array['$topnews'] = $topnews;
        $data_array['$intern'] = $intern;
        $data_array['$tags'] = $tags;
        $data_array['$langcount'] = $langcount;
        $data_array['$link1'] = $link1;
        $data_array['$url1'] = $url1;
        $data_array['$window1_new'] = $window1_new;
        $data_array['$window1_self'] = $window1_self;
        $data_array['$link2'] = $link2;
        $data_array['$url2'] = $url2;
        $data_array['$window2_new'] = $window2_new;
        $data_array['$window2_self'] = $window2_self;
        $data_array['$link3'] = $link3;
        $data_array['$url3'] = $url3;
        $data_array['$window3_new'] = $window3_new;
        $data_array['$window3_self'] = $window3_self;
        $data_array['$link4'] = $link4;
        $data_array['$url4'] = $url4;
        $data_array['$window4_new'] = $window4_new;
        $data_array['$window4_self'] = $window4_self;
        $data_array['$userID'] = $userID;
        $data_array['$comments'] = $comments;
        $data_array['$componentsJs'] = $componentsJs;
        $news_post = $GLOBALS["_template"]->replaceTemplate("news_post", $data_array);
        echo $news_post;
    } else {
        echo '<script src="js/bbcode.js"></script>
    <link href="_stylesheet.css" rel="stylesheet" type="text/css">
    <p class="text-center"><br><br><br><br>
    <strong>' . $_language->module[ 'clanwar_saved' ] . '.</strong><br><br>
    <input type="button" onclick="window.open(\'upload.php?cwID=' . $cwID .
        '\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="' .
            $_language->module[ 'upload_screenshot' ] . '">
    <input type="button" onclick="javascript:self.close()" value="' . $_language->module[ 'close_window' ] . '"></p>';
    }
} elseif ($action == "edit") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('clanwars');
    if (!isanyadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    $cwID = $_GET[ 'cwID' ];

    if (isclanwaradmin($userID)) {
        $squads = getgamesquads();
        $jumpsquads = str_replace(
            'value="',
            'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=',
            $squads
        );

        $games = "";
        $maps = "";
        $hometeam = "";

        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT
                    *
                FROM
                    `" . PREFIX . "clanwars`
                WHERE
                    `cwID` = '" . (int)$cwID."'"
            )
        );

        $date = date("Y-m-d", $ds[ 'date' ]);

        $gamesa = safe_query("SELECT tag, name FROM `" . PREFIX . "games` ORDER BY `name`");
        while ($dv = mysqli_fetch_array($gamesa)) {
            $games .= '<option value="' . $dv[ 'tag' ] . '">' . $dv[ 'name' ] . '</option>';
        }

        $games =
            str_replace('value="' . $ds[ 'game' ] . '"', 'value="' . $ds[ 'game' ] . '" selected="selected"', $games);
        $squads = getgamesquads();
        $squads = str_replace(
            'value="' . $ds[ 'squad' ] . '"',
            'value="' . $ds[ 'squad' ] . '" selected="selected"',
            $squads
        );
        $league = htmlspecialchars($ds[ 'league' ]);
        $leaguehp = htmlspecialchars($ds[ 'leaguehp' ]);
        $opponent = htmlspecialchars($ds[ 'opponent' ]);
        $opptag = htmlspecialchars($ds[ 'opptag' ]);
        $countries = getcountries();
        $countries = str_replace(
            'value="' . $ds[ 'oppcountry' ] . '"',
            'value="' . $ds[ 'oppcountry' ] . '" selected="selected"',
            $countries
        );
        $opphp = htmlspecialchars($ds[ 'opphp' ]);
        $oppteam = htmlspecialchars($ds[ 'oppteam' ]);
        $server = htmlspecialchars($ds[ 'server' ]);
        $hltv = htmlspecialchars($ds[ 'hltv' ]);
        $linkpage = htmlspecialchars($ds[ 'linkpage' ]);
        $report = htmlspecialchars($ds[ 'report' ]);
        $linkpage = htmlspecialchars($ds[ 'linkpage' ]);

        // map-output, v1.0
        $map = unserialize($ds[ 'maps' ]);
        $theHomeScore = unserialize($ds[ 'homescore' ]);
        $theOppScore = unserialize($ds[ 'oppscore' ]);
        $i = 0;
        $counter = count($map);
        for ($i = 0; $i < $counter; $i++) {
            $maps .= '<tr>
            <td width="15%">
                <input type="hidden" name="map_id[]" value="' . $i . '">map #' . ($i + 1) . '
            </td>
            <td width="25%">
                <input type="text" name="map_name[]" value="' . getinput($map[ $i ]) . '" size="35">
            </td>
            <td width="20%">
                <input type="text" name="map_result_home[]" value="' . $theHomeScore[ $i ] . '" size="3">
            </td>
            <td width="20%">
                <input type="text" name="map_result_opp[]" value="' . $theOppScore[ $i ] . '" size="3">
            </td>
            <td width="25%">
                <input type="checkbox" name="delete[' . $i . ']" value="1"> ' . $_language->module[ 'delete' ] . '
            </td></tr>';
        }

        $gamesquads = safe_query("SELECT * FROM `" . PREFIX . "squads` WHERE `gamesquad` = '1' ORDER BY `sort`");
        while ($dq = mysqli_fetch_array($gamesquads)) {
            $hometeam .= '<option value="0">' . $dq[ 'name' ] . '</option>';
            $squadmembers = safe_query(
                "SELECT
                    *
                FROM
                    `" . PREFIX . "squads_members`
                WHERE
                    `squadID` = '$dq[squadID]'
                ORDER BY
                    `sort`"
            );
            while ($dm = mysqli_fetch_array($squadmembers)) {
                $hometeam .= '<option value="' . $dm[ 'userID' ] . '">&nbsp; - ' . getnickname($dm[ 'userID' ]) .
                    '</option>';
            }
            $hometeam .= '<option value="0">&nbsp;</option>';
        }

        if (!empty($ds[ 'hometeam' ])) {
            $array = unserialize($ds[ 'hometeam' ]);
            foreach ($array as $id) {
                if (!empty($id)) {
                    $hometeam =
                        str_replace('value="' . $id . '"', 'value="' . $id . '" selected="selected"', $hometeam);
                }
            }
        }

        $comments = '<option value="0">' . $_language->module[ 'disable_comments' ] . '</option><option value="1">' .
            $_language->module[ 'user_comments' ] . '</option><option value="2">' .
            $_language->module[ 'visitor_comments' ] . '</option>';
        $comments = str_replace(
            'value="' . $ds[ 'comments' ] . '"',
            'value="' . $ds[ 'comments' ] . '" selected="selected"',
            $comments
        );

        $componentsCss = '';
        foreach ($components['css'] as $component) {
            $componentsCss .= '<link href="' . $component . '" rel="stylesheet">';
        }

        $componentsJs = '';
        foreach ($components['js'] as $component) {
            $componentsJs .= '<script src="' . $component . '"></script>';
        }

        $data_array = array();
        $data_array['$rewriteBase'] = $rewriteBase;
        $data_array['$componentsCss'] = $componentsCss;
        $data_array['$date'] = $date;
        $data_array['$squads'] = $squads;
        $data_array['$games'] = $games;
        $data_array['$league'] = $league;
        $data_array['$leaguehp'] = $leaguehp;
        $data_array['$linkpage'] = $linkpage;
        $data_array['$opponent'] = $opponent;
        $data_array['$opptag'] = $opptag;
        $data_array['$opphp'] = $opphp;
        $data_array['$countries'] = $countries;
        $data_array['$server'] = $server;
        $data_array['$hltv'] = $hltv;
        $data_array['$hometeam'] = $hometeam;
        $data_array['$oppteam'] = $oppteam;
        $data_array['$report'] = $report;
        $data_array['$maps'] = $maps;
        $data_array['$cwID'] = $cwID;
        $data_array['$comments'] = $comments;
        $data_array['$componentsJs'] = $componentsJs;
        $clanwar_edit = $GLOBALS["_template"]->replaceTemplate("clanwar_edit", $data_array);
        echo $clanwar_edit;
    } else {
        redirect('index.php?site=clanwars', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "saveedit") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('clanwars');

    if (!isanyadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $cwID = $_POST[ 'cwID' ];
    $date = strtotime($_POST['date']);
    if (isset($_POST[ 'hometeam' ])) {
        $hometeam = $_POST[ 'hometeam' ];
    } else {
        $hometeam = array();
    }
    $squad = $_POST[ 'squad' ];
    $game = $_POST[ 'game' ];
    $league = $_POST[ 'league' ];
    $leaguehp = $_POST[ 'leaguehp' ];
    $opponent = $_POST[ 'opponent' ];
    $opptag = $_POST[ 'opptag' ];
    $oppcountry = $_POST[ 'oppcountry' ];
    $opphp = $_POST[ 'opphp' ];
    $oppteam = $_POST[ 'oppteam' ];
    $server = $_POST[ 'server' ];
    $hltv = $_POST[ 'hltv' ];
    $report = $_POST[ 'message' ];
    $comments = $_POST[ 'comments' ];
    $linkpage = $_POST[ 'linkpage' ];
    $maplist = $_POST[ 'map_name' ];
    $homescr = $_POST[ 'map_result_home' ];
    $oppscr = $_POST[ 'map_result_opp' ];
    if (isset($_POST[ 'delete' ])) {
        $delete = $_POST[ 'delete' ];
    } else {
        $delete = array();
    }

    // v1.0 -- MAP-REMOVAL
    $theMaps = array();
    $theHomeScore = array();
    $theOppScore = array();

    if (is_array($maplist)) {
        foreach ($maplist as $key => $map) {
            if (!isset($delete[ $key ])) {
                $theMaps[ ] = stripslashes($map);
                $theHomeScore[ ] = $homescr[ $key ];
                $theOppScore[ ] = $oppscr[ $key ];
            }
        }
    }
    $theMaps = $_database->escape_string(serialize($theMaps));

    $theHomeScore = serialize($theHomeScore);
    $theOppScore = serialize($theOppScore);

    echo '<script src="js/bbcode.js"></script><link href="_stylesheet.css" rel="stylesheet" type="text/css">';

    $team = array();
    if (is_array($hometeam)) {
        foreach ($hometeam as $player) {
            if (!in_array($player, $team)) {
                $team[ ] = $player;
            }
        }
    }
    $home_string = serialize($team);

    safe_query(
        "UPDATE
            `" . PREFIX . "clanwars`
        SET
            `date` = '" . $date . "',
            `squad` = '" . $squad . "',
            `game` = '" . $game . "',
            `league` = '" . $league . "',
            `leaguehp` = '" . $leaguehp . "',
            `opponent` = '" . $opponent . "',
            `opptag` = '" . $opptag . "',
            `oppcountry` = '" . $oppcountry . "',
            `opphp` = '" . $opphp . "',
            `maps` = '" . $theMaps . "',
            `hometeam` = '" . $home_string . "',
            `oppteam` = '" . $oppteam . "',
            `server` = '" . $server . "',
            `hltv` = '" . $hltv . "',
            `homescore` = '" . $theHomeScore . "',
            `oppscore` = '" . $theOppScore . "',
            `report` = '" . $report . "',
            `comments` = '" . $comments . "',
            `linkpage` = '" . $linkpage . "'
        WHERE
            `cwID` = '" . (int)$cwID ."'"
    );

    echo '<p class="text-center"><br><br><br><br>
    <strong>' . $_language->module[ 'clanwar_updated' ] . '</strong><br><br>
    <input type="button" onclick="window.open(\'upload.php?cwID=' . $cwID .
        '\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="' .
        $_language->module[ 'upload_screenshot' ] . '">
    <input type="button" onclick="javascript:self.close()" value="' . $_language->module[ 'close_window' ] . '"></p>';
} elseif ($action == "delete") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('clanwars');

    if (!isanyadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    if (isset($_POST[ 'cwID' ])) {
        $cwID = $_POST[ 'cwID' ];
    }
    if (!isset($cwID)) {
        $cwID = $_GET[ 'cwID' ];
    }
    $ergebnis = safe_query("SELECT `screens` FROM `" . PREFIX . "clanwars` WHERE `cwID` = '$cwID'");
    $ds = mysqli_fetch_array($ergebnis);
    $screens = explode("|", $ds[ 'screens' ]);
    $filepath = "./images/clanwar-screens/";
    if (is_array($screens)) {
        foreach ($screens as $screen) {
            if (!empty($screen)) {
                if (file_exists($filepath . $screen)) {
                    @unlink($filepath . $screen);
                }
            }
        }
    }
    safe_query("DELETE FROM `" . PREFIX . "clanwars` WHERE `cwID` = '$cwID'");
    header("Location: index.php?site=clanwars");
} elseif (isset($_POST[ 'quickactiontype' ]) == "delete") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('clanwars');

    if (!isanyadmin($userID)) {
        die('no access!');
    }
    if (isset($_POST[ 'cwID' ])) {
        $cwID = $_POST[ 'cwID' ];
        foreach ($cwID as $id) {
            $ergebnis = safe_query("SELECT `screens` FROM `" . PREFIX . "clanwars` WHERE `cwID` = '" . (int)$id."'");
            $ds = mysqli_fetch_array($ergebnis);
            $screens = explode("|", $ds[ 'screens' ]);
            $filepath = "./images/clanwar-screens/";
            if (is_array($screens)) {
                foreach ($screens as $screen) {
                    if (!empty($screen)) {
                        if (file_exists($filepath . $screen)) {
                            @unlink($filepath . $screen);
                        }
                    }
                }
            }

            safe_query("DELETE FROM `" . PREFIX . "clanwars` WHERE `cwID` = '" . (int)$id."'");
            safe_query("DELETE FROM `" . PREFIX . "comments` WHERE `parentID` = '" . (int)$id . "' AND type='cw'");
        }
    }
    header("Location: index.php?site=clanwars");
} elseif ($action == "stats") {
    $title_clanwars = $GLOBALS["_template"]->replaceTemplate("title_clanwars", array());
    echo $title_clanwars;

    echo '<a href="index.php?site=clanwars" class="btn btn-primary">' . $_language->module[ 'show_clanwars' ] . '</a>';

    echo '<h2>' . $_language->module[ 'clan_stats' ] . '</h2>';

    $totalHomeScore = "";
    $totalOppScore = "";
    $allWon = "";
    $allLose = "";
    $allDraw = "";
    $totaldrawall = "";
    $totalwonall = "";
    $totalloseall = "";

    // TOTAL

    $dp = safe_query("SELECT * FROM `" . PREFIX . "clanwars`");
    // clanwars gesamt
    $totaltotal = mysqli_num_rows($dp);

    while ($cwdata = mysqli_fetch_array($dp)) {
        // total home points
        $totalhomeqry =
            safe_query("SELECT `homescore` FROM `" . PREFIX . "clanwars` WHERE `cwID = '" . (int)$cwdata['cwID']."'");
        while ($theHomeData = mysqli_fetch_array($totalhomeqry)) {
            $totalHomeScore += array_sum(unserialize($theHomeData[ 'homescore' ]));
            $theHomeScore = array_sum(unserialize($theHomeData[ 'homescore' ]));
        }
        // total opponent points
        $totaloppqry =
            safe_query("SELECT `oppscore` FROM `" . PREFIX . "clanwars` WHERE `cwID` = '" . (int)$cwdata['cwID']."'");
        while ($theOppData = mysqli_fetch_array($totaloppqry)) {
            $totalOppScore += array_sum(unserialize($theOppData[ 'oppscore' ]));
            $theOppScore = array_sum(unserialize($theOppData[ 'oppscore' ]));
        }

        //
        if ($allWon == '') {
            $allWon = 0;
        }
        if ($allLose == '') {
            $allLose = 0;
        }
        if ($allDraw == '') {
            $allDraw = 0;
        }

        //
        if ($theHomeScore > $theOppScore) {
            $totalwonall++;
        }
        if ($theHomeScore < $theOppScore) {
            $totalloseall++;
        }
        if ($theHomeScore == $theOppScore) {
            $totaldrawall++;
        }
    }
    $totalhome = $totalHomeScore;
    $totalopp = $totalOppScore;

    if (!$totalwonall) {
        $totalwonall = 0;
    }
    if (!$totalloseall) {
        $totalloseall = 0;
    }
    if (!$totaldrawall) {
        $totaldrawall = 0;
    }
    if (!$totalhome) {
        $totalhome = 0;
    }
    if (!$totalopp) {
        $totalopp = 0;
    }

    $totalwonperc = percent($totalwonall, $totaltotal, 2);
    if ($totalwonperc) {
        $totalwon =
            $totalwonperc . '%<br><img src="images/icons/won.gif" width="30" height="' . round($totalwonperc, 0) .
            '" border="1" alt="' . $_language->module[ 'won' ] . '">';
    } else {
        $totalwon = 0;
    }

    $totalloseperc = percent($totalloseall, $totaltotal, 2);
    if ($totalloseperc) {
        $totallost =
            $totalloseperc . '%<br><img src="images/icons/lost.gif" width="30" height="' . round($totalloseperc, 0) .
            '" border="1" alt="' . $_language->module[ 'lost' ] . '">';
    } else {
        $totallost = 0;
    }

    $totaldrawperc = percent($totaldrawall, $totaltotal, 2);
    if ($totaldrawperc) {
        $totaldraw =
            $totaldrawperc . '%<br><img src="images/icons/draw.gif" width="30" height="' . round($totaldrawperc, 0) .
            '" border="1" alt="' . $_language->module[ 'draw' ] . '">';
    } else {
        $totaldraw = 0;
    }

    $squad = $_language->module[ 'clan' ];

    $data_array = array();
    $data_array['$totaltotal'] = $totaltotal;
    $data_array['$totalwonall'] = $totalwonall;
    $data_array['$totalloseall'] = $totalloseall;
    $data_array['$totaldrawall'] = $totaldrawall;
    $data_array['$totalhome'] = $totalhome;
    $data_array['$totalopp'] = $totalopp;
    $data_array['$totalwon'] = $totalwon;
    $data_array['$totallost'] = $totallost;
    $data_array['$totaldraw'] = $totaldraw;
    $clanwars_stats_total = $GLOBALS["_template"]->replaceTemplate("clanwars_stats_total", $data_array);
    echo $clanwars_stats_total;

    // SQUADS

    $squads = safe_query("SELECT * FROM `" . PREFIX . "squads` WHERE `gamesquad` = '1' ORDER BY `sort`");
    if (mysqli_num_rows($squads)) {
        while ($squaddata = mysqli_fetch_array($squads)) {
            $squad = getsquadname($squaddata[ 'squadID' ]);

            echo '<h2>' . $squad . ' - ' . $_language->module[ 'stats' ] . '</h2>';

            $totalHomeScoreSQ = "";
            $totalOppScoreSQ = "";
            $drawall = "";
            $wonall = "";
            $loseall = "";

            // SQUAD STATISTICS

            $squadcws =
                safe_query(
                    "SELECT
                        *
                    FROM
                        `" . PREFIX . "clanwars`
                    WHERE
                        `squad` = '" . (int)$squaddata[ 'squadID' ] . "'"
                );
            $total = mysqli_num_rows($squadcws);
            $totalperc = percent($total, $totaltotal, 2);

            while ($squadcwdata = mysqli_fetch_array($squadcws)) {
                // SQUAD CLANWAR STATISTICS

                // total squad homescore
                $sqHomeScoreQry =
                    mysqli_fetch_array(
                        safe_query(
                            "SELECT
                                homescore
                            FROM
                                `" . PREFIX . "clanwars`
                            WHERE
                                `cwID` = '" . $squadcwdata[ 'cwID' ] . "' AND
                                `squad` = '" . $squaddata[ 'squadID' ]."'"
                        )
                    );
                $sqHomeScore = array_sum(unserialize($sqHomeScoreQry[ 'homescore' ]));
                $totalHomeScoreSQ += array_sum(unserialize($sqHomeScoreQry[ 'homescore' ]));
                // total squad oppscore
                $sqOppScoreQry =
                    mysqli_fetch_array(
                        safe_query(
                            "SELECT
                                oppscore
                            FROM
                                `" . PREFIX . "clanwars`
                            WHERE
                                `cwID` = '" . (int)$squadcwdata[ 'cwID' ] . "' AND
                                `squad` = '" . (int)$squaddata[ 'squadID' ]."'"
                        )
                    );
                $sqOppScore = array_sum(unserialize($sqOppScoreQry[ 'oppscore' ]));
                $totalOppScoreSQ += array_sum(unserialize($sqOppScoreQry[ 'oppscore' ]));

                //
                if ($sqHomeScore > $sqOppScore) {
                    $wonall++;
                }
                if ($sqHomeScore < $sqOppScore) {
                    $loseall++;
                }
                if ($sqHomeScore == $sqOppScore) {
                    $drawall++;
                }
                //
                unset($sqHomeScore);
                unset($sqOppScore);
            }

            // SQUAD STATISTICS - CLANWARS

            // total squad clanwars - home points
            $home = $totalHomeScoreSQ;
            if (empty($home)) {
                $home = 0;
            }
            $homeperc = percent($home, $totalhome, 2);
            // total squad clanwars - opponent points
            $opp = $totalOppScoreSQ;
            if (empty($opp)) {
                $opp = 0;
            }
            $oppperc = percent($opp, $totalopp, 2);
            // total squad clanwars won
            $wonperc = percent($wonall, $totaltotal, 2);
            if ($wonperc) {
                $totalwon = $wonperc . '%<br><img src="images/icons/won.gif" width="30" height="' . round($wonperc, 0) .
                    '" border="1" alt="' . $_language->module[ 'won' ] . '">';
            } else {
                $totalwon = '0%';
            }
            // total squad clanwars lost
            $loseperc = percent($loseall, $totaltotal, 2);
            if ($loseperc) {
                $totallost =
                    $loseperc . '%<br><img src="images/icons/lost.gif" width="30" height="' . round($loseperc, 0) .
                    '" border="1" alt="' . $_language->module[ 'lost' ] . '">';
            } else {
                $totallost = '0%';
            }
            // total squad clanwars draw
            $drawperc = percent($drawall, $totaltotal, 2);
            if ($drawperc) {
                $totaldraw =
                    $drawperc . '%<br><img src="images/icons/draw.gif" width="30" height="' . round($drawperc, 0) .
                    '" border="1" alt="' . $_language->module[ 'draw' ] . '">';
            } else {
                $totaldraw = '0%';
            }

            // fill empty vars
            if (empty($totalwon)) {
                $totalwon = 0;
            }
            if (empty($totallost)) {
                $totallost = 0;
            }
            if (empty($totaldraw)) {
                $totaldraw = 0;
            }
            if (empty($wonall)) {
                $wonall = 0;
            }
            if (empty($loseall)) {
                $loseall = 0;
            }
            if (empty($drawall)) {
                $drawall = 0;
            }

            // start output for squad details
            $data_array = array();
            $data_array['$total'] = $total;
            $data_array['$totaltotal'] = $totaltotal;
            $data_array['$totalperc'] = $totalperc;
            $data_array['$wonall'] = $wonall;
            $data_array['$totalwonall'] = $totalwonall;
            $data_array['$wonperc'] = $wonperc;
            $data_array['$loseall'] = $loseall;
            $data_array['$totalloseall'] = $totalloseall;
            $data_array['$loseperc'] = $loseperc;
            $data_array['$drawall'] = $drawall;
            $data_array['$totaldrawall'] = $totaldrawall;
            $data_array['$drawperc'] = $drawperc;
            $data_array['$home'] = $home;
            $data_array['$totalhome'] = $totalhome;
            $data_array['$homeperc'] = $homeperc;
            $data_array['$opp'] = $opp;
            $data_array['$totalopp'] = $totalopp;
            $data_array['$oppperc'] = $oppperc;
            $data_array['$totalwon'] = $totalwon;
            $data_array['$totallost'] = $totallost;
            $data_array['$totaldraw'] = $totaldraw;
            $clanwars_stats = $GLOBALS["_template"]->replaceTemplate("clanwars_stats", $data_array);
            echo $clanwars_stats;

            unset(
                $opp,
                $home,
                $totalwon,
                $totallost,
                $totaldraw,
                $totalHomeScoreSQ,
                $totalOppScoreSQ,
                $homeperc,
                $oppperc
            );

            // PLAYER STATISTICS

            $hometeam = array();
            $playerlist = "";

            // start output for squad details - players of the squad - head
            $clanwars_stats_player_head = $GLOBALS["_template"]->replaceTemplate("clanwars_stats_player_head", array());
            echo $clanwars_stats_player_head;

            // get playerlist for squad
            $squadmembers =
                safe_query(
                    "SELECT
                        *
                    FROM
                        `" . PREFIX . "squads_members`
                    WHERE
                        `squadID` = '" . (int)$squaddata[ 'squadID' ]."'"
                );
            while ($player = mysqli_fetch_array($squadmembers)) {
                $playerlist[ ] = $player[ 'userID' ];
            }

            // get roster for squad and find matches with playerlist
            $playercws =
                safe_query(
                    "SELECT
                        `hometeam`
                    FROM
                        `" . PREFIX . "clanwars`
                    WHERE
                        `squad` = '" . (int)$squaddata[ 'squadID' ]."'"
                );
            while ($roster = mysqli_fetch_array($playercws)) {
                $hometeam = array_merge($hometeam, unserialize($roster[ 'hometeam' ]));
            }

            // counts clanwars the member has taken part in
            $anz = array();
            if (!empty($hometeam)) {
                foreach ($hometeam as $id) {
                    if (!isset($anz[ $id ])) {
                        $anz[ $id ] = '';
                    }
                    if (!empty($id)) {
                        $anz[ $id ] = $anz[ $id ] + 1;
                    }
                }
            }
            // member's details and the output
            if (is_array($playerlist)) {
                $i = 1;
                foreach ($playerlist as $id) {
                    if ($i % 2) {
                        $bg1 = BG_1;
                        $bg2 = BG_2;
                    } else {
                        $bg1 = BG_3;
                        $bg2 = BG_4;
                    }

                    $country = '[flag]' . getcountry($id) . '[/flag]';
                    $country = flags($country);
                    $member = '<a href="index.php?site=profile&amp;id=' . $id . '"><strong>' . getnickname($id) .
                        '</strong></a>';
                    if (!isset($anz[ $id ])) {
                        $anz[ $id ] = '';
                    }
                    $wars = $anz[ $id ];
                    if (empty($wars)) {
                        $wars = '0';
                    }
                    $perc = percent($wars, $total, 2);
                    if ($perc) {
                        $percpic =
                            '<div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' .
                                    round($perc, 0) . '" aria-valuemin="0" aria-valuemax="100" style="width: ' .
                                    round($perc, 0) . '%;">
                                    <span class="sr-only">' . round($perc, 0) . '% Complete</span>
                                </div>
                            </div>';
                    } else {
                        $percpic =
                            '<img src="images/icons/poll_start.gif" width="1" height="5" alt="">
                            <img src="images/icons/poll_end.gif" width="1" height="5" alt=""> ' . $perc . '%';
                    }

                    $data_array = array();
                    $data_array['$country'] = $country;
                    $data_array['$member'] = $member;
                    $data_array['$wars'] = $wars;
                    $data_array['$percpic'] = $percpic;
                    $clanwars_stats_player_content = $GLOBALS["_template"]->replaceTemplate(
                        "clanwars_stats_player_content",
                        $data_array
                    );
                    echo $clanwars_stats_player_content;
                    $i++;
                }
            }
            echo '</table>';

            unset($wonall);
            unset($loseall);
            unset($drawall);
            unset($playerlist);
            unset($hometeam);
            unset($squadcwdata);
        }
    }
} elseif ($action == "showonly") {
    if (isset($_GET[ 'cwID' ])) {
        $cwID = (int)$_GET[ 'cwID' ];
    }
    if (isset($_GET[ 'id' ])) {
        if (is_numeric($_GET[ 'id' ]) || (is_gametag($_GET[ 'id' ]))) {
            $id = $_GET[ 'id' ];
        }
    }
    $only = 'squad';
    if (isset($_GET[ 'only' ])) {
        if (($_GET[ 'only' ] == "squad") || ($_GET[ 'only' ] == "game")) {
            $only = $_GET[ 'only' ];
        }
    }
    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }
    $sort = "date";
    if (isset($_GET[ 'sort' ])) {
        if (($_GET[ 'sort' ] == 'date') || ($_GET[ 'sort' ] == 'game') || ($_GET[ 'sort' ] == 'squad')
            || ($_GET[ 'sort' ] == 'oppcountry') || ($_GET[ 'sort' ] == 'league')
        ) {
            $sort = $_GET[ 'sort' ];
        }
    }

    $type = "DESC";
    if (isset($_GET[ 'type' ])) {
        if (($_GET[ 'type' ] == 'ASC') || ($_GET[ 'type' ] == 'DESC')) {
            $type = $_GET[ 'type' ];
        }
    }

    $squads = getgamesquads();

    $jumpsquads =
        str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);
    $jumpmenu =
        '<div class="input-group">
            <select name="selectgame" onchange="MMJumpMenu(\'parent\',this,0)" class="form-control">
                <option value="index.php?site=clanwars">- ' . $_language->module[ 'show_all_squads' ] . ' -</option>' .
                    $jumpsquads .
            '</select>
            <span class="input-group-btn"><input type="button" name="Button1" value="' . $_language->module[ 'go' ] .
                '" onclick="MM_jumpMenuGo(\'selectgame\',\'parent\',0)" class="btn btn-primary">
            </span>
        </div>';

    $title_clanwars = $GLOBALS["_template"]->replaceTemplate("title_clanwars", array());
    echo $title_clanwars;

    $gesamt = mysqli_num_rows(safe_query("SELECT cwID FROM " . PREFIX . "clanwars WHERE $only='$id'"));
    $pages = 1;

    $max = $maxclanwars;
    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link =
            makepagelink(
                "index.php?site=clanwars&amp;action=showonly&amp;id=$id&amp;sort=$sort&amp;type=$type&amp;only=$only",
                $page,
                $pages
            );
    } else {
        $page_link = "";
    }

    if ($page == "1") {
        $ergebnis =
            safe_query("SELECT * FROM " . PREFIX . "clanwars WHERE $only='$id' ORDER BY $sort $type LIMIT 0,$max");
        if ($type == "DESC") {
            $n = $gesamt;
        } else {
            $n = 1;
        }
    } else {
        $start = $page * $max - $max;
        $ergebnis =
            safe_query("SELECT * FROM " . PREFIX . "clanwars WHERE $only='$id' ORDER BY $sort $type LIMIT $start,$max");
        if ($type == "DESC") {
            $n = ($gesamt) - $page * $max + $max;
        } else {
            $n = ($gesamt + 1) - $page * $max + $max;
        }
    }

    if ($type == "ASC") {
        $seiten =
            '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;page=' . $page . '&amp;sort=' .
            $sort . '&amp;type=DESC&amp;only=' . $only . '">' . $_language->module[ 'sort' ] .
            ' <span class="glyphicon glyphicon-chevron-down"></span></a> ' . $page_link . '';
    } else {
        $seiten =
            '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;page=' . $page . '&amp;sort=' .
            $sort . '&amp;type=ASC&amp;only=' . $only . '">' . $_language->module[ 'sort' ] .
            ' <span class="glyphicon glyphicon-chevron-up"></span></a>  ' . $page_link . '';
    }

    if (isclanwaradmin($userID)) {
        $admin =
            '<input type="button" onclick="window.open(
                    \'clanwars.php?action=new\',
                    \'Clanwars\',
                    \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
                )" value="' . $_language->module[ 'new_clanwar' ] . '" class="btn btn-danger">';
    } else {
        $admin = '';
    }
    $Statistics =
        '<a href="index.php?site=clanwars&amp;action=stats" class="btn btn-primary">>' .
            $_language->module[ 'stat' ] . '</a>';

    echo '<form name="jump" action=""><div class="row">
            <div class="col-xs-6">' . $admin . ' ' . $Statistics . '</div>
            <div class="col-xs-6">' . $jumpmenu . '</div>
        </div>
        </form>
        <p>' . $seiten . '</p>';

    if ($gesamt) {
        $headdate =
            '<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;only=' .
            $only . '&amp;page=' . $page . '&amp;sort=date&amp;type=' . $type . '">' . $_language->module[ 'date' ] .
            ':</a>';
        $headgame =
            '<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;only=' .
            $only . '&amp;page=' . $page . '&amp;sort=game&amp;type=' . $type . '">' . $_language->module[ 'game' ] .
            ':</a>';
        $headsquad =
            '<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;only=' .
            $only . '&amp;page=' . $page . '&amp;sort=squad&amp;type=' . $type . '">' . $_language->module[ 'squad' ] .
            ':</a>';
        $headcountry =
            '<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;only=' .
            $only . '&amp;page=' . $page . '&amp;sort=oppcountry&amp;type=' . $type . '">' .
            $_language->module[ 'country' ] . ':</a>';
        $headleague =
            '<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;only=' .
            $only . '&amp;page=' . $page . '&amp;sort=league&amp;type=' . $type . '">' .
            $_language->module[ 'league' ] . ':</a>';

        $data_array = array();
        $data_array['$headdate'] = $headdate;
        $data_array['$headgame'] = $headgame;
        $data_array['$headsquad'] = $headsquad;
        $data_array['$headcountry'] = $headcountry;
        $data_array['$headleague'] = $headleague;
        $clanwars_head = $GLOBALS["_template"]->replaceTemplate("clanwars_head", $data_array);
        echo $clanwars_head;
        $n = 1;

        while ($ds = mysqli_fetch_array($ergebnis)) {
            if ($n % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }
            $date = getformatdate($ds[ 'date' ]);
            $league = '<a href="' . $ds[ 'leaguehp' ] . '" target="_blank">' . $ds[ 'league' ] . '</a>';
            $oppcountry = "[flag]" . $ds[ 'oppcountry' ] . "[/flag]";
            $country = flags($oppcountry);
            $opponent = '<a href="' . $ds[ 'opphp' ] . '" target="_blank"><strong>' . $ds[ 'opptag' ] . '</strong></a>';
            $maps = $ds[ 'maps' ];
            $hometeam = $ds[ 'hometeam' ];
            $oppteam = $ds[ 'oppteam' ];
            $server = $ds[ 'server' ];

            $squad = '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $id . '&amp;page=' . $page .
                '&amp;sort=game&amp;type=' . $type . '&amp;only=squad"><strong>' . getsquadname($ds[ 'squad' ]) .
                '</strong></a>';
            if (file_exists('images/games/' . $ds[ 'game' ] . '.gif')) {
                $pic = $ds[ 'game' ] . '.gif';
            }
            $game =
                '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $ds[ 'game' ] . '&amp;page=' . $page .
                '&amp;sort=game&amp;type=' . $type . '&amp;only=game"><img src="images/games/' . $pic .
                '" alt=""></a>';

            $homescr = array_sum(unserialize($ds[ 'homescore' ]));
            $oppscr = array_sum(unserialize($ds[ 'oppscore' ]));

            if ($homescr > $oppscr) {
                $results = '<font color="' . $wincolor . '">' . $homescr . ':' . $oppscr . '</font>';
            } elseif ($homescr < $oppscr) {
                $results = '<font color="' . $loosecolor . '">' . $homescr . ':' . $oppscr . '</font>';
            } else {
                $results = '<font color="' . $drawcolor . '">' . $homescr . ':' . $oppscr . '</font>';
            }

            if (getanzcwcomments($ds[ 'cwID' ])) {
                $details = '<a href="index.php?site=clanwars_details&amp;cwID=' . $ds[ 'cwID' ] .
                    '"><img src="images/icons/foldericons/newhotfolder.gif" alt="' . $_language->module[ 'details' ] .
                    '"> (' . getanzcwcomments($ds[ 'cwID' ]) . ')</a>';
            } else {
                $details = '<a href="index.php?site=clanwars_details&amp;cwID=' . $ds[ 'cwID' ] .
                    '"><img src="images/icons/foldericons/folder.gif" alt="' . $_language->module[ 'details' ] .
                    '"> (' . getanzcwcomments($ds[ 'cwID' ]) . ')</a>';
            }

            $multiple = '';
            $admdel = '';
            if (isclanwaradmin($userID)) {
                $multiple = '<input class="input" type="checkbox" name="cwID[]" value="' . $ds[ 'cwID' ] . '">';
            }

            $data_array = array();
            $data_array['$multiple'] = $multiple;
            $data_array['$date'] = $date;
            $data_array['$game'] = $game;
            $data_array['$squad'] = $squad;
            $data_array['$opponent'] = $opponent;
            $data_array['$country'] = $country;
            $data_array['$league'] = $league;
            $data_array['$results'] = $results;
            $data_array['$details'] = $details;
            $clanwars_content = $GLOBALS["_template"]->replaceTemplate("clanwars_content", $data_array);
            echo $clanwars_content;
            unset($result);
            $n++;
        }

        if (isclanwaradmin($userID)) {
            $admdel =
                '<div class="row">
                    <div class="col-xs-6">
                        <input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);"> ' .
                            $_language->module[ 'select_all' ] .
                    '</div>
                    <div class="input-group col-xs-6">
                        <select name="quickactiontype" class="form-control">
                            <option value="delete">' .
                                $_language->module[ 'delete_selected' ] .
                            '</option>
                        </select>
                        <span class="input-group-btn">
                            <input type="submit" name="quickaction" value="' . $_language->module[ 'go' ] .
                                '" class="btn btn-danger">
                        </span>
                    </div>
                </div>';
        }

        $data_array = array();
        $data_array['$admdel'] = $admdel;
        $clanwars_foot = $GLOBALS["_template"]->replaceTemplate("clanwars_foot", $data_array);
        echo $clanwars_foot;
    } else {
        echo $_language->module[ 'no_entries' ];
    }
} elseif (empty($_GET[ 'action' ])) {
    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }
    $sort = "date";
    if (isset($_GET[ 'sort' ])) {
        if ($_GET[ 'sort' ] == 'date'
            || $_GET[ 'sort' ] == 'game'
            || $_GET[ 'sort' ] == 'squad'
            || $_GET[ 'sort' ] == 'oppcountry'
            || $_GET[ 'sort' ] == 'league'
        ) {
            $sort = $_GET[ 'sort' ];
        }
    }

    $type = "DESC";
    if (isset($_GET[ 'type' ])) {
        if (($_GET[ 'type' ] == 'ASC') || ($_GET[ 'type' ] == 'DESC')) {
            $type = $_GET[ 'type' ];
        }
    }
    $squads = getgamesquads();
    $jumpsquads =
        str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);
    $jumpmenu =
        '<div class="input-group">
            <select name="selectgame" onchange="MMJumpMenu(\'parent\',this,0)" class="form-control">
                <option value="index.php?site=clanwars">- ' . $_language->module[ 'show_all_squads' ] . ' -</option>'
                    . $jumpsquads .
            '</select>
            <span class="input-group-btn">
                <input type="button" name="Button1" value="' . $_language->module[ 'go' ] .
                    '" onclick="MM_jumpMenuGo(\'selectgame\',\'parent\',0)" class="btn btn-primary">
            </span>
        </div>';

    $title_clanwars = $GLOBALS["_template"]->replaceTemplate("title_clanwars", array());
    echo $title_clanwars;

    $gesamt = mysqli_num_rows(safe_query("SELECT `cwID` FROM `" . PREFIX . "clanwars`"));
    $pages = 1;
    if (!isset($page)) {
        $page = 1;
    }
    if (!isset($sort)) {
        $sort = "date";
    }
    if (!isset($type)) {
        $type = "DESC";
    }

    $max = $maxclanwars;
    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=clanwars&amp;sort=$sort&amp;type=$type", $page, $pages);
    } else {
        $page_link = "";
    }

    if ($page == "1") {
        $ergebnis = safe_query(
            "SELECT
                c.*,
                s.name AS `squadname`
            FROM
                " . PREFIX . "clanwars c
            LEFT JOIN
                " . PREFIX . "squads s ON
                s.squadID=c.squad
            ORDER BY
                c.$sort $type
            LIMIT 0,$max"
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
                c.*,
                s.name AS squadname
            FROM
                " . PREFIX . "clanwars c
            LEFT JOIN
                " . PREFIX . "squads s ON
                s.squadID=c.squad
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
        $seiten = '<a href="index.php?site=clanwars&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
            $_language->module[ 'sort' ] . ':</a> <span class="glyphicon glyphicon-chevron-down"></span> ' .
            $page_link . '<br><br>';
    } else {
        $seiten = '<a href="index.php?site=clanwars&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
            $_language->module[ 'sort' ] . ':</a> <span class="glyphicon glyphicon-chevron-up"></span> ' .
            $page_link . '<br><br>';
    }

    if (isclanwaradmin($userID)) {
        $admin =
            '<input type="button" onclick="window.open(
                \'clanwars.php?action=new\',
                \'Clanwars\',
                \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
            )" value="' . $_language->module[ 'new_clanwar' ] . '" class="btn btn-danger">';
    } else {
        $admin = '';
    }

    $statistics =
        '<a href="index.php?site=clanwars&amp;action=stats" class="btn btn-primary">' .
            $_language->module[ 'stat' ] . '</a>';

    echo '<form name="jump" action="">
            <div class="row">
                <div class="col-xs-6">' . $admin . ' ' . $statistics . '</div>
                <div class="col-xs-6 text-right">' . $jumpmenu . '</div>
            </div>
            <div>' . $seiten . '</div>
        </form>';

    if ($gesamt) {
        $headdate =
            '<a class="titlelink" href="index.php?site=clanwars&amp;page=' . $page . '&amp;sort=date&amp;type=' .
            $type . '">' . $_language->module[ 'date' ] . ':</a>';
        $headgame =
            '<a class="titlelink" href="index.php?site=clanwars&amp;page=' . $page . '&amp;sort=games&amp;type=' .
            $type . '">' . $_language->module[ 'game' ] . ':</a>';
        $headsquad =
            '<a class="titlelink" href="index.php?site=clanwars&amp;page=' . $page . '&amp;sort=squad&amp;type=' .
            $type . '">' . $_language->module[ 'squad' ] . ':</a>';
        $headcountry =
            '<a class="titlelink" href="index.php?site=clanwars&amp;page=' . $page . '&amp;sort=oppcountry&amp;type=' .
            $type . '">' . $_language->module[ 'country' ] . ':</a>';
        $headleague =
            '<a class="titlelink" href="index.php?site=clanwars&amp;page=' . $page . '&amp;sort=league&amp;type=' .
            $type . '">' . $_language->module[ 'league' ] . ':</a>';

        $data_array = array();
        $data_array['$headdate'] = $headdate;
        $data_array['$headgame'] = $headgame;
        $data_array['$headsquad'] = $headsquad;
        $data_array['$headcountry'] = $headcountry;
        $data_array['$headleague'] = $headleague;
        $clanwars_head = $GLOBALS["_template"]->replaceTemplate("clanwars_head", $data_array);
        echo $clanwars_head;

        $n = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            if ($n % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }
            $date = getformatdate($ds[ 'date' ]);
            $squad =
                '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $ds[ 'squad' ] . '&amp;page=' . $page .
                '&amp;sort=game&amp;type=' . $type . '&amp;only=squad"><strong>' . $ds[ 'squadname' ] . '</strong></a>';
            $league = '<a href="' . getinput($ds[ 'leaguehp' ]) . '" target="_blank">' . $ds[ 'league' ] . '</a>';
            $oppcountry = "[flag]" . $ds[ 'oppcountry' ] . "[/flag]";
            $country = flags($oppcountry);
            $opponent = '<a href="' . getinput($ds[ 'opphp' ]) . '" target="_blank"><strong>' . $ds[ 'opptag' ] .
                '</strong></a>';
            $hometeam = $ds[ 'hometeam' ];
            $oppteam = $ds[ 'oppteam' ];
            $server = $ds[ 'server' ];
            if (file_exists('images/games/' . $ds[ 'game' ] . '.gif')) {
                $pic = $ds[ 'game' ] . '.gif';
            }
            $game =
                '<a href="index.php?site=clanwars&amp;action=showonly&amp;id=' . $ds[ 'game' ] . '&amp;page=' . $page .
                '&amp;sort=game&amp;type=' . $type . '&amp;only=game"><img src="images/games/' . $pic . '" alt=""></a>';

            $homescr = array_sum(unserialize($ds[ 'homescore' ]));
            $oppscr = array_sum(unserialize($ds[ 'oppscore' ]));

            if ($homescr > $oppscr) {
                $results = '<font color="' . $wincolor . '">' . $homescr . ':' . $oppscr . '</font>';
            } elseif ($homescr < $oppscr) {
                $results = '<font color="' . $loosecolor . '">' . $homescr . ':' . $oppscr . '</font>';
            } else {
                $results = '<font color="' . $drawcolor . '">' . $homescr . ':' . $oppscr . '</font>';
            }

            if ($anzcomments = getanzcwcomments($ds[ 'cwID' ])) {
                $details = '<a href="index.php?site=clanwars_details&amp;cwID=' . $ds[ 'cwID' ] .
                    '"><img src="images/icons/foldericons/newhotfolder.gif" alt="' . $_language->module[ 'details' ] .
                    '"> (' . $anzcomments . ')</a>';
            } else {
                $details = '<a href="index.php?site=clanwars_details&amp;cwID=' . $ds[ 'cwID' ] .
                    '"><img src="images/icons/foldericons/folder.gif" alt="' . $_language->module[ 'details' ] .
                    '"> (0)</a>';
            }

            $multiple = '';
            $admdel = '';
            if (isclanwaradmin($userID)) {
                $multiple = '<input class="input" type="checkbox" name="cwID[]" value="' . $ds[ 'cwID' ] . '">';
            }

            $data_array = array();
            $data_array['$multiple'] = $multiple;
            $data_array['$date'] = $date;
            $data_array['$game'] = $game;
            $data_array['$squad'] = $squad;
            $data_array['$opponent'] = $opponent;
            $data_array['$country'] = $country;
            $data_array['$league'] = $league;
            $data_array['$results'] = $results;
            $data_array['$details'] = $details;
            $clanwars_content = $GLOBALS["_template"]->replaceTemplate("clanwars_content", $data_array);
            echo $clanwars_content;
            unset($result, $anzcomments);
            $n++;
        }
        if (isclanwaradmin($userID)) {
            $admdel = '<div class="row">
        <div class="col-xs-6">
            <input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);"> ' .
                $_language->module[ 'select_all' ] . '
        </div>
        <div class="col-xs-6 input-group text-right">
            <select name="quickactiontype" class="form-control">
                <option value="delete">' . $_language->module[ 'delete_selected' ] . '</option>
            </select>
            <span class="input-group-btn"><input type="submit" name="quickaction" value="' .
                $_language->module[ 'go' ] . '" class="btn btn-danger"></span>
        </div></div>';
        }

        $data_array = array();
        $data_array['$admdel'] = $admdel;
        $clanwars_foot = $GLOBALS["_template"]->replaceTemplate("clanwars_foot", $data_array);
        echo $clanwars_foot;
    } else {
        echo $_language->module[ 'no_entries' ];
    }
}
