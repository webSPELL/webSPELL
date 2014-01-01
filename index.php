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

// important data include
include("_mysql.php");
include("_settings.php");
include("_functions.php");

$_language->read_module('index');
$index_language = $_language->module;

if(isset($_GET['site'])) $site = $_GET['site'];
else
if(isset($site)) unset($site);
// end important data include
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Clanpage using webSPELL 4 CMS" />
<meta name="author" content="webspell.org" />
<meta name="keywords" content="webspell, webspell4, clan, cms<?php if(isset($GLOBALS['metatags']['keywords'])){echo ', '.$GLOBALS['metatags']['keywords'] ;}?>" />
<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
<meta name="generator" content="webSPELL" />

<!-- Head & Title include -->
<title><?php echo PAGETITLE; ?></title>
<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
<link href="tmp/rss.xml" rel="alternate" type="application/rss+xml" title="<?php echo getinput($myclanname); ?> - RSS Feed" />
<script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
<!-- end Head & Title include -->

<!-- standard Design stylesheet -->
<style type="text/css">
body { margin: 5px 0 10px 0; padding: 0; font: 10px Verdana, Arial, Tahoma, Helvetica, sans-serif; color: #000000; background-image: url(images/bg.gif); }
div#container { width: 1000px; margin: 0 auto; padding: 0; text-align: left; }
div#head { width: 1000px; height: 12px; background-color: #36befc; }
div#content { width: 1000px; margin: 0; background-color: #ffffff; }
div#content .cols { float: left; width: 800px; }
div#content .col1 { float: left; width: 200px; border-right: 1px solid #cccccc; }
div#content .col2 { margin-left: 200px; text-align: justify; }
div#content .col3 { margin-left: 800px; border-left: 1px solid #cccccc; }
div#footer { clear: both; height: 50px; width: 1000px; text-align: center; background-color: <?php echo BG_4; ?>; }
hr.grey { height: 1px; background-color: #cccccc; color: #cccccc; border: none; margin: 10px 0 4px 0; }
.nav { color: #36befc; font-weight: bold; }
</style>
<!--[if IE]>
<style type="text/css">
div#content .col2 { width: 74%; }
div#content .col3 { width: 19%; }
hr.grey { margin: 3px 0 3px 0; }
</style>
<![endif]-->
<!--[if lte IE 7]>
<style type="text/css">
hr.grey { margin: 3px 0 -3px 0; }
</style>
<![endif]-->
<!--[if gte IE 8]>
<style type="text/css">
hr.grey { margin: 3px 0 3px 0;}
</style>
<![endif]-->
<!-- end standard Design stylesheet -->
</head>
<body>
<div id="container">
	<div id="head"></div>
	<div id="content">
		<div class="cols">
			<div class="col1">
				<div style="padding:10px;">
					<!-- clanname -->
					<span style="font-size:20px;"><?php echo $myclanname ?></span>
					<!-- end clanname -->
					<!-- quicksearch include -->
					<?php include("quicksearch.php"); ?><br style="line-height:1px;" />
					<!-- end clanname -->
					<hr class="grey" />
					<!-- poll include -->
					<b><?php echo $myclanname.".".$index_language['poll']; ?></b><br />
					<?php include("poll.php"); ?>
					<!-- end poll include -->
					<hr class="grey" />
					<!-- pic of the moment include -->
					<b><?php echo $myclanname.".".$index_language['pic_of_the_moment']; ?></b><br />
					<center><?php include("sc_potm.php"); ?></center>
					<!-- end pic of the moment include -->
					<hr class="grey" />
					<!-- language switch include -->
					<b><?php echo $myclanname.".".$index_language['language_switch']; ?></b><br />
					<center><?php include("sc_language.php"); ?></center>
					<!-- end language switch include -->
					<hr class="grey" />
					<!-- randompic include -->
					<b><?php echo $myclanname.".".$index_language['random_user']; ?></b><br />
					<?php include("sc_randompic.php"); ?>
					<!-- end randompic include -->
					<hr class="grey" />
					<!-- articles include -->
					<b><?php echo $myclanname.".".$index_language['articles']; ?></b><br />
					<?php include("sc_articles.php"); ?>
					<!-- end articles include -->
					<hr class="grey" />
					<!-- downloads include -->
					<b><?php echo $myclanname.".".$index_language['downloads']; ?></b><br />
					<?php include("sc_files.php"); ?>
					<!-- end downloads include -->
					<hr class="grey" />
					<!-- latest topics include -->
					<b><?php echo $myclanname.".".$index_language['topics']; ?></b><br />
					<?php include("latesttopics.php"); ?>
					<!-- end latest topics include -->
					<hr class="grey" />
					<!-- servers include -->
					<b><?php echo $myclanname.".".$index_language['server']; ?></b><br />
					<?php include("sc_servers.php"); ?>
					<!-- end servers include -->
					<hr class="grey" />
					<!-- sponsors include -->
					<b><?php echo $myclanname.".".$index_language['sponsors']; ?></b><br />
					<center><?php include("sc_sponsors.php"); ?></center>
					<!-- end sponsors include -->
					<hr class="grey" />
					<!-- tags include -->
					<b><?php echo $myclanname.".".$index_language['tags']; ?></b><br />
					<center><?php include("sc_tags.php"); ?></center>
					<!-- end tags include -->
					<hr class="grey" />
					<!-- partners include -->
					<b><?php echo $myclanname.".".$index_language['partners']; ?></b><br />
					<center><?php include("partners.php"); ?></center>
					<!-- end partners include -->
				</div>
			</div>
			<div class="col2">
				<div style="padding:10px;">
					<!-- navigation include -->
					<?php include("navigation.php"); ?>
					<!-- end navigation include -->
					<!-- scrolltext include -->
					<b><?php echo $myclanname.".".$index_language['scrolltext']; ?></b><br />
					<?php include("sc_scrolltext.php"); ?>
					<!-- end scrolltext include -->
					<hr class="grey" />
					<!-- bannerrotation include -->
					<b><?php echo $myclanname.".".$index_language['advertisement']; ?></b><br />
					<center><?php include("sc_bannerrotation.php"); ?></center>
					<!-- end bannerrotation include -->
					<hr class="grey" />
					<!-- content include -->
					<b><?php echo $myclanname.".".$index_language['content']; ?></b><br />
					<!-- php site include -->
                    <?php
				    if(isset($site) && $site!="news"){
				    $invalide = array('\\','/','//',':','.');
				    $site = str_replace($invalide,' ',$site);
					if(file_exists($site.'.php')) include($site.'.php');
					else include('404.php');
				    }
				    else include('news.php');
				    ?>
					<!-- content include -->
				</div>
			</div>
		</div>
		<div class="col3">
			<div style="padding:10px;">
				<!-- login include -->
				<b><?php echo $myclanname.".".$index_language['login']; ?></b><br />
				<?php include("login.php"); ?>
				<!-- end login include -->
				<hr class="grey" />
				<!-- topnews include -->
				<b><?php echo $myclanname.".".$index_language['hotest_news']; ?></b><br />
				<?php include("sc_topnews.php"); ?>
				<!-- topnews include -->
				<hr class="grey" />
				<!-- headlines include -->
				<b><?php echo $myclanname.".".$index_language['latest_news']; ?></b><br />
				<?php include("sc_headlines.php"); ?>
				<!-- end headlines include -->
				<hr class="grey" />
				<!-- squads include -->
				<b><?php echo $myclanname.".".$index_language['squads']; ?></b><br />
				<center><?php include("sc_squads.php"); ?></center>
				<!-- end squads include -->
				<hr class="grey" />
				<!-- clanwars include -->
				<b><?php echo $myclanname.".".$index_language['matches']; ?></b><br />
				<?php include("sc_results.php"); ?>
				<!-- end clanwars include -->
				<hr class="grey" />
				<!-- demos include -->
				<b><?php echo $myclanname.".".$index_language['demos']; ?></b><br />
				<?php include("sc_demos.php"); ?>
				<!-- end demos include -->
				<hr class="grey" />
				<!-- upcoming events include -->
				<b><?php echo $myclanname.".".$index_language['upcoming_events']; ?></b><br />
				<?php include("sc_upcoming.php"); ?>
				<!-- end upcoming events include -->
				<hr class="grey" />
				<!-- shoutbox include -->
				<b><?php echo $myclanname.".".$index_language['shoutbox']; ?></b><br />
				<center><?php include("shoutbox.php"); ?></center>
				<!-- end shoutbox include -->
				<hr class="grey" />
				<!-- newsletter include -->
				<b><?php echo $myclanname.".".$index_language['newsletter']; ?></b><br />
				<?php include("sc_newsletter.php"); ?>
				<!-- end newsletter include -->
				<hr class="grey" />
				<!-- statistics include -->
				<b><?php echo $myclanname.".".$index_language['statistics']; ?></b><br />
				<?php include("counter.php"); ?>
				<!-- end statistics include -->
			</div>
		</div>
		<div style="line-height:10px;clear:both;">&nbsp;</div>
	</div>
	<div id="footer">
		<br style="line-height:16px;" />Copyright by <b><?php echo $myclanname ?></b>&nbsp; | &nbsp;CMS powered by <a href="http://www.webspell.org" target="_blank"><b>webSPELL.org</b></a>&nbsp; | &nbsp;<a href="http://validator.w3.org/check?uri=referer" target="_blank">XHTML 1.0</a> &amp; <a href="http://jigsaw.w3.org/css-validator/check/refer" target="_blank">CSS 2.1</a> valid W3C standards&nbsp; | &nbsp;<a href="tmp/rss.xml" target="_blank"><img src="images/icons/rss.png" width="16" height="16" style="vertical-align:bottom;" alt="" /></a> <a href="tmp/rss.xml" target="_blank">RSS Feed</a>
		</div>
</div>
</body>
</html>