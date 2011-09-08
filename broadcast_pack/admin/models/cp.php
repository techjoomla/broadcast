<?php 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
class broadcastModelcp extends JModel
{	
	var $_data = null;
	var $_total = null;
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();
		global $option;
		$app		= JFactory::getApplication();
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$limitstart	= $app->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	 
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}	
	
	function store($post)
	{
		$db	= JFactory::getDBO();
		$obj	= new stdClass();
		$obj->status	= $post['status'];
		$obj->userid	= $post['userid'];
		$obj->flag		= 0;
		$obj->count		= $post['count'];
		$obj->interval	= $post['interval'];
		$db->insertObject('#__broadcast_queue',$obj,'id');
		
		return true;
	}
}

