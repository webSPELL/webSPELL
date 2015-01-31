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

include("_mysql.php");
include("_settings.php");
include("_functions.php");
$_language->readModule('forum');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo PAGETITLE; ?></title>
    <style type="text/css">
        <!--
        body {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000000;
        }

        table {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000000;
        }

        -->
    </style>
</head>

<body>
<?php
$topic = $_GET[ 'topic' ];
$thread = safe_query("SELECT * FROM " . PREFIX . "forum_topics WHERE topicID='$topic' ");

if (mysqli_num_rows($thread)) {
    $dt = mysqli_fetch_array($thread);

    if ($dt[ 'readgrps' ] != "") {
        $usergrps = explode(";", $dt[ 'readgrps' ]);
        $usergrp = 0;
        foreach ($usergrps as $value) {
            if (isinusergrp($value, $userID)) {
                $usergrp = 1;
                break;
            }
        }
        if (!$usergrp && !ismoderator($userID, $dt[ 'boardID' ])) {
            die($_language->module[ 'no_access' ]);
        }
    }

    $ergebnis = safe_query(
        "SELECT * FROM `" . PREFIX . "forum_boards` WHERE `boardID` = '" . (int)$dt['boardID'] . "'"
    );
    $db = mysqli_fetch_array($ergebnis);
    $boardname = $db[ 'name' ];

    echo '<div style="width:640px;">
    <table class="table">
        <tr>
            <td><strong>' . $boardname . '</strong> &#8226; <strong>' . getinput($dt[ 'topic' ]) . '</strong></td>
        </tr>
    </table><hr size="1"><br>';

    echo '<table class="table">';

    $replys = safe_query("SELECT * FROM " . PREFIX . "forum_posts WHERE topicID='$topic' ORDER BY date");
    while ($dr = mysqli_fetch_array($replys)) {
        $date = getformatdate($dr[ 'date' ]);
        $time = getformattime($dr[ 'date' ]);

        $message = cleartext($dr[ 'message' ]);
        $username = getnickname($dr[ 'poster' ]);

        if (getsignatur($dr[ 'poster' ])) {
            $signatur = '<br><br>' . getsignatur($dr[ 'poster' ]);
        } else {
            $signatur = '';
        }
        $posts = getuserforumposts($dr[ 'poster' ]);
        if (isforumadmin($dr[ 'poster' ]) || ismoderator($dr[ 'poster' ], $dt[ 'boardID' ])) {
            if (ismoderator($dr[ 'poster' ], $dt[ 'boardID' ])) {
                $usertype = "Moderator";
                $rang = '<img src="images/icons/ranks/moderator.gif" alt="">';
            }
            if (isforumadmin($dr[ 'poster' ])) {
                $usertype = "Administrator";
                $rang = '<img src="images/icons/ranks/admin.gif" alt="">';
            }
        } else {
            $ergebnis =
                safe_query(
                    "SELECT
                        *
                    FROM
                        " . PREFIX . "forum_ranks
                    WHERE
                        $posts > postmin
                    AND
                        $posts < postmax
                    AND
                        special = '0'"
                );
            $ds = mysqli_fetch_array($ergebnis);
            $usertype = $ds[ 'rank' ];
            $rang = '<img src="images/icons/ranks/' . $ds[ 'pic' ] . '" alt="">';
        }

        $specialrang = "";
        $specialtype = "";
        $getrank = safe_query(
            "SELECT IF
                (u.special_rank = 0, 0, CONCAT_WS('__', r.rank, r.pic)) as RANK
            FROM
                " . PREFIX . "user u LEFT JOIN " . PREFIX . "forum_ranks r ON u.special_rank = r.rankID
            WHERE
                userID = '" . $dr[ 'poster' ] . "'"
        );
        $rank_data = mysqli_fetch_assoc($getrank);

        if ($rank_data[ 'RANK' ] != '0') {
            $tmp_rank = explode("__", $rank_data[ 'RANK' ], 2);
            $specialrang = " - " . $tmp_rank[0];
            if (!empty($tmp_rank[1]) && file_exists("images/icons/ranks/" . $tmp_rank[1])) {
                $specialtype = "<img src='images/icons/ranks/" . $tmp_rank[1] . "' alt = '" . $specialrang . "' />";
            }
        }

        echo '<tr>
        <td valign="top"><i>' . $date . ', ' . $time . ' </i> - <strong>' . $username . '</strong> - ' .
            $usertype . $rang . $specialrang . $specialtype . ' - ' . $posts . ' ' . $_language->module[ 'posts' ] .
            '<br>' . $message . ' ><i>' . $signatur . '</i>><br>&nbsp;</td>
        </tr>';
    }
    echo '</table><br></div></body></html>';
}
