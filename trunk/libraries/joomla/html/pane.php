<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

/**
 * JPane abstract class
 *
 * @abstract
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
abstract class JPane extends JObject
{

	public $useCookies = false;

	/**
	* Constructor
	*
 	* @param	array	$params		Associative array of values
	*/
	function __construct($params = array())
	{
	}

	/**
	 * Returns a reference to a JPanel object
	 *
	 * @param	string 	$behavior   The behavior to use
	 * @param	boolean	$useCookies Use cookies to remember the state of the panel
	 * @param	array 	$params		Associative array of values
	 * @return	object
	 */
	public static function &getInstance($behavior = 'Tabs', $params = array())
	{
		$classname = 'JPane'.$behavior;
		$instance = new $classname($params);

		return $instance;
	}

	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @abstract
	 * @param	string	The pane identifier
	 */
	abstract public function startPane($id);

	/**
	 * Ends the pane
	 *
	 * @abstract
	 */
	abstract public function endPane();

	/**
	 * Creates a panel with title text and starts that panel
	 *
	 * @abstract
	 * @param	string	$text The panel name and/or title
	 * @param	string	$id The panel identifer
	 */
	abstract public function startPanel($text, $id);

	/**
	 * Ends a panel
	 *
	 * @abstract
	 */
	abstract public function endPanel();

	/**
	 * Load the javascript behavior and attach it to the document
	 *
	 * @abstract
	 */
	abstract protected function _loadBehavior();
}

/**
 * JPanelTabs class to to draw parameter panes
 *
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class JPaneTabs extends JPane
{
	/**
	 * @var array array of loaded panes
	 */
	protected static $loaded=array();
	
	/**
	 * @var string name of pane
	 */
	protected $name=null;
	
	/**
	 * Constructor
	 *
	 * @param array $params array of params to construct the pane
	 */
	function __construct($params = array())
	{
		parent::__construct($params);
		
		if (array_key_exists('name',$params)) {
			$this->name = $params['name'];
		}
		else {
			$this->name = 'default';
		}

		if (!array_key_exists($this->name,$loaded)) {
			$this->_loadBehavior($params);
			$loaded[$this->name] = true;
		}
	}

	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param string The pane identifier
	 */
	public function startPane($id)
	{
		return '<div class="'.$this->name.'"><dl class="tabs" id="'.$id.'">';
	}

	/**
	 * Ends the pane
	 */
	public function endPane()
	{
		return "</dl></div>";
	}

	/**
	 * Creates a tab panel with title text and starts that panel
	 *
	 * @param	string	$text	The name of the tab
	 * @param	string	$id		The tab identifier
	 */
	public function startPanel($text, $id)
	{
		return '<dt id="'.$id.'"><span>'.$text.'</span></dt><dd>';
	}

	/**
	 * Ends a tab page
	 */
	public function endPanel()
	{
		return "</dd>";
	}

	/**
	 * Load the javascript behavior and attach it to the document
	 *
	 * @param	array 	$params		Associative array of values
	 */
	protected function _loadBehavior($params = array())
	{
		// Include mootools framework
		JHtml::_('behavior.framework', true);

		$document = &JFactory::getDocument();

		$options = '{';
		$opt['onActive']		= (isset($params['onActive'])) ? $params['onActive'] : null ;
		$opt['onBackground'] = (isset($params['onBackground'])) ? $params['onBackground'] : null ;
		$opt['display']		= (isset($params['startOffset'])) ? (int)$params['startOffset'] : null ;
		foreach ($opt as $k => $v)
		{
			if ($v) {
				$options .= $k.': '.$v.',';
			}
		}
		if (substr($options, -1) == ',') {
			$options = substr($options, 0, -1);
		}
		$options .= '}';

		$js = '		window.addEvent(\'domready\', function(){ $$(\'.'.$this->name.' dl.tabs\').each(function(tabs){ new JTabs(tabs, '.$options.'); }); });';

		$document->addScriptDeclaration($js);
		$document->addScript(JURI::root(true). '/media/system/js/tabs.js');
	}
}

/**
 * JPanelSliders class to to draw parameter panes
 *
 * @package		Joomla.Framework
 * @subpackage	HTML
 * @since		1.5
 */
class JPaneSliders extends JPane
{
	/**
	 * @var array array of loaded panes
	 */
	protected static $loaded=array();
	
	/**
	 * @var string name of pane
	 */
	protected $name=null;
	
	/**
	 * Constructor
	 *
	 * @param array $params array of params to construct the pane
	 */
	function __construct($params = array())
	{
		parent::__construct($params);
		
		if (array_key_exists('name',$params)) {
			$this->name = $params['name'];
		}
		else {
			$this->name = 'default';
		}

		if (!array_key_exists($this->name,$loaded)) {
			$this->_loadBehavior($params);
			$loaded[$this->name] = true;
		}
	}

	/**
	 * Creates a pane and creates the javascript object for it
	 *
	 * @param string The pane identifier
	 */
	public function startPane($id)
	{
		return '<div class="'.$this->name.'"><div id="'.$id.'" class="pane-sliders">';
	}

	/**
	 * Ends the pane
	 */
	public function endPane()
	{
		return '</div></div>';
	}

	/**
	 * Creates a tab panel with title text and starts that panel
	 *
	 * @param	string	$text - The name of the tab
	 * @param	string	$id - The tab identifier
	 */
	public function startPanel($text, $id)
	{
		return '<div class="panel">'
			.'<h3 class="jpane-toggler title" id="'.$id.'"><span>'.$text.'</span></h3>'
			.'<div class="jpane-slider content">';
	}

	/**
	 * Ends a tab page
	 */
	public function endPanel()
	{
		return '</div></div>';
	}

	/**
	 * Load the javascript behavior and attach it to the document
	 *
	 * @param	array 	$params		Associative array of values
	 */
	protected function _loadBehavior($params = array())
	{
		// Include mootools framework
		JHtml::_('behavior.framework', true);

		$document = &JFactory::getDocument();

		$options = '{';
		$opt['onActive']	 = 'function(toggler, i) { toggler.addClass(\'jpane-toggler-down\'); toggler.removeClass(\'jpane-toggler\'); }';
		$opt['onBackground'] = 'function(toggler, i) { toggler.addClass(\'jpane-toggler\'); toggler.removeClass(\'jpane-toggler-down\'); }';
		$opt['duration']	 = (isset($params['duration'])) ? (int)$params['duration'] : 300;
		$opt['display']		 = (isset($params['startOffset']) && ($params['startTransition'])) ? (int)$params['startOffset'] : null ;
		$opt['show']		 = (isset($params['startOffset']) && (!$params['startTransition'])) ? (int)$params['startOffset'] : null ;
		$opt['opacity']		 = (isset($params['opacityTransition']) && ($params['opacityTransition'])) ? 'true' : 'false' ;
		$opt['alwaysHide']	 = (isset($params['allowAllClose']) && (!$params['allowAllClose'])) ? 'false' : 'true';
		foreach ($opt as $k => $v)
		{
			if ($v) {
				$options .= $k.': '.$v.',';
			}
		}
		if (substr($options, -1) == ',') {
			$options = substr($options, 0, -1);
		}
		$options .= '}';

		$js = '		window.addEvent(\'domready\', function(){ new Accordion($$(\'.'.$this->name.' .panel h3.jpane-toggler\'), $$(\'.'.$this->name.' div.jpane-slider\'), '.$options.'); });';

		$document->addScriptDeclaration($js);
	}
}
