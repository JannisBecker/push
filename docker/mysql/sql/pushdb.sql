SET NAMES utf8;
SET time_zone = '+00:00';

CREATE DATABASE `push` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `push`;

DROP TABLE IF EXISTS `preferences`;
CREATE TABLE `preferences` (
  `user_id` int(11) NOT NULL,
  `tilesize` tinyint(1) NOT NULL DEFAULT '0',
  `tiles` tinyint(1) NOT NULL DEFAULT '50',
  `navbar` tinyint(1) NOT NULL DEFAULT '0',
  `listview` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `initial` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2017-12-04 11:40:56
