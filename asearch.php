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
$_language->read_module('asearch');

//allowed tables for search
$allowed_tables=array("user");
$allowed_columns=array("nickname");
$allowed_identifiers=array("userID");
$allowed_searchtemps=array("search_user");

$table=$_GET['table'];
if(!in_array($table, $allowed_tables)) die($_language->module['invalid_request']);
$column=$_GET['column'];
if(!in_array($column, $allowed_columns)) die($_language->module['invalid_request']);
$identifier=$_GET['identifier'];
if(!in_array($identifier, $allowed_identifiers)) die($_language->module['invalid_request']);
$searchtemp=$_GET['searchtemp'];
if(!in_array($searchtemp, $allowed_searchtemps)) die($_language->module['invalid_request']);

$search=$_GET['search'];
$searchtype=$_GET['searchtype'];

if(get_magic_quotes_gpc()){
	$search=stripslashes($search);
}

if($searchtype=='ac_usersearch'){
	$search = mysql_real_escape_string(htmlspecialchars(rawurldecode($search)));
}
else{
	$search = mysql_real_escape_string(rawurldecode($search));
}

$div=$_GET['div'];

if(isset($_GET['exact'])){
	if($_GET['exact']=='true'){
		$exact=true;
	}
	else{
		$exact=false;
	}
}
else{
	$exact=false;
}
if($searchtype=='ac_usersearch'){
	if($exact){
		$db_results = safe_query("SELECT * FROM ".PREFIX.$table." WHERE ".$column."='".$search."'");
	}
	else{
		$db_results = safe_query("SELECT * FROM ".PREFIX.$table." WHERE ".$column." LIKE '%".$search."%'");
	}
}
else{
  if($exact){
		$db_results = safe_query("SELECT * FROM ".PREFIX.$table." WHERE ".$column."='".$search."'");
	}
	else{
		$db_results = safe_query("SELECT * FROM ".PREFIX.$table." WHERE ".$column." LIKE '%".$search."%'");
	}
}
$any=mysql_num_rows($db_results);

if ($any==0) {
	echo $_language->module['no_result'];
}
elseif ($any <= 100) {
	while ($row = mysql_fetch_array($db_results)) {
		$searchresult=stripslashes($row[$column]);
		$resultidentifier=$row[$identifier];
		eval ("\$resultemp = \"".gettemplate($searchtemp)."\";");
		echo $resultemp;
	}
}
else {
	echo $_language->module['to_much_results'];
}
?>