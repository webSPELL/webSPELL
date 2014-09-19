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

$_language->read_module('faqcategories');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['delcat'])) {
	$faqcatID = $_GET['faqcatID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."faq WHERE faqcatID='$faqcatID'");
		safe_query("DELETE FROM ".PREFIX."faq_categories WHERE faqcatID='$faqcatID'");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['sortieren'])) {
	$sortfaqcat = $_POST['sortfaqcat'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(is_array($sortfaqcat)) {
			foreach($sortfaqcat as $sortstring) {
				$sorter=explode("-", $sortstring);
				safe_query("UPDATE ".PREFIX."faq_categories SET sort='$sorter[1]' WHERE faqcatID='$sorter[0]' ");
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['savecat'])) {
	$faqcatname = $_POST['faqcatname'];
	$description = $_POST['message'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('faqcatname'))) safe_query("INSERT INTO ".PREFIX."faq_categories ( faqcatname, description, sort ) values( '$faqcatname', '$description', '1' )");
		else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveeditcat'])) {
	$faqcatname = $_POST['faqcatname'];
	$description = $_POST['message'];
	$faqcatID = $_POST['faqcatID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('faqcatname'))) safe_query("UPDATE ".PREFIX."faq_categories SET faqcatname='$faqcatname', description='$description' WHERE faqcatID='$faqcatID' ");
		else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) {
	if($_GET['action']=="addcat") {
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    $_language->read_module('bbcode', true);
    
    eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
    eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
    
    echo'<h1>&curren; <a href="admincenter.php?site=faqcategories" class="white">'.$_language->module['faq_categories'].'</a> &raquo; '.$_language->module['add_category'].'</h1>';
    
    echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
    
    echo '<form method="post" action="admincenter.php?site=faqcategories" id="post" name="post" enctype="multipart/form-data" onsubmit="return chkFormular();">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td width="15%"><b>'.$_language->module['category_name'].'</b></td>
        <td width="85%"><input type="text" name="faqcatname" size="60" /></td>
      </tr>
      <tr>
        <td colspan="2"><b>'.$_language->module['description'].'</b>
	        <table width="99%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td valign="top">'.$addbbcode.'</td>
			        <td valign="top">'.$addflags.'</td>
			      </tr>
			    </table>
	        <br /><textarea id="message" rows="10" cols="" name="message" style="width: 100%;"></textarea>
	      </td>
      </tr>
      <tr>
        <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="savecat" value="'.$_language->module['add_category'].'" /></td>
      </tr>
    </table>
    </form>';
	}

	elseif($_GET['action']=="editcat") {

		$faqcatID = $_GET['faqcatID'];

		$ergebnis=safe_query("SELECT * FROM ".PREFIX."faq_categories WHERE faqcatID='$faqcatID'");
		$ds=mysql_fetch_array($ergebnis);
    
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    $_language->read_module('bbcode', true);
    
    eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
    eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
    
    echo'<h1>&curren; <a href="admincenter.php?site=faqcategories" class="white">'.$_language->module['faq_categories'].'</a> &raquo; '.$_language->module['edit_category'].'</h1>';

    echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
    
    echo '<form method="post" action="admincenter.php?site=faqcategories" id="post" name="post" onsubmit="return chkFormular();">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td width="15%"><b>'.$_language->module['category_name'].'</b></td>
        <td width="85%"><input type="text" name="faqcatname" size="60" value="'.getinput($ds['faqcatname']).'" /></td>
      </tr>
      <tr>
        <td colspan="2"><b>'.$_language->module['description'].'</b>
	        <table width="99%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td valign="top">'.$addbbcode.'</td>
			        <td valign="top">'.$addflags.'</td>
			      </tr>
			    </table>
	        <br /><textarea id="message" rows="10" cols="" name="message" style="width: 100%;">'.getinput($ds['description']).'</textarea>
	      </td>
      </tr>
      <tr>
        <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="faqcatID" value="'.$faqcatID.'" /><input type="submit" name="saveeditcat" value="'.$_language->module['edit_category'].'" /></td>
      </tr>
    </table>
    </form>';
	}
}

else {
	
  echo '<h1>&curren; '.$_language->module['faq_categories'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=faqcategories&amp;action=addcat\');return document.MM_returnValue" value="'.$_language->module['new_category'].'" /><br /><br />';	

	echo'<form method="post" action="admincenter.php?site=faqcategories">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="72%" class="title"><b>'.$_language->module['faq_categories'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
      <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
    </tr>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."faq_categories ORDER BY sort");
	$anz=safe_query("SELECT count(faqcatID) FROM ".PREFIX."faq_categories");
	$anz=mysql_result($anz, 0);

	$i=1;
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  while($ds=mysql_fetch_array($ergebnis)) {
    if($i%2) { $td='td1'; }
    else { $td='td2'; }
  
		echo'<tr>
      <td class="'.$td.'"><b>'.getinput($ds['faqcatname']).'</b>
      <br />'.cleartext($ds['description'],1,'admin').'</td>
      <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=faqcategories&amp;action=editcat&amp;faqcatID='.$ds['faqcatID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
      <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=faqcategories&amp;delcat=true&amp;faqcatID='.$ds['faqcatID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
      <td class="'.$td.'" align="center"><select name="sortfaqcat[]">';
		
    for($n=1; $n<=$anz; $n++) {
			if($ds['sort'] == $n) echo'<option value="'.$ds['faqcatID'].'-'.$n.'" selected="selected">'.$n.'</option>';
			else echo'<option value="'.$ds['faqcatID'].'-'.$n.'">'.$n.'</option>';
		}
    
		echo'</select></td>
    </tr>';
    
    $i++;
	}
	echo'<tr>
      <td class="td_head" colspan="3" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>