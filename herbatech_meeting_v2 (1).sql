-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 26, 2025 at 02:58 AM
-- Server version: 8.4.3
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `herbatech_meeting_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_items`
--

CREATE TABLE `action_items` (
  `id` bigint UNSIGNED NOT NULL,
  `meeting_id` bigint UNSIGNED NOT NULL,
  `assigned_to` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `priority` int NOT NULL DEFAULT '1',
  `completion_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `action_items`
--

INSERT INTO `action_items` (`id`, `meeting_id`, `assigned_to`, `department_id`, `title`, `description`, `due_date`, `status`, `priority`, `completion_notes`, `completed_at`, `created_at`, `updated_at`) VALUES
(18, 20, 11, 11, 'Read of Invoice', 'Afddd', '2025-11-21', 'completed', 3, NULL, '2025-11-19 21:54:48', '2025-11-19 20:56:25', '2025-11-19 21:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `agendas`
--

CREATE TABLE `agendas` (
  `id` bigint UNSIGNED NOT NULL,
  `meeting_id` bigint UNSIGNED NOT NULL,
  `topic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `duration` int DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `presenter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Produksi', 'Departemen Produksi', 1, '2025-11-11 00:19:43', '2025-11-11 00:19:43'),
(10, 'IT', 'Departemen Teknologi Informasi', 1, '2025-11-11 00:19:43', '2025-11-11 00:19:43'),
(11, 'Supply Chain', '(PPIC WH)', 1, '2025-11-14 00:01:26', '2025-11-14 00:01:26'),
(12, 'Procurement', 'Pengadaan', 1, '2025-11-14 00:01:45', '2025-11-14 00:01:45'),
(13, 'Quality Control (QC)', 'Controlling Quality of Production', 1, '2025-11-14 00:02:58', '2025-11-14 00:02:58'),
(14, 'Quality Assurance (QA)', NULL, 1, '2025-11-14 00:03:26', '2025-11-14 00:03:26'),
(15, 'HRGA', NULL, 1, '2025-11-14 00:03:38', '2025-11-14 00:03:38'),
(16, 'FAT', NULL, 1, '2025-11-14 00:03:47', '2025-11-14 00:03:47'),
(17, 'Managerials', NULL, 1, '2025-11-14 00:13:14', '2025-11-14 00:13:14'),
(18, 'Direksi', NULL, 1, '2025-11-14 00:27:50', '2025-11-14 00:27:50');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meeting_type_id` bigint UNSIGNED NOT NULL,
  `organizer_id` bigint UNSIGNED NOT NULL,
  `assigned_minute_taker_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `started_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `current_agenda_id` bigint UNSIGNED DEFAULT NULL,
  `meeting_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meeting_platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `title`, `description`, `meeting_type_id`, `organizer_id`, `assigned_minute_taker_id`, `department_id`, `start_time`, `end_time`, `location`, `status`, `started_at`, `ended_at`, `current_agenda_id`, `meeting_link`, `meeting_platform`, `meeting_id`, `meeting_password`, `is_online`, `created_at`, `updated_at`) VALUES
(20, 'Incoming', 'Trend Data untuk Action Items - Data dibuat, selesai, dan terlambat per hari\r\n\r\nTrend Data untuk Meetings - Data dibuat, selesai, dan terjadwal per hari\r\n\r\nAPI Endpoint untuk update chart secara dinamis\r\n\r\nMethod tambahan untuk statistik department, tipe meeting, dll\r\n\r\nData spesifik user berdasarkan role', 6, 1, 11, 11, '2025-11-20 11:30:00', '2025-11-21 12:30:00', 'Ruang Meeting Plan B', 'completed', '2025-11-19 20:35:22', '2025-11-19 21:34:24', NULL, NULL, NULL, NULL, NULL, 0, '2025-11-19 20:31:46', '2025-11-19 21:34:24'),
(21, 'Monthly Meeting', 'AAddgg', 7, 1, NULL, 11, '2025-11-20 14:02:00', '2025-11-20 16:02:00', 'Ruang Meeting Plan B', 'scheduled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(22, 'Pembuatan HRIS', 'A', 7, 1, NULL, 10, '2025-11-21 15:30:00', '2025-11-21 17:32:00', 'Ruang Meeting Plan B', 'scheduled', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2025-11-19 23:29:05', '2025-11-19 23:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_files`
--

CREATE TABLE `meeting_files` (
  `id` bigint UNSIGNED NOT NULL,
  `meeting_id` bigint UNSIGNED NOT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_files`
--

INSERT INTO `meeting_files` (`id`, `meeting_id`, `uploaded_by`, `file_name`, `file_path`, `file_type`, `file_size`, `description`, `created_at`, `updated_at`) VALUES
(11, 20, 1, 'Purchase Requisition Oktober 2025 NEW - PR.pdf', 'meeting_files/20/tzGKB8WPIMym9Or8oR4eaLV8Wi01DCznnLwKlTqA.pdf', 'application/pdf', 81319, NULL, '2025-11-19 20:35:18', '2025-11-19 20:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_minutes`
--

CREATE TABLE `meeting_minutes` (
  `id` bigint UNSIGNED NOT NULL,
  `meeting_id` bigint UNSIGNED NOT NULL,
  `minute_taker_id` bigint UNSIGNED NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `decisions` json DEFAULT NULL,
  `is_finalized` tinyint(1) NOT NULL DEFAULT '0',
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_minutes`
--

INSERT INTO `meeting_minutes` (`id`, `meeting_id`, `minute_taker_id`, `content`, `decisions`, `is_finalized`, `finalized_at`, `created_at`, `updated_at`) VALUES
(13, 20, 11, 'Trend Data untuk Action Items - Data dibuat, selesai, dan terlambat per hari\r\n\r\nTrend Data untuk Meetings - Data dibuat, selesai, dan terjadwal per hari\r\n\r\nAPI Endpoint untuk update chart secara dinamis\r\n\r\nMethod tambahan untuk statistik department, tipe meeting, dll\r\n\r\nData spesifik user berdasarkan role', '{\"0\": \"Trend Data untuk Action Items - Data dibuat, selesai, dan terlambat per hari\", \"2\": \"Trend Data untuk Meetings - Data dibuat, selesai, dan terjadwal per hari\", \"4\": \"API Endpoint untuk update chart secara dinamis\", \"6\": \"Method tambahan untuk statistik department, tipe meeting, dll\", \"8\": \"Data spesifik user berdasarkan role\"}', 0, NULL, '2025-11-19 20:37:26', '2025-11-19 20:37:26');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_participants`
--

CREATE TABLE `meeting_participants` (
  `id` bigint UNSIGNED NOT NULL,
  `meeting_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` enum('chairperson','participant','secretary') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'participant',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `attended` tinyint(1) NOT NULL DEFAULT '0',
  `excuse` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_participants`
--

INSERT INTO `meeting_participants` (`id`, `meeting_id`, `user_id`, `role`, `is_required`, `attended`, `excuse`, `created_at`, `updated_at`) VALUES
(109, 20, 1, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(110, 20, 11, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(111, 20, 12, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(112, 20, 13, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(113, 20, 14, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(114, 20, 15, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(115, 20, 16, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(116, 20, 17, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(117, 20, 18, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(118, 20, 19, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(119, 20, 20, 'participant', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(120, 20, 1, 'chairperson', 1, 0, NULL, '2025-11-19 20:31:46', '2025-11-19 20:31:46'),
(121, 21, 1, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(122, 21, 11, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(123, 21, 12, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(124, 21, 13, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(125, 21, 14, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(126, 21, 15, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(127, 21, 16, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(128, 21, 17, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(129, 21, 18, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(130, 21, 19, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(131, 21, 20, 'participant', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(132, 21, 1, 'chairperson', 1, 0, NULL, '2025-11-19 23:02:35', '2025-11-19 23:02:35'),
(133, 22, 1, 'participant', 1, 0, NULL, '2025-11-19 23:29:05', '2025-11-19 23:29:05'),
(134, 22, 16, 'participant', 1, 0, NULL, '2025-11-19 23:29:05', '2025-11-19 23:29:05'),
(135, 22, 18, 'participant', 1, 0, NULL, '2025-11-19 23:29:05', '2025-11-19 23:29:05'),
(136, 22, 19, 'participant', 1, 0, NULL, '2025-11-19 23:29:05', '2025-11-19 23:29:05'),
(137, 22, 20, 'participant', 1, 0, NULL, '2025-11-19 23:29:05', '2025-11-19 23:29:05'),
(138, 22, 1, 'chairperson', 1, 0, NULL, '2025-11-19 23:29:05', '2025-11-19 23:29:05');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_types`
--

CREATE TABLE `meeting_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `required_fields` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_types`
--

INSERT INTO `meeting_types` (`id`, `name`, `description`, `required_fields`, `is_active`, `created_at`, `updated_at`) VALUES
(6, 'Weekly Meetings', 'mingguan', '[\"production_report\", \"quality_issues\", \"maintenance_needs\", \"safety_incidents\", \"budget_review\", \"project_updates\", \"performance_metrics\"]', 1, '2025-11-12 00:24:38', '2025-11-19 19:52:42'),
(7, 'Monthly Meeting Sasaran Mutu', NULL, '[\"production_report\", \"quality_issues\", \"maintenance_needs\", \"safety_incidents\", \"budget_review\", \"project_updates\", \"performance_metrics\"]', 1, '2025-11-19 19:52:28', '2025-11-19 19:52:28'),
(8, 'Weekly Meetings (Inspirations)', NULL, '[\"production_report\", \"quality_issues\", \"maintenance_needs\", \"safety_incidents\", \"budget_review\", \"project_updates\", \"performance_metrics\"]', 1, '2025-11-19 19:53:09', '2025-11-19 19:53:09');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_11_000000_create_departments_table', 1),
(2, '2014_10_12_000000_create_users_table', 1),
(3, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2025_11_11_040408_create_meeting_types_table', 1),
(7, '2025_11_11_040430_create_meetings_table', 1),
(8, '2025_11_11_040451_create_agendas_table', 1),
(9, '2025_11_11_040511_create_meeting_participants_table', 1),
(10, '2025_11_11_040531_create_meeting_files_table', 1),
(11, '2025_11_11_040549_create_meeting_minutes_table', 1),
(12, '2025_11_11_040610_create_action_items_table', 1),
(13, '2025_11_12_013231_add_timer_columns_to_agendas_table', 2),
(14, '2025_11_12_013302_add_timer_columns_to_meetings_table', 2),
(15, '2025_11_12_073855_update_agendas_table_add_missing_fields', 3),
(16, '2025_11_14_233057_add_meeting_platform_fields_to_meetings_table', 4),
(17, '2025_11_14_235758_add_assigned_minute_taker_to_meetings_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','manager','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `position` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `department_id`, `position`, `phone`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@company.com', NULL, '$2y$12$tyovMoD6ey43X6KVkQ8ps.9QyPfK.3jyysbq4ISyu/77he2B0A9za', 'admin', 10, 'System Administrator', '081234567890', 1, NULL, '2025-11-11 00:19:44', '2025-11-11 00:19:44'),
(11, 'Ass. Manager Supply Chain', 'assmanager@herbatech.id', NULL, '$2y$12$9k2HNwAUekTBsNqzuMe97.XWNbSvg1uhPVBy15Op3pdkiiMKjxp5K', 'user', 11, 'Assistant Manager', '0899', 1, NULL, '2025-11-14 00:06:42', '2025-11-14 00:06:42'),
(12, 'Spv. Procurement', 'spvprocurement@herbatech.id', NULL, '$2y$12$.ghQoBpZF5UWVCgjGWZbf.hKxNmdEVuJ8dYLOszkee51djUcQPYd6', 'user', 12, 'Spv Procurement', '08766', 1, NULL, '2025-11-14 00:07:49', '2025-11-14 00:07:49'),
(13, 'Spv Produksi', 'spvproduksi@herbatech.id', NULL, '$2y$12$c0DB1npelSgpjtG50ujJgeAsDO.uCuzd0kv27n2jcZ/ltPRO9tvei', 'user', 1, 'Spv Produksi', NULL, 1, NULL, '2025-11-14 00:09:00', '2025-11-14 00:09:00'),
(14, 'Spv Quality Control', 'spvqc@herbatech.id', NULL, '$2y$12$jxk9rK3fkFrPGsSTMYq4QOGS/iYxFioMqzcLqTaDJNzvmtXST02SK', 'user', 13, 'Spv Quality Control', '07787', 1, NULL, '2025-11-14 00:09:56', '2025-11-14 00:09:56'),
(15, 'Spv Quality Assurance', 'spvqa@herbatech.id', NULL, '$2y$12$mqoHxS9cC6nTJ1kQbt.yZONMZm/D2.DSBvcmtllEMeEWdJSZNoaI6', 'user', 14, 'Spv Quality Assurance', '087899', 1, NULL, '2025-11-14 00:10:43', '2025-11-14 00:10:43'),
(16, 'Spv HRGA', 'spvhrga@herbatech.id', NULL, '$2y$12$W0Tr3yYfPriGDk4JJLUyTejGU3V6H62aK1IhXuF1zU7miPg56UnlK', 'user', 15, 'Spv HRGA', NULL, 1, NULL, '2025-11-14 00:11:25', '2025-11-19 19:55:52'),
(17, 'Spv FAT', 'spvfat@herbatech.id', NULL, '$2y$12$JxBX4VlZwZRRkbvv2GCpEe7jHL/xw6pM4ZbHJaCCeNB9WXLO.kPte', 'user', 16, 'Spv FAT', NULL, 1, NULL, '2025-11-14 00:12:08', '2025-11-14 00:12:08'),
(18, 'Operatinal Manager', 'op@herbatech.id', NULL, '$2y$12$qD4Kc7/FtJRHn5gICnSHVel0a19B/QVyKMYNQNYTd2cbvkETrCWN6', 'manager', 17, 'Operatinal Manager', NULL, 1, NULL, '2025-11-14 00:14:01', '2025-11-14 00:14:01'),
(19, 'General Manager', 'gm@herbatech.id', NULL, '$2y$12$dNcAGQIjThNMHuNDv/I0MepHEsVzAsLewWiFD1J8SI9VbtQBe8DpW', 'admin', 17, 'General Manager', NULL, 1, NULL, '2025-11-14 00:14:46', '2025-11-14 00:14:46'),
(20, 'Directur', 'directur@herbatech.id', NULL, '$2y$12$t6o4r/BpzdnrGY6Lp0GAteN.NOy4YtcLKfy5s8LCkYKAHc.HMMbHG', 'admin', 18, 'Directur', NULL, 1, NULL, '2025-11-14 00:29:04', '2025-11-14 00:29:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_items`
--
ALTER TABLE `action_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_items_meeting_id_foreign` (`meeting_id`),
  ADD KEY `action_items_assigned_to_foreign` (`assigned_to`),
  ADD KEY `action_items_department_id_foreign` (`department_id`);

--
-- Indexes for table `agendas`
--
ALTER TABLE `agendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agendas_meeting_id_foreign` (`meeting_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meetings_meeting_type_id_foreign` (`meeting_type_id`),
  ADD KEY `meetings_organizer_id_foreign` (`organizer_id`),
  ADD KEY `meetings_department_id_foreign` (`department_id`),
  ADD KEY `meetings_current_agenda_id_foreign` (`current_agenda_id`),
  ADD KEY `meetings_assigned_minute_taker_id_foreign` (`assigned_minute_taker_id`);

--
-- Indexes for table `meeting_files`
--
ALTER TABLE `meeting_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_files_meeting_id_foreign` (`meeting_id`),
  ADD KEY `meeting_files_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_minutes_meeting_id_foreign` (`meeting_id`),
  ADD KEY `meeting_minutes_minute_taker_id_foreign` (`minute_taker_id`);

--
-- Indexes for table `meeting_participants`
--
ALTER TABLE `meeting_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_participants_meeting_id_foreign` (`meeting_id`),
  ADD KEY `meeting_participants_user_id_foreign` (`user_id`);

--
-- Indexes for table `meeting_types`
--
ALTER TABLE `meeting_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_department_id_foreign` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_items`
--
ALTER TABLE `action_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `agendas`
--
ALTER TABLE `agendas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `meeting_files`
--
ALTER TABLE `meeting_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `meeting_participants`
--
ALTER TABLE `meeting_participants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `meeting_types`
--
ALTER TABLE `meeting_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `action_items`
--
ALTER TABLE `action_items`
  ADD CONSTRAINT `action_items_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `action_items_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `action_items_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `agendas`
--
ALTER TABLE `agendas`
  ADD CONSTRAINT `agendas_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_assigned_minute_taker_id_foreign` FOREIGN KEY (`assigned_minute_taker_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meetings_current_agenda_id_foreign` FOREIGN KEY (`current_agenda_id`) REFERENCES `agendas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meetings_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meetings_meeting_type_id_foreign` FOREIGN KEY (`meeting_type_id`) REFERENCES `meeting_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meetings_organizer_id_foreign` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_files`
--
ALTER TABLE `meeting_files`
  ADD CONSTRAINT `meeting_files_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  ADD CONSTRAINT `meeting_minutes_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_minutes_minute_taker_id_foreign` FOREIGN KEY (`minute_taker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_participants`
--
ALTER TABLE `meeting_participants`
  ADD CONSTRAINT `meeting_participants_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
