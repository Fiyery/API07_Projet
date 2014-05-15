--
-- Base de données: `tlh_technologies`
--
CREATE DATABASE IF NOT EXISTS `api07_projet` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `api07_projet`;

--
-- Structure de la table `user`
--
CREATE TABLE IF NOT EXISTS `user` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`nom` varchar(30) CHARACTER SET latin1 NOT NULL,
	`prenom` varchar(30) CHARACTER SET latin1 NOT NULL,
	`name` ENUM('employee', 'secretaire', 'medecin'),
	`civilite` ENUM('M', 'Mme', 'Mlle'),
	`tel` varchar(10),
	`email` varchar(100),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Structure de la table `vaccination`
--
CREATE TABLE IF NOT EXISTS `vaccination` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user` int(11) NOT NULL,
	`nom` varchar(30) CHARACTER SET latin1 NOT NULL,
	`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Contraintes pour la table `vaccination`
--
ALTER TABLE `vaccination`
	ADD CONSTRAINT `vaccination_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Structure de la table `maladie`
--
CREATE TABLE IF NOT EXISTS `maladie` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user` int(11) NOT NULL,
	`nom` varchar(30) CHARACTER SET latin1 NOT NULL,
	`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Contraintes pour la table `maladie`
--
ALTER TABLE `maladie`
	ADD CONSTRAINT `maladie_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);