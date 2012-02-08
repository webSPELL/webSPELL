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

if(isset($_GET['new_lang'])) {
	
	if(file_exists('languages/'.$_GET['new_lang'])) {
	
		include("_mysql.php");
		include("_settings.php");
		include("_functions.php");
		if($userID) {
			$lang = $_GET['new_lang'];
			safe_query("UPDATE ".PREFIX."user SET language='".$lang."' WHERE userID='".$userID."'");
		}
		else{
			$_SESSION['language'] = $_GET['new_lang'];
		}

	}

	if(isset($_GET['query'])) {

		$query = rawurldecode($_GET['query']);
		header("Location: index.php?".$query);

	} else header("Location: index.php");

}
else {

	$_language->read_module('sc_language');

	$filepath = "languages/";
	$langs = array();
	// Select all possible languages
	$mysql_langs = array();
	$query = safe_query("SELECT lang, language FROM ".PREFIX."news_languages");
	while($ds = mysql_fetch_assoc($query)){
		$mysql_langs[$ds['lang']] = $ds['language'];
	}
	
	if($dh = opendir($filepath)) {
		while($file = mb_substr(readdir($dh), 0, 2)) {
			if($file != "." and $file!=".." and is_dir($filepath.$file)) {
				if(isset($mysql_langs[$file])){
					$name = $mysql_langs[$file];
					$name = ucfirst($name);
					$langs[] = array($file,$name);
				}
				else{
					$langs[] = array($file,$file);
				}
			}
		}
		closedir($dh);
	}
	
	foreach($langs as $lang){
		$querystring='';
		if($_SERVER['QUERY_STRING']) $querystring = "&amp;query=".rawurlencode($_SERVER['QUERY_STRING']);
		echo '<a href="sc_language.php?new_lang='.$lang[0].$querystring.'" title="'.$lang[1].'">';
		if($_language->language == $lang[0]){
			 echo '<img src="images/haken.gif" alt="'.$lang[1].'" border="0" style="background-image:url(\'images/flags/'.$lang[0].'.gif\'); background-position: center;" />';
		}	 
		else { 
			echo '<img src="images/flags/'.$lang[0].'.gif" alt="'.$lang[1].'" border="0" />';
		} 	
		echo "</a> ";
	}
}
?>