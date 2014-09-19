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

function unhtmlspecialchars($input) {
	$input = preg_replace("/&gt;/i", ">", $input);
	$input = preg_replace("/&lt;/i", "<", $input);
	$input = preg_replace("/&quot;/i", "\"", $input);
	$input = preg_replace("/&amp;/i", "&", $input);

	return $input;
}
function replace_smileys($text, $calledfrom = 'root'){
	if($calledfrom == 'admin'){
		$prefix = '.';
		$prefix2 = '../';
	}
	else{
		$prefix = '';
		$prefix2 = '';
	}
	
	$filepath = $prefix."./images/smileys/";
	unset($files);
	if ($dh = opendir($filepath)) {
		while($file = readdir($dh)) {
			if (preg_match("/\.gif/si",$file)) $files[] = $file;
		}
	}

	$replacements_1 = Array();	
	$replacements_2 = Array();	

	foreach($files as $file) {
		$smiley = explode(".", $file);
		$replacements_1[] = ':'.quotemeta($smiley[0]).':';
		$replacements_2[] = '[SMILE=smile]'.$prefix2.'images/smileys/'.$file.'[/SMILE]';
	}

	$ergebnis = safe_query("SELECT * FROM `".PREFIX."smileys`");
	while($ds = mysql_fetch_array($ergebnis)) {
		$replacements_1[] = $ds['pattern'];
		$replacements_2[] = '[SMILE='.$ds['alt'].']'.$prefix2.'images/smileys/'.$ds['name'].'[/SMILE]';
	}
	
	$text = strtr($text, array_combine($replacements_1, $replacements_2));
	
	return $text;
}
function smileys($text, $specialchars=0, $calledfrom = 'root') {

	if($specialchars) $text=unhtmlspecialchars($text);
	$splits = preg_split("/(\[[\/]{0,1}code\])/si",$text,-1,PREG_SPLIT_DELIM_CAPTURE);
	$anz = count($splits);
	for($i=0;$i<$anz;$i++){
		$opentags = 0;
		$closetags = 0;
		$match = false;
		if(strtolower($splits[$i]) == "[code]"){
			$opentags++;
			for($z=($i+1);$z<$anz;$z++){
				if(strtolower($splits[$z]) == "[code]") $opentags++;
				if(strtolower($splits[$z]) == "[/code]") $closetags++;
				if($closetags == $opentags){
					$match = true;
					break;
				}
			}
		}
		if($match == false){
			$splits[$i] = replace_smileys($splits[$i], $calledfrom);
		}
		else {
			$i = $z;			
		}		
	}
	$text = implode("",$splits);
	if($specialchars) $text=htmlspecialchars($text);
	return $text;
}

function htmlnl($text){
	preg_match_all('/<(table|form|li|ul|ol|tr|td|dl|dt|dd|dir|menu|th|thead|caption|colgroup|col|tbody|tfoot|div|span*)[^>]*>(.*?)<\/\1>/si',$text,$matches,PREG_SET_ORDER);
	foreach($matches as $match){
		if(stristr($match[0],'class="quote"') === false && 
			 stristr($match[0],'class="code"') === false && 
			 stristr($match[0],'align=') === false &&
			 stristr($match[0],'size=') === false && 
			 stristr($match[0],'color=') === false){
			$new_str = str_replace(array("\r\n", "\n", "\r"),array("", "", ""),$match[0]);
			$text = str_replace($match[0],$new_str,$text);
		}
	}
	return $text;
}

function fixJavaEvents($string){
	return str_replace(array('onabort=', 'onblur=', 'onchange=', 'onclick=', 'ondblclick=', 'onerror=', 'onfocus=', 'onkeydown=', 'onkeypress=', 'onkeyup=', 'onload=', 'onmousedown=', 'onmousemove=', 'onmouseout=', 'onmouseover=', 'onmouseup=', 'onreset=', 'onresize=', 'onselect=', 'onsubmit=', 'onunload=', ' '),'',$string);
}

function flags($text,$calledfrom = 'root') {
  global $_language;
	$_language->read_module('bbcode', true);
	
	if($calledfrom == 'admin'){
		$prefix = '../';
	}
	else{
		$prefix = '';
	}
	$ergebnis = safe_query("SELECT * FROM `".PREFIX."countries`");
	while($ds = mysql_fetch_array($ergebnis)) {
		$text = str_ireplace ("[flag]".$ds['short']."[/flag]", '<img src="'.$prefix.'images/flags/'.$ds['short'].'.gif" width="18" height="12" border="0" alt="'.$ds['country'].'" />', $text);
	}

	$text = str_ireplace ("[flag][/flag]", '<img src="'.$prefix.'images/flags/na.gif" width="18" height="12" border="0" alt="'.$_language->module['na'].'" />', $text);
	$text = str_ireplace ("[flag]", '', $text);
	$text = str_ireplace ("[/flag]", '', $text);

	return $text;
}

//replace [code]-tags

function codereplace($content) {
	global $_language;
	$_language->read_module('bbcode', true);

	global $picsize_l;
	
	$border=BORDER;
	$bg1=BG_1;
	$splits = preg_split("/(\[[\/]{0,1}code\])/si",$content,-1,PREG_SPLIT_DELIM_CAPTURE);
	$anz = count($splits);
	for($i=0;$i<$anz;$i++){
		$opentags = 0;
		$closetags = 0;
		$match = false;
		if(strtolower($splits[$i]) == "[code]"){
			$opentags++;
			for($z=($i+1);$z<$anz;$z++){
				if(strtolower($splits[$z]) == "[code]") $opentags++;
				elseif(strtolower($splits[$z]) == "[/code]") $closetags++;
				if($closetags == $opentags){
					$match = true;
					break;
				}
			}
			if($match){
				$splits[$i] = '<div style="width:'.$picsize_l.'px;height:100%;overflow:auto;background-color:'.$bg1.';border: 1px '.$border.' solid;" class="code"><b>'.$_language->module['code'].':</b><hr /><div class="codeinner">';

				/* concat pieces until arriving closing tag ($z) and save to $i+1 */
				for($x=($i+2); $x<$z;$x++){
					$splits[($i+1)] .= $splits[$x];
					unset($splits[$x]);
				}

				$splits[($i+1)] = insideCode($splits[($i+1)]);
				$splits[$z] = '</div></div>';
				$i=$z;		
			}
		}
	}
	$content = implode($splits);
	return $content;
}

//replace inside [code]-tags
function insideCode($content){
	
	global $userID;
	$code_entities_match = array(
		'#"#',
		'#<#',
		'#>#',
		'#:#',
		'#\[#',
		'#\]#',
		'#\(#',
		'#\)#',
		'#\{#',
		'#\}#',
		'#\t#',
		'#\040#'
	);
	$code_entities_replace = array(
		'&quot;',
		'&lt;',
		'&gt;',
		'&#58;',
		'&#91;',
		'&#93;',
		'&#40;',
		'&#41;',
		'&#123;',
		'&#125;',
		'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
		'&nbsp;'
	);
	
	$content = preg_replace($code_entities_match, $code_entities_replace, $content);
	
	// add line number
	$splits = preg_split("#\\n#", $content, -1, PREG_SPLIT_NO_EMPTY);
	
	$i = 0;
	$codelines='';
	$codecontent='';
	foreach($splits as $res) {
		if($i > 0 OR trim($res) != "") {
			$codelines.='<div class="codeline'.($i%2).'">'.($i+1).'.</div>';
			$codecontent.='<div class="codeline'.($i%2).'">'.$res.'</div>';
			$i++;
		}
	}
	$content='<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="20"><div style="text-align: right;">'.$codelines.'</div></td><td valign="top">'.$codecontent.'</td></tr></table>';
	
	return $content;
}

//replace [img]-tags

function imgreplace($content) {
	global $_language;
	$_language->read_module('bbcode', true);

	global $picsize_l;
	global $picsize_h;
	global $autoresize;

	if($autoresize>0) {
		preg_match_all("#(\[img\])(.*?)(png|gif|jpeg|jpg)(\[\/img\])#i", $content, $imgtags, PREG_SET_ORDER);
		$i=0;
		foreach($imgtags as $teil) {
			$teil[2] .= $teil[3];
			$i++;
			if($autoresize == 1) {
				$picinfo = getimagesize($teil[2]);
				switch($picinfo[2]) {
					case 1: $format = "gif"; break;
					case 2: $format = "jpeg"; break;
					case 3: $format = "png"; break;
				}
				if(!$picsize_l) $size_l = "9999"; else $size_l=$picsize_l;
				if(!$picsize_h) $size_h = "9999"; else $size_h=$picsize_h;
				if($picinfo[0] > $size_l OR $picinfo[1] > $size_h) 
				$content = str_ireplace('[img]'.$teil[2].'[/img]', '[url='.$teil[2].']<img src="'.fixJavaEvents($teil[2]).'" border="0" width="'.$picsize_l.'" alt="'.$teil[2].'" /><br />([i]'.$_language->module['auto_resize'].': '.$picinfo[1].'x'.$picinfo[0].'px, '.$format.'[/i])[/url]', $content);
				elseif($picinfo[0] > (2*$size_l) OR $picinfo[1] > (2*$size_h)) $content = str_ireplace('[img]'.$teil[2].'[/img]', '[url='.$teil[2].'][b]'.$_language->module['large_picture'].'[/b]<br />('.$picinfo[1].'x'.$picinfo[0].'px, '.$format.')[/url]', $content);
				else $content = preg_replace('#\[img\]'.preg_quote($teil[2],"#").'\[/img\]#si', '<img src="'.fixJavaEvents($teil[2]).'" border="0" alt="'.$teil[2].'" />', $content, 1);
			}
			else {
				$n = str_replace('.', '', microtime(1)).'_'.$i;
				$n = str_replace(' ', '', $n);
				$content = preg_replace('#\[img\]'.preg_quote($teil[2],"#").'\[/img\]#si', '<img src="'.fixJavaEvents($teil[2]).'" id="ws_image_'.$n.'" border="0" onload="checkSize(\''.$n.'\', '.$picsize_l.', '.$picsize_h.')" alt="'.fixJavaEvents($teil[2]).'" style="max-width: '.($picsize_l+1).'px; max-height: '.($picsize_h+1).'px;" /><div id="ws_imagediv_'.$n.'" style="display:none;">[url='.fixJavaEvents($teil[2]).'][i]('.$_language->module['auto_resize'].': '.$_language->module['show_original'].')[/i][/url]</div>', $content, 1);
			}
		}
	}
	else $content = preg_replace("#\[img\](.*?)(png|gif|jpeg|jpg)\[/img\]#sie", "'<img src=\"'.fixJavaEvents('\\1\\2').'\" border=\"0\" alt=\"'.fixJavaEvents('\\1\\2').'\" />'", $content);

	return $content;
}

//replace [quote]-tags

function quotereplace($content) {
  
	global $_language, $picsize_l, $picsize_h;
	$_language->read_module('bbcode', true);
	$border=BORDER;
	$bg1=BG_1;

	$content = str_ireplace('[quote]', '[quote]', $content);
	$content = str_ireplace('[/quote]', '[/quote]', $content);
	$wrote = $_language->module['wrote'];
	$content = preg_replace("#\[quote=(.*?)\]#si", "[quote][b]\\1 ".$wrote.":[/b][br][hr]",$content);

	//prepare: how often start- and end-tag occurrs
	$starttags = substr_count($content, '[quote]');
	$endtags = substr_count($content, '[/quote]');

	$overflow=abs($starttags-$endtags);

	for($i=0;$i<$overflow;$i++) {
		if($starttags>$endtags) $content=$content.'[/quote]';
		elseif($endtags>$starttags) $content='[quote]'.$content;
	}

	$content = preg_replace("#\[quote\]#s", '<div style="width:'.$picsize_l.'px;max-height:'.$picsize_h.'px;overflow:auto;background-color:'.$bg1.';border: 1px '.$border.' solid;" class="quote">', $content, 10);
	$content = preg_replace("#\[/quote\]#s", '</div>', $content, 10);

	//remove overflowed quote-tags

	$content = str_replace('[quote]','',$content);
	$content = str_replace('[/quote]','',$content);

	return $content;

}

function cut_middle($str, $max = 50 ){
 	$strlen = mb_strlen($str);
	if( $strlen>$max ){
		$part1 = mb_substr($str,0,$strlen/2);
		$part2 = mb_substr($str,$strlen/2);
		$part1 = mb_substr($part1,0,($max/2)-3)."...";
		$part2 = mb_substr($part2,-($max/2));
		$str = $part1.$part2;
	}
	return $str;
}

function urlreplace($content){	
 	$starttags = substr_count(strtolower($content), strtolower('[url'));
	$endtags = substr_count(strtolower($content), strtolower('[/url]'));
	$overflow=abs($starttags-$endtags);
	for($i=0;$i<$overflow;$i++) {
		if($starttags>$endtags) $content=$content.'[/url]';
		elseif($endtags>$starttags) $content='[url]'.$content;
	}
	$content = preg_replace("#\[url\](.*?)\[/url\]#i","[url=\\1]\\1[/url]",$content);
	preg_match_all("/\[url=(\[(.*?)\])\]/si",$content,$erg,PREG_SET_ORDER);
	foreach($erg as $match){
		preg_match("/\[(.*?)\](.*?)\[(.*?)\]/si",$match[1],$new_erg);
		$match_rep = str_replace($match[1],$new_erg[2],$match[0]);
		$content = str_replace($match[0],$match_rep,$content);
	}
	$content = preg_replace("#\[url=(.*?)\]#ie","'<a href=\"'.fixJavaEvents('\\1').'\" target=\"_blank\">'",$content);
	$content = preg_replace("#\<a href='www(.*?)' target='_blank'>#i","<a href='http://www\\1' target='_blank'>",$content);
	$content = str_ireplace("[/url]","</a>",$content);
	return $content;
}

function linkreplace($link){
	if( ord($link[1])==39 || ord($link[1])==62 ) return $link[0];
	else{
		$backup = "";
		$backup_end = "";
		if(mb_substr($link[0],-1,1) == "]"){
			$backup = mb_substr($link[0],0,1);
			$link[0] = mb_substr($link[0],1);
			$link[0] = mb_substr($link[0],0,mb_strrpos($link[0],"["));
			$backup_end = mb_substr($link[3],mb_strrpos($link[3],"["));
			$link[3] = mb_substr($link[3],0,mb_strrpos($link[3],"["));
		}
		$check = preg_match("%(http://|https://|ftp://|mailto:|news:|www\.)([a-zA-Z0-9-\.]{3,50})(\.[a-z]{2,4})%si",$link[0]);
	 	if($check){
			$http = $link[2];
		 	if(mb_substr($http,0,4)=="www."){
				$http = "http://".$http;
			}
			$link = str_replace(trim($link[0]),'<a href="'.$http.$link[3].'" target="_blank" rel="nofollow">'.$link[2].$link[3].'</a>',$link[0]);
			return $backup.$link.$backup_end;
		}
		return $backup.$link[0].$backup_end;
	}
}

//insert member links
function insertlinks($content,$calledfrom = 'root') {
	global $insertlinks;
	if($calledfrom == 'admin'){
		$prefix = '../';
	}
	else{
		$prefix = '';
	}	
	
	if($insertlinks==1) {
		$ergebnis = safe_query("SELECT us.userID, us.nickname, us.country FROM ".PREFIX."squads_members AS sq, ".PREFIX."user AS us WHERE sq.userID=us.userID GROUP BY us.userID");
		while($ds = mysql_fetch_array($ergebnis)) {
			$content = str_replace($ds['nickname'].' ', '[flag]'.$ds['country'].'[/flag] <a href="'.$prefix.'index.php?site=profile&amp;id='.$ds['userID'].'">'.$ds['nickname'].'</a>&nbsp;', $content);
		}
		return $content;
	} else {
		return $content;
	}
}

function cut_urls($link){
 	$new_str = $link[1];
 	if(!stristr($link[1],"<img") && !stristr($link[1],"[SMILE")){
		$new_str = cut_middle($link[1]);
	}
	$link[0] = ( stristr($link[0],"javascript:") ) ? str_ireplace("javascript:","#killed",$link[0]) : $link[0];
	return str_replace(">".$link[1],">".$new_str,$link[0]);
}

function replacement($content, $bbcode=true) {
	$pagebg=PAGEBG;
	$border=BORDER;
	$bg1=BG_1;
	$bghead=BGHEAD;
	$bgcat=BGCAT;

	if($bbcode) {
		$content = codereplace($content);
		$content = imgreplace($content);
		$content = quotereplace($content);
		$content = urlreplace($content);
		$content = preg_replace_callback("#(^|<[^\"=]{1}>|\s|\[b|i|u\]][^<a.*>])(http://|https://|ftp://|mailto:|news:|www.)([^\s<>|$]+)#si","linkreplace",$content);
		$content = preg_replace("#\[email\](.*?)\[/email\]#sie", "'<a href=\"mailto:'.mail_protect(fixJavaEvents('\\1')).'\">'.fixJavaEvents('\\1').'</a>'", $content);
		$content = preg_replace("#\[email=(.*?)\](.*?)\[/email\]#sie", "'<a href=\"mailto:'.mail_protect(fixJavaEvents('\\1')).'\">\\2</a>'", $content);
		$content = preg_replace_callback("#<a\b[^>]*>(.*?)</a>#si","cut_urls",$content);
		while(preg_match("#\[size=(.*?)\](.*?)\[/size\]#si", $content)){
		  $content = preg_replace("#\[size=(.*?)\](.*?)\[/size\]#si", "<font size=\"\\1\">\\2</font>", $content);
		}
		while(preg_match("#\[color=(.*?)\](.*?)\[/color\]#si", $content)){  
		  $content = preg_replace("#\[color=(.*?)\](.*?)\[/color\]#si", "<font color=\"\\1\">\\2</font>", $content);
		}
		while(preg_match("#\[font=(.*?)\](.*?)\[/font\]#si", $content)){
		  $content = preg_replace("#\[font=(.*?)\](.*?)\[/font\]#si", "<font face=\"\\1\">\\2</font>", $content);
		}
		while(preg_match("#\[align=(.*?)\](.*?)\[/align\]#si", $content)){
		  $content = preg_replace("#\[align=(.*?)\](.*?)\[/align\]#si", "<div align=\"\\1\">\\2</div>", $content);
		}
		$content = preg_replace("#\[b\](.*?)\[/b\]#si", "<b>\\1</b>",$content);
		$content = preg_replace("#\[i\](.*?)\[/i\]#si", "<i>\\1</i>",$content);
		$content = preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>",$content);
		$content = preg_replace("#\[s\](.*?)\[/s\]#si", "<s>\\1</s>",$content);
		$content = preg_replace("#\[list\][\s]{0,}(.*?)\[/list\]#si", "<ul class='list'>\\1</ul>", $content);
		$content = preg_replace("#\[list=1\][\s]{0,}(.*?)\[/list=1\]#si", "<ol class='list_num'>\\1</ol>", $content);
		$content = preg_replace("#\[list=a\][\s]{0,}(.*?)\[/list=a\]#si", "<ol type=\"a\" class='list_alpha'>\\1</ol>", $content);
		$content = preg_replace("#\[\*\](.*?)\[/\*\](\s){0,}#si", "<li>\\1</li>", $content);
		$content = preg_replace("#\[br]#si", "<br />", $content);
		$content = preg_replace("#\[hr]#si", "<hr />", $content);
		$content = preg_replace("#\[center]#si", "<center>", $content);
		$content = preg_replace("#\[/center]#si", "</center>", $content);
	}
	$content = preg_replace("#\[SMILE=(.*?)\](.*?)\[/SMILE\]#si", '<img src="\\2" alt="\\1" border="0" />', $content);

	return $content;
}

function toggle($content, $id) {
	global $_language;
	$_language->read_module('bbcode', true);
	$replace1='<table width="100%">
    <tr>
      <td><a href="javascript:Toggle(\''.$id.'_%d\')"><img src="images/icons/expand.gif" border="0" id="ToggleImg_'.$id.'_%d" alt="'.$_language->module['read_more'].'" title="'.$_language->module['read_more'].'" /> %s</a></td>
    </tr>
    <tr>
      <td style="padding-left: 16px;"><div id="ToggleRow_'.$id.'_%d" style="display:none">';
		$replace2='</div></td>
    </tr>
  </table>';

	$n=0;
	while(($pos = mb_strpos(strtolower($content), "[toggle=")) !== false) {
		$start = mb_substr($content, 0, $pos);
		$end = mb_substr($content, $pos);
		
		$toggle_name_end = mb_strpos($end, "]");
		
		if(($toggle_close_tag = mb_strpos(strtolower($end), "[/toggle]")) === false) {
			$content = $start.mb_substr($end, $toggle_name_end + 1);
		}
		else {
			$toggle_name = mb_substr($end, 8, $toggle_name_end - 8);
			$middle = str_replace("%d", $n, str_replace("%s", $toggle_name, $replace1));
			$toggle_content = mb_substr($end, $toggle_name_end + 1, $toggle_close_tag - $toggle_name_end - 1);
			$end = mb_substr($content, $pos + $toggle_close_tag + 9);
			$content = $start.$middle.$toggle_content.$replace2.$end;
			$n++;
		}
	}
	
	$content = str_ireplace("[/toggle]", "", $content);

	return $content;
}
?>
