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

//options

$showonlygamingsquads = true;  //only show gaming squads (=true) or show all squads (=false)?

//php below this line ;)

if(isset($site)) $_language->read_module('joinus');

eval ("\$title_joinus = \"".gettemplate("title_joinus")."\";");
echo $title_joinus;

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = "";
$show = true;
if($action=="save" && isset($_POST['post'])) {

	if(isset($_POST['squad'])) $squad = $_POST['squad'];
	else $squad = 0;
	$nick = $_POST['nick'];
	$name = $_POST['name'];
	$email = $_POST['email'];
	$messenger = $_POST['messenger'];
	$age = $_POST['age'];
	$city = $_POST['city'];
	$clanhistory = $_POST['clanhistory'];
	$info = $_POST['info'];
	$run=0;
  
  	$error = array();
	if(!(mb_strlen(trim($nick)))) $error[]=$_language->module['forgot_nickname'];
	if(!(mb_strlen(trim($name)))) $error[]=$_language->module['forgot_realname'];
	if(! validate_email($email)) $error[]=$_language->module['email_not_valid'];
  	if(!(mb_strlen(trim($messenger)))) $error[]=$_language->module['forgot_messenger'];
  	if(!(mb_strlen(trim($age)))) $error[]=$_language->module['forgot_age'];
	if(!(mb_strlen(trim($city)))) $error[]=$_language->module['forgot_city'];
	if(!(mb_strlen(trim($clanhistory)))) $error[]=$_language->module['forgot_history'];
  
  if($userID) {
		$run=1;
	}
	else {
		$CAPCLASS = new Captcha;
		if(!$CAPCLASS->check_captcha($_POST['captcha'], $_POST['captcha_hash'])) $error[]=$_language->module['wrong_security_code'];
		else $run=1;
	}

	if(!count($error) and $run) {
		$ergebnis=safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE joinmember='1' AND squadID='".$squad."'");
		while($ds=mysql_fetch_array($ergebnis)) {
			$touser[]=$ds['userID'];
		}

		if($touser[0] != "") {
			$tmp_lang = new Language();
			foreach($touser as $id) {
				$tmp_lang->set_language(getuserlanguage($id));
				$tmp_lang->read_module('joinus');
				$message = '[b]'.$tmp_lang->module['someone_want_to_join_your_squad'].' '.mysql_real_escape_string(getsquadname($squad)).'![/b]
				 '.$tmp_lang->module['nick'].' '.$nick.'
				 '.$tmp_lang->module['name'].': '.$name.'
				 '.$tmp_lang->module['age'].': '.$age.'
				 '.$tmp_lang->module['mail'].': [email]'.$email.'[/email]
				 '.$tmp_lang->module['messenger'].': '.$messenger.'
				 '.$tmp_lang->module['city'].': '.$city.'
				 '.$tmp_lang->module['clan_history'].': '.$clanhistory.'
		
				 '.$tmp_lang->module['info'].':
				 '.$info.'
				 ';
				sendmessage($id,$tmp_lang->module['message_title'],$message);
			}
		}
		echo $_language->module['thanks_you_will_get_mail'];
		unset($_POST['nick'], $_POST['name'], $_POST['email'],$_POST['messenger'],$_POST['age'],$_POST['city'],$_POST['clanhistory'],$_POST['info']);
		$show = false;
	}
	else {
		$fehler=implode('<br />&#8226; ',$error);
		$show = true;
    $showerror = '<div class="errorbox">
      <b>'.$_language->module['problems'].':</b><br /><br />
      &#8226; '.$fehler.'
    </div>';
	}
}
if($show == true){
	if($showonlygamingsquads) $squads=getgamesquads();
	else $squads=getsquads();
	
  	$bg1 = BG_1;

	if($loggedin) {
		if(!isset($showerror)) $showerror='';
		$res = safe_query("SELECT *, DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW()) - TO_DAYS(birthday)), '%y') 'age' FROM ".PREFIX."user WHERE userID = '$userID'");
    	$ds = mysql_fetch_assoc($res);
		$nickname = getinput($ds['nickname']);
		$name = getinput($ds['firstname']." ".$ds['lastname']);
		$email = getinput($ds['email']);
		$messenger = getinput($ds['icq']);
		$age = $ds['age'];
		$city = getinput($ds['town']);
		
	    if(isset($_POST['clanhistory'])) $clanhistory=getforminput($_POST['clanhistory']);
	    else $clanhistory='';
	    if(isset($_POST['info'])) $info=getforminput($_POST['info']);
	    else $info='';

		eval ("\$joinus_loggedin = \"".gettemplate("joinus_loggedin")."\";");
		echo $joinus_loggedin;
	}
	else {
		$CAPCLASS = new Captcha;
		$captcha = $CAPCLASS->create_captcha();
		$hash = $CAPCLASS->get_hash();
		$CAPCLASS->clear_oldcaptcha();
		
	    if(!isset($showerror)) $showerror='';
	    if(isset($_POST['nick'])) $nick= getforminput($_POST['nick']);
	    else $nick='';
	    if(isset($_POST['name'])) $name= getforminput($_POST['name']);
	    else $name='';
	    if(isset($_POST['email'])) $email= getforminput($_POST['email']);
	    else $email='';
	    if(isset($_POST['messenger'])) $messenger= getforminput($_POST['messenger']);
	    else $messenger='';
	    if(isset($_POST['age'])) $age= getforminput($_POST['age']);
	    else $age='';
	    if(isset($_POST['city'])) $city= getforminput($_POST['city']);
	    else $city='';
	    if(isset($_POST['clanhistory'])) $clanhistory= getforminput($_POST['clanhistory']);
	    else $clanhistory='';
	    if(isset($_POST['info'])) $info= getforminput($_POST['info']);
	    else $info='';

		eval ("\$joinus_notloggedin = \"".gettemplate("joinus_notloggedin")."\";");
		echo $joinus_notloggedin;
	}
}
?>