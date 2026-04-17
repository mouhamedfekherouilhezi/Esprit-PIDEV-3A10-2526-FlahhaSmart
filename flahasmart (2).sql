-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  jeu. 05 mars 2026 à 14:48
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `flahasmart`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id_article` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `categorie` varchar(100) DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `poids` double DEFAULT NULL,
  `unite` varchar(20) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id_article`, `nom`, `description`, `categorie`, `prix`, `stock`, `poids`, `unite`, `image_url`, `date_ajout`, `id_user`) VALUES
(11, 'house', 'bla bla bla', 'housing', '0.10', 10, 1.1, 'kg', 'https://img1.jpg', '2026-02-18 16:21:50', 1),
(12, 'nour', 'Un etudiant d\'esprit', 'person', '8.00', 55, 50, 'kg', 'https://img2.jpg', '2026-02-19 10:21:22', 1),
(13, 'chein', 'C\'est un animal beau!', 'animaux', '620.00', 150, 22, 'kg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSB9zZVgbow8_BpgJpmavj1qYHiRs2okDU72A&s', '2026-02-19 11:18:38', 1),
(14, 'un chat', 'un chat d\'une maison !', 'animaux', '77.00', 10, 64, 'pound', 'https://upload.wikimedia.org/wikipedia/en/b/b9/Toad_by_Shigehisa_Nakaue.png', '2026-02-19 11:23:35', 1),
(16, 'chein', 'dans la maison', 'animal', '22.00', 33, 100, 'kg', 'https://maison.jpg', '2026-03-01 20:17:40', 1),
(18, 'hello', 'heelllooo', 'hello', '99.00', 33, 100, 'kg', 'https://helo.jpg', '2026-03-03 00:55:32', 1),
(20, 'bourr', 'bourrbourr', 'bourr', '66.00', 12, 77, 'kg', 'https://', '2026-03-04 21:27:48', 1),
(21, 'cow', 'cowcow', 'cowcow', '66.00', 88, 120, 'kg', 'https://eee.jpg', '2026-03-04 21:29:29', 1);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `date_commande` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(50) NOT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `adresse_livraison` varchar(255) DEFAULT NULL,
  `montant_total` decimal(10,2) NOT NULL,
  `frais_livraison` decimal(10,2) DEFAULT '0.00',
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `reference`, `date_commande`, `statut`, `mode_paiement`, `adresse_livraison`, `montant_total`, `frais_livraison`, `id_user`) VALUES
(5, 'Cannard', '1993-02-19 23:00:00', 'Aviable', 'en piéce', 'Ein Drahem', '89.00', '10.00', 1),
(6, 'Mouse', '2025-09-09 23:00:00', 'Delivré', 'en ligne', 'A nefza', '100.00', '5.00', 1),
(7, 'Pesticide', '2026-02-01 23:00:00', 'En Cours', 'Carte', 'France , Paris', '150.00', '22.00', 1),
(8, 'bourr', '2020-12-16 23:00:00', 'en delivre', 'carte', 'bizerte', '120.00', '11.00', 1);

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id_commentaire` int(11) NOT NULL,
  `id_thread` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(10) DEFAULT 'actif',
  `sentiment` varchar(10) DEFAULT 'neutre'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `consommation_produit`
--

CREATE TABLE `consommation_produit` (
  `id_produit` int(11) NOT NULL,
  `type_produit` varchar(100) NOT NULL,
  `surface` double NOT NULL,
  `quantite_utilisee` double NOT NULL,
  `unite` varchar(20) NOT NULL,
  `date_recolte` date NOT NULL,
  `date_utilisation` date NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `equipement`
--

CREATE TABLE `equipement` (
  `id_equipement` int(11) NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `etat` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'libre'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipement`
--

INSERT INTO `equipement` (`id_equipement`, `nom`, `type`, `etat`) VALUES
(20, 'Tracteur', 'Transport', 'libre'),
(8, 'Pompe à eau', 'Irrigation', 'réservé'),
(19, 'semoir mécanique 2', 'Machine', 'réservé'),
(18, 'semoir mécanique 1', 'Machine', 'réservé'),
(16, 'pompe a eau 3', 'Irrigation', 'réservé'),
(17, 'pomp a eau 2', 'Irrigation', 'réservé');

-- --------------------------------------------------------

--
-- Structure de la table `jaime`
--

CREATE TABLE `jaime` (
  `id_jaime` int(11) NOT NULL,
  `id_thread` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_jaime` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `jaime`
--

INSERT INTO `jaime` (`id_jaime`, `id_thread`, `id_user`, `date_jaime`) VALUES
(2, 6, 7, '2026-03-05 12:26:41'),
(3, 10, 201, '2026-03-05 13:04:54'),
(4, 9, 201, '2026-03-05 13:04:55'),
(5, 8, 201, '2026-03-05 13:04:59'),
(6, 7, 201, '2026-03-05 13:05:00'),
(7, 6, 201, '2026-03-05 13:05:03');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notif` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `message` varchar(500) NOT NULL,
  `type` varchar(20) NOT NULL,
  `lu` tinyint(1) DEFAULT '0',
  `date_notif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id_notif`, `id_user`, `message`, `type`, `lu`, `date_notif`) VALUES
(1, 7, '❤️  User #201 a aimé votre thread : \"Bad harvest this year\"', 'like', 0, '2026-03-05 12:04:54'),
(2, 7, '❤️  User #201 a aimé votre thread : \"plante\"', 'like', 0, '2026-03-05 12:04:55'),
(3, 7, '❤️  User #201 a aimé votre thread : \"agriculture\"', 'like', 0, '2026-03-05 12:04:59'),
(4, 7, '❤️  User #201 a aimé votre thread : \"manque d\'eau\"', 'like', 0, '2026-03-05 12:05:00'),
(5, 7, '❤️  User #201 a aimé votre thread : \"merci\"', 'like', 0, '2026-03-05 12:05:03');

-- --------------------------------------------------------

--
-- Structure de la table `operation`
--

CREATE TABLE `operation` (
  `id_operation` int(11) NOT NULL,
  `id_equipement` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `type_operation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'en cours'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `operation`
--

INSERT INTO `operation` (`id_operation`, `id_equipement`, `id_user`, `type_operation`, `date_debut`, `date_fin`, `statut`) VALUES
(26, 13, 1, 'Irrigation tomate', '2026-03-25', '2026-03-26', 'en cours'),
(27, 11, 1, 'labour parcelle B', '2026-03-17', '2026-03-18', 'en cours'),
(25, 12, 1, 'Irrigation tomate', '2026-03-12', '2026-03-13', 'en cours'),
(23, 14, 1, 'Semis de blé', '2026-03-10', '2026-03-11', 'en cours'),
(24, 9, 1, 'labour parcelle A', '2026-03-15', '2026-03-16', 'en cours'),
(28, 10, 1, 'test', '2026-04-01', '2026-04-01', 'en cours'),
(29, 8, 7, 'Irrigation de tomate', '2026-03-10', '2026-03-12', 'en cours'),
(30, 17, 7, 'Irrigation de carrot', '2026-03-16', '2026-03-24', 'en cours'),
(31, 16, 7, 'Irrigation de tomate', '2026-03-07', '2026-03-29', 'en cours'),
(32, 18, 7, 'Semis de blé', '2026-03-18', '2026-03-25', 'en cours'),
(33, 19, 7, 'Semis de carrot', '2026-03-07', '2026-03-07', 'en cours');

-- --------------------------------------------------------

--
-- Structure de la table `reputation`
--

CREATE TABLE `reputation` (
  `id_rep` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `points` int(11) DEFAULT '0',
  `badge` varchar(20) DEFAULT '? Débutant'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `reputation`
--

INSERT INTO `reputation` (`id_rep`, `id_user`, `points`, `badge`) VALUES
(1, 1, 0, '🌱 Débutant'),
(2, 4, 0, '🌱 Débutant'),
(3, 6, 0, '🌱 Débutant'),
(4, 5, 0, '🌱 Débutant'),
(5, 7, 30, '🌿 Actif'),
(6, 201, 0, '🌱 Débutant');

-- --------------------------------------------------------

--
-- Structure de la table `stock_produit`
--

CREATE TABLE `stock_produit` (
  `id_produit` int(11) NOT NULL,
  `type_produit` varchar(100) NOT NULL,
  `variete` varchar(100) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin_estimee` date DEFAULT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'en cours',
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `threads`
--

CREATE TABLE `threads` (
  `id_thread` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `statut` varchar(10) DEFAULT 'actif',
  `sentiment` varchar(10) DEFAULT 'neutre',
  `tags` varchar(500) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `threads`
--

INSERT INTO `threads` (`id_thread`, `id_user`, `titre`, `contenu`, `date_creation`, `date_update`, `statut`, `sentiment`, `tags`) VALUES
(6, 7, 'merci', 'faire des salutation a mes amis', '2026-03-05 11:26:28', '2026-03-05 11:29:16', 'actif', 'positif', 'agriculture, conseil'),
(7, 7, 'manque d\'eau', 'manque d\'eau dans beja', '2026-03-05 11:27:55', '2026-03-05 11:28:56', 'actif', 'neutre', 'conseil'),
(8, 7, 'agriculture', 'une voiture noir', '2026-03-05 11:29:42', '2026-03-05 11:29:42', 'actif', 'neutre', 'conseil'),
(9, 7, 'plante', 'plante noir dans la jardin!!', '2026-03-05 11:30:03', '2026-03-05 11:30:03', 'actif', 'neutre', 'agriculture'),
(10, 7, 'Bad harvest this year', 'This year was terrible, all my crops died, horrible drought destroyed everything, worst season ever', '2026-03-05 11:48:52', '2026-03-05 11:49:57', 'actif', 'negatif', 'agriculture, ble');

-- --------------------------------------------------------

--
-- Structure de la table `todo`
--

CREATE TABLE `todo` (
  `NomTache` varchar(255) NOT NULL,
  `Tache` varchar(255) NOT NULL,
  `Statut` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `todo`
--

INSERT INTO `todo` (`NomTache`, `Tache`, `Statut`) VALUES
('ahmed', 'doinggggg', 'DOING'),
('name', 'gaming', 'TODO'),
('num', 'eae', 'DONE'),
('Projet', 'Agriculture', 'DONE');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `role` enum('ADMINISTRATEUR','AGRICULTEUR','CLIENT') NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `nom`, `prenom`, `email`, `password`, `telephone`, `adresse`, `ville`, `photo_profil`, `role`, `actif`, `date_creation`) VALUES
(1, 'Farmer', 'Sample', 'farmer@example.com', 'password123456', NULL, NULL, NULL, NULL, 'ADMINISTRATEUR', 1, '2026-02-16 12:17:46'),
(2, 'nour', 'khediri', 'nour@gmail.com', '$2a$12$ZD.WD8gIb6ZtKQEcyQWZUec7kAjWsJKXPL69JP..nRV5GS/BtttD.', '88500700', 'Avenue Mahmoud El Matri, El Menzah 6, Gouvernorat Ariana, Tunisie', 'Gouvernorat Ariana', NULL, 'CLIENT', 1, '2026-03-04 21:07:42'),
(3, 'nour', 'khediiri', 'nourkhediri516@gmail.com', '$2a$12$di7OqCMdxgkYc4dVMKG05u4FyyODo8N.D/nyKVMx2yee7HiNB//BC', '80500700', 'Planinarska staza 50, Ville de Zagreb, Croatie', 'Ville de Zagreb', NULL, 'CLIENT', 1, '2026-03-04 21:10:47'),
(4, 'nour', 'khedirio', 'nourkhediri@516gmail.com', '$2a$12$LqEVmH1mBZt5w60BSve8Ru3al2ngS.THzqpziCOTXCewazwTT6.iC', '50800700', 'Rue El Imam Ibn Arafa, Tunis, Gouvernorat Tunis, Tunisie', 'Gouvernorat Tunis', NULL, 'CLIENT', 1, '2026-03-04 22:58:12'),
(5, 'nouri', 'nouri', 'nouri@gmail.com', '$2a$12$8H.MmVx413rGzp0fHod5beQT2K/q6901HfgpKg31zlVF5g6E13Kfq', '50400700', 'Ennasr 2, Gouvernorat Ariana, Tunisie', 'Gouvernorat Ariana', NULL, 'AGRICULTEUR', 1, '2026-03-05 04:31:25'),
(6, 'biscit', 'kiki', 'aaa@gmail.com', '$2a$12$CrVkKAAtfP7uhcscFyYzaO.w0FnA4cYYUb9orWGsh8p//0ZEc2UyG', '80700400', 'Avenue Fattouma Bourguiba, Soukra, Gouvernorat Ariana, Tunisie', 'Gouvernorat Ariana', NULL, 'AGRICULTEUR', 1, '2026-03-05 04:32:43'),
(7, 'nadim', 'ben ahmed', 'nadim@gmail.com', '$2a$12$XSUpq1nCjLQJF..0UiJcReEEJvgPpRVfJlqP.GAoj1zTr6KzzWISa', '80700600', 'El Mabtouh, Gouvernorat Bizerte, Tunisie', 'Gouvernorat Bizerte', NULL, 'AGRICULTEUR', 1, '2026-03-05 09:02:33'),
(100, 'mahmoudi', 'lakhder', 'mahmoudi@gmail.com', 'mahmoudi123', '80700500', 'bizete', 'tunis', NULL, 'ADMINISTRATEUR', 1, '2026-03-05 11:54:45'),
(200, 'nihdal', 'ahmer', 'nidhal@gmail.com', '$2a$12$ZD.WD8gIb6ZtKQEcyQWZUec7kAjWsJKXPL69JP..nRV5GS/BtttD.', '50700602', 'ariana', 'tunis', NULL, 'ADMINISTRATEUR', 1, '2026-03-05 11:56:42'),
(201, 'el nour', 'lakhder', 'nour1@gmail.com', '$2a$12$OPpy0AvHPpDxvkPuTuSwAOo0ppDp.b6eDSLPJmFFFbAqwUN8aaaOa', '80456210', 'Chaouet, Gouvernorat La Manouba, Tunisie', 'Gouvernorat La Manouba', NULL, 'AGRICULTEUR', 1, '2026-03-05 11:04:03');

-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE `votes` (
  `id_vote` int(11) NOT NULL,
  `id_thread` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `type_vote` varchar(5) NOT NULL,
  `date_vote` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `votes`
--

INSERT INTO `votes` (`id_vote`, `id_thread`, `id_user`, `type_vote`, `date_vote`) VALUES
(5, 6, 7, 'down', '2026-03-05 11:26:43'),
(8, 9, 7, 'up', '2026-03-05 11:30:09'),
(9, 8, 7, 'down', '2026-03-05 11:30:10');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `fk_produit_agriculteur` (`id_user`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `fk_commande_client` (`id_user`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id_commentaire`),
  ADD KEY `fk_comment_thread` (`id_thread`),
  ADD KEY `fk_comment_user` (`id_user`);

--
-- Index pour la table `consommation_produit`
--
ALTER TABLE `consommation_produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD KEY `fk_consommation_user` (`id_user`);

--
-- Index pour la table `equipement`
--
ALTER TABLE `equipement`
  ADD PRIMARY KEY (`id_equipement`);

--
-- Index pour la table `jaime`
--
ALTER TABLE `jaime`
  ADD PRIMARY KEY (`id_jaime`),
  ADD UNIQUE KEY `unique_jaime` (`id_thread`,`id_user`),
  ADD KEY `idx_thread` (`id_thread`),
  ADD KEY `idx_user` (`id_user`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notif`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `operation`
--
ALTER TABLE `operation`
  ADD PRIMARY KEY (`id_operation`),
  ADD KEY `fk_operation_equipement` (`id_equipement`);

--
-- Index pour la table `reputation`
--
ALTER TABLE `reputation`
  ADD PRIMARY KEY (`id_rep`),
  ADD UNIQUE KEY `unique_user` (`id_user`);

--
-- Index pour la table `stock_produit`
--
ALTER TABLE `stock_produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD KEY `fk_stock_utilisateur` (`id_user`);

--
-- Index pour la table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id_thread`),
  ADD KEY `fk_thread_user` (`id_user`);

--
-- Index pour la table `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`NomTache`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id_vote`),
  ADD UNIQUE KEY `unique_vote` (`id_thread`,`id_user`),
  ADD KEY `votes_ibfk_2` (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id_article` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id_commentaire` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `consommation_produit`
--
ALTER TABLE `consommation_produit`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `equipement`
--
ALTER TABLE `equipement`
  MODIFY `id_equipement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT pour la table `jaime`
--
ALTER TABLE `jaime`
  MODIFY `id_jaime` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `operation`
--
ALTER TABLE `operation`
  MODIFY `id_operation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT pour la table `reputation`
--
ALTER TABLE `reputation`
  MODIFY `id_rep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `stock_produit`
--
ALTER TABLE `stock_produit`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `threads`
--
ALTER TABLE `threads`
  MODIFY `id_thread` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;
--
-- AUTO_INCREMENT pour la table `votes`
--
ALTER TABLE `votes`
  MODIFY `id_vote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_produit_agriculteur` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_commande_client` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `fk_comment_thread` FOREIGN KEY (`id_thread`) REFERENCES `threads` (`id_thread`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `consommation_produit`
--
ALTER TABLE `consommation_produit`
  ADD CONSTRAINT `fk_consommation_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `jaime`
--
ALTER TABLE `jaime`
  ADD CONSTRAINT `jaime_ibfk_1` FOREIGN KEY (`id_thread`) REFERENCES `threads` (`id_thread`) ON DELETE CASCADE,
  ADD CONSTRAINT `jaime_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reputation`
--
ALTER TABLE `reputation`
  ADD CONSTRAINT `reputation_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `stock_produit`
--
ALTER TABLE `stock_produit`
  ADD CONSTRAINT `fk_stock_utilisateur` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `fk_thread_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`id_thread`) REFERENCES `threads` (`id_thread`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
