<?php
function validateSpam($message){
	global $spamapihost;
	$postdata = array();
	$postdata["validate"] = json_encode(array("message"=>$message));
	return post_request($spamapihost,$postdata);
}

define("Spamfilter_HAM","ham");
define("Spamfilter_SPAM","spam");

function learnSpamfilter($message, $type){
	global $spamapikey,$spamapihost;
	$postdata = array();
	$postdata["apikey"] = $spamapikey;
	$postdata["learn"] = json_encode(array("message"=>$message,"mode"=>$type));
	return post_request($spamapihost,$postdata);
}

function logSpamError($message){
	safe_query("INSERT INTO ".PREFIX."api_log (`message`,`date`) VALUES ('".addslashes($message)."','".time()."')");
}
function post_request($url, $data){
	if(function_exists("curl_init")){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CAINFO,"src/ca.pem");

		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
	elseif(include("HTTP/Request2.php") && class_exists("HTTP_Request2")){
		$request = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);
		$request->setConfig(array("ssl_cafile"=>"src/ca.pem","ssl_verify_peer"=>false));
		$url = $request->getUrl();
		$url->setQueryVariables($data);
		return $request->send()->getBody();
	}
	elseif(class_exists("HttpRequest")){
		$r = new HttpRequest($url, HttpRequest::METH_POST);
		$r->addPostFields($data);
		try {
			return $r->getBody();
		} catch (Exception $ex) {
			return "";
		}
	}
	elseif(ini_get("allow_url_fopen")){
		$build_data = http_build_query($data);
		$params = array('http'=>array(
								'method'=>'POST',
								'header'=>"Content-type: application/x-www-form-urlencoded",
								'content'=>$build_data
								)
						);
		$context= stream_context_create($params);
		$con= file_get_contents($url, false, $context);
		return $con;
	}
	else{
		return "No Method available to query Api. Enable Curl or HttpRequest2 or allow_url_fopen.";
	}
}
?>