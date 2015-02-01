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

$_language->readModule('rubrics');

if (!isnewsadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query("INSERT INTO " . PREFIX . "news_rubrics ( rubric ) values( '" . $_POST[ 'name' ] . "' ) ");
            $id = mysqli_insert_id($_database);

            $filepath = "../images/news-rubrics/";

            $errors = array();

            $upload = new \webspell\Upload('pic');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/jpeg','image/png','image/gif');

                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());

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
                            $file = $tag . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "news_rubrics SET pic='" . $file . "' WHERE rubricID='" . $id . "'"
                                );
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
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query(
                "UPDATE
                    `" . PREFIX . "news_rubrics`
                SET
                    `rubric` = '" . $_POST[ 'name' ] . "'
                WHERE
                    `rubricID` = '" . $_POST[ 'rubricID' ] . "'"
            );

            $id = $_POST[ 'rubricID' ];
            $filepath = "../images/news-rubrics/";

            $errors = array();

            $upload = new \webspell\Upload('pic');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/jpeg','image/png','image/gif');

                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());

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
                            $file = $tag . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "news_rubrics SET pic='" . $file . "' WHERE rubricID='" . $id . "'"
                                );
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
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $rubricID = (int)$_GET[ 'rubricID' ];
        $filepath = "../images/news-rubrics/";
        safe_query("DELETE FROM " . PREFIX . "news_rubrics WHERE rubricID='$rubricID'");
        if (file_exists($filepath . $rubricID . '.gif')) {
            @unlink($filepath . $rubricID . '.gif');
        }
        if (file_exists($filepath . $rubricID . '.jpg')) {
            @unlink($filepath . $rubricID . '.jpg');
        }
        if (file_exists($filepath . $rubricID . '.png')) {
            @unlink($filepath . $rubricID . '.png');
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
    echo '<h1>&curren; <a href="admincenter.php?site=rubrics" class="white">' . $_language->module[ 'news_rubrics' ] .
        '</a> &raquo; ' . $_language->module[ 'add_rubric' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=rubrics" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>' . $_language->module[ 'rubric_name' ] . '</b></td>
      <td width="85%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'picture_upload' ] . '</b></td>
      <td><input name="pic" type="file" size="40" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
      <td><input type="submit" name="save" value="' . $_language->module[ 'add_rubric' ] . '" /></td>
    </tr>
  </table>
  </form>';
} elseif ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo '<h1>&curren; <a href="admincenter.php?site=rubrics" class="white">' . $_language->module[ 'news_rubrics' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_rubric' ] . '</h1>';

    $rubricID = (int)$_GET[ 'rubricID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "news_rubrics WHERE rubricID='$rubricID'");
    $ds = mysqli_fetch_array($ergebnis);

    echo '<form method="post" action="admincenter.php?site=rubrics" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>' . $_language->module[ 'rubric_name' ] . '</b></td>
      <td width="85%"><input type="text" name="name" size="60" value="' . getinput($ds[ 'rubric' ]) . '" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'picture' ] . '</b></td>
      <td><img src="../images/news-rubrics/' . $ds[ 'pic' ] . '" alt=""></td>
    </tr>
    <tr>
		   <td><b>' . $_language->module[ 'picture_upload' ] . '</b></td>
       <td><input name="pic" type="file" size="40" /></td>
     </tr>
     <tr>
      <td><input type="hidden" name="captcha_hash" value="' . $hash .
        '" /><input type="hidden" name="rubricID" value="' . $ds[ 'rubricID' ] . '" /></td>
      <td><input type="submit" name="saveedit" value="' . $_language->module[ 'edit_rubric' ] . '" /></td>
    </tr>
  </table>
  </form>';
} else {

    echo '<h1>&curren; ' . $_language->module[ 'news_rubrics' ] . '</h1>';

    echo '<a href="admincenter.php?site=rubrics&amp;action=add" class="input">' .
        $_language->module[ 'new_rubric' ] . '</a><br><br>';

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "news_rubrics ORDER BY rubric");

    echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="25%" class="title"><b>' . $_language->module[ 'rubric_name' ] . '</b></td>
      <td width="55%" class="title"><b>' . $_language->module[ 'picture' ] . '</b></td>
      <td width="20%" class="title"><b>' . $_language->module[ 'actions' ] . '</b></td>
   		</tr>';
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    $i = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }

        echo '<tr>
      <td class="' . $td . '">' . getinput($ds[ 'rubric' ]) . '</td>
      <td class="' . $td . '" align="center"><img src="../images/news-rubrics/' . $ds[ 'pic' ] . '" alt=""></td>
      <td class="' . $td . '" align="center"><a href="admincenter.php?site=rubrics&amp;action=edit&amp;rubricID=' .
            $ds[ 'rubricID' ] . '">' . $_language->module[ 'edit' ] . '</a>
      <input type="button" onclick="MM_confirm(\'' . $_language->module[ 'really_delete' ] .
            '\', \'admincenter.php?site=rubrics&amp;delete=true&amp;rubricID=' . $ds[ 'rubricID' ] .
            '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module[ 'delete' ] . '" /></td>
    </tr>';

        $i++;
    }
    echo '</table>';
}
