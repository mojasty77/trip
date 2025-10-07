-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2025 at 11:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trip_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE `people` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `people`
--

INSERT INTO `people` (`id`, `full_name`, `position`) VALUES
(1, 'นายวรรณลภย์ กรีฑาพันธ์', 'นักเทคโนโลยีสารสนเทศ'),
(6, 'นางสาวผกาวดี สุขศิริสวัสดิกุล', 'ผอ.สกร.ประจำ จว.มส.'),
(7, 'นายธีระพงค์ ชุมพล', 'พนักงานขับรถยนต์');

-- --------------------------------------------------------

--
-- Table structure for table `travelers`
--

CREATE TABLE `travelers` (
  `id` int(11) NOT NULL,
  `trip_id` varchar(100) DEFAULT NULL,
  `traveler_index` tinyint(4) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travelers`
--

INSERT INTO `travelers` (`id`, `trip_id`, `traveler_index`, `full_name`, `position`) VALUES
(1, '4', 1, 'นายวรรณลภย์ กรีฑาพันธ์', 'นักเทคโนโลยีสารสนเทศ'),
(2, '4', 2, 'นายวรรณลภย์ อีกคน', 'นักคอมอีกที'),
(3, '5', 1, 'นายวรรณลภย์ กรีฑาพันธ์', 'นักเทคโนโลยีสารสนเทศ'),
(4, '6', 1, 'นายวรรณลภย์ กรีฑาพันธ์', 'นักเทคโนโลยีสารสนเทศ'),
(5, '7', 1, 'นายวรรณลภย์ กรีฑาพันธ์', 'นักเทคโนโลยีสารสนเทศ'),
(6, '8', 1, 'นายวรรณลภย์ กรีฑาพันธ์', 'นักเทคโนโลยีสารสนเทศ'),
(7, '9', 1, 'นายวรรณลภย์ กรีฑาพันธ์', 'นักเทคโนโลยีสารสนเทศ'),
(8, '9', 2, 'นายวรรณลภย์ อีกคน', 'นักคอมอีกที'),
(9, '00', 1, 'นายวรรณลภย์ อีกคน', 'นักคอมอีกที'),
(10, '00', 2, 'นายวรรณลภย์ อีกคน', 'นักคอมอีกที'),
(11, '000/2568', 1, 'นางสาวผกาวดี สุขศิริสวัสดิกุล', 'ผอ.สกร.ประจำ จว.มส.'),
(12, '000/2568', 2, 'นายธีระพงค์ ชุมพล', 'พนักงานขับรถยนต์');

-- --------------------------------------------------------

--
-- Table structure for table `trip_requests`
--

CREATE TABLE `trip_requests` (
  `trip_id` varchar(50) NOT NULL,
  `person_count` int(11) NOT NULL,
  `full_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`full_names`)),
  `positions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`positions`)),
  `purpose` text NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `duration_days` int(11) NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `transport` varchar(50) NOT NULL,
  `budget_type` varchar(100) DEFAULT NULL,
  `project` varchar(100) DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_requests`
--

INSERT INTO `trip_requests` (`trip_id`, `person_count`, `full_names`, `positions`, `purpose`, `start_datetime`, `end_datetime`, `duration_days`, `duration_hours`, `transport`, `budget_type`, `project`, `estimated_cost`) VALUES
('00', 2, '[\"นายวรรณลภย์ อีกคน\",\"นายวรรณลภย์ อีกคน\"]', '[\"นักคอมอีกที\",\"นักคอมอีกที\"]', '000', '2025-10-23 18:05:00', '2025-11-08 20:07:00', 16, 3, 'รถรับจ้าง', '00', '00', 0.00),
('000/2568', 2, '[\"นางสาวผกาวดี สุขศิริสวัสดิกุล\",\"นายธีระพงค์ ชุมพล\"]', '[\"ผอ.สกร.ประจำ จว.มส.\",\"พนักงานขับรถยนต์\"]', 'ไปเที่ยว', '2025-10-09 15:06:00', '2025-10-11 21:12:00', 2, 6, 'รถยนต์ราชการ', 'ทดลอง', 'ไม่รู้ครับ', 1000000.00),
('1', 1, '[\"นายวรรณลภย์ อีกคน\"]', '[\"นักคอมอีกที\"]', '1', '2025-10-07 01:01:00', '2025-10-07 13:11:00', 0, 12, '0', '1', '1', 1.00),
('2', 1, '[\"นางสาว B\"]', '[\"นักวิชาการ\"]', 'กกกไปไหนก็ได้โตละ', '2025-10-01 06:04:00', '2025-10-06 18:05:00', 5, 12, '0', 'หมวดไหนก็ได้', 'โตแล้วเหมือนกัน', 9987.00),
('3', 2, '[\"นายวรรณลภย์ กรีฑาพันธ์\",\"\"]', '[\"\",\"\"]', 'ประชุมขับเคลื่อนการดำเนินงานจัดทำโปรแกรมฐานข้อมูลบุคลากรของสำนักงานส่งเสริมการเรียนรู้ประจำจังหวัดแม่ฮ่องสอน  ณ สำนักงานส่งเสริมการเรียนรู้ประจำจังหวัดแม่ฮ่องสอน ', '2025-10-08 08:35:00', '2025-10-10 21:20:00', 2, 12, '0', 'อุดหนุน', 'จัดการเรียการสอน 163', 14514.00),
('4', 2, '[\"นายวรรณลภย์ กรีฑาพันธ์\",\"นายวรรณลภย์ อีกคน\"]', '[\"นักเทคโนโลยีสารสนเทศ\",\"นักคอมอีกที\"]', 'เดินทางไปราชการ', '2568-11-10 08:00:00', '2568-11-14 16:00:00', 4, 8, '0', 'งบพัฒนาคุณภาพผู้เรียน', 'โครงการจัดอบรมพัฒนาบุคลากร', 32000.00),
('5', 1, '[\"นายวรรณลภย์ กรีฑาพันธ์\"]', '[\"นักเทคโนโลยีสารสนเทศ\"]', 'ไปประชุมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมมม', '2025-10-07 20:08:00', '2025-10-23 20:08:00', 16, 0, '0', 'อุกหนุน', 'อุึเ', 1500.00),
('6', 1, '[\"นายวรรณลภย์ กรีฑาพันธ์\"]', '[\"นักเทคโนโลยีสารสนเทศ\"]', 'กหฟกฟหกฟหกฟหก', '2025-10-07 04:39:00', '2025-10-07 16:39:00', 0, 12, '0', 'ก', 'ก', 4242.00),
('7', 1, '[\"นายวรรณลภย์ กรีฑาพันธ์\"]', '[\"นักเทคโนโลยีสารสนเทศ\"]', 'ไปประชุมรอิบที่ 159494 รอบลฃะฃ', '2025-10-07 04:46:00', '2025-10-07 18:48:00', 0, 14, '0', 'ดุดหนุนน', 'นอกระบบบ', 151651.00),
('8', 1, '[\"นายวรรณลภย์ กรีฑาพันธ์\"]', '[\"นักเทคโนโลยีสารสนเทศ\"]', 'ไปราชการ ทดสอบจ้า ประชุมทำอะไรอีกดีเอายาวๆ', '2025-10-07 03:55:00', '2025-10-07 04:58:00', 0, 1, '0', 'อถุด', 'ไนหนุน', 4949.00),
('9', 2, '[\"นายวรรณลภย์ กรีฑาพันธ์\",\"นายวรรณลภย์ อีกคน\"]', '[\"นักเทคโนโลยีสารสนเทศ\",\"นักคอมอีกที\"]', 'ต้องไปปรับระบบอีกว่าให้ เอามาทีละคน ห้ามซ้ำ แต่ขี้เกียจแล้วอะคแค่ไปหราชการก็แย่ 55555555555555555555', '2025-10-07 02:59:00', '2025-10-07 05:02:00', 0, 2, 'รถโดยสารประจำทาง', 'กกกก', 'กกกกก', 999.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `travelers`
--
ALTER TABLE `travelers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_requests`
--
ALTER TABLE `trip_requests`
  ADD PRIMARY KEY (`trip_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `travelers`
--
ALTER TABLE `travelers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
