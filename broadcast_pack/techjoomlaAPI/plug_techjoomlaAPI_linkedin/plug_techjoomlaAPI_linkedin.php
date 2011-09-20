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
require_once('plug_techjoomlaAPI_linkedin'.DS.'lib'.DS.'linkedin.php');
class plgTechjoomlaAPIplug_techjoomlaAPI_linkedin extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_linkedin(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$appKey	=& $this->params->get('appKey');
		$appSecret	=& $this->params->get('appSecret');
		$this->API_CONFIG=array(
		'appKey'       => $appKey,
		'appSecret'    => $appSecret,
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
   	$plug['name']="Linkedin";
  	//check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']=='' || !$config['linkedin']) #TODO add condition to check config
		{		
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="linkedin.png";   
	//dipti
		$plug['apistatus'] = $this->status();
	//eoc     
		return $plug;   
	}
	
	function status(){
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$query = "SELECT linkedin_oauth,linkedin_secret FROM #__broadcast_users WHERE user_id = {$user->id}";
		$db->setQuery($query);
		$uaccess = $db->loadObject();
		if(isset($uaccess) ){
			if ($uaccess->linkedin_oauth && $uaccess->linkedin_secret)
				return 1;
		}
		return 0;
	}
	
	function get_request_token($callback) 
	{
		session_start();		
		// user initiated LinkedIn connection, create the LinkedIn object
		$this->API_CONFIG['callbackUrl']= $callback.'&'.LinkedInAPI::_GET_RESPONSE.'=1'; //JURI::base().'index.php?option=com_invitex&controller=invites&task=get_access_token&Itemid='.JRequest::getVar('Itemid').'&'.LinkedInAPI::_GET_RESPONSE.'=1';
		
		//create object of LinkedInAPI class
		$OBJ_linkedin = new LinkedInAPI($this->API_CONFIG);
				
		
		//check for response from LinkedIn
		$_GET[LinkedInAPI::_GET_RESPONSE] = (isset($_GET[LinkedInAPI::_GET_RESPONSE])) ? $_GET[LinkedInAPI::_GET_RESPONSE] : ''; 
		if(!$_GET[LinkedInAPI::_GET_RESPONSE])// LinkedIn hasn't sent us a response, the user is initiating the connectionSE
		{
			//send a request for a LinkedIn access token
			$response = $OBJ_linkedin->retrieveTokenRequest();
			if($response['success'] === TRUE)
			{
				// split up the response and stick the LinkedIn portion in the user session
				$_SESSION['oauth']['linkedin']['request'] = $response['linkedin'];
				//redirect the user to the LinkedIn authentication/authorisation page to initiate validation.
				//die('sucess');
				header('Location:'.LinkedInAPI::_URL_AUTH.$_SESSION['oauth']['linkedin']['request']['oauth_token']);
				return true;
			}
			else
			{
				// bad token request
				//echo "Request token retrieval failed:<br /><br />RESPONSE:<br /><br /><pre>" . print_r($response, TRUE) . "</pre><br /><br />LINKEDIN OBJ:<br /><br /><pre>" . print_r($OBJ_linkedin, TRUE) . "</pre>";
				return false;//important
			}
		}//end if
		
	}
	
	function get_access_token($get,$client,$callback) 
	{
		// user initiated LinkedIn connection, create the LinkedIn object
		$this->API_CONFIG['callbackUrl']=NULL;
		$OBJ_linkedin = new LinkedInAPI($this->API_CONFIG);
		//check for response from LinkedIn
		$get[LINKEDINAPI::_GET_RESPONSE] = (isset($get[LINKEDINAPI::_GET_RESPONSE])) ? $get[LINKEDINAPI::_GET_RESPONSE] : ''; 
		if($get[LINKEDINAPI::_GET_RESPONSE])// LinkedIn hasn't sent us a response, the user is initiating the connectionSE
		{
		
				// LinkedIn has sent a response, user has granted permission, take the temp access token, the user's secret and the verifier to request the user's real secret key
				$response = $OBJ_linkedin->retrieveTokenAccess($get['oauth_token'], $_SESSION['oauth']['linkedin']['request']['oauth_token_secret'], $get['oauth_verifier']);
				
				if($response['success'] === TRUE)
				{
				  // the request went through without an error, gather user's 'access' tokens
				  $_SESSION['oauth']['linkedin']['access'] = $response['linkedin'];
				  // set the user as authorized for future quick reference
				  $_SESSION['oauth']['linkedin']['authorized'] = TRUE;
				//dipti
						$data['linkedin_oauth']		= implode(",",$response['linkedin']);		
						$data['linkedin_secret']	= $get['oauth_verifier'];
						$this->store($client,$data); 
						return true;
				//eoc
				}
				else
				{
					 return false;
				}
			}
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
	function remove_token($client)
	{ 
		$db	 = & JFactory::getDBO();
		$user 	= JFactory::getUser();
		#TODO add condition for client also
		$qry 	= "UPDATE #__broadcast_users SET linkedin_oauth='',linkedin_secret='' WHERE user_id = {$user->id}";
		$db->setQuery($qry);	
		$db->query();
	}
	
	function get_contacts() 
	{
		$this->API_CONFIG['callbackUrl']= JURI::base().'index.php?option=com_invitex&view=invites&layout=apis';
		if($_SESSION['oauth']['linkedin']['authorized'] === TRUE)
    {
			// user is already connected
			$OBJ_linkedin = new LinkedInAPI($this->API_CONFIG);
			$OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
			$response = $OBJ_linkedin->connections('~/connections:(id,first-name,last-name,picture-url)');
			if($response['success'] === TRUE)
			{
				$connections = simplexml_load_string($response['linkedin']);
				$contacts=array();
				$contacts=$this->renderContacts($connections);
			} 
			else 
			{
				//TODO Remove this add JError Raise Warning
				// connections retrieval failed
				$menu = &JSite::getMenu();
				$items= $menu->getItems('link', 'index.php?option=com_invitex&view=invites');//pass the link for which you want the ItemId.
				if(isset($items[0])){
					$itemid = $items[0]->id;
				}		

				$mainframe->redirect('index.php?option=com_invitex&view=invites&Itemid='.$itemid,$msg);
			}
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
		else
		{
				//TODO Remove this add JError Raise Warning
			$menu = &JSite::getMenu();			
			$items= $menu->getItems('link', 'index.php?option=com_invitex&view=invites');//pass the link for which you want the ItemId.
			if(isset($items[0])){
				$itemid = $items[0]->id;
			}			
			$mainframe->redirect('index.php?option=com_invitex&view=invites&Itemid='.$itemid,"No connections found for this account");
		}
	}
	
	function send_message($post)
	{
		if($_SESSION['oauth']['linkedin']['authorized'] === TRUE)
    {
			if(!empty($post['contacts']))
			{
				$this->API_CONFIG['callbackUrl']=NULL;
				$OBJ_linkedin = new LinkedInAPI($this->API_CONFIG);
				$OBJ_linkedin->setTokenAccess($_SESSION['oauth']['linkedin']['access']);
				
				if(!empty($post['message_copy']))
				{
					$copy = TRUE;
				}
				else
				{
					$copy = FALSE;
				}
				$response = $OBJ_linkedin->message($post['contacts'], $post['message_subject'], $post['message_body'],$copy);
				if($response['success'] === TRUE)
				{
					//return "message sent";
					return true;
				} 
				else
				{
					return false;
				}
			} 
			else
			{
				//TODO Use language constants
				echo "You must select at least one recipient.";
			}
            
    }
  }//end send message
	function getstatus($oauth_key)
	{  	
		$autharray = explode(",",$oauth_key->linkedin_oauth);
		$i = 0;
		$oauth= array();
		$keys = array(0=>'oauth_token',1=>'oauth_token_secret',2=>'oauth_expires_in',3=>'oauth_authorization_expires_in' );
		foreach($autharray as $v )
		{
		$oauth[$keys[$i++]] = $v;
		}
		$OBJ_linkedin = new LinkedInAPI($this->API_CONFIG);  	
		$OBJ_linkedin->retrieveTokenRequest();
		$this->API_CONFIG['callbackUrl']=NULL;
		$OBJ_linkedin->setTokenAccess($oauth);				
		$options='&type=SHAR&format=json';
		$response_updates = $OBJ_linkedin->updates($options);//before count
		$json_linkedin= $response_updates['linkedin'];

		//print_r(json_decode($json_linkedin));		
		$this->setstatus($oauth_key);				
	}
  
	function setstatus($oauth_key,$content='')
	{
	//Todo use json encode decode for this	
		$autharray = explode(",",$oauth_key->linkedin_oauth);
		$i = 0;
		$oauth= array();
		$keys = array(0=>'oauth_token',1=>'oauth_token_secret',2=>'oauth_expires_in',3=>'oauth_authorization_expires_in' );
		foreach($autharray as $v )
		{
		$oauth[$keys[$i++]] = $v;
		}
		$OBJ_linkedin = new LinkedInAPI($this->API_CONFIG);  	
		$OBJ_linkedin->setTokenAccess($oauth);				
		$content = array ('comment' => $content, 'title' => '', 'submitted-url' => '', 'submitted-image-url' => '', 'description' => '');
		$status= $OBJ_linkedin->share('new',$content);
		return $status[success];
	}
}//end class
