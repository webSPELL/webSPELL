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

$_language->readModule('faq');

eval ("\$title_faq = \"".gettemplate("title_faq")."\";");
echo $title_faq;

if(isset($_GET['action'])) $action = $_GET['action'];
else $action='';

if($action=="faqcat" and is_numeric($_GET['faqcatID'])) {
	if(ispageadmin($userID)) echo'<input type="button" onclick="MM_openBrWindow(\'admin/admincenter.php?site=faq\',\'News\',\'toolbar=yes,status=yes,scrollbars=yes,resizable=yes,width=800,height=600\')" value="'.$_language->module['admin_button'].'" class="btn btn-danger"><br><br>';

	$faqcatID = $_GET['faqcatID'];
	$get = safe_query("SELECT faqcatname FROM ".PREFIX."faq_categories WHERE faqcatID='".$faqcatID."'");
	$dc = mysqli_fetch_assoc($get);
	$faqcatname = $dc['faqcatname'];

	$faqcat=safe_query("SELECT question,faqID,sort FROM ".PREFIX."faq WHERE faqcatID='".$faqcatID."' ORDER BY sort");
	if(mysqli_num_rows($faqcat)) {

		eval ("\$faq_question_head = \"".gettemplate("faq_question_head")."\";");
		echo $faq_question_head;
		$i=1;
		while($ds=mysqli_fetch_array($faqcat)){
			if($i%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$i++;

			$sort=$ds['sort'];
			$question='<a href="index.php?site=faq&amp;action=faq&amp;faqID='.$ds['faqID'].'&amp;faqcatID='.$faqcatID.'" class="list-group-item">'.$ds['question'].'</a>';

			eval ("\$faq_question = \"".gettemplate("faq_question")."\";");
			echo $faq_question;
		}
		eval ("\$faq_foot = \"".gettemplate("faq_foot")."\";");
		echo $faq_foot;
	}
	else echo $_language->module['no_faq'];
}

elseif($action=="faq") {
	if(ispageadmin($userID)) echo'<p><input type="button" onclick="MM_openBrWindow(\'admin/admincenter.php?site=faq\',\'News\',\'toolbar=yes,status=yes,scrollbars=yes,resizable=yes,width=800,height=600\')" value="'.$_language->module['admin_button'].'" class="btn btn-danger"></p>';

	$faqcatID = intval($_GET['faqcatID']);
	$get = safe_query("SELECT faqcatname FROM ".PREFIX."faq_categories WHERE faqcatID='".$faqcatID."'");
	$dc = mysqli_fetch_assoc($get);
	$faqcatname = $dc['faqcatname'];
	$faqID = intval($_GET['faqID']);

	$faq=safe_query("SELECT faqcatID,date,question,answer FROM ".PREFIX."faq WHERE faqID='$faqID'");
	if(mysqli_num_rows($faq)) {
		$ds=mysqli_fetch_array($faq);
		$backlink='<a href="index.php?site=faq&amp;action=faqcat&amp;faqcatID='.$faqcatID.'" class="titlelink">'.$faqcatname.'</a>';
		$question=$ds['question'];
		if(mb_strlen($question) > 40) {
			if($question{39} == " ") $question = mb_substr($question, 0, 38)."...";
			else $question = mb_substr($question, 0, 40)."...";
		}

		eval ("\$faq_answer_head = \"".gettemplate("faq_answer_head")."\";");
		echo $faq_answer_head;

		$bg1=BG_1;
		$date=getformatdate($ds['date']);
		$answer=htmloutput($ds['answer']);


		eval ("\$faq_answer = \"".gettemplate("faq_answer")."\";");
		echo $faq_answer;
	}
	else echo $_language->module['no_faq'];
}

else {
	if(ispageadmin($userID)) echo'<p><input type="button" onclick="MM_openBrWindow(\'admin/admincenter.php?site=faq\',\'News\',\'toolbar=yes,status=yes,scrollbars=yes,resizable=yes,width=800,height=600\')" value="'.$_language->module['admin_button'].'" class="btn btn-danger"></p>';

	$faqcats=safe_query("SELECT * FROM ".PREFIX."faq_categories ORDER BY sort");
	$anzcats=mysqli_num_rows($faqcats);
	if($anzcats) {

		eval ("\$faq_category_head = \"".gettemplate("faq_category_head")."\";");
		echo $faq_category_head;
		$i=1;
		while($ds=mysqli_fetch_array($faqcats)) {
			$anzfaqs=mysqli_num_rows(safe_query("SELECT faqID FROM ".PREFIX."faq WHERE faqcatID='".$ds['faqcatID']."'"));
			if($i%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
			$faqcatname='<a href="index.php?site=faq&amp;action=faqcat&amp;faqcatID='.$ds['faqcatID'].'">'.$ds['faqcatname'].'</a>';
			$description=htmloutput($ds['description']);

			eval ("\$faq_category = \"".gettemplate("faq_category")."\";");
			echo $faq_category;
			$i++;
		}
	}
	else echo $_language->module['no_categories'];
}

?>
