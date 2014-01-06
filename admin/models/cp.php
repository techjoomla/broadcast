<?php 
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
class broadcastModelcp extends JModelLegacy
{	
	
	 
	function getqueue()
	{
		$query 		= "SELECT * FROM #__broadcast_queue order by id,status desc";
		$this->_db->setQuery($query);
	 	return $this->_db->loadObjectList();
	}	
	
	function store($post)
	{
		
		require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
		if(isset($post['api_status']) && !empty($post['api_status']))
			$apis = $post['api_status'];
		else
			$apis = '';
		$combroadcastHelper=new combroadcastHelper();
		return $combroadcastHelper->addtoQueue($post['userid'], $post['status'], date('Y-m-d H:i:s',time()),$post['count'],$post['interval'],$apis,'com_broadcast',1);
	}
}

