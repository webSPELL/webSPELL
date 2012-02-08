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

$_language->read_module('imprint');

eval ("\$title_imprint = \"".gettemplate("title_imprint")."\";");
echo $title_imprint;

$ergebnis=safe_query("SELECT u.firstname, u.lastname, u.nickname, u.userID FROM ".PREFIX."user_groups as g, ".PREFIX."user as u WHERE u.userID = g.userID AND (g.page='1' OR g.forum='1' OR g.user='1' OR g.news='1' OR g.clanwars='1' OR g.feedback='1' OR g.super='1' OR g.gallery='1' OR g.cash='1' OR g.files='1')");
$administrators='';
while($ds=mysql_fetch_array($ergebnis)) {
	$administrators .= "<a href='index.php?site=profile&amp;id=".$ds['userID']."'>".$ds['firstname']." '".$ds['nickname']."' ".$ds['lastname']."</a><br />";
}
$ergebnis=safe_query("SELECT u.firstname, u.lastname, u.nickname, u.userID FROM ".PREFIX."user_groups as g, ".PREFIX."user as u WHERE u.userID = g.userID AND g.moderator='1'");
$moderators='';
while($ds=mysql_fetch_array($ergebnis)) {
	$moderators .= "<a href='index.php?site=profile&amp;id=".$ds['userID']."'>".$ds['firstname']." '".$ds['nickname']."' ".$ds['lastname']."</a><br />";
}

// reading version
include('version.php');

$bg1=BG_1;
$headline1 = $_language->module['imprint'];
$headline2 = $_language->module['coding'];

if($imprint_type) {

	$ds=mysql_fetch_array(safe_query("SELECT imprint FROM `".PREFIX."imprint`"));
	$imprint_head = htmloutput($ds['imprint'])."<br /><br />";

} else{

	$imprint_head='<table border="0" width="96%" align="center">
    <tr>
      <td colspan="2"><b>'.$_language->module['responsible_persons'].'</b></td>
    </tr>
    <tr>
      <td width="130" valign="top"><br /><b>'.$_language->module['webmaster'].'</b></td>
      <td><br /><a href="mailto:'.mail_protect($admin_email).'">'.$admin_name.'</a></td>
    </tr>
    <tr>
      <td valign="top"><br /><b>'.$_language->module['admins'].'</b></td>
      <td><br />'.$administrators.'</td>
    </tr>
    <tr>
      <td valign="top"><br /><b>'.$_language->module['mods'].'</b></td>
      <td><br />'.$moderators.'</td>
    </tr>
  </table>';
}

eval ("\$imprint = \"".gettemplate("imprint")."\";");
echo $imprint;
?>
