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

$_language->read_module('members');

if(!isuseradmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_POST['sortieren'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
	 	if(isset($_POST['sort'])){
			$sort = $_POST['sort'];
			if(is_array($sort)) {
				foreach($sort as $sortstring) {
					$sorter=explode("-", $sortstring);
					safe_query("UPDATE ".PREFIX."squads_members SET sort='$sorter[1]' WHERE sqmID='$sorter[0]' ");
				}
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['delete'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		$id = $_GET['id'];
		$squadID = $_GET['squadID'];
		$squads=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE userID='$id'"));
		if($squads<2 AND !issuperadmin($id)) safe_query("DELETE FROM ".PREFIX."user_groups WHERE userID='$id'");
	
		safe_query("DELETE FROM ".PREFIX."squads_members WHERE userID='$id' AND squadID='$squadID'");
	} else echo $_language->module['transaction_invalid'];		
}

if(isset($_POST['saveedit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$id = $_POST['id'];
    $newswriter = isset($_POST['newswriter']);
		$newsadmin = isset($_POST['newsadmin']);
		$pollsadmin = isset($_POST['pollsadmin']);
		$feedbackadmin = isset($_POST['feedbackadmin']);
		$useradmin = isset($_POST['useradmin']);
		$cwadmin = isset($_POST['cwadmin']);
		$boardadmin = isset($_POST['boardadmin']);
		$moderator = isset($_POST['moderator']);
		$pageadmin = isset($_POST['pageadmin']);
		$fileadmin = isset($_POST['fileadmin']);
		$cashadmin = isset($_POST['cashadmin']);
		if(isset($_POST['position'])) $position = $_POST['position'];
		else $position=array();
		if(isset($_POST['message'])) $userdescription = $_POST['message'];
		else $userdescription='';
		if(isset($_POST['activity'])) $activity = $_POST['activity'];
		else $activity=array();
		if(isset($_POST['join'])) $join = $_POST['join'];
		else $join = array();
		if(isset($_POST['war'])) $war = $_POST['war'];
		else $war = array();
		$gallery = isset($_POST['galleryadmin']);
	
		if($userID != $id OR issuperadmin($userID)) {
	
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."user_groups WHERE userID='".$id."'");
			if(!mysql_num_rows($ergebnis)) safe_query("INSERT INTO ".PREFIX."user_groups (userID) values ('".$id."')");
			safe_query("UPDATE ".PREFIX."user_groups SET news='$newsadmin',
													  news_writer='".$newswriter."',
													  polls='$pollsadmin',
													  feedback='$feedbackadmin',
													  user='$useradmin', 
													  clanwars='$cwadmin',
													  forum='$boardadmin',
													  moderator='$moderator',
													  page='$pageadmin',
													  gallery='$gallery',
													  files='$fileadmin',
	                          cash='$cashadmin' WHERE userID='".$id."'");
			//remove from mods
			if($moderator == false){
				safe_query("DELETE FROM ".PREFIX."forum_moderators WHERE userID='".$id."'");
			}
			
			$sql=safe_query("SELECT * FROM ".PREFIX."forum_groups");
			while($dc=mysql_fetch_array($sql)) {
				$name=$dc['name'];
				$fgrID=$dc['fgrID'];
				$abc=safe_query("SELECT COUNT(*) as anz FROM ".PREFIX."user_forum_groups WHERE userID='".$id."'");
				$row = mysql_fetch_array($abc);
				if($row['anz']==1) {
	        safe_query("UPDATE ".PREFIX."user_forum_groups SET `".$fgrID."`='".isset($_POST[$fgrID])."' WHERE userID='".$id."'");
				}
				else {
					safe_query("INSERT INTO ".PREFIX."user_forum_groups ( userID , `".$fgrID."` ) VALUES ('".$id."', '".isset($_POST[$fgrID])."');");
				}
			}

		safe_query("UPDATE ".PREFIX."user SET userdescription='$userdescription' WHERE userID='$id'");

      	foreach($position as $sqmID=>$pos) {
			safe_query("UPDATE ".PREFIX."squads_members SET position='$pos' WHERE sqmID='$sqmID'");
		}
      	foreach($activity as $sqmID=>$act) {
			safe_query("UPDATE ".PREFIX."squads_members SET activity='$act' WHERE sqmID='$sqmID'");
		}
      	foreach($join as $sqmID=>$joi) {
			safe_query("UPDATE ".PREFIX."squads_members SET joinmember='$joi' WHERE sqmID='$sqmID'");
		}
      	foreach($war as $sqmID=>$wara) {
			safe_query("UPDATE ".PREFIX."squads_members SET warmember='$wara' WHERE sqmID='$sqmID'");
		}
		if(issuperadmin($userID)) safe_query("UPDATE ".PREFIX."user_groups SET super='".isset($_POST['superadmin'])."' WHERE userID='$id'");
		}
	  	else redirect('admincenter.php?site=members',$_language->module['error_own_rights'], 3);
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action']) and $_GET['action'] == "edit") {

  echo'<h1>&curren; <a href="admincenter.php?site=members" class="white">'.$_language->module['members'].'</a> &raquo; '.$_language->module['edit_member'].'</h1>';

	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$_language->read_module('bbcode', true);
	
	eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
  eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
	
	$id = $_GET['id'];
	$squads = '';
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE userID='$id' AND squadID!='0' GROUP BY squadID");
	$anz=mysql_num_rows($ergebnis);
	if($anz) {
		while($ds=mysql_fetch_array($ergebnis)) {
			if($ds['activity']) $activity=' <select name="activity['.$ds['sqmID'].']"><option value="1" selected="selected">'.$_language->module['active'].'</option><option value="0">'.$_language->module['inactive'].'</option></select>';
			else $activity=' <select name="activity['.$ds['sqmID'].']"><option value="1">'.$_language->module['active'].'</option><option value="0" selected="selected">'.$_language->module['inactive'].'</option></select>';
			if($ds['joinmember']) $join='<select name="join['.$ds['sqmID'].']"><option value="1" selected="selected">'.$_language->module['yes'].'</option><option value="0">'.$_language->module['no'].'</option></select>';
			else $join='<select name="join['.$ds['sqmID'].']"><option value="1">'.$_language->module['yes'].'</option><option value="0" selected="selected">'.$_language->module['no'].'</option></select>';
			if($ds['warmember']) $fight='<select name="war['.$ds['sqmID'].']"><option value="1" selected="selected">'.$_language->module['yes'].'</option><option value="0">'.$_language->module['no'].'</option></select>';
			else $fight='<select name="war['.$ds['sqmID'].']"><option value="1">'.$_language->module['yes'].'</option><option value="0" selected="selected">'.$_language->module['no'].'</option></select>';

			$squads.='<tr>
        <td colspan="2"><hr /></td>
      </tr>
      <tr>
        <td><b>'.$_language->module['squad'].'</b></td>
        <td><b>'.getsquadname($ds['squadID']).'</b></td>
      </tr>
      <tr>
        <td><b>'.$_language->module['position'].'</b></td>
        <td><input type="text" name="position['.$ds['sqmID'].']" value="'.getinput($ds['position']).'" size="60" />'.$activity.'</td>						   
      </tr>
      <tr>
        <td><b>'.$_language->module['access_rights'].'</b></td>
        <td>'.$_language->module['joinus_admin'].': '.$join.'&nbsp; &nbsp; '.$_language->module['fightus_admin'].': '.$fight.'</td>
      </tr>';
		}
	}
  
	if(isnewsadmin($id)) $news='<input type="checkbox" name="newsadmin" value="1" onmouseover="showWMTT(\'id1\')" onmouseout="hideWMTT()" checked="checked" />';
	else $news='<input type="checkbox" name="newsadmin" value="1" onmouseover="showWMTT(\'id1\')" onmouseout="hideWMTT()" />';
	
	if(isnewswriter($id)) $newswriter='<input type="checkbox" name="newswriter" value="1" onmouseover="showWMTT(\'id2\')" onmouseout="hideWMTT()" checked="checked" />';
	else $newswriter='<input type="checkbox" name="newswriter" onmouseover="showWMTT(\'id2\')" onmouseout="hideWMTT()" value="1" />';

	if(ispollsadmin($id)) $polls='<input type="checkbox" name="pollsadmin" value="1" onmouseover="showWMTT(\'id3\')" onmouseout="hideWMTT()" checked="checked" />';
	else $polls='<input type="checkbox" name="pollsadmin" value="1" onmouseover="showWMTT(\'id3\')" onmouseout="hideWMTT()" />';

	if(isfeedbackadmin($id)) $feedback='<input type="checkbox" name="feedbackadmin" value="1" onmouseover="showWMTT(\'id4\')" onmouseout="hideWMTT()" checked="checked" />';
	else $feedback='<input type="checkbox" name="feedbackadmin" value="1" onmouseover="showWMTT(\'id4\')" onmouseout="hideWMTT()" />';

	if(isuseradmin($id)) $useradmin='<input type="checkbox" name="useradmin" value="1" onmouseover="showWMTT(\'id5\')" onmouseout="hideWMTT()" checked="checked" />';
	else $useradmin='<input type="checkbox" name="useradmin" value="1" onmouseover="showWMTT(\'id5\')" onmouseout="hideWMTT()" />';

	if(isclanwaradmin($id)) $cwadmin='<input type="checkbox" name="cwadmin" value="1" onmouseover="showWMTT(\'id6\')" onmouseout="hideWMTT()" checked="checked" />';
	else $cwadmin='<input type="checkbox" name="cwadmin" value="1" onmouseover="showWMTT(\'id6\')" onmouseout="hideWMTT()" />';

	if(isforumadmin($id)) $board='<input type="checkbox" name="boardadmin" value="1" onmouseover="showWMTT(\'id7\')" onmouseout="hideWMTT()" checked="checked" />';
	else $board='<input type="checkbox" name="boardadmin" value="1" onmouseover="showWMTT(\'id7\')" onmouseout="hideWMTT()" />';

	if(isanymoderator($id)) $mod='<input type="checkbox" name="moderator" value="1" onmouseover="showWMTT(\'id8\')" onmouseout="hideWMTT()" checked="checked" />';
	else $mod='<input type="checkbox" name="moderator" value="1" onmouseover="showWMTT(\'id8\')" onmouseout="hideWMTT()" />';

	if(ispageadmin($id)) $page='<input type="checkbox" name="pageadmin" value="1" onmouseover="showWMTT(\'id9\')" onmouseout="hideWMTT()" checked="checked" />';
	else $page='<input type="checkbox" name="pageadmin" value="1" onmouseover="showWMTT(\'id9\')" onmouseout="hideWMTT()" />';

	if(isfileadmin($id)) $file='<input type="checkbox" name="fileadmin" value="1" onmouseover="showWMTT(\'id10\')" onmouseout="hideWMTT()" checked="checked" />';
	else $file='<input type="checkbox" name="fileadmin" value="1" onmouseover="showWMTT(\'id10\')" onmouseout="hideWMTT()" />';

	if(iscashadmin($id)) $cash='<input type="checkbox" name="cashadmin" value="1" onmouseover="showWMTT(\'id11\')" onmouseout="hideWMTT()" checked="checked" />';
	else $cash='<input type="checkbox" name="cashadmin" value="1" onmouseover="showWMTT(\'id11\')" onmouseout="hideWMTT()" />';

	if(isgalleryadmin($id)) $gallery='<input type="checkbox" name="galleryadmin" value="1" onmouseover="showWMTT(\'id12\')" onmouseout="hideWMTT()" checked="checked" />';
	else $gallery='<input type="checkbox" name="galleryadmin" value="1" onmouseover="showWMTT(\'id12\')" onmouseout="hideWMTT()" />';

	if(issuperadmin($id)) $super='<input type="checkbox" name="superadmin" value="1" onmouseover="showWMTT(\'id13\')" onmouseout="hideWMTT()" checked="checked" />';
	else $super='<input type="checkbox" name="superadmin" value="1" onmouseover="showWMTT(\'id13\')" onmouseout="hideWMTT()" />';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_groups");
	while($ds=mysql_fetch_array($ergebnis)) {
		$name=$ds['name'];
		$fgrID=$ds['fgrID'];
		if(isinusergrp($fgrID, $id, 0)) $usergrp[$fgrID]='<input type="checkbox" name="'.$fgrID.'" value="1" checked="checked" />';
		else $usergrp[$fgrID]='<input type="checkbox" name="'.$fgrID.'" value="1" />';
	}

	if(isclanmember($id)) $userdes='<tr>
    <td colspan="2"><b>'.$_language->module['description'].'</b><br />
      <table width="99%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td valign="top">'.$addbbcode.'</td>
		        <td valign="top">'.$addflags.'</td>
		      </tr>
		    </table>
      <br /><textarea id="message" rows="5" cols="" name="message" style="width: 100%;">'.getuserdescription($id).'</textarea>
    </td>
  </tr>';
	else $userdes='';

	echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
	
	echo '<form method="post" id="post" name="post" action="admincenter.php?site=members" onsubmit="return chkFormular();">
  <div class="tooltip" id="id1">'.$_language->module['tooltip_1'].'</div>
  <div class="tooltip" id="id2">'.$_language->module['tooltip_2'].'</div>
  <div class="tooltip" id="id3">'.$_language->module['tooltip_3'].'</div>
  <div class="tooltip" id="id4">'.$_language->module['tooltip_4'].'</div>
  <div class="tooltip" id="id5">'.$_language->module['tooltip_5'].'</div>
  <div class="tooltip" id="id6">'.$_language->module['tooltip_6'].'</div>
  <div class="tooltip" id="id7">'.$_language->module['tooltip_7'].'</div>
  <div class="tooltip" id="id8">'.$_language->module['tooltip_8'].'</div>
  <div class="tooltip" id="id9">'.$_language->module['tooltip_9'].'</div>
  <div class="tooltip" id="id10">'.$_language->module['tooltip_10'].'</div>
  <div class="tooltip" id="id11">'.$_language->module['tooltip_11'].'</div>
  <div class="tooltip" id="id12">'.$_language->module['tooltip_12'].'</div>
  <div class="tooltip" id="id13">'.$_language->module['tooltip_13'].'</div>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['nickname'].'</b></td>
      <td width="85%"><a href="../index.php?site=profile&amp;id='.$id.'" target="_blank">'.strip_tags(stripslashes(getnickname($id))).'</a></td>
    </tr>
    '.$squads.'
    '.$userdes.'
    <tr>
      <td colspan="2"><hr /></td>
    </tr>
  </table>
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td colspan="3"><b>'.$_language->module['access_rights'].'</b></td>
     </tr>
    <tr>
      <td width="25%">'.$news.' '.$_language->module['news_admin'].'</td>
      <td width="25%">'.$board.' '.$_language->module['messageboard_admin'].'</td>
      <td width="50%">'.$cwadmin.' '.$_language->module['clanwar_admin'].'</td>
    </tr>
    <tr>
      <td>'.$newswriter.' '.$_language->module['news_writer'].'</td>
      <td>'.$mod.' '.$_language->module['messageboard_moderator'].'</td>
      <td>'.$cash.' '.$_language->module['cash_admin'].'</td>
    </tr>
    <tr>
      <td>'.$polls.' '.$_language->module['polls_admin'].'</td>
      <td>'.$gallery.' '.$_language->module['gallery_admin'].'</td>
      <td>'.$useradmin.' '.$_language->module['user_admin'].'</td>
    </tr>
    <tr>
      <td>'.$feedback.' '.$_language->module['feedback_admin'].'</td>
      <td>'.$page.' '.$_language->module['page_admin'].'</td>
      <td>'.$file.' '.$_language->module['file_admin'].'</td>
    </tr>';
    
	if(issuperadmin($userID)) {
    echo '<tr>
      <td colspan="3">'.$super.' <b>'.$_language->module['super_admin'].'</b></td>
     </tr>';
   }
   
   echo '<tr>
    <td colspan="3"><hr /></td>
  </tr>
  <tr>
    <td colspan="3"><b>'.$_language->module['group_access'].'</b></td>
  </tr>';
  
	$sql=safe_query("SELECT * FROM ".PREFIX."forum_groups");
  echo '<tr>';
	$i = 1;
	while($dc=mysql_fetch_array($sql)) {
    $name=$dc['name'];
		$fgrID=$dc['fgrID'];
		echo '<td>'.$usergrp[$fgrID].' '.$name.'</td>';
		if(3 > 1) {
			if(($i - 1) % 3==(3-1)) echo '</tr><tr>';
		}
		else echo '</tr><tr>';
		$i++;
	}
	echo '<td></td></tr>';
  
  echo '<tr>
      <td><input type="hidden" name="id" value="'.$id.'" /><input type="hidden" name="captcha_hash" value="'.$hash.'" />
      <input type="submit" name="saveedit" value="'.$_language->module['edit_member'].'" /></td>
    </tr>
  </table>
  </form>';
  
	unset($squads);
  unset($userdes);
}

else {
	
  echo'<h1>&curren; '.$_language->module['members'].'</h1>';
  $CAPCLASS = new Captcha;
  $CAPCLASS->create_transaction();
  $hash = $CAPCLASS->get_hash();
  $squads=safe_query("SELECT * FROM ".PREFIX."squads ORDER BY sort");
	echo'<form method="post" action="admincenter.php?site=members">';
	while($ds=mysql_fetch_array($squads)) {
		
    echo'<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td class="title" colspan="5"><b>'.$ds['name'].'</b></td>
      </tr>';

		$members=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE squadID='".$ds['squadID']."' ORDER BY sort");
		$anzmembers=safe_query("SELECT count(squadID) FROM ".PREFIX."squads_members WHERE squadID='".$ds['squadID']."'");
		$anzmembers=mysql_result($anzmembers, 0);

		echo'<tr>
      <td width="30%" class="td_head"><b>'.$_language->module['country_nickname'].'</b></td>
      <td width="30%" class="td_head"><b>'.$_language->module['position'].'</b></td>
      <td width="12%" class="td_head"><b>'.$_language->module['activity'].'</b></td>
      <td width="20%" class="td_head"><b>'.$_language->module['actions'].'</b></td>
      <td width="8%" class="td_head"><b>'.$_language->module['sort'].'</b></td>
    </tr>';

		$i=1;
    while($dm=mysql_fetch_array($members)) {
      if($i%2) { $td='td1'; }
      else { $td='td2'; }
      
			$country = '[flag]'.getcountry($dm['userID']).'[/flag]';
			$country=flags($country);
			$country=str_replace("images/", "../images/", $country);
			$nickname='<a href="../index.php?site=profile&amp;id='.$dm['userID'].'" target="_blank">'.strip_tags(stripslashes(getnickname($dm['userID']))).'</a>';
			if($dm['activity']) $activity='<font color="green">'.$_language->module['active'].'</font>';
			else $activity='<font color="red">'.$_language->module['inactive'].'</font>';
			
      echo'<tr>
        <td class="'.$td.'">'.$country.' '.$nickname.'</td>
        <td class="'.$td.'">'.$dm['position'].'</td>
        <td class="'.$td.'" align="center">'.$activity.'</td>
        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=members&amp;action=edit&amp;id='.$dm['userID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=members&amp;delete=true&amp;id='.$dm['userID'].'&amp;squadID='.$dm['squadID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
        <td class="'.$td.'" align="center"><select name="sort[]">';
           
			for($j=1; $j<=$anzmembers; $j++) {
				if($dm['sort'] == $j) echo'<option value="'.$dm['sqmID'].'-'.$j.'" selected="selected">'.$j.'</option>';
				else echo'<option value="'.$dm['sqmID'].'-'.$j.'">'.$j.'</option>';
			}
			echo'</select></td>
      </tr>';
         
         $i++;
		}
		echo'</table><br />';
	}
	echo'<div align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></div></form>';
}
?>