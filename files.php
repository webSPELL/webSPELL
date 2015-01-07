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

$_language->readModule('files');
function get_all_sub_cats($parent, $start = 0)
{
    $end = 0;
    if ($start == 1) {
        $cat_query = "( filecatID='" . $parent . "' ";
    } else {
        $cat_query = "";
    }
    $get_catIDs = safe_query(
        "SELECT
            `filecatID`
        FROM
            `" . PREFIX . "files_categorys`
        WHERE
            `subcatID` = '" . (int)$parent."'"
    );

    if (mysqli_num_rows($get_catIDs)) {
        while ($dc = mysqli_fetch_assoc($get_catIDs)) {
            $cat_query .= " || filecatID='" . $dc[ 'filecatID' ] . "'";
            $more = mysqli_num_rows(
                safe_query(
                    "SELECT
                        `filecatID`
                    FROM
                        `" . PREFIX . "files_categorys`
                    WHERE
                        `subcatID` = '" . (int)$dc[ 'filecatID' ]."'"
                )
            );
            if ($more > 0) {
                $cat_query .= get_all_sub_cats($dc[ 'filecatID' ], 0);
            }
        }
    }
    if ($start == 1) {
        $cat_query .= ")";
    }
    return $cat_query;
}

function unit_to_size($num, $unit)
{
    switch ($unit) {
        case 'b':
            $size = $num;
            break;
        case 'kb':
            $size = $num * 1024;
            break;
        case 'mb':
            $size = $num * 1024 * 1024;
            break;
        case 'gb':
            $size = $num * 1024 * 1024 * 1024;
            break;
    }
    return $size;
}

eval ("\$title_files = \"" . gettemplate("title_files") . "\";");
echo $title_files;

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = "";
}

if ($action == "save") {
    if (!isfileadmin($userID)) {
        die(redirect("index.php?site=files", $_language->module[ 'no_access' ], "3"));
    }

    $upfile = $_FILES[ 'upfile' ];
    $poster = $_POST[ 'poster' ];
    $filecat = $_POST[ 'filecat' ];
    $filename = $_POST[ 'filename' ];
    $fileurl = $_POST[ 'fileurl' ];
    $filesize = unit_to_size($_POST[ 'filesize' ], $_POST[ 'unit' ]);
    $info = $_POST[ 'info' ];
    $accesslevel = $_POST[ 'accesslevel' ];
    $mirror1 = $_POST[ 'mirror2' ];
    $mirror2 = $_POST[ 'mirror3' ];

    // MIRRORS
    if (stristr($mirror1, "http://") || stristr($mirror1, "ftp://")) {
        if (stristr($mirror2, "http://") || stristr($mirror2, "ftp://")) {
            $mirrors = $mirror1 . '||' . $mirror2;
        } else {
            $mirrors = $mirror1;
        }
    } elseif (stristr($mirror2, "http://") || stristr($mirror2, "ftp://")) {
        $mirrors = $mirror2;
    } else {
        $mirrors = '';
    }

    if ($upfile || $fileurl) {
        $filepath = "./downloads/";
        if ($upfile[ 'name' ] != "") {
            $des_file = $filepath . $upfile[ 'name' ];
            if (!file_exists($des_file)) {
                if (move_uploaded_file($upfile[ 'tmp_name' ], $des_file)) {
                    $file = $upfile[ 'name' ];
                    $filesize = $upfile[ 'size' ];
                    @chmod($des_file, $new_chmod);
                }
            } else {
                $date = time();
                $des_file = $filepath . $date . "_" . $upfile[ 'name' ];
                if (!file_exists($des_file)) {
                    if (move_uploaded_file($upfile[ 'tmp_name' ], $des_file)) {
                        $file = $date . "_" . $upfile[ 'name' ];
                        $filesize = $upfile[ 'size' ];
                        @chmod($des_file, $new_chmod);
                    }
                } else {
                    die($_language->module[ 'file_already_exists' ]);
                }
            }
        } else {
            if (stristr($fileurl, "http://") || stristr($fileurl, "ftp://")) {
                $file = $fileurl;
            }
        }

        if (
            safe_query(
                "INSERT INTO
                    `" . PREFIX . "files` (
                        `filecatID`,
                        `poster`,
                        `date`,
                        `filename`,
                        `filesize`,
                        `info`,
                        `file`,
                        `mirrors`,
                        `downloads`,
                        `accesslevel`
                    )
                    VALUES (
                        '" . $filecat . "',
                        '" . $poster . "',
                        '" . time() . "',
                        '" . $filename . "',
                        '" . $filesize . "',
                        '" . $info . "',
                        '" . $file . "',
                        '" . $mirrors . "',
                        '0',
                        '" . $accesslevel . "'
                    )"
            )
        ) {
            redirect(
                "index.php?site=files&amp;file=" . mysqli_insert_id($_database) . "",
                $_language->module[ 'file_created' ],
                "3"
            );
        } else {
            redirect("index.php?site=files", $_language->module[ 'file_not_created' ], "3");
        }
    } else {
        redirect("index.php?site=files", $_language->module[ 'no_valid_file' ], "3");
    }
} elseif ($action == "saveedit") {
    if (!isfileadmin($userID)) {
        die(redirect("index.php?site=files", $_language->module[ 'no_access' ], "3"));
    }

    $fileID = $_POST[ 'fileID' ];
    $upfile = $_FILES[ 'upfile' ];
    $filecat = $_POST[ 'filecat' ];
    $filename = $_POST[ 'filename' ];
    $fileurl = $_POST[ 'fileurl' ];
    $filesize = unit_to_size($_POST[ 'filesize' ], $_POST[ 'unit' ]);
    $info = $_POST[ 'info' ];
    $accesslevel = $_POST[ 'accesslevel' ];
    $mirror1 = $_POST[ 'mirror2' ];
    $mirror2 = $_POST[ 'mirror3' ];
    unset($file);

    // MIRRORS
    if (stristr($mirror1, "http://") || stristr($mirror1, "ftp://")) {
        if (stristr($mirror2, "http://") || stristr($mirror2, "ftp://")) {
            $mirrors = $mirror1 . '||' . $mirror2;
        } else {
            $mirrors = $mirror1;
        }
    } elseif (stristr($mirror2, "http://") || stristr($mirror2, "ftp://")) {
        $mirrors = $mirror2;
    } else {
        $mirrors = '';
    }

    $filepath = "./downloads/";
    if ($upfile[ 'name' ] != "") {
        $des_file = $filepath . $upfile[ 'name' ];
        if (file_exists($des_file)) {
            unlink($des_file);
        }
        if (move_uploaded_file($upfile[ 'tmp_name' ], $des_file)) {
            $file = $upfile[ 'name' ];
            $filesize = $upfile[ 'size' ];
            chmod($des_file, $new_chmod);
        }
    } else {
        if ((stristr($fileurl, "http://") || stristr($fileurl, "ftp://")) && $fileurl != "http://") {
            $file = $fileurl;
        }
    }
    safe_query(
        "UPDATE
            `" . PREFIX . "files`
        SET
            `filecatID` = '" . $filecat . "',
            `mirrors` = '" . $mirrors . "',
            `filename` = '" . $filename . "',
            `filesize` = '" . $filesize . "',
            `info` = '" . $info . "',
            `accesslevel` = '" . $accesslevel . "'
        WHERE
            `fileID` = '" . (int)$fileID."'"
    ) || die(redirect("index.php?site=files", $_language->module[ 'failed_save_file-info' ], "3"));
    if (isset($file)) {
        if (
            !safe_query(
                "UPDATE `" . PREFIX . "files` SET `file` = '" . $file . "' WHERE `fileID` = '" . (int)$fileID
            )
        ) {
            die(redirect("index.php?site=files", $_language->module[ 'failed_edit_file' ], "3"));
        }
    }
    redirect("index.php?site=files", $_language->module[ 'successful' ]);
} elseif ($action == "delete") {
    if (!isfileadmin($userID)) {
        die(redirect("index.php?site=files", $_language->module[ 'no_access' ], "3"));
    }

    if (isset($_GET[ 'cat' ])) {
        $cat = $_GET[ 'cat' ];
    } else {
        $cat = '';
    }
    if (isset($_GET[ 'ref' ])) {
        $ref = $_GET[ 'ref' ];
    } else {
        $ref = '';
    }
    if ($cat) {
        $ref = '&amp;cat=' . $cat;
    }
    $file = (int)$_GET[ 'file' ];

    if ($file) {
        $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "files` WHERE `fileID` = '" . $file."'");
        $ds = mysqli_fetch_array($ergebnis);

        if (!stristr($ds[ 'file' ], "http://") && !stristr($ds[ 'file' ], "ftp://")) {
            @unlink('./downloads/' . $ds[ 'file' ]);
        }

        if (safe_query("DELETE FROM `" . PREFIX . "files` WHERE `fileID` = '" . (int)$file."'")) {
            redirect("index.php?site=files" . $ref, $_language->module[ 'file_deleted' ], "3");
        } else {
            redirect("index.php?site=files", $_language->module[ 'file_not_deleted' ], "3");
        }
    } else {
        redirect("index.php", $_language->module[ 'cant_delete_without_fileID' ], "3");
    }
} elseif ($action == "newfile") {
    // ADMINACTIONS
    $adminactions =
        '<div class="row"><div class="col-xs-6"><a href="index.php?site=files" class="btn btn-primary">' .
        $_language->module[ 'files' ] . '</a></div>';

    if (isfileadmin($userID)) {
        $adminactions .=
            '<div class="col-xs-6 text-right">
                <a href="admin/admincenter.php?site=filecategorys" class="btn btn-danger">' .
                    $_language->module[ 'new_category' ] . '</a>
            </div></div>';

        function generate_options($filecats = '', $offset = '', $subcatID = 0)
        {
            $rubrics = safe_query(
                "SELECT
                    *
                FROM
                    `" . PREFIX . "files_categorys`
                WHERE
                    `subcatID` = '" . (int)$subcatID . "'
                ORDER BY
                    name"
            );
            while ($dr = mysqli_fetch_array($rubrics)) {
                $filecats .= '<option value="' . $dr[ 'filecatID' ] . '">' .
                    $offset . htmlspecialchars($dr[ 'name' ]) . '</option>';
                if (
                    mysqli_num_rows(
                        safe_query(
                            "SELECT
                                *
                            FROM
                                `" . PREFIX . "files_categorys`
                            WHERE
                                `subcatID` = '" . (int)$dr[ 'filecatID' ]."'"
                        )
                    )
                ) {
                    $filecats .= generate_options("", $offset . "- ", $dr[ 'filecatID' ]);
                }
            }
            return $filecats;
        }

        $filecats = generate_options();

        $access = '<option value="0">' . $_language->module[ 'all' ] . '</option><option value="1">' .
            $_language->module[ 'registered' ] . '</option><option value="2">' . $_language->module[ 'clanmember' ] .
            '</option>';

        $bg1 = BG_1;

        if ($filecats == '') {
            redirect('index.php?site=files', $_language->module[ 'first_create_file-category' ], '3');
        } else {
            echo $adminactions;
            eval("\$files_new = \"" . gettemplate("files_new") . "\";");
            echo $files_new;
        }
    } else {
        redirect("index.php?site=files", $_language->module[ 'no_access' ], "3");
    }
} elseif ($action == "edit") {
    $fileID = $_GET[ 'fileID' ];
    if ($fileID) {
        if (isfileadmin($userID)) {
            // ADMINACTIONS
            $adminactions =
                '<a href="admin/admincenter.php?site=filecategories" class="btn btn-danger">' .
                    $_language->module[ 'new_category' ] . '</a>';

            function generate_options($filecats = '', $offset = '', $subcatID = 0)
            {
                $rubrics = safe_query(
                    "SELECT
                        *
                    FROM
                        `" . PREFIX . "files_categorys`
                    WHERE
                        `subcatID` = '" . (int)$subcatID . "'
                    ORDER BY
                        name"
                );
                while ($dr = mysqli_fetch_array($rubrics)) {
                    $filecats .=
                        '<option value="' . $dr[ 'filecatID' ] . '">' . $offset . htmlspecialchars($dr[ 'name' ]) .
                        '</option>';
                    if (
                        mysqli_num_rows(
                            safe_query(
                                "SELECT
                                    *
                                FROM
                                    `" . PREFIX . "files_categorys`
                                WHERE
                                    `subcatID` = '" . (int)$dr[ 'filecatID' ]."'"
                            )
                        )
                    ) {
                        $filecats .= generate_options("", $offset . "- ", $dr[ 'filecatID' ]);
                    }
                }
                return $filecats;
            }

            $filecats = generate_options();

            $file = mysqli_fetch_array(
                safe_query("SELECT * FROM `" . PREFIX . "files` WHERE `fileID` = '" . (int)$fileID."'")
            );
            $filecats = str_replace(
                'value="' . $file[ 'filecatID' ] . '"',
                'value="' . $file[ 'filecatID' ] . '" selected="selected"',
                $filecats
            );
            $accessmenu = '<option value="0">' . $_language->module[ 'all' ] . '</option><option value="1">' .
                $_language->module[ 'registered' ] . '</option><option value="2">' .
                $_language->module[ 'clanmember' ] . '</option>';
            $access = str_replace(
                'value="' . $file[ 'accesslevel' ] . '"',
                'value="' . $file[ 'accesslevel' ] . '" selected="selected"',
                $accessmenu
            );

            $sizeinfo = strtolower(detectfilesize($file[ 'filesize' ]));
            $sizeinfo = explode(" ", $sizeinfo);

            $filesize = $sizeinfo[ 0 ];
            $description = htmlspecialchars($file[ 'info' ]);
            $name = htmlspecialchars($file[ 'filename' ]);
            $unit = '
                <option value="b">Byte</option>
                <option value="kb">KByte</option>
                <option value="mb">MByte</option>
                <option value="gb">GByte</option>';

            switch ($sizeinfo[ 1 ]) {
                case 'byte':
                    $unit = str_replace('value="b"', 'value="b" selected="selected"', $unit);
                    break;
                case 'kb':
                    $unit = str_replace('value="kb"', 'value="kb" selected="selected"', $unit);
                    break;
                case 'mb':
                    $unit = str_replace('value="mb"', 'value="mb" selected="selected"', $unit);
                    break;
                case 'gb':
                    $unit = str_replace('value="gb"', 'value="gb" selected="selected"', $unit);
                    break;
            }
            $extern = 'http://';
            if (stristr($file[ 'file' ], "http://") || stristr($file[ 'file' ], "ftp://")) {
                $extern = $file[ 'file' ];
            }
            // FILE-MIRRORS (remember: the primary mirror is still the uploaded or external file!)
            $mirror2 = "";
            $mirror3 = "";
            $mirrors = $file[ 'mirrors' ];
            if ($mirrors) {
                if (stristr($mirrors, "||")) {
                    $secondarymirror = explode("||", $mirrors);
                    $mirror2 = $secondarymirror[ 0 ];
                    $mirror3 = $secondarymirror[ 1 ];
                } else {
                    $mirror2 = $mirrors;
                }
            }

            eval("\$files_edit = \"" . gettemplate("files_edit") . "\";");
            echo $files_edit;
        } else {
            redirect("index.php?site=files", $_language->module[ 'no_access' ], "3");
        }
    } else {
        redirect("index.php", $_language->module[ 'cant_edit_without_fileID' ], "3");
    }
} elseif (isset($_GET[ 'cat' ])) {
    $accesslevel = 1;
    if (isclanmember($userID)) {
        $accesslevel = 2;
    }
    // ADMINACTIONS
    $adminactions = '';
    if (isfileadmin($userID)) {
        $adminactions =
            '<div class="row">
                <div class="col-xs-6">
                    <a href="index.php?site=files&amp;action=newfile" class="btn btn-danger">' .
                        $_language->module[ 'new_file' ] .'
                    </a>
                </div>
                <div class="col-xs-6 text-right">
                    <a href="admin/admincenter.php?site=filecategorys" class="btn btn-danger">' .
                        $_language->module[ 'new_category' ] . '
                    </a>
                </div>
            </div><br>';
    }
    echo $adminactions;

    // CATEGORY
    $catID = $_GET[ 'cat' ];
    $cat = mysqli_fetch_array(
        safe_query(
            "SELECT
                `filecatID`,
                `name`,
                `subcatID`
            FROM
                `" . PREFIX . "files_categorys`
            WHERE `filecatID` = '" . (int)$catID."'"
        )
    );
    $category = $cat[ 'name' ];

    $cat_id = $cat[ 'subcatID' ];
    while ($cat_id != 0) {
        $subcat = mysqli_fetch_array(
            safe_query(
                "SELECT
                    `filecatID`,
                    `name`,
                    `subcatID`
                FROM
                    `" . PREFIX . "files_categorys`
                WHERE
                    `filecatID` = '" . (int)$cat_id."'"
            )
        );
        $category = '<a href="index.php?site=files&amp;cat=' . $subcat[ 'filecatID' ] . '" class="titlelink">' .
            $subcat[ 'name' ] . '</a> &raquo; ' . $category;
        $cat_id = $subcat[ 'subcatID' ];
    }

    unset($n);

    // SUBCATEGORIES

    $subcats = safe_query(
        "SELECT
            *
        FROM
            `" . PREFIX . "files_categorys`
        WHERE
            `subcatID` = '" . (int)$cat[ 'filecatID' ] . "'
        ORDER BY
            name"
    );
    if (mysqli_num_rows($subcats)) {
        eval("\$files_category_head = \"" . gettemplate("files_category_head") . "\";");
        echo $files_category_head;

        eval("\$files_category_list = \"" . gettemplate("files_subcat_list_head") . "\";");
        echo $files_category_list;

        while ($subcat = mysqli_fetch_array($subcats)) {
            $catname = '<a href="index.php?site=files&amp;cat=' . $subcat[ 'filecatID' ] . '"><b>' . $subcat[ 'name' ] .
                '</b></a>';
            $downloads = 0;
            $sub_cat_qry = get_all_sub_cats($subcat[ 'filecatID' ], 1);
            $query =
                safe_query(
                    "SELECT
                        `downloads`
                    FROM
                        `" . PREFIX . "files`
                    WHERE
                        `" . $sub_cat_qry . "` AND
                        `accesslevel` <= " . (int)$accesslevel . "
                    ORDER BY
                        `fileID` DESC"
                );
            $cat_file_total = mysqli_num_rows($query);
            while ($ds = mysqli_fetch_array($query)) {
                $downloads += $ds[ 'downloads' ];
            }
            $subcategories =
                mysqli_num_rows(
                    safe_query(
                        "SELECT
                            `filecatID`
                        FROM
                            `" . PREFIX . "files_categorys`
                        WHERE `subcatID` = '" . (int)$subcat[ 'filecatID' ]
                    )
                );

            eval("\$files_category_list = \"" . gettemplate("files_subcat_list") . "\";");
            echo $files_category_list;
        }
        eval("\$files_category_list = \"" . gettemplate("files_subcat_list_foot") . "\";");
        echo $files_category_list;
    }

    // FILES
    $files = safe_query(
        "SELECT
            *
        FROM
            `" . PREFIX . "files`
        WHERE
            `filecatID` = '" . (int)$cat[ 'filecatID' ] . "' AND
            `accesslevel` <= " . (int)$accesslevel . "
        ORDER BY
            `filename`"
    );
    if (mysqli_num_rows($files)) {
        eval("\$files_category_list = \"" . gettemplate("files_category_list_head") . "\";");
        echo $files_category_list;

        $n = 0;

        while ($file = mysqli_fetch_array($files)) {
            $n++;
            if ($n % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_4;
                $bg2 = BG_3;
            }

            $fileid = $file[ 'fileID' ];
            $filename =
                '<a href="index.php?site=files&amp;file=' . $fileid . '"><b>' . clearfromtags($file[ 'filename' ]) .
                '</b></a>';
            $fileinfo = cleartext($file[ 'info' ]);
            $fileinfo = toggle($fileinfo, $file[ 'fileID' ]);
            $filesize = $file[ 'filesize' ];
            $fileload = $file[ 'downloads' ];
            $filevotes = $file[ 'votes' ];
            $filevotes ? $filevotes = ', ' . $filevotes . ' votes' : $filevotes = ', unrated';
            $filedate = getformatdatetime($file[ 'date' ]);
            $traffic = $filesize * $fileload;
            $rating = $file[ 'rating' ];

            // RATING
            $rating = $file[ 'rating' ];
            $rating ? $rating = $rating . ' / 10' : $rating = '0 / 10';
            $ratings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for ($i = 0; $i < $file[ 'rating' ]; $i++) {
                $ratings[ $i ] = 1;
            }
            $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif">';
            foreach ($ratings as $pic) {
                $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif">';
            }

            if (!$userID && $file[ 'accesslevel' ] >= 1) {
                $link = '(R)';
            } else {
                $link = '<a href="download.php?fileID=' . $fileid . '">
                <span class="icon-download icon-large"></span>
                </a>';
            }

            eval("\$files_category_list = \"" . gettemplate("files_category_list") . "\";");
            echo $files_category_list;
        }
        eval("\$files_category_list = \"" . gettemplate("files_category_list_foot") . "\";");
        echo $files_category_list;
    }
    if (!isset($n)) {
        echo "<br>" . $_language->module[ 'cant_display_empty_cat' ];
    }
} elseif (isset($_GET[ 'file' ])) {
    // ADMINACTIONS
    $adminactions = '';
    if (isfileadmin($userID)) {
        $adminactions =
            '<div class="row">
                <div class="col-xs-6">
                    <a href="index.php?site=files&amp;action=newfile" class="btn btn-danger">' .
                        $_language->module[ 'new_file' ] . '
                    </a>
                </div>
                <div class="col-xs-6 text-right">
                    <a href="admin/admincenter.php?site=filecategorys" class="btn btn-danger">' .
                        $_language->module[ 'new_category' ] . '
                    </a>
                </div>
            </div><br>';
    }

    // FILE-INFORMATION
    $file = mysqli_fetch_array(
        safe_query(
            "SELECT
                *
            FROM
                `" . PREFIX . "files`
            WHERE
                `fileID` = '" . $_GET[ 'file' ]."'"
        )
    );
    if ($file[ 'accesslevel' ] == 2 && !isclanmember($userID)) {
        die($_language->module[ 'no_access' ]);
    }

    $fileID = $file[ 'fileID' ];
    $filename = clearfromtags($file[ 'filename' ]);
    $fileinfo = cleartext($file[ 'info' ]);
    $fileinfo = toggle($fileinfo, $file[ 'fileID' ]);
    $filesize = $file[ 'filesize' ];
    if (!$filesize) {
        $filesize = 0;
    }
    $downloads = $file[ 'downloads' ];
    if (!$downloads) {
        $downloads = 0;
    }
    $filevotes = $file[ 'votes' ];
    $filevotes ? $filevotes = ', ' . $filevotes . ' votes' : $filevotes = ', unrated';
    $traffic = detectfilesize($filesize * $downloads);
    $filesize = detectfilesize($file[ 'filesize' ]);
    $reportlink = '<a href="index.php?site=files&amp;action=report&amp;link=' . $file[ 'fileID' ] . '"><b>' .
        $_language->module[ 'report_dead_link' ] . '</b></a>';
    $date = getformatdate($file[ 'date' ]);

    // FILE-AUTHOR
    $uploader =
        cleartext('[flag]' . getcountry($file[ 'poster' ]) . '[/flag]') . ' <a href="index.php?site=profile&amp;id=' .
        $file[ 'poster' ] . '">' . getnickname($file[ 'poster' ]) . '</a>';

    // FILE-CATEGORY
    $cat = mysqli_fetch_array(
        safe_query(
            "SELECT
                *
            FROM
                `" . PREFIX . "files_categorys`
            WHERE
                `filecatID` = '" . (int)$file[ 'filecatID' ]."'"
        )
    );
    $category = '<a href="index.php?site=files&amp;cat=' . $cat[ 'filecatID' ] . '" class="titlelink">' .
        $cat[ 'name' ] . '</a>';
    $categories = '<a href="index.php?site=files&amp;cat=' . $cat[ 'filecatID' ] . '"><strong>' .
        $cat[ 'name' ] . '</strong></a>';

    $cat_id = $cat[ 'subcatID' ];
    while ($cat_id != 0) {
        $subcat = mysqli_fetch_array(
            safe_query(
                "SELECT
                    `filecatID`,
                    `name`,
                    `subcatID`
                FROM
                    `" . PREFIX . "files_categorys`
                WHERE
                    `filecatID` = '" . (int)$cat_id."'"
            )
        );
        $category = '<a href="index.php?site=files&amp;cat=' . $subcat[ 'filecatID' ] . '" class="titlelink">' .
            $subcat[ 'name' ] . '</a> >> ' . $category;
        $categories =
            '<a href="index.php?site=files&amp;cat=' . $subcat[ 'filecatID' ] . '"><b>' . $subcat[ 'name' ] .
            '</b></a> >> ' . $categories;
        $cat_id = $subcat[ 'subcatID' ];
    }

    // FILE-MIRRORS (remember: the primary mirror is still the uploaded or external file!)
    $mirrors = $file[ 'mirrors' ];
    if ($mirrors) {
        if (stristr($mirrors, "||")) {
            $secondarymirror = explode("||", $mirrors);
            $mirrorlist = '&#8226; <a href="' . $secondarymirror[ 0 ] . '" target="_blank">' .
                $_language->module[ 'download_via_mirror' ] . ' #2</a><br>&#8226; <a href="' . $secondarymirror[ 1 ] .
                '" target="_blank">' . $_language->module[ 'download_via_mirror' ] . ' #3</a>';
        } else {
            $mirrorlist =
                '&#8226; <a href="' . $mirrors . '" target="_blank">' . $_language->module[ 'download_via_mirror' ] .
                ' #2</a>';
        }
    } else {
        $mirrorlist = $_language->module[ 'no_mirrors' ];
    }

    if ($file[ 'accesslevel' ] && !$userID) {
        $mirrorlist = $_language->module[ 'please_login' ];
    }

    // RATING
    $rating = $file[ 'rating' ];
    $rating ? $rating = $rating . ' / 10' : $rating = '0 / 10';
    $ratings = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    for ($i = 0; $i < $file[ 'rating' ]; $i++) {
        $ratings[ $i ] = 1;
    }
    $ratingpic = '<img src="images/icons/rating_' . $ratings[ 0 ] . '_start.gif">';
    foreach ($ratings as $pic) {
        $ratingpic .= '<img src="images/icons/rating_' . $pic . '.gif">';
    }
    if ($loggedin) {
        $getfiles = safe_query("SELECT `files` FROM `" . PREFIX . "user` WHERE `userID` = '" . (int)$userID."'");
        $found = false;
        if (mysqli_num_rows($getfiles)) {
            $ga = mysqli_fetch_array($getfiles);
            if ($ga[ 'files' ] != "") {
                $string = $ga[ 'files' ];
                $array = explode(":", $string);
                $anzarray = count($array);
                for ($i = 0; $i < $anzarray; $i++) {
                    if ($array[ $i ] == $file[ 'fileID' ]) {
                        $found = true;
                    }
                }
            }
        }
        if ($found) {
            $rateform = "<i>" . $_language->module[ 'you_have_already_rated' ] . "</i>";
        } else {
            $rateform = '<form method="post" name="rating_file' . $file[ 'fileID' ] .
                '" action="rating.php" role="form">
            <td>' . $_language->module[ 'rate_now' ] . '</td>
            <td><div class="input-group">
                <select name="rating" class="form-control">
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

                <span class="input-group-btn">
                    <input type="submit" name="Submit" value="' . $_language->module[ 'rate' ] .
                        '" class="btn btn-primary">
                </span>
            </div></td>
            <input type="hidden" name="userID" value="' . $userID . '">
            <input type="hidden" name="type" value="fi">
            <input type="hidden" name="id" value="' . $file[ 'fileID' ] . '">
        </form>';
        }
    } else {
        $rateform = '<i>' . $_language->module[ 'rate_have_to_reg_login' ] . '</i>';
    }

    // DISPLAY
    $bg1 = BG_1;
    $bg2 = BG_2;
    $border = BORDER;
    $pagebg = PAGEBG;

    $admintools = '';
    // ADMINTOOLS
    if (isfileadmin($userID)) {
        $admintools = '<tr><td colspan="2" class="text-right">';
        $admintools .=
            '<a href="index.php?site=files&amp;action=edit&amp;fileID=' . $file[ 'fileID' ] .
                '" class="btn btn-danger">' . $_language->module[ 'edit_file' ] . '</a>';
        $admintools .= '<input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete_file' ] .
            '\', \'index.php?site=files&amp;action=delete&amp;file=' . $file[ 'fileID' ] . '\')" value="' .
            $_language->module[ 'delete_file' ] . '" class="btn btn-danger"> ';
        $admintools .= '</td></tr>';
    }

    $accesslevel = 0;
    if ($userID) {
        $accesslevel = 1;
    }
    if (isclanmember($userID)) {
        $accesslevel = 2;
    }

    if ($file[ 'accesslevel' ] <= $accesslevel) {
        $link = '<a href="download.php?fileID=' . $fileID .
            '" class="btn btn-lg btn-success"><span class="icon-download icon-large"></span> ' .
            str_replace('%filename%', $filename, $_language->module[ 'download_now' ]) . '</a>';
    } else {
        $link = $_language->module[ 'download_registered_only' ] . '<br><a href="index.php?site=login">' .
            $_language->module[ 'login' ] . '</a> | <a href="index.php?site=register">' .
            $_language->module[ 'register' ] . '</a>';
    }

    eval("\$files_display = \"" . gettemplate("files_display") . "\";");
    echo $files_display;
} elseif ($action == "report") {
    // DEAD-LINK TICKET SYSTEM
    $mode = 'deadlink';
    $type = 'files';
    $id = getforminput($_GET[ 'link' ]);
    $referer = $hp_url . '/index.php?site=files&amp;fileID=' . $id;

    if ($id) {
        $type = 'files';
        $captcha_form = "";
        $type_ = "";
        if (!$userID) {
            $CAPCLASS = new \webspell\Captcha();
            $captcha = $CAPCLASS->createCaptcha();
            $hash = $CAPCLASS->getHash();
            $CAPCLASS->clearOldCaptcha();
            $captcha_form =
                '<div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon captcha-img">' . $captcha . '</span>
                        <input type="number" name="captcha" size="5" maxlength="5" placeholder="Captcha"
                            class="form-control">
                        <input name="captcha_hash" type="hidden" value="' . $hash . '">
                    </div>
                </div>';
        }

        eval("\$report_deadlink = \"" . gettemplate("report_deadlink") . "\";");
        echo $report_deadlink;
    } else {
        redirect("index.php?site=files", $_language->module[ 'cant_report_without_fileID' ], "3");
    }
} else {
    $accesslevel = 1;
    $adminactions = '';
    if (isclanmember($userID)) {
        $accesslevel = 2;
    }
    if (isfileadmin($userID)) {
        $adminactions =
            '<div class="text-right">
                <a href="index.php?site=files&amp;action=newfile" class="btn btn-danger">' .
                    $_language->module[ 'new_file' ] . '</a>
                <a href="admin/admincenter.php?site=filecategorys" class="btn btn-danger">' .
                    $_language->module[ 'new_category' ] . '</a>
            </div><br>';
    }

    // STATS

    // categories in database
    $catQry = safe_query(
        "SELECT
            *
        FROM
            `" . PREFIX . "files_categorys`
        WHERE
            `subcatID` = '0'
        ORDER BY
            `name`"
    );

    $totalcats = mysqli_num_rows($catQry);
    if ($totalcats) {
        // files in database
        $fileQry = safe_query("SELECT * FROM `" . PREFIX . "files`");
        $totalfiles = mysqli_num_rows($fileQry);
        if ($totalfiles) {
            $hddspace = 0;
            $traffic = 0;
            // total traffic caused by downloads
            while ($file = mysqli_fetch_array($fileQry)) {
                $filesize = $file[ 'filesize' ];
                $fileload = $file[ 'downloads' ];
                $hddspace += $filesize;
                $traffic += $filesize * $fileload;
                $rating = $file[ 'rating' ];
            }
            $traffic = detectfilesize($traffic);
            $hddspace = detectfilesize($hddspace);

            // last uploaded file
            $filedata =
                mysqli_fetch_array(
                    safe_query(
                        "SELECT
                            *
                        FROM
                            `" . PREFIX . "files`
                        WHERE
                            `accesslevel` <= " . $accesslevel . "
                        ORDER BY
                            date DESC
                        LIMIT 0,1"
                    )
                );
            $filename = $filedata[ 'filename' ];
            if (mb_strlen($filename) > 12) {
                $filename = mb_substr($filename, 0, 12);
                $filename .= '...';
            }
            $lastfile = '<a href="index.php?site=files&amp;file=' . $filedata[ 'fileID' ] . '" title="' .
                $filedata[ 'filename' ] . ' - ' .
                str_replace('%d', $filedata[ 'rating' ], $_language->module[ 'rating_x_of_10' ]) . '">' . $filename .
                '</a>';
        } else {
            $traffic = 'n/a';
            $hddspace = 'n/a';
            $lastfile = 'n/a';
        }

        // TOP 5 FILES
        $top5qry = safe_query(
            "SELECT
                *
            FROM
                `" . PREFIX . "files`
            WHERE
                `accesslevel` <= " . $accesslevel . "
            ORDER BY
                downloads DESC
            LIMIT 0,5"
        );
        $top5 = '<strong>' . $_language->module[ 'top_5_downloads' ] . '</strong><ul class="list-group">';

        $n = 1;
        while ($file = mysqli_fetch_array($top5qry)) {
            $n % 2 ? $bg = BG_1 : $bg = BG_2;
            $filename = $file[ 'filename' ];
            if (mb_strlen($filename) > 12) {
                $filename = mb_substr($filename, 0, 12);
                $filename .= '...';
            }
            $filename =
                '<a href="index.php?site=files&amp;file=' . $file[ 'fileID' ] . '"><strong>' .
                $filename . '</strong></a>';
            if ($file[ 'downloads' ] != '0') {
                $top5 .=
                    '<li class="list-group-item">
                        <span class="badge">' . $file[ 'downloads' ] . '</span> ' .
                            $n . ' ' . $filename .
                    '</li>';
            }
            $n++;
        }
        $top5 .= '</ul>';

        eval("\$files_stats = \"" . gettemplate("files_stats") . "\";");
        eval("\$files_overview = \"" . gettemplate("files_overview_head") . "\";");
        echo $files_overview;

        unset($traffic);
        unset($size);

        // FILE-CATEGORIES
        if ($totalcats) {
            while ($cat = mysqli_fetch_array($catQry)) {
                // cat-information
                $catID = $cat[ 'filecatID' ];
                $sub_cat_qry = get_all_sub_cats($catID, 1);
                $catname = '<a href="index.php?site=files&amp;cat=' . $catID . '"><strong>' .
                    $cat[ 'name' ] . '</strong></a>';
                $subcategories =
                    mysqli_num_rows(
                        safe_query(
                            "SELECT
                                `filecatID`
                            FROM
                                `" . PREFIX . "files_categorys`
                            WHERE
                                " . $sub_cat_qry
                        )
                    ) - 1;

                // get all files associated to the catID
                $catFileQry =
                    safe_query(
                        "SELECT
                            *
                        FROM
                            `" . PREFIX . "files`
                        WHERE
                            " . $sub_cat_qry . " AND
                            `accesslevel` <= " . (int)$accesslevel . "
                        ORDER BY
                            `fileID` DESC"
                    );
                $catFileTotal = mysqli_num_rows($catFileQry);
                if ($catFileTotal || $subcategories) {
                    $i++;
                    $traffic = 0;
                    $downloads = 0;
                    $size = 0;
                    while ($file = mysqli_fetch_array($catFileQry)) {
                        $filename = $file[ 'filename' ];
                        $filesize = $file[ 'filesize' ];
                        $fileload = $file[ 'downloads' ];
                        $traffic += $filesize * $fileload;
                        $downloads += $fileload;
                        $size += $file[ 'filesize' ];
                    }
                    $size = detectfilesize($size);
                    $traffic = detectfilesize($traffic);

                    // last uploaded file in category
                    $filedata =
                        mysqli_fetch_array(
                            safe_query(
                                "SELECT
                                    *
                                FROM
                                    `" . PREFIX . "files`
                                WHERE
                                    " . $sub_cat_qry . "
                                ORDER BY
                                    date DESC
                                LIMIT 0,1"
                            )
                        );
                    $filename = $filedata[ 'filename' ];
                    if (mb_strlen($filename) > 12) {
                        $filename = mb_substr($filename, 0, 12);
                        $filename .= '...';
                    }
                    $lastfile_cat = '<a href="index.php?site=files&amp;file=' . $filedata[ 'fileID' ] . '" title="' .
                        $filedata[ 'filename' ] . ' - ' .
                        sprintf($_language->module[ 'rating_x_of_10' ], $filedata[ 'rating' ]) . '">' . $filename .
                        '</a>';

                    // output
                    eval("\$files_category = \"" . gettemplate("files_category") . "\";");
                    echo $files_category;

                    unset($traffic);
                    unset($downloads);
                }
            }
        }
        eval ("\$files_overview = \"" . gettemplate("files_overview_foot") . "\";");
        echo $files_overview;
    } else {
        echo $_language->module[ 'no_categories_and_files' ];
    }
}
