<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	Templates
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * @package		Joomla.Administrator
 * @subpackage	Templates
 */
class TemplatesModelCssedit extends JModel
{
	/**
	 * Template id
	 *
	 * @var int
	 */
	protected $_id = null;

	/**
	 * Template data
	 *
	 * @var array
	 */
	protected $_data = null;

	/**
	 * client object
	 *
	 * @var object
	 */
	protected $_client = null;

	/**
	 * filename
	 *
	 * @var string
	 */
	protected $_filename = null;

	/**
	 * Template name
	 *
	 * @var string
	 */
	protected $_template = null;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_id		= JRequest::getVar('id', '', 'method', 'cmd');
		$this->_template = JRequest::getVar('template');
		$this->_filename	= JRequest::getVar('filename', '', 'method', 'cmd');
		$this->_client	= &JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
	}

	/**
	 * Method to load Template data
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	protected function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			jimport('joomla.filesystem.file');

			if (JFile::getExt($this->_filename) !== 'css') {
				return JError::raiseWarning(500, JText::_('Wrong file type given, only CSS files can be edited.'));
			}

			$content = JFile::read($this->_client->path.DS.'templates'.DS.$this->_template.DS.'css'.DS.$this->_filename);

			if ($content === false)
			{
				$this->setError(JText::sprintf('Operation Failed Could not open', $this->_client->path.DS.'templates'.DS.$this->_template.DS.'css'.DS.$this->_filename));
				return false;
			}

			$content = htmlspecialchars($content, ENT_COMPAT, 'UTF-8');

			$this->_data = $content;
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the Template data
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	protected function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$template = new stdClass();
			$this->_data = $template;
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to get a Template
	 *
	 * @since 1.6
	 */
	public function &getData()
	{
		// Load the data
		if (!$this->_loadData())
			$this->_initData();

		return $this->_data;
	}

	/**
	 * Method to get the client object
	 *
	 * @since 1.6
	 */
	public function &getClient()
	{
		return $this->_client;
	}

	public function &getTemplate()
	{
		return $this->_template;
	}

	public function &getId()
	{
		return $this->_id;
	}

	public function &getFilename()
	{
		return $this->_filename;
	}

	/**
	 * Method to store the Template
	 *
	 * @return	boolean	True on success
	 * @since	1.6
	 */
	public function store($filecontent)
	{
		// Set FTP credentials, if given
		jimport('joomla.client.helper');
		JClientHelper::setCredentialsFromRequest('ftp');
		$ftp = JClientHelper::getCredentials('ftp');

		$file = $this->_client->path.DS.'templates'.DS.$this->_template.DS.'css'.DS.$this->_filename;

		// Try to make the css file writeable
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0755')) {
			$this->setError(JText::_('Could not make the css file writable'));
			return false;
		}
		jimport('joomla.filesystem.file');
		$return = JFile::write($file, $filecontent);

		// Try to make the css file unwriteable
		if (!$ftp['enabled'] && JPath::isOwner($file) && !JPath::setPermissions($file, '0555')) {
			$this->setError(JText::_('Could not make the css file unwritable'));
			return false;
		}

		if (!$return)
		{
			$this->setError(JText::_('Operation Failed').': '.JText::sprintf('Failed to open file for writing.', $file));
			return false;
		}

		return true;
	}
}