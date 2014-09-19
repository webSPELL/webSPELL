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

$_language->read_module('groups');

if(!isforumadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="delete") {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		if(!$_GET['fgrID']) die('missing fgrID... <a href="admincenter.php?site=groups">back</a>');
		safe_query("ALTER TABLE ".PREFIX."user_forum_groups DROP `".$_GET['fgrID']."`");
		safe_query("DELETE FROM ".PREFIX."forum_groups WHERE fgrID='".$_GET['fgrID']."'");
		
  		redirect("admincenter.php?site=groups","",0);
  	} else echo $_language->module['transaction_invalid'];
}

elseif($action=="add") {
	
  echo'<h1>&curren; <a href="admincenter.php?site=groups" class="white">'.$_language->module['groups'].'</a> &raquo; '.$_language->module['add_group'].'</h1>';

  $CAPCLASS = new Captcha;
  $CAPCLASS->create_transaction();
  $hash = $CAPCLASS->get_hash();
  
  echo'<form method="post" action="admincenter.php?site=groups&amp;action=save">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['group_name'].'</b></td>
      <td width="85%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_group'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="save") {
	if(!$_POST['name']) die('<b>'.$_language->module['error_group'].'</b><br /><br /><a href="admincenter.php?site=groups&amp;action=add">&laquo; '.$_language->module['back'].'</a>');
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("INSERT INTO ".PREFIX."forum_groups ( name ) values( '".$_POST['name']."' ) ");
		$id = mysql_insert_id();
		if(!safe_query("ALTER TABLE ".PREFIX."user_forum_groups ADD `".$id."` INT( 1 ) NOT NULL ; ")) {
			safe_query("ALTER TABLE ".PREFIX."user_forum_groups DROP `".$id."`");
			safe_query("ALTER TABLE ".PREFIX."user_forum_groups ADD `".$id."` INT( 1 ) NOT NULL ; ");
		}
	
		redirect("admincenter.php?site=groups","",0);
	} else echo $_language->module['transaction_invalid'];	
}

elseif($action=="saveedit") {
	$name=$_POST['name'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."forum_groups SET name='".$name."' WHERE fgrID='".$_POST['fgrID']."'");	
  		redirect("admincenter.php?site=groups","",0);
	} else echo $_language->module['transaction_invalid'];
}

elseif($action=="edit") {
	
  echo'<h1>&curren; <a href="admincenter.php?site=groups" class="white">'.$_language->module['groups'].'</a> &raquo; '.$_language->module['edit_group'].'</h1>';
  
  if(!$_GET['fgrID']) die('<b>'.$_language->module['error_groupid'].'</b><br /><br /><a href="admincenter.php?site=groups">&laquo; '.$_language->module['back'].'</a>');
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_groups WHERE fgrID='".$_GET['fgrID']."'");
	$ds=mysql_fetch_array($ergebnis);
  
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  echo'<form method="post" action="admincenter.php?site=groups&amp;action=saveedit">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['group_name'].'</b></td>
      <td width="85%"><input type="text" name="name" value="'.getinput($ds["name"]).'" size="60" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input name="fgrID" type="hidden" value="'.$ds["fgrID"].'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['edit_group'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {
	
  echo'<h1>&curren; '.$_language->module['groups'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=groups&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_group'].'" /><br /><br />';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_groups ORDER BY fgrID");
	
  echo'<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="80%" class="title"><b>'.$_language->module['group_name'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';
  
  $i=1;
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
	while($ds=mysql_fetch_array($ergebnis)) {
    if($i%2) { $td='td1'; }
    else { $td='td2'; }
  
		echo'<tr>
      <td class="'.$td.'"><b>'.getinput($ds['name']).'</b></td>
      <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=groups&amp;action=edit&amp;fgrID='.$ds["fgrID"].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
      <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=groups&amp;action=delete&amp;fgrID='.$ds["fgrID"].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
		</tr>';
      
      $i++;
	}

	echo'</table>';
}
?>