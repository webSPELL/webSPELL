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

$_language->read_module('update');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['webspell_update'].'</h1>';

//Where to get the newest WebSPELL from? (standard: http://update.webspell.org/)
$updateserver = "http://update.webspell.org/";

// reading version
include('../version.php');

if(!isset($_GET['action'])) {
	if(!$getnew = file_get_contents($updateserver."index.php?show=version")) {
    	echo'<i><b>'.$_language->module['error'].'&nbsp;'.$updateserver.'.</b></i>';
  } else {

		$latest = explode(".",$getnew);
		$ownversion = explode(".",$version);

		if($latest[0]>$ownversion[0]) echo '<a href="admincenter.php?site=update&amp;action=update"><font color="red">'.$_language->module['new_version'].'!</font></a>';
		elseif($latest[0]==$ownversion[0] AND $latest[1]>$ownversion[1]) echo '<a href="admincenter.php?site=update&amp;action=update">'.$_language->module['new_functions'].'&nbsp;'.$version[0].'!</a>';
		elseif($latest[0]==$ownversion[0] AND $latest[1]==$ownversion[1] AND $latest[2]>$ownversion[2]) echo '<a href="admincenter.php?site=update&amp;action=update">'.$_language->module['new_updates'].'&nbsp;'.$version[0].'!</a>';
	}

}

if(isset($_GET['action'])) $action = $_GET['action'];
else $action = '';

if($action=="update") {

	//update server sends update information in following form: package1:updateversion1:additional1.package2:additional2:WritttenBy2..., e.g. members:4.01.30:written by FS

	if($getnew = file_get_contents($updateserver."index.php?version=".$version."")) {

		$updates = explode("##",$getnew);

		//get packages

		echo'<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
      <tr>
        <td width="25%" class="title"><b>'.$_language->module['filename'].'</b></td>
        <td width="25%" class="title"><b>'.$_language->module['version'].'</b></td>
        <td width="50%" class="title"><b>'.$_language->module['information'].'</b></td>
      </tr>';


		foreach($updates as $value=>$package) {

			$updateinfo = explode("#",$package);
			//get packageinfos
			if($updateinfo[0]=="noupdates") {
				echo '<tr><td class="td1" colspan="4">'.$_language->module['no_updates'].'</td></tr>';
			} else {
				echo '<tr>
          <td class="td1"><a href="'.$updateserver.'?package='.$updateinfo[0].'" target="_blank">'.$updateinfo[0].'.php</a></td>
          <td class="td1">'.$updateinfo[1].'</td>
          <td class="td1">'.$updateinfo[2].'</td>
        </tr>';
			}
		}
		echo'</table>
    <br /><br />&raquo; <a href="'.$updateserver.'?get=true" target="_blank"><b>'.$_language->module['get_new_version'].'</b></a>';
	}
}
?>