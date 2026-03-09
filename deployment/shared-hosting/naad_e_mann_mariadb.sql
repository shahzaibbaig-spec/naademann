-- MariaDB import dump generated from SQLite
-- Generated at: 2026-03-09 11:22:31 UTC

SET NAMES utf8mb4;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET FOREIGN_KEY_CHECKS = 0;

-- Table structure for albums
DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `artist_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `genre` VARCHAR(255) NULL,
  `description` LONGTEXT NULL,
  `cover_image_url` VARCHAR(255) NULL,
  `release_date` DATE NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `artist_profile_id` BIGINT UNSIGNED NULL,
  `genre_id` BIGINT UNSIGNED NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'draft',
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for artist_follows
DROP TABLE IF EXISTS `artist_follows`;
CREATE TABLE `artist_follows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `artist_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for artist_profiles
DROP TABLE IF EXISTS `artist_profiles`;
CREATE TABLE `artist_profiles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `genre_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `bio` LONGTEXT NULL,
  `avatar_url` VARCHAR(255) NULL,
  `cover_image_url` VARCHAR(255) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'draft',
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  `monthly_listeners` INT NOT NULL DEFAULT 0,
  `followers_count` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for artists
DROP TABLE IF EXISTS `artists`;
CREATE TABLE `artists` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `genre` VARCHAR(255) NULL,
  `bio` LONGTEXT NULL,
  `image_url` VARCHAR(255) NULL,
  `monthly_listeners` INT NOT NULL DEFAULT 0,
  `followers` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for banners
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) NULL,
  `cta_label` VARCHAR(255) NULL,
  `cta_url` VARCHAR(255) NULL,
  `image_url` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `slug` VARCHAR(255) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'draft',
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` LONGTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` LONGTEXT NOT NULL,
  `queue` LONGTEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for favorites
DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `song_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `track_id` BIGINT UNSIGNED NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for follows
DROP TABLE IF EXISTS `follows`;
CREATE TABLE `follows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `artist_profile_id` BIGINT UNSIGNED NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for genres
DROP TABLE IF EXISTS `genres`;
CREATE TABLE `genres` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` LONGTEXT NULL,
  `image_url` VARCHAR(255) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `color` VARCHAR(255) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'published',
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` LONGTEXT NULL,
  `cancelled_at` INT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` INT NOT NULL,
  `reserved_at` INT NULL,
  `available_at` INT NOT NULL,
  `created_at` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for platform_settings
DROP TABLE IF EXISTS `platform_settings`;
CREATE TABLE `platform_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `value` LONGTEXT NULL,
  `type` VARCHAR(255) NOT NULL DEFAULT 'text',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for playlist_track
DROP TABLE IF EXISTS `playlist_track`;
CREATE TABLE `playlist_track` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `playlist_id` BIGINT UNSIGNED NOT NULL,
  `track_id` BIGINT UNSIGNED NOT NULL,
  `position` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for playlists
DROP TABLE IF EXISTS `playlists`;
CREATE TABLE `playlists` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` LONGTEXT NULL,
  `cover_image_url` VARCHAR(255) NULL,
  `song_ids` LONGTEXT NULL,
  `is_public` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'draft',
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(255) NULL,
  `user_agent` LONGTEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for site_settings
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(255) NOT NULL,
  `label` VARCHAR(255) NULL,
  `value` LONGTEXT NULL,
  `group` VARCHAR(255) NOT NULL DEFAULT 'general',
  `type` VARCHAR(255) NOT NULL DEFAULT 'text',
  `is_public` TINYINT(1) NOT NULL DEFAULT 0,
  `status` VARCHAR(255) NOT NULL DEFAULT 'published',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for songs
DROP TABLE IF EXISTS `songs`;
CREATE TABLE `songs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `artist_id` BIGINT UNSIGNED NOT NULL,
  `album_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `genre` VARCHAR(255) NOT NULL,
  `duration` INT NOT NULL DEFAULT 0,
  `audio_url` VARCHAR(255) NOT NULL,
  `cover_image_url` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `streams_count` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `moderation_status` VARCHAR(255) NOT NULL DEFAULT 'approved',
  `approved_at` DATETIME NULL,
  `description` LONGTEXT NULL,
  `lyrics` LONGTEXT NULL,
  `release_date` DATE NULL,
  `published_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for streams
DROP TABLE IF EXISTS `streams`;
CREATE TABLE `streams` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `song_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(255) NULL,
  `played_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for track_views
DROP TABLE IF EXISTS `track_views`;
CREATE TABLE `track_views` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `track_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(255) NULL,
  `viewed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for tracks
DROP TABLE IF EXISTS `tracks`;
CREATE TABLE `tracks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `artist_profile_id` BIGINT UNSIGNED NOT NULL,
  `genre_id` BIGINT UNSIGNED NULL,
  `album_id` BIGINT UNSIGNED NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` LONGTEXT NULL,
  `lyrics` LONGTEXT NULL,
  `duration` INT NOT NULL DEFAULT 0,
  `audio_url` VARCHAR(255) NOT NULL,
  `cover_image_url` VARCHAR(255) NULL,
  `release_date` DATE NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'draft',
  `is_published` TINYINT(1) NOT NULL DEFAULT 0,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `published_at` DATETIME NULL,
  `views_count` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` DATETIME NULL,
  `password` VARCHAR(255) NOT NULL,
  `api_token` VARCHAR(255) NULL,
  `avatar_url` VARCHAR(255) NULL,
  `remember_token` VARCHAR(255) NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  `role` VARCHAR(255) NOT NULL DEFAULT 'listener',
  `headline` VARCHAR(255) NULL,
  `bio` LONGTEXT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for albums
INSERT INTO `albums` (`id`, `artist_id`, `title`, `slug`, `genre`, `description`, `cover_image_url`, `release_date`, `created_at`, `updated_at`, `artist_profile_id`, `genre_id`, `status`, `is_published`, `is_featured`, `published_at`) VALUES
(1, 1, 'Aurora Veil', 'aurora-veil', 'Neo-Soul', 'Warm vocals, velvet keys, and night-drive bass pressure.', 'https://placehold.co/1200x1200/0f172a/ec4899?text=Aurora%20Veil', '2026-02-21 00:00:00', '2026-03-07 17:58:11', '2026-03-07 20:31:43', 1, 1, 'published', 1, 1, '2026-02-21 20:00:00'),
(2, 2, 'Velvet Pulse', 'velvet-pulse', 'Electronic', 'Club-ready percussion and luminous synth pressure.', 'https://placehold.co/1200x1200/0f172a/38bdf8?text=Velvet%20Pulse', '2026-01-14 00:00:00', '2026-03-07 17:58:12', '2026-03-07 20:31:43', 2, 2, 'published', 1, 1, '2026-01-14 20:00:00'),
(3, 3, 'Rhythm Theory', 'rhythm-theory', 'Hip Hop', 'Punchy drums, modern rap cadence, and city-night focus.', 'https://placehold.co/1200x1200/0f172a/22d3ee?text=Rhythm%20Theory', '2025-11-30 00:00:00', '2026-03-07 17:58:18', '2026-03-07 20:31:44', 3, 3, 'published', 1, 0, '2025-11-30 20:00:00'),
(4, 1, 'Afterglow Letters', 'afterglow-letters', 'R&B', 'Late-night letters folded into polished rhythm and neon romance.', 'https://placehold.co/1200x1200/0f172a/60a5fa?text=Afterglow%20Letters', '2025-11-08 00:00:00', '2026-03-07 20:31:43', '2026-03-07 20:31:43', 1, 10, 'published', 1, 1, '2025-11-08 20:00:00'),
(5, 2, 'Silent Horizons', 'silent-horizons', 'Ambient', 'Wide, drifting atmospheres shaped for headphones after midnight.', 'https://placehold.co/1200x1200/0f172a/2dd4bf?text=Silent%20Horizons', '2025-10-02 00:00:00', '2026-03-07 20:31:43', '2026-03-07 20:31:43', 2, 8, 'published', 1, 1, '2025-10-02 20:00:00'),
(6, 3, 'Sidewalk Cinema', 'sidewalk-cinema', 'Indie', 'Street poetry, alt-leaning hooks, and neon-film storytelling.', 'https://placehold.co/1200x1200/0f172a/a3e635?text=Sidewalk%20Cinema', '2025-08-18 00:00:00', '2026-03-07 20:31:44', '2026-03-07 20:31:44', 3, 9, 'published', 1, 0, '2025-08-18 20:00:00'),
(7, 4, 'Midnight Raga', 'midnight-raga', 'Classical', 'Contemporary classical themes with raga-inspired melodic movement.', 'https://placehold.co/1200x1200/0f172a/c084fc?text=Midnight%20Raga', '2025-12-05 00:00:00', '2026-03-07 20:31:44', '2026-03-07 20:31:44', 4, 7, 'published', 1, 0, '2025-12-05 20:00:00'),
(8, 4, 'Velvet Chamber', 'velvet-chamber', 'Jazz', 'Strings, upright warmth, and a chamber-room glow.', 'https://placehold.co/1200x1200/0f172a/f59e0b?text=Velvet%20Chamber', '2025-07-11 00:00:00', '2026-03-07 20:31:44', '2026-03-07 20:31:44', 4, 4, 'published', 1, 0, '2025-07-11 20:00:00'),
(9, 5, 'Electric Bloom', 'electric-bloom', 'Pop', 'Hooks, handclaps, and a polished summer-night charge.', 'https://placehold.co/1200x1200/0f172a/fb7185?text=Electric%20Bloom', '2026-01-26 00:00:00', '2026-03-07 20:31:45', '2026-03-07 20:31:45', 5, 6, 'published', 1, 0, '2026-01-26 20:00:00'),
(10, 5, 'North of Noise', 'north-of-noise', 'Rock', 'Guitar lift, anthem choruses, and rain-soaked stage lights.', 'https://placehold.co/1200x1200/0f172a/f97316?text=North%20of%20Noise', '2025-09-27 00:00:00', '2026-03-07 20:31:45', '2026-03-07 20:31:45', 5, 5, 'published', 1, 0, '2025-09-27 20:00:00');

-- Data for artist_follows
INSERT INTO `artist_follows` (`id`, `user_id`, `artist_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-03-07 19:08:21', '2026-03-07 19:08:21');

-- Data for artist_profiles
INSERT INTO `artist_profiles` (`id`, `user_id`, `genre_id`, `name`, `slug`, `bio`, `avatar_url`, `cover_image_url`, `status`, `is_published`, `is_featured`, `published_at`, `monthly_listeners`, `followers_count`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Mira Skye', 'mira-skye', 'A premium neo-soul voice balancing intimacy, warm harmony, and cinematic low-end.', 'https://placehold.co/900x900/020617/ec4899?text=Mira%20Skye', 'https://placehold.co/1600x900/020617/ec4899?text=Mira%20Skye%20Live', 'published', 1, 1, '2025-01-11 20:31:39', 8600000, 980000, '2026-03-07 17:58:08', '2026-03-07 20:31:39'),
(2, 5, 2, 'Rehan Pulse', 'rehan-pulse', 'An electronic producer focused on bass architecture, glassy synth motion, and club momentum.', 'https://placehold.co/900x900/020617/38bdf8?text=Rehan%20Pulse', 'https://placehold.co/1600x900/020617/38bdf8?text=Rehan%20Pulse%20Live', 'published', 1, 1, '2025-02-10 20:31:40', 6400000, 720000, '2026-03-07 17:58:10', '2026-03-07 20:31:40'),
(3, 6, 3, 'Kian Drift', 'kian-drift', 'A detail-heavy rapper with nocturnal hooks, cinematic ad-libs, and restless city writing.', 'https://placehold.co/900x900/020617/22d3ee?text=Kian%20Drift', 'https://placehold.co/1600x900/020617/22d3ee?text=Kian%20Drift%20Live', 'published', 1, 1, '2025-03-07 20:31:41', 7100000, 805000, '2026-03-07 17:58:11', '2026-03-07 20:31:41'),
(4, 7, 7, 'Aanya Raag', 'aanya-raag', 'A classically trained composer bending raga phrasing and chamber space into modern releases.', 'https://placehold.co/900x900/020617/c084fc?text=Aanya%20Raag', 'https://placehold.co/1600x900/020617/c084fc?text=Aanya%20Raag%20Live', 'published', 1, 0, '2025-04-01 20:31:41', 2900000, 310000, '2026-03-07 20:31:41', '2026-03-07 20:31:41'),
(5, 8, 6, 'Leena Sol', 'leena-sol', 'A bright pop songwriter with polished toplines, festival-ready choruses, and live-band energy.', 'https://placehold.co/900x900/020617/fb7185?text=Leena%20Sol', 'https://placehold.co/1600x900/020617/fb7185?text=Leena%20Sol%20Live', 'published', 1, 1, '2025-04-11 20:31:42', 5300000, 560000, '2026-03-07 20:31:42', '2026-03-07 20:31:42');

-- Data for artists
INSERT INTO `artists` (`id`, `user_id`, `name`, `slug`, `genre`, `bio`, `image_url`, `monthly_listeners`, `followers`, `created_at`, `updated_at`) VALUES
(1, 2, 'Usman Shaani', 'usman-shaani', 'Neo-Soul', 'A premium neo-soul voice balancing intimacy, warm harmony, and cinematic low-end.', '/storage/uploads/artists/b63c8827-cd4c-49a2-a509-56c9e13ad880.jpg', 8600000, 980000, '2026-03-07 17:58:08', '2026-03-08 00:33:32'),
(2, 5, 'Rehan Pulse', 'rehan-pulse', 'Electronic', 'An electronic producer focused on bass architecture, glassy synth motion, and club momentum.', 'https://placehold.co/900x900/020617/38bdf8?text=Rehan%20Pulse', 6400000, 720000, '2026-03-07 17:58:10', '2026-03-07 20:31:40'),
(3, 6, 'Kian Drift', 'kian-drift', 'Hip Hop', 'A detail-heavy rapper with nocturnal hooks, cinematic ad-libs, and restless city writing.', 'https://placehold.co/900x900/020617/22d3ee?text=Kian%20Drift', 7100000, 805000, '2026-03-07 17:58:11', '2026-03-07 20:31:40'),
(4, 7, 'Aanya Raag', 'aanya-raag', 'Classical', 'A classically trained composer bending raga phrasing and chamber space into modern releases.', 'https://placehold.co/900x900/020617/c084fc?text=Aanya%20Raag', 2900000, 310000, '2026-03-07 20:31:41', '2026-03-07 20:31:41'),
(5, 8, 'Leena Sol', 'leena-sol', 'Pop', 'A bright pop songwriter with polished toplines, festival-ready choruses, and live-band energy.', 'https://placehold.co/900x900/020617/fb7185?text=Leena%20Sol', 5300000, 560000, '2026-03-07 20:31:42', '2026-03-07 20:31:42');

-- Data for banners
INSERT INTO `banners` (`id`, `title`, `subtitle`, `cta_label`, `cta_url`, `image_url`, `is_active`, `sort_order`, `created_at`, `updated_at`, `slug`, `status`, `is_published`, `is_featured`, `published_at`) VALUES
(1, 'Naad-e-Maan Resonance', 'Stream premium artists, emotional soundscapes, and neon-lit late-night sessions.', 'Start Listening', '/', 'https://placehold.co/1600x900/020617/22d3ee?text=Naad-e-Maan%20Resonance', 1, 1, '2026-03-07 19:08:22', '2026-03-07 20:32:08', 'naad-e-maan-resonance', 'published', 1, 1, '2026-02-21 20:32:08'),
(2, 'Calling All Creators', 'Upload tracks, manage albums, and build your audience with a premium creator suite.', 'Open Creator Studio', '/creator/dashboard', 'https://placehold.co/1600x900/020617/60a5fa?text=Calling%20All%20Creators', 1, 5, '2026-03-07 19:08:22', '2026-03-07 20:32:10', 'calling-all-creators', 'published', 1, 0, '2026-03-01 20:32:10'),
(3, 'Night Sessions Live', 'Follow new videos, live cuts, and cinematic performances from rising artists.', 'Watch Videos', '/#videos', 'https://placehold.co/1600x900/020617/ec4899?text=Night%20Sessions%20Live', 1, 2, '2026-03-07 20:32:08', '2026-03-07 20:32:08', 'night-sessions-live', 'published', 1, 1, '2026-02-23 20:32:08'),
(4, 'Genre Worlds', 'Move from rock stages and pop hooks to ambient drift and classical depth.', 'Explore Genres', '/#genres', 'https://placehold.co/1600x900/020617/f59e0b?text=Genre%20Worlds', 1, 3, '2026-03-07 20:32:09', '2026-03-07 20:32:09', 'genre-worlds', 'published', 1, 0, '2026-02-25 20:32:09'),
(5, 'Creator Spotlight', 'Featured creators shaping the next wave of releases on Naad-e-Maan.', 'Meet Artists', '/artists', 'https://placehold.co/1600x900/020617/a3e635?text=Creator%20Spotlight', 1, 4, '2026-03-07 20:32:09', '2026-03-07 20:32:09', 'creator-spotlight', 'published', 1, 0, '2026-02-27 20:32:09');

-- Data for favorites
INSERT INTO `favorites` (`id`, `user_id`, `song_id`, `created_at`, `updated_at`, `track_id`) VALUES
(1, 1, 1, '2026-03-07 19:08:21', '2026-03-07 19:08:21', 1),
(2, 1, 3, '2026-03-07 19:08:21', '2026-03-07 19:08:21', 3);

-- Data for follows
INSERT INTO `follows` (`id`, `user_id`, `artist_profile_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-03-07 19:08:21', '2026-03-07 19:08:21');

-- Data for genres
INSERT INTO `genres` (`id`, `name`, `slug`, `description`, `image_url`, `is_active`, `sort_order`, `created_at`, `updated_at`, `color`, `status`, `is_published`, `is_featured`, `published_at`) VALUES
(1, 'Neo-Soul', 'neo-soul', 'Intimate vocals, velvet chords, and glowing rhythm beds.', 'https://placehold.co/1200x1600/020617/ec4899?text=Neo-Soul', 1, 7, '2026-03-07 19:08:13', '2026-03-07 20:31:38', '#ec4899', 'published', 1, 0, '2026-01-24 20:31:38'),
(2, 'Electronic', 'electronic', 'Synthetic motion, club pressure, and neon detail.', 'https://placehold.co/1200x1600/020617/38bdf8?text=Electronic', 1, 6, '2026-03-07 19:08:14', '2026-03-07 20:31:38', '#38bdf8', 'published', 1, 0, '2026-01-21 20:31:38'),
(3, 'Hip Hop', 'hip-hop', 'Rhythmic storytelling, heavy low-end, and sharp detail.', 'https://placehold.co/1200x1600/020617/22d3ee?text=Hip%20Hop', 1, 3, '2026-03-07 19:08:15', '2026-03-07 20:31:37', '#22d3ee', 'published', 1, 1, '2026-01-12 20:31:37'),
(4, 'Jazz', 'jazz', 'Improvised warmth, smoky harmony, and late-night swing.', 'https://placehold.co/1200x1600/020617/f59e0b?text=Jazz', 1, 5, '2026-03-07 19:08:17', '2026-03-07 20:31:37', '#f59e0b', 'published', 1, 1, '2026-01-18 20:31:37'),
(5, 'Rock', 'rock', 'Guitars, stage energy, and arena-scale emotion.', 'https://placehold.co/1200x1600/020617/f97316?text=Rock', 1, 1, '2026-03-07 20:31:36', '2026-03-07 20:31:36', '#f97316', 'published', 1, 1, '2026-01-06 20:31:36'),
(6, 'Pop', 'pop', 'Immediate hooks, polished production, and vocal shine.', 'https://placehold.co/1200x1600/020617/fb7185?text=Pop', 1, 2, '2026-03-07 20:31:36', '2026-03-07 20:31:36', '#fb7185', 'published', 1, 1, '2026-01-09 20:31:36'),
(7, 'Classical', 'classical', 'Orchestral depth, chamber nuance, and timeless structure.', 'https://placehold.co/1200x1600/020617/c084fc?text=Classical', 1, 4, '2026-03-07 20:31:37', '2026-03-07 20:31:37', '#c084fc', 'published', 1, 1, '2026-01-15 20:31:37'),
(8, 'Ambient', 'ambient', 'Slow-building atmosphere and cinematic texture fields.', 'https://placehold.co/1200x1600/020617/2dd4bf?text=Ambient', 1, 8, '2026-03-07 20:31:38', '2026-03-07 20:31:38', '#2dd4bf', 'published', 1, 0, '2026-01-27 20:31:38'),
(9, 'Indie', 'indie', 'Personal writing, live-band character, and underground polish.', 'https://placehold.co/1200x1600/020617/a3e635?text=Indie', 1, 9, '2026-03-07 20:31:38', '2026-03-07 20:31:38', '#a3e635', 'published', 1, 0, '2026-01-30 20:31:38'),
(10, 'R&B', 'r-b', 'Smooth grooves, expressive vocals, and modern soul movement.', 'https://placehold.co/1200x1600/020617/60a5fa?text=R%26B', 1, 10, '2026-03-07 20:31:39', '2026-03-07 20:31:39', '#60a5fa', 'published', 1, 0, '2026-02-02 20:31:39');

-- Data for migrations
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 2),
(4, '2026_03_07_082902_create_artists_table', 3),
(5, '2026_03_07_082903_create_albums_table', 3),
(6, '2026_03_07_082904_create_songs_table', 3),
(7, '2026_03_07_082905_create_playlists_table', 3),
(8, '2026_03_07_082907_create_streams_table', 3),
(9, '2026_03_07_235000_add_platform_roles_and_profiles', 4),
(10, '2026_03_07_235100_create_genres_table', 4),
(11, '2026_03_07_235200_create_banners_table', 4),
(12, '2026_03_07_235300_create_platform_settings_table', 4),
(13, '2026_03_07_235400_create_favorites_table', 4),
(14, '2026_03_07_235500_create_artist_follows_table', 4),
(15, '2026_03_08_010000_add_creator_upload_fields_to_songs_table', 5),
(16, '2026_03_08_020000_add_color_to_genres_table', 6),
(17, '2026_03_08_030000_generate_naad_e_maan_catalog_schema', 7),
(18, '2026_03_08_040000_normalize_uploaded_storage_urls', 8);

-- Data for platform_settings
INSERT INTO `platform_settings` (`id`, `key`, `label`, `value`, `type`, `created_at`, `updated_at`) VALUES
(1, 'platform_tagline', 'Platform Tagline', 'The Sound of the Soul', 'text', '2026-03-07 19:08:23', '2026-03-08 00:42:40'),
(2, 'support_email', 'Support Email', 'support@naademaan.test', 'email', '2026-03-07 19:08:23', '2026-03-08 00:42:40'),
(3, 'homepage_cta', 'Homepage CTA', 'Open the immersive player', 'text', '2026-03-07 19:08:24', '2026-03-07 19:08:24'),
(4, 'logo_text', 'Logo Text', 'Naad-e-Maan', 'text', '2026-03-07 20:02:19', '2026-03-08 00:42:40'),
(5, 'footer_text', 'Footer Text', 'Naad-e-Maan is a premium streaming space for resonance, release culture, and creator-led sound journeys.', 'textarea', '2026-03-07 20:02:19', '2026-03-08 00:42:40'),
(6, 'social_instagram', 'Instagram URL', 'https://instagram.com/naademaan', 'url', '2026-03-07 20:02:19', '2026-03-08 00:42:40'),
(7, 'social_youtube', 'YouTube URL', 'https://youtube.com/@naademaan', 'url', '2026-03-07 20:02:19', '2026-03-08 00:42:40'),
(8, 'social_soundcloud', 'SoundCloud URL', 'https://soundcloud.com/naademaan', 'url', '2026-03-07 20:02:20', '2026-03-08 00:42:40'),
(9, 'homepage_video', 'Homepage Video URL or ID', 'NoAnDF171IQ', 'text', '2026-03-08 00:23:06', '2026-03-08 00:42:40'),
(10, 'artist_default_video', 'Artist Default Video URL or ID', 'ScMzIvxBSi4', 'text', '2026-03-08 00:23:06', '2026-03-08 00:42:40');

-- Data for playlist_track
INSERT INTO `playlist_track` (`id`, `playlist_id`, `track_id`, `position`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-03-07 17:58:34', '2026-03-07 17:58:34'),
(2, 1, 3, 2, '2026-03-07 17:58:34', '2026-03-07 17:58:34'),
(3, 1, 2, 3, '2026-03-07 17:58:34', '2026-03-07 17:58:34'),
(4, 1, 4, 4, '2026-03-07 17:58:34', '2026-03-07 17:58:34');

-- Data for playlists
INSERT INTO `playlists` (`id`, `user_id`, `name`, `slug`, `description`, `cover_image_url`, `song_ids`, `is_public`, `created_at`, `updated_at`, `status`, `is_published`, `is_featured`, `published_at`) VALUES
(1, 1, 'Naad Night Drive', 'naad-night-drive', 'Featured late-night electronic, soul, and hip hop tracks.', 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80', '[1,3,2,4]', 1, '2026-03-07 17:58:34', '2026-03-07 17:58:34', 'draft', 0, 0, '2026-03-07 17:58:34');

-- Data for sessions
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('15pjySfluIW0GMkND09PQzjphsZG0rfZFANkRg6c', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.2161', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDVJR0swYUxJSTFPTTdCaFZWQVN0S0U0NWxqZXJEYlZocUFyWHlHTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773054194),
('Dfzx6rqpA4YXhgFF7yCQcazHBavI6JgaF4L5ezCJ', NULL, '127.0.0.1', 'curl/8.10.1', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidmZKM0g0WG1zd0ZuYWNtYjhMUjhNd1pySlpEdW1kTHlBMmx0RWVwdiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772928328),
('HHIOH0BiBh9CvXE5I0bYyyRlTQyaLTxa3vKRsAJ9', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWlNHeTdvZzdxTGlUUzZ4S2h3YUJEVGVNdmxxZ2RSSVFkdmZ3V1dNWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772992446),
('TgruzuHXHTzUhrEA8AOWjkxDUe9taMR0ip4owZR7', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXJpY25DT1U5QUlnN1pGNTZTWGg5dnFRVFhEcTk5N1ZGWkxQTVZpaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDt9', 1773054429),
('ZCa8KGipYtDDsEQdUdibr5KOxfHJQIpjJ73GOPwm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNUFSZDc4aGhFOEhpRkpoZldrdml0ZFVyeHJxN3hoNEFmYWVwd2NVSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1772979585),
('ae6jnQ0HLU2DYvEXkdsWOCXXPzov2irzDqTT7d5D', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSHFwVlJmd1g0UEo3WlgxMEJlbVpnYjZpRERPcnBBUVZUZVNCSWdzRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMSI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDt9', 1772930641),
('ddjSaNxA8ASJC60BJoF6xd9slAZlS7fJVKWLRCqA', NULL, '127.0.0.1', 'curl/8.10.1', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidTVHbHQzTDRkeHU5SUJmQ3VKVGIzcEdBQXV2cXFMTmE0OU1KdHJsMyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772930443),
('eKfQAwRHk0rKnYzTwwyPjVXkReJ8SAWlB1RqwLhw', NULL, '127.0.0.1', 'curl/8.10.1', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTWVicWZWMmtOYWU5a0RIT2hzZGV2eG1uNFBOWFlqa045U3ptU1Y0ZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772928211),
('ov6ez6TchXxLoDvmreEpM7KjtmxosMOPIyQ7cNFx', NULL, '127.0.0.1', 'curl/8.10.1', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiR1c3UEM2dlZwWlZBOGtUbEt0dVRlRGU0TGtlWElkdUlIbVpHY3EyTiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772928275),
('umKo5cAfG5AuyihzfthoQTN73WltdAp9EzJGPyCy', NULL, '127.0.0.1', 'curl/8.10.1', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiZnc5aFM2OHJ6U2cxR0t4M3dtdnJsRExqMjdrSEd3aTVZdm5pNndOTSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772979742),
('ypfQNYd8zlM0XAxvZpNPGm9IPfVUsib6EKFTHNBO', NULL, '127.0.0.1', 'curl/8.10.1', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoidHlzOTBUT1E1Z3YyOUdNamh3RWZGSFdWN1RoOUdna0lncXVVT1k0NiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772929388);

-- Data for site_settings
INSERT INTO `site_settings` (`id`, `key`, `label`, `value`, `group`, `type`, `is_public`, `status`, `created_at`, `updated_at`) VALUES
(1, 'platform_tagline', 'Platform Tagline', 'The Sound of the Soul', 'branding', 'text', 1, 'published', '2026-03-07 19:08:23', '2026-03-07 19:08:23'),
(2, 'support_email', 'Support Email', 'support@naademaan.test', 'support', 'email', 1, 'published', '2026-03-07 19:08:23', '2026-03-07 20:32:10'),
(3, 'homepage_cta', 'Homepage CTA', 'Open the immersive player', 'branding', 'text', 1, 'published', '2026-03-07 19:08:24', '2026-03-07 19:08:24'),
(4, 'logo_text', 'Logo Text', 'Naad-e-Maan', 'branding', 'text', 1, 'published', '2026-03-07 20:02:19', '2026-03-07 20:02:19'),
(5, 'footer_text', 'Footer Text', 'Naad-e-Maan is a premium streaming space for resonance, release culture, and creator-led sound journeys.', 'branding', 'textarea', 1, 'published', '2026-03-07 20:02:19', '2026-03-07 20:32:10'),
(6, 'social_instagram', 'Instagram URL', 'https://instagram.com/naademaan', 'social', 'url', 1, 'published', '2026-03-07 20:02:19', '2026-03-07 20:02:19'),
(7, 'social_youtube', 'YouTube URL', 'https://youtube.com/@naademaan', 'social', 'url', 1, 'published', '2026-03-07 20:02:19', '2026-03-07 20:02:19'),
(8, 'social_soundcloud', 'SoundCloud URL', 'https://soundcloud.com/naademaan', 'social', 'url', 1, 'published', '2026-03-07 20:02:20', '2026-03-07 20:02:20'),
(9, 'homepage_video', 'Homepage Video URL or ID', 'ScMzIvxBSi4', 'media', 'text', 0, 'published', '2026-03-08 00:23:06', '2026-03-08 00:23:06'),
(10, 'artist_default_video', 'Artist Default Video URL or ID', 'ScMzIvxBSi4', 'media', 'text', 0, 'published', '2026-03-08 00:23:06', '2026-03-08 00:23:06');

-- Data for songs
INSERT INTO `songs` (`id`, `artist_id`, `album_id`, `title`, `slug`, `genre`, `duration`, `audio_url`, `cover_image_url`, `is_featured`, `streams_count`, `created_at`, `updated_at`, `moderation_status`, `approved_at`, `description`, `lyrics`, `release_date`, `published_at`) VALUES
(1, 1, 1, 'Echoes in Blue', 'echoes-in-blue', 'Neo-Soul', 280, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3', 'https://placehold.co/1200x1200/0f172a/ec4899?text=Aurora%20Veil', 1, 637500, '2026-03-07 17:58:21', '2026-03-07 20:31:45', 'approved', '2026-02-21 21:00:00', 'Echoes in Blue from Aurora Veil by Mira Skye, carrying the Neo-Soul energy of Naad-e-Maan.', 'Echoes in Blue in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-02-21 00:00:00', '2026-02-21 21:00:00'),
(2, 1, 1, 'Midnight Bloom', 'midnight-bloom', 'Neo-Soul', 216, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3', 'https://placehold.co/1200x1200/0f172a/ec4899?text=Aurora%20Veil', 0, 825000, '2026-03-07 17:58:23', '2026-03-07 20:31:46', 'approved', '2026-02-21 21:00:00', 'Midnight Bloom from Aurora Veil by Mira Skye, carrying the Neo-Soul energy of Naad-e-Maan.', 'Midnight Bloom in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-02-21 00:00:00', '2026-02-21 21:00:00'),
(3, 2, 2, 'Velvet Pulse', 'velvet-pulse-track', 'Electronic', 242, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3', 'https://placehold.co/1200x1200/0f172a/38bdf8?text=Velvet%20Pulse', 1, 1762500, '2026-03-07 17:58:25', '2026-03-07 20:31:50', 'approved', '2026-01-14 21:00:00', 'Velvet Pulse from Velvet Pulse by Rehan Pulse, carrying the Electronic energy of Naad-e-Maan.', 'Velvet Pulse in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-01-14 00:00:00', '2026-01-14 21:00:00'),
(4, 3, 3, 'Rhythm Theory', 'rhythm-theory-track', 'Hip Hop', 236, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3', 'https://placehold.co/1200x1200/0f172a/22d3ee?text=Rhythm%20Theory', 1, 2887500, '2026-03-07 17:58:26', '2026-03-07 20:31:54', 'approved', '2025-11-30 21:00:00', 'Rhythm Theory from Rhythm Theory by Kian Drift, carrying the Hip Hop energy of Naad-e-Maan.', 'Rhythm Theory in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-11-30 00:00:00', '2025-11-30 21:00:00'),
(5, 3, 3, 'City Haze', 'city-haze', 'Hip Hop', 224, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3', 'https://placehold.co/1200x1200/0f172a/22d3ee?text=Rhythm%20Theory', 0, 3075000, '2026-03-07 17:58:32', '2026-03-07 20:31:55', 'approved', '2025-11-30 21:00:00', 'City Haze from Rhythm Theory by Kian Drift, carrying the Hip Hop energy of Naad-e-Maan.', 'City Haze in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-11-30 00:00:00', '2025-11-30 21:00:00'),
(6, 2, 2, 'Neon Dust', 'neon-dust', 'Electronic', 250, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3', 'https://placehold.co/1200x1200/0f172a/38bdf8?text=Velvet%20Pulse', 0, 1950000, '2026-03-07 17:58:34', '2026-03-07 20:31:50', 'approved', '2026-01-14 21:00:00', 'Neon Dust from Velvet Pulse by Rehan Pulse, carrying the Electronic energy of Naad-e-Maan.', 'Neon Dust in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-01-14 00:00:00', '2026-01-14 21:00:00'),
(7, 1, 1, 'Silver Thread', 'silver-thread', 'Neo-Soul', 248, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3', 'https://placehold.co/1200x1200/0f172a/ec4899?text=Aurora%20Veil', 0, 1012500, '2026-03-07 19:08:20', '2026-03-07 20:31:46', 'approved', '2026-02-21 21:00:00', 'Silver Thread from Aurora Veil by Mira Skye, carrying the Neo-Soul energy of Naad-e-Maan.', 'Silver Thread in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-02-21 00:00:00', '2026-02-21 21:00:00'),
(8, 1, 4, 'Satin Static', 'satin-static', 'R&B', 234, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3', 'https://placehold.co/1200x1200/0f172a/60a5fa?text=Afterglow%20Letters', 1, 1200000, '2026-03-07 20:31:48', '2026-03-07 20:31:48', 'approved', '2025-11-08 21:00:00', 'Satin Static from Afterglow Letters by Mira Skye, carrying the R&B energy of Naad-e-Maan.', 'Satin Static in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-11-08 00:00:00', '2025-11-08 21:00:00'),
(9, 1, 4, 'Love on Delay', 'love-on-delay', 'R&B', 227, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3', 'https://placehold.co/1200x1200/0f172a/60a5fa?text=Afterglow%20Letters', 0, 1387500, '2026-03-07 20:31:49', '2026-03-07 20:31:49', 'approved', '2025-11-08 21:00:00', 'Love on Delay from Afterglow Letters by Mira Skye, carrying the R&B energy of Naad-e-Maan.', 'Love on Delay in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-11-08 00:00:00', '2025-11-08 21:00:00'),
(10, 1, 4, 'Gold Frequency', 'gold-frequency', 'R&B', 241, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3', 'https://placehold.co/1200x1200/0f172a/60a5fa?text=Afterglow%20Letters', 0, 1575000, '2026-03-07 20:31:49', '2026-03-07 20:31:49', 'approved', '2025-11-08 21:00:00', 'Gold Frequency from Afterglow Letters by Mira Skye, carrying the R&B energy of Naad-e-Maan.', 'Gold Frequency in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-11-08 00:00:00', '2025-11-08 21:00:00'),
(11, 2, 2, 'Glass Voltage', 'glass-voltage', 'Electronic', 238, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3', 'https://placehold.co/1200x1200/0f172a/38bdf8?text=Velvet%20Pulse', 0, 2137500, '2026-03-07 20:31:51', '2026-03-07 20:31:51', 'approved', '2026-01-14 21:00:00', 'Glass Voltage from Velvet Pulse by Rehan Pulse, carrying the Electronic energy of Naad-e-Maan.', 'Glass Voltage in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-01-14 00:00:00', '2026-01-14 21:00:00'),
(12, 2, 5, 'Low Tide Light', 'low-tide-light', 'Ambient', 264, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3', 'https://placehold.co/1200x1200/0f172a/2dd4bf?text=Silent%20Horizons', 1, 2325000, '2026-03-07 20:31:52', '2026-03-07 20:31:52', 'approved', '2025-10-02 21:00:00', 'Low Tide Light from Silent Horizons by Rehan Pulse, carrying the Ambient energy of Naad-e-Maan.', 'Low Tide Light in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-10-02 00:00:00', '2025-10-02 21:00:00'),
(13, 2, 5, 'Drift Signal', 'drift-signal', 'Ambient', 272, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3', 'https://placehold.co/1200x1200/0f172a/2dd4bf?text=Silent%20Horizons', 0, 2512500, '2026-03-07 20:31:53', '2026-03-07 20:31:53', 'approved', '2025-10-02 21:00:00', 'Drift Signal from Silent Horizons by Rehan Pulse, carrying the Ambient energy of Naad-e-Maan.', 'Drift Signal in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-10-02 00:00:00', '2025-10-02 21:00:00'),
(14, 2, 5, 'Skyline Hush', 'skyline-hush', 'Ambient', 289, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3', 'https://placehold.co/1200x1200/0f172a/2dd4bf?text=Silent%20Horizons', 0, 2700000, '2026-03-07 20:31:53', '2026-03-07 20:31:53', 'approved', '2025-10-02 21:00:00', 'Skyline Hush from Silent Horizons by Rehan Pulse, carrying the Ambient energy of Naad-e-Maan.', 'Skyline Hush in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-10-02 00:00:00', '2025-10-02 21:00:00'),
(15, 3, 3, 'Corner Cipher', 'corner-cipher', 'Hip Hop', 219, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-15.mp3', 'https://placehold.co/1200x1200/0f172a/22d3ee?text=Rhythm%20Theory', 0, 3262500, '2026-03-07 20:31:55', '2026-03-07 20:31:55', 'approved', '2025-11-30 21:00:00', 'Corner Cipher from Rhythm Theory by Kian Drift, carrying the Hip Hop energy of Naad-e-Maan.', 'Corner Cipher in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-11-30 00:00:00', '2025-11-30 21:00:00'),
(16, 3, 6, 'Backseat Monologue', 'backseat-monologue', 'Indie', 230, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-16.mp3', 'https://placehold.co/1200x1200/0f172a/a3e635?text=Sidewalk%20Cinema', 1, 3450000, '2026-03-07 20:31:56', '2026-03-07 20:31:56', 'approved', '2025-08-18 21:00:00', 'Backseat Monologue from Sidewalk Cinema by Kian Drift, carrying the Indie energy of Naad-e-Maan.', 'Backseat Monologue in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-08-18 00:00:00', '2025-08-18 21:00:00'),
(17, 3, 6, 'Alley Chorus', 'alley-chorus', 'Indie', 213, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3', 'https://placehold.co/1200x1200/0f172a/a3e635?text=Sidewalk%20Cinema', 0, 3637500, '2026-03-07 20:31:57', '2026-03-07 20:31:57', 'approved', '2025-08-18 21:00:00', 'Alley Chorus from Sidewalk Cinema by Kian Drift, carrying the Indie energy of Naad-e-Maan.', 'Alley Chorus in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-08-18 00:00:00', '2025-08-18 21:00:00'),
(18, 3, 6, 'Night Window', 'night-window', 'Indie', 221, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3', 'https://placehold.co/1200x1200/0f172a/a3e635?text=Sidewalk%20Cinema', 0, 3825000, '2026-03-07 20:31:58', '2026-03-07 20:31:58', 'approved', '2025-08-18 21:00:00', 'Night Window from Sidewalk Cinema by Kian Drift, carrying the Indie energy of Naad-e-Maan.', 'Night Window in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-08-18 00:00:00', '2025-08-18 21:00:00'),
(19, 4, 7, 'Moonlit Alaap', 'moonlit-alaap', 'Classical', 301, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3', 'https://placehold.co/1200x1200/0f172a/c084fc?text=Midnight%20Raga', 1, 4012500, '2026-03-07 20:31:58', '2026-03-07 20:31:58', 'approved', '2025-12-05 21:00:00', 'Moonlit Alaap from Midnight Raga by Aanya Raag, carrying the Classical energy of Naad-e-Maan.', 'Moonlit Alaap in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-12-05 00:00:00', '2025-12-05 21:00:00'),
(20, 4, 7, 'Saffron Echo', 'saffron-echo', 'Classical', 288, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3', 'https://placehold.co/1200x1200/0f172a/c084fc?text=Midnight%20Raga', 0, 4200000, '2026-03-07 20:31:59', '2026-03-07 20:31:59', 'approved', '2025-12-05 21:00:00', 'Saffron Echo from Midnight Raga by Aanya Raag, carrying the Classical energy of Naad-e-Maan.', 'Saffron Echo in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-12-05 00:00:00', '2025-12-05 21:00:00'),
(21, 4, 7, 'Dawn Taal', 'dawn-taal', 'Classical', 276, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3', 'https://placehold.co/1200x1200/0f172a/c084fc?text=Midnight%20Raga', 0, 4387500, '2026-03-07 20:32:00', '2026-03-07 20:32:00', 'approved', '2025-12-05 21:00:00', 'Dawn Taal from Midnight Raga by Aanya Raag, carrying the Classical energy of Naad-e-Maan.', 'Dawn Taal in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-12-05 00:00:00', '2025-12-05 21:00:00'),
(22, 4, 8, 'Blue Smoke Quartet', 'blue-smoke-quartet', 'Jazz', 254, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3', 'https://placehold.co/1200x1200/0f172a/f59e0b?text=Velvet%20Chamber', 1, 4575000, '2026-03-07 20:32:01', '2026-03-07 20:32:01', 'approved', '2025-07-11 21:00:00', 'Blue Smoke Quartet from Velvet Chamber by Aanya Raag, carrying the Jazz energy of Naad-e-Maan.', 'Blue Smoke Quartet in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-07-11 00:00:00', '2025-07-11 21:00:00'),
(23, 4, 8, 'Paper Lanterns', 'paper-lanterns', 'Jazz', 247, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3', 'https://placehold.co/1200x1200/0f172a/f59e0b?text=Velvet%20Chamber', 0, 4762500, '2026-03-07 20:32:02', '2026-03-07 20:32:02', 'approved', '2025-07-11 21:00:00', 'Paper Lanterns from Velvet Chamber by Aanya Raag, carrying the Jazz energy of Naad-e-Maan.', 'Paper Lanterns in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-07-11 00:00:00', '2025-07-11 21:00:00'),
(24, 4, 8, 'River in 7/8', 'river-in-7-8', 'Jazz', 262, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3', 'https://placehold.co/1200x1200/0f172a/f59e0b?text=Velvet%20Chamber', 0, 4950000, '2026-03-07 20:32:02', '2026-03-07 20:32:02', 'approved', '2025-07-11 21:00:00', 'River in 7/8 from Velvet Chamber by Aanya Raag, carrying the Jazz energy of Naad-e-Maan.', 'River in 7/8 in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-07-11 00:00:00', '2025-07-11 21:00:00'),
(25, 5, 9, 'Cherry Neon', 'cherry-neon', 'Pop', 210, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3', 'https://placehold.co/1200x1200/0f172a/fb7185?text=Electric%20Bloom', 1, 5137500, '2026-03-07 20:32:03', '2026-03-07 20:32:03', 'approved', '2026-01-26 21:00:00', 'Cherry Neon from Electric Bloom by Leena Sol, carrying the Pop energy of Naad-e-Maan.', 'Cherry Neon in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-01-26 00:00:00', '2026-01-26 21:00:00'),
(26, 5, 9, 'Heartline', 'heartline', 'Pop', 205, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3', 'https://placehold.co/1200x1200/0f172a/fb7185?text=Electric%20Bloom', 0, 5325000, '2026-03-07 20:32:03', '2026-03-07 20:32:03', 'approved', '2026-01-26 21:00:00', 'Heartline from Electric Bloom by Leena Sol, carrying the Pop energy of Naad-e-Maan.', 'Heartline in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-01-26 00:00:00', '2026-01-26 21:00:00'),
(27, 5, 9, 'Summer on Repeat', 'summer-on-repeat', 'Pop', 218, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3', 'https://placehold.co/1200x1200/0f172a/fb7185?text=Electric%20Bloom', 0, 5512500, '2026-03-07 20:32:05', '2026-03-07 20:32:05', 'approved', '2026-01-26 21:00:00', 'Summer on Repeat from Electric Bloom by Leena Sol, carrying the Pop energy of Naad-e-Maan.', 'Summer on Repeat in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2026-01-26 00:00:00', '2026-01-26 21:00:00'),
(28, 5, 10, 'Chrome Guitars', 'chrome-guitars', 'Rock', 244, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3', 'https://placehold.co/1200x1200/0f172a/f97316?text=North%20of%20Noise', 1, 5700000, '2026-03-07 20:32:06', '2026-03-07 20:32:06', 'approved', '2025-09-27 21:00:00', 'Chrome Guitars from North of Noise by Leena Sol, carrying the Rock energy of Naad-e-Maan.', 'Chrome Guitars in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-09-27 00:00:00', '2025-09-27 21:00:00'),
(29, 5, 10, 'Stadium Rain', 'stadium-rain', 'Rock', 252, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3', 'https://placehold.co/1200x1200/0f172a/f97316?text=North%20of%20Noise', 0, 5887500, '2026-03-07 20:32:06', '2026-03-07 20:32:06', 'approved', '2025-09-27 21:00:00', 'Stadium Rain from North of Noise by Leena Sol, carrying the Rock energy of Naad-e-Maan.', 'Stadium Rain in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-09-27 00:00:00', '2025-09-27 21:00:00'),
(30, 5, 10, 'Fire Exit', 'fire-exit', 'Rock', 239, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3', 'https://placehold.co/1200x1200/0f172a/f97316?text=North%20of%20Noise', 0, 6075000, '2026-03-07 20:32:07', '2026-03-07 20:32:07', 'approved', '2025-09-27 21:00:00', 'Fire Exit from North of Noise by Leena Sol, carrying the Rock energy of Naad-e-Maan.', 'Fire Exit in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', '2025-09-27 00:00:00', '2025-09-27 21:00:00');

-- Data for tracks
INSERT INTO `tracks` (`id`, `artist_profile_id`, `genre_id`, `album_id`, `title`, `slug`, `description`, `lyrics`, `duration`, `audio_url`, `cover_image_url`, `release_date`, `status`, `is_published`, `is_featured`, `published_at`, `views_count`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Echoes in Blue', 'echoes-in-blue', 'Echoes in Blue from Aurora Veil by Mira Skye, carrying the Neo-Soul energy of Naad-e-Maan.', 'Echoes in Blue in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 280, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3', 'https://placehold.co/1200x1200/0f172a/ec4899?text=Aurora%20Veil', '2026-02-21 00:00:00', 'published', 1, 1, '2026-02-21 21:00:00', 465000, '2026-03-07 17:58:21', '2026-03-07 20:31:46'),
(2, 1, 1, 1, 'Midnight Bloom', 'midnight-bloom', 'Midnight Bloom from Aurora Veil by Mira Skye, carrying the Neo-Soul energy of Naad-e-Maan.', 'Midnight Bloom in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 216, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3', 'https://placehold.co/1200x1200/0f172a/ec4899?text=Aurora%20Veil', '2026-02-21 00:00:00', 'published', 1, 0, '2026-02-21 21:00:00', 610000, '2026-03-07 17:58:23', '2026-03-07 20:31:46'),
(3, 2, 2, 2, 'Velvet Pulse', 'velvet-pulse-track', 'Velvet Pulse from Velvet Pulse by Rehan Pulse, carrying the Electronic energy of Naad-e-Maan.', 'Velvet Pulse in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 242, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3', 'https://placehold.co/1200x1200/0f172a/38bdf8?text=Velvet%20Pulse', '2026-01-14 00:00:00', 'published', 1, 1, '2026-01-14 21:00:00', 1335000, '2026-03-07 17:58:25', '2026-03-07 20:31:50'),
(4, 3, 3, 3, 'Rhythm Theory', 'rhythm-theory-track', 'Rhythm Theory from Rhythm Theory by Kian Drift, carrying the Hip Hop energy of Naad-e-Maan.', 'Rhythm Theory in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 236, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3', 'https://placehold.co/1200x1200/0f172a/22d3ee?text=Rhythm%20Theory', '2025-11-30 00:00:00', 'published', 1, 1, '2025-11-30 21:00:00', 2205000, '2026-03-07 17:58:26', '2026-03-07 20:31:54'),
(5, 3, 3, 3, 'City Haze', 'city-haze', 'City Haze from Rhythm Theory by Kian Drift, carrying the Hip Hop energy of Naad-e-Maan.', 'City Haze in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 224, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3', 'https://placehold.co/1200x1200/0f172a/22d3ee?text=Rhythm%20Theory', '2025-11-30 00:00:00', 'published', 1, 0, '2025-11-30 21:00:00', 2350000, '2026-03-07 17:58:32', '2026-03-07 20:31:55'),
(6, 2, 2, 2, 'Neon Dust', 'neon-dust', 'Neon Dust from Velvet Pulse by Rehan Pulse, carrying the Electronic energy of Naad-e-Maan.', 'Neon Dust in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 250, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3', 'https://placehold.co/1200x1200/0f172a/38bdf8?text=Velvet%20Pulse', '2026-01-14 00:00:00', 'published', 1, 0, '2026-01-14 21:00:00', 1480000, '2026-03-07 17:58:34', '2026-03-07 20:31:51'),
(7, 1, 1, 1, 'Silver Thread', 'silver-thread', 'Silver Thread from Aurora Veil by Mira Skye, carrying the Neo-Soul energy of Naad-e-Maan.', 'Silver Thread in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 248, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3', 'https://placehold.co/1200x1200/0f172a/ec4899?text=Aurora%20Veil', '2026-02-21 00:00:00', 'published', 1, 0, '2026-02-21 21:00:00', 755000, '2026-03-07 19:08:20', '2026-03-07 20:31:47'),
(8, 1, 10, 4, 'Satin Static', 'satin-static', 'Satin Static from Afterglow Letters by Mira Skye, carrying the R&B energy of Naad-e-Maan.', 'Satin Static in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 234, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3', 'https://placehold.co/1200x1200/0f172a/60a5fa?text=Afterglow%20Letters', '2025-11-08 00:00:00', 'published', 1, 1, '2025-11-08 21:00:00', 900000, '2026-03-07 20:31:48', '2026-03-07 20:31:48'),
(9, 1, 10, 4, 'Love on Delay', 'love-on-delay', 'Love on Delay from Afterglow Letters by Mira Skye, carrying the R&B energy of Naad-e-Maan.', 'Love on Delay in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 227, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3', 'https://placehold.co/1200x1200/0f172a/60a5fa?text=Afterglow%20Letters', '2025-11-08 00:00:00', 'published', 1, 0, '2025-11-08 21:00:00', 1045000, '2026-03-07 20:31:49', '2026-03-07 20:31:49'),
(10, 1, 10, 4, 'Gold Frequency', 'gold-frequency', 'Gold Frequency from Afterglow Letters by Mira Skye, carrying the R&B energy of Naad-e-Maan.', 'Gold Frequency in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 241, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3', 'https://placehold.co/1200x1200/0f172a/60a5fa?text=Afterglow%20Letters', '2025-11-08 00:00:00', 'published', 1, 0, '2025-11-08 21:00:00', 1190000, '2026-03-07 20:31:49', '2026-03-07 20:31:49'),
(11, 2, 2, 2, 'Glass Voltage', 'glass-voltage', 'Glass Voltage from Velvet Pulse by Rehan Pulse, carrying the Electronic energy of Naad-e-Maan.', 'Glass Voltage in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 238, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3', 'https://placehold.co/1200x1200/0f172a/38bdf8?text=Velvet%20Pulse', '2026-01-14 00:00:00', 'published', 1, 0, '2026-01-14 21:00:00', 1625000, '2026-03-07 20:31:51', '2026-03-07 20:31:51'),
(12, 2, 8, 5, 'Low Tide Light', 'low-tide-light', 'Low Tide Light from Silent Horizons by Rehan Pulse, carrying the Ambient energy of Naad-e-Maan.', 'Low Tide Light in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 264, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3', 'https://placehold.co/1200x1200/0f172a/2dd4bf?text=Silent%20Horizons', '2025-10-02 00:00:00', 'published', 1, 1, '2025-10-02 21:00:00', 1770000, '2026-03-07 20:31:52', '2026-03-07 20:31:52'),
(13, 2, 8, 5, 'Drift Signal', 'drift-signal', 'Drift Signal from Silent Horizons by Rehan Pulse, carrying the Ambient energy of Naad-e-Maan.', 'Drift Signal in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 272, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3', 'https://placehold.co/1200x1200/0f172a/2dd4bf?text=Silent%20Horizons', '2025-10-02 00:00:00', 'published', 1, 0, '2025-10-02 21:00:00', 1915000, '2026-03-07 20:31:53', '2026-03-07 20:31:53'),
(14, 2, 8, 5, 'Skyline Hush', 'skyline-hush', 'Skyline Hush from Silent Horizons by Rehan Pulse, carrying the Ambient energy of Naad-e-Maan.', 'Skyline Hush in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 289, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3', 'https://placehold.co/1200x1200/0f172a/2dd4bf?text=Silent%20Horizons', '2025-10-02 00:00:00', 'published', 1, 0, '2025-10-02 21:00:00', 2060000, '2026-03-07 20:31:54', '2026-03-07 20:31:54'),
(15, 3, 3, 3, 'Corner Cipher', 'corner-cipher', 'Corner Cipher from Rhythm Theory by Kian Drift, carrying the Hip Hop energy of Naad-e-Maan.', 'Corner Cipher in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 219, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-15.mp3', 'https://placehold.co/1200x1200/0f172a/22d3ee?text=Rhythm%20Theory', '2025-11-30 00:00:00', 'published', 1, 0, '2025-11-30 21:00:00', 2495000, '2026-03-07 20:31:56', '2026-03-07 20:31:56'),
(16, 3, 9, 6, 'Backseat Monologue', 'backseat-monologue', 'Backseat Monologue from Sidewalk Cinema by Kian Drift, carrying the Indie energy of Naad-e-Maan.', 'Backseat Monologue in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 230, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-16.mp3', 'https://placehold.co/1200x1200/0f172a/a3e635?text=Sidewalk%20Cinema', '2025-08-18 00:00:00', 'published', 1, 1, '2025-08-18 21:00:00', 2640000, '2026-03-07 20:31:57', '2026-03-07 20:31:57'),
(17, 3, 9, 6, 'Alley Chorus', 'alley-chorus', 'Alley Chorus from Sidewalk Cinema by Kian Drift, carrying the Indie energy of Naad-e-Maan.', 'Alley Chorus in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 213, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3', 'https://placehold.co/1200x1200/0f172a/a3e635?text=Sidewalk%20Cinema', '2025-08-18 00:00:00', 'published', 1, 0, '2025-08-18 21:00:00', 2785000, '2026-03-07 20:31:57', '2026-03-07 20:31:57'),
(18, 3, 9, 6, 'Night Window', 'night-window', 'Night Window from Sidewalk Cinema by Kian Drift, carrying the Indie energy of Naad-e-Maan.', 'Night Window in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 221, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3', 'https://placehold.co/1200x1200/0f172a/a3e635?text=Sidewalk%20Cinema', '2025-08-18 00:00:00', 'published', 1, 0, '2025-08-18 21:00:00', 2930000, '2026-03-07 20:31:58', '2026-03-07 20:31:58'),
(19, 4, 7, 7, 'Moonlit Alaap', 'moonlit-alaap', 'Moonlit Alaap from Midnight Raga by Aanya Raag, carrying the Classical energy of Naad-e-Maan.', 'Moonlit Alaap in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 301, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-3.mp3', 'https://placehold.co/1200x1200/0f172a/c084fc?text=Midnight%20Raga', '2025-12-05 00:00:00', 'published', 1, 1, '2025-12-05 21:00:00', 3075000, '2026-03-07 20:31:59', '2026-03-07 20:31:59'),
(20, 4, 7, 7, 'Saffron Echo', 'saffron-echo', 'Saffron Echo from Midnight Raga by Aanya Raag, carrying the Classical energy of Naad-e-Maan.', 'Saffron Echo in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 288, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-4.mp3', 'https://placehold.co/1200x1200/0f172a/c084fc?text=Midnight%20Raga', '2025-12-05 00:00:00', 'published', 1, 0, '2025-12-05 21:00:00', 3220000, '2026-03-07 20:32:00', '2026-03-07 20:32:00'),
(21, 4, 7, 7, 'Dawn Taal', 'dawn-taal', 'Dawn Taal from Midnight Raga by Aanya Raag, carrying the Classical energy of Naad-e-Maan.', 'Dawn Taal in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 276, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-5.mp3', 'https://placehold.co/1200x1200/0f172a/c084fc?text=Midnight%20Raga', '2025-12-05 00:00:00', 'published', 1, 0, '2025-12-05 21:00:00', 3365000, '2026-03-07 20:32:01', '2026-03-07 20:32:01'),
(22, 4, 4, 8, 'Blue Smoke Quartet', 'blue-smoke-quartet', 'Blue Smoke Quartet from Velvet Chamber by Aanya Raag, carrying the Jazz energy of Naad-e-Maan.', 'Blue Smoke Quartet in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 254, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3', 'https://placehold.co/1200x1200/0f172a/f59e0b?text=Velvet%20Chamber', '2025-07-11 00:00:00', 'published', 1, 1, '2025-07-11 21:00:00', 3510000, '2026-03-07 20:32:01', '2026-03-07 20:32:01'),
(23, 4, 4, 8, 'Paper Lanterns', 'paper-lanterns', 'Paper Lanterns from Velvet Chamber by Aanya Raag, carrying the Jazz energy of Naad-e-Maan.', 'Paper Lanterns in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 247, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-7.mp3', 'https://placehold.co/1200x1200/0f172a/f59e0b?text=Velvet%20Chamber', '2025-07-11 00:00:00', 'published', 1, 0, '2025-07-11 21:00:00', 3655000, '2026-03-07 20:32:02', '2026-03-07 20:32:02'),
(24, 4, 4, 8, 'River in 7/8', 'river-in-7-8', 'River in 7/8 from Velvet Chamber by Aanya Raag, carrying the Jazz energy of Naad-e-Maan.', 'River in 7/8 in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 262, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-8.mp3', 'https://placehold.co/1200x1200/0f172a/f59e0b?text=Velvet%20Chamber', '2025-07-11 00:00:00', 'published', 1, 0, '2025-07-11 21:00:00', 3800000, '2026-03-07 20:32:02', '2026-03-07 20:32:02'),
(25, 5, 6, 9, 'Cherry Neon', 'cherry-neon', 'Cherry Neon from Electric Bloom by Leena Sol, carrying the Pop energy of Naad-e-Maan.', 'Cherry Neon in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 210, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-9.mp3', 'https://placehold.co/1200x1200/0f172a/fb7185?text=Electric%20Bloom', '2026-01-26 00:00:00', 'published', 1, 1, '2026-01-26 21:00:00', 3945000, '2026-03-07 20:32:03', '2026-03-07 20:32:03'),
(26, 5, 6, 9, 'Heartline', 'heartline', 'Heartline from Electric Bloom by Leena Sol, carrying the Pop energy of Naad-e-Maan.', 'Heartline in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 205, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-10.mp3', 'https://placehold.co/1200x1200/0f172a/fb7185?text=Electric%20Bloom', '2026-01-26 00:00:00', 'published', 1, 0, '2026-01-26 21:00:00', 4090000, '2026-03-07 20:32:04', '2026-03-07 20:32:04'),
(27, 5, 6, 9, 'Summer on Repeat', 'summer-on-repeat', 'Summer on Repeat from Electric Bloom by Leena Sol, carrying the Pop energy of Naad-e-Maan.', 'Summer on Repeat in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 218, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3', 'https://placehold.co/1200x1200/0f172a/fb7185?text=Electric%20Bloom', '2026-01-26 00:00:00', 'published', 1, 0, '2026-01-26 21:00:00', 4235000, '2026-03-07 20:32:05', '2026-03-07 20:32:05'),
(28, 5, 5, 10, 'Chrome Guitars', 'chrome-guitars', 'Chrome Guitars from North of Noise by Leena Sol, carrying the Rock energy of Naad-e-Maan.', 'Chrome Guitars in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 244, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-12.mp3', 'https://placehold.co/1200x1200/0f172a/f97316?text=North%20of%20Noise', '2025-09-27 00:00:00', 'published', 1, 1, '2025-09-27 21:00:00', 4380000, '2026-03-07 20:32:06', '2026-03-07 20:32:06'),
(29, 5, 5, 10, 'Stadium Rain', 'stadium-rain', 'Stadium Rain from North of Noise by Leena Sol, carrying the Rock energy of Naad-e-Maan.', 'Stadium Rain in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 252, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-13.mp3', 'https://placehold.co/1200x1200/0f172a/f97316?text=North%20of%20Noise', '2025-09-27 00:00:00', 'published', 1, 0, '2025-09-27 21:00:00', 4525000, '2026-03-07 20:32:07', '2026-03-07 20:32:07'),
(30, 5, 5, 10, 'Fire Exit', 'fire-exit', 'Fire Exit from North of Noise by Leena Sol, carrying the Rock energy of Naad-e-Maan.', 'Fire Exit in the afterglow.\r\nThe waveform bends and the room keeps breathing.\r\nNaad-e-Maan holds the pulse until the lights fade.', 239, 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-14.mp3', 'https://placehold.co/1200x1200/0f172a/f97316?text=North%20of%20Noise', '2025-09-27 00:00:00', 'published', 1, 0, '2025-09-27 21:00:00', 4670000, '2026-03-07 20:32:08', '2026-03-07 20:32:08');

-- Data for users
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `api_token`, `avatar_url`, `remember_token`, `created_at`, `updated_at`, `role`, `headline`, `bio`) VALUES
(1, 'Naad Listener', 'listener@naademaan.test', NULL, '$2y$12$c3sl81cNZ5IP5/gBesktbufXyOEtydvwAggjh6Nx21tf8/sCFKqvG', NULL, 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80', NULL, '2026-03-07 17:57:56', '2026-03-07 20:02:14', 'listener', 'Night-drive curator', 'Collects late-night electronic, hip hop, and soul frequencies.'),
(2, 'Mira Skye', 'mira@naademaan.test', NULL, '$2y$12$aLBAFLGcewSSPdhOYswEveC38LcPSD1oWbzPSI6C6e3Bu8FUn.VsW', NULL, 'https://placehold.co/500x500/020617/f59e0b?text=Mira%20Skye', NULL, '2026-03-07 17:57:59', '2026-03-07 20:31:31', 'creator', 'Neo-soul architect', 'Builds intimate songs with luminous bass, warm vocals, and widescreen atmosphere.'),
(3, 'Naad Admin', 'admin@naademaan.test', NULL, '$2y$12$JeaKOYqEjYvnDdviqe8AZuFH/BFVrdELiOVi.mBPhMODKRNY8AR.u', NULL, 'https://placehold.co/500x500/020617/f472b6?text=Naad%20Admin', NULL, '2026-03-07 19:08:12', '2026-03-07 20:31:30', 'admin', 'Operations lead', 'Coordinates homepage campaigns, user operations, and platform-quality reviews.'),
(4, 'Naad Super Admin', 'superadmin@naademaan.test', NULL, '$2y$12$CiWMA2knvuBWPf4qiixI4eJAhq5pnViKGCwNdo68sN/XeSyKJ6jT2', NULL, 'https://placehold.co/500x500/020617/22d3ee?text=Naad%20Super%20Admin', NULL, '2026-03-07 20:31:28', '2026-03-07 20:31:28', 'super_admin', 'Platform architect', 'Controls system-wide curation, moderation, and release strategy for Naad-e-Maan.'),
(5, 'Rehan Pulse', 'rehan@naademaan.test', NULL, '$2y$12$Dohp1lfPzuLNohlZQU/rYO7ednvaUssxFyjqou8D2R7E1ecNY3Ahu', NULL, 'https://placehold.co/500x500/020617/34d399?text=Rehan%20Pulse', NULL, '2026-03-07 20:31:32', '2026-03-07 20:31:32', 'creator', 'Electronic pressure builder', 'Designs glowing club records and cinematic low-end for midnight listening sessions.'),
(6, 'Kian Drift', 'kian@naademaan.test', NULL, '$2y$12$CavJqG.FrYHnE9b1TEDznOydLwIKc.IJJuO6PQK1ltzbmMb1G6h5O', NULL, 'https://placehold.co/500x500/020617/60a5fa?text=Kian%20Drift', NULL, '2026-03-07 20:31:33', '2026-03-07 20:31:33', 'creator', 'Hip hop storyteller', 'Writes sharp verses around city detail, nocturnal textures, and melodic hooks.'),
(7, 'Aanya Raag', 'aanya@naademaan.test', NULL, '$2y$12$6o/QdtWwmiZZOl6ElWmF6ORo/fQY.S3F0Cj9uy2mE36qMYPfQ6vf6', NULL, 'https://placehold.co/500x500/020617/f97316?text=Aanya%20Raag', NULL, '2026-03-07 20:31:34', '2026-03-07 20:31:34', 'creator', 'Classical crossover composer', 'Brings raga phrasing, chamber dynamics, and modern production into one stage identity.'),
(8, 'Leena Sol', 'leena@naademaan.test', NULL, '$2y$12$R0EAwzHPyXXQAFFXCqmS/OmhkM7FeZmTZ9TheGyrjoSh9RT/EDxVS', NULL, 'https://placehold.co/500x500/020617/e879f9?text=Leena%20Sol', NULL, '2026-03-07 20:31:35', '2026-03-07 20:31:35', 'creator', 'Pop radiance writer', 'Combines bright hooks, emotional toplines, and live-band energy for premium pop releases.');

-- Indexes for albums
ALTER TABLE `albums` ADD UNIQUE KEY `albums_slug_unique` (`slug`);

-- Indexes for artist_follows
ALTER TABLE `artist_follows` ADD UNIQUE KEY `artist_follows_user_id_artist_id_unique` (`user_id`, `artist_id`);

-- Indexes for artist_profiles
ALTER TABLE `artist_profiles` ADD UNIQUE KEY `artist_profiles_slug_unique` (`slug`);

-- Indexes for artists
ALTER TABLE `artists` ADD UNIQUE KEY `artists_slug_unique` (`slug`);

-- Indexes for banners
ALTER TABLE `banners` ADD UNIQUE KEY `banners_slug_unique` (`slug`);

-- Indexes for cache
ALTER TABLE `cache` ADD KEY `cache_expiration_index` (`expiration`);

-- Indexes for cache_locks
ALTER TABLE `cache_locks` ADD KEY `cache_locks_expiration_index` (`expiration`);

-- Indexes for failed_jobs
ALTER TABLE `failed_jobs` ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

-- Indexes for favorites
ALTER TABLE `favorites` ADD UNIQUE KEY `favorites_user_id_track_id_unique` (`user_id`, `track_id`);
ALTER TABLE `favorites` ADD UNIQUE KEY `favorites_user_id_song_id_unique` (`user_id`, `song_id`);

-- Indexes for follows
ALTER TABLE `follows` ADD UNIQUE KEY `follows_user_id_artist_profile_id_unique` (`user_id`, `artist_profile_id`);

-- Indexes for genres
ALTER TABLE `genres` ADD UNIQUE KEY `genres_slug_unique` (`slug`);
ALTER TABLE `genres` ADD UNIQUE KEY `genres_name_unique` (`name`);

-- Indexes for jobs
ALTER TABLE `jobs` ADD KEY `jobs_queue_index` (`queue`);

-- Indexes for platform_settings
ALTER TABLE `platform_settings` ADD UNIQUE KEY `platform_settings_key_unique` (`key`);

-- Indexes for playlist_track
ALTER TABLE `playlist_track` ADD UNIQUE KEY `playlist_track_playlist_id_track_id_unique` (`playlist_id`, `track_id`);

-- Indexes for playlists
ALTER TABLE `playlists` ADD UNIQUE KEY `playlists_slug_unique` (`slug`);

-- Indexes for sessions
ALTER TABLE `sessions` ADD KEY `sessions_last_activity_index` (`last_activity`);
ALTER TABLE `sessions` ADD KEY `sessions_user_id_index` (`user_id`);

-- Indexes for site_settings
ALTER TABLE `site_settings` ADD UNIQUE KEY `site_settings_key_unique` (`key`);

-- Indexes for songs
ALTER TABLE `songs` ADD UNIQUE KEY `songs_slug_unique` (`slug`);

-- Indexes for tracks
ALTER TABLE `tracks` ADD UNIQUE KEY `tracks_slug_unique` (`slug`);

-- Indexes for users
ALTER TABLE `users` ADD UNIQUE KEY `users_api_token_unique` (`api_token`);
ALTER TABLE `users` ADD UNIQUE KEY `users_email_unique` (`email`);

-- Foreign keys for albums
ALTER TABLE `albums` ADD CONSTRAINT `albums_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `albums` ADD CONSTRAINT `albums_artist_profile_id_foreign` FOREIGN KEY (`artist_profile_id`) REFERENCES `artist_profiles` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `albums` ADD CONSTRAINT `albums_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for artist_follows
ALTER TABLE `artist_follows` ADD CONSTRAINT `artist_follows_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `artist_follows` ADD CONSTRAINT `artist_follows_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for artist_profiles
ALTER TABLE `artist_profiles` ADD CONSTRAINT `artist_profiles_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `artist_profiles` ADD CONSTRAINT `artist_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- Foreign keys for artists
ALTER TABLE `artists` ADD CONSTRAINT `artists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

-- Foreign keys for favorites
ALTER TABLE `favorites` ADD CONSTRAINT `favorites_track_id_foreign` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `favorites` ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `favorites` ADD CONSTRAINT `favorites_song_id_foreign` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for follows
ALTER TABLE `follows` ADD CONSTRAINT `follows_artist_profile_id_foreign` FOREIGN KEY (`artist_profile_id`) REFERENCES `artist_profiles` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `follows` ADD CONSTRAINT `follows_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for playlist_track
ALTER TABLE `playlist_track` ADD CONSTRAINT `playlist_track_track_id_foreign` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `playlist_track` ADD CONSTRAINT `playlist_track_playlist_id_foreign` FOREIGN KEY (`playlist_id`) REFERENCES `playlists` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for playlists
ALTER TABLE `playlists` ADD CONSTRAINT `playlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for songs
ALTER TABLE `songs` ADD CONSTRAINT `songs_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `songs` ADD CONSTRAINT `songs_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for streams
ALTER TABLE `streams` ADD CONSTRAINT `streams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `streams` ADD CONSTRAINT `streams_song_id_foreign` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for track_views
ALTER TABLE `track_views` ADD CONSTRAINT `track_views_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `track_views` ADD CONSTRAINT `track_views_track_id_foreign` FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Foreign keys for tracks
ALTER TABLE `tracks` ADD CONSTRAINT `tracks_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `tracks` ADD CONSTRAINT `tracks_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `tracks` ADD CONSTRAINT `tracks_artist_profile_id_foreign` FOREIGN KEY (`artist_profile_id`) REFERENCES `artist_profiles` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

-- Auto increment for albums
ALTER TABLE `albums` AUTO_INCREMENT = 11;

-- Auto increment for artist_follows
ALTER TABLE `artist_follows` AUTO_INCREMENT = 2;

-- Auto increment for artist_profiles
ALTER TABLE `artist_profiles` AUTO_INCREMENT = 6;

-- Auto increment for artists
ALTER TABLE `artists` AUTO_INCREMENT = 6;

-- Auto increment for banners
ALTER TABLE `banners` AUTO_INCREMENT = 6;

-- Auto increment for failed_jobs
ALTER TABLE `failed_jobs` AUTO_INCREMENT = 1;

-- Auto increment for favorites
ALTER TABLE `favorites` AUTO_INCREMENT = 3;

-- Auto increment for follows
ALTER TABLE `follows` AUTO_INCREMENT = 2;

-- Auto increment for genres
ALTER TABLE `genres` AUTO_INCREMENT = 11;

-- Auto increment for jobs
ALTER TABLE `jobs` AUTO_INCREMENT = 1;

-- Auto increment for migrations
ALTER TABLE `migrations` AUTO_INCREMENT = 19;

-- Auto increment for platform_settings
ALTER TABLE `platform_settings` AUTO_INCREMENT = 11;

-- Auto increment for playlist_track
ALTER TABLE `playlist_track` AUTO_INCREMENT = 5;

-- Auto increment for playlists
ALTER TABLE `playlists` AUTO_INCREMENT = 2;

-- Auto increment for site_settings
ALTER TABLE `site_settings` AUTO_INCREMENT = 11;

-- Auto increment for songs
ALTER TABLE `songs` AUTO_INCREMENT = 31;

-- Auto increment for streams
ALTER TABLE `streams` AUTO_INCREMENT = 1;

-- Auto increment for track_views
ALTER TABLE `track_views` AUTO_INCREMENT = 1;

-- Auto increment for tracks
ALTER TABLE `tracks` AUTO_INCREMENT = 31;

-- Auto increment for users
ALTER TABLE `users` AUTO_INCREMENT = 9;

SET FOREIGN_KEY_CHECKS = 1;
