<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.plugin.plugin' );


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
			$plugin			=& JPluginHelper::getPlugin('easyblog', 'broadcasteasyblog');
			$pluginParams	= new JParameter( $plugin->params );  
								
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
					$app = JFactory::getApplication();
					if($app->isAdmin())
					{
						require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
						$path = JRoute::_(JURI::root()."index.php?option=com_easyblog&view=entry&id=".$param->id);
					}
					else
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
