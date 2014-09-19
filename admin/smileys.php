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

$_language->read_module('smileys');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

$filepath = "../images/smileys/";

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; <a href="admincenter.php?site=smileys" class="white">'.$_language->module['smilies'].'</a> &raquo; '.$_language->module['add_smiley'].'</h1>';
  
  echo'<form method="post" action="admincenter.php?site=smileys" enctype="multipart/form-data">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['icon'].'</b></td>
      <td width="85%"><input name="icon" type="file" size="40" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['smiley_name'].'</b></td>
      <td><input type="text" name="alt" size="60" maxlength="255" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['pattern'].'</b></td>
      <td><input type="text" name="pattern" size="60" maxlength="20" /> '.$_language->module['example'].'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_smiley'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="edit") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	$ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."smileys WHERE smileyID='".$_GET["smileyID"]."'"));
	$pic='<img src="../images/smileys/'.$ds['name'].'" border="0" alt="'.getinput($ds['alt']).'" />';

	echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; <a href="admincenter.php?site=smileys" class="white">'.$_language->module['smilies'].'</a> &raquo; '.$_language->module['edit_smiley'].'</h1>';
  
  echo'<form method="post" action="admincenter.php?site=smileys" enctype="multipart/form-data">
		<input type="hidden" name="smileyID" value="'.$ds['smileyID'].'" />
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['present_icon'].'</b></td>
      <td width="85%">'.$pic.'</td>
    </tr>
    <tr>
      <td><b>'.$_language->module['icon'].'</b></td>
      <td><input name="icon" type="file" size="40" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['smiley_name'].'</b></td>
      <td><input type="text" name="alt" size="60" maxlength="255" value="'.htmlspecialchars($ds['alt']).'" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['pattern'].'</b></td>
      <td><input type="text" name="pattern" size="60" maxlength="20" value="'.htmlspecialchars($ds['pattern']).'" /> '.$_language->module['example'].'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="saveedit" value="'.$_language->module['edit_smiley'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif(isset($_POST["save"])) {
	$icon=$_FILES["icon"];
	$alt=$_POST["alt"];
	$pattern=$_POST["pattern"];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if($pattern) {
			$file_ext=strtolower(mb_substr($icon['name'], strrpos($icon['name'], ".")));
			if($file_ext==".gif") {
				if($icon['name'] != "") {
					move_uploaded_file($icon['tmp_name'], $filepath.$icon['name']);
					@chmod($filepath.$icon['name'], 0755);
					$file=$pattern.$file_ext;
					rename($filepath.$icon['name'], $filepath.$file);
					safe_query("INSERT INTO ".PREFIX."smileys (smileyID, name, alt, pattern) values('', '".$file."', '".$alt."', '".$pattern."')");
					redirect('admincenter.php?site=smileys','',0);
				}
			} else echo'<b>'.$_language->module['format_incorrect'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
		} else echo'<b>'.$_language->module['fill_form'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST["saveedit"])) {
	$icon=$_FILES["icon"];
	$alt=$_POST["alt"];
	$pattern=$_POST['pattern'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if($pattern) {
			if($icon['name']=="") {
				if(safe_query("UPDATE ".PREFIX."smileys SET alt='".$alt."', pattern='".$pattern."' WHERE smileyID='".$_POST["smileyID"]."'"))
				redirect('admincenter.php?site=smileys','',0);
			} else {
				$file_ext=strtolower(mb_substr($icon['name'], strrpos($icon['name'], ".")));
				if($file_ext==".gif") {
					move_uploaded_file($icon['tmp_name'], $filepath.$icon['name']);
					@chmod($filepath.$icon['name'], 0755);
					$file=$pattern.$file_ext;
					if(file_exists($filepath.$file)) @unlink($filepath.$file);
					@rename($filepath.$icon['name'], $filepath.$file);
	
					if(safe_query("UPDATE ".PREFIX."smileys SET name='".$file."', alt='".$alt."', pattern='".$pattern."' WHERE smileyID='".$_POST["smileyID"]."'")) {
						redirect('admincenter.php?site=smileys','',0);
					}
				} else echo'<b>'.$_language->module['format_incorrect'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
			}
		} else echo'<b>'.$_language->module['fill_form'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET["delete"])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."smileys WHERE smileyID='".$_GET["smileyID"]."'");
		redirect('admincenter.php?site=smileys','',0);
	} else redirect('admincenter.php?site=smileys',$_language->module['transaction_invalid'],3);
}

else {
	echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; '.$_language->module['smilies'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=smileys&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_smiley'].'" /><br /><br />';
  
  echo'<form method="post" action="admincenter.php?site=smileys">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="15%" class="title"><b>'.$_language->module['icon'].'</b></td>
      <td width="45%" class="title"><b>'.$_language->module['smiley_name'].'</b></td>
      <td width="15%" class="title"><b>'.$_language->module['pattern'].'</b></td>
      <td width="25%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';
    
	$ds=safe_query("SELECT * FROM ".PREFIX."smileys");
	$anz=mysql_num_rows($ds);
	if($anz) {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
    $i=1;
    while($smileys = mysql_fetch_array($ds)) {
      if($i%2) { $td='td1'; }
      else { $td='td2'; }
      		
			$pic='<img src="../images/smileys/'.$smileys['name'].'" border="0" alt="'.getinput($smileys['alt']).'" />';
			if($smileys['alt']=="") $smileys['alt'] = $smileys['name'];
			
      echo'<tr>
        <td class="'.$td.'" align="center">'.$pic.'</td>
        <td class="'.$td.'">'.$smileys['alt'].'</td>
        <td class="'.$td.'" align="center">'.$smileys['pattern'].'</td>
        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=smileys&amp;action=edit&amp;smileyID='.$smileys['smileyID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=smileys&amp;delete=true&amp;smileyID='.$smileys['smileyID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
      </tr>';
      
      $i++;
		}
	} else echo'<tr><td class="td1">'.$_language->module['no_entries'].'</td></tr>';
	echo '</table>
  </form>';
}
?>