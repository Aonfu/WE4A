-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : lun. 06 avr. 2026 à 11:33
-- Version du serveur : 8.0.45
-- Version de PHP : 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `WE4A_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `enchere_participants`
--

CREATE TABLE `enchere_participants` (
  `id_user` int NOT NULL,
  `id_enchere` int NOT NULL,
  `montant` int NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `enchere_participants`
--
ALTER TABLE `enchere_participants`
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_enchere` (`id_enchere`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `enchere_participants`
--
ALTER TABLE `enchere_participants`
  ADD CONSTRAINT `enchere_participants_ibfk_1` FOREIGN KEY (`id_enchere`) REFERENCES `enchere` (`id_enchere`),
  ADD CONSTRAINT `enchere_participants_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`utilisateur_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
