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

$info = '';

if ($_POST['installtype'] == 'update') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update31_4beta4();
    update4beta4_4beta5();
    update4beta5_4beta6();
    update4beta6_4final();
    update40000_40100();
    update40100_40101();
    update40101_420();
    update420_430();
    updatePasswordHash();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'full') {
    $type = '<b>'.$_language->module['install_complete'].'</b>';
    $info = $_language->module['reset_chmod'];

    $host = $_POST['host'];
    $user = $_POST['user'];
    $pwd = $_POST['pwd'];
    $db = $_POST['db'];
    $prefix = $_POST['prefix'];
    $adminname = $_POST['adminname'];
    $adminpwd = $_POST['adminpwd'];
    $adminmail = $_POST['adminmail'];
    $url = $_POST['url'];

    if (!(mb_strlen(trim($host)))) {
        $error=$_language->module['verify_data'];
        die("<b>".$_language->module['error']."<br>".$error."</b><br><br><a href='javascript:history.back()'>".$_language->module['back']."</a>");
    }
    if (!(mb_strlen(trim($db)))) {
        $error=$_language->module['verify_data'];
        die("<b>".$_language->module['error']."<br>".$error."</b><br><br><a href='javascript:history.back()'>".$_language->module['back']."</a>");
    }
    if (!(mb_strlen(trim($adminname)))) {
        $error=$_language->module['verify_data'];
        die("<b>".$_language->module['error']."<br>".$error."</b><br><br><a href='javascript:history.back()'>".$_language->module['back']."</a>");
    }
    if (!(mb_strlen(trim($adminpwd)))) {
        $error=$_language->module['verify_data'];
        die("<b>".$_language->module['error']."<br>".$error."</b><br><br><a href='javascript:history.back()'>".$_language->module['back']."</a>");
    }
    if (!(mb_strlen(trim($adminmail)))) {
        $error=$_language->module['verify_data'];
        die("<b>".$_language->module['error']."<br>".$error."</b><br><br><a href='javascript:history.back()'>".$_language->module['back']."</a>");
    }
    if (!(mb_strlen(trim($url)))) {
        $error=$_language->module['verify_data'];
        die("<b>".$_language->module['error']."<br>".$error."</b><br><br><a href='javascript:history.back()'>".$_language->module['back']."</a>");
    }

    $adminpassword=generatePasswordHash($adminpwd);

    //write _mysql.php

    $file = ('../_mysql.php');
    if ($fp = fopen($file, 'wb')) {
        $string='<?php
$host = "'.$host.'";
$user = "'.$user.'";
$pwd = "'.$pwd.'";
$db = "'.$db.'";
if(!defined("PREFIX")){
	define("PREFIX", \''.$prefix.'\');
}
?>';

        fwrite($fp, $string);
        fclose($fp);
    } else {
        echo $_language->module['write_failed'];
    }

    //write sql-tables

    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");


    define("PREFIX", $prefix);

    fullinstall();
    update4beta4_4beta5();
    update4beta5_4beta6();
    update4beta6_4final();
    update40000_40100();
    update40100_40101();
    update40101_420();
    update420_430();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'update_beta') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update4beta4_4beta5();
    update4beta5_4beta6();
    update4beta6_4final();
    update40000_40100();
    update40100_40101();
    update40101_420();
    update420_430();
    updatePasswordHash();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'update_beta5') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update4beta5_4beta6();
    update4beta6_4final();
    update40000_40100();
    update40100_40101();
    update40101_420();
    update420_430();
    updatePasswordHash();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'update_beta6') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update4beta6_4final();
    update40000_40100();
    update40100_40101();
    update40101_420();
    update420_430();
    updatePasswordHash();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'update_final') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update40000_40100();
    update40100_40101();
    update40101_420();
    update420_430();
    updatePasswordHash();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'update_40100') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update40100_40101();
    update40101_420();
    update420_430();
    updatePasswordHash();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'update_40102') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update40101_420();
    update420_430();
    updatePasswordHash();
    addSMTPSupport();

} elseif ($_POST['installtype'] == 'update_420') {
    $type = '<b>'.$_language->module['update_complete'].'</b>';

    include('../_mysql.php');
    $_database = new mysqli($host, $user, $pwd, $db);

    if (mysqli_connect_error()) {
        die($_language->module['error_mysql']);
    }

    mysqli_query($_database, "SET NAMES 'utf8'");

    update420_430();
    updatePasswordHash();
    addSMTPSupport();

}
include("../src/func/filesystem.php");
$remove_install = @rm_recursive("./");
if ($remove_install) {
    $delete_info = $_language->module['folder_removed'];
} else {
    $delete_info = $_language->module['delete_folder'];
}
?>

   <center>
    <?php echo $type; ?><br><br>
    <?php echo $delete_info; ?><br><br>
    <?php echo $info; ?><br><br><br>
   <a href="../index.php"><b><?php echo $_language->module['view_site']; ?></b></a>
   </center>
   </td>
  </tr>
