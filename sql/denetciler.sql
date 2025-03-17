-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 07 May 2024, 16:44:40
-- Sunucu sürümü: 8.0.35
-- PHP Sürümü: 8.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `alimentc_nazdys`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `denetciler`
--

DROP TABLE IF EXISTS `denetciler`;
CREATE TABLE `denetciler` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `denetci` varchar(75) DEFAULT NULL,
  `ea` text CHARACTER SET latin5 COLLATE latin5_turkish_ci,
  `nace` text,
  `kategori` text CHARACTER SET latin5 COLLATE latin5_turkish_ci,
  `kategorioic` text,
  `kategoribg` text CHARACTER SET latin5 COLLATE latin5_turkish_ci,
  `teknikalan` varchar(75) CHARACTER SET latin5 COLLATE latin5_turkish_ci DEFAULT NULL,
  `komiteea9` varchar(250) DEFAULT NULL,
  `komiteea14` varchar(250) DEFAULT NULL,
  `komiteea45` varchar(250) CHARACTER SET latin5 COLLATE latin5_turkish_ci DEFAULT NULL,
  `komiteea50` varchar(250) CHARACTER SET latin5 COLLATE latin5_turkish_ci DEFAULT NULL,
  `atama9001` varchar(50) DEFAULT NULL,
  `atama14001` varchar(50) DEFAULT NULL,
  `atama22000` varchar(50) DEFAULT NULL,
  `atama27001` varchar(75) CHARACTER SET latin5 COLLATE latin5_turkish_ci DEFAULT NULL,
  `atama45001` varchar(75) CHARACTER SET latin5 COLLATE latin5_turkish_ci DEFAULT NULL,
  `atama50001` varchar(75) CHARACTER SET latin5 COLLATE latin5_turkish_ci DEFAULT NULL,
  `atamaOicsmiic` varchar(75) DEFAULT NULL,
  `atamaOicsmiic6` varchar(75) DEFAULT NULL,
  `atamaOicsmiic9` varchar(75) DEFAULT NULL,
  `atamaOicsmiic171` varchar(75) DEFAULT NULL,
  `atamaOicsmiic24` varchar(75) DEFAULT NULL,
  `iku` varchar(75) DEFAULT NULL,
  `kararkomite` int NOT NULL DEFAULT '0',
  `is_active` int NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin5;

--
-- Tablo döküm verisi `denetciler`
--

INSERT INTO `denetciler` (`id`, `uid`, `denetci`, `ea`, `nace`, `kategori`, `kategorioic`, `kategoribg`, `teknikalan`, `komiteea9`, `komiteea14`, `komiteea45`, `komiteea50`, `atama9001`, `atama14001`, `atama22000`, `atama27001`, `atama45001`, `atama50001`, `atamaOicsmiic`, `atamaOicsmiic6`, `atamaOicsmiic9`, `atamaOicsmiic171`, `atamaOicsmiic24`, `iku`, `kararkomite`, `is_active`) VALUES
(1, 0, 'Atilla YILDIRIM', '03, 29, 30', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11.0, 12.0, 45.1, 45.2, 45.3, 45.4, 46.1, 46.2, 46.3, 46.4, 46.5, 46.6, 46.7, 46.9, 47.1, 47.2, 47.3, 47.4, 47.5, 47.6, 47.7, 47.8, 47.9, 55.1, 56.1, 56.2', 'C.01,C.02,C.03,C.04,E.01,F.01,G.01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(2, 0, 'Cem FİDAN', '03, 29, 30', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0, 45.1, 45.2, 45.3, 45.4, 46.1, 46.2, 46.3, 47.1, 47.2, 55.1, 55.2, 55.3, 55.9, 56.1, 56.2, 56.3', 'C.01,C.02,C.03,C.04,E.01,F.01,F.02,G.01,G.02,I.01', 'CI, CII, CIII, CIV, EI, GI', NULL, NULL, '1,32,33,35,36,37', '32,33,35,36,37', '32,33,35,36,37', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(3, 0, 'Şah İsmail KAYA', '03, 07, 12, 29, 31, 30', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0, 17.2, 20.3, 20.4, 45.1, 45.2, 45.3, 45.4, 46.1, 46.2, 46.3, 46.4, 46.5, 46.6, 46.7, 46.9, 47.1, 47.2, 47.3, 47.4, 47.5, 47.6, 47.7, 47.8, 47.9, 49.3, 49.4,52.1, 52.2, 55.1, 56.1, 56.2', 'C.01,C.02,C.03,C.04,E.01,F.01,F.02,G.01,G.02,K.01,I.01', 'CI,CII,CIII,CIV,EI,GII,II,KI,HIII', NULL, NULL, '1,8,9,10,24,25,26,32,33,35,36,37,39', '8,9,10,13,14,17,24,25,26,32,33,35,36,37,39', '8,9,10,13,14,15,16,17,24,25,26,32,33,35,36,37,39', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(4, 2, 'Volkan ÖZÇELİK', '03, 24, 29, 30, 35, 39', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0, 38.1, 38.2, 38.3, 46.1, 46.3, 47.1, 47.2, 47.7, 47.9, 55.1, 55.2, 55.3, 55.9, 56.1, 56.2, 56.3, 82.9', 'C.01,C.02,C.03,C.04,E.01,F.01,F.02,G.01,G.02,I.01,K.01', 'CI,CII,CIII,CIV,EI,GII,HIII,II', 'B.01', NULL, '1,31,32,33,36,37', '31,32,33,36,37', '26,27,31,32,33,36,37', '26,27', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 0, 1),
(92, 0, 'Serkan KILIÇ', '28,34 ', '41.2, 43.2,71.1,71.2,72.1,72.2,74.1,74.9', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(5, 0, 'Mustafa GÖLLÜ', '03,27,29,30', '10.1,10.2,10.3,10.4,10.5,10.6,10.7,10.8,10.9,11,11.0,12,12.0,36.0,45.1,45.2,45.3,45.4,46.1,46.2,46.3,46.4,46.5,46.6,46.7,46.9,47.1,47.2,55.1,56.1,56.2', 'C.01,C.04,E.01,I.01', 'CI,CII,CIII,CIV,HIII', NULL, NULL, '1,25,26,32,33,35,36,37', '25,26,32,33,35,36,37', '25,26,32,33,35,36,37', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(7, 1, 'Özcan ARSLAN', '15, 16, 33 ', '23.1, 23.3, 23.5, 23.6, 23.7, 62.0', '', NULL, NULL, NULL, '2,29,32,33,35,36,37', '7,10,12,13,14,15,17,29,32,33,35,36,37', '7,10,12,13,14,17,29,32,35,36,37', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(10, 0, 'Abdullah AYDIN', '36,37', '84.1,84.2,84.3,85.1,85.2,85.3,85.4,85.5,85.6', '', NULL, NULL, NULL, '29,32,33,35', '29,32,33,35', '29, 32, 33, 36', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(11, 0, 'Yusuf TATAR', '04', '13.1,13.2,13.3,13.9,14.1,14.2,14.3', '', NULL, NULL, NULL, '5,6,14,23', '6,23', '6,23', NULL, 'Başdenetçi', '', '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(12, 0, 'Gülten AYDIN', '36,37', '84.1,84.2,84.3,85.1,85.2,85.3,85.4,85.5,85.6', '', NULL, NULL, NULL, '29,32,33,35', '29,32,33,35', '29,32,33,35', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(13, 0, 'Fatma Kuzudişli TORUN', '01,03', '01.1,01.2,01.3,03.1,03.2,10.9', 'C.02,D.01', NULL, NULL, NULL, '30', '30', '30', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(14, 0, 'Emine POLAT ÖZÇELİK', '05,23,39', '15.2,31.0,32.5,96.0,38.1, 79.1', '', NULL, NULL, NULL, '4,6,14,24,31', '6,24,31', '4,6,24,31', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(15, 0, 'Erhan Özer UZUN', '06,23 ', '16.1,16.2,31.0,32.5', '', NULL, NULL, NULL, '4,5,14', '4,5', '4,5', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(16, 0, 'Emrah KARACAN', '06,14 ', '16.1,16.2,22.2', '', NULL, NULL, NULL, '4,5,23', '7,10,12,13,17,23', '4,5,7,10,12,13,15,16,17,23', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(17, 0, 'Ömer Uğur SAKABAŞI', '14 ', '22.2', '', NULL, NULL, NULL, '4,5,6,23', '7,10,12,13,17', '7,10,12,13,15,17', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(163, 0, 'Dünya TEMİZ', '03, 29, 30, 35 ', '10.1, 10.2, 10.3, 10.4, 10.5,10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0,46.3,46.4, 47.1, 47.7,47.9,56.1, 56.2, 82.9', 'C.01,C.02,C.03,C.04,E.01,F.01,F.02', 'CI,CII,CIII,CIV,EI,HIII,II', NULL, NULL, '1,32,33,35,36,37', '32,33,35,36,37', '32,33,36,37', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(169, 0, 'Mervan ZİREK', '05,24,34,39', '15.2,38.1,38.2,38.3,71.1,71.2,72.1,72.2,74.1,74.9', '', NULL, NULL, NULL, '31', '31', '31', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(25, 0, 'Sevtap SÜMEN', '01,06', '01.1,01.2,01.3,03.1,03.2,16.1,16.2', 'C.02,C.04,D.01', NULL, NULL, NULL, '3,30', '3,30', NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(26, 0, 'Çiğdem DERYAN', '31,32,35', '49.3,49.4,66.1,69.2,77.1,81.2,81.3,78.1', '', NULL, NULL, NULL, '29,33,36,37', '29,33,36,37', '29,33,36,37', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(28, 0, 'Umut SALCAN', '08,09,29,35', '18.1, 46.1, 46.4,46.9, 47.6, 58.1, 73.1', '', NULL, NULL, NULL, '7,32,33,35,36', '7,32,33,35,36', '7,32,33,35,36', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(33, 0, 'Hüseyin ÖKSÜZLER', '19,22,25,29,31', '26.1,26.4,26.5,27.1,27.2,27.3,27.4,27.5,27.9,29.2,29.3,30.2,35.11,45.3,47.4,49.3,49.4,52.1,52.2', '', NULL, NULL, NULL, '17,18,20,24,32,33,35,36,37,39', '17,18,20,21,24,32,33,35,36,37,39', '17,18,20,21,24,32,33,35,36,37,39', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(43, 0, 'Coşkun TÜRK', '16, 28,34 ', '23.5, 23.6, 41.1, 41.2, 42.1, 42.2, 42.9, 43.1, 43.2, 43.3, 43.9,71.1', '', NULL, NULL, NULL, '2,15', '7,10,12,13,14,15,17', '7,10,12,13,14,15,17', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(45, 0, 'Cumali YILMAZ', '32,35', '66.1,66.2,66.3,68.1,68.2,68.3,69.2,77.1,82.9', '', NULL, NULL, NULL, '29,33,36,37', '29,33,36,37', '29,33,36,37', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(79, 0, 'Oğuzhan AKÇAKOYUN', '28,34 ', ' 41.2, 43.2,71.1,71.2,72.1,72.2,74.1,74.9', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(51, 0, 'Duygu CENGİZEROĞLU SALCAN', '08,09,35', '18.1,58.1,70.2,73.1', '', NULL, NULL, NULL, '7,29,32,33,36,37', '7,29,32,33,36,37', '7,29,32,33,36,37', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(174, 0, 'Hasan KAYA', '05,07,12,14,15,39', '15.2,17.2,20.1,20.2,20.3,20.4,20.5,20.6,22.1,22.2,23.3,96.0', NULL, NULL, NULL, NULL, '4,6,7,10,23,24,31', '4,6,7,10,13,17,23,24,31', '4,6,7,10,13,17,23,24,31', NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(63, 0, 'Seda YILDIRIM', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(89, 0, 'Demo User', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(106, 0, 'Bülent ÖZKARTAL', '03, 30', '10.1, 10.3, 10.8, 56.1, 56.2', 'C.01,C.02,C.04,E.01', 'CI,CII,CIV,HIII', NULL, NULL, '1', '-', NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(108, 0, 'Yelda YÜNCÜLER', '03, 29, 30, 35 ', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0, 46.1, 46.3, 47.1, 47.2, 55.1, 55.2, 56.1, 56.2, 82.9', 'C.01,C.02,C.03,C.04,E.01,F.01,F.02,G.01,G.02,I.01,K.01', 'CI,CII,CIII,CIV,EI,GI,KI,HIII', NULL, NULL, '1,32,33,35,36,37', '32,33,35,36,37', '32,33,35,36,37', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, 0, 1),
(110, 0, 'İbrahim CENGİZ', '14, 17, 23, 31, 32, 35', '22.2, 24.1, 24.2, 24.3, 24.4, 24.5, 25.1, 25.2, 25.3, 25.4, 25.5, 25.6, 25.7, 25.9, 32.3, 32.9, 49.3, 49.4, 52.2, 53.2, 81.2, 81.3, 77.3, 78.1, 80.1', '', NULL, NULL, NULL, '4,5,6,18,19,20,22,24,29,33,36,37,39', '6,7,10,12,13,24,29,33,36,37,39', '4,5,6,7,10,12,13,15,16,24,29,33,36,37,39', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(111, 0, 'Ali ÜNAL', '17,18,19,22,28,29,34', '24.1,24.2,24.3,24.4,25.1,25.2,25.3,25.4,25.5,25.6,25.7,25.9,28.1,28.2,28.3,28.4,28.9,30.2,30.4,33.12,33.2,27.1,29.2,29.3,30.9,41.2,43.2,46.7,46.9,71.2', '', NULL, NULL, NULL, '20,32,33,34,35,36,37', '20,21,32,33,34,35,36,37', '7,10,12,13,14,15,16,19,20,21', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(167, 0, 'Aslan ASLAN', '17, 18, 19, 25, 28, 34, 35, 37', '24.1, 24.2, 24.3, 24.4, 24.5, 25.1, 25.2, 25.3, 25.4, 25.5, 25.6, 25.7, 25.9, 26.1, 25.6, 25.6, 26.4, 26.5, 26.6, 26.7, 26.8, 27.1, 27.2, 27.3, 27.4, 27.5, 27.9, 28.1, 28.2, 28.3, 28.4, 28.9, 30.4, 33.12, 33.13, 33.14, 33.2, 35.11, 43.1, 43.2, 43.3, 71.2, 78.1, 78.2, 78.3, 80.1, 80.2, 81.2, 85.4, 95.1', '', NULL, 'A.01,B.01,C.01,D.01,D.02,D.03,D.04,D.05,D.06,D.07', 'A, C, D, H', '7,10,20,22,29,32,33,36', '7,10,13,14,20,22,29,32,33,36', '7,10,13,14,15,16,20,22,26,27,29,32,33,36', NULL, 'Başdenetçi', 'Başdenetçi', '', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(118, 0, 'Sinem ACERÇELİK', '17, 18, 19, 22, 23, 28, 29, 31, 32, 35', '25.2, 25.5, 25.6,25.7, 25.9, 26.1, 26.2, 26.3, 26.4, 26.5, 26.6, 26.7, 26.8, 27.1, 27.2, 27.3, 27.4, 27.5, 27.9, 28.1, 28.2, 28.3, 28.4, 28.9, 29.1, 29.2, 29.3, 30.2, 32.9, 33.1, 43.2, 45.1, 45.2, 45.3, 45.4, 46.7, 49.3, 49.4, 52.2, 53.2, 77.3, 78.1, 81.2, 81.3, 95.1', '', NULL, NULL, NULL, '6,14,20,24,29,33,34,36,37', '6,20,21,24,29,33,34,36,37,39', '4,5,6,20,21,24,29,33,34,36,37,39', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(135, 0, 'Onur SALCAN', '16, 28, 34', '23.5, 23.6, 41.1, 41.2, 42.1, 42.2, 42.9, 43.1, 43.2, 43.3, 43.9,71.1', '', NULL, NULL, NULL, '2,15', '7,10,12,13,14,15,17', '7,10,12,13,15,17', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(153, 0, 'Teksin EKİZ', NULL, NULL, '', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, 'Gözlemci', 'Gözlemci', 'Gözlemci', NULL, NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, 0, 1),
(240, 0, 'Melike YILDIRIM', NULL, NULL, 'E.01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Gözlemci', 'Gözlemci', 'Başdenetçi', NULL, 'Gözlemci', '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(164, 0, 'Mehmet Kadir YILMAZ', NULL, NULL, '', NULL, NULL, 'A, C, D, H', NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(168, 0, 'Ilgaz YILDIZ', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(170, 0, 'Derya ÇİFTÇİ', '07,12,14,15', '17.2,20.1,20.2,20.3,20.4,20.5,20.6,22.1,22.2,23.9', '', NULL, NULL, NULL, '7,10', '7,10,13,14,17', '4,6,7,10,13,15,16,17,23', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(171, 0, 'Onur AYDIN', '03, 29, 30', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7,10.8, 10.9, 11, 11.0, 12, 12.0, 46.3, 46.4, 47.1, 47.7, 47.9, 56.1, 56.2, 56.3', 'C.01,C.02,C.03,C.04,E.01,F.01,F.02,G.01,G.02', 'CI,CII,CIII,CIV,EI,GI', 'A.01,B.01,C.01,D.01,D.02,D.03,D.04,D.05,D.06,D.07', NULL, '1,32,33,35,36,37', '32,33,35,36,37', '32,33,35,36,37', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(172, 0, 'Ali Can ULUSOY', '18,28 ', ' 28.2,43.2', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(175, 0, 'Abdulkadir CAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(176, 0, 'Damla SÖNMEZ', NULL, NULL, 'C.01,C.02,C.03,C.04', 'CI,CII,CIII,CIV,EI,HIII', NULL, NULL, '', NULL, NULL, NULL, '', '', 'Başdenetçi', NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(239, 0, 'Mehmet Hulusi ADA', NULL, NULL, '', 'CI, CII, CIII, CIV, EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, '', 0, 1),
(178, 0, 'Can Oral BUGAN', NULL, NULL, '', 'CI', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, 'Veteriner', 0, 1),
(179, 0, 'Üzeyir CAN', NULL, NULL, '', 'CI', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, 'Veteriner', 0, 1),
(194, 0, 'Ahmet TEKE', '25', '35.1, 35.11', '', NULL, NULL, NULL, '-', '-', NULL, '26,27', NULL, NULL, '', NULL, '', 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(195, 0, 'Yusuf TOPAL', '25', '35.1,35.11', '', NULL, NULL, 'A, C, D, H', '26,27', '26,27', '26,27', '26,27', 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(197, 0, 'Osman YÜCE', '25', '35.11', '', NULL, NULL, NULL, NULL, NULL, NULL, '', 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(199, 0, 'Siyami ASLAN', NULL, NULL, '', 'CI,CII, CIV,GII,HIII,KI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', NULL, 0, 1),
(200, 0, 'Seda Gül BAYGÜL', '03, 29, 30', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0, 55.1, 55.2, 55.3, 55.9, 56.1, 56.2, 56.3', 'C.01,C.02,C.03,C.04,E.01', 'CI,CII,CIII,CIV,EI', NULL, NULL, '', NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, 0, 1),
(208, 0, 'Muhammed YAZAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', NULL, NULL, 'Başdenetçi', 0, 1),
(207, 0, 'Fatih ÖKTEM', '33 ', ' 62.0', '', NULL, NULL, NULL, '2,29,32,33,35,36,37', '29,32,33,35,36,37', '7,10,12,13,14,17,29,32,35,36,37', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(209, 0, 'Nisa CENGİZ', '35', '82.2', '', NULL, NULL, NULL, '7,29,32,33,36,37', '7,29,32,33,36,37', '7,29,32,33,36,37', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(210, 0, 'Abdulbaki BEKTAŞ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(211, 0, 'Hakkı AĞIRBAŞ', NULL, NULL, '', 'CI,GII,KI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', NULL, 0, 1),
(212, 0, 'Fazlı ÇEKEMATMA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(213, 0, 'Hakan Yiğit ERTUĞ', '16, 28', '23.5, 23.6, 41.1, 41.2, 42.1, 42.2, 42.9, 43.1, 43.2, 43.3, 43.9', '', NULL, NULL, NULL, '2,15', '7,10,12,13,14,15,17', '7,10,12,13,15,17', NULL, 'Teknik Uzman', 'Teknik Uzman', '', NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(214, 0, 'Sait Kaya KARS', NULL, NULL, NULL, NULL, '', 'A, C, D, H', NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(217, 0, 'Serdar BİÇER', NULL, NULL, NULL, NULL, 'A.01,B.01,C.01,D.01,D.02,D.03,D.04,D.05,D.06,D.07', NULL, NULL, NULL, NULL, NULL, '', '', '', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(215, 0, 'Ali Rıza TALİ', NULL, NULL, NULL, NULL, 'A.01,B.01,C.01,D.01,D.02,D.03,D.04,D.05,D.06,D.07', NULL, NULL, NULL, NULL, NULL, '', '', '', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(216, 0, 'Güçhan ERYILMAZ', '04, 05, 06, 07, 14, 15, 17, 18, 19, 23, 34, 36', '13.1, 13.2, 13.3, 13.9, 14.1, 14.2, 14.3, 15.2, 16.1, 16.2, 17.1, 17.2, 22.1, 22.2, 23.1, 24.1, 24.2, 24.3, 24.4, 24.5, 25.1, 25.2, 25.6, 25.7, 25.9, 27.1, 28.1, 28.2, 28.3, 28.4, 28.9, 31.0, 32.4, 32.5, 32.9, 33.1, 45.1, 45.2, 45.3, 45.4, 71.1, 71.2, 72.1, 74.9, 84.1, 84.2, 84.3', '', NULL, NULL, NULL, '7,32,33,35,36', '7,32,33,35,36', '7,32,33,35,36', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(219, 0, 'Osman Levent DEMİRCİ', NULL, NULL, NULL, NULL, '', 'A, C, D, H', NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(218, 0, 'Okan İŞGÜZAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(220, 0, 'Ömer GÜN', NULL, NULL, NULL, NULL, '', 'A, C, D, H', NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(221, 0, 'Ekrem MUSAOĞLU', NULL, NULL, NULL, NULL, 'A.01,B.01,C.01,D.01,D.02,D.03,D.04,D.05,D.06,D.07', NULL, NULL, NULL, NULL, NULL, '', '', '', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(222, 0, 'Tuncer SARACIK', NULL, NULL, NULL, NULL, '', 'A, C, D, H', NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(223, 0, 'GÖKŞEN PINAR GÖKPINAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Gözlemci', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(224, 0, 'Cüneyt KAYA', NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Gözlemci', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(225, 0, 'Ayşe BEKTAŞ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(226, 0, 'Özgün GÖLLÜ', '30', '55.1, 55.2, 55.3, 56.1, 56.2,56.3', 'E.01', 'HIII', NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(227, 0, 'Alper DEVECİ', '30', '55.1, 55.2, 55.3, 56.1, 56.2,56.3', 'E.01', 'HIII', NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(228, 0, 'Nilgün İŞHANLIOĞLU', '03, 29, 30', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0, 55.1, 55.2, 55.3, 55.9, 56.1, 56.2, 56.3', 'C.01,C.02,C.03,C.04,E.01', NULL, NULL, NULL, '', NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(229, 0, 'Dolunay YALÇIN', '24, 39 ', '38.1,38.2,38.3', NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(230, 0, 'Ali ÇELİKER', '35', '82.3', '', NULL, NULL, NULL, '7,29,32,33,36,37', '7,29,32,33,36,37', '7,29,32,33,36,37', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(231, 0, 'Süleyman Tahir SÖNMEZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(232, 0, 'Belkıs ERTÜRK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Gözlemci', 'Gözlemci', 'Gözlemci', NULL, NULL, 'Gözlemci', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(233, 0, 'Onur ERBAY', NULL, NULL, NULL, NULL, 'A.01,B.01,C.01,D.01,D.02,D.03,D.04,D.05,D.06,D.07', NULL, NULL, NULL, NULL, NULL, '', '', '', 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(234, 0, 'Çetin BÜYÜKNİSAN', '03,30', '10.1,10.2,10.3,10.4,10.5,10.6,10.7,10.8,10.9,11,11.0,12,12.0,55.1,56.1,56.2', 'C.01,C.04,E.01', NULL, NULL, NULL, '1,25,26,32,33,35,36,37', '25,26,32,33,35,36,37', '25,26,32,33,35,36,37', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(235, 0, 'Zeliha ÖZYILMAZ', '03,30', '10.1,10.2,10.3,10.4,10.5,10.6,10.7,10.8,10.9,11,11.0,12,12.0,55.1,56.1,56.2', 'C.01,C.04,E.01', NULL, NULL, NULL, '1,25,26,32,33,35,36,37', '25,26,32,33,35,36,37', '25,26,32,33,35,36,37', NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, 0, 1),
(236, 0, 'Aycan KAYA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(237, 0, 'Arif ZİREK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(238, 0, 'Gazi UĞURLU', NULL, NULL, '', 'CI, CII, CIII, CIV, EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, '', 0, 1),
(241, 0, 'Ahmet YILDIRIM', NULL, NULL, NULL, NULL, 'A.01,B.01,C.01,D.01,D.02,D.03,D.04,D.05,D.06,D.07', NULL, NULL, NULL, NULL, NULL, 'Aday Denetçi', NULL, '', 'Başdenetçi', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(242, 0, 'Muhittin Onur TÜRKOĞLU', NULL, NULL, '', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, 0, 1),
(243, 0, 'Açelya BALABAN', '03 ', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0', 'C.01,C.02,C.03,C.04,E.01', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, 0, 1),
(244, 0, 'Güven KISAOĞLU', '03 ', '10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11, 11.0, 12, 12.0', 'C.01,C.02,C.03,C.04,E.01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', 'Başdenetçi', NULL, 'Başdenetçi', NULL, '', NULL, NULL, NULL, NULL, NULL, 0, 1),
(245, 0, 'Dara DİKEN', '28 ', ' 41.1, 41.2, 42.1, 42.2, 42.9, 43.1, 43.2, 43.3, 43.9', '', NULL, NULL, NULL, NULL, '7,10,12,13,14,15,17', '7,10,12,13,14,15,17', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(246, 0, 'Çağla SÖNMEZ', '28 ', ' 41.1, 41.2, 42.1, 42.2, 42.9, 43.1, 43.2, 43.3, 43.9', '', NULL, NULL, NULL, NULL, '7,10,12,13,14,15,17', '7,10,12,13,14,15,17', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(247, 0, 'Sami İlker CANAT', NULL, NULL, '', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, 0, 1),
(248, 0, 'Şervan Ramazan ABA', '28 ', ' 41.1, 41.2, 42.1, 42.2, 42.9, 43.1, 43.2, 43.3, 43.9', '', NULL, NULL, NULL, NULL, '7,10,12,13,14,15,17', '7,10,12,13,14,15,17', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(249, 0, 'Sibel Şebnem ERDOĞAN', '28 ', ' 41.1, 41.2, 42.1, 42.2, 42.9, 43.1, 43.2, 43.3, 43.9', '', NULL, NULL, NULL, NULL, '7,10,12,13,14,15,17', '7,10,12,13,14,15,17', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(250, 0, 'Elif DEMİRDAŞ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(251, 0, 'Kemal SÖNMEZ', NULL, NULL, '', 'CI', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', NULL, NULL, NULL, NULL, 'Veteriner', 0, 1),
(252, 0, 'Özlem AKSOY', '29', '46.4,47.5,47.7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(253, 0, 'Ekrem POLAT', '29', '46.4,47.5,47.7', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(254, 0, 'Sena ÇETİNBAŞ', NULL, NULL, '', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, 0, 1),
(256, 0, 'Rumeysa BEYAZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(259, 0, 'Gizem DOLGUN', NULL, NULL, '', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, 0, 1),
(257, 0, 'Esengül ÖZCAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Teknik Uzman', 'Başdenetçi', 0, 1),
(258, 0, 'Hasan ERYILMAZ', NULL, NULL, '', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, 0, 1),
(260, 0, 'Meltem TÜZÜN', NULL, NULL, '', 'CI,CII,CIII,CIV,EI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Başdenetçi', 'Başdenetçi', NULL, NULL, NULL, NULL, 0, 1),
(261, 0, 'Nazlı Hilal SAĞLAM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Gözlemci', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1),
(263, 0, 'Sermin GAZEL', '36,37', '84.1,84.2,84.3,85.1,85.2,85.3,85.4,85.5,85.6', '', NULL, NULL, NULL, '29,32,33,35', '29,32,33,35', '29, 32, 33, 36', NULL, 'Başdenetçi', 'Başdenetçi', '', NULL, 'Başdenetçi', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `denetciler`
--
ALTER TABLE `denetciler`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `denetciler`
--
ALTER TABLE `denetciler`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
