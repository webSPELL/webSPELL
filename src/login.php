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
#   Copyright 2005-2015 by webspell.org                                  #
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

/**
 * Somewhat secure login cookie (at the very least more secure than the previous
 * implementation).
 *
 * This implementation generates a random key for each login and stores a
 * hash of that key in the database. Because the keys are random, the database
 * entries are hashed and the tokens expire after a certain amount of time,
 * forging login cookies becomes rather hard.
 */
class LoginCookie
{
    private static function generateHash($key)
    {
        return hash('sha512', $key, true);
    }
  
    /**
     * Generate a cookie key. Optionally, a length in bytes can be specified,
     * which defaults to 64.
     * Note that on some systems, this function will not produce
     * cryptographically strong keys due to openssl_random_pseudo_bytes not
     * being available.
     */
    private static function generateKey($length = 64)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $key = openssl_random_pseudo_bytes($length);
        } else {
            $key = '';
            while ($length --> 0) {
                $key .= pack('C', mt_rand(0, 255));
            }
        }
        return $key;
    }
  
    private static function setCookie($cookieName, $cookieValue, $expiration)
    {
        if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
            $cookieInfo = session_get_cookie_params();
            setcookie(
                $cookieName,
                $cookieValue,
                $cookieExpire,
                $cookieInfo[ 'path' ],
                $cookieInfo[ 'domain' ],
                $cookieInfo[ 'secure' ],
                true
            );
        } else {
            setcookie($cookieName, $cookieValue, $cookieExpire);
        }
    }

    private static function checkResult(\mysqli_stmt $stmt)
    {
        global $userID, $loggedin, $_language;
    
        if ($stmt->bind_result($userID, $language, $lastlogin)
                && $stmt->fetch()) {
            $loggedin = true;
            $_SESSION['ws_user'] = $userID;
            $_SESSION['ws_lastlogin'] = $lastlogin;

            if (!empty($language) && isset($_language)) {
                if ($_language->setLanguage($language)) {
                    $_SESSION['language'] = $language;
                }
            }
        }
    }

    /**
     * Set a cookie that can be used for a persistent login. The login cookie
     * is set to expire after a certain interval.
     * The cookie has the form <userID>:<key>, with <key> being a random
     * 64-character string (base64-encoded). Apart from setting the cookie, an
     * entry containing the user ID, a cryptographic hash of the key and the
     * expiration time will be inserted in the database.
     */
    public static function set($cookieName, $user, $expiration)
    {
        global $_database;

        $key  = self::generateKey();
        $hash = self::generateHash($key);
        $cookieValue = $user . ":" . base64_encode($key);
        $cookieExpire = $expiration > 0 ? time() + $expiration : 0;

        $stmt = $_database->prepare("INSERT INTO " . PREFIX . "cookies
                (userID, cookie, expiration) VALUES (?, ?, ?)");
        if (false !== $stmt) {
            $success = $stmt->bind_param("isi", $user, $hash, $cookieExpire)
                && $stmt->execute();
            $stmt->close();
        }

        if ($success) {
            self::setCookie($cookieName, $cookieValue, $cookieExpire);
        }
    }

    /**
     * Remove a login cookie. Both the cookie on the client side and the
     * database entry on the server side are removed. Note that this does not
     * remove the client session.
     */
    public static function clear($cookieName)
    {
        global $_database;

        $cookie = $_COOKIE[$cookieName];

        if (isset($cookie)) {
            $authent = explode(":", $cookie);
            $user = $authent[0];
            $key  = base64_decode($authent[1]);

            $stmt = $_database->prepare("DELETE FROM " . PREFIX . "cookies
                    WHERE `userID` = ? AND `cookie` = ?");
            if (false !== $stmt) {
                $stmt->bind_param('is', $user, self::generateHash($key)) &&
                $stmt->execute();
                $stmt->close();
            }

            $cookieValue = '';
            $cookieExpire = time() - (24 * 60 * 60);
            self::setCookie($cookieName, $cookieValue, $cookieExpire);
        }
    }
 
    /**
     * Check a login cookie. If an entry in the database exists for the cookie,
     * log the user in and set their last login time and language preference.
     * This also sets the ws_user session key, mapping to the user ID, to be
     * used during the remainder of the session.
     */
    public static function check($cookieName)
    {
        global $_database, $userID;
    
        $authent = explode(":", $_COOKIE[$cookieName]);
        $ws_user = $authent[0];
        $ws_pwd  = base64_decode($authent[1]);

        if (isset($ws_user, $ws_pwd)) {
            $stmt = $_database->prepare(
                "SELECT u.userID, language, lastlogin
                FROM  `" . PREFIX . "cookies` c
                INNER JOIN `" . PREFIX . "user` u
                ON c.userID = u.userID
                WHERE c.userID = ? AND c.cookie = ? AND c.expiration > ?"
            );

            if (false !== $stmt) {
                $hash = self::generateHash($ws_pwd);
                $stmt->bind_param('isi', $ws_user, $hash, time())
                    && $stmt->execute()
                    && self::checkResult($stmt);

                $stmt->close();
            }
        }
    }

    /**
     * Purge expired cookies from the database.
     */
    public static function purge()
    {
        global $_database;

        $stmt = $_database->prepare(
            "DELETE FROM " . PREFIX . "cookies WHERE `expiration` < ?"
        );

        if (false !== $stmt) {
            $stmt->bind_param('i', time()) &&
            $stmt->execute();
            $stmt->close();
        }
    }
}

global $userID, $loggedin;

$userID = 0;
$loggedin = false;

if (isset($_SESSION['ws_user'])) {
    $userID = $_SESSION['ws_user'];
    $loggedin = true;
} elseif (isset($_COOKIE['ws_auth'])) {
    LoginCookie::purge();
    LoginCookie::check('ws_auth');
}
