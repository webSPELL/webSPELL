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

include("_mysql.php");
include("_settings.php");

echo'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Clanpage using webSPELL 4 CMS" />
	<meta name="author" content="webspell.org" />
	<meta name="keywords" content="webspell, webspell4, clan, cms" />
	<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
	<meta name="generator" content="webSPELL" />
	<title>Smilies</title>
	<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
  <script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
</head>

<body bgcolor="'.PAGEBG.'">
<table width="300" cellpadding="2" cellspacing="1" bgcolor="'.BORDER.'">
  <tr bgcolor="'.BGHEAD.'">
    <td class="title" align="center">Smiley:</td>
    <td class="title" align="center">Tag:</td>
  </tr>
  <tr><td colspan="2" bgcolor="'.BG_1.'"></td></tr>';


$filepath = "./images/smileys/";
unset($files);
if ($dh = opendir($filepath)) {
	while($file = readdir($dh)) {
		if (preg_match("/\.gif/si",$file)) $files[] = $file;
	}
	closedir($dh);
}

if (is_array($files)) {
	sort($files);
	foreach($files as $file) {
		$smiley = explode(".", $file);
		
    echo'<tr>
        <td bgcolor="'.BG_1.'" align="center"><a href="javascript:AddCodeFromWindow(\':'.$smiley[0].':\')"><img src="images/smileys/'.$file.'" border="0" alt="" /></a></td>
        <td bgcolor="'.BG_2.'" align="center"><a href="javascript:AddCodeFromWindow(\':'.$smiley[0].':\')">:'.$smiley[0].':</a></td>
      </tr>';
	}
	echo '</table>';
}

?>
</body>
</html>
