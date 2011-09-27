<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class combroadcastHelper
{ 	
	function getapistatus(){
		require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'models'.DS.'broadcast.php');
		$apis=BroadcastModelbroadcast::getapistatus();
		return $apis;
	}

	function makelink($text)
	{
		$text = $text;
	   	$text = utf8_decode( $text );
		$text = preg_replace('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>',  $text );
		$text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $text);  
		$text = preg_replace("#(^|[\n ])\#([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://hashtags.org/search?query=\\2\" >#\\2</a>'", $text);
		return $text;
	}
	
	#inQueue function called from plugin as well can be called from custom place	
	function inQueue($newstatus, $userid, $count, $interval, $supplier)
	{
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

		if(!$count)	$count = 1;
	    $db =& JFactory::getDBO();
		$obj		   = new StdClass();
		$obj->id	   = '';
		$obj->status   = $newstatus;
		$obj->userid   = $userid;
		$obj->flag 	   = 0;
		$obj->count	   = $count;
		$obj->interval = $interval;
		$obj->api = implode(',',$broadcast_config['api']);
		$obj->supplier	   = $supplier ;
			
		if(!$db->insertObject('#__broadcast_queue', $obj)){
      			$db->stderr();
      			return false;
  		}
  		return true;
	}
	
	#populate the temp activity table of broadcast called from broadcast & rss models 
	function intempAct($id, $act, $date, $api='')
	{
		$db 			=& JFactory::getDBO();
		$obj			= new StdClass();
		$obj->uid 		= $id;
		$obj->status 	= $act;
		$obj->created_date	= $date;	
		$obj->type		= $api; 
		if(!$db->insertObject('#__broadcast_tmp_activities', $obj)){		
      		$db->stderr();
      		return false;
  		}
	}
	#populate the Jomsocial activity table called from broadcast & rss models 
	function inJSAct($actor,$target,$title,$content,$api,$cid,$date)
	{
		$db 			=& JFactory::getDBO();
		$obj			= new StdClass();
		$obj->actor 	= $actor;
		$obj->target 	= $target;
		$obj->title		= $title;			
		$obj->content	= $content;
		$obj->app		= $api;
		$obj->cid		= $cid;
		$obj->params	= '';
		$obj->created	= $date;	#TODO convert into correct date time 
		$obj->access	= 0;
		$obj->points	= 1;
		$obj->archived	= 0; 
		if(!$db->insertObject('#__community_activities', $obj)){		
      		$db->stderr();
      		return false;
  		}
	}
	#set the current Jomsocial status, called from broadcast & rss models 
	function updateJSstatus($userid,$status,$date)
	{
		$db 	=& JFactory::getDBO();
		$query	= "UPDATE `#__community_users` SET `status` ='{$db->getEscaped($status)}', 
								posted_on='{$date}', points=points +1 WHERE userid='{$userid}'";
		$db->setQuery( $query );
		$result =$db->query();
	}
	#check if the status exist in the temp table of broadcast
	function checkexist($status,$uid,$api='')
	{
		$db 		=& JFactory::getDBO();
		$status		= explode('(via',$status);		
		$newstatus	= trim($status[0]);
		$newstatus	=$db->getEscaped($newstatus);
		$where = '';
		if($api)
			$where = ' AND type="'.$api.'"';
		$query = "SELECT status FROM #__broadcast_tmp_activities WHERE uid = {$uid} AND status = '{$newstatus}' ".$where ;
		$db->setQuery($query);
		if($db->loadResult())			
			return 1;					
		else
			return 0;
	}	
}


//this class is used to make log for f/l/t controllers 
if (!class_exists('techjoomlaHelperLogs'))
{
class techjoomlaHelperLogs
{	
	function simpleLog($comment,$type,$filename,$path="", $display=1,$params=array())
    {
   
    		if($path=="" and $type="plugin")
    		{
		  		if(JVERSION >='1.6.0')
					$path=JPATH_SITE.DS.'plugins'.DS.$params['group'].DS.$params['name'].DS.$params['name'].DS.'lib';
					else
					$path=JPATH_SITE.DS.'plugins'.DS.$params['group'].DS.$params['name'].DS.'lib';    		
    		}
    		
    		if($path=="" and $type="component")
    			$path=JPATH_JPATH_COMPONENT; 
    			   	
        // Include the library dependancies
        jimport('joomla.error.log');
        $my = JFactory::getUser();
        $options = array('format' => "{DATE}\t{TIME}\t{USER}\t{COMMENT}");
        // Create the instance of the log file in case we use it later
       	$log = &JLog::getInstance($filename, $options, $path);
        $log->addEntry(array('comment' => $comment, 'user' => $my->name .'('.$my->id.')'));
        if($display==1)
        JError::raiseWarning(500, $comment);

        
    }
}	
}
?>
