<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class ContentViewType extends JView
{
	public $type = null;

	public $lists = array();

	public $tablePrefix = null;

	public function display($tpl = null)
	{
		$type = $this->get('Data');
		$tablePrefix = $this->get('TablePrefix');

		$isNew = ($type->id < 1);

		$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
		JToolBarHelper::title(   JText::_( 'Content Type' ).': <small>[ ' . $text.' ]</small>' );
		JToolBarHelper::save();
		JToolBarHelper::apply();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		// build the html select list for ordering
		$query = 'SELECT ordering AS value, name AS text'
			. ' FROM #__content_types'
			. ' ORDER BY ordering';
		$lists['ordering'] = JHTML::_('list.specificordering', $type, $type->id, $query, 1 );

		$this->assign('tablePrefix', $tablePrefix);
		$this->assignRef('type', $type);
		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}