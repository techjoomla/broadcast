<?php
/*
	* @package Twitter plugin for TechjoomlaAPI
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

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

$lang =  JFactory::getLanguage();
$lang->load('plug_techjoomlaAPI_twitter', JPATH_ADMINISTRATOR);

class plgTechjoomlaAPIplug_techjoomlaAPI_twitter extends JPlugin
{
	function plgTechjoomlaAPIplug_techjoomlaAPI_twitter(& $subject, $config)
	{

		parent::__construct($subject, $config);
		$this->appKey	= $this->params->get('appKey');
		$this->appSecret	= $this->params->get('appSecret');
		$this->errorlogfile='twitter_error_log.php';
		$this->user = JFactory::getUser();
		$this->db=JFactory::getDBO();
		 $this->twitter = new tmhOAuth(array(
  	'consumer_key'    =>trim($this->appKey),
  	'consumer_secret' => trim($this->appSecret),
		'curl_ssl_verifypeer'   => false
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
		if($this->appKey=='' || $this->appSecret=='' || !in_array($this->_name,$config)) #TODO add condition to check config
		{
			$plug['error_message']=true;
			return $plug;
		}
		$plug['api_used']=$this->_name;
		$plug['message_type']='pm';
		$plug['img_file_name']="twitter.png";
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
		$session->set("['oauth']['twitter']['request']",'');
		$session->set("['oauth']['twitter']['access']",'');
		$session->set("['oauth']['twitter']['contacts']",'');
		$session->set("['oauth']['twitter']['contacts']['session']",'');
	$params = array(
    'oauth_callback' => $callback
  );

  /*if (isset($_REQUEST['force_write'])) :
    $params['x_auth_access_type'] = 'write';
  elseif (isset($_REQUEST['force_read'])) :
    $params['x_auth_access_type'] = 'read';
  endif;
*/
  $code = $this->twitter->request('POST', $this->twitter->url('oauth/request_token', ''), $params);
		if ($code == 200) {

		$oauth = $this->twitter->extract_params($this->twitter->response['response']);
		$session->set("['oauth']['twitter']['request']",$oauth);
		$request_token=$session->get("['oauth']['twitter']['request']");
		$authurl = $this->twitter->url("oauth/authorize", '') .  "?oauth_token=".$request_token['oauth_token'];
		$response=header('Location:'.$authurl);
		$this->raiseLog(JText::_('LOG_GET_REQUEST_TOKEN_SUCCESS'),JText::_('LOG_GET_REQUEST_TOKEN'),$this->user->id,0,$code);

		} else{
		$this->raiseException(JText::_('LOG_GET_REQUEST_TOKEN_FAIL'),$this->user->id,1);
		$this->raiseLog(JText::_(JText::_('LOG_GET_REQUEST_TOKEN_FAIL'),'LOG_GET_REQUEST_TOKEN'),$this->user->id,0,$code);
		return false;
  }

			return true;
	}

	function get_access_token($get,$client='',$callback='')
	{
		$session = JFactory::getSession();
		$request_token=$session->get("['oauth']['twitter']['request']");
		if(isset($get['oauth_verifier'])) {
			$this->twitter->config['user_token']  = $request_token['oauth_token'];
			$this->twitter->config['user_secret'] = $request_token['oauth_token_secret'];

  		$code = $this->twitter->request('POST', $this->twitter->url('oauth/access_token', ''),
  					 array('oauth_verifier' => $get['oauth_verifier']));
			if ($code == 200)
			{

				$response = $this->twitter->extract_params($this->twitter->response['response']);
				$session->set("['oauth']['twitter']['access']",$response);
				$session->set("['oauth']['twitter']['authorized']",true);
				$data = $session->get("['oauth']['twitter']['access']",'');

				$this->store($client,$data);
				$this->raiseLog(JText::_('LOG_GET_ACCESS_TOKEN_SUCCESS'),JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0,$code);
				return true;

			}
			else
			{
					$this->raiseLog(JText::_('LOG_GET_ACCESS_TOKEN_SUCCESS'),JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0,$code);
			}
		}

	}

	function store($client,$data) 	#TODO insert client also in db
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

	function getToken($user='',$client=''){
		$this->removeDeletedUsers();
		$where = '';
		if($user)
			$where = ' AND user_id='.$user;

			if($client)
			$where .= " AND client='".$client."'";

		$query = "SELECT user_id,token
		FROM #__techjoomlaAPI_users
		WHERE token<>'' AND api='{$this->_name}' ".$where ;
		$this->db->setQuery($query);
		return $this->db->loadObjectlist();
	}


	//This is function to remove users from Broadcast which are deleted from joomla
	function removeDeletedUsers()
	{
		$query = "SELECT user_id FROM #__techjoomlaAPI_users";
		$this->db->setQuery($query);
		$brusers=$this->db->loadObjectlist();
		if(!$brusers)
		return;
		foreach($brusers as $bruser)
		{
				$id='';
				$query = "SELECT id FROM #__users WHERE id=".$bruser->user_id;
				$this->db->setQuery($query);
				$id=$this->db->loadResult();
				if(!$id)
				{
					$qry 	= "DELETE FROM #__techjoomlaAPI_users WHERE user_id = {$bruser->user_id} ";
					$this->db->setQuery($qry);
					$this->db->query();

				}



		}

	}

	function remove_token($client)
	{
		if($client!='')
		$where="AND client='{$client}' AND api='{$this->_name}'";

		$qry 	= "UPDATE #__techjoomlaAPI_users SET token='' WHERE user_id = {$this->user->id} ".$where;
		$this->db->setQuery($qry);
		$this->db->query();
	}


	function plug_techjoomlaAPI_twitterget_contacts($offset=0,$limit=99)
	{
		//if(!$limit)
			$limit=99;
		$session = JFactory::getSession();
		$token = $session->get("['oauth']['twitter']['access']",'');
		$tmhOAuth = new tmhOAuth(array(
				'consumer_key'    =>trim($this->appKey),
  			'consumer_secret' => trim($this->appSecret),
				'user_token'      => trim($token['oauth_token']),
				'user_secret'     => trim($token['oauth_token_secret']),
				'curl_ssl_verifypeer'   => false
				));
			$params=array();
			$connection=array();
		if($session->get("['oauth']['twitter']['contacts']['session']")!='1')
		{
			//echo $tmhOAuth->url('1.1/followers/ids').'&count=100';
			$response=$tmhOAuth->request('GET', $tmhOAuth->url('1.1/followers/ids'));
			if ($tmhOAuth->response['code'] == 200) {
		  $data = json_decode($tmhOAuth->response['response'], true);
			$session->set("['oauth']['twitter']['contacts']",$data['ids']);
			$session->set("['oauth']['twitter']['contacts']['session']",'1');
			}
		}
		$contacts=$tot_contacts = $session->get("['oauth']['twitter']['contacts']",'');
		array_splice($contacts,$limit);
		if($contacts)
		{
				//echo $tmhOAuth->url('1.1/users/lookup.json?user_id=1174316172','');
					$status = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/users/lookup'),array('user_id'=>implode(',',$contacts)));
					if($tmhOAuth->response['code'] == 200) {
							$profiles = json_decode($tmhOAuth->response['response'], true);
						$i=0;
						foreach($profiles as $userprofile )
						{
								$connection[$i]['id']=$userprofile['screen_name'];
								$connection[$i]['name']=$userprofile['name'];
								$connection[$i]['picture-url']=$userprofile['profile_image_url_https'];
								$i++;
						}
				}
			$remain = array_slice($tot_contacts, 99);
			$session->set("['oauth']['twitter']['contacts']",$remain);
			$contacts=$this->renderContacts($connection);
			return $contacts;
		}
		else
		return array();
	}

	function renderContacts($emails)
	{

			$count=0;
			$r_connections=array();
			foreach($emails as $connection)
			{
				$r_connections[$count]=new stdClass();
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

	function plug_techjoomlaAPI_twittersend_message($mail,$post)
	{
		require(JPATH_SITE.DS.'components'.DS.'com_invitex'.DS.'helper.php');
		$cominvitexHelper = new cominvitexHelper();
		$invitex_settings	= $cominvitexHelper->getconfigData();


		if($post['invite_type'] > 0){
				$types_res=$cominvitexHelper->types_data($post['invite_type']);
				$template	=	stripslashes($types_res->template_twitter);
			}
			else
				$template	=	stripslashes($invitex_settings['twitter_message_body']);

		$token	=	$post['token'];
		$token=json_decode($token);
		$token=(array)$token;
		$tmhOAuth = new tmhOAuth(array(
				'consumer_key'    =>trim($this->appKey),
				'consumer_secret' => trim($this->appSecret),
				'user_token'      => trim($token['oauth_token']),
				'user_secret'     => trim($token['oauth_token_secret']),
				'curl_ssl_verifypeer'   => false));

			$params=array();
			$connection=array();


			$mail['msg_body']=$template;
			$message	=	$cominvitexHelper->tagreplace($mail);


		 	$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/direct_messages/new'), array('text' => $message,'screen_name'=>$post['invitee_email']));
			if($code==200)
			{
					$return[0] = 1;
					$return[1]= $tmhOAuth->response['response'];
			}
			else
			{
				$this->raiseLog(JText::_('LOG_SEND_MESSAGE_FAIL'),JText::_('LOG_SEND_MESSAGE'),$this->user->id,0,$code.'=>'.$tmhOAuth->response['response']);
				$return[0]= -1;
				$return[1]= $tmhOAuth->response['response'];
			}
			return $return;
  }//end send message


	function plug_techjoomlaAPI_twittergetstatus()
	{
	 $oauth_keys =array();
	 $oauth_keys = $this->getToken('','broadcast');
	 if(!$oauth_keys)
		return false;
		$i=0;
		$returndata=array(array());
		if(empty($oauth_keys))
		return;
	 	foreach($oauth_keys as $oauth_key)
	 	{
	 		if(empty($oauth_key->token))
			continue;
				$token =	json_decode($oauth_key->token,true);
				$tmhOAuth = new tmhOAuth(array(
				'consumer_key'    =>trim($this->appKey),
  			'consumer_secret' => trim($this->appSecret),
				'user_token'      => trim($token['oauth_token']),
				'user_secret'     => trim($token['oauth_token_secret']),
				'curl_ssl_verifypeer'   => false));

				if($this->params->get('broadcast_limit'))
				$twitter_profile_limit=$this->params->get('broadcast_limit');
				else
				$twitter_profile_limit=2;

				$params = array('count'=>$twitter_profile_limit,'user_id'=>$token['user_id'],'include_entities'=>1,'screen_name'=>$token['screen_name']);
				try{
				$tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/user_timeline'),$params);
				}
				catch (Exception $e)
				{
					$response=$this->raiseLog(JText::_('LOG_GET_STATUS_FAIL_TWITTER'),$e->getMessage(),$oauth_key->user_id,1);

				}
				$content=json_decode($tmhOAuth->response['response'],true);

				$data=$this->renderstatus($content);
				if(empty($data))
		 		 continue;
				if($data)
				{
					$returndata[$i]['user_id'] = $oauth_key->user_id;
					$returndata[$i]['status']	 = $data;
					$i++;
					$this->raiseLog(JText::_('LOG_GET_STATUS_SUCCESS'),JText::_('LOG_GET_STATUS'),$oauth_key->user_id,1);
				}
				else
				{

					$this->raiseLog(JText::_('LOG_GET_STATUS_FAIL'),JText::_('LOG_GET_STATUS'),$oauth_key->user_id,1);
				}

		}

		if(!empty($returndata['0']))
		return $returndata;
		else
		return;

	}
	function renderstatus($response)
	{

		if($response)
		{
			if(count($response)>=1)
			{
			$j=0;
			if(empty($response))
			return array();
			foreach($response as $data)
			{
				if($j==10)
				break;
				if(!empty($data['text']))
				{
					if( !($data['source']=='web') and  !empty($data['entities']['urls']))		//for converting the urls t.co into goo.gl
					{
						foreach($data['entities']['urls'] as $url)
						{
							$data['text'] = str_replace($url['url'],$url['expanded_url'],$data['text']);
						}
					}
					$status[$j]['comment'] =  $data['text'];
					$status[$j]['timestamp'] = strtotime($data['created_at']);
					$config =JFactory::getConfig();
					$offset = $config->get('config.offset');
					$get_date=JFactory::getDate($status[$j]['timestamp'],$offset);
					$status[$j]['timestamp'] = strtotime($get_date->format("Y-m-d"));
					$j++;
				}

			}
			return $status;

			}
		}
		else
		return array();

	}

	function plug_techjoomlaAPI_twittersetstatus($userid='',$originalContent,$comment,$attachment='')
	{

		$oauth_key = $this->getToken($userid,'broadcast');

		if(!$oauth_key)
		return false;
		else
		$token =json_decode($oauth_key[0]->token,true);

		$tmhOAuth = new tmhOAuth(array(
		 'consumer_key'    =>trim($this->appKey),
  			'consumer_secret' => trim($this->appSecret),
				'user_token'      => trim($token['oauth_token']),
				'user_secret'     => trim($token['oauth_token_secret']),
				'curl_ssl_verifypeer'   => false));
			$method = "https://userstream.twitter.com/2/user.json";
			$params = array(
				// parameters go here
			);
		/*   $twitter = $twitteroauth->post('statuses/update_with_media.json', array(
           'status' => $message ,
           '@media[]' => "@{$image}"
            ));

			if($attachment)
				$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update_with_media'), array('status' => $comment,'@media[]' => "@{$attachment}"));
			else
			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array('status' => $comment));
			*/

			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array('status' => $originalContent));
			if($code=200)
			{
					$response=$this->raiseLog(JText::_('LOG_SET_STATUS_SUCCESS')."=>".$originalContent,JText::_('LOG_SET_STATUS'),$userid,1,200);
					return true;
			}
			else
			{
				$response=$this->raiseLog(JText::_('LOG_SET_STATUS_FAIL')."=>".$originalContent,JText::_('LOG_SET_STATUS'),$userid,1,$code);
				return false;

			}


	}

function raiseException($exception,$userid='',$display=1,$params=array())
	{
		$path="";
		$params['name']=$this->_name;
		$params['group']=$this->_type;
		$loghelperobj=	new techjoomlaHelperLogs();
		$loghelperobj->simpleLog($exception,$userid,'plugin',$this->errorlogfile,$path,$display,$params);
		return;
	}

	function raiseLog($status_log,$desc="",$userid="",$display="",$http_code="")
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
						if(isset($status['twitter']))
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

		if($http_code)
		$params['http_code']	=$http_code;
		$this->raiseException($status_log,$userid,$display,$params);
		return true;
	}

	function plug_techjoomlaAPI_twitterget_profile($integr_with,$client,$callback)
	{
		$session = JFactory::getSession();
		$mapData[0]		=& $this->params->get('mapping_field_0');	//joomla
		$mapData[1]		=& $this->params->get('mapping_field_1'); //jomsocial
		$mapData[2]		=& $this->params->get('mapping_field_2'); //cb

		$token = $session->get("['oauth']['twitter']['access']",'');
		$tmhOAuth = new tmhOAuth(array(
				'consumer_key'    =>trim($this->appKey),
  			'consumer_secret' => trim($this->appSecret),
				'user_token'      => trim($token['oauth_token']),
				'user_secret'     => trim($token['oauth_token_secret']),
				'curl_ssl_verifypeer'   => false));

			$params=array();
			$connection=array();

		$oauth_key = $this->getToken($this->user->id,'profileimport');
		if(!$oauth_key)
		return false;
		else
		$token =json_decode($oauth_key[0]->token,true);
   	$params = array('user_id'=>$token['user_id'],'screen_name'=>$token['screen_name']);
  	$data = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/users/show'),$params);
  	$profileData=json_decode($tmhOAuth->response['response'],true);

		if($profileData)
		{
			$profileDetails['profileData']=$profileData;
			$profileDetails['mapData']=$mapData;
			return $profileDetails;
		}


  }
}//end class
