<?
    class news_model extends model {
        public function get_latest_news() {
			$statement = database::connection()->prepare('SELECT n.id, n.time, COALESCE(n.author, -1) AS author, n.title, n.message, COALESCE(a.nickname, \'Unknown\') AS nickname, COALESCE(a.avatar, \'' . DEFAULT_AVATAR . '\') AS avatar FROM '. DB_WEBSITE .'.news AS n LEFT JOIN '. DB_WEBSITE .'.account AS a ON n.author = a.id WHERE time > :time OR pinned = 1 ORDER BY n.time DESC');
			$statement->execute(array('time' => time() - 60 * 60 * 24 * NEWS_ARCHIVE_AFTER_DAYS));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (!$result)
				return array();
			return $result;
        }

        public function get_archived_news() {
			$statement = database::connection()->prepare('SELECT n.id, n.time, COALESCE(n.author, -1) AS author, n.title, n.message, COALESCE(a.nickname, \'Unknown\') AS nickname, COALESCE(a.avatar, \'' . DEFAULT_AVATAR . '\') AS avatar FROM '. DB_WEBSITE .'.news AS n LEFT JOIN '. DB_WEBSITE .'.account AS a ON n.author = a.id WHERE time <= :time AND pinned = 0 ORDER BY n.time DESC');
			$statement->execute(array('time' => time() - 60 * 60 * 24 * NEWS_ARCHIVE_AFTER_DAYS));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			if (!$result)
				return array();
			return $result;
        }
    }
?>