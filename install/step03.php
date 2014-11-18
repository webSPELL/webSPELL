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
$fatal_error = false;
if (version_compare(PHP_VERSION, '5.2.0', '<')) { 
  $php_version_check = '<b><font color="red">'.$_language->module['no'].'</font></b>';
  $fatal_error = true;
} 
else {
  $php_version_check = '<b><font color="green">'.$_language->module['yes'].'</font></b>';
}

if(function_exists( 'mysqli_connect' )){
  $mysql_check = '<b><font color="green">'.$_language->module['available'].'</font></b>';
}
else{
  $mysql_check = '<b><font color="red">'.$_language->module['unavailable'].'</font></b>';
  $fatal_error = true;
}

if(function_exists('mb_substr')){
   $mb_check = '<b><font color="green">'.$_language->module['available'].'</font></b>';
}
else{
  $mb_check = '<b><font color="red">'.$_language->module['unavailable'].'</font></b>';
  $fatal_error = true;
}

?>

<tr>
 <td id="step" align="center" colspan="2">
   <span class="steps start"><?php echo $_language->module['step0']; ?></span>
   <span class="steps"><?php echo $_language->module['step1']; ?></span>
   <span class="steps"><?php echo $_language->module['step2']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step3']; ?></span>
   <span class="steps"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
 </td>
</tr>
<tr id="headline">
 <td colspan="2" id="title"><?php echo $_language->module['set_chmod']; ?></td>
</tr>
<tr>
 <td id="content" colspan="2">
   <table border="0" cellpadding="0" cellspacing="0" width="100%">
     <tr align="left" valign="top">
      <td><b><?php echo $_language->module['check_chmod']; ?>:</b></td>
      <td>
        <table align="center" border="0" width="100%">
          <tr>
           <td><b><?php echo $_language->module['check_requirements']; ?>:</b></td>
           <td align="left">&nbsp;</td>
         </tr>
         <tr>
           <td><?php echo $_language->module['php_version']; ?> &gt;= 5.2</td>
           <td align="left"><?php echo $php_version_check;?></td>
         </tr>
         <tr>
           <td><?php echo $_language->module['multibyte_support']; ?></td>
           <td align="left"><?php echo $mb_check;?></td>
         </tr>
         <tr>
           <td><?php echo $_language->module['mysql_support']; ?></td>
           <td align="left"><?php echo $mysql_check;?></td>
         </tr>
         <tr>
           <td>&nbsp;</td>
           <td>&nbsp;</td>
         </tr>
         <tr>
           <td>_mysql.php</td>
           <td align="left"><?php
           if (@file_exists('../_mysql.php') &&  @is_writable( '../_mysql.php' )){
             echo '<b><font color="green">'.$_language->module['writeable'].'</font></b>';
           } else if (is_writable( '..' )) {
             echo '<b><font color="green">'.$_language->module['writeable'].'</font></b>';
           } else {
             echo '<b><font color="red">'.$_language->module['unwriteable'].'</font></b><br>
             <small>'.$_language->module['mysql_error'].'</small>';
           } ?></td>
         </tr>
         <tr>
           <td valign="top">_stylesheet.css</td>
           <td align="left"><?php
           if (@file_exists('../_stylesheet.css') &&  @is_writable( '../_stylesheet.css' )){
             echo '<b><font color="green">'.$_language->module['writeable'].'</font></b>';
           } else if (is_writable( '..' )) {
             echo '<b><font color="green">'.$_language->module['writeable'].'</font></b>';
           } else {
             echo '<b><font color="red">'.$_language->module['unwriteable'].'</font></b><br>
             <small>'.$_language->module['stylesheet_error'].'</small>';
           } ?></td>
         </tr>
         <tr>
           <td colspan="2" valign="top">&nbsp;</td>
         </tr>
         <tr>
           <td colspan="2" valign="top"><b><?php echo $_language->module['setting_chmod'];?></b></td>
         </tr>
         <tr>
           <td colspan="2" valign="top">
            <?php
            $chmodfiles = Array('_mysql.php','_stylesheet.css','demos/','downloads/','images/articles-pics','images/avatars','images/banner','images/bannerrotation','images/clanwar-screens','images/flags','images/gallery/large','images/gallery/thumb','images/games','images/icons/ranks','images/links','images/linkus','images/news-pics','images/news-rubrics','images/partners','images/smileys','images/sponsors','images/squadicons','images/userpics','tmp/');
            sort($chmodfiles);
            $error = array();
            foreach($chmodfiles as $file) {
              if(!is_writable('../'.$file)) {
                echo '-> '.$file.'<br>';
                if(!@chmod('../'.$file, 0777)) $error[]=$file.'<br>';
              }
            }
         ?></td>
       </tr>
       <tr>
         <td colspan="2" valign="top">&nbsp;</td>
       </tr>
       <tr><?php
       if(count($error)) {
        sort($error);
        echo '<td colspan="2" valign="top"><font color="red">'.$_language->module['chmod_error'].'</font>:</td>';
        foreach($error as $value)
          echo '<tr><td valign="top"><font color="red">'.$value.'</font></td><td align="left"></td></tr>';
      } else echo '<td colspan="2" valign="top"><font color="green"><b>'.$_language->module['successful'].'</b></font></td>';
      ?>
    </tr>
  </table>
</td>
</tr>
</table>

<input type="hidden" name="hp_url" value="<?php echo str_replace('http://','',$_POST['hp_url']); ?>">
<?php if(!$fatal_error){?>
<div align="right"><br><a href="javascript:document.ws_install.submit()"><img src="images/next.jpg" alt=""></a></div>
<?php }?>
</td>
</tr>
