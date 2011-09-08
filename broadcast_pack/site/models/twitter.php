<?php

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class BroadcastModeltwitter extends JModel
{
	
	function store($data)
	{
		$db	 	= $this->getDBO();
		$user = JFactory::getUser();
		$qry = "SELECT user_id FROM #__broadcast_users WHERE user_id = {$user->id}";
		$db->setQuery($qry);
		$exists = $db->loadResult();
		$row = new stdClass;
		$row->user_id = $user->id;
		foreach ($data as $k=>$v)
		 {
			$row->$k = $v;
		 }		

		if ($exists)
		 {
			$db->updateObject('#__broadcast_users', $row, 'user_id');
		 }
		 else
		 {
			$db->insertObject('#__broadcast_users', $row);
		 }
		sleep(4);
		
	}
	
	function remove()
	{
		$db	 	= &$this->getDBO();
		$user 	= JFactory::getUser();
		$qry 	= "UPDATE #__broadcast_users SET twitter_oauth='',twitter_secret='' WHERE user_id = {$user->id}";
		$db->setQuery($qry);	
		$addHit =$db->query();
	}
	
	///This function store data from twitter 
	function twitstore($data)
	{
		jimport('joomla.utilities.date');
		$db	 	= &$this->getDBO();
		$obj	= new StdClass();
		for($i=0;$i<count($data);$i++)
		{
			$today	= & JFactory::getDate($data[$i]['created']);	
			$content = $data[$i]['status'];
			$content = preg_replace("/<img[^>]+\>/i", "", $content); 
			$checkcontent	= trim($content);	    
			if(empty($checkcontent))return; 
			
			if((!$this->checkexist($data[$i]['status'],$data[$i]['actor'])))
			{
			$obj->actor 	= $data[$i]['actor'];
			$obj->target 	= $data[$i]['target'];
			$data[$i]['title']	= combroadcastHelper::makelink($content);					
			$obj->title		= $data[$i]['title'];
			$data[$i]['content']	=$db->getEscaped($data[$i]['content']);					
			$obj->content	= $data[$i]['content'];
			$obj->app			= 'twitter';//$data[$i]['app'];
			$obj->cid			= $data[$i]['cid'];
			$obj->params	= $data[$i]['params'];
			$obj->created	= $today->toMySQL();
			$obj->access	= $data[$i]['access'];
			$obj->points		= $data[$i]['points'];
			$obj->archived	= $data[$i]['archived'];
			$db->insertObject('#__community_activities', $obj);	
					
			$tmp_obj=null;
			$tmp_obj->uid 			= $data[$i]['cid'];
			$tmpstatus=explode('(via',$data[$i]['status']);
			$tmp_obj->status =$content;
			$tmp_obj->created_date	= date('Y-m-d',$data[$i]['created']);	
			$tmp_obj->type='twitter';																
			$db->insertObject('#__broadcast_tmp_activities', $tmp_obj);
			
			$qry = "SELECT title, created FROM #__community_activities WHERE actor = {$obj->actor} ORDER BY created desc ";
			$db->setQuery($qry);		
			$mydata = $db->loadObject();
			
			$currentstatus = $mydata->title;
			$created = $mydata->created;		
			$currentstatus		= str_replace("{actor} ",'',$currentstatus);		
			$currentstatus	= $db->getEscaped($content);//mysql_real_escape_string("$currentstatus");					
			$query	= "UPDATE #__community_users SET `status` ='{$currentstatus}', posted_on='{$created}', points=points+1 WHERE userid='{$obj->actor}'";
			$db->setQuery( $query );
			$addHit =$db->query();
		}
		
	 }	
		
	}
	
		function checkexist($status,$uid)
		{
			$db	 	= &$this->getDBO();
			$status		= explode('(via',$status);		
			$newstatus	= trim($status[0]);
			$newstatus	= $db->getEscaped("$newstatus");
			$query = "SELECT status FROM #__broadcast_tmp_activities WHERE uid = {$uid} and status LIKE '{$newstatus}'";
			$db->setQuery($query);	
			$result =$db->loadObjectList();
			if($result)			
				return 1;					
			else
				return 0;
		}
		
		function twitdeletetmpactivity()
		{
		 	$curtime=time();
		 	$previousdt1= $curtime-(3600*24*2);		 	
		 	$previousdt=date('Y-m-d',$previousdt1);
			$db	 	= &$this->getDBO();		
			$query = "DELETE FROM #__broadcast_tmp_activities WHERE type LIKE 'twitter' AND created_date<'$previousdt'";
			$db->setQuery($query);	
			$db->query();			
		}
	
}
