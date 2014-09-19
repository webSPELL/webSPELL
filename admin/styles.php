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

$_language->read_module('styles');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['styles'].'</h1>';

if(isset($_POST['submit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$error = array();
		$sem = '/^#[a-fA-F0-9]{6}/';
		if(!(preg_match($sem, $_POST['page']))) $error[]=$_language->module['error_page_bg'];
		if(!(preg_match($sem, $_POST['borderc']))) $error[]=$_language->module['error_bordercolor'];
		if(!(preg_match($sem, $_POST['head']))) $error[]=$_language->module['error_head_bg'];
		if(!(preg_match($sem, $_POST['cat']))) $error[]=$_language->module['error_category_bg'];
		if(!(preg_match($sem, $_POST['bg1']))) $error[]=$_language->module['error_cell_bg1'];
		if(!(preg_match($sem, $_POST['bg2']))) $error[]=$_language->module['error_cell_bg2'];
		if(!(preg_match($sem, $_POST['bg3']))) $error[]=$_language->module['error_cell_bg3'];
		if(!(preg_match($sem, $_POST['bg4']))) $error[]=$_language->module['error_cell_bg4'];
		if(!(preg_match($sem, $_POST['win']))) $error[]=$_language->module['error_win_color'];
		if(!(preg_match($sem, $_POST['loose']))) $error[]=$_language->module['error_loose_color'];
		if(!(preg_match($sem, $_POST['draw']))) $error[]=$_language->module['error_draw_color'];
		if(count($error)) {
			
	    echo'<b>'.$_language->module['errors'].':</b><br /><ul>';
			
	    foreach($error as $err) {
				echo'<li>'.$err.'</li>';
			}
			echo'</ul><br /><input type="button" onclick="javascript:history.back()" value="'.$_language->module['back'].'" />';
		}
		else {
			safe_query("UPDATE ".PREFIX."styles SET title='".$_POST['title']."', bgpage='".$_POST['page']."', border='".$_POST['borderc']."', bghead='".$_POST['head']."', bgcat='".$_POST['cat']."', bg1='".$_POST['bg1']."', bg2='".$_POST['bg2']."', bg3='".$_POST['bg3']."', bg4='".$_POST['bg4']."', win='".$_POST['win']."', loose='".$_POST['loose']."', draw='".$_POST['draw']."' ");
			$file = ("../_stylesheet.css");
			$fp = fopen($file, "w");
			fwrite($fp, stripslashes(str_replace('\r\n', "\n", $_POST['stylesheet'])));
			fclose($fp);
			redirect("admincenter.php?site=styles","",0);
		}
	} else echo $_language->module['transaction_invalid'];
}

else {
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."styles");
	$ds=mysql_fetch_array($ergebnis);

	$file = ("../_stylesheet.css");
	$size = filesize($file);
	$fp = fopen ($file, "r");
	$stylesheet = fread($fp, $size);
	fclose($fp);
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();

	echo'<form method="post" action="admincenter.php?site=styles">
  <table width="50%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td align="right"><b>'.$_language->module['page_title'].'</b></td>
      <td width="37%"><input type="text" name="title" value="'.getinput($ds['title']).'" /></td>
      <td></td>
    </tr>
    <tr><td colspan="3"></td></tr>
    <tr>
      <td align="right"><b>'.$_language->module['page_bg'].'</b></td>
      <td><input type="text" name="page" value="'.$ds['bgpage'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['bgpage'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['bordercolor'].'</b></td>
      <td><input type="text" name="borderc" value="'.$ds['border'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['border'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['head_bg'].'</b></td>
      <td><input type="text" name="head" value="'.$ds['bghead'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['bghead'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['category_bg'].'</b></td>
      <td><input type="text" name="cat" value="'.$ds['bgcat'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['bgcat'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['cell_bg1'].'</b></td>
      <td><input type="text" name="bg1" value="'.$ds['bg1'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['bg1'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['cell_bg2'].'</b></td>
      <td><input type="text" name="bg2" value="'.$ds['bg2'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['bg2'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['cell_bg3'].'</b></td>
      <td><input type="text" name="bg3" value="'.$ds['bg3'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['bg3'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['cell_bg4'].'</b></td>
      <td><input type="text" name="bg4" value="'.$ds['bg4'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['bg4'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['win_color'].'</b></td>
      <td><input type="text" name="win" value="'.$ds['win'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['win'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['loose_color'].'</b></td>
      <td><input type="text" name="loose" value="'.$ds['loose'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['loose'].'"></td>
    </tr>
    <tr>
      <td align="right"><b>'.$_language->module['draw_color'].'</b></td>
      <td><input type="text" name="draw" value="'.$ds['draw'].'" maxlength="7" /></td>
      <td style="border: 1px solid #000000;" width="20" bgcolor="'.$ds['draw'].'"></td>
    </tr>
  </table>
  <br /><br />
  <b>'.$_language->module['stylesheet'].'</b><br /><small>'.$_language->module['stylesheet_info'].'</small><br /><br />
  <textarea name="stylesheet" rows="30" cols="" style="width: 100%;">'.$stylesheet.'</textarea>
  <br /><br />
  <input type="hidden" name="captcha_hash" value="'.$hash.'" />
  <input type="submit" name="submit" value="'.$_language->module['update'].'" />
  </form>';
}
?>