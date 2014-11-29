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

function settitle($string){
	return $GLOBALS['hp_title'].' - '.$string;
}

function extractFirstElement($element){
	return $element[0];
}

function getPageTitle($url = null, $prefix = true){
	$data = parseWebspellURL($url);
	if(isset($GLOBALS['metatags'])){
		$GLOBALS['metatags'] = $GLOBALS['metatags'] + $data['metatags'];
	}
	else{
		$GLOBALS['metatags'] = $data['metatags'];
	}

	$titles = array_map("extractFirstElement",$data['titles']);
	$title = implode($titles,'&nbsp; &raquo; &nbsp;');
	if($prefix){
		$title = settitle($title);
	}
	return $title;
}

function parseWebspellURL($parameters = null){
	$_language = $GLOBALS['_language'];
	$_language->read_module('seo');

	if($parameters == null){
		$parameters = $_GET;
	}

	if(isset($parameters['action'])) $action = $parameters['action'];
	else $action='';

	$returned_title = array();
	$metadata = array();
	if(isset($parameters['site'])){
		switch ($parameters['site']) {

			case 'about':
				$returned_title[] = array($_language->module['about']);
				break;
			
			case 'articles':
				if(isset($parameters['articlesID'])) $articlesID = (int)$parameters['articlesID'];
				else $articlesID = '';
				if($action=="show") {
					$get=mysqli_fetch_array(safe_query("SELECT title FROM `".PREFIX."articles` WHERE articlesID='$articlesID'"));
					$returned_title[] = array($_language->module['articles'],'index.php?site=articles');#&nbsp; &raquo; &nbsp;'.$get['title'];
					$returned_title[] = array($get['title']);
					$metadata['keywords'] = Tags::getTags('articles',$articlesID);
				}
				else {
					$returned_title[] = array($_language->module['articles']);
				}
				break;
			
			case 'awards':
				if(isset($parameters['awardID'])) $awardID = (int)$parameters['awardID'];
				else $awardID = '';		
				if($action=="details") {
					$get=mysqli_fetch_array(safe_query("SELECT award FROM `".PREFIX."awards` WHERE awardID='$awardID'"));
					$returned_title[] = array($_language->module['awards'],'index.php?site=awards');#.'&nbsp; &raquo; &nbsp;'.$get['award'];
					$returned_title[] = array($get['award']);
				}
				else {
					$returned_title[] = array($_language->module['awards']);
				}
				break;
			
			case 'buddies':
				$returned_title[] = array($_language->module['buddys']);
				break;
			
			case 'calendar':
				$returned_title[] = array($_language->module['calendar']);
				break;
			
			case 'cashbox':
				$returned_title[] = array($_language->module['cash_box']);
				break;
			
			case 'challenge':
				$returned_title[] = array($_language->module['challenge']);
				break;
			
			case 'clanwars':
				if($action=="stats") {
					$returned_title[] = array($_language->module['clanwars'],'index.php?site=clanwars');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($_language->module['stats']);
				}
				else {
					$returned_title[] = array($_language->module['clanwars']);
				}
				break;
			
			case 'clanwars_details':
				if(isset($parameters['cwID'])) $cwID = (int)$parameters['cwID'];
				else $cwID = '';
				$get=mysqli_fetch_array(safe_query("SELECT opponent FROM `".PREFIX."clanwars` WHERE cwID='$cwID'"));
				$returned_title[] = array($_language->module['clanwars'],'index.php?site=clanwars');#.'&nbsp; &raquo; &nbsp;'.
				$returned_title[] = array($_language->module['clanwars_details']);#.'&nbsp;'.
				$returned_title[] = array($get['opponent']);
				break;
			
			case 'contact':
				$returned_title[] = array($_language->module['contact']);
				break;
			
			case 'counter_stats':
				$returned_title[] = array($_language->module['stats']);
				break;
			
			case 'demos':
				if(isset($parameters['demoID'])) $demoID = (int)$parameters['demoID'];
				else $demoID = '';
				if($action=="showdemo") {
					$get=mysqli_fetch_array(safe_query("SELECT game, clan1, clan2 FROM `".PREFIX."demos` WHERE demoID='$demoID'"));
					$returned_title[] = array($_language->module['demos'],'index.php?site=demos');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($get['game'].' '.$_language->module['demo'].': '.$get['clan1'].' '.$_language->module['versus'].' '.$get['clan2']);
				}
				else {
					$returned_title[] = array($_language->module['demos']);
				}
				break;
			
			case 'faq':
				if(isset($parameters['faqcatID'])) $faqcatID = (int)$parameters['faqcatID'];
				else $faqcatID = '';
				if(isset($parameters['faqID'])) $faqID = (int)$parameters['faqID'];
				else $faqID = '';
				$get=mysqli_fetch_array(safe_query("SELECT faqcatname FROM `".PREFIX."faq_categories` WHERE faqcatID='$faqcatID'"));
				$get2=mysqli_fetch_array(safe_query("SELECT question FROM `".PREFIX."faq` WHERE faqID='$faqID'"));
				if($action=="faqcat") {
					$returned_title[] = array($_language->module['faq'],'index.php?site=faq');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($get['faqcatname']);
				}
				elseif($action=="faq") {
					$returned_title[] = array($_language->module['faq'],'index.php?site=faq');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($get['faqcatname'],'index.php?site=faq&amp;action=faqcat&amp;faqcatID='.$faqcatID);#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($get2['question']);
					$metadata['keywords'] = Tags::getTags('faq',$faqID);
				}
				else {
					$returned_title[] = array($_language->module['faq']);
				}
				break;
			
			case 'files':
				if(isset($parameters['cat'])) $cat = (int)$parameters['cat'];
				else $cat = '';
				if(isset($parameters['file'])) $file = (int)$parameters['file'];
				else $file = '';
				if(isset($parameters['cat'])) {
					$cat = mysqli_fetch_array(safe_query("SELECT filecatID, name FROM ".PREFIX."files_categorys WHERE filecatID='".$cat."'"));
					$returned_title[] = array($_language->module['files'],'index.php?site=files');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($cat['name']);
				}
				elseif(isset($parameters['file'])) {
					$file = mysqli_fetch_array(safe_query("SELECT fileID, filecatID, filename FROM ".PREFIX."files WHERE fileID='".$file."'"));
					$catname = mysqli_fetch_array(safe_query("SELECT name FROM ".PREFIX."files_categorys WHERE filecatID='".$file['filecatID']."'"));
					$returned_title[] = array($_language->module['files'],'index.php?site=files');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($catname['name'],'index.php?site=files&amp;cat='.$cat);#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($file['filename']);
				}
				else {
					$returned_title[] = array($_language->module['files']);
				}
				break;
			
			case 'forum':
				if(isset($parameters['board'])) $board = (int)$parameters['board'];
				else $board = '';		
				if(isset($parameters['board'])) {
					$board = mysqli_fetch_array(safe_query("SELECT boardID, name FROM ".PREFIX."forum_boards WHERE boardID='".$board."'"));
					$returned_title[] = array($_language->module['forum'],'index.php?site=forum');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($board['name']);
				}
				else {
					$returned_title[] = array($_language->module['forum']);
				}
				break;
			
			case 'forum_topic':
				if(isset($parameters['topic'])) $topic = (int)$parameters['topic'];
				else $topic = '';
				if(isset($parameters['topic'])) {
					$topic = mysqli_fetch_array(safe_query("SELECT topicID, boardID, topic FROM ".PREFIX."forum_topics WHERE topicID='".$topic."'"));
					$boardname = mysqli_fetch_array(safe_query("SELECT name FROM ".PREFIX."forum_boards WHERE boardID='".$topic['boardID']."'"));
					$returned_title[] = array($_language->module['forum'],'index.php?site=forum');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($boardname['name'],'index.php?site=forum&amp;board='.$topic['boardID']);#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($topic['topic']);
				}
				else {
					$returned_title[] = array($_language->module['forum']);
				}
				break;
			
			case 'gallery':
				if(isset($parameters['groupID'])) $groupID = (int)$parameters['groupID'];
				else $groupID = '';
				if(isset($parameters['galleryID'])) $galleryID = (int)$parameters['galleryID'];
				else $galleryID = '';
				if(isset($parameters['picID'])) $picID = (int)$parameters['picID'];
				else $picID = '';
				if(isset($parameters['groupID'])) {
					$groupID = mysqli_fetch_array(safe_query("SELECT groupID, name FROM ".PREFIX."gallery_groups WHERE groupID='".$groupID."'"));
					$returned_title[] = array($_language->module['gallery'],'index.php?site=gallery');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($groupID['name']);
				}
				elseif(isset($parameters['galleryID'])) {
					$galleryID = mysqli_fetch_array(safe_query("SELECT galleryID, name, groupID FROM ".PREFIX."gallery WHERE galleryID='".$galleryID."'"));
					$groupname = mysqli_fetch_array(safe_query("SELECT name FROM ".PREFIX."gallery_groups WHERE groupID='".$galleryID['groupID']."'"));
					if($groupname['name'] == "") $groupname['name'] = $_language->module['usergallery'];
					$returned_title[] = array($_language->module['gallery'],'index.php?site=gallery');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($groupname['name'],'index.php?site=gallery&amp;galleryID='.$galleryID['galleryID']);#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($galleryID['name']);
				}
				elseif(isset($parameters['picID'])) {
					$getgalleryname = mysqli_fetch_array(safe_query("SELECT gal.groupID, gal.galleryID, gal.name FROM ".PREFIX."gallery_pictures as pic, ".PREFIX."gallery as gal WHERE pic.picID='".$parameters['picID']."' AND gal.galleryID=pic.galleryID"));
					$getgroupname = mysqli_fetch_array(safe_query("SELECT name FROM ".PREFIX."gallery_groups WHERE groupID='".$getgalleryname['groupID']."'"));
					if($getgroupname['name'] == ""){
						$getgroupname['name'] = $_language->module['usergallery'];
					} 
					else{

					}
					$picID = mysqli_fetch_array(safe_query("SELECT picID, galleryID, name FROM ".PREFIX."gallery_pictures WHERE picID='".$picID."'"));
					$returned_title[] = array($_language->module['gallery'],'index.php?site=gallery');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($getgroupname['name'],$url);
					$returned_title[] = array($getgalleryname['name'],'index.php?site=gallery&amp;groupID='.$getgalleryname['galleryID']);#'.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($picID['name']);
				}
				else {
					$returned_title[] = array($_language->module['gallery']);
				}
				break;
			
			case 'guestbook':
				$returned_title[] = array($_language->module['guestbook']);
				break;
			
			case 'history':
				$returned_title[] = array($_language->module['history']);
				break;
			
			case 'imprint':
				$returned_title[] = array($_language->module['imprint']);
				break;
			
			case 'joinus':
				$returned_title[] = array($_language->module['joinus']);
				break;
			
			case 'links':
				if(isset($parameters['linkcatID'])) $linkcatID = (int)$parameters['linkcatID'];
				else $linkcatID = '';
				if($action=="show") {
					$get=mysqli_fetch_array(safe_query("SELECT name FROM `".PREFIX."links_categorys` WHERE linkcatID='$linkcatID'"));
					$returned_title[] = array($_language->module['links'],'index.php?site=links');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($get['name']);
				}
				else {
					$returned_title[] = array($_language->module['links']);
				}
				break;
			
			case 'linkus':
				$returned_title[] = array($_language->module['linkus']);
				break;
			
			case 'login':
				$returned_title[] = array($_language->module['login']);
				break;
			
			case 'loginoverview':
				$returned_title[] = array($_language->module['loginoverview']);
				break;
			
			case 'lostpassword':
				$returned_title[] = array($_language->module['lostpassword']);
				break;
			
			case 'members':
				if(isset($parameters['squadID'])) $squadID = (int)$parameters['squadID'];
				else $squadID = '';
				if($action=="show") {
					$get=mysqli_fetch_array(safe_query("SELECT name FROM `".PREFIX."squads` WHERE squadID='$squadID'"));
					$returned_title[] = array($_language->module['members'],'index.php?site=members');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($get['name']);
				}
				else {
					$returned_title[] = array($_language->module['members']);
				}
				break;
			
			case 'messenger':
				$returned_title[] = array($_language->module['messenger']);
				break;
			
			case 'myprofile':
				$returned_title[] = array($_language->module['myprofile']);
				break;
			
			case 'news':
				if($action=="archive") {
					$returned_title[] = array($_language->module['news'],'index.php?site=news');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($_language->module['archive']);
				}
				else {
					$returned_title[] = array($_language->module['news']);
				}
				break;
			
			case 'news_comments':
				if(isset($parameters['newsID'])) $newsID = (int)$parameters['newsID'];
				else $newsID = '';
				
				$message_array = array();
				$query=safe_query("SELECT n.* FROM ".PREFIX."news_contents n  WHERE n.newsID='".$newsID."'");
				while($qs = mysqli_fetch_array($query)) {
					$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline']);
				}
				if(isset($parameters['lang'])) $showlang = getlanguageid($parameters['lang'], $message_array);
				else $showlang = select_language($message_array);
				
				$headline=$message_array[$showlang]['headline'];
				
				$metadata['keywords'] = Tags::getTags('news',$newsID);

				$returned_title[] = array($_language->module['news'],'index.php?site=news');#.'&nbsp; &raquo; &nbsp;'.
				$returned_title[] = array($headline);
				break;
			
			case 'newsletter':
				$returned_title[] = array($_language->module['newsletter']);
				break;
			
			case 'partners':
				$returned_title[] = array($_language->module['partners']);
				break;
			
			case 'polls':
				if(isset($parameters['vote'])) $vote = (int)$parameters['vote'];
				else $vote = '';
				if(isset($parameters['pollID'])) $pollID = (int)$parameters['pollID'];
				else $pollID = '';
				if(isset($parameters['vote'])) {
					$vote = mysqli_fetch_array(safe_query("SELECT titel FROM ".PREFIX."poll WHERE pollID='".$vote."'"));
					$returned_title[] = array($_language->module['polls'],'index.php?site=polls');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($vote['titel']);
				}
				elseif(isset($parameters['pollID'])) {
					$pollID = mysqli_fetch_array(safe_query("SELECT titel FROM ".PREFIX."poll WHERE pollID='".$pollID."'"));
					$returned_title[] = array($_language->module['polls'],'index.php?site=polls');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($pollID['titel']);
				}
				else {
					$returned_title[] = array($_language->module['polls']);
				}
				break;
			
			case 'profile':
				if(isset($parameters['id'])) $id = (int)$parameters['id'];
				else $id='';
				$returned_title[] = array($_language->module['profile']);#.' '.
				$returned_title[] = array(getnickname($id));
				break;
			
			case 'register':
				$returned_title[] = array($_language->module['register']);
				break;
			
			case 'registered_users':
				$returned_title[] = array( $_language->module['registered_users']);
				break;
			
			case 'search':
				$returned_title[] = array($_language->module['search']);
				break;
				
			case 'server':
				$returned_title[] = array($_language->module['server']);
				break;
				
			case 'shoutbox':
				$returned_title[] = array($_language->module['shoutbox']);
				break;
			
			case 'sponsors':
				$returned_title[] = array($_language->module['sponsors']);
				break;
			
			case 'squads':
				if(isset($parameters['squadID'])) $squadID = (int)$parameters['squadID'];
				else $squadID = '';
				if($action=="show") {
					$get=mysqli_fetch_array(safe_query("SELECT name FROM `".PREFIX."squads` WHERE squadID='$squadID'"));
					$returned_title[] = array($_language->module['squads'],'index.php?site=squads');#.'&nbsp; &raquo; &nbsp;'.
					$returned_title[] = array($get['name']);
				}
				else {
					$returned_title[] = array($_language->module['squads']);
				}
				break;
			
			case 'static':
				if(isset($parameters['staticID'])) $staticID = (int)$parameters['staticID'];
				else $staticID = '';
				$get=mysqli_fetch_array(safe_query("SELECT name FROM `".PREFIX."static` WHERE staticID='$staticID'"));
				$returned_title[] = array($get['name']);
				$metadata['keywords'] = Tags::getTags('static',$staticID);
				break;
			
			case 'usergallery':
				$returned_title[] = array($_language->module['usergallery']);
				break;
			
			case 'whoisonline':
				$returned_title[] = array($_language->module['whoisonline']);
				break;
			
			default:
				$returned_title[] = array($_language->module['news']);
				break;
		}
	}
	else{
		$returned_title[] = array($_language->module['news']);
	}
	return array('titles'=>$returned_title,'metatags'=>$metadata);
}
?>