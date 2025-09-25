-- Deri Pazarı Projesi - Tam Veritabanı Kurulumu
-- Bu dosya install.php tarafından otomatik olarak kullanılır.
-- Versiyon: 1.2 (Tüm tabloları ve ilişkileri içerir)

--
-- Tablo yapısı: `categories`
--
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(75) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- `categories` tablosu için başlangıç verileri
INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Deri Ceketler', 'deri-ceketler'),
(2, 'Deri Çantalar', 'deri-cantalar'),
(3, 'Deri Ayakkabılar', 'deri-ayakkabilar'),
(4, 'Deri Cüzdanlar', 'deri-cuzdanlar'),
(5, 'Deri Kemerler', 'deri-kemerler'),
(6, 'Deri Aksesuarlar', 'deri-aksesuarlar');

--
-- Tablo yapısı: `users`
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('member','vendor','admin') NOT NULL DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo yapısı: `vendors`
--
CREATE TABLE `vendors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `store_description` text DEFAULT NULL,
  `store_logo` varchar(255) DEFAULT 'default_logo.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo yapısı: `products`
--
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `external_url` varchar(512) NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo yapısı: `favorites`
--
CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product_unique` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo yapısı: `tags`
--
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo yapısı: `product_tags`
--
CREATE TABLE `product_tags` (
  `product_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo yapısı: `settings`
--
CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- `settings` tablosu için başlangıç verileri
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Deri Pazarı'),
('site_description', 'El emeği, göz nuru deri ürünler...'),
('site_keywords', 'deri, ceket, çanta, el yapımı');

