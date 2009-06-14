<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

require_once (JPATH_COMPONENT.DS.'view.php');

/**
 * Frontpage View class
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	Content
 * @since 1.5
 */
class ContentViewFrontpage extends ContentView
{
	protected $state = null;
	protected $items = null;
	protected $params = null;
	protected $pagination = null;

	protected $lead_items = array();
	protected $intro_items = array();
	protected $link_items = array();
	protected $columns = 1;

	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null)
	{
		// Initialize variables
		$user		= &JFactory::getUser();
		$app		= &JFactory::getApplication();
		$params		= &$app->getParams();

		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Get the metrics for the structural page layout.
		$numLeading	= $params->def('num_leading_articles',	1);
		$numIntro	= $params->def('num_intro_articles',	4);
		$numLinks	= $params->def('num_links', 			4);

		// Preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interogate the arrays.
		$max	= count($this->items);

		// The first group is the leading articles.
		$limit	= $numLeading;
		for ($i = 0; $i < $limit && $i < $max; $i++)
		{
			$this->lead_items[$i] = &$this->items[$i];
		}

		// The second group is the intro articles.
		$limit		= $numLeading + $numIntro;
		$this->columns	= max(1, $this->params->def('num_columns', 1));
		$order		= $this->params->def('multi_column_order', 1);

		if ($this->params->def('multi_column_order', 1) || $this->columns == 1)
		{
			// Order articles across, then down (or single column mode)
			for ($i = $numLeading; $i < $limit && $i < $max; $i++)
			{
				$this->intro_items[$i] = &$this->items[$i];
			}
		}
		else
		{
			// Order articles down, then across
			$k = $numLeading;

			// Pass over the second group by the number of columns
			for ($j = 0; $j < $this->columns; $j++)
			{
				for ($i = $numLeading + $j; $i < $limit && $i < $max; $i += $this->columns, $k++)
				{
					$this->intro_items[$k] = &$this->items[$i];
				}
			}
		}

		// The remainder are the links.
		for ($i = $numLeading + $numIntro; $i < $max; $i++)
		{
			$this->link_items[$i] = &$this->items[$i];
		}

		// Request variables
		$id			= JRequest::getVar('id', null, '', 'int');
		$limit		= JRequest::getVar('limit', 5, '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		// Create a user access object for the user
		//$access				= new stdClass();
		//$access->canEdit	= $user->authorize('com_content.article.edit_article');
		//$access->canEditOwn	= $user->authorize('com_content.article.edit_own');
		//$access->canPublish	= $user->authorize('com_content.article.publish');


		//jimport('joomla.html.pagination');
		//$this->pagination = new JPagination($total, $limitstart, $limit - $links);


		//$this->assignRef('user',		$user);
		$this->assignRef('params',		$params);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= &JFactory::getApplication();
		$menus	= &JSite::getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		if ($menu = $menus->getActive())
		{
			$menuParams = new JParameter($menu->params);
			$title = $menuParams->get('page_title');
		}
		if (empty($title)) {
			$title	= htmlspecialchars_decode($app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		// Add feed links
		if ($this->params->get('show_feed_link', 1))
		{
			$link = '&format=feed&limitstart=';

			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);

			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}
	}



	function &getItem($index = 0, &$params)
	{
		global $mainframe;

		// Initialize some variables
		$user		= &JFactory::getUser();
		$groups		= $user->authorisedLevels();
		$dispatcher	= &JDispatcher::getInstance();

		$SiteName	= $mainframe->getCfg('sitename');

		$task		= JRequest::getCmd('task');

		$linkOn		= null;
		$linkText	= null;

		$item = &$this->items[$index];
		$item->text = $item->introtext;

		// Get the page/component configuration and article parameters
		$item->params = clone($params);
		$aparams = new JParameter($item->attribs);

		// Merge article parameters into the page configuration
		$item->params->merge($aparams);

		// Process the content preparation plugins
		JPluginHelper::importPlugin('content');
		$results = $dispatcher->trigger('onPrepareContent', array (& $item, & $item->params, 0));

		// Build the link and text of the readmore button
		if (($item->params->get('show_readmore') && @ $item->readmore) || $item->params->get('link_titles'))
		{
			// checks if the item is a public or registered/special item
			if (in_array($item->access, $groups))
			{
				$item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid));
				$item->readmore_register = false;
			}
			else
			{
				$item->readmore_link = JRoute::_("index.php?option=com_users&view=login");
				$item->readmore_register = true;
			}
		}

		$item->event = new stdClass();
		$results = $dispatcher->trigger('onAfterDisplayTitle', array (& $item, & $item->params,0));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onBeforeDisplayContent', array (& $item, & $item->params, 0));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onAfterDisplayContent', array (& $item, & $item->params, 0));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		return $item;
	}


}
