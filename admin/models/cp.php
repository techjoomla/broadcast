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
		$post['status'] = combroadcastHelper::givShortURL($post['status']);
		if( combroadcastHelper::inQueue($post['status'], $post['userid'],$post['count'],$post['interval'],'com_broadcast'))
			combroadcastHelper::intempAct($post['userid'], $post['status'], date('Y-m-d H:i:s',time()));
	}
}

