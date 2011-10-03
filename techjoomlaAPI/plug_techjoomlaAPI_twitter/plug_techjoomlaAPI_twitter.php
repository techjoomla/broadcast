<?php
/*
	* @package Twitter plugin for TechjoomlaAPI
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
// include the Twitter class
if(JVERSION >='1.6.0')
{
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_twitter'.DS.'plug_techjoomlaAPI_twitter'.DS.'lib'.DS.'tmhOAuth.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_twitter'.DS.'plug_techjoomlaAPI_twitter'.DS.'lib'.DS.'tmhUtilities.php');
}
else
{
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_twitter'.DS.'lib'.DS.'tmhOAuth.php');
	require_once(JPATH_SITE.DS.'plugins'.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_twitter'.DS.'lib'.DS.'tmhUtilities.php');
}

$lang = & JFactory::getLanguage();
$lang->load('plug_techjoomlaAPI_twitter', JPATH_ADMINISTRATOR);
	
class plgTechjoomlaAPIplug_techjoomlaAPI_twitter extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_twitter(& $subject, $config)
	{
		
		parent::__construct($subject, $config);
		$this->appKey	=& $this->params->get('appKey');
		$this->appSecret	=& $this->params->get('appSecret');
		$this->errorlogfile='twitter_error_log.php';
		$this->user =& JFactory::getUser();		
		$this->db=JFactory::getDBO();
		
		 $this->twitter = new tmhOAuth(array(
  	'consumer_key'    => $this->appKey,
  	'consumer_secret' => $this->appSecret,
		));
		$this->streaming_callback=array();
		
		
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
   	$plug['name']="Twitter";
  	//check if keys are set
		if($this->appKey=='' || $this->appSecret=='') //|| !in_array($this->_name,$config)) #TODO add condition to check config
		{
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="twitter.png"; 
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
	
	$params = array('oauth_callback'=> $callback);
	//echo $callback;
  $code = $this->twitter->request('POST', $this->twitter->url('oauth/request_token', ''), $params);
 
  if ($code == 200) {
    $_SESSION['oauth'] = $this->twitter->extract_params($this->twitter->response['response']);
    
    $authurl = $this->twitter->url("oauth/authorize", '') .  "?oauth_token=".$_SESSION['oauth']['oauth_token'];
    $response=header('Location:'.$authurl);
     
  } else { 
  		//print_r($this->twitter->response['response']);die;
 			$this->outputError($this->twitter);
  }

			return true;
	}
	
	function get_access_token($get,$client='',$callback='') 
	{
			
		if(isset($get['oauth_verifier'])) {
			$this->twitter->config['user_token']  = $_SESSION['oauth']['oauth_token'];
			$this->twitter->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  		$code = $this->twitter->request('POST', $this->twitter->url('oauth/access_token', ''),
  					 array('oauth_verifier' => $get['oauth_verifier']));
			if ($code == 200) 
			{
				$_SESSION['access_token'] = $this->twitter->extract_params($this->twitter->response['response']);
				$data = $_SESSION['access_token'];
				$return=$this->raiseLog($data,JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0); 
				$this->store($client,$data);
				return true;
				
			} 
			else{
					$this->outputError($code);
			}
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
	
	function plug_techjoomlaAPI_twitterget_contacts()
	{
		$friends= $this->twitter->api('/me/friends');
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
				$emails[$cnt]['picture-url']= 'https://graph.twitter.com/'.$emails[$cnt]['id'].'/picture';																						
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
	
	function plug_techjoomlaAPI_twittersend_message($post)
	{
	
  }//end send message
  
  
	function plug_techjoomlaAPI_twittergetstatus()
	{ 
	 $oauth_keys =array();
	 $oauth_keys = $this->getToken();
	 //$this->plug_techjoomlaAPI_twittersetstatus('62','test for broadcast1');die;
	 if(!$oauth_keys)
		return false;
	 	foreach($oauth_keys as $oauth_key){	
	 		
			$token =	json_decode($oauth_key->token,true);	
			//$this->twitter->config['user_token']=$token['oauth_token'];
			//$this->twitter->config['user_token_secret']=$token['oauth_token_secret'];
			//,$token['oauth_token_secret']
			$tmhOAuth = new tmhOAuth(array(
  		'consumer_key'    => $this->appKey,
  		'consumer_secret' => $this->appSecret,
			'user_token'      => $token['oauth_token'],
			'user_secret'     => $token['oauth_token_secret'],));

			$method = "https://userstream.twitter.com/2/user.json";
			$params = array(
				'limit'=>10,
			);
			$response=$tmhOAuth->streaming_request('POST', $method, $params,'my_streaming_callback_data');
			print_r($response);die;
			
			echo "<br><------------->";
			
		  
			
			if($returndata[$i]['status'])
			{
				
				
			}
			else
			{
				
				
			}
			
			$i++;
			}
			die;
	 /*
	 $this->twitter = new tmhOAuth(array(
  	'consumer_key'    => $this->appKey,
  	'consumer_secret' => $this->appSecret,
  	
  	
		));
		$method = "https://userstream.twitter.com/2/user.json";
		$params = array(
  	
		);
$tmhOAuth->streaming_request('POST', $method, $params, 'my_streaming_callback', false);

		$access_token =$oauth_key->twitter_secret;	
		$today=date('Y-m-d');
		$twitter_profile_limit=10;
		try {
			$response = $this->twitter->api($oauth_key->facbook_uid.'/statuses',array('access_token'=>$oauth_key->twitter_secret,'since'=>$today,'limit'=>$twitter_profile_limit));
		}
		catch(TwitterApiException $o ){
		  print_r($o);
		}
			$status_arr=$this->renderstatus($response);
			return $status_arr;
		
		
		*/
			
	}
	public function my_streaming_callback_data($data)
  {
  	print_r($data);die('here1');
  
  }
	function renderstatus($response)
	{
	return $response;
		if($response)
		{
			if(count($response)>=1)
			{
			$i=0;
			foreach($response as $data)
			{
			$status[$i]=$data;
			$i++;
			
			}
			return $status;
			
			}
		}
		else
		return array();
	
	}

	function plug_techjoomlaAPI_twittersetstatus($userid='',$content='')
	{	
		$oauth_key = $this->getToken($userid);
		
		if(!$oauth_key)
		return false;
		else
		$token =json_decode($oauth_key[0]->token,true);	
		
		$tmhOAuth = new tmhOAuth(array(
  		'consumer_key'    => $this->appKey,
  		'consumer_secret' => $this->appSecret,
			'user_token'      => $token['oauth_token'],
			'user_secret'     => $token['oauth_token_secret'],));

			$method = "https://userstream.twitter.com/2/user.json";
			$params = array(
				// parameters go here
			);
			//$response=$tmhOAuth->streaming_request('POST', $method, $params,'',false);
			echo $code = $tmhOAuth->request('POST', $tmhOAuth->url('1/statuses/update'), array(
  'status' => $content,
	));die;

	
	
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
		
		
		
		if(is_array($status_log))
		{
			$status=$status_log;
			if(isset($status['info']['http_code']))
			{
				$params['http_code']		=	$status['info']['http_code'];
				if(!$status['success'])
				{
						if(isset($status['facebook']))				
							$response_error=techjoomlaHelperLogs::xml2array($status['facebook']);
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
		$this->raiseException($status_log,$userid,$display,$params);	
		return true;	
	}
	
	function plug_techjoomlaAPI_twitterget_profile()
	{

  }
  
	function outputError($tmhOAuth) {
				return JError::raiseWarning( 500,$tmhOAuth->response['response']);
		tmhUtilities::pr($tmhOAuth);
	}
	
	

}//end class
