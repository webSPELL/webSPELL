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

class Template
{
    private $rootFolder;

    /**
    * @param string $rootFolder base folder where the template files are located
    */
    public function __construct($rootFolder = "templates")
    {
        $this->rootFolder = $rootFolder;
    }

    /**
    * returns the content of a template file
    *
    * @param string $template name of the template
    *
    * @return string content of the template
    * @throws \Exception when the file is not found
    */
    private function loadFile($template)
    {
        $file = $this->rootFolder . "/" . $template . ".html";
        if (file_exists($file)) {
            return file_get_contents($file);
        } else {
            throw new \Exception("Unknown Template File " . $file, 1);
        }
    }

    /**
    * Replace all keys of data with its values in the string
    * Longer keys are replaced first (users before user)
    *
    * @param string $template
    * @param array  $data
    *
    * @return string
    */
    private function replace($template, $data)
    {
        return strtr($template, $data);
    }

    /**
    * Replace a single template with one set of data and translate all language keys
    *
    * @param string $template name of a template
    * @param array  $data data which gets replaced
    *
    * @return string
    * @throws \Exception
    */
    public function replaceTemplate($template, $data)
    {
        $templateString = $this->loadFile($template);
        $templateTranslated = $this->replaceLanguage($templateString);
        return $this->replace($templateTranslated, $data);
    }

    /**
    * Replaces all language variables which are available
    *
    * @param string $template content of a template
    *
    * @return string
    */
    private function replaceLanguage($template)
    {
        return $this->replace($template, $GLOBALS[ '_language' ]->getTranslationTable());
    }

    /**
    * Return the content of one template evaluated multiple times
    * languagekeys are only translated once
    *
    * @param string $template name of the template
    * @param array  $datas multidimensional array with data for every replacements
    *
    *
    * @return string
    * @throws \Exception
    */
    public function replaceMulti($template, &$datas)
    {
        if (!is_array($datas) || !isset($datas[ 0 ]) || !is_array($datas[ 0 ])) {
            throw new \Exception("No multidimensional data given", 2);
        }

        $templateString = $this->loadFile($template);

        $templateBase = $this->replaceLanguage($templateString);

        $return = '';
        foreach ($datas as $data) {
            $return .= $this->replace($templateBase, $data);
        }
        unset($datas);
        return $return;
    }
}
