<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import the JModel class
jimport('joomla.application.component.model');

class ContactsModelContact extends JModel {

	var $_id = null;
	var $_data = null;
	var $_fields = null;
	var $_categories = null;
	
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit	= JRequest::getVar('edit',true);
		if ($edit){
			$this->setId((int)$array[0]);
		}
	}

	/**
	 * Method to set the contact identifier
	 *
	 * @access	public
	 * @param	int Contact identifier
	 */
	public function setId($id)
	{
		// Set contact id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}	

	/**
	 * Method to get a contact
	 *
	 * @since 1.5
	 */
	public function &getData()
	{
		// Load the contact data
		$result = $this->_loadData();
		if (!$result) $this->_initData();

		return $this->_data;
	}

	public function &getFields()
	{
		if(!$this->_fields){
			$query = "SELECT f.title, d.data, f.type, d.show_contact, d.show_directory, f.params "
					."FROM #__contacts_fields f "
					."LEFT JOIN #__contacts_details d ON d.field_id = f.id "
					."WHERE f.published = 1 AND d.contact_id = '$this->_id'"
					."ORDER BY f.pos, f.ordering";
			$this->_db->setQuery($query);
			$this->_fields = $this->_db->loadObjectList();	
		}
		return $this->_fields;
	}
	
	public function &getCategories()
	{
		if(!$this->_categories){
			$query = "SELECT c.title, map.category_id AS id, map.ordering "
					."FROM jos_categories c "
					."LEFT JOIN jos_contacts_con_cat_map map ON map.category_id = c.id "
					."WHERE c.published = 1 AND map.contact_id = '$this->_id'"
					."ORDER BY c.lft";
			$this->_db->setQuery($query);
			$this->_categories = $this->_db->loadObjectList();
		}
		return $this->_categories;
	}
	
	/**
	 * Method to load the contact data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	protected function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT * FROM #__contacts_contacts WHERE id = '.(int) $this->_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}
	
	protected function _initData(){
		// Lets load the field data if it doesn't already exist
		if (empty($this->_data))
		{
			$contact = new stdClass();
			$contact->id	= null;
			$contact->name = '';
			$contact->alias = '';
			$contact->published = 0;
			$contact->checked_out = 0;
			$contact->checked_out_time	= 0;
			$contact->params	 = null;
			$contact->user_id	= 0;
			$contact->access = 0;
			$this->_data				= $contact;
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Tests if contact is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	public function isCheckedOut( $uid=0 )
	{
		if ($this->_loadData())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}	
	
	/**
	 * Method to checkin/unlock the contact
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function checkin()
	{
		if ($this->_id)
		{
			$contact = & $this->getTable();
			if(! $contact->checkin($this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	/**
	 * Method to checkout/lock the contact
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the article out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function checkout($uid = null)
	{
		if ($this->_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$contact = & $this->getTable();
			if(!$contact->checkout($uid, $this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}	

	/**
	 * Method to store the contact
	 *
	 * @access	public
	 * @return	the id on success or false if error
	 * @since	1.5
	 */
	public function store($data)
	{	
		$row =& $this->getTable();
		
		// Bind the form contacts to the contact table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}		
		
		// Create the timestamp for the date
		$row->checked_out_time = gmdate('Y-m-d H:i:s');		
		
		// Make sure the contacts table is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Store the data in the different database tables
		if (!$row->store($data)) {
			$this->setError($row->getError());
			return false;
		}
		return $row->id;
	}
	
	/**
	 * Method to remove a contact
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function delete($cid = array())
	{
		$result = false;

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__contacts_contacts'
				. ' WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			$query = 'DELETE FROM #__contacts_details WHERE contact_id IN ('.$cids.')';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			
			$query = 'DELETE FROM #__contacts_con_cat_map WHERE contact_id IN ('.$cids.')';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to (un)publish a contact
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function publish($cid = array(), $publish = 1)
	{
		$user =& JFactory::getUser();

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__contacts_contacts'
				. ' SET published = '.(int) $publish
				. ' WHERE id IN ( '.$cids.' )'
				. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ) )';
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}	
	
	/**
	* Set the access of selected menu items
	*/
	public function setAccess( $cid = array(), $access=0 )
	{
		$row =& $this->getTable();
		foreach ($items as $id)
		{
			$row->load( $id );
			$row->access = $access;

			if (!$row->check()) {
				$this->setError($row->getError());
				return false;
			}
			if (!$row->store()) {
				$this->setError($row->getError());
				return false;
			}
		}
		return true;
	}
}

?>