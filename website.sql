SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `account`
-- ----------------------------
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` int(11) unsigned NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `avatar` text NOT NULL,
  `signature` text NOT NULL,
  `ip` varchar(32) NOT NULL,
  `tokens` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `account_tokens`
-- ----------------------------
DROP TABLE IF EXISTS `account_tokens`;
CREATE TABLE `account_tokens` (
  `id` int(11) unsigned NOT NULL,
  `type` int(1) unsigned NOT NULL,
  `token` varchar(32) NOT NULL,
  `expiration` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `flags` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for `news`
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) DEFAULT NULL,
  `author` int(11) DEFAULT NULL,
  `title` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `message` text CHARACTER SET utf8,
  `pinned` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=925770 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', '1688234231', '-1', 'Epic World of Warcraft Raid Boss Revealed: Prepare for the Challenge of a Lifetime', '[b]Gear up for the ultimate raid challenge as the ancient monstrosity, Xerathul the Unforgiving, emerges from the depths of Azeroth.[/b]\n\nGather your allies, study the boss\'s devastating abilities, and form an unbreakable strategy to bring this behemoth down. Will you and your raid team have what it takes to conquer Xerathul and claim the coveted treasures that lie within its lair?\n\n[s]Prepare to face the unforgiving wrath of Xerathul and emerge victorious in the face of insurmountable odds![/s]', '1');
INSERT INTO `news` VALUES ('2', '1678234231', '-1', 'Heartwarming Tale of Friendship Unveiled in World of Warcraft Storyline', '[b]Experience an emotionally charged storyline in World of Warcraft\'s latest update, "Bonds of Brotherhood."[/b]\n\nJoin two unlikely NPCs, Thalra and Gromm, as they overcome their differences and forge an unbreakable bond. Witness their journey as they embark on a perilous adventure, facing trials and tribulations together.\n\n[s]This heartwarming tale will remind us of the power of friendship and unity in the face of adversity.[/s]', '0');