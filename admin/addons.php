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

$_language->read_module('addons');

if(!issuperadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['delete'])) {
	$linkID = $_GET['linkID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("DELETE FROM ".PREFIX."addon_links WHERE linkID='$linkID' ");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_GET['delcat'])) {
	$catID = $_GET['catID'];
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_GET['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."addon_links SET catID='0' WHERE catID='$catID' ");
		safe_query("DELETE FROM ".PREFIX."addon_categories WHERE catID='$catID' ");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['sortieren'])) {
	$sortcat = $_POST['sortcat'];
	$sortlinks = $_POST['sortlinks'];
  
  	if(is_array($sortcat)) {
		foreach($sortcat as $sortstring) {
			$sorter=explode("-", $sortstring);
			safe_query("UPDATE ".PREFIX."addon_categories SET sort='$sorter[1]' WHERE catID='$sorter[0]' ");
		}
	}
	if(is_array($sortlinks)) {
		foreach($sortlinks as $sortstring) {
			$sorter=explode("-", $sortstring);
			safe_query("UPDATE ".PREFIX."addon_links SET sort='$sorter[1]' WHERE linkID='$sorter[0]' ");
		}
	}
}

elseif(isset($_POST['save'])) {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$anz=mysqli_num_rows(safe_query("SELECT linkID FROM ".PREFIX."addon_links WHERE catID='".$_POST['catID']."'"));
		safe_query("INSERT INTO ".PREFIX."addon_links ( catID, name, url, accesslevel, sort ) values ( '".$_POST['catID']."', '".$_POST['name']."', '".$_POST['url']."', '".$_POST['accesslevel']."', '".($anz+1)."' )");
  	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['savecat'])) {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		$anz=mysqli_num_rows(safe_query("SELECT catID FROM ".PREFIX."addon_categories"));
		safe_query("INSERT INTO ".PREFIX."addon_categories ( name, sort ) values( '".$_POST['name']."', '".($anz+1)."' )");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveedit'])) {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."addon_links SET catID='".$_POST['catID']."', name='".$_POST['name']."', url='".$_POST['url']."', accesslevel='".$_POST['accesslevel']."' WHERE linkID='".$_POST['linkID']."'");
	} else echo $_language->module['transaction_invalid'];
}

elseif(isset($_POST['saveeditcat'])) {
	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."addon_categories SET name='".$_POST['name']."' WHERE catID='".$_POST['catID']."' ");
	} else echo $_language->module['transaction_invalid'];
}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="add") {
  	echo '<h1>&curren; <a href="admincenter.php?site=addons" class="white">'.$_language->module['addons'].'</a> &raquo; '.$_language->module['add_link'].'</h1>';
  
  	$ergebnis = safe_query("SELECT * FROM ".PREFIX."addon_categories ORDER BY sort");
	$cats = '<select name="catID">';
	while($ds=mysqli_fetch_array($ergebnis)) {
		$cats .= '<option value="'.$ds['catID'].'">'.getinput($ds['name']).'</option>';
	}
	$cats .= '</select>';
	
	$accesslevel = '<option value="any">'.$_language->module['admin_any'].'</option>
					<option value="super">'.$_language->module['admin_super'].'</option>
					<option value="forum">'.$_language->module['admin_forum'].'</option>
					<option value="file">'.$_language->module['admin_file'].'</option>
					<option value="page">'.$_language->module['admin_page'].'</option>
					<option value="feedback">'.$_language->module['admin_feedback'].'</option>
					<option value="news">'.$_language->module['admin_news'].'</option>
					<option value="polls">'.$_language->module['admin_polls'].'</option>
					<option value="clanwar">'.$_language->module['admin_clanwar'].'</option>
					<option value="user">'.$_language->module['admin_user'].'</option>
					<option value="cash">'.$_language->module['admin_cash'].'</option>
					<option value="gallery">'.$_language->module['admin_gallery'].'</option>';

	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
  	echo '<form method="post" action="admincenter.php?site=addons">
		  <table width="100%" border="0" cellspacing="1" cellpadding="3">
			<tr>
			  <td width="15%"><b>'.$_language->module['category'].'</b></td>
			  <td width="85%">'.$cats.'</td>
			</tr>
			<tr>
			  <td><b>'.$_language->module['name'].'</b></td>
			  <td><input type="text" name="name" size="60" /></td>
			</tr>
			<tr>
			  <td><b>'.$_language->module['url'].'</b></td>
			  <td><input type="text" name="url" size="60" /></td>
			</tr>
			<tr>
			  <td valign="top"><b>'.$_language->module['accesslevel'].'</b></td>
			  <td><select name="accesslevel">'.$accesslevel.'</select></td>
			</tr>
			<tr>
			  <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
			  <td><input type="submit" name="save" value="'.$_language->module['add_link'].'" /></td>
			</tr>
		  </table>
		  </form>';
}

elseif($action=="edit") {
  	echo '<h1>&curren; <a href="admincenter.php?site=addons" class="white">'.$_language->module['addons'].'</a> &raquo; '.$_language->module['edit_link'].'</h1>';
  
  	$linkID = $_GET['linkID'];
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."addon_links WHERE linkID='$linkID'");
	$ds = mysqli_fetch_array($ergebnis);

	$category = safe_query("SELECT * FROM ".PREFIX."addon_categories ORDER BY sort");
	$cats = '<select name="catID">';
	while($dc=mysqli_fetch_array($category)) {
		if($ds['catID']==$dc['catID']) $selected = " selected=\"selected\"";
		else $selected = "";
		$cats .= '<option value="'.$dc['catID'].'"'.$selected.'>'.getinput($dc['name']).'</option>';
	}
	$cats .= '</select>';
	
	$accesslevel = '<option value="any">'.$_language->module['admin_any'].'</option>
					<option value="super">'.$_language->module['admin_super'].'</option>
					<option value="forum">'.$_language->module['admin_forum'].'</option>
					<option value="file">'.$_language->module['admin_file'].'</option>
					<option value="page">'.$_language->module['admin_page'].'</option>
					<option value="feedback">'.$_language->module['admin_feedback'].'</option>
					<option value="news">'.$_language->module['admin_news'].'</option>
					<option value="polls">'.$_language->module['admin_polls'].'</option>
					<option value="clanwar">'.$_language->module['admin_clanwar'].'</option>
					<option value="user">'.$_language->module['admin_user'].'</option>
					<option value="cash">'.$_language->module['admin_cash'].'</option>
					<option value="gallery">'.$_language->module['admin_gallery'].'</option>';
	$accesslevel=str_replace('value="'.$ds['accesslevel'].'"', 'value="'.$ds['accesslevel'].'" selected="selected"', $accesslevel);

	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo '<form method="post" action="admincenter.php?site=addons">
		  <table width="100%" border="0" cellspacing="1" cellpadding="3">
			<tr>
			  <td width="15%"><b>'.$_language->module['category'].'</b></td>
			  <td width="85%">'.$cats.'</td>
			</tr>
			<tr>
			  <td><b>'.$_language->module['name'].'</b></td>
			  <td><input type="text" name="name" value="'.getinput($ds['name']).'" size="60" /></td>
			</tr>
			<tr>
			  <td><b>'.$_language->module['url'].'</b></td>
			  <td><input type="text" name="url" value="'.getinput($ds['url']).'" size="60" /></td>
			</tr>
			<tr>
			  <td valign="top"><b>'.$_language->module['accesslevel'].'</b></td>
			  <td><select name="accesslevel">'.$accesslevel.'</select></td>
			</tr>
			<tr>
			  <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="linkID" value="'.$linkID.'" /></td>
			  <td><input type="submit" name="saveedit" value="'.$_language->module['edit_link'].'" /></td>
			</tr>
		  </table>
		  </form>';
}

elseif($action=="addcat") {
  	echo '<h1>&curren; <a href="admincenter.php?site=addons" class="white">'.$_language->module['addons'].'</a> &raquo; '.$_language->module['add_category'].'</h1>';
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo '<form method="post" action="admincenter.php?site=addons">
		  <table width="100%" border="0" cellspacing="1" cellpadding="3">
			<tr>
			  <td width="15%"><b>'.$_language->module['name'].'</b></td>
			  <td width="85%"><input type="text" name="name" size="60" /></td>
			</tr>
			<tr>
			  <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /></td>
			  <td><input type="submit" name="savecat" value="'.$_language->module['add_category'].'" /></td>
			</tr>
		  </table>
		  </form>';
}

elseif($action=="editcat") {
  	echo '<h1>&curren; <a href="admincenter.php?site=addons" class="white">'.$_language->module['addons'].'</a> &raquo; '.$_language->module['edit_category'].'</h1>';

	$catID = $_GET['catID'];
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."addon_categories WHERE catID='$catID'");
	$ds = mysqli_fetch_array($ergebnis);

  	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	
	echo '<form method="post" action="admincenter.php?site=addons">
		  <table width="100%" border="0" cellspacing="1" cellpadding="3">
			<tr>
			  <td width="15%"><b>'.$_language->module['name'].'</b></td>
			  <td width="85%"><input type="text" name="name" value="'.getinput($ds['name']).'" size="60" /></td>
			</tr>
			<tr>
			  <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="catID" value="'.$catID.'" /></td>
			  <td><input type="submit" name="saveeditcat" value="'.$_language->module['edit_category'].'" /></td>
			</tr>
		  </table>
		  </form>';
}

else {
	echo '<h1>&curren; '.$_language->module['addons'].'</h1>';

	echo '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=addons&amp;action=addcat\');return document.MM_returnValue" value="'.$_language->module['new_category'].'" /> <input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=addons&amp;action=add\');return document.MM_returnValue" value="'.$_language->module['new_link'].'" /><br /><br />';	

	echo '<form method="post" action="admincenter.php?site=addons">
		  <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
			<tr>
			  <td width="55%" class="title"><b>'.$_language->module['name'].'</b></td>
			  <td width="17%" class="title" align="center"><b>'.$_language->module['accesslevel'].'</b></td>
			  <td width="20%" class="title"><b>'.$_language->module['actions'].'</b></td>
			  <td width="8%" class="title"><b>'.$_language->module['sort'].'</b></td>
			</tr>';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."addon_categories ORDER BY sort");
	$tmp=mysqli_fetch_assoc(safe_query("SELECT count(catID) as cnt FROM ".PREFIX."addon_categories"));
	$anz=$tmp['cnt'];

	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	while($ds=mysqli_fetch_array($ergebnis)) { 
		$list = '<select name="sortcat[]">';
		for($n=1; $n<=$anz; $n++) {
			if($n<=8) $list .= '';
			else $list .= '<option value="'.$ds['catID'].'-'.$n.'">'.$n.'</option>';
		}
		$list .= '</select>';
		$list = str_replace('value="'.$ds['catID'].'-'.$ds['sort'].'"','value="'.$ds['catID'].'-'.$ds['sort'].'" selected="selected"',$list);
		if($ds['default']==1) {
			$sort = '<b>'.$ds['sort'].'</b>';
			$catactions = '';
			$name = $_language->module['cat_'.getinput($ds['name'])];
		}
		else {
			$sort = $list;
			$catactions = '<input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=addons&amp;action=editcat&amp;catID='.$ds['catID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_category'].'\', \'admincenter.php?site=addons&amp;delcat=true&amp;catID='.$ds['catID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" />';
			$name = getinput($ds['name']);
		}
		
	    echo '<tr bgcolor="#CCCCCC">
			    <td class="td_head" colspan="2"><b>'.$name.'</b></td>
			    <td class="td_head" align="center">'.$catactions.'</td>
			    <td class="td_head" align="center">'.$sort.'</td>
	    	  </tr>';		 

		$links=safe_query("SELECT * FROM ".PREFIX."addon_links WHERE catID='".$ds['catID']."' ORDER BY sort");
		$tmp=mysqli_fetch_assoc(safe_query("SELECT count(linkID) as cnt FROM ".PREFIX."addon_links WHERE catID='".$ds['catID']."'"));
		$anzlinks=$tmp['cnt'];

		$i=1;
		$CAPCLASS = new Captcha;
	    $CAPCLASS->create_transaction();
	    $hash = $CAPCLASS->get_hash();
	    while($db=mysqli_fetch_array($links)) {
	      	if($i%2) { $td='td1'; }
	      	else { $td='td2'; }
			
			$linklist = '<select name="sortlinks[]">';
			for($n=1; $n<=$anzlinks; $n++) {
				$linklist .= '<option value="'.$db['linkID'].'-'.$n.'">'.$n.'</option>';
			}
			$linklist .= '</select>';
			$linklist = str_replace('value="'.$db['linkID'].'-'.$db['sort'].'"','value="'.$db['linkID'].'-'.$db['sort'].'" selected="selected"',$linklist);
				
	      	echo '<tr>
				    <td class="'.$td.'"><b>'.$db['name'].'</b><br /><small>'.$db['url'].'</small></td>
					<td class="'.$td.'" align="center"><small><b>'.$_language->module['admin_'.getinput($db['accesslevel'])].'</b></small></td>
					<td class="'.$td.'" align="center"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=addons&amp;action=edit&amp;linkID='.$db['linkID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_link'].'\', \'admincenter.php?site=addons&amp;delete=true&amp;linkID='.$db['linkID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" /></td>
					<td class="'.$td.'" align="center">'.$linklist.'</td>
				  </tr>';
	      	$i++;
		}
	}

	$links=safe_query("SELECT * FROM ".PREFIX."addon_links WHERE catID='0' ORDER BY sort");
	$tmp=mysqli_fetch_assoc(safe_query("SELECT count(linkID) as cnt FROM ".PREFIX."addon_links WHERE catID='0'"));
	$anzlinks=$tmp['cnt'];
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
	while($db=mysqli_fetch_array($links)) {
		$noncatlist = '<select name="sortlinks[]">';
		for($n=1; $n<=$anz; $n++) {
			$noncatlist .= '<option value="'.$db['linkID'].'-'.$n.'">'.$n.'</option>';
		}
		$noncatlist .= '</select>';
		$noncatlist = str_replace('value="'.$db['linkID'].'-'.$db['sort'].'"','value="'.$db['linkID'].'-'.$db['sort'].'" selected="selected"',$noncatlist);
		echo '<tr bgcolor="#dcdcdc">
			    <td bgcolor="#FFFFFF"><b>'.getinput($db['name']).'</b><br /><small>'.$db['url'].'</small></td>
			    <td bgcolor="#FFFFFF"><small><b>'.$_language->module['admin_'.getinput($db['accesslevel'])].'</b></small></td>
			    <td bgcolor="#FFFFFF"><input type="button" onclick="MM_goToURL(\'parent\',\'admincenter.php?site=addons&amp;action=edit&amp;linkID='.$db['linkID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete_link'].'\', \'admincenter.php?site=addons&amp;delete=true&amp;linkID='.$db['linkID'].'&amp;captcha_hash='.$hash.'\')" value="delete" /></td>
			    <td bgcolor="#FFFFFF">'.$noncatlist.'</td>
			  </tr>';
	}
	echo '	<tr>
		  	  <td class="td_head" colspan="5" align="right"><input type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
			</tr>
	  	  </table>
	      </form>';
}
?>