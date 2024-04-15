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
