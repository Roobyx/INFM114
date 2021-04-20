-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2021 at 02:15 PM
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


-- --------------------------------------------------------

--
-- Table structure for table `courseworks`
--

CREATE TABLE `courseworks` (
  `id` int(11) NOT NULL,
  `dueDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `course` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=648;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
