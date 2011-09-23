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
		$this->appKey	=& $this->params->get('appKey');
		$this->appSecret	=& $this->params->get('appSecret');
		$this->callbackUrl='';
		$this->errorlogfile='facebook_error_log.php';
		$this->user = JFactory::getUser();
		$this->db=JFactory::getDBO();
		$this->facebook = new Facebook(array(
 	 'appId'  => $this->appKey,
   'secret' => $this->appSecret,
   'callbackUrl'=> $this->callbackUrl,
   'cookie' => true, // enable optional cookie support
		));
		
		//Create Global Error Log Object//
		$this->ERROR_LOG=new BroadcastHelperLogs();	
		
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
		if($this->appKey=='' || $this->appSecret=='' || !in_array($this->_name,$config))
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
	 	$query 	= "SELECT token FROM #__techjoomlaAPI_users WHERE user_id = {$this->user->id}  AND api='{$this->_name}'";
		$this->db->setQuery($query);
		$result	= $this->db->loadResult();		
		$uaccess=json_decode($result);
		if ($uaccess->facebook_uid && $uaccess->facebook_secret)
			return 1;
		else
			return 0;
	}
	
	function get_request_token($callback) 
	{
		
		$this->callbackUrl=$callback;
		$params = array(
							'redirect_uri' => $callback,
							'scope' =>'email,read_stream,user_status,publish_stream,offline_access', 
							);
			
		try	{
			$loginUrl = $this->facebook->getLoginUrl($params);
			$user = $this->facebook->getUser();
		} 
		catch (FacebookApiException $e) 
		{
			$this->raiseException($e->getMessage());
			return false;
		}	
			$response=header('Location:'.$loginUrl);  
		
			return true; 
	
	}
	
	function get_access_token($get,$client,$callback) 
	{
		
		try{	
			$uid = $this->facebook->getUser();			
			$facebook_secret = $this->facebook->getAccessToken();
		}
		catch (FacebookApiException $e) 
		{
			$this->raiseException($e->getMessage());
			return false;
    }	
    
		$data = array('facebook_uid'=>$uid,'facebook_secret'=>$facebook_secret);
		$this->store($client,$data);		
		return true;
		
	}
	
	function store($client,$data) #TODO insert client also in db 
	{
		
		$qry = "SELECT id FROM #__techjoomlaAPI_users WHERE user_id ={$this->user->id} AND client='{$client}' AND api='{$this->_name}' ";
		$this->db->setQuery($qry);
		$id	=$exists = $this->db->loadResult();
		$row = new stdClass;
		$row->id=NULL;
		$row->user_id = $this->user->id;
		$row->api 		= $this->_name;
		$row->client=$client;
		$row->token=json_encode($data);
		
		if($exists)
		 {
		 		$row->id=$id;
	 			$this->db->updateObject('#__techjoomlaAPI_users', $row, 'id');
		 }
		 else
		 {
		 			
				$status=$this->db->insertObject('#__techjoomlaAPI_users', $row);
		 }
		
	}
	
	function getToken($user=''){
		$user=$this->user->id;
		$where = '';
		if($user)
			$where = ' AND user_id='.$user;
			
		$query = "SELECT user_id,token
		FROM #__techjoomlaAPI_users 
		WHERE token<>'' AND api='{$this->_name}' ".$where ;
		$this->db->setQuery($query);
		return $this->db->loadObjectlist();
	}
	function remove_token($client)
	{ 
		if($client!='')
		$where="AND client='{$client}' AND api='{$this->_name}'";
		
		#TODO add condition for client also
		$qry 	= "UPDATE #__techjoomlaAPI_users SET token='' WHERE user_id = {$this->user->id} ".$where;
		$this->db->setQuery($qry);	
		$this->db->query();
	}
	
	        
	function get_contacts() 
	{
		try{	
			$contacts=array();
			$friends= $this->facebook->api('/me/friends');
		}
		catch (FacebookApiException $e) 
		{
			$this->raiseException($e->getMessage());
			return false;
    }	
		if(!$friends)
		{
					$this->raiseException(JText::_( 'EXCEPTION_CONTACT_NOT_FOUND' ));			
					return $contacts;
		}
		
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
		
		return $contacts;
		
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
		$oauth_keys =array();
	 	$oauth_keys = $this->getToken();
	 
	 	$i = 0;
	 	$today=date('Y-m-d');
		$facebook_profile_limit=10;
		$returndata = array();
		if(!$oauth_keys)
		return array();
	 	foreach($oauth_keys as $oauth_key){	
	 	
			$token =json_decode($oauth_key->token);	
			try{		
				$json_facebook = $this->facebook->api($token->facebook_uid.'/statuses',array('access_token'=>$token->facebook_secret,'since'=>$today,'limit'=>$facebook_profile_limit));
			}
			catch (FacebookApiException $e) 
			{
				$this->raiseException($e->getMessage());
				return false;
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

	function plug_techjoomlaAPI_facebooksetstatus($userid='',$content='')
	{
		if(!$userid)
		{
			$this->raiseException($this->raiseException(JText::_( 'SESSION_TIMEOUT' )));
     	return array();
		}
		$oauth_key = $this->getToken($userid);
		$token =json_decode($oauth_key->token);	
		$post=array();
		if(!$content)
		return array();
		
		try{
		$post = $this->facebook->api($token->facebook_uid.'/feed', 'POST', array('access_token'=>$token->facebook_secret,'message' => $content));
		
		} 
		catch (FacebookApiException $e) 
		{
  
     $this->raiseException($e->getMessage());
     return array();
    }
		
		return $post;
	
	}
	
	function raiseException($exception)
	{
		$params=array(
		'name'=>$this->_name,
		'group'=>$this->_type,	
		);	
		$this->ERROR_LOG->simpleLog($exception,$path='',$this->errorlogfile,'plugin',$params);
		return;
	}
	
	

}//end class
