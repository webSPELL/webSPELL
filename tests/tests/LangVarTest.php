<?php

namespace webspell;

class LangVarTest extends PHPUnit_Framework_TestCase
{

    const PATH_LANG_FILES = "../languages/";
    const PATH_TPL_FILES = "../templates/";
    const PATH_ROOT = "../";
    const DEFAULT_LANG = "uk";

    const LANG_VAR_REGEX = "/\'(?<key>.*)\'(.*)=>/";

    const TPL_LANG_VAR_REGEX = "/%(?<key>\w+)%/";
    const PHP_LANG_VAR_REGEX = '/\$_language->module\[\s*\'(?<key>\w+)\'/';

    private function startsWith($haystack, $needle)
    {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    private function endsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== false;
    }

    private function checkIfKeyExistsInEveryLangFile($filename, $key)
    {
        $langFolders = scandir(self::PATH_LANG_FILES);
        foreach ($langFolders as $folder) {
            if ($this->startsWith($folder, ".")) {
                continue;
            }
            if (!is_dir(self::PATH_LANG_FILES . $folder)) {
                continue;
            }

            $fileString = $this->readLangFileAsString($folder, $filename);

            preg_match_all(self::LANG_VAR_REGEX, $fileString, $matches);
            $keys = $matches['key'];
            if (!in_array($key, $keys)) {
                echo "key: " . $key . " does not exist in " . $folder . "/" . $filename;
                return false;
            }
        }
        return true;
    }

    private function readLangFileAsString($lang, $filename)
    {
        $fileString = file_get_contents(self::PATH_LANG_FILES . $lang . "/" . $filename);
        return $fileString;
    }

    private function checkForLanguageKeyUsage($path, $key)
    {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($this->startsWith($file, ".")) {
                continue;
            }
            if (!is_file($path . $file)) {
                continue;
            }

            $fileString = file_get_contents($path . $file);
            if (strpos($fileString, "%" . $key . "%") !== false ||
                strpos($fileString, '$_language->module[ \'' . $key . '\' ]') !== false) {
                return true;
            }
        }
        return false;
    }

    private function checkForKeyExists($key)
    {
        $files = scandir(self::PATH_LANG_FILES . self::DEFAULT_LANG);
        foreach ($files as $file) {
            if ($this->startsWith($file, ".")) {
                continue;
            }
            if (!is_file(self::PATH_LANG_FILES . self::DEFAULT_LANG . "/" . $file)) {
                continue;
            }

            $fileString = $this->readLangFileAsString(self::DEFAULT_LANG, $file);

            preg_match_all(self::LANG_VAR_REGEX, $fileString, $matches);
            $keys = $matches['key'];
            if (in_array($key, $keys)) {
                return true;
            }
        }
        return false;
    }

    public function testCheckForUnknownButUsedKeys()
    {

        // check tpl files
        $templates = scandir(self::PATH_TPL_FILES);
        foreach ($templates as $file) {
            if ($this->startsWith($file, ".")) {
                continue;
            }
            if (!is_file(self::PATH_TPL_FILES . $file)) {
                continue;
            }
            $fileString = file_get_contents(self::PATH_TPL_FILES . $file);
            preg_match_all(self::TPL_LANG_VAR_REGEX, $fileString, $matches);
            $keys = $matches['key'];
            foreach ($keys as $key) {
                if (!$this->checkForKeyExists($key)) {
                    echo "key: " . $key . " does not exist in vars";
                    return false;
                }
            }
        }


        // check php files
        $files = scandir(self::PATH_ROOT);
        foreach ($files as $file) {
            if ($this->startsWith($file, ".")) {
                continue;
            }
            if (!is_file(self::PATH_ROOT . $file)) {
                continue;
            }
            $fileString = file_get_contents(self::PATH_ROOT . $file);
            preg_match_all(self::PHP_LANG_VAR_REGEX, $fileString, $matches);
            $keys = $matches['key'];
            foreach ($keys as $key) {
                if (!$this->checkForKeyExists($key)) {
                    echo "key: " . $key . " does not exist in vars \n";
                    return false;
                }
            }
        }

        return true;
    }


    public function testCheckForUnusedKeysInFallbackLang()
    {

        $folderPath = self::PATH_LANG_FILES . self::DEFAULT_LANG;
        $langFiles = scandir($folderPath);
        foreach ($langFiles as $file) {
            if ($this->startsWith($file, ".")) {
                continue;
            }
            if (!is_file($folderPath . "/" . $file)) {
                continue;
            }

            $fileString = $this->readLangFileAsString(self::DEFAULT_LANG, $file);

            preg_match_all(self::LANG_VAR_REGEX, $fileString, $matches);

            $keys = $matches['key'];

            foreach ($keys as $key) {
                if (!$this->checkForLanguageKeyUsage(self::PATH_TPL_FILES, $key) &&
                    !$this->checkForLanguageKeyUsage(self::PATH_ROOT, $key)) {
                    echo "key: " . $key . " not found (coming from: " . $file . ")\n";
                    return false;
                }
            }
        }

        return true;
    }


    public function testCheckForConsistenceInFiles()
    {
        $langFolders = scandir(self::PATH_LANG_FILES);
        foreach ($langFolders as $folder) {
            if ($this->startsWith($folder, ".")) {
                continue;
            }
            $folderPath = self::PATH_LANG_FILES . $folder;
            if (!is_dir($folderPath)) {
                continue;
            }

            $langFiles = scandir($folderPath);
            foreach ($langFiles as $file) {
                if ($this->startsWith($file, ".")) {
                    continue;
                }
                if (!is_file($folderPath . "/" . $file)) {
                    continue;
                }

                $fileString = $this->readLangFileAsString($folder, $file);

                preg_match_all(self::LANG_VAR_REGEX, $fileString, $matches);

                $keys = $matches['key'];

                foreach ($keys as $key) {
                    if (!$this->checkIfKeyExistsInEveryLangFile($file, $key)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
