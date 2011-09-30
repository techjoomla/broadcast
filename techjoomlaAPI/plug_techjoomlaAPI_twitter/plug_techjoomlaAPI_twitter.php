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
	//Helper class to write log file//
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');
	
class plgTechjoomlaAPIplug_techjoomlaAPI_twitter extends JPlugin
{ 
	function plgTechjoomlaAPIplug_techjoomlaAPI_twitter(& $subject, $config)
	{
		
		parent::__construct($subject, $config);
		$this->appKey	=& $this->params->get('appKey');
		$this->appSecret	=& $this->params->get('appSecret');
		 $this->twitter = new tmhOAuth(array(
  'consumer_key'    => $this->appKey,
  'consumer_secret' => $this->appSecret,
		));
			

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
		if($this->appKey=='' || $this->appSecret=='')
		{
			$plug['error_message']=true;		
			return $plug;
		}		
		$plug['api_used']=$this->_name; 
		$plug['message_type']='pm';               
		$plug['img_file_name']="twitter.png"; 
		//dipti
		$plug['apistatus'] = $this->status();
		//eoc            
		return $plug; 
	}
	
	function status(){
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$query = "SELECT 	twitter_oauth,twitter_secret FROM #__broadcast_users WHERE user_id = {$user->id}";
		$db->setQuery($query);
		$uaccess = $db->loadObject();
		if ($uaccess->facbook_uid && $uaccess->twitter_secret)
			return 1;
		else
			return 0;
	}
	
	
	function get_request_token($callback='') 
	{
	$params = array('oauth_callback'=> $callback);
	//echo $callback;
 echo  $code = $this->twitter->request('POST', $this->twitter->url('oauth/request_token', ''), $params);
  if ($code == 200) {
    $_SESSION['oauth'] = $this->twitter->extract_params($this->twitter->response['response']);
    $authurl = $this->twitter->url("oauth/authorize", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
  } else {
 
 	$this->outputError($this->twitter);
  }

			return true;
	}
	
	function get_access_token($get,$client='',$callback='') 
	{
			
			if(isset($get['oauth_verifier'])) {
			$this->twitter->config['user_token']  = $_SESSION['oauth']['oauth_token'];
			$this->twitter->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  		$code = $this->twitter->request('POST', $this->twitter->url('oauth/access_token', ''), array(
    'oauth_verifier' => $get['oauth_verifier']
  ));
	if ($code == 200) {
		$_SESSION['access_token'] = $this->twitter->extract_params($this->twitter->response['response']);
		unset($_SESSION['oauth']);
		header("Location: {$here}");
	} else {
		$this->outputError($code);
	}
	print_r($_SESSION['access_token']);die;
// start the OAuth dance
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
		$qry 	= "UPDATE #__broadcast_users SET twitter_oauth='',twitter_secret='' WHERE user_id = {$user->id}";//twitter_oauth,twitter_secret
		$db->setQuery($qry);	
		$db->query();
	}
	
	function get_profile()
	{

  }
          
	function get_contacts() 
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
	
	function send_message($post)
	{
	
  }//end send message
  
  
	function getstatus($oauth_key)
	{ 
	 
	/*$token=Array
	(
		  ['oauth_token'] => '227916664-yanJsIHVPWMhecwWxPHXUPOkXCtGvWBzzLhN0Gci'
		  ['oauth_token_secret'] => 'PShd1v3XOmhnt3FliP2opg3lWM4PDhBfhIdFKodWQ'
		  ['user_id'] => '227916664'
		  ['screen_name'] => 'sagarchinchavad'
	);*/

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

	function setstatus($oauth_key,$content='')
	{	
		$post=array();
		if(!$content)
		return array();
		$access_token =$oauth_key->twitter_secret;	
		try {
		$post = $this->twitter->api($oauth_key->facbook_uid.'/feed', 'POST', array('access_token'=>$oauth_key->twitter_secret,'message' => $content));
		}
		catch(TwitterApiException $o ){
    print_r($o);
	}
	
	
	}

function raiseException($exception)
	{
		$params=array(
		'name'=>$this->_name,
		'group'=>$this->_type,	
		);	
		techjoomlaHelperLogs::simpleLog($exception,'plugin',$this->errorlogfile,$path='',$display=1,$params);
		return;
	}
function outputError($tmhOAuth) {
			return JError::raiseWarning( 500,$tmhOAuth->response['response']);
  tmhUtilities::pr($tmhOAuth);
}
	
	

}//end class
