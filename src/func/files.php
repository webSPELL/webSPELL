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
#   Copyright 2005-2015 by webspell.org                                  #
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

function generateFileCategoryOptions($filecats = '', $offset = '', $subcatID = 0)
{
    $rubrics = safe_query(
        "SELECT
                    *
        FROM
        `" . PREFIX . "files_categorys`
        WHERE
        `subcatID` = '" . (int)$subcatID . "'
        ORDER BY
        name"
    );
    while ($dr = mysqli_fetch_array($rubrics)) {
        $filecats .= '<option value="' . $dr[ 'filecatID' ] . '">' .
                    $offset . htmlspecialchars($dr[ 'name' ]) . '</option>';
        if (
            mysqli_num_rows(
                safe_query(
                    "SELECT
                                *
                    FROM
                    `" . PREFIX . "files_categorys`
                    WHERE
                    `subcatID` = '" . (int)$dr[ 'filecatID' ]."'"
                )
            )
        ) {
            $filecats .= generateFileCategoryOptions("", $offset . "- ", $dr[ 'filecatID' ]);
        }
    }
    return $filecats;
}
