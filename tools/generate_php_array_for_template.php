<?php
error_reporting(E_ALL);
#$regex_find_eval_calls = '/eval\s\("\\\$([\w_])+\s*=\s*\\""\s+.\s+gettemplate\("([\w_]+?)"\)\s+.\s+"\\";"\);/si';
$regex_find_eval_calls =
    '/(?<intend>[ \t]*)eval\s*\("' . preg_quote('\$', "/") . '(?<variable>[\w_]+?)\s*=\s*' . preg_quote('\"', "/") .
    '"\s*.\s*gettemplate\(["\'](?<parameters>[\w_,\'" ]+?)["\']\)\s*.\s*"' . preg_quote('\"', "/") . ';"\);/si';

$folders = array('../', '../admin/', '../src/', '../src/func/');
$count = 0;

function extractVariablesFromTemplate($file)
{
    $content = file_get_contents($file);
    preg_match_all("/(?<variable>" . preg_quote('$', "/") . "[\w_]+)/si", $content, $matches, PREG_SET_ORDER);
    return $matches;
}

function generateNewTemplateClass($variable, $options, $intend)
{
    if (stristr($options, ",")) {
        $options = preg_split("/[,\" ]/si", $options, -1, PREG_SPLIT_NO_EMPTY);
        $template_file = $options[ 0 ];
        $extension = $options[ 1 ];
        $calledfrom = $options[ 2 ];
    } else {
        $template_file = $options;
        $extension = "html";
        $calledfrom = "root";
    }

    $variables_used = extractVariablesFromTemplate('../templates/' . $template_file . "." . $extension);
    $replace_code = '';
    $unique_list = array();
    if (count($variables_used)) {
        $variable_in_call = '$data_array';
        $replace_code = $intend . '$data_array = array();' . "\n";
        foreach ($variables_used as $var) {
            if (!in_array($var[ 'variable' ], $unique_list)) {
                $replace_code .= $intend . '$data_array[\'' . $var[ 'variable' ] . '\'] = ' . $var[ 'variable' ] . ';' .
                    "\n";
                $unique_list[ ] = $var[ 'variable' ];
            }
        }
    } else {
        $variable_in_call = 'array()';
    }

    $replace_code .= $intend . '$' . $variable . ' = $GLOBALS["_template"]->replaceTemplate("' . $template_file . '", '
        .$variable_in_call . ');';

    return $replace_code;
}

foreach ($folders as $folder) {
    $files = glob($folder . "*.php");

    foreach ($files as $file) {
        $file_content = file_get_contents($file);
        echo $file . "\n";
        $modified = false;
        while (preg_match($regex_find_eval_calls, $file_content, $result)) {
            echo "Old Line:\n";
            echo $result[ 0 ] . "\n";
            echo "New Line\n";
            $new_line = generateNewTemplateClass($result[ 'variable' ], $result[ 'parameters' ], $result[ 'intend' ]);
            echo $new_line;
            $file_content = preg_replace("/" . preg_quote($result[ 0 ], "/") . "/si", $new_line, $file_content, 1);
            echo "\n";
            $count++;
            $modified = true;
        }
        if ($modified) {
            file_put_contents($file, $file_content);
        }
        echo "----------\n";
    }
}
echo $count . " replaces";
