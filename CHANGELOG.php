<?php
/**
* @version		$Id$
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
1. Copyright and disclaimer
---------------------------
This application is opensource software released under the GPL.  Please
see source code and the LICENSE file


2. Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
Joomla! 1.5, including beta and release candidate versions.
Our thanks to all those people who've contributed bug reports and
code fixes.


Legend:

* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note

29-Jul-2008 Sam Moffatt
 + Added discover_install functionality to templates and com_installer
 + Added purge functionality for discovery cache
 + Started on discover_install for component installation adapter

25-Jul-2008 Sam Moffatt
 + Added a state field and some indicies to the jos_extensions table
 + Added discover and discover_install support in the installer class
 + Discover tab in admininistrator:com_installer now goes somewhere 
 + Discover function works for templates
 + Added an icon to the Khepri template CSS

23-Jul-2008 Sam Moffatt
 ^ Plugins now have their own directory with their group
 + Plugins now have install triggers and SQL support
 + Packages can handle folders as well as archives
 ^ Altered #__extensions.data to be called 'custom_data' and 'system_data' 
 

22-Jul-2008 Sam Moffatt
 ^ Clean installation now adding entries for plugins, modules and components
 - Removed plugins table completely
 + Modules now can use SQL and have full install, update and uninstall triggers
 + Modules now have an update function
 ^ Changed the parseSQLQueries code to use extension_root instead of extension_administrator
 ^ Changed component installer adapter to set extension_root to extension_administrator
 - Removed old comments
 # Fixed rollback bug in installer handler for extensions
 + Added File and SQL adapters as copies of libraries and components respectively (nonfunctional)
 ^ Set svn:keywords recursively to Id for /libraries/joomla/installer

21-Jul-2008 Sam Moffatt
 + Gave modules install trigger set
 ^ Changed triggers to use elements (e.g. com_alpha) instead of clean names to ensure uniqueness
 + Added warnings tab
 ^ Converting plugins to extensions in more places
 ^ Changed version string
 ^ Changed components menu to use extenions table instead of components

18-Jul-2008 Sam Moffatt
 + Components now use the extensions table for install and uninstall
 + Added a simple migration script for components, modules, plugins 

17-Jul-2008 Sam Moffatt
 ^ Converting plugins to use jos_extensions on install and uninstall
 ^ Converted com_plugins to use jos_extensions

16-Jul-2008 Sam Moffatt
 - Hid old extension specific tabs from com_installer
 + Added 'discover' tab to find new extensions/installed extensions
 $ Added language strings
 ^ Changed modules to use jos_extensions on uninstall
 ^ Changed libraries to use jos_extensions when installing and uninstalling

15-Jul-2008 Sam Moffatt
 + Added jos_extensions table
 + Added 'manage' tab to installer
 ^ Changed modules to use jos_extensions on install

14-Jul-2008 Sam Moffatt
 ^ Strings in extension installs can now be translated
 ^ Merged in the 1.5.4 language installer changes

10-Jul-2008 Sam Moffatt
 # Fixed [#10374] setAdapter pass by reference from 1.5 bug tracker
 - Removed excess calls to set the extension.message in the component adapter
 ^ Changed some legacy references to mosinstall to extension
 

05-Jun-2008 Sam Moffatt
 + Added packages to installer UI
 ^ Altered package format in manifest handler
 ^ Altered library format in manifest handler

28-May-2008 Sam Moffatt
 + Added libraries to system
 + Added packages to system (non-functional UI)
 ^ Changed language files to handle new features

-------------------- 1.5.2 Stable Release [22-March-2008] ---------------------

