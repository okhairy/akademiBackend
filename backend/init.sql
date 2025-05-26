-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 26 mai 2025 à 04:52
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
-- Base de données : `sagisu`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_vigiles`
--

CREATE TABLE `admin_vigiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `statut` enum('active','bloqué') NOT NULL DEFAULT 'active',
  `role` enum('admin','vigile') NOT NULL,
  `date_de_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `lieu` enum('campus','restaurant') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admin_vigiles`
--

INSERT INTO `admin_vigiles` (`id`, `nom`, `prenom`, `email`, `telephone`, `mot_de_passe`, `statut`, `role`, `date_de_creation`, `lieu`) VALUES
(5, 'Ndiaye', 'Oumoul Khairy', 'oumoulkhairyndiaye1@gmail.com', '756993535', '$2y$12$PpVwt173l6gagTGaj/dY4ONYjGSVdfA0/HC3krPe0PlgHVWZJDF0y', 'active', 'admin', '2025-02-24 12:24:44', NULL),
(6, 'Sow', 'Abou Abdrahmane', 'aabdrahmane.sow@univ-thies.sn', '781691081', '$2y$12$ZzPOP0enw.rnXIvKjtTfL.2WU3EJvvbscjk76IIMuaAJll/eA5wCa', 'active', 'admin', '2025-02-25 12:37:10', NULL),
(7, 'Sow', 'Mouhamed', 'asow19133@gmail.com', '785240028', '$2y$12$TxLebPEGGKWi2WSetdzlvO4s6vghEr3VmHqml.MfQrIR8f/a8aIH2', 'active', 'admin', '2025-02-25 12:47:58', NULL),
(8, 'Toure', 'Tidiane', 'touretige1@gmail.com', '771580718', '$2y$12$pBM953HAF4qdg3aCfPP5Cexqyef9cBh4VRVfeLGxD0DkF56umVCOi', 'active', 'admin', '2025-02-25 13:49:47', NULL),
(9, 'Toure', 'Houleye', 'touret@gmail.com', '+33612457854', '$2y$12$.NBtc60FcuWmlMUEjFk/S.K8qb./2IT2gVfRhfkvsoTeIIjZZbT.O', 'bloqué', 'vigile', '2025-02-25 17:01:15', 'campus'),
(18, 'Sow', 'Awa', 'sow876455@gmail.com', '773299196', '$2y$12$2X4JEoaLUo3DZojeeWhpJuVlIs0m0vAxcpYwExec8Foj.DXSMZH/G', 'active', 'vigile', '2025-04-09 15:58:00', 'campus'),
(19, 'Dia', 'Goundo', 'dia@gmail.com', '752140102', '$2y$12$RLkbl/hl888S824pJ0PYe.WKSUNtiwQ3pTX.Ku.D4xb6JQnRVwJIi', 'active', 'vigile', '2025-05-23 17:43:16', 'campus');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `solde` double NOT NULL DEFAULT 0,
  `telephone` varchar(255) NOT NULL,
  `chambre` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL DEFAULT '$2y$12$pCZnq4c9tuNSSLxR3u9vh.708Td.tLKOHfyCvF6arXlNmeLunwKNC',
  `statut` enum('active','bloqué') NOT NULL DEFAULT 'active',
  `date_de_creation` date NOT NULL DEFAULT current_timestamp(),
  `numero_de_dossier` int(11) NOT NULL,
  `uid_carte` varchar(255) DEFAULT NULL,
  `status_carte` enum('bloqué','débloqué') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `nom`, `prenom`, `email`, `solde`, `telephone`, `chambre`, `photo`, `mot_de_passe`, `statut`, `date_de_creation`, `numero_de_dossier`, `uid_carte`, `status_carte`, `created_at`, `updated_at`) VALUES
(1, 'Ba', 'Binta', 'binta1@gmail.com', 70, '781245514', NULL, NULL, '$2y$12$TYkw/6oOnDMPSlS3O95KB.PZQSTqhYIA2/XZeWTdX5yUg19MsuZIq', 'active', '2025-02-21', 4578, NULL, NULL, '2025-02-21 14:51:09', '2025-05-24 12:30:50'),
(2, 'Ba', 'Binta', 'binta@gmail.com', 2700, '771245514', NULL, NULL, '$2y$12$H2ntaAineKMU/X7/xvGQtusQdhoLYe0u.cNLAR6gWo3Q1jchOHVIG', 'active', '2025-02-21', 2103078, '', '', '2025-02-21 15:03:47', '2025-04-08 14:54:08'),
(5, 'Kane', 'houlimata', 'houli@gmail.com', 0, '772204400', NULL, NULL, '$2y$12$A3EGCkknMflLDC.6A2D7T.5sNkwg8gBgFdCUw1mGknTVA3M35qDfG', 'active', '2025-02-21', 2122027, NULL, NULL, '2025-02-21 15:51:33', '2025-04-08 16:04:30'),
(7, 'Tall', 'Oumar Foutiyou', 'hanafiyahdr@gmail.com', 700, '779244425', 'B2', NULL, '$2y$12$wjW3tKB6oSfEsK2w6/OKnOOGKHE/rz.UUYUcfva8/bnjaLvpfjHrK', 'active', '2025-02-25', 2105465854, '132BC102', 'bloqué', '2025-02-25 13:19:52', '2025-05-24 13:27:58'),
(8, 'Diaw', 'Maty', 'diaw@gmail.com', 0, '761234584', 'A88', NULL, '$2y$12$7TtmP7W4QhRH.evpbbqwWua/tNSN5ikO2AQ6RNQbBPyzNcczcVhfq', 'active', '2025-03-04', 210345721, NULL, NULL, '2025-03-04 16:45:06', '2025-03-04 16:45:06'),
(10, 'Ba', 'Fatou', 'bafatou@gmail.fr', 0, '701051111', 'C69', NULL, '$2y$12$LgwyAh/wjlgIrlNatw.JN./nYUXrFP5/GHt8flXnlBM0.hvvPfW9m', 'active', '2025-05-24', 21030147, NULL, NULL, '2025-05-24 13:00:44', '2025-05-25 12:15:50');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(9, '0001_01_01_000000_create_users_table', 1),
(10, '0001_01_01_000001_create_cache_table', 1),
(11, '0001_01_01_000002_create_jobs_table', 1),
(12, '2025_02_20_134314_create_etudiants_table', 1),
(13, '2025_02_20_135225_create_admin-_vigiles_table', 1),
(14, '2025_02_20_155517_create_transactions_table', 1),
(15, '2025_02_20_164828_add_two_factor_columns_to_users_table', 1),
(16, '2025_02_20_164856_create_personal_access_tokens_table', 1),
(17, '2025_03_01_155227_create_password_resets_table', 2);

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `created_at`, `updated_at`) VALUES
(1, 'binta@gmail.com', 'k4qeMErvegufWyVUdwHimRgHp0CfggrsoYjxsGPG3iTKoP8yO5fSUHEMk4Dv', '2025-03-01 18:53:18', NULL),
(11, 'oumoulkhairyndiaye1@gmail.com', '458SPKXeuAmUEupl5gBUyETT3qUYSI71F2FeMGbxNgoj2gs5raDtZQ08Zq2e', '2025-03-03 11:09:17', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(2, 'App\\Models\\Etudiant', 5, 'authToken', '9984858fd1ec14797f4ed5e3660052ac57367e8ffc6c5500460a8f8018a78ffb', '[\"*\"]', NULL, NULL, '2025-02-21 16:42:05', '2025-02-21 16:42:05'),
(3, 'App\\Models\\Etudiant', 5, 'authToken', '59fb35155422378586f6285dbda0f0c28f170c1c70e079a085a3eced9a74ffc6', '[\"*\"]', NULL, NULL, '2025-02-23 01:32:50', '2025-02-23 01:32:50'),
(7, 'App\\Models\\Etudiant', 5, 'authToken', '974c1b2972e67a7fe06f27e5f913d52e64e39c684ead7db503da14aa91f65936', '[\"*\"]', NULL, NULL, '2025-02-23 10:24:15', '2025-02-23 10:24:15'),
(8, 'App\\Models\\Etudiant', 5, 'authToken', '1961279952ffd45fd570d530db25c9baddf0cac96637fafa6480123a767cb5ad', '[\"*\"]', '2025-02-23 23:15:53', NULL, '2025-02-23 15:02:56', '2025-02-23 23:15:53'),
(9, 'App\\Models\\Etudiant', 5, 'authToken', '1669da36a0be91e9b75fb475fc9b31d081278211077705866c37b770697ef7a8', '[\"*\"]', '2025-02-24 12:24:43', NULL, '2025-02-23 23:16:38', '2025-02-24 12:24:43'),
(11, 'App\\Models\\AdminVigile', 5, 'authToken', 'edaf924cf655d47a4510c8f7f6362efbe56467ecbf8529c52ddec1985066b44a', '[\"*\"]', '2025-02-25 13:19:51', NULL, '2025-02-25 12:32:51', '2025-02-25 13:19:51'),
(13, 'App\\Models\\AdminVigile', 7, 'authToken', '23567ab74b9161a514d54b6a2886351761c5afeb17cf9dd651412073328ee68d', '[\"*\"]', '2025-02-25 17:02:07', NULL, '2025-02-25 13:47:36', '2025-02-25 17:02:07'),
(16, 'App\\Models\\AdminVigile', 8, 'authToken', '9807303d392a2c70e2a375f068b016f5e41c1ec510f6c12c7643f51e4e4ac335', '[\"*\"]', NULL, NULL, '2025-02-25 15:47:42', '2025-02-25 15:47:42'),
(17, 'App\\Models\\AdminVigile', 8, 'authToken', '6b522ed5e6a43e77713bc187d6361bc5e4e8708d59d62ae3cb092706ca8dcd98', '[\"*\"]', '2025-02-25 16:20:24', NULL, '2025-02-25 16:18:55', '2025-02-25 16:20:24'),
(18, 'App\\Models\\Etudiant', 7, 'authToken', '0a1c43becb93622096e65d99a34b80ae50fbd5b194c93276d3ec694a0b30f6f6', '[\"*\"]', NULL, NULL, '2025-02-28 16:15:11', '2025-02-28 16:15:11'),
(19, 'App\\Models\\Etudiant', 7, 'authToken', '2bac3d8ad9f3a600edc59a3fa62ac457b75c6429fb55a8641709040d2f4c3bf7', '[\"*\"]', NULL, NULL, '2025-02-28 16:39:59', '2025-02-28 16:39:59'),
(20, 'App\\Models\\Etudiant', 7, 'authToken', 'edefeef4d22bb13088ebb59593b301c98e1bde4d8d42306001bb739326687603', '[\"*\"]', NULL, NULL, '2025-02-28 17:05:50', '2025-02-28 17:05:50'),
(21, 'App\\Models\\Etudiant', 7, 'authToken', 'b079a9502f15c65070114dda5d6cfa3e78537683b251f6c523375d15f0755cb6', '[\"*\"]', NULL, NULL, '2025-02-28 17:58:35', '2025-02-28 17:58:35'),
(22, 'App\\Models\\AdminVigile', 6, 'authToken', '4f6ae5003e877f4e1edddfac20086166d554865f018bdc7066aaeb97f108a19a', '[\"*\"]', NULL, NULL, '2025-03-01 20:34:48', '2025-03-01 20:34:48'),
(23, 'App\\Models\\AdminVigile', 7, 'authToken', '3ce24e7b72c54fcf3d8c11ecccf09d7a6c950a4313726994092ee59f6ee3fced', '[\"*\"]', NULL, NULL, '2025-03-02 01:15:12', '2025-03-02 01:15:12'),
(24, 'App\\Models\\AdminVigile', 7, 'authToken', '870cbeb986b30981c23538c12bf4bf75ebf0647decd6f1226f8c253d63846f8e', '[\"*\"]', NULL, NULL, '2025-03-02 01:18:01', '2025-03-02 01:18:01'),
(25, 'App\\Models\\AdminVigile', 7, 'authToken', 'b656336ed4d43fc2dce91ab47a5d28a578b8adca1d0e82980849827c64b0d2ae', '[\"*\"]', NULL, NULL, '2025-03-02 01:22:22', '2025-03-02 01:22:22'),
(26, 'App\\Models\\AdminVigile', 6, 'authToken', 'f59e2db3c78a02e2b18406438942c127a38907c8895841df5bcfbcaed0fbb9f5', '[\"*\"]', NULL, NULL, '2025-03-02 20:35:03', '2025-03-02 20:35:03'),
(27, 'App\\Models\\AdminVigile', 7, 'authToken', '795f58f6daad1988560694570831b5c4e484f8a196437f0330ebee50834b840a', '[\"*\"]', NULL, NULL, '2025-03-02 20:36:50', '2025-03-02 20:36:50'),
(28, 'App\\Models\\AdminVigile', 6, 'authToken', 'b326b20976202bae17ddac501b0a4af10f8a836de635ae077c9713e3dff35fd0', '[\"*\"]', NULL, NULL, '2025-03-03 11:50:05', '2025-03-03 11:50:05'),
(29, 'App\\Models\\AdminVigile', 6, 'authToken', '42df21768a80d7d29338c4b280bdb894242e9a1b2a2eb915c49d5c94334d1562', '[\"*\"]', NULL, NULL, '2025-03-03 16:19:33', '2025-03-03 16:19:33'),
(31, 'App\\Models\\AdminVigile', 6, 'authToken', 'c396a4b1406538a8520121a975a2f02d43db062033e4f5440daa2d5566bd7618', '[\"*\"]', NULL, NULL, '2025-03-04 10:59:56', '2025-03-04 10:59:56'),
(33, 'App\\Models\\AdminVigile', 6, 'authToken', '66514f1b3856bee81c85563e549b9023f2d49ff6829b8fe9662564c25e9cbb28', '[\"*\"]', '2025-03-06 14:37:16', NULL, '2025-03-04 16:38:22', '2025-03-06 14:37:16'),
(34, 'App\\Models\\Etudiant', 7, 'authToken', '09cf31d60eca38d1c1e0deb76145322513a537a99d0d13a283c45fadb1e61b7f', '[\"*\"]', '2025-03-07 12:19:16', NULL, '2025-03-06 14:46:39', '2025-03-07 12:19:16'),
(35, 'App\\Models\\Etudiant', 7, 'authToken', 'a5069d496cad8862e0954ab0d784eaec1606cc4cd1487fa6713ea06e26706a87', '[\"*\"]', '2025-03-07 15:22:52', NULL, '2025-03-06 16:17:31', '2025-03-07 15:22:52'),
(37, 'App\\Models\\Etudiant', 7, 'authToken', 'b352906a8994a151b1305aa33ba5fda3dede788ad46b2297603b613ea06f9894', '[\"*\"]', '2025-03-09 15:14:30', NULL, '2025-03-07 13:00:50', '2025-03-09 15:14:30'),
(38, 'App\\Models\\Etudiant', 7, 'authToken', '2a8d037d52c9d70a3726342b9ae60a722f247a890f34997b602cd4885273ae91', '[\"*\"]', '2025-03-07 15:44:15', NULL, '2025-03-07 15:17:33', '2025-03-07 15:44:15'),
(39, 'App\\Models\\Etudiant', 7, 'authToken', 'fba8f2c8f89e094cd4bec88bf9037ce93b31bc29e28cb29aa4a78f0234f1bb5f', '[\"*\"]', '2025-03-09 02:08:53', NULL, '2025-03-07 22:43:07', '2025-03-09 02:08:53'),
(41, 'App\\Models\\Etudiant', 7, 'authToken', 'fb5eb2bf58ceb5bcd8a05b0e5f028ecb2256378648438c412aa9a72da6693a9c', '[\"*\"]', '2025-03-23 01:33:39', NULL, '2025-03-09 13:46:06', '2025-03-23 01:33:39'),
(46, 'App\\Models\\Etudiant', 7, 'authToken', '7fe0617c7adbcd7d14615e97e584b3db776cf4daa07b2cb92fe853e2a353a695', '[\"*\"]', '2025-03-30 01:01:57', NULL, '2025-03-30 00:52:03', '2025-03-30 01:01:57'),
(47, 'App\\Models\\Etudiant', 7, 'authToken', 'ec8772cfe9275abd90205d3b835b97df48bb71f15cd883938cbf914c64b910c9', '[\"*\"]', '2025-04-08 12:57:57', NULL, '2025-03-30 01:02:57', '2025-04-08 12:57:57'),
(48, 'App\\Models\\Etudiant', 7, 'authToken', '6064f9591cfafbda429fa0c296e4a50fb1dcbccb9a829d7602a1f566399cc292', '[\"*\"]', '2025-04-08 12:58:57', NULL, '2025-04-08 12:58:55', '2025-04-08 12:58:57'),
(49, 'App\\Models\\AdminVigile', 7, 'authToken', '2c0039c773da99f50071bc047f6361786dc92c9cfe85d78d4d5b78ff5ccaf07f', '[\"*\"]', '2025-04-08 15:25:11', NULL, '2025-04-08 13:00:10', '2025-04-08 15:25:11'),
(50, 'App\\Models\\AdminVigile', 7, 'authToken', 'f8379a0dbd1d8612a4cb1f5706e90bf1274c15470c12edcb7a53d1b9dd9a5f87', '[\"*\"]', '2025-04-10 13:18:51', NULL, '2025-04-09 11:52:33', '2025-04-10 13:18:51'),
(51, 'App\\Models\\Etudiant', 7, 'authToken', '0d373865c14d42dff31f849b1bbe6987e18d28705533e606d775c96ae5485b0b', '[\"*\"]', '2025-04-10 14:11:51', NULL, '2025-04-10 13:19:54', '2025-04-10 14:11:51'),
(52, 'App\\Models\\AdminVigile', 18, 'authToken', 'cedeca1aa11044336d4643519d34023497d5c8edf0b0b38f83428425f9dccad5', '[\"*\"]', NULL, NULL, '2025-04-10 16:06:29', '2025-04-10 16:06:29'),
(53, 'App\\Models\\AdminVigile', 18, 'authToken', '8a99b85a0e306a35247ed8fafbb849b7f04aaa4c987218bd521809ea3676c4fd', '[\"*\"]', NULL, NULL, '2025-04-10 16:09:04', '2025-04-10 16:09:04'),
(54, 'App\\Models\\Etudiant', 7, 'authToken', '92130ea07308b5103d9c0d1bf4be98f5b76ae81867725695de2aa008ec67db41', '[\"*\"]', '2025-04-11 15:52:58', NULL, '2025-04-11 14:34:46', '2025-04-11 15:52:58'),
(55, 'App\\Models\\Etudiant', 7, 'authToken', '3964e34ad2d3aee6b664eaffcecf75c87480c874c188784694dda351a667730f', '[\"*\"]', '2025-05-16 23:08:03', NULL, '2025-05-16 22:51:08', '2025-05-16 23:08:03'),
(60, 'App\\Models\\Etudiant', 7, 'authToken', '429a9ace4ff063acee1a9773397e23801e4005e467ea1e0d0044b4ac087e0b0a', '[\"*\"]', '2025-05-23 20:29:25', NULL, '2025-05-23 17:49:23', '2025-05-23 20:29:25'),
(63, 'App\\Models\\AdminVigile', 7, 'authToken', 'f349885e200d8ffce3f181beada0c98d681e1df65fa819eaaa3112c2d1b9999d', '[\"*\"]', '2025-05-24 12:39:40', NULL, '2025-05-24 12:39:39', '2025-05-24 12:39:40'),
(66, 'App\\Models\\AdminVigile', 18, 'authToken', 'e14d1ee226ae73f74c82f0769c428b360c837832f865befb241acafea769e5fb', '[\"*\"]', NULL, NULL, '2025-05-24 12:51:35', '2025-05-24 12:51:35'),
(69, 'App\\Models\\AdminVigile', 7, 'authToken', '79b9932f74cf28d15e8238130d14355a972da269e12f2a8ed9404338a8b289e2', '[\"*\"]', '2025-05-24 13:36:56', NULL, '2025-05-24 13:36:55', '2025-05-24 13:36:56'),
(72, 'App\\Models\\AdminVigile', 7, 'authToken', '39ae43d191f71aa89e3c2298bab421042b6ec139cf2e3d4d78ac385926d42257', '[\"*\"]', '2025-05-25 03:00:57', NULL, '2025-05-25 03:00:56', '2025-05-25 03:00:57'),
(73, 'App\\Models\\AdminVigile', 7, 'authToken', 'a377c778bf06e0a358634890f13d61e4944c06683c9d499c1515c17c85e64844', '[\"*\"]', '2025-05-25 18:47:39', NULL, '2025-05-25 12:14:04', '2025-05-25 18:47:39');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0KNfq0lgaUaqX6bkzmwkyPapOITKjh3l7P2Oznh1', NULL, '127.0.0.1', 'PostmanRuntime/7.43.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoic3VzTFpRb1NJbkNuMDA5TDI0T2dnR3VHR3ZMWW8wZkdlSmsxQ3BJZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1740401467),
('1kSjrLVRkE29v12zhyrkPJAoMlaasSImGwY2iX7e', NULL, '127.0.0.1', 'PostmanRuntime/7.43.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN0UxS0l4Zk5nNnREUVJuanQ0cjJDYlpOczA2NEFrUXptWm9MeVlJYSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1740148513),
('BCjvBBS66WMlsJkGv79M7vsYXk0Qn9yVuad9TTVo', NULL, '127.0.0.1', 'PostmanRuntime/7.43.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMlVoeVlVNExDRHNzcW9WVmRCcVA5MjdwM0tDSGdyOGpjVEtkNDJmaSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1740500079),
('iTXbCHWWkC98YeI0aJv6dDF1Dy5oYaRHuaNe4jpX', NULL, '127.0.0.1', 'PostmanRuntime/7.43.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMmMxTjRWazVYcVY4WENtN0tvUENCUEdzVW9hcG9yVjBEclpicFB2cSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1740416483),
('OB5I0Gty4RXtcvpxlWF5njGT2mM0k4Ztwx6slJ0P', NULL, '127.0.0.1', 'PostmanRuntime/7.43.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmpkTjNOdTlDekNVSlNvdWJLdXFtTDMwNDdhRVdYZXlsSXV4VFdvbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1740131326),
('oWfBICmSthpe2eixNhXclOj0omihMtjJm6qMl9ZK', NULL, '127.0.0.1', 'PostmanRuntime/7.43.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZmN3VVUyZThjU1p2ZTNKeEVNOWFReHBMU0IyTzNncUtvdnZBZ1l2diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1740491647),
('wwRsMK3vjyWP8Xi3ULtWjBkPJrv7FSaCdL9zCTrc', NULL, '127.0.0.1', 'PostmanRuntime/7.43.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSExHVmpha1Y4am1ITmhVSU1TSFRxa3NLN1FveGlTZmNUa2Q0U3M1ViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1740445065);

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `montant` int(11) NOT NULL,
  `type` enum('dépot','petit déjeuner','déjeuner','dîner') NOT NULL,
  `operateur` enum('wave','orange','free') DEFAULT NULL,
  `id_etudiant` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `date`, `montant`, `type`, `operateur`, `id_etudiant`) VALUES
(1, '2025-02-22', 1000, 'dépot', 'wave', 5),
(2, '2025-02-23', 100, 'dîner', NULL, 2),
(3, '2025-02-23', 100, 'dîner', NULL, 5),
(4, '2025-02-23', 50, 'petit déjeuner', NULL, 5),
(5, '2025-02-25', 70, 'dépot', 'wave', 1),
(6, '2025-02-25', 100, 'déjeuner', NULL, 2),
(7, '2025-03-04', 1000, 'dépot', 'orange', 7),
(8, '2025-03-04', 50, 'petit déjeuner', NULL, 7),
(9, '2025-03-09', 100, 'déjeuner', NULL, 7),
(10, '2025-03-09', 50, 'petit déjeuner', NULL, 7),
(11, '2025-03-09', 1000, 'dépot', 'wave', 7),
(12, '2025-03-09', 500, 'dépot', 'orange', 7),
(13, '2025-03-09', 100, 'déjeuner', NULL, 7),
(14, '2025-03-09', 100, 'déjeuner', NULL, 7),
(15, '2025-03-09', 100, 'déjeuner', NULL, 7),
(16, '2025-03-09', 100, 'déjeuner', NULL, 7),
(17, '2025-03-09', 100, 'déjeuner', NULL, 7),
(18, '2025-03-10', 100, 'déjeuner', NULL, 7),
(19, '2025-03-10', 100, 'déjeuner', NULL, 7),
(20, '2025-03-10', 100, 'déjeuner', NULL, 7),
(21, '2025-03-10', 100, 'déjeuner', NULL, 7),
(22, '2025-03-10', 100, 'déjeuner', NULL, 7),
(23, '2025-03-10', 100, 'déjeuner', NULL, 7),
(24, '2025-03-10', 100, 'déjeuner', NULL, 7),
(25, '2025-03-10', 100, 'déjeuner', NULL, 7),
(26, '2025-03-20', 100, 'déjeuner', NULL, 7),
(27, '2025-03-20', 100, 'déjeuner', NULL, 7),
(28, '2025-03-20', 100, 'dépot', 'wave', 7),
(29, '2025-03-20', 100, 'déjeuner', NULL, 7),
(30, '2025-03-20', 300, 'dépot', 'free', 7),
(31, '2025-03-20', 100, 'déjeuner', NULL, 7),
(32, '2025-03-20', 100, 'déjeuner', NULL, 7),
(33, '2025-03-20', 100, 'déjeuner', NULL, 7),
(34, '2025-05-16', 100, 'dépot', 'wave', 7),
(35, '2025-05-16', 100, 'dépot', 'wave', 7),
(36, '2025-05-23', 100, 'dépot', 'orange', 7),
(37, '2025-05-24', 100, 'dépot', 'wave', 7),
(38, '2025-05-24', 100, 'dépot', 'orange', 7),
(39, '2025-05-24', 200, 'dépot', 'free', 7);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `current_team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin_vigiles`
--
ALTER TABLE `admin_vigiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_vigiles_email_unique` (`email`),
  ADD UNIQUE KEY `admin_vigiles_telephone_unique` (`telephone`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `etudiants_email_unique` (`email`),
  ADD UNIQUE KEY `etudiants_numero_de_dossier_unique` (`numero_de_dossier`),
  ADD UNIQUE KEY `etudiants_uid_carte_unique` (`uid_carte`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_resets_email_index` (`email`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_id_etudiant_foreign` (`id_etudiant`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin_vigiles`
--
ALTER TABLE `admin_vigiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_id_etudiant_foreign` FOREIGN KEY (`id_etudiant`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
