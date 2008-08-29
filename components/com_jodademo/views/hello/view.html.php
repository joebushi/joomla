<?php
/**
 * @package    Joomla.JodaDemo
 * @subpackage Components
 * @license    GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 *
 * @package    HelloWorld
 */

class JodaDemoViewJodaDemo extends JView
{
	function display($tpl = null)
	{
		$greeting = "Hello, World! This is Joda Demonstration!";
		$this->assignRef( 'greeting', $greeting );

		parent::display($tpl);
	}
}