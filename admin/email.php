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

$_language->readModule('email');

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

echo '<h1>&curren; ' . $_language->module[ 'email' ] . '</h1>';

if (isset($_POST[ 'submit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE
                " . PREFIX . "email
            SET
                host='" . $_POST[ 'host' ] . "',
                user='" . $_POST[ 'user' ] . "',
                password='" . $_POST[ 'password' ] . "',
                port='" . intval($_POST[ 'port' ]) . "',
                secure='" . intval($_POST[ 'secure' ]) . "',
                auth='" . intval($_POST[ 'auth' ]) . "',
                debug='" . intval($_POST[ 'debug' ]) . "',
                smtp='" . intval($_POST[ 'smtp' ]) . "',
                html='" . intval($_POST[ 'html' ]) . "'"
        );
        redirect("admincenter.php?site=email", "", 0);
    } else {
        redirect("admincenter.php?site=email", $_language->module[ 'transaction_invalid' ], 3);
    }
} else {
    $settings = safe_query("SELECT * FROM " . PREFIX . "email");
    $ds = mysqli_fetch_array($settings);

    if ($ds[ 'smtp' ] == '0') {
        if ($ds[ 'auth' ]) {
            $auth = " checked=\"checked\"";
        } else {
            $auth = "";
        }
        $show_auth = " style=\"display: none;\"";
        $show_auth2 = " style=\"display: none;\"";
    } else {
        if ($ds[ 'auth' ]) {
            $auth = " checked=\"checked\"";
            $show_auth = "";
        } else {
            $auth = "";
            $show_auth = " style=\"display: none;\"";
        }
        $show_auth2 = "";
    }

    if ($ds[ 'html' ]) {
        $html = " checked=\"checked\"";
    } else {
        $html = "";
    }

    $smtp = "<option value='0'>" . $_language->module[ 'type_phpmail' ] . "</option><option value='1'>" .
        $_language->module[ 'type_smtp' ] . "</option><option value='2'>" .
        $_language->module[ 'type_pop' ] . "</option>";
    $smtp = str_replace(
        "value='" . $ds[ 'smtp' ] . "'",
        "value='" . $ds[ 'smtp' ] . "' selected='selected'",
        $smtp
    );

    $secure = "<option value='0'>" . $_language->module[ 'secure_none' ] . "</option><option value='1'>" .
        $_language->module[ 'secure_tls' ] . "</option><option value='2'>" .
        $_language->module[ 'secure_ssl' ] . "</option>";
    $secure = str_replace(
        "value='" . $ds[ 'secure' ] . "'",
        "value='" . $ds[ 'secure' ] . "' selected='selected'",
        $secure
    );

    $debug = "<option value='0'>" . $_language->module[ 'debug_0' ] . "</option><option value='1'>" .
        $_language->module[ 'debug_1' ] . "</option><option value='2'>" .
        $_language->module[ 'debug_2' ] . "</option><option value='3'>" .
        $_language->module[ 'debug_3' ] . "</option><option value='4'>" .
        $_language->module[ 'debug_4' ] . "</option>";
    $debug = str_replace(
        "value='" . $ds[ 'debug' ] . "'",
        "value='" . $ds[ 'debug' ] . "' selected='selected'",
        $debug
    );

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    ?>

    <script type="text/javascript">
        function HideFields(state){
            if(state == true){
                document.getElementById('tr_user').style.display = "";
                document.getElementById('tr_password').style.display = "";
            }
            else{
                document.getElementById('tr_user').style.display = "none";
                document.getElementById('tr_password').style.display = "none";
            }
        }

        function SetPort(){
            var x = document.getElementById('select_secure').selectedIndex;
            switch(x) {
                case 0:
                    var port = '25'
                    break;
                case 1:
                    var port = '587'
                    break;
                case 2:
                    var port = '465'
                    break;
                default:
                    var port = '25'
            }
            document.getElementById('input_port').value = port;
        }

        function HideFields2(){
            var x = document.getElementById('select_smtp').selectedIndex;
            if(x == '0'){
                document.getElementById('tr_user').style.display = "none";
                document.getElementById('tr_password').style.display = "none";
                document.getElementById('tr_auth').style.display = "none";
                document.getElementById('tr_host').style.display = "none";
                document.getElementById('tr_html').style.display = "none";
                document.getElementById('tr_port').style.display = "none";
                document.getElementById('tr_secure').style.display = "none";
            }
            else{
                var y = document.getElementById('check_auth').checked;
                if(y === true){
                    document.getElementById('tr_user').style.display = "";
                    document.getElementById('tr_password').style.display = "";
                    document.getElementById('tr_auth').style.display = "";
                    document.getElementById('tr_host').style.display = "";
                    document.getElementById('tr_html').style.display = "";
                    document.getElementById('tr_port').style.display = "";
                    document.getElementById('tr_secure').style.display = "";
                } else {
                    document.getElementById('tr_host').style.display = "";
                    document.getElementById('tr_auth').style.display = "";
                    document.getElementById('tr_html').style.display = "";
                    document.getElementById('tr_port').style.display = "";
                    document.getElementById('tr_secure').style.display = "";
                }
            }
        }
    </script>

    <form method="post" action="admincenter.php?site=email" enctype="multipart/form-data">
    <div class="tooltip" id="id1"><?php echo $_language->module[ 'tooltip_1' ]; ?></div>
    <div class="tooltip" id="id2"><?php echo $_language->module[ 'tooltip_2' ]; ?></div>
    <div class="tooltip" id="id3"><?php echo $_language->module[ 'tooltip_3' ]; ?></div>
    <div class="tooltip" id="id4"><?php echo $_language->module[ 'tooltip_4' ]; ?></div>
    <div class="tooltip" id="id5"><?php echo $_language->module[ 'tooltip_5' ]; ?></div>
    <div class="tooltip" id="id6"><?php echo $_language->module[ 'tooltip_6' ]; ?></div>
    <div class="tooltip" id="id7"><?php echo $_language->module[ 'tooltip_7' ]; ?></div>
    <div class="tooltip" id="id8"><?php echo $_language->module[ 'tooltip_8' ]; ?></div>
    <div class="tooltip" id="id9"><?php echo $_language->module[ 'tooltip_9' ]; ?></div>

        <table width="100%" border="0" cellspacing="1" cellpadding="3">
        <tr>
            <td width="15%"><b><?php echo $_language->module[ 'type' ]; ?></b></td>
            <td width="35%"><select id="select_smtp" name="smtp" onchange="javascript:HideFields2();"
                onmouseover="showWMTT('id1')"
                onmouseout="hideWMTT()"><?php echo $smtp; ?></select></td>
        </tr>
        <tr id="tr_auth"<?php echo $show_auth2; ?>>
            <td width="15%"><b><?php echo $_language->module[ 'auth' ]; ?></b></td>
            <td width="35%"><input type="checkbox" id="check_auth" name="auth"
                onchange="javascript:HideFields(this.checked);" onmouseover="showWMTT('id2')"
                onmouseout="hideWMTT()" value="1" <?php echo $auth; ?>/></td>
        </tr>
        <tr id="tr_user"<?php echo $show_auth; ?>>
            <td width="15%"><b><?php echo $_language->module[ 'user' ]; ?></b></td>
            <td width="35%"><input name="user" type="text" value="<?php echo getinput($ds[ 'user' ]); ?>" size="35"
                onmouseover="showWMTT('id3')" onmouseout="hideWMTT()"/></td>
        </tr>
        <tr id="tr_password"<?php echo $show_auth; ?>>
            <td width="15%"><b><?php echo $_language->module[ 'password' ]; ?></b></td>
            <td width="35%"><input type="password" name="password" value="<?php echo getinput($ds[ 'password' ]); ?>"
                size="35" onmouseover="showWMTT('id4')" onmouseout="hideWMTT()"/></td>
        </tr>
        <tr id="tr_host"<?php echo $show_auth2; ?>>
            <td width="15%"><b><?php echo $_language->module[ 'host' ]; ?></b></td>
            <td width="35%"><input type="text" name="host" value="<?php echo getinput($ds[ 'host' ]); ?>" size="35"
                onmouseover="showWMTT('id6')" onmouseout="hideWMTT()"/></td>
        </tr>
        <tr id="tr_port"<?php echo $show_auth2; ?>>
            <td width="15%"><b><?php echo $_language->module[ 'port' ]; ?></b></td>
            <td width="35%"><input id="input_port" type="text" name="port"
                value="<?php echo getinput($ds[ 'port' ]); ?>" size="5"
                onmouseover="showWMTT('id5')" onmouseout="hideWMTT()"/></td>
        </tr>
        <tr id="tr_html"<?php echo $show_auth2; ?>>
            <td width="15%"><b><?php echo $_language->module[ 'html' ]; ?></b></td>
            <td width="35%"><input type="checkbox" id="check_html" name="html"
                onmouseover="showWMTT('id7')"
                onmouseout="hideWMTT()" value="1" <?php echo $html; ?>/></td>
        </tr>
        <tr id="tr_secure"<?php echo $show_auth2; ?>>
            <td width="15%"><b><?php echo $_language->module[ 'secure' ]; ?></b></td>
            <td width="35%"><select id="select_secure" name="secure" onmouseover="showWMTT('id8')"
                onchange="javascript:SetPort();"
                onmouseout="hideWMTT()"><?php echo $secure; ?></select></td>
        </tr>
        <tr>
            <td width="15%"><b><?php echo $_language->module[ 'debug' ]; ?></b></td>
            <td width="35%"><select id="select_debug" name="debug" onmouseover="showWMTT('id9')"
                onmouseout="hideWMTT()"><?php echo $debug; ?></select></td>
        </tr>

    </table>
    <br/><br/>

    <div style="clear: both; text-align: right; padding-top: 20px;">
        <input type="hidden" name="captcha_hash" value="<?php echo $hash; ?>">
        <input type="submit" name="submit" value="<?php echo $_language->module[ 'update' ]; ?>">
    </div>
    </form>
    <?php
}
