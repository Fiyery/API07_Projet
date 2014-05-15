USE `api07_projet`;

--
-- Contraintes pour la table `vaccination`
--
ALTER TABLE `vaccination`
	DROP FOREIGN KEY `vaccination_ibfk_1`;

--
-- Contraintes pour la table `maladie`
--
ALTER TABLE `maladie`
	DROP FOREIGN KEY `maladie_ibfk_1`;

--
-- La table `user`
--
DROP TABLE IF EXISTS `user`;

--
-- La table `vaccination`
--
DROP TABLE IF EXISTS `vaccination`;

--
-- La table `maladie`
--
DROP TABLE IF EXISTS `maladie`;

--
-- Base de données
--
DROP DATABASE IF EXISTS `api07_projet`;
