<?php
/*
	* @package Facebook plugin for TechjoomlaAPI
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
// include the Facebook class
if(JVERSION >='1.6.0')
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_facebook'.DS.'plug_techjoomlaAPI_facebook'.DS.'lib'.DS.'facebook.php');
else
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_facebook'.DS.'lib'.DS.'facebook.php');
//Helper class to write log file//
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');
	
class plgTechjoomlaAPIplug_techjoomlaAPI_facebook extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_facebook(& $subject, $config)
	{
		
		parent::__construct($subject, $config);
		$appKey	=& $this->params->get('appKey');
		$appSecret	=& $this->params->get('appSecret');
		$this->API_CONFIG=array(
		'appKey'       => $appKey,
		'appSecret'    => $appSecret,
		'callbackUrl'  => NULL 
		);
		
		$this->facebook = new Facebook(array(
 	 'appId'  => $appKey,
   'secret' => $appSecret,
   'cookie' => true, // enable optional cookie support
		));
			//Create Global Error Log Object//
		$error_log=new BroadcastHelperLogs();		
		$this->ERROR_LOG	=$error_log;
	}
	
	/*
		 * Get the plugin output as a separate html form 
     *
     * @return  string  The html form for this plugin
     * NOTE: all hidden inputs returned are very important
	*/
 function renderPluginHTML($config=array())
	{
    $plug=array(); 
   	$plug['name']="Facebook";
  	//check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']==''|| !in_array($this->_name,$config) )
		{
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="facebook.png"; 
		//dipti
		$plug['apistatus'] = $this->status();
		//eoc            
		return $plug; 
	}
	
	function status(){
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$query = "SELECT facbook_uid,facebook_secret FROM #__broadcast_users WHERE user_id = {$user->id}";
		$db->setQuery($query);
		$uaccess = $db->loadObject();
		if ($uaccess->facbook_uid && $uaccess->facebook_secret)
			return 1;
		else
			return 0;
	}
	function get_request_token($callback) 
	{
		
		$this->API_CONFIG['callbackUrl']=$callback;
		$params = array(
							'redirect_uri' => $callback,
							'scope' =>'email,read_stream,user_status,publish_stream,offline_access', //,
							);
		$loginUrl = $this->facebook->getLoginUrl($params);
		
		$user = $this->facebook->getUser();
		$response=header('Location:'.$loginUrl);   
		return true;
		

	
	}
	
	function get_access_token($get,$client,$callback) 
	{
		
			try
			{
				$uid = $this->facebook->getUser();
				$facebook_secret = $this->facebook->getAccessToken();

				$data['facbook_uid'] = $uid;
				$data['facebook_secret'] = $facebook_secret;
				$this->store($client,$data);
								
			}
			catch (FacebookApiException $e)
			{	
				
				return false;
			}
			
			return true;
		
		
		
		
	}
	
	function store($client,$data) #TODO insert client also in db 
	{
		$db	 	=  & JFactory::getDBO();
		$user = JFactory::getUser();
		$qry = "SELECT user_id FROM #__broadcast_users WHERE user_id = {$user->id}";
		$db->setQuery($qry);
		$exists = $db->loadResult();
		$row = new stdClass;
		$row->user_id = $user->id;
		foreach ($data as $k=>$v)
		 {
			$row->$k = $v;
	   }		

		if ($exists)
		 {
				$db->updateObject('#__broadcast_users', $row, 'user_id');
		 }
		 else
		 {
			$db->insertObject('#__broadcast_users', $row);
		 }
	}
	
	function getToken($user=''){
		$db = JFactory::getDBO();
		$where = '';
		if($user)
			$where = ' AND user_id='.$user;
		$query = "SELECT user_id,facbook_uid,facebook_secret
		FROM #__broadcast_users 
		WHERE facbook_uid<>'' and facebook_secret<>'' ".$where ;
		$db->setQuery($query);
		return $db->loadObjectlist();
	}
	function remove_token($client)
	{ 
		$db	 = & JFactory::getDBO();
		$user 	= JFactory::getUser();
		#TODO add condition for client also
		$qry 	= "UPDATE #__broadcast_users SET facbook_uid='',facebook_secret='' WHERE user_id = {$user->id}";
		$db->setQuery($qry);	
		$db->query();
	}
	
	        
	function get_contacts() 
	{
		$friends= $this->facebook->api('/me/friends');
		if(!$friends)
		{
		
			return false;//Actual Message should be passed back to controller to display. No Hard coded Messages in Controller
		
		}
		$contacts=array();
		$connections =$friends;	
		
		$cnt=0;
		$emails=array(array());
		foreach ($connections['data'] as $contact)
			{
				
				$emails[$cnt]['id']= $contact['id'];	
				$emails[$cnt]['name']= $contact['name'];
				$emails[$cnt]['picture-url']= 'https://graph.facebook.com/'.$emails[$cnt]['id'].'/picture';																						
				$cnt++;
				
			}
			
			$contacts=$this->renderContacts($emails);
		if($contacts)
		return $contacts;
		else
		return array();
	}
	
	function renderContacts($emails)
	{
			
			$count=0;
			foreach($emails as $connection)
			{
						
				$r_connections[$count]->id  =$connection['id'];
				$first_name ='';
				$last_name ='';
				if(array_key_exists('first-name',$connection))
					$first_name =$connection['first-name'];
				if(array_key_exists('last-name',$connection))
					$last_name  =$connection['last-name'];
				if(array_key_exists('first-name',$connection) or array_key_exists('last-name',$connection))											
				$r_connections[$count]->name=$first_name.' '.$last_name;
				else if(array_key_exists('name',$connection))
				$r_connections[$count]->name=$connection['name'];
				if($connection['picture-url']	)
				{
					$r_connections[$count]->picture_url=$connection['picture-url'];
				}
				else
				{
					$r_connections[$count]->picture_url='';
				}
				$count++;
			}
		return $r_connections;
	}
	
	function send_message($post)
	{
	
  }//end send message
  
  function plug_techjoomlaAPI_facebookgetstatus()
	{ 
	 	$oauth_keys = $this->getToken();
	 	$i = 0;
	 	$today=date('Y-m-d');
		$facebook_profile_limit=10;
		$returndata = array();
	 	foreach($oauth_keys as $oauth_key){	
	 	
			$access_token =$oauth_key->facebook_secret;			
			try {
				$json_facebook = $this->facebook->api($oauth_key->facbook_uid.'/statuses',array('access_token'=>$oauth_key->facebook_secret,'since'=>$today,'limit'=>$facebook_profile_limit));
				
			}
			catch(FacebookApiException $o ){
				print_r($o);
			}
			$returndata[$i]['user_id'] = $oauth_key->user_id;
			$returndata[$i]['status'] = $this->renderstatus($json_facebook['data']);
			$i++;
		}
		
			return $returndata;
		
	}
			

	function renderstatus($totalresponse)
	{	
		$status = array();
	 	$j=0;
		for($i=0; $i <= count($totalresponse); $i++ )
		{			
			if(isset($totalresponse[$i]['message'])){
				$status[$j]['comment'] =  $totalresponse[$i]['message'];
				$status[$j]['timestamp'] = strtotime($totalresponse[$i]['updated_time']);
				$j++;
			}
		  }
		return $status;
	}

	function plug_techjoomlaAPI_facebooksetstatus($userid,$content='')
	{
		$oauth_key = $this->getToken($userid);
		$post=array();
		if(!$content)
		return array();
		$access_token =$oauth_key[0]->facebook_secret;	
		try {
		$post = $this->facebook->api($oauth_key[0]->facbook_uid.'/feed', 'POST', array('access_token'=>$oauth_key[0]->facebook_secret,'message' => $content));
		}
		catch(FacebookApiException $o ){
    print_r($o);
	}    print_r($post); 
	return $post;
	
	}
	
	

}//end class
