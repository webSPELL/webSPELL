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

$_language->read_module('ranks');

if(!isforumadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['delete'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query(" DELETE FROM ".PREFIX."forum_ranks WHERE rankID='".$_GET['rankID']."' ");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['save'])) {

	$name = $_POST['name'];
	$rank = $_FILES['rank'];
	$max = $_POST['max'];
  	$min = $_POST['min'];
  	
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('min', 'max'))) {
			if($max=="MAX") $maximum=2147483647;
			else $maximum=$max;
		
			safe_query("INSERT INTO ".PREFIX."forum_ranks ( rank, postmin, postmax ) values( '$name', '$min', '$maximum' )");
			$id=mysql_insert_id();
		
			$filepath = "../images/icons/ranks/";
			if ($rank['name'] != "") {
				move_uploaded_file($rank['tmp_name'], $filepath.$rank['name']);
				@chmod($filepath.$rank['name'], 0755);
				$file_ext=strtolower(mb_substr($rank['name'], strrpos($rank['name'], ".")));
				$file=$id.$file_ext;
				rename($filepath.$rank['name'], $filepath.$file);
				safe_query("UPDATE ".PREFIX."forum_ranks SET pic='$file' WHERE rankID='$id' ");
			}
		} else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {

	$rank = $_POST['rank'];
	$min = $_POST['min'];
	$max = $_POST['max'];
	
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {	 
		if(checkforempty(Array('min', 'max'))) {
			$ergebnis = safe_query("SELECT * FROM ".PREFIX."forum_ranks ORDER BY rankID");
			$anz=mysql_num_rows($ergebnis);
			if($anz) {
				while($ds=mysql_fetch_array($ergebnis)) {
					if($ds['rank'] != "Administrator" && $ds['rank'] != "Moderator") {
						$id=$ds['rankID'];
						if($max[$id]=="MAX") $maximum=2147483647;
						else $maximum=$max[$id];
						safe_query("UPDATE ".PREFIX."forum_ranks SET rank='$rank[$id]' WHERE rankID='$id'");
						safe_query("UPDATE ".PREFIX."forum_ranks SET postmin='$min[$id]' WHERE rankID='$id'");
						safe_query("UPDATE ".PREFIX."forum_ranks SET postmax='$maximum' WHERE rankID='$id'");
					}
				}
			}
		} else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  echo'<h1>&curren; <a href="admincenter.php?site=ranks" class="white">'.$_language->module['user_ranks'].'</a> &raquo; '.$_language->module['add_rank'].'</h1>';

  echo'<form method="post" action="admincenter.php?site=ranks" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['rank_icon'].'</b></td>
      <td width="85%"><input name="rank" type="file" size="40" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['rank_name'].'</b></td>
      <td><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['min_posts'].'</b></td>
      <td><input type="text" name="min" size="4" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['max_posts'].'</b></td>
      <td><input type="text" name="max" size="4" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_rank'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {
	
  echo'<h1>&curren; '.$_language->module['user_ranks'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=ranks&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_rank'].'" /><br /><br />';
	
  echo'<form method="post" action="admincenter.php?site=ranks">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="20%" class="title"><b>'.$_language->module['rank_icon'].'</b></td>
      <td width="49%" class="title"><b>'.$_language->module['rank_name'].'</b></td>
      <td width="10%" class="title"><b>'.$_language->module['min_posts'].'</b></td>
      <td width="11%" class="title"><b>'.$_language->module['max_posts'].'</b></td>
      <td width="10%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_ranks ORDER BY postmax");
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  $i=1;
  while($ds=mysql_fetch_array($ergebnis)) {
    if($i%2) { $td='td1'; }
    else { $td='td2'; }    
		if($ds['rank']=="Administrator" || $ds['rank']=="Moderator") {			
      		echo'<tr>
	        <td class="'.$td.'" align="center"><img src="../images/icons/ranks/'.$ds['pic'].'" alt="" /></td>
	        <td class="'.$td.'">'.$ds['rank'].'</td>
	        <td class="'.$td.'">&nbsp;</td>
	        <td class="'.$td.'">&nbsp;</td>
	        <td class="'.$td.'">&nbsp;</td>
	      </tr>';
		}
    
		else {
			if(mb_strlen(trim($ds['postmax']))>8) $max="MAX";
			else $max=$ds['postmax'];
			
      		echo'<tr>
	        <td class="'.$td.'" align="center"><img src="../images/icons/ranks/'.$ds['pic'].'" alt="" /></td>
	        <td class="'.$td.'"><input type="text" name="rank['.$ds['rankID'].']" value="'.getinput($ds['rank']).'" size="58" /></td>
	        <td class="'.$td.'" align="center"><input type="text" name="min['.$ds['rankID'].']" value="'.$ds['postmin'].'" size="6" dir="rtl" /></td>
	        <td class="'.$td.'" align="center"><input type="text" name="max['.$ds['rankID'].']" value="'.$max.'" size="6" dir="rtl" /></td>
	        <td class="'.$td.'" align="center"><input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=ranks&amp;delete=true&amp;rankID='.$ds['rankID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
	      </tr>';
		}
		$i++;
	}
	echo'<tr>
      <td class="td_head" colspan="5" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="saveedit" value="'.$_language->module['update'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>