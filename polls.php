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
    $action = "";
}

if ($action == "vote") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    if (isset($_POST[ 'pollID' ]) && isset($_POST[ 'vote' ])) {
        $pollID = (int)$_POST[ 'pollID' ];
        $vote = (int)$_POST[ 'vote' ];
        $_language->readModule('polls');

        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT
                    `userIDs`,
                    `hosts`
                FROM
                    `" . PREFIX . "poll`
                WHERE
                    `pollID` = '" . (int)$pollID."'"
            )
        );
        $anz = mysqli_num_rows(
            safe_query(
                "SELECT
                    `pollID`
                FROM
                    `" . PREFIX . "poll`
                WHERE
                    pollID = '" . $pollID . "' AND
                    hosts LIKE '%" . $_SERVER[ 'REMOTE_ADDR' ] . "%' AND
                    intern<=" . (int)isclanmember($userID)
            )
        );

        $anz_user = false;
        if ($userID) {
            if ($ds[ 'userIDs' ]) {
                $user_ids = explode(";", $ds[ 'userIDs' ]);
                if (in_array($userID, $user_ids)) {
                    $anz_user = true;
                }
            } else {
                $user_ids = array();
            }
        }

        $cookie = false;
        if (isset($_COOKIE[ 'poll' ]) && is_array($_COOKIE[ 'poll' ])) {
            $cookie = in_array($pollID, $_COOKIE[ 'poll' ]);
        }
        if (!$cookie && !$anz && !$anz_user && isset($_POST[ 'vote' ])) {
            //write cookie
            $index = count($_COOKIE[ 'poll' ]);
            setcookie("poll[" . $index . "]", $pollID, time() + (3600 * 24 * 365));

            //write ip and userID if logged
            $add_query = "";
            if ($userID) {
                $user_ids[ ] = $userID;
                $add_query = ", userIDs='" . implode(";", $user_ids) . "'";
            }

            safe_query(
                "UPDATE
                    " . PREFIX . "poll
                SET
                    hosts='" . $ds[ 'hosts' ] . "#" . $_SERVER[ 'REMOTE_ADDR' ] . "#'" . $add_query . "
                WHERE
                    pollID='" . (int)$pollID."'"
            );

            //write vote
            safe_query(
                "UPDATE
                    " . PREFIX . "poll_votes
                SET
                    o" . $vote . " = o" . $vote . "+1
                WHERE
                    pollID='" . (int)$pollID."'"
            );
        }
        header('Location: index.php?site=polls');
    } else {
        header('Location: index.php?site=polls');
    }
} elseif (isset($_POST[ 'save' ])) {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    $_language->readModule('polls');

    if (isset($_POST[ 'intern' ])) {
        $intern = $_POST[ 'intern' ];
    } else {
        $intern = "";
    }

    if (!ispollsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $date_string = $_POST['runtime_time'].' '.$_POST['runtime_date'];
    $runtime = strtotime($date_string);

    safe_query(
        "INSERT INTO
            " . PREFIX . "poll (
                aktiv,
                titel,
                o1,
                o2,
                o3,
                o4,
                o5,
                o6,
                o7,
                o8,
                o9,
                o10,
                comments,
                laufzeit,
                intern
            )
        values(
            '1',
            '" . $_POST[ 'title' ] . "',
            '" . $_POST[ 'op1' ] . "',
            '" . $_POST[ 'op2' ] . "',
            '" . $_POST[ 'op3' ] . "',
            '" . $_POST[ 'op4' ] . "',
            '" . $_POST[ 'op5' ] . "',
            '" . $_POST[ 'op6' ] . "',
            '" . $_POST[ 'op7' ] . "',
            '" . $_POST[ 'op8' ] . "',
            '" . $_POST[ 'op9' ] . "',
            '" . $_POST[ 'op10' ] . "',
            '" . $_POST[ 'comments' ] . "',
            '" . $runtime . "',
            '" . $intern . "'
        )"
    );
    $id = mysqli_insert_id($_database);

    safe_query(
        "INSERT INTO
            " . PREFIX . "poll_votes (pollID, o1, o2, o3, o4, o5, o6, o7, o8, o9, o10)
            values( '$id', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0' )"
    );
    header('Location: index.php?site=polls');
} elseif (isset($_POST[ 'saveedit' ])) {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    $_language->readModule('polls');
    if (!ispollsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $pollID = $_POST[ 'pollID' ];
    if (isset($_POST[ 'intern' ])) {
        $intern = $_POST[ 'intern' ];
    } else {
        $intern = "";
    }

    $date_string = $_POST['runtime_time'].' '.$_POST['runtime_date'];
    $runtime = strtotime($date_string);

    if (isset($_POST[ 'reset' ])) {
        safe_query("DELETE FROM " . PREFIX . "poll WHERE pollID='$pollID'");
        safe_query("DELETE FROM " . PREFIX . "poll_votes WHERE pollID='$pollID'");

        safe_query(
            "INSERT INTO
                " . PREFIX . "poll (
                    aktiv,
                    titel,
                    o1,
                    o2,
                    o3,
                    o4,
                    o5,
                    o6,
                    o7,
                    o8,
                    o9,
                    o10,
                    comments,
                    laufzeit,
                    intern
                )
            values(
                '1',
                '" . $_POST[ 'title' ] . "',
                '" . $_POST[ 'op1' ] . "',
                '" . $_POST[ 'op2' ] . "',
                '" . $_POST[ 'op3' ] . "',
                '" . $_POST[ 'op4' ] . "',
                '" . $_POST[ 'op5' ] . "',
                '" . $_POST[ 'op6' ] . "',
                '" . $_POST[ 'op7' ] . "',
                '" . $_POST[ 'op8' ] . "',
                '" . $_POST[ 'op9' ] . "',
                '" . $_POST[ 'op10' ] . "',
                '" . $_POST[ 'comments' ] . "',
                '" . $runtime . "',
                '" . $intern . "'
            )"
        );
        $id = mysqli_insert_id($_database);
        safe_query(
            "INSERT INTO
                " . PREFIX . "poll_votes (
                    pollID,
                    o1,
                    o2,
                    o3,
                    o4,
                    o5,
                    o6,
                    o7,
                    o8,
                    o9,
                    o10
                )
                values(
                    '" . (int)$id . "',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0'
                )"
        );
    } else {
        safe_query(
            "UPDATE
                " . PREFIX . "poll
            SET
                titel='" . $_POST[ 'title' ] . "',
                o1='" . $_POST[ 'op1' ] . "',
                o2='" . $_POST[ 'op2' ] . "',
                o3='" . $_POST[ 'op3' ] . "',
                o4='" . $_POST[ 'op4' ] . "',
                o5='" . $_POST[ 'op5' ] . "',
                o6='" . $_POST[ 'op6' ] . "',
                o7='" . $_POST[ 'op7' ] . "',
                o8='" . $_POST[ 'op8' ] . "',
                o9='" . $_POST[ 'op9' ] . "',
                o10='" . $_POST[ 'op10' ] . "',
                comments = '" . $_POST[ 'comments' ] . "',
                laufzeit = '" . $runtime . "',
                intern = '" . $intern . "'
            WHERE
                pollID='" . (int)$pollID."'"
        );
    }
    header('Location: index.php?site=polls');
} elseif (isset($_GET[ 'end' ])) {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    $_language->readModule('polls');
    if (!ispollsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    $pollID = $_GET[ 'pollID' ];
    safe_query("UPDATE " . PREFIX . "poll SET aktiv='0' WHERE pollID='" . $pollID . "'");
    header('Location: index.php?site=polls');
} elseif (isset($_GET[ 'reopen' ])) {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    $_language->readModule('polls');
    if (!ispollsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    $pollID = $_GET[ 'pollID' ];
    safe_query("UPDATE " . PREFIX . "poll SET aktiv='1' WHERE pollID='" . $pollID . "'");
    header('Location: index.php?site=polls');
} elseif (isset($_GET[ 'delete' ])) {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    $_language->readModule('polls');
    if (!ispollsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $pollID = $_GET[ 'pollID' ];
    safe_query("DELETE FROM " . PREFIX . "poll WHERE pollID = '" . $pollID . "'");
    safe_query("DELETE FROM " . PREFIX . "poll_votes WHERE pollID = '" . $pollID . "'");
    header('Location: index.php?site=polls');
}

$_language->readModule('polls');
eval("\$title_polls = \"" . gettemplate("title_polls") . "\";");
echo $title_polls;

if ($action == "new") {
    if (ispollsadmin($userID)) {
        eval("\$polls_new = \"" . gettemplate("polls_new") . "\";");
        echo $polls_new;
    } else {
        redirect('index.php?site=news', $_language->module[ 'no_access' ], 3);
    }
} elseif ($action == "edit") {
    if (ispollsadmin($userID)) {
        $pollID = $_GET[ 'pollID' ];
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "poll WHERE pollID='$pollID'");
        $ds = mysqli_fetch_array($ergebnis);

        if (isset($ds[ 'pollID' ])) {
            $runtime_date = date("Y-m-d", $ds[ 'laufzeit' ]);
            $runtime_time = date("H:i", $ds[ 'laufzeit' ]);

            $polltitle = getinput($ds[ 'titel' ]);
            $option1 = getinput($ds[ 'o1' ]);
            $option2 = getinput($ds[ 'o2' ]);
            $option3 = getinput($ds[ 'o3' ]);
            $option4 = getinput($ds[ 'o4' ]);
            $option5 = getinput($ds[ 'o5' ]);
            $option6 = getinput($ds[ 'o6' ]);
            $option7 = getinput($ds[ 'o7' ]);
            $option8 = getinput($ds[ 'o8' ]);
            $option9 = getinput($ds[ 'o9' ]);
            $option10 = getinput($ds[ 'o10' ]);

            $comments = '
                <option value="0">' . $_language->module[ 'disable_comments' ] . '</option>
                <option value="1">' . $_language->module[ 'enable_user_comments' ] . '</option>
                <option value="2">' . $_language->module[ 'enable_visitor_comments' ] . '</option>';
            $comments = str_replace(
                'value="' . $ds[ 'comments' ] . '"',
                'value="' . $ds[ 'comments' ] . '" selected="selected"',
                $comments
            );
            if ($ds[ 'intern' ]) {
                $intern = "checked='checked'";
            } else {
                $intern = '';
            }
            $bg1 = BG_1;
            eval("\$polls_edit = \"" . gettemplate("polls_edit") . "\";");
            echo $polls_edit;
        }
    } else {
        redirect('index.php?site=polls', $_language->module[ 'no_access' ], 3);
    }
} elseif (isset($_GET[ 'pollID' ])) {
    $pollID = $_GET[ 'pollID' ];
    if (ispollsadmin($userID)) {
        echo '<div class="form-group">
            <a href="index.php?site=polls&amp;action=new" class="btn btn-danger">
                ' . $_language->module[ 'new_poll' ] . '
            </a>
        </div>';
    }

    $ergebnis =
        safe_query("SELECT * FROM " . PREFIX . "poll WHERE pollID='$pollID' AND intern<=" . (int)isclanmember($userID));
    $ds = mysqli_fetch_array($ergebnis);
    $bg1 = BG_1;
    $title = $ds[ 'titel' ];

    if ($ds[ 'intern' ] == 1) {
        $isintern = '(' . $_language->module[ 'intern' ] . ')';
    } else {
        $isintern = '';
    }

    if ($ds[ 'laufzeit' ] < time() || $ds[ 'aktiv' ] == "0") {
        $timeleft = $_language->module[ 'poll_ended' ];
        $active = '';
    } else {
        $timeleft = floor(($ds[ 'laufzeit' ] - time()) / (60 * 60 * 24)) . " " . $_language->module[ 'days' ];
        $active = 'active';
    }
    for ($n = 1; $n <= 10; $n++) {
        if ($ds[ 'o' . $n ]) {
            $options[ ] = clearfromtags($ds[ 'o' . $n ]);
        }
    }

    $adminactions = '';
    if (ispollsadmin($userID)) {
        if ($ds[ 'aktiv' ]) {
            $stop = ' <input type="button" onclick="MM_confirm(
                \'' . $_language->module[ 'really_stop' ] . '\',
                \'polls.php?end=true&amp;pollID=' . $ds[ 'pollID' ] . '\'
            )" value="' . $_language->module[ 'stop_poll' ] . '" class="btn btn-danger"> ';
        } else {
            $stop = ' <input type="button" onclick="MM_confirm(
                \'' . $_language->module[ 'really_reopen' ] . '\',
                \'polls.php?reopen=true&amp;pollID=' . $ds[ 'pollID' ] . '\'
            )" value="' . $_language->module[ 'reopen_poll' ] . '" class="btn btn-danger"> ';
        }
        $edit = ' <a href="index.php?site=polls&amp;action=edit&amp;pollID=' . $ds[ 'pollID' ] .
            '" class="btn btn-danger">' . $_language->module[ 'edit' ] . '</a>';
        $adminactions = $edit . '<input type="button" onclick="MM_confirm(
            \'' . $_language->module[ 'really_delete' ] . '\',
            \'polls.php?delete=true&amp;pollID=' . $ds[ 'pollID' ] . '\'
        )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger">' . $stop;
    }

    $votes = safe_query("SELECT * FROM " . PREFIX . "poll_votes WHERE pollID='" . $pollID . "'");
    $dv = mysqli_fetch_array($votes);
    $gesamtstimmen =
        $dv[ 'o1' ] + $dv[ 'o2' ] + $dv[ 'o3' ] + $dv[ 'o4' ] + $dv[ 'o5' ] + $dv[ 'o6' ] + $dv[ 'o7' ] + $dv[ 'o8' ] +
        $dv[ 'o9' ] + $dv[ 'o10' ];
    $n = 1;

    eval("\$polls_head = \"" . gettemplate("polls_head") . "\";");
    echo $polls_head;
    $comments = "";
    foreach ($options as $option) {
        $stimmen = $dv[ 'o' . $n ];
        if ($gesamtstimmen) {
            $perc = $stimmen / $gesamtstimmen * 10000;
            settype($perc, "integer");
            $perc = $perc / 100;
        } else {
            $perc = 0;
        }
        $picwidth = $perc;
        settype($picwidth, "integer");

        if ($picwidth) {
            $pic = '<div class="progress">
            <div
                class="progress-bar"
                role="progressbar"
                aria-valuenow="' . $picwidth . '"
                aria-valuemin="0"
                aria-valuemax="100"
                style="width: ' . $picwidth . '%;"
            >
                ' . $picwidth . ' %
            </div>
        </div>';
        } else {
            $pic = '<div class="progress">
            <div
                class="progress-bar"
                role="progressbar"
                aria-valuenow="0"
                aria-valuemin="0"
                aria-valuemax="100"
                style="width: 0%;"
            >
                0 %
            </div>
        </div>';
        }

        eval("\$polls_content = \"" . gettemplate("polls_content") . "\";");
        echo $polls_content;
        $n++;
    }

    eval("\$polls_foot = \"" . gettemplate("polls_foot") . "\";");
    echo $polls_foot;

    $comments_allowed = $ds[ 'comments' ];
    $parentID = $pollID;
    $type = "po";
    $referer = "index.php?site=polls&amp;pollID=" . $pollID;

    include("comments.php");
} elseif (isset($_GET[ 'vote' ])) {
    $pagebg = PAGEBG;
    $border = BORDER;
    $bghead = BGHEAD;
    $bgcat = BGCAT;

    $poll = $_GET[ 'vote' ];

    $lastpoll = safe_query(
        "SELECT
            *
        FROM
            " . PREFIX . "poll
        WHERE
            aktiv='1' AND
            laufzeit>" . time() . " AND
            intern<=" . (int)isclanmember($userID) . " AND
            pollID='" . $poll . "'
        LIMIT 0,1"
    );

    $anz = mysqli_num_rows($lastpoll);
    $ds = mysqli_fetch_array($lastpoll);
    if ($anz) {
        $anz = mysqli_num_rows(
            safe_query(
                "SELECT
                    pollID
                FROM
                    `" . PREFIX . "poll`
                WHERE
                    pollID='" . $ds[ 'pollID' ] . "' AND
                    hosts
                    LIKE
                        '%" . $_SERVER[ 'REMOTE_ADDR' ] . "%' AND
                        intern<=" . (int)isclanmember($userID)
            )
        );

        $anz_user = false;
        if ($userID) {
            $user_ids = explode(";", $ds[ 'userIDs' ]);
            if (in_array($userID, $user_ids)) {
                $anz_user = true;
            }
        }
        $cookie = false;
        if (isset($_COOKIE[ 'poll' ]) && is_array($_COOKIE[ 'poll' ])) {
            $cookie = in_array($ds[ 'pollID' ], $_COOKIE[ 'poll' ]);
        }

        if ($cookie || $anz || $anz_user) {
            redirect('index.php?site=polls&amp;pollID=' . $ds[ 'pollID' ], $_language->module[ 'already_voted' ], 3);
        } else {
            echo '<form method="post" action="polls.php?action=vote">
            <table class="table">
                <tr>
                    <td><strong>' . $ds[ 'titel' ] . '</strong><br><br></td>
                </tr>
                <tr>
                <td>';

            for ($n = 1; $n <= 10; $n++) {
                if ($ds[ 'o' . $n ]) {
                    $options[ ] = clearfromtags($ds[ 'o' . $n ]);
                }
            }
            $n = 1;
            foreach ($options as $option) {
                echo '<input class="input" type="radio" name="vote" value="' . $n . '"> ' . $option . '<br>';
                $n++;
            }
            echo '</td>
            </tr>
            <tr>
                <td><br><input type="hidden" name="pollID" value="' . $ds[ 'pollID' ] . '">
                <input type="submit" value="vote"></td>
            </tr>
            <tr>
                <td><br>&#8226; <a href="index.php?site=polls">' . $_language->module[ 'show_polls' ] . '</a></td>
            </tr>
        </table>
        </form>';
        }
    } else {
        redirect('index.php?site=polls&pollID=' . $ds[ 'pollID' ], $_language->module[ 'poll_ended' ], 3);
    }
} else {
    if (ispollsadmin($userID)) {
        echo '<div class="form-group">
            <a href="index.php?site=polls&amp;action=new" class="btn btn-danger">
                ' . $_language->module[ 'new_poll' ] . '
            </a>
        </div>';
    }

    $ergebnis =
        safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "poll
            WHERE
                intern<=" . (int)isclanmember($userID) . "
            ORDER BY
                pollID DESC"
        );
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        $i = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            if ($i % 2) {
                $bg1 = BG_1;
            } else {
                $bg1 = BG_2;
            }

            $title = $ds[ 'titel' ];

            if ($ds[ 'intern' ] == 1) {
                $isintern = '(' . $_language->module[ 'intern' ] . ')';
            } else {
                $isintern = '';
            }

            if ($ds[ 'laufzeit' ] < time() || $ds[ 'aktiv' ] == "0") {
                $timeleft = $_language->module[ 'poll_ended' ];
                $active = '';
            } else {
                $timeleft =
                    floor(($ds[ 'laufzeit' ] - time()) / (60 * 60 * 24)) . " " . $_language->module[ 'days' ] . " (" .
                    date("d.m.Y H:i", $ds[ 'laufzeit' ]) . ")<br>
                        <a href='index.php?site=polls&amp;vote=" . $ds[ 'pollID' ] . "' class='btn btn-primary'>" .
                    $_language->module[ 'vote_now' ] . "</a>";
                $active = 'active';
            }

            for ($n = 1; $n <= 10; $n++) {
                if ($ds[ 'o' . $n ]) {
                    $options[ ] = clearfromtags($ds[ 'o' . $n ]);
                }
            }

            $adminactions = '';
            if (ispollsadmin($userID)) {
                if ($ds[ 'aktiv' ]) {
                    $stop = ' <input type="button" onclick="MM_confirm(
                        \'' . $_language->module[ 'really_stop' ] . '\',
                        \'polls.php?end=true&amp;pollID=' . $ds[ 'pollID' ] . '\'
                    )" value="' . $_language->module[ 'stop_poll' ] . '" class="btn btn-danger"> ';
                } else {
                    $stop = ' <input type="button" onclick="MM_confirm(
                    \'' . $_language->module[ 'really_reopen' ] . '\',
                    \'polls.php?reopen=true&amp;pollID=' . $ds[ 'pollID' ] . '\'
                    )" value="' . $_language->module[ 'reopen_poll' ] . '" class="btn btn-danger"> ';
                }
                $edit = ' <a href="index.php?site=polls&amp;action=edit&amp;pollID=' . $ds[ 'pollID' ] . '"
                        class="btn btn-danger">
                    ' . $_language->module[ 'edit' ] . '
                </a> ';
                $adminactions = $edit . '<input type="button" onclick="MM_confirm(
                    \'' . $_language->module[ 'really_delete' ] . '\',
                    \'polls.php?delete=true&amp;pollID=' . $ds[ 'pollID' ] . '\'
                )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger">' . $stop;
            }

            $votes = safe_query("SELECT * FROM " . PREFIX . "poll_votes WHERE pollID='" . $ds[ 'pollID' ] . "'");
            $dv = mysqli_fetch_array($votes);
            $gesamtstimmen =
                $dv[ 'o1' ] + $dv[ 'o2' ] + $dv[ 'o3' ] + $dv[ 'o4' ] + $dv[ 'o5' ] + $dv[ 'o6' ] + $dv[ 'o7' ] +
                $dv[ 'o8' ] + $dv[ 'o9' ] + $dv[ 'o10' ];
            $n = 1;

            eval ("\$polls_head = \"" . gettemplate("polls_head") . "\";");
            echo $polls_head;

            foreach ($options as $option) {
                $stimmen = $dv[ 'o' . $n ];
                if ($gesamtstimmen) {
                    $perc = $stimmen / $gesamtstimmen * 10000;
                    settype($perc, "integer");
                    $perc = $perc / 100;
                } else {
                    $perc = 0;
                }
                $picwidth = $perc;
                settype($picwidth, "integer");

                $anzcomments = getanzcomments($ds[ 'pollID' ], 'po');
                if ($anzcomments) {
                    $comments = '<a href="index.php?site=polls&amp;pollID=' . $ds[ 'pollID' ] . '"
                        class="btn btn-primary">
                        ' . $anzcomments . ' ' . $_language->module[ 'comments' ] . '
                    </a> ' . $_language->module[ 'latest_by' ] . ' ' . getlastcommentposter($ds[ 'pollID' ], 'po') .
                        ' - ' . date("d.m.Y - H:i", getlastcommentdate($ds[ 'pollID' ], 'po'));
                } else {
                    $comments = '<a href="index.php?site=polls&amp;pollID=' . $ds[ 'pollID' ] . '"
                        class="btn btn-primary">
                        0 ' . $_language->module[ 'comments' ] . '
                    </a>';
                }

                eval ("\$polls_content = \"" . gettemplate("polls_content") . "\";");
                echo $polls_content;

                $n++;
            }

            eval ("\$polls_foot = \"" . gettemplate("polls_foot") . "\";");
            echo $polls_foot;

            $i++;

            unset($options);
        }
    } else {
        echo $_language->module[ 'no_entries' ];
    }
}
