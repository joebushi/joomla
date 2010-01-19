<?php
/**
 * @version		$Id$
 * @package		Joomla.Framework
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.base.node');
/**
 * JCategories Class.
 *
 * @package 	Joomla.Framework
 * @subpackage	Application
 * @since		1.6
 */
class JCategories
{
	/**
	 * Array to hold the object instances
	 *
	 * @param array
	 */
	static $instances = array();

	/**
	 * Array of category nodes
	 *
	 * @var mixed
	 */
	protected $_nodes = null;

	/**
	 * Name of the extension the categories belong to
	 *
	 * @var string
	 */
	protected $_extension = null;

	/**
	 * Name of the linked content table to get category content count
	 *
	 * @var string
	 */
	protected $_table = null;

	/**
	 * Name of the category field
	 *
	 * @var string
	 */
	protected $_field = null;

	/**
	 * Name of the key field
	 *
	 * @var string
	 */
	protected $_key = null;

	/**
	 * Array of options
	 *
	 * @var array
	 */
	protected $_options = null;

	/**
	 * Class constructor
	 *
	 * @access public
	 * @return boolean True on success
	 */
	public function __construct($options)
	{
		$this->_extension 	= $options['extension'];
		$this->_table		= $options['table'];
		$this->_field		= (isset($options['field'])&&$options['field'])?$options['field']:'catid';
		$this->_key			= (isset($options['key'])&&$options['key'])?$options['key']:'id';
		$this->_options		= $options;
		return true;
	}

	/**
	 * Returns a reference to a JCategories object
	 *
	 * @param $extension Name of the categories extension
	 * @param $options An array of options
	 * @return object
	 */
	public static function getInstance($extension, $options = array())
	{
		if (isset(self::$instances[$extension]))
		{
			return self::$instances[$extension];
		}
		$parts = explode('.',$extension);
		$component = $parts[0];
		$section = (count($parts)>1)?$parts[1]:'';
		$classname = ucfirst(substr($component,4)).ucfirst($section).'Categories';
		if (!class_exists($classname))
		{
			$path = JPATH_SITE.DS.'components'.DS.$component.DS.'helpers'.DS.'category.php';
			if (is_file($path))
			{
				require_once $path;
			} else {
				return false;
			}
		}
		self::$instances[$extension] = new $classname($options);
		return self::$instances[$extension];
	}

	/**
	 * Loads a specific category and all its children in a JCategoryNode object
	 * @param $id
	 * @return JCategoryNode
	 */
	public function get($id,$options=array())
	{
		$id = (int) $id;
		if ($id == 0)
		{
			return false;
		}
		if (!isset($this->_nodes[$id]) || (isset($options['reload']) && $options['reload']))
		{
			$this->_load($id,$options);
		}
		if ($this->_nodes[$id] instanceof JCategoryNode)
		{
			return $this->_nodes[$id];
		} else {
			throw new JException('Unable to load category: '.$id, 0000, E_ERROR, $info, true);
		}
	}

	protected function _load($id,$options)
	{
		$db	= JFactory::getDbo();
		$user = JFactory::getUser();
		$extension = $this->_extension;
		
		$children = !isset($options['children']) || !$options['children'];
		$siblings= !isset($options['siblings']) || !$options['siblings'];
		
		$query = new JQuery;
		
		// c for category
		$query->select('c.*');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
		$query->from('#__categories as c');
		$query->where('c.parent_id<>0');
		$query->where('c.extension='.$db->Quote($extension));
		$query->where('c.access IN ('.implode(',', $user->authorisedLevels()).')');		
		$query->order('c.lft');
		$query->group('c.id');

		// s for selected id
		if (!empty($id))
		{
			// Get the parents
			$test = '(s.lft<=c.lft AND s.rgt >= c.rgt)'; 
			// Get the children
			$test.=$children ? ' OR (s.lft>=c.lft AND s.rgt <= c.rgt)':'';
			// Get the siblings
			$test.=$siblings ? ' OR (s.parent_id=c.parent_id)':'';
			
			$query->leftJoin('#__categories AS s ON ' . $test);
			$query->where('s.id='.(int)$id);
		}

		// i for item
		$query->leftJoin($db->nameQuote($this->_table).' AS i ON i.'.$db->nameQuote($this->_field).' = c.id ');
		$query->select('COUNT(i.'.$db->nameQuote($this->_key).') AS numitems');
		
		var_dump((string)$query);
		
		// Add category extra fields
		$parts = explode('.',$extension);
		$component = $parts[0];
		$section = (count($parts)>1)?$parts[1]:'';
		jimport('joomla.application.component.model');
		JModel::addIncludePath(JPATH_ADMINISTRATOR .'/components/com_categories/models');
		$model = JModel::getInstance('Category','CategoriesModel',array('ignore_request'=>true));
		$model->setState('category.extension',$extension);
		$model->setState('category.component',$component);
		$model->setState('category.section',$section);
		$form=$model->getForm();
		// foreach all groups
		foreach ($form->getFieldsets() as $name => $fieldSet)
		{
			// Don't use params, metadata and _default group	
			if (!in_array($name, array('params','metadata','_default')))
			{
				// Foreach fields in this group
				foreach($form->getFields($name) as $field)
				{
					// Add the field as #{group}{field} in the query
					$query->leftJoin('#__category_attributes as '.$db->nameQuote($name.$field->name).' ON '.$db->nameQuote($name.$field->name).'.catid=c.id AND '.$db->nameQuote($name.$field->name).'.group='.$db->Quote($name).' AND '.$db->nameQuote($name.$field->name).'.field='.$db->Quote($field->name));
					$query->select($db->nameQuote($name.$field->name).'.value AS '.$db->nameQuote('_'.$name.'_'.$field->name));
				}
			}
		}

		// Get the results
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		if (count($results))
		{
			// foreach categories
			foreach($results as $result)
			{
				// foreach all groups
				foreach ($form->getFieldsets() as $name => $fieldSet)
				{
					// Don't use params, metadata and _default group	
					if (!in_array($name, array('params','metadata','_default')))
					{
						// Prepare the attribute equal to the group name
						$result->{$name}=array();
						// Foreach fields in this group
						foreach($form->getFields($name) as $field)
						{
							// For each field in this group, add it to the array
							$result->{$name}[$field->name]=$result->{'_'.$name.'_'.$field->name};
							// Unset the special field
							unset($result->{'#'.$name.$field->name});
						}
					}
				}
				// Convert the params field to an array.
				$registry = new JRegistry();
				$registry->loadJSON($result->params);
				$result->params = $registry->toArray();

				// Convert the metadata field to an array.
				$registry = new JRegistry();
				$registry->loadJSON($result->metadata);
				$result->metadata = $registry->toArray();
				
				// Create the JCategoryNode
				$this->_nodes[$result->id] = new JCategoryNode($result);
			}
		} else {
			$this->_nodes[$id] = false;
		}
	}
}

/**
 * Helper class to load Categorytree
 * @author Hannes
 * @since 1.6
 */
class JCategoryNode extends JNode
{
	/** @var int Primary key */
	public $id					= null;
	public $lft					= null;
	public $rgt					= null;
	public $ref_id				= null;
	public $parent_id			= null;
	/** @var int */
	public $extension			= null;
	public $lang					= null;
	/** @var string The menu title for the category (a short name)*/
	public $title				= null;
	/** @var string The the alias for the category*/
	public $alias				= null;
	/** @var string */
	public $description			= null;
	/** @var boolean */
	public $published			= null;
	/** @var boolean */
	public $checked_out			= 0;
	/** @var time */
	public $checked_out_time		= 0;
	/** @var int */
	public $access				= null;
	/** @var string */
	public $params				= null;
	/** @var int */
	public $numitems				= null;
	/** @var string */
	public $slug					= null;

	/**
	 * Class constructor
	 * @param $category
	 * @return unknown_type
	 */
	public function __construct($category = null)
	{
		if ($category)
		{
			$this->setProperties($category);
			if ($this->parent_id > 1)
			{
				$categoryTree = JCategories::getInstance($this->extension);
				$parentNode = &$categoryTree->get($this->parent_id);
				$parentNode->addChild($this);
			}
			return true;
		}
		return false;
	}
}
