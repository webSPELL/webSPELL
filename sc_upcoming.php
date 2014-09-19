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

if (isset($site)) $_language->read_module('sc_upcoming');
$now=time();
$ergebnis=safe_query("SELECT * FROM ".PREFIX."upcoming WHERE date>= $now ORDER BY date LIMIT 0, ".$maxupcoming);
$n=1;
while($ds=mysql_fetch_array($ergebnis)) {
	echo'<ul class="list-group">';
	if($ds['type']=="c") {
		$date=date("d.m.Y", $ds['date']);
		$upsquad=getsquadname($ds['squad']);
    
    if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}

		$upurl='index.php?site=calendar&amp;tag='.date("d", $ds['date']).'&amp;month='.date("m", $ds['date']).'&amp;year='.date("Y", $ds['date']);

		$opponent=$ds['opponent'];
		
		eval ("\$upcomingactions = \"".gettemplate("upcomingactions")."\";");
		echo $upcomingactions;
	}
	else {
		$date=date("d.m.Y", $ds['date']);
		$country="[flag]".$ds['country']."[/flag]";
		$country=flags($country);
    
    if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}

		$upurl='index.php?site=calendar&amp;tag='.date("d", $ds['date']).'&amp;month='.date("m", $ds['date']).'&amp;year='.date("Y", $ds['date']);
    
		$eventtitle=$ds['title'];
		
		eval ("\$upcomingevent = \"".gettemplate("upcomingevent")."\";");
		echo $upcomingevent;
	}
  $n++;
  echo'</ul>';
}
$anzahl='';
?>
