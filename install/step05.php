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

if ($_POST['installtype'] == "full" AND $_POST['hp_url']) {
?>

<tr>
    <td id="step" align="center" colspan="2">
        <span class="steps start"><?php echo $_language->module['step0']; ?></span>
        <span class="steps"><?php echo $_language->module['step1']; ?></span>
        <span class="steps"><?php echo $_language->module['step2']; ?></span>
        <span class="steps"><?php echo $_language->module['step3']; ?></span>
        <span class="steps"><?php echo $_language->module['step4']; ?></span>
        <span class="steps" id="active"><?php echo $_language->module['step5']; ?></span>
        <span class="steps end"><?php echo $_language->module['step6']; ?></span>
    </td>
</tr>
<tr id="headline">
    <td colspan="2" id="title"><?php echo $_language->module['data_config']; ?></td>
</tr>
<tr>
    <td id="content" colspan="2">
        <table border="0" cellpadding="0" cellspacing="2">
            <tr>
                <td colspan="2"><b><?php echo $_language->module['database_config']; ?></b></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['host_name']; ?>:</td>
                <td><input type="text" name="host" size="30" value="localhost">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_1']; ?></span></a></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['mysql_username']; ?>:</td>
                <td><input type="text" name="user" size="30">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_2']; ?></span></a></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['mysql_password']; ?>:</td>
                <td><input type="password" name="pwd" size="30">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_3']; ?></span></a></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['mysql_database']; ?>:</td>
                <td><input type="text" name="db" size="30">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_4']; ?></span></a></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['mysql_prefix']; ?>:</td>
                <td><input name="prefix" type="text" value="<?php echo 'ws_' . RandPass(3) . '_'; ?>" size="10">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_5']; ?></span></a></td>
            </tr>
            <tr>
                <td colspan="2"><br><b><?php echo $_language->module['webspell_config']; ?></b></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['admin_username']; ?>:</td>
                <td><input type="text" name="adminname" size="30">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_6']; ?></span></a></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['admin_password']; ?>:</td>
                <td><input type="password" name="adminpwd" size="30">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_7']; ?></span></a></td>
            </tr>
            <tr>
                <td><?php echo $_language->module['admin_email']; ?>:</td>
                <td><input type="text" name="adminmail" size="30">
                    <a class="ws_tooltip" href="#"><img src="images/tooltip.png" alt="">
                        <span><?php echo $_language->module['tooltip_8']; ?></span></a></td>
            </tr>
        </table>
        <input type="hidden" name="url" value="<?php echo $_POST['hp_url']; ?>">

        <?php
        } else echo '<tr>
   <td id="step" align="center" colspan="2">
   <span class="steps start">' . $_language->module['step0'] . '</span>
   <span class="steps">' . $_language->module['step1'] . '</span>
   <span class="steps">' . $_language->module['step2'] . '</span>
   <span class="steps">' . $_language->module['step3'] . '</span>
   <span class="steps">' . $_language->module['step4'] . '</span>
   <span class="steps" id="active">' . $_language->module['step5'] . '</span>
   <span class="steps end">' . $_language->module['step6'] . '</span>
   </td>
  </tr>
  <tr id="headline">
   <td colspan="2" id="title">' . $_language->module['finish_install'] . '</td>
  </tr>
  <tr>
   <td id="content" colspan="2">
	' . $_language->module['finish_next'];
        ?>

        <input type="hidden" name="installtype" value="<?php echo $_POST['installtype']; ?>">

        <div align="right"><br><a href="javascript:document.ws_install.submit()"><img src="images/next.jpg" alt=""></a>
        </div>
    </td>
</tr>
