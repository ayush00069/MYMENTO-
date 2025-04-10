-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2025 at 02:13 PM
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
-- Database: `mymento`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(10) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `email` varchar(20) NOT NULL,
  `phone` int(10) DEFAULT NULL,
  `password` int(8) NOT NULL,
  `secret_key` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `phone`, `password`, `secret_key`) VALUES
(1, 'monika', 'monapatel18r@gmail.c', 2147483647, 789456, 'MOHIT_2005');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `month` varchar(20) NOT NULL,
  `semester` int(11) NOT NULL,
  `section` varchar(10) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `month`, `semester`, `section`, `upload_date`, `file_path`) VALUES
(2, 'January', 1, 'A', '2025-02-20 06:26:47', 'uploads/1740032807_p1.csv');

-- --------------------------------------------------------

--
-- Table structure for table `clg_fees_receipts`
--

CREATE TABLE `clg_fees_receipts` (
  `id` int(11) NOT NULL,
  `enrollment_no` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `semester` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clg_fees_receipts`
--

INSERT INTO `clg_fees_receipts` (`id`, `enrollment_no`, `email`, `semester`, `file_path`, `status`, `upload_date`) VALUES
(3, '22BCA53986', 'patelaanshi97@gmail.com', 3, 'uploads/fees_receipts/1741507804_Screenshot (4).png', 'approved', '2025-03-09 08:10:04'),
(4, '22BCA53986', 'patelaanshi97@gmail.com', 4, 'uploads/fees_receipts/1741508188_Screenshot (2).png', 'approved', '2025-03-09 08:16:28'),
(5, '22BCA53986', 'patelaanshi97@gmail.com', 5, 'uploads/fees_receipts/1741511140_Screenshot (2).png', 'rejected', '2025-03-09 09:05:40'),
(6, '22BCA53986', 'patelaanshi97@gmail.com', 6, 'uploads/fees_receipts/1741511159_Screenshot (2).png', 'approved', '2025-03-09 09:05:59'),
(8, '22BCA53208', 'ishishah@gmail.com', 4, 'uploads/fees_receipts/1741539721_Screenshot (2).png', 'approved', '2025-03-09 17:02:01'),
(9, '22BCA53208', 'ishishah@gmail.com', 4, 'uploads/fees_receipts/1741539735_Screenshot (2).png', 'approved', '2025-03-09 17:02:15'),
(10, '22BCA53696969', 'ayushsingh54148@gmail.com', 1, 'uploads/fees_receipts/1743745647_778160.jpg', 'rejected', '2025-04-04 05:47:27'),
(11, '22BCA53696969', 'ayushsingh54148@gmail.com', 2, 'uploads/fees_receipts/1743745676_778160.jpg', 'rejected', '2025-04-04 05:47:56'),
(12, '22BCA53696969', 'ayushsingh54148@gmail.com', 3, 'uploads/fees_receipts/1743747585_dog-holding-camera-selfie.jpg', 'approved', '2025-04-04 06:19:45'),
(13, '22BCA53696969', 'ayushsingh54148@gmail.com', 4, 'uploads/fees_receipts/1743747609_778160.jpg', 'rejected', '2025-04-04 06:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `clg_marksheets`
--

CREATE TABLE `clg_marksheets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `enrollment_no` varchar(50) NOT NULL,
  `semester` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clg_marksheets`
--

INSERT INTO `clg_marksheets` (`id`, `email`, `enrollment_no`, `semester`, `file_path`, `status`, `upload_date`) VALUES
(22, 'ayushsingh5148@gmail.com', '22bca53170966', 3, 'uploads/marksheets/1742191101_Screenshot (2).png', 'rejected', '2025-03-17 05:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `school_marksheets`
--

CREATE TABLE `school_marksheets` (
  `id` int(11) NOT NULL,
  `enrollment_no` varchar(50) NOT NULL,
  `marksheet_10` varchar(255) DEFAULT NULL,
  `marksheet_12` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `upload_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_marksheets`
--

INSERT INTO `school_marksheets` (`id`, `enrollment_no`, `marksheet_10`, `marksheet_12`, `status`, `upload_time`) VALUES
(1, '22bca531700', 'uploads/1741254664_10th_Screenshot__2_.png', 'uploads/1741254664_12th_Screenshot__4_.png', 'approved', '2025-03-06 09:31:24'),
(3, '22BCA53986', 'uploads/1741507776_10th_Screenshot__3_.png', 'uploads/1741507776_12th_Screenshot__4_.png', 'approved', '2025-03-09 08:09:36'),
(4, '22BCA53208', 'uploads/1741514140_10th_Screenshot__1_.png', 'uploads/1741514140_12th_Screenshot__3_.png', 'approved', '2025-03-09 09:55:40'),
(6, '22BCA53696969', 'uploads/1743747796_10th_dog-holding-camera-selfie.jpg', 'uploads/1743747796_12th_OIP.jpeg', 'approved', '2025-04-04 05:21:39');

-- --------------------------------------------------------

--
-- Table structure for table `studentreg`
--

CREATE TABLE `studentreg` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studentreg`
--

INSERT INTO `studentreg` (`id`, `username`, `email`, `password`) VALUES
(7, 'Takshal', 'ayushsingh5148@gmail.com', '123456'),
(9, 'ayush', 'ayushsingh54148@gmail.com', '123456'),
(10, 'monika', 'monika18r@gmail.com', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `student_aadhar`
--

CREATE TABLE `student_aadhar` (
  `id` int(11) NOT NULL,
  `enrollment_no` varchar(20) DEFAULT NULL,
  `student_aadhar_image` varchar(255) NOT NULL,
  `father_aadhar_image` varchar(255) NOT NULL,
  `mother_aadhar_image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_aadhar`
--

INSERT INTO `student_aadhar` (`id`, `enrollment_no`, `student_aadhar_image`, `father_aadhar_image`, `mother_aadhar_image`, `status`) VALUES
(13, '22bca53170966', 'uploads/1742191015_student_Screenshot__1_.png', 'uploads/1742191015_father_Screenshot__2_.png', 'uploads/1742191015_mother_Screenshot__3_.png', 'approved'),
(14, '22BCA53696969', 'uploads/1743743911_student_dog-holding-camera-selfie.jpg', 'uploads/1743743911_father_OIP.jpeg', 'uploads/1743743911_mother_R.jpeg', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `student_profile`
--

CREATE TABLE `student_profile` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `enrollment_no` varchar(50) NOT NULL,
  `division` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `profile_photo` varchar(255) DEFAULT 'default.jpg',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profile`
--

INSERT INTO `student_profile` (`id`, `email`, `enrollment_no`, `division`, `dob`, `age`, `full_name`, `profile_photo`, `updated_at`) VALUES
(12, 'ayushsingh5148@gmail.com', '22bca53170966', 'A', '0000-00-00', 7, 'takshal darji', 'uploads/67d7b8f3da341_Screenshot (2).png', '2025-03-17 05:53:55'),
(13, 'ayushsingh54148@gmail.com', '22BCA53696969', 'A', '2019-02-27', 6, 'AYUSH SINGH', 'uploads/67ef6b47cbe84_OIP.jpeg', '2025-04-04 11:50:28');

-- --------------------------------------------------------

--
-- Table structure for table `teacherprofile`
--

CREATE TABLE `teacherprofile` (
  `teacher_id` int(10) NOT NULL,
  `full_name` varchar(30) DEFAULT NULL,
  `mentoringclass` varchar(8) DEFAULT NULL,
  `qualification` varchar(40) DEFAULT NULL,
  `profile_photo_path` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacherprofile`
--

INSERT INTO `teacherprofile` (`teacher_id`, `full_name`, `mentoringclass`, `qualification`, `profile_photo_path`) VALUES
(1, 'RAVAL MOHIT M.', '6A', 'MCA', 'uploads/67d270d8ac237_Screenshot (1).png'),
(2, 'monaaaa', '6A', 'bcaa', 'uploads/67c4c2ab4e4fa_Screenshot (1).png'),
(5, 'GEETA', '6B', 'bca', 'uploads/67cd4c2a4e3d3_Screenshot (3).png'),
(7, 'SHAH ISHI', '6A', 'PHYSYO', 'uploads/67cdc7e66d5d8_Screenshot (2).png'),
(8, 'AASTHA', '6C', 'BCA', 'uploads/67ef698ecabfc_778160.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `teacherreg`
--

CREATE TABLE `teacherreg` (
  `id` int(10) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacherreg`
--

INSERT INTO `teacherreg` (`id`, `username`, `email`, `password`) VALUES
(1, 'MOHIT', 'mohitraval804@gmail.com', 123456),
(2, 'monika', 'monapatel18r@gmail.com', 123456),
(3, 'Takshal', 'takshal1234@gmail.com', 123456),
(4, 'panic.boi_', 'hasvisompura79@gmail.com', 123456),
(5, 'GEETA', 'geetasingh1236@gmail.com', 123456),
(7, 'ISHI', 'ishishah@gmail.com', 123456),
(8, 'AASTHA', 'aastha123@gmail.com', 123456),
(9, 'ayush', 'ayushsingh5148@gmail.com', 123456);

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_files`
--

CREATE TABLE `uploaded_files` (
  `id` int(10) NOT NULL,
  `file_type` varchar(10) DEFAULT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clg_fees_receipts`
--
ALTER TABLE `clg_fees_receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clg_marksheets`
--
ALTER TABLE `clg_marksheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `school_marksheets`
--
ALTER TABLE `school_marksheets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_no` (`enrollment_no`);

--
-- Indexes for table `studentreg`
--
ALTER TABLE `studentreg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_aadhar`
--
ALTER TABLE `student_aadhar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_no` (`enrollment_no`);

--
-- Indexes for table `student_profile`
--
ALTER TABLE `student_profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `teacherprofile`
--
ALTER TABLE `teacherprofile`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacherreg`
--
ALTER TABLE `teacherreg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clg_fees_receipts`
--
ALTER TABLE `clg_fees_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `clg_marksheets`
--
ALTER TABLE `clg_marksheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `school_marksheets`
--
ALTER TABLE `school_marksheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `studentreg`
--
ALTER TABLE `studentreg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_aadhar`
--
ALTER TABLE `student_aadhar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_profile`
--
ALTER TABLE `student_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `teacherprofile`
--
ALTER TABLE `teacherprofile`
  MODIFY `teacher_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teacherreg`
--
ALTER TABLE `teacherreg`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clg_marksheets`
--
ALTER TABLE `clg_marksheets`
  ADD CONSTRAINT `clg_marksheets_ibfk_1` FOREIGN KEY (`email`) REFERENCES `student_profile` (`email`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
