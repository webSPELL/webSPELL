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

$_language->read_module('myprofile');

if(!$userID) echo $_language->module['not_logged_in'];

else {

	$showerror = '';
	eval ("\$title_myprofile = \"".gettemplate("title_myprofile")."\";");
	echo $title_myprofile;

	if(isset($_POST['submit'])) {
		$nickname = htmlspecialchars(mb_substr(trim($_POST['nickname']), 0, 30));
		if(isset($_POST['mail'])) $mail = $_POST['mail'];
    	else $mail="";
		if(isset($_POST['mail_hide'])) $mail_hide = true;
		else $mail_hide = false;
		$usernamenew = mb_substr(trim($_POST['usernamenew']), 0, 30);
		$usertext = $_POST['usertext'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$b_day = $_POST['b_day'];
		$b_month = $_POST['b_month'];
		$b_year = $_POST['b_year'];
		$sex = $_POST['sex'];
		$flag = preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['flag']);
		$town = $_POST['town'];
		$icq = $_POST['icq'];
		$icq = str_replace('-','',$icq); // Replace - 
		$about = $_POST['messageabout'];
		$clantag = $_POST['clantag'];
		$clanname = $_POST['clanname'];
		$clanhp = $_POST['clanhp'];
		$clanirc = $_POST['clanirc'];
		$clanhistory = $_POST['clanhistory'];
		$cpu = $_POST['cpu'];
		$mainboard = $_POST['mainboard'];
		$monitor = $_POST['monitor'];
		$ram = $_POST['ram'];
		$graphiccard = $_POST['graphiccard'];
		$soundcard = $_POST['soundcard'];
		$connection = $_POST['connection'];
		$keyboard = $_POST['keyboard'];
		$mouse = $_POST['mouse'];
		$mousepad = $_POST['mousepad'];
		$newsletter = $_POST['newsletter'];
		$homepage = str_replace('http://', '', $_POST['homepage']);
		$pm_mail = $_POST['pm_mail'];
		$avatar = $_FILES['avatar'];
		$userpic = $_FILES['userpic'];
		$language = $_POST['language'];
		$date_format = $_POST['date_format'];
		$time_format = $_POST['time_format'];
		$user_gbook = $_POST['user_guestbook'];
		$id = $userID;
		
		$error_array = array();
		
		if(isset($_POST['userID']) or isset($_GET['userID']) or $userID=="") die($_language->module['not_logged_in']);

		if(isset($_POST['delavatar'])) {
			$filepath = "./images/avatars/";
			if(file_exists($filepath.$id.'.gif')) @unlink($filepath.$id.'.gif');
			if(file_exists($filepath.$id.'.jpg')) @unlink($filepath.$id.'.jpg');
			if(file_exists($filepath.$id.'.png')) @unlink($filepath.$id.'.png');
			safe_query("UPDATE ".PREFIX."user SET avatar='' WHERE userID='".$id."'");
		}
		if(isset($_POST['deluserpic'])) {
			$filepath = "./images/userpics/";
			if(file_exists($filepath.$id.'.gif')) @unlink($filepath.$id.'.gif');
			if(file_exists($filepath.$id.'.jpg')) @unlink($filepath.$id.'.jpg');
			if(file_exists($filepath.$id.'.png')) @unlink($filepath.$id.'.png');
			safe_query("UPDATE ".PREFIX."user SET userpic='' WHERE userID='".$id."'");
		}

		//avatar
		$filepath = "./images/avatars/";
		if($avatar['name'] != "" or ($_POST['avatar_url'] != "" and $_POST['avatar_url'] != "http://")) {
			if($avatar['name'] != "") {
				move_uploaded_file($avatar['tmp_name'], $filepath.$avatar['name'].".tmp");
			}
			else {
				$avatar['name'] = strrchr($_POST['avatar_url'],"/");
				if(!copy($_POST['avatar_url'],$filepath.$avatar['name'].".tmp")) {
					$error_array['can_not_copy'] = $_language->module['can_not_copy'];
				}
			}
			if(!array_key_exists('can_not_copy', $error_array))
			{
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
							$error_array[] = $_language->module['invalid_picture-format'];
						}
						else {
							$error_array[] = $_language->module['upload_failed'];
						}
					}
				}
				else {
					@unlink($filepath.$avatar['name'].".tmp");
					$error_array[] = $_language->module['picture_too_big_avatar'];
				}
			}
		}

		//userpic
		$filepath = "./images/userpics/";
		if($userpic['name'] != "" or ($_POST['userpic_url'] != "" and $_POST['userpic_url'] != "http://")) {
			if($userpic['name'] != "") {
				move_uploaded_file($userpic['tmp_name'], $filepath.$userpic['name'].".tmp");
			} else {
				$userpic['name'] = strrchr($_POST['userpic_url'],"/");
				if(!copy($_POST['userpic_url'],$filepath.$userpic['name'].".tmp")) {
					$error_array['can_not_copy'] = $_language->module['can_not_copy'];
				}
			}
			if(!array_key_exists('can_not_copy', $error_array))
			{
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
							$error_array[] = $_language->module['invalid_picture-format'];
						}
						else {
							$error_array[] = $_language->module['upload_failed'];
						}
					}
				}
				else {
					@unlink($filepath.$userpic['name'].".tmp");
					$error_array[] = $_language->module['picture_too_big_userpic'];
				}
			}
		}

		$birthday = $b_year.'-'.$b_month.'-'.$b_day;
		
		
		if(empty($usernamenew)){
			$error_array[] = $_language->module['you_have_to_username'];
		}
		if(empty($nickname)){
			$error_array[] = $_language->module['you_have_to_nickname'];
		}

		$qry = "SELECT userID FROM ".PREFIX."user WHERE username = '".$usernamenew."' AND userID != ".$userID." LIMIT 0,1";
		if(mysqli_num_rows(safe_query($qry))) {
			$error_array[] = $_language->module['username_aleady_in_use'];
		}
		
		$qry = "SELECT userID FROM ".PREFIX."user WHERE nickname = '".$nickname."' AND userID!=".$userID." LIMIT 0,1";
		if(mysqli_num_rows(safe_query($qry))) {
				$error_array[] = $_language->module['nickname_already_in_use'];
		}

		if(count($error_array)) 
		{
			$fehler=implode('<br />&#8226; ', $error_array);
			$showerror = '<div class="errorbox">
			  <b>'.$_language->module['errors_there'].':</b><br /><br />
			  &#8226; '.$fehler.'
			</div>';
		}
		else
		{
			safe_query("UPDATE `".PREFIX."user`
						SET 
							nickname='".$nickname."',
							username='".$usernamenew."',
							email_hide='".$mail_hide."',
							firstname='".$firstname."',
							lastname='".$lastname."',
							sex='".$sex."',
							country='".$flag."',
							town='".$town."',
							birthday='".$birthday."',
							icq='".$icq."',
							usertext='".$usertext."',
							clantag='".$clantag."',
							clanname='".$clanname."',
							clanhp='".$clanhp."',
							clanirc='".$clanirc."',
							clanhistory='".$clanhistory."',
							cpu='".$cpu."',
							mainboard='".$mainboard."',
							ram='".$ram."',
							monitor='".$monitor."',
							graphiccard='".$graphiccard."',
							soundcard='".$soundcard."',
							verbindung='".$connection."',
							keyboard='".$keyboard."',
							mouse='".$mouse."',
							mousepad='".$mousepad."',
							mailonpm='".$pm_mail."',
							newsletter='".$newsletter."',
							homepage='".$homepage."',
							about='".$about."',
							date_format='".$date_format."',
							time_format='".$time_format."',
							language='".$language."',
							user_guestbook='".$user_gbook."'
						WHERE 
							userID='".$id."'");
	
			redirect("index.php?site=profile&amp;id=$id", $_language->module['profile_updated'],3);
		}
  }

	if(isset($_GET['action']) AND $_GET['action']=="editpwd") {
	
		$bg1 = BG_1;
		$bg2 = BG_2;
	  	$bg3 = BG_3;
		$bg4 = BG_4;
		$border = BORDER;
	
		eval("\$myprofile_editpwd = \"".gettemplate("myprofile_editpwd")."\";");
		echo $myprofile_editpwd;

	}	
	
	elseif(isset($_POST['savepwd'])) {

		$oldpwd = $_POST['oldpwd'];
		$pwd1 = $_POST['pwd1'];
		$pwd2 = $_POST['pwd2'];
		$id = $userID;

		$ergebnis = safe_query("SELECT password FROM ".PREFIX."user WHERE userID='".$id."'");
		$ds = mysqli_fetch_array($ergebnis);

		if(!(mb_strlen(trim($oldpwd)))) {
			$error = $_language->module['forgot_old_pw'];
			die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
		}
		$oldmd5pwd = md5($oldpwd);
		if($oldmd5pwd != $ds['password']) {
			$error = $_language->module['old_pw_not_valid'];
			die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
		}
		if($pwd1 == $pwd2) {
			if(!(mb_strlen(trim($pwd1)))) {
				$error = $_language->module['forgot_new_pw'];
				die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
			}
		}
		else {
			$error = $_language->module['repeated_pw_not_valid'];
			die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
		}
		$newmd5pwd = md5(stripslashes($pwd1));
		safe_query("UPDATE ".PREFIX."user SET password='".$newmd5pwd."' WHERE userID='".$userID."'");

		//logout
		unset($_SESSION['ws_auth']);
		unset($_SESSION['ws_lastlogin']);
		session_destroy();

    redirect('index.php?site=login', $_language->module['pw_changed'],3);

	}	
	
	elseif(isset($_GET['action']) AND $_GET['action']=="editmail") {

		$bg1 = BG_1;
		$bg2 = BG_2;
    	$bg3 = BG_3;
		$bg4 = BG_4;
		$border = BORDER;

		eval("\$myprofile_editmail = \"".gettemplate("myprofile_editmail")."\";");
		echo $myprofile_editmail;

	}	
	
	elseif(isset($_POST['savemail'])){

		$activationkey = md5(RandPass(20));
		$activationlink = 'http://'.$hp_url.'/index.php?site=register&mailkey='.$activationkey;
		$pwd = $_POST['oldpwd'];
		$mail1 = $_POST['mail1'];
		$mail2 = $_POST['mail2'];

		$ergebnis = safe_query("SELECT password, username FROM ".PREFIX."user WHERE userID='".$userID."'");
		$ds = mysqli_fetch_array($ergebnis);
		$username = $ds['username'];
		if(!(mb_strlen(trim($pwd)))) {
			$error = $_language->module['forgot_old_pw'];
			die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
		}
		$md5pwd = md5(stripslashes($pwd));
		if($md5pwd != $ds['password']) {
			die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
		}
		if($mail1 == $mail2) {
			if(!(mb_strlen(trim($mail1)))) {
				$error = $_language->module['mail_not_valid'];
				die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
			}
		}
		else {
			$error = $_language->module['repeated_pw_not_valid'];
			die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
		}

		// check e-mail
		
		if(!validate_email($mail1)){ 
			$error=$_language->module['invalid_mail'];
			die('<b>ERROR: '.$error.'</b><br /><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />');
		}
		
		safe_query("UPDATE ".PREFIX."user SET email_change = '".$mail1."', email_activate = '".$activationkey."' WHERE userID='".$userID."'");

		$ToEmail = $mail1;
		$ToName = $username;
		$header =  str_replace(Array('%homepage_url%'), Array($hp_url), $_language->module['mail_subject']);
		$Message = str_replace(Array('%username%', '%activationlink%', '%pagetitle%', '%homepage_url%'), Array($username, $activationlink, $hp_title, $hp_url), $_language->module['mail_text']);

		if(mail($ToEmail,$header, $Message, "From:".$admin_email."\nContent-type: text/plain; charset=utf-8\n")) echo $_language->module['mail_changed'];
		else echo $_language->module['mail_failed'];

	}	
	
	else {
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."user WHERE userID='".$userID."'");
		$anz = mysqli_num_rows($ergebnis);
		if($anz) {
			$ds = mysqli_fetch_array($ergebnis);
			$flag = '[flag]'.$ds['country'].'[/flag]';
			$country = flags($flag);
			$country = str_replace("<img","<img id='county'",$country);
			$sex = '<option value="m">'.$_language->module['male'].'</option><option value="f">'.$_language->module['female'].'</option><option value="u">'.$_language->module['unknown'].'</option>';
			$sex = str_replace('value="'.$ds['sex'].'"','value="'.$ds['sex'].'" selected="selected"',$sex);
			if($ds['newsletter'] == "1") $newsletter = '<option value="1" selected="selected">'.$_language->module['yes'].'</option><option value="0">'.$_language->module['no'].'</option>';
			else $newsletter = '<option value="1">'.$_language->module['yes'].'</option><option value="0" selected="selected">'.$_language->module['no'].'</option>';
			if($ds['mailonpm'] == "1") $pm_mail = '<option value="1" selected="selected">'.$_language->module['yes'].'</option><option value="0">'.$_language->module['no'].'</option>';
			else $pm_mail = '<option value="1">'.$_language->module['yes'].'</option><option value="0" selected="selected">'.$_language->module['no'].'</option>';
			if($ds['email_hide']) $email_hide = ' checked="checked"';
			else $email_hide = '';
			$format_date = "<option value='d.m.y'>DD.MM.YY</option>
							<option value='d.m.Y'>DD.MM.YYYY</option>
							<option value='j.n.y'>D.M.YY</option>
							<option value='j.n.Y'>D.M.YYYY</option>
							<option value='y-m-d'>YY-MM-DD</option>
							<option value='Y-m-d'>YYYY-MM-DD</option>
							<option value='y/m/d'>YY/MM/DD</option>
							<option value='Y/m/d'>YYYY/MM/DD</option>";
			$format_date = str_replace("value='".$ds['date_format']."'","value='".$ds['date_format']."' selected='selected'",$format_date);
	
			$format_time = "<option value='G:i'>H:MM</option>
							<option value='H:i'>HH:MM</option>
							<option value='G:i a'>H:MM am/pm</option>
							<option value='H:i a'>HH:MM am/pm</option>
							<option value='G:i A'>H:MM AM/PM</option>
							<option value='H:i A'>HH:MM AM/PM</option>
							<option value='G:i:s'>H:MM:SS</option>
							<option value='H:i:s'>HH:MM:SS</option>
							<option value='G:i:s a'>H:MM:SS am/pm</option>
							<option value='H:i:s a'>HH:MM:SS am/pm</option>
							<option value='G:i:s A'>H:MM:SS AM/PM</option>
							<option value='H:i:s A'>HH:MM:SS AM/PM</option>";
			$format_time = str_replace("value='".$ds['time_format']."'","value='".$ds['time_format']."' selected='selected'",$format_time);
			$user_gbook = "<option value='0'>".$_language->module['deactivated']."</option><option value='1'>".$_language->module['activated']."</option>";
			$user_gbook = str_replace("value='".$ds['user_guestbook']."'","value='".$ds['user_guestbook']."' selected='selected'",$user_gbook);
			$user_gbook_select = '';
			if($user_guestbook) {
				$user_gbook_select = '<tr bgcolor="'.BG_2.'">
										<td align="right" bgcolor="'.BG_1.'">'.$_language->module['guestbook'].'</td>
										<td bgcolor="'.BG_2.'"><select name="user_guestbook">'.$user_gbook.'</select></td>
									  </tr>';
			}
			$b_day = mb_substr($ds['birthday'],8,2);
			$b_month = mb_substr($ds['birthday'],5,2);
			$b_year = mb_substr($ds['birthday'],0,4);
			$countries=getcountries();
			$countries = str_replace('value="'.$ds['country'].'"', 'value="'.$ds['country'].'" selected="selected"', $countries);
			if($ds['avatar']) $viewavatar = '&#8226; <a href="javascript:MM_openBrWindow(\'images/avatars/'.$ds['avatar'].'\',\'avatar\',\'width=120,height=120\')">'.$_language->module['avatar'].'</a>';
			else $viewavatar = $_language->module['avatar'];
			if($ds['userpic']) $viewpic = '&#8226; <a href="javascript:MM_openBrWindow(\'images/userpics/'.$ds['userpic'].'\',\'userpic\',\'width=250,height=230\')">'.$_language->module['userpic'].'</a>';
			else $viewpic = $_language->module['userpic'];

			$usertext = getinput($ds['usertext']);
			$clanhistory = clearfromtags($ds['clanhistory']);
			$clanname = clearfromtags($ds['clanname']);
			$clantag = clearfromtags($ds['clantag']);
			$clanirc = clearfromtags($ds['clanirc']);
			$firstname = clearfromtags($ds['firstname']);
			$lastname = clearfromtags($ds['lastname']);
			$town = clearfromtags($ds['town']);
			$cpu = clearfromtags($ds['cpu']);
			$mainboard = clearfromtags($ds['mainboard']);
			$ram = clearfromtags($ds['ram']);
			$monitor = clearfromtags($ds['monitor']);
			$graphiccard = clearfromtags($ds['graphiccard']);
			$soundcard = clearfromtags($ds['soundcard']);
			$connection = clearfromtags($ds['verbindung']);
			$keyboard = clearfromtags($ds['keyboard']);
			$mouse = clearfromtags($ds['mouse']);
			$mousepad = clearfromtags($ds['mousepad']);
			$clanhp = getinput($ds['clanhp']);
			$about = getinput($ds['about']);
			$nickname = $ds['nickname'];
			$username = getinput($ds['username']);
			$email = getinput($ds['email']);
			$icq = getinput($ds['icq']);
			$homepage = getinput($ds['homepage']);

			// Select all possible languages
			$langdirs = '';
			$filepath = "./languages/";

			$mysql_langs = array();
			$query = safe_query("SELECT lang, language FROM ".PREFIX."news_languages");
			while($sql_lang = mysqli_fetch_assoc($query)){
				$mysql_langs[$sql_lang['lang']] = $sql_lang['language'];
			}
			$langs = array();
			if($dh = opendir($filepath)) {
				while($file = mb_substr(readdir($dh), 0, 2)) {
					if($file != "." and $file!=".." and is_dir($filepath.$file)) {
						if(isset($mysql_langs[$file])){
							$name = $mysql_langs[$file];
							$name = ucfirst($name);
							$langs[$name] = $file;
						}
						else{
							$langs[$file] = $file;
						}
					}
				}
				closedir($dh);
			}
			ksort($langs,SORT_NATURAL);
			foreach($langs as $lang=>$flag){
				$langdirs .= '<option value="'.$flag.'">'.$lang.'</option>';
			}

			if($ds['language']) $langdirs = str_replace('"'.$ds['language'].'"', '"'.$ds['language'].'" selected="selected"', $langdirs);
			else $langdirs = str_replace('"'.$_language->language.'"', '"'.$_language->language.'" selected="selected"', $langdirs);

			
			$bg1 = BG_1;
			$bg2 = BG_2;
			$bg3 = BG_3;
			$bg4 = BG_4;

			eval("\$myprofile = \"".gettemplate("myprofile")."\";");
			echo $myprofile;

		}
		else echo $_language->module['not_logged_in'];
	}
}
?>