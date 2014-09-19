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

$_language->read_module('settings');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['settings'].'</h1>';

if(isset($_POST['submit'])) {
 	$CAPCLASS = new Captcha;
	if($CAPCLASS->check_captcha(0, $_POST['captcha_hash'])) {
		safe_query("UPDATE ".PREFIX."settings SET hpurl='".$_POST['url']."',
									 clanname='".$_POST['clanname']."',
									 clantag='".$_POST['clantag']."',
									 adminname='".$_POST['admname']."',
									 adminemail='".$_POST['admmail']."',
									 news='".$_POST['news']."',
									 newsarchiv='".$_POST['newsarchiv']."',
									 headlines='".$_POST['headlines']."',
									 headlineschars='".$_POST['headlineschars']."',
									 topnewschars='".$_POST['topnewschars']."',
									 articles='".$_POST['articles']."',
									 latestarticles='".$_POST['latestart']."',
									 articleschars='".$_POST['articlesch']."',
									 clanwars='".$_POST['clanwars']."',
									 results='".$_POST['results']."',
									 upcoming='".$_POST['upcoming']."',
									 shoutbox='".$_POST['shoutbox']."',
									 sball='".$_POST['sball']."',
									 sbrefresh='".$_POST['refresh']."',
									 topics='".$_POST['topics']."',
									 posts='".$_POST['posts']."',
									 latesttopics='".$_POST['latesttopics']."',
									 latesttopicchars='".$_POST['latesttopicchars']."',
									 awards='".$_POST['awards']."',
									 demos='".$_POST['demos']."',
									 guestbook='".$_POST['guestbook']."',
									 feedback='".$_POST['feedback']."',
									 messages='".$_POST['messages']."',
									 users='".$_POST['users']."',
									 sessionduration='".$_POST['sessionduration']."',
									 gb_info='".isset($_POST['gb_info'])."',
									 picsize_l='".$_POST['picsize_l']."',
									 picsize_h='".$_POST['picsize_h']."',
									 pictures='".$_POST['pictures']."',
									 publicadmin='".isset($_POST['publicadmin'])."',
									 thumbwidth='".$_POST['thumbwidth']."',
									 usergalleries='".isset($_POST['usergalleries'])."',
									 maxusergalleries='".($_POST['maxusergalleries']*1024*1024)."',
									 profilelast='".$_POST['lastposts']."',
									 default_language='".$_POST['language']."',
									 insertlinks='".isset($_POST['insertlinks'])."',
									 search_min_len='".$_POST['searchminlen']."',
									 max_wrong_pw='".intval($_POST['max_wrong_pw'])."',
									 captcha_type='".intval($_POST['captcha_type'])."',
									 captcha_bgcol='".$_POST['captcha_bgcol']."',
									 captcha_fontcol='".$_POST['captcha_fontcol']."',
									 captcha_math='".$_POST['captcha_math']."',
									 captcha_noise='".$_POST['captcha_noise']."',
									 captcha_linenoise='".$_POST['captcha_linenoise']."',
									 autoresize='".$_POST['autoresize']."'");
		safe_query("UPDATE ".PREFIX."styles SET title='".$_POST['title']."' ");	
	  	redirect("admincenter.php?site=settings","",0);
	} else redirect("admincenter.php?site=settings",$_language->module['transaction_invalid'],3);
}

else {

	$settings=safe_query("SELECT * FROM ".PREFIX."settings");
	$ds=mysql_fetch_array($settings);

	$styles=safe_query("SELECT * FROM ".PREFIX."styles");
	$dt=mysql_fetch_array($styles);

	if($ds['gb_info']) $gb_info='<input type="checkbox" name="gb_info" value="1" checked="checked" onmouseover="showWMTT(\'id36\')" onmouseout="hideWMTT()" />';
	else $gb_info='<input type="checkbox" name="gb_info" value="1" onmouseover="showWMTT(\'id36\')" onmouseout="hideWMTT()" />';

	if($ds['publicadmin']) $publicadmin = " checked=\"checked\"";
	else $publicadmin = "";
	if($ds['usergalleries']) $usergalleries = " checked=\"checked\"";
	else $usergalleries = "";

	$langdirs = '';
	$filepath = "../languages/";
	if ($dh = opendir($filepath)) {
		while($file = mb_substr(readdir($dh), 0,2)) {
			if($file!="." AND $file!=".." AND is_dir($filepath.$file)) $langdirs .= '<option value="'.$file.'">'.$file.'</option>';
		}
		closedir($dh);
	}
	$lang = $ds['default_language'];
	$langdirs = str_replace('"'.$lang.'"', '"'.$lang.'" selected="selected"', $langdirs);
  
  	if($ds['insertlinks']) $insertlinks='<input type="checkbox" name="insertlinks" value="1" checked="checked" onmouseover="showWMTT(\'id41\')" onmouseout="hideWMTT()" />';
	else $insertlinks='<input type="checkbox" name="insertlinks" value="1" onmouseover="showWMTT(\'id41\')" onmouseout="hideWMTT()" />';
  	
	$captcha_style = "<option value='0'>".$_language->module['captcha_only_text']."</option><option value='2'>".$_language->module['captcha_both']."</option><option value='1'>".$_language->module['captcha_only_math']."</option>";
	$captcha_style = str_replace("value='".$ds['captcha_math']."'","value='".$ds['captcha_math']."' selected='selected'",$captcha_style);
	
	$captcha_type = "<option value='0'>".$_language->module['captcha_text']."</option><option value='2'>".$_language->module['captcha_autodetect']."</option><option value='1'>".$_language->module['captcha_image']."</option>";
	$captcha_type = str_replace("value='".$ds['captcha_type']."'","value='".$ds['captcha_type']."' selected='selected'",$captcha_type);
	
	$autoresize = "<option value='0'>".$_language->module['autoresize_off']."</option><option value='2'>".$_language->module['autoresize_js']."</option><option value='1'>".$_language->module['autoresize_php']."</option>";
	$autoresize = str_replace("value='".$ds['autoresize']."'","value='".$ds['autoresize']."' selected='selected'",$autoresize);
	
	$CAPCLASS = new Captcha;
	$CAPCLASS->create_transaction();
	$hash = $CAPCLASS->get_hash();
?>

<form method="post" action="admincenter.php?site=settings">
<div class="tooltip" id="id1"><?php echo $_language->module['tooltip_1']; ?> '<?php echo $_SERVER['HTTP_HOST']; ?>'</div>
<div class="tooltip" id="id2"><?php echo $_language->module['tooltip_2']; ?></div>
<div class="tooltip" id="id3"><?php echo $_language->module['tooltip_3']; ?></div>
<div class="tooltip" id="id4"><?php echo $_language->module['tooltip_4']; ?></div>
<div class="tooltip" id="id5"><?php echo $_language->module['tooltip_5']; ?></div>
<div class="tooltip" id="id6"><?php echo $_language->module['tooltip_6']; ?></div>
<div class="tooltip" id="id7"><?php echo $_language->module['tooltip_7']; ?></div>
<div class="tooltip" id="id8"><?php echo $_language->module['tooltip_8']; ?></div>
<div class="tooltip" id="id9"><?php echo $_language->module['tooltip_9']; ?></div>
<div class="tooltip" id="id10"><?php echo $_language->module['tooltip_10']; ?></div>
<div class="tooltip" id="id11"><?php echo $_language->module['tooltip_11']; ?></div>
<div class="tooltip" id="id12"><?php echo $_language->module['tooltip_12']; ?></div>
<div class="tooltip" id="id13"><?php echo $_language->module['tooltip_13']; ?></div>
<div class="tooltip" id="id14"><?php echo $_language->module['tooltip_14']; ?></div>
<div class="tooltip" id="id15"><?php echo $_language->module['tooltip_15']; ?></div>
<div class="tooltip" id="id16"><?php echo $_language->module['tooltip_16']; ?></div>
<div class="tooltip" id="id17"><?php echo $_language->module['tooltip_17']; ?></div>
<div class="tooltip" id="id18"><?php echo $_language->module['tooltip_18']; ?></div>
<div class="tooltip" id="id19"><?php echo $_language->module['tooltip_19']; ?></div>
<div class="tooltip" id="id20"><?php echo $_language->module['tooltip_20']; ?></div>
<div class="tooltip" id="id21"><?php echo $_language->module['tooltip_21']; ?></div>
<div class="tooltip" id="id22"><?php echo $_language->module['tooltip_22']; ?></div>
<div class="tooltip" id="id23"><?php echo $_language->module['tooltip_23']; ?></div>
<div class="tooltip" id="id24"><?php echo $_language->module['tooltip_24']; ?></div>
<div class="tooltip" id="id25"><?php echo $_language->module['tooltip_25']; ?></div>
<div class="tooltip" id="id26"><?php echo $_language->module['tooltip_26']; ?></div>
<div class="tooltip" id="id27"><?php echo $_language->module['tooltip_27']; ?></div>
<div class="tooltip" id="id28"><?php echo $_language->module['tooltip_28']; ?></div>
<div class="tooltip" id="id29"><?php echo $_language->module['tooltip_29']; ?></div>
<div class="tooltip" id="id30"><?php echo $_language->module['tooltip_30']; ?></div>
<div class="tooltip" id="id31"><?php echo $_language->module['tooltip_31']; ?></div>
<div class="tooltip" id="id32"><?php echo $_language->module['tooltip_32']; ?></div>
<div class="tooltip" id="id33"><?php echo $_language->module['tooltip_33']; ?></div>
<div class="tooltip" id="id34"><?php echo $_language->module['tooltip_34']; ?></div>
<div class="tooltip" id="id35"><?php echo $_language->module['tooltip_35']; ?></div>
<div class="tooltip" id="id36"><?php echo $_language->module['tooltip_36']; ?></div>
<div class="tooltip" id="id37"><?php echo $_language->module['tooltip_37']; ?></div>
<div class="tooltip" id="id38"><?php echo $_language->module['tooltip_38']; ?></div>
<div class="tooltip" id="id39"><?php echo $_language->module['tooltip_39']; ?></div>
<div class="tooltip" id="id40"><?php echo $_language->module['tooltip_40']; ?></div>
<div class="tooltip" id="id41"><?php echo $_language->module['tooltip_41']; ?></div>
<div class="tooltip" id="id42"><?php echo $_language->module['tooltip_42']; ?></div>
<div class="tooltip" id="id43"><?php echo $_language->module['tooltip_43']; ?></div>
<div class="tooltip" id="id44"><?php echo $_language->module['tooltip_44']; ?></div>
<div class="tooltip" id="id45"><?php echo $_language->module['tooltip_45']; ?></div>
<div class="tooltip" id="id46"><?php echo $_language->module['tooltip_46']; ?></div>
<div class="tooltip" id="id47"><?php echo $_language->module['tooltip_47']; ?></div>
<div class="tooltip" id="id48"><?php echo $_language->module['tooltip_48']; ?></div>
<div class="tooltip" id="id49"><?php echo $_language->module['tooltip_49']; ?></div>
<div class="tooltip" id="id50"><?php echo $_language->module['tooltip_50']; ?></div>
<div class="tooltip" id="id51"><?php echo $_language->module['tooltip_51']; ?></div>

<table width="100%" border="0" cellspacing="1" cellpadding="3">
  <tr>
    <td width="15%"><b><?php echo $_language->module['page_title']; ?></b></td>
    <td width="35%"><input name="title" type="text" value="<?php echo getinput($dt['title']); ?>" size="35" onmouseover="showWMTT('id2')" onmouseout="hideWMTT()" /></td>
    <td width="15%"><b><?php echo $_language->module['page_url']; ?></b></td>
    <td width="35%"><input type="text" name="url" value="<?php echo getinput($ds['hpurl']); ?>" size="35" onmouseover="showWMTT('id1')" onmouseout="hideWMTT()" /></td>
  </tr>
  <tr>
    <td><b><?php echo $_language->module['clan_name']; ?></b></td>
    <td><input type="text" name="clanname" value="<?php echo getinput($ds['clanname']); ?>" size="35" onmouseover="showWMTT('id3')" onmouseout="hideWMTT()" /></td>
    <td><b><?php echo $_language->module['clan_tag']; ?></b></td>
    <td><input type="text" name="clantag" value="<?php echo getinput($ds['clantag']); ?>" size="35" onmouseover="showWMTT('id4')" onmouseout="hideWMTT()" /></td>
  </tr>
  <tr>
    <td><b><?php echo $_language->module['admin_name']; ?></b></td>
    <td><input type="text" name="admname" value="<?php echo getinput($ds['adminname']); ?>" size="35" onmouseover="showWMTT('id5')" onmouseout="hideWMTT()" /></td>
    <td><b><?php echo $_language->module['admin_email']; ?></b></td>
    <td><input type="text" name="admmail" value="<?php echo getinput($ds['adminemail']); ?>" size="35" onmouseover="showWMTT('id6')" onmouseout="hideWMTT()" /></td>
  </tr>
</table>
<br /><br />
<table width="100%" border="0" cellspacing="1" cellpadding="3">
  <tr>
    <td colspan="2"><b><?php echo $_language->module['additional_options']; ?>:</b></td>
  </tr>
  <tr>
    <td>&#8226; <a href="admincenter.php?site=lock"><b><?php echo $_language->module['pagelock']; ?></b></a></td>
  </tr>
</table>
<br />

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['news']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
    <tr>
	    <td align="right"><input name="news" type="text" value="<?php echo $ds['news']; ?>" size="3" onmouseover="showWMTT('id7')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['news']; ?></td>
	  </tr>
	  <tr>
	    <td align="right"><input name="newsarchiv" type="text" value="<?php echo $ds['newsarchiv']; ?>" size="3" onmouseover="showWMTT('id10')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['archive']; ?></td>
	  </tr>
    <tr>
    	<td align="right"><input type="text" name="headlines" value="<?php echo $ds['headlines']; ?>" size="3" onmouseover="showWMTT('id13')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['headlines']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="headlineschars" value="<?php echo $ds['headlineschars']; ?>" size="3" onmouseover="showWMTT('id16')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['max_length_headlines']; ?></td>
	  </tr>
  	  <tr>
	    <td align="right"><input type="text" name="topnewschars" value="<?php echo $ds['topnewschars']; ?>" size="3" onmouseover="showWMTT('id51')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['max_length_topnews']; ?></td>
	  </tr>
	</table>
</div>

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['captcha']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
    <tr>
	    <td align="right"><select name="captcha_type" onmouseover="showWMTT('id44')" onmouseout="hideWMTT()"><?php echo $captcha_type;?></select></td>
	    <td><?php echo $_language->module['captcha_type']; ?></td>
	  </tr>
	  <tr>
	    <td align="right"><input type="text" name="captcha_bgcol" size="7" value="<?php echo $ds['captcha_bgcol']; ?>" onmouseover="showWMTT('id45')" onmouseout="hideWMTT()" /></td>
	  	<td><?php echo $_language->module['captcha_bgcol']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="captcha_fontcol" size="7" value="<?php echo $ds['captcha_fontcol']; ?>" onmouseover="showWMTT('id46')" onmouseout="hideWMTT()" /></td>
	  	<td><?php echo $_language->module['captcha_fontcol']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><select name="captcha_math" onmouseover="showWMTT('id47')" onmouseout="hideWMTT()"><?php echo $captcha_style;?></select></td>
	    <td><?php echo $_language->module['captcha_style']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="captcha_noise" size="3" value="<?php echo $ds['captcha_noise']; ?>" onmouseover="showWMTT('id48')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['captcha_noise']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="captcha_linenoise" size="3" value="<?php echo $ds['captcha_linenoise']; ?>" onmouseover="showWMTT('id49')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['captcha_linenoise']; ?></td>
	  </tr>
	</table>
</div>

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['forum']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="topics" value="<?php echo $ds['topics']; ?>" size="3" onmouseover="showWMTT('id8')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['forum_topics']; ?></td>
	  </tr>
	  <tr>
	    <td align="right"><input type="text" name="posts" value="<?php echo $ds['posts']; ?>" size="3" onmouseover="showWMTT('id11')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['forum_posts']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="latesttopics" value="<?php echo $ds['latesttopics']; ?>" size="3" onmouseover="showWMTT('id14')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['latest_topics']; ?></td>
	  </tr>
  	  <tr>
	    <td align="right"><input type="text" name="latesttopicchars" value="<?php echo $ds['latesttopicchars']; ?>" size="3" onmouseover="showWMTT('id42')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['max_length_latest_topics']; ?></td>
	  </tr>
	</table>
</div>

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['gallery']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td align="right"><input type="text" name="pictures" value="<?php echo $ds['pictures']; ?>" size="3" onmouseover="showWMTT('id9')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['pictures']; ?></td>
	  </tr>
	  <tr>
	    <td align="right"><input type="text" name="thumbwidth" value="<?php echo $ds['thumbwidth']; ?>" size="3" onmouseover="showWMTT('id12')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['thumb_width']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="maxusergalleries" value="<?php echo ($ds['maxusergalleries']/(1024*1024)); ?>" size="3" onmouseover="showWMTT('id15')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['space_user']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="checkbox" name="usergalleries" value="1" <?php echo $usergalleries; ?> onmouseover="showWMTT('id18')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['allow_usergalleries']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="checkbox" name="publicadmin" value="1" <?php echo $publicadmin; ?> onmouseover="showWMTT('id19')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['public_admin']; ?></td>
	  </tr>
	</table>
</div>

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['articles']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
    <tr>
	    <td align="right"><input name="articles" type="text" value="<?php echo $ds['articles']; ?>" size="3" onmouseover="showWMTT('id20')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['articles']; ?></td>
	  </tr>
	  <tr>
	    <td align="right"><input name="latestart" type="text" id="latestart" value="<?php echo $ds['latestarticles']; ?>" size="3" onmouseover="showWMTT('id22')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['latest_articles']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input name="articlesch" type="text" id="articlesch" value="<?php echo $ds['articleschars']; ?>" size="3" onmouseover="showWMTT('id24')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['max_length_latest_articles']; ?></td>
	  </tr>
	</table>
</div>

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['clanwars']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
	  <tr>
	    <td align="right"><input type="text" name="clanwars" value="<?php echo $ds['clanwars']; ?>" size="3" onmouseover="showWMTT('id28')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['clanwars']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input name="results" type="text" value="<?php echo $ds['results']; ?>" size="3" onmouseover="showWMTT('id30')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['latest_results']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="upcoming" value="<?php echo $ds['upcoming']; ?>" size="3" onmouseover="showWMTT('id32')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['upcoming_actions']; ?></td>
	  </tr>
	</table>
</div>

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['other']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
    <tr>
	    <td align="right"><input name="awards" type="text" value="<?php echo $ds['awards']; ?>" size="3" onmouseover="showWMTT('id21')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['awards']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input name="demos" type="text" value="<?php echo $ds['demos']; ?>" size="3" onmouseover="showWMTT('id23')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['demos']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="guestbook" value="<?php echo $ds['guestbook']; ?>" size="3" onmouseover="showWMTT('id25')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['guestbook']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="feedback" value="<?php echo $ds['feedback']; ?>" size="3" onmouseover="showWMTT('id26')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['comments']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="messages" value="<?php echo $ds['messages']; ?>" size="3" onmouseover="showWMTT('id27')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['messenger']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="users" value="<?php echo $ds['users']; ?>" size="3" onmouseover="showWMTT('id29')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['registered_users']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input name="lastposts" type="text" id="lastposts" value="<?php echo $ds['profilelast']; ?>" size="3" onmouseover="showWMTT('id31')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['profile_last_posts']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="sessionduration" value="<?php echo $ds['sessionduration']; ?>" size="3" onmouseover="showWMTT('id33')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['login_duration']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><?php echo $gb_info; ?></td>
	    <td><?php echo $_language->module['msg_on_gb_entry']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><select name="language" onmouseover="showWMTT('id40')" onmouseout="hideWMTT()"><?php echo $langdirs; ?></select></td>
	    <td><?php echo $_language->module['default_language']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><?php echo $insertlinks; ?></td>
	    <td><?php echo $_language->module['insert_links']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="searchminlen" value="<?php echo $ds['search_min_len']; ?>" size="3" onmouseover="showWMTT('id17')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['search_min_length']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="max_wrong_pw" value="<?php echo $ds['max_wrong_pw']; ?>" size="3" onmouseover="showWMTT('id43')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['max_wrong_pw']; ?></td>
	  </tr>
	  <tr>
	    <td align="right"><input type="text" name="picsize_l" value="<?php echo $ds['picsize_l']; ?>" size="3" onmouseover="showWMTT('id34')" onmouseout="hideWMTT()" /> x <input type="text" name="picsize_h" value="<?php echo $ds['picsize_h']; ?>" size="3" onmouseover="showWMTT('id35')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['content_size']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><select name="autoresize" onmouseover="showWMTT('id50')" onmouseout="hideWMTT()"><?php echo $autoresize;?></select></td>
	    <td><?php echo $_language->module['autoresize']; ?></td>
	  </tr>
	</table>
</div>

<div style="width: 45%;float: left;">
	<table width="100%" border="0" cellspacing="1" cellpadding="3">
	  <tr>
	    <td width="50%"><b><?php echo $_language->module['shoutbox']; ?>:</b></td>
	    <td>&nbsp;</td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="shoutbox" value="<?php echo $ds['shoutbox']; ?>" size="3" onmouseover="showWMTT('id37')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['shoutbox']; ?></td>
	  </tr>
	  <tr>
	    <td align="right"><input type="text" name="sball" value="<?php echo $ds['sball']; ?>" size="3" onmouseover="showWMTT('id38')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['shoutbox_all_messages']; ?></td>
	  </tr>
    <tr>
	    <td align="right"><input type="text" name="refresh" value="<?php echo $ds['sbrefresh']; ?>" size="3" onmouseover="showWMTT('id39')" onmouseout="hideWMTT()" /></td>
	    <td><?php echo $_language->module['shoutbox_refresh']; ?></td>
	  </tr>
	</table>
</div>

<div style="clear: both; text-align: right; padding-top: 20px;">
  <input type="hidden" name="captcha_hash" value="<?php echo $hash; ?>" />
  <input type="submit" name="submit" value="<?php echo $_language->module['update']; ?>" />
</div>
</form>
<?php
}
?>