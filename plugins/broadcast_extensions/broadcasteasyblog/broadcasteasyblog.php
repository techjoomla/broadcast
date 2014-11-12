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
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.plugin.plugin' );
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

class plgEasyblogbroadcasteasyblog extends JPlugin
{
    function plgEasyblogbroadcasteasyblog(& $subject, $config)
    {

			if(JFile::exists(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'helper.php'))
			{
				require_once (JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'helper.php');
			}

			if(JFile::exists(JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'router.php'))
			{
				require_once (JPATH_ROOT.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'router.php');
			}

			parent::__construct($subject, $config);
    }


		function onAfterEasyBlogDelete( $blog )
		{

			// Get plugin info


		}

		function onAfterEasyBlogSave ( $param, $isNew )
		{
			$plugin			=JPluginHelper::getPlugin('easyblog', 'broadcasteasyblog');

			if(is_array($this->params->get('category')) )
				$categorys = ($this->params->get('category'));
			else{
				$categorys = array();
				$categorys[] = ($this->params->get('category'));
			}

			if(in_array($param->category_id,$categorys))
			{
				if($isNew)
				{
					$user =JFactory::getUser();
					$userid = $user->id;
					//require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
					$com_params=JComponentHelper::getParams('com_broadcast');
					$userid_arr = array();
					$userids=$com_params->get('user_ids');
					if(isset($userids))
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
					/*if($app->isAdmin())
					{

						$path = JRoute::_(JURI::root()."index.php?option=com_easyblog&view=entry&id=".$param->id);
					}
					else*/
						$path = JURI::root().substr(JRoute::_('index.php?option=com_easyblog&view=entry&id='.$param->id),strlen(JURI::base(true))+1);

					$title = $param->title;
					$msg_str = $this->params->get('msg');
					$msg_str= str_replace( '{username}',$username ,$msg_str);
					$msg_str= str_replace( '{title}',$title,$msg_str);
					$msg_str= str_replace( '{path}',$path,$msg_str);

					$date = $item->publish_up;
					$count = 1;
					$interval = 0;
					$supplier = 'EasyBlog_plugin';
					$shorten_url = 1;
					combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url);
				}
			}
    }
}
