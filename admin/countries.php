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

$_language->readModule('countries', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/flags/";

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; <a href="admincenter.php?site=icons" class="white">' . $_language->module[ 'icons' ] .
        '</a> &raquo; <a href="admincenter.php?site=countries" class="white">' . $_language->module[ 'countries' ] .
        '</a> &raquo; ' . $_language->module[ 'add_country' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=countries" enctype="multipart/form-data">
		  <table width="100%" border="0" cellspacing="1" cellpadding="3">
			<tr>
			  <td width="15%"><b>' . $_language->module[ 'icon_upload' ] . '</b></td>
			  <td width="85%"><input name="icon" type="file" size="40" /> <small>' . $_language->module[ 'max_18x12' ] .
        '</small></td>
			</tr>
			<tr>
			  <td><b>' . $_language->module[ 'country' ] . '</b></td>
			  <td><input type="text" name="country" size="60" maxlength="255" /></td>
			</tr>
			<tr>
			  <td><b>' . $_language->module[ 'shorthandle' ] . '</b></td>
			  <td><input type="text" name="shorthandle" size="5" maxlength="3" /></td>
			</tr>
			<tr>
			  <td><b>' . $_language->module[ 'favorite' ] . '</b></td>
			  <td><input type="checkbox" name="fav" value="1" /></td>
			</tr>
			<tr>
			  <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
			  <td><input type="submit" name="save" value="' . $_language->module[ 'add_country' ] . '" /></td>
			</tr>
		  </table>
		  </form>';
} elseif ($action == "edit") {
    $ds =
        mysqli_fetch_array(safe_query(
            "SELECT * FROM " . PREFIX . "countries WHERE countryID='" . $_GET[ "countryID" ] .
            "'"
        ));
    $pic = '<img src="../images/flags/' . $ds[ 'short' ] . '.gif" alt="' . $ds[ 'country' ] . '" />';
    if ($ds[ 'fav' ] == '1') {
        $fav = '<input type="checkbox" name="fav" value="1" checked="checked" />';
    } else {
        $fav = '<input type="checkbox" name="fav" value="1" />';
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; <a href="admincenter.php?site=icons" class="white">' . $_language->module[ 'icons' ] .
        '</a> &raquo; <a href="admincenter.php?site=countries" class="white">' . $_language->module[ 'countries' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_country' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=countries" enctype="multipart/form-data">
        <input type="hidden" name="countryID" value="' . $ds[ 'countryID' ] . '" />
        <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
            <td width="15%"><b>' . $_language->module[ 'present_icon' ] . '</b></td>
            <td width="85%">' . $pic . '</td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'icon_upload' ] . '</b></td>
            <td>
                <input name="icon" type="file" size="40" /> <small>' . $_language->module[ 'max_18x12' ] . '</small>
            </td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'country' ] . '</b></td>
            <td>
                <input type="text" name="country" size="60" maxlength="255" value="' .
                    getinput($ds[ 'country' ]) . '" />
            </td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'shorthandle' ] . '</b></td>
            <td>
                <input type="text" name="shorthandle" size="5" maxlength="3" value="' .
                    getinput($ds[ 'short' ]) . '" />
            </td>
        </tr>
        <tr>
          <td><b>' . $_language->module[ 'favorite' ] . '</b></td>
          <td>' . $fav . '</td>
        </tr>
        <tr>
          <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
          <td><input type="submit" name="saveedit" value="' . $_language->module[ 'edit_country' ] . '" /></td>
        </tr>
        </table>
    </form>';
} elseif (isset($_POST[ 'save' ])) {
    $icon = $_FILES[ "icon" ];
    $country = $_POST[ "country" ];
    $short = $_POST[ "shorthandle" ];
    if (isset($POST[ "fav" ])) {
        $fav = 1;
    } else {
        $fav = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('shorthandle','country'))) {
            $errors = array();

            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('icon');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/gif');

                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());

                        if (is_array($imageInformation)) {
                            safe_query(
                                "INSERT INTO
                                    `" . PREFIX . "countries` (
                                        `country`,
                                        `short`,
                                        `fav`
                                    ) VALUES (
                                        '" . $country . "',
                                        '" . $short . "',
                                        '" . $fav . "'
                                    )"
                            );

                            $file = $short . ".gif";

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                            }
                        } else {
                            $errors[] = $_language->module['broken_image'];
                        }
                    } else {
                        $errors[] = $_language->module['unsupported_image_type'];
                    }
                } else {
                    $errors[] = $upload->translateError();
                }
            }
            if (count($errors)) {
                $errors = array_unique($errors);
                echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
            }
        } else {
            echo $_language->module['information_incomplete'];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "saveedit" ])) {
    $icon = $_FILES[ "icon" ];
    $country = $_POST[ "country" ];
    $short = $_POST[ "shorthandle" ];
    if (isset($POST[ "fav" ])) {
        $fav = 1;
    } else {
        $fav = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('shorthandle','country'))) {
            safe_query(
                "UPDATE
                    `" . PREFIX . "countries`
                SET
                    `country` = '" . $country . "',
                    `short` = '" . $short . "',
                    `fav` = '" . $fav . "'
                WHERE `countryID` = '" . $_POST[ "countryID" ] . "'"
            );

            $errors = array();

            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('icon');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/gif');

                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());

                        if (is_array($imageInformation)) {
                            $file = $short . ".gif";

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                            }
                        } else {
                            $errors[] = $_language->module['broken_image'];
                        }
                    } else {
                        $errors[] = $_language->module['unsupported_image_type'];
                    }
                } else {
                    $errors[] = $upload->translateError();
                }
            }
            if (count($errors)) {
                $errors = array_unique($errors);
                echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
            }

        } else {
            echo $_language->module['information_incomplete'];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $countryID = (int) $_GET[ "countryID" ];
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT short FROM `" . PREFIX . "countries` WHERE `countryID` = '" . $countryID . "'"
            )
        );
        safe_query("DELETE FROM `" . PREFIX . "countries` WHERE `countryID` = '" . $countryID . "'");
        $file = $ds['short'].".gif";
        if (file_exists($filepath.$file)) {
            unlink($filepath.$file);
        }
        redirect("admincenter.php?site=countries", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    echo '<h1>&curren; <a href="admincenter.php?site=icons" class="white">' . $_language->module[ 'icons' ] .
        '</a> &raquo; ' . $_language->module[ 'countries' ] . '</h1>';

    echo '<a href="admincenter.php?site=countries&amp;action=add" class="input">' .
        $_language->module[ 'new_country' ] . '</a><br><br>';

    echo '<form method="post" action="admincenter.php?site=countries">
		  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
			<tr>
			  <td width="10%" class="title" align="center"><b>' . $_language->module[ 'icon' ] . '</b></td>
			  <td width="55%" class="title"><b>' . $_language->module[ 'country' ] . '</b></td>
			  <td width="10%" class="title" align="center"><b>' . $_language->module[ 'shorthandle' ] . '</b></td>
			  <td width="25%" class="title"><b>' . $_language->module[ 'actions' ] . '</b></td>
			</tr>';

    $ds = safe_query("SELECT * FROM `" . PREFIX . "countries` ORDER BY `country`");
    $anz = mysqli_num_rows($ds);
    if ($anz) {
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        $i = 1;
        while ($flags = mysqli_fetch_array($ds)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }
            $pic = '<img src="../images/flags/' . $flags[ 'short' ] . '.gif" alt="' . $flags[ 'country' ] . '">';
            if ($flags[ 'fav' ] == 1) {
                $fav = ' <small style="color:green"><b>(' . $_language->module[ 'favorite' ] . ')</b></small>';
            } else {
                $fav = '';
            }

            echo '<tr>
                <td class="' . $td . '" align="center">' . $pic . '</td>
                <td class="' . $td . '">' . getinput($flags[ 'country' ]) . $fav . '</td>
                <td class="' . $td . '" align="center">' . getinput($flags[ 'short' ]) . '</td>
                <td class="' . $td . '" align="center">
                    <a href="admincenter.php?site=countries&amp;action=edit&amp;countryID='. $flags[ 'countryID' ]
                    . '" class="input">' . $_language->module[ 'edit' ] . '</a>
                    <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
                        '\', \'admincenter.php?site=countries&amp;delete=true&amp;countryID=' . $flags[ 'countryID' ] .
                        '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module[ 'delete' ] . '" />
                </td>
            </tr>';

            $i++;
        }
    } else {
        echo '<tr>
            <td class="td1" colspan="5">' . $_language->module[ 'no_entries' ] . '</td>
        </tr>';
    }
    echo '</table>
      </form>';
}
