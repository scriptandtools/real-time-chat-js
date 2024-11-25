-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table rtc.tbl_admin: ~0 rows (approximately)
INSERT INTO `tbl_admin` (`id`, `username`, `email`, `password`) VALUES
	(1, 'hassan', 'admin22@gmail.com', '$2y$10$OoG0J.L5BXstF27v1nVKeODD.Rwt21DAAruu1ahy0aZIZTjbE9suG');

-- Dumping data for table rtc.tbl_chat: ~9 rows (approximately)
INSERT INTO `tbl_chat` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`, `file`) VALUES
	(196, 1, 2, 'hi', '2024-11-25 10:43:14', NULL),
	(197, 1, 2, 'hi', '2024-11-25 10:48:47', NULL),
	(198, 1, 2, 'jnd', '2024-11-25 10:48:48', NULL),
	(199, 1, 2, 'kanind', '2024-11-25 10:48:49', NULL),
	(200, 1, 2, 'jua', '2024-11-25 10:48:51', NULL),
	(201, 1, 2, 'jwu', '2024-11-25 10:48:52', NULL),
	(202, 1, 3, 'hi', '2024-11-25 11:04:11', NULL),
	(203, 1, 3, 'jnsa', '2024-11-25 11:04:13', NULL),
	(204, 1, 4, 'h', '2024-11-25 11:15:22', NULL);

-- Dumping data for table rtc.tbl_users: ~4 rows (approximately)
INSERT INTO `tbl_users` (`id`, `username`, `email`, `password`, `image`, `status`, `role`) VALUES
	(1, 'smith', 'hassanehsan739@gmail.com', '$2y$10$tJ76IgHfkJLifQfIUCSoa.6yjXjGKWWdVPe3eBipcesUOnr2vKkrG', 'https://randomuser.me/api/portraits/men/9.jpg', 'online', 'user'),
	(2, 'Robet', 'robet@gmail.com', '$2y$10$Hs4sa.sDUIhR2eHJmVx6huiHl/1AnJrMB7QsD3T4zl7MY4TjPsdTW', 'https://randomuser.me/api/portraits/men/89.jpg', 'online', 'user'),
	(3, 'alex', 'alex@gmail.com', '$2y$10$qfle3Y2dQ0LjRfT6RMHGK.IvLECVh9mMlXwXftOKXhSXuzRt2Zrr2', 'https://randomuser.me/api/portraits/men/74.jpg', 'online', 'user'),
	(4, 'Mike', 'mike@gmail.com', '$2y$10$UGXqCrLig.b773Htozh4yOYMZQtkb2dTRYPhUl9qtql2sNqvp0b2e', 'https://randomuser.me/api/portraits/men/13.jpg', 'online', 'user'),
	(6, 'alexzander', 'alexzander2222@gmail.com', '$2y$10$/jwzW41MgvCMY0HuEBc5Z.Di01uOu56eV5k4qW4kx/TjivjfVEUAy', NULL, 'online', 'user');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
