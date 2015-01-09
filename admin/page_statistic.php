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

$_language->readModule('page_statistic');

if (!isanyadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

echo '<h1>&curren; ' . $_language->module[ 'page_stats' ] . '</h1>';

$count_array = [];
$tables_array = [
    PREFIX . "articles",
    PREFIX . "banner",
    PREFIX . "awards",
    PREFIX . "bannerrotation",
    PREFIX . "challenge",
    PREFIX . "clanwars",
    PREFIX . "comments",
    PREFIX . "contact",
    PREFIX . "countries",
    PREFIX . "demos",
    PREFIX . "faq",
    PREFIX . "faq_categories",
    PREFIX . "files",
    PREFIX . "files_categorys",
    PREFIX . "forum_announcements",
    PREFIX . "forum_boards",
    PREFIX . "forum_categories",
    PREFIX . "forum_groups",
    PREFIX . "forum_moderators",
    PREFIX . "forum_posts",
    PREFIX . "forum_ranks",
    PREFIX . "forum_topics",
    PREFIX . "gallery",
    PREFIX . "gallery_groups",
    PREFIX . "gallery_pictures",
    PREFIX . "games",
    PREFIX . "guestbook",
    PREFIX . "links",
    PREFIX . "links_categorys",
    PREFIX . "linkus",
    PREFIX . "messenger",
    PREFIX . "news",
    PREFIX . "news_languages",
    PREFIX . "news_rubrics",
    PREFIX . "partners",
    PREFIX . "poll",
    PREFIX . "servers",
    PREFIX . "shoutbox",
    PREFIX . "smileys",
    PREFIX . "sponsors",
    PREFIX . "squads",
    PREFIX . "static",
    PREFIX . "user",
    PREFIX . "user_gbook"
];
$db_size = 0;
$db_size_op = 0;
if (!isset($db)) {
    $get = safe_query("SELECT DATABASE()");
    $ret = mysqli_fetch_array($get);
    $db = $ret[ 0 ];
}
$query = safe_query("SHOW TABLES");

$count_tables = mysqli_num_rows($query);
foreach ($tables_array as $table) {
    $table_name = $table;
    $sql = safe_query("SHOW TABLE STATUS FROM `" . $db . "` LIKE '" . $table_name . "'");
    $data = mysqli_fetch_array($sql);
    $db_size += ($data[ 'Data_length' ] + $data[ 'Index_length' ]);
    if (strtolower($data[ 'Engine' ]) == "myisam") {
        $db_size_op += $data[ 'Data_free' ];
    }

    $table_base_name = str_replace(PREFIX, "", $table_name);
    if (isset($_language->module[ $table_base_name ])) {
        $table_name = $_language->module[ $table_base_name ];
    } else {
        $table_name = ucfirst(str_replace("_", " ", $table_name));
    }
    $count_array[ ] = [$table_name, $data[ 'Rows' ]];
}
?>

<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
        <td class="title" colspan="4"><b><?php echo $_language->module[ 'database' ]; ?></b></td>
    </tr>
    <tr>
        <td width="25%" class="td1"><b><?php echo $_language->module[ 'mysql_version' ]; ?></b></td>
        <td width="25%" class="td1"><?php echo mysqli_get_server_info($_database); ?></td>
        <td width="25%" class="td1"><b><?php echo $_language->module[ 'overhead' ]; ?></b></td>
        <td width="25%" class="td1"><?php echo $db_size_op; ?> Bytes
            <?php
            if ($db_size_op != 0) {
                echo '<a href="admincenter.php?site=database&amp;action=optimize&amp;back=page_statistic">
                    <b style="color: red">' . $_language->module[ 'optimize' ] . '</b>
                </a>';
            }
            ?></td>
    </tr>
    <tr>
        <td class="td2"><b><?php echo $_language->module[ 'size' ]; ?></b></td>
        <td class="td2"><?php echo $db_size; ?> Bytes (<?php
                echo round($db_size / 1024 / 1024, 2);
            ?> MB)</td>
        <td class="td2"><b><?php echo $_language->module[ 'tables' ]; ?></b></td>
        <td class="td2"><?php echo $count_tables; ?></td>
    </tr>
</table>
<br/><br/>
<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
    <tr>
        <td class="title" colspan="4"><b><?php echo $_language->module[ 'page_stats' ]; ?></b></td>
    </tr>
    <?php
    $counter = count($count_array);
    for ($i = 0; $i < $counter; $i += 1) {        
        if ($i % 4) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }
        ?>
        <tr>
            <td width="25%" class="<?php
                echo $td;
            ?>"><b><?php
                    echo $count_array[ $i ][ 0 ];
                    ?></b>
            </td>
            <td width="25%" class="<?php
                echo $td;
            ?>"><?php
                echo $count_array[ $i ][ 1 ];
                ?>
            </td>
    <?php
    if (isset($count_array[ $i + 1 ])) {
        ?>
                <td width="25%" class="<?php
                    echo $td;
                ?>"><b><?php
                        echo $count_array[ $i + 1 ][ 0 ];
                        ?></b></td>
                <td width="25%" class="<?php
                    echo $td;
                ?>"><?php
                    echo $count_array[ $i + 1 ][ 1 ];
                    ?>
                </td>
    <?php
    } else {
        ?>
                <td width="25" class="<?php echo $td; ?>"></td>
                <td width="25" class="<?php echo $td; ?>"></td>
    <?php
    }
        ?>
        </tr>
    <?php
        $i++;
    }

    echo '</table>';
