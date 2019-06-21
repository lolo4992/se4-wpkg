-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Ven 21 Juin 2019 à 17:07
-- Version du serveur :  5.5.59-0+deb7u1
-- Version de PHP :  5.4.45-0+deb7u14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `se3wpkg`
--

--
-- Contenu de la table `mise_en_forme`
--

INSERT INTO `mise_en_forme` (`id_mef`, `label_mef`, `value_mef`) VALUES
(1, 'warning_bg', 'FF0000'),
(2, 'warning_txt', 'FFFFFF'),
(3, 'warning_lnk', 'FFFF00'),
(4, 'error_bg', 'FFFF00'),
(5, 'error_txt', '000000'),
(6, 'error_lnk', '415594'),
(7, 'ok_bg', '00FF00'),
(8, 'ok_txt', '000000'),
(9, 'ok_lnk', '415594'),
(10, 'unknown_bg', 'FFFFFF'),
(11, 'unknown_txt', '000000'),
(12, 'unknown_lnk', '415594'),
(13, 'regular_lnk', '0080ff'),
(14, 'wintype_txt', 'FFF8DC'),
(15, 'dep_entite_bg', '0000FF'),
(16, 'dep_entite_txt', 'FFFFFF'),
(17, 'dep_entite_lnk', 'FF0000'),
(18, 'dep_parc_bg', '0080FF'),
(19, 'dep_parc_txt', '000000'),
(20, 'dep_parc_lnk', 'FF0000'),
(21, 'dep_depend_bg', '00FFFF'),
(22, 'dep_depend_txt', '000000'),
(23, 'dep_depend_lnk', 'FF0000'),
(24, 'dep_no_bg', 'FFFFFF'),
(25, 'dep_no_txt', '000000'),
(26, 'dep_no_lnk', 'FF0000');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
