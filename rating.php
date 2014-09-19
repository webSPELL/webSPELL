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
$_language->read_module('rating');

if(!$userID) die($_language->module['no_access']);

$table = "0";
$key = "0";

$rating = $_POST['rating'];
settype($rating, "integer");
if($rating > 10 OR $rating < 0) die($_language->module['just_rate_between_0_10']);
$type = $_POST['type'];
$id = $_POST['id'];

if($type == "ar") {
	$table = "articles";
	$key = "articlesID";
}
elseif($type == "de") {
	$table = "demos";
	$key = "demoID";
}
elseif($type == "fi") {
	$table = "files";
	$key = "fileID";
}
elseif($type == "ga") {
	$table = "gallery_pictures";
	$key = "picID";
}


$getarticles = safe_query("SELECT ".$table." FROM ".PREFIX."user WHERE userID='".$userID."'");
if(mysql_num_rows($getarticles)) {
	$ga = mysql_fetch_array($getarticles);
	$go = false;
	if($ga[$table] == ""){
		$array = array();
		$go = true;
	}
	else {
		$string = $ga[$table];
		$array = explode(":", $string);
		if(!in_array($id,$array)) $go = true;
	}
	// Only vote, if isn't voted
	if($go == true){
		safe_query("UPDATE ".PREFIX.$table." SET votes=votes+1, points=points+".$rating." WHERE ".$key."='".$id."'");
		$ergebnis = safe_query("SELECT votes, points FROM ".PREFIX.$table." WHERE ".$key."='".$id."'");
		$ds = mysql_fetch_array($ergebnis);
		$rate = round($ds['points'] / $ds['votes']);
		safe_query("UPDATE ".PREFIX.$table." SET rating='".$rate."' WHERE ".$key."='".$id."'");
		$array[] = $id;
		$string_new = implode(":", $array);
		safe_query("UPDATE ".PREFIX."user SET ".$table."='".$string_new."' WHERE userID='".$userID."'");
	}
}

if($table == "gallery_pictures") $table = "gallery&picID=".$id;
elseif($table == "articles") $table = "articles&action=show&articlesID=".$id;
elseif($table == "demos") $table = "demos&action=showdemo&demoID=".$id;
elseif($table == "files") $table = "files&file=".$id;
header("Location: index.php?site=".$table);
?>