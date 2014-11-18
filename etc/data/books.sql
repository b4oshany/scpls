-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 17, 2014 at 11:08 PM
-- Server version: 5.5.40
-- PHP Version: 5.3.10-1ubuntu3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `scpls`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `addUser`(in user_email varchar(55), in user_password varchar(25), in first_name varchar(25), in last_name varchar(25), in user_dob date, in sex varchar(7))
BEGIN
DECLARE userid int;
insert into users(email, password) values(user_email, md5(user_password));
SET userid = (select LAST_INSERT_ID());
insert into user_profile(user_id, first_name, last_name, dob, email, gender, date_joined) values(userid, first_name, last_name, user_dob, user_email, sex, CURDATE());
select * from user_profile where user_id = userid and email = user_email;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(55) NOT NULL,
  `last_name` varchar(55) NOT NULL,
  `email` varchar(55) NOT NULL,
  `gender` tinytext NOT NULL,
  `cover_photo` varchar(128) NOT NULL DEFAULT 'static/img/icon/user.png',
  `description` varchar(180) NOT NULL,
  PRIMARY KEY (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`author_id`, `first_name`, `last_name`, `email`, `gender`, `cover_photo`, `description`) VALUES
(1, 'Oshane', 'Bailey', '', '', 'static/img/icon/user.png', ''),
(2, 'Andrelle', 'Thompson', '', '', 'static/img/icon/user.png', ''),
(3, 'Eric', 'Needham', '', '', 'static/img/icon/user.png', ''),
(4, 'Cloyde', 'McBeth', '', '', 'static/img/icon/user.png', ''),
(5, 'Marc', 'Lynch', '', '', 'static/img/icon/user.png', ''),
(6, 'Neil', 'Armstrong', '', '', 'static/img/icon/user.png', ''),
(7, 'Alifumike', 'Adedipe', '', '', 'static/img/icon/user.png', ''),
(8, 'Gwen', 'Stephanie', '', '', 'static/img/icon/user.png', ''),
(9, 'Mike', 'Will', '', '', 'static/img/icon/user.png', ''),
(10, 'Cathy', 'Underwood', '', '', 'static/img/icon/user.png', ''),
(11, 'Will', 'Smith', '', '', 'static/img/icon/user.png', ''),
(12, 'Aca', 'Cop', '', '', 'static/img/icon/user.png', ''),
(13, 'Dean', 'Jones', '', '', 'static/img/icon/user.png', '');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `book_title` varchar(55) NOT NULL,
  `author_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `cover_photo` varchar(155) NOT NULL DEFAULT 'static/img/photo/default-book-cover.png',
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `book_title`, `author_id`, `genre_id`, `rating`, `cover_photo`, `date_added`) VALUES
(1, 'Vecni', 1, 600, 0, 'http://www.technaturals.com/wp-content/uploads/2013/08/Technology.jpg', '2014-11-16 23:31:15'),
(2, 'Marco', 2, 421, 0, 'http://c.tadst.com/gfx/600x400/galician-literature-day-spain.jpg?1', '2014-11-16 23:31:15'),
(3, 'Best Arts Practises', 3, 740, 0, 'http://www.designindaba.com/sites/default/files/node/page/23/IMG_3015.jpg', '2014-11-16 23:31:15'),
(4, 'Programming For Dummies', 4, 5, 0, 'http://bobchoat.files.wordpress.com/2014/06/where-is-technology-heading.jpg', '2014-11-16 23:31:15'),
(5, 'Work Ethics', 5, 170, 0, 'http://artsonthepeninsula.files.wordpress.com/2012/08/we-value-the-arts1.jpg', '2014-11-16 23:31:15'),
(6, 'Around The Globe', 6, 199, 0, 'http://sd.keepcalm-o-matic.co.uk/i/keep-calm-and-study-geography-113.png', '2014-11-16 23:31:15'),
(7, 'World War I', 7, 931, 0, 'http://www.dpcdsb.org/NR/rdonlyres/22300638-C9FC-439B-8040-A7C4A2C5D7EC/87305/history.gif', '2014-11-16 23:31:15'),
(8, 'New Moon', 9, 135, 0, 'http://www.ithaca.edu/depts/i/Philosophy/28473_photo.jpg', '2014-11-16 23:31:15'),
(9, 'Mind Dynamics', 10, 135, 0, 'http://www.ithaca.edu/depts/i/Philosophy/28473_photo.jpg', '2014-11-16 23:31:15'),
(10, 'Steam Turbines', 11, 6, 0, 'http://3.bp.blogspot.com/-YFPIfvj7h0M/U6OD1EglnOI/AAAAAAAACdY/VEqw1DlqTy4/s1600/technology44-743413.png', '2014-11-16 23:31:15'),
(11, 'Islam a Religion or Excuse', 12, 200, 0, 'http://kenanmalik.files.wordpress.com/2013/08/religion-praying.jpg?w=800', '2014-11-16 23:31:15'),
(12, 'Mastering UI Design', 13, 742, 0, 'http://www.ronnestam.com/wp-content/uploads/2013/03/design-thinking.jpg', '2014-11-16 23:31:15');

-- --------------------------------------------------------

--
-- Stand-in structure for view `book_information`
--
CREATE TABLE IF NOT EXISTS `book_information` (
`book_id` int(11)
,`book_title` varchar(55)
,`author_id` int(11)
,`genre_id` int(11)
,`rating` int(11)
,`cover_photo` varchar(155)
,`date_added` timestamp
,`genre` varchar(55)
,`author` varchar(111)
);
-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE IF NOT EXISTS `genres` (
  `genre_id` int(11) NOT NULL,
  `genre` varchar(55) NOT NULL,
  PRIMARY KEY (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`genre_id`, `genre`) VALUES
(0, 'General Works'),
(5, 'lol'),
(6, 'lol'),
(100, 'Philosophy and Psychology'),
(135, 'lol'),
(170, 'lol'),
(199, 'lol'),
(200, 'Religion'),
(300, 'Social Sciences'),
(400, 'Language'),
(421, 'lol'),
(500, 'Pure Science'),
(600, 'Technology'),
(616, 'Diseases'),
(621, 'Applied physics'),
(628, 'Sanitary Engineering'),
(638, 'Insect culture'),
(700, 'Arts & Recreation'),
(740, 'lol'),
(742, 'lol'),
(800, 'Literature'),
(900, 'History'),
(920, 'Geography'),
(931, 'lol');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(30) NOT NULL,
  `password` varchar(64) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`) VALUES
(1, 'b4.oshany@gmail.com', '038300856746502133662bccc733b863');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE IF NOT EXISTS `user_profile` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(55) DEFAULT NULL,
  `last_name` varchar(55) DEFAULT NULL,
  `email` varchar(65) DEFAULT NULL,
  `gender` varchar(7) DEFAULT NULL,
  `occupation` varchar(25) NOT NULL,
  `dob` date DEFAULT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT 'static/img/icons/user.png',
  `date_joined` datetime NOT NULL,
  `is_login` tinyint(1) NOT NULL DEFAULT '1',
  `last_seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT 'last logged in date',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `first_name`, `last_name`, `email`, `gender`, `occupation`, `dob`, `profile_pic`, `date_joined`, `is_login`, `last_seen`) VALUES
(1, 'Oshane', 'Bailey', 'b4.oshany@gmail.com', '1991-09', '', '0000-00-00', 'static/img/icons/user.png', '2014-11-11 00:00:00', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure for view `book_information`
--
DROP TABLE IF EXISTS `book_information`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_information` AS select `books`.`book_id` AS `book_id`,`books`.`book_title` AS `book_title`,`books`.`author_id` AS `author_id`,`books`.`genre_id` AS `genre_id`,`books`.`rating` AS `rating`,`books`.`cover_photo` AS `cover_photo`,`books`.`date_added` AS `date_added`,`genres`.`genre` AS `genre`,concat(`authors`.`first_name`,' ',`authors`.`last_name`) AS `author` from ((`books` left join `authors` on((`authors`.`author_id` = `books`.`author_id`))) left join `genres` on((`genres`.`genre_id` = `books`.`genre_id`)));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
