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

// -- ERROR REPORTING -- //
define('DEBUG', "ON"); // ON = development-mode | OFF = public mode
if (DEBUG === 'ON') {
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

// -- SET ENCODING FOR MB-FUNCTIONS -- //

mb_internal_encoding("UTF-8");

// -- SET HTTP ENCODING -- //

header('content-type: text/html; charset=utf-8');

// -- INSTALL CHECK -- //

if (DEBUG == "OFF" && file_exists('install/index.php')) {
    system_error(
        'The install-folder exists. Did you run the <a href="install/">Installer</a>?<br>
        If yes, please remove the install-folder.',
        0
    );
}

// -- CONNECTION TO MYSQL -- //
if (!isset($GLOBALS[ '_database' ])) {
    $_database = @new mysqli($host, $user, $pwd, $db);

    if ($_database->connect_error) {
        system_error('ERROR: Can not connect to MySQL-Server');
    }

    $_database->query("SET NAMES 'utf8'");
}

// -- GENERAL PROTECTIONS -- //

if (function_exists("globalskiller") == false) {
    function globalskiller()
    {        // kills all non-system variables
        $global =
            ['GLOBALS', '_POST', '_GET', '_COOKIE', '_FILES', '_SERVER', '_ENV', '_REQUEST', '_SESSION', '_database'];
        foreach ($GLOBALS as $key => $val) {
            if (!in_array($key, $global)) {
                if (is_array($val)) {
                    unset_array($GLOBALS[ $key ]);
                } else {
                    unset($GLOBALS[ $key ]);
                }
            }
        }
    }
}

if (function_exists("unset_array") == false) {
    function unset_array($array)
    {
        foreach ($array as $key) {
            if (is_array($key)) {
                unset_array($key);
            } else {
                unset($key);
            }
        }
    }
}

globalskiller();

if (isset($_GET[ 'site' ])) {
    $site = $_GET[ 'site' ];
} else {
    $site = null;
}
if ($site != "search") {
    $request = strtolower(urldecode($_SERVER[ 'QUERY_STRING' ]));
    $protarray = [
        "union",
        "select",
        "into",
        "where",
        "update ",
        "from",
        "/*",
        "set ",
        PREFIX . "user ",
        PREFIX . "user(",
        PREFIX . "user`",
        PREFIX . "user_groups",
        "phpinfo",
        "escapeshellarg",
        "exec",
        "fopen",
        "fwrite",
        "escapeshellcmd",
        "passthru",
        "proc_close",
        "proc_get_status",
        "proc_nice",
        "proc_open",
        "proc_terminate",
        "shell_exec",
        "system",
        "telnet",
        "ssh",
        "cmd",
        "mv",
        "chmod",
        "chdir",
        "locate",
        "killall",
        "passwd",
        "kill",
        "script",
        "bash",
        "perl",
        "mysql",
        "~root",
        ".history",
        "~nobody",
        "getenv"
    ];
    $check = str_replace($protarray, '*', $request);
    if ($request != $check) {
        system_error("Invalid request detected.");
    }
}

function security_slashes(&$array)
{

    global $_database;

    foreach ($array as $key => $value) {
        if (is_array($array[ $key ])) {
            security_slashes($array[ $key ]);
        } else {
            if (get_magic_quotes_gpc()) {
                $tmp = stripslashes($value);
            } else {
                $tmp = $value;
            }
            if (function_exists("mysqli_real_escape_string")) {
                $array[ $key ] = $_database->escape_string($tmp);
            } else {
                $array[ $key ] = addslashes($tmp);
            }
            unset($tmp);
        }
    }
}

security_slashes($_POST);
security_slashes($_COOKIE);
security_slashes($_GET);
security_slashes($_REQUEST);

// -- MYSQL QUERY FUNCTION -- //
$_mysql_querys = [];
function safe_query($query = "")
{

    global $_database;
    global $_mysql_querys;

    if (stristr(str_replace(' ', '', $query), "unionselect") === false and
        stristr(str_replace(' ', '', $query), "union(select") === false
    ) {
        $_mysql_querys[ ] = $query;
        if (empty($query)) {
            return false;
        }
        if (DEBUG == "OFF") {
            $result = $_database->query($query) or system_error('Query failed!');
        } else {
            $result = $_database->query($query) or
            system_error(
                'Query failed: ' . '<ul>' .
                '<li>errorno=' . $_database->errno . '</li>' .
                '<li>error=' . $_database->error . '</li>' .
                '<li>query=' . $query . '</li>'.
                '</ul>'
            );
        }
        return $result;
    } else {
        die();
    }
}

// -- SYSTEM ERROR DISPLAY -- //

function system_error($text, $system = 1)
{

    global $_database;

    if ($system) {
        include('version.php');
        $info = 'webSPELL Version: ' . $version . '<br>PHP Version: ' . phpversion() . '<br>';
        if (!mysqli_connect_error()) {
            $info .= 'MySQL Version: ' . $_database->server_info . '<br>';
        }
    } else {
        $info = '';
    }
    die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Clanpage using webSPELL 4 CMS">
        <meta name="author" content="webspell.org">
        <meta name="keywords" content="webspell, webspell4, clan, cms">
        <meta name="copyright" content="Copyright 2005-2014 by webspell.org">
        <meta name="generator" content="webSPELL">
        <title>webSPELL</title>
    </head>
    <body class="text-center">
    <table class="table">
        <tr>
            <td>
                <a href="http://www.webspell.org" target="_blank">
                    <img src="images/banner.gif" style="border:none;" alt="webSPELL.org" title="webSPELL.org">
                </a>
            </td>
        </tr>
        <tr>
            <td><div style="color:#333333;font-family:Tahoma,Verdana,Arial;font-size:11px;padding:5px;">' .
                $info . '<br><font color="red">' . $text . '</font><br>&nbsp;</div></td>
        </tr>
        <tr>
            <td>
                <div style="color:#333333;font-family:Tahoma,Verdana,Arial;font-size:11px;padding:5px;">
                    For support visit <a href="http://webspell.org" target="_blank">webspell.org</a>
                </div>
            </td>
        </tr>
        </table>
    </body>
    </html>');
}

// -- SYSTEM FILE INCLUDE -- //

function systeminc($file)
{
    if (!include('src/' . $file . '.php')) {
        system_error('Could not get system file for ' . $file);
    }
}

// -- IGNORED USERS -- //

function isignored($userID, $buddy)
{
    $anz = mysqli_num_rows(
        safe_query(
            "SELECT userID FROM " . PREFIX . "buddys WHERE buddy='$buddy' AND userID='$userID' "
        )
    );
    if ($anz) {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "buddys WHERE buddy='$buddy' AND userID='$userID' ");
        $ds = mysqli_fetch_array($ergebnis);
        if ($ds[ 'banned' ] == 1) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 0;
    }
}

// -- GLOBAL SETTINGS -- //

$ds = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "settings"));

$components = [
    'css' => [
        'components/bootstrap/dist/css/bootstrap.min.css'
    ],
    'js' => [
        'components/jquery/dist/jquery.min.js',
        'components/bootstrap/dist/js/bootstrap.min.js',
        'components/webshim/js-webshim/minified/polyfiller.js'
    ]
];

$maxshownnews = $ds[ 'news' ];
if (empty($maxshownnews)) {
    $maxshownnews = 10;
}
$maxnewsarchiv = $ds[ 'newsarchiv' ];
if (empty($maxnewsarchiv)) {
    $maxnewsarchiv = 20;
}
$maxheadlines = $ds[ 'headlines' ];
if (empty($maxheadlines)) {
    $maxheadlines = 10;
}
$maxheadlinechars = $ds[ 'headlineschars' ];
if (empty($maxheadlinechars)) {
    $maxheadlinechars = 18;
}
$maxtopnewschars = $ds[ 'topnewschars' ];
if (empty($maxtopnewschars)) {
    $maxtopnewschars = 200;
}
$maxarticles = $ds[ 'articles' ];
if (empty($maxarticles)) {
    $maxarticles = 20;
}
$latestarticles = $ds[ 'latestarticles' ];
if (empty($latestarticles)) {
    $latestarticles = 5;
}
$articleschars = $ds[ 'articleschars' ];
if (empty($articleschars)) {
    $articleschars = 18;
}
$maxclanwars = $ds[ 'clanwars' ];
if (empty($maxclanwars)) {
    $maxclanwars = 20;
}
$maxresults = $ds[ 'results' ];
if (empty($maxresults)) {
    $maxresults = 5;
}
$maxupcoming = $ds[ 'upcoming' ];
if (empty($maxupcoming)) {
    $maxupcoming = 5;
}
$maxguestbook = $ds[ 'guestbook' ];
if (empty($maxguestbook)) {
    $maxguestbook = 20;
}
$maxshoutbox = $ds[ 'shoutbox' ];
if (empty($maxshoutbox)) {
    $maxshoutbox = 5;
}
$maxsball = $ds[ 'sball' ];
if (empty($maxsball)) {
    $maxsball = 5;
}
$sbrefresh = $ds[ 'sbrefresh' ];
if (empty($sbrefresh)) {
    $sbrefresh = 60;
}
$maxtopics = $ds[ 'topics' ];
if (empty($maxtopics)) {
    $maxtopics = 20;
}
$maxposts = $ds[ 'posts' ];
if (empty($maxposts)) {
    $maxposts = 10;
}
$maxlatesttopics = $ds[ 'latesttopics' ];
if (empty($maxlatesttopics)) {
    $maxlatesttopics = 10;
}
$maxlatesttopicchars = $ds[ 'latesttopicchars' ];
if (empty($maxlatesttopicchars)) {
    $maxlatesttopicchars = 18;
}
$maxfeedback = $ds[ 'feedback' ];
if (empty($maxfeedback)) {
    $maxfeedback = 5;
}
$maxmessages = $ds[ 'messages' ];
if (empty($maxmessages)) {
    $maxmessages = 5;
}
$maxusers = $ds[ 'users' ];
if (empty($maxusers)) {
    $maxusers = 5;
}
$hp_url = $ds[ 'hpurl' ];
$admin_name = $ds[ 'adminname' ];
$admin_email = $ds[ 'adminemail' ];
$myclantag = $ds[ 'clantag' ];
$myclanname = $ds[ 'clanname' ];
$maxarticles = $ds[ 'articles' ];
if (empty($maxarticles)) {
    $maxarticles = 5;
}
$maxawards = $ds[ 'awards' ];
if (empty($maxawards)) {
    $maxawards = 20;
}
$maxdemos = $ds[ 'demos' ];
if (empty($maxdemos)) {
    $maxdemos = 20;
}
$profilelast = $ds[ 'profilelast' ];
if (empty($profilelast)) {
    $profilelast = 20;
}
$topnewsID = $ds[ 'topnewsID' ];
$sessionduration = $ds[ 'sessionduration' ];
if (empty($sessionduration)) {
    $sessionduration = 24;
}
$closed = (int)$ds[ 'closed' ];
$gb_info = $ds[ 'gb_info' ];
$imprint_type = $ds[ 'imprint' ];
$picsize_l = $ds[ 'picsize_l' ];
if (empty($picsize_l)) {
    $picsize_l = 9999;
}
$picsize_h = $ds[ 'picsize_h' ];
if (empty($picsize_h)) {
    $picsize_h = 9999;
}
$gallerypictures = $ds[ 'pictures' ];
$publicadmin = $ds[ 'publicadmin' ];
$thumbwidth = $ds[ 'thumbwidth' ];
if (empty($thumbwidth)) {
    $thumbwidth = 120;
}
$usergalleries = $ds[ 'usergalleries' ];
$maxusergalleries = $ds[ 'maxusergalleries' ];
$default_language = $ds[ 'default_language' ];
if (empty($default_language)) {
    $default_language = 'uk';
}
$rss_default_language = $ds[ 'default_language' ];
if (empty($rss_default_language)) {
    $rss_default_language = 'uk';
}
$search_min_len = $ds[ 'search_min_len' ];
if (empty($search_min_len)) {
    $search_min_len = '4';
}
$autoresize = $ds[ 'autoresize' ];
if (!isset($autoresize)) {
    $autoresize = 2;
}
$max_wrong_pw = $ds[ 'max_wrong_pw' ];
if (empty($max_wrong_pw)) {
    $max_wrong_pw = 3;
}
$lastBanCheck = $ds[ 'bancheck' ];
$insertlinks = $ds[ 'insertlinks' ];
$autoDetectLanguage = (int)$ds[ 'detect_language' ];
$spamapikey = $ds[ 'spamapikey' ];
$spamapihost = $ds[ 'spamapihost' ];
if (empty($spamapihost)) {
    $spamapihost = "https://api.webspell.org/";
}
$spamCheckMaxPosts = $ds[ 'spammaxposts' ];
if (empty($spamCheckMaxPosts)) {
    $spamCheckMaxPosts = 30;
}
$spamCheckEnabled = (int)$ds[ 'spam_check' ];
$spamBlockOnError = (int)$ds[ 'spamapiblockerror' ];
$spamCheckRating = 0.95;
$default_format_date = $ds[ 'date_format' ];
if (empty($default_format_date)) {
    $default_format_date = 'd.m.Y';
}
$default_format_time = $ds[ 'time_format' ];
if (empty($default_format_time)) {
    $default_format_time = 'H:i';
}
$user_guestbook = $ds[ 'user_guestbook' ];
if (!isset($user_guestbook)) {
    $user_guestbook = 1;
}
$modRewrite = (bool)$ds[ 'modRewrite' ];
if (empty($modRewrite)) {
    $modRewrite = false;
}

$new_chmod = 0666;

// -- STYLES -- //

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "styles");
$ds = mysqli_fetch_array($ergebnis);

define('PAGEBG', $ds[ 'bgpage' ]);
define('BORDER', $ds[ 'border' ]);
define('BGHEAD', $ds[ 'bghead' ]);
define('BGCAT', $ds[ 'bgcat' ]);
define('BG_1', $ds[ 'bg1' ]);
define('BG_2', $ds[ 'bg2' ]);
define('BG_3', $ds[ 'bg3' ]);
define('BG_4', $ds[ 'bg4' ]);

$hp_title = stripslashes($ds[ 'title' ]);
$pagebg = PAGEBG;
$border = BORDER;
$bghead = BGHEAD;
$bgcat = BGCAT;

$wincolor = $ds[ 'win' ];
$loosecolor = $ds[ 'loose' ];
$drawcolor = $ds[ 'draw' ];
