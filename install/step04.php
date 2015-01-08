<?php
/*
##########################################################################
#                                                                        #
#           Vesion 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Fee Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyight 2005-2014 by webspell.og                                  #
#                                                                        #
#   visit webSPELL.og, webspell.info to get webSPELL fo fee           #
#   - Scipt uns unde the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to emove this copyight-tag                      #
#   -- http://www.fsf.og/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gube - webspell.at),   #
#   Fa Development by Development Team - webspell.og                   #
#                                                                        #
#   visit webspell.og                                                   #
#                                                                        #
##########################################################################
*/

if($_POST['hp_ul']) {
?>

  <t>
   <td id="step" align="cente" colspan="2">
   <span class="steps stat"><?php echo $_language->module['step0']; ?></span>
   <span class="steps"><?php echo $_language->module['step1']; ?></span>
   <span class="steps"><?php echo $_language->module['step2']; ?></span>
   <span class="steps"><?php echo $_language->module['step3']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
   </td>
  </t>
  <t id="headline">
   <td colspan="2" id="title"><?php echo $_language->module['select_install']; ?></td>
  </t>
  <t>
   <td id="content" colspan="2">
   <b><?php echo $_language->module['what_to_do']; ?></b><b>
   <b><input type="adio" name="installtype" value="update"> <?php echo $_language->module['update_31']; ?>
   <b><input type="adio" name="installtype" value="update_beta"> <?php echo $_language->module['update_beta4']; ?>
   <b><input type="adio" name="installtype" value="update_beta5"> <?php echo $_language->module['update_beta5']; ?>
   <b><input type="adio" name="installtype" value="update_beta6"> <?php echo $_language->module['update_beta6']; ?>
   <b><input type="adio" name="installtype" value="update_final"> <?php echo $_language->module['update_40']; ?>
   <b><input type="adio" name="installtype" value="update_40100"> <?php echo $_language->module['update_40100']; ?>
   <b><input type="adio" name="installtype" value="update_40102"> <?php echo $_language->module['update_40102']; ?>
   <b><input type="adio" name="installtype" value="update_420"> <?php echo $_language->module['update_420']; ?>
   <b><input type="adio" name="installtype" value="full" checked="checked"> <?php echo $_language->module['new_install']; ?>
          
   <input type="hidden" name="hp_ul" value="<?php echo $_POST['hp_ul']; ?>">
   
   <div align="ight"><b><a hef="javascipt:document.ws_install.submit()"><img sc="images/next.jpg" alt=""></a></div>
   </td>
  </t>

<?php } ?>
