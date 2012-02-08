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

if($_POST['agree'] == "1") {
	function getwspath() {
		$path=$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		return str_replace('/install/index.php','',$path);
	}

	function getwebserver($path) {
		$path=str_replace('http://','',$path);
		$server = str_replace(strstr($path,'/'),'',$path);
		if(mb_substr($server,0,3) == 'www') $server = mb_substr(strstr($server,'.'),1);
		return $server;

	}
	//version test
	$versionerror=false;
	if(phpversion()=='5.2.6') $versionerror=true;
?>

  <tr>
   <td id="step" align="center" colspan="2">
   <span class="steps start"><?php echo $_language->module['step0']; ?></span>
   <span class="steps"><?php echo $_language->module['step1']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step2']; ?></span>
   <span class="steps"><?php echo $_language->module['step3']; ?></span>
   <span class="steps"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
   </td>
  </tr>
  <tr id="headline">
   <td colspan="2" id="title"><?php if($versionerror) { echo $_language->module['error']; } else { echo $_language->module['your_site_url']; } ?></td>
  </tr>
  <tr>
   <td id="content" colspan="2">
   <?php if($versionerror) {
   	echo '<p style="color: #FF0000; font-weight: bold;">'.$_language->module['php_version'].':</p>
		<p>'.$_language->module['php_info'].'</p><br /><br />';
   } 
   else {
		echo $_language->module['enter_url'].':<br /><br />
           http://<input type="text" name="hp_url" value="'.getwspath().'" size="50" />
           <a class="tooltip" href="#"><img src="images/tooltip.png" alt="" />
           <span>'.$_language->module['tooltip'].'</span></a>
   
           <div align="right"><br /><a href="javascript:document.ws_install.submit()"><img src="images/next.jpg" alt="" /></a></div>';
   }
   ?>
   </td>
  </tr>

<?php } else{ ?>

  <tr>
   <td id="step" align="center" colspan="2">
   <span class="steps start"><?php echo $_language->module['step0']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step1']; ?></span>
   <span class="steps"><?php echo $_language->module['step2']; ?></span>
   <span class="steps"><?php echo $_language->module['step3']; ?></span>
   <span class="steps"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
   </td>
  </tr>
  <tr id="headline">
   <td colspan="2" id="title"><?php echo $_language->module['licence']; ?></td>
  </tr>
  <tr>
   <td id="content" colspan="2">
   <?php echo $_language->module['you_have_to_agree'];?>
   
   <div align="left"><br /><a href="javascript:history.back()"><img src="images/back.jpg" alt="" /></a></div>
   </td>
  </tr>

<?php } ?>
