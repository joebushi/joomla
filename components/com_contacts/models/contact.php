<?php
/**
 * @version		$Id: contact.php 10094 2008-03-02 04:35:10Z instance $
 * @package		Joomla
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * @package		Joomla
 * @subpackage	Contact
 */
class ContactsModelContact extends JModel
{
	var $_id = null;
	var $_data = null;
	var $_fields = null;
	var $_email = null;
	var $_categories = null;
	var $_formData = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_id	= JRequest::getVar('id', 0, '', 'int');
		
		$this->_formData = new stdClass();
		$this->_formData->name = JRequest::getString('name', '', 'post');
		$this->_formData->email = JRequest::getString('email', '', 'post');
		$this->_formData->subject = JRequest::getString('subject', '', 'post');
		$this->_formData->body = JRequest::getString('body', '', 'post');
		$this->_formData->email_copy = JRequest::getString('email_copy', '', 'post');
		$this->_formData->captcha = JRequest::getString('captcha', '', 'post');
	}
	
	public function &getFormData()
	{
		return $this->_formData;
	}

	/**
	 * Method to get a contact
	 *
	 * @since 1.5
	 */
	public function &getData($access)
	{
		// Load the contact data
		if ($this->_loadData())
		{
			// Initialize some variables
			$user = &JFactory::getUser();

			// Make sure the contact is published
			if (!$this->_data->published) {
				JError::raiseError(404, JText::_("Resource Not Found"));
				return false;
			}

			// Check to see if at least one of the categories is published
			$this->getCategories();
			$found = false;
			foreach ($this->_categories as $category) {
				if($category->published == 1){
					$found = true;
				}
			}
			if (!$found) {
				JError::raiseError( 404, JText::_("Resource Not Found") );
				return false;
			}

			// Check whether category access level allows access
			if ($this->_data->access > $access) {
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return false;
			}
		}
		else  $this->_initData();

		return $this->_data;
	}

	public function &getFields()
	{
		if(!$this->_fields){
			$query = " SELECT f.id, f.title, d.data, f.pos, f.type, d.show_contact AS show_field, f.params, f.access "
					." FROM #__contacts_fields f "
					." LEFT JOIN #__contacts_details d ON d.field_id = f.id "
					." WHERE f.published = 1 AND d.contact_id = $this->_id"
					." ORDER BY f.pos, f.ordering ";
			$this->_db->setQuery($query);
			$this->_fields = $this->_db->loadObjectList();	
		}
		return $this->_fields;
	}
	
	public function &getEmail()
	{
		if(!$this->_email){
			$query = " SELECT d.data FROM #__contacts_details d "
							." JOIN  #__contacts_fields f ON d.field_id = f.id "
							." WHERE f.published = 1 AND d.field_id = 1 AND d.contact_id = $this->_id ";
			$this->_db->setQuery($query);
			$this->_email = $this->_db->loadResult();	
		}
		return $this->_email;
	}
	
	public function &getCategories()
	{
		if(!$this->_categories){
			$query = " SELECT c.title, c.published, map.category_id AS id, map.ordering "
					." FROM jos_categories c "
					." LEFT JOIN jos_contacts_con_cat_map map ON map.category_id = c.id "
					." WHERE map.contact_id = '$this->_id' ";
			$this->_db->setQuery($query);
			$this->_categories = $this->_db->loadObjectList();
		}
		return $this->_categories;
	}
	
	/**
	 * Method to load content contact data
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

	/**
	 * Method to initialise the contact data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	protected function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$contact = new stdClass();
			$contact->id	= 0;
			$contact->name = null;
			$contact->alias	= null;
			$contact->published = 0;
			$contact->checked_out	= 0;
			$contact->checked_out_time	= 0;
			$contact->params	 = null;
			$contact->user_id	= 0;
			$contact->access = 0;
			$this->_data	= $contact;
			return (boolean) $this->_data;
		}
		return true;
	}
	
	public function mailTo($access) {
		$mainframe = JFactory::getApplication();
		
		$pparams =& $mainframe->getParams('com_contacts');
		$SiteName = $mainframe->getCfg('sitename');
		$default = JText::sprintf( '%s Enquiry', $SiteName );
		
		$subject = $this->_formData->subject;
		if(!$subject) { $subject = $default; }
		
		$contact =& $this->getData($access);
		$email_to =& $this->getEmail();

		$cparams =  new JParameter($contact->params);
		
		if($email_to == '' && $contact->user_id != 0){
			$contact_user = JUser::getInstance($contact->user_id);
			$email_to = $contact_user->get('email');
		}

		jimport('joomla.mail.helper');
		if (!$this->_formData->email || !$this->_formData->body || (JMailHelper::isEmailAddress($this->_formData->email) == false))
		{
			$this->setError(JText::_('Please make sure the form is complete and valid.'));
			return false;
		}
		
		JPluginHelper::importPlugin( 'contact' );
		$dispatcher =& JDispatcher::getInstance();
				
		if  (!$this->_validateInputs( $contact, $this->_formData->email, $this->_formData->subject, $this->_formData->body, $this->_formData->captcha ) ) {
			return false;
		}

		$post = JRequest::get( 'post' );
		$results	= $dispatcher->trigger( 'onValidateContact', array( &$contact, &$post ) );

		foreach ($results as $result)
		{
			if (JError::isError( $result )) {
				return false;
			}
		}

		$results	= $dispatcher->trigger( 'onSubmitContact', array( &$contact, &$post ) );
				
		if (!$pparams->get('custom_reply')) {
			$MailFrom = $mainframe->getCfg('mailfrom');
			$FromName = $mainframe->getCfg('fromname');

			$prefix = JText::sprintf('This is an enquiry e-mail via %s from:', JURI::base());
			$body 	= $prefix."\n".$this->_formData->name.' <'.$this->_formData->email.'>'."\r\n\r\n".stripslashes($this->_formData->body);
			
			$mail = JFactory::getMailer();

			$mail->addRecipient($email_to);
			$mail->setSender(array( $this->_formData->email, $this->_formData->name));
			$mail->setSubject($FromName.': '. $subject);
			$mail->setBody($body);

			$sent = $mail->Send();

			$params = new JParameter($contact->params);
			$emailcopyCheck = $params->get( 'show_email_copy', 0 );

			if ( $this->_formData->email_copy && $emailcopyCheck )
			{
				$copyText 		= JText::sprintf('Copy of:', $contact->name, $SiteName);
				$copyText 		.= "\r\n\r\n".$body;
				$copySubject 	= JText::_('Copy of:')." ".$subject;

				$mail = JFactory::getMailer();

				$mail->addRecipient( $this->_formData->email );
				$mail->setSender( array( $MailFrom, $FromName ) );
				$mail->setSubject( $copySubject );
				$mail->setBody( $copyText );

				$sent = $mail->Send();
			}
		}
		return true;
	}
	
	protected function _validateInputs( $contact, $email, $subject, $body, $captcha ) {
		global $mainframe;

		$session =& JFactory::getSession();

		$params	= new JParameter($contact->params);
		$pparams = &$mainframe->getParams('com_contacts');

		$sessionCheck = $pparams->get( 'validate_session', 1 );
		$sessionName	= $session->getName();
		if  ( $sessionCheck ) {
			if ( !isset($_COOKIE[$sessionName]) ) {
				$this->setError( JText::_('You are not authorised to view this resource.') );
				return false;
			}
		}

		$configEmail = $pparams->get( 'banned_email', '' );
		$paramsEmail = $params->get( 'banned_mail', '' );
		$bannedEmail = $configEmail . ($paramsEmail ? ';'.$paramsEmail : '');

		if ( $bannedEmail ) {
			$bannedEmail = explode( ';', $bannedEmail );
			foreach ($bannedEmail as $value) {
				if ( JString::stristr($email, $value) ) {
					$this->setError( JText::sprintf('The %s of your e-mail contains banned text.', 'email') );
					return false;
				}
			}
		}

		$configSubject = $pparams->get( 'banned_subject', '' );
		$paramsSubject = $params->get( 'banned_subject', '' );
		$bannedSubject = $configSubject . ( $paramsSubject ? ';'.$paramsSubject : '');

		if ( $bannedSubject ) {
			$bannedSubject = explode( ';', $bannedSubject );
			foreach ($bannedSubject as $value) {
				if ( $value && JString::stristr($subject, $value) ) {
					$this->setError( JText::sprintf('The %s of your e-mail contains banned text.', 'subject') );
					return false;
				}
			}
		}

		$configText = $pparams->get( 'banned_text', '' );
		$paramsText = $params->get( 'banned_text', '' );
		$bannedText = $configText . ( $paramsText ? ';'.$paramsText : '' );

		if ( $bannedText ) {
			$bannedText = explode( ';', $bannedText );
			foreach ($bannedText as $value) {
				if ( $value && JString::stristr($body, $value) ) {
					$this->setError( JText::sprintf('The %s of your e-mail contains banned text.', 'message') );
					return false;
				}
			}
		}

		$check = explode( '@', $email );
		if ( strpos( $email, ';' ) || strpos( $email, ',' ) || strpos( $email, ' ' ) || count( $check ) > 2 ) {
			$this->setError( JText::_( 'You cannot enter more than one email address', true ) );
			return false;
		}
		$sc = $params->get('show_captcha');
		if($sc == '') {
			$sc = $pparams->get('show_captcha');
		}
		if($sc) {
			require_once JPATH_COMPONENT . DS . 'includes' . DS . 'securimage' . DS . 'securimage.php';
			$img = new securimage();
			if($captcha == '' || $img->check($captcha) == false) {
				$this->setError( JText::_( 'Wrong security code', true ) );
				return false;
			}
		}
		return true;
	}
}