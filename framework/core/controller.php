<?php
    class controller {
        private static $instance;
        public $load, $input, $model;
        public $data = array(
            'values' => array(),
            'errors' => array()
        );

        public function __construct() {
            self::$instance = $this;
            $this->load = new loader();
            $this->input = new input();
        }

        public static function instance() {
            return self::$instance;
        }

        public function errors() {
            return count(array_filter($this->data['errors'], function ($value) { return $value !== false; }));
        }

        public function add_error($field, $error) {
            if (is_array($this->data['errors']))
                $this->data['errors'] = array_merge($this->data['errors'], array($field => $error));
            else $this->data['errors'] = array($field => $error);
        }
        public function get_errors() {
            if (isset($this->data['errors']))
                return $this->data['errors'];
            else return array();
        }

        public function get_version($builds) {
            if (strpos($builds, '12340') !== false)
                return 'wotlk';
            if (strpos($builds, '8606') !== false)
                return 'tbc';
            return 'classic';
        }

        public function get_faction($race) {
            return in_array($race, [1, 3, 4, 7, 11]) ? 'Alliance' : 'Horde';
        }

        public function get_race_name($race) {
            $races = array(
                '1' => 'Human',
                '2' => 'Orc',
                '3' => 'Dwarf',
                '4' => 'Night Elf',
                '5' => 'Undead',
                '6' => 'Tauren',
                '7' => 'Gnome',
                '8' => 'Troll',
                '10' => 'Blood Elf',
                '11' => 'Draenei'
            );
            return isset($races[$race]) ? $races[$race] : 'Unknown';
        }

        public function get_class_name($class) {
            $classes = array(
                '1' => 'Warrior',
                '2' => 'Paladin',
                '3' => 'Hunter',
                '4' => 'Rogue',
                '5' => 'Priest',
                '7' => 'Shaman',
                '8' => 'Mage',
                '9' => 'Warlock',
                '11' => 'Druid'
            );
            return isset($classes[$class]) ? $classes[$class] : 'Unknown';
        }

        public function get_available_races($builds) {
            $races = array('1-0', '1-1', '2-0', '2-1', '3-0', '3-1', '4-0', '4-1', '5-0', '5-1', '6-0', '6-1', '7-0', '7-1', '8-0', '8-1');
            if (strpos($builds, '12340') !== false || strpos($builds, '8606') !== false)
                $races = array_merge($races, array('10-0', '10-1', '11-0', '11-1'));
            return $races;
        }

        public function get_available_classes() {
            return array('1', '2', '3', '4', '5', '7', '8', '9', '11');
        }

        public function get_uptime($uptime, $started, $history = false) {
            if (!$history)
                $uptime = $uptime > 0 ? $uptime : time() - $started;
            $days = floor($uptime/(24*3600));
            $uptime -= $days*(24*3600);
            $hours = floor($uptime/3600);
            $uptime -= $hours*3600;
            $minutes = floor($uptime/60);

            return $days.'d '.$hours.'h '.$minutes.'m';
        }

        public function get_money($money) {
            $gold = floor($money/1000);
            $money -= $gold*1000;
            $silver = floor($money/100);
            $money -= $silver*100;
            $copper = $money;

            return array(
                'gold' => $gold,
                'silver' => $silver,
                'copper' => $copper
            );
        }

        public function authenticated() {
            // Database cleanup.
            $this->model->remove_invalid_uptime();
            $this->model->remove_inactive_accounts();
            $this->model->remove_expired_tokens();

            if (!isset($_COOKIE[COOKIE_NAME]))
                return false;
            
            list($id, $username, $ip) = jwt::verify_token($_COOKIE[COOKIE_NAME]);
            if (!isset($id) || !isset($username) || !isset($ip) || $ip !== $_SERVER['REMOTE_ADDR']) {
                setcookie(COOKIE_NAME, '', time() - COOKIE_TIMEOUT, COOKIE_PATH);
                return false;
            }

            list($salt, $verifier, $secret, $tokens, $locked, $banned) = $this->model->get_login($username);
            if((isset($locked) && $locked) || (isset($banned) && $banned > 0)) {
                setcookie(COOKIE_NAME, '', time() - COOKIE_TIMEOUT, COOKIE_PATH);
                return false;
            }

			$this->model->set_user_address($username, $_SERVER['REMOTE_ADDR']);
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            
            return true;
        }
    }
?>