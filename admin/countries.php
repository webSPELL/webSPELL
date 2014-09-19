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

$_language->read_module('countries');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

$filepath = "../images/flags/";

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; <a href="admincenter.php?site=countries" class="white">'.$_language->module['countries'].'</a> &raquo; '.$_language->module['add_country'].'</h1>';
	
	echo'<form method="post" action="admincenter.php?site=countries" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['icon_upload'].'</b></td>
      <td width="85%"><input name="icon" type="file" size="40" /> <small>'.$_language->module['max_18x12'].'</small></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['country'].'</b></td>
      <td><input type="text" name="country" size="60" maxlength="255" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['shorthandle'].'</b></td>
      <td><input type="text" name="shorthandle" size="5" maxlength="3" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_country'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="edit") {
	$ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."countries WHERE countryID='".$_GET["countryID"]."'"));
	$pic='<img src="../images/flags/'.$ds['short'].'.gif" border="0" alt="'.$ds['country'].'" />';
  
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
  echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; <a href="admincenter.php?site=countries" class="white">'.$_language->module['countries'].'</a> &raquo; '.$_language->module['edit_country'].'</h1>';

	echo'<form method="post" action="admincenter.php?site=countries" enctype="multipart/form-data">
  <input type="hidden" name="countryID" value="'.$ds['countryID'].'" />
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['present_icon'].'</b></td>
      <td width="85%">'.$pic.'</td>
    </tr>
    <tr>
      <td><b>'.$_language->module['icon_upload'].'</b></td>
      <td><input name="icon" type="file" size="40" /> <small>'.$_language->module['max_18x12'].'</small></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['country'].'</b></td>
      <td><input type="text" name="country" size="60" maxlength="255" value="'.getinput($ds['country']).'" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['shorthandle'].'</b></td>
      <td><input type="text" name="shorthandle" size="5" maxlength="3" value="'.getinput($ds['short']).'" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="saveedit" value="'.$_language->module['edit_country'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif(isset($_POST['save'])) {
	$icon=$_FILES["icon"];
	$country=$_POST["country"];
	$short=$_POST["shorthandle"];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {	
		if($country AND $short) {
			$file_ext=strtolower(mb_substr($icon['name'], strrpos($icon['name'], ".")));
			if($file_ext==".gif") {
				safe_query("INSERT INTO ".PREFIX."countries (countryID, country, short) values('', '".$country."', '".$short."')");
				if($icon['name'] != "") {
					move_uploaded_file($icon['tmp_name'], $filepath.$icon['name']);
					$file=$short.$file_ext;
					rename($filepath.$icon['name'], $filepath.$file);
	        redirect("admincenter.php?site=countries","",0);
				}
			} else echo'<b>'.$_language->module['format_incorrect'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
		} else echo'<b>'.$_language->module['fill_correctly'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
	} else echo $_language->module['transaction_invalid'];	
}

elseif(isset($_POST["saveedit"])) {
	$icon=$_FILES["icon"];
	$country=$_POST["country"];
	$short=$_POST["shorthandle"];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if($country AND $short) {
			if($icon['name']=="") {
				if(safe_query("UPDATE ".PREFIX."countries SET country='".$country."', short='".$short."' WHERE countryID='".$_POST["countryID"]."'"))
	      redirect("admincenter.php?site=countries","",0);
	
			} else {
				$file_ext=strtolower(mb_substr($icon['name'], strrpos($icon['name'], ".")));
				if($file_ext==".gif") {
					move_uploaded_file($icon['tmp_name'], $filepath.$icon['name']);
					@chmod($filepath.$icon['name'], 0755);
					$file=$short.$file_ext;
					rename($filepath.$icon['name'], $filepath.$file);
	
					if(safe_query("UPDATE ".PREFIX."countries SET country='".$country."', short='".$short."' WHERE countryID='".$_POST["countryID"]."'")) {
						
	          redirect("admincenter.php?site=countries","",0);
					}
				} else echo'<b>'.$_language->module['format_incorrect'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
			}
		} else echo'<b>'.$_language->module['fill_correctly'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET["delete"])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."countries WHERE countryID='".$_GET["countryID"]."'");
		redirect("admincenter.php?site=countries","",0);
	} else echo $_language->module['transaction_invalid'];
}

else {
	
  echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; '.$_language->module['countries'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=countries&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_country'].'" /><br /><br />';
  
  echo'<form method="post" action="admincenter.php?site=countries">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="15%" class="title"><b>'.$_language->module['icon'].'</b></td>
      <td width="45%" class="title"><b>'.$_language->module['country'].'</b></td>
      <td width="15%" class="title"><b>'.$_language->module['shorthandle'].'</b></td>
      <td width="25%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';
  
	$ds=safe_query("SELECT * FROM ".PREFIX."countries ORDER BY country");
	$anz=mysql_num_rows($ds);
	if($anz) {
		
    $i=1;
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    while($flags = mysql_fetch_array($ds)) {
      if($i%2) { $td='td1'; }
			else { $td='td2'; }
			$pic='<img src="../images/flags/'.$flags['short'].'.gif" border="0" alt="'.$flags['country'].'" />';
			
      echo'<tr>
        <td class="'.$td.'" align="center">'.$pic.'</td>
        <td class="'.$td.'">'.getinput($flags['country']).'</td>
        <td class="'.$td.'" align="center">'.getinput($flags['short']).'</td>
        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=countries&amp;action=edit&amp;countryID='.$flags['countryID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=countries&amp;delete=true&amp;countryID='.$flags['countryID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
      </tr>';
      
      $i++;
		}
	}
  else echo'<tr><td class="td1" colspan="5">'.$_language->module['no_entries'].'</td></tr>';
	
  echo '</table>
  </form>';
}
?>