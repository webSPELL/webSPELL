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

$_language->read_module('scrolltext');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['scrolltext'].'</h1>';

if(isset($_POST['submit']) != "") {
	$text = $_POST['text'];
	$delay = $_POST['delay'];
	$direction = $_POST['direction'];
	$color = $_POST['color'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."scrolltext"))) safe_query("UPDATE ".PREFIX."scrolltext SET text='$text', delay='$delay', direction='$direction', color='$color'");
		else safe_query("INSERT INTO ".PREFIX."scrolltext (text, delay, direction, color) values( '$text', '$delay', '$direction', '$color') ");
	
		redirect("admincenter.php?site=scrolltext","",0);
	} else redirect("admincenter.php?site=scrolltext",$_language->module['transaction_invalid'],3);
}

elseif(isset($_POST['delete']) != "") {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
  		safe_query("DELETE FROM ".PREFIX."scrolltext");
		redirect("admincenter.php?site=scrolltext","",0);
	} else redirect("admincenter.php?site=scrolltext",$_language->module['transaction_invalid'],3);
}

else {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."scrolltext");
	$ds=mysql_fetch_array($ergebnis);

	$direction = '<option value="left">'.$_language->module['right_to_left'].'</option>
  <option value="right">'.$_language->module['left_to_right'].'</option>';
	$direction = str_replace('value="'.$ds['direction'].'"','value="'.$ds['direction'].'" selected="selected"',$direction);

	$delay = '<option value="1">'.$_language->module['1_slow'].'</option>
  <option value="2">'.$_language->module['2_normal'].'</option>
  <option value="3">'.$_language->module['3_fast'].'</option>';
	$delay = str_replace('value="'.$ds['delay'].'"','value="'.$ds['delay'].'" selected="selected"',$delay);;

	echo'<form method="post" action="admincenter.php?site=scrolltext">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td colspan="2"><b>'.$_language->module['scrolltext'].'</b><br /><small>'.$_language->module['you_can_use_html'].'</small><br /><br />
      <input type="text" size="110" name="text" value="'.getinput($ds['text']).'" /></td>
    </tr>
    <tr>
      <td width="15%"><b>'.$_language->module['direction'].'</b></td>
      <td width="85%"><select name="direction">'.$direction.'</select></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['delay'].'</b></td>
      <td><select name="delay">'.$delay.'</select></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['color'].'</b></td>
      <td><input type="text" name="color" value="'.$ds['color'].'" maxlength="7" /> '.$_language->module['example'].'</td>
    </tr>
    <tr>
      <td colspan="2"><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="submit" value="'.$_language->module['update'].'" />
      <input type="submit" name="delete" value="'.$_language->module['delete'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>