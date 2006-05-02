# $Id: upgrade452to453.sql,v 1.5 2005/08/29 21:28:56 alekandreev Exp $

# Mambo 4.5.2 to Mambo 4.5.3

# Component Additions
INSERT INTO `mos_components` VALUES (0, 'Mass Mail', '', 0, 0, 'option=com_massmail', 'Send Mass Mail', 'com_massmail', 7, 'js/ThemeOffice/mass_email.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'Media Manager', '', 0, 0, 'option=com_media', 'Manage Media', 'com_media', 0, '', 1, 'enable_thumbnailer_adm=1\r\njpg_quality_admin=50\r\nimages_filetypes=jpg png gif bmp\r\ndocs_filetypes=pdf doc zip xls ppt sxw\r\nmax_upload_size=2097152\r\nroot_directories=images');

# Component Modifications
UPDATE `mos_components` SET `admin_menu_img` = 'js/ThemeOffice/globe2.png' WHERE `name` = 'Web Links';
UPDATE `mos_components` SET `admin_menu_img` = 'js/ThemeOffice/user.png' WHERE `name` = 'Contacts';
UPDATE `mos_components` SET `admin_menu_img` = 'js/ThemeOffice/edit.png' WHERE `name` = 'Manage Contacts';

# Mambot Additions
INSERT INTO `mos_mambots` VALUES (0, 'Mambo Userbot', 'mambo.userbot', 'user', 0, 1, 1, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `mos_mambots` VALUES (0, 'LDAP Userbot', 'ldap.userbot', 'user', 0, 1, 0, 1, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `mos_mambots` VALUES (0, 'Visitor Statistics', 'visitors', 'system', 0, 0, 0, 1, 0, 0, '0000-00-00 00:00:00', '');

# Mambot Modifications
UPDATE `mos_mambots` SET `params` = 'margin=5\r\npadding=5\r\nenable_thumbnailer_frontend=1\r\nprocess_img=1\r\nprocess_img_this_domain=1\r\nprocess_img_other_domains=0\r\nmax_image_width=300\r\nmax_image_height=300\r\ndefault_image_width=150\r\ndefault_image_height=150\r\nenforce_default_size=0\r\njpg_quality_frontend=70\r\noutput_all_as_jpg=0' WHERE `name` = 'MOS Image';

# Module Additions
INSERT INTO `mos_modules` VALUES (0, 'RSS', '', 4, 'right', 0, '0000-00-00 00:00:00', 0, 'mod_rss', 0, 0, 1, 'moduleclass_sfx=\nrssurl=http://news.mamboserver.com/index2.php?option=com_rss&feed=RSS1.0&no_html=1\nrssdesc=1\nrssimage=1\nrssitems=3\nrssitemdesc=1\nword_count=10\ncache=0', 0, 0);
INSERT INTO `mos_modules` VALUES (0, 'Linkbar', '', 0, 'user1', 0, '0000-00-00 00:00:00', 1, 'mod_linkbar', 0, 0, 0, '', 0, 1);        
INSERT INTO `mos_modules` VALUES (500, 'Footer', '', 1, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 0);
INSERT INTO `mos_modules` VALUES (0, 'Footer', '', 0, 'footer', 0, '0000-00-00 00:00:00', 1, 'mod_footer', 0, 0, 1, '', 1, 1);
INSERT INTO `mos_modules` VALUES (0, 'Logout Button', '', 3, 'header', 0, '0000-00-00 00:00:00', 1, 'mod_logoutbutton', 0, 0, 1, '', 1, 1);
INSERT INTO `mos_modules_menu` VALUES (500, 0);

# Fix Menu 
UPDATE `mos_menu` SET `link` = 'index.php?option=com_login' WHERE `menutype` = 'usermenu' AND `name` = 'Logout' AND `type` = 'components' LIMIT 1 ;

# Fix column names in phpgacl tables
ALTER TABLE `mos_core_acl_aro` CHANGE COLUMN `aro_id` `id` INTEGER NOT NULL AUTO_INCREMENT;
ALTER TABLE `mos_core_acl_aro_groups` CHANGE COLUMN `group_id` `id` INTEGER NOT NULL AUTO_INCREMENT;
ALTER TABLE `mos_core_acl_aro_sections` CHANGE COLUMN `section_id` `id` INTEGER NOT NULL AUTO_INCREMENT;

ALTER TABLE `mos_core_acl_aro_groups` ADD COLUMN `value` varchar(255) NOT NULL default '';
UPDATE `mos_core_acl_aro_groups` SET value=name;
ALTER TABLE `mos_core_acl_aro_groups` ADD UNIQUE `value_aro_groups`(`value`);
ALTER TABLE `mos_core_acl_aro_groups` DROP PRIMARY KEY, ADD PRIMARY KEY(`id`, `value`);

# Change column data length 
ALTER TABLE `mos_content` MODIFY COLUMN `title` varchar(255) NOT NULL default '';
ALTER TABLE `mos_content` MODIFY COLUMN `title_alias` varchar(255) NOT NULL default '';
ALTER TABLE `mos_categories` MODIFY COLUMN `title` varchar(255) NOT NULL default '';
ALTER TABLE `mos_sections` MODIFY COLUMN `title` varchar(255) NOT NULL default '';

# Fix Menu Stats 
UPDATE `mos_modules` SET `module` = 'mod_menustats' WHERE `module` = 'mod_stats' AND `client_id` = '1'

# Versioning
ALTER TABLE `mos_content` ADD `active` TINYINT DEFAULT '1' NOT NULL ;
ALTER TABLE `mos_content` ADD `revision` INT UNSIGNED NOT NULL AFTER `id` ;
UPDATE `mos_content` SET active = 1;
ALTER TABLE `mos_content` DROP PRIMARY KEY ,
ADD PRIMARY KEY ( `id` , `revision` ) ;

# Mambo Update
-- --------------------------------------------------------

-- 
-- Table structure for table `mos_update_cache`
-- 

CREATE TABLE `mos_update_cache` (
  `cacheid` int(10) unsigned NOT NULL auto_increment,
  `productname` varchar(50) default NULL,
  `productid` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `directory` varchar(50) default NULL,
  `versionstring` varchar(20) default NULL,
  `updateurl` text,
  `lastupdate` date default NULL,
  `updated` int(11) NOT NULL default '0',
  `updatedversion` varchar(20) NOT NULL default '',
  `releaseid` int(11) NOT NULL default '0',
  `releaseinfourl` text NOT NULL,
  `downloadurl` text NOT NULL,
  `remoteapp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cacheid`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `mos_versions`
-- Duplicate of mos_update_cache but designed to be there all the time
-- 

CREATE TABLE `mos_versions` (
  `cacheid` int(10) unsigned NOT NULL auto_increment,
  `productname` varchar(50) default NULL,
  `productid` int(11) NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `directory` varchar(50) default NULL,
  `versionstring` varchar(20) default NULL,
  `updateurl` text,
  `lastupdate` date default NULL,
  `updated` int(11) NOT NULL default '0',
  `updatedversion` varchar(20) NOT NULL default '',
  `releaseid` int(11) NOT NULL default '0',
  `releaseinfourl` text NOT NULL,
  `downloadurl` text NOT NULL,
  `remoteapp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cacheid`)
) TYPE=MyISAM;


-- --------------------------------------------------------

-- 
-- Table structure for table `mos_update_dependencies`
-- 

CREATE TABLE `mos_update_dependencies` (
  `dependencyid` int(10) unsigned NOT NULL auto_increment,
  `currentrelease` int(10) unsigned default NULL,
  `depprodname` varchar(50) default NULL,
  `depversionstring` varchar(20) default NULL,
  `depremotesite` int(10) unsigned default NULL,
  `upgradeonly` int(10) NOT NULL default '0',
  PRIMARY KEY  (`dependencyid`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `mos_update_product`
-- 

CREATE TABLE `mos_update_product` (
  `productid` int(10) unsigned NOT NULL auto_increment,
  `productname` varchar(50) default NULL,
  `producttype` varchar(20) NOT NULL default '',
  `productdescription` text,
  `producturl` text,
  `productdetailsurl` text,
  `published` int(11) NOT NULL default '0',
  PRIMARY KEY  (`productid`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `mos_update_releases`
-- 

CREATE TABLE `mos_update_releases` (
  `releaseid` int(10) unsigned NOT NULL auto_increment,
  `productid` int(11) default NULL,
  `releasetitle` varchar(30) default NULL,
  `releasedescription` text,
  `releasesurl` text,
  `releasechangelog` text,
  `releasenotes` text,
  `versionstring` varchar(20) default NULL,
  `updateurl` text,
  `releaseurl` text,
  `releasedate` date default NULL,
  `published` int(11) NOT NULL default '0',
  PRIMARY KEY  (`releaseid`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `mos_update_remotesite`
-- 

CREATE TABLE `mos_update_remotesite` (
  `remotesiteid` int(10) unsigned NOT NULL auto_increment,
  `remotesitename` varchar(30) default NULL,
  `remotesiteurl` text,
  PRIMARY KEY  (`remotesiteid`)
) TYPE=MyISAM;

INSERT INTO `mos_components` VALUES (0, 'Mambo Update Server', 'option=com_update_server', 0, 0, '', '', 'com_update_server', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'Products', '', 0, 20, 'option=com_update_server', 'Manage Products', '', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'Releases', '', 0, 20, 'option=com_update_server&task=listreleases', 'Manage Releases', '', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'Remote Sites', '', 0, 20, 'option=com_update_server&task=listremotesites', 'Manage Remote Sites', '', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'Dependencies', '', 0, 20, 'option=com_update_server&task=listdependencies', 'Manage Package Dependencies', '', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'Mambo Update Client', 'option=com_update_client', 0, 0, '', '', 'com_update_client', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'View Installed Packages', '', 0, 25, 'option=com_update_client', 'Shows a listing of all components capable of being installed', '', 0, 'js/ThemeOffice/component.png', 0, '');
INSERT INTO `mos_components` VALUES (0, 'Update Package Lists', '', 0, 25, 'option=com_update_client&task=update', 'Downloads package lists from component websites and remote sites', '', 0, 'js/ThemeOffice/component.png', 0, '');


INSERT INTO `mos_mambots` VALUES (0, 'Mambo Update Server', 'mambo_update', 'xmlrpc', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `mos_mambots` VALUES (0, 'Dependency Checker', 'dependency.check', 'system', 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', '');
INSERT INTO `mos_mambots` VALUES (0, 'Update Cache Rebuilder', 'update.buildcache', 'system', 0, 0, 1, 0, 0, 0, '0000-00-00 00:00:00', '');
