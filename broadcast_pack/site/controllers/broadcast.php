<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.'config.php');

class BroadcastControllerbroadcast extends JController
{
	var $bconfig = '';	
	function display()
	{
		parent::display();
	}

 	function broadcast()
	{
		$db 		= & JFactory::getDBO();
		$this->bconfig = new BroadcastConfig;
		
		$query 		= "SELECT * FROM #__broadcast_queue";
		$db->setQuery($query);
	 	$status 	= $db->loadObjectList();
		
		foreach($status as $stat) 
		{
			$fbquery = "SELECT * FROM #__broadcast_users WHERE user_id = {$stat->userid}";
			$db->setQuery($fbquery);
			$this->uaccess = $db->loadObject();	
			$cnt=$stat->count;
		  	
		  	$updtinterval=strtotime($stat->date)+($stat->flag+1)+$stat->interval;
		   	$curttime=time();
		  		  	
		  	if ($updtinterval<$curttime)
		  	{  
				$this->setTwitterStatus($stat->status);
				$this->setLinkedinStatus($stat->status);
				$this->setFacebookProfileStatus($stat->status);	
				$this->setFacebookPageStatus($stat->status);	

				if($cnt>1)
					$query1="UPDATE #__broadcast_queue SET count=count-1,flag=flag+1 WHERE id={$stat->id}";
				else
					$query1="DELETE FROM #__broadcast_queue where id={$stat->id}";  

				$db->setQuery($query1);
				$db->query();
			}
 	   		  
		} //end foreach
	}
	
	function setTwitterStatus($new_status)
	{
	    // Twitter Update
		try {
			$new_status_twit 	= substr($new_status, 0, 140);	 	
				
			$to = new TwitterOAuth($this->bconfig->twitter_consumer, $this->bconfig->twitter_secret, 
								$this->uaccess->twitter_oauth, $this->uaccess->twitter_secret);
			$params = array('status' => $new_status_twit);
		
			$do_dm = simplexml_load_string($to->OAuthRequest('http://twitter.com/statuses/update.xml', $params, 'POST'));

			BroadcastHelperLog::simpleLog("JStoT\n ");
			BroadcastHelperLog::simpleLog("[Twitter Response]:\n  " . JArrayHelper::toString(get_object_vars($do_dm)));

			$bchepler	= new combroadcastHelper();
			if($bchepler->validate($this->uaccess, 'twitter'))		
				$this->store(trim($new_status2[0]),'twitter');
	   }
		catch (Exception $e) {
			// error log code can be written here
			BroadcastHelperLog::simpleLog("JStoT\n ");
			BroadcastHelperLog::simpleLog("[Twitter Response]:\n  " . JArrayHelper::toString($e));
		}
	}
	
	function setLinkedinStatus($new_status)
	{
		// Linkedin Update
		try {
			$new_status_link 	= substr($new_status, 0, 140);		
			$linkedin 		= new LinkedIn($this->bconfig->linkedin_access, $this->bconfig->linkedin_secret);
			$requestToken	= $linkedin->getRequestToken();
			$linkedin->access_token     =   unserialize($this->uaccess->linkedin_oauth);
			$stat = $linkedin->setStatus($new_status_link);
			
			$bchepler	= new combroadcastHelper();
			if($bchepler->validate($this->uaccess, 'linkedin'))		
				$this->store(trim($new_status2[0]),'linkedin');
				
			BroadcastHelperLog::simpleLog("JStoL\n ");		
			BroadcastHelperLog::simpleLog("[Linkedin Response]:\n  " . JArrayHelper::toString(array($new_status)));
		}
		catch (Exception $e) {
			// error log code can be written here
			BroadcastHelperLog::simpleLog("JStoL\n");		
			BroadcastHelperLog::simpleLog("[Linkedin Response]:\n  " . JArrayHelper::toString($e));
		}
					
	}
	
	function setFacebookPageStatus($new_status)
	{
	///Facebook Page Update			
	 try {
	  $facebook = new Facebook(array(
				  		'appId'  => $this->bconfig->fb_api,
				  		'secret' => $this->bconfig->fb_secret,
				  		'cookie' => true,
						));
		$attachment2 = array( 'access_token' => $this->uaccess->facebook_secret );
		 
		$page = $facebook->api('/me/accounts', 'get', $attachment2); 
		
		foreach($page['data'] as $pagedata)
			{
				$attachment = array(
				'access_token' => $pagedata['access_token'],
				'message'=> $new_status
						);
				$facebook->api('/me/feed','POST', $attachment,$this->uaccess->facbook_uid);
			}
			
			 BroadcastHelperLog::simpleLog("JStoFPG \n  ");			
			 BroadcastHelperLog::simpleLog("[Facebook page Response]:\n  " . JArrayHelper::toString(array($new_status)));
		}
	 catch (Exception $e) {
			// error log code can be written here
			 BroadcastHelperLog::simpleLog("JStoFPG \n");			
			 BroadcastHelperLog::simpleLog("[Facebook page Response]:\n  " . JArrayHelper::toString($e));
		}
	}
	
	function setFacebookProfileStatus($new_status)
	{
	 //Facebook Profile Update
	 try {
	 	$facebook = new Facebook(array(
  		'appId'  => $this->bconfig->fb_api,
  		'secret' => $this->bconfig->fb_secret,
  		'cookie' => true,
		));
		
		$params['method'] = 'users.setStatus';
		$params['status'] = $new_status;
		$params['status_includes_verb'] = true;
		$params['uid'] = $this->uaccess->facbook_uid;
		$params['callback'] = '';
		$result = $facebook->api($params);
		
		BroadcastHelperLog::simpleLog("JStoFPR \n ");		
		BroadcastHelperLog::simpleLog("[Facebook profile Response]:\n  " . JArrayHelper::toString(array($new_status)));
		
	 }
	 catch (Exception $e) {
			// error log code can be written here
			BroadcastHelperLog::simpleLog("JStoFPR \n ");		
			BroadcastHelperLog::simpleLog("[Facebook profile Response]:\n  " . JArrayHelper::toString($e));
		}
	}
	
	function store($data,$type)
	{
		$db   =& JFactory::getDBO();
		$user = JFactory::getUser();		
		$obj  = new StdClass();
		$obj->uid = $user->id;
		$obj->status =$data;
		$obj->type = $type;
		$obj->created_date =date('Y-m-d');
		$db->insertObject('#__broadcast_tmp_activities', $obj);
		
	}
	
}
class BroadcastHelperLog
{
  function simpleLog($comment, $level=1)
  {
        // Include the library dependancies
        jimport('joomla.error.log');
        $my = JFactory::getUser();
        $options = array('format' => "{DATE}\t{TIME}\t{USER}\t{COMMENT}");
        // Create the instance of the log file in case we use it later
        $log = &JLog::getInstance('broadcast.log');
        $log->addEntry(array('comment' => $comment, 'user' => $my->name .'('.$my->id.')'));
  }
}
?>
