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
		$api_response=$dispatcher->trigger('renderPluginHTML',array($broadcast_config));
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
		$callback = JURI::base()."index.php?option=com_broadcast&controller=broadcast&task=get_access_token";
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$_SESSION['api_used']);
		$grt_response = $dispatcher->trigger('get_access_token',array($get,'broadcast',$callback));
		if(!$grt_response[0])	{
			return FALSE;
		}
		else{ 
			return TRUE;
		}
	}
	function removetoken($api_used){
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI',$api_used);
		$grt_response = $dispatcher->trigger('remove_token',array('broadcast'));
	}


}
