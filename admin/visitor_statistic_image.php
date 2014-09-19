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

include("../_mysql.php");
include("../_settings.php");
include("../_functions.php");

$_language->read_module('visitor_statistic_image');

$admin=isanyadmin($userID);
if(!$loggedin) die($_language->module['not_logged_in']);
if(!$admin) die($_language->module['access_denied']);

header("Content-type: image/png");

$offset_left = 25;
$offset_right = 80;
$offset_top = 25;
$offset_bottom = 35;

if(isset($_GET['size_x'])) {
	$size_x = (int)$_GET['size_x'];
	if($size_x <= 0) $size_x = 1;
	$_SESSION['size_x'] = $size_x;
}
elseif(isset($_SESSION['size_x'])) {
	$size_x = $_SESSION['size_x'];
}
else {
	$size_x = 650;
}

if(isset($_GET['size_y'])) {
	$size_y = (int)$_GET['size_y'];
	if($size_y <= 0) $size_y = 1;
	$_SESSION['size_y'] = $size_y;
}
elseif(isset($_SESSION['size_y'])) {
	$size_y = $_SESSION['size_y'];
}
else {
	$size_y = 200;
}

$im = imagecreatetruecolor($size_x + $offset_left + $offset_right, $size_y + $offset_top + $offset_bottom);

$weiss = imagecolorallocate($im, 255, 255, 255);
$grau = imagecolorallocate($im, 200, 200, 200);
$schwarz = imagecolorallocate($im, 0, 0, 0);
$linie = imagecolorallocate($im, 0, 40, 80);
$gelb = imagecolorallocate($im, 255, 0, 0);

imagefill($im, 0, 0, $weiss);

$linie_still = array($grau, $grau, $weiss, $weiss);
imagesetstyle($im, $linie_still);

function teilen(&$item, $key, $teiler) {
	$item = round($item / $teiler);
}

function multiplizieren(&$item, $key, $faktor) {
	$item = round($item * $faktor);
}

function hinzufuegen(&$item, $key, $faktor) {
	$item = $item + $faktor;
}

$array = array();
if(isset($_GET['month']))
{
	$datemonth = date(".m.Y", mktime(0, 0, 0, $_GET['month'] ? $_GET['month'] : date("n"), 1, $_GET['year'] ? $_GET['year'] : date("Y")));
	for($i = 1; $i <= date("d", mktime(0, 0, 0, $_GET['month'] ? $_GET['month'] + 1 : date("n") + 1, 0, $_GET['year'] ? $_GET['year'] : date("Y"))); $i++)
	{
		$tmp = mysql_fetch_array(safe_query("SELECT count FROM ".PREFIX."counter_stats WHERE dates LIKE '%".$i.$datemonth."'"));
		$array[] = $tmp['count'] ? $tmp['count'] : 0;
	}
}
elseif(isset($_GET['year']))
{
	for($i = 1; $i < 13; $i++)
	{
		$datemonth = date(".m.Y", mktime(0, 0, 0, $i, 1, $_GET['year'] ? $_GET['year'] : date("Y")));
		$month = 0;
		$monthquery = safe_query("SELECT count FROM ".PREFIX."counter_stats WHERE dates LIKE '%".$datemonth."'");
		while($dm = mysql_fetch_array($monthquery)) {
			$month += $dm['count'];
		}
		$array[] = $month;
	}
}
elseif(isset($_GET['last']))
{
	if($_GET['last'] == "days")
	{
		if(isset($_GET['count'])) {
			$count = (int)$_GET['count'];
			if($count <= 1) $count = 2;
			$_SESSION['count_days'] = $count;
		}
		elseif(isset($_SESSION['count_days'])) {
			$count = $_SESSION['count_days'];
		}
		else {
			$count = 30;
		}
		for($i = $count; $i > 0; $i--)
		{
			$day = date("d.m.Y", mktime(0, 0, 0, date("m"), date("d") - $i, date("Y")));
			$tmp = mysql_fetch_array(safe_query("SELECT count FROM ".PREFIX."counter_stats WHERE dates LIKE '%".$day."'"));
			$array[] = $tmp['count'] ? $tmp['count'] : 0;
		}
	}
	elseif($_GET['last'] == "months")
	{
		if(isset($_GET['count'])) {
			$count = (int)$_GET['count'];
			if($count <= 1) $count = 2;
			$_SESSION['count_months'] = $count;
		}
		elseif(isset($_SESSION['count_months'])) {
			$count = $_SESSION['count_months'];
		}
		else {
			$count = 12;
		}
		for($i = $count; $i > 0; $i--)
		{
			$month = 0;
			$monthquery = safe_query("SELECT count FROM ".PREFIX."counter_stats WHERE dates LIKE '%".date(".m.Y", mktime(0, 0, 0, date("m") - $i, 1, date("Y")))."'");
			while($dm = mysql_fetch_array($monthquery)) {
				$month += $dm['count'];
			}
			$array[] = $month;
		}
	}
}
else {
	for($i = 1; $i < 100; $i++) {
		$array[] = rand(0, 50050);
	}
}

asort($array);
$max_y_original = end($array);
$min_y_original = min($array);
$max_y_key = key($array);
$stellen = mb_strlen($max_y_original);
if($stellen == 1) $stellen++;
$max_y = $max_y_original + (pow(10, $stellen - 1) - ($max_y_original % pow(10, $stellen - 1)));

if($max_y >= $size_y) {
	$teiler = $max_y / $size_y;
	array_walk($array, 'teilen', $teiler);
}
else {
	if($max_y != 0) {
		$faktor = $size_y / $max_y;
	}
	else {
		$faktor = $size_y;
	}
	array_walk($array, 'multiplizieren', $faktor);
}
ksort($array);

$max_x = $size_x / (count($array) - 1);

for($i = 0; $i <= $size_y; $i += $size_y / 4) {
	imageline($im, $offset_left, $i + $offset_top, $size_x + $offset_left, $i + $offset_top, IMG_COLOR_STYLED);
}
for($i = 0; $i < $size_x - 1; $i += $max_x) {
	imageline($im, $i + $offset_left, $offset_top, $i + $offset_left, $size_y + $offset_top, IMG_COLOR_STYLED);
}
imageline($im, $offset_left, $offset_top, $size_x + $offset_left, $offset_top, $grau);
imageline($im, $offset_left, $offset_top + $size_y, $size_x + $offset_left, $offset_top + $size_y, $grau);

imageline($im, $offset_left, $offset_top, $offset_left, $size_y + $offset_top, $grau);
imageline($im, $offset_left + $size_x, $offset_top, $offset_left + $size_x, $size_y + $offset_top, $grau);

$nr = $offset_left;
foreach($array as $i => $int) {
	$y1 = (($int - $size_y) * (-1));
	if(isset($array[($i + 1)])) $y2 = ($array[($i + 1)] - $size_y) * (-1);
	else $y2 = -1;

	if($y2 != -1) imageline($im, $nr, $y1 + $offset_top, ($nr + $max_x), $y2 + $offset_top, $linie);

	if($i == $max_y_key) imagefilledrectangle($im, $nr - 2, $offset_top + $y1 - 2, $nr + 2, $offset_top + $y1 + 2, $gelb);

	if(isset($_GET['last']) and $_GET['last'] == "days") {
		imagestring($im, 2, $nr - 5, $offset_top + $size_y + 10, date("d", mktime(0, 0, 0, date("m"), date("d") - $count + $i, date("Y"))), $schwarz);
	}
	elseif(isset($_GET['last']) and $_GET['last'] == "months") {
		imagestring($im, 2, $nr - 5, $offset_top + $size_y + 10, date("m", mktime(0, 0, 0, date("m") - $count + $i, 1, date("Y"))), $schwarz);
	}
	else {
		imagestring($im, 2, $nr - 5, $offset_top + $size_y + 10, $i + 1, $schwarz);
	}

	$nr += $max_x;
}

$max_y_graph = $max_y;

//imageline($im, $offset_left + $size_x - 5, $offset_top, $offset_left + $size_x + 5, $offset_top, $linie);
imagestring($im, 2, $offset_left + $size_x + 15, $offset_top - 7, $max_y_graph, $schwarz);

//imageline($im, $offset_left + $size_x - 5, $offset_top + ($size_y / 4), $offset_left + $size_x + 5, $offset_top + ($size_y / 4), $linie);
imagestring($im, 2, $offset_left + $size_x + 15, $offset_top + ($size_y / 4) - 7, $max_y_graph / 4 * 3, $schwarz);

//imageline($im, $offset_left + $size_x - 5, $offset_top + ($size_y / 2), $offset_left + $size_x + 5, $offset_top + ($size_y / 2), $linie);
imagestring($im, 2, $offset_left + $size_x + 15, $offset_top + ($size_y / 2) - 7, $max_y_graph / 2, $schwarz);

//imageline($im, $offset_left + $size_x - 5, $offset_top + ($size_y / 4 * 3), $offset_left + $size_x + 5, $offset_top + ($size_y / 4 * 3), $linie);
imagestring($im, 2, $offset_left + $size_x + 15, $offset_top + ($size_y / 4 * 3) - 7, $max_y_graph / 4, $schwarz);

//imageline($im, $offset_left + $size_x - 5, $offset_top + $size_y, $offset_left + $size_x + 5, $offset_top + $size_y, $linie);
imagestring($im, 2, $offset_left + $size_x + 15, $offset_top + $size_y - 7, 0, $schwarz);

imagestring($im, 2, $offset_left, $offset_top - 15, $_language->module['max'].": ".$max_y_original." - ".$_language->module['min'].": ".$min_y_original, $schwarz);

imagepng($im);

?>