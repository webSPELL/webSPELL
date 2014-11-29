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

$_language->readModule('server');

eval("\$title_server = \"".gettemplate("title_server")."\";");
echo $title_server;

$ergebnis = safe_query("SELECT * FROM ".PREFIX."servers ORDER BY sort");

if(mysqli_num_rows($ergebnis)) {
	$i = 1;
	while($ds = mysqli_fetch_array($ergebnis)) {
		if($ds['game'] == "CS") $game = "HL";
		else $game = $ds['game'];

    	$showgame = getgamename($ds['game']);

    	$serverdata = explode(":", $ds['ip']);
		$ip = $serverdata[0];
		if(isset($serverdata[1])) $port = $serverdata[1];
		else $port='';

		if(!checkenv('disable_functions','fsockopen')) {
			if(!fsockopen("udp://".$ip, $port, $strErrNo, $strErrStr, 30)) $status= "<i>".$_language->module['timeout']."</i>";
			else $status = "<b>".$_language->module['online']."</b>";
		}
		else $status = "<i>".$_language->module['not_supported']."</i>";
    	$servername=htmloutput($ds['name']);
		$info=htmloutput($ds['info']);
		eval("\$server = \"".gettemplate("server")."\";");
		echo $server;
		$i++;
	}

}
else echo $_language->module['no_server'];

?>

