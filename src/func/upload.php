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

class Upload {
	public function __constructor($field_name){
		$this->field = $field_name;
	}

	public function hasFile(){
		return isset($_FILES[$this->field]['error']);
	}

	public function hasError(){
		return $_FILES[$this->field]['error'] === UPLOAD_ERR_OK;
	}

	public function getError() {
		if($this->hasFile()){
			return $_FILES[$this->field]['error'];
		}
		else{
			return null;
		}
	}

	public function getMimeType(){
		if(function_exists("finfo_file")){
			$handle = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($handle, );
			if(stristr($mime, ";") !== false){
				$mime = substr($mime, 0,strpos($mime, ";"));
			}
		} else{
			$mime = mime_content_type($filename);
		}

		if($mime === false){
			$mime = $_FILES[$this->field]['type'];
		}
	}

	public function supportedMimeType($required_mime){
		$filename = $_FILES[$this->field]['tmp_name'];
		$mime = $this->getMimeType();

		if(is_array($required_mime)){
			foreach($required_mime as $req_mime){
				if($req_mime == $mime){
					return true;
				}
			}
		}
		else{
			if($required_mime == $mime){
				return true;
			}
		}

		return false;
	}

	public function getTempFile(){
		return $_FILES[$this->field]['tmp_name'];
	}

	public function saveAs($newFilePath){
		return move_uploaded_file($_FILES[$this->field]['tmp_name'], $newFilePath);
	}

	public function translateError($code){
		global $_language;
		$_language->readModule('upload',true);
		switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = $_language->module['file_too_big'];
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = $_language->module['file_too_big'];
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = $_language->module['incomplete_upload'];
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = $_language->module['no_file_uploaded'];
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = $_language->module['no_temp_folder_available'];
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = $_language->module['cant_write_temp_file'];
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = $_language->module['unexpected_errpr'];
                break;

            default:
                $message = $_language->module['unexpected_errpr'];
                break;
        } 
        return $message;
	}

}

?>