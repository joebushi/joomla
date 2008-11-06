<?php

jimport('joomla.error.profiler');

/**
 * Individual task entry
 * @since 1.6
 */
class JTask extends JTable {
	private $_quanta = Array();
	private $_lasttick = 0;
	/** Duration between yields; overwritten each yield; Requires two yields to calculated */
	private $_duration = 0;	
	private $_instance = null;

	protected $taskid = 0;
	protected $tasksetid = 0;
	protected $data = '';
	protected $offset = 0;
	protected $total = 0;
	/** The parent task set for this task */
	private $_parent;

	function __construct(& $db=null, &$parent=null, $taskid = 0, $tasksetid = 0, $data = '') {
		if($db != null) {
			$this->taskid = $taskid;
			$this->tasksetid = $tasksetid;
			$this->data = $data;
			if($parent != null) {
				$this->_parent =& $parent;
			} else if($tasksetid) {
				$this->_parent = new JTaskSet($db);
				$this->_parent->load($tasksetid);
			}
			parent::__construct( '#__tasks', 'taskid', $db );
		}
	}
	
	public function setDBO(&$dbo) {
		$this->_db =& $dbo;
	}
	
	public function setParent(&$parent) {
		$this->_parent = $parent;
	}
	
	public function setInstance(&$instance, $restore=false) {
		$this->_instance =& $instance;
		$this->_instance->setTask($this);
		if($restore) {
			$this->_instance->restoreTask($this->data);
		}
	}
	
	function load($pid=null) {
		$res = parent::load($pid);
		if($res) $this->data = unserialize($this->data); // pull the data back out
		return $res;
	}
	
	function store($updateNulls=false) {
		$this->data = serialize($this->data);
		$res = parent::store($updateNulls);
		$this->data = unserialize($this->data);
		return $res;
	}
	
	public function yield() {
		$now = JProfiler::getmicrotime();
		if($this->_lasttick) {
			$this->_quanta[] = $now - $this->_lasttick;
			$this->duration = ceil(array_sum($this->_quanta) / count($this->_quanta));
		}
		// check if we're over the run time now
		// OR if now plus our average duration will put us over the max time		
		if (($now - $this->_parent->_startTime) >= $this->_parent->get('run_time',15) 
			|| (($now - $this->_parent->_startTime) + $this->_duration) > $this->_parent->get('max_time', 30)) {
				$this->reload();
		}
	}
	
	// TODO: redo this function
	public function reload() {
		if($this->_instance) $this->data = $this->_instance->suspendTask();
		$this->store(); // save ourselves before we reload
		$link = $this->_parent->executionpage .'&taskset='.$this->tasksetid;
		echo '<a href="'.$link.'">'.JText::_('Next').'</a>';
		// mark:javascript autoprogress
		echo "<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"" . $link . "\";',1000);</script>\n";
		echo '</div>';
		$mainframe =& JFactory::getApplication();
		$mainframe->close();
	}


	// TODO: legacy functions, validate relevance
	function execute($callback, &$context=null) {
		global $mainframe;
		// $run_time, $startTime;
		if($context) $return = $context->$callback($this); else $return = $callback($this);
		
		if($return) {
			if(!$this->total || $this->offset >= $this->total) { $this->delete(); return false; }
			$this->store();
			$checkTime = JProfiler :: getmicrotime();
			if (($checkTime - $this->_parent->_startTime) >= $this->_parent->_run_time) {
				$link = $this->_parent->executionpage .'&taskset='.$this->tasksetid;
				echo '<a href="'.$link.'">'.JText::_('Next').'</a>';
				// mark:javascript autoprogress
				echo "<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"" . $link . "\";',1000);</script>\n";
				echo '</div>';
				$mainframe->close();
				return true;
			}
	
			//$this->delete() or die($this->_db->getErrorMsg());
			return true;
		} else {
			$this->delete();
			return false;
		}
	}
}