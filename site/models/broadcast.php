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
		include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
		
		$api_name = str_replace('plug_techjoomlaAPI_', '', $api);

		foreach($apistatuses as $apistatus){
			$userid = $apistatus['user_id'];
			foreach ($apistatus['status'] as $status )
			{	
				if((!combroadcastHelper::checkexist($status['comment'],$userid,$api)))
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

					combroadcastHelper::inJSAct($userid,$userid,$actor.$status_content,'', $api_name,$userid,$today_date->toMySQL() );
					combroadcastHelper::intempAct($userid, $status['comment'],date('Y-m-d',time()),$api );
					combroadcastHelper::updateJSstatus($userid, $status_content,$today_date->toMySQL() );
				}
			}
		}
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