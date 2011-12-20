<?php
/**
 * DOCman 1.5.x - Joomla! Document Manager
 * @version $Id: dmtestplugin.php 1014 2009-12-05 14:43:24Z mathias $
 * @package dmtestplugin
 * @author Mathias Verraes
 * @copyright (C) 2003-2007 The DOCman Development Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.joomlatools.eu/ Official website
 **/
defined('_JEXEC') or die('Restricted access');

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport('joomla.event.plugin');

class plgDocmanbroadcastdocman extends JPlugin
{
    
   /**
    * Constructor
       
    */
    function plgDocmanbroadcastdocman(& $subject, $config) 
    {
            parent::__construct($subject, $config);

     }      

    function onAfterEditDocument()
    {		$app = JFactory::getApplication();
			if($app->isAdmin())
			{
				require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
			}
			
			$user =& JFactory::getUser();
			$userid = $user->id;
			require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
			$userids = $broadcast_config['user_ids'];
			$userid_arr = explode(',', $userids);
			$userid_arr[]= array_push($userid_arr, $userid);
        		
        	$plugin			=& JPluginHelper::getPlugin('docman', 'Broadcastdocman');
        	$pluginParams	= new JParameter( $plugin->params );	
        		
            $categorys = array($this->params->get('category'));
            $cid = JRequest::getInt('catid', 0, 'post');
			if(in_array($cid,$categorys))
			{	
            
		   	$user =& JFactory::getUser();
	        $userid = $user->id;
	             
	        if(!$id = JRequest::getInt('id', 0, 'post')) 
	        {
	        $id = JRequest::getInt('id', 0, 'post'); 
	        $gid = JRequest::getInt('catid', 0, 'post');
	        $dmname = JRequest::getWord ('dmname', 0, 'post'); 
	        
			/*construct the msg to push into the queue*/
	        $username = $user->username;
	        $path = JURI::root()."index.php?option=com_docman&task=cat_view&gid=".$gid."&Itemid=".$id."&dmname=".$dmname.":general";
	         
	        
	        $msg_str = $this->params->get('msg'); 
			$msg_str= str_replace( '{username}',$username ,$msg_str);
			$msg_str= str_replace( '{title}',$dmname,$msg_str);
			$msg_str= str_replace( '{path}',$path,$msg_str);
	          
			$date = $item->publish_up;
	        $count = 1;
	        $interval = 0;
	        $supplier = 'Docman_plugin';
	        $shorten_url = 1;
			combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url); 
            }     
           
        }
    }
    
   
}
