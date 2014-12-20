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
#   Copyright 2005-2014 by webspell.org                                  #
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

include("_mysql.php");
include("_settings.php");

echo '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="description" content="Clanpage using webSPELL 4 CMS">
	<meta name="author" content="webspell.org">
	<meta name="copyright" content="Copyright 2005-2014 by webspell.org">
	<meta name="generator" content="webSPELL">
	<title>Smilies</title>';
$componentsCss = generateComponents($components['css'], 'css');
foreach ($componentsCss as $component) {
    echo $component;
}
    echo '<link href="_stylesheet.css" rel="stylesheet">
    <script src="js/bbcode.js"></script>
</head>

<body>
<div class="container">
<div class="page-header">
    <h1>Smilies</h1>
</div>
<table class="table table-striped table-bordered text-center">
    <thead>
        <tr>
            <th>Smiley:</th>
            <th>Tag:</th>
        </tr>
    </thead>
    <tbody>';


$filepath = "./images/smileys/";
unset($files);
if ($dh = opendir($filepath)) {
    while ($file = readdir($dh)) {
        if (preg_match("/\.gif/si", $file)) {
            $files[ ] = $file;
        }
    }
    closedir($dh);
}

if (is_array($files)) {
    sort($files);
    foreach ($files as $file) {
        $smiley = explode(".", $file);

        echo '<tr>
        <td><a href="javascript:AddCodeFromWindow(\':' . $smiley[ 0 ] . ':\')">
                <img src="images/smileys/' . $file . '" alt="">
            </a>
        </td>
        <td><a href="javascript:AddCodeFromWindow(\':' . $smiley[ 0 ] . ':\')">:' . $smiley[ 0 ] . ':</a></td>
      </tr>';
    }
}

echo '</tbody>
</table>
</div>
</body>
</html>';
