<?php
/*
	* @package orkut plugin for Invitex
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

// include the orkut class
if(JVERSION >='1.6.0')
{
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'plug_techjoomlaAPI_orkut'.DS.'lib'.DS.'orkut.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'plug_techjoomlaAPI_orkut'.DS.'friends.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'plug_techjoomlaAPI_orkut'.DS.'lib'.DS.'auth/auth.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'plug_techjoomlaAPI_orkut'.DS.'scrap.php');
}
else
{
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'lib'.DS.'orkut.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'friends.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'lib'.DS.'auth/auth.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_orkut'.DS.'scrap.php');
}
$lang = & JFactory::getLanguage();
$lang->load('plug_techjoomlaAPI_orkut', JPATH_ADMINISTRATOR);	
class plgTechjoomlaAPIplug_techjoomlaAPI_orkut extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_orkut(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$appKey	=& $this->params->get('appKey');
		$appSecret	=& $this->params->get('appSecret');
		$this->callbackUrl='';
		$this->errorlogfile='orkut_error_log.php';
		$this->user =& JFactory::getUser();
		
		$this->db=JFactory::getDBO();
		$this->API_CONFIG=array(
		'appKey'       => $appKey,
		'appSecret'    => $appSecret,
		'callbackUrl'  => NULL 
		);
		//$this->orkut = new orkutAPI($this->API_CONFIG);
		$orkutApi = new Orkut($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret']);
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
   	$plug['name']="Orkut";
  	//check if keys are set
		if($this->API_CONFIG['appKey']=='' || $this->API_CONFIG['appSecret']=='' || !in_array($this->_name,$config)) #TODO add condition to check config
		{	
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="orkut.png";   
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
	
	 	$query 	= "SELECT token FROM #__techjoomlaAPI_users WHERE user_id = {$this->user->id}  AND api='{$this->_name}'".$where;
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
		$this->API_CONFIG['callbackUrl']= $callback; 
		
		$orkutApi= new Orkut($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret']);
		try {
			$orkutApi->login($callback);
			//print_r($orkutApi);die;
		}
		catch(Exception $e) {
			//print_r($e);
			$_SESSION['oauth_token']='';
			$this->raiseException($e->getMessage());
		}
	
		
	}

	
	function get_access_token($get,$client,$callback) 
	{
	
		$session = JFactory::getSession();
		$this->API_CONFIG['callbackUrl']=NULL;
		if($get['oauth_token'])
		{
				
				$session->set("['oauth']['orkut']['authorized']",true);
				$_SESSION['oauth_token']=$response_data['orkut_oauth']		= $get['oauth_token'];		
				$_SESSION['oauth_verifier']=$response_data['orkut_secret']	= $get['oauth_verifier'];
				$this->store($client,$response_data);
			
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
	
	function plug_techjoomlaAPI_orkutget_contacts() 
	{
			JRequest::setVar('oauth_token',$_SESSION['oauth_token']);
			JRequest::setVar('oauth_verifier',$_SESSION['oauth_verifier']);
		$session = JFactory::getSession();
		$this->API_CONFIG['callbackUrl']= JRoute::_(JURI::base().'index.php?option=com_invitex&view=invites&layout=apis');
		//print_r($_SESSION);die;
		if($session->get("['oauth']['orkut']['authorized']",'') === TRUE)
    {
			// user is already connected
			try{
						$orkutApi= new Orkut($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret']);
						$orkutApi->login($this->API_CONFIG['callbackUrl']);
						// create the instance and print the json output
						$friends = new Friends($orkutApi);
						$friends->fetchUsers();
						$contacts=$friends->execute();
						
			}
			catch(orkutException $e)
			{ 
				$this->raiseException($e->getMessage());
				return false;
			}
		
			if($friends)
			{
				try{
					$contacts=$this->renderContacts($contacts);
				}
				catch(Exception $e)
				{ 
					$this->raiseException($e->getMessage());
					return false;
				}
				if(count($contacts)==0)
				$this->raiseException(JText::_('NO_CONTACTS'));
			} 
			
    }
  	return $contacts;
	}
	
	function renderContacts($connections)
	{

		$mainframe=JFactory::getApplication();		
		$conns = $connections[1]['friends'];
		if($conns['data']['list'])
		{

				$conns = $conns['data']['list'];
				$count=0;
				$r_connections=array();
			
					foreach($conns as $connection)
					{
							
						if($connection['id'])
						{
							$r_connections[$count]->id  =$connection['id'];
							$r_connections[$count]->name =$connection['displayName'];
							if($connection['thumbnailUrl'])
							{
								$r_connections[$count]->picture_url=$connection['thumbnailUrl'];
							}
							else
							{
								$r_connections[$count]->picture_url='';
							}
							$count++;
						}
						else
							continue;
					}
				return $r_connections;
		}
		
	}
	
	function plug_techjoomlaAPI_orkutsend_message($raw_mail,$invitee_data,$cap)
	{
			
		require(JPATH_SITE.DS.'components'.DS.'com_invitex'.DS.'config.php');
	
			JRequest::setVar('oauth_token',$_SESSION['oauth_token']);
			JRequest::setVar('oauth_verifier',$_SESSION['oauth_verifier']);

			//print_r($raw_mail);die;
			$uids=array();
			foreach($invitee_data as $id=>$invitee_name)
		 	{
					$invitee_email[]	= "'".$invitee_name.'|'.$id."'";
					$uids[]	=	$id;	
			}
			$session = JFactory::getSession();	
			
			if($session->get("['oauth']['orkut']['authorized']",'') === TRUE)
    	{
					if($uids)
					{
								
									$this->API_CONFIG['callbackUrl']=NULL;
									// message
									if($session->get('invite_anywhere'))
									{
												$invitee_string=implode(',',$invitee_email);
												$db				= JFactory::getDBO();
												$user_id	=	JFactory::getUser()->id;
												$query="select i.id from #__invitex_imports as i, #__invitex_imports_emails as ie
																WHERE invitee_email IN($invitee_string) AND i.id=ie.import_id AND i.inviter_id=$user_id group by ie.import_id order by i.id DESC LIMIT 1";
												$db->setQuery($query);
												$import_id=trim($db->loadResult());
												
												$raw_mail['message_join']=cominvitexHelper::getIAinviteURL($import_id);
									}
									else
									{
										$raw_mail['message_register']=cominvitexHelper::getinviteURL();
									}							
									$message	=	cominvitexHelper::tagreplace($raw_mail);	
								$orkutApi= new Orkut($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret']);
								$orkutApi->login($this->API_CONFIG['callbackUrl']);
								// create the instance and print the json output
						
								$send_scrap = new Scrap($orkutApi);
								if(isset($cap['tokencaptcha']) && $cap['tokencaptcha']!='' && isset($cap['textcaptcha']) && $cap['textcaptcha']!='')
								{
				        		$send_scrap->setCaptchaRequest($cap['tokencaptcha'], $cap['textcaptcha']);
								}					
							$resp	=	$send_scrap->send($uids, $message);
							return $resp;
			
					} 
		}
  }//end send message
 
	function plug_techjoomlaAPI_orkutgetstatus()
	{  	
		$i = 0;
		$returndata = array();
		$oauth_keys = $this->getToken(); 
		
		foreach($oauth_keys as $oauth_key){
			
			$oauth_token		 	= json_decode($oauth_key->token);
			$oauth_token_arr	=	json_decode($oauth_token->orkut_oauth);
			try{
			$this->orkut->retrieveTokenRequest();
			$this->API_CONFIG['callbackUrl']=NULL;
			$oauth_token_arr1=JArrayHelper::fromObject($oauth_token_arr);
			$this->orkut->setTokenAccess($oauth_token_arr1);	
			if($this->params->get('broadcast_limit'))
			$orkut_profile_limit=$this->params->get('broadcast_limit');
			else
			$orkut_profile_limit=2;
			$options='&type=SHAR&format=json&count='.$orkut_profile_limit;
			
			$response_updates = $this->orkut->updates($options);
			}
			catch(orkutException $e)
			{ 
				$this->raiseException($e->getMessage(),$oauth_key->user_id,1);
				//return false;
			}
			
			if(!$response_updates)	
			continue;
			$response=$this->raiseLog($response_updates,JText::_('LOG_GET_STATUS'),$oauth_key->user_id,1);
			if($response)
			{
					$json_orkut= $response_updates['orkut']; 	
					$returndata[$i]['user_id'] = $oauth_key->user_id;
					$returndata[$i]['status'] = $this->renderstatus(json_decode($json_orkut));
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
					$status[$j]['timestamp'] = number_format($status[$j]['timestamp'],0,'','');
					$status[$j]['timestamp'] = intval($status[$j]['timestamp'] /1000); 
					$j++;
				}
			} 
	  	return $status;
		}
function getCaptchaURL($url)
{
	header("content-type: image/jpeg");
	ini_set('output_buffering ','off');
	//output_buffering = Off;
	// captcha url
	$c = parse_url($url);
	$cap=explode('=',$c['query']);
	$captcha=$cap[1].'='.$cap[2];
	//echo $captcha;die;
	$orkutApi= new Orkut($this->API_CONFIG['appKey'], $this->API_CONFIG['appSecret']);
	$orkutApi->login('http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	$r = $orkutApi->executeCaptcha($captcha,'');
	//return ($r['data']);
print_r($r['data']);die;
		//	return ($r['data']);
} 
	  	
	function plug_techjoomlaAPI_orkutsetstatus($userid,$originalContent,$comment,$attachment='')
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
		if(is_object($status_log))
		$status=JArrayHelper::fromObject($status_log,true);
		
		
		
		if(is_array($status_log) or is_array($status))
		{
			$status=$status_log;
			if(isset($status['info']['http_code']))
			{
				$params['http_code']		=	$status['info']['http_code'];
				if(!$status['success'])
				{
						if(isset($status['orkut']))				
							$response_error=techjoomlaHelperLogs::xml2array($status['orkut']);
				
			
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
	
	
	function plug_techjoomlaAPI_orkutget_profile()
	{

  }
}//end class
