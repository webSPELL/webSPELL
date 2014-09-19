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

$_language->read_module('games');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

$filepath = "../images/games/";

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; <a href="admincenter.php?site=games" class="white">'.$_language->module['games'].'</a> &raquo; '.$_language->module['add_game'].'</h1>';
	
	echo'<form method="post" action="admincenter.php?site=games" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['game_icon'].'</b></td>
      <td width="85%"><input name="icon" type="file" size="40" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['game_name'].'</b></td>
      <td><input type="text" name="name" size="60" maxlength="255" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['game_tag'].'</b></td>
      <td><input type="text" name="tag" size="5" maxlength="3" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_game'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="edit") {
	$ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."games WHERE gameID='".$_GET["gameID"]."'"));
	$pic='<img src="../images/games/'.$ds['tag'].'.gif" border="0" alt="'.$ds['name'].'" />';
  
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
  echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; <a href="admincenter.php?site=games" class="white">'.$_language->module['games'].'</a> &raquo; '.$_language->module['edit_game'].'</h1>';

	echo'<form method="post" action="admincenter.php?site=games" enctype="multipart/form-data">
  <input type="hidden" name="gameID" value="'.$ds['gameID'].'" />
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['present_icon'].'</b></td>
      <td width="85%">'.$pic.'</td>
    </tr>
    <tr>
      <td><b>'.$_language->module['game_icon'].'</b></td>
      <td><input name="icon" type="file" size="40" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['game_name'].'</b></td>
      <td><input type="text" name="name" size="60" maxlength="255" value="'.getinput($ds['name']).'" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['game_tag'].'</b></td>
      <td><input type="text" name="tag" size="5" maxlength="3" value="'.getinput($ds['tag']).'" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="saveedit" value="'.$_language->module['edit_game'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif(isset($_POST['save'])) {
	$icon=$_FILES["icon"];
	$name=$_POST["name"];
	$tag=$_POST["tag"];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {	
		if($name AND $tag) {
			$file_ext=strtolower(mb_substr($icon['name'], strrpos($icon['name'], ".")));
			if($file_ext==".gif") {
				safe_query("INSERT INTO ".PREFIX."games (gameID, name, tag) values('', '".$name."', '".$tag."')");
				if($icon['name'] != "") {
					move_uploaded_file($icon['tmp_name'], $filepath.$icon['name']);
					$file=$tag.$file_ext;
					rename($filepath.$icon['name'], $filepath.$file);
	        redirect("admincenter.php?site=games","",0);
				}
			} else echo'<b>'.$_language->module['format_incorrect'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
		} else echo'<b>'.$_language->module['fill_correctly'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
	} else echo $_language->module['transaction_invalid'];	
}

elseif(isset($_POST["saveedit"])) {
	$icon=$_FILES["icon"];
	$name=$_POST["name"];
	$tag=$_POST["tag"];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if($name AND $tag) {
			if($icon['name']=="") {
				if(safe_query("UPDATE ".PREFIX."games SET name='".$name."', tag='".$tag."' WHERE gameID='".$_POST["gameID"]."'"))
	      redirect("admincenter.php?site=games","",0);
	
			} else {
				$file_ext=strtolower(mb_substr($icon['name'], strrpos($icon['name'], ".")));
				if($file_ext==".gif") {
					move_uploaded_file($icon['tmp_name'], $filepath.$icon['name']);
					@chmod($filepath.$icon['name'], 0755);
					$file=$tag.$file_ext;
					rename($filepath.$icon['name'], $filepath.$file);
	
					if(safe_query("UPDATE ".PREFIX."games SET name='".$name."', tag='".$tag."' WHERE gameID='".$_POST["gameID"]."'")) {
						
	          redirect("admincenter.php?site=games","",0);
					}
				} else echo'<b>'.$_language->module['format_incorrect'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
			}
		} else echo'<b>'.$_language->module['fill_correctly'].'</b><br /><br /><a href="javascript:history.back()">&laquo; '.$_language->module['back'].'</a>';
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET["delete"])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."games WHERE gameID='".$_GET["gameID"]."'");
		redirect("admincenter.php?site=games","",0);
	} else echo $_language->module['transaction_invalid'];
}

else {
	
  echo'<h1>&curren; <a href="admincenter.php?site=icons" class="white">'.$_language->module['icons'].'</a> &raquo; '.$_language->module['games'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=games&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_game'].'" /><br /><br />';
  
  echo'<form method="post" action="admincenter.php?site=games">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="15" class="title"><b>'.$_language->module['icons'].'</b></td>
      <td width="45%" class="title"><b>'.$_language->module['game_name'].'</b></td>
      <td width="15%" class="title"><b>'.$_language->module['game_tag'].'</b></td>
      <td width="25%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';
  
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."games ORDER BY name");
	$anz=mysql_num_rows($ergebnis);
	if($anz) {
		
    $i=1;
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    while($ds = mysql_fetch_array($ergebnis)) {
      if($i%2) { $td='td1'; }
      else { $td='td2'; }
      $pic='<img src="../images/games/'.$ds['tag'].'.gif" border="0" alt="" />';
      			
      echo'<tr>
        <td class="'.$td.'" align="center">'.$pic.'</td>
        <td class="'.$td.'">'.getinput($ds['name']).'</td>
        <td class="'.$td.'" align="center">'.getinput($ds['tag']).'</td>
        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=games&amp;action=edit&amp;gameID='.$ds['gameID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=games&amp;delete=true&amp;gameID='.$ds['gameID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
      </tr>';
      
      $i++;
		}
	}
  else echo'<tr><td class="td1" colspan="5">'.$_language->module['no_entries'].'</td></tr>';
	
  echo '</table>
  </form>';
}
?>