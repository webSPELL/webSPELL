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

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if (isset($_POST[ 'save' ])) {
    $_language->readModule('links');
    if (!ispageadmin($userID) || !isnewsadmin($userID)) {
        echo generateAlert($_language->module['no_access'], 'alert-danger');
    } else {

        safe_query(
            "INSERT INTO
                " . PREFIX . "links (
                    linkcatID,
                    name,
                    url,
                    info
                )
            values (
                '" . (int)$_POST[ 'cat' ] . "',
                '" . strip_tags($_POST[ 'name' ]) . "',
                '" . $_POST[ 'url' ] . "',
                '" . $_POST[ 'info' ] . "'
            ) "
        );

        $filepath = "./images/links/";

        $upload = new \webspell\Upload('banner');

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

                            $id = mysqli_insert_id($_database);
                            $file = $id.$endung;

                            if ($upload->saveAs($filepath.$file)) {
                                @chmod($file, $new_chmod);
                                safe_query("UPDATE " . PREFIX . "links SET banner='" . $file . "' WHERE linkID='" . $id . "'");
                            }
                        } else {
                            echo generateErrorBox($_language->module[ 'banner_to_big' ]);
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
} elseif (isset($_POST[ 'saveedit' ])) {
    $_language->readModule('links');
    if (!ispageadmin($userID) || !isnewsadmin($userID)) {
        echo generateAlert($_language->module['no_access'], 'alert-danger');
    } else {

        safe_query(
            "UPDATE
                " . PREFIX . "links
            SET
                linkcatID='" . $_POST[ 'cat' ] . "',
                name='" . strip_tags($_POST[ 'name' ]) . "',
                url='" . $_POST[ 'url' ] . "',
                info='" . $_POST[ 'info' ] . "'
            WHERE
                linkID='" . $_POST[ 'linkID' ] . "'"
        );

        $filepath = "./images/links/";
        $id = $_POST[ 'linkID' ];

        $upload = new \webspell\Upload('banner');
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
                                @chmod($file, $new_chmod);
                                safe_query("UPDATE " . PREFIX . "links SET banner='" . $file . "' WHERE linkID='" . $id . "'");
                            }
                        } else {
                            echo generateErrorBox($_language->module[ 'banner_to_big' ]);
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
} elseif ($action == "delete") {
    include("_mysql.php");
    include("_settings.php");
    include("_functions.php");
    $_language->readModule('links');
    if (!ispageadmin($userID) || !isnewsadmin($userID)) {
        die($_language->module[ 'no_access' ]);
    }
    $linkID = $_GET[ 'linkID' ];
    safe_query("DELETE FROM " . PREFIX . "links WHERE linkID='$linkID'");
    $filepath = "./images/links/";
    if (file_exists($filepath . $linkID . '.gif')) {
        @unlink($filepath . $linkID . '.gif');
    }
    if (file_exists($filepath . $linkID . '.jpg')) {
        @unlink($filepath . $linkID . '.jpg');
    }
    if (file_exists($filepath . $linkID . '.png')) {
        @unlink($filepath . $linkID . '.png');
    }
    header("Location: index.php?site=links");
}

$_language->readModule('links');

eval ("\$title_links = \"" . gettemplate("title_links") . "\";");
echo $title_links;

if ($action == "new") {

    if (ispageadmin($userID) || isnewsadmin($userID)) {
        $rubrics = safe_query("SELECT * FROM " . PREFIX . "links_categorys ORDER BY name");
        $linkcats = '';
        while ($dr = mysqli_fetch_array($rubrics)) {
            $linkcats .= '<option value="' . $dr[ 'linkcatID' ] . '">' . htmlspecialchars($dr[ 'name' ]) . '</option>';
        }
        $bg1 = BG_1;
        eval ("\$links_new = \"" . gettemplate("links_new") . "\";");
        echo $links_new;
    } else {
        redirect('index.php?site=links', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "edit") {
    $linkID = $_GET[ 'linkID' ];
    if (ispageadmin($userID) || isnewsadmin($userID)) {
        $ds = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "links WHERE linkID='$linkID'"));

        $name = htmlspecialchars($ds[ 'name' ]);
        $url = htmlspecialchars($ds[ 'url' ]);
        $info = htmlspecialchars($ds[ 'info' ]);

        $newsrubrics = safe_query("SELECT * FROM " . PREFIX . "links_categorys ORDER BY name");
        if (mysqli_num_rows($newsrubrics)) {
            $linkcats = '';
            while ($dr = mysqli_fetch_array($newsrubrics)) {
                if ($ds[ 'linkcatID' ] == $dr[ 'linkcatID' ]) {
                    $linkcats .= '<option value="' . $dr[ 'linkcatID' ] . '" selected="selected">' .
                        htmlspecialchars($dr[ 'name' ]) . '</option>';
                } else {
                    $linkcats .= '<option value="' . $dr[ 'linkcatID' ] . '">' . htmlspecialchars($dr[ 'name' ]) .
                        '</option>';
                }
            }
        } else {
            $linkcats = '<option>' . $_language->module[ 'no_categories' ] . '</option>';
        }

        $linkcats = str_replace(" selected=\"selected\"", "", $linkcats);
        $linkcats =
            str_replace(
                'value="' . $ds[ 'linkcatID' ] . '"',
                'value="' . $ds[ 'linkcatID' ] . '" selected="selected"',
                $linkcats
            );

        $bg1 = BG_1;
        eval ("\$links_edit = \"" . gettemplate("links_edit") . "\";");
        echo $links_edit;
    } else {
        redirect('index.php?site=links', $_language->module[ 'no_access' ]);
    }
} elseif ($action == "show" && is_numeric($_GET[ 'linkcatID' ])) {
    if (ispageadmin($userID) || isnewsadmin($userID)) {
        echo
            '<a href="index.php?site=links&amp;action=new" class="btn btn-danger">' .
            $_language->module[ 'new_link' ] . '</a><br><br>';
    }

    $linkcatID = $_GET[ 'linkcatID' ];
    $getcat = safe_query("SELECT * FROM " . PREFIX . "links_categorys  WHERE linkcatID='$linkcatID'");
    $ds = mysqli_fetch_array($getcat);
    $linkcatname = $ds[ 'name' ];

    $linkcat = safe_query("SELECT * FROM " . PREFIX . "links WHERE linkcatID='$linkcatID' ORDER BY name");
    if (mysqli_num_rows($linkcat)) {
        eval ("\$links_details_head = \"" . gettemplate("links_details_head") . "\";");
        echo $links_details_head;

        $i = 1;
        while ($ds = mysqli_fetch_array($linkcat)) {
            if ($i % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }
            $i++;

            $link = '<a href="' . $ds[ 'url' ] . '" target="_blank"><strong>' . $ds[ 'name' ] . '</strong></a>';
            $info = cleartext($ds[ 'info' ]);
            if ($ds[ 'banner' ]) {
                $banner = '<a href="' . $ds[ 'url' ] . '" target="_blank"><img src="images/links/' . $ds[ 'banner' ] .
                    '" alt=""></a>';
            } else {
                $banner = '';
            }
            if (ispageadmin($userID) || isnewsadmin($userID)) {
                $adminaction =
                    '<a href="index.php?site=links&amp;action=edit&amp;linkID=' . $ds[ 'linkID' ] .
                    '" class="btn btn-danger">' . $_language->module[ 'edit' ] . '</a>
                    <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
                    '\', \'links.php?action=delete&amp;linkID=' . $ds[ 'linkID' ] . '\')" value="' .
                    $_language->module[ 'delete' ] . '" class="btn btn-danger">';
            } else {
                $adminaction = '';
            }

            eval ("\$links_details = \"" . gettemplate("links_details") . "\";");
            echo $links_details;

            unset($banner);
        }
        eval ("\$links_foot = \"" . gettemplate("links_foot") . "\";");
        echo $links_foot;
    } else {
        echo $_language->module[ 'no_links' ] . '<br><br>[ <a href="index.php?site=links">' .
            $_language->module[ 'go_back' ] . '</a> ]';
    }
} else {
    $_language->readModule('links');
    $cats = safe_query("SELECT * FROM " . PREFIX . "links_categorys ORDER BY name");
    if (mysqli_num_rows($cats)) {
        if (ispageadmin($userID) || isnewsadmin($userID)) {
            echo '<a href="index.php?site=links&amp;action=new" class="btn btn-danger">' .
                $_language->module[ 'new_link' ] . '</a><br><br>';
        }
        $anzcats = mysqli_num_rows(safe_query("SELECT linkcatID FROM " . PREFIX . "links_categorys"));
        $bg1 = BG_1;

        eval ("\$links_category = \"" . gettemplate("links_category") . "\";");
        echo $links_category;

        $i = 1;
        while ($ds = mysqli_fetch_array($cats)) {
            $anzlinks = mysqli_num_rows(
                safe_query(
                    "SELECT
                        linkID
                    FROM
                        " . PREFIX . "links
                    WHERE
                        linkcatID='" . $ds[ 'linkcatID' ] . "'"
                )
            );
            if ($i % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }
            $linkcatname =
                '<a href="index.php?site=links&amp;action=show&amp;linkcatID=' . $ds[ 'linkcatID' ] . '"><b>' .
                $ds[ 'name' ] . '</b></a>';

            eval ("\$links_content = \"" . gettemplate("links_content") . "\";");
            echo $links_content;
            $i++;
        }
        eval ("\$links_foot = \"" . gettemplate("links_foot") . "\";");
        echo $links_foot;
    } else {
        if (ispageadmin($userID) || isnewsadmin($userID)) {
            echo
                '<a href="admin/admincenter.php?site=linkcategories" class="btn btn-danger">' .
                $_language->module[ 'new_category' ] . '</a><br><br>';
        }
        echo $_language->module[ 'no_categories' ];
    }
}
