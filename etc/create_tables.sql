CREATE TABLE IF NOT EXISTS `Twitter` (
  `ID` varchar(20) NOT NULL DEFAULT '',
  `User` varchar(100) NOT NULL DEFAULT '',
  `UserID` varchar(64) NOT NULL DEFAULT '',
  `Date` datetime DEFAULT NULL,
  `Message` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ID`,`User`),
  KEY `Date` (`Date`),
  KEY `User` (`User`),
  KEY `ID` (`ID`),
  KEY `UserID` (`UserID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `TwitterUsers` (
  `ID` varchar(64) NOT NULL,
  `Username` varchar(128) NOT NULL,
  `Name` varchar(128) NOT NULL,
  `Image` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Username` (`Username`),
  KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `TwitterReplies` (
  `Child` varchar(20) NOT NULL,
  `Parent` varchar(20) NOT NULL,
  PRIMARY KEY (`Child`),
  KEY `Parent` (`Parent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `TwitterMedia` (
	`ID` VARCHAR(34) NOT NULL,
	`Tweet` VARCHAR(20) NOT NULL,
	`Ordering` TINYINT UNSIGNED NOT NULL,
	`Type` VARCHAR(10) NOT NULL,
	`URL` TEXT NOT NULL,
	PRIMARY KEY (`ID`),
	INDEX `Tweet` (`Tweet`),
	INDEX `Type` (`Type`),
	INDEX `Ordering` (`Ordering`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
