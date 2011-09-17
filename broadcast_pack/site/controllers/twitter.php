<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.'config.php');
$bconfig = new BroadcastConfig;
//require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.$bconfig->twitter_library_path);


require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib_new'.DS.'linkedin_twitter'.DS.'twitteroauth.php');


class BroadcastControllertwitter extends JController
{
	
	function BroadcastControllerlinkedin()
	{
		parent::__construct();
		$bconfig = new BroadcastConfig;
		$this->bconfig = $bconfig;		
	}
	function authorise2()
	{
		try{

$connection = new TwitterOAuth($this->bconfig->twitter_consumer, $this->bconfig->twitter_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$tok= $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $tok;

	$data['twitter_oauth'] 	= $tok['oauth_token'];
			$data['twitter_secret']	= $tok['oauth_token_secret'];
			$model =	$this->getModel('twitter');
			$model->store($data);

		/*	$bconfig = new BroadcastConfig;
			$to = new TwitterOAuth($this->bconfig->twitter_consumer, 
			$bconfig->twitter_secret, $_SESSION['oauth_request_token'],$_SESSION['oauth_request_token_secret']);
			$tok = $to->getAccessToken();
			print_r($tok);die;
			$data['twitter_oauth'] 	= $tok['oauth_token'];
			$data['twitter_secret']	= $tok['oauth_token_secret'];
			$model =	$this->getModel('twitter');
			$model->store($data);
*/
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
 echo $_SESSION['status'] = 'verified';

} 
		}catch(Exception $e){error_log($e);}
		
		$this->redirect();
		
	}
	function remove()
	{
		$model =	$this->getModel('twitter');
		$model->remove();
		$this->redirect();
	
	}
	function getstatus()
	{
		$mylogobj	= new BroadcastHelperLogs();
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$broadcast_config['private_key_cronjob'])		
		{
			echo "This Private Cron Key Doesnot Exist";
			return;
		}

		$database = JFactory::getDBO();
		$fbquery = "SELECT user_id,twitter_oauth,twitter_secret FROM #__broadcast_users where twitter_oauth<>'' AND twitter_secret<>''";		
		$database->setQuery($fbquery);
		$this->uaccess = $database->loadObjectlist();
		$pkey = JRequest::getVar('pkey', '');
		$log = array();
		
	if($broadcast_config['twitter'])
	{		
	
	$twitter_limit_cron=$broadcast_config['twitter_limit'];
	if(!$twitter_limit_cron)
	$twitter_limit_cron='10';
		foreach($this->uaccess as $k=>$v){
		$log[] = 'User: ' . JFactory::getUser($v->user_id)->name.' ';
		$userid = $v->user_id;
		try{
				$to = new TwitterOAuth($this->bconfig->twitter_consumer, $this->bconfig->twitter_secret, 
				    				$v->twitter_oauth, $v->twitter_secret);
				$data 		= array();
				$statusdata	= array();
				$data     	= simplexml_load_string($to->OAuthRequest("http://api.twitter.com/1/account/verify_credentials.xml", '', 'GET'));
				$screen	  	= $data->screen_name;
				//Check twitter limit
				$twitter_limit	= "http://api.twitter.com/1/account/rate_limit_status.json";
				$buffer_limit	= @file_get_contents($twitter_limit);		
				$obj	= json_decode($buffer_limit);		
				//End twitter limit

				if($obj->remaining_hits>1)
				{
					
					$twitter_url = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=".$screen."&count=".$twitter_limit_cron."&trim_user=true";
					$buffer = json_decode(@file_get_contents($twitter_url));	
					if(!$buffer)	continue;
						$i=0;
						$currrent_time	=time();
						$one_hr_before	=time()-(60*60);
				
						while($buffer)
						{
				
							$status_item 	= $buffer[$i];		
							if($status_item)
							{												
								$status[$i]['status'] 		=  $status_item->text;						
								$status[$i]['created_at']	=  strtotime($status_item->created_at);				
								if($status[$i]['created_at']>$one_hr_before)
								{
									$log[] = 'Status: ' . $status[$i]['status'];
									
									//echo implode('<br />', $log);
									$k=0;
									$statusdata[$k]['actor']			= $userid;
									$statusdata[$k]['target']			= $userid;
									$statusup="";
									$actor="";
									
									if($broadcast_config['status_skip'])
									{
										$search=explode(',', $broadcast_config['status_skip']);
										$statusup=str_replace($search, '', $status[$i]['status']);
									}
									else
									{
										$statusup=$status[$i]['status'];
									}
									
												
									
									if($broadcast_config['show_name'])
									{			
										$actor='{actor} ';
									}
									else
									{
										$actor='';
									}
									
									if($broadcast_config['status_via'])
									{
										$statusdata[$k]['title']			= $actor.$statusup.' (via Twitter)';
									}
									else
									{
									    $statusdata[$k]['title']			= $actor.$statusup.' <img src='.JURI::base().'modules/mod_jomsocialbroadcast/images/twitter.jpeg height=19>';
									}
									$statusdata[$k]['content']			= '';
									$statusdata[$k]['app']				= 'profile';
									$statusdata[$k]['cid']				= $userid;
									$statusdata[$k]['created']			= $status[$i]['created_at'];		
									$statusdata[$k]['access']			= 0;
									$statusdata[$k]['params']			= '';
									$statusdata[$k]['points']			= 1;
									$statusdata[$k]['status']			= $status[$i]['status'];						
									$model =$this->getModel('twitter');
									
									$model->twitstore($statusdata);						
									$k++;

								}
								$i++;			
							}
							else
							break;			
						}		
					}//end of limit 	
				}catch(Exception $e){
				$log[]	= $e;
				error_log($e);}
			}//for each
						
			echo implode('<br />', $log);
			$mylogobj->simpleLog("TtoJS\n ");
			$mylogobj->simpleLog(" [ggTwitter Response]:\n  " . JArrayHelper::toString($log));
		}
			
		$model =	$this->getModel('twitter');
		$model->twitdeletetmpactivity();
		die;	
	}
	
	function redirect()
	{	
		$data=combroadcastHelper::getInfo();
		
		$user 	= JFactory::getUser();
		$msg="";
		$session =& JFactory::getSession();
		if($session->get('statustwitterlog') != $data['status_twitter'])
		{
		$session->set('statustwitterlog', $data['status_twitter']); 
		global $mainframe;		
		$mainframe =& JFactory::getApplication();  
		$sitename=$mainframe->getCfg('sitename');
		if($data['status_twitter'])
			$msg=$user->name." ".JText::_('T_CONN_MSG')." ".$sitename;		
		}

	    $mainframe	= JFactory::getApplication();
		$currentMenu = $session->get('currentMenu');
		$mainframe->redirect($currentMenu, $msg);
	}
	
	function getRemotedata($URL)
	{
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $URL); 
		$data = curl_exec($ch); 
		curl_close($ch);
		return $data;
	}
}//class
