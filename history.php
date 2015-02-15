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

$_language->readModule('history');

$title_history = $GLOBALS["_template"]->replaceTemplate("title_history", array());
echo $title_history;

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "history");
if (mysqli_num_rows($ergebnis)) {
    $ds = mysqli_fetch_array($ergebnis);

    $history = htmloutput($ds[ 'history' ]);
    $history = toggle($history, 1);

    $bg1 = BG_1;
    $data_array = array();
    $data_array['$history'] = $history;
    $history = $GLOBALS["_template"]->replaceTemplate("history", $data_array);
    echo $history;
} else {
    echo $_language->module[ 'no_history' ];
}
