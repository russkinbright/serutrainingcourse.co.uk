-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 02:41 PM
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
-- Database: `russkin_seru`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(255) NOT NULL,
  `title` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  `unique_id` bigint(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `footer_price` decimal(10,0) DEFAULT NULL,
  `total_student` text DEFAULT NULL,
  `rating` text DEFAULT NULL,
  `week` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `title`, `meta`, `unique_id`, `description`, `image`, `price`, `footer_price`, `total_student`, `rating`, `week`, `created_at`, `updated_at`) VALUES
(6, 'SERU Assessment Course - 4 Week Unlimited Access', 'SERU Assessment Course Test 4 Week', 2324548, '<p>Unlock your pathway to success with the <strong>SERU Assessment Course</strong>, designed specifically to help you master the <strong>Safety, Equality and Regulatory Understanding (SERU)</strong> requirements. This 4-week course gives you <strong>unlimited access</strong> to expertly curated learning materials, mock tests, and real exam-style questions — all aligned with the Transport for London (TfL) guidelines.</p><p>Whether you\'re preparing for your first SERU test or aiming to improve your confidence and speed, this course provides a flexible and supportive environment to learn at your own pace.</p><p><strong>What’s Included:</strong></p><ul><li>Unlimited access for 4 weeks</li><li>Practice questions based on the official SERU framework</li><li>Realistic timed mock exams</li><li>Progress tracking and performance tips</li><li>Mobile and desktop friendly access</li></ul><p><br></p><p>Perfect for private hire drivers and anyone applying for a TfL licence who needs to pass the SERU assessment with confidence.</p><p><br></p>', 'https://static.wixstatic.com/media/31df3bd26e6a48aba9dff089e64abd3b.jpg/v1/fill/w_2739,h_1828,al_c,q_90/31df3bd26e6a48aba9dff089e64abd3b.jpg', 60, 70, '567', '4.2', '4', '2025-07-07 03:57:31.000000', '2025-07-17 09:41:05.000000'),
(8, 'SERU Assessment Course - 8 Week Unlimited Access', 'SERU Assessment Course Test 8 Week', 7612348, '<p>Unlock your pathway to success with the <strong>SERU Assessment Course</strong>, designed specifically to help you master the <strong>Safety, Equality and Regulatory Understanding (SERU)</strong> requirements. This 4-week course gives you <strong>unlimited access</strong> to expertly curated learning materials, mock tests, and real exam-style questions — all aligned with the Transport for London (TfL) guidelines.</p><p>Whether you\'re preparing for your first SERU test or aiming to improve your confidence and speed, this course provides a flexible and supportive environment to learn at your own pace.</p><p><strong>What’s Included:</strong></p><ul><li>Unlimited access for 4 weeks</li><li>Practice questions based on the official SERU framework</li><li>Realistic timed mock exams</li><li>Progress tracking and performance tips</li><li>Mobile and desktop friendly access</li></ul><p><br></p><p>Perfect for private hire drivers and anyone applying for a TfL licence who needs to pass the SERU assessment with confidence.</p>', 'https://www.addisonlee.com/wp-content/uploads/2023/07/What-Happens-if-I-Fail-SERU_663x300.jpg', 80, 90, '675', '5', '8', '2025-07-07 05:05:30.000000', '2025-07-17 09:41:19.000000'),
(9, 'SERU Assessment Course - 2 Week Unlimited Access', 'SERU Assessment Course Test 2 Week', 4525673, '<p>Unlock your pathway to success with the <strong>SERU Assessment Course</strong>, designed specifically to help you master the <strong>Safety, Equality and Regulatory Understanding (SERU)</strong> requirements. This 2-week course gives you <strong>unlimited access</strong> to expertly curated learning materials, mock tests, and real exam-style questions — all aligned with the Transport for London (TfL) guidelines.</p><p>Whether you\'re preparing for your first SERU test or aiming to improve your confidence and speed, this course provides a flexible and supportive environment to learn at your own pace.</p><p><strong>What’s Included:</strong></p><ul><li>Unlimited access for 4 weeks</li><li>Practice questions based on the official SERU framework</li><li>Realistic timed mock exams</li><li>Progress tracking and performance tips</li><li>Mobile and desktop friendly access</li></ul><p>Perfect for private hire drivers and anyone applying for a TfL licence who needs to pass the SERU assessment with confidence.</p>', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTTAsi_6j_T9sHwzcB1-8zAP28Wfh2cQunuPBt_EDJmd7HEBx9nD7liW2z7g-_Gsbd6ln8&usqp=CAU', 45, 50, '567', '5', '2', '2025-07-07 05:07:29.000000', '2025-07-17 09:40:48.000000');

-- --------------------------------------------------------

--
-- Table structure for table `course_progress`
--

CREATE TABLE `course_progress` (
  `id` bigint(255) NOT NULL,
  `learner_secret_id` bigint(255) DEFAULT NULL,
  `course_unique_id` bigint(255) DEFAULT NULL,
  `permodule` bigint(255) DEFAULT NULL,
  `perquestion` bigint(255) DEFAULT NULL,
  `progress` bigint(255) DEFAULT NULL,
  `mark` bigint(255) DEFAULT NULL,
  `total_mark` bigint(255) DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_progress`
--

INSERT INTO `course_progress` (`id`, `learner_secret_id`, `course_unique_id`, `permodule`, `perquestion`, `progress`, `mark`, `total_mark`, `is_completed`, `created_at`, `updated_at`) VALUES
(24, 61259, 2324548, NULL, NULL, 0, NULL, NULL, NULL, '2025-07-21 00:46:57.000000', '2025-07-21 00:46:57.000000');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learner`
--

CREATE TABLE `learner` (
  `id` int(255) NOT NULL,
  `secret_id` bigint(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `whom` text DEFAULT NULL,
  `payment_type` text DEFAULT NULL,
  `card` bigint(255) DEFAULT NULL,
  `card_expiry` text DEFAULT NULL,
  `card_code` bigint(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `postal_code` text DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `question` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learner`
--

INSERT INTO `learner` (`id`, `secret_id`, `name`, `email`, `whom`, `payment_type`, `card`, `card_expiry`, `card_code`, `phone`, `country`, `city`, `address`, `postal_code`, `message`, `password`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(7, 61259, 'Chinmoy Datta Priom', 'mrinmoy@instructor.com', 'personal', NULL, NULL, NULL, NULL, '01758506585', 'Bangladesh', 'Sylhet', 'Murila,Nathpara', '3100', NULL, '$2y$12$7wXGUrkq7C7qa2Fc1E.bdORqGuYAtElNCSLleHbxjGH.C1D2pFvwe', NULL, NULL, '2025-06-26 05:25:31.000000', '2025-07-21 00:46:28.000000');

-- --------------------------------------------------------

--
-- Table structure for table `m1_question`
--

CREATE TABLE `m1_question` (
  `id` int(255) NOT NULL,
  `unique_id` bigint(255) DEFAULT NULL,
  `mock_unique_id` bigint(255) DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `option_a` text DEFAULT NULL,
  `option_b` text DEFAULT NULL,
  `option_c` text DEFAULT NULL,
  `option_d` text DEFAULT NULL,
  `option_e` text DEFAULT NULL,
  `option_f` text DEFAULT NULL,
  `answer_1` text DEFAULT NULL,
  `answer_2` text DEFAULT NULL,
  `answer_3` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m1_question`
--

INSERT INTO `m1_question` (`id`, `unique_id`, `mock_unique_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `option_e`, `option_f`, `answer_1`, `answer_2`, `answer_3`, `created_at`, `updated_at`) VALUES
(21, 1559594, 4619030, 'You must _______ your _________ at all times when you are ________ as a PHV Driver', 'Working', 'badge', 'wear', 'badges', 'wearing', 'worked', 'a', 'b', 'c', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(22, 1683187, 4619030, 'When you get your licence you _______ also _______ a PHV driver\'s _______ (alsoknown as photographic ID).', 'would', 'badge', 'will', 'badger', 'receives', 'receive', 'b', 'c', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(23, 6897799, 4619030, 'You must wear your badge ________ you are working as a PHV driver, _______ whenyou are waiting to receive a booking or _________ to pick up a passenger', 'whennever', 'travels', 'including', 'travelling', 'included', 'whenever', 'c', 'd', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(24, 4869520, 4619030, 'You will need to take a seperate test to check you _______ to select and _________ aroute, __________ a map and identify locations', 'Experience', 'planned', 'plan', 'ability', 'read', 'reading', 'c', 'd', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(25, 3854720, 4619030, 'If we _________ you __________ exemption, we will send you an exemption _________.', 'give', 'an', 'gives', 'that', 'notice', 'noted', 'a', 'b', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(26, 8418971, 4619030, 'From the age of ________ , PHV drivers must have a medical _______ each time they________ to renew their licence', '45', '50', 'applies', 'examination', 'examiner', 'apply', 'a', 'd', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(27, 5827299, 4619030, 'When you get your _________ to work as a London PHV driver, there are a number of_________ and policies that you _________ to know about.', 'need', 'licence', 'needs', 'licenses', 'rules', 'Rules', 'a', 'b', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(28, 1807866, 4619030, 'You ________ keep your licence at __________ in a safe place and give a copy of it toany licensed private hire __________ you are working with.', 'drivers', 'will', 'house', 'should', 'home', 'operators', 'd', 'e', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(29, 3164937, 4619030, 'If TfL receives a ___________ about you or __________ aware of any __________ thatis not satisfactory, they may write to you and ask for your comments', 'complaint', 'beehaivor', 'becomes', 'postcard', 'behaviour', 'becoming', 'a', 'c', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(30, 7709988, 4619030, 'The letter that comes with your licence _________ important information about your______________ as a London PHV driver, including any ____________ you must meet', 'conditions', 'responsibilites', 'contained', 'reponsibling', 'contains', 'tasks', 'a', 'e', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(31, 1750951, 4619030, 'You must be _______ 21 or older _______ you _______ for your licence, there is noupper age limit.', 'ages', 'old', 'when', 'while', 'apply', 'applies', 'a', 'c', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(32, 6204786, 4619030, 'As soon as you are _______ it is important that you keep in contact with _______ andrespond to any letters, emails or other _______ of communication.', 'licencing', 'licensed', 'telephones', 'forms', 'TfL', 'DVLA', 'b', 'd', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(33, 6179238, 4619030, 'If there _______ any restrictions on a driver\'s right to _______ and work in the UK, TfLwill add an _______ condition to the licence.', 'live', 'are', 'life', 'is', 'appropriate', 'aproper', 'a', 'b', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(34, 2620264, 4619030, 'As a _______ London PHV driver, you must tell TfL _______ if your personal _______change.', 'circumstances', 'licencing', 'immediately', 'cirmustanced', 'licensed', 'within 7 days', 'a', 'c', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(35, 6910960, 4619030, 'If your application is approved, TfL will send you a London PHV _______. Your licencemay have some _______ attached to it. For example, if you have a medical condition,you may be required to _______ extra medical checks.', 'driver’s licence', 'tickets', 'had', 'conditions', 'driving licence', 'have', 'a', 'd', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(36, 8263887, 4619030, 'To get your licence to be a PHV driver you must be _______. The standards you must_______ are the _______ medical standards.', 'meet', 'DVLA Group 1', 'medically fit', 'met', 'DVLA Group 2', 'sick', 'a', 'c', 'e', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(37, 2120784, 4619030, 'You _______ tell TfL immediately if you are arrested, _______ with, convicted orcautioned for _______ crime.', 'can', 'the', 'must', 'charged', 'an', 'any', 'c', 'd', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(38, 4862743, 4619030, 'The badge _______ text in _______ which means a _______ person can know that youare licensed.', 'includes', 'deaf', 'braille', 'including', 'vision-impaired', 'Braille', 'a', 'e', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(39, 3063904, 4619030, 'To work as a London PHV driver you will need to be _______ by _______ – only then canyou carry out bookings for a London private hire _______ that is also licensed by TfL.', 'TfL', 'licence', 'operator', 'licensed', 'DVLA', 'operating', 'a', 'c', 'd', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000'),
(40, 9925628, 4619030, 'Your London PHV driver\'s licence normally lasts for _______. If it is for a _______ theletter that comes with the licence _______ explain why.', 'would', 'shorting period', 'five year', 'shorter period', 'three years', 'will', 'd', 'e', 'f', '2025-07-21 03:28:32.000000', '2025-07-21 03:28:32.000000');

-- --------------------------------------------------------

--
-- Table structure for table `m2_question`
--

CREATE TABLE `m2_question` (
  `id` int(255) NOT NULL,
  `unique_id` bigint(255) DEFAULT NULL,
  `mock_unique_id` bigint(255) DEFAULT NULL,
  `type` text DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `option_a` text DEFAULT NULL,
  `option_b` text DEFAULT NULL,
  `option_c` text DEFAULT NULL,
  `answer_1` text DEFAULT NULL,
  `answer_2` text DEFAULT NULL,
  `incorrect` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `m2_question`
--

INSERT INTO `m2_question` (`id`, `unique_id`, `mock_unique_id`, `type`, `question_text`, `option_a`, `option_b`, `option_c`, `answer_1`, `answer_2`, `incorrect`, `created_at`, `updated_at`) VALUES
(91, 9043887, 2717515, 'single', 'What is the minimum age at which someone can apply for a PHV driver\'s licence?', '19', '21', '25', 'b', NULL, 'Applicant must be 21 or older when at the time of application', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(92, 5823925, 2717515, 'single', 'Is there an upper age limit on applicants for a PHV driver\'s licence?', 'Yes-65', 'No', 'Yes-70', 'b', NULL, 'there is no upper age limit however applicants over the age of 65 arerequired to have annual medical assessments', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(93, 4565993, 2717515, 'single', 'For how many years must you have held a driving licence before applying for a PHVdriver\'s licence?', '3', '5', 'There is no limit', 'a', NULL, 'You must have held a full driving licence for at least 3 years', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(94, 7683330, 2717515, 'single', 'What is a Topographical Skills assessment?', 'A driving test with a TfL instructor', 'A test to check your ability to select and plan a route , read a map and identifylocations', 'A test to check your knowledge of London', 'b', NULL, 'it\'s a test that checks your abilities to select and plan a route, read a mapand identify locations.', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(95, 5530017, 2717515, 'single', 'Do you need a British passport to apply for PHV driver\'s licence?', 'No , you but you must have the right to live and work in the UK', 'Yes', 'Only British or European Union passports are accepted', 'a', NULL, 'You must have the right to live and work in the UK', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(96, 3032792, 2717515, 'double', 'What driving licence must you have held to apply for a PHV driver\'s licence? (select 2)', 'Full DVLA or Northern Ireland', 'It does not matter, as long as it is valid (anywhere in the world)', 'European Union or European Economic Area', 'a', 'c', 'You must have a full DVLA, Northern Ireland, European Union (EU) orEuropeanEconomic Area (EEA) state driving licence', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(97, 8834387, 2717515, 'double', 'Which of these must be kept by an operator you work for (select 2)', 'Copy of your DVLA/NI/EU/EEA driving licence', 'Copy of your PHV driver\'s licence', 'Copy of your passport and proof of address', 'a', 'b', 'The PHV operator(s) you work with must keep copies of your PHV driver’slicence and your DVLA/NI/EU/EEA driving licence', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(98, 9563853, 2717515, 'double', 'You may be exempt from supplying a medical form in support of your new PHVdriver application if you: (select 2)', 'Have a provisional DVLA Group 2 licence or a valid, current pilot’s licence issuedby the Joint Aviation Authorities', 'Have a current London taxi driver licence', 'Are under the age of 45', 'a', 'b', 'you must be medically fit to become licensed. The standards you mustmeet are the DVLA Group 2 medical standards. In most cases, this will mean that youwill need to have a medical examination with someone (i.e. a doctor) who has accessto your full medical records. You may be exempt from supplying a medical form ifyou: Have a full or provisional (issued after January 1997) DVLA Group 2 licence orHave a current London taxi driver’s licence or Have a valid, current pilot’s licenceissued by the Joint Aviation Authorities .If you\'re under 45 you may also be exemptbut only if you\'re renewing your licence - this does not apply to new applications', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(99, 6926077, 2717515, 'double', 'What other changes of personal circumstances must be reported to TfLimmediately? (select 2)', 'You have applied for a PHV driver\'s licence with another licensing authority', 'You are the subject of a sexual offences order', 'If you have broken the law and have been disqualified from driving', 'b', 'c', 'You must report if you have broken the law and have been disqualifiedfrom driving.If you are the subject of a mental health order or sexual offences order 10. If you areon either the Adults or Children’s Barred Lists. If you have a private hire or taxidriver’s licence with another licensing authority and that authority has suspended orrevoked your licence, or refused any new application you have made', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000'),
(100, 6164993, 2717515, 'double', 'You need to notify TfL immediately if: (select 2)', 'You are on either the Adult or Children\'s Barred lists', 'You work over 12 hours a day', 'You have a private hire or taxi driver’s licence with another licensing authority andthat authority has suspended or revoked your licence, or refused any newapplication you have made', 'a', 'c', 'You need to notify TfL immediately if:You have broken the law and have been disqualified from driving. Please note thatyou will also have to return your London PHV driver’s licence and badge to TfL. Youare the subject of a mental health order or sexual offences order 10. You are oneither the Adults or Children’s Barred Lists. You have a private hire or taxi driver’slicence with another licensing authority and that authority has suspended or revokedyour licence, or refused any new application you have made', '2025-07-22 00:45:20.000000', '2025-07-22 00:45:20.000000');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mock`
--

CREATE TABLE `mock` (
  `id` int(255) NOT NULL,
  `unique_id` bigint(255) DEFAULT NULL,
  `mock_number` text DEFAULT NULL,
  `tag` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mock`
--

INSERT INTO `mock` (`id`, `unique_id`, `mock_number`, `tag`, `name`, `created_at`, `updated_at`) VALUES
(1, 4619030, 'mock 1', 'Section 1 - London PHV Driver Licensing', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(2, 1655827, 'mock 1', 'Section 2 - Licensing Requirements for PHVs', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(3, 6623923, 'mock 1', 'Section 3 - Carrying out Private Hire Journeys', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(4, 5872398, 'mock 1', 'Section 4 - Staying Safe', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(5, 3648377, 'mock 1', 'Section 5 - Driver Behaviour', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(6, 5708104, 'mock 1', 'Section 6 - Driving and Parking in London', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(7, 2111962, 'mock 1', 'Section 7 - Safer Driving', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(8, 1185054, 'mock 1', 'Section 8 - Being Aware of Equality and Disability', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(9, 3045600, 'mock 1', 'Section 9 - Safeguarding Children and Adults at Risk', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(10, 2480745, 'mock 1', 'Section 10 - Ridesharing', 'Mock Test-1', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(11, 2717515, 'mock 2', 'Section 1 - London PHV Driver Licensing', 'Mock Test-2', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(12, 2711425, 'mock 2', 'Section 2 - Licensing Requirements for PHVs', 'Mock Test-2', '2025-07-09 06:06:09.000000', '2025-07-09 06:06:09.000000'),
(13, 6361884, 'mock 2', 'Section 3 - Carrying out Private Hire Journeys', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000'),
(14, 8010482, 'mock 2', 'Section 4 - Staying Safe', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000'),
(15, 4519445, 'mock 2', 'Section 5 - Driver Behaviour', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000'),
(16, 1583977, 'mock 2', 'Section 6 - Driving and Parking in London', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000'),
(17, 1752433, 'mock 2', 'Section 7 - Safer Driving', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000'),
(18, 4635360, 'mock 2', 'Section 8 - Being Aware of Equality and Disability', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000'),
(19, 9350205, 'mock 2', 'Section 9 - Safeguarding Children and Adults at Risk', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000'),
(20, 6732932, 'mock 2', 'Section 10 - Ridesharing', 'Mock Test-2', '2025-07-09 06:06:10.000000', '2025-07-09 06:06:10.000000');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` bigint(255) NOT NULL,
  `payment_unique_id` bigint(255) DEFAULT NULL,
  `learner_secret_id` bigint(255) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `postal_code` bigint(255) DEFAULT NULL,
  `payment_type` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `course_unique_id` bigint(255) DEFAULT NULL,
  `quantity` bigint(255) DEFAULT NULL,
  `whom` text DEFAULT NULL,
  `course_title` text DEFAULT NULL,
  `price` bigint(255) DEFAULT NULL,
  `media` text DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `payment_unique_id`, `learner_secret_id`, `name`, `email`, `phone`, `city`, `address`, `postal_code`, `payment_type`, `country`, `course_unique_id`, `quantity`, `whom`, `course_title`, `price`, `media`, `message`, `created_at`, `updated_at`) VALUES
(13, 13863943, 61259, 'Chinmoy Datta Priom', 'mrinmoy@instructor.com', '01758506585', 'Sylhet', 'Murila,Nathpara', 3100, 'other', 'Bangladesh', 2324548, 1, 'personal', 'SERU Assessment Course - 4 Week Unlimited Access', 60, 'Course Cave', NULL, '2025-07-21 00:46:57.000000', '2025-07-21 00:46:57.000000');

-- --------------------------------------------------------

--
-- Table structure for table `practice`
--

CREATE TABLE `practice` (
  `id` int(255) NOT NULL,
  `unique_id` bigint(255) DEFAULT NULL,
  `tag` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `practice`
--

INSERT INTO `practice` (`id`, `unique_id`, `tag`, `name`, `created_at`, `updated_at`) VALUES
(1, 8875728, 'Section 1 - London PHV Driver Licensing', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(2, 8212197, 'Section 2 - Licensing Requirements for PHVs', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(3, 6345238, 'Section 3 - Carrying out Private Hire Journeys', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(4, 3635054, 'Section 4 - Staying Safe', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(5, 7132067, 'Section 5 - Driver Behaviour', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(6, 1255964, 'Section 6 - Driving and Parking in London', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(7, 8938048, 'Section 7 - Safer Driving', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(8, 3660507, 'Section 8 - Being Aware of Equality and Disability', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(9, 9611116, 'Section 9 - Safeguarding Children and Adults at Risk', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000'),
(10, 4615690, 'Section 10 - Ridesharing', 'Practice Question', '2025-07-08 03:26:36.000000', '2025-07-08 03:26:36.000000');

-- --------------------------------------------------------

--
-- Table structure for table `p_question`
--

CREATE TABLE `p_question` (
  `id` int(255) NOT NULL,
  `unique_id` bigint(255) DEFAULT NULL,
  `practice_unique_id` bigint(255) DEFAULT NULL,
  `type` text DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `option_a` text DEFAULT NULL,
  `option_b` text DEFAULT NULL,
  `option_c` text DEFAULT NULL,
  `answer_1` text DEFAULT NULL,
  `answer_2` text DEFAULT NULL,
  `incorrect` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `p_question`
--

INSERT INTO `p_question` (`id`, `unique_id`, `practice_unique_id`, `type`, `question_text`, `option_a`, `option_b`, `option_c`, `answer_1`, `answer_2`, `incorrect`, `created_at`, `updated_at`) VALUES
(31, 3397573, 8875728, 'single', 'What is the minimum age at which someone can apply for a PHV driver\'s licence?', '19', '21', '25', 'b', NULL, 'Applicant must be 21 or older when at the time of application', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(32, 4803283, 8875728, 'single', 'Is there an upper age limit on applicants for a PHV driver\'s licence?', 'Yes-65', 'No', 'Yes-70', 'b', NULL, 'there is no upper age limit however applicants over the age of 65 arerequired to have annual medical assessments', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(33, 4680514, 8875728, 'single', 'For how many years must you have held a driving licence before applying for a PHVdriver\'s licence?', '3', '5', 'There is no limit', 'a', NULL, 'You must have held a full driving licence for at least 3 years', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(34, 1178911, 8875728, 'single', 'What is a Topographical Skills assessment?', 'A driving test with a TfL instructor', 'A test to check your ability to select and plan a route , read a map and identifylocations', 'A test to check your knowledge of London', 'b', NULL, 'it\'s a test that checks your abilities to select and plan a route, read a mapand identify locations.', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(35, 6164099, 8875728, 'single', 'Do you need a British passport to apply for PHV driver\'s licence?', 'No , you but you must have the right to live and work in the UK', 'Yes', 'Only British or European Union passports are accepted', 'a', NULL, 'You must have the right to live and work in the UK', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(36, 8135990, 8875728, 'double', 'What driving licence must you have held to apply for a PHV driver\'s licence? (select 2)', 'Full DVLA or Northern Ireland', 'It does not matter, as long as it is valid (anywhere in the world)', 'European Union or European Economic Area', 'a', 'c', 'You must have a full DVLA, Northern Ireland, European Union (EU) orEuropeanEconomic Area (EEA) state driving licence', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(37, 5642345, 8875728, 'double', 'Which of these must be kept by an operator you work for (select 2)', 'Copy of your DVLA/NI/EU/EEA driving licence', 'Copy of your PHV driver\'s licence', 'Copy of your passport and proof of address', 'a', 'b', 'The PHV operator(s) you work with must keep copies of your PHV driver’slicence and your DVLA/NI/EU/EEA driving licence', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(38, 2306621, 8875728, 'double', 'You may be exempt from supplying a medical form in support of your new PHVdriver application if you: (select 2)', 'Have a provisional DVLA Group 2 licence or a valid, current pilot’s licence issuedby the Joint Aviation Authorities', 'Have a current London taxi driver licence', 'Are under the age of 45', 'a', 'b', 'you must be medically fit to become licensed. The standards you mustmeet are the DVLA Group 2 medical standards. In most cases, this will mean that youwill need to have a medical examination with someone (i.e. a doctor) who has accessto your full medical records. You may be exempt from supplying a medical form ifyou: Have a full or provisional (issued after January 1997) DVLA Group 2 licence orHave a current London taxi driver’s licence or Have a valid, current pilot’s licenceissued by the Joint Aviation Authorities .If you\'re under 45 you may also be exemptbut only if you\'re renewing your licence - this does not apply to new applications', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(39, 5493166, 8875728, 'double', 'What other changes of personal circumstances must be reported to TfLimmediately? (select 2)', 'You have applied for a PHV driver\'s licence with another licensing authority', 'You are the subject of a sexual offences order', 'If you have broken the law and have been disqualified from driving', 'b', 'c', 'You must report if you have broken the law and have been disqualifiedfrom driving.If you are the subject of a mental health order or sexual offences order 10. If you areon either the Adults or Children’s Barred Lists. If you have a private hire or taxidriver’s licence with another licensing authority and that authority has suspended orrevoked your licence, or refused any new application you have made', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000'),
(40, 5316375, 8875728, 'double', 'You need to notify TfL immediately if: (select 2)', 'You are on either the Adult or Children\'s Barred lists', 'You work over 12 hours a day', 'You have a private hire or taxi driver’s licence with another licensing authority andthat authority has suspended or revoked your licence, or refused any newapplication you have made', 'a', 'c', 'You need to notify TfL immediately if:You have broken the law and have been disqualified from driving. Please note thatyou will also have to return your London PHV driver’s licence and badge to TfL. Youare the subject of a mental health order or sexual offences order 10. You are oneither the Adults or Children’s Barred Lists. You have a private hire or taxi driver’slicence with another licensing authority and that authority has suspended or revokedyour licence, or refused any new application you have made', '2025-07-21 02:50:48.000000', '2025-07-21 02:50:48.000000');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(255) NOT NULL,
  `unique_id` bigint(255) DEFAULT NULL,
  `course_unique_id` bigint(255) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `sequence` bigint(255) DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `unique_id`, `course_unique_id`, `name`, `sequence`, `created_at`, `updated_at`) VALUES
(1, 9753202, 2324548, 'Section 1 - London PHV Driver Licensing', 1, '2025-07-07 23:36:58.000000', '2025-07-07 23:47:12.000000'),
(2, 2495306, 2324548, 'Section 2 - Licensing Requirements for PHVs', 2, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(3, 7489229, 2324548, 'Section 3 - Carrying out Private Hire Journeys', 3, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(4, 6533232, 2324548, 'Section 4 - Staying Safe', 4, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(5, 8186182, 2324548, 'Section 5 - Driver Behaviour', 5, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(6, 2092689, 2324548, 'Section 6 - Driving and Parking in London', 6, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(7, 6451929, 2324548, 'Section 7 - Safer Driving', 7, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(8, 9950442, 2324548, 'Section 8 - Being Aware of Equality and Disability', 8, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(9, 2235489, 2324548, 'Section 9 - Safeguarding Children and Adults at Risk', 9, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(10, 5236994, 2324548, 'Section 10 - Ridesharing', 10, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000'),
(11, 6723140, 2324548, 'FINAL MOCK TEST', 11, '2025-07-07 23:36:58.000000', '2025-07-07 23:36:58.000000');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('qhnwIoddxPg5z5wHYlCSB9OQ51WOWioxkCw5CgmZ', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSEkwbGU2T3EyM2VaelpzRDJzT2xaSWFFcHRxNXhmYkg5cFo1UnJtaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvbGVhcm5lci9jb3Vyc2UvMjMyNDU0OCI7fXM6NTQ6ImxvZ2luX2xlYXJuZXJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1753187532);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_progress`
--
ALTER TABLE `course_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learner`
--
ALTER TABLE `learner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m1_question`
--
ALTER TABLE `m1_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m2_question`
--
ALTER TABLE `m2_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mock`
--
ALTER TABLE `mock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `practice`
--
ALTER TABLE `practice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `p_question`
--
ALTER TABLE `p_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course_progress`
--
ALTER TABLE `course_progress`
  MODIFY `id` bigint(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learner`
--
ALTER TABLE `learner`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `m1_question`
--
ALTER TABLE `m1_question`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `m2_question`
--
ALTER TABLE `m2_question`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mock`
--
ALTER TABLE `mock`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` bigint(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `practice`
--
ALTER TABLE `practice`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `p_question`
--
ALTER TABLE `p_question`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
