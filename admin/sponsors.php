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

$_language->readModule('sponsors');

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/sponsors/";

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    echo '<h1>&curren; <a href="admincenter.php?site=sponsors" class="white">' . $_language->module[ 'sponsors' ] .
    '</a> &raquo; ' . $_language->module[ 'add_sponsor' ] . '</h1>';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true);

    eval ("\$addbbcode = \"" . gettemplate("addbbcode", "html", "admin") . "\";");
    eval ("\$addflags = \"" . gettemplate("flags_admin", "html", "admin") . "\";");

    echo '<script>
    <!--
    function chkFormular() {
        if(!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
           return false;
       }
   }
-->
</script>';

    echo '<form method="post" id="post" name="post" action="admincenter.php?site=sponsors" enctype="multipart/form-data"
    onsubmit="return chkFormular();">
<table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>' . $_language->module[ 'banner_upload' ] . '</b></td>
      <td width="85%"><input name="banner" type="file" size="40" /></td>
  </tr>
  <tr>
      <td width="15%"><b>' . $_language->module[ 'banner_upload_small' ] . '</b></td>
      <td width="85%"><input name="banner_small" type="file" size="40" /> <small>(' .
        $_language->module[ 'banner_upload_info' ] . ')</small></td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'sponsor_name' ] . '</b></td>
  <td><input type="text" name="name" size="60" maxlength="255" /></td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'sponsor_url' ] . '</b></td>
  <td><input type="text" name="url" size="60" maxlength="255" /></td>
</tr>
<tr>
  <td colspan="2">
    <b>' . $_language->module[ 'description' ] . '</b>
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top">' . $addbbcode . '</td>
          <td valign="top">' . $addflags . '</td>
      </tr>
  </table>
  <br /><textarea id="message" rows="5" cols="" name="message" style="width: 100%;"></textarea>
</td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'is_displayed' ] . '</b></td>
  <td><input type="checkbox" name="displayed" value="1" checked="checked" /></td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'mainsponsor' ] . '</b></td>
  <td><input type="checkbox" name="mainsponsor" value="1" /></td>
</tr>
<tr>
  <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
  <td><input type="submit" name="save" value="' . $_language->module[ 'add_sponsor' ] . '" /></td>
</tr>
</table>
</form>';
} elseif ($action == "edit") {
    echo '<h1>&curren; <a href="admincenter.php?site=sponsors" class="white">' . $_language->module[ 'sponsors' ] .
    '</a> &raquo; ' . $_language->module[ 'edit_sponsor' ] . '</h1>';

    $ds =
    mysqli_fetch_array(
        safe_query(
            "SELECT * FROM " . PREFIX . "sponsors WHERE sponsorID='" . $_GET[ "sponsorID" ] ."'"
        )
    );
    if (!empty($ds[ 'banner' ])) {
        $pic = '<img src="' . $filepath . $ds[ 'banner' ] . '" alt="">';
    } else {
        $pic = $_language->module[ 'no_upload' ];
    }
    if (!empty($ds[ 'banner_small' ])) {
        $pic_small = '<img src="' . $filepath . $ds[ 'banner_small' ] . '" alt="">';
    } else {
        $pic_small = $_language->module[ 'no_upload' ];
    }

    if ($ds[ 'displayed' ] == 1) {
        $displayed = '<input type="checkbox" name="displayed" value="1" checked="checked" />';
    } else {
        $displayed = '<input type="checkbox" name="displayed" value="1" />';
    }

    if ($ds[ 'mainsponsor' ] == 1) {
        $mainsponsor = '<input type="checkbox" name="mainsponsor" value="1" checked="checked" />';
    } else {
        $mainsponsor = '<input type="checkbox" name="mainsponsor" value="1" />';
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true);

    eval ("\$addbbcode = \"" . gettemplate("addbbcode", "html", "admin") . "\";");
    eval ("\$addflags = \"" . gettemplate("flags_admin", "html", "admin") . "\";");

    echo '<script>
    <!--
    function chkFormular() {
        if(!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
           return false;
       }
   }
-->
</script>';

    echo '<form method="post" id="post" name="post" action="admincenter.php?site=sponsors"
    enctype="multipart/form-data" onsubmit="return chkFormular();">
<input type="hidden" name="sponsorID" value="' . $ds[ 'sponsorID' ] . '" />
<table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%" valign="top"><b>' . $_language->module[ 'current_banner' ] . '</b></td>
      <td width="85%">' . $pic . '</td>
  </tr>
  <tr>
      <td valign="top"><b>' . $_language->module[ 'current_banner_small' ] . '</b></td>
      <td>' . $pic_small . '</td>
  </tr>
  <tr>
      <td><b>' . $_language->module[ 'banner_upload' ] . '</b></td>
      <td><input name="banner" type="file" size="40" /></td>
  </tr>
  <tr>
      <td><b>' . $_language->module[ 'banner_upload_small' ] . '</b></td>
      <td><input name="banner_small" type="file" size="40" /> <small>(' . $_language->module[ 'banner_upload_info' ] .
        ')</small></td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'sponsor_name' ] . '</b></td>
  <td><input type="text" name="name" size="60" maxlength="255" value="' . getinput($ds[ 'name' ]) . '" /></td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'sponsor_url' ] . '</b></td>
  <td><input type="text" name="url" size="60" value="' . getinput($ds[ 'url' ]) . '" /></td>
</tr>
<tr>
  <td colspan="2">
    <b>' . $_language->module[ 'description' ] . '</b>
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top">' . $addbbcode . '</td>
          <td valign="top">' . $addflags . '</td>
      </tr>
  </table>
  <br /><textarea id="message" rows="5" cols="" name="message" style="width: 100%;">' . getinput($ds[ 'info' ]) .
        '</textarea>
</td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'is_displayed' ] . '</b></td>
  <td>' . $displayed . '</td>
</tr>
<tr>
  <td><b>' . $_language->module[ 'mainsponsor' ] . '</b></td>
  <td>' . $mainsponsor . '</td>
</tr>
<tr>
  <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
  <td><input type="submit" name="saveedit" value="' . $_language->module[ 'edit_sponsor' ] . '" /></td>
</tr>
</table>
</form>';
} elseif (isset($_POST[ 'sortieren' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $sort = $_POST[ 'sort' ];
        if (is_array($sort)) {
            foreach ($sort as $sortstring) {
                $sorter = explode("-", $sortstring);
                safe_query("UPDATE " . PREFIX . "sponsors SET sort='$sorter[1]' WHERE sponsorID='$sorter[0]' ");
                redirect("admincenter.php?site=sponsors", "", 0);
            }
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "save" ])) {
    $name = $_POST[ "name" ];
    $url = $_POST[ "url" ];
    $info = $_POST[ "message" ];
    if (isset($_POST[ "displayed" ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    if (!$displayed) {
        $displayed = 0;
    }
    if (isset($_POST[ "mainsponsor" ])) {
        $mainsponsor = 1;
    } else {
        $mainsponsor = 0;
    }

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "INSERT INTO " . PREFIX .
            "sponsors (sponsorID, name, url, info, displayed, mainsponsor, date, sort) values('', '" . $name . "', '" .
            $url . "', '" . $info . "', '" . $displayed . "', '" . $mainsponsor . "', '" . time() . "', '1')"
        );

        $id = mysqli_insert_id($_database);

        $errors = array();

        $upload = new \webspell\Upload('banner');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
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

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner='" . $file . "' WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        $upload = new \webspell\Upload('banner_small');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
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
                        $file = $id.'_small'.$endung;

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner_small='" . $file . "'
                                WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        }

        redirect("admincenter.php?site=sponsors", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "saveedit" ])) {
    $name = $_POST[ "name" ];
    $url = $_POST[ "url" ];
    $info = $_POST[ "message" ];
    if (isset($_POST[ "displayed" ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    if (isset($_POST[ "mainsponsor" ])) {
        $mainsponsor = 1;
    } else {
        $mainsponsor = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (stristr($url, 'http://')) {
            $url = $url;
        } else {
            $url = 'http://' . $url;
        }

        safe_query(
            "UPDATE " . PREFIX . "sponsors SET name='" . $name . "', url='" . $url . "', info='" . $info .
            "', displayed='" . $displayed . "', mainsponsor='" . $mainsponsor . "' WHERE sponsorID='" .
            $_POST[ "sponsorID" ] . "'"
        );

        $id = $_POST[ 'sponsorID' ];

        $errors = array();

        $upload = new \webspell\Upload('banner');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
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

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner='" . $file . "' WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        $upload = new \webspell\Upload('banner_small');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');

                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());

                    if (is_array($imageInformation)) {
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
                        $file = $id.'_small'.$endung;

                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "sponsors SET banner_small='" . $file . "' ".
                                "WHERE sponsorID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }

        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        }

        redirect("admincenter.php?site=sponsors", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $get = safe_query("SELECT * FROM " . PREFIX . "sponsors WHERE sponsorID='" . $_GET[ "sponsorID" ] . "'");
        $data = mysqli_fetch_assoc($get);

        if (safe_query("DELETE FROM " . PREFIX . "sponsors WHERE sponsorID='" . $_GET[ "sponsorID" ] . "'")) {
            @unlink($filepath.$data['banner']);
            @unlink($filepath.$data['banner_small']);
            redirect("admincenter.php?site=sponsors", "", 0);
        } else {
            redirect("admincenter.php?site=sponsors", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    echo '<h1>&curren; ' . $_language->module[ 'sponsors' ] . '</h1>';

    echo
    '<a href="admincenter.php?site=sponsors&amp;action=add" class="input">' .
    $_language->module[ 'new_sponsor' ] . '</a><br /><br />';

    echo '<form method="post" action="admincenter.php?site=sponsors">
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
        <tr>
          <td width="29%" class="title"><b>' . $_language->module[ 'sponsor' ] . '</b></td>
          <td width="15%" class="title"><b>' . $_language->module[ 'clicks' ] . '</b></td>
          <td width="15%" class="title"><b>' . $_language->module[ 'is_displayed' ] . '</b></td>
          <td width="13%" class="title"><b>' . $_language->module[ 'mainsponsor' ] . '</b></td>
          <td width="20%" class="title"><b>' . $_language->module[ 'actions' ] . '</b></td>
          <td width="8%" class="title"><b>' . $_language->module[ 'sort' ] . '</b></td>
      </tr>';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $qry = safe_query("SELECT * FROM " . PREFIX . "sponsors ORDER BY sort");
    $anz = mysqli_num_rows($qry);
    if ($anz) {
        $i = 1;
        while ($ds = mysqli_fetch_array($qry)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }

            $ds[ 'displayed' ] == 1 ?
            $displayed = '<font color="green"><b>' . $_language->module[ 'yes' ] . '</b></font>' :
            $displayed = '<font color="red"><b>' . $_language->module[ 'no' ] . '</b></font>';
            $ds[ 'mainsponsor' ] == 1 ?
            $mainsponsor = '<font color="green"><b>' . $_language->module[ 'yes' ] . '</b></font>' :
            $mainsponsor = '<font color="red"><b>' . $_language->module[ 'no' ] . '</b></font>';

            if (stristr($ds[ 'url' ], 'http://')) {
                $name = '<a href="' . getinput($ds[ 'url' ]) . '" target="_blank">' . getinput($ds[ 'name' ]) . '</a>';
            } else {
                $name = '<a href="http://' . getinput($ds[ 'url' ]) . '" target="_blank">' . getinput($ds[ 'name' ]) .
                '</a>';
            }

            $days = round((time() - $ds[ 'date' ]) / (60 * 60 * 24));
            if ($days) {
                $perday = round($ds[ 'hits' ] / $days, 2);
            } else {
                $perday = $ds[ 'hits' ];
            }

            echo '<tr>
            <td class="' . $td . '">' . $name . '</td>
            <td class="' . $td . '">' . $ds[ 'hits' ] . ' (' . $perday . ')</td>
            <td class="' . $td . '" align="center">' . $displayed . '</td>
            <td class="' . $td . '" align="center">' . $mainsponsor . '</td>
            <td class="' . $td . '" align="center"><a href="admincenter.php?site=sponsors&amp;action=edit&amp;sponsorID=' .
                $ds[ 'sponsorID' ] . '" class="input">' . $_language->module[ 'edit' ] . '</a>
                <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
                    '\', \'admincenter.php?site=sponsors&amp;delete=true&amp;sponsorID=' . $ds[ 'sponsorID' ] .
                    '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module[ 'delete' ] . '" /></td>
<td class="' . $td . '" align="center"><select name="sort[]">';

    for ($j = 1; $j <= $anz; $j++) {
        if ($ds[ 'sort' ] == $j) {
            echo '<option value="' . $ds[ 'sponsorID' ] . '-' . $j . '" selected="selected">' . $j . '</option>';
        } else {
            echo '<option value="' . $ds[ 'sponsorID' ] . '-' . $j . '">' . $j . '</option>';
        }
    }
    echo '</select>
</td>
</tr>';

    $i++;
    }
} else {
    echo '<tr><td class="td1" colspan="6">' . $_language->module[ 'no_entries' ] . '</td></tr>';
}

echo '<tr>
<td class="td_head" colspan="6" align="right"><input type="hidden" name="captcha_hash" value="' . $hash .
    '"><input type="submit" name="sortieren" value="' . $_language->module[ 'to_sort' ] . '" /></td>
</tr>
</table>
</form>';
}
