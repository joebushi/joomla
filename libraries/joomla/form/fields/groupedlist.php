<?php

/**
 * @version		$Id: list.php 13967 2010-01-03 22:22:59Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldGroupedList extends JFormField
{

    /**
     * The field type.
     *
     * @var		string
     */
    protected $type = 'GroupedList';

    /**
     * Method to get a list of groups for a list input.
     *
     * @return	array		An array of array JHtml options.
     */
    protected function _getGroups() 
    {
        $groups = array();

        // Iterate through the children and build an array of groups.
        foreach($this->_element->children() as $group) 
        {
            $label = $group->attributes('label');
            $groups[$label] = array();

            // Iterate through the children and build an array of options.
            foreach($group->children() as $option) 
            {
                $groups[$label][] = JHtml::_('select.option', $option->attributes('value'), JText::_(trim($option->data())));
            }
        }
        return $groups;
    }

    /**
     * Method to get the field input.
     *
     * @return	string		The field input.
     */
    protected function _getInput() 
    {
        $disabled = $this->_element->attributes('disabled') == 'true' ? true : false;
        $attributes = ' ';
        if ($v = $this->_element->attributes('size')) 
        {
            $attributes.= 'size="' . $v . '"';
        }
        if ($v = $this->_element->attributes('class')) 
        {
            $attributes.= 'class="' . $v . '"';
        }
        else
        {
            $attributes.= 'class="inputbox"';
        }
        if ($m = $this->_element->attributes('multiple')) 
        {
            $attributes.= 'multiple="multiple"';
        }
        if ($v = $this->_element->attributes('onchange')) 
        {
            $attributes.= 'onchange="' . $v . '"';
        }
        if ($disabled) 
        {
            $attributes.= ' disabled="disabled"';
        }

        // Get the groups
        $groups = (array)$this->_getGroups();

        // Get the html
        $return = JHtml::_('select.groupedlist', $groups, $this->inputName, array('list.attr' => $attributes, 'id' => $this->inputId, 'list.select' => $this->value, 'group.items' => null));

        // Return the html
        return $return;
    }
}

