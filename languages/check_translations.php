<?php

header("Content-Type: text/plain; charset=utf-8");

define('BOM', "\xEF\xBB\xBF");

$baseLanguage = "uk";
$checkUntranslated = false;

$all_langs = glob("*",GLOB_ONLYDIR);
if(in_array($baseLanguage,$all_langs)){
	unset($all_langs[array_search($baseLanguage, $all_langs)]);
}

$ref_keys = array();
$erros = array();
$all_langs = array($baseLanguage) + $all_langs;

function checkBom($file){
	return (false !== strpos($file, BOM));
}

foreach($all_langs as $lang){
	echo "Checking ".$lang." ... ";
	$errors = array();
	$files = glob($lang.'/*');
	foreach ($files as $file) {
		if(checkBom($file) !== false){
			$errors[$lang][$file_name][] = 'UTF-8 BOM';
		}
		ob_start();
		include($file);
		ob_end_clean();
		$file_name = basename($file);
		if($lang == $baseLanguage){
			$ref_keys[$file_name] = $language_array;
		}
		else{
			$tmp = $ref_keys[$file_name];
			foreach($language_array as $key => $val){
				if(!isset($ref_keys[$file_name][$key])){
					$errors[$lang][$file_name][] = 'Unknown key: '.$key;
				}
				else{
					if($val == $ref_keys[$file_name][$key] && $checkUntranslated == true){
						$errors[$lang][$file_name][] = 'Not translated key: '.$key;
						unset($tmp[$key]);
					}
					else{
						unset($tmp[$key]);
					}
				}
			}
			foreach($tmp as $key => $val){
				$errors[$lang][$file_name][] = 'Missing key: '.$key;
			}
		}
	}
	if(count($errors)){
		echo "\n";
		print_r($errors);
		echo "\n";
	}
	else{
		echo "ok\n";
	}
}

?>