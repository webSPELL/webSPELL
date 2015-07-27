<?php
$languageBaseFolder = "../../languages/";
$baseLanguageCode = 'uk';

$baseLanguageFolder = $languageBaseFolder.$baseLanguageCode;

$file_header = '<?php
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

$language_array = array(

/* do not edit above this line */'."\n\n";

$file_footer = ');'."\n\n";


$sortMode = SORT_REGULAR;
if (defined("SORT_NATURAL")) {
    $sortMode = SORT_NATURAL;
}

define("PAGETITLE","PAGETITLE");

function fixGlobals($string)
{
    return str_replace("PAGETITLE", "'.PAGETITLE.'", $string);
}

function escape($string)
{
    return addcslashes($string, "'");
}

function checkBom($file)
{
    return (false !== strpos($file, "\xEF\xBB\xBF"));
}

function writeLanguageFile($file, $language_array){
    global $file_header, $file_footer, $sortMode;
    $rows = array();
    ksort($language_array, $sortMode);
    foreach ($language_array as $lang_key => $lang_val) {
        $rows[] = "    '".escape($lang_key)."' => '".fixGlobals(escape($lang_val))."'";
    }

    $new_array = implode(",\n", $rows)."\n";

    $new_content = $file_header.$new_array.$file_footer;

    file_put_contents($file, $new_content);
}
