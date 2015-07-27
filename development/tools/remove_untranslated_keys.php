<?php

header("Content-Type: text/plain; charset=utf-8");

include("translation_config.php");

$checkUntranslated = true;

$all_langs = glob($languageBaseFolder."*", GLOB_ONLYDIR);
if (in_array($baseLanguageFolder, $all_langs)) {
    unset($all_langs[ array_search($baseLanguageFolder, $all_langs) ]);
}

$ref_keys = array();
$erros = array();
$all_langs = array_merge(array($baseLanguageFolder), $all_langs);

echo "Base Language: ".$baseLanguageCode."\n";

$all_keys = 0;
foreach ($all_langs as $lang) {
    $langCode = basename($lang);
    echo "Checking " . $langCode . " ... ";
    $errors = array();
    $files = glob($lang . '/*');
    $untranslated = 0;
    $version_exists = false;
    foreach ($files as $file) {
        $file_name = basename($file);
        $ext = substr($file_name, strrpos($file_name, "."));
        if ($ext == ".php") {
            if (checkBom($file) !== false) {
                $errors[ $file_name ][ ] = 'UTF-8 BOM';
            }
            ob_start();
            include($file);
            $content = ob_get_contents();
            $outputted_content = ob_get_length();
            ob_clean();
            if ($langCode === $baseLanguageCode) {
                $ref_keys[ $file_name ] = $language_array;
                $all_keys += count($language_array);
            } else {
                if (isset($ref_keys[ $file_name ])) {
                    $tmp = $ref_keys[ $file_name ];
                    foreach ($language_array as $key => $val) {
                        if (!isset($ref_keys[ $file_name ][ $key ])) {
                            $errors[ ] = "removing unneeded key ".$key."\n";
                            unset($language_array[ $key ]);
                        } else {
                            if ($val == $ref_keys[$file_name][$key]) {
                                $errors[ ] = "removing untranslated key ".$key."\n";
                                unset($language_array[ $key ]);
                            }
                        }
                    }
                    writeLanguageFile($file, $language_array);
                }
            }
        }
    }
    if ($untranslated > 0) {
        $errors[ ] = 'Untranslated Keys: ' . $untranslated . ' - ' . round($untranslated / $all_keys * 100, 2) . '%';
    }
    if (count($errors)) {
        echo "\n";
        print_r($errors);
        echo "\n";
    } else {
        echo "ok\n";
    }
}
