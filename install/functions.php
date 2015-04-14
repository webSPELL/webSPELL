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

class Transaction
{
    private $database;
    private $success;
    private $errors = array();

    function __construct($database)
    {
        $this->database = $database;
        $this->success = true;
    }

    function addQuery($query)
    {
        if (!mysqli_query($this->database, $query)) {
            $this->success = false;
            $this->errors[] = mysqli_error($this->database);
        }
    }

    function successful()
    {
        if ($this->success) {
            $this->database->commit();
            return true;
        } else {
            //$this->error = mysqli_error($this->database);
            $this->database->rollback();
            return false;
        }
    }

    function getError()
    {
        return implode("<br/>", $this->errors);
    }
}

function update_progress($functions_to_call)
{
    return '<div id="todo_list" style="display:none;">' . json_encode($functions_to_call) . '</div><div class="progress">
  <div id="progress_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
    <span class="sr-only">0%</span>
  </div>
</div><div id="details_text" style="height: 150px; overflow-y:scroll;"></div>';
}

function update_clearfolder($_database)
{
    global $_language;
    include("../src/func/filesystem.php");
    $remove_install = @rm_recursive("./");
    if ($remove_install) {
        return array('status' => 'success', 'message' => $_language->module['folder_removed']);
    } else {
        return array('status' => 'success', 'message' => $_language->module['delete_folder']);
    }
}

function updateMySQLConfig()
{
    global $_language;
    include('../_mysql.php');
    /** variables from _mysql.php
     * @var string $host
     * @var string $user
     * @var string $pwd
     * @var string $db
     */
    $new_content = '<?php
$host = ' . var_export($host, true) . ';
$user = ' . var_export($user, true) . ';
$pwd = ' . var_export($pwd, true) . ';
$db = ' . var_export($db, true) . ';
if (!defined("PREFIX")) {
    define("PREFIX", ' . var_export(PREFIX, true) . ');
}
';
    $ret = file_put_contents('../_mysql.php', $new_content);
    if ($ret === false) {
        echo $_language->module['write_failed'];
    }
}

function update_base_1($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "about`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "about` (
  `about` longtext NOT NULL
)");
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "articles`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "articles` (
  `articlesID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `screens` text NOT NULL,
  `poster` int(11) NOT NULL default '0',
  `link1` varchar(255) NOT NULL default '',
  `url1` varchar(255) NOT NULL default '',
  `window1` int(1) NOT NULL default '0',
  `link2` varchar(255) NOT NULL default '',
  `url2` varchar(255) NOT NULL default '',
  `window2` int(1) NOT NULL default '0',
  `link3` varchar(255) NOT NULL default '',
  `url3` varchar(255) NOT NULL default '',
  `window3` int(1) NOT NULL default '0',
  `link4` varchar(255) NOT NULL default '',
  `url4` varchar(255) NOT NULL default '',
  `window4` int(1) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `saved` int(1) NOT NULL default '0',
  `viewed` int(11) NOT NULL default '0',
  `comments` int(1) NOT NULL default '0',
  PRIMARY KEY  (`articlesID`)
) AUTO_INCREMENT=1 ");
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "awards`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "awards` (
  `awardID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `squadID` int(11) NOT NULL default '0',
  `award` varchar(255) NOT NULL default '',
  `homepage` varchar(255) NOT NULL default '',
  `rang` int(11) NOT NULL default '0',
  `info` text NOT NULL,
  PRIMARY KEY  (`awardID`)
) AUTO_INCREMENT=1 ");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "a"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "a"<br/>' . $transaction->getError());
    }
}

function update_base_2($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "banner`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "banner` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `banner` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bannerID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "buddys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "buddys` (
  `buddyID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `buddy` int(11) NOT NULL default '0',
  `banned` int(1) NOT NULL default '0',
  PRIMARY KEY  (`buddyID`)
) AUTO_INCREMENT=1 ");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "b"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "b"<br/>' . $transaction->getError());
    }
}

function update_base_3($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "cash_box`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "cash_box` (
  `cashID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `paydate` int(14) NOT NULL default '0',
  `usedfor` text NOT NULL,
  `info` text NOT NULL,
  `totalcosts` double(8,2) NOT NULL default '0.00',
  `usercosts` double(8,2) NOT NULL default '0.00',
  `squad` int(11) NOT NULL default '0',
  `konto` text NOT NULL,
  PRIMARY KEY  (`cashID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "cash_box_payed`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "cash_box_payed` (
  `payedID` int(11) NOT NULL AUTO_INCREMENT,
  `cashID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `costs` double(8,2) NOT NULL default '0.00',
  `date` int(14) NOT NULL default '0',
  `payed` int(1) NOT NULL default '0',
  PRIMARY KEY  (`payedID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "challenge`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "challenge` (
  `chID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `cwdate` int(14) NOT NULL default '0',
  `squadID` varchar(255) NOT NULL default '',
  `opponent` varchar(255) NOT NULL default '',
  `opphp` varchar(255) NOT NULL default '',
  `oppcountry` char(2) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `map` varchar(255) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `info` text NOT NULL,
  PRIMARY KEY  (`chID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "clanwars`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "clanwars` (
  `cwID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `squad` int(11) NOT NULL default '0',
  `game` varchar(5) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `leaguehp` varchar(255) NOT NULL default '',
  `opponent` varchar(255) NOT NULL default '',
  `opptag` varchar(255) NOT NULL default '',
  `oppcountry` char(2) NOT NULL default '',
  `opphp` varchar(255) NOT NULL default '',
  `maps` varchar(255) NOT NULL default '',
  `hometeam` varchar(255) NOT NULL default '',
  `oppteam` varchar(255) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `homescr1` int(11) NOT NULL default '0',
  `oppscr1` int(11) NOT NULL default '0',
  `homescr2` int(11) NOT NULL default '0',
  `oppscr2` int(11) NOT NULL default '0',
  `screens` text NOT NULL,
  `report` text NOT NULL,
  `comments` int(1) NOT NULL default '0',
  `linkpage` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cwID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "comments`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "comments` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `parentID` int(11) NOT NULL default '0',
  `type` char(2) NOT NULL default '',
  `userID` int(11) NOT NULL default '0',
  `nickname` varchar(255) NOT NULL default '',
  `date` int(14) NOT NULL default '0',
  `comment` text NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`commentID`)
) AUTO_INCREMENT=1 ");


    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter` (
  `hits` int(20) NOT NULL default '0',
  `online` int(14) NOT NULL default '0'
)");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "counter` (`hits`, `online`) VALUES (1, '" . time() . "')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter_iplist`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter_iplist` (
  `dates` varchar(255) NOT NULL default '',
  `del` int(20) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default ''
)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter_stats`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter_stats` (
  `dates` varchar(255) NOT NULL default '',
  `count` int(20) NOT NULL default '0'
)");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "c"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "c"<br/>' . $transaction->getError());
    }
}

function update_base_4($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "demos`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "demos` (
  `demoID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `game` varchar(255) NOT NULL default '',
  `clan1` varchar(255) NOT NULL default '',
  `clan2` varchar(255) NOT NULL default '',
  `clantag1` varchar(255) NOT NULL default '',
  `clantag2` varchar(255) NOT NULL default '',
  `url1` varchar(255) NOT NULL default '',
  `url2` varchar(255) NOT NULL default '',
  `country1` char(2) NOT NULL default '',
  `country2` char(2) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `leaguehp` varchar(255) NOT NULL default '',
  `maps` varchar(255) NOT NULL default '',
  `player` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `downloads` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `comments` int(1) NOT NULL default '0',
  `accesslevel` int(1) NOT NULL default '0',
  PRIMARY KEY  (`demoID`)
) AUTO_INCREMENT=1 ");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "d"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "d"<br/>' . $transaction->getError());
    }
}

function update_base_5($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "files`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "files` (
  `fileID` int(11) NOT NULL AUTO_INCREMENT,
  `filecatID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filesize` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `downloads` int(11) NOT NULL default '0',
  `accesslevel` int(1) NOT NULL default '0',
  PRIMARY KEY  (`fileID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "files_categorys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "files_categorys` (
  `filecatID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`filecatID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_announcements`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_announcements` (
  `announceID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `intern` int(1) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `topic` varchar(255) NOT NULL default '',
  `announcement` text NOT NULL,
  PRIMARY KEY  (`announceID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_boards`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_boards` (
  `boardID` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `intern` int(1) NOT NULL default '0',
  `sort` int(2) NOT NULL default '0',
  PRIMARY KEY  (`boardID`)
) AUTO_INCREMENT=3 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_boards` (`boardID`, `category`, `name`, `info`, `intern`, `sort`) VALUES (1, 1, 'Main Board', 'The general public board', 0, 1)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_boards` (`boardID`, `category`, `name`, `info`, `intern`, `sort`) VALUES (2, 2, 'Main Board', 'The general intern board', 1, 1)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_categories`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_categories` (
  `catID` int(11) NOT NULL AUTO_INCREMENT,
  `intern` int(1) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`catID`)
) AUTO_INCREMENT=3 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_categories` (`catID`, `intern`, `name`, `info`, `sort`) VALUES (1, 0, 'Public Boards', '', 2)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_categories` (`catID`, `intern`, `name`, `info`, `sort`) VALUES (2, 1, 'Intern Boards', '', 3)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_moderators`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_moderators` (
  `modID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`modID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_notify`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_notify` (
  `notifyID` int(11) NOT NULL AUTO_INCREMENT,
  `topicID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`notifyID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_posts`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_posts` (
  `postID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `topicID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `poster` int(11) NOT NULL default '0',
  `message` text NOT NULL,
  PRIMARY KEY  (`postID`)
) AUTO_INCREMENT=1 ");


    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_ranks`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_ranks` (
  `rankID` int(11) NOT NULL AUTO_INCREMENT,
  `rank` varchar(255) NOT NULL default '',
  `pic` varchar(255) NOT NULL default '',
  `postmin` int(11) NOT NULL default '0',
  `postmax` int(11) NOT NULL default '0',
  `special` int(1) NULL DEFAULT '0',
  PRIMARY KEY  (`rankID`)
) AUTO_INCREMENT=9 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (1, 'Rank 1', 'rank1.gif', 0, 9)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (2, 'Rank 2', 'rank2.gif', 10, 24)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (3, 'Rank 3', 'rank3.gif', 25, 49)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (4, 'Rank 4', 'rank4.gif', 50, 199)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (5, 'Rank 5', 'rank5.gif', 200, 399)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (6, 'Rank 6', 'rank6.gif', 400, 2147483647)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (7, 'Administrator', 'admin.gif', 0, 0)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (8, 'Moderator', 'moderator.gif', 0, 0)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_topics`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_topics` (
  `topicID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `icon` varchar(255) NOT NULL default '',
  `intern` int(1) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `topic` varchar(255) NOT NULL default '',
  `lastdate` int(14) NOT NULL default '0',
  `lastposter` int(11) NOT NULL default '0',
  `replys` int(11) NOT NULL default '0',
  `views` int(11) NOT NULL default '0',
  `closed` int(1) NOT NULL default '0',
  `moveID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`topicID`)
) AUTO_INCREMENT=1 ");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "f"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "f"<br/>' . $transaction->getError());
    }
}

function update_base_6($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "games`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "games` (
  `gameID` int(3) NOT NULL AUTO_INCREMENT,
  `tag` varchar(5) NOT NULL default '',
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`gameID`)
) PACK_KEYS=0 AUTO_INCREMENT=8 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (1, 'cs', 'Counter-Strike')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (2, 'ut', 'Unreal Tournament')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (3, 'to', 'Tactical Ops')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (4, 'hl2', 'Halflife 2')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (5, 'wc3', 'Warcraft 3')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (6, 'hl', 'Halflife')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (7, 'bf', 'Battlefield')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "guestbook`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "guestbook` (
  `gbID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `hp` varchar(255) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`gbID`)
) AUTO_INCREMENT=1 ");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "g"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "g"<br/>' . $transaction->getError());
    }
}

function update_base_7($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "history`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "history` (
  `history` text NOT NULL
)");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "h"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "h"<br/>' . $transaction->getError());
    }
}

function update_base_8($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "links`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "links` (

  `linkID` int(11) NOT NULL AUTO_INCREMENT,
  `linkcatID` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `banner` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`linkID`)
) AUTO_INCREMENT=2 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "links` (`linkID`, `linkcatID`, `name`, `url`, `info`, `banner`) VALUES (1, 1, 'webSPELL.org', 'http://www.webspell.org', 'webspell.org: Webdesign und Webdevelopment', '1.gif')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "links_categorys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "links_categorys` (
  `linkcatID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`linkcatID`)
) AUTO_INCREMENT=2 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "links_categorys` (`linkcatID`, `name`) VALUES (1, 'Webdesign')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "linkus`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "linkus` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bannerID`)
) AUTO_INCREMENT=1 ");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "l"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "l"<br/>' . $transaction->getError());
    }
}


function update_base_9($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "messenger`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "messenger` (
  `messageID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `fromuser` int(11) NOT NULL default '0',
  `touser` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `message` text NOT NULL,
  `viewed` int(11) NOT NULL default '0',
  PRIMARY KEY  (`messageID`)
) AUTO_INCREMENT=1 ");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "m"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "m"<br/>' . $transaction->getError());
    }
}


function update_base_10($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "news`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news` (
  `newsID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `rubric` int(11) NOT NULL default '0',
  `lang1` char(2) NOT NULL default '',
  `headline1` varchar(255) NOT NULL default '',
  `content1` text NOT NULL,
  `lang2` char(2) NOT NULL default '',
  `headline2` varchar(255) NOT NULL default '',
  `content2` text NOT NULL,
  `screens` text NOT NULL,
  `poster` int(11) NOT NULL default '0',
  `link1` varchar(255) NOT NULL default '',
  `url1` varchar(255) NOT NULL default '',
  `window1` int(11) NOT NULL default '0',
  `link2` varchar(255) NOT NULL default '',
  `url2` varchar(255) NOT NULL default '',
  `window2` int(11) NOT NULL default '0',
  `link3` varchar(255) NOT NULL default '',
  `url3` varchar(255) NOT NULL default '',
  `window3` int(11) NOT NULL default '0',
  `link4` varchar(255) NOT NULL default '',
  `url4` varchar(255) NOT NULL default '',
  `window4` int(11) NOT NULL default '0',
  `saved` int(1) NOT NULL default '1',
  `published` int(11) NOT NULL default '0',
  `comments` int(1) NOT NULL default '0',
  `cwID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`newsID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "news_languages`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news_languages` (
  `langID` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(255) NOT NULL default '',
  `lang` char(2) NOT NULL default '',
  `alt` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`langID`)
) AUTO_INCREMENT=12 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (1, 'danish', 'dk', 'danish')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (2, 'dutch', 'nl', 'dutch')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (3, 'english', 'uk', 'english')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (4, 'finnish', 'fi', 'finnish')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (5, 'french', 'fr', 'french')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (6, 'german', 'de', 'german')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (7, 'hungarian', 'hu', 'hungarian')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (8, 'italian', 'it', 'italian')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (9, 'norwegian', 'no', 'norwegian')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (10, 'spanish', 'es', 'spanish')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (11, 'swedish', 'se', 'swedish')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "news_rubrics`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news_rubrics` (
  `rubricID` int(11) NOT NULL AUTO_INCREMENT,
  `rubric` varchar(255) NOT NULL default '',
  `pic` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rubricID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "newsletter`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "newsletter` (
  `email` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default ''
)");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "n"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "n"<br/>' . $transaction->getError());
    }
}


function update_base_11($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "partners`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "partners` (
  `partnerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `banner` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`partnerID`)
) PACK_KEYS=0 AUTO_INCREMENT=2 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "partners` (`partnerID`, `name`, `url`, `banner`, `sort`) VALUES (1, 'webSPELL 4', 'http://www.webspell.org', '1.gif', 1)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "poll`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "poll` (
  `pollID` int(10) NOT NULL AUTO_INCREMENT,
  `aktiv` int(1) NOT NULL default '0',
  `laufzeit` bigint(20) NOT NULL default '0',
  `titel` varchar(255) NOT NULL default '',
  `o1` varchar(255) NOT NULL default '',
  `o2` varchar(255) NOT NULL default '',
  `o3` varchar(255) NOT NULL default '',
  `o4` varchar(255) NOT NULL default '',
  `o5` varchar(255) NOT NULL default '',
  `o6` varchar(255) NOT NULL default '',
  `o7` varchar(255) NOT NULL default '',
  `o8` varchar(255) NOT NULL default '',
  `o9` varchar(255) NOT NULL default '',
  `o10` varchar(255) NOT NULL default '',
  `comments` int(1) NOT NULL default '0',
  PRIMARY KEY  (`pollID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "poll_votes`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "poll_votes` (
  `pollID` int(10) NOT NULL default '0',
  `o1` int(11) NOT NULL default '0',
  `o2` int(11) NOT NULL default '0',
  `o3` int(11) NOT NULL default '0',
  `o4` int(11) NOT NULL default '0',
  `o5` int(11) NOT NULL default '0',
  `o6` int(11) NOT NULL default '0',
  `o7` int(11) NOT NULL default '0',
  `o8` int(11) NOT NULL default '0',
  `o9` int(11) NOT NULL default '0',
  `o10` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollID`)
)");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "p"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "p"<br/>' . $transaction->getError());
    }
}


function update_base_12($_database)
{
    global $url;
    global $adminmail;
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "servers`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "servers` (
  `serverID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `game` char(3) NOT NULL default '',
  `info` text NOT NULL,
  PRIMARY KEY  (`serverID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "settings`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "settings` (
  `settingID` int(11) NOT NULL AUTO_INCREMENT,
  `hpurl` varchar(255) NOT NULL default '',
  `clanname` varchar(255) NOT NULL default '',
  `clantag` varchar(255) NOT NULL default '',
  `adminname` varchar(255) NOT NULL default '',
  `adminemail` varchar(255) NOT NULL default '',
  `news` int(11) NOT NULL default '0',
  `newsarchiv` int(11) NOT NULL default '0',
  `headlines` int(11) NOT NULL default '0',
  `headlineschars` int(11) NOT NULL default '0',
  `articles` int(11) NOT NULL default '0',
  `latestarticles` int(11) NOT NULL default '0',
  `articleschars` int(11) NOT NULL default '0',
  `clanwars` int(11) NOT NULL default '0',
  `results` int(11) NOT NULL default '0',
  `upcoming` int(11) NOT NULL default '0',
  `shoutbox` int(11) NOT NULL default '0',
  `sball` int(11) NOT NULL default '0',
  `sbrefresh` int(11) NOT NULL default '0',
  `topics` int(11) NOT NULL default '0',
  `posts` int(11) NOT NULL default '0',
  `latesttopics` int(11) NOT NULL default '0',
  `hideboards` int(1) NOT NULL default '0',
  `awards` int(11) NOT NULL default '0',
  `demos` int(11) NOT NULL default '0',
  `guestbook` int(11) NOT NULL default '0',
  `feedback` int(11) NOT NULL default '0',
  `messages` int(11) NOT NULL default '0',
  `users` int(11) NOT NULL default '0',
  `profilelast` int(11) NOT NULL default '0',
  `topnewsID` int(11) NOT NULL default '0',
<<<<<<< HEAD
=======
  `sessionduration` int(3) NOT NULL default '0',
  `sc_files` int(1) NOT NULL default '0',
  `sc_demos` int(1) NOT NULL default '0',
>>>>>>> dev
  PRIMARY KEY  (`settingID`)
) AUTO_INCREMENT=2 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "settings` (`settingID`, `hpurl`, `clanname`, `clantag`, `adminname`, `adminemail`, `news`, `newsarchiv`, `headlines`, `headlineschars`, `articles`, `latestarticles`, `articleschars`, `clanwars`, `results`, `upcoming`, `shoutbox`, `sball`, `sbrefresh`, `topics`, `posts`, `latesttopics`, `hideboards`, `awards`, `demos`, `guestbook`, `feedback`, `messages`, `users`, `profilelast`, `topnewsID`) VALUES
     (1, '" . $url . "', 'Clanname', 'MyClan', 'Admin-Name', '" . $adminmail . "', 10, 20, 10, 22, 20, 5, 20, 20, 5, 5, 5, 30, 60, 20, 10, 10, 1, 20, 20, 20, 20, 20, 60, 10, 27)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "shoutbox`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "shoutbox` (
  `shoutID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`shoutID`)
) AUTO_INCREMENT=1 ");


    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "sponsors`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "sponsors` (
  `sponsorID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `info` text NOT NULL,
  `banner` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sponsorID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "squads`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "squads` (
  `squadID` int(11) NOT NULL AUTO_INCREMENT,
  `gamesquad` int(11) NOT NULL default '1',
  `name` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`squadID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "squads_members`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "squads_members` (
  `sqmID` int(11) NOT NULL AUTO_INCREMENT,
  `squadID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `position` varchar(255) NOT NULL default '',
  `activity` int(1) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `joinmember` int(1) NOT NULL default '0',
  `warmember` int(1) NOT NULL default '0',
  PRIMARY KEY  (`sqmID`)
) AUTO_INCREMENT=1 ");


    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "styles`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "styles` (
  `styleID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL default '',
  `bgpage` varchar(255) NOT NULL default '',
  `border` varchar(255) NOT NULL default '',
  `bghead` varchar(255) NOT NULL default '',
  `bgcat` varchar(255) NOT NULL default '',
  `bg1` varchar(255) NOT NULL default '',
  `bg2` varchar(255) NOT NULL default '',
  `bg3` varchar(255) NOT NULL default '',
  `bg4` varchar(255) NOT NULL default '',
  `win` varchar(255) NOT NULL default '',
  `loose` varchar(255) NOT NULL default '',
  `draw` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`styleID`)
) AUTO_INCREMENT=2 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "styles` (`styleID`, `title`, `bgpage`, `border`, `bghead`, `bgcat`, `bg1`, `bg2`, `bg3`, `bg4`, `win`, `loose`, `draw`) VALUES (1, 'webSPELL v4', '#E6E6E6', '#666666', '#333333', '#FFFFFF', '#FFFFFF', '#F2F2F2', '#F2F2F2', '#D9D9D9', '#00CC00', '#DD0000', '#FF6600')");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "s"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "s"<br/>' . $transaction->getError());
    }
}

function update_base_13($_database)
{
    global $adminname;
    global $adminpassword;
    global $adminmail;
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "upcoming`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "upcoming` (
  `upID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `type` char(1) NOT NULL default '',
  `squad` int(11) NOT NULL default '0',
  `opponent` varchar(255) NOT NULL default '',
  `opptag` varchar(255) NOT NULL default '',
  `opphp` varchar(255) NOT NULL default '',
  `oppcountry` char(2) NOT NULL default '',
  `maps` varchar(255) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `leaguehp` varchar(255) NOT NULL default '',
  `warinfo` text NOT NULL,
  `short` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `enddate` int(14) NOT NULL default '0',
  `country` char(2) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `locationhp` varchar(255) NOT NULL default '',
  `dateinfo` text NOT NULL,
  PRIMARY KEY  (`upID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "upcoming_announce`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "upcoming_announce` (
  `annID` int(11) NOT NULL AUTO_INCREMENT,
  `upID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '',
  PRIMARY KEY  (`annID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `registerdate` int(14) NOT NULL default '0',
  `lastlogin` int(14) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `nickname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `sex` char(1) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `town` varchar(255) NOT NULL default '',
  `birthday` int(14) NOT NULL default '0',
  `icq` varchar(255) NOT NULL default '',
  `avatar` varchar(255) NOT NULL default '',
  `usertext` varchar(255) NOT NULL default '',
  `userpic` varchar(255) NOT NULL default '',
  `clantag` varchar(255) NOT NULL default '',
  `clanname` varchar(255) NOT NULL default '',
  `clanhp` varchar(255) NOT NULL default '',
  `clanirc` varchar(255) NOT NULL default '',
  `clanhistory` varchar(255) NOT NULL default '',
  `cpu` varchar(255) NOT NULL default '',
  `mainboard` varchar(255) NOT NULL default '',
  `ram` varchar(255) NOT NULL default '',
  `monitor` varchar(255) NOT NULL default '',
  `graphiccard` varchar(255) NOT NULL default '',
  `soundcard` varchar(255) NOT NULL default '',
  `connection` varchar(255) NOT NULL default '',
  `keyboard` varchar(255) NOT NULL default '',
  `mouse` varchar(255) NOT NULL default '',
  `mousepad` varchar(255) NOT NULL default '',
  `newsletter` int(1) NOT NULL default '1',
  `about` text NOT NULL,
  `pmgot` int(11) NOT NULL default '0',
  `pmsent` int(11) NOT NULL default '0',
  `visits` int(11) NOT NULL default '0',
  `banned` int(1) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default '',
  `topics` text NOT NULL,
  `articles` text NOT NULL,
  `demos` text NOT NULL,
  `special_rank` INT(11) NULL DEFAULT '0',
  PRIMARY KEY  (`userID`)
) AUTO_INCREMENT=2 ");


    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "user` (`userID`, `registerdate`, `lastlogin`, `username`, `password`, `nickname`, `email`, `firstname`, `lastname`, `sex`, `country`, `town`, `birthday`, `icq`, `avatar`, `usertext`, `userpic`, `clantag`, `clanname`, `clanhp`, `clanirc`, `clanhistory`, `cpu`, `mainboard`, `ram`, `monitor`, `graphiccard`, `soundcard`, `connection`, `keyboard`, `mouse`, `mousepad`, `newsletter`, `about`, `pmgot`, `pmsent`, `visits`, `banned`, `ip`, `topics`, `articles`, `demos`)
      VALUES (1, '" . time() . "', '" . time() . "', '" . $adminname . "', '" . $adminpassword . "', '" . $adminname . "', '" . $adminmail . "', '', '', 'u', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, '', 0, 0, 0, '', '', '', '', '')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_gbook`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_gbook` (
  `userID` int(11) NOT NULL default '0',
  `gbID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `hp` varchar(255) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`gbID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_groups` (
  `usgID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `news` int(1) NOT NULL default '0',
  `newsletter` int(1) NOT NULL default '0',
  `polls` int(1) NOT NULL default '0',
  `forum` int(1) NOT NULL default '0',
  `moderator` int(1) NOT NULL default '0',
  `internboards` int(1) NOT NULL default '0',
  `clanwars` int(1) NOT NULL default '0',
  `feedback` int(1) NOT NULL default '0',
  `user` int(1) NOT NULL default '0',
  `page` int(1) NOT NULL default '0',
  `files` int(1) NOT NULL default '0',
  `cash` int(1) NOT NULL default '0',
  PRIMARY KEY  (`usgID`)
) AUTO_INCREMENT=2 ");

    $transaction->addQuery("INSERT INTO " . PREFIX . "user_groups (usgID, userID, news, newsletter, polls, forum, moderator, internboards, clanwars, feedback, user, page, files)
VALUES (1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_visitors`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_visitors` (
  `visitID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `visitor` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  PRIMARY KEY  (`visitID`)
) AUTO_INCREMENT=1 ");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "u"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "u"<br/>' . $transaction->getError());
    }
}

function update_base_14($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "whoisonline`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "whoisonline` (
  `time` int(14) NOT NULL default '0',
  `ip` varchar(20) NOT NULL default '',
  `userID` int(11) NOT NULL default '0',
  `nickname` varchar(255) NOT NULL default '',
  `site` varchar(255) NOT NULL default ''
)");


    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "whowasonline`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "whowasonline` (
  `time` int(14) NOT NULL default '0',
  `ip` varchar(20) NOT NULL default '',
  `userID` int(11) NOT NULL default '0',
  `nickname` varchar(255) NOT NULL default '',
  `site` varchar(255) NOT NULL default ''
)");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "w"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "w"<br/>' . $transaction->getError());
    }
}

function update_31_4beta4($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "about`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "about` (
  `about` longtext NOT NULL
 )");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "awards` ADD `homepage` VARCHAR( 255 ) NOT NULL ,
 ADD `rang` INT DEFAULT '0' NOT NULL ,
 ADD `info` TEXT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "cash_box` ADD `squad` INT NOT NULL ,
 ADD `konto` TEXT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` ADD `linkpage` VARCHAR( 255 ) NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `game` `game` VARCHAR( 5 ) NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter_stats`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter_stats` (
  `dates` varchar(255) NOT NULL default '',
  `count` int(20) NOT NULL default '0'
 )");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "demos` ADD `accesslevel` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `accesslevel` INT( 1 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "games`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "games` (
  `gameID` int(3) NOT NULL AUTO_INCREMENT,
  `tag` varchar(5) NOT NULL default '',
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`gameID`)
 ) AUTO_INCREMENT=8 ");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (1, 'cs', 'Counter-Strike')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (2, 'ut', 'Unreal Tournament')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (3, 'to', 'Tactical Ops')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (4, 'hl2', 'Halflife 2')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (7, 'bf', 'Battlefield')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (5, 'wc3', 'Warcraft 3')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (6, 'hl', 'Halflife')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "linkus`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "linkus` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bannerID`)
 ) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "newsletter`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "newsletter` (
  `email` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default ''
 )");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `laufzeit` BIGINT(20) NOT NULL after `aktiv`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` DROP `showed`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` CHANGE `bannerrot` `profilelast` INT( 11 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `topnewsID` INT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads_members` ADD `joinmember` INT(1) DEFAULT '0' NOT NULL ,
 ADD `warmember` INT(1) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_gbook`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_gbook` (
  `userID` int(11) NOT NULL default '0',
  `gbID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `hp` varchar(255) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`gbID`)
 ) AUTO_INCREMENT=1 ");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` CHANGE `game` `game` CHAR( 3 ) NOT NULL");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 4');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4 Beta 4<br/>' . $transaction->getError());
    }
}

function update_4beta4_4beta5($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `sessionduration` INT( 3 ) NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `closed` INT( 1 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "lock`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "lock` (
  `time` INT NOT NULL ,
  `reason` TEXT NOT NULL
 )");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` ADD `intern` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "guestbook` ADD `admincomment` TEXT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `gb_info` INT( 1 ) DEFAULT '1' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "static`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "static` (
  `staticID` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR( 255 ) NOT NULL ,
  `accesslevel` INT( 1 ) NOT NULL ,
  PRIMARY KEY ( `staticID` )
  );");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 5');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4 Beta 5<br/>' . $transaction->getError());
    }

}

function update_4beta5_4beta6($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `mailonpm` INT( 1 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "imprint`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "imprint` (
`imprintID` INT NOT NULL AUTO_INCREMENT ,
`imprint` TEXT NOT NULL ,
PRIMARY KEY ( `imprintID` )
)");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `imprint` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `hosts` TEXT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` CHANGE `info` `info` TEXT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `homepage` VARCHAR( 255 ) NOT NULL AFTER `newsletter`");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 6');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4 Beta 6<br/>' . $transaction->getError());
    }

}

function update_4beta6_4final_1($_database)
{
    $transaction = new Transaction($_database);

    //files
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `votes` INT NOT NULL ,
ADD `points` INT NOT NULL ,
ADD `rating` INT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `mirrors` TEXT NOT NULL AFTER `file`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `files` TEXT NOT NULL AFTER `demos`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `picsize_l` INT DEFAULT '450' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `picsize_h` INT DEFAULT '500' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `poster` INT NOT NULL");

    //gallery
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "gallery`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "gallery` (
`galleryID` INT NOT NULL AUTO_INCREMENT ,
`userID` INT NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`date` INT( 14 ) NOT NULL ,
`groupID` INT NOT NULL ,
PRIMARY KEY ( `galleryID` )
)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "gallery_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "gallery_groups` (
`groupID` INT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 255 ) NOT NULL ,
`sort` INT NOT NULL ,
PRIMARY KEY ( `groupID` )
)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "gallery_pictures`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "gallery_pictures` (
`picID` INT NOT NULL AUTO_INCREMENT ,
`galleryID` INT NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`comment` TEXT NOT NULL ,
`views` INT DEFAULT '0' NOT NULL ,
`comments` INT( 1 ) DEFAULT '1' NOT NULL ,
PRIMARY KEY ( `picID` )
)");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `pictures` INT DEFAULT '12' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `publicadmin` INT( 1 ) DEFAULT '1' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` ADD `gallery` INT( 1 ) NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `thumbwidth` INT DEFAULT '130' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `usergalleries` INT( 1 ) DEFAULT '1' NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `gallery_pictures` TEXT NOT NULL AFTER `files`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "gallery_pictures` ADD `votes` INT NOT NULL ,
ADD `points` INT NOT NULL ,
ADD `rating` INT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `maxusergalleries` INT DEFAULT '1048576' NOT NULL");


    //country-list
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "countries`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "countries` (
`countryID` INT NOT NULL AUTO_INCREMENT ,
`country` VARCHAR( 255 ) NOT NULL ,
`short` VARCHAR( 3 ) NOT NULL ,
PRIMARY KEY ( `countryID` )
)");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "countries` ( `countryID` , `country` , `short` )
VALUES
('', 'Argentina', 'ar'),
('', 'Australia', 'au'),
('', 'Austria', 'at'),
('', 'Belgium', 'be'),
('', 'Bosnia Herzegowina', 'ba'),
('', 'Brazil', 'br'),
('', 'Bulgaria', 'bg'),
('', 'Canada', 'ca'),
('', 'Chile', 'cl'),
('', 'China', 'cn'),
('', 'Colombia', 'co'),
('', 'Czech Republic', 'cz'),
('', 'Croatia', 'hr'),
('', 'Cyprus', 'cy'),
('', 'Denmark', 'dk'),
('', 'Estonia', 'ee'),
('', 'Finland', 'fi'),
('', 'Faroe Islands', 'fo'),
('', 'France', 'fr'),
('', 'Germany', 'de'),
('', 'Greece', 'gr'),
('', 'Hungary', 'hu'),
('', 'Iceland', 'is'),
('', 'Ireland', 'ie'),
('', 'Israel', 'il'),
('', 'Italy', 'it'),
('', 'Japan', 'jp'),
('', 'Korea', 'kr'),
('', 'Latvia', 'lv'),
('', 'Lithuania', 'lt'),
('', 'Luxemburg', 'lu'),
('', 'Malaysia', 'my'),
('', 'Malta', 'mt'),
('', 'Netherlands', 'nl'),
('', 'Mexico', 'mx'),
('', 'Mongolia', 'mn'),
('', 'New Zealand', 'nz'),
('', 'Norway', 'no'),
('', 'Poland', 'pl'),
('', 'Portugal', 'pt'),
('', 'Romania', 'ro'),
('', 'Russian Federation', 'ru'),
('', 'Singapore', 'sg'),
('', 'Slovak Republic', 'sk'),
('', 'Slovenia', 'si'),
('', 'Taiwan', 'tw'),
('', 'South Africa', 'za'),
('', 'Spain', 'es'),
('', 'Sweden', 'se'),
('', 'Syria', 'sy'),
('', 'Switzerland', 'ch'),
('', 'Tibet', 'ti'),
('', 'Tunisia', 'tn'),
('', 'Turkey', 'tr'),
('', 'Ukraine', 'ua'),
('', 'United Kingdom', 'uk'),
('', 'USA', 'us'),
('', 'Venezuela', 've'),
('', 'Yugoslavia', 'rs'),
('', 'European Union', 'eu')");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 6 Part 1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4 Beta 6 Part 1<br/>' . $transaction->getError());
    }

}

function update_4beta6_4final_2($_database)
{
    $transaction = new Transaction($_database);


    //smileys
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "smileys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "smileys` (
  `smileyID` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `alt` varchar(255) NOT NULL default '',
  `pattern` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`smileyID`),
  UNIQUE KEY `name` (`name`)
) AUTO_INCREMENT=16");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (1, 'biggrin.gif', 'amüsiert', ':D')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (2, 'confused.gif', 'verwirrt', '?(')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (3, 'crying.gif', 'traurig', ';(')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (4, 'pleased.gif', 'erfreut', ':]')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (5, 'happy.gif', 'fröhlich', ':))')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (6, 'smile.gif', 'lächeln', ':)')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (7, 'wink.gif', 'zwinkern', ';)')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (8, 'frown.gif', 'unglücklich', ':(')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (9, 'tongue.gif', 'zunge raus', ':P')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (10, 'tongue2.gif', 'zunge raus', ';P')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (11, 'redface.gif', 'müde', ':O')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (12, 'cool.gif', 'cool', '8)')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (13, 'eek.gif', 'geschockt', '8o')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (14, 'evil.gif', 'teuflisch', ':evil:')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES (15, 'mad.gif', 'sauer', 'X(')");

    //clanwars
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` ADD `hltv` VARCHAR( 255 ) NOT NULL AFTER `server`");

    //polls
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `intern` INT( 1 ) DEFAULT '0' NOT NULL");

    //games
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "games` CHANGE `name` `name` VARCHAR( 255 ) NOT NULL");

    //servers
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` ADD `sort` INT DEFAULT '1' NOT NULL");

    //scrolltext
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "scrolltext`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "scrolltext` (
  `text` longtext NOT NULL,
  `delay` int(11) NOT NULL default '100',
  `direction` varchar(255) NOT NULL default ''
)");

    //superuser
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` ADD `super` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("UPDATE `" . PREFIX . "user_groups` SET super='1' WHERE userID='1' ");

    //bannerrotation
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "bannerrotation` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `banner` varchar(255) NOT NULL default '',
  `bannername` varchar(255) NOT NULL default '',
  `bannerurl` varchar(255) NOT NULL default '',
  `displayed` varchar(255) NOT NULL default '',
  `hits` int(11) default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`bannerID`),
  UNIQUE KEY `banner` (`banner`))");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `connection` `verbindung` VARCHAR( 255 ) NOT NULL DEFAULT ''");

    //converting clanwars-TABLE
    $clanwarQry = mysqli_query($_database, "SELECT * FROM " . PREFIX . "clanwars");
    $total = mysqli_num_rows($clanwarQry);
    if ($total) {
        while ($olddata = mysqli_fetch_array($clanwarQry)) {
            $id = $olddata['cwID'];
            $maps = $olddata['maps'];
            $scoreHome1 = $olddata['homescr1'];
            $scoreHome2 = $olddata['homescr2'];
            $scoreOpp1 = $olddata['oppscr1'];
            $scoreOpp2 = $olddata['oppscr2'];

            // do the convertation
            if (!empty($scoreHome2)) {
                $scoreHome = $scoreHome1 . '||' . $scoreHome2;
            } else {
                $scoreHome = $scoreHome1;
            }

            if (!empty($scoreOpp2)) {
                $scoreOpp = $scoreOpp1 . '||' . $scoreOpp2;
            } else {
                $scoreOpp = $scoreOpp1;
            }

            // update database, set new structure
            if (mysqli_query($_database, "ALTER TABLE `" . PREFIX . "clanwars` CHANGE `homescr1` `homescore` TEXT NOT NULL")) {
                $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `oppscr1` `oppscore` TEXT NOT NULL");
                if (mysqli_query($_database, "ALTER TABLE `" . PREFIX . "clanwars` DROP `homescr2`")) {
                    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` DROP `oppscr2`");
                    // save converted data into the database
                    $transaction->addQuery("UPDATE " . PREFIX . "clanwars SET homescore='" . $scoreHome . "', oppscore='" . $scoreOpp . "', maps='" . $maps . "' WHERE cwID='" . $id . "'");

                }
            }
        }
    } else {
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `homescr1` `homescore` TEXT");
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `oppscr1` `oppscore` TEXT");
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` DROP `homescr2`");
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` DROP `oppscr2`");
    }

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 6 Part 2');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4 Beta 6 Part 2<br/>' . $transaction->getError());
    }

}

function update_40000_40100($_database)
{
    $transaction = new Transaction($_database);

    // FAQ
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "faq`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "faq` (
  `faqID` int(11) NOT NULL AUTO_INCREMENT,
  `faqcatID` int(11) NOT NULL default '0',
  `question` varchar(255) NOT NULL default '',
  `answer` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`faqID`)
	) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "faq_categories`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "faq_categories` (
  `faqcatID` int(11) NOT NULL AUTO_INCREMENT,
  `faqcatname` varchar(255) NOT NULL default '',
  `description` TEXT NOT NULL,
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`faqcatID`)
	) AUTO_INCREMENT=1 ");

    // Admin Member Beschreibung
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `userdescription` TEXT NOT NULL");

    // Forum Sticky Function
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `sticky` INT(1) NOT NULL DEFAULT '0'");

    // birthday converter
    mysqli_query($_database, "ALTER TABLE `" . PREFIX . "user` ADD `birthday2` DATETIME NOT NULL AFTER `birthday`");
    $q = mysqli_query($_database, "SELECT userID, birthday FROM `" . PREFIX . "user`");
    while ($ds = mysqli_fetch_array($q)) {
        $transaction->addQuery("UPDATE `" . PREFIX . "user` SET birthday2='" . date("Y", $ds['birthday']) . "-" . date("m", $ds['birthday']) . "-" . date("d", $ds['birthday']) . "' WHERE userID='" . $ds['userID'] . "'");
    }
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` DROP `birthday`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `birthday2` `birthday` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.1<br/>' . $transaction->getError());
    }

}

function update_40100_40101($_database)
{
    $transaction = new Transaction($_database);
    //forum speedfix
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `topics` INT DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `posts` INT DEFAULT '0' NOT NULL");

    $q = mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_boards`");
    while ($ds = mysqli_fetch_array($q)) {
        $topics = mysqli_num_rows(mysqli_query($_database, "SELECT topicID FROM `" . PREFIX . "forum_topics` WHERE boardID='" . $ds['boardID'] . "' AND moveID='0'"));
        $posts = mysqli_num_rows(mysqli_query($_database, "SELECT postID FROM `" . PREFIX . "forum_posts` WHERE boardID='" . $ds['boardID'] . "'"));
        if (($posts - $topics) < 0) $posts = 0;
        else $posts = $posts - $topics;
        $transaction->addQuery("UPDATE `" . PREFIX . "forum_boards` SET topics='" . $topics . "' , posts='" . $posts . "' WHERE boardID='" . $ds['boardID'] . "'");
    }

    //add captcha
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "captcha` (
  `hash` varchar(255) NOT NULL default '',
  `captcha` int(11) NOT NULL default '0',
  `deltime` int(11) NOT NULL default '0',
  PRIMARY KEY  (`hash`)
	)");

    //useractivation
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `activated` varchar(255) NOT NULL default '1'");

    //counter: max. online
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "counter` ADD `maxonline` INT NOT NULL");

    //faq
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "faq` CHANGE `answer` `answer` TEXT NOT NULL");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.1.1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.1.1<br/>' . $transaction->getError());
    }

}

function update_40101_420_1($_database)
{
    $transaction = new Transaction($_database);

    //set default language
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `default_language` VARCHAR( 2 ) DEFAULT 'uk' NOT NULL");

    //user groups
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_forum_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_forum_groups` (
	  `usfgID` int(11) NOT NULL auto_increment,
	  `userID` int(11) NOT NULL default '0',
	  PRIMARY KEY  (`usfgID`)
	) AUTO_INCREMENT=0");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_groups` (
	  `fgrID` int(11) NOT NULL auto_increment,
	  `name` varchar(32) NOT NULL default '0',
	  PRIMARY KEY  (`fgrID`)
	) AUTO_INCREMENT=0");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "static` ADD `content` TEXT NOT NULL ");
    $get = mysqli_query($_database, "SELECT * FROM " . PREFIX . "static");
    while ($ds = mysqli_fetch_assoc($get)) {
        $file = "../html/" . $ds['name'];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (get_magic_quotes_gpc()) {
                $content = stripslashes($content);
            }
            if (function_exists("mysqli_real_escape_string")) {
                $content = mysqli_real_escape_string($_database, $content);
            } else {
                $content = addslashes($content);
            }
            $transaction->addQuery("UPDATE " . PREFIX . "static SET content='" . $content . "' WHERE staticID='" . $ds['staticID'] . "'");
            @unlink($file);
        }
    }
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads` CHANGE `info` `info` TEXT  NOT NULL ");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `writegrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `writegrps` text NOT NULL AFTER `intern`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_announcements` ADD `readgrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_categories` ADD `readgrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `readgrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `readgrps` text NOT NULL AFTER `intern`");

    //add group 1 and convert intern to group 1
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_forum_groups` ADD `1` INT( 1 ) NOT NULL ;");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "forum_groups` ( `fgrID` , `name` ) VALUES ('1', 'Old intern board users');");

    $transaction->addQuery("UPDATE `" . PREFIX . "forum_announcements` SET `readgrps` = '1' WHERE `intern` = 1");
    $transaction->addQuery("UPDATE `" . PREFIX . "forum_categories` SET `readgrps` = '1' WHERE `intern` = 1");
    $transaction->addQuery("UPDATE `" . PREFIX . "forum_boards` SET `readgrps` = '1', `writegrps` = '1' WHERE `intern` = 1");
    $transaction->addQuery("UPDATE `" . PREFIX . "forum_topics` SET `readgrps` = '1', `writegrps` = '1' WHERE `intern` = 1");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_announcements` DROP `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_categories` DROP `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` DROP `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` DROP `intern`");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 1<br/>' . $transaction->getError());
    }

}

function update_40101_420_2($_database)
{
    $transaction = new Transaction($_database);

    $sql = mysqli_query($_database, "SELECT `boardID` FROM `" . PREFIX . "forum_boards`");
    while ($ds = mysqli_fetch_array($sql)) {
        $anz_topics = mysqli_num_rows(mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_topics` WHERE `boardID` = " . $ds['boardID']));
        $anz_posts = mysqli_num_rows(mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_posts` WHERE `boardID` = " . $ds['boardID']));
        $anz_announcements = mysqli_num_rows(mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_announcements` WHERE `boardID` = " . $ds['boardID']));
        $anz_topics = $anz_topics + $anz_announcements;
        $transaction->addQuery("UPDATE `" . PREFIX . "forum_boards` SET `topics` = '" . $anz_topics . "', `posts` = '" . $anz_posts . "' WHERE `boardID` = " . $ds['boardID']);
    }

    //add all internboards user to "Intern board user"
    $sql = mysqli_query($_database, "SELECT `userID` FROM `" . PREFIX . "user_groups` WHERE `internboards` = '1'");
    while ($ds = mysqli_fetch_array($sql)) {
        if (mysqli_num_rows(mysqli_query($_database, "SELECT userID FROM `" . PREFIX . "user_forum_groups` WHERE `userID`=" . $ds['userID']))) $transaction->addQuery("UPDATE `" . PREFIX . "user_forum_groups` SET `1`='1' WHERE `userID`='" . $ds['userID'] . "'");
        else $transaction->addQuery("INSERT INTO `" . PREFIX . "user_forum_groups` (`userID`, `1`) VALUES (" . $ds['userID'] . ", 1)");
    }
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` DROP `internboards`");

    //add games cell to squads
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads` ADD `games` TEXT NOT NULL AFTER `gamesquad`");

    //add email_hide cell to user
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `email_hide` INT( 1 ) NOT NULL DEFAULT '1' AFTER `email`");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `email_hide` = '1' WHERE `email_hide` = '0'");

    //add userIDs cell to poll
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `userIDs` TEXT NOT NULL");

    //add table for banned ips
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "banned_ips` (
                   `banID` int(11) NOT NULL auto_increment,
                   `ip` varchar(255) NOT NULL,
                   `deltime` int(15) NOT NULL,
                   `reason` varchar(255) NULL,
                   PRIMARY KEY  (`banID`)
                 )");

    //add table for wrong logins
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "failed_login_attempts` (
                              `ip` varchar(255) NOT NULL default '',
                              `wrong` int(2) default '0',
                              PRIMARY KEY  (`ip`)
                            )");

    //news multilanguage
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news_contents` (
	`newsID` INT NOT NULL ,
	`language` VARCHAR( 2 ) NOT NULL ,
	`headline` VARCHAR( 255 ) NOT NULL ,
	`content` TEXT NOT NULL
	)");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 2');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 2<br/>' . $transaction->getError());
    }

}

function update_40101_420_3($_database)
{
    $transaction = new Transaction($_database);

    //news converter
    $q = mysqli_query($_database, "SELECT newsID, lang1, lang2, headline1, headline2, content1, content2 FROM `" . PREFIX . "news`");
    while ($ds = mysqli_fetch_array($q)) {
        if ($ds['headline1'] != "" or $ds['content1'] != "") {
            if (get_magic_quotes_gpc()) $content1 = str_replace('\r\n', "\n", $ds['content1']);
            else $content1 = str_replace('\r\n', "\n", mysqli_real_escape_string($_database, $ds['content1']));
            $transaction->addQuery("INSERT INTO " . PREFIX . "news_contents (newsID, language, headline, content) VALUES ('" . $ds['newsID'] . "', '" . mysqli_real_escape_string($_database, $ds['lang1']) . "', '" . mysqli_real_escape_string($_database, $ds['headline1']) . "', '" . $content1 . "')");
        }
        if ($ds['headline2'] != "" or $ds['content2'] != "") {
            if (get_magic_quotes_gpc()) $content2 = str_replace('\r\n', "\n", $ds['content2']);
            else $content2 = str_replace('\r\n', "\n", mysqli_real_escape_string($_database, $ds['content2']));
            $transaction->addQuery("INSERT INTO " . PREFIX . "news_contents (newsID, language, headline, content) VALUES ('" . $ds['newsID'] . "', '" . mysqli_real_escape_string($_database, $ds['lang2']) . "', '" . mysqli_real_escape_string($_database, $ds['headline2']) . "', '" . $content2 . "')");
        }
    }

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `lang1`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `headline1`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `content1`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `lang2`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `headline2`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `content2`");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 3');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 3<br/>' . $transaction->getError());
    }

}

function update_40101_420_4($_database)
{
    $transaction = new Transaction($_database);
    //article multipage
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "articles_contents` (
	  `articlesID` INT( 11 ) NOT NULL ,
	  `content` TEXT NOT NULL ,
	  `page` INT( 2 ) NOT NULL
	)");

    //article converter
    $sql = mysqli_query($_database, "SELECT articlesID, content FROM " . PREFIX . "articles");
    while ($ds = mysqli_fetch_array($sql)) {
        if (get_magic_quotes_gpc()) {
            $content = str_replace('\r\n', "\n", $ds['content']);
        } else {
            $content = str_replace('\r\n', "\n", mysqli_real_escape_string($_database, $ds['content']));
        }
        $transaction->addQuery("INSERT INTO " . PREFIX . "articles_contents (articlesID, content, page) VALUES ('" . $ds['articlesID'] . "', '" . $content . "', '0')");
    }

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `language` VARCHAR( 2 ) NOT NULL");

    //add news writer right column
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` ADD `news_writer` INT( 1 ) NOT NULL AFTER `news`");

    //add sub cat column
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files_categorys` ADD `subcatID` INT( 11 ) NOT NULL DEFAULT '0'");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 4');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 4<br/>' . $transaction->getError());
    }

}

function update_40101_420_5($_database)
{
    $transaction = new Transaction($_database);
    //announcement converter
    $sql = mysqli_query($_database, "SELECT * FROM " . PREFIX . "forum_announcements");
    while ($ds = mysqli_fetch_assoc($sql)) {
        $ds['topic'] = mysqli_real_escape_string($_database, $ds['topic']);
        $ds['announcement'] = mysqli_real_escape_string($_database, $ds['announcement']);
        $sql_board = mysqli_query($_database, "SELECT readgrps, writegrps
								FROM " . PREFIX . "forum_boards
								WHERE boardID = '" . $ds['boardID'] . "'");
        $rules = mysqli_fetch_assoc($sql_board);
        $transaction->addQuery("INSERT INTO " . PREFIX . "forum_topics
				( boardID, readgrps, writegrps, userID, date, lastdate, topic, lastposter, sticky)
				VALUES
				('" . $ds['boardID'] . "', '" . $rules['readgrps'] . "', '" . $rules['writegrps'] . "', '" . $ds['userID'] . "', '" . $ds['date'] . "', '" . $ds['date'] . "', '" . $ds['topic'] . "', '" . $ds['userID'] . "', '1')");
        $annID = mysqli_insert_id($_database);
        $transaction->addQuery("INSERT INTO " . PREFIX . "forum_posts
				( boardID, topicID, date, poster, message)
				VALUES
				( '" . $ds['boardID'] . "', '" . $annID . "', '" . $ds['date'] . "', '" . $ds['userID'] . "', '" . $ds['announcement'] . "')");
        $transaction->addQuery("UPDATE " . PREFIX . "forum_boards
					SET topics=topics+1
					WHERE boardID = '" . $ds['boardID'] . "' ");
        $transaction->addQuery("DELETE FROM " . PREFIX . "forum_announcements
					WHERE announceID='" . $ds['announceID'] . "' ");
    }

    // clanwar converter
    $get = mysqli_query($_database, "SELECT cwID, maps, hometeam, homescore, oppscore FROM " . PREFIX . "clanwars");
    while ($ds = mysqli_fetch_assoc($get)) {
        $maps = explode("||", $ds['maps']);
        if (function_exists("mysqli_real_escape_string")) {
            $theMaps = mysqli_real_escape_string($_database, serialize($maps));
        } else {
            $theMaps = addslashes(serialize($maps));
        }
        $hometeam = serialize(explode("|", $ds['hometeam']));
        $homescore = serialize(explode("||", $ds['homescore']));
        $oppscore = serialize(explode("||", $ds['oppscore']));
        $cwID = $ds['cwID'];
        $transaction->addQuery("UPDATE " . PREFIX . "clanwars SET maps='" . $theMaps . "', hometeam='" . $hometeam . "', homescore='" . $homescore . "', oppscore='" . $oppscore . "' WHERE cwID='" . $cwID . "'");
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 5');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 5<br/>' . $transaction->getError());
    }

}

function update_40101_420_6($_database)
{
    $transaction = new Transaction($_database);

    // converter board-speedup :)
    $transaction->addQuery("UPDATE " . PREFIX . "user SET topics='|'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `topics` `topics` TEXT NOT NULL");

    // update for email-change-activation
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `email_change` VARCHAR(255) NOT NULL AFTER `email_hide`,
				ADD `email_activate` VARCHAR(255) NOT NULL AFTER `email_change`");

    //add insertlinks cell to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `insertlinks` INT( 1 ) NOT NULL DEFAULT '1' AFTER `default_language`");

    //add search string min len and max wrong password cell to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `search_min_len` INT( 3 ) NOT NULL DEFAULT '3' AFTER `insertlinks`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `max_wrong_pw` INT( 2 ) NOT NULL DEFAULT '10' AFTER `search_min_len`");

    //set default sex to u(nknown)
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `sex` `sex` CHAR( 1 ) NOT NULL DEFAULT 'u' ");

    // convert banned to varchar
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `banned` `banned` VARCHAR(255) NULL DEFAULT NULL ");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET banned='perm' WHERE banned='1'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET banned=(NULL) WHERE banned='0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `ban_reason` VARCHAR(255) NOT NULL AFTER `banned`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` DROP `hideboards`");

    //add lastpostID to topics for latesttopics
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `lastpostID` INT NOT NULL DEFAULT '0' AFTER `lastposter`");

    //add color parameter for scrolltext
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "scrolltext` ADD `color` VARCHAR(7) NOT NULL DEFAULT '#000000'");

    //add new games
    $transaction->addQuery("UPDATE `" . PREFIX . "games` SET `name` = 'Battlefield 1942' WHERE `name` = 'Battlefield'");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "games` ( `gameID` , `tag` , `name` )
		VALUES
			('', 'aa', 'Americas Army'),
			('', 'aoe', 'Age of Empires 3'),
			('', 'b21', 'Battlefield 2142'),
			('', 'bf2', 'Battlefield 2'),
			('', 'bfv', 'Battlefield Vietnam'),
			('', 'c3d', 'Carom 3D'),
			('', 'cc3', 'Command &amp; Conquer'),
			('', 'cd2', 'Call of Duty 2'),
			('', 'cd4', 'Call of Duty 4'),
			('', 'cod', 'Call of Duty'),
			('', 'coh', 'Company of Heroes'),
			('', 'crw', 'Crysis Wars'),
			('', 'cry', 'Crysis'),
			('', 'css', 'Counter-Strike: Source'),
			('', 'cz', 'Counter-Strike: Condition Zero'),
			('', 'dds', 'Day of Defeat: Source'),
			('', 'dod', 'Day of Defeat'),
			('', 'dow', 'Dawn of War'),
			('', 'dta', 'DotA'),
			('', 'et', 'Enemy Territory'),
			('', 'fc', 'FarCry'),
			('', 'fer', 'F.E.A.R.'),
			('', 'fif', 'FIFA'),
			('', 'fl', 'Frontlines: Fuel of War'),
			('', 'hal', 'HALO'),
			('', 'jk2', 'Jedi Knight 2'),
			('', 'jk3', 'Jedi Knight 3'),
			('', 'lfs', 'Live for Speed'),
			('', 'lr2', 'LotR: Battle for Middle Earth 2'),
			('', 'lr', 'LotR: Battle for Middle Earth'),
			('', 'moh', 'Medal of Hornor'),
			('', 'nfs', 'Need for Speed'),
			('', 'pes', 'Pro Evolution Soccer'),
			('', 'q3', 'Quake 3'),
			('', 'q4', 'Quake 4'),
			('', 'ql', 'Quakelive'),
			('', 'rdg', 'Race Driver Grid'),
			('', 'sc2', 'Starcraft 2'),
			('', 'sc', 'Starcraft'),
			('', 'sof', 'Soldier of Fortune 2'),
			('', 'sw2', 'Star Wars: Battlefront 2'),
			('', 'sw', 'Star Wars: Battlefront'),
			('', 'swa', 'SWAT 4'),
			('', 'tf2', 'Team Fortress 2'),
			('', 'tf', 'Team Fortress'),
			('', 'tm', 'TrackMania'),
			('', 'ut3', 'Unreal Tournament 3'),
			('', 'ut4', 'Unreal Tournament 2004'),
			('', 'war', 'War Rock'),
			('', 'wic', 'World in Conflict'),
			('', 'wow', 'World of Warcraft'),
			('', 'wrs', 'Warsow')");

    //add new countries
    $transaction->addQuery("INSERT INTO `" . PREFIX . "countries` ( `countryID` , `country` , `short` )
		VALUES
			('', 'Albania', 'al'),
			('', 'Algeria', 'dz'),
			('', 'American Samoa', 'as'),
			('', 'Andorra', 'ad'),
			('', 'Angola', 'ao'),
			('', 'Anguilla', 'ai'),
			('', 'Antarctica', 'aq'),
			('', 'Antigua and Barbuda', 'ag'),
			('', 'Armenia', 'am'),
			('', 'Aruba', 'aw'),
			('', 'Azerbaijan', 'az'),
			('', 'Bahamas', 'bz'),
			('', 'Bahrain', 'bh'),
			('', 'Bangladesh', 'bd'),
			('', 'Barbados', 'bb'),
			('', 'Belarus', 'by'),
			('', 'Benelux', 'bx'),
			('', 'Benin', 'bj'),
			('', 'Bermuda', 'bm'),
			('', 'Bhutan', 'bt'),
			('', 'Bolivia', 'bo'),
			('', 'Botswana', 'bw'),
			('', 'Bouvet Island', 'bv'),
			('', 'British Indian Ocean Territory', 'io'),
			('', 'Brunei Darussalam', 'bn'),
			('', 'Burkina Faso', 'bf'),
			('', 'Burundi', 'bi'),
			('', 'Cambodia', 'kh'),
			('', 'Cameroon', 'cm'),
			('', 'Cape Verde', 'cv'),
			('', 'Cayman Islands', 'ky'),
			('', 'Central African Republic', 'cf'),
			('', 'Christmas Island', 'cx'),
			('', 'Cocos Islands', 'cc'),
			('', 'Comoros', 'km'),
			('', 'Congo', 'cg'),
			('', 'Cook Islands', 'ck'),
			('', 'Costa Rica', 'cr'),
			('', 'Cote d\'Ivoire', 'ci'),
			('', 'Cuba', 'cu'),
			('', 'Democratic Congo', 'cd'),
			('', 'Democratic Korea', 'kp'),
			('', 'Djibouti', 'dj'),
			('', 'Dominica', 'dm'),
			('', 'Dominican Republic', 'do'),
			('', 'East Timor', 'tp'),
			('', 'Ecuador', 'ec'),
			('', 'Egypt', 'eg'),
			('', 'El Salvador', 'sv'),
			('', 'England', 'en'),
			('', 'Eritrea', 'er'),
			('', 'Ethiopia', 'et'),
			('', 'Falkland Islands', 'fk'),
			('', 'Fiji', 'fj'),
			('', 'French Polynesia', 'pf'),
			('', 'French Southern Territories', 'tf'),
			('', 'Gabon', 'ga'),
			('', 'Gambia', 'gm'),
			('', 'Georgia', 'ge'),
			('', 'Ghana', 'gh'),
			('', 'Gibraltar', 'gi'),
			('', 'Greenland', 'gl'),
			('', 'Grenada', 'gd'),
			('', 'Guadeloupe', 'gp'),
			('', 'Guam', 'gu'),
			('', 'Guatemala', 'gt'),
			('', 'Guinea', 'gn'),
			('', 'Guinea-Bissau', 'gw'),
			('', 'Guyana', 'gy'),
			('', 'Haiti', 'ht'),
			('', 'Heard Islands', 'hm'),
			('', 'Holy See', 'va'),
			('', 'Honduras', 'hn'),
			('', 'Hong Kong', 'hk'),
			('', 'India', 'in'),
			('', 'Indonesia', 'id'),
			('', 'Iran', 'ir'),
			('', 'Iraq', 'iq'),
			('', 'Jamaica', 'jm'),
			('', 'Jordan', 'jo'),
			('', 'Kazakhstan', 'kz'),
			('', 'Kenia', 'ke'),
			('', 'Kiribati', 'ki'),
			('', 'Kuwait', 'kw'),
			('', 'Kyrgyzstan', 'kg'),
			('', 'Lao People\'s', 'la'),
			('', 'Lebanon', 'lb'),
			('', 'Lesotho', 'ls'),
			('', 'Liberia', 'lr'),
			('', 'Libyan Arab Jamahiriya', 'ly'),
			('', 'Liechtenstein', 'li'),
			('', 'Macau', 'mo'),
			('', 'Macedonia', 'mk'),
			('', 'Madagascar', 'mg'),
			('', 'Malawi', 'mw'),
			('', 'Maldives', 'mv'),
			('', 'Mali', 'ml'),
			('', 'Marshall Islands', 'mh'),
			('', 'Mauritania', 'mr'),
			('', 'Mauritius', 'mu'),
			('', 'Micronesia', 'fm'),
			('', 'Moldova', 'md'),
			('', 'Monaco', 'mc'),
			('', 'Montserrat', 'ms'),
			('', 'Morocco', 'ma'),
			('', 'Mozambique', 'mz'),
			('', 'Myanmar', 'mm'),
			('', 'Namibia', 'nb'),
			('', 'Nauru', 'nr'),
			('', 'Nepal', 'np'),
			('', 'Netherlands Antilles', 'an'),
			('', 'New Caledonia', 'nc'),
			('', 'Nicaragua', 'ni'),
			('', 'Nigeria', 'ng'),
			('', 'Niue', 'nu'),
			('', 'Norfolk Island', 'nf'),
			('', 'Northern Ireland', 'nx'),
			('', 'Northern Mariana Islands', 'mp'),
			('', 'Oman', 'om'),
			('', 'Pakistan', 'pk'),
			('', 'Palau', 'pw'),
			('', 'Palestinian', 'ps'),
			('', 'Panama', 'pa'),
			('', 'Papua New Guinea', 'pg'),
			('', 'Paraguay', 'py'),
			('', 'Peru', 'pe'),
			('', 'Philippines', 'ph'),
			('', 'Pitcairn', 'pn'),
			('', 'Puerto Rico', 'pr'),
			('', 'Qatar', 'qa'),
			('', 'Reunion', 're'),
			('', 'Rwanda', 'rw'),
			('', 'Saint Helena', 'sh'),
			('', 'Saint Kitts and Nevis', 'kn'),
			('', 'Saint Lucia', 'lc'),
			('', 'Saint Pierre and Miquelon', 'pm'),
			('', 'Saint Vincent', 'vc'),
			('', 'Samoa', 'ws'),
			('', 'San Marino', 'sm'),
			('', 'Sao Tome and Principe', 'st'),
			('', 'Saudi Arabia', 'sa'),
			('', 'Scotland', 'sc'),
			('', 'Senegal', 'sn'),
			('', 'Sierra Leone', 'sl'),
			('', 'Solomon Islands', 'sb'),
			('', 'Somalia', 'so'),
			('', 'South Georgia', 'gs'),
			('', 'Sri Lanka', 'lk'),
			('', 'Sudan', 'sd'),
			('', 'Suriname', 'sr'),
			('', 'Svalbard and Jan Mayen', 'sj'),
			('', 'Swaziland', 'sz'),
			('', 'Tajikistan', 'tj'),
			('', 'Tanzania', 'tz'),
			('', 'Thailand', 'th'),
			('', 'Togo', 'tg'),
			('', 'Tokelau', 'tk'),
			('', 'Tonga', 'to'),
			('', 'Trinidad and Tobago', 'tt'),
			('', 'Turkmenistan', 'tm'),
			('', 'Turks_and Caicos Islands', 'tc'),
			('', 'Tuvalu', 'tv'),
			('', 'Uganda', 'ug'),
			('', 'United Arab Emirates', 'ae'),
			('', 'Uruguay', 'uy'),
			('', 'Uzbekistan', 'uz'),
			('', 'Vanuatu', 'vu'),
			('', 'Vietnam', 'vn'),
			('', 'Virgin Islands (British)', 'vg'),
			('', 'Virgin Islands (USA)', 'vi'),
			('', 'Wales', 'wa'),
			('', 'Wallis and Futuna', 'wf'),
			('', 'Western Sahara', 'eh'),
			('', 'Yemen', 'ye'),
			('', 'Zambia', 'zm'),
			('', 'Zimbabwe', 'zw')");

    //add standard news languages for the existing language system
    $transaction->addQuery("INSERT INTO `" . PREFIX . "news_languages` ( `langID` , `language`, `lang` , `alt` )
		VALUES
			('', 'czech', 'cz', 'czech'),
			('', 'croatian', 'hr', 'croatian'),
			('', 'lithuanian', 'lt', 'lithuanian'),
			('', 'polish', 'pl', 'polish'),
			('', 'portugese', 'pt', 'portugese'),
			('', 'slovak', 'sk', 'slovak')");

    //add sponsors click counter, small banner, mainsponsor option, sort and display choice
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "sponsors` ADD `banner_small` varchar(255) NOT NULL default '', ADD `displayed` varchar(255) NOT NULL default '1', ADD `mainsponsor` varchar(255) NOT NULL default '0', ADD `hits` int(11) default '0', ADD `date` int(14) NOT NULL default '0', ADD `sort` int(11) NOT NULL default '1' AFTER `banner`");
    $transaction->addQuery("UPDATE `" . PREFIX . "sponsors` SET `date` = '" . time() . "' WHERE `date` = '0'");

    //add parnters click counter and display choice
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "partners` ADD `displayed` varchar(255) NOT NULL default '1', ADD `hits` int(11) default '0', ADD `date` int(14) NOT NULL default '0' AFTER `banner`");
    $transaction->addQuery("UPDATE `" . PREFIX . "partners` SET `date` = '" . time() . "' WHERE `date` = '0'");

    //add latesttopicchars to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `latesttopicchars` int(11) NOT NULL default '0' AFTER `latesttopics`");
    $transaction->addQuery("UPDATE `" . PREFIX . "settings` SET `latesttopicchars` = '18' WHERE `latesttopicchars` = '0'");

    //add maxtopnewschars to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `topnewschars` int(11) NOT NULL default '0' AFTER `headlineschars`");
    $transaction->addQuery("UPDATE `" . PREFIX . "settings` SET `topnewschars` = '200' WHERE `topnewschars` = '0'");

    //add captcha and bancheck to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_math` int(1) NOT NULL default '2' AFTER `max_wrong_pw`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_bgcol` varchar(7) NOT NULL default '#FFFFFF' AFTER `captcha_math`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_fontcol` varchar(7) NOT NULL default '#000000' AFTER `captcha_bgcol`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_type` int(1) NOT NULL default '2' AFTER `captcha_fontcol`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_noise` int(3) NOT NULL default '100' AFTER `captcha_type`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_linenoise` int(2) NOT NULL default '10' AFTER `captcha_noise`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `bancheck` INT( 13 ) NOT NULL");

    //add small icon to squads
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads` ADD `icon_small` varchar(255) NOT NULL default '' AFTER `icon`");

    // add autoresize to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `autoresize` int(1) NOT NULL default '2' AFTER `captcha_linenoise`");

    // add contacts for mail formular
    $getadminmail = mysqli_fetch_array(mysqli_query($_database, "SELECT adminemail FROM `" . PREFIX . "settings`"));
    $adminmail = $getadminmail['adminemail'];

    $transaction->addQuery("CREATE TABLE IF NOT EXISTS `" . PREFIX . "contact` (
	  `contactID` int(11) NOT NULL auto_increment,
	  `name` varchar(100) NOT NULL,
	  `email` varchar(200) NOT NULL,
	  `sort` int(11) NOT NULL default '0',
	  	PRIMARY KEY ( `contactID` )
	  ) AUTO_INCREMENT=2 ;");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "contact` (`contactID`, `name`, `email`, `sort`) VALUES
	  (1, 'Administrator', '" . $adminmail . "', 1);");

    // add date to faqs
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "faq` ADD `date` int(14) NOT NULL default '0' AFTER `faqcatID`");
    $transaction->addQuery("UPDATE `" . PREFIX . "faq` SET `date` = '" . time() . "' WHERE `date` = '0'");

    // remove nickname from who is/was online
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "whoisonline` DROP `nickname`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "whowasonline` DROP `nickname`");

    // set default to none in user table
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clantag` `clantag` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clanname` `clanname` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clanirc` `clanirc` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clanhistory` `clanhistory` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `cpu` `cpu` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `mainboard` `mainboard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `ram` `ram` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `monitor` `monitor` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `graphiccard` `graphiccard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `soundcard` `soundcard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `keyboard` `keyboard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `mouse` `mouse` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `mousepad` `mousepad` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `verbindung` `verbindung` VARCHAR( 255 ) NOT NULL default ''");

    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clantag` = '' WHERE `clantag` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clanname` = '' WHERE `clanname` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clanirc` = '' WHERE `clanirc` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clanhistory` = '' WHERE `clanhistory` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `cpu` = '' WHERE `cpu` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `mainboard` = '' WHERE `mainboard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `ram` = '' WHERE `ram` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `monitor` = '' WHERE `monitor` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `graphiccard` = '' WHERE `graphiccard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `soundcard` = '' WHERE `soundcard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `verbindung` = '' WHERE `verbindung` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `keyboard` = '' WHERE `keyboard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `mouse` = '' WHERE `mouse` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `mousepad` = '' WHERE `mousepad` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `verbindung` = '' WHERE `verbindung` = 'n/a'");

    // Smilie update
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `pattern` = '=)' WHERE `pattern` = ':))'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `pattern` = ':p' WHERE `pattern` = ':P'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `pattern` = ';p' WHERE `pattern` = ';P'");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` VALUES ('', 'crazy.gif', 'crazy', '^^')");

    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'amused' WHERE `pattern` = ':D'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'confused' WHERE `pattern` = '?('");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'sad' WHERE `pattern` = ';('");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'pleased' WHERE `pattern` = ':]'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'happy' WHERE `pattern` = '=)'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'smiling' WHERE `pattern` = ':)'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'wink' WHERE `pattern` = ';)'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'unhappy' WHERE `pattern` = ':('");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'tongue' WHERE `pattern` = ':p'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'funny' WHERE `pattern` = ';p'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'tired' WHERE `pattern` = ':O'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'cool' WHERE `pattern` = '8)'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'shocked' WHERE `pattern` = '8o'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'devilish' WHERE `pattern` = ':evil:'");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'angry' WHERE `pattern` = 'X('");
    $transaction->addQuery("UPDATE `" . PREFIX . "smileys` SET `alt` = 'crazy' WHERE `pattern` = '^^'");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 6');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 6<br/>' . $transaction->getError());
    }

}

function update_40101_420_7($_database)
{
    $transaction = new Transaction($_database);
    //Reverter of wrong escapes
    if (get_magic_quotes_gpc()) {
        @ini_set("max_execution_time", "300");
        @set_time_limit(300);

        // Fix About Us
        $get = mysqli_query($_database, "SELECT about FROM " . PREFIX . "about");
        if (mysqli_num_rows($get)) {
            $ds = mysqli_fetch_assoc($get);
            $transaction->addQuery("UPDATE " . PREFIX . "about SET about='" . $ds['about'] . "'");
        }

        // Fix History
        $get = mysqli_query($_database, "SELECT history FROM " . PREFIX . "history");
        if (mysqli_num_rows($get)) {
            $ds = mysqli_fetch_assoc($get);
            $transaction->addQuery("UPDATE " . PREFIX . "history SET history='" . $ds['history'] . "'");
        }

        // Fix Comments
        $get = mysqli_query($_database, "SELECT commentID, nickname, comment, url, email FROM " . PREFIX . "comments");
        while ($ds = mysqli_fetch_assoc($get)) {
            $transaction->addQuery("UPDATE " . PREFIX . "comments SET 	nickname='" . $ds['nickname'] . "',
															comment='" . $ds['comment'] . "',
															url='" . $ds['url'] . "',
															email='" . $ds['email'] . "'
															WHERE commentID='" . $ds['commentID'] . "'");
        }
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 7');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 7<br/>' . $transaction->getError());
    }

}

function update_40101_420_8($_database)
{
    $transaction = new Transaction($_database);

    // Fix Articles
    $get = mysqli_query($_database, "SELECT articlesID, title, url1, url2, url3, url4 FROM " . PREFIX . "articles");
    while ($ds = mysqli_fetch_assoc($get)) {
        $title = $ds['title'];
        $url1 = $ds['url1'];
        $url2 = $ds['url2'];
        $url3 = $ds['url3'];
        $url4 = $ds['url4'];
        $transaction->addQuery("UPDATE " . PREFIX . "articles SET title='" . $title . "', url1='" . $url1 . "', url2='" . $url2 . "', url3='" . $url3 . "', url4='" . $url4 . "' WHERE articlesID='" . $ds['articlesID'] . "'");
    }

    // Fix Profiles
    $get = mysqli_query($_database, "SELECT  userID, nickname, email, firstname, lastname, sex, country, town,
									birthday, icq, usertext, clantag, clanname, clanhp,
									clanirc, clanhistory, cpu, mainboard, ram, monitor,
									graphiccard, soundcard, verbindung, keyboard, mouse,
									mousepad, mailonpm, newsletter, homepage, about FROM " . PREFIX . "user");
    while ($ds = mysqli_fetch_assoc($get)) {
        $id = $ds['userID'];
        unset($ds['userID']);
        $string = '';
        foreach ($ds as $key => $value) {
            $string .= $key . "='" . $value . "', ";
        }
        $set = substr($string, 0, -2);
        $transaction->addQuery("UPDATE " . PREFIX . "user SET " . $set . " WHERE userID='" . $id . "'");
    }

    @ini_set("max_execution_time", "300");
    @set_time_limit(300);

    // Fix Userguestbook
    $get = mysqli_query($_database, "SELECT gbID, name, email, hp, comment FROM " . PREFIX . "user_gbook");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "user_gbook SET name='" . $ds['name'] . "',
															comment='" . $ds['comment'] . "',
															hp='" . $ds['hp'] . "',
															email='" . $ds['email'] . "'
															WHERE gbID='" . $ds['gbID'] . "'");
    }

    // Fix Messenges
    $get = mysqli_query($_database, "SELECT messageID, message FROM " . PREFIX . "messenger");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "messenger SET message='" . $ds['message'] . "' WHERE messageID='" . $ds['messageID'] . "'");
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 8');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 8<br/>' . $transaction->getError());
    }

}

function update_40101_420_9($_database)
{
    $transaction = new Transaction($_database);

    @ini_set("max_execution_time", "300");
    @set_time_limit(300);

    // Fix Forum
    $get = mysqli_query($_database, "SELECT topicID, topic FROM " . PREFIX . "forum_topics");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "forum_topics SET topic='" . $ds['topic'] . "' WHERE topicID='" . $ds['topicID'] . "'");
    }

    @ini_set("max_execution_time", "300");
    @set_time_limit(300);

    $get = mysqli_query($_database, "SELECT postID, message FROM " . PREFIX . "forum_posts");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "forum_posts SET message='" . $ds['message'] . "' WHERE postID='" . $ds['postID'] . "'");
    }

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 9');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.2 Part 9<br/>' . $transaction->getError());
    }
}

function update_420_430_1($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_posts_spam`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_posts_spam` (
    `postID` int(11) NOT NULL AUTO_INCREMENT,
    `boardID` int(11) NOT NULL default '0',
    `topicID` int(11) NOT NULL default '0',
    `date` int(14) NOT NULL default '0',
    `poster` int(11) NOT NULL default '0',
    `message` text NOT NULL,
    `rating` varchar(255) NOT NULL default '',
    PRIMARY KEY (`postID`)
    ) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_topics_spam`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_topics_spam` (
    `topicID` int(11) NOT NULL AUTO_INCREMENT,
    `boardID` int(11) NOT NULL,
    `userID` int(11) NOT NULL,
    `date` int(14) NOT NULL,
    `icon` varchar(255) NOT NULL,
    `topic` varchar(255) NOT NULL,
    `sticky` int(1) NOT NULL,
    `message` text NOT NULL,
    `rating` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`topicID`)
    ) AUTO_INCREMENT=1 ");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "comments_spam`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "comments_spam` (
    `commentID` int(11) NOT NULL AUTO_INCREMENT,
    `parentID` int(11) NOT NULL DEFAULT '0',
    `type` char(2) NOT NULL DEFAULT '',
    `userID` int(11) NOT NULL DEFAULT '0',
    `nickname` varchar(255) NOT NULL DEFAULT '',
    `date` int(14) NOT NULL DEFAULT '0',
    `comment` text NOT NULL,
    `url` varchar(255) NOT NULL DEFAULT '',
    `email` varchar(255) NOT NULL DEFAULT '',
    `ip` varchar(255) NOT NULL DEFAULT '',
    `rating` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`commentID`)
    ) AUTO_INCREMENT=1 ");

    $transaction->addQuery("CREATE TABLE `" . PREFIX . "api_log` (
    `date` int(11) NOT NULL,
    `message` varchar(255) NOT NULL,
    `data` text NOT NULL
    )");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spam_check` int(1) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `detect_language` int(1) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spamapikey` varchar(32) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spamapihost` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spammaxposts` int(11) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spamapiblockerror` int(1) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `date_format` varchar(255) NOT NULL default 'd.m.Y'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `time_format` varchar(255) NOT NULL default 'H:i'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `user_guestbook` int(1) NOT NULL default '1'");

    $transaction->addQuery("UPDATE `" . PREFIX . "settings` SET spamapihost='https://api.webspell.org/'");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `date_format` varchar(255) NOT NULL default 'd.m.Y'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `time_format` varchar(255) NOT NULL default 'H:i'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `hdd` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `headset` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `user_guestbook` int(1) NOT NULL default '1'");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `modRewrite` int(1) NOT NULL default '0'");

    //add new languages for the existing language system
    $transaction->addQuery("INSERT INTO `" . PREFIX . "news_languages` ( `langID` , `language`, `lang` , `alt` )
    VALUES
      ('', 'arabic', 'sa', 'arabic'),
      ('', 'bosnian', 'ba', 'bosnian'),
      ('', 'estonian', 'ee', 'estonian'),
      ('', 'georgian', 'ge', 'georgian'),
      ('', 'macedonian', 'mk', 'macedonian'),
      ('', 'persian', 'ir', 'persian'),
      ('', 'romanian', 'ro', 'romanian'),
      ('', 'russian', 'ru', 'russian'),
      ('', 'serbian', 'rs', 'serbian'),
      ('', 'slovenian', 'si', 'slovenian'),
      ('', 'latvian', 'lv', 'latvian'),
      ('', 'finnish', 'fi', 'finnish'),
      ('', 'turkish', 'tr', 'turkish'),
      ('', 'albanian', 'al', 'albanian'),
      ('', 'bulgarian', 'bg', 'bulgarian'),
      ('', 'greek', 'gr', 'greek'),
      ('', 'ukrainian', 'ua', 'ukrainian'),
      ('', 'luxembourgish', 'lu', 'luxembourgish'),
      ('', 'afrikaans', 'za', 'afrikaans')");

    //edit countries
    $transaction->addQuery("INSERT INTO `" . PREFIX . "countries` ( `countryID` , `country` , `short` )
    VALUES
     ('', 'Afghanistan', 'af'),
     ('', 'Aland Islands', 'ax'),
     ('', 'Bahamas', 'bs'),
     ('', 'Saint Barthelemy', 'bl'),
     ('', 'Caribbean Netherlands', 'bq'),
     ('', 'Chad', 'td'),
     ('', 'Curacao', 'cw'),
     ('', 'French Guiana', 'gf'),
     ('', 'Guernsey', 'gg'),
     ('', 'Equatorial Guinea', 'gq'),
     ('', 'Canary Islands', 'ic'),
     ('', 'Isle of Man', 'im'),
     ('', 'Jersey', 'je'),
     ('', 'Kosovo', 'xk'),
     ('', 'Martinique', 'mq'),
     ('', 'Mayotte', 'yt'),
     ('', 'Montenegro', 'me'),
     ('', 'Namibia', 'na'),
     ('', 'Niger', 'ne'),
     ('', 'Saint Barthelemy', 'bl'),
     ('', 'Saint Martin', 'mf'),
     ('', 'Serbia', 'rs'),
     ('', 'South Sudan', 'ss'),
     ('', 'Timor-Leste', 'tl')
  ");

    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Bosnia and Herzegowina' WHERE short = 'ba'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Brunei' WHERE short = 'bn'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Belize' WHERE short = 'bz'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Ivory Coast' WHERE short = 'ci'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='South Georgia and the South Sandwich Islands' WHERE short = 'gs'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Kenya' WHERE short = 'ke'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='North Korea' WHERE short = 'kp'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='South Korea' WHERE short = 'kr'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Burma' WHERE short = 'mm'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Laos' WHERE short = 'la'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Libya' WHERE short = 'ly'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Russia' WHERE short = 'ru'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Seychelles' WHERE short = 'sc'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Slovakia' WHERE short = 'sk'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Turks and Caicos Islands' WHERE short = 'tc'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Vatican City' WHERE short = 'va'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Luxembourg' WHERE short = 'lu'");

    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'bv'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'gp'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'hm'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'io'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'nb'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'nx'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'pm'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'sj'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'ti'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'wa'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'yu'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'tp'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = ''");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.3 Part 1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.3 Part 1<br/>' . $transaction->getError());
    }
}

function update_420_430_2($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("CREATE TABLE `" . PREFIX . "tags` (
  `rel` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL
)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "addon_categories`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "addon_categories` (
  `catID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `default` int(1) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`catID`)
) AUTO_INCREMENT=9 ");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('1', 'main', '1', '1');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('2', 'user', '1', '2');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('3', 'spam', '1', '3');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('4', 'rubrics', '1', '4');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('5', 'settings', '1', '5');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('6', 'content', '1', '6');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('7', 'forum', '1', '7');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "addon_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('8', 'gallery', '1', '8');");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "addon_links`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "addon_links` (
  `linkID` int(11) NOT NULL AUTO_INCREMENT,
  `catID` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `accesslevel` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`linkID`)
) AUTO_INCREMENT=1 ");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "countries` ADD `fav` int(1) NOT NULL default '0'");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "modrewrite` (
    `ruleID` int(11) NOT NULL AUTO_INCREMENT,
    `regex` text NOT NULL,
    `link` text NOT NULL,
    `fields` text NOT NULL,
    `replace_regex` text NOT NULL,
    `replace_result` text NOT NULL,
    `rebuild_regex` text NOT NULL,
    `rebuild_result` text NOT NULL,
    PRIMARY KEY (`ruleID`)
    ) AUTO_INCREMENT=1 ");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('about.html','index.php?site=about','a:0:{}','index\\\\.php\\\\?site=about','about.html','about\\\\.html','index.php?site=about')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles.html','index.php?site=articles','a:0:{}','index\\\\.php\\\\?site=articles','articles.html','articles\\\\.html','index.php?site=articles')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{articlesID}.html','index.php?site=articles&action=show&articlesID={articlesID}','a:1:{s:10:\"articlesID\";s:7:\"integer\";}','index\\\\.php\\\\?site=articles[&|&amp;]*action=show[&|&amp;]*articlesID=([0-9]+)','articles/$3.html','articles\\\\/([0-9]+?)\\\\.html','index.php?site=articles&action=show&articlesID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{articlesID}/edit.html','articles.php?action=edit&articlesID={articlesID}','a:1:{s:10:\"articlesID\";s:7:\"integer\";}','articles\\\\.php\\\\?action=edit[&|&amp;]*articlesID=([0-9]+)','articles/$3/edit.html','articles\\\\/([0-9]+?)\\\\/edit\\\\.html','articles.php?action=edit&articlesID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{page}/{articlesID}.html','index.php?site=articles&action=show&articlesID={articlesID}&page={page}','a:2:{s:10:\"articlesID\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";}','index\\\\.php\\\\?site=articles[&|&amp;]*action=show[&|&amp;]*articlesID=([0-9]+)[&|&amp;]*page=([0-9]+)','articles/$4/$3.html','articles\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=articles&action=show&articlesID=$2&page=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{sort}/{type}/{page}.html','index.php?site=articles&sort={sort}&type={type}&page={page}','a:3:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";}','index\\\\.php\\\\?site=articles[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*page=([0-9]+)','articles/$3/$4/$5.html','articles\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=articles&sort=$1&type=$2&page=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{sort}/{type}/1.html','index.php?site=articles&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=articles[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','articles/$3/$4/1.html','articles\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=articles&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards.html','index.php?site=awards','a:0:{}','index\\\\.php\\\\?site=awards','awards.html','awards\\\\.html','index.php?site=awards')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{awardID}.html','index.php?site=awards&action=details&awardID={awardID}','a:1:{s:7:\"awardID\";s:7:\"integer\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=details[&|&amp;]*awardID=([0-9]+)','awards/$3.html','awards\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=details&awardID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{sort}/{type}/{page}.html','index.php?site=awards&sort={sort}&type={type}&page={page}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*page=([0-9]+)','awards/$3/$4/$5.html','awards\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&sort=$1&type=$2&page=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{sort}/{type}/{page}.html','index.php?site=awards&page={page}&sort={sort}&type={type}','a:3:{s:4:\"type\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$4/$5/$3.html','awards\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&page=$3&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{sort}/{type}/1.html','index.php?site=awards&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$3/$4/1.html','awards\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=awards&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{squadID}/{sort}/{type}/{page}.html','index.php?site=awards&action=showsquad&squadID={squadID}&sort={sort}&type={type}&page={page}','a:4:{s:7:\"squadID\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=showsquad[&|&amp;]*squadID=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*page=([0-9]+)','awards/$3/$4/$5/$6.html','awards\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=showsquad&squadID=$1&sort=$2&type=$3&page=$4')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{squadID}/{sort}/{type}/{page}.html','index.php?site=awards&action=showsquad&squadID={squadID}&page={page}&sort={sort}&type={type}','a:4:{s:7:\"squadID\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=showsquad[&|&amp;]*squadID=([0-9]+)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$3/$5/$6/$4.html','awards\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=showsquad&squadID=$1&page=$4&sort=$2&type=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{squadID}/{sort}/{type}/1.html','index.php?site=awards&action=showsquad&squadID={squadID}&sort={sort}&type={type}','a:3:{s:7:\"squadID\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=showsquad[&|&amp;]*squadID=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$3/$4/$5/1.html','awards\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=awards&action=showsquad&squadID=$1&sort=$2&type=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/edit/{awardID}.html','index.php?site=awards&action=edit&awardID={awardID}','a:1:{s:7:\"awardID\";s:7:\"integer\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=edit[&|&amp;]*awardID=([0-9]+)','awards/edit/$3.html','awards\\\\/edit\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=edit&awardID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/new.html','index.php?site=awards&action=new','a:0:{}','index\\\\.php\\\\?site=awards[&|&amp;]*action=new','awards/new.html','awards\\\\/new\\\\.html','index.php?site=awards&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('buddies.html','index.php?site=buddies','a:0:{}','index\\\\.php\\\\?site=buddies','buddies.html','buddies\\\\.html','index.php?site=buddies')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar.html','index.php?site=calendar','a:0:{}','index\\\\.php\\\\?site=calendar','calendar.html','calendar\\\\.html','index.php?site=calendar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar.html#event','index.php?site=calendar#event','a:0:{}','index\\\\.php\\\\?site=calendar#event','calendar.html#event','calendar\\\\.html#event','index.php?site=calendar#event')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/{year}/{month}.html','index.php?site=calendar&month={month}&year={year}','a:2:{s:5:\"month\";s:7:\"integer\";s:4:\"year\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*month=([0-9]+)[&|&amp;]*year=([0-9]+)','calendar/$4/$3.html','calendar\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&month=$2&year=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/{year}/{month}/{tag}.html','index.php?site=calendar&tag={tag}&month={month}&year={year}#event','a:3:{s:3:\"tag\";s:7:\"integer\";s:5:\"month\";s:7:\"integer\";s:4:\"year\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*tag=([0-9]+)[&|&amp;]*month=([0-9]+)[&|&amp;]*year=([0-9]+)#event','calendar/$5/$4/$3.html','calendar\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&tag=$3&month=$2&year=$1#event')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/{year}/{month}/{tag}.html','index.php?site=calendar&tag={tag}&month={month}&year={year}','a:3:{s:3:\"tag\";s:7:\"integer\";s:5:\"month\";s:7:\"integer\";s:4:\"year\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*tag=([0-9]+)[&|&amp;]*month=([0-9]+)[&|&amp;]*year=([0-9]+)','calendar/$5/$4/$3.html','calendar\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&tag=$3&month=$2&year=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/adddate.html','index.php?site=calendar&action=adddate','a:0:{}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=adddate','calendar/adddate.html','calendar\\\\/adddate\\\\.html','index.php?site=calendar&action=adddate')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/addwar.html','index.php?site=calendar&action=addwar','a:0:{}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=addwar','calendar/addwar.html','calendar\\\\/addwar\\\\.html','index.php?site=calendar&action=addwar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/announce/{upID}.html','index.php?site=calendar&action=announce&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=announce[&|&amp;]*upID=([0-9]+)','calendar/announce/$3.html','calendar\\\\/announce\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&action=announce&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/delete/{upID}.html','calendar.php?action=delete&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','calendar\\\\.php\\\\?action=delete[&|&amp;]*upID=([0-9]+)','calendar/delete/$3.html','calendar\\\\/delete\\\\/([0-9]+?)\\\\.html','calendar.php?action=delete&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/editdate/{upID}.html','index.php?site=calendar&action=editdate&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=editdate[&|&amp;]*upID=([0-9]+)','calendar/editdate/$3.html','calendar\\\\/editdate\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&action=editdate&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/editwar/{upID}.html','index.php?site=calendar&action=editwar&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=editwar[&|&amp;]*upID=([0-9]+)','calendar/editwar/$3.html','calendar\\\\/editwar\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&action=editwar&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/event/save.html','calendar.php?action=savedate','a:0:{}','calendar\\\\.php\\\\?action=savedate','calendar/event/save.html','calendar\\\\/event\\\\/save\\\\.html','calendar.php?action=savedate')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/event/saveedit.html','calendar.php?action=saveeditdate','a:0:{}','calendar\\\\.php\\\\?action=saveeditdate','calendar/event/saveedit.html','calendar\\\\/event\\\\/saveedit\\\\.html','calendar.php?action=saveeditdate')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/month/{month}.html','index.php?site=calendar&month={month}','a:1:{s:5:\"month\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*month=([0-9]+)','calendar/month/$3.html','calendar\\\\/month\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&month=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/new/{upID}.html','clanwars.php?action=new&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','clanwars\\\\.php\\\\?action=new[&|&amp;]*upID=([0-9]+)','calendar/new/$3.html','calendar\\\\/new\\\\/([0-9]+?)\\\\.html','clanwars.php?action=new&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/war/save.html','calendar.php?action=savewar','a:0:{}','calendar\\\\.php\\\\?action=savewar','calendar/war/save.html','calendar\\\\/war\\\\/save\\\\.html','calendar.php?action=savewar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/war/saveedit.html','calendar.php?action=saveeditwar','a:0:{}','calendar\\\\.php\\\\?action=saveeditwar','calendar/war/saveedit.html','calendar\\\\/war\\\\/saveedit\\\\.html','calendar.php?action=saveeditwar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox.html','index.php?site=cashbox','a:0:{}','index\\\\.php\\\\?site=cashbox','cashbox.html','cashbox\\\\.html','index.php?site=cashbox')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/{id}.html','index.php?site=cashbox&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=cashbox[&|&amp;]*id=([0-9]+)','cashbox/$3.html','cashbox\\\\/([0-9]+?)\\\\.html','index.php?site=cashbox&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/action.html','cashbox.php','a:0:{}','cashbox\\\\.php','cashbox/action.html','cashbox\\\\/action\\\\.html','cashbox.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/delete/{id}.html','cashbox.php?delete=true&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','cashbox\\\\.php\\\\?delete=true[&|&amp;]*id=([0-9]+)','cashbox/delete/$3.html','cashbox\\\\/delete\\\\/([0-9]+?)\\\\.html','cashbox.php?delete=true&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/edit/{id}.html','index.php?site=cashbox&action=edit&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=cashbox[&|&amp;]*action=edit[&|&amp;]*id=([0-9]+)','cashbox/edit/$3.html','cashbox\\\\/edit\\\\/([0-9]+?)\\\\.html','index.php?site=cashbox&action=edit&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/new.html','index.php?site=cashbox&action=new','a:0:{}','index\\\\.php\\\\?site=cashbox[&|&amp;]*action=new','cashbox/new.html','cashbox\\\\/new\\\\.html','index.php?site=cashbox&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/new.html','index.php?site=cashbox&action=new','a:0:{}','index\\\\.php\\\\?site=cashbox[&|&amp;]*action=new','cashbox/new.html','cashbox\\\\/new\\\\.html','index.php?site=cashbox&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('challenge.html','index.php?site=challenge','a:0:{}','index\\\\.php\\\\?site=challenge','challenge.html','challenge\\\\.html','index.php?site=challenge')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('challenge/{type}.html','index.php?site=challenge&type={type}','a:1:{s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=challenge[&|&amp;]*type=(\\\\w*?)','challenge/$3.html','challenge\\\\/(\\\\w*?)\\\\.html','index.php?site=challenge&type=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('challenge/save.html','index.php?site=challenge&action=save','a:0:{}','index\\\\.php\\\\?site=challenge[&|&amp;]*action=save','challenge/save.html','challenge\\\\/save\\\\.html','index.php?site=challenge&action=save')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars.html','index.php?site=clanwars','a:0:{}','index\\\\.php\\\\?site=clanwars','clanwars.html','clanwars\\\\.html','index.php?site=clanwars')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{id}.html','index.php?site=clanwars_details&cwID={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=clanwars_details[&|&amp;]*cwID=([0-9]+)','clanwars/$3.html','clanwars\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars_details&cwID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}.html','index.php?site=clanwars&action=showonly&only={only}&id={id}','a:2:{s:2:\"id\";s:7:\"integer\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*only=(\\\\w*?)[&|&amp;]*id=([0-9]+)','clanwars/$3/$4.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&only=$1&id=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/{page}.html','index.php?site=clanwars&action=showonly&id={id}&sort={sort}&type={type}&only={only}&page={page}','a:5:{s:4:\"page\";s:7:\"integer\";s:2:\"id\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)[&|&amp;]*page=([0-9]+)','clanwars/$6/$3/$4/$5/$7.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&id=$2&sort=$3&type=$4&only=$1&page=$5')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/{page}.html','index.php?site=clanwars&action=showonly&id={id}&page={page}&sort={sort}&type={type}&only={only}','a:5:{s:2:\"id\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)','clanwars/$7/$3/$5/$6/$4.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&id=$2&page=$5&sort=$3&type=$4&only=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/{page}.html','index.php?site=clanwars&action=showonly&id={id}&only={only}&page={page}&sort={sort}&type={type}','a:5:{s:2:\"id\";s:7:\"integer\";s:4:\"only\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*only=(\\\\w*?)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','clanwars/$4/$3/$6/$7/$5.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&id=$2&only=$1&page=$5&sort=$3&type=$4')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/1.html','index.php?site=clanwars&action=showonly&id={id}&sort={sort}&type={type}&only={only}','a:4:{s:2:\"id\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)','clanwars/$6/$3/$4/$5/1.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=clanwars&action=showonly&id=$2&sort=$3&type=$4&only=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{squadID}/{sort}/DESC/1.html','index.php?site=clanwars&action=showonly&id={squadID}&sort={sort}&only={only}','a:3:{s:7:\"squadID\";s:7:\"integer\";s:4:\"only\";s:6:\"string\";s:4:\"sort\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)','clanwars/$5/$3/$4/DESC/1.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/DESC\\\\/1\\\\.html','index.php?site=clanwars&action=showonly&id=$2&sort=$3&only=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/new.html','clanwars.php?action=new','a:0:{}','clanwars\\\\.php\\\\?action=new','clanwars/new.html','clanwars\\\\/new\\\\.html','clanwars.php?action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/stats.html','index.php?site=clanwars&action=stats','a:0:{}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=stats','clanwars/stats.html','clanwars\\\\/stats\\\\.html','index.php?site=clanwars&action=stats')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('comments.html','comments.php','a:0:{}','comments\\\\.php','comments.html','comments\\\\.html','comments.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('comments/delete.html','comments.php?delete=true','a:0:{}','comments\\\\.php\\\\?delete=true','comments/delete.html','comments\\\\/delete\\\\.html','comments.php?delete=true')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('contact.html','index.php?site=contact','a:0:{}','index\\\\.php\\\\?site=contact','contact.html','contact\\\\.html','index.php?site=contact')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('counter.html','index.php?site=counter_stats','a:0:{}','index\\\\.php\\\\?site=counter_stats','counter.html','counter\\\\.html','index.php?site=counter_stats')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos.html','index.php?site=demos','a:0:{}','index\\\\.php\\\\?site=demos','demos.html','demos\\\\.html','index.php?site=demos')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{demoID}/edit.html','index.php?site=demos&action=edit&demoID={demoID}','a:1:{s:6:\"demoID\";s:7:\"integer\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=edit[&|&amp;]*demoID=([0-9]+)','demos/$3/edit.html','demos\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=demos&action=edit&demoID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{demoID}/show.html','index.php?site=demos&action=showdemo&demoID={demoID}','a:1:{s:6:\"demoID\";s:7:\"integer\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=showdemo[&|&amp;]*demoID=([0-9]+)','demos/$3/show.html','demos\\\\/([0-9]+?)\\\\/show\\\\.html','index.php?site=demos&action=showdemo&demoID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{game}/{sort}/{type}/{page}.html','index.php?site=demos&action=showgame&game={game}&page={page}&sort={sort}&type={type}','a:4:{s:4:\"game\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=showgame[&|&amp;]*game=(\\\\w*?)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','demos/$3/$5/$6/$4.html','demos\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=demos&action=showgame&game=$1&page=$4&sort=$2&type=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{sort}/{type}/{page}.html','index.php?site=demos&page={page}&sort={sort}&type={type}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=demos[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','demos/$4/$5/$3.html','demos\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=demos&page=$3&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/game/{game}.html','index.php?site=demos&action=showgame&game={game}','a:1:{s:4:\"game\";s:6:\"string\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=showgame[&|&amp;]*game=(\\\\w*?)','demos/game/$3.html','demos\\\\/game\\\\/(\\\\w*?)\\\\.html','index.php?site=demos&action=showgame&game=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/new.html','index.php?site=demos&action=new','a:0:{}','index\\\\.php\\\\?site=demos[&|&amp;]*action=new','demos/new.html','demos\\\\/new\\\\.html','index.php?site=demos&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/save.html','demos.php','a:0:{}','demos\\\\.php','demos/save.html','demos\\\\/save\\\\.html','demos.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('download/demo/{demoID}','download.php?demoID={demoID}','a:1:{s:6:\"demoID\";s:7:\"integer\";}','download\\\\.php\\\\?demoID=([0-9]+)','download/demo/$3','download\\\\/demo\\\\/([0-9]+?)','download.php?demoID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('download/file/{fileID}','download.php?fileID={fileID}','a:1:{s:6:\"fileID\";s:7:\"integer\";}','download\\\\.php\\\\?fileID=([0-9]+)','download/file/$3','download\\\\/file\\\\/([0-9]+?)','download.php?fileID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('faq.html','index.php?site=faq','a:0:{}','index\\\\.php\\\\?site=faq','faq.html','faq\\\\.html','index.php?site=faq')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('faq/{catID}.html','index.php?site=faq&action=faqcat&faqcatID={catID}','a:1:{s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=faq[&|&amp;]*action=faqcat[&|&amp;]*faqcatID=([0-9]+)','faq/$3.html','faq\\\\/([0-9]+?)\\\\.html','index.php?site=faq&action=faqcat&faqcatID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('faq/{catID}/{faqID}.html','index.php?site=faq&action=faq&faqID={faqID}&faqcatID={catID}','a:2:{s:5:\"faqID\";s:7:\"integer\";s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=faq[&|&amp;]*action=faq[&|&amp;]*faqID=([0-9]+)[&|&amp;]*faqcatID=([0-9]+)','faq/$4/$3.html','faq\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=faq&action=faq&faqID=$2&faqcatID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files.html','index.php?site=files','a:0:{}','index\\\\.php\\\\?site=files','files.html','files\\\\.html','index.php?site=files')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files/category/{catID}','index.php?site=files&cat={catID}','a:1:{s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=files[&|&amp;]*cat=([0-9]+)','files/category/$3','files\\\\/category\\\\/([0-9]+?)','index.php?site=files&cat=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files/file/{fileID}','index.php?site=files&file={fileID}','a:1:{s:6:\"fileID\";s:7:\"integer\";}','index\\\\.php\\\\?site=files[&|&amp;]*file=([0-9]+)','files/file/$3','files\\\\/file\\\\/([0-9]+?)','index.php?site=files&file=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files/report/{fileID}','index.php?site=files&action=report&link={fileID}','a:1:{s:6:\"fileID\";s:7:\"integer\";}','index\\\\.php\\\\?site=files[&|&amp;]*action=report[&|&amp;]*link=([0-9]+)','files/report/$3','files\\\\/report\\\\/([0-9]+?)','index.php?site=files&action=report&link=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum.html','index.php?site=forum','a:0:{}','index\\\\.php\\\\?site=forum','forum.html','forum\\\\.html','index.php?site=forum')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/{action}/board/{board}.html','index.php?site=forum&board={board}&action={action}','a:2:{s:5:\"board\";s:7:\"integer\";s:6:\"action\";s:6:\"string\";}','index\\\\.php\\\\?site=forum[&|&amp;]*board=([0-9]+)[&|&amp;]*action=(\\\\w*?)','forum/$4/board/$3.html','forum\\\\/(\\\\w*?)\\\\/board\\\\/([0-9]+?)\\\\.html','index.php?site=forum&board=$2&action=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/action.html','forum.php','a:0:{}','forum\\\\.php','forum/action.html','forum\\\\/action\\\\.html','forum.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/actions/markall.html','index.php?site=forum&action=markall','a:0:{}','index\\\\.php\\\\?site=forum[&|&amp;]*action=markall','forum/actions/markall.html','forum\\\\/actions\\\\/markall\\\\.html','index.php?site=forum&action=markall')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/board/{board}.html','index.php?site=forum&board={board}','a:1:{s:5:\"board\";s:7:\"integer\";}','index\\\\.php\\\\?site=forum[&|&amp;]*board=([0-9]+)','forum/board/$3.html','forum\\\\/board\\\\/([0-9]+?)\\\\.html','index.php?site=forum&board=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/board/{board}/addtopic.html','index.php?site=forum&addtopic=true&board={board}','a:1:{s:5:\"board\";s:7:\"integer\";}','index\\\\.php\\\\?site=forum[&|&amp;]*addtopic=true[&|&amp;]*board=([0-9]+)','forum/board/$3/addtopic.html','forum\\\\/board\\\\/([0-9]+?)\\\\/addtopic\\\\.html','index.php?site=forum&addtopic=true&board=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/cat/{cat}.html','index.php?site=forum&cat={cat}','a:1:{s:3:\"cat\";s:7:\"integer\";}','index\\\\.php\\\\?site=forum[&|&amp;]*cat=([0-9]+)','forum/cat/$3.html','forum\\\\/cat\\\\/([0-9]+?)\\\\.html','index.php?site=forum&cat=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery.html','index.php?site=gallery','a:0:{}','index\\\\.php\\\\?site=gallery','gallery.html','gallery\\\\.html','index.php?site=gallery')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery/{galID}.html','index.php?site=gallery&galleryID={galID}','a:1:{s:5:\"galID\";s:7:\"integer\";}','index\\\\.php\\\\?site=gallery[&|&amp;]*galleryID=([0-9]+)','gallery/$3.html','gallery\\\\/([0-9]+?)\\\\.html','index.php?site=gallery&galleryID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery/picture/{picID}.html','index.php?site=gallery&picID={picID}','a:1:{s:5:\"picID\";s:7:\"integer\";}','index\\\\.php\\\\?site=gallery[&|&amp;]*picID=([0-9]+)','gallery/picture/$3.html','gallery\\\\/picture\\\\/([0-9]+?)\\\\.html','index.php?site=gallery&picID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery/usergalleries.html','index.php?site=gallery&groupID=0','a:0:{}','index\\\\.php\\\\?site=gallery[&|&amp;]*groupID=0','gallery/usergalleries.html','gallery\\\\/usergalleries\\\\.html','index.php?site=gallery&groupID=0')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook.html','index.php?site=guestbook','a:0:{}','index\\\\.php\\\\?site=guestbook','guestbook.html','guestbook\\\\.html','index.php?site=guestbook')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/{type}/{page}.html','index.php?site=guestbook&page={page}&type={type}','a:2:{s:4:\"page\";s:7:\"integer\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=guestbook[&|&amp;]*page=([0-9]+)[&|&amp;]*type=(\\\\w*?)','guestbook/$4/$3.html','guestbook\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=guestbook&page=$2&type=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/add.html','index.php?site=guestbook&action=add','a:0:{}','index\\\\.php\\\\?site=guestbook[&|&amp;]*action=add','guestbook/add.html','guestbook\\\\/add\\\\.html','index.php?site=guestbook&action=add')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/add/{id}.html','index.php?site=guestbook&action=add&messageID={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=guestbook[&|&amp;]*action=add[&|&amp;]*messageID=([0-9]+)','guestbook/add/$3.html','guestbook\\\\/add\\\\/([0-9]+?)\\\\.html','index.php?site=guestbook&action=add&messageID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/comment/{id}.html','index.php?site=guestbook&action=comment&guestbookID={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=guestbook[&|&amp;]*action=comment[&|&amp;]*guestbookID=([0-9]+)','guestbook/comment/$3.html','guestbook\\\\/comment\\\\/([0-9]+?)\\\\.html','index.php?site=guestbook&action=comment&guestbookID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('help/bbcode.html','code.php','a:0:{}','code\\\\.php','help/bbcode.html','help\\\\/bbcode\\\\.html','code.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('help/smileys.html','smileys.php','a:0:{}','smileys\\\\.php','help/smileys.html','help\\\\/smileys\\\\.html','smileys.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('history.html','index.php?site=history','a:0:{}','index\\\\.php\\\\?site=history','history.html','history\\\\.html','index.php?site=history')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('imprint.html','index.php?site=imprint','a:0:{}','index\\\\.php\\\\?site=imprint','imprint.html','imprint\\\\.html','index.php?site=imprint')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('joinus.html','index.php?site=joinus','a:0:{}','index\\\\.php\\\\?site=joinus','joinus.html','joinus\\\\.html','index.php?site=joinus')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('joinus/save.html','index.php?site=joinus&action=save','a:0:{}','index\\\\.php\\\\?site=joinus[&|&amp;]*action=save','joinus/save.html','joinus\\\\/save\\\\.html','index.php?site=joinus&action=save')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('links.html','index.php?site=links','a:0:{}','index\\\\.php\\\\?site=links','links.html','links\\\\.html','index.php?site=links')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('links/{linkID}/edit.html','index.php?site=links&action=edit&linkID={linkID}','a:1:{s:6:\"linkID\";s:7:\"integer\";}','index\\\\.php\\\\?site=links[&|&amp;]*action=edit[&|&amp;]*linkID=([0-9]+)','links/$3/edit.html','links\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=links&action=edit&linkID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('links/category/{catID}.html','index.php?site=links&action=show&linkcatID={catID}','a:1:{s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=links[&|&amp;]*action=show[&|&amp;]*linkcatID=([0-9]+)','links/category/$3.html','links\\\\/category\\\\/([0-9]+?)\\\\.html','index.php?site=links&action=show&linkcatID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus.html','index.php?site=linkus','a:0:{}','index\\\\.php\\\\?site=linkus','linkus.html','linkus\\\\.html','index.php?site=linkus')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus/{bannerID}/delete.html','linkus.php?delete=true&bannerID={bannerID}','a:1:{s:8:\"bannerID\";s:7:\"integer\";}','linkus\\\\.php\\\\?delete=true[&|&amp;]*bannerID=([0-9]+)','linkus/$3/delete.html','linkus\\\\/([0-9]+?)\\\\/delete\\\\.html','linkus.php?delete=true&bannerID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus/{bannerID}/edit.html','index.php?site=linkus&action=edit&bannerID={bannerID}','a:1:{s:8:\"bannerID\";s:7:\"integer\";}','index\\\\.php\\\\?site=linkus[&|&amp;]*action=edit[&|&amp;]*bannerID=([0-9]+)','linkus/$3/edit.html','linkus\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=linkus&action=edit&bannerID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus/new.html','index.php?site=linkus&action=new','a:0:{}','index\\\\.php\\\\?site=linkus[&|&amp;]*action=new','linkus/new.html','linkus\\\\/new\\\\.html','index.php?site=linkus&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('loginoverview.html','index.php?site=loginoverview','a:0:{}','index\\\\.php\\\\?site=loginoverview','loginoverview.html','loginoverview\\\\.html','index.php?site=loginoverview')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('logout.html','logout.php','a:0:{}','logout\\\\.php','logout.html','logout\\\\.html','logout.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('lostpassword.html','index.php?site=lostpassword','a:0:{}','index\\\\.php\\\\?site=lostpassword','lostpassword.html','lostpassword\\\\.html','index.php?site=lostpassword')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('members.html','index.php?site=members','a:0:{}','index\\\\.php\\\\?site=members','members.html','members\\\\.html','index.php?site=members')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger.html','index.php?site=messenger','a:0:{}','index\\\\.php\\\\?site=messenger','messenger.html','messenger\\\\.html','index.php?site=messenger')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/{messageID}/read.html','index.php?site=messenger&action=show&id={messageID}','a:1:{s:9:\"messageID\";s:7:\"integer\";}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=show[&|&amp;]*id=([0-9]+)','messenger/$3/read.html','messenger\\\\/([0-9]+?)\\\\/read\\\\.html','index.php?site=messenger&action=show&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/{messageID}/reply.html','index.php?site=messenger&action=reply&id={messageID}','a:1:{s:9:\"messageID\";s:7:\"integer\";}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=reply[&|&amp;]*id=([0-9]+)','messenger/$3/reply.html','messenger\\\\/([0-9]+?)\\\\/reply\\\\.html','index.php?site=messenger&action=reply&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/action.html','messenger.php','a:0:{}','messenger\\\\.php','messenger/action.html','messenger\\\\/action\\\\.html','messenger.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/incoming.html','index.php?site=messenger&action=incoming','a:0:{}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=incoming','messenger/incoming.html','messenger\\\\/incoming\\\\.html','index.php?site=messenger&action=incoming')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/new.html','index.php?site=messenger&action=newmessage','a:0:{}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=newmessage','messenger/new.html','messenger\\\\/new\\\\.html','index.php?site=messenger&action=newmessage')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/outgoing.html','index.php?site=messenger&action=outgoing','a:0:{}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=outgoing','messenger/outgoing.html','messenger\\\\/outgoing\\\\.html','index.php?site=messenger&action=outgoing')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news.html','index.php?site=news','a:0:{}','index\\\\.php\\\\?site=news','news.html','news\\\\.html','index.php?site=news')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{lang}/{newsID}.html','index.php?site=news_comments&newsID={newsID}&lang={lang}','a:2:{s:6:\"newsID\";s:7:\"integer\";s:4:\"lang\";s:6:\"string\";}','index\\\\.php\\\\?site=news_comments[&|&amp;]*newsID=([0-9]+)[&|&amp;]*lang=(\\\\w*?)','news/$4/$3.html','news\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=news_comments&newsID=$2&lang=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{newsID}.html','index.php?site=news_comments&newsID={newsID}','a:1:{s:6:\"newsID\";s:7:\"integer\";}','index\\\\.php\\\\?site=news_comments[&|&amp;]*newsID=([0-9]+)','news/$3.html','news\\\\/([0-9]+?)\\\\.html','index.php?site=news_comments&newsID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{newsID}/edit.html','news.php?action=edit&newsID={newsID}','a:1:{s:6:\"newsID\";s:7:\"integer\";}','news\\\\.php\\\\?action=edit[&|&amp;]*newsID=([0-9]+)','news/$3/edit.html','news\\\\/([0-9]+?)\\\\/edit\\\\.html','news.php?action=edit&newsID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{newsID}/unpublish.html','news.php?quickactiontype=unpublish&newsID={newsID}','a:1:{s:6:\"newsID\";s:7:\"integer\";}','news\\\\.php\\\\?quickactiontype=unpublish[&|&amp;]*newsID=([0-9]+)','news/$3/unpublish.html','news\\\\/([0-9]+?)\\\\/unpublish\\\\.html','news.php?quickactiontype=unpublish&newsID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/action.html','news.php','a:0:{}','news\\\\.php','news/action.html','news\\\\/action\\\\.html','news.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/archive.html','index.php?site=news&action=archive','a:0:{}','index\\\\.php\\\\?site=news[&|&amp;]*action=archive','news/archive.html','news\\\\/archive\\\\.html','index.php?site=news&action=archive')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/archive/{sort}/{type}/{page}.html','index.php?site=news&action=archive&page={page}&sort={sort}&type={type}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=news[&|&amp;]*action=archive[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','news/archive/$4/$5/$3.html','news\\\\/archive\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=news&action=archive&page=$3&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/archive/{sort}/{type}/1.html','index.php?site=news&action=archive&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=news[&|&amp;]*action=archive[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','news/archive/$3/$4/1.html','news\\\\/archive\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=news&action=archive&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/new.html','news.php?action=new','a:0:{}','news\\\\.php\\\\?action=new','news/new.html','news\\\\/new\\\\.html','news.php?action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/unpublish.html','news.php?quickactiontype=unpublish','a:0:{}','news\\\\.php\\\\?quickactiontype=unpublish','news/unpublish.html','news\\\\/unpublish\\\\.html','news.php?quickactiontype=unpublish')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/unpublished.html','index.php?site=news&action=unpublished','a:0:{}','index\\\\.php\\\\?site=news[&|&amp;]*action=unpublished','news/unpublished.html','news\\\\/unpublished\\\\.html','index.php?site=news&action=unpublished')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter.html','index.php?site=newsletter','a:0:{}','index\\\\.php\\\\?site=newsletter','newsletter.html','newsletter\\\\.html','index.php?site=newsletter')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter/delete.html','index.php?site=newsletter&action=delete','a:0:{}','index\\\\.php\\\\?site=newsletter[&|&amp;]*action=delete','newsletter/delete.html','newsletter\\\\/delete\\\\.html','index.php?site=newsletter&action=delete')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter/forgot.html','index.php?site=newsletter&action=forgot','a:0:{}','index\\\\.php\\\\?site=newsletter[&|&amp;]*action=forgot','newsletter/forgot.html','newsletter\\\\/forgot\\\\.html','index.php?site=newsletter&action=forgot')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter/save.html','index.php?site=newsletter&action=save','a:0:{}','index\\\\.php\\\\?site=newsletter[&|&amp;]*action=save','newsletter/save.html','newsletter\\\\/save\\\\.html','index.php?site=newsletter&action=save')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls.html','index.php?site=polls','a:0:{}','index\\\\.php\\\\?site=polls','polls.html','polls\\\\.html','index.php?site=polls')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/{pollID}.html','index.php?site=polls&pollID={pollID}','a:1:{s:6:\"pollID\";s:7:\"integer\";}','index\\\\.php\\\\?site=polls[&|&amp;]*pollID=([0-9]+)','polls/$3.html','polls\\\\/([0-9]+?)\\\\.html','index.php?site=polls&pollID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/{pollID}/edit.html','index.php?site=polls&action=edit&pollID={pollID}','a:1:{s:6:\"pollID\";s:7:\"integer\";}','index\\\\.php\\\\?site=polls[&|&amp;]*action=edit[&|&amp;]*pollID=([0-9]+)','polls/$3/edit.html','polls\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=polls&action=edit&pollID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/{pollID}/vote.html','index.php?site=polls&vote={pollID}','a:1:{s:6:\"pollID\";s:7:\"integer\";}','index\\\\.php\\\\?site=polls[&|&amp;]*vote=([0-9]+)','polls/$3/vote.html','polls\\\\/([0-9]+?)\\\\/vote\\\\.html','index.php?site=polls&vote=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/new.html','index.php?site=polls&action=new','a:0:{}','index\\\\.php\\\\?site=polls[&|&amp;]*action=new','polls/new.html','polls\\\\/new\\\\.html','index.php?site=polls&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/{action}/{id}.html','index.php?site=profile&id={id}&action={action}','a:2:{s:2:\"id\";s:7:\"integer\";s:6:\"action\";s:6:\"string\";}','index\\\\.php\\\\?site=profile[&|&amp;]*id=([0-9]+)[&|&amp;]*action=(\\\\w*?)','profile/$4/$3.html','profile\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=profile&id=$2&action=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/{action}/{id}.html','index.php?site=profile&action={action}&id={id}','a:2:{s:2:\"id\";s:7:\"integer\";s:6:\"action\";s:6:\"string\";}','index\\\\.php\\\\?site=profile[&|&amp;]*action=(\\\\w*?)[&|&amp;]*id=([0-9]+)','profile/$3/$4.html','profile\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=profile&action=$1&id=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/{id}.html','index.php?site=profile&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=profile[&|&amp;]*id=([0-9]+)','profile/$3.html','profile\\\\/([0-9]+?)\\\\.html','index.php?site=profile&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/edit.html','index.php?site=myprofile','a:0:{}','index\\\\.php\\\\?site=myprofile','profile/edit.html','profile\\\\/edit\\\\.html','index.php?site=myprofile')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/mail.html','index.php?site=myprofile&action=editmail','a:0:{}','index\\\\.php\\\\?site=myprofile[&|&amp;]*action=editmail','profile/mail.html','profile\\\\/mail\\\\.html','index.php?site=myprofile&action=editmail')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/password.html','index.php?site=myprofile&action=editpwd','a:0:{}','index\\\\.php\\\\?site=myprofile[&|&amp;]*action=editpwd','profile/password.html','profile\\\\/password\\\\.html','index.php?site=myprofile&action=editpwd')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('register.html','index.php?site=register','a:0:{}','index\\\\.php\\\\?site=register','register.html','register\\\\.html','index.php?site=register')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('search.html','index.php?site=search','a:0:{}','index\\\\.php\\\\?site=search','search.html','search\\\\.html','index.php?site=search')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('search/results.html','index.php?site=search&action=search','a:0:{}','index\\\\.php\\\\?site=search[&|&amp;]*action=search','search/results.html','search\\\\/results\\\\.html','index.php?site=search&action=search')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('search/submit.html','search.php','a:0:{}','search\\\\.php','search/submit.html','search\\\\/submit\\\\.html','search.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('server.html','index.php?site=server','a:0:{}','index\\\\.php\\\\?site=server','server.html','server\\\\.html','index.php?site=server')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('shoutbox.html','index.php?site=shoutbox_content&action=showall','a:0:{}','index\\\\.php\\\\?site=shoutbox_content[&|&amp;]*action=showall','shoutbox.html','shoutbox\\\\.html','index.php?site=shoutbox_content&action=showall')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('shoutbox/delete.html','shoutbox_content.php?action=delete','a:0:{}','shoutbox_content\\\\.php\\\\?action=delete','shoutbox/delete.html','shoutbox\\\\/delete\\\\.html','shoutbox_content.php?action=delete')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('sponsors.html','index.php?site=sponsors','a:0:{}','index\\\\.php\\\\?site=sponsors','sponsors.html','sponsors\\\\.html','index.php?site=sponsors')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('squads.html','index.php?site=squads','a:0:{}','index\\\\.php\\\\?site=squads','squads.html','squads\\\\.html','index.php?site=squads')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('squads/{squadID}.html','index.php?site=squads&action=show&squadID={squadID}','a:1:{s:7:\"squadID\";s:7:\"integer\";}','index\\\\.php\\\\?site=squads[&|&amp;]*action=show[&|&amp;]*squadID=([0-9]+)','squads/$3.html','squads\\\\/([0-9]+?)\\\\.html','index.php?site=squads&action=show&squadID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery.html','index.php?site=usergallery','a:0:{}','index\\\\.php\\\\?site=usergallery','usergallery.html','usergallery\\\\.html','index.php?site=usergallery')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery/{galleryID}/edit.html','index.php?site=usergallery&action=edit&galleryID={galleryID}','a:1:{s:9:\"galleryID\";s:7:\"integer\";}','index\\\\.php\\\\?site=usergallery[&|&amp;]*action=edit[&|&amp;]*galleryID=([0-9]+)','usergallery/$3/edit.html','usergallery\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=usergallery&action=edit&galleryID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery/{galleryID}/upload.html','index.php?site=usergallery&action=upload&upload=form&galleryID={galleryID}','a:1:{s:9:\"galleryID\";s:7:\"integer\";}','index\\\\.php\\\\?site=usergallery[&|&amp;]*action=upload[&|&amp;]*upload=form[&|&amp;]*galleryID=([0-9]+)','usergallery/$3/upload.html','usergallery\\\\/([0-9]+?)\\\\/upload\\\\.html','index.php?site=usergallery&action=upload&upload=form&galleryID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery/add.html','index.php?site=usergallery&action=add','a:0:{}','index\\\\.php\\\\?site=usergallery[&|&amp;]*action=add','usergallery/add.html','usergallery\\\\/add\\\\.html','index.php?site=usergallery&action=add')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('users.html','index.php?site=registered_users','a:0:{}','index\\\\.php\\\\?site=registered_users','users.html','users\\\\.html','index.php?site=registered_users')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('users/{type}/{sort}/{page}.html','index.php?site=registered_users&page={page}&sort={sort}&type={type}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=registered_users[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','users/$5/$4/$3.html','users\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=registered_users&page=$3&sort=$2&type=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('users/ASC/{sort}/{page}.html','index.php?site=registered_users&sort={sort}&page={page}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";}','index\\\\.php\\\\?site=registered_users[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*page=([0-9]+)','users/ASC/$3/$4.html','users\\\\/ASC\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=registered_users&sort=$1&page=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('whoisonline.html','index.php?site=whoisonline','a:0:{}','index\\\\.php\\\\?site=whoisonline','whoisonline.html','whoisonline\\\\.html','index.php?site=whoisonline')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('whoisonline.html#was','index.php?site=whoisonline#was','a:0:{}','index\\\\.php\\\\?site=whoisonline#was','whoisonline.html#was','whoisonline\\\\.html#was','index.php?site=whoisonline#was')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('whoisonline/{sort}/{type}.html','index.php?site=whoisonline&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=whoisonline[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','whoisonline/$3/$4.html','whoisonline\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\.html','index.php?site=whoisonline&sort=$1&type=$2')");
    updateMySQLConfig();
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.3 Part 2');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to updated to webSPELL 4.3 Part 2<br/>' . $transaction->getError());
    }
}

function update_PasswordHash($_database)
{
    $transaction = new Transaction($_database);
    // update user passwords for new hashing
    $q = mysqli_query($_database, "SELECT userID, password FROM `" . PREFIX . "user`");
    while ($ds = mysqli_fetch_assoc($q)) {
        $transaction->addQuery("UPDATE `" . PREFIX . "user` SET password='" . hash('sha512', substr($ds['password'], 0, 14) . $ds['password']) . "' WHERE userID=" . $ds['userID']);
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated password hashes');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update password hashes');
    }
}

function addSMTPSupport($_database)
{
    global $_database;
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "email`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "email` (
  `emailID` int(1) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `debug` int(1) NOT NULL,
  `auth` int(1) NOT NULL,
  `html` int(1) NOT NULL,
  `smtp` int(1) NOT NULL,
  `secure` int(1) NOT NULL
)");

    $transaction->addQuery($_database, "INSERT INTO " . PREFIX . "email (emailID, user, password, host, port, debug, auth, html, smtp, secure)
VALUES (1, '', '', '', 25, 0, 0, 1, 0, 0)");

    $transaction->addQuery($_database, "ALTER TABLE " . PREFIX ." ADD UNIQUE KEY emailID (emailID)");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'SMTP support added');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to add SMTP support');
    }
}

function removedotINSTALL()
{
    unlink('../.INSTALL');
}
