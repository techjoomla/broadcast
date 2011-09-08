<?php

defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );

class BroadcastModelrss extends JModel
{
		
	 function rssstore($act)
	 {
				$db	 	= &$this->getDBO();	
				$db->insertObject('#__community_activities', $act);
				$tmp_obj=null;
				$currentstatus=$act->title;
				$tmp_obj->uid 				= $act->actor;
				$currentstatus=str_replace("{actor} ",'',$currentstatus);
				$currentstatus	=mysql_real_escape_string("$currentstatus");	
				$tmp_obj->status 		=$currentstatus;
				$tmp_obj->created_date	=$act->created;	
				$tmp_obj->type='rss';					
				$db->insertObject('#__broadcast_tmp_activities', $tmp_obj);
				$qry	 			= "SELECT title, created FROM #__community_activities WHERE actor = {$act->actor} ORDER BY created desc ";
				$db->setQuery($qry);		
				$mydata 		= $this->_db->loadObject();		
				$currentstatus  = $mydata->title;
				$created 		= $mydata->created;
				$currentstatus=str_replace("{actor} ",'',$currentstatus);
				$currentstatus	=mysql_real_escape_string("$currentstatus");					
				$query	= "UPDATE `#__community_users` SET `status` ='{$currentstatus}', 
									posted_on='{$created}', points=points +1 WHERE userid='{$act->actor}'";
				$db->setQuery( $query );
				$addHit =$db->query();
	  }

		function rssdeletetmpactivity()
		{
			$curtime=time();
		 	$previousdt1= $curtime-(3600*24*2);		 	
		 	$previousdt=date('Y-m-d',$previousdt1);
			$db	 	= &$this->getDBO();		
			$query = "DELETE FROM #__broadcast_tmp_activities WHERE created_date<'$previousdt'";
			$db->setQuery($query);	
			$db->query();		
		}
		
		function checkexist($status,$uid)
		{
			$newstatus	=mysql_real_escape_string($status);
			$db	 			= &$this->getDBO();	
			$query 			= "SELECT status FROM #__broadcast_tmp_activities WHERE uid = {$uid} and status LIKE '{$newstatus}' AND type='rss' ";
			$db->setQuery($query);	
			$result 			=$db->loadObjectList();
			if($result)			
			return 1;					
			else
			return 0;
		}
	}
