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
#   Copyright 2005-2015 by webspell.org                                  #
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

$_language->readModule('index');
$index_language = $_language->module;
// end important data include

$hide1 = array("forum", "forum_topic");
header('X-UA-Compatible: IE=edge,chrome=1');
?>
<!DOCTYPE html>
<html>
<head>
    <?php
    if (
        (isset($_SESSION[ 'language' ]) && ($_SESSION[ 'language' ] == 'ac')) ||
        (isset($_COOKIE[ 'language' ]) && ($_COOKIE[ 'language' ] == 'ac'))
    ) {
        echo '<script type="text/javascript">
            var _jipt = [];
            _jipt.push([\'project\', \'webspell-cms\']);
        </script>
        <script type="text/javascript" src="//cdn.crowdin.com/jipt/jipt.js"></script>';
    }
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="description" content="Clanpage using webSPELL 4 CMS">
    <meta name="author" content="webspell.org">
    <meta name="generator" content="webSPELL">

    <!-- Head & Title include -->
    <title><?php echo PAGETITLE; ?></title>
    <base href="<?php echo $rewriteBase; ?>">
    <?php foreach ($components['css'] as $component) {
        echo '<link href="' . $component . '" rel="stylesheet">';
}
    ?>
    <link href="_stylesheet.css" rel="stylesheet">
    <link href="tmp/rss.xml" rel="alternate" type="application/rss+xml" title="<?php echo $myclanname; ?> - RSS Feed">
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
            <ul class="nav navbar-nav visible-xs">
                <li class="dropdown">
                    <?php if($loggedin) {
                        echo
                            '<a href="index.php?site=loginoverview">' . getnickname($userID) . '</a>';
                    } else {
                        echo '<a href="index.php?site=login">' . ucfirst($index_language[ 'login' ]) . '</a>';
                    } ?>
                </li>
            </ul>

            <?php include("navigation.php"); ?>
        </div>

    </div>
    <!-- container -->
</div>
<!-- navbar navbar-inverse navbar-fixed-top -->

<div class="ws_main_wrapper">

    <div class="container">

        <div class="row">

            <?php // show left column
            if (!in_array($site, $hide1)) {
?>
                <!-- left column -->
                <div id="leftcol" class="col-lg-3 visible-lg">
                    <!-- poll include -->
                    <h3><?php echo $index_language[ 'poll' ]; ?></h3>
                    <?php include("poll.php"); ?>
                    <!-- end poll include -->
                    <hr class="grey">

                    <!-- pic of the moment include -->
                    <h3><?php echo $index_language[ 'pic_of_the_moment' ]; ?></h3>

                    <?php include("sc_potm.php"); ?>
                    <!-- end pic of the moment include -->
                    <hr class="grey">

                    <!-- language switch include -->
                    <h3><?php echo $index_language[ 'language_switch' ]; ?></h3>

                    <?php include("sc_language.php"); ?>
                    <!-- end language switch include -->
                    <hr class="grey">

                    <!-- randompic include -->
                    <h3><?php echo $index_language[ 'random_user' ]; ?></h3>
                    <?php include("sc_randompic.php"); ?>
                    <!-- end randompic include -->
                    <hr class="grey">

                    <!-- articles include -->
                    <h3><?php echo $index_language[ 'articles' ]; ?></h3>
                    <?php include("sc_articles.php"); ?>
                    <!-- end articles include -->
                    <hr class="grey">

                    <!-- downloads include -->
                    <h3><?php echo $index_language[ 'downloads' ]; ?></h3>
                    <?php include("sc_files.php"); ?>
                    <!-- end downloads include -->
                    <hr class="grey">

                    <!-- servers include -->
                    <h3><?php echo $index_language[ 'server' ]; ?></h3>
                    <?php include("sc_servers.php"); ?>
                    <!-- end servers include -->
                    <hr class="grey">

                    <!-- sponsors include -->
                    <h3><?php echo $index_language[ 'sponsors' ]; ?></h3>

                    <?php include("sc_sponsors.php"); ?>
                    <!-- end sponsors include -->
                    <hr class="grey">

                    <!-- partners include -->
                    <h3><?php echo $index_language[ 'partners' ]; ?></h3>

                    <?php include("partners.php"); ?>
                    <!-- end partners include -->
                </div>
            <?php
            // end of show left column
            } ?>
            <!-- main content area -->
            <div id="maincol" class="
            <?php
            if (in_array($site, $hide1)) {
                echo "col-lg-9 col-sm-9 col-xs-12";
            } else {
                echo "col-lg-6 col-sm-9 col-xs-12";
            }
            ?>">
                <?php
                if (!isset($site)) {
                    $site = "news";
                }
                $invalide = array('\\', '/', '/\/', ':', '.');
                $site = str_replace($invalide, ' ', $site);
                if (!file_exists($site . ".php")) {
                    $site = "news";
                }
                include($site . ".php");
                ?>
            </div>

            <!-- right column -->
            <div id="rightcol" class="col-md-3 col-sm-3 hidden-xs">
                <!-- login include -->
                <div class="hidden-xs">
                    <h3><?php echo $index_language[ 'login' ]; ?></h3>
                    <?php include("login.php"); ?>
                    <hr class="grey">
                </div>

                <div class="visible-sm">
                    <h3><?php echo $index_language[ 'topics' ]; ?></h3>
                    <?php include("latesttopics.php"); ?>
                </div>

                <div class="visible-lg">
                    <h3><?php echo $index_language[ 'hotest_news' ]; ?></h3>
                    <?php include("sc_topnews.php"); ?>
                    <hr class="grey">
                </div>

                <div class="visible-sm">
                    <!-- headlines include -->
                    <h3><?php echo $index_language[ 'latest_news' ]; ?></h3>
                    <?php include("sc_headlines.php"); ?>
                    <!-- end headlines include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- squads include -->
                    <h3><?php echo $index_language[ 'squads' ]; ?></h3>

                    <?php include("sc_squads.php"); ?>
                    <!-- end squads include -->
                    <hr class="grey">
                </div>

                <div class="visible-sm">
                    <!-- clanwars include -->
                    <h3><?php echo $index_language[ 'matches' ]; ?></h3>
                    <?php include("sc_results.php"); ?>
                    <!-- end clanwars include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- demos include -->
                    <h3><?php echo $index_language[ 'demos' ]; ?></h3>
                    <?php include("sc_demos.php"); ?>
                    <!-- end demos include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- upcoming events include -->
                    <h3><?php echo $index_language[ 'upcoming_events' ]; ?></h3>
                    <?php include("sc_upcoming.php"); ?>
                    <!-- end upcoming events include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- shoutbox include -->
                    <h3><?php echo $index_language[ 'shoutbox' ]; ?></h3>

                    <?php include("shoutbox.php"); ?>
                    <!-- end shoutbox include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <hr class="grey">
                    <!-- newsletter include -->
                    <h3><?php echo $index_language[ 'newsletter' ]; ?></h3>
                    <?php include("sc_newsletter.php"); ?>
                    <!-- end newsletter include -->
                </div>

                <div class="visible-lg">
                    <!-- statistics include -->
                    <h3><?php echo $index_language[ 'statistics' ]; ?></h3>
                    <?php include("counter.php"); ?>
                    <!-- end statistics include -->
                    <hr class="grey">
                </div>
            </div>
        </div>
    </div>
</div>
<?php foreach ($components['js'] as $component) {
    echo '<script src="' . $component . '"></script>';
}
?>
<script>
    webshim.setOptions('basePath', 'components/webshim/js-webshim/minified/shims/');
    //request the features you need:
    webshim.setOptions("forms-ext",
    {
        replaceUI: false,
        types: "date time datetime-local"
    });
    webshim.polyfill('forms forms-ext');
</script>
<script src="js/bbcode.js" type="text/javascript"></script>
</body>
</html>
