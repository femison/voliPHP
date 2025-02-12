-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: voli
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `ProjectID` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectName` varchar(50) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ProjectID`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (19,'Обучение детей','2023-04-01','2023-04-30','Завершен'),(20,'Поддержка пожилых людей','2023-05-01','2024-09-29','Активен'),(81,'Поддержка больных детей','2024-05-01','2024-06-13','Активен'),(86,'Уборка больницы','2024-05-14','2024-05-22','Активен'),(88,'Помощь в благотворительных мероприятиях','2024-05-15','2024-11-14','Отменен'),(89,'Поддержка людей с ограниченными возможностями','2024-01-22','2024-10-22','Завершен'),(90,'Уборка территории ','2024-05-01','2024-05-31','Отменен'),(91,'Проект без задач','2024-09-01','2024-09-05','Активен'),(103,'Новый проект','2025-02-07','2025-02-21','Завершен');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taskinfo`
--

DROP TABLE IF EXISTS `taskinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taskinfo` (
  `TaskID` int(11) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Date` date DEFAULT NULL,
  PRIMARY KEY (`TaskID`),
  CONSTRAINT `taskinfo_ibfk_1` FOREIGN KEY (`TaskID`) REFERENCES `tasks` (`TaskID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taskinfo`
--

LOCK TABLES `taskinfo` WRITE;
/*!40000 ALTER TABLE `taskinfo` DISABLE KEYS */;
INSERT INTO `taskinfo` VALUES (122,'Местная аптека','2024-05-07'),(128,'Больница №4','2024-05-15'),(129,'DNS','2024-05-01'),(130,'Кабинет 304','2023-04-14'),(132,'Больница№4','2024-05-15'),(133,'Парк \"Юность\"','2024-05-30'),(134,'Очистка пляжа в Зеленоградске','2024-05-31'),(135,'Центр помощи людям с  ограниченными возможностями ','2024-02-01'),(137,'кадинет 103','2024-09-19'),(138,'кабинет 103','2024-09-19'),(139,'качбинет 103','2024-09-19');
/*!40000 ALTER TABLE `taskinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `TaskID` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`TaskID`),
  KEY `FK_Projects_Tasks` (`ProjectID`),
  CONSTRAINT `FK_Projects_Tasks` FOREIGN KEY (`ProjectID`) REFERENCES `projects` (`ProjectID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (122,81,'Покупка лекарства','Активен'),(128,86,'Сбор мусора','Активен'),(129,20,'Закуп техники','Активен'),(130,19,'Основы работы за ПК','Планируется'),(132,86,'Вынос мешков на свалку','Активен'),(133,88,'Посадка деревьев','Активен'),(134,88,'Уборка прибрежных территорий','Планируется'),(135,89,'Сопровождение','Активен'),(137,19,'Обучение html','Активен'),(138,19,'обучение CSS','Активен'),(139,19,'обучение JS','Активен');
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tasks`
--

DROP TABLE IF EXISTS `user_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tasks` (
  `UserTaskID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `TaskID` int(11) DEFAULT NULL,
  `ProjectID` int(11) DEFAULT NULL,
  PRIMARY KEY (`UserTaskID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `user_tasks_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tasks`
--

LOCK TABLES `user_tasks` WRITE;
/*!40000 ALTER TABLE `user_tasks` DISABLE KEYS */;
INSERT INTO `user_tasks` VALUES (28,1,110,19),(30,22,128,86),(32,1,129,20),(35,24,135,89),(37,22,133,88),(38,22,122,81),(39,22,134,88),(40,24,134,88),(41,22,132,86),(51,28,129,20),(52,25,129,20),(53,25,122,81),(54,1,129,20),(55,24,129,20),(56,28,122,81),(57,28,135,89),(58,28,133,88),(59,28,134,88);
/*!40000 ALTER TABLE `user_tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usercredentials`
--

DROP TABLE IF EXISTS `usercredentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usercredentials` (
  `UserID` int(11) NOT NULL,
  `Login` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  CONSTRAINT `usercredentials_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usercredentials`
--

LOCK TABLES `usercredentials` WRITE;
/*!40000 ALTER TABLE `usercredentials` DISABLE KEYS */;
INSERT INTO `usercredentials` VALUES (1,'admin','$2y$10$c454mjVH9UCJ6Gq3FOj4NOz3bukLhr5lk6dio.eiCIdq9F/u9jiv2'),(2,'root','$2y$10$4yX4zpG4qbBJa3YaZGSfH.5iOi2c/LJISmN.gf/VXnVhU6rxdtPj6'),(22,'321','$2y$10$zWMU9Qaj3bcTwDAew5a5gOQJstRkG.TusAew2al1oQcbgbW9GfP5G'),(24,'123','$2y$10$UeZuTJ.ptfeKjwnaD6crMubR7R0LJT5GqYLARvk.CJdDi8FaArFO2'),(25,'vlad','$2y$10$//uTfEXvkIwa2vSOzebLMu6VWqbVg8G5M/hcz8qkVU0E/5NuBM0U6'),(28,'ilua','$2a$11$tBACzbeikX5XDiez/INHU.Dtk4p3b94RLuE3sIhdTR5bihsnLZX0S');
/*!40000 ALTER TABLE `usercredentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `Surname` varchar(255) DEFAULT NULL,
  `UserSkills` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Gender` enum('м','ж') DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `Role` enum('Волонтер','Администратор') DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Петр','Грызунов','Управление проектами','ivangry1539@gmail.ru','89114958794','2005-11-15','м','Толстикова 2','Администратор'),(2,'Мария','Петрова','Программирование','mpetrova@example.com','2345678901','1990-09-25','ж','ул. Мира, 20','Волонтер'),(22,'Тимур','Мамедов','Управление процессами','Timurka1337@gmail.com','89114869544','2005-02-16','м','Мореходная 14','Администратор'),(24,'Евгений','Кириешков','Фотограф','Evgh@gmail.com','89114765322','2004-02-21','м','Киевская 24','Волонтер'),(25,'Владислав','Игыч','ыффыыф','vlad@gmail.com','89224657348','2024-09-18','м','1С','Волонтер'),(27,'Иван','Грызунов','хихиха','ivangry1539@gmail.com','89114958796','2005-11-15','м','Ул.Толстикова д 2Б','Администратор'),(28,'Илья','Беляев','1С, Фотограф, Программист','iuha@gmail.com','89114958693','2005-07-01','м','Светлый','Волонтер');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_pending_approval`
--

DROP TABLE IF EXISTS `users_pending_approval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_pending_approval` (
  `RequestID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `ProjectID` int(11) DEFAULT NULL,
  `TaskID` int(11) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'В процессе',
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`RequestID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_pending_approval`
--

LOCK TABLES `users_pending_approval` WRITE;
/*!40000 ALTER TABLE `users_pending_approval` DISABLE KEYS */;
INSERT INTO `users_pending_approval` VALUES (7,24,81,122,'Отклонена','2024-09-17 22:23:49','2024-09-18 09:58:00'),(8,24,86,128,'Одобрена','2024-09-17 22:24:50','2024-09-18 09:58:38'),(9,24,86,132,'Отклонена','2024-09-17 22:26:42','2024-09-18 09:58:54'),(10,25,20,129,'Одобрена','2024-09-18 09:30:21','2024-10-21 11:39:10'),(11,25,81,122,'Одобрена','2024-09-18 09:30:24','2024-10-21 11:39:58'),(12,25,86,128,'Одобрена','2024-09-18 09:30:31','2024-09-18 09:57:35'),(13,25,86,132,'Одобрена','2024-09-18 09:30:35','2024-09-18 09:57:03'),(14,28,20,129,'В процессе','2024-10-21 11:29:51','2024-10-21 11:29:51'),(15,28,86,128,'В процессе','2024-10-21 11:30:00','2024-10-21 11:30:00'),(16,2,20,129,'В процессе','2024-10-21 11:40:37','2024-10-21 11:40:37');
/*!40000 ALTER TABLE `users_pending_approval` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-12 22:54:43
