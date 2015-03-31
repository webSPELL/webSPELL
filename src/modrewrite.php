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

class ModRewrite
{

    private $translation = array();

    private $cache = null;
    private static $rewriteBase;

    public function __construct()
    {
        $this->translation['integer'] = array('replace' => '([0-9]+)', 'rebuild' => '([0-9]+?)');
        $this->translation['string'] = array('replace' => '(\w*?)', 'rebuild' => '(\w*?)');
        $this->translation['everything'] = array('replace' => '([^\'\\"]*)', 'rebuild' => '([^\'\\"]*)');
        $GLOBALS['rewriteBase'] = $this->getRewriteBase();
    }

    public function getTypes()
    {
        return array_keys($this->translation);
    }

    public function enable()
    {
        $this->buildCache();
        ob_start(array($this, 'rewriteBody'));

        /*
        header_register_callback only works after php 5.5.8 and 5.4.24 because of
        https://bugs.php.net/bug.php?id=66375
        fixed in
        https://github.com/php/php-src/commit/3c3ff434329d2f505b00a79bacfdef95ca96f0d2
        */
        // @codingStandardsIgnoreStart
        $fixedHeader = false;
        if (PHP_MAJOR_VERSION == 5) {
            if (PHP_MINOR_VERSION == 4) {
                if (PHP_RELEASE_VERSION > 24) {
                    $fixedHeader = true;
                }
            } elseif (PHP_MINOR_VERSION == 5) {
                if (PHP_RELEASE_VERSION > 8) {
                    $fixedHeader = true;
                }
            }
        }

        if ($fixedHeader) {
            header_register_callback(array($this, 'rewriteHeaders'));
        } else {
            register_shutdown_function(array($this, 'rewriteHeaders'));
        }
        // @codingStandardsIgnoreEnd
    }

    private function buildCache()
    {
        $this->cache = array();
        $get = safe_query("SELECT replace_regex, replace_result FROM " . PREFIX . "modrewrite ORDER BY link DESC");
        while ($ds = mysqli_fetch_assoc($get)) {
            $this->cache[] = $ds;
        }
    }

    public function getRewriteBase()
    {
        if (!isset(self::$rewriteBase)) {
            $path = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath(dirname(__FILE__) . '/../'));
            $path = str_replace('\\', '/', $path);
            if (strlen($path) > 0) {
                if ($path[0] != '/') {
                    $path = '/' . $path;
                }
                if ($path[strlen($path) - 1] != '/') {
                    $path = $path . '/';
                }
            } else {
                $path = '/';
            }
            self::$rewriteBase = $path;
        }
        return self::$rewriteBase;
    }

    public function generateHtAccess($basepath, $rewriteFileName = "_rewrite.php")
    {
        return '<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteBase ' . $basepath . '
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ ' . $rewriteFileName . '?url=$1 [L,QSA]
</IfModule>';
    }

    public function rewriteHeaders()
    {
        $headers = headers_list();
        foreach ($headers as $header) {
            if (stristr($header, "Location")) {
                header($this->rewrite($header, true), true);
            }
        }
    }

    public function rewriteBody($content, $phase)
    {
        return $this->rewrite($content, false);
    }

    private function rewrite($content, $headers = false)
    {
        $start_time = microtime(true);
        if (
            stristr($content, "MM_goToURL") ||
            stristr($content, "window.open") ||
            stristr($content, 'http-equiv="refresh"')
        ) {
            $extended_replace = true;
        } else {
            $extended_replace = false;
        }
        foreach ($this->cache as $ds) {
            $regex = $ds['replace_regex'];
            $replace = $ds['replace_result'];
            if ($headers === true) {
                $content = preg_replace(
                    "/()()Location:\s" . $regex . "/i",
                    'Location: ' . $this->getRewriteBase() . $replace,
                    $content
                );
            } else {
                $content = preg_replace(
                    "/(href|action|option value)=(['\"])" . $regex . "[\"']/iS",
                    '$1=$2' . $replace . '$2',
                    $content
                );
                if ($extended_replace) {
                    $content =
                        preg_replace(
                            "/onclick=(['\"])(window.open\(|MM_goToURL\('parent',|MM_confirm\('.*?',\s)'" .
                            $regex . "'/Si",
                            'onclick=$1$2\'' . $replace . '\'',
                            $content
                        );
                    $content =
                        preg_replace(
                            "/href=(['\"])(javascript:window.open\(|window.open\(|MM_goToURL\('parent',)'" .
                            $regex . "'/Si",
                            'href=$1$2\'' . $replace . '\'',
                            $content
                        );
                    $content = preg_replace(
                        "/()(<meta .*?;URL=)" . $regex . "\"/Si",
                        '$2' . $this->getRewriteBase() . $replace . '"',
                        $content
                    );
                }
            }
        }
        $needed = microtime(true) - $start_time;
        header('X-Rewrite-Time: ' . $needed);
        return $content;
    }

    public function buildReplace($regex, $replace, $fields = array())
    {
        $regex = str_replace(array('.', '?', '&', '/'), array('\.', '\?', '[&|&amp;]*', '\/'), $regex);
        if (count($fields)) {
            $i = 3;
            preg_match_all("/{(\w*)}/si", $regex, $matches, PREG_SET_ORDER);
            foreach ($matches as $field) {
                $regex =
                    str_replace("{" . $field[1] . "}", $this->translation[$fields[$field[1]]]['replace'], $regex);
                $replace = str_replace("{" . $field[1] . "}", '$' . $i, $replace);
                $i++;
            }
        }
        return array($regex, $replace);
    }

    public function buildRebuild($regex, $replace, $fields = array())
    {
        $i = 1;
        $regex = str_replace(array('.', '?', '/'), array('\.', '\?', '\/'), $regex);
        if (count($fields)) {
            preg_match_all("/{(\w*)}/si", $regex, $matches, PREG_SET_ORDER);
            foreach ($matches as $field) {
                $regex =
                    str_replace("{" . $field[1] . "}", $this->translation[$fields[$field[1]]]['rebuild'], $regex);
                $replace = str_replace("{" . $field[1] . "}", '$' . $i, $replace);
                $i++;
            }
        }
        return array($regex, $replace);
    }
}
