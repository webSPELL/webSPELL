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

$_language->read_module('partners');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['delete'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		$partnerID = $_GET['partnerID'];
		safe_query(" DELETE FROM ".PREFIX."partners WHERE partnerID='$partnerID' ");
		$filepath = "../images/partners/";
		if(file_exists($filepath.$partnerID.'.gif')) unlink($filepath.$partnerID.'.gif');
		if(file_exists($filepath.$partnerID.'.jpg')) unlink($filepath.$partnerID.'.jpg');
		if(file_exists($filepath.$partnerID.'.png')) unlink($filepath.$partnerID.'.png');
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['sortieren'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$sort = $_POST['sort'];
		foreach($sort as $sortstring) {
			$sorter=explode("-", $sortstring);
			safe_query("UPDATE ".PREFIX."partners SET sort='$sorter[1]' WHERE partnerID='$sorter[0]' ");
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['save'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$name = $_POST['name'];
		$url = $_POST['url'];
		$banner = $_FILES['banner'];
		if(isset($_POST["displayed"])) $displayed = $_POST['displayed'];
		else $displayed="";
		if(!$displayed) $displayed=0;
	
		safe_query("INSERT INTO ".PREFIX."partners ( name, url, displayed, date, sort )
		             values( '$name', '$url', '".$displayed."', '".time()."', '1' )");
		$id=mysql_insert_id();
	
		$filepath = "../images/partners/";
		
		if($banner['name'] != "") {
			move_uploaded_file($banner['tmp_name'], $filepath.$banner['name'].".tmp");
			@chmod($filepath.$banner['name'].".tmp", 0755);
			$getimg = getimagesize($filepath.$banner['name'].".tmp");
			if($getimg[0] < 89 && $getimg[1] < 32) {
				$pic = '';
				if($getimg[2] == 1) $pic=$id.'.gif';
				elseif($getimg[2] == 2) $pic=$id.'.jpg';
				elseif($getimg[2] == 3) $pic=$id.'.png';
				if($pic != "") {
					if(file_exists($filepath.$id.'.gif')) unlink($filepath.$id.'.gif');
					if(file_exists($filepath.$id.'.jpg')) unlink($filepath.$id.'.jpg');
					if(file_exists($filepath.$id.'.png')) unlink($filepath.$id.'.png');
					rename($filepath.$banner['name'].".tmp", $filepath.$pic);
					safe_query("UPDATE ".PREFIX."partners SET banner='".$pic."' WHERE partnerID='".$id."'");
				}  else {
					if(unlink($filepath.$banner['name'].".tmp")) {
						$error = $_language->module['format_incorrect'];
						die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
					} else {
						$error = $_language->module['format_incorrect'];
						die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
					}
				}
			} else {
				@unlink($filepath.$banner['name'].".tmp");
				$error = $_language->module['banner_to_big'];
				die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$name = $_POST['name'];
		$url = $_POST['url'];
		$banner = $_FILES['banner'];
		if(isset($_POST["displayed"])) $displayed = $_POST['displayed'];
		else $displayed="";
		if(!$displayed) $displayed=0;
		$partnerID = $_POST['partnerID'];
		$id=$partnerID;
		
		$filepath = "../images/partners/";
		
		if($banner['name'] != "") {
			move_uploaded_file($banner['tmp_name'], $filepath.$banner['name'].".tmp");
			@chmod($filepath.$banner['name'].".tmp", 0755);
			$getimg = getimagesize($filepath.$banner['name'].".tmp");
			if($getimg[0] < 89 && $getimg[1] < 32) {
				$pic = '';
				if($getimg[2] == 1) $pic=$id.'.gif';
				elseif($getimg[2] == 2) $pic=$id.'.jpg';
				elseif($getimg[2] == 3) $pic=$id.'.png';
				if($pic != "") {
					if(file_exists($filepath.$id.'.gif')) unlink($filepath.$id.'.gif');
					if(file_exists($filepath.$id.'.jpg')) unlink($filepath.$id.'.jpg');
					if(file_exists($filepath.$id.'.png')) unlink($filepath.$id.'.png');
					rename($filepath.$banner['name'].".tmp", $filepath.$pic);
					safe_query("UPDATE ".PREFIX."partners SET banner='".$pic."' WHERE partnerID='".$id."'");
				}  else {
					if(unlink($filepath.$banner['name'].".tmp")) {
						$error = $_language->module['format_incorrect'];
						die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
					} else {
						$error = $_language->module['format_incorrect'];
						die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
					}
				}
			} else {
				@unlink($filepath.$banner['name'].".tmp");
				$error = $_language->module['banner_to_big'];
				die('<b>'.$error.'</b><br /><br /><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
			}
		}
		safe_query("UPDATE ".PREFIX."partners SET name='$name', url='$url', displayed='".$displayed."' WHERE partnerID='$partnerID' ");
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo'<h1>&curren; <a href="admincenter.php?site=partners" class="white">'.$_language->module['partners'].'</a> &raquo; '.$_language->module['add_partner'].'</h1>';

	echo'<form method="post" action="admincenter.php?site=partners" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['partner_name'].'</b></td>
      <td width="85%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['banner'].'</b></td>
      <td><input name="banner" type="file" size="40" /> <small>'.$_language->module['max_88x31'].'</small></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['homepage_url'].'</b></td>
      <td><input type="text" name="url" size="60" value="http://" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['is_displayed'].'</b></td>
      <td><input type="checkbox" name="displayed" value="1" checked="checked" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_partner'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="edit") {
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  echo'<h1>&curren; <a href="admincenter.php?site=partners" class="white">'.$_language->module['partners'].'</a> &raquo; '.$_language->module['edit_partner'].'</h1>';
  
  $partnerID = $_GET['partnerID'];
  $ergebnis=safe_query("SELECT * FROM ".PREFIX."partners WHERE partnerID='$partnerID'");
  $ds=mysql_fetch_array($ergebnis);
  
  if($ds['displayed']=='1') $displayed='<input type="checkbox" name="displayed" value="1" checked="checked" />';
  else $displayed='<input type="checkbox" name="displayed" value="1" />';
  
	echo'<form method="post" action="admincenter.php?site=partners" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['current_banner'].'</b></td>
      <td width="85%"><img src="../images/partners/'.$ds['banner'].'" alt="" /></td>
    </tr>
    <tr>
      <td width="15%"><b>'.$_language->module['partner_name'].'</b></td>
      <td width="85%"><input type="text" name="name" size="60" value="'.getinput($ds['name']).'" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['banner'].'</b></td>
      <td><input name="banner" type="file" size="40" /> <small>'.$_language->module['max_88x31'].'</small></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['homepage_url'].'</b></td>
      <td><input type="text" name="url" size="60" value="'.getinput($ds['url']).'" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['is_displayed'].'</b></td>
      <td>'.$displayed.'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="partnerID" value="'.$partnerID.'" /></td>
      <td><input type="submit" name="saveedit" value="'.$_language->module['edit_partner'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {
	
  echo'<h1>&curren; '.$_language->module['partners'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=partners&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_partner'].'" /><br /><br />';

	echo'<form method="post" action="admincenter.php?site=partners">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="42%" class="title"><b>'.$_language->module['partners'].'</b></td>
      <td width="15%" class="title"><b>'.$_language->module['clicks'].'</b></td>
      <td width="15%" class="title"><b>'.$_language->module['is_displayed'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
      <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
    </tr>';

	$partners=safe_query("SELECT * FROM ".PREFIX."partners ORDER BY sort");
	$anzpartners=safe_query("SELECT count(partnerID) FROM ".PREFIX."partners");
	$anzpartners=mysql_result($anzpartners, 0);
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	$CAPCLASS->create_transaction();
	$hash_2 = $CAPCLASS->get_hash();
	
	$i=1;
	while($db=mysql_fetch_array($partners)) {
    if($i%2) { $td='td1'; }
    else { $td='td2'; }
    
    $db['displayed']==1 ? $displayed='<font color="green"><b>'.$_language->module['yes'].'</b></font>' : $displayed='<font color="red"><b>'.$_language->module['no'].'</b></font>';
    
    $days=round((time()-$db['date'])/(60*60*24));
    if($days) $perday=round($db['hits']/$days,2);
    else $perday=$db['hits'];
    
		echo'<tr>
      <td class="'.$td.'"><a href="'.getinput($db['url']).'" target="_blank">'.getinput($db['name']).'</a></td>
      <td class="'.$td.'">'.$db['hits'].' ('.$perday.')</td>
      <td class="'.$td.'" align="center">'.$displayed.'</td>
      <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=partners&amp;action=edit&amp;partnerID='.$db['partnerID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
      <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=partners&amp;delete=true&amp;partnerID='.$db['partnerID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
      <td class="'.$td.'" align="center">
      <select name="sort[]">';
      
		for($j=1; $j<=$anzpartners; $j++) {
			if($db['sort'] == $j) echo'<option value="'.$db['partnerID'].'-'.$j.'" selected="selected">'.$j.'</option>';
			else echo'<option value="'.$db['partnerID'].'-'.$j.'">'.$j.'</option>';
		}
    
		echo'</select>
      </td>
    </tr>';
    $i++;
         
	}
	echo'<tr class="td_head">
      <td colspan="5" align="right"><input type="hidden" name="captcha_hash" value="'.$hash_2.'" /><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>