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
session_name("ws_session");
session_start();
include('functions.php');
include("../src/func/language.php");
include("../src/func/user.php");
if (isset($_GET['function']) && function_exists('update_' . $_GET['function'])) {
    $function = 'update_' . $_GET['function'];
} else {
    $function = null;
}
$_language = new webspell\Language();

if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = "en";
}
$_language->setLanguage($_SESSION['language']);
$_language->readModule('step6');

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
if ($function != null) {
    if (isset($_SESSION['adminname'])) $adminname = $_SESSION['adminname'];
    if (isset($_SESSION['adminpassword'])) $adminpassword = $_SESSION['adminpassword'];
    if (isset($_SESSION['adminmail'])) $adminmail = $_SESSION['adminmail'];
    if (isset($_SESSION['url'])) $url = $_SESSION['url'];
    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);
    $_database->autocommit(false);
    echo json_encode($function($_database));
    $_database->autocommit(true);
} else {
    echo json_encode(array('status' => 'failed', 'message' => 'Unknown Method'));
}
?>
