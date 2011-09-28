<?php
/*
	* @package LinkedIn plugin for Invitex
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

// include the LinkedIn class
if(JVERSION >='1.6.0')
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_linkedin'.DS.'plug_techjoomlaAPI_linkedin'.DS.'lib'.DS.'linkedin.php');
else
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_linkedin'.DS.'lib'.DS.'linkedin.php');
	
class plgTechjoomlaAPIplug_techjoomlaAPI_linkedin extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_linkedin(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$appKey	=& $this->params->get('appKey');
		$appSecret	=& $this->params->get('appSecret');
		$this->callbackUrl='';
		$this->errorlogfile='linkedin_error_log.php';
		$this->user =& JFactory::getUser();
		
		$this->db=JFactory::getDBO();
		$this->API_CONFIG=array(
		'appKey'       => $appKey,
		'appSecret'    => $appSecret,
		'callbackUrl'  => NULL 
		);
		$this->linkedin = new LinkedInAPI($this->API_CONFIG);
		}
	
	/*
		 * Get the plugin output as a separate html form 
     *
     * @return  string  The html form for this plugin
     * NOTE: all hidden inputs returned are very important
	*/
  function renderPluginHTML($config) 
	{	
		$plug=array(); 
   	$plug['name']="Linkedin";
  	//check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']=='' || !in_array($this->_name,$config)) #TODO add condition to check config
		{	
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="linkedin.png";   
		$plug['apistatus'] = $this->status();
		return $plug;   
	}
	
	function status(){
	 	$query 	= "SELECT token FROM #__techjoomlaAPI_users WHERE user_id = {$this->user->id}  AND api='{$this->_name}'";
		$this->db->setQuery($query);
		$result	= $this->db->loadResult();		
		if ($result)
			return 1;
		else
			return 0;
	}
	
	
	function get_request_token($callback) 
	{
		$session = JFactory::getSession();
		$this->linkedin->callbackUrl=$this->API_CONFIG['callbackUrl']= $callback.'&'.LinkedInAPI::_GET_RESPONSE.'=1'; 
		try{
		$this->linkedin = new LinkedInAPI($this->API_CONFIG);
		}
		catch(LinkedInException $e)
		{ 
			$this->raiseException($e->getMessage());
			return false;
		}
		
		$_GET[LinkedInAPI::_GET_RESPONSE] = (isset($_GET[LinkedInAPI::_GET_RESPONSE])) ? $_GET[LinkedInAPI::_GET_RESPONSE] : ''; 
		if(!$_GET[LinkedInAPI::_GET_RESPONSE])
		{	
			try{		
			$response = $this->linkedin->retrieveTokenRequest();
			}	
			catch(LinkedInException $e)
				{ 
					$this->raiseException($e->getMessage());
					return false;
				}
				
			$return=$this->raiseLog($response,"From Linkedin To site[Get Access Token]",$this->user->id,0);
			
			if($response['success'] === TRUE)
			{
				$cart['oauth'][][]=array();
				$session->set("['oauth']['linkedin']['request']",$response['linkedin']);
				$request_token=$session->get("['oauth']['linkedin']['request']");
				
				try{
				header('Location:'.LinkedInAPI::_URL_AUTH.$request_token['oauth_token']);
				}
				catch(LinkedInException $e)
				{ 
					$this->raiseException($e->getMessage());
					return false;
				}
				return true;
			}
			else
			{
				
				return false;
			}
			return $return;
				
		}//end if
		
	}
	
	function get_access_token($get,$client,$callback) 
	{
	
		$session = JFactory::getSession();
		$this->API_CONFIG['callbackUrl']=NULL;
		$this->linkedin = new LinkedInAPI($this->API_CONFIG);
		
		$get[LINKEDINAPI::_GET_RESPONSE] = (isset($get[LINKEDINAPI::_GET_RESPONSE])) ? $get[LINKEDINAPI::_GET_RESPONSE] : ''; 
		if($get[LINKEDINAPI::_GET_RESPONSE])
		{
				try{
				$request_token=$session->get("['oauth']['linkedin']['request']");
				$response = $this->linkedin->retrieveTokenAccess($get['oauth_token'], $request_token['oauth_token_secret'], $get['oauth_verifier']);
				}
				catch(LinkedInException $e)
				{ 
					$this->raiseException($e->getMessage());
					return false;
				}
				
				$return=$this->raiseLog($response,"From Linkedin To site[Get Access Token]",$this->user->id,0);
				if($response['success'] === TRUE)
				{
					
				  $session->set("['oauth']['linkedin']['access']",$response['linkedin']);
				  $session->set("['oauth']['linkedin']['authorized']",true);
					  
					$response_data['linkedin_oauth']		= json_encode($response['linkedin']);		
					$response_data['linkedin_secret']	= $get['oauth_verifier'];
					$this->store($client,$response_data);
					
				
				}
				return $return;
				
			}
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
	
	function plug_techjoomlaAPI_linkedinget_contacts() 
	{
		$session = JFactory::getSession();
		$this->API_CONFIG['callbackUrl']= JURI::base().'index.php?option=com_invitex&view=invites&layout=apis';
		
		if($session->get("['oauth']['linkedin']['authorized']",'') === TRUE)
    {
			// user is already connected
			try{
				$this->linkedin = new LinkedInAPI($this->API_CONFIG);
				$this->linkedin->setTokenAccess($session->get("['oauth']['linkedin']['access']",''));			
				$response = $this->linkedin->connections('~/connections:(id,first-name,last-name,picture-url)');			
			}
			catch(LinkedInException $e)
			{ 
				$this->raiseException($e->getMessage());
				return false;
			}
				$return=$this->raiseLog($response,"From Linkedin to site[get contacts]",$this->user->id,0);
				if(!$return)
				return $return;
			
    }
  
		return $contacts;
	}
	
	function renderContacts($connections)
	{
		$mainframe=JFactory::getApplication();		
		$conns = (array) $connections;
		if(isset($conns['person']))
		{
				$conns = $conns['person'];
		
				$count=0;
				$r_connections=array();
				if (array_key_exists("0",$conns))
				{
					foreach($conns as $connection)
					{
						$connection  = (array) $connection;
						$r_connections[$count]->id  =$connection['id'];
						$r_connections[$count]->name =$connection['first-name'].' '.$connection['last-name'];
						if(array_key_exists('picture-url',$connection))
						{
							$r_connections[$count]->picture_url=$connection['picture-url'];
						}
						else
						{
							$r_connections[$count]->picture_url='';
						}
						$count++;
					}
				}
				else//only 1 connection
				{	
					$connection  = (array) $conns;
					$r_connections[0]->id  =$connection['id'];
					$r_connections[0]->first_name =$connection['first-name'].' '.$connection['last-name'];
					if($connection['picture-url']	)
					{
						$r_connections[0]->picture_url=$connection['picture-url'];
					}
					else
					{
						$r_connections[0]->picture_url='';
					}
				}
				return $r_connections;
		}
		
	}
	
	function plug_techjoomlaAPI_linkedinsend_message($post)
	{
		$session = JFactory::getSession();	
		if($session->get("['oauth']['linkedin']['authorized']",'') === TRUE)
    {
			if(!empty($post['contacts']))
			{
				$this->API_CONFIG['callbackUrl']=NULL;
				$this->linkedin = new LinkedInAPI($this->API_CONFIG);
				$this->linkedin->setTokenAccess($session->get("['oauth']['linkedin']['access']",''));
				
				if(!empty($post['message_copy']))
				{
					$copy = TRUE;
				}
				else
				{
					$copy = FALSE;
				}
				
				try{
				$response = $this->linkedin->message($post['contacts'], $post['message_subject'], $post['message_body'],$copy);
				}
				catch(LinkedInException $e)
				{ 
					$this->raiseException($e->getMessage());
					return false;
				}
				
				$return=$this->raiseLog($response,"From site to linkedin[Send Message]",$this->user->id,0);
				return $return;
			
			} 
			
            
    }
  }//end send message
 
	function plug_techjoomlaAPI_linkedingetstatus()
	{  	
		$i = 0;
		$returndata = array();
		$oauth_keys = $this->getToken(); 
		
		foreach($oauth_keys as $oauth_key){
			
			$oauth_token		 	= json_decode($oauth_key->token);
			$oauth_token_arr	=	json_decode($oauth_token->linkedin_oauth);
			try{
			$this->linkedin->retrieveTokenRequest();
			$this->API_CONFIG['callbackUrl']=NULL;
			$oauth_token_arr1=JArrayHelper::fromObject($oauth_token_arr);
			$this->linkedin->setTokenAccess($oauth_token_arr1);	
			$options='&type=SHAR&format=json';
			
			$response_updates = $this->linkedin->updates($options);
			}
			catch(LinkedInException $e)
			{ 
				$this->raiseException($e->getMessage(),$oauth_key->user_id,1);
				//return false;
			}
			
			$response=$this->raiseLog($response_updates,"From Linkedin To site[Get status]",$oauth_key->user_id,1);
			if($response)
			{
					$json_linkedin= $response_updates['linkedin']; 	
					$returndata[$i]['user_id'] = $oauth_key->user_id;
					$returndata[$i]['status'] = $this->renderstatus(json_decode($json_linkedin));
					$i++;
			}
		}
		return $returndata;
	}
  	function renderstatus($totalresponse)
  	{
			$status = array();
			$j=0;
			for($i=0; $i <= count($totalresponse->values); $i++ )
			{
				if(isset($totalresponse->values[$i]->updateContent->person->currentShare->comment)){
					$status[$j]['comment'] =  $totalresponse->values[$i]->updateContent->person->currentShare->comment;
					$status[$j]['timestamp'] = $totalresponse->values[$i]->updateContent->person->currentShare->timestamp;
					$j++;
				}
			} 
	  	return $status;
		}
	function plug_techjoomlaAPI_linkedinsetstatus($userid,$comment='')
	{
	
		//To do use json encode decode for this	
		$oauth_key = $this->getToken($userid); 
		$oauth_token		 	= json_decode($oauth_key[0]->token);
		$oauth	=	json_decode($oauth_token->linkedin_oauth, true);
		
		try{
			$this->linkedin = new LinkedInAPI($this->API_CONFIG);  	
			$this->linkedin->setTokenAccess($oauth);			

			$content = array ('comment' => $comment);
			//$content = array ('comment' => $comment, 'title' => '', 'submitted-url' => '', 'submitted-image-url' => '', 'description' => '');
			$status= $this->linkedin->share('new',$content); 
		
		}
		catch(LinkedInException $e)
		{
			
			$this->raiseException($e->getMessage(),$userid,1);
			return false;
		} 
		
		$response=$this->raiseLog($status,"FROM site to Linkedin profile",$userid,1);
		return $response;
	}
	
	function raiseException($exception,$userid='',$display=1,$params=array())
	{
		$path="";
		$params['name']=$this->_name;
		$params['group']=$this->_type;	
		if($this->params->get('log_file_path'))
		$path=& $this->params->get('log_file_path');
		techjoomlaHelperLogs::simpleLog($exception,$userid,'plugin',$this->errorlogfile,$path,$display,$params);
		return;
	}
	
	function raiseLog($status,$desc="",$userid="",$display="")
	{
		$params=array();		
		$params['desc']	=	$desc;
		$params['http_code']		=	$status['info']['http_code'];
		if(!$status['success'])
		{
			$response_error=techjoomlaHelperLogs::xml2array($status['linkedin']);
			
			$params['success']			=	false;
			$this->raiseException($response_error['error']['message'],$userid,$display,$params);
			return false;
		
		}
		else
		{
			$params['success']	=	true;
			$this->raiseException("Successfully updated Linkedin",$userid,$display,$params);		
			return true;
		
		}
	}
	
}//end class
