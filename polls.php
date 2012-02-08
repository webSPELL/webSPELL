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

if(isset($_GET['action'])) $action = $_GET['action'];
else $action="";

if($action=="vote") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	if(isset($_POST['pollID']) && isset($_POST['vote'])){
		$pollID = (int)$_POST['pollID'];
    	$vote = (int)$_POST['vote'];
		$_language->read_module('polls');
	  
		$ds=mysql_fetch_array(safe_query("SELECT userIDs, hosts FROM `".PREFIX."poll` WHERE pollID='".$pollID."'"));
		$anz = mysql_num_rows(safe_query("SELECT pollID FROM `".PREFIX."poll` WHERE pollID='".$pollID."' AND hosts LIKE '%".$_SERVER['REMOTE_ADDR']."%' AND intern<=".isclanmember($userID).""));
		
		$anz_user = false;
	  	if($userID) {
			if($ds['userIDs']){
				$user_ids = explode(";", $ds['userIDs']);
				if(in_array($userID, $user_ids)) $anz_user = true;
			}
			else{
				$user_ids = array();
			}
		}
		
		$cookie = false;
		if(isset($_COOKIE['poll']) && is_array($_COOKIE['poll'])){
			$cookie = in_array($pollID, $_COOKIE['poll']);
		}
	  	if(!$cookie and !$anz and !$anz_user and isset($_POST['vote'])) {
	
			//write cookie
			$index = count($_COOKIE['poll']);
			setcookie("poll[".$index."]", $pollID, time() + (3600 * 24 * 365));
	
			//write ip and userID if logged
			$add_query = "";
			if($userID) {
				$user_ids[] = $userID;
				$add_query = ", userIDs='".implode(";", $user_ids)."'";
			}
			
	    	safe_query("UPDATE ".PREFIX."poll SET hosts='".$ds['hosts']."#".$_SERVER['REMOTE_ADDR']."#'".$add_query." WHERE pollID='".$pollID."'");
	
			//write vote
			safe_query("UPDATE ".PREFIX."poll_votes SET o".$vote."=o".$vote."+1 WHERE pollID='".$pollID."'");
		}
		header('Location: index.php?site=polls');
	}
	else{
		header('Location: index.php?site=polls');
	}
}
elseif(isset($_POST['save']))  {
	include("_mysql.php");
	include("_settings.php");
	include('_functions.php');
	$_language->read_module('polls');
	
  if(isset($_POST['intern'])) $intern = $_POST['intern'];
  else $intern="";
  
  if(!ispollsadmin($userID)) die($_language->module['no_access']);

	safe_query("INSERT INTO ".PREFIX."poll (aktiv, titel, o1, o2, o3, o4, o5, o6, o7, o8, o9, o10, comments, laufzeit, intern)
		         	values( '1', '".$_POST['title']."', '".$_POST['op1']."', '".$_POST['op2']."', '".$_POST['op3']."', '".$_POST['op4']."', '".$_POST['op5']."', '".$_POST['op6']."', '".$_POST['op7']."', '".$_POST['op8']."', '".$_POST['op9']."', '".$_POST['op10']."', '".$_POST['comments']."' ,'".mktime((int)$_POST['laufzeit_hour'], (int)$_POST['laufzeit_minute'], 0, (int)$_POST['laufzeit_month'], (int)$_POST['laufzeit_day'], (int)$_POST['laufzeit_year'])."', '".$intern."')");
	$id = mysql_insert_id();

	safe_query("INSERT INTO ".PREFIX."poll_votes (pollID, o1, o2, o3, o4, o5, o6, o7, o8, o9, o10)
		         values( '$id', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0' )");
	header('Location: index.php?site=polls');
}
elseif(isset($_POST['saveedit'])) {
	include("_mysql.php");
	include("_settings.php");
	include('_functions.php');
	$_language->read_module('polls');
	if(!ispollsadmin($userID)) die($_language->module['no_access']);

	$pollID = $_POST['pollID'];
  if(isset($_POST['intern'])) $intern = $_POST['intern'];
  else $intern="";
    
	if(isset($_POST['reset'])) {
		safe_query("DELETE FROM ".PREFIX."poll WHERE pollID='$pollID'");
		safe_query("DELETE FROM ".PREFIX."poll_votes WHERE pollID='$pollID'");

		safe_query("INSERT INTO ".PREFIX."poll (aktiv, titel, o1, o2, o3, o4, o5, o6, o7, o8, o9, o10, comments, laufzeit, intern) values( '1', '".$_POST['title']."', '".$_POST['op1']."', '".$_POST['op2']."', '".$_POST['op3']."', '".$_POST['op4']."', '".$_POST['op5']."', '".$_POST['op6']."', '".$_POST['op7']."', '".$_POST['op8']."', '".$_POST['op9']."', '".$_POST['op10']."', '".$_POST['comments']."', '".mktime((int)$_POST['laufzeit_hour'], (int)$_POST['laufzeit_minute'], 0, (int)$_POST['laufzeit_month'], (int)$_POST['laufzeit_day'], (int)$_POST['laufzeit_year'])."' , '".$intern."')");
		$id = mysql_insert_id();
		safe_query("INSERT INTO ".PREFIX."poll_votes (pollID, o1, o2, o3, o4, o5, o6, o7, o8, o9, o10) values( '".$id."', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0' )");
	}
	else safe_query("UPDATE ".PREFIX."poll SET titel='".$_POST['title']."', o1='".$_POST['op1']."', o2='".$_POST['op2']."', o3='".$_POST['op3']."', o4='".$_POST['op4']."', o5='".$_POST['op5']."', o6='".$_POST['op6']."', o7='".$_POST['op7']."', o8='".$_POST['op8']."', o9='".$_POST['op9']."', o10='".$_POST['op10']."', comments='".$_POST['comments']."', laufzeit='".mktime((int)$_POST['laufzeit_hour'], (int)$_POST['laufzeit_minute'], 0, (int)$_POST['laufzeit_month'], $_POST['laufzeit_day'], (int)$_POST['laufzeit_year'])."', intern='".$intern."' WHERE pollID='$pollID'");
	header('Location: index.php?site=polls');
}
elseif(isset($_GET['end']))  {
	include("_mysql.php");
	include("_settings.php");
	include('_functions.php');
	$_language->read_module('polls');
	if(!ispollsadmin($userID)) die($_language->module['no_access']);
	$pollID = $_GET['pollID'];
	safe_query("UPDATE ".PREFIX."poll SET aktiv='0' WHERE pollID='".$pollID."'");
	header('Location: index.php?site=polls');
}
elseif(isset($_GET['reopen']))  {
	include("_mysql.php");
	include("_settings.php");
	include('_functions.php');
	$_language->read_module('polls');
	if(!ispollsadmin($userID)) die($_language->module['no_access']);
	$pollID = $_GET['pollID'];
	safe_query("UPDATE ".PREFIX."poll SET aktiv='1' WHERE pollID='".$pollID."'");
	header('Location: index.php?site=polls');
}
elseif(isset($_GET['delete'])) {
	include("_mysql.php");
	include("_settings.php");
	include('_functions.php');
	$_language->read_module('polls');
	if(!ispollsadmin($userID)) die($_language->module['no_access']);

	$pollID = $_GET['pollID'];
	safe_query("DELETE FROM ".PREFIX."poll WHERE pollID = '".$pollID."'");
	safe_query("DELETE FROM ".PREFIX."poll_votes WHERE pollID = '".$pollID."'");
	header('Location: index.php?site=polls');
}

$_language->read_module('polls');
eval("\$title_polls = \"".gettemplate("title_polls")."\";");
echo $title_polls;

if($action=="new") {
	if(ispollsadmin($userID)) {
		$bg1 = BG_1;
		eval("\$polls_new = \"".gettemplate("polls_new")."\";");
		echo $polls_new;
	}
	else redirect('index.php?site=news', $_language->module['no_access'],3);
}
elseif($action=="edit") {
	if(ispollsadmin($userID)) {
		$pollID = $_GET['pollID'];
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."poll WHERE pollID='$pollID'");
		$ds = mysql_fetch_array($ergebnis);
		
		if(isset($ds['pollID'])) {

			$ds["laufzeit_year"] = date("y",$ds["laufzeit"]);
			$ds["laufzeit_month"] = date("m",$ds["laufzeit"]);
			$ds["laufzeit_day"] = date("d",$ds["laufzeit"]);
			$ds["laufzeit_hour"] = date("H",$ds["laufzeit"]);
			$ds["laufzeit_minute"] = date("i",$ds["laufzeit"]);
			
			$polltitle = getinput($ds['titel']);
			$option1 = getinput($ds['o1']);
			$option2 = getinput($ds['o2']);
			$option3 = getinput($ds['o3']);
			$option4 = getinput($ds['o4']);
			$option5 = getinput($ds['o5']);
			$option6 = getinput($ds['o6']);
			$option7 = getinput($ds['o7']);
			$option8 = getinput($ds['o8']);
			$option9 = getinput($ds['o9']);
			$option10 = getinput($ds['o10']);
	
			$comments = '<option value="0">'.$_language->module['disable_comments'].'</option><option value="1">'.$_language->module['enable_user_comments'].'</option><option value="2">'.$_language->module['enable_visitor_comments'].'</option>';
			$comments = str_replace('value="'.$ds['comments'].'"', 'value="'.$ds['comments'].'" selected="selected"', $comments);
			if($ds['intern']) $intern = "checked='checked'";
			else $intern = '';
			$bg1 = BG_1;
			eval("\$polls_edit = \"".gettemplate("polls_edit")."\";");
			echo $polls_edit;
		}
	}
	else redirect('index.php?site=polls', $_language->module['no_access'],3);
}
elseif(isset($_GET['pollID'])) {
	$pollID = $_GET['pollID'];
	if(ispollsadmin($userID)) echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=polls&amp;action=new\');return document.MM_returnValue" value="'.$_language->module['new_poll'].'" /><br /><br />';
  
  $ergebnis = safe_query("SELECT * FROM ".PREFIX."poll WHERE pollID='$pollID' AND intern<=".isclanmember($userID));
	$ds = mysql_fetch_array($ergebnis);
	$bg1 = BG_1;
	$title=$ds['titel'];
  
  if($ds['intern'] == 1) $isintern = '('.$_language->module['intern'].')';
  else $isintern = '';
	
  if($ds['laufzeit'] < time() OR $ds['aktiv'] == "0") $timeleft = $_language->module['poll_ended']; else $timeleft = floor(($ds['laufzeit']-time())/(60*60*24))." ".$_language->module['days'];

	for ($n = 1; $n <= 10; $n++) {
		if($ds['o'.$n]) $options[] = clearfromtags($ds['o'.$n]);
	}

	$adminactions = '';
  if(ispollsadmin($userID)) {
		if($ds['aktiv']) {
			$stop=' <input type="button" onclick="MM_confirm(\''.$_language->module['really_stop'].'\', \'polls.php?end=true&amp;pollID='.$ds['pollID'].'\')" value="'.$_language->module['stop_poll'].'" /> ';
    	}
		else {
			$stop = ' <input type="button" onclick="MM_confirm(\''.$_language->module['really_reopen'].'\', \'polls.php?reopen=true&amp;pollID='.$ds['pollID'].'\')" value="'.$_language->module['reopen_poll'].'" /> ';
		}
		$edit=' <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=polls&amp;action=edit&amp;pollID='.$ds['pollID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> ';
		$adminactions=$edit.'<input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'polls.php?delete=true&amp;pollID='.$ds['pollID'].'\')" value="'.$_language->module['delete'].'" />'.$stop;
	}

	$votes = safe_query("SELECT * FROM ".PREFIX."poll_votes WHERE pollID='".$pollID."'");
	$dv = mysql_fetch_array($votes);
	$gesamtstimmen = $dv['o1'] + $dv['o2'] + $dv['o3'] + $dv['o4'] + $dv['o5'] + $dv['o6'] + $dv['o7'] + $dv['o8'] + $dv['o9'] + $dv['o10'];
	$n = 1;

	eval("\$polls_head = \"".gettemplate("polls_head")."\";");
	echo $polls_head;
	$comments = "";
	foreach ($options as $option) {
		$stimmen = $dv['o'.$n];
		if ($gesamtstimmen) {
			$perc = $stimmen / $gesamtstimmen * 10000;
			settype($perc, "integer");
			$perc = $perc / 100;
		}
		else $perc = 0;
		$picwidth = $perc;
		settype($picwidth, "integer");

		if($picwidth) $pic = '<table width="100" cellspacing="1" cellpadding="1" bgcolor="'.BORDER.'">
      <tr bgcolor="'.BG_2.'">
        <td style="background-image: url(images/icons/poll_bg.gif);"><img src="images/icons/poll.gif" width="'.$picwidth.'" height="5" alt="" /></td>
      </tr>
    </table>';
    
		else $pic = '';

		eval("\$polls_content = \"".gettemplate("polls_content")."\";");
		echo $polls_content;
		$n++;
	}

	eval("\$polls_foot = \"".gettemplate("polls_foot")."\";");
	echo $polls_foot;

	$comments_allowed = $ds['comments'];
	$parentID = $pollID;
	$type = "po";
	$referer = "index.php?site=polls&amp;pollID=".$pollID;

	include("comments.php");
}
elseif(isset($_GET['vote'])) {

	$pagebg = PAGEBG;
	$border = BORDER;
	$bghead = BGHEAD;
	$bgcat = BGCAT;

	$poll = $_GET['vote'];

	$lastpoll = safe_query("SELECT * FROM ".PREFIX."poll WHERE aktiv='1' AND laufzeit>".time()." AND intern<=".isclanmember($userID)." and pollID='".$poll."' LIMIT 0,1");

	$anz = mysql_num_rows($lastpoll);
	$ds = mysql_fetch_array($lastpoll);
	if($anz) {
		$anz = mysql_num_rows(safe_query("SELECT pollID FROM `".PREFIX."poll` WHERE pollID='".$ds['pollID']."' AND hosts LIKE '%".$_SERVER['REMOTE_ADDR']."%' AND intern<=".isclanmember($userID).""));
		
		$anz_user = false;
		if($userID) {
			$user_ids = explode(";", $ds['userIDs']);
			if(in_array($userID, $user_ids)) $anz_user = true;
		}
		$cookie = false;
		if(isset($_COOKIE['poll']) && is_array($_COOKIE['poll'])){
			$cookie = in_array($ds['pollID'], $_COOKIE['poll']);
		}
		
		
		if($cookie or $anz or $anz_user){
    		redirect('index.php?site=polls&amp;pollID='.$ds['pollID'], $_language->module['already_voted'],3);
		}
		else {
			echo'<form method="post" action="polls.php?action=vote">
			<table cellspacing="2" cellpadding="0">
				<tr>
					<td><b>'.$ds['titel'].'</b><br /><br /></td>
				</tr>
				<tr>
					<td>';

			for ($n=1; $n<=10; $n++) {
				if($ds['o'.$n]) $options[]=clearfromtags($ds['o'.$n]);
			}
			$n=1;
			foreach ($options as $option) {
				echo'<input class="input" type="radio" name="vote" value="'.$n.'" /> '.$option.'<br />';
				$n++;
			}
			echo'</td>
        </tr>
        <tr>
          <td><br /><input type="hidden" name="pollID" value="'.$ds['pollID'].'" />
          <input type="submit" value="vote" /></td>
        </tr>
        <tr>
          <td><br />&#8226; <a href="index.php?site=polls">'.$_language->module['show_polls'].'</a></td>
        </tr>
      </table>
      </form>';
      
		}
	}	else redirect('index.php?site=polls&pollID='.$ds['pollID'], $_language->module['poll_ended'],3);
}
else {
	if(ispollsadmin($userID)) echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=polls&amp;action=new\');return document.MM_returnValue" value="'.$_language->module['new_poll'].'" /><br /><br />';

	$ergebnis = safe_query("SELECT * FROM ".PREFIX."poll WHERE intern<=".isclanmember($userID)." ORDER BY pollID DESC");
	$anz = mysql_num_rows($ergebnis);
	if($anz) {
		$i = 1;
		while ($ds = mysql_fetch_array($ergebnis)) {
			if($i % 2) $bg1 = BG_1;
			else $bg1 = BG_2;

			$title = $ds['titel'];
      
      if($ds['intern'] == 1) $isintern = '('.$_language->module['intern'].')';
      else $isintern = '';
      
			if($ds['laufzeit'] < time() or $ds['aktiv'] == "0") $timeleft = $_language->module['poll_ended']; else $timeleft = floor(($ds['laufzeit']-time())/(60*60*24))." ".$_language->module['days']." (".date("d.m.Y H:i", $ds['laufzeit']).") <br /><a href='index.php?site=polls&amp;vote=".$ds['pollID']."'>[".$_language->module['vote_now']."]</a>";

			for ($n=1; $n<=10; $n++) {
				if($ds['o'.$n]) $options[] = clearfromtags($ds['o'.$n]);
			}

			$adminactions = '';
      if(ispollsadmin($userID)) {
				if($ds['aktiv']) {
					$stop = ' <input type="button" onclick="MM_confirm(\''.$_language->module['really_stop'].'\', \'polls.php?end=true&amp;pollID='.$ds['pollID'].'\')" value="'.$_language->module['stop_poll'].'" /> ';
        		}
				else {
					$stop = ' <input type="button" onclick="MM_confirm(\''.$_language->module['really_reopen'].'\', \'polls.php?reopen=true&amp;pollID='.$ds['pollID'].'\')" value="'.$_language->module['reopen_poll'].'" /> ';
				}
				$edit = ' <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=polls&amp;action=edit&amp;pollID='.$ds['pollID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /> ';
				$adminactions = $edit.'<input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'polls.php?delete=true&amp;pollID='.$ds['pollID'].'\')" value="'.$_language->module['delete'].'" />'.$stop;
			}

			$votes = safe_query("SELECT * FROM ".PREFIX."poll_votes WHERE pollID='".$ds['pollID']."'");
			$dv = mysql_fetch_array($votes);
			$gesamtstimmen = $dv['o1'] + $dv['o2'] + $dv['o3'] + $dv['o4'] + $dv['o5'] + $dv['o6'] + $dv['o7'] + $dv['o8'] + $dv['o9'] + $dv['o10'];
			$n=1;
      
      eval ("\$polls_head = \"".gettemplate("polls_head")."\";");
			echo $polls_head;

			foreach ($options as $option) {
				$stimmen = $dv['o'.$n];
				if ($gesamtstimmen) {
					$perc = $stimmen / $gesamtstimmen * 10000;
					settype($perc, "integer");
					$perc = $perc / 100;
				}
				else $perc = 0;
				$picwidth = $perc;
				settype($picwidth, "integer");

				$pic='<table width="104" cellspacing="1" cellpadding="1" bgcolor="'.BORDER.'">
          <tr bgcolor="'.BG_2.'">
            <td style="background-image: url(images/icons/poll_bg.gif);"><img src="images/icons/poll.gif" width="'.($picwidth).'" height="5" alt="" /></td>
          </tr>
        </table>';

				$anzcomments = getanzcomments($ds['pollID'], 'po');
				if($anzcomments) $comments = '<a href="index.php?site=polls&amp;pollID='.$ds['pollID'].'">['.$anzcomments.'] '.$_language->module['comments'].'</a> '.$_language->module['latest_by'].' '.getlastcommentposter($ds['pollID'], 'po').' - '.date("d.m.Y - H:i", getlastcommentdate($ds['pollID'], 'po'));
				else $comments = '<a href="index.php?site=polls&amp;pollID='.$ds['pollID'].'">[0] '.$_language->module['comments'].'</a>';

				eval ("\$polls_content = \"".gettemplate("polls_content")."\";");
				echo $polls_content;

				$n++;
			}

			eval ("\$polls_foot = \"".gettemplate("polls_foot")."\";");
			echo $polls_foot;
			
      $i++;

			unset($options);
		}
	}
	else echo $_language->module['no_entries'];
}
?>
