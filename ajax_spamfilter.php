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

include("_mysql.php");
include("_settings.php");
include("_functions.php");

if (isset($_GET[ 'type' ])) {
    $type = $_GET[ 'type' ];
} else {
    $type = null;
}

if (!empty($spamapikey)) {
    if (isset($_GET[ 'postID' ])) {
        $postID = $_GET[ 'postID' ];

        $get = safe_query("SELECT * FROM " . PREFIX . "forum_posts WHERE postID='" . $postID . "'");
        if (mysqli_num_rows($get)) {
            $ds = mysqli_fetch_array($get);

            if (ispageadmin($userID) || ismoderator($userID, $ds[ 'boardID' ])) {
                $message = $ds[ 'message' ];
                $spamApi = \webspell\SpamApi::getInstance();
                if (in_array($type, array("spam", "ham"))) {
                    $spamApi->learn($message, $type);
                }
            }
        }
    } elseif (isset($_GET[ 'commentID' ])) {
        $commentID = $_GET[ 'commentID' ];
        if (ispageadmin($userID) || isfeedbackadmin($userID)) {
            $get = safe_query("SELECT * FROM " . PREFIX . "comments WHERE commentID='" . $commentID . "'");
            if (mysqli_num_rows($get)) {
                $ds = mysqli_fetch_array($get);

                $text = $ds[ 'comment' ];
                $spamApi = \webspell\SpamApi::getInstance();
                if (in_array($type, array("spam", "ham"))) {
                    $spamApi->learn($text, $type);
                }
            }
        }
    }
}
