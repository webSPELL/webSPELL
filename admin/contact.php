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

$_language->read_module('contact');

if(!isuseradmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['delete'])) {
	$contactID = $_GET['contactID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."contact WHERE contactID='$contactID'");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['sortieren'])) {
	$sortcontact = $_POST['sortcontact'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(is_array($sortcontact)) {
			foreach($sortcontact as $sortstring) {
				$sorter=explode("-", $sortstring);
				safe_query("UPDATE ".PREFIX."contact SET sort='$sorter[1]' WHERE contactID='$sorter[0]' ");
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['save'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('name', 'email'))) {
			safe_query("INSERT INTO ".PREFIX."contact ( name, email, sort )
	            values( '$name', '$email', '1' )");
		} else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {
	$name = $_POST['name'];
	$email = $_POST['email'];
	$contactID = $_POST['contactID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('name', 'email'))) {
			safe_query("UPDATE ".PREFIX."contact SET name='$name', email='$email' WHERE contactID='$contactID' ");
		} else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) {
	if($_GET['action']=="add") {
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    echo'<h1>&curren; <a href="admincenter.php?site=contact" class="white">'.$_language->module['contact'].'</a> &raquo; '.$_language->module['add_contact'].'</h1>';
    
    echo '<form method="post" action="admincenter.php?site=contact" name="post">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td width="15%"><b>'.$_language->module['contact_name'].'</b></td>
        <td width="85%"><input type="text" name="name" size="60" /></td>
      </tr>
      <tr>
        <td width="15%"><b>'.$_language->module['email'].'</b></td>
        <td width="85%"><input type="text" name="email" size="60" /></td>
      </tr>
      <tr>
        <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="save" value="'.$_language->module['add_contact'].'" /></td>
      </tr>
    </table>
    </form>';
	}

	elseif($_GET['action']=="edit") {

		$contactID = (int)$_GET['contactID'];

		$ergebnis=safe_query("SELECT * FROM ".PREFIX."contact WHERE contactID='$contactID'");
		$ds=mysql_fetch_array($ergebnis);
    
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    echo'<h1>&curren; <a href="admincenter.php?site=contact" class="white">'.$_language->module['contact'].'</a> &raquo; '.$_language->module['edit_contact'].'</h1>';

    echo '<form method="post" action="admincenter.php?site=contact" name="post">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td width="15%"><b>'.$_language->module['contact_name'].'</b></td>
        <td width="85%"><input type="text" name="name" size="60" value="'.getinput($ds['name']).'" /></td>
      </tr>
      <tr>
        <td width="15%"><b>'.$_language->module['email'].'</b></td>
        <td width="85%"><input type="text" name="email" size="60" value="'.getinput($ds['email']).'" /></td>
      </tr>
      <tr>
        <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="contactID" value="'.getforminput($contactID).'" /><input type="submit" name="saveedit" value="'.$_language->module['edit_contact'].'" /></td>
      </tr>
    </table>
    </form>';
	}
}

else {
	
  echo '<h1>&curren; '.$_language->module['contact'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=contact&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_contact'].'" /><br /><br />';	

	echo'<form method="post" action="admincenter.php?site=contact">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="36%" class="title"><b>'.$_language->module['contact_name'].'</b></td>
      <td width="36%" class="title"><b>'.$_language->module['email'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
      <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
    </tr>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."contact ORDER BY sort");
	$anz=safe_query("SELECT count(contactID) FROM ".PREFIX."contact");
	$anz=mysql_result($anz, 0);

	$i=1;
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  while($ds=mysql_fetch_array($ergebnis)) {
    if($i%2) { $td='td1'; }
    else { $td='td2'; }
  
		echo'<tr>
      <td class="'.$td.'">'.getinput($ds['name']).'</td>
		<td class="'.$td.'">'.getinput($ds['email']).'</td>
      <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=contact&amp;action=edit&amp;contactID='.$ds['contactID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
      <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=contact&amp;delete=true&amp;contactID='.$ds['contactID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
      <td class="'.$td.'" align="center"><select name="sortcontact[]">';
		
    for($n=1; $n<=$anz; $n++) {
			if($ds['sort'] == $n) echo'<option value="'.$ds['contactID'].'-'.$n.'" selected="selected">'.$n.'</option>';
			else echo'<option value="'.$ds['contactID'].'-'.$n.'">'.$n.'</option>';
		}
    
		echo'</select></td>
    </tr>';
    
    $i++;
	}
	echo'<tr>
      <td class="td_head" colspan="4" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>