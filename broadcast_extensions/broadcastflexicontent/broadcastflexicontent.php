<?php
/**
 
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.event.plugin');

class plgFlexicontent_fieldsBroadcastflexicontent extends JPlugin
{
	function plgFlexicontent_fieldsBroadcastflexicontent( &$subject, $params )
	{
	   
		parent::__construct( $subject, $params );
		//JPlugin::loadLanguage('plg_flexicontent_fields_core', JPATH_ADMINISTRATOR);
		
	}

	function onCompleteSaveItem(  &$item, &$fields)
	{
		$plugin			=& JPluginHelper::getPlugin('flexicontent_fields', 'broadcastflexicontent');
		$pluginParams	= new JParameter( $plugin->params );

		if(is_array($this->params->get('category')) )
			$categorys = ($this->params->get('category'));
		else{
			$categorys = array();
			$categorys[] = ($this->params->get('category'));
		}
		$cid = $item->catid;
		if(in_array($cid,$categorys))
		{	 
			if(!$id = JRequest::getInt('id', 0, 'post')) 
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
		
				$id = $item->id; 

				$title = $item->title; 

				$app = JFactory::getApplication();
				if($app->isAdmin())
				{
					require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
					$path = JRoute::_(JURI::root()."index.php?option=com_flexicontent&view=items&cid=".$cid."&id=".$id);
				}
				else
					$path = JURI::root().substr(JRoute::_("index.php?option=com_flexicontent&view=items&cid=".$cid."&id=".$id),strlen(JURI::base(true))+1);

				$username = $user->username;
				 
				$msg_str= $pluginParams->get('msg', '');
				$msg_str= str_replace( '{username}',$username ,$msg_str);
				$msg_str= str_replace( '{item_name}',$title,$msg_str);
				$msg_str= str_replace( '{path}',$path,$msg_str);

				$date = $item->publish_up;
				$count = 1;
				$interval = 0;
				$supplier = 'FLEXIcontent_plugin';
				$shorten_url = 1;
				combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url); 	
			} 		
		}      
   }
}
