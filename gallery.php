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

//Options

$galleries_per_row = 2;
$pics_per_row = 2;

//Script

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if (isset($_POST[ 'saveedit' ])) {
    include('_mysql.php');
    include('_settings.php');
    include('_functions.php');

    $_language->readModule('gallery');

    $galclass = new \webspell\Gallery;

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                `galleryID`
            FROM
                `" . PREFIX . "gallery_pictures`
            WHERE
                `picID` = '" . (int)$_POST[ 'picID' ] . "'"
        )
    );

    if ((isgalleryadmin($userID) || $galclass->isGalleryOwner($ds[ 'galleryID' ], $userID)) && $_POST[ 'picID' ]) {
        safe_query(
            "UPDATE
                `" . PREFIX . "gallery_pictures`
            SET
                `name` = '" . $_POST[ 'name' ] . "',
                `comment` = '" . $_POST[ 'comment' ] . "',
                `comments` = '" . (int)$_POST[ 'comments' ] . "'
            WHERE
                `picID` = '" . (int)$_POST[ 'picID' ] . "'"
        );
        if (isset($_POST[ 'reset' ])) {
            safe_query(
                "UPDATE
                    `" . PREFIX . "gallery_pictures`
                SET
                    `views` = '0'
                WHERE
                    `picID` = '" . $_POST[ 'picID' ] . "'"
            );
        }
    } else {
        redirect('index.php?site=gallery', $_language->module[ 'no_pic_set' ]);
    }

    redirect('index.php?site=gallery&amp;picID=' . $_POST[ 'picID' ], '', 0);
} elseif ($action == "edit") {
    $_language->readModule('gallery');

    if ($_GET[ 'id' ]) {
        $ds =
            mysqli_fetch_array(
                safe_query(
                    "SELECT
                        *
                    FROM
                        `" . PREFIX . "gallery_pictures`
                    WHERE
                        `picID` = '" . $_GET[ 'id' ] . "'"
                )
            );

        $picID = $_GET[ 'id' ];
        $bg1 = BG_1;
        $comments = '<option value="0">' . $_language->module[ 'no_comments' ] . '</option><option value="1">' .
            $_language->module[ 'user_comments' ] . '</option><option value="2">' .
            $_language->module[ 'visitor_comments' ] . '</option>';
        $comments = str_replace(
            'value="' . $ds[ 'comments' ] . '"',
            'value="' . $ds[ 'comments' ] . '" selected="selected"',
            $comments
        );
        $name = str_replace('"', '&quot;', getinput($ds[ 'name' ]));
        $comment = getinput($ds[ 'comment' ]);
        $data_array = array();
        $data_array['$name'] = $name;
        $data_array['$comment'] = $comment;
        $data_array['$comments'] = $comments;
        $data_array['$picID'] = $picID;
        $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_edit", $data_array);
        echo $gallery;
    } else {
        redirect('index.php?site=gallery', $_language->module[ 'no_pic_set' ]);
    }
} elseif ($action == "delete") {
    include('_mysql.php');
    include('_settings.php');
    include('_functions.php');

    $galclass = new \webspell\Gallery;

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                `galleryID`
            FROM
                `" . PREFIX . "gallery_pictures`
            WHERE
                `picID` = '" . (int)$_GET[ 'id' ] . "'"
        )
    );

    if ((isgalleryadmin($userID) || $galclass->isGalleryOwner($ds[ 'galleryID' ], $userID)) && $_GET[ 'id' ]) {
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT
                    `galleryID`
                FROM
                    `" . PREFIX . "gallery_pictures`
                WHERE `picID` = '" . (int)$_GET[ 'id' ] . "'"
            )
        );

        $dir = 'images/gallery/';

        //delete thumb

        @unlink($dir . 'thumb/' . $_GET[ 'id' ] . '.jpg');

        //delete original

        if (file_exists($dir . 'large/' . $_GET[ 'id' ] . '.jpg')) {
            @unlink($dir . 'large/' . $_GET[ 'id' ] . '.jpg');
        } else {
            @unlink($dir . 'large/' . $_GET[ 'id' ] . '.gif');
        }

        //delete database entry

        safe_query(
            "DELETE FROM
                `" . PREFIX . "gallery_pictures`
            WHERE
                `picID` = '" . (int)$_GET[ 'id' ] . "'"
        );
        safe_query(
            "DELETE FROM
                `" . PREFIX . "comments`
            WHERE
                `parentID` = '" . (int)$_GET[ 'id' ] . "'
            AND
                `type` = 'ga'"
        );
    }
    redirect('index.php?site=gallery&amp;galleryID=' . $ds[ 'galleryID' ], '', 0);
} elseif ($action == "diashow" || $action == "window") {
    include('_mysql.php');
    include('_settings.php');
    include('_functions.php');

    $_language->readModule('gallery');

    if (!isset($_GET[ 'picID' ])) {
        $result = mysqli_fetch_array(
            safe_query(
                "SELECT
                    `picID`
                FROM
                    `" . PREFIX . "gallery_pictures`
                WHERE
                    `galleryID` ='" . (int)$_GET[ 'galleryID' ] . "'
                ORDER BY
                    `picID` ASC
                LIMIT 0,1"
            )
        );
        $picID = (int)$result[ 'picID' ];
    } else {
        $picID = (int)$_GET[ 'picID' ];
    }

    //get name+comment
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                `name`,
                `comment`
            FROM
                `" . PREFIX . "gallery_pictures`
            WHERE `picID` = '" . (int)$picID . "'"
        )
    );

    echo '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="description" content="Clanpage using webSPELL 4 CMS">
<meta name="author" content="webspell.org">
<meta name="copyright" content="Copyright 2005-2015 by webspell.org">
<meta name="generator" content="webSPELL">
<title>' . $_language->module[ 'webs_diashow' ] . ' ' . $ds[ 'name' ] . '</title>
<link href="_stylesheet.css" rel="stylesheet" type="text/css">';

    //get next

    $browse = mysqli_fetch_array(
        safe_query(
            "SELECT
                `picID`
            FROM
                `" . PREFIX . "gallery_pictures`
            WHERE
                `galleryID` = '" . (int)$_GET[ 'galleryID' ] . "'
            AND
                `picID` > " . (int)$picID . "
            ORDER BY
                `picID` ASC
            LIMIT 0,1"
        )
    );

    if ($browse[ 'picID' ] && $_GET[ 'action' ] == "diashow") {
        echo '<meta http-equiv="refresh" content="2;URL=gallery.php?action=diashow&amp;galleryID=' .
            (int)$_GET[ 'galleryID' ] . '&amp;picID=' . $browse[ 'picID' ] . '">';
    }

    echo '</head><body class="text-center">';

    if ($_GET[ 'action' ] == "diashow") {
        if ($browse[ 'picID' ]) {
            echo '<a href="gallery.php?action=diashow&amp;galleryID=' . $_GET[ 'galleryID' ] . '&amp;picID=' .
                $browse[ 'picID' ] . '">';
            safe_query(
                "UPDATE
                    `" . PREFIX . "gallery_pictures`
                SET
                    `views` = views+1
                WHERE
                    `picID` = '" . (int)$picID . "'"
            );
        }
    } else {
        echo '<a href="javascript:close()">';
    }

    //output image

    echo '<img src="picture.php?id=' . $picID . '" alt=""><br>
    <strong>' . cleartext($ds[ 'comment' ], false) . '</strong>';

    if ($browse[ 'picID' ] || $_GET[ 'action' ] == "window") {
        echo '</a>';
    }

    echo '</body></html>';
} elseif (isset($_GET[ 'picID' ])) {
    $_language->readModule('gallery');

    $galclass = new \webspell\Gallery;

    $gallery = $GLOBALS["_template"]->replaceTemplate("title_gallery", array());
    echo $gallery;

    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "gallery_pictures` WHERE `picID` = '" . $_GET[ 'picID' ] . "'");
    if (mysqli_num_rows($ergebnis)) {
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT * FROM `" . PREFIX . "gallery_pictures` WHERE `picID` = '" . (int)$_GET[ 'picID' ] . "'"
            )
        );
        safe_query(
            "UPDATE
                `" . PREFIX . "gallery_pictures`
            SET
                `views` = views+1
            WHERE
                `picID` = '" . (int)$_GET[ 'picID' ] . "'"
        );

        $picturename = clearfromtags($ds[ 'name' ]);
        $picID = $ds[ 'picID' ];

        $picture = $galclass->getLargeFile($picID);

        $picinfo = getimagesize($picture);
        $xsize = $picinfo[ 0 ];
        $ysize = $picinfo[ 1 ];

        $xwindowsize = $xsize + 30;
        $ywindowsize = $ysize + 30;

        $comment = cleartext($ds[ 'comment' ], false);
        $views = $ds[ 'views' ];

        if ($xsize > $picsize_l) {
            $width = 'width="' . $picsize_l . '"';
        } else {
            $width = 'width="' . $xsize . '"';
        }

        $filesize = round(filesize($picture) / 1024, 1);

        //next picture
        $browse = mysqli_fetch_array(
            safe_query(
                "SELECT
                    `picID`
                FROM
                    `" . PREFIX . "gallery_pictures`
                WHERE
                    `galleryID` = '" . (int)$ds[ 'galleryID' ] . "'
                AND
                    `picID` > " . (int)$ds[ 'picID' ] . "
                ORDER BY
                    `picID` ASC
                LIMIT 0,1"
            )
        );
        if ($browse[ 'picID' ]) {
            $forward = '<a href="index.php?site=gallery&amp;picID=' . $browse[ 'picID' ] . '#picture">' .
                $_language->module[ 'next' ] . ' &raquo;</a>';
        } else {
            $forward = '';
        }

        $browse = mysqli_fetch_array(
            safe_query(
                "SELECT
                    `picID`
                FROM
                    `" . PREFIX . "gallery_pictures`
                WHERE
                    `galleryID` = '" . (int)$ds[ 'galleryID' ] . "'
                AND
                    `picID` < " . (int)$ds[ 'picID' ] . "
                ORDER BY
                    `picID` DESC
                LIMIT 0,1"
            )
        );
        if ($browse[ 'picID' ]) {
            $backward = '<a href="index.php?site=gallery&amp;picID=' . $browse[ 'picID' ] . '#picture">&laquo; ' .
                $_language->module[ 'back' ] . '</a>';
        } else {
            $backward = '';
        }

        //rateform

        if ($loggedin) {
            $getgallery = safe_query(
                "SELECT `gallery_pictures` FROM `" . PREFIX . "user` WHERE `userID` = '" . (int)$userID . "'"
            );
            $found = false;
            if (mysqli_num_rows($getgallery)) {
                $ga = mysqli_fetch_array($getgallery);
                if ($ga[ 'gallery_pictures' ] != "") {
                    $string = $ga[ 'gallery_pictures' ];
                    $array = explode(":", $string);
                    $anzarray = count($array);
                    for ($i = 0; $i < $anzarray; $i++) {
                        if ($array[ $i ] == $_GET[ 'picID' ]) {
                            $found = true;
                        }
                    }
                }
            }

            if ($found) {
                $rateform = "<i>" . $_language->module[ 'you_have_already_rated' ] . "</i>";
            } else {
                $rateform = '<form method="post" name="rating_picture' . $_GET[ 'picID' ] .
                    '" action="rating.php" class="form-inline">' . $_language->module[ 'rate_now' ] . '
                                <select name="rating">
                                <option>0 - ' . $_language->module[ 'poor' ] . '</option>
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10 - ' . $_language->module[ 'perfect' ] . '</option>
                                </select>
                                <input type="hidden" name="userID" value="' . $userID . '">
                                <input type="hidden" name="type" value="ga">
                                <input type="hidden" name="id" value="' . $_GET[ 'picID' ] . '">
                                <input type="submit" name="submit" value="' . $_language->module[ 'rate' ] .
                    '" class="btn btn-primary"></form>';
            }
        } else {
            $rateform = '<i>' . $_language->module[ 'rate_have_to_reg_login' ] . '</i>';
        }

        $votes = $ds[ 'votes' ];

        unset($ratingpic);
        $ratings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        for ($i = 0; $i < $ds[ 'rating' ]; $i++) {
            $ratings[ $i ] = 1;
        }
        $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif" width="1" height="5" alt="">';
        foreach ($ratings as $pic) {
            $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif" width="4" height="5" alt="">';
        }

        //admin

        if ((isgalleryadmin($userID) && $publicadmin) || $galclass->isGalleryOwner($ds[ 'galleryID' ], $userID)) {
            $adminaction =
                '<a href="index.php?site=gallery&amp;action=edit&amp;id=' . $_GET[ 'picID' ] .
                '" class="btn btn-danger">' . $_language->module[ 'edit' ] . '</a>
                <input type="button" onclick="MM_confirm(
                        \'' . $_language->module[ 'really_del' ] . '\',
                        \'gallery.php?action=delete&amp;id=' . $_GET[ 'picID' ] . '\'
                    )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger">';
        } else {
            $adminaction = "";
        }

        //group+gallery

        $gallery = '<a href="index.php?site=gallery&amp;galleryID=' . $ds[ 'galleryID' ] . '" class="titlelink">' .
            $galclass->getGalleryName($_GET[ 'picID' ]) . '</a>';
        if ($galclass->getGroupIdByGallery($ds[ 'galleryID' ])) {
            $group =
                '<a href="index.php?site=gallery&amp;groupID=' . $galclass->getGroupIdByGallery($ds[ 'galleryID' ]) .
                '" class="titlelink">' . $galclass->getGroupName($galclass->getGroupIdByGallery($ds[ 'galleryID' ])) .
                '</a>';
        } else {
            $group = '<a href="index.php?site=gallery&amp;groupID=0" class="titlelink">' .
                $_language->module[ 'usergalleries' ] .
                '</a> &gt;&gt; <a href="index.php?site=profile&amp;action=galleries&amp;id=' .
                $galclass->getGalleryOwner($ds[ 'galleryID' ]) . '" class="titlelink">' .
                getnickname($galclass->getGalleryOwner($ds[ 'galleryID' ])) . '</a>';
        }

        $data_array = array();
        $data_array['$group'] = $group;
        $data_array['$gallery'] = $gallery;
        $data_array['$picturename'] = $picturename;
        $data_array['$backward'] = $backward;
        $data_array['$forward'] = $forward;
        $data_array['$picID'] = $picID;
        $data_array['$width'] = $width;
        $data_array['$views'] = $views;
        $data_array['$xsize'] = $xsize;
        $data_array['$ysize'] = $ysize;
        $data_array['$filesize'] = $filesize;
        $data_array['$comment'] = $comment;
        $data_array['$ratingpic'] = $ratingpic;
        $data_array['$votes'] = $votes;
        $data_array['$rateform'] = $rateform;
        $data_array['$adminaction'] = $adminaction;
        $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_comments", $data_array);
        echo $gallery;

        //comments

        $comments_allowed = $ds[ 'comments' ];
        $parentID = $ds[ 'picID' ];
        $type = "ga";
        $referer = "index.php?site=gallery&amp;picID=" . $ds[ 'picID' ];

        include("comments.php");
    }
} elseif (isset($_GET[ 'galleryID' ])) {
    $_language->readModule('gallery');

    $galclass = new \webspell\Gallery;

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT `name` FROM `" . PREFIX . "gallery` WHERE `galleryID` = '" . $_GET[ 'galleryID' ] . "'"
        )
    );
    $title = str_break(clearfromtags($ds[ 'name' ]), 45);
    $pics = mysqli_num_rows(
        safe_query(
            "SELECT
                `picID`
            FROM
                `" . PREFIX . "gallery_pictures`
            WHERE
                `galleryID` = '" . (int)$_GET[ 'galleryID' ] . "'"
        )
    );

    $carouselIndicators = '<li data-target="#my-carousel" data-slide-to="0" class="active"></li>';
    for ($foo = 1; $foo < $pics; $foo++) {
        $carouselIndicators .= '<li data-target="#my-carousel" data-slide-to="' . $foo . '"></li>';
    }

    $gallery = $GLOBALS["_template"]->replaceTemplate("title_gallery", array());
    echo $gallery;

    $pages = ceil($pics / $gallerypictures);
    $galleryID = $_GET[ 'galleryID' ];
    if ($galclass->getGroupIdByGallery($_GET[ 'galleryID' ])) {
        $group =
            '<a href="index.php?site=gallery&amp;groupID=' . $galclass->getGroupIdByGallery($_GET[ 'galleryID' ]) .
            '" class="titlelink">' . $galclass->getGroupName($galclass->getGroupIdByGallery($_GET[ 'galleryID' ])) .
            '</a>';
    } else {
        $group = '<a href="index.php?site=gallery&amp;groupID=0" class="titlelink">' .
            $_language->module[ 'usergalleries' ] .
            '</a> &gt;&gt; <a href="index.php?site=profile&amp;action=galleries&amp;id=' .
            $galclass->getGalleryOwner($_GET[ 'galleryID' ]) . '" class="titlelink">' .
            getnickname($galclass->getGalleryOwner($_GET[ 'galleryID' ])) . '</a>';
    }

    $ergebnis = safe_query(
        "SELECT
            *
        FROM
            `" . PREFIX . "gallery_pictures`
        WHERE
            `galleryID` = '" . (int)$_GET[ 'galleryID' ] . "'
        ORDER BY
            `picID`"
    );

    if (mysqli_num_rows($ergebnis)) {
        $diashow =
            "<strong>- <a href=\"javascript:window.open('gallery.php?action=diashow&amp;galleryID=" . $galleryID .
                "','webspell_diashow','toolbar=no,status=no,scrollbars=yes')\"><small>[" .
                $_language->module[ 'start_diashow' ] . "]</small></a></strong>";
    } else {
        $diashow = "";
    }
    $data_array = array();
    $data_array['$group'] = $group;
    $data_array['$title'] = $title;
    $data_array['$pics'] = $pics;
    $data_array['$pages'] = $pages;
    $data_array['$diashow'] = $diashow;
    $data_array['$pagelink'] = $pagelink;
    $data_array['$carouselIndicators'] = $carouselIndicators;
    $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_gallery_head", $data_array);
    echo $gallery;
    echo '<tr>';
    $i = 1;

    $percent = 100 / $pics_per_row;

    while ($pic = mysqli_fetch_array($ergebnis)) {
        if ($i % 2) {
            $bg = BG_2;
        } else {
            $bg = BG_1;
        }

        $firstactive = '';
        if ($i == 1) {
            $firstactive = 'active';
        }

        $dir = $galclass->getLargeFile($pic[ 'picID' ]);

        list($width, $height, $type, $attr) = getimagesize($dir);
        $pic[ 'comments' ] =
            mysqli_num_rows(
                safe_query(
                    "SELECT
                        `commentID`
                    FROM
                        `" . PREFIX . "comments`
                    WHERE
                        `parentID` = '" . (int)$pic[ 'picID' ] . "'
                    AND
                        `type` = 'ga'"
                )
            );

        $data_array = array();
        $data_array['$firstactive'] = $firstactive;
        $data_array['$picID'] = $pic['picID'];
        $data_array['$name'] = clearfromtags($pic[ 'name' ]);
        $data_array['$comment'] = cleartext($pic[ 'comment' ], false);
        $data_array['$dir'] = $dir;
        $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_showlist", $data_array);
        echo $gallery;

        if ($pics_per_row > 1) {
            if (($i - 1) % $pics_per_row == ($pics_per_row - 1)) {
                echo '</tr><tr>';
            }
        } else {
            echo '</tr><tr>';
        }
        $i++;
    }

    $data_array = array();
    $data_array['$pagelink'] = $pagelink;
    $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_gallery_foot", $data_array);
    echo $gallery;
} elseif (isset($_GET[ 'groupID' ])) {
    $_language->readModule('gallery');

    $galclass = new \webspell\Gallery;

    $gallery = $GLOBALS["_template"]->replaceTemplate("title_gallery", array());
    echo $gallery;

    $galleries =
        mysqli_num_rows(
            safe_query(
                "SELECT `galleryID` FROM `" . PREFIX . "gallery` WHERE `groupID` = '" . (int)$_GET[ 'groupID' ] . "'"
            )
        );
    $pages = ceil($galleries / $gallerypictures);

    if (!isset($_GET[ 'page' ])) {
        $page = 1;
    } else {
        $page = $_GET[ 'page' ];
    }

    if ($pages > 1) {
        $pagelink = makepagelink("index.php?site=gallery&amp;groupID=" . $_GET[ 'groupID' ], $page, $pages);
    } else {
        $pagelink = '';
    }

    $group = $galclass->getGroupName($_GET[ 'groupID' ]);
    if ($_GET[ 'groupID' ] == 0) {
        $group = $_language->module[ 'usergalleries' ];
    }

    $data_array = array();
    $data_array['$group'] = $group;
    $data_array['$galleries'] = $galleries;
    $data_array['$pages'] = $pages;
    $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_group_head", $data_array);
    echo $gallery;

    $ergebnis = safe_query(
        "SELECT
            *
        FROM
            `" . PREFIX . "gallery`
        WHERE
            `groupID` = '" . $_GET[ 'groupID' ] . "'
        ORDER BY
            `galleryID` DESC
        LIMIT 0, " . (int)$gallerypictures
    );

    $i = 1;

    while ($gallery = mysqli_fetch_array($ergebnis)) {
        $dir = 'images/gallery/';

        $gallery[ 'picID' ] = $galclass->randomPic($gallery[ 'galleryID' ]);
        $gallery[ 'pic' ] = $dir . 'thumb/' . $gallery[ 'picID' ] . '.jpg';
        $gallery[ 'pics' ] =
            mysqli_num_rows(
                safe_query(
                    "SELECT
                        `picID`
                    FROM
                        `" . PREFIX . "gallery_pictures`
                    WHERE
                        `galleryID` ='" . (int)$gallery[ 'galleryID' ] . "'"
                )
            );
        $gallery[ 'date' ] = getformatdatetime($gallery[ 'date' ]);
        if (!file_exists($gallery[ 'pic' ])) {
            $gallery[ 'pic' ] = 'images/nopic.gif';
        }

        $data_array = array();
        $data_array['$galleryID'] = $gallery['galleryID'];
        $data_array['$pic'] = $gallery[ 'pic' ];
        $data_array['$name'] = $gallery[ 'name' ];
        $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_showlist_group", $data_array);
        echo $gallery;
    }
    echo '<td>&nbsp;</td></tr>';

    $data_array = array();
    $data_array['$pagelink'] = $pagelink;
    $gallery = $GLOBALS["_template"]->replaceTemplate("gallery_group_foot", $data_array);
    echo $gallery;
} else {
    $_language->readModule('gallery');

    $galclass = new \webspell\Gallery;

    $gallery = $GLOBALS["_template"]->replaceTemplate("title_gallery", array());
    echo $gallery;

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery_groups ORDER BY sort");

    if (mysqli_num_rows($ergebnis)) {
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $groupID = $ds[ 'groupID' ];
            $title = $ds[ 'name' ];
            $gallerys = mysqli_num_rows(
                safe_query(
                    "SELECT
                        `galleryID`
                    FROM
                        `" . PREFIX . "gallery`
                    WHERE
                        `groupID` = '" . $ds[ 'groupID' ] . "'"
                )
            );

            $data_array = array();
            $data_array['$groupID'] = $groupID;
            $data_array['$title'] = $title;
            $gallery_groups = $GLOBALS["_template"]->replaceTemplate("gallery_content_categorys_head", $data_array);
            echo $gallery_groups;

            $groups = safe_query(
                "SELECT
                    *
                FROM
                    `" . PREFIX . "gallery`
                WHERE
                    groupID = '" . (int)$ds[ 'groupID' ] . "'
                ORDER BY
                    galleryID DESC"
            );
            $anzgroups = mysqli_num_rows($groups);
            $i = 0;
            while ($ds = mysqli_fetch_array($groups)) {
                $i++;

                if (isset($ds[ 'date' ])) {
                    $ds[ 'date' ] = date('d.m.Y', $ds[ 'date' ]);
                }
                if (isset($ds[ 'galleryID' ])) {
                    $ds[ 'count' ] =
                        mysqli_num_rows(
                            safe_query(
                                "SELECT
                                    `picID`
                                FROM
                                    `" . PREFIX . "gallery_pictures`
                                WHERE
                                    `galleryID` = '" . (int)$ds[ 'galleryID' ] . "'"
                            )
                        );
                }

                if (isset($ds[ 'count' ])) {
                    $data_array = array();
                    $data_array['$galleryID'] = $ds['galleryID'];
                    $data_array['$pictureID'] = $galclass->randomPic($ds[ 'galleryID' ]);
                    $data_array['$name'] = $ds['name'];
                    $data_array['$count'] = $ds['count'];
                    $data_array['$date'] = $ds['date'];
                    $gallery_groups = $GLOBALS["_template"]->replaceTemplate("gallery_content_showlist", $data_array);
                    echo $gallery_groups;

                    // preventing to break Layout if number of groups is odd
                    if ($anzgroups % 2 != 0 && $i == $anzgroups) {
                        echo '<div class="col-xs-3"></div>';
                    }
                } else {
                    echo '<p class="col-xs-6">' . $_language->module[ 'no_gallery_exists' ] . '</p>';
                }
            }

            $gallery_content_categorys_foot = $GLOBALS["_template"]->replaceTemplate(
                "gallery_content_categorys_foot",
                array()
            );
            echo $gallery_content_categorys_foot;
        }
    } else {
        echo generateAlert($_language->module['no_gallery_exists'], 'alert-info');
    }
}
