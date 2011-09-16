<?php
defined('_JEXEC') or die('Restricted access');

require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
require_once( JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php'); 
require_once( JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'helpers' . DS . 'time.php'); 

class plgCommunityjomsocialbroadcast extends CApplications 
{ 

	function plgCommunityjomsocialbroadcast(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
	
	function onProfileDisplay() 
	{
		include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
		$show 		= $this->params->get('show_plugin', '');	
		if(!$show)
			return;	
		$user 				= CFactory::getUser();
		$activeProfile 	=& CFactory::getActiveProfile();
		$target = $activeProfile->id;
		if($target==$user->id)
		{
			$lang = & JFactory::getLanguage();
			$lang->load('mod_jomsocialbroadcast', JPATH_SITE);
			$data = combroadcastHelper::getInfo();			
			$align=$this->params->get('show_horizontal', 0);
			ob_start();
				require(JModuleHelper::getLayoutPath('mod_jomsocialbroadcast'));
				$html .= ob_get_contents();
			ob_end_clean();
		return ($html);
		}
	}
	
	
	function onBeforeStreamCreate($activity) ///trigger present in SOME versions of joomla
	{
		include_once(JPATH_SITE .DS. 'components'.DS.'com_community'.DS.'libraries'.DS.'activities.php');
		require(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'controllers'.DS.'url-shortening-class.php');
		if($broadcast_config['facebook_profile'] or $broadcast_config['facebook_page'] or $broadcast_config['twitter'] or  $broadcast_config['linkedin'])
		{
			$subscribedapp	= explode('|',$this->getusersetting());
			if(in_array($activity->app,$subscribedapp))
			{	 
				$title=$this->tag_replace($activity->actor,$activity->target,$activity->created,$activity);
/*			
				$matches="";
				preg_match_all("/<a href=.*?<\/a>/", $new_status, $matches);
				$orgUrl  ="";
				$innertest="";
				$shortUrl="";
				if(!empty($matches))
					$this->setUrlShortening($broadcast_config,$matches); 
*/
				$user	= JFactory::getUser();	
				combroadcastHelper::inQueue($title, $user->id, 1,0);
			}
		}
	return true;
	}

	function tag_replace($actor, $target, $date = null, $activity )
	{
		$my			= CFactory::getUser();
		$config		= CFactory::getConfig();
		$dayinterval 	= ACTIVITY_INTERVAL_DAY;
		$row1=$oRow1= $activity2;
				
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
		$url='';
		if($activity->params){
		$shorturl = array();
			$shorturl[0][] = $this->_getURL($activity->app,json_decode($activity->params));
		}
		if(!empty($shorturl))
			$url=	$this->setUrlShortening($shorturl); 
	
		$activity->title=strip_tags($activity->title." ".$url);
		return $activity->title;
	}

	function _getURL($app,$paramarray)
	{
		$url =  '';
		switch($app)
		{
		case 'events':
				$url = $paramarray->event_url;
		break;
		case 'videos':
				$url = $paramarray->video_url;
		break;
		case 'photos':
				$url = $paramarray->photo_url;
		break;
				
		}
		return $url;
	}
	
	function getusersetting($userid)
	{
		$db        = & JFactory::getDBO();
		$qry       = "SELECT broadcast_activity_config  FROM #__broadcast_config WHERE  user_id  = {$userid}";
		$db->setQuery($qry);
		$sub_list  = $db->loadResult();
		return $sub_list;
	}
	
			
	function setUrlShortening($mtchs)
	{
		if(isset($mtchs[0]))
		foreach ($mtchs[0] as $mtch) 
		{
			try 
			{
				$innertest=strip_tags($mtch);
				$mtch_bits = explode('"', $mtch); 

				$pathsite=str_replace(JURI::base( true )."/", "", JURI::base());
				$objGoogl = new Googl();
				if(stristr($mtch_bits[1],"http://") || stristr($mtch_bits[1],"www"))
					$orgUrl  = "{$mtch_bits[1]}";
				else
					$orgUrl  = $pathsite."{$mtch_bits[1]}";

				 if(strlen($orgUrl)>$broadcast_config['url_limit'])
				 {
					$shortUrl  = $objGoogl->shorten($orgUrl);
					if($orgUrl != $shortUrl && $shortUrl != "")
					$orgUrl  = $shortUrl;
				 }
			}
			catch (Exception $e) {}
		}
		return $orgUrl;
	}	
		
}

