<?php

header("Content-Type: text/plain; charset=utf-8");

define('BOM', "\xEF\xBB\xBF");

$baseLanguage = "de";
$checkUntranslated = true;

$all_langs = glob("*",GLOB_ONLYDIR);
if(in_array($baseLanguage,$all_langs)){
	unset($all_langs[array_search($baseLanguage, $all_langs)]);
}

$ref_keys = array();
$erros = array();
$all_langs = array_merge(array($baseLanguage),$all_langs);

function checkBom($file){
	return (false !== strpos($file, BOM));
}
$all_keys = 0;
foreach($all_langs as $lang){
	echo "Checking ".$lang." ... ";
	$errors = array();
	$files = glob($lang.'/*');
	$untranslated = 0;
	foreach ($files as $file) {
		if(checkBom($file) !== false){
			$errors[$file_name][] = 'UTF-8 BOM';
		}
		ob_start();
		include($file);
		ob_end_clean();
		$file_name = basename($file);
		if($lang == $baseLanguage){
			$ref_keys[$file_name] = $language_array;
			$all_keys += count($language_array);
		}
		else{
			if(isset($ref_keys[$file_name])){
				$tmp = $ref_keys[$file_name];
				foreach($language_array as $key => $val){
					if(!isset($ref_keys[$file_name][$key])){
						$errors[$file_name][] = 'Unknown key: '.$key;
					}
					else{
						if($val == $ref_keys[$file_name][$key] && $checkUntranslated == true){
							//$errors[$file_name][] = 'Not translated key: '.$key;
							$untranslated += 1;
							unset($tmp[$key]);
						}
						else{
							unset($tmp[$key]);
						}
					}
				}
				foreach($tmp as $key => $val){
					$errors[$file_name][] = 'Missing key: '.$key;
				}
			}
			else{
				$errors['unknown_files'][] = $file_name;
			}
		}
	}
	if($untranslated > 0){
		$errors[] = 'Untranslated Keys: '.$untranslated.' - '.round($untranslated / $all_keys*100,2).'%';
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
