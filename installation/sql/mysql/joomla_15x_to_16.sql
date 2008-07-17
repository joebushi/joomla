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


ALTER TABLE `jos_extensions` ADD COLUMN `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `data`,
 ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT 0 AFTER `checked_out`,
 ADD COLUMN `ordering` INTEGER DEFAULT 0 AFTER `checked_out_time`;

 
 INSERT INTO jos_extensions SELECT id AS extensionid, name, 'plugin', element, folder, client_id, published, access, iscore AS protected, '', params, '', checked_out, checked_out_time, ordering FROM jos_plugins
 