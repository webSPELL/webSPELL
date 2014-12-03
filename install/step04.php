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
#   Copyright 2005-2011 by webspell.org                                  #
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

if($_POST['hp_url']) {
?>

  <tr>
   <td id="step" align="center" colspan="2">
   <span class="steps start"><?php echo $_language->module['step0']; ?></span>
   <span class="steps"><?php echo $_language->module['step1']; ?></span>
   <span class="steps"><?php echo $_language->module['step2']; ?></span>
   <span class="steps"><?php echo $_language->module['step3']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
   </td>
  </tr>
  <tr id="headline">
   <td colspan="2" id="title"><?php echo $_language->module['select_install']; ?></td>
  </tr>
  <tr>
   <td id="content" colspan="2">
   <b><?php echo $_language->module['what_to_do']; ?></b><br />
   <br /><input type="radio" name="installtype" value="update" /> <?php echo $_language->module['update_31']; ?>
   <br /><input type="radio" name="installtype" value="update_beta" /> <?php echo $_language->module['update_beta4']; ?>
   <br /><input type="radio" name="installtype" value="update_beta5" /> <?php echo $_language->module['update_beta5']; ?>
   <br /><input type="radio" name="installtype" value="update_beta6" /> <?php echo $_language->module['update_beta6']; ?>
   <br /><input type="radio" name="installtype" value="update_final" /> <?php echo $_language->module['update_40']; ?>
   <br /><input type="radio" name="installtype" value="update_40100" /> <?php echo $_language->module['update_40100']; ?>
   <br /><input type="radio" name="installtype" value="update_40102" /> <?php echo $_language->module['update_40102']; ?>
   <br /><input type="radio" name="installtype" value="update_40200" /> <?php echo $_language->module['update_40200']; ?>
   <br /><input type="radio" name="installtype" value="full" checked="checked" /> <?php echo $_language->module['new_install']; ?>
          
   <input type="hidden" name="hp_url" value="<?php echo $_POST['hp_url']; ?>" />
   
   <div align="right"><br /><a href="javascript:document.ws_install.submit()"><img src="images/next.jpg" alt="" /></a></div>
   </td>
  </tr>

<?php } ?>
