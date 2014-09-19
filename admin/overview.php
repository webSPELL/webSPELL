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

$_language->read_module('overview');

if(!isanyadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

include '../version.php';

$username='<b>'.getnickname($userID).'</b>';
$lastlogin = date('d.m.Y, H:i',$_SESSION['ws_lastlogin']);

/*$wsversion = file_get_contents("http://update.webspell.org/index.php?show=version");
if($wsversion == $version) {
	$version = '<font color="#008000">'.$version.'</font>';
} else {
	$version = '<font color="#FF0000">'.$version.'</font> <small>('.$_language->module['current'].' <a href="admincenter.php?site=update&amp;action=update">'.$wsversion.'</a>)</small>';
}*/
$phpversion = phpversion() < '4.3' ? '<font color="#FF0000">'.phpversion().'</font>' : '<font color="#008000">'.phpversion().'</font>';
$zendversion = zend_version() < '1.3' ? '<font color="#FF0000">'.zend_version().'</font>' : '<font color="#008000">'.zend_version().'</font>';
$mysqlversion = mysql_get_server_info() < '4.0' ? '<font color="#FF0000">'.mysql_get_server_info().'</font>' : '<font color="#008000">'.mysql_get_server_info().'</font>';
$get_phpini_path = get_cfg_var('cfg_file_path');
$get_allow_url_fopen= get_cfg_var('allow_url_fopen') ? '<font color="#008000">'.$_language->module['on'].'</font>' : '<font color="#FF0000">'.$_language->module['off'].'</font>';
$get_allow_url_include= get_cfg_var('allow_url_include') ? '<font color="#FF0000">'.$_language->module['on'].'</font>' : '<font color="#008000">'.$_language->module['off'].'</font>';
$get_display_errors= get_cfg_var('display_errors') ? '<font color="#FFA500">'.$_language->module['on'].'</font>' : '<font color="#008000">'.$_language->module['off'].'</font>';
$get_file_uploads= get_cfg_var('file_uploads') ? '<font color="#008000">'.$_language->module['on'].'</font>' : '<font color="#FF0000">'.$_language->module['off'].'</font>';
$get_log_errors = get_cfg_var('log_errors') ? '<font color="#008000">'.$_language->module['on'].'</font>' : '<font color="#FF0000">'.$_language->module['off'].'</font>';
$get_magic_quotes = get_cfg_var('magic_quotes_gpc') ? '<font color="#008000">'.$_language->module['on'].'</font>' : '<font color="#FFA500">'.$_language->module['off'].'</font>';
$get_max_execution_time = get_cfg_var('max_execution_time') < 30 ? '<font color="#FF0000">'.get_cfg_var('max_execution_time').'</font> <small>(min. > 30)</small>' : '<font color="#008000">'.get_cfg_var('max_execution_time').'</font>';
$get_memory_limit = get_cfg_var('memory_limit') > 128 ? '<font color="#FFA500">'.get_cfg_var('memory_limit').'</font>' : '<font color="#008000">'.get_cfg_var('memory_limit').'</font>';
$get_open_basedir= get_cfg_var('open_basedir') ? '<font color="#008000">'.$_language->module['on'].'</font>' : '<font color="#FFA500">'.$_language->module['off'].'</font>';
$get_post_max_size = get_cfg_var('post_max_size') > 8 ? '<font color="#FFA500">'.get_cfg_var('post_max_size').'</font>' : '<font color="#008000">'.get_cfg_var('post_max_size').'</font>';
$get_register_globals = get_cfg_var('register_globals') ? '<font color="#FF0000">'.$_language->module['on'].'</font>' : '<font color="#008000">'.$_language->module['off'].'</font>';
$get_safe_mode= get_cfg_var('safe_mode') ? '<font color="#008000">'.$_language->module['on'].'</font>' : '<font color="#FF0000">'.$_language->module['off'].'</font>';
$get_short_open_tag = get_cfg_var('short_open_tag') ? '<font color="#008000">'.$_language->module['on'].'</font>' : '<font color="#FFA500">'.$_language->module['off'].'</font>';
$get_upload_max_filesize = get_cfg_var('upload_max_filesize') > 16 ? '<font color="#FFA500">'.get_cfg_var('upload_max_filesize').'</font>' : '<font color="#008000">'.get_cfg_var('upload_max_filesize').'</font>';
$info_na = '<font color="#8F8F8F">'.$_language->module['na'].'</font>';
if(function_exists("gd_info")) {
	$gdinfo = gd_info();
	$get_gd_info = '<font color="#008000">'.$_language->module['enable'].'</font>';
	$get_gdtypes = array();
	if (isset($gdinfo['FreeType Support'])) { $get_gdtypes[] = "FreeType"; }
	if (isset($gdinfo['T1Lib Support'])) { $get_gdtypes[] = "T1Lib"; }
	if (isset($gdinfo['GIF Read Support'])) { $get_gdtypes[] = "*.gif ".$_language->module['read']; }
	if (isset($gdinfo['GIF Create Support'])) { $get_gdtypes[] = "*.gif ".$_language->module['create']; }
	if (isset($gdinfo['JPG Support'])) { $get_gdtypes[] = "*.jpg"; }
	if (isset($gdinfo['PNG Support'])) { $get_gdtypes[] = "*.png"; }
	if (isset($gdinfo['WBMP Support'])) { $get_gdtypes[] = "*.wbmp"; }
	if (isset($gdinfo['XBM Support'])) { $get_gdtypes[] = "*.xbm"; }
	if (isset($gdinfo['XPM Support'])) { $get_gdtypes[] = "*.xpm"; }
	$get_gdtypes = implode(", ",$get_gdtypes);
}
else {
	$get_gd_info = '<font color="#FF0000">'.$_language->module['disable'].'</font>';
	$gdinfo['GD Version'] = '---';
	$get_gdtypes = '---';
}
$get = safe_query("SELECT DATABASE()");
$ret = mysql_fetch_array($get);
$db = $ret[0];

echo '<h1>&curren; '.$_language->module['welcome'].'</h1>';
echo $_language->module['hello'].'&nbsp;'.$username.',&nbsp;'.$_language->module['last_login'].'&nbsp;'.$lastlogin.'.<br /><br />';
echo $_language->module['welcome_message']; ?>
<br /><iframe src="http://update.webspell.org/index.php?new&amp;v=<?php echo $version;?>&amp;h=<?php echo $_SERVER['SERVER_NAME'];?>" height="70" frameborder="0" scrolling="no" width="100%"></iframe><br/><br/>
<h1>&curren; <?php echo $_language->module['serverinfo']; ?></h1>
<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
  <tr>
    <td class="title" colspan="4"><b><?php echo $_language->module['serverinfo']; ?></b></td>
  </tr>
  <tr>
    <td width="25%" class="td1"><b><?php echo $_language->module['webspell_version']; ?></b></td>
    <td width="25%" class="td1"><font color="#008000"><?php echo $version; ?></font></td>
    <td width="25%" class="td1"><b><?php echo $_language->module['server_os']; ?></b></td>
    <td width="25%" class="td1"><?php echo (($php_s = @php_uname('s')) ? $php_s : $info_na); ?></td>
  </tr>
  <tr>
    <td class="td2"><b><?php echo $_language->module['php_version']; ?></b></td>
    <td class="td2"><?php echo $phpversion; ?></td>
    <td class="td2"><b><?php echo $_language->module['server_host']; ?></b></td>
    <td class="td2"><?php echo (($php_n = @php_uname('n')) ? $php_n : $info_na); ?></td>
  </tr>
  <tr>
    <td class="td1"><b><?php echo $_language->module['zend_version']; ?></b></td>
    <td class="td1"><?php echo $zendversion; ?></td>
    <td class="td1"><b><?php echo $_language->module['server_release']; ?></b></td>
    <td class="td1"><?php echo (($php_r = @php_uname('r')) ? $php_r : $info_na); ?></td>
  </tr>
  <tr>
    <td class="td2"><b><?php echo $_language->module['mysql_version']; ?></b></td>
    <td class="td2"><?php echo $mysqlversion; ?></td>
    <td class="td2"><b><?php echo $_language->module['server_version']; ?></b></td>
    <td class="td2"><?php echo (($php_v = @php_uname('v')) ? $php_v : $info_na); ?></td>
  </tr>
  <tr>
    <td class="td1"><b><?php echo $_language->module['databasename']; ?></b></td>
    <td class="td1"><?php echo $db; ?></td>
    <td class="td1"><b><?php echo $_language->module['server_machine']; ?></b></td>
    <td class="td1"><?php echo (($php_m = @php_uname('m')) ? $php_m : $info_na); ?></td>
  </tr>
</table>
<br /><br />
<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
  <tr>
    <td class="title" colspan="4"><b><?php echo $_language->module['interface']; ?></b></td>
  </tr>
  <tr>
    <td width="25%" class="td1"><b><?php echo $_language->module['server_api']; ?></b></td>
    <td width="75%" class="td1"><?php echo php_sapi_name(); ?></td>
  </tr>
  <tr>
    <td class="td2"><b><?php echo $_language->module['apache']; ?></b></td>
    <td class="td2"><?php if(function_exists("apache_get_version")) echo apache_get_version(); else echo $_language->module['na']; ?></td>
  </tr>
  <tr>
    <td class="td1"><b><?php echo $_language->module['apache_modules']; ?></b></td>
    <td class="td1"><?php if(function_exists("apache_get_modules")){if(count(apache_get_modules()) > 1) $get_apache_modules = implode(", ",apache_get_modules()); echo $get_apache_modules;} else{ echo $_language->module['na'];} ?></td>
  </tr>
</table>
<br /><br />
<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
  <tr>
    <td class="title" colspan="4"><b><?php echo $_language->module['php_settings']; ?></b></td>
  </tr>
  <tr>
    <td class="td1" colspan="4"><b><?php echo $_language->module['legend']; ?>:</b>&nbsp; &nbsp;<font color="#008000"><?php echo $_language->module['green']; ?>:</font> <?php echo $_language->module['setting_ok']; ?>&nbsp; - &nbsp;<font color="#FFA500"><?php echo $_language->module['orange']; ?>:</font> <?php echo $_language->module['setting_notice']; ?>&nbsp; - &nbsp;<font color="#FF0000"><?php echo $_language->module['red']; ?>:</font> <?php echo $_language->module['setting_error']; ?></td>
  </tr>
  <tr>
    <td width="25%" class="td2"><b>php.ini <?php echo $_language->module['path']; ?></b></td>
    <td width="75%" class="td2" colspan="3"><?php echo $get_phpini_path; ?></td>
  </tr>
  <tr>
    <td width="25%" class="td1"><b>Allow URL fopen</b></td>
    <td width="25%" class="td1"><?php echo $get_allow_url_fopen; ?></td>
    <td width="25%" class="td1"><b>Open Basedir</b></td>
    <td width="25%" class="td1"><?php echo $get_open_basedir; ?></td>
  </tr>
  <tr>
    <td class="td2"><b>Allow URL Include</b></td>
    <td class="td2"><?php echo $get_allow_url_include; ?></td>
    <td class="td2"><b>max. Upload (Filesize)</b></td>
    <td class="td2"><?php echo $get_upload_max_filesize; ?></td>
  </tr>
  <tr>
    <td class="td1"><b>Display Errors</b></td>
    <td class="td1"><?php echo $get_display_errors; ?></td>
    <td class="td1"><b>Memory Limit</b></td>
    <td class="td1"><?php echo $get_memory_limit; ?></td>
  </tr>
  <tr>
    <td class="td2"><b>Error Log</b></td>
    <td class="td2"><?php echo $get_log_errors; ?></td>
    <td class="td2"><b>Post max Size</b></td>
    <td class="td2"><?php echo $get_post_max_size; ?></td>
  </tr>
  <tr>
    <td class="td1"><b>File Uploads</b></td>
    <td class="td1"><?php echo $get_file_uploads; ?></td>
    <td class="td1"><b>Register Globals</b></td>
    <td class="td1"><?php echo $get_register_globals; ?></td>
  </tr>
  <tr>
    <td class="td2"><b>Magic Quotes</b></td>
    <td class="td2"><?php echo $get_magic_quotes; ?></td>
    <td class="td2"><b>Safe Mode</b></td>
    <td class="td2"><?php echo $get_safe_mode; ?></td>
  </tr>
  <tr>
    <td class="td1"><b>max. Execution Time</b></td>
    <td class="td1"><?php echo $get_max_execution_time; ?></td>
    <td class="td1"><b>Short Open Tag</b></td>
    <td class="td1"><?php echo $get_short_open_tag; ?></td>
  </tr>
</table>
<br /><br />
<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
  <tr>
    <td class="title" colspan="4"><b>GD Graphics Library</b></td>
  </tr>
  <tr>
    <td width="25%" class="td1"><b>GD Graphics Library</b></td>
    <td width="25%" class="td1"><?php echo $get_gd_info; ?></td>
    <td width="25%" class="td1"><b>GD Lib <?php echo $_language->module['version']; ?></b></td>
    <td width="25%" class="td1"><?php echo $gdinfo['GD Version']; ?></td>
  </tr>
  <tr>
    <td width="25%" class="td2"><b><?php echo $_language->module['supported_types']; ?></b></td>
    <td width="75%" colspan="3" class="td2"><?php echo $get_gdtypes; ?></td>
  </tr>
</table>