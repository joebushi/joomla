<?php
/**
 * @version		$Id:  $
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

jimport( 'joomla.plugin.plugin' );

/**
 * Editor Article button
 *
 * @author Ross Crawford <ross.crawford@gmail.com>
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonArticle extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param 	object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgButtonImage(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Display the button
	 *
	 * @return object button
	 */
	function onDisplay($name)
	{
		/*
		 * Javascript to insert the link
		 * View element calls jSelectArticle when an article is clicked
		 * jSelectArticle creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		$js = "
		function jSelectArticle(id, title) {
			var tag = \"<a href=\\\"index.php?option=com_content&amp;view=article&amp;id=\"+id+\"\\\">\"+title+\"</a>\";
			jInsertEditorText(tag, \"".$name."\");
			document.getElementById('sbox-window').close();
		}";
		
		/*
		 * CSS for article button
		 */
		$css = "
		.button2-left .article {
			background: url(../../../plugins/editors-xtd/article.png) 100% 0 no-repeat;
		}";
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		$doc->addStyleDeclaration($css);

		JHTML::_('behavior.modal');

		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_content&amp;task=element&amp;tmpl=component';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('Article'));
		$button->set('name', 'article');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
