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
#   Copyright 2005-2015 by webspell.org                                  #
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

function getExceptionTraceAsString($exception) {
    $rtn = "";
    $count = 0;
    foreach ($exception->getTrace() as $frame) {
        $args = "";
        if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $arg) {
                if (is_string($arg)) {
                    $args[] = "'" . $arg . "'";
                } elseif (is_array($arg)) {
                    $args[] = "Array";
                } elseif (is_null($arg)) {
                    $args[] = 'NULL';
                } elseif (is_bool($arg)) {
                    $args[] = ($arg) ? "true" : "false";
                } elseif (is_object($arg)) {
                    $args[] = get_class($arg);
                } elseif (is_resource($arg)) {
                    $args[] = get_resource_type($arg);
                } else {
                    $args[] = $arg;
                }
            }
            $args = join(", ", $args);
        }
        $rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
            $count,
            isset($frame['file']) ? $frame['file'] : 'unknown file',
            isset($frame['line']) ? $frame['line'] : 'unknown line',
            (isset($frame['class']))  ? $frame['class'].$frame['type'].$frame['function'] : $frame['function'],
            $args );
        $count++;
    }
    return $rtn;
}

function generateCallTrace()
{
    $e = new Exception();
    //$trace = explode("\n", str_replace($_SERVER['DOCUMENT_ROOT'], '', $e->getTraceAsString()));
    $trace = explode("\n", str_replace($_SERVER['DOCUMENT_ROOT'], '', getExceptionTraceAsString($e)));
    $trace = array_reverse($trace);
    array_shift($trace);
    array_pop($trace);
    array_pop($trace);
    $length = count($trace);
    $result = array();

    for ($i = 0; $i < $length; $i++)
    {
        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' '));
    }

    return "\t" . implode("\n\t", $result);
}

function system_error($text, $system = 1, $strace = 0)
{

    ob_clean();
    global $_database;

    if ($strace) {
        $trace = '<pre>' . generateCallTrace() . '</pre>';
    } else {
        $trace = '';
    }

    if ($system) {
        include('version.php');
        $info = 'Version: ' . $version . ', PHP Version: ' . phpversion();
        if (!mysqli_connect_error()) {
            $info .= ', MySQL Version: ' . $_database->server_info;
        }
    } else {
        $info = 'webSPELL';
    }
    die('<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="utf-8">
<meta name="description" content="Clanpage using webSPELL 4 CMS">
<meta name="author" content="webspell.org">
<meta name="keywords" content="webspell, webspell4, clan, cms">
<meta name="generator" content="webSPELL">

<!-- Head & Title include -->
<title>webSPELL - Error</title>
<link href="components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="_stylesheet.css" rel="stylesheet">
<style>
/* centered columns styles */
.row-centered {
    text-align:center;
}
.col-centered {
    display:inline-block;
    float:none;
    /* reset the text-align */
    text-align:left;
    /* inline-block space fix */
    margin-right:-4px;
}
</style>
<!-- end Head & Title include -->
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="index.php">webSPELL</a>
            <p class="navbar-text">' . $info . '</p>
        </div>
    </div>
    <!-- container -->
</div>

<div class="container">
    <div class="row row-centered">
        <!-- main content area -->
        <div class="col-xs-12 col-sm-6 col-md-8 col-centered">
            <div>
                <div class="alert alert-danger" role="alert"><strong>An error has occured</strong></div>
            </div>
            <div class="alert alert-info" role="alert">
                ' . $text . '
            </div>
                ' . $trace . '
        </div>
    </div>
</div>
<script src="components/jquery/dist/jquery.min.js"></script>
<script src="components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>');
}
