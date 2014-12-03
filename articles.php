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
else $action = '';

if($action=="save") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('articles');

	if(!isnewsadmin($userID)) die($_language->module['no_access']);
	$title = $_POST['title'];
	$message = $_POST['message'];
	$link1 = $_POST['link1'];
	$url1 = $_POST['url1'];
	$window1 = $_POST['window1'];
	$link2 = $_POST['link2'];
	$url2 = $_POST['url2'];
	$window2 = $_POST['window2'];
	$link3 = $_POST['link3'];
	$url3 = $_POST['url3'];
	$window3 = $_POST['window3'];
	$link4 = $_POST['link4'];
	$url4 = $_POST['url4'];
	$window4 = $_POST['window4'];
	$comments = $_POST['comments'];
	$articlesID = $_POST['articlesID'];

	safe_query("UPDATE ".PREFIX."articles SET
								 title='".$title."',
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
								 comments='".$comments."' WHERE articlesID='".$articlesID."'");

	Tags::setTags('articles', $articlesID, $_POST['tags']);

	$anzpages = mysqli_num_rows(safe_query("SELECT * FROM ".PREFIX."articles_contents WHERE articlesID='".$articlesID."'"));
	if($anzpages > count($message)) {
		safe_query("DELETE FROM `".PREFIX."articles_contents` WHERE `articlesID` = '".$articlesID."' and `page` > ".count($message));
	}
	
	for($i = 0; $i <= count($message); $i++) {
	 	if(isset($message[$i])){
			if($i >= $anzpages) {
				safe_query("INSERT INTO ".PREFIX."articles_contents (articlesID, content, page) VALUES ('".$articlesID."', '".$message[$i]."', '".$i."')");
			}
			else {
				safe_query("UPDATE ".PREFIX."articles_contents SET content = '".$message[$i]."' WHERE articlesID = '".$articlesID."' and page = '".$i."'");
			}
		}
	}
	for($x=$_POST['language_count'];$x<100;$x++){
		safe_query("DELETE FROM ".PREFIX."articles_contents WHERE articlesID = '".$articlesID."' and page = '".$x."'");
	}

	// delete the entries that are older than 2 hour and contain no text
	safe_query("DELETE FROM `".PREFIX."articles` WHERE `saved` = '0' and ".time()." - `date` > ".(2 * 60 * 60));

	die('<body onload="window.close()"></body>');
}
elseif(isset($_GET['delete'])) {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('articles');

	if(!isnewsadmin($userID)) die($_language->module['no_access']);

	$ds=mysqli_fetch_array(safe_query("SELECT screens FROM ".PREFIX."articles WHERE articlesID='".$_GET['articlesID']."'"));
	if($ds['screens']) {
		$screens=explode("|", $ds['screens']);
		if(is_array($screens)) {
			$filepath = "./images/articles-pics/";
			foreach($screens as $screen) {
				if(file_exists($filepath.$screen)) @unlink($filepath.$screen);
			}
		}
	}

	Tags::removeTags('articles', $_GET['articlesID']);

	safe_query("DELETE FROM ".PREFIX."articles WHERE articlesID='".$_GET['articlesID']."'");
	safe_query("DELETE FROM ".PREFIX."articles_contents WHERE articlesID='".$_GET['articlesID']."'");
	safe_query("DELETE FROM ".PREFIX."comments WHERE parentID='".$_GET['articlesID']."' AND type='ar'");

	if(isset($close)) echo'<body onload="window.close()"></body>';
	else header("Location: index.php?site=articles");
}

function top5() {
	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	global $_language;

	$_language->read_module('articles');

	echo'<table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="49%" valign="top">';
      
	// RATING
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."articles WHERE saved='1' ORDER BY rating DESC LIMIT 0,5");
	$top=$_language->module['top5_rating'];
	
  eval ("\$top5_head = \"".gettemplate("top5_head")."\";");
  echo $top5_head;
	
  $n=1;
	while($ds=mysqli_fetch_array($ergebnis)) {
		if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}

		$title='<a href="index.php?site=articles&amp;action=show&amp;articlesID='.$ds['articlesID'].'">'.clearfromtags($ds['title']).'</a>';
		$poster='<a href="index.php?site=profile&amp;id='.$ds['poster'].'">'.getnickname($ds['poster']).'</a>';
		$viewed='('.$ds['viewed'].')';
		$ratings=array(0,0,0,0,0,0,0,0,0,0);
		for($i=0; $i<$ds['rating']; $i++) {
			$ratings[$i]=1;
		}
		$ratingpic='<img src="images/icons/rating_'.$ratings[0].'_start.gif" width="1" height="5" alt="" />';
		foreach($ratings as $pic) {
			$ratingpic.='<img src="images/icons/rating_'.$pic.'.gif" width="4" height="5" alt="" />';
		}
		
    echo'<tr>
        <td bgcolor="'.$bg1.'" align="center"><b>'.$n.'.</b></td>
        <td bgcolor="'.$bg1.'" align="center" style="white-space:nowrap;">'.$ratingpic.'</td>
        <td bgcolor="'.$bg1.'">'.$title.'</td>
      </tr>';

		unset($ratingpic);
		$n++;
	}
	
  echo'</table>';
	echo'</td><td width="2%">&nbsp;</td><td width="49%" valign="top">';
  
	// POINTS
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."articles WHERE saved='1' ORDER BY points DESC LIMIT 0,5");
	$top=$_language->module['top5_points'];
	
  eval ("\$top5_head = \"".gettemplate("top5_head")."\";");
	echo $top5_head;
  
	$n=1;
	while($ds=mysqli_fetch_array($ergebnis)) {
    if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}
    
		$title='<a href="index.php?site=articles&amp;action=show&amp;articlesID='.$ds['articlesID'].'">'.clearfromtags($ds['title']).'</a>';
		$viewed='('.$ds['viewed'].')';
		echo'<tr>
        <td bgcolor="'.$bg1.'" align="center"><b>'.$n.'.</b></td>
        <td bgcolor="'.$bg1.'" align="center">'.$ds['points'].'</td>
        <td bgcolor="'.$bg1.'">'.$title.'</td>
      </tr>';
      
		$n++;
	}
	echo'</table></td></tr></table><br />';
}

if($action=="new") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");

	$_language->read_module('articles');
	$_language->read_module('bbcode', true);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	if(isnewsadmin($userID)) {
		safe_query("INSERT INTO ".PREFIX."articles ( date, poster, saved ) VALUES( '".time()."', '$userID', '0' ) ");
		$articlesID=mysqli_insert_id($_database);

		$selects='';
		for($i=1;$i<100;$i++) {
			$selects .= '<option value="'.$i.'">'.$i.'</option>';
		}

		$tags = '';

		$pages = 1;

		$bg1=BG_1;
		eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
		eval ("\$addflags = \"".gettemplate("flags")."\";");

		eval ("\$articles_post = \"".gettemplate("articles_post")."\";");
		echo $articles_post;
	}
	else redirect('index.php?site=articles', $_language->module['no_access']);
}
elseif($action=="edit") {

	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");

	$_language->read_module('articles');
	$_language->read_module('bbcode', true);

	$articlesID = $_GET['articlesID'];

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	if(isnewsadmin($userID)) {
		$ds=mysqli_fetch_array(safe_query("SELECT * FROM ".PREFIX."articles WHERE articlesID = '".$articlesID."'"));

		$title=getinput($ds['title']);

		$message = array();
		$query = safe_query("SELECT content FROM ".PREFIX."articles_contents WHERE articlesID = '".$articlesID."' ORDER BY page ASC");
		while($qs = mysqli_fetch_array($query)) {
			$message[] = $qs['content'];
		}

		$message_vars='';
		$i=0;
		foreach($message as $val) {
			$message_vars .= "message[".$i."] = '".js_replace($val)."';\n";
			$i++;
		}
		$pages = count($message);

		$selects='';
		for($i=1;$i<100;$i++) {
		 	if($i==$pages) $selected = "selected='selected'";
		 	else $selected = NULL;
			$selects .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
		}

		$link1=getinput($ds['link1']);
		$link2=getinput($ds['link2']);
		$link3=getinput($ds['link3']);
		$link4=getinput($ds['link4']);
		$url1=getinput($ds['url1']);
		$url2=getinput($ds['url2']);
		$url3=getinput($ds['url3']);
		$url4=getinput($ds['url4']);
		
    	if($ds['window1']) $window1='<input class="input" name="window1" type="radio" value="1" checked="checked" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window1" value="0" /> '.$_language->module['self'].'';
		else $window1='<input class="input" name="window1" type="radio" value="1" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window1" value="0" checked="checked" /> '.$_language->module['self'].'';

		if($ds['window2']) $window2='<input class="input" name="window2" type="radio" value="1" checked="checked" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window2" value="0" /> '.$_language->module['self'].'';
		else $window2='<input class="input" name="window2" type="radio" value="1" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window2" value="0" checked="checked" /> '.$_language->module['self'].'';

		if($ds['window3']) $window3='<input class="input" name="window3" type="radio" value="1" checked="checked" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window3" value="0" /> '.$_language->module['self'].'';
		else $window3='<input class="input" name="window3" type="radio" value="1" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window3" value="0" checked="checked" /> '.$_language->module['self'].'';

		if($ds['window4']) $window4='<input class="input" name="window4" type="radio" value="1" checked="checked" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window4" value="0" /> '.$_language->module['self'].'';
		else $window4='<input class="input" name="window4" type="radio" value="1" /> '.$_language->module['new_window'].' <input class="input" type="radio" name="window4" value="0" checked="checked" /> '.$_language->module['self'].'';

		$tags = Tags::getTags('articles', $articlesID);

		$comments='<option value="0">'.$_language->module['no_comments'].'</option><option value="1">'.$_language->module['user_comments'].'</option><option value="2">'.$_language->module['visitor_comments'].'</option>';
		$comments=str_replace('value="'.$ds['comments'].'"', 'value="'.$ds['comments'].'" selected="selected"', $comments);

		$bg1=BG_1;
		eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
		eval ("\$addflags = \"".gettemplate("flags")."\";");

		eval ("\$articles_edit = \"".gettemplate("articles_edit")."\";");
		echo $articles_edit;
	}
	else redirect('index.php?site=articles', $_language->module['no_access']);
}
elseif($action=="show") {

	$_language->read_module('articles');

	eval ("\$title_articles = \"".gettemplate("title_articles")."\";");
	echo $title_articles;

	$articlesID = (int)$_GET['articlesID'];
	if(isset($_GET['page'])) $page = (int)$_GET['page'];
	else $page = 1;

	if(isnewsadmin($userID)) echo'<input type="button" onclick="MM_openBrWindow(\'articles.php?action=new\',\'Articles\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="'.$_language->module['new_article'].'" /> ';
	echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=articles\');return document.MM_returnValue;" value="'.$_language->module['all_articles'].'" /><br /><br />';

	if($page==1) safe_query("UPDATE ".PREFIX."articles SET viewed=viewed+1 WHERE articlesID='".$articlesID."'");
	$result=safe_query("SELECT * FROM ".PREFIX."articles WHERE articlesID='".$articlesID."'");

	if(mysqli_num_rows($result)) {

		$ds=mysqli_fetch_array($result);
		$date_time = getformatdatetime($ds['date']);
		$title = clearfromtags($ds['title']);

		$content = array();
		$query = safe_query("SELECT * FROM ".PREFIX."articles_contents WHERE articlesID = '".$articlesID."' ORDER BY page ASC");
		while($qs = mysqli_fetch_array($query)) {
			$content[] = $qs['content'];
		}

		$pages = count($content);
		$content = htmloutput($content[$page-1]);
		$content = toggle($content, $ds['articlesID']);
		if($pages>1) $page_link = makepagelink("index.php?site=articles&amp;action=show&amp;articlesID=$articlesID", $page, $pages);
    	else $page_link='';

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

		$comments_allowed = $ds['comments'];

		$ratings=array(0,0,0,0,0,0,0,0,0,0);
		for($i=0; $i<$ds['rating']; $i++) {
			$ratings[$i]=1;
		}
		$ratingpic='<img src="images/icons/rating_'.$ratings[0].'_start.gif" width="1" height="5" alt="" />';
		foreach($ratings as $pic) {
			$ratingpic.='<img src="images/icons/rating_'.$pic.'.gif" width="4" height="5" alt="" />';
		}

		if(isnewsadmin($userID)) $adminaction='<br /><br /><input type="button" onclick="MM_openBrWindow(\'articles.php?action=edit&amp;articlesID='.$ds['articlesID'].'\',\'News\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="'.$_language->module['edit'].'" />
    <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'articles.php?delete=true&amp;articlesID='.$ds['articlesID'].'\');" value="'.$_language->module['delete'].'" />';
		else $adminaction='';

		if($loggedin) {
			$getarticles=safe_query("SELECT articles FROM ".PREFIX."user WHERE userID='$userID'");
			$found=false;
			if(mysqli_num_rows($getarticles)) {
				$ga=mysqli_fetch_array($getarticles);
				if($ga['articles']!="") {
					$string=$ga['articles'];
					$array=explode(":", $string);
					$anzarray=count($array);
					for($i=0; $i<$anzarray; $i++) {
						if($array[$i]==$articlesID) $found=true;
					}
				}
			}
			if($found) $rateform=$_language->module['already_rated'];
			else $rateform='<form method="post" action="rating.php">
      <table cellspacing="0" cellpadding="2" align="right">
        <tr>
          <td>'.$_language->module['rate_with'].'
          <select name="rating">
            <option>0 - '.$_language->module['poor'].'</option>
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
            <option>7</option>
            <option>8</option>
            <option>9</option>
            <option>10 - '.$_language->module['perfect'].'</option>
          </select>
          <input type="hidden" name="userID" value="'.$userID.'" />
          <input type="hidden" name="type" value="ar" />
          <input type="hidden" name="id" value="'.$ds['articlesID'].'" />
          <input type="submit" name="Submit" value="'.$_language->module['rate'].'" /></td>
        </tr>
      </table>
      </form>';
		}
		else $rateform=$_language->module['login_for_rate'];

		$tags = Tags::getTagsLinked('articles',$articlesID);
		
		$bg1=BG_1;
		eval ("\$articles = \"".gettemplate("articles")."\";");
		echo $articles;

		unset($related);
		unset($comments);
		unset($lang);
		unset($ds);
		unset($ratingpic);
		unset($page);
		unset($pages);

		$parentID = $articlesID;
		$type = "ar";
		$referer = "index.php?site=articles&amp;action=show&amp;articlesID=$articlesID";

		include("comments.php");
	}
	else echo $_language->module['no_entries'];
}
else {

	$_language->read_module('articles');

	if(isset($_GET['page'])) $page=(int)$_GET['page'];
	else $page = 1;
	$sort="date";
	if(isset($_GET['sort'])){
	  if(($_GET['sort']=='date') || ($_GET['sort']=='poster') || ($_GET['sort']=='rating') || ($_GET['sort']=='viewed')) $sort=$_GET['sort'];
	}
	$type="DESC";
	if(isset($_GET['type'])){
	  if(($_GET['type']=='ASC') || ($_GET['type']=='DESC')) $type=$_GET['type'];
	}
	
	eval ("\$title_articles = \"".gettemplate("title_articles")."\";");
	echo $title_articles;
	
  if(isnewsadmin($userID)) echo'<input type="button" onclick="MM_openBrWindow(\'articles.php?action=new\',\'Articles\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\');" value="'.$_language->module['new_article'].'" /><br /><br />';

	$alle=safe_query("SELECT articlesID FROM ".PREFIX."articles WHERE saved='1'");
	$gesamt = mysqli_num_rows($alle);
	$pages=1;

	$max=$maxarticles;

	for ($n=$max; $n<=$gesamt; $n+=$max) {
		if($gesamt>$n) $pages++;
	}

	if($pages>1) $page_link = makepagelink("index.php?site=articles&amp;sort=".$sort."&amp;type=".$type, $page, $pages);
  else $page_link='';

	if ($page == "1") {
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."articles WHERE saved='1' ORDER BY $sort $type LIMIT 0,$max");
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."articles WHERE saved='1' ORDER BY $sort $type LIMIT $start,$max");
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}
	if($gesamt) {
		top5();
		if($type=="ASC")
		echo'<a href="index.php?site=articles&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=DESC">'.$_language->module['sort'].'</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';
		else
		echo'<a href="index.php?site=articles&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=ASC">'.$_language->module['sort'].'</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="" />&nbsp;&nbsp;&nbsp;';


		if($pages>1) echo $page_link;
		
    eval ("\$articles_head = \"".gettemplate("articles_head")."\";");
		echo $articles_head;
    
		$n=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$date=getformatdate($ds['date']);

			$title='<a href="index.php?site=articles&amp;action=show&amp;articlesID='.$ds['articlesID'].'">'.clearfromtags($ds['title']).'</a>';
			$poster='<a href="index.php?site=profile&amp;id='.$ds['poster'].'"><b>'.getnickname($ds['poster']).'</b></a>';
			$viewed=$ds['viewed'];

			$ratings=array(0,0,0,0,0,0,0,0,0,0);
			for($i=0; $i<$ds['rating']; $i++) {
				$ratings[$i]=1;
			}
			$ratingpic='<img src="images/icons/rating_'.$ratings[0].'_start.gif" width="1" height="5" alt="" />';
			foreach($ratings as $pic) {
				$ratingpic.='<img src="images/icons/rating_'.$pic.'.gif" width="4" height="5" alt="" />';
			}

			eval ("\$articles_content = \"".gettemplate("articles_content")."\";");
			echo $articles_content;
			unset($ratingpic);
			$n++;
		}
		eval ("\$articles_foot = \"".gettemplate("articles_foot")."\";");
		echo $articles_foot;
		unset($ds);
	}
	else echo $_language->module['no_entries'];
}

?>