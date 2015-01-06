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

if (isset($_GET[ 'new_lang' ])) {

    if (file_exists('languages/' . $_GET[ 'new_lang' ])) {

        include("_mysql.php");
        include("_settings.php");
        include("_functions.php");
        if ($userID) {
            $lang = $_GET[ 'new_lang' ];
            safe_query("UPDATE " . PREFIX . "user SET language='" . $lang . "' WHERE userID='" . $userID . "'");
        } else {
            $_SESSION[ 'language' ] = $_GET[ 'new_lang' ];
        }
    }

    if (isset($_GET[ 'query' ])) {

        $query = rawurldecode($_GET[ 'query' ]);
        header("Location: ./" . $query);
    } else {
        header("Location: index.php");
    }
} else {

    $_language->readModule('sc_language');

    $filepath = "languages/";
    $langs = [];
    // Select all possible languages
    $mysql_langs = [];
    $query = safe_query("SELECT lang, language FROM " . PREFIX . "news_languages");
    while ($ds = mysqli_fetch_assoc($query)) {
        $mysql_langs[ $ds[ 'lang' ] ] = $ds[ 'language' ];
    }

    if ($dh = opendir($filepath)) {
        while ($file = mb_substr(readdir($dh), 0, 2)) {
            if ($file != "." && $file != ".." && is_dir($filepath . $file)) {
                if (isset($mysql_langs[ $file ])) {
                    $name = $mysql_langs[ $file ];
                    $name = ucfirst($name);
                    $langs[ $name ] = $file;
                } else {
                    $langs[ $file ] = $file;
                }
            }
        }
        closedir($dh);
    }
    if (defined("SORT_NATURAL")) {
        $sortMode = SORT_NATURAL;
    } else {
        $sortMode = SORT_LOCALE_STRING;
    }
    ksort($langs, $sortMode);

    $querystring = '';
    if ($modRewrite == true) {
        $path = rawurlencode(str_replace($GLOBALS[ 'rewriteBase' ], '', $_SERVER[ 'REQUEST_URI' ]));

    } else {
        $path = rawurlencode($_SERVER[ 'QUERY_STRING' ]);
        if (!empty($path)) {
            $path = "?".$path;
        }
    }
    if (!empty($path)) {
        $querystring = "&amp;query=" . $path;
    }

    foreach ($langs as $lang => $flag) {
        echo '<a href="sc_language.php?new_lang=' . $flag . $querystring . '" title="' . $lang . '" class="flag' .
            ($_language->language == $flag ? ' active' : '') . '"><img src="images/flags/' . $flag . '.gif" alt="' .
            $lang . '"></a>';
    }
}
