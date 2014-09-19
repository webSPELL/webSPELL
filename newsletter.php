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

$_language->read_module('newsletter');

if(isset($_GET['action'])) $action = $_GET['action'];
else $action='';

if($action=="save") {

	$email = $_POST['email'];

	if(!validate_email($email)) redirect('index.php?site=newsletter', $_language->module['email_not_valid'],3);
	else {
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."newsletter WHERE email='".$email."'");
		if(!mysql_num_rows($ergebnis)) {
      		$pass = RandPass(7);

			safe_query("INSERT INTO ".PREFIX."newsletter ( email, pass) values ('".$email."', '".$pass."')");

			$header="From:".$hp_title."<".$admin_email.">\n";
			$header .= "Reply-To: ".$admin_email."\n";
			$header.= "Content-Type: text/html; charset=utf-8\n";
			$vars = Array('%delete_key%', '%homepage_url%', '%mail%');
			$repl = Array($pass, $hp_url, $email);
			mail($email, $hp_title.": ".$_language->module['newsletter_registration'], str_replace($vars, $repl, $_language->module['success_mail']), $header);

			redirect('index.php?site=newsletter', $_language->module['thank_you_for_registration'],3);
		}
		else redirect('index.php?site=newsletter', $_language->module['you_are_already_registered'],3);
	}
}
elseif($action=="delete") {
	$ergebnis = safe_query("SELECT pass FROM ".PREFIX."newsletter WHERE email='".$_POST['email']."'");
	$any=mysql_num_rows($ergebnis);
	if($any){
		$dn=mysql_fetch_array($ergebnis);
	
		if($_POST['password'] == $dn['pass']) {
			safe_query("DELETE FROM ".PREFIX."newsletter WHERE email='".$_POST['email']."'");
			redirect('index.php?site=newsletter', $_language->module['your_mail_adress_deleted'],3);
		}
		else {
			redirect('index.php?site=newsletter', $_language->module['mail_pw_didnt_match'],3);
		}
	}
	else {
		redirect('index.php?site=newsletter', $_language->module['mail_not_in_db'],3);
	}
}
elseif($action=="forgot") {
	$ergebnis = safe_query("SELECT pass FROM ".PREFIX."newsletter WHERE email='".$_POST['email']."'");
	$dn=mysql_fetch_array($ergebnis);

	if($dn['pass'] != "") {

		$email = $_POST['email'];
		$pass = $dn['pass'];

		$header="From:".$hp_title."<".$admin_email.">\n";
		$header .= "Reply-To: ".$admin_email."\n";
		$header.= "Content-Type: text/html; charset=utf-8\n";
		$vars = Array('%delete_key%', '%homepage_url%', '%mail%');
		$repl = Array($pass, $hp_url, $email);
		mail($email, $hp_title.": ".$_language->module['deletion_key'], str_replace($vars, $repl, $_language->module['request_mail']), $header);

		redirect('index.php?site=newsletter', $_language->module['password_had_been_send'],3);
	}
	else redirect('index.php?site=newsletter', $_language->module['no_such_mail_adress'],3);
}
else {

	$bg1=BG_1;
	$bg2=BG_2;
	$bg3=BG_3;
	$bg4=BG_4;

	$usermail = getemail($userID);
	if(isset($_GET['mail'])) $get_mail = getforminput($_GET['mail']);
  	else $get_mail='';
	if($get_mail == "") $get_mail = $_language->module['mail_adress'];
  	if(isset($_GET['pass'])) $get_pw = getforminput($_GET['pass']);
  	else $get_pw='';
	if($get_pw == "") $get_pw = $_language->module['del_key'];

	eval ("\$newsletter_title = \"".gettemplate("title_newsletter")."\";");
	echo $newsletter_title;

	eval ("\$newsletter = \"".gettemplate("newsletter")."\";");
	echo $newsletter;

}
?>