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

$_language->read_module('page_statistic');

if(!isanyadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; '.$_language->module['page_stats'].'</h1>';

$count_array = array();
$tables_array = array(PREFIX."articles", PREFIX."banner", PREFIX."awards", PREFIX."bannerrotation", PREFIX."challenge", PREFIX."clanwars", PREFIX."comments", PREFIX."contact", PREFIX."countries", PREFIX."demos", PREFIX."faq", PREFIX."faq_categories", PREFIX."files", PREFIX."files_categorys", PREFIX."forum_announcements", PREFIX."forum_boards", PREFIX."forum_categories", PREFIX."forum_groups", PREFIX."forum_moderators", PREFIX."forum_posts", PREFIX."forum_ranks", PREFIX."forum_topics", PREFIX."gallery", PREFIX."gallery_groups", PREFIX."gallery_pictures", PREFIX."games", PREFIX."guestbook", PREFIX."links", PREFIX."links_categorys", PREFIX."linkus", PREFIX."messenger", PREFIX."news", PREFIX."news_languages", PREFIX."news_rubrics", PREFIX."partners", PREFIX."poll", PREFIX."servers", PREFIX."shoutbox", PREFIX."smileys", PREFIX."sponsors", PREFIX."squads", PREFIX."static", PREFIX."user", PREFIX."user_gbook");
$db_size = 0;
$db_size_op = 0;
if(!isset($db)){
	$get = safe_query("SELECT DATABASE()");
	$ret = mysql_fetch_array($get);
	$db = $ret[0];
}	
$query = safe_query("SHOW TABLES");

$count_tables = mysql_num_rows($query);
foreach($tables_array as $table){
 	$qs[0] = $table;
		$sql = safe_query("SHOW TABLE STATUS FROM `".$db."` LIKE '".$qs[0]."'");
		$data = mysql_fetch_array($sql);
		$db_size += ($data['Data_length'] + $data['Index_length']);
		if(strtolower($data['Engine']) == "myisam" ){
			$db_size_op += $data['Data_free'];
		}
		switch($qs[0]) {
		case PREFIX."articles":
				$qs[0] = $_language->module['articles'];
				break;
      case PREFIX."awards":
				$qs[0] = $_language->module['awards'];
				break;
		case PREFIX."banner":
				$qs[0] = $_language->module['banner'];
				break;
      case PREFIX."bannerrotation":
				$qs[0] = $_language->module['bannerrotation'];
				break;
      case PREFIX."challenge":
				$qs[0] = $_language->module['challenge'];
				break;
      case PREFIX."clanwars":
				$qs[0] = $_language->module['clanwars'];
				break;
      case PREFIX."comments":
				$qs[0] = $_language->module['comments'];
				break;
		case PREFIX."contact":
				$qs[0] = $_language->module['contacts'];
				break;
      case PREFIX."countries":
				$qs[0] = $_language->module['countries'];
				break;
		case PREFIX."demos":
				$qs[0] = $_language->module['demos'];
				break;
      case PREFIX."faq":
				$qs[0] = $_language->module['faq'];
				break;
      case PREFIX."faq_categories":
				$qs[0] = $_language->module['faq_categories'];
				break;
      case PREFIX."files":
				$qs[0] = $_language->module['files'];
				break;
      case PREFIX."files_categorys":
				$qs[0] = $_language->module['files_categorys'];
				break;
      case PREFIX."forum_announcements":
				$qs[0] = $_language->module['forum_announcements'];
				break;
      case PREFIX."forum_boards":
				$qs[0] = $_language->module['forum_boards'];
				break;
      case PREFIX."forum_categories":
				$qs[0] = $_language->module['forum_categories'];
				break;
      case PREFIX."forum_groups":
				$qs[0] = $_language->module['forum_groups'];
				break;
      case PREFIX."forum_moderators":
				$qs[0] = $_language->module['forum_moderators'];
				break;
      case PREFIX."forum_posts":
				$qs[0] = $_language->module['forum_posts'];
				break;
      case PREFIX."forum_ranks":
				$qs[0] = $_language->module['forum_ranks'];
				break;
      case PREFIX."forum_topics":
				$qs[0] = $_language->module['forum_topics'];
				break;
      case PREFIX."gallery":
				$qs[0] = $_language->module['gallery'];
				break;
      case PREFIX."gallery_groups":
				$qs[0] = $_language->module['gallery_groups'];
				break;
      case PREFIX."gallery_pictures":
				$qs[0] = $_language->module['gallery_pictures'];
				break;
      case PREFIX."games":
				$qs[0] = $_language->module['games'];
				break;
      case PREFIX."guestbook":
				$qs[0] = $_language->module['guestbook'];
				break;
      case PREFIX."links":
				$qs[0] = $_language->module['links'];
				break;
      case PREFIX."links_categorys":
				$qs[0] = $_language->module['links_categorys'];
				break;
      case PREFIX."linkus":
				$qs[0] = $_language->module['linkus'];
				break;
      case PREFIX."messenger":
				$qs[0] = $_language->module['messenger'];
				break;
      case PREFIX."news":
				$qs[0] = $_language->module['news'];
				break;
      case PREFIX."news_languages":
				$qs[0] = $_language->module['news_languages'];
				break;
      case PREFIX."news_rubrics":
				$qs[0] = $_language->module['news_rubrics'];
				break;
      case PREFIX."partners":
				$qs[0] = $_language->module['partners'];
				break;
      case PREFIX."poll":
				$qs[0] = $_language->module['poll'];
				break;
      case PREFIX."servers":
				$qs[0] = $_language->module['servers'];
				break;
      case PREFIX."shoutbox":
				$qs[0] = $_language->module['shoutbox'];
				break;
      case PREFIX."smileys":
				$qs[0] = $_language->module['smileys'];
				break;
      case PREFIX."sponsors":
				$qs[0] = $_language->module['sponsors'];
				break;
      case PREFIX."squads":
				$qs[0] = $_language->module['squads'];
				break;
      case PREFIX."static":
				$qs[0] = $_language->module['static'];
				break;
      case PREFIX."user":
				$qs[0] = $_language->module['user'];
				break;
      case PREFIX."user_gbook":
				$qs[0] = $_language->module['user_gbook'];
				break;
		}
		$qs[0] = str_replace(strtolower(PREFIX), "", ucwords($qs[0]));
		$count_array[] = array($qs[0], $data['Rows']);
}
?>

<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
  <tr>
    <td class="title" colspan="4"><b><?php echo $_language->module['database']; ?></b></td>
  </tr>
  <tr>
    <td width="25%" class="td1"><b><?php echo $_language->module['mysql_version']; ?></b></td>
    <td width="25%" class="td1"><?php echo mysql_get_server_info(); ?></td>
    <td width="25%" class="td1"><b><?php echo $_language->module['overhead']; ?></b></td>
    <td width="25%" class="td1"><?php echo $db_size_op; ?> Bytes
    <?php
    if($db_size_op != 0) {
    	echo'<a href="admincenter.php?site=database&amp;action=optimize&amp;back=page_statistic"><font color="red"><b>'.$_language->module['optimize'].'</b></font></a>';
    }
    ?></td>
  </tr>
  <tr>
    <td class="td2"><b><?php echo $_language->module['size']; ?></b></td>
    <td class="td2"><?php echo $db_size; ?> Bytes (<?php echo round($db_size / 1024 / 1024, 2); ?> MB)</td>
    <td class="td2"><b><?php echo $_language->module['tables']; ?></b></td>
    <td class="td2"><?php echo $count_tables; ?></td>
  </tr>
</table>
<br /><br />
<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
  <tr>
    <td class="title" colspan="4"><b><?php echo $_language->module['page_stats']; ?></b></td>
  </tr>
  <?php
  for($i = 0; $i < count($count_array); $i += 1) {
    if($i%4) { $td='td1'; }
    else { $td='td2'; }
  ?>
  <tr>
    <td width="25%" class="<?php echo $td; ?>"><b><?php echo $count_array[$i][0]; ?></b></td>
    <td width="25%" class="<?php echo $td; ?>"><?php echo $count_array[$i][1]; ?></td>
    <?php if(isset($count_array[$i + 1])) { ?>
    <td width="25%" class="<?php echo $td; ?>"><b><?php echo $count_array[$i + 1][0]; ?></b></td>
    <td width="25%" class="<?php echo $td; ?>"><?php echo $count_array[$i + 1][1]; ?></td>
    <?php 
		} 
  	else { ?>
    <td width="25" class="<?php echo $td; ?>"></td>
    <td width="25" class="<?php echo $td; ?>"></td>
    <?php } ?>
  </tr>
<?php
$i++;
}
?>
</table>