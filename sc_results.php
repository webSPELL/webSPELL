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

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "clanwars ORDER BY date DESC LIMIT 0, " . $maxresults);
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="list-group">';
    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {

        $date = getformatdate($ds[ 'date' ]);
        $homescr = array_sum(unserialize($ds[ 'homescore' ]));
        $oppscr = array_sum(unserialize($ds[ 'oppscore' ]));

        if ($n % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        if ($homescr > $oppscr) {
            $result = '<font color="' . $wincolor . '">' . $homescr . ':' . $oppscr . '</font>';
        } elseif ($homescr < $oppscr) {
            $result = '<font color="' . $loosecolor . '">' . $homescr . ':' . $oppscr . '</font>';
        } else {
            $result = '<font color="' . $drawcolor . '">' . $homescr . ':' . $oppscr . '</font>';
        }

        $resultID = $ds[ 'cwID' ];
        $gameicon = "images/games/";
        if (file_exists($gameicon . $ds[ 'game' ] . ".gif")) {
            $gameicon = $gameicon . $ds[ 'game' ] . ".gif";
        }

        eval ("\$results = \"" . gettemplate("results") . "\";");
        echo $results;
        $n++;
    }
    echo '</ul>';
}
