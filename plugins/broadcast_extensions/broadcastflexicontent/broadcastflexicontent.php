<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.event.plugin');
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
class plgFlexicontentBroadcastflexicontent extends JPlugin
{
	function plgFlexicontentBroadcastflexicontent( &$subject, $params )
	{

		parent::__construct( $subject, $params );
		//JPlugin::loadLanguage('plg_flexicontent_fields_core', JPATH_ADMINISTRATOR);

	}

	function onCompleteSaveItem(  &$item, &$fields)
	{
		$plugin			=& JPluginHelper::getPlugin('flexicontent', 'broadcastflexicontent');
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
				$userid_arr = array();
				$com_params=JComponentHelper::getParams('com_broadcast');
				$useids=$com_params->get('user_ids');
				if(isset($useids))
				{
					$userids = $com_params->get('user_ids');
					$userid_arr = explode(',', $userids);
				}
				if(! ( in_array($userid, $userid_arr) )  )
					array_push($userid_arr, $userid);

				$id = $item->id;

				$title = $item->title;
					require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
				$app = JFactory::getApplication();
				if($app->isAdmin())
				{

					$path = JRoute::_(JURI::root()."index.php?option=com_flexicontent&view=item&cid=".$cid."&id=".$id);
				}
				else
					$path = JURI::root().substr(JRoute::_("index.php?option=com_flexicontent&view=item&cid=".$cid."&id=".$id),strlen(JURI::base(true))+1);

				$username = $user->username;

				$msg_str= $this->params->get('msg');
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
