<?php

header("Content-Type: text/plain; charset=utf-8");

include("translation_config.php");

$all_langs = glob($languageBaseFolder."*", GLOB_ONLYDIR);

function correctFile($file){
    global $file_header, $file_footer, $sortMode;
    ob_start();
    include($file);
    $outputted_content = ob_get_length();
    ob_clean();

    $rows = array();
    ksort($language_array, $sortMode);
    foreach ($language_array as $lang_key => $lang_val) {
        $rows[] = "    '".escape($lang_key)."' => '".fixGlobals(escape($lang_val))."'";
    }

    $new_array = implode(",\n", $rows)."\n";

    $new_content = $file_header.$new_array.$file_footer;

    file_put_contents($file, $new_content);
}

$all_keys = 0;
foreach ($all_langs as $lang) {
    echo "Correcting " . basename($lang) . " ... ";
    $files = glob($lang . '/*');
    foreach ($files as $file) {
        $file_name = basename($file);
        $ext = substr($file_name, strrpos($file_name, "."));
        if ($ext == ".php") {
            correctFile($file);
        }
        if ($file_name == "admin") {
            echo "admin ...";
            $adminFiles = glob($lang."/admin/*");
            foreach ($adminFiles as $file) {
                $file_name = basename($file);
                $ext = substr($file_name, strrpos($file_name, "."));
                if ($ext == ".php") {
                    correctFile($file);
                }
            }
        }
    }
    echo "ok\n";
    flush();
    ob_flush();
}
