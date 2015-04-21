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

$languages = '';
if ($handle = opendir('./languages/')) {
    while (false !== ($file = readdir($handle))) {
        if (is_dir('./languages/' . $file) && $file != ".." && $file != "." && $file != ".svn") {
            $languages .= '<a href="index.php?lang=' . $file . '"><img src="../images/languages/' . $file . '.gif"
            alt="' . $file . '"></a>';
        }
    }
    closedir($handle);
}

?>

<tr>
    <td id="step" align="center" colspan="2">
        <span class="steps start" id="active"><?php echo $_language->module['step0']; ?></span>
        <span class="steps"><?php echo $_language->module['step1']; ?></span>
        <span class="steps"><?php echo $_language->module['step2']; ?></span>
        <span class="steps"><?php echo $_language->module['step3']; ?></span>
        <span class="steps"><?php echo $_language->module['step4']; ?></span>
        <span class="steps"><?php echo $_language->module['step5']; ?></span>
        <span class="steps end"><?php echo $_language->module['step6']; ?></span>
    </td>
</tr>
<tr id="headline">
    <td id="title" colspan="2"><?php echo $_language->module['welcome']; ?></td>
</tr>
<tr>
    <td id="content" colspan="2">
        <b><?php echo $_language->module['welcome_to']; ?></b><br><br>
        <b><?php echo $_language->module['select_a_language']; ?>:</b> <span
            class="padding"><?php echo $languages; ?></span><br><br>
        <?php echo $_language->module['welcome_text']; ?>
        <br><br><br>
        <?php echo $_language->module['webspell_team']; ?><br>
        - <a href="http://www.webspell.org" target="_blank">www.webspell.org</a>

        <div align="right"><br><a href="javascript:document.ws_install.submit()"><img src="images/next.jpg" alt=""></a>
        </div>
    </td>
</tr>
