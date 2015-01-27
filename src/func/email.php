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

require '../../components/phpmailer/PHPMailerAutoload.php';

class Email
{
    public static function sendEmail($from, $module, $to, $subject, $message)
    {
        $get = safe_query("SELECT * FROM " . PREFIX . "email");
        while ($ds = mysqli_fetch_assoc($get)) {
            $host = $ds['host'];
            $user = $ds['user'];
            $password = $ds['password'];
            $port = $ds['port'];
            $debug = $ds['debug'];
            $auth = $ds['auth'];
            $html = $ds['html'];
            $smtp = $ds['smtp'];
            $secure = $ds['secure'];

        }
        $mail = new PHPMailer;

        $mail->SMTPDebug = $debug;

        if ($smtp === 1) {
            $mail->isSMTP();
        } else {
            $mail->isMail();
        }

        $mail->Host = $host;

        if ($auth === 1) {
            $mail->SMTPAuth = true;
            $mail->Username = $user;
            $mail->Password = $password;
        } else {
            $mail->SMTPAuth = false;
        }

        $mail->SMTPSecure = $secure;
        $mail->Port = $port;

        $mail->From = $from;
        $mail->FromName = $module;
        $mail->addAddress($to);

        if ($html === 1) {
            $mail->isHTML(true);
            $mail->Body = $message;
        } else {
            $mail->isHTML(false);
            $plain = $mail->html2text($message);
            $mail->Body = $plain;
        }

        $mail->Subject = $subject;

        $plain = $mail->html2text($message);
        $mail->AltBody = $plain;

        if(!$mail->send()) {
            return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            return 'Message has been sent';
        }
    }

}
