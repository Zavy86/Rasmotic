--
-- phpMyAdmin SQL Dump
--

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `thermostat`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `setting` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting`, `value`) VALUES
('heating_status', 'off'),
('heating_modality', 'auto'),
('heating_manual_started', NULL),
('heating_absent_temperature', '15.0'),
('heating_manual_temperature', '22.0'),
('heating_manual_timeout', '10800'),
('heating_absent_temperature', '14.0'),
('system_language', 'en'),
('system_passcode', '5f4dcc3b5aa765d61d8327deb882cf99');


-- --------------------------------------------------------

--
-- Table structure for table `heating_modalities`
--

CREATE TABLE IF NOT EXISTS `heating_modalities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `temperature` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `heating_modalities`
--

INSERT INTO `heating_modalities` (`id`, `name`, `color`, `temperature`) VALUES
(1, 'Economic', '#5BC0DE', 15),
(2, 'Night', '#337AB7', 18),
(3, 'Comfort', '#5CB85C', 22);

-- --------------------------------------------------------

--
-- Table structure for table `heating_plannings`
--

CREATE TABLE IF NOT EXISTS `heating_plannings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `day` tinyint(1) unsigned NOT NULL,
  `hour_start` time NOT NULL,
  `hour_end` time NOT NULL,
  `modality_fk` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `day` (`day`),
  KEY `modality_fk` (`modality_fk`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Constraints for table `heating_plannings`
--
ALTER TABLE `heating_plannings`
  ADD CONSTRAINT `heating_plannings_ibfk_1` FOREIGN KEY (`modality_fk`) REFERENCES `heating_modalities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Dumping data for table `heating_plannings`
--

INSERT INTO `heating_plannings` (`id`, `day`, `hour_start`, `hour_end`, `modality_fk`) VALUES
(NULL, 1, '00:00:00', '23:59:59', NULL),
(NULL, 2, '00:00:00', '23:59:59', NULL),
(NULL, 3, '00:00:00', '23:59:59', NULL),
(NULL, 4, '00:00:00', '23:59:59', NULL),
(NULL, 5, '00:00:00', '23:59:59', NULL),
(NULL, 6, '00:00:00', '23:59:59', NULL),
(NULL, 7, '00:00:00', '23:59:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detections`
--

CREATE TABLE IF NOT EXISTS `detections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `typology` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`datetime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------