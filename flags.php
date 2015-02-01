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
include("_functions.php");
$componentsCss = generateComponents($components['css'], 'css');
$componentsJs = generateComponents($components['js'], 'js');
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="description" content="Clanpage using webSPELL 4 CMS">
    <meta name="author" content="webspell.org">
    <meta name="copyright" content="Copyright 2005-2014 by webspell.org">
    <meta name="generator" content="webSPELL">
    <title>Flags</title>
    <base href="'.$rewriteBase.'">
    '.$componentsCss.'
</head>
<body>
<table class="table table-striped">
    <tr>
        <th>Flag:</th>
        <th>Tag:</th>
    </tr>';


$filepath = "./images/flags/";
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
        $flag = explode(".", $file);

        echo '<tr>
            <td align="center"><a href="javascript:AddCodeFromWindow(\'[flag]' . $flag[ 0 ] .
                '[/flag]\')"><img src="images/flags/' . $file . '" alt=""></a></td>
            <td align="center"><a href="javascript:AddCodeFromWindow(\'[flag]' . $flag[ 0 ] .
                '[/flag]\')">' . $flag[ 0 ] . '</a></td>
        </tr>';
    }
}
echo $componentsJs;
echo '<script src="js/bbcode.js"></script>';
echo '</table></body></html>';
