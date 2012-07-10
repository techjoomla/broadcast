<?php
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class BroadcastModelconfig extends JModel
{

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
		$qry 	= "SELECT broadcast_activity_config,broadcast_rss  FROM #__broadcast_config  WHERE  user_id  = {$user->id} ";
		$this->_db->setQuery($qry);
	 	$sub_list 	= $this->_db->loadObject();		 	
	 	return $sub_list;
	}
	
	/**** Start Added & Modified By - Deepak */
	function save()
	{	
	require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$integration=$broadcast_config['integration'];
		$user = JFactory::getUser();
		$row = new stdClass;
		$data = JRequest::get('post');
		$row->user_id = $user->id;
				
		if(!$user->id)		
			return false;
			
		if($data['broadcast_activity'])
		$broadcast_activity = implode('|', $data['broadcast_activity']); 
		else
		$broadcast_activity ='';
		
		
		$i=0;


			
		foreach($data['rss_title'] as $key=>$title){
			$rss[$i]['title']=$title;
			$rss[$i]['link']=$data['rss_link'][$key];
			$i++;
		
		}

				
		$row->broadcast_activity_config = $broadcast_activity;
		$row->broadcast_rss = json_encode($rss);
		$qry = "SELECT user_id FROM #__broadcast_config WHERE  user_id = {$user->id}";
		$this->_db->setQuery($qry);
	 	$userexists = $this->_db->loadResult();		
	 	if($userexists)
	 	{
			if(!$this->_db->updateObject('#__broadcast_config', $row, 'user_id'))
			{
				echo $this->_db->stderr();die;
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
