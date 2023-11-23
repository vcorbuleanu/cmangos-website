<?
	/**
	* @package   cmangos-website
	* @version   1.0
	* @author    Victor Corbuleanu
	* @copyright 2023 Victor Corbuleanu
	* @link      https://github.com/vcorbuleanu/cmangos-website/
	* @license   https://github.com/vcorbuleanu/cmangos-website/blob/master/LICENCE.txt Attribution-NonCommercial-NoDerivatives 4.0 International  
	**/
	
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    require 'framework/libraries/phpmailer/Exception.php';
    require 'framework/libraries/phpmailer/PHPMailer.php';
    require 'framework/libraries/phpmailer/SMTP.php';

    class mailer {
        public static $instance = null;
        public static function send($recipient, $subject, $message) {
            if (!self::$instance)
                self::$instance = new PHPMailer;
            else self::$instance->clearAddresses();

            self::$instance->isSMTP(); 
            self::$instance->isHTML(true);
            self::$instance->SMTPDebug = 0;
            self::$instance->SMTPAuth = true;
            self::$instance->SMTPSecure = 'tls';
            self::$instance->SMTPOptions = array('ssl' => array('verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => true));
            self::$instance->Host = SMTP_HOST;
            self::$instance->Port = SMTP_PORT;
            self::$instance->Username = SMTP_USERNAME;
            self::$instance->Password = SMTP_PASSWORD;
            self::$instance->setFrom(SMTP_USERNAME, WEBSITE_TITLE);
            self::$instance->addAddress($recipient);
            self::$instance->Subject = $subject;
            self::$instance->Body = $message;

            return self::$instance->send();
        }
    }
?>