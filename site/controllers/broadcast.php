<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');


class BroadcastControllerbroadcast extends JControllerLegacy
{
	var $bconfig = '';

	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}
	
	function inEasysocialact($actor,$target,$title,$content,$api,$cid,$date)
	{
		require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
		
		$linkHTML='<a href="'.$content.'">'.$title.'</a>';	
		if($actor!=0)
		$myUser = Foundry::user( $actor );
		$stream = Foundry::stream();
		$template = $stream->getTemplate();
		$template->setActor( $actor, SOCIAL_TYPE_USER );
		$template->setContext( $actor, SOCIAL_TYPE_USERS );
		$template->setVerb( 'broadcast' );
		$template->setType( 'full' );
		if($actor!=0)
		{
			$userProfileLink = '<a href="'. $myUser->getPermalink() .'">' . $myUser->getName() . '</a>';
			$title 	 = ($userProfileLink." ".$linkHTML);
		}
		else
		$title 	 = ("A guest ".$act_description);
		$template->setTitle( $title );
		$template->setContent($content );

		$template->setAggregate( false );

		$template->setPublicStream( 'core.view' );
		$stream->add( $template );
		return true;
	}
		//START apis
	/*call model for request token*/
	function get_request_token()
	{
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();	
		$model=$this->getModel('broadcast');
		$api_used =JRequest::getVar('api'); 
		$session->set('api_used',$api_used);
		$grt_response=$model->getRequestToken($api_used);
	}
	
	/*single cron URL for running all the functions*/
	function br_allfunc_cron(){
		//require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$params=JComponentHelper::getParams('com_broadcast');
		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$params->get('private_key_cronjob'))		
		{
			echo JText::_("CRON_KEY_MSG");
			return;
		}
		
		$func = JRequest::getVar('func');
		if($func)
			$this->$func();
		else{	
			$funcs = array ('get_status','set_status');	 /*add the function names you need to add here*/
			foreach ($funcs as $func){
			echo '<br>***************************************<br>';
				$this->$func();
			echo '<br>***************************************<br>';			
			}
		}
		
		require_once(JPATH_SITE.DS."components".DS."com_broadcast".DS."controllers".DS."rss.php");
		$rssobj=new BroadcastControllerrss;
		$rssobj->getrssdata();
	}		
	
	/*call model for access token*/
	function get_access_token()
	{
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();	
		$msg = '';
		$get=JRequest::get('get'); 
		$model=$this->getModel('broadcast');
		$response=$model->getAccessToken($get);
		if($response){
			$user	= JFactory::getUser();
			$msg = JText::sprintf("BC_CONN_TO",$user->name,ucfirst(str_replace('plug_techjoomlaAPI_', '', $session->get('api_used',''))), $mainframe->getCfg('sitename') );
			$userconfig = $model->checkuserconfig($user->id);
			if(!$userconfig){ 
				$mainframe->redirect(JURI::base()."index.php?option=com_broadcast&view=config", $msg."<br>".JText::_("BC_USER_SET_MSG") );
			}
		}
	 	$currentMenu = $session->get('currentMenu'); 
		$mainframe->redirect(JURI::base()."index.php?option=com_broadcast&view=config", $msg."<br>".JText::_("BC_USER_SET_MSG") );
	}
	/*call to destroy the token of a user*/
	function remove_token()
	{ 
		$mainframe = JFactory::getApplication();
		$session =JFactory::getSession();	
		$api_used =JRequest::getVar('api');
		$model = $this->getModel('broadcast');
		$model->removeToken($api_used);

		$currentMenu = $session->get('currentMenu'); 
		$mainframe->redirect(JURI::base()."index.php?option=com_broadcast&view=config");
	}
	function get_status()
	{
		$params=JComponentHelper::getParams('com_broadcast');
		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$params->get('private_key_cronjob'))		
		{
			echo JText::_("NOT_AUTH_KEY"); //This Private Cron Key Doesnot Exist
			return;
		}
		foreach($params->get('api') as $v){
			$model = $this->getModel('broadcast');
			$model->getStatus($v);
		}
	}
	function set_status()
	{
		$db=JFactory::getDBO();
		$params=JComponentHelper::getParams('com_broadcast');
		//require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$integration=$params->get('integration');

		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$params->get('private_key_cronjob'))		
		{
			echo JText::_("NOT_AUTH_KEY"); //This Private Cron Key Doesnot Exist
			return;
		}
		$response = array();
		$model = $this->getModel('broadcast');
		$queues = $model->getqueue();		
		$model->purgequeue();
		if(empty($queues))
		echo "No Data in Queue";
		foreach($queues as $queue) 
		{
			$updtinterval=strtotime($queue->date)+($queue->flag+1)+$queue->interval;
		   	$curttime=time();
		  	if ( ($updtinterval<$curttime || $queue->flag==0)  && $queue->count >0)
		  	{
				$response = $model->setStatus($queue->api,$queue->userid,$queue->status);
				if( !in_array(0,$response) ){
					$qtime = date('Y-m-d H:i:s',$curttime);
					$query="UPDATE #__broadcast_queue SET date='{$qtime}', count=count-1,flag=1 WHERE id={$queue->id}";
				}
				else{
					$query="UPDATE #__broadcast_queue SET flag=0 WHERE id={$queue->id}";
				}
				$db->setQuery($query);
				$db->query();
			}// end of the interval chk if
			$model->purgequeue();
		}// end of foreach of queue
		//purge the queue table

	}

}
class BroadcastHelperLog
{
  function simpleLog($comment, $level=1)
  {
        // Include the library dependancies
        jimport('joomla.error.log');
        $my = JFactory::getUser();
        $options = array('format' => "{DATE}\t{TIME}\t{USER}\t{COMMENT}");
        // Create the instance of the log file in case we use it later
        $log = &JLog::getInstance('broadcast.log');
        $log->addEntry(array('comment' => $comment, 'user' => $my->name .'('.$my->id.')'));
  }
}
?>
