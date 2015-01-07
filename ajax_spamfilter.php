<?php
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
                $spamApi = SpamApi::getInstance();
                if (in_array($type, ["spam", "ham"])) {
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
                $spamApi = SpamApi::getInstance();
                if (in_array($type, ["spam", "ham"])) {
                    $spamApi->learn($text, $type);
                }
            }
        }
    }
}
