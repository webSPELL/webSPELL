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
