<?php

/**
 * @version		$Id: category.php 13825 2009-12-23 01:03:06Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

// Import html library
jimport('joomla.html.html');

// Import joomla field list class
require_once dirname(__FILE__) . DS . 'list.php';

/**
 * Supports an HTML select list of menu item
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldMenuitem extends JFormFieldList
{

    /**
     * The field type.
     *
     * @var		string
     */
    public $type = 'Menuitem';

    /**
     * Method to get a list of options for a list input.
     *
     * @return	array		An array of JHtml options.
     */
    protected function _getOptions() 
    {

        // Get the attributes
        $menuType = $this->_element->attributes('menu_type');
        $published = explode(','$this->_element->attributes('published'));
        $disable = explode(',', $this->_element->attributes('disable'));

        // Get the com_menus helper
        require_once realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php');

        // Get the items
        $items = MenusHelper::getMenuLinks($menuType, 0, 0, $published);

        // Prepare return value
        $options = array();

        // If a menu type was set
        if ($menuType) 
        {

            // Loop over links
            foreach($items as $link) 
            {

                // Generate an option disabling it if it's the case
                $options[] = JHtml::_('select.option', $link->value, $link->text, 'value', 'text', in_array($link->type, $disable));
            }
        }

        // else all menu types have to be displayed
        else 
        {

            // Loop over types
            foreach($items as $type) 
            {

                // Generate two disabled options
                $options[] = JHtml::_('select.option', '0', '&nbsp;', 'value', 'text', true);
                $options[] = JHtml::_('select.option', $type->menutype, $type->title . ' - ' . JText::_('Top'), 'value', 'text', true);

                // Loop over links
                foreach($type->links as $link) 
                {

                    // Generate an option disabling it if it's the case
                    $options[] = JHtml::_('select.option', $link->value, '&nbsp;&nbsp;&nbsp;' . $link->text, 'value', 'text', in_array($link->type, $disable));
                }
            }
        }
        return $options;
    }
}

