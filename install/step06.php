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
?>

<tr>
    <td id="step" align="center" colspan="2">
        <span class="steps start"><?php echo $_language->module['step0']; ?></span>
        <span class="steps"><?php echo $_language->module['step1']; ?></span>
        <span class="steps"><?php echo $_language->module['step2']; ?></span>
        <span class="steps"><?php echo $_language->module['step3']; ?></span>
        <span class="steps"><?php echo $_language->module['step4']; ?></span>
        <span class="steps"><?php echo $_language->module['step5']; ?></span>
        <span class="steps end" id="active"><?php echo $_language->module['step6']; ?></span>
    </td>
</tr>
<tr id="headline">
    <td colspan="2" id="title"><?php echo $_language->module['finish_install']; ?></td>
</tr>
<tr>
<td id="content" colspan="2">

<?php
include('functions.php');

if ($_POST['installtype'] == 'update') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);
    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "31_4beta4";
        $update_functions[] = "4beta4_4beta5";
        $update_functions[] = "4beta5_4beta6";
        $update_functions[] = "4beta6_4final_1";
        $update_functions[] = "4beta6_4final_2";
        $update_functions[] = "40000_40100";
        $update_functions[] = "40100_40101";
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }

} elseif ($_POST['installtype'] == 'full') {

    $type = '<b>' . $_language->module['install_complete'] . '</b>';

    $host = $_POST['host'];
    $user = $_POST['user'];
    $pwd = $_POST['pwd'];
    $db = $_POST['db'];
    $prefix = $_POST['prefix'];
    $adminname = $_POST['adminname'];
    $adminpwd = $_POST['adminpwd'];
    $adminmail = $_POST['adminmail'];
    $url = $_POST['url'];

    $errors = array();

    if (!(mb_strlen(trim($host)))) {
        $errors[] = $_language->module['verify_data'];
    }
    if (!(mb_strlen(trim($db)))) {
        $errors[] = $_language->module['verify_data'];
    }
    if (!(mb_strlen(trim($adminname)))) {
        $errors[] = $_language->module['verify_data'];
    }
    if (!(mb_strlen(trim($adminpwd)))) {
        $errors[] = $_language->module['verify_data'];
    }
    if (!(mb_strlen(trim($adminmail)))) {
        $errors[] = $_language->module['verify_data'];
    }
    if (!(mb_strlen(trim($url)))) {
        $errors[] = $_language->module['verify_data'];
    }

    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    $file = ('../_mysql.php');
    if ($fp = fopen($file, 'wb')) {
        $string = '<?php
$host = "' . $host . '";
$user = "' . $user . '";
$pwd = "' . $pwd . '";
$db = "' . $db . '";
if(!defined("PREFIX")){
	define("PREFIX", \'' . $prefix . '\');
}
?>';

        fwrite($fp, $string);
        fclose($fp);
    } else {
        $errors[] = $_language->module['write_failed'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {

        $_SESSION['adminpassword'] = generatePasswordHash($adminpwd);
        $_SESSION['adminname'] = $adminname;
        $_SESSION['adminmail'] = $adminmail;
        $_SESSION['url'] = $url;

        $update_functions = array();
        $update_functions[] = "base_1";
        $update_functions[] = "base_2";
        $update_functions[] = "base_3";
        $update_functions[] = "base_4";
        $update_functions[] = "base_5";
        $update_functions[] = "base_6";
        $update_functions[] = "base_7";
        $update_functions[] = "base_8";
        $update_functions[] = "base_9";
        $update_functions[] = "base_10";
        $update_functions[] = "base_11";
        $update_functions[] = "base_12";
        $update_functions[] = "base_13";
        $update_functions[] = "base_14";
        $update_functions[] = "4beta4_4beta5";
        $update_functions[] = "4beta5_4beta6";
        $update_functions[] = "4beta6_4final_1";
        $update_functions[] = "4beta6_4final_2";
        $update_functions[] = "40000_40100";
        $update_functions[] = "40100_40101";
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";

        $text = update_progress($update_functions);
    }

} elseif ($_POST['installtype'] == 'update_beta') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "4beta4_4beta5";
        $update_functions[] = "4beta5_4beta6";
        $update_functions[] = "4beta6_4final_1";
        $update_functions[] = "4beta6_4final_2";
        $update_functions[] = "40000_40100";
        $update_functions[] = "40100_40101";
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }
} elseif ($_POST['installtype'] == 'update_beta5') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);
    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "4beta5_4beta6";
        $update_functions[] = "4beta6_4final_1";
        $update_functions[] = "4beta6_4final_2";
        $update_functions[] = "40000_40100";
        $update_functions[] = "40100_40101";
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }
} elseif ($_POST['installtype'] == 'update_beta6') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "4beta6_4final_1";
        $update_functions[] = "4beta6_4final_2";
        $update_functions[] = "40000_40100";
        $update_functions[] = "40100_40101";
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }


} elseif ($_POST['installtype'] == 'update_final') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "40000_40100";
        $update_functions[] = "40100_40101";
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }
} elseif ($_POST['installtype'] == 'update_40100') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "40100_40101";
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }

} elseif ($_POST['installtype'] == 'update_40102') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "40101_420_1";
        $update_functions[] = "40101_420_2";
        $update_functions[] = "40101_420_3";
        $update_functions[] = "40101_420_4";
        $update_functions[] = "40101_420_5";
        $update_functions[] = "40101_420_6";
        $update_functions[] = "40101_420_7";
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }
} elseif ($_POST['installtype'] == 'update_420') {

    $type = '<b>' . $_language->module['update_complete'] . '</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);
    $errors = array();
    if (mysqli_connect_error()) {
        $errors[] = $_language->module['error_mysql'];
    }

    if (count($errors)) {
        $fehler = implode('<br>&#8226; ', array_unique($error));

        $text = '<div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Close</span>
            </button>
            <strong>' . $_language->module['error'] . ':</strong><br>
            <br>
            &#8226; ' . $fehler . '
        </div>';
    } else {
        $update_functions = array();
        $update_functions[] = "420_430_1";
        $update_functions[] = "420_430_2";
        $update_functions[] = "passwordhash";

        $text = update_progress($update_functions);
    }

}
?>

<center>
    <?php echo $type; ?><br><br>
    <?php echo $text; ?><br><br><br>
    <a href="../index.php"><b><?php echo $_language->module['view_site']; ?></b></a>
</center>
</td>
</tr>
