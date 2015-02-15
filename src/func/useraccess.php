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

function isanyadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    `userID` = " . (int)$userID . " AND
                    (
                        `page` = 1 OR
                        `forum` = 1 OR
                        `user` = 1 OR
                        `news` = 1 OR
                        `clanwars` = 1 OR
                        `feedback` = 1 OR
                        `super` = 1 OR
                        `gallery` = 1 OR
                        `cash` = 1 OR
                        `files` = 1
                    )"
            )
        ) > 0
    );
}

function issuperadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT userID FROM " . PREFIX . "user_groups WHERE `super` = 1 AND `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isforumadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `forum` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isfileadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `files` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function ispageadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `page` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isfeedbackadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `feedback` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isnewsadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `news` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isnewswriter($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `news` = 1 OR
                        `super` = 1 OR
                        `news_writer` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function ispollsadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `polls` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isclanwaradmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    (
                        `clanwars` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function ismoderator($userID, $boardID)
{
    if (empty($userID) || empty($boardID)) {
        return false;
    }

    if (!isanymoderator($userID)) {
        return false;
    }

    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "forum_moderators
                WHERE
                    `userID` = " . (int)$userID . " AND
                    `boardID` = " . (int)$boardID
            )
        ) > 0
    );
}

function isanymoderator($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    " . PREFIX . "user_groups
                WHERE
                    `userID` = " . (int)$userID . " AND
                    `moderator` = 1"
            )
        ) > 0
    );
}

function isuseradmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    `" . PREFIX . "user_groups`
                WHERE
                    (
                        `user` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function iscashadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    `" . PREFIX . "user_groups`
                WHERE
                    (
                        `cash` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isgalleryadmin($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    `" . PREFIX . "user_groups`
                WHERE
                    (
                        `gallery` = 1 OR
                        `super` = 1
                    ) AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isclanmember($userID)
{
    if (
        mysqli_num_rows(
            safe_query(
                "SELECT userID FROM `" . PREFIX . "squads_members` WHERE `userID` = " . (int)$userID
            )
        ) > 0
    ) {
        return true;
    } else {
        return issuperadmin($userID);
    }
}

function isjoinusmember($userID)
{
    if (
        mysqli_num_rows(
            safe_query(
                "SELECT userID FROM `" . PREFIX . "squads_members` WHERE `userID` = " . (int)$userID
            )
        ) > 0
    ) {
        return true;
    } else {
        return issuperadmin($userID);
    }
}

function isbanned($userID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    userID
                FROM
                    `" . PREFIX . "user`
                WHERE
                    `userID` = " . (int)$userID . " AND
                    (
                        `banned` = 'perm' OR
                        `banned` IS NOT NULL
                    )"
            )
        ) > 0
    );
}

function iscommentposter($userID, $commID)
{
    if (empty($userID) || empty($commID)) {
        return false;
    }

    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    commentID
                FROM
                    " . PREFIX . "comments
                WHERE
                    `commentID` = " . (int)$commID . " AND
                    `userID` = " . (int)$userID
            )
        ) > 0
    );
}

function isforumposter($userID, $postID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    postID
                FROM
                    " . PREFIX . "forum_posts
                WHERE
                    `postID` = " . (int)$postID . " AND
                    `poster` = " . (int)$userID
            )
        ) > 0
    );
}

function istopicpost($topicID, $postID)
{
    return (
        mysqli_num_rows(
            safe_query(
                "SELECT
                    postID
                FROM
                    " . PREFIX . "forum_posts
                WHERE
                    `topicID` = " . (int)$topicID . " AND
                    `postId` = " . (int)$postID . "
                ORDER BY
                    `date` ASC
                LIMIT
                    0,1"
            )
        ) > 0
    );
}

function isinusergrp($usergrp, $userID, $sp = 1)
{
    if ($usergrp == 'user' && !empty($userID)) {
        return true;
    }

    if (!usergrpexists($usergrp)) {
        return false;
    }

    if (
        mysqli_num_rows(safe_query(
            "SELECT
                userID
            FROM
                " . PREFIX . "user_forum_groups
            WHERE
                `" . $usergrp . "` = 1 AND
                `userID` = " . (int)$userID
        )) > 0
    ) {
        return true;
    }

    return isforumadmin($userID);
}
