-- Server version: 5.1.41

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `cvxml`
--

-- --------------------------------------------------------

--
-- Table structure for table `mob_driving_licence`
--

CREATE TABLE IF NOT EXISTS `mob_driving_licence` (
  `ID` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `XML_ID` int(10) NOT NULL COMMENT 'Person''s id (FK)',
  `DRIVING_SKILL` varchar(3) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `ID_2` (`ID`),
  KEY `XML_ID` (`XML_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to store the person''s driving skills' AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `mob_education`
--

CREATE TABLE IF NOT EXISTS `mob_education` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `XML_ID` int(10) NOT NULL,
  `TITLE` varchar(200) DEFAULT NULL,
  `SUBJECT` varchar(200) DEFAULT NULL,
  `ORG_NAME` varchar(100) DEFAULT NULL,
  `ORG_ADDRESS` varchar(100) DEFAULT NULL,
  `ORG_MUNIC` varchar(50) DEFAULT NULL,
  `ORG_ZCODE` varchar(10) DEFAULT NULL,
  `CODE_COUNTRY` varchar(3) DEFAULT NULL,
  `COUNTRY` varchar(50) DEFAULT NULL,
  `CODE_LEVEL` varchar(2) DEFAULT NULL,
  `EDULEVEL` varchar(1024) DEFAULT NULL,
  `CODE_EDU_FIELD` varchar(5) DEFAULT NULL,
  `EDU_FIELD` varchar(1024) DEFAULT NULL,
  `DAY_FROM` varchar(2) DEFAULT NULL,
  `MONTH_FROM` varchar(2) DEFAULT NULL,
  `YEAR_FROM` varchar(4) DEFAULT NULL,
  `DAY_TO` varchar(2) DEFAULT NULL,
  `MONTH_TO` varchar(2) DEFAULT NULL,
  `YEAR_TO` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `XML_ID` (`XML_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to store the Education list items' AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `mob_language`
--

CREATE TABLE IF NOT EXISTS `mob_language` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `XML_ID` int(10) NOT NULL,
  `CODE_LANGUAGE` varchar(3) DEFAULT NULL,
  `OLANGUAGE` varchar(30) DEFAULT NULL,
  `LISTENING` varchar(2) DEFAULT NULL,
  `READING` varchar(2) DEFAULT NULL,
  `SPOKEN_INTERACTION` varchar(2) DEFAULT NULL,
  `SPOKEN_PRODUCTION` varchar(2) DEFAULT NULL,
  `WRITING` varchar(2) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `XML_ID` (`XML_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to store the Other Language list Items' AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `mob_nationality`
--

CREATE TABLE IF NOT EXISTS `mob_nationality` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `XML_ID` int(10) NOT NULL COMMENT 'Person''s id (FK)',
  `CODE` varchar(10) DEFAULT NULL COMMENT 'Nationality Code',
  `NATIONALITY` varchar(32) DEFAULT NULL COMMENT 'Nationality',
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `ID_2` (`ID`),
  KEY `XML_ID` (`XML_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to store the nationalities of a person' AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `mob_work_experience`
--

CREATE TABLE IF NOT EXISTS `mob_work_experience` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `XML_ID` int(10) NOT NULL,
  `DAY_FROM` varchar(2) DEFAULT NULL,
  `MONTH_FROM` varchar(2) DEFAULT NULL,
  `YEAR_FROM` varchar(4) DEFAULT NULL,
  `DAY_TO` varchar(2) DEFAULT NULL,
  `MONTH_TO` varchar(2) DEFAULT NULL,
  `YEAR_TO` varchar(4) DEFAULT NULL,
  `WPOSITION` varchar(1024) DEFAULT NULL,
  `ACTIVITIES` varchar(1024) DEFAULT NULL,
  `EMPLOYER_NAME` varchar(100) DEFAULT NULL,
  `EMPLOYER_ADDRESS` varchar(100) DEFAULT NULL,
  `EMPLOYER_MUNIC` varchar(50) DEFAULT NULL,
  `EMPLOYER_ZCODE` varchar(10) DEFAULT NULL,
  `CODE_COUNTRY` varchar(3) DEFAULT NULL,
  `COUNTRY` varchar(50) DEFAULT NULL,
  `CODE_SECTOR` varchar(3) DEFAULT NULL,
  `SECTOR` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `ID_2` (`ID`),
  KEY `ID_3` (`ID`),
  KEY `XML_ID` (`XML_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to store the Work Experience list items' AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `mob_xml`
--

CREATE TABLE IF NOT EXISTS `mob_xml` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `FNAME` varchar(30) DEFAULT NULL,
  `LNAME` varchar(30) DEFAULT NULL,
  `ADDRESS` varchar(50) DEFAULT NULL,
  `MUNIC` varchar(50) DEFAULT NULL,
  `POSTAL_CODE` varchar(10) DEFAULT NULL,
  `CODE_COUNTRY` varchar(5) DEFAULT NULL,
  `COUNTRY` varchar(30) DEFAULT NULL,
  `PHONE` varchar(30) DEFAULT NULL,
  `PHONE2` varchar(30) DEFAULT NULL,
  `PHONE3` varchar(30) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `GENDER` varchar(2) DEFAULT NULL,
  `BIRTHDATE` varchar(10) DEFAULT NULL,
  `PHOTO_TYPE` varchar(10) DEFAULT NULL,
  `PHOTO` blob,
  `CODE_APPLICATION` varchar(50) DEFAULT NULL,
  `APPLICATION` varchar(1024) DEFAULT NULL,
  `CODE_MOTHER_LANGUAGE` varchar(5) DEFAULT NULL,
  `MOTHER_LANGUAGE` varchar(100) DEFAULT NULL,
  `SOCIAL` varchar(1024) DEFAULT NULL,
  `ORGANISATIONAL` varchar(1024) DEFAULT NULL,
  `JOB_RELATED` varchar(1024) DEFAULT NULL,
  `COMPUTER` varchar(1024) DEFAULT NULL,
  `OTHER` varchar(1024) DEFAULT NULL,
  `ADDITIONAL` varchar(1024) DEFAULT NULL,
  `ANNEXES` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Table to store the the XML main data' AUTO_INCREMENT=14 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mob_driving_licence`
--
ALTER TABLE `mob_driving_licence`
  ADD CONSTRAINT `mob_driving_licence_ibfk_1` FOREIGN KEY (`XML_ID`) REFERENCES `mob_xml` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mob_education`
--
ALTER TABLE `mob_education`
  ADD CONSTRAINT `mob_education_ibfk_1` FOREIGN KEY (`XML_ID`) REFERENCES `mob_xml` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mob_language`
--
ALTER TABLE `mob_language`
  ADD CONSTRAINT `mob_language_ibfk_1` FOREIGN KEY (`XML_ID`) REFERENCES `mob_xml` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mob_nationality`
--
ALTER TABLE `mob_nationality`
  ADD CONSTRAINT `mob_nationality_ibfk_1` FOREIGN KEY (`XML_ID`) REFERENCES `mob_xml` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mob_work_experience`
--
ALTER TABLE `mob_work_experience`
  ADD CONSTRAINT `mob_work_experience_ibfk_1` FOREIGN KEY (`XML_ID`) REFERENCES `mob_xml` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
