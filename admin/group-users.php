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
if(isset($_GET['ajax'])){
	chdir('../');
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	chdir('admin');
	if(isforumadmin($userID)){
		if(isset($_GET['action'])) $action = $_GET['action'];
		else $action = '';
		if($action == "usergroups"){
			$user = (int)$_GET['user'];
			$group = $_GET['group'];
			if($_GET['state'] == "true") $state = "1";
			else $state = "0";
			
			$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user_forum_groups WHERE userID='".$user."'"));
			if(!$anz) {
				safe_query("INSERT INTO ".PREFIX."user_forum_groups ( userID ) VALUES ('".$user."')");
			}
			$do = safe_query("UPDATE ".PREFIX."user_forum_groups SET `".$group."`='".$state."' WHERE userID='".$user."'");
		}
		else{
			$_language->read_module('group-users');
			echo $_language->module['access_denied'];
		}
	}
	else{
		$_language->read_module('group-users');
		echo $_language->module['access_denied'];
	}
	exit();
}
$_language->read_module('group-users');

if(!isforumadmin($userID) or mb_substr(basename($_SERVER['REQUEST_URI']), 0, 15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['action'])) {
	switch($_GET['action']):
	case "show":
		$anz_users_page = 50;
		if(isset($_REQUEST['page'])) {
			$page = (int)$_REQUEST['page'];
		}
		else {
			$page = 1;
		}
		
		if(isset($_GET['users'])) {
			$_POST['users'] = explode("-", $_GET['users']);
		}
		if(!isset($_POST['users'])){
			$_POST['users'] = array();
		}
		if(is_null($_POST['users'])){
			$_POST['users'] = array();
		}
		if(isset($_GET['groups'])) {
			$_POST['groups'] = explode("-", $_GET['groups']);
		}
		if(isset($_GET['addfield'])) {
			$_POST['addfield'] = $_GET['addfield'];
		}
		$users=array();
		if(in_array(0, $_POST['users'])) {
			$query=safe_query("SELECT userID FROM `".PREFIX."squads_members`");
			while($ds=mysql_fetch_array($query)) {
				if(!in_array($ds['userID'], $users)) $users[] = $ds['userID'];
			}
		}
		if(in_array(1, $_POST['users'])) {
			$query=safe_query("SELECT userID FROM `".PREFIX."user_groups` WHERE (page='1' OR forum='1' OR user='1' OR news='1' OR clanwars='1' OR feedback='1' OR super='1' OR gallery='1' OR cash='1' OR files='1')");
			while($ds=mysql_fetch_array($query)) {
				if(!in_array($ds['userID'], $users)) $users[] = $ds['userID'];
			}
		}
		if(in_array(2, $_POST['users'])) {
			$query=safe_query("SELECT userID FROM `".PREFIX."user_groups` WHERE super='1'");
			while($ds=mysql_fetch_array($query)) {
				if(!in_array($ds['userID'], $users)) $users[] = $ds['userID'];
			}
		}
		if(in_array(3, $_POST['users'])) {
			$fgrID=mysql_fetch_array(safe_query("SELECT fgrID FROM `".PREFIX."forum_groups` WHERE name = '".$_POST['addfield']."'"));
			if(!$fgrID['fgrID']) {
				echo '<b>'.$_language->module['error_group'].'</b><br /><br /><a href="admincenter.php?site=group-users">&laquo; '.$_language->module['back'].'</a>';
				break;
			}
			$query=safe_query("SELECT userID FROM `".PREFIX."user_forum_groups` WHERE `".$fgrID['fgrID']."` = '1'");
			while($ds=mysql_fetch_array($query)) {
				if(!in_array($ds['userID'], $users)) $users[] = $ds['userID'];
			}
		}
		if(in_array(4, $_POST['users']) or !count($_POST['users']) or empty($_GET['users'])) {
			$query=safe_query("SELECT userID FROM `".PREFIX."user`");
			while($ds=mysql_fetch_array($query)) {
				if(!in_array($ds['userID'], $users)) $users[] = $ds['userID'];
			}
		}
		$groups=array();
		if(isset($_POST['groups']))$grps = $_POST['groups'];
		else $grps = array(1);
		$sql=safe_query("SELECT * FROM ".PREFIX."forum_groups");
		while($ds=mysql_fetch_array($sql)) {
			if(in_array($ds['fgrID'], $grps)) $groups[] = array('fgrID' => $ds['fgrID'], 'name' => getinput($ds['name']));
		}
		$groups_anz = count($groups);
		
		$anz_users = count($users);
		$pages = ceil($anz_users / $anz_users_page);
		if($pages > 1) echo makepagelink("admincenter.php?site=group-users&amp;action=show&amp;users=".implode("-", $_POST['users'])."&amp;groups=".implode("-", $_POST['groups'])."&amp;addfield=".$_POST['addfield'], $page, $pages);
		
    echo'<h1>&curren; <a href="admincenter.php?site=group-users" class="white">'.$_language->module['group_users'].'</a> &raquo; '.$_language->module['edit_group_users'].'</h1>';
    echo'<script type="text/javascript">
    function setUser(userID,group,status){
    	fetch("group-users.php?ajax=true&action=usergroups&user="+userID+"&group="+group+"&state="+status,"","return","event");
    }
    function SelectAllEval() {
	for(var x=0;x<document.form.elements.length;x++) {
		var y=document.form.elements[x];
		if(y.name!=\'ALL\'){
			y.checked=document.form.ALL.checked;
			parts = y.value.split(\' => \');
			if(parts.length == 2){
				setUser(parts[0],parts[1],y.checked);
			}
		}
	}
}</script>';
    echo '<form method="post" name="form" action="admincenter.php?site=group-users&amp;action=show&amp;users='.implode("-", $_POST['users']).'&amp;groups='.implode("-", $_POST['groups']).'&amp;addfield='.$_POST['addfield'].'">
		<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="35%" class="title"><b>'.$_language->module['group_users'].'</b></td>';

		for($i=0; $i < $groups_anz; $i++) {
			echo '<td class="title"><b>'.$groups[$i]['name'].'</b></td>';
		}

		echo '</tr>';
    
    $n=1;
    $skip = $anz_users_page * ($page - 1);
		for($z = $skip; $z < ($skip + $anz_users_page) and $z < $anz_users; $z++) {
			if($n%2) { $td='td1'; }
      else { $td='td2'; }
      echo'<tr>
				<td class="'.$td.'">'.strip_tags(stripslashes(getnickname($users[$z]))).'</td>';

			for($i=0; $i < $groups_anz; $i++) {
				if(isinusergrp($groups[$i]['fgrID'], $users[$z], 0)) $checked = ' checked="checked"';
				else $checked = '';
				echo '<td class="'.$td.'"><input type="checkbox" onchange="javascript:setUser(\''.$users[$z].'\',\''.$groups[$i]['fgrID'].'\', this.checked);" value="'.$users[$z].' => '.$groups[$i]['fgrID'].'"'.$checked.' /></td>';
			}
			echo'</tr>';
      $n++;
		}
		echo'<tr>
        <td class="td_head"><input type="checkbox" name="ALL" value="ALL" onclick="SelectAllEval(this.form);" /> '.$_language->module['select_all'].'</td>
        <td class="td_head" colspan="'.($groups_anz).'" align="right"><input name="grps" type="hidden" value="'.implode(';', $grps).'" /><input name="users" type="hidden" value="'.implode(';', $users).'" />';
        
		if($pages > 1) {
			$page_select = '<select name="page">';
			for($i = 1; $i <= $pages; $i++) {
				if($i == $page) $selected = " selected='selected'";
				else $selected = "";
				$page_select .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
			}
			$page_select .= '</select>';
			echo $_language->module['save_and_jump'].'&nbsp;'.$page_select.' <input name="jump" type="submit" value="'.$_language->module['go'].'" /> ';
		}
		
        echo '</td>
		  </tr>
    </table>
    </form>';

		break;
		endswitch;
}
else {

	$groups = '';
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_groups");
	$selector=0;
	while($ds=mysql_fetch_array($ergebnis)) {	
		if($selector==0){
			$groups .= "\t\t".'<option value="'.$ds['fgrID'].'" selected="selected">'.getinput($ds['name']).'</option>'."\n";
		}
		else{
			$groups .= "\t\t".'<option value="'.$ds['fgrID'].'">'.getinput($ds['name']).'</option>'."\n";
		}
		$selector=1;
	}

	echo'<h1>&curren; '.$_language->module['group_users'].'</h1>';
  
  echo '<script type="text/javascript">
  /*<![CDATA[*/
  	function checkForFilter(select){
  		if(select.options[4].selected == true){
  			document.getElementById(\'addfield\').style.display = \'block\';
  			document.getElementById(\'addfield\').focus();
  		} else {
  			document.getElementById(\'addfield\').style.display = \'none\';
  		}
  	}
  /*]]>*/ 
  </script>
  <form method="post" name="post" action="admincenter.php?site=group-users&amp;action=show">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="65%" class="title"><b>'.$_language->module['groups'].'</b></td>
      <td width="35%" class="title"><b>'.$_language->module['user_filter'].'</b></td>
    </tr>
    <tr>
      <td class="td1" align="center" valign="top">
      <select name="groups[]" style="width:450px;" multiple="multiple">
        '.$groups.'
      </select>
      </td>
      <td class="td1" valign="top" align="center">
      <select name="users[]" style="width:200px;" multiple="multiple" onchange="checkForFilter(this);">
        <option value="4">'.$_language->module['filter_registered'].'</option>
        <option value="0">'.$_language->module['filter_clanmember'].'</option>
        <option value="1">'.$_language->module['filter_anyadmin'].'</option>
        <option value="2">'.$_language->module['filter_superadmin'].'</option>
        <option value="3">'.$_language->module['users_from_group'].'</option>
      </select>
      <div id="addfield" style="display:none;">
      <input name="addfield" style="width:170px; margin-top:5px;" type="text" />
      '.$_language->module['filter'].'
      </div>
      </td>
    </tr>
	  <tr>
      <td class="td_head" colspan="2" align="right"><input type="submit" value="'.$_language->module['show'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>