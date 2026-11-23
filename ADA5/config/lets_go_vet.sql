-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2026 a las 06:27:53
-- Versión del servidor: 10.11.16-MariaDB
-- Versión de PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lets_go_vet`
--

DELIMITER //
--
-- Procedimientos
--
CREATE PROCEDURE `sp_registrar_producto` (IN `p_codigo` VARCHAR(50), IN `p_producto` VARCHAR(150), IN `p_precio` DECIMAL(10,2), IN `p_stock` INT, OUT `p_nuevo_id` INT, OUT `p_mensaje` VARCHAR(200))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_nuevo_id = -1;
        SET p_mensaje = 'Error: No se pudo registrar el producto (verifica si el código ya existe).';
    END;

    START TRANSACTION;
    IF p_precio < 0 OR p_stock < 0 THEN
        SET p_nuevo_id = -1;
        SET p_mensaje = 'Error: El precio y el stock no pueden ser negativos.';
        ROLLBACK;
    ELSE
        INSERT INTO productos (id_codigo_de_barras, producto, precio, stock)
        VALUES (p_codigo, p_producto, p_precio, p_stock);
        SET p_nuevo_id = LAST_INSERT_ID();
        SET p_mensaje = CONCAT('Producto registrado con ID: ', p_nuevo_id);
        COMMIT;
    END IF;
END//

--
-- Funciones
--
CREATE FUNCTION `fn_estado_stock` (`p_stock` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    IF p_stock = 0 THEN
        RETURN 'Agotado';
    ELSEIF p_stock <= 5 THEN
        RETURN 'Stock Bajo';
    ELSE
        RETURN 'Disponible';
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_stock`
--

CREATE TABLE `bitacora_stock` (
  `id_bitacora` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `producto` varchar(150) DEFAULT NULL,
  `stock_antes` int(11) DEFAULT NULL,
  `stock_nuevo` int(11) DEFAULT NULL,
  `modificado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora_stock`
--

INSERT INTO `bitacora_stock` (`id_bitacora`, `id_producto`, `producto`, `stock_antes`, `stock_nuevo`, `modificado_en`) VALUES
(1, 1, 'Amoxicilina', 15, 0, '2026-11-23 05:07:35'),
(2, 2, 'Gasas esteriles', 30, 0, '2026-11-23 05:07:35'),
(3, 3, 'Croquetas para perro 2 kg', 20, 0, '2026-11-23 05:07:35'),
(4, 4, 'Toalla para mascotas', 12, 0, '2026-11-23 05:07:35'),
(5, 1, 'Amoxicilina', 0, 20, '2026-11-23 05:08:04'),
(6, 2, 'Gasas esteriles', 0, 20, '2026-11-23 05:08:08'),
(7, 3, 'Croquetas para perro 2 kg', 0, 20, '2026-11-23 05:08:13'),
(8, 4, 'Toalla para mascotas', 0, 20, '2026-11-23 05:08:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_facturas`
--

CREATE TABLE `detalle_facturas` (
  `id_detalle` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_facturas`
--

INSERT INTO `detalle_facturas` (`id_detalle`, `id_factura`, `id_producto`, `id_servicio`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 1, NULL, 15, 220.00, 3300.00),
(2, 1, 2, NULL, 30, 85.00, 2550.00),
(3, 1, 3, NULL, 20, 260.00, 5200.00),
(4, 1, 4, NULL, 12, 120.00, 1440.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_paciente` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`id_factura`, `id_usuario`, `id_paciente`, `total`, `fecha`) VALUES
(1, NULL, NULL, 12490.00, '2026-11-23 05:07:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id_paciente` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `edad` varchar(50) DEFAULT '',
  `especie` varchar(50) NOT NULL,
  `raza` varchar(100) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `nombre_dueno` varchar(150) NOT NULL,
  `telefono_dueno` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id_paciente`, `id_usuario`, `nombre`, `edad`, `especie`, `raza`, `peso`, `diagnostico`, `nombre_dueno`, `telefono_dueno`, `activo`, `creado_en`) VALUES
(1, 1, 'Firulais', '3 años', 'Perro', 'Mestizo', 12.50, 'Resfriado leve', 'Jonathan Pech', '9991234567', 1, '2026-11-22 19:14:54'),
(2, 2, 'Mandarino', '2 años', 'Gato', 'Atigrado', 4.20, 'Revisión general', 'Jaime Guillermo', '9997654321', 1, '2026-11-22 19:14:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `id_codigo_de_barras` varchar(50) NOT NULL,
  `producto` varchar(150) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `es_medicina` tinyint(1) DEFAULT 0,
  `es_para_venta` tinyint(1) DEFAULT 1,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `id_codigo_de_barras`, `producto`, `precio`, `stock`, `es_medicina`, `es_para_venta`, `activo`, `creado_en`) VALUES
(1, 'MED-001', 'Amoxicilina', 220.00, 20, 1, 1, 1, '2026-11-22 19:14:54'),
(2, 'INS-001', 'Gasas esteriles', 85.00, 20, 0, 1, 1, '2026-11-22 19:14:54'),
(3, 'ALI-001', 'Croquetas para perro 2 kg', 140.00, 20, 0, 1, 1, '2026-11-22 19:14:54'),
(4, 'ACC-001', 'Toalla para mascotas', 120.00, 20, 0, 1, 1, '2026-11-22 19:14:54');

--
-- Disparadores `productos`
--
DELIMITER $$
CREATE TRIGGER `trg_cambio_stock` AFTER UPDATE ON `productos` FOR EACH ROW BEGIN
    IF NOT (OLD.stock <=> NEW.stock) THEN
        INSERT INTO bitacora_stock (id_producto, producto, stock_antes, stock_nuevo)
        VALUES (OLD.id_producto, OLD.producto, OLD.stock, NEW.stock);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id_servicio` int(11) NOT NULL,
  `servicio` varchar(150) NOT NULL,
  `duracion` varchar(50) DEFAULT '30 min',
  `descripcion` varchar(255) DEFAULT '',
  `tipo_cobro` varchar(50) DEFAULT 'Consulta',
  `precio` decimal(10,2) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id_servicio`, `servicio`, `duracion`, `descripcion`, `tipo_cobro`, `precio`, `activo`, `creado_en`) VALUES
(1, 'Consulta general', '30 min', 'Evaluación médica completa', 'Consulta', 300.00, 1, '2026-11-22 19:14:54'),
(2, 'Vacunación', '20 min', 'Aplicación de vacunas', 'Consulta', 350.00, 1, '2026-11-22 19:14:54'),
(3, 'Desparasitación', '15 min', '', 'Consulta', 150.00, 1, '2026-11-23 05:17:03'),
(4, 'Limpieza dental', '60 min', '', 'Consulta', 800.00, 1, '2026-11-23 05:17:03'),
(5, 'Esterilización', '120 min', '', 'Consulta', 1500.00, 1, '2026-11-23 05:17:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `veterinario?` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `usuario`, `contrasenia`, `veterinario?`, `activo`, `creado_en`) VALUES
(1, 'brito', '1234', 1, 1, '2026-11-22 19:14:54'),
(2, 'israel', '1234', 1, 1, '2026-11-22 19:14:54');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_inventario_estado`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_inventario_estado` (
`id_producto` int(11)
,`id_codigo_de_barras` varchar(50)
,`producto` varchar(150)
,`precio` decimal(10,2)
,`stock` int(11)
,`estado_stock` varchar(10)
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora_stock`
--
ALTER TABLE `bitacora_stock`
  ADD PRIMARY KEY (`id_bitacora`);

--
-- Indices de la tabla `detalle_facturas`
--
ALTER TABLE `detalle_facturas`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `id_factura` (`id_factura`),
  ADD KEY `id_producto` (`id_producto`),
  ADD KEY `detalle_facturas_ibfk_3` (`id_servicio`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_paciente` (`id_paciente`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id_paciente`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `id_codigo_de_barras` (`id_codigo_de_barras`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora_stock`
--
ALTER TABLE `bitacora_stock`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `detalle_facturas`
--
ALTER TABLE `detalle_facturas`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id_paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_inventario_estado`
--
DROP TABLE IF EXISTS `vista_inventario_estado`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vista_inventario_estado`  AS SELECT `productos`.`id_producto` AS `id_producto`, `productos`.`id_codigo_de_barras` AS `id_codigo_de_barras`, `productos`.`producto` AS `producto`, `productos`.`precio` AS `precio`, `productos`.`stock` AS `stock`, CASE WHEN `productos`.`stock` = 0 THEN 'Agotado' WHEN `productos`.`stock` <= 5 THEN 'Stock Bajo' ELSE 'Disponible' END AS `estado_stock` FROM `productos` WHERE `productos`.`activo` = 1 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_facturas`
--
ALTER TABLE `detalle_facturas`
  ADD CONSTRAINT `detalle_facturas_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `facturas` (`id_factura`),
  ADD CONSTRAINT `detalle_facturas_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`),
  ADD CONSTRAINT `detalle_facturas_ibfk_3` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`);

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`id_paciente`) REFERENCES `pacientes` (`id_paciente`);

--
-- Filtros para la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD CONSTRAINT `pacientes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;