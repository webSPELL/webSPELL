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

$_language->readModule('ranks');
$_language->readModule('rank_special', true);

if (!isforumadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $rankID = (int)$_GET[ 'rankID' ];
        safe_query("UPDATE " . PREFIX . "users SET special_rank='0' WHERE special_rank='".$rankID."'");
        safe_query(" DELETE FROM " . PREFIX . "forum_ranks WHERE rankID='" . $rankID . "' ");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'save' ])) {
    $name = $_POST[ 'name' ];
    $rank = $_FILES[ 'rank' ];
    $max = $_POST[ 'max' ];
    $min = $_POST[ 'min' ];

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('min', 'max')) || isset($_POST['special'])) {
            if ($max == "MAX") {
                $maximum = 2147483647;
            } else {
                $maximum = $max;
            }

            safe_query(
                "INSERT INTO
                    `" . PREFIX . "forum_ranks` (
                        `rank`,
                        `postmin`,
                        `postmax`,
                        `special`
                    )
                    VALUES (
                        '$name',
                        '$min',
                        '$maximum',
                        '".isset($_POST['special'])."'
                    )"
            );
            $id = mysqli_insert_id($_database);

            $filepath = "../images/icons/ranks/";
            if ($rank[ 'name' ] != "") {
                move_uploaded_file($rank[ 'tmp_name' ], $filepath . $rank[ 'name' ]);
                @chmod($filepath . $rank[ 'name' ], 0755);
                $file_ext = strtolower(mb_substr($rank[ 'name' ], strrpos($rank[ 'name' ], ".")));
                $file = $id . $file_ext;
                rename($filepath . $rank[ 'name' ], $filepath . $file);
                safe_query("UPDATE " . PREFIX . "forum_ranks SET pic='$file' WHERE rankID='$id' ");
            }
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $rank = $_POST[ 'rank' ];
    $min = $_POST[ 'min' ];
    $max = $_POST[ 'max' ];

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('min', 'max'))) {
            $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_ranks ORDER BY rankID");
            $anz = mysqli_num_rows($ergebnis);
            if ($anz) {
                while ($ds = mysqli_fetch_array($ergebnis)) {
                    if ($ds[ 'rank' ] != "Administrator" && $ds[ 'rank' ] != "Moderator") {
                        $id = $ds[ 'rankID' ];
                        if ($ds[ 'special' ] != 1) {
                            if ($max[ $id ] == "MAX") {
                                $maximum = 2147483647;
                            } else {
                                $maximum = $max[ $id ];
                            }
                            safe_query("UPDATE " . PREFIX . "forum_ranks SET postmin='$min[$id]' WHERE rankID='$id'");
                            safe_query("UPDATE " . PREFIX . "forum_ranks SET postmax='$maximum' WHERE rankID='$id'");
                        } else {
                            safe_query("UPDATE " . PREFIX . "forum_ranks SET rank='$rank[$id]' WHERE rankID='$id'");
                        }
                    }
                }
            }
        } else {
            echo $_language->module[ 'information_incomplete' ];
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

    echo '<h1>&curren; <a href="admincenter.php?site=ranks" class="white">' . $_language->module[ 'user_ranks' ] .
        '</a> &raquo; ' . $_language->module[ 'add_rank' ] . '</h1>';

    echo '<script type="text/javascript">
  function HideFields(state){
  	if(state == true){
  		document.getElementById(\'max\').style.display = "none";
  		document.getElementById(\'min\').style.display = "none";
  	}
  	else{
  		document.getElementById(\'max\').style.display = "";
  		document.getElementById(\'min\').style.display = "";
  	}
  }
  </script>
  <form method="post" action="admincenter.php?site=ranks" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>' . $_language->module[ 'rank_icon' ] . '</b></td>
      <td width="85%"><input name="rank" type="file" size="40" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'rank_name' ] . '</b></td>
      <td><input type="text" name="name" size="60" /></td>
    </tr>
    <tr id="min">
      <td><b>' . $_language->module[ 'min_posts' ] . '</b></td>
      <td><input type="text" name="min" size="4" /></td>
    </tr>
    <tr id="max">
      <td><b>' . $_language->module[ 'max_posts' ] . '</b></td>
      <td><input type="text" name="max" size="4" /></td>
    </tr>
    <tr>
      <td><b>' . $_language->module[ 'special_rank' ] . '</b></td>
      <td><input type="checkbox" name="special" onchange="javascript:HideFields(this.checked);" value="1" /></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
      <td><input type="submit" name="save" value="' . $_language->module[ 'add_rank' ] . '" /></td>
    </tr>
  </table>
  </form>';
} else {
    echo '<h1>&curren; ' . $_language->module[ 'user_ranks' ] . '</h1>';

    echo
        '<a href="admincenter.php?site=ranks&amp;action=add" class="input">' .
        $_language->module[ 'new_rank' ] . '</a><br><br>';

    echo '<form method="post" action="admincenter.php?site=ranks">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="20%" class="title"><b>' . $_language->module[ 'rank_icon' ] . '</b></td>
      <td width="48%" class="title"><b>' . $_language->module[ 'rank_name' ] . '</b></td>
      <td width="10%" class="title"><b>' . $_language->module[ 'special_rank' ] . '</b></td>
      <td width="6%" class="title"><b>' . $_language->module[ 'min_posts' ] . '</b></td>
      <td width="6%" class="title"><b>' . $_language->module[ 'max_posts' ] . '</b></td>
      <td width="10%" class="title"><b>' . $_language->module[ 'actions' ] . '</b></td>
    </tr>';

    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_ranks ORDER BY postmax");
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
        if ($ds[ 'rank' ] == "Administrator" || $ds[ 'rank' ] == "Moderator") {
            echo '<tr>
	        <td class="' . $td . '" align="center"><img src="../images/icons/ranks/' . $ds[ 'pic' ] . '" alt=""></td>
	        <td class="' . $td . '">' . $ds[ 'rank' ] . '</td>
	        <td class="' . $td . '" align="center">x</td>
	        <td class="' . $td . '">&nbsp;</td>
	        <td class="' . $td . '">&nbsp;</td>
	        <td class="' . $td . '">&nbsp;</td>
	      </tr>';
        } else {
            if (mb_strlen(trim($ds[ 'postmax' ])) > 8) {
                $max = "MAX";
            } else {
                $max = $ds[ 'postmax' ];
            }

            $user_list = "";
            $min = '<input type="text" name="min['.$ds['rankID'].']" value="'.$ds['postmin'].'" size="6" dir="rtl" />';
            $max = '<input type="text" name="max['.$ds['rankID'].']" value="'.$max.'" size="6" dir="rtl" />';

            if ($ds['special']==1) {
                $get = safe_query("SELECT nickname FROM ".PREFIX."user WHERE special_rank = '".$ds['rankID']."'");
                $user_list = array();
                while ($user = mysqli_fetch_assoc($get)) {
                    $user_list[] = $user['nickname'];
                }
                $user_list = "<br/><small>".$_language->module['used_for'].": ".implode(", ", $user_list)."</small>";
                $min = "";
                $max = "";
            }

            echo '<tr>
	        <td class="' . $td . '" align="center"><img src="../images/icons/ranks/' . $ds[ 'pic' ] . '" alt=""></td>
	        <td class="' . $td . '"><input type="text" name="rank[' . $ds[ 'rankID' ] . ']" value="' .
                getinput($ds[ 'rank' ]) . '" size="58" />'.$user_list.'</td>
            <td class="' . $td . '" align="center">' . (($ds[ 'special' ]==1) ? "x" : "") . '</td>
	        <td class="' . $td . '" align="center">'.$min.'</td>
	        <td class="' . $td . '" align="center">'.$max.'</td>
	        <td class="' . $td . '" align="center"><input type="button" onclick="MM_confirm(\'' .
                $_language->module[ 'really_delete' ] . '\', \'admincenter.php?site=ranks&amp;delete=true&amp;rankID=' .
                $ds[ 'rankID' ] . '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module[ 'delete' ] .
                '" /></td>
	      </tr>';
        }
        $i++;
    }
    echo '<tr>
      <td class="td_head" colspan="6" align="right"><input type="hidden" name="captcha_hash" value="' . $hash .
        '"><input type="submit" name="saveedit" value="' . $_language->module[ 'update' ] . '" /></td>
    </tr>
  </table>
  </form>';
}
