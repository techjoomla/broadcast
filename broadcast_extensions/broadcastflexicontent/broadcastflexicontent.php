<?php
/**
 
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.event.plugin');

class plgFlexicontent_fieldsbroadcastflexicontent extends JPlugin
{
	function plgFlexicontent_fieldsbroadcastflexicontent( &$subject, $params )
	{
	   
		parent::__construct( $subject, $params );
		//JPlugin::loadLanguage('plg_flexicontent_fields_core', JPATH_ADMINISTRATOR);
		
	}
	
	

	function onCompleteSaveItem(  &$item, &$fields)
	{
	        $app = JFactory::getApplication();
			if($app->isAdmin())
			{
				require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
			}
	
	        $plugin			=& JPluginHelper::getPlugin('flexicontent_fields', 'broadcastflexicontent');
        	$pluginParams	= new JParameter( $plugin->params );
        	
            $user =& JFactory::getUser();
			$userid = $user->id;
			require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
			$userids = $broadcast_config['user_ids'];
			$userid_arr = explode(',', $userids);
			$userid_arr[]= array_push($userid_arr, $userid);   	
        	
	       
	        $categorys = array($this->params->get('category'));
            $cid = JRequest::getInt('catid', 0, 'post');
			if(in_array($cid,$categorys))
			{	 
		   	$user =& JFactory::getUser();
	        $userid = $user->id;
	        
	        if(!$id = JRequest::getInt('id', 0, 'post')) 
	        {
	        
	        $id = JRequest::getInt('id', 0, 'post'); 
	        $catid = JRequest::getInt('catid', 0, 'post');
	        $title = JRequest::getVar('title', 0, 'post'); 
	   		
	   		$username = $user->username;
	        $path = JURI::root()."index.php?option=com_flexicontent&view=items".$id."&catid=".$catid."&title=".$title.":general";
	         
	        $msg_str= $pluginParams->get('msg', '');
			$msg_str= str_replace( '{username}',$username ,$msg_str);
			$msg_str= str_replace( '{item_name}',$title,$msg_str);
			$msg_str= str_replace( '{path}',$path,$msg_str);
	          
			$date = $item->publish_up;
	        $count = 1;
	        $interval = 0;
	        $supplier = 'flexicontent_plugin';
	        $shorten_url = 1;
			combroadcastHelper::addtoQueue($userid_arr,$msg_str,$date,$count,$interval,'',$supplier,$shorten_url); 	
   		   }
   		
   		}
	        
   }
}
