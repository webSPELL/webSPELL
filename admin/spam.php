<?php
if(isset($_GET['getnickname'])){
	chdir('../');
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	chdir('admin');
	if(!ispageadmin($userID)) die();
	echo "<a target='_blank' href='../index.php?site=profile&id=".$_GET['getnickname']."'>".getnickname($_GET['getnickname']).'</a> since '.getregistered($_GET['getnickname']).'';
	exit();
}
if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die();
$_language->read_module('spam');
function deleteSpamUser($spammerID){
	global $_language;
	// Delete Comments
	safe_query("DELETE FROM ".PREFIX."comments WHERE userID='".$spammerID."'");
	echo mysqli_affected_rows()." ".$_language->module["comments_deleted"]."<br/>";
	// Delete Forum Topics (update posts / topics)
	$topics = safe_query("SELECT topicID,boardID FROM ".PREFIX."forum_topics WHERE userID='".$spammerID."'");
	$topicIDs = array();
	$boardIDs = array();
	while($ds = mysqli_fetch_assoc($topics)){
		$topicIDs[] = $ds['topicID'];
		$boardIDs[] = $ds['boardID'];
	}
	if(!empty($topicIDs)){
		safe_query("DELETE FROM ".PREFIX."forum_notify WHERE topicID IN (".implode(",",$topicIDs).")");
		safe_query("DELETE FROM ".PREFIX."forum_topics WHERE topicID IN (".implode(",",$topicIDs).")");
		safe_query("DELETE FROM ".PREFIX."forum_topics WHERE moveID IN (".implode(",",$topicIDs).")");
		safe_query("DELETE FROM ".PREFIX."forum_posts WHERE topicID IN (".implode(",",$topicIDs).")");
		echo count($topicIDs)." ".$_language->module['topics_deleted']."<br/>";
	}
	$update_topics = array();
	$posts = safe_query("SELECT * FROM ".PREFIX."forum_posts WHERE poster='".$spammerID."'");
	while($ds = mysqli_fetch_assoc($posts)){
		$update_topics[] = $ds['topicID'];
	}
	$update_topics = array_unique($update_topics);
	safe_query("DELETE FROM ".PREFIX."forum_posts WHERE poster='".$spammerID."'");
	echo mysqli_num_rows($posts)." ".$_language->module["posts_deleted"]."<br/>";

	if(!empty($update_topics)){
		safe_query("UPDATE ".PREFIX."forum_topics t SET replys 	=  (SELECT COUNT(postID) FROM ".PREFIX."forum_posts p WHERE p.topicID = t.topicID) -1
																												WHERE t.topicID IN (".implode(",",$update_topics).")");
		echo count($update_topics)." ".$_language->module["topic_count_updated"]."<br/>";
	}

	$topic = safe_query("SELECT topicID, boardID FROM ".PREFIX."forum_topics WHERE lastposter = '".$spammerID."'");
	$num = mysqli_num_rows($topic);
	while($ds=mysqli_fetch_assoc($topic)){
		$get = safe_query("SELECT poster, date FROM ".PREFIX."forum_posts WHERE topicID='".$ds['topicID']."' ORDER BY date DESC LIMIT 0,1");
		$topicData = mysqli_fetch_assoc($get);
		safe_query("UPDATE ".PREFIX."forum_topics SET lastposter='".$topicData['poster']."', lastdate='".$topicData['date']."' WHERE topicID='".$ds['topicID']."'");
		$boardIDs[] = $ds['boardID'];
	}
	echo $num." ".$_language->module["topics_updated"]."<br/>";
	if(!empty($boardIDs)){
		$boardIDs = array_unique($boardIDs);
		safe_query("UPDATE ".PREFIX."forum_boards b SET topics 	= (SELECT COUNT(topicID) FROM ".PREFIX."forum_topics t WHERE t.boardID = b.boardID),
																												posts 	= (SELECT COUNT(postID) FROM ".PREFIX."forum_posts p WHERE p.boardID = b.boardID) 
																												WHERE b.boardID IN (".implode(",",$boardIDs).")");
		echo count($boardIDs)." ".$_language->module["boards_updated"]."<br>";
	}

	// Delete Guestbooks
	$get = safe_query("SELECT nickname, email FROM ".PREFIX."user WHERE userID='".$spammerID."'");
	$spammer = mysqli_fetch_assoc($get);
	//safe_query("DELETE FROM ".PREFIX."guestbook WHERE name='".$spammer['nickname']."' AND email='".$spammer['email']."'");
	$user_g_book = safe_query("DELETE FROM ".PREFIX."user_gbook WHERE name='".$spammer['nickname']."' AND email='".$spammer['email']."'");
	echo mysqli_affected_rows()." ".$_language->module["guestbook_deleted"]."<br/>";

	// Delete Messenges
	$mess = safe_query("DELETE FROM ".PREFIX."messenger WHERE userID='".$spammerID."' OR fromuser='".$spammerID."'");
	echo mysqli_affected_rows()." ".$_language->module["messages_deleted"]."<br/>";

	//safe_query("DELETE FROM ".PREFIX."user WHERE userID='".$spammerID."'");
	safe_query("UPDATE ".PREFIX."user SET banned='perm', ban_reason='Spam',about='' WHERE userID='".$spammerID."'");
	echo $_language->module["user_banned"]."<br/>";
}
if(isset($_GET['action'])) $action = $_GET['action'];
else $action = null;


if($action == "user"){
	echo'<h1>&curren; Spam</h1>';
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	if(isset($_GET['user'])){
		$id = $_GET['user'];
		$nick = getnickname($id);
	}
	else{
		$nick = $id = "";
	}
	echo '<form method="post" id="post" name="post" action="admincenter.php?site=spam&amp;action=user_ban">';
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td valign="top" width="25%">'.$_language->module["userID"].':</td>
		        <td valign="top"><input type="text" name="id" value="'.$id.'" onchange="fetch(\'spam.php?getnickname=\'+this.value+\'\',\'nick\',\'replace\',\'event\');"/></td>
		      </tr>
		      <tr>
		        <td valign="top">'.$_language->module["profile"].':</td>
		        <td valign="top"><span id="nick">'.$nick.'</span></td>
		      </tr>
		    </table>';
	echo '<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="spam" value="'.$_language->module["ban_user"].'" />
  </form>';
}
elseif($action == "user_ban"){
	echo'<h1>&curren; Spam</h1>';
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$spammerID = $_POST['id'];
		if(!is_numeric($spammerID)){
			echo $_language->module["userid_must_be_numeric"];
		}
		elseif(isclanmember($spammerID)){
			echo $_language->module["cant_delete_team_members"];
		}
		else{
			echo "<h3>".getnickname($spammerID)."</h3>";
			deleteSpamUser($spammerID);
		}
	}
	else{
		echo $_language->module['transaction_invalid'];
	}
}
elseif($action == "multi"){
	echo'<h1>&curren; Multiple Accounts</h1>';
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	$get = safe_query("SELECT ip, GROUP_CONCAT(userID) AS `ids`, GROUP_CONCAT(password) AS `passwords` FROM ".PREFIX."user WHERE ip !='' AND lastlogin > '".(time()-60*60*24*90)."' GROUP BY ip HAVING COUNT(userID) > 1  ORDER BY lastlogin DESC");
	if(mysqli_num_rows($get)){
		echo '<table border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD" width="100%">
    <tr>
    <td class="title">'.$_language->module["userID"].':</td>
    <td class="title">'.$_language->module["nickname"].':</td>
    <td class="title">'.$_language->module["register_date"].':</td>
    <td class="title">'.$_language->module["last_login"].':</td>
    <td class="title">'.$_language->module["activated"].':</td>
    <td class="title">'.$_language->module["banned"].':</td>
    <td class="title">'.$_language->module["posts"].':</td>
    </tr>';
		$i = 0;
		while($ds = mysqli_fetch_assoc($get)){
			$ids = explode(",",$ds['ids']);
			$passwords = explode(",",$ds['passwords']);
			if($i%2) { $td='td1'; }
			else { $td='td2'; }
			$buttons  = ' - <a href="http://geoip.flagfox.net/?ip='.$ds['ip'].'">'.$_language->module["ip_look_up"].'</a>';
			$buttons .= ' - <a onclick="MM_confirm(\''.$_language->module["question_delete_all"].'\', \'admincenter.php?site=spam&amp;action=multi_ban&amp;ip='.$ds['ip'].'&amp;captcha_hash='.$hash.'\')">'.$_language->module["ban_and_delete_all"].'</a>';
			$buttons .= ' - <a onclick="MM_confirm(\''.$_language->module["question_ban_all"].'\', \'admincenter.php?site=spam&amp;action=multi_just_block&amp;ip='.$ds['ip'].'&amp;captcha_hash='.$hash.'\')">'.$_language->module["ban_all"].'</a>';
			echo '<tr>
 			<td colspan="7" class="'.$td.'"><b>'.$ds['ip'].'</b>'.$buttons.'</td>
 			</tr>';
			foreach($ids as $key => $id){
				if($i%2) { $td='td1'; }
				else { $td='td2'; }
				$get_u = safe_query("SELECT * FROM ".PREFIX."user WHERE userID='".$id."'");
				$data = mysqli_fetch_assoc($get_u);
					
				$active = ($data['activated'] == '1') ? "<font color='green'>&#10004;</font>" : "<font color='red'>&#10006;</font>";
				$banned = ($data['banned'] != null) ? "<font color='red'>&#10004;</font>" : "<font color='green'>&#10006;</font>";
					
				if($data['lastlogin'] > time() - (60*60*24*10)){
					$last_login = '<font color="green">'.date('d.m.Y',$data['lastlogin']).'</font>';
				}
				else{
					$last_login = date('d.m.Y',$data['lastlogin']);
				}
					
				$posts = getuserforumposts($data['userID']);
				if($posts > 5){
					$posts ='<b>'.$posts.'</b>';
				}
					
				echo '<tr>
 				<td class="'.$td.'">'.$data['userID'].'</td>
 				<td class="'.$td.'"><a href="../index.php?site=profile&amp;id='.$data['userID'].'" target="_blank">'.$data['nickname'].'</a><br/><small>'.$_language->module["password"].': '.$passwords[$key].'</small></td>
 				<td class="'.$td.'">'.date('d.m.Y',$data['registerdate']).'</td>
 				<td class="'.$td.'">'.$last_login.'</td>
 				<td class="'.$td.'">'.$active.'</td>
 				<td class="'.$td.'">'.$banned.'</td>
 				<td class="'.$td.'">'.$posts.'</td>
 				</tr>';
				$i++;
			}
			$i++;
		}
		echo '</table>';
	}
	else{
		echo $_language->module["no_accounts_with_same_ips"];
	}
}
elseif($action == "multi_ban"){
	echo'<h1>&curren; Multiple Accounts</h1>';
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		$ip = $_GET['ip'];
		$get = safe_query("SELECT userID, nickname FROM ".PREFIX."user WHERE ip='".$ip."'");
		while($ds = mysqli_fetch_assoc($get)){
			echo "<h3>".$ds['nickname']."</h3>";
			if(isclanmember($ds['userID']) == false){
				deleteSpamUser($ds['userID']);
			}
			else{
				echo $_language->module["cant_delete_team_members"];
			}
		}
	}
	else{
		echo $_language->module['transaction_invalid'];
	}
}
elseif($action == "multi_just_block"){
	echo'<h1>&curren; Multiple Accounts</h1>';
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		$ip = $_GET['ip'];
		$get = safe_query("SELECT userID, nickname,GROUP_CONCAT(nickname) AS `nicknames` FROM ".PREFIX."user WHERE ip='".$ip."' GROUP BY ip");
		while($ds = mysqli_fetch_assoc($get)){
			echo "<h3>".$ip."</h3>";
			if(isclanmember($ds['userID']) == false){
				safe_query("UPDATE ".PREFIX."user SET banned='perm', ban_reason='Multi Accounts (".$_database->escape_string($ds['nicknames']).")' WHERE ip='".$ip."'");
				echo $_language->module["user_banned"]." (".$_language->module["nothing_deleted"].")<br/>".$ds['nicknames'];
			}
			else{
				echo $_language->module["cant_delete_team_members"];
			}
		}
	}
	else{
		echo $_language->module['transaction_invalid'];
	}
}
elseif($action == "api_log"){
	echo'<h1>&curren; Api-Log</h1>';

	if(isset($_GET['del_option']) && $_GET['del_option']== "del_all"){
		safe_query("DELETE FROM ".PREFIX."api_log");
	}

	$get = safe_query("SELECT * FROM ".PREFIX."api_log ORDER BY `date` DESC");
	if(mysqli_num_rows($get)){

		echo '<input type="button" onclick="MM_confirm(\''.$_language->module["question_delete_all"].'\', \'admincenter.php?site=spam&amp;action=api_log&amp;del_option=del_all\')" value="'.$_language->module["delete_all"].'" /><br/><br/>';

		echo '<table border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD" width="100%">
		<tr>
		<td class="title">'.$_language->module["message"].':</td>
		<td class="title">'.$_language->module["date"].':</td>
		</tr>';
		while($ds = mysqli_fetch_assoc($get)){

			if($i%2) {
				$td='td1';
			}
			else { $td='td2';
			}
			
			echo '<tr>
			<td class="'.$td.'"><font color="red">'.getinput($ds['message']).'</font><br/>'.$ds['data'].'</td>
			<td class="'.$td.'">'.date('d.m.Y H:i:s',$ds['date']).'</td>
			</tr>';
			
			$i++;

		}
		echo '</table>';
	}
	else{
		echo "n/a";
	}
}
elseif($action == "forum_spam"){
	echo'<h1>&curren; Forum Spam</h1>';
	
	echo '<input type="button" onclick="MM_confirm(\''.$_language->module["question_delete_all"].'\', \'admincenter.php?site=spam&amp;action=forum_spam&amp;del_option=del_all\')" value="'.$_language->module["delete_all"].'" />';
	
	if(isset($_GET['del_option']) && $_GET['del_option']== "del_all"){
		$get = safe_query("SELECT userID FROM ".PREFIX."forum_topics_spam");
		while($ds = mysqli_fetch_assoc($get)){
			safe_query("UPDATE ".PREFIX."user SET banned='perm', ban_reason='Spam' WHERE userID='".$ds["userID"]."'");
		}
		safe_query("DELETE FROM ".PREFIX."forum_topics_spam");

		$get = safe_query("SELECT poster FROM ".PREFIX."forum_posts_spam");
		while($ds = mysqli_fetch_assoc($get)){
			safe_query("UPDATE ".PREFIX."user SET banned='perm', ban_reason='Spam' WHERE userID='".$ds["poster"]."'");
		}
		safe_query("DELETE FROM ".PREFIX."forum_posts_spam");
		safe_query("DELETE FROM ".PREFIX."comments_spam");
	}
	elseif(isset($_GET['del_option']) && $_GET['del_option']== "delete_topic"){
		$topicID = $_GET['topicID'];
		safe_query("DELETE FROM ".PREFIX."forum_topics_spam WHERE topicID='".$topicID."'");
	}
	elseif(isset($_GET['del_option']) && $_GET['del_option'] == "delete_post"){
		$postID = $_GET['postID'];
		safe_query("DELETE FROM ".PREFIX."forum_posts_spam WHERE postID='".$postID."'");
	}
	elseif(isset($_GET['del_option']) && $_GET['del_option'] == "delete_comment"){
		$commentID = $_GET['commentID'];
		safe_query("DELETE FROM ".PREFIX."comments_spam WHERE commentID='".$commentID."'");
	}
	
	echo "<h3>Topics</h3>";
	
	$get = safe_query("SELECT * FROM ".PREFIX."forum_topics_spam ORDER BY date DESC");
	if(mysqli_num_rows($get)){
		echo '<table border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD" width="100%">
		<tr>
		<td class="title">'.$_language->module["nickname"].':</td>
		<td class="title">'.$_language->module["date"].':</td>
		<td class="title">'.$_language->module["topic"].':</td>
		<td class="title">'.$_language->module["message"].':</td>
		<td class="title">'.$_language->module["options"].':</td>
		</tr>';
		
		$i = 0;
		
		while($ds = mysqli_fetch_assoc($get)){
			if($i%2) {
				$td='td1';
			}
			else { $td='td2';
			}
			
			$options = '<input type="button" onclick="MM_confirm(\''.$_language->module["question_delete"].'\', \'admincenter.php?site=spam&amp;action=forum_spam&amp;del_option=delete_topic&amp;topicID='.$ds['topicID'].'\')" value="'.$_language->module["delete"].'" />';
			
			echo '<tr>
			<td class="'.$td.'"><a href="../index.php?site=profile&amp;id='.$ds['userID'].'" target="_blank">'.getnickname($ds['userID']).'</a></td>
			<td class="'.$td.'">'.date('d.m.Y',$ds['date']).'</td>
			<td class="'.$td.'">'.getinput($ds['topic']).'</td>
			<td class="'.$td.'">'.getinput($ds['message']).'</td>
			<td class="'.$td.'">'.$options.'</td>
			</tr>';
			
			$i++;
		}
		
		echo '</table>';
	}
	else{
		echo "n/a";
	}
	
	echo "<h3>Posts</h3>";
	
	$get = safe_query("SELECT * FROM ".PREFIX."forum_posts_spam ORDER BY date DESC");
	if(mysqli_num_rows($get)){
		echo '<table border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD" width="100%">
		<tr>
		<td class="title">'.$_language->module["nickname"].':</td>
		<td class="title">'.$_language->module["date"].':</td>
		<td class="title">'.$_language->module["message"].':</td>
		<td class="title">'.$_language->module["options"].':</td>
		</tr>';
		
		$i = 0;
		
		while($ds = mysqli_fetch_assoc($get)){
			if($i%2) {
				$td='td1';
			}
			else { $td='td2';
			}
			
			$options = '<input type="button" onclick="MM_confirm(\''.$_language->module["question_delete"].'\', \'admincenter.php?site=spam&amp;action=forum_spam&amp;del_option=delete_post&amp;postID='.$ds['postID'].'\')" value="'.$_language->module["delete"].'" />';
			
			echo '<tr>
			<td class="'.$td.'"><a href="../index.php?site=profile&amp;id='.$ds['poster'].'" target="_blank">'.getnickname($ds['poster']).'</a></td>
			<td class="'.$td.'">'.date('d.m.Y',$ds['date']).'</td>
			<td class="'.$td.'">'.mb_substr(getinput($ds['message']),0,250).'...</td>
			<td class="'.$td.'">'.$options.'</td>
			</tr>';
			
			$i++;
		}
		
		echo '</table>';
	}
	else{
		echo "n/a";
	}

	echo "<h3>Comments</h3>";
	
	$get = safe_query("SELECT * FROM ".PREFIX."comments_spam ORDER BY date DESC");
	if(mysqli_num_rows($get)){
		echo '<table border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD" width="100%">
		<tr>
		<td class="title">'.$_language->module["nickname"].':</td>
		<td class="title">'.$_language->module["date"].':</td>
		<td class="title">'.$_language->module["message"].':</td>
		<td class="title">'.$_language->module["options"].':</td>
		</tr>';
		
		$i = 0;
		
		while($ds = mysqli_fetch_assoc($get)){
			if($i%2) {
				$td='td1';
			}
			else { $td='td2';
			}
			
			$options = '<input type="button" onclick="MM_confirm(\''.$_language->module["question_delete"].'\', \'admincenter.php?site=spam&amp;action=forum_spam&amp;del_option=delete_comment&amp;commentID='.$ds['commentID'].'\')" value="'.$_language->module["delete"].'" />';
			if(!empty($ds['userID'])){
				$nick = '<a href="../index.php?site=profile&amp;id='.$ds['userID'].'" target="_blank">'.getnickname($ds['userID']).'</a>';
			}
			else{
				$nick = $ds['nickname'];
			}
			echo '<tr>
			<td class="'.$td.'">'.$nick.'</a></td>
			<td class="'.$td.'">'.date('d.m.Y',$ds['date']).'</td>
			<td class="'.$td.'">'.mb_substr(getinput($ds['comment']),0,250).'...</td>
			<td class="'.$td.'">'.$options.'</td>
			</tr>';
			
			$i++;
		}
		
		echo '</table>';
	}
	else{
		echo "n/a";
	}
	
}
else{
	
}
?>
