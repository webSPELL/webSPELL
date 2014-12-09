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

if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = null;
}

$_language->readModule('error');

if ($type == 404) {
    $error_header = $_language->module['error_404'];
    $error_message = $_language->module['message_404'];
}

if (isset($error_header)) {
    echo '<h2>' . $error_header . '</h2>';
    echo $error_message;
} else {
    echo '<h2>Error</h2>';
}

$urlparts = preg_split('/[\s.,-\/]+/si', $_GET['url']);
$results = [];
foreach ($urlparts as $tag) {
    $sql = safe_query("SELECT * FROM " . PREFIX . "tags WHERE tag='" . $tag . "'");
    if ($sql->num_rows) {
        while ($ds = mysqli_fetch_assoc($sql)) {
            $data_check = null;
            if ($ds['rel'] == "news") {
                $data_check = \webspell\Tags::getNews($ds['ID']);
            } elseif ($ds['rel'] == "articles") {
                $data_check = \webspell\Tags::getArticle($ds['ID']);
            } elseif ($ds['rel'] == "static") {
                $data_check = \webspell\Tags::getStaticPage($ds['ID']);
            } elseif ($ds['rel'] == "faq") {
                $data_check = \webspell\Tags::getFaq($ds['ID']);
            }
            if (is_array($data_check)) {
                $results[] = $data_check;
            }
        }
    }
}
if (count($results)) {
    echo "<h1>" . $_language->module['alternative_results'] . "</h1>";
    usort($results, ['Tags', 'sortByDate']);
    echo "<p class='text-center'><b>" . count($data) . "</b> " . $_language->module['results_found'] . "</p>";
    foreach ($results as $entry) {

        $date = getformatdate($entry['date']);
        $type = $entry['type'];
        $auszug = $entry['content'];
        $link = $entry['link'];
        $title = $entry['title'];
        eval ("\$search_tags = \"" . gettemplate("search_tags") . "\";");
        echo $search_tags;
    }
}
