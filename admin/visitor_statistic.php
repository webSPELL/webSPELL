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

$_language->read_module('visitor_statistic');

if(!isanyadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo '<h1>&curren; '.$_language->module['visitor_stats_overall'].'</h1>';

$time = time();
$date = date("d.m.Y", $time);
$dateyesterday = date("d.m.Y", $time-(24*3600));
$datemonth = date(".m.Y", time());

$ergebnis=safe_query("SELECT hits FROM ".PREFIX."counter");
$ds=mysql_fetch_array($ergebnis);
$us = mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."user"));

$total=$ds['hits'];
$dt = mysql_fetch_array(safe_query("SELECT count FROM ".PREFIX."counter_stats WHERE dates='$date'"));
if($dt['count']) $today = $dt['count']; else $today = 0;

$dy = mysql_fetch_array(safe_query("SELECT count FROM ".PREFIX."counter_stats WHERE dates='$dateyesterday'"));
if($dy['count']) $yesterday = $dy['count']; else $yesterday = 0;

$month=0;
$monthquery = safe_query("SELECT count FROM ".PREFIX."counter_stats WHERE dates LIKE '%$datemonth'");
while($dm=mysql_fetch_array($monthquery)) {
	$month = $month+$dm['count'];
}
if($month == 0) $month = 1;
$monatsstat = '';

$tmp = mysql_fetch_array(safe_query("SELECT online FROM ".PREFIX."counter"));
$days_online = round((time()-$tmp['online'])/(3600*24));

if(!$days_online) $days_online = 1;

$perday = round($total/$days_online,2);
$perhour = round($total/$days_online/24,2);
$permonth = round($total/$days_online*24,2);

$tmp = mysql_fetch_array(safe_query("SELECT max(count) as MAXIMUM FROM ".PREFIX."counter_stats"));
$maxvisits = $tmp['MAXIMUM'];
$tmp2 = mysql_fetch_array(safe_query("SELECT dates FROM ".PREFIX."counter_stats WHERE count='$maxvisits'"));
$maxvisits_date = $tmp2['dates'];

$online = mysql_num_rows(safe_query("SELECT time FROM ".PREFIX."whoisonline"));
$dm=mysql_fetch_array(safe_query("SELECT maxonline FROM ".PREFIX."counter"));
$maxonline=$dm['maxonline'];

$guests = mysql_num_rows(safe_query("SELECT ip FROM ".PREFIX."whoisonline WHERE userID=''"));
$user = mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."whoisonline WHERE ip=''"));
$useronline = $guests + $user;

if($user==1) $user_on='1 '.$_language->module['user'];
else $user_on=$user.' '.$_language->module['users'];

if($guests==1) $guests_on='1 '.$_language->module['guest'];
else $guests_on= $guests.' '.$_language->module['guests'];

echo '<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
  <tr>
    <td class="title" colspan="2"><b>'.$_language->module['visitor'].'</b></td>
	 <td class="title" colspan="2"><b>'.$_language->module['stats'].'</b></td>
  </tr>
  <tr>
    <td width="23%" class="td1"><b>'.$_language->module['today'].'</b></td>
    <td width="27%" class="td1">'.$today.'</td>
    <td width="25%" class="td1"><b>'.$_language->module['days_online'].'</b></td>
    <td width="25%" class="td1">'.$days_online.'</td>
  </tr>
  <tr>
    <td class="td2"><b>'.$_language->module['yesterday'].'</b></td>
    <td class="td2">'.$yesterday.'</td>
    <td class="td2"><b>'.$_language->module['visits_month'].'</b></td>
    <td class="td2">'.$permonth.'</td>
  </tr>
  <tr>
    <td class="td1"><b>'.$_language->module['this_month'].'</b></td>
    <td class="td1">'.$month.'</td>
    <td class="td1"><b>'.$_language->module['visits_day'].'</b></td>
    <td class="td1">'.$perday.'</td>
  </tr>
  <tr>
    <td class="td2"><b>'.$_language->module['total'].'</b></td>
    <td class="td2">'.$total.'</td>
    <td class="td2"><b>'.$_language->module['visits_hour'].'</b></td>
    <td class="td2">'.$perhour.'</td>
  </tr>
  <tr>
    <td class="td1"><b>'.$_language->module['now_online'].'</b></td>
    <td class="td1">'.$online.' ('.$user_on.', '.$guests_on.')</td>
    <td class="td1"><b>'.$_language->module['max_day'].'</b></td>
    <td class="td1">'.$maxvisits.' ('.$maxvisits_date.')</td>
  </tr>
</table>
<br /><br />';

echo '<h1>&curren; '.$_language->module['visitor_stats_graphics'].'</h1>';

if(isset($_SESSION['size_x'])) {
	$size_x = $_SESSION['size_x'];
}
else {
	$size_x = 650;
}
if(isset($_SESSION['size_y'])) {
	$size_y = $_SESSION['size_y'];
}
else {
	$size_y = 200;
}

if(isset($_SESSION['count_days'])) {
	$count_days = $_SESSION['count_days'];
}
else {
	$count_days = 30;
}
if(isset($_SESSION['count_months'])) {
	$count_months = $_SESSION['count_months'];
}
else {
	$count_months = 12;
}

?>
<script language="javascript" type="text/javascript">
<!--

size_x = <?php echo $size_x; ?>;
size_y = <?php echo $size_y; ?>;
year = 0;
month = 0;
count_days = <?php echo $count_days; ?>;
count_months = <?php echo $count_months; ?>;

function display_stat(new_year, new_month)
{
	year = new_year;
	month = new_month;
	if(month) {
		document.getElementById('img').src = 'visitor_statistic_image.php?year=' + year + '&month=' + month + '&size_x=' + size_x + '&size_y=' + size_y;
	}
	else {
		document.getElementById('img').src = 'visitor_statistic_image.php?year=' + year + '&size_x=' + size_x + '&size_y=' + size_y;
	}
	document.getElementById('img').style.display = '';
	if(month) {
		document.getElementById('h2').innerHTML = year + '.' + month;
	}
	else {
		document.getElementById('h2').innerHTML = year;
	}
	document.getElementById('h2').style.display = '';
}

function update_size(new_x, new_y)
{
	size_x = new_x;
	size_y = new_y;
	
	if(size_x <= 0) {
		size_x = 1;
		document.getElementById('new_x').value = 1;
	}
	if(size_y <= 0) {
		size_y = 1;
		document.getElementById('new_y').value = 1;
	}
	
	if(year) {
		display_stat(year, month);
	}
	
	document.getElementById('last_days').src = 'visitor_statistic_image.php?last=days&count=' + count_days + '&size_x=' + size_x + '&size_y=' + size_y;
	document.getElementById('last_months').src = 'visitor_statistic_image.php?last=months&count=' + count_months + '&size_x=' + size_x + '&size_y=' + size_y;
}
function update_count(new_days, new_months)
{
	count_days = new_days;
	count_months = new_months;
	
	if(count_days <= 1) {
		count_days = 2;
		document.getElementById('count_days').value = 2;
	}
	if(count_months <= 1) {
		count_months = 2;
		document.getElementById('count_months').value = 2;
	}
	
	document.getElementById('last_days_h2').innerHTML = '&curren; <?php echo $_language->module['last']; ?> ' + count_days + ' <?php echo $_language->module['days']; ?>';
	document.getElementById('last_months_h2').innerHTML = '&curren; <?php echo $_language->module['last']; ?> ' + count_months + ' <?php echo $_language->module['months']; ?>';
	
	document.getElementById('last_days').src = 'visitor_statistic_image.php?last=days&count=' + count_days + '&size_x=' + size_x + '&size_y=' + size_y;
	document.getElementById('last_months').src = 'visitor_statistic_image.php?last=months&count=' + count_months + '&size_x=' + size_x + '&size_y=' + size_y;
}
-->
</script>

<p><b><?php echo $_language->module['settings']; ?>:</b></p>
<?php echo $_language->module['last']; ?> <input type="text" id="count_days" value="<?php echo $count_days; ?>" style="width:20px;" /> <?php echo $_language->module['days']; ?><br /><br />
<?php echo $_language->module['last']; ?> <input type="text" id="count_months" value="<?php echo $count_months; ?>" style="width:20px;" /> <?php echo $_language->module['months']; ?> <input type="button" onclick="update_count(document.getElementById('count_days').value, document.getElementById('count_months').value);" value="<?php echo $_language->module['show']; ?>" /><br /><br />

<p><b><?php echo $_language->module['change_size']; ?>:</b></p>
<input type="text" id="new_x" value="<?php echo $size_x; ?>" style="width:40px;" /> x <input type="text" id="new_y" value="<?php echo $size_y; ?>" style="width:40px;" /> <input type="button" onclick="update_size(document.getElementById('new_x').value, document.getElementById('new_y').value);" value="<?php echo $_language->module['show']; ?>" /> <?php echo $_language->module['width_height']; ?><br /><br />

<p><b><?php echo $_language->module['show_year_month']; ?>:</b></p>
<input type="text" id="year" style="width:40px;" /> <input type="button" onclick="display_stat(document.getElementById('year').value, 0);" value="<?php echo $_language->module['show']; ?>" /> <?php echo $_language->module['yyyy']; ?><br /><br />
<input type="text" id="year2" style="width:40px;" />.<input type="text" id="month" style="width:20px;" /> <input type="button" onclick="display_stat(document.getElementById('year2').value, document.getElementById('month').value);" value="<?php echo $_language->module['show']; ?>" /> <?php echo $_language->module['yyyy_mm']; ?><br /><br />
<h1 id="h2" style="display:none;"></h1>
<img id="img" style="display:none;" src="" alt="" />

<h1 id="last_days_h2">&curren; <?php echo $_language->module['last']; ?> <?php echo $count_days; ?> <?php echo $_language->module['days']; ?></h1>
<img id="last_days" src="visitor_statistic_image.php?last=days&amp;count=<?php echo $count_days; ?>" alt="" />
<h1 id="last_months_h2">&curren; <?php echo $_language->module['last']; ?> <?php echo $count_months; ?> <?php echo $_language->module['months']; ?></h1>
<img id="last_months" src="visitor_statistic_image.php?last=months&amp;count=<?php echo $count_months; ?>" alt="" />