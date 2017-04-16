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

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = "";
}

$_language->readModule('demos');

$title_demos = $GLOBALS["_template"]->replaceTemplate("title_demos", array());
echo $title_demos;

if (isset($_POST[ 'save' ])) {
    if (isfileadmin($userID)) {
        $_language->readModule('formvalidation', true);

        $demo = new \webspell\HttpUpload('demo');

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

        $error = array();

        $file = null;

        if ($demo->hasFile()) {
            if ($demo->hasError() === false) {
                $new_name = $filepath . $demo->getFileName();
                if ($demo->saveAs($new_name) === false) {
                    $error[ ] = $_language->module[ 'file_already_exists' ];
                } else {
                    @chmod($new_name, $new_chmod);
                    $file = $demo->getFileName();
                }
            } else {
                $error[ ] = $demo->translateError();
            }
        } else {
            if ($link != "http://") {
                $file = $link;
            }
        }

        if (count($error)) {
            echo generateErrorBoxFromArray($_language->module[ 'errors_there' ], $error);
        } else {
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

            $id = mysqli_insert_id($_database);
            $message = generateAlert($_language->module[ 'successful' ], 'alert-success');

            redirect("index.php?site=demos&action=showdemo&demoID=" . $id, $message);
        }
    } else {
        echo generateErrorBox($_language->module[ 'no_access' ]);
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    if (isfileadmin($userID)) {
        $_language->readModule('formvalidation', true);
        $demo = new \webspell\HttpUpload('demo');

        $demoID = (int)$_POST[ 'demoID' ];
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
        $date = strtotime($_POST[ 'date' ]);

        $filepath = "./demos/";

        $error = array();

        $file = null;

        if ($demo->hasFile()) {
            if ($demo->hasError() === false) {
                $new_name = $filepath . $demo->getFileName();
                if ($demo->saveAs($new_name) === false) {
                    $error[ ] = $_language->module[ 'file_already_exists' ];
                } else {
                    @chmod($new_name, $new_chmod);
                    $file = $demo->getFileName();
                }
            } else {
                $error[ ] = $demo->translateError();
            }
        } else {
            if ($link != "http://") {
                $file = $link;
            }
        }

        if (count($error)) {
            echo generateErrorBoxFromArray($_language->module[ 'errors_there' ], $error);
        } else {
            if (isset($file)) {
                $mysql_file = "file='" . $file . "',";
            } else {
                $mysql_file = "";
            }

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
                    demoID='" . $demoID . "'"
            );
            $message = generateAlert($_language->module[ 'successful' ], 'alert-success');
            redirect("index.php?site=demos&action=showdemo&demoID=" . $demoID, $message);
        }
    } else {
        echo generateErrorBox($_language->module[ 'no_access' ]);
    }
} elseif ($action == "delete") {
    if (isfileadmin($userID)) {
        $demoID = (int)$_GET[ 'demoID' ];
        $filepath = "./demos/";
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "demos WHERE demoID = '" . $demoID . "'");
        $ds = mysqli_fetch_array($ergebnis);
        if (file_exists($filepath . $ds[ 'file' ])) {
            @unlink($filepath . $ds[ 'file' ]);
        }

        safe_query("DELETE FROM `" . PREFIX . "demos` WHERE `demoID` = '" . $demoID . "'");
        safe_query("DELETE FROM `" . PREFIX . "comments` WHERE `parentID` = '" . $demoID . "' AND `type` = 'de'");

        $message = generateAlert($_language->module[ 'successful' ], 'alert-success');
        redirect("index.php?site=demos", $message);
    } else {
        echo generateErrorBox($_language->module[ 'no_access' ]);
    }
} elseif ($action == "new") {
    if (isfileadmin($userID)) {
        $countries = getcountries();
        $games = getGamesAsOptionList();
        $data_array = array();
        $data_array['$games'] = $games;
        $data_array['$countries'] = $countries;
        $demo_new = $GLOBALS["_template"]->replaceTemplate("demo_new", $data_array);
        echo $demo_new;
    } else {
        redirect('index.php?site=demos', generateErrorBox($_language->module[ 'no_access' ]));
    }
} elseif ($action == "edit") {
    $demoID = (int)$_GET[ 'demoID' ];
    if (isfileadmin($userID)) {
        $ds = mysqli_fetch_array(safe_query("SELECT * FROM `" . PREFIX . "demos` WHERE `demoID` = '" . $demoID ."'"));
        $date = date("Y-m-d", $ds[ 'date' ]);
        $games = getGamesAsOptionList($ds[ 'game' ]);

        $country1 = getcountries($ds[ 'country1' ]);
        $country2 = getcountries($ds[ 'country2' ]);

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
        $data_array = array();
        $data_array['$date'] = $date;
        $data_array['$games'] = $games;
        $data_array['$clanname1'] = $clanname1;
        $data_array['$clan1'] = $clan1;
        $data_array['$country1'] = $country1;
        $data_array['$url1'] = $url1;
        $data_array['$clanname2'] = $clanname2;
        $data_array['$clan2'] = $clan2;
        $data_array['$country2'] = $country2;
        $data_array['$url2'] = $url2;
        $data_array['$league'] = $league;
        $data_array['$leaguehp'] = $leaguehp;
        $data_array['$maps'] = $maps;
        $data_array['$player'] = $player;
        $data_array['$extern'] = $extern;
        $data_array['$comments'] = $comments;
        $data_array['$demoID'] = $demoID;
        $demo_edit = $GLOBALS["_template"]->replaceTemplate("demo_edit", $data_array);
        echo $demo_edit;
    } else {
        redirect('index.php?site=demos', generateErrorBox($_language->module[ 'no_access' ]));
    }
} elseif ($action == "showdemo") {
    $demoID = $_GET[ 'demoID' ];
    echo '<div class="form-group">';
    if (isfileadmin($userID)) {
        echo
            '<a href="index.php?site=demos&amp;action=new" class="btn btn-primary">' . $_language->module[ 'new_demo' ] .
            '</a> ';
    }
    echo '<a href="index.php?site=demos" class="btn btn-default">' . $_language->module[ 'all_demos' ] . '</a></div>';

    $result = safe_query("SELECT * FROM `" . PREFIX . "demos` WHERE `demoID` = '" . (int)$demoID . "'");
    $ds = mysqli_fetch_array($result);
    $date = getformatdate($ds[ 'date' ]);
    $league = '<a href="' . $ds[ 'leaguehp' ] . '" target="_blank">' . $ds[ 'league' ] . '</a>';
    $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
    $country1 = flags($country1);
    $clan1 = $country1 . ' <a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'clan1' ] . '</a>';
    $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
    $country2 = flags($country2);
    $clan2 = $country2 . ' <a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'clan2' ] . '</a>';
    $game = '<img src="images/games/' . $ds[ 'game' ] . '.gif" alt=""> ' .
        getgamename($ds[ 'game' ]);

    $clicks = $ds[ 'downloads' ];
    $player = $ds[ 'player' ];
    $maps = $ds[ 'maps' ];

    $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
        $ratings[ $i ] = 1;
    }
    $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" alt="">';
    foreach ($ratings as $pic) {
        $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" alt="">';
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
                                <input type="submit" name="Submit"
value="' . $_language->module[ 'rate' ] . '" class="btn btn-primary">
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
        $adminaction = '<div class="panel-body text-right">
            <a href="index.php?site=demos&amp;action=edit&amp;demoID=' . $ds[ 'demoID' ] . '" class="btn btn-warning btn-sm" role="button">' . $_language->module[ 'edit' ] . '</a>
            <input type="button" onclick="MM_confirm(\'really delete this demo?\', \'index.php?site=demos&amp;action=delete&amp;demoID=' . $ds[ 'demoID' ] . '\')" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger btn-sm">
        </div>';
    }

    $data_array = array();
    $data_array['$date'] = $date;
    $data_array['$game'] = $game;
    $data_array['$clan1'] = $clan1;
    $data_array['$clan2'] = $clan2;
    $data_array['$league'] = $league;
    $data_array['$player'] = $player;
    $data_array['$maps'] = $maps;
    $data_array['$clicks'] = $clicks;
    $data_array['$ratingpic'] = $ratingpic;
    $data_array['$rateform'] = $rateform;
    $data_array['$download'] = $download;
    $data_array['$adminaction'] = $adminaction;
    $demos_showdemo = $GLOBALS["_template"]->replaceTemplate("demos_showdemo", $data_array);
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
            '<div class="form-group">
                <a href="index.php?site=demos&amp;action=new" class="btn btn-primary" role="button">' . $_language->module[ 'new_demo' ] . '</a>
            </div>';
    }

    $alle = safe_query("SELECT demoID FROM " . PREFIX . "demos WHERE game='$game'");
    $gesamt = mysqli_num_rows($alle);
    $pages = 1;

    $max = $maxdemos;
    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link = makepagelink(
            "index.php?site=demos&amp;action=showgame&amp;game=$game&amp;sort=$sort&amp;type=$type",
            $page,
            $pages
        );
    } else {
        $page_link = "";
    }

    if ($page == "1") {
        $ergebnis = safe_query(
            "SELECT * FROM `" . PREFIX . "demos`
            WHERE `game` = '$game'
            ORDER BY $sort $type LIMIT 0, " .(int)$max
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
        echo '<div class="row">';

        // RATING
        $ergebnis_top_5_rating = safe_query("SELECT * FROM `" . PREFIX . "demos` ORDER BY `rating` DESC LIMIT 0,5");
        $top = 'TOP 5 DEMOS (' . $_language->module[ 'rating' ] . ')';

        $data_array = array();
        $data_array['$top'] = $top;
        $top5_head = $GLOBALS["_template"]->replaceTemplate("top5_head", $data_array);
        echo $top5_head;

        while ($ds = mysqli_fetch_array($ergebnis_top_5_rating)) {
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $link =
                '<a href="index.php?site=demos&amp;action=showdemo&amp;demoID=' . $ds[ 'demoID' ] . '">' . $country1 .
                ' ' .$ds[ 'clantag1' ] . ' vs. ' . $ds[ 'clantag2' ] . ' ' . $country2 . '</a>';
            $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
            }

            echo '<li class="list-group-item"><span class="badge">' . $ratingpic . '</span> ' .
                $n . '. ' . $link . '</li>';
        }

        echo '</ul></div>';

        // POINTS
        $ergebnis_top_5_downloads = safe_query(
            "SELECT * FROM `" . PREFIX . "demos` ORDER BY `downloads` DESC LIMIT 0,5"
        );
        $top = 'TOP 5 DEMOS (' . $_language->module[ 'downloaded' ] . ')';
        $data_array = array();
        $data_array['$top'] = $top;
        $top5_head = $GLOBALS["_template"]->replaceTemplate("top5_head", $data_array);
        echo $top5_head;
        while ($ds = mysqli_fetch_array($ergebnis_top_5_downloads)) {
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $link =
                '<a href="index.php?site=demos&amp;action=showdemo&amp;demoID=' . $ds[ 'demoID' ] . '">' .
                $country1 . ' ' .$ds[ 'clantag1' ] . ' vs. ' . $ds[ 'clantag2' ] . ' ' . $country2 . '</a>';

            echo '<li class="list-group-item"><span class="badge">'.$ds[ 'downloads' ] . '</span> ' .
                $n . '. ' . $link .'</li>';
        }
        echo '</ul></div>';
        echo '</div>';

        echo '<p>';
        if ($type == "ASC") {
            echo '<a class="btn btn-default btn-xs" href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
                $_language->module['sort'] . ' <span class="glyphicon glyphicon-chevron-down"></span></a>';
        } else {
            echo '<a class="btn btn-default btn-xs" href="index.php?site=demos&amp;action=showgame&amp;game=' . $game . '&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
                $_language->module['sort'] . ' <span class="glyphicon glyphicon-chevron-up"></span></a>';
        }
        echo '</p>';

        echo $page_link;

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

        $data_array = array();
        $data_array['$headdate'] = $headdate;
        $data_array['$headgame'] = $headgame;
        $data_array['$headleague'] = $headleague;
        $data_array['$headrating'] = $headrating;
        $data_array['$headclicks'] = $headclicks;
        $demos_head = $GLOBALS["_template"]->replaceTemplate("demos_head", $data_array);
        echo $demos_head;
        $n = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $demoID = $ds[ 'demoID' ];
            $date = getformatdate($ds[ 'date' ]);
            $league = '<a href="' . $ds[ 'leaguehp' ] . '" target="_blank">' . $ds[ 'league' ] . '</a>';
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $clan1 = $country1 . ' <a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'clantag1' ] . '</a>';
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $clan2 = '<a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'clantag2' ] . '</a> ' . $country2;
            $game = '<img src="images/games/' . $ds[ 'game' ] . '.gif" alt="">';
            $clicks = $ds[ 'downloads' ];

            $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" alt="">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" alt="">';
            }

            $data_array = array();
            $data_array['$date'] = $date;
            $data_array['$game'] = $game;
            $data_array['$clan1'] = $clan1;
            $data_array['$clan2'] = $clan2;
            $data_array['$league'] = $league;
            $data_array['$ratingpic'] = $ratingpic;
            $data_array['$clicks'] = $clicks;
            $data_array['$demoID'] = $demoID;
            $demos_content = $GLOBALS["_template"]->replaceTemplate("demos_content", $data_array);
            echo $demos_content;
            unset($ratingpic);
            $n++;
        }
        $demos_foot = $GLOBALS["_template"]->replaceTemplate("demos_foot", array());
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
        if ($_GET[ 'sort' ] == 'date'
            || $_GET[ 'sort' ] == 'game'
            || $_GET[ 'sort' ] == 'league'
            || $_GET[ 'sort' ] == 'rating'
            || $_GET[ 'sort' ] == 'downloads'
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
            '<div class="form-group">
                <a href="index.php?site=demos&amp;action=new" class="btn btn-primary" role="button">' . $_language->module[ 'new_demo' ] . '</a>
            </div>';
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
        $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "demos` ORDER BY $sort $type LIMIT $start, " . (int)$max);
        if ($type == "DESC") {
            $n = ($gesamt) - $page * $max + $max;
        } else {
            $n = ($gesamt + 1) - $page * $max + $max;
        }
    }
    if ($gesamt) {
        echo '<div class="row">';

        // RATING
        $ergebnis_top_5_rating = safe_query("SELECT * FROM `" . PREFIX . "demos` ORDER BY `rating` DESC LIMIT 0,5");
        $top = 'TOP 5 DEMOS (' . $_language->module[ 'rating' ] . ')';

        $data_array = array();
        $data_array['$top'] = $top;
        $top5_head = $GLOBALS["_template"]->replaceTemplate("top5_head", $data_array);
        echo $top5_head;

        $n = 1;
        $multiTemplateData = array();
        while ($ds = mysqli_fetch_array($ergebnis_top_5_rating)) {
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $link =
                '<a href="index.php?site=demos&amp;action=showdemo&amp;demoID=' . $ds[ 'demoID' ] . '">' .
                $country1 . ' ' .$ds[ 'clantag1' ] . ' vs. ' . $ds[ 'clantag2' ] . ' ' . $country2 . '</a>';
            $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" alt="">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" alt="">';
            }

            $multiTemplateData[] = array('$badge'=>$ratingpic, '$text'=>$n . '. ' . $link);
            $n++;
        }

        echo $GLOBALS["_template"]->replaceMulti('top5_content', $multiTemplateData);
        echo $GLOBALS["_template"]->replaceTemplate("top5_foot");

        // POINTS
        $ergebnis_top_5_downloads = safe_query(
            "SELECT * FROM `" . PREFIX . "demos` ORDER BY `downloads` DESC LIMIT 0,5"
        );
        $top = 'TOP 5 DEMOS (' . $_language->module[ 'downloaded' ] . ')';
        $data_array = array();
        $data_array['$top'] = $top;
        $top5_head = $GLOBALS["_template"]->replaceTemplate("top5_head", $data_array);
        echo $top5_head;
        $multiTemplateData = array();
        while ($ds = mysqli_fetch_array($ergebnis_top_5_downloads)) {
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $link ='<a href="index.php?site=demos&amp;action=showdemo&amp;demoID=' . $ds[ 'demoID' ] . '">'.
                $country1 . ' ' .$ds[ 'clantag1' ] . ' vs. ' . $ds[ 'clantag2' ] . ' ' . $country2 . '</a>';

            $multiTemplateData[] = array('$badge'=>$ds[ 'downloads' ], '$text'=>$n . '. ' . $link);
        }

        echo $GLOBALS["_template"]->replaceMulti('top5_content', $multiTemplateData);
        echo $GLOBALS["_template"]->replaceTemplate("top5_foot");

        echo '</div>';

        echo '<p>';
        if ($type == "ASC") {
            echo '<a class="btn btn-default btn-xs" href="index.php?site=demos&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
                $_language->module['sort'] . ' <span class="glyphicon glyphicon-chevron-down"></span></a>';
        } else {
            echo '<a class="btn btn-default btn-xs" href="index.php?site=demos&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
                $_language->module['sort'] . ' <span class="glyphicon glyphicon-chevron-up"></span></a>';
        }
        echo '</p>';

        echo $page_link;

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

        $data_array = array();
        $data_array['$headdate'] = $headdate;
        $data_array['$headgame'] = $headgame;
        $data_array['$headleague'] = $headleague;
        $data_array['$headrating'] = $headrating;
        $data_array['$headclicks'] = $headclicks;
        $demos_head = $GLOBALS["_template"]->replaceTemplate("demos_head", $data_array);
        echo $demos_head;
        $n = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $demoID = $ds[ 'demoID' ];
            $date = getformatdate($ds[ 'date' ]);
            $league = '<a href="' . $ds[ 'leaguehp' ] . '" target="_blank">' . $ds[ 'league' ] . '</a>';
            $country1 = "[flag]" . $ds[ 'country1' ] . "[/flag]";
            $country1 = flags($country1);
            $clan1 = '<a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'clantag1' ] . '</a> ' . $country1;
            $country2 = "[flag]" . $ds[ 'country2' ] . "[/flag]";
            $country2 = flags($country2);
            $clan2 = $country2 . ' <a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'clantag2' ] . '</a> ';
            $game = '<a href="index.php?site=demos&amp;action=showgame&amp;game=' . $ds[ 'game' ] .
                '"><img src="images/games/' . $ds[ 'game' ] . '.gif" alt=""></a>';
            $clicks = $ds[ 'downloads' ];

            $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" alt="">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" alt="">';
            }

            $data_array = array();
            $data_array['$date'] = $date;
            $data_array['$game'] = $game;
            $data_array['$clan1'] = $clan1;
            $data_array['$clan2'] = $clan2;
            $data_array['$league'] = $league;
            $data_array['$ratingpic'] = $ratingpic;
            $data_array['$clicks'] = $clicks;
            $data_array['$demoID'] = $demoID;
            $demos_content = $GLOBALS["_template"]->replaceTemplate("demos_content", $data_array);
            echo $demos_content;
            unset($ratingpic);
            $n++;
        }
        $demos_foot = $GLOBALS["_template"]->replaceTemplate("demos_foot", array());
        echo $demos_foot;
    } else {
        echo generateAlert($_language->module[ 'no_demos' ], 'alert-info');
    }
}
