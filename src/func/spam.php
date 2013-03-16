<?php
function validateSpam($message){
	global $spamapihost;
	$postdata = array();
	$postdata["validate"] = json_encode(array("message"=>$message));
	return post_request($spamapihost,$postdata);
}

function learnSpamfilter($message, $type){
	global $spamapikey,$spamapihost;
	$postdata = array();
}

function post_request($url, $data){
	if(function_exists("curl_init")){
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
	elseif(class_exists("HttpRequest")){
		$r = new HttpRequest($url, HttpRequest::METH_POST);
		$r->addPostFields($data);
		try {
			return $r->send()->getBody();
		} catch (HttpException $ex) {
			return "";
		}
	}
	elseif(ini_get("allow_url_fopen") == "on"){
		$params = array('http'=>array('method'=>'post','content'=>http_build_query($data)));
		$context= stream_context_create($params);
		return file_get_contents($url, false, $context);
	}
	else{
		return "";
	}
}
?>