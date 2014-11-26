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
#   Copyright 2005-2011 by webspell.org                                  #
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

$_language = $GLOBALS['_language'];
$_language->read_module('seo');

function settitle($string){
	return $GLOBALS['hp_title'].' - '.$string;
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action='';

switch ($GLOBALS['site']) {

	case 'about':
		define('PAGETITLE', settitle($_language->module['about']));
		break;
	
	case 'articles':
		if(isset($_GET['articlesID'])) $articlesID = (int)$_GET['articlesID'];
		else $articlesID = '';
		if($action=="show") {
			$get=mysql_fetch_array(safe_query("SELECT title FROM `".PREFIX."articles` WHERE articlesID='$articlesID'"));
			define('PAGETITLE', settitle($_language->module['articles'].'&nbsp; &raquo; &nbsp;'.$get['title']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['articles']));
		}
		break;
	
	case 'awards':
		if(isset($_GET['awardID'])) $awardID = (int)$_GET['awardID'];
		else $awardID = '';		
		if($action=="details") {
			$get=mysql_fetch_array(safe_query("SELECT award FROM `".PREFIX."awards` WHERE awardID='$awardID'"));
			define('PAGETITLE', settitle($_language->module['awards'].'&nbsp; &raquo; &nbsp;'.$get['award']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['awards']));
		}
		break;
	
	case 'buddys':
		define('PAGETITLE', settitle($_language->module['buddys']));
		break;
	
	case 'calendar':
		define('PAGETITLE', settitle($_language->module['calendar']));
		break;
	
	case 'cash_box':
		define('PAGETITLE', settitle($_language->module['cash_box']));
		break;
	
	case 'challenge':
		define('PAGETITLE', settitle($_language->module['challenge']));
		break;
	
	case 'clanwars':
		if($action=="stats") {
			define('PAGETITLE', settitle($_language->module['clanwars'].'&nbsp; &raquo; &nbsp;'.$_language->module['stats']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['clanwars']));
		}
		break;
	
	case 'clanwars_details':
		if(isset($_GET['cwID'])) $cwID = (int)$_GET['cwID'];
		else $cwID = '';
		$get=mysql_fetch_array(safe_query("SELECT opponent FROM `".PREFIX."clanwars` WHERE cwID='$cwID'"));
		define('PAGETITLE', settitle($_language->module['clanwars'].'&nbsp; &raquo; &nbsp;'.$_language->module['clanwars_details'].'&nbsp;'.$get['opponent']));
		break;
	
	case 'contact':
		define('PAGETITLE', settitle($_language->module['contact']));
		break;
	
	case 'counter_stats':
		define('PAGETITLE', settitle($_language->module['stats']));
		break;
	
	case 'demos':
		if(isset($_GET['demoID'])) $demoID = (int)$_GET['demoID'];
		else $demoID = '';
		if($action=="showdemo") {
			$get=mysql_fetch_array(safe_query("SELECT game, clan1, clan2 FROM `".PREFIX."demos` WHERE demoID='$demoID'"));
			define('PAGETITLE', settitle($_language->module['demos'].'&nbsp; &raquo; &nbsp;'.$get['game'].' '.$_language->module['demo'].': '.$get['clan1'].' '.$_language->module['versus'].' '.$get['clan2']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['demos']));
		}
		break;
	
	case 'faq':
		if(isset($_GET['faqcatID'])) $faqcatID = (int)$_GET['faqcatID'];
		else $faqcatID = '';
		if(isset($_GET['faqID'])) $faqID = (int)$_GET['faqID'];
		else $faqID = '';
		$get=mysql_fetch_array(safe_query("SELECT faqcatname FROM `".PREFIX."faq_categories` WHERE faqcatID='$faqcatID'"));
		$get2=mysql_fetch_array(safe_query("SELECT question FROM `".PREFIX."faq` WHERE faqID='$faqID'"));
		if($action=="faqcat") {
			define('PAGETITLE', settitle($_language->module['faq'].'&nbsp; &raquo; &nbsp;'.$get['faqcatname']));
		}
		elseif($action=="faq") {
			define('PAGETITLE', settitle($_language->module['faq'].'&nbsp; &raquo; &nbsp;'.$get['faqcatname'].'&nbsp; &raquo; &nbsp;'.$get2['question']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['faq']));
		}
		break;
	
	case 'files':
		if(isset($_GET['cat'])) $cat = (int)$_GET['cat'];
		else $cat = '';
		if(isset($_GET['file'])) $file = (int)$_GET['file'];
		else $file = '';
		if(isset($_GET['cat'])) {
			$cat = mysql_fetch_array(safe_query("SELECT filecatID, name FROM ".PREFIX."files_categorys WHERE filecatID='".$cat."'"));
			define('PAGETITLE', settitle($_language->module['files'].'&nbsp; &raquo; &nbsp;'.$cat['name']));
		}
		elseif(isset($_GET['file'])) {
			$file = mysql_fetch_array(safe_query("SELECT fileID, filecatID, filename FROM ".PREFIX."files WHERE fileID='".$file."'"));
			$catname = mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."files_categorys WHERE filecatID='".$file['filecatID']."'"));
			define('PAGETITLE', settitle($_language->module['files'].'&nbsp; &raquo; &nbsp;'.$catname['name'].'&nbsp; &raquo; &nbsp;'.$file['filename']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['files']));
		}
		break;
	
	case 'forum':
		if(isset($_GET['board'])) $board = (int)$_GET['board'];
		else $board = '';		
		if(isset($_GET['board'])) {
			$board = mysql_fetch_array(safe_query("SELECT boardID, name FROM ".PREFIX."forum_boards WHERE boardID='".$board."'"));
			define('PAGETITLE', settitle($_language->module['forum'].'&nbsp; &raquo; &nbsp;'.$board['name']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['forum']));
		}
		break;
	
	case 'forum_topic':
		if(isset($_GET['topic'])) $topic = (int)$_GET['topic'];
		else $topic = '';
		if(isset($_GET['topic'])) {
			$topic = mysql_fetch_array(safe_query("SELECT topicID, boardID, topic FROM ".PREFIX."forum_topics WHERE topicID='".$topic."'"));
			$boardname = mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."forum_boards WHERE boardID='".$topic['boardID']."'"));
			define('PAGETITLE', settitle($_language->module['forum'].'&nbsp; &raquo; &nbsp;'.$boardname['name'].'&nbsp; &raquo; &nbsp;'.$topic['topic']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['forum']));
		}
		break;
	
	case 'gallery':
		if(isset($_GET['groupID'])) $groupID = (int)$_GET['groupID'];
		else $groupID = '';
		if(isset($_GET['galleryID'])) $galleryID = (int)$_GET['galleryID'];
		else $galleryID = '';
		if(isset($_GET['picID'])) $picID = (int)$_GET['picID'];
		else $picID = '';
		if(isset($_GET['groupID'])) {
			$groupID = mysql_fetch_array(safe_query("SELECT groupID, name FROM ".PREFIX."gallery_groups WHERE groupID='".$groupID."'"));
			define('PAGETITLE', settitle($_language->module['gallery'].'&nbsp; &raquo; &nbsp;'.$groupID['name']));
		}
		elseif(isset($_GET['galleryID'])) {
			$galleryID = mysql_fetch_array(safe_query("SELECT galleryID, name, groupID FROM ".PREFIX."gallery WHERE galleryID='".$galleryID."'"));
			$groupname = mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."gallery_groups WHERE groupID='".$galleryID['groupID']."'"));
			if($groupname['name'] == "") $groupname['name'] = $_language->module['usergallery'];
			define('PAGETITLE', settitle($_language->module['gallery'].'&nbsp; &raquo; &nbsp;'.$groupname['name'].'&nbsp; &raquo; &nbsp;'.$galleryID['name']));
		}
		elseif(isset($_GET['picID'])) {
			$getgalleryname = mysql_fetch_array(safe_query("SELECT gal.groupID, gal.galleryID, gal.name FROM ".PREFIX."gallery_pictures as pic, ".PREFIX."gallery as gal WHERE pic.picID='".$_GET['picID']."' AND gal.galleryID=pic.galleryID"));
			$getgroupname = mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."gallery_groups WHERE groupID='".$getgalleryname['groupID']."'"));
			if($getgroupname['name'] == "") $getgroupname['name'] = $_language->module['usergallery'];
			$picID = mysql_fetch_array(safe_query("SELECT picID, galleryID, name FROM ".PREFIX."gallery_pictures WHERE picID='".$picID."'"));
			define('PAGETITLE', settitle($_language->module['gallery'].'&nbsp; &raquo; &nbsp;'.$getgroupname['name'].'&nbsp; &raquo; &nbsp;'.$getgalleryname['name'].'&nbsp; &raquo; &nbsp;'.$picID['name']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['gallery']));
		}
		break;
	
	case 'guestbook':
		define('PAGETITLE', settitle($_language->module['guestbook']));
		break;
	
	case 'history':
		define('PAGETITLE', settitle($_language->module['history']));
		break;
	
	case 'imprint':
		define('PAGETITLE', settitle($_language->module['imprint']));
		break;
	
	case 'joinus':
		define('PAGETITLE', settitle($_language->module['joinus']));
		break;
	
	case 'links':
		if(isset($_GET['linkcatID'])) $linkcatID = (int)$_GET['linkcatID'];
		else $linkcatID = '';
		if($action=="show") {
			$get=mysql_fetch_array(safe_query("SELECT name FROM `".PREFIX."links_categorys` WHERE linkcatID='$linkcatID'"));
			define('PAGETITLE', settitle($_language->module['links'].'&nbsp; &raquo; &nbsp;'.$get['name']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['links']));
		}
		break;
	
	case 'linkus':
		define('PAGETITLE', settitle($_language->module['linkus']));
		break;
	
	case 'login':
		define('PAGETITLE', settitle($_language->module['login']));
		break;
	
	case 'loginoverview':
		define('PAGETITLE', settitle($_language->module['loginoverview']));
		break;
	
	case 'lostpassword':
		define('PAGETITLE', settitle($_language->module['lostpassword']));
		break;
	
	case 'members':
		if(isset($_GET['squadID'])) $squadID = (int)$_GET['squadID'];
		else $squadID = '';
		if($action=="show") {
			$get=mysql_fetch_array(safe_query("SELECT name FROM `".PREFIX."squads` WHERE squadID='$squadID'"));
			define('PAGETITLE', settitle($_language->module['members'].'&nbsp; &raquo; &nbsp;'.$get['name']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['members']));
		}
		break;
	
	case 'messenger':
		define('PAGETITLE', settitle($_language->module['messenger']));
		break;
	
	case 'myprofile':
		define('PAGETITLE', settitle($_language->module['myprofile']));
		break;
	
	case 'news':
		if($action=="archive") {
			define('PAGETITLE', settitle($_language->module['news'].'&nbsp; &raquo; &nbsp;'.$_language->module['archive']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['news']));
		}
		break;
	
	case 'news_comments':
		if(isset($_GET['newsID'])) $newsID = (int)$_GET['newsID'];
		else $newsID = '';
		
		$message_array = array();
		$query=safe_query("SELECT n.* FROM ".PREFIX."news_contents n  WHERE n.newsID='".$newsID."'");
		while($qs = mysql_fetch_array($query)) {
			$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline']);
		}
		if(isset($_GET['lang'])) $showlang = getlanguageid($_GET['lang'], $message_array);
		else $showlang = select_language($message_array);
		
		$headline=$message_array[$showlang]['headline'];
		
		define('PAGETITLE', settitle($_language->module['news'].'&nbsp; &raquo; &nbsp;'.$headline));
		break;
	
	case 'newsletter':
		define('PAGETITLE', settitle($_language->module['newsletter']));
		break;
	
	case 'partners':
		define('PAGETITLE', settitle($_language->module['partners']));
		break;
	
	case 'polls':
		if(isset($_GET['vote'])) $vote = (int)$_GET['vote'];
		else $vote = '';
		if(isset($_GET['pollID'])) $pollID = (int)$_GET['pollID'];
		else $pollID = '';
		if(isset($_GET['vote'])) {
			$vote = mysql_fetch_array(safe_query("SELECT titel FROM ".PREFIX."poll WHERE pollID='".$vote."'"));
			define('PAGETITLE', settitle($_language->module['polls'].'&nbsp; &raquo; &nbsp;'.$vote['titel']));
		}
		elseif(isset($_GET['pollID'])) {
			$pollID = mysql_fetch_array(safe_query("SELECT titel FROM ".PREFIX."poll WHERE pollID='".$pollID."'"));
			define('PAGETITLE', settitle($_language->module['polls'].'&nbsp; &raquo; &nbsp;'.$pollID['titel']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['polls']));
		}
		break;
	
	case 'profile':
		if(isset($_GET['id'])) $id = (int)$_GET['id'];
		else $id='';
		define('PAGETITLE', settitle($_language->module['profile'].' '.getnickname($id)));
		break;
	
	case 'register':
		define('PAGETITLE', settitle($_language->module['register']));
		break;
	
	case 'registered_users':
		define('PAGETITLE', settitle($_language->module['registered_users']));
		break;
	
	case 'search':
		define('PAGETITLE', settitle($_language->module['search']));
		break;
		
	case 'server':
		define('PAGETITLE', settitle($_language->module['server']));
		break;
		
	case 'shoutbox':
		define('PAGETITLE', settitle($_language->module['shoutbox']));
		break;
	
	case 'sponsors':
		define('PAGETITLE', settitle($_language->module['sponsors']));
		break;
	
	case 'squads':
		if(isset($_GET['squadID'])) $squadID = (int)$_GET['squadID'];
		else $squadID = '';
		if($action=="show") {
			$get=mysql_fetch_array(safe_query("SELECT name FROM `".PREFIX."squads` WHERE squadID='$squadID'"));
			define('PAGETITLE', settitle($_language->module['squads'].'&nbsp; &raquo; &nbsp;'.$get['name']));
		}
		else {
			define('PAGETITLE', settitle($_language->module['squads']));
		}
		break;
	
	case 'static':
		if(isset($_GET['staticID'])) $staticID = (int)$_GET['staticID'];
		else $staticID = '';
		$get=mysql_fetch_array(safe_query("SELECT name FROM `".PREFIX."static` WHERE staticID='$staticID'"));
		define('PAGETITLE', settitle($get['name']));
		break;
	
	case 'usergallery':
		define('PAGETITLE', settitle($_language->module['usergallery']));
		break;
	
	case 'whoisonline':
		define('PAGETITLE', settitle($_language->module['whoisonline']));
		break;
	
	default:
		define('PAGETITLE', settitle($_language->module['news']));
		break;
}
?>