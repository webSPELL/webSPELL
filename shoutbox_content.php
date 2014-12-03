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

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = null;
if($action=="save") {

	$message = trim($_POST['message']);
	$name = trim($_POST['name']);
	$run=0;
	if($userID) {
		$run=1;
		$name = $_database->escape_string(getnickname($userID));
	}
	else {
		$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha($_POST['captcha'], $_POST['captcha_hash'])) $run=1;

		if(mysqli_num_rows(safe_query("SELECT * FROM ".PREFIX."user WHERE nickname = '$name' "))) $name = '*'.$name.'*';
		$name = clearfromtags($name);
	}

	if(!empty($name) && !empty($message) && $run) {
		$date=time();
		$ip = $GLOBALS['ip'];
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."shoutbox ORDER BY date DESC LIMIT 0,1");
		$ds=mysqli_fetch_array($ergebnis);
		if(($ds['message'] != $message) OR ($ds['name'] != $name)) safe_query("INSERT INTO ".PREFIX."shoutbox (date, name, message, ip) VALUES ( '$date', '$name', '$message', '$ip' ) ");
	}
	redirect('index.php?site=shoutbox_content&action=showall','shoutbox',0);
}
elseif($action=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include('_functions.php');
	if(!isfeedbackadmin($userID)) die('No access.');
	if(isset($_POST['shoutID'])){
		if(!is_array($_POST['shoutID'])){
			$_POST['shoutID'] = array($_POST['shoutID']);
		}
		foreach($_POST['shoutID'] as $id) {
			safe_query("DELETE FROM ".PREFIX."shoutbox WHERE shoutID='$id'");
		}
	}
	header("Location: index.php?site=shoutbox_content&action=showall");
}

elseif($action=="showall") {

	$_language->read_module('shoutbox');
	eval ("\$title_shoutbox = \"".gettemplate("title_shoutbox")."\";");
	echo $title_shoutbox;

	$tmp = mysqli_fetch_assoc(safe_query("SELECT count(shoutID) as cnt FROM ".PREFIX."shoutbox ORDER BY date"));
	$gesamt = $tmp['cnt'];
	$pages=ceil($gesamt/$maxsball);
	$max=$maxsball;
	if(!isset($_GET['page'])) $page = 1; else $page = (int)$_GET['page'];
	$type = 'DESC';
	if(isset($_GET['type'])){
	  if($_GET['type']=='ASC'){
	  	$type = 'ASC';
	  }
	}

	if($pages>1) $page_link = makepagelink("index.php?site=shoutbox_content&amp;action=showall&amp;type=$type", $page, $pages);
	else $page_link='';

	if ($page == "1") {
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."shoutbox ORDER BY date $type LIMIT 0,$max");
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."shoutbox ORDER BY date $type LIMIT $start,$max");
		if($type=="DESC") $n = $gesamt-($page-1)*$max;
		else $n = ($page-1)*$max+1;
	}

	if($type=="ASC")
	$sorter='<a href="index.php?site=shoutbox_content&amp;action=showall&amp;page='.$page.'&amp;type=DESC">'.$_language->module['sort'].'</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';
	else
	$sorter='<a href="index.php?site=shoutbox_content&amp;action=showall&amp;page='.$page.'&amp;type=ASC">'.$_language->module['sort'].'</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';

	eval ("\$shoutbox_all_head = \"".gettemplate("shoutbox_all_head")."\";");
	echo $shoutbox_all_head;

	$i=1;
	while($ds=mysqli_fetch_array($ergebnis)) {

		$i%2 ? $bg1=BG_1 : $bg1=BG_2;
		$date=getformatdatetime($ds['date']);
		$name=$ds['name'];
		$message=cleartext($ds['message'], false);
		$ip='logged';

		if(isfeedbackadmin($userID)) {
			$actions='<input class="input" type="checkbox" name="shoutID[]" value="'.$ds['shoutID'].'" />';
			$ip=$ds['ip'];
		}
		else $actions='';

		eval ("\$shoutbox_all_content = \"".gettemplate("shoutbox_all_content")."\";");
		echo $shoutbox_all_content;
		if($type=="DESC") $n--;
		else $n++;
		$i++;
	}
	eval ("\$shoutbox_all_foot = \"".gettemplate("shoutbox_all_foot")."\";");
	echo $shoutbox_all_foot;

	if(isfeedbackadmin($userID)) $submit='<input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'
											  <input type="submit" value="'.$_language->module['delete_selected'].'" />';
	else $submit='';
	echo'<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
   		<td>'.$page_link.'</td>
   		<td align="right">'.$submit.'</td>
		</tr>
		</table></form>';

	if($pages>1) $page_link = makepagelink("index.php?site=shoutbox_content&amp;action=showall", $page, $pages);
}
elseif(basename($_SERVER['PHP_SELF'])!="shoutbox_content.php"){
	redirect('index.php?site=shoutbox_content&action=showall','shoutbox',0);
}
else {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;
	$bg1=BG_1;

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."shoutbox ORDER BY date DESC LIMIT 0,".$maxshoutbox);
	while($ds=mysqli_fetch_array($ergebnis)) {
		$date=getformattime($ds['date']);
		$name=$ds['name'];
		$message=cleartext($ds['message'], false);
		$message=str_replace("&amp;amp;","&",$message);

		eval ("\$shoutbox_content = \"".gettemplate("shoutbox_content")."\";");
		echo $shoutbox_content;
	}
}



?>