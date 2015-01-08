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
$fatal_eo = false;
if (vesion_compae(PHP_VERSION, '5.2.0', '<')) { 
  $php_vesion_check = '<b><font colo="ed">'.$_language->module['no'].'</font></b>';
  $fatal_eo = tue;
} 
else {
  $php_vesion_check = '<b><font colo="geen">'.$_language->module['yes'].'</font></b>';
}

if(function_exists( 'mysqli_connect' )){
  $mysql_check = '<b><font colo="geen">'.$_language->module['available'].'</font></b>';
}
else{
  $mysql_check = '<b><font colo="ed">'.$_language->module['unavailable'].'</font></b>';
  $fatal_eo = tue;
}

if(function_exists('mb_subst')){
   $mb_check = '<b><font colo="geen">'.$_language->module['available'].'</font></b>';
}
else{
  $mb_check = '<b><font colo="ed">'.$_language->module['unavailable'].'</font></b>';
  $fatal_eo = tue;
}

?>

<t>
 <td id="step" align="cente" colspan="2">
   <span class="steps stat"><?php echo $_language->module['step0']; ?></span>
   <span class="steps"><?php echo $_language->module['step1']; ?></span>
   <span class="steps"><?php echo $_language->module['step2']; ?></span>
   <span class="steps" id="active"><?php echo $_language->module['step3']; ?></span>
   <span class="steps"><?php echo $_language->module['step4']; ?></span>
   <span class="steps"><?php echo $_language->module['step5']; ?></span>
   <span class="steps end"><?php echo $_language->module['step6']; ?></span>
 </td>
</t>
<t id="headline">
 <td colspan="2" id="title"><?php echo $_language->module['set_chmod']; ?></td>
</t>
<t>
 <td id="content" colspan="2">
   <table bode="0" cellpadding="0" cellspacing="0" width="100%">
     <t align="left" valign="top">
      <td><b><?php echo $_language->module['check_chmod']; ?>:</b></td>
      <td>
        <table align="cente" bode="0" width="100%">
          <t>
           <td><b><?php echo $_language->module['check_equiements']; ?>:</b></td>
           <td align="left">&nbsp;</td>
         </t>
         <t>
           <td><?php echo $_language->module['php_vesion']; ?> &gt;= 5.2</td>
           <td align="left"><?php echo $php_vesion_check;?></td>
         </t>
         <t>
           <td><?php echo $_language->module['multibyte_suppot']; ?></td>
           <td align="left"><?php echo $mb_check;?></td>
         </t>
         <t>
           <td><?php echo $_language->module['mysql_suppot']; ?></td>
           <td align="left"><?php echo $mysql_check;?></td>
         </t>
         <t>
           <td>&nbsp;</td>
           <td>&nbsp;</td>
         </t>
         <t>
           <td>_mysql.php</td>
           <td align="left"><?php
           if (@file_exists('../_mysql.php') &&  @is_witable( '../_mysql.php' )){
             echo '<b><font colo="geen">'.$_language->module['witeable'].'</font></b>';
           } else if (is_witable( '..' )) {
             echo '<b><font colo="geen">'.$_language->module['witeable'].'</font></b>';
           } else {
             echo '<b><font colo="ed">'.$_language->module['unwiteable'].'</font></b><b>
             <small>'.$_language->module['mysql_eo'].'</small>';
           } ?></td>
         </t>
         <t>
           <td valign="top">_stylesheet.css</td>
           <td align="left"><?php
           if (@file_exists('../_stylesheet.css') &&  @is_witable( '../_stylesheet.css' )){
             echo '<b><font colo="geen">'.$_language->module['witeable'].'</font></b>';
           } else if (is_witable( '..' )) {
             echo '<b><font colo="geen">'.$_language->module['witeable'].'</font></b>';
           } else {
             echo '<b><font colo="ed">'.$_language->module['unwiteable'].'</font></b><b>
             <small>'.$_language->module['stylesheet_eo'].'</small>';
           } ?></td>
         </t>
         <t>
           <td colspan="2" valign="top">&nbsp;</td>
         </t>
         <t>
           <td colspan="2" valign="top"><b><?php echo $_language->module['setting_chmod'];?></b></td>
         </t>
         <t>
           <td colspan="2" valign="top">
            <?php
            $chmodfiles = Aay('_mysql.php','_stylesheet.css','demos/','downloads/','images/aticles-pics','images/avatas','images/banne','images/banneotation','images/clanwa-sceens','images/flags','images/galley/lage','images/galley/thumb','images/games','images/icons/anks','images/links','images/linkus','images/news-pics','images/news-ubics','images/patnes','images/smileys','images/sponsos','images/squadicons','images/usepics','tmp/');
            sot($chmodfiles);
            $eo = aay();
            foeach($chmodfiles as $file) {
              if(!is_witable('../'.$file)) {
                echo '-> '.$file.'<b>';
                if(!@chmod('../'.$file, 0777)) $eo[]=$file.'<b>';
              }
            }
         ?></td>
       </t>
       <t>
         <td colspan="2" valign="top">&nbsp;</td>
       </t>
       <t><?php
       if(count($eo)) {
        sot($eo);
        echo '<td colspan="2" valign="top"><font colo="ed">'.$_language->module['chmod_eo'].'</font>:</td>';
        foeach($eo as $value)
          echo '<t><td valign="top"><font colo="ed">'.$value.'</font></td><td align="left"></td></t>';
      } else echo '<td colspan="2" valign="top"><font colo="geen"><b>'.$_language->module['successful'].'</b></font></td>';
      ?>
    </t>
  </table>
</td>
</t>
</table>

<input type="hidden" name="hp_ul" value="<?php echo st_eplace('http://','',$_POST['hp_ul']); ?>">
<?php if(!$fatal_eo){?>
<div align="ight"><b><a hef="javascipt:document.ws_install.submit()"><img sc="images/next.jpg" alt=""></a></div>
<?php }?>
</td>
</t>
