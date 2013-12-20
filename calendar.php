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

/* define calendar functions */

function print_calendar($mon,$year) {
	global $dates, $first_day, $start_day, $_language;

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$first_day = mktime(0,0,0,$mon,1,$year);
	$start_day = date("w",$first_day);
	if($start_day == 0) $start_day = 7;
	$res = getdate($first_day);
	$month_name = $res["month"];
	$no_days_in_month = date("t",$first_day);

	//If month's first day does not start with first Sunday, fill table cell with a space
	for ($i = 1; $i <= $start_day;$i++) $dates[1][$i] = " ";

	$row = 1;
	$col = $start_day;
	$num = 1;
	while($num<=31)	{
		if ($num > $no_days_in_month) break;
		else {
			$dates[$row][$col] = $num;
			if (($col + 1) > 7)	{
				$row++;
				$col = 1;
			}
			else  $col++;

			$num++;
		}
	}

	$mon_num = date("n",$first_day);
	$temp_yr = $next_yr = $prev_yr = $year;

	$prev = $mon_num - 1;
	if ($prev<10) $prev="0".$prev;
	$next = $mon_num + 1;
	if ($next<10) $next="0".$next;

	//If January is currently displayed, month previous is December of previous year
	if ($mon_num == 1){
		$prev_yr = $year - 1;
		$prev = 12;
	}

	//If December is currently displayed, month next is January of next year
	if ($mon_num == 12)	{
		$next_yr = $year + 1;
		$next = 1;
	}

	echo'<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="'.PAGEBG.'">
    <tr>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=01">'.mb_substr($_language->module['jan'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=02">'.mb_substr($_language->module['feb'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=03">'.mb_substr($_language->module['mar'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=04">'.mb_substr($_language->module['apr'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=05">'.mb_substr($_language->module['may'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=06">'.mb_substr($_language->module['jun'], 0, 3).'</a></td>
    </tr>
    <tr>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=07">'.mb_substr($_language->module['jul'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=08">'.mb_substr($_language->module['aug'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=09">'.mb_substr($_language->module['sep'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=10">'.mb_substr($_language->module['oct'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=11">'.mb_substr($_language->module['nov'], 0, 3).'</a></td>
      <td bgcolor="'.BGCAT.'" align="center"><a class="category" href="index.php?site=calendar&amp;month=12">'.mb_substr($_language->module['dec'], 0, 3).'</a></td>
    </tr>
    </table>
    <br />';	 

	echo'<a name="event"></a><table width="100%" cellspacing="1" border="0" cellpadding="2" bgcolor="'.PAGEBG.'">
    <tr>
      <td class="title" align="center">&laquo; <a class="titlelink" href="index.php?site=calendar&amp;month='.$prev.'&amp;year='.$prev_yr.'">'.mb_substr($_language->module[strtolower(date('M', mktime(0, 0, 0, $prev, 1, $prev_yr)))], 0, 3).'</a></td>
      <td class="title" align="center" colspan="5">'.$_language->module[strtolower(date("M",$first_day))].' '.$temp_yr.'</td>
      <td class="title" align="center"><a class="titlelink" href="index.php?site=calendar&amp;month='.$next.'&amp;year='.$next_yr.'">'.mb_substr($_language->module[strtolower(date('M', mktime(0, 0, 0, $next, 1, $next_yr)))], 0, 3).'</a> &raquo;</td>
    </tr>
    <tr><td colspan="7" bgcolor="'.PAGEBG.'"></td></tr>
    <tr>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['mon'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['tue'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['wed'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['thu'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['fri'].'</td>
      <td bgcolor="'.BGCAT.'" width="14%" align="center">'.$_language->module['sat'].'</td>
      <td bgcolor="'.BGCAT.'" width="16%" align="center">'.$_language->module['sun'].'</td>
    </tr>
    <tr><td colspan="7" bgcolor="'.BG_1.'"></td></tr>
    <tr>';

	$days = date("t", mktime(0, 0, 0, $mon, 1, $year)); //days of selected month
  switch($days) {
	  case 28: 
			$end = ($start_day > 1) ? 5:4;
	  break;
	  case 29: 
			$end = 5;
	  break;
	  case 30: 
			$end = ($start_day == 7) ? 6:5;
	  break;
	  case 31: 
			$end = ($start_day > 5) ? 6:5;
	  break;
	 	default: 
			$end = 6;
  } 
	$count=0;
	for ($row=1;$row<=$end;$row++) {
		for ($col=1;$col<=7;$col++) {
			if (!isset($dates[$row][$col])) $dates[$row][$col] = " ";
			if (!strcmp($dates[$row][$col]," ")) $count++;

			$t = $dates[$row][$col];
			if($t < 10) $tag = "0$t";
			else $tag = $t;

			// DATENBANK ABRUF
			$start_date = mktime(0, 0, 0, $mon, (int)$t, $year);
			$end_date = mktime(23, 59, 59, $mon, (int)$t, $year);
			
      unset($termin);

			$ergebnis = safe_query("SELECT * FROM ".PREFIX."upcoming");
			$anz = mysqli_num_rows($ergebnis);
			if($anz) {
				$termin = '';
				while ($ds = mysqli_fetch_array($ergebnis)) {
					if($ds['type']=="d") {
						if(($start_date<=$ds['date'] && $end_date>=$ds['date']) || ($start_date>=$ds['date'] && $end_date<=$ds['enddate']) || ($start_date<=$ds['enddate'] && $end_date>=$ds['enddate']))
						$termin.='<a href="index.php?site=calendar&amp;tag='.$t.'&amp;month='.$mon.'&amp;year='.$year.'#event">'.clearfromtags($ds['short']).'</a><br />';
					}
					else {
						if($ds['date']>=$start_date && $ds['date']<=$end_date) {
							$begin = getformattime($ds['date']);
							$termin.='<a href="index.php?site=calendar&amp;tag='.$t.'&amp;month='.$mon.'&amp;year='.$year.'">'.$begin.' '.clearfromtags($ds['opptag']).'</a><br />';
						}
					}
				}
			}
			else $termin="<br /><br />";
			// DB ABRUF ENDE

			//If date is today, highlight it
			if (($t == date("j")) && ($mon == date("n")) && ($year == date("Y"))) echo'<td height="40" valign="top" bgcolor="'.BG_4.'"><b>'.$t.'</b><br />'.$termin.'</td>';
			//  If the date is absent ie after 31, print space
			else {
				if($t==' ') echo'<td height="40" valign="top" style="background-color:'.BG_1.';">&nbsp;</td>';
				else echo'<td height="40" valign="top" style="background-color:'.BG_2.';">'.$t.'<br />'.$termin.'</td>';
			}

		}
		if (($row + 1) != ($end+1)) echo'</tr><tr>';
		else echo'</tr>';
	}
	echo'<tr><td colspan="7" bgcolor="'.PAGEBG.'"></td></tr>
    <tr>
      <td bgcolor="'.BGCAT.'" colspan="7" align="center"><a class="category" href="index.php?site=calendar#event"><b>'.$_language->module['today_events'].'</b></a></td>
    </tr>
  </table>
  <br /><br />';
}

function print_termine($tag,$month,$year) {
	global $wincolor;
	global $loosecolor;
	global $drawcolor;
	global $userID;
	global $_language;

	$_language->read_module('calendar');

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$start_date = mktime(0, 0, 0, $month, $tag, $year);
	$end_date = mktime(23, 59, 59, $month, $tag, $year);
	unset($termin);

	$ergebnis = safe_query("SELECT * FROM ".PREFIX."upcoming");
	$anz = mysqli_num_rows($ergebnis);
	if($anz) {
		while ($ds=mysqli_fetch_array($ergebnis)) {
			if($ds['type']=="c") {
				if($ds['date']>=$start_date && $ds['date']<=$end_date) {
					$date=getformatdate($ds['date']);
					$time=getformattime($ds['date']);
					$squad=getsquadname($ds['squad']);
					$oppcountry="[flag]".$ds['oppcountry']."[/flag]";
					$oppcountry=flags($oppcountry);
					$opponent=$oppcountry.' <a href="'.$ds['opphp'].'" target="_blank">'.clearfromtags($ds['opptag']).' / '.clearfromtags($ds['opponent']).'</a>';
					$maps=clearfromtags($ds['maps']);
					$server=clearfromtags($ds['server']);
					$league='<a href="'.$ds['leaguehp'].'" target="_blank">'.clearfromtags($ds['league']).'</a>';
					if(isclanmember($userID)) $warinfo = cleartext($ds['warinfo']);
					else $warinfo = $_language->module['you_have_to_be_clanmember'];
					$players = "";
					$announce = "";
					$adminaction = '';
					if(isclanmember($userID) or isanyadmin($userID)) {
						$anmeldung=safe_query("SELECT * FROM ".PREFIX."upcoming_announce WHERE upID='".$ds['upID']."'");
						if(mysqli_num_rows($anmeldung)) {
							$i=1;
							while ($da = mysqli_fetch_array($anmeldung)) {
								if ($da['status'] == "y") $fontcolor = $wincolor;
								elseif ($da['status'] == "n") $fontcolor = $loosecolor;
								else $fontcolor = $drawcolor;

								if($i>1) $players.=', <a href="index.php?site=profile&amp;id='.$da['userID'].'"><font color="'.$fontcolor.'">'.getnickname($da['userID']).'</font></a>';
								else $players.='<a href="index.php?site=profile&amp;id='.$da['userID'].'"><font color="'.$fontcolor.'">'.getnickname($da['userID']).'</font></a>';
								$i++;
							}
						}
						else $players=$_language->module['no_announced'];

						if(issquadmember($userID, $ds['squad']) AND $ds['date']>time()) $announce='&#8226; <a href="index.php?site=calendar&amp;action=announce&amp;upID='.$ds['upID'].'">'.$_language->module['announce_here'].'</a>';
						else $announce = "";

						if(isclanwaradmin($userID)) $adminaction='<div align="right">
            <input type="button" onclick="MM_openBrWindow(\'clanwars.php?action=new&amp;upID='.$ds['upID'].'\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=490\')" value="'.$_language->module['add_clanwars'].'" />
            <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=editwar&amp;upID='.$ds['upID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" />
            <input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'calendar.php?action=delete&amp;upID='.$ds['upID'].'\')" value="'.$_language->module['delete'].'" /></div>';
						else $adminaction = '';
					} else $players = $_language->module['access_member'];

					$bg1=BG_1;
					$bg2=BG_2;
					$bg3=BG_3;
					$bg4=BG_4;

					eval ("\$upcoming_war_details = \"".gettemplate("upcoming_war_details")."\";");
					echo $upcoming_war_details;
				}
			}
			else {
				if(($start_date<=$ds['date'] && $end_date>=$ds['date']) || ($start_date>=$ds['date'] && $end_date<=$ds['enddate']) || ($start_date<=$ds['enddate'] && $end_date>=$ds['enddate'])) {
					$date=getformatdate($ds['date']);
					$time=getformattime($ds['date']);
					$enddate=getformatdate($ds['enddate']);
					$endtime=getformattime($ds['enddate']);
					$title=clearfromtags($ds['title']);
					$location='<a href="'.$ds['locationhp'].'" target="_blank">'.clearfromtags($ds['location']).'</a>';
					$dateinfo=cleartext($ds['dateinfo']);
					$dateinfo = toggle($dateinfo, $ds['upID']);
					$country="[flag]".$ds['country']."[/flag]";
					$country=flags($country);
					$players = "";
          
					if(isclanmember($userID)) {
						$anmeldung=safe_query("SELECT * FROM ".PREFIX."upcoming_announce WHERE upID='".$ds['upID']."'");
						if(mysqli_num_rows($anmeldung)) {
							$i=1;
							while ($da = mysqli_fetch_array($anmeldung)) {
								if ($da['status'] == "y") $fontcolor = $wincolor;
								elseif ($da['status'] == "n") $fontcolor = $loosecolor;
								else $fontcolor = $drawcolor;

								if($i>1) $players.=', <a href="index.php?site=profile&amp;id='.$da['userID'].'"><font color="'.$fontcolor.'">'.getnickname($da['userID']).'</font></a>';
								else $players.='<a href="index.php?site=profile&amp;id='.$da['userID'].'"><font color="'.$fontcolor.'">'.getnickname($da['userID']).'</font></a>';
								$i++;
							}
						}
						else $players=$_language->module['no_announced'];

						if(isclanmember($userID) AND $ds['date']>time()) $announce='&#8226; <a href="index.php?site=calendar&amp;action=announce&amp;upID='.$ds['upID'].'">'.$_language->module['announce_here'].'</a>';
						else $announce='';

						if(isclanwaradmin($userID)) $adminaction='<div align="right"><input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=editdate&amp;upID='.$ds['upID'].'\');return document.MM_returnValue" value="'.$_language->module['edit'].'" /><input type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'calendar.php?action=delete&amp;upID='.$ds['upID'].'\')" value="'.$_language->module['delete'].'" /></div>';
						else $adminaction='';
					} else {
						$players = $_language->module['access_member'];
						$announce = '';
						$adminaction = '';
					}

					$bg1=BG_1;
					$bg2=BG_2;
					$bg3=BG_3;
					$bg4=BG_4;

					eval ("\$upcoming_date_details = \"".gettemplate("upcoming_date_details")."\";");
					echo $upcoming_date_details;
				}
			}
		}
	}
	else echo $_language->module['no_entries'];
}

/* beginn processing file */

if(isset($_GET['action'])) $action = $_GET['action'];
else $action='';

if($action=="savewar") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);

	$hour = (int)$_POST['hour'];
	$minute = (int)$_POST['minute'];
	$day = (int)$_POST['day'];
	$month = (int)$_POST['month'];
	$year = (int)$_POST['year'];
	$squad = $_POST['squad'];
	$opponent = $_POST['opponent'];
	$opptag = $_POST['opptag'];
	$opphp = $_POST['opphp'];
	$oppcountry = $_POST['oppcountry'];
	$maps = $_POST['maps'];
	$server = $_POST['server'];
	$league = $_POST['league'];
	$leaguehp = $_POST['leaguehp'];
	$warinfo = $_POST['message'];
	$chID = $_POST['chID'];
	if(isset($_POST['messages'])) $messages = true;
	else $messages = false;

	$date=mktime($hour,$minute,0,$month,$day,$year);
	safe_query("INSERT INTO ".PREFIX."upcoming ( date, type, squad, opponent, opptag, opphp, oppcountry, maps, server, league, leaguehp, warinfo )
                values( '".$date."', 'c', '".$squad."', '".$opponent."', '".$opptag."', '".$opphp."', '".$oppcountry."', '".$maps."', '".$server."', '".$league."', '".$leaguehp."', '".$warinfo."' ) ");

	if(isset($chID) and $chID > 0) safe_query("DELETE FROM ".PREFIX."challenge WHERE chID='".$chID."'");

	if($messages) {
		$replace = array('%date%' => getformatdate($date),
						 '%opponent_flag%' => $oppcountry,
						 '%opp_hp%' => $opphp,
						 '%opponent%' => $opponent, 
						 '%league_hp%' => $leaguehp,
						 '%league%' => $league,
						 '%warinfo%' => $warinfo);
		$ergebnis=safe_query("SELECT userID FROM ".PREFIX."squads_members WHERE squadID='$squad'");
		$tmp_lang = new Language();
		while($ds=mysqli_fetch_array($ergebnis)) {
			$id=$ds['userID'];
			$tmp_lang->set_language(getuserlanguage($id));
			$tmp_lang->read_module('calendar');
			$title = $tmp_lang->module['clanwar_message_title'];
			$message = $tmp_lang->module['clanwar_message'];
			$message = str_replace(array_keys($replace), array_values($replace), $message);
			sendmessage($id, $title, $message);
		}
	}
	header("Location: index.php?site=calendar&tag=$day&month=$month&year=$year");
}
elseif($action=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);
	$upID = $_GET['upID'];

	safe_query("DELETE FROM ".PREFIX."upcoming WHERE upID='$upID'");
	safe_query("DELETE FROM ".PREFIX."upcoming_announce WHERE upID='$upID'");
	header("Location: index.php?site=calendar");
}
elseif($action=="saveannounce") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanmember($userID)) die($_language->module['no_access']);

	$ds=mysqli_fetch_assoc(safe_query("SELECT date FROM ".PREFIX."upcoming WHERE upID=".(int)$_POST['upID']." AND date>".time()));
	if(isset($ds['date'])) {
		$tag = date('d',$ds['date']);
		$month = date('m',$ds['date']);
		$year = date('y',$ds['date']);

		$ergebnis=safe_query("SELECT annID FROM ".PREFIX."upcoming_announce WHERE upID='".(int)$_POST['upID']."' AND userID='".$userID."'");
		if(mysqli_num_rows($ergebnis)) {
			$ds=mysqli_fetch_array($ergebnis);
			safe_query("UPDATE ".PREFIX."upcoming_announce SET status='".$_POST['status']{0}."' WHERE annID='".$ds['annID']."'");
		}
		else safe_query("INSERT INTO ".PREFIX."upcoming_announce ( upID, userID, status ) values( '".(int)$_POST['upID']."', '$userID', '".$_POST['status']{0}."' ) ");
		header("Location: index.php?site=calendar&tag=$tag&month=$month&year=$year");
	} else header("Location: index.php?site=calendar");
}
elseif($action=="saveeditdate") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);

	$hour = $_POST['hour'];
	$minute = $_POST['minute'];
	$day = $_POST['day'];
	$month = $_POST['month'];
	$year = $_POST['year'];

	$date=mktime($hour,$minute,0,$month,$day,$year);
	$enddate=mktime($_POST['endhour'],$_POST['endminute'],0,$_POST['endmonth'],$_POST['endday'],$_POST['endyear']);
	safe_query("UPDATE ".PREFIX."upcoming SET date='$date',
                                 enddate='$enddate', 
								 short='".$_POST['short']."',
								 title='".$_POST['title']."',
								 country='".$_POST['country']."',
								 location='".$_POST['location']."',
								 locationhp='".$_POST['locationhp']."',
								 dateinfo='".$_POST['message']."' WHERE upID='".$_POST['upID']."' ");

	header("Location: index.php?site=calendar&tag=$day&month=$month&year=$year");
}
elseif($action=="savedate") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);

	$date=mktime((int)$_POST['hour'],(int)$_POST['minute'],0,$_POST['month'],$_POST['day'],$_POST['year']);
	$enddate=mktime($_POST['endhour'],$_POST['endminute'],0,$_POST['endmonth'],$_POST['endday'],$_POST['endyear']);
	if($date>$enddate) {
		$temp=$date;
		$date=$enddate;
		$enddate=$temp;
		unset($temp);
	}

	safe_query("INSERT INTO ".PREFIX."upcoming ( date, type, enddate, short, title, country, location, locationhp, dateinfo  )
                values( '$date', 'd', '".$enddate."', '".$_POST['short']."', '".$_POST['title']."', '".$_POST['country']."', '".$_POST['location']."', '".$_POST['locationhp']."', '".$_POST['message']."' ) ");
	redirect("index.php?site=calendar&amp;tag=".$_POST['day']."&amp;month=".$_POST['month']."&amp;year=".$_POST['year'],"",0);
}
elseif($action=="saveeditwar") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('calendar');
	if(!isclanwaradmin($userID)) die($_language->module['no_access']);

	$upID = $_POST['upID'];
	$hour = $_POST['hour'];
	$minute = $_POST['minute'];
	$day = $_POST['day'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	$squad = $_POST['squad'];
	$opponent = $_POST['opponent'];
	$opptag = $_POST['opptag'];
	$opphp = $_POST['opphp'];
	$oppcountry = $_POST['oppcountry'];
	$maps = $_POST['maps'];
	$server = $_POST['server'];
	$league = $_POST['league'];
	$leaguehp = $_POST['leaguehp'];
	$warinfo = $_POST['message'];

	$date=mktime($hour,$minute,0,$month,$day,$year);
	safe_query("UPDATE ".PREFIX."upcoming SET date='$date',
                 type='c', 
								 squad='$squad', 
								 opponent='$opponent', 
								 opptag='$opptag', 
								 opphp='$opphp', 
								 oppcountry='$oppcountry', 
								 maps='$maps', 
								 server='$server', 
								 league='$league', 
								 leaguehp='$leaguehp', 
								 warinfo='$warinfo' WHERE upID='$upID' ");

	header("Location: index.php?site=calendar&tag=$day&month=$month&year=$year");
}
elseif($action=="addwar") {
	$_language->read_module('calendar');
	if(isclanwaradmin($userID)) {

		eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
		echo $title_calendar;

		echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=addwar\');return document.MM_returnValue" value="'.$_language->module['add_clanwar'].'" />
    <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=adddate\');return document.MM_returnValue" value="'.$_language->module['add_event'].'" /><br /><br />';

		$day='';
		$month='';
		$year='';

		for($i=1; $i<32; $i++) {
			if($i==date("d", time())) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", time())) $month.='<option value="'.$i.'" selected="selected">'.date("M", time()).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", time())) $year.='<option value="'.$i.'" selected="selected">'.date("Y", time()).'</option>';
			else $year.='<option value="'.$i.'">'.$i.'</option>';
		}
		$squads=getgamesquads();
		$hours="20";
		$minutes="00";

		$opphp="http://";

		$chID=0;

		$countries='';
		$ergebnis = safe_query("SELECT * FROM `".PREFIX."countries` ORDER BY country");
		while($ds = mysqli_fetch_array($ergebnis)) {
			$countries .= '<option value="'.$ds['short'].'">'.$ds['country'].'</option>';
		}

		if(isset($_GET['chID'])) {

			$chID = (int)$_GET['chID'];
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."challenge WHERE chID='".$chID."'");
			$ds=mysqli_fetch_array($ergebnis);
			$day=str_replace("<option selected=\"selected\">", "<option>", $day);
			$day=str_replace("<option>".date("d", $ds['cwdate'])."</option>", "<option selected=\"selected\">".date("d", $ds['cwdate'])."</option>", $day);

			$month=str_replace(" selected=\"selected\"", "", $month);
			$month=str_replace(' value="'.date("n", $ds['cwdate']).'"', ' value="'.date("n", $ds['cwdate']).'" selected="selected"', $month);

			$year=str_replace(" selected=\"selected\"", "", $year);
			$year=str_replace(' value="'.date("Y", $ds['cwdate']).'"', ' value="'.date("Y", $ds['cwdate']).'" selected="selected"', $year);

			$hours=date("H", $ds['cwdate']);
			$minutes=date("i", $ds['cwdate']);

			$squads=str_replace(" selected=\"selected\"", "", $squads);
			$squads=str_replace('<option value="'.$ds['squadID'].'">', '<option value="'.$ds['squadID'].'" selected="selected">', $squads);

			$map = $ds['map'];
			$server = $ds['server'];
			$opponent = $ds['opponent'];
			$league = $ds['league'];
			$info = $ds['info'];

			$countries=str_replace(" selected=\"selected\"", "", $countries);
			$countries=str_replace('<option value="'.$ds['oppcountry'].'">', '<option value="'.$ds['oppcountry'].'" selected="selected">', $countries);

			$opphp=$ds['opphp'];
		} else {
			$map = '';
			$server = '';
			$opponent = '';
			$league = '';
			$info = '';
		}

		$bg1=BG_1;
		eval ("\$upcoming_war_new = \"".gettemplate("upcoming_war_new")."\";");
		echo $upcoming_war_new;
	}
	else redirect('index.php?site=calendar', $_language->module['no_access']);
}
elseif($action=="editwar") {
	$_language->read_module('calendar');	
	if(isclanwaradmin($userID)) {

		eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
		echo $title_calendar;

		echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=addwar\');return document.MM_returnValue" value="'.$_language->module['add_clanwar'].'" />
    <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=adddate\');return document.MM_returnValue" value="'.$_language->module['add_event'].'" /><br /><br />';

		$day='';
		$month='';
		$year='';

		$upID = $_GET['upID'];
		$ds=mysqli_fetch_array(safe_query("SELECT * FROM ".PREFIX."upcoming WHERE upID='$upID'"));
		for($i=1; $i<32; $i++) {
			if($i==date("d", $ds['date'])) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", $ds['date'])) $month.='<option value="'.$i.'" selected="selected">'.date("M", $ds['date']).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", $ds['date'])) $year.='<option selected="selected">'.$i.'</option>';
			else $year.='<option>'.$i.'</option>';
		}
		$squads = getgamesquads();
		$squads = str_replace('value="'.$ds['squad'].'"', 'value="'.$ds['squad'].'" selected="selected"', $squads);
		$league = htmlspecialchars($ds['league']);
		$leaguehp = htmlspecialchars($ds['leaguehp']);
		$opponent = htmlspecialchars($ds['opponent']);
		$opptag = htmlspecialchars($ds['opptag']);
		$opphp = htmlspecialchars($ds['opphp']);
		$maps = htmlspecialchars($ds['maps']);
		$server = htmlspecialchars($ds['server']);
		$warinfo = htmlspecialchars($ds['warinfo']);
		$countries='';
		$ergebnis = safe_query("SELECT * FROM `".PREFIX."countries` ORDER BY country");
		while($ds = mysqli_fetch_array($ergebnis)) {
			$countries .= '<option value="'.$ds['short'].'">'.$ds['country'].'</option>';
		}
		$countries = str_replace('value="'.$ds['oppcountry'].'"', 'value="'.$ds['oppcountry'].'" selected="selected"', $countries);
		$hour = date("H", $ds['date']);
		$minutes = date("i", $ds['date']);

		$bg1=BG_1;
		eval ("\$upcoming_war_edit = \"".gettemplate("upcoming_war_edit")."\";");
		echo $upcoming_war_edit;
	}
	else redirect('index.php?site=calendar', $_language->module['no_access']);
}
elseif($action=="adddate") {
	$_language->read_module('calendar');
	if(isclanwaradmin($userID)) {

		eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
		echo $title_calendar;

		echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=addwar\');return document.MM_returnValue" value="'.$_language->module['add_clanwar'].'" />
    <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=adddate\');return document.MM_returnValue" value="'.$_language->module['add_event'].'" /><br /><br />';

		$day = '';
		$month = '';
		$year = '';

		for($i=1; $i<32; $i++) {
			if($i==date("d", time())) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", time())) $month.='<option value="'.$i.'" selected="selected">'.date("M", time()).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", time())) $year.='<option value="'.$i.'" selected="selected">'.date("Y", time()).'</option>';
			else $year.='<option value="'.$i.'">'.$i.'</option>';
		}
		$squads=getgamesquads();

		$countries='';
		$ergebnis = safe_query("SELECT * FROM `".PREFIX."countries` ORDER BY country");
		while($ds = mysqli_fetch_array($ergebnis)) {
			$countries .= '<option value="'.$ds['short'].'">'.$ds['country'].'</option>';
		}

		$bg1=BG_1;
		eval ("\$upcoming_date_new = \"".gettemplate("upcoming_date_new")."\";");
		echo $upcoming_date_new;
	}
	else redirect('index.php?site=calendar', $_language->module['no_access']);
}
elseif($action=="editdate") {
	$_language->read_module('calendar');
	if(isclanwaradmin($userID)) {

		eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
		echo $title_calendar;

		echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=addwar\');return document.MM_returnValue" value="'.$_language->module['add_clanwar'].'" />
    <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=adddate\');return document.MM_returnValue" value="'.$_language->module['add_event'].'" /><br /><br />';

		$day='';
		$month='';
		$year='';
		$endday='';
		$endmonth='';
		$endyear='';

		$upID = $_GET['upID'];
		$ds=mysqli_fetch_array(safe_query("SELECT * FROM ".PREFIX."upcoming WHERE upID='$upID'"));
		for($i=1; $i<32; $i++) {
			if($i==date("d", $ds['date'])) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", $ds['date'])) $month.='<option value="'.$i.'" selected="selected">'.date("M", $ds['date']).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", $ds['date'])) $year.='<option selected="selected">'.$i.'</option>';
			else $year.='<option>'.$i.'</option>';
		}
		for($i=1; $i<32; $i++) {
			if($i==date("d", $ds['enddate'])) $endday.='<option selected="selected">'.$i.'</option>';
			else $endday.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", $ds['enddate'])) $endmonth.='<option value="'.$i.'" selected="selected">'.date("M", $ds['enddate']).'</option>';
			else $endmonth.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<2016; $i++) {
			if($i==date("Y", $ds['enddate'])) $endyear.='<option selected="selected">'.$i.'</option>';
			else $endyear.='<option>'.$i.'</option>';
		}
		$countries='';
		$ergebnis = safe_query("SELECT * FROM `".PREFIX."countries` ORDER BY country");
		while($ds = mysqli_fetch_array($ergebnis)) {
			$countries .= '<option value="'.$ds['short'].'">'.$ds['country'].'</option>';
		}
		$countries=str_replace(' selected="selected"', '', $countries);
		$countries=str_replace('value="'.$ds['country'].'"', 'value="'.$ds['country'].'" selected="selected"', $countries);

		$hour=date("H", $ds['date']);
		$endhour=date("H", $ds['enddate']);
		$minute=date("i", $ds['date']);
		$endminute=date("i", $ds['enddate']);

		$short = htmlspecialchars($ds['short']);
		$title = htmlspecialchars($ds['title']);
		$location = htmlspecialchars($ds['location']);
		$locationhp = htmlspecialchars($ds['locationhp']);
		$dateinfo = htmlspecialchars($ds['dateinfo']);
		

		$bg1=BG_1;
		eval ("\$upcoming_date_edit = \"".gettemplate("upcoming_date_edit")."\";");
		echo $upcoming_date_edit;
	}
	else redirect('index.php?site=calendar', $_language->module['no_access']);
}
elseif($action=="announce" AND isclanmember($userID)) {

	$_language->read_module('calendar');

	eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
	echo $title_calendar;

	if(isset($_GET['upID'])) {

		$upID = (int)$_GET['upID'];
		
		eval ("\$upcomingannounce = \"".gettemplate("upcomingannounce")."\";");
		echo $upcomingannounce;
	}
}
else {

	$_language->read_module('calendar');

	eval ("\$title_calendar = \"".gettemplate("title_calendar")."\";");
	echo $title_calendar;

	if(isclanwaradmin($userID)) echo '<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=addwar\');return document.MM_returnValue" value="'.$_language->module['add_clanwar'].'" />
  <input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=calendar&amp;action=adddate\');return document.MM_returnValue" value="'.$_language->module['add_event'].'" /><br /><br />';
	
	if(isset($_GET['month'])) $month = (int)$_GET['month'];
	else $month = date("m");
	
  	if(isset($_GET['year'])) $year = (int)$_GET['year'];
	else $year = date("Y");

	if(isset($_GET['tag'])) $tag = (int)$_GET['tag'];
	else $tag = date("d");

	print_calendar($month,$year);
	print_termine($tag, $month, $year);
}

?>