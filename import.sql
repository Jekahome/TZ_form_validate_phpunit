-- mysql -u<user> -p<password> -P3306 --host 127.0.0.1 dbform < "import.sql"
CREATE DATABASE IF NOT EXISTS `dbname` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE dbform;
-- SHOW CREATE TABLE `users`;
-- DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS  users
(
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL  COMMENT 'name',
  `email` varchar(255) NOT NULL COMMENT 'email',
  `login` varchar(255) NOT NULL COMMENT 'login',
  `password` varchar(255) NOT NULL COMMENT 'password',
  `token` varchar(255) DEFAULT '' COMMENT 'token',
  `confirmed` BOOL DEFAULT 0 COMMENT 'confirmed',
  `create_time` int(10) UNSIGNED NOT NULL,
   UNIQUE KEY `email` (`email`),
   KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='users';

INSERT INTO users(name,email,login,password,token,confirmed,create_time) VALUES(:name,:email,:login,:password,;token,0,:create_time);