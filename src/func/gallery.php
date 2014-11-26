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

class Gallery {


	function showthumb($picID) {
    
		global $_language;
		$_language->read_module('gallery', true);
		global $thumbwidth,$_language;

		$pic = mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."gallery_pictures WHERE picID='".$picID."'"));
		if($pic['picID']) {
			$pic['gallery'] = str_break(stripslashes($this->getgalleryname($picID)), 45);
			if(file_exists('images/gallery/thumb/'.$picID.'.jpg')) $pic['image'] = '<a href="index.php?site=gallery&amp;picID='.$picID.'"><img src="images/gallery/thumb/'.$picID.'.jpg" border="0" width="'.$thumbwidth.'" alt="" /></a>';
			else $pic['image'] = '<a href="index.php?site=gallery&amp;picID='.$picID.'"><img src="images/nopic.gif" border="0" width="'.$thumbwidth.'" alt="'.$_language->module['no_thumb'].'" /></a>';
			$pic['comments'] = mysql_num_rows(safe_query("SELECT commentID FROM ".PREFIX."comments WHERE parentID='".$picID."' AND type='ga'"));
			$ergebnis = mysql_fetch_array(safe_query("SELECT date FROM ".PREFIX."gallery as gal, ".PREFIX."gallery_pictures as pic WHERE gal.galleryID=pic.galleryID AND pic.picID='".$picID."'"));
			$pic['date']=date("d.m.Y",$ergebnis['date']);
			$pic['groupID']=$this->getgroupid_by_gallery($pic['galleryID']);
			$pic['name']=stripslashes(clearfromtags($pic['name']));

			eval ("\$thumb = \"".gettemplate("gallery_content_showthumb")."\";");

		} else $thumb='<tr><td colspan="2">'.$_language->module['no_picture'].'</td></tr>';
		return $thumb;
	}

	function savethumb($image,$dest) {

		global $picsize_h;
		global $thumbwidth;
		global $new_chmod;

		$max_x = $thumbwidth;
		$max_y = $picsize_h;

		$ext=getimagesize($image);
		switch (strtolower($ext[2])) {
			case '2': $im  = imagecreatefromjpeg ($image);
			break;
			case '1' : $im  = imagecreatefromgif  ($image);
			break;
			case '3' : $im  = imagecreatefrompng  ($image);
			break;
			default    : $stop = true;
			break;
		}

		$result="";
    if (!isset($stop)) {
			$x = imagesx($im);
			$y = imagesy($im);


			if (($max_x/$max_y) < ($x/$y)) {
				$save = imagecreatetruecolor($x/($x/$max_x), $y/($x/$max_x));
			}
			else {
				$save = imagecreatetruecolor($x/($y/$max_y), $y/($y/$max_y));
			}
			imagecopyresampled($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);

			imagejpeg($save, $dest, 80);
			@chmod($dest, $new_chmod);

			imagedestroy($im);
			imagedestroy($save);
			return $result;
		} else return false;
	}

	function randompic($galleryID=0) {

		if($galleryID) $only = "WHERE galleryID='".$galleryID."'";
		else $only = '';
		$anz=mysql_num_rows(safe_query("SELECT picID FROM `".PREFIX."gallery_pictures` $only"));
		$selected = rand(1,$anz);
		$start=$selected-1;
		$pic=mysql_fetch_array(safe_query("SELECT picID FROM ".PREFIX."gallery_pictures $only LIMIT $start,$anz"));

		return $pic['picID'];
	}

	function getgalleryname($picID) {

		$ds=mysql_fetch_array(safe_query("SELECT gal.name FROM ".PREFIX."gallery_pictures as pic, ".PREFIX."gallery as gal WHERE pic.picID='".$picID."' AND gal.galleryID=pic.galleryID"));
		return htmlspecialchars($ds['name']);

	}

	function getgroupname($groupID) {

		$ds=mysql_fetch_array(safe_query("SELECT name FROM ".PREFIX."gallery_groups WHERE groupID='".$groupID."'"));
		return htmlspecialchars($ds['name']);

	}

	function getgroupid_by_gallery($galleryID) {

		$ds=mysql_fetch_array(safe_query("SELECT groupID FROM ".PREFIX."gallery WHERE galleryID='".$galleryID."'"));
		return $ds['groupID'];
	}

	function isgalleryowner($galleryID,$userID) {

		if($userID)	return mysql_num_rows(safe_query("SELECT galleryID FROM ".PREFIX."gallery WHERE userID='".$userID."' AND galleryID='".$galleryID."'"));
		else return false;

	}

	function getgalleryowner($galleryID) {

		$ds = mysql_fetch_array(safe_query("SELECT userID FROM ".PREFIX."gallery WHERE galleryID='".$galleryID."'"));
		return $ds['userID'];

	}

	function getlargefile($picID) {

		if(file_exists('images/gallery/large/'.$picID.'.jpg')) $file='images/gallery/large/'.$picID.'.jpg';
		elseif(file_exists('images/gallery/large/'.$picID.'.gif')) $file='images/gallery/large/'.$picID.'.gif';
		elseif(file_exists('images/gallery/large/'.$picID.'.png')) $file='images/gallery/large/'.$picID.'.png';
		else $file='images/nopic.gif';

		return $file;

	}

	function getuserspace($userID) {

		$size=0;
		$ergebnis=safe_query("SELECT pic.picID FROM ".PREFIX."gallery_pictures as pic, ".PREFIX."gallery as gal WHERE gal.userID='".$userID."' AND gal.galleryID=pic.galleryID");
		while($ds=mysql_fetch_array($ergebnis)) {
			$size = $size + filesize('images/gallery/thumb/'.$ds['picID'].'.jpg') + filesize($this->getlargefile($ds['picID']));
		}
		return $size;
	}

}

?>