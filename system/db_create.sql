-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2021 at 08:18 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `infm114`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `signature` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `teacherName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `courses`:
--

-- --------------------------------------------------------

--
-- Table structure for table `courseworks`
--

CREATE TABLE `courseworks` (
  `id` int(11) NOT NULL,
  `dueDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `courseworks`:
--

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `notes`:
--

-- --------------------------------------------------------

--
-- Table structure for table `student_courses`
--

CREATE TABLE `student_courses` (
  `id` int(11) NOT NULL,
  `fnumber` int(11) NOT NULL,
  `signature` varchar(20) NOT NULL,
  `dayOfWeek` tinyint(4) DEFAULT NULL,
  `week` tinyint(4) DEFAULT NULL,
  `timeStart` time DEFAULT NULL,
  `timeEnd` time DEFAULT NULL,
  `location` varchar(20) DEFAULT NULL,
  `noteId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `student_courses`:
--   `signature`
--       `courses` -> `signature`
--   `noteId`
--       `notes` -> `id`
--   `fnumber`
--       `users` -> `fNumber`
--

-- --------------------------------------------------------

--
-- Table structure for table `student_courseworks`
--

CREATE TABLE `student_courseworks` (
  `id` int(11) NOT NULL,
  `fnumber` int(11) NOT NULL,
  `courseworkId` int(11) NOT NULL,
  `noteId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `student_courseworks`:
--   `courseworkId`
--       `courseworks` -> `id`
--   `noteId`
--       `notes` -> `id`
--   `fnumber`
--       `users` -> `fNumber`
--

-- --------------------------------------------------------

--
-- Table structure for table `student_tests`
--

CREATE TABLE `student_tests` (
  `id` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `testId` int(11) NOT NULL,
  `noteId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `student_tests`:
--   `noteId`
--       `notes` -> `id`
--   `testId`
--       `tests` -> `id`
--   `student`
--       `users` -> `fNumber`
--

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `course` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `tests`:
--   `course`
--       `courses` -> `signature`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `fNumber` int(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `virgin` tinyint(1) NOT NULL COMMENT 'Has the user logged in the account.',
  `latestSemester` varchar(40) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- RELATIONSHIPS FOR TABLE `users`:
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`signature`);

--
-- Indexes for table `courseworks`
--
ALTER TABLE `courseworks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `signature` (`signature`),
  ADD KEY `FK_student_courses_1` (`signature`),
  ADD KEY `FK_student_courses_2` (`noteId`),
  ADD KEY `student_fNumber` (`fnumber`);

--
-- Indexes for table `student_courseworks`
--
ALTER TABLE `student_courseworks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_stundet_courseworks_1` (`fnumber`),
  ADD KEY `FK_stundet_courseworks_2` (`courseworkId`),
  ADD KEY `FK_stundet_courseworks_3` (`noteId`);

--
-- Indexes for table `student_tests`
--
ALTER TABLE `student_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_stundet_tests_1` (`student`),
  ADD KEY `FK_stundet_tests_2` (`testId`),
  ADD KEY `FK_stundet_tests_3` (`noteId`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course` (`course`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`fNumber`),
  ADD UNIQUE KEY `fNumber` (`fNumber`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courseworks`
--
ALTER TABLE `courseworks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=776;

--
-- AUTO_INCREMENT for table `student_courseworks`
--
ALTER TABLE `student_courseworks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_tests`
--
ALTER TABLE `student_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD CONSTRAINT `rel_courses_student_courses` FOREIGN KEY (`signature`) REFERENCES `courses` (`signature`),
  ADD CONSTRAINT `rel_notes_student_courses` FOREIGN KEY (`noteId`) REFERENCES `notes` (`id`),
  ADD CONSTRAINT `rel_users_student_courses` FOREIGN KEY (`fnumber`) REFERENCES `users` (`fNumber`);

--
-- Constraints for table `student_courseworks`
--
ALTER TABLE `student_courseworks`
  ADD CONSTRAINT `rel_courseworks_student_courseworks` FOREIGN KEY (`courseworkId`) REFERENCES `courseworks` (`id`),
  ADD CONSTRAINT `rel_notes_student_courseworks` FOREIGN KEY (`noteId`) REFERENCES `notes` (`id`),
  ADD CONSTRAINT `rel_users_student_courseworks` FOREIGN KEY (`fnumber`) REFERENCES `users` (`fNumber`);

--
-- Constraints for table `student_tests`
--
ALTER TABLE `student_tests`
  ADD CONSTRAINT `rel_notes_student_tests` FOREIGN KEY (`noteId`) REFERENCES `notes` (`id`),
  ADD CONSTRAINT `rel_tests_student_tests` FOREIGN KEY (`testId`) REFERENCES `tests` (`id`),
  ADD CONSTRAINT `rel_users_student_tests` FOREIGN KEY (`student`) REFERENCES `users` (`fNumber`);

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`course`) REFERENCES `courses` (`signature`);


--
-- Metadata
--
USE `phpmyadmin`;

--
-- Dumping data for table `pma__pdf_pages`
--

INSERT INTO `pma__pdf_pages` (`db_name`, `page_descr`) VALUES
('infm114', 'INFM114_View');

SET @LAST_PAGE = LAST_INSERT_ID();

--
-- Dumping data for table `pma__table_coords`
--

INSERT INTO `pma__table_coords` (`db_name`, `table_name`, `pdf_page_number`, `x`, `y`) VALUES
('infm114', 'courses', @LAST_PAGE, 1120, 100),
('infm114', 'courseworks', @LAST_PAGE, 920, 280),
('infm114', 'notes', @LAST_PAGE, 230, 240),
('infm114', 'student_courses', @LAST_PAGE, 580, 60),
('infm114', 'student_courseworks', @LAST_PAGE, 580, 240),
('infm114', 'student_tests', @LAST_PAGE, 580, 360),
('infm114', 'tests', @LAST_PAGE, 960, 390),
('infm114', 'users', @LAST_PAGE, 200, 80);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
