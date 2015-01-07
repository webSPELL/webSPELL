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
    $_language->readModule('demos');
}

if (isset($_POST[ 'save' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('demos');

    if (!isfileadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    if (isset($_FILES[ 'demo' ])) {
        $demo = $_FILES[ 'demo' ];
    } else {
        $demo = null;
    }
    $game = $_POST[ 'game' ];
    $clanname1 = $_POST[ 'clanname1' ];
    $clanname2 = $_POST[ 'clanname2' ];
    $clan1 = $_POST[ 'clan1' ];
    $clan2 = $_POST[ 'clan2' ];
    $hp1 = $_POST[ 'hp1' ];
    $hp2 = $_POST[ 'hp2' ];
    $country1 = $_POST[ 'country1' ];
    $country2 = $_POST[ 'country2' ];
    $league = $_POST[ 'league' ];
    $leaguehp = $_POST[ 'leaguehp' ];
    $maps = $_POST[ 'maps' ];
    $player = $_POST[ 'player' ];
    $comments = $_POST[ 'comments' ];
    $date = strtotime($_POST[ 'date' ]);
    $link = $_POST[ 'link' ];

    $filepath = "./demos/";
    if ($demo[ 'name' ] != "") {
        $des_file = $filepath . $demo[ 'name' ];
        if (!file_exists($des_file)) {
            move_uploaded_file($demo[ 'tmp_name' ], $des_file);
            @chmod($des_file, 0755);
            $file = $demo[ 'name' ];
        } else {
            die($_language->module[ 'file_exists' ]);
        }
    } else {
        if (stristr($link, "http://")) {
            $file = $link;
        }
    }

    safe_query(
        "INSERT INTO `" . PREFIX . "demos` (
            `date`,
            `game`,
            `clan1`,
            `clan2`,
            `clantag1`,
            `clantag2`,
            `url1`,
            `url2`,
            `country1`,
            `country2`,
            `league`,
            `leaguehp`,
            `maps`,
            `player`,
            `file`,
            `downloads`,
            `comments`
        )
        VALUES (
            '$date',
            '$game',
            '$clanname1',
            '$clanname2',
            '$clan1',
            '$clan2',
            '$hp1',
            '$hp2',
            '$country1',
            '$country2',
            '$league',
            '$leaguehp',
            '$maps',
            '$player',
            '$file',
            '0',
            '$comments'
        )"
    );
    header("Location: index.php?site=demos");
} elseif (isset($_POST[ 'saveedit' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('demos');

    if (!isfileadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    if (isset($_FILES[ 'demo' ])) {
        $demo = $_FILES[ 'demo' ];
    } else {
        $demo = null;
    }
    $demoID = $_POST[ 'demoID' ];
    $game = $_POST[ 'game' ];
    $clanname1 = $_POST[ 'clanname1' ];
    $clanname2 = $_POST[ 'clanname2' ];
    $clan1 = $_POST[ 'clan1' ];
    $clan2 = $_POST[ 'clan2' ];
    $hp1 = $_POST[ 'hp1' ];
    $hp2 = $_POST[ 'hp2' ];
    $country1 = $_POST[ 'country1' ];
    $country2 = $_POST[ 'country2' ];
    $league = $_POST[ 'league' ];
    $leaguehp = $_POST[ 'leaguehp' ];
    $maps = $_POST[ 'maps' ];
    $player = $_POST[ 'player' ];
    $comments = $_POST[ 'comments' ];
    $link = $_POST[ 'link' ];

    $filepath = "./demos/";
    $file = "";
    if ($demo[ 'name' ] != "") {
        $des_file = $filepath . $demo[ 'name' ];
        if (!file_exists($des_file)) {
            move_uploaded_file($demo[ 'tmp_name' ], $des_file);
            @chmod($des_file, 0755);
            $file = $demo[ 'name' ];
        } else {
            die($_language->module[ 'file_exists' ]);
        }
    } else {
        if (stristr($link, "http://") && $link != "http://" && $link != "") {
            $file = $link;
        }
    }
    if ($file != "") {
        $mysql_file = "file='" . $file . "',";
    } else {
        $mysql_file = "";
    }
    $date = strtotime($_POST[ 'date' ]);

    safe_query(
        "UPDATE
            `" . PREFIX . "demos`
        SET
            date='$date',
            game='$game',
            clan1='$clanname1',
            clan2='$clanname2',
            clantag1='$clan1',
            clantag2='$clan2',
            url1='$hp1',
            url2='$hp2',
            country1='$country1',
            country2='$country2',
            league='$league',
            leaguehp='$leaguehp',
            maps='$maps',
            player='$player',
            " . $mysql_file . "
            comments='$comments'
        WHERE
            demoID='" . (int)$demoID."'"
    );
    header("Location: index.php?site=demos");
} elseif (isset($_GET[ 'delete' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('demos');

    if (!isfileadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $demoID = $_GET[ 'demoID' ];
    $filepath = "./demos/";
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "demos WHERE demoID = '" . (int)$demoID."'");
    $ds = mysqli_fetch_array($ergebnis);
    if (file_exists($filepath . $ds[ 'file' ])) {
        @unlink($filepath . $ds[ 'file' ]);
    }
    safe_query("DELETE FROM `" . PREFIX . "demos` WHERE `demoID` = '" . (int)$demoID."'");
    safe_query("DELETE FROM `" . PREFIX . "comments` WHERE `parentID` = '" . (int)$demoID . "' AND `type` = 'de'");
    header("Location: index.php?site=demos");
}

eval ("\$title_demos = \"" . gettemplate("title_demos") . "\";");
echo $title_demos;

$games = null;
$gamesa = safe_query("SELECT * FROM `" . PREFIX . "games` ORDER BY `name`");
while ($ds = mysqli_fetch_array($gamesa)) {
    $games .= '<option value="' . $ds[ 'tag' ] . '">' . $ds[ 'name' ] . '</option>';
}

function top5()
{
    global $_language;
    $_language->readModule('demos');

    echo '<div class="row">';

    // RATING
    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "demos` ORDER BY `rating` DESC LIMIT 0,5");
    $top = 'TOP 5 DEMOS (' . $_language->module[ 'rating' ] . ')';

    eval ("\$top5_head = \"" . gettemplate("top5_head") . "\";");
    echo $top5_head;

    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
        $country1 = flags($country1);
        $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
        $country2 = flags($country2);
        $link =
            '<a href="index.php?site=demos&amp;action=showdemo&amp;demoID=' . $ds[ 'demoID' ] . '">' . $country1 . ' ' .
            $ds[ 'clantag1' ] . ' vs. ' . $ds[ 'clantag2' ] . ' ' . $country2 . '</a>';
        $ratings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
            $ratings[ $i ] = 1;
        }
        $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
        foreach ($ratings as $pic) {
            $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
        }

        echo '<li class="list-group-item"><span class="badge">' . $ratingpic . '</span> ' . $n . '. ' . $link . '</li>';

        unset($ratingpic);
        $n++;
    }

    echo '</ul></div>';

    // POINTS
    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "demos` ORDER BY `downloads` DESC LIMIT 0,5");
    $top = 'TOP 5 DEMOS (' . $_language->module[ 'downloaded' ] . ')';
    eval ("\$top5_head = \"" . gettemplate("top5_head") . "\";");
    echo $top5_head;
    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
        $country1 = flags($country1);
        $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
        $country2 = flags($country2);
        $link =
            '<a href="index.php?site=demos&amp;action=showdemo&amp;demoID=' . $ds[ 'demoID' ] . '">' . $country1 . ' ' .
            $ds[ 'clantag1' ] . ' vs. ' . $ds[ 'clantag2' ] . ' ' . $country2 . '</a>';

        echo '<li class="list-group-item"><span class="badge">' . $ds[ 'downloads' ] . '</span> ' . $n . '. ' . $link .
            '</li>';

        $n++;
    }
    echo '</ul></div>';
    echo '</div>';
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = "";
}

if ($action == "new") {
    if (isfileadmin($userID)) {
        $countries = getcountries();
        eval ("\$demo_new = \"" . gettemplate("demo_new") . "\";");
        echo $demo_new;
    } else {
        redirect('index.php?site=demos', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "edit") {
    $demoID = $_GET[ 'demoID' ];
    if (isfileadmin($userID)) {
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT * FROM `" . PREFIX . "demos` WHERE `demoID` = '" . (int)$demoID."'"
            )
        );
        $date = date("Y-m-d", $ds[ 'date' ]);
        $games = str_replace(' selected="selected"', '', $games);
        $games =
            str_replace('value="' . $ds[ 'game' ] . '"', 'value="' . $ds[ 'game' ] . '" selected="selected"', $games);
        $countries = getcountries();
        $country1 = str_replace(
            'value="' . $ds[ 'country1' ] . '"',
            'value="' . $ds[ 'country1' ] . '" selected="selected"',
            $countries
        );
        $country2 = str_replace(
            'value="' . $ds[ 'country2' ] . '"',
            'value="' . $ds[ 'country2' ] . '" selected="selected"',
            $countries
        );
        $clanname1 = htmlspecialchars($ds[ 'clan1' ]);
        $clanname2 = htmlspecialchars($ds[ 'clan2' ]);
        $clan1 = htmlspecialchars($ds[ 'clantag1' ]);
        $clan2 = htmlspecialchars($ds[ 'clantag2' ]);
        $url1 = htmlspecialchars($ds[ 'url1' ]);
        $url2 = htmlspecialchars($ds[ 'url2' ]);
        $maps = htmlspecialchars($ds[ 'maps' ]);
        $player = htmlspecialchars($ds[ 'player' ]);
        $league = htmlspecialchars($ds[ 'league' ]);
        $leaguehp = htmlspecialchars($ds[ 'leaguehp' ]);
        if (stristr($ds[ 'file' ], "http://")) {
            $extern = $ds[ 'file' ];
        } else {
            $extern = 'http://';
        }

        $comments = '<option value="0">' . $_language->module[ 'disable_comments' ] . '</option><option value="1">' .
            $_language->module[ 'user_comments' ] . '</option><option value="2">' .
            $_language->module[ 'visitor_comments' ] . '</option>';
        $comments = str_replace(
            'value="' . $ds[ 'comments' ] . '"',
            'value="' . $ds[ 'comments' ] . '" selected="selected"',
            $comments
        );

        $bg1 = BG_1;
        eval ("\$demo_edit = \"" . gettemplate("demo_edit") . "\";");
        echo $demo_edit;
    } else {
        redirect('index.php?site=demos', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "showdemo") {
    $demoID = $_GET[ 'demoID' ];
    if (isfileadmin($userID)) {
        echo
            '<a href="index.php?site=demos&amp;action=new" class="btn btn-danger">' .
                $_language->module[ 'new_demo' ] . '</a>';
    }
    echo '<a href="index.php?site=demos" class="btn btn-primary">' . $_language->module[ 'all_demos' ] . '</a><br><br>';

    $result = safe_query("SELECT * FROM `" . PREFIX . "demos` WHERE `demoID` = '" . (int)$demoID."'");
    $ds = mysqli_fetch_array($result);
    $date = getformatdate($ds[ 'date' ]);
    $league = '<a href="' . $ds[ 'leaguehp' ] . '" target="_blank">' . $ds[ 'league' ] . '</a>';
    $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
    $country1 = flags($country1);
    $clan1 = $country1 . ' <a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'clan1' ] . '</a>';
    $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
    $country2 = flags($country2);
    $clan2 = $country2 . ' <a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'clan2' ] . '</a>';
    $game = '<img src="images/games/' . $ds[ 'game' ] . '.gif" width="13" height="13" alt=""> ' . $ds[ 'game' ];

    $clicks = $ds[ 'downloads' ];
    $player = $ds[ 'player' ];
    $maps = $ds[ 'maps' ];

    $ratings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
        $ratings[ $i ] = 1;
    }
    $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
    foreach ($ratings as $pic) {
        $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
    }

    if ($loggedin) {
        $download = '<a href="download.php?demoID=' . $ds[ 'demoID' ] .
            '" class="btn btn-lg btn-success"><span class="icon-download icon-large"></span> ' .
            $_language->module[ 'download_now' ] . '</a>';

        $getdemos = safe_query("SELECT demos FROM " . PREFIX . "user WHERE userID='$userID'");
        $found = false;
        if (mysqli_num_rows($getdemos)) {
            $ga = mysqli_fetch_array($getdemos);
            if ($ga[ 'demos' ] != "") {
                $string = $ga[ 'demos' ];
                $array = explode(":", $string);
                $anzarray = count($array);
                for ($i = 0; $i < $anzarray; $i++) {
                    if ($array[ $i ] == $demoID) {
                        $found = true;
                    }
                }
            }
        }
        if ($found) {
            $rateform = '<strong>' . $_language->module[ 'allready_rated' ] . '</strong>';
        } else {
            $rateform = '<div class="input-group">
                            <select name="rating" class="form-control">
                                <option>0 - ' . $_language->module[ 'poor' ] . '</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10 - ' . $_language->module[ 'perfect' ] . '</option>
                            </select>

                            <span class="input-group-btn">
                                <input type="submit" name="Submit" value="' . $_language->module[ 'rate' ] .
                                '" class="btn btn-primary">
                            </span>
                        </div>
                        <input type="hidden" name="userID" value="' . $userID . '">
                        <input type="hidden" name="type" value="de">
                        <input type="hidden" name="id" value="' . $ds[ 'demoID' ] . '">';
        }
    } else {
        $rateform = '<strong>' . $_language->module[ 'to_rate' ] . '</strong>';
        $download = '<strong>' . $_language->module[ 'to_download' ] . '</strong>';
    }

    $adminaction = "";
    if (isfileadmin($userID)) {
        $adminaction =
            '<a href="index.php?site=demos&amp;action=edit&amp;demoID=' . $ds[ 'demoID' ] .
                '" class="btn btn-danger">' . $_language->module[ 'edit' ] . '</a>
            <input type="button" onclick="MM_confirm(
                    \'really delete this demo?\',
                    \'demos.php?delete=true&amp;demoID=' . $ds[ 'demoID' ] . '\'
                )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger">';
    }

    eval ("\$demos_showdemo = \"" . gettemplate("demos_showdemo") . "\";");
    echo $demos_showdemo;

    $comments_allowed = $ds[ 'comments' ];
    $parentID = $demoID;
    $type = "de";
    $referer = "index.php?site=demos&amp;action=showdemo&amp;demoID=$demoID";

    include("comments.php");
} elseif ($action == "showgame") {
    $game = $_GET[ 'game' ];

    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }
    $sort = "date";
    if (isset($_GET[ 'sort' ])) {
        if (($_GET[ 'sort' ] == 'date') || ($_GET[ 'sort' ] == 'game') || ($_GET[ 'sort' ] == 'league') ||
            ($_GET[ 'sort' ] == 'rating') || ($_GET[ 'sort' ] == 'downloads')
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

    if (isfileadmin($userID)) {
        echo
            '<a href="index.php?site=demos&amp;action=new" class="btn btn-danger">' .
            $_language->module[ 'new_demo' ] . '</a><br><br>';
    }

    $alle = safe_query("SELECT demoID FROM " . PREFIX . "demos WHERE game='$game'");
    $gesamt = mysqli_num_rows($alle);
    $pages = 1;

    $max = $maxdemos;
    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link =
            makepagelink(
                "index.php?site=demos&amp;action=showgame&amp;game=$game&amp;sort=$sort&amp;type=$type",
                $page,
                $pages
            );
    } else {
        $page_link = "";
    }

    if ($page == "1") {
        $ergebnis =
            safe_query(
                "SELECT * FROM `" . PREFIX . "demos` WHERE `game` = '$game' ORDER BY $sort $type LIMIT 0, " . (int)$max
            );
        if ($type == "DESC") {
            $n = $gesamt;
        } else {
            $n = 1;
        }
    } else {
        $start = $page * $max - $max;
        $ergebnis =
            safe_query(
                "SELECT
                    *
                FROM
                    `" . PREFIX . "demos`
                WHERE
                    `game` = '$game'
                ORDER BY
                    $sort $type
                LIMIT
                    $start, " . (int)$max
            );
        if ($type == "DESC") {
            $n = ($gesamt) - $page * $max + $max;
        } else {
            $n = ($gesamt + 1) - $page * $max + $max;
        }
    }
    if ($gesamt) {
        top5();
        if ($type == "ASC") {
            echo '<a href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' . $page .
                '&amp;sort=' . $sort . '&amp;type=DESC">' . $_language->module[ 'sort' ] .
                ':</a> <img src="images/icons/asc.gif" width="9" height="7" alt="">&nbsp;&nbsp;&nbsp;';
        } else {
            echo '<a href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' . $page .
                '&amp;sort=' . $sort . '&amp;type=ASC">' . $_language->module[ 'sort' ] .
                ':</a> <img src="images/icons/desc.gif" width="9" height="7" alt="">&nbsp;&nbsp;&nbsp;';
        }

        echo $page_link;
        echo '<br><br>';

        $headdate =
            '<a class="titlelink" href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' .
            $page . '&amp;sort=date&amp;type=' . $type . '">' . $_language->module[ 'date' ] . ':</a>';
        $headgame = '' . $_language->module[ 'game' ] . ':';
        $headleague =
            '<a class="titlelink" href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' .
            $page . '&amp;sort=league&amp;type=' . $type . '">' . $_language->module[ 'league' ] . ':</a>';
        $headrating =
            '<a class="titlelink" href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' .
            $page . '&amp;sort=rating&amp;type=' . $type . '">' . $_language->module[ 'rating' ] . ':</a>';
        $headclicks =
            '<a class="titlelink" href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' .
            $page . '&amp;sort=downloads&amp;type=' . $type . '">' . $_language->module[ 'download' ] . ':</a>';

        eval ("\$demos_head = \"" . gettemplate("demos_head") . "\";");
        echo $demos_head;
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
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $clan1 = $country1 . ' <a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'clantag1' ] . '</a>';
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $clan2 = '<a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'clantag2' ] . '</a> ' . $country2;
            $game = '<img src="images/games/' . $ds[ 'game' ] . '.gif" width="13" height="13" alt="">';
            $clicks = $ds[ 'downloads' ];

            $ratings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
            }

            eval ("\$demos_content = \"" . gettemplate("demos_content") . "\";");
            echo $demos_content;
            unset($ratingpic);
            $n++;
        }
        eval ("\$demos_foot = \"" . gettemplate("demos_foot") . "\";");
        echo $demos_foot;
    } else {
        echo $_language->module[ 'no_demos' ];
    }
} else {
    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }
    $sort = "date";
    if (isset($_GET[ 'sort' ])) {
        if (
            $_GET[ 'sort' ] == 'date' ||
            $_GET[ 'sort' ] == 'game' ||
            $_GET[ 'sort' ] == 'league' ||
            $_GET[ 'sort' ] == 'rating' ||
            $_GET[ 'sort' ] == 'downloads'
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

    if (isfileadmin($userID)) {
        echo
            '<a href="index.php?site=demos&amp;action=new" class="btn btn-danger">' .
                $_language->module[ 'new_demo' ] . '</a><br><br>';
    }
    $alle = safe_query("SELECT `demoID` FROM `" . PREFIX . "demos`");
    $gesamt = mysqli_num_rows($alle);
    $pages = 1;

    $max = $maxdemos;
    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=demos&amp;sort=$sort&amp;type=$type", $page, $pages);
    } else {
        $page_link = "";
    }

    if ($page == "1") {
        $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "demos` ORDER BY $sort $type LIMIT 0, " . (int)$max);
        if ($type == "DESC") {
            $n = $gesamt;
        } else {
            $n = 1;
        }
    } else {
        $start = $page * $max - $max;
        $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "demos` ORDER BY $sort $type LIMIT $start, ". (int)$max);
        if ($type == "DESC") {
            $n = ($gesamt) - $page * $max + $max;
        } else {
            $n = ($gesamt + 1) - $page * $max + $max;
        }
    }
    if ($gesamt) {
        top5();
        if ($type == "ASC") {
            echo '<a href="index.php?site=demos&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
                $_language->module[ 'sort' ] .
                ':</a> <img src="images/icons/asc.gif" width="9" height="7" alt="">&nbsp;&nbsp;&nbsp;';
        } else {
            echo '<a href="index.php?site=demos&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
                $_language->module[ 'sort' ] .
                ':</a> <img src="images/icons/desc.gif" width="9" height="7" alt="">&nbsp;&nbsp;&nbsp;';
        }

        echo $page_link;
        echo '<br><br>';

        $headdate =
            '<a class="titlelink" href="index.php?site=demos&amp;page=' . $page . '&amp;sort=date&amp;type=' . $type .
            '">' . $_language->module[ 'date' ] . ':</a>';
        $headgame =
            '<a class="titlelink" href="index.php?site=demos&amp;page=' . $page . '&amp;sort=game&amp;type=' . $type .
            '">' . $_language->module[ 'game' ] . ':</a>';
        $headleague =
            '<a class="titlelink" href="index.php?site=demos&amp;page=' . $page . '&amp;sort=league&amp;type=' . $type .
            '">' . $_language->module[ 'league' ] . ':</a>';
        $headrating =
            '<a class="titlelink" href="index.php?site=demos&amp;page=' . $page . '&amp;sort=rating&amp;type=' . $type .
            '">' . $_language->module[ 'rating' ] . ':</a>';
        $headclicks =
            '<a class="titlelink" href="index.php?site=demos&amp;page=' . $page . '&amp;sort=downloads&amp;type=' .
            $type . '">' . $_language->module[ 'download' ] . ':</a>';

        eval ("\$demos_head = \"" . gettemplate("demos_head") . "\";");
        echo $demos_head;
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
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $clan1 = '<a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'clantag1' ] . '</a> ' . $country1;
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $clan2 = $country2 . ' <a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'clantag2' ] . '</a> ';
            $game = '<a href="index.php?site=demos&amp;action=showgame&amp;game=' . $ds[ 'game' ] .
                '"><img src="images/games/' . $ds[ 'game' ] . '.gif" width="13" height="13" alt=""></a>';
            $clicks = $ds[ 'downloads' ];

            $ratings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
            }

            eval ("\$demos_content = \"" . gettemplate("demos_content") . "\";");
            echo $demos_content;
            unset($ratingpic);
            $n++;
        }
        eval ("\$demos_foot = \"" . gettemplate("demos_foot") . "\";");
        echo $demos_foot;
    } else {
        echo $_language->module[ 'no_demos' ];
    }
}
