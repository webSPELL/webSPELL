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

	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
  
	if(isset($_GET['modul'])) $modul = strip_tags($_GET['modul']);
	else $modul = null;
	if(isset($_GET['var'])) $var = $_GET['var'];
	else $var = null;
	if(isset($_GET['mode'])) $mode = $_GET['mode'];
	else $mode = 'plain';
	
	if(!is_null($modul)){
		if($_language->read_module($modul)){
			if($mode=='array'){
				foreach($_language->module as $key => $value){
					echo 'language_array["'.$modul.'"]["'.$key.'"]="'.preg_replace("/\r?\n/", "\\n", addslashes($value)).'";';
				}
			}
			else{
				if(!is_null($var)) echo $_language->module[$var];
				else echo "Error";
			}
		}
		else{
			echo "Error";
		}
	}
	else{
		echo "Error";
	}
?>