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
    $_language->readModule('awards');
}

if (isset($_POST[ 'save' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('awards');

    if (!isclanwaradmin($userID) && !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $date = strtotime($_POST['date']);

    if (isset($_POST[ 'squad' ])) {
        $squad = $_POST[ 'squad' ];
    } else {
        $squad = 0;
    }
    $award = $_POST[ 'award' ];
    $homepage = $_POST[ 'homepage' ];
    $rang = $_POST[ 'rang' ];
    $info = $_POST[ 'message' ];

    safe_query(
        "INSERT INTO
            `" . PREFIX . "awards` (
                `date`,
                `squadID`,
                `award`,
                `homepage`,
                `rang`,
                `info`
            )
            VALUES(
                '$date',
                '$squad',
                '$award',
                '$homepage',
                '$rang',
                '$info'
            )"
    );
    header("Location: index.php?site=awards");
} elseif (isset($_POST[ 'saveedit' ])) {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('awards');

    if (!isclanwaradmin($userID) && !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    $awardID = $_POST[ 'awardID' ];
    $date = strtotime($_POST['date']);
    if (isset($_POST[ 'squad' ])) {
        $squad = $_POST[ 'squad' ];
    } else {
        $squad = 0;
    }
    $award = $_POST[ 'award' ];
    $homepage = $_POST[ 'homepage' ];
    $rang = $_POST[ 'rang' ];
    $info = $_POST[ 'message' ];

    safe_query(
        "UPDATE
            `" . PREFIX . "awards`
        SET
            `date` = '$date',
            `squadID` = '$squad',
            `award` = '$award',
            `homepage` = '$homepage',
            `rang` = '$rang',
            `info` = '$info'
        WHERE
            awardID = '". (int)$awardID."'"
    );
    header("Location: index.php?site=awards");
} elseif (isset($_GET[ 'delete' ])) {
    if (!isclanwaradmin($userID) && !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $awardID = $_GET[ 'awardID' ];
    safe_query("DELETE FROM `" . PREFIX . "awards` WHERE `awardID` = '" . (int)$awardID . "'");
    header("Location: index.php?site=awards");
}

eval ("\$title_awards = \"" . gettemplate("title_awards") . "\";");
echo $title_awards;

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = "";
}

if ($action == "new") {
    if (isclanwaradmin($userID) || isnewsadmin($userID)) {
        $_language->readModule('bbcode', true);
        $day = "";
        for ($i = 1; $i < 32; $i++) {
            if ($i == date("d", time())) {
                $day .= '<option selected="selected">' . $i . '</option>';
            } else {
                $day .= '<option>' . $i . '</option>';
            }
        }
        $month = "";
        for ($i = 1; $i < 13; $i++) {
            if ($i == date("n", time())) {
                $month .= '<option value="' . $i . '" selected="selected">' . date("M", time()) . '</option>';
            } else {
                $month .= '<option value="' . $i . '">' . date("M", mktime(0, 0, 0, $i, 1, 2000)) . '</option>';
            }
        }
        $year = "";
        for ($i = 2000; $i < 2016; $i++) {
            if ($i == date("Y", time())) {
                $year .= '<option value="' . $i . '" selected="selected">' . date("Y", time()) . '</option>';
            } else {
                $year .= '<option value="' . $i . '">' . $i . '</option>';
            }
        }
        $squads = getgamesquads();

        $bg1 = BG_1;

        eval ("\$addbbcode = \"" . gettemplate("addbbcode") . "\";");
        eval ("\$addflags = \"" . gettemplate("flags") . "\";");
        eval ("\$awards_new = \"" . gettemplate("awards_new") . "\";");
        echo $awards_new;
    } else {
        redirect('index.php?site=awards', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "edit") {
    $awardID = $_GET[ 'awardID' ];
    if (isclanwaradmin($userID) || isnewsadmin($userID)) {
        $_language->readModule('bbcode', true);
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT * FROM " . PREFIX . "awards WHERE awardID='" . (int)$awardID . "'"
            )
        );
        $day = "";
        for ($i = 1; $i < 32; $i++) {
            if ($i == date("d", $ds[ 'date' ])) {
                $day .= $i;
            }
        }
        $month = "";
        for ($i = 1; $i < 13; $i++) {
            if ($i == date("n", $ds[ 'date' ])) {
                $month .= date("m", $ds[ 'date' ]);
            }
        }
        $year = "";
        for ($i = 2000; $i < 2016; $i++) {
            if ($i == date("Y", $ds[ 'date' ])) {
                $year .= $i;
            }
        }

        $date = $year . '-' . $month . '-' . $day;
        $squads = getgamesquads();
        $squads = str_replace(
            'value="' . $ds[ 'squadID' ] . '"',
            'value="' . $ds[ 'squadID' ] . '" selected="selected"',
            $squads
        );
        $award = htmlspecialchars($ds[ 'award' ]);
        $homepage = htmlspecialchars($ds[ 'homepage' ]);
        $rang = $ds[ 'rang' ];
        $info = htmlspecialchars($ds[ 'info' ]);
        $bg1 = BG_1;
        eval ("\$addbbcode = \"" . gettemplate("addbbcode") . "\";");
        eval ("\$addflags = \"" . gettemplate("flags") . "\";");
        eval ("\$awards_edit = \"" . gettemplate("awards_edit") . "\";");
        echo $awards_edit;
    } else {
        redirect('index.php?site=awards', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "showsquad") {
    $squadID = $_GET[ 'squadID' ];
    $page = (isset($_GET[ 'page' ])) ? $_GET[ 'page' ] : 1;
    $sort = (isset($_GET[ 'page' ])) ? $_GET[ 'page' ] : "date";
    $type = (isset($_GET[ 'type' ])) ? $_GET[ 'type' ] : "DESC";

    if (isclanwaradmin($userID) || isnewsadmin($userID)) {
        echo
            '<a href="index.php?site=awards&amp;action=new" class="btn btn-danger">' .
            $_language->module[ 'new_award' ] . '</a><br><br>';
    }
    $alle = safe_query("SELECT awardID FROM " . PREFIX . "awards WHERE squadID='$squadID'");
    $gesamt = mysqli_num_rows($alle);
    $pages = 1;
    $max = $maxawards;

    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link =
            makepagelink(
                "index.php?site=awards&amp;action=showsquad&amp;squadID=$squadID&amp;sort=$sort&amp;type=$type",
                $page,
                $pages
            );
    } else {
        $page_link = "";
    }
    if ($page == "1") {
        $ergebnis =
            safe_query(
                "SELECT * FROM `" . PREFIX . "awards` WHERE `squadID` = '$squadID' ORDER BY $sort $type LIMIT 0,$max"
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
                `" . PREFIX . "awards`
            WHERE
                `squadID` = '$squadID'
            ORDER BY
                $sort $type
            LIMIT $start,$max"
        );
        if ($type == "DESC") {
            $n = ($gesamt) - $page * $max + $max;
        } else {
            $n = ($gesamt + 1) - $page * $max + $max;
        }
    }
    if ($gesamt) {
        if ($type == "ASC") {
            echo '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID . '&amp;page=' . $page .
                '&amp;sort=' . $sort . '&amp;type=DESC">' . $_language->module[ 'sort' ] .
                ':</a> <span class="glyphicon glyphicon-chevron-down"></span>&nbsp;&nbsp;&nbsp;';
        } else {
            echo '<a href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID . '&amp;page=' . $page .
                '&amp;sort=' . $sort . '&amp;type=ASC">' . $_language->module[ 'sort' ] .
                ':</a> <span class="glyphicon glyphicon-chevron-up"></span>&nbsp;&nbsp;&nbsp;';
        }

        echo $page_link;
        echo '<br><br>';
        $headdate = '<a class="titlelink" href="index.php?site=awards&amp;action=showsquad&amp;squadID=' . $squadID .
            '&amp;page=' . $page . '&amp;sort=date&amp;type=' . $type . '">' . $_language->module[ 'date' ] . ':</a>';
        $headsquad = $_language->module[ 'squad' ] . ':';

        eval ("\$awards_head = \"" . gettemplate("awards_head") . "\";");
        echo $awards_head;
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
            $squad = getsquadname($ds[ 'squadID' ]);
            $award = cleartext($ds[ 'award' ]);
            $homepage = $ds[ 'homepage' ];
            $rang = $ds[ 'rang' ];

            if (isclanwaradmin($userID) || isnewsadmin($userID)) {
                $adminaction =
                    '<a href="index.php?site=awards&amp;action=edit&amp;awardID=' .
                    $ds[ 'awardID' ] .'" class="btn btn-danger">'. $_language->module[ 'edit' ] . '</a>
            <input type="button" onclick="MM_confirm(
                \'really delete this award?\',
                \'awards.php?delete=true&amp;awardID=' . $ds[ 'awardID' ] . '\'
            )" value="' . $_language->module[ 'delete' ] . '">';
            } else {
                $adminaction = '';
            }

            eval ("\$awards_content = \"" . gettemplate("awards_content") . "\";");
            echo $awards_content;

            unset($result);
            $n++;
        }
        eval ("\$awards_foot = \"" . gettemplate("awards_foot") . "\";");
        echo $awards_foot;
    } else {
        echo $_language->module[ 'no_entries' ];
    }
} elseif ($action == "details") {
    $awardID = $_GET[ 'awardID' ];
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                *
            FROM
                `" . PREFIX . "awards`
            WHERE
                awardID='" . (int)$awardID . "'"
        )
    );

    $rang = $ds[ 'rang' ];
    if ($rang == '') {
        $rang = "-";
    }
    $award = cleartext($ds[ 'award' ]);
    if ($award == '') {
        $award = "-";
    }
    $squad = getsquadname($ds[ 'squadID' ]);
    $squadID = $ds[ 'squadID' ];
    $date = getformatdate($ds[ 'date' ]);
    $info = htmloutput($ds[ 'info' ]);
    if ($info == '') {
        $info = "-";
    }
    $homepage = '<a href="http://' . getinput(
        str_replace(
            'http://',
            '',
            $ds[ 'homepage' ]
        )
    )
    . '" target="_blank">' . $ds[ 'homepage' ] . '</a>';

    $bg1 = BG_1;
    $bg2 = BG_2;
    $bg3 = BG_3;
    $bg4 = BG_4;

    if (isclanwaradmin($userID) || isnewsadmin($userID)) {
        $adminaction =
            '<br><a href="index.php?site=awards&amp;action=edit&amp;awardID=' . $ds[ 'awardID' ] .
            '" class="btn btn-danger">' . $_language->module[ 'edit' ] . '</a>
            <input type="button" onclick="MM_confirm(
                    \'really delete this award?\',
                    \'awards.php?delete=true&amp;awardID=' . $ds[ 'awardID' ] . '\'
                )" value="' . $_language->module[ 'delete' ] . '">';
    } else {
        $adminaction = '';
    }

    eval ("\$awards_info = \"" . gettemplate("awards_info") . "\";");
    echo $awards_info;
} else {
    $page = (isset($_GET[ 'page' ])) ? (int)$_GET[ 'page' ] : 1;
    $sort = (isset($_GET[ 'sort' ]) && $_GET[ 'sort' ] == 'squadID') ? "squadID" : "date";
    $type = (isset($_GET[ 'type' ]) && $_GET[ 'type' ] == 'ASC') ? "ASC" : "DESC";

    if (isclanwaradmin($userID) || isnewsadmin($userID)) {
        echo '<a href="index.php?site=awards&amp;action=new" class="btn btn-danger">' .
            $_language->module[ 'new_award' ] . '</a><br><br>';
    }

    $alle = safe_query("SELECT awardID FROM " . PREFIX . "awards");
    $gesamt = mysqli_num_rows($alle);
    $pages = 1;
    $max = $maxawards;
    $pages = ceil($gesamt / $max);

    if ($pages > 1) {
        $page_link = makepagelink("index.php?site=awards&sort=$sort&type=$type", $page, $pages);
    } else {
        $page_link = '';
    }

    if ($page == "1") {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "awards ORDER BY $sort $type LIMIT 0,$max");
        if ($type == "DESC") {
            $n = $gesamt;
        } else {
            $n = 1;
        }
    } else {
        $start = $page * $max - $max;
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "awards ORDER BY $sort $type LIMIT $start,$max");
        if ($type == "DESC") {
            $n = ($gesamt) - $page * $max + $max;
        } else {
            $n = ($gesamt + 1) - $page * $max + $max;
        }
    }
    if ($gesamt) {
        if ($type == "ASC") {
            echo '<a href="index.php?site=awards&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=DESC">' .
                $_language->module[ 'sort' ] . ':</a> <span class="glyphicon glyphicon-chevron-down"></span>';
        } else {
            echo '<a href="index.php?site=awards&amp;page=' . $page . '&amp;sort=' . $sort . '&amp;type=ASC">' .
                $_language->module[ 'sort' ] . ':</a> <span class="glyphicon glyphicon-chevron-up"></span>';
        }

        echo $page_link;
        echo '<br><br>';
        $headdate =
            '<a class="titlelink" href="index.php?site=awards&amp;page=' . $page . '&amp;sort=date&amp;type=' . $type .
            '">' . $_language->module[ 'date' ] . ':</a>';
        $headsquad =
            '<a class="titlelink" href="index.php?site=awards&amp;page=' . $page . '&amp;sort=squadID&amp;type=' .
            $type . '">' . $_language->module[ 'squad' ] . ':</a>';

        eval ("\$awards_head = \"" . gettemplate("awards_head") . "\";");
        echo $awards_head;

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
                '<a href="index.php?site=members&amp;action=showsquad&amp;squadID=' . $ds[ 'squadID' ] . '&amp;page=' .
                $page . '&amp;sort=' . $sort . '&amp;type=' . $type . '">' . getsquadname($ds[ 'squadID' ]) . '</a>';
            $award = cleartext($ds[ 'award' ]);
            $homepage = $ds[ 'homepage' ];
            $rang = $ds[ 'rang' ];

            if (isclanwaradmin($userID) || isnewsadmin($userID)) {
                $adminaction =
                    '<a href="index.php?site=awards&amp;action=edit&amp;awardID=' . $ds[ 'awardID' ] .
                    '" class="btn btn-danger">' . $_language->module[ 'edit' ] . '</a>
                    <input type="button" class="btn btn-danger" onclick="MM_confirm(
                            \'really delete this award?\',
                            \'index.php?site=awards&amp;delete=true&amp;awardID=' . $ds[ 'awardID' ] . '\'
                        )" value="' . $_language->module[ 'delete' ] . '">';
            } else {
                $adminaction = '';
            }

            eval ("\$awards_content = \"" . gettemplate("awards_content") . "\";");
            echo $awards_content;
            $n++;
        }
        eval ("\$awards_foot = \"" . gettemplate("awards_foot") . "\";");
        echo $awards_foot;
    } else {
        echo $_language->module[ 'no_entries' ];
    }
}
