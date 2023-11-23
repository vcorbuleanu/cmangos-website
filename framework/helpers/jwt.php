<?
	/**
	* @package   cmangos-website
	* @version   1.0
	* @author    Victor Corbuleanu
	* @copyright 2023 Victor Corbuleanu
	* @link      https://github.com/vcorbuleanu/cmangos-website/
	* @license   https://github.com/vcorbuleanu/cmangos-website/blob/master/LICENCE.txt Attribution-NonCommercial-NoDerivatives 4.0 International  
	**/
	
    class jwt {
        public static function generate_token($payload) {
            if (COOKIE_ENCRYPTION) {
                $header = self::base64_url_encode(json_encode(array("alg" => "RSA-OAEP", "enc" => "A256ECB")));
                $payload = self::base64_url_encode(openssl_encrypt(json_encode($payload), "aes-256-ecb", COOKIE_ENCRYPTION_KEY, OPENSSL_RAW_DATA));
                $payload = array("jwe" => "$header.$payload");
        
                $base64_url_header = self::base64_url_encode(json_encode(array("alg" => "HS256", "typ" => "JWT")));
                $base64_url_payload = self::base64_url_encode(json_encode($payload));
                $base64_url_signature = self::base64_url_encode(hash_hmac('sha256', "$base64_url_header.$base64_url_payload", COOKIE_SIGNING_KEY, true));

                return "$base64_url_header.$base64_url_payload.$base64_url_signature";
            } else {
                $header = json_encode(array("alg" => "HS256", "typ" => "JWT"));
                $payload = json_encode($payload);
            
                $base64_url_header = self::base64_url_encode($header);
                $base64_url_payload = self::base64_url_encode($payload);
                $base64_url_signature = self::base64_url_encode(hash_hmac('sha256', "$base64_url_header.$base64_url_payload", COOKIE_SECRET, true));

                return "$base64_url_header.$base64_url_payload.$base64_url_signature";
            }
        }

        public static function verify_token($token) {
            if (COOKIE_ENCRYPTION) {
                list($header, $payload, $signature) = explode('.', $token);
                $expected_signature = self::base64_url_encode(hash_hmac('sha256', "$header.$payload", COOKIE_SIGNING_KEY, true));    
                if ($signature !== $expected_signature)
                    return null;

                $jwt_payload = json_decode(self::base64_url_decode($payload), true);
                if (!$jwt_payload || !isset($jwt_payload['jwe']))
                    return null;

                list($header, $encrypted_payload) = explode('.', $jwt_payload['jwe']);
                $payload = json_decode(openssl_decrypt(self::base64_url_decode($encrypted_payload), "aes-256-ecb", COOKIE_ENCRYPTION_KEY, OPENSSL_RAW_DATA), true);

                return $payload;
            } else {
                list($header_encoded, $payload_encoded, $signature) = explode('.', $token);

                $expected_signature = self::base64_url_encode(hash_hmac('sha256', "$header_encoded.$payload_encoded", COOKIE_SECRET, true));
                if ($signature !== $expected_signature)
                    return null;

                $payload = json_decode(self::base64_url_decode($payload_encoded), true);

                return $payload;
            }
        }

        private static function base64_url_encode($data) {
            $base64 = base64_encode($data);
            $base64_url = str_replace(array('+', '/', '='), array('-', '_', ''), $base64);
            return $base64_url;
        }

        private static function base64_url_decode($base64_url) {
            $base64 = str_replace(array('-', '_'), array('+', '/'), $base64_url);
            $data = base64_decode($base64);
            return $data;
        }
    }
?>