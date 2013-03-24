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

// -- LOGIN SESSION -- //

systeminc('session');
systeminc('ip');

// -- INSTALL CHECK -- //

if(DEBUG=="OFF") if(file_exists('install/index.php')) system_error('Please remove the install-folder first.',0);

// -- GLOBAL WEBSPELL FUNCTIONS -- //

if(!function_exists('file_get_contents')) {
	function file_get_contents($filename) {
		$fd = fopen("$filename", "rb");
		$content = fread($fd, filesize($filename));
		fclose($fd);
		return $content;
	}
}

if(!function_exists('str_split')) {
	function str_split($str,$split_length=1) {

		$cnt = mb_strlen($str);

		for ($i=0;$i<$cnt;$i+=$split_length)
		$result[]= mb_substr($str,$i,$split_length);

		return $result;
	}
}

if(!function_exists('str_ireplace')) {
	function str_ireplace($search,$replace,$subject) {
		$search = preg_quote($search, "/");
		return preg_replace("/".$search."/i", $replace, $subject);
	}
}

function gettemplate($template,$endung="html", $calledfrom="root") {
	$templatefolder = "templates";
	if($calledfrom=='root') {
		return str_replace("\"", "\\\"", $GLOBALS['_language']->replace(file_get_contents($templatefolder."/".$template.".".$endung)));
	}
	elseif($calledfrom=='admin') {
		return str_replace("\"", "\\\"", $GLOBALS['_language']->replace(file_get_contents("../".$templatefolder."/".$template.".".$endung)));
	}
}

function makepagelink($link, $page, $pages, $sub='') {
	$page_link = '<span class="pagelink"><img src="images/icons/multipage.gif" width="10" height="12" alt="" /> <small>';

	if($page != 1) $page_link .= '&nbsp;<a href="'.$link.'&amp;'.$sub.'page=1">&laquo;</a>&nbsp;<a href="'.$link.'&amp;'.$sub.'page='.($page-1).'">&lsaquo;</a>';
	if($page >= 6) $page_link .= '&nbsp;<a href="'.$link.'&amp;'.$sub.'page='.($page-5).'">...</a>';
	if($page+4 >= $pages) $pagex=$pages;
	else $pagex = $page+4;
	for($i=$page-4 ; $i<=$pagex ; $i++) {
		if($i<=0) $i=1;
		if($i==$page) $page_link .= '&nbsp;<b><u>'.$i.'</u></b>';
		else $page_link .= '&nbsp;<a href="'.$link.'&amp;'.$sub.'page='.$i.'">'.$i.'</a>';
	}
	if(($pages-$page) >= 5) $page_link .= '&nbsp;<a href="'.$link.'&amp;'.$sub.'page='.($page+5).'">...</a>';
	if($page != $pages) $page_link .= '&nbsp;<a href="'.$link.'&amp;'.$sub.'page='.($page+1).'">&rsaquo;</a>&nbsp;<a href="'.$link.'&amp;'.$sub.'page='.$pages.'">&raquo;</a>';
	$page_link .= '</small></span>';

	return $page_link;
}

function str_break($str, $maxlen) {
	$nobr = 0;
	$str_br = '';
	$len = mb_strlen($str);
	for ($i = 0; $i<$len; $i++) {
		$char = mb_substr($str,$i,1);
		if (($char!=' ') && ($char!='-') && ($char!="\n")) $nobr++;
		else {
			$nobr = 0; 
			if($maxlen+$i>$len) {
				$str_br .= mb_substr($str, $i);
				break;
			}
		}
		if ($nobr>$maxlen) {
			$str_br .= '- '.$char;
			$nobr = 1;
		}
		else $str_br .= $char;
	}
	return $str_br;
}

function substri_count_array($haystack, $needle) {
	$return = 0;
	foreach($haystack as $value) {
		if(is_array($value)) {
			$return += substri_count_array($value, $needle);
		}
		else {
			$return += substr_count(strtolower($value), strtolower($needle));
		}
	}
	return $return;
}

function js_replace($string){
	$output=preg_replace("/(\\\)/si", '\\\\\1', $string);
	$output=str_replace(array("\r\n", "\n", "'", "<script>", "</script>", "<noscript>", "</noscript>"), array("\\n", "\\n", "\'", "\\x3Cscript\\x3E", "\\x3C/script\\x3E", "\\x3Cnoscript\\x3E", "\\x3C/noscript\\x3E"), $output);
	return $output;
}

function percent($sub, $total, $dec) {
	if ($sub) {
		$perc = $sub / $total * 100;
		$perc = round($perc, $dec);
		return $perc;
	}
	else return 0;
}

function showlock($reason, $time) {
	$gettitle = mysql_fetch_array(safe_query("SELECT title FROM ".PREFIX."styles"));
	$pagetitle = $gettitle['title'];
	eval ("\$lock = \"".gettemplate("lock")."\";");
	die($lock);
}

function checkenv($systemvar,$checkfor) {
	return stristr(ini_get($systemvar),$checkfor);
}

function createkey($length) {
	$key='';
	for($i=0;$i<$length;$i++) {
		switch(rand(1,3)) {
			case 1:
				$key.=chr(rand(48,57));
				break;
			case 2:
				$key.=chr(rand(65,90));
				break;
			case 3:
				$key.=chr(rand(97,122));
				break;
		}
	}
	return md5($key);
}

function mail_protect($mailaddress) {
	$protected_mail = "";
	$arr = unpack("C*", $mailaddress);
	foreach ($arr as $entry) {
		$protected_mail .= sprintf("%%%X", $entry);
	}
	return $protected_mail;
}

function validate_url($url) {
	return preg_match("/^(ht|f)tps?:\/\/([^:@]+:[^:@]+@)?(?!\.)(\.?(?!-)[0-9\p{L}-]+(?<!-))+(:[0-9]{2,5})?(\/[^#\?]*(\?[^#\?]*)?(#.*)?)?$/sui", $url);
}
function validate_email($email){
	return preg_match("/^(?!\.)(\.?[\p{L}0-9!#\$%&'\*\+\/=\?^_`\{\|}~-]+)+@(?!\.)(\.?(?!-)[0-9\p{L}-]+(?<!-))+\.[\p{L}0-9]{2,}$/sui",$email);
}
if(!function_exists('array_combine')){
	function array_combine($keyarray, $valuearray){
		$keys=array();
		$values=array();
		$result=array();
		foreach($keyarray AS $key){
			$keys[]=$key;
		}
		foreach($valuearray AS $value){
			$values[]=$value;
		}
		foreach($keys AS $access => $resultkey){
			$result[$resultkey]=$values[$access];
		}
		return $result;
	}
}

/* counts empty variables in an array */

function countempty($checkarray) {
	
	$ret = 0;
		
	foreach($checkarray as $value) {
		if(is_array($value)) $ret += countempty($value);
		elseif(trim($value) == "") $ret++;
	}
		
	return $ret;
}

/* checks, if given request-variables are empty */

function checkforempty($valuearray) {

	$check = Array();
	foreach($valuearray as $value) {
		$check[] = $_REQUEST[$value];
	}

	if(countempty($check) > 0) return false;
	return true;

}

// -- FILESYSTEM -- //

systeminc('func/filesystem');

// -- USER INFORMATION -- //

systeminc('func/user');

// -- ACCESS INFORMATION -- //

systeminc('func/useraccess');

// -- MESSENGER INFORMATION -- //

systeminc('func/messenger');

// -- NEWS INFORMATION -- //

systeminc('func/news');

// -- GAME INFORMATION -- //

systeminc('func/game');

// -- BOARD INFORMATION -- //

systeminc('func/board');

// -- CAPTCHA -- //

systeminc('func/captcha');

// -- LANGUAGE SYSTEM -- //

systeminc('func/language');
$_language = new Language;
$_language->set_language($default_language);

// -- GALLERY -- //

systeminc('func/gallery');

// -- SPAM -- //

systeminc('func/spam');

// -- BB CODE -- //

systeminc('func/bbcode');

function cleartext($text, $bbcode=true, $calledfrom='root') {
	$text = htmlspecialchars($text);
	$text = strip_tags($text);
	$text = smileys($text,1,$calledfrom);
	$text = insertlinks($text,$calledfrom);
	$text = flags($text,$calledfrom);
	$text = replacement($text, $bbcode);
	$text = htmlnl($text);
	$text = nl2br($text);

	return $text;
}

function htmloutput($text) {
	$text = smileys($text);
	$text = insertlinks($text);
	$text = flags($text);
	$text = replacement($text);
	$text = htmlnl($text);
	$text = nl2br($text);

	return $text;
}

function clearfromtags($text) {
	$text = getinput($text);
	$text = strip_tags($text);
	$text = htmlnl($text);
	$text = nl2br($text);

	return $text;
}

function getinput($text) {
	//$text = stripslashes($text);
	$text = htmlspecialchars($text);

	return $text;
}

function getforminput($text) {
	$text = str_replace(array('\r','\n'),array("\r","\n"),$text);
	$text = stripslashes($text);
	$text = htmlspecialchars($text);

	return $text;
}

// -- LOGIN -- //

$login_per_cookie = false;
if(isset($_COOKIE['ws_auth']) AND !isset($_SESSION['ws_auth'])) {
	$login_per_cookie = true;
	$_SESSION['ws_auth'] = $_COOKIE['ws_auth'];
}

systeminc('login');

if($loggedin == false) {
	if(isset($_COOKIE['language'])) {
	 	$_language->set_language($_COOKIE['language']);
	}
	elseif( isset($_SESSION['language']) ) {
		$_language->set_language($_SESSION['language']);
	}
}

if($login_per_cookie) {
	$ll=mysql_fetch_array(safe_query("SELECT lastlogin FROM ".PREFIX."user WHERE userID='$userID'"));
	$_SESSION['ws_lastlogin'] = $ll['lastlogin'];
}

// -- SITE VARIABLE -- //

if(isset($_GET['site'])) $site = $_GET['site'];
else $site = '';
if($closed AND !isanyadmin($userID)) {
	$dl=mysql_fetch_array(safe_query("SELECT * FROM `".PREFIX."lock` LIMIT 0,1"));
	$reason = $dl['reason'];
	$time = $dl['time'];
	showlock($reason, $time);
}
if(!isset($_SERVER['HTTP_REFERER'])) {
	$_SERVER['HTTP_REFERER'] = "";
}

// -- BANNED USERS -- //
if(date("dh",$lastBanCheck) != date("dh")){
	$get = safe_query("SELECT userID, banned FROM ".PREFIX."user WHERE banned IS NOT NULL");
	$removeBan = array();
	while($ds = mysql_fetch_assoc($get)){
		if($ds['banned'] != "perm"){
			if($ds['banned'] <= time()){
				$removeBan[] = 'userID="'.$ds['userID'].'"';
			}
		}
	}
	if(!empty($removeBan)){
		$where = implode(" OR ",$removeBan);
		safe_query("UPDATE ".PREFIX."user SET banned=NULL WHERE ".$where);
	}
	safe_query("UPDATE ".PREFIX."settings SET bancheck='".time()."'");
}

$banned=safe_query("SELECT userID, banned, ban_reason FROM ".PREFIX."user WHERE (userID='".$userID."' OR ip='".$GLOBALS['ip']."') AND banned IS NOT NULL");
while($bq=mysql_fetch_array($banned)) {
	if($bq['ban_reason']) $reason = "<br />".$bq['ban_reason'];
	else $reason = '';
	if($bq['banned']) system_error('You have been banished.'.$reason,0);
}

// -- BANNED IPs -- //

safe_query("DELETE FROM ".PREFIX."banned_ips WHERE deltime < ".time()."");

// -- WHO IS - WAS ONLINE -- //

$timeout=5; // 1 second
$deltime = time()-($timeout*60); // IS 1m
$wasdeltime = time()-(60*60*24); // WAS 24h

safe_query("DELETE FROM ".PREFIX."whoisonline WHERE time < '".$deltime."'");  // IS online
safe_query("DELETE FROM ".PREFIX."whowasonline WHERE time < '".$wasdeltime."'");  // WAS online

// -- HELP MODE -- //

systeminc('help');

// -- WHOISONLINE -- //

if(mb_strlen($site)) {
	if($userID) {
		// IS online
		if(mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."whoisonline WHERE userID='$userID'"))) {
			safe_query("UPDATE ".PREFIX."whoisonline SET time='".time()."', site='$site' WHERE userID='$userID'");
			safe_query("UPDATE ".PREFIX."user SET lastlogin='".time()."' WHERE userID='$userID'");
		}
		else safe_query("INSERT INTO ".PREFIX."whoisonline (time, userID, site) VALUES ('".time()."', '$userID', '$site')");
	
		// WAS online
		if(mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."whowasonline WHERE userID='$userID'")))
		safe_query("UPDATE ".PREFIX."whowasonline SET time='".time()."', site='$site' WHERE userID='$userID'");
		else safe_query("INSERT INTO ".PREFIX."whowasonline (time, userID, site) VALUES ('".time()."', '$userID', '$site')");
	}
	else {
		$anz = mysql_num_rows(safe_query("SELECT ip FROM ".PREFIX."whoisonline WHERE ip='".$GLOBALS['ip']."'"));
		if($anz) safe_query("UPDATE ".PREFIX."whoisonline SET time='".time()."', site='$site' WHERE ip='".$GLOBALS['ip']."'");
		else safe_query("INSERT INTO ".PREFIX."whoisonline (time, ip, site) VALUES ('".time()."','".$GLOBALS['ip']."', '$site')");
	}
}

// -- COUNTER -- //

$time = time();
$date = date("d.m.Y", $time);
$deltime = $time-(3600*24);
safe_query("DELETE FROM ".PREFIX."counter_iplist WHERE del<".$deltime);

if(!mysql_num_rows(safe_query("SELECT ip FROM ".PREFIX."counter_iplist WHERE ip='".$GLOBALS['ip']."'"))) {
	if($userID){
		safe_query("UPDATE ".PREFIX."user SET ip='".$GLOBALS['ip']."' WHERE userID='".$userID."'");
	}
	safe_query("UPDATE ".PREFIX."counter SET hits=hits+1");
	safe_query("INSERT INTO ".PREFIX."counter_iplist (dates, del, ip) VALUES ('".$date."', '".$time."', '".$GLOBALS['ip']."')");
	if(!mysql_num_rows(safe_query("SELECT dates FROM ".PREFIX."counter_stats WHERE dates='".$date."'")))
		safe_query("INSERT INTO `".PREFIX."counter_stats` (`dates`, `count`) VALUES ('".$date."', '1')");
	else
		safe_query("UPDATE ".PREFIX."counter_stats SET count=count+1 WHERE dates='".$date."'");
}

/* update maxonline if necessary */
$res=mysql_fetch_assoc(safe_query("SELECT count(*) as maxuser FROM ".PREFIX."whoisonline"));
safe_query("UPDATE ".PREFIX."counter SET maxonline = ".$res['maxuser']." WHERE maxonline < ".$res['maxuser']);

// -- COUNTRY LIST -- //

$countries='';
$ergebnis = safe_query("SELECT * FROM `".PREFIX."countries` ORDER BY country");
while($ds = mysql_fetch_array($ergebnis)) {
	$countries .= '<option value="'.$ds['short'].'">'.$ds['country'].'</option>';
}

// -- SEARCH ENGINE OPTIMIZATION (SEO) -- //
if(stristr($_SERVER['PHP_SELF'],"/admin/") == false){
	systeminc('seo');
}
else{
	define('PAGETITLE', $GLOBALS['hp_title']);
}

// -- RSS FEEDS -- //

systeminc('func/feeds');
?>