<?
    class realms_model extends model {
        public function get_realmlist() {
            $query = 'SELECT r.id AS id, r.name AS name, r.address AS address, r.port AS port, r.icon AS type, r.population AS population, r.realmbuilds AS builds, COALESCE(u.starttime, 0) AS started, COALESCE(u.uptime, 0) AS uptime
                      FROM ' . DB_REALMD . '.realmlist r
                      LEFT JOIN ' . DB_REALMD . '.uptime u ON r.id = u.realmid
                      LEFT JOIN (SELECT realmid, MAX(starttime) AS max_starttime FROM ' . DB_REALMD . '.uptime GROUP BY realmid) u2
                      ON u.realmid = u2.realmid AND u.starttime = u2.max_starttime
                      WHERE (u.realmid IS NULL OR (u.realmid IS NOT NULL AND u.starttime = u2.max_starttime))
                      ORDER BY r.id ASC';
			$statement = database::connection()->prepare($query);
			$statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (!$result)
				return array();
			return $result;
        }

        public function get_realm_history($id) {
            $query = 'SELECT r.name AS name, COALESCE(u.starttime, 0) AS started, COALESCE(u.uptime, 0) AS uptime, COALESCE(u.maxplayers, 0) AS players
                      FROM ' . DB_REALMD . '.realmlist r, ' . DB_REALMD . '.uptime u
                      WHERE r.id = u.realmid AND r.id = :id
                      ORDER BY r.id ASC, u.starttime DESC';
			$statement = database::connection()->prepare($query);
			$statement->execute(array('id' => $id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (!$result)
				return array();
			return $result;
        }

        public function get_players_online_count($id) {
            $statement = database::connection()->prepare('SELECT count(online) AS count FROM ' . DB_MANGOSD[$id]['character'] . '.characters WHERE online = 1');
			$statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$result)
                return 0;
            else return $result['count'];
        }
        
        public function get_players_online($id) {
            $statement = database::connection()->prepare('SELECT `name`, `race`, `class`, `gender`, `level`, `totaltime` FROM ' . DB_MANGOSD[$id]['character'] . '.characters WHERE online = 1');
			$statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (!$result)
                return array();
            else return $result;
        }

        public function get_race_statistics($id) {
            $statement = database::connection()->prepare('SELECT CONCAT(race, \'-\', gender) as race, COUNT(race) AS count, (SELECT COUNT(*) FROM ' . DB_MANGOSD[$id]['character'] . '.characters) AS total FROM ' . DB_MANGOSD[$id]['character'] . '.characters GROUP BY race, gender');
			$statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (!$result)
                return array();
            else return $result;
        }

        public function get_class_statistics($id) {
            $statement = database::connection()->prepare('SELECT class, COUNT(class) AS count, (SELECT COUNT(*) FROM ' . DB_MANGOSD[$id]['character'] . '.characters) AS total FROM ' . DB_MANGOSD[$id]['character'] . '.characters GROUP BY class');
			$statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (!$result)
                return array();
            else return $result;
        }

        public function get_realm_status($address, $port) {
            return @fsockopen($address, $port, $code, $message, 0.005);
        }
    }
?>