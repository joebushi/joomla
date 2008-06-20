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
 * Joomla! JavaScript Toolbar Class
 * 
 * @author		Rob Schley <rob.schley@community.joomla.org>
 * @package		Joomla!
 * @subpackage	Javascript
 * @since		1.6
 */
var JToolbar = new Class
({
	Implements: Options,
	
	// The configuration options object.
	options: {
		legacy:		true
	},
	
	// Class constructor.
	initialize: function(bar, form, options)
	{
		// Merge in the optional options.
		this.setOptions(options);
	
		// Prepare the class state.
		this.element	= bar;
		this.form		= form;
		this.buttons	= new Hash();
		this.tasks		= new Hash();
		this.selected	= 0;
		
		form.store('toolbar', this);
		
		form.addEvent('listItemSelect', function(params) {
			var bar		= this.retrieve('toolbar');
			bar.selected += 1;
			
			bar.getButtons().each(function(button) {
				if (button.list) {
					button.disabled = false;
					button.element.getParent().removeClass('disabled');
				}
			});
		});
		
		form.addEvent('listItemDeselect', function() {
			var bar = this.retrieve('toolbar');
			bar.selected -= 1;
			
			if (bar.selected == 0) {
				bar.getButtons().each(function(button) {
					if (button.list) {
						button.disabled = true;
						button.element.getParent().addClass('disabled');
					}
				});
			}
		});
		
		// Get the child button elements.
		elements = this.element.getElements('td.button');
		
		// Load the button elements.
		for (i=0; i < elements.length; i++) {
			this.defButton(new JToolbarButton(elements[i], this));
		}
	},
	
	// Method to add a toolbar button.
	defButton: function(button)
	{
		// Add the button if id does not exist.
		if (!this.buttons.has(button.id)) {
			this.buttons.set(button.id, button);
		} 
	},
	
	// Method to get a button by id.
	getButton: function(id)
	{
		// Return the button if the id exists.
		if (this.buttons.has(id)) {
			return this.buttons.get(id);
		}
				
		return null;
	},
	
	// Method to get the toolbar buttons.
	getButtons: function()
	{
		return this.buttons;
	},
	
	// Method to set a button by id.
	setButton: function(id, button)
	{
		this.buttons.set(id, button);
	},
	
	// Method to get the toolbar's form.
	getForm: function()
	{
		return this.form;
	},
	
	// Method to set the toolbar's form.
	setForm: function(form)
	{
		return this.form = form;
	},
	
	// Method to get the state of legacy support.
	getLegacy: function()
	{
		return this.options.legacy;
	},
	
	// Method to set the state of legacy support.
	setLegacy: function(val)
	{
		return this.options.legacy = val;
	},
	
	// Method to execute a task.
	doTask: function(task)
	{
		params = arguments[1] ? arguments[1] : new Hash();
		
		// Execute the requested task.
		if (this.tasks.has(task)) {
			fn = this.tasks.get(task);
			fn(task, params);
		}
		
		// Execute the legacy support task if enabled and present.
		if (this.options.legacy && this.tasks.has('legacy')) {
			fn = this.tasks.get('legacy');
			fn(task, params);
		}
	},
	
	// Method to add a task if it does not exist.
	defTask: function(task, fn)
	{
		if (!this.tasks.has(task)) {
			this.tasks.set(task, fn);
		}
	},
	
	// Method to get a task if it exists.
	getTask: function(task)
	{
		if (this.tasks.has(task)) {
			return this.tasks.get(task);
		}
		
		return null;
	},
	
	// Method to get the tasks.
	getTasks: function()
	{
		return this.tasks;
	},
	
	// Method to set a task.
	setTask: function(task, fn)
	{
		this.tasks.set(task, fn);
	}
});

/**
 * Joomla! JavaScript  Button Class
 * 
 * @author		Rob Schley <rob.schley@community.joomla.org>
 * @package		Joomla!
 * @subpackage	Javascript
 * @since		1.6
 */
var JToolbarButton = new Class
({
	// Class constructor.
	initialize: function(element, bar)
	{
		// Prepare the class state.
		this.bar		= bar;
		this.id			= element.get('id');
		this.task		= this.id.split('-')[1];
		this.disabled	= element.hasClass('disabled');
		this.list		= element.hasClass('list');
		this.element	= $(element).getElement('a');
		
		// Store the object data inside the element.
		this.element.store('button', this);
		
		// Attach the onclick behavior.
		this.element.addEvent('click', function()
		{
			var button	= this.retrieve('button');
			var bar		= button.bar;
			var task	= button.task;
	
			if (!button.disabled) {
				bar.doTask(task);
			}
		});
		
		// Attach the onmouseover behavior.
		this.element.addEvent('mouseover', function()
		{
			var button = this.retrieve('button');
			
			if (!button.disabled) {
				this.getParent().addClass('button-hover');
			}
		});
		
		// Attach the onmouseout behavior.
		this.element.addEvent('mouseout', function()
		{
			var button = this.retrieve('button');
			
			if (!button.disabled) {
				this.getParent().removeClass('button-hover');
			}
		});
	}
});

window.addEvent('domready', function()
{
	var toolbar 	= document.retrieve('toolbar',	new JToolbar($('toolbar').getElement('table.toolbar'), $('blah')));
	var language	= document.retrieve('language',	new Hash());

	// Define the default legacy support task.
	toolbar.defTask('legacy', function(task, params)
	{
		// Check that the submitbutton function is defined.
		if ($defined(window.submitbutton) && $type(window.submitbutton) == 'function') {
			submitbutton(task);
		}
	});
	
	// Define the default unarchive task.
	toolbar.defTask('unarchive', function(task, params)
	{
		console.log(task);
		return true;
		
		var form = toolbar.getForm();
		form.fireEvent('submit');
		form.submit();
	});
	
	document.store('toolbar', toolbar);
});