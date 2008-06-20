/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU General Public License
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * List Behavior
 * 
 * @author		Rob Schley <rob.schley@community.joomla.org>
 * @package		Joomla!
 * @subpackage	Javascript
 * @since		1.6
 */
var JList = new Class
({
	Implements: Options,
	
	// The configuration options object.
	options: {
		
	},
	
	// Class constructor.
	initialize: function(form, options)
	{
		this.form	= form;
		this.items	= new Array();
		
		// Get the child item elements.
		items = form.getElements('tr.item');
		
		// Load the button elements.
		for (i=0; i < items.length; i++) {
			this.addItem(new JListItem(items[i], this));
		}
	},
	
	// Method to add an item to the end of the list.
	addItem: function(item)
	{
		this.items.push(item);
	},
	
	// Method to get an item by its position in the list.
	getItem: function(pos)
	{
		if ($defined(this.items[pos])) {
			return this.items[pos];
		}
		
		return null;
	},
	
	// Method to get the list of items.
	getItems: function()
	{
		return this.items;
	},
	
	// Method to set an item in the list at a specific position.
	setItem: function(pos, item)
	{
		return this.items[pos] = item;
	}
});


/**
 * List Item Behavior
 * 
 * @author		Rob Schley <rob.schley@community.joomla.org>
 * @package		Joomla!
 * @subpackage	Javascript
 * @since		1.6
 */
var JListItem = new Class
({
	// Class constructor.
	initialize: function(item, form)
	{
		this.item		= item;
		this.form		= form;
		this.selector	= null
		
		// Store the object data inside the element.
		this.item.store('item', this);
	}
});