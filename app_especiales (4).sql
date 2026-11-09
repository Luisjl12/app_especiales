-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-11-2026 a las 23:39:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `app_especiales`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cordes`
--

CREATE TABLE `cordes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clave_corde` varchar(20) NOT NULL,
  `nombre_corde` varchar(255) NOT NULL,
  `fecha_actualizacion_seccion` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cordes`
--

INSERT INTO `cordes` (`id`, `clave_corde`, `nombre_corde`, `fecha_actualizacion_seccion`, `created_at`, `updated_at`) VALUES
(1, '1', 'Puebla', '2025-07-19 01:03:06', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(2, '01HUA', 'HUAUCHINANGO', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(3, '02CHI', 'CHIGNAHUAPAN', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(4, '03TEZ', 'TEZIUTLAN', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(5, '04LIB', 'LIBRES', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(6, '05CHO', 'CHOLULA', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(7, '06PUEPON', 'PUEBLA PONIENTE', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(8, '07IZU', 'IZUCAR DE MATAMOROS', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(9, '08ACA', 'ACATLAN DE OSORIO', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(10, '09TEP', 'TEPEACA', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(11, '10TEH', 'TEHUACAN', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(12, '11PUEORI', 'PUEBLA ORIENTE', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(13, '12PUENOR', 'PUEBLA NORTE', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(14, '13PUESUR', 'PUEBLA SUR', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(15, '14ZAC', 'ZACAPOAXTLA', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(16, '15SER', 'CIUDAD SERDAN', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(17, '16ATL', 'ATLIXCO', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(18, '17TEP', 'TEPEXI', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(19, '18TEX', 'SAN MARTIN TEXMELUCAN', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29'),
(20, '19ZAC', 'ZACATLAN', '2025-07-19 02:29:05', '2025-08-23 01:08:29', '2025-08-23 01:08:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `localidades`
--

CREATE TABLE `localidades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `municipio_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `localidades`
--

INSERT INTO `localidades` (`id`, `municipio_id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tehuacán', '2026-11-07 03:33:26', '2026-11-07 03:33:26'),
(2, 1, 'Santa María Coapan', '2026-11-07 03:33:26', '2026-11-07 03:33:26'),
(3, 1, 'San Diego Chalma', '2026-11-07 03:33:26', '2026-11-07 03:33:26'),
(4, 1, 'San Cristóbal Tepeteopan', '2026-11-07 03:33:26', '2026-11-07 03:33:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(5, '2026_10_28_152403_create_roles_table', 2),
(6, '2026_10_28_153656_add_role_id_to__users_table', 3),
(7, '2026_10_29_190511_create_plantels_table', 4),
(8, '2026_10_29_220623_create_municipios_table', 5),
(11, '2026_10_29_220644_create_localidads_table', 6),
(12, '2026_10_29_220747_add_localidad_id_to_planteles_table', 6),
(13, '2026_11_06_230405_create_cordes_table', 7),
(14, '2026_11_06_230621_add_corde_id_to_planteles_table', 7),
(15, '2026_11_09_222741_add_datos_generales_to_planteles_table', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Tehuacán', '2026-11-04 05:45:03', '2026-11-04 05:45:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planteles`
--

CREATE TABLE `planteles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cct` varchar(10) NOT NULL,
  `nombre_escuela` varchar(255) NOT NULL,
  `turno` varchar(50) DEFAULT NULL,
  `nivel_educativo` varchar(100) DEFAULT NULL,
  `sostenimiento` varchar(100) DEFAULT NULL,
  `domicilio_calle_numero` varchar(255) DEFAULT NULL,
  `domicilio_colonia` varchar(255) DEFAULT NULL,
  `domicilio_cp` varchar(10) DEFAULT NULL,
  `telefono_plantel` varchar(50) DEFAULT NULL,
  `correo_institucional` varchar(255) DEFAULT NULL,
  `localidad_id` bigint(20) UNSIGNED DEFAULT NULL,
  `corde_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `planteles`
--

INSERT INTO `planteles` (`id`, `cct`, `nombre_escuela`, `turno`, `nivel_educativo`, `sostenimiento`, `domicilio_calle_numero`, `domicilio_colonia`, `domicilio_cp`, `telefono_plantel`, `correo_institucional`, `localidad_id`, `corde_id`, `created_at`, `updated_at`) VALUES
(3, '21DJN0501N', 'XANAT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 4, '2026-11-07 05:55:49', '2026-11-07 05:55:49'),
(4, '21DJN0614Q', 'JOSE ANTONIO ALZATE', 'Matutino', 'Preescolar', 'Federal', '6', 'Santa Cruz', '74169', '2211019902', 'lunajuarez@outlook.com', 2, 1, '2026-11-10 05:30:13', '2026-11-10 05:30:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Super usuario', '2026-10-28 16:39:26', '2026-10-28 16:39:26'),
(2, 'usuario', 'Usuario normal', '2026-10-28 16:39:26', '2026-10-28 16:39:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('9faVpvEDkwV4p622dl4JQ4ZUHVCFFV4BVUxggzZM', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJEMWUxcGhBZm9ZQXhVYlpYcDV4ZzYxVUdiSGl5TVg5RHY4RXFkaGN6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pblwvcGxhbnRlbGVzIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyLCJwYXNzd29yZF9oYXNoX3dlYiI6IjEwMmNjMjE2YjE0M2Q2ZjdjZjY1Njc5YTMwYWUwMDMzZmE0NDNmYTI1Y2FkNzVmYTExOGFlMmUwMmNiZDEzYjIifQ==', 1794267046);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 2,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'Administrador', 'admin@demo.com', NULL, '$2y$12$WPx3JC.NPr6M33WrKzAkL.WOuDQaL4bTgQE0O0oWNq4H253W6Lkee', 'B3Bsd11EXkKN1n5I0p1wj6tbVmu9ll1Xqq8aM59HvFWz9o75foGVuXSkJZga', '2026-10-28 03:42:02', '2026-10-29 00:36:54'),
(2, 1, 'Luis Mario Juárez Luna', 'lunajuarez566@outlook.com', NULL, '$2y$12$zjQ2tOtrhFizKhAs.lio1.i1khVy8lJotrVfXgaBOocay9UFUHEqC', NULL, '2026-10-29 04:59:01', '2026-10-29 04:59:01'),
(3, 2, 'Juan Carlos Juárez Luna', 'juanj21@gmail.com', NULL, '$2y$12$6RxyRVs4BwJQHbFE7WaoCeXYyhUkY5hJy6bWQ5xe88yed8N2n.zVC', NULL, '2026-10-29 05:00:44', '2026-10-29 05:00:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `cordes`
--
ALTER TABLE `cordes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `localidades`
--
ALTER TABLE `localidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `localidades_municipio_id_foreign` (`municipio_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `planteles`
--
ALTER TABLE `planteles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plantels_cct_unique` (`cct`),
  ADD KEY `planteles_localidad_id_foreign` (`localidad_id`),
  ADD KEY `planteles_corde_id_foreign` (`corde_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cordes`
--
ALTER TABLE `cordes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `localidades`
--
ALTER TABLE `localidades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `planteles`
--
ALTER TABLE `planteles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `localidades`
--
ALTER TABLE `localidades`
  ADD CONSTRAINT `localidades_municipio_id_foreign` FOREIGN KEY (`municipio_id`) REFERENCES `municipios` (`id`);

--
-- Filtros para la tabla `planteles`
--
ALTER TABLE `planteles`
  ADD CONSTRAINT `planteles_corde_id_foreign` FOREIGN KEY (`corde_id`) REFERENCES `cordes` (`id`),
  ADD CONSTRAINT `planteles_localidad_id_foreign` FOREIGN KEY (`localidad_id`) REFERENCES `localidades` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
