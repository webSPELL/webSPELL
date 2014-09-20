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
// end important data include

$hide1 = array("forum","forum_topic");
header('X-UA-Compatible: IE=edge,chrome=1');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" content="Clanpage using webSPELL 4 CMS">
<meta name="author" content="webspell.org">
<meta name="keywords" content="webspell, webspell4, clan, cms">
<meta name="generator" content="webSPELL">

<!-- Head & Title include -->
<title><?php echo PAGETITLE; ?></title>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="tmp/rss.xml" rel="alternate" type="application/rss+xml" title="<?php echo getinput($myclanname); ?> - RSS Feed">

<script src="js/bbcode.js"></script>
<!-- end Head & Title include -->

</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><?php echo $myclanname ?></a>
            </div>
            
            <div class="navbar-collapse collapse"> 
                <?php include("navigation.php"); ?>
            </div>
            
        </div><!-- container -->
</div><!-- navbar navbar-inverse navbar-fixed-top -->

<div class="ws_main_wrapper">    
    
    <div class="container">
        
        <div class="row">

<?php if(!in_array($site, $hide1)) { // show left column ?>
            <!-- left column -->
            <div id="leftcol" class="col-lg-3 visible-lg">
                <!-- poll include -->
                <b><?php echo $myclanname.".".$index_language['poll']; ?></b><br>
                <?php include("poll.php"); ?>
                <!-- end poll include -->
                <hr class="grey">
                
                <!-- pic of the moment include -->
                <b><?php echo $myclanname.".".$index_language['pic_of_the_moment']; ?></b><br>
                <p class="text-center"><?php include("sc_potm.php"); ?></p>
                <!-- end pic of the moment include -->
                <hr class="grey">
                
                <!-- language switch include -->
                <b><?php echo $myclanname.".".$index_language['language_switch']; ?></b><br>
                <p class="text-center"><?php include("sc_language.php"); ?></p>
                <!-- end language switch include -->
                <hr class="grey">
                
                <!-- randompic include -->
                <b><?php echo $myclanname.".".$index_language['random_user']; ?></b><br>
                <?php include("sc_randompic.php"); ?>
                <!-- end randompic include -->
                <hr class="grey">
                
                <!-- articles include -->
                <b><?php echo $myclanname.".".$index_language['articles']; ?></b><br>
                <?php include("sc_articles.php"); ?>
                <!-- end articles include -->
                <hr class="grey">
                
                <!-- downloads include -->
                <b><?php echo $myclanname.".".$index_language['downloads']; ?></b><br>
                <?php include("sc_files.php"); ?>
                <!-- end downloads include -->
                <hr class="grey">
                
                <!-- servers include -->
                <b><?php echo $myclanname.".".$index_language['server']; ?></b><br>
                <?php include("sc_servers.php"); ?>
                <!-- end servers include -->
                <hr class="grey">
                
                <!-- sponsors include -->
                <b><?php echo $myclanname.".".$index_language['sponsors']; ?></b><br>
                <p class="text-center"><?php include("sc_sponsors.php"); ?></p>
                <!-- end sponsors include -->
                <hr class="grey">
                
                <!-- partners include -->
                <b><?php echo $myclanname.".".$index_language['partners']; ?></b><br>
                <p class="text-center"><?php include("partners.php"); ?></p>
                <!-- end partners include -->
            </div>
<?php } // end of show left column ?>            
            <!-- main content area -->
            <div id="maincol" class="<?php if(in_array($site, $hide1)) echo "col-lg-9 col-sm-9 col-xs-12"; else echo"col-lg-6 col-sm-9 col-xs-12"; ?>">
                <?php
                    if(!isset($site)) $site="news";
                    $invalide = array('\\','/','/\/',':','.');
                    $site = str_replace($invalide,' ',$site);
                    if(!file_exists($site.".php")) $site = "news";
                    include($site.".php");
                ?>
            </div>
             
            <!-- right column -->
            <div id="rightcol" class="col-md-3 col-sm-3">
                <!-- login include -->
                <div>
                    <b><?php echo $myclanname.".".$index_language['login']; ?></b><br>
                    <?php include("login.php"); ?>
                    <hr class="grey">
                </div>
                
                <div class="visible-sm<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <b><?php echo $myclanname.".".$index_language['topics']; ?></b><br>
                    <?php include("latesttopics.php"); ?>
                </div>
                
                <div class="visible-lg<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <b><?php echo $myclanname.".".$index_language['hotest_news']; ?></b><br>
                    <?php include("sc_topnews.php"); ?>
                    <hr class="grey">
                </div>
                
                <div class="visible-sm<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <!-- headlines include -->
                    <b><?php echo $myclanname.".".$index_language['latest_news']; ?></b><br>
                    <?php include("sc_headlines.php"); ?>
                    <!-- end headlines include -->
                    <hr class="grey">
                </div>
                
                <div class="visible-lg<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <!-- squads include -->
                    <b><?php echo $myclanname.".".$index_language['squads']; ?></b><br>
                    <p class="text-center"><?php include("sc_squads.php"); ?></p>
                    <!-- end squads include -->
                    <hr class="grey">
                </div>
                
                <div class="visible-sm<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <!-- clanwars include -->
                    <b><?php echo $myclanname.".".$index_language['matches']; ?></b><br>
                    <?php include("sc_results.php"); ?>
                    <!-- end clanwars include -->
                    <hr class="grey">
                </div>
                
                <div class="visible-lg<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <!-- demos include -->
                    <b><?php echo $myclanname.".".$index_language['demos']; ?></b><br>
                    <?php include("sc_demos.php"); ?>
                    <!-- end demos include -->
                    <hr class="grey">
                </div>
                
                <div class="visible-lg<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <!-- upcoming events include -->
                    <b><?php echo $myclanname.".".$index_language['upcoming_events']; ?></b><br>
                    <?php include("sc_upcoming.php"); ?>
                    <!-- end upcoming events include -->
                    <hr class="grey">
                </div>
                
                <div class="visible-lg<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <!-- shoutbox include -->
                    <b><?php echo $myclanname.".".$index_language['shoutbox']; ?></b><br>
                    <p class="text-center"><?php include("shoutbox.php"); ?></p>
                    <!-- end shoutbox include -->
                    <hr class="grey">
                </div>
                
                <div class="visible-lg<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <hr class="grey">
                    <!-- newsletter include -->
                    <b><?php echo $myclanname.".".$index_language['newsletter']; ?></b><br>
                    <?php include("sc_newsletter.php"); ?>
                    <!-- end newsletter include -->
                </div>
                
                <div class="visible-lg<?php if(in_array($site, $hide1)) echo " hidden"; ?>">
                    <!-- statistics include -->
                    <b><?php echo $myclanname.".".$index_language['statistics']; ?></b><br>
                    <?php include("counter.php"); ?>
                    <!-- end statistics include -->
                    <hr class="grey">
                </div>
            </div>
        </div>
    </div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<script src="js/wSBs.js" type="text/javascript"></script>

</body>
</html>
