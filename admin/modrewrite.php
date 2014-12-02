<?php
/*
##########################################################################
# #
# Version 4 / / / #
# -----------__---/__---__------__----__---/---/- #
# | /| / /___) / ) (_ ` / ) /___) / / #
# _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___ #
# Free Content / Management System #
# / #
# #
# #
# Copyright 2005-2014 by webspell.org #
# #
# visit webSPELL.org, webspell.info to get webSPELL for free #
# - Script runs under the GNU GENERAL PUBLIC LICENSE #
# - It's NOT allowed to remove this copyright-tag #
# -- http://www.fsf.org/licensing/licenses/gpl.html #
# #
# Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at), #
# Far Development by Development Team - webspell.org #
# #
# visit webspell.org #
# #
##########################################################################
*/

$_language->readModule('modrewrite');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

$types = '';
foreach($GLOBALS['_modRewrite']->getTypes() as $typ){
    $types .= '<option value="'.$typ.'">'.$typ.'</option>';
}

if($action=="add") {

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo'<h1>&curren; <a href="admincenter.php?site=modrewrite" class="white">'.$_language->module['modrewrite'].'</a> &raquo; '.$_language->module['add_rule'].'</h1>';
    echo '<script type="text/javascript">
    function addRow(){
        table = document.getElementById("fields");
        rows = table.rows;
        text = table.rows[1].innerHTML;
        new_row = table.insertRow(rows.length-1);
        new_row.innerHTML = text;
    }
    </script>';
    echo'<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
    <td><b>'.$_language->module['variables'].':</b></td>
    <td><table id="fields" width="100%">
    <tr>
    <td class="title">'.$_language->module['variable'].':</td>
    <td class="title">'.$_language->module['type'].':</td>
    </tr>
    <tr>
    <td><input type="text" name="keys[]"></td>
    <td><select name="values[]">'.$types.'</select></td>
    </tr>
    <tr>
    <td></td>
    <td><a onclick="javascript:addRow();">'.$_language->module['more'].'</a></td>
    </tr>
    </table></td>
    </tr>
    <tr>
    <td><b>'.$_language->module['url'].':</b></td>
    <td><input type="text" name="url" style="width:100%;"></td>
    </tr>
    <tr>
    <td><b>'.$_language->module['replace'].':</b></td>
    <td><input type="text" name="regex" style="width:100%;"></td>
    </tr>
    <tr>
    <td><input type="hidden" name="captcha_hash" value="'.$hash.'"></td>
    <td><input type="submit" name="save" value="'.$_language->module['save_rule'].'"></td>
    </tr>
    </table>
    </form>';
}

elseif($action=="edit") {
    $ds=mysqli_fetch_assoc(safe_query("SELECT * FROM ".PREFIX."modrewrite WHERE ruleID='".$_GET["ruleID"]."'"));

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo'<h1>&curren; <a href="admincenter.php?site=modrewrite" class="white">'.$_language->module['modrewrite'].'</a> &raquo; '.$_language->module['edit_rule'].'</h1>';

    $rules = '';
    $data = unserialize($ds['fields']);
    if(count($data)){
        foreach($data as $key => $field){
            $rules .= '<tr>
            <td><input type="text" value="'.$key.'" name="keys[]"></td>
            <td><select name="values[]">'.str_replace('value="'.$field.'"','value="'.$field.'" selected="selected"',$types).'</select></td>
            </tr>';
        }
    }
    else{
        $rules .= '<tr>
        <td><input type="text" value="" name="keys[]"></td>
        <td><select name="values[]">'.$types.'</select></td>
        </tr>';
    }
    echo '<script type="text/javascript">
    function addRow(){
        table = document.getElementById("fields");
        rows = table.rows;
        text = table.rows[1].innerHTML;
        new_row = table.insertRow(rows.length-1);
        new_row.innerHTML = text;
    }
    </script>';
    echo'<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
    <td><b>'.$_language->module['variables'].':</b></td>
    <td><table id="fields" width="100%">
    <tr>
    <td class="title">'.$_language->module['variable'].':</td>
    <td class="title">'.$_language->module['type'].':</td>
    </tr>
    '.$rules.'
    <tr>
    <td></td>
    <td><a onclick="javascript:addRow();">'.$_language->module['more'].'</a></td>
    </tr>
    </table></td>
    </tr>
    <tr>
    <td><b>'.$_language->module['url'].':</b></td>
    <td><input type="text" name="url" value="'.$ds['link'].'" style="width:100%;"></td>
    </tr>
    <tr>
    <td><b>'.$_language->module['replace'].':</b></td>
    <td><input type="text" name="regex" value="'.$ds['regex'].'" style="width:100%;"></td>
    </tr>
    <tr>
    <td><input type="hidden" name="ruleID" value="'.$ds['ruleID'].'"><input type="hidden" name="captcha_hash" value="'.$hash.'"></td>
    <td><input type="submit" name="saveedit" value="'.$_language->module['save_rule'].'"></td>
    </tr>
    </table>
    </form>';
}
elseif($action == 'rebuild'){
    $ds=safe_query("SELECT * FROM ".PREFIX."modrewrite");
    $anz=mysqli_num_rows($ds);
    while($flags = mysqli_fetch_array($ds)) {

        $data = unserialize($flags['fields']);
        $replace = $GLOBALS['_modRewrite']->buildReplace($flags['link'], $flags['regex'],$data);
        security_slashes($replace);
        $rebuild = $GLOBALS['_modRewrite']->buildRebuild($flags['regex'], $flags['link'],$data);
        security_slashes($rebuild);

        safe_query("UPDATE ".PREFIX."modrewrite SET
            replace_regex ='".$replace[0]."',
            replace_result ='".$replace[1]."',
            rebuild_regex ='".$rebuild[0]."',
            rebuild_result ='".$rebuild[1]."'
            WHERE ruleID='".$flags["ruleID"]."'");
    }
    echo "Done";
}

elseif(isset($_POST['save'])) {
    $CAPCLASS = new \webspell\Captcha;
    if($CAPCLASS->checkCaptcha(0, $_POST['captcha_hash'])) {
        $data = array();
        foreach($_POST['keys'] as $key => $val){
            if(!empty($val)){
                $data[$val] = $_POST['values'][$key];
            }
        }

        $replace = $GLOBALS['_modRewrite']->buildReplace(stripslashes($_POST['url']), stripslashes($_POST['regex']),$data);
        security_slashes($replace);
        $rebuild = $GLOBALS['_modRewrite']->buildRebuild(stripslashes($_POST['regex']), stripslashes($_POST['url']),$data);
        security_slashes($rebuild);

        $data = serialize($data);
        safe_query("INSERT INTO ".PREFIX."modrewrite (link,regex, fields, replace_regex, replace_result, rebuild_regex, rebuild_result) values('".$_POST['url']."', '".$_POST['regex']."','".$data."','".$replace[0]."','".$replace[1]."','".$rebuild[0]."','".$rebuild[1]."')");
        redirect("admincenter.php?site=modrewrite","",0);
    }
    else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST["saveedit"])) {
    $CAPCLASS = new \webspell\Captcha;
    if($CAPCLASS->checkCaptcha(0, $_POST['captcha_hash'])) {
        $data = array();
        foreach($_POST['keys'] as $key => $val){
            if(!empty($val)){
                $data[$val] = $_POST['values'][$key];
            }
        }

        $replace = $GLOBALS['_modRewrite']->buildReplace(stripslashes($_POST['url']), stripslashes($_POST['regex']),$data);
        security_slashes($replace);
        $rebuild = $GLOBALS['_modRewrite']->buildRebuild(stripslashes($_POST['regex']), stripslashes($_POST['url']),$data);
        security_slashes($rebuild);

        $data = serialize($data);
        safe_query("UPDATE ".PREFIX."modrewrite SET link='".$_POST['url']."',
            regex='".$_POST['regex']."',
            fields='".$data."',
            replace_regex ='".$replace[0]."',
            replace_result ='".$replace[1]."',
            rebuild_regex ='".$rebuild[0]."',
            rebuild_result ='".$rebuild[1]."'
            WHERE ruleID='".$_POST["ruleID"]."'");
        redirect("admincenter.php?site=modrewrite","",0);

    } else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET["delete"])) {
    $CAPCLASS = new \webspell\Captcha;
    if($CAPCLASS->checkCaptcha(0, $_GET['captcha_hash'])) {
        safe_query("DELETE FROM ".PREFIX."modrewrite WHERE ruleID='".$_GET["ruleID"]."'");
        redirect("admincenter.php?site=modrewrite","",0);
    } else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['test'])){
    echo'<h1>&curren; '.$_language->module['modrewrite_settings'].'</h1>';
    $do_test = false;
    if(function_exists("apache_get_modules")){
        $info = $_language->module['apache_with_module'].'<br>';
        if(in_array('mod_rewrite',apache_get_modules())){
            $info .=  $_language->module['modrewrite_is_enabled'].'<br>';
            $do_test = true;
        }
        else{
            $info .=  $_language->module['modrewrite_is_disabled'].'<br>';
        }
    }
    elseif(stristr($_SERVER['SERVER_SOFTWARE'],'Apache')){
        $info = $_language->module['apache_with_cgi'].'<br>';
        $do_test = true;
    }
    else{
        $info = $_language->module['unsupported_webserver'].'<br>';
    }

    $enable = "";
    $status = $_language->module['unexpected_result'];

    if($do_test){
        $folder = 'ht_test';
        if(!is_dir($folder)){
            mkdir($folder,0777);
        }
        $file = ".htaccess";
        $module = "mod_rewrite.c";
        $path = $GLOBALS['_modRewrite']->getRewriteBase().'admin/'.$folder.'/';
        $content = '<IfModule '.$module.'>
        RewriteEngine on
        RewriteBase '.$path.'
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule (.*) test.php?q=$1
</IfModule>';
        file_put_contents($folder.'/test.php','Test successful');
        $written = @file_put_contents($folder.'/'.$file, $content);

        $enable = "";
        $unlink = true;

        if($written == false){
            $info .= sprintf($_language->module['can_not_write_file'],$file);
        }
        else{
            $protocol = 'http';
            if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
                $protocol .= 's';
            }

            $port = '';
            if ($_SERVER["SERVER_PORT"] != "80") {
                $port = ":".$_SERVER["SERVER_PORT"];
            }

            $url = $protocol.'://'.$_SERVER["SERVER_NAME"].$port.dirname($_SERVER["REQUEST_URI"]).'/ht_test/not_existing_file';
            $headers = @get_headers($url, 1);
            if($headers == false){
                $info .= $_language->module['fopen_disabled'];
                $status = '<div id="result"></div>';
                $unlink = false;
            }
            elseif(stristr($headers[0],'404')){
                $status = $_language->module['modrewrite_failed'];
            }
            elseif(stristr($headers[0],'500')){
                $status = $_language->module['htaccess_failed'];
            }
            elseif(stristr($headers[0],'200')){
                $status = $_language->module['test_successful'];
                $enable = '<input type="submit" name="enable" value="'.$_language->module['enable'].'">';
            }
            else{
                $status = $_language->module['unexpected_result'];
                $info .= var_dump($headers);
            }
        }
        if($unlink){
            unlink($folder.'/test.php');
            unlink($folder.'/'.$file);
            rmdir($folder);
        }
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo'<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
    <td width="15%"><b>'.$_language->module['result'].':</b></td>
    <td>'.$status.'</td>
    </tr>
    <tr>
    <td width="15%"><b>'.$_language->module['debug'].':</b></td>
    <td>'.$info.'</td>
    </tr>
    <tr>
    <td><input type="hidden" name="captcha_hash" value="'.$hash.'"></td>
    <td>'.$enable.'</td>
    </tr>
    </table>
    </form>';
}

elseif(isset($_POST['enable'])){
    $folder = '../';
    $file = ".htaccess";
    $module = "mod_rewrite.c";
    $path = $GLOBALS['_modRewrite']->getRewriteBase();
    $content = '<IfModule '.$module.'>
    RewriteEngine on
    RewriteBase '.$path.'
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ _rewrite.php?url=$1 [L,QSA]
</IfModule>';

    $info = '';
    if(file_exists($folder.'/'.$file)){
        $info .= $_language->module['htaccess_exists_merge'];
        $file = '.htaccess_ws';
    }

    $written = @file_put_contents($folder.'/'.$file, $content);

    if($written == false){
        $info .= sprintf($_language->module['can_not_write_file'],$file);
        echo $info;
    }
    else{
        safe_query("UPDATE ".PREFIX."settings SET modRewrite='1'");
        echo $info;
        redirect("admincenter.php?site=modrewrite",$_language->module['successful'],2);
    }

}

elseif(isset($_POST['disable'])){
    safe_query("UPDATE ".PREFIX."settings SET modRewrite='0'");
    redirect("admincenter.php?site=modrewrite",$_language->module['successful'],2);
}

else {

    echo'<h1>&curren; '.$_language->module['modrewrite_settings'].'</h1>';
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    if($modRewrite == false){
        echo'<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
        <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
        <td width="15%"><b>RewriteBase:</b></td>
        <td><input type="text" name="base" value="'.$GLOBALS['_modRewrite']->getRewriteBase().'" style="width:70%;"></td>
        </tr>
        <tr>
        <td><input type="hidden" name="captcha_hash" value="'.$hash.'"></td>
        <td><input type="submit" name="test" value="'.$_language->module['test_support'].'"></td>
        </tr>
        </table>
        </form>';
    }
    else{
        echo'<form method="post" action="admincenter.php?site=modrewrite" enctype="multipart/form-data">
        <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
        <td width="15%"><b>'.$_language->module['state'].':</b></td>
        <td>'.$_language->module['enabled'].'</td>
        </tr>
        <tr>
        <td><input type="hidden" name="captcha_hash" value="'.$hash.'"></td>
        <td><input type="submit" name="disable" value="'.$_language->module['disable'].'"></td>
        </tr>
        </table>
        </form>';
    }

    echo'<h1>&curren; '.$_language->module['modrewrite_rules'].'</h1>';

    echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modrewrite&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_rule'].'"> ';
    echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modrewrite&amp;action=rebuild\');return document.MM_returnValue" value="'.$_language->module['rebuild'].'"><br><br>';

    echo'
    <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
    <td width="60%" class="title"><b>'.$_language->module['rule'].'</b></td>
    <td width="15%" class="title"><b>'.$_language->module['variables'].'</b></td>
    <td width="25%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';

    $ds=safe_query("SELECT * FROM ".PREFIX."modrewrite ORDER BY regex");
    $anz=mysqli_num_rows($ds);
    if($anz) {

        $i=1;
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        while($flags = mysqli_fetch_array($ds)) {
            if($i%2) { $td='td1'; }
            else { $td='td2'; }
            echo'<tr>
            <td class="'.$td.'" align="left">'.$flags['regex'].'<br>'.$flags['link'].'</td>
            <td class="'.$td.'">'.count(unserialize($flags['fields'])).'</td>
            <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=modrewrite&amp;action=edit&amp;ruleID='.$flags['ruleID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'">
            <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=modrewrite&amp;delete=true&amp;ruleID='.$flags['ruleID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'"></td>
            </tr>';

            $i++;
        }
    }
    else echo'<tr><td class="td1" colspan="5">'.$_language->module['no_entries'].'</td></tr>';

    echo '</table>
    </form>';
}
?>
