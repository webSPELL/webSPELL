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

function getanzcwcomments($cwID)
{
    return mysqli_num_rows(
        safe_query(
            "SELECT commentID FROM `" . PREFIX . "comments` WHERE `parentID` = " .(int)$cwID . " AND `type` = 'cw'"
        )
    );
}

function getsquads()
{
    $squads = "";
    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "squads`");
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $squads .= '<option value="' . $ds[ 'squadID' ] . '">' . $ds[ 'name' ] . '</option>';
    }
    return $squads;
}

function getgamesquads()
{
    $squads = '';
    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "squads` WHERE `gamesquad` = 1");
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $squads .= '<option value="' . $ds[ 'squadID' ] . '">' . $ds[ 'name' ] . '</option>';
    }
    return $squads;
}

function getsquadname($squadID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT `name` FROM `" . PREFIX . "squads` WHERE `squadID` = " . (int)$squadID
        )
    );
    return $ds[ 'name' ];
}

function issquadmember($userID, $squadID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    `sqmID`
                FROM
                    `" . PREFIX . "squads_members`
                WHERE
                    `userID` = " . (int)$userID . " AND
                    `squadID` = " . (int)$squadID
            )
        ) > 0);
}

function isgamesquad($squadID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    `squadID`
                FROM
                    `" . PREFIX . "squads`
                WHERE
                    `squadID` = " . (int)$squadID . " AND
                    gamesquad = 1"
            )
        ) > 0);
}

function getgamename($tag)
{
    $ds = mysqli_fetch_array(safe_query("SELECT `name` FROM `" . PREFIX . "games` WHERE `tag` = '$tag'"));
    return $ds[ 'name' ];
}

function is_gametag($tag)
{
    return (mysqli_num_rows(safe_query("SELECT `name` FROM `" . PREFIX . "games` WHERE `tag` = '$tag'")) > 0);
}

function getGamesAsOptionList($selected = null)
{
    $gamesa = safe_query("SELECT * FROM `" . PREFIX . "games` ORDER BY `name`");
    $list = "";

    while ($ds = mysqli_fetch_array($gamesa)) {
        if ($ds[ 'tag' ] == $selected) {
            $list .= '<option value="' . $ds[ 'tag' ] . '" selected="selected">' . $ds[ 'name' ] . '</option>';
        } else {
            $list .= '<option value="' . $ds[ 'tag' ] . '">' . $ds[ 'name' ] . '</option>';
        }
    }

    return $list;
}
