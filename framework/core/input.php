<?php
    class input {
        private $headers = array();
        
        public function post($index = null) {
            return $this->fetch_from_array($_POST, $index);
        }

        public function server($index = null) {
            return $this->fetch_from_array($_SERVER, $index);
        }

        public function method() {
            return strtolower($this->server('REQUEST_METHOD'));
        }

        private function fetch_from_array(&$array, $index = null) {
            $index = isset($index) ? $index : array_keys($array);
            if (is_array($index)) {
                $output = array();
                foreach ($index as $key) {
                    $output[$key] = $this->fetch_from_array($array, $key);
                }
                return $output;
            }
            if (isset($array[$index])) {
                $value = $array[$index];
            }
            else if (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) {
                $value = $array;
                for ($i = 0; $i < $count; $i++) {
                    $key = trim($matches[0][$i], '[]');
                    if ($key === '') {
                        break;
                    }
                    if (isset($value[$key])) {
                        $value = $value[$key];
                    } else {
                        return null;
                    }
                }
            } else {
                return null;
            }
            return $this->sanitize($value);
        }

        private function sanitize($value) {
            $sanitized = trim($value);
            $sanitized = filter_var($sanitized, FILTER_SANITIZE_STRING);
            $sanitized = strip_tags($sanitized);
            return $sanitized;
        }
    }
?>