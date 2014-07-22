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
#   Copyright 2005-2014 by webspell.org                                  #
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

if(basename($_SERVER['SCRIPT_FILENAME']) == basename("_rewrite.php")){
	include_once("_mysql.php");
	$_database = new mysqli($host, $user, $pwd, $db);

	if($_database->connect_error) {
		die('ERROR: Can not connect to MySQL-Server');
	}
	$_database->query("SET NAMES 'utf8'");

	$_site = null;
	$start_time = microtime(true);
	if(isset($_GET['url'])){
		$url_parts = preg_split("/[\._\/-]/", $_GET['url']);
		$first = $url_parts[0];
		$get = mysqli_query($_database, "SELECT * FROM ".PREFIX."modrewrite WHERE regex LIKE '%".$first."%' ORDER BY LENGTH(regex) ASC");
		while($ds = mysqli_fetch_assoc($get)){
			$replace = $ds['rebuild_result'];
			$regex = $ds['rebuild_regex'];
			$new = preg_replace("/".$regex."/i", $replace,$_GET['url'],-1,$replace_count);
			if($replace_count > 0){
				$url = parse_url($new);
				if(isset($url['query'])){
					$parts = explode("&",$url['query']);
					foreach($parts as $part){
						$k = explode("=",$part);
						$_GET[$k[0]] = $k[1];
						$_REQUEST[$k[0]] = $k[1];
					}
				}
				$_site = $url['path'];
				break;
			}
		}
	}

	if($_site == null){
		header("HTTP/1.0 404 Not Found");
		$_site = "index.php";
		$_GET['site'] = "error";
		$_GET['type'] = 404;
	}
	$needed = microtime(true)-$start_time;
	header('X-Rebuild-Time: '.$needed);
	require($_site);
}
?>