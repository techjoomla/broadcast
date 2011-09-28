<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');


class BroadcastControllerbroadcast extends JController
{
	var $bconfig = '';	
	function display()
	{
		parent::display();
	}
		//START apis
	/*call model for request token*/
	function get_request_token()
	{
		$mainframe = JFactory::getApplication();
		$session =& JFactory::getSession();	
		$model=&$this->getModel('broadcast');
		$api_used =JRequest::getVar('api'); 
		$session->set('api_used',$api_used);
		$grt_response=$model->getRequestToken($api_used);
	}
	
	/*call model for access token*/
	function get_access_token()
	{
		$mainframe = JFactory::getApplication();
		$session =& JFactory::getSession();	
		$msg = '';
		$get=JRequest::get('get'); 
		$model=&$this->getModel('broadcast');
		$response=$model->getAccessToken($get);
		if($response){
			$user	= JFactory::getUser();
			$msg	= $user->name." "."connected!!"." ".$mainframe->getCfg('sitename');
		}
	 	$currentMenu = $session->get('currentMenu'); 
		$mainframe->redirect( JURI::base(), $msg);
	}
	/*call to destroy the token of a user*/
	function remove_token()
	{ 
		$mainframe = JFactory::getApplication();
		$session =& JFactory::getSession();	
		$api_used =JRequest::getVar('api');
		$model = $this->getModel('broadcast');
		$model->removeToken($api_used);

		$currentMenu = $session->get('currentMenu'); 
		$mainframe->redirect( JURI::base(), $msg);
	}
	function get_status()
	{
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$broadcast_config['private_key_cronjob'])		
		{
			echo JText::_("NOT_AUTH_KEY"); //This Private Cron Key Doesnot Exist
			return;
		}
		foreach($broadcast_config['api'] as $v){
			$model = $this->getModel('broadcast');
			$model->getStatus($v);
		}
	}
	function set_status()
	{
		$db = & JFactory::getDBO();
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$broadcast_config['private_key_cronjob'])		
		{
			echo JText::_("NOT_AUTH_KEY"); //This Private Cron Key Doesnot Exist
			return;
		}
		$response = array();
		$model = $this->getModel('broadcast');
		$queue = $model->getqueue(); 
		foreach($queue as $queue) 
		{
			$updtinterval=strtotime($queue->date)+($queue->flag+1)+$queue->interval;
		   	$curttime=time();
		  	if ($updtinterval<$curttime || $queue->flag==0 )
		  	{
				foreach($broadcast_config['api'] as $v){
				if ( in_array( $v,explode(",",$queue->api) ) )
					$response[$v] = $model->setStatus($v,$queue->userid,$queue->status);
				}
				$remain_api = array();
				foreach ($response as $key => $row)
				{
					foreach($row as $cell)
					{
						if ($cell == false){
							$remain_api[] = $key;
							break;
						}
					}
				}  
				if( empty($remain_api) ){
					if($queue->count > 1){
						$qtime = date('Y-m-d H:i:s',$curttime); 
						$query="UPDATE #__broadcast_queue SET date='{$qtime}', count=count-1,flag=flag+1,api='".implode(',',$broadcast_config['api'])."' WHERE id={$queue->id}";
					}else
						$query="DELETE FROM #__broadcast_queue where id={$queue->id}";  
					$db->setQuery($query);
					$db->query();
				}
				else{
					$query="UPDATE #__broadcast_queue SET flag=0,api='".implode(',',$remain_api)."'";
					$db->setQuery($query);
					$db->query();
					
				}
			}// end of the interval chk if
		}// end of foreach of queue
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
