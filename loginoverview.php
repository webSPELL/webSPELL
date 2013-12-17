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
$_language->read_module('loginoverview');

if($userID && !isset($_GET['userID']) && !isset($_POST['userID'])) {

	eval ("\$title_loginoverview = \"".gettemplate("title_loginoverview")."\";");
	echo $title_loginoverview;

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$ds=mysqli_fetch_array(safe_query("SELECT registerdate FROM `".PREFIX."user` WHERE userID='".$userID."'"));
	$username='<a href="index.php?site=profile&amp;id='.$userID.'">'.getnickname($userID).'</a>';
	$lastlogin = getformatdatetime($_SESSION['ws_lastlogin']);
	$registerdate = getformatdatetime($ds['registerdate']);

	//messages?
	$newmessages = getnewmessages($userID);
	if($newmessages==1) $newmessages=$_language->module['one_new_message'];
	elseif($newmessages>1) $newmessages=str_replace('%new_messages%', $newmessages, $_language->module['x_new_message']);
	else $newmessages=$_language->module['no_new_messages'];

	//boardposts?

	$posts=safe_query("SELECT
					p.topicID, 
					p.date, 
					p.message, 
					p.boardID,
					t.topic,
					t.readgrps
				FROM 
					`".PREFIX."forum_posts` AS p, 
					`".PREFIX."forum_topics` AS t 
				WHERE 
					p.date>".$_SESSION['ws_lastlogin']." AND 
					p.topicID = t.topicID
				LIMIT
					0, 10");
	$topics=safe_query("SELECT * FROM ".PREFIX."forum_topics WHERE date > ".$_SESSION['ws_lastlogin']." LIMIT 0, 10");

	$new_posts=mysqli_num_rows(safe_query("SELECT p.postID FROM `".PREFIX."forum_posts` AS p, `".PREFIX."forum_topics` AS t WHERE p.date>".$_SESSION['ws_lastlogin']." AND p.topicID = t.topicID"));
	$new_topics=mysqli_num_rows(safe_query("SELECT * FROM ".PREFIX."forum_topics WHERE date > ".$_SESSION['ws_lastlogin']));

	//new topics

  $topiclist="";
	if(mysqli_num_rows($topics)) {
		$n=1;
		while($db=mysqli_fetch_array($topics)) {
			if($db['readgrps'] != "") {
				$usergrps = explode(";", $db['readgrps']);
				$usergrp = 0;
				foreach($usergrps as $value) {
					if(isinusergrp($value, $userID)) {
						$usergrp = 1;
						break;
					}
				}
				if(!$usergrp and !ismoderator($userID, $db['boardID'])) continue;
			}
			$n%2 ? $bgcolor=BG_1 : $bgcolor=BG_2;
			$posttime=getformatdatetime($db['date']);

			$topiclist.='<tr bgcolor="'.$bgcolor.'">
          <td>
          <table width="100%" cellpadding="1" cellspacing="1">
            <tr>
              <td colspan="3"><a href="index.php?site=forum_topic&amp;topic='.$db['topicID'].'">'.$posttime.'<br /><b>'.str_break(getinput($db['topic']), 34).'</b></a><br /><i>'.$db['views'].' '.$_language->module['views'].' - '.$db['replys'].' '.$_language->module['replys'].'</i></td>
            </tr>
          </table>
          </td>
        </tr>';
        
			$n++;
		}
	}
	else $topiclist='<tr>
      <td bgcolor="'.BG_1.'">'.$_language->module['no_new_topics'].'</td>
    </tr>';

	//new posts

	$postlist="";
  if(mysqli_num_rows($posts)) {
		$n=1;
		while($db=mysqli_fetch_array($posts)) {
			if($db['readgrps'] != "") {
				$usergrps = explode(";", $db['readgrps']);
				$usergrp = 0;
				foreach($usergrps as $value) {
					if(isinusergrp($value, $userID)) {
						$usergrp = 1;
						break;
					}
				}
				if(!$usergrp and !ismoderator($userID, $db['boardID'])) continue;
			}
			$n%2 ? $bgcolor1=BG_1 : $bgcolor1=BG_2;
			$n%2 ? $bgcolor2=BG_3 : $bgcolor2=BG_4;
			$posttime=getformatdatetime($db['date']);
			if(mb_strlen($db['message']) > 100) $message=mb_substr($db['message'],0,90+mb_strpos(mb_substr($db['message'],90,mb_strlen($db['message']))," "))."...";
			else $message = $db['message'];

      $postlist.='<tr bgcolor="'.$bgcolor1.'">
          <td>
          <table width="100%" cellpadding="2" cellspacing="1">
            <tr>
              <td colspan="3"><a href="index.php?site=forum_topic&amp;topic='.$db['topicID'].'">'.$posttime.' <br /><b>'.str_break(getinput($db['topic']), 34).'</b></a></td>
            </tr>
            <tr><td></td></tr>
            <tr>
              <td width="1%">&nbsp;</td>
              <td bgcolor="'.$bgcolor2.'" width="98%"><div style="overflow:hidden;">'.str_break(clearfromtags($message), 34).'</div></td>
              <td width="1%">&nbsp;</td>
            </tr>
          </table>
          </td>
         </tr>';
          
			$n++;
		}
	}
	else $postlist='<tr>
      <td bgcolor="'.BG_1.'" valign="top">'.$_language->module['no_new_posts'].'</td>
    </tr>';

	//clanmember/admin/referer

	if(isclanmember($userID)) $cashboxpic = '<td><a href="index.php?site=cash_box"><img src="images/icons/cashbox.gif" border="0" alt="Cashbox" /></a></td>
  <td width="10"></td>';
	else $cashboxpic = '<td></td><td></td>';
  
	if(isanyadmin($userID)) $admincenterpic = '<td><a href="admin/admincenter.php" target="_blank"><img src="images/icons/admincenter.gif" border="0" alt="Admincenter" /></a></td>
  <td width="10"></td>';
	else $admincenterpic = '<td></td><td></td>';
  
	if(isset($_SESSION['referer'])) {
		$referer_uri = '<tr><td bgcolor="'.$bgcat.'"><br /><a href="'.$_SESSION['referer'].'" style="padding:8px 0;"><b>&laquo; '.$_language->module['back_last_page'].'</b></a><br />&nbsp;</td></tr>';
		unset($_SESSION['referer']);
	}
	else $referer_uri = '';

	//upcoming
	$clanwars = '';
	if(isclanmember($userID)) {

		$clanwars .= '<tr>
      <td colspan="3"><b>'.$_language->module['upcoming_clanwars'].'</b><br />&nbsp;</td>
    </tr>';

		$squads=safe_query("SELECT squadID FROM `".PREFIX."squads_members` WHERE userID='".$userID."'");
		while($squad=mysqli_fetch_array($squads)) {

			if(isgamesquad($squad['squadID'])) {

				$dn=mysqli_fetch_array(safe_query("SELECT name FROM `".PREFIX."squads` WHERE squadID='".$squad['squadID']."' AND gamesquad='1'"));
				$clanwars .= '<tr><td><i>'.$_language->module['squad'].': '.$dn['name'].'</i></td></tr><tr><td align="center">';
				$n = 1;
				$ergebnis=safe_query("SELECT * FROM `".PREFIX."upcoming` WHERE type='c' AND squad='".$squad['squadID']."' AND date>".time()." ORDER by date");
				$anz = mysqli_num_rows($ergebnis);
				
        if($anz) {
			$clanwars .= '<table border="0" width="98%" cellpadding="2">
				<tr>
					<td width="20%"><b>'.$_language->module['date'].'</b></td>
					<td width="20%"><b>'.$_language->module['against'].'</b></td>
					<td><b>'.$_language->module['announcement'].'</b></td>
					<td width="10%"><b>'.$_language->module['announce'].'</b></td>
				</tr>';
				
				while($ds=mysqli_fetch_array($ergebnis)) {
					$n%2 ? $bg=BG_1 : $bg=BG_2;
					$date=getformatdate($ds['date']);
					
					$anmeldung=safe_query("SELECT * FROM ".PREFIX."upcoming_announce WHERE upID='".$ds['upID']."'");
					if(mysqli_num_rows($anmeldung)) {
						$i=1;
						$players = "";
						while ($da = mysqli_fetch_array($anmeldung)) {
							if ($da['status'] == "y") $fontcolor = $wincolor;
							elseif ($da['status'] == "n") $fontcolor = $loosecolor;
							else $fontcolor = $drawcolor;
							
							if($i>1) $players.=', <a href="index.php?site=profile&amp;id='.$da['userID'].'"><font color="'.$fontcolor.'">'.strip_tags(stripslashes(getnickname($da['userID']))).'</font></a>';
							else $players.='<a href="index.php?site=profile&amp;id='.$da['userID'].'"><font color="'.$fontcolor.'">'.strip_tags(stripslashes(getnickname($da['userID']))).'</font></a>';
							$i++;
						}
					}	else $players=$_language->module['no_players_announced'];
					
					$tag = date("d", $ds['date']);
					$monat = date("m", $ds['date']);
					$yahr = date("Y", $ds['date']);
					
					$clanwars .= '<tr>
						<td bgcolor="'.$bg.'">'.$date.'</td>
						<td bgcolor="'.$bg.'"><a href="'.$ds['opphp'].'" target="_blank">'.$ds['opptag'].' / '.$ds['opponent'].'</a></td>
						<td bgcolor="'.$bg.'">'.$players.'</td>
						<td bgcolor="'.$bg.'"><a href="index.php?site=calendar&amp;action=announce&amp;upID='.$ds['upID'].'&amp;tag='.$tag.'&amp;month='.$monat.'&amp;year='.$yahr.'#event">'.$_language->module['click'].'</a></td>
					</tr>';
					$n++;
				}
				$clanwars .= '</table></td></tr>';
			} else $clanwars .= $_language->module['no_entries'].'</td></tr>';
		}
	}
	$clanwars.='<tr><td>&nbsp;</td></tr>';
	}
	unset($events);
	
	$bg1=BG_1;
	$bg2=BG_2;
	$bg3=BG_3;
	$bg4=BG_4;
		
	$events = '';
	$ergebnis=safe_query("SELECT * FROM `".PREFIX."upcoming` WHERE type='d' AND date>".time()." ORDER by date");
	$anz = mysqli_num_rows($ergebnis);
	if($anz) {
		$n=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			$n%2 ? $bg=BG_1 : $bg=BG_2;
			$events.='<tr>
				<td bgcolor="'.$bg.'">'.$ds['title'].'</td>
				<td bgcolor="'.$bg.'">'.getformatdatetime($ds['date']).'</td>
				<td bgcolor="'.$bg.'">'.getformatdatetime($ds['enddate']).'</td>
				<td bgcolor="'.$bg.'">'.$ds['location'].'</td>
				<td bgcolor="'.$bg.'"><a href="index.php?site=calendar&amp;tag='.date('d',$ds['date']).'&amp;month='.date('m',$ds['date']).'&amp;year='.date('Y',$ds['date']).'#event">'.$_language->module['click'].'</a></td>
			</tr>';
			$n++;
		}
	}
	else $events='<tr>
		<td colspan="5" bgcolor="'.$bg1.'"><i>'.$_language->module['no_events'].'</i></td>
	</tr>';

	eval ("\$loginoverview = \"".gettemplate("loginoverview")."\";");
	echo $loginoverview;
}
else echo $_language->module['you_have_to_be_logged_in'];

?>