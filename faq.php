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

$_language->readModule('faq');

$title_faq = $GLOBALS["_template"]->replaceTemplate("title_faq", array());
echo $title_faq;

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "faqcat" && is_numeric($_GET[ 'faqcatID' ])) {
    if (ispageadmin($userID)) {
        echo '<input type="button" onclick="window.open(
            \'admin/admincenter.php?site=faq\',
            \'News\',\'toolbar=yes,status=yes,scrollbars=yes,resizable=yes,width=800,height=600\'
        )" value="' . $_language->module[ 'admin_button' ] . '" class="btn btn-danger"><br><br>';
    }

    $faqcatID = $_GET[ 'faqcatID' ];
    $get = safe_query("SELECT faqcatname FROM " . PREFIX . "faq_categories WHERE faqcatID='" . (int)$faqcatID . "'");
    $dc = mysqli_fetch_assoc($get);
    $faqcatname = $dc[ 'faqcatname' ];

    $faqcat = safe_query(
        "SELECT
            `question`,
            `faqID`,
            `sort`
        FROM
            `" . PREFIX . "faq`
        WHERE
            `faqcatID` = '" . (int)$faqcatID . "'
        ORDER BY
            sort"
    );
    if (mysqli_num_rows($faqcat)) {
        $data_array = array();
        $data_array['$faqcatname'] = $faqcatname;
        $faq_question_head = $GLOBALS["_template"]->replaceTemplate("faq_question_head", $data_array);
        echo $faq_question_head;
        $i = 1;
        while ($ds = mysqli_fetch_array($faqcat)) {
            if ($i % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }
            $i++;

            $sort = $ds[ 'sort' ];
            $question = '<a href="index.php?site=faq&amp;action=faq&amp;faqID=' . $ds[ 'faqID' ] . '&amp;faqcatID=' .
                $faqcatID . '" class="list-group-item">' . $ds[ 'question' ] . '</a>';

            $data_array = array();
            $data_array['$question'] = $question;
            $faq_question = $GLOBALS["_template"]->replaceTemplate("faq_question", $data_array);
            echo $faq_question;
        }
        $faq_foot = $GLOBALS["_template"]->replaceTemplate("faq_foot", array());
        echo $faq_foot;
    } else {
        echo $_language->module[ 'no_faq' ];
    }
} elseif ($action == "faq") {
    if (ispageadmin($userID)) {
        echo
            '<p><input type="button" onclick="window.open(
                \'admin/admincenter.php?site=faq\',
                \'News\',\'toolbar=yes,status=yes,scrollbars=yes,resizable=yes,width=800,height=600\'
            )" value="' . $_language->module[ 'admin_button' ] . '" class="btn btn-danger"></p>';
    }

    $faqcatID = intval($_GET[ 'faqcatID' ]);
    $get = safe_query(
        "SELECT
            `faqcatname`
        FROM
            `" . PREFIX . "faq_categories`
        WHERE
            `faqcatID` = '" . (int)$faqcatID . "'"
    );
    $dc = mysqli_fetch_assoc($get);
    $faqcatname = $dc[ 'faqcatname' ];
    $faqID = intval($_GET[ 'faqID' ]);

    $faq = safe_query(
        "SELECT
            `faqcatID`,
            `date`,
            `question`,
            `answer`
        FROM
            `" . PREFIX . "faq`
        WHERE
            `faqID` = '" . (int)$faqID . "'"
    );
    if (mysqli_num_rows($faq)) {
        $ds = mysqli_fetch_array($faq);
        $backlink = '<a href="index.php?site=faq&amp;action=faqcat&amp;faqcatID=' . $faqcatID . '" class="titlelink">' .
            $faqcatname . '</a>';
        $question = $ds[ 'question' ];
        if (mb_strlen($question) > 40) {
            if ($question{39} == " ") {
                $question = mb_substr($question, 0, 38) . "...";
            } else {
                $question = mb_substr($question, 0, 40) . "...";
            }
        }

        $data_array = array();
        $data_array['$backlink'] = $backlink;
        $data_array['$question'] = $question;
        $faq_answer_head = $GLOBALS["_template"]->replaceTemplate("faq_answer_head", $data_array);
        echo $faq_answer_head;

        $bg1 = BG_1;
        $date = getformatdate($ds[ 'date' ]);
        $answer = htmloutput($ds[ 'answer' ]);

        $data_array = array();
        $data_array['$answer'] = $answer;
        $data_array['$date'] = $date;
        $faq_answer = $GLOBALS["_template"]->replaceTemplate("faq_answer", $data_array);
        echo $faq_answer;
    } else {
        echo $_language->module[ 'no_faq' ];
    }
} else {
    if (ispageadmin($userID)) {
        echo
            '<p><input type="button" onclick="window.open(
                \'admin/admincenter.php?site=faq\',
                \'News\',
                \'toolbar=yes,status=yes,scrollbars=yes,resizable=yes,width=800,height=600\'
            )" value="' . $_language->module[ 'admin_button' ] . '" class="btn btn-danger"></p>';
    }

    $faqcats = safe_query("SELECT * FROM `" . PREFIX . "faq_categories` ORDER BY `sort`");
    $anzcats = mysqli_num_rows($faqcats);
    if ($anzcats) {
        $data_array = array();
        $data_array['$anzcats'] = $anzcats;
        $faq_category_head = $GLOBALS["_template"]->replaceTemplate("faq_category_head", $data_array);
        echo $faq_category_head;
        $i = 1;
        while ($ds = mysqli_fetch_array($faqcats)) {
            $anzfaqs =
                mysqli_num_rows(
                    safe_query(
                        "SELECT
                            `faqID`
                        FROM
                            `" . PREFIX . "faq`
                        WHERE
                            `faqcatID` = '" . (int)$ds[ 'faqcatID' ] . "'"
                    )
                );
            if ($i % 2) {
                $bg1 = BG_1;
                $bg2 = BG_2;
            } else {
                $bg1 = BG_3;
                $bg2 = BG_4;
            }
            $faqcatname = '<a href="index.php?site=faq&amp;action=faqcat&amp;faqcatID=' . $ds[ 'faqcatID' ] . '">' .
                $ds[ 'faqcatname' ] . '</a>';
            $description = htmloutput($ds[ 'description' ]);

            $data_array = array();
            $data_array['$faqcatname'] = $faqcatname;
            $data_array['$anzfaqs'] = $anzfaqs;
            $data_array['$description'] = $description;
            $faq_category = $GLOBALS["_template"]->replaceTemplate("faq_category", $data_array);
            echo $faq_category;
            $i++;
        }
    } else {
        echo $_language->module[ 'no_categories' ];
    }
}
