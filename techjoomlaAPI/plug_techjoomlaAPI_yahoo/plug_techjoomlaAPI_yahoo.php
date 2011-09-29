<?php
/*
	* @package Yahoo plugin for Invitex
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

if(JVERSION >='1.6.0')
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_yahoo'.DS.'plug_techjoomlaAPI_yahoo'.DS.'lib'.DS.'Yahoo.inc');
else
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_yahoo'.DS.'lib'.DS.'Yahoo.inc');
	

$lang = & JFactory::getLanguage();
$lang->load('plug_techjoomlaAPI_yahoo', JPATH_ADMINISTRATOR);	
class plgTechjoomlaAPIplug_techjoomlaAPI_yahoo extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_yahoo(& $subject, $config)
	{
		
		parent::__construct($subject, $config);
		$appKey	=& $this->params->get('appKey');
		$appSecret	=& $this->params->get('appSecret');
		$appId	=& $this->params->get('appId');
		
		$this->callbackUrl='';
		$this->errorlogfile='yahoo_error_log.php';
		$this->user =& JFactory::getUser();		
		$this->db=JFactory::getDBO();
		$this->API_CONFIG=array(
		'appKey'       => $appKey,
		'appSecret'    => $appSecret,
		'appId'    => $appId,
		'callbackUrl'  => NULL,
		'logfilename'=>"yahoo_error_log.php"
		
		);
		
		//create log object for plugins
    		$this->callbackUrl='';
		$this->errorlogfile='yahoo_error_log.php';
		$this->user =& JFactory::getUser();
		
		$this->db=JFactory::getDBO();
		
		
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
   	$plug['name']="Yahoo";
  	//check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']=='' || $this->API_CONFIG['appId']=='' || !in_array($this->_name,$config))
		{
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='email';               
		$plug['img_file_name']="yahoo.png";
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
	
	
	function get_request_token() 
	{
		$session = JFactory::getSession();
		$YahooLogger=new YahooLogger;
		$YahooLogger->setDebug(true);	
		$YahooLogger->setDebugDestination('CONSOLE');
				
		$session->set("invitex['oauth']['yahoo']['contacts']", '');
		$session->set("invitex['oauth']['yahoo']['authorized']", false);
		//check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']=='')
		return false;
	
		try{
		
			YahooSession::clearSession();
			$hasSession = YahooSession::hasSession($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret'], $this->API_CONFIG['appId']);
		
			if(!$hasSession) {
				// create the callback url,
				$callback=$this->API_CONFIG['callbackUrl']=JURI::base().'index.php?option=com_invitex&controller=invites&task=get_access_token&Itemid='.JRequest::getVar('Itemid');
				// pass the credentials to get an auth url.
				// this URL will be used for the pop-up.		
				YahooSession::clearSession();
				$hasSession = YahooSession::hasSession($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret'], $this->API_CONFIG['appId']);
				$auth_url = YahooSession::createAuthorizationUrl($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret'], $callback);

				if($auth_url)
				$session->set("invitex['oauth']['yahoo']['authorized']", true);
				$res=header('Location:'.$auth_url);
			}
		}		
		catch(YahooException $e)
		{ 
			$this->raiseException($e->getMessage());
			return false;
		}
		
		$return=$this->raiseLog($hasSession,JText::_('LOG_GET_REQUEST_TOKEN'),$this->user->id,0);
			if($res)
			return true;	
			
	}
	
	function get_access_token($get,$client,$callback)  
	{
		unset($_SESSION['yahoo_exception']);			
		$session = JFactory::getSession();	
		try{
		$session_yahoo = YahooSession::requireSession($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret'],$this->API_CONFIG['appId']);
		}
		catch(YahooException $e)
		{ 
			$this->raiseException($e->getMessage());
			return false;
		}
		$return=$this->raiseLog($session_yahoo,JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0);
		if($session) {
			//Get the currently sessioned user.
			if($session_yahoo)
			{
				try{
				$user = $session_yahoo->getSessionedUser();
				$response_data['yahoo_oauth']= json_encode($user);		
				$this->store($client,$response_data);
				$contacts = $user->getContacts(0, 1000);
				}
				catch(YahooException $e)
				{ 
					$this->raiseException($e->getMessage());
					return false;
				}			
				$session->set("invitex['oauth']['yahoo']['contacts']", $contacts);	
				return true;
			}	
			else
			{
				return false;
				
			}	
			

			}
			else
			return false;
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
	
          
	function plug_techjoomlaAPI_yahooget_contacts()
	{
		$session = JFactory::getSession();
		$contacts=array();
		
		$this->API_CONFIG['callbackUrl']= JURI::base().'index.php?option=com_invitex&view=invites&layout=apis';
		if($session->get("invitex['oauth']['yahoo']['authorized']",'')=== true)
    	{
			$contacts=$session->get("invitex['oauth']['yahoo']['contacts']", '');
			$cnt=0;
			
			foreach ($contacts->contacts->contact as $contact)
			{
				foreach ($contact->fields as $field)
				{
					if ($field->type == "email")
					{
						 $emails[$cnt]['id'] = $field->value;
					}
					if ($field->type == "name")
					{
						$emails[$cnt]['first-name'] = $field->value->givenName;
						$emails[$cnt]['last-name'] = $field->value->familyName;
					}
				}
				$cnt++;
			}
			$contacts=$this->renderContacts($emails);
			if(count($contacts)==0)
			$this->raiseException(JText::_('NO_CONTACTS'));

    }
    
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
				{
					if($connection['name'])
					$r_connections[$count]->name=$connection['name'];
				}
				
				$count++;
			}
		
		return $r_connections;
	}
	
	function plug_techjoomlaAPI_yahooget_profile()
	{

  }
	function plug_techjoomlaAPI_yahoosend_message($post)
	{
	
	}
	
	function plug_techjoomlaAPI_yahoogetstatus()
	{  
	
	}
	function plug_techjoomlaAPI_yahoosetstatus()
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
	
	function raiseLog($status,$desc="",$userid="",$display="")
	{
		
		$params=array();		
		$params['desc']	=	$desc;
		
		if(isset($status['info']['http_code']))
		{
			$params['http_code']		=	$status['info']['http_code'];
			if(!$status['success'])
			{
				$response_error=techjoomlaHelperLogs::xml2array($status['yahoo']);
			
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
		$this->raiseException(JText::_('LOG_SUCCESS'),$userid,$display,$params);	
		return true;	
	}
}//end class
