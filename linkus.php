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
    $action = "";
}

if (isset($_POST[ 'save' ])) {
    $_language->readModule('linkus');
    if (!ispageadmin($userID)) {
        die('<div class="alert alert-danger" role="alert">' . $_language->module[ 'no_access' ] . '</div>');
    }

    safe_query("INSERT INTO " . PREFIX . "linkus ( name ) VALUES( '" . $_POST[ 'name' ] . "' ) ");
    $id = mysqli_insert_id($_database);
    $banner = $_FILES[ 'banner' ];
    $filepath = "./images/linkus/";

    if ($banner[ 'name' ] != "") {
        move_uploaded_file($banner[ 'tmp_name' ], $filepath . $banner[ 'name' ] . ".tmp");
        @chmod($filepath . $banner[ 'name' ] . ".tmp", 0755);
        $getimg = getimagesize($filepath . $banner[ 'name' ] . ".tmp");
        if ($getimg[ 0 ] < 801 && $getimg[ 1 ] < 601) {
            $file = '';
            if ($getimg[ 2 ] == 1) {
                $file = $id . '.gif';
            } elseif ($getimg[ 2 ] == 2) {
                $file = $id . '.jpg';
            } elseif ($getimg[ 2 ] == 3) {
                $file = $id . '.png';
            }
            if ($file != "") {
                if (file_exists($filepath . $id . '.gif')) {
                    unlink($filepath . $id . '.gif');
                }
                if (file_exists($filepath . $id . '.jpg')) {
                    unlink($filepath . $id . '.jpg');
                }
                if (file_exists($filepath . $id . '.png')) {
                    unlink($filepath . $id . '.png');
                }
                rename($filepath . $banner[ 'name' ] . ".tmp", $filepath . $file);
                safe_query("UPDATE " . PREFIX . "linkus SET file='" . $file . "' WHERE bannerID='" . $id . "'");
            } else {
                if (unlink($filepath . $banner[ 'name' ] . ".tmp")) {
                    $error = $_language->module[ 'format_incorrect' ];
                    die('<div class="alert alert-danger" role="alert">
						<strong>' . $error . '</strong><br>
						<br>
						<a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $id .
                        '" class="alert-link">&laquo; ' . $_language->module[ 'back' ] . '</a>
					</div>');
                } else {
                    $error = $_language->module[ 'format_incorrect' ];
                    die('<div class="alert alert-danger" role="alert">
						<strong>' . $error . '</strong><br>
						<br>
						<a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $id .
                        '" class="alert-link">&laquo; ' . $_language->module[ 'back' ] . '</a>
					</div>');
                }
            }
        } else {
            @unlink($filepath . $banner[ 'name' ] . ".tmp");
            $error = $_language->module[ 'banner_to_big' ];
            die('<div class="alert alert-danger" role="alert">
				<strong>' . $error . '</strong><br>
				<br>
				<a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $id . '" class="alert-link">&laquo; ' .
                $_language->module[ 'back' ] . '</a>
			</div>');
        }
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $_language->readModule('linkus');
    if (!ispageadmin($userID)) {
        die('<div class="alert alert-danger" role="alert">' . $_language->module[ 'no_access' ] . '</div>');
    }

    safe_query(
        "UPDATE
          " . PREFIX . "linkus
        SET
          name='" . $_POST[ 'name' ] . "'
        WHERE
          bannerID='" . $_POST[ 'bannerID' ] . "'"
    );

    $filepath = "./images/linkus/";
    $id = $_POST[ 'bannerID' ];
    $banner = $_FILES[ 'banner' ];

    if ($banner[ 'name' ] != "") {
        move_uploaded_file($banner[ 'tmp_name' ], $filepath . $banner[ 'name' ] . ".tmp");
        @chmod($filepath . $banner[ 'name' ] . ".tmp", 0755);
        $getimg = getimagesize($filepath . $banner[ 'name' ] . ".tmp");
        if ($getimg[ 0 ] < 801 && $getimg[ 1 ] < 601) {
            $file = '';
            if ($getimg[ 2 ] == 1) {
                $file = $id . '.gif';
            } elseif ($getimg[ 2 ] == 2) {
                $file = $id . '.jpg';
            } elseif ($getimg[ 2 ] == 3) {
                $file = $id . '.png';
            }
            if ($file != "") {
                if (file_exists($filepath . $id . '.gif')) {
                    unlink($filepath . $id . '.gif');
                }
                if (file_exists($filepath . $id . '.jpg')) {
                    unlink($filepath . $id . '.jpg');
                }
                if (file_exists($filepath . $id . '.png')) {
                    unlink($filepath . $id . '.png');
                }
                rename($filepath . $banner[ 'name' ] . ".tmp", $filepath . $file);
                safe_query("UPDATE " . PREFIX . "linkus SET file='" . $file . "' WHERE bannerID='" . $id . "'");
            } else {
                if (unlink($filepath . $banner[ 'name' ] . ".tmp")) {
                    $error = $_language->module[ 'format_incorrect' ];
                    die('<div class="alert alert-danger" role="alert">
						<strong>' . $error . '</strong><br>
						<br>
						<a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $id .
                        '" class="alert-link">&laquo; ' . $_language->module[ 'back' ] . '</a>
					</div>');
                } else {
                    $error = $_language->module[ 'format_incorrect' ];
                    die('<div class="alert alert-danger" role="alert">
						<strong>' . $error . '</strong><br>
						<br>
						<a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $id .
                        '" class="alert-link">&laquo; ' . $_language->module[ 'back' ] . '</a>
					</div>');
                }
            }
        } else {
            @unlink($filepath . $banner[ 'name' ] . ".tmp");
            $error = $_language->module[ 'banner_to_big' ];
            die('<div class="alert alert-danger" role="alert">
				<strong>' . $error . '</strong><br>
				<br>
				<a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $id . '" class="alert-link">&laquo; ' .
                $_language->module[ 'back' ] . '</a>
			</div>');
        }
    }
} elseif (isset($_GET[ 'delete' ])) {
    include("_mysql.php");
    include("_settings.php");
    include('_functions.php');
    $_language->readModule('linkus');
    if (!ispageadmin($userID)) {
        die('<div class="alert alert-danger" role="alert">' . $_language->module[ 'no_access' ] . '</div>');
    }

    $bannerID = $_GET[ 'bannerID' ];
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

$_language->readModule('linkus');

eval ("\$title_linkus = \"" . gettemplate("title_linkus") . "\";");
echo $title_linkus;

if ($action == "new") {
    if (ispageadmin($userID)) {
        eval ("\$linkus_new = \"" . gettemplate("linkus_new") . "\";");
        echo $linkus_new;
    } else {
        redirect(
            'index.php?site=linkus',
            '<div class="alert alert-danger" role="alert">' . $_language->module[ 'no_access' ] . '</div>'
        );
    }
} elseif ($action == "edit") {
    if (ispageadmin($userID)) {
        $bannerID = $_GET[ 'bannerID' ];
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
        $name = getinput($ds[ 'name' ]);
        $banner = '<img src="images/linkus/' . $ds[ 'file' ] . '" alt="">';

        eval ("\$linkus_edit = \"" . gettemplate("linkus_edit") . "\";");
        echo $linkus_edit;
    } else {
        redirect(
            'index.php?site=linkus',
            '<div class="alert alert-danger" role="alert">' . $_language->module[ 'no_access' ] . '</div>'
        );
    }
} else {
    $filepath = "./images/linkus/";
    $filepath2 = "/images/linkus/";
    if (ispageadmin($userID)) {
        echo
            '<div class="form-group">
            <a href="index.php?site=linkus&amp;action=new" class="btn btn-primary" role="button">' .
            $_language->module[ 'new_banner' ] . '</a></div>';
    }
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "linkus ORDER BY name");
    if (mysqli_num_rows($ergebnis)) {
        $i = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {

            $name = htmloutput($ds[ 'name' ]);
            $fileinfo = getimagesize($filepath . $ds[ 'file' ]);
            if ($fileinfo[ 0 ] > $picsize_l) {
                $width = ' width="' . $picsize_l . '"';
            } else {
                $width = '';
            }
            if ($fileinfo[ 1 ] > $picsize_h) {
                $height = ' height="' . $picsize_h . '"';
            } else {
                $height = '';
            }
            $banner = '<img src="' . $filepath . $ds[ 'file' ] . '" class="img-responsive">';
            $code =
                '&lt;a href=&quot;http://' . $hp_url . '&quot;&gt;&lt;img src=&quot;http://' . $hp_url . $filepath2 .
                $ds[ 'file' ] . '&quot; alt=&quot;' . $myclanname . '&quot;&gt;&lt;/a&gt;';

            $adminaction = '';
            if (ispageadmin($userID)) {
                $adminaction = '<div class="pull-right">
					<a href="index.php?site=linkus&amp;action=edit&amp;bannerID=' . $ds[ 'bannerID' ] .
                    '" class="btn btn-warning btn-sm" role="button">' . $_language->module[ 'edit' ] . '</a>
					<a href="linkus.php?delete=true&amp;bannerID=' . $ds[ 'bannerID' ] .
                    '" class="btn btn-danger btn-sm" role="button">' . $_language->module[ 'delete' ] . '</a>
				</div>';
            }

            eval("\$linkus = \"" . gettemplate("linkus") . "\";");
            echo $linkus;
            $i++;
        }
    } else {
        echo '<div class="alert alert-info" role="alert">' . $_language->module[ 'no_banner' ] . '</div>';
    }
}
