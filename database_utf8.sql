-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: tech_agency
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `recovery_email` varchar(255) DEFAULT NULL,
  `recovery_phone` varchar(50) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `avatar_emoji` varchar(10) DEFAULT '?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','$2y$10$aHK7b.PZcYoSqTQqDNe.huZzoJESswkvSJIxHlwwl0K7/pMmfDLPu','2026-02-23 11:14:30','','','','≡ƒÄ»');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_field_translations`
--

DROP TABLE IF EXISTS `booking_field_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_field_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `label` varchar(200) NOT NULL,
  `placeholder` varchar(200) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_field_locale` (`field_id`,`locale`),
  CONSTRAINT `booking_field_translations_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `booking_fields` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_field_translations`
--

LOCK TABLES `booking_field_translations` WRITE;
/*!40000 ALTER TABLE `booking_field_translations` DISABLE KEYS */;
INSERT INTO `booking_field_translations` VALUES (1,1,'en','Full Name','Enter your full name'),(2,1,'ar','╪º┘ä╪º╪│┘à ╪º┘ä┘â╪º┘à┘ä','╪ú╪»╪«┘ä ╪º╪│┘à┘â ╪º┘ä┘â╪º┘à┘ä'),(3,2,'en','Email Address','Enter your email'),(4,2,'ar','╪º┘ä╪¿╪▒┘è╪» ╪º┘ä╪Ñ┘ä┘â╪¬╪▒┘ê┘å┘è','╪ú╪»╪«┘ä ╪¿╪▒┘è╪»┘â ╪º┘ä╪Ñ┘ä┘â╪¬╪▒┘ê┘å┘è'),(5,3,'en','Phone Number','Enter your phone number'),(6,3,'ar','╪▒┘é┘à ╪º┘ä┘ç╪º╪¬┘ü','╪ú╪»╪«┘ä ╪▒┘é┘à ┘ç╪º╪¬┘ü┘â'),(7,4,'en','Select Service','Choose a service'),(8,4,'ar','╪º╪«╪¬╪▒ ╪º┘ä╪«╪»┘à╪⌐','╪º╪«╪¬╪▒ ╪«╪»┘à╪⌐'),(9,5,'en','Preferred Date','Select a date'),(10,5,'ar','╪º┘ä╪¬╪º╪▒┘è╪« ╪º┘ä┘à┘ü╪╢┘ä','╪º╪«╪¬╪▒ ╪¬╪º╪▒┘è╪«╪º┘ï'),(11,6,'en','Project Details','Tell us about your project'),(12,6,'ar','╪¬┘ü╪º╪╡┘è┘ä ╪º┘ä┘à╪┤╪▒┘ê╪╣','╪ú╪«╪¿╪▒┘å╪º ╪╣┘å ┘à╪┤╪▒┘ê╪╣┘â'),(85,11,'en','XX',''),(86,11,'ar','XXXXX',''),(89,12,'en','XSX',''),(90,12,'ar','SXSXCS',''),(91,10,'en','SXSXS',''),(92,10,'ar','WDWD','');
/*!40000 ALTER TABLE `booking_field_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_fields`
--

DROP TABLE IF EXISTS `booking_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_name` varchar(100) NOT NULL,
  `field_type` enum('text','email','tel','date','select','textarea','number') DEFAULT 'text',
  `options` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_fields`
--

LOCK TABLES `booking_fields` WRITE;
/*!40000 ALTER TABLE `booking_fields` DISABLE KEYS */;
INSERT INTO `booking_fields` VALUES (1,'name','text','',1,1,0),(2,'email','email','',1,2,0),(3,'phone','tel','',0,3,0),(4,'service','select',NULL,1,4,1),(5,'preferred_date','date','',0,5,0),(6,'message','textarea',NULL,0,6,1),(7,'name','text',NULL,1,1,1),(8,'email','email',NULL,1,2,1),(9,'phone','tel',NULL,0,3,1),(10,'service','select','',1,4,0),(11,'preferred_date','date','',0,5,0),(12,'message','textarea','',0,6,0);
/*!40000 ALTER TABLE `booking_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `phone` varchar(50) DEFAULT '',
  `service` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `status` enum('new','viewed','contacted','completed','cancelled') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `extra_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra_fields`)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,'JSDJSADS','DMAADWH@FNDN.COM','456456456','Web Engineering','',NULL,'new','2026-02-23 18:58:36','2026-02-23 18:58:36',NULL);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_messages`
--

DROP TABLE IF EXISTS `chatbot_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `sender` enum('bot','user') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `chatbot_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chatbot_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_messages`
--

LOCK TABLES `chatbot_messages` WRITE;
/*!40000 ALTER TABLE `chatbot_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `chatbot_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_node_translations`
--

DROP TABLE IF EXISTS `chatbot_node_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot_node_translations` (
  `node_id` int(11) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `message` text NOT NULL,
  PRIMARY KEY (`node_id`,`locale`),
  CONSTRAINT `chatbot_node_translations_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `chatbot_nodes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_node_translations`
--

LOCK TABLES `chatbot_node_translations` WRITE;
/*!40000 ALTER TABLE `chatbot_node_translations` DISABLE KEYS */;
INSERT INTO `chatbot_node_translations` VALUES (1,'ar','┘à╪▒╪¡╪¿╪º┘ï ╪¿┘â ┘ü┘è ┘à┘è┘â┘ê ╪│┘è╪¼ ≡ƒæï ┘â┘è┘ü ┘è┘à┘â┘å┘å╪º ┘à╪│╪º╪╣╪»╪¬┘â ╪º┘ä┘è┘ê┘à╪ƒ'),(1,'en','Hi there! ≡ƒæï Welcome to Mico Sage. How can we help you today?'),(2,'ar','┘å┘é╪»┘à ╪«╪»┘à╪º╪¬ ┘ç┘å╪»╪│╪⌐ ╪º┘ä┘ê┘è╪¿ ╪º┘ä┘à╪¬┘à┘è╪▓╪⌐╪î ┘ê╪¬╪╖╪¿┘è┘é╪º╪¬ ╪│╪╖╪¡ ╪º┘ä┘à┘â╪¬╪¿╪î ┘ê╪º┘ä╪¬╪│┘ê┘è┘é ╪º┘ä╪▒┘é┘à┘è. ┘ç┘ä ╪¬┘ê╪» ╪▒╪ñ┘è╪⌐ ╪ú╪╣┘à╪º┘ä┘å╪º ╪ú┘ê ╪¡╪¼╪▓ ╪º╪│╪¬╪┤╪º╪▒╪⌐╪ƒ'),(2,'en','We offer premium Web Engineering, Windows Desktop Applications, and Digital Marketing services. Would you like to view our portfolio or book a consultation?'),(3,'ar','┘ü╪▒┘è┘é ╪º┘ä╪»╪╣┘à ╪º┘ä┘ü┘å┘è ╪¼╪º┘ç╪▓ ┘ä┘à╪│╪º╪╣╪»╪¬┘â. ┘è┘à┘â┘å┘â ╪º┘ä╪º╪¬╪╡╪º┘ä ╪¿┘å╪º ┘à╪¿╪º╪┤╪▒╪⌐.'),(3,'en','Our support team is ready to assist you. You can give us a call directly.');
/*!40000 ALTER TABLE `chatbot_node_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_nodes`
--

DROP TABLE IF EXISTS `chatbot_nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot_nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `is_root` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_nodes`
--

LOCK TABLES `chatbot_nodes` WRITE;
/*!40000 ALTER TABLE `chatbot_nodes` DISABLE KEYS */;
INSERT INTO `chatbot_nodes` VALUES (1,'Welcome',1,'2026-02-23 17:47:10'),(2,'Services Info',0,'2026-02-23 17:47:10'),(3,'Support',0,'2026-02-23 17:47:10');
/*!40000 ALTER TABLE `chatbot_nodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_option_translations`
--

DROP TABLE IF EXISTS `chatbot_option_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot_option_translations` (
  `option_id` int(11) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `label` varchar(200) NOT NULL,
  PRIMARY KEY (`option_id`,`locale`),
  CONSTRAINT `chatbot_option_translations_ibfk_1` FOREIGN KEY (`option_id`) REFERENCES `chatbot_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_option_translations`
--

LOCK TABLES `chatbot_option_translations` WRITE;
/*!40000 ALTER TABLE `chatbot_option_translations` DISABLE KEYS */;
INSERT INTO `chatbot_option_translations` VALUES (1,'ar','╪«╪»┘à╪º╪¬┘å╪º'),(1,'en','Our Services'),(2,'ar','╪º┘ä╪»╪╣┘à ╪º┘ä┘ü┘å┘è'),(2,'en','Contact Support'),(3,'ar','╪▒╪ñ┘è╪⌐ ╪º┘ä╪ú╪╣┘à╪º┘ä'),(3,'en','View Portfolio'),(4,'ar','╪¡╪¼╪▓ ╪º╪│╪¬╪┤╪º╪▒╪⌐'),(4,'en','Book Consultation'),(5,'ar','╪º╪¬╪╡┘ä ╪¿┘å╪º ╪º┘ä╪ó┘å'),(5,'en','Call Us Now');
/*!40000 ALTER TABLE `chatbot_option_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_options`
--

DROP TABLE IF EXISTS `chatbot_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `next_node_id` int(11) DEFAULT NULL,
  `action_type` enum('goto_node','link','call') DEFAULT 'goto_node',
  `action_value` varchar(255) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  KEY `next_node_id` (`next_node_id`),
  CONSTRAINT `chatbot_options_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `chatbot_nodes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chatbot_options_ibfk_2` FOREIGN KEY (`next_node_id`) REFERENCES `chatbot_nodes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_options`
--

LOCK TABLES `chatbot_options` WRITE;
/*!40000 ALTER TABLE `chatbot_options` DISABLE KEYS */;
INSERT INTO `chatbot_options` VALUES (1,1,2,'goto_node','',1),(2,1,3,'goto_node','',2),(3,2,NULL,'link','/portfolio',1),(4,2,NULL,'link','/#booking',2),(5,3,NULL,'call','',1);
/*!40000 ALTER TABLE `chatbot_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chatbot_sessions`
--

DROP TABLE IF EXISTS `chatbot_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chatbot_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_uuid` varchar(100) NOT NULL,
  `user_ip` varchar(50) DEFAULT '',
  `user_agent` varchar(500) DEFAULT '',
  `status` enum('active','closed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_uuid` (`session_uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chatbot_sessions`
--

LOCK TABLES `chatbot_sessions` WRITE;
/*!40000 ALTER TABLE `chatbot_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `chatbot_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `logo_url` varchar(500) DEFAULT '',
  `website_url` varchar(500) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'TechCorp International','','',1,1,'2026-02-23 11:39:46'),(2,'Global Solutions Ltd','','',2,1,'2026-02-23 11:39:46'),(3,'Innovation Hub','','',3,1,'2026-02-23 11:39:46'),(4,'Digital Ventures','','',4,1,'2026-02-23 11:39:46'),(5,'SmartBiz Group','','',5,1,'2026-02-23 11:39:46'),(6,'TechCorp International','','',1,1,'2026-02-23 12:13:16'),(7,'Global Solutions Ltd','','',2,1,'2026-02-23 12:13:16'),(8,'Innovation Hub','','',3,1,'2026-02-23 12:13:16'),(9,'Digital Ventures','','',4,1,'2026-02-23 12:13:16'),(10,'SmartBiz Group','','',5,1,'2026-02-23 12:13:16');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('company','individual') DEFAULT 'company',
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `vat_number` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `poc_details` text DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'company','XCCSDCSD','','456547657','','','','','','','2026-02-23 20:04:46');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_key` varchar(100) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_section_locale` (`section_key`,`locale`)
) ENGINE=InnoDB AUTO_INCREMENT=393 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contents`
--

LOCK TABLES `contents` WRITE;
/*!40000 ALTER TABLE `contents` DISABLE KEYS */;
INSERT INTO `contents` VALUES (1,'hero_title','en','We Build The Future','2026-02-23 11:14:30'),(2,'hero_title','ar','┘å╪¡┘å ┘å╪¿┘å┘è ╪º┘ä┘à╪│╪¬┘é╪¿┘ä','2026-02-23 15:50:38'),(3,'hero_subtitle','en','Premium Web Engineering ┬╖ Windows Desktop Apps ┬╖ Digital Growth ┬╖ Creative Solutions','2026-02-23 15:50:38'),(4,'hero_subtitle','ar','┘ç┘å╪»╪│╪⌐ ┘ê┘è╪¿ ┘à╪¬┘à┘è╪▓╪⌐ ┬╖ ╪¬╪╖╪¿┘è┘é╪º╪¬ ╪│╪╖╪¡ ╪º┘ä┘à┘â╪¬╪¿ ┬╖ ╪º┘ä┘å┘à┘ê ╪º┘ä╪▒┘é┘à┘è ┬╖ ╪¡┘ä┘ê┘ä ╪Ñ╪¿╪»╪º╪╣┘è╪⌐','2026-02-23 15:50:38'),(5,'about_title','en','Why Choose Mico Sage?','2026-02-23 11:39:46'),(6,'about_title','ar','┘ä┘à╪º╪░╪º ╪¬╪«╪¬╪º╪▒ ┘à┘è┘â┘ê ╪│┘è╪¼╪ƒ','2026-02-23 15:50:38'),(7,'about_text','en','We are a forward-thinking tech agency that combines cutting-edge design with robust engineering. Our team delivers world-class digital products that propel your business into the future. From concept to launch, we handle every pixel and every line of code with precision.','2026-02-23 11:39:46'),(8,'about_text','ar','┘å╪¡┘å ┘ê┘â╪º┘ä╪⌐ ╪¬┘é┘å┘è╪⌐ ┘à╪¿╪¬┘â╪▒╪⌐ ╪¬╪¼┘à╪╣ ╪¿┘è┘å ╪º┘ä╪¬╪╡┘à┘è┘à ╪º┘ä┘à╪¬╪╖┘ê╪▒ ┘ê╪º┘ä┘ç┘å╪»╪│╪⌐ ╪º┘ä┘é┘ê┘è╪⌐. ┘è┘é╪»┘à ┘ü╪▒┘è┘é┘å╪º ┘à┘å╪¬╪¼╪º╪¬ ╪▒┘é┘à┘è╪⌐ ╪╣╪º┘ä┘à┘è╪⌐ ╪º┘ä┘à╪│╪¬┘ê┘ë ╪¬╪»┘ü╪╣ ╪ú╪╣┘à╪º┘ä┘â ┘å╪¡┘ê ╪º┘ä┘à╪│╪¬┘é╪¿┘ä. ┘à┘å ╪º┘ä┘ü┘â╪▒╪⌐ ╪Ñ┘ä┘ë ╪º┘ä╪Ñ╪╖┘ä╪º┘é╪î ┘å╪¬╪╣╪º┘à┘ä ┘à╪╣ ┘â┘ä ╪¿┘â╪│┘ä ┘ê┘â┘ä ╪│╪╖╪▒ ┘â┘ê╪» ╪¿╪»┘é╪⌐.','2026-02-23 15:50:38'),(9,'service_web_title','en','Web Engineering','2026-02-23 11:14:30'),(10,'service_web_title','ar','┘ç┘å╪»╪│╪⌐ ╪º┘ä┘ê┘è╪¿','2026-02-23 15:50:38'),(11,'service_web_desc','en','Full-stack web applications built with modern frameworks, responsive designs, and pixel-perfect interfaces that convert visitors into customers.','2026-02-23 11:14:30'),(12,'service_web_desc','ar','╪¬╪╖╪¿┘è┘é╪º╪¬ ┘ê┘è╪¿ ┘à╪¬┘â╪º┘à┘ä╪⌐ ┘à╪¿┘å┘è╪⌐ ╪¿╪ú╪¡╪»╪½ ╪º┘ä╪ú╪╖╪▒ ┘ê╪º┘ä╪¬╪╡╪º┘à┘è┘à ╪º┘ä┘à╪¬╪¼╪º┘ê╪¿╪⌐ ┘ê┘ê╪º╪¼┘ç╪º╪¬ ┘à╪½╪º┘ä┘è╪⌐ ╪¬╪¡┘ê┘ä ╪º┘ä╪▓┘ê╪º╪▒ ╪Ñ┘ä┘ë ╪╣┘à┘ä╪º╪í.','2026-02-23 15:50:38'),(13,'service_windows_title','en','Windows Desktop Apps','2026-02-23 11:14:30'),(14,'service_windows_title','ar','╪¬╪╖╪¿┘è┘é╪º╪¬ ╪│╪╖╪¡ ╪º┘ä┘à┘â╪¬╪¿','2026-02-23 15:50:38'),(15,'service_windows_desc','en','Native Windows applications with sleek UIs, powerful performance, and seamless integration with your business workflows.','2026-02-23 11:14:30'),(16,'service_windows_desc','ar','╪¬╪╖╪¿┘è┘é╪º╪¬ ┘ê┘è┘å╪»┘ê╪▓ ╪ú╪╡┘ä┘è╪⌐ ╪¿┘ê╪º╪¼┘ç╪º╪¬ ╪ú┘å┘è┘é╪⌐ ┘ê╪ú╪»╪º╪í ┘é┘ê┘è ┘ê╪¬┘â╪º┘à┘ä ╪│┘ä╪│ ┘à╪╣ ╪│┘è╪▒ ╪╣┘à┘ä ╪┤╪▒┘â╪¬┘â.','2026-02-23 15:50:38'),(17,'service_marketing_title','en','Digital Growth','2026-02-23 11:14:30'),(18,'service_marketing_title','ar','╪º┘ä┘å┘à┘ê ╪º┘ä╪▒┘é┘à┘è','2026-02-23 15:50:38'),(19,'service_marketing_desc','en','Data-driven digital marketing strategies including SEO, social media, PPC, and brand identity that amplify your online presence.','2026-02-23 11:14:30'),(20,'service_marketing_desc','ar','╪º╪│╪¬╪▒╪º╪¬┘è╪¼┘è╪º╪¬ ╪¬╪│┘ê┘è┘é ╪▒┘é┘à┘è ┘à╪¿┘å┘è╪⌐ ╪╣┘ä┘ë ╪º┘ä╪¿┘è╪º┘å╪º╪¬ ╪¬╪┤┘à┘ä ╪¬╪¡╪│┘è┘å ┘à╪¡╪▒┘â╪º╪¬ ╪º┘ä╪¿╪¡╪½ ┘ê╪º┘ä╪¬┘ê╪º╪╡┘ä ╪º┘ä╪º╪¼╪¬┘à╪º╪╣┘è ┘ê╪º┘ä╪Ñ╪╣┘ä╪º┘å╪º╪¬ ╪º┘ä┘à╪»┘ü┘ê╪╣╪⌐ ┘ê┘ç┘ê┘è╪⌐ ╪º┘ä╪╣┘ä╪º┘à╪⌐ ╪º┘ä╪¬╪¼╪º╪▒┘è╪⌐.','2026-02-23 15:50:38'),(21,'booking_title','en','Book a Consultation','2026-02-23 11:14:30'),(22,'booking_title','ar','╪º╪¡╪¼╪▓ ╪º╪│╪¬╪┤╪º╪▒╪⌐','2026-02-23 15:50:38'),(23,'booking_subtitle','en','Let us bring your vision to life. No login required.','2026-02-23 11:14:30'),(24,'booking_subtitle','ar','╪»╪╣┘å╪º ┘å╪¡┘ê┘ä ╪▒╪ñ┘è╪¬┘â ╪Ñ┘ä┘ë ┘ê╪º┘é╪╣. ┘ä╪º ╪¡╪º╪¼╪⌐ ┘ä╪¬╪│╪¼┘è┘ä ╪º┘ä╪»╪«┘ê┘ä.','2026-02-23 15:50:38'),(25,'footer_text','en','┬⌐ 2026 Mico Sage. All rights reserved.','2026-02-23 15:50:38'),(26,'footer_text','ar','┬⌐ 2026 ┘à┘è┘â┘ê ╪│┘è╪¼. ╪¼┘à┘è╪╣ ╪º┘ä╪¡┘é┘ê┘é ┘à╪¡┘ü┘ê╪╕╪⌐.','2026-02-23 15:50:38'),(27,'clients_title','en','Trusted By Industry Leaders','2026-02-23 11:39:46'),(28,'clients_title','ar','┘à┘ê╪½┘ê┘é ┘à┘å ┘é╪¿┘ä ┘é╪º╪»╪⌐ ╪º┘ä╪╡┘å╪º╪╣╪⌐','2026-02-23 15:50:38'),(29,'products_title','en','Ready-Made Solutions','2026-02-23 11:39:46'),(30,'products_title','ar','╪¡┘ä┘ê┘ä ╪¼╪º┘ç╪▓╪⌐','2026-02-23 15:50:38'),(31,'products_subtitle','en','Pre-built platforms customized to your brand. Launch faster, grow smarter.','2026-02-23 11:39:46'),(32,'products_subtitle','ar','┘à┘å╪╡╪º╪¬ ╪¼╪º┘ç╪▓╪⌐ ┘à╪«╪╡╪╡╪⌐ ┘ä╪╣┘ä╪º┘à╪¬┘â ╪º┘ä╪¬╪¼╪º╪▒┘è╪⌐. ╪º┘å╪╖┘ä┘é ╪ú╪│╪▒╪╣╪î ╪º┘å┘à┘Å ╪¿╪░┘â╪º╪í.','2026-02-23 15:50:38'),(33,'marketing_title','en','Digital Marketing That Delivers Results','2026-02-23 11:39:46'),(34,'marketing_title','ar','╪¬╪│┘ê┘è┘é ╪▒┘é┘à┘è ┘è╪¡┘é┘é ╪º┘ä┘å╪¬╪º╪ª╪¼','2026-02-23 15:50:38'),(35,'marketing_subtitle','en','We don\'t just market ΓÇö we engineer growth. Data-driven strategies that turn clicks into customers.','2026-02-23 15:50:38'),(36,'marketing_subtitle','ar','┘å╪¡┘å ┘ä╪º ┘å╪│┘ê┘é ┘ü┘é╪╖ ΓÇö ╪¿┘ä ┘å┘ç┘å╪»╪│ ╪º┘ä┘å┘à┘ê. ╪º╪│╪¬╪▒╪º╪¬┘è╪¼┘è╪º╪¬ ┘à╪¿┘å┘è╪⌐ ╪╣┘ä┘ë ╪º┘ä╪¿┘è╪º┘å╪º╪¬ ╪¬╪¡┘ê┘ä ╪º┘ä┘å┘é╪▒╪º╪¬ ╪Ñ┘ä┘ë ╪╣┘à┘ä╪º╪í.','2026-02-23 15:50:38'),(37,'marketing_seo_title','en','SEO Optimization','2026-02-23 11:39:46'),(38,'marketing_seo_title','ar','╪¬╪¡╪│┘è┘å ┘à╪¡╪▒┘â╪º╪¬ ╪º┘ä╪¿╪¡╪½','2026-02-23 15:50:38'),(39,'marketing_seo_desc','en','Dominate search rankings with white-hat SEO strategies, technical audits, and content optimization.','2026-02-23 11:39:46'),(40,'marketing_seo_desc','ar','╪¬╪╡╪»╪▒ ┘å╪¬╪º╪ª╪¼ ╪º┘ä╪¿╪¡╪½ ╪¿╪º╪│╪¬╪▒╪º╪¬┘è╪¼┘è╪º╪¬ SEO ╪ú╪«┘ä╪º┘é┘è╪⌐ ┘ê╪¬╪»┘é┘è┘é ╪¬┘é┘å┘è ┘ê╪¬╪¡╪│┘è┘å ╪º┘ä┘à╪¡╪¬┘ê┘ë.','2026-02-23 15:50:38'),(41,'marketing_social_title','en','Social Media Marketing','2026-02-23 11:39:46'),(42,'marketing_social_title','ar','╪º┘ä╪¬╪│┘ê┘è┘é ╪╣╪¿╪▒ ╪º┘ä╪¬┘ê╪º╪╡┘ä ╪º┘ä╪º╪¼╪¬┘à╪º╪╣┘è','2026-02-23 15:50:38'),(43,'marketing_social_desc','en','Build a loyal community with engaging content, strategic campaigns, and influencer partnerships.','2026-02-23 11:39:46'),(44,'marketing_social_desc','ar','╪º╪¿┘å┘É ┘à╪¼╪¬┘à╪╣╪º┘ï ┘à╪«┘ä╪╡╪º┘ï ╪¿┘à╪¡╪¬┘ê┘ë ╪¼╪░╪º╪¿ ┘ê╪¡┘à┘ä╪º╪¬ ╪º╪│╪¬╪▒╪º╪¬┘è╪¼┘è╪⌐ ┘ê╪┤╪▒╪º┘â╪º╪¬ ┘à╪╣ ╪º┘ä┘à╪ñ╪½╪▒┘è┘å.','2026-02-23 15:50:38'),(45,'marketing_ppc_title','en','PPC & Paid Ads','2026-02-23 11:39:46'),(46,'marketing_ppc_title','ar','╪º┘ä╪Ñ╪╣┘ä╪º┘å╪º╪¬ ╪º┘ä┘à╪»┘ü┘ê╪╣╪⌐','2026-02-23 15:50:38'),(47,'marketing_ppc_desc','en','Maximize ROI with precision-targeted Google Ads, Meta Ads, and programmatic advertising campaigns.','2026-02-23 11:39:46'),(48,'marketing_ppc_desc','ar','╪¡┘é┘é ╪ú┘é╪╡┘ë ╪╣╪º╪ª╪» ╪º╪│╪¬╪½┘à╪º╪▒ ╪¿╪Ñ╪╣┘ä╪º┘å╪º╪¬ ╪¼┘ê╪¼┘ä ┘ê┘à┘è╪¬╪º ╪º┘ä┘à╪│╪¬┘ç╪»┘ü╪⌐ ╪¿╪»┘é╪⌐ ┘ê╪º┘ä╪¡┘à┘ä╪º╪¬ ╪º┘ä╪Ñ╪╣┘ä╪º┘å┘è╪⌐ ╪º┘ä╪¿╪▒┘à╪¼┘è╪⌐.','2026-02-23 15:50:38'),(49,'marketing_brand_title','en','Brand Identity','2026-02-23 11:39:46'),(50,'marketing_brand_title','ar','┘ç┘ê┘è╪⌐ ╪º┘ä╪╣┘ä╪º┘à╪⌐ ╪º┘ä╪¬╪¼╪º╪▒┘è╪⌐','2026-02-23 15:50:38'),(51,'marketing_brand_desc','en','Craft a premium brand identity with logo design, brand guidelines, and visual storytelling that resonates.','2026-02-23 11:39:46'),(52,'marketing_brand_desc','ar','╪º╪╡┘å╪╣ ┘ç┘ê┘è╪⌐ ╪╣┘ä╪º┘à╪⌐ ╪¬╪¼╪º╪▒┘è╪⌐ ┘à╪¬┘à┘è╪▓╪⌐ ╪¿╪¬╪╡┘à┘è┘à ╪┤╪╣╪º╪▒ ┘ê╪Ñ╪▒╪┤╪º╪»╪º╪¬ ╪º┘ä╪╣┘ä╪º┘à╪⌐ ┘ê╪º┘ä╪│╪▒╪» ╪º┘ä╪¿╪╡╪▒┘è ╪º┘ä┘à╪ñ╪½╪▒.','2026-02-23 15:50:38'),(157,'portfolio_title','en','Our Portfolio','2026-02-23 15:50:38'),(158,'portfolio_title','ar','╪ú╪╣┘à╪º┘ä┘å╪º','2026-02-23 15:50:38'),(159,'portfolio_subtitle','en','Showcasing our finest work ΓÇö from concept to launch, every project tells a story of innovation.','2026-02-23 15:50:38'),(160,'portfolio_subtitle','ar','┘å╪╣╪▒╪╢ ╪ú┘ü╪╢┘ä ╪ú╪╣┘à╪º┘ä┘å╪º ΓÇö ┘à┘å ╪º┘ä┘ü┘â╪▒╪⌐ ╪Ñ┘ä┘ë ╪º┘ä╪Ñ╪╖┘ä╪º┘é╪î ┘â┘ä ┘à╪┤╪▒┘ê╪╣ ┘è╪▒┘ê┘è ┘é╪╡╪⌐ ╪º╪¿╪¬┘â╪º╪▒.','2026-02-23 15:50:38'),(385,'team_title','en','Meet Our Team','2026-02-23 18:13:56'),(386,'team_title','ar','┘ü╪▒┘è┘é ╪º┘ä╪╣┘à┘ä','2026-02-23 18:13:56'),(387,'team_subtitle','en','The brilliant minds behind our innovative solutions.','2026-02-23 18:13:56'),(388,'team_subtitle','ar','╪º┘ä╪╣┘é┘ê┘ä ╪º┘ä┘à╪¿╪¬┘â╪▒╪⌐ ┘ê╪▒╪º╪í ╪¡┘ä┘ê┘ä┘å╪º ╪º┘ä╪Ñ╪¿╪»╪º╪╣┘è╪⌐.','2026-02-23 18:13:56'),(389,'testimonials_title','en','Client Success Stories','2026-02-23 18:13:56'),(390,'testimonials_title','ar','┘é╪╡╪╡ ┘å╪¼╪º╪¡ ╪╣┘à┘ä╪º╪ª┘å╪º','2026-02-23 18:13:56'),(391,'testimonials_subtitle','en','Don\'t just take our word for it. See what our partners have to say.','2026-02-23 18:13:56'),(392,'testimonials_subtitle','ar','┘ä╪º ╪¬╪ú╪«╪░ ╪¿┘â┘ä╪º┘à┘å╪º ┘ü┘é╪╖. ╪┤╪º┘ç╪» ┘à╪º ┘è┘é┘ê┘ä┘ç ╪┤╪▒┘â╪º╪ñ┘å╪º ┘ê┘à╪▓┘ê╪»┘è┘å╪º.','2026-02-23 18:13:56');
/*!40000 ALTER TABLE `contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `service_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `qty` decimal(10,2) DEFAULT 1.00,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `vat_rate` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` VALUES (1,1,'Web Development','',1.00,0.00,0.00),(2,2,'Backend Save Test','Did unit_price save?',1.50,1000.55,5.00);
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('invoice','quote') DEFAULT 'invoice',
  `invoice_number` varchar(50) NOT NULL,
  `client_name` varchar(200) NOT NULL,
  `client_email` varchar(200) DEFAULT '',
  `client_phone` varchar(50) DEFAULT '',
  `client_address` varchar(500) DEFAULT '',
  `discount` decimal(10,2) DEFAULT 0.00,
  `vat_rate` decimal(5,2) DEFAULT 0.00,
  `status` enum('draft','sent','paid','cancelled') DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `contact_id` int(11) DEFAULT NULL,
  `invoice_currency` varchar(10) DEFAULT 'USD',
  `payment_terms` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,'invoice','<br /><b>Warning</b>:  Undefined variable $invNum ','Test Client','','','',0.00,0.00,'draft','','Payment is due within 15 days of issue.','2026-02-23 19:09:14','2026-02-23 19:09:14',NULL,'USD',NULL),(2,'invoice','TEST-1234','Server Test','','','',0.00,0.00,'draft','','','2026-02-23 19:14:06','2026-02-23 19:14:06',NULL,'USD',NULL);
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_project_translations`
--

DROP TABLE IF EXISTS `portfolio_project_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portfolio_project_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `client_name` varchar(200) DEFAULT '',
  `tags` varchar(500) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_project_locale` (`project_id`,`locale`),
  CONSTRAINT `portfolio_project_translations_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_project_translations`
--

LOCK TABLES `portfolio_project_translations` WRITE;
/*!40000 ALTER TABLE `portfolio_project_translations` DISABLE KEYS */;
INSERT INTO `portfolio_project_translations` VALUES (1,1,'en','Al-Riyada E-Commerce','A full-featured e-commerce platform with real-time inventory management, multi-currency checkout, AI-powered product recommendations, and a sleek responsive storefront that boosted conversions by 40%.','Al-Riyada Trading Co.','Laravel,Vue.js,Stripe,MySQL,Redis'),(2,1,'ar','┘à┘å╪╡╪⌐ ╪º┘ä╪▒┘è╪º╪»╪⌐ ┘ä┘ä╪¬╪¼╪º╪▒╪⌐ ╪º┘ä╪Ñ┘ä┘â╪¬╪▒┘ê┘å┘è╪⌐','┘à┘å╪╡╪⌐ ╪¬╪¼╪º╪▒╪⌐ ╪Ñ┘ä┘â╪¬╪▒┘ê┘å┘è╪⌐ ┘à╪¬┘â╪º┘à┘ä╪⌐ ┘à╪╣ ╪Ñ╪»╪º╪▒╪⌐ ┘à╪«╪▓┘ê┘å ┘ü┘ê╪▒┘è╪⌐ ┘ê╪»┘ü╪╣ ┘à╪¬╪╣╪»╪» ╪º┘ä╪╣┘à┘ä╪º╪¬ ┘ê╪¬┘ê╪╡┘è╪º╪¬ ┘à┘å╪¬╪¼╪º╪¬ ╪¿╪º┘ä╪░┘â╪º╪í ╪º┘ä╪º╪╡╪╖┘å╪º╪╣┘è ┘ê┘ê╪º╪¼┘ç╪⌐ ┘à╪¬╪¼╪º┘ê╪¿╪⌐ ╪ú┘å┘è┘é╪⌐ ╪▒┘ü╪╣╪¬ ╪º┘ä╪¬╪¡┘ê┘è┘ä╪º╪¬ ╪¿┘å╪│╪¿╪⌐ 40%.','╪┤╪▒┘â╪⌐ ╪º┘ä╪▒┘è╪º╪»╪⌐ ╪º┘ä╪¬╪¼╪º╪▒┘è╪⌐','Laravel,Vue.js,Stripe,MySQL,Redis'),(3,2,'en','Noir Perfumes','Luxury perfume brand website with immersive 3D product visualization, scent-matching quiz, subscription box builder, and an elegant dark-themed UI that mirrors the brand\'s premium identity.','Noir Fragrances LLC','Next.js,Three.js,Tailwind CSS,PostgreSQL'),(4,2,'ar','╪╣╪╖┘ê╪▒ ┘å┘ê╪º╪▒','┘à┘ê┘é╪╣ ╪╣┘ä╪º┘à╪⌐ ╪╣╪╖┘ê╪▒ ┘ü╪º╪«╪▒╪⌐ ┘à╪╣ ╪╣╪▒╪╢ ╪½┘ä╪º╪½┘è ╪º┘ä╪ú╪¿╪╣╪º╪» ┘ä┘ä┘à┘å╪¬╪¼╪º╪¬ ┘ê╪º╪«╪¬╪¿╪º╪▒ ┘à╪╖╪º╪¿┘é╪⌐ ╪º┘ä╪╣╪╖┘ê╪▒ ┘ê┘à┘å╪┤╪ª ╪╡┘å╪º╪»┘è┘é ╪º┘ä╪º╪┤╪¬╪▒╪º┘â ┘ê┘ê╪º╪¼┘ç╪⌐ ╪ú┘å┘è┘é╪⌐ ╪»╪º┘â┘å╪⌐ ╪¬╪╣┘â╪│ ┘ç┘ê┘è╪⌐ ╪º┘ä╪╣┘ä╪º┘à╪⌐ ╪º┘ä┘à╪¬┘à┘è╪▓╪⌐.','┘å┘ê╪º╪▒ ┘ä┘ä╪╣╪╖┘ê╪▒','Next.js,Three.js,Tailwind CSS,PostgreSQL'),(5,3,'en','Vogue Models Agency','Model management platform with digital portfolios, casting call boards, availability calendars, and a dynamic gallery showcasing talent with smooth animations and video integration.','Vogue Agency International','React,Node.js,MongoDB,AWS S3,FFmpeg'),(6,3,'ar','┘ê┘â╪º┘ä╪⌐ ┘ü┘ê╪║ ┘ä┘ä╪╣╪º╪▒╪╢╪º╪¬','┘à┘å╪╡╪⌐ ╪Ñ╪»╪º╪▒╪⌐ ╪╣╪º╪▒╪╢╪º╪¬ ┘à╪╣ ┘à┘ä┘ü╪º╪¬ ╪▒┘é┘à┘è╪⌐ ┘ê┘ä┘ê╪¡╪º╪¬ ╪º╪«╪¬┘è╪º╪▒ ┘à┘à╪½┘ä┘è┘å ┘ê╪¬┘é┘ê┘è┘à ╪º┘ä╪¬┘ê┘ü╪▒ ┘ê┘à╪╣╪▒╪╢ ╪»┘è┘å╪º┘à┘è┘â┘è ┘è╪╣╪▒╪╢ ╪º┘ä┘à┘ê╪º┘ç╪¿ ╪¿╪¡╪▒┘â╪º╪¬ ╪│┘ä╪│╪⌐ ┘ê╪¬┘â╪º┘à┘ä ╪º┘ä┘ü┘è╪»┘è┘ê.','┘ê┘â╪º┘ä╪⌐ ┘ü┘ê╪║ ╪º┘ä╪»┘ê┘ä┘è╪⌐','React,Node.js,MongoDB,AWS S3,FFmpeg'),(7,4,'en','FleetTrack Pro','Enterprise Windows desktop application for real-time fleet tracking with GPS integration, driver behavior analytics, maintenance scheduling, and comprehensive reporting dashboards.','Gulf Logistics Group','C#,WPF,.NET 8,SQLite,Google Maps API'),(8,4,'ar','┘ü┘ä┘è╪¬ ╪¬╪▒╪º┘â ╪¿╪▒┘ê','╪¬╪╖╪¿┘è┘é ╪│╪╖╪¡ ┘à┘â╪¬╪¿ ┘ê┘è┘å╪»┘ê╪▓ ┘ä╪¬╪¬╪¿╪╣ ╪º┘ä╪ú╪│╪º╪╖┘è┘ä ┘ü┘è ╪º┘ä┘ê┘é╪¬ ╪º┘ä┘ü╪╣┘ä┘è ┘à╪╣ ╪¬┘â╪º┘à┘ä GPS ┘ê╪¬╪¡┘ä┘è┘ä╪º╪¬ ╪│┘ä┘ê┘â ╪º┘ä╪│╪º╪ª┘é┘è┘å ┘ê╪¼╪»┘ê┘ä╪⌐ ╪º┘ä╪╡┘è╪º┘å╪⌐ ┘ê┘ä┘ê╪¡╪º╪¬ ╪¬┘é╪º╪▒┘è╪▒ ╪┤╪º┘à┘ä╪⌐.','┘à╪¼┘à┘ê╪╣╪⌐ ╪º┘ä╪«┘ä┘è╪¼ ╪º┘ä┘ä┘ê╪¼╪│╪¬┘è╪⌐','C#,WPF,.NET 8,SQLite,Google Maps API'),(9,5,'en','Al-Maskan Hotels','Modern hotel booking platform with dynamic room pricing, interactive floor plans, virtual tours, guest portal with loyalty points, and integration with major OTA channels.','Al-Maskan Hospitality','PHP,Alpine.js,MySQL,Stripe,Mapbox'),(10,5,'ar','┘ü┘å╪º╪»┘é ╪º┘ä┘à╪│┘â┘å','┘à┘å╪╡╪⌐ ╪¡╪¼╪▓ ┘ü┘å╪º╪»┘é ╪¡╪»┘è╪½╪⌐ ┘à╪╣ ╪¬╪│╪╣┘è╪▒ ╪»┘è┘å╪º┘à┘è┘â┘è ┘ä┘ä╪║╪▒┘ü ┘ê┘à╪«╪╖╪╖╪º╪¬ ╪╖┘ê╪º╪¿┘é ╪¬┘ü╪º╪╣┘ä┘è╪⌐ ┘ê╪¼┘ê┘ä╪º╪¬ ╪º┘ü╪¬╪▒╪º╪╢┘è╪⌐ ┘ê╪¿┘ê╪º╪¿╪⌐ ╪╢┘è┘ê┘ü ┘à╪╣ ┘å┘é╪º╪╖ ┘ê┘ä╪º╪í ┘ê╪¬┘â╪º┘à┘ä ┘à╪╣ ┘é┘å┘ê╪º╪¬ ╪º┘ä╪¡╪¼╪▓ ╪º┘ä┘â╪¿╪▒┘ë.','╪╢┘è╪º┘ü╪⌐ ╪º┘ä┘à╪│┘â┘å','PHP,Alpine.js,MySQL,Stripe,Mapbox'),(11,6,'en','Digital Bloom Campaign','Comprehensive digital marketing campaign that tripled social media engagement, achieved #1 Google rankings for 15 target keywords, and generated 200% ROI through strategic PPC and content marketing.','Bloom Beauty','Google Ads,Meta Ads,SEO,Analytics,Figma'),(12,6,'ar','╪¡┘à┘ä╪⌐ ╪»┘è╪¼┘è╪¬╪º┘ä ╪¿┘ä┘ê┘à','╪¡┘à┘ä╪⌐ ╪¬╪│┘ê┘è┘é ╪▒┘é┘à┘è ╪┤╪º┘à┘ä╪⌐ ╪╢╪º╪╣┘ü╪¬ ╪º┘ä╪¬┘ü╪º╪╣┘ä ╪╣┘ä┘ë ┘ê╪│╪º╪ª┘ä ╪º┘ä╪¬┘ê╪º╪╡┘ä ╪½┘ä╪º╪½ ┘à╪▒╪º╪¬ ┘ê╪¡┘é┘é╪¬ ╪º┘ä┘à╪▒┘â╪▓ ╪º┘ä╪ú┘ê┘ä ┘ü┘è ╪¼┘ê╪¼┘ä ┘ä┘Ç15 ┘â┘ä┘à╪⌐ ┘à┘ü╪¬╪º╪¡┘è╪⌐ ┘à╪│╪¬┘ç╪»┘ü╪⌐ ┘ê╪¡┘é┘é╪¬ ╪╣╪º╪ª╪» ╪º╪│╪¬╪½┘à╪º╪▒ 200% ┘à┘å ╪«┘ä╪º┘ä ╪º┘ä╪Ñ╪╣┘ä╪º┘å╪º╪¬ ╪º┘ä┘à╪»┘ü┘ê╪╣╪⌐ ┘ê╪¬╪│┘ê┘è┘é ╪º┘ä┘à╪¡╪¬┘ê┘ë.','╪¿┘ä┘ê┘à ╪¿┘è┘ê╪¬┘è','Google Ads,Meta Ads,SEO,Analytics,Figma');
/*!40000 ALTER TABLE `portfolio_project_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portfolio_projects`
--

DROP TABLE IF EXISTS `portfolio_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portfolio_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(200) NOT NULL,
  `image_url` varchar(500) DEFAULT '',
  `demo_url` varchar(500) DEFAULT '',
  `category` enum('website','app','branding','marketing') DEFAULT 'website',
  `color` varchar(50) DEFAULT 'cobalt',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portfolio_projects`
--

LOCK TABLES `portfolio_projects` WRITE;
/*!40000 ALTER TABLE `portfolio_projects` DISABLE KEYS */;
INSERT INTO `portfolio_projects` VALUES (1,'al-riyada-ecommerce','','','website','cobalt',1,1,1,'2026-02-23 15:50:38'),(2,'noir-perfumes','','','website','violet',2,1,0,'2026-02-23 15:50:38'),(3,'vogue-models-agency','','','website','pink',3,1,0,'2026-02-23 15:50:38'),(4,'fleettrack-pro','','','app','emerald',4,1,0,'2026-02-23 15:50:38'),(5,'al-maskan-hotels','','','website','cyan',5,1,0,'2026-02-23 15:50:38'),(6,'digital-bloom-campaign','','','marketing','orange',6,1,0,'2026-02-23 15:50:38');
/*!40000 ALTER TABLE `portfolio_projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_translations`
--

DROP TABLE IF EXISTS `product_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_locale` (`product_id`,`locale`),
  CONSTRAINT `product_translations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_translations`
--

LOCK TABLES `product_translations` WRITE;
/*!40000 ALTER TABLE `product_translations` DISABLE KEYS */;
INSERT INTO `product_translations` VALUES (1,1,'en','Car Rental Platform','Complete car rental management system with booking engine, fleet management, and payment integration.'),(2,1,'ar','┘à┘å╪╡╪⌐ ╪¬╪ú╪¼┘è╪▒ ╪º┘ä╪│┘è╪º╪▒╪º╪¬','┘å╪╕╪º┘à ┘à╪¬┘â╪º┘à┘ä ┘ä╪Ñ╪»╪º╪▒╪⌐ ╪¬╪ú╪¼┘è╪▒ ╪º┘ä╪│┘è╪º╪▒╪º╪¬ ┘à╪╣ ┘à╪¡╪▒┘â ╪¡╪¼┘ê╪▓╪º╪¬ ┘ê╪Ñ╪»╪º╪▒╪⌐ ╪ú╪│╪╖┘ê┘ä ┘ê╪¬┘â╪º┘à┘ä ┘à╪»┘ü┘ê╪╣╪º╪¬.'),(3,2,'en','E-Commerce Store','Feature-rich online store with product management, shopping cart, payment gateways, and analytics dashboard.'),(4,2,'ar','┘à╪¬╪¼╪▒ ╪Ñ┘ä┘â╪¬╪▒┘ê┘å┘è','┘à╪¬╪¼╪▒ ╪Ñ┘ä┘â╪¬╪▒┘ê┘å┘è ╪║┘å┘è ╪¿╪º┘ä┘à┘è╪▓╪º╪¬ ┘à╪╣ ╪Ñ╪»╪º╪▒╪⌐ ╪º┘ä┘à┘å╪¬╪¼╪º╪¬ ┘ê╪╣╪▒╪¿╪⌐ ╪º┘ä╪¬╪│┘ê┘é ┘ê╪¿┘ê╪º╪¿╪º╪¬ ╪º┘ä╪»┘ü╪╣ ┘ê┘ä┘ê╪¡╪⌐ ╪º┘ä╪¬╪¡┘ä┘è┘ä╪º╪¬.'),(5,3,'en','Hotel Booking System','Modern hotel reservation platform with room management, dynamic pricing, and guest portal.'),(6,3,'ar','┘å╪╕╪º┘à ╪¡╪¼╪▓ ┘ü┘å╪º╪»┘é','┘à┘å╪╡╪⌐ ╪¡╪¼╪▓ ┘ü┘å╪º╪»┘é ╪¡╪»┘è╪½╪⌐ ┘à╪╣ ╪Ñ╪»╪º╪▒╪⌐ ╪º┘ä╪║╪▒┘ü ┘ê╪º┘ä╪¬╪│╪╣┘è╪▒ ╪º┘ä╪»┘è┘å╪º┘à┘è┘â┘è ┘ê╪¿┘ê╪º╪¿╪⌐ ╪º┘ä╪╢┘è┘ê┘ü.'),(7,4,'en','Billing & Invoice App','Streamlined billing application with invoice generation, recurring payments, and financial reporting.'),(8,4,'ar','╪¬╪╖╪¿┘è┘é ╪º┘ä┘ü┘ê╪º╪¬┘è╪▒','╪¬╪╖╪¿┘è┘é ┘ü┘ê╪¬╪▒╪⌐ ┘à╪¬╪╖┘ê╪▒ ┘à╪╣ ╪Ñ╪╡╪»╪º╪▒ ╪º┘ä┘ü┘ê╪º╪¬┘è╪▒ ┘ê╪º┘ä┘à╪»┘ü┘ê╪╣╪º╪¬ ╪º┘ä┘à╪¬┘â╪▒╪▒╪⌐ ┘ê╪º┘ä╪¬┘é╪º╪▒┘è╪▒ ╪º┘ä┘à╪º┘ä┘è╪⌐.'),(9,5,'en','CRM System','Customer relationship management with lead tracking, pipeline management, and automated workflows.'),(10,5,'ar','┘å╪╕╪º┘à ╪Ñ╪»╪º╪▒╪⌐ ╪º┘ä╪╣┘à┘ä╪º╪í','╪Ñ╪»╪º╪▒╪⌐ ╪╣┘ä╪º┘é╪º╪¬ ╪º┘ä╪╣┘à┘ä╪º╪í ┘à╪╣ ╪¬╪¬╪¿╪╣ ╪º┘ä╪╣┘à┘ä╪º╪í ╪º┘ä┘à╪¡╪¬┘à┘ä┘è┘å ┘ê╪Ñ╪»╪º╪▒╪⌐ ╪º┘ä┘à╪¿┘è╪╣╪º╪¬ ┘ê╪│┘è╪▒ ╪º┘ä╪╣┘à┘ä ╪º┘ä╪ó┘ä┘è.'),(11,6,'en','Website Maintenance','Ongoing website maintenance, security updates, performance optimization, and content management support.'),(12,6,'ar','╪╡┘è╪º┘å╪⌐ ╪º┘ä┘à┘ê╪º┘é╪╣','╪╡┘è╪º┘å╪⌐ ┘à╪│╪¬┘à╪▒╪⌐ ┘ä┘ä┘à┘ê╪º┘é╪╣ ┘ê╪¬╪¡╪»┘è╪½╪º╪¬ ╪º┘ä╪ú┘à╪º┘å ┘ê╪¬╪¡╪│┘è┘å ╪º┘ä╪ú╪»╪º╪í ┘ê╪»╪╣┘à ╪Ñ╪»╪º╪▒╪⌐ ╪º┘ä┘à╪¡╪¬┘ê┘ë.');
/*!40000 ALTER TABLE `product_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) DEFAULT 'globe',
  `category` enum('website','app','maintenance') DEFAULT 'website',
  `color` varchar(50) DEFAULT 'cobalt',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'car','website','cobalt',1,1,'2026-02-23 11:39:46'),(2,'cart','website','violet',2,1,'2026-02-23 11:39:46'),(3,'hotel','website','cyan',3,1,'2026-02-23 11:39:46'),(4,'billing','app','emerald',4,1,'2026-02-23 11:39:46'),(5,'crm','app','pink',5,1,'2026-02-23 11:39:46'),(6,'wrench','maintenance','orange',6,1,'2026-02-23 11:39:46'),(7,'car','website','cobalt',1,1,'2026-02-23 12:13:16'),(8,'cart','website','violet',2,1,'2026-02-23 12:13:16'),(9,'hotel','website','cyan',3,1,'2026-02-23 12:13:16'),(10,'billing','app','emerald',4,1,'2026-02-23 12:13:16'),(11,'crm','app','pink',5,1,'2026-02-23 12:13:16'),(12,'wrench','maintenance','orange',6,1,'2026-02-23 12:13:16');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_meta`
--

DROP TABLE IF EXISTS `seo_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seo_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(50) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `title` varchar(255) DEFAULT '',
  `description` text DEFAULT NULL,
  `keywords` varchar(500) DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_page_locale` (`page`,`locale`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_meta`
--

LOCK TABLES `seo_meta` WRITE;
/*!40000 ALTER TABLE `seo_meta` DISABLE KEYS */;
INSERT INTO `seo_meta` VALUES (1,'home','en','Mico Sage | Web Development, Windows Apps & Digital Marketing','Premium tech agency specializing in web engineering, Windows desktop applications, and digital marketing solutions.','web development, windows apps, digital marketing, tech agency','2026-02-23 11:39:46'),(2,'home','ar','┘à┘è┘â┘ê ╪│┘è╪¼ | ╪¬╪╖┘ê┘è╪▒ ╪º┘ä┘à┘ê╪º┘é╪╣ ┘ê╪¬╪╖╪¿┘è┘é╪º╪¬ ┘ê┘è┘å╪»┘ê╪▓ ┘ê╪º┘ä╪¬╪│┘ê┘è┘é ╪º┘ä╪▒┘é┘à┘è','┘ê┘â╪º┘ä╪⌐ ╪¬┘é┘å┘è╪⌐ ┘à╪¬┘à┘è╪▓╪⌐ ┘à╪¬╪«╪╡╪╡╪⌐ ┘ü┘è ┘ç┘å╪»╪│╪⌐ ╪º┘ä┘ê┘è╪¿ ┘ê╪¬╪╖╪¿┘è┘é╪º╪¬ ╪│╪╖╪¡ ╪º┘ä┘à┘â╪¬╪¿ ┘ä┘å╪╕╪º┘à ┘ê┘è┘å╪»┘ê╪▓ ┘ê╪¡┘ä┘ê┘ä ╪º┘ä╪¬╪│┘ê┘è┘é ╪º┘ä╪▒┘é┘à┘è.','╪¬╪╖┘ê┘è╪▒ ┘à┘ê╪º┘é╪╣, ╪¬╪╖╪¿┘è┘é╪º╪¬ ┘ê┘è┘å╪»┘ê╪▓, ╪¬╪│┘ê┘è┘é ╪▒┘é┘à┘è, ┘ê┘â╪º┘ä╪⌐ ╪¬┘é┘å┘è╪⌐','2026-02-23 15:50:38'),(7,'portfolio','en','Our Portfolio | Mico Sage ΓÇö Projects & Case Studies','Explore our portfolio of web applications, desktop software, and digital marketing campaigns. See how Mico Sage delivers premium digital solutions.','portfolio, case studies, web projects, app development, digital marketing','2026-02-23 15:50:38'),(8,'portfolio','ar','╪ú╪╣┘à╪º┘ä┘å╪º | ┘à┘è┘â┘ê ╪│┘è╪¼ ΓÇö ┘à╪┤╪º╪▒┘è╪╣┘å╪º ┘ê╪»╪▒╪º╪│╪º╪¬ ╪º┘ä╪¡╪º┘ä╪⌐','╪º╪│╪¬┘â╪┤┘ü ╪ú╪╣┘à╪º┘ä┘å╪º ┘à┘å ╪¬╪╖╪¿┘è┘é╪º╪¬ ╪º┘ä┘ê┘è╪¿ ┘ê╪¿╪▒╪º┘à╪¼ ╪│╪╖╪¡ ╪º┘ä┘à┘â╪¬╪¿ ┘ê╪¡┘à┘ä╪º╪¬ ╪º┘ä╪¬╪│┘ê┘è┘é ╪º┘ä╪▒┘é┘à┘è. ╪┤╪º┘ç╪» ┘â┘è┘ü ╪¬┘é╪»┘à ┘à┘è┘â┘ê ╪│┘è╪¼ ╪¡┘ä┘ê┘ä ╪▒┘é┘à┘è╪⌐ ┘à╪¬┘à┘è╪▓╪⌐.','╪ú╪╣┘à╪º┘ä, ╪»╪▒╪º╪│╪º╪¬ ╪¡╪º┘ä╪⌐, ┘à╪┤╪º╪▒┘è╪╣ ┘ê┘è╪¿, ╪¬╪╖┘ê┘è╪▒ ╪¬╪╖╪¿┘è┘é╪º╪¬, ╪¬╪│┘ê┘è┘é ╪▒┘é┘à┘è','2026-02-23 15:50:38');
/*!40000 ALTER TABLE `seo_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_translations`
--

DROP TABLE IF EXISTS `service_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_service_locale` (`service_id`,`locale`),
  CONSTRAINT `service_translations_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_translations`
--

LOCK TABLES `service_translations` WRITE;
/*!40000 ALTER TABLE `service_translations` DISABLE KEYS */;
INSERT INTO `service_translations` VALUES (1,1,'en','Web Engineering','Full-stack web applications built with modern frameworks, responsive designs, and pixel-perfect interfaces that convert visitors into customers.'),(2,1,'ar','┘ç┘å╪»╪│╪⌐ ╪º┘ä┘ê┘è╪¿','╪¬╪╖╪¿┘è┘é╪º╪¬ ┘ê┘è╪¿ ┘à╪¬┘â╪º┘à┘ä╪⌐ ┘à╪¿┘å┘è╪⌐ ╪¿╪ú╪¡╪»╪½ ╪º┘ä╪ú╪╖╪▒ ┘ê╪º┘ä╪¬╪╡╪º┘à┘è┘à ╪º┘ä┘à╪¬╪¼╪º┘ê╪¿╪⌐ ┘ê┘ê╪º╪¼┘ç╪º╪¬ ┘à╪½╪º┘ä┘è╪⌐ ╪¬╪¡┘ê┘ä ╪º┘ä╪▓┘ê╪º╪▒ ╪Ñ┘ä┘ë ╪╣┘à┘ä╪º╪í.'),(3,2,'en','Windows Desktop Apps','Native Windows applications with sleek UIs, powerful performance, and seamless integration with your business workflows.'),(4,2,'ar','╪¬╪╖╪¿┘è┘é╪º╪¬ ╪│╪╖╪¡ ╪º┘ä┘à┘â╪¬╪¿','╪¬╪╖╪¿┘è┘é╪º╪¬ ┘ê┘è┘å╪»┘ê╪▓ ╪ú╪╡┘ä┘è╪⌐ ╪¿┘ê╪º╪¼┘ç╪º╪¬ ╪ú┘å┘è┘é╪⌐ ┘ê╪ú╪»╪º╪í ┘é┘ê┘è ┘ê╪¬┘â╪º┘à┘ä ╪│┘ä╪│ ┘à╪╣ ╪│┘è╪▒ ╪╣┘à┘ä ╪┤╪▒┘â╪¬┘â.'),(5,3,'en','Digital Growth','Data-driven digital marketing strategies including SEO, social media, PPC, and brand identity that amplify your online presence.'),(6,3,'ar','╪º┘ä┘å┘à┘ê ╪º┘ä╪▒┘é┘à┘è','╪º╪│╪¬╪▒╪º╪¬┘è╪¼┘è╪º╪¬ ╪¬╪│┘ê┘è┘é ╪▒┘é┘à┘è ┘à╪¿┘å┘è╪⌐ ╪╣┘ä┘ë ╪º┘ä╪¿┘è╪º┘å╪º╪¬ ╪¬╪┤┘à┘ä ╪¬╪¡╪│┘è┘å ┘à╪¡╪▒┘â╪º╪¬ ╪º┘ä╪¿╪¡╪½ ┘ê╪º┘ä╪¬┘ê╪º╪╡┘ä ╪º┘ä╪º╪¼╪¬┘à╪º╪╣┘è ┘ê╪º┘ä╪Ñ╪╣┘ä╪º┘å╪º╪¬ ╪º┘ä┘à╪»┘ü┘ê╪╣╪⌐ ┘ê┘ç┘ê┘è╪⌐ ╪º┘ä╪╣┘ä╪º┘à╪⌐ ╪º┘ä╪¬╪¼╪º╪▒┘è╪⌐.');
/*!40000 ALTER TABLE `service_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` varchar(50) DEFAULT 'code',
  `color` varchar(50) DEFAULT 'cobalt',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'code','cobalt',1,1,'2026-02-23 11:39:46'),(2,'monitor','violet',2,1,'2026-02-23 11:39:46'),(3,'chart','emerald',3,1,'2026-02-23 11:39:46'),(4,'code','cobalt',1,1,'2026-02-23 12:13:16'),(5,'monitor','violet',2,1,'2026-02-23 12:13:16'),(6,'chart','emerald',3,1,'2026-02-23 12:13:16');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` varchar(20) DEFAULT 'text',
  `setting_group` varchar(50) DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES ('contact_email','hello@micosage.com','text','contact','2026-02-23 17:26:29'),('contact_phone','+971 50 123 4567','text','contact','2026-02-23 17:26:29'),('primary_color','#3b82f6','color','branding','2026-02-23 11:39:46'),('secondary_color','#8b5cf6','color','branding','2026-02-23 11:39:46'),('show_clients_section','1','boolean','sections','2026-02-23 11:39:46'),('show_marketing_section','1','boolean','sections','2026-02-23 11:39:46'),('show_products_section','1','boolean','sections','2026-02-23 11:39:46'),('show_stats_section','1','boolean','sections','2026-02-23 11:39:46'),('show_team','1','boolean','general','2026-02-23 18:13:56'),('show_testimonials','1','boolean','general','2026-02-23 18:13:56'),('site_name','Mico Sage','text','branding','2026-02-23 11:39:46'),('site_tagline_ar','┘å╪¬╪¡╪»┘ë ╪º┘ä╪¡╪»┘ê╪» ╪º┘ä╪▒┘é┘à┘è╪⌐','text','branding','2026-02-23 11:39:46'),('site_tagline_en','Defying Digital Limits','text','branding','2026-02-23 11:39:46'),('stat_clients_label_ar','╪╣┘à┘è┘ä ╪│╪╣┘è╪»','text','stats','2026-02-23 11:39:46'),('stat_clients_label_en','Happy Clients','text','stats','2026-02-23 11:39:46'),('stat_clients_num','50+','text','stats','2026-02-23 11:39:46'),('stat_projects_label_ar','┘à╪┤╪▒┘ê╪╣ ┘à┘å╪¼╪▓','text','stats','2026-02-23 11:39:46'),('stat_projects_label_en','Projects Delivered','text','stats','2026-02-23 11:39:46'),('stat_projects_num','150+','text','stats','2026-02-23 11:39:46'),('stat_years_label_ar','╪│┘å┘ê╪º╪¬ ╪«╪¿╪▒╪⌐','text','stats','2026-02-23 11:39:46'),('stat_years_label_en','Years Experience','text','stats','2026-02-23 11:39:46'),('stat_years_num','8+','text','stats','2026-02-23 11:39:46'),('visit_count','22','text','general','2026-02-23 20:55:14');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_member_translations`
--

DROP TABLE IF EXISTS `team_member_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_member_translations` (
  `member_id` int(11) NOT NULL,
  `locale` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  PRIMARY KEY (`member_id`,`locale`),
  CONSTRAINT `team_member_translations_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `team_members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_member_translations`
--

LOCK TABLES `team_member_translations` WRITE;
/*!40000 ALTER TABLE `team_member_translations` DISABLE KEYS */;
INSERT INTO `team_member_translations` VALUES (1,'ar','╪│╪º╪▒╪⌐ ╪¼┘å┘â┘è┘å╪▓','╪º┘ä╪▒╪ª┘è╪│╪⌐ ╪º┘ä╪¬┘å┘ü┘è╪░┘è╪⌐ ┘ê╪º┘ä┘à╪ñ╪│╪│╪⌐','┘é╪º╪ª╪»╪⌐ ╪░╪º╪¬ ╪▒╪ñ┘è╪⌐ ┘à╪│╪¬┘é╪¿┘ä┘è╪⌐ ╪¿╪«╪¿╪▒╪⌐ ╪¬╪¬╪¼╪º┘ê╪▓ 15 ╪╣╪º┘à╪º┘ï ┘ü┘è ╪º┘ä╪¬╪¡┘ê┘ä ╪º┘ä╪▒┘é┘à┘è ┘ê┘ç┘å╪»╪│╪⌐ ╪º┘ä┘à╪┤╪º╪▒┘è╪╣ ╪º┘ä╪Ñ╪│╪¬╪▒╪º╪¬┘è╪¼┘è╪⌐.'),(1,'en','Zoi','CEO & Founder','Visionary leader with 15+ years of experience in digital transformation and enterprise architecture.'),(2,'ar','╪ú╪¡┘à╪» ╪º┘ä┘ü┘ç╪»','╪º┘ä╪▒╪ª┘è╪│ ╪º┘ä╪¬┘å┘ü┘è╪░┘è ┘ä┘ä╪¬┘â┘å┘ê┘ä┘ê╪¼┘è╪º','╪«╪¿┘è╪▒ ┘ü┘è ╪º┘ä╪¡┘ä┘ê┘ä ╪º┘ä╪│╪¡╪º╪¿┘è╪⌐ ╪º┘ä┘é╪º╪¿┘ä╪⌐ ┘ä┘ä╪¬╪╖┘ê┘è╪▒╪î ┘ê╪»┘à╪¼ ╪º┘ä╪░┘â╪º╪í ╪º┘ä╪º╪╡╪╖┘å╪º╪╣┘è╪î ┘ê┘é┘è╪º╪»╪⌐ ┘ü╪▒┘é ┘ç┘å╪»╪│┘è╪⌐ ╪╣╪º┘ä┘è╪⌐ ╪º┘ä╪ú╪»╪º╪í.'),(2,'en','Sapna Majeed','Chief Technology Officer','Expert in scalable cloud solutions, AI integration, and leading high-performance engineering teams.'),(3,'ar','╪Ñ┘è┘ä┘è┘å╪º ╪▒┘ê╪»╪▒┘è╪║┘è╪▓','╪º┘ä┘à╪»┘è╪▒╪⌐ ╪º┘ä╪Ñ╪¿╪»╪º╪╣┘è╪⌐','┘à╪╡┘à┘à╪⌐ ╪¡╪º╪ª╪▓╪⌐ ╪╣┘ä┘ë ╪¼┘ê╪º╪ª╪▓ ╪┤╪║┘ê┘ü╪⌐ ╪¿╪¿┘å╪º╪í ╪¬╪¼╪º╪▒╪¿ ┘à╪│╪¬╪«╪»┘à ╪¿╪»┘è┘ç┘è╪⌐╪î ┘ê╪¼┘à┘è┘ä╪⌐╪î ┘ê┘à╪¬╪º╪¡╪⌐ ┘ä┘ä╪¼┘à┘è╪╣.'),(3,'en','Elena Rodriguez','Creative Director','Award-winning designer passionate about building intuitive, beautiful, and accessible user experiences.');
/*!40000 ALTER TABLE `team_member_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `team_members`
--

DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_url` varchar(255) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `team_members`
--

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
INSERT INTO `team_members` VALUES (1,'',1,1,'2026-02-23 18:13:56','2026-02-23 18:13:56'),(2,'',2,1,'2026-02-23 18:13:56','2026-02-23 18:13:56'),(3,'',3,1,'2026-02-23 18:13:56','2026-02-23 18:13:56');
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonial_translations`
--

DROP TABLE IF EXISTS `testimonial_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonial_translations` (
  `testimonial_id` int(11) NOT NULL,
  `locale` varchar(10) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_company` varchar(255) DEFAULT '',
  `content` text NOT NULL,
  PRIMARY KEY (`testimonial_id`,`locale`),
  CONSTRAINT `testimonial_translations_ibfk_1` FOREIGN KEY (`testimonial_id`) REFERENCES `testimonials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonial_translations`
--

LOCK TABLES `testimonial_translations` WRITE;
/*!40000 ALTER TABLE `testimonial_translations` DISABLE KEYS */;
INSERT INTO `testimonial_translations` VALUES (1,'ar','┘à╪º┘è┘â┘ä ╪¬╪┤╪º┘å╪║','╪¬┘è┘â ┘â┘ê╪▒╪¿ ╪º┘ä╪»┘ê┘ä┘è╪⌐','┘é╪º┘à╪¬ ┘à┘è┘â┘ê ╪│┘è╪¼ ╪¿╪¬╪¼╪»┘è╪» ╪ú┘å╪╕┘à╪¬┘å╪º ╪º┘ä┘é╪»┘è┘à╪⌐ ╪¿╪º┘ä┘â╪º┘à┘ä. ┘ü╪▒┘è┘é ┘ç┘å╪»╪│╪⌐ ╪º┘ä┘ê┘è╪¿ ┘ä╪»┘è┘ç┘à ┘à┘å ╪º┘ä╪╖╪▒╪º╪▓ ╪º┘ä╪ú┘ê┘ä╪î ┘ê┘é╪»┘à┘ê╪º ┘à┘å╪¬╪¼╪º┘ï ┘ü╪º┘é ┘â┘ä ╪¬┘ê┘é╪╣╪º╪¬┘å╪º ┘à┘å ┘å╪º╪¡┘è╪⌐ ╪º┘ä╪ú╪»╪º╪í.'),(1,'en','Michael Chang','TechCorp International','Mico Sage entirely revamped our legacy systems. Their Web Engineering team is top-tier, delivering a product that exceeded all our performance expectations.'),(2,'ar','┘ü╪º╪╖┘à╪⌐ ╪º┘ä╪│┘è╪»','╪¿┘ä┘ê┘à ╪¿┘è┘ê╪¬┘è','╪¡┘à┘ä╪⌐ ╪º┘ä╪¬╪│┘ê┘è┘é ╪º┘ä╪▒┘é┘à┘è ╪º┘ä╪¬┘è ╪╡┘à┘à┘ê┘ç╪º ┘ä┘å╪º ╪╢╪º╪╣┘ü╪¬ ┘à╪¿┘è╪╣╪º╪¬┘å╪º ╪╣╪¿╪▒ ╪º┘ä╪Ñ┘å╪¬╪▒┘å╪¬ ╪½┘ä╪º╪½ ┘à╪▒╪º╪¬ ┘ü┘è ╪½┘ä╪º╪½╪⌐ ╪ú╪┤┘ç╪▒ ┘ü┘é╪╖. ╪¡┘ä┘ê┘ä┘ç┘à ╪º┘ä╪Ñ╪¿╪»╪º╪╣┘è╪⌐ ┘ä╪º ┘à╪½┘è┘ä ┘ä┘ç╪º.'),(2,'en','Fatima Al-Sayed','Bloom Beauty','The digital marketing campaign they designed for us tripled our online sales in just three months. Their creative solutions are unmatched.'),(3,'ar','╪»┘è┘ü┘è╪» ╪▒┘è┘å┘ê┘ä╪»╪▓','┘à╪¼┘à┘ê╪╣╪⌐ ╪º┘ä╪«┘ä┘è╪¼ ╪º┘ä┘ä┘ê╪¼╪│╪¬┘è╪⌐','╪¬╪╖╪¿┘è┘é ╪│╪╖╪¡ ╪º┘ä┘à┘â╪¬╪¿ ╪º┘ä┘à╪«╪╡╪╡ ╪º┘ä╪░┘è ╪╡┘à┘à┘ê┘ç ┘ä┘å╪º ╪│┘ç┘æ┘ä ╪╣┘à┘ä┘è╪⌐ ╪¬╪¬╪¿╪╣ ╪º┘ä╪ú╪│╪╖┘ê┘ä ╪¿╪º┘ä┘â╪º┘à┘ä. ┘ü╪▒┘è┘é ╪╣╪º┘ä┘è ╪º┘ä╪º╪¡╪¬╪▒╪º┘ü┘è╪⌐ ┘ê╪│╪▒┘è╪╣ ╪º┘ä╪º╪│╪¬╪¼╪º╪¿╪⌐.'),(3,'en','David Reynolds','Gulf Logistics Group','Their custom Windows desktop application streamlined our entire fleet tracking process. Highly professional and responsive team.');
/*!40000 ALTER TABLE `testimonial_translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_image_url` varchar(255) DEFAULT '',
  `rating` int(11) DEFAULT 5,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'',5,1,1,'2026-02-23 18:13:56','2026-02-23 18:13:56'),(2,'',5,2,1,'2026-02-23 18:13:56','2026-02-23 18:13:56'),(3,'',4,3,1,'2026-02-23 18:13:56','2026-02-23 18:13:56');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_key` varchar(200) NOT NULL,
  `locale` varchar(5) NOT NULL DEFAULT 'en',
  `trans_value` text NOT NULL,
  `trans_group` varchar(50) DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_trans_key_locale` (`trans_key`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translations`
--

LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-24  0:55:38

