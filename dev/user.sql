DROP DATABASE IF EXISTS `downloadpage`;

GO

CREATE DATABASE IF NOT EXISTS `downloadpage`;

GO

USE `downloadpage`;

CREATE TABLE IF NOT EXISTS `downloadpage`.`users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';
GO

INSERT INTO users (user_name, user_password_hash, user_email)
  VALUES('dev', '$2y$10$chOQuinGR3cP8Dh/gokDUu3kkB3BTRhhRWtkORBrcz6TEtjaZNsxG', 'dev@example.com');