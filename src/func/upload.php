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
#   Copyright 2005-2014 by webspell.org                                  #
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

abstract class Upload {

    const UPLOAD_ERR_CANT_READ = 99;

    protected $error;

    public abstract function hasFile();
    public abstract function hasError();
    public abstract function getError();
    public abstract function getTempFile();
    public abstract function getFilename();
    public abstract function getSize();
    public abstract function saveAs($newFilePath, $override = true);
    protected abstract function getFallbackMimeType();

    public function getExtension()
    {
        $filename = $this->getFilename();
        if (stristr($filename, ".") !== false) {
            return substr($filename, strrpos($filename, ".") + 1);
        } else {
            return null;
        }
    }

    public function getMimeType()
    {
        $filename = $this->getTempFile();
        if (function_exists("finfo_file")) {
            $handle = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($handle, $filename);
            if (stristr($mime, ";") !== false) {
                $mime = substr($mime, 0, strpos($mime, ";"));
            }
        } elseif (function_exists("mime_content_type")) {
            $mime = mime_content_type($filename);
        }

        if (!isset($mime) || empty($mime)) {
            $mime = $this->getFallbackMimeType();
        }

        return $mime;
    }

    public function supportedMimeType($required_mime)
    {
        $mime = $this->getMimeType();

        if (is_array($required_mime)) {
            foreach ($required_mime as $req_mime) {
                if ($req_mime == $mime) {
                    return true;
                }
            }
        } else {
            if ($required_mime == $mime) {
                return true;
            }
        }

        return false;
    }

    public function translateError()
    {
        global $_language;
        $_language->readModule('upload', true);
        switch ($this->error) {
            case UPLOAD_ERR_INI_SIZE:
                $message = $_language->module[ 'file_too_big' ];
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = $_language->module[ 'file_too_big' ];
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = $_language->module[ 'incomplete_upload' ];
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = $_language->module[ 'no_file_uploaded' ];
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = $_language->module[ 'no_temp_folder_available' ];
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = $_language->module[ 'cant_write_temp_file' ];
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = $_language->module[ 'unexpected_error' ];
                break;
            case self::UPLOAD_ERR_CANT_READ:
                $message = $_language->module[ 'cant_copy_file' ];
                break;
            default:
                $message = $_language->module[ 'unexpected_error' ];
                break;
        }
        return $message;
    }
}

class HttpUpload extends Upload
{
    private $field;

    public function __construct($field_name)
    {
        $this->field = $field_name;
        $this->error = $_FILES[ $this->field ][ 'error' ];
    }

    public function hasFile()
    {
        return (isset($_FILES[ $this->field ]) && $_FILES[ $this->field ][ 'error' ] != UPLOAD_ERR_NO_FILE);
    }

    public function hasError()
    {
        return $_FILES[ $this->field ][ 'error' ] !== UPLOAD_ERR_OK;
    }

    public function getError()
    {
        if ($this->hasFile()) {
            return $_FILES[ $this->field ][ 'error' ];
        } else {
            return null;
        }
    }

    public function getTempFile()
    {
        return $_FILES[ $this->field ][ 'tmp_name' ];
    }

    public function getFilename()
    {
        return basename($_FILES[ $this->field ][ 'name' ]);
    }

    public function getSize()
    {
        return $_FILES[ $this->field ]['size'];
    }

    public function saveAs($newFilePath, $override = true)
    {
        if (!file_exists($newFilePath) || $override) {
            return move_uploaded_file($this->getTempFile(), $newFilePath);
        } else {
            return false;
        }
    }

    protected function getFallbackMimeType()
    {
        return $_FILES[ $this->field ][ 'type' ];
    }
}

class UrlUpload extends Upload {
    private $tmpfile;
    private $file;
    public function __construct($url)
    {
        $this->file = $url;
        $this->error = UPLOAD_ERR_NO_FILE;
        $this->download();
    }

    private function download()
    {
        if(empty($this->file) === false){
            $this->tempfile = tempnam('tmp/','upload_');
            $this->filename = basename(parse_url($this->file,PHP_URL_PATH));
            if(copy($this->file, $this->tempfile)){
                $this->error = UPLOAD_ERR_OK;
            }
            else {
                $this->error = self::UPLOAD_ERR_CANT_READ;
            }
        }
        else{
            $this->error = UPLOAD_ERR_NO_FILE;
        }
    }

    public function hasFile(){
        return ($this->error != UPLOAD_ERR_NO_FILE);
    }

    public function hasError(){
        return ($this->error !== UPLOAD_ERR_OK);
    }

    public function getError(){
        if ($this->hasFile()) {
            return $this->error;
        } else {
            return null;
        }
    }

    public function getTempFile(){
        return $this->tempfile;
    }

    public function getFilename(){
        return $this->filename;
    }

    public function getSize(){
        return filesize($this->getTempFile());
    }

    protected function getFallbackMimeType(){
        $headers = get_headers($this->file,1);
        return (isset($headers['Content-Type'])) ? $headers['Content-Type'] : "application/octet-stream";
    }

    public function saveAs($newFilePath, $override = true)
    {
        if (!file_exists($newFilePath) || $override) {
            return rename($this->getTempFile(), $newFilePath);
        } else {
            return false;
        }
    }
}
