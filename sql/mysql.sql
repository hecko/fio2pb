SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE `fio2pb` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `fio2pb`;

CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` float NOT NULL,
  `info` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  UNIQUE KEY `timestamp` (`timestamp`,`info`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
