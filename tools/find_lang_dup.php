<?php
/**
 * Created by PhpStorm.
 * User: derchris
 * Date: 15/03/15
 * Time: 10:51
 */

$lang_site = array('al', 'ba', 'bg', 'cz', 'de', 'dk', 'ee', 'es', 'fi', 'fr', 'ge', 'gr', 'hr', 'hu', 'ir', 'it', 'lt',
    'lu', 'lv', 'mk', 'nl', 'no', 'pl', 'pt', 'ro', 'rs', 'ru', 'sa', 'se', 'si', 'sk', 'tr', 'ua', 'uk', 'za');

$lang_admin = array('de', 'hu', 'it', 'uk');

$ref_keys_site = array();
$ref_keys_admin = array();
$duplicate_keys_site = array();
$duplicate_keys_admin = array();
$all_keys_site = '';
$all_keys_admin = '';
define('PAGETITLE', "PAGETITLE");

foreach ($lang_site as $lang) {
    echo "Checking Site Language: " . $lang . " ... ", PHP_EOL;
    $files = glob('../languages/' . $lang . '/*');
    foreach ($files as $file) {
        $file_name = basename($file);
        $ext = substr($file_name, strrpos($file_name, "."));
        if ($ext == ".php") {
            include_once$file;
            $ref_keys_site[$file_name] = $language_array;
        }
    }
    foreach ($ref_keys_site[$file_name] as $key => $value) {
        $dup = array_search($key, $ref_keys_site[$file_name]);
        if ($key != $dup) {
            $duplicate_keys_site[$key] = $dup;
        }
    }
}

foreach ($lang_admin as $lang) {
    echo "Checking Admin Language: " . $lang . " ... ", PHP_EOL;
    $files = glob('../admin/languages/' . $lang . '/*');
    foreach ($files as $file) {
        $file_name = basename($file);
        $ext = substr($file_name, strrpos($file_name, "."));
        if ($ext == ".php") {
            include_once$file;
            $ref_keys_admin[$file_name] = $language_array;
        }
    }
    foreach ($ref_keys_admin[$file_name] as $key => $value) {
        $dup = array_search($key, $ref_keys_admin[$file_name]);
        if ($key != $dup) {
            $duplicate_keys_admin[$key] = $dup;
        }
    }
    #while($element = current($duplicate_keys_admin)) {
    #    $all_keys_admin .= key($duplicate_keys_admin);
    #    next($duplicate_keys_admin);
    #}
    #print_r($duplicate_keys_admin);
}
echo "", PHP_EOL;

foreach ($duplicate_keys_site as $key => $value) {
    $all_keys_site .= $key . ", ";
}
echo "Duplicate Language Keys in Site: " . $all_keys_site, PHP_EOL;

foreach ($duplicate_keys_admin as $key => $value) {
    $all_keys_admin .= $key . ", ";
}
echo "Duplicate Language Keys in Admincenter: " . $all_keys_admin, PHP_EOL;
