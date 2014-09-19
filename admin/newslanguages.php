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

$_language->read_module('newslanguages');

if(!isnewsadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_POST['save'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('language', 'lang','alt'))) {
			safe_query("INSERT INTO ".PREFIX."news_languages ( language, lang, alt ) values( '".$_POST['language']."', '".$_POST['lang']."', '".$_POST['alt']."' ) ");
		} else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('language', 'lang', 'alt'))) {
			safe_query("UPDATE ".PREFIX."news_languages SET language='".$_POST['language']."', lang='".$_POST['lang']."', alt='".$_POST['alt']."' WHERE langID='".$_POST['langID']."'");
		} else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET['delete'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."news_languages WHERE langID='".$_GET['langID']."'");
	} else echo $_language->module['transaction_invalid'];
}

$langs='';
$getlangs=safe_query("SELECT country, short FROM ".PREFIX."countries ORDER BY country");
while($dt=mysql_fetch_array($getlangs)) {
  $langs.='<option value="'.$dt['short'].'">'.$dt['country'].'</option>';
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  $flag = '[flag][/flag]';
$country = flags($flag,'admin');
$country = str_replace("<img","<img id='getcountry'",$country);
  echo'<h1>&curren; <a href="admincenter.php?site=newslanguages" class="white">'.$_language->module['news_languages'].'</a> &raquo; '.$_language->module['add_language'].'</h1>';
  
  echo'<form method="post" action="admincenter.php?site=newslanguages">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['language'].'</b></td>
      <td width="85%"><input type="text" name="language" size="60" /></td>
    </tr>
    <tr>
      <td width="15%"><b>'.$_language->module['title'].'</b></td>
      <td width="85%"><input type="text" name="alt" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['flag'].'</b></td>
      <td><select name="lang" onchange="document.getElementById(\'getcountry\').src=\'../images/flags/\'+this.options[this.selectedIndex].value+\'.gif\'">'.$langs.'</select> &nbsp; '.$_language->module['preview'].': '.$country.'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_language'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="edit") {
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  echo'<h1>&curren; <a href="admincenter.php?site=newslanguages" class="white">'.$_language->module['news_languages'].'</a> &raquo; '.$_language->module['edit_language'].'</h1>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."news_languages WHERE langID='".$_GET['langID']."'");
	$ds=mysql_fetch_array($ergebnis);
  $flag = '[flag]'.$ds['lang'].'[/flag]';
$country = flags($flag,'admin');
$country = str_replace("<img","<img id='getcountry'",$country);
	$langs=str_replace(' selected="selected"', '', $langs);
	$langs=str_replace('value="'.$ds['lang'].'"', 'value="'.$ds['lang'].'" selected="selected"', $langs);
  
  echo'<form method="post" action="admincenter.php?site=newslanguages">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['language'].'</b></td>
      <td width="85%"><input type="text" name="language" value="'.getinput($ds['language']).'" size="60" /></td>
    </tr>
    <tr>
      <td width="15%"><b>'.$_language->module['title'].'</b></td>
      <td width="85%"><input type="text" name="alt" value="'.getinput($ds['alt']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['flag'].'</b></td>
      <td><select name="lang" onchange="document.getElementById(\'getcountry\').src=\'../images/flags/\'+this.options[this.selectedIndex].value+\'.gif\'">'.$langs.'</select> &nbsp; '.$_language->module['preview'].': '.$country.'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="langID" value="'.$ds['langID'].'" /></td>
      <td><input type="submit" name="saveedit" value="'.$_language->module['edit_language'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {
	
  echo'<h1>&curren; '.$_language->module['news_languages'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=newslanguages&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_language'].'" /><br /><br />';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."news_languages ORDER BY language");
	
  echo'<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="10%" class="title"><b>'.$_language->module['flag'].'</b></td>
      <td width="35%" class="title"><b>'.$_language->module['language'].'</b></td>
      <td width="35%" class="title"><b>'.$_language->module['title'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';

	$i=1;
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  while($ds=mysql_fetch_array($ergebnis)) {
    if($i%2) { $td='td1'; }
    else { $td='td2'; }
    
    $getflag='<img src="../images/flags/'.$ds['lang'].'.gif" border="0" alt="'.$ds['alt'].'" />';
      
		echo'<tr>
      <td class="'.$td.'" align="center">'.$getflag.'</td>
      <td class="'.$td.'">'.getinput($ds['language']).'</td>
      <td class="'.$td.'">'.getinput($ds['alt']).'</td>
      <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=newslanguages&amp;action=edit&amp;langID='.$ds['langID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
      <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=newslanguages&amp;delete=true&amp;langID='.$ds['langID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
    </tr>';
      
      $i++;
	}
	echo'</table>';
}
?>