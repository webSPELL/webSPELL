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

class Language
{

    public $language = 'uk';
    public $module = array();
    private $language_path = 'languages/';

    public function setLanguage($to)
    {

        $filepath = "languages/";
        $langs = array();

        if ($dh = opendir($filepath)) {
            while ($file = mb_substr(readdir($dh), 0, 2)) {
                if ($file != "." && $file != ".." && is_dir($filepath . $file)) {
                    $langs[ ] = $file;
                }
            }
            closedir($dh);
        }

        if (in_array($to, $langs)) {
            if (is_dir($this->language_path . $to)) {
                $this->language = $to;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getRootPath()
    {
        return $this->language_path;
    }

    public function readModule($module, $add = false)
    {

        global $default_language;

        $module = str_replace(array('\\', '/', '.'), '', $module);
        if (file_exists($this->language_path . $this->language . '/' . $module . '.php')) {
            $module_file = $this->language_path . $this->language . '/' . $module . '.php';
        } elseif (file_exists($this->language_path . $default_language . '/' . $module . '.php')) {
            $module_file = $this->language_path . $default_language . '/' . $module . '.php';
        } elseif (file_exists($this->language_path . 'uk/' . $module . '.php')) {
            // UK as worst case
            $module_file = $this->language_path . 'uk/' . $module . '.php';
        } else {
            return false;
        }

        if (isset($module_file)) {
            include($module_file);
            if (!$add) {
                $this->module = array();
            }

            foreach ($language_array as $key => $val) {
                $this->module[ $key ] = $val;
            }
        }
        return true;
    }

    public function replace($template)
    {

        foreach ($this->module as $key => $val) {
            $template = str_replace('%' . $key . '%', $val, $template);
        }

        return $template;
    }

    public function getTranslationTable()
    {
        $map = array();
        foreach ($this->module as $key => $val) {
            $newKey = '%' . $key . '%';
            $map[ $newKey ] = $val;
        }
        return $map;
    }
}
