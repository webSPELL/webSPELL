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
include("_functions.php");
$_language->read_module('upload');
if(!isanyadmin($userID)) die($_language->module['no_access']);

if(isset($_GET['cwID'])) {
	$filepath = "images/clanwar-screens/";
	$table = "clanwars";
	$tableid = "cwID";
	$id = $_GET['cwID'];
}
elseif(isset($_GET['newsID'])) {
	$filepath = "images/news-pics/";
	$table = "news";
	$tableid = "newsID";
	$id = $_GET['newsID'];
}
elseif(isset($_GET['articlesID'])) {
	$filepath = "images/articles-pics/";
	$table = "articles";
	$tableid = "articlesID";
	$id = $_GET['articlesID'];
}
else die($_language->module['invalid_access']);

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = null;

if(isset($_POST['submit'])) {
	$screen = $_FILES['screen'];
	if(!empty($screen['name'])) {
		move_uploaded_file($screen['tmp_name'], $filepath.$screen['name']);
		@chmod($filepath.$screen['name'], $new_chmod);
		$file_ext=strtolower(mb_substr($screen['name'], strrpos($screen['name'], ".")));
		$file=$id.'_'.time().$file_ext;
		rename($filepath.$screen['name'], $filepath.$file);
		$ergebnis=safe_query("SELECT screens FROM ".PREFIX."$table WHERE $tableid='$id'");
		$ds=mysql_fetch_array($ergebnis);
		$screens=explode("|", $ds['screens']);
		$screens[]=$file;
		$screens_string=implode("|", $screens);

		safe_query("UPDATE ".PREFIX.$table." SET screens='".$screens_string."' WHERE ".$tableid."='".$id."'");
	}
	header("Location: upload.php?$tableid=$id");
}
elseif($action=="delete") {
	$file = $_GET['file'];
	if(file_exists($filepath.$file)) @unlink($filepath.$file);

	$ergebnis=safe_query("SELECT screens FROM ".PREFIX."$table WHERE $tableid='$id'");
	$ds=mysql_fetch_array($ergebnis);
	$screens=explode("|", $ds['screens']);
	foreach($screens as $pic) {
		if($pic != $file) $newscreens[]=$pic;
	}
	if(is_array($newscreens)) $newscreens_string=implode("|", $newscreens);
	safe_query("UPDATE ".PREFIX."$table SET screens='$newscreens_string' WHERE $tableid='$id'");

	header("Location: upload.php?$tableid=$id");

}
else {

echo'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Clanpage using webSPELL 4 CMS" />
	<meta name="author" content="webspell.org" />
	<meta name="keywords" content="webspell, webspell4, clan, cms" />
	<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
	<meta name="generator" content="webSPELL" />
	<title>'.$_language->module['file_upload'].'</title>
  <script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
	<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
</head>
<body>
<center>
<h2>'.$_language->module['file_upload'].':</h2>
<form method="post" action="upload.php?'.$tableid.'='.$id.'" enctype="multipart/form-data">
<table width="100%" cellpadding="4" cellspacing="1" bgcolor="'.BORDER.'">
  <tr bgcolor="'.BG_1.'">
    <td align="center"><input type="file" name="screen" />
    <input type="submit" name="submit" value="'.$_language->module['upload'].'" />
    <hr />
    <h2>'.$_language->module['existing_files'].':</h2>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">';

	$ergebnis=safe_query("SELECT screens FROM ".PREFIX."$table WHERE $tableid='$id'");

	$ds=mysql_fetch_array($ergebnis);
	$screens = array();
	if(!empty($ds['screens'])) $screens=explode("|", $ds['screens']);
	if(is_array($screens)) {
		foreach($screens as $screen) {
			if($screen!="") {
				
        echo'<tr>
            <td><a href="'.$filepath.$screen.'" target="_blank">'.$screen.'</a></td>
            <td><input type="text" name="pic" size="70" value="&lt;img src=&quot;'.$filepath.$screen.'&quot; border=&quot;0&quot; align=&quot;left&quot; style=&quot;padding:4px;&quot; alt=&quot;&quot; /&gt;" /></td>
            <td><input type="button" onclick="AddCodeFromWindow(\'[img]'.$filepath.$screen.'[/img] \')" value="'.$_language->module['add_to_message'].'" /></td>
            <td><input type="button" onclick="MM_confirm(\''.$_language->module['delete'].'\',\'upload.php?action=delete&amp;'.$tableid.'='.$id.'&amp;file='.$screen.'\')" value="'.$_language->module['delete'].'" /></td>
          </tr>';
			}
		}
	}

	echo'</table>
      </td>
    </tr>
  </table>
  </form>
  <br /><br /><input type="button" onclick="javascript:self.close()" value="'.$_language->module['close_window'].'" />
  </center>
  </body>
  </html>';
}
?>
