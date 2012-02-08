<?php
  function generate_rss2(){
  	global $hp_url,$hp_title;
  	global $rss_default_language;
  	$_language = new Language;
    $_language->set_language($rss_default_language);
    $_language->read_module('feeds');
  	$date=safe_query("SELECT `date` FROM ".PREFIX."news WHERE published = '1' AND intern=0 ORDER BY date DESC LIMIT 0,1");
	if(mysql_num_rows($date)){
		$date = mysql_fetch_assoc($date);
		$updated = $date['date'];
	}
	else{
		$updated = time();
	}
  	$xmlstring='<?xml version="1.0" encoding="UTF-8"?>
                <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
                  <channel>
                    <title>'.$hp_title.' '.$_language->module['news_feed'].'</title>
                    <link>http://'.$hp_url.'</link>
                    <atom:link href="http://'.$hp_url.'/tmp/rss.xml" rel="self" type="application/rss+xml" />
                    <description>'.$_language->module['latest_news_from'].' http://'.$hp_url.'</description>
                    <language>'.$rss_default_language.'-'.$rss_default_language.'</language>
                    <pubDate>'.date('D, d M Y h:i:s O', $updated).'</pubDate>
                    ';
  	$db_news=safe_query("SELECT * FROM ".PREFIX."news WHERE published = '1' AND intern=0 ORDER BY date DESC LIMIT 0,10");
  	$any_news=mysql_num_rows($db_news);
  	if($any_news){
  		while($news=mysql_fetch_array($db_news)){
  			$db_newscontent=safe_query("SELECT * FROM ".PREFIX."news_contents WHERE newsID = '".$news['newsID']."' AND language='".$rss_default_language."'");
  			$any_newscontent=mysql_num_rows($db_newscontent);
  			if($any_newscontent){
  				$newscontent=mysql_fetch_array($db_newscontent);
	  			$xmlstring.='<item>
	                       <title>'.htmlspecialchars(($newscontent['headline'])).'</title>
	                       <description><![CDATA['.htmloutput($newscontent['content']).']]></description>
	                       <author>'.getemail($news['poster']).' ('.getfirstname($news['poster']).' '.getlastname($news['poster']).')</author>
	                       <guid><![CDATA[http://'.$hp_url.'/index.php?site=news_comments&newsID='.$news['newsID'].']]></guid>
	                       <link><![CDATA[http://'.$hp_url.'/index.php?site=news_comments&newsID='.$news['newsID'].']]></link>
	                     </item>
	  			            ';
  			}
  			else{
  				continue;
  			}
  		}
  	}
  	$xmlstring.='</channel>
                 </rss>';
    $rss_xml = fopen("tmp/rss.xml", "w");
  	fwrite($rss_xml, $xmlstring);
  	fclose($rss_xml);
  }
?>