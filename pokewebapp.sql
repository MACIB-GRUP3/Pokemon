-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2024 a las 17:29:03
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
-- Base de datos: `pokewebapp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pokedek`
--

CREATE TABLE `pokedek` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pokedek`
--

INSERT INTO `pokedek` (`id`, `id_usuario`) VALUES
(197, 247),
(198, 248);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pokedek_pokemon`
--

CREATE TABLE `pokedek_pokemon` (
  `id` int(11) NOT NULL,
  `id_pokedek` int(11) DEFAULT NULL,
  `id_pokemon` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pokemon`
--

CREATE TABLE `pokemon` (
  `id` int(11) NOT NULL,
  `img_id` varchar(255) NOT NULL,
  `especie` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `peso` decimal(5,2) NOT NULL,
  `altura` decimal(5,2) NOT NULL,
  `baxp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pokemon`
--

INSERT INTO `pokemon` (`id`, `img_id`, `especie`, `nombre`, `peso`, `altura`, `baxp`) VALUES
(1, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(2, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(3, '11', 'metapod', 'metapod', 99.00, 7.00, 72),
(4, '18', 'pidgeot', 'pidgeot', 395.00, 15.00, 216),
(5, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(6, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(7, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(8, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(9, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(10, '11', 'metapod', 'metapod', 99.00, 7.00, 72),
(11, '12', 'butterfree', 'butterfree', 320.00, 11.00, 198),
(12, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(13, '9', 'blastoise', 'blastoise', 855.00, 16.00, 265),
(14, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(15, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(16, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(17, '7', 'squirtle', 'squirtle', 90.00, 5.00, 63),
(18, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(19, '25', 'pikachu', 'pikachu', 60.00, 4.00, 112),
(20, '33', 'nidorino', 'nidorino', 195.00, 9.00, 128),
(21, '15', 'beedrill', 'beedrill', 295.00, 10.00, 178),
(22, '38', 'ninetales', 'ninetales', 199.00, 11.00, 177),
(23, '54', 'psyduck', 'psyduck', 196.00, 8.00, 64),
(24, '59', 'arcanine', 'arcanine', 999.99, 19.00, 194),
(25, '52', 'meowth', 'meowth', 42.00, 4.00, 58),
(26, '8', 'wartortle', 'wartortle', 225.00, 10.00, 142),
(27, '26', 'raichu', 'raichu', 300.00, 8.00, 243),
(28, '58', 'growlithe', 'growlithe', 190.00, 7.00, 70),
(29, '68', 'machamp', 'machamp', 999.99, 16.00, 253),
(30, '66', 'machop', 'machop', 195.00, 8.00, 61),
(31, '7', 'squirtle', 'squirtle', 90.00, 5.00, 63),
(32, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(33, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(34, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(35, '151', 'mew', 'mew', 40.00, 4.00, 300),
(36, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(37, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(38, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(39, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(40, '141', 'kabutops', 'kabutops', 405.00, 13.00, 173),
(41, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(42, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(43, '7', 'squirtle', 'squirtle', 90.00, 5.00, 63),
(44, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(45, '151', 'mew', 'mew', 40.00, 4.00, 300),
(46, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(47, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(48, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(49, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(50, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(51, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(52, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(53, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(54, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(55, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(56, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(57, '146', 'moltres', 'moltres', 600.00, 20.00, 290),
(58, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(59, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(60, '49', 'venomoth', 'venomoth', 125.00, 15.00, 158),
(61, '21', 'spearow', 'spearow', 20.00, 3.00, 52),
(62, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(63, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(64, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(65, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(66, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(67, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(68, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(69, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(70, '11', 'metapod', 'metapod', 99.00, 7.00, 72),
(71, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(72, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(73, '26', 'raichu', 'raichu', 300.00, 8.00, 243),
(74, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(75, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(76, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(77, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(78, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(79, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(80, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(81, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(82, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(83, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(84, '11', 'metapod', 'metapod', 99.00, 7.00, 72),
(85, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(86, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(87, '33', 'nidorino', 'nidorino', 195.00, 9.00, 128),
(88, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(89, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(90, '8', 'wartortle', 'wartortle', 225.00, 10.00, 142),
(91, '17', 'pidgeotto', 'pidgeotto', 300.00, 11.00, 122),
(92, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(93, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(94, '1|whoami', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(95, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(96, '9', 'blastoise', 'blastoise', 855.00, 16.00, 265),
(97, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(98, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(99, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(100, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(101, '129', 'magikarp', 'magikarp', 100.00, 9.00, 40),
(102, '20', 'raticate', 'raticate', 185.00, 7.00, 145),
(103, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(104, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(105, '92', 'gastly', 'gastly', 1.00, 13.00, 62),
(106, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(107, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(108, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(109, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(110, '9', 'blastoise', 'blastoise', 855.00, 16.00, 265),
(111, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(112, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(113, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(114, '25', 'pikachu', 'pikachu', 60.00, 4.00, 112),
(115, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(116, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(117, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(118, '69', 'bellsprout', 'bellsprout', 40.00, 7.00, 60),
(119, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(120, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(121, '41', 'zubat', 'zubat', 75.00, 8.00, 49),
(122, '38', 'ninetales', 'ninetales', 199.00, 11.00, 177),
(123, '12', 'butterfree', 'butterfree', 320.00, 11.00, 198),
(124, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(125, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(126, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(127, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(128, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(129, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(130, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(131, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(132, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(133, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(134, '11', 'metapod', 'metapod', 99.00, 7.00, 72),
(135, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(136, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(137, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(138, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(139, '12', 'kakuna', 'kakuna', 320.00, 11.00, 198),
(140, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(141, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(142, '1', '', '', 0.00, 0.00, 0),
(143, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(144, '150', 'mewtwo', 'mewtwo', 999.99, 20.00, 340),
(145, '151', 'mew', 'mew', 40.00, 4.00, 300),
(146, '149', 'dragonite', 'dragonite', 999.99, 22.00, 300),
(147, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(148, '78', 'rapidash', 'rapidash', 950.00, 17.00, 175),
(149, '148', 'dragonair', 'dragonair', 165.00, 40.00, 147),
(150, '147', 'dratini', 'dratini', 33.00, 18.00, 60),
(151, '146', 'moltres', 'moltres', 600.00, 20.00, 290),
(152, '145', 'zapdos', 'zapdos', 526.00, 16.00, 290),
(153, '144', 'articuno', 'articuno', 554.00, 17.00, 290),
(154, '143', 'snorlax', 'snorlax', 999.99, 21.00, 189),
(155, '12', 'kabuto', 'kabuto', 320.00, 11.00, 198),
(156, '1', 'ninetales', 'ninetales', 69.00, 7.00, 64),
(157, '142', 'aerodactyl', 'aerodactyl', 590.00, 18.00, 180),
(158, '141', 'kabutops', 'kabutops', 405.00, 13.00, 173),
(159, '140', 'kabuto', 'kabuto', 115.00, 5.00, 71),
(160, '139', 'omastar', 'omastar', 350.00, 10.00, 173),
(161, '138', 'omanyte', 'omanyte', 75.00, 4.00, 71),
(162, '138', 'omanyte', 'omanyte', 75.00, 4.00, 71),
(163, '122', 'mr-mime', 'mr-mime', 545.00, 13.00, 161),
(164, '7', 'ninetales', 'ninetales', 69.00, 7.00, 64),
(165, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(166, '136', 'flareon', 'flareon', 250.00, 9.00, 184),
(167, '138', 'omanyte', 'omanyte', 75.00, 4.00, 71),
(168, '38', 'ninetales', 'ninetales', 199.00, 11.00, 177),
(169, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(170, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(171, '22', 'fearow', 'fearow', 380.00, 12.00, 155),
(172, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(173, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(174, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(175, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(176, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(177, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(178, '19', 'rattata', 'rattata', 35.00, 3.00, 51),
(179, '', '', '', 0.00, 0.00, 0),
(180, '4', 'charizard', 'charizard', 85.00, 6.00, 62),
(181, '', '', '', 0.00, 0.00, 0),
(182, '144', 'articuno', 'articuno', 554.00, 17.00, 290),
(183, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(184, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(185, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(186, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(187, '15', 'beedrill', 'beedrill', 295.00, 10.00, 178),
(188, '8', 'wartortle', 'wartortle', 225.00, 10.00, 142),
(189, '48', 'venonat', 'venonat', 300.00, 10.00, 61),
(190, '48', 'venonat', 'venonat', 300.00, 10.00, 61),
(191, '34', 'nidoking', 'nidoking', 620.00, 14.00, 253),
(192, '8', 'wartortle', 'wartortle', 225.00, 10.00, 142),
(193, '46', 'paras', 'paras', 54.00, 3.00, 57),
(194, '54', 'psyduck', 'psyduck', 196.00, 8.00, 64),
(195, '150', 'mewtwo', 'mewtwo', 999.99, 20.00, 340),
(196, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(197, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(198, '144', 'articuno', 'articuno', 554.00, 17.00, 290),
(199, '58', 'growlithe', 'growlithe', 190.00, 7.00, 70),
(200, '23', 'ekans', 'ekans', 69.00, 20.00, 58),
(201, '7', 'squirtle', 'squirtle', 90.00, 5.00, 63),
(202, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(203, '16', 'pidgey', 'pidgey', 18.00, 3.00, 50),
(204, '9', 'blastoise', 'blastoise', 855.00, 16.00, 265),
(205, '24', 'arbok', 'arbok', 650.00, 35.00, 157),
(206, '47', 'parasect', 'parasect', 295.00, 10.00, 142),
(207, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(208, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(209, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(210, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(211, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(212, '9', 'blastoise', 'blastoise', 855.00, 16.00, 265),
(213, '8', 'wartortle', 'wartortle', 225.00, 10.00, 142),
(214, '22', 'fearow', 'fearow', 380.00, 12.00, 155),
(215, '7', 'squirtle', 'squirtle', 90.00, 5.00, 63),
(216, '24', 'arbok', 'arbok', 650.00, 35.00, 157),
(217, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(218, '20', 'raticate', 'raticate', 185.00, 7.00, 145),
(219, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(220, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(221, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(222, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(223, '17', 'pidgeotto', 'pidgeotto', 300.00, 11.00, 122),
(224, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(225, '25', 'pikachu', 'pikachu', 60.00, 4.00, 112),
(226, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(227, '111', 'rhyhorn', 'rhyhorn', 999.99, 10.00, 69),
(228, '18', 'pidgeot', 'pidgeot', 395.00, 15.00, 216),
(229, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(230, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(231, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(232, '38', 'ninetales', 'ninetales', 199.00, 11.00, 177),
(233, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(234, '45', 'vileplume', 'vileplume', 186.00, 12.00, 245),
(235, '39', 'jigglypuff', 'jigglypuff', 55.00, 5.00, 95),
(236, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(237, '46', 'paras', 'paras', 54.00, 3.00, 57),
(238, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(239, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(240, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(241, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(242, '16', 'pidgey', 'pidgey', 18.00, 3.00, 50),
(243, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(244, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(245, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(246, '151', 'mew', 'mew', 40.00, 4.00, 300),
(247, '150', 'mewtwo', 'mewtwo', 999.99, 20.00, 340),
(248, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(249, '132', 'ditto', 'ditto', 40.00, 3.00, 101),
(250, '52', 'meowth', 'meowth', 42.00, 4.00, 58),
(251, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(252, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(253, '8', 'wartortle', 'wartortle', 225.00, 10.00, 142),
(254, '8', 'wartortle', 'wartortle', 225.00, 10.00, 142),
(255, '9', 'blastoise', 'blastoise', 855.00, 16.00, 265),
(256, '144', 'articuno', 'articuno', 554.00, 17.00, 290),
(257, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(258, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(259, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(260, '7', 'squirtle', 'squirtle', 90.00, 5.00, 63),
(261, '25', 'pikachu', 'pikachu', 60.00, 4.00, 112),
(262, '26', 'raichu', 'raichu', 300.00, 8.00, 243),
(263, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(264, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(265, '10', 'caterpie', 'caterpie', 29.00, 3.00, 39),
(266, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(267, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(268, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(269, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(270, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(271, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(272, '11', 'metapod', 'metapod', 99.00, 7.00, 72),
(273, '45', 'vileplume', 'vileplume', 186.00, 12.00, 245),
(274, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(275, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(276, '151', 'mew', 'mew', 40.00, 4.00, 300),
(277, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(278, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(279, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(280, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(281, '1', 'bulbasaur', 'bulbasaur', 69.00, 7.00, 64),
(282, '2', 'ivysaur', 'ivysaur', 130.00, 10.00, 142),
(283, '3', 'venusaur', 'venusaur', 999.99, 20.00, 263),
(284, '4', 'charmander', 'charmander', 85.00, 6.00, 62),
(285, '5', 'charmeleon', 'charmeleon', 190.00, 11.00, 142),
(286, '6', 'charizard', 'charizard', 905.00, 17.00, 267),
(287, '7', 'squirtle', 'squirtle', 90.00, 5.00, 63),
(288, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(289, '11', 'metapod', 'metapod', 99.00, 7.00, 72),
(290, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(291, '13', 'weedle', 'weedle', 32.00, 3.00, 39),
(292, '14', 'kakuna', 'kakuna', 100.00, 6.00, 72),
(293, '16', 'pidgey', 'pidgey', 18.00, 3.00, 50),
(294, '4', 'charmander', 'charmander', 85.00, 6.00, 62);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(500) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `pokeballs` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `correo`, `contrasena`, `pokeballs`, `fecha_creacion`) VALUES
(247, 'mmora', 'marc.mora.m@gmail.com', '$2y$10$BihYjTKqItk1jpk5pchCl.6aorz7JFbFMgTQMSWY6smxtjFityzs.', 30, '2024-10-30 03:03:34'),
(248, 'mmora1', 'm@mmora.es', '$2y$10$D172TQw7axPkxgQEBusfF.sKSiL6n7dG.HArV.wFIrg/AzkohGf5e', 30, '2024-10-30 03:57:04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pokedek`
--
ALTER TABLE `pokedek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `pokedek_pokemon`
--
ALTER TABLE `pokedek_pokemon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pokedek` (`id_pokedek`),
  ADD KEY `id_pokemon` (`id_pokemon`);

--
-- Indices de la tabla `pokemon`
--
ALTER TABLE `pokemon`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pokedek`
--
ALTER TABLE `pokedek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT de la tabla `pokedek_pokemon`
--
ALTER TABLE `pokedek_pokemon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT de la tabla `pokemon`
--
ALTER TABLE `pokemon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `pokedek`
--
ALTER TABLE `pokedek`
  ADD CONSTRAINT `pokedek_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pokedek_pokemon`
--
ALTER TABLE `pokedek_pokemon`
  ADD CONSTRAINT `pokedek_pokemon_ibfk_1` FOREIGN KEY (`id_pokedek`) REFERENCES `pokedek` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pokedek_pokemon_ibfk_2` FOREIGN KEY (`id_pokemon`) REFERENCES `pokemon` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
