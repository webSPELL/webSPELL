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

//Options:
$list = 1; //1=top 5 downloads , 2=latest 5 downloads

//dont edit above this line

if ($list == 1) {
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

        eval ("\$sc_files = \"" . gettemplate("sc_files") . "\";");
        echo $sc_files;

        $n++;
    }
    echo '</ul>';
}
