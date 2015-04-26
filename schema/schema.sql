-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 26 Kwi 2015, 16:42
-- Wersja serwera: 5.5.40-0ubuntu0.14.04.1
-- Wersja PHP: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `my_gps_workouts`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sport`
--

CREATE TABLE IF NOT EXISTS `sport` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `color` varchar(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sports_users_idx` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `trackpoint`
--

CREATE TABLE IF NOT EXISTS `trackpoint` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `workout_id` bigint(20) NOT NULL,
  `index` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `lat` decimal(9,6) NOT NULL,
  `lng` decimal(9,6) NOT NULL,
  `altitude_meters` int(11) DEFAULT NULL,
  `heart_rate_bpm` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_trackpoints_workouts1_idx` (`workout_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163574 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `username_canonical` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_canonical` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext NOT NULL,
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `workout`
--

CREATE TABLE IF NOT EXISTS `workout` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `sport_id` int(11) NOT NULL,
  `start_datetime` datetime NOT NULL,
  `total_time_seconds` int(11) NOT NULL,
  `distance_meters` int(11) NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `average_heart_rate_bpm` int(11) DEFAULT NULL,
  `maximum_heart_rate_bpm` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_table1_users1_idx` (`user_id`),
  KEY `fk_table1_sports1_idx` (`sport_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `sport`
--
ALTER TABLE `sport`
  ADD CONSTRAINT `fk_sports_users` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `trackpoint`
--
ALTER TABLE `trackpoint`
  ADD CONSTRAINT `fk_trackpoints_workouts1` FOREIGN KEY (`workout_id`) REFERENCES `workout` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `workout`
--
ALTER TABLE `workout`
  ADD CONSTRAINT `fk_table1_sports1` FOREIGN KEY (`sport_id`) REFERENCES `sport` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_table1_users1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
