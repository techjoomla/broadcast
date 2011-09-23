<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class BroadcastModelconfig extends JModel
{
	function store($data)
	{
		$user = JFactory::getUser();
		$row = new stdClass;
		$qry = "SELECT `user_id` FROM #__broadcast_users WHERE `user_id` = {$user->id}";
		$this->_db->setQuery($qry);
		$exists = $this->_db->loadResult();
		$row->user_id = $user->id;
		
		foreach ($data as $k=>$v)
			$row->$k = $v;	
				
		if ($exists)
			$this->_db->updateObject('#__broadcast_users', $row, 'user_id');
		else 
			 $this->_db->insertObject('#__broadcast_users', $row);
	}
	
	function remove()
	{
		$user 	= JFactory::getUser();
		$qry 	= "UPDATE #__broadcast_users SET facbook_uid='',facebook_secret='' WHERE user_id = {$user->id}";
		$this->_db->setQuery($qry);	
		$addHit =$this->_db->query();
	}
	
	function getlist()
	{
		$qry = "SELECT distinct(app) FROM #__community_activities WHERE app<>'' order by app";
		$this->_db->setQuery($qry);
		$list = $this->_db->loadObjectList();
		return $list;	
	}
	
	function getsubscribedlist()
	{
		$user 	= JFactory::getUser();
		$qry 	= "SELECT broadcast_activity_config,broadcast_rss_url  FROM #__broadcast_config  WHERE  user_id  = {$user->id}";
		$this->_db->setQuery($qry);
	 	$sub_list 	= $this->_db->loadObject();		 	
	 	return $sub_list;
	}
	
	
	function getrsslinks()
	{
			$user 	= JFactory::getUser();
			$qry 	= "SELECT	broadcast_rss_url  FROM #__broadcast_config    WHERE  user_id  = {$user->id}";
			$this->_db->setQuery($qry);
		 	$rss_list 	= $this->_db->loadObjectList();
		 	return $rss_list;
	}

	/**** Start Added & Modified By - Deepak */
	function save()
	{	
		$user = JFactory::getUser();
		$row = new stdClass;
		$data = JRequest::get('post');
		$row->user_id = $user->id;
				
		if(!$user->id)		
			return false;
			
		$broadcast_activity = implode('|', $data['broadcast_activity']); 
		$rss_link 			= implode('|', $data['rss_link']);
				
		$row->broadcast_activity_config = $broadcast_activity;
		$row->broadcast_rss_url = $rss_link;		
		$qry = "SELECT user_id FROM #__broadcast_config WHERE  user_id = {$user->id}";
		$this->_db->setQuery($qry);
	 	$userexists = $this->_db->loadResult();		
	 	if($userexists)
	 	{
			if(!$this->_db->updateObject('#__broadcast_config', $row, 'user_id'))
			{
				echo $this->_db->stderr();
				return false;
			}
		} 
		else
		{
			if(!$this->_db->insertObject('#__broadcast_config', $row, 'user_id'))
			{
				echo $this->_db->stderr();
				return false;
			}		
		}
		return true;
	}	
	/**** End Added & Modified By - Deepak */

}
