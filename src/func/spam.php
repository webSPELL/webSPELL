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

namespace webspell;

class SpamApi
{
    const NOSPAM = 0;
    const SPAM = 1;
    private static $instance;

    private function __construct()
    {
        $get = safe_query(
            "SELECT
                `spam_check`,
                `spamapikey`,
                `spamapihost`,
                `spamapiblockerror`,
                `spammaxposts`
            FROM
                " . PREFIX . "settings
            LIMIT 0,1"
        );
        if (mysqli_num_rows($get)) {
            $ds = mysqli_fetch_assoc($get);
            $this->key = $ds['spamapikey'];
            $this->host = $ds['spamapihost'];
            $this->enabled = ($ds['spam_check'] == 1);
            $this->blockOnError = ($ds['spamapiblockerror'] == 1);
            $this->maxPosts = (int)$ds['spammaxposts'];
        }
    }

    final public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function learn($message, $type)
    {
        if ($this->enabled && $this->key) {
            $postdata = array();
            $postdata["apikey"] = $this->key;
            $postdata["learn"] = json_encode(array("message" => $message, "type" => $type));
            $response = $this->postRequest($postdata);
            if (!empty($response)) {
                $json = json_decode($response, true);
                if ($json['response'] != "ok") {
                    $this->logError($response, $postdata);
                }
            }
        }
    }

    public function validate($message)
    {
        if ($this->enabled) {
            $run = true;
            if ($GLOBALS['loggedin']) {
                if (getuserforumposts($GLOBALS['userID']) + getallusercomments($GLOBALS['userID']) > $this->maxPosts) {
                    $run = false;
                }
            }
            if ($run) {
                $ret = self::NOSPAM;
                $postdata = array();
                $postdata["validate"] = json_encode(array("message" => $message));
                $response = $this->postRequest($postdata);
                if (!empty($response)) {
                    $json = json_decode($response, true);
                    if ($json['response'] != "ok") {
                        $this->logError($response, $postdata);
                        if ($this->blockOnError) {
                            $ret = self::SPAM;
                        }
                    } else {
                        $rating = (float)$data["response"];
                        if ($rating >= $GLOBALS['spamCheckRating']) {
                            $ret = self::SPAM;
                        }
                    }
                } else {
                    if ($this->blockOnError) {
                        $ret = self::SPAM;
                    }
                }
                return $ret;
            }
            return self::NOSPAM;
        }
        return self::NOSPAM;
    }

    private function logError($message, $data)
    {
        safe_query(
            "INSERT INTO
                `" . PREFIX . "api_log` (`message`,`date`, `data`)
            VALUES (
                '" . addslashes($message) . "',
                '" . time() . "',
                '" . json_encode($data) . "'
            )"
        );
    }

    private function postRequest($data)
    {
        if (function_exists("curl_init")) {
            $ch = curl_init($this->host);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (stripos($this->host, "https") == 0) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CAINFO, "src/ca.pem");
            }
            $response = curl_exec($ch);
            if ($response !== false) {
                curl_close($ch);
                return $response;
            } else {
                $this->logError("No Api-Respone. " . curl_error($ch), $data);
                curl_close($ch);
                return "";
            }
        } elseif (include ("HTTP/Request2.php") && class_exists("HTTP_Request2")) {
            $request = new HTTP_Request2($this->host, HTTP_Request2::METHOD_POST);
            if (stripos($this->host, "https") == 0) {
                $request->setConfig(array("ssl_cafile" => "src/ca.pem", "ssl_verify_peer" => false));
            }
            $url = $request->getUrl();
            $url->setQueryVariables($data);
            try {
                return $request->send()->getBody();
            } catch (Exception $ex) {
                $this->logError("No Api-Respone. Code: " . $ex->getCode() . ", Message: " . $ex->getMessage(), $data);
                return "";
            }
        } elseif (class_exists("HttpRequest")) {
            $request = new HttpRequest($this->host, HttpRequest::METH_POST);
            $request->addPostFields($data);
            try {
                return $request->getBody();
            } catch (Exception $ex) {
                $this->logError("No Api-Respone. Code: " . $ex->getCode() . ", Message: " . $ex->getMessage(), $data);
                return "";
            }
        } elseif (ini_get("allow_url_fopen")) {
            $build_data = http_build_query($data);
            $params = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/x-www-form-urlencoded",
                    'content' => $build_data
                )
            );
            $context = stream_context_create($params);
            $con = file_get_contents($this->host, false, $context);
            if ($con !== false) {
                return $con;
            } else {
                $this->logError("No Api-Respone.", $data);
                return "";
            }
        } else {
            $this->logError(
                "No Method available to query Api.",
                "Enable Curl or Pear HTTP Request(2) or allow_url_fopen"
            );
            return "";
        }
    }
}
