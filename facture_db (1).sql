-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 01 déc. 2025 à 13:51
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `facture_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `agents`
--

CREATE TABLE `agents` (
  `id_agent` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `numero_interne` varchar(20) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `nom_utilisateur` varchar(255) NOT NULL,
  `motdepasse` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agents`
--

INSERT INTO `agents` (`id_agent`, `nom`, `prenom`, `numero_interne`, `telephone`, `nom_utilisateur`, `motdepasse`) VALUES
(1, 'Zakaria', 'Rayanne', '', '', 'Rayanne', 'passer');

-- --------------------------------------------------------

--
-- Structure de la table `appels`
--

CREATE TABLE `appels` (
  `id_appel` int(11) NOT NULL,
  `numero_appelant` varchar(20) NOT NULL,
  `numero_recu` varchar(20) NOT NULL,
  `id_agent` int(11) NOT NULL,
  `facture_type` varchar(20) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut_paiement` varchar(10) NOT NULL,
  `repondu` varchar(5) NOT NULL,
  `duree` int(11) NOT NULL,
  `date_appel` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp(),
  `solde` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `telephone`, `email`, `date_inscription`, `solde`) VALUES
(1, 'Zakaria ', 'Rayanne', '774574365', '', '2025-11-30 16:10:50', 87000.00);

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

CREATE TABLE `factures` (
  `id_facture` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `numero_facture` varchar(100) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','payée','échouée','') NOT NULL,
  `date_paiement` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_agent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `factures`
--

INSERT INTO `factures` (`id_facture`, `client_id`, `service_id`, `numero_facture`, `montant`, `statut`, `date_paiement`, `id_agent`) VALUES
(1, 1, 1, 'FA20251130-001', 5000.00, 'payée', '2025-11-30 21:40:06', 0),
(2, 1, 3, 'FA20251130-002', 3000.00, 'payée', '2025-12-01 12:49:29', 1),
(3, 1, 4, 'FA20251130-003', 20000.00, 'échouée', '2025-11-30 23:48:06', 0);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id_paiement` int(11) NOT NULL,
  `id_facture` int(11) NOT NULL,
  `montant_paye` decimal(10,2) NOT NULL,
  `methode` varchar(150) NOT NULL,
  `date_paiement` datetime NOT NULL,
  `agent_nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `id_facture`, `montant_paye`, `methode`, `date_paiement`, `agent_nom`) VALUES
(1, 1, 5000.00, 'Espèces', '2025-12-01 00:32:15', ''),
(2, 1, 5000.00, 'Espèces', '2025-12-01 00:40:06', ''),
(3, 2, 3000.00, 'Espèces', '2025-12-01 15:49:29', '');

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `nom_service` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `service`
--

INSERT INTO `service` (`id`, `nom_service`, `description`, `image`) VALUES
(1, '💡 Paiement Senelec', 'Payez vos factures d’électricité en quelques secondes, facilement et en toute sécurité, sans files d’attente. Votre énergie, toujours sous contrôle.', 'senelec-logo.jpg'),
(2, '⚡ Rechargement Woyofal', 'Achetez rapidement vos unités Woyofal et restez connecté. Recharge instantanée, simple et fiable.', 'woyofal.jpg'),
(3, '💧 Paiement Sen’Eau', 'Réglez vos factures d’eau en un clic. Évitez les retards et profitez d’un service pratique.', 'eau.jpeg'),
(4, '🌐 Paiement Internet (Sonatel)', 'Payez vos abonnements Internet ADSL ou Fibre en toute simplicité. Forfaits et services toujours accessibles, sans vous déplacer.', 'orange.jpg'),
(5, '📺 Canal+', 'Renouvelez votre abonnement TV en toute simplicité. Profitez de vos chaînes préférées sans interruption, en quelques secondes seulement.', 'canal.png');

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `facture_id` int(11) NOT NULL,
  `mode_paiement` varchar(50) NOT NULL,
  `reference_transaction` varchar(150) NOT NULL,
  `statut` enum('succès','échec','en cours','') NOT NULL,
  `date_transaction` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id_agent`);

--
-- Index pour la table `appels`
--
ALTER TABLE `appels`
  ADD PRIMARY KEY (`id_appel`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id_paiement`);

--
-- Index pour la table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agents`
--
ALTER TABLE `agents`
  MODIFY `id_agent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `appels`
--
ALTER TABLE `appels`
  MODIFY `id_appel` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id_paiement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
