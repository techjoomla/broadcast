<?php 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
class broadcastModelcp extends JModel
{	
	
	 
	function getqueue()
	{
		$query 		= "SELECT * FROM #__broadcast_queue";
		$this->_db->setQuery($query);
	 	return $this->_db->loadObjectList();
	}	
	
	function store($post)
	{
		include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
		return combroadcastHelper::inQueue($post['status'], $post['userid'],$post['count'],$post['interval'],'com_broadcast');
	}
}

