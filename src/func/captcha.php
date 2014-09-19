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
class Captcha {

	var $hash;
	var $length = 5;
	var $type;
	var $noise = 100;
	var $linenoise = 10;
	var $valide_time = 1440; /* captcha or transaction is valide for x minutes */
	var $math;
	var $math_max = 30;
	var $bgcol = array("r"=>255,"g"=>255,"b"=>255);
	var $fontcol = array("r"=>0,"g"=>0,"b"=>0);
	
	/* constructor: set captcha type */
	function hex2rgb($col){
		$col = str_replace("#","",$col);
		$int = hexdec($col);
		$return = array(
            "r" => 0xFF & $int >> 0x10,
            "g" => 0xFF & ($int >> 0x8),
            "b" => 0xFF & $int
            );
        return $return;
	}
	function captcha() {
		$ds = mysql_fetch_assoc(safe_query("SELECT captcha_math, captcha_bgcol, captcha_fontcol, captcha_type, captcha_noise, captcha_linenoise FROM ".PREFIX."settings"));
		if(mb_strlen($ds['captcha_bgcol']) == 7)
			$this->bgcol = $this->hex2rgb($ds['captcha_bgcol']);
		
		if(mb_strlen($ds['captcha_fontcol']) == 7)
			$this->fontcol = $this->hex2rgb($ds['captcha_fontcol']);

		if($ds['captcha_math'] == 1) $this->math = 1;
		elseif($ds['captcha_math'] == 2) $this->math = rand(0,1);
		else $this->math = 0;
		
		if($ds['captcha_type'] == 1) $this->type='g';
		elseif(function_exists('imagecreatetruecolor') && ($ds['captcha_type'] == 2)) $this->type='g';
		else $this->type='t';
		
		$this->noise = $ds['captcha_noise'];
		$this->linenoise = $ds['captcha_linenoise'];
		
		$this->clear_oldcaptcha();
	}

	/* create captcha image/string and hash */
	function create_captcha() {
		global $_language;
		$_language->read_module('captcha', true);
		global $new_chmod;
		$this->hash = md5(time().rand(0, 10000));
		$captchastring='';

		if($this->type=='g') {

			// initial captcha image
			if($this->math)$this->length = 6;
			$imgziel = imagecreatetruecolor(($this->length*15)+10, 25);
			$bgcolor = ImageColorAllocate($imgziel, $this->bgcol['r'], $this->bgcol['g'], $this->bgcol['b']);
			$fontcolor = imagecolorallocate($imgziel, $this->fontcol['r'], $this->fontcol['g'], $this->fontcol['b']);
			$xziel = imagesx($imgziel); // get image width
			$yziel = imagesy($imgziel); // get image height
			ImageFilledRectangle($imgziel, 0, 0, $xziel, $yziel, $bgcolor);

			// add line and point noise
			for($i=0;$i<$this->linenoise;$i++) {
				imageline($imgziel, rand(0,$xziel), rand(0,$yziel), rand(0,$xziel), rand(0,$yziel), ImageColorAllocate($imgziel, rand(0, 255), rand(0, 255), rand(0, 255)));
			}

			for($i=0;$i<$this->noise;$i++) {
				imagesetpixel($imgziel, rand(0,$xziel), rand(0,$yziel), $fontcolor);
			}

			/* create captcha string */
			
			// math captcha
			if($this->math == 1){
				$first = rand(1,$this->math_max);
				$captchastring = $first;
				while(mb_strlen($first)<mb_strlen($this->math_max)) {
					$first=' '.$first;
				}
				$captchastring_show = (string) $first;
				if(rand(0,1)){
					$captchastring_show .= "+";
					$next = rand(1,$this->math_max);
					$captchastring += $next;
					while(mb_strlen($next)<mb_strlen($this->math_max)) {
						$next=' '.$next;
					}
					$captchastring_show .= $next;
				}
				else{
					$captchastring_show .= "-";
					$next = rand(1,$first-1);
					$captchastring -= $next;
					while(mb_strlen($next)<mb_strlen($this->math_max)) {
						$next=' '.$next;
					}
					$captchastring_show .= $next;
				}
				$captchastring_show .= "=";
				$lenght = mb_strlen($captchastring_show);
				for($i=0;$i<$lenght;$i++){
					$char = mb_substr($captchastring_show,$i,1);
					if($char == "-" || $char == "+" || $char == "="){
						imagesetthickness($imgziel,2);
						if($char == "-"){
							imageline($imgziel,$i*15,13,$i*15+8,13,$fontcolor);
						}
						if($char == "+"){
							imageline($imgziel,$i*15,13,$i*15+9,13,$fontcolor);
							imageline($imgziel,($i*15)+5,8,($i*15)+5,18,$fontcolor);
						}
						if($char == "="){
							imageline($imgziel,$i*15,11,$i*15+9,11,$fontcolor);
							imageline($imgziel,$i*15,15,$i*15+9,15,$fontcolor);
						}
					}
					else{
						$font = rand(2,5);
						imagestring($imgziel, $font, $i*15+5, 5, $char, $fontcolor);
					}
				}
			}
			
			// numeric captcha
			
			else{
				for($i=0;$i<$this->length;$i++) {
					$int=rand(0,9);
					$captchastring.=$int;
					imagestring($imgziel, rand(2,5), $i*15+5, 5, $int, $fontcolor);
				}
			}

			imageJPEG($imgziel, 'tmp/'.$this->hash.'.jpg');
			@chmod('tmp/'.$this->hash.'.jpg', $new_chmod);
			$captcha = '<img src="tmp/'.$this->hash.'.jpg" border="0" alt="'.$_language->module['security_code'].'" />';

		} elseif($this->type=='t') {
			$captcha = '';
			for($i=0;$i<$this->length;$i++) {
				$captcha .= rand(0,9);
			}
			$captchastring=$captcha;
		}
		safe_query("INSERT INTO `".PREFIX."captcha` (`hash`, `captcha`, `deltime`) VALUES ('".$this->hash."', '".$captchastring."', '".(time()+($this->valide_time*60))."');");
		return $captcha;
	}

	/* create transaction hash for formulars */
	function create_transaction() {
		
		$this->hash = md5(time().rand(0, 10000));
		safe_query("INSERT INTO `".PREFIX."captcha` (`hash`, `captcha`, `deltime`) VALUES ('".$this->hash."', '0', '".(time()+($this->valide_time*60))."');");
		return true;

	}

	/* print created hash */
	function get_hash() {

		return $this->hash;

	}

	/* check if input fits captcha */
	function check_captcha($input, $hash) {

		if(mysql_num_rows(safe_query("SELECT hash FROM `".PREFIX."captcha` WHERE captcha='".$input."' AND hash='".$hash."'"))) {
			safe_query("DELETE FROM `".PREFIX."captcha` WHERE captcha='".$input."' AND hash='".$hash."'");
			$file='tmp/'.$hash.'.jpg';
			if(file_exists($file)) unlink($file);
			return true;
		}
		else return false;

	}

	/* remove old captcha files */
	function clear_oldcaptcha() {
	 	$time = time();
		$ergebnis=safe_query("SELECT hash FROM `".PREFIX."captcha` WHERE deltime<".$time);
		while($ds=mysql_fetch_array($ergebnis)) {
			$file='tmp/'.$ds['hash'].'.jpg';
			if(file_exists($file)) unlink($file);
		}
		safe_query("DELETE FROM `".PREFIX."captcha` WHERE deltime<".$time);
	}
}
?>
