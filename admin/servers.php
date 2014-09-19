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

$_language->read_module('servers');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_POST['save'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("INSERT INTO ".PREFIX."servers ( name, ip, game, info ) values( '".$_POST['name']."', '".$_POST['serverip']."', '".$_POST['game']."', '".$_POST['message']."' ) ");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."servers SET name='".$_POST['name']."', ip='".$_POST['serverip']."', game='".$_POST['game']."', info='".$_POST['message']."' WHERE serverID='".$_POST['serverID']."'");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['sort'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(is_array($_POST['sortlist'])) {
			foreach($_POST['sortlist'] as $sortstring) {
				$sorter=explode("-", $sortstring);
				safe_query("UPDATE ".PREFIX."servers SET sort='$sorter[1]' WHERE serverID='$sorter[0]' ");
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET['delete'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."servers WHERE serverID='".$_GET['serverID']."'");
	} else echo $_language->module['transaction_invalid'];
}

$games='';
$gamesa=safe_query("SELECT tag, name FROM ".PREFIX."games ORDER BY name");
while($dv=mysql_fetch_array($gamesa)) {
  $games.='<option value="'.$dv['tag'].'">'.getinput($dv['name']).'</option>';
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
	$_language->read_module('bbcode', true);
	
	eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
  eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
	
  echo '<h1>&curren; <a href="admincenter.php?site=servers" class="white">'.$_language->module['servers'].'</a> &raquo; '.$_language->module['add_server'].'</h1>';

  echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
  
	echo '<form method="post" id="post" name="post" action="admincenter.php?site=servers" onsubmit="return chkFormular();">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['server_name'].'</b></td>
      <td width="85%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['game'].'</b></td>
      <td><select name="game">'.$games.'</select></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['ip_port'].'</b></td>
      <td><input type="text" name="serverip" size="60" /></td>
    </tr>
    <tr>
      <td colspan="2" valign="top"><b>'.$_language->module['info'].'</b>
        <table width="99%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td valign="top">'.$addbbcode.'</td>
		        <td valign="top">'.$addflags.'</td>
		      </tr>
		    </table>
        <br /><textarea id="message" name="message" rows="5" cols="" style="width: 100%;"></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="save" value="'.$_language->module['add_server'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="edit") {
	
  echo'<h1>&curren; <a href="admincenter.php?site=servers" class="white">'.$_language->module['servers'].'</a> &raquo; '.$_language->module['edit_server'].'</h1>';

  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$_language->read_module('bbcode', true);
	
	eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
  eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
	
  $serverID = $_GET['serverID'];
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."servers WHERE serverID='".$serverID."'");
	$ds=mysql_fetch_array($ergebnis);

	$games=str_replace(' selected="selected"', '', $games);
	$games=str_replace('value="'.$ds['game'].'"', 'value="'.$ds['game'].'" selected="selected"', $games);
	
	echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
	
  echo '<form method="post" id="post" name="post" action="admincenter.php?site=servers" onsubmit="return chkFormular();">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['server_name'].'</b></td>
      <td width="85%"><input type="text" name="name" size="60" value="'.getinput($ds['name']).'" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['game'].'</b></td>
      <td><select name="game">'.$games.'</select></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['ip_port'].'</b></td>
      <td><input type="text" name="serverip" size="60" value="'.getinput($ds['ip']).'" /></td>
    </tr>
    <tr>
      <td colspan="2" valign="top"><b>'.$_language->module['info'].'</b>
        <table width="99%" border="0" cellspacing="0" cellpadding="0">
		      <tr>
		        <td valign="top">'.$addbbcode.'</td>
		        <td valign="top">'.$addflags.'</td>
		      </tr>
		    </table>
        <br /><textarea id="message" name="message" rows="5" cols="" style="width: 100%;">'.getinput($ds['info']).'</textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2"><input type="hidden" name="serverID" value="'.$serverID.'" /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="saveedit" value="'.$_language->module['edit_server'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {
	
  echo'<h1>&curren; '.$_language->module['servers'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=servers&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_server'].'" /><br /><br />';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."servers ORDER BY sort");
	$anz=mysql_num_rows($ergebnis);
	if($anz) {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  echo'<form method="post" name="ws_servers" action="admincenter.php?site=servers">
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="72%" class="title"><b>'.$_language->module['servers'].'</b></td>
        <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
        <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
      </tr>';

		$i=1;
    while($ds=mysql_fetch_array($ergebnis)) {
      if($i%2) { $td='td1'; }
      else { $td='td2'; }
    
			$list = '<select name="sortlist[]">';
			for($n=1;$n<=mysql_num_rows($ergebnis);$n++) {
				$list.='<option value="'.$ds['serverID'].'-'.$n.'">'.$n.'</option>';
			}
			$list .= '</select>';
			$list = str_replace('value="'.$ds['serverID'].'-'.$ds['sort'].'"','value="'.$ds['serverID'].'-'.$ds['sort'].'" selected="selected"',$list);

			echo'<tr>
        <td class="'.$td.'"><img src="../images/games/'.$ds['game'].'.gif" width="13" height="13" border="0" alt="" /> <a href="hlsw://'.$ds['ip'].'"><b>'.$ds['ip'].'</b></a><br /><b>'.getinput($ds['name']).'</b><br />'.cleartext($ds['info'],1,'admin').'</td>
        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=servers&amp;action=edit&amp;serverID='.$ds['serverID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=servers&amp;delete=true&amp;serverID='.$ds['serverID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
        <td class="'.$td.'" align="center">'.$list.'</td>
      </tr>';
        
        $i++;
		}
		echo'<tr>
        <td colspan="3" class="td_head" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="sort" value="'.$_language->module['to_sort'].'" /></td>
      </tr>
    </table>
    </form>';
	}
	else echo $_language->module['no_server'];
}
?>