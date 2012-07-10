<?php
defined('_JEXEC') or die('Restricted access');


class plgSystemjomwallbroadcast extends JPlugin 
{ 

	function plgSystemjomwallbroadcast(& $subject, $config)
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
	
	
	function onJomwallstreamcreate($message,$attachment,$type) ///trigger present in SOME versions of jowall
	{ 
	
		

		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');
		//if only to broadcast public events 
		
		if($broadcast_config['push_only_public_acts']==1)
		{
			
			//if access is public then only add to queue
			if($_REQUEST['post_privacy']!='0')
			{
					return true;
			}

		}

				
		 $type=trim($type);
		if($type=='')
		$type='text';

		$user	=&JFactory::getUser();
		$sublst=$this->getusersetting($user->id);
				
		$subscribedapp	= explode('|',$sublst);


		if(in_array($type,$subscribedapp))
		{
				$string=$_REQUEST['awd_message'];
				preg_match('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/?:@=_#&%~,+$]+/', $string, $matches);
				if(!empty($matches['0']))
				{
					if($attachment!=$matches['0'])
					$message=trim($_REQUEST['awd_message']).' '.$attachment;
					else
					$message=trim($_REQUEST['awd_message']);
				}
				else
				$message=trim($_REQUEST['awd_message']).' '.$attachment;
			$uids=trim($broadcast_config['user_ids']);
			if(!empty($uids) || $uids!= '' || ($uids) )
			{
				$userids =$uids;
				$userid_arr = explode(',', $userids);
				if( !( in_array($user->id, $userid_arr) )  )
					array_push($userid_arr, $user->id);

				combroadcastHelper::addtoQueue($userid_arr,$message,date('Y-m-d H:i:s',time()),1,0,'','com_awdwall',1);
				
			}
			else
			{
				combroadcastHelper::addtoQueue($user->id,$message,date('Y-m-d H:i:s',time()),1,0,'','com_awdwall',1);
			
			}
		}

	

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

