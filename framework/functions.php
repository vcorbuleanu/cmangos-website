<?
	/**
	* @package   cmangos-website
	* @version   1.0
	* @author    Victor Corbuleanu
	* @copyright 2023 Victor Corbuleanu
	* @link      https://github.com/vcorbuleanu/cmangos-website/
	* @license   https://github.com/vcorbuleanu/cmangos-website/blob/master/LICENCE.txt Attribution-NonCommercial-NoDerivatives 4.0 International  
	**/
	
    // These are here in case you're running a PHP version older than 7.2.33 
    if (!function_exists('str_starts_with')) {
        function str_starts_with($haystack, $needle) {
            return substr($haystack, 0, strlen($needle)) === $needle;
        }
    }

    if (!function_exists('str_ends_with')) {
        function str_ends_with($haystack, $needle) {
            $length = strlen($needle);
            if (!$length)
                return true;
            return substr($haystack, -$length) === $needle;
        }
    }

    function sanitize($value) {
        return strip_tags(filter_var(trim($value), FILTER_SANITIZE_STRING));
    }

    function random_string($length = 32) {
        return substr(sha1(time()), 0, $length);
    }

    function bbcode_to_html($text) {
        $text = htmlentities($text);
        $text = nl2br($text);
        $text = str_replace('  ', '&nbsp;&nbsp;', $text);
        $text = preg_replace('/\n\r/', '<br/>', $text);
        $text = preg_replace("/\[b\](.*?)\[\/b\]/s","<b>$1</b>", $text);
        $text = preg_replace("/\[i\](.*?)\[\/i\]/s","<i>$1</i>", $text);
        $text = preg_replace("/\[u\](.*?)\[\/u\]/s","<u>$1</u>", $text);
        $text = preg_replace("/\[s\](.*?)\[\/s\]/s","<s>$1</s>", $text);
        $text = preg_replace("/\[quote\](.*?)\[\/quote\]/s","<p>$1</p>", $text);
        $text = preg_replace("/\[img\](.*?)\[\/img\]/s","<img src='$1'>", $text);
        $text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/s","<a href='$1' target='_blank'>$2</a>", $text);
        $text = preg_replace("/\[p=(.*?)\](.*?)\[\/p\]/s","<div align='$1'>$2</div>", $text);
        $text = preg_replace("/\[color=(.*?)\](.*?)\[\/color\]/s","<font color='$1'>$2</font>", $text);
        $text = preg_replace("/[^\'\"\=\]\[<>\w]([\w]+:\/\/[^\n\r\t\s\[\]\>\<\'\"]+)/s","<a href='$1' target='_blank'>$1</a>", $text);
        return $text;
    }
?>