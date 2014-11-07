--
-- Database: scpls
--

-- --------------------------------------------------------

--
-- Table structure for table authors
--

CREATE TABLE IF NOT EXISTS authors (
author_id int(11) NOT NULL,
  first_name varchar(15) NOT NULL,
  last_name varchar(15) NOT NULL,
  email varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table books
--

CREATE TABLE IF NOT EXISTS books (
id int(11) NOT NULL,
  title int(11) NOT NULL,
  isbn int(11) NOT NULL,
  description int(11) NOT NULL,
  genre_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table book_authors
--

CREATE TABLE IF NOT EXISTS book_authors (
  author_id int(11) NOT NULL,
  book_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table genres
--

CREATE TABLE IF NOT EXISTS genres (
  genre_id int(11) NOT NULL,
  genre varchar(50) NOT NULL,
  start_range int(11) NOT NULL,
  end_range int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE IF NOT EXISTS users (
  uid int(11) NOT NULL,
  first_name int(11) NOT NULL,
  last_name int(11) NOT NULL,
  email int(11) NOT NULL,
  password int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table authors
--
ALTER TABLE authors
 ADD PRIMARY KEY (author_id);

--
-- Indexes for table books
--
ALTER TABLE books
 ADD PRIMARY KEY (id), ADD UNIQUE KEY isbn (isbn);

--
-- Indexes for table genres
--
ALTER TABLE genres
 ADD PRIMARY KEY (genre_id), ADD UNIQUE KEY start_range (start_range,end_range);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table authors
--
ALTER TABLE authors
MODIFY author_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table books
--
ALTER TABLE books
MODIFY id int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
