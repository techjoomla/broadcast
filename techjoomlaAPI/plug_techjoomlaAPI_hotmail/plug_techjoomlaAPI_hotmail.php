<?php
/*
	* @package Hotmail plugin for Invitex
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
// include the Hotmail class
if(JVERSION >='1.6.0')
require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_hotmail'.DS.'plug_techjoomlaAPI_hotmail'.DS.'lib'.DS.'contacts_importer.class.php');
else
require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_hotmail'.DS.'lib'.DS.'contacts_importer.class.php');

	
	$lang = & JFactory::getLanguage();
	$lang->load('plug_techjoomlaAPI_hotmail', JPATH_ADMINISTRATOR);	

class plgTechjoomlaAPIplug_techjoomlaAPI_hotmail extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_hotmail(& $subject, $config)
	{
		
		parent::__construct($subject, $config);		
		$appKey	=& $this->params->get('appKey');
		$appSecret	=& $this->params->get('appSecret');
		$this->db=JFactory::getDBO();
		$this->callbackUrl='';
		$this->errorlogfile='hotmail_error_log.php';
		$this->user =& JFactory::getUser();
		
		$this->API_CONFIG=array(
		'appKey'       => $appKey,
		'appSecret'    => $appSecret,
		'callbackUrl'  => NULL 
		);
		
		
		$import = new ContactsImporter;
		$import->returnURL =JRoute::_(JURI::base().'techjoomla_hotmail_api.php');//itemid /jroute
		$import->WLLPolicy = JRoute::_(JURI::base().'policy.php');
		$import->WLLAPIid = $appKey;
		$import->WLLSecret = $appSecret;
		
		//Create Global object For hotmail//
		$this->import_live=$import;
		
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
   	$plug['name']="Hotmail";
   //check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']=='' || !in_array($this->_name,$config))
		{	
			$plug['error_message']=true;		
			return $plug;
		}
   //Check if plugin has been configured.. Then only display the image. Else show message:This plugin is not configured propelry. Please contact the site Administrator . 
                            
     $plug['name']="Hotmail";
     $plug['api_used']=$this->_name; 
     $plug['message_type']='email';               
     $plug['img_file_name']="hotmail.png";  
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
			try{
				$url=$this->import_live->getWLLLink();
			}
			catch(Exception $e)
			{ 
				$this->raiseException($e->getMessage());
				return false;
			}
			
			$response=header('Location:'.$url);	
			$return=$this->raiseLog($response,JText::_('LOG_GET_REQUEST_TOKEN'),$this->user->id,0);		
			
			return true;
		
	}
	
	function get_access_token($get,$client,$callback) 
	{	
		
		$return=$this->raiseLog($_REQUEST,JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0);
		$response_data['hotmail_oauth']		= json_encode($get);	
		try{
		$this->imported_contacts = $this->import_live->getContacts();
		}
		catch(Exception $e)
		{ 
			$this->raiseException($e->getMessage());
			return false;
		}
		
		$connections = $this->imported_contacts;		
		$cnt=0;
			foreach ($connections as $contact)
			{
					if ($contact->email)
					{
						 $emails[$cnt]['id']= $contact->email;						
					}
					if ($contact->name)
					{
							$emails[$cnt]['name'] = $contact->name;						
					}
				
				$cnt++;
			}
			$contacts=$this->renderContacts($emails);
			
			$session = JFactory::getSession();
			$session->set("invitex['oauth']['hotmail']['contacts']", $contacts);
		
		
		$this->store($client,$response_data);
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
 	
	function plug_techjoomlaAPI_hotmailget_profile()
	{
	
	}
          
	function plug_techjoomlaAPI_hotmailget_contacts() 
	{
		$session = JFactory::getSession();
		$contacts=$session->get("invitex['oauth']['hotmail']['contacts']", '');		
		if(count($contacts)==0)
			$this->raiseException(JText::_('NO_CONTACTS'));	
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
				
				if(trim($first_name)=='' and trim($last_name)=='')
				{
					if(array_key_exists('name',$connection))
					{				
						$r_connections[$count]->name=$connection['name'];				
					}
				}
				
				$count++;
			}
		
		return $r_connections;
	}
	
	function plug_techjoomlaAPI_hotmailget_profile()
	{

  }
	function plug_techjoomlaAPI_hotmailsend_message($post)
	{
	
	}
	
	function plug_techjoomlaAPI_hotmailgetstatus()
	{  
	
	}
	function plug_techjoomlaAPI_hotmailsetstatus()
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
							if(isset($status['hotmail']))				
							$response_error=techjoomlaHelperLogs::xml2array($status['hotmail']);
				
			
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
