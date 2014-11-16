-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 16, 2014 at 01:47 PM
-- Server version: 5.5.40
-- PHP Version: 5.3.10-1ubuntu3.15

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: scpls
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=root@localhost PROCEDURE addUser(in user_email varchar(55), in user_password varchar(25), in first_name varchar(25), in last_name varchar(25), in user_dob date, in sex varchar(7))
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
-- Table structure for table authors
--

CREATE TABLE IF NOT EXISTS authors (
  author_id int(11) NOT NULL AUTO_INCREMENT,
  first_name varchar(55) NOT NULL,
  last_name varchar(55) NOT NULL,
  email varchar(55) NOT NULL,
  gender tinytext NOT NULL,
  cover_photo varchar(128) NOT NULL DEFAULT 'static/img/icon/user.png',
  description varchar(180) NOT NULL,
  PRIMARY KEY (author_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table books
--

CREATE TABLE IF NOT EXISTS books (
  book_id int(11) NOT NULL AUTO_INCREMENT,
  book_title varchar(55) NOT NULL,
  author_id int(11) NOT NULL,
  genre_id int(11) NOT NULL,
  rating int(11) NOT NULL,
  cover_photo int(11) NOT NULL,
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (book_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table genres
--

CREATE TABLE IF NOT EXISTS genres (
  genre_id int(11) NOT NULL,
  genre varchar(55) NOT NULL,
  PRIMARY KEY (genre_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table genres
--

INSERT INTO genres (genre_id, genre) VALUES
(0, 'General Works'),
(200, 'Religion'),
(300, 'Social Sciences'),
(400, 'Language'),
(500, 'Pure Science'),
(600, 'Technology'),
(700, 'Arts & Recreation'),
(800, 'Literature'),
(900, 'History'),
(920, 'Geography');

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE IF NOT EXISTS users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(30) NOT NULL,
  password varchar(64) NOT NULL,
  PRIMARY KEY (user_id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table users
--

INSERT INTO users (user_id, email, password) VALUES
(1, 'b4.oshany@gmail.com', '038300856746502133662bccc733b863');

-- --------------------------------------------------------

--
-- Table structure for table user_profile
--

CREATE TABLE IF NOT EXISTS user_profile (
  user_id int(11) NOT NULL,
  first_name varchar(55) DEFAULT NULL,
  last_name varchar(55) DEFAULT NULL,
  email varchar(65) DEFAULT NULL,
  gender varchar(7) DEFAULT NULL,
  occupation varchar(25) NOT NULL,
  dob date DEFAULT NULL,
  profile_pic varchar(255) NOT NULL DEFAULT 'static/img/icons/user.png',
  date_joined datetime NOT NULL,
  is_login tinyint(1) NOT NULL DEFAULT '1',
  last_seen timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT 'last logged in date',
  PRIMARY KEY (user_id),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table user_profile
--

INSERT INTO user_profile (user_id, first_name, last_name, email, gender, occupation, dob, profile_pic, date_joined, is_login, last_seen) VALUES
(1, 'Oshane', 'Bailey', 'b4.oshany@gmail.com', '1991-09', '', '0000-00-00', 'static/img/icons/user.png', '2014-11-11 00:00:00', 1, '0000-00-00 00:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
