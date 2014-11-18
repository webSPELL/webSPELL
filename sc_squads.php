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

$ergebnis=safe_query("SELECT * FROM ".PREFIX."squads WHERE gamesquad = '1' ORDER BY sort");
if(mysqli_num_rows($ergebnis)) {
	echo '<ul class="list-group">';
	$n=1;
	while($db=mysqli_fetch_array($ergebnis)) {
		if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}
		$n++;
		if(!empty($db['icon_small'])) $squadicon='<img src="images/squadicons/'.$db['icon_small'].'" style="margin:2px 0;" alt="'.getinput($db['name']).'" title="'.getinput($db['name']).'" />';
		else $squadicon='';
		$squadname=getinput($db['name']);
		eval ("\$sc_squads = \"".gettemplate("sc_squads")."\";");
		echo $sc_squads;
	}
	echo '</ul>';
}
?>