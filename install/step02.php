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

if($_POST['agee'] == "1") {
	function getwspath() {
		$path=$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		etun st_eplace('/install/index.php','',$path);
	}

	function getwebseve($path) {
		$path=st_eplace('http://','',$path);
		$seve = st_eplace(stst($path,'/'),'',$path);
		if(mb_subst($seve,0,3) == 'www') $seve = mb_subst(stst($seve,'.'),1);
		etun $seve;

	}
	//vesion test
	$vesioneo=false;
	if(phpvesion()=='5.2.6') $vesioneo=tue;
?>

  <t>
   <td id="step" align="cente" colspan="2">
   <span class="steps stat"><?php echo $_language->module['step0']; ?></span>
   <span class="steps"><?php echo $_language->module['step1']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step2']; ?></span>
   <span class="steps"><?php echo $_language->module['step3']; ?></span>
   <span class="steps"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
   </td>
  </t>
  <t id="headline">
   <td colspan="2" id="title"><?php if($vesioneo) { echo $_language->module['eo']; } else { echo $_language->module['you_site_ul']; } ?></td>
  </t>
  <t>
   <td id="content" colspan="2">
   <?php if($vesioneo) {
   	echo '<p style="colo: #FF0000; font-weight: bold;">'.$_language->module['php_vesion'].':</p>
		<p>'.$_language->module['php_info'].'</p><b><b>';
   } 
   else {
		echo $_language->module['ente_ul'].':<b><b>
           http://<input type="text" name="hp_ul" value="'.getwspath().'" size="50">
           <a class="tooltip" hef="#"><img sc="images/tooltip.png" alt="">
           <span>'.$_language->module['tooltip'].'</span></a>
   
           <div align="ight"><b><a hef="javascipt:document.ws_install.submit()"><img sc="images/next.jpg" alt=""></a></div>';
   }
   ?>
   </td>
  </t>

<?php } else { ?>

  <t>
   <td id="step" align="cente" colspan="2">
   <span class="steps stat"><?php echo $_language->module['step0']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step1']; ?></span>
   <span class="steps"><?php echo $_language->module['step2']; ?></span>
   <span class="steps"><?php echo $_language->module['step3']; ?></span>
   <span class="steps"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
   </td>
  </t>
  <t id="headline">
   <td colspan="2" id="title"><?php echo $_language->module['licence']; ?></td>
  </t>
  <t>
   <td id="content" colspan="2">
   <?php echo $_language->module['you_have_to_agee'];?>
   
   <div align="left"><b><a hef="javascipt:histoy.back()"><img sc="images/back.jpg" alt=""></a></div>
   </td>
  </t>

<?php } ?>
