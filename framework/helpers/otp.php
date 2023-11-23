<?
	/**
	* @package   cmangos-website
	* @version   1.0
	* @author    Victor Corbuleanu
	* @copyright 2023 Victor Corbuleanu
	* @link      https://github.com/vcorbuleanu/cmangos-website/
	* @license   https://github.com/vcorbuleanu/cmangos-website/blob/master/LICENCE.txt Attribution-NonCommercial-NoDerivatives 4.0 International  
	**/
	
    class otp {
        public static function get_secret() {
            $base32_alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
            $bits_per_value = 5;
            $random_bytes_required = (int)(((int)OTP_SECRET_LENGTH * $bits_per_value) / 8 ) + 1;
            $random_bytes = random_bytes($random_bytes_required);

            $random_bits='';
            for ($i=0; $i < $random_bytes_required; ++$i) {
                $random_bits .= str_pad(decbin(ord($random_bytes[$i])), 8, '0', STR_PAD_LEFT);
            }

            $secret='';
            for ($i=0; $i < (int)OTP_SECRET_LENGTH; ++$i) {
                $random_value_bin=substr($random_bits, $i * $bits_per_value, $bits_per_value);
                $secret .= $base32_alphabet[bindec($random_value_bin)];
            }
            
            return $secret;
        }

        public static function get_recovery_tokens() {
            $recovery_tokens = [];

            $base32_alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
            $bits_per_value = 5;
            $random_bytes_required = (int)(((int)OTP_TOKEN_LENGTH * $bits_per_value) / 8 ) + 1;

            for($k=0; $k < (int)OTP_RECOVERY_TOKENS; ++$k) {
                $random_bytes = random_bytes($random_bytes_required);
                
                $random_bits='';
                for ($i=0; $i < $random_bytes_required; ++$i) {
                    $random_bits .= str_pad(decbin(ord($random_bytes[$i])), 8, '0', STR_PAD_LEFT);
                }
    
                $token='';
                for ($i=0; $i < (int)OTP_TOKEN_LENGTH; ++$i) {
                    $random_value_bin=substr($random_bits, $i * $bits_per_value, $bits_per_value);
                    $token .= $base32_alphabet[bindec($random_value_bin)];
                }
                $recovery_tokens[] = $token;
            }

            return implode(',', $recovery_tokens);
        }

        public static function check_otp($code, $secret) {
            $timestamp = self::timestamp();
            $_secret = self::base32_to_binary($secret);
            for ($_timestamp=-OTP_TOKEN_TIMEWINDOW; $_timestamp <= OTP_TOKEN_TIMEWINDOW; $_timestamp++)
                if (self::get_otp($_secret, $timestamp+$_timestamp) === $code)
                    return true;
            return false;
        }

        public static function get_otp($secret, $timestamp) {
            $_timestamp = pack('N*', 0) . pack('N*', $timestamp);
            $hash = hash_hmac('sha1', $_timestamp, $secret, true);
            $offset = ord($hash[19]) & 0xf;
            $token = ((ord($hash[$offset+0]) & 0x7f) << 24 ) | ((ord($hash[$offset+1]) & 0xff) << 16 ) | ((ord($hash[$offset+2]) & 0xff) << 8 ) | (ord($hash[$offset+3]) & 0xff);
            while ($token > OTP_TOKEN_CEILING)
                $token -= OTP_TOKEN_CEILING;
            return str_pad($token, OTP_TOKEN_LENGTH, '0', STR_PAD_LEFT);
        }
 
        public static function base32_to_binary($secret) {
            $secret  = strtoupper($secret);
            $n = $j = 0;
            $result = "";
            for ($i=0; $i < strlen($secret); $i++) {
                $n = $n << 5;
                $c = ord($secret[$i]);
                $c -= ($c>64) ? 65 : 24;
                $n += $c;
                $j += 5;

                if ($j > 7) {
                    $j -= 8;
                    $result .= chr(($n & (0xFF << $j)) >> $j);
                }
            }
            return $result;
        }

        public static function timestamp() {
            return floor(microtime(true) / OTP_TOKEN_TIMESTEP);
        }
    }
?>