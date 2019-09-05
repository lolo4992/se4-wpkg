-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 05 Septembre 2019 à 10:37
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
-- Contenu de la table `depot`
--

INSERT INTO `depot` (`id_depot`, `url_depot`, `nom_depot`, `depot_actif`, `depot_principal`, `hash_xml`) VALUES
(1, 'http://deb.sambaedu.org/wpkg/xml/packages.xml', 'SambaEdu Officiel', 1, 1, ''),
(2, 'http://deb.sambaedu.org/wpkg/xml/packages_dev.xml', 'SambaEdu Dev', 0, 0, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
