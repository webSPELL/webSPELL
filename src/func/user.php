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

function getuserid($nickname)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT userID FROM " . PREFIX . "user WHERE `nickname` = '" . $nickname . "'"
        )
    );
    return $ds['userID'];
}

function getnickname($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT nickname FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return $ds['nickname'];
}

function getuserdescription($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT userdescription FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return getinput($ds['userdescription']);
}

function getfirstname($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT firstname FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return getinput($ds['firstname']);
}

function getlastname($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT lastname FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return getinput($ds['lastname']);
}

function getbirthday($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT birthday FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return getformatdate($ds['birthday']);
}

function gettown($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT town FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return getinput($ds['town']);
}

function getemail($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT email FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return getinput($ds['email']);
}

function getemailhide($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT email_hide FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return getinput($ds['email_hide']);
}

function gethomepage($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT homepage FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return str_replace('http://', '', getinput($ds['homepage']));
}

function geticq($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT icq FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return getinput($ds['icq']);
}

function getcountries($selected = null)
{
    $countries = '';
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "countries WHERE `fav` = 1 ORDER BY `country`");
    $anz = mysqli_num_rows($ergebnis);
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($ds['short'] == $selected) {
            $countries .= '<option value="' . $ds['short'] . '" selected="selected">' . $ds['country'] . '</option>';
        } else {
            $countries .= '<option value="' . $ds['short'] . '">' . $ds['country'] . '</option>';
        }
    }
    if ($anz) {
        $countries .= '<option value="">----------------------------------</option>';
    }
    $result = safe_query("SELECT * FROM " . PREFIX . "countries WHERE `fav`= 0 ORDER BY `country`");
    while ($dv = mysqli_fetch_array($result)) {
        if ($dv['short'] == $selected) {
            $countries .= '<option value="' . $dv['short'] . '" selected="selected">' . $dv['country'] . '</option>';
        } else {
            $countries .= '<option value="' . $dv['short'] . '">' . $dv['country'] . '</option>';
        }
    }
    return $countries;
}

function getcountry($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT country FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return getinput($ds['country']);
}

function getuserlanguage($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT language FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    return getinput($ds['language']);
}

function getuserpic($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT userpic FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID));
    if (!$ds['userpic']) {
        $userpic = "nouserpic.gif";
    } else {
        $userpic = $ds['userpic'];
    }
    return $userpic;
}

function getavatar($userID)
{
    $ds = mysqli_fetch_array(safe_query("SELECT avatar FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID . ""));
    if (empty($ds['avatar'])) {
        return "noavatar.gif";
    }

    return $ds['avatar'];
}

function getsignatur($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT usertext FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return strip_tags($ds['usertext']);
}

function getregistered($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT registerdate FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return getformatdate($ds['registerdate']);
}

function usergroupexists($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT userID FROM " . PREFIX . "user_groups WHERE `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function wantmail($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user
                WHERE
                    `userID` = " . (int)$userID . " AND
                    `mailonpm` = 1"
            )
        ) > 0
    );
}

function getuserguestbookstatus($userID)
{
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT user_guestbook FROM " . PREFIX . "user WHERE `userID` = " . (int)$userID
        )
    );
    return getinput($ds['user_guestbook']);
}

function getusercomments($userID, $type)
{
    return mysqli_num_rows(
        safe_query(
            "SELECT
                commentID
            FROM
                `" . PREFIX . "comments`
            WHERE
                `userID` = " . (int)$userID . " AND
                `type` = '" . $type . "'"
        )
    );
}

function getallusercomments($userID)
{
    return mysqli_num_rows(
        safe_query(
            "SELECT commentID FROM `" . PREFIX . "comments` WHERE `userID` = " . (int)$userID
        )
    );
}

function isbuddy($userID, $buddy)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "buddys
                WHERE
                    `banned` = 0 AND
                    `buddy` = " . (int)$buddy . " AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function RandPass($length, $type = 0)
{

    /* Randpass: Generates an random password
    Parameter:
    length - length of the password string
    type - there are 4 types: 0 - all chars, 1 - numeric only, 2 - upper chars only, 3 - lower chars only
    Example:
    echo RandPass(7, 1); => 0917432
    */
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
        if ($type == 0) {
            $rand = rand(1, 3);
        } else {
            $rand = $type;
        }
        switch ($rand) {
            case 1:
                $pass .= chr(rand(48, 57));
                break;
            case 2:
                $pass .= chr(rand(65, 90));
                break;
            case 3:
                $pass .= chr(rand(97, 122));
                break;
        }
    }
    return $pass;
}

function isonline($userID)
{
    $q = safe_query("SELECT site FROM " . PREFIX . "whoisonline WHERE userID=" . (int)$userID);
    if (mysqli_num_rows($q) > 0) {
        $ds = mysqli_fetch_array($q);
        return '<strong>online</strong> @ <a href="index.php?site=' . $ds['site'] . '">' . $ds['site'] . '</a>';
    }

    return 'offline';
}

function getLanguageWeight($language)
{
    if (empty($language)) {
        return 1;
    }

    return $language;
}

function detectUserLanguage()
{
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        preg_match_all(
            "/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i",
            $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            $matches
        );
        if (count($matches)) {
            $languages_found = array_combine($matches[1], array_map("getLanguageWeight", $matches[4]));
            arsort($languages_found, SORT_NUMERIC);
            $path = $GLOBALS['_language']->getRootPath();
            foreach ($languages_found as $key => $val) {
                if (is_dir($path . $key)) {
                    return $key;
                }
            }
        }
    }
    return null;
}

function generatePasswordHash($password)
{
    $md5 = hash("md5", $password);
    return hash("sha512", substr($md5, 0, 14) . $md5);
}
