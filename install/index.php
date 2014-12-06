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
#   Copyright 2005-2014 by webspell.org                                  #
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

session_name("ws_session");
session_start();
header('content-type: text/html; charset=utf-8');
include("../src/func/language.php");
include("../src/func/user.php");

$_language = new webspell\Language();

if(!isset($_SESSION['language'])){
	$_SESSION['language'] = "uk";
}

if(isset($_GET['lang'])){
	if($_language->setLanguage($_GET['lang'])) $_SESSION['language'] = $_GET['lang'];
	header("Location: index.php");
	exit();
}

$_language->setLanguage($_SESSION['language']);
$_language->readModule('index');

if(isset($_GET['step'])) $_language->readModule('step'.(int)$_GET['step'],true);
else $_language->readModule('step0',true);

if(!isset($_GET['step'])){
	$_GET['step'] = "";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" content="Clanpage using webSPELL 4 CMS">
<meta name="author" content="webspell.org">
<meta name="keywords" content="webspell, webspell4, clan, cms">
<meta name="copyright" content="Copyright 2005-2014 by webspell.org">
<meta name="generator" content="webSPELL">
<title>webSPELL Installation</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="776" align="center">
  <tr>
   <td id="head" colspan="2">
   <div id="links">
    <ul>
      <li><a href="http://www.webspell.org/index.php?site=support" target="_blank" class="link1"></a></li>
      <li><a href="http://www.webspell.org/index.php?site=license" target="_blank" class="link2"></a></li>
      <li><a href="http://www.webspell.org/index.php?site=about" target="_blank" class="link3"></a></li>
    </ul>
   </div>
   </td>
  </tr>
   <?php
   echo '<tr><td colspan="2"><form action="index.php?step='.($_GET['step']+1).'" method="post" name="ws_install"></td></tr>';
   include('step0'.$_GET['step'].'.php');
   ?>
  <tr>
   <td style="background-image:url(images/4.jpg);" height="25" colspan="2"></td>
  </tr>
</table>
</body>
</html>
