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
$bg1 = BG_1;
function checkCommentsAllow($type, $parentID){
	global $userID;
	$moduls = array();
	$moduls['ne'] = array("news","newsID","comments");
	$moduls['ar'] = array("articles","articlesID","comments");
	$moduls['ga'] = array("gallery_pictures","picID","comments");
	$moduls['cw'] = array("clanwars","cwID","comments");
	$moduls['de'] = array("demos","demoID","comments");
	$moduls['po'] = array("poll","pollID","comments");
	$allowed = 0;
	$modul = $moduls[$type];
	$get = safe_query("SELECT ".$modul[2]." FROM ".PREFIX.$modul[0]." WHERE ".$modul[1]."='".$parentID."'");
	if(mysql_num_rows($get)){
		$data = mysql_fetch_assoc($get);
		switch($data[$modul[2]]){
			case 0: $allowed = 0; break;
			case 1: if($userID) $allowed = 1; break;
			case 2: $allowed = 1; break;
			default: $allowed=0;
		}
	}
	return $allowed;
}
if(isset($_POST['savevisitorcomment'])) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");

	$name = $_POST['name'];
	$mail = $_POST['mail'];
	$url = $_POST['url'];
	$parentID = (int)$_POST['parentID'];
	$type = $_POST['type'];
	$message = $_POST['message'];
	$ip = $GLOBALS['ip'];
	$CAPCLASS = new Captcha;

	setcookie("visitor_info", $name."--||--".$mail."--||--".$url, time()+(3600*24*365));
	$query = safe_query("SELECT nickname, username FROM ".PREFIX."user ORDER BY nickname");
	while($ds=mysql_fetch_array($query)) {
		$nicks[] = $ds['nickname'];
		$nicks[] = $ds['username'];
	}
	$_SESSION['comments_message'] = $message;

	if(in_array(trim($name), $nicks)) header("Location: ".$_POST['referer']."&error=nickname#addcomment");
	elseif(!($CAPCLASS->check_captcha($_POST['captcha'], $_POST['captcha_hash']))) header("Location: ".$_POST['referer']."&error=captcha#addcomment");
	elseif(checkCommentsAllow($type,$parentID) == false ) header("Location: ".$_POST['referer']);
	else {
		$date=time();
		safe_query("INSERT INTO ".PREFIX."comments ( parentID, type, nickname, date, comment, url, email, ip )
		            values( '".$parentID."', '".$type."', '".$name."', '".$date."', '".$message."', '".$url."', '".$mail."', '".$ip."' ) ");
		unset($_SESSION['comments_message']);
		header("Location: ".$_POST['referer']);
	}
}
elseif(isset($_POST['saveusercomment'])) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('comments');
	if(!$userID) die($_language->module['access_denied']);

	$parentID = $_POST['parentID'];
	$type = $_POST['type'];
	$message = $_POST['message'];
	if(checkCommentsAllow($type,$parentID)){
		$date=time();
		safe_query("INSERT INTO ".PREFIX."comments ( parentID, type, userID, date, comment ) values( '".$parentID."', '".$type."', '".$userID."', '".$date."', '".$message."' ) ");
	}
	header("Location: ".$_POST['referer']);
}
elseif(isset($_GET['delete'])) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('comments');
	if(!isanyadmin($userID)) die($_language->module['access_denied']);
	foreach($_POST['commentID'] as $id) {
		safe_query("DELETE FROM ".PREFIX."comments WHERE commentID='".$id."'");
	}
	header("Location: ".$_POST['referer']);
}
elseif(isset($_GET['editcomment'])) {
	$id=$_GET['id'];
	$referer=$_GET['ref'];
	$_language->read_module('comments');
	$_language->read_module('bbcode', true);
	if(isfeedbackadmin($userID) or iscommentposter($userID,$id)) {
		if(!empty($id)) {
			$dt = safe_query("SELECT * FROM ".PREFIX."comments WHERE commentID='".$id."'");
			if(mysql_num_rows($dt)) {
				$ds = mysql_fetch_array($dt);
				$poster='<a href="index.php?site=profile&amp;id='.$ds['userID'].'"><b>'.getnickname($ds['userID']).'</b></a>';
				$message=getinput($ds['comment']);
				$message=preg_replace("#\n\[br\]\[br\]\[hr]\*\*(.+)#si", '', $message);
				$message=preg_replace("#\n\[br\]\[br\]\*\*(.+)#si", '', $message);

				eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");

				eval("\$comments_edit = \"".gettemplate("comments_edit")."\";");
				echo $comments_edit;
			}
			else {
				redirect($referer, $_language->module['no_database_entry'], 2);
			}
		}
		else {
			redirect($referer, $_language->module['no_commentid'], 2);
		}
	}
	else {
		redirect($referer, $_language->module['access_denied'], 2);
	}
}
elseif(isset($_POST['saveeditcomment'])) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");

	if(!isfeedbackadmin($userID) and !iscommentposter($userID,$_POST['commentID'])) die('No access');

	$message=$_POST['message'];
	$author=$_POST['authorID'];
	$referer=urldecode($_POST['referer']);

	// check if any admin edited the post
	if(safe_query("UPDATE ".PREFIX."comments SET comment='".$message."' WHERE commentID='".$_POST['commentID']."'")) {
		header("Location: ".$referer);
	}
}
else {

	$_language->read_module('comments');
	$_language->read_module('bbcode', true);

	if(isset($_GET['commentspage'])) $commentspage = (int)$_GET['commentspage'];
	else $commentspage = 1;
	if(isset($_GET['sorttype']) AND strtoupper($_GET['sorttype']=="ASC")) $sorttype = 'ASC';
	else $sorttype='DESC';

	if(!isset($parentID) AND isset($_GET['parentID'])) $parentID = (int)$_GET['parentID'];
	if(!isset($type) AND isset($_GET['type'])) $type = mb_substr($_GET['type'], 0, 2);

	$alle=safe_query("SELECT commentID FROM ".PREFIX."comments WHERE parentID='$parentID' AND type='$type'");
	$gesamt=mysql_num_rows($alle);
	$commentspages=ceil($gesamt/$maxfeedback);

	if($commentspages>1) $page_link = makepagelink("$referer&amp;sorttype=$sorttype", $commentspage, $commentspages, 'comments');
	else $page_link='';

	if ($commentspage == "1") {
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."comments WHERE parentID='$parentID' AND type='$type' ORDER BY date $sorttype LIMIT 0,$maxfeedback");
		if($sorttype=="DESC") $n=$gesamt;
		else $n=1;
	}	else {
		$start=($commentspage-1)*$maxfeedback;
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."comments WHERE parentID='$parentID' AND type='$type' ORDER BY date $sorttype LIMIT $start,$maxfeedback");
		if($sorttype=="DESC") $n = $gesamt-($commentspage-1)*$maxfeedback;
		else $n = ($commentspage-1)*$maxfeedback+1;
	}
	if($gesamt) {

		eval ("\$title_comments = \"".gettemplate("title_comments")."\";");
		echo $title_comments;

		if($sorttype=="ASC") {
			$sorter='<a href="'.$referer.'&amp;commentspage='.$commentspage.'&amp;sorttype=DESC">'.$_language->module['sort'].'</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="'.$_language->module['sort_desc'].'" />&nbsp;&nbsp;&nbsp;';
		} else {
			$sorter='<a href="'.$referer.'&amp;commentspage='.$commentspage.'&amp;sorttype=ASC">'.$_language->module['sort'].'</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="'.$_language->module['sort_asc'].'" />&nbsp;&nbsp;&nbsp;';
		}

		eval ("\$comments_head = \"".gettemplate("comments_head")."\";");
		echo $comments_head;

		while($ds=mysql_fetch_array($ergebnis)) {
			$n%2 ? $bg1=BG_1 : $bg1=BG_3;

			$date=date("d.m.Y - H:i", $ds['date']);

			if($ds['userID']) {
				$ip='';
				$poster='<a class="titlelink" href="index.php?site=profile&amp;id='.$ds['userID'].'"><b>'.strip_tags(getnickname($ds['userID'])).'</b></a>';
				if(isclanmember($ds['userID'])) $member=$_language->module['clanmember_icon'];
				else $member='';

				$quotemessage = addslashes(getinput($ds['comment']));
				$quotemessage = str_replace(array("\r\n","\r","\n"),array('\r\n','\r','\n'),$quotemessage);
				$quotenickname = addslashes(getinput(getnickname($ds['userID'])));
				$quote=str_replace(Array('%nickname%', '%message%'), Array($quotenickname, $quotemessage), $_language->module['quote_link']);

				$country='[flag]'.getcountry($ds['userID']).'[/flag]';
				$country=flags($country);
				
				if ($email = getemail($ds['userID']) AND !getemailhide($ds['userID'])) $email = str_replace('%email%', mail_protect($email), $_language->module['email_link']);
				else $email='';
				$gethomepage = gethomepage($ds['userID']);
				if ($gethomepage != "" && $gethomepage != "http://" && $gethomepage != "http:///" && $gethomepage != "n/a") $hp = '<a href="http://'.$gethomepage.'" target="_blank"><img src="images/icons/hp.gif" border="0" width="14" height="14" alt="'.$_language->module['homepage'].'" /></a>';
				else $hp='';
				
				if(isonline($ds['userID'])=="offline") $statuspic='<img src="images/icons/offline.gif" width="7" height="7" alt="offline" />';
				else $statuspic='<img src="images/icons/online.gif" width="7" height="7" alt="online" />';

				$avatar='<img src="images/avatars/'.getavatar($ds['userID']).'" align="left" alt="Avatar" />';

				if($loggedin && $ds['userID'] != $userID) {
					$pm='<a href="index.php?site=messenger&amp;action=touser&amp;touser='.$ds['userID'].'"><img src="images/icons/pm.gif" border="0" width="12" height="13" alt="'.$_language->module['send_message'].'" /></a>';
					if(isignored($userID, $ds['userID'])) $buddy='<a href="buddys.php?action=readd&amp;id='.$ds['userID'].'&amp;userID='.$userID.'"><img src="images/icons/buddy_readd.gif" width="16" height="16" border="0" alt="'.$_language->module['readd_buddy'].'" /></a>';
					elseif(isbuddy($userID, $ds['userID'])) $buddy='<a href="buddys.php?action=ignore&amp;id='.$ds['userID'].'&amp;userID='.$userID.'"><img src="images/icons/buddy_ignore.gif" width="16" height="16" border="0" alt="'.$_language->module['ignore_user'].'" /></a>';
					elseif($userID==$ds['userID']) $buddy='';
					else $buddy='<a href="buddys.php?action=add&amp;id='.$ds['userID'].'&amp;userID='.$userID.'"><img src="images/icons/buddy_add.gif" width="16" height="16" border="0" alt="'.$_language->module['add_buddy'].'" /></a>';
				} else {
					$pm='';
					$buddy='';
				}
			}
			else {
				$member='';
				$avatar='<img src="images/avatars/noavatar.gif" align="left" alt="Avatar" />';
				$country='';
				$pm='';
				$buddy='';
				$statuspic='';
				$ds['nickname'] = strip_tags($ds['nickname']);
				$ds['nickname'] = htmlspecialchars($ds['nickname']);
				$poster = strip_tags($ds['nickname']);

				$ds['email'] = strip_tags($ds['email']);
				$ds['email'] = htmlspecialchars($ds['email']);
				if($ds['email'])  $email = str_replace('%email%', mail_protect($ds['email']), $_language->module['email_link']);
				else $email='';

				$ds['url'] = strip_tags($ds['url']);
				$ds['url'] = htmlspecialchars($ds['url']);
				if(!stristr($ds['url'],'http://')) $ds['url'] = "http://".$ds['url'];
				if($ds['url']!="http://" && $ds['url']!="") $hp = '<a href="'.$ds['url'].'" target="_blank"><img src="images/icons/hp.gif" border="0" width="14" height="14" alt="'.$_language->module['homepage'].'" /></a>';
				else $hp='';
				$ip = 'IP: ';
				if(isfeedbackadmin($userID)) $ip.=$ds['ip'];
				else $ip.='saved';

				$quotemessage = addslashes(getinput($ds['comment']));
				$quotenickname = addslashes(getinput($ds['nickname']));
				$quote=str_replace(Array('%nickname%', '%message%'), Array($quotenickname, $quotemessage), $_language->module['quote_link']);
			}

			$content = cleartext($ds['comment']);
			$content = toggle($content, $ds['commentID']);

			if(isfeedbackadmin($userID) or iscommentposter($userID,$ds['commentID'])) {
				$edit = '<a href="index.php?site=comments&amp;editcomment=true&amp;id='.$ds['commentID'].'&amp;ref='.urlencode($referer).'" title="'.$_language->module['edit_comment'].'"><img src="images/icons/edit.gif" border="0" alt="'.$_language->module['edit_comment'].'" /></a>';
			} else $edit='';

			if(isfeedbackadmin($userID)) $actions='<input class="input" type="checkbox" name="commentID[]" value="'.$ds['commentID'].'" />';
			else $actions='';

			eval ("\$comments = \"".gettemplate("comments")."\";");
			echo $comments;

			unset($member);
			unset($quote);
			unset($country);
			unset($email);
			unset($hp);
			unset($avatar);
			unset($pm);
			unset($buddy);
			unset($ip);
			unset($edit);

			if(isfeedbackadmin($userID)) {
				$submit='<input type="hidden" name="referer" value="'.$referer.'" />
        <input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'
        <input type="submit" value="'.$_language->module['delete_selected'].'" />';
			}	else $submit='';

			if($sorttype=="DESC") $n--;
			else $n++;
		}

		eval ("\$comments_foot = \"".gettemplate("comments_foot")."\";");
		echo $comments_foot;

	}

	if($comments_allowed) {
		if($loggedin) {
      
      		eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
			eval ("\$comments_add_user = \"".gettemplate("comments_add_user")."\";");
			echo $comments_add_user;
		}
		elseif($comments_allowed == 2) {
    
			$ip = getenv('REMOTE_ADDR');
			
			if (isset($_COOKIE['visitor_info'])) {
				$visitor = explode("--||--", $_COOKIE['visitor_info']);
				$name = getforminput(stripslashes($visitor[0]));
				$mail = getforminput(stripslashes($visitor[1]));
				$url = getforminput(stripslashes($visitor[2]));
			}
			else{
				$url = "http://";
				$name = "";
				$mail = "";
			}
			
			if(isset($_GET['error'])) $err = $_GET['error'];
			else $err = "";
			if($err=="nickname"){
				$error = $_language->module['error_nickname'];
				$name = "";
			}
			elseif($err=="captcha") $error = $_language->module['error_captcha'];
			else $error = '';
			
			if(isset($_SESSION['comments_message'])){
				$message = getforminput($_SESSION['comments_message']);
				unset($_SESSION['comments_message']);
			}
			else{
				$message = "";
			}
			
			$CAPCLASS = new Captcha;
			$captcha = $CAPCLASS->create_captcha();
			$hash = $CAPCLASS->get_hash();
			$CAPCLASS->clear_oldcaptcha();

      		eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
			eval ("\$comments_add_visitor = \"".gettemplate("comments_add_visitor")."\";");
			echo $comments_add_visitor;
		}
		else echo $_language->module['no_access'];
	}
	else echo $_language->module['comments_disabled'];
}

?>
