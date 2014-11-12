<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
// No direct access allowed to this file
defined( '_JEXEC' ) or die( 'Restricted access' );
 if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

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
		$this->_newcontentsave($article, $isNew);
		return true;
	}

	// for 1.7
	public function onContentAfterSave($context, $article, $isNew)
	{
		$this->_newcontentsave($article, $isNew);
		return true;
	}

	protected function _newcontentsave($article, $isNew)
	{

		$categorys = ($this->params->get('category'));
		if(is_array($this->params->get('category')) )
			$categorys = ($this->params->get('category'));
		else{
			$categorys = array();
			$categorys[] = ($this->params->get('category'));
		}

		if(in_array($article->catid,$categorys))
		{
			if($isNew)
			{
				$user =JFactory::getUser($article->created_by);
				$userid = $user->id;

				$com_params=JComponentHelper::getParams('com_broadcast');

				$userid_arr = array();
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

					$path = JRoute::_(JURI::root()."index.php?option=com_content&view=article&id=".$article->id.":".$article->alias."&catid=".$article->catid.":general");
				}
				else
					$path = JURI::root().substr(JRoute::_("index.php?option=com_content&view=article&id=".$article->id.":".$article->alias."&catid=".$article->catid.":general"),strlen(JURI::base(true))+1);

				$msg_str = $this->params->get('msg');
				$msg_str= str_replace( '{username}',$username ,$msg_str);
				$msg_str= str_replace( '{article_name}',$article->title ,$msg_str);
				$msg_str= str_replace( '{path}',$path,$msg_str);

				$date = $article->publish_up;
				$count = 1;
				$interval = 0;
				$supplier = 'Content_plugin';
				$shorten_url = 1;
				combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url);

			}
		}
	}

}
