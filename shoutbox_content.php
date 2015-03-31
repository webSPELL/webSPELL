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
    $action = null;
}
if ($action == "save") {
    $message = trim($_POST[ 'message' ]);
    $name = trim($_POST[ 'name' ]);
    $run = 0;
    if ($userID) {
        $run = 1;
        $name = $_database->escape_string(getnickname($userID));
    } else {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha($_POST[ 'captcha' ], $_POST[ 'captcha_hash' ])) {
            $run = 1;
        }

        if (mysqli_num_rows(safe_query("SELECT * FROM " . PREFIX . "user WHERE nickname = '$name' "))) {
            $name = '*' . $name . '*';
        }
        $name = clearfromtags($name);
    }

    if (!empty($name) && !empty($message) && $run) {
        $date = time();
        $ip = $GLOBALS[ 'ip' ];
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "shoutbox ORDER BY date DESC LIMIT 0,1");
        $ds = mysqli_fetch_array($ergebnis);
        if (
            ($ds[ 'message' ] != $message) ||
            ($ds[ 'name' ] != $name)
        ) {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "shoutbox (
                        `date`,
                        `name`,
                        `message`,
                        `ip`
                    )
                VALUES (
                    '$date',
                    '$name',
                    '$message',
                    '$ip'
                ) "
            );
        }
    }
    redirect('index.php?site=shoutbox_content&action=showall', 'shoutbox', 0);
} elseif ($action == "delete") {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    if (!isfeedbackadmin($userID)) {
        die('No access.');
    }
    if (isset($_POST[ 'shoutID' ])) {
        if (!is_array($_POST[ 'shoutID' ])) {
            $_POST[ 'shoutID' ] = array($_POST[ 'shoutID' ]);
        }
        foreach ($_POST[ 'shoutID' ] as $id) {
            safe_query("DELETE FROM " . PREFIX . "shoutbox WHERE shoutID='".(int)$id."'");
        }
    }
    header("Location: index.php?site=shoutbox_content&action=showall");
} elseif ($action == "showall") {
    $_language->readModule('shoutbox');
    $title_shoutbox = $GLOBALS["_template"]->replaceTemplate("title_shoutbox", array());
    echo $title_shoutbox;

    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(shoutID) as cnt FROM " . PREFIX . "shoutbox ORDER BY date"));
    $gesamt = $tmp[ 'cnt' ];
    $pages = ceil($gesamt / $maxsball);
    $max = $maxsball;
    if (!isset($_GET[ 'page' ])) {
        $page = 1;
    } else {
        $page = (int)$_GET[ 'page' ];
    }
    $type = 'DESC';
    if (isset($_GET[ 'type' ])) {
        if ($_GET[ 'type' ] == 'ASC') {
            $type = 'ASC';
        }
    }

    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=shoutbox_content&amp;action=showall&amp;type=$type", $page, $pages);
    } else {
        $page_link = '';
    }

    if ($page == "1") {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "shoutbox ORDER BY date $type LIMIT 0,$max");
        if ($type == "DESC") {
            $n = $gesamt;
        } else {
            $n = 1;
        }
    } else {
        $start = $page * $max - $max;
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "shoutbox ORDER BY date $type LIMIT $start,$max");
        if ($type == "DESC") {
            $n = $gesamt - ($page - 1) * $max;
        } else {
            $n = ($page - 1) * $max + 1;
        }
    }

    if ($type == "ASC") {
        $sorter = '<a href="index.php?site=shoutbox_content&amp;action=showall&amp;page=' . $page . '&amp;type=DESC">' .
            $_language->module[ 'sort' ] . '</a> <span class="glyphicon glyphicon-chevron-down"></span>';
    } else {
        $sorter = '<a href="index.php?site=shoutbox_content&amp;action=showall&amp;page=' . $page . '&amp;type=ASC">' .
            $_language->module[ 'sort' ] . '</a> <span class="glyphicon glyphicon-chevron-up"></span>';
    }

    $data_array = array();
    $data_array['$sorter'] = $sorter;
    $data_array['$page_link'] = $page_link;
    $shoutbox_all_head = $GLOBALS["_template"]->replaceTemplate("shoutbox_all_head", $data_array);
    echo $shoutbox_all_head;

    $i = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $i % 2 ? $bg1 = BG_1 : $bg1 = BG_2;
        $date = getformatdatetime($ds[ 'date' ]);
        $name = $ds[ 'name' ];
        $message = cleartext($ds[ 'message' ], false);
        $ip = 'logged';

        if (isfeedbackadmin($userID)) {
            $actions = '<input class="input" type="checkbox" name="shoutID[]" value="' . $ds[ 'shoutID' ] . '">';
            $ip = $ds[ 'ip' ];
        } else {
            $actions = '';
        }

        $data_array = array();
        $data_array['$actions'] = $actions;
        $data_array['$n'] = $n;
        $data_array['$name'] = $name;
        $data_array['$date'] = $date;
        $data_array['$ip'] = $ip;
        $data_array['$message'] = $message;
        $shoutbox_all_content = $GLOBALS["_template"]->replaceTemplate("shoutbox_all_content", $data_array);
        echo $shoutbox_all_content;
        if ($type == "DESC") {
            $n--;
        } else {
            $n++;
        }
        $i++;
    }
    $shoutbox_all_foot = $GLOBALS["_template"]->replaceTemplate("shoutbox_all_foot", array());
    echo $shoutbox_all_foot;

    if (isfeedbackadmin($userID)) {
        $submit = '<input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);"> ' .
            $_language->module[ 'select_all' ] . '
                <input type="submit" value="' . $_language->module[ 'delete_selected' ] . '" class="btn btn-danger">';
    } else {
        $submit = '';
    }
    echo '<div class="row">
            <div class="col-md-6">' . $page_link . '</div>
            <div class="col-md-6 text-right">' . $submit . '</div>
        </div>
        </form>';

    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=shoutbox_content&amp;action=showall", $page, $pages);
    }
} elseif (basename($_SERVER[ 'PHP_SELF' ]) != "shoutbox_content.php") {
    redirect('index.php?site=shoutbox_content&action=showall', 'shoutbox', 0);
} else {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");

    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;
    $bg1 = BG_1;

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "shoutbox ORDER BY date DESC LIMIT 0," . $maxshoutbox);
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $date = getformattime($ds[ 'date' ]);
        $name = $ds[ 'name' ];
        $message = cleartext($ds[ 'message' ], false);
        $message = str_replace("&amp;amp;", "&", $message);

        $data_array = array();
        $data_array['$name'] = $name;
        $data_array['$date'] = $date;
        $data_array['$message'] = $message;
        $shoutbox_content = $GLOBALS["_template"]->replaceTemplate("shoutbox_content", $data_array);
        echo $shoutbox_content;
    }
}
