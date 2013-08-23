-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 23, 2013 at 12:52 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `track_executor`
--

CREATE TABLE IF NOT EXISTS `track_executor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `blockedIP` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `codeToRun` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `track_labels`
--

CREATE TABLE IF NOT EXISTS `track_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `ipAddress` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `label` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `color` char(6) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `track_sites`
--

CREATE TABLE IF NOT EXISTS `track_sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `siteName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `track_users`
--

CREATE TABLE IF NOT EXISTS `track_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `salt` varchar(256) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `activated` int(1) NOT NULL DEFAULT '1',
  `activationKey` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `track_visitList`
--

CREATE TABLE IF NOT EXISTS `track_visitList` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `siteID` int(11) NOT NULL,
  `ipAddress` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `browser` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `operatingSystem` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `unixTime` int(10) NOT NULL,
  `ipLocation` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `ISP` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `referrer` text COLLATE utf8_unicode_ci NOT NULL,
  `currentPage` text COLLATE utf8_unicode_ci NOT NULL,
  `screenH` int(11) NOT NULL,
  `screenW` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5030 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
