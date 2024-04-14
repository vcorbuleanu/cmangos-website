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
INSERT INTO `news` VALUES ('1', '<TIMESTAMP>', '-1', '<SERVER_NAME> is Now Live! Rediscover Classic Azeroth', 'Welcome back to the classic era of World of Warcraft with [b]<SERVER_NAME>[/b]! Immerse yourself in the original, unaltered Azeroth as it was meant to be experienced.\n\n[b]Key Features:[/b]\n- [b]Authentic Blizzlike Gameplay:[/b] Every aspect of the server replicates the classic 1.12.1 experience.\n- [b]Enhanced by Bots:[/b] To ensure a thriving environment, our server includes bots that simulate a populated world, making it easy to find allies for your adventures.\n\n[b]Get Started:[/b]\n1. [b]Download the 1.12.1 Client:[/b] If you don\'t already have it, download the classic version 1.12.1 client.\n2. [b]Edit Your Realmlist.wtf File:[/b] Navigate to your WoW directory, open the \'realmlist.wtf\' file with a text editor, and replace its contents with: `set realmlist <SERVER_ADDRESS>`.\n3. [b]Create an Account:[/b] Visit <WEBSITE_PUBLIC_URL> to register.\n4. [b]Log In and Play:[/b] Enter the world and start your classic adventure!\n\nJoin us at [b]<SERVER_NAME>[/b] and relive the legendary tales of Azeroth as they were originally told!', '1');
