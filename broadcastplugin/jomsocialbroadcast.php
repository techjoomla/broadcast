<?php
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php'); 
require_once( JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'helpers' . DS . 'time.php'); 
include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');

class plgCommunityjomsocialbroadcast extends CApplications 
{ 
	function plgCommunityjomsocialbroadcast(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	function onProfileDisplay() 
	{
		$show 		= $this->params->get('show_plugin', '');	
		if(!$show)
			return;	
		$user 				= CFactory::getUser();
		$activeProfile 	=& CFactory::getActiveProfile();
		$target = $activeProfile->id;
		if($target==$user->id)
		{
			$lang = & JFactory::getLanguage();
			$lang->load('mod_broadcast', JPATH_SITE);
			$apidata = combroadcastHelper::getapistatus();			
			$align=$this->params->get('show_horizontal', 0);
			ob_start();
				require(JModuleHelper::getLayoutPath('mod_broadcast'));
				$html .= ob_get_contents();
			ob_end_clean();
		return ($html);
		}
	}
	
	
	function onBeforeStreamCreate($activity) ///trigger present in SOME versions of joomla
	{ 
		$bc_activity = clone $activity;
		include_once(JPATH_SITE .DS. 'components'.DS.'com_community'.DS.'libraries'.DS.'activities.php');
		$user	= JFactory::getUser();	
		$subscribedapp	= explode('|',$this->getusersetting($user->id)); 
		if(in_array($bc_activity->app,$subscribedapp))
		{
			$title=$this->tag_replace($bc_activity->actor,$bc_activity->target,$bc_activity->created,$bc_activity);
			
			require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
			if(isset($broadcast_config['user_ids']) || $broadcast_config['user_ids'] != '' || ($broadcast_config['user_ids']) )
			{
				$userids = $broadcast_config['user_ids'];
				$userid_arr = explode(',', $userids);
				if( !( in_array($user->id, $userid_arr) )  )
					array_push($userid_arr, $user->id);
					
				combroadcastHelper::addtoQueue($userid_arr, $title, date('Y-m-d H:i:s',time()),1,0,'','com_community',1);
			}
			else
				combroadcastHelper::addtoQueue($user->id, $title, date('Y-m-d H:i:s',time()),1,0,'','com_community',1);
		}

	return true;
	}

	function tag_replace($actor, $target, $date = null, $activity )
	{
		require(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'controllers'.DS.'googlshorturl.php');
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$api_key=$broadcast_config['url_apikey'];
		$goo = new Googl($api_key);//if you have an api key

		$my			= CFactory::getUser();
		$config		= CFactory::getConfig();
		$dayinterval 	= ACTIVITY_INTERVAL_DAY;
				
		$title = $activity->title;
		$app = $activity->app;
		$cid = $activity->cid;
		$actor = $activity->actor;		
	
	//Convert
		$actor_name=CFactory::getUser($activity->actor)->name;
		$target = CFactory::getUser($activity->target)->name;
	
	//Replacements
		$activity->title	= JString::str_ireplace('{app}', $app, $activity->title);    
		$activity->title	= JString::str_ireplace('{target}', $target, $activity->title);
	    $activity->title	= CString::str_ireplace('{actor}', $actor_name, $activity->title);
	//Faulty Replacements - needs to be fixed		
	
		$activity->title = preg_replace('/\{multiple\}(.*)\{\/multiple\}/i', '', $activity->title);
		$search  = array('{single}','{/single}');
		$activity->title	= CString::str_ireplace($search, '', $activity->title);		
	
	//Append URL
		$shorturl='';
		if($activity->params){
			$activity->title = $activity->title." ".$this->_getURL($activity->app,$activity->params);
		}
		$activity->title=strip_tags($activity->title);		
		return $activity->title;
	}

	function _getURL($app,$params)
	{
		$paramarray = new CParameter( $params );
		$url =  '';
		switch($app)
		{
			case 'events':
				$url = JURI::base().$paramarray->get('event_url', null);
			break;
			case 'groups':
				$url = JURI::base().$paramarray->get('group_url', null);
			break;
			case 'videos':
				$url = JURI::base().$paramarray->get('video_url', null);
			break;
			case 'photos':
				$u =& JURI::getInstance(JURI::current());
				$url = 'http://'.$u->getHost().$paramarray->get('photo_url', null);
			break;
		}
//		echo $url; die;
		return $url;
	}
	
	function getusersetting($userid)
	{
		$db        = & JFactory::getDBO();
		$qry       = "SELECT broadcast_activity_config  FROM #__broadcast_config WHERE user_id  = {$userid}";
		$db->setQuery($qry);
		$sub_list  = $db->loadResult();
		return $sub_list;
	}
		
}

