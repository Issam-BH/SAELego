-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: lego_db
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `address` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `line1` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `country` char(2) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'FR',
  `is_default` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_addr_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `address`
--

LOCK TABLES `address` WRITE;
/*!40000 ALTER TABLE `address` DISABLE KEYS */;
INSERT INTO `address` VALUES (1,1,'123 hdjfgh','Paris','75001','FR',1),(2,1,'HDFISUJOFUDHGYDIJOZPOIHG','DSFDS','94100','FR',1);
/*!40000 ALTER TABLE `address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `basket`
--

DROP TABLE IF EXISTS `basket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `basket` (
  `basket_id` int unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`basket_id`),
  KEY `fk_basket_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `basket`
--

LOCK TABLES `basket` WRITE;
/*!40000 ALTER TABLE `basket` DISABLE KEYS */;
/*!40000 ALTER TABLE `basket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `basket_item`
--

DROP TABLE IF EXISTS `basket_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `basket_item` (
  `basket_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `unique_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`basket_id`,`unique_id`),
  KEY `fk_bi_user` (`user_id`),
  KEY `fk_bi_brick` (`unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `basket_item`
--

LOCK TABLES `basket_item` WRITE;
/*!40000 ALTER TABLE `basket_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `basket_item` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_bi_before_insert` BEFORE INSERT ON `basket_item` FOR EACH ROW BEGIN
    DECLARE v_basket_user INT;

    SELECT user_id INTO v_basket_user
    FROM basket
    WHERE basket_id = NEW.basket_id;

    IF v_basket_user IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Panier inexistant';
    END IF;

    IF v_basket_user <> NEW.user_id THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Utilisateur du panier incorrect';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `brick_spec`
--

DROP TABLE IF EXISTS `brick_spec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brick_spec` (
  `spec_id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `width` decimal(5,2) NOT NULL,
  `lenght` decimal(5,2) NOT NULL,
  PRIMARY KEY (`spec_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brick_spec`
--

LOCK TABLES `brick_spec` WRITE;
/*!40000 ALTER TABLE `brick_spec` DISABLE KEYS */;
INSERT INTO `brick_spec` VALUES (1,'4-6',4.00,6.00),(2,'4-8',4.00,8.00),(3,'4-10',4.00,10.00),(4,'1-12',1.00,12.00),(5,'3-3-1245',3.00,3.00),(6,'2-14',2.00,14.00),(7,'6-8',6.00,8.00),(8,'4-4',4.00,4.00),(9,'1-2',1.00,2.00),(10,'2-3',2.00,3.00),(11,'1-8',1.00,8.00),(12,'6-6',6.00,6.00),(13,'2-2-1',2.00,2.00),(14,'6-8',6.00,8.00),(15,'2-12',2.00,12.00),(16,'6-14',6.00,14.00),(17,'2-16',2.00,16.00),(18,'2-2',2.00,2.00),(19,'1-1',1.00,1.00),(20,'6-16',6.00,16.00),(21,'1-4',1.00,4.00),(22,'2-6',2.00,6.00),(23,'2-4',2.00,4.00),(24,'6-10',6.00,10.00),(25,'8-8',8.00,8.00),(26,'6-24',6.00,24.00),(27,'1-1',1.00,1.00),(28,'6-12',6.00,12.00),(29,'2-3-1',2.00,3.00),(30,'1-6',1.00,6.00),(31,'2-10',2.00,10.00),(32,'1-5',1.00,5.00),(33,'3-3-0268',3.00,3.00),(34,'4-12',4.00,12.00),(35,'3-3',3.00,3.00),(36,'1-3',1.00,3.00),(37,'4-4-2367',4.00,4.00),(38,'8-11',8.00,11.00),(39,'1-10',1.00,10.00),(40,'16-16',16.00,16.00),(41,'2-8',2.00,8.00);
/*!40000 ALTER TABLE `brick_spec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `color`
--

DROP TABLE IF EXISTS `color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `color` (
  `color_id` int unsigned NOT NULL AUTO_INCREMENT,
  `hex_code` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_fr` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `color`
--

LOCK TABLES `color` WRITE;
/*!40000 ALTER TABLE `color` DISABLE KEYS */;
INSERT INTO `color` VALUES (1,'05131D','Black','Noir'),(2,'237841','Green','Vert'),(3,'008F9B','Dark Turquoise',''),(4,'C91A09','Red','Rouge'),(5,'C870A0','Dark Pink',''),(6,'583927','Brown','Marron'),(7,'9BA19D','Light Gray','Gris clair'),(8,'6D6E5C','Dark Gray','Gris foncé'),(9,'B4D2E3','Light Blue','Bleu clair'),(10,'4B9F4A','Bright Green',''),(11,'55A5AF','Light Turquoise',''),(12,'F2705E','Salmon',''),(13,'FC97AC','Pink','Rose'),(14,'F2CD37','Yellow','Jaune'),(15,'FFFFFF','White','Blanc'),(17,'C2DAB8','Light Green','Vert clair'),(18,'FBE696','Light Yellow',''),(19,'E4CD9E','Tan',''),(20,'C9CAE2','Light Violet',''),(21,'D4D5C9','Glow In Dark Opaque',''),(22,'81007B','Purple','Pourpre'),(23,'2032B0','Dark Blue-Violet',''),(25,'FE8A18','Orange','Orange'),(26,'923978','Magenta','Magenta'),(27,'BBE90B','Lime',''),(28,'958A73','Dark Tan',''),(29,'E4ADC8','Bright Pink',''),(30,'AC78BA','Medium Lavender',''),(31,'E1D5ED','Lavender',''),(32,'635F52','Trans-Black IR Lens',''),(33,'0020A0','Trans-Dark Blue',''),(34,'84B68D','Trans-Green',''),(35,'D9E4A7','Trans-Bright Green',''),(36,'C91A09','Trans-Red',''),(40,'635F52','Trans-Brown',''),(41,'AEEFEC','Trans-Light Blue',''),(42,'F8F184','Trans-Neon Green',''),(43,'C1DFF0','Trans-Very Lt Blue',''),(45,'DF6695','Trans-Dark Pink',''),(46,'F5CD2F','Trans-Yellow',''),(47,'FCFCFC','Trans-Clear',''),(52,'A5A5CB','Trans-Purple',''),(54,'DAB000','Trans-Neon Yellow',''),(57,'FF800D','Trans-Neon Orange',''),(60,'645A4C','Chrome Antique Brass',''),(61,'6C96BF','Chrome Blue',''),(62,'3CB371','Chrome Green',''),(63,'AA4D8E','Chrome Pink',''),(64,'1B2A34','Chrome Black',''),(68,'F3CF9B','Very Light Orange',''),(69,'CD6298','Light Purple',''),(70,'582A12','Reddish Brown',''),(71,'A0A5A9','Light Bluish Gray',''),(72,'6C6E68','Dark Bluish Gray',''),(73,'5A93DB','Medium Blue',''),(74,'73DCA1','Medium Green',''),(75,'05131D','Speckle Black-Copper',''),(76,'6C6E68','Speckle DBGray-Silver',''),(77,'FECCCF','Light Pink',''),(78,'F6D7B3','Light Nougat',''),(79,'FFFFFF','Milky White',''),(80,'A5A9B4','Metallic Silver',''),(81,'899B5F','Metallic Green',''),(82,'DBAC34','Metallic Gold',''),(84,'AA7D55','Medium Nougat',''),(85,'3F3691','Dark Purple',''),(86,'7C503A','Light Brown',''),(89,'4C61DB','Royal Blue',''),(92,'D09168','Nougat',''),(100,'FEBABD','Light Salmon',''),(110,'4354A3','Violet',''),(112,'6874CA','Medium Bluish Violet',''),(114,'DF6695','Glitter Trans-Dark Pink',''),(115,'C7D23C','Medium Lime',''),(117,'FFFFFF','Glitter Trans-Clear',''),(118,'B3D7D1','Aqua',''),(120,'D9E4A7','Light Lime',''),(125,'F9BA61','Light Orange',''),(129,'A5A5CB','Glitter Trans-Purple',''),(132,'05131D','Speckle Black-Silver',''),(133,'05131D','Speckle Black-Gold',''),(134,'AE7A59','Copper',''),(135,'9CA3A8','Pearl Light Gray',''),(137,'7988A1','Pearl Sand Blue',''),(142,'DCBC81','Pearl Light Gold',''),(143,'CFE2F7','Trans-Medium Blue',''),(148,'575857','Pearl Dark Gray',''),(150,'ABADAC','Pearl Very Light Gray',''),(151,'E6E3E0','Very Light Bluish Gray',''),(158,'DFEEA5','Yellowish Green',''),(178,'B48455','Flat Dark Gold',''),(179,'898788','Flat Silver',''),(182,'F08F1C','Trans-Orange',''),(183,'F2F3F2','Pearl White',''),(191,'F8BB3D','Bright Light Orange',''),(212,'9FC3E9','Bright Light Blue',''),(216,'B31004','Rust',''),(226,'FFF03A','Bright Light Yellow',''),(230,'E4ADC8','Trans-Pink',''),(232,'7DBFDD','Sky Blue',''),(236,'96709F','Trans-Light Purple',''),(272,'0A3463','Dark Blue','Bleu foncé'),(288,'184632','Dark Green','Vert foncé'),(294,'BDC6AD','Glow In Dark Trans',''),(297,'AA7F2E','Pearl Gold',''),(308,'352100','Dark Brown',''),(313,'3592C3','Maersk Blue',''),(320,'720E0F','Dark Red',''),(321,'078BC9','Dark Azure',''),(322,'36AEBF','Medium Azure',''),(323,'ADC3C0','Light Aqua',''),(326,'9B9A5A','Olive Green',''),(334,'BBA53D','Chrome Gold',''),(335,'D67572','Sand Red',''),(351,'F785B1','Medium Dark Pink',''),(366,'FA9C1C','Earth Orange',''),(373,'845E84','Sand Purple',''),(378,'A0BCAC','Sand Green',''),(379,'6074A1','Sand Blue',''),(383,'E0E0E0','Chrome Silver',''),(450,'B67B50','Fabuland Brown',''),(462,'FFA70B','Medium Orange',''),(484,'A95500','Dark Orange',''),(503,'E6E3DA','Very Light Gray',''),(1000,'D9D9D9','Glow in Dark White',''),(1001,'9391E4','Medium Violet',''),(1002,'C0F500','Glitter Trans-Neon Green',''),(1003,'68BCC5','Glitter Trans-Light Blue',''),(1004,'FCB76D','Trans-Flame Yellowish Orange',''),(1005,'FBE890','Trans-Fire Yellow',''),(1006,'B4D4F7','Trans-Light Royal Blue',''),(1007,'8E5597','Reddish Lilac',''),(1008,'039CBD','Vintage Blue',''),(1009,'1E601E','Vintage Green',''),(1010,'CA1F08','Vintage Red',''),(1011,'F3C305','Vintage Yellow',''),(1012,'EF9121','Fabuland Orange',''),(1013,'F4F4F4','Modulex White',''),(1014,'AfB5C7','Modulex Light Bluish Gray',''),(1015,'9C9C9C','Modulex Light Gray',''),(1016,'595D60','Modulex Charcoal Gray',''),(1017,'6B5A5A','Modulex Tile Gray',''),(1018,'4D4C52','Modulex Black',''),(1019,'330000','Modulex Tile Brown',''),(1020,'5C5030','Modulex Terracotta',''),(1021,'907450','Modulex Brown',''),(1022,'DEC69C','Modulex Buff',''),(1023,'B52C20','Modulex Red',''),(1024,'F45C40','Modulex Pink Red',''),(1025,'F47B30','Modulex Orange',''),(1026,'F7AD63','Modulex Light Orange',''),(1027,'FFE371','Modulex Light Yellow',''),(1028,'FED557','Modulex Ochre Yellow',''),(1029,'BDC618','Modulex Lemon',''),(1030,'7DB538','Modulex Pastel Green',''),(1031,'7C9051','Modulex Olive Green',''),(1032,'27867E','Modulex Aqua Green',''),(1033,'467083','Modulex Teal Blue',''),(1034,'0057A6','Modulex Tile Blue',''),(1035,'61AFFF','Modulex Medium Blue',''),(1036,'68AECE','Modulex Pastel Blue',''),(1037,'BD7D85','Modulex Violet',''),(1038,'F785B1','Modulex Pink',''),(1039,'FFFFFF','Modulex Clear',''),(1040,'595D60','Modulex Foil Dark Gray',''),(1041,'9C9C9C','Modulex Foil Light Gray',''),(1042,'006400','Modulex Foil Dark Green',''),(1043,'7DB538','Modulex Foil Light Green',''),(1044,'0057A6','Modulex Foil Dark Blue',''),(1045,'68AECE','Modulex Foil Light Blue',''),(1046,'4B0082','Modulex Foil Violet',''),(1047,'8B0000','Modulex Foil Red',''),(1048,'FED557','Modulex Foil Yellow',''),(1049,'F7AD63','Modulex Foil Orange',''),(1050,'FF698F','Coral',''),(1051,'5AC4DA','Pastel Blue',''),(1052,'F08F1C','Glitter Trans-Orange',''),(1053,'68BCC5','Opal Trans-Light Blue',''),(1054,'CE1D9B','Opal Trans-Dark Pink',''),(1055,'FCFCFC','Opal Trans-Clear',''),(1056,'583927','Opal Trans-Brown',''),(1057,'C9E788','Trans-Light Bright Green',''),(1058,'94E5AB','Trans-Light Green',''),(1059,'8320B7','Opal Trans-Purple',''),(1060,'84B68D','Opal Trans-Bright Green',''),(1061,'0020A0','Opal Trans-Dark Blue',''),(1062,'EBD800','Vibrant Yellow',''),(1063,'B46A00','Pearl Copper',''),(1064,'FF8014','Fabuland Red',''),(1065,'AC8247','Reddish Gold',''),(1066,'DD982E','Curry',''),(1067,'AD6140','Dark Nougat',''),(1068,'EE5434','Bright Reddish Orange',''),(1069,'D60026','Pearl Red',''),(1070,'0059A3','Pearl Blue',''),(1071,'008E3C','Pearl Green',''),(1072,'57392C','Pearl Brown',''),(1073,'0A1327','Pearl Black',''),(1074,'009ECE','Duplo Blue',''),(1075,'3E95B6','Duplo Medium Blue',''),(1076,'FFF230','Duplo Lime',''),(1077,'78FC78','Fabuland Lime',''),(1078,'468A5F','Duplo Medium Green',''),(1079,'60BA76','Duplo Light Green',''),(1080,'F3C988','Light Tan',''),(1081,'872B17','Rust Orange',''),(1082,'FE78B0','Clikits Pink',''),(1083,'945148','Two-tone Copper',''),(1084,'AB673A','Two-tone Gold',''),(1085,'737271','Two-tone Silver',''),(1086,'6A7944','Pearl Lime',''),(1087,'FF879C','Duplo Pink',''),(1088,'755945','Medium Brown',''),(1089,'CCA373','Warm Tan',''),(1090,'3FB69E','Duplo Turquoise',''),(1091,'FFCB78','Warm Yellowish Orange',''),(1092,'764D3B','Metallic Copper',''),(1093,'9195CA','Light Lilac',''),(1094,'8D73B3','Trans-Medium Purple',''),(1095,'635F52','Trans-Black',''),(1096,'D9E4A7','Glitter Trans-Bright Green',''),(1097,'8D73B3','Glitter Trans-Medium Purple',''),(1098,'84B68D','Glitter Trans-Green',''),(1099,'E4ADC8','Glitter Trans-Pink',''),(1100,'FFCF0B','Clikits Yellow',''),(1101,'5F27AA','Duplo Dark Purple',''),(1102,'FF0040','Trans-Neon Red',''),(1103,'3E3C39','Pearl Titanium',''),(1104,'B3D7D1','HO Aqua',''),(1105,'1591cb','HO Azure',''),(1106,'354e5a','HO Blue-gray',''),(1107,'5b98b3','HO Cyan',''),(1108,'a7dccf','HO Dark Aqua',''),(1109,'0A3463','HO Dark Blue',''),(1110,'6D6E5C','HO Dark Gray',''),(1111,'184632','HO Dark Green',''),(1112,'b2b955','HO Dark Lime',''),(1113,'631314','HO Dark Red',''),(1114,'627a62','HO Dark Sand Green',''),(1115,'10929d','HO Dark Turquoise',''),(1116,'bb771b','HO Earth Orange',''),(1117,'b4a774','HO Gold',''),(1118,'a3d1c0','HO Light Aqua',''),(1119,'965336','HO Light Brown',''),(1120,'cdc298','HO Light Gold',''),(1121,'f9f1c7','HO Light Tan',''),(1122,'f5fab7','HO Light Yellow',''),(1123,'7396c8','HO Medium Blue',''),(1124,'c01111','HO Medium Red',''),(1125,'0d4763','HO Metallic Blue',''),(1126,'5e5e5e','HO Metallic Dark Gray',''),(1127,'879867','HO Metallic Green',''),(1128,'5f7d8c','HO Metallic Sand Blue',''),(1129,'9B9A5A','HO Olive Green',''),(1130,'d06262','HO Rose',''),(1131,'6e8aa6','HO Sand Blue',''),(1132,'A0BCAC','HO Sand Green',''),(1133,'E4CD9E','HO Tan',''),(1134,'616161','HO Titanium',''),(1135,'A5ADB4','Metal',''),(1136,'CA4C0B','Reddish Orange',''),(1137,'915C3C','Sienna Brown',''),(1138,'5E3F33','Umber Brown',''),(1139,'F5CD2F','Opal Trans-Yellow',''),(1140,'EC4612','Neon Orange',''),(1141,'D2FC43','Neon Green',''),(1142,'5d5c36','Dark Olive Green',''),(1143,'FFFFFF','Glitter Milky White',''),(1144,'CE3021','Chrome Red',''),(1145,'DD9E47','Ochre Yellow',''),(9999,'05131D','[No Color/Any Color]','');
/*!40000 ALTER TABLE `color` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country_names`
--

DROP TABLE IF EXISTS `country_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country_names` (
  `country_code` char(2) COLLATE utf8mb4_general_ci NOT NULL,
  `name_en` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `name_fr` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country_names`
--

LOCK TABLES `country_names` WRITE;
/*!40000 ALTER TABLE `country_names` DISABLE KEYS */;
INSERT INTO `country_names` VALUES ('AT','Austria','Autriche'),('AU','Australia','Australie'),('BE','Belgium','Belgique'),('CA','Canada','Canada'),('CH','Switzerland','Suisse'),('CN','China','Chine'),('CZ','Czech Republic','République Tchèque'),('DE','Germany','Allemagne'),('DK','Denmark','Danemark'),('ES','Spain','Espagne'),('FI','Finland','Finlande'),('FR','France','France'),('GB','United Kingdom','Royaume-Uni'),('IT','Italy','Italie'),('JP','Japan','Japon'),('NL','Netherlands','Pays-Bas'),('NO','Norway','Norvège'),('PL','Poland','Pologne'),('SE','Sweden','Suède'),('US','United States','États-Unis');
/*!40000 ALTER TABLE `country_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `image_id` int unsigned NOT NULL AUTO_INCREMENT,
  `image` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`image_id`),
  KEY `fk_images_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_images_before_insert` BEFORE INSERT ON `images` FOR EACH ROW BEGIN
    IF NEW.upload_date IS NULL THEN
        SET NEW.upload_date = NOW();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `content_json` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `fk_invoice_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
INSERT INTO `invoices` VALUES (1,1,'FACT-20260217-20E1','{\"client\":{\"firstname\":\"Yevhen\",\"lastname\":\"KEFA\",\"email\":\"eugenekefa04@gmail.com\"},\"shipping_address\":{\"line1\":\"123 hdjfgh\",\"city\":\"Paris\",\"zip\":\"75001\",\"country\":\"FR\"},\"items\":[{\"description\":\"Mosa\\u00efque LEGO\\u00ae (64x64)\",\"quantity\":1,\"unit_price\":\"59.2\",\"total\":\"59.2\"}],\"payment\":{\"method\":\"Carte Bancaire\",\"date\":\"2026-02-17 09:22:12\",\"total\":\"59.2 \\u20ac\"}}','2026-02-17 09:22:12'),(2,2,'FACT-20260310-9587','{\"client\":{\"firstname\":\"Yevhen\",\"lastname\":\"KEFA\",\"email\":\"eugenekefa04@gmail.com\"},\"shipping_address\":{\"line1\":\"HDFISUJOFUDHGYDIJOZPOIHG\",\"city\":\"DSFDS\",\"zip\":\"94100\",\"country\":\"FR\"},\"items\":[{\"description\":\"Mosa\\u00efque LEGO\\u00ae (64x64)\",\"quantity\":1,\"unit_price\":\"409.6\",\"total\":\"409.6\"}],\"payment\":{\"method\":\"Carte Bancaire\",\"date\":\"2026-03-10 14:30:50\",\"total\":\"409.6 \\u20ac\"}}','2026-03-10 14:30:50');
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manufactured_brick`
--

DROP TABLE IF EXISTS `manufactured_brick`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufactured_brick` (
  `unique_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `certif_num` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image_id` int unsigned DEFAULT NULL,
  `color_id` int unsigned NOT NULL,
  `stock_id` int unsigned NOT NULL,
  `spec_id` int unsigned NOT NULL,
  PRIMARY KEY (`unique_id`),
  UNIQUE KEY `serial_number` (`serial_number`),
  KEY `fk_mb_image` (`image_id`),
  KEY `fk_mb_color` (`color_id`),
  KEY `fk_mb_stock` (`stock_id`),
  KEY `fk_mb_spec` (`spec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manufactured_brick`
--

LOCK TABLES `manufactured_brick` WRITE;
/*!40000 ALTER TABLE `manufactured_brick` DISABLE KEYS */;
/*!40000 ALTER TABLE `manufactured_brick` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mosaic`
--

DROP TABLE IF EXISTS `mosaic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mosaic` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uploads_id` int NOT NULL,
  `filter_used` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `size_option` int NOT NULL,
  `estimated_price` decimal(10,2) NOT NULL,
  `brick_data` longtext COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_mosaic_upload` (`uploads_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mosaic`
--

LOCK TABLES `mosaic` WRITE;
/*!40000 ALTER TABLE `mosaic` DISABLE KEYS */;
/*!40000 ALTER TABLE `mosaic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `mosaic_id` int NOT NULL,
  `shipping_address_id` int NOT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'paid',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'card',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `fk_order_user` (`user_id`),
  KEY `fk_order_mosaic` (`mosaic_id`),
  KEY `fk_order_addr` (`shipping_address_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `payment_id` int unsigned NOT NULL AUTO_INCREMENT,
  `CB_code` varchar(19) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CB_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CB_CVV` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CB_expirationdate` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payment_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock` (
  `stock_id` int unsigned NOT NULL AUTO_INCREMENT,
  `quantity` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`stock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_color`
--

DROP TABLE IF EXISTS `stock_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_color` (
  `stock_id` int unsigned NOT NULL,
  `color_id` int unsigned NOT NULL,
  PRIMARY KEY (`stock_id`,`color_id`),
  KEY `fk_sc_color` (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_color`
--

LOCK TABLES `stock_color` WRITE;
/*!40000 ALTER TABLE `stock_color` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_color` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uploads` (
  `id_upload` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `image_data` longblob NOT NULL,
  `image_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_upload`),
  KEY `fk_uploads_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploads`
--
LOCK TABLES `uploads` WRITE;
/*!40000 ALTER TABLE `uploads` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploads` ENABLE KEYS */;
UNLOCK TABLES;
--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `level` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `fk_log_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_log`
--

LOCK TABLES `user_log` WRITE;
/*!40000 ALTER TABLE `user_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_year` int DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `two_factor_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_expires_at` datetime DEFAULT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `totp_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `totp_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'YYYY','eugenekefa04@gmail.com','$2y$10$9.aqwFKxIDBkUhtMPwUSmeE3xxIP/ACqh/1HhbMnJVS38YbKDIDVK','Yevhen','KEFA','+33604107070',2004,'','user',0,'2026-02-17 08:51:28',NULL,'2026-02-25 15:55:31',NULL,NULL,'6ACC2UUEJFVQ7Q5E',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-13  8:20:34
