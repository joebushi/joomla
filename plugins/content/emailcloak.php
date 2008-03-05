<?php
/**
* @version      $Id$
* @package      Joomla
* @copyright    Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent('onPrepareContent', 'plgContentEmailCloak');

/**
 * Plugin that cloaks all emails in content from spambots via Javascript.
 *
 * @param object|string An object with a "text" property or the string to be
 * cloaked.
 * @param array Additional parameters. See {@see plgEmailCloak()}.
 * @param int Optional page number. Unused. Defaults to zero.
 * @return boolean True on success.
 */
function plgContentEmailCloak(&$row, &$params, $page=0)
{
    if (is_object($row)) {
        return plgEmailCloak($row->text, $params);
    }
    return plgEmailCloak($row, $params);
}

/**
 * Genarate a search pattern based on link and text.
 *
 * @param string The target of an e-mail link.
 * @param string The text enclosed by the link.
 * @return string A regular expression that matches a link containing the
 * parameters.
 */
function plgContentEmailCloak_searchPattern ($link, $text) {
    // <a href="mailto:anyLink">anyText</a>
    $pattern = "(<a [[:alnum:] _\"\'=\@\.\-]*href=[\"\']mailto:"
        . $link . "[\"\'][[:alnum:] _\"\'=\@\.\-]*)>" . $text . '</a>';

    return $pattern;
}

/**
 * Cloak all emails in text from spambots via Javascript.
 *
 * @param string The string to be cloaked.
 * @param array Additional parameters. Parameter "mode" (integer, default 1)
 * replaces addresses with "mailto:" links if nonzero.
 * @return boolean True on success.
 */
function plgEmailCloak(&$text, &$params)
{
    // Simple performance check to determine whether bot should process further.
    if (JString::strpos($text, '@') === false) {
        return true;
    }

    $plugin = & JPluginHelper::getPlugin('content', 'emailcloak');

    /*
     * Check for presence of {emailcloak=off} which is explicits disables this
     * bot for the item.
     */
    if (! JString::strpos($text, '{emailcloak=off}') === false) {
        $text = JString::str_ireplace('{emailcloak=off}', '', $text);
        return true;
    }

    // Load plugin params info
    $pluginParams   = new JParameter($plugin->params);
    $mode           = $pluginParams->def('mode', 1);

    // any@email.address.com
    $search_email       = '([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)';
    // any@email.address.com?subject=anyText
    $search_email_msg   = '([[:alnum:]_\.\-]+)(\@[[:alnum:]\.\-]+\.+)([[:alnum:]\.\-]+)([[:alnum:][:space:][:punct:]][^"<>]+)';
    // anyText
    $search_text        = '([[:alnum:][:space:][:punct:]][^<>]+)';

    /*
     * Search for derivatives of link code <a href="mailto:email@amail.com"
     * >email@amail.com</a>
     */
    $pattern = plgContentEmailCloak_searchPattern($search_email, $search_email);
    while (eregi($pattern, $text, $regs)) {
        $mail       = $regs[2] . $regs[3] . $regs[4];
        $mail_text  = $regs[5] . $regs[6] . $regs[7];

        // Check to see if mail text is different from mail addy
        $replacement = JHTML::_('email.cloak', $mail, $mode, $mail_text);

        // Replace the found address with the js cloacked email
        $text = str_replace($regs[0], $replacement, $text);
    }

    /*
     * Search for derivatives of link code <a href="mailto:email@amail.com">
     * anytext</a>
     */
    $pattern = plgContentEmailCloak_searchPattern($search_email, $search_text);
    while (eregi($pattern, $text, $regs)) {
        $mail       = $regs[2] . $regs[3] . $regs[4];
        $mail_text  = $regs[5];

        $replacement = JHTML::_('email.cloak', $mail, $mode, $mail_text, 0);

        // Replace the found address with the js cloacked email
        $text = str_replace($regs[0], $replacement, $text);
    }

    /*
     * Search for derivatives of link code <a href="mailto:email@amail.com?
     * subject=Text">email@amail.com</a>
     */
    $pattern = plgContentEmailCloak_searchPattern($search_email_msg, $search_email);
    while (eregi($pattern, $text, $regs)) {
        $mail       = $regs[2] . $regs[3] . $regs[4] . $regs[5];
        $mail_text  = $regs[6] . $regs[7]. $regs[8];
        // Needed for handling of Body parameter
        $mail       = str_replace( '&amp;', '&', $mail );

        // Check to see if mail text is different from mail addy
        $replacement = JHTML::_('email.cloak', $mail, $mode, $mail_text);

        // Replace the found address with the js cloacked email
        $text = str_replace($regs[0], $replacement, $text);
    }

    /*
     * Search for derivatives of link code <a href="mailto:email@amail.com?
     * subject=Text">anytext</a>
     */
    $pattern = plgContentEmailCloak_searchPattern($search_email_msg, $search_text);
    while (eregi($pattern, $text, $regs)) {
        $mail       = $regs[2] . $regs[3] . $regs[4] . $regs[5];
        $mail_text  = $regs[6];
        // Needed for handling of Body parameter
        $mail       = str_replace('&amp;', '&', $mail);

        $replacement = JHTML::_('email.cloak', $mail);

        // Replace the found address with the js cloacked email
        $text    = str_replace($regs[0], $replacement, $text);
    }

    // Search for plain text email@amail.com
    while (eregi($search_email, $text, $regs)) {
        $mail = $regs[0];

        $replacement = JHTML::_('email.cloak', $mail, $mode);

        // Replace the found address with the js cloaked email
        $text = str_replace($regs[0], $replacement, $text);
    }
    return true;
}
