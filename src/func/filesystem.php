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

function detectfilesize($size, $round='2') {
	$filesize=$size;
	for($i=0;$filesize>=1024;$i++) {
		$filesize = $filesize/1024;
	}
	$filesize = round($filesize,$round);
	switch($i) {
		case 0: $filesize = $filesize." Byte"; break;
		case 1: $filesize = $filesize." kB"; break;
		case 2: $filesize = $filesize." MB"; break;
		case 3: $filesize = $filesize." GB"; break;
		case 4: $filesize = $filesize." TB"; break;
		default: $filesize = $size." Byte"; break;
	}
	return $filesize;
}

function getdirsize($dir) {

	$size=0;
	$handle = opendir($dir);
	while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
			if(is_dir($dir.$file)) $size = $size + getdirsize($dir.$file.'/');
			else $size=$size+filesize($dir.$file);
		}
	}
	return $size;
}

function rm_recursive($filepath){
	if (is_dir($filepath) && !is_link($filepath)){
	    if ($dh = @opendir($filepath)){
	        while (($sf = readdir($dh)) !== false){
	            if ($sf == '.' || $sf == '..'){
	                continue;
	            }
	            if (!rm_recursive($filepath.'/'.$sf)){
	                return false;
	            }
	        }
	        closedir($dh);
	    }
	    return @rmdir($filepath);
	}
	return @unlink($filepath);
}

?>