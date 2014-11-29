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

if(isset($site)) $_language->readModule('contact');

eval ("\$title_contact = \"".gettemplate("title_contact")."\";");
echo $title_contact;

if(isset($_POST["action"])) $action=$_POST["action"];
else $action='';

if($action == "send") {
	$getemail = $_POST['getemail'];
	$subject = $_POST['subject'];
	$text = $_POST['text'];
	$text=str_replace('\r\n', "\n", $text);
	$name = $_POST['name'];
	$from = $_POST['from'];
	$run=0;

	$fehler = array();
	if(!(mb_strlen(trim($name)))) $fehler[] = $_language->module['enter_name'];

	if(!validate_email($from)) $fehler[] = $_language->module['enter_mail'];
	if(!(mb_strlen(trim($subject)))) $fehler[] = $_language->module['enter_subject'];
	if(!(mb_strlen(trim($text)))) $fehler[] = $_language->module['enter_message'];

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."contact WHERE email='".$getemail."'");
	if(mysqli_num_rows($ergebnis) == 0){
		$fehler[] = $_language->module['unknown_receiver'];
	}

	if($userID) {
		$run=1;
	} else {
		$CAPCLASS = new \webspell\Captcha;
		if(!$CAPCLASS->check_captcha($_POST['captcha'], $_POST['captcha_hash'])) $fehler[] = $_language->module['wrong_securitycode'];
		else $run=1;
	}

	if(!count($fehler) and $run) {
		$header="From:$from\n";
		$header .= "Reply-To: $from\n";
		$header.="Content-Type: text/html; charset=utf-8\n";
		mail($getemail, stripslashes($subject), stripslashes('This mail was send over your webSPELL - Website (IP '.$GLOBALS['ip'].'): '.$hp_url.'<br><br><strong>'.getinput($name).' writes:</strong><br>'.clearfromtags($text)), $header);
		redirect('index.php?site=contact',$_language->module['send_successfull'],3);
		unset($_POST['name']);
		unset($_POST['from']);
		unset($_POST['text']);
		unset($_POST['subject']);
	} else {
		$errors=implode('<br>&#8226; ',$fehler);

		$showerror = '<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<strong>'.$_language->module['errors_there'].':</strong><br>
			<br>
			&#8226; '.$errors.'
		</div>';
	}
}

$getemail = '';
$ergebnis=safe_query("SELECT * FROM ".PREFIX."contact ORDER BY sort");
while($ds=mysqli_fetch_array($ergebnis)) {
	if($getemail==$ds['email']) $getemail.='<option value="'.$ds['email'].'" selected="selected">'.$ds['name'].'</option>';
	else $getemail.='<option value="'.$ds['email'].'">'.$ds['name'].'</option>';
}

if($loggedin) {
	if(!isset($showerror)) $showerror='';
	$name=getinput(stripslashes(getnickname($userID)));
	$from=getinput(getemail($userID));
	if(isset($_POST['subject'])) $subject = getforminput($_POST['subject']);
	else $subject='';
	if(isset($_POST['text'])) $text =  getforminput($_POST['text']);
	else $text='';

	eval ("\$contact_loggedin = \"".gettemplate("contact_loggedin")."\";");
	echo $contact_loggedin;
} else {
	$CAPCLASS = new \webspell\Captcha;
	$captcha = $CAPCLASS->createCaptcha();
	$hash = $CAPCLASS->getHash();
	$CAPCLASS->clearOldCaptcha();
	if(!isset($showerror)) $showerror='';
	if(isset($_POST['name'])) $name = getforminput($_POST['name']);
	else $name='';
	if(isset($_POST['from'])) $from = getforminput($_POST['from']);
	else $from='';
	if(isset($_POST['subject'])) $subject = getforminput($_POST['subject']);
	else $subject='';
	if(isset($_POST['text'])) $text = getforminput($_POST['text']);
	else $text='';

	eval ("\$contact_notloggedin = \"".gettemplate("contact_notloggedin")."\";");
	echo $contact_notloggedin;
}
?>
