-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 26 juin 2023 à 18:55
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `taches_projet`
--

-- --------------------------------------------------------

--
-- Structure de la table `catégorie`
--

CREATE TABLE `catégorie` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `catégorie`
--

INSERT INTO `catégorie` (`id`, `nom`, `description`) VALUES
(1, 'Travail', NULL),
(2, 'Études', NULL),
(3, 'Personnel', NULL),
(4, 'Projet', NULL),
(5, 'Loisirs', NULL),
(6, 'Santé', NULL),
(7, 'Famille', NULL),
(8, 'Finances', NULL),
(9, 'Sport', NULL),
(10, 'Voyage', NULL),
(11, 'Alimentation', NULL),
(12, 'Technologie', NULL),
(13, 'Art', NULL),
(14, 'Événements', NULL),
(15, 'Maison', NULL),
(16, 'Informatique', NULL),
(17, 'Divertissement', NULL),
(18, 'Mode', NULL),
(19, 'Projets personnels', NULL),
(20, 'Marketing', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `priorité`
--

CREATE TABLE `priorité` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `couleur` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `priorité`
--

INSERT INTO `priorité` (`id`, `nom`, `couleur`) VALUES
(1, 'Haute', NULL),
(2, 'Moyenne', NULL),
(3, 'Basse', NULL),
(4, 'Très haute', NULL),
(5, 'Faible', NULL),
(6, 'Urgent', NULL),
(7, 'Très basse', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `date_expiration` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `session`
--

INSERT INTO `session` (`id`, `utilisateur_id`, `token`, `date_expiration`) VALUES
(9, 19, '8833d4bab3f57e4766c19ef6f9bd8befd49985bcd2bbc27c2e8ed69761b9a32c', '2023-06-26');

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

CREATE TABLE `taches` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `id_catégorie` int(11) DEFAULT NULL,
  `id_priorité` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `complétée` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `taches`
--

INSERT INTO `taches` (`id`, `titre`, `description`, `date_echeance`, `id_catégorie`, `id_priorité`, `utilisateur_id`, `complétée`) VALUES
(2, 'jus d\'orange', 'permet l\'amour de la fleure vulve', '2023-06-27', 3, 4, 19, 0),
(3, 'base de donne ', 'Le projet consiste à concevoir et développer un système de gestion de tâches en utilisant les \r\nconcepts du langage SQL. Le système devra permettre aux utilisateurs de créer, modifier,\r\nsupprimer et suivre les tâches, ainsi que de gérer les informations relatives aux priorités, aux \r\ndates d\'échéance et aux catégories.', '2023-06-28', 4, 2, 19, 0),
(4, 'Devoir d\'audiovisuel', 'Le devoir consiste à crée une video avec different effect et animation qu\'en a eu a voir ', '2023-06-29', 2, 2, 19, 0),
(5, 'audio devoir ', 'fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', '2023-06-29', 1, 1, 19, 0);

-- --------------------------------------------------------

--
-- Structure de la table `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `valeur` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `token`
--

INSERT INTO `token` (`id`, `utilisateur_id`, `valeur`, `actif`) VALUES
(19, 19, '8833d4bab3f57e4766c19ef6f9bd8befd49985bcd2bbc27c2e8ed69761b9a32c', 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `date_inscription` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `email`, `mot_de_passe`, `date_inscription`) VALUES
(19, 'tchicaya mackly', 'macklyloickabsolue@gmail.com', '$2y$10$AaYl5PKkNxSuep6RKrFogunmGhIW0C/TjYmCWTNKrmcMbkRhzx.9e', '2023-06-26');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `catégorie`
--
ALTER TABLE `catégorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `priorité`
--
ALTER TABLE `priorité`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `taches`
--
ALTER TABLE `taches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_catégorie` (`id_catégorie`),
  ADD KEY `id_priorité` (`id_priorité`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `catégorie`
--
ALTER TABLE `catégorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `priorité`
--
ALTER TABLE `priorité`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `taches`
--
ALTER TABLE `taches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `taches`
--
ALTER TABLE `taches`
  ADD CONSTRAINT `taches_ibfk_1` FOREIGN KEY (`id_catégorie`) REFERENCES `catégorie` (`id`),
  ADD CONSTRAINT `taches_ibfk_2` FOREIGN KEY (`id_priorité`) REFERENCES `priorité` (`id`),
  ADD CONSTRAINT `taches_ibfk_3` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
