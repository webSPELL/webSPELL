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

$_language->read_module('users');

if(!isuseradmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_POST['add'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$anz = mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE squadID='".$_POST['squad']."' AND userID='".$_POST['id']."'"));
		if(!$anz){
			safe_query("INSERT INTO ".PREFIX."squads_members (squadID, userID, position, activity, sort) values('".$_POST['squad']."', '".$_POST['id']."', '".$_POST['position']."', '".$_POST['activity']."', '1')");	
		}
	  	else{
			echo $_language->module['user_exists'];
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['edit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$avatar = $_FILES['avatar'];
		$userpic = $_FILES['userpic'];
		$id = $_POST['id'];
	
		//avatar
		$filepath = "../images/avatars/";
		if(isset($_POST['avatar_url'])) $avatar_url = $_POST['avatar_url'];
		else $avatar_url = '';
		
		if($avatar['name'] != "" or ($avatar_url != "" and $avatar_url != "http://")) {
			if($avatar['name'] != "") {
				move_uploaded_file($avatar['tmp_name'], $filepath.$avatar['name'].".tmp");
			}
			else {
				$avatar['name'] = strrchr($avatar_url,"/");
				if(!copy($_POST['avatar_url'],$filepath.$avatar['name'].".tmp")) {
					$error = $_language->module['can_not_copy'];
					die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
				}
			}
			@chmod($filepath.$avatar['name'].".tmp", $new_chmod);
			$info = getimagesize($filepath.$avatar['name'].".tmp");
			if($info[0] < 91 && $info[1] < 91) {
				$pic = '';
				if($info[2] == 1) $pic=$id.'.gif';
				elseif($info[2] == 2) $pic=$id.'.jpg';
				elseif($info[2] == 3) $pic=$id.'.png';
				if($pic != "") {
					if(file_exists($filepath.$id.'.gif')) @unlink($filepath.$id.'.gif');
					if(file_exists($filepath.$id.'.jpg')) @unlink($filepath.$id.'.jpg');
					if(file_exists($filepath.$id.'.png')) @unlink($filepath.$id.'.png');
					rename($filepath.$avatar['name'].'.tmp', $filepath.$pic);
					safe_query("UPDATE ".PREFIX."user SET avatar='".$pic."' WHERE userID='".$id."'");
				}
				else {
					if(unlink($filepath.$avatar['name'].".tmp")) {
						$error = $_language->module['invalid_format'];
						die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
					}
					else {
						$error = $_language->module['upload_failed'];
						die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
					}
				}
			}
			else {
				@unlink($filepath.$avatar['name'].".tmp");
				$error = $_language->module['error_avatar'];
				die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
			}
		}

		//userpic
		$filepath = "../images/userpics/";
		if(isset($_POST['userpic_url'])) $userpic_url = $_POST['userpic_url'];
		else $userpic_url = '';
		
		if($userpic['name'] != "" or ($userpic_url != "" and $userpic_url != "http://")) {
			if($userpic['name'] != "") {
				move_uploaded_file($userpic['tmp_name'], $filepath.$userpic['name'].".tmp");
			} else {
				$userpic['name'] = strrchr($userpic_url,"/");
				if(!copy($_POST['userpic_url'],$filepath.$userpic['name'].".tmp")) {
					$error = $_language->module['can_not_copy'];
					die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
				}
			}
			@chmod($filepath.$userpic['name'].".tmp", $new_chmod);
			$info = getimagesize($filepath.$userpic['name'].".tmp");
			if($info[0] < 231 && $info[1] < 211) {
				$pic = '';
				if($info[2] == 1) $pic=$id.'.gif';
				elseif($info[2] == 2) $pic=$id.'.jpg';
				elseif($info[2] == 3) $pic=$id.'.png';
				if($pic != "") {
					if(file_exists($filepath.$id.'.gif')) @unlink($filepath.$id.'.gif');
					if(file_exists($filepath.$id.'.jpg')) @unlink($filepath.$id.'.jpg');
					if(file_exists($filepath.$id.'.png')) @unlink($filepath.$id.'.png');
					rename($filepath.$userpic['name'].".tmp", $filepath.$pic);
					safe_query("UPDATE ".PREFIX."user SET userpic='".$pic."' WHERE userID='".$id."'");
				}
				else {
					if(unlink($filepath.$userpic['name'].".tmp")) {
						$error = $_language->module['invalid_format'];
						die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
					}
					else {
						$error = $_language->module['upload_failed'];
						die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
					}
				}
			}
			else {
				@unlink($filepath.$userpic['name'].".tmp");
				$error = $_language->module['error_picture'];
				die('ERROR: '.$error.'<br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
			}
		}
		
	  $b_day = $_POST['b_day'];
	  $b_month = $_POST['b_month'];
	  $b_year = $_POST['b_year'];
	  $birthday = $b_year.'.'.$b_month.'.'.$b_day;
	  $nickname = htmlspecialchars(mb_substr(trim($_POST['nickname']), 0, 30));
	  
	  if(!mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user WHERE nickname='".$nickname."' AND userID!=".$_POST['id']))) {
	  
	  	safe_query("UPDATE ".PREFIX."user SET nickname='".$nickname."',
									 email='".$_POST['email']."',
									 firstname='".$_POST['firstname']."',
									 lastname='".$_POST['lastname']."',
									 sex='".$_POST['sex']."',
									 country='".$_POST['flag']."',
									 town='".$_POST['town']."',
									 birthday='".$birthday."',
									 icq='".$_POST['icq']."',
									 usertext='".$_POST['usertext']."',
									 clantag='".$_POST['clantag']."',
									 clanname='".$_POST['clanname']."',
									 clanhp='".$_POST['clanhp']."',
									 clanirc='".$_POST['clanirc']."',
									 clanhistory='".$_POST['clanhistory']."',
									 cpu='".$_POST['cpu']."',
									 mainboard='".$_POST['mainboard']."',
									 ram='".$_POST['ram']."',
									 monitor='".$_POST['monitor']."',
									 graphiccard='".$_POST['graphiccard']."',
									 soundcard='".$_POST['soundcard']."',
									 verbindung='".$_POST['connection']."',
									 keyboard='".$_POST['keyboard']."',
									 mouse='".$_POST['mouse']."',
	 								 mousepad='".$_POST['mousepad']."',
									 homepage='".$_POST['homepage']."',
									 about='".$_POST['about']."' WHERE userID='".$_POST['id']."' ");
	
			if(isset($_POST['avatar'])) {
				safe_query("UPDATE ".PREFIX."user SET avatar='' WHERE userID='".$_POST['id']."'");
				@unlink('../images/avatars/'.$_POST['id'].'.gif');
				@unlink('../images/avatars/'.$_POST['id'].'.jpg');
				@unlink('../images/avatars/'.$_POST['id'].'.png');
			}
			if(isset($_POST['userpic'])) {
				safe_query("UPDATE ".PREFIX."user SET userpic='' WHERE userID='".$_POST['id']."'");
				@unlink('../images/userpics/'.$_POST['id'].'.gif');
				@unlink('../images/userpics/'.$_POST['id'].'.jpg');
				@unlink('../images/userpics/'.$_POST['id'].'.png');
			}

	  } else echo $_language->module['user_exists'];
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['newuser'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$newnickname = htmlspecialchars(mb_substr(trim($_POST['username']), 0, 30));
		$newusername = mb_substr(trim($_POST['username']), 0, 30);
		$anz = mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user WHERE (username='".$newusername."' OR nickname='".$newnickname."') "));
		if(!$anz AND $newusername!=""){
			safe_query("INSERT INTO ".PREFIX."user ( username, nickname, password, registerdate, activated) VALUES( '".$newusername."', '".$newnickname."', '".md5(stripslashes($_POST['pass']))."', '".time()."', 1) ");
			safe_query("INSERT INTO ".PREFIX."user_groups ( userID ) values('".mysql_insert_id()."' )");
		} else echo $_language->module['user_exists'];
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET['delete'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		$id = $_GET['id'];
		if(!issuperadmin($id) OR (issuperadmin($id) AND issuperadmin($userID))) {
			safe_query("DELETE FROM ".PREFIX."forum_moderators WHERE userID='$id'");
			safe_query("DELETE FROM ".PREFIX."messenger WHERE touser='$id'");
			safe_query("DELETE FROM ".PREFIX."squads_members WHERE userID='$id'");
			safe_query("DELETE FROM ".PREFIX."upcoming_announce WHERE userID='$id'");
			safe_query("DELETE FROM ".PREFIX."user WHERE userID='$id'");
			safe_query("DELETE FROM ".PREFIX."user_groups WHERE userID='$id'");
			$userfiles=Array('../images/avatars/'.$id.'.jpg', '../images/avatars/'.$id.'.gif', '../images/userpics/'.$id.'.jpg', '../images/userpics/'.$id.'.gif');
			foreach($userfiles as $file) {
				if(file_exists($file)) unlink($file);
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['ban'])) {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$id = $_POST['id'];
		if(isset($_POST['permanent'])) $permanent = $_POST['permanent'];
		else $permanent = 0;
		if(isset($_POST['ban_num'])) $ban_num = ($_POST['ban_num']);
		else $ban_num = 0;	
		if(isset($_POST['ban_multi'])) $ban_multi = ($_POST['ban_multi']);
		else $ban_multi = 0;
		$reason = $_POST['reason'];
		
		if(isset($_POST['remove_ban'])) {
			safe_query("UPDATE ".PREFIX."user SET banned=(NULL) WHERE userID='$id'");
		}
		else {
			if($permanent == "1") {
				safe_query("UPDATE ".PREFIX."user SET banned='perm', ban_reason='".$reason."' WHERE userID='$id'");
			}
			else {
				if($ban_num && $ban_multi) {
					$ban_time = time()+(60*60*24*$ban_num*$ban_multi);
					safe_query("UPDATE ".PREFIX."user SET banned='".$ban_time."', ban_reason='".$reason."' WHERE userID='$id'");
				}
				else {
					$ban_time = mktime(0,0,0,$_POST['u_month'],$_POST['u_day'],$_POST['u_year']);
					safe_query("UPDATE ".PREFIX."user SET banned='".$ban_time."', ban_reason='".$reason."' WHERE userID='$id'");
				}
			}
		}
	} 
	else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="activate") {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		$id = $_GET['id'];
  		safe_query("UPDATE ".PREFIX."user SET activated='1' WHERE userID='$id'");
  		redirect('admincenter.php?site=users','',0);
  	} else echo $_language->module['transaction_invalid'];
}

elseif($action=="ban") {
	
	echo'<h1>&curren; <a href="admincenter.php?site=users" class="white">'.$_language->module['users'].'</a> &raquo; '.$_language->module['ban_user'].'</h1>';
	
	$id = $_GET['id'];
	
	if($userID != $id) {
		if(!issuperadmin($id) OR (issuperadmin($id) AND issuperadmin($userID))) {
			$CAPCLASS = new Captcha;
			$CAPCLASS->create_transaction();
			$hash = $CAPCLASS->get_hash();
			$get = safe_query("SELECT nickname,banned,ban_reason FROM ".PREFIX."user WHERE userID='".$id."'");
			$data = mysql_fetch_assoc($get);
			$nickname = $data['nickname'];
		
			if($data['banned'] == "perm") {
				$checked = "checked='checked'";
				$u_day = '';
				$u_month = '';
				$u_year = '';
				$hide = "style='display:none;'";
			}
			else {
				$checked = '';
				$hide = '';
				if($data['banned']) {
					$u_day = date("d",$data['banned']);
					$u_month = date("m",$data['banned']);
					$u_year = date("Y",$data['banned']);
				}
				else {
					$u_day = "";
					$u_month = "";
					$u_year = "";
				}
			}
			$reason = $data['ban_reason'];
		
			echo'<script type="text/javascript">
				function hide_forms() {
					if(document.getElementById("permanent").checked){
						document.getElementById("until_date").style.display = "none";
						document.getElementById("ban_for").style.display = "none";
					}
					else {
						document.getElementById("until_date").style.display = "";
						document.getElementById("ban_for").style.display = "";
					}
					document.getElementById("u_day").value = "";
					document.getElementById("u_month").value = "";
					document.getElementById("u_year").value = "";
					document.getElementById("ban_num").value = "";
				}
				function kill_form(type) {
					if(type == "until") {
						document.getElementById("permanent").checked == false;
						document.getElementById("ban_num").value = "";
					}
					else {
						document.getElementById("permanent").checked == false;
						document.getElementById("u_day").value = "";
						document.getElementById("u_month").value = "";
						document.getElementById("u_year").value = "";
					}
				}
			</script>
			<form method="post" action="admincenter.php?site=users">
			<table width="100%" border="0" cellspacing="1" cellpadding="3">
			  <tr>
			    <td width="15%"><b>'.$_language->module['nickname'].'</b></td>
			    <td width="85%">'.$nickname.'</td>
			  </tr>
			  <tr id="until_date" '.$hide.'>
			    <td><b>'.$_language->module['ban_until'].':</b></td>
			    <td><input type="text" name="u_day" onchange="kill_form(\'until\');" id="u_day" size="2" value="'.$u_day.'" />.<input type="text" onchange="kill_form(\'until\');" name="u_month" id="u_month" size="2" value="'.$u_month.'" />.<input type="text" onchange="kill_form(\'until\');" name="u_year" id="u_year" size="4" value="'.$u_year.'" /> <i>dd.mm.YY</i></td>
			  </tr>
			  <tr id="ban_for" '.$hide.'>
			    <td><b>'.$_language->module['ban_for'].':</b></td>
			    <td><input type="text" name="ban_num" onchange="kill_form(\'\');" id="ban_num" size="3" /> <select name="ban_multi"><option value="1">'.$_language->module['days'].'</option><option value="7">'.$_language->module['weeks'].'</option><option value="28">'.$_language->module['month'].'</option></select></td>
			  </tr>
			  <tr>
			    <td><b>'.$_language->module['permanently'].'</b></td>
			    <td><input type="checkbox" id="permanent" onchange="hide_forms();" value="1" name="permanent" '.$checked.' /></td>
			  </tr>
			  <tr>
			    <td><b>'.$_language->module['reason'].':</b></td>
			    <td><textarea name="reason" rows="3" cols="" style="width: 50%;">'.$reason.'</textarea></td>
			  </tr>';
		
			if($data['banned']) {
				echo '<tr>
				  <td><b>'.$_language->module['remove_ban'].'</b></td>
				  <td><input type="checkbox" name="remove_ban" value="1" /></td>
				</tr>';
			}
			echo '<tr>
			    <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="id" value="'.$id.'" /></td>
			    <td><br /><input type="submit" name="ban" value="'.$_language->module['edit_ban'].'" /></td>
			  </tr>
			</table>
			</form>';
		}
		else {
			echo $_language->module['you_cant_ban'].'<br /><br />&laquo; <a href="javascript:history.back()">'.$_language->module['back'].'</a>';
		}
	}
	else {
		echo $_language->module['you_cant_ban_yourself'].'<br /><br />&laquo; <a href="javascript:history.back()">'.$_language->module['back'].'</a>';
	}
}

elseif($action=="addtoclan") {
	
  echo'<h1>&curren; <a href="admincenter.php?site=users" class="white">'.$_language->module['users'].'</a> &raquo; '.$_language->module['add_to_clan'].'</h1>';
  
  $id = $_GET['id'];
  $nickname=getnickname($id);
  $squads = getsquads();
  $CAPCLASS = new Captcha;
  $CAPCLASS->create_transaction();
  $hash = $CAPCLASS->get_hash();
  
  echo'<form method="post" action="admincenter.php?site=users&amp;page='.(int)$_GET['page'].'&amp;type='.getforminput($_GET['type']).'&amp;sort='.$_GET['sort'].'">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['nickname'].'</b></td>
      <td width="85%">'.$nickname.'</td>
    </tr>
    <tr>
      <td><b>'.$_language->module['squad'].'</b></td>
      <td><select name="squad">'.$squads.'</select></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['position'].'</b></td>
      <td><input type="text" name="position" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['activity'].'</b></td>
      <td><input type="radio" name="activity" value="1" checked="checked" /> '.$_language->module['active'].' &nbsp; <input type="radio" name="activity" value="0" /> '.$_language->module['inactive'].'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="id" value="'.$id.'" /></td>
      <td><br /><input type="submit" name="add" value="'.$_language->module['add_to_clan'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="adduser") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
	echo'<h1>&curren; <a href="admincenter.php?site=users" class="white">'.$_language->module['users'].'</a> &raquo; '.$_language->module['add_new_user'].'</h1>';
  
  echo'<form method="post" action="admincenter.php?site=users">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['username'].'</b></td>
      <td width="85%"><input type="text" name="username" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['password'].'</b></td>
      <td><input type="password" name="pass" size="60" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="newuser" value="'.$_language->module['add_new_user'].'" /></td>
    </tr>
  </table>
  </form>';

}

elseif($action=="profile") {
	
  echo'<h1>&curren; <a href="admincenter.php?site=users" class="white">'.$_language->module['users'].'</a> &raquo; '.$_language->module['edit_profile'].'</h1>';
  
  $id = $_GET['id'];
  $ds = mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."user WHERE userID='$id'"));
  
  if($ds['userpic']) $viewpic='<a href="javascript:MM_openBrWindow(\'../images/userpics/'.$ds['userpic'].'\',\'userpic\',\'width=250,height=230\')">'.$_language->module['picture'].'</a>';
  else $viewpic=$_language->module['picture'];
  if($ds['avatar']) $viewavatar='<a href="javascript:MM_openBrWindow(\'../images/avatars/'.$ds['avatar'].'\',\'avatar\',\'width=120,height=120\')">'.$_language->module['avatar'].'</a>';
  else $viewavatar=$_language->module['avatar'];
  $sex = '<option value="m">'.$_language->module['male'].'</option><option value="f">'.$_language->module['female'].'</option><option value="u">'.$_language->module['not_available'].'</option>';
  $sex = str_replace('value="'.$ds['sex'].'"','value="'.$ds['sex'].'" selected="selected"',$sex);
  $countries = str_replace(" selected=\"selected\"", "", $countries);
  $countries = str_replace('value="'.$ds['country'].'"', 'value="'.$ds['country'].'" selected="selected"', $countries);
  $b_day=mb_substr($ds['birthday'],8,2);
  $b_month=mb_substr($ds['birthday'],5,2);
  $b_year=mb_substr($ds['birthday'],0,4);
  
  $CAPCLASS = new Captcha;
  $CAPCLASS->create_transaction();
  $hash = $CAPCLASS->get_hash();
  
  echo'<form method="post" enctype="multipart/form-data" action="admincenter.php?site=users&amp;page='.$_GET['page'].'&amp;type='.$_GET['type'].'&amp;sort='.$_GET['sort'].'">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['user_id'].'</b></td>
      <td width="85%"><b>'.$ds['userID'].'</b></td>
    </tr>
    <tr>
      <td colspan="2"><br /><i><b>'.$_language->module['general'].'</b></i></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['nickname'].'</b></td>
      <td><input type="text" name="nickname" value="'.$ds['nickname'].'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['email'].'</b></td>
      <td><input type="text" name="email" value="'.getinput($ds['email']).'" size="60" /></td>
    </tr>
    <tr>
      <td colspan="2"><br /><i><b>'.$_language->module['pictures'].'</b></i></td>
    </tr>
    <tr>
      <td><b>'.$viewavatar.'</b></td>
      <td><input name="avatar" type="file" size="40" /> <small>'.$_language->module['max_90x90'].'</small></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="checkbox" name="avatar" value="1" /> '.$_language->module['delete_avatar'].'</td>
    </tr>
    <tr>
      <td><b>'.$viewpic.'</b></td>
      <td><input name="userpic" type="file" size="40" /> <small>'.$_language->module['max_230x210'].'</small></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="checkbox" name="userpic" value="1" /> '.$_language->module['delete_picture'].'</td>
    </tr>
    <tr>
      <td colspan="2"><br /><i><b>'.$_language->module['personal'].'</b></i></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['firstname'].'</b></td>
      <td><input type="text" name="firstname" value="'.getinput($ds['firstname']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['lastname'].'</b></td>
      <td><input type="text" name="lastname" value="'.getinput($ds['lastname']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['birthday'].'</b></td>
      <td><input type="text" name="b_day" value="'.getinput($b_day).'" size="2" />
      .
      <input type="text" name="b_month" value="'.getinput($b_month).'" size="2" />
      .
      <input type="text" name="b_year" value="'.getinput($b_year).'" size="4" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['gender'].'</b></td>
      <td><select name="sex">'.$sex.'</select></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['country'].'</b></td>
      <td><select name="flag">'.$countries.'</select></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['town'].'</b></td>
      <td><input type="text" name="town" value="'.getinput($ds['town']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['icq'].'</b></td>
      <td><input type="text" name="icq" value="'.getinput($ds['icq']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['homepage'].'</b></td>
      <td><input type="text" name="homepage" value="'.getinput($ds['homepage']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['signatur'].'</b></td>
      <td><textarea name="usertext" rows="5" cols="" style="width: 60%;">'.getinput($ds['usertext']).'</textarea></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['about_myself'].'</b></td>
      <td><textarea name="about" rows="5" cols="" style="width: 60%;">'.getinput($ds['about']).'</textarea></td>
    </tr>
    <tr>
      <td colspan="2"><br /><i><b>'.$_language->module['various'].'</b></i></td>
    </tr>
    <tr><td><b>'.$_language->module['clantag'].'</b></td>
      <td><input type="text" name="clantag" value="'.getinput($ds['clantag']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['clanname'].'</b></td>
      <td><input type="text" name="clanname" value="'.getinput($ds['clanname']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['clan_homepage'].'</b></td>
      <td><input type="text" name="clanhp" value="'.getinput($ds['clanhp']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['clan_irc'].'</b></td>
      <td><input type="text" name="clanirc" value="'.getinput($ds['clanirc']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['clan_history'].'</b></td>
      <td><input type="text" name="clanhistory" value="'.getinput($ds['clanhistory']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['cpu'].'</b></td>
      <td><input type="text" name="cpu" value="'.getinput($ds['cpu']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['mainboard'].'</b></td>
      <td><input type="text" name="mainboard" value="'.getinput($ds['mainboard']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['ram'].'</b></td>
      <td><input type="text" name="ram" value="'.getinput($ds['ram']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['monitor'].'</b></td>
      <td><input type="text" name="monitor" value="'.getinput($ds['monitor']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['graphiccard'].'</b></td>
      <td><input type="text" name="graphiccard" value="'.getinput($ds['graphiccard']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['soundcard'].'</b></td>
      <td><input type="text" name="soundcard" value="'.getinput($ds['soundcard']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['connection'].'</b></td>
      <td><input type="text" name="connection" value="'.getinput($ds['verbindung']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['keyboard'].'</b></td>
      <td><input type="text" name="keyboard" value="'.getinput($ds['keyboard']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['mouse'].'</b></td>
      <td><input type="text" name="mouse" value="'.getinput($ds['mouse']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['mousepad'].'</b></td>
      <td><input type="text" name="mousepad" value="'.getinput($ds['mousepad']).'" size="60" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="id" value="'.$id.'" /></td>
      <td><br /><input type="submit" name="edit" value="'.$_language->module['edit_profile'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {

  echo'<h1>&curren; '.$_language->module['users'].'</h1>';

	if(isset($_GET['search'])){
		$search = (int)$_GET['search'];
	}
	else{
		$search='';
	}
	if(isset($_GET['page'])){
		$page = (int)$_GET['page'];
	}
	else{
		$page = 1;
	}
	$type="ASC";
	if(isset($_GET['type'])){
	  if(($_GET['type']=='ASC') || ($_GET['type']=='DESC')) $type=$_GET['type'];
	}
	$sort="nickname";
	$status = false;
	if(isset($_GET['sort'])){
	  if(($_GET['sort']=='nickname') || ($_GET['sort']=='registerdate')) $sort="u.".$_GET['sort'];
	  elseif($_GET['sort']=='status'){
	  	$sort = "IF(	(SELECT super FROM ".PREFIX."user_groups WHERE userID=u.userID LIMIT 0,1) = 1,'1', 
	  				IF( 	(SELECT userID FROM ".PREFIX."user_groups WHERE userID=u.userID AND (page='1' OR forum='1' OR user='1' OR news='1' OR clanwars='1' OR feedback='1' OR super='1' OR gallery='1' OR cash='1' OR files='1') LIMIT 0,1) =u.userID,2, 
	  					IF( 	(SELECT userID FROM ".PREFIX."user_groups WHERE userID=u.userID AND moderator='1' LIMIT 0,1) = u.userID, 3, 
	  						IF( 	(SELECT userID FROM ".PREFIX."squads_members WHERE userID=u.userID LIMIT 0,1) = u.userID,4,5 ) 
	  					) 
	  				) 
	  			)";
	  	$status = true;
	  }
	}
	
	if($search!='') $alle = safe_query("SELECT userID FROM ".PREFIX."user WHERE userID=".$search);
	else $alle = safe_query("SELECT userID FROM ".PREFIX."user");
	$gesamt = mysql_num_rows($alle);
	$pages=1;
	
	$max=$maxusers;
	$pages = ceil($gesamt/$max);


	if ($page == "1") {
		if($search) $ergebnis = safe_query("SELECT u.* FROM ".PREFIX."user u WHERE userID='$search' ORDER BY $sort $type LIMIT 0,$max");
		else $ergebnis = safe_query("SELECT u.* FROM ".PREFIX."user u ORDER BY $sort $type LIMIT 0,$max");
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		if($search) $ergebnis = safe_query("SELECT u.* FROM ".PREFIX."user u WHERE userID='$search' ORDER BY $sort $type LIMIT $start,$max");
		else $ergebnis = safe_query("SELECT u.* FROM ".PREFIX."user u ORDER BY $sort $type LIMIT $start,$max");
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}
	$page_link = '';
  	if($pages>1) {
  		if($status == true) $sort = "status";
		$page_link = makepagelink("admincenter.php?site=users&amp;sort=$sort&amp;type=$type&amp;search=$search", $page, $pages);
		$page_link = str_replace('images/', '../images/', $page_link);
	}
	$anz=mysql_num_rows($ergebnis);
	if($anz) {
	 	$CAPCLASS = new Captcha;
		$CAPCLASS->create_transaction();
		$hash = $CAPCLASS->get_hash();
		if(!isset($_GET['sort'])) $_GET['sort'] = '';
		if($status == true) $sort = "status";
		elseif(($_GET['sort']=='nickname') || ($_GET['sort']=='registerdate')) $sort=$_GET['sort'];
		if($type=="ASC")
		$sorter='<a href="admincenter.php?site=users&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=DESC&amp;search='.$search.'">'.$_language->module['to_sort'].':</a> <img src="../images/icons/asc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';
		else
		$sorter='<a href="admincenter.php?site=users&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=ASC&amp;search='.$search.'">'.$_language->module['to_sort'].':</a> <img src="../images/icons/desc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';

		echo'<table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td>'.$sorter.' '.$page_link.'</td>
        <td align="right"><b>'.$_language->module['usersearch'].':</b> &nbsp; <input id="exact" type="checkbox" /> '.$_language->module['exactsearch'].' &nbsp; <input type="text" onkeyup=\'overlay(this, "searchresult");search("user","nickname","userID",encodeURIComponent(this.value),"search_user","searchresult","replace", document.getElementById("exact").checked, "ac_usersearch")\' size="25" /><br />
        <div id="searchresult" style="position:absolute;display:none;border:1px solid black;background-color:#DDDDDD; padding:2px;"></div></td>
      </tr>
      <tr>
        <td colspan="2"><b>'.$gesamt.'</b> '.$_language->module['users_available'].'</td>
      </tr>
    </table>';

		echo'<br />
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="16%" class="title"><a href="admincenter.php?site=users&amp;type='.$type.'&amp;sort=registerdate&amp;page='.$page.'&amp;type='.$type.'&amp;search='.$search.'"><b>'.$_language->module['registered_since'].'</b></a></td>
        <td width="22%" class="title"><a href="admincenter.php?site=users&amp;type='.$type.'&amp;sort=nickname&amp;page='.$page.'&amp;type='.$type.'&amp;search='.$search.'"><b>'.$_language->module['nickname'].'</b></a></td>
        <td width="17%" class="title"><a href="admincenter.php?site=users&amp;type='.$type.'&amp;sort=status&amp;page='.$page.'&amp;type='.$type.'&amp;search='.$search.'"><b>'.$_language->module['status'].'</b></a></td>
        <td width="12%" class="title"><b>'.$_language->module['ban_status'].'</b></td>
        <td width="33%" class="title" colspan="2"><b>'.$_language->module['actions'].'</b></td>
      </tr>';

		$n=1;
		$i=1;
		while($ds=mysql_fetch_array($ergebnis)) {
      if($i%2) { $td='td1'; }
      else { $td='td2'; }
		
		$id=$ds['userID'];
		$registered=date("d.m.Y - H:i", $ds['registerdate']);
		$nickname_c=getnickname($ds['userID']);
		$replaced_search=str_replace("%", "", $search);
		$nickname=str_replace($replaced_search, '<b>'.$replaced_search.'</b>', $nickname_c);
		
		if(issuperadmin($ds['userID']) && isclanmember($ds['userID'])) $status=$_language->module['superadmin'].'<br />&amp; '.$_language->module['clanmember'];
		elseif(issuperadmin($ds['userID'])) $status=$_language->module['superadmin'];
		elseif(isanyadmin($ds['userID']) && isclanmember($ds['userID'])) $status=$_language->module['admin'].'<br />&amp; '.$_language->module['clanmember'];
		elseif(isanyadmin($ds['userID'])) $status=$_language->module['admin'];
		elseif(isanymoderator($ds['userID']) && isclanmember($ds['userID'])) $status=$_language->module['moderator'].'<br />&amp; '.$_language->module['clanmember'];
		elseif(isanymoderator($ds['userID'])) $status=$_language->module['moderator'];
		elseif(isclanmember($ds['userID'])) $status=$_language->module['clanmember'];
		else $status=$_language->module['user'];
		
		if(isbanned($ds['userID'])) $banned='<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=users&amp;action=ban&amp;id='.$ds['userID'].'\');return document.MM_returnValue" value="'.$_language->module['undo_ban'].'" />';
		else $banned='<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=users&amp;action=ban&amp;id='.$ds['userID'].'\');return document.MM_returnValue" value="'.$_language->module['banish'].'" />';
		
		if($ds['activated']=="1") $actions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=users&amp;page='.$page.'&amp;type='.$type.'&amp;sort='.$sort.'&amp;search='.$search.'&amp;action=addtoclan&amp;id='.$ds['userID'].'\');return document.MM_returnValue" value="'.$_language->module['to_clan'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=members&amp;action=edit&amp;id='.$ds['userID'].'\');return document.MM_returnValue" value="'.$_language->module['rights'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=users&amp;action=profile&amp;page='.$page.'&amp;type='.$type.'&amp;sort='.$sort.'&amp;search='.$search.'&amp;id='.$ds['userID'].'\');return document.MM_returnValue" value="'.$_language->module['profile'].'" />';
		else $actions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=users&amp;action=activate&amp;id='.$ds['userID'].'&amp;captcha_hash='.$hash.'\');return document.MM_returnValue" value="'.$_language->module['activate'].'" />';
		
		echo'<tr>
        <td class="'.$td.'">'.$registered.'</td>
        <td class="'.$td.'"><a href="../index.php?site=profile&amp;id='.$id.'" target="_blank">'.strip_tags(stripslashes($nickname)).'</a></td>
        <td class="'.$td.'" align="center"><small>'.$status.'</small></td>
        <td class="'.$td.'" align="center">'.$banned.'</td>
        <td class="'.$td.'" align="center">'.$actions.'</td>
        <td class="'.$td.'" align="center" width="6%"><input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=users&amp;page='.$page.'&amp;type='.$type.'&amp;sort='.$sort.'&amp;search='.$search.'&amp;delete=true&amp;id='.$ds['userID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['del'].'" /></td>
			</tr>';
      
      $i++;
		}
		echo'</table>
    <br /><br />&raquo; <a href="admincenter.php?site=users&amp;action=adduser"><b>'.$_language->module['add_new_user'].'</b></a>';
	}
	else echo $_language->module['no_users'];
}
?>