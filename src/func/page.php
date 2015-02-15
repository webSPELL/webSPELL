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

function redirect($url, $info, $time = 1)
{
    if ($url == "back" && $info != '' && isset($_SERVER['HTTP_REFERER'])) {
        $url = $_SERVER['HTTP_REFERER'];
        $info = '';
    } elseif ($url == "back" && $info != '') {
        $url = $info;
        $info = '';
    }
    echo
        '<meta http-equiv="refresh" content="' . $time . ';URL=' . $url . '"><br />' .
        '<p style="color:#000000">' . $info . '</p><br /><br />';
}

function isStaticPage($staticID = null)
{
    if ($GLOBALS['site'] != "static") {
        return false;
    }

    if ($staticID !== null) {
        if ($_GET['staticID'] != $staticID) {
            return false;
        }
    }

    return true;
}

function generateAlert($text, $class = 'alert-warning', $dismissible = false)
{
    $classes = 'alert ' . $class;
    if ($dismissible) {
        $classes .= ' alert-dismissible';
    }
    $return = '<div class="' . $classes . '" role="alert">';
    if ($dismissible) {
        $return .= '<button type="button" class="close" data-dismiss="alert">';
        $return .= '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>';
        $return .= '</button>';
    }
    $return .= $text;
    $return .= '</div>';
    return $return;
}

function generateErrorBox($message, $dismissible = false)
{
    return generateAlert($message, 'alert-danger', $dismissible);
}

function generateErrorBoxFromArray($intro, $errors, $dismissible = false)
{
    $message = '<strong>' . $intro . ':</strong><br/><ul>';
    foreach ($errors as $error) {
        $message .= '<li>' . $error . '</li>';
    }
    $message .= '</ul>';
    return generateAlert($message, 'alert-danger', $dismissible);
}

function generateComponents($components, $type)
{
    $return = '';
    foreach ($components as $component) {
        if ($type === 'js') {
            $return .= '<script src="' . $component . '"></script>';
        } elseif ($type === 'css') {
            $return .= '<link href="' . $component . '" rel="stylesheet">';
        }
    }

    return $return;
}
