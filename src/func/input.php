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

class Input
{
    const String = 1;
    const Int = 2;
    const StringAlpha = 3;

    static function translateTypeToFlags($type)
    {
        if ($type == Input::String || $type == Input::StringAlpha) {
            return array(FILTER_DEFAULT, FILTER_NULL_ON_FAILURE);
        } elseif ($type == Input::Int) {
            return array(FILTER_VALIDATE_INT, FILTER_FLAG_NONE);
        }
    }

    static function validFromGet($key, $type = Input::String)
    {
        list($filter_type, $flags) = Input::translateTypeToFlags($type);
        $filter_result = filter_input(INPUT_GET, $key, $filter_type, $flags);
        if ($type === Input::StringAlpha && !ctype_alpha($filter_result)) {
            return null;
        }
        return $filter_result;
    }

    static function validFromPost($key, $type = Input::String)
    {
        return filter_input(INPUT_POST, $key, Input::translateTypeToFlags($type));
    }
}

?>
