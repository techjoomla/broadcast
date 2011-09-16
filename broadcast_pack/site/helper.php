<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.'config.php');
$bconfig = new BroadcastConfig;   // please remove and check all functionality
require_once  JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.$bconfig->twitter_library_path;
require_once  JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.$bconfig->facebook_library_path;
require_once  JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.$bconfig->linkedin_library_path;

class combroadcastHelper
{ 	
	// this function is called from linkedin / twitter / facebook controllers
    function getInfo()
    {
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$bconfig = new BroadcastConfig;

		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$fbquery = "SELECT * FROM #__broadcast_users WHERE user_id = {$user->id}";
		$db->setQuery($fbquery);
		$uaccess = $db->loadObject();		

		//////////////////////Facebook Connection//////////////
		if($broadcast_config['facebook_page'] or $broadcast_config['facebook_profile'])
		{	
			$facebook = new Facebook(array(
				'appId'  => $bconfig->fb_api,
				'secret' => $bconfig->fb_secret,
				'cookie' => true,
			));

			$loginUrl_fb='';
			if (combroadcastHelper::validate($uaccess, 'facebook')) 
				$status_fb = 1;
			else 
				$status_fb = 0;
				
				$remove_link_fb ='';

			if ($status_fb) 
				$remove_link_fb = JRoute::_('index.php?option=com_broadcast&controller=facebook&task=remove');
			else 
			{
					if($broadcast_config['facebook_page'])
						$loginUrl_fb = $facebook->getLoginUrl( array('req_perms' => 'offline_access,publish_stream,user_status,status_update,manage_pages'),$bconfig->callback_url_facebook);
					else
						$loginUrl_fb = $facebook->getLoginUrl( array('req_perms' =>'offline_access,publish_stream,user_status,status_update'),$bconfig->callback_url_facebook);				
			}
				$data['status_fb']		= $status_fb;		
				$data['remove_link_fb']	= $remove_link_fb;
				$data['loginUrl_fb']	= $loginUrl_fb;
		}
		//////////////////////////////////////facebook End//////////////////////////////////////
	
	
		/////////////////////////////////////Twitter start//////////////////////////////////////
		if($broadcast_config['twitter'])
		{	
			$request_link_twitter ='';
			if (combroadcastHelper::validate($uaccess, 'twitter'))
				$status_twitter = 1;
			else
			{
				$status_twitter = 0;		
				$to = new TwitterOAuth($bconfig->twitter_consumer, $bconfig->twitter_secret);
				$tok = $to->getRequestToken();
				$request_link_twitter = $to->getAuthorizeURL($tok);	  	
				$_SESSION['oauth_request_token'] = $token = $tok['oauth_token'];
				$_SESSION['oauth_request_token_secret'] = $tok['oauth_token_secret'];		  	
			}

			$remove_link_twitter='';
			if ($status_twitter) {
				$remove_link_twitter = JRoute::_('index.php?option=com_broadcast&controller=twitter&task=remove');		
			}  
			$data['status_twitter']			= $status_twitter;		
			$data['request_link_twitter']	= $request_link_twitter;
			$data['remove_link_twitter']	= $remove_link_twitter;
	
		}		
		/////////////////////////////////////Twitter End////////////////////////////////////////
	
		/////////////////////////////////////Linkedin start//////////////////////////////////////
		if($broadcast_config['linkedin'])
		{		
			$request_link_linkedin='';
	
			if (combroadcastHelper::validate($uaccess, 'linkedin'))
				$status_linkedin = 1;
			 else
			 {
					$status_linkedin = 0;
					$linkedin = new LinkedIn($bconfig->linkedin_access, $bconfig->linkedin_secret, $bconfig->callback_url_linkedin );    		
					$linkedin->getRequestToken();
					$_SESSION['requestToken']= serialize($linkedin->request_token);			 		    		
					$request_link_linkedin = $linkedin->generateAuthorizeUrl();		
			}

			$remove_link_linkedin ='';
			if ($status_linkedin)
				$remove_link_linkedin = JRoute::_('index.php?option=com_broadcast&controller=linkedin&task=remove');			
	
			$data['status_linkedin']			= $status_linkedin;		
			$data['request_link_linkedin']		= $request_link_linkedin;
			$data['remove_link_linkedin']		= $remove_link_linkedin;
		}
	/////////////////////////////////////Linkedin End//////////////////////////////////////
		return $data;		
	}

	function validate($uaccess, $app) 
	{
		 switch ($app)
		 {		
			case 'twitter':
			if ($uaccess->twitter_oauth && $uaccess->twitter_secret)
			return true;
			break;
			
			case 'linkedin':
			if ($uaccess->linkedin_oauth && $uaccess->linkedin_secret)
			return true;
			break;
			
			case 'facebook':
			if ($uaccess->facbook_uid)
			return true;
			break;
		}
	
	}
	
	function makelink($text)
	{
		$text = $text;
	   	$text = utf8_decode( $text );
		$text = preg_replace('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>',  $text );
		$text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $text);  
		$text = preg_replace("#(^|[\n ])\#([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://hashtags.org/search?query=\\2\" >#\\2</a>'", $text);
		return $text;
	}
	
	
	#inQueue function called from plugin as well can be called from custom place	
	function inQueue($newstatus, $userid, $count, $interval)
	{
		if(!$count)	$count = 1;
	    $db =& JFactory::getDBO();
		$obj		   = new StdClass();
		$obj->id	   = '';
		$obj->status   = $newstatus;
		$obj->userid   = $userid;
		$obj->flag 	   = 0;
		$obj->count	   = $count;
		$obj->interval = $interval;
			
		if(!$db->insertObject('#__broadcast_queue', $obj)){
      			$db->stderr();
      			return false;
  		}
	}
}

//this class is used to make log for f/l/t controllers 
class BroadcastHelperLogs
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
