<?php

header("Content-Type: text/plain; charset=utf-8");

$all_langs = glob("../languages/*", GLOB_ONLYDIR);

$header = '<?php
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
#   - It\'s NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

$language_array = Array(

/* do not edit above this line */'."\n\n";

$footer = ');'."\n";

function escape($string)
{
    return addcslashes($string, "'");
}

$sortMode = SORT_REGULAR;
if (defined("SORT_NATURAL")) {
    $sortMode = SORT_NATURAL;
}

function fixGlobals($string)
{
    return str_replace("PAGETITLE", "'.PAGETITLE.'", $string);
}

$filename = $argv[1];
$new_key = $argv[2];
$new_val = $argv[3];

echo "file to edit: ".$filename."\n";

//define("PAGETITLE","'.PAGETITLE.'");

$all_keys = 0;
foreach ($all_langs as $lang) {
    $files = glob($lang . '/*');
    foreach ($files as $file) {
        $file_name = basename($file);
        $ext = substr($file_name, strrpos($file_name, "."));
        if ($file_name === $filename) {
            ob_start();
            include($file);
            $outputted_content = ob_get_length();
            ob_clean();

            $rows = array();
            if(!isset($language_array[$new_key])){
                $language_array[$new_key] = $new_val;
            }
            ksort($language_array, $sortMode);
            foreach ($language_array as $lang_key => $lang_val) {
                $rows[] = "    '".escape($lang_key)."' => '".fixGlobals(escape($lang_val))."'";
            }

            $new_array = implode(",\n", $rows)."\n";

            $new_content = $header.$new_array.$footer;

            file_put_contents($file, $new_content);
        }
    }
    echo "ok\n";
}
