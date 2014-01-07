<?php

class ModRewrite {

	private $translation = array();

	private $cache = null;
	public function __construct(){
		$this->translation['integer'] = array('replace'=>'([0-9]+)','rebuild'=>'([0-9]+?)');
		$this->translation['string'] = array('replace'=>'(\w*?)','rebuild'=>'(\w*?)');
		$this->translation['everything'] = array('replace'=>'([^\'\\"]*?)','rebuild'=>'([^\'\\"]*?)');
	}

	public function getTypes(){
		return array_keys($this->translation);
	}

	public function enable(){
		$this->buildCache();
		ob_start(array($this,'rewriteBody'));

		/*
		header_register_callback only works after php 5.5.7 and 5.4.23 because of 
		https://bugs.php.net/bug.php?id=66375
		fixed in
		https://github.com/php/php-src/commit/3c3ff434329d2f505b00a79bacfdef95ca96f0d2
		*/

		$fixedHeader = false;
		if(PHP_MAJOR_VERSION == 5){
			if(PHP_MINOR_VERSION == 4){
				if(PHP_RELEASE_VERSION > 23){
					$fixedHeader = true;
				}
			}
			elseif(PHP_MINOR_VERSION == 5){
				if(PHP_RELEASE_VERSION > 7){
					$fixedHeader = true;
				}
			}
		}

		if($fixedHeader){
			header_register_callback(array($this,'rewriteHeaders'));
		}
		else{
			register_shutdown_function(array($this,'rewriteHeaders'));
		}
	}

	private function buildCache(){
		$this->cache = array();
		$get = safe_query("SELECT * FROM ".PREFIX."modrewrite ORDER BY link DESC");
		while($ds = mysqli_fetch_assoc($get)){
			$this->cache[] = $ds;
		}
	}

	public function getRewriteBase(){
		$path = str_replace(realpath($_SERVER['DOCUMENT_ROOT']),'',realpath(__DIR__.'/../'));
		$path = str_replace('\\','/',$path);
		if($path[0] != '/'){
			$path = '/'.$path;
		}
		if($path[strlen($path)-1] != '/'){
			$path = $path.'/';
		}
		return $path;
	}

	public function rewriteHeaders(){
		$headers = headers_list();
		foreach($headers as $header){
			if(stristr($header,"Location")){
				header($this->rewrite($header,true),true);
			}
		}
	}

	public function rewriteBody($content, $phase){
		return $this->rewrite($content,false);
	}

	private function rewrite($content, $headers = false){
		$start_time = microtime(true);
		foreach($this->cache as $ds){
			$regex = $ds['replace_regex'];
			$replace = $ds['replace_result'];
			if($headers == true){
				$content = preg_replace("/()()Location:".$regex."/si",'Location: '.$replace,$content);
			}
			else{
				$content = preg_replace("/(href|action)=(['\"])".$regex."[\"']/si",'$1=$2'.$replace.'$2',$content);
			}
		}
		$needed = microtime(true)-$start_time;
		header('X-Rewrite-Time: '.$needed);
		return $content;
	}

	public function buildReplace($regex,$replace,$fields = array()){
		$regex = str_replace(array('.','?','&','/'),array('\.','\?','[&|&amp;]*','\/'),$regex);
		if(count($fields)){
			$i=3;
			foreach($fields as $key => $field){
				$regex = str_replace("{".$key."}",$this->translation[$field]['replace'],$regex);
				$replace = str_replace("{".$key."}",'$'.$i,$replace);
				$i++;
			}
		}
		return array($regex,$replace);
	}

	public function buildRebuild($regex,$replace,$fields = array()){
		$i=1;
		$regex = str_replace(array('.','?','/'),array('\.','\?','\/'),$regex);
		if(count($fields)){
			preg_match_all("/{(\w*)}/si",$regex,$matches,PREG_SET_ORDER);
			foreach($matches as $field){
				$regex = str_replace("{".$field[1]."}",$this->translation[$fields[$field[1]]]['rebuild'],$regex);
				$replace = str_replace("{".$field[1]."}",'$'.$i,$replace);
				$i++;
			}
		}
		return array($regex,$replace);
	}
}
?>