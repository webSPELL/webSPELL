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

$_language->readModule('sc_scrolltext');

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "scrolltext");
if (mysqli_num_rows($ergebnis)) {
    $ds = mysqli_fetch_array($ergebnis);

    $scrolltext = js_replace($ds[ 'text' ]);
    $direction = $ds[ 'direction' ];
    $delay = $ds[ 'delay' ].'s';
    $color = $ds[ 'color' ];

    if ($direction == 'right') {
        $css_animation = '
@-webkit-keyframes marquee {
    0%   { -webkit-transform: translate(0, 0); }
    100% { -webkit-transform: translate(100%, 0); }
}
@-moz-keyframes {
    0%   { -moz-transform: translate(0, 0); }
    100% { -moz-transform: translate(100%, 0); }
}
@keyframes marquee {
    0%   { transform: translate(0, 0); }
    100% { transform: translate(100%, 0); }
}';
    } elseif ($direction == 'left') {
        $css_animation = '
@-webkit-keyframes marquee {
    0%   { -webkit-transform: translate(0, 0); }
    100% { -webkit-transform: translate(-100%, 0); }
}
@-moz-keyframes {
    0%   { -moz-transform: translate(0, 0); }
    100% { -moz-transform: translate(-100%, 0); }
}
@keyframes marquee {
    0%   { transform: translate(0, 0); }
    100% { transform: translate(-100%, 0); }
}';
    }

    eval ("\$sc_scrolltext = \"" . gettemplate("sc_scrolltext") . "\";");
    echo $sc_scrolltext;
} else {
    echo $_language->module[ 'no_text' ];
}
