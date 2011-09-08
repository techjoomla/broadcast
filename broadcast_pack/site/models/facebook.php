<?php

defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class BroadcastModelfacebook extends JModel
{
	function store($data)
	{
		$db	 	= &$this->getDBO();
		$user = JFactory::getUser();
		$qry = "SELECT `user_id` FROM #__broadcast_users WHERE `user_id` = '{$user->id}'";
		$db->setQuery($qry);
		$exists = $db->loadResult();	
		$row = new stdClass;
		$row->user_id = $user->id;
		
		foreach ($data as $k=>$v)
			$row->$k = $v;
		
		if ($exists)
			 $db->updateObject('#__broadcast_users', $row, 'user_id');
		 else
		 	$db->insertObject('#__broadcast_users', $row);
	 }
	 
	function remove()
	{
		$db	 	= &$this->getDBO();
		$user 	= JFactory::getUser();
		$qry 	= "UPDATE #__broadcast_users SET facbook_uid='',facebook_secret='' WHERE user_id = {$user->id}";
		$db->setQuery($qry);	
		$addHit =$db->query();
	 }
	 
	function fbstore($data,$type)
	{
		jimport('joomla.utilities.date');
		$db	 	= &$this->getDBO();
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

		if($type=='profile')	
		{	
			$i=0;
			///Inserting Profile Data	
			$obj	= new StdClass();
			$content	= '';	
			$content = $data[$i]['status'];
			$content = preg_replace("/<img[^>]+\>/i", "", $content); 
			$checkcontent	= trim($content);	    
			if(empty($checkcontent))return;
			
			if((!$this->checkexist($data[$i]['status'],$data[$i]['actor'],'profile')))
			{

				$obj='';
				$actor="";
				if($config['show_name'])
					$actor='{actor} ';
				else
					$actor='';

				$day			= & JFactory::getDate($data[$i]['created']);	
				$obj->actor 	= $data[$i]['actor'];
				$obj->target 	= $data[$i]['target'];
				$data[$i]['title']	= combroadcastHelper::makelink($content);//$db->getEscaped($data['title']);
				//print_r($data[$i]['title']);die;
				$obj->title		= $actor.$data[$i]['title'];
				$data[$i]['content']	=$db->getEscaped($data[$i]['content']);
				$obj->content	= $data[$i]['content'];
				$obj->app		= 'facebook'; //$data[$i]['app'];
				$obj->cid		= $data[$i]['cid'];
				$obj->params	= $data[$i]['params'];
				$obj->created	= $day->toMySQL();	
				$obj->access	= $data[$i]['access'];
				$obj->points	= $data[$i]['points'];
				$obj->archived	= $data[$i]['archived'];		
				$db->insertObject('#__community_activities', $obj);
				
				$qry	 		= "SELECT title, created FROM #__community_activities WHERE actor = {$obj->actor} ORDER BY created desc ";
				$db->setQuery($qry);		
				$mydata 		= $db->loadObject();		
				
				$currentstatus  = $mydata->title;
				$created 		= $mydata->created;	
				$currentstatus		= str_replace("{actor} ",'',$currentstatus);	
				$currentstatus	= $db->getEscaped($content);//mysql_real_escape_string("$currentstatus");					
				$query	= "UPDATE `#__community_users` SET `status` ='{$currentstatus}', 
							posted_on='{$created}', points=points +1 WHERE userid='{$obj->actor}'";
				$db->setQuery( $query );
				$addHit =$db->query();
				$tmp_obj=null;
				$tmp_obj->uid 			= $data[$i]['cid'];
				$tmpstatus=explode('(via',$data[$i]['title']);
				$tmp_obj->status =$content;
				$tmp_obj->created_date	= date('Y-m-d',$data[$i]['created']);	
				$tmp_obj->type='facebook';	
				$db->insertObject('#__broadcast_tmp_activities', $tmp_obj);
				///
			}
		}
		if($type=='page')	
		{
				$obj	= new StdClass();
				$content	= '';
				$content = $data[$i]['status'];
				$content = preg_replace("/<img[^>]+\>/i", "", $content); 
				$checkcontent	= trim($content);	    
				if(empty($checkcontent))return; 
				
				if((!$this->checkexist($data['status'],$data['actor'],'page')))
				{
					$obj='';	
					$actor="";
					if($config['show_name'])
						$actor='{actor} ';
					else
						$actor='';
					$day					= & JFactory::getDate($data['created']);	
					$obj->actor 		= $data['actor'];
					$obj->target 	= $data['target'];
					$data['title']	= combroadcastHelper::makelink($content);//$db->getEscaped($data['title']);
					
					$obj->title		= $actor.$data['title'];
					$data['content']	=$db->getEscaped($data['content']);
					$obj->content	= $data['content'];
					$obj->app			= 'facebook'; //$data['app'];
					$obj->cid			= $data['cid'];
					$obj->params	= $data['params'];
					$obj->created	= $day->toMySQL();	
					$obj->access	= $data['access'];
					$obj->points		= $data['points'];
					$obj->archived	= $data['archived'];		
					$db->insertObject('#__community_activities', $obj);
					
					$tmp_obj=null;
					$tmp_obj->uid 			= $data['cid'];
					$tmpstatus=explode('(via',$data['title']);
					$tmp_obj->status =$content;
					$tmp_obj->created_date	= date('Y-m-d',$data['created']);	
					$tmp_obj->type='facebook';													
					$db->insertObject('#__broadcast_tmp_activities', $tmp_obj);
					
					$qry= "SELECT title, created FROM #__community_activities WHERE actor = {$obj->actor} ORDER BY created desc ";
					$db->setQuery($qry);		
					$mydata = $db->loadObject();	
						
					$currentstatus= $mydata->title;
					$created= $mydata->created;
					$currentstatus= str_replace("{actor} ",'',$currentstatus);	
					$currentstatus	= $content;//mysql_real_escape_string("$currentstatus");					
					$query	= "UPDATE `#__community_users` SET `status` ='{$currentstatus}', 
								posted_on='{$created}', points=points +1 WHERE userid='{$obj->actor}'";
					$db->setQuery( $query );
					$addHit =$db->query();
				}
		}
		
	}
	
		function checkexist($status,$cid,$type)
		{
			$db	 		= &$this->getDBO();			
			$status1=explode('(via',$status);		
			$newstatus	= trim($status1[0]);
			$newstatus	=$db->getEscaped($newstatus);
			if($type=='profile')
				$query 	= "SELECT status FROM #__broadcast_tmp_activities WHERE status = '{$newstatus}' and uid='$cid'";
			else
		 		$query 	= "SELECT status FROM #__broadcast_tmp_activities WHERE status = '{$newstatus}' ";
			$db->setQuery($query);	
			$result =$db->loadResult();
			if($result)			
			return 1;					
			else
			return 0;
		}
		
		function fbdeletetmpactivity()
		{
		 	$curtime		= time();
		 	$previousdt1	= $curtime-(3600*24*2);		 	
		 	$previousdt	=date('Y-m-d',$previousdt1);
			$db	 			= &$this->getDBO();		
			$query  		= "DELETE FROM #__broadcast_tmp_activities WHERE type LIKE 'facebook' AND created_date<'$previousdt'";
			$db->setQuery($query);	
			$db->query();
		
		}
}
