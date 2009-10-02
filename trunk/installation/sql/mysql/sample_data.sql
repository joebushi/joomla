# @version		$Id$
#
# IMPORTANT - THIS FILE MUST BE SAVED WITH UTF-8 ENCODING ONLY. BEWARE IF EDITING!
#

-- 
-- Dumping data for table `#__access_action_rule_map`
-- 

INSERT IGNORE INTO `#__access_action_rule_map` VALUES 
(1, 35);

--
-- Dumping data for table `#__access_asset_assetgroups`
--

INSERT IGNORE INTO `#__access_assetgroups` VALUES
(4, 1, 2, 3, 'Confidential', 1, 'core');

--
-- Dumping data for table `#__access_assetgroup_rule_map`
--

INSERT IGNORE INTO `#__access_assetgroup_rule_map` VALUES 
(4, 35);

--
-- Dumping data for table `#__access_assets`
--


--
-- Dumping data for table `#__access_asset_assetgroup_map`
--


-- 
-- Dumping data for table `#__access_asset_rule_map`
-- 

--
-- Dumping data for table `#__access_rules`
--

INSERT IGNORE INTO `#__access_rules` VALUES 
(35, 1, 'core', 'core.view.4', 'SYSTEM', NULL, 0, 1, 1, 3, 0, NULL);

--
-- Dumping data for table `#__banner`
--

INSERT IGNORE INTO `#__banner` VALUES
(1, 1, '', 'OSM 1', 'osm-1', 0, 43, 0, 'osmbanner1.png', 'http://www.opensourcematters.org', '2009-09-26 03:03:00', 1, 0, '0000-00-00 00:00:00', '', '', 32, '', 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 'width=0\nheight=0');

--
-- Dumping data for table `#__bannerclient`
--

INSERT IGNORE INTO `#__bannerclient` VALUES
(1, 'Open Source Matters', 'Administrator', 'admin@opensourcematters.org', '', 0, '00:00:00', NULL);

--
-- Dumping data for table `#__categories`
--

REPLACE INTO `#__categories` VALUES
(1, 0, 0, 23, 0, '', 'system', 'ROOT', 'root', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-06-22 20:25:13', 0, '0000-00-00 00:00:00', 0, ''),
(11, 1, 1, 2, 1, 'news', 'com_content', 'News', 'news', 'The top articles category.', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-06-22 19:42:11', 0, '0000-00-00 00:00:00', 0, 'en_GB'),
(12, 1, 9, 16, 1, 'countries', 'com_content', 'Countries', 'countries', 'The latest news from the Joomla! Team', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-06-22 20:25:13', 0, '0000-00-00 00:00:00', 0, 'en_GB'),
(20, 1, 3, 8, 1, 'weblinks', 'com_weblinks', 'Weblinks', 'weblinks', 'The top weblinks category.', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-06-22 19:42:11', 0, '0000-00-00 00:00:00', 0, 'en_GB'),
(21, 20, 4, 7, 2, 'weblinks/joomla-specific-links', 'com_weblinks', 'Joomla! Specific Links', 'joomla-specific-links', 'A selection of links that are all related to the Joomla! Project.', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-06-22 19:42:11', 0, '0000-00-00 00:00:00', 0, 'en_GB'),
(22, 21, 5, 6, 3, 'weblinks/joomla-specific-links/other-resources', 'com_weblinks', 'Other Resources', 'other-resources', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2009-06-22 19:42:11', 0, '0000-00-00 00:00:00', 0, 'en_GB'),
(23, 12, 10, 15, 2, 'countries/australia', 'com_content', 'Australia', 'australia', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-06-22 20:25:13', 0, '0000-00-00 00:00:00', 0, ''),
(24, 23, 11, 12, 3, 'countries/australia/queensland', 'com_content', 'Queensland', 'queensland', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-06-22 20:25:17', 0, '0000-00-00 00:00:00', 0, ''),
(25, 23, 13, 14, 3, 'countries/australia/tasmania', 'com_content', 'Tasmania', 'tasmania', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-06-22 20:25:17', 0, '0000-00-00 00:00:00', 0, ''),
(26, 1, 17, 22, 1, 'articles', 'com_content', 'Articles', 'articles', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-09-22 22:44:29', 0, '0000-00-00 00:00:00', 0, ''),
(27, 26, 18, 19, 2, 'articles/joomla-users', 'com_content', 'Joomla! Users', 'joomla-users', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-09-22 22:34:09', 0, '0000-00-00 00:00:00', 0, ''),
(30, 1, 23, 24, 1, 'contact-category', 'com_contact', 'contact category', 'contact-category', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-09-25 23:00:41', 0, '0000-00-00 00:00:00', 0, ''),
(31, 1, 25, 26, 1, 'feeds', 'com_newsfeeds', 'Feeds', 'feeds', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-09-25 23:01:20', 0, '0000-00-00 00:00:00', 0, ''),
(32, 1, 27, 28, 1, 'banners', 'com_banners', 'Banners', 'banners', '', 1, 0, '0000-00-00 00:00:00', 1, '', '', '', '', 0, '2009-09-25 23:02:28', 0, '0000-00-00 00:00:00', 0, '');



--
-- Dumping data for table `#__contact_details`
--

INSERT IGNORE INTO `#__contact_details` VALUES
(1, 'Name', 'name', 'Position', 'Street', 'Suburb', 'State', 'Country', 'Zip Code', 'Telephone', 'Fax', 'Miscellanous info', 'powered_by.png', 'top', 'email@email.com', 1, 1, 42, '2009-09-26 02:59:08', 1, '{"show_name":"1","show_position":"1","show_email":"0","show_street_address":"1","show_suburb":"1","show_state":"1","show_postcode":"1","show_country":"1","show_telephone":"1","show_mobile":"1","show_fax":"1","show_webpage":"1","show_misc":"1","show_image":"1","allow_vcard":"0","show_articles":"1","show_links":"1","linka_name":"","linka":"","linkb_name":"","linkb":"","linkc_name":"","linkc":"","linkd_name":"","linkd":"","linke_name":"","linke":""}', 0, 30, 1, '', '');


--
-- Dumping data for table `#__content`
--

INSERT IGNORE INTO `#__content` VALUES
(1, 0, 'Welcome to Joomla!', 'welcome-to-joomla', '', '<p>Introtext</p>', '<p>Bodytext</p>', 1, 1, 0, 10, '2008-08-12 10:00:00', 42, '', '2008-08-12 10:00:00', 42, 0, '0000-00-00 00:00:00', '2006-01-03 01:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_section":"","link_section":"","show_category":"","link_category":"","show_vote":"","show_author":"","show_create_date":"","show_modify_date":"","show_print_icon":"","show_email_icon":"","language":"en-GB","keyref":"","readmore":""}', 29, 0, 1, '', '', 1, 102, '{"robots":"","author":""}', 1, 'en-GB', ''),
(2, 0, 'Great Barrier Reef', 'great-barrier-reef', '', '<p>The Great Barrier Reef is the largest coral reef system composed of over 2,900 individual reefs[3] and 900 islands stretching for over 3,000 kilometres (1,600 mi) over an area of approximately 344,400 square kilometres (133,000 sq mi). The reef is located in the Coral Sea, off the coast of Queensland in northeast Australia.</p>\r\n<p>http://en.wikipedia.org/wiki/Great_Barrier_Reef</p>', '<p>The Great Barrier Reef can be seen from outer space and is the world''s biggest single structure made by living organisms. This reef structure is composed of and built by billions of tiny organisms, known as coral polyps. The Great Barrier Reef supports a wide diversity of life, and was selected as a World Heritage Site in 1981.CNN has labelled it one of the 7 natural wonders of the world. The Queensland National Trust has named it a state icon of Queensland.</p>\r\n<p>A large part of the reef is protected by the Great Barrier Reef Marine Park, which helps to limit the impact of human use, such as overfishing and tourism. Other environmental pressures to the reef and its ecosystem include water quality from runoff, climate change accompanied by mass coral bleaching, and cyclic outbreaks of the crown-of-thorns starfish.</p>\r\n<p>The Great Barrier Reef has long been known to and utilised by the Aboriginal Australian and Torres Strait Islander peoples, and is an important part of local groups'' cultures and spirituality. The reef is a very popular destination for tourists, especially in the Whitsundays and Cairns regions. Tourism is also an important economic activity for the region. Fishing also occurs in the region, generating AU$ 1 billion per year.</p>', 1, 0, 0, 24, '2009-06-22 11:07:08', 42, '', '2009-06-22 11:14:50', 42, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","article-allow_ratings":"","article-allow_comments":"","show_author":"","show_create_date":"","show_modify_date":"","show_print_icon":"","show_email_icon":"","readmore":"","page_title":"","layout":""}', 1, 0, 0, '', '', 1, 0, '{"robots":"","author":""}', 0, '', ''),
(3, 0, 'Cradle Mountain-Lake St Clair National Park', 'cradle-mountain-lake-st-clair-national-park', '', '<p>Cradle Mountain-Lake St Clair National Park is located in the Central Highlands area of Tasmania (Australia), 165 km northwest of Hobart. The park contains many walking trails, and is where hikes along the well-known Overland Track usually begins. Major features are Cradle Mountain and Barn Bluff in the northern end, Mount Pelion East, Mount Pelion West, Mount Oakleigh and Mount Ossa in the middle and Lake St Clair in the southern end of the park. The park is part of the Tasmanian Wilderness World Heritage Area.</p>\r\n<p>http://en.wikipedia.org/wiki/Cradle_Mountain-Lake_St_Clair_National_Park</p>', '<h3>Access and usage fee</h3>\r\n<p>Access from the south (Lake St. Clair) is usually from Derwent Bridge on the Lyell Highway. Northern access (Cradle Valley) is usually via Sheffield, Wilmot or Mole Creek. A less frequently used entrance is via the Arm River Track, from the east.</p>\r\n<p>In 2005, the Tasmanian Parks & Wildlife Service introduced a booking system & fee for use of the Overland Track over peak periods. Initially the fee was 100 Australian dollars, but this was raised to 150 Australian dollars in 2007. The money that is collected is used to finance the park ranger organisation, track maintenance, building of new facilities and rental of helicopter transport to remove waste from the toilets at the huts in the park.</p>', 1, 0, 0, 25, '2009-06-22 11:17:24', 42, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","article-allow_ratings":"","article-allow_comments":"","show_author":"","show_create_date":"","show_modify_date":"","show_print_icon":"","show_email_icon":"","readmore":"","page_title":"","layout":""}', 1, 0, 0, '', '', 1, 0, '{"robots":"","author":""}', 0, '', ''),
(4, 2, 'Joomla!', 'joomla', '', 'Congratulations, You have a Joomla! site! Joomla! makes your site easy to build a website just the way you want it and keep it simple to update and maintain. Joomla! is a flexible and powerful platform, whether you are building a small site for yourself or a huge site with hundreds of thousands of visitors. Joomla is open source, which means you can make it work just the way you want it to.', '', 1, 0, 0, 27, '2009-09-23 02:20:52', 42, '', '2009-09-26 07:59:42', 42, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_author":"","show_create_date":"","show_modify_date":"","show_readmore":"","show_print_icon":"","show_email_icon":"","page_title":"","layout":"","article-allow_ratings":"","article-allow_comments":""}', 4, 0, 0, 'Joomla', '', 1, 1, '{"robots":"","author":""}', 1, '', ''),
(5, 3, ' Beginners', 'joomla-beginners', '', 'If this is your first Joomla site or your first web site, you have come to the right place. Joomla will help you get your website up and running quickly and easily.  Start off using your site by logging in using the administrator account you created when you installed. Explore the articles and other resources right here on your site data to learn more about how Joomla works. (When you''re done reading, you can delete or archive all of this.)\r\n\r\n\r\n You will also probably want to visit the beginners'' areas of the [Joomla documentation sote] and [forums].  Also, be sure to sign up for the Joomla Security mailing list and the Announcements mailing list. For inspiration visit the Joomla Site Showcase to see an amazing array of ways people use Joomla to tell their stories on the web. The basic Joomla! installation will let you get a great site up and running, but when you are ready for more features the power of Joomla! is in the creative ways that developers have extended it to do all kinds of things. Visit the Joomla! Extensions Directory to see thousands of extensions that can do almost anything you could want on a website. Can''t find what you need? You may want to find a Joomla professional on the Joomla! Resources Directory. Want to learn more? Consider attending a Joomla! Day or other event or joining a local Joomla! Users Group. Can''t find one near you? Start one yourself.', '', 1, 0, 0, 27, '2009-09-23 02:21:48', 42, '', '2009-09-26 08:05:34', 42, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_author":"","show_create_date":"","show_modify_date":"","show_readmore":"","show_print_icon":"","show_email_icon":"","page_title":"","layout":"","article-allow_ratings":"","article-allow_comments":""}', 5, 0, 0, '', '', 1, 2, '{"robots":"","author":""}', 1, '', ''),
(6, 4, 'Upgraders', 'upgraders', '', 'If you are an experienced Joomla! 1.5 user, 1.6 will seem very familiar. There are new templates and improved user interfaces, but most functionality is the same. The biggest changes are improved access control (ACL), nested categories and comments.\r\nThe new user manager which will let you manage who has access to what in your site. You can leave access groups exactly the way you had them in Joomla 1.5 or make them as complicated as you want. You can learn more about how access control works [in this article] and on the [Joomla Documentation site].\r\nIn Joomla 1.5 and 1.0 content was organized into sections and categories. In 1.6 sections are gone, and you can create categories within categories, going as deep as you want. You can learn more about how categories work in 1.6 [in this article] and [on the Joomla Documentation site].\r\nComments are now integrated into all front end components. You can control what content has comments enable, who can comment, and much more. You can learn more about comments [in this article] and [on the Joomla Documentation site].\r\nAll layouts have been redesigned to improve accessibility and flexibility. If you would like to keep the 1.5 layouts, you can find them in the html folder of the MilkyWay template. Simply copy the layouts you want to the html folder of your template.  Updating your site and extensions when needed is easier than ever thanks to installer improvements.  To learn more about how to move a Joomla 1.5 site to a Joomla 1.6 installation [read this].', '', 1, 0, 0, 27, '2009-09-23 02:22:31', 42, '', '2009-09-26 07:52:38', 42, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_author":"","show_create_date":"","show_modify_date":"","show_readmore":"","show_print_icon":"","show_email_icon":"","page_title":"","layout":"","article-allow_ratings":"","article-allow_comments":""}', 2, 0, 0, '', '', 1, 2, '{"robots":"","author":""}', 1, '', ''),
(7, 5, 'Developers and Designers', 'developers-and-designers', '', 'Joomla! 1.6  continues development of the Joomla Framework and CMS as a powerful and flexible way to bring your vision of the web to reality.\r\nWith the administrator now fully MVC, the ability to control its look and the management of extensions is now complete. Languages files can now be overridden and working with multiple templates and overrides for the same views, creating the design you want is easier than it has ever been. Limiting support to PHP 5.x and above and ending legacy support for Joomla 1.0 makes Joomla lighter and faster than ever.\r\nAccess control lists are now incorporated using a new system developed for Joomla. The ACL system is designed with developers in mind, so it is easy to incorporate into your extensions.  The new nested sets libraries allow you to incorporate infinitely deep categories but also to use nested sets in a variety of other ways.  A new forms library makes creating all kinds of user interaction simple.\r\nMooTools 1.2 provides a highly flexible javascript framework that is a major advance over MooTools 1.0.  New events throughout the core make integration of your plugins where you want them a snap.\r\nLearn about:\r\n\r\n [working with ACL] \r\n [working with nested sets] \r\n [integrating comments] \r\n [using the forms library] \r\n [working with Mootools 1.2] \r\n [using the override system] \r\n [Joomla! API] \r\n [Database] \r\n [Triggers] \r\n [Xmlrpc] \r\n [Installing and updating extensions] \r\n [Setting up your development environment] \r\n\r\n ', '', 1, 0, 0, 27, '2009-09-23 02:23:09', 42, '', '2009-09-26 07:57:43', 42, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_author":"","show_create_date":"","show_modify_date":"","show_readmore":"","show_print_icon":"","show_email_icon":"","page_title":"","layout":"","article-allow_ratings":"","article-allow_comments":""}', 2, 0, 0, '', '', 1, 4, '{"robots":"","author":""}', 1, '', ''),
(8, 7, 'What''s New in 1.5?', 'whats-new-in-15', '', 'As with previous releases, Joomla! provides a unified and easy-to-use framework for delivering content for Web sites of all kinds. To support the changing nature of the Internet and emerging Web technologies, Joomla! required substantial restructuring of its core functionality and we also used this effort to simplify many challenges within the current user interface. Joomla! 1.5 has many new features.\r\n\r\nIn Joomla! 1.5, you''ll notice:\r\n\r\n\r\nSubstantially improved usability, manageability, and scalability far beyond the original Mambo foundations\r\n\r\n\r\nExpanded accessibility to support internationalisation, double-byte characters and right-to-left support for Arabic, Farsi, and Hebrew languages among others\r\n\r\n\r\nExtended integration of external applications through Web services and remote authentication such as the Lightweight Directory Access Protocol (LDAP)\r\n\r\n\r\nEnhanced content delivery, template and presentation capabilities to support accessibility standards and content delivery to any destination\r\n\r\n\r\nA more sustainable and flexible framework for Component and Extension developers\r\n\r\n\r\nBackward compatibility with previous releases of Components, Templates, Modules, and other Extensions\r\n\r\n', '', -1, 0, 0, 11, '2009-09-26 06:27:19', 42, '', '0000-00-00 00:00:00', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_author":"","show_create_date":"","show_modify_date":"","show_readmore":"","show_print_icon":"","show_email_icon":"","page_title":"","layout":"","article-allow_ratings":"","article-allow_comments":""}', 1, 0, 0, '', '', 1, 0, '{"robots":"","author":""}', 0, '', '');

--
-- Dumping data for table `#__content_frontpage`
--

INSERT IGNORE INTO `#__content_frontpage` VALUES
(6, 2),
(4, 4),
(5, 3),
(7, 1);

--
-- Dumping data for table `#__menu`
--



REPLACE INTO `#__menu` VALUES
(1, '', 'Menu_Item_Root', 'root', '', '', '', 1, 0, 0, 0, 0, 0, '0000-00-00 00:00:00', 0, 0, 0, '', 0, 33, 0),
(2, 'mainmenu', 'Home', 'home', 'home', 'index.php?option=com_content&view=frontpage', 'component', 1, 1, 1, 20, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, 'show_page_title=1\r\npage_title=Welcome to the Frontpage\r\nshow_description=0\r\nshow_description_image=0\r\nnum_leading_articles=1\r\nnum_intro_articles=4\r\nnum_columns=2\r\nnum_links=4\r\nshow_title=1\r\npageclass_sfx=\r\nmenu_image=-1\r\nsecure=0\r\norderby_pri=\r\norderby_sec=front\r\nshow_pagination=2\r\nshow_pagination_results=1\r\nshow_noauth=0\r\nlink_titles=0\r\nshow_intro=1\r\nshow_section=0\r\nlink_section=0\r\nshow_category=0\r\nlink_category=0\r\nshow_author=1\r\nshow_create_date=1\r\nshow_modify_date=1\r\nshow_item_navigation=0\r\nshow_readmore=1\r\nshow_vote=0\r\nshow_icons=1\r\nshow_pdf_icon=1\r\nshow_print_icon=1\r\nshow_email_icon=1\r\nshow_hits=1\r\n\r\n', 1, 2, 1),
(11, 'mainmenu', 'Wrapper', 'wrapper', 'wrapper', 'index.php?option=com_wrapper&view=wrapper', 'component', 1, 1, 1, 17, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"url":"http:\\/\\/joomlacode.org","scrolling":"auto","width":"100%","height":"500","height_auto":"0","add_scheme":"1","menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 31, 32, 0),
(3, 'mainmenu', 'Administrator', 'administrator', 'administrator', 'administrator/', 'url', 1, 1, 1, 0, 2, 0, '0000-00-00 00:00:00', 0, 1, 0, 'menu_image=-1\r\n\r\n', 7, 8, 0),
(4, 'usermenu', 'Your Details', 'your-details', 'your-details', 'index.php?option=com_user&view=user&task=edit', 'component', 1, 1, 1, 14, 1, 0, '0000-00-00 00:00:00', 0, 2, 0, '', 3, 4, 0),
(5, 'usermenu', 'Logout', 'logout', 'logout', 'index.php?option=com_user&view=login', 'component', 1, 1, 1, 14, 5, 0, '0000-00-00 00:00:00', 0, 2, 0, '', 21, 22, 0),
(6, 'usermenu', 'Submit an Article', 'submit-an-article', 'submit-an-article', 'index.php?option=com_content&view=article&layout=form', 'component', 1, 1, 1, 20, 3, 0, '0000-00-00 00:00:00', 0, 2, 0, '', 5, 6, 0),
(7, 'usermenu', 'Submit a Web Link', 'submit-a-web-link', 'submit-a-web-link', 'index.php?option=com_weblinks&view=weblink&layout=edit', 'component', 1, 1, 1, 4, 4, 0, '0000-00-00 00:00:00', 0, 2, 0, '', 17, 18, 0),
(8, 'mainmenu', 'Weblinks', 'weblinks', 'weblinks', 'index.php?option=com_weblinks&amp;view=category&amp;id=21', 'component', 1, 1, 1, 4, 6, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"show_feed_link":"1","image":"","menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"-1","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 15, 16, 0),
(13, 'mainmenu', 'Single Article', 'single-article', 'single-article', 'index.php?option=com_content&amp;view=article&amp;id=5', 'component', 1, 1, 1, 20, 7, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 13, 14, 0),
(9, 'mainmenu', 'Article Categories', 'article-categories', 'article-categories', 'index.php?option=com_content&view=categories', 'component', 1, 1, 1, 20, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 11, 12, 0),
(15, 'mainmenu', 'Contact Category', 'contact-category', 'contact-category', 'index.php?option=com_contact&amp;view=category&amp;catid=30', 'component', 1, 1, 1, 7, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"display_num":"20","image":"","image_align":"right","show_limit":"0","show_feed_link":"1","menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 25, 26, 0),
(12, 'mainmenu', 'Search', 'search', 'search', 'index.php?option=com_search&view=search', 'component', 1, 1, 1, 15, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"search_areas":"1","show_date":"1","menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 19, 20, 0),
(14, 'mainmenu', 'Single Contact', 'single-contact', 'single-contact', 'index.php?option=com_contact&amp;view=contact&amp;id=1', 'component', 1, 1, 1, 7, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"show_contact_list":"0","show_category_crumb":"0","menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 23, 24, 0),
(16, 'mainmenu', 'News Feeds Category', 'news-feeds-category', 'news-feeds-category', 'index.php?option=com_newsfeeds&amp;view=category&amp;id=31', 'component', 1, 1, 1, 11, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"show_limit":"1","menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":1,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 27, 28, 0),
(17, 'mainmenu', 'Single Feed', 'single-feed', 'single-feed', 'index.php?option=com_newsfeeds&amp;view=newsfeed&amp;id=03', 'component', 1, 1, 1, 11, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"menu-anchor_title":"","menu-anchor_css":"","page_title":"","show_page_title":0,"page_heading":"","pageclass_sfx":"","menu_image":"","link_title":"","secure":0,"menu-meta_description":"","menu-meta_keywords":"","robots":""}', 29, 30, 0),
(18, 'mainmenu', 'Types of Menu Links', 'types-of-menu-links', 'types-of-menu-links', '', 'separator', 1, 1, 1, 0, 0, 0, '0000-00-00 00:00:00', 0, 1, 0, '{"menu-anchor_title":"","menu-anchor_css":""}', 9, 10, 0);

--
-- Dumping data for table `#__menu_types`
--

INSERT IGNORE INTO `#__menu_types` VALUES
(2, 'usermenu', 'User Menu', 'A Menu for logged in Users');

--
-- Dumping data for table `#__modules`
--

--
-- Dumping data for table `#__modules_menu`
--

--
-- Dumping data for table `#__newsfeeds`
--
INSERT INTO `#__newsfeeds` (`catid`, `id`, `name`, `alias`, `link`, `filename`, `published`, `numarticles`, `cache_time`, `checked_out`, `checked_out_time`, `ordering`, `rtl`) VALUES
(31, 1, 'Joomla! Security News', 'joomla-security-news', 'http://feeds.joomla.org/JoomlaSecurityNews', NULL, 1, 5, 3600, 0, '0000-00-00 00:00:00', 3, 0),
(31, 2, 'Joomla! Connect', 'joomla-connect', 'http://feeds.joomla.org/JoomlaConnect', NULL, 1, 5, 3600, 42, '2009-09-28 02:08:20', 2, 0),
(31, 3, 'Joomla! Announcements', 'joomla-announcements', 'http://www.joomla.org/announcements.feed?type=rss', NULL, 1, 5, 3600, 42, '2009-09-28 02:57:52', 1, 0),
(31, 4, 'Joomla! Extensions Diretory: New Extensions', 'joomla-extensions-diretory-new-extensions', 'http://feeds.joomla.org/JoomlaExtensions', NULL, 1, 5, 3600, 0, '0000-00-00 00:00:00', 4, 0);

--
-- Dumping data for table `#__usergroups`
--

INSERT IGNORE INTO `#__usergroups` VALUES
(9, 2, 15, 16, 'Park Rangers', 1, 'core');

--
-- Dumping data for table `#__usergroup_rule_map`
--

INSERT IGNORE INTO `#__usergroup_rule_map` VALUES
(9, 4),
(9, 35);

--
-- Dumping data for table `#__weblinks`
--

INSERT IGNORE INTO `#__weblinks` VALUES
(1, 20, 0, 'Joomla!', 'joomla', 'http://www.joomla.org', 'Home of Joomla!', '2005-02-14 15:19:02', 3, 1, 0, '0000-00-00 00:00:00', 1, 0, 1, 1, '{"target":"0"}'),
(2, 21, 0, 'php.net', 'php', 'http://www.php.net', 'The language that Joomla! is developed in', '2004-07-07 11:33:24', 6, 1, 0, '0000-00-00 00:00:00', 3, 0, 1, 1, '{}'),
(3, 21, 0, 'MySQL', 'mysql', 'http://www.mysql.com', 'The database that Joomla! uses', '2004-07-07 10:18:31', 1, 1, 0, '0000-00-00 00:00:00', 5, 0, 1, 1, '{}'),
(4, 20, 0, 'OpenSourceMatters', 'opensourcematters', 'http://www.opensourcematters.org', 'Home of OSM', '2005-02-14 15:19:02', 11, 1, 0, '0000-00-00 00:00:00', 2, 0, 1, 1, '{"target":"0"}'),
(5, 21, 0, 'Joomla! - Forums', 'joomla-forums', 'http://forum.joomla.org', 'Joomla! Forums', '2005-02-14 15:19:02', 4, 1, 0, '0000-00-00 00:00:00', 4, 0, 1, 1, '{"target":"0"}'),
(6, 21, 0, 'Ohloh Tracking of Joomla!', 'ohloh-tracking-of-joomla', 'http://www.ohloh.net/projects/20', 'Objective reports from Ohloh about Joomla''s development activity. Joomla! has some star developers with serious kudos.', '2007-07-19 09:28:31', 1, 1, 0, '0000-00-00 00:00:00', 6, 0, 1, 1, '{"target":"0"}');
