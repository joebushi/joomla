# $Id$

# Joomla! 1.5.x to Joomla! 1.6 upgrade script
# --------------------------------------------------------
# Table structure for table `#__extensions`
CREATE TABLE `jos_extensions` (
  `extensionid` INT  NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100)  NOT NULL,
  `type` VARCHAR(20)  NOT NULL,
  `element` VARCHAR(100) NOT NULL,
  `folder` VARCHAR(100) NOT NULL,
  `client_id` TINYINT(3) NOT NULL,
  `enabled` TINYINT(3) NOT NULL DEFAULT '1',
  `access` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',  
  `protected` TINYINT(3) NOT NULL DEFAULT '0', 
  `manifestcache` TEXT  NOT NULL,
  `params` TEXT NOT NULL,
  `data` TEXT NOT NULL,
  PRIMARY KEY (`extensionid`)
) TYPE=MyISAM CHARACTER SET `utf8`;


