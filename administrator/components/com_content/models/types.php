<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.database.query');

class ContentModelTypes extends JModel
{
    protected $_data = null;

    protected $_pagination = null;

    protected $_total = null;

    protected function _buildQuery()
    {
        $query = new JQuery;

        $query->select('t.*');
        $query->select('CONCAT('.$this->_db->Quote($this->_db->replacePrefix('#__content_type_')).', t.table_name) AS tablename');
        $query->from('#__content_types AS t');

        if ($search	= $this->getState('filter.search')) {
            $search = $this->_db->Quote('%'.$this->_db->getEscaped($search, true).'%', false);
            $query->where('LOWER(t.name) LIKE '.$search);
        }

        if ($order = $this->getState('filter.order')) {
            $orderDir = $this->getState('filter.orderDir');
            $query->order($this->_db->getEscaped($order.' '.$orderDir));
        }

        return $query;
    }

    public function getData()
    {
        if (empty($this->_data)) {
            $query = $this->_buildQuery();

            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

    public function getTotal()
    {
        if (empty($this->_total))
        {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }

        return $this->_total;
    }

    public function getPagination()
    {
        jimport('joomla.html.pagination');
        return new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
    }
}