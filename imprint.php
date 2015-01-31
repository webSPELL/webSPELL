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

$_language->readModule('imprint');

$title_imprint = $GLOBALS["_template"]->replaceTemplate("title_imprint", array());
echo $title_imprint;

$ergebnis =
    safe_query(
        "SELECT
            u.firstname, u.lastname, u.nickname, u.userID
        FROM
            " . PREFIX . "user_groups as g, " . PREFIX . "user as u
        WHERE
            u.userID = g.userID
        AND
            (g.page='1'
        OR
            g.forum='1'
        OR
            g.user='1'
        OR
            g.news='1'
        OR
            g.clanwars='1'
        OR
            g.feedback='1'
        OR
            g.super='1'
        OR
            g.gallery='1'
        OR
            g.cash='1'
        OR
            g.files='1')"
    );
$administrators = '';
while ($ds = mysqli_fetch_array($ergebnis)) {
    $administrators .= "<a href='index.php?site=profile&amp;id=" . $ds[ 'userID' ] . "'>" . $ds[ 'firstname' ] . " '" .
        $ds[ 'nickname' ] . "' " . $ds[ 'lastname' ] . "</a><br>";
}
$ergebnis =
    safe_query(
        "SELECT
            u.firstname, u.lastname, u.nickname, u.userID
        FROM
            " . PREFIX . "user_groups as g, " . PREFIX . "user as u
        WHERE
            u.userID = g.userID
        AND
            g.moderator='1'"
    );
$moderators = '';
while ($ds = mysqli_fetch_array($ergebnis)) {
    $moderators .= "<a href='index.php?site=profile&amp;id=" . $ds[ 'userID' ] . "'>" . $ds[ 'firstname' ] . " '" .
        $ds[ 'nickname' ] . "' " . $ds[ 'lastname' ] . "</a><br>";
}

// reading version
include('version.php');

$headline1 = $_language->module[ 'imprint' ];
$headline2 = $_language->module[ 'coding' ];

if ($imprint_type) {
    $ds = mysqli_fetch_array(safe_query("SELECT imprint FROM `" . PREFIX . "imprint`"));
    $imprint_head = htmloutput($ds[ 'imprint' ]);
} else {
    $imprint_head = '<div class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-3 control-label">' . $_language->module[ 'webmaster' ] . '</label>
            <div class="col-sm-9">
                <p class="form-control-static">
                    <a href="mailto:' . mail_protect($admin_email) . '">' . $admin_name . '</a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">' . $_language->module[ 'admins' ] . '</label>
            <div class="col-sm-9">
                <p class="form-control-static">' . $administrators . '</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">' . $_language->module[ 'webmaster' ] . '</label>
            <div class="col-sm-9">
                <p class="form-control-static">' . $moderators . '</p>
            </div>
        </div>
    </div>';
}

$data_array = array();
$data_array['$headline1'] = $headline1;
$data_array['$imprint_head'] = $imprint_head;
$data_array['$headline2'] = $headline2;
$data_array['$version'] = $version;
$imprint = $GLOBALS["_template"]->replaceTemplate("imprint", $data_array);
echo $imprint;
