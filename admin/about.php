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

$_language->read_module('about');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['about'].'</h1>';

if(isset($_POST['submit']) != "") {
	$about = $_POST['message'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."about")))	safe_query("UPDATE ".PREFIX."about SET about='".$about."'");
		else safe_query("INSERT INTO ".PREFIX."about (about) values( '".$about."') ");
		redirect("admincenter.php?site=about", "", 0);
	} else echo $_language->module['transaction_invalid'];
}
else {
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."about");
	$ds=mysql_fetch_array($ergebnis);

	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$_language->read_module('bbcode', true);

	echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
	
	echo '<form method="post" id="post" name="post" action="admincenter.php?site=about" onsubmit="return chkFormular();">
  <b>'.$_language->module['about'].'</b><br /><small>'.$_language->module['you_can_use_html'].'</small><br /><br />';
	
	eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
  eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
  
  echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td valign="top">'.$addbbcode.'</td>
		        <td valign="top">'.$addflags.'</td>
		      </tr>
		    </table>';
  
  echo '<br /><textarea id="message" name="message" rows="30" cols="" style="width: 100%;">'.getinput($ds['about']).'</textarea>
  <br /><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="submit" value="'.$_language->module['update'].'" />
  </form>';
}
?>