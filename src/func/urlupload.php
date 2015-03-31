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

class UrlUpload extends Upload
{
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
        if (empty($this->file) === false) {
            $this->tempfile = tempnam('tmp/', 'upload_');
            $this->filename = basename(parse_url($this->file, PHP_URL_PATH));
            if (copy($this->file, $this->tempfile)) {
                $this->error = UPLOAD_ERR_OK;
            } else {
                $this->error = self::UPLOAD_ERR_CANT_READ;
            }
        } else {
            $this->error = UPLOAD_ERR_NO_FILE;
        }
    }

    public function hasFile()
    {
        return ($this->error != UPLOAD_ERR_NO_FILE);
    }

    public function hasError()
    {
        return ($this->error !== UPLOAD_ERR_OK);
    }

    public function getError()
    {
        if ($this->hasFile()) {
            return $this->error;
        } else {
            return null;
        }
    }

    public function getTempFile()
    {
        return $this->tempfile;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getSize()
    {
        return filesize($this->getTempFile());
    }

    protected function getFallbackMimeType()
    {
        $headers = get_headers($this->file, 1);
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
