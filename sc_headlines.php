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

if (isset($rubricID) and $rubricID) {
    $only = "AND rubric='" . $rubricID . "'";
} else {
    $only = '';
}

$ergebnis = safe_query(
    "SELECT
        *
    FROM
        " . PREFIX . "news
    WHERE
        published='1' " . $only . " AND
        intern<=" . (int)isclanmember($userID) . "
    ORDER BY
        date DESC
    LIMIT 0," . (int)$maxheadlines
);
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="nav nav-pills">';
    $n = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $date = getformatdate($ds[ 'date' ]);
        $time = getformattime($ds[ 'date' ]);
        $news_id = $ds[ 'newsID' ];

        if ($n % 2) {
            $bg1 = BG_1;
            $bg2 = BG_2;
        } else {
            $bg1 = BG_3;
            $bg2 = BG_4;
        }

        $message_array = [];
        $query =
            safe_query(
                "SELECT
                    n.*,
                    c.short AS `countryCode`,
                    c.country
                FROM
                    " . PREFIX . "news_contents n
                LEFT JOIN
                    " . PREFIX . "countries c ON
                    c.short = n.language
                WHERE
                    n.newsID='" . (int)$ds[ 'newsID' ]
            );
        while ($qs = mysqli_fetch_array($query)) {
            $message_array[ ] = [
                'lang' => $qs[ 'language' ],
                'headline' => $qs[ 'headline' ],
                'message' => $qs[ 'content' ],
                'country' => $qs[ 'country' ],
                'countryShort' => $qs[ 'countryCode' ]
            ];
        }
        $showlang = select_language($message_array);

        $languages = '';
        $i = 0;
        foreach ($message_array as $val) {
            if ($showlang != $i) {
                $languages .= '<span style="padding-left:2px"><a href="index.php?site=news_comments&amp;newsID=' .
                    $ds[ 'newsID' ] . '&amp;lang=' . $val[ 'lang' ] . '"><img src="images/flags/' .
                    $val[ 'countryShort' ] . '.gif" width="18" height="12" alt="' . $val[ 'country' ] . '"></a></span>';
            }
            $i++;
        }

        $lang = $message_array[ $showlang ][ 'lang' ];

        $headlines = $message_array[ $showlang ][ 'headline' ];

        if (mb_strlen($headlines) > $maxheadlinechars) {
            $headlines = mb_substr($headlines, 0, $maxheadlinechars);
            $headlines .= '...';
        }

        $headlines = clearfromtags($headlines);

        eval ("\$sc_headlines = \"" . gettemplate("sc_headlines") . "\";");
        echo $sc_headlines;

        $n++;
    }
    echo '</ul>';
    unset($rubricID);
}
