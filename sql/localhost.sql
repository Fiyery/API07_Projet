-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 14 Mai 2014 à 14:03
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `tlh_technologies`
--
CREATE DATABASE IF NOT EXISTS `tlh_technologies` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `tlh_technologies`;

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET latin1 NOT NULL,
  `enable` tinyint(1) NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `menu`
--

INSERT INTO `menu` (`id`, `name`, `enable`, `content`, `date_creation`, `date_modification`, `order`) VALUES
(1, 'Entreprise', 1, '', '2014-05-13 07:08:59', '2014-05-13 07:08:59', 0),
(2, 'Projets', 1, '', '2014-05-13 07:08:59', '2014-05-13 07:08:59', 1),
(3, 'Développement', 0, '', '2014-05-13 21:29:11', '2014-05-13 21:29:11', 3);

-- --------------------------------------------------------

--
-- Structure de la table `sous_menu`
--

CREATE TABLE IF NOT EXISTS `sous_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET latin1 NOT NULL,
  `enable` tinyint(1) NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_menu` (`id_menu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `sous_menu`
--

INSERT INTO `sous_menu` (`id`, `name`, `enable`, `content`, `date_creation`, `date_modification`, `order`, `id_menu`) VALUES
(1, 'Historique', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 1),
(2, 'Expertise', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 1),
(3, 'Equipe', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 1),
(4, 'LaPromoDuCoin', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 2);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `sous_menu`
--
ALTER TABLE `sous_menu`
  ADD CONSTRAINT `sous_menu_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
