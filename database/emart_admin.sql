-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 29, 2022 at 08:07 AM
-- Server version: 5.7.39-42-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `emart_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `routes` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `role_id`, `permission`, `routes`, `created_at`, `updated_at`) VALUES
(1, 1, 'section-service', 'section-service.list', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(2, 1, 'section-service', 'section.service.save', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(3, 1, 'section-service', 'section.service.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(4, 1, 'section-service', 'section.service.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(5, 1, 'roles', 'role.index', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(6, 1, 'roles', 'role.save', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(7, 1, 'roles', 'role.store', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(8, 1, 'roles', 'role.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(9, 1, 'roles', 'role.update', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(10, 1, 'roles', 'role.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(11, 1, 'admins', 'admin.users', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(12, 1, 'admins', 'admin.users.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(13, 1, 'admins', 'admin.users.store', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(14, 1, 'admins', 'admin.users.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(15, 1, 'admins', 'admin.users.update', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(16, 1, 'admins', 'admin.users.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(17, 1, 'users', 'users', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(18, 1, 'users', 'users.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(19, 1, 'users', 'users.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(20, 1, 'users', 'users.view', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(21, 1, 'users', 'users.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(22, 1, 'vendors', 'vendors', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(23, 1, 'vendors', 'vendors.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(24, 1, 'stores', 'stores', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(25, 1, 'stores', 'stores.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(26, 1, 'stores', 'stores.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(27, 1, 'stores', 'stores.view', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(28, 1, 'stores', 'stores.copy', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(29, 1, 'stores', 'stores.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(30, 1, 'drivers', 'drivers', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(31, 1, 'drivers', 'drivers.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(32, 1, 'drivers', 'drivers.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(33, 1, 'drivers', 'drivers.view', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(34, 1, 'drivers', 'drivers.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(35, 1, 'categories', 'categories', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(36, 1, 'categories', 'categories.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(37, 1, 'categories', 'categories.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(38, 1, 'categories', 'categories.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(39, 1, 'brands', 'brands', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(40, 1, 'brands', 'brands.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(41, 1, 'brands', 'brands.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(42, 1, 'brands', 'brands.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(43, 1, 'destinations', 'destinations', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(44, 1, 'destinations', 'destinations.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(45, 1, 'destinations', 'destinations.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(46, 1, 'destinations', 'destinations.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(47, 1, 'item-attributes', 'item.attributes', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(48, 1, 'item-attributes', 'item.attributes.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(49, 1, 'item-attributes', 'item.attributes.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(50, 1, 'item-attributes', 'item.attributes.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(51, 1, 'review-attributes', 'review.attributes', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(52, 1, 'review-attributes', 'review.attributes.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(53, 1, 'review-attributes', 'review.attributes.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(54, 1, 'review-attributes', 'review.attributes.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(55, 1, 'report', 'sales', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(56, 1, 'items', 'items', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(57, 1, 'items', 'items.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(58, 1, 'items', 'items.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(59, 1, 'items', 'items.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(60, 1, 'god-eye', 'map', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(61, 1, 'orders', 'orders', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(62, 1, 'orders', 'orders.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(63, 1, 'orders', 'orders.print', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(64, 1, 'orders', 'orders.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(65, 1, 'gift-cards', 'gift-card.index', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(66, 1, 'gift-cards', 'gift-card.save', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(67, 1, 'gift-cards', 'gift-card.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(68, 1, 'gift-cards', 'gift-card.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(69, 1, 'coupons', 'coupons', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(70, 1, 'coupons', 'coupons.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(71, 1, 'coupons', 'coupons.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(72, 1, 'coupons', 'coupons.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(73, 1, 'banners', 'banners', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(74, 1, 'banners', 'banners.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(75, 1, 'banners', 'banners.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(76, 1, 'banners', 'banners.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(77, 1, 'parcel-service-god-eye', 'parcel-service-map', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(78, 1, 'parcel-categories', 'parcel.categories', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(79, 1, 'parcel-categories', 'parcel.categories.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(80, 1, 'parcel-categories', 'parcel.categories.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(81, 1, 'parcel-categories', 'parcel.categories.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(82, 1, 'parcel-weight', 'parcel.weight', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(83, 1, 'parcel-weight', 'parcel.weight.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(84, 1, 'parcel-weight', 'parcel.weight.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(85, 1, 'parcel-weight', 'parcel.weight.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(86, 1, 'parcel-coupons', 'parcel.coupons', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(87, 1, 'parcel-coupons', 'parcel.coupons.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(88, 1, 'parcel-coupons', 'parcel.coupons.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(89, 1, 'parcel-coupons', 'parcel.coupons.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(90, 1, 'parcel-orders', 'parcel.orders', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(91, 1, 'parcel-orders', 'parcel.orders.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(92, 1, 'parcel-orders', 'parcel.orders.print', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(93, 1, 'parcel-orders', 'parcel.orders.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(94, 1, 'cab-service-god-eye', 'cab-service-map', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(95, 1, 'rides', 'rides', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(96, 1, 'rides', 'rides.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(97, 1, 'rides', 'rides.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(98, 1, 'sos-rides', 'sos.rides', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(99, 1, 'sos-rides', 'sos.rides.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(100, 1, 'sos-rides', 'sos.rides.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(101, 1, 'cab-promo', 'cab.promo', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(102, 1, 'cab-promo', 'cab.promo.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(103, 1, 'cab-promo', 'cab.promo.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(104, 1, 'cab-promo', 'cab.promo.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(105, 1, 'complaints', 'complaints', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(106, 1, 'complaints', 'complaints.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(107, 1, 'complaints', 'complaints.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(108, 1, 'cab-vehicle-type', 'cab-vehicle-type', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(109, 1, 'cab-vehicle-type', 'cab-vehicle-type.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(110, 1, 'cab-vehicle-type', 'cab-vehicle-type.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(111, 1, 'cab-vehicle-type', 'cab-vehicle-type.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(112, 1, 'rental-plural-god-eye', 'rental-plural-map', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(113, 1, 'rental-vehicle-type', 'rental-vehicle-type', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(114, 1, 'rental-vehicle-type', 'rental-vehicle-type.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(115, 1, 'rental-vehicle-type', 'rental-vehicle-type.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(116, 1, 'rental-vehicle-type', 'rental-vehicle-type.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(117, 1, 'rental-discount', 'rental-discount', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(118, 1, 'rental-discount', 'rental-discount.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(119, 1, 'rental-discount', 'rental-discount.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(120, 1, 'rental-discount', 'rental-discount.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(121, 1, 'rental-orders', 'rental-orders', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(122, 1, 'rental-orders', 'rental-orders.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(123, 1, 'rental-orders', 'rental-orders.print', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(124, 1, 'rental-orders', 'rental-orders.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(125, 1, 'rental-vehicle', 'rental-vehicle', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(126, 1, 'rental-vehicle', 'rental-vehicle.view', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(127, 1, 'make', 'make', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(128, 1, 'make', 'make.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(129, 1, 'make', 'make.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(130, 1, 'make', 'make.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(131, 1, 'model', 'model', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(132, 1, 'model', 'model.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(133, 1, 'model', 'model.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(134, 1, 'model', 'model.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(135, 1, 'general-notifications', 'notification', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(136, 1, 'general-notifications', 'notification.send', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(137, 1, 'general-notifications', 'notification.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(138, 1, 'dynamic-notifications', 'dynamic-notification.index', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(139, 1, 'dynamic-notifications', 'dynamic-notification.save', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(140, 1, 'email-template', 'email-templates.index', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(141, 1, 'email-template', 'email-templates.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(142, 1, 'cms', 'cms', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(143, 1, 'cms', 'cms.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(144, 1, 'cms', 'cms.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(145, 1, 'cms', 'cms.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(146, 1, 'stores-payment', 'stores.payment', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(147, 1, 'stores-payout', 'stores.payout', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(148, 1, 'stores-payout', 'stores.payout.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(149, 1, 'drivers-payment', 'drivers.payment', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(150, 1, 'drivers-payout', 'drivers.payout', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(151, 1, 'drivers-payout', 'drivers.payout.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(152, 1, 'wallet-transaction', 'wallet-transaction', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(154, 1, 'global-setting', 'settings.app.globals', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(155, 1, 'currency', 'currencies', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(156, 1, 'currency', 'currencies.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(157, 1, 'currency', 'currencies.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(158, 1, 'currency', 'currency.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(159, 1, 'payment-method', 'payment-method', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(160, 1, 'admin-commission', 'settings.app.adminCommission', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(161, 1, 'radius', 'settings.app.radiusConfiguration', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(162, 1, 'tax', 'tax', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(163, 1, 'tax', 'tax.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(164, 1, 'tax', 'tax.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(165, 1, 'tax', 'tax.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(166, 1, 'delivery-charge', 'settings.app.deliveryCharge', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(167, 1, 'language', 'language', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(168, 1, 'language', 'language.create', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(169, 1, 'language', 'language.edit', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(170, 1, 'language', 'language.delete', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(171, 1, 'special-offer', 'setting.specialOffer', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(172, 1, 'terms', 'termsAndConditions', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(173, 1, 'privacy', 'privacyPolicy', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(174, 1, 'home-page', 'homepageTemplate', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(175, 1, 'footer', 'footerTemplate', '2023-12-08 11:08:51', '2023-12-08 11:08:51'),
(350, 1, 'payout-request-driver', 'payout-request.driver', '2023-12-08 11:45:44', '2023-12-08 11:45:44'),
(351, 1, 'payout-request-vendor', 'payout-request.vendor', '2023-12-08 11:45:57', '2023-12-08 11:45:57'),
(1309, 1, 'ondemand-categories', 'ondemand.categories', '2024-01-24 13:07:48', '2024-01-24 13:07:48'),
(1310, 1, 'ondemand-categories', 'ondemand.categories.create', '2024-01-24 13:10:04', '2024-01-24 13:10:04'),
(1311, 1, 'ondemand-categories', 'ondemand.categories.edit', '2024-01-24 13:11:57', '2024-01-24 13:11:57'),
(1315, 1, 'ondemand-categories', 'ondemand.categories.delete', '2024-01-24 13:40:27', '2024-01-24 13:40:27'),
(1320, 1, 'providers', 'providers', '2024-01-30 09:48:08', '2024-01-30 09:48:08'),
(1321, 1, 'providers', 'providers.create', '2024-01-30 09:48:25', '2024-01-30 09:48:25'),
(1322, 1, 'providers', 'providers.edit', '2024-01-30 09:48:47', '2024-01-30 09:48:47'),
(1323, 1, 'ondemand-coupons', 'ondemand.coupons', '2024-02-02 05:14:41', '2024-02-02 05:14:41'),
(1324, 1, 'ondemand-coupons', 'ondemand.coupons.create', '2024-02-02 05:14:59', '2024-02-02 05:14:59'),
(1325, 1, 'ondemand-coupons', 'ondemand.coupons.edit', '2024-02-02 05:15:21', '2024-02-02 05:15:21'),
(1326, 1, 'ondemand-coupons', 'ondemand.coupons.delete', '2024-02-02 07:08:07', '2024-02-02 07:08:07'),
(1327, 1, 'providers', 'providers.delete', '2024-02-02 07:40:43', '2024-02-02 07:40:43'),
(1504, 1, 'ondemand-services', 'ondemand.services.index', '2024-02-13 11:12:18', '2024-02-13 11:12:18'),
(1505, 1, 'ondemand-services', 'ondemand.services.create', '2024-02-13 11:12:18', '2024-02-13 11:12:18'),
(1506, 1, 'ondemand-services', 'ondemand.services.edit', '2024-02-13 11:12:18', '2024-02-13 11:12:18'),
(1507, 1, 'ondemand-services', 'ondemand.services.delete', '2024-02-13 11:12:18', '2024-02-13 11:12:18'),
(1508, 1, 'ondemand-bookings', 'ondemand.bookings.index', '2024-02-13 12:41:11', '2024-02-13 12:41:11'),
(1509, 1, 'ondemand-bookings', 'ondemand.bookings.print', '2024-02-13 12:41:11', '2024-02-13 12:41:11'),
(1510, 1, 'ondemand-bookings', 'ondemand.bookings.edit', '2024-02-13 12:41:11', '2024-02-13 12:41:11'),
(1511, 1, 'ondemand-bookings', 'ondemand.bookings.delete', '2024-02-13 12:41:11', '2024-02-13 12:41:11'),
(1512, 1, 'ondemand-workers', 'ondemand.workers.index', '2024-02-13 14:03:46', '2024-02-13 14:03:46'),
(1513, 1, 'ondemand-workers', 'ondemand.workers.create', '2024-02-13 14:03:46', '2024-02-13 14:03:46'),
(1514, 1, 'ondemand-workers', 'ondemand.workers.edit', '2024-02-13 14:03:46', '2024-02-13 14:03:46'),
(1515, 1, 'ondemand-workers', 'ondemand.workers.delete', '2024-02-13 14:03:46', '2024-02-13 14:03:46'),
(1518, 1, 'providers', 'providers.view', '2024-04-02 08:05:58', '2024-04-02 08:05:58'),
(1519, 1, 'payout-request-provider', 'payout-request.provider', '2024-04-03 08:16:58', '2024-04-03 08:16:58'),
(1520, 1, 'provider-payout', 'provider.payout', '2024-04-03 10:30:21', '2024-04-03 10:30:21'),
(1521, 1, 'provider-payout', 'provider.payout.create', '2024-04-03 10:31:23', '2024-04-03 10:31:23'),
(1522, 1, 'provider-payment', 'provider.payment', '2024-04-03 11:55:11', '2024-04-03 11:55:11'),
(1523, 1, 'on-board', 'onboard.list', '2024-04-22 10:03:06', '2024-04-22 10:03:06'),
(1524, 1, 'on-board', 'onboard.edit', '2024-04-22 10:03:39', '2024-04-22 10:03:39'),
(2309, 1, 'app-banners-setting', 'settings.app.banners', '2024-09-23 12:23:15', '2024-09-23 12:23:15'),
(2792, 1, 'subscription-plans', 'subscription-plans', '2025-02-18 05:07:37', '2025-02-18 05:07:37'),
(2793, 1, 'subscription-plans', 'subscription-plans.create', '2025-02-18 05:07:55', '2025-02-18 05:07:55'),
(2794, 1, 'subscription-plans', 'subscription-plans.edit', '2025-02-18 05:08:19', '2025-02-18 05:08:19'),
(2795, 1, 'subscription-plans', 'subscription-plans.delete', '2025-02-18 05:08:42', '2025-02-18 05:08:42'),
(2796, 1, 'subscription-history', 'subscription.history', '2025-02-18 05:09:22', '2025-02-18 05:09:22'),
(2797, 1, 'business-model', 'business-model', '2025-02-18 07:50:05', '2025-02-18 07:50:05'),
(3219, 1, 'vendors', 'vendors.create', NULL, NULL),
(3220, 1, 'vendors', 'vendors.edit', NULL, NULL);

-- --------------------------------------------------------
--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role` (`id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', '2023-11-27 05:10:43', '2023-11-27 06:36:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int(15) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@emart.com', NULL, '$2y$10$4D/Oi3x7gxPwZ/zxCKtgCOlPNujUnUER0vkMjQ0moL7l3cAJwTIJa', 1, 'HjSePqqru7FrAQGczeNp2DSYtURRzxz7ffUYurQoV3vzEi0CgRtefjpbz5kA', '2022-02-26 12:22:29', '2022-03-02 08:48:06');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
