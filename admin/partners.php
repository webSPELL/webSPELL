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

$_language->readModule('partners');

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $partnerID = (int)$_GET[ 'partnerID' ];
        safe_query("DELETE FROM " . PREFIX . "partners WHERE partnerID='" . $partnerID . "' ");
        $filepath = "../images/partners/";
        if (file_exists($filepath . $partnerID . '.gif')) {
            unlink($filepath . $partnerID . '.gif');
        }
        if (file_exists($filepath . $partnerID . '.jpg')) {
            unlink($filepath . $partnerID . '.jpg');
        }
        if (file_exists($filepath . $partnerID . '.png')) {
            unlink($filepath . $partnerID . '.png');
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'sortieren' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $sort = $_POST[ 'sort' ];
        foreach ($sort as $sortstring) {
            $sorter = explode("-", $sortstring);
            safe_query("UPDATE " . PREFIX . "partners SET sort='".$sorter[1]."' WHERE partnerID='".$sorter[0]."' ");
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $name = $_POST[ 'name' ];
        $url = $_POST[ 'url' ];
        if (isset($_POST[ "displayed" ])) {
            $displayed = 1;
        } else {
            $displayed = 0;
        }

        safe_query(
            "INSERT INTO
                `" . PREFIX . "partners` (
                    `name`,
                    `url`,
                    `displayed`,
                    `date`,
                    `sort`
                )
                VALUES (
                    '$name',
                    '$url',
                    '" . $displayed . "',
                    '" . time() . "',
                    '1'
                )"
        );
        $id = mysqli_insert_id($_database);

        $filepath = "../images/partners/";

        //TODO: should be loaded from root language folder
        $_language->readModule('formvalidation', true);

        $upload = new \webspell\HttpUpload('banner');

        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        if ($imageInformation[0] < 89 && $imageInformation[1] < 32) {
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

                            if (file_exists($filepath . $id . '.gif')) {
                                unlink($filepath . $id . '.gif');
                            }
                            if (file_exists($filepath . $id . '.jpg')) {
                                unlink($filepath . $id . '.jpg');
                            }
                            if (file_exists($filepath . $id . '.png')) {
                                unlink($filepath . $id . '.png');
                            }

                            if ($upload->saveAs($filepath.$file)) {
                                @chmod($filepath.$file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "partners
                                    SET banner='" . $file . "' WHERE partnerID='" . $id . "'"
                                );
                            }
                        } else {
                            echo generateErrorBox(sprintf($_language->module[ 'image_too_big' ], 88, 31));
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
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $name = $_POST[ 'name' ];
        $url = $_POST[ 'url' ];
        if (isset($_POST[ "displayed" ])) {
            $displayed = 1;
        } else {
            $displayed = 0;
        }

        $partnerID = (int)$_POST[ 'partnerID' ];
        $id = $partnerID;

        safe_query(
            "UPDATE
                `" . PREFIX . "partners`
            SET
                `name` = '" . $name . "',
                `url` = '" . $url . "',
                `displayed` = '" . $displayed . "'
            WHERE
                `partnerID` = '" . $partnerID . "'"
        );

        $filepath = "../images/partners/";

        //TODO: should be loaded from root language folder
        $_language->readModule('formvalidation', true);

        $upload = new \webspell\HttpUpload('banner');

        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
                        if ($imageInformation[0] < 89 && $imageInformation[1] < 32) {
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

                            if (file_exists($filepath . $id . '.gif')) {
                                unlink($filepath . $id . '.gif');
                            }
                            if (file_exists($filepath . $id . '.jpg')) {
                                unlink($filepath . $id . '.jpg');
                            }
                            if (file_exists($filepath . $id . '.png')) {
                                unlink($filepath . $id . '.png');
                            }

                            if ($upload->saveAs($filepath.$file)) {
                                @chmod($filepath.$file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "partners
                                    SET banner='" . $file . "' WHERE partnerID='" . $id . "'"
                                );
                            }
                        } else {
                            echo generateErrorBox(sprintf($_language->module[ 'image_too_big' ], 88, 31));
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
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; <a href="admincenter.php?site=partners" class="white">' . $_language->module[ 'partners' ] .
        '</a> &raquo; ' . $_language->module[ 'add_partner' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=partners" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>' . $_language->module[ 'partner_name' ] . '</b></td>
      <td width="85%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'banner' ] . '</b></td>
      <td><input name="banner" type="file" size="40" /> <small>' . $_language->module[ 'max_88x31' ] . '</small></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'homepage_url' ] . '</b></td>
      <td><input type="text" name="url" size="60" value="http://" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'is_displayed' ] . '</b></td>
      <td><input type="checkbox" name="displayed" value="1" checked="checked" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
      <td><input type="submit" name="save" value="' . $_language->module[ 'add_partner' ] . '" /></td>
    </tr>
  </table>
  </form>';
} elseif ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; <a href="admincenter.php?site=partners" class="white">' . $_language->module[ 'partners' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_partner' ] . '</h1>';

    $partnerID = $_GET[ 'partnerID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "partners WHERE partnerID='$partnerID'");
    $ds = mysqli_fetch_array($ergebnis);

    if ($ds[ 'displayed' ] == '1') {
        $displayed = '<input type="checkbox" name="displayed" value="1" checked="checked" />';
    } else {
        $displayed = '<input type="checkbox" name="displayed" value="1" />';
    }

    echo '<form method="post" action="admincenter.php?site=partners" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>' . $_language->module[ 'current_banner' ] . '</b></td>
      <td width="85%"><img src="../images/partners/' . $ds[ 'banner' ] . '" alt=""></td>
    </tr>
    <tr>
      <td width="15%"><b>' . $_language->module[ 'partner_name' ] . '</b></td>
      <td width="85%"><input type="text" name="name" size="60" value="' . getinput($ds[ 'name' ]) . '" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'banner' ] . '</b></td>
      <td><input name="banner" type="file" size="40" /> <small>' . $_language->module[ 'max_88x31' ] . '</small></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'homepage_url' ] . '</b></td>
      <td><input type="text" name="url" size="60" value="' . getinput($ds[ 'url' ]) . '" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'is_displayed' ] . '</b></td>
      <td>' . $displayed . '</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="' . $hash .
        '" /><input type="hidden" name="partnerID" value="' . $partnerID . '" /></td>
      <td><input type="submit" name="saveedit" value="' . $_language->module[ 'edit_partner' ] . '" /></td>
    </tr>
  </table>
  </form>';
} else {
    echo '<h1>&curren; ' . $_language->module[ 'partners' ] . '</h1>';

    echo '<a href="admincenter.php?site=partners&amp;action=add" class="input">' .
        $_language->module[ 'new_partner' ] . '</a><br><br>';

    echo '<form method="post" action="admincenter.php?site=partners">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="42%" class="title"><b>' . $_language->module[ 'partners' ] . '</b></td>
      <td width="15%" class="title"><b>' . $_language->module[ 'clicks' ] . '</b></td>
      <td width="15%" class="title"><b>' . $_language->module[ 'is_displayed' ] . '</b></td>
      <td width="20%" class="title"><b>' . $_language->module[ 'actions' ] . '</b></td>
      <td width="8%" class="title"><b>' . $_language->module[ 'sort' ] . '</b></td>
    </tr>';

    $partners = safe_query("SELECT * FROM " . PREFIX . "partners ORDER BY sort");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(partnerID) as cnt FROM " . PREFIX . "partners"));
    $anzpartners = $tmp[ 'cnt' ];
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $CAPCLASS->createTransaction();
    $hash_2 = $CAPCLASS->getHash();

    $i = 1;
    while ($db = mysqli_fetch_array($partners)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }

        $db[ 'displayed' ] == 1 ? $displayed = '<font color="green"><b>' . $_language->module[ 'yes' ] . '</b></font>' :
            $displayed = '<font color="red"><b>' . $_language->module[ 'no' ] . '</b></font>';

        $days = round((time() - $db[ 'date' ]) / (60 * 60 * 24));
        if ($days) {
            $perday = round($db[ 'hits' ] / $days, 2);
        } else {
            $perday = $db[ 'hits' ];
        }

        echo '<tr>
      <td class="' . $td . '"><a href="' . getinput($db[ 'url' ]) . '" target="_blank">' . getinput($db[ 'name' ]) .
            '</a></td>
      <td class="' . $td . '">' . $db[ 'hits' ] . ' (' . $perday . ')</td>
      <td class="' . $td . '" align="center">' . $displayed . '</td>
      <td class="' . $td . '" align="center"><a href="admincenter.php?site=partners&amp;action=edit&amp;partnerID=' .
            $db[ 'partnerID' ] . '" class="input">' . $_language->module[ 'edit' ] . '</a>
      <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
            '\', \'admincenter.php?site=partners&amp;delete=true&amp;partnerID=' . $db[ 'partnerID' ] .
            '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module[ 'delete' ] . '" /></td>
      <td class="' . $td . '" align="center">
      <select name="sort[]">';

        for ($j = 1; $j <= $anzpartners; $j++) {
            if ($db[ 'sort' ] == $j) {
                echo '<option value="' . $db[ 'partnerID' ] . '-' . $j . '" selected="selected">' . $j . '</option>';
            } else {
                echo '<option value="' . $db[ 'partnerID' ] . '-' . $j . '">' . $j . '</option>';
            }
        }

        echo '</select>
      </td>
    </tr>';
        $i++;
    }
    echo '<tr class="td_head">
      <td colspan="5" align="right"><input type="hidden" name="captcha_hash" value="' . $hash_2 .
        '"><input type="submit" name="sortieren" value="' . $_language->module[ 'to_sort' ] . '" /></td>
    </tr>
  </table>
  </form>';
}
