-- Backup generado el 2025-05-18 22:01:52
-- Sistema de Asistencia y Control de Estudiantes (SACE)

SET FOREIGN_KEY_CHECKS=0;

-- Estructura para tabla anios_escolares
DROP TABLE IF EXISTS `anios_escolares`;
CREATE TABLE `anios_escolares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla anios_escolares
INSERT INTO `anios_escolares` VALUES('1','2025-2026','2025-07-05','2026-04-06','1');

-- Estructura para tabla anios_grados
DROP TABLE IF EXISTS `anios_grados`;
CREATE TABLE `anios_grados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anio_escolar_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `anio_escolar_id` (`anio_escolar_id`),
  KEY `grado_id` (`grado_id`),
  CONSTRAINT `anios_grados_ibfk_1` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`),
  CONSTRAINT `anios_grados_ibfk_2` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla anios_grados
INSERT INTO `anios_grados` VALUES('1','1','1','1');
INSERT INTO `anios_grados` VALUES('2','1','2','1');
INSERT INTO `anios_grados` VALUES('3','1','3','1');
INSERT INTO `anios_grados` VALUES('4','1','4','1');
INSERT INTO `anios_grados` VALUES('5','1','5','0');
INSERT INTO `anios_grados` VALUES('6','1','6','1');
INSERT INTO `anios_grados` VALUES('11','1','11','1');

-- Estructura para tabla asistencia
DROP TABLE IF EXISTS `asistencia`;
CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `materia_anio_escolar_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `asistio` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_asistencia` (`estudiante_id`,`materia_anio_escolar_id`,`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla asistencia
INSERT INTO `asistencia` VALUES('1','45','38','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('2','45','38','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('3','45','38','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('4','2','38','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('5','2','38','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('6','2','21','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('7','2','21','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('8','2','38','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('9','2','38','2025-05-06','','1');
INSERT INTO `asistencia` VALUES('10','2','9','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('11','2','30','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('12','2','30','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('13','2','18','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('14','2','18','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('15','2','18','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('16','2','18','2026-05-01','','1');
INSERT INTO `asistencia` VALUES('17','2','18','2024-05-01','','1');
INSERT INTO `asistencia` VALUES('18','2','18','2024-05-02','','1');
INSERT INTO `asistencia` VALUES('19','2','18','2025-05-06','','1');
INSERT INTO `asistencia` VALUES('20','2','18','2025-05-07','','1');
INSERT INTO `asistencia` VALUES('21','2','18','2025-05-08','','1');
INSERT INTO `asistencia` VALUES('22','45','38','2025-05-06','','1');
INSERT INTO `asistencia` VALUES('23','45','24','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('24','45','24','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('25','2','18','2025-05-09','','1');
INSERT INTO `asistencia` VALUES('26','45','38','2025-05-07','','1');
INSERT INTO `asistencia` VALUES('27','45','24','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('28','45','38','2026-05-01','','1');
INSERT INTO `asistencia` VALUES('29','45','24','2026-05-04','','1');
INSERT INTO `asistencia` VALUES('30','45','24','2026-12-01','','1');
INSERT INTO `asistencia` VALUES('31','45','24','2026-12-02','','1');
INSERT INTO `asistencia` VALUES('32','45','24','2026-12-03','','1');
INSERT INTO `asistencia` VALUES('33','45','38','2026-12-01','','1');
INSERT INTO `asistencia` VALUES('34','2','18','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('35','2','11','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('36','2','11','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('37','2','2','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('38','2','2','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('39','45','38','2025-05-08','','1');
INSERT INTO `asistencia` VALUES('40','45','38','2026-05-04','','1');
INSERT INTO `asistencia` VALUES('41','45','24','2026-05-05','','1');
INSERT INTO `asistencia` VALUES('42','45','24','2026-05-06','','1');
INSERT INTO `asistencia` VALUES('43','45','21','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('44','45','21','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('45','45','38','2026-05-05','','1');
INSERT INTO `asistencia` VALUES('46','45','33','2026-05-07','','1');
INSERT INTO `asistencia` VALUES('47','46','2','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('48','46','2','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('49','50','2','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('50','50','2','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('51','50','2','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('52','50','2','2025-05-06','','1');
INSERT INTO `asistencia` VALUES('53','45','2','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('54','45','2','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('55','45','2','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('56','45','2','2025-05-06','','1');
INSERT INTO `asistencia` VALUES('57','48','18','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('58','45','21','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('59','46','33','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('60','46','33','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('61','61','38','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('62','61','38','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('63','53','18','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('64','53','18','2025-05-13','','1');
INSERT INTO `asistencia` VALUES('65','2','18','2025-05-13','','1');
INSERT INTO `asistencia` VALUES('66','2','2','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('67','53','2','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('68','53','41','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('69','2','41','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('70','53','7','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('71','2','7','2025-05-12','','1');
INSERT INTO `asistencia` VALUES('72','61','24','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('73','61','21','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('74','53','18','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('75','61','2','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('76','61','38','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('77','61','38','2025-05-06','','1');
INSERT INTO `asistencia` VALUES('78','61','38','2025-05-07','','1');
INSERT INTO `asistencia` VALUES('79','46','38','2025-05-01','','1');
INSERT INTO `asistencia` VALUES('80','46','38','2025-05-02','','1');
INSERT INTO `asistencia` VALUES('81','46','38','2025-05-05','','1');
INSERT INTO `asistencia` VALUES('82','61','2','2025-05-06','','1');
INSERT INTO `asistencia` VALUES('83','53','66','2025-05-01','Thu','1');
INSERT INTO `asistencia` VALUES('84','2','67','2025-05-01','Thu','1');
INSERT INTO `asistencia` VALUES('85','2','67','2025-05-02','Fri','1');
INSERT INTO `asistencia` VALUES('86','59','38','2025-05-01','Thu','1');

-- Estructura para tabla datos_academicos
DROP TABLE IF EXISTS `datos_academicos`;
CREATE TABLE `datos_academicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estudiante_id` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `escolaridad` enum('regular','pendiente','repitiente','doble_inscripcion','repite_pendiente','equivalencia') NOT NULL,
  `anio_grado_id` int(11) NOT NULL,
  `seccion` varchar(50) NOT NULL,
  `anio_escolar_id` int(11) NOT NULL,
  `promedio` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiante_id` (`estudiante_id`),
  KEY `datos_academicos_ibfk_2` (`anio_grado_id`),
  CONSTRAINT `datos_academicos_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`),
  CONSTRAINT `datos_academicos_ibfk_2` FOREIGN KEY (`anio_grado_id`) REFERENCES `anios_grados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla datos_academicos
INSERT INTO `datos_academicos` VALUES('2','2','2025-05-01','regular','2','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('20','45','2025-05-06','regular','1','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('21','46','2025-05-10','regular','1','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('22','47','2025-05-10','regular','1','B','1','0.00');
INSERT INTO `datos_academicos` VALUES('23','48','2025-05-10','regular','4','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('27','52','2025-05-10','regular','1','C','1','0.00');
INSERT INTO `datos_academicos` VALUES('28','53','2025-05-10','regular','2','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('29','54','2025-05-10','regular','2','B','1','0.00');
INSERT INTO `datos_academicos` VALUES('30','55','2025-05-10','regular','2','C','1','0.00');
INSERT INTO `datos_academicos` VALUES('31','56','2025-05-10','regular','3','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('32','57','2025-05-10','regular','3','B','1','0.00');
INSERT INTO `datos_academicos` VALUES('33','58','2025-05-10','regular','3','C','1','0.00');
INSERT INTO `datos_academicos` VALUES('34','59','2025-05-10','regular','3','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('35','60','2025-05-08','regular','1','A','1','0.00');
INSERT INTO `datos_academicos` VALUES('36','61','2025-05-09','regular','1','A','1','0.00');

-- Estructura para tabla estudiantes
DROP TABLE IF EXISTS `estudiantes`;
CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `representante_id` int(11) DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla estudiantes
INSERT INTO `estudiantes` VALUES('2','2','V-30367235','Jorge Luis','Ortega Baena','masculino','2004-02-05','21','04249065920','Fino','Bolívar','Heres','Ciudad Bolívar','Carlos Emiliano Salom');
INSERT INTO `estudiantes` VALUES('45','30','V-67890123','Jorge antonio','Torres Pérez','masculino','2009-11-11','15','041604281234572','Finoooo','Bolívar','Heres','Las Minas','Jardín de Infancia Alegríaa');
INSERT INTO `estudiantes` VALUES('46','31','V-87654321','Carlos José','González López','masculino','2018-05-10','7','04142345678','Alergia a polvo','Bolívar','Heres','Ciudad Bolívar','Jardín de Infancia Alegría');
INSERT INTO `estudiantes` VALUES('47','32','V-98765432','Ana Sofía','Martínez García','femenino','2018-02-15','7','04163456789','Ninguna','Bolívar','Heres','Ciudad Bolívar','Jardín de Infancia Arcoíris');
INSERT INTO `estudiantes` VALUES('48','33','V-09876543','Pedro Luis','Rodríguez Pérez','masculino','2017-08-20','8','04242345678','Usa lentes','Bolívar','Heres','Ciudad Bolívar','U.E. Carlos Emiliano Salom');
INSERT INTO `estudiantes` VALUES('49','34','V-10987654','Valentina María','Hernández López','femenino','2016-10-05','9','04164567890','Ninguna','Bolívar','Heres','Ciudad Bolívar','U.E. República del Perú');
INSERT INTO `estudiantes` VALUES('52','37','V-09876543','Pedro Luis','Rodríguez Pérez','masculino','2018-08-20','7','04242345678','Usa lentes','Bolívar','Heres','Ciudad Bolívar','Jardín de Infancia Los Pinos');
INSERT INTO `estudiantes` VALUES('53','38','V-10987654','Valentina María','Hernández López','femenino','2017-10-05','8','04164567890','Ninguna','Bolívar','Heres','Ciudad Bolívar','U.E. Carlos Emiliano Salom');
INSERT INTO `estudiantes` VALUES('54','39','V-21098765','Diego Alejandro','Díaz Rojas','masculino','2017-03-12','8','04263456789','Asma leve','Bolívar','Heres','Ciudad Bolívar','U.E. Carlos Emiliano Salom');
INSERT INTO `estudiantes` VALUES('55','40','V-32109876','María Gabriela','Pérez Martínez','femenino','2017-07-18','8','04145678901','Ninguna','Bolívar','Heres','Ciudad Bolívar','U.E. República del Perú');
INSERT INTO `estudiantes` VALUES('56','41','V-43210987','Luis Miguel','García Díaz','masculino','2016-01-22','9','04244567890','Ninguna','Bolívar','Heres','Ciudad Bolívar','U.E. Manuel Piar');
INSERT INTO `estudiantes` VALUES('57','42','V-54321098','Sofía Camila','López García','femenino','2016-11-08','9','04166789012','Ninguna','Bolívar','Heres','Ciudad Bolívar','U.E. Andrés Bello');
INSERT INTO `estudiantes` VALUES('58','43','V-65432109','Javier Antonio','Sánchez Rodríguez','masculino','2016-04-17','9','04267890123','Alergia a mariscos','Bolívar','Heres','Ciudad Bolívar','U.E. República de Chile');
INSERT INTO `estudiantes` VALUES('59','44','V-76543210','Andrea Carolina','Fernández López','femenino','2016-09-03','9','04148901234','Ninguna','Bolívar','Heres','Ciudad Bolívar','U.E. República de Argentina');
INSERT INTO `estudiantes` VALUES('60','45','V-1236742323','Antonia','Lopez','femenino','2015-11-11','7','0416122323244','Fino ','Bolívar','Heres','Las Minas','Jardín de Infancia Alegría');
INSERT INTO `estudiantes` VALUES('61','46','V-283662324','Rivero ','Garcia','masculino','2016-11-11','8','041623465345','Fino ','Bolívar','Heres','Ciudad Bolívar','Agua Salada');

-- Estructura para tabla grados
DROP TABLE IF EXISTS `grados`;
CREATE TABLE `grados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla grados
INSERT INTO `grados` VALUES('1','1 ');
INSERT INTO `grados` VALUES('2','2 ');
INSERT INTO `grados` VALUES('3','3');
INSERT INTO `grados` VALUES('4','4');
INSERT INTO `grados` VALUES('5','1 Grado');
INSERT INTO `grados` VALUES('6','5');
INSERT INTO `grados` VALUES('8','6');
INSERT INTO `grados` VALUES('11','7');

-- Estructura para tabla horarios
DROP TABLE IF EXISTS `horarios`;
CREATE TABLE `horarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seccion_anio_grado_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `dia_semana` enum('Mon','Tue','Wed','Thu','Fri','Sat','Sun') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `seccion_anio_grado_id` (`seccion_anio_grado_id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`seccion_anio_grado_id`) REFERENCES `secciones_anio_grado` (`id`),
  CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura para tabla materias
DROP TABLE IF EXISTS `materias`;
CREATE TABLE `materias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('cursando','finalizada') NOT NULL,
  `periodo` varchar(50) NOT NULL,
  `creditos` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla materias
INSERT INTO `materias` VALUES('1','Ciencias Naturales','cursando','2025-2026','');
INSERT INTO `materias` VALUES('2','Lengua y Literatura','cursando','2025-2026','');
INSERT INTO `materias` VALUES('3','Matemáticas','cursando','2025-2026','');
INSERT INTO `materias` VALUES('4','Ciencias Naturales','cursando','2025-2026','');
INSERT INTO `materias` VALUES('5','Física','cursando','2025-2026','');
INSERT INTO `materias` VALUES('6','Química','cursando','2025-2026','');
INSERT INTO `materias` VALUES('7','Biología','cursando','2025-2026','');
INSERT INTO `materias` VALUES('8','Historia de Venezuela','cursando','2025-2026','');
INSERT INTO `materias` VALUES('9','Historia Universal','cursando','2025-2026','');
INSERT INTO `materias` VALUES('10','Geografía General','cursando','2025-2026','');
INSERT INTO `materias` VALUES('11','Geografía de Venezuela','cursando','2025-2026','');
INSERT INTO `materias` VALUES('12','Educación Física','cursando','2025-2026','');
INSERT INTO `materias` VALUES('13','Formación Ciudadana','cursando','2025-2026','');
INSERT INTO `materias` VALUES('14','Orientación y Convivencia','cursando','2025-2026','');
INSERT INTO `materias` VALUES('15','Inglés','cursando','2025-2026','');
INSERT INTO `materias` VALUES('16','Arte y Patrimonio','cursando','2025-2026','');
INSERT INTO `materias` VALUES('17','Informática','cursando','2025-2026','');
INSERT INTO `materias` VALUES('18','Filosofía','cursando','2025-2026','');
INSERT INTO `materias` VALUES('19','Economía Social','cursando','2025-2026','');
INSERT INTO `materias` VALUES('20','Participación Protagónica','cursando','2025-2026','');
INSERT INTO `materias` VALUES('21','Proyecto Socio-Productivo','cursando','2025-2026','');
INSERT INTO `materias` VALUES('22','Lengua y Literatura','cursando','2025-2026','');
INSERT INTO `materias` VALUES('23','Matemáticas','cursando','2025-2026','');
INSERT INTO `materias` VALUES('24','Ciencias Naturales','cursando','2025-2026','');
INSERT INTO `materias` VALUES('25','Física','cursando','2025-2026','');
INSERT INTO `materias` VALUES('26','Química','cursando','2025-2026','');
INSERT INTO `materias` VALUES('27','Biología','cursando','2025-2026','');
INSERT INTO `materias` VALUES('28','Historia de Venezuela','cursando','2025-2026','');
INSERT INTO `materias` VALUES('29','Historia Universal','cursando','2025-2026','');
INSERT INTO `materias` VALUES('30','Geografía General','cursando','2025-2026','');
INSERT INTO `materias` VALUES('31','Geografía de Venezuela','cursando','2025-2026','');
INSERT INTO `materias` VALUES('32','Educación Física','cursando','2025-2026','');
INSERT INTO `materias` VALUES('33','Formación Ciudadana','cursando','2025-2026','');
INSERT INTO `materias` VALUES('34','Orientación y Convivencia','cursando','2025-2026','');
INSERT INTO `materias` VALUES('35','Inglés','cursando','2025-2026','');
INSERT INTO `materias` VALUES('36','Arte y Patrimonio','cursando','2025-2026','');
INSERT INTO `materias` VALUES('37','Informática','cursando','2025-2026','');
INSERT INTO `materias` VALUES('38','Filosofía','cursando','2025-2026','');
INSERT INTO `materias` VALUES('39','Economía Social','cursando','2025-2026','');
INSERT INTO `materias` VALUES('40','Participación Protagónica','cursando','2025-2026','');
INSERT INTO `materias` VALUES('41','Proyecto Socio-Productivo','cursando','2025-2026','');
INSERT INTO `materias` VALUES('42','Materia','','anual','');
INSERT INTO `materias` VALUES('43','ingles 2','cursando','','');

-- Estructura para tabla materias_actuales
DROP TABLE IF EXISTS `materias_actuales`;
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

-- Estructura para tabla materias_anio_escolar
DROP TABLE IF EXISTS `materias_anio_escolar`;
CREATE TABLE `materias_anio_escolar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anio_escolar_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `anio_escolar_id` (`anio_escolar_id`),
  KEY `materia_id` (`materia_id`),
  CONSTRAINT `materias_anio_escolar_ibfk_1` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`),
  CONSTRAINT `materias_anio_escolar_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla materias_anio_escolar
INSERT INTO `materias_anio_escolar` VALUES('2','1','1');
INSERT INTO `materias_anio_escolar` VALUES('3','1','1');
INSERT INTO `materias_anio_escolar` VALUES('4','1','2');
INSERT INTO `materias_anio_escolar` VALUES('5','1','3');
INSERT INTO `materias_anio_escolar` VALUES('6','1','4');
INSERT INTO `materias_anio_escolar` VALUES('7','1','5');
INSERT INTO `materias_anio_escolar` VALUES('8','1','6');
INSERT INTO `materias_anio_escolar` VALUES('9','1','7');
INSERT INTO `materias_anio_escolar` VALUES('10','1','8');
INSERT INTO `materias_anio_escolar` VALUES('11','1','9');
INSERT INTO `materias_anio_escolar` VALUES('12','1','10');
INSERT INTO `materias_anio_escolar` VALUES('13','1','11');
INSERT INTO `materias_anio_escolar` VALUES('14','1','12');
INSERT INTO `materias_anio_escolar` VALUES('15','1','13');
INSERT INTO `materias_anio_escolar` VALUES('16','1','14');
INSERT INTO `materias_anio_escolar` VALUES('17','1','15');
INSERT INTO `materias_anio_escolar` VALUES('18','1','16');
INSERT INTO `materias_anio_escolar` VALUES('19','1','17');
INSERT INTO `materias_anio_escolar` VALUES('20','1','18');
INSERT INTO `materias_anio_escolar` VALUES('21','1','19');
INSERT INTO `materias_anio_escolar` VALUES('22','1','20');
INSERT INTO `materias_anio_escolar` VALUES('23','1','21');
INSERT INTO `materias_anio_escolar` VALUES('24','1','22');
INSERT INTO `materias_anio_escolar` VALUES('25','1','23');
INSERT INTO `materias_anio_escolar` VALUES('26','1','24');
INSERT INTO `materias_anio_escolar` VALUES('27','1','25');
INSERT INTO `materias_anio_escolar` VALUES('28','1','26');
INSERT INTO `materias_anio_escolar` VALUES('29','1','27');
INSERT INTO `materias_anio_escolar` VALUES('30','1','28');
INSERT INTO `materias_anio_escolar` VALUES('31','1','29');
INSERT INTO `materias_anio_escolar` VALUES('32','1','30');
INSERT INTO `materias_anio_escolar` VALUES('33','1','31');
INSERT INTO `materias_anio_escolar` VALUES('34','1','32');
INSERT INTO `materias_anio_escolar` VALUES('35','1','33');
INSERT INTO `materias_anio_escolar` VALUES('36','1','34');
INSERT INTO `materias_anio_escolar` VALUES('37','1','35');
INSERT INTO `materias_anio_escolar` VALUES('38','1','36');
INSERT INTO `materias_anio_escolar` VALUES('39','1','37');
INSERT INTO `materias_anio_escolar` VALUES('40','1','38');
INSERT INTO `materias_anio_escolar` VALUES('41','1','39');
INSERT INTO `materias_anio_escolar` VALUES('42','1','40');
INSERT INTO `materias_anio_escolar` VALUES('43','1','41');
INSERT INTO `materias_anio_escolar` VALUES('66','1','43');
INSERT INTO `materias_anio_escolar` VALUES('67','1','42');

-- Estructura para tabla materias_dictadas
DROP TABLE IF EXISTS `materias_dictadas`;
CREATE TABLE `materias_dictadas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `materia_id` int(11) DEFAULT NULL,
  `docente_id` int(11) DEFAULT NULL,
  `anio_escolar_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materia_id` (`materia_id`),
  KEY `docente_id` (`docente_id`),
  KEY `anio_escolar_id` (`anio_escolar_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura para tabla materias_grados
DROP TABLE IF EXISTS `materias_grados`;
CREATE TABLE `materias_grados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `materia_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `materia_id` (`materia_id`,`grado_id`),
  KEY `grado_id` (`grado_id`),
  CONSTRAINT `materias_grados_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`),
  CONSTRAINT `materias_grados_ibfk_2` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla materias_grados
INSERT INTO `materias_grados` VALUES('22','36','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('23','1','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('24','19','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('25','12','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('26','31','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('27','30','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('28','35','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('29','22','1','2025-04-06 22:53:53');
INSERT INTO `materias_grados` VALUES('34','36','3','2025-04-08 15:20:07');
INSERT INTO `materias_grados` VALUES('35','24','3','2025-04-08 15:20:07');
INSERT INTO `materias_grados` VALUES('36','1','3','2025-04-08 15:20:07');
INSERT INTO `materias_grados` VALUES('43','16','4','2025-04-21 21:15:33');
INSERT INTO `materias_grados` VALUES('44','36','4','2025-04-21 21:15:33');
INSERT INTO `materias_grados` VALUES('45','27','4','2025-04-21 21:15:33');
INSERT INTO `materias_grados` VALUES('46','24','4','2025-04-21 21:15:33');
INSERT INTO `materias_grados` VALUES('47','16','6','2025-04-21 21:23:32');
INSERT INTO `materias_grados` VALUES('48','27','6','2025-04-21 21:23:33');
INSERT INTO `materias_grados` VALUES('49','19','6','2025-04-21 21:23:33');
INSERT INTO `materias_grados` VALUES('50','25','6','2025-04-21 21:23:33');
INSERT INTO `materias_grados` VALUES('51','33','6','2025-04-21 21:23:33');
INSERT INTO `materias_grados` VALUES('52','8','6','2025-04-21 21:23:33');
INSERT INTO `materias_grados` VALUES('53','35','6','2025-04-21 21:23:33');
INSERT INTO `materias_grados` VALUES('54','35','8','2025-05-01 15:03:09');
INSERT INTO `materias_grados` VALUES('67','36','11','2025-05-17 21:37:51');
INSERT INTO `materias_grados` VALUES('68','16','11','2025-05-17 21:37:51');
INSERT INTO `materias_grados` VALUES('69','27','11','2025-05-17 21:37:51');
INSERT INTO `materias_grados` VALUES('70','24','11','2025-05-17 21:37:51');
INSERT INTO `materias_grados` VALUES('71','19','11','2025-05-17 21:37:51');
INSERT INTO `materias_grados` VALUES('72','38','11','2025-05-17 21:37:51');
INSERT INTO `materias_grados` VALUES('73','43','2','2025-05-18 13:10:29');
INSERT INTO `materias_grados` VALUES('74','42','2','2025-05-18 13:10:29');
INSERT INTO `materias_grados` VALUES('75','14','2','2025-05-18 13:10:29');
INSERT INTO `materias_grados` VALUES('76','34','2','2025-05-18 13:10:29');

-- Estructura para tabla mensajes
DROP TABLE IF EXISTS `mensajes`;
CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remitente` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Estructura para tabla notificaciones
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla notificaciones
INSERT INTO `notificaciones` VALUES('54','inscripcion_estudiante','Nuevo estudiante inscrito con ID: 61','2025-05-11 21:26:52');

-- Estructura para tabla representantes
DROP TABLE IF EXISTS `representantes`;
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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla representantes
INSERT INTO `representantes` VALUES('2','V-10572100','Mirla Coromoto','Baena Loreto','1966-06-15','58','Venezolana','Obrera','04168851697','Madre','La sabanita','Conscripto','Calle yopal entre chacaito y nicaragua #07','');
INSERT INTO `representantes` VALUES('30','V-1274756553','Gabriela','Diaz mendoza','1980-11-11','44','Venezolana','Profesora','0416238365455','Madre','saswasawsaw','swswasa','swdesaasw','');
INSERT INTO `representantes` VALUES('31','V-12345678','María Elena','González Pérez','1985-03-15','39','Venezolana','Docente','04141234567','Madre','La Sabanita','Centro','Calle Bolívar #12','maria.gonzalez@email.com');
INSERT INTO `representantes` VALUES('32','V-23456789','Juan Carlos','Martínez Rojas','1980-07-22','44','Venezolana','Ingeniero','04161234567','Padre','Catedral','Los Olivos','Av. Sucre #45','juan.martinez@email.com');
INSERT INTO `representantes` VALUES('33','V-34567890','Luisa Fernanda','Rodríguez Sánchez','1982-11-30','42','Venezolana','Enfermera','04241234567','Madre','Vista al Sol','Brisas del Sur','Calle Miranda #23','luisa.rodriguez@email.com');
INSERT INTO `representantes` VALUES('34','V-45678901','Roberto Antonio','Hernández García','1978-04-18','47','Venezolana','Comerciante','04161234568','Padre','Unare','Pozo Verde','Av. República #56','roberto.hernandez@email.com');
INSERT INTO `representantes` VALUES('37','V-34567890','Luisa Fernanda','Rodríguez Sánchez','1982-11-30','42','Venezolana','Enfermera','04241234567','Madre','Vista al Sol','Brisas del Sur','Calle Miranda #23','luisa.rodriguez@email.com');
INSERT INTO `representantes` VALUES('38','V-45678901','Roberto Antonio','Hernández García','1978-04-18','47','Venezolana','Comerciante','04161234568','Padre','Unare','Pozo Verde','Av. República #56','roberto.hernandez@email.com');
INSERT INTO `representantes` VALUES('39','V-56789012','Carmen Teresa','Díaz Mendoza','1983-09-25','41','Venezolana','Abogada','04261234567','Madre','Cachamay','Los Mangos','Calle Piar #78','carmen.diaz@email.com');
INSERT INTO `representantes` VALUES('40','V-67890123','José Gregorio','Pérez López','1975-12-05','49','Venezolana','Mecánico','04141234568','Padre','Simón Bolívar','La Llovizna','Av. Bolívar #34','jose.perez@email.com');
INSERT INTO `representantes` VALUES('41','V-78901234','Ana Isabel','García Fernández','1981-06-30','43','Venezolana','Contadora','04241234568','Madre','Vista al Sol','Los Próceres','Calle Sucre #67','ana.garcia@email.com');
INSERT INTO `representantes` VALUES('42','V-89012345','Carlos Eduardo','López Ramírez','1979-08-15','45','Venezolana','Arquitecto','04161234569','Padre','Catedral','Centro','Av. Jesús Soto #89','carlos.lopez@email.com');
INSERT INTO `representantes` VALUES('43','V-90123456','Marta Lucía','Sánchez Méndez','1984-02-28','41','Venezolana','Psicóloga','04261234568','Madre','Unare','Pozo Negro','Calle Bolívar #12','marta.sanchez@email.com');
INSERT INTO `representantes` VALUES('44','V-01234567','Ricardo José','Fernández Castro','1977-10-10','47','Venezolana','Ingeniero','04141234569','Padre','Cachamay','La Llovizna','Av. Andrés Bello #45','ricardo.fernandez@email.com');
INSERT INTO `representantes` VALUES('45','V-127475655323','Gabriela','Diaz mendoza','1990-11-11','34','Venezolana','Profesora','04162323234424','Madre','swswasaws','swswasa','Quinta avenida','');
INSERT INTO `representantes` VALUES('46','V-87723234','Grabiel ','Diaz','1982-07-02','42','Venezolano','Profesor','04162323434223','padre','importante','importante','importante','');

-- Estructura para tabla secciones_anio_grado
DROP TABLE IF EXISTS `secciones_anio_grado`;
CREATE TABLE `secciones_anio_grado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `anio_grado_id` int(11) NOT NULL,
  `seccion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `anio_grado_id` (`anio_grado_id`),
  CONSTRAINT `secciones_anio_grado_ibfk_1` FOREIGN KEY (`anio_grado_id`) REFERENCES `anios_grados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla secciones_anio_grado
INSERT INTO `secciones_anio_grado` VALUES('1','1','A');
INSERT INTO `secciones_anio_grado` VALUES('2','1','B');
INSERT INTO `secciones_anio_grado` VALUES('3','1','C');
INSERT INTO `secciones_anio_grado` VALUES('4','2','A');
INSERT INTO `secciones_anio_grado` VALUES('5','2','B');
INSERT INTO `secciones_anio_grado` VALUES('6','2','C');
INSERT INTO `secciones_anio_grado` VALUES('7','3','A');
INSERT INTO `secciones_anio_grado` VALUES('8','3','B');
INSERT INTO `secciones_anio_grado` VALUES('9','3','C');
INSERT INTO `secciones_anio_grado` VALUES('10','4','A');
INSERT INTO `secciones_anio_grado` VALUES('11','4','B');
INSERT INTO `secciones_anio_grado` VALUES('12','4','C');
INSERT INTO `secciones_anio_grado` VALUES('13','5','A');
INSERT INTO `secciones_anio_grado` VALUES('14','5','B');
INSERT INTO `secciones_anio_grado` VALUES('15','5','C');
INSERT INTO `secciones_anio_grado` VALUES('16','6','A');
INSERT INTO `secciones_anio_grado` VALUES('17','6','B');
INSERT INTO `secciones_anio_grado` VALUES('18','6','C');
INSERT INTO `secciones_anio_grado` VALUES('32','11','B');

-- Estructura para tabla usuarios
DROP TABLE IF EXISTS `usuarios`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla usuarios
INSERT INTO `usuarios` VALUES('1','jorge','$2y$10$viq9a.uWofo/5ra1.7lBI.Eh/B7STZo8Dm0Q3Z5M/hI5bAShEbRGe','admin','jorluismj04@gmail.com','2025-04-06 15:26:07','¿Cuál es el nombre de tu primera mascota?','doki','¿Cuál es tu color favorito?','azul');
INSERT INTO `usuarios` VALUES('2','usuario1','$2y$10$srl.a2.Uxn8gBe9sfRebcelBnEGwfrqyrSW.krHurZqnOcubgsf.O','user','3030@gmail.com','2025-04-10 14:47:18','¿Cuál es el nombre de tu primera mascota?','doki','¿Cuál es tu color favorito?','azul');

SET FOREIGN_KEY_CHECKS=1;
