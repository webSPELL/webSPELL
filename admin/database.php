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

if (isset($_POST[ 'upload' ])) {
    $_language->readModule('database');

    if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
        die($_language->module[ 'access_denied' ]);
    }
    $upload = $_FILES[ 'sql' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if ($upload[ 'name' ] != "") {
            $get = safe_query("SELECT DATABASE()");
            $ret = mysqli_fetch_array($get);
            $db = $ret[ 0 ];
            //drop all tables from webSPELL DB
            $result = mysqli_query($_database, "SHOW TABLES FROM " . $db);
            while ($table = mysqli_fetch_array($result)) {
                safe_query("DROP TABLE `" . $table[ 0 ] . "`");
            }

            move_uploaded_file($upload[ 'tmp_name' ], '../tmp/' . $upload[ 'name' ]);
            $new_query = file('../tmp/' . $upload[ 'name' ]);
            foreach ($new_query as $query) {
                @mysqli_query($_database, $query);
            }
            @unlink('../tmp/' . $upload[ 'name' ]);
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

if (isset($_GET[ 'back' ])) {
    $returnto = $_GET[ 'back' ];
} else {
    $returnto = "database";
}

if ($action == "optimize") {
    $_language->readModule('database');

    echo '<h1>&curren; ' . $_language->module[ 'database' ] . '</h1>';

    if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
        die($_language->module[ 'access_denied' ]);
    }

    $get = safe_query("SELECT DATABASE()");
    $ret = mysqli_fetch_array($get);
    $db = $ret[ 0 ];

    $result = mysqli_query($_database, "SHOW TABLES FROM " . $db);
    while ($table = mysqli_fetch_array($result)) {
        safe_query("OPTIMIZE TABLE `" . $table[ 0 ] . "`");
    }
    redirect('admincenter.php?site=' . $returnto, '', 0);
} elseif ($action == "write") {
    include('../_mysql.php');
    include('../_settings.php');
    include('../version.php');
    systeminc("func/captcha");

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        //Get database information and write SQL-commands
        $final = "--   #webSPELL " . $version . ", visit webspell.org#\n";
        $final .= "--   webSPELL.org database backup\n";
        $final .= "--\n";
        $final .= "--   webSPELL version: " . $version . "\n";
        $final .= "--   PHP version: " . phpversion() . "\n";
        $final .= "--   MySQL version: " . mysqli_get_server_info($_database) . "\n";
        $final .= "--   Date: " . date("r") . "\n";

        $result = mysqli_query($_database, "SHOW TABLE STATUS");
        while ($table = mysqli_fetch_array($result, MYSQLI_BOTH)) {
            $i = 0;
            $result2 = mysqli_query($_database, "SHOW COLUMNS FROM $table[0]");
            $z = mysqli_num_rows($result2);
            $final .=
                "\n--\n-- webSPELL DB Export - Table structure for table `" . $table[ 0 ] . "`\n--\n\nCREATE TABLE `" .
                $table[ 0 ] . "` (";
            $prikey = false;
            $insert_keys = null;
            while ($row2 = mysqli_fetch_assoc($result2)) {
                $i++;
                $insert_keys .= "`" . $row2[ 'Field' ] . "`";
                $final .= "`" . $row2[ 'Field' ] . "` " . $row2[ 'Type' ];
                if ($row2[ 'Null' ] != "YES") {
                    $final .= " NOT NULL";
                }
                if ($row2[ 'Default' ]) {
                    $final .= " DEFAULT '" . $row2[ 'Default' ] . "'";
                }
                if ($row2[ 'Extra' ]) {
                    $final .= " " . $row2[ 'Extra' ];
                }
                if ($row2[ 'Key' ] == "PRI") {
                    $final .= ", PRIMARY KEY  (`" . $row2[ 'Field' ] . "`)";
                    $prikey = true;
                }
                if ($i < $z) {
                    $final .= ", ";
                    $insert_keys .= ", ";
                } else {
                    $final .= " ";
                }
            }
            if ($prikey) {
                if ($table[ 10 ]) {
                    $auto_inc = " AUTO_INCREMENT=" . $table[ 10 ];
                } else {
                    $auto_inc = " AUTO_INCREMENT=1";
                }
            } else {
                $auto_inc = "";
            }
            $charset = explode("_", $table[ 14 ]);
            $final .= ") ENGINE=" . $table[ 1 ] . " DEFAULT CHARSET=" . $charset[ 0 ] . " COLLATE=" . $table[ 14 ] .
                $auto_inc . ";\n\n--\n-- webSPELL DB Export - Dumping data for table `" . $table[ 0 ] . "`\n--\n";

            $inhaltq = mysqli_query($_database, "SELECT * FROM $table[0]");
            while ($inhalt = mysqli_fetch_array($inhaltq, MYSQLI_BOTH)) {
                $final .= "\nINSERT INTO `$table[0]` (";
                $final .= $insert_keys;
                $final .= ") VALUES (";
                for ($i = 0; $i < $z; $i++) {
                    $inhalt[ $i ] = str_replace("'", "`", $inhalt[ $i ]);
                    $inhalt[ $i ] = str_replace("\\", "\\\\", $inhalt[ $i ]);
                    $einschub = "'" . $inhalt[ $i ] . "'";
                    $final .= preg_replace('/\r\n|\r|\n/', '\r\n', $einschub);
                    if (($i + 1) < $z) {
                        $final .= ", ";
                    }
                }
                $final .= ");";
            }
            $final .= "\n";
        }

        systeminc('session');
        systeminc('login');

        $anz = mysqli_num_rows(safe_query(
            "SELECT userID FROM " . PREFIX .
            "user_groups WHERE (page='1' OR super='1') AND userID='$userID'"
        ));

        if ($anz) {
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download");
            header("Content-Description: File Transfer");
            if (is_integer(mb_strpos(strtolower($_SERVER[ "HTTP_USER_AGENT" ]), "msie")) &&
                is_integer(mb_strpos(strtolower($_SERVER[ "HTTP_USER_AGENT" ]), "win"))
            ) {
                header("Content-Disposition: filename=backup-" . strtolower(date("D-d-M-Y")) . ".sql;");
            } else {
                header("Content-Disposition: attachment; filename=backup-" . strtolower(date("D-d-M-Y")) . ".sql;");
            }
            header("Content-Transfer-Encoding: binary");
            echo $final;
        }
    } else {
        echo $_language->readModule('database') . $_language->module[ 'transaction_invalid' ];
    }
} else {
    $_language->readModule('database');

    if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
        die($_language->module[ 'access_denied' ]);
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<h1>&curren; ' . $_language->module[ 'database' ] . '</h1>';

    echo '<form method="post" action="admincenter.php?site=database" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td class="title"><b>' . $_language->module[ 'select_option' ] . '</b></td>
    </tr>
    <tr>
      <td class="td1" colspan="2">&#8226; <a href="database.php?action=write&amp;captcha_hash=' . $hash . '">' .
        $_language->module[ 'export' ] . '</a></td>
    </tr>
    <tr>
      <td class="td2" colspan="2">&#8226; <a href="admincenter.php?site=database&amp;action=optimize">' .
        $_language->module[ 'optimize' ] . '</a></td>
    </tr>
    <tr>
      <td class="td1">' . $_language->module[ 'import_info' ] . '<br><br>
      <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
          <td width="15%"><b>' . $_language->module[ 'backup_file' ] . '</b></td>
          <td width="85%"><input name="sql" type="file" size="40" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
          <td><input type="submit" name="upload" value="' . $_language->module[ 'upload' ] . '" /></td>
        </tr>
      </table>
      </td>
    </tr>
  </table>
  </form>';
}
