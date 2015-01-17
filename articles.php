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

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "save") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('articles');

    if (!isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    $title = $_POST[ 'title' ];
    $message = $_POST[ 'message' ];
    $link1 = $_POST[ 'link1' ];
    $url1 = $_POST[ 'url1' ];
    $window1 = $_POST[ 'window1' ];
    $link2 = $_POST[ 'link2' ];
    $url2 = $_POST[ 'url2' ];
    $window2 = $_POST[ 'window2' ];
    $link3 = $_POST[ 'link3' ];
    $url3 = $_POST[ 'url3' ];
    $window3 = $_POST[ 'window3' ];
    $link4 = $_POST[ 'link4' ];
    $url4 = $_POST[ 'url4' ];
    $window4 = $_POST[ 'window4' ];
    $comments = $_POST[ 'comments' ];
    $articlesID = $_POST[ 'articlesID' ];

    safe_query(
        "UPDATE
            " . PREFIX . "articles
        SET
            title='" . $title . "',
            link1='" . $link1 . "',
            url1='" . $url1 . "',
            window1='" . $window1 . "',
            link2='" . $link2 . "',
            url2='" . $url2 . "',
            window2='" . $window2 . "',
            link3='" . $link3 . "',
            url3='" . $url3 . "',
            window3='" . $window3 . "',
            link4='" . $link4 . "',
            url4='" . $url4 . "',
            window4='" . $window4 . "',
            saved='1',
            comments='" . $comments . "'
        WHERE
            articlesID='" . (int)$articlesID."'"
    );

    \webspell\Tags::setTags('articles', $articlesID, $_POST[ 'tags' ]);

    $anzpages =
        mysqli_num_rows(
            safe_query(
                "SELECT * FROM " . PREFIX . "articles_contents WHERE articlesID='" . (int)$articlesID ."'"
            )
        );
    if ($anzpages > count($message)) {
        safe_query(
            "DELETE FROM
                `" . PREFIX . "articles_contents`
            WHERE
                `articlesID` = '" . (int)$articlesID . "' AND
                `page` > " . count($message)
        );
    }

    $counter = count($message);
    for ($i = 0; $i <= $counter; $i++) {
        if (isset($message[ $i ])) {
            if ($i >= $anzpages) {
                safe_query(
                    "INSERT INTO
                        " . PREFIX . "articles_contents (
                            `articlesID`,
                            `content`,
                            `page`
                        )
                        VALUES (
                            '" . $articlesID . "',
                            '" . $message[ $i ] . "',
                            '" . $i . "'
                        )"
                );
            } else {
                safe_query(
                    "UPDATE
                        `" . PREFIX . "articles_contents`
                    SET
                        `content` = '" . $message[ $i ] . "'
                    WHERE
                        `articlesID` = '" . $articlesID . "' AND
                        `page` = '" . (int)$i."'"
                );
            }
        }
    }
    for ($x = $_POST[ 'language_count' ]; $x < 100; $x++) {
        safe_query(
            "DELETE FROM
                `" . PREFIX . "articles_contents`
            WHERE
                `articlesID` = '" . $articlesID . "' AND
                `page` = '" . (int)$x."'"
        );
    }

    // delete the entries that are older than 2 hour and contain no text
    safe_query(
        "DELETE FROM
            `" . PREFIX . "articles`
        WHERE
            `saved` = '0' AND
            " . time() . " - `date` > " . (2 * 60 * 60)
    );

    die('<body onload="window.close()"></body>');
} elseif (isset($_GET[ 'delete' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('articles');

    if (!isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                `screens`
            FROM
                `" . PREFIX . "articles`
            WHERE
                `articlesID` = '" . (int)$_GET[ 'articlesID' ] . "'"
        )
    );
    if ($ds[ 'screens' ]) {
        $screens = explode("|", $ds[ 'screens' ]);
        if (is_array($screens)) {
            $filepath = "./images/articles-pics/";
            foreach ($screens as $screen) {
                if (file_exists($filepath . $screen)) {
                    @unlink($filepath . $screen);
                }
            }
        }
    }

    \webspell\Tags::removeTags('articles', $_GET[ 'articlesID' ]);

    safe_query("DELETE FROM " . PREFIX . "articles WHERE articlesID='" . (int)$_GET[ 'articlesID' ] . "'");
    safe_query("DELETE FROM " . PREFIX . "articles_contents WHERE articlesID='" . (int)$_GET[ 'articlesID' ] . "'");
    safe_query("DELETE FROM " . PREFIX . "comments WHERE parentID='" . (int)$_GET[ 'articlesID' ] . "' AND type='ar'");

    if (isset($close)) {
        echo '<body onload="window.close()"></body>';
    } else {
        header("Location: index.php?site=articles");
    }
}

function top5()
{
    global $_language;

    $_language->readModule('articles');

    // RATING
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "articles WHERE saved='1' ORDER BY rating DESC LIMIT 0,5");
    $top = $_language->module[ 'top5_rating' ];
    echo '<div class="row">';

    $data_array = array();
    $data_array['$top'] = $top;
    $top5_head = $GLOBALS["_template"]->replaceTemplate("top5_head", $data_array);
    echo $top5_head;

    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($n % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        $title = '<a href="index.php?site=articles&amp;action=show&amp;articlesID=' . $ds[ 'articlesID' ] . '">' .
            clearfromtags($ds[ 'title' ]) . '</a>';
        $poster =
            '<a href="index.php?site=profile&amp;id=' . $ds[ 'poster' ] . '">' . getnickname($ds[ 'poster' ]) . '</a>';
        $viewed = '(' . $ds[ 'viewed' ] . ')';
        $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
            $ratings[ $i ] = 1;
        }
        $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
        foreach ($ratings as $pic) {
            $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
        }

        echo '<li class="list-group-item"><span class="badge">' . $ratingpic . '</span>' . $n . ' ' . $title . '</li>';

        unset($ratingpic);
        $n++;
    }

    echo '</ul></div>';

    // POINTS
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "articles WHERE saved='1' ORDER BY points DESC LIMIT 0,5");
    $top = $_language->module[ 'top5_points' ];

    $data_array = array();
    $data_array['$top'] = $top;
    $top5_head = $GLOBALS["_template"]->replaceTemplate("top5_head", $data_array);
    echo $top5_head;

    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($n % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        $title = '<a href="index.php?site=articles&amp;action=show&amp;articlesID=' . $ds[ 'articlesID' ] . '">' .
            clearfromtags($ds[ 'title' ]) . '</a>';
        $viewed = '(' . $ds[ 'viewed' ] . ')';
        echo '<li class="list-group-item"><span class="badge">' . $ds[ 'points' ] . '</span>' . $n . ' ' . $title .
            '</li>';

        $n++;
    }
    echo '</ul></div></div>';
}

if ($action == "new") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");

    $_language->readModule('articles');
    $_language->readModule('bbcode', true);

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    if (isnewsadmin($userID)) {
        safe_query(
            "INSERT INTO
                " . PREFIX . "articles (
                    date,
                    poster,
                    saved
                )
                VALUES(
                    '" . time() . "',
                    '$userID',
                    '0'
                )"
        );
        $articlesID = mysqli_insert_id($_database);

        $selects = '';
        for ($i = 1; $i < 100; $i++) {
            $selects .= '<option value="' . $i . '">' . $i . '</option>';
        }

        $tags = '';

        $title = '';

        $pages = 1;

        $componentsCss = generateComponents($components['css'], 'css');
        $componentsJs = generateComponents($components['js'], 'js');

        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $addflags = $GLOBALS["_template"]->replaceTemplate("flags", array());

        $data_array = array();
        $data_array['$rewriteBase'] = $rewriteBase;
        $data_array['$componentsCss'] = $componentsCss;
        $data_array['$addbbcode'] = $addbbcode;
        $data_array['$addflags'] = $addflags;
        $data_array['$articlesID'] = $articlesID;
        $data_array['$title'] = $title;
        $data_array['$componentsJs'] = $componentsJs;
        $articles_post = $GLOBALS["_template"]->replaceTemplate("articles_post", $data_array);
        echo $articles_post;
    } else {
        redirect('index.php?site=articles', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "edit") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");

    $_language->readModule('articles');
    $_language->readModule('bbcode', true);

    $articlesID = $_GET[ 'articlesID' ];

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    if (isnewsadmin($userID)) {
        $ds = mysqli_fetch_array(
            safe_query("SELECT * FROM " . PREFIX . "articles WHERE articlesID = '" . (int)$articlesID . "'")
        );

        $title = getinput($ds[ 'title' ]);

        $message = array();
        $query = safe_query(
            "SELECT
                `content`
            FROM
                `" . PREFIX . "articles_contents`
            WHERE
                `articlesID` = '" . $articlesID . "'
            ORDER BY
                `page` ASC"
        );
        while ($qs = mysqli_fetch_array($query)) {
            $message[ ] = $qs[ 'content' ];
        }

        $message_vars = '';
        $i = 0;
        foreach ($message as $val) {
            $message_vars .= "message[" . $i . "] = '" . js_replace($val) . "';\n";
            $i++;
        }
        $pages = count($message);

        $selects = '';
        for ($i = 1; $i < 100; $i++) {
            if ($i == $pages) {
                $selected = "selected='selected'";
            } else {
                $selected = null;
            }
            $selects .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
        }

        $link1 = getinput($ds[ 'link1' ]);
        $link2 = getinput($ds[ 'link2' ]);
        $link3 = getinput($ds[ 'link3' ]);
        $link4 = getinput($ds[ 'link4' ]);
        $url1 = getinput($ds[ 'url1' ]);
        $url2 = getinput($ds[ 'url2' ]);
        $url3 = getinput($ds[ 'url3' ]);
        $url4 = getinput($ds[ 'url4' ]);

        if ($ds[ 'window1' ]) {
            $window1 = '<input class="input" name="window1" type="radio" value="1" checked="checked"> ' .
                $_language->module[ 'new_window' ] . ' <input class="input" type="radio" name="window1" value="0"> ' .
                $_language->module[ 'self' ] . '';
        } else {
            $window1 =
                '<input class="input" name="window1" type="radio" value="1"> ' . $_language->module[ 'new_window' ] .
                ' <input class="input" type="radio" name="window1" value="0" checked="checked"> ' .
                $_language->module[ 'self' ] . '';
        }

        if ($ds[ 'window2' ]) {
            $window2 = '<input class="input" name="window2" type="radio" value="1" checked="checked"> ' .
                $_language->module[ 'new_window' ] . ' <input class="input" type="radio" name="window2" value="0"> ' .
                $_language->module[ 'self' ] . '';
        } else {
            $window2 =
                '<input class="input" name="window2" type="radio" value="1"> ' . $_language->module[ 'new_window' ] .
                ' <input class="input" type="radio" name="window2" value="0" checked="checked"> ' .
                $_language->module[ 'self' ] . '';
        }

        if ($ds[ 'window3' ]) {
            $window3 = '<input class="input" name="window3" type="radio" value="1" checked="checked"> ' .
                $_language->module[ 'new_window' ] . ' <input class="input" type="radio" name="window3" value="0"> ' .
                $_language->module[ 'self' ] . '';
        } else {
            $window3 =
                '<input class="input" name="window3" type="radio" value="1"> ' . $_language->module[ 'new_window' ] .
                ' <input class="input" type="radio" name="window3" value="0" checked="checked"> ' .
                $_language->module[ 'self' ] . '';
        }

        if ($ds[ 'window4' ]) {
            $window4 = '<input class="input" name="window4" type="radio" value="1" checked="checked"> ' .
                $_language->module[ 'new_window' ] . ' <input class="input" type="radio" name="window4" value="0"> ' .
                $_language->module[ 'self' ] . '';
        } else {
            $window4 =
                '<input class="input" name="window4" type="radio" value="1"> ' . $_language->module[ 'new_window' ] .
                ' <input class="input" type="radio" name="window4" value="0" checked="checked"> ' .
                $_language->module[ 'self' ] . '';
        }

        $tags = \webspell\Tags::getTags('articles', $articlesID);

        $comments = '<option value="0">' . $_language->module[ 'no_comments' ] . '</option><option value="1">' .
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

        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $addflags = $GLOBALS["_template"]->replaceTemplate("flags", array());

        $data_array = array();
        $data_array['$rewriteBase'] = $rewriteBase;
        $data_array['$componentsCss'] = $componentsCss;
        $data_array['$message_vars'] = $message_vars;
        $data_array['$addbbcode'] = $addbbcode;
        $data_array['$addflags'] = $addflags;
        $data_array['$link1'] = $link1;
        $data_array['$url1'] = $url1;
        $data_array['$window1'] = $window1;
        $data_array['$link2'] = $link2;
        $data_array['$url2'] = $url2;
        $data_array['$window2'] = $window2;
        $data_array['$link3'] = $link3;
        $data_array['$url3'] = $url3;
        $data_array['$window3'] = $window3;
        $data_array['$link4'] = $link4;
        $data_array['$url4'] = $url4;
        $data_array['$window4'] = $window4;
        $data_array['$articlesID'] = $articlesID;
        $data_array['$userID'] = $userID;
        $data_array['$title'] = $title;
        $data_array['$pages'] = $pages;
        $data_array['$componentsJs'] = $componentsJs;
        $articles_edit = $GLOBALS["_template"]->replaceTemplate("articles_edit", $data_array);
        echo $articles_edit;
    } else {
        redirect('index.php?site=articles', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "show") {
    $_language->readModule('articles');

    $articlesID = (int)$_GET[ 'articlesID' ];
    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }

    if (isnewsadmin($userID)) {
        echo
            '<input type="button" onclick="window.open(
                \'articles.php?action=new\',
                \'Articles\',
                \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
                );" value="' . $_language->module[ 'new_article' ] . '" class="btn btn-danger"> ';
    }
    echo
        '<a href="index.php?site=articles" class="btn btn-primary">' .
            $_language->module[ 'all_articles' ] .
        '</a><br><br>';

    if ($page == 1) {
        safe_query(
            "UPDATE `" . PREFIX . "articles`
            SET `viewed`=viewed+1
            WHERE `articlesID` = '" . (int)$articlesID."'"
        );
    }
    $result = safe_query("SELECT * FROM `" . PREFIX . "articles` WHERE `articlesID` = '" . (int)$articlesID . "'");

    if (mysqli_num_rows($result)) {
        $ds = mysqli_fetch_array($result);
        $date_time = getformatdatetime($ds[ 'date' ]);
        $title = clearfromtags($ds[ 'title' ]);

        $content = array();
        $query = safe_query(
            "SELECT
                *
            FROM
                `" . PREFIX . "articles_contents`
            WHERE
                `articlesID` = '" . (int)$articlesID . "'
            ORDER BY
                `page` ASC"
        );
        while ($qs = mysqli_fetch_array($query)) {
            $content[ ] = $qs[ 'content' ];
        }

        $pages = count($content);
        $content = htmloutput($content[ $page - 1 ]);
        $content = toggle($content, $ds[ 'articlesID' ]);
        if ($pages > 1) {
            $page_link =
                makepagelink("index.php?site=articles&amp;action=show&amp;articlesID=$articlesID", $page, $pages);
        } else {
            $page_link = '';
        }

        $poster = '<a href="index.php?site=profile&amp;id=' . $ds[ 'poster' ] . '"><b>' . getnickname($ds[ 'poster' ]) .
            '</b></a>';
        $related = "";
        if ($ds[ 'link1' ] && $ds[ 'url1' ] != "http://" && $ds[ 'window1' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url1' ] . '" target="_blank">' . $ds[ 'link1' ] . '</a> ';
        }
        if ($ds[ 'link1' ] && $ds[ 'url1' ] != "http://" && !$ds[ 'window1' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url1' ] . '">' . $ds[ 'link1' ] . '</a> ';
        }

        if ($ds[ 'link2' ] && $ds[ 'url2' ] != "http://" && $ds[ 'window2' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url2' ] . '" target="_blank">' . $ds[ 'link2' ] . '</a> ';
        }
        if ($ds[ 'link2' ] && $ds[ 'url2' ] != "http://" && !$ds[ 'window2' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url2' ] . '">' . $ds[ 'link2' ] . '</a> ';
        }

        if ($ds[ 'link3' ] && $ds[ 'url3' ] != "http://" && $ds[ 'window3' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url3' ] . '" target="_blank">' . $ds[ 'link3' ] . '</a> ';
        }
        if ($ds[ 'link3' ] && $ds[ 'url3' ] != "http://" && !$ds[ 'window3' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url3' ] . '">' . $ds[ 'link3' ] . '</a> ';
        }

        if ($ds[ 'link4' ] && $ds[ 'url4' ] != "http://" && $ds[ 'window4' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url4' ] . '" target="_blank">' . $ds[ 'link4' ] . '</a> ';
        }
        if ($ds[ 'link4' ] && $ds[ 'url4' ] != "http://" && !$ds[ 'window4' ]) {
            $related .= '&#8226; <a href="' . $ds[ 'url4' ] . '">' . $ds[ 'link4' ] . '</a> ';
        }
        if (empty($related)) {
            $related = "n/a";
        }

        $comments_allowed = $ds[ 'comments' ];

        $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
            $ratings[ $i ] = 1;
        }
        $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
        foreach ($ratings as $pic) {
            $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
        }

        if (isnewsadmin($userID)) {
            $adminaction =
                '<br><br><input type="button" onclick="window.open(\'articles.php?action=edit&amp;articlesID=' .
                $ds[ 'articlesID' ] .
                '\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="' .
                $_language->module[ 'edit' ] . '" class="btn btn-danger">
    <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
                '\', \'articles.php?delete=true&amp;articlesID=' . $ds[ 'articlesID' ] . '\');" value="' .
                $_language->module[ 'delete' ] . '" class="btn btn-danger">';
        } else {
            $adminaction = '';
        }

        if ($loggedin) {
            $getarticles = safe_query("SELECT articles FROM " . PREFIX . "user WHERE userID='$userID'");
            $found = false;
            if (mysqli_num_rows($getarticles)) {
                $ga = mysqli_fetch_array($getarticles);
                if ($ga[ 'articles' ] != "") {
                    $string = $ga[ 'articles' ];
                    $array = explode(":", $string);
                    $anzarray = count($array);
                    for ($i = 0; $i < $anzarray; $i++) {
                        if ($array[ $i ] == $articlesID) {
                            $found = true;
                        }
                    }
                }
            }
            if ($found) {
                $rateform = $_language->module[ 'already_rated' ];
            } else {
                $rateform = '<form method="post" action="rating.php">
                <div class="input-group">
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
                        <input type="submit" name="Submit" value="' .
                        $_language->module[ 'rate' ] . '" class="btn btn-primary">
                    </span>
                </div>
                <input type="hidden" name="userID" value="' . $userID . '">
                <input type="hidden" name="type" value="ar">
                <input type="hidden" name="id" value="' . $ds[ 'articlesID' ] . '"></form>';
            }
        } else {
            $rateform = $_language->module[ 'login_for_rate' ];
        }

        $tags = \webspell\Tags::getTagsLinked('articles', $articlesID);

        $bg1 = BG_1;
        $data_array = array();
        $data_array['$title'] = $title;
        $data_array['$date'] = $date;
        $data_array['$content'] = $content;
        $data_array['$adminaction'] = $adminaction;
        $data_array['$poster'] = $poster;
        $data_array['$related'] = $related;
        $data_array['$ratingpic'] = $ratingpic;
        $data_array['$rateform'] = $rateform;
        $articles = $GLOBALS["_template"]->replaceTemplate("articles", $data_array);
        echo $articles;

        unset($related);
        unset($comments);
        unset($lang);
        unset($ds);
        unset($ratingpic);
        unset($page);
        unset($pages);

        $parentID = $articlesID;
        $type = "ar";
        $referer = "index.php?site=articles&amp;action=show&amp;articlesID=$articlesID";

        include("comments.php");
    } else {
        echo $_language->module[ 'no_entries' ];
    }
} else {
    $_language->readModule('articles');

    if (isset($_GET[ 'page' ])) {
        $page = (int)$_GET[ 'page' ];
    } else {
        $page = 1;
    }
    $sort = "date";
    if (isset($_GET[ 'sort' ])) {
        if (($_GET[ 'sort' ] == 'date') || ($_GET[ 'sort' ] == 'poster') || ($_GET[ 'sort' ] == 'rating')
            || ($_GET[ 'sort' ] == 'viewed')
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

    $title_articles = $GLOBALS["_template"]->replaceTemplate("title_articles", array());
    echo $title_articles;

    if (isnewsadmin($userID)) {
        echo
            '<p><input type="button" onclick="window.open(
                \'articles.php?action=new\',
                \'Articles\',
                \'toolbar=no,status=no,scrollbars=yes,width=800,height=600\'
            );" value="' . $_language->module[ 'new_article' ] . '" class="btn btn-danger"></p>';
    }

    $alle = safe_query("SELECT `articlesID` FROM `" . PREFIX . "articles` WHERE `saved`='1'");
    $gesamt = mysqli_num_rows($alle);
    $pages = 1;

    $max = $maxarticles;

    for ($n = $max; $n <= $gesamt; $n += $max) {
        if ($gesamt > $n) {
            $pages++;
        }
    }

    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=articles&amp;sort=" . $sort . "&amp;type=" . $type, $page, $pages);
    } else {
        $page_link = '';
    }

    if ($page == "1") {
        $ergebnis =
            safe_query(
                "SELECT
                    *
                FROM
                    `" . PREFIX . "articles`
                WHERE
                    `saved`='1'
                ORDER BY
                    $sort $type
                LIMIT 0,".(int)$max
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
                    `" . PREFIX . "articles`
                WHERE
                    `saved` = '1'
                ORDER BY
                    $sort $type
                LIMIT $start,".(int)$max
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
            echo '<a href="index.php?site=articles&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
                $_language->module[ 'sort' ] .
                '</a> <span class="glyphicon glyphicon-chevron-down"></span>&nbsp;&nbsp;&nbsp;';
        } else {
            echo '<a href="index.php?site=articles&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
                $_language->module[ 'sort' ] .
                '</a> <span class="glyphicon glyphicon-chevron-up"></span>&nbsp;&nbsp;&nbsp;';
        }

        if ($pages > 1) {
            echo $page_link;
        }

        $data_array = array();
        $data_array['$page'] = $page;
        $articles_head = $GLOBALS["_template"]->replaceTemplate("articles_head", $data_array);
        echo $articles_head;

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

            $title = '<a href="index.php?site=articles&amp;action=show&amp;articlesID=' . $ds[ 'articlesID' ] . '">' .
                clearfromtags($ds[ 'title' ]) . '</a>';
            $poster =
                '<a href="index.php?site=profile&amp;id=' . $ds[ 'poster' ] . '"><b>' . getnickname($ds[ 'poster' ]) .
                '</b></a>';
            $viewed = $ds[ 'viewed' ];

            $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
            }

            $data_array = array();
            $data_array['$date'] = $date;
            $data_array['$title'] = $title;
            $data_array['$poster'] = $poster;
            $data_array['$ratingpic'] = $ratingpic;
            $data_array['$viewed'] = $viewed;
            $articles_content = $GLOBALS["_template"]->replaceTemplate("articles_content", $data_array);
            echo $articles_content;
            unset($ratingpic);
            $n++;
        }
        $articles_foot = $GLOBALS["_template"]->replaceTemplate("articles_foot", array());
        echo $articles_foot;
        unset($ds);
    } else {
        echo $_language->module[ 'no_entries' ];
    }
}
