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

$ergebnis = safe_query(
    "SELECT
        date,
        title,
        articlesID
    FROM
        " . PREFIX . "articles
    WHERE
        saved='1'
    ORDER BY
        date DESC
    LIMIT 0, " . (int)$latestarticles
);
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="list-group">';
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $date = getformatdate($ds[ 'date' ]);
        $time = getformattime($ds[ 'date' ]);
        $title = $ds[ 'title' ];
        $articlesID = $ds[ 'articlesID' ];

        if (mb_strlen($title) > $articleschars) {
            $title = mb_substr($title, 0, $articleschars);
            $title .= '..';
        }

        eval("\$sc_articles = \"" . gettemplate("sc_articles") . "\";");
        echo $sc_articles;
    }
    echo '</ul>';
}
