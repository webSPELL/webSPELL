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

$_language->readModule('games', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/games/";

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
        '</a> &raquo; <a href="admincenter.php?site=games" class="white">' . $_language->module[ 'games' ] .
        '</a> &raquo; ' . $_language->module[ 'add_game' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=games" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
            <td width="15%"><b>' . $_language->module[ 'game_icon' ] . '</b></td>
            <td width="85%"><input name="icon" type="file" size="40" /></td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'game_name' ] . '</b></td>
            <td><input type="text" name="name" size="60" maxlength="255" /></td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'game_tag' ] . '</b></td>
            <td><input type="text" name="tag" size="5" maxlength="3" /></td>
        </tr>
        <tr>
            <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
            <td><input type="submit" name="save" value="' . $_language->module[ 'add_game' ] . '" /></td>
        </tr>
    </table>
    </form>';
} elseif ($action == "edit") {
    $ds = mysqli_fetch_array(safe_query("SELECT * FROM " . PREFIX . "games WHERE gameID='" . $_GET[ "gameID" ] . "'"));
    $pic = '<img src="../images/games/' . $ds[ 'tag' ] . '.gif" alt="' . $ds[ 'name' ] . '">';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; <a href="admincenter.php?site=icons" class="white">' . $_language->module[ 'icons' ] .
        '</a> &raquo; <a href="admincenter.php?site=games" class="white">' . $_language->module[ 'games' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_game' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=games" enctype="multipart/form-data">
    <input type="hidden" name="gameID" value="' . $ds[ 'gameID' ] . '" />
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
            <td width="15%"><b>' . $_language->module[ 'present_icon' ] . '</b></td>
            <td width="85%">' . $pic . '</td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'game_icon' ] . '</b></td>
            <td><input name="icon" type="file" size="40" /></td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'game_name' ] . '</b></td>
            <td><input type="text" name="name" size="60" maxlength="255" value="' . getinput($ds[ 'name' ]) . '" /></td>
        </tr>
        <tr>
            <td><b>' . $_language->module[ 'game_tag' ] . '</b></td>
            <td><input type="text" name="tag" size="5" maxlength="3" value="' . getinput($ds[ 'tag' ]) . '" /></td>
        </tr>
        <tr>
            <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
            <td><input type="submit" name="saveedit" value="' . $_language->module[ 'edit_game' ] . '" /></td>
        </tr>
    </table>
    </form>';
} elseif (isset($_POST[ 'save' ])) {
    $icon = $_FILES[ "icon" ];
    $name = $_POST[ "name" ];
    $tag = $_POST[ "tag" ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name','tag'))) {
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
                                "INSERT INTO " . PREFIX . "games (
                                    name,
                                    tag
                                ) VALUES (
                                    '" . $name . "',
                                    '" . $tag ."'
                                )"
                            );

                            $file = $tag . ".gif";

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
            echo $_language->module[ 'fill_correctly' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "saveedit" ])) {
    $icon = $_FILES[ "icon" ];
    $name = $_POST[ "name" ];
    $tag = $_POST[ "tag" ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name','tag'))) {
            safe_query(
                "UPDATE
                    " . PREFIX . "games
                SET
                    name='" . $name . "',
                    tag='" . $tag ."'
                WHERE gameID='" . $_POST[ "gameID" ] . "'"
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
                            $file = $tag . ".gif";

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
            echo $_language->module[ 'fill_correctly' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha();
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $ds = mysqli_fetch_array(
            safe_query(
                "SELECT tag FROM " . PREFIX . "games WHERE gameID='" . $_GET[ "gameID" ] . "'"
            )
        );
        safe_query("DELETE FROM " . PREFIX . "games WHERE gameID='" . $_GET[ "gameID" ] . "'");
        if (file_exists($filepath.$ds['tag'].".gif")) {
            unlink($filepath.$ds['tag'].".gif");
        }
        redirect("admincenter.php?site=games", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    echo '<h1>&curren; <a href="admincenter.php?site=icons" class="white">' . $_language->module[ 'icons' ] .
        '</a> &raquo; ' . $_language->module[ 'games' ] . '</h1>';

    echo
        '<a href="admincenter.php?site=games&amp;action=add" class="input">' .
        $_language->module[ 'new_game' ] . '</a><br /><br />';

    echo '<form method="post" action="admincenter.php?site=games">
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
        <tr>
            <td width="15" class="title"><b>' . $_language->module[ 'icons' ] . '</b></td>
            <td width="45%" class="title"><b>' . $_language->module[ 'game_name' ] . '</b></td>
            <td width="15%" class="title"><b>' . $_language->module[ 'game_tag' ] . '</b></td>
            <td width="25%" class="title"><b>' . $_language->module[ 'actions' ] . '</b></td>
        </tr>';

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "games ORDER BY name");
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        $i = 1;
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        while ($ds = mysqli_fetch_array($ergebnis)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }
            $pic = '<img src="../images/games/' . $ds[ 'tag' ] . '.gif" alt="">';

            echo '<tr>
                <td class="' . $td . '" align="center">' . $pic . '</td>
                <td class="' . $td . '">' . getinput($ds[ 'name' ]) . '</td>
                <td class="' . $td . '" align="center">' . getinput($ds[ 'tag' ]) . '</td>
                <td class="' . $td . '" align="center">
                    <a href=admincenter.php?site=games&amp;action=edit&amp;gameID=' . $ds[ 'gameID' ] .
                '" class="input">' . $_language->module[ 'edit' ] . '</a>
                <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
                '\', \'admincenter.php?site=games&amp;delete=true&amp;gameID=' . $ds[ 'gameID' ] .
                '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module[ 'delete' ] . '" /></td>
            </tr>';

            $i++;
        }
    } else {
        echo '<tr><td class="td1" colspan="5">' . $_language->module[ 'no_entries' ] . '</td></tr>';
    }

    echo '</table>
    </form>';
}
