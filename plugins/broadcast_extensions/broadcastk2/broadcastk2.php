<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/

// no direct access
defined('_JEXEC') or die ('Restricted access');
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
// Load the K2 Plugin API
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'k2plugin.php');

// Initiate class to hold plugin events
class plgK2Broadcastk2 extends K2Plugin {

	// Some params
	var $pluginName = 'broadcastk2';
	var $pluginNameHumanReadable = 'Broadcast K2 Plugin';

	function plgK2Broadcastk2( & $subject, $params)
	 {
		parent::__construct($subject, $params);
	}


	function onAfterK2Save(& $item, $isNew)
	{
		$plugin			=& JPluginHelper::getPlugin('K2', 'broadcastk2');
		if(is_array($this->params->get('category')) )
			$categorys = ($this->params->get('category'));
		else{
			$categorys = array();
			$categorys[] = ($this->params->get('category'));
		}
		$cid = JRequest::getInt('catid', 0, 'post');

		if(in_array($cid,$categorys))
			{

			if($isNew)
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

		/*construct the msg to push into the queue*/
				$username = $user->username;
				$app = JFactory::getApplication();
				require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
				if($app->isAdmin())
				{
					$path = JRoute::_(JURI::root()."index.php?option=com_k2&view=item&id=".$item->id.":".$item->alias);
				}
				else
					$path = JURI::root().substr(JRoute::_("index.php?option=com_k2&view=item&id=".$item->id.":".$item->alias),strlen(JURI::base(true))+1);

				$title = $item->title;
				$msg_str = $this->params->get('msg');
				$msg_str= str_replace( '{username}',$username ,$msg_str);
				$msg_str= str_replace( '{item_name}',$title,$msg_str);
				$msg_str= str_replace( '{path}',$path,$msg_str);

				$date = $item->publish_up;
				$count = 1;
				$interval = 0;
				$supplier = 'K2_plugin';
				$shorten_url = 1;
				combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url);

			}
		}

	}

} // END CLASS

?>
