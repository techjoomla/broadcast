<?php
/*
	* @package Gmail plugin for Invitex
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
	if(JVERSION >='1.6.0')
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_gmail'.DS.'plug_techjoomlaAPI_gmail'.DS.'lib'.DS.'GmailOath.php');
	else
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_gmail'.DS.'lib'.DS.'GmailOath.php');
	
	$lang = & JFactory::getLanguage();
	$lang->load('plug_techjoomlaAPI_gmail', JPATH_ADMINISTRATOR);	
	class plgTechjoomlaAPIplug_techjoomlaAPI_gmail extends JPlugin
	{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_gmail(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$appKey	=& $this->params->get('appKey');
		$appSecret	=& $this->params->get('appSecret');
		$this->callbackUrl='';
		$this->errorlogfile='gmail_error_log.php';
		$this->user =& JFactory::getUser();
		
		$this->db=JFactory::getDBO();
		$this->API_CONFIG=array(
		'appKey'       => $appKey,
		'appSecret'    => $appSecret,
		'argarray' 		 => array(),
		'debug' 			 => 0,
		'callbackUrl'  => NULL 
		);

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
   	$plug['name']="Gmail";
  	//check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']=='')//  || !in_array($this->_name,$config))
		{
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='email';               
		$plug['img_file_name']="gmail.png"; 
		if(isset($config['client']))
		$client=$config['client'];
		else
		$client='';
		$plug['apistatus'] = $this->connectionstatus($client);
		return $plug;   
	}
	
	function connectionstatus($client=''){
		$where='';
		if($client)
		$where=" AND client='".$client."'";
	 	$query 	= "SELECT token FROM #__techjoomlaAPI_users WHERE token<>'' AND user_id = {$this->user->id}  AND api='{$this->_name}'".$where;
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
		// user initiated Gmail connection, create the Gmail object
		$this->API_CONFIG['callbackUrl']=$callback;
		try{
		$oauth =new GmailOath($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret'], $this->API_CONFIG['argarray'], $this->API_CONFIG['debug'], $this->API_CONFIG['callbackUrl']);
		
		$getcontact=new GmailGetContacts();
		$response=$getcontact->get_request_token($oauth, false, true, true);
		}
		catch(Exception $e)
		{ 
			$this->raiseException($e->getMessage());
			return false;
		}
		$return=$this->raiseLog($response,JText::_('LOG_GET_REQUEST_TOKEN'),$this->user->id,0);
		if($response['oauth_token']){
		$session->set("invitex['oauth']['gmail']['request']", $response);
		$session->set("invitex['oauth']['gmail']['request']['oauth_token']", $response['oauth_token']);
		$session->set("invitex['oauth']['gmail']['request']['oauth_token_secret']", $response['oauth_token_secret']);
		header('Location:'."https://www.google.com/accounts/OAuthAuthorizeToken?oauth_token=". $oauth->rfc3986_decode($response['oauth_token']));
		return true;
		}
		else
		{
			$this->raiseException("consumer key unknown");
			return false;
		}
		
	}
	function get_access_token($get,$client,$callback) 
	{
		$session = JFactory::getSession();
		// user initiated Gmail connection, create the Gmail object
		$this->API_CONFIG['callbackUrl']=NULL;
		try{
		$oauth =new GmailOath($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret'], $this->API_CONFIG['argarray'], $this->API_CONFIG['debug'], $this->API_CONFIG['callbackUrl']);
		$request_token=$oauth->rfc3986_decode($get['oauth_token']);
		$request_oauth_token_secret=$session->get("invitex['oauth']['gmail']['request']['oauth_token_secret']", '');
		
		$request_token_secret=	$oauth->rfc3986_decode($request_oauth_token_secret);
		$oauth_verifier= $oauth->rfc3986_decode($get['oauth_verifier']);
		$getcontact_access=new GmailGetContacts();
		$retarr = $getcontact_access->get_access_token($oauth,$request_token, $request_token_secret,$oauth_verifier, false, true, true);
		}
		catch(Exception $e)
		{ 
			$this->raiseException($e->getMessage());
			return false;
		}
		$return=$this->raiseLog($retarr,JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0);
		$response_data['gmail_oauth']		= json_encode($retarr);		
		$this->store($client,$response_data);
		if($retarr['oauth_token'])
		{
				$session->set("invitex['oauth']['gmail']['authorized']", true);
				$session->set("invitex['oauth']['gmail']['request']['oauth_token']", $retarr['oauth_token']);
				$session->set("invitex['oauth']['gmail']['request']['oauth_token_secret']", $retarr['oauth_token_secret']);				
				return true;
		}
		else
		{
						 return false;
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
	
	function plug_techjoomlaAPI_gmailget_contacts() 
	{
		global $mainframe ;
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();		
		//fIND CORRECT ITEM ID & PASS IT TO THE CALL BACK
		$this->API_CONFIG['callbackUrl']= JURI::base().'index.php?option=com_invitex&view=invites&layout=apis';
		try{
		$oauth =new GmailOath($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret'], $this->API_CONFIG['argarray'], $this->API_CONFIG['debug'], $this->API_CONFIG['callbackUrl']);
		
		$access_token=$oauth->rfc3986_decode($session->get("invitex['oauth']['gmail']['request']['oauth_token']", ''));
		$access_token_secret=$oauth->rfc3986_decode($session->get("invitex['oauth']['gmail']['request']['oauth_token_secret']", ''));
		}
		catch(Exception $e)
		{ 
			$this->raiseException($e->getMessage());
			return false;
		}
		
		$contacts=array();
		
		if($session->get("invitex['oauth']['gmail']['authorized']", ''))
    {
    	try{
			$getcontact_access=new GmailGetContacts();
		  $connections= $getcontact_access->callcontact($oauth, $access_token, $access_token_secret, false, true);
			$contacts=$this->renderContacts($connections);
			}
			catch(Exception $e)
			{ 
				$this->raiseException($e->getMessage());
				return false;
			}
			if(count($contacts)==0)
			$this->raiseException(JText::_('NO_CONTACTS'));				
		}
		
		return $contacts;
	}
	function renderContacts($connections)
	{
		$r_connections=array();
		$count=0;
		foreach($connections as $conn)
		{
			if(isset($conn['gd$email']) || isset($conn['title']['$t']))
			{
				if(isset($conn['gd$email']))
				$r_connections[$count]->id = $conn['gd$email'][0]['address'];
				if(isset($conn['title']['$t']))
				$r_connections[$count]->name =$conn['title']['$t'];
			
				if(array_key_exists('picture-url',$conn))
				{
								$r_connections[$count]->picture_url=$conn['picture-url'];
				}
				else
				{
								$r_connections[$count]->picture_url='';
				}
			}
			else 
			continue;
			$count++;
		
		}
		return $r_connections;
	}
	
	function plug_techjoomlaAPI_gmailget_profile()
	{

  }
	function plug_techjoomlaAPI_gmailsend_message($post)
	{
	
	}
	
	function plug_techjoomlaAPI_gmailgetstatus()
	{  
	
	}
	function plug_techjoomlaAPI_gmailsetstatus()
	{  
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
	
	function raiseLog($status_log,$desc="",$userid="",$display="")
	{
		
		$params=array();		
		$params['desc']	=	$desc;
		if(is_object($status))
		$status=JArrayHelper::fromObject($status_log,true);
		
		if(is_array($status))
		{
			if(isset($status['info']['http_code']))
			{
				$params['http_code']		=	$status['info']['http_code'];
				if(!$status['success'])
				{
						if(isset($status['gmail']))				
							$response_error=techjoomlaHelperLogs::xml2array($status['gmail']);
				
			
					$params['success']			=	false;
					$this->raiseException($response_error['error']['message'],$userid,$display,$params);
					return false;
		
				}
				else
				{
					$params['success']	=	true;
					$this->raiseException(JText::_('LOG_SUCCESS'),$userid,$display,$params);		
					return true;
		
				}
			
			}
		}
		$this->raiseException(JText::_('LOG_SUCCESS'),$userid,$display,$params);	
		return true;	
	}
	
}//end class
