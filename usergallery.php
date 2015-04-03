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
$_language->readModule('usergallery');
$galclass = new \webspell\Gallery;

if ($userID) {
    if (isset($_POST[ 'save' ])) {
        if ($_POST[ 'name' ]) {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "gallery (
                        `name`,
                        `date`,
                        `userID`
                    )
                    values(
                    '" . $_POST[ 'name' ] . "',
                    '" . time() . "',
                    '" . $userID . "'
                    ) "
            );
        } else {
            redirect('index.php?site=usergallery&action=add', $_language->module[ 'please_enter_name' ]);
        }
    } elseif (isset($_POST[ 'saveedit' ])) {
        safe_query(
            "UPDATE
                " . PREFIX . "gallery
            SET
                name='" . $_POST[ 'name' ] . "'
            WHERE
                galleryID='" . (int)$_POST[ 'galleryID' ] . "' AND
                userID='" . (int)$userID."'"
        );
    } elseif (isset($_POST[ 'saveform' ])) {
        $dir = 'images/gallery/';

        $upload = new \webspell\HttpUpload('picture');

        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
                if ($upload->supportedMimeType($mime_types)) {
                    if (!empty($_POST[ 'name' ])) {
                        $insertname = $_POST[ 'name' ];
                    } else {
                        $insertname = $upload->getFileName();
                    }

                    $typ =  getimagesize($upload->getTempFile());

                    if (is_array($typ)) {
                        switch ($typ[ 2 ]) {
                            case 1:
                                $endung = '.gif';
                                break;
                            case 3:
                                $endung = '.png';
                                break;
                            default:
                                $endung = '.jpg';
                                break;
                        }

                        safe_query(
                            "INSERT INTO
                                " . PREFIX . "gallery_pictures (
                                    `galleryID`,
                                    `name`,
                                    `comment`,
                                    `comments`
                                )
                                VALUES (
                                    '" . (int)$_POST[ 'galleryID' ] . "',
                                    '" . $insertname . "',
                                    '" . $_POST[ 'comment' ] . "',
                                    '" . $_POST[ 'comments' ] . "'
                                )"
                        );

                        $insertid = mysqli_insert_id($_database);

                        $newBigFile   = $dir . 'large/' . $insertid . $endung;
                        $newThumbFile = $dir . 'thumb/' . $insertid . '.jpg';

                        if ($upload->saveAs($newBigFile)) {
                            @chmod($newBigFile, $new_chmod);
                            $galclass->saveThumb($newBigFile, $newThumbFile);

                            if (($galclass->getUserSpace($userID) + filesize($newBigFile) +
                                    filesize($newThumbFile)) > $maxusergalleries
                            ) {
                                @unlink($newBigFile);
                                @unlink($newThumbFile);
                                safe_query(
                                    "DELETE FROM " . PREFIX . "gallery_pictures WHERE picID='" . $insertid . "'"
                                );
                                echo generateErrorBox($_language->module[ 'no_space_left' ]);
                            }
                        } else {
                            safe_query("DELETE FROM " . PREFIX . "gallery_pictures WHERE picID='" . $insertid . "'");
                            @unlink($upload->getTempFile());
                        }
                    } else {
                        echo generateErrorBox($_language->module[ 'broken_image' ]);
                    }
                } else {
                    echo generateErrorBox($_language->module[ 'unsupported_image_type' ]);
                }
            }
        }
    } elseif (isset($_GET[ 'delete' ])) {
        //SQL
        if (
            safe_query(
                "DELETE FROM
                    " . PREFIX . "gallery
                WHERE
                    galleryID='" . (int)$_GET[ 'galleryID' ] . "' AND
                    userID='" . (int)$userID."'"
            )
        ) {
            //FILES
            $ergebnis =
                safe_query(
                    "SELECT
                        `picID`
                    FROM
                        " . PREFIX . "gallery_pictures
                    WHERE
                        `galleryID` = '" . (int)$_GET[ 'galleryID' ]."'"
                );
            while ($ds = mysqli_fetch_array($ergebnis)) {
                @unlink('images/gallery/thumb/' . $ds[ 'picID' ] . '.jpg'); //thumbnails
                $path = 'images/gallery/large/';
                if (file_exists($path . $ds[ 'picID' ] . '.jpg')) {
                    $path = $path . $ds[ 'picID' ] . '.jpg';
                } elseif (file_exists($path . $ds[ 'picID' ] . '.png')) {
                    $path = $path . $ds[ 'picID' ] . '.png';
                } else {
                    $path = $path . $ds[ 'picID' ] . '.gif';
                }
                @unlink($path); //large
                safe_query("DELETE FROM " . PREFIX . "comments WHERE parentID='" . $ds[ 'picID' ] . "' AND type='ga'");
            }
            safe_query("DELETE FROM " . PREFIX . "gallery_pictures WHERE galleryID='" . $_GET[ 'galleryID' ] . "'");
        }
    }

    $usergallery_title = $GLOBALS["_template"]->replaceTemplate("title_usergallery", array());
    echo $usergallery_title;

    if (isset($_GET[ 'action' ])) {
        if ($_GET[ 'action' ] == "add") {
            $usergallery_add = $GLOBALS["_template"]->replaceTemplate("usergallery_add", array());
            echo $usergallery_add;
        } elseif ($_GET[ 'action' ] == "edit") {
            $ergebnis = safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "gallery
                WHERE
                    galleryID='" . $_GET[ 'galleryID' ] . "'AND
                    userID='" . (int)$userID."'"
            );
            $ds = mysqli_fetch_array($ergebnis);

            $name = getinput($ds[ 'name' ]);
            $galleryID = $ds[ 'galleryID' ];
            $data_array = array();
            $data_array['$name'] = $name;
            $data_array['$galleryID'] = $galleryID;
            $usergallery_edit = $GLOBALS["_template"]->replaceTemplate("usergallery_edit", $data_array);
            echo $usergallery_edit;
        } elseif ($_GET[ 'action' ] == "upload") {
            $id = (int)$_GET[ 'galleryID' ];

            $data_array = array();
            $data_array['$id'] = $id;
            $usergallery_upload = $GLOBALS["_template"]->replaceTemplate("usergallery_upload", $data_array);
            echo $usergallery_upload;
        }
    } else {
        $size = $galclass->getUserSpace($userID);
        $percent = percent($size, $maxusergalleries, 0);

        if ($percent > 95) {
            $color = $loosecolor;
        } else {
            $color = $wincolor;
        }

        $bg1 = BG_1;
        $bg2 = BG_2;
        $pagebg = PAGEBG;
        $border = BORDER;
        $bghead = BGHEAD;
        $bgcat = BGCAT;

        $vars = array('%spacecolor%', '%used_size%', '%available_size%');
        $repl = array($color, round($size / (1024 * 1024), 2), round($maxusergalleries / (1024 * 1024), 2));
        $space_max_in_user = str_replace($vars, $repl, $_language->module[ 'x_of_y_mb_in_use' ]);

        $data_array = array();
        $data_array['$space_max_in_user'] = $space_max_in_user;
        $usergallery_head = $GLOBALS["_template"]->replaceTemplate("usergallery_head", $data_array);
        echo $usergallery_head;

        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery WHERE userID='" . (int)$userID."'");

        if (mysqli_num_rows($ergebnis) == 0) {
            echo '<tr>' . $_language->module[ 'no_galleries' ] . '</td></tr>';
        }

        for ($i = 1; $ds = mysqli_fetch_array($ergebnis); $i++) {
            if ($i % 2) {
                $bg = $bg1;
            } else {
                $bg = $bg2;
            }
            $name = clearfromtags($ds[ 'name' ]);
            $galleryID = $ds[ 'galleryID' ];

            $data_array = array();
            $data_array['$galleryID'] = $galleryID;
            $data_array['$name'] = $name;
            $usergallery = $GLOBALS["_template"]->replaceTemplate("usergallery", $data_array);
            echo $usergallery;
        }

        $usergallery_foot = $GLOBALS["_template"]->replaceTemplate("usergallery_foot", array());
        echo $usergallery_foot;
    }
} else {
    redirect('index.php?site=login', '', 0);
}
