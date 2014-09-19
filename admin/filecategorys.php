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

$_language->read_module('filecategorys');

if(!isfileadmin($userID) or mb_substr(basename($_SERVER['REQUEST_URI']), 0, 15) != "admincenter.php") die($_language->module['access_denied']);

function generate_overview($filecats = '', $offset = '', $subcatID = 0) {

	global $_language;
	$rubrics = safe_query("SELECT * FROM ".PREFIX."files_categorys WHERE subcatID = '".$subcatID."' ORDER BY name");
		
    $i=1;
    $CAPCLASS = new Captcha;
    $CAPCLASS->create_transaction();
    $hash = $CAPCLASS->get_hash();
	    
    while($ds = mysql_fetch_array($rubrics)) {
    	if($i%2) { $td='td1'; }
		else { $td='td2'; }
				
		$filecats .= '<tr>
        <td class="'.$td.'">'.$offset.getinput($ds['name']).'</td>
        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=filecategorys&amp;action=edit&amp;filecatID='.$ds['filecatID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=filecategorys&amp;delete=true&amp;filecatID='.$ds['filecatID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
    	</tr>';
	      
      	$i++;
	
		if(mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."files_categorys WHERE subcatID = '".$ds['filecatID']."'"))) {
			$filecats .= generate_overview("", $offset.getinput($ds['name'])." &raquo; ", $ds['filecatID']);
    	}
	}
	
	return $filecats;
}

function delete_category($filecat){
	$rubrics = safe_query("SELECT filecatID FROM ".PREFIX."files_categorys WHERE subcatID = '".$filecat."' ORDER BY name");
	if(mysql_num_rows($rubrics)){
		while($ds = mysql_fetch_assoc($rubrics)){
			delete_category($ds['filecatID']);
		}
	}
	safe_query("DELETE FROM ".PREFIX."files_categorys WHERE filecatID='".$filecat."'");
	$files = safe_query("SELECT * FROM ".PREFIX."files WHERE filecatID='".$filecat."'");
	while($ds = mysql_fetch_array($files)) {
		if(stristr($ds['file'],"http://") or stristr($ds['file'],"ftp://")) @unlink('../downloads/'.$ds['file']);
	}
	safe_query("DELETE FROM ".PREFIX."files WHERE filecatID='".$filecat."'");
}

/* start processing */
	
if(isset($_POST['save'])) {
 	if(mb_strlen($_POST['name'])>0){
 	 	$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
			safe_query("INSERT INTO ".PREFIX."files_categorys ( name, subcatID ) values( '".$_POST['name']."', '".$_POST['subcat']."' ) ");
		} else echo $_language->module['transaction_invalid'];
	}
	else{
	 	redirect("admincenter.php?site=filecategorys&amp;action=add", $_language->module['enter_name'], 3);
	}
}

elseif(isset($_POST['saveedit'])) {
 	if(mb_strlen($_POST['name'])>0){
	 	$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
			safe_query("UPDATE ".PREFIX."files_categorys SET name='".$_POST['name']."', subcatID = '".$_POST['subcat']."' WHERE filecatID='".$_POST['filecatID']."'");
		} else echo $_language->module['transaction_invalid'];
	}
	else{
	 	redirect("admincenter.php?site=filecategorys&amp;action=edit&amp;filecatID=".$_POST['filecatID'], $_language->module['enter_name'], 3);
	}
}

elseif(isset($_GET['delete'])) {
	$filecatID = $_GET['filecatID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		delete_category($filecatID);
	} else echo $_language->module['transaction_invalid'];
}

if(!isset($_GET['action'])) {
	$_GET['action'] = '';
}

if($_GET['action']=="add") {
	
	function generate_options($filecats = '', $offset = '', $subcatID = 0) {
		$rubrics = safe_query("SELECT * FROM ".PREFIX."files_categorys WHERE subcatID = '".$subcatID."' ORDER BY name");
		while($dr = mysql_fetch_array($rubrics)) {
			$filecats .= '<option value="'.$dr['filecatID'].'">'.$offset.getinput($dr['name']).'</option>';
			if(mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."files_categorys WHERE subcatID = '".$dr['filecatID']."'"))) {
				$filecats .= generate_options("", $offset."- ", $dr['filecatID']);
			}
		}
		return $filecats;
	}
	$filecats = generate_options('<option value="0">'.$_language->module['main'].'</option>', '- ');
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
  
  echo'<h1>&curren; <a href="admincenter.php?site=filecategorys" class="white">'.$_language->module['file_categories'].'</a> &raquo; '.$_language->module['add_category'].'</h1>';
  
	echo'<form method="post" action="admincenter.php?site=filecategorys">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['category_name'].'</b></td>
      <td width="85%"><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['sub_category'].'</b></td>
      <td><select name="subcat">'.$filecats.'</select></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_category'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($_GET['action']=="edit") {

	$filecatID = $_GET['filecatID'];
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."files_categorys WHERE filecatID='$filecatID'");
	$ds=mysql_fetch_array($ergebnis);

	function generate_options($filecats = '', $offset = '', $subcatID = 0) {
	
		global $filecatID;
		$rubrics = safe_query("SELECT * FROM ".PREFIX."files_categorys WHERE subcatID = '".$subcatID."' AND (filecatID !='".$filecatID."' AND subcatID !='".$filecatID."')  ORDER BY name");
		while($dr = mysql_fetch_array($rubrics)) {
			$filecats .= '<option value="'.$dr['filecatID'].'">'.$offset.getinput($dr['name']).'</option>';
			if(mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."files_categorys WHERE subcatID = '".$dr['filecatID']."'"))) {
				$filecats .= generate_options("", $offset."- ", $dr['filecatID']);
			}
		}
		return $filecats;
	}
	
	$filecats = generate_options('<option value="0">'.$_language->module['main'].'</option>', '- ');
	
	$filecats = str_replace('value="'.$ds['subcatID'].'"', 'value="'.$ds['subcatID'].'" selected="selected"', $filecats);
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo'<h1>&curren; <a href="admincenter.php?site=filecategorys" class="white">'.$_language->module['file_categories'].'</a> &raquo; '.$_language->module['edit_category'].'</h1>';
  
  echo'<form method="post" action="admincenter.php?site=filecategorys" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['category_name'].'</b></td>
      <td width="85%"><input type="text" name="name" size="60" value="'.getinput($ds['name']).'" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['sub_category'].'</b></td>
      <td><select name="subcat">'.$filecats.'</select></td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="filecatID" value="'.$ds['filecatID'].'" /></td>
      <td><input type="submit" name="saveedit" value="'.$_language->module['edit_category'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {
	
  echo'<h1>&curren; '.$_language->module['file_categories'].'</h1>';
  
  echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=filecategorys&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_category'].'" /><br /><br />';

	echo'<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="80%" class="title"><b>'.$_language->module['category_name'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
    </tr>';

	$overview = generate_overview();
	echo $overview;

	echo'</table>';
}
?>