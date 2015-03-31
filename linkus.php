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

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = "";
}

if (isset($_POST['save'])) {
    $_language->readModule('linkus');
    if (!ispageadmin($userID)) {
        echo generateAlert($_language->module['no_access'], 'alert-danger');
    } else {
        $filepath = "./images/linkus/";

        $upload = new \webspell\HttpUpload('banner');

        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        if ($imageInformation[0] < 801 && $imageInformation[1] < 601) {
                            switch ($imageInformation[ 2 ]) {
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

                            safe_query("INSERT INTO " . PREFIX . "linkus ( name ) VALUES( '" . $_POST['name'] . "' ) ");
                            $id = mysqli_insert_id($_database);
                            $file = $id.$endung;

                            if ($upload->saveAs($filepath.$file)) {
                                @chmod($filepath.$file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "linkus SET file='" . $file . "' WHERE bannerID='" . $id . "'"
                                );
                            }
                        } else {
                            echo generateErrorBox($_language->module[ 'image_too_big' ]);
                        }
                    } else {
                        echo generateErrorBox($_language->module[ 'broken_image' ]);
                    }
                } else {
                    echo generateErrorBox($_language->module[ 'unsupported_image_type' ]);
                }
            } else {
                echo generateErrorBox($upload->translateError());
            }
        }
    }
} elseif (isset($_POST['saveedit'])) {
    $_language->readModule('linkus');
    if (!ispageadmin($userID)) {
        echo generateAlert($_language->module['no_access'], 'alert-danger');
    } else {
        safe_query(
            "UPDATE
                " . PREFIX . "linkus
            SET
                name='" . $_POST['name'] . "'
            WHERE
                bannerID='" . $_POST['bannerID'] . "'"
        );

        $filepath = "./images/linkus/";
        $id = $_POST['bannerID'];
        $upload = new \webspell\HttpUpload('banner');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        if ($imageInformation[0] < 801 && $imageInformation[1] < 601) {
                            switch ($imageInformation[ 2 ]) {
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

                            $file = $id.$endung;

                            if ($upload->saveAs($filepath.$file)) {
                                @chmod($filepath.$file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "linkus SET file='" . $file . "' WHERE bannerID='" . $id . "'"
                                );
                            }
                        } else {
                            echo generateErrorBox($_language->module[ 'image_too_big' ]);
                        }
                    } else {
                        echo generateErrorBox($_language->module[ 'broken_image' ]);
                    }
                } else {
                    echo generateErrorBox($_language->module[ 'unsupported_image_type' ]);
                }
            } else {
                echo generateErrorBox($upload->translateError());
            }
        }
    }
} elseif (isset($_GET['delete'])) {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    $_language->readModule('linkus');
    if (!ispageadmin($userID)) {
        echo generateAlert($_language->module['no_access'], 'alert-danger');
    } else {
        $bannerID = $_GET['bannerID'];
        $filepath = "./images/linkus/";
        safe_query("DELETE FROM " . PREFIX . "linkus WHERE bannerID='" . $bannerID . "'");
        if (file_exists($filepath . $bannerID . '.gif')) {
            @unlink($filepath . $bannerID . '.gif');
        }
        if (file_exists($filepath . $bannerID . '.jpg')) {
            @unlink($filepath . $bannerID . '.jpg');
        }
        if (file_exists($filepath . $bannerID . '.png')) {
            @unlink($filepath . $bannerID . '.png');
        }
        header("Location: index.php?site=linkus");
    }
}

$_language->readModule('linkus');

$title_linkus = $GLOBALS["_template"]->replaceTemplate("title_linkus", array());
echo $title_linkus;

if ($action == "new") {
    if (ispageadmin($userID)) {
        $linkus_new = $GLOBALS["_template"]->replaceTemplate("linkus_new", array());
        echo $linkus_new;
    } else {
        redirect(
            'index.php?site=linkus',
            generateAlert($_language->module['no_access'], 'alert-danger')
        );
    }
} elseif ($action == "edit") {
    if (ispageadmin($userID)) {
        $bannerID = $_GET['bannerID'];
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT
                    *
                FROM
                    " . PREFIX . "linkus
                WHERE
                    bannerID='" . $bannerID . "'"
            )
        );
        $name = getinput($ds['name']);
        $banner = '<img src="images/linkus/' . $ds['file'] . '" alt="">';

        $data_array = array();
        $data_array['$name'] = $name;
        $data_array['$bannerID'] = $bannerID;
        $linkus_edit = $GLOBALS["_template"]->replaceTemplate("linkus_edit", $data_array);
        echo $linkus_edit;
    } else {
        redirect(
            'index.php?site=linkus',
            generateAlert($_language->module['no_access'], 'alert-danger')
        );
    }
} else {
    $filepath = "./images/linkus/";
    $filepath2 = "/images/linkus/";
    if (ispageadmin($userID)) {
        echo
            '<div class="form-group">
            <a href="index.php?site=linkus&amp;action=new" class="btn btn-primary" role="button">' .
            $_language->module['new_banner'] . '</a></div>';
    }
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "linkus ORDER BY name");
    if (mysqli_num_rows($ergebnis)) {
        $i = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $name = htmloutput($ds['name']);
            $banner = '<img src="' . $filepath . $ds['file'] . '" class="img-responsive">';
            $code =
                '&lt;a href=&quot;http://' . $hp_url . '&quot;&gt;&lt;img src=&quot;http://' . $hp_url . $filepath2 .
                $ds['file'] . '&quot; alt=&quot;' . $myclanname . '&quot;&gt;&lt;/a&gt;';

            $adminaction = '';
            if (ispageadmin($userID)) {
                $adminaction = '<div class="pull-right">
                    <a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $ds['bannerID'] .
                    '" class="btn btn-warning btn-sm" role="button">' . $_language->module['edit'] . '</a>
                    <a href="linkus.php?delete=true&amp;bannerID=' . $ds['bannerID'] .
                    '" class="btn btn-danger btn-sm" role="button">' . $_language->module['delete'] . '</a>
                </div>';
            }

            $data_array = array();
            $data_array['$name'] = $name;
            $data_array['$banner'] = $banner;
            $data_array['$code'] = $code;
            $data_array['$adminaction'] = $adminaction;
            $linkus = $GLOBALS["_template"]->replaceTemplate("linkus", $data_array);
            echo $linkus;
            $i++;
        }
    } else {
        echo generateAlert($_language->module['no_banner'], 'alert-info');
    }
}
