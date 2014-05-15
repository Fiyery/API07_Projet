--
-- Base de données: `tlh_technologies`
--
CREATE DATABASE IF NOT EXISTS `tlh_technologies` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `tlh_technologies`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contraintes pour la table `sous_menu`
--
ALTER TABLE `sous_menu`
	ADD CONSTRAINT `sous_menu_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id`);