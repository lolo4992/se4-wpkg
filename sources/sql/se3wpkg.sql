-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Dim 06 Janvier 2019 à 22:27
-- Version du serveur :  5.5.59-0+deb7u1
-- Version de PHP :  5.4.45-0+deb7u13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `se3wpkg`
--

-- --------------------------------------------------------

--
-- Structure de la table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `id_app` int(11) NOT NULL AUTO_INCREMENT,
  `id_nom_app` varchar(255) NOT NULL,
  `nom_app` varchar(255) NOT NULL,
  `version_app` varchar(255) NOT NULL,
  `compatibilite_app` tinyint(4) NOT NULL,
  `categorie_app` varchar(255) NOT NULL,
  `prorite_app` int(11) NOT NULL,
  `reboot_app` tinyint(4) NOT NULL,
  `sha_app` varchar(128) NOT NULL,
  `date_modif_app` datetime NOT NULL,
  `user_modif_app` varchar(255) NOT NULL,
  `active_app` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_app`),
  KEY `id_nom_app` (`id_nom_app`),
  KEY `id_nom_app_2` (`id_nom_app`,`active_app`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=153 ;

-- --------------------------------------------------------

--
-- Structure de la table `dependance`
--

CREATE TABLE IF NOT EXISTS `dependance` (
  `id_dependance` int(11) NOT NULL AUTO_INCREMENT,
  `id_app` int(11) NOT NULL,
  `id_app_requise` int(11) NOT NULL,
  PRIMARY KEY (`id_dependance`),
  UNIQUE KEY `id_app` (`id_app`,`id_app_requise`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=106 ;

-- --------------------------------------------------------

--
-- Structure de la table `depot_applications`
--

CREATE TABLE IF NOT EXISTS `depot_applications` (
  `id_depot_applications` int(11) NOT NULL DEFAULT '0',
  `id_nom_app` varchar(255) NOT NULL,
  `nom_app` varchar(255) NOT NULL,
  `xml` varchar(255) NOT NULL,
  `url_xml` varchar(255) NOT NULL,
  `sha_xml` varchar(128) NOT NULL,
  `url_log` varchar(255) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `compatibilite` tinyint(4) NOT NULL,
  `version` varchar(255) NOT NULL,
  `branche` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `id_depot` int(11) NOT NULL,
  PRIMARY KEY (`id_depot_applications`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `journal_app`
--

CREATE TABLE IF NOT EXISTS `journal_app` (
  `id_journal_app` int(11) NOT NULL AUTO_INCREMENT,
  `id_app` int(11) NOT NULL,
  `operation_journal_app` varchar(3) NOT NULL,
  `user_journal_app` varchar(255) NOT NULL,
  `date_journal_app` datetime NOT NULL,
  `xml_journal_app` varchar(255) NOT NULL,
  `sha_journal_app` varchar(128) NOT NULL,
  PRIMARY KEY (`id_journal_app`),
  KEY `id_app` (`id_app`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=430 ;

-- --------------------------------------------------------

--
-- Structure de la table `postes`
--

CREATE TABLE IF NOT EXISTS `postes` (
  `id_poste` int(11) NOT NULL AUTO_INCREMENT,
  `nom_poste` varchar(255) NOT NULL,
  `OS_poste` varchar(20) NOT NULL,
  `date_rapport_poste` datetime NOT NULL,
  `ip_poste` varchar(15) NOT NULL,
  `mac_address_poste` varchar(17) NOT NULL,
  `sha_rapport_poste` varchar(128) NOT NULL,
  `file_log_poste` varchar(255) NOT NULL,
  `file_rapport_poste` varchar(255) NOT NULL,
  `date_modification_poste` datetime NOT NULL,
  PRIMARY KEY (`id_poste`),
  UNIQUE KEY `nom_poste` (`nom_poste`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=445 ;

-- --------------------------------------------------------

--
-- Structure de la table `poste_app`
--

CREATE TABLE IF NOT EXISTS `poste_app` (
  `id_poste_rapport` int(11) NOT NULL AUTO_INCREMENT,
  `id_poste` int(11) NOT NULL,
  `id_app` int(11) NOT NULL,
  `id_nom_app` varchar(255) NOT NULL,
  `revision_poste_app` varchar(255) NOT NULL,
  `statut_poste_app` varchar(13) NOT NULL,
  `reboot_poste_app` tinyint(4) NOT NULL,
  PRIMARY KEY (`id_poste_rapport`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70182 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
