-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-09-2025 a las 00:59:21
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
-- Base de datos: `gestion`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equiposmedicos`
--

CREATE TABLE `equiposmedicos` (
  `ID_EM` varchar(12) NOT NULL,
  `Num_activo` varchar(20) NOT NULL,
  `Marca` varchar(20) NOT NULL,
  `Modelo` varchar(20) NOT NULL,
  `Codigo_ubi` varchar(12) DEFAULT NULL,
  `Codigo_Resp` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equiposmedicos`
--

INSERT INTO `equiposmedicos` (`ID_EM`, `Num_activo`, `Marca`, `Modelo`, `Codigo_ubi`, `Codigo_Resp`) VALUES
('1', '123', 'rgt', 'max', 'U_OFT0001', 'REN0001'),
('2', '789', 'got', 'men', 'U_PED0002', 'RPR0001');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `responsables`
--

CREATE TABLE `responsables` (
  `Codigo_Resp` varchar(12) NOT NULL,
  `ID_Resp` varchar(12) NOT NULL,
  `Cedula` varchar(11) NOT NULL,
  `Nombre` varchar(20) NOT NULL,
  `Apellido` varchar(20) NOT NULL,
  `Cargo` varchar(20) NOT NULL,
  `Telefono` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `responsables`
--

INSERT INTO `responsables` (`Codigo_Resp`, `ID_Resp`, `Cedula`, `Nombre`, `Apellido`, `Cargo`, `Telefono`) VALUES
('REN0001', '3', '1002677825', 'Kevin Stiven', 'Jaramillo', 'Enfermero', '340125937'),
('REN0002', '5', '1000789344', 'Oscar', 'restrepo', 'Enfermero', '1221232'),
('RME0001', '4', '901132412', 'Sandra', 'Milena', 'Medica', '31241352323'),
('RME0002', '2', '1000569323', 'Sebastian', 'Villa', 'Medico', '310934675'),
('RPR0001', '1', '78934389', 'Juan', 'ocampo', 'Practicante', '34312644');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

CREATE TABLE `ubicaciones` (
  `Codigo_ubi` varchar(12) NOT NULL,
  `ID_ubi` varchar(12) NOT NULL,
  `Nombre_ubi` varchar(50) NOT NULL,
  `Ubicacion` varchar(50) NOT NULL,
  `Telefono` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`Codigo_ubi`, `ID_ubi`, `Nombre_ubi`, `Ubicacion`, `Telefono`) VALUES
('U_CIR0001', '4', 'Cirugia', 'piso 1, sala 5', '4252'),
('U_OFT0001', '6', 'Oftomologia', 'ala norte ', '123131'),
('U_PED0001', '3', 'Pediatria', 'piso 1', '5673410'),
('U_PED0002', '2', 'Pediatria', 'ala norte ', '3204184');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','medico','auxiliar') DEFAULT 'medico'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `rol`) VALUES
(7, 'victor', '$2y$10$gSWNqU39dyysupuUb4PNP.10WfIKUNOlu6bX8WausMorDX1ImI1dK', 'medico');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `equiposmedicos`
--
ALTER TABLE `equiposmedicos`
  ADD PRIMARY KEY (`ID_EM`),
  ADD KEY `Codigo_ubi` (`Codigo_ubi`,`Codigo_Resp`),
  ADD KEY `Codigo_Resp` (`Codigo_Resp`);

--
-- Indices de la tabla `responsables`
--
ALTER TABLE `responsables`
  ADD PRIMARY KEY (`Codigo_Resp`),
  ADD UNIQUE KEY `Cedula` (`Cedula`);

--
-- Indices de la tabla `ubicaciones`
--
ALTER TABLE `ubicaciones`
  ADD PRIMARY KEY (`Codigo_ubi`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `equiposmedicos`
--
ALTER TABLE `equiposmedicos`
  ADD CONSTRAINT `equiposmedicos_ibfk_1` FOREIGN KEY (`Codigo_Resp`) REFERENCES `responsables` (`Codigo_Resp`) ON UPDATE CASCADE,
  ADD CONSTRAINT `equiposmedicos_ibfk_2` FOREIGN KEY (`Codigo_ubi`) REFERENCES `ubicaciones` (`Codigo_ubi`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
