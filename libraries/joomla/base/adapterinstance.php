<?php

class JAdapterInstance extends JObject {
	
	/** Parent 
	 * @var object */
	var $parent = null;
	
	
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$parent	Parent object [JAdapter instance]
	 * @return	void
	 * @since	1.5
	 */
	function __construct(&$parent)
	{
		$this->parent =& $parent;
	}
}