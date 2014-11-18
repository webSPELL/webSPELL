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
else $action='';
if(isset($_REQUEST['quickactiontype'])) $quickactiontype = $_REQUEST['quickactiontype'];
else $quickactiontype='';

if($action=="new") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');
	$_language->read_module('bbcode', true);
	if(!isnewswriter($userID)) die($_language->module['no_access']);

	safe_query("INSERT INTO ".PREFIX."news (date, poster, saved) VALUES ('".time()."', '".$userID."', '0')");
	$newsID=mysqli_insert_id($_database);

	$rubrics='';
	$newsrubrics=safe_query("SELECT rubricID, rubric FROM ".PREFIX."news_rubrics ORDER BY rubric");
	while($dr=mysqli_fetch_array($newsrubrics)) {
		$rubrics.='<option value="'.$dr['rubricID'].'">'.$dr['rubric'].'</option>';
	}

	if(isset($_POST['topnews'])) safe_query("UPDATE ".PREFIX."settings SET topnewsID='$newsID'");

	$count_langs = 0;
	$lang=safe_query("SELECT lang, language FROM ".PREFIX."news_languages ORDER BY language");
	$langs='';
	while($dl=mysqli_fetch_array($lang)) {
		$langs.="news_languages[".$count_langs."] = new Array();\nnews_languages[".$count_langs."][0] = '".$dl['lang']."';\nnews_languages[".$count_langs."][1] = '".$dl['language']."';\n";
		$count_langs++;
	}

	$message_vars='';
	$headline_vars='';
	$langs_vars='';
	$langcount=1;

	$url1="http://";
	$url2="http://";
	$url3="http://";
	$url4="http://";
	$link1='';
	$link2='';
	$link3='';
	$link4='';
	$window1_new = 'checked="checked"';
	$window1_self = '';
	$window2_new = 'checked="checked"';
	$window2_self = '';
	$window3_new = 'checked="checked"';
	$window3_self = '';
	$window4_new = 'checked="checked"';
	$window4_self = '';
	$intern = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';
	$topnews = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';

	$bg1=BG_1;

	$selects='';
	for($i = 1; $i <= $count_langs; $i++) {
		$selects .= '<option value="'.$i.'">'.$i.'</option>';
	}

	$tags = '';

	$postform = '';
	$comments='<option value="0">'.$_language->module['no_comments'].'</option><option value="1">'.$_language->module['user_comments'].'</option><option value="2" selected="selected">'.$_language->module['visitor_comments'].'</option>';
	
	eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
	eval ("\$addflags = \"".gettemplate("flags")."\";");

	eval ("\$news_post = \"".gettemplate("news_post")."\";");
	echo $news_post;
}
elseif($action=="save") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');
	$newsID = $_POST['newsID'];

	$ds=mysqli_fetch_array(safe_query("SELECT poster FROM ".PREFIX."news WHERE newsID = '".$newsID."'"));
	if(($ds['poster'] != $userID or !isnewswriter($userID)) and !isnewsadmin($userID)) {
		die($_language->module['no_access']);
	}

	$save = isset($_POST['save']);
	$preview = isset($_POST['preview']);

	if(isset($_POST['rubric'])) $rubric = $_POST['rubric'];
	else $rubric = 0;

	$lang = $_POST['lang'];
	$headline = $_POST['headline'];
	$message = $_POST['message'];
	$message = str_replace('\r\n', "\n", $message);

	$link1 = strip_tags($_POST['link1']);
	$url1 = strip_tags($_POST['url1']);
	$window1 = $_POST['window1'];

	$link2 = strip_tags($_POST['link2']);
	$url2 = strip_tags($_POST['url2']);
	$window2 = $_POST['window2'];

	$link3 = strip_tags($_POST['link3']);
	$url3 = strip_tags($_POST['url3']);
	$window3 = $_POST['window3'];

	$link4 = strip_tags($_POST['link4']);
	$url4 = strip_tags($_POST['url4']);
	$window4 = $_POST['window4'];

	$intern = $_POST['intern'];
	$comments = $_POST['comments'];

	safe_query("UPDATE ".PREFIX."news SET rubric='".$rubric."',
                      link1='".$link1."',
                      url1='".$url1."',
                      window1='".$window1."',
                      link2='".$link2."',
                      url2='".$url2."',
                      window2='".$window2."',
                      link3='".$link3."',
                      url3='".$url3."',
                      window3='".$window3."',
                      link4='".$link4."',
                      url4='".$url4."',
                      window4='".$window4."',
                      saved='1',
                      intern='".$intern."',
                      comments='".$comments."' WHERE newsID='".$newsID."'");

	Tags::setTags('news', $newsID, $_POST['tags']);

	$update_langs = array();
	$query = safe_query("SELECT language FROM ".PREFIX."news_contents WHERE newsID = '".$newsID."'");
	while($qs = mysqli_fetch_array($query)) {
		$update_langs[] = $qs['language'];
		if(in_array($qs['language'], $lang)) {
			$update_langs[] = $qs['language'];
		}
		else {
			safe_query("DELETE FROM ".PREFIX."news_contents WHERE newsID = '".$newsID."' and language = '".$qs['language']."'");
		}
	}

	for($i = 0; $i < count($message); $i++) {
		if(in_array($lang[$i], $update_langs)) {
			safe_query("UPDATE ".PREFIX."news_contents SET headline = '".$headline[$i]."', content = '".$message[$i]."' WHERE newsID = '".$newsID."' and language = '".$lang[$i]."'");
			unset($update_langs[$lang[$i]]);
		}
		else {
			safe_query("INSERT INTO ".PREFIX."news_contents (newsID, language, headline, content) VALUES ('".$newsID."', '".$lang[$i]."', '".$headline[$i]."', '".$message[$i]."')");
		}
	}

	// delete the entries that are older than 2 hour and contain no text
	safe_query("DELETE FROM `".PREFIX."news` WHERE `saved` = '0' and ".time()." - `date` > ".(2 * 60 * 60));

	if(isset($_POST['topnews'])) {
		if($_POST['topnews']) {
			safe_query("UPDATE ".PREFIX."settings SET topnewsID='".$newsID."'");
		}
		elseif(!$_POST['topnews'] and $newsID == $topnewsID) {
			safe_query("UPDATE ".PREFIX."settings SET topnewsID='0'");
		}
	}
  generate_rss2();
	if($save) echo'<body onload="window.close()"></body>';
	if($preview) header("Location: news.php?action=preview&newsID=".$newsID);
	if($languagecount) header("Location: news.php?action=edit&newsID=".$newsID);

}
elseif($action=="preview") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');

	$newsID = $_GET['newsID'];

	$result=safe_query("SELECT * FROM ".PREFIX."news WHERE newsID='$newsID'");
	$ds=mysqli_fetch_array($result);

	if(($ds['poster'] != $userID or !isnewswriter($userID)) and !isnewsadmin($userID)) {
		die($_language->module['no_access']);
	}

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Clanpage using webSPELL 4 CMS" />
	<meta name="author" content="webspell.org" />
	<meta name="keywords" content="webspell, webspell4, clan, cms" />
	<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
	<meta name="generator" content="webSPELL" />

<!-- Head & Title include -->
	<title>'.PAGETITLE.'; ?></title>
	<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
	<script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
<!-- end Head & Title include -->
</head>
<body>';

	$bg1=BG_1;

	eval ("\$title_news = \"".gettemplate("title_news")."\";");
	echo $title_news;

	$bgcolor=BG_1;
	$date = getformatdate($ds['date']);
	$time = getformattime($ds['date']);
	$rubrikname=getrubricname($ds['rubric']);
	$rubrikname_link = getinput(getrubricname($ds['rubric']));
	$rubricpic='<img src="images/news-rubrics/'.getrubricpic($ds['rubric']).'" alt="" />';
	if(!file_exists($rubricpic)) $rubricpic = '';

	$adminaction='';

	$message_array = array();
	$query=safe_query("SELECT * FROM ".PREFIX."news_contents WHERE newsID='".$newsID."'");
	while($qs = mysqli_fetch_array($query)) {
		$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content']);
	}
	$showlang = select_language($message_array);

	$langs='';
	$i=0;
	foreach($message_array as $val) {
		if($showlang!=$i)	$langs.='<a href="index.php?site=news_comments&amp;newsID='.$ds['newsID'].'&amp;lang='.$val['lang'].'">[flag]'.$val['lang'].'[/flag]</a>';
		$i++;
	}
	$langs = flags($langs);

	$headline=$message_array[$showlang]['headline'];
	$content=$message_array[$showlang]['message'];
	
	if($ds['intern'] == 1) $isintern = '('.$_language->module['intern'].')';
    else $isintern = '';
    
	$content = htmloutput($content);
	$content = toggle($content, $ds['newsID']);
	$poster='<a href="index.php?site=profile&amp;id='.$ds['poster'].'"><b>'.getnickname($ds['poster']).'</b></a>';
	$related='';
	$comments="";
	if($ds['link1'] && $ds['url1']!="http://" && $ds['window1']) $related.='&#8226; <a href="'.$ds['url1'].'" target="_blank">'.$ds['link1'].'</a> ';
	if($ds['link1'] && $ds['url1']!="http://" && !$ds['window1']) $related.='&#8226; <a href="'.$ds['url1'].'">'.$ds['link1'].'</a> ';

	if($ds['link2'] && $ds['url2']!="http://" && $ds['window2']) $related.='&#8226; <a href="'.$ds['url2'].'" target="_blank">'.$ds['link2'].'</a> ';
	if($ds['link2'] && $ds['url2']!="http://" && !$ds['window2']) $related.='&#8226; <a href="'.$ds['url2'].'">'.$ds['link2'].'</a> ';

	if($ds['link3'] && $ds['url3']!="http://" && $ds['window3']) $related.='&#8226; <a href="'.$ds['url3'].'" target="_blank">'.$ds['link3'].'</a> ';
	if($ds['link3'] && $ds['url3']!="http://" && !$ds['window3']) $related.='&#8226; <a href="'.$ds['url3'].'">'.$ds['link3'].'</a> ';

	if($ds['link4'] && $ds['url4']!="http://" && $ds['window4']) $related.='&#8226; <a href="'.$ds['url4'].'" target="_blank">'.$ds['link4'].'</a> ';
	if($ds['link4'] && $ds['url4']!="http://" && !$ds['window4']) $related.='&#8226; <a href="'.$ds['url4'].'">'.$ds['link4'].'</a> ';

	$tags = Tags::getTagsLinked('news',$ds['newsID']);

	eval ("\$news = \"".gettemplate("news")."\";");
	echo $news;

	echo'<hr>
  <input type="button" onclick="MM_goToURL(\'parent\',\'news.php?action=edit&amp;newsID='.$newsID.'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" class="btn btn-danger" />
  <input type="button" onclick="javascript:self.close()" value="'.$_language->module['save_news'].'" class="btn btn-danger" />
  <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'news.php?action=delete&amp;id='.$newsID.'&amp;close=true\')" value="'.$_language->module['delete'].'" class="btn btn-danger" /></body></html>';
}
elseif($quickactiontype=="publish") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');
	if(!isnewsadmin($userID)) die($_language->module['no_access']);

	if(isset($_POST['newsID'])){
		$newsID = $_POST['newsID'];
		if(is_array($newsID)) {
			foreach($newsID as $id) {
				safe_query("UPDATE ".PREFIX."news SET published='1' WHERE newsID='".(int)$id."'");
			}
		} else safe_query("UPDATE ".PREFIX."news SET published='1' WHERE newsID='".(int)$newsID."'");
		generate_rss2();
		header("Location: index.php?site=news");
	}
	else{
		header("Location: index.php?site=news&action=unpublished");
	}
}
elseif($quickactiontype=="unpublish") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');
	if(!isnewsadmin($userID)) die($_language->module['no_access']);
	
	if(isset($_REQUEST['newsID'])){
		$newsID = $_REQUEST['newsID'];
		if(is_array($newsID)) {
			foreach($newsID as $id) {
				safe_query("UPDATE ".PREFIX."news SET published='0' WHERE newsID='".(int)$id."'");
			}
		}	
		else safe_query("UPDATE ".PREFIX."news SET published='0' WHERE newsID='".(int)$newsID."'");	
		generate_rss2();
	}
	header("Location: index.php?site=news");
}
elseif($quickactiontype=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');
  	if(isset($_POST['newsID'])){
  	$newsID = $_POST['newsID'];
	
		foreach($newsID as $id) {
			$ds=mysqli_fetch_array(safe_query("SELECT screens, poster FROM ".PREFIX."news WHERE newsID='".$id."'"));
			if(($ds['poster'] != $userID or !isnewswriter($userID)) and !isnewsadmin($userID)) {
				die($_language->module['no_access']);
			}
			if($ds['screens']) {
				$screens=explode("|", $ds['screens']);
				if(is_array($screens)) {
					$filepath = "./images/news-pics/";
					foreach($screens as $screen) {
						if(file_exists($filepath.$screen)) @unlink($filepath.$screen);
					}
				}
			}
			Tags::removeTags('news', $id);
			safe_query("DELETE FROM ".PREFIX."news WHERE newsID='".$id."'");
			safe_query("DELETE FROM ".PREFIX."news_contents WHERE newsID='".$id."'");
			safe_query("DELETE FROM ".PREFIX."comments WHERE parentID='".$id."' AND type='ne'");
		}
		generate_rss2();
		header("Location: index.php?site=news&action=archive");
  }
  else{
  	generate_rss2();
  	header("Location: index.php?site=news&action=archive");
  }
}
elseif($action=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');

	$id = $_GET['id'];

	$ds=mysqli_fetch_array(safe_query("SELECT screens, poster FROM ".PREFIX."news WHERE newsID='".$id."'"));
	if(($ds['poster'] != $userID or !isnewswriter($userID)) and !isnewsadmin($userID)) {
		die($_language->module['no_access']);
	}
	if($ds['screens']) {
		$screens=explode("|", $ds['screens']);
		if(is_array($screens)) {
			$filepath = "./images/news-pics/";
			foreach($screens as $screen) {
				if(file_exists($filepath.$screen)) @unlink($filepath.$screen);
			}
		}
	}

	Tags::removeTags('news', $id);

	safe_query("DELETE FROM ".PREFIX."news WHERE newsID='".$id."'");
	safe_query("DELETE FROM ".PREFIX."news_contents WHERE newsID='".$id."'");
	safe_query("DELETE FROM ".PREFIX."comments WHERE parentID='".$id."' AND type='ne'");
  
	generate_rss2();
	if(isset($_GET['close'])) echo'<body onload="window.close()"></body>';
	else header("Location: index.php?site=news");
}
elseif($action=="edit") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('news');

	$newsID = $_GET['newsID'];

	$ds=mysqli_fetch_array(safe_query("SELECT * FROM ".PREFIX."news WHERE newsID='".$newsID."'"));
	if(($ds['poster'] != $userID or !isnewswriter($userID)) and !isnewsadmin($userID)) {
		die($_language->module['no_access']);
	}

	$_language->read_module('bbcode', true);


	$message_array = array();
	$query=safe_query("SELECT * FROM ".PREFIX."news_contents WHERE newsID='".$newsID."'");
	while($qs = mysqli_fetch_array($query)) {
		$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content']);
	}

	$count_langs = 0;
	$lang=safe_query("SELECT lang, language FROM ".PREFIX."news_languages ORDER BY language");
	$langs='';
	while($dl=mysqli_fetch_array($lang)) {
		$langs.="news_languages[".$count_langs."] = new Array();\nnews_languages[".$count_langs."][0] = '".$dl['lang']."';\nnews_languages[".$count_langs."][1] = '".$dl['language']."';\n";
		$count_langs++;
	}

	$message_vars='';
	$headline_vars='';
	$langs_vars='';
	$i=0;
	foreach($message_array as $val) {
		$message_vars .= "message[".$i."] = '".js_replace($val['message'])."';\n";
		$headline_vars .= "headline[".$i."] = '".js_replace(htmlspecialchars($val['headline']))."';\n";
		$langs_vars .= "langs[".$i."] = '".$val['lang']."';\n";
		$i++;
	}
	$langcount = $i;

	$newsrubrics=safe_query("SELECT * FROM ".PREFIX."news_rubrics ORDER BY rubric");
	$rubrics='';
	while($dr=mysqli_fetch_array($newsrubrics)) {
		if($ds['rubric']==$dr['rubricID']) $rubrics.='<option value="'.$dr['rubricID'].'" selected="selected">'.getinput($dr['rubric']).'</option>';
		else $rubrics.='<option value="'.$dr['rubricID'].'">'.getinput($dr['rubric']).'</option>';
	}

	if($ds['intern']) $intern = '<option value="0">'.$_language->module['no'].'</option><option value="1" selected="selected">'.$_language->module['yes'].'</option>';
	else $intern = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';
	if($topnewsID == $newsID) $topnews = '<option value="0">'.$_language->module['no'].'</option><option value="1" selected="selected">'.$_language->module['yes'].'</option>';
	else $topnews = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';

	$selects='';
	for($i = 1; $i <= $count_langs; $i++) {
		if($i == $langcount) $selects .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
		else $selects .= '<option value="'.$i.'">'.$i.'</option>';
	}

	$link1=getinput($ds['link1']);
	$link2=getinput($ds['link2']);
	$link3=getinput($ds['link3']);
	$link4=getinput($ds['link4']);

	$url1="http://";
	$url2="http://";
	$url3="http://";
	$url4="http://";

	if($ds['url1']!="http://") $url1=$ds['url1'];
	if($ds['url2']!="http://") $url2=$ds['url2'];
	if($ds['url3']!="http://") $url3=$ds['url3'];
	if($ds['url4']!="http://") $url4=$ds['url4'];

	if($ds['window1']){
		$window1_new = 'checked="checked"';
		$window1_self = '';
	}
	else{
		$window1_new = '';
		$window1_self = 'checked="checked"';
	}
	if($ds['window2']){
		$window2_new = 'checked="checked"';
		$window2_self = '';
	}
	else{
		$window2_new = '';
		$window2_self = 'checked="checked"';
	}
	if($ds['window3']){
		$window3_new = 'checked="checked"';
		$window3_self = '';
	}
	else{
		$window3_new = '';
		$window3_self = 'checked="checked"';
	}
	if($ds['window4']){
		$window4_new = 'checked="checked"';
		$window4_self = '';
	}
	else{
		$window4_new = '';
		$window4_self = 'checked="checked"';
	}

	$tags = Tags::getTags('news', $newsID);

	$comments='<option value="0">'.$_language->module['no_comments'].'</option><option value="1">'.$_language->module['user_comments'].'</option><option value="2">'.$_language->module['visitor_comments'].'</option>';
	$comments=str_replace('value="'.$ds['comments'].'"', 'value="'.$ds['comments'].'" selected="selected"', $comments);

	$bg1=BG_1;

	eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
	eval ("\$addflags = \"".gettemplate("flags")."\";");

	eval ("\$news_post = \"".gettemplate("news_post")."\";");
	echo $news_post;
}
elseif(basename($_SERVER['PHP_SELF'])=="news.php"){
	generate_rss2();
	header("Location: index.php?site=news");
}
elseif($action=="unpublished") {
	$_language->read_module('news');
	
  	eval ("\$title_news = \"".gettemplate("title_news")."\";");
	echo $title_news;
	
	$post = '';
	if(isnewsadmin($userID)) $post='<input type="button" onclick="MM_openBrWindow(\'news.php?action=new\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="'.$_language->module['post_news'].'" class="btn btn-danger" />';

	echo $post.' <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=news\');return document.MM_returnValue;" value="'.$_language->module['show_news'].'" class="btn btn-danger" /><hr>';

	$page='';

	// Not published News
	if(isnewsadmin($userID)) {
		$ergebnis=safe_query("SELECT * FROM ".PREFIX."news WHERE published='0' AND saved='1' ORDER BY date ASC");
		if(mysqli_num_rows($ergebnis)) {
			echo $_language->module['title_unpublished_news'];

			echo '<form method="post" name="form" action="news.php">';
			eval ("\$news_unpublished_head = \"".gettemplate("news_unpublished_head")."\";");
			echo $news_unpublished_head;

			$i=1;
			while($ds=mysqli_fetch_array($ergebnis)) {
				if($i%2) {
					$bg1=BG_1;
					$bg2=BG_2;
				}
				else {
					$bg1=BG_3;
					$bg2=BG_4;
				}

				$date=getformatdate($ds['date']);
				$rubric=getrubricname($ds['rubric']);
				if(!isset($rubric)) $rubric='';
				$comms = getanzcomments($ds['newsID'], 'ne');
				$message_array = array();
				$query=safe_query("SELECT * FROM ".PREFIX."news_contents WHERE newsID='".$ds['newsID']."'");
				while($qs = mysqli_fetch_array($query)) {
					$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content']);
				}

				$headlines='';
				
				foreach($message_array as $val) {
					$headlines.='<a href="index.php?site=news_comments&amp;newsID='.$ds['newsID'].'&amp;lang='.$val['lang'].'">'.flags('[flag]'.$val['lang'].'[/flag]').' '.clearfromtags($val['headline']).'</a><br>';
				}

				$poster='<a href="index.php?site=profile&amp;id='.$ds['poster'].'">'.getnickname($ds['poster']).'</a>';

				$multiple='';
				$admdel='';
				if(isnewsadmin($userID)) {
					$multiple='<input class="input" type="checkbox" name="newsID[]" value="'.$ds['newsID'].'">';
					$admdel='<div class="row">
                        <div class="col-md-6">
                            <input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);"> '.$_language->module['select_all'].'
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <select name="quickactiontype" class="form-control">
                                    <option value="publish">'.$_language->module['publish_selected'].'</option>
                                    <option value="delete">'.$_language->module['delete_selected'].'</option>
                                </select>
                                <span class="input-group-btn">
                                    <input type="submit" name="quickaction" value="'.$_language->module['go'].'" class="btn btn-danger">
                                </span>
                            </div>
                        </div>
                    </div></form>';
				}

				eval ("\$news_archive_content = \"".gettemplate("news_archive_content")."\";");
				echo $news_archive_content;
				$i++;
			}
			eval ("\$news_archive_foot = \"".gettemplate("news_archive_foot")."\";");
			echo $news_archive_foot;

			unset($ds);
		}
	}
}
elseif($action=="archive") {

	$_language->read_module('news');
  
	eval ("\$title_news = \"".gettemplate("title_news")."\";");
	echo $title_news;

	if(isset($_GET['page'])) $page=(int)$_GET['page'];
	else $page = 1;
	$sort="date";
	if(isset($_GET['sort'])){
	  if(($_GET['sort']=='date') || ($_GET['sort']=='poster') || ($_GET['sort']=='rubric')) $sort=$_GET['sort'];
	}
	
	$type="DESC";
	if(isset($_GET['type'])){
	  if(($_GET['type']=='ASC') || ($_GET['type']=='DESC')) $type=$_GET['type'];
	}
	
	$post='';
	$publish='';
	if(isnewsadmin($userID)) {
		$post='<input type="button" onclick="MM_openBrWindow(\'news.php?action=new\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['post_news'].'" class="btn btn-danger">';
		$unpublished=safe_query("SELECT newsID FROM ".PREFIX."news WHERE published='0' AND saved='1'");
		$unpublished=mysqli_num_rows($unpublished);
		if($unpublished) $publish='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=news&amp;action=unpublished\');return document.MM_returnValue" value="'.$unpublished.' '.$_language->module['unpublished_news'].'" class="btn btn-danger"> ';
	}
	echo $post.' '.$publish.' <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=news\');return document.MM_returnValue" value="'.$_language->module['show_news'].'" class="btn btn-primary"><hr>';

	$all=safe_query("SELECT newsID FROM ".PREFIX."news WHERE published='1' AND intern<=".isclanmember($userID));
	$gesamt=mysqli_num_rows($all);
	$pages=1;

	$max = empty($maxnewsarchiv) ? 20 : $maxnewsarchiv;
	$pages = ceil($gesamt/$max);

	if($pages>1) $page_link = makepagelink("index.php?site=news&amp;action=archive&amp;sort=".$sort."&amp;type=".$type, $page, $pages);
	else $page_link='';

	if($page == "1") {
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."news WHERE published='1' AND intern<=".isclanmember($userID)." ORDER BY ".$sort." ".$type." LIMIT 0,".$max);
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."news WHERE published='1' AND intern<=".isclanmember($userID)." ORDER BY ".$sort." ".$type." LIMIT ".$start.",".$max);
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}
	if($all) {
		if($type=="ASC")
			echo'<a href="index.php?site=news&amp;action=archive&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=DESC">'.$_language->module['sort'].'</a> <img src="images/icons/asc.gif" width="9" height="7" alt="">&nbsp;&nbsp;&nbsp;';
		else
			echo'<a href="index.php?site=news&amp;action=archive&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=ASC">'.$_language->module['sort'].'</a> <img src="images/icons/desc.gif" width="9" height="7" alt="">&nbsp;&nbsp;&nbsp;';


		if($pages>1) echo $page_link;
		if(isnewsadmin($userID)) echo'<form method="post" name="form" action="news.php">';
		
    eval ("\$news_archive_head = \"".gettemplate("news_archive_head")."\";");
		echo $news_archive_head;
    
		$i=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			if($i%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}

			$date=getformatdate($ds['date']);
			$rubric=getrubricname($ds['rubric']);
			$comms = getanzcomments($ds['newsID'], 'ne');
		    if($ds['intern'] == 1) $isintern = '<small>('.$_language->module['intern'].')</small>';
		    else $isintern = '';
      
      $message_array = array();
			$query=safe_query("SELECT * FROM ".PREFIX."news_contents WHERE newsID='".$ds['newsID']."'");
			while($qs = mysqli_fetch_array($query)) {
				$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content']);
			}

			$headlines='';

			foreach($message_array as $val) {
				$headlines.='<a href="index.php?site=news_comments&amp;newsID='.$ds['newsID'].'&amp;lang='.$val['lang'].'">'.flags('[flag]'.$val['lang'].'[/flag]').' '.clearfromtags($val['headline']).'</a> '.$isintern.'<br>';
			}

			$poster='<a href="index.php?site=profile&amp;id='.$ds['poster'].'">'.getnickname($ds['poster']).'</a>';

			$multiple='';
			$admdel='';
			if(isnewsadmin($userID)) $multiple='<input class="archiveitem-checkb" type="checkbox" name="newsID[]" value="'.$ds['newsID'].'">';

			eval ("\$news_archive_content = \"".gettemplate("news_archive_content")."\";");
			echo $news_archive_content;
			$i++;
		}
		
	    if(isnewsadmin($userID)){
	    	$admdel='<div class="row">
			  
	          <div class="col-md-4">
	           <input class="input" id="archivecbx" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);">
	           <label for="archivecbx">'.$_language->module['select_all'].'</label>
	          </div>
	          <div class="col-md-8">

	            <div class="input-group">
			      <select name="quickactiontype" class="form-control">
			          <option value="delete">'.$_language->module['delete_selected'].'</option>
			          <option value="unpublish">'.$_language->module['unpublish_selected'].'</option>
			      </select>

	              <span class="input-group-btn">
	                <input type="submit" name="quickaction" value="'.$_language->module['go'].'" class="btn btn-danger">
	              </span>
	            </div>

	          </div>
	    </div></form>';
		}
		else $admdel='';

		eval ("\$news_archive_foot = \"".gettemplate("news_archive_foot")."\";");
		echo $news_archive_foot;
		unset($ds);

	}
	else echo'no entries';
}
else {
	$_language->read_module('news');
  
	eval ("\$title_news = \"".gettemplate("title_news")."\";");
	echo $title_news;

	$post='';
	$publish='';
	if(isnewswriter($userID)) {
		$post='<input type="button" onclick="MM_openBrWindow(\'news.php?action=new\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="'.$_language->module['post_news'].'" class="btn btn-danger" />';
	}
	if(isnewsadmin($userID)) {
		$unpublished=safe_query("SELECT newsID FROM ".PREFIX."news WHERE published='0' AND saved='1'");
		$unpublished=mysqli_num_rows($unpublished);
		if($unpublished) $publish='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=news&amp;action=unpublished\');return document.MM_returnValue;" value="'.$unpublished.' '.$_language->module['unpublished_news'].'" class="btn btn-danger" /> ';
	}
	echo $post.' '.$publish.'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=news&amp;action=archive\');return document.MM_returnValue;" value="'.$_language->module['news_archive'].'" class="btn btn-primary" /><hr>';

	if(isset($_GET['show'])) {
		$result=safe_query("SELECT rubricID FROM ".PREFIX."news_rubrics WHERE rubric='".$_GET['show']."' LIMIT 0,1");
		$dv=mysqli_fetch_array($result);
		$showonly = "AND rubric='".$dv['rubricID']."'";
	}
	else $showonly = '';

	$result=safe_query("SELECT * FROM ".PREFIX."news WHERE published='1' AND intern<=".isclanmember($userID)." ".$showonly." ORDER BY date DESC LIMIT 0,".$maxshownnews);

	$i=1;
	while($ds=mysqli_fetch_array($result)) {
		if($i%2) $bg1=BG_1;
		else $bg1=BG_2;

		$date = getformatdate($ds['date']);
		$time = getformattime($ds['date']);
		$rubrikname = getrubricname($ds['rubric']);
		$rubrikname_link = getinput($rubrikname);
		$rubricpic_path = "images/news-rubrics/".getrubricpic($ds['rubric']);
		$rubricpic='<img src="'.$rubricpic_path.'" alt="" />';
		if(!is_file($rubricpic_path)) $rubricpic='';

		$message_array = array();
		$query=safe_query("SELECT * FROM ".PREFIX."news_contents WHERE newsID='".$ds['newsID']."'");
		while($qs = mysqli_fetch_array($query)) {
			$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content']);
		}

		$showlang = select_language($message_array);

		$langs='';
		$x=0;
		foreach($message_array as $val) {
			if($showlang!=$x) $langs.='<span style="padding-left:2px"><a href="index.php?site=news_comments&amp;newsID='.$ds['newsID'].'&amp;lang='.$val['lang'].'">[flag]'.$val['lang'].'[/flag]</a></span>';
			$x++;
		}
		$langs = flags($langs);

		$headline=$message_array[$showlang]['headline'];
		$content=$message_array[$showlang]['message'];
		$newsID=$ds['newsID'];
    if($ds['intern'] == 1) $isintern = '('.$_language->module['intern'].')';
    else $isintern = '';
    
    $content = htmloutput($content);
		$content = toggle($content, $ds['newsID']);
		$headline = clearfromtags($headline);
		$poster='<a href="index.php?site=profile&amp;id='.$ds['poster'].'"><b>'.getnickname($ds['poster']).'</b></a>';
		$related="";
    if($ds['link1'] && $ds['url1']!="http://" && $ds['window1']) $related.='&#8226; <a href="'.$ds['url1'].'" target="_blank">'.$ds['link1'].'</a> ';
		if($ds['link1'] && $ds['url1']!="http://" && !$ds['window1']) $related.='&#8226; <a href="'.$ds['url1'].'">'.$ds['link1'].'</a> ';

		if($ds['link2'] && $ds['url2']!="http://" && $ds['window2']) $related.='&#8226; <a href="'.$ds['url2'].'" target="_blank">'.$ds['link2'].'</a> ';
		if($ds['link2'] && $ds['url2']!="http://" && !$ds['window2']) $related.='&#8226; <a href="'.$ds['url2'].'">'.$ds['link2'].'</a> ';

		if($ds['link3'] && $ds['url3']!="http://" && $ds['window3']) $related.='&#8226; <a href="'.$ds['url3'].'" target="_blank">'.$ds['link3'].'</a> ';
		if($ds['link3'] && $ds['url3']!="http://" && !$ds['window3']) $related.='&#8226; <a href="'.$ds['url3'].'">'.$ds['link3'].'</a> ';

		if($ds['link4'] && $ds['url4']!="http://" && $ds['window4']) $related.='&#8226; <a href="'.$ds['url4'].'" target="_blank">'.$ds['link4'].'</a> ';
		if($ds['link4'] && $ds['url4']!="http://" && !$ds['window4']) $related.='&#8226; <a href="'.$ds['url4'].'">'.$ds['link4'].'</a> ';

		if(empty($related)) $related="n/a";

		if($ds['comments']) {
			if($ds['cwID']) {  // CLANWAR-NEWS
				$anzcomments = getanzcomments($ds['cwID'], 'cw');
				$replace = Array('$anzcomments', '$url', '$lastposter', '$lastdate');
				$vars = Array($anzcomments, 'index.php?site=clanwars_details&amp;cwID='.$ds['cwID'], clearfromtags(getlastcommentposter($ds['cwID'], 'cw')), getformatdatetime(getlastcommentdate($ds['cwID'], 'cw')));

				switch($anzcomments) {
					case 0: $comments = str_replace($replace, $vars, $_language->module['no_comment']); break;
					case 1: $comments = str_replace($replace, $vars, $_language->module['comment']); break;
					default: $comments = str_replace($replace, $vars, $_language->module['comments']); break;
				}
			}
			else {
				$anzcomments = getanzcomments($ds['newsID'], 'ne');
				$replace = Array('$anzcomments', '$url', '$lastposter', '$lastdate');
				$vars = Array($anzcomments, 'index.php?site=news_comments&amp;newsID='.$ds['newsID'], clearfromtags(html_entity_decode(getlastcommentposter($ds['newsID'], 'ne'))), getformatdatetime(getlastcommentdate($ds['newsID'], 'ne')));

				switch($anzcomments) {
					case 0: $comments = str_replace($replace, $vars, $_language->module['no_comment']); break;
					case 1: $comments = str_replace($replace, $vars, $_language->module['comment']); break;
					default: $comments = str_replace($replace, $vars, $_language->module['comments']); break;
				}
			}
		}
		else $comments='';

		$tags = Tags::getTagsLinked('news',$ds['newsID']);

		$adminaction = '';
		if(isnewsadmin($userID)) {
			$adminaction .= '<input type="button" onclick="MM_goToURL(\'parent\',\'news.php?quickactiontype=unpublish&amp;newsID='.$ds['newsID'].'\');return document.MM_returnValue;" value="'.$_language->module['unpublish'].'" class="btn btn-danger" /> ';
		}
		if((isnewswriter($userID) and $ds['poster'] == $userID) or isnewsadmin($userID)) {
			$adminaction .= '<input type="button" onclick="MM_openBrWindow(\'news.php?action=edit&amp;newsID='.$ds['newsID'].'\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="'.$_language->module['edit'].'" class="btn btn-danger" />
		  <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'news.php?action=delete&amp;id='.$ds['newsID'].'\')" value="'.$_language->module['delete'].'" class="btn btn-danger" />';
		}

		eval ("\$news = \"".gettemplate("news")."\";");
		echo $news;

		$i++;

		unset($related);
		unset($comments);
		unset($lang);
		unset($ds);
	}
}
?>