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

$getlist = safe_query("SELECT sc_files FROM " . PREFIX . "settings");
$ds = mysqli_fetch_array($getlist);

if ($ds[ 'sc_files' ] == 1) {
    $list = "downloads";
} else {
    $list = "date";
}

$accesslevel = 1;
if (isclanmember($userID)) {
    $accesslevel = 2;
}

$ergebnis = safe_query(
    "SELECT
        *
    FROM
        " . PREFIX . "files
    WHERE
        accesslevel<=" . $accesslevel . "
    ORDER BY
        " . $list . " DESC
    LIMIT 0,5"
);
$n = 1;
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="list-group">';
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $fileID = $ds[ 'fileID' ];
        $count = $ds[ 'downloads' ];
        $filename = $ds[ 'filename' ];
        $number = $n;

        if ($n % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        $data_array = array();
        $data_array['$count'] = $count;
        $data_array['$fileID'] = $fileID;
        $data_array['$filename'] = $filename;
        $sc_files = $GLOBALS["_template"]->replaceTemplate("sc_files", $data_array);
        echo $sc_files;

        $n++;
    }
    echo '</ul>';
}
