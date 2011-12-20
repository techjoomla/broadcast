<?php
// No direct access allowed to this file
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');

class plgContentBroadcast_content extends JPlugin
{

     
	function plgContentBroadcast_content(& $subject, $config)
		{
			parent::__construct($subject, $config);
		}
		
	// for 1.5	
		
	public function onAfterContentSave( &$article, $isNew )
	{
		$app = JFactory::getApplication();
		if($app->isAdmin())
		{
			require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
		}		
		$categorys = ($this->params->get('category'));
		if(in_array($article->catid,$categorys))
		{		
        	$user =& JFactory::getUser();
	        $userid = $user->id;
	 			require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
				$userid_arr = array();
				if(isset($broadcast_config['user_ids']) || $broadcast_config['user_ids'] != '' || ($broadcast_config['user_ids']) )
				{ 
					$userids = $broadcast_config['user_ids'];
					$userid_arr = explode(',', $userids);
				}
				if(! ( in_array($userid, $userid_arr) )  )
					array_push($userid_arr, $userid);  
					      
			/*construct the msg to push into the queue*/
	        $username = $user->username; 
	        $path = JURI::base()."index.php?option=com_content&view=article&id=".$article->id.":".$article->alias."&catid=".$article->catid.":general";
			$msg_str = $this->params->get('msg');
			$msg_str= str_replace( '{username}',$username ,$msg_str);
			$msg_str= str_replace( '{article_name}',$article->title ,$msg_str);
			$msg_str= str_replace( '{path}',$path,$msg_str);
	        
			$date = $article->publish_up;
	        $count = 1;
	        $interval = 0;
	        $supplier = 'content_plugin';
	        $shorten_url = 1;
			combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url);
		}
	}
	
// for 1.7	
	public function onContentAfterSave($context, &$article, $isNew)
	{
		$categorys = ($this->params->get('category'));
		if(in_array($article->catid,$categorys))
		{		
        	$user =& JFactory::getUser();
	        $userid = $user->id;
	      require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
				$userid_arr = array();
				if(isset($broadcast_config['user_ids']) || $broadcast_config['user_ids'] != '' || ($broadcast_config['user_ids']) )
				{ 
					$userids = $broadcast_config['user_ids'];
					$userid_arr = explode(',', $userids);
				}
				if(! ( in_array($userid, $userid_arr) )  )
					array_push($userid_arr, $userid); 
					
			/*construct the msg to push into the queue*/
	        $username = $user->username; 
	        $path = JURI::base()."index.php?option=com_content&view=article&id=".$article->id.":".$article->alias."&catid=".$article->catid.":general";
			$msg_str = $this->params->get('msg');
			$msg_str= str_replace( '{username}',$username ,$msg_str);
			$msg_str= str_replace( '{article_name}',$article->title ,$msg_str);
			$msg_str= str_replace( '{path}',$path,$msg_str);
	        
			$date = $article->publish_up;
	        $count = 1;
	        $interval = 0;
	        $supplier = 'Joomlacontent_plugin';
	        $shorten_url = 1;
			combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url);
		}
		
	}
	
	
}


 
