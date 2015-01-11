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
$result = safe_query("SELECT * FROM " . PREFIX . "user ORDER BY registerdate DESC LIMIT 0,5");
echo '<ul class="list-group">';
while ($row = mysqli_fetch_array($result)) {
    $username = '<a href="index.php?site=profile&amp;id=' . $row[ 'userID' ] . '">' . $row[ 'nickname' ] . '</a>';
    $country = flags('[flag]' . $row[ 'country' ] . '[/flag]');
    $registerdate = getformatdate($row[ 'registerdate' ]);
    $data_array = array();
    $data_array['$registerdate'] = $registerdate;
    $data_array['$country'] = $country;
    $data_array['$username'] = $username;
    $sc_lastregistered = $GLOBALS["_template"]->replaceTemplate("sc_lastregistered", $data_array);
    echo $sc_lastregistered;
}
echo '</ul>';
