<?php
/**
 * Created by PhpStorm.
 * User: derchris
 * Date: 15/03/15
 * Time: 10:51
 */

include("translation_config.php");

$allLangFiles = glob($baseLanguageFolder."/*");
$all_keys = array();

foreach($allLangFiles as $file){
    ob_start();
    include($file);
    $content = ob_get_contents();
    $outputted_content = ob_get_length();
    ob_clean();

    foreach($language_array as $key => $value){
        if(isset($all_keys[$key])){
            $found = false;
            foreach($all_keys[$key] as $i => $c_v){
                $i_counter = $c_v[0];
                $i_value = $c_v[1];
                if($value == $i_value){
                    $i_counter++;
                    $all_keys[$key][$i] = array($i_counter, $i_value);
                    $found = true;
                    break;
                }
            }
            if($found == false){
                $all_keys[$key][] = array(1, $value);
            }
        }
        else{
            $all_keys[$key] = array();
            $all_keys[$key][] = array(1, $value);
        }
    }
}

$errors = array();

foreach($all_keys as $key => $usages){
    if(count($usages) > 1){
        $errors['multiple_different_usages'][] = $key . "- count ".count($usages)." -  ". implode(", ",array_map(function($e){return $e[1];}, $usages));
    }
    else{
        if($usages[0][0] > 1){
            $errors['multiple_definitionss'][] = $key . " - ". $usages[0][0];
        }
    }
}
sort($errors['multiple_different_usages']);
sort($errors['multiple_definitionss']);
print_r($errors);

/*
$lang_site = array('al', 'ba', 'bg', 'cz', 'de', 'dk', 'ee', 'es', 'fi', 'fr', 'ge', 'gr', 'hr', 'hu', 'ir', 'it', 'lt',
    'lu', 'lv', 'mk', 'nl', 'no', 'pl', 'pt', 'ro', 'rs', 'ru', 'sa', 'se', 'si', 'sk', 'tr', 'ua', 'uk', 'za');

$lang_admin = array('de', 'hu', 'it', 'uk');

$ref_keys_site = array();
$ref_keys_admin = array();
$duplicate_keys_site = array();
$duplicate_keys_admin = array();
define('PAGETITLE', "PAGETITLE");

foreach ($lang_site as $lang) {
    echo "Checking Site Language: " . $lang . " ... ", PHP_EOL;
    $files = glob('../../languages/' . $lang . '/*');
    foreach ($files as $file) {
        $file_name = basename($file);
        $ext = substr($file_name, strrpos($file_name, "."));
        if ($ext == ".php") {
            include_once$file;
            foreach ($language_array as $key => $value) {
                $dup = array_search($key, $ref_keys_site[$file_name]);
                if ($key != $dup) {
                    $duplicate_keys_site[$key] = $dup;
                }
            }
        }
    }
}

foreach ($lang_admin as $lang) {
    echo "Checking Admin Language: " . $lang . " ... ", PHP_EOL;
    $files = glob('../../admin/languages/' . $lang . '/*');
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
echo "Duplicate Language Keys in Site: " . implode(", ",array_keys($duplicate_keys_site)), PHP_EOL;
echo "Duplicate Language Keys in Admincenter: " . implode(", ",array_keys($duplicate_keys_admin)), PHP_EOL;
*/
