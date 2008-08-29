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

class JodaDemoViewHello extends JView
{
	function display($tpl = null)
	{
		$greeting = "Hello, World! This is Joda Demonstration!";
		$description = "Joda is an attempto to make Joomla! a multidatabase framework and CMS";
		$this->assignRef( 'greeting', $greeting );
		$this->assignRef( 'description', $description );
		
		parent::display($tpl);
	}
}