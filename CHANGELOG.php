<?php
/**
* @version $Id$
* @package Joomla
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );
?>

1. Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
Joomla! 1.0, including beta and release candidate versions.
Our thanks to all those people who've contributed bug reports and
code fixes.

Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

07-Dec-2005 Johan Janssens
 # Fixed artf2430 : invalid values in tabpane.css
 # Fixed artf2457 : VCard bug IS a bug
 # Fixed artf2218 : RSS Newsfeed module generates wrong rendering output
 # Fixed artf2453 : Random Image Module
 # Fixed artf2251 : Poll title error
 # Fixed artf2393 : Original editor cannot open content item if checked out

06-Dec-2005 Alex Kempkens
 # Fixed artf2434: Typo in database.php checkout function line 1050
 # Fixed artf2398 : Parameter Text Area field name

06-Dec-2005 Johan Janssens
 # Fixed artf2418 : Banners Client Manager Next Page Issue: Joomla 1.04
 # Fixed artf2156 : memory exhastion error in joomla.xml.php
 # Fixed artf2378 : mosCommonHTML::CheckedOutProcessing not checking if the current user 
                    has checked out the document
 # Fixed artf1948 : Pagination problem still exists
 ^ Upgraded TinyMCE Compressor [1.0.4]
 ^ Upgraded TinyMCE [2.0.1]

01-Dec-2005 Andrew Eddie
 # Fixed nullDate error in mosDBTable::checkin method
 # Removed $migrate global in mosDBTable::store method
 # Fixed some MySQL 5 issues (still very unreliable)
 + Component may force frontend application to include joomla.javascript.js by:
   $mainframe->set( 'joomlaJavascript', 1 );

01-Dec-2005 Andrew Eddie
 # Fixed limit error in sections search bot
 # Bug in gacl_api::add_group query [c/o Mambo bug #8199]
 # Search highlighting fails when a "?" is entered [c/o Mambo bug #8260]

30-Nov-2005 Emir Sakic
 + Added 404 handling for missing content and components
 + Added 404 handling to SEF for unknown files

30-Nov-2005 Andrew Eddie
 # Site templates allowed to have custom index2.php (fixes problems where custom code is required in index2)

29-Nov-2005 Andrew Eddie
 # Fixed artf2258 : Parameter tooltips missing in 1.0.4

28-Nov-2005 Andrew Eddie
 # Fixed artf2329 : mosMainFrame::getBasePath refers to non-existant JFile class.
 # Fixed artf2246 : Error in frontend.html.php
 # Fixed artf2190 : mod_poll.php modification
 # Fixed artf2292 : [WITH FIX] Sql query missing hits

24-Nov-2005 Emir Sakic
 # Fixed artf2225 : Email / Print redirects to homepage
 # Fixed artf1705 : Not same URL for same item : duplicate content

23-Nov-2005 Johan Janssens
 # Fixed : Content Finish Publishing & not authorized

22-Nov-2005 Marko Schmuck
 # Fixed artf2240 : 1.0.4 URL encoding entire frontend?
 # Fixed artf2222 : ampReplace in content.html.php
 + Versioncheck for new_link parameter for mysql_connect.

22-Nov-2005 Levis Bisson
 # Fixed artf2221 : 1.0.4: includes/database.php faulty on PHP < 4.2.0
 # Fixed artf2219 : Bug in pageNavigation.php - added "if not define _PN_LT or _PN_RT"

22-Nov-2005 Johan Janssens
 # Fixed artf2224 : Problem with Media Manager
 # Fixed : Can't create new folders in media manager

---------------- 1.0.4 Released -- [21-Nov-2005 10:00 UTC] ------------------

This Release Contains following Security Fixes

Critical Level Threat
 * Potentional XSS injection through GET and other variables
 * Hardened SEF against XSS injection

Low Level Threat
 * Potential SQL injection in Polls modules through the Itemid variable
 * Potential SQL injection in several methods in mosDBTable class
 * Potential misuse of Media component file management functions
 * Add search limit param (default of 50) to `Search` Mambots to prevent search flooding

---

20-Nov-2005 Levis Bisson
 # Fixed Artifact artf1967 displays with an escaped apostrophe in both title and TOC.

20-Nov-2005 Emir Sakic
 * SECURITY: Hardened SEF against XSS injection

19-Nov-2005 Levis Bisson
 # replaced charset=utf-8 to charset=iso-8859-1 in language file

19-Nov-2005 Andrew Eddie
 * SECURITY: Fixed XSS injection of global variable through the _GET array

17-Nov-2005 Johan Janssens
 ^ Replaced install.png with new image
 - Reverted artf2139 : admin menu xhtml
 + Added clone function for PHP5 backwards compatibility

16-Nov-2005 Rey Gigataras
 # Fixed artf2137 : editorArea xhtml
 # Fixed artf2139 : admin menu xhtml
 # Fixed artf2136 : Admin menubar valid xhtml
 # Fixed artf2135 : Admin invalid xhtml
 # Fixed artf2140 : mosMenuBar::publishList
 # Fixed artf2027 : uploading images from custom component

13-Nov-2005 Rey Gigataras
 # PERFORMANCE: Fixed artf1993 : Inefficient queries in com_content
 # Fixed artf2021 : artf1791 : Failed Login results in redirect to referring page
 # Fixed artf2021 : appendMetaTag() prepends instead of appends
 # Fixed artf1981 : incorrect url's at next/previous links at content items
 # Fixed artf2079 : SQL error in category manager thru contact manager
 # Fixed artf1586 : .htaccess - RewriteEngine problem
 # Fixed artf1976 : Check for custom icon in mod_quickicon.php

11-Nov-2005 Andy Miller
 # Fixed issue with RSS module not displaying inside module rendering wrapper

10-Nov-2005 Rey Gigataras
 # Fixed contact component dropdown select category bug

07-Nov-2005 Rey Gigataras
 # Fixed mod_quickicon `redeclaration of function` error possibilities

07-Nov-2005 Johan Janssens
 # Fixed  artf1648 : tinyMCE BR and P elements
 # Fixed artf1700 : TinyMCE doesn't support relative URL's for images

07-Nov-2005 Andrew Eddie
 * SECURITY: Fixed artf1978 : mod_poll SQL Injection Vulnerability [ Low Level Security Bug ]
 * SECURITY: Fixed SQL injection possibility in several mosDBTable methods [ Low Level Security Bug ]
 * SECURITY: Fixed malicious injection into filename variables in com_media [ Low Level Security Bug ]
 ^ mosDBTable::publish_array renamed to publish
 ^ mosDBTable::save no longer updates the ordering (must now be done separately)

06-Nov-2005 Rey Gigataras
 * SECURITY: Add search limit param (default of 50) to `Search` Mambots to prevent search flooding [ Low Level Security Bug ]
 # Fixed custom() & customX() functions in menu.html.php no checking for image in /administrator/images/

04-Nov-2005 Rey Gigataras
 # Fixed artf1953 : Page Class Suffix in Contacts component
 # Fixed artf1945 : mosToolTip not generating valid xhtml

03-Nov-2005 Rey Gigataras
 + modduleclass_sfx support to mod_poll
 # Fixed artf1902 : Incorrect number of table cells in mod_poll

03-Nov-2005 Samuel Moffatt
 # Fixed bug which prevented component uninstall if another XML file was in the directory

01-Nov-2005 Rey Gigataras
 # Fixed artf1888 : linkable [category|section] URL incorrect
 # Fixed artf1620 : Hardcoded words in pdf.php
 # Fixed artf1887 : Content: Bug in creation date generation

31-Oct-2005 Johan Janssens
 # Fixed artf1277 : News Feed Display Bad Accent character

31-Oct-2005 Rey Gigataras
 # Fixed artf1739 : Problem with the menuitem type url and assigned templates and modules
 # Fixed artf1574 : Who is online after update to Joomla 1.0.3 no more work correctly
 # Fixed artf1666 : Notice: on component installation
 # Fixed artf1573 : Manage Banners | Error in Field Name
 # Fixed artf1597 : Small bug in loadAssocList function in database.php
 # Fixed artf1832 : Logout problem
 # Fixed artf1769 : Undefined index: 2 in includes/joomla.php on line 2721
 # Fixed artf1749 : Email-to-friend is NOT actually from friend
 # Fixed artf1591 : page is expired at installation
 # Fixed artf1851 : 1.0.2 copy content has error
 # Fixed artf1569 : Display of mouseover in IE gives a problem with a dropdown-box
 # Fixed artf1869 : Poll produces MySQL-Error when accessed via Component Link
 # Fixed artf1694 : 1.0.3 undefined indexes filter_sectionid and catid on "Add New Content"
 # Fixed artf1834 : English Localisation
 # Fixed artf1771 : Wrong mosmsg
 # Fixed artf1792 : "Receive Submission Emails" label is misleading
 # Fixed artf1770 : Undefined index: HTTP_USER_AGENT

30-Oct-2005 Rey Gigataras
 ^ Upgraded TinyMCE Compressor [1.02]
 ^ Upgraded TinyMCE [2.0 RC4]

27-Oct-2005 Johan Janssens
 # Fixed artf1671 : Media Manager
 # Fixed artf1814 : Tab Class wrong
 # Fixed artf1086 : Icons at the control panel fall apart

26-Oct-2005 Samuel Moffatt
 # Fixed bug where a new database object with the same username, password and host but different database name would kill Joomla!

25-Oct-2005 Johan Janssens
 # Fixed artf1733 : $contact->id used instead of $Itemid
 # Fixed artf1654 : base url above title tag
 # Fixed artf1738 : Registration - javascript alert

23-Oct-2005 Rey Gigataras
 # Fixed artf1695 : Show Empty Categories in Section does not work
 # Fixed artf1710 : Unnecessary queries (optimization)
 # Fixed artf1711 : Missing whitespace in search results
 # Fixed artf1706 : Mambo logo not removed from admin images
 # Fixed artf1708 : Search CMT: Hardcoded date format
 # Fixed artf1689 : Joomla! Installer - Wording still not correct
 # Fixed artf1692 : email and print buttons (maybe also the PDF) does not validate

19-Oct-2005 Andrew Eddie
 # Fixed missing autoclear in "list-item" stock template

19-Oct-2005 Rey Gigataras
 # Fixed artf1577 : MenuLink Blog section error

19-Oct-2005 Levis Bisson
  Applyed Feature Requests:
^ Artifact artf1282 : Easier sorting of static content in creating menu links
^ Artifact artf1162 : Remove hardcoding of <<, <, > and >> in pageNavigation.php

---------------- 1.0.3 Released -- [14-Oct-2005 10:00 UTC] ------------------

Contains following Security Fixes
Medium Level Threat
 * Fixed SQL injection bug in content submission (thanks Dead Krolik)

Low Level Threat
 * Fixed securitybug in admin.content.html.php when 2 logged in and try to edit the same content
 * Fixed Search Component flooding, by limiting searching to between 3 and 20 characters
 * Fixed artf1405 : Joomla shows Items to unauthorized users

-------

14-Oct-2005 Rey Gigataras
 # Fixed edit icon not showing on frontpage
 # Fixed artf1553 : database.php fails to pass resource id into mysql_get_server_info() call
 # Fixed artf1560 : Install1.php doesn't enforce rule against old_ table prefix

13-Oct-2005 Andy Miller
 # Fixed artf1504 : rhuk_solarflare_ii Template | Menus with " not displaying correctly

13-Oct-2005 Rey Gigataras
 # Fixed duplicated module creation in install
 # Fixed XHTML issue in rss feed module
 # Fixed XHTML issue in com_search
 # Fixed artf1550 : Properly SEFify com_registration links
 # Fixed artf1533 : rhuk_solarflare_ii 2.2 active_menu
 # Fixed artf1354 : Can't create new user
 # Fixed artf1433 : Images in Templates
 # Fixed artf1531 : RSS Feed showing wrong livesite URL

12-Oct-2005 Marko Schmuck
 * Fixed securitybug in admin.content.html.php when 2 logged in and try to edit the same content [ Low Level Security Bug ]

12-Oct-2005 Johan Janssens
 # Fixed artf1266 : gzip compression conflict
 # Fixed artf1453 : Weblink item missing approved parameter
 # Fixed artf1452 : Error deleting Language file
 # Fixed artf1373 : Pagination error

12-Oct-2005 Rey Gigataras
 ^ Core now automatically calculates the offset between yourself and the server
 # Fixed bug in Global Config param `Time Offset`
 # Fixed artf1414 : Missing images in HTML_toolbar
 # Fixed artf1513 : PDF format does not work at version 1.0.2

11-Oct-2005 Rey Gigataras
 * Fixed Search Component flooding, by limiting searching to between 3 and 20 characters [ Low Level Security Bug in 1.0.x ]
 ^ Blog - Content Category Archive will no longer show dropdown selector when coming from Archive Module
 # Fixed artf1470 : Archives not working in the front end
 # Fixed artf1495 : Frontend Archive blog display
 # Fixed artf1364 : TinyMCE loads wrong template styles
 # Fixed artf1494 : Template fault in offline preview
 # Fixed artf1497 : mosemailcloak adds trailing space
 # Fixed artf1493 : mod_whosonline.php

09-Oct-2005 Rey Gigataras
 * Fixed SQL injection bug in content submission [ Medium Level Security Bug in 1.0.x ]
 * Fixed artf1405 : Joomla shows Items to unauthorized users [ Low Level Security Bug in 1.0.2 ]
 # Fixed artf1454 : After update email_cloacking bot is always on
 # Fixed artf1447 : Bug in mosloadposition mambot
 # Fixed artf1483 : SEF default .htaccess file settings are too lax
 # Fixed artf1480 : Administrator type user can loggof Super Adminstrator
 # Fixed artf1422 : PDF Icon is set to on when it should be off
 # Fixed artf1476 : Error at "number of Trashed Items" in sections
 # Fixed artf1415 : Wrong image in editList() function of mosToolBar class

08-Oct-2005 Johan Janssens
 # Fixed artf1384 : tinyMCE doesnt save converted entities

07-Oct-2005 Andy Miller
 # Fixed tabpane css font issue

07-Oct-2005 Johan Janssens
 # Fixed artf1421 : unneeded file includes\domit\testing_domit.php

07-Oct-2005 Andy Stewart
 # Fixed artf1382 : Added installation check to ensure "//" is not generated via PHP_SELF
 # Fixed artf1439 : Used correct ErrorMsg function and updated javascript redirect to remove POSTDATA message
 # Fixed artf1400 : Added a check of $other within com_categories to skip section exists check if set to "other"

05-Oct-2005 Robin Muilwijk
 # Fixed artf1366 : Typo in admin, Adding a new menu item - Blog Content Category

---------------- 1.0.2 Released -- [02-Oct-2005 16:00 UTC] ------------------

02-Oct-2005 Rey Gigataras
 ^ Added check to mosCommonHTML::loadOverlib(); function that will stop it from being loaded twice on a page
 # Fixed Content display not honouring Section or Category publish state
 # Fixed artf1344 : Link to menu shows wrong menu type
 # Fixed artf1189 : Long menu names get truncated, duplicate menus made
 # Fixed artf1192 : Unpublished Bots
 # Fixed artf1223 : Error with Edit items in categories and sections
 # Fixed artf1219 : Joomla Component Module displays Error!
 # Fixed artf1183 : Section module: Still "no items to display"
 # Fixed artf1241 : Editing content fails with MySQL 5.0.12b
 # Fixed artf1306 : modules - parameters of type text not stored correctly

01-Oct-2005 Andy Miller
 # Fixed base href in Content Preview for broken images

01-Oct-2005 Johan Janssens
 ^ Updated TinyMCE editor to version RC 3
 # Fixed artf1221 : Unable to Submit Content (still not working post-patch)
 # Fixed artf1108 : Tooltips on mouseover causes parameter panel to widen
 # Fixed artf1217 : WYSIWYG-Editor and mospagebreak with 2 parameters

01-Oct-2005 Andy Stewart
 # Fixed artf1305 - Added a check within mosimage mambot for introtext being hidden
 # Fixes artf1343 - Removed xml declaration at top of gpl.html

01-Oct-2005 Arno Zijlstra
 ^ Changed OSM banner 2 a little to show banner changing

01-Oct-2005 Levis Bisson
 # Fixed artf1311 : Banners not showing / returning PHP error
 # Fixed artf1319 : Banners not showing in frontend / admin

30-Sep-2005 Andy Miller
 # Fixed poor rendering of fieldset with solarflare2
 ^ Updated solarflare2 template with new colors and logos
 ^ Moved modules to divs, and shuffled pathway to give more button room
 ^ Updated favicon and other Joomla! logos for admin
 # Fixed alignment of footer in admin for safari/opera

30-Sep-2005 Andy Stewart
 + Updated installation routine to recognise port numbers other than 80
 # Fixed artf1293 : added $op=mosGetParam so sendmail is called when running globals.php-off

30-Sep-2005 Rey Gigataras
 ^ Module Manager `position` dropdown ordering alphabetically
 ^ Ability to Hide feed title for `New` modules used to display feeds
 ^ Content Items `New` button sensitive to dropdown filters
 # Fixed Seach Module not using Itemid of existng `Seach` component menu item
 # Fixed `Link to Menu` problem with Sections menu ordering
 # Fixed `Link to Menu` problem with Category = `Content Category`
 # Fixed artf1300 : PDF shows Author name despite setting content item

30-Sep-2005 Levis Bisson
 + Added UTF-8 support
 # Fixed tooltips empty links
 # Fixed artf1265 : url in 'edit-menue-item' of submenues is wrong
 # Fixed artf1277 : News Feed Display Bad Accent character

29-Sep-2005 Arno Zijlstra
 # Fixed publish/unpublish select check in contacts

29-Sep-2005 Rey Gigataras
 # Fixed artf1276 : tiny mce background
 # Fixed artf1281 : Bad name of XML file
 # Fixed artf1180 : Call-by-reference warning when editing menu
 # Fixed artf1188 : includes/vcard.class.php uses short open tags

29-Sep-2005 Levis Bisson
 # Fixed artf1274 : Module display bug when using register/forgot password links
 # Fixed artf1238 : header("Location: $url")- some servers require an absolute URI

28-Sep-2005 Levis Bisson
 # Fixed artf1250 : Order is no use when many pages
 # Fixed artf1254 : Unable to delete when count > 1
 # Fixed artf1248 : Invalid argument supplied for 3P modules

27-Sep-2005 Arno Zijlstra
 # Fixed artf1253 : Apply button image path
 # Fixed artf1240 : WITH FIX: banners component - undefined var task
 # Fixed artf1242 : Problem with "Who's online"
 # Fixed artf1218 : 'Search' does not include weblinks?

25-Sep-2005 Emir Sakic
 # Fixed artf1185 : globals.php-off breaks pathway
 # Fixed artf1196 : undefined constant categoryid
 # Fixed artf1216 : madeyourweb no </head> TAG

24-Sep-2005 Rey Gigataras
 ^ artf1214 : pastarchives.jpg seems unintuitive.

22-Sep-2005 Rey Gigataras
 + Added Version Information to bottom of joomla_admin template, with link to 'Joomla! 1.0.x Series Information'
 # Fixed artf1175 : Create catagory with selection of Section
 # Fixed artf1179 : Custom RSS Newsfeed Module has nested <TR>

---------------- 1.0.1 Released -- [21-Sep-2005 16:30 UTC] ------------------

21-Sep-2005 Rey Gigataras
 # Fixed artf1157 : Section module: Content not displayed, wrong header
 # Fixed artf1159 : Can't cancel "Submit - Content" menu item type form
 # Fixed artf1172 : "Help" link in Administration links to Mamboserver.com
 # Fixed artf1171 : mod_related_items shows all items twice
 # Fixed artf1167 : Component - Search
 # Fixed [RC] incorrect redirect when cancelling from Frontend 'Submit - Content'
 # Fixed undefined variable in Trash Manager
 # Fixed [RC] `Trash` button when no item selected
 # Fixed [RC] `New` Menu Item Type `Next` button bug

20-Sep-2005 Levis Bisson
 ^ added a chmod to the install unlink function
 # Fixed artf1150 : the created_by on initial creation of Static Content Item

20-Sep-2005 Marko Schmuck
 ^ Changed Time Offsets to hardcoded list with country/city names

20-Sep-2005 Rey Gigataras
 # Fixed /installation/ folder check
 # Fixed artf1153 : Quote appears in com_poll error
 # Fixed artf1151 : empty span
 # Fixed artf1089 : multile select image insert reverses list order
 # Fixed artf1138 : Joomla allows creation of double used username
 # Fixed artf1133 : There is no install request to make /mambot/editor writeable

19-Sep-2005 Andrew Eddie
 # Fixed incorrect js function in patTemplate sticky and ordering templates/links

19-Sep-2005 Rey Gigataras
 ^ Changed Overlib styling when creating new menu items
 ^ Additional Overlib info for non-image files and directories
 ^ 'Cancel' button for Media Manager
 ^ Option to run TinyMCE in compressed mode - off by default
 # Fixed artf1111 : mosShowHead and the order of headers
 # Fixed artf1117 : database.php - bcc
 # Fixed artf1114 : database.php _nullDate
 # Fixed TinyMCE errors caused by use of compressed tinymce_gzip.php [artf1088||artf1034||artf1090||artf1044]
 # Installed Editor Mambots are now published by default
 # Fixed error in RSS module
 # Fixed artf1106 : Default Editor Will Not Take Codes Like Java Script
 # Fixed delete file in Media Manager

18-Sep-2005 Arno Zijlstra
 # Fixed artf1084 : <br> stays in empty content
 # Fixed artf1101: Typo in Global Config

18-Sep-2005 Andrew Eddie
 # Fixed issues in patTemplate Translate Function and Modifier
 # Fixed issue with patTemplate variable for Tabs graphics

18-Sep-2005 Rey Gigataras
 # Fixed artf1046 : Menu Manager Item Publishing
 # Fixed artf1036 : newsflash error when logged in in frontend
 # Fixed artf1033 : madeyourweb template logo path
 # Fixed artf1039 : & to &amp; translation in menu and contenttitle
 # Fixed PHP5 passed by reference error in admin.content.php
 # Fixed artf1068 : live bookmark link is wrong
 # Fixed artf1030 : Bug Joomla 1.0.0 Stable (un)publishing News Feeds
 # Fixed artf1048 : Custom Module Bug
 # Fixed artf1080 : Joomla! Installer
 # Fixed artf1050 : error in sql - database update
 # Fixed artf1081 : com_categories can't edit category when clicking hyperlink
 # Fixed artf1053 : Can not unassign template
 # Fixed artf1079 : com_weblinks can't edit links
 # Fixed artf1029 : Site -> Global Configuration = greyed out top menu
 # Fixed artf1064 : Deletion of Modules and Fix
 # Fixed artf1052 : Double Installer Locations
 # Fixed artf1051 : Copyright bumped to the right of the site
 # Fixed artf1059 : component editor bug
 # Fixed artf1041 : mod_mainmenu.xml: escape character for apostrophe missing
 # Fixed artf1040 : category manager not in content-menu

17-Sep-2005 Levis Bisson
 # Fixed artf1037: Media Manager not uploading
 # Fixed artf1025: Registration admin notification
 # Fixed artf1043: Template Chooser doesn't work
 # Fixed artf1042: Template Chooser shows rogue entry

---------------- 1.0.0 Released -- [17-Sep-2005 00:30 UTC] ------------------

Contains following Security Fixes
Medium Level Threat
 * Fixed SQL injection bugs in user activation (thanks Enno Klasing)

Low Level Threat
 * Fixed [#6775] Display of static content without Itemid

-------

16-Sep-2005 Andrew Eddie
 # Fixed: 1014 : & amp ; in pathway
 # Fixed: Missing space in mosimage IMG tags
 # Fixed: Incomplete function call - mysql_insert_id()
 + Added nullDate handling to database class
 + Added database::NameQuote function for quoting field names
 # Fixed: com_checkin to properly use database class
 # Fixed: Missed stripslashes in`global configuration - site`
 + Added admin menu item to clear all caches (for 3rd party addons)

16-Sep-2005 Emir Sakic
 # Fixed sorting by author on frontend category listing
 + Added time offset to copyright year in footer
 # Fixed spelling in sam
 # Reflected some file name changes in installer CHMOD
 # Fixed bugs in paged search component

16-Sep-2005 Alex Kempkens
 + template contest winner 'MadeYourWeb' added

16-Sep-2005 Rey Gigataras
 + Pagination Support for Search Component
 ^ Ordering of Toolbar Icons/buttons now more consistent
 ^ Frontend Edit, status info moved to an overlib
 ^ Search Component converted to GET method
 # Fixed artf1018 : Warning Backend Statistic
 # Fixed artf1016 : Notice: RSS undefined constant
 # Fixed artf1020 : Hide mosimages in blogview doesn't work
 # Various Search Component Fixes
 # Fixed Search Component not honouring Show/Hide Date Global Config setting
 # Fixed [#6668] No static content edit icon for frontend logged in author
 # Fixed [#6710] `Link to menu` function from components Category not working
 # Fixed [#7011] Subtle bug in saveUser() - admin.users.php
 # Fixed [#7120] Articles with `publish_up` today after noon are shown with status `pending`
 # Fixed [#6669] mosmail BCC not working, send as CC
 # Fixed [#7422] Weblink submission emails
 # Fixed [#7196] mosRedirect and Input Filter CGI Error
 # Fixed [#6814] com_wrapper Iframe Name tag / relative url modifications
 # Fixed [#6844] rss version is wrong in the Live Bookmark feeds
 # Fixed [#7120] Articles with `publish_up` today after noon are shown with status `pending`
 # Fixed [#7161] Apparently unncessary code in sendNewPass - registration.php

15-Sep-2005 Andy Miller
 ^ Fixed some width issues with Admin template in IE
 ^ Fixed some UI issues with Banners Component
 ^ Added a default header image for components that don't specify one

15-Sep-2005 Andrew Eddie
 - Removed unused globals from joomla.php
 + Added mosAbstractLog class

15-Sep-2005 Rey Gigataras
 + added `Apply` button to frontend Content editing
 ^ Added publish date to syndicated feeds output [credit: gharding]
 ^ Added RSS Enclosure support to feedcreator [credit: Joseph L. LeBlanc]
 ^ Added Google Sitemap support to feedcreator
 ^ Modified layout of Media Manager
 ^ Added Media Manager support for XCF, ODG, ODT, ODS, ODP file formats
 # Fixed use of 302 redirect instead of 301
 # Content frontend `Save` Content redirects to full content view
 # Fixed Wrapper auto-height problem
 # Queries cleaned of incorrect encapsulation of integer values
 # Fixed Login Component redirection [credit: David Gal]

15-Sep-2005 Arno Zijlstra
 ^ changed tab images to fit new color
 ^ changed overlib colors

14-Sep-2005 Rey Gigataras
 ^ Ugraded TinyMCE [2.0 RC2]
 ^ Param tip style change to dashed underline
 # Queries cleaned of incorrect encapsulation of integer values

14-Sep-2005 Andrew Eddie
 # Added PHP 5 compatibility functions file_put_contents and file_get_contents
 + Added new version of js calendar
 + mosAbstractTasker::setAccessControl method
 + mosUser::getUserListFromGroup
 + mosParameters::toObject and mosParameters::toArray

13-Sep-2005 Andrew Eddie
 ^ Rationalised global configuration handling
 # Fixed polls access bug
 # Fixed module positions preview to show positions regardless of module count
 ^ Modified database:setQuery method to take offset and record limit
 + Added alternative version of globals.php that emulates register_globals=off
 # Added missing parent_id field from mosCategory class

12-Sep-2005 Rey Gigataras
 + Per User Editor selection
 # Module styling applied to custom/new modules
 # Fixed Agent Browser bug

12-Sep-2005 Andrew Eddie
 + New onAfterMainframe event added to site index.php
 + Added dtree javascript library
 + Added some extra useful toolbar icons
 + Added css for fieldsets and legends and some 1.1 admin style formating
 + Added mosDBTable::isCheckedOut() method, applied to components
 # fixed bug in typedcontent edit - checked out is done before object load and always passes
 ^ Updated Help toolbar button to accept component based help files
 ^ Updated version class with new methods
 + Added support for params file to have <mosparams> root tag

12-Sep-2005 Andy Stewart
 # Fixed issue with new content where Categories weren't displayed for sections

12-Sep-2005 Andrew Eddie
 ^ Upgrade DOMIT! and DOMIT!RSS (fixes issues in PHP 4.4.x)
 + Added database.mysqli.php, a MySQL 4.1.x compatible version
 + Added [Check Again] button to installation check screen
 ^ Changed web installer to always use the database connector
 # Fixed PHP 4.4 issues with new objects returning by reference

11-Sep-2005 Rey Gigataras
 + Output Buffering for Admin [pulled from Johan's work in 1.1]
 + Loading of WYSIWYG Editor only when `editorArea` is present [pulled from Johan's work in 1.1]
 ^ Upgraded JSCookMenu [1.4.3]
 ^ Upgraded wz_tooltip [3.34]
  ^ Upgraded Overlib [4.21]
 ^ editor-xtd mosimage & mospagebreak button hidden on category, section & module pages
 # Poll class $this-> bug
 # Fixed change creator dropdown to exclude registered users (who do not have author rights)

11-sep-2005 Arno Zijlstra
 + Added offlinebar.php
 ^ Changed site offline check
 ^ Cosmetic change to offline.php

11-Sep-2005 Andrew Eddie
 + Added sort up and down icons
 + Added mosPageNav::setTemplateVars method

10-Sep-2005 Rey Gigataras
 + `Submit - Content` menu type [credit: Jason Murpy]

09-Sep-2005 Andy Miller
 ^ made changes to new joomla admin template
 ^ changed login lnf to match new admin template
 ^ removed border and width, set padding on div.main in admin
 ^ changed Force Logout text

09-Sep-2005 Alex Kempkens
 ^ changed mosHTML::makeOption to handle different coulmn names
 ^ corrected several calls from makeOption in order to become multi lingual compatible
 ^ corrected little fixes in query handling in order to get multi lingual compatible
 + Added system bot's for better integration of ml support, ssl & multi sites

08-Sep-2005 Rey Gigataras
 + Added back Sys Info link in menubar
 + Added Changelog link to Help area
 ^ Cosmetic change to Toolbar Icon appearance
 ^ Cosmetic change to QuickIcon appearance
 ^ Toolbar icons now 'coloured' no longer 'greyed out'
 ^ Dropdown menu now shows on edit pages but is inactive
 # Fixed Newsfeed component generates image tag instead of img tag
 # Fixed Joomlaxml: tooltips need to use label instead of name
 # Fixed One parameter too many in orderModule call in admin.modules.php
 # Fixed inabiility to show/hide VCard
 # Fixed Mambot Manager filtering

08-Sep-2005 Alex Kempkens
 + mosParameter::_mos_filelist for xml parameters
 ^ mos_ table prefix to jos_ in installation and in some other files.
 + added category handling for contact component
 + added color adapted joomla_admin template

07-Sep-2005 Andrew Eddie
 # Added label tags to mod_login (WCAG compliance)
 # Added label tags to com_contact (WCAG compliance)
 # Added label tags to com_search (WCAG compliance)
 # Added label tag support to mosHTML::selectList (WCAG compliance)
 # Added label tag support to mosHTML::radioList (WCAG compliance)

01-Sep-2005 Andrew Eddie
 + Added article_separator span after a content item
 ^ SECURITY: Hardened mosGetParam by using phpInputFilter for NO_HTML mode
 + Added new mosHash function to produce secure keys
 + SECURITY: Hardened Email to Friend form

31-Aug-2005 Andrew Eddie
 + Added setTemplateVars method to admin pageNavigation class
 ^ Added auto mapping function to mosAbstractTasker constructor
 + Added patHTML class for patTemplate utility methods
 ^ Upgraded patTemplate library
 ! patTemplate::createTemplate has changed parameters
 - Removed requirement to accept GPL on installation
 # Fixed bug in Send New Password function - mail from not defined
 # Fixed undefined $row variable in wrapper component
 # Fixed undefined $params in contacts component
 - Removed unused getids.php
 - Removed redundant whitespace
 ^ Convert 4xSpace to tab

08-Aug-2005 Andrew Eddie
 * SECURITY: Fixed SQL injection bugs in user activation (thanks Enno Klasing) [ Medium Level Security Bug ]
 ^ Encased text files in PHP wrapper to help obsfucate version info
 # Changed admin session name to hash of live_site to allow you to log into more than one Joomla! on the same host
 # Fixed hardcoded (c) character in web installer files
 # Fixed slow query in admin User Manager list screen
 # Fixed bug in poll stats calculation
 # Updated bug fixes in phpMailer class
 # Fixed login bug for nested Joomla! sites on the same domain

02-Aug-2005 Alex Kempkens
 * Fixed [#6775] Display of static content without Itemid [ Low Level Security Bug ]
 # Fixed [#6330] Corrected default value of field

----- Derived from Mambo 4.5.2.3 circa. 17 Aug 12005 -----

2. Copyright and disclaimer
---------------------------
This application is opensource software released under the GPL.  Please
see source code and the LICENSE file
