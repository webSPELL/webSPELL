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

$_language->read_module('faq');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['delete'])) {
	$faqID = $_GET['faqID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query(" DELETE FROM ".PREFIX."faq WHERE faqID='$faqID' ");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['sortieren'])) {
	$sortfaq = $_POST['sortfaq'];
	
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(is_array($sortfaq)) {
			foreach($sortfaq as $sortstring) {
				$sorter=explode("-", $sortstring);
				safe_query("UPDATE ".PREFIX."faq SET sort='$sorter[1]' WHERE faqID='$sorter[0]' ");
			}
		}
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['save'])) {
	$faqcat = $_POST['faqcat'];
	$question = $_POST['question'];
	$answer = $_POST['message'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('question', 'message'))) {
			if($faqcat=="") {
				redirect('admincenter.php?site=faq',$_language->module['no_faq_selected'], 3);
				exit;
			}
			safe_query("INSERT INTO ".PREFIX."faq ( faqcatID, date, question, answer, sort ) values( '$faqcat', '".time()."', '$question', '$answer', '1' )");
		} else echo $_language->module['information_incomplete'];
    } else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {
	$faqcat = $_POST['faqcat'];
	$question = $_POST['question'];
	$answer = $_POST['message'];
	$faqID = $_POST['faqID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		if(checkforempty(Array('question', 'message'))) {
			safe_query("UPDATE ".PREFIX."faq SET faqcatID='$faqcat', date='".time()."', question='$question', answer='$answer' WHERE faqID='$faqID' ");
		} else echo $_language->module['information_incomplete'];
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) {
	if($_GET['action']=="add") {
		$ergebnis=safe_query("SELECT * FROM ".PREFIX."faq_categories ORDER BY sort");
		$faqcats='<select name="faqcat">';
		while($ds=mysql_fetch_array($ergebnis)) {
			$faqcats.='<option value="'.$ds['faqcatID'].'">'.getinput($ds['faqcatname']).'</option>';
		}
		$faqcats.='</select>';

		if(isset($_GET['answer'])) {
			echo '<font color="red">'.$_language->module['no_category_selected'].'</font>';
			$question = $_GET['question'];
			$answer = $_GET['answer'];
		}
		else {
			$question = "";
			$answer = "";
		}
    
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    $_language->read_module('bbcode', true);
    
    eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
    eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
    
		echo'<h1>&curren; <a href="admincenter.php?site=faq" class="white">'.$_language->module['faq'].'</a> &raquo; '.$_language->module['add_faq'].'</h1>';
    
		echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
		
    echo'<form method="post" id="post" name="post" action="admincenter.php?site=faq" onsubmit="return chkFormular();">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td width="15%"><b>'.$_language->module['category'].'</b></td><td width="85%">'.$faqcats.'</td>
      </tr>
      <tr>
        <td><b>'.$_language->module['faq'].'</b></td><td><input type="text" name="question" value="'.$question.'" size="97" />
        </td>
      </tr>
      <tr>
        <td colspan="2"><b>'.$_language->module['answer'].'</b><br />
          <table width="99%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td valign="top">'.$addbbcode.'</td>
			        <td valign="top">'.$addflags.'</td>
			      </tr>
			    </table>
          <br /><textarea id="message" rows="10" cols="" name="message" style="width: 100%;">'.$answer.'</textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><br /><input type="submit" name="save" value="'.$_language->module['add_faq'].'" /></td>
      </tr>
    </table>
    </form>';
	}

	elseif($_GET['action']=="edit") {

		$faqID = $_GET['faqID'];

		$ergebnis=safe_query("SELECT * FROM ".PREFIX."faq WHERE faqID='$faqID'");
		$ds=mysql_fetch_array($ergebnis);

		$faqcategory=safe_query("SELECT * FROM ".PREFIX."faq_categories ORDER BY sort");
		$faqcats='<select name="faqcat">';
		while($dc=mysql_fetch_array($faqcategory)) {
			$selected='';
			if($dc['faqcatID'] == $ds['faqcatID']) $selected=' selected="selected"';
			$faqcats.='<option value="'.$dc['faqcatID'].'"'.$selected.'>'.getinput($dc['faqcatname']).'</option>';
		}
		$faqcats.='</select>';
    
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
    
    $_language->read_module('bbcode', true);
    
    eval ("\$addbbcode = \"".gettemplate("addbbcode", "html", "admin")."\";");
    eval ("\$addflags = \"".gettemplate("flags_admin", "html", "admin")."\";");
    
		echo'<h1>&curren; <a href="admincenter.php?site=faq" class="white">'.$_language->module['faq'].'</a> &raquo; '.$_language->module['edit_faq'].'</h1>';
    
		echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
		
    echo '<form method="post" id="post" name="post" action="admincenter.php?site=faq" onsubmit="return chkFormular();">
    <table width="100%" border="0" cellspacing="1" cellpadding="3">
      <tr>
        <td width="15%"><b>'.$_language->module['category'].'</b></td>
        <td width="85%">'.$faqcats.'</td>
      </tr>
      <tr>
        <td><b>'.$_language->module['faq'].'</b></td>
        <td><input type="text" name="question" value="'.getinput($ds['question']).'" size="97" /></td>
      </tr>
      <tr>
        <td colspan="2"><b>'.$_language->module['answer'].'</b>
          <table width="99%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td valign="top">'.$addbbcode.'</td>
			        <td valign="top">'.$addflags.'</td>
			      </tr>
			    </table>
          <textarea id="message" rows="10" cols="" name="message" style="width: 100%;">'.getinput($ds['answer']).'</textarea>
        </td>
      </tr>
      <tr>
        <td colspan="2"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="faqID" value="'.$faqID.'" /><input type="submit" name="saveedit" value="'.$_language->module['edit_faq'].'" /></td>
      </tr>
    </table>
    </form>';
	}
}

else {
	
  echo '<h1>&curren; '.$_language->module['faq'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=faq&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_faq'].'" /><br /><br />';	

	echo'<form method="post" action="admincenter.php?site=faq">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="72%" class="title"><b>'.$_language->module['faq'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
      <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
    </tr>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."faq_categories ORDER BY sort");
	$anz=safe_query("SELECT count(faqcatID) FROM ".PREFIX."faq_categories");
	$anz=mysql_result($anz, 0);
  
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  while($ds=mysql_fetch_array($ergebnis)) {
		echo'<tr>
      <td class="td_head" colspan="3"><b>'.$ds['faqcatname'].'</b>
      <br /><small>'.cleartext($ds['description'],1,'admin').'</small></td>
    </tr>';		 

		$faq=safe_query("SELECT * FROM ".PREFIX."faq WHERE faqcatID='$ds[faqcatID]' ORDER BY sort");
		$anzfaq=safe_query("SELECT count(faqID) FROM ".PREFIX."faq WHERE faqcatID='$ds[faqcatID]'");
		$anzfaq=mysql_result($anzfaq, 0);

		$i=1;
    while($db=mysql_fetch_array($faq)) {
      if($i%2) { $td='td1'; }
      else { $td='td2'; }
      
			echo'<tr>
        <td class="'.$td.'"><b>- '.getinput($db['question']).'</b></td>
        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=faq&amp;action=edit&amp;faqID='.$db['faqID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=faq&amp;delete=true&amp;faqID='.$db['faqID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
        <td class="'.$td.'" align="center"><select name="sortfaq[]">';
        
			for($j=1; $j<=$anzfaq; $j++) {
				if($db['sort'] == $j) echo'<option value="'.$db['faqID'].'-'.$j.'" selected="selected">'.$j.'</option>';
				else echo'<option value="'.$db['faqID'].'-'.$j.'">'.$j.'</option>';
			}
			echo'</select></td>
      </tr>';
      
      $i++;
		}
	}

	echo'<tr>
      <td class="td_head" colspan="3" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>