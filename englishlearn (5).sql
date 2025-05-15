-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 10:48 PM
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
-- Database: `englishlearn`
--

-- --------------------------------------------------------

--
-- Table structure for table `learningcard`
--

CREATE TABLE `learningcard` (
  `card_id` int(11) NOT NULL,
  `card_title` varchar(255) NOT NULL,
  `difficulty_level` varchar(50) NOT NULL,
  `category` varchar(100) NOT NULL,
  `card_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `learningcard`
--

INSERT INTO `learningcard` (`card_id`, `card_title`, `difficulty_level`, `category`, `card_image`, `description`) VALUES
(25, 'CARD 1', 'Beginner', 'Vocabulary', 'cards/681659eb17d68.jpg', 'Basic English vocabulary and simple sentences to build foundational language skills.'),
(33, 'Card 2', 'Beginner', 'Vocabulary', 'cards/6816277633080.jpg', 'this card of for grade 2');

-- --------------------------------------------------------

--
-- Table structure for table `subjectcard`
--

CREATE TABLE `subjectcard` (
  `subject_id` int(11) NOT NULL,
  `learning_card_id` int(11) DEFAULT NULL,
  `subject_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `subject_card_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subjectcard`
--

INSERT INTO `subjectcard` (`subject_id`, `learning_card_id`, `subject_type`, `description`, `title`, `subject_card_image`) VALUES
(42, 25, 'English', 'They practice phonics, and basic grammar while developing their communication skills.', 'ENGLISH', '../subject-img/6818a1cc7995b.jpg'),
(43, 25, 'Mathematics', 'Students learn to count and recognize numbers by reading words.', 'MATHEMATICS', 'subject-img/67d9bb3759df9.jpg'),
(60, 33, 'English', 'asdasdasd', 'history', '.../subject-img/68162fed29f34.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `user_type` enum('admin','student') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_login_time` int(11) DEFAULT 0,
  `otp_code` int(11) DEFAULT NULL,
  `grade_level` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_image`, `user_type`, `created_at`, `total_login_time`, `otp_code`, `grade_level`) VALUES
(1, 'Acabo', 'acabo@gmail.com', 'admin123', 'uploads/RobloxScreenShot20250221_113116791.png', 'student', '2025-02-22 15:25:56', 53059, 0, '2'),
(3, 'admin', 'admin@gmail.com', 'admin123', NULL, 'admin', '2025-02-22 15:26:35', 0, 0, NULL),
(13, 'Paul John Acabo', 'ASDFWERAD@gmail.com', 'pjacabo123', 'uploads/user.png', 'student', '2025-03-14 07:05:09', 663, 0, NULL),
(19, 'dummy1', 'dummy1@gmail.com', 'dummy123', 'uploads/1b1.jpg', 'student', '2025-03-18 16:15:58', 0, NULL, NULL),
(21, 'asdsad', 'asdas@gmail.com', 'mark123', NULL, 'student', '2025-05-03 14:08:05', 161777, NULL, '2'),
(25, 'markk', 'markvil64@gmail.com', 'mark123', 'uploads/669a307a80d4d5da13d5467a.webp', 'student', '2025-05-05 14:18:39', 0, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `wordcard`
--

CREATE TABLE `wordcard` (
  `word_id` int(11) NOT NULL,
  `subject_card_id` int(11) DEFAULT NULL,
  `word` varchar(100) NOT NULL,
  `phonetic_spelling` varchar(100) DEFAULT NULL,
  `definition` text DEFAULT NULL,
  `example_sentence` text DEFAULT NULL,
  `word_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wordcard`
--

INSERT INTO `wordcard` (`word_id`, `subject_card_id`, `word`, `phonetic_spelling`, `definition`, `example_sentence`, `word_image`) VALUES
(102, 42, 'TREE', 'tree', '', 'A bird is in the TREE.', '../word-img/6818a0122f421-398826f3-8eac-4dfa-93b6-b53a00bcf6b7.png'),
(377, 60, 'History', 'historiee', 'asdasd', 'asdasd', '../word-img/6816303d867a1.png'),
(379, 42, 'This is so hard to do', 'thiss iss sooo hard to doo', 'asdasd', 'this is so hard', '../word-img/6818a669c1ef6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `word_progress`
--

CREATE TABLE `word_progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `word_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  `pronunciation_score` int(11) DEFAULT 0,
  `completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `word_progress`
--

INSERT INTO `word_progress` (`progress_id`, `user_id`, `word_id`, `card_id`, `pronunciation_score`, `completed`, `completed_at`) VALUES
(342, 21, 377, 33, 98, 1, '2025-05-03 17:58:35'),
(343, 21, 102, 25, 94, 1, '2025-05-04 06:20:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `learningcard`
--
ALTER TABLE `learningcard`
  ADD PRIMARY KEY (`card_id`);

--
-- Indexes for table `subjectcard`
--
ALTER TABLE `subjectcard`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `learning_card_id` (`learning_card_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- Indexes for table `wordcard`
--
ALTER TABLE `wordcard`
  ADD PRIMARY KEY (`word_id`),
  ADD KEY `subject_card_id` (`subject_card_id`);

--
-- Indexes for table `word_progress`
--
ALTER TABLE `word_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `user_word_card` (`user_id`,`word_id`,`card_id`),
  ADD KEY `word_id` (`word_id`),
  ADD KEY `card_id` (`card_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `learningcard`
--
ALTER TABLE `learningcard`
  MODIFY `card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `subjectcard`
--
ALTER TABLE `subjectcard`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `wordcard`
--
ALTER TABLE `wordcard`
  MODIFY `word_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=380;

--
-- AUTO_INCREMENT for table `word_progress`
--
ALTER TABLE `word_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=351;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subjectcard`
--
ALTER TABLE `subjectcard`
  ADD CONSTRAINT `subjectcard_ibfk_1` FOREIGN KEY (`learning_card_id`) REFERENCES `learningcard` (`card_id`);

--
-- Constraints for table `wordcard`
--
ALTER TABLE `wordcard`
  ADD CONSTRAINT `wordcard_ibfk_1` FOREIGN KEY (`subject_card_id`) REFERENCES `subjectcard` (`subject_id`);

--
-- Constraints for table `word_progress`
--
ALTER TABLE `word_progress`
  ADD CONSTRAINT `word_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `word_progress_ibfk_2` FOREIGN KEY (`word_id`) REFERENCES `wordcard` (`word_id`),
  ADD CONSTRAINT `word_progress_ibfk_3` FOREIGN KEY (`card_id`) REFERENCES `learningcard` (`card_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
