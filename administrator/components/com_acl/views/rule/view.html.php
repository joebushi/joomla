<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

// Helper functions

function aclObjectChecked( &$array, $section, $value )
{
	$values	= @$array[$section];
	return in_array( $value, (array) $values ) ? 'checked="checked"' : '';
}

function aclGroupChecked( &$array, $value )
{
	return in_array( $value, (array) $array ) ? 'checked="checked"' : '';
}

/**
 * @package		Joomla.Administrator
 * @subpackage	com_acl
 */
class AccessViewRule extends JView
{
	/**
	 * Display the view
	 *
	 * @access	public
	 */
	function display($tpl = null)
	{
		$state	= $this->get( 'State' );
		$item	= $this->get( 'ExtendedItem' );
		$acl	= $this->get( 'ACL' );

		//$layout = $this->getLayout();
		if ($state->get( 'id' )) {
			// Existing
		}
		else {
			// New
			$item->section_value	= 'user';
			$item->enabled			= 1;
			$item->allow			= 1;
		}

		$this->assignRef( 'state',		$state );
		$this->assignRef( 'item',		$item );
		$this->assignRef( 'acos',		$this->get( 'ACOs' ) );
		$this->assignRef( 'aroGroups',	$this->get( 'AROGroups' ) );
		$this->assignRef( 'axos',		$this->get( 'AXOs' ) );
		$this->assignRef( 'axoGroups',	$this->get( 'AXOGroups' ) );
		$this->assignRef( 'acl',		$acl );
		$this->assign( 'allow_axo_groups', $state->get( 'has_axo_groups', false ) );
		$this->assign( 'allow_axos', 0 );

		/*
		// this only happens for 3D rules
		if (!isset( $this->allow_axos )) {
			$this->assign( 'allow_axos', 0 );
			$temp	= array();
			foreach ($this->acos as $aco) {
				$temp[$aco->value]	= $aco->allow_axos;
			}
			// Scan ACO's
			if (isset( $this->acl['aco'] ) AND isset( $this->acl['aco'][$state->option] )) {
				foreach ($this->acl['aco'][$state->option] as $aco) {
					if (isset( $temp[$aco] ) && $temp[$aco] == 1) {
						$this->assign( 'allow_axos', 1 );
						break;
					}
				}
			}
		}
*/

		$this->_setToolBar();
		parent::display($tpl);
		JRequest::setVar('hidemainmenu',1);
	}

	/**
	 * Display the toolbar
	 *
	 * @access	private
	 */
	function _setToolBar()
	{
		// Set the toolbar
		if (empty($this->item->id)) {
			$title	= $this->allow_axos ? 'Add Role' : 'Add Rule';
		}
		else {
			$title	= $this->allow_axos ? 'Edit Role' : 'Edit Rule';
		}

		JToolBarHelper::title(JText::_('Access Control: '.$title));
		JToolBarHelper::custom('acl.save2new', 'new.png', 'new_f2.png', 'Toolbar Save And New', false,  false);
		JToolBarHelper::save('acl.save');
		JToolBarHelper::apply('acl.apply');
		JToolBarHelper::cancel('acl.cancel');
	}
}