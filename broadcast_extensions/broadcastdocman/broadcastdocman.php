<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/

defined('_JEXEC') or die('Restricted access');

// Import library dependencies
jimport('joomla.event.plugin');
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
class plgDocmanBroadcastdocman extends JPlugin
{  
   /**
    * Constructor
       
    */
    function plgDocmanBroadcastdocman(& $subject, $config) 
    {
            parent::__construct($subject, $config);

     }      

    function onAfterEditDocument()
    {		
		
			if(is_array($this->params->get('category')) )
				$categorys = ($this->params->get('category'));
			else{
				$categorys = array();
				$categorys[] = ($this->params->get('category'));
			}
			$cid = JRequest::getInt('catid', 0, 'post');
			if(in_array($cid,$categorys))
			{					 
				if(!$id = JRequest::getInt('id', 0, 'post')) 
				{
					$user =& JFactory::getUser();
					$userid = $user->id;
					//require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
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

					$id = JRequest::getInt('id', 0, 'post'); 
					$gid = JRequest::getInt('catid', 0, 'post');
					$dmname = JRequest::getWord ('dmname', 0, 'post'); 

					/*construct the msg to push into the queue*/
					$username = $user->username;

					$app = JFactory::getApplication();
					require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
					/*if($app->isAdmin())
					{

						$path = JRoute::_(JURI::root().'index.php?option=com_docman&task=cat_view&gid='.$gid);
					}
					else*/
						$path = JURI::root().substr(JRoute::_('index.php?option=com_docman&task=cat_view&gid='.$gid),strlen(JURI::base(true))+1);

					$msg_str = $this->params->get('msg'); 
					$msg_str= str_replace( '{username}',$username ,$msg_str);
					$msg_str= str_replace( '{title}',$dmname,$msg_str);
					$msg_str= str_replace( '{path}',$path,$msg_str);
					$date=JRequest::getVar('dmdate_published');
					$count = 1;
					$interval = 0;
					$supplier = 'DOCman_plugin';
					$shorten_url = 1;
					combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url); 
				}
			}
		}  
}
