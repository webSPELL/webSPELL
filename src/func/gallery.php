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
#   Copyright 2005-2014 by webspell.org                                  #
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

namespace webspell;

class Gallery
{

    public function showThumb($picID)
    {

        global $_language;
        $_language->readModule('gallery', true);
        global $thumbwidth, $_language;

        $pic = mysqli_fetch_array(
            safe_query(
                "SELECT * FROM `" . PREFIX . "gallery_pictures` WHERE `picID` = " . (int)$picID
            )
        );
        if ($pic['picID']) {
            $pic['gallery'] = str_break(stripslashes($this->getGalleryName($picID)), 45);
            if (file_exists('images/gallery/thumb/' . $picID . '.jpg')) {
                $pic['image'] =
                    '<a href="index.php?site=gallery&amp;picID=' . $picID . '">' .
                    '<img src="images/gallery/thumb/' . $picID . '.jpg" width="' . $thumbwidth . '" alt="" /></a>';
            } else {
                $pic['image'] =
                    '<a href="index.php?site=gallery&amp;picID=' . $picID . '">' .
                    '<img src="images/nopic.gif" width="' . $thumbwidth . '" alt="' .
                    $_language->module['no_thumb'] . '" /></a>';
            }
            $pic['comments'] = mysqli_num_rows(
                safe_query(
                    "SELECT
                        `commentID`
                    FROM
                        `" . PREFIX . "comments`
                    WHERE
                        `parentID` = " . (int)$picID . " AND
                        `type` = 'ga'"
                )
            );
            $ergebnis = mysqli_fetch_array(
                safe_query(
                    "SELECT
                        `date`
                    FROM
                        `" . PREFIX . "gallery` AS gal,
                        `" . PREFIX . "gallery_pictures` AS pic
                    WHERE
                        gal.`galleryID` = pic.`galleryID` AND
                        pic.`picID` = " . (int)$picID
                )
            );
            $pic['date'] = getformatdate($ergebnis['date']);
            $pic['groupID'] = $this->getGroupIdByGallery($pic['galleryID']);
            $pic['name'] = stripslashes(clearfromtags($pic['name']));

            eval ("\$thumb = \"" . gettemplate("gallery_content_showthumb") . "\";");

        } else {
            $thumb = '<tr><td colspan="2">' . $_language->module['no_picture'] . '</td></tr>';
        }
        return $thumb;
    }

    public function saveThumb($image, $dest)
    {

        global $picsize_h;
        global $thumbwidth;
        global $new_chmod;

        $max_x = $thumbwidth;
        $max_y = $picsize_h;

        $ext = getimagesize($image);
        switch (strtolower($ext[2])) {
            case '2':
                $im = imagecreatefromjpeg($image);
                break;
            case '1':
                $im = imagecreatefromgif($image);
                break;
            case '3':
                $im = imagecreatefrompng($image);
                break;
            default:
                $stop = true;
                break;
        }

        $result = "";
        if (!isset($stop)) {
            $x = imagesx($im);
            $y = imagesy($im);


            if (($max_x / $max_y) < ($x / $y)) {
                $save = imagecreatetruecolor($x / ($x / $max_x), $y / ($x / $max_x));
            } else {
                $save = imagecreatetruecolor($x / ($y / $max_y), $y / ($y / $max_y));
            }
            imagecopyresampled($save, $im, 0, 0, 0, 0, imagesx($save), imagesy($save), $x, $y);

            imagejpeg($save, $dest, 80);
            @chmod($dest, $new_chmod);

            imagedestroy($im);
            imagedestroy($save);
            return $result;
        } else {
            return false;
        }
    }

    public function randomPic($galleryID = 0)
    {

        if ($galleryID) {
            $only = "WHERE `galleryID` = " . (int)$galleryID;
        } else {
            $only = '';
        }
        $anz = mysqli_num_rows(safe_query("SELECT picID FROM `" . PREFIX . "gallery_pictures` $only"));
        $selected = rand(1, $anz);
        $start = $selected - 1;
        $pic = mysqli_fetch_array(
            safe_query(
                "SELECT `picID` FROM `" . PREFIX . "gallery_pictures` $only LIMIT $start, $anz"
            )
        );

        return $pic['picID'];
    }

    public function getGalleryName($picID)
    {

        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT
                    gal.name
                FROM
                    `" . PREFIX . "gallery_pictures` AS pic,
                    `" . PREFIX . "gallery` AS gal
                WHERE
                    pic.`picID` = " . (int)$picID . " AND
                    gal.`galleryID` = pic.`galleryID`"
            )
        );
        return htmlspecialchars($ds['name']);

    }

    public function getGroupName($groupID)
    {

        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT `name` FROM `" . PREFIX . "gallery_groups` WHERE `groupID` = " . (int)$groupID
            )
        );
        return htmlspecialchars($ds['name']);

    }

    public function getGroupIdByGallery($galleryID)
    {

        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT `groupID` FROM `" . PREFIX . "gallery` WHERE `galleryID` = " . (int)$galleryID
            )
        );
        return $ds['groupID'];
    }

    public function isGalleryOwner($galleryID, $userID)
    {
        if (empty($userID)) {
            return false;
        }

        return (
            mysqli_num_rows(
                safe_query(
                    "SELECT
                        `galleryID`
                    FROM
                        `" . PREFIX . "gallery`
                    WHERE
                        `userID` = " . (int)$userID . " AND
                        `galleryID` = " . (int)$galleryID
                )
            ) > 0
        );
    }

    public function getGalleryOwner($galleryID)
    {

        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT `userID` FROM `" . PREFIX . "gallery` WHERE `galleryID` = " . (int)$galleryID . ""
            )
        );
        return $ds['userID'];

    }

    public function getLargeFile($picID)
    {

        if (file_exists('images/gallery/large/' . $picID . '.jpg')) {
            $file = 'images/gallery/large/' . $picID . '.jpg';
        } elseif (file_exists('images/gallery/large/' . $picID . '.gif')) {
            $file = 'images/gallery/large/' . $picID . '.gif';
        } elseif (file_exists('images/gallery/large/' . $picID . '.png')) {
            $file = 'images/gallery/large/' . $picID . '.png';
        } else {
            $file = 'images/nopic.gif';
        }

        return $file;

    }

    public function getUserSpace($userID)
    {

        $size = 0;
        $ergebnis = safe_query(
            "SELECT
                pic.picID
            FROM
                `" . PREFIX . "gallery_pictures` AS pic,
                `" . PREFIX . "gallery` AS gal
            WHERE
                gal.`userID` = " . (int)$userID . " AND
                gal.`galleryID` = pic.`galleryID`"
        );
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $size +=
                filesize('images/gallery/thumb/' . $ds['picID'] . '.jpg') +
                filesize($this->getLargeFile($ds['picID']));
        }
        return $size;
    }
}
