-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Хост: 10.0.0.11
-- Время создания: Янв 14 2018 г., 11:12
-- Версия сервера: 5.7.20-19
-- Версия PHP: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `todo`
--

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_sessions` (IN `_user_id` INT(11) UNSIGNED)  BEGIN
	SELECT `app_id`, `token` FROM `access` WHERE `user_id` = _user_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_users` ()  BEGIN
	SELECT `user_id`, `email` FROM `users`;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `access`
--

CREATE TABLE `access` (
  `app_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `colors`
--

CREATE TABLE `colors` (
  `color_id` int(2) UNSIGNED NOT NULL,
  `red` int(3) UNSIGNED NOT NULL,
  `green` int(3) UNSIGNED NOT NULL,
  `blue` int(3) UNSIGNED NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `colors`
--

INSERT INTO `colors` (`color_id`, `red`, `green`, `blue`, `insert_date`, `update_date`) VALUES
(1, 26, 188, 156, '2018-01-08 21:07:22', NULL),
(2, 22, 160, 133, '2018-01-08 21:07:22', NULL),
(3, 46, 204, 113, '2018-01-08 21:07:22', NULL),
(4, 39, 174, 96, '2018-01-08 21:07:22', NULL),
(5, 52, 152, 219, '2018-01-08 21:07:22', NULL),
(6, 41, 128, 185, '2018-01-08 21:07:22', NULL),
(7, 155, 98, 182, '2018-01-08 21:07:22', NULL),
(8, 142, 68, 173, '2018-01-08 21:07:22', NULL),
(9, 52, 73, 94, '2018-01-08 21:07:22', NULL),
(10, 44, 62, 80, '2018-01-08 21:07:22', NULL),
(11, 241, 196, 15, '2018-01-08 21:07:22', NULL),
(12, 243, 156, 18, '2018-01-08 21:07:22', NULL),
(13, 230, 126, 34, '2018-01-08 21:07:22', NULL),
(14, 211, 84, 0, '2018-01-08 21:07:22', NULL),
(15, 231, 76, 60, '2018-01-08 21:07:22', NULL),
(16, 192, 57, 43, '2018-01-08 21:07:22', NULL),
(17, 236, 240, 241, '2018-01-08 21:07:22', NULL),
(18, 189, 195, 199, '2018-01-08 21:07:22', NULL),
(19, 149, 165, 166, '2018-01-08 21:07:22', NULL),
(20, 127, 140, 141, '2018-01-08 21:07:22', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `icons`
--

CREATE TABLE `icons` (
  `icon_id` int(2) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `icons`
--

INSERT INTO `icons` (`icon_id`, `name`, `insert_date`, `update_date`) VALUES
(1, 'appointments', '2018-01-08 21:07:57', NULL),
(2, 'birthdays', '2018-01-08 21:07:57', NULL),
(3, 'chores', '2018-01-08 21:07:57', NULL),
(4, 'drinks', '2018-01-08 21:07:57', NULL),
(5, 'folder', '2018-01-08 21:07:57', NULL),
(6, 'groceries', '2018-01-08 21:07:57', NULL),
(7, 'inbox', '2018-01-08 21:07:57', NULL),
(8, 'no_icon', '2018-01-08 21:07:57', NULL),
(9, 'photos', '2018-01-08 21:07:57', NULL),
(10, 'trips', '2018-01-08 21:07:57', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `items`
--

CREATE TABLE `items` (
  `item_id` int(11) UNSIGNED NOT NULL,
  `list_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(40) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `lists`
--

CREATE TABLE `lists` (
  `list_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(40) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color_id` int(2) UNSIGNED NOT NULL,
  `icon_id` int(2) UNSIGNED NOT NULL,
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`app_id`);

--
-- Индексы таблицы `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`color_id`);

--
-- Индексы таблицы `icons`
--
ALTER TABLE `icons`
  ADD PRIMARY KEY (`icon_id`);

--
-- Индексы таблицы `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Индексы таблицы `lists`
--
ALTER TABLE `lists`
  ADD PRIMARY KEY (`list_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `access`
--
ALTER TABLE `access`
  MODIFY `app_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `colors`
--
ALTER TABLE `colors`
  MODIFY `color_id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT для таблицы `icons`
--
ALTER TABLE `icons`
  MODIFY `icon_id` int(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT для таблицы `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `lists`
--
ALTER TABLE `lists`
  MODIFY `list_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
