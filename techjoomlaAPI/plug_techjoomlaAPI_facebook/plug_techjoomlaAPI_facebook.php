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

$lang = & JFactory::getLanguage();
$lang->load('plug_techjoomlaAPI_facebook', JPATH_ADMINISTRATOR);
	
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
		if($result)
		{	
		$uaccess=json_decode($result);		
		if ($uaccess->facebook_uid && $uaccess->facebook_secret)
		{
			//Check if token is valid if not then remove token from database and return false
			$validtoken=$this->isAccessTokenValid($uaccess->facebook_secret);

			if(!$validtoken)
			{
				$this->remove_token('broadcast',$this->user->id);
				return 0;
				
			}
			return 1;
			
		}	
		else
			return 0;
		}
		else
		return 0;
	}
	
	
	function get_request_token($callback) 
	{
		
		$this->callbackUrl=$callback;
		$params = array(
							'redirect_uri' => $callback,
							'scope' =>'email,read_stream,user_status,publish_stream,offline_access,manage_pages,user_groups', 
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
			$return=$this->raiseLog($user,JText::_('LOG_GET_REQUEST_TOKEN'),$this->user->id,0);
			
		
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
		$return=$this->raiseLog($data,JText::_('LOG_GET_ACCESS_TOKEN'),$this->user->id,0); 
		$this->store($client,$data);		
		return true;
		
	}
	
	function isAccessTokenValid($accesstoken)
	{
	  // Attempt to query the graph:
  	$graph_url = "https://graph.facebook.com/me?access_token=" . $accesstoken;
  	$response = $this->curl_get_file_contents($graph_url);
  	$decoded_response = json_decode($response); 
 	 	//Check for errors 
  	if (!empty($decoded_response->error)) {
  	// check to see if this is an oAuth error:
    if ($decoded_response->error->type== "OAuthException") {
    		//$sendmail=@techjoomlaHelperLogs::emailtoClient('ACCESS_TOKEN_EXPIRE','Facebook');
    		return false;
    
    	}
    	return false;
    }
    return true;
  }
    
  // note this wrapper function exists in order to circumvent PHP’s 
  //strict obeying of HTTP error codes.  In this case, Facebook 
  //returns error code 400 which PHP obeys and wipes out 
  //the response.
  function curl_get_file_contents($URL) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
    curl_close($c);
    if ($contents) return $contents;
    else return FALSE;
  }


	function store($client,$data) 
	{
		$qry 	= "DELETE FROM #__techjoomlaAPI_users WHERE user_id ={$this->user->id} AND client='{$client}'	AND api='{$this->_name}'";
		$this->db->setQuery($qry);	
		$this->db->query();
		
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
	
		//Delete Entries Where token is blank
		$qry 	= "DELETE FROM #__techjoomlaAPI_users WHERE  token=''";
		$this->db->setQuery($qry);	
		$this->db->query();
		//Delete Entries Where token is blank
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
	
	function remove_token($client,$userid='')
	{ 
	if(empty($userid))
	$userid=$this->user->id;
		if($client!='')
		$where="AND client='{$client}' AND api='{$this->_name}'";
		
		#TODO add condition for client also
		$qry 	= "UPDATE #__techjoomlaAPI_users SET token='' WHERE user_id = {$userid} ".$where;
		$this->db->setQuery($qry);	
		$this->db->query();
	}
	
	        
	function plug_techjoomlaAPI_facebookget_contacts() 
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
			if(count($contacts)==0)
			{
				$this->raiseException(JText::_('NO_CONTACTS'));
				$this->raiseLog(JText::_('NO_CONTACTS'),JText::_('LOG_GET_CONTACTS'),$this->user->id,0);
			}
			else
			
			$this->raiseLog(JText::_('CONTACTS_FOUND'),JText::_('LOG_GET_CONTACTS'),$this->user->id,0);
		
		return $contacts;
		
	}
	
	function renderContacts($emails)
	{
			
			$count=0;
			foreach($emails as $connection)
			{
				if($connection['id'])	
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
			}
		return $r_connections;
	}
	
	function plug_techjoomlaAPI_facebooksend_message($raw_mail,$invitee_data)
	{	
		require(JPATH_SITE.DS.'components'.DS.'com_invitex'.DS.'config.php');
		$session = JFactory::getSession();	
		foreach($invitee_data as $id=>$invitee_name)
		{
				$invitee_email[]	= "'".$invitee_name.'|'.$id."'";
				$inviteid[]=$id;	
		}
	
		$inviteeidstr=implode(',',$inviteid);
		$userid=md5($this->user->id);
		$regurl= cominvitexHelper::getinviteURL();
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
		
		$subject	= $invitex_settings['pm_message_body_no_replace_sub'];
		$subject	=	str_replace("[SITENAME]", $raw_mail['sitename'], $subject);
		$parameters = array(
		'app_id' => $this->facebook->getAppId(),

		'link' => $regurl,
		'redirect_uri' => JURI::base()."index.php?option=com_invitex&view=invites&fb_redirect=success",
		'name'=>$subject,
		'description'=>$message,
		'to' => $inviteeidstr
 		);
 		
		$url = 'http://www.facebook.com/dialog/send?'.http_build_query($parameters);
		header('Location:'.$url);	
		die;
	
  }//end send message
  
  
  function plug_techjoomlaAPI_facebookgetstatus()
	{ 
		$oauth_keys =array();
	 	$oauth_keys = $this->getToken('','broadcast');
	 	$returndata=array(array());
	 	$i = 0;
	 	if($this->params->get('broadcast_limit'))
	 	$facebook_profile_limit=$this->params->get('broadcast_limit');
	 	else
	 	$facebook_profile_limit=10;
		$returndata = array();
		if(!$oauth_keys)
		return false;
	 	foreach($oauth_keys as $oauth_key){	
	 		
			$token =json_decode($oauth_key->token);	
			//Check if token is valid if not then remove token from database
			$validtoken=$this->isAccessTokenValid($token->facebook_secret);

			if(!$validtoken)
			{
				$this->remove_token('broadcast',$oauth_key->user_id);
				$response=$this->raiseLog(JText::_('LOG_GET_STATUS_FAIL_FACEBOOK'),'Not Valid Access Token',$oauth_key->user_id,1);
				continue;
			
			}
			try{		
				$json_facebook = $this->facebook->api($token->facebook_uid.'/statuses',array('access_token'=>$token->facebook_secret,'limit'=>$facebook_profile_limit));
				if($this->params->get('pages')==1)			
				$json_pagedata=$this->plug_techjoomlaAPI_facebookget_page_status($token,$oauth_key->user_id,$facebook_profile_limit);
				if($this->params->get('groups')==1)
				$json_groupdata=$this->plug_techjoomlaAPI_facebookget_group_status($token,$oauth_key->user_id,$facebook_profile_limit);

			}
			catch (FacebookApiException $e) 
			{
					$response=$this->raiseLog(JText::_('LOG_GET_STATUS_FAIL_FACEBOOK'),$e->getMessage(),$oauth_key->user_id,1);

		  }
		  
		  $status=$this->renderstatus($json_facebook['data'])	;

		 
		  if(!empty($json_pagedata))
		  {
		  	foreach($json_pagedata as $pgdts)
		  	{
			  	foreach($pgdts as $pgdt)			  	
		  		$status[]=$pgdt;
		  	
		  	}
		  }

		  if(!empty($json_groupdata))
		  {
		  	foreach($json_groupdata as $pgdts)
		  	{
			  	foreach($pgdts as $pgdt)			  	
		  		$status[]=$pgdt;
		  	
		  	}
		  }


		  if(empty($status))
		  continue;
		  if($status)
			{
				$returndata[$i]['user_id'] 	= $oauth_key->user_id;
				$returndata[$i]['status'] 	= $status;	

				$response=$this->raiseLog(JText::_('LOG_GET_STATUS_SUCCESS'),JText::_('LOG_GET_STATUS'),$oauth_key->user_id,1);
			}
			else
			{
				
				$response=$this->raiseLog(JText::_('LOG_GET_STATUS_FAIL'),JText::_('LOG_GET_STATUS'),$oauth_key->user_id,0);
			}
			
			$i++;
		}

		if(!empty($returndata['0']))
		return $returndata;
		else
		return;
		
	}
			

	function renderstatus($totalresponse)
	{	
		$status = array();
	 	$j=0;
		for($i=0; $i <= count($totalresponse); $i++ )
		{			
				if(isset($totalresponse[$i]['message']))
				{
					$status[$j]['comment'] =  $totalresponse[$i]['message'];
					$status[$j]['timestamp'] = strtotime($totalresponse[$i]['updated_time']);
					$j++;
				}
		  }
		return $status;
	}

	function plug_techjoomlaAPI_facebooksetstatus($userid,$content='')
	{
	
		$oauth_key = $this->getToken($userid,'broadcast');
		
		if(!$oauth_key)
		return false;
		else
		$token =json_decode($oauth_key[0]->token);	
		//Check if token is valid if not then remove token from database
		$validtoken=$this->isAccessTokenValid($token->facebook_secret);
		if(!$validtoken)
		{
			$this->remove_token('broadcast',$oauth_key[0]->user_id);
			return false;
			
		}

		$post=array();
		if(!$content)
		return array();
		
		try{
		if(isset($token))
		{
		$post = $this->facebook->api($token->facebook_uid.'/feed', 'POST', array('access_token'=>$token->facebook_secret,'message' => $content));
		if($this->params->get('pages')==1)
		$this->plug_techjoomlaAPI_facebookset_page_status($token,$oauth_key[0]->user_id,$content);
		if($this->params->get('groups')==1)
		$this->plug_techjoomlaAPI_facebookset_group_status($token,$oauth_key[0]->user_id,$content);
		}
		} 
		catch (FacebookApiException $e) 
		{
			$response=$this->raiseLog(JText::_('LOG_SET_STATUS_FAIL').JText::_('LOG_SET_STATUS'),$e->getMessage(),$userid,1);
		  return false;
    }
		if($post)
			$response=$this->raiseLog(JText::_('LOG_SET_STATUS_SUCCESS').JText::_('LOG_SET_STATUS'),$content,$userid,1);
		else
			$response=$this->raiseLog(JText::_('LOG_SET_STATUS_FAIL').JText::_('LOG_SET_STATUS'),$e->getMessage(),$userid,1);
			
		return $response;
	
	}
	
	
	
  function get_otherAccountData()
	{
			$data='';
			
			//	echo $this->params->get('pages');
				//echo "===========";
					//			echo $this->params->get('groups');
				$session = JFactory::getSession();	
				if($this->params->get('pages')==1)
				{
				$pagedata=$this->plug_techjoomlaAPI_facebookgetpagedata();

					$fbpagesessiondata='';
					$i=0;
					$column='facebook_page_update';
					if($pagedata)
					{
						foreach($pagedata as $fbpage)
						{
							if($fbpage['category']=='Application')
							continue;
						
							$checkexist=combroadcastHelper::checkexistparams($fbpage['id'],$this->user->id,$this->_name,$column='facebook_page_update');
						
							$fbpage['image']='http://graph.facebook.com/'.$fbpage['id'].'/picture';
							if($checkexist)
							$fbpage['connectionstatus']=1;
							else
							$fbpage['connectionstatus']=0;
							$fbpage['displayname']='Your Facebook Pages';
							$fbpage['fieldname']='facebook_page_update';
						
							$fbpage['techjoomlaapiname']='facebook';
							$data['data'][$column][]=$fbpage;

							$i++;
						}
					}
					
					}

			if($this->params->get('groups')==1)
			{
			$groupdata=$this->plug_techjoomlaAPI_facebookgetgroupdata();
			$i=0;
			$column='facebook_group_update';
			if($groupdata)
					{
						foreach($groupdata as $group)
						{

							$checkexist=combroadcastHelper::checkexistparams($group['gid'],$this->user->id,$this->_name,$column='facebook_group_update');
							$group['id']=$group['gid'];
							$group['image']=$group['icon'];
							$group['name']=$group['name'];
							if($checkexist)
							$group['connectionstatus']=1;
							else
							$group['connectionstatus']=0;
						
							$group['fieldname']='facebook_group_update';
							$group['displayname']='Your Facebook Groups';
							$group['techjoomlaapiname']='facebook';
							$data['data'][$column][]=$group;

							$i++;
						}
					}
			

		
		
		}
	//	print_r($data);die;

				return $data;
		

	}
	
	function plug_techjoomlaAPI_facebookgetgroupdata()
	{
	$groupData='';
		$oauth_keys=$this->getToken($this->user->id,'broadcast');
	 	foreach($oauth_keys as $oauth_key){	
	 		$token =json_decode($oauth_key->token);	
			try{			
				$fql    =   "select  gid from group_member where uid=".$token->facebook_uid;
				$param  =   array(
       'method'     => 'fql.query',
        'query'     => $fql,
      'callback'    => '');
			$groupids   =   $this->facebook->api($param);



			}
			catch (FacebookApiException $e) 
			{
				$response=$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
				return false;
			}
		}
		if(!empty($groupids))
		{
			foreach($groupids as $gid)
			{

				try{			
					$fql    =   "select  gid,icon,creator,name from group where gid=".$gid['gid'];
					$param  =   array(
		     'method'     => 'fql.query',
		      'query'     => $fql,
		    	'callback'    => '');
					$grdata   =   $this->facebook->api($param);
					$groupData[]=$grdata[0];

				}
				catch (FacebookApiException $e) 
				{

					$response=$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
				}
				
	
		
			}
		}
		


		return $groupData;
		
	}
	
  function plug_techjoomlaAPI_facebookgetpagedata()
  {
  	$pageData='';
  	$oauth_keys=$this->getToken($this->user->id,'broadcast');
	 	foreach($oauth_keys as $oauth_key){	
	 		$token =json_decode($oauth_key->token);	
			try{			
			$pageData= $this->facebook->api($token->facebook_uid.'/accounts','GET', array('access_token'=>$token->facebook_secret));

			}
			catch (FacebookApiException $e) 
			{
				$response=$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
				return false;
			}
		}
		if(!empty($pageData))
		return $pageData['data'];
		
  }
  		
	function	plug_techjoomlaAPI_facebookset_group_status($token,$userid,$content)
	{
			

				$groupData='';
				$fql    =   "select  gid from group_member where uid=".$token->facebook_uid;
				$param  =   array(
       'method'     => 'fql.query',
       'query'     => $fql,
	    'callback'    => '');
	    	try{
					$groupids   =   $this->facebook->api($param);
				}
				catch (FacebookApiException $e) 
				{
					$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
				}
		
				foreach($groupids as $grp)
				{
						$checkexist=0;
						$checkexist=combroadcastHelper::checkexistparams($grp['gid'],$userid,$api='plug_techjoomlaAPI_facebook',$column='facebook_group_update');
					if(!$checkexist)
					continue;
					try{
							
							$post = $this->facebook->api($grp['gid'].'/feed', 'POST', array('access_token'=>$token->facebook_secret,'message' => $content));

						}
						catch (FacebookApiException $e) 
						{
							$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
						}
				}


	}
	
	function	plug_techjoomlaAPI_facebookget_group_status($token,$userid,$facebook_profile_limit)
	{
			

			$groupData=$groupids='';
				$fql    =   "select  gid from group_member where uid=".$token->facebook_uid;
				$param  =   array(
       'method'     => 'fql.query',
       'query'     => $fql,
	    'callback'    => '');
	    	try{
					$groupids   =   $this->facebook->api($param);

					
				}
				catch (FacebookApiException $e) 
				{
					//$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
				}

				$statuses='';
				foreach($groupids as $grp)
				{
						$checkexist=0;
					$checkexist=combroadcastHelper::checkexistparams($grp['gid'],$userid,$api='plug_techjoomlaAPI_facebook',$column='facebook_group_update');
					if(!$checkexist)
					continue;
					try{
								$response = $this->facebook->api($grp['gid'].'/feed', 'GET', array('access_token'=>$token->facebook_secret,'limit'=>$facebook_profile_limit,));

							if(!empty($response))
						  $statuses[]=$this->renderstatus($response['data']);

						}
					catch (FacebookApiException $e) 
						{

							//$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
						}
						

				}


			if(!empty($statuses))
				{

					
					return $statuses;
				
				}
				else
				{
									return '';
				}

	}
	

	function plug_techjoomlaAPI_facebookset_page_status($token,$userid,$content)
  {
			if($this->params->get('pages')!=1)
			return;
							
				$pageData= $this->facebook->api($token->facebook_uid.'/accounts','GET', array('access_token'=>$token->facebook_secret));
				if($pageData)
				{
					foreach($pageData as $pages)
					{
						foreach($pages as $page)
						{

						$checkexist='';
						$checkexist=combroadcastHelper::checkexistparams($page['id'],$userid,$api='plug_techjoomlaAPI_facebook',$column='facebook_page_update');
							if($checkexist)
							{
								$attachment = array(
								'access_token' => $page['access_token'],
								'message'=> $content,
								);
								
								try{	
								$response=$this->facebook->api($page['id']."/feed",'POST', $attachment);
								}
								catch (FacebookApiException $e) 
								{
									$response=$this->raiseLog(JText::_('LOG_SET_PAGE_STATUS').JText::_('LOG_SET_PAGE_STATUS'),$e->getMessage(),$userid,1);

								}

					
					}
					}
				}

				}
				
	}
	
	function plug_techjoomlaAPI_facebookget_page_status($token,$userid,$facebook_profile_limit)
  {
  		
  		if($this->params->get('pages')!=1)
			return;
				$pageData= $this->facebook->api($token->facebook_uid.'/accounts','GET', array('access_token'=>$token->facebook_secret));

				if($pageData['data'])
				{
					foreach($pageData['data'] as $page)
					{
						$checkexist='';
						$checkexist=combroadcastHelper::checkexistparams($page['id'],$userid,$api='plug_techjoomlaAPI_facebook',$column='facebook_page_update');
						if($checkexist)
						{
							$attachment = array(
							'access_token' => $page['access_token'],
							'limit'=>$facebook_profile_limit,
							);
							try{
								$response='';
								$response=$this->facebook->api($page['id']."/feed",'GET', $attachment);
							}
							catch (FacebookApiException $e) 
							{
								$response=$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
								//return false;
							}
							if(!empty($response))
						  $statuses[]=$this->renderstatus($response['data']);

						}
					}

				}

				if(!empty($statuses))
				{
					
					return $statuses;
				
				}
				else
				{
									return '';
				}


 
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
	
	function plug_techjoomlaAPI_facebookget_profile($integr_with,$client,$callback)
	{
			
			$mapData[0]		=& $this->params->get('mapping_field_0');	//joomla		
			$mapData[1]		=& $this->params->get('mapping_field_1'); //jomsocial
			$mapData[2]		=& $this->params->get('mapping_field_2'); //cb
			
		try{			
			$profileData= $this->facebook->api('/me');
			$profileData['picture-url']='https://graph.facebook.com/'.$profileData['id'].'/picture';
		} 
		catch (FacebookApiException $e) 
		{
			$response=$this->raiseLog(JText::_('LOG_GET_PROFILE_FAIL').JText::_('LOG_GET_PROFILE'),$e->getMessage(),$userid,1);
			return false;
		}

		if($profileData)
		{
			$profileDetails['profileData']=$profileData;	
			$profileDetails['mapData']=$mapData;
			return $profileDetails;
		}
			
  }

}//end class
