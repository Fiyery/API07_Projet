-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 15 Mai 2014 à 15:59
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `api07_projet`
--
CREATE DATABASE IF NOT EXISTS `api07_projet` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `api07_projet`;

-- --------------------------------------------------------

--
-- Structure de la table `maladie`
--

CREATE TABLE IF NOT EXISTS `maladie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `nom` varchar(30) CHARACTER SET latin1 NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `maladie_ibfk_1` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) CHARACTER SET latin1 NOT NULL,
  `prenom` varchar(30) CHARACTER SET latin1 NOT NULL,
  `name` enum('employee','secretaire','medecin') DEFAULT NULL,
  `civilite` enum('M','Mme','Mlle') DEFAULT NULL,
  `tel` varchar(10) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mdp` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `nom`, `prenom`, `name`, `civilite`, `tel`, `email`, `mdp`) VALUES
(1, 'Chaumin', 'Yoann', 'medecin', 'M', '0625523734', 'yoann.chaumin@gmail.com', '83353d597cbad458989f2b1a5c1fa1f9f665c858'),
(2, 'BURNS', 'Ewan', 'secretaire', 'Mlle', '0670635420', 'connard@etu.utc.fr', '298c92b30b2691c821e98abdcbbbac8a6c9be61f'),
(3, 'Toto', 'Toto', 'employee', 'M', '0000000000', 'toto@utc.fr', '6cf2ca74899b0a8c3239c2ed6732b7286b729cd4'),
(4, 'Tata', 'Tata', 'employee', 'Mme', '0000000000', 'tata@utc.fr', 'a0edece707ef72b06a7f68f3d2e08ee77a87f70a'),
(5, 'Titi', 'Titi', 'employee', 'M', '0000000000', 'titi@utc.fr', '06d4f39f923702690b8b999a04b632fd86d869c5');

-- --------------------------------------------------------

--
-- Structure de la table `user_maladie`
--

CREATE TABLE IF NOT EXISTS `user_maladie` (
  `id_user` int(11) NOT NULL,
  `id_maladie` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`,`id_maladie`,`time`),
  KEY `id_maladie` (`id_maladie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `user_vaccination`
--

CREATE TABLE IF NOT EXISTS `user_vaccination` (
  `id_user` int(11) NOT NULL,
  `id_vaccination` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`,`id_vaccination`,`time`),
  KEY `id_vaccination` (`id_vaccination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `vaccination`
--

CREATE TABLE IF NOT EXISTS `vaccination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `nom` varchar(30) CHARACTER SET latin1 NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vaccination_ibfk_1` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `maladie`
--
ALTER TABLE `maladie`
  ADD CONSTRAINT `maladie_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user_maladie`
--
ALTER TABLE `user_maladie`
  ADD CONSTRAINT `user_maladie_ibfk_2` FOREIGN KEY (`id_maladie`) REFERENCES `maladie` (`id`),
  ADD CONSTRAINT `user_maladie_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user_vaccination`
--
ALTER TABLE `user_vaccination`
  ADD CONSTRAINT `user_vaccination_ibfk_2` FOREIGN KEY (`id_vaccination`) REFERENCES `vaccination` (`id`),
  ADD CONSTRAINT `user_vaccination_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `vaccination`
--
ALTER TABLE `vaccination`
  ADD CONSTRAINT `vaccination_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
