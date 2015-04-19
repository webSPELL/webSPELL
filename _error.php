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

function system_error($text, $system = 1)
{

    ob_clean();
    global $_database;

    if ($system) {
        include('version.php');
        $info = 'webSPELL Version: ' . $version . ', PHP Version: ' . phpversion();
        if (!mysqli_connect_error()) {
            $info .= ', MySQL Version: ' . $_database->server_info;
        }
    } else {
        $info = 'webSPELL';
    }
    die('<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="utf-8">
<meta name="description" content="Clanpage using webSPELL 4 CMS">
<meta name="author" content="webspell.org">
<meta name="keywords" content="webspell, webspell4, clan, cms">
<meta name="generator" content="webSPELL">

<!-- Head & Title include -->
<title>webSPELL - Error</title>
<link href="components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="_stylesheet.css" rel="stylesheet">
<style>
/* centered columns styles */
.row-centered {
    text-align:center;
}
.col-centered {
    display:inline-block;
    float:none;
    /* reset the text-align */
    text-align:left;
    /* inline-block space fix */
    margin-right:-4px;
}
</style>
<!-- end Head & Title include -->
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <p class="navbar-text">' . $info . '</p>
        </div>

        <div class="navbar-collapse collapse">
            <?php include("navigation.php"); ?>
        </div>

    </div>
    <!-- container -->
</div>

<div class="container">
    <div class="row row-centered">
        <!-- main content area -->
        <div class="col-xs-12 col-sm-6 col-md-8 col-centered">
            <div>
                <div class="alert alert-danger" role="alert"><strong>An error has occured</strong></div>
            </div>
            <div class="alert alert-info" role="alert">
                ' . $text .'
            </div>
        </div>
    </div>
</div>
<script src="components/jquery/dist/jquery.min.js"></script>
<script src="components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>');
}
