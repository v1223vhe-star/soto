-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: gestion_escolar
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
-- Table structure for table `anios_escolares`
--

DROP TABLE IF EXISTS `anios_escolares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anios_escolares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anios_escolares`
--

LOCK TABLES `anios_escolares` WRITE;
/*!40000 ALTER TABLE `anios_escolares` DISABLE KEYS */;
INSERT INTO `anios_escolares` VALUES (7,'2026-2027','2026-09-20','2027-07-20',1);
/*!40000 ALTER TABLE `anios_escolares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencia`
--

DROP TABLE IF EXISTS `asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `materia_anio_escolar_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `asistio` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `materia_anio_escolar_id` (`materia_anio_escolar_id`),
  CONSTRAINT `asistencia_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`),
  CONSTRAINT `asistencia_ibfk_2` FOREIGN KEY (`materia_anio_escolar_id`) REFERENCES `materias_anio_escolar` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencia`
--

LOCK TABLES `asistencia` WRITE;
/*!40000 ALTER TABLE `asistencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `asistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `datos_academicos`
--

DROP TABLE IF EXISTS `datos_academicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datos_academicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `escolaridad` enum('regular','pendiente','repitiente','doble_inscripcion','repite_pendiente','equivalencia') NOT NULL,
  `grado_ano` varchar(50) NOT NULL,
  `seccion` varchar(50) NOT NULL,
  `periodo` varchar(50) NOT NULL,
  `promedio` decimal(5,2) DEFAULT NULL,
  `grado` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  CONSTRAINT `datos_academicos_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datos_academicos`
--

LOCK TABLES `datos_academicos` WRITE;
/*!40000 ALTER TABLE `datos_academicos` DISABLE KEYS */;
INSERT INTO `datos_academicos` VALUES (4,11,'2025-03-04','regular','','A','2025-2026',NULL,'6'),(5,12,'2025-03-04','equivalencia','','A','2025-2026',NULL,'5');
/*!40000 ALTER TABLE `datos_academicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docentes`
--

DROP TABLE IF EXISTS `docentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docentes`
--

LOCK TABLES `docentes` WRITE;
/*!40000 ALTER TABLE `docentes` DISABLE KEYS */;
/*!40000 ALTER TABLE `docentes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `representante_id` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `sexo` enum('masculino','femenino') NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `estado` varchar(100) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `plantel_procedencia` varchar(100) DEFAULT NULL,
  `correo_electronico` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
INSERT INTO `estudiantes` VALUES (11,10,'28708689','Diego Eloy','Carpio Carpio','masculino','2004-01-06',21,'04127021982','FINO','Bolívar','Municipio','Ciudad Bolívar','Agua Salada',NULL),(12,11,'28705715','Juan José ','Gurmeite Lunar','masculino','2003-05-06',21,'04147623591','Regular','Bolívar','Cedeño','Caicara','\"U.E.N MANUEL MANRIQUE\"',NULL);
/*!40000 ALTER TABLE `estudiantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materias`
--

DROP TABLE IF EXISTS `materias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('cursando','finalizada') NOT NULL,
  `periodo` varchar(50) NOT NULL,
  `creditos` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias`
--

LOCK TABLES `materias` WRITE;
/*!40000 ALTER TABLE `materias` DISABLE KEYS */;
INSERT INTO `materias` VALUES (1,'Matemática','cursando','2025-2026',50),(2,'Castellano','cursando','2025-2026',50),(3,'Inglés','cursando','2025-2026',50),(4,'Biología','cursando','2025-2026',50),(6,'Geografía','cursando','2025-2026',50),(7,'Dibujo Técnico','cursando','2025-2026',50),(8,'Geografía','cursando','2025-2026',50),(9,'Física','cursando','2025-2026',50),(10,'Historia de Venezuela','cursando','2025-2026',50),(11,'Ciencias Sociales','cursando','2025-2026',50);
/*!40000 ALTER TABLE `materias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materias_actuales`
--

DROP TABLE IF EXISTS `materias_actuales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materias_actuales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `calificacion` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `materias_actuales_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`),
  CONSTRAINT `materias_actuales_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias_actuales`
--

LOCK TABLES `materias_actuales` WRITE;
/*!40000 ALTER TABLE `materias_actuales` DISABLE KEYS */;
/*!40000 ALTER TABLE `materias_actuales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materias_anio_escolar`
--

DROP TABLE IF EXISTS `materias_anio_escolar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materias_anio_escolar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anio_escolar_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `anio_escolar_id` (`anio_escolar_id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `materias_anio_escolar_ibfk_1` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`),
  CONSTRAINT `materias_anio_escolar_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias_anio_escolar`
--

LOCK TABLES `materias_anio_escolar` WRITE;
/*!40000 ALTER TABLE `materias_anio_escolar` DISABLE KEYS */;
/*!40000 ALTER TABLE `materias_anio_escolar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materias_dictadas`
--

DROP TABLE IF EXISTS `materias_dictadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materias_dictadas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `materia_id` int(11) DEFAULT NULL,
  `docente_id` int(11) DEFAULT NULL,
  `anio_escolar_id` int(11) DEFAULT NULL,
  `grado` varchar(255) DEFAULT NULL,
  `seccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materia_id` (`materia_id`),
  KEY `docente_id` (`docente_id`),
  KEY `anio_escolar_id` (`anio_escolar_id`),
  CONSTRAINT `materias_dictadas_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `materias_dictadas_ibfk_2` FOREIGN KEY (`docente_id`) REFERENCES `docentes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `materias_dictadas_ibfk_3` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materias_dictadas`
--

LOCK TABLES `materias_dictadas` WRITE;
/*!40000 ALTER TABLE `materias_dictadas` DISABLE KEYS */;
/*!40000 ALTER TABLE `materias_dictadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matricula`
--

DROP TABLE IF EXISTS `matricula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matricula` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `anio_escolar_id` int(11) NOT NULL,
  `fecha_matricula` date DEFAULT NULL,
  `grado` varchar(255) DEFAULT NULL,
  `seccion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `anio_escolar_id` (`anio_escolar_id`),
  KEY `estudiante_id` (`estudiante_id`),
  CONSTRAINT `matricula_ibfk_1` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`),
  CONSTRAINT `matricula_ibfk_2` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matricula`
--

LOCK TABLES `matricula` WRITE;
/*!40000 ALTER TABLE `matricula` DISABLE KEYS */;
/*!40000 ALTER TABLE `matricula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `representantes`
--

DROP TABLE IF EXISTS `representantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `representantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `edad` int(11) NOT NULL,
  `nacionalidad` varchar(50) NOT NULL,
  `profesion_oficio` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) NOT NULL,
  `parentesco` varchar(50) NOT NULL,
  `parroquia` varchar(100) NOT NULL,
  `sector` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `correo_electronico` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `representantes`
--

LOCK TABLES `representantes` WRITE;
/*!40000 ALTER TABLE `representantes` DISABLE KEYS */;
INSERT INTO `representantes` VALUES (10,'10660558','Maricela Coromoto','Carpio Rosillo','1974-02-23',51,'Venezolano','Docente','04165908909','Madre','Agua Salada','Urb, La Ceiba','Ym6- 81','carpiodiego24@gmail.com'),(11,'15984124','Aniuska ','Lunar','1980-04-13',35,'Venezolano','Docente','04165908909','Madre','Agua Salada','Urb, La Ceiba','Ym6- 81','genericorreo@yopmail.com');
/*!40000 ALTER TABLE `representantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `pregunta_seguridad1` varchar(255) DEFAULT NULL,
  `respuesta_seguridad1` varchar(255) DEFAULT NULL,
  `pregunta_seguridad2` varchar(255) DEFAULT NULL,
  `respuesta_seguridad2` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (5,'Diego Carpio','$2y$10$sBVlpQaRfjnokvNznzXise95v3v0OoitAbfyIWd.X27HkUBBAyDq2','admin','carpiodiego24@gmail.com','2025-03-08 21:32:24','¿Cuál es el nombre de tu primera mascota?','sultan','¿Cuál es tu color favorito?','azul'),(6,'Gabriel Caraculo','$2y$10$OPE2AM6QoMIQbcuom281FOieTvKKJ91nWIIrgTdI61shLdifY.Nra','user','gabrieldelgado@yopmail.com','2025-03-08 23:15:01','¿Cuál es el nombre de tu primera mascota?','pug','¿Cuál es tu color favorito?','negro');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER limit_user_count BEFORE INSERT ON `usuarios`
FOR EACH ROW
BEGIN
  DECLARE admin_count INT;
  DECLARE user_count INT;

  -- Contar el número de administradores
  SELECT COUNT(*) INTO admin_count FROM `usuarios` WHERE `role` = 'admin';
  IF NEW.role = 'admin' AND admin_count >= 2 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se pueden registrar más de 2 administradores';
  END IF;

  -- Contar el número de usuarios
  SELECT COUNT(*) INTO user_count FROM `usuarios` WHERE `role` = 'user';
  IF NEW.role = 'user' AND user_count >= 3 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se pueden registrar más de 3 usuarios';
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-08 23:33:58
