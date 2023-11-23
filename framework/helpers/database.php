<?
	/**
	* @package   cmangos-website
	* @version   1.0
	* @author    Victor Corbuleanu
	* @copyright 2023 Victor Corbuleanu
	* @link      https://github.com/vcorbuleanu/cmangos-website/
	* @license   https://github.com/vcorbuleanu/cmangos-website/blob/master/LICENCE.txt Attribution-NonCommercial-NoDerivatives 4.0 International  
	**/
	
    class database {
        public static $connection = null;
        public static function connection() {
            if (!self::$connection) {
                try {
                    self::$connection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_REALMD . ';charset=utf8', DB_USERNAME, DB_PASSWORD, array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                        PDO::ATTR_PERSISTENT => false
                    ));
                } catch(PDOException $exception) {
                    exit('Unable to connect to database!' . $exception->getMessage());
                }
            }
            return self::$connection;
        }
    }
?>