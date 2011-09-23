<?php

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class BroadcastModelbroadcast extends JModel
{
	/*trigger plugin to get the api data required for display*/
	function getapistatus(){
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI');
		$api_response=$dispatcher->trigger('renderPluginHTML',array($broadcast_config['api']));
		return $api_response;
	}
	
	/*trigger the api for request token */
	function getRequestToken($api_used)
	{ 
		$callback = JURI::base()."index.php?option=com_broadcast&controller=broadcast&task=get_access_token";
		$grt_response = array();
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$api_used);
		$grt_response=$dispatcher->trigger('get_request_token',array($callback));
		
		if(!$grt_response[0])	{
			return FALSE;
		}
	}
	
	/*trigger the api for access token */
	function getAccessToken($get)
	{
		$session =& JFactory::getSession();	
		$callback = JURI::base()."index.php?option=com_broadcast&controller=broadcast&task=get_access_token";
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$session->get('api_used',''));
		$grt_response = $dispatcher->trigger('get_access_token',array($get,'broadcast',$callback));
		if(!$grt_response[0])	{
			return FALSE;
		}
		else{ 
			return TRUE;
		}
	}
	/*trigger to destroy the token of a user*/
	function removeToken($api_used){
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$api_used);
		$grt_response = $dispatcher->trigger('remove_token',array('broadcast'));
	}
	
	function getStatus($api_used){
		$statuses = array();
		
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$api_used);
		$statuses = $dispatcher->trigger($api_used.'getstatus'); 
		$this->storestatus($statuses[0],$api_used); 		
	}
	
	function storestatus($apistatuses,$api){
		jimport('joomla.utilities.date');
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$api_name = str_replace('plug_techjoomlaAPI_', '', $api);

		foreach($apistatuses as $apistatus){
			$userid = $apistatus['user_id'];
			foreach ($apistatus['status'] as $status )
			{	
				if((!$this->checkexist($status['comment'],$userid,$api)))
				{
					$obj = new StdClass();
					if($broadcast_config['show_name'])
						$actor='{actor} ';
					else
						$actor='';
					if($broadcast_config['status_skip'])
					{
						$search=explode(',', trim($broadcast_config['status_skip']) );
						$status_content=str_replace($search, '', $status['comment']);
					}
					else
						$status_content= $status['comment'];
					
					if($broadcast_config['status_via'])
						$status_content = $status_content.' (via '.ucfirst($api_name).')';

					$status_content = combroadcastHelper::makelink($status_content);
					
					$obj->actor 	= $userid;
					$obj->target 	= $userid;
					$obj->title		= $actor.$status_content;			
					$obj->content	= '';
					$obj->app		= $api_name;
					$obj->cid		= $userid;
					$obj->params	= '';
					$today_date	= & JFactory::getDate($status['timestamp']);
					$obj->created	= $today_date->toMySQL();	#TODO convert into correct date time 
					$obj->access	= 0;
					$obj->points	= 1;
					$obj->archived	= 0; 
					$this->_db->insertObject('#__community_activities', $obj);
					
					$obj=null;
					$obj->uid 		= $userid;
					$obj->status 	= $status['comment'];
					$obj->created_date	= date('Y-m-d',time());	
					$obj->type		= $api; 
					$this->_db->insertObject('#__broadcast_tmp_activities', $obj);

					$query	= "UPDATE `#__community_users` SET `status` ='{$this->_db->getEscaped($status_content)}', 
								posted_on='{$today_date->toMySQL()}', points=points +1 WHERE userid='{$userid}'";
					$this->_db->setQuery( $query );
					$addHit =$this->_db->query();	
				}
			}
		}
	}
	function checkexist($status,$uid,$api)
	{
		$status		= explode('(via',$status);		
		$newstatus	= trim($status[0]);
		$newstatus	=$this->_db->getEscaped($newstatus);
		$query = "SELECT status FROM #__broadcast_tmp_activities WHERE uid = {$uid} and status = '{$newstatus}' ";
		$this->_db->setQuery($query);
		if($this->_db->loadResult())			
			return 1;					
		else
			return 0;
	}
	function getqueue(){
		$query 		= "SELECT * FROM #__broadcast_queue";
		$this->_db->setQuery($query);
	 	return $this->_db->loadObjectList();
	}
	function setStatus($api_used,$userid,$status){
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$api_used);
		return $grt_response = $dispatcher->trigger($api_used.'setstatus',array($userid,$status));
	}


}
