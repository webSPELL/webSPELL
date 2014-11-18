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

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = "";

if(isset($_POST['save'])) {
	$_language->read_module('linkus');
	if(!ispageadmin($userID)) die($_language->module['no_access']);

	safe_query("INSERT INTO ".PREFIX."linkus ( name ) VALUES( '".$_POST['name']."' ) ");
	$id=mysqli_insert_id($_database);
	$banner = $_FILES['banner'];
	$filepath = "./images/linkus/";
	
	if($banner['name'] != "") {
		move_uploaded_file($banner['tmp_name'], $filepath.$banner['name'].".tmp");
		@chmod($filepath.$banner['name'].".tmp", 0755);
		$getimg = getimagesize($filepath.$banner['name'].".tmp");
		if($getimg[0] < 801 && $getimg[1] < 601) {
			$file = '';
			if($getimg[2] == 1) $file=$id.'.gif';
			elseif($getimg[2] == 2) $file=$id.'.jpg';
			elseif($getimg[2] == 3) $file=$id.'.png';
			if($file != "") {
				if(file_exists($filepath.$id.'.gif')) unlink($filepath.$id.'.gif');
				if(file_exists($filepath.$id.'.jpg')) unlink($filepath.$id.'.jpg');
				if(file_exists($filepath.$id.'.png')) unlink($filepath.$id.'.png');
				rename($filepath.$banner['name'].".tmp", $filepath.$file);
				safe_query("UPDATE ".PREFIX."linkus SET file='".$file."' WHERE bannerID='".$id."'");
			}  else {
				if(unlink($filepath.$banner['name'].".tmp")) {
					$error = $_language->module['format_incorrect'];
					die('<b>'.$error.'</b><br><br><a href="index.php?site=linkus&amp;action=edit&amp;bannerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
				} else {
					$error = $_language->module['format_incorrect'];
					die('<b>'.$error.'</b><br><br><a href="index.php?site=linkus&amp;action=edit&amp;bannerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
				}
			}
		} else {
			@unlink($filepath.$banner['name'].".tmp");
			$error = $_language->module['banner_to_big'];
			die('<b>'.$error.'</b><br><br><a href="index.php?site=linkus&amp;action=edit&amp;bannerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
		}
	}
}
elseif(isset($_POST['saveedit'])) {
	$_language->read_module('linkus');
	if(!ispageadmin($userID)) die($_language->module['no_access']);

	safe_query("UPDATE ".PREFIX."linkus SET name='".$_POST['name']."' WHERE bannerID='".$_POST['bannerID']."'");

	$filepath = "./images/linkus/";
	$id=$_POST['bannerID'];
	$banner = $_FILES['banner'];
	
	if($banner['name'] != "") {
		move_uploaded_file($banner['tmp_name'], $filepath.$banner['name'].".tmp");
		@chmod($filepath.$banner['name'].".tmp", 0755);
		$getimg = getimagesize($filepath.$banner['name'].".tmp");
		if($getimg[0] < 801 && $getimg[1] < 601) {
			$file = '';
			if($getimg[2] == 1) $file=$id.'.gif';
			elseif($getimg[2] == 2) $file=$id.'.jpg';
			elseif($getimg[2] == 3) $file=$id.'.png';
			if($file != "") {
				if(file_exists($filepath.$id.'.gif')) unlink($filepath.$id.'.gif');
				if(file_exists($filepath.$id.'.jpg')) unlink($filepath.$id.'.jpg');
				if(file_exists($filepath.$id.'.png')) unlink($filepath.$id.'.png');
				rename($filepath.$banner['name'].".tmp", $filepath.$file);
				safe_query("UPDATE ".PREFIX."linkus SET file='".$file."' WHERE bannerID='".$id."'");
			}  else {
				if(unlink($filepath.$banner['name'].".tmp")) {
					$error = $_language->module['format_incorrect'];
					die('<b>'.$error.'</b><br><br><a href="index.php?site=linkus&amp;action=edit&amp;bannerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
				} else {
					$error = $_language->module['format_incorrect'];
					die('<b>'.$error.'</b><br><br><a href="index.php?site=linkus&amp;action=edit&amp;bannerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
				}
			}
		} else {
			@unlink($filepath.$banner['name'].".tmp");
			$error = $_language->module['banner_to_big'];
			die('<b>'.$error.'</b><br><br><a href="index.php?site=linkus&amp;action=edit&amp;bannerID='.$id.'">&laquo; '.$_language->module['back'].'</a>');
		}
	}
}
elseif(isset($_GET['delete'])) {
	include("_mysql.php");
	include("_settings.php");
	include('_functions.php');
	$_language->read_module('linkus');
	if(!ispageadmin($userID)) die($_language->module['no_access']);

	$bannerID = $_GET['bannerID'];
	$filepath = "./images/linkus/";
	safe_query("DELETE FROM ".PREFIX."linkus WHERE bannerID='".$bannerID."'");
	if(file_exists($filepath.$bannerID.'.gif')) @unlink($filepath.$bannerID.'.gif');
	if(file_exists($filepath.$bannerID.'.jpg')) @unlink($filepath.$bannerID.'.jpg');
	if(file_exists($filepath.$bannerID.'.png')) @unlink($filepath.$bannerID.'.png');
	header("Location: index.php?site=linkus");
}

$_language->read_module('linkus');

eval ("\$title_linkus = \"".gettemplate("title_linkus")."\";");
echo $title_linkus;

if($action=="new") {
	if(ispageadmin($userID)) {
		$bg1=BG_1;
		eval ("\$linkus_new = \"".gettemplate("linkus_new")."\";");
		echo $linkus_new;
	}
	else redirect('index.php?site=linkus', $_language->module['no_access']);
}
elseif($action=="edit") {
	if(ispageadmin($userID)) {
		$bannerID = $_GET['bannerID'];
		$ds=mysqli_fetch_array(safe_query("SELECT * FROM ".PREFIX."linkus WHERE bannerID='".$bannerID."'"));
		$name=getinput($ds['name']);
		$banner='<img src="images/linkus/'.$ds['file'].'" alt="">';

		$bg1=BG_1;
		eval ("\$linkus_edit = \"".gettemplate("linkus_edit")."\";");
		echo $linkus_edit;
	}
	else redirect('index.php?site=linkus', $_language->module['no_access']);
}
else {
	$filepath = "./images/linkus/";
	$filepath2 = "/images/linkus/";
	if(ispageadmin($userID)) echo'<div class="form-group"><input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=linkus&amp;action=new\');return document.MM_returnValue" value="'.$_language->module['new_banner'].'" class="btn btn-danger"></div>';
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."linkus ORDER BY name");
	if(mysqli_num_rows($ergebnis)) {
		$i=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			if($i%2) $bg1=BG_1;
			else $bg1=BG_2;

			$name=htmloutput($ds['name']);
			$fileinfo=getimagesize($filepath.$ds['file']);
			if($fileinfo[0]>$picsize_l) $width=' width="'.$picsize_l.'"';
			else $width='';
			if($fileinfo[1]>$picsize_h) $height=' height="'.$picsize_h.'"';
			else $height='';
			$banner='<img src="'.$filepath.$ds['file'].'" class="img-responsive">';
			$code = '&lt;a href=&quot;http://'.$hp_url.'&quot;&gt;&lt;img src=&quot;http://'.$hp_url.$filepath2.$ds['file'].'&quot; alt=&quot;'.$myclanname.'&quot;&gt;&lt;/a&gt;';

			$adminaction='';
			if(ispageadmin($userID)){
				$adminaction='<p class="form-group pull-right"><input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=linkus&amp;action=edit&amp;bannerID='.$ds['bannerID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" class="btn btn-danger">
			<input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_banner'].'\', \'linkus.php?delete=true&amp;bannerID='.$ds['bannerID'].'\')" value="'.$_language->module['delete'].'" class="btn btn-danger"></p>';
			}

			eval("\$linkus = \"".gettemplate("linkus")."\";");
			echo $linkus;
			$i++;
		}
	}
	else echo '<p>'.$_language->module['no_banner'].'</p>';
}


?>
