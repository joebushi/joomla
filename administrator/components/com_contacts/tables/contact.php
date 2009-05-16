<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Contact Table class
 *
 * @package		Joomla.Administrator
 * @subpackage	Contacts
 */
class TableContact extends JTable
{
	/** @var int Primary key */
	var $id = null;
	/** @var string */
	var $name = '';
	/** @var string */
	var $alias = '';
	/** @var int */
	var $published = 0;
	/** @var int */
	var $checked_out = 0;
	/** @var time */
	var $checked_out_time = 0;	
	/** @var string */
	var $params = null;
	/** @var int */
	var $user_id = 0;		
	/** @var int */
	var $access = 0;

	/**
	* @param database A database connector object
	*/
	public function __construct(&$db)
	{
		parent::__construct('#__contacts_contacts', 'id', $db);
	}
	
	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	public function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{
		if (empty($this->alias)) {
			$this->alias = $this->name;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);
		if (trim(str_replace('-','',$this->alias)) == '') {
			$datenow = &JFactory::getDate();
			$this->alias = $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
		}
		
		/** check for valid name */
		if (trim($this->name) == '') {
			$this->setError(JText::_('Your Contact must have a name.'));
			return false;
		}

		/** check for existing name*/
		$query = 'SELECT id FROM #__contacts_contacts WHERE name = '.$this->_db->Quote($this->name);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::sprintf('WARNNAMETRYAGAIN', JText::_('Contact')));
			return false;
		}
		return true;
	}
	
	/**
	 * Overloaded store method
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
    function store( $data )
    {
        if($this->id != null) {
	        if (!$this->_db->updateObject('#__contacts_contacts', $this, 'id', false)) {
	            $this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
	            return false;
	        }
	        
	        $query = "SELECT id FROM #__contacts_fields WHERE published = 1 ORDER BY pos, ordering";
	        $this->_db->setQuery($query);
	        $tables = $this->_db->loadObjectList();
        	if (!$tables) {
				$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
				return false;
			}
	        
	        $i = 0;
	        $fields = $data['fields'];
	        
	        foreach ($fields as $field) {
	        	$field = addslashes($field);
	        	$query = "UPDATE #__contacts_details SET data = '$field', show_contact = ".$data['showContact'.$i].", show_directory = ".$data['showDirectory'.$i]." WHERE contact_id = $this->id AND field_id = ".$tables[$i]->id;
	        	$this->_db->setQuery($query);
		        if (!$this->_db->query()) {
					$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
					return false;
				}
				$i++;
	        }
	        
	        $query = "SELECT category_id FROM #__contacts_con_cat_map WHERE contact_id = '$this->id'";
	        $this->_db->setQuery($query);
	        $cat_map = $this->_db->loadResultArray();
        	if (!$cat_map) {
				$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
				return false;
			}
	        
	        $categories = $data['categories'];
	        $ordering = $data['ordering'];
	        
	        $i = 0;
	        foreach ($categories as $category) {
	        	$found = false;
	        	for ($k=0; $k<count($cat_map); $k++) {
	        		if ($category == $cat_map[$k]) {
	        			$found = true;
	        			$cat_map[$k] = -1;
	        			$query = "UPDATE #__contacts_con_cat_map SET ordering = '$ordering[$i]' WHERE contact_id = '$this->id' AND category_id = '$category'";
		        		$this->_db->setQuery($query);
			        	if (!$this->_db->query()) {
							$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
							return false;
						}
	        		}
	        	}
	        	if (!$found) {
			        $query = "SELECT MAX(ordering) FROM #__contacts_con_cat_map WHERE category_id = '$category'";
			        $this->_db->setQuery( $query );
			        $maxord = $this->_db->loadResult();
			        if ($this->_db->getErrorNum()) {
						$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
			            return false;
			        }	        		
	        		$maxord++;

	        		$query = "INSERT INTO #__contacts_con_cat_map VALUES('$this->id', '$category', '$maxord')";
		        	$this->_db->setQuery($query);
			        if (!$this->_db->query()) {
						$this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
						return false;
					}
	        	}
	        	$i++;
	        }
	        
        	for ($k=0; $k<count($cat_map); $k++) {
        		if ($cat_map[$k] != -1) {
        			$query = "DELETE FROM #__contacts_con_cat_map WHERE category_id = '$cat_map[$k]' AND contact_id = '$this->id'";
	        		$this->_db->setQuery($query);
			        if (!$this->_db->query()) {
						$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
						return false;
					}
        		}
        		
        		// Reorder the ordering
				$query = "SELECT contact_id, category_id, ordering "
		        			."FROM #__contacts_con_cat_map "
		        			."WHERE ordering >= 0 AND  category_id = '$category' " 
		        			."ORDER BY ordering";
	         	$this->_db->setQuery( $query );
		        if (!($orders = $this->_db->loadObjectList())) {
		            $this->setError($this->_db->getErrorMsg());
		            return false;
		        }				
		            
		         // compact the ordering numbers
		        for ($i=0, $n=count( $orders ); $i < $n; $i++) {
		            if ($orders[$i]->ordering >= 0) {
		                if ($orders[$i]->ordering != $i+1) {
		                    $orders[$i]->ordering = $i+1;
		                    $query = 'UPDATE #__contacts_con_cat_map SET ordering = '. (int) $orders[$i]->ordering
		                    				.' WHERE contact_id = '. $this->_db->Quote($orders[$i]->contact_id)
		                    				.' AND category_id = '.$this->_db->Quote($orders[$i]->category_id);
		                    $this->_db->setQuery($query);
			                if (!$this->_db->query()) {
								$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
								return false;
							}
		                }
		            }
		        }
        	}
	        
        } else {
            $ret = $this->_db->insertObject('#__contacts_contacts', $this, 'id');
            $this->id = $this->_db->insertid();

        	if (!$ret || $this->id == null) {
	            $this->setError(get_class( $this ).'::store failed - '.$this->_db->getErrorMsg());
	            return false;
	        }
	        
        	$query = "SELECT id FROM #__contacts_fields WHERE published = 1 ORDER BY pos, ordering";
	        $this->_db->setQuery($query);
	        $tables = $this->_db->loadObjectList();
        	if (!$tables) {
				$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
				return false;
			}
	        
	        $i = 0;
	        $fields = $data['fields'];
	        
	        foreach ($fields as $field) {
	        	$field = addslashes($field);
	        	$query = "INSERT INTO #__contacts_details VALUES('$this->id', '".$tables[$i]->id."', '$field', '".$data['showContact'.$i]."', '".$data['showDirectory'.$i]."')";
	        	$this->_db->setQuery($query);
		        if(!$this->_db->query()) {
					$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
					return false;
				}
				$i++;
	        }
	        
	        $categories = $data['categories'];
	        
	        $i = 0;
	        foreach ($categories as $category) {
	        	$query = "SELECT MAX(ordering) FROM #__contacts_con_cat_map WHERE category_id = '$category'";
		        $this->_db->setQuery($query);
		        $maxord = $this->_db->loadResult();
		        if ($this->_db->getErrorNum()) {
					$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
		            return false;
		        }
		        if ($maxord == -1) {
		        	$maxord += 2;	        		
		        }
        		$maxord++;
	        	
	        	$query = "INSERT INTO #__contacts_con_cat_map VALUES('$this->id', '$category', '$maxord')";
	        	$this->_db->setQuery($query);
	        	if(!$this->_db->query()) {
					$this->setError(get_class($this).'::store failed - '.$this->_db->getErrorMsg());
					return false;
				}        					
	        }
        }
        return true;
    }
}	
?>