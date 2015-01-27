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
if (isset($site)) $_language->read_module('clanwars');

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = "";
if($action=="new") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	if(isset($_GET['upID'])) $upID = $_GET['upID'];

	if(isclanwaradmin($userID)) {
		$squads=getgamesquads();
		$jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);

		$games="";
    $hometeam="";
    
    $gamesa=safe_query("SELECT * FROM ".PREFIX."games ORDER BY name");
		while($ds=mysql_fetch_array($gamesa)) {
			$games.='<option value="'.$ds['tag'].'">'.$ds['name'].'</option>';
		}

		$gamesquads=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad='1' ORDER BY sort");
		while($ds=mysql_fetch_array($gamesquads)) {
			$hometeam.='<option value="0">'.$ds['name'].'</option>';
			$squadmembers=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE squadID='$ds[squadID]' ORDER BY sort");
			while($dm=mysql_fetch_array($squadmembers)) {
				$hometeam.='<option value="'.$dm['userID'].'">&nbsp; - '.getnickname($dm['userID']).'</option>';
			}
			$hometeam.='<option value="0" disabled="disabled">-----</option>';
		}

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
		for($i=2000; $i<=date('Y', strtotime('+1 year')); $i++) {
			if($i==date("Y", time())) $year.='<option value="'.$i.'" selected="selected">'.date("Y", time()).'</option>';
			else $year.='<option value="'.$i.'">'.$i.'</option>';
		}

		$leaguehp="http://";
		$opphp="http://";
		$linkpage="http://";
		$server = "";
		$league = "";
		$opponent = "";
		$opptag = "";
		if(isset($upID)) {
			$ergebnis=safe_query("SELECT * FROM ".PREFIX."upcoming WHERE upID='$upID'");
			$ds=mysql_fetch_array($ergebnis);
			$league=$ds['league'];
			if($ds['leaguehp'] != $leaguehp) $leaguehp=$ds['leaguehp'];
			$opponent=$ds['opponent'];
			$opptag=$ds['opptag'];
			if($ds['opphp'] != $opphp) $opphp=$ds['opphp'];
			$countries=str_replace(" selected=\"selected\"", "", $countries);
			$countries=str_replace('value="'.$ds['oppcountry'].'"', 'value="'.$ds['oppcountry'].'" selected="selected"', $countries);

			$squads=str_replace(" selected=\"selected\"", "", $squads);
			$squads=str_replace('value="'.$ds['squad'].'"', 'value="'.$ds['squad'].'" selected="selected"', $squads);
			$server = $ds['server'];
			$day=str_replace(" selected=\"selected\"", "", $day);
			$day=str_replace('<option>'.date("j", $ds['date']).'</option>', '<option selected="selected">'.date("j", $ds['date']).'</option>', $day);
			$month=str_replace(" selected=\"selected\"", "", $month);
			$month=str_replace('value="'.date("n", $ds['date']).'"', 'value="'.date("n", $ds['date']).'" selected="selected"', $month);
			$year=str_replace(" selected=\"selected\"", "", $year);
			$year=str_replace('value="'.date("Y", $ds['date']).'"', 'value="'.date("Y", $ds['date']).'" selected="selected"', $year);
		}

		$bg1=BG_1;
		eval ("\$clanwar_new = \"".gettemplate("clanwar_new")."\";");
		echo $clanwar_new;
	}
	else redirect('index.php?site=clanwars', 'no access!');
}
elseif($action=="save") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	if(isset($_POST['hometeam'])) $hometeam = $_POST['hometeam'];
	if(isset($_POST['squad'])) $squad = $_POST['squad'];
	else $squad = '';
	$game = $_POST['game'];
	$league = $_POST['league'];
	$leaguehp = $_POST['leaguehp'];
	$opponent = $_POST['opponent'];
	$opptag = $_POST['opptag'];
	$oppcountry = $_POST['oppcountry'];
	$opphp = $_POST['opphp'];
	$oppteam = $_POST['oppteam'];
	$server = $_POST['server'];
	$hltv = $_POST['hltv'];
	$report = $_POST['message'];
	$comments = $_POST['comments'];
	$linkpage = $_POST['linkpage'];
	if(isset($_POST['news'])) $news=$_POST['news'];
  

	// v1.0 -- EXTENDED CLANWAR RESULTS
	if(isset($_POST['map_name'])) $maplist = $_POST['map_name'];
	if(isset($_POST['map_result_home'])) $homescr = $_POST['map_result_home'];
	if(isset($_POST['map_result_opp'])) $oppscr = $_POST['map_result_opp'];

	$maps = array();
	if(!empty($maplist)) {
		if(is_array($maplist)) {
			foreach($maplist as $map) {
				$maps[]=stripslashes($map);
			}
		}
	}
	$backup_theMaps = serialize($maps);
	if(function_exists("mysql_real_escape_string")) {
		$theMaps = mysql_real_escape_string($backup_theMaps);
	}
	else{
		$theMaps = addslashes($backup_theMaps);
	}
	$scores = array();
	if(!empty($homescr)) {
		if(is_array($homescr)) {
			foreach($homescr as $result) {
				$scores[]=$result;
			}
		}
	}
	$theHomeScore = serialize($scores);
	
	$results = array();
	if(!empty($oppscr)) {
		if(is_array($oppscr)) {
			foreach($oppscr as $result) {
				$results[]=$result;
			}
		}
	}
	$theOppScore = serialize($results);
	
	$team=array();
	if(is_array($hometeam)) {
		foreach($hometeam as $player) {
			if(!in_array($player, $team)) $team[]=$player;
		}
	}
	$home_string = serialize($team);

	$date=mktime(0,0,0,$month,$day,$year);

	safe_query("INSERT INTO ".PREFIX."clanwars ( date, squad, game, league, leaguehp, opponent, opptag, oppcountry, opphp, maps, hometeam, oppteam, server, hltv, homescore, oppscore, report, comments, linkpage)
                 VALUES( '$date', '$squad', '$game', '".$league."', '$leaguehp', '".$opponent."', '".$opptag."', '$oppcountry', '$opphp', '".$theMaps."', '$home_string', '$oppteam', '$server', '$hltv', '$theHomeScore', '$theOppScore', '".$report."', '$comments', '$linkpage' ) ");

	$cwID=mysql_insert_id();
	$date=date("d.m.Y", $date);

	// INSERT CW-NEWS
	if(isset($news)) {
	 	$_language->read_module('news',true);
	 	$_language->read_module('bbcode', true);
	 	
		safe_query("INSERT INTO ".PREFIX."news (date, poster, saved, cwID) VALUES ('".time()."', '$userID', '0', '$cwID')");
		$newsID=mysql_insert_id();
		
		$rubrics = '';
		$newsrubrics=safe_query("SELECT rubricID, rubric FROM ".PREFIX."news_rubrics ORDER BY rubric");
		while($dr=mysql_fetch_array($newsrubrics)) {
			$rubrics.='<option value="'.$dr['rubricID'].'">'.$dr['rubric'].'</option>';
		}

		$count_langs = 0;
		$lang=safe_query("SELECT lang, language FROM ".PREFIX."news_languages ORDER BY language");
		$langs='';
		while($dl=mysql_fetch_array($lang)) {
			$langs.="news_languages[".$count_langs."] = new Array();\nnews_languages[".$count_langs."][0] = '".$dl['lang']."';\nnews_languages[".$count_langs."][1] = '".$dl['language']."';\n";
			$count_langs++;
		}

		$squad=getsquadname($squad);
		$link1=$opptag;
		$url1=$opphp;
		$link2=$league;
		$url2=$leaguehp;
		$url3="http://";
		$url4="http://";
		$link3="";
		$link4="";
		$window1_new = 'checked="checked"';
		$window1_self = '';
		$window2_new = 'checked="checked"';
		$window2_self = '';
		$window3_new = 'checked="checked"';
		$window3_self = '';
		$window4_new = 'checked="checked"';
		$window4_self = '';

		// v1.0 -- PREPARE CW-NEWS OUTPUT
		$maps = unserialize($backup_theMaps);
		$scoreHome = unserialize($theHomeScore);
		$scoreOpp = unserialize($theOppScore);
		$homescr=array_sum($scoreHome);
		$oppscr=array_sum($scoreOpp);

		if($homescr>$oppscr) {
			$results='[color='.$wincolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
			$result2='won';
		}
		elseif($homescr<$oppscr) {
			$results='[color='.$loosecolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
			$result2='lost';
		}
		else {
			$results='[color='.$drawcolor.'][b]'.$homescr.':'.$oppscr.'[/b][/color]';
			$result2='draw';
		}
		
		$headline1='War '.stripslashes($squad).' vs. '.stripslashes($opponent).' '.$result2;
		if($url1!='http://' AND !(empty($url1))) $opponent='[url='.$opphp.'][b]'.$opptag.' / '.$opponent.'[/b][/url]';
		else $opponent='[b]'.$opptag.' / '.$opponent.'[/b]';
		if($url2!='http://' AND !(empty($url2))) $league='[url='.$leaguehp.']'.$league.'[/url]';
		// v1.0 -- CREATE CW-NEWS EXTENDED RESULTS
		if(is_array($maps)) {
			$d=0;
			$results_ext='[TOGGLE=Results (extended)]';
			foreach($maps as $maptmp) {
				$map=stripslashes($maptmp);
			 	$score = "";
				if($scoreHome[$d] > $scoreOpp[$d]) $score.='<td>[color='.$wincolor.'][b]'.$scoreHome[$d].'[/b][/color] : [color='.$loosecolor.'][b]'.$scoreOpp[$d].'[/b][/color]</td>';
				elseif($scoreHome[$d] < $scoreOpp[$d]) $score.='<td>[color='.$loosecolor.'][b]'.$scoreHome[$d].'[/b][/color] : [color='.$wincolor.'][b]'.$scoreOpp[$d].'[/b][/color]</td>';
				else $score.='<td>[color='.$drawcolor.'][b]'.$scoreHome[$d].'[/b][/color] : [color='.$drawcolor.'][b]'.$scoreOpp[$d].'[/b][/color]</td>';
				$d++;
				eval ("\$news_cw_results = \"".gettemplate("news_cw_results")."\";");
				$results_ext.=$news_cw_results;
				unset($score);
			}
			$results_ext.='[/TOGGLE]';
		}

		if(!empty($report)) {
			$more1='[TOGGLE=Report]'.getforminput($report).'[/TOGGLE]';
		}
		$home = "";
		if(is_array($team)) {
			$n=1;
			foreach($team as $id) {
				if(!empty($id)) {
					if($n>1) $home.=', <a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
					else $home='<a href="index.php?site=profile&amp;id='.$id.'">'.getnickname($id).'</a>';
					$n++;
				}
			}
		}
    
  	$_languagepagedefault = new Language;
    $_languagepagedefault->set_language($rss_default_language);
    $_languagepagedefault->read_module('clanwars');
		$message=$_language->module['clanwar_against'].' [flag]'.$oppcountry.'[/flag] '.stripslashes($opponent).' '.$_language->module['on'].' '.$date.'
		
'.$_language->module['league'].': '.stripslashes($league).'
'.$_language->module['result'].': '.$results.'
'.$results_ext.'
'.stripslashes($myclantag).' '.$_language->module['team'].': '.stripslashes($home).'
'.stripslashes($opptag).' '.$_language->module['team'].': '.stripslashes($oppteam).'

'.$more1.'
<a href="index.php?site=clanwars_details&#38;&#97;&#109;&#112;&#59;cwID='.$cwID.'">'.$_languagepagedefault->module['clanwar_details'].'</a>';
		$i = 0;
		$message_vars = "message[".$i."] = '".js_replace($message)."';\n";
		$headline_vars = "headline[".$i."] = '".js_replace(htmlspecialchars($headline1))."';\n";
		$langs_vars = "langs[".$i."] = '$default_language';\n";
		$langcount = 1;
		$selects = "";
		for($i = 1; $i <= $count_langs; $i++) {
			if($i == $langcount) $selects .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			else $selects .= '<option value="'.$i.'">'.$i.'</option>';
		}
		$intern = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';
		$topnews = '<option value="0" selected="selected">'.$_language->module['no'].'</option><option value="1">'.$_language->module['yes'].'</option>';
		
		$rubrics='';
		$newsrubrics=safe_query("SELECT rubricID, rubric FROM ".PREFIX."news_rubrics ORDER BY rubric");
		while($dr=mysql_fetch_array($newsrubrics)) {
			$rubrics.='<option value="'.$dr['rubricID'].'">'.$dr['rubric'].'</option>';
		}
		$bg1=BG_1;
		
		$comments='<option value="0">'.$_language->module['no_comments'].'</option><option value="1">'.$_language->module['user_comments'].'</option><option value="2" selected="selected">'.$_language->module['visitor_comments'].'</option>';
		
		eval ("\$addbbcode = \"".gettemplate("addbbcode")."\";");
		eval ("\$addflags = \"".gettemplate("flags")."\";");
		$_language->read_module('news');
		eval ("\$news_post = \"".gettemplate("news_post")."\";");
		echo $news_post;

	}
	else echo'<script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
  <link href="_stylesheet.css" rel="stylesheet" type="text/css">
  <center><br /><br /><br /><br />
  <b>'.$_language->module['clanwar_saved'].'.</b><br /><br />
  <input type="button" onclick="MM_openBrWindow(\'upload.php?cwID='.$cwID.'\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['upload_screenshot'].'" />
  <input type="button" onclick="javascript:self.close()" value="'.$_language->module['close_window'].'" /></center>';
}
elseif($action=="edit") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');
	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$pagebg=PAGEBG;
	$border=BORDER;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	$cwID = $_GET['cwID'];

	if(isclanwaradmin($userID)) {
		$squads=getgamesquads();
		$jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);

		$games="";
    $maps="";
    $hometeam="";
    $day='';
		$month='';
		$year='';
    
    $ds=mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."clanwars WHERE cwID='$cwID'"));

		$gamesa=safe_query("SELECT tag, name FROM ".PREFIX."games ORDER BY name");
		while($dv=mysql_fetch_array($gamesa)) {
			$games.='<option value="'.$dv['tag'].'">'.$dv['name'].'</option>';
		}

		
    
    for($i=1; $i<32; $i++) {
			if($i==date("d", $ds['date'])) $day.='<option selected="selected">'.$i.'</option>';
			else $day.='<option>'.$i.'</option>';
		}
		for($i=1; $i<13; $i++) {
			if($i==date("n", $ds['date'])) $month.='<option value="'.$i.'" selected="selected">'.date("M", $ds['date']).'</option>';
			else $month.='<option value="'.$i.'">'.date("M", mktime(0,0,0,$i,1,2000)).'</option>';
		}
		for($i=2000; $i<=date('Y', strtotime('+1 year')); $i++) {
			if($i==date("Y", $ds['date'])) $year.='<option selected="selected">'.$i.'</option>';
			else $year.='<option>'.$i.'</option>';
		}
		$games=str_replace('value="'.$ds['game'].'"', 'value="'.$ds['game'].'" selected="selected"', $games);
		$squads=getgamesquads();
		$squads=str_replace('value="'.$ds['squad'].'"', 'value="'.$ds['squad'].'" selected="selected"', $squads);
		$league=htmlspecialchars($ds['league']);
		$leaguehp=htmlspecialchars($ds['leaguehp']);
		$opponent=htmlspecialchars($ds['opponent']);
		$opptag=htmlspecialchars($ds['opptag']);
		$countries=str_replace('value="at" selected="selected"', 'value="at"', $countries);
		$countries=str_replace('value="'.$ds['oppcountry'].'"', 'value="'.$ds['oppcountry'].'" selected="selected"', $countries);
		$opphp=htmlspecialchars($ds['opphp']);
		$oppteam=htmlspecialchars($ds['oppteam']);
		$server=htmlspecialchars($ds['server']);
		$hltv=htmlspecialchars($ds['hltv']);
		$linkpage=htmlspecialchars($ds['linkpage']);
		$report=htmlspecialchars($ds['report']);
		$linkpage=htmlspecialchars($ds['linkpage']);

		// map-output, v1.0
		$map = unserialize($ds['maps']);
		$theHomeScore = unserialize($ds['homescore']);
		$theOppScore = unserialize($ds['oppscore']);
		$i=0;
		for($i=0; $i<count($map); $i++) {
			
      $maps.='
      <tr>
        <td width="15%"><input type="hidden" name="map_id[]" value="'.$i.'" />map #'.($i+1).'</td>
				<td width="25%"><input type="text" name="map_name[]" value="'.getinput($map[$i]).'" size="35" /></td>
				<td width="20%"><input type="text" name="map_result_home[]" value="'.$theHomeScore[$i].'" size="3" /></td>
				<td width="20%"><input type="text" name="map_result_opp[]" value="'.$theOppScore[$i].'" size="3" /></td>
				<td width="25%"><input type="checkbox" name="delete['.$i.']" value="1" /> '.$_language->module['delete'].'</td>
			</tr>';
		}

		$gamesquads=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad='1' ORDER BY sort");
		while($dq=mysql_fetch_array($gamesquads)) {
			$hometeam.='<option value="0">'.$dq['name'].'</option>';
			$squadmembers=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE squadID='$dq[squadID]' ORDER BY sort");
			while($dm=mysql_fetch_array($squadmembers)) {
				$hometeam.='<option value="'.$dm['userID'].'">&nbsp; - '.getnickname($dm['userID']).'</option>';
			}
			$hometeam.='<option value="0">&nbsp;</option>';
		}

		if(!empty($ds['hometeam'])) {
			$array = unserialize($ds['hometeam']);
			foreach($array as $id) {
				if(!empty($id)) $hometeam=str_replace('value="'.$id.'"', 'value="'.$id.'" selected="selected"', $hometeam);
			}
		}

		$comments='<option value="0">'.$_language->module['disable_comments'].'</option><option value="1">'.$_language->module['user_comments'].'</option><option value="2">'.$_language->module['visitor_comments'].'</option>';
		$comments=str_replace('value="'.$ds['comments'].'"', 'value="'.$ds['comments'].'" selected="selected"', $comments);

		$bg1=BG_1;
		eval ("\$clanwar_edit = \"".gettemplate("clanwar_edit")."\";");
		echo $clanwar_edit;
	}
	else redirect('index.php?site=clanwars', $_language->module['no_access']);
}
elseif($action=="saveedit") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);

	$cwID = $_POST['cwID'];
	$month = $_POST['month'];
	$day = $_POST['day'];
	$year = $_POST['year'];
	if(isset($_POST['hometeam'])) $hometeam = $_POST['hometeam'];
	else $hometeam = array();
	$squad = $_POST['squad'];
	$game = $_POST['game'];
	$league = $_POST['league'];
	$leaguehp = $_POST['leaguehp'];
	$opponent = $_POST['opponent'];
	$opptag = $_POST['opptag'];
	$oppcountry = $_POST['oppcountry'];
	$opphp = $_POST['opphp'];
	$oppteam = $_POST['oppteam'];
	$server = $_POST['server'];
	$hltv = $_POST['hltv'];
	$report = $_POST['message'];
	$comments = $_POST['comments'];
	$linkpage = $_POST['linkpage'];
	$maplist = $_POST['map_name'];
	$homescr = $_POST['map_result_home'];
	$oppscr = $_POST['map_result_opp'];
	if(isset($_POST['delete'])) $delete = $_POST['delete'];
	else $delete = array();
	
	// v1.0 -- MAP-REMOVAL
	$theMaps = array();
	$theHomeScore = array();
	$theOppScore = array();
	
	if(is_array($maplist)){
		foreach($maplist as $key=>$map) {
			if(!isset($delete[$key])) {
				$theMaps[]=stripslashes($map);
				$theHomeScore[]=$homescr[$key];
				$theOppScore[]=$oppscr[$key];
			}
		}
	}
	if(function_exists("mysql_real_escape_string")) {
		$theMaps = mysql_real_escape_string(serialize($theMaps));
	}
	else{
		$theMaps = addslashes(serialize($theMaps));
	}
	$theHomeScore = serialize($theHomeScore);
	$theOppScore = serialize($theOppScore);

	echo'<script src="js/bbcode.js" language="jscript" type="text/javascript"></script>
  <link href="_stylesheet.css" rel="stylesheet" type="text/css">';

	$date=mktime(0,0,0,$month,$day,$year);
	$team=array();
	if(is_array($hometeam)) {
		foreach($hometeam as $player) {
			if(!in_array($player, $team)) $team[]=$player;
		}
	}
	$home_string = serialize($team);

	safe_query("UPDATE ".PREFIX."clanwars SET date='$date',
                 squad='$squad',
								 game='$game',
								 league='".$league."',
								 leaguehp='$leaguehp',
								 opponent='".$opponent."',
								 opptag='".$opptag."',
								 oppcountry='$oppcountry',
								 opphp='$opphp',
								 maps='".$theMaps."',
								 hometeam='".$home_string."',
								 oppteam='".$oppteam."',
								 server='$server',
								 hltv='$hltv',
								 homescore='$theHomeScore',
								 oppscore='$theOppScore',
								 report='".$report."',
								 comments='$comments',
                 linkpage='$linkpage' WHERE cwID='$cwID'");

	echo'<center><br /><br /><br /><br />
  <b>'.$_language->module['clanwar_updated'].'</b><br /><br />
  <input type="button" onclick="MM_openBrWindow(\'upload.php?cwID='.$cwID.'\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['upload_screenshot'].'" />
  <input type="button" onclick="javascript:self.close()" value="'.$_language->module['close_window'].'" /></center>';
}
elseif($action=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die($_language->module['no_access']);
	if(isset($_POST['cwID'])) $cwID = $_POST['cwID'];
	if(!isset($cwID)) $cwID = $_GET['cwID'];
	$ergebnis=safe_query("SELECT screens FROM ".PREFIX."clanwars WHERE cwID='$cwID'");
	$ds=mysql_fetch_array($ergebnis);
	$screens=explode("|", $ds['screens']);
	$filepath = "./images/clanwar-screens/";
	if(is_array($screens)) {
		foreach($screens as $screen) {
			if(!empty($screen)) {
				if(file_exists($filepath.$screen)) @unlink($filepath.$screen);
			}
		}
	}
	safe_query("DELETE FROM ".PREFIX."clanwars WHERE cwID='$cwID'");
	header("Location: index.php?site=clanwars");
}
elseif(isset($_POST['quickactiontype'])=="delete") {
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	$_language->read_module('clanwars');

	if(!isanyadmin($userID)) die('no access!');
	if(isset($_POST['cwID'])){
		$cwID = $_POST['cwID'];
		foreach($cwID as $id) {
			$ergebnis=safe_query("SELECT screens FROM ".PREFIX."clanwars WHERE cwID='$id'");
			$ds=mysql_fetch_array($ergebnis);
			$screens=explode("|", $ds['screens']);
			$filepath = "./images/clanwar-screens/";
			if(is_array($screens)) {
				foreach($screens as $screen) {
					if(!empty($screen)) {
						if(file_exists($filepath.$screen)) @unlink($filepath.$screen);
					}
				}
			}
	
			safe_query("DELETE FROM ".PREFIX."clanwars WHERE cwID='$id'");
			safe_query("DELETE FROM ".PREFIX."comments WHERE parentID='$id' AND type='cw'");
		}
	}
	header("Location: index.php?site=clanwars");
}
elseif($action=="stats") {
	eval ("\$title_clanwars = \"".gettemplate("title_clanwars")."\";");
	echo $title_clanwars;

	echo'<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars\');return document.MM_returnValue" value="'.$_language->module['show_clanwars'].'" />';
	
  echo'<h2>'.$_language->module['clan_stats'].'</h2>';

	$bg1=BG_1;
  $bg2=BG_2;
  $totalHomeScore="";
  $totalOppScore="";
  $allWon="";
  $allLose="";
  $allDraw="";
  $totaldrawall="";
  $totalwonall="";
  $totalloseall="";
  
  // TOTAL

	$dp=safe_query("SELECT * FROM ".PREFIX."clanwars");
	// clanwars gesamt
	$totaltotal=mysql_num_rows($dp);

	while($cwdata = mysql_fetch_array($dp)) {
		// total home points
		$totalhomeqry=safe_query("SELECT homescore FROM ".PREFIX."clanwars WHERE cwID='$cwdata[cwID]'");
		while($theHomeData = mysql_fetch_array($totalhomeqry)) {
			$totalHomeScore+=array_sum(unserialize($theHomeData['homescore']));
			$theHomeScore=array_sum(unserialize($theHomeData['homescore']));
		}
		// total opponent points
		$totaloppqry=safe_query("SELECT oppscore FROM ".PREFIX."clanwars WHERE cwID='$cwdata[cwID]'");
		while($theOppData = mysql_fetch_array($totaloppqry)) {
			$totalOppScore+=array_sum(unserialize($theOppData['oppscore']));
			$theOppScore=array_sum(unserialize($theOppData['oppscore']));
		}

		//
		if($allWon=='') $allWon=0;
		if($allLose=='') $allLose=0;
		if($allDraw=='') $allDraw=0;

		//
		if($theHomeScore > $theOppScore) $totalwonall++;
		if($theHomeScore < $theOppScore) $totalloseall++;
		if($theHomeScore == $theOppScore) $totaldrawall++;
	}
	$totalhome=$totalHomeScore;
	$totalopp=$totalOppScore;

	if(!$totalwonall) $totalwonall=0;
	if(!$totalloseall) $totalloseall=0;
	if(!$totaldrawall) $totaldrawall=0;
	if(!$totalhome) $totalhome=0;
	if(!$totalopp) $totalopp=0;

	$totalwonperc=percent($totalwonall, $totaltotal, 2);
	if($totalwonperc) $totalwon=$totalwonperc.'%<br /><img src="images/icons/won.gif" width="30" height="'.round($totalwonperc, 0).'" border="1" alt="'.$_language->module['won'].'" />';
	else $totalwon=0;

	$totalloseperc=percent($totalloseall, $totaltotal, 2);
	if($totalloseperc) $totallost=$totalloseperc.'%<br /><img src="images/icons/lost.gif" width="30" height="'.round($totalloseperc, 0).'" border="1" alt="'.$_language->module['lost'].'" />';
	else $totallost=0;

	$totaldrawperc=percent($totaldrawall, $totaltotal, 2);
	if($totaldrawperc) $totaldraw=$totaldrawperc.'%<br /><img src="images/icons/draw.gif" width="30" height="'.round($totaldrawperc, 0).'" border="1" alt="'.$_language->module['draw'].'" />';
	else $totaldraw=0;

	$squad=$_language->module['clan'];

	eval ("\$clanwars_stats_total = \"".gettemplate("clanwars_stats_total")."\";");
	echo $clanwars_stats_total;

	// SQUADS

	$squads=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad='1' ORDER BY sort");
	if(mysql_num_rows($squads)) {
		while($squaddata=mysql_fetch_array($squads)) {
			$squad=getsquadname($squaddata['squadID']);
			
      echo '<h2>'.$squad.' - '.$_language->module['stats'].'</h2>';
      
      $totalHomeScoreSQ="";
      $totalOppScoreSQ="";
      $drawall="";
      $wonall="";
      $loseall="";

			// SQUAD STATISTICS

			$squadcws=safe_query("SELECT * FROM ".PREFIX."clanwars WHERE squad='".$squaddata['squadID']."'");
			$total=mysql_num_rows($squadcws);
			$totalperc=percent($total, $totaltotal, 2);

			while($squadcwdata=mysql_fetch_array($squadcws)) {

				// SQUAD CLANWAR STATISTICS

				// total squad homescore
				$sqHomeScoreQry=mysql_fetch_array(safe_query("SELECT homescore FROM ".PREFIX."clanwars WHERE cwID='".$squadcwdata['cwID']."' AND squad='".$squaddata['squadID']."'"));
				$sqHomeScore=array_sum(unserialize($sqHomeScoreQry['homescore']));
				$totalHomeScoreSQ+=array_sum(unserialize($sqHomeScoreQry['homescore']));
				// total squad oppscore
				$sqOppScoreQry=mysql_fetch_array(safe_query("SELECT oppscore FROM ".PREFIX."clanwars WHERE cwID='".$squadcwdata['cwID']."' AND squad='".$squaddata['squadID']."'"));
				$sqOppScore=array_sum(unserialize($sqOppScoreQry['oppscore']));
				$totalOppScoreSQ+=array_sum(unserialize($sqOppScoreQry['oppscore']));

				//
				if($sqHomeScore > $sqOppScore) $wonall++;
				if($sqHomeScore < $sqOppScore) $loseall++;
				if($sqHomeScore == $sqOppScore) $drawall++;
				//
				unset($sqHomeScore);
				unset($sqOppScore);
			}

			// SQUAD STATISTICS - CLANWARS

			// total squad clanwars - home points
			$home=$totalHomeScoreSQ;
			if(empty($home)) $home=0;
			$homeperc=percent($home, $totalhome, 2);
			// total squad clanwars - opponent points
			$opp=$totalOppScoreSQ;
			if(empty($opp)) $opp=0;
			$oppperc=percent($opp, $totalopp, 2);
			// total squad clanwars won
			$wonperc=percent($wonall, $totaltotal, 2);
			if($wonperc) $totalwon=$wonperc.'%<br /><img src="images/icons/won.gif" width="30" height="'.round($wonperc, 0).'" border="1" alt="'.$_language->module['won'].'" />';
			else $totalwon='0%';
			// total squad clanwars lost
			$loseperc=percent($loseall, $totaltotal, 2);
			if($loseperc) $totallost=$loseperc.'%<br /><img src="images/icons/lost.gif" width="30" height="'.round($loseperc, 0).'" border="1" alt="'.$_language->module['lost'].'" />';
			else $totallost='0%';
			// total squad clanwars draw
			$drawperc=percent($drawall, $totaltotal, 2);
			if($drawperc) $totaldraw=$drawperc.'%<br /><img src="images/icons/draw.gif" width="30" height="'.round($drawperc, 0).'" border="1" alt="'.$_language->module['draw'].'" />';
			else $totaldraw='0%';

			// fill empty vars
			if(empty($totalwon)) $totalwon=0;
			if(empty($totallost)) $totallost=0;
			if(empty($totaldraw)) $totaldraw=0;
			if(empty($wonall)) $wonall=0;
			if(empty($loseall)) $loseall=0;
			if(empty($drawall)) $drawall=0;

			// start output for squad details
			eval("\$clanwars_stats = \"".gettemplate("clanwars_stats")."\";");
			echo $clanwars_stats;

			unset($opp); unset($home);
			unset($totalwon); unset($totallost); unset($totaldraw);
			unset($totalHomeScoreSQ); unset($totalOppScoreSQ);
			unset($homeperc); unset($oppperc);

			// PLAYER STATISTICS
      
      $hometeam=array();
      $playerlist="";

			// start output for squad details - players of the squad - head
			eval ("\$clanwars_stats_player_head = \"".gettemplate("clanwars_stats_player_head")."\";");
			echo $clanwars_stats_player_head;

			// get playerlist for squad
			$squadmembers=safe_query("SELECT * FROM ".PREFIX."squads_members WHERE squadID='".$squaddata['squadID']."'");
			while($player=mysql_fetch_array($squadmembers)) {
				$playerlist[]=$player['userID'];
			}

			// get roster for squad and find matches with playerlist
			$playercws=safe_query("SELECT hometeam FROM ".PREFIX."clanwars WHERE squad='".$squaddata['squadID']."'");
			while($roster=mysql_fetch_array($playercws)) {
				$hometeam = array_merge($hometeam, unserialize($roster['hometeam']));
			}

			// counts clanwars the member has taken part in
     		$anz=array();
			if(!empty($hometeam)) {
				foreach($hometeam as $id) {
			        if(!isset($anz[$id])) $anz[$id] = '';
					if(!empty($id)) {
						$anz[$id]=$anz[$id]+1;
					}
				}
			}
			// member's details and the output
			if(is_array($playerlist)) {
				$i=1;
				foreach($playerlist as $id) {
					if($i%2) {
						$bg1=BG_1;
						$bg2=BG_2;
					}
					else {
						$bg1=BG_3;
						$bg2=BG_4;
					}

					$country='[flag]'.getcountry($id).'[/flag]';
					$country=flags($country);
					$member='<a href="index.php?site=profile&amp;id='.$id.'"><b>'.getnickname($id).'</b></a>';
					if(!isset($anz[$id])) $anz[$id] = '';
          $wars=$anz[$id];
					if(empty($wars)) $wars='0';
					$perc=percent($wars, $total, 2);
					if($perc) $percpic='<img src="images/icons/poll_start.gif" width="1" height="5" alt="" /><img src="images/icons/poll.gif" width="'.round($perc, 0).'" height="5" alt="" /><img src="images/icons/poll_end.gif" width="1" height="5" alt="" /> '.$perc.'%';
					else $percpic='<img src="images/icons/poll_start.gif" width="1" height="5" alt="" /><img src="images/icons/poll_end.gif" width="1" height="5" alt="" /> '.$perc.'%';

					eval ("\$clanwars_stats_player_content = \"".gettemplate("clanwars_stats_player_content")."\";");
					echo $clanwars_stats_player_content;
					$i++;
				}
			}
			echo'</table>';

			unset($wonall);
			unset($loseall);
			unset($drawall);
			unset($playerlist);
			unset($hometeam);
			unset($squadcwdata);
		}
	}
}
elseif($action=="showonly") {
	if(isset($_GET['cwID'])) $cwID = (int)$_GET['cwID'];
	if(isset($_GET['id'])){
		if(is_numeric($_GET['id']) || (is_gametag($_GET['id']))) $id = $_GET['id'];
	}
	$only = 'squad';
	if(isset($_GET['only'])){
		if(($_GET['only']=="squad") || ($_GET['only']=="game")) $only = $_GET['only'];
	}
	if(isset($_GET['page'])) $page=(int)$_GET['page'];
	else $page = 1;
	$sort="date";
	if(isset($_GET['sort'])){
	  if(($_GET['sort']=='date') || ($_GET['sort']=='game') || ($_GET['sort']=='squad') || ($_GET['sort']=='oppcountry') || ($_GET['sort']=='league')) $sort=$_GET['sort'];
	}
	
	$type="DESC";
	if(isset($_GET['type'])){
	  if(($_GET['type']=='ASC') || ($_GET['type']=='DESC')) $type=$_GET['type'];
	}
	
	$squads=getgamesquads();
	
  $jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);
	$jumpmenu='<select name="selectgame" onchange="MM_jumpMenu(\'parent\',this,0)">			   <option value="index.php?site=clanwars">- '.$_language->module['show_all_squads'].' -</option>'.$jumpsquads.'</select> <input type="button" name="Button1" value="'.$_language->module['go'].'" onclick="MM_jumpMenuGo(\'selectgame\',\'parent\',0)" />';		

	eval ("\$title_clanwars = \"".gettemplate("title_clanwars")."\";");
	echo $title_clanwars;
  
	$gesamt = mysql_num_rows(safe_query("SELECT cwID FROM ".PREFIX."clanwars WHERE $only='$id'"));
	$pages=1;
	
	$max=$maxclanwars;
	$pages = ceil($gesamt/$max);

  if($pages>1) $page_link = makepagelink("index.php?site=clanwars&amp;action=showonly&amp;id=$id&amp;sort=$sort&amp;type=$type&amp;only=$only", $page, $pages);
  else $page_link = "";

	if ($page == "1") {
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."clanwars WHERE $only='$id' ORDER BY $sort $type LIMIT 0,$max");
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT * FROM ".PREFIX."clanwars WHERE $only='$id' ORDER BY $sort $type LIMIT $start,$max");
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}

	if($type=="ASC")
	$seiten='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=DESC&amp;only='.$only.'">'.$_language->module['sort'].':</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';
	else
	$seiten='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=ASC&amp;only='.$only.'">'.$_language->module['sort'].':</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';

	if(isclanwaradmin($userID)) $admin='<input type="button" onclick="MM_openBrWindow(\'clanwars.php?action=new\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['new_clanwar'].'" />';
  else $admin='';
	$Statistics='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars&amp;action=stats\');return document.MM_returnValue" value="'.$_language->module['stat'].'" />';

	echo'<form name="jump" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td>'.$admin.' '.$Statistics.'</td>
      <td align="right">'.$jumpmenu.'</td>
    </tr>
    <tr>
      <td>'.$seiten.'</td>
      <td></td>
    </tr>
  </table>
  </form>';

	if($gesamt) {
		$headdate='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'">'.$_language->module['date'].':</a>';
		$headgame='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'">'.$_language->module['game'].':</a>';
		$headsquad='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=squad&amp;type='.$type.'">'.$_language->module['squad'].':</a>';
		$headcountry='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=oppcountry&amp;type='.$type.'">'.$_language->module['country'].':</a>';
		$headleague='<a class="titlelink" href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;only='.$only.'&amp;page='.$page.'&amp;sort=league&amp;type='.$type.'">'.$_language->module['league'].':</a>';

		eval ("\$clanwars_head = \"".gettemplate("clanwars_head")."\";");
		echo $clanwars_head;
		$n=1;
	
		while($ds=mysql_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$date=date("d.m.y", $ds['date']);
			$league='<a href="'.$ds['leaguehp'].'" target="_blank">'.$ds['league'].'</a>';
			$oppcountry="[flag]".$ds['oppcountry']."[/flag]";
			$country=flags($oppcountry);
			$opponent='<a href="'.$ds['opphp'].'" target="_blank"><b>'.$ds['opptag'].'</b></a>';
			$maps=$ds['maps'];
			$hometeam=$ds['hometeam'];
			$oppteam=$ds['oppteam'];
			$server=$ds['server'];

			$squad='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$id.'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'&amp;only=squad"><b>'.getsquadname($ds['squad']).'</b></a>';
			if(file_exists('images/games/'.$ds['game'].'.gif')) $pic = $ds['game'].'.gif';
			$game='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['game'].'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'&amp;only=game"><img src="images/games/'.$pic.'" width="13" height="13" border="0" alt="" /></a>';

			$homescr=array_sum(unserialize($ds['homescore']));
			$oppscr=array_sum(unserialize($ds['oppscore']));

			if($homescr>$oppscr) $results='<font color="'.$wincolor.'">'.$homescr.':'.$oppscr.'</font>';
			elseif($homescr<$oppscr) $results='<font color="'.$loosecolor.'">'.$homescr.':'.$oppscr.'</font>';
			else $results='<font color="'.$drawcolor.'">'.$homescr.':'.$oppscr.'</font>';

			if(getanzcwcomments($ds['cwID'])) $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/newhotfolder.gif" alt="'.$_language->module['details'].'" border="0" /> ('.getanzcwcomments($ds['cwID']).')</a>';
			else $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/folder.gif" alt="'.$_language->module['details'].'" border="0" /> ('.getanzcwcomments($ds['cwID']).')</a>';

			$multiple='';
			$admdel='';
			if(isclanwaradmin($userID)) $multiple='<input class="input" type="checkbox" name="cwID[]" value="'.$ds['cwID'].'" />';

			eval ("\$clanwars_content = \"".gettemplate("clanwars_content")."\";");
			echo $clanwars_content;
			unset($result);
			$n++;
		}
    if(isclanwaradmin($userID)) $admdel='<table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td><input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'</td>
        <td align="right"><select name="quickactiontype">
        <option value="delete">'.$_language->module['delete_selected'].'</option>
        </select>
        <input type="submit" name="quickaction" value="'.$_language->module['go'].'" /></td>
      </tr>
    </table>';

		eval ("\$clanwars_foot = \"".gettemplate("clanwars_foot")."\";");
		echo $clanwars_foot;
	}
	else echo $_language->module['no_entries'];
}
elseif(empty($_GET['action'])) {
	if(isset($_GET['page'])) $page=(int)$_GET['page'];
	else $page = 1;
	$sort="date";
	if(isset($_GET['sort'])){
	  if(($_GET['sort']=='date') || ($_GET['sort']=='game') || ($_GET['sort']=='squad') || ($_GET['sort']=='oppcountry') || ($_GET['sort']=='league')) $sort=$_GET['sort'];
	}
	
	$type="DESC";
	if(isset($_GET['type'])){
	  if(($_GET['type']=='ASC') || ($_GET['type']=='DESC')) $type=$_GET['type'];
	}
	$squads=getgamesquads();
	$jumpsquads=str_replace('value="', 'value="index.php?site=clanwars&amp;action=showonly&amp;only=squad&amp;id=', $squads);
	$jumpmenu='<select name="selectgame" onchange="MM_jumpMenu(\'parent\',this,0)"><option value="index.php?site=clanwars">- '.$_language->module['show_all_squads'].' -</option>'.$jumpsquads.'</select> <input type="button" name="Button1" value="'.$_language->module['go'].'" onclick="MM_jumpMenuGo(\'selectgame\',\'parent\',0)" />';		

	eval ("\$title_clanwars = \"".gettemplate("title_clanwars")."\";");
	echo $title_clanwars;

	$gesamt = mysql_num_rows(safe_query("SELECT cwID FROM ".PREFIX."clanwars"));
	$pages=1;
	if(!isset($page)) $page = 1;
	if(!isset($sort)) $sort = "date";
	if(!isset($type)) $type = "DESC";

	$max=$maxclanwars;
	$pages = ceil($gesamt/$max);

	if($pages>1) $page_link = makepagelink("index.php?site=clanwars&amp;sort=$sort&amp;type=$type", $page, $pages);
  else $page_link = "";

	if($page == "1") {
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad ORDER BY c.$sort $type LIMIT 0,$max");
		if($type=="DESC") $n=$gesamt;
		else $n=1;
	}
	else {
		$start=$page*$max-$max;
		$ergebnis = safe_query("SELECT c.*, s.name AS squadname FROM ".PREFIX."clanwars c LEFT JOIN ".PREFIX."squads s ON s.squadID=c.squad ORDER BY $sort $type LIMIT $start,$max");
		if($type=="DESC") $n = ($gesamt)-$page*$max+$max;
		else $n = ($gesamt+1)-$page*$max+$max;
	}

  if($type=="ASC") $seiten='<a href="index.php?site=clanwars&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=DESC">'.$_language->module['sort'].':</a> <img src="images/icons/asc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';
	else $seiten='<a href="index.php?site=clanwars&amp;page='.$page.'&amp;sort='.$sort.'&amp;type=ASC">'.$_language->module['sort'].':</a> <img src="images/icons/desc.gif" width="9" height="7" border="0" alt="" /> '.$page_link.'<br /><br />';

  if(isclanwaradmin($userID)) $admin='<input type="button" onclick="MM_openBrWindow(\'clanwars.php?action=new\',\'Clanwars\',\'toolbar=no,status=no,scrollbars=yes,width=800,height=600\')" value="'.$_language->module['new_clanwar'].'" />';
  else $admin='';
	$statistics='<input type="button" onclick="MM_goToURL(\'parent\',\'index.php?site=clanwars&amp;action=stats\');return document.MM_returnValue" value="'.$_language->module['stat'].'" />';

	echo'<form name="jump" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td>'.$admin.' '.$statistics.'</td>
      <td align="right">'.$jumpmenu.'</td>
    </tr>
    <tr>
      <td>'.$seiten.'</td>
      <td></td>
    </tr>
  </table>
  </form>';

	if($gesamt) {
		$headdate='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=date&amp;type='.$type.'">'.$_language->module['date'].':</a>';
		$headgame='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=games&amp;type='.$type.'">'.$_language->module['game'].':</a>';
		$headsquad='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=squad&amp;type='.$type.'">'.$_language->module['squad'].':</a>';
		$headcountry='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=oppcountry&amp;type='.$type.'">'.$_language->module['country'].':</a>';
		$headleague='<a class="titlelink" href="index.php?site=clanwars&amp;page='.$page.'&amp;sort=league&amp;type='.$type.'">'.$_language->module['league'].':</a>';

		eval ("\$clanwars_head = \"".gettemplate("clanwars_head")."\";");
		echo $clanwars_head;

		$n=1;
		while($ds=mysql_fetch_array($ergebnis)) {
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$date=date("d.m.y", $ds['date']);
			$squad='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['squad'].'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'&amp;only=squad"><b>'.$ds['squadname'].'</b></a>';
			$league='<a href="'.getinput($ds['leaguehp']).'" target="_blank">'.$ds['league'].'</a>';
			$oppcountry="[flag]".$ds['oppcountry']."[/flag]";
			$country=flags($oppcountry);
			$opponent='<a href="'.getinput($ds['opphp']).'" target="_blank"><b>'.$ds['opptag'].'</b></a>';
			$hometeam=$ds['hometeam'];
			$oppteam=$ds['oppteam'];
			$server=$ds['server'];
			if(file_exists('images/games/'.$ds['game'].'.gif')) $pic = $ds['game'].'.gif';
			$game='<a href="index.php?site=clanwars&amp;action=showonly&amp;id='.$ds['game'].'&amp;page='.$page.'&amp;sort=game&amp;type='.$type.'&amp;only=game"><img src="images/games/'.$pic.'" width="13" height="13" border="0" alt="" /></a>';

			$homescr=array_sum(unserialize($ds['homescore']));
			$oppscr=array_sum(unserialize($ds['oppscore']));

			if($homescr>$oppscr) $results='<font color="'.$wincolor.'">'.$homescr.':'.$oppscr.'</font>';
			elseif($homescr<$oppscr) $results='<font color="'.$loosecolor.'">'.$homescr.':'.$oppscr.'</font>';
			else $results='<font color="'.$drawcolor.'">'.$homescr.':'.$oppscr.'</font>';

			if($anzcomments = getanzcwcomments($ds['cwID'])) $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/newhotfolder.gif" alt="'.$_language->module['details'].'" border="0" /> ('.$anzcomments.')</a>';
			else $details='<a href="index.php?site=clanwars_details&amp;cwID='.$ds['cwID'].'"><img src="images/icons/foldericons/folder.gif" alt="'.$_language->module['details'].'" border="0" /> (0)</a>';

			$multiple='';
			$admdel='';
			if(isclanwaradmin($userID)) $multiple='<input class="input" type="checkbox" name="cwID[]" value="'.$ds['cwID'].'" />';

			eval ("\$clanwars_content = \"".gettemplate("clanwars_content")."\";");
			echo $clanwars_content;
			unset($result,$anzcomments);
			$n++;
		}
    if(isclanwaradmin($userID)) $admdel='<table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
        <td><input class="input" type="checkbox" name="ALL" value="ALL" onclick="SelectAll(this.form);" /> '.$_language->module['select_all'].'</td>
        <td align="right"><select name="quickactiontype">
        <option value="delete">'.$_language->module['delete_selected'].'</option>
        </select>
        <input type="submit" name="quickaction" value="'.$_language->module['go'].'" /></td>
      </tr>
    </table>';

		eval ("\$clanwars_foot = \"".gettemplate("clanwars_foot")."\";");
		echo $clanwars_foot;
	}
	else echo $_language->module['no_entries'];
}
?>