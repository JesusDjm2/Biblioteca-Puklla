-- ============================================================================
-- MIGRACIÓN MANUAL PARA PRODUCCIÓN: TAQ'E (REPOSITORIO DE TRABAJOS)
-- Servidor: EESP Pukllasunchis
-- Fecha: 2026-10-15 21:36:26
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Estructura de tabla: taqe_categorias
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `taqe_categorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#c78e41',
  `icono` varchar(50) NOT NULL DEFAULT 'fa-folder',
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taqe_categorias_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Estructura de tabla: taqe_trabajos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `taqe_trabajos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` bigint(20) unsigned NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(265) NOT NULL,
  `autores` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `formato_principal` enum('pdf','audio','video','imagen','multimedia') NOT NULL DEFAULT 'pdf',
  `idioma` enum('castellano','quechua','bilingue') NOT NULL DEFAULT 'castellano',
  `año_periodo` varchar(50) DEFAULT NULL,
  `ciclo_carrera` varchar(150) DEFAULT NULL,
  `archivo_pdf` varchar(255) DEFAULT NULL,
  `archivo_audio` varchar(255) DEFAULT NULL,
  `url_video` varchar(255) DEFAULT NULL,
  `portada` varchar(255) DEFAULT NULL,
  `vistas` int(10) unsigned NOT NULL DEFAULT 0,
  `descargas` int(10) unsigned NOT NULL DEFAULT 0,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `estado` enum('publicado','borrador') NOT NULL DEFAULT 'publicado',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taqe_trabajos_slug_unique` (`slug`),
  KEY `taqe_trabajos_categoria_id_foreign` (`categoria_id`),
  KEY `taqe_trabajos_formato_principal_index` (`formato_principal`),
  KEY `taqe_trabajos_idioma_index` (`idioma`),
  KEY `taqe_trabajos_estado_index` (`estado`),
  CONSTRAINT `taqe_trabajos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `taqe_categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Datos iniciales: Categorías Taq'e
-- --------------------------------------------------------
INSERT INTO `taqe_categorias` (`id`, `nombre`, `slug`, `descripcion`, `color`, `icono`, `orden`, `activo`, `created_at`, `updated_at`) VALUES (1, 'Saberes y Prácticas Ancestrales', 'saberes-ancestrales', 'Investigaciones sobre la siembra, abuelos, cosmovisión y el ciclo agrofestivo.', '#c78e41', 'fa-leaf', 1, 1, '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);
INSERT INTO `taqe_categorias` (`id`, `nombre`, `slug`, `descripcion`, `color`, `icono`, `orden`, `activo`, `created_at`, `updated_at`) VALUES (2, 'Estrategias y Materiales Pedagógicos', 'materiales-pedagogicos', 'Herramientas de planificación, programación y cartillas para el aula bilingüe.', '#2e7d32', 'fa-book-reader', 2, 1, '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);
INSERT INTO `taqe_categorias` (`id`, `nombre`, `slug`, `descripcion`, `color`, `icono`, `orden`, `activo`, `created_at`, `updated_at`) VALUES (3, 'Cuentos, Mitos y Tradición Oral', 'tradicion-oral', 'Narraciones, relatos orales y leyendas recopiladas en quechua y castellano.', '#1565c0', 'fa-commenting', 3, 1, '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);
INSERT INTO `taqe_categorias` (`id`, `nombre`, `slug`, `descripcion`, `color`, `icono`, `orden`, `activo`, `created_at`, `updated_at`) VALUES (4, 'Salud Comunitaria y Plantas Medicinales', 'plantas-medicinales', 'Saberes médicos ancestrales, curación tradicional y uso de flora medicinal.', '#6a1b9a', 'fa-heartbeat', 4, 1, '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- --------------------------------------------------------
-- 4. Datos iniciales: Trabajos de Estudiantes Taq'e
-- --------------------------------------------------------
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (1, 1, 'Los Apus tienen vida', 'los-apus-tienen-vida', 'Ruth Mery Quispicusi, Bill Fredy Arones', 'Investigación y animación en stopmotion que visibiliza la relación comunitaria con los Apus tutelares y el respeto a la Madre Tierra.', 'video', 'castellano', '2024', 'II Ciclo Educación Inicial EIB', NULL, NULL, 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', NULL, 0, 0, 1, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (2, 2, 'Cartillas de Señas e Indicadores Naturales', 'cartillas-de-senas-e-indicadores-naturales', 'Franklin Medina', 'Cartillas pedagógicas que sistematizan las señas del clima, floración de plantas y comportamiento animal para la siembra comunitaria.', 'pdf', 'bilingue', '2024', 'IV Ciclo Educación Primaria EIB', NULL, NULL, NULL, NULL, 0, 0, 1, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (3, 1, 'Wankay de despedida a la siembra de maíz', 'wankay-de-despedida-a-la-siembra-de-maiz', 'Estudiantes de Práctica Pedagógica EIB', 'Registro sonoro y canto tradicional quechua de despedida al trabajo colectivo de siembra en las parcelas familiares.', 'audio', 'quechua', '2022', 'III Ciclo EIB', NULL, NULL, NULL, NULL, 0, 0, 1, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (4, 4, 'Curación de Huesos a través de plantas medicinales', 'curacion-de-huesos-a-traves-de-plantas-medicinales', 'Jakeline Aparicio, Luz Mery Conza', 'Documentación de prácticas de curación tradicional y aplicación de plantas medicinales para fracturas y dolores articulares.', 'pdf', 'bilingue', '2024', 'V Ciclo Educación Inicial EIB', NULL, NULL, NULL, NULL, 0, 0, 1, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (5, 3, 'Cuentos y Leyendas de Nuestras Comunidades', 'cuentos-y-leyendas-de-nuestras-comunidades', 'Judith Turpo, Maribel Coyo', 'Compilado ilustrado de relatos orales transmitidos por abuelas y abuelos en comunidades quechuahablantes del Cusco.', 'multimedia', 'bilingue', '2023', 'II Ciclo Educación Primaria EIB', NULL, NULL, NULL, NULL, 0, 0, 0, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (6, 3, 'Historia de la Laguna Auquis Anyarate', 'historia-de-la-laguna-auquis-anyarate', 'Abraham Gamarra', 'Testimonio en lengua originaria quechua sobre los misterios, memoria y encantos de la laguna Auquis.', 'video', 'quechua', '2022', 'I Ciclo Quechua', NULL, NULL, NULL, NULL, 0, 0, 0, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (7, 4, 'Narraciones sobre Plantas Medicinales Autóctonas', 'narraciones-sobre-plantas-medicinales-autoctonas', 'Felipe Huisa', 'Sistematización y diagramación gráfica de recetas ancestrales a base de hierbas medicinales de los valles andinos.', 'pdf', 'quechua', '2023', 'III Ciclo EIB', NULL, NULL, NULL, NULL, 0, 0, 0, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);
INSERT INTO `taqe_trabajos` (`id`, `categoria_id`, `titulo`, `slug`, `autores`, `descripcion`, `formato_principal`, `idioma`, `año_periodo`, `ciclo_carrera`, `archivo_pdf`, `archivo_audio`, `url_video`, `portada`, `vistas`, `descargas`, `destacado`, `estado`, `created_at`, `updated_at`) VALUES (8, 1, 'El Chúyay de las ovejas: Ritos de fertilidad y protección', 'el-chuyay-de-las-ovejas-ritos-de-fertilidad-y-proteccion', 'Marina Chocata, María Rosa Challhua, Verónica Qqenta', 'Registro sonoro y etnográfico de los cantos y rituales de agradecimiento al rebaño en el ciclo ganadero andino.', 'audio', 'quechua', '2024', 'IV Ciclo Educación Inicial EIB', NULL, NULL, NULL, NULL, 0, 0, 1, 'publicado', '2026-10-15 21:22:05', '2026-10-15 21:22:05') ON DUPLICATE KEY UPDATE `titulo` = VALUES(`titulo`);

SET FOREIGN_KEY_CHECKS = 1;
