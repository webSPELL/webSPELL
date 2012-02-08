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

if(isset($_GET['page'])) $page = (int)$_GET['page'];
if(isset($_GET['delete'])) $delete = (bool)$_GET['delete'];					else $delete='';
if(isset($_GET['edit'])) $edit = (bool)$_GET['edit'];						else $edit='';
if(isset($_REQUEST['topic'])) $topic = (int)$_REQUEST['topic'];				else $topic='';
if(isset($_REQUEST['addreply'])) $addreply = (bool)$_REQUEST['addreply'];	else $addreply='';
if(isset($_GET['type'])) $type = (($_GET['type']=='ASC') || ($_GET['type']=='DESC')) ? $_GET['type']: ''; else $type='';
if(isset($_GET['quoteID'])) $quoteID = (int)$_GET['quoteID'];				else $quoteID='';
$do_sticky = (isset($_POST['sticky'])) ? true : false;

if(isset($_POST['newreply']) && !isset($_POST['preview'])) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('forum');

	if(!$userID) die($_language->module['not_logged']);

	$message = $_POST['message'];
	$topic = (int)$_POST['topic'];
	$page = (int)$_POST['page'];

	if(!(mb_strlen(trim($message)))) die($_language->module['forgot_message']);
	$ds=mysql_fetch_array(safe_query("SELECT closed, writegrps, boardID FROM ".PREFIX."forum_topics WHERE topicID='".$topic."'"));
	if($ds['closed']) die($_language->module['topic_closed']);

	$writer = 0;
	if($ds['writegrps'] != "") {
		$writegrps = explode(";", $ds['writegrps']);
		foreach($writegrps as $value) {
			if(isinusergrp($value, $userID)) {
				$writer = 1;
				break;
			}
		}
		if(ismoderator($userID, $ds['boardID'])) $writer = 1;
	}
	else $writer = 1;
	if(!$writer) die($_language->module['no_access_write']);
	$do_sticky = '';
	if(isforumadmin($userID) OR isanymoderator($userID, $ds['boardID'])) {
		$do_sticky = (isset($_POST['sticky'])) ? ', sticky=1' : ', sticky=0';
	}

	$date=time();
	safe_query("INSERT INTO ".PREFIX."forum_posts ( boardID, topicID, date, poster, message ) VALUES( '".$_REQUEST['board']."', '$topic', '$date', '$userID', '".$message."' ) ");
	$lastpostID = mysql_insert_id();
	safe_query("UPDATE ".PREFIX."forum_boards SET posts=posts+1 WHERE boardID='".$_REQUEST['board']."' ");
	safe_query("UPDATE ".PREFIX."forum_topics SET lastdate='".$date."', lastposter='".$userID."', lastpostID='".$lastpostID."', replys=replys+1 $do_sticky WHERE topicID='$topic' ");

	// check if there are more than 1000 unread topics => delete oldest one
	$dv = safe_query("SELECT topics FROM ".PREFIX."user WHERE userID='".$userID."'");
	$array = explode('|', $dv['topics']);
	if(count($array)>=1000) safe_query("UPDATE ".PREFIX."user SET topics='|".implode('|', array_slice($array, 2))."' WHERE userID='".$userID."'");
	unset($array);

	// add this topic to unread
	safe_query("UPDATE ".PREFIX."user SET topics=CONCAT(topics, '".$topic."|') WHERE topics NOT LIKE '%|".$topic."|%'"); // update unread topics, format: |oldstring| => |oldstring|topicID|

	$emails=array();
	$ergebnis=safe_query("SELECT f.userID, u.email, u.language FROM ".PREFIX."forum_notify f JOIN ".PREFIX."user u ON u.userID=f.userID WHERE f.topicID=$topic");
	while($ds=mysql_fetch_array($ergebnis)) {
		$emails[] = Array('mail'=>$ds['email'], 'lang'=>$ds['language']);
	}
	safe_query("DELETE FROM ".PREFIX."forum_notify WHERE topicID='$topic'");

	if(count($emails)) {

		$de=mysql_fetch_array(safe_query("SELECT nickname FROM ".PREFIX."user WHERE userID='$userID'"));
		$poster=$de['nickname'];
		$de=mysql_fetch_array(safe_query("SELECT topic FROM ".PREFIX."forum_topics WHERE topicID='$topic'"));
		$topicname=getinput($de['topic']);

		$link="http://".$hp_url."/index.php?site=forum_topic&topic=".$topic;
		$maillanguage = new Language;
		$maillanguage->set_language($default_language);
		
		foreach($emails as $email) {
			$maillanguage->set_language($email['lang']);
			$maillanguage->read_module('forum');
			$forum_topic_notify = str_replace(Array('%poster%', '%topic_link%', '%pagetitle%', '%hpurl%'), Array(html_entity_decode($poster), $link, $hp_title, 'http://'.$hp_url), $maillanguage->module['notify_mail']);
			$header = "From:".$admin_email."\nContent-type: text/plain; charset=utf-8\n";
			@mail($email['mail'], $maillanguage->module['new_reply'].' ('.$hp_title.')', $forum_topic_notify, $header);
		}
	}

	if(isset($_POST['notify']) and (bool)$_POST['notify']) {
		safe_query("INSERT INTO ".PREFIX."forum_notify (topicID, userID) values('".$topic."', '".$userID."') ");
	}
	header("Location: index.php?site=forum_topic&topic=".$topic."&page=".$page);
	exit();
}
elseif(isset($_POST['editreply']) and (bool)$_POST['editreply']) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('forum');

	if(!isforumposter($userID,$_POST['id']) and !isforumadmin($userID) and !ismoderator($userID,$_GET['board'])) die($_language->module['no_accses']);

	$message = $_POST['message'];
	$id = (int)$_POST['id'];
	$check=mysql_num_rows(safe_query("SELECT postID FROM ".PREFIX."forum_posts WHERE postID='".$id."' AND poster='".$userID."'"));
	if(($check or isforumadmin($userID) or ismoderator($userID,(int)$_GET['board'])) and mb_strlen(trim($message))) {
		
		if(isforumadmin($userID) OR isanymoderator($userID, $ds['boardID'])) {
			$do_sticky = (isset($_POST['sticky'])) ? 'sticky=1' : 'sticky=0';
			safe_query("UPDATE ".PREFIX."forum_topics SET $do_sticky WHERE topicID='".(int)$_GET['topic']."'");
		}

		$date=date("d.m.Y - H:i", time());
		safe_query("UPDATE ".PREFIX."forum_posts SET message = '".$message."' WHERE postID='$id' ");
		safe_query("DELETE FROM ".PREFIX."forum_notify WHERE userID='$userID' AND topicID='".(int)$_GET['topic']."'");
		if(isset($_POST['notify'])) if((bool)$_POST['notify']) safe_query("INSERT INTO ".PREFIX."forum_notify (`notifyID`, `topicID`, `userID`) VALUES ('', '$userID', '".(int)$_GET['topic']."')");
	}
	header("Location: index.php?site=forum_topic&topic=".(int)$_GET['topic']."&page=".(int)$_GET['page']);
}
elseif(isset($_POST['saveedittopic']) and (bool)$_POST['saveedittopic']) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('forum');

	if(!isforumadmin($userID) and !isforumposter($userID,$_POST['post']) and !ismoderator($userID,$_GET['board'])) die($_language->module['no_accses']);

	$board = (int)$_GET['board'];
	$topic = (int)$_GET['topic'];
	$post = $_POST['post'];
	if(isset($_POST['notify']))$notify = (bool)$_POST['notify'];
	else $notify = false;
	$topicname = $_POST['topicname'];
	if(!$topicname) $topicname = $_language->module['default_topic_title'];
	$message = $_POST['message'];
	if(mb_strlen($message)){
		if(isset($_POST['icon'])) $icon = $_POST['icon'];
		else $icon = '';
		$do_sticky = (isset($_POST['sticky'])) ? true : false;
		if($do_sticky AND (isforumadmin($userID) OR isanymoderator($userID, $board))) $do_sticky=true;
		else $do_sticky=false;
	
		safe_query("UPDATE ".PREFIX."forum_posts SET message='".$message."' WHERE postID='".$post."'");
		safe_query("UPDATE ".PREFIX."forum_topics SET topic='".$topicname."', icon='".$icon."', sticky='".$do_sticky."' WHERE topicID='".$topic."'");
	
		if($notify==1) {
			$notified = safe_query("SELECT * FROM ".PREFIX."forum_notify WHERE topicID='".$topic."' AND userID='".$userID."'");
			if(mysql_num_rows($notified)!=1) {
				safe_query("INSERT INTO ".PREFIX."forum_notify (notifyID, topicID, userID) VALUES ('', '$topic', '$userID')");
			}
		} else {
			safe_query("DELETE FROM ".PREFIX."forum_notify WHERE topicID='".$topic."' AND userID='".$userID."'");
		}
	}
	header("Location: index.php?site=forum_topic&topic=".$topic);
}

function showtopic($topic, $edit, $addreply, $quoteID, $type) {
	global $userID;
	global $loggedin;
	global $page;
	global $maxposts;
	global $preview;
	global $message;
	global $picsize_l;
	global $_language;

	$_language->read_module('forum');
	$_language->read_module('bbcode', true);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$thread = safe_query("SELECT * FROM ".PREFIX."forum_topics WHERE topicID='$topic' ");
	$dt = mysql_fetch_array($thread);

	$usergrp = 0;
	$writer = 0;
	$ismod = ismoderator($userID, $dt['boardID']);
	if($dt['writegrps'] != "" and !$ismod) {
		$writegrps = explode(";", $dt['writegrps']);
		foreach($writegrps as $value) {
			if(isinusergrp($value, $userID)) {
				$usergrp = 1;
				$writer = 1;
				break;
			}
		}
	}
	else $writer = 1;
	if($dt['readgrps'] != "" and !$usergrp and !$ismod) {
		$readgrps = explode(";", $dt['readgrps']);
		foreach($readgrps as $value) {
			if(isinusergrp($value, $userID)) {
				$usergrp = 1;
				break;
			}
		}
		if(!$usergrp){ 
			echo $_language->module['no_permission'];
	    	redirect('index.php?site=forum',$_language->module['no_permission'],2);
	    	return;
		}
	}
	$gesamt = mysql_num_rows(safe_query("SELECT topicID FROM ".PREFIX."forum_posts WHERE topicID='$topic'"));
	if($gesamt==0) die($_language->module['topic_not_found']." <a href=\"javascript:history.back()\">back</a>");
	$pages=1;
	if(!isset($page) || $site='') $page=1;
	if(isset($type)){
	  if(!(($type=='ASC') || ($type=='DESC'))) $type="ASC";
	}
	else{
	  $type="ASC";
	}
	$max=$maxposts;
	$pages=ceil($gesamt/$maxposts);

	$page_link = '';
	if($pages>1) $page_link = makepagelink("index.php?site=forum_topic&amp;topic=$topic&amp;type=$type", $page, $pages);
	if($type=="ASC") {
		$sorter='<a href="index.php?site=forum_topic&amp;topic='.$topic.'&amp;page='.$page.'&amp;type=DESC">'.$_language->module['sort'].':</a> <img src="images/icons/asc.gif" alt="" />';
	}
	else {
		$sorter='<a href="index.php?site=forum_topic&amp;topic='.$topic.'&amp;page='.$page.'&amp;type=ASC">'.$_language->module['sort'].':</a> <img src="images/icons/desc.gif" alt="" />';
	}

	$start=0;
	if($page>1) $start=$page*$max-$max;

	safe_query("UPDATE ".PREFIX."forum_topics SET views=views+1 WHERE topicID='$topic' ");

	// viewed topics

	if(mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user WHERE topics LIKE '%|".$topic."|%'"))) {
		
		$gv=mysql_fetch_array(safe_query("SELECT topics FROM ".PREFIX."user WHERE userID='$userID'"));
		$array=explode("|", $gv['topics']);
		$new='|';
		
		foreach($array as $split) {
			if($split != "" AND $split!=$topic) $new = $new.$split.'|';
		}

		safe_query("UPDATE ".PREFIX."user SET topics='".$new."' WHERE userID='$userID'");
	}
		
	// end viewed topics

	$topicname=getinput($dt['topic']);

	$ergebnis = safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE boardID='".$dt['boardID']."' ");
	$db = mysql_fetch_array($ergebnis);
	$boardname = $db['name'];

	$moderators=getmoderators($dt['boardID']);

	$topicactions='<a href="printview.php?board='.$dt['boardID'].'&amp;topic='.$topic.'" target="_blank"><img src="images/icons/printview.gif" border="0" alt="printview" /></a> ';
	if($loggedin and $writer) $topicactions.='<a href="index.php?site=forum&amp;addtopic=true&amp;action=newtopic&amp;board='.$dt['boardID'].'">'.$_language->module['newtopic_image'].'</a> <a href="index.php?site=forum_topic&amp;topic='.$topic.'&amp;addreply=true&amp;page='.$pages.'&amp;type='.$type.'">'.$_language->module['newreply_image'].'</a>';
	if($dt['closed']) $closed=$_language->module['closed_image'];
	else $closed='';
	$posttype='topic';
	
	$kathname = getcategoryname($db['category']);
  	eval ("\$forum_topics_title = \"".gettemplate("forum_topics_title")."\";");
	echo $forum_topics_title;

	eval ("\$forum_topics_actions = \"".gettemplate("forum_topics_actions")."\";");
	echo $forum_topics_actions;

	if($dt['closed']) {
		echo'<br /><br />'.$_language->module['closed_image'].'<br /><br />';
	}

	if($edit && !$dt['closed']) {

		$id = $_GET['id'];
		$dr = mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE postID='".$id."'"));
		$topic = $_GET['topic'];
		$bg1=BG_1;
		$_sticky = ($dt['sticky'] == '1') ? 'checked="checked"' : '';

		$anz = mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE topicID='".$dt['topicID']."' AND postID='".$id."' AND poster='".$userID."' ORDER BY date ASC LIMIT 0,1"));
		if($anz OR isforumadmin($userID) OR ismoderator($userID,$dt['boardID'])) {
			if(istopicpost($dt['topicID'], $id)) {
				$bg1=BG_1;

				// topicmessage
				$message = getinput($dr['message']);
				$post = $id;
				$board = $dt['boardID'];

				// notification check
				$notifyqry = safe_query("SELECT * FROM ".PREFIX."forum_notify WHERE topicID='".$topic."' AND userID='".$userID."'");
				if(mysql_num_rows($notifyqry)) {
					$notify = '<input class="input" type="checkbox" name="notify" value="1" checked="checked" /> '.$_language->module['notify_reply'].'<br />';
				} else {
					$notify = '<input class="input" type="checkbox" name="notify" value="1" /> '.$_language->module['notify_reply'].'<br />';
				}
				//STICKY
				if(isforumadmin($userID) || ismoderator($userID, $board)) {
					$chk_sticky = '<br />'."\n".' <input class="input" type="checkbox" name="sticky" value="1" '.$_sticky.' /> '.$_language->module['make_sticky'];
				}
				else {
					$chk_sticky = '';
				}

				// topic icon list
				$iconlist = '<tr bgcolor="'.$bg1.'">
          <td><input type="radio" class="input" name="icon" value="ausrufezeichen.gif" />
          <img src="images/icons/topicicons/ausrufezeichen.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="biggrin.gif" />
          <img src="images/icons/topicicons/biggrin.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="boese.gif" />
          <img src="images/icons/topicicons/boese.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="bored.gif" />
          <img src="images/icons/topicicons/bored.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="cool.gif" />
          <img src="images/icons/topicicons/cool.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="eek.gif" />
          <img src="images/icons/topicicons/eek.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="frage.gif" />
          <img src="images/icons/topicicons/frage.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="frown.gif" />
          <img src="images/icons/topicicons/frown.gif" width="15" height="15" alt="" /></td>
        </tr>
        <tr bgcolor="'.$bg1.'">
          <td><input type="radio" class="input" name="icon" value="lampe.gif" />
          <img src="images/icons/topicicons/lampe.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="mad.gif" />
          <img src="images/icons/topicicons/mad.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="pfeil.gif" />
          <img src="images/icons/topicicons/pfeil.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="smile.gif" />
          <img src="images/icons/topicicons/smile.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="text.gif" />
          <img src="images/icons/topicicons/text.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="thumb_down.gif" />
          <img src="images/icons/topicicons/thumb_down.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="thumb_up.gif" />
          <img src="images/icons/topicicons/thumb_up.gif" width="15" height="15" alt="" /></td>
          <td><input type="radio" class="input" name="icon" value="wink.gif" />
          <img src="images/icons/topicicons/wink.gif" width="15" height="15" alt="" /></td>
        </tr>
        <tr bgcolor="'.$bg1.'">
            <td colspan="4"><input type="radio" class="input" name="icon" value="0" /> '.$_language->module['no_icon'].'</td>
          </tr>';
				if($dt['icon'])	$iconlist = str_replace('value="'.$dt['icon'].'"', 'value="'.$dt['icon'].'" checked="checked"', $iconlist);
				else $iconlist = str_replace('value="0"', 'value="0" checked="checked"', $iconlist);
				eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
				eval ("\$forum_edittopic = \"".gettemplate("forum_edittopic")."\";");
				echo $forum_edittopic;
			}
			else {
				// notification check
				$notifyqry = safe_query("SELECT * FROM ".PREFIX."forum_notify WHERE topicID='".$topic."' AND userID='".$userID."'");
				if(mysql_num_rows($notifyqry)) {
					$notify = '<input class="input" type="checkbox" name="notify" value="1" checked="checked" /> '.$_language->module['notify_reply'];
				} else {
					$notify = '<input class="input" type="checkbox" name="notify" value="1" /> '.$_language->module['notify_reply'];
				}
        //STICKY
				if(isforumadmin($userID) || ismoderator($userID, $board)) {
					$chk_sticky = '<br />'."\n".' <input class="input" type="checkbox" name="sticky" value="1" '.$_sticky.' /> '.$_language->module['make_sticky'];
				}
				else {
					$chk_sticky = '';
				}
				$dr['message']=getinput($dr['message']);
				eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
				eval ("\$forum_editpost = \"".gettemplate("forum_editpost")."\";");
				echo $forum_editpost;
			}
		}
		else {
			echo $_language->module['permission_denied'].'<br /><br />';
		}

		$replys = safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE topicID='$topic' ORDER BY date DESC LIMIT $start, $max");
	}
	elseif($addreply && !$dt['closed']) {
		if($loggedin and $writer) {
			if(isset($_POST['preview'])) {
				$bg1=BG_1;
				$bg2=BG_2;

				$time=date("H:i", time());
				$date=$_language->module['today'];

				$message_preview = getforminput($_POST['message']);
				$postID = 0;

				$message = cleartext(getforminput($_POST['message']));
				
				$message = toggle($message, 'xx');
				$username='<a href="index.php?site=profile&amp;id='.$userID.'"><b>'.getnickname($userID).'</b></a>';

				if(isclanmember($userID)) $member=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
				else $member='';
				if($getavatar = getavatar($userID)) $avatar='<img src="images/avatars/'.$getavatar.'" alt="" />';
				else $avatar='';
				if($getsignatur = getsignatur($userID)) $signatur=cleartext($getsignatur);
				else $signatur='';
				if($getemail = getemail($userID) and !getemailhide($userID)) $email = '<a href="mailto:'.mail_protect($getemail).'"><img src="images/icons/email.gif" border="0" alt="email" /></a>';
				else $email='';
				if(isset($_POST['notify'])) $notify = 'checked="checked"';
				else $notify = '';
				$pm='';
				$buddy='';
				$statuspic='<img src="images/icons/online.gif" alt="online" />';
				if(!validate_url(gethomepage($userID))) $hp='';
				else $hp='<a href="'.gethomepage($userID).'" target="_blank"><img src="images/icons/hp.gif" border="0" alt="'.$_language->module['homepage'].'" /></a>';
				$registered = getregistered($userID);
				$posts = getuserforumposts($userID);
				if(isset($_POST['sticky'])) $post_sticky = $_POST['sticky'];
				else $post_sticky = null;
				$_sticky = ($dt['sticky'] == '1' || $post_sticky == '1') ? 'checked="checked"' : '';

				if(isforumadmin($userID)) {
					$usertype=$_language->module['admin'];
					$rang='<img src="images/icons/ranks/admin.gif" alt="" />';
				}
				elseif(isanymoderator($userID)) {
					$usertype=$_language->module['moderator'];
					$rang='<img src="images/icons/ranks/moderator.gif" alt="" />';
				} else {
					$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_ranks WHERE $posts >= postmin AND $posts <= postmax AND postmax >0");
					$ds=mysql_fetch_array($ergebnis);
					$usertype=$ds['rank'];
					$rang='<img src="images/icons/ranks/'.$ds['pic'].'" alt="" />';
				}
				
				if(isforumadmin($userID)) $chk_sticky = '<br />'."\n".' <input class="input" type="checkbox" name="sticky" value="1" '.$_sticky.' /> '.$_language->module['make_sticky'];
				elseif(isanymoderator($userID)) $chk_sticky = '<br />'."\n".' <input class="input" type="checkbox" name="sticky" value="1" '.$_sticky.' /> '.$_language->module['make_sticky'];
				else $chk_sticky = '';
				$quote = "";
				$actions = "";
				echo'<table width="100%" cellspacing="1" cellpadding="2" bgcolor="'.BORDER.'">
          <tr bgcolor="'.BGHEAD.'">
            <td colspan="2" class="title" align="center">'.$_language->module['preview'].'</td>
          </tr>
          <tr bgcolor="'.PAGEBG.'"><td colspan="2"></td></tr>';
          
				eval ("\$forum_topic_content = \"".gettemplate("forum_topic_content")."\";");
				echo $forum_topic_content;
        
				echo'</table>';

				$message = $message_preview;
			}
			else {
				if($quoteID) {
					$ergebnis=safe_query("SELECT poster,message FROM ".PREFIX."forum_posts WHERE postID='$quoteID'");
					$ds=mysql_fetch_array($ergebnis);
					$message='[quote='.getnickname($ds['poster']).']'.getinput($ds['message']).'[/quote]';
				}
			}
			if(isset($_POST['sticky'])) $post_sticky = $_POST['sticky'];
			else $post_sticky = null;
			$_sticky = ($dt['sticky'] == '1' || $post_sticky == '1') ? 'checked="checked"' : '';
			if(isforumadmin($userID) || ismoderator($userID, $dt['boardID'])) {
				$chk_sticky = '<br />'."\n".' <input class="input" type="checkbox" name="sticky" value="1" '.$_sticky.' /> '.$_language->module['make_sticky'];
			}
			else {
				$chk_sticky = '';
			}
			
			if(isset($_POST['notify'])) $post_notify = $_POST['notify'];
			else $post_notify = null;
			$mysql_notify = mysql_num_rows(safe_query("SELECT notifyID FROM ".PREFIX."forum_notify WHERE userID='".$userID."' AND topicID='".$topic."'"));
			$notify = ($mysql_notify || $post_notify == '1') ? 'checked="checked"' : '';
			
			
			$bg1=BG_1;
			$board = $dt['boardID'];

			eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
			eval ("\$forum_newreply = \"".gettemplate("forum_newreply")."\";");
			echo $forum_newreply;
		}
		elseif($loggedin) {
			echo'<br /><br />'.$_language->module['no_access_write'].'<br /><br />';
		}
		else {
			echo $_language->module['not_logged_msg'];
		}
		$replys = safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE topicID='$topic' ORDER BY date DESC LIMIT 0, ".$max."");
	}
	else $replys = safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE topicID='$topic' ORDER BY date $type LIMIT ".$start.", ".$max."");

	eval ("\$forum_topic_head = \"".gettemplate("forum_topic_head")."\";");
	echo $forum_topic_head;
	$i=1;
	while($dr=mysql_fetch_array($replys)) {
		if($i%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}

		$date=date("d.m.Y", $dr['date']);
		$time=date("H:i", $dr['date']);

		$today=date("d.m.Y", time());
		$yesterday = date("d.m.Y", time()-3600*24);

		if($date==$today) $date=$_language->module['today'];
		elseif($date==$yesterday && $date<$today) $date=$_language->module['yesterday'];
		else $date=$date;

		$message = cleartext($dr['message']);
		$message = toggle($message, $dr['postID']);
		$postID = $dr['postID'];

		$username='<a href="index.php?site=profile&amp;id='.$dr['poster'].'"><b>'.stripslashes(getnickname($dr['poster'])).'</b></a>';

		if(isclanmember($dr['poster'])) $member=' <img src="images/icons/member.gif" alt="'.$_language->module['clanmember'].'" />';
		else $member='';

		if($getavatar = getavatar($dr['poster'])) $avatar='<img src="images/avatars/'.$getavatar.'" alt="" />';
		else $avatar='';

		if($getsignatur = getsignatur($dr['poster'])) $signatur=cleartext($getsignatur);
		else $signatur='';

		if($getemail = getemail($dr['poster']) and !getemailhide($dr['poster'])) $email = '<a href="mailto:'.mail_protect($getemail).'"><img src="images/icons/email.gif" border="0" alt="email" /></a>';
		else $email='';

		$pm='';
		$buddy='';
		if($loggedin && $dr['poster']!=$userID) {
			$pm='<a href="index.php?site=messenger&amp;action=touser&amp;touser='.$dr['poster'].'"><img src="images/icons/pm.gif" border="0" width="12" height="13" alt="'.$_language->module['messenger'].'" /></a>';
			if(isignored($userID, $dr['poster'])) $buddy='<a href="buddys.php?action=readd&amp;id='.$dr['poster'].'&amp;userID='.$userID.'"><img src="images/icons/buddy_readd.gif" border="0" alt="'.$_language->module['back_buddy'].'" /></a>';
			elseif(isbuddy($userID, $dr['poster'])) $buddy='<a href="buddys.php?action=ignore&amp;id='.$dr['poster'].'&amp;userID='.$userID.'"><img src="images/icons/buddy_ignore.gif" border="0" alt="'.$_language->module['ignore'].'" /></a>';
			else $buddy='<a href="buddys.php?action=add&amp;id='.$dr['poster'].'&amp;userID='.$userID.'"><img src="images/icons/buddy_add.gif" border="0" alt="'.$_language->module['add_buddy'].'" /></a>';
		}

		if(isonline($dr['poster'])=="offline") $statuspic='<img src="images/icons/offline.gif" alt="offline" />';
		else $statuspic='<img src="images/icons/online.gif" alt="online" />';

		if(!validate_url(gethomepage($dr['poster']))) $hp='';
		else $hp='<a href="'.gethomepage($dr['poster']).'" target="_blank"><img src="images/icons/hp.gif" border="0" alt="'.$_language->module['homepage'].'" /></a>';

		if(!$dt['closed']) $quote='<a href="index.php?site=forum_topic&amp;addreply=true&amp;board='.$dt['boardID'].'&amp;topic='.$topic.'&amp;quoteID='.$dr['postID'].'&amp;page='.$page.'&amp;type='.$type.'"><img src="images/icons/quote.gif" border="0" alt="'.$_language->module['quote'].'" /></a>';
		else $quote = "";

		$registered = getregistered($dr['poster']);

		$posts = getuserforumposts($dr['poster']);

		if(isforumadmin($dr['poster'])) {
			$usertype=$_language->module['admin'];
			$rang='<img src="images/icons/ranks/admin.gif" alt="" />';
		}
		elseif(isanymoderator($dr['poster'])) {
			$usertype=$_language->module['moderator'];
			$rang='<img src="images/icons/ranks/moderator.gif" alt="" />';
		} else {
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_ranks WHERE $posts >= postmin AND $posts <= postmax AND postmax >0");
			$ds=mysql_fetch_array($ergebnis);
			$usertype=$ds['rank'];
			$rang='<img src="images/icons/ranks/'.$ds['pic'].'" alt="" />';
		}

		$actions='';
		if(($userID == $dr['poster'] OR isforumadmin($userID) OR ismoderator($userID,$dt['boardID']))&& !$dt['closed']) $actions=' <a href="index.php?site=forum_topic&amp;topic='.$topic.'&amp;edit=true&amp;id='.$dr['postID'].'&amp;page='.$page.'"><img src="images/icons/edit.gif" border="0" alt="'.$_language->module['edit'].'" /></a> ';
		if(isforumadmin($userID) OR ismoderator($userID,$dt['boardID'])) $actions.='<input class="input" type="checkbox" name="postID[]" value="'.$dr['postID'].'" />';

		eval ("\$forum_topic_content = \"".gettemplate("forum_topic_content")."\";");
		echo $forum_topic_content;
		unset($actions);
		$i++;
	}
	
	$adminactions = "";
	if(isforumadmin($userID) OR ismoderator($userID,$dt['boardID'])) {

		if($dt['closed']) $close='<option value="opentopic">- '.$_language->module['reopen_topic'].'</option>';
		else $close='<option value="closetopic">- '.$_language->module['close_topic'].'</option>';

		$adminactions='<input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'
		<select name="admaction">
      <option value="0">'.$_language->module['admin_actions'].':</option>
      <option value="delposts">- '.$_language->module['delete_posts'].'</option>
      <option value="stickytopic">- '.$_language->module['make_topic_sticky'].'</option>
      <option value="unstickytopic">- '.$_language->module['make_topic_unsticky'].'</option>
      <option value="movetopic">- '.$_language->module['move_topic'].'</option>
      '.$close.'
      <option value="deletetopic">- '.$_language->module['delete_topic'].'</option>
    </select>
    <input type="hidden" name="topicID" value="'.$topic.'" />
    <input type="hidden" name="board" value="'.$dt['boardID'].'" />
    <input type="submit" name="submit" value="'.$_language->module['go'].'" />';

	}

	eval ("\$forum_topic_foot = \"".gettemplate("forum_topic_foot")."\";");
	echo $forum_topic_foot;

	eval ("\$forum_topics_actions = \"".gettemplate("forum_topics_actions")."\";");
	echo $forum_topics_actions;

	echo'<div align="right">'.$adminactions.'</div></form>';

	if($dt['closed']) {
		echo $_language->module['closed_image'];
	}
	else {
		if(!$loggedin && !$edit) {
			echo $_language->module['not_logged_msg'];
		}
	}
}

showtopic($topic, $edit, $addreply, $quoteID, $type);

?>