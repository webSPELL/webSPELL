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
$_language->read_module('usergallery');
$galclass = new Gallery;

if($userID) {

	if(isset($_POST['save'])) {
		if($_POST['name']) safe_query("INSERT INTO ".PREFIX."gallery ( name, date, userID ) values( '".$_POST['name']."', '".time()."', '".$userID."' ) ");
		else redirect('index.php?site=usergallery&action=add', $_language->module['please_enter_name']);
	}
	elseif(isset($_POST['saveedit'])) {
		safe_query("UPDATE ".PREFIX."gallery SET name='".$_POST['name']."' WHERE galleryID='".$_POST['galleryID']."' AND userID='".$userID."'");
	}
	elseif(isset($_POST['saveform'])) {
		$endung='';
		$dir = 'images/gallery/';
		$picture = $_FILES['picture'];

		if($picture['name'] != "") {

			if($_POST['name']!="") $insertname = $_POST['name'];
			else $insertname = $picture['name'];
			safe_query("INSERT INTO ".PREFIX."gallery_pictures ( galleryID, name, comment, comments) VALUES ('".$_POST['galleryID']."', '".$insertname."', '".$_POST['comment']."', '".$_POST['comments']."' )");

			$typ = getimagesize($picture['tmp_name']);
			$insertid = mysql_insert_id();
			if(is_array($typ)){
				switch ($typ[2]) {
					case 1: $endung = '.gif'; break;
					case 3: $endung = '.png'; break;
					default: $endung = '.jpg'; break;
				}
	
				move_uploaded_file($picture['tmp_name'], $dir.'large/'.$insertid.$endung);
				@chmod($dir.'large/'.$insertid.$endung, $new_chmod);
				$galclass->savethumb($dir.'large/'.$insertid.$endung, $dir.'thumb/'.$insertid.'.jpg');
	
				if( ($galclass->getuserspace($userID)+filesize($dir.'large/'.$insertid.$endung) + filesize($dir.'thumb/'.$insertid.'.jpg')) > $maxusergalleries ) {
					@unlink($dir.'large/'.$insertid.$endung);
					@unlink($dir.'thumb/'.$insertid.'.jpg');
					safe_query("DELETE FROM ".PREFIX."gallery_pictures WHERE picID='".$insertid."'");
					echo '<p style="color:'.$loosecolor.'">'.$_language->module['no_space_left'].'</p>';
				}
			}
			else{
				safe_query("DELETE FROM ".PREFIX."gallery_pictures WHERE picID='".$insertid."'");
			}
		}
	}
	elseif(isset($_GET['delete'])) {
		//SQL
		if(safe_query("DELETE FROM ".PREFIX."gallery WHERE galleryID='".$_GET['galleryID']."' AND userID='".$userID."'")) {
			//FILES
			$ergebnis=safe_query("SELECT picID FROM ".PREFIX."gallery_pictures WHERE galleryID='".$_GET['galleryID']."'");
			while($ds=mysql_fetch_array($ergebnis)) {
				@unlink('images/gallery/thumb/'.$ds['picID'].'.jpg'); //thumbnails
				$path = 'images/gallery/large/';
				if(file_exists($path.$ds['picID'].'.jpg')) $path = $path.$ds['picID'].'.jpg';
				elseif(file_exists($path.$ds['picID'].'.png')) $path = $path.$ds['picID'].'.png';
				else $path = $path.$ds['picID'].'.gif';
				@unlink($path); //large
				safe_query("DELETE FROM ".PREFIX."comments WHERE parentID='".$ds['picID']."' AND type='ga'");
			}
			safe_query("DELETE FROM ".PREFIX."gallery_pictures WHERE galleryID='".$_GET['galleryID']."'");
		}
	}

	eval("\$usergallery_title = \"".gettemplate("title_usergallery")."\";");
	echo $usergallery_title;

	if(isset($_GET['action'])) {
		if($_GET['action'] == "add") {
			
      eval("\$usergallery_add = \"".gettemplate("usergallery_add")."\";");
			echo $usergallery_add;
		}
		elseif($_GET['action'] == "edit") {

			$ergebnis=safe_query("SELECT * FROM ".PREFIX."gallery WHERE galleryID='".$_GET['galleryID']."' AND userID='".$userID."'");
			$ds=mysql_fetch_array($ergebnis);

			$name = getinput($ds['name']);
			$galleryID = $ds['galleryID'];
			eval("\$usergallery_edit = \"".gettemplate("usergallery_edit")."\";");
			echo $usergallery_edit;
		}
		elseif($_GET['action']=="upload") {

			$id=(int)$_GET['galleryID'];

			eval("\$usergallery_upload = \"".gettemplate("usergallery_upload")."\";");
			echo $usergallery_upload;

		}
	}
	else {

		$size = $galclass->getuserspace($userID);
		$percent = percent($size, $maxusergalleries, 0);

		if($percent>95) $color = $loosecolor;
		else $color = $wincolor;

		$bg1=BG_1;
		$bg2=BG_2;
		$pagebg=PAGEBG;
		$border=BORDER;
		$bghead=BGHEAD;
		$bgcat=BGCAT;

		$vars = Array('%spacecolor%', '%used_size%', '%available_size%');
		$repl = Array($color, round($size/(1024*1024),2), round($maxusergalleries/(1024*1024),2));
		$space_max_in_user = str_replace($vars, $repl, $_language->module['x_of_y_mb_in_use']);


		eval("\$usergallery_head = \"".gettemplate("usergallery_head")."\";");
		echo $usergallery_head;

		$ergebnis=safe_query("SELECT * FROM ".PREFIX."gallery WHERE userID='".$userID."'");

		if(mysql_num_rows($ergebnis) == 0) echo '<tr bgcolor="'.$bg1.'"><td colspan="4">'.$_language->module['no_galleries'].'</td></tr>';

		for($i=1;$ds=mysql_fetch_array($ergebnis);$i++) {
			if($i%2) $bg=$bg1;
			else $bg=$bg2;
			$name = clearfromtags($ds['name']);
			$galleryID = $ds['galleryID'];

			eval("\$usergallery = \"".gettemplate("usergallery")."\";");
			echo $usergallery;
		}

		eval("\$usergallery_foot = \"".gettemplate("usergallery_foot")."\";");
		echo $usergallery_foot;
	}

}
else redirect('index.php?site=login', '', 0);
?>