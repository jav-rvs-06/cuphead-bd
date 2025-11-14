-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-11-2025 a las 06:32:54
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
-- Base de datos: `cuphead_guia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `pagina` varchar(100) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_comentario` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id`, `usuario_id`, `pagina`, `titulo`, `contenido`, `fecha_comentario`) VALUES
(1, 4, 'personajes', 'Cuphead The best', 'amo a cuphead', '2025-11-11 06:45:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `jefe_nombre` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `valor` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_prueba` varchar(255) NOT NULL,
  `verificador_id` int(11) DEFAULT NULL,
  `comentario_verificacion` text DEFAULT NULL,
  `fecha_verificacion` timestamp NULL DEFAULT NULL,
  `verificado` enum('pendiente','aprobado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `records`
--

INSERT INTO `records` (`id`, `usuario_id`, `jefe_nombre`, `categoria`, `valor`, `descripcion`, `imagen_prueba`, `verificador_id`, `comentario_verificacion`, `fecha_verificacion`, `verificado`, `fecha_registro`) VALUES
(1, 4, 'The Devil', 'Sin Daño', '2:36', 'He logrado superar al diablo en ese tiempo', 'uploads/records/record_4_1762843599.jpg', NULL, NULL, NULL, 'aprobado', '2025-11-11 06:46:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `correo_electronico` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('usuario','admin_comentarios','admin_records','superadmin','admin','admin_comunidad') DEFAULT 'usuario',
  `activo` tinyint(4) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre_usuario`, `correo_electronico`, `contrasena`, `rol`, `activo`, `fecha_creacion`) VALUES
(1, 'superadmin', 'superadmin@cuphead.com', '$2y$10$j2KHLwskUw/LSImEvVworOIvHov0L599sv0FWscw5HA1ayyJYZpma', 'superadmin', 1, '2025-11-11 06:32:15'),
(2, 'admin_comunidad', 'admin_comunidad@cuphead.com', '$2y$10$CkZyZuctfFcTC6NJ505zdeaFdr23wfLKCEKjUqKuuhRbSXDTDdwRi', 'admin_comunidad', 1, '2025-11-11 06:32:15'),
(3, 'admin_records', 'admin_records@cuphead.com', '$2y$10$xtlOt8JgmAAEEG9wrHBOPOJ6b2.dakRgzXBbQkpiekaBNjxPUr0OG', 'admin_records', 1, '2025-11-11 06:32:15'),
(4, 'usuario_normal', 'usuario@cuphead.com', '$2y$10$e8hhabu3nHoWB2XKGyMRJObPST7NrSUuzCB85EKhporWc2aAK5hL.', 'usuario', 1, '2025-11-11 06:32:15');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pagina` (`pagina`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verificador_id` (`verificador_id`),
  ADD KEY `idx_usuario_records` (`usuario_id`),
  ADD KEY `idx_verificado` (`verificado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo_electronico` (`correo_electronico`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `records`
--
ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `records`
--
ALTER TABLE `records`
  ADD CONSTRAINT `records_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `records_ibfk_2` FOREIGN KEY (`verificador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
