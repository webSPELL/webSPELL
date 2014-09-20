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

if($action == "quicksearch" OR ((isset($_GET['forum']) OR isset($_GET['news']) OR isset($_GET['articles']) OR isset($_GET['faq'])) AND $action=="")) {

	$getstring='';
	foreach($_GET as $key=>$val) $getstring .= '&'.$key.'='.stripslashes($val);
	header("Location: index.php?site=search&action=search".$getstring);

}
elseif($action=="search" AND ($userID OR isset($_GET['captcha']))) {

	$_language->read_module('search');

	$run=0;
	if($userID) $run = 1;
	else {
		$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha($_GET['captcha'], $_GET['captcha_hash'])) $run=1;
	}
	
	if($run) {
	
		eval ("\$title_search = \"".gettemplate("title_search")."\";");
		echo $title_search;
	
		$text = str_replace(array('%', '*'), array('\%', '%'), $_GET['text']);
		if(!isset($_GET['r']) or $_GET['r'] < 1 or $_GET['r'] > 100) {
			$results = 50;
		}
		else {
			$results = (int)$_GET['r'];
		}
		isset($_GET['page']) ? $page = (int)$_GET['page'] : $page = 1;
//		isset($_GET['bm']) ? $bm = (int)$_GET['bm'] : $bm = 0;
//		isset($_GET['bd']) ? $bd = (int)$_GET['bd'] : $bd = 0;
//		isset($_GET['by']) ? $by = (int)$_GET['by'] : $by = 0;
		isset($_GET['afterdate']) ? $afterdate = $_GET['afterdate'] : $afterdate = 0;
		isset($_GET['beforedate']) ? $beforedate = $_GET['beforedate'] : $beforedate = 0;
		
		if(mb_strlen(str_replace('%', '', $text))>=$search_min_len){

			if(!$afterdate) {
				$after = 0;
			}
			else {
                $ad = substr($afterdate, 8, 2); 
                $am = substr($afterdate, 5, 2); 
                if($am>12 && $ad < 12) { // User might have mixed up day and month
                    $oldam = $am;
                    $am = $ad;
                    $ad = $oldam;
                }                
                $ay = substr($afterdate, 0, 4);
				$after = mktime(0, 0, 0, $am, $ad, $ay);
			}
			if(!$beforedate) {
				$before = time();
			}
			else {
				$bd = substr($beforedate, 8, 2);
				$bm = substr($beforedate, 5, 2); 
                if($bm>12 && $bd < 12) { // User might have mixed up day and month
                    $oldbm = $bm;
                    $bm = $bd;
                    $bd = $oldbm;
                }
				$by = substr($beforedate, 0, 4);
				$before = mktime(0, 0, 0, $bm, $bd, $by);
			}
		
			$i=0;
			$res_message=array();
			$res_title=array();
			$res_link=array();
			$res_type=array();
			$res_date=array();
			$res_occurr=array();
		
			if(isset($_GET['articles'])) {
				$ergebnis_articles=safe_query("SELECT title, articlesID, date FROM ".PREFIX."articles WHERE date between ".$after." AND ".$before);
		
				while($ds=mysql_fetch_array($ergebnis_articles)) {
					$articlesID = $ds['articlesID'];
		
					$ergebnis_articles_contents = safe_query("SELECT content FROM ".PREFIX."articles_contents WHERE articlesID = '".$articlesID."' AND content LIKE '%".$text."%'");
					if(!mysql_num_rows($ergebnis_articles_contents) and substr_count(strtolower($ds['title']), strtolower(stripslashes($text))) == 0) {
						continue;
					}
					elseif(!mysql_num_rows($ergebnis_articles_contents)) {
						$query_result = mysql_fetch_array(safe_query("SELECT content FROM ".PREFIX."articles_contents WHERE articlesID = '".$articlesID."' ORDER BY page ASC LIMIT 0, 1"));
						$res_message[$i] = clearfromtags($query_result['content']);
						$content = array($query_result['content']);
					}
					else {
						$content = array();
						while($qs = mysql_fetch_array($ergebnis_articles_contents)) {
							$content[] = $qs['content'];
						}
						$res_message[$i] = clearfromtags($content[0]);
					}
		
					$res_title[$i]		=	$ds['title'];
					$res_link[$i]		=	'<a href="index.php?site=articles&amp;action=show&amp;articlesID='.$articlesID.'">'.$_language->module['articles_link'].'</a>';
					$res_occurr[$i]		=	substri_count_array($content, stripslashes($text)) + substr_count(strtolower($ds['title']), strtolower(stripslashes($text)));
					$res_date[$i]		=	$ds['date'];
					$res_type[$i]		=	$_language->module['article'];
		
					$i++;
				}
		
			}
			if(isset($_GET['faq'])) {
				$ergebnis_faq=safe_query("SELECT faqID, faqcatID, date FROM ".PREFIX."faq WHERE date between ".$after." AND ".$before." ORDER BY date");
		
				while($ds = mysql_fetch_array($ergebnis_faq)) {
					$ergebnis_faq_contents = safe_query("SELECT question, answer FROM ".PREFIX."faq WHERE faqID = '".$ds['faqID']."' AND (answer LIKE '%".$text."%' or question LIKE '%".$text."%')");
					if(mysql_num_rows($ergebnis_faq_contents)) {
						$faq_array = array();
						while($qs = mysql_fetch_array($ergebnis_faq_contents)) {
							$faq_array[] = array('question' => $qs['question'], 'answer' => $qs['answer']);
						}
						$faqID = $ds['faqID'];
						$faqcatID = $ds['faqcatID'];
		
						$res_title[$i]		=	$faq_array[0]['question'];
						$res_message[$i]	=	clearfromtags($faq_array[0]['answer']);
						$res_link[$i]		=	'<a href="index.php?site=faq&amp;action=faq&amp;faqID='.$faqID.'&amp;faqcatID='.$faqcatID.'">'.$_language->module['faq_link'].'</a>';
						$res_occurr[$i]		=	substri_count_array($faq_array, stripslashes($text));
						$res_date[$i]		=	$ds['date'];
						$res_type[$i]		=	$_language->module['faq'];
		
						$i++;
					}
				}
		
			}
			if(isset($_GET['forum'])) {
		
				$ergebnis_forum=safe_query("SELECT
										b.readgrps,
										t.boardID,
										p.date, 
										p.topicID, 
										t.topic,
										t.topic as topicname,
										p.message 
									FROM 
										".PREFIX."forum_posts p 
									JOIN ".PREFIX."forum_topics t ON p.topicID = t.topicID
							   		JOIN ".PREFIX."forum_boards b ON p.boardID = b.boardID
									WHERE 
										( 
											p.date between ".$after." AND ".$before." 
										) 
										AND
										(
											( 
												p.message LIKE '%".$text."%' 
											)
											OR
											(
												t.topic LIKE '%".$text."%' 
											)
										) 
										GROUP BY postID
										ORDER BY date");
		
				while($ds=mysql_fetch_array($ergebnis_forum)) {
					if($ds['readgrps'] != "") {
						$usergrps = explode(";", $ds['readgrps']);
						$usergrp = 0;
						foreach($usergrps as $value) {
							if(isinusergrp($value, $userID)) {
								$usergrp = 1;
								break;
							}
						}
						if(!$usergrp and !ismoderator($userID, $ds['boardID'])) continue;
					}
					$topicID = $ds['topicID'];
		
					$res_title[$i]		=	getinput($ds['topicname']);
					$res_message[$i]	=	cleartext($ds['message'], false);
					$res_link[$i]		=	'<a href="index.php?site=forum_topic&amp;topic='.$topicID.'">'.$_language->module['forum_link'].'</a>';
					$res_occurr[$i]		=	substr_count(strtolower($ds['message']), strtolower(stripslashes($text))) + substr_count(strtolower($ds['topic']), strtolower(stripslashes($text)));
					$res_date[$i]		=	$ds['date'];
					$res_type[$i]		=	$_language->module['forum'];
		
					if(isset($alreadythere)) unset($alreadythere);
					$key = array_search($res_link[$i],$res_link);
					if ($key!==null&&$key!==false) { 
						if($key != $i){
							$res_occurr[$key]+=$res_occurr[$i];
							$alreadythere=true;
						}
					}
		
					if(isset($alreadythere)) {
						unset($res_title[$i]);
						unset($res_message[$i]);
						unset($res_link[$i]);
						unset($res_occurr[$i]);
						unset($res_date[$i]);
						unset($res_type[$i]);
					}
					else {
						$i++;
					}
				}
		
			}
			if(isset($_GET['news'])) {
				$ergebnis_news=safe_query("SELECT 
												date,
												poster,
												newsID
										   FROM
										   		".PREFIX."news
										   WHERE
													published = '1'
												AND
													intern <= '".isclanmember($userID)."'
												AND
												(
													date between ".$after." AND ".$before."
												)");
		
				while($ds = mysql_fetch_array($ergebnis_news)) {
					$ergebnis_news_contents = safe_query("SELECT language, headline, content FROM ".PREFIX."news_contents WHERE newsID = '".$ds['newsID']."' and (content LIKE '%".$text."%' or headline LIKE '%".$text."%')");
					if(mysql_num_rows($ergebnis_news_contents)) {
						$message_array = array();
						while($qs = mysql_fetch_array($ergebnis_news_contents)) {
							$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content']);
						}
						$showlang = select_language($message_array);
		
						$newsID = $ds['newsID'];
		
						$res_title[$i]		=	$message_array[$showlang]['headline'];
						$res_message[$i]	=	clearfromtags($message_array[$showlang]['message']);
						$res_link[$i]		=	'<a href="index.php?site=news_comments&amp;newsID='.$newsID.'">'.$_language->module['news_link'].'</a>';
						$res_occurr[$i]		=	substri_count_array($message_array, stripslashes($text));
						$res_date[$i]		=	$ds['date'];
						$res_type[$i]		=	$_language->module['news'];
		
						$i++;
					}
				}
		
			}
			$count_results = $i;
			echo "<p class="text-center"><b>".$count_results."</b> ".$_language->module['results_found']."</p><br><br>";
		
			$pages = ceil($count_results / $results);
			if($pages > 1) echo makepagelink("index.php?site=search&amp;action=search&amp;articles=".$_GET['articles']."&amp;faq=".$_GET['faq']."&amp;forum=".$_GET['forum']."&amp;news=".$_GET['news']."&amp;r=".$_GET['r']."&amp;text=".$_GET['text']."&amp;am=".$_GET['am']."&amp;ad=".$_GET['ad']."&amp;ay=".$_GET['ay']."&amp;bm=".$_GET['bm']."&amp;bd=".$_GET['bd']."&amp;by=".$_GET['by']."&amp;order=".$_GET['order'], $page, $pages);

			// sort results
			if($_GET['order']=='2') asort($res_occurr);
			else arsort($res_occurr);
		
			$i=0;
			foreach($res_occurr as $key => $val) {
				if($page > 1 and $i < ($results * ($page - 1))) {
					$i++;
					continue;
				}
				if($i >= ($results * $page)) {
					break;
				}
		
				$date=date("d.m.Y", $res_date[$key]);
				$type=$res_type[$key];
				if(mb_strlen($res_message[$key]) > 200) {
					for($z = 0; $z < mb_strlen($res_message[$key]); $z++) {
						$tmp = mb_substr($res_message[$key], $z, 1);
						if($z >= 200 && $tmp == " ") {
							$res_message[$key] = mb_substr($res_message[$key], 0, $z)."...";
							break;
						}
					}
				}
				$auszug=str_ireplace(stripslashes($text), '<b>'.stripslashes($text).'</b>', $res_message[$key]);
				if(mb_strlen($res_title[$key])>50) {
					$title=mb_substr($res_title[$key], 0, 50);
					$title.='..';
				}
				else $title=$res_title[$key];
				$link=$res_link[$key];
				$frequency = $res_occurr[$key];
		
				eval ("\$search_result = \"".gettemplate("search_result")."\";");
				echo $search_result;
		
				$i++;
		
			}
		}
		else echo str_replace("%min_chars%", $search_min_len,$_language->module['too_short']);
	} else echo $_language->module['wrong_securitycode'];

}
else {
	if(!isset($_GET['site'])){
		header("Location: index.php?site=search");
	}
	$_language->read_module('search');
	
	if(isset($_REQUEST['text'])) $text = getinput($_REQUEST['text']);
	else $text='';

	eval ("\$title_search = \"".gettemplate("title_search")."\";");
	echo $title_search;

	$bg1=BG_1;
	
	if($userID) {
		eval ("\$search_form = \"".gettemplate("search_form_loggedin")."\";");
		echo $search_form;
	} else {
		$CAPCLASS = new Captcha;
		$captcha = $CAPCLASS->create_captcha();
		$hash = $CAPCLASS->get_hash();
		$CAPCLASS->clear_oldcaptcha();
		eval ("\$search_form = \"".gettemplate("search_form_notloggedin")."\";");
		echo $search_form;
	}
}

?>