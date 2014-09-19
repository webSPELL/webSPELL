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

$_language->read_module('polls');

function vote($poll) {
	global $userID, $_language;
	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	if($poll) $lastpoll = safe_query("SELECT * FROM ".PREFIX."poll WHERE aktiv='1' AND laufzeit>".time()." AND intern<=".isclanmember($userID)." and pollID='".$poll."' LIMIT 0,1");
	else {
		$num = mysql_num_rows(safe_query("SELECT * FROM ".PREFIX."poll WHERE aktiv='1' AND laufzeit>".time()." AND intern<=".isclanmember($userID).""));
		if($num) {
			$start = rand(0,($num-1));
			$lastpoll = safe_query("SELECT * FROM ".PREFIX."poll WHERE aktiv='1' AND laufzeit>".time()." AND intern<=".isclanmember($userID)." ORDER BY pollID DESC LIMIT ".$start.",".($start+1)."");
		}
		else {
			echo $_language->module['no_active_poll'].'<br /><br />&#8226; <a href="index.php?site=polls">'.$_language->module['show_polls'].'</a>';
			return true;
		}
	}

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
		
		if($cookie or $anz or $anz_user) {

			if($ds['intern'] == 1) $isintern = '('.$_language->module['intern'].')';
      		else $isintern = '';
      	
      		$title = $ds['titel'];      

			for ($n = 1; $n <= 10; $n++) {
				if($ds['o'.$n]) $options[] = clearfromtags($ds['o'.$n]);
			}

			$votes = safe_query("SELECT * FROM ".PREFIX."poll_votes WHERE pollID='".$ds['pollID']."'");
			$dv = mysql_fetch_array($votes);
			$gesamtstimmen = $dv['o1'] + $dv['o2'] + $dv['o3'] + $dv['o4'] + $dv['o5'] + $dv['o6'] + $dv['o7'] + $dv['o8'] + $dv['o9'] + $dv['o10'];
			
			eval("\$poll_voted_head = \"".gettemplate("poll_voted_head")."\";");
			echo $poll_voted_head;

			$n=1;
			$bg = BG_2;
			foreach ($options as $option) {
				$stimmen = $dv['o'.$n];
				if ($gesamtstimmen) {
					$perc = $stimmen / $gesamtstimmen * 10000;
					settype($perc, "integer");
					$perc = $perc/100;
				}
				else $perc = 0;
				$picwidth = $perc;
				settype($picwidth, "integer");
				eval("\$poll_voted_content = \"".gettemplate("poll_voted_content")."\";");
				echo $poll_voted_content;
				$n++;
			}

			$anzcomments = getanzcomments($ds['pollID'], 'po');
			$comments='<a href="index.php?site=polls&amp;pollID='.$ds['pollID'].'">['.$anzcomments.'] '.$_language->module['comments'].'</a>';
			
			eval("\$poll_voted_foot = \"".gettemplate("poll_voted_foot")."\";");
			echo $poll_voted_foot;
			
			unset($options);
		}
		else {
			
      		if($ds['intern'] == 1) $isintern = '('.$_language->module['intern'].')';
      		else $isintern = '';
      		$title = $ds['titel'];
      
      		eval("\$poll_head = \"".gettemplate("poll_head")."\";");
			echo $poll_head;
			
			for ($n=1; $n<=10; $n++) {
				if($ds['o'.$n]) $options[]=$ds['o'.$n];
			}
			$n=1;
			foreach ($options as $option) {
				$option=$option;
				eval("\$poll_content = \"".gettemplate("poll_content")."\";");
				echo $poll_content;
				$n++;
			}
			$pollID = $ds['pollID'];
			eval("\$poll_foot = \"".gettemplate("poll_foot")."\";");
			echo $poll_foot;
		}
	}
	else{
		echo $_language->module['no_active_poll'].'<br /><br />&#8226; <a href="index.php?site=polls">'.$_language->module['show_polls'].'</a>';
	}
}

if(!isset($pollID)) $pollID = '';
vote($pollID);
?>