# $Id$

# Joomla! 1.5.x to Joomla! 1.6 upgrade script
# --------------------------------------------------------
# Table structure for table `#__extensions`
CREATE TABLE  `jos_extensions` (
  `extensionid` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `element` varchar(100) NOT NULL default '',
  `folder` varchar(100) NOT NULL default '',
  `client_id` tinyint(3) NOT NULL default '0',
  `enabled` tinyint(3) NOT NULL default '1',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `protected` tinyint(3) NOT NULL default '0',
  `manifestcache` text NOT NULL,
  `params` text NOT NULL,
  `data` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) default '0',
  PRIMARY KEY  (`extensionid`)
) TYPE=MyISAM CHARACTER SET `utf8`;

# Earlier versions of this file didn't have the checkout and ordering fields
ALTER TABLE `jos_extensions` ADD COLUMN `checked_out` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `data`,
 ADD COLUMN `checked_out_time` DATETIME NOT NULL DEFAULT 0 AFTER `checked_out`,
 ADD COLUMN `ordering` INTEGER DEFAULT 0 AFTER `checked_out_time`;


# Migration script; adds modules, plugins and components to the extensions table
 TRUNCATE TABLE jos_extensions; 
 INSERT INTO jos_extensions SELECT 
 	0,				 		# extension id (regenerate)
 	name, 					# name
 	'plugin', 				# type
 	element, 				# element
 	folder, 					# folder
 	client_id, 				# client_id
 	published,				# enabled 
 	access, 					# access
 	iscore,				 	# protected
 	'', 						# manifestcache
 	params, 					# params
 	'', 						# data
 	checked_out, 			# checked_out
 	checked_out_time, 		# checked_out_time
 	ordering					# ordering
 	FROM jos_plugins; 		# #__extensions replaces the old #__plugins table
 	
 INSERT INTO jos_extensions SELECT 
 	0, 						# extension id (regenerate)
 	name, 					# name
 	'component', 			# type
 	`option`, 				# element
 	'', 						# folder
 	0, 						# client id (unused for components)
 	enabled,					# enabled 
 	0, 						# access
 	iscore, 					# protected
 	'', 						# manifest cache
 	params, 					# params
 	'', 						# data
 	'0', 					# checked_out
 	'0000-00-00 00:00:00', 	# checked_out_time
 	0						# ordering
 	FROM jos_components		# #__extensions replaces #__components for install uninstall
 							# component menu selection still utilises the #__components table
 	WHERE parent = 0;		# only get top level entries
 	
 INSERT INTO jos_extensions SELECT
 	0, 						# extension id (regenerate)
 	module, 					# name
 	'module', 				# type
 	`module`, 				# element
 	'', 						# folder
 	client_id,				# client id
 	1,						# enabled (module instances may be enabled/disabled in #__modules) 
 	0, 						# access (module instance access controlled in #__modules)
 	iscore,					# protected
 	'', 						# manifest cache
 	'',	 					# params (module instance params controlled in #__modules)
 	'', 						# data
 	'0', 					# checked_out (module instance, see #__modules)
 	'0000-00-00 00:00:00', 	# checked_out_time (module instance, see #__modules)
 	0						# ordering (module instance, see #__modules)
 	FROM jos_modules
 	WHERE id IN (SELECT id FROM jos_modules GROUP BY module ORDER BY id) 	