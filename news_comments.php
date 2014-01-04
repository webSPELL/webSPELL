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

$_language->read_module('news');

eval ("\$title_news = \"".gettemplate("title_news")."\";");
echo $title_news;

if(isset($newsID)) unset($newsID);
if(isset($_GET['newsID'])) $newsID = $_GET['newsID'];
if(isset($lang)) unset($lang);
if(isset($_GET['lang'])) $lang = $_GET['lang'];
$post = "";
if(isnewswriter($userID)) $post='<input type="button" onclick="MM_openBrWindow(\'news.php?action=new\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['post_news'].'" />';
echo $post.' <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=news&amp;action=archive\');return document.MM_returnValue" value="'.$_language->module['news_archive'].'" /><hr />';

if($newsID) {
	$result=safe_query("SELECT * FROM ".PREFIX."news WHERE newsID='".$newsID."'");
	$ds=mysqli_fetch_array($result);

	if($ds['intern'] <= isclanmember($userID) && ($ds['published'] || (isnewsadmin($userID) || (isnewswriter($userID) and $ds['poster'] == $userID)))) {

		$date = getformatdate($ds['date']);
		$time = getformattime($ds['date']);
		$rubrikname=getrubricname($ds['rubric']);
		$rubrikname_link = getinput($rubrikname);
		$rubricpic_name = getrubricpic($ds['rubric']);
		$rubricpic='images/news-rubrics/'.$rubricpic_name;
		if(!file_exists($rubricpic) OR $rubricpic_name=='') $rubricpic = ''; 
		else $rubricpic = '<img src="'.$rubricpic.'" border="0" alt="" />';

		$message_array = array();
		$query=safe_query("SELECT n.*, c.short AS `countryCode`, c.country FROM ".PREFIX."news_contents n LEFT JOIN ".PREFIX."countries c ON c.short = n.language WHERE n.newsID='".$newsID."'");
		while($qs = mysqli_fetch_array($query)) {
			$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content'], 'country'=> $qs['country'], 'countryShort' => $qs['countryCode']);
		}
		if(isset($_GET['lang'])) $showlang = getlanguageid($_GET['lang'], $message_array);
		else $showlang = select_language($message_array);

		$langs='';
		$i=0;
		foreach($message_array as $val) {
			if($showlang!=$i)	$langs.='<span style="padding-left:2px"><a href="index.php?site=news_comments&amp;newsID='.$ds['newsID'].'&amp;lang='.$val['lang'].'"><img src="images/flags/'.$val['countryShort'].'.gif" width="18" height="12" border="0" alt="'.$val['country'].'" /></a></span>';
			$i++;
		}
		
		$headline=$message_array[$showlang]['headline'];
		$content=$message_array[$showlang]['message'];
		
		if($ds['intern'] == 1) $isintern = '('.$_language->module['intern'].')';
		else $isintern = '';

        $content = readMore($content, $newsID, true);
		$content = htmloutput($content);
		$content = toggle($content, $ds['newsID']);
		$headline = clearfromtags($headline);
		$comments = '';

		$poster='<a href="index.php?site=profile&amp;id='.$ds['poster'].'"><b>'.getnickname($ds['poster']).'</b></a>';
		$related='';
		if($ds['link1'] && $ds['url1']!="http://" && $ds['window1']) $related.='&#8226; <a href="'.$ds['url1'].'" target="_blank">'.$ds['link1'].'</a> ';
		if($ds['link1'] && $ds['url1']!="http://" && !$ds['window1']) $related.='&#8226; <a href="'.$ds['url1'].'">'.$ds['link1'].'</a> ';

		if($ds['link2'] && $ds['url2']!="http://" && $ds['window2']) $related.='&#8226; <a href="'.$ds['url2'].'" target="_blank">'.$ds['link2'].'</a> ';
		if($ds['link2'] && $ds['url2']!="http://" && !$ds['window2']) $related.='&#8226; <a href="'.$ds['url2'].'">'.$ds['link2'].'</a> ';

		if($ds['link3'] && $ds['url3']!="http://" && $ds['window3']) $related.='&#8226; <a href="'.$ds['url3'].'" target="_blank">'.$ds['link3'].'</a> ';
		if($ds['link3'] && $ds['url3']!="http://" && !$ds['window3']) $related.='&#8226; <a href="'.$ds['url3'].'">'.$ds['link3'].'</a> ';

		if($ds['link4'] && $ds['url4']!="http://" && $ds['window4']) $related.='&#8226; <a href="'.$ds['url4'].'" target="_blank">'.$ds['link4'].'</a> ';
		if($ds['link4'] && $ds['url4']!="http://" && !$ds['window4']) $related.='&#8226; <a href="'.$ds['url4'].'">'.$ds['link4'].'</a> ';

		if(empty($related)) $related="n/a";
    
    	if(isnewsadmin($userID) or (isnewswriter($userID) and $ds['poster'] == $userID)) {
			$adminaction='<input type="button" onclick="MM_openBrWindow(\'news.php?action=edit&amp;newsID='.$ds['newsID'].'\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['edit'].'" />
	    <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'news.php?action=delete&amp;id='.$ds['newsID'].'\')" value="'.$_language->module['delete'].'" />';
		}
		else $adminaction='';

		$bg1=BG_1;

		$tags = Tags::getTagsLinked('news',$newsID);

		eval ("\$news = \"".gettemplate("news")."\";");
		echo $news;

		if(isnewsadmin($userID)) {
			if(!$ds['published']) echo '<form method="post" action="news.php?quickactiontype=publish"><input type="hidden" name="newsID[]" value="'.$ds['newsID'].'" /><input type="submit" name="submit" value="'.$_language->module['publish_now'].'" /></form>';
			else echo '<form method="post" action="news.php?quickactiontype=unpublish"><input type="hidden" name="newsID[]" value="'.$ds['newsID'].'" /><input type="submit" name="submit" value="'.$_language->module['unpublish'].'" /></form>';
		}

		$comments_allowed = $ds['comments'];
		$parentID = $newsID;
		$type = "ne";
		$referer = "index.php?site=news_comments&amp;newsID=$newsID";

		include("comments.php");
	}
	else echo $_language->module['no_access'];
}

?>