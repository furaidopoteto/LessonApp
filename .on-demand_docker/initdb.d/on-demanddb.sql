-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2023-03-19 11:02:51
-- サーバのバージョン： 10.4.27-MariaDB
-- PHP のバージョン: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `on-demanddb`
--

CREATE DATABASE `on-demanddb`;

USE `on-demanddb`

-- --------------------------------------------------------

--
-- テーブルの構造 `account`
--

CREATE TABLE `account` (
  `studentnumber` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `grade` int(11) NOT NULL,
  `gakubu` text NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `anketo`
--

CREATE TABLE `anketo` (
  `anketoid` int(11) NOT NULL,
  `jugyoid` int(11) NOT NULL,
  `studentnumber` int(11) NOT NULL,
  `item1` int(11) NOT NULL,
  `item2` int(11) NOT NULL,
  `item3` int(11) NOT NULL,
  `item4` int(11) NOT NULL,
  `item5` int(11) NOT NULL,
  `question` text NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `jukodata`
--

CREATE TABLE `jukodata` (
  `id` int(11) NOT NULL,
  `studentnumber` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `gakubu` text NOT NULL,
  `jugyoid` int(11) NOT NULL,
  `jukotime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `kadai`
--

CREATE TABLE `kadai` (
  `id` int(11) NOT NULL,
  `jugyoid` int(11) NOT NULL,
  `studentnumber` int(11) NOT NULL,
  `kadaipass` text NOT NULL,
  `filename` text NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `question`
--

CREATE TABLE `question` (
  `questionid` int(11) NOT NULL,
  `studentnumber` int(11) NOT NULL,
  `question` text NOT NULL,
  `teachernumber` int(11) DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `jugyoid` int(11) NOT NULL,
  `questiontime` datetime NOT NULL,
  `answertime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `teacheraccount`
--

CREATE TABLE `teacheraccount` (
  `teachernumber` int(200) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `teacheraccount`
--

INSERT INTO `teacheraccount` (`teachernumber`, `username`, `password`, `time`) VALUES
(10633, 'テスト教授', '55b45c37f8298ead3671fe46f9a6885963c4c2b6', '2022-05-17 23:47:45'),
(10634, 'テスト教授2', '55b45c37f8298ead3671fe46f9a6885963c4c2b6', '2022-08-23 12:56:04');

-- --------------------------------------------------------

--
-- テーブルの構造 `video`
--

CREATE TABLE `video` (
  `jugyoid` int(11) NOT NULL,
  `title` text NOT NULL,
  `grade` int(200) NOT NULL,
  `gakubu` text NOT NULL,
  `teachernumber` int(11) NOT NULL,
  `videopass` text NOT NULL,
  `samune` text NOT NULL,
  `kadai` text NOT NULL,
  `kadaitext` text NOT NULL,
  `simekiri` datetime DEFAULT NULL,
  `time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`studentnumber`);

--
-- テーブルのインデックス `anketo`
--
ALTER TABLE `anketo`
  ADD PRIMARY KEY (`anketoid`);

--
-- テーブルのインデックス `jukodata`
--
ALTER TABLE `jukodata`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `kadai`
--
ALTER TABLE `kadai`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`questionid`);

--
-- テーブルのインデックス `teacheraccount`
--
ALTER TABLE `teacheraccount`
  ADD PRIMARY KEY (`teachernumber`);

--
-- テーブルのインデックス `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`jugyoid`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `anketo`
--
ALTER TABLE `anketo`
  MODIFY `anketoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- テーブルの AUTO_INCREMENT `jukodata`
--
ALTER TABLE `jukodata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- テーブルの AUTO_INCREMENT `kadai`
--
ALTER TABLE `kadai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- テーブルの AUTO_INCREMENT `question`
--
ALTER TABLE `question`
  MODIFY `questionid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- テーブルの AUTO_INCREMENT `video`
--
ALTER TABLE `video`
  MODIFY `jugyoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
