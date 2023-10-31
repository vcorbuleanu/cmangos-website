<?
    class srp6 {
        public static function verifier($username, $password, $salt) {
            // Constants
            $g = gmp_init(7);
            $n = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
            // First Hash
            $h1 = sha1(strtoupper($username . ':' . $password), true);
            // Second Hash
            $h2 = sha1(strrev($salt) . $h1, true);
            // Integer Conversion
            $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);
            // Formula: g^h2%n
            $verifier = gmp_powm($g, $h2, $n);
            // Byte Array Conversion
            $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);
            // Pad To 32 Bytes
            $verifier = str_pad($verifier, 32, chr(0), STR_PAD_RIGHT);
            // Return Reversed Verifier
            return strrev($verifier);
        }

        public static function get_data($username, $password) {
            // Generate Binary Salt
            $salt = random_bytes(32);
            // Calculate Verifier
            $verifier = self::verifier(strtoupper($username), $password, $salt);
            // Uppercase Conversion
            $salt = strtoupper(bin2hex($salt));
            $verifier = strtoupper(bin2hex($verifier));
            // Return Data
            return array($salt, $verifier);
        }

        public static function check_data($username, $password, $salt, $verifier)
        {
            // Generate Binary Salt
            $salt = hex2bin($salt);
            // Calculate Verifier
            $_verifier = self::verifier(strtoupper($username), $password, $salt);
            // Uppercase Conversion
            $_verifier = strtoupper(bin2hex($_verifier));

            return $verifier === $_verifier;
        }
    }
?>