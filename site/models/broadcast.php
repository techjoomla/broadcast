<?php

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class BroadcastModelbroadcast extends JModel
{
	/*trigger plugin to get the api data required for display*/
	function getapistatus(){
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$api_response=array();
		if( isset($broadcast_config['api']) )
		{
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin('techjoomlaAPI');
		$broadcast_config['api']['client']='broadcast';
		$api_response=$dispatcher->trigger('renderPluginHTML',array($broadcast_config['api']));
		}
		return $api_response;
	}
	
	/*trigger the api for request token */
	function getRequestToken($api_used)
	{ 
		$callback = JRoute::_(JURI::base()."index.php?option=com_broadcast&controller=broadcast&task=get_access_token");
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
		$callback = JRoute::_(JURI::base()."index.php?option=com_broadcast&controller=broadcast&task=get_access_token");
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
	function checkuserconfig($userid){
		$sub_list = '';
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$integration=$broadcast_config['integration'];
		$qry 	= "SELECT broadcast_activity_config FROM #__broadcast_config WHERE user_id  = {$userid}";
		$this->_db->setQuery($qry);
	 	$sub_list 	= $this->_db->loadResult();		 	
	 	return $sub_list;
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
		$api_name = str_replace('plug_techjoomlaAPI_', '', $api_used);
		

		if(isset($statuses[0]) && !empty($statuses[0]))
			$this->storestatus($statuses[0],$api_used); 		
	}
	
	function storestatus($apistatuses,$api){

		jimport('joomla.utilities.date');
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
		
		
				$api_name = str_replace('plug_techjoomlaAPI_', '', $api);
		foreach($apistatuses as $apistatus){
			$userid = $apistatus['user_id'];
			$apistatus['status'] = array_reverse($apistatus['status']);
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



						//if Jomsocial
						if($broadcast_config['integration']==0)
						{		
							$status_content = combroadcastHelper::makelink($status_content,$api_name);	
							$today_date	= & JFactory::getDate($status['timestamp']);
							combroadcastHelper::inJSAct($userid,$userid,$actor.$status_content,'', $api_name,$userid,$today_date->toMySQL() );
							combroadcastHelper::intempAct($userid, $status['comment'],$today_date->toMySQL(),$api );
							$today =& JFactory::getDate();
							combroadcastHelper::updateJSstatus($userid, $status['comment'],$today->toMySQL() );
						}
						//if Jomwall
						if($broadcast_config['integration']==1)
						{

							$today_date	= & JFactory::getDate($status['timestamp']);
							$today =& JFactory::getDate();
							combroadcastHelper::inJomwallact($userid, $status['comment'],$status_content,$today,$status['timestamp'],$api);
							combroadcastHelper::intempAct($userid, $status['comment'],$today_date->toMySQL(),$api);
						}
            //if Superactivity  
						if($broadcast_config['integration']==2)
						{

							$today_date	= & JFactory::getDate($status['timestamp']);
							$today =& JFactory::getDate();
							combroadcastHelper::inSuperaact($userid, $status['comment'],$status_content,$today,$status['timestamp'],$api);
							combroadcastHelper::intempAct($userid, $status['comment'],$today_date->toMySQL(),$api);
						}
				}
			}
		}
	}
	

	
	function getqueue(){
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$integration=$broadcast_config['integration'];
		$query 		= "SELECT * FROM #__broadcast_queue";
		$this->_db->setQuery($query);
	 	return $this->_db->loadObjectList();
	}
	function setStatus($api_used,$userid,$status){
	$attachment='';
		$dispatcher = &JDispatcher::getInstance();
		include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
		JPluginHelper::importPlugin('techjoomlaAPI',$api_used);
		
		$comment=$status;
		$link=combroadcastHelper::makelink($comment,'');
			$link=trim($link);
			$comment=trim($comment);
			if($api_used!='rss')
			{
				if($link!=$comment)
				{				
					$type='link';
					 $attachment=combroadcastHelper::seperateurl($comment);
						$comment=str_replace($attachment,'',$comment);

				}
			}

		return $grt_response = $dispatcher->trigger($api_used.'setstatus',array($userid,$status,$comment,$attachment));
	}
	function purgequeue(){
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$integration=$broadcast_config['integration'];
		
		$query = "SELECT id 
					FROM #__broadcast_queue 
					WHERE count=0  ORDER BY date desc LIMIT ".$broadcast_config['purgelimit'];
		$this->_db->setQuery($query);
	 	$queue = $this->_db->loadResultArray();
	 	if(!empty($queue))
	 	{
			$query = "DELETE 
						FROM #__broadcast_queue 
						WHERE id IN(".implode(',',$queue).") AND count=0 AND flag=1";
		
			$this->_db->setQuery($query); 
			if (!$this->_db->query()) {
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}
	}

}
