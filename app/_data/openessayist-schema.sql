--
-- DO NOT DELETE! Used by `app/cli.php` --
--
-- OpenEssayist MySQL database schema.
--
-- @copyright © 2013-2018 The Open University (IET-OU).
-- @link https://github.com/IET-OU/openEssayist-slim
--


-- MySQL dump 10.13  Distrib 5.7.13, for osx10.11 (x86_64)
--
-- Host: localhost    Database: openessayist
-- ------------------------------------------------------
-- Server version	5.7.13

/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `draft`
--

-- DROP TABLE IF EXISTS `draft`;
CREATE TABLE `draft` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `type` int(11) DEFAULT '0',
  `processed` int(11) DEFAULT '1',
  `version` int(11) NOT NULL DEFAULT '1',
  `name` varchar(120) DEFAULT NULL,
  `analysis` longblob COMMENT 'Large JSON object!',
  `text` MEDIUMTEXT COMMENT 'Added. The original draft text.',
  `tstart` int(11) DEFAULT NULL COMMENT 'Added. Time-start.',
  `tend`   int(11) DEFAULT NULL COMMENT 'Added. Time-end.',
  `date` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation date. Changed "DEFAULT".',
  `modified` DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'Add a modified date.',
  `counts` varchar(160) DEFAULT NULL COMMENT 'Added. JSON from Countable.js',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ALTER TABLE `draft` ADD column `counts` varchar(160) DEFAULT NULL COMMENT 'Added. JSON from Countable.js';

--
-- Table structure for table `feedback`
--

-- DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `referer` varchar(255) DEFAULT NULL,
  `text` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `group`
--

-- DROP TABLE IF EXISTS `group`;
CREATE TABLE `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `code` varchar(120) DEFAULT NULL COMMENT='Must by one of "H810" or "VCS" (relates to Python backend data-files).',
  `url` varchar(255) DEFAULT NULL,
  `email` varchar(220) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `kwcategory`
--

-- DROP TABLE IF EXISTS `kwcategory`;
CREATE TABLE `kwcategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `draft_id` int(11) NOT NULL,
  `category` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `note`
--

-- DROP TABLE IF EXISTS `note`;
CREATE TABLE `note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `notes` longblob,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Add a creation date.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `task`
--

-- DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `code` varchar(120) DEFAULT NULL COMMENT 'Must be one of "TMA01" or "ICS" (relates to Python backend data-files).',
  `assignment` text,
  `deadline` date DEFAULT NULL,
  `wordcount` int(11) DEFAULT '0',
  `isopen` int(11) DEFAULT '0',
  `group_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Added. Creation date.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `users`
--

-- DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) DEFAULT NULL COMMENT 'Reduced from varchar(120).',
  `password` varchar(64) NOT NULL COMMENT 'Reduced from varchar(255).',
  `name` varchar(180) DEFAULT NULL COMMENT 'Full name / display name',
  `email` varchar(120) DEFAULT NULL COMMENT 'Reduced from varchar(220).',
  `ip_address` varchar(16) NOT NULL,
  `group_id` int(11) NOT NULL,
  `active` int(11) DEFAULT '0',
  `isadmin` int(11) DEFAULT '0',
  `isgroup` int(11) DEFAULT '0',
  `isdemo` int(11) DEFAULT '0',
  `auth_type` varchar(32) DEFAULT NULL COMMENT 'Added. Examples, "ou-sams" ...',
  `visit_count` int(11) DEFAULT '0' COMMENT 'Added. Track the number of successful logins.',
  `last_visit` datetime DEFAULT NULL COMMENT 'Added. Last visit date-time.',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Add a creation date.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- SHOW CREATE TABLE `users`;
-- ALTER TABLE `users` ADD column `visit_count` int(11) DEFAULT '0' COMMENT 'Added. Track the number of successful logins.';

-- Dump completed on 2018-01-11 14:28:07

-- DO NOT DELETE! Used by `app/cli.php` --
-- End. --
