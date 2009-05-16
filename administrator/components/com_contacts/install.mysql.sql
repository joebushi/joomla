-- $Id$

-- --------------------------------------------------------

--
-- Table structure for table `#__contacts_contacts`
--

CREATE TABLE IF NOT EXISTS `#__contacts_contacts` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text,
  `user_id` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__contacts_con_cat_map`
--

CREATE TABLE IF NOT EXISTS `#__contacts_con_cat_map` (
  `contact_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`contact_id`,`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__contacts_details`
--

CREATE TABLE IF NOT EXISTS `#__contacts_details` (
  `contact_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `data` text character set utf8 NOT NULL,
  `show_contact` tinyint(1) NOT NULL default '1',
  `show_directory` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`contact_id`,`field_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__contacts_fields`
--

CREATE TABLE IF NOT EXISTS `#__contacts_fields` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` mediumtext,
  `type` varchar(50) NOT NULL default 'text',
  `published` tinyint(1) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `pos` enum('title','top','left','main','right','bottom') NOT NULL default 'main',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `params` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `#__contacts_fields`
--

INSERT INTO `#__contacts_fields` (`title`, `description`, `type`, `published`, `ordering`, `checked_out`, `checked_out_time`, `pos`, `access`, `params`) VALUES
('E-mail', 'The email address used for receiving emails from the contact form.', 'email', 1, 1, 0, '0000-00-00 00:00:00', 'main', 0, 'use=0\nrequired=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Contact''s Position', '', 'text', 1, 1, 0, '0000-00-00 00:00:00', 'title', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Street Address', '', 'textarea', 1, 2, 0, '2008-07-27 22:43:58', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Town/Suburb', '', 'text', 1, 3, 0, '2008-07-27 22:44:38', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('State/County', '', 'text', 1, 4, 0, '2008-07-27 22:45:21', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Postal Code/ZIP', '', 'text', 1, 5, 0, '2008-07-27 22:46:08', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Country', '', 'text', 1, 6, 0, '2008-07-27 22:47:02', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Telephone', '', 'text', 1, 7, 0, '2008-07-27 22:47:40', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Mobile Phone Number', '', 'text', 1, 8, 0, '2008-07-27 22:48:18', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Fax', '', 'text', 1, 9, 0, '2008-07-27 22:48:40', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Web URL', '', 'url', 1, 10, 0, '2008-07-27 22:49:18', 'main', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Miscellaneous Information', '', 'editor', 1, 1, 0, '0000-00-00 00:00:00', 'bottom', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n'),
('Contact Image', '', 'image', 1, 1, 0, '0000-00-00 00:00:00', 'right', 0, 'required=0\ncss_tag=\nfield_title=0\nchoose_icon=\n\n');

-- --------------------------------------------------------

--
-- Dumping data for table `#__categories`
--

INSERT INTO `#__categories` (`parent_id`, `title`, `name`, `alias`, `image`, `section`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `editor`, `ordering`, `access`, `count`, `params`) VALUES
(0, 'Contacts', '', 'contacts', '', 'com_contacts', '', '', 1, 0, '0000-00-00 00:00:00', NULL, 1, 0, 0, '');