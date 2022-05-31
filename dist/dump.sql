SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `advanced_words` (
  `id` int(11) NOT NULL,
  `word` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v1d` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `wordtype1` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v2d` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `wordtype2` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v3d` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `wordtype3` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v4d` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `wordtype4` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v5d` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `wordtype5` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `done` tinyint(1) DEFAULT '0',
  `study` tinyint(1) DEFAULT '1',
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `random_order` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `regular_words` (
  `id` int(11) NOT NULL,
  `word` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v1` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v1d` text COLLATE utf8mb4_general_ci,
  `wordtype1` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v2d` text COLLATE utf8mb4_general_ci,
  `wordtype2` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v3` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v3d` text COLLATE utf8mb4_general_ci,
  `wordtype3` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v4` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v4d` text COLLATE utf8mb4_general_ci,
  `wordtype4` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v5` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `v5d` text COLLATE utf8mb4_general_ci,
  `wordtype5` enum('phrasalverb','setphrase','idiom') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `done` tinyint(1) DEFAULT '0',
  `study` tinyint(1) DEFAULT '1',
  `auto` tinyint(1) NOT NULL DEFAULT '0',
  `random_order` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `advanced_words`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `word` (`word`);

ALTER TABLE `regular_words`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `word` (`word`);


ALTER TABLE `advanced_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `regular_words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
