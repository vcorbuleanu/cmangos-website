<?
    class account_model extends model {
		public function check_username($username) {
			$statement = database::connection()->prepare('SELECT r.username, w.nickname FROM '. DB_REALMD . '.account AS r LEFT JOIN ' . DB_WEBSITE . '.account AS w ON r.id = w.id WHERE r.username = :username OR w.nickname = :username');
			$statement->execute(array('username' => $username));
			return ($statement->rowCount() > 0) ? 1 : 0;
		}
		
		public function check_email($email) {
			$statement = database::connection()->prepare('SELECT id, username FROM '. DB_REALMD .'.account WHERE email = :email');
			$statement->execute(array('email' => $email));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return array(null, null);
			return array($result['id'], $result['username']);
		}

		public function get_user_information($username) {
			$statement = database::connection()->prepare('SELECT r.id AS id, r.username AS username, w.avatar AS avatar, w.signature AS signature, w.ip AS ip, r.email AS email, w.nickname AS nickname, r.gmlevel AS gmlevel, r.locked AS locked, r.os AS os, r.platform AS platform, r.locale AS locale FROM ' . DB_REALMD . '.account r, ' . DB_WEBSITE . '.account w WHERE r.id = w.id AND r.username = :username');
			$statement->execute(array('username' => $username));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return null;
			return $result;
		}

		public function get_user_characters($username, $realm_id) {
			$statement = database::connection()->prepare('SELECT c.name AS name, c.race AS race, c.class AS class, c.gender AS gender, c.level AS level, c.xp AS xp, c.money AS money, c.online AS online, c.totaltime AS totaltime, c.map AS map, c.zone AS zone FROM '. DB_REALMD .'.account r, ' . DB_MANGOSD[$realm_id]['character'] . '.characters c WHERE r.id = c.account AND r.username = :username');
			$statement->execute(array('username' => $username));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (!$result)
				return array();
			return $result;
		}

		public function get_user_access_logs($username, $days) {
			$statement = database::connection()->prepare('SELECT r2.ip AS ip, r2.loginTime AS time FROM '. DB_REALMD .'.account r, '. DB_REALMD .'.account_logons r2 WHERE r.id = r2.accountId AND r.username = :username AND r2.loginTime > :time AND r2.loginSource = 0 ORDER BY r2.loginTime DESC');
			$statement->execute(array('username' => $username, 'time' => date("Y-m-d H:i:s", time()-60*60*24*$days)));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (!$result)
				return array();
			return $result;
		}
		
		public function add_user($username, $email, $salt, $verifier, $token, $expiration) {
			try {
				database::connection()->beginTransaction();
				$statement = database::connection()->prepare('INSERT INTO '. DB_REALMD .'.account (username, email, s, v, locked, expansion) VALUES (:username, :email, :salt, :verifier, :locked, :expansion)');
				$result = $statement->execute(array('username' => $username, 'email' => $email, 'salt' => $salt, 'verifier' => $verifier, 'locked' => REQUIRE_ACCOUNT_ACTIVATION ? 1 : 0, 'expansion' => 2));
				if (!$result)
					throw new Exception("Error!");
				$id = database::connection()->lastInsertId();
				$statement = database::connection()->prepare('INSERT INTO ' . DB_WEBSITE . '.account (id, nickname, avatar, signature, ip) VALUES (:id, :nickname, :avatar, :signature, :ip)');
				$result2 = $statement->execute(array('id' => $id, 'nickname' => $username, 'avatar' => DEFAULT_AVATAR, 'signature' => DEFAULT_SIGNATURE, 'ip' => $_SERVER['REMOTE_ADDR']));
				if (!$result2)
					throw new Exception("Error!");
				if (REQUIRE_ACCOUNT_ACTIVATION) {
					$statement = database::connection()->prepare('REPLACE INTO '. DB_WEBSITE .'.account_tokens (id, type, token, expiration) VALUES (:id, :type, :token, :expiration)');
					$result3 = $statement->execute(array('id' => $id, 'type' => 0, 'token' => $token, 'expiration' => date("Y-m-d H-i-s", $expiration)));
					if (!$result3)
						throw new Exception("Error!");
				}
				database::connection()->commit();
				return true;
			} catch(Exception $e) {
				database::connection()->rollBack();
				return false;
			}
		}

		public function expire_activation_token($token) {
			if (REQUIRE_ACCOUNT_ACTIVATION) {
				$statement = database::connection()->prepare('UPDATE '. DB_WEBSITE .'.account_tokens SET expiration = :expiration WHERE type = :type AND token = :token');
				$statement->execute(array('type' => 0, 'token' => $token, 'expiration' => date("Y-m-d H-i-s", floor(microtime(true) - 60))));
			}
		}
		
		public function set_user_nickname($username, $nickname) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w SET w.nickname = :nickname WHERE r.id = w.id AND r.username = :username');
			return $statement->execute(array('nickname' => $nickname, 'username' => $username));
		}
		
		public function set_user_avatar($username, $avatar) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w SET w.avatar = :avatar WHERE r.id = w.id AND r.username = :username');
			return $statement->execute(array('avatar' => $avatar, 'username' => $username));
		}
		
		public function set_user_signature($username, $signature) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w SET w.signature = :signature WHERE r.id = w.id AND r.username = :username');
			return $statement->execute(array('signature' => $signature, 'username' => $username));
		}
		
		public function set_user_password($username, $salt, $verifier) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account SET s = :salt, v = :verifier WHERE username = :username');
			return $statement->execute(array('salt' => $salt, 'verifier' => $verifier, 'username' => $username));
		}
		
		public function set_user_email($id, $email) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account SET email = :email WHERE id = :id');
			return $statement->execute(array('email' => $email, 'id' => $id));
		}

		public function get_jtw_data($username) {
			$statement = database::connection()->prepare('SELECT r.id AS id, r.username AS username, w.ip AS ip FROM '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w WHERE r.id = w.id AND r.username = :username');
			$statement->execute(array('username' => $username));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return array(null, null, null);
			return array($result['id'], $result['username'], $result['ip']);
		}

		public function get_realmlist() {
			$statement = database::connection()->prepare('SELECT id, name, realmbuilds AS builds FROM ' . DB_REALMD . '.realmlist ORDER BY id ASC');
			$statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (!$result)
				return array();
			return $result;
        }

		// Account MFA
		public function get_mfa($username) {
			$statement = database::connection()->prepare('SELECT r.token AS token, w.tokens AS tokens FROM '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w  WHERE r.id = w.id AND r.username = :username');
			$statement->execute(array('username' => $username));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return array(null, null);
			return array($result['token'], explode(',', $result['tokens']));
		}

		public function add_mfa($username, $token, $tokens) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w SET r.token = :token, w.tokens = :tokens WHERE r.id = w.id AND r.username = :username');
			return $statement->execute(array('token' => $token, 'tokens' => $tokens, 'username' => $username));
		}

		public function remove_mfa($username) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account r, '. DB_WEBSITE .'.account w SET r.token = NULL, w.tokens = NULL WHERE r.id = w.id AND r.username = :username');
			return $statement->execute(array('username' => $username));
		}

		// Account recovery
		public function get_recovery($token) {
			$statement = database::connection()->prepare('SELECT w.id AS id, r.username AS username, w.expiration AS expiration, r.token AS token, w2.tokens AS tokens FROM ' . DB_REALMD . '.account r, '. DB_WEBSITE .'.account_tokens w,  '. DB_WEBSITE .'.account w2 WHERE w.type = :type AND r.id = w.id AND r.id = w2.id AND w.token = :token');
			$statement->execute(array('type' => 1, 'token' => $token));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return array(null, null, null, null, null);
			return array($result['id'], $result['username'], strtotime($result['expiration']), $result['token'], explode(',', $result['tokens']));
		}

		public function add_recovery_token($id, $token, $expiration) {
			$statement = database::connection()->prepare('REPLACE INTO '. DB_WEBSITE .'.account_tokens (id, type, token, expiration) VALUES (:id, :type, :token, :expiration)');
			return $statement->execute(array('id' => $id, 'type' => 1, 'token' => $token, 'expiration' => date("Y-m-d H-i-s", $expiration)));
		}

		public function remove_recovery_token($id) {
			$statement = database::connection()->prepare('DELETE FROM '. DB_WEBSITE .'.account_tokens WHERE type = :type AND id = :id');
			return $statement->execute(array('type' => 1, 'id' => $id));
		}

		public function update_password($id, $salt, $verifier) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account SET s = :salt, v = :verifier WHERE id = :id');
			return $statement->execute(array('id' => $id, 'salt' => $salt, 'verifier' => $verifier));
		}

		// Account activation
		public function get_activation($token) {
			$statement = database::connection()->prepare('SELECT w.id AS id, r.username AS username FROM ' . DB_REALMD . '.account r LEFT JOIN '. DB_WEBSITE .'.account_tokens w ON r.id = w.id WHERE w.type = :type AND w.token = :token AND r.locked = :locked');
			$statement->execute(array('token' => $token, 'type' => 0, 'locked' => 1));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return array(null, null);
			return array($result['id'], $result['username']);
		}

		public function remove_activation_token($id) {
			$statement = database::connection()->prepare('DELETE FROM '. DB_WEBSITE .'.account_tokens WHERE id = :id AND type = :type');
			return $statement->execute(array('id' => $id, 'type' => 0));
		}

		public function activate_user($id) {
			$statement = database::connection()->prepare('UPDATE '. DB_REALMD .'.account SET locked = :locked WHERE id = :id');
			return $statement->execute(array('locked' => '0', 'id' => $id));
		}

		// Account email validation
		public function get_validation($token) {
			$statement = database::connection()->prepare('SELECT w.id AS id, r.username AS username, w.type AS type, w.token AS token, w.expiration AS expiration, w.flags AS flags FROM ' . DB_REALMD . '.account r, '. DB_WEBSITE .'.account_tokens w WHERE w.type IN(2, 3) AND r.id = w.id AND w.token = :token');
			$statement->execute(array('token' => $token));
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			if (!$result)
				return array(null, null, null, null, null, null);
			return array($result['id'], $result['username'], $result['type'], $result['token'], strtotime($result['expiration']), $result['flags']);
		}

		public function add_validation_token($id, $token, $type, $expiration, $flags) {
			$statement = database::connection()->prepare('REPLACE INTO '. DB_WEBSITE .'.account_tokens (id, type, token, expiration, flags) VALUES (:id, :type, :token, :expiration, :flags)');
			return $statement->execute(array('id' => $id, 'type' => $type, 'token' => $token, 'expiration' => date("Y-m-d H-i-s", $expiration), 'flags' => $flags));
		}

		public function remove_validation_token($id, $type) {
			$statement = database::connection()->prepare('DELETE FROM '. DB_WEBSITE .'.account_tokens WHERE id = :id AND type = :type');
			return $statement->execute(array('id' => $id, 'type' => $type));
		}
    }
?>