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

chdir('../');
include("_mysql.php");
include("_settings.php");
include("_functions.php");
chdir('admin');

$_language->read_module('admincenter');

if(isset($_GET['site'])) $site = $_GET['site'];
else
if(isset($site)) unset($site);

$admin=isanyadmin($userID);
if(!$loggedin) die($_language->module['not_logged_in']);
if(!$admin) die($_language->module['access_denied']);

if(!isset($_SERVER['REQUEST_URI'])) {
	$arr = explode("/", $_SERVER['PHP_SELF']);
	$_SERVER['REQUEST_URI'] = "/" . $arr[count($arr)-1];
	if ($_SERVER['argv'][0]!="")
	$_SERVER['REQUEST_URI'] .= "?" . $_SERVER['argv'][0];
}

function admincenternav($catID) {
	global $userID;
	$links = '';
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."addon_links WHERE catID='$catID' ORDER BY sort");
	while($ds=mysqli_fetch_array($ergebnis)) {
		$accesslevel = 'is'.$ds['accesslevel'].'admin';
		if($accesslevel($userID)) {
			$links .= '<li><a href="'.$ds['url'].'">'.$ds['name'].'</a></li>';
		}
	}
	return $links;
}

function addonnav() {
	global $userID;
	$links = '';
	$ergebnis = safe_query("SELECT * FROM ".PREFIX."addon_categories WHERE sort>'8' ORDER BY sort");
	while($ds=mysqli_fetch_array($ergebnis)) {
		$links .= '<h2>&not; '.$ds['name'].'</h2>
    			   <ul>';
		$catlinks = safe_query("SELECT * FROM ".PREFIX."addon_links WHERE catID='".$ds['catID']."' ORDER BY sort");
		while($db=mysqli_fetch_array($catlinks)) {
			$accesslevel = 'is'.$db['accesslevel'].'admin';
			if($accesslevel($userID)) {
				$links .= '<li><a href="'.$db['url'].'">'.$db['name'].'</a></li>';
			}
		}
		$links .= '</ul>';
	}
	return $links;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="description" content="Clanpage using webSPELL 4 CMS" />
	<meta name="author" content="webspell.org" />
	<meta name="keywords" content="webspell, webspell4, clan, cms" />
	<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
	<meta name="generator" content="webSPELL" />
	<title><?php echo $myclanname ?> - webSPELL AdminCenter</title>
	<link href="_stylesheet.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
	<style type="text/css">
	.td1 {  height: 18px; }
	.td2 {  height: 18px; }
	</style>
	<![endif]-->
	<script language="JavaScript" type="text/JavaScript">
	  var calledfrom='admin';
	</script>
	<script src="../js/bbcode.js" language="JavaScript" type="text/javascript"></script>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="1000" align="center">
  <tr>
   <td colspan="5" id="head">
   <div id="links">
    <ul>
      <li><a href="http://www.webspell.org/index.php?site=support" target="_blank" class="link1"></a></li>
      <li><a href="http://www.webspell.org/index.php?site=license" target="_blank" class="link2"></a></li>
      <li><a href="http://www.webspell.org" target="_blank" class="link3"></a></li>
    </ul>
   </div>
   </td>
  </tr>
  <tr>
   <td colspan="5"><img src="images/2.jpg" width="1000" height="5" border="0" alt="" /></td>
  </tr>
  <tr>
   <td style="background-image:url(images/3.jpg);" width="5" valign="top"></td>
   <td bgcolor="#f2f2f2" width="202" valign="top">
   <div id="menu">
    <h2>&not; <?php echo $_language->module['main_panel']; ?></h2>
    <ul>
      <li><a href="admincenter.php"><?php echo $_language->module['overview']; ?></a></li>
      <li><a href="admincenter.php?site=page_statistic"><?php echo $_language->module['page_statistics']; ?></a></li>
      <li><a href="admincenter.php?site=visitor_statistic"><?php echo $_language->module['visitor_statistics']; ?></a></li>
      <?php echo admincenternav(1); ?>
      <li><a href="../logout.php"><b><?php echo $_language->module['log_out']; ?></b></a></li>
    </ul>
    <?php if(isuseradmin($userID)) { ?>
    <h2>&not; <?php echo $_language->module['user_administration']; ?></h2>
    <ul>
      <li><a href="admincenter.php?site=users"><?php echo $_language->module['registered_users']; ?></a></li>
      <li><a href="admincenter.php?site=squads"><?php echo $_language->module['squads']; ?></a></li>
      <li><a href="admincenter.php?site=members"><?php echo $_language->module['clanmembers']; ?></a></li>
	  <li><a href="admincenter.php?site=contact"><?php echo $_language->module['contact']; ?></a></li>
      <li><a href="admincenter.php?site=newsletter"><?php echo $_language->module['newsletter']; ?></a></li>
      <?php echo admincenternav(2); ?>
    </ul>
    <?php }
    if(ispageadmin($userID)) { ?>
    <h2>&not; <?php echo $_language->module['spam']; ?></h2>
    <ul>
      <li><a href="admincenter.php?site=spam&amp;action=forum_spam"><?php echo $_language->module['blocked_content']; ?></a></li>
      <li><a href="admincenter.php?site=spam&amp;action=user"><?php echo $_language->module['spam_user']; ?></a></li>
      <li><a href="admincenter.php?site=spam&amp;action=multi"><?php echo $_language->module['multiaccounts']; ?></a></li>
      <li><a href="admincenter.php?site=spam&amp;action=api_log"><?php echo $_language->module['api_log']; ?></a></li>
      <?php echo admincenternav(3); ?>
    </ul>
    <?php } if(isnewsadmin($userID) || isfileadmin($userID) || ispageadmin($userID)) { ?>
    <h2>&not; <?php echo $_language->module['rubrics']; ?></h2>
    <ul>
      <?php } if(isnewsadmin($userID)) { ?>
      <li><a href="admincenter.php?site=rubrics"><?php echo $_language->module['news_rubrics']; ?></a></li>
      <li><a href="admincenter.php?site=newslanguages"><?php echo $_language->module['news_languages']; ?></a></li>
      <?php } if(isfileadmin($userID)) { ?>
      <li><a href="admincenter.php?site=filecategorys"><?php echo $_language->module['file_categories']; ?></a></li>
      <?php } if(ispageadmin($userID)) { ?>
      <li><a href="admincenter.php?site=faqcategories"><?php echo $_language->module['faq_categories']; ?></a></li>
      <li><a href="admincenter.php?site=linkcategorys"><?php echo $_language->module['link_categories']; ?></a></li>
      <?php echo admincenternav(4); ?>
    </ul>
    <?php } if(ispageadmin($userID)) { ?>
    <h2>&not; <?php echo $_language->module['settings']; ?></h2>
    <ul>
      <li><a href="admincenter.php?site=settings"><?php echo $_language->module['settings']; ?></a></li>
      <li><a href="admincenter.php?site=styles"><?php echo $_language->module['styles']; ?></a></li>
      <li><a href="admincenter.php?site=addons"><?php echo $_language->module['addons']; ?></a></li>
      <li><a href="admincenter.php?site=countries"><?php echo $_language->module['countries']; ?></a></li>
      <li><a href="admincenter.php?site=games"><?php echo $_language->module['games']; ?></a></li>
      <li><a href="admincenter.php?site=smileys"><?php echo $_language->module['smilies']; ?></a></li>
      <li><a href="admincenter.php?site=database"><?php echo $_language->module['database']; ?></a></li>
      <?php echo admincenternav(5); ?>
      <li><a href="admincenter.php?site=update&amp;action=update"><?php echo $_language->module['update_webspell']; ?></a></li>
    </ul>
    <h2>&not; <?php echo $_language->module['content']; ?></h2>
    <ul>
      <li><a href="admincenter.php?site=static"><?php echo $_language->module['static_pages']; ?></a></li>
      <li><a href="admincenter.php?site=faq"><?php echo $_language->module['faq']; ?></a></li>
      <li><a href="admincenter.php?site=servers"><?php echo $_language->module['servers']; ?></a></li>
	  <li><a href="admincenter.php?site=sponsors"><?php echo $_language->module['sponsors']; ?></a></li>
      <li><a href="admincenter.php?site=partners"><?php echo $_language->module['partners']; ?></a></li>
      <li><a href="admincenter.php?site=history"><?php echo $_language->module['history']; ?></a></li>
      <li><a href="admincenter.php?site=about"><?php echo $_language->module['about_us']; ?></a></li>
      <li><a href="admincenter.php?site=imprint"><?php echo $_language->module['imprint']; ?></a></li>
      <li><a href="admincenter.php?site=bannerrotation"><?php echo $_language->module['bannerrotation']; ?></a></li>
      <li><a href="admincenter.php?site=scrolltext"><?php echo $_language->module['scrolltext']; ?></a></li>
      <?php echo admincenternav(6); ?>
    </ul>
    <?php } if(isforumadmin($userID)) { ?>
    <h2>&not; <?php echo $_language->module['forum']; ?></h2>
    <ul>
      <li><a href="admincenter.php?site=boards"><?php echo $_language->module['boards']; ?></a></li>
      <li><a href="admincenter.php?site=groups"><?php echo $_language->module['manage_user_groups']; ?></a></li>
      <li><a href="admincenter.php?site=group-users"><?php echo $_language->module['manage_group_users']; ?></a></li>
      <li><a href="admincenter.php?site=ranks"><?php echo $_language->module['user_ranks']; ?></a></li>
      <?php echo admincenternav(7); ?>
    </ul>
    <?php } if(isgalleryadmin($userID)) { ?>
    <h2>&not; <?php echo $_language->module['gallery']; ?></h2>
    <ul>
      <li><a href="admincenter.php?site=gallery&amp;part=groups"><?php echo $_language->module['manage_groups']; ?></a></li>
      <li><a href="admincenter.php?site=gallery&amp;part=gallerys"><?php echo $_language->module['manage_galleries']; ?></a></li>
      <?php echo admincenternav(8); ?>
    </ul>
    <?php echo addonnav(); ?>
    <?php } ?>
   </div>
   </td>
   <td bgcolor="#2a2a2a" width="2" valign="top"></td>
   <td bgcolor="#ffffff" width="786" valign="top">
   <div class="pad"><?php
   if(isset($site) && $site!="news"){
   $invalide = array('\\','/','//',':','.');
   $site = str_replace($invalide,' ',$site);
   	if(file_exists($site.'.php')) include($site.'.php');
   	else include('404.php');
   }
   else include('overview.php');
   ?></div>
   </td>
   <td style="background-image:url(images/4.jpg);" width="5" valign="top"></td>
  </tr>
  <tr>
   <td colspan="5"><img src="images/5.jpg" width="1000" height="7" border="0" alt="" /></td>
  </tr>
</table>
<center><br />&copy; 2005 - 2011 <a href="http://www.webspell.org" target="_blank" class="white"><b>webSPELL.org</b></a> &amp; <a href="http://www.webspell.at" target="_blank" class="white"><b>webSPELL.at</b></a><br />&nbsp;</center>
</body>
</html>
