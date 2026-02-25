-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-02-2026 a las 14:48:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sace2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anios_escolares`
--

CREATE TABLE `anios_escolares` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anios_escolares`
--

INSERT INTO `anios_escolares` (`id`, `nombre`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
(1, '2025-2026', '2025-10-27', '2026-06-26', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anios_grados`
--

CREATE TABLE `anios_grados` (
  `id` int(11) NOT NULL,
  `anio_escolar_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anios_grados`
--

INSERT INTO `anios_grados` (`id`, `anio_escolar_id`, `grado_id`, `activo`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 0),
(6, 1, 6, 1),
(11, 1, 11, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `materia_anio_escolar_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `dia_semana` varchar(20) NOT NULL,
  `asistio` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `estudiante_id`, `materia_anio_escolar_id`, `fecha`, `dia_semana`, `asistio`) VALUES
(1, 45, 38, '2025-05-01', '', 1),
(2, 45, 38, '2025-05-02', '', 1),
(3, 45, 38, '2025-05-05', '', 1),
(4, 2, 38, '2025-05-01', '', 1),
(5, 2, 38, '2025-05-02', '', 1),
(6, 2, 21, '2025-05-01', '', 1),
(7, 2, 21, '2025-05-02', '', 1),
(8, 2, 38, '2025-05-05', '', 1),
(9, 2, 38, '2025-05-06', '', 1),
(10, 2, 9, '2025-05-01', '', 1),
(11, 2, 30, '2025-05-01', '', 1),
(12, 2, 30, '2025-05-02', '', 1),
(13, 2, 18, '2025-05-01', '', 1),
(14, 2, 18, '2025-05-02', '', 1),
(15, 2, 18, '2025-05-05', '', 1),
(16, 2, 18, '2026-05-01', '', 1),
(17, 2, 18, '2024-05-01', '', 1),
(18, 2, 18, '2024-05-02', '', 1),
(19, 2, 18, '2025-05-06', '', 1),
(20, 2, 18, '2025-05-07', '', 1),
(21, 2, 18, '2025-05-08', '', 1),
(22, 45, 38, '2025-05-06', '', 1),
(23, 45, 24, '2025-05-01', '', 1),
(24, 45, 24, '2025-05-02', '', 1),
(25, 2, 18, '2025-05-09', '', 1),
(26, 45, 38, '2025-05-07', '', 1),
(27, 45, 24, '2025-05-05', '', 1),
(28, 45, 38, '2026-05-01', '', 1),
(29, 45, 24, '2026-05-04', '', 1),
(30, 45, 24, '2026-12-01', '', 1),
(31, 45, 24, '2026-12-02', '', 1),
(32, 45, 24, '2026-12-03', '', 1),
(33, 45, 38, '2026-12-01', '', 1),
(34, 2, 18, '2025-05-12', '', 1),
(35, 2, 11, '2025-05-01', '', 1),
(36, 2, 11, '2025-05-02', '', 1),
(37, 2, 2, '2025-05-01', '', 1),
(38, 2, 2, '2025-05-02', '', 1),
(39, 45, 38, '2025-05-08', '', 1),
(40, 45, 38, '2026-05-04', '', 1),
(41, 45, 24, '2026-05-05', '', 1),
(42, 45, 24, '2026-05-06', '', 1),
(43, 45, 21, '2025-05-01', '', 1),
(44, 45, 21, '2025-05-02', '', 1),
(45, 45, 38, '2026-05-05', '', 1),
(46, 45, 33, '2026-05-07', '', 1),
(47, 46, 2, '2025-05-01', '', 1),
(48, 46, 2, '2025-05-02', '', 1),
(49, 50, 2, '2025-05-01', '', 1),
(50, 50, 2, '2025-05-02', '', 1),
(51, 50, 2, '2025-05-05', '', 1),
(52, 50, 2, '2025-05-06', '', 1),
(53, 45, 2, '2025-05-01', '', 1),
(54, 45, 2, '2025-05-02', '', 1),
(55, 45, 2, '2025-05-05', '', 1),
(56, 45, 2, '2025-05-06', '', 1),
(57, 48, 18, '2025-05-01', '', 1),
(58, 45, 21, '2025-05-05', '', 1),
(59, 46, 33, '2025-05-01', '', 1),
(60, 46, 33, '2025-05-02', '', 1),
(61, 61, 38, '2025-05-01', '', 1),
(62, 61, 38, '2025-05-02', '', 1),
(63, 53, 18, '2025-05-12', '', 1),
(64, 53, 18, '2025-05-13', '', 1),
(65, 2, 18, '2025-05-13', '', 1),
(66, 2, 2, '2025-05-12', '', 1),
(67, 53, 2, '2025-05-12', '', 1),
(68, 53, 41, '2025-05-12', '', 1),
(69, 2, 41, '2025-05-12', '', 1),
(70, 53, 7, '2025-05-12', '', 1),
(71, 2, 7, '2025-05-12', '', 1),
(72, 61, 24, '2025-05-01', '', 1),
(73, 61, 21, '2025-05-01', '', 1),
(74, 53, 18, '2025-05-01', '', 1),
(75, 61, 2, '2025-05-05', '', 1),
(76, 61, 38, '2025-05-05', '', 1),
(77, 61, 38, '2025-05-06', '', 1),
(78, 61, 38, '2025-05-07', '', 1),
(79, 46, 38, '2025-05-01', '', 1),
(80, 46, 38, '2025-05-02', '', 1),
(81, 46, 38, '2025-05-05', '', 1),
(82, 61, 2, '2025-05-06', '', 1),
(83, 53, 66, '2025-05-01', 'Thu', 1),
(84, 2, 67, '2025-05-01', 'Thu', 1),
(85, 2, 67, '2025-05-02', 'Fri', 1),
(86, 59, 38, '2025-05-01', 'Thu', 1),
(87, 61, 37, '2025-05-01', 'Thu', 1),
(88, 46, 38, '2025-05-06', 'Tue', 1),
(89, 46, 38, '2025-05-08', 'Thu', 0),
(90, 46, 38, '2025-05-07', 'Wed', 1),
(91, 61, 38, '2025-05-08', 'Thu', 1),
(92, 60, 38, '2025-05-01', 'Thu', 1),
(93, 53, 16, '2025-05-01', 'Thu', 1),
(94, 53, 8, '2025-05-01', 'Thu', 1),
(95, 53, 8, '2025-05-02', 'Fri', 1),
(96, 53, 8, '2025-05-06', 'Tue', 1),
(97, 53, 8, '2025-05-05', 'Mon', 1),
(98, 2, 8, '2025-05-01', 'Thu', 1),
(99, 61, 38, '2025-05-09', 'Fri', 0),
(100, 2, 8, '2025-05-02', 'Fri', 1),
(101, 61, 2, '2025-05-01', 'Thu', 1),
(102, 60, 38, '2025-05-02', 'Fri', 1),
(103, 2, 66, '2025-05-20', '', 1),
(104, 59, 38, '2025-05-02', 'Fri', 1),
(105, 59, 2, '2025-05-01', 'Thu', 1),
(106, 56, 2, '2025-05-01', 'Thu', 1),
(107, 61, 36, '2025-05-01', 'Thu', 1),
(108, 63, 2, '2025-05-01', 'Thu', 1),
(109, 63, 2, '2025-05-02', 'Fri', 1),
(110, 63, 38, '2025-05-01', 'Thu', 1),
(111, 66, 38, '2025-05-01', 'Thu', 1),
(112, 63, 38, '2025-05-02', 'Fri', 0),
(113, 53, 38, '2025-05-01', 'Thu', 1),
(114, 46, 17, '2025-05-01', 'Thu', 1),
(115, 46, 16, '2025-05-01', 'Thu', 0),
(116, 68, 38, '2025-05-02', 'Fri', 0),
(117, 68, 38, '2025-05-01', 'Thu', 1),
(118, 46, 38, '2025-06-02', 'Mon', 1),
(119, 46, 17, '2025-05-02', 'Fri', 1),
(120, 46, 17, '2025-05-05', 'Mon', 1),
(121, 46, 17, '2025-05-06', 'Tue', 1),
(122, 46, 17, '2025-05-07', 'Wed', 1),
(123, 46, 17, '2025-05-08', 'Thu', 1),
(124, 46, 17, '2025-05-09', 'Fri', 1),
(125, 46, 38, '2025-06-03', 'Tue', 1),
(126, 46, 38, '2025-06-04', 'Wed', 1),
(127, 46, 38, '2025-06-05', 'Thu', 1),
(128, 46, 38, '2025-06-06', 'Fri', 1),
(129, 46, 17, '2025-06-02', 'Mon', 1),
(130, 46, 27, '2025-06-03', 'Tue', 1),
(131, 46, 27, '2025-06-02', 'Mon', 1),
(132, 53, 38, '2025-06-02', 'Mon', 1),
(133, 53, 38, '2025-06-03', 'Tue', 1),
(134, 53, 38, '2025-06-04', 'Wed', 1),
(135, 80, 38, '2025-06-02', 'Mon', 1),
(136, 46, 8, '2025-06-02', 'Mon', 1),
(137, 53, 38, '2025-06-05', 'Thu', 1),
(138, 46, 38, '2025-12-29', 'Mon', 0),
(139, 46, 38, '2025-12-11', 'Thu', 0),
(140, 46, 38, '2025-12-01', 'Mon', 0),
(141, 46, 38, '2025-12-15', 'Mon', 0),
(142, 68, 38, '2026-02-25', 'Wed', 1),
(143, 68, 38, '2026-02-27', 'Fri', 1),
(144, 68, 38, '2026-02-06', 'Fri', 1),
(145, 68, 38, '2026-02-09', 'Mon', 1),
(146, 68, 38, '2026-02-11', 'Wed', 1),
(147, 68, 38, '2026-02-16', 'Mon', 0),
(148, 68, 38, '2026-02-18', 'Wed', 0),
(149, 68, 38, '2026-02-17', 'Tue', 1),
(150, 68, 38, '2026-02-04', 'Wed', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_academicos`
--

CREATE TABLE `datos_academicos` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `escolaridad` enum('regular','pendiente','repitiente','doble_inscripcion','repite_pendiente','equivalencia') NOT NULL,
  `anio_grado_id` int(11) NOT NULL,
  `seccion` varchar(50) NOT NULL,
  `anio_escolar_id` int(11) NOT NULL,
  `promedio` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `datos_academicos`
--

INSERT INTO `datos_academicos` (`id`, `estudiante_id`, `fecha_ingreso`, `escolaridad`, `anio_grado_id`, `seccion`, `anio_escolar_id`, `promedio`) VALUES
(20, 45, '2025-05-20', 'regular', 2, 'A', 1, 0.00),
(21, 46, '2025-05-20', 'regular', 2, 'A', 1, 0.00),
(22, 47, '2025-05-20', 'regular', 2, 'a', 1, 0.00),
(23, 48, '2025-05-10', 'regular', 4, 'A', 1, 0.00),
(27, 52, '2025-05-20', 'regular', 2, 'a', 1, 0.00),
(28, 53, '2025-05-10', 'regular', 2, 'A', 1, 0.00),
(29, 54, '2025-05-10', 'regular', 2, 'B', 1, 0.00),
(30, 55, '2025-05-10', 'regular', 2, 'C', 1, 0.00),
(31, 56, '2025-05-10', 'regular', 3, 'A', 1, 0.00),
(32, 57, '2025-05-20', 'regular', 4, 'a', 1, 0.00),
(33, 58, '2025-05-10', 'regular', 3, 'C', 1, 0.00),
(34, 59, '2025-05-20', 'repitiente', 3, 'A', 1, 0.00),
(35, 60, '2025-05-20', 'regular', 2, 'A', 1, 0.00),
(37, 62, '2025-05-20', 'regular', 2, 'A', 1, NULL),
(43, 68, '2025-05-31', 'regular', 1, 'A', 1, NULL),
(44, 69, '2025-06-06', 'regular', 4, 'A', 1, NULL),
(45, 80, '2025-06-18', 'regular', 1, 'A', 1, NULL),
(48, 83, '2026-01-03', 'regular', 1, 'A', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `id` int(11) NOT NULL,
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
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`id`, `representante_id`, `cedula`, `nombres`, `apellidos`, `sexo`, `fecha_nacimiento`, `edad`, `telefono`, `observaciones`, `estado`, `municipio`, `localidad`, `plantel_procedencia`, `foto`) VALUES
(45, 30, 'V-67890123', 'Jorge antonio', 'Torres Pérez', 'masculino', '2009-11-11', 15, '041604281234572', 'Finoooo', 'Bolívar', 'Heres', 'Las Minas', 'Jardín de Infancia Alegríaa', NULL),
(46, 31, 'V-********', 'Carlos José', 'González López', 'masculino', '2018-05-10', 7, '0416', 'Alergia a polvo', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'Jardín de Infancia Alegría', NULL),
(47, 32, 'V-98765432', 'Ana Sofía', 'Martínez García', 'femenino', '2015-02-15', 7, '04163456789', 'Ninguna', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'Jardín de Infancia Arcoíris', NULL),
(48, 33, 'V-09876543', 'Pedro Luis', 'Rodríguez Pérez', 'masculino', '2017-08-20', 8, '04242345678', 'Usa lentes', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. Carlos Emiliano Salom', NULL),
(49, 34, 'V-10987654', 'Valentina María', 'Hernández López', 'femenino', '2016-10-05', 9, '04164567890', 'Ninguna', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. República del Perú', NULL),
(52, 37, 'V-09876543', 'Pedro Luis', 'Rodríguez Pérez', 'masculino', '2018-08-20', 7, '04242345678', 'Usa lentes', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'Jardín de Infancia Los Pinos', NULL),
(53, 38, 'V-10987654', 'Valentina María', 'Hernández López', 'femenino', '2017-10-05', 8, '04164567890', 'Ninguna', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. Carlos Emiliano Salom', NULL),
(54, 39, 'V-21098765', 'Diego Alejandro', 'Díaz Rojas', 'masculino', '2017-03-12', 8, '04263456789', 'Asma leve', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. Carlos Emiliano Salom', NULL),
(55, 40, 'V-32109876', 'María Gabriela', 'Pérez Martínez', 'femenino', '2017-07-18', 8, '04145678901', 'Ninguna', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. República del Perú', NULL),
(56, 41, 'V-43210987', 'Luis Miguel', 'García Díaz', 'masculino', '2016-01-22', 9, '04244567890', 'Ninguna', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. Manuel Piar', NULL),
(57, 42, 'V-54321098', 'Sofía Camila', 'López García', 'femenino', '2016-11-08', 9, '04166789012', 'Ninguna', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. Andrés Bello', NULL),
(58, 43, 'V-65432109', 'Javier Antonio', 'Sánchez Rodríguez', 'masculino', '2016-04-17', 9, '04267890123', 'Alergia a mariscos', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. República de Chile', NULL),
(59, 44, 'V-76543210', 'Andrea Carolina', 'Fernández López', 'femenino', '2016-09-03', 9, '04148901234', 'Ninguna', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'U.E. República de Argentina', NULL),
(60, 45, 'V-1236742323', 'Antonia', 'Lopez', 'femenino', '2015-11-11', 7, '0416122323244', 'Fino ', 'Bolívar', 'Heres', 'Las Minas', 'Jardín de Infancia Alegría', NULL),
(62, 47, 'V-12343435', 'Antoniaa', 'Torres Pérez', 'masculino', '2016-11-11', 8, '0416245646634', 'Fino ', 'Distrito Capital', 'Distrito capital', 'Caracas', 'Colegio Los Arcos', NULL),
(68, 48, 'V-23023034', 'Antonia Rivera', 'Martinez Perez', 'femenino', '2015-11-11', 9, '0412', 'Fino ', 'Bolívar', 'Heres', 'Ciudad Bolívar', 'Jardín de Infancia Alegría', NULL),
(69, 49, 'V-30367235', 'Jorge Luis', 'Ortega Baena', 'masculino', '2004-02-05', 21, '04249065920', 'Fino ', 'Bolívar', 'Angostura del Orinoco (Heres)', 'Ciudad Bolívar', NULL, 'alumno_68439cb9bccd4.jpg'),
(70, 50, 'V-11223344', 'Martina Andrea', 'Gómez Ruíz', 'femenino', '2019-01-10', 6, '04123456789', 'Ninguna', 'Bolívar', 'Heres', 'El Bosque', 'Escuela Básica Nacional', NULL),
(71, 51, 'V-55667788', 'Alejandro David', 'Ruiz Castro', 'masculino', '2018-06-15', 7, '04149876543', 'Alergia al polen', 'Bolívar', 'Heres', 'Las Palmas', 'Colegio Simón Rodríguez', NULL),
(72, 52, 'V-99001122', 'Isabella Victoria', 'Parra Morales', 'femenino', '2020-02-28', 5, '04267890123', 'Ninguna', 'Bolívar', 'Heres', 'Altamira', 'Preescolar Mi Pequeño Mundo', NULL),
(73, 53, 'V-33445566', 'Santiago Gabriel', 'Vargas Quintero', 'masculino', '2017-09-03', 8, '04160987654', 'Asma', 'Bolívar', 'Heres', 'Los Chaguaramos', 'U.E. Libertador', NULL),
(74, 54, 'V-77889900', 'Valeria Sofía', 'Peña Soto', 'femenino', '2019-04-22', 6, '04245678901', 'Ninguna', 'Bolívar', 'Heres', 'Valle Abajo', 'Escuela Bolivariana', NULL),
(75, 50, 'V-11223344', 'Martina Andrea', 'Gómez Ruíz', 'femenino', '2019-01-10', 6, '04123456789', 'Ninguna', 'Bolívar', 'Heres', 'El Bosque', 'Escuela Básica Nacional', NULL),
(76, 51, 'V-55667788', 'Alejandro David', 'Ruiz Castro', 'masculino', '2018-06-15', 7, '04149876543', 'Alergia al polen', 'Bolívar', 'Heres', 'Las Palmas', 'Colegio Simón Rodríguez', NULL),
(77, 52, 'V-99001122', 'Isabella Victoria', 'Parra Morales', 'femenino', '2020-02-28', 5, '04267890123', 'Ninguna', 'Bolívar', 'Heres', 'Altamira', 'Preescolar Mi Pequeño Mundo', NULL),
(78, 53, 'V-33445566', 'Santiago Gabriel', 'Vargas Quintero', 'masculino', '2017-09-03', 8, '04160987654', 'Asma', 'Bolívar', 'Heres', 'Los Chaguaramos', 'U.E. Libertador', NULL),
(79, 54, 'V-77889900', 'Valeria Sofía', 'Peña Soto', 'femenino', '2019-04-22', 6, '04245678901', 'Ninguna', 'Bolívar', 'Heres', 'Valle Abajo', 'Escuela Bolivariana', NULL),
(80, 60, 'V-12323232343', 'Alfredo', 'Torres Pérez', 'masculino', '2010-11-11', 12, '04262323234', 'Fino ', 'Bolívar', 'Angostura del Orinoco (Heres)', 'Ciudad Bolívar', 'Agua Salada', NULL),
(83, 63, 'V-30748112', 'jesus rafael', 'vargas medina', 'masculino', '2018-11-30', 7, '04148626382', 'ninguna', 'Bolívar', 'Angostura del Orinoco (Heres)', 'ciudad bolivar', 'La coromoto', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

CREATE TABLE `grados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grados`
--

INSERT INTO `grados` (`id`, `nombre`) VALUES
(1, '1°Año'),
(2, '2°Año'),
(3, '3°Año'),
(4, '4°Año'),
(5, '5°Año'),
(6, '5'),
(8, '6'),
(11, '7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `seccion_anio_grado_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `dia_semana` enum('Mon','Tue','Wed','Thu','Fri','Sat','Sun') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('cursando','finalizada') NOT NULL,
  `periodo` varchar(50) NOT NULL,
  `creditos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id`, `nombre`, `estado`, `periodo`, `creditos`) VALUES
(1, 'Ciencias Naturales', 'cursando', '2025-2026', 0),
(3, 'Matemáticas', 'cursando', '2025-2026', 0),
(6, 'Química', 'cursando', '2025-2026', 0),
(8, 'Historia de Venezuela', 'cursando', '2025-2026', 0),
(12, 'Educación Física', 'cursando', '2025-2026', 0),
(14, 'Orientación y Convivencia', 'cursando', '2025-2026', 0),
(15, 'Inglés', 'cursando', '2025-2026', 0),
(16, 'Arte y Patrimonio', 'cursando', '2025-2026', 0),
(19, 'Economía Social', 'cursando', '2025-2026', 0),
(22, 'Lengua y Literatura', 'cursando', '2025-2026', 0),
(24, 'Ciencias Naturales', 'cursando', '2025-2026', 0),
(25, 'Física', 'cursando', '2025-2026', 0),
(27, 'Biología', 'cursando', '2025-2026', 0),
(30, 'Geografía General', 'cursando', '2025-2026', 0),
(31, 'Geografía de Venezuela', 'cursando', '2025-2026', 0),
(33, 'Formación Ciudadana', 'cursando', '2025-2026', 0),
(34, 'Orientación y Convivencia', 'cursando', '2025-2026', 0),
(35, 'Inglés', 'cursando', '2025-2026', 0),
(36, 'Arte y Patrimonio', 'cursando', '2025-2026', 0),
(38, 'Filosofía', 'cursando', '2025-2026', 0),
(39, 'Economía Social', 'cursando', '2025-2026', 0),
(44, 'Ciencias Naturales', 'cursando', '', NULL),
(45, 'Ciencias Naturales', 'cursando', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_actuales`
--

CREATE TABLE `materias_actuales` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `calificacion` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_anio_escolar`
--

CREATE TABLE `materias_anio_escolar` (
  `id` int(11) NOT NULL,
  `anio_escolar_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_anio_escolar`
--

INSERT INTO `materias_anio_escolar` (`id`, `anio_escolar_id`, `materia_id`) VALUES
(2, 1, 1),
(3, 1, 1),
(5, 1, 3),
(8, 1, 6),
(10, 1, 8),
(14, 1, 12),
(16, 1, 14),
(17, 1, 15),
(18, 1, 16),
(21, 1, 19),
(24, 1, 22),
(26, 1, 24),
(27, 1, 25),
(29, 1, 27),
(32, 1, 30),
(33, 1, 31),
(35, 1, 33),
(36, 1, 34),
(37, 1, 35),
(38, 1, 36),
(40, 1, 38),
(41, 1, 39);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_dictadas`
--

CREATE TABLE `materias_dictadas` (
  `id` int(11) NOT NULL,
  `materia_id` int(11) DEFAULT NULL,
  `docente_id` int(11) DEFAULT NULL,
  `anio_escolar_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias_grados`
--

CREATE TABLE `materias_grados` (
  `id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `grado_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias_grados`
--

INSERT INTO `materias_grados` (`id`, `materia_id`, `grado_id`, `created_at`) VALUES
(34, 36, 3, '2025-04-08 19:20:07'),
(35, 24, 3, '2025-04-08 19:20:07'),
(36, 1, 3, '2025-04-08 19:20:07'),
(43, 16, 4, '2025-04-22 01:15:33'),
(44, 36, 4, '2025-04-22 01:15:33'),
(45, 27, 4, '2025-04-22 01:15:33'),
(46, 24, 4, '2025-04-22 01:15:33'),
(47, 16, 6, '2025-04-22 01:23:32'),
(48, 27, 6, '2025-04-22 01:23:33'),
(49, 19, 6, '2025-04-22 01:23:33'),
(50, 25, 6, '2025-04-22 01:23:33'),
(51, 33, 6, '2025-04-22 01:23:33'),
(52, 8, 6, '2025-04-22 01:23:33'),
(53, 35, 6, '2025-04-22 01:23:33'),
(54, 35, 8, '2025-05-01 19:03:09'),
(67, 36, 11, '2025-05-18 01:37:51'),
(68, 16, 11, '2025-05-18 01:37:51'),
(69, 27, 11, '2025-05-18 01:37:51'),
(70, 24, 11, '2025-05-18 01:37:51'),
(71, 19, 11, '2025-05-18 01:37:51'),
(72, 38, 11, '2025-05-18 01:37:51'),
(101, 36, 1, '2025-05-31 19:29:15'),
(102, 1, 1, '2025-05-31 19:29:15'),
(103, 19, 1, '2025-05-31 19:29:15'),
(104, 31, 1, '2025-05-31 19:29:15'),
(105, 30, 1, '2025-05-31 19:29:15'),
(106, 15, 1, '2025-05-31 19:29:15'),
(107, 22, 1, '2025-05-31 19:29:15'),
(108, 36, 2, '2025-06-07 17:42:59'),
(109, 25, 2, '2025-06-07 17:42:59'),
(110, 15, 2, '2025-06-07 17:42:59'),
(111, 34, 2, '2025-06-07 17:42:59'),
(112, 14, 2, '2025-06-07 17:42:59'),
(113, 6, 2, '2025-06-07 17:42:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `remitente` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `tipo`, `mensaje`, `fecha`) VALUES
(96, 'inscripcion_estudiante', 'Nuevo estudiante inscrito con ID: 83', '2026-02-09 20:30:59'),
(97, 'inscripcion_estudiante', 'Nuevo estudiante inscrito con ID: 84', '2026-02-09 20:34:12'),
(98, 'eliminacion_estudiante', 'Estudiante con ID: 84 eliminado', '2026-02-09 20:35:17'),
(99, 'registro_usuario', 'Nuevo usuario registrado con ID: 4', '2026-02-10 22:49:58'),
(100, 'recuperacion_password', 'Usuario con ID: 3 recuperó su contraseña', '2026-02-10 23:09:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `estudiante_id` int(11) NOT NULL,
  `anio_escolar_id` int(11) NOT NULL,
  `mes` varchar(20) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `estudiante_id`, `anio_escolar_id`, `mes`, `monto`, `iva`, `total`, `fecha_pago`) VALUES
(1, 83, 1, 'Septiembre', 10.00, 2.00, 12.00, '2026-02-10 17:11:00'),
(2, 69, 1, 'Septiembre', 10.00, 5.00, 15.00, '2026-02-10 17:16:03'),
(3, 83, 1, 'Octubre', 10.00, 2.00, 12.00, '2026-02-10 20:49:13'),
(4, 83, 1, 'Noviembre', 10.00, 2.00, 12.00, '2026-02-10 21:30:53'),
(5, 59, 1, 'Septiembre', 10.00, 5.00, 15.00, '2026-02-10 21:43:44'),
(6, 46, 1, 'Enero', 10.00, 5.00, 15.00, '2026-02-11 01:40:29'),
(7, 58, 1, 'Septiembre', 1556.00, 128.10, 1684.10, '2026-02-11 03:16:39'),
(8, 59, 1, 'Enero', 20.00, 1.98, 21.98, '2026-02-11 22:02:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `representantes`
--

CREATE TABLE `representantes` (
  `id` int(11) NOT NULL,
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
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `representantes`
--

INSERT INTO `representantes` (`id`, `cedula`, `nombres`, `apellidos`, `fecha_nacimiento`, `edad`, `nacionalidad`, `profesion_oficio`, `telefono`, `parentesco`, `parroquia`, `sector`, `direccion`, `correo_electronico`, `foto`) VALUES
(30, 'V-1274756553', 'Gabriela', 'Diaz mendoza', '1980-11-11', 44, 'Venezolana', 'Profesora', '0416238365455', 'Madre', 'saswasawsaw', 'swswasa', 'swdesaasw', '', NULL),
(31, 'V-12345678', 'María Elena', 'González Pérez', '1985-03-15', 39, 'Venezolana', 'Docente', '0414*************', 'Madre', 'La Sabanita', 'Centro', 'Calle Bolívar #12', 'maria.gonzalez@email.com', NULL),
(32, 'V-23456789', 'Juan Carlos', 'Martínez Rojas', '1980-07-22', 44, 'Venezolana', 'Ingeniero', '04161234567', 'Padre', 'Catedral', 'Los Olivos', 'Av. Sucre #45', 'juan.martinez@email.com', NULL),
(33, 'V-34567890', 'Luisa Fernanda', 'Rodríguez Sánchez', '1982-11-30', 42, 'Venezolana', 'Enfermera', '04241234567', 'Madre', 'Vista al Sol', 'Brisas del Sur', 'Calle Miranda #23', 'luisa.rodriguez@email.com', NULL),
(34, 'V-45678901', 'Roberto Antonio', 'Hernández García', '1978-04-18', 47, 'Venezolana', 'Comerciante', '04161234568', 'Padre', 'Unare', 'Pozo Verde', 'Av. República #56', 'roberto.hernandez@email.com', NULL),
(37, 'V-34567890', 'Luisa Fernanda', 'Rodríguez Sánchez', '1982-11-30', 42, 'Venezolana', 'Enfermera', '04241234567', 'Madre', 'Vista al Sol', 'Brisas del Sur', 'Calle Miranda #23', 'luisa.rodriguez@email.com', NULL),
(38, 'V-45678901', 'Roberto Antonio', 'Hernández García', '1978-04-18', 47, 'Venezolana', 'Comerciante', '*********', 'Padre', 'Unare', 'Pozo Verde', 'Av. República #56', 'roberto.hernandez@email.com', NULL),
(39, 'V-56789012', 'Carmen Teresa', 'Díaz Mendoza', '1983-09-25', 41, 'Venezolana', 'Abogada', '04261234567', 'Madre', 'Cachamay', 'Los Mangos', 'Calle Piar #78', 'carmen.diaz@email.com', NULL),
(40, 'V-67890123', 'José Gregorio', 'Pérez López', '1975-12-05', 49, 'Venezolana', 'Mecánico', '04141234568', 'Padre', 'Simón Bolívar', 'La Llovizna', 'Av. Bolívar #34', 'jose.perez@email.com', NULL),
(41, 'V-78901234', 'Ana Isabel', 'García Fernández', '1981-06-30', 43, 'Venezolana', 'Contadora', '04241234568', 'Madre', 'Vista al Sol', 'Los Próceres', 'Calle Sucre #67', 'ana.garcia@email.com', NULL),
(42, 'V-89012345', 'Carlos Eduardo', 'López Ramírez', '1979-08-15', 45, 'Venezolana', 'Arquitecto', '04161234569', 'Padre', 'Catedral', 'Centro', 'Av. Jesús Soto #89', 'carlos.lopez@email.com', NULL),
(43, 'V-90123456', 'Marta Lucía', 'Sánchez Méndez', '1984-02-28', 41, 'Venezolana', 'Psicóloga', '04261234568', 'Madre', 'Unare', 'Pozo Negro', 'Calle Bolívar #12', 'marta.sanchez@email.com', NULL),
(44, 'V-01234567', 'Ricardo José', 'Fernández Castro', '1977-10-10', 47, 'Venezolana', 'Ingeniero', '04141234569', 'Padre', 'Cachamay', 'La Llovizna', 'Av. Andrés Bello #45', 'ricardo.fernandez@email.com', NULL),
(45, 'V-127475655323', 'Gabriela', 'Diaz mendoza', '1990-11-11', 34, 'Venezolana', 'Profesora', '04162323234424', 'Madre', 'swswasaws', 'swswasa', 'Quinta avenida', '', NULL),
(47, 'V-20345432', 'Antonio', 'Dia torrest', '1980-11-11', 44, 'Venezolano', 'Profesor', '041623234434342', 'padre', 'saswasawsaw', 'Conscripto', 'Quinta avenida', '', NULL),
(48, 'V-23234567', 'Gabriela Rivero', 'Martinez ', '1980-11-11', 44, 'Venezolana', 'Profesora', '042423345456', 'Madre', 'importante', 'importante', 'importante', '', NULL),
(49, 'V-10572100', 'Mirla Coromoto', 'Baena Loreto', '1966-06-15', 58, 'Venezolana', 'Obrera', '04168851697', 'Madre', 'La sabanita', 'Conscripto', 'Calle yopal entre chacaito y nicaragua #07', '', NULL),
(50, 'V-987654321', 'Laura Sofia', 'Gómez Castro', '1988-03-20', 37, 'Venezolana', 'Diseñadora', '04123456789', 'Madre', 'San Isidro', 'El Bosque', 'Av. Principal #10', 'laura.gomez@email.com', NULL),
(51, 'V-123450987', 'Miguel Angel', 'Ruiz Morales', '1970-11-01', 54, 'Venezolana', 'Arquitecto', '04149876543', 'Padre', 'La Pastora', 'Las Palmas', 'Calle Real #25', 'miguel.ruiz@email.com', NULL),
(52, 'V-678905432', 'Sara Isabel', 'Parra Quintero', '1992-07-10', 32, 'Venezolana', 'Médico', '04267890123', 'Madre', 'El Recreo', 'Altamira', 'Av. Libertador #30', 'sara.parra@email.com', NULL),
(53, 'V-234567890', 'Daniel José', 'Vargas Soto', '1983-01-25', 42, 'Venezolana', 'Abogado', '04160987654', 'Padre', 'Santa Rosalía', 'Los Chaguaramos', 'Calle Urdaneta #15', 'daniel.vargas@email.com', NULL),
(54, 'V-876543210', 'Camila Alejandra', 'Peña Durán', '1976-09-08', 48, 'Venezolana', 'Periodista', '04245678901', 'Madre', 'San Pedro', 'Valle Abajo', 'Carrera 5 #40', 'camila.pena@email.com', NULL),
(55, 'V-987654321', 'Laura Sofia', 'Gómez Castro', '1988-03-20', 37, 'Venezolana', 'Diseñadora', '04123456789', 'Madre', 'San Isidro', 'El Bosque', 'Av. Principal #10', 'laura.gomez@email.com', NULL),
(56, 'V-123450987', 'Miguel Angel', 'Ruiz Morales', '1970-11-01', 54, 'Venezolana', 'Arquitecto', '04149876543', 'Padre', 'La Pastora', 'Las Palmas', 'Calle Real #25', 'miguel.ruiz@email.com', NULL),
(57, 'V-678905432', 'Sara Isabel', 'Parra Quintero', '1992-07-10', 32, 'Venezolana', 'Médico', '04267890123', 'Madre', 'El Recreo', 'Altamira', 'Av. Libertador #30', 'sara.parra@email.com', NULL),
(58, 'V-234567890', 'Daniel José', 'Vargas Soto', '1983-01-25', 42, 'Venezolana', 'Abogado', '04160987654', 'Padre', 'Santa Rosalía', 'Los Chaguaramos', 'Calle Urdaneta #15', 'daniel.vargas@email.com', NULL),
(59, 'V-876543210', 'Camila Alejandra', 'Peña Durán', '1976-09-08', 48, 'Venezolana', 'Periodista', '04245678901', 'Madre', 'San Pedro', 'Valle Abajo', 'Carrera 5 #40', 'camila.pena@email.com', NULL),
(60, 'V-23232334', 'Carmen ', 'Melendez', '1980-11-11', 44, 'Venezolana', 'Profesora', '04162323232', 'Madre', 'importante', 'importante', 'importante', '', NULL),
(63, 'V-13384622', 'carmen', 'medina', '1969-10-24', 56, 'venezuela', 'del hogar', '04169430849', 'madre', 'la sabanita', 'udo', 'x', 'v1223vhe@gmail.com', NULL),
(64, 'V-13384622', 'carmen', 'medina', '1996-10-10', 29, 'venezuela', 'del hogar', '04169430849', 'madre', 'la sabanita', 'udo', 'x', 'v1223vhe@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones_anio_grado`
--

CREATE TABLE `secciones_anio_grado` (
  `id` int(11) NOT NULL,
  `anio_grado_id` int(11) NOT NULL,
  `seccion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones_anio_grado`
--

INSERT INTO `secciones_anio_grado` (`id`, `anio_grado_id`, `seccion`) VALUES
(1, 1, 'A'),
(2, 1, 'B'),
(3, 1, 'C'),
(4, 2, 'A'),
(5, 2, 'B'),
(6, 2, 'C'),
(7, 3, 'A'),
(8, 3, 'B'),
(9, 3, 'C'),
(10, 4, 'A'),
(11, 4, 'B'),
(12, 4, 'C'),
(13, 5, 'A'),
(14, 5, 'B'),
(15, 5, 'C'),
(16, 6, 'A'),
(17, 6, 'B'),
(18, 6, 'C'),
(32, 11, 'B');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `pregunta_seguridad1` varchar(255) DEFAULT NULL,
  `respuesta_seguridad1` varchar(255) DEFAULT NULL,
  `pregunta_seguridad2` varchar(255) DEFAULT NULL,
  `respuesta_seguridad2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `role`, `email`, `fecha_registro`, `pregunta_seguridad1`, `respuesta_seguridad1`, `pregunta_seguridad2`, `respuesta_seguridad2`) VALUES
(1, 'jorge', '$2y$10$viq9a.uWofo/5ra1.7lBI.Eh/B7STZo8Dm0Q3Z5M/hI5bAShEbRGe', 'admin', 'jorluismj04@gmail.com', '2025-04-06 15:26:07', '¿Cuál es el nombre de tu primera mascota?', 'doki', '¿Cuál es tu color favorito?', 'azul'),
(2, 'usuario1', '$2y$10$HGXzT04Wzb3hZd.4LmBAdOicAnnYRyrgHxxDrYWVARMvDZCTxbCGa', 'user', '3030@gmail.com', '2025-04-10 14:47:18', '¿Cuál es el nombre de tu primera mascota?', 'doki', '¿Cuál es tu color favorito?', 'azul'),
(3, 'jesus', '$2y$10$TrjF3YtMicPrh6Rhk8R4VOzxWOiUBmQImPU1QoQ.YwW2yoUA6lL1a', 'admin', 'v123@gmail.com', '2025-10-14 14:19:34', '¿Cuál es el nombre de tu primera mascota?', 'pelusa', '¿Cuál es tu color favorito?', 'azul'),
(4, 'jose', '$2y$10$STrEgPXwtZSxDCErMvm93u4zDYPO5rSfyGgeD3DMyvejMiDBzC6Yu', 'user', '1223@gmail.com', '2026-02-10 22:49:57', '¿Cuál es el nombre de tu primera mascota?', 'pelusa', '¿Cuál es tu color favorito?', 'azul');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anios_escolares`
--
ALTER TABLE `anios_escolares`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `anios_grados`
--
ALTER TABLE `anios_grados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anio_escolar_id` (`anio_escolar_id`),
  ADD KEY `grado_id` (`grado_id`);

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_asistencia` (`estudiante_id`,`materia_anio_escolar_id`,`fecha`);

--
-- Indices de la tabla `datos_academicos`
--
ALTER TABLE `datos_academicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `datos_academicos_ibfk_2` (`anio_grado_id`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grados`
--
ALTER TABLE `grados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seccion_anio_grado_id` (`seccion_anio_grado_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `materias_actuales`
--
ALTER TABLE `materias_actuales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `materias_anio_escolar`
--
ALTER TABLE `materias_anio_escolar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anio_escolar_id` (`anio_escolar_id`),
  ADD KEY `materia_id` (`materia_id`);

--
-- Indices de la tabla `materias_dictadas`
--
ALTER TABLE `materias_dictadas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `docente_id` (`docente_id`),
  ADD KEY `anio_escolar_id` (`anio_escolar_id`);

--
-- Indices de la tabla `materias_grados`
--
ALTER TABLE `materias_grados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `materia_id` (`materia_id`,`grado_id`),
  ADD KEY `grado_id` (`grado_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estudiante_id` (`estudiante_id`),
  ADD KEY `anio_escolar_id` (`anio_escolar_id`);

--
-- Indices de la tabla `representantes`
--
ALTER TABLE `representantes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `secciones_anio_grado`
--
ALTER TABLE `secciones_anio_grado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `anio_grado_id` (`anio_grado_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anios_escolares`
--
ALTER TABLE `anios_escolares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `anios_grados`
--
ALTER TABLE `anios_grados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT de la tabla `datos_academicos`
--
ALTER TABLE `datos_academicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT de la tabla `grados`
--
ALTER TABLE `grados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `materias_actuales`
--
ALTER TABLE `materias_actuales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_anio_escolar`
--
ALTER TABLE `materias_anio_escolar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT de la tabla `materias_dictadas`
--
ALTER TABLE `materias_dictadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias_grados`
--
ALTER TABLE `materias_grados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `representantes`
--
ALTER TABLE `representantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `secciones_anio_grado`
--
ALTER TABLE `secciones_anio_grado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anios_grados`
--
ALTER TABLE `anios_grados`
  ADD CONSTRAINT `anios_grados_ibfk_1` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`),
  ADD CONSTRAINT `anios_grados_ibfk_2` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`);

--
-- Filtros para la tabla `datos_academicos`
--
ALTER TABLE `datos_academicos`
  ADD CONSTRAINT `datos_academicos_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`),
  ADD CONSTRAINT `datos_academicos_ibfk_2` FOREIGN KEY (`anio_grado_id`) REFERENCES `anios_grados` (`id`);

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`seccion_anio_grado_id`) REFERENCES `secciones_anio_grado` (`id`),
  ADD CONSTRAINT `horarios_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `materias_actuales`
--
ALTER TABLE `materias_actuales`
  ADD CONSTRAINT `materias_actuales_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`),
  ADD CONSTRAINT `materias_actuales_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `materias_anio_escolar`
--
ALTER TABLE `materias_anio_escolar`
  ADD CONSTRAINT `materias_anio_escolar_ibfk_1` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`),
  ADD CONSTRAINT `materias_anio_escolar_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`);

--
-- Filtros para la tabla `materias_grados`
--
ALTER TABLE `materias_grados`
  ADD CONSTRAINT `materias_grados_ibfk_1` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id`),
  ADD CONSTRAINT `materias_grados_ibfk_2` FOREIGN KEY (`grado_id`) REFERENCES `grados` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`anio_escolar_id`) REFERENCES `anios_escolares` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `secciones_anio_grado`
--
ALTER TABLE `secciones_anio_grado`
  ADD CONSTRAINT `secciones_anio_grado_ibfk_1` FOREIGN KEY (`anio_grado_id`) REFERENCES `anios_grados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
