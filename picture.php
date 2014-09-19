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

if(phpversion() < "4.3.0") {
	function file_get_contents($filename)
	{
		$fd = fopen("$filename", "rb");
		$content = fread($fd, filesize($filename));
		fclose($fd);
		return $content;
	}
}

$pictureID = (int)$_GET['id'];

if(file_exists('images/gallery/large/'.$pictureID.'.jpg')) $file='images/gallery/large/'.$pictureID.'.jpg';
elseif(file_exists('images/gallery/large/'.$pictureID.'.gif')) $file='images/gallery/large/'.$pictureID.'.gif';
elseif(file_exists('images/gallery/large/'.$pictureID.'.png')) $file='images/gallery/large/'.$pictureID.'.png';
else $file='';

$info=getimagesize($file);
switch($info[2]) {
	case 1: Header("Content-type: image/gif"); break;
	case 2: Header("Content-type: image/jpeg"); break;
	case 3: Header("Content-type: image/png"); break;
}

echo file_get_contents($file);
?>
