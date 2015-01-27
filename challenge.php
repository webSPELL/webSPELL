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

if(isset($site)) $_language->read_module('challenge');

eval ("\$title_challenge = \"".gettemplate("title_challenge")."\";");
echo $title_challenge;

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = "";

$show = true;
if($action=="save" && isset($_POST['post'])) {

	$opponent = $_POST['opponent'];
	$opphp = $_POST['opphp'];
	$oppcountry = $_POST['oppcountry'];
	if(isset($_POST['squad'])) $squad = $_POST['squad'];
	else $squad = "";
	$league = $_POST['league'];
	$map = $_POST['map'];
	$server = $_POST['server'];
	$email = $_POST['email'];
	$info = $_POST['info'];
	$hour = (int)$_POST['hour'];
	$minute = (int)$_POST['minute'];
	$month = (int)$_POST['month'];
	$day = (int)$_POST['day'];
	$year = (int)$_POST['year'];
	$run=0;
	
  	$error = array();
  	if(!(mb_strlen(trim($opponent)))) $error[]=$_language->module['enter_clanname'];
	if(!validate_url($opphp)) $error[]=$_language->module['enter_url'];
	if(!validate_email($email)) $error[]=$_language->module['enter_email'];
	if(!(mb_strlen(trim($league)))) $error[]=$_language->module['enter_league'];
	if(!(mb_strlen(trim($map)))) $error[]=$_language->module['enter_map'];
	if(!(mb_strlen(trim($server)))) $error[]=$_language->module['enter_server'];
  
  	if($userID) {
		$run=1;
	}
	else {
		$CAPCLASS = new Captcha;
		if(!$CAPCLASS->check_captcha($_POST['captcha'], $_POST['captcha_hash'])) $error[]=$_language->module['wrong_security_code'];
		else $run=1;
	}
  
  	if(!count($error) and $run) {
		$date=time();
		$touser = array();
		$cwdate=mktime($hour,$minute,0,$month,$day,$year);
		safe_query("INSERT INTO ".PREFIX."challenge (date, cwdate, squadID, opponent, opphp, oppcountry, league, map, server, email, info) values('$date', '$cwdate', '$squad', '$opponent', '$opphp', '$oppcountry', '$league', '$map', '$server', '$email', '$info')");
		$ergebnis=safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE warmember='1' AND squadID='".$squad."'");
		while($ds=mysql_fetch_array($ergebnis)) {
			$touser[]=$ds['userID'];
		}

		if($touser[0] != "") {
			$date = time();
			$tmp_lang = new Language();
			foreach($touser as $id) {
				$tmp_lang->set_language(getuserlanguage($id));
				$tmp_lang->read_module('challenge');
				$message = $tmp_lang->module['challenge_message'];
				sendmessage($id,$tmp_lang->module['message_title'],$message);
			}
		}
		echo $_language->module['thank_you'];
		unset($_POST['opponent'],$_POST['opphp'],$_POST['league'],$_POST['map'],$_POST['server'],$_POST['info'],$_POST['email']);
		$show = false;
  	}
	else {
		$show = true;
		$fehler=implode('<br />&#8226; ',$error);
    
    	$showerror = '<div class="errorbox">
      <b>'.$_language->module['problems'].':</b><br /><br />
      &#8226; '.$fehler.'
    </div>';
	}
}
elseif($action=="delete") {
	$chID = $_GET['chID'];
	if(isclanwaradmin($userID)) {
		safe_query("DELETE FROM ".PREFIX."challenge WHERE chID='$chID'");
		redirect('index.php?site=challenge', $_language->module['entry_deleted'],3);
	}
	else redirect('index.php?site=challenge', $_language->module['no_access'],3);
}
	$type = (isset($_GET['type']) && $_GET['type']=='ASC') ? "ASC" : "DESC";

  	if($show == true){
		$day = '';
	  	for($i=1; $i<32; $i++) {
			if($i==date("d", time())) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
	  	$month = '';
		for($i=1; $i<13; $i++) {
			if($i==date("n", time())) $month.='<option value="'.$i.'" selected="selected">'.date("M", time()).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
	  	$year = '';
		for($i=date('Y'); $i<=date('Y', strtotime('+5 year')); $i++) {
			if($i==date("Y", time())) $year.='<option value="'.$i.'" selected="selected">'.date("Y", time()).'</option>';
			else $year.='<option value="'.$i.'">'.$i.'</option>';
		}
		
		
	  	$squads = getgamesquads();
	  
	  	$bg1 = BG_1;
	  	
	  	if(!isset($showerror)) $showerror='';
	    if(isset($_POST['opponent'])) $opponent = getforminput($_POST['opponent']);
	    else $opponent='';
	    if(isset($_POST['opphp'])) $opphp = getforminput($_POST['opphp']);
	    else $opphp='http://';
	    if(isset($_POST['league'])) $league = getforminput($_POST['league']);
	    else $league='';
	    if(isset($_POST['map'])) $map = getforminput($_POST['map']);
	    else $map='';
	    if(isset($_POST['server'])) $server = getforminput($_POST['server']);
	    else $server='';
	    if(isset($_POST['info'])) $info = getforminput($_POST['info']);
	    else $info='';
	    
	    
		if($loggedin) {
			$email = getemail($userID);
			eval ("\$challenge_loggedin = \"".gettemplate("challenge_loggedin")."\";");
			echo $challenge_loggedin;
		}
		else {
			$CAPCLASS = new Captcha;
			$captcha = $CAPCLASS->create_captcha();
			$hash = $CAPCLASS->get_hash();
			$CAPCLASS->clear_oldcaptcha();
			if(isset($_POST['email'])) $email = getforminput($_POST['email']);
			else $email = "";
			eval ("\$challenge_notloggedin = \"".gettemplate("challenge_notloggedin")."\";");
			echo $challenge_notloggedin;
	  	}
  	}
  
if(isclanwaradmin($userID)) {
  $ergebnis = safe_query("SELECT * FROM ".PREFIX."challenge ORDER BY date $type");
	$anz=mysql_num_rows($ergebnis);
	if($anz) {
		if(!isset($type)) $type = "DESC";

		if($type=="ASC") echo'<a href="index.php?site=challenge&amp;type=DESC">'.$_language->module['sort'].'</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';
		else echo'<a href="index.php?site=challenge&amp;type=ASC">'.$_language->module['sort'].'</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';
		echo'<br /><br />';
		
		$i=0;
		while ($ds = mysql_fetch_array($ergebnis)) {
			$bg1 = ($i%2)? BG_1: BG_1;
			$date = date("d.m.Y", $ds['date']);
			$cwdate = date("d.m.Y - H:i", $ds['cwdate']);
			$squad= getsquadname($ds['squadID']);
			$oppcountry="[flag]".$ds['oppcountry']."[/flag]";
			$country=flags($oppcountry);
			$opponent='<a href="'.$ds['opphp'].'" target="_blank">'.clearfromtags($ds['opponent']).'</a>';
			$league=clearfromtags($ds['league']);
			$map=clearfromtags($ds['map']);
			$server=clearfromtags($ds['server']);
			$info=cleartext($ds['info']);
			$email = '<a href="mailto:'.mail_protect(cleartext($ds['email'])).'">'.$ds['email'].'</a>';
			
			if(isset($ds['hp']))
     		if(!validate_url($ds['hp'])) $homepage='';
			else $homepage='<a href="'.$ds['hp'].'" target="_blank"><img src="images/icons/hp.gif" border="0" width="14" height="14" alt="homepage" /></a>';
			
			if(isset($ds['name'])) $name=cleartext($ds['name']);
      		if(isset($ds['comment'])) $message=cleartext($ds['comment']);
			
			$actions='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=addwar&amp;chID='.$ds['chID'].'\');return document.MM_returnValue" value="'.$_language->module['insert_in_calendar'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=challenge&amp;action=delete&amp;chID='.$ds['chID'].'\');return document.MM_returnValue" value="'.$_language->module['delete_challenge'].'" />';

			eval ("\$challenges = \"".gettemplate("challenges")."\";");
			echo $challenges;
			$i++;
		}
		echo'<br />';
	}
	else echo $_language->module['no_entries'];
}
?>