<?
	class model {
		public function get_login($username) {
			$statement = database::connection()->prepare('SELECT r.id AS id, r.s AS salt, r.v AS verifier, r.token, w.tokens AS tokens, r.locked AS locked, COALESCE((SELECT SUM(r4.active) FROM '. DB_REALMD .'.account r3, '. DB_REALMD .'.account_banned r4 WHERE r3.id = r4.account_id AND r3.username = :username), 0) as banned FROM '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w WHERE r.id = w.id AND r.username = :username GROUP BY r.id');
			$statement->execute(array('username' => $username));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return array(null, null, null, null, null, null);
			return array($result['salt'], $result['verifier'], $result['token'], explode(',', $result['tokens']), $result['locked'], $result['banned']);
		}
		
		public function set_user_address($username, $ip) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w SET w.ip = :ip WHERE r.id = w.id AND r.username = :username');
			return $statement->execute(array('ip' => $ip, 'username' => $username));
		}

		public function remove_expired_tokens() {
			$date = date("Y-m-d H-i-s", floor(microtime(true)));
			$statement = database::connection()->prepare('DELETE FROM '. DB_WEBSITE .'.account_tokens WHERE type IN(1, 2) AND expiration < :expiration');
			return $statement->execute(array('expiration' => $date));
		}

		public function remove_inactive_accounts() {
			$date = date("Y-m-d H-i-s", floor(microtime(true)));
			try {
				database::connection()->beginTransaction();
				$statement = database::connection()->prepare('DELETE FROM '. DB_REALMD .'.account WHERE id IN (SELECT id FROM '. DB_WEBSITE .'.account_tokens WHERE type = :type AND expiration < :expiration)');
				$result = $statement->execute(array('type' => 0, 'expiration' => $date));
				if (!$result)
					throw new Exception("Error!");
				$statement = database::connection()->prepare('DELETE FROM '. DB_WEBSITE .'.account WHERE id IN (SELECT id FROM '. DB_WEBSITE .'.account_tokens WHERE type = :type AND expiration < :expiration)');
				$result2 = $statement->execute(array('type' => 0, 'expiration' => $date));
				if (!$result2)
					throw new Exception("Error!");
				$statement = database::connection()->prepare('DELETE FROM '. DB_WEBSITE .'.account_tokens WHERE type = :type AND expiration < :expiration');
				$result3 = $statement->execute(array('type' => 0, 'expiration' => $date));
				if (!$result3)
					throw new Exception("Error!");
				database::connection()->commit();
			} catch(Exception $e) {
				database::connection()->rollBack();
			}
		}

		public function remove_invalid_uptime() {
			$statement = database::connection()->prepare('DELETE FROM '. DB_REALMD .'.uptime WHERE (uptime < :uptime AND starttime < :starttime) OR (uptime < :history_uptime AND starttime < :history_starttime)');
			return $statement->execute(array('uptime' => MANGOSD_UPTIME_CLEAR, 'starttime' => time() - MANGOSD_UPTIME_CLEAR, 'history_uptime' => MANGOSD_UPTIME_HISTORY_CLEAR, 'history_starttime' => time() - MANGOSD_UPTIME_HISTORY_CLEAR));
		}
    }
?>