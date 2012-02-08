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

if(isset($_POST['board'])) $board = (int)$_POST['board'];
elseif(isset($_GET['board'])) $board = (int)$_GET['board'];
else $board = null;

if(!isset($_GET['page'])) $page = '';
else $page = (int)$_GET['page'];
if(!isset($_GET['action'])) $action = '';
else $action = $_GET['action'];

function forum_stats() {
	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;
	$bg1=BG_1;
	global $wincolor;
	global $loosecolor;
	global $drawcolor;
	global $_language;

	$_language->read_module('forum');

	// TODAY birthdays
	$ergebnis=safe_query("SELECT nickname, userID, YEAR(CURRENT_DATE()) -YEAR(birthday) 'age' FROM ".PREFIX."user WHERE DATE_FORMAT(`birthday`, '%m%d') = DATE_FORMAT(NOW(), '%m%d')");
	$n=0;
	while($db=mysql_fetch_array($ergebnis)) {
		$n++;
		$years=$db['age'];
		if($n>1) $birthdays.=', <a href="index.php?site=profile&amp;id='.$db['userID'].'"><b>'.$db['nickname'].'</b></a> ('.$years.')';
		else $birthdays='<a href="index.php?site=profile&amp;id='.$db['userID'].'"><b>'.$db['nickname'].'</b></a> ('.$years.')';
	}
	if(!$n) $birthdays=$_language->module['n_a'];


	// WEEK birthdays
	$ergebnis=safe_query("SELECT nickname, userID, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y') + 1 AS age FROM ".PREFIX."user WHERE IF(DAYOFYEAR(NOW())<=358,((DAYOFYEAR(birthday)>DAYOFYEAR(NOW())) AND (DAYOFYEAR(birthday)<=DAYOFYEAR(DATE_ADD(NOW(), INTERVAL 7 DAY)))),(DAYOFYEAR(BIRTHDAY)>DAYOFYEAR(NOW()) OR DAYOFYEAR(birthday)<=DAYOFYEAR(DATE_ADD(NOW(), INTERVAL 7 DAY)))) AND birthday !='0000-00-00 00:00:00' ORDER BY `birthday` ASC");
	$n=0;
	while($db=mysql_fetch_array($ergebnis)) {
		$n++;
		$years=$db['age'];
		if($n>1) $birthweek.=', <a href="index.php?site=profile&amp;id='.$db['userID'].'"><b>'.$db['nickname'].'</b></a> ('.$years.')';
		else $birthweek='<a href="index.php?site=profile&amp;id='.$db['userID'].'"><b>'.$db['nickname'].'</b></a> ('.$years.')';
	}
	if(!$n) $birthweek=$_language->module['n_a'];

	// WHOISONLINE
	$guests = mysql_num_rows(safe_query("SELECT ip FROM ".PREFIX."whoisonline WHERE userID=''"));
	$user = mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."whoisonline WHERE ip=''"));
	$useronline = $guests + $user;

	if($user==1) $user_on=$_language->module['registered_user'];
	else $user_on=$user.' '.$_language->module['registered_users'];

	if($guests==1) $guests_on=$_language->module['guest'];
	else $guests_on= $guests.' '.$_language->module['guests'];

	$ergebnis = safe_query("SELECT w.*, u.nickname FROM ".PREFIX."whoisonline w LEFT JOIN ".PREFIX."user u ON u.userID = w.userID  WHERE w.ip='' ORDER BY u.nickname");
	$user_names = "";
	if($user) {
		$n=1;
		while($ds=mysql_fetch_array($ergebnis)) {
			if(isforumadmin($ds['userID'])) $nickname = '<span style="color:'.$loosecolor.'">'.$ds['nickname'].'</span>';
			elseif(isanymoderator($ds['userID'])) $nickname = '<span style="color:'.$drawcolor.'">'.$ds['nickname'].'</span>';
			elseif(isclanmember($ds['userID'])) $nickname = '<span style="color:'.$wincolor.'">'.$ds['nickname'].'</span>';
      		else $nickname = $ds['nickname'];
			if($n>1) $user_names .= ', <a href="index.php?site=profile&amp;id='.$ds['userID'].'"><b>'.$nickname.'</b></a>';
			else $user_names = '<a href="index.php?site=profile&amp;id='.$ds['userID'].'"><b>'.$nickname.'</b></a>';
			$n++;
		}
	}

	$dt=mysql_fetch_array(safe_query("SELECT sum(topics), sum(posts) FROM ".PREFIX."forum_boards"));
	$topics=$dt[0];
	$posts=$dt[1];
	$dt=mysql_fetch_array(safe_query("SELECT count(userID) FROM ".PREFIX."user WHERE activated='1'"));
	$registered=$dt[0];
	$newestuser=safe_query("SELECT userID, nickname FROM ".PREFIX."user WHERE activated='1' ORDER BY registerdate DESC LIMIT 0,1");
	$dn=mysql_fetch_array($newestuser);
	$dm=mysql_fetch_array(safe_query("SELECT maxonline FROM ".PREFIX."counter"));
	$maxonline=$dm['maxonline'];

	$newestmember='<a href="index.php?site=profile&amp;id='.$dn['userID'].'"><b>'.$dn['nickname'].'</b></a>';
	eval ("\$forum_stats = \"".gettemplate("forum_stats")."\";");
	echo $forum_stats;
}

function boardmain() {
	global $maxposts;
	global $userID;
	global $action;
	global $loggedin;
	global $_language;
	global $maxtopics;

	$_language->read_module('forum');

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	eval ("\$title_messageboard = \"".gettemplate("title_messageboard")."\";");
	echo $title_messageboard;

	if($action=="markall") {
		safe_query("UPDATE ".PREFIX."user SET topics='|' WHERE userID='$userID'");
	}

	eval ("\$forum_main_head = \"".gettemplate("forum_main_head")."\";");
	echo $forum_main_head;

	// KATEGORIEN
	$sql_where = '';
	if(isset($_GET['cat'])){
		if(is_numeric($_GET['cat'])){
			$sql_where = " WHERE catID='".$_GET['cat']."'";
		}
	}
	$kath=safe_query("SELECT catID, name, info, readgrps FROM ".PREFIX."forum_categories".$sql_where." ORDER BY sort");
	while($dk=mysql_fetch_array($kath)) {
		$kathname = "<a href='index.php?site=forum&amp;cat=".$dk['catID']."'>".$dk['name']."</a>";
		if($dk['info']) $info=$dk['info'];
		else $info='';

		if($dk['readgrps'] != "") {
			$usergrp = 0;
			$readgrps = explode(";", $dk['readgrps']);
			foreach($readgrps as $value) {
				if(isinusergrp($value, $userID)) {
					$usergrp = 1;
					break;
				}
			}

			if(!$usergrp) continue;
		}
		eval ("\$forum_main_kath = \"".gettemplate("forum_main_kath")."\";");
		echo $forum_main_kath;

		// BOARDS MIT KATEGORIE
		$boards=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE category='".$dk['catID']."' ORDER BY sort");
		$i=1;

		while($db=mysql_fetch_array($boards)) {

			if($i%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}

			$ismod = ismoderator($userID, $db['boardID']);
			$usergrp = 0;
			$writer = 'ro-';
			if($db['writegrps'] != "" and !$ismod) {
				$writegrps = explode(";", $db['writegrps']);
				foreach($writegrps as $value) {
					if(isinusergrp($value, $userID)) {
						$usergrp = 1;
						$writer = '';
						break;
					}
				}
			}
			else $writer = '';
			if($db['readgrps'] != "" and !$usergrp and !$ismod) {
				$readgrps = explode(";", $db['readgrps']);
				foreach($readgrps as $value) {
					if(isinusergrp($value, $userID)) {
						$usergrp = 1;
						break;
					}
				}
				if(!$usergrp) continue;
			}

			$board=$db['boardID'];
			$anztopics=$db['topics'];
			$anzposts=$db['posts'];
			$boardname = $db['name'];
			$boardname ='&#8226; <a href="index.php?site=forum&amp;board='.$board.'"><b>'.$boardname.'</b></a>';

			if($db['info']) $boardinfo=$db['info'];
			else $boardinfo='';
			$moderators=getmoderators($db['boardID']);
			if($moderators) $moderators=$_language->module['moderated_by'].': '.$moderators;

			$postlink='';
			$date='';
			$time='';
			$poster='';
			$member='';

			$q = safe_query("SELECT topicID, lastdate, lastposter, replys FROM ".PREFIX."forum_topics WHERE boardID='".$db['boardID']."' AND moveID='0' ORDER BY lastdate DESC LIMIT 0,".$maxtopics);
			$n=1;
			$board_topics = Array();
			while($lp = mysql_fetch_assoc($q)) {
				
				if($n == 1) {

					$date=date("d.m.Y", $lp['lastdate']);
					$today=date("d.m.Y", time());
					$yesterday = date("d.m.Y", time()-3600*24);
	
					if($date==$today) $date=$_language->module['today'];
					elseif($date==$yesterday && $date<$today) $date=$_language->module['yesterday'];
					else $date=$date;
	
					$time=date("- H:i", $lp['lastdate']);
					$poster='<a href="index.php?site=profile&amp;id='.$lp['lastposter'].'">'.getnickname($lp['lastposter']).'</a>';
					if(isclanmember($lp['lastposter'])) $member=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
					else $member='';
					$topic=$lp['topicID'];
					$postlink='index.php?site=forum_topic&amp;topic='.$topic.'&amp;type=ASC&amp;page='.ceil(($lp['replys']+1)/$maxposts);

				}
				if($userID) $board_topics[] = $lp['topicID'];
				else break;
				$n++;				
			}
			
			// get unviewed topics
			
			$found = false;
			
			if($userID) {
				
				$gv=mysql_fetch_array(safe_query("SELECT topics FROM ".PREFIX."user WHERE userID='$userID'"));
				$array=explode("|", $gv['topics']);
		
				foreach($array as $split) {

					if($split != "" AND in_array($split, $board_topics)) {
							$found=true;
							break;
					}
				}
			}

			if($found) $icon='<img src="images/icons/boardicons/'.$writer.'on.gif" alt="'.$_language->module['new_posts'].'" />';
			else $icon='<img src="images/icons/boardicons/'.$writer.'off.gif" alt="'.$_language->module['no_new_posts'].'" />';


			eval ("\$forum_main_board = \"".gettemplate("forum_main_board")."\";");
			echo $forum_main_board;

			$i++;
		}
	}

	// BOARDS OHNE KATEGORIE
	$boards=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE category='0' ORDER BY sort");
	$i=1;
	while($db=mysql_fetch_array($boards)) {

		if($i%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}

		$usergrp = 0;
		$writer = 'ro-';
		$ismod = ismoderator($userID, $db['boardID']);
		if($db['writegrps'] != "" and !$ismod) {
			$writegrps = explode(";", $db['writegrps']);
			foreach($writegrps as $value) {
				if(isinusergrp($value, $userID)) {
					$usergrp = 1;
					$writer = '';
					break;
				}
			}
		}
		else $writer = '';
		if($db['readgrps'] != "" and !$usergrp and !$ismod) {
			$readgrps = explode(";", $db['readgrps']);
			foreach($readgrps as $value) {
				if(isinusergrp($value, $userID)) {
					$usergrp = 1;
					break;
				}
			}
			if(!$usergrp) continue;
		}

		$board=$db['boardID'];
		$anztopics=$db['topics'];
		$anzposts=$db['posts'];

		$boardname = $db['name'];
		$boardname='&#8226; <a href="index.php?site=forum&amp;board='.$db['boardID'].'"><b>'.$boardname.'</b></a>';

		$boardinfo='';
		if($db['info']) $boardinfo=$db['info'];
		$moderators=getmoderators($db['boardID']);
		if($moderators) $moderators=$_language->module['moderated_by'].': '.$moderators;

			$q = safe_query("SELECT topicID, lastdate, lastposter, replys FROM ".PREFIX."forum_topics WHERE boardID='".$db['boardID']."' AND moveID='0' ORDER BY lastdate DESC LIMIT 0,".$maxtopics);
			$n=1;
			$board_topics = Array();
			while($lp = mysql_fetch_assoc($q)) {
				
				if($n == 1) {

					$date=date("d.m.Y", $lp['lastdate']);
					$today=date("d.m.Y", time());
					$yesterday = date("d.m.Y", time()-3600*24);
	
					if($date==$today) $date=$_language->module['today'];
					elseif($date==$yesterday && $date<$today) $date=$_language->module['yesterday'];
					else $date=$date;
	
					$time=date("- H:i", $lp['lastdate']);
					$poster='<a href="index.php?site=profile&amp;id='.$lp['lastposter'].'">'.getnickname($lp['lastposter']).'</a>';
					if(isclanmember($lp['lastposter'])) $member=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
					else $member='';
					$topic=$lp['topicID'];
					$postlink='index.php?site=forum_topic&amp;topic='.$topic.'&amp;type=ASC&amp;page='.ceil(($lp['replys']+1)/$maxposts);

				}
				if($userID) $board_topics[] = $ds['topicID'];
				else break;
				$n++;				
			}
			
			// get unviewed topics
			
			$found = false;
			
			if($userID) {
				
				$gv=mysql_fetch_array(safe_query("SELECT topics FROM ".PREFIX."user WHERE userID='$userID'"));
				$array=explode("|", $gv['topics']);
		
				foreach($array as $split) {

					if($split != "" AND in_array($split, $board_topics)) {
							$found=true;
							break;
					}
				}
			}

			if($found) $icon='<img src="images/icons/boardicons/'.$writer.'on.gif" alt="'.$_language->module['new_posts'].'" />';
			else $icon='<img src="images/icons/boardicons/'.$writer.'off.gif" alt="'.$_language->module['no_new_posts'].'" />';

		eval ("\$forum_main_board = \"".gettemplate("forum_main_board")."\";");
		echo $forum_main_board;

		$i++;
	}

	eval ("\$forum_main_foot = \"".gettemplate("forum_main_foot")."\";");
	echo $forum_main_foot;

	if($loggedin) {
		eval ("\$forum_main_legend = \"".gettemplate("forum_main_legend")."\";");
		echo $forum_main_legend;
	}


	forum_stats();
}

function showboard($board) {
	global $userID;
	global $loggedin;
	global $maxtopics;
	global $maxposts;
	global $page;
	global $action;
	global $_language;

	$_language->read_module('forum');

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	eval ("\$title_messageboard = \"".gettemplate("title_messageboard")."\";");
	echo $title_messageboard;

	$alle = safe_query("SELECT topicID FROM ".PREFIX."forum_topics WHERE boardID='$board'");
	$gesamt=mysql_num_rows($alle);

	if($action=="markall" AND $userID) {
		$gv=mysql_fetch_array(safe_query("SELECT topics FROM ".PREFIX."user WHERE userID='$userID'"));
		
		$board_topics = Array();
		while($ds=mysql_fetch_array($alle))	$board_topics[] = $ds['topicID'];
		
		$array=explode("|", $gv['topics']);
		$new='|';
		
		foreach($array as $split) {
			if($split != "" AND !in_array($split, $board_topics)) $new .= $split.'|';
		}

		safe_query("UPDATE ".PREFIX."user SET topics='".$new."' WHERE userID='$userID'");
	}

	if(!isset($page) || $page=='') $page=1;
	$max=$maxtopics;
	$pages=ceil($gesamt/$max);

	$page_link = '';
	if($pages>1) $page_link = makepagelink("index.php?site=forum&amp;board=$board", $page, $pages);

	if($page==1) $start=0;
	if($page>1) $start=$page*$max-$max;

	$db = mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE boardID='".$board."' "));
	$boardname = $db['name'];

	$usergrp = 0;
	$writer = 0;

	$ismod=false;
	if(ismoderator($userID, $board) OR isforumadmin($userID)) $ismod = true;

	if($db['writegrps'] != "" and !$ismod) {
		$writegrps = explode(";", $db['writegrps']);
		foreach($writegrps as $value) {
			if(isinusergrp($value, $userID)) {
				$usergrp = 1;
				$writer = 1;
				break;
			}
		}
	}
	else $writer = 1;
	if($db['readgrps'] != "" and !$usergrp and !$ismod) {
		$readgrps = explode(";", $db['readgrps']);
		foreach($readgrps as $value) {
			if(isinusergrp($value, $userID)) {
				$usergrp = 1;
				break;
			}
		}
		if(!$usergrp){
			echo $_language->module['no_permission'];
			redirect('index.php?site=forum','',2);
			return;
		}
	}

	$moderators=getmoderators($board);
	if($moderators) $moderators='('.$_language->module['moderated_by'].': '.$moderators.')';

	$actions='<a href="index.php?site=search">'.$_language->module['search_image'].'</a>';
	if($loggedin) {
		$mark='&#8226; <a href="index.php?site=forum&amp;board='.$board.'&amp;action=markall">'.$_language->module['mark_topics_read'].'</a>';
		if($writer) $actions.=' <a href="index.php?site=forum&amp;addtopic=true&amp;board='.$board.'">'.$_language->module['newtopic_image'].'</a>';
	} else $mark='';
	
	$cat = $db['category'];
	$kathname = getcategoryname($cat);
	eval ("\$forum_head = \"".gettemplate("forum_head")."\";");
	echo $forum_head;

	// TOPICS

	
	$topics = safe_query("SELECT * FROM ".PREFIX."forum_topics WHERE boardID='$board' ORDER BY sticky DESC, lastdate DESC LIMIT $start,$max");
	$anztopics = mysql_num_rows(safe_query("SELECT boardID FROM ".PREFIX."forum_topics WHERE boardID='$board'"));

	$i=1;
	unset($link);
	if($anztopics) {
		eval ("\$forum_topics_head = \"".gettemplate("forum_topics_head")."\";");
		echo $forum_topics_head;
		while($dt=mysql_fetch_array($topics)) {
			if($i%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}

			if($dt['moveID']) $gesamt=0;
			else $gesamt=$dt['replys']+1;

			$topicpages=1;
			$topicpages=ceil($gesamt/$maxposts);

			$topicpage_link = '';
			if($topicpages>1) $topicpage_link = makepagelink("index.php?site=forum_topic&amp;topic=".$dt['topicID'], 1, $topicpages);

			if($dt['icon']) $icon='<img src="images/icons/topicicons/'.$dt['icon'].'" alt="" />';
			else $icon='';

			// viewed topics

			if($dt['sticky']) {
				$onicon = '<img src="images/icons/foldericons/newsticky.gif" alt="'.$_language->module['sticky'].'" />';
				$officon = '<img src="images/icons/foldericons/sticky.gif" alt="'.$_language->module['sticky'].'" />';
				$onhoticon = '<img src="images/icons/foldericons/newsticky.gif" alt="'.$_language->module['sticky'].'" />';
				$offhoticon = '<img src="images/icons/foldericons/sticky.gif" alt="'.$_language->module['sticky'].'" />';
			}
			else {
				$onicon = '<img src="images/icons/foldericons/newfolder.gif" alt="'.$_language->module['new_posts'].'" />';
				$officon = '<img src="images/icons/foldericons/folder.gif" alt="no '.$_language->module['new_posts'].'" />';
				$onhoticon = '<img src="images/icons/foldericons/newhotfolder.gif" alt="'.$_language->module['new_posts'].' ['.$_language->module['popular'].']" />';
				$offhoticon = '<img src="images/icons/foldericons/hotfolder.gif" alt="no '.$_language->module['new_posts'].' ['.$_language->module['popular'].']" />';
			}

			if($dt['closed']) $folder='<img src="images/icons/foldericons/lockfolder.gif" alt="'.$_language->module['closed'].'" />';
			elseif($dt['moveID']) $folder='<img src="images/icons/topicicons/pfeil.gif" alt="'.$_language->module['moved'].'" />';
			elseif($userID) {

				$is_unread = mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user WHERE topics LIKE '%|".$dt['topicID']."|%' AND userID='".$userID."'"));

				if($is_unread) {
					if($dt['replys']>15 || $dt['views']>150) $folder=$onhoticon;
					else $folder=$onicon;
				}
				else {
					if($dt['replys']>15 || $dt['views']>150) $folder=$offhoticon;
					else $folder=$officon;
				}
			}
			else {
				if($gesamt>15) $folder=$offhoticon;
				else $folder=$officon;
			}
			// end viewed topics

			$topictitle=getinput($dt['topic']);
			$topictitle=str_break($topictitle, 40);

			$poster='<a href="index.php?site=profile&amp;id='.$dt['userID'].'">'.getnickname($dt['userID']).'</a>';
			if(isset($posterID) and isclanmember($posterID)) $member1=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
			else $member1='';

			$replys='0';
			$views='0';

			if($dt['moveID']) { // MOVED TOPIC
				$move=safe_query("SELECT * FROM ".PREFIX."forum_topics WHERE topicID='".$dt['moveID']."'");
				$dm=mysql_fetch_array($move);

				if($dm['replys']) $replys=$dm['replys'];
				if($dm['views']) $views=$dm['views'];

				$date=date("d.m.y", $dm['lastdate']);
				$time=date("H:i", $dm['lastdate']);
				$today=date("d.m.y", time());
				$yesterday = date("d.m.y", time()-3600*24);
				if($date==$today) $date=$_language->module['today'].", ".$time;
				elseif($date==$yesterday && $date<$today) $date=$_language->module['yesterday'].", ".$time;
				else $date=$date.", ".$time;
				$lastposter='<a href="index.php?site=profile&amp;id='.$dm['lastposter'].'">'.getnickname($dm['lastposter']).'</a>';
				if(isclanmember($dm['lastposter'])) $member=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
				else $member='';
				$link='<a href="index.php?site=forum_topic&amp;topic='.$dt['moveID'].'"><b>'.$_language->module['moved'].': '.$topictitle.'</b></a>';

			}
			else {	// NO MOVED TOPIC
				if($dt['replys']) $replys=$dt['replys'];
				if($dt['views']) $views=$dt['views'];

				$date=date("d.m.y", $dt['lastdate']);
				$time=date("H:i", $dt['lastdate']);
				$today=date("d.m.y", time());
				$yesterday = date("d.m.y", time()-3600*24);
				if($date==$today) $date=$_language->module['today'].", ".$time;
				elseif($date==$yesterday && $date<$today) $date=$_language->module['yesterday'].", ".$time;
				else $date=$date.", ".$time;
				$lastposter='<a href="index.php?site=profile&amp;id='.$dt['lastposter'].'">'.getnickname($dt['lastposter']).'</a>';
				if(isclanmember($dt['lastposter'])) $member=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
				else $member='';
				$link='<a href="index.php?site=forum_topic&amp;topic='.$dt['topicID'].'"><b>'.$topictitle.'</b></a>';
			}

			eval ("\$forum_topics_content = \"".gettemplate("forum_topics_content")."\";");
			echo $forum_topics_content;
			$i++;
			unset($topicpage_link);
			unset($lastposter);
			unset($member);
			unset($member1);
			unset($date);
			unset($time);
			unset($link);

		}
		eval ("\$forum_topics_foot = \"".gettemplate("forum_topics_foot")."\";");
		echo $forum_topics_foot;

	}

	eval ("\$forum_actions = \"".gettemplate("forum_actions")."\";");
	echo $forum_actions;

	if($loggedin) {
		eval ("\$forum_topics_legend = \"".gettemplate("forum_topics_legend")."\";");
		echo $forum_topics_legend;
	}

	if(!$loggedin) echo $_language->module['not_logged_msg'];

	unset($page_link);
}

if(isset($_POST['submit']) || isset($_POST['movetopic']) || isset($_GET['addtopic']) || isset($_POST['addtopic']) || (isset($_GET['action']) and $_GET['action'] == "admin-action") || isset($_POST['admaction'])) {

	if(!isset($_POST['admaction'])) $_POST['admaction'] = '';

	if($_POST['admaction']=="closetopic") {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');

		$topicID = (int)$_POST['topicID'];
		$board = (int)$_POST['board'];

		if(!isforumadmin($userID) and !ismoderator($userID, $board)) die($_language->module['no_access']);

		safe_query("UPDATE ".PREFIX."forum_topics SET closed='1' WHERE topicID='$topicID' ");
		header("Location: index.php?site=forum&board=$board");
	}
	elseif($_POST['admaction']=="opentopic") {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');

		$topicID = (int)$_POST['topicID'];
		$board = (int)$_POST['board'];

		if(!isforumadmin($userID) and !ismoderator($userID, $board)) die($_language->module['no_access']);

		safe_query("UPDATE ".PREFIX."forum_topics SET closed='0' WHERE topicID='$topicID' ");
		header("Location: index.php?site=forum&board=$board");
	}
	elseif($_POST['admaction']=="deletetopic") {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');

		$topicID = (int)$_POST['topicID'];
		$board = (int)$_POST['board'];

		if(!isforumadmin($userID) and !ismoderator($userID, $board)) die($_language->module['no_access']);
		
		$numposts = mysql_num_rows(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE topicID='".$topicID."'"));
		$numposts --;
		
		safe_query("UPDATE ".PREFIX."forum_boards SET topics=topics-1, posts=posts-".$numposts." WHERE boardID='".$board."' ");
		safe_query("DELETE FROM ".PREFIX."forum_topics WHERE topicID='$topicID' ");
		safe_query("DELETE FROM ".PREFIX."forum_topics WHERE moveID='$topicID' ");
		safe_query("DELETE FROM ".PREFIX."forum_posts WHERE topicID='$topicID' ");
		header("Location: index.php?site=forum&board=$board");
	}
	elseif($_POST['admaction']=="stickytopic") {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');

		$topicID = (int)$_POST['topicID'];
		$board = (int)$_POST['board'];

		if(!isforumadmin($userID) and !ismoderator($userID, $board)) die($_language->module['no_access']);

		safe_query("UPDATE ".PREFIX."forum_topics SET sticky='1' WHERE topicID='$topicID' ");
		header("Location: index.php?site=forum&board=$board");
	}
	elseif($_POST['admaction']=="unstickytopic") {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');

		$topicID = (int)$_POST['topicID'];
		$board = (int)$_POST['board'];

		if(!isforumadmin($userID) and !ismoderator($userID, $board)) die($_language->module['no_access']);

		safe_query("UPDATE ".PREFIX."forum_topics SET sticky='0' WHERE topicID='$topicID' ");
		header("Location: index.php?site=forum&board=$board");
	}
	elseif($_POST['admaction']=="delposts") {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');

		$topicID = (int)$_POST['topicID'];
		if(isset($_POST['postID']))$postID = $_POST['postID'];
		else $postID = array();
		$board = (int)$_POST['board'];

		if(!isforumadmin($userID) and !ismoderator($userID, $board)) die($_language->module['no_access']);
		$last = safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE topicID = '$topicID' ");
		$anz = mysql_num_rows($last);
		$deleted = false;
		foreach($postID as $id) {
			if($anz > 1) {
				safe_query("DELETE FROM ".PREFIX."forum_posts WHERE postID='".(int)$id."' ");
				safe_query("UPDATE ".PREFIX."forum_boards SET posts=posts-1 WHERE boardID='".$board."' ");
				$last = safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE topicID = '$topicID' ORDER BY date DESC LIMIT 0,1 ");
				$dl = mysql_fetch_array($last);
				safe_query("UPDATE ".PREFIX."forum_topics SET lastdate='".$dl['date']."', lastposter='".$dl['poster']."', lastpostID='".$ds['postID']."', replys=replys-1 WHERE topicID='$topicID' ");
				$deleted=false;
			}
			else {
				safe_query("DELETE FROM ".PREFIX."forum_posts WHERE postID='".(int)$id."' ");
				safe_query("DELETE FROM ".PREFIX."forum_topics WHERE topicID='$topicID' OR moveID='$topicID'");
				safe_query("UPDATE ".PREFIX."forum_boards SET topics=topics-1 WHERE boardID='".$board."' ");
				$deleted=true;
			}
		}
		if($deleted) header("Location: index.php?site=forum&board=$board");
		else header("Location: index.php?site=forum_topic&topic=$topicID");
	}
	elseif(isset($_POST['movetopic'])) {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');

		$toboard = (int)$_POST['toboard'];
		$topicID = (int)$_POST['topicID'];

		if(!isanyadmin($userID) and !ismoderator($userID, getboardid($topicID))) die($_language->module['no_access']);

		$di=mysql_fetch_array(safe_query("SELECT writegrps, readgrps FROM ".PREFIX."forum_boards WHERE boardID='$toboard'"));

		$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_topics WHERE topicID='$topicID'");
		$ds=mysql_fetch_array($ergebnis);

		if(isset($_POST['movelink']) and $ds['boardID'] != $toboard) safe_query("INSERT INTO ".PREFIX."forum_topics (boardID, icon, userID, date, topic, lastdate, lastposter, replys, views, closed, moveID) values ('".$ds['boardID']."', '', '".$ds['userID']."', '".$ds['date']."', '".addslashes($ds['topic'])."', '".$ds['lastdate']."', '', '', '', '', '$topicID') ");

		safe_query("UPDATE ".PREFIX."forum_topics SET boardID='$toboard', readgrps='".$di['readgrps']."', writegrps='".$di['writegrps']."' WHERE topicID='$topicID'");
		safe_query("UPDATE ".PREFIX."forum_posts SET boardID='$toboard' WHERE topicID='$topicID'");
		$post_num = mysql_affected_rows()-1;
		safe_query("UPDATE ".PREFIX."forum_boards SET topics=topics+1 WHERE boardID='$toboard'");
		safe_query("UPDATE ".PREFIX."forum_boards SET topics=topics-1 WHERE boardID='".$ds['boardID']."'");
		safe_query("UPDATE ".PREFIX."forum_boards SET posts=posts+".$post_num." WHERE boardID='".$toboard."'");
		safe_query("UPDATE ".PREFIX."forum_boards SET posts=posts-".$post_num." WHERE boardID='".$ds['boardID']."'");

		header("Location: index.php?site=forum&board=$toboard");
	}
	elseif($_POST['admaction']=="movetopic") {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');
		if(!isanyadmin($userID) and !ismoderator($userID, getboardid($_POST['topicID']))) die($_language->module['no_access']);

		$boards='';
		$kath=safe_query("SELECT * FROM ".PREFIX."forum_categories ORDER BY sort");
		while($dk=mysql_fetch_array($kath)) {
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE category='$dk[catID]' ORDER BY sort");
			while($db=mysql_fetch_array($ergebnis)) {
				$boards.='<option value="'.$db['boardID'].'">'.$dk['name'].' - '.$db['name'].'</option>';
			}
		}

		$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE category='0' ORDER BY sort");
		while($ds=mysql_fetch_array($ergebnis)) {
			$boards.='<option value="'.$ds['boardID'].'">'.$ds['name'].'</option>';
		}

		$pagetitle = PAGETITLE;
		$pagebg = PAGEBG;
		$border = BORDER;
		$bghead = BGHEAD;
		$bg1 = BG_1;
		
		eval ("\$forum_move_topic = \"".gettemplate("forum_move_topic")."\";");
		echo $forum_move_topic;
	}
	elseif(isset($_POST['newtopic']) && !isset($_POST['preview'])) {
		include("_mysql.php");
		include("_settings.php");
		include('_functions.php');
		$_language->read_module('forum');
		$_language->read_module('bbcode', true);

		if(!$userID) die($_language->module['not_logged']);

		$board = (int)$_POST['board'];
		if(boardexists($board)){
			if(isset($_POST['icon'])){
				$icon = $_POST['icon'];
				if(file_exists("images/icons/topicicons/".$icon)) $icon = $icon;
				else $icon = "";
			}
			else $icon = '';
			$topicname = $_POST['topicname']; if(!$topicname) $topicname = $_language->module['default_topic_title'];
			$message = $_POST['message'];
			$topic_sticky = (isset($_POST['sticky'])) ? '1' : '0';
			$notify = (isset($_POST['notify'])) ? '1' : '0';
	
			$ds=mysql_fetch_array(safe_query("SELECT readgrps, writegrps FROM ".PREFIX."forum_boards WHERE boardID='$board'"));
	
			$writer = 0;
			if($ds['writegrps'] != "") {
				$writegrps = explode(";", $ds['writegrps']);
				foreach($writegrps as $value) {
					if(isinusergrp($value, $userID)) {
						$writer = 1;
						break;
					}
				}
				if(ismoderator($userID, $board)) $writer = 1;
			}
			else $writer = 1;
			if(!$writer) die($_language->module['no_access_write']);
	
			$date=time();
			safe_query("INSERT INTO ".PREFIX."forum_topics ( boardID, readgrps, writegrps, userID, date, icon, topic, lastdate, lastposter, replys, views, closed, sticky ) values ( '$board', '".$ds['readgrps']."', '".$ds['writegrps']."', '$userID', '$date', '".$icon."', '".$topicname."', '$date', '$userID', '0', '0', '0', '$topic_sticky' ) ");
			$id=mysql_insert_id();
			safe_query("UPDATE ".PREFIX."forum_boards SET topics=topics+1 WHERE boardID='".$board."'");
			safe_query("INSERT INTO ".PREFIX."forum_posts ( boardID, topicID, date, poster, message ) values( '$board', '$id', '$date', '$userID', '".$message."' ) ");
	
			// check if there are more than 1000 unread topics => delete oldest one
			$dv = safe_query("SELECT topics FROM ".PREFIX."user WHERE userID='".$userID."'");
			$array = explode('|', $dv['topics']);
			if(count($array)>=1000) safe_query("UPDATE ".PREFIX."user SET topics='|".implode('|', array_slice($array, 2))."' WHERE userID='".$userID."'");
			unset($array);
	
			safe_query("UPDATE ".PREFIX."user SET topics=CONCAT(topics, '".$id."|')"); // update unread topics, format: |oldstring| => |oldstring|topicID|
	
			if($notify) safe_query("INSERT INTO ".PREFIX."forum_notify (topicID, userID) VALUES ('$id', '$userID') ");
			header("Location: index.php?site=forum&board=".$board."");
		}
		else{
			header("Location: index.php?site=forum");
		}		
	}
	elseif(isset($_REQUEST['addtopic'])) {
		$_language->read_module('forum');
		$_language->read_module('bbcode', true);

		eval ("\$title_messageboard = \"".gettemplate("title_messageboard")."\";");
		echo $title_messageboard;

		$ergebnis = safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE boardID='$board' ");
		$db = mysql_fetch_array($ergebnis);
		$boardname = $db['name'];

		$writer = 0;
		if($db['writegrps'] != "") {
			$writegrps = explode(";", $db['writegrps']);
			foreach($writegrps as $value) {
				if(isinusergrp($value, $userID)) {
					$writer = 1;
					break;
				}
			}
			if(ismoderator($userID, $board)) $writer = 1;
		}
		else $writer = 1;
		if(!$writer) die($_language->module['no_access_write']);

		$moderators='';
		$cat = $db['category'];
		$kathname = getcategoryname($cat);
		
		eval ("\$forum_head = \"".gettemplate("forum_head")."\";");
		echo $forum_head;

		$bg1=BG_1;

		$message = '';

		if($loggedin) {
			if(isset($_POST['preview'])) {

				$bg1=BG_1;
				$bg2=BG_2;

				
				$time=date("H:i", time());
				$date="today";
				$message = cleartext(stripslashes(str_replace(array('\r\n', '\n'),array("\n","\n" ), $_POST['message'])));
				$message = toggle($message, 'xx');
				$username='<a href="index.php?site=profile&amp;id='.$userID.'"><b>'.getnickname($userID).'</b></a>';
				
				$board = (int)$_POST['board'];
				$topicname = stripslashes($_POST['topicname']);
				if(!isset($postID)) $postID = '';

				if(isclanmember($userID)) $member=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
				else $member='';
				if(getavatar($userID)) $avatar='<img src="images/avatars/'.getavatar($userID).'" alt="" />';
				else $avatar='';
				if(getsignatur($userID)) $signatur=cleartext(getsignatur($userID));
				else $signatur='';
				if(getemail($userID) and !getemailhide($userID)) $email = '<a href="mailto:'.mail_protect(getemail($userID)).'"><img src="images/icons/email.gif" border="0" alt="email" /></a>';
				else $email='';
				
				$pm='';
				$buddy='';
				$statuspic='<img src="images/icons/online.gif" width="7" height="7" alt="online" />';
				
				if(!validate_url(gethomepage($userID))) $hp='';
				else $hp='<a href="'.gethomepage($userID).'" target="_blank"><img src="images/icons/hp.gif" border="0" width="14" height="14" alt="'.$_language->module['homepage'].'" /></a>';
				
				$registered = getregistered($userID);
				$posts = getuserforumposts($userID);
				if(isforumadmin($userID) || ismoderator($userID, $board)) {
					if(ismoderator($userID, $board)) {
						$usertype=$_language->module['moderator'];
						$rang='<img src="images/icons/ranks/moderator.gif" alt="" />';
						if(isset($_POST['sticky'])){
							$_sticky = 'checked="checked"';
						}
					}
					if(isforumadmin($userID)) {
						$usertype="Administrator";
						$rang='<img src="images/icons/ranks/admin.gif" alt="" />';
						if(isset($_POST['sticky'])){
							$_sticky = 'checked="checked"';
						}
					}
				}
				else {
					$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_ranks WHERE $posts >= postmin AND $posts <= postmax");
					$ds=mysql_fetch_array($ergebnis);
					$usertype=$ds['rank'];
					$rang='<img src="images/icons/ranks/'.$ds['pic'].'" alt="" />';
				}
				$actions = '';
				$quote = '';

				echo'<table width="100%" cellspacing="1" cellpadding="2" bgcolor="'.BORDER.'">
          <tr bgcolor="'.BGHEAD.'">
            <td colspan="2" class="title" align="center">'.cleartext($topicname).'</td>
          </tr>
          <tr bgcolor="'.PAGEBG.'"><td colspan="2"></td></tr>';

				eval ("\$forum_topic_content = \"".gettemplate("forum_topic_content")."\";");
				echo $forum_topic_content;
				
        	echo'</table>';
        	
        	
			}
			else{
				$topicname = "";
			}

			eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");

			if(isforumadmin($userID) || ismoderator($userID, $board)) {
				if(isset($_sticky)){
					$chk_sticky = '<br />'."\n".' <input class="input" type="checkbox" name="sticky" value="1" '.$_sticky.' /> '.$_language->module['make_sticky'];
				}
				else {
					$chk_sticky = '<br />'."\n".' <input class="input" type="checkbox" name="sticky" value="1" /> '.$_language->module['make_sticky'];
				}
			}
			else {
				$chk_sticky = '';
			}
			if(isset($_POST['notify'])){
				$notify = ' checked="checked"';
			}
			else {
				$notify = '';
			}
			if(isset($_POST['topicname'])){
				$topicname=getforminput($_POST['topicname']);
			}
			if(isset($_POST['message'])){
				$message = getforminput($_POST['message']);
			}
			eval ("\$forum_newtopic = \"".gettemplate("forum_newtopic")."\";");
			echo $forum_newtopic;
		}
		else {
			echo $_language->module['not_logged_msg'];
		}
	}
	elseif(!$_POST['admaction']) {
		header("Location: index.php?site=forum");
	}

}
elseif(!isset($board)) {
	boardmain();
}
else showboard($board);

?>