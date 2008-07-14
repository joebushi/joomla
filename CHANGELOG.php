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

