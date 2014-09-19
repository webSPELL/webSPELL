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

$_language->read_module('boards');

if(!isforumadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_POST['savemods'])) {
	$boardID = $_POST['boardID'];
	if(isset($_POST['mods'])){
		$mods = $_POST['mods'];
		$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
			safe_query("DELETE FROM ".PREFIX."forum_moderators WHERE boardID='$boardID'");
			if(is_array($mods)) {
				foreach($mods as $id) {
					safe_query("INSERT INTO ".PREFIX."forum_moderators (boardID, userID) values ('$boardID', '$id') ");
				}
			}
		} 
		else {
			echo $_language->module['transaction_invalid'];
		}
	}
	else{
		$CAPCLASS = new Captcha;
		if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
			safe_query("DELETE FROM ".PREFIX."forum_moderators WHERE boardID='$boardID'");
		}
		else {
			echo $_language->module['transaction_invalid'];
		}
	}
}

elseif(isset($_GET['delete'])) {
	$boardID = $_GET['boardID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."forum_posts WHERE boardID='$boardID' ");
		safe_query("
			DELETE `topics`.*, `moved`.* 
			FROM `".PREFIX."forum_topics` AS `topics` 
			LEFT JOIN `".PREFIX."forum_topics` AS `moved` ON (`topics`.`topicID` = `moved`.`moveID`)
			WHERE `topics`.`boardID` = '".$boardID."'");
		safe_query("DELETE FROM ".PREFIX."forum_boards WHERE boardID='$boardID' ");
		safe_query("DELETE FROM ".PREFIX."forum_moderators WHERE boardID='$boardID' ");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET['delcat'])) {
	$catID = $_GET['catID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."forum_boards SET category='0' WHERE category='$catID' ");
		safe_query("DELETE FROM ".PREFIX."forum_categories WHERE catID='$catID' ");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['sortieren'])) {
	$sortcat = $_POST['sortcat'];
	$sortboards = $_POST['sortboards'];
  if(isset($_POST["hideboards"])) $hideboards = $_POST['hideboards'];
  else $hideboards="";

	if(is_array($sortcat)) {
		foreach($sortcat as $sortstring) {
			$sorter=explode("-", $sortstring);
			safe_query("UPDATE ".PREFIX."forum_categories SET sort='$sorter[1]' WHERE catID='$sorter[0]' ");
		}
	}
	if(is_array($sortboards)) {
		foreach($sortboards as $sortstring) {
			$sorter=explode("-", $sortstring);
			safe_query("UPDATE ".PREFIX."forum_boards SET sort='$sorter[1]' WHERE boardID='$sorter[0]' ");
		}
	}
}

elseif(isset($_POST['save'])) {
	$kath = $_POST['kath'];
	$name = $_POST['name'];
	$boardinfo = $_POST['boardinfo'];
	if(isset($_POST['readgrps'])) $readgrps = implode(";", $_POST['readgrps']);
	else $readgrps='';
	if(isset($_POST['writegrps'])) $writegrps = implode(";", $_POST['writegrps']);
	else $writegrps='';

	if($kath=="") $kath=0;
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
  		safe_query("INSERT INTO ".PREFIX."forum_boards ( category, name, info, readgrps, writegrps, sort )
  values( '$kath', '$name', '$boardinfo', '$readgrps', '$writegrps', '1' )");
  	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['savecat'])) {
	$catname = $_POST['catname'];
	$catinfo = $_POST['catinfo'];
	if(isset($_POST['readgrps'])) $readgrps = implode(";", $_POST['readgrps']);
	else $readgrps='';
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("INSERT INTO ".PREFIX."forum_categories ( readgrps, name, info, sort ) values( '$readgrps', '$catname', '$catinfo', '1' )");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {
	$kath = $_POST['kath'];
	$name = $_POST['name'];
	$boardinfo = $_POST['boardinfo'];
	$boardID = $_POST['boardID'];
	if(isset($_POST['readgrps'])) $readgrps = implode(";", $_POST['readgrps']);
	else $readgrps='';
	if(isset($_POST['writegrps'])) $writegrps = implode(";", $_POST['writegrps']);
	else $writegrps='';
	
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."forum_boards SET category='$kath',
		           name='$name',
	             info='$boardinfo',
	             readgrps='$readgrps',
	             writegrps='$writegrps' WHERE boardID='$boardID'");
		safe_query("UPDATE ".PREFIX."forum_topics SET readgrps='$readgrps', writegrps='$writegrps' WHERE boardID='$boardID'");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveeditcat'])) {
	$catname = $_POST['catname'];
	$catinfo = $_POST['catinfo'];
	$catID = $_POST['catID'];
	if(isset($_POST['readgrps'])) $readgrps = implode(";", $_POST['readgrps']);
	else $readgrps='';
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."forum_categories SET readgrps='$readgrps', name='$catname', info='$catinfo' WHERE catID='$catID' ");
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="mods") {

	echo'<h1>&curren; <a href="admincenter.php?site=boards" class="white">'.$_language->module['boards'].'</a> &raquo; '.$_language->module['moderators'].'</h1>';
  
  $boardID = $_GET['boardID'];

	$moderators=safe_query("SELECT * FROM ".PREFIX."user_groups WHERE moderator='1'");
	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE boardID='$boardID'");
	$ds=mysql_fetch_array($ergebnis);
	
  echo $_language->module['choose_moderators'].' <b>'.$ds['name'].'</b><br /><br />';
  
	echo'<form method="post" action="admincenter.php?site=boards">
  <select name="mods[]" multiple="multiple" size="10">';

	while($dm=mysql_fetch_array($moderators)) {
		$nick=getnickname($dm['userID']);
		$ismod=mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."forum_moderators WHERE boardID='$boardID' AND userID='".$dm['userID']."'"));
		if($ismod) echo'<option value="'.$dm['userID'].'" selected="selected">'.$nick.'</option>';
		else echo'<option value="'.$dm['userID'].'">'.$nick.'</option>';
	}
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();

	echo'</select><br /><br />
	<input type="hidden" name="captcha_hash" value="'.$hash.'" />
  <input type="hidden" name="boardID" value="'.$boardID.'" />
  <input type="submit" name="savemods" value="'.$_language->module['select_moderators'].'" />
  </form>';
}

elseif($action=="add") {
	
  echo'<h1>&curren; <a href="admincenter.php?site=boards" class="white">'.$_language->module['boards'].'</a> &raquo; '.$_language->module['add_board'].'</h1>';
  
  $ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_categories ORDER BY sort");
	$cats='<select name="kath">';
	while($ds=mysql_fetch_array($ergebnis)) {
		$cats.='<option value="'.$ds['catID'].'">'.getinput($ds['name']).'</option>';
	}
	$cats.='</select>';

	$sql=safe_query("SELECT * FROM ".PREFIX."forum_groups");
	$groups='';
	while($db=mysql_fetch_array($sql)) {
		$groups.='<option value="'.$db['fgrID'].'">'.getinput($db['name']).'</option>';
	}
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	echo '<script language="javascript" type="text/javascript">
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form method="post" action="admincenter.php?site=boards">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['category'].'</b></td>
      <td width="85%">'.$cats.'</td>
    </tr>
    <tr>
      <td><b>'.$_language->module['boardname'].'</b></td>
      <td><input type="text" name="name" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['boardinfo'].'</b></td>
      <td><input type="text" name="boardinfo" size="60" /></td>
    </tr>
    <tr>
      <td valign="top"><b>'.$_language->module['read_right'].'</b></td>
      <td><select id="readgrps" name="readgrps[]" multiple="multiple" size="10">
        <option value="user">'.$_language->module['registered_users'].'</option>
        '.$groups.'
      </select><br />
      <a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['read_right_info_board'].'</td>
    </tr>
    <tr>
      <td valign="top"><b>'.$_language->module['write_right'].'</b></td>
      <td><select id="writegrps" name="writegrps[]" multiple="multiple" size="10">
        <option value="user" selected="selected">'.$_language->module['registered_users'].'</option>
        '.$groups.'
      </select><br />
      <a href="javascript:unselect_all(\'writegrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['write_right_info_board'].'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="save" value="'.$_language->module['add_board'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="edit") {

  echo'<h1>&curren; <a href="admincenter.php?site=boards" class="white">'.$_language->module['boards'].'</a> &raquo; '.$_language->module['edit_board'].'</h1>';
  
  $boardID = $_GET['boardID'];

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE boardID='$boardID'");
	$ds=mysql_fetch_array($ergebnis);

	$category=safe_query("SELECT * FROM ".PREFIX."forum_categories ORDER BY sort");
	$cats='<select name="kath">';
	while($dc=mysql_fetch_array($category)) {
		if($ds['category']==$dc['catID']) $selected=" selected=\"selected\"";
		else $selected="";
		$cats.='<option value="'.$dc['catID'].'"'.$selected.'>'.getinput($dc['name']).'</option>';
	}
	$cats.='</select>';

	$groups=array();
	$sql=safe_query("SELECT * FROM ".PREFIX."forum_groups");
	while($db=mysql_fetch_array($sql)) {
		$groups[$db['fgrID']] = $db['name'];
	}

	$readgrps='';
	$writegrps='';

	$grps = explode(";", $ds['readgrps']);
	if(in_array('user', $grps)) $readgrps .= '<option value="user" selected="selected">'.$_language->module['registered_users'].'</option>';
	else $readgrps .= '<option value="user">'.$_language->module['registered_users'].'</option>';
	foreach($groups as $fgrID => $name) {
		if(in_array($fgrID, $grps)) $selected=' selected="selected"';
		else $selected = '';
		$readgrps .= '<option value="'.$fgrID.'"'.$selected.'>'.getinput($name).'</option>';
	}

	$grps = explode(";", $ds['writegrps']);
	if(in_array('user', $grps)) $writegrps .= '<option value="user" selected="selected">'.$_language->module['registered_users'].'</option>';
	else $writegrps .= '<option value="user">'.$_language->module['registered_users'].'</option>';
	foreach($groups as $fgrID => $name) {
		if(in_array($fgrID, $grps)) $selected=' selected="selected"';
		else $selected = '';
		$writegrps .= '<option value="'.$fgrID.'"'.$selected.'>'.getinput($name).'</option>';
	}
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo '<script language="javascript" type="text/javascript">
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form method="post" action="admincenter.php?site=boards">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['category'].'</b></td>
      <td width="85%">'.$cats.'</td>
    </tr>
    <tr>
      <td><b>'.$_language->module['boardname'].'</b></td>
      <td><input type="text" name="name" value="'.getinput($ds['name']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['boardinfo'].'</b></td>
      <td><input type="text" name="boardinfo" value="'.getinput($ds['info']).'" size="60" /></td>
    </tr>
    <tr>
      <td valign="top"><b>'.$_language->module['read_right'].'</b></td>
      <td><select id="readgrps" name="readgrps[]" multiple="multiple" size="10">'.$readgrps.'</select><br />
      <a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['read_right_info_board'].'</td>
    </tr>
    <tr>
      <td valign="top"><b>'.$_language->module['write_right'].'</b></td>
      <td><select id="writegrps" name="writegrps[]" multiple="multiple" size="10">'.$writegrps.'</select><br />
      <a href="javascript:unselect_all(\'writegrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['write_right_info_board'].'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="boardID" value="'.$boardID.'" /></td>
      <td><input type="submit" name="saveedit" value="'.$_language->module['edit_board'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="addcat") {

  echo'<h1>&curren; <a href="admincenter.php?site=boards" class="white">'.$_language->module['boards'].'</a> &raquo; '.$_language->module['add_category'].'</h1>';
	
  $sql = safe_query("SELECT * FROM ".PREFIX."forum_groups");
	$groups = '<select id="readgrps" name="readgrps[]" multiple="multiple" size="10">
  <option value="user">'.$_language->module['registered_users'].'</option>';
	while($db = mysql_fetch_array($sql)) {
		$groups .= '<option value="'.$db['fgrID'].'">'.getinput($db['name']).'</option>';
	}
	$groups .= '</select>';
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	echo '<script language="javascript" type="text/javascript">
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form method="post" action="admincenter.php?site=boards" enctype="multipart/form-data">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['category_name'].'</b></td>
      <td width="85%"><input type="text" name="catname" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['category_info'].'</b></td>
      <td><input type="text" name="catinfo" size="60" /></td>
    </tr>
    <tr>
      <td valign="top"><b>'.$_language->module['read_right'].'</b></td>
      <td>'.$groups.'<br />
      <a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['right_info_category'].'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
      <td><input type="submit" name="savecat" value="'.$_language->module['add_category'].'" /></td>
    </tr>
  </table>
  </form>';
}

elseif($action=="editcat") {

  echo'<h1>&curren; <a href="admincenter.php?site=boards" class="white">'.$_language->module['boards'].'</a> &raquo; '.$_language->module['edit_category'].'</h1>';

	$catID = $_GET['catID'];

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_categories WHERE catID='$catID'");
	$ds=mysql_fetch_array($ergebnis);

	$usergrps = explode(";", $ds['readgrps']);
	$sql=safe_query("SELECT * FROM ".PREFIX."forum_groups");
	$groups='<select id="readgrps" name="readgrps[]" multiple="multiple" size="10">';
	if(in_array('user', $usergrps)) $groups.='<option value="user" selected="selected">'.$_language->module['registered_users'].'</option>';
	else $groups.='<option value="user">'.$_language->module['registered_users'].'</option>';
	while($db=mysql_fetch_array($sql)) {
		if(in_array($db['fgrID'], $usergrps)) $selected=' selected="selected"';
		else $selected='';
		$groups.='<option value="'.$db['fgrID'].'" '.$selected.'>'.getinput($db['name']).'</option>';
	}
	$groups.='</select>';
  $CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	echo '<script language="javascript" type="text/javascript">
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form method="post" action="admincenter.php?site=boards">
  <table width="100%" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td width="15%"><b>'.$_language->module['category_name'].'</b></td>
      <td width="85%"><input type="text" name="catname" value="'.getinput($ds['name']).'" size="60" /></td>
    </tr>
    <tr>
      <td><b>'.$_language->module['category_info'].'</b></td>
      <td><input type="text" name="catinfo" value="'.getinput($ds['info']).'" size="60" /></td>
    </tr>
    <tr>
      <td valign="top"><b>'.$_language->module['read_right'].'</b></td>
      <td>'.$groups.'<br />
      <a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['right_info_category'].'</td>
    </tr>
    <tr>
      <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="catID" value="'.$catID.'" /></td>
      <td><input type="submit" name="saveeditcat" value="'.$_language->module['edit_category'].'" /></td>
    </tr>
  </table>
  </form>';
}

else {

	echo'<h1>&curren; '.$_language->module['boards'].'</h1>';

	echo'<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=boards&amp;action=addcat\');return document.MM_returnValue" value="'.$_language->module['new_category'].'" />
  <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=boards&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_board'].'" /><br /><br />';	

	echo'<form method="post" action="admincenter.php?site=boards">
  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
      <td width="60%" class="title"><b>'.$_language->module['boardname'].'</b></td>
      <td width="12%" class="title"><b>'.$_language->module['mods'].'</b></td>
      <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
      <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
    </tr>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."forum_categories ORDER BY sort");
	$anz=safe_query("SELECT count(catID) FROM ".PREFIX."forum_categories");
	$anz=mysql_result($anz, 0);

	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	while($ds=mysql_fetch_array($ergebnis)) {
		
	    echo'<tr bgcolor="#CCCCCC">
	      <td class="td_head"><b>'.getinput($ds['name']).'</b><br /><small>'.getinput($ds['info']).'</small></td>
	      <td class="td_head"></td>
	      <td class="td_head" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=boards&amp;action=editcat&amp;catID='.$ds['catID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> 
	      <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_category'].'\', \'admincenter.php?site=boards&amp;delcat=true&amp;catID='.$ds['catID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
	      <td class="td_head" align="center"><select name="sortcat[]">';
      
		for($n=1; $n<=$anz; $n++) {
			if($ds['sort'] == $n) echo'<option value="'.$ds['catID'].'-'.$n.'" selected="selected">'.$n.'</option>';
			else echo'<option value="'.$ds['catID'].'-'.$n.'">'.$n.'</option>';
		}
		
	    echo'</select></td>
	    </tr>';		 

		$boards=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE category='".$ds['catID']."' ORDER BY sort");
		$anzboards=safe_query("SELECT count(boardID) FROM ".PREFIX."forum_boards WHERE category='$ds[catID]'");
		$anzboards=mysql_result($anzboards, 0);

		$i=1;
		$CAPCLASS = new Captcha;
	    $CAPCLASS->create_transaction();
	    $hash = $CAPCLASS->get_hash();
	    while($db=mysql_fetch_array($boards)) {
	      if($i%2) { $td='td1'; }
	      else { $td='td2'; }
				
	      echo'<tr>
	        <td class="'.$td.'">'.$db['name'].'<br /><small>'.$db['info'].'</small></td>
	        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=boards&amp;action=mods&amp;boardID='.$db['boardID'].'\');return document.MM_returnValue" value="'.$_language->module['mods'].'" /></td>
	        <td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=boards&amp;action=edit&amp;boardID='.$db['boardID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> 
	        <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_board'].'\', \'admincenter.php?site=boards&amp;delete=true&amp;boardID='.$db['boardID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
	        <td class="'.$td.'" align="center"><select name="sortboards[]">';
	        
				for($j=1; $j<=$anzboards; $j++) {
					if($db['sort'] == $j) echo'<option value="'.$db['boardID'].'-'.$j.'" selected="selected">'.$j.'</option>';
					else echo'<option value="'.$db['boardID'].'-'.$j.'">'.$j.'</option>';
				}
				
	      echo'</select></td>
	      </tr>';
	      
	      $i++;
		}
	}

	$boards=safe_query("SELECT * FROM ".PREFIX."forum_boards WHERE category='0' ORDER BY sort");
	$anzboards=safe_query("SELECT count(boardID) FROM ".PREFIX."forum_boards WHERE category='0'");
	$anzboards=mysql_result($anzboards, 0);
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	while($db=mysql_fetch_array($boards)) {

		echo'<tr bgcolor="#dcdcdc">
      <td bgcolor="#FFFFFF"><b>'.getinput($db['name']).'</b></td>
      <td bgcolor="#FFFFFF"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=boards&amp;action=mods&amp;boardID='.$db['boardID'].'\');return document.MM_returnValue" value="'.$_language->module['mods'].'" /></td>
      <td bgcolor="#FFFFFF"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=boards&amp;action=edit&amp;boardID='.$db['boardID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> 
      <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_board'].'\', \'admincenter.php?site=boards&amp;delete=true&amp;boardID='.$db['boardID'].'&amp;captcha_hash='.$hash.'\')" value="delete" /></td>
      <td bgcolor="#FFFFFF"><select name="sort[]">';
      
		for($n=1; $n<=$anzboards; $n++) {
			if($ds['sort'] == $n) echo'<option value="'.$db['boardID'].'-'.$n.'" selected="selected">'.$n.'</option>';
			else echo'<option value="'.$db['boardID'].'-'.$n.'">'.$n.'</option>';
		}
		echo'</select></td></tr>';
	}
	
  echo'<tr>
      <td class="td_head" colspan="5" align="right"><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
?>