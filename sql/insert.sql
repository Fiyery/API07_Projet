USE `tlh_technologies`;

--
-- Contenu de la table `menu`
--
INSERT INTO `menu` (`id`, `name`, `enable`, `content`, `date_creation`, `date_modification`, `order`) VALUES
(1, 'Entreprise', 1, '', '2014-05-13 07:08:59', '2014-05-13 07:08:59', 0),
(2, 'Projets', 1, '', '2014-05-13 07:08:59', '2014-05-13 07:08:59', 1),
(3, 'Développement', 0, '', '2014-05-13 21:29:11', '2014-05-13 21:29:11', 3);

--
-- Contenu de la table `sous_menu`
--
INSERT INTO `sous_menu` (`id`, `name`, `enable`, `content`, `date_creation`, `date_modification`, `order`, `id_menu`) VALUES
(1, 'Historique', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 1),
(2, 'Expertise', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 1),
(3, 'Equipe', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 1),
(4, 'LaPromoDuCoin', 0, '', '2014-05-13 09:17:36', '2014-05-13 09:17:36', 0, 2);